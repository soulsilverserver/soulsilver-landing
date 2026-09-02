# SOULSILVER — állapot

Utolsó frissítés: 2026-09-01

## Kész

### Referenciák oldal (2026-09-01)

- Spec: [docs/superpowers/specs/2026-09-01-referenciak-oldal-design.md](superpowers/specs/2026-09-01-referenciak-oldal-design.md)
- Terv: [docs/superpowers/plans/2026-09-01-referenciak-oldal.md](superpowers/plans/2026-09-01-referenciak-oldal.md)
- Új oldal: `referenciak.html` — hero + count-up statisztikák, platform-logó
  marquee, 8 kategóriás szűrő, 10 kártyás bento mozaik, lightbox, 2 full-bleed
  parallax videó-sáv, CTA
- `styles.css`: új `REFERENCIÁK OLDAL` blokk a fájl végén
- `app.js`: új modulok (count-up, szűrő, lightbox, vband-parallax, hover-tilt),
  mind null-guardolt
- Bekötve: nav + mobil panel + footer mind a 9 tartalmi oldalon, `sitemap.xml`
- `robots.txt`: `Disallow: /docs/`

Pusholva a `main`-re 2026-09-01-en, él: https://soulsilver.hu/referenciak.html
(minden `main`-re pusholt commit azonnal élesedik a Hostinger auto-deployon).

### Valós tartalom (2026-09-01, 2. kör)

A demó szövegek lecserélve a Drive `REFERENCIÁK` mappája alapján
(https://drive.google.com/drive/folders/14JKD5Zm0nPWb_LGkFkLUiKpwxlyQXBod).
12 valós referencia-kártya:

| # | Projekt | Kategória | Média-slug |
|---|---------|-----------|------------|
| 1 | YOUTOPIA × Sara Landry | aftermovie, social | `sara-landry` |
| 2 | Kowalsky Meg a Vega — Budapest Park | aftermovie | `kowalsky-bp-park` |
| 3 | Barabás Bio Hungary | web | `barabas-bio-hungary` |
| 4 | Nyári rendezvényszezon | aftermovie, social | `rendezvenyek` |
| 5 | Építőipari videósorozat | social, marka | `epitoipar` |
| 6 | Foqusd | web, marka | `foqusd` |
| 7 | THE G.O.A.T. España | social, termek | `goat-espana` |
| 8 | Eurama | marka | `eurama` |
| 9 | Hirdetési kreatívok | ppc, social | `hirdetesi-kreativok` |
| 10 | GLDN Street | marka | `gldn-street` |
| 11 | Parton Tali — légi felvételek | dron | `parton-tali` |
| 12 | BUZZ Sneaker Store | termek, marka | `buzz-sneaker` |

A két videó-sáv: YOUTOPIA × Sara Landry és THE G.O.A.T. España.

Hero statisztikák a Drive-mappák alapján számolva: 30+ lezárt projekt,
14 dokumentált rendezvény, 7 szolgáltatási terület. **Ezeket érdemes
ellenőrizni** — mappaszámlálásból származnak, nem könyvelésből.

Kikerült az oldalról a `img/hero-dronfelvetel.jpg`: az a szolgáltatás-oldal
dekorációja, nem valós referenciaanyag, ezért nem szerepelhet referenciaként.

## Hátralévő teendők a referenciák oldalon

1. **Média feltöltése** az `img/ref/` mappába. Minden kártya `<slug>.jpg`
   posztert vár, a videós kártyák `<slug>.mp4`-et is (lásd a fenti táblát).
   Amíg hiányzik, a gradiens látszik és a lightbox „A látványanyag hamarosan
   felkerül." szöveget mutat — nem törik el semmi.
   A Drive-ban lévő nyers fájlok nagyok (a Barabás weboldal-felvétel 1,79 GB),
   webre tömöríteni kell — a git repóba nyers videó ne kerüljön.
2. **Ügyféllogók**: a marquee továbbra is platformlogókat mutat
   („Platformok, amelyeken dolgozunk"). Ha lesz engedélyezett ügyféllogó:
   `img/logos/ugyfel/`, az eyebrow átírása „Ügyfeleink"-re, és a `.logo-item`
   képek cseréje (a listát kétszer kell felsorolni a végtelen csúszás miatt).
3. **Szövegek ellenőrzése**: a leírásokat a Drive mappa- és fájlnevekből
   vezettem le. Ahol a projekt tartalma pontosabban megfogalmazható, javítsd.

## Hibakeresés eredménye (2026-09-02, `fdbda59` állapot)

Részletes terv a javításokról és a hátralévő munkáról:
[docs/superpowers/plans/2026-09-02-hatralevo-munkak.md](superpowers/plans/2026-09-02-hatralevo-munkak.md)

**RENDBEN:** az `app.js` mind a 15 oldalon végig lefut; nincs törött belső link,
halott horgony, duplikált `id`, `alt` nélküli kép vagy tag-eltérés; a
referenciák oldal szűrője és lightboxa a párhuzamos módosítások után is működik;
a WhatsApp gomb (z-index 70) helyesen a lightbox (120) alatt van.

**VÍZSZINTES TÚLCSORDULÁS 375px-en** (`body.scrollWidth - html.clientWidth`):

| Oldal | Túlcsordulás | Okozó |
|---|---|---|
| `adatvedelem.html` | **330px** | `.legal-table` 666px széles, nem görgethető |
| `crm.html` | 115px | `.phero` + footer |
| `index.html` | 103px | `.rail`, `.step-cards` 439px |
| `cookie-szabalyzat.html` | 51px | `.legal-table` 387px |
| a többi 10 oldal | 35px | `.foot-grid` 2 kolúmna 48px gappel |
| `koszonjuk.html` | 0px | (nincs nav/footer) |

A footer gyökéroka: `.foot-grid` 760px alatt `1fr 1fr` + `gap: var(--sp-6)`
(48px); 360px-en két ~205px-es kolúmna nem fér ki, és a negyediket az
`info@soulsilvermarketing.com` link (205px, nem tördelhető) feszíti szét.

**További hiányok:** a `sitemap.xml`-ből kimaradt a négy új jogi oldal
(`impresszum`, `adatvedelem`, `aszf`, `cookie-szabalyzat`); a `koszonjuk.html`-en
nincs `canonical`.

**Blokkoló:** a `.claude/worktrees/sharp-pike-731f5a` worktree (branch
`claude/sharp-pike-731f5a`) félbehagyva áll a `d724a09` bázison, nem commitolt
`styles.css` módosítással — ugyanazt a fájlt érinti, mint az 1. fázis.


## Javítva (2026-09-02)

A fenti hibakeresés összes túlcsordulási hibája megszűnt. **Mind a 16 oldal
0px vízszintes túlcsordulást mér 375px, 768px és 1280px szélességen.**

| Commit | Mit javított |
|---|---|
| `ca80166` | `.foot-grid > * { min-width:0 }` + tördelhető email → a site-szintű 35px; `.phero`/`.pband` `100vw` → `100%`; `.reveal` vízszintes eltolás mobilon; `.subnav` tördelés; `.price-grid` 1180px töréspont; hosszú magyar szavak `overflow-wrap` |
| `2f6ec48` | `.table-scroll` görgethető keret a jogi táblázatoknak (330px és 51px); a négy jogi oldal bekerült a `sitemap.xml`-be |

A `.claude/worktrees/sharp-pike-731f5a` worktree beolvasztva a `main`-be
(`git rebase main` konfliktus nélkül), a branch törölve. A `.claude/worktrees/`
alatt maradt egy üres, zárolt könyvtár — gitignore-olt, ártalmatlan.

**Nyitva maradt, alacsony prioritás:** a `koszonjuk.html`-en nincs `canonical`
(az oldal `Disallow`-olt a `robots.txt`-ben, ezért SEO-szempontból nem sürgős).

## Workflow-automatizálás oldal (2026-09-02)

Spec: [2026-09-02-workflow-automatizalas-design.md](superpowers/specs/2026-09-02-workflow-automatizalas-design.md)
Terv: [2026-09-02-workflow-automatizalas.md](superpowers/plans/2026-09-02-workflow-automatizalas.md)

Új oldal: `workflow-automatizalas.html` — a 8. szolgáltatás-oldal.

- Hero kapacitás-üzenettel, „Hol szivárog el az idő?" 6 kártyával
- Workflow-katalógus: 10 workflow 4 kategóriában, akkordeonként kinyitható,
  mindegyik trigger → 5 lépés → eredmény szerkezetben
- Inline SVG folyamatábra: a lead-válasz workflow anatómiája
- ROI-kalkulátor: 3 csúszka, élő számítás, a lapon kiírt formulával
- Modellezett számpélda a kötelező jelölésekkel
- „Mit NEM automatizálunk" 4 kártya, folyamat 5 lépés, 3 árcsomag,
  5 GYIK + `FAQPage` JSON-LD
- `styles.css`: új `WORKFLOW AUTOMATIZÁLÁS OLDAL` blokk
- `app.js`: 2 új null-guardolt modul (akkordeon, kalkulátor)
- Bekötve: `index.html` work-grid csempe + footer Szolgáltatások lista mind a
  15 oldalon, `sitemap.xml`

**Pozicionálás:** kapacitás („ugyanennyi emberrel többet"), NEM
létszámcsökkentés. A kért „2 embert elbocsátottunk, +30% bevétel" eset
**modellezett példaként** került ki, `SZÁMPÉLDA · modellezett forgatókönyv`
címkével és záró jegyzettel — a user megerősítette, hogy nem valós ügyfél
adata. Ezt a két jelölést nem szabad eltávolítani (Fttv./UCPD).

**Ellenőrizve:** a kalkulátor kimenete kézzel visszaszámolva egyezik
(3 fő × 1,5 óra × 21 munkanap × 0,7 = 66 óra/hó, 297 675 Ft/hó; 10 fő × 2 óra
× 6000 Ft = 294 óra/hó, 1 764 000 Ft/hó); az akkordeon `aria-expanded`-del
nyit és zár; az `app.js` mind a 16 oldalon végig lefut.

**Hátra van ezen az oldalon:**

1. **Hero-kép** (`img/hero-workflow.jpg`) — amíg nincs, a gradiens fallback
   látszik, ami önmagában is jól néz ki. A `chatgpt-parallax` skillel
   generálható, mint a többi szolgáltatás-oldal heroja.
2. **Árak jóváhagyása** — 90 000 / 180 000 / 490 000 Ft-tól + 39 000 Ft/hó
   üzemeltetés. Ezeket én tettem be a többi oldal nagyságrendjéhez illesztve,
   a user nem hagyta jóvá számszerűen.
3. **Valós esettanulmány**, ha lesz dokumentált eset ügyfél-hozzájárulással —
   akkor a modellezett számpélda kiváltható.

## Mobilnézet-audit (2026-09-02)

Programozott mérés 17 oldalon × 5 szélességen (320/375/414/768/1280) = 85 mérés.
Mért szempontok: dokumentum-túlcsordulás, saját konténerén kilógó/levágott
szöveg, 24×24px alatti tap-target, apró betűk, átfedő elemek, kilógó képek,
és hogy az `app.js` végig lefut-e.

### Talált és javított hibák

| Hol | Mi volt | Javítás |
|---|---|---|
| `arak.html`, workflow ROI | Az SVG-diagram feliratai **~7,5px-en** jelentek meg mobilon (a 760/520 egységes viewBox 246px-es keretbe zsugorodott), és a diagram 41%-a látszott egyszerre | Az ártartomány-diagram mobil változata **HTML/CSS sávlista** (az SVG-szöveg a konténerrel skálázódik, tehát nem lehet reszponzív); a ROI-görbe mobil viewBox-a 240 egység |
| workflow-katalógus | 320px-en a címke-pill a workflow nevét **31px-re** szorította | A pill a név alá kerül 520px alatt; a név `flex:1 1 0`-val a chevronnal egy sorban marad |
| `referenciak.html` | A `.bento-title` levágódott 320px-en (a kártya `overflow:hidden`) | `overflow-wrap:anywhere` a bento szövegeken |
| `aszf.html` | **25px túlcsordulás**: a „Nyilvántartási/cégjegyzékszám:" nem tört (a `/` nem törési pont) | `overflow-wrap:anywhere` a `.legal-content` listákon |
| kalkulátor | A csúszkák **16px** magasak voltak — ujjal alig fogható | 44px magas sáv, 24px-es fogantyú (webkit + moz) |
| footer, jogi oldalak | Listalinkek **20–22px** magasak | `padding-block` → 31–36px (`.foot-grid`, `.legal-content`, `.foot-legal`) |
| mobil fejléc | A logó (135px) + CTA-gomb (117px, két sorba tördelve) + hamburger (44px) = 323px a 309px-es helyen; a gomb a logóhoz tapadt, a fejléc 84px magas lett | A fejléc-CTA 620px alatt elrejtve — a Kapcsolat a hamburger-menüben és a WhatsApp gombban is elérhető |
| szűrő-chipek | 30px magasak | 620px alatt nagyobb padding |

### Vakriasztás (nem hiba)

- `input.hp-field` — a kapcsolati űrlap honeypotja. `left:-9999px`, `opacity:0`,
  `tabindex="-1"`, `aria-hidden` → helyesen van elrejtve, csak geometriája van.
- A logó-marquee képei „kilógnak", de a `.logo-marquee` `overflow:hidden`-je levágja.

### Tudatosan nyitva hagyva

- **Desktop nav-linkek 21px magasak** (csak 1280px-en jelez). A köztük lévő
  térköz 32px, ami kimeríti a WCAG 2.5.8 spacing-kivételét, és egérrel pontos —
  a fejléc dizájnját nem érdemes ezért átszabni.
- **A lebegő WhatsApp gomb** 320px-en a lap-alji CTA-gomb sarkát ~3×19px-en
  fedi (a link 1%-a). Bármely lap-alji CTA-val előfordul; nem tap-hiba.

**Eredmény: 320/375/414px-en nulla túlcsordulás, nulla levágott szöveg, nulla
24px alatti tap-target mind a 17 oldalon.**

## Szolgáltatás-hero képek kivéve (2026-09-02)

A generált „hightech iroda" fotók kikerültek mind a 9 oldalról (8 szolgáltatás
+ CRM). A user szerint AI-klisék voltak; a mérés ezt alátámasztja: a
-n a monitoron **halandzsa szöveg** van („ROAS" után
olvashatatlan karakterek), ami klasszikus generálási artifact, és ugyanez a
 papírjain.

Helyettük **rétegzett gradiens** minden oldalon,  osztályokkal
a -ben. Két dolog kellett hozzá:

1. A  , tehát a gradiens a
   **viewporthoz** méretezett, nem az elemhez — a százalékos pozíciók a
   képernyőre értendők.
2. A  a jobb oldalon is ~44%-ot elnyel, ezért az első próbálkozás
   (alfa 0,2–0,3) sík sötét sávnak látszott. Éles kifutású, 0,7–0,95 alfájú
   fénypont kellett, hogy szándékos megvilágításnak olvasódjon. A fény a jobb
   oldalon van, ahol a scrim a legvilágosabb; balra a scrim 94%, oda kerül a
   fehér szöveg.

Oldalanként más a fénypont helye és színe (9 különböző háttér — ellenőrizve).
A 8 hero-JPEG (900 KB) törölve, mert hivatkozás nélkül maradt; a git
történetében megvan, ha kellene.

### Nyitott: az og-image.jpg

A közösségi megosztásokhoz használt  **1200×1335, tehát
portré**. Az Open Graph 1200×630-at vár (1,91:1), így a platformok a középső
sávot vágják ki — a megosztásokból pont a „SOUL SILVER" és a „MARKETING
AGENCY" felirat esne le, és nagyrészt a tenger meg az „ARTIFICIAL
INTELLIGENCE" maradna. A bézs/barna színvilág a site grafit + mint
palettájához sem passzol. Mind a 16 oldal ezt a képet hivatkozza.
