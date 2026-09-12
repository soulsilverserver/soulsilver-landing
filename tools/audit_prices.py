# -*- coding: utf-8 -*-
"""Az arak konzisztenciaja az OT helyen:
 1. a szolgaltatas-oldalak .price-grid-je  (a forras)
 2. tools/gen_charts.py PROJEKT / HAVI listaja (az arak.html diagramjai)
 3. arak.html svc-table-je
 4. tools/gen_arlista_pdf.py SERVICES listaja
 5. stripe-csomagok.php (amit a kartyas fizetes valoban terhel)
"""
import io
import os
import re
import glob

# A repo gyokere. A regi, bedrotozott Windows-utvonal csak akkor lep be, ha
# letezik - igy a szkript a repo barmely masolatabol is fut (macOS/Linux).
_WIN = r'C:\Users\SOULSILVER\Downloads\SOULSILVER'
os.chdir(_WIN if os.path.isdir(_WIN)
         else os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

NB = '\u00a0'


def norm(s):
    return s.replace(NB, ' ').replace('\u00ad', '').strip()


def num(s):
    d = re.sub(r'[^0-9]', '', s.replace(NB, ''))
    return int(d) if d else None


# ---------- 1. a szolgaltatas-oldalak price-grid-je ----------
SERVICE_PAGE = {
    'ppc-hirdeteskezeles.html': 'PPC hirdetéskezelés',
    'workflow-automatizalas.html': 'Workflow automatizálás',
    'markaidentitas.html': 'Márkaidentitás',
    'kozossegi-media.html': 'Közösségi média',
    'weboldalkeszites.html': 'Weboldalkészítés',
    'dronfelvetel.html': 'Drónfelvétel',
    'aftermovie.html': 'Aftermovie',
    'termekfotozas.html': 'Termékfotózás',
    'crm.html': 'SOULSILVER CRM',
}

pages = {}
for f, name in SERVICE_PAGE.items():
    s = io.open(f, encoding='utf-8').read()
    i = s.index('<div class="price-grid">')
    j = s.index('</section>', i)
    block = s[i:j]
    amounts = re.findall(r'<div class="price-amount">(.*?)</div>', block, re.S)
    vals = []
    for a in amounts:
        a = re.sub(r'<[^>]+>', ' ', a)
        vals.append(num(a))
    pages[name] = vals

# ---------- 2. gen_charts.py ----------
src = io.open('tools/gen_charts.py', encoding='utf-8').read()
charts = {}
for block_name in ('PROJEKT', 'HAVI'):
    m = re.search(block_name + r' = \[(.*?)\n\]', src, re.S)
    for row in re.finditer(r"\('([^']+)',\s*(\d+),\s*(\d+),\s*(None|\d+)", m.group(1)):
        charts[row.group(1)] = [int(row.group(2)), int(row.group(3)),
                                None if row.group(4) == 'None' else int(row.group(4))]

# ---------- 3. arak.html svc-table ----------
# A sorfej <th scope="row">, nem <td>: az arazas-atepiteskor a .price-grid
# helyere igazi tablazat kerult. A szolgaltatas-nev utan egy .svc-what span is
# all a fejcellaban, ezert a nevet a linkbol vesszuk, nem a cella szovegebol.
s = io.open('arak.html', encoding='utf-8').read()
i = s.index('<tbody>')
j = s.index('</tbody>')
table = {}
row_re = re.compile(r'<th scope="row"><a href="[^"]+">([^<]+)</a>.*?</th>(.*?)</tr>', re.S)
for tr in row_re.finditer(s[i:j]):
    name = tr.group(1)
    cells = re.findall(r'<td class="num">([^<]*)</td>', tr.group(2))
    # "Egyedi ar" = nincs listaaras tetel -> None, ugyanugy mint a price-grid-ben
    table[name] = [num(c) if re.search(r'\d', c) else None for c in cells]
if len(table) != 9:
    print('FIGYELEM: az arak.html tablabol %d sor olvasodott be, nem 9 - '
          'valoszinuleg valtozott a markup.' % len(table))

# ---------- 4. PDF generator ----------
src = io.open('tools/gen_arlista_pdf.py', encoding='utf-8').read()
pdf = {}
for m in re.finditer(r"\n    \('([^']+)', '[^']*', \[(.*?)\n    \], ", src, re.S):
    name = m.group(1)
    prices = re.findall(r"\('[^']+', '([^']+)'", m.group(2))
    pdf[name] = [num(p) if re.search(r'\d', p) else None for p in prices]

# ---------- 5. stripe-csomagok.php (a kartyas fizetes arai) ----------
# Ez az egyetlen hely, ahol a rossz ar nem csak elirast jelent, hanem hibas
# terhelest is, ezert kulon ellenorizzuk. Az "Egyedi ar" csomagok itt
# szandekosan nem szerepelnek -> None.
TIERS = ['Belépő', 'Középső', 'Felső']
src = io.open('stripe-csomagok.php', encoding='utf-8').read()
stripe = {}
for m in re.finditer(r"'nev' => '([^']+)',\s*'netto' => (\d+)", src):
    nev, netto = m.group(1), int(m.group(2))
    if ' — ' not in nev:
        print('FIGYELEM: ertelmezhetetlen csomagnev a stripe-csomagok.php-ban: %s' % nev)
        continue
    base, tier = nev.split(' — ', 1)
    stripe.setdefault(base, {})[tier] = netto
stripe = dict((base, [t.get(x) for x in TIERS]) for base, t in stripe.items())

# ---------- osszehasonlitas ----------
print('%-24s %-22s %-22s %-22s %-22s %s' % ('szolgaltatas', 'oldal (price-grid)', 'diagram', 'arak.html tabla', 'PDF', 'stripe'))
print('-' * 140)
problems = []
for name in SERVICE_PAGE.values():
    p = pages.get(name, [])
    c = charts.get(name)
    t = table.get(name, [])
    d = pdf.get(name, [])
    st = stripe.get(name, [])
    fmtl = lambda v: '/'.join('-' if x is None else str(x) for x in v) if v else '(nincs)'
    print('%-24s %-22s %-22s %-22s %-22s %s' % (name, fmtl(p), fmtl(c or []), fmtl(t), fmtl(d), fmtl(st)))
    ref = [x for x in p]
    for label, other in (('diagram', c), ('arak.html tabla', t), ('PDF', d),
                         ('stripe-csomagok.php', st)):
        if other is None:
            problems.append('%s: HIANYZIK a %s-bol' % (name, label))
            continue
        if other != ref:
            problems.append('%s: %s = %s, de az oldalon %s' % (name, label, other, ref))

print()
if problems:
    print('*** ELTERESEK:')
    for x in problems:
        print('  -', x)
else:
    print('Mind az ot helyen ugyanazok az arak.')
