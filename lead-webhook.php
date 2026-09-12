<?php
/*
 * Google Ads "Potencialis ugyfel urlapja" (lead form) webhook fogado.
 *
 * MIERT KELL: integracio nelkul a lead form eszkozzel szerzett leadeket
 * kezzel kellene letolteni CSV-ben a Google Adsbol, es a Google 30 nap utan
 * TORLI oket. Aki nem nez ra idoben, elveszti a leadet. Ez a vegpont a
 * beerkezes pillanataban emailt kuld, es egy naploba is beirja.
 *
 * A Google ide POST-ol JSON-t, amint valaki bekuldi az urlapot a talalati
 * oldalon. A hitelesites egy elore megosztott kulcs ("google_key"), amit a
 * Google minden keresben visszakuld — ezt a gitignore-olt config.php-ban
 * taroljuk, ugyanott, ahol a Resend API kulcsot.
 *
 * Beallitas a Google Adsben:
 *   Eszkozok > Eszkozok > a lead form eszkoz > "Potencialis ugyfelek
 *   exportalasa" > Webhook
 *   Webhook URL:  https://soulsilver.hu/lead-webhook.php
 *   Kulcs:        (a config.php-ban levo 'google_lead_key' ertek)
 *   majd "Adatok tesztelese" — ennek 200-at kell kapnia.
 */

// --- Konfiguracio ---
// A kozos ss_config() az elso HASZNALHATO config.php-t veszi, nem az elso
// letezot. Ez itt nem kozmetika: 2026-09-12-en egy ottfelejtett, ures
// ../config.php neman elnyomta a jo fajlt. Ha ez a vegpont a regi mintat
// hasznalna, kulcs nelkul maradna, es MINDEN beerkezo leadet 401-gyel
// dobna el — vagyis pont azt veszitenenk el, amiert a webhook keszult.
require_once __DIR__ . '/stripe-lib.php';

$RESEND_API_KEY = ss_cfg('resend_api_key');
$FROM = ss_cfg('from') ?: 'SOULSILVER weboldal <noreply@soulsilver.hu>';
$TO   = ss_cfg('to')   ?: 'info@soulsilvermarketing.com';
$KEY  = ss_cfg('google_lead_key');

/**
 * Minden beerkezo leadet naplozunk, meg azt is, amit visszautasitunk.
 * Ez a halo a halo alatt: ha az email kuldes barmiert elhasal, a lead
 * akkor is megvan a szerveren. A fajl gitignore-olt (*.log).
 */
function wh_log($ok, $reszlet = '')
{
    $sor = sprintf("%s\t%s\t%s\n", date('c'), $ok, $reszlet);
    // A public_html FOLE irunk elsokent. Korabban ide, a gyokerbe ment, es a
    // webszerver simán kiszolgalta: a /lead-webhook.log 200-zal valaszolt, es
    // lead-neveket, email-cimeket, telefonszamokat adott ki barkinek.
    // A helyi tartalek csak vegszukseg (ott a .htaccess zarja el).
    foreach ([__DIR__ . '/../lead-webhook.log', __DIR__ . '/lead-webhook.log'] as $f) {
        if (@file_put_contents($f, $sor, FILE_APPEND | LOCK_EX) !== false) {
            @chmod($f, 0600);
            return;
        }
    }
    error_log('SOULSILVER lead-webhook: ' . $sor);
}

function wh_valasz($kod, $uzenet)
{
    http_response_code($kod);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $uzenet], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    wh_valasz(405, 'csak POST');
}

$nyers = file_get_contents('php://input');
$adat  = json_decode($nyers, true);

if (!is_array($adat)) {
    wh_log('ervenytelen-json', substr($nyers, 0, 200));
    wh_valasz(400, 'ervenytelen json');
}

// --- Hitelesites ---
// hash_equals: idozites-alapu tamadas ellen. Ha nincs kulcs beallitva a
// szerveren, NEM engedunk at semmit — kulonben barki kuldhetne ide leadet.
$kapott = (string) ($adat['google_key'] ?? '');
if ($KEY === '' || !hash_equals($KEY, $kapott)) {
    wh_log('rossz-kulcs', 'form=' . (string) ($adat['form_id'] ?? '-'));
    wh_valasz(401, 'ervenytelen kulcs');
}

// --- Mezok kibontasa ---
// A Google column_id-kkal kuldi a mezoket; az egyeni kerdesek sajat
// azonositot kapnak. Amit nem ismerunk fel, azt a nyers nevevel irjuk ki,
// hogy egy uj kerdes soha ne tunjon el csendben.
$cimkek = [
    'FULL_NAME'     => 'Nev',
    'FIRST_NAME'    => 'Keresztnev',
    'LAST_NAME'     => 'Vezeteknev',
    'EMAIL'         => 'Email',
    'PHONE_NUMBER'  => 'Telefon',
    'POSTAL_CODE'   => 'Iranyitoszam',
    'CITY'          => 'Varos',
    'REGION'        => 'Megye',
    'COUNTRY'       => 'Orszag',
    'COMPANY_NAME'  => 'Cegnev',
    'JOB_TITLE'     => 'Beosztas',
    'WORK_EMAIL'    => 'Ceges email',
    'WORK_PHONE'    => 'Ceges telefon',
];

$mezok = [];
$email = '';
$nev   = '';
foreach (($adat['user_column_data'] ?? []) as $oszlop) {
    if (!is_array($oszlop)) {
        continue;
    }
    $id  = (string) ($oszlop['column_id'] ?? '');
    $ert = (string) ($oszlop['string_value'] ?? '');
    if ($ert === '') {
        continue;
    }
    $cimke = $cimkek[$id] ?? (string) ($oszlop['column_name'] ?? $id);
    $mezok[] = $cimke . ': ' . $ert;
    if ($id === 'EMAIL' || $id === 'WORK_EMAIL') {
        $email = $ert;
    }
    if ($id === 'FULL_NAME') {
        $nev = $ert;
    }
}

$teszt = !empty($adat['is_test']);

$subject = 'UJ LEAD a Google hirdetesbol'
         . ($nev !== '' ? ' - ' . preg_replace('/[^\p{L}\p{N} .-]/u', '', $nev) : '')
         . ($teszt ? ' [TESZT]' : '');

$text = "Egy erdeklodo kitoltotte az urlapot KOZVETLENUL a Google talalati oldalan.\n"
      . "Hivd vissza minel hamarabb — ez a lead meg nem jart a weboldalon.\n\n"
      . implode("\n", $mezok) . "\n\n"
      . "---\n"
      . 'Lead ID: ' . (string) ($adat['lead_id'] ?? '-') . "\n"
      . 'Kampany: ' . (string) ($adat['campaign_id'] ?? '-') . "\n"
      . 'Urlap:   ' . (string) ($adat['form_id'] ?? '-') . "\n"
      . 'GCLID:   ' . (string) ($adat['gcl_id'] ?? '-') . "\n"
      . ($teszt ? "\nFIGYELEM: ez a Google TESZT-leadje, nem valodi erdeklodo.\n" : '');

// A naplo a biztos pont: ezt meg az email kuldes ELOTT irjuk ki.
wh_log($teszt ? 'teszt-lead' : 'lead', str_replace(["\r", "\n", "\t"], ' | ', implode('; ', $mezok)));

// --- Email: Resend, majd tartalek natív mail() ---
$elkuldve = false;
if ($RESEND_API_KEY !== '' && function_exists('curl_init')) {
    $payload = [
        'from'    => $FROM,
        'to'      => [$TO],
        'subject' => $subject,
        'text'    => $text,
    ];
    if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $payload['reply_to'] = $email;
    }
    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $RESEND_API_KEY,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT    => 15,
    ]);
    curl_exec($ch);
    $kod = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $elkuldve = ($kod >= 200 && $kod < 300);
}

if (!$elkuldve) {
    $headers = "From: $FROM\r\n"
             . 'Reply-To: ' . ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : $TO) . "\r\n"
             . "Content-Type: text/plain; charset=UTF-8\r\n";
    $elkuldve = @mail($TO, $subject, $text, $headers);
}

wh_log('email', $elkuldve ? 'ok' : 'HIBA');

// A Google-nek 200 kell, kulonben hibasnak jeloli a webhookot es leallitja.
// A lead ilyenkor is megvan a naploban, ezert itt akkor is 200-at adunk,
// ha az email kuldes elhasalt — a naplo a biztositek.
wh_valasz(200, 'ok');
