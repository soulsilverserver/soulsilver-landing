<?php
/*
 * SOULSILVER — Stripe Checkout session indítása.
 *
 * A böngésző CSAK a csomag azonosítóját küldi; az árat mindig a szerveren,
 * a stripe-csomagok.php-ból olvassuk (árhamisítás ellen). Ismeretlen
 * azonosítót nem szolgálunk ki.
 *
 * FONTOS: a Stripe fiók közös a Protein Bázis webshoppal, ezért minden
 * fizetésre rákerül a `webhely = soulsilver.hu` metaadat, és a webhook
 * mindent eldob, amin ez nincs rajta. Ezt a szűrőt ne vedd ki.
 */
require __DIR__ . '/stripe-lib.php';

$vissza = ss_site_url() . '/fizetes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $vissza, true, 303);
    exit;
}

$id      = (string) ($_POST['csomag'] ?? '');
$csomag  = ss_csomag($id);

if ($csomag === null) {
    ss_log('checkout: ismeretlen csomag azonosito: ' . substr($id, 0, 60));
    header('Location: ' . $vissza . '?hiba=ismeretlen-csomag', true, 303);
    exit;
}
if (ss_stripe_secret() === '') {
    ss_log('checkout: nincs stripe_secret_key a config.php-ban');
    header('Location: ' . $vissza . '?hiba=nincs-kulcs', true, 303);
    exit;
}

$netto  = (int) $csomag['netto'];
$brutto = ss_brutto($netto);
$havi   = ($csomag['tipus'] ?? 'egyszeri') === 'havi';
$afa_sz = (int) round(ss_afa_kulcs() * 100);

$params = [
    'mode'       => $havi ? 'subscription' : 'payment',
    'locale'     => 'hu',
    'success_url' => ss_site_url() . '/fizetes-siker.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url'  => $vissza . '?megszakitva=1',
    // A számlázáshoz kell: név/cégnév, cím, adószám, telefon. A számlát mi
    // állítjuk ki kézzel, ezért minden adatot itt gyűjtünk be, hogy a
    // visszaigazoló emailben már minden együtt legyen.
    'billing_address_collection' => 'required',
    'phone_number_collection'    => ['enabled' => 'true'],
    'tax_id_collection'          => ['enabled' => 'true'],   // adószám: opcionális
    'name_collection'            => [
        'business'   => ['enabled' => 'true', 'optional' => 'true'],
        'individual' => ['enabled' => 'true', 'optional' => 'false'],
    ],
    'line_items' => [[
        'quantity'   => 1,
        'price_data' => [
            'currency'     => 'huf',
            'unit_amount'  => ss_stripe_amount($brutto),
            'product_data' => [
                'name'        => $csomag['nev'],
                'description' => $csomag['mit'] . ' — ' . ($afa_sz > 0
                    ? ss_ft($netto, false) . ' + ' . $afa_sz . '% ÁFA'
                    : ss_ft($netto, false) . ' (alanyi adómentes)'),
            ],
        ],
    ]],
    // Ez a metaadat különíti el a soulsilver.hu fizetéseit a közös Stripe
    // fiókban futó Protein Bázis rendelésektől.
    'metadata' => [
        'webhely'   => 'soulsilver.hu',
        'csomag'    => $csomag['id'],
        'netto_ft'  => (string) $netto,
        'brutto_ft' => (string) $brutto,
        'afa_szaz'  => (string) $afa_sz,
        'tipus'     => $havi ? 'havi' : 'egyszeri',
    ],
];

if ($havi) {
    // Előfizetés: havonta automatikusan terhel, amíg fel nem mondják.
    $params['line_items'][0]['price_data']['recurring'] = ['interval' => 'month'];
    $params['subscription_data']['metadata'] = $params['metadata'];
} else {
    $params['customer_creation'] = 'always';
    // A PaymentIntent is kapja meg a jelölőt, hogy a webhook a
    // payment_intent.* eseményeknél is tudja szűrni.
    $params['payment_intent_data']['metadata'] = $params['metadata'];
    $params['payment_intent_data']['description'] = 'soulsilver.hu — ' . $csomag['nev'];
}

/*
 * Az adatgyűjtő paraméterek közül a name_collection újabb keletű, mint egyes
 * rögzített API-verziók. Ha a Stripe nem ismeri valamelyiket, ELDOBJUK és
 * újrapróbáljuk — egy ilyen apróság soha ne akadályozzon meg egy fizetést.
 * A kötelező paramétereket (mode, line_items, ...) sosem dobjuk el.
 */
$elhagyhato = ['name_collection', 'tax_id_collection', 'phone_number_collection'];
$res = ss_stripe_api('POST', '/v1/checkout/sessions', $params);
for ($i = 0; $i < count($elhagyhato) && !$res['ok'] && $res['code'] === 400; $i++) {
    $hibas = (string) ($res['body']['error']['param'] ?? '');
    $kulcs = $hibas === '' ? '' : explode('[', $hibas)[0];
    if ($kulcs === '' || !in_array($kulcs, $elhagyhato, true) || !isset($params[$kulcs])) {
        break;
    }
    unset($params[$kulcs]);
    ss_log('checkout: a Stripe nem ismerte a(z) "' . $kulcs . '" paramétert, újrapróbálom nélküle');
    $res = ss_stripe_api('POST', '/v1/checkout/sessions', $params);
}

if (!$res['ok'] || empty($res['body']['url'])) {
    ss_log('checkout HIBA (' . $csomag['id'] . '): ' . $res['error']);
    header('Location: ' . $vissza . '?hiba=stripe', true, 303);
    exit;
}

ss_log('checkout session letrehozva: ' . ($res['body']['id'] ?? '?') . ' csomag=' . $csomag['id'] . ' brutto=' . $brutto . 'Ft' . (ss_eles_mod() ? ' [ELES]' : ' [teszt]'));

header('Location: ' . $res['body']['url'], true, 303);
exit;
