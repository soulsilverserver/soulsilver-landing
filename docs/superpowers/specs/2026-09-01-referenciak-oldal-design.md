# Referenciák oldal — design

Dátum: 2026-09-01
Státusz: jóváhagyva (user, 2026-09-01)

## Cél

Önálló, látványos portfólió/referencia oldal a soulsilver.hu-hoz (`referenciak.html`),
amely képet, videót és ügyféllogót tud megjeleníteni. A hangsúly a vizuális
hatáson van: parallax videó-sávok és mozaik (bento) képrács.

Jelenleg NINCS önálló referenciák oldal — csak az `index.html#munkaink`
szolgáltatás-csempéi és aloldalankénti 3-3 `ref-card` blokk.

## Nem cél (YAGNI)

- Projektenkénti esettanulmány-aloldal (később, ha kell)
- CMS / admin felület — a bővítés HTML-kártya másolással történik
- Kliensoldali keresés, lapozás, végtelen görgetés

## Oldalváz

`referenciak.html`, a `dronfelvetel.html` fej/lábléc szerkezetével:

1. Hero — sötét, „liquid glass", count-up statisztikák
2. Ügyféllogó-marquee — végtelen sáv, grayscale → hoveren színes
3. Szűrő chipek — Mind · Drón · Aftermovie · Közösségi média · Weboldal ·
   Márka · Termékfotó · PPC
4. Bento rács — referencia-kártyák
5. 2 db full-bleed videó-sáv, a rács közé ékelve
6. CTA — `index.html#kapcsolat`

## Komponensek

### Videó-sáv (`.vband`)

- `overflow:hidden` keret; a videó 115%-ra túlméretezve, görgetéskor lassabban
  mozog (`transform: translateY`), a fölötte lévő cím + logó ellenirányban úszik
- A meglévő `[data-parallax]` motort használja (app.js) — nincs új rendszer
- `<video muted loop playsinline preload="metadata" poster>`; IntersectionObserver
  indítja/állítja meg, hogy kigörgetve ne fogyasszon
- `prefers-reduced-motion`: nincs mozgás, marad a poster

### Bento rács (`.bento`)

- Vegyes méretű kártyák: alap / `wide` (2 oszlop) / `tall` (2 sor)
- Görgetés-reveal: `clip-path` felfelé nyílik + a kép 1.15 → 1.0 zoom
- Hover: enyhe 3D-tilt egérirány szerint, `--mint` fényszalag söpör végig,
  cím alulról felúszik
- Kattintás: lightbox

### Lightbox (`.lb`)

- Nagy kép VAGY beágyazott videó, cím, leírás, ügyféllogó, szolgáltatás-tag
- Zárás: Esc, háttérkattintás, X gomb; navigáció: ← → nyilak
- Fókuszcsapda + `aria-modal`, a háttér `overflow:hidden`

### Szűrés

- Kártyán `data-cat="dron aftermovie"` (szóközzel elválasztott címkék)
- Chipre kattintva a nem illők kifakulnak és összecsuknak (CSS transition)
- URL-hash támogatás: `referenciak.html#dron` betöltéskor is szűr

## Adat és média

- Statikus HTML kártyák (SEO + illeszkedik a repo stílusához)
- Média: `img/ref/<slug>.jpg` és `img/ref/<slug>.mp4`, logók `img/logos/ugyfel/`
- **Hiánytűrés**: ha a képfájl nincs meg, az `<img>` `onerror` eltávolítja magát,
  és marad alatta a gradient-thumb → nincs törött kép élesben
- Új referencia = egy `<article class="bento-item">` blokk másolása

## Bekötés

- Nav-menüpont mind a 10 HTML oldalon (desktop nav + mobil panel)
- `sitemap.xml` bejegyzés, footer link
- `styles.css`: új blokk (`.bento-*`, `.vband-*`, `.lb-*`, `.logo-marquee`, `.chip-*`)
- `app.js`: új, null-guardolt modulok (szűrő, lightbox, videó-observer, count-up),
  hogy a többi oldalon ne dobjon hibát

## Tartalmi figyelmeztetés

A meglévő aloldalak `ref-card`-jai kitalált referenciák. Az új oldal is DEMÓ
tartalommal indul (helykitöltő). Élesítés előtt valós projektekre kell cserélni,
különben félrevezető. A demó kártyák HTML-kommenttel meg vannak jelölve.

## Kész-kritériumok

- `referenciak.html` betölt, konzolhiba nélkül, kép/videó fájlok nélkül is
- Szűrés, lightbox, billentyűzet-navigáció működik
- A többi oldal továbbra is hibátlanul fut a bővített `app.js`-szel
- Reszponzív: 3 / 2 / 1 oszlop a meglévő töréspontokon (860px, 560px)
- `prefers-reduced-motion` esetén nincs mozgás
