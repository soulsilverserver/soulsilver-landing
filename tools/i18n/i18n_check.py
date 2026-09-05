# -*- coding: utf-8 -*-
"""Melyik egysegek vannak leforditva es melyik hianyzik, adagonkent."""
import glob
import io
import json
import os
import sys

HERE = os.path.dirname(os.path.abspath(__file__))
REPO = os.path.dirname(os.path.dirname(HERE))
TRDIR = os.path.join(REPO, 'tools', 'i18n', 'translations')
OUT = os.path.join(REPO, 'tools', '_out', 'i18n')


LANG = sys.argv[1] if len(sys.argv) > 1 else 'en'

batches = json.load(io.open(os.path.join(OUT, 'batches.json'), encoding='utf-8'))
have = {}
for f in glob.glob(os.path.join(TRDIR, LANG, '*.json')):
    d = json.load(io.open(f, encoding='utf-8'))
    for k, v in d.items():
        have[k] = v

print('%-38s %6s %6s %6s' % ('adag', 'kell', 'kesz', 'hiany'))
print('-' * 60)
missing_total = 0
for b in batches:
    need = b['egysegek']
    miss = [t for t in need if t not in have]
    missing_total += len(miss)
    flag = '' if not miss else ('  <-- %d hianyzik' % len(miss))
    print('%-38s %6d %6d %6d%s'
          % (b['nev'], len(need), len(need) - len(miss), len(miss), flag))
print('-' * 60)
total = sum(len(b['egysegek']) for b in batches)
print('%-38s %6d %6d %6d' % ('OSSZESEN', total, total - missing_total, missing_total))

# ures vagy gyanus forditasok
suspicious = [k for k, v in have.items() if not str(v).strip()]
if suspicious:
    print('\nURES forditas: %d' % len(suspicious))
    for k in suspicious[:5]:
        print('  ', k[:70])

# olyan kulcs a forditasban, ami nincs a forrasban (elgepeles)
allowed = set()
for b in batches:
    allowed.update(b['egysegek'])
extra = [k for k in have if k not in allowed]
if extra:
    print('\nISMERETLEN kulcs (nincs ilyen a forrasban) - valoszinuleg elgepeles: %d' % len(extra))
    for k in extra[:10]:
        print('  ', repr(k[:70]))
