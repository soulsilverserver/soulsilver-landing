# Hátralévő munkák terve — soulsilver.hu

Készült: 2026-09-02, a `fdbda59` commit állapotára futtatott hibakeresés alapján.

**Cél:** a referenciák oldal befejezése (média + tartalom) és a hibakeresésben
feltárt reszponzív hibák javítása, prioritási sorrendben.

**Kontextus:** a site statikus, build nélküli; közös `styles.css` + `app.js`.
Minden `main`-re pusholt commit AZONNAL élesedik a Hostinger auto-deployon —
tehát a `main`-re csak kész, ellenőrzött munka kerülhet.

---

## 0. Előfeltétel: a félbehagyott worktree rendezése

**Ez blokkolja a 2. fázist**, mert ugyanazt a `styles.css`-t módosítja.

`.claude/worktrees/sharp-pike-731f5a` (branch: `claude/sharp-pike-731f5a`)
a `d724a09` bázison áll — három committtal a `main` mögött —, és van benne
nem commitolt `styles.css` módosítás + egy `_overflow-test.html` segédfájl.
Ez a footer-túlcsordulás taszkja, félbehagyva.

- [ ] Eldönteni: a benne lévő módosítás használható-e, vagy dobjuk
- [ ] Ha használható: rebase a `main`-re, majd beolvasztás
- [ ] Ha nem: `git worktree remove` és a branch törlése
- [ ] `_overflow-test.html` NE kerüljön a repóba (a `.gitignore` már fogja a
      `_preview_*.html`-t, ezt is érdemes felvenni)

---

## 1. fázis — Reszponzív hibák (P1)

Mérés módja mindenhol: 375px-es iframe-ben
`document.body.scrollWidth - document.documentElement.clientWidth`.
Jelenlegi értékek (360px tényleges tartalomszélesség mellett):

| Oldal | Túlcsordulás | Okozó |
|---|---|---|
| `adatvedelem.html` | **330px** | `.legal-table` 666px széles |
| `crm.html` | 115px | `.phero` + footer |
| `index.html` | 103px | `.rail`, `.step-cards` 439px |
| `cookie-szabalyzat.html` | 51px | `.legal-table` 387px |
| a többi 10 oldal | 35px | footer |
| `koszonjuk.html` | 0px | (nincs nav/footer) |

### 1.1 Jogi táblázatok (`adatvedelem.html`, `cookie-szabalyzat.html`)

- [ ] `styles.css` — a `.legal-table` kapjon vízszintesen görgethető keretet:
      a táblát egy `.table-scroll{overflow-x:auto}` wrapperbe tenni, VAGY
      `display:block; overflow-x:auto` a táblán 560px alatt
- [ ] A `th`/`td` kapjon `min-width`-et, hogy ne törjön szét olvashatatlanul
- [ ] Ellenőrzés: mindkét oldal túlcsordulása 0 legyen 375px-en

### 1.2 Footer (mind a 14 oldal)

Gyökér: `.foot-grid` 760px alatt `1fr 1fr`, `gap: var(--sp-6)` = 48px.
360px-en két ~205px-es kolumna + 48px gap nem fér ki. A negyedik kolumnát
az `info@soulsilvermarketing.com` link (205px, nem tördelhető) feszíti ki.

- [ ] `styles.css` — új töréspont 560px alatt: `.foot-grid{grid-template-columns:1fr}`
- [ ] A gap csökkentése mobilon (`var(--sp-4)`)
- [ ] `.foot-grid a{overflow-wrap:anywhere}` — hogy a hosszú email tördelhető legyen
- [ ] Ellenőrzés: a 35px-es alapszintű túlcsordulás nullázódik minden oldalon

---

## 2. fázis — Reszponzív hibák (P2)

### 2.1 `index.html` folyamat-szekció

- [ ] A `.rail` és `.step-cards` (439px) mobil viselkedésének javítása —
      valószínűleg fix szélesség vagy `min-width` van rajtuk
- [ ] Ellenőrzés: `index.html` túlcsordulása 0

### 2.2 `.phero` / `.pband` 100vw

- [ ] A `width:100vw; left:50%; margin-left:-50vw` mintát ugyanúgy `width:100%`-ra
      cserélni, ahogy a `.vband` és `.logo-marquee` esetében már megtörtént —
      DE előbb ellenőrizni, hogy a szülő elem tényleg teljes szélességű-e
      mind a 6 érintett oldalon (a `.phero` a `<main>` közvetlen gyereke)
- [ ] Ez ~8px-et hoz vissza minden parallax hero-s oldalon

### 2.3 Sitemap hiányok

A másik session négy jogi oldalt hozott létre, de a `sitemap.xml`-be nem kerültek be.

- [ ] `impresszum.html`, `adatvedelem.html`, `aszf.html`, `cookie-szabalyzat.html`
      felvétele (alacsony `priority`, pl. 0.3)
- [ ] `koszonjuk.html` szándékosan kimarad (robots-ban is `Disallow`)

---

## 3. fázis — Referenciák: média

Ez a legnagyobb tétel, és a felhasználó közreműködése kell hozzá.

Forrás: Drive `REFERENCIÁK` mappa
(https://drive.google.com/drive/folders/14JKD5Zm0nPWb_LGkFkLUiKpwxlyQXBod)

Jelenleg 21 hivatkozott médiafájl hiányzik. A kártyák nem törnek el
(gradiens-fallback), de a portfólió képek nélkül félkész.

| Slug | Kell | Drive-forrás |
|---|---|---|
| `sara-landry` | .jpg + .mp4 | Videók/Sara Landry after movie, Fénykép/SARALANDRY KÉP |
| `kowalsky-bp-park` | .jpg + .mp4 | Videók/Kowalsky Meg a Vega BP Park/KOWA-bppark.mov |
| `barabas-bio-hungary` | .jpg + .mp4 | Weboldalak/barabasbiohungaryweboldal.mov |
| `rendezvenyek` | .jpg | Fénykép/Plázs, Romkert, Mundo, HOTSPOT mappák |
| `epitoipar` | .jpg + .mp4 | Videók/Építőipar |
| `foqusd` | .jpg + .mp4 | Weboldalak/Foqusd Képernyőfelvétel |
| `goat-espana` | .jpg + .mp4 | Videók/Goat, Fénykép/THE G.O.A.T. ESPANA |
| `eurama` | .jpg + .mp4 | AI/Eurama_airport_videó.mov |
| `hirdetesi-kreativok` | .jpg | Grafikák,Logók stb. |
| `gldn-street` | .jpg + .mp4 | Animáció/GLDNSTREET.mov |
| `parton-tali` | .jpg | Fénykép/PartonTaliDrónkép |
| `buzz-sneaker` | .jpg + .mp4 | AI/BUZZ_sneaker_store_.mp4 |

**Fontos megkötés:** a Drive-ban nyers fájlok vannak (a Barabás
weboldal-felvétel 1,79 GB). Nyers videó NEM kerülhet a git repóba —
a Hostinger tárhely és a git is megszenvedi, a látogató pedig nem tölt le
gigabájtokat.

- [ ] Döntés: a videók a repóba (tömörítve) vagy külső hostra (YouTube/Vimeo)?
      Ha külső: a lightbox már támogatja a `data-lb-type="youtube"` módot,
      csak az `data-lb-src`-t kell embed-URL-re cserélni — kevesebb sávszél,
      kevesebb tárhely, viszont külső beágyazás és sütik
- [ ] Poszterképek: 1600px szélesség, JPEG ~80%, cél <250 KB/db
- [ ] Ha repóba mennek a videók: 1080p, H.264, ~2-3 Mbps, cél <8 MB/db,
      hang nélkül (a kártyák némán játszanak)
- [ ] Feltöltés `img/ref/` alá a fenti slug-nevekkel
- [ ] Ellenőrzés: a 21 `MISSING` bejegyzés nullára csökken az auditban

---

## 4. fázis — Referenciák: tartalom pontosítása

- [ ] **Statisztikák ellenőrzése.** A hero három száma (30+ lezárt projekt,
      14 dokumentált rendezvény, 7 szolgáltatási terület) a Drive-mappák
      megszámolásából jött, nem könyvelésből. Ha van pontos adat, cserélni.
- [ ] **Leírások ellenőrzése.** A 12 kártya leírását a Drive mappa- és
      fájlneveiből vezettem le. Ahol a projekt tartalma pontosabban
      megfogalmazható (mit kért az ügyfél, mi lett a végeredmény), javítani.
- [ ] **Ügyféllogók.** A marquee ma platformlogókat mutat („Platformok,
      amelyeken dolgozunk") — ez szándékos, mert engedélyezett ügyféllogó
      nincs. Ha lesz: `img/logos/ugyfel/`, eyebrow → „Ügyfeleink", és a
      `.logo-item` képek cseréje. A listát KÉTSZER kell felsorolni,
      különben a végtelen csúszás megszakad.
- [ ] **Jogi kérdés:** ügyféllogó és ügyfélnév megjelenítéséhez érdemes
      engedély. A projektnevek (Kowalsky Meg a Vega, Barabás Bio Hungary,
      YOUTOPIA) már kint vannak az élesen — ha valamelyikhez nincs
      hozzájárulás, azt le kell venni.

---

## 5. fázis — Apróságok (P3)

- [ ] `koszonjuk.html`: nincs `<link rel="canonical">` — bekerülhet,
      bár az oldal `Disallow`-olt a robots.txt-ben
- [ ] Szűrő-chipek akadálymentesítése: a `.chip` gombokra `aria-pressed`
      attribútum, hogy képernyőolvasó is jelezze az aktív szűrőt
- [ ] `README.md` szekciólistája még nem tartalmazza a jogi oldalakat
      és a WhatsApp gombot

---

## Amit a hibakeresés RENDBEN talált

Ezeket ellenőriztem, nem kell velük foglalkozni:

- Az `app.js` mind a 15 oldalon végig lefut, kivétel nélkül (a lebegő
  WhatsApp gomb a szkript legvégén jön létre — ha megvan, nem szakadt meg)
- Nincs törött belső hivatkozás, nincs halott horgony (`#…`)
- Nincs duplikált `id`, nincs `alt` nélküli `<img>`
- Nincs nyitó/záró tag eltérés egyik oldalon sem
- A `canonical` minden oldalon egyezik a fájlnévvel (a `koszonjuk.html`
  kivételével, ahol nincs)
- A referenciák oldal interakciói a másik session módosításai után is
  működnek: szűrés (Drón → 1 találat), lightbox (`1 / 12`, valós cím),
  Esc-zárás, `body` scroll visszaállítás
- A WhatsApp gomb (`z-index:70`) helyesen a lightbox (`z-index:120`) ALATT
  van, tehát nem lóg bele a megnyitott képnézőbe
- A `.vband` és `.logo-marquee` már nem okoz túlcsordulást (`width:100%`)

---

## Javasolt sorrend

1. **0. fázis** — worktree rendezése (blokkoló)
2. **1. fázis** — P1 reszponzív hibák: a jogi táblázat 330px-es
   túlcsordulása a legrosszabb élő hiba, a footer pedig minden oldalt érint
3. **2.3** — sitemap (két perc, SEO-hatás)
4. **3. fázis** — média (a legnagyobb érték a látogatónak, de külső
   döntést és fájlokat igényel)
5. **2.1, 2.2** — P2 reszponzív
6. **4. és 5. fázis** — tartalmi pontosítás és apróságok
