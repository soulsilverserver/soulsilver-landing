# SOULSILVER — Marketing Agency

A SOULSILVER. Marketing Agency weboldala — full-service marketing ügynökség +
saját CRM bemutatása.

> ## 👉 Átvétel / folytatás
>
> **Ha új sessionként veszed át a munkát, [docs/PROGRESS.md](docs/PROGRESS.md)
> az egyetlen fájl, amit el kell olvasnod.** Ott van a jelenlegi állapot, a
> nyitott döntések, a hátralévő munka prioritás szerint, és egy „Csapdák"
> szakasz azokról a nem nyilvánvaló dolgokról, amiket drága volt megtalálni
> (pl. az árak négy külön helyen vannak; a `clip-path` nem lehet azon az
> elemen, amit IntersectionObserver figyel; a preview-panelben nem fut a
> `requestAnimationFrame`).

Statikus, többoldalas site build lépés nélkül. Közös stíluslap (`styles.css`)
és közös szkript (`app.js`); a betűtípusok a Google Fontsról töltődnek.

## Oldalak

| | |
|---|---|
| `index.html` | főoldal |
| `arak.html` | gyűjtő árlista, ártartomány-diagramokkal |
| `referenciak.html` | portfólió (bento rács, lightbox, parallax videó-sávok) |
| `crm.html` | a saját CRM bemutatása |
| `koszonjuk.html` | űrlap utáni köszönőoldal (itt tüzel a lead-konverzió) |
| 8 szolgáltatás-oldal | `ppc-hirdeteskezeles`, `workflow-automatizalas`, `markaidentitas`, `kozossegi-media`, `weboldalkeszites`, `dronfelvetel`, `aftermovie`, `termekfotozas` |
| 4 jogi oldal | `impresszum`, `adatvedelem`, `aszf`, `cookie-szabalyzat` |

## Helyi futtatás

```bash
python -m http.server 8643
```

Majd nyisd meg: http://localhost:8643

## Ellenőrzés

```bash
python3 tools/audit_static.py     # linkek, id-k, alt, nav, sitemap, canonical
python3 tools/audit_prices.py     # az árak egyeznek-e mind a négy helyen
```

Layout-mérés böngészőben (túlcsordulás, levágott szöveg, tap-target, címsorok):
lásd [tools/audit_browser.js](tools/audit_browser.js) — a fájl fejlécében ott a
használat. Ez azért szkript és nem szemrevételezés, mert a preview-panel
képernyőképe nem lát a hajtás alá.

## Generátorok

```bash
python3 tools/gen_charts.py       # arak.html diagramjai
python3 tools/gen_roi.py          # a workflow-oldal ROI-görbéje
python3 tools/gen_arlista_pdf.py  # letölthető árlista PDF
```

Részletek: [tools/README.md](tools/README.md)

## Deploy

A `main` branchre pusholt változások **automatikusan élesednek** a Hostingeren
(hPanel → Advanced → GIT, auto-deployment BE). Nincs staging: amit pusholsz, az
azonnal kint van a soulsilver.hu-n.

## Titkos konfiguráció

A kapcsolati űrlap (`contact.php`) a Resend API-val küld emailt. Az API kulcs a
**gitignore-olt `config.php`-ban** van (sablon: `config.example.php`). A repo
publikus — kulcs soha ne kerüljön commitba.
