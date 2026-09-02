# -*- coding: utf-8 -*-
"""Az arak konzisztenciaja a NEGY helyen:
 1. a szolgaltatas-oldalak .price-grid-je  (a forras)
 2. tools/gen_charts.py PROJEKT / HAVI listaja (az arak.html diagramjai)
 3. arak.html tablazatos nezete
 4. tools/gen_arlista_pdf.py SERVICES listaja
"""
import io
import os
import re
import glob

os.chdir(r'C:\Users\SOULSILVER\Downloads\SOULSILVER')

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

# ---------- 3. arak.html tablazat ----------
s = io.open('arak.html', encoding='utf-8').read()
i = s.index('<tbody>')
j = s.index('</tbody>')
table = {}
for tr in re.finditer(r'<tr><td><a href="[^"]+">([^<]+)</a></td>(.*?)</tr>', s[i:j], re.S):
    name = tr.group(1)
    cells = re.findall(r'<td class="num">([^<]*)</td>', tr.group(2))
    table[name] = [num(c) if re.search(r'\d', c) else None for c in cells]

# ---------- 4. PDF generator ----------
src = io.open('tools/gen_arlista_pdf.py', encoding='utf-8').read()
pdf = {}
for m in re.finditer(r"\n    \('([^']+)', '[^']*', \[(.*?)\n    \], ", src, re.S):
    name = m.group(1)
    prices = re.findall(r"\('[^']+', '([^']+)'", m.group(2))
    pdf[name] = [num(p) if re.search(r'\d', p) else None for p in prices]

# ---------- osszehasonlitas ----------
print('%-24s %-22s %-22s %-22s %s' % ('szolgaltatas', 'oldal (price-grid)', 'diagram', 'arak.html tabla', 'PDF'))
print('-' * 116)
problems = []
for name in SERVICE_PAGE.values():
    p = pages.get(name, [])
    c = charts.get(name)
    t = table.get(name, [])
    d = pdf.get(name, [])
    fmtl = lambda v: '/'.join('-' if x is None else str(x) for x in v) if v else '(nincs)'
    print('%-24s %-22s %-22s %-22s %s' % (name, fmtl(p), fmtl(c or []), fmtl(t), fmtl(d)))
    ref = [x for x in p]
    for label, other in (('diagram', c), ('arak.html tabla', t), ('PDF', d)):
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
    print('Mind a negy helyen ugyanazok az arak.')
