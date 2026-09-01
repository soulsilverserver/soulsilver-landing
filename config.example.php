<?php
/*
 * PÉLDA konfiguráció. Másold "config.php" néven, és írd bele a valódi
 * Resend API kulcsod. A config.php GITIGNORE-olt — SOHA ne kerüljön
 * a nyilvános GitHub repóba (a benne lévő kulcs titkos).
 *
 * Feltöltés: Hostinger hPanel → File Manager → public_html → itt hozd létre
 * a config.php-t ezzel a tartalommal. (Vagy még biztonságosabban a
 * public_html fölötti mappába — a contact.php azt is megtalálja.)
 */
return [
    // Resend API kulcs — https://resend.com/api-keys
    'resend_api_key' => 're_ide_a_valodi_kulcs',

    // Feladó — a domainnek VERIFIKÁLTNAK kell lennie a Resendben.
    // Teszthez használható: 'onboarding@resend.dev'
    'from' => 'SOULSILVER weboldal <noreply@soulsilver.hu>',

    // Ide érkeznek a megkeresések
    'to' => 'info@soulsilvermarketing.com',
];
