<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.html#kapcsolat');
    exit;
}

// Honeypot: valódi felhasználó üresen hagyja; a botok kitöltik.
// Ha ki van töltve, csendben eldobjuk (nem megy köszönőoldalra, így konverzió sem tüzel).
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

// Csak érvényes emailt engedünk a Reply-To fejlécbe (header-injection ellen).
$email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';

$to      = 'info@soulsilvermarketing.com';
$subject = 'Uj megkereses a soulsilver.hu kapcsolatfelveteli urlaprol';
$body    = "Nev: $name\r\n"
         . "Telefon: $phone\r\n"
         . ($email !== '' ? "Email: $email\r\n" : '')
         . "\r\nUzenet:\r\n$message\r\n";

// From a saját domainen (SPF-hez igazodik), Reply-To a beküldőre, ha adott emailt.
$headers = "From: SOULSILVER weboldal <noreply@soulsilver.hu>\r\n"
         . 'Reply-To: ' . ($email !== '' ? $email : $to) . "\r\n"
         . "Content-Type: text/plain; charset=UTF-8\r\n";

@mail($to, $subject, $body, $headers);

header('Location: /koszonjuk.html');
exit;
