<?php
/*
 * PÉLDA konfiguráció. Másold "config.php" néven, és írd bele a valódi
 * kulcsaidat. A config.php GITIGNORE-olt — SOHA ne kerüljön
 * a nyilvános GitHub repóba (a benne lévő kulcsok titkosak).
 *
 * A kulcsokat nem kell kézzel beírni: a repo gyökeréből futtasd a
 *     bash bin/setup-keys.sh          (terminál)
 *     bash bin/kulcsok-ablak.command  (ablakos, macOS)
 * scriptet — az beírja őket ebbe a fájlba.
 *
 * Feltöltés: Hostinger hPanel → File Manager → public_html → itt hozd létre
 * a config.php-t ezzel a tartalommal. (Vagy még biztonságosabban a
 * public_html fölötti mappába — a contact.php és a stripe-lib.php azt is
 * megtalálja.)
 */
return [
    // ───── Resend (email küldés) ─────
    // Resend API kulcs — https://resend.com/api-keys
    'resend_api_key' => 're_ide_a_valodi_kulcs',

    // Feladó — a domainnek VERIFIKÁLTNAK kell lennie a Resendben.
    // Teszthez használható: 'onboarding@resend.dev'
    'from' => 'SOULSILVER weboldal <noreply@soulsilver.hu>',

    // Ide érkeznek a megkeresések és a fizetési értesítők
    'to' => 'info@soulsilvermarketing.com',

    // ───── Stripe (kártyás fizetés) ─────
    // Stripe Dashboard → Developers → API keys
    // Teszthez a pk_test_/sk_test_ párost használd, élesben a pk_live_/sk_live_-ot.
    // A kettőt SOHA ne keverd (a publishable és a secret ugyanabból a módból kell).
    'stripe_publishable_key' => 'pk_test_ide_a_valodi_kulcs',
    'stripe_secret_key'      => 'sk_test_ide_a_valodi_kulcs',

    // Stripe Dashboard → Developers → Webhooks → az endpoint „Signing secret"-je.
    // Enélkül a stripe-webhook.php MINDEN kérést eldob (nem ellenőrizhető).
    'stripe_webhook_secret'  => 'whsec_ide_a_valodi_kulcs',

    // ÁFA-kulcs a nettó árakra. 0.27 = 27%.
    'afa_kulcs' => 0.27,

    // A saját domain — a Stripe ide irányít vissza fizetés után.
    // Szándékosan nem a Host fejlécből, mert azt a látogató hamisíthatja.
    'site_url' => 'https://soulsilver.hu',
];
