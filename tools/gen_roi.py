# -*- coding: utf-8 -*-
"""ROI-görbe SVG a workflow-oldal kalkulátorához — széles és mobil változatban.

Egy sorozat (nincs legend-box), 2px vonal, ~10% area wash a jelenlegi
létszámig, szilárd hajszálvonal rács, >=8px jelölő 2px felszín-gyűrűvel.

A vonal a kalkulátor formulája kirajzolva:
  felszabaduló óra/hó = létszám × napi óra × 21 × 0,7

MIÉRT KÉT VÁLTOZAT: a széles viewBox 520 egység. Egy 375px-es telefonon a
görgethető keret ~246px, ott a 9px-es tengelyfelirat ~4px-en jelenne meg. A
mobil változat 340 egység széles, nagyobb betűkkel, rövidebb tengelyfelirattal.

A geometria data-* attribútumokban is ott van, mert az app.js MINDKÉT SVG-t
ebből rajzolja újra a csúszkákra — így nem kell a JS-ben duplikálni a számokat.
"""
import io
import os

NMIN, NMAX = 1, 20
MUNKANAP = 21
AUTO = 0.7

# (nev, viewBox szelesseg, magassag, px0, px1, py0, py1, x-tengely cimkek, tengelyfelirat)
VARIANTS = [
    ('', 520, 236, 46, 502, 16, 190, (1, 5, 10, 15, 20),
     'ismétlődő adminisztrációt végző fő'),
    ('-m', 240, 172, 40, 234, 10, 130, (1, 5, 10, 15, 20),
     'adminisztrációt végző fő'),
]


def nice_max(v):
    if v <= 0:
        return 10
    import math
    step = 10 ** math.floor(math.log10(v))
    for m in (1, 2, 2.5, 5, 10):
        if v <= step * m:
            return int(step * m)
    return int(step * 10)


def fmt(n):
    return format(int(round(n)), ',d').replace(',', '\u00a0')


def build(people, hours, suffix, vw, vh, px0, px1, py0, py1, xticks, xlabel):
    ymax = nice_max(NMAX * hours * MUNKANAP * AUTO)
    sx = lambda n: px0 + (n - NMIN) / float(NMAX - NMIN) * (px1 - px0)
    sy = lambda v: py1 - (v / float(ymax)) * (py1 - py0)
    val = lambda n: n * hours * MUNKANAP * AUTO
    cid = 'roi' + suffix
    cls = 'roi' + (' roi-compact' if suffix else '')

    o = ['<svg viewBox="0 0 %d %d" class="%s" data-roi="1" '
         'data-px0="%d" data-px1="%d" data-py0="%d" data-py1="%d" '
         'role="img" aria-labelledby="%s-t %s-d">' % (vw, vh, cls, px0, px1, py0, py1, cid, cid)]
    o.append('  <title id="%s-t">Felszabaduló kapacitás a létszám függvényében</title>' % cid)
    o.append('  <desc id="%s-d">Minél többen végzik az ismétlődő adminisztrációt, '
             'annál több óra szabadul fel havonta. A pontos értékeket a kalkulátor '
             'mutatja a diagram mellett.</desc>' % cid)

    o.append('  <g class="roi-grid">')
    for i in range(5):
        y = sy(ymax * i / 4.0)
        o.append('    <line x1="%d" y1="%.1f" x2="%d" y2="%.1f"/>' % (px0, y, px1, y))
    o.append('  </g>')
    o.append('  <g class="roi-ylab">')
    for i in range(5):
        v = ymax * i / 4.0
        o.append('    <text x="%d" y="%.1f" text-anchor="end">%s</text>'
                 % (px0 - 7, sy(v) + 3.2, fmt(v)))
    o.append('  </g>')

    pts = [(sx(n), sy(val(n))) for n in range(NMIN, people + 1)]
    d = ''
    if len(pts) >= 2:
        d = ('M%.1f %.1f ' % (pts[0][0], py1)
             + ' '.join('L%.1f %.1f' % p for p in pts)
             + ' L%.1f %.1f Z' % (pts[-1][0], py1))
    o.append('  <path class="roi-area" d="%s"/>' % d)

    line = [(sx(n), sy(val(n))) for n in range(NMIN, NMAX + 1)]
    o.append('  <path class="roi-line" d="M%s"/>' % ' L'.join('%.1f %.1f' % p for p in line))

    mx, my = sx(people), sy(val(people))
    o.append('  <circle class="roi-dot" cx="%.1f" cy="%.1f" r="5"/>' % (mx, my))
    o.append('  <text class="roi-lab" x="%.1f" y="%.1f">%s óra / hó</text>'
             % (mx + 10, my - 8, fmt(val(people))))

    o.append('  <g class="roi-axis">')
    o.append('    <line x1="%d" y1="%d" x2="%d" y2="%d"/>' % (px0, py1, px1, py1))
    for n in xticks:
        o.append('    <text x="%.1f" y="%d" text-anchor="middle">%d</text>' % (sx(n), py1 + 16, n))
    o.append('    <text class="roi-unit" x="%d" y="%d" text-anchor="middle">%s</text>'
             % ((px0 + px1) // 2, py1 + 33, xlabel))
    o.append('  </g>')
    o.append('  <text class="roi-unit roi-yunit" x="2" y="%d">óra / hó</text>' % (py0 - 3))
    o.append('</svg>')
    return '\n'.join(o)


# A kimenet a repon beluli tools/_out/ (gitignore-olt), hogy sessiontol
# fuggetlenul mukodjon. CHART_OUT env valtozoval felulirhato.
DST = os.environ.get('CHART_OUT') or os.path.join(
    os.path.dirname(os.path.abspath(__file__)), '_out')
os.makedirs(DST, exist_ok=True)

for suffix, vw, vh, px0, px1, py0, py1, xt, xl in VARIANTS:
    svg = build(3, 1.5, suffix, vw, vh, px0, px1, py0, py1, xt, xl)   # a kalkulator alapertekei
    path = os.path.join(DST, 'chart-roi%s.svg' % suffix)
    io.open(path, 'w', encoding='utf-8', newline='').write(svg)
    print('chart-roi%s.svg  %d sor' % (suffix, svg.count('\n') + 1))

print('ellenorzes: 3 fo * 1,5 ora * 21 * 0,7 =', 3 * 1.5 * MUNKANAP * AUTO)
