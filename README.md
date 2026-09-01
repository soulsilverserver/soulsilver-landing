# SOULSILVER — Marketing Agency

A SOULSILVER. Marketing Agency flagship landing oldala — full-service marketing ügynökség + saját CRM rendszer bemutatása.

Statikus, többoldalas site külső build lépés nélkül. Közös stíluslap (`styles.css`)
és közös szkript (`app.js`); a betűtípusok a Google Fontsról töltődnek.

Oldalak: `index.html`, `referenciak.html`, `crm.html`, `koszonjuk.html` és hét
szolgáltatás-aloldal (`ppc-hirdeteskezeles`, `markaidentitas`, `kozossegi-media`,
`weboldalkeszites`, `dronfelvetel`, `aftermovie`, `termekfotozas`).

Az aktuális állapot és a hátralévő teendők: [docs/PROGRESS.md](docs/PROGRESS.md).

## Szekciók

- **Hero** — folyékony króm/ezüst háttér, színes szó-badge headline
- **Platformok** — valódi márkaikonok (Meta, Google Ads, TikTok, YouTube, LinkedIn)
- **Weboldal készítés** — látványos böngésző + mobil mockup, Web Vitals score
- **Funkciók** — CRM funkció-rács mini mockupokkal
- **Folyamat** — sticky/parallax 5 lépéses szekció
- **CRM** — termékbemutató dashboard-mockuppal
- **Munkáink**, **adatbiztonság**, **hírlevél**, záró CTA

Külön oldalon: **Referenciák** (`referenciak.html`) — szűrhető bento portfólió,
lightbox és parallax videó-sávok.

## Helyi futtatás

```bash
python -m http.server 8000
```

Majd nyisd meg: http://localhost:8000

## Deploy

A `main` branchre pusholt változások automatikusan élesednek a Hostingeren
(lásd a deploy beállítást a projekt dokumentációjában / hPanelben).
