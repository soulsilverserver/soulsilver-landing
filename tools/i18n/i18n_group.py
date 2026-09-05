# -*- coding: utf-8 -*-
"""Az egysegeket forditasi adagokra bontja.

1. KOZOS: ami tobb oldalon szerepel (nav, lablec, urlap, ismetlodo CTA-k) -
   ezt egyszer forditjuk, es 22 oldalon hasznaljuk.
2. Oldalankent: a tobbi, dokumentum-sorrendben, hogy kontextussal lehessen
   forditani (egy cimsor utani bekezdes tudja, mirol szol).
"""
import collections
import io
import json
import os

HERE = os.path.dirname(os.path.abspath(__file__))
REPO = os.path.dirname(os.path.dirname(HERE))
TRDIR = os.path.join(REPO, 'tools', 'i18n', 'translations')
OUT = os.path.join(REPO, 'tools', '_out', 'i18n')

units = json.load(io.open(os.path.join(OUT, 'units.json'), encoding='utf-8'),
                  object_pairs_hook=collections.OrderedDict)

common = collections.OrderedDict()
per_page = collections.OrderedDict()

for text, meta in units.items():
    if len(meta['pages']) > 1:
        common[text] = meta['kind']
    else:
        per_page.setdefault(meta['pages'][0], collections.OrderedDict())[text] = meta['kind']

# adagok: a kozos elore, utana oldalak meret szerint csokkenoen
batches = [('_kozos', common)]
for page in sorted(per_page, key=lambda p: -len(per_page[p])):
    batches.append((page, per_page[page]))

print('%-38s %6s %7s' % ('adag', 'egyseg', 'szo'))
print('-' * 54)
total_u = total_w = 0
import re

for name, d in batches:
    w = sum(len(re.sub(r'<[^>]+>', ' ', t).split()) for t in d)
    total_u += len(d)
    total_w += w
    print('%-38s %6d %7d' % (name, len(d), w))
print('-' * 54)
print('%-38s %6d %7d' % ('OSSZESEN', total_u, total_w))

manifest = [{'nev': n, 'egysegek': list(d.keys())} for n, d in batches]
io.open(os.path.join(OUT, 'batches.json'), 'w', encoding='utf-8').write(
    json.dumps(manifest, ensure_ascii=False, indent=1))
print('\nkiirva: batches.json (%d adag)' % len(batches))
