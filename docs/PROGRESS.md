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

## Hátralévő teendők a referenciák oldalon

1. **Valós tartalom** (kötelező élesítés előtt) — most minden kártya, statisztika
   és média-útvonal helykitöltő, a HTML-ben `DEMÓ` kommenttel jelölve:
   - `referenciak.html` hero: a három `data-count` érték (24+, 1,8M, 7)
   - a 10 `.bento-item` címe, leírása, kategóriája
   - a két `.vband` szövege
2. **Média feltöltése** az `img/ref/` mappába. A kártyák a fájlnevet a
   `data-lb-src` és a `background-image` alapján keresik; amíg nincs fájl, a
   gradiens látszik és a lightbox „A látványanyag hamarosan felkerül." szöveget
   mutat — nem törik el semmi. Szükséges fájlok:
   - képek: `termek-kozmetikum.jpg`, `ppc-webshop.jpg`, `marka-arculat.jpg`,
     `dron-ipari.jpg`, `social-kreativ-teszt.jpg`, `termek-gasztro.jpg`,
     `social-vendeglatas.jpg`, `web-szolgaltato.jpg`, `aftermovie-fesztival.jpg`
   - videók: `aftermovie-fesztival.mp4`, `dron-ingatlan.mp4`,
     `vband-aftermovie.mp4` (+ `vband-aftermovie.jpg` poszter), `vband-dron.mp4`
3. **Ügyféllogók**: jelenleg a marquee a platformlogókat mutatja
   („Platformok, amelyeken dolgozunk") — ez szándékos, mert valós ügyféllogó még
   nincs. Ha meglesznek: `img/logos/ugyfel/` mappa, az eyebrow átírása
   „Ügyfeleink"-re, és a `.logo-item` képek cseréje (a listát kétszer kell
   felsorolni a végtelen csúszás miatt).
4. **Push** a `main`-re, amint a tartalom valós.

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
