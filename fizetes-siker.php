<?php
/*
 * SOULSILVER — sikeres fizetés visszairányító oldala.
 *
 * Ez az oldal CSAK visszajelzés a vevőnek. A megrendelés tényét nem innen
 * könyveljük — azt a stripe-webhook.php dolgozza fel, mert ezt az URL-t a
 * látogató soha nem is töltheti be (bezárja a fület), vagy épp többször is.
 */
require __DIR__ . '/stripe-lib.php';

$sid = (string) ($_GET['session_id'] ?? '');
$ok        = false;
$fizetve   = false;
$nev       = '';
$brutto    = 0;
$email     = '';
$havi      = false;

// A Stripe session azonosító formátuma fix — csak ilyet kérdezünk le.
if (preg_match('/^cs_[A-Za-z0-9_]+$/', $sid) === 1 && ss_stripe_secret() !== '') {
    $res = ss_stripe_api('GET', '/v1/checkout/sessions/' . $sid);
    if ($res['ok']) {
        $s  = $res['body'];
        $md = $s['metadata'] ?? [];
        // Idegen (pl. Protein Bázis) session adatait nem jelenítjük meg.
        if (($md['webhely'] ?? '') === 'soulsilver.hu') {
            $ok      = true;
            $fizetve = in_array($s['payment_status'] ?? '', ['paid', 'no_payment_required'], true);
            $csomag  = ss_csomag((string) ($md['csomag'] ?? ''));
            $nev     = $csomag['nev'] ?? (string) ($md['csomag'] ?? '');
            $brutto  = (int) round(((int) ($s['amount_total'] ?? 0)) / 100);
            $email   = (string) ($s['customer_details']['email'] ?? '');
            $havi    = ($md['tipus'] ?? '') === 'havi';
        }
    } else {
        ss_log('siker oldal: session lekerdezes hiba: ' . $res['error']);
    }
}
?><!doctype html>
<html lang="hu">
<head>
<meta charset="UTF-8" />
<title>Sikeres fizetés — SOULSILVER</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="robots" content="noindex" />
<link rel="icon" type="image/png" href="favicon.png" />
<link rel="icon" type="image/png" sizes="512x512" href="icon-512.png" />
<link rel="apple-touch-icon" href="apple-touch-icon.png" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-17312625266"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'AW-17312625266');
<?php if ($ok && $fizetve): ?>
  /* Vásárlás mérése. FIGYELEM: a Google Ads-ben ehhez külön konverziós
     műveletet kell létrehozni (mint a koszonjuk.html-en a lead), és a
     send_to címkét ide beírni — addig ez csak GA4-stílusú purchase esemény. */
  gtag('event', 'purchase', {
    'value': <?= (int) $brutto ?>,
    'currency': 'HUF',
    'items': [{ 'item_name': <?= json_encode($nev, JSON_UNESCAPED_UNICODE) ?> }]
  });
<?php endif; ?>
</script>
</head>
<body>
<a class="skip-link" href="#tartalom">Ugrás a tartalomra</a>
<div class="liquid-bg" aria-hidden="true">
  <span class="blob blob-a"></span>
  <span class="blob blob-b"></span>
  <span class="blob blob-c"></span>
  <span class="blob blob-d"></span>
</div>
<div class="grain-veil" aria-hidden="true"></div>

<nav class="topnav glass">
  <a class="brand" href="index.html" style="text-decoration:none;">SOULSILVER<span class="dot">.</span></a>
</nav>

<main id="tartalom">
  <section class="final-cta" style="padding-top:calc(var(--sp-16) + 2rem); min-height:60vh; display:flex; align-items:center;">
    <div class="wrap">
      <div class="eyebrow" style="justify-content:center;">Fizetés</div>
<?php if ($ok && $fizetve): ?>
      <h1 style="margin-top:.8rem; font-size:clamp(1.9rem,2.6vw + 1rem,3rem);">Megérkezett. Köszönjük!</h1>
      <p><b><?= ss_h($nev) ?></b> — <?= ss_ft($brutto) ?><?= $havi ? '/hó' : '' ?> (bruttó).
<?php   if ($email !== ''): ?>
        A visszaigazolást elküldtük a <b><?= ss_h($email) ?></b> címre.
<?php   endif; ?>
      </p>
      <p>A számlát külön küldjük emailben — a Stripe visszaigazolása nem számla.
        24 órán belül jelentkezünk az indulás részleteivel.
<?php   if ($havi): ?>
        Az előfizetést bármikor felmondhatod, ha írsz nekünk.
<?php   endif; ?>
      </p>
<?php elseif ($ok): ?>
      <h1 style="margin-top:.8rem; font-size:clamp(1.9rem,2.6vw + 1rem,3rem);">A fizetés feldolgozás alatt.</h1>
      <p>A bankod még nem véglegesítette a tranzakciót. Amint megérkezik, emailben visszaigazoljuk —
        nem kell újra fizetnie.</p>
<?php else: ?>
      <h1 style="margin-top:.8rem; font-size:clamp(1.9rem,2.6vw + 1rem,3rem);">Köszönjük!</h1>
      <p>A fizetés részleteit most nem tudjuk megjeleníteni, de ha a bankod visszaigazolta,
        a megrendelés nálunk is megvan. Ha bizonytalan vagy,
        <a href="index.html#kapcsolat">írj nekünk</a> és megnézzük.</p>
<?php endif; ?>
      <a href="index.html" class="btn btn-primary" style="margin-top:var(--sp-5);">Vissza a főoldalra</a>
    </div>
  </section>
</main>

<footer>
  <div class="wrap">
    <div class="foot-bottom" style="justify-content:center;">
      <span>© <?= date('Y') ?> SOULSILVER Marketing Agency</span>
      <nav class="foot-legal" aria-label="Jogi információk">
        <a href="adatvedelem.html">Adatvédelem</a>
        <a href="cookie-szabalyzat.html">Cookie-szabályzat</a>
        <a href="aszf.html">ÁSZF</a>
        <a href="impresszum.html">Impresszum</a>
      </nav>
    </div>
  </div>
</footer>
<script src="app.js" defer></script>
</body>
</html>
