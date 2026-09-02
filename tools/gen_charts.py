# -*- coding: utf-8 -*-
"""Ártartomány-diagramok SVG-je a SOULSILVER árlista-oldalhoz.

Forma: range bar — a sáv a belépő és a középső csomag ára között fut, a nyitott
végű („Egyedi ár") szintet szaggatott folytatás jelzi.

MIÉRT KÉT DIAGRAM: a havidíjas és a projektalapú szolgáltatás más mértékegység
(Ft/hó vs Ft). Egy tengelyre tenni őket ugyanaz a hiba, mint a két y-skála.

MIÉRT KÉT VÁLTOZAT MINDEGYIKBŐL: a széles változat viewBox-a 760 egység. Egy
375px-es telefonon a görgethető keret ~246px, tehát a felirat ~0,79-szeresére
kicsinyedik — a 9,5px-es értékcímke ~7,5px-en jelenne meg, és egyszerre a
diagram 41%-a látszana.

A mobil változat ezért NEM SVG, hanem HTML/CSS sávlista. Az SVG-szöveg a
konténerrel együtt skálázódik, tehát nem lehet reszponzívvá tenni: bármilyen
viewBox-méretet választunk, valamelyik készülékszélességen elromlik a
betűméret. HTML-ben a szöveg mindig a CSS-méretén marad, a sáv hosszát pedig
százalék adja — így 320px-től 430px-ig egyformán olvasható, és nem kell
vízszintesen görgetni.

Kimenet: SVG fájlok a scratchpadbe; onnan kell bemásolni az arak.html-be.
A jelölő-szín a CSS-ben van (--chart-mark), lásd tools/README.md.
"""
import io
import os

NL = chr(10)

# --- széles (desktop) geometria ---
W_WIDE = 760
BAR_H = 14                # <= 24px
R = 4                     # 4px rounded data-end
ROW_H = 42
LABEL_W = 152
VAL_W = 62
AXIS_H = 34

# --- kompakt (mobil) geometria ---
W_COMPACT = 300
C_ROW_H = 56
C_BAR_H = 12
C_AXIS_H = 30
C_PAD = 4


def fmt(n):
    return format(n, ',d').replace(',', '\u00a0')      # 120 000 (nbsp)


def bar_path(x0, x1, y, h, r=R):
    """Mindkét végén lekerekített sáv."""
    r = min(r, max((x1 - x0) / 2.0, 0.1), h / 2.0)
    return ('M%.1f %.1f H%.1f A%.1f %.1f 0 0 1 %.1f %.1f V%.1f '
            'A%.1f %.1f 0 0 1 %.1f %.1f H%.1f A%.1f %.1f 0 0 1 %.1f %.1f V%.1f '
            'A%.1f %.1f 0 0 1 %.1f %.1f Z') % (
        x0 + r, y, x1 - r, r, r, x1, y + r, y + h - r,
        r, r, x1 - r, y + h, x0 + r, r, r, x0, y + h - r, y + r,
        r, r, x0 + r, y)


def row_attrs(name, entry, mid, top, unit, slug):
    top_txt = fmt(top) + ' ' + unit if top else 'Egyedi ár'
    return ('data-name="%s" data-entry="%s" data-mid="%s" data-top="%s" data-slug="%s"'
            % (name, fmt(entry) + ' ' + unit, fmt(mid) + ' ' + unit, top_txt, slug))


def header(w, h, cid, title, rows, unit, extra_class=''):
    o = ['<svg viewBox="0 0 %d %d" class="rng%s" role="img" '
         'aria-labelledby="%s-t %s-d">' % (w, h, extra_class, cid, cid)]
    o.append('  <title id="%s-t">%s</title>' % (cid, title))
    desc = '; '.join('%s: %s %s-tól %s %s-ig'
                     % (r[0], fmt(r[1]), unit, fmt(r[2]), unit) for r in rows)
    o.append('  <desc id="%s-d">%s</desc>' % (cid, desc))
    return o


def chart_wide(rows, axis_max, ticks, unit, cid, title):
    plot_x0 = LABEL_W + VAL_W
    plot_x1 = W_WIDE - 8 - 54
    plot_w = plot_x1 - plot_x0
    h = len(rows) * ROW_H + AXIS_H
    sx = lambda v: plot_x0 + (v / float(axis_max)) * plot_w

    o = header(W_WIDE, h, cid, title, rows, unit)

    o.append('  <g class="rng-grid">')
    for t in ticks:
        x = sx(t)
        o.append('    <line x1="%.1f" y1="0" x2="%.1f" y2="%d"/>' % (x, x, len(rows) * ROW_H))
    o.append('  </g>')

    for i, (name, entry, mid, top, slug) in enumerate(rows):
        y = i * ROW_H
        cy = y + ROW_H / 2.0
        x_e, x_m = sx(entry), sx(mid)
        o.append('  <g class="rng-row" %s tabindex="0">' % row_attrs(name, entry, mid, top, unit, slug))
        o.append('    <rect class="rng-hit" x="0" y="%.1f" width="%d" height="%d"/>' % (y, W_WIDE, ROW_H))
        o.append('    <text class="rng-name" x="4" y="%.1f">%s</text>' % (cy + 3.8, name))
        o.append('    <text class="rng-val" x="%.1f" y="%.1f" text-anchor="end">%s</text>'
                 % (plot_x0 - 8, cy + 3.5, fmt(entry)))
        if top:
            x_t = sx(top)
            o.append('    <line class="rng-ext-solid" x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f"/>'
                     % (x_m, cy, x_t, cy))
            o.append('    <circle class="rng-dot" cx="%.1f" cy="%.1f" r="4.5"/>' % (x_t, cy))
            o.append('    <text class="rng-val2" x="%.1f" y="%.1f">%s</text>'
                     % (x_t + 10, cy + 3.5, fmt(top)))
        else:
            # ELŐBB a címke, UTÁNA a szaggatás: ha a szaggatott vonal a szöveg
            # magasságában fut át, áthúzottnak látszik az érték.
            txt = fmt(mid)
            dash_x0 = x_m + 10 + len(txt) * 5.7 + 7
            o.append('    <text class="rng-val2" x="%.1f" y="%.1f">%s</text>' % (x_m + 10, cy + 3.5, txt))
            o.append('    <line class="rng-ext" x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f"/>'
                     % (dash_x0, cy, min(dash_x0 + 28, plot_x1 + 40), cy))
        o.append('    <path class="rng-bar" d="%s"/>' % bar_path(x_e, x_m, cy - BAR_H / 2.0, BAR_H))
        o.append('  </g>')

    ay = len(rows) * ROW_H
    o.append('  <g class="rng-axis">')
    o.append('    <line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f"/>' % (plot_x0, ay, plot_x1, ay))
    for t in ticks:
        o.append('    <text x="%.1f" y="%.1f" text-anchor="middle">%s</text>'
                 % (sx(t), ay + 16, fmt(t // 1000)))
    o.append('    <text class="rng-unit" x="%.1f" y="%.1f" text-anchor="end">ezer %s</text>'
             % (plot_x1, ay + 30, unit))
    o.append('  </g>')
    o.append('</svg>')
    return '\n'.join(o)


def chart_compact(rows, axis_max, ticks, unit, cid, title):
    """Mobil változat: HTML/CSS sávlista (lásd a modul docstringjét)."""
    o = ['<ul class="rng-list" aria-label="%s">' % title]
    for name, entry, mid, top, slug in rows:
        left = entry / float(axis_max) * 100.0
        width = (mid - entry) / float(axis_max) * 100.0
        end_pct = mid / float(axis_max) * 100.0
        val_txt = '%s → %s' % (fmt(entry), fmt(top) if top else fmt(mid))
        o.append('  <li class="rng-litem" %s>' % row_attrs(name, entry, mid, top, unit, slug))
        o.append('    <div class="rng-litem-head">')
        o.append('      <span class="rng-litem-name">%s</span>' % name)
        o.append('      <span class="rng-litem-val">%s</span>' % val_txt)
        o.append('    </div>')
        o.append('    <div class="rng-track">')
        o.append('      <div class="rng-fill" style="left:%.2f%%; width:%.2f%%"></div>'
                 % (left, max(width, 1.2)))
        if top:
            o.append('      <div class="rng-cap" style="left:%.2f%%"></div>'
                     % (top / float(axis_max) * 100.0))
        else:
            o.append('      <div class="rng-open" style="left:%.2f%%"></div>' % end_pct)
        o.append('    </div>')
        o.append('  </li>')
    o.append('  <li class="rng-scale" aria-hidden="true">')
    o.append('    <span>0</span><span>%s ezer %s</span>' % (fmt(axis_max // 1000), unit))
    o.append('  </li>')
    o.append('</ul>')
    return NL.join(o)


PROJEKT = [
    ('Termékfotózás',           50000, 120000, None,   'termekfotozas'),
    ('Drónfelvétel',            80000, 180000, None,   'dronfelvetel'),
    ('Workflow automatizálás',  90000, 180000, 490000, 'workflow-automatizalas'),
    ('Márkaidentitás',         120000, 350000, None,   'markaidentitas'),
    ('Aftermovie',             150000, 300000, None,   'aftermovie'),
    ('Weboldalkészítés',       150000, 350000, None,   'weboldalkeszites'),
]

HAVI = [
    ('SOULSILVER CRM',           9900,  24900, None, 'crm'),
    ('PPC hirdetéskezelés',     90000, 180000, None, 'ppc-hirdeteskezeles'),
    ('Közösségi média',        120000, 250000, None, 'kozossegi-media'),
]

SPECS = [
    ('projekt', PROJEKT, 500000, [0, 100000, 200000, 300000, 400000, 500000], 'Ft',
     'projektalapu-arak', 'Projektalapú szolgáltatások ártartománya'),
    ('havi', HAVI, 250000, [0, 50000, 100000, 150000, 200000, 250000], 'Ft/hó',
     'havidijas-arak', 'Havidíjas szolgáltatások ártartománya'),
]

DST = os.environ.get('CHART_OUT') or (
    r'C:\Users\SOULSI~1\AppData\Local\Temp\claude'
    r'\C--Users-SOULSILVER-Downloads-SOULSILVER'
    r'\b56e1554-c390-4ea5-acba-e77dc82aace4\scratchpad')

for key, rows, amax, ticks, unit, cid, title in SPECS:
    wide = chart_wide(rows, amax, ticks, unit, cid, title)
    comp = chart_compact(rows, amax, ticks, unit, cid + '-m', title)
    for suffix, ext, body in (('', 'svg', wide), ('-m', 'html', comp)):
        path = os.path.join(DST, 'chart-%s%s.%s' % (key, suffix, ext))
        io.open(path, 'w', encoding='utf-8', newline='').write(body)
        print('chart-%s%s.%s  %d sor' % (key, suffix, ext, body.count(NL) + 1))
