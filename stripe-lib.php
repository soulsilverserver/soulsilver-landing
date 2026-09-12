<?php
/*
 * SOULSILVER — Stripe segédfüggvények.
 *
 * Szándékosan NINCS composer/SDK: a Hostinger-en nincs build lépés, ezért a
 * Stripe REST API-t közvetlenül hívjuk cURL-lel. A titkos kulcs a
 * gitignore-olt config.php-ban van (lásd config.example.php).
 */

/** config.php betöltése — előbb a public_html FÖLÖTTI mappából (biztonságosabb). */
function ss_config(): array
{
    static $cfg = null;
    if ($cfg !== null) return $cfg;
    $cfg = [];
    if (is_file(__DIR__ . '/../config.php')) {
        $cfg = require __DIR__ . '/../config.php';
    } elseif (is_file(__DIR__ . '/config.php')) {
        $cfg = require __DIR__ . '/config.php';
    }
    if (!is_array($cfg)) $cfg = [];
    return $cfg;
}

function ss_cfg(string $kulcs, string $default = ''): string
{
    $c = ss_config();
    $v = getenv(strtoupper($kulcs)) ?: ($c[$kulcs] ?? '');
    return is_string($v) ? trim($v) : $default;
}

function ss_stripe_secret(): string      { return ss_cfg('stripe_secret_key'); }
function ss_stripe_publishable(): string { return ss_cfg('stripe_publishable_key'); }
function ss_stripe_whsec(): string       { return ss_cfg('stripe_webhook_secret'); }

/** A saját domain — NEM a Host fejlécből, mert azt a látogató hamisíthatja. */
function ss_site_url(): string
{
    $u = ss_cfg('site_url', '');
    return rtrim($u !== '' ? $u : 'https://soulsilver.hu', '/');
}

/** ÁFA-kulcs. 0.27 = 27%. Nettó árakat tartunk nyilván, bruttót terhelünk. */
function ss_afa_kulcs(): float
{
    $c = ss_config();
    return isset($c['afa_kulcs']) ? (float) $c['afa_kulcs'] : 0.27;
}

/** Élesben futunk-e (sk_live_…), vagy teszt módban. */
function ss_eles_mod(): bool
{
    return strpos(ss_stripe_secret(), '_live_') !== false;
}

function ss_csomagok(): array
{
    static $cs = null;
    if ($cs === null) {
        $cs = require __DIR__ . '/stripe-csomagok.php';
        if (!is_array($cs)) $cs = [];
    }
    return $cs;
}

function ss_csomag(string $id): ?array
{
    $cs = ss_csomagok();
    return isset($cs[$id]) ? ($cs[$id] + ['id' => $id]) : null;   // fehérlista
}

/** Bruttó forint egész forintra kerekítve. */
function ss_brutto(int $netto): int
{
    return (int) round($netto * (1 + ss_afa_kulcs()));
}

/**
 * Stripe összeg. A HUF a Stripe-nál KÉTdecimális töltésnél, ezért fillérben
 * kell megadni: Ft × 100. (A 100-cal osztható szabály csak a manuális
 * payoutra igaz, a terhelésre nem.) Minimum töltés: 175 Ft.
 */
function ss_stripe_amount(int $brutto_ft): int
{
    return $brutto_ft * 100;
}

/** „152 400 Ft" — nem törő szóközzel, ahogy az arak.html. */
function ss_ft(int $ft, bool $nbsp = true): string
{
    $s = number_format($ft, 0, ',', $nbsp ? "\xC2\xA0" : ' ');
    return $s . ($nbsp ? "\xC2\xA0" : ' ') . 'Ft';
}

/**
 * Stripe API hívás.
 * @return array{ok:bool,code:int,body:array,error:string}
 */
function ss_stripe_api(string $method, string $path, array $params = []): array
{
    $secret = ss_stripe_secret();
    if ($secret === '') {
        return ['ok' => false, 'code' => 0, 'body' => [], 'error' => 'Nincs beállítva stripe_secret_key a config.php-ban.'];
    }
    if (!function_exists('curl_init')) {
        return ['ok' => false, 'code' => 0, 'body' => [], 'error' => 'A PHP cURL kiterjesztés nem elérhető.'];
    }

    // Alapból az igazi Stripe. A config-beli 'stripe_api_base' CSAK tesztelésre
    // való (helyi hamis API), élesben soha ne legyen beállítva.
    $bazis = ss_cfg('stripe_api_base') ?: 'https://api.stripe.com';
    $url = rtrim($bazis, '/') . $path;
    $ch  = curl_init();
    $opt = [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 20,
        // Szandekosan NINCS Stripe-Version fejlec: a fiok sajat (rogzitett)
        // API verziojat hasznaljuk. Igy a valasz mezoi ugyanazok, mint amit a
        // Dashboard mutat, es nem kell egy tippelt verzio-stringet karbantartani.
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $secret,
            'Content-Type: application/x-www-form-urlencoded',
        ],
    ];
    if (strtoupper($method) === 'POST') {
        $opt[CURLOPT_POST] = true;
        // A Stripe a beágyazott tömböket a[b][c] formában várja — ezt a
        // http_build_query pontosan így állítja elő.
        $opt[CURLOPT_POSTFIELDS] = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    } elseif (!empty($params)) {
        $opt[CURLOPT_URL] = $url . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }
    curl_setopt_array($ch, $opt);

    $resp = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $cerr = curl_error($ch);

    if ($resp === false) {
        return ['ok' => false, 'code' => $code, 'body' => [], 'error' => 'cURL: ' . $cerr];
    }
    $body = json_decode($resp, true);
    if (!is_array($body)) $body = [];
    $ok = ($code >= 200 && $code < 300);
    return [
        'ok'    => $ok,
        'code'  => $code,
        'body'  => $body,
        'error' => $ok ? '' : ($body['error']['message'] ?? ('HTTP ' . $code)),
    ];
}

/**
 * Naplózás. Előbb a public_html FÖLÖTTI mappába próbál írni, hogy a napló
 * ne legyen böngészőből letölthető. A *.log gitignore-olt.
 */
function ss_log(string $uzenet): void
{
    $sor = '[' . date('Y-m-d H:i:s') . '] ' . str_replace(["\r", "\n"], ' ', $uzenet) . "\n";
    foreach ([__DIR__ . '/../soulsilver-stripe.log', __DIR__ . '/stripe.log'] as $f) {
        if (@file_put_contents($f, $sor, FILE_APPEND | LOCK_EX) !== false) {
            @chmod($f, 0600);
            return;
        }
    }
    error_log('SOULSILVER stripe: ' . $uzenet);
}

/** Email értesítés Resenddel (ugyanaz a minta, mint a contact.php-ban). */
function ss_email(string $subject, string $text, string $html = ''): bool
{
    $c    = ss_config();
    $key  = ss_cfg('resend_api_key');
    $from = $c['from'] ?? 'SOULSILVER weboldal <noreply@soulsilver.hu>';
    $to   = $c['to']   ?? 'info@soulsilvermarketing.com';
    $sent = false;

    if ($key !== '' && function_exists('curl_init')) {
        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $key, 'Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode(array_filter([
                'from' => $from, 'to' => [$to], 'subject' => $subject,
                'text' => $text, 'html' => $html,
            ])),
            CURLOPT_TIMEOUT        => 15,
        ]);
        curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $sent = ($code >= 200 && $code < 300);
    }
    if (!$sent) {
        $sent = @mail($to, $subject, $text, "From: $from\r\nContent-Type: text/plain; charset=UTF-8\r\n");
    }
    return $sent;
}

function ss_h(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/* ───────────────────── Ismétlődés-védelem ─────────────────────
 * A Stripe legalább egyszer kézbesít: ha a 200-as válaszunk nem ér vissza
 * hozzá, újraküldi ugyanazt az eseményt. Számlázási értesítőnél ez két
 * levelet — és könnyen két számlát — jelentene, ezért az eseményazonosítót
 * a sikeres küldés UTÁN rögzítjük, és a már látottakat kihagyjuk.
 */

function ss_esemeny_fajl(): string
{
    foreach ([__DIR__ . '/../soulsilver-stripe-esemenyek.txt', __DIR__ . '/stripe-esemenyek.txt'] as $f) {
        if (is_file($f) || @touch($f)) { @chmod($f, 0600); return $f; }
    }
    return '';
}

function ss_esemeny_feldolgozva(string $id): bool
{
    if ($id === '') return false;
    $f = ss_esemeny_fajl();
    if ($f === '' || !is_file($f)) return false;
    $sorok = @file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return is_array($sorok) && in_array($id, $sorok, true);
}

function ss_esemeny_rogzit(string $id): void
{
    if ($id === '') return;
    $f = ss_esemeny_fajl();
    if ($f === '') return;
    @file_put_contents($f, $id . "\n", FILE_APPEND | LOCK_EX);
    // Ne nőjön a végtelenségig: az utolsó 500 azonosítót tartjuk meg.
    $sorok = @file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (is_array($sorok) && count($sorok) > 500) {
        @file_put_contents($f, implode("\n", array_slice($sorok, -500)) . "\n", LOCK_EX);
    }
}

/* ───────────────────── Számlázási értesítő ───────────────────── */

/** Egysoros címke: „Budapest, 1051 Fő utca 1." — üres részek nélkül. */
function ss_cim_egy_sorban(array $cim): string
{
    $orszag = trim((string) ($cim['country'] ?? ''));
    $reszek = array_filter([
        trim((string) ($cim['postal_code'] ?? '')) . ' ' . trim((string) ($cim['city'] ?? '')),
        trim((string) ($cim['line1'] ?? '')),
        trim((string) ($cim['line2'] ?? '')),
        trim((string) ($cim['state'] ?? '')),
        $orszag !== '' && $orszag !== 'HU' ? $orszag : '',
    ], function ($x) { return trim((string) $x) !== ''; });
    return implode(', ', array_map('trim', $reszek));
}

/**
 * A vásárlásról szóló, SZÁMLÁZÁSRA KÉSZ értesítő. Mindent tartalmaz, ami a
 * számla kiállításához kell, hogy ne kelljen a Stripe Dashboardot nyitogatni.
 *
 * @return array{targy:string,text:string,html:string}
 */
function ss_rendeles_email(array $r): array
{
    $eles    = !empty($r['eles']);
    $havi    = ($r['tipus'] ?? '') === 'havi';
    $brutto  = (int) ($r['brutto'] ?? 0);
    $netto   = (int) ($r['netto'] ?? 0);
    $afa_sz  = (int) ($r['afa_szaz'] ?? 27);
    $afa     = $brutto - $netto;
    $penznem = (string) ($r['penznem'] ?? 'HUF');
    $nev     = (string) ($r['csomag_nev'] ?? '');
    $datum   = date('Y-m-d H:i', (int) ($r['fizetve'] ?? time()));

    $vevo    = trim((string) ($r['vevo_nev'] ?? ''));
    $cegnev  = trim((string) ($r['cegnev'] ?? ''));
    $adoszam = trim((string) ($r['adoszam'] ?? ''));
    $email   = trim((string) ($r['email'] ?? ''));
    $telefon = trim((string) ($r['telefon'] ?? ''));
    $cim     = ss_cim_egy_sorban((array) ($r['cim'] ?? []));
    $szamlazando = $cegnev !== '' ? $cegnev : $vevo;

    $targy = ($eles ? '' : '[TESZT] ')
           . 'Fizetés: ' . $nev . ' — ' . ss_ft($brutto, false)
           . ($havi ? '/hó' : '') . ' — SZÁMLÁZANDÓ';

    // A pénznemet csak akkor írjuk ki külön, ha nem forint — különben
    // „444 500 Ft HUF" lenne belőle.
    $pj = ($penznem === 'HUF') ? '' : ' ' . $penznem;

    // Igazított „címke: érték" sor. Az mb_strlen kell, mert az ékezetes
    // címkék bájtban hosszabbak, és a str_pad elcsúsztatná őket.
    $kv = function (string $cimke, string $ertek): string {
        $hossz = 15;
        $par   = max(1, $hossz - mb_strlen($cimke, 'UTF-8'));
        return $cimke . str_repeat(' ', $par) . $ertek;
    };

    // ── sima szöveg (ez a hordozható változat) ──
    $sorok = [];
    $sorok[] = $eles ? 'Beérkezett egy kártyás fizetés a soulsilver.hu-n.'
                     : 'TESZT fizetés a soulsilver.hu-n (nem valódi pénz).';
    $sorok[] = '';
    $sorok[] = 'A SZÁMLÁT NEKED KELL KIÁLLÍTANOD — a Stripe visszaigazolása nem számla.';
    $sorok[] = '';
    $sorok[] = '── SZÁMLÁZÁSI ADATOK ──────────────────────────────';
    $sorok[] = $kv('Vevő neve:', $szamlazando !== '' ? $szamlazando : '(nem adta meg)');
    if ($cegnev !== '' && $vevo !== '' && $cegnev !== $vevo) {
        $sorok[] = $kv('Kapcsolattartó:', $vevo);
    }
    $sorok[] = $kv('Adószám:', $adoszam !== '' ? $adoszam : '(nincs — magánszemélyként vásárolt)');
    $sorok[] = $kv('Cím:', $cim !== '' ? $cim : '(nem adta meg)');
    $sorok[] = $kv('Email:', $email !== '' ? $email : '(nincs)');
    $sorok[] = $kv('Telefon:', $telefon !== '' ? $telefon : '(nincs)');
    $sorok[] = '';
    $sorok[] = '── A SZÁMLA TÉTELE ────────────────────────────────';
    $sorok[] = $kv('Megnevezés:', $nev . ($havi ? ' (havidíj)' : ''));
    $sorok[] = $kv('Nettó:', ss_ft($netto, false));
    $sorok[] = $afa_sz > 0
        ? $kv('ÁFA (' . $afa_sz . '%):', ss_ft($afa, false))
        : $kv('ÁFA:', 'alanyi adómentes — nincs felszámítva');
    $sorok[] = $kv('Bruttó:', ss_ft($brutto, false) . $pj . ($havi ? ' / hó' : ''));
    $sorok[] = $kv('Fizetve:', $datum . ' — bankkártyával (Stripe)');
    $sorok[] = $kv('Teljesítés:', date('Y-m-d', (int) ($r['fizetve'] ?? time())));
    $sorok[] = '';
    $sorok[] = '── AZONOSÍTÓK ─────────────────────────────────────';
    $sorok[] = $kv('Csomag:', (string) ($r['csomag_id'] ?? ''));
    $sorok[] = $kv('Stripe:', (string) ($r['azonosito'] ?? ''));
    if (!empty($r['stripe_link'])) {
        $sorok[] = $kv('Megnyitás:', (string) $r['stripe_link']);
    }
    if ($havi) {
        $sorok[] = '';
        $sorok[] = 'FIGYELEM: ez havidíjas előfizetés — a kártyát havonta újra terheljük,';
        $sorok[] = 'és minden hónapban kapsz egy ilyen levelet, tehát havonta kell számlázni.';
    }
    $text = implode("\n", $sorok) . "\n";

    // ── HTML (olvashatóbb, de a sima szöveg mindent tartalmaz) ──
    $h = function ($v) { return ss_h((string) $v); };
    $sor = function ($cimke, $ertek, $kiemelt = false) use ($h) {
        $st = $kiemelt ? 'font-weight:700;' : '';
        return '<tr><td style="padding:6px 14px 6px 0;color:#565F6B;white-space:nowrap;vertical-align:top;">'
             . $h($cimke) . '</td><td style="padding:6px 0;color:#0D1013;' . $st . '">'
             . $h($ertek) . '</td></tr>';
    };
    $html = '<div style="font-family:-apple-system,Segoe UI,Helvetica,Arial,sans-serif;font-size:15px;line-height:1.5;color:#0D1013;max-width:620px;">'
      . (!$eles ? '<p style="background:#FFF4CC;border:1px solid #E0C060;padding:10px 14px;border-radius:8px;margin:0 0 16px;"><b>TESZT fizetés</b> — nem valódi pénz.</p>' : '')
      . '<h2 style="font-size:18px;margin:0 0 4px;">' . $h($nev) . ($havi ? ' <span style="font-weight:400;color:#565F6B;">(havidíj)</span>' : '') . '</h2>'
      . '<p style="font-size:24px;font-weight:800;margin:0 0 16px;">' . $h(ss_ft($brutto, false) . $pj) . ($havi ? ' / hó' : '') . '</p>'
      . '<p style="background:#E7FBF3;border-left:3px solid #0A9E77;padding:10px 14px;margin:0 0 20px;"><b>A számlát neked kell kiállítanod</b> — a Stripe visszaigazolása nem számla.</p>'
      . '<h3 style="font-size:13px;letter-spacing:.08em;text-transform:uppercase;color:#565F6B;margin:0 0 6px;">Számlázási adatok</h3>'
      . '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:0 0 20px;">'
      . $sor('Vevő neve', $szamlazando !== '' ? $szamlazando : '(nem adta meg)', true)
      . (($cegnev !== '' && $vevo !== '' && $cegnev !== $vevo) ? $sor('Kapcsolattartó', $vevo) : '')
      . $sor('Adószám', $adoszam !== '' ? $adoszam : '(nincs — magánszemély)', $adoszam !== '')
      . $sor('Cím', $cim !== '' ? $cim : '(nem adta meg)')
      . $sor('Email', $email !== '' ? $email : '(nincs)')
      . $sor('Telefon', $telefon !== '' ? $telefon : '(nincs)')
      . '</table>'
      . '<h3 style="font-size:13px;letter-spacing:.08em;text-transform:uppercase;color:#565F6B;margin:0 0 6px;">A számla tétele</h3>'
      . '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:0 0 20px;">'
      . $sor('Megnevezés', $nev . ($havi ? ' (havidíj)' : ''))
      . $sor('Nettó', ss_ft($netto, false))
      . ($afa_sz > 0 ? $sor('ÁFA (' . $afa_sz . '%)', ss_ft($afa, false))
                     : $sor('ÁFA', 'alanyi adómentes — nincs felszámítva'))
      . $sor('Bruttó', ss_ft($brutto, false) . $pj, true)
      . $sor('Fizetve', $datum . ' — bankkártya (Stripe)')
      . $sor('Teljesítés', date('Y-m-d', (int) ($r['fizetve'] ?? time())))
      . '</table>'
      . '<h3 style="font-size:13px;letter-spacing:.08em;text-transform:uppercase;color:#565F6B;margin:0 0 6px;">Azonosítók</h3>'
      . '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:0 0 20px;">'
      . $sor('Csomag', (string) ($r['csomag_id'] ?? ''))
      . $sor('Stripe', (string) ($r['azonosito'] ?? ''))
      . '</table>'
      . (!empty($r['stripe_link']) ? '<p><a href="' . $h($r['stripe_link']) . '" style="color:#0A9E77;">Megnyitás a Stripe Dashboardban</a></p>' : '')
      . ($havi ? '<p style="color:#565F6B;">Ez havidíjas előfizetés: a kártyát havonta újra terheljük, és minden hónapban kapsz egy ilyen levelet — tehát havonta kell számlázni.</p>' : '')
      . '</div>';

    return ['targy' => $targy, 'text' => $text, 'html' => $html];
}
