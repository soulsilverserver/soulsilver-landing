# -*- coding: utf-8 -*-
"""Forditando egysegek kinyerese a SOULSILVER oldalrol.

A bejarast az i18n_common.walk() vegzi - UGYANAZ, amit a cserelo hasznal.
Itt a handler csak gyujt, es valtozatlanul adja vissza a szoveget.

Futtatas:  python3 tools/i18n/i18n_extract.py
Kimenet:   tools/_out/i18n/units.json
"""
import collections
import glob
import io
import json
import os
import re
import sys

HERE = os.path.dirname(os.path.abspath(__file__))
REPO = os.path.dirname(os.path.dirname(HERE))
OUT = os.path.join(REPO, 'tools', '_out', 'i18n')
sys.path.insert(0, HERE)
if not os.path.isdir(OUT):
    os.makedirs(OUT)
os.chdir(REPO)

import i18n_common as C   # noqa: E402

PAGES = sorted(p for p in glob.glob('*.html')
               if not p.startswith('google') and not p.startswith('_'))

units = collections.OrderedDict()
by_kind = collections.Counter()

for page in PAGES:
    raw = C.strip_i18n(io.open(page, encoding='utf-8').read())

    def collect(text, kind, _page=page):
        u = units.setdefault(text, {'kind': kind, 'pages': set()})
        u['pages'].add(_page)
        return text

    C.walk(raw, collect)

for t, u in units.items():
    by_kind[u['kind']] += 1

words = sum(len(re.sub(r'<[^>]+>', ' ', t).split()) for t in units)
shared = sum(1 for u in units.values() if len(u['pages']) > 1)

print('Egysegek: %d  (%d szo)' % (len(units), words))
for k, n in by_kind.most_common():
    print('  %-8s %5d' % (k, n))
print('  tobb oldalon ismetlodo: %d' % shared)

data = collections.OrderedDict(
    (t, {'kind': u['kind'], 'pages': sorted(u['pages'])}) for t, u in units.items())
io.open(os.path.join(OUT, 'units.json'), 'w', encoding='utf-8').write(
    json.dumps(data, ensure_ascii=False, indent=1))
print('kiirva: tools/_out/i18n/units.json')
