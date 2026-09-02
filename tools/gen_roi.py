# -*- coding: utf-8 -*-
"""ROI-gorbe SVG a workflow-oldal kalkulatorahoz.

Egy sorozat (nincs legend-box), 2px vonal, ~10% area wash a jelenlegi letszamig,
szilard hajszalvonal racs, >=8px jelolo 2px felszin-gyuruvel.

A vonal a kalkulator formulaja kirajzolva:
  felszabadulo ora/ho = letszam * napi_ora * 21 * 0.7
Linearis, ezert egyenes - a diagram azt mutatja meg, hol all a nezo a savon,
es mennyi a fejlodesi ter felette.
"""
import io

VB_W, VB_H = 520, 236
PX0, PX1 = 46, 502          # plot vizszintesen
PY0, PY1 = 16, 190          # plot fuggolegesen (PY1 = a nulla vonal)
NMIN, NMAX = 1, 20
MUNKANAP = 21
AUTO = 0.7


def nice_max(v):
    """Kerek felso hatar a y tengelyhez."""
    if v <= 0:
        return 10
    import math
    step = 10 ** math.floor(math.log10(v))
    for m in (1, 2, 2.5, 5, 10):
        if v <= step * m:
            return int(step * m)
    return int(step * 10)


def build(people, hours):
    ymax = nice_max(NMAX * hours * MUNKANAP * AUTO)

    def sx(n):
        return PX0 + (n - NMIN) / float(NMAX - NMIN) * (PX1 - PX0)

    def sy(v):
        return PY1 - (v / float(ymax)) * (PY1 - PY0)

    def val(n):
        return n * hours * MUNKANAP * AUTO

    o = []
    o.append('<svg viewBox="0 0 %d %d" class="roi" id="roiChart" role="img" '
             'aria-labelledby="roi-t roi-d">' % (VB_W, VB_H))
    o.append('  <title id="roi-t">Felszabaduló kapacitás a létszám függvényében</title>')
    o.append('  <desc id="roi-d">Minél többen végzik az ismétlődő adminisztrációt, '
             'annál több óra szabadul fel havonta. A pontos értékeket a kalkulátor '
             'mutatja a diagram mellett.</desc>')

    # vizszintes racs + y cimkek (SZILARD hajszalvonal)
    o.append('  <g class="roi-grid" id="roiGrid">')
    for i in range(5):
        v = ymax * i / 4.0
        y = sy(v)
        o.append('    <line x1="%d" y1="%.1f" x2="%d" y2="%.1f"/>' % (PX0, y, PX1, y))
    o.append('  </g>')
    o.append('  <g class="roi-ylab" id="roiYlab">')
    for i in range(5):
        v = ymax * i / 4.0
        y = sy(v)
        o.append('    <text x="%d" y="%.1f" text-anchor="end">%s</text>'
                 % (PX0 - 8, y + 3.2, format(int(round(v)), ',d').replace(',', '\u00a0')))
    o.append('  </g>')

    # area wash a jelenlegi letszamig (~10%)
    pts = [(sx(n), sy(val(n))) for n in range(NMIN, people + 1)]
    if len(pts) >= 2:
        d = 'M%.1f %.1f ' % (pts[0][0], PY1)
        d += ' '.join('L%.1f %.1f' % p for p in pts)
        d += ' L%.1f %.1f Z' % (pts[-1][0], PY1)
    else:
        d = ''
    o.append('  <path class="roi-area" id="roiArea" d="%s"/>' % d)

    # a vonal a teljes savon
    line = [(sx(n), sy(val(n))) for n in range(NMIN, NMAX + 1)]
    o.append('  <path class="roi-line" id="roiLine" d="M%s"/>'
             % ' L'.join('%.1f %.1f' % p for p in line))

    # elo jelolo: >=8px atmero (r=5) + 2px felszin-gyuru
    mx, my = sx(people), sy(val(people))
    o.append('  <circle class="roi-dot" id="roiDot" cx="%.1f" cy="%.1f" r="5"/>' % (mx, my))
    # kozvetlen cimke a jelolonel (nem minden ponton!)
    o.append('  <text class="roi-lab" id="roiLab" x="%.1f" y="%.1f">%s óra / hó</text>'
             % (mx + 10, my - 8, format(int(round(val(people))), ',d').replace(',', '\u00a0')))

    # x tengely
    o.append('  <g class="roi-axis">')
    o.append('    <line x1="%d" y1="%d" x2="%d" y2="%d"/>' % (PX0, PY1, PX1, PY1))
    for n in (1, 5, 10, 15, 20):
        o.append('    <text x="%.1f" y="%d" text-anchor="middle">%d</text>'
                 % (sx(n), PY1 + 16, n))
    o.append('    <text class="roi-unit" x="%d" y="%d" text-anchor="middle">'
             'ismétlődő adminisztrációt végző fő</text>'
             % ((PX0 + PX1) // 2, PY1 + 34))
    o.append('  </g>')
    o.append('  <text class="roi-unit roi-yunit" x="%d" y="%d">óra / hó</text>' % (4, PY0 - 4))
    o.append('</svg>')
    return '\n'.join(o)


dst = (r'C:\Users\SOULSI~1\AppData\Local\Temp\claude'
       r'\C--Users-SOULSILVER-Downloads-SOULSILVER'
       r'\b56e1554-c390-4ea5-acba-e77dc82aace4\scratchpad\chart-roi.svg')
svg = build(3, 1.5)      # a kalkulator alapertekei
io.open(dst, 'w', encoding='utf-8', newline='').write(svg)
print('chart-roi.svg', svg.count('\n') + 1, 'sor')
print('ellenorzes: 3 fo * 1,5 ora * 21 * 0,7 =', 3 * 1.5 * 21 * 0.7)
