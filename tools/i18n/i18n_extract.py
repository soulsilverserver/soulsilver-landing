# -*- coding: utf-8 -*-
"""Forditando egysegek kinyerese a SOULSILVER oldalrol.

MIERT NEM SZOVEGCSOMOPONTONKENT: a mondatok belsejeben inline jelolok vannak
(<strong>, <a>, <em>), amik kulon szovegcsomopontokra vagjak a mondatot. Ha
darabonkent forditanank, a szorend osszeomlana. Ezert a BLOKK-elemek belso
HTML-je az egyseg - az inline jelolok bent maradnak, es a forditasban is
ott lesznek.

MIERT VAN MASODIK KOR: nem minden szoveg all blokk-elemben; ezen az oldalon
sok szoveg <div class="eyebrow">, <span class="pill"> stb. belsejeben van. Az
elso kor talalatait kimaszkoljuk, es ami szoveg utana is marad, azt kulon
egysegkent vesszuk fel. Igy nem maradhat le semmi.
"""
import collections
import glob
import io
import json
import os
import re

HERE = os.path.dirname(os.path.abspath(__file__))
REPO = os.path.dirname(os.path.dirname(HERE))
TRDIR = os.path.join(REPO, 'tools', 'i18n', 'translations')
OUT = os.path.join(REPO, 'tools', '_out', 'i18n')
if not os.path.isdir(OUT):
    os.makedirs(OUT)
os.chdir(REPO)

PAGES = sorted(p for p in glob.glob('*.html')
               if not p.startswith('google') and not p.startswith('_'))

BLOCK = ('h1|h2|h3|h4|h5|h6|p|li|td|th|button|summary|figcaption|label|option'
         '|blockquote|dt|dd|caption|legend')

# Ezek NEM forditandok: kod, ikon, gepi tartalom.
SKIP_PARENT = re.compile(r'<(script|style|svg)\b.*?</\1>', re.S)

# Van-e benne barmi betu? (a "80", "6,25%", "—" nem forditando)
HAS_LETTER = re.compile(r'[A-Za-zÁÉÍÓÖŐÚÜŰáéíóöőúüű]')

ATTRS = ('alt', 'aria-label', 'placeholder', 'title')
META = ('description', 'og:description', 'twitter:description',
        'og:title', 'twitter:title', 'og:image:alt', 'og:site_name')


def strip_i18n(s):
    """A build altal a magyar oldalakba beszurt darabok eltavolitasa.

    Enelkul a kinyeres nem stabil: a nyelvvalaszto sajat szovegei ("Magyar",
    "English", "Nyelv / Language") es a hreflang-sorok minden ujabb korben
    ujabb "forditando egysegkent" jelennenek meg.
    """
    s = re.sub(r'\n<link rel="alternate" hreflang="[^"]*" href="[^"]*" />', '', s)
    s = re.sub(r'\s*<details class="lang-switch">.*?</details>', '', s, flags=re.S)
    s = re.sub(r'<div class="panel-langs">.*?</div>', '', s, flags=re.S)
    s = re.sub(r'<input type="hidden" name="lang" value="[a-z]{2}">\s*\n\s*', '', s)
    return s


def mask_code(s):
    """A script/style/svg tartalmat kimaszkoljuk, hogy ne kerulhessen a kinyert
    egysegek koze - ugyanakkora helyorzovel, hogy az offsetek ne csusszanak."""
    return SKIP_PARENT.sub(lambda m: '\x00' * len(m.group(0)), s)


units = collections.OrderedDict()   # szoveg -> {'kind':..., 'pages':set()}


def add(text, kind, page):
    text = text.strip()
    # A betu-vizsgalat a SZOVEGRE megy, nem a jelolore: a "<span></span>"-ban a
    # tagnev miatt van betu, de forditando szoveg nincs benne.
    if not text or not HAS_LETTER.search(re.sub(r'<[^>]+>', ' ', text)):
        return
    if '\x00' in text:
        return
    u = units.setdefault(text, {'kind': kind, 'pages': set()})
    u['pages'].add(page)


leftovers = collections.Counter()

for page in PAGES:
    raw = strip_i18n(io.open(page, encoding='utf-8').read())
    masked = mask_code(raw)

    # ---- title ----
    m = re.search(r'<title>(.*?)</title>', masked, re.S)
    if m:
        add(m.group(1), 'title', page)

    # ---- meta ----
    for m in re.finditer(r'<meta\s+(?:name|property)="([^"]+)"\s+content="([^"]*)"',
                         masked):
        if m.group(1) in META:
            add(m.group(2), 'meta', page)

    # ---- forditando attributumok ----
    for a in ATTRS:
        for m in re.finditer(r'\b%s="([^"]*)"' % a, masked):
            add(m.group(1), 'attr', page)

    # ---- 1. kor: blokk-elemek belso HTML-je ----
    # A minta nem engedi meg ugyanannak a blokk-tagnek a beagyazasat (pl. <li>
    # <p>-vel), ezert a legbelso talalatot veszi - ami helyes egyseg-hatar.
    body = masked
    block_re = re.compile(r'<(%s)\b[^>]*>((?:(?!<(?:%s)\b).)*?)</\1>'
                          % (BLOCK, BLOCK), re.S)
    consumed = body
    for m in block_re.finditer(body):
        add(m.group(2), 'block', page)
    consumed = block_re.sub(lambda m: '\x00' * len(m.group(0)), body)

    # ---- 2. kor: ami szoveg megmaradt (div/span/egyeb) ----
    for m in re.finditer(r'>([^<>\x00]+)<', consumed):
        t = m.group(1)
        if HAS_LETTER.search(t) and t.strip():
            add(t, 'inline', page)
            leftovers[t.strip()[:60]] += 1

# ---------------------------------------------------------------- osszegzes
by_kind = collections.Counter(u['kind'] for u in units.values())
shared = sum(1 for u in units.values() if len(u['pages']) > 1)
words = sum(len(re.sub(r'<[^>]+>', ' ', t).split()) for t in units)

print('Egysegek osszesen: %d  (%d szo)' % (len(units), words))
for k, n in by_kind.most_common():
    print('  %-8s %5d' % (k, n))
print('  tobb oldalon ismetlodo: %d' % shared)
print()
print('Ez %d egyseg x 3 nyelv = %d forditas.' % (len(units), len(units) * 3))
print()
print('A 2. korben talalt (nem blokk-elemben allo) szovegek - mintak:')
for t, n in leftovers.most_common(12):
    print('  %2dx  %s' % (n, t))

data = collections.OrderedDict(
    (t, {'kind': u['kind'], 'pages': sorted(u['pages'])})
    for t, u in units.items())
io.open(os.path.join(OUT, 'units.json'), 'w', encoding='utf-8').write(
    json.dumps(data, ensure_ascii=False, indent=1))
print('\nkiirva: %s' % os.path.join(OUT, 'units.json'))
