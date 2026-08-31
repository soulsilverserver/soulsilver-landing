<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.html#kapcsolat');
    exit;
}

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $phone === '') {
    header('Location: /index.html?hiba=hianyos#kapcsolat');
    exit;
}

$to = 'info@soulsilvermarketing.com';
$subject = 'Uj megkereses a soulsilver.hu kapcsolatfelveteli urlaprol';
$body = "Nev: $name\r\nTelefon: $phone\r\n\r\nUzenet:\r\n$message\r\n";
$headers = "From: SOULSILVER weboldal <noreply@soulsilver.hu>\r\n" .
           "Reply-To: $to\r\n" .
           "Content-Type: text/plain; charset=UTF-8\r\n";

@mail($to, $subject, $body, $headers);

header('Location: /koszonjuk.html');
exit;
