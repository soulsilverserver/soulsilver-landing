# tools/

Generátorok. Nem futnak deploykor — kézzel kell futtatni, ha az árak változnak.

| Fájl | Mit csinál |
|---|---|
| `gen_charts.py` | Az `arak.html` két ártartomány-diagramjának SVG-je |
| `gen_roi.py` | A workflow-oldal ROI-görbéjének alapállapota (3 fő, 1,5 óra) |
| `gen_arlista_pdf.py` | A letölthető árlista PDF (Downloads mappába ír) |

## Ha árat változtatsz

Az árak **négy helyen** vannak, és nincs köztük automatikus kapcsolat:

1. a szolgáltatás-oldal `.price-grid`-je (a forrás)
2. `tools/gen_charts.py` → `PROJEKT` / `HAVI` lista
3. `arak.html` táblázatos nézete (a `build_arak.py` `ROWS` listájából, lásd lent)
4. `tools/gen_arlista_pdf.py` → `SERVICES`

Mind a négyet frissítsd, majd futtasd újra a generátorokat.

## Futtatás

Ehhez `reportlab` kell (a PDF-hez). A rendszer PATH-on lévő `python` az
Inkscape-é és nincs benne pip — használj rendes Pythont:

```bash
python3 tools/gen_charts.py
python3 tools/gen_roi.py
python3 tools/gen_arlista_pdf.py
```

A `gen_charts.py` és a `gen_roi.py` SVG-t ír a scratchpadbe; azt kell bemásolni
a megfelelő HTML-be (`arak.html`, illetve `workflow-automatizalas.html`).

## Diagram-színek — ne írd át

A jelölő-szín `#0A9E77`, és NEM a téma `--mint-deep` tokene. A sötét téma
`#12C592`-je megbukik a dataviz-validátor világosság-sávján (OKLCH L 0.73 a
0.48–0.67 sáv helyett). A `#0A9E77` mindkét kártya-felszínen (`#E4E7EA` és
`#101317`) átmegy mind az öt ellenőrzésen.
