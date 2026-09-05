# -*- coding: utf-8 -*-
"""Kozos bejaras a kinyereshez ES a cserehez.

MIERT KOZOS MODUL: ha a ket lepes kulon implementalja a bejarast, elcsusznak
egymastol - a kinyero talal egy egyseget, amit a cserelo nem, es az a szoveg
csendben magyarul marad. Itt egyetlen `walk()` van; a kinyero gyujto
kezelovel hivja, a cserelo fordito kezelovel. Amit az egyik lat, azt latja a
masik is.

MIT KEZEL:
  1. <title>
  2. forditando <meta content="...">
  3. forditando attributumok (alt, aria-label, placeholder, title)
  4. SVG-n BELULI <text> - a diagramok feliratai (workflow-anatomia, ROI-abra)
  5. blokk-elemek belso HTML-je, az inline jelolokkel egyutt
  6. maradek szovegcsomopontok (div/span/egyeb)

AZ IKON-TOKEN: sok blokk-elem ikonnal kezdodik
(<li><svg>...</svg>Szoveg</li>). Ha az SVG-t egyszeruen kimaszkolnank, az
egesz egyseg kiesne - eppen ez tortent az arazas csomagtartalmaval. Ezert az
SVG-k helyere [[ikon1]], [[ikon2]] ... kerul, es a forditasban is ott marad;
visszatoltesnel sorrendben allitjuk vissza az EREDETI SVG-ket.
"""
import re

BLOCK = ('h1|h2|h3|h4|h5|h6|p|li|td|th|button|summary|figcaption|label|option'
         '|blockquote|dt|dd|caption|legend')
BLOCK_RE = re.compile(r'<(%s)\b[^>]*>((?:(?!<(?:%s)\b).)*?)</\1>' % (BLOCK, BLOCK), re.S)

CODE_RE = re.compile(r'<(script|style)\b.*?</\1>', re.S)
COMMENT_RE = re.compile(r'<!--.*?-->', re.S)
SVG_RE = re.compile(r'<svg\b.*?</svg>', re.S)
SVGTEXT_RE = re.compile(r'(<text\b[^>]*>)([^<]*)(</text>)')

HAS_LETTER = re.compile(r'[A-Za-zÁÉÍÓÖŐÚÜŰáéíóöőúüű]')
ATTRS = ('alt', 'aria-label', 'placeholder', 'title')
META = ('description', 'og:description', 'twitter:description',
        'og:title', 'twitter:title', 'og:image:alt', 'og:site_name')

ICON = '[[ikon%d]]'
ICON_RE = re.compile(r'\[\[ikon(\d+)\]\]')
PLACE = '\x01%d\x01'
PLACE_RE = re.compile(r'\x01(\d+)\x01')


def has_text(s):
    """Van-e forditando szoveg? A tagneveket nem szamitjuk (a <span></span>-ban
    a "span" szo miatt lenne betu)."""
    return bool(HAS_LETTER.search(re.sub(r'<[^>]+>', ' ', s)))


def walk(html, handler):
    """Vegigmegy a forditando helyeken, es mindegyikre meghivja a handlert.

    handler(szoveg, kind) -> szoveg
    A kinyero valtozatlanul adja vissza (csak gyujt), a cserelo a forditast.
    """
    holes = []

    def stash(text):
        holes.append(text)
        return PLACE % (len(holes) - 1)

    def call(raw, kind):
        key = raw.strip()
        if not key or not has_text(key):
            return raw
        lead = raw[:len(raw) - len(raw.lstrip())]
        trail = raw[len(raw.rstrip()):]
        return lead + handler(key, kind) + trail

    # --- 1. kod es megjegyzes vedelme (soha nem forditando) ---
    s = CODE_RE.sub(lambda m: stash(m.group(0)), html)
    s = COMMENT_RE.sub(lambda m: stash(m.group(0)), s)

    # --- 2. SVG-n beluli feliratok (a maszkolas ELOTT!) ---
    def svg_texts(m):
        return SVGTEXT_RE.sub(
            lambda t: t.group(1) + call(t.group(2), 'svgtext') + t.group(3),
            m.group(0))
    s = SVG_RE.sub(svg_texts, s)

    # --- 3. title / meta / attributumok ---
    s = re.sub(r'(<title>)(.*?)(</title>)',
               lambda m: m.group(1) + call(m.group(2), 'title') + m.group(3),
               s, flags=re.S)

    def meta_sub(m):
        if m.group(2) in META:
            return m.group(1) + m.group(2) + m.group(3) + call(m.group(4), 'meta') + m.group(5)
        return m.group(0)
    s = re.sub(r'(<meta\s+(?:name|property)=")([^"]+)("\s+content=")([^"]*)(")',
               meta_sub, s)

    for a in ATTRS:
        s = re.sub(r'(\b%s=")([^"]*)(")' % a,
                   lambda m: m.group(1) + call(m.group(2), 'attr') + m.group(3), s)

    # --- 4. blokk-elemek ---
    def block_sub(m):
        whole, inner = m.group(0), m.group(2)
        i = m.start(2) - m.start(0)
        j = m.end(2) - m.start(0)

        # az ikonokat tokenre csereljuk, hogy a szoveg egyben maradjon
        icons = []

        def take(mm):
            icons.append(mm.group(0))
            return ICON % len(icons)
        masked = SVG_RE.sub(take, inner)

        out = call(masked, 'block')

        # visszatoltes sorrendben; ami hianyzik a forditasbol, azt a vegere tesszuk
        used = set()

        def put(mm):
            k = int(mm.group(1))
            used.add(k)
            return icons[k - 1] if 1 <= k <= len(icons) else ''
        out = ICON_RE.sub(put, out)
        for k in range(1, len(icons) + 1):
            if k not in used:
                out = icons[k - 1] + out
        return stash(whole[:i] + out + whole[j:])
    s = BLOCK_RE.sub(block_sub, s)

    # --- 5. maradek szovegcsomopontok ---
    s = re.sub(r'(>)([^<>\x01]+)(<)',
               lambda m: m.group(1) + call(m.group(2), 'inline') + m.group(3), s)

    # --- 6. visszatoltes ---
    while PLACE_RE.search(s):
        s = PLACE_RE.sub(lambda m: holes[int(m.group(1))], s)
    return s


def strip_i18n(s):
    """A build altal beszurt darabok eltavolitasa, hogy a lanc idempotens legyen
    es a kinyeres ne a sajat kimenetet olvassa vissza."""
    s = re.sub(r'\n<link rel="alternate" hreflang="[^"]*" href="[^"]*" />', '', s)
    s = re.sub(r'\s*<details class="lang-switch">.*?</details>', '', s, flags=re.S)
    s = re.sub(r'<div class="panel-langs">.*?</div>', '', s, flags=re.S)
    s = re.sub(r'<input type="hidden" name="lang" value="[a-z]{2}">\s*\n\s*', '', s)
    return s
