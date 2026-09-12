<?php
require __DIR__ . '/stripe-lib.php';

$csomagok = ss_csomagok();
$afa_sz   = (int) round(ss_afa_kulcs() * 100);
$van_kulcs = ss_stripe_secret() !== '';
// Alanyi adómentesség esetén (afa_kulcs = 0) nincs mit rászámolni, és a
// „+ 0% ÁFA" felirat félrevezető lenne.
$afas      = $afa_sz > 0;
$teszt     = $van_kulcs && !ss_eles_mod();

$hibak = [
    'nincs-kulcs'       => 'A kártyás fizetés még nincs beállítva. Írj nekünk, és elküldjük a fizetési linket.',
    'ismeretlen-csomag' => 'Ezt a csomagot nem találjuk. Válassz a lenti listából.',
    'stripe'            => 'A fizetés indítása nem sikerült. Próbáld újra, vagy írj nekünk.',
];
$hiba = $hibak[$_GET['hiba'] ?? ''] ?? '';
$megszakitva = isset($_GET['megszakitva']);

$csoportok = [
    'egyszeri' => ['cim' => 'Egyszeri projektdíj', 'alcim' => 'Egy alkalommal fizetsz, a projekt indulásakor.'],
    'havi'     => ['cim' => 'Havidíjas előfizetés', 'alcim' => 'A kártyát havonta automatikusan terheljük, amíg fel nem mondod.'],
];
?><!doctype html>
<html lang="hu">
<head>
<meta charset="UTF-8" />
<title>Fizetés — SOULSILVER</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="description" content="Fizesd ki bankkártyával a SOULSILVER fix árú csomagjait. Az árak nettó árak, a fizetendő összeg tartalmazza a <?= $afa_sz ?>% ÁFÁ-t." />
<meta name="robots" content="noindex" />
<link rel="canonical" href="https://soulsilver.hu/fizetes.php" />
<link rel="icon" type="image/png" href="favicon.png" />
<link rel="icon" type="image/png" sizes="512x512" href="icon-512.png" />
<link rel="apple-touch-icon" href="apple-touch-icon.png" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
<style>
  /* Csak ezen az oldalon kell — a többit a styles.css adja. */
  .fiz-notice{max-width:820px;margin:0 auto var(--sp-4);padding:var(--sp-2) var(--sp-3);
    border:1px solid var(--edge-strong);border-radius:var(--radius-tile);
    background:var(--surface);color:var(--ink);font-size:.95rem;line-height:1.5;}
  .fiz-notice b{color:var(--ink);}
  .fiz-notice.warn{border-color:var(--mint-deep);}
  .fiz-group{margin-top:var(--sp-8);}
  .fiz-group-head{display:flex;flex-direction:column;gap:.3rem;margin-bottom:var(--sp-4);}
  .fiz-group-head h2{margin:0;font-size:clamp(1.3rem,1.2vw + 1rem,1.9rem);}
  .fiz-group-head p{margin:0;color:var(--ink-soft);}
  .fiz-grid{display:grid;gap:var(--sp-3);grid-template-columns:repeat(auto-fit,minmax(280px,1fr));}
  .fiz-card{display:flex;flex-direction:column;gap:.55rem;padding:var(--sp-3);
    border:1px solid var(--edge);border-radius:var(--radius-card);
    background:var(--surface);box-shadow:var(--shadow);}
  .fiz-card .fiz-nev{font-family:var(--font-display);font-weight:700;font-size:1.05rem;line-height:1.25;}
  .fiz-card .fiz-mit{color:var(--ink-faint);font-size:.85rem;font-family:var(--font-mono);}
  .fiz-card .fiz-brutto{font-family:var(--font-display);font-weight:800;
    font-size:1.55rem;letter-spacing:-.02em;margin-top:.2rem;}
  .fiz-card .fiz-brutto small{font-family:var(--font-body);font-weight:600;font-size:.8rem;color:var(--ink-soft);}
  .fiz-card .fiz-netto{color:var(--ink-soft);font-size:.85rem;margin-bottom:.2rem;}
  .fiz-card form{margin-top:auto;}
  .fiz-card .btn{width:100%;}
  .fiz-reszlet{font-size:.8rem;color:var(--ink-faint);}
  .fiz-reszlet a{color:inherit;}
</style>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-17312625266"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'AW-17312625266');
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
  <a href="arak.html" class="btn btn-ghost" style="margin-left:auto;">Árlista</a>
</nav>

<main id="tartalom">
  <section style="padding-top:calc(var(--sp-16) + 1rem);">
    <div class="wrap">

      <div class="sec-head">
        <div class="eyebrow">Fizetés</div>
        <h1 style="margin-top:.8rem;font-size:clamp(1.9rem,2.6vw + 1rem,3rem);">Fizetés bankkártyával.</h1>
        <p>A fix árú csomagokat itt ki tudod fizetni. A kártyaadatokat a Stripe kezeli — hozzánk nem jut el belőlük semmi.</p>
      </div>

<?php if ($megszakitva): ?>
      <p class="fiz-notice">Megszakítottad a fizetést — nem történt terhelés. Ha elakadtál, <a href="index.html#kapcsolat">írj nekünk</a>.</p>
<?php endif; ?>
<?php if ($hiba !== ''): ?>
      <p class="fiz-notice warn"><b>Hiba:</b> <?= ss_h($hiba) ?></p>
<?php endif; ?>
<?php if (!$van_kulcs): ?>
      <p class="fiz-notice warn"><b>A kártyás fizetés még nincs élesítve.</b>
        A csomagokat lentebb látod, de a fizetés gombok addig nem működnek, amíg a Stripe kulcsok
        nincsenek beállítva. Addig <a href="index.html#kapcsolat">írj nekünk</a>, és küldünk fizetési linket
        vagy átutalásos díjbekérőt.</p>
<?php elseif ($teszt): ?>
      <p class="fiz-notice warn"><b>TESZT mód.</b> Ez az oldal most a Stripe teszt-kulcsaival fut,
        valódi pénz nem mozog. Éles kártyát ne adj meg.</p>
<?php endif; ?>

<?php if ($afas): ?>
      <p class="fiz-notice">Az árlistában <b>nettó</b> árak szerepelnek. Itt a <b>fizetendő bruttó</b> összeget
        mutatjuk, ami tartalmazza a <?= $afa_sz ?>% ÁFÁ-t. Az „egyedi ár" csomagokra ajánlat készül —
        azokat nem lehet kattintásra megvenni, <a href="index.html#kapcsolat">kérj ajánlatot</a>.</p>
<?php else: ?>
      <p class="fiz-notice">A feltüntetett összeg a <b>fizetendő végösszeg</b> — alanyi adómentesség miatt
        ÁFA nem kerül felszámításra. Az „egyedi ár" csomagokra ajánlat készül —
        azokat nem lehet kattintásra megvenni, <a href="index.html#kapcsolat">kérj ajánlatot</a>.</p>
<?php endif; ?>

<?php foreach ($csoportok as $tipus => $meta):
        $lista = array_filter($csomagok, function ($cs) use ($tipus) {
            return ($cs['tipus'] ?? 'egyszeri') === $tipus;
        });
        if (!$lista) continue; ?>
      <div class="fiz-group">
        <div class="fiz-group-head">
          <h2><?= ss_h($meta['cim']) ?></h2>
          <p><?= ss_h($meta['alcim']) ?></p>
        </div>
        <div class="fiz-grid">
<?php   foreach ($lista as $id => $cs):
            $netto  = (int) $cs['netto'];
            $brutto = ss_brutto($netto); ?>
          <div class="fiz-card">
            <div class="fiz-nev"><?= ss_h($cs['nev']) ?></div>
            <div class="fiz-mit"><?= ss_h($cs['mit']) ?></div>
            <div class="fiz-brutto"><?= ss_ft($brutto) ?><?= $tipus === 'havi' ? '<small>/hó</small>' : '' ?></div>
            <div class="fiz-netto"><?= $afas ? ss_ft($netto) . ' + ' . $afa_sz . '% ÁFA' : 'Alanyi adómentes — nincs ÁFA' ?></div>
            <div class="fiz-reszlet"><a href="<?= ss_h($cs['oldal']) ?>">Mit tartalmaz?</a></div>
            <form method="post" action="stripe-checkout.php">
              <input type="hidden" name="csomag" value="<?= ss_h($id) ?>" />
              <button type="submit" class="btn btn-primary btn-block"<?= $van_kulcs ? '' : ' disabled aria-disabled="true"' ?>>
                <?= $tipus === 'havi' ? 'Előfizetek' : 'Fizetés' ?>
              </button>
            </form>
          </div>
<?php   endforeach; ?>
        </div>
      </div>
<?php endforeach; ?>

      <p class="price-note" style="margin-top:var(--sp-6);">
        A fizetés gombra kattintva a Stripe biztonságos fizetőoldalára kerülsz.
        A megrendeléssel elfogadod az <a href="aszf.html">ÁSZF-et</a> és az
        <a href="adatvedelem.html">adatkezelési tájékoztatót</a>.
        A számlát a fizetés után külön küldjük — a Stripe visszaigazolása nem számla.
      </p>

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
