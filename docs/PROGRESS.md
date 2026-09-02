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

