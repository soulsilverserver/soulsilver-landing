# -*- coding: utf-8 -*-
"""Statikus audit a SOULSILVER siten: hivatkozasok, ID-k, alt, nav, tag-balansz."""
import io, os, re, glob, collections

ROOT = r'C:\Users\SOULSILVER\Downloads\SOULSILVER'
os.chdir(ROOT)

# A _preview_*.html gitignore-olt, lokalis munkafajl - nem deployol, ezert
# nem szabad hibaként jeleznie (nincs benne nav, canonical, app.js).
pages = sorted(p for p in glob.glob('*.html')
               if not p.startswith('google') and not p.startswith('_'))
problems = collections.OrderedDict()


def add(page, kind, detail):
    problems.setdefault(page, []).append((kind, detail))


href_re = re.compile(r'(?:href|src)="([^"]+)"')
srcset_re = re.compile(r'<source[^>]+src="([^"]+)"')
bgurl_re = re.compile(r'url\(([^)]+)\)')
id_re = re.compile(r'\bid="([^"]+)"')
img_re = re.compile(r'<img\b[^>]*>')
navlink_re = re.compile(r'<ul class="nav-links">(.*?)</ul>', re.S)

all_ids = {}

for page in pages:
    html = io.open(page, encoding='utf-8').read()

    # --- 1. lokalis hivatkozasok / assetek ---
    refs = set(href_re.findall(html))
    refs |= set(srcset_re.findall(html))
    for u in bgurl_re.findall(html):
        refs.add(u.strip('\'"'))
    for u in sorted(refs):
        if u.startswith(('http://', 'https://', 'mailto:', 'tel:', '#', 'data:', '//')):
            continue
        path = u.split('#')[0].split('?')[0]
        if not path:
            continue
        if not os.path.exists(path):
            add(page, 'MISSING', path)

    # --- 2. horgony-hivatkozasok ugyanarra az oldalra ---
    ids = set(id_re.findall(html))
    all_ids[page] = ids
    for u in sorted(refs):
        if u.startswith('#') and len(u) > 1:
            if u[1:] not in ids:
                add(page, 'DEAD ANCHOR', u)

    # --- 3. duplikalt ID ---
    dupes = [i for i, c in collections.Counter(id_re.findall(html)).items() if c > 1]
    for d in dupes:
        add(page, 'DUPLICATE ID', d)

    # --- 4. img alt nelkul ---
    for tag in img_re.findall(html):
        if 'alt=' not in tag:
            add(page, 'IMG NO ALT', tag[:90])

    # --- 5. tag-balansz a fontos elemekre ---
    for tag in ('div', 'section', 'button', 'main', 'span'):
        o = len(re.findall(r'<%s\b' % tag, html))
        c = len(re.findall(r'</%s>' % tag, html))
        if o != c:
            add(page, 'TAG IMBALANCE', '%s: %d nyito / %d zaro' % (tag, o, c))

    # --- 6. app.js / styles.css bekotve? ---
    if 'app.js' not in html:
        add(page, 'NO APP.JS', '-')
    if 'styles.css' not in html:
        add(page, 'NO STYLES', '-')

    # --- 7. canonical egyezik a fajlnevvel? ---
    m = re.search(r'<link rel="canonical" href="([^"]+)"', html)
    if not m:
        add(page, 'NO CANONICAL', '-')
    else:
        want = 'https://soulsilver.hu/' + ('' if page == 'index.html' else page)
        if m.group(1) != want:
            add(page, 'CANONICAL MISMATCH', '%s (elvart: %s)' % (m.group(1), want))


# --- 8. nav konzisztencia ---
# CSAK a menupont-feliratokat es a sorrendjuket hasonlitjuk, a hrefeket NEM:
# az index.html-en ugyanezek a pontok sajat oldalon beluli horgonyok
# (#crm, #kapcsolat), mashol viszont teljes utak (crm.html, index.html#kapcsolat).
# Mindketto helyes, csak mas oldalrol nezve. A hrefek ervenyesseget az 1. pont
# (torott link / halott horgony) mar ellenorzi, itt duplikalva csak vakriasztas
# lenne - korabban minden futas jelezte az indexet.
navs = {}
for page in pages:
    html = io.open(page, encoding='utf-8').read()
    m = navlink_re.search(html)
    if m:
        # A href NEM feltetlen az egyetlen attributum: az aktuális oldal linkje
        # aria-current="page"-et is visel. A regi, [^"]+"> vegu minta ezeket
        # kihagyta, igy pont arrol az oldalrol nem latszott az elteres, ahol a
        # sajat menupontja allt (a blog.html-en igy maradt eszrevetlen).
        labels = re.findall(r'<a href="[^"]+"[^>]*>([^<]+)</a>', m.group(1))
        navs[page] = tuple(labels)
if navs:
    counts = collections.Counter(navs.values())
    canonical_nav = counts.most_common(1)[0][0]
    for page, nav in navs.items():
        if nav != canonical_nav:
            add(page, 'NAV DIFFERS', '%s (elvart: %s)'
                % (' | '.join(nav), ' | '.join(canonical_nav)))
# A koszonjuk.html-en SZANDEKOSAN nincs menu, csak a logo: ez a konverzio-
# visszaigazolo oldal, ahol minden tovabbi link elterelne. A logo visszavisz a
# fooldalra, tobb navigacio nem kell.
NAVLESS_OK = {'koszonjuk.html'}
for page in pages:
    if page not in navs and page not in NAVLESS_OK:
        add(page, 'NO NAV', '-')

# --- 9. sitemap vs valosag ---
sm = io.open('sitemap.xml', encoding='utf-8').read()
listed = set(re.findall(r'<loc>https://soulsilver\.hu/([^<]*)</loc>', sm))
listed = {(l or 'index.html') for l in listed}
for page in pages:
    if page in ('koszonjuk.html',) or page.startswith('google'):
        continue
    if page not in listed:
        add('sitemap.xml', 'NOT IN SITEMAP', page)
for l in sorted(listed):
    if not os.path.exists(l):
        add('sitemap.xml', 'SITEMAP DEAD URL', l)

# ---------- kimenet ----------
print('Vizsgalt oldalak: %d\n' % len(pages))
if not problems:
    print('Nem talaltam problemat.')
for page, items in problems.items():
    print('--- %s ---' % page)
    for kind, detail in items:
        print('  [%s] %s' % (kind, detail))
    print('')
