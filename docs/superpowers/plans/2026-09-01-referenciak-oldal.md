# Referenciák oldal — implementációs terv

**Goal:** Önálló, látványos `referenciak.html` portfólió oldal parallax videó-sávokkal,
bento képráccsal, lightboxszal és kategória-szűrővel.

**Architecture:** Statikus HTML oldal a meglévő `styles.css` + `app.js` párosra épülve.
Nincs build lépés, nincs framework. Az új viselkedés az `app.js` IIFE-jébe kerül,
null-guardolva, hogy a többi 10 oldalon ne dobjon hibát. A média hiánytűrő:
hiányzó fájl esetén a kártya gradient-thumbra esik vissza.

**Tech Stack:** HTML5, CSS (custom properties, grid, clip-path), vanilla JS (ES5-stílus,
IntersectionObserver), Hostinger git auto-deploy.

**Verifikáció teszt helyett:** nincs teszkeret a repóban. Minden taszk végén a
Browser pane-ben betöltjük az oldalt, `read_console_messages` (0 hiba), és a
taszkra jellemző interakció ellenőrzése.

## Global Constraints

- Nyelv: magyar szövegek, `lang="hu"`
- Design tokenek a `styles.css`-ből: `--mint`, `--edge`, `--surface`, `--sp-*`,
  `--font-mono` — új színt NE vezessünk be
- Töréspontok: 860px és 560px (a meglévőkkel egyezően)
- `prefers-reduced-motion: reduce` esetén minden mozgás kikapcsol
- Az `app.js` egyetlen IIFE — minden új modul `if(!el) return;` guarddal
- A repo PUBLIKUS: titkos adat nem kerülhet bele
- Minden demó tartalom `<!-- DEMÓ -->` kommenttel jelölve

---

### Task 1: CSS alapok

**Files:**
- Modify: `styles.css` (új blokk a fájl végére, `/* ===== REFERENCIÁK ===== */` fejléccel)

**Interfaces — Produces (ezekre épül a 2. és 3. taszk):**
- `.ref-hero`, `.ref-stats`, `.ref-stat b[data-count]`
- `.logo-marquee`, `.logo-track`, `.logo-item`
- `.chip-row`, `.chip` (+ `.chip.active`)
- `.bento`, `.bento-item` (+ `.wide`, `.tall`), `.bento-media`, `.bento-img`,
  `.bento-overlay`, `.bento-tag`, `.bento-title`, `.bento-sheen`
- `.bento-item.is-hidden` (szűrés: opacity 0 + scale .96 + grid-hely elvétele)
- `.vband`, `.vband-frame`, `.vband-video`, `.vband-content`
- `.lb`, `.lb.open`, `.lb-stage`, `.lb-media`, `.lb-info`, `.lb-close`, `.lb-nav`

- [ ] **Step 1:** A `styles.css` végére új blokk a fenti osztályokkal, a meglévő
  `.ref-card` / `.work-tile` szabályok mintájára (border-radius 20px, `var(--edge)`
  keret, `var(--surface)` háttér).
- [ ] **Step 2:** Bento rács: `grid-template-columns:repeat(4,1fr)`, `grid-auto-rows:220px`;
  `.wide{grid-column:span 2}`, `.tall{grid-row:span 2}`. 860px alatt 2 oszlop és a
  `.tall` span megszűnik, 560px alatt 1 oszlop, minden span megszűnik.
- [ ] **Step 3:** Reveal-maszk: `.bento-item{clip-path:inset(0 0 100% 0)}` →
  `.bento-item.is-visible{clip-path:inset(0 0 0 0)}`, a `.bento-img` 1.15 → 1 scale.
- [ ] **Step 4:** `@media (prefers-reduced-motion: reduce)` blokk: minden
  transition/transform/animation kikapcsolva, a clip-path azonnal nyitott.
- [ ] **Step 5:** Verifikáció: `referenciak.html` még nincs, ezért csak azt ellenőrizzük,
  hogy a meglévő `index.html` változatlanul renderel (nincs elgépelt szabály, ami
  globális szelektort érintene).
- [ ] **Step 6:** Commit — `Add CSS for the referenciak portfolio page`

---

### Task 2: Az oldal váza és demó tartalma

**Files:**
- Create: `referenciak.html`
- Create: `img/ref/.gitkeep`

**Interfaces — Consumes:** Task 1 összes osztálya.
**Interfaces — Produces:** a `data-cat`, `data-lb-title`, `data-lb-desc`,
`data-lb-src`, `data-lb-type` attribútumok, amelyeket a Task 3 lightboxa olvas.

- [ ] **Step 1:** Fej- és lábléc átemelése a `dronfelvetel.html`-ből (ugyanaz a
  `<head>` meta/GA/gtag blokk, nav, mobil panel, footer, cookie bar), a title és
  a canonical `referenciak.html`-re cserélve, OG-tagek frissítve.
- [ ] **Step 2:** Hero szekció `.ref-hero` + 3 `.ref-stat` (`data-count` értékekkel).
- [ ] **Step 3:** Logó-marquee: `.logo-track` a logókkal KÉTSZER egymás után
  (a végtelen csúszáshoz), `aria-hidden="true"` a második példányon.
- [ ] **Step 4:** Chip-sor 8 kategóriával; az első („Mind", `data-filter="*"`) aktív.
- [ ] **Step 5:** 10 db `.bento-item` demó kártya, vegyes `wide`/`tall` méretekkel,
  mindegyiken `data-cat`, gradient háttér + `<img onerror>` fallback.
- [ ] **Step 6:** 2 db `.vband` videó-sáv a rács közé ékelve, `data-parallax` attribútummal.
- [ ] **Step 7:** Lightbox váz (`.lb`, `hidden`, `role="dialog"`, `aria-modal="true"`) + CTA szekció.
- [ ] **Step 8:** Verifikáció: oldal betölt, konzolhiba nélkül; 3/2/1 oszlop a
  töréspontokon (`resize_window`).
- [ ] **Step 9:** Commit — `Add referenciak.html portfolio page with demo content`

---

### Task 3: Interakció (app.js)

**Files:**
- Modify: `app.js` (új modulok az IIFE végére, a gtag blokk elé)

**Interfaces — Consumes:** Task 2 data-attribútumai.

- [ ] **Step 1:** `count-up` modul — `[data-count]` elemek, IntersectionObserver-rel
  induló számláló, `reduceMotion` esetén azonnal a végérték.
- [ ] **Step 2:** Szűrő modul — `.chip` kattintás → `.bento-item.is-hidden` toggle
  a `data-cat` alapján; `location.hash` olvasása betöltéskor; a hash frissítése kattintáskor.
- [ ] **Step 3:** Lightbox modul — megnyitás kártyakattintásra, tartalom a
  `data-lb-*` attribútumokból; zárás Esc/háttér/X; `←`/`→` navigáció a LÁTHATÓ
  (nem szűrt) kártyák között; `document.body.style.overflow='hidden'` nyitva;
  fókusz a bezáró gombra, visszaadás a kiinduló kártyára.
- [ ] **Step 4:** Videó-observer — `.vband-video` play/pause láthatóság szerint.
- [ ] **Step 5:** Hover-tilt — `.bento-item` `mousemove` → CSS változó
  (`--tx`, `--ty`); csak `(hover:hover)` eszközön és `!reduceMotion` esetén.
- [ ] **Step 6:** Verifikáció: chipek szűrnek, lightbox nyílik/zár/lapoz,
  Esc működik, `index.html` és `dronfelvetel.html` továbbra is konzolhiba nélkül tölt.
- [ ] **Step 7:** Commit — `Add filter, lightbox and parallax behaviour for referenciak`

---

### Task 4: Bekötés a többi oldalba

**Files:**
- Modify: `index.html`, `crm.html`, `dronfelvetel.html`, `weboldalkeszites.html`,
  `kozossegi-media.html`, `aftermovie.html`, `markaidentitas.html`,
  `termekfotozas.html`, `ppc-hirdeteskezeles.html`, `koszonjuk.html`
- Modify: `sitemap.xml`

- [ ] **Step 1:** „Referenciák" link a desktop navba mindegyik oldalon
  (az indexen `#munkaink` elé/helyére, aloldalakon a meglévő nav mintája szerint).
- [ ] **Step 2:** Ugyanez a mobil panelbe.
- [ ] **Step 3:** Footer link mindenhol.
- [ ] **Step 4:** `sitemap.xml` bejegyzés `https://soulsilver.hu/referenciak.html`.
- [ ] **Step 5:** Verifikáció: minden oldal navjából elérhető az új oldal;
  nincs 404-es belső link.
- [ ] **Step 6:** Commit — `Link the referenciak page from every page and the sitemap`

---

### Task 5: Végső ellenőrzés

- [ ] **Step 1:** Böngészőben végigkattintani: szűrés → lightbox → nyilak → Esc.
- [ ] **Step 2:** `resize_window` mobile/tablet/desktop + dark/light színséma.
- [ ] **Step 3:** `read_console_messages` mind a 11 oldalon: 0 hiba.
- [ ] **Step 4:** Képernyőkép a felhasználónak.
- [ ] **Step 5:** A hiányzó valós tartalom listázása a usernek (mit kell feltölteni,
  milyen fájlnévvel).
