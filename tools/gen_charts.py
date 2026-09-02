# -*- coding: utf-8 -*-
"""Arsav-diagramok SVG generalasa a SOULSILVER arlista-oldalhoz.

Forma: range bar (dumbbell-jellegu savok) - a sav a belepo es a kozepso csomag
ara kozott fut, a nyitott vegu ("Egyedi ar") szint szaggatott folytatas.

Miert KET diagram: a havidijas es a projektalapu szolgaltatas MAS mertekegyseg
(Ft/ho vs Ft). Egy tengelyre tenni oket ugyanaz a hiba, mint a ket y-skala -
ezert small multiples.

Szinek: egyetlen sorozat, egy jelolo-szin (#0A9E77), ami a dataviz validatoron
vilagos ES sotet felszinen is atmegy mind az ot ellenorzesen. A sotet tema sajat
--mint-deep tokene (#12C592) megbukik a vilagossag-savon, ezert nem hasznalhato.
"""
import io

MARK = '#0A9E77'          # validalt mindket modban
BAR_H = 14                # <= 24px
R = 4                     # 4px rounded data-end
ROW_H = 42
LABEL_W = 152             # bal oldali nev-kolumna
VAL_W = 62                # belepo ar helye a sav elott
PAD_L = 4
PAD_R = 8
W = 760
AXIS_H = 34


def fmt(n):
    return format(n, ',d').replace(',', '\u00a0')      # 120 000 (nbsp)


def bar_path(x0, x1, y, h=BAR_H, r=R):
    """Mindkét végén lekerekített sáv."""
    r = min(r, (x1 - x0) / 2.0, h / 2.0)
    return ('M%.1f %.1f H%.1f A%.1f %.1f 0 0 1 %.1f %.1f V%.1f '
            'A%.1f %.1f 0 0 1 %.1f %.1f H%.1f A%.1f %.1f 0 0 1 %.1f %.1f V%.1f '
            'A%.1f %.1f 0 0 1 %.1f %.1f Z') % (
        x0 + r, y, x1 - r, r, r, x1, y + r, y + h - r,
        r, r, x1 - r, y + h, x0 + r, r, r, x0, y + h - r, y + r,
        r, r, x0 + r, y)


def chart(rows, axis_max, ticks, unit, chart_id, title):
    """rows: [(nev, belepo, kozepso, felso|None, slug)]"""
    plot_x0 = LABEL_W + VAL_W
    plot_x1 = W - PAD_R - 54          # jobb oldalt hely a kozepso ar cimkejenek
    plot_w = plot_x1 - plot_x0
    h = len(rows) * ROW_H + AXIS_H

    def sx(v):
        return plot_x0 + (v / float(axis_max)) * plot_w

    o = []
    o.append('<svg viewBox="0 0 %d %d" class="rng" role="img" '
             'aria-labelledby="%s-t %s-d">' % (W, h, chart_id, chart_id))
    o.append('  <title id="%s-t">%s</title>' % (chart_id, title))
    desc = '; '.join('%s: %s %s-tól %s %s-ig'
                     % (r[0], fmt(r[1]), unit, fmt(r[2]), unit) for r in rows)
    o.append('  <desc id="%s-d">%s</desc>' % (chart_id, desc))

    # --- racs: SZILARD hajszalvonal (soha nem szaggatott) ---
    o.append('  <g class="rng-grid">')
    for t in ticks:
        x = sx(t)
        o.append('    <line x1="%.1f" y1="0" x2="%.1f" y2="%d"/>' % (x, x, len(rows) * ROW_H))
    o.append('  </g>')

    # --- sorok ---
    for i, (name, entry, mid, top, slug) in enumerate(rows):
        y = i * ROW_H
        cy = y + ROW_H / 2.0
        by = cy - BAR_H / 2.0
        x_e, x_m = sx(entry), sx(mid)

        top_txt = fmt(top) + ' Ft' if top else 'Egyedi ár'
        o.append('  <g class="rng-row" data-name="%s" data-entry="%s" data-mid="%s" '
                 'data-top="%s" data-unit="%s" data-slug="%s" tabindex="0">'
                 % (name, fmt(entry) + ' ' + unit, fmt(mid) + ' ' + unit, top_txt, unit, slug))
        # nagyobb hit-target, mint a jelolo
        o.append('    <rect class="rng-hit" x="0" y="%.1f" width="%d" height="%d"/>'
                 % (y, W, ROW_H))
        # szolgaltatas neve
        o.append('    <text class="rng-name" x="%d" y="%.1f">%s</text>' % (PAD_L, cy + 3.8, name))
        # belepo ar a sav ELOTT
        o.append('    <text class="rng-val" x="%.1f" y="%.1f" text-anchor="end">%s</text>'
                 % (plot_x0 - 8, cy + 3.5, fmt(entry)))

        # nyitott veg: szaggatott folytatas (a szaggatas itt ADAT - "felfele nyitott"),
        # vagy szilard, vekonyabb folytatas a szamszeru felso szintig
        if top:
            x_t = sx(top)
            o.append('    <line class="rng-ext-solid" x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f"/>'
                     % (x_m, cy, x_t, cy))
            o.append('    <circle class="rng-dot" cx="%.1f" cy="%.1f" r="4.5"/>' % (x_t, cy))
            o.append('    <text class="rng-val2" x="%.1f" y="%.1f">%s</text>'
                     % (x_t + 10, cy + 3.5, fmt(top)))
        else:
            # ELOBB a cimke, UTANA a szaggatas: ha a szaggatott vonal a szoveg
            # magassagaban fut at, athuzottnak latszik az ertek.
            txt = fmt(mid)
            lw = len(txt) * 5.7                     # mono 9.5px kozelites
            dash_x0 = x_m + 10 + lw + 7
            o.append('    <text class="rng-val2" x="%.1f" y="%.1f">%s</text>'
                     % (x_m + 10, cy + 3.5, txt))
            o.append('    <line class="rng-ext" x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f"/>'
                     % (dash_x0, cy, min(dash_x0 + 28, plot_x1 + 40), cy))

        # a sav: belepo -> kozepso
        o.append('    <path class="rng-bar" d="%s"/>' % bar_path(x_e, x_m, by))
        o.append('  </g>')

    # --- x tengely ---
    ay = len(rows) * ROW_H
    o.append('  <g class="rng-axis">')
    o.append('    <line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f"/>'
             % (plot_x0, ay, plot_x1, ay))
    for t in ticks:
        o.append('    <text x="%.1f" y="%.1f" text-anchor="middle">%s</text>'
                 % (sx(t), ay + 16, format(t // 1000, ',d').replace(',', '\u00a0')))
    o.append('    <text class="rng-unit" x="%.1f" y="%.1f" text-anchor="end">ezer %s</text>'
             % (plot_x1, ay + 30, unit))
    o.append('  </g>')
    o.append('</svg>')
    return '\n'.join(o)


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

out = {}
out['projekt'] = chart(PROJEKT, 500000, [0, 100000, 200000, 300000, 400000, 500000],
                       'Ft', 'projektalapu-arak',
                       'Projektalapú szolgáltatások ártartománya')
out['havi'] = chart(HAVI, 250000, [0, 50000, 100000, 150000, 200000, 250000],
                    'Ft/hó', 'havidijas-arak',
                    'Havidíjas szolgáltatások ártartománya')

dst = (r'C:\Users\SOULSI~1\AppData\Local\Temp\claude'
       r'\C--Users-SOULSILVER-Downloads-SOULSILVER'
       r'\b56e1554-c390-4ea5-acba-e77dc82aace4\scratchpad')
for k, v in out.items():
    io.open(dst + '\\chart-%s.svg' % k, 'w', encoding='utf-8', newline='').write(v)
    print('chart-%s.svg  %d sor' % (k, v.count('\n') + 1))
