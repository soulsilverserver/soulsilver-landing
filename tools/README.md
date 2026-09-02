# tools/

Generátorok és ellenőrző szkriptek. **Nem futnak deploykor** — kézzel kell
futtatni.

## Python

A PATH-on lévő `python` az Inkscape-é, és **nincs benne pip**. Használd ezt:

```bash
/c/Users/SOULSILVER/scoop/shims/python3.exe
```

Ebben megvan a `reportlab` (PDF), a `pdfplumber` és a `pypdfium2` (PDF
ellenőrzés).

## Ellenőrzők

| Fájl | Mit mér |
|---|---|
| `audit_static.py` | törött belső link, halott horgony, duplikált `id`, `alt` nélküli kép, tag-eltérés, `canonical`, nav-konzisztencia, sitemap |
| `audit_prices.py` | egyeznek-e az árak mind a négy helyen |
| `audit_browser.js` | túlcsordulás, levágott szöveg, tap-target, apró betű, címsor-hierarchia — böngészőben, iframe-ekben |

Az `audit_browser.js` használata a fájl fejlécében van. A kimenete csak a
**problémákat** listázza; ha „NINCS", akkor tiszta. A vakriasztásokat
(honeypot, `.visually-hidden` címkék, desktop nav-linkek) szándékosan kiszűri —
ha új kivételt adsz hozzá, írd oda az okát is.

## Generátorok

| Fájl | Mit csinál |
|---|---|
| `gen_charts.py` | `arak.html`: 2 SVG (desktop) + 2 HTML sávlista (mobil) |
| `gen_roi.py` | a workflow-oldal ROI-görbéje: széles + mobil változat |
| `gen_arlista_pdf.py` | a letölthető árlista PDF (a Downloads mappába ír, **nem** a repóba) |

A kimenet a `tools/_out/` mappába megy (gitignore-olt). Onnan kell **bemásolni**
a megfelelő HTML-be — nincs automatikus beillesztés.

## Ha árat változtatsz

Az árak **négy helyen** vannak, és nincs köztük automatikus kapcsolat:

1. a szolgáltatás-oldal `.price-grid`-je — **ez a forrás**
2. `gen_charts.py` → `PROJEKT` / `HAVI` lista
3. `arak.html` táblázatos nézete
4. `gen_arlista_pdf.py` → `SERVICES` lista

Mind a négyet frissítsd, majd futtasd az `audit_prices.py`-t — az kimutatja az
eltérést.

## Diagram-színek — ne írd át

A jelölő-szín `--chart-mark: #0A9E77`, és szándékosan ugyanez világos és sötét
témában. A sötét téma `--mint-deep`-je (`#12C592`) **megbukik** a dataviz
validátor világosság-sávján (OKLCH L 0,73 a 0,48–0,67 helyett). A `#0A9E77`
mindkét kártya-felszínen (`#E4E7EA` és `#101317`) átmegy mind az öt
ellenőrzésen. Ne „javítsd vissza" a téma tokenjére.

## Miért HTML a mobil ártartomány-diagram

Az SVG-szöveg a konténerrel együtt skálázódik, tehát nem lehet reszponzívvá
tenni: bármelyik viewBox-méretnél valamelyik készülékszélességen elromlik a
betűméret (375px-en a 9,5px-es címke ~7,5px-en jelent meg). HTML-ben a szöveg
mindig a CSS-méretén marad, a sáv hosszát pedig százalék adja. A ROI-görbe
maradt SVG, de 240 egységes viewBox-szal, hogy a nagyítás minden telefonon
≥0,93 legyen.
