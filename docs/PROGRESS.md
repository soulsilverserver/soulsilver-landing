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

**Commitolva a `main`-en, DE NINCS PUSHOLVA** — push = azonnali éles deploy.

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

## Ismert, korábbról meglévő hibák (nem ehhez a munkához tartoznak)

- A `<footer>` mobilon (375px) ~20px vízszintes túlcsordulást okoz — minden
  oldalon, a referenciák oldal előtt is.
- Az `index.html` mobilon ~88px túlcsordulást mutat.
- A `.phero` és `.pband` a `100vw` full-bleed trükköt használja, ami a
  görgetősáv szélességével túlcsordul. Az új `.vband` és `.logo-marquee` már
  `width:100%`-ot használ, mert a szülő szekció úgyis teljes szélességű.

## Korábbi mérföldkövek

- 2026-09-01 — kapcsolatfelvételi űrlap + Resend API-s email (`contact.php`),
  `config.php` gitignore-olva
- 2026-08-30 — többoldalas szerkezet, közös `styles.css` + `app.js`,
  parallax hero 3 szolgáltatás-oldalon
