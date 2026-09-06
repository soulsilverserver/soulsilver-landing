# -*- coding: utf-8 -*-
"""Egy adag MEG NEM forditott egysegeit irja ki, forditasra kesz formaban."""
import glob
import io
import json
import os
import sys

HERE = os.path.dirname(os.path.abspath(__file__))
REPO = os.path.dirname(os.path.dirname(HERE))
OUT = os.path.join(REPO, 'tools', '_out', 'i18n')
TRDIR = os.path.join(REPO, 'tools', 'i18n', 'translations')

lang = sys.argv[1]
names = sys.argv[2:]

have = {}
for f in glob.glob(os.path.join(TRDIR, lang, '*.json')):
    have.update(json.load(io.open(f, encoding='utf-8')))

batches = json.load(io.open(os.path.join(OUT, 'batches.json'), encoding='utf-8'))
n = 0
for b in batches:
    if names and b['nev'] not in names:
        continue
    miss = [t for t in b['egysegek'] if t not in have]
    if not miss:
        continue
    print('### %s  (%d hianyzik)' % (b['nev'], len(miss)))
    for t in miss:
        print(t)
        print('---')
    n += len(miss)
print('# osszesen: %d' % n)
