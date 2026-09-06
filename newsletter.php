<?php
/*
 * Hírlevél feliratkozás — a beérkező emailt a Resenden át értesítésként
 * elküldi az info@ címre (a contact.php-val közös config.php-t használja).
 * Fetch (AJAX) hívásra JSON-t ad vissza; sima POST-ra átirányít.
 *
 * Bővíthető: valódi listaépítéshez a Resend Audiences API-ra is fel lehet
 * venni a feliratkozót (audience_id a config.php-ban) — lásd lentebb.
 */

$cfg = [];
if (is_file(__DIR__ . '/../config.php')) {
    $cfg = require __DIR__ . '/../config.php';
} elseif (is_file(__DIR__ . '/config.php')) {
    $cfg = require __DIR__ . '/config.php';
}
$KEY  = getenv('RESEND_API_KEY') ?: ($cfg['resend_api_key'] ?? '');
$FROM = $cfg['from'] ?? 'SOULSILVER weboldal <noreply@soulsilver.hu>';
$TO   = $cfg['to']   ?? 'info@soulsilvermarketing.com';
$AUDIENCE = $cfg['resend_audience_id'] ?? '';

$isAjax = isset($_POST['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== '');

function respond($ok, $isAjax, $msg = '') {
    if ($isAjax) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['ok' => $ok, 'msg' => $msg]);
    } else {
        header('Location: ' . ($ok ? '/koszonjuk.html' : '/index.html?hiba=hirlevel#hirlevel'));
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.html#hirlevel');
    exit;
}

// Honeypot: bot esetén úgy teszünk, mintha sikerült volna.
if (trim($_POST['website'] ?? '') !== '') {
    respond(true, $isAjax, 'Köszönjük a feliratkozást!');
}

$email = trim($_POST['email'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, $isAjax, 'Érvénytelen email cím.');
}

// --- 1) Opcionális: felvétel a Resend Audience-be (ha be van állítva) ---
if ($AUDIENCE !== '' && $KEY !== '' && function_exists('curl_init')) {
    $ch = curl_init('https://api.resend.com/audiences/' . rawurlencode($AUDIENCE) . '/contacts');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $KEY, 'Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode(['email' => $email, 'unsubscribed' => false]),
        CURLOPT_TIMEOUT => 15,
    ]);
    curl_exec($ch);
    curl_close($ch);
}

// --- 2) Értesítő email az ügynökségnek (mindig) ---
$subject = 'Uj hirlevel feliratkozo: ' . $email;
$body    = "Uj hirlevel feliratkozo a soulsilver.hu-rol.\r\n\r\n"
         . "Email: $email\r\n"
         . "Idopont: " . date('Y-m-d H:i') . "\r\n";

$sent = false;
if ($KEY !== '' && function_exists('curl_init')) {
    $payload = [
        'from'     => $FROM,
        'to'       => [$TO],
        'subject'  => $subject,
        'text'     => $body,
        'reply_to' => $email,
    ];
    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $KEY, 'Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 15,
    ]);
    curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $sent = ($code >= 200 && $code < 300);
}
if (!$sent) {
    $headers = "From: $FROM\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8\r\n";
    @mail($TO, $subject, $body, $headers);
}

respond(true, $isAjax, 'Köszönjük a feliratkozást!');
