# -*- coding: utf-8 -*-
"""A nyelvi valtozatok legyartasa.

SZERKEZET: /en/, /de/, /es/ almappak, UGYANAZOKKAL a fajlnevekkel. Igy minden
oldalak-kozotti relativ link (arak.html, index.html#kapcsolat) valtozatlanul
mukodik a mappan belul - nem kell slug-terkep, es nem tudunk torott linket
gyartani. Az ESZKOZ-utak viszont eltornenek (/en/styles.css nem letezik),
ezert azokat gyoker-abszolutta irjuk at.

A horgony-ID-k (#munkaink, #kapcsolat, #csomagok) MAGYARUL maradnak minden
nyelven: ezek azonositok, nem szoveg.

!!! MIERT NEM html.replace(magyar, angol) !!!
Az elso valtozat globalis szovegcserevel dolgozott, es az "a" -> "the" egyseg
a JELOLOT is atirta: <html lang= -> <html ltheng=, facebook.svg ->
fthecebook.svg. A csere ezert POZICIOHOZ KOTOTT: pontosan ott cserelunk, ahol
a kinyero talalt egyseget (title, meta, attributum, blokk-elem belseje,
maradek szovegcsomopont) - sehol mashol. A ket lepes ugyanazokat a mintakat
hasznalja, tehat amit a kinyero megtalalt, azt a cserelo is megtalalja.
"""
import glob
import io
import json
import os
import re
import shutil

HERE = os.path.dirname(os.path.abspath(__file__))
REPO = os.path.dirname(os.path.dirname(HERE))
TRDIR = os.path.join(REPO, 'tools', 'i18n', 'translations')
TR = os.path.join(REPO, 'tools', '_out', 'i18n')
if not os.path.isdir(TR):
    os.makedirs(TR)
os.chdir(REPO)

LANGS = ['en', 'de', 'es']
LOCALE = {'hu': 'hu_HU', 'en': 'en_US', 'de': 'de_DE', 'es': 'es_ES'}
LANGNAME = {'hu': 'Magyar', 'en': 'English', 'de': 'Deutsch', 'es': 'Español'}
BASE = 'https://soulsilver.hu/'

PAGES = sorted(p for p in glob.glob('*.html')
               if not p.startswith('google') and not p.startswith('_'))
LEGAL = {'impresszum.html', 'adatvedelem.html', 'aszf.html',
         'cookie-szabalyzat.html'}

BLOCK = ('h1|h2|h3|h4|h5|h6|p|li|td|th|button|summary|figcaption|label|option'
         '|blockquote|dt|dd|caption|legend')
BLOCK_RE = re.compile(r'<(%s)\b[^>]*>((?:(?!<(?:%s)\b).)*?)</\1>' % (BLOCK, BLOCK), re.S)
SKIP_PARENT = re.compile(r'<(script|style|svg)\b.*?</\1>', re.S)
HAS_LETTER = re.compile(r'[A-Za-zÁÉÍÓÖŐÚÜŰáéíóöőúüű]')
ATTRS = ('alt', 'aria-label', 'placeholder', 'title')
META = ('description', 'og:description', 'twitter:description',
        'og:title', 'twitter:title', 'og:image:alt', 'og:site_name')

LEGAL_NOTE = {
    'en': ('<p class="legal-note"><strong>This is a courtesy translation.</strong> '
           'The legally binding version of this document is the Hungarian one — '
           'in case of any discrepancy, <a href="/%(page)s">the Hungarian text</a> '
           'prevails.</p>'),
    'de': ('<p class="legal-note"><strong>Dies ist eine Übersetzung zu '
           'Informationszwecken.</strong> Rechtlich verbindlich ist ausschließlich '
           'die ungarische Fassung — bei Abweichungen gilt '
           '<a href="/%(page)s">der ungarische Text</a>.</p>'),
    'es': ('<p class="legal-note"><strong>Esta es una traducción de cortesía.</strong> '
           'La versión jurídicamente vinculante de este documento es la húngara; '
           'en caso de discrepancia, prevalece '
           '<a href="/%(page)s">el texto en húngaro</a>.</p>'),
}


def load_lang(lang):
    d = {}
    for f in sorted(glob.glob(os.path.join(TRDIR, lang, '*.json'))):
        d.update(json.load(io.open(f, encoding='utf-8')))
    return d


ASSET = re.compile(
    r'(?P<attr>\b(?:href|src|action)=")(?!https?:|//|/|#|mailto:|tel:|data:)'
    r'(?P<path>[^"]*\.(?:css|js|png|jpg|jpeg|svg|ico|mp4|webm|webmanifest|php))"')


def rewrite_assets(html):
    return ASSET.sub(lambda m: m.group('attr') + '/' + m.group('path') + '"', html)


def hreflang_block(page):
    """Kolcsonosnek kell lennie: ugyanez a blokk kerul a magyar oldalakra is,
    kulonben a Google nem fogadja el a parositast."""
    tail = '' if page == 'index.html' else page
    out = ['<link rel="alternate" hreflang="hu" href="%s%s" />' % (BASE, tail)]
    for lg in LANGS:
        out.append('<link rel="alternate" hreflang="%s" href="%s%s/%s" />'
                   % (lg, BASE, lg, tail))
    out.append('<link rel="alternate" hreflang="x-default" href="%s%s" />' % (BASE, tail))
    return '\n'.join(out)


def href_for(lg, page):
    return ('/' if lg == 'hu' else '/%s/' % lg) + ('' if page == 'index.html' else page)


def switcher(cur, page):
    items = []
    for lg in ['hu'] + LANGS:
        if lg == cur:
            items.append('<li><span aria-current="true">%s</span></li>' % LANGNAME[lg])
        else:
            items.append('<li><a href="%s" hreflang="%s">%s</a></li>'
                         % (href_for(lg, page), lg, LANGNAME[lg]))
    return ('<details class="lang-switch"><summary aria-label="Nyelv / Language">'
            '%s</summary><ul>%s</ul></details>' % (cur.upper(), ''.join(items)))


def panel_switcher(cur, page):
    out = ['<a href="%s" hreflang="%s">%s</a>' % (href_for(lg, page), lg, LANGNAME[lg])
           for lg in ['hu'] + LANGS if lg != cur]
    return '<div class="panel-langs">%s</div>' % ''.join(out)


# ---------------------------------------------------------------- forditas
def translate(html, tr, stats):
    holes = []

    def stash(text):
        holes.append(text)
        return '\x01%d\x01' % (len(holes) - 1)

    def get(raw):
        """A kinyero strip()-elt kulcsot tarolt, ezert itt is strip-elunk, de a
        korulotte levo whitespace-t visszatesszuk - kulonben elcsuszna a
        formazas."""
        key = raw.strip()
        if not key:
            return raw
        lead = raw[:len(raw) - len(raw.lstrip())]
        trail = raw[len(raw.rstrip()):]
        if key in tr:
            stats['applied'] += 1
            return lead + tr[key] + trail
        if HAS_LETTER.search(re.sub(r'<[^>]+>', ' ', key)):
            stats['missing'] += 1
            stats['samples'].add(key[:80])
        return raw

    # 1. kod kimaszkolasa (soha ne forditsuk)
    s = SKIP_PARENT.sub(lambda m: stash(m.group(0)), html)

    # 2. title
    s = re.sub(r'(<title>)(.*?)(</title>)',
               lambda m: m.group(1) + get(m.group(2)) + m.group(3), s, flags=re.S)

    # 3. meta content
    def meta_sub(m):
        if m.group(2) in META:
            return m.group(1) + m.group(2) + m.group(3) + get(m.group(4)) + m.group(5)
        return m.group(0)
    s = re.sub(r'(<meta\s+(?:name|property)=")([^"]+)("\s+content=")([^"]*)(")',
               meta_sub, s)

    # 4. forditando attributumok
    for a in ATTRS:
        s = re.sub(r'(\b%s=")([^"]*)(")' % a,
                   lambda m: m.group(1) + get(m.group(2)) + m.group(3), s)

    # 5. blokk-elemek: a belso HTML-t forditjuk, majd az EGESZ elemet
    #    kimaszkoljuk, hogy a 6. lepes ne turkaljon bele ujra
    def block_sub(m):
        whole = m.group(0)
        i = m.start(2) - m.start(0)
        j = m.end(2) - m.start(0)
        return stash(whole[:i] + get(m.group(2)) + whole[j:])
    s = BLOCK_RE.sub(block_sub, s)

    # 6. maradek szovegcsomopontok (div/span/egyeb)
    s = re.sub(r'(>)([^<>\x01]+)(<)',
               lambda m: m.group(1) + get(m.group(2)) + m.group(3), s)

    # 7. visszatoltes (kivulrol befele, mert a blokkok tartalmazhatnak holes-t)
    while '\x01' in s:
        s = re.sub(r'\x01(\d+)\x01', lambda m: holes[int(m.group(1))], s)
    return s


def strip_i18n(s):
    """Minden korabban beszurt i18n-darabot eltavolit.

    MIERT KELL: a build a magyar oldalakba is beleir (hreflang, valaszto,
    rejtett lang mezo). A kovetkezo futas mar ezt a - modositott - forrast
    olvassa, es ha nem tisztitanank, az /en/ oroklone a magyar valasztot es a
    magyar hreflanget is, a sajatja MELLE. Igy viszont a build barmilyen
    allapotbol ugyanazt az eredmenyt adja."""
    s = re.sub(r'\n<link rel="alternate" hreflang="[^"]*" href="[^"]*" />', '', s)
    s = re.sub(r'\s*<details class="lang-switch">.*?</details>', '', s, flags=re.S)
    s = re.sub(r'<div class="panel-langs">.*?</div>', '', s, flags=re.S)
    s = re.sub(r'<input type="hidden" name="lang" value="[a-z]{2}">\s*\n\s*', '', s)
    return s


def build_page(page, lang, tr, stats):
    s = strip_i18n(io.open(page, encoding='utf-8').read())
    s = translate(s, tr, stats)
    s = rewrite_assets(s)
    s = s.replace('<html lang="hu">', '<html lang="%s">' % lang, 1)

    newloc = BASE + lang + '/' + ('' if page == 'index.html' else page)
    s = re.sub(r'<link rel="canonical" href="[^"]*"\s*/?>',
               '<link rel="canonical" href="%s" />\n%s' % (newloc, hreflang_block(page)),
               s, count=1)
    s = re.sub(r'(<meta property="og:url" content=")[^"]*(")',
               lambda m: m.group(1) + newloc + m.group(2), s, count=1)
    s = re.sub(r'(<meta property="og:locale" content=")[^"]*(")',
               lambda m: m.group(1) + LOCALE[lang] + m.group(2), s, count=1)

    s = s.replace('<div class="nav-cta">',
                  '  ' + switcher(lang, page) + '\n  <div class="nav-cta">', 1)
    s = re.sub(r'(<div class="mobile-panel[^"]*"[^>]*>.*?)(</div>)',
               lambda m: m.group(1) + panel_switcher(lang, page) + m.group(2),
               s, count=1, flags=re.S)

    if page in LEGAL:
        m = re.search(r'<h1>.*?</h1>', s, re.S)
        if m:
            s = s[:m.end()] + '\n      ' + LEGAL_NOTE[lang] % {'page': page} + s[m.end():]
            stats['legal'] += 1

    if 'name="lang"' not in s:
        s = s.replace('<input type="text" name="website"',
                      '<input type="hidden" name="lang" value="%s">\n'
                      '            <input type="text" name="website"' % lang)

    io.open(os.path.join(lang, page), 'w', encoding='utf-8', newline='').write(s)


def main():
    for lang in LANGS:
        if os.path.isdir(lang):
            shutil.rmtree(lang)
        os.makedirs(lang)
        tr = load_lang(lang)
        stats = {'applied': 0, 'missing': 0, 'legal': 0, 'samples': set()}
        for page in PAGES:
            build_page(page, lang, tr, stats)
        print('/%s/  %d oldal | szotar %4d | csere %5d | forditatlan %5d | jogi %d'
              % (lang, len(PAGES), len(tr), stats['applied'], stats['missing'],
                 stats['legal']))
        io.open(os.path.join(TR, '%s-hianyzik.txt' % lang), 'w',
                encoding='utf-8').write('\n'.join(sorted(stats['samples'])))

    n = 0
    for page in PAGES:
        s = strip_i18n(io.open(page, encoding='utf-8').read())
        s = re.sub(r'(<link rel="canonical" href="[^"]*"\s*/?>)',
                   lambda m: m.group(1) + '\n' + hreflang_block(page), s, count=1)
        s = s.replace('<div class="nav-cta">',
                      '  ' + switcher('hu', page) + '\n  <div class="nav-cta">', 1)
        s = re.sub(r'(<div class="mobile-panel[^"]*"[^>]*>.*?)(</div>)',
                   lambda m: m.group(1) + panel_switcher('hu', page) + m.group(2),
                   s, count=1, flags=re.S)
        s = s.replace('<input type="text" name="website"',
                      '<input type="hidden" name="lang" value="hu">\n'
                      '            <input type="text" name="website"')
        io.open(page, 'w', encoding='utf-8', newline='').write(s)
        n += 1
    print('magyar oldalak: hreflang + valaszto + lang mezo -> %d' % n)


if __name__ == '__main__':
    main()
