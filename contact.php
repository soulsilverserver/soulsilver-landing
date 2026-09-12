<?php
/*
 * Kapcsolatfelvételi űrlap feldolgozó — Resend API-val küld emailt.
 *
 * A titkos API kulcs NEM ebben a fájlban van (a repo publikus), hanem a
 * gitignore-olt "config.php"-ban, amit kézzel kell a szerverre feltölteni.
 * Lásd: config.example.php
 */

// --- Konfiguráció betöltése (env vagy config.php) ---
$cfg = [];
if (is_file(__DIR__ . '/../config.php')) {          // legbiztonságosabb: public_html-en KÍVÜL
    $cfg = require __DIR__ . '/../config.php';
} elseif (is_file(__DIR__ . '/config.php')) {       // vagy a mappában (gitignore-olt)
    $cfg = require __DIR__ . '/config.php';
}
$RESEND_API_KEY = getenv('RESEND_API_KEY') ?: ($cfg['resend_api_key'] ?? '');
$FROM = $cfg['from'] ?? 'SOULSILVER weboldal <noreply@soulsilver.hu>';
$TO   = $cfg['to']   ?? 'info@soulsilvermarketing.com';

/**
 * Ahova a hibas beküldest visszakuldjuk. Korabban mindig a fooldalra ment,
 * igy egy /epitoipari-marketing.html-rol indulo erdeklodo elvesztette a
 * kampany-uzenetet es a sajat urlapjat is. A Referer-t NEM engedjuk at
 * nyersen (fejlec-injection es nyilt atiranyitas ellen): csak a sajat
 * hosztunkrol szarmazo, sima .html utvonalat fogadjuk el.
 */
function vissza_az_urlaphoz($hiba = '')
{
    $cel = '/index.html';
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    if ($ref !== '') {
        $p = parse_url($ref);
        $sajat = $_SERVER['HTTP_HOST'] ?? '';
        if (!empty($p['host']) && $p['host'] === $sajat
            && !empty($p['path']) && preg_match('#^(/(en|de|es))?/[A-Za-z0-9_-]+\.html$#', $p['path'])) {
            $cel = $p['path'];
        }
    }
    return $cel . ($hiba !== '' ? '?hiba=' . rawurlencode($hiba) : '') . '#kapcsolat';
}

/**
 * A csendben eldobott beküldeseket naplozzuk. Enelkul nem lehet megmondani,
 * hogy "nem jon a lead" azert van-e, mert senki nem ir, vagy azert, mert a
 * szuroink eszik meg oket. A fajl gitignore-olt, es nem tartalmaz uzenetet.
 */
function lead_drop_log($ok, $reszlet = '')
{
    $sor = sprintf(
        "%s\t%s\t%s\tref=%s\tua=%s\n",
        date('c'),
        $ok,
        (string) $reszlet,
        substr($_SERVER['HTTP_REFERER'] ?? '-', 0, 120),
        substr($_SERVER['HTTP_USER_AGENT'] ?? '-', 0, 120)
    );
    // A public_html FOLE irunk elsokent: a gyokerbe kerulo .log fajlokat a
    // webszerver kiszolgalja (a lead-webhook.log-nal ez elo is fordult),
    // ez a naplo pedig referert es user-agentet tartalmaz.
    foreach ([__DIR__ . '/../lead-drop.log', __DIR__ . '/lead-drop.log'] as $f) {
        if (@file_put_contents($f, $sor, FILE_APPEND | LOCK_EX) !== false) {
            @chmod($f, 0600);
            return;
        }
    }
    error_log('SOULSILVER lead-drop: ' . $sor);
}

// --- Csak POST ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.html#kapcsolat');
    exit;
}

// --- Honeypot: bot kiszűrése (csendben eldobjuk, konverzió nélkül) ---
if (trim($_POST['website'] ?? '') !== '') {
    lead_drop_log('honeypot');
    header('Location: /index.html');
    exit;
}

// --- Idő-csapda: a JS az oldalbetöltés óta eltelt ms-et küldi a beküldéskor.
//
// FONTOS: ez korabban MINDEN 1200 ms alatti erteket eldobott, a hianyzo/0
// erteket is. Csakhogy a 0 nem bot-jel: az a mezo alapertelmezett erteke,
// es pontosan 0 marad akkor is, ha az app.js barmiert nem fut le (halozati
// hiba, reklamblokkolo, regi cache). Ilyenkor egy valodi erdeklodo urlapja
// nyom nelkul eltunt, o meg a fooldalon kotott ki abban a hitben, hogy
// elkuldte. Egy kimaradt lead tobbe kerul, mint egy spam email, ezert itt
// most szandekosan "fail open" a szabaly:
//   - ts hianyzik vagy 0  => ATENGEDJUK, de megjeloljuk a targyban
//   - 0 < ts < 1200       => valodi sebesseg-jel, eldobjuk (ehhez futott a JS)
// A csendes eldobas ettol fuggetlenul naplozodik, hogy lassuk, mennyi van.
$tsRaw     = $_POST['ts'] ?? '';
$elapsedMs = (int) $tsRaw;
$tsHianyzik = ($tsRaw === '' || $elapsedMs === 0);
if (!$tsHianyzik && $elapsedMs < 1200) {
    lead_drop_log('ido-csapda', $elapsedMs);
    header('Location: ' . vissza_az_urlaphoz('tulgyors'));
    exit;
}

$name    = trim($_POST['name'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$email   = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

// Melyik landing oldalrol jott a lead (kampany-attribucio).
// SZIGORU feherlista: a subject fejlecbe is bekerul, ezert sorveg-karakter
// semmikeppen nem maradhat benne (mail() header-injection).
$forras = preg_replace('/[^A-Za-z0-9 _-]/', '', $_POST['forras'] ?? '');
$forras = substr(trim($forras), 0, 40);

if ($name === '' || $phone === '') {
    header('Location: ' . vissza_az_urlaphoz('hianyos'));
    exit;
}

// Email csak akkor kerül fejlécbe/reply_to-ba, ha érvényes (injection ellen).
$email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';

$subject = 'Uj megkereses a soulsilver.hu kapcsolatfelveteli urlaprol'
         . ($forras !== '' ? ' [' . $forras . ']' : '')
         . ($tsHianyzik ? ' [ts-hianyzik]' : '');
$text = "Nev: $name\n"
      . "Telefon: $phone\n"
      . ($email !== '' ? "Email: $email\n" : '')
      . ($forras !== '' ? "Forras: $forras\n" : '')
      . "\nUzenet:\n$message\n";

$sent = false;

// --- 1) Resend API (ha van kulcs és cURL) ---
if ($RESEND_API_KEY !== '' && function_exists('curl_init')) {
    $payload = [
        'from'    => $FROM,
        'to'      => [$TO],
        'subject' => $subject,
        'text'    => $text,
    ];
    if ($email !== '') {
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
    $resp = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $sent = ($code >= 200 && $code < 300);
}

// --- 2) Fallback: natív mail(), hogy egy lead se vesszen el ---
if (!$sent) {
    $headers = "From: $FROM\r\n"
             . 'Reply-To: ' . ($email !== '' ? $email : $TO) . "\r\n"
             . "Content-Type: text/plain; charset=UTF-8\r\n";
    @mail($TO, $subject, $text, $headers);
}

// A latogato azon a nyelven kapja vissza a koszonooldalt, ahonnan kuldte.
// FEHERLISTA, nem atengedes: a $_POST-bol jovo erteket sosem tesszuk
// kozvetlenul Location fejlecbe (fejlec-injection).
$lang = $_POST['lang'] ?? 'hu';
$prefix = in_array($lang, ['en', 'de', 'es'], true) ? '/' . $lang : '';
header('Location: ' . $prefix . '/koszonjuk.html');
exit;
