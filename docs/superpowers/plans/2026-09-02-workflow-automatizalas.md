# Workflow-automatizálás oldal — implementációs terv

Spec: [2026-09-02-workflow-automatizalas-design.md](../specs/2026-09-02-workflow-automatizalas-design.md)

**Verifikáció teszt helyett:** nincs teszkeret a repóban. Minden taszk végén
böngészős ellenőrzés: 0 konzolhiba, 0px vízszintes túlcsordulás, és a taszkra
jellemző interakció.

## Global Constraints

- Magyar szöveg, `lang="hu"`; a meglévő tokenek (`--mint`, `--edge`, `--surface`,
  `--sp-*`, `--font-mono`) — új szín NEM kerül be
- Töréspontok: 1180px, 860px, 760px, 640px, 560px (a meglévőkkel egyezően)
- `prefers-reduced-motion: reduce` esetén minden mozgás kikapcsol
- Az `app.js` egyetlen IIFE; minden új modul `if(!el) return;` guarddal
- A számpélda-szekcióból a két jelölés („SZÁMPÉLDA — modellezett forgatókönyv"
  és a lábjegyzet) NEM távolítható el
- A full-bleed elemek `width:100%`-ot használnak, NEM `100vw`-t (görgetősáv)
- Hosszú magyar szavak miatt minden új szöveges konténer örökli az
  `overflow-wrap` beállítást; új rácsnál `min-width:0` a gyerekeken

---

### Task 1: CSS blokk

**Files:** Modify `styles.css` (új blokk a fájl végére)

**Produces:** `.wf-cat`, `.wf-cat-head`, `.wf-list`, `.wf-item`, `.wf-trigger`,
`.wf-panel`, `.wf-steps`, `.wf-step`, `.wf-result`, `.wf-anatomy`,
`.calc`, `.calc-inputs`, `.calc-field`, `.calc-out`, `.calc-big`, `.calc-note`,
`.model-case`, `.model-badge`

- [ ] Katalógus: kategória-fejléc + akkordeon-elemek (`.wf-panel` rejtve,
      `[aria-expanded="true"]` mellett nyitva; `max-height` átmenet helyett
      `display` váltás + opacity, hogy tartalomfüggetlen legyen)
- [ ] Lépéslista: számozott `.wf-step` elemek bal oldali mint vonallal
- [ ] Kalkulátor: 3 mező `range` inputtal, jobb oldalt kiemelt kimenet-panel;
      860px alatt egy kolumna
- [ ] Számpélda: `.model-case` sötét kártya, `.model-badge` mint pill címke
- [ ] Anatómia-ábra kerete: `overflow-x:auto`, hogy szűk kijelzőn görgethető
- [ ] Reduced-motion blokk
- [ ] Commit

### Task 2: Az oldal

**Files:** Create `workflow-automatizalas.html`

- [ ] Head/nav/mobil panel/footer/cookie bar a `ppc-hirdeteskezeles.html`-ből,
      title/canonical/OG/description átírva
- [ ] `FAQPage` JSON-LD az 5 GYIK-kérdéssel
- [ ] Hero: `.phero` gradiens-fallbackes háttérrel, 3 `.phero-stat`
- [ ] „Hol szivárog el az idő?" — 6 `.incl-card` ikonokkal
- [ ] Workflow-katalógus: 4 `.wf-cat`, összesen 10 `.wf-item`;
      minden `.wf-trigger` `<button aria-expanded="false" aria-controls>`
- [ ] Anatómia: inline SVG (trigger → feltétel → 2 párhuzamos akció →
      értesítés → eszkalálás), `role="img"` + `<title>`/`<desc>`
- [ ] Kalkulátor szekció a 3 mezővel, kimenet-panellel, kiírt formulával
- [ ] Számpélda `.model-case` a két kötelező jelöléssel
- [ ] „Mit NEM automatizálunk" — 4 kártya
- [ ] Folyamat: 5 `.stepbox`
- [ ] Árak: 3 `.price-card` (a középső `featured`)
- [ ] GYIK: 5 `.faq-item` (egyezik a JSON-LD-vel)
- [ ] CTA
- [ ] Commit

### Task 3: JS modulok

**Files:** Modify `app.js` (új modulok a gtag blokk elé)

- [ ] `wf-akkordeon`: `.wf-trigger` klikk → `aria-expanded` váltás, a
      hozzátartozó `.wf-panel` `hidden` attribútumának váltása. Egyszerre
      több is nyitva lehet. Billentyűzetről a `<button>` natívan működik.
- [ ] `wf-kalkulator`: `input` esemény a három `range`-en → újraszámolás.
      `megtakitott_ora_ho = fo * napi_ora * 21 * 0.7`; `ft_ho = ora_ho * orabér`;
      `fte = ora_ho / 168`. Kimenet `Intl.NumberFormat('hu-HU')`-val,
      a Ft-értékek egész forintra kerekítve.
      Induláskor egyszer lefut, hogy ne 0 legyen a kimenet.
- [ ] Commit

### Task 4: Bekötés

**Files:** Modify `index.html` (work-grid + footer), a többi 13 oldal footere,
`sitemap.xml`

- [ ] `index.html` `.work-grid`: új `.work-tile` a workflow-oldalra
- [ ] Footer „Szolgáltatások" lista: új sor MINDEN oldalon
- [ ] `sitemap.xml` bejegyzés
- [ ] Commit

### Task 5: Verifikáció

- [ ] 0 konzolhiba az új oldalon
- [ ] 0px túlcsordulás 375 / 768 / 1280px-en
- [ ] A kalkulátor kimenete kézzel visszaszámolva egyezik
- [ ] Akkordeon nyit/zár, `aria-expanded` helyes
- [ ] Az `app.js` végig lefut minden oldalon (`.wa-float` létrejön)
- [ ] Az egész site túlcsordulás-mérése nem regresszált
- [ ] Képernyőkép a felhasználónak
