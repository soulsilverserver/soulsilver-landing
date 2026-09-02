/* =============================================================================
   Böngészős layout-audit a SOULSILVER oldalhoz.

   MIÉRT ÍGY: a Claude Code preview-paneljének képernyőképe nem lát a hajtás
   alá (legörgetve üres képet ad), és a `requestAnimationFrame` sem fut benne.
   Ezért nem szemre ellenőrizünk, hanem iframe-ekben betöltjük az oldalakat a
   kívánt szélességen, és a DOM-ból mérünk.

   HASZNÁLAT:
   1. python -m http.server 8643   (vagy preview_start a launch.json-ből)
   2. navigálj bármelyik oldalra a preview-panelen
   3. másold be ezt a fájlt a javascript_tool-ba (definiálja a window.__audit-ot)
   4. futtasd:  await window.__auditAll([320, 375, 414, 768, 1280])

   FONTOS: a styles.css és az app.js a böngésző cache-éből jön, mert a
   cache-buster csak a HTML-en van. A __auditAll ezért frissíti őket.
   ========================================================================== */

window.__PAGES = [
  'index.html', 'arak.html', 'workflow-automatizalas.html', 'referenciak.html',
  'crm.html', 'dronfelvetel.html', 'weboldalkeszites.html', 'kozossegi-media.html',
  'aftermovie.html', 'markaidentitas.html', 'termekfotozas.html',
  'ppc-hirdeteskezeles.html', 'impresszum.html', 'adatvedelem.html', 'aszf.html',
  'cookie-szabalyzat.html', 'koszonjuk.html'
];

/* Amit tudatosan kihagyunk a tap-target listából:
   - hp-field: a kapcsolati űrlap honeypotja, left:-9999px-en, helyesen rejtett
   - a.brand:  a logó 26px magas, ami a WCAG 2.5.8 24px minimuma fölött van
   - nav-toggle: 44px széles, csak a magassága mérése furcsa                  */
window.__TAP_IGNORE = /hp-field|a\.brand|nav-toggle/;

/* A desktop nav-linkek 21px magasak, DE 32px (--sp-4) terkoz van kozottuk, ami
   kimeriti a WCAG 2.5.8 spacing-kivetelet, es egerrel pontosak. Tudatos dontes,
   ne jelezzuk. A tap-target amugy is touch-kerdes: 620px felett nem mérjük. */
window.__TAP_MAX_WIDTH = 620;

window.__audit = async function (page, W, H) {
  const f = document.createElement('iframe');
  f.style.cssText = 'position:fixed;left:-9999px;top:0;border:0;';
  f.width = W; f.height = H || 850; f.src = '/' + page + '?cb=' + Date.now();
  document.body.appendChild(f);
  await new Promise(r => { f.onload = r; setTimeout(r, 6000); });
  await new Promise(r => setTimeout(r, 350));

  const d = f.contentDocument, w = f.contentWindow;
  const cw = d.documentElement.clientWidth;
  const out = {
    docOverflow: d.body.scrollWidth - cw,
    appJsRan: !!d.querySelector('.wa-float'),   // az app.js legvégén jön létre
    offenders: [], clipped: [], smallTap: [], tiny: [], headings: {}
  };

  const sel = e => {
    const c = ((e.className || '') + '').trim().split(/\s+/).filter(Boolean).slice(0, 2).join('.');
    return e.tagName.toLowerCase() + (c ? '.' + c : '');
  };
  const txt = e => (e.textContent || '').replace(/\s+/g, ' ').trim().slice(0, 38);
  /* Egy elem "kilógása" csak akkor hiba, ha NEM vágja le semmi: a
     scroll-konténerek és a position:fixed elemek szándékosan túlnyúlnak. */
  const isClipped = e => {
    let p = e.parentElement;
    while (p && p !== d.body) {
      const cs = w.getComputedStyle(p);
      if (cs.overflowX !== 'visible' || cs.position === 'fixed') return true;
      p = p.parentElement;
    }
    return false;
  };

  for (const e of d.querySelectorAll('body *')) {
    const cs = w.getComputedStyle(e);
    if (cs.display === 'none' || cs.visibility === 'hidden') continue;
    const r = e.getBoundingClientRect();
    if (!r.width && !r.height) continue;

    if (r.right > cw + 1 && !isClipped(e)) {
      out.offenders.push(sel(e) + ' | right=' + Math.round(r.right) + ' | ' + txt(e));
    }
    /* A .visually-hidden segedosztaly SZANDEKOSAN 1px szeles + overflow:hidden
       (kepernyoolvasonak megmarad, szemmel nem latszik) - az nem levagott szoveg. */
    if (e.children.length === 0 && e.clientWidth > 0
        && e.scrollWidth > e.clientWidth + 1
        && !e.classList.contains('visually-hidden')) {
      out.clipped.push(sel(e) + ' | sw' + e.scrollWidth + '/cw' + e.clientWidth + ' | ' + txt(e));
    }
    if (e.children.length === 0 && txt(e).length > 3 && parseFloat(cs.fontSize) < 11) {
      out.tiny.push(sel(e) + ' | ' + cs.fontSize + ' | ' + txt(e));
    }
  }

  for (const e of d.querySelectorAll('a, button, summary, input, [tabindex]')) {
    const cs = w.getComputedStyle(e);
    if (cs.display === 'none' || cs.visibility === 'hidden') continue;
    const r = e.getBoundingClientRect();
    if (!r.width || !r.height) continue;
    /* A folyó szövegbe ágyazott linkekre a WCAG 2.5.8 inline-kivétele áll. */
    if (e.closest('p, .wf-step, .subnav, td')) continue;
    if (r.width < 40 || r.height < 40) {
      out.smallTap.push(sel(e) + ' | ' + Math.round(r.width) + 'x' + Math.round(r.height) + ' | ' + txt(e));
    }
  }

  const hs = [...d.querySelectorAll('h1,h2,h3,h4,h5,h6')].map(h => +h.tagName[1]);
  out.headings.h1Count = hs.filter(x => x === 1).length;
  out.headings.skips = [];
  for (let i = 1; i < hs.length; i++) {
    if (hs[i] - hs[i - 1] > 1) out.headings.skips.push('h' + hs[i - 1] + '->h' + hs[i]);
  }

  f.remove();
  return out;
};

/* Csak a PROBLÉMÁKAT adja vissza, hogy olvasható maradjon. */
window.__auditAll = async function (widths, pages) {
  await fetch('/styles.css', { cache: 'reload' });
  await fetch('/app.js', { cache: 'reload' });
  const list = pages || window.__PAGES;
  const bad = {};
  let n = 0;
  for (const W of (widths || [320, 375, 1280])) {
    for (const p of list) {
      const a = await window.__audit(p, W);
      n++;
      const r = {};
      if (a.docOverflow) r.ovf = a.docOverflow;
      if (a.offenders.length) r.offenders = a.offenders.slice(0, 3);
      const cl = a.clipped.filter(s => !/SVGAnimated/.test(s));   // SVG <text> vakriasztás
      if (cl.length) r.clipped = cl.slice(0, 3);
      if (!a.appJsRan) r.appJsBroken = true;
      if (a.headings.h1Count !== 1 && p !== 'x') r.h1Count = a.headings.h1Count;
      if (a.headings.skips.length) r.headingSkip = a.headings.skips[0];
      const taps = (W > window.__TAP_MAX_WIDTH ? [] : [...new Set(a.smallTap)])
        .filter(s => !window.__TAP_IGNORE.test(s))
        .filter(s => { const m = s.match(/\| (\d+)x(\d+) \|/); return m && (+m[1] < 24 || +m[2] < 24); });
      if (taps.length) r.under24 = taps.slice(0, 3);
      if (Object.keys(r).length) bad[p + '@' + W] = r;
    }
  }
  return { meresek: n, hibak: Object.keys(bad).length ? bad : 'NINCS' };
};

'audit betöltve — futtasd: await window.__auditAll([320,375,414,768,1280])';
