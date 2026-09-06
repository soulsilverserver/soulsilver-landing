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

// --- IDEIGLENES DIAGNOSZTIKA (token-védett) — futtatás után eltávolítandó ---
if (($_POST['diag'] ?? '') === 'a7f3k9x2') {
    header('Content-Type: application/json; charset=UTF-8');
    $out = ['key_present' => ($KEY !== ''), 'curl' => function_exists('curl_init')];
    // list
    $ch = curl_init('https://api.resend.com/audiences');
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $KEY]]);
    $lr = curl_exec($ch); $out['list_code'] = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    $out['list_body'] = substr((string) $lr, 0, 400);
    // create
    $ch = curl_init('https://api.resend.com/audiences');
    curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $KEY, 'Content-Type: application/json'], CURLOPT_POSTFIELDS => json_encode(['name' => 'SOULSILVER hirlevel'])]);
    $cr = curl_exec($ch); $out['create_code'] = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    $out['create_body'] = substr((string) $cr, 0, 400);
    $cd = json_decode((string) $cr, true); $aid = $cd['id'] ?? '';
    if ($aid) {
        $ch = curl_init('https://api.resend.com/audiences/' . rawurlencode($aid) . '/contacts');
        curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $KEY, 'Content-Type: application/json'], CURLOPT_POSTFIELDS => json_encode(['email' => 'diag@soulsilver.hu', 'unsubscribed' => false])]);
        $ar = curl_exec($ch); $out['contact_code'] = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
        $out['contact_body'] = substr((string) $ar, 0, 400);
    }
    echo json_encode($out);
    exit;
}

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

// --- 1) Audience (lista): config-beli id, vagy a "SOULSILVER hirlevel" lista
//        automatikus megkeresése/létrehozása, majd a feliratkozó felvétele. ---
$audId = $AUDIENCE;
if ($KEY !== '' && function_exists('curl_init')) {
    if ($audId === '') {
        // meglévő azonos nevű lista?
        $ch = curl_init('https://api.resend.com/audiences');
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $KEY]]);
        $ld = json_decode(curl_exec($ch), true);
        curl_close($ch);
        if (!empty($ld['data'])) {
            foreach ($ld['data'] as $a) {
                if (($a['name'] ?? '') === 'SOULSILVER hirlevel') { $audId = $a['id']; break; }
            }
        }
        // ha nincs, létrehozzuk
        if ($audId === '') {
            $ch = curl_init('https://api.resend.com/audiences');
            curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15,
                CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $KEY, 'Content-Type: application/json'],
                CURLOPT_POSTFIELDS => json_encode(['name' => 'SOULSILVER hirlevel'])]);
            $cd = json_decode(curl_exec($ch), true);
            curl_close($ch);
            $audId = $cd['id'] ?? '';
        }
    }
    // feliratkozó felvétele a listára
    if ($audId !== '') {
        $ch = curl_init('https://api.resend.com/audiences/' . rawurlencode($audId) . '/contacts');
        curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $KEY, 'Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode(['email' => $email, 'unsubscribed' => false])]);
        curl_exec($ch);
        curl_close($ch);
    }
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
