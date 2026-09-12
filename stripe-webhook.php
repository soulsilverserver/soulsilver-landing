<?php
/*
 * SOULSILVER — Stripe webhook.
 *
 * A Stripe ide POST-ol minden eseményt. A fiók KÖZÖS a Protein Bázis
 * webshoppal, ezért itt landolnak annak a rendelései is: amin nincs
 * `webhely = soulsilver.hu` metaadat, azt csendben eldobjuk (200 OK).
 *
 * Az aláírást kézzel ellenőrizzük (nincs SDK): Stripe-Signature fejléc
 * `t=<idő>,v1=<hmac>` — a hmac a "<t>.<nyers törzs>" HMAC-SHA256-ja a
 * webhook signing secrettel.
 *
 * Beállítás: Stripe Dashboard → Developers → Webhooks → Add endpoint
 *   URL:       https://soulsilver.hu/stripe-webhook.php
 *   Események: checkout.session.completed, invoice.paid
 */
require __DIR__ . '/stripe-lib.php';

const SS_WEBHOOK_TOLERANCIA = 300;   // másodperc

$nyers = file_get_contents('php://input');
$sig   = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$whsec = ss_stripe_whsec();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('POST only');
}
if ($whsec === '') {
    // Ellenőrizhetetlen kérést nem dolgozunk fel — különben bárki
    // hamisíthatna „sikeres fizetés" értesítést.
    ss_log('webhook: nincs stripe_webhook_secret a config.php-ban, kérés eldobva');
    http_response_code(400);
    exit('no signing secret configured');
}

// --- aláírás ellenőrzése ---
$t = null; $v1 = [];
foreach (explode(',', $sig) as $resz) {
    $p = explode('=', trim($resz), 2);
    if (count($p) !== 2) continue;
    if ($p[0] === 't')  $t = $p[1];
    if ($p[0] === 'v1') $v1[] = $p[1];
}
if ($t === null || !ctype_digit((string) $t) || empty($v1)) {
    ss_log('webhook: hibás Stripe-Signature fejléc');
    http_response_code(400);
    exit('bad signature header');
}
if (abs(time() - (int) $t) > SS_WEBHOOK_TOLERANCIA) {
    ss_log('webhook: elavult időbélyeg (replay védelem)');
    http_response_code(400);
    exit('timestamp outside tolerance');
}
$vart = hash_hmac('sha256', $t . '.' . $nyers, $whsec);
$jo = false;
foreach ($v1 as $adott) {
    if (hash_equals($vart, $adott)) { $jo = true; break; }
}
if (!$jo) {
    ss_log('webhook: érvénytelen aláírás');
    http_response_code(400);
    exit('signature mismatch');
}

// --- innentől megbízható a törzs ---
$event = json_decode($nyers, true);
if (!is_array($event)) {
    http_response_code(400);
    exit('bad json');
}
$tipus     = (string) ($event['type'] ?? '');
$esemenyId = (string) ($event['id'] ?? '');
$obj       = is_array($event['data']['object'] ?? null) ? $event['data']['object'] : [];

/** Az első nem üres érték a megadott útvonalakról ("a.b.c"). */
function ss_elso(array $forras, array $utvonalak): string
{
    foreach ($utvonalak as $ut) {
        $v = $forras;
        foreach (explode('.', $ut) as $kulcs) {
            if (!is_array($v) || !isset($v[$kulcs])) { $v = null; break; }
            $v = $v[$kulcs];
        }
        if (is_string($v) && trim($v) !== '') return trim($v);
        if (is_int($v) || is_float($v)) return (string) $v;
    }
    return '';
}

/** A `webhely` jelölő több helyen lehet, esemény típusától függően. */
function ss_webhely_jelolo(array $obj): string
{
    return ss_elso($obj, [
        'metadata.webhely',
        'subscription_details.metadata.webhely',
        'lines.data.0.metadata.webhely',
        'parent.subscription_details.metadata.webhely',
    ]);
}

/** Adószám az első talált tax id bejegyzésből (checkout session vagy számla). */
function ss_adoszam(array $obj): string
{
    foreach ([$obj['customer_details']['tax_ids'] ?? null, $obj['customer_tax_ids'] ?? null] as $lista) {
        if (!is_array($lista)) continue;
        foreach ($lista as $t) {
            $v = is_array($t) ? trim((string) ($t['value'] ?? '')) : '';
            if ($v !== '') return $v;
        }
    }
    return '';
}

$erdekel = ['checkout.session.completed', 'invoice.paid'];
if (!in_array($tipus, $erdekel, true)) {
    http_response_code(200);
    exit('ignored (event type)');
}
if (ss_webhely_jelolo($obj) !== 'soulsilver.hu') {
    // Nagy valószínűséggel Protein Bázis rendelés a közös fiókból.
    http_response_code(200);
    exit('ignored (other site)');
}
if ($tipus === 'invoice.paid' && ss_elso($obj, ['billing_reason', 'parent.subscription_details.billing_reason']) === 'subscription_create') {
    // Az előfizetés első számláját a checkout.session.completed már jelentette.
    http_response_code(200);
    exit('ok (first invoice, already reported)');
}
// A Stripe legalább egyszer kézbesít: ugyanaz az esemény kétszer is jöhet.
// Számlázási értesítőnél ez két számlát jelentene, ezért kihagyjuk.
if (ss_esemeny_feldolgozva($esemenyId)) {
    ss_log('webhook: mar feldolgozott esemeny, kihagyva: ' . $esemenyId);
    http_response_code(200);
    exit('ok (duplicate)');
}

// ── a rendelés adatai, normalizálva a kétféle eseményből ──
$md       = is_array($obj['metadata'] ?? null) ? $obj['metadata'] : [];
if (!$md) {
    $md = $obj['subscription_details']['metadata'] ?? ($obj['parent']['subscription_details']['metadata'] ?? []);
    if (!is_array($md)) $md = [];
}
$csomagId = (string) ($md['csomag'] ?? '');
$csomag   = ss_csomag($csomagId);

$brutto_fill = (int) ($obj['amount_total'] ?? $obj['amount_paid'] ?? 0);
$brutto      = (int) round($brutto_fill / 100);
$afa_sz      = (int) ($md['afa_szaz'] ?? round(ss_afa_kulcs() * 100));
$netto       = isset($md['netto_ft']) ? (int) $md['netto_ft']
             : ($csomag['netto'] ?? (int) round($brutto / (1 + $afa_sz / 100)));

$cim = $obj['customer_details']['address'] ?? ($obj['customer_address'] ?? []);
if (!is_array($cim)) $cim = [];

$pi     = ss_elso($obj, ['payment_intent']);
$szamla = ss_elso($obj, ['hosted_invoice_url']);
$eles   = !empty($event['livemode']) || ss_eles_mod();
$dbUrl  = 'https://dashboard.stripe.com/' . ($eles ? '' : 'test/');
$link   = $pi !== '' ? $dbUrl . 'payments/' . $pi
        : ($szamla !== '' ? $szamla : $dbUrl . 'payments');

$rendeles = [
    'esemeny'     => $tipus,
    'azonosito'   => (string) ($obj['id'] ?? ''),
    'csomag_id'   => $csomagId,
    'csomag_nev'  => $csomag['nev'] ?? ($csomagId !== '' ? $csomagId : 'ismeretlen csomag'),
    'tipus'       => (string) ($md['tipus'] ?? ($csomag['tipus'] ?? 'egyszeri')),
    'netto'       => $netto,
    'afa_szaz'    => $afa_sz,
    'brutto'      => $brutto,
    'penznem'     => strtoupper(ss_elso($obj, ['currency']) ?: 'huf'),
    'fizetve'     => (int) ($obj['created'] ?? ($event['created'] ?? time())),
    'vevo_nev'    => ss_elso($obj, ['customer_details.name', 'customer_name']),
    'cegnev'      => ss_elso($obj, [
                        'customer_details.business_name',
                        'customer_details.name_collection.business',
                        'collected_information.business_name',
                     ]),
    'adoszam'     => ss_adoszam($obj),
    'email'       => ss_elso($obj, ['customer_details.email', 'customer_email']),
    'telefon'     => ss_elso($obj, ['customer_details.phone', 'customer_phone']),
    'cim'         => $cim,
    'eles'        => $eles,
    'stripe_link' => $link,
];

$level = ss_rendeles_email($rendeles);
$kuldve = ss_email($level['targy'], $level['text'], $level['html']);

if (!$kuldve) {
    // NEM rögzítjük az eseményt: 500-ra a Stripe újrapróbálja, és így az
    // értesítő később mégis megérkezik. Enélkül némán elveszne egy rendelés.
    ss_log('webhook: az ERTESITO EMAIL NEM MENT EL! esemeny=' . $esemenyId . ' ' . $rendeles['azonosito']
         . ' csomag=' . $csomagId . ' brutto=' . $brutto . ' Ft — a Stripe ujra fogja probalni');
    http_response_code(500);
    exit('email send failed, please retry');
}

ss_esemeny_rogzit($esemenyId);
ss_log('webhook OK: ' . $tipus . ' ' . $rendeles['azonosito'] . ' csomag=' . $csomagId
     . ' ' . $brutto . ' ' . $rendeles['penznem'] . ' — ertesito elkuldve');

http_response_code(200);
echo 'ok';
