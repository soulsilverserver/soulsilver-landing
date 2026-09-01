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

// --- Csak POST ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.html#kapcsolat');
    exit;
}

// --- Honeypot: bot kiszűrése (csendben eldobjuk, konverzió nélkül) ---
if (trim($_POST['website'] ?? '') !== '') {
    header('Location: /index.html');
    exit;
}

$name    = trim($_POST['name'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$email   = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $phone === '') {
    header('Location: /index.html?hiba=hianyos#kapcsolat');
    exit;
}

// Email csak akkor kerül fejlécbe/reply_to-ba, ha érvényes (injection ellen).
$email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';

$subject = 'Uj megkereses a soulsilver.hu kapcsolatfelveteli urlaprol';
$text = "Nev: $name\n"
      . "Telefon: $phone\n"
      . ($email !== '' ? "Email: $email\n" : '')
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

header('Location: /koszonjuk.html');
exit;
