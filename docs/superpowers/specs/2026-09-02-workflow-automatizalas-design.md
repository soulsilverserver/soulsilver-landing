# Workflow-automatizálás szolgáltatás-oldal — design

Dátum: 2026-09-02
Státusz: jóváhagyva (user, 2026-09-02)

## Cél

Új szolgáltatás-oldal (`workflow-automatizalas.html`), amely a workflow-automatizálást
mint **kapacitásnövelést** adja el: a csapat ugyanannyi emberrel többet tud
elvégezni, mert a kézi, ismétlődő lépések kikerülnek a folyamatból.

## Pozicionálási döntés

**Kapacitás, nem létszámcsökkentés.** A user által kért „2 embert elbocsátottunk"
keretezés helyett a felszabaduló kapacitás átterhelése a történet. Két okból:
a döntéshozónak is ez az erősebb ajánlat (több munka ugyanannyi bérköltségen),
és az oldalt a látogató saját csapata is olvassa.

## Tartalmi korlát (kötelező)

A „2 fővel kevesebb adminisztráció → ~30% több elvállalt munka" eset **modellezett
példa, nem valós ügyfél adata** — a user ezt megerősítette. Ezért:

- a szekció címkéje kötelezően „SZÁMPÉLDA — modellezett forgatókönyv"
- a blokk alatt kiírva: „Modellezett példa tipikus értékekkel, nem konkrét ügyfél adata."
- a kiindulási feltételek (8 fős csapat, 2 fő adminisztráció) láthatók, hogy a
  szám levezethető legyen

Indoklás: valós ügyfélesetként megfogalmazott, kitalált konkrét eredmény
megtévesztő kereskedelmi gyakorlat (Fttv. 2008. évi XLVII. tv., UCPD), és az
oldal élő, kereskedelmi felületen jelenik meg.

A hero statisztikái szintén nem eredmény-ígéretek, hanem a szolgáltatás
betartható paraméterei: `1 hét` folyamatfelmérés, `2 hét` első élő workflow,
`0` kézi átgépelés.

## Szekciók

1. **Hero** (`.phero`) — H1: „Ugyanennyi emberrel *kétszer* annyi munka."
2. **„Hol szivárog el az idő?"** (`.incl-grid`, 6 kártya)
3. **Workflow-katalógus** — 10 workflow, 4 kategóriában, mindegyik
   trigger → lépések → eredmény szerkezetben, kinyitható akkordeonként
4. **Egy workflow anatómiája** — inline SVG folyamatábra a lead-folyamatra
5. **ROI-kalkulátor** — 3 bemenet, élő számítás, látható formula
6. **Számpélda** (`.pband`) — a modellezett eset, kötelező címkékkel
7. **„Mit NEM automatizálunk"** (`.incl-grid`, 4 kártya) — hitelesség
8. **Folyamat** (`.steprow`, 5 lépés)
9. **Árak** (`.price-grid`, 3 csomag, „-tól" árakkal)
10. **GYIK** (`.faq-list`, 5 kérdés) + `FAQPage` JSON-LD
11. **CTA** (`.page-cta`)

## Workflow-katalógus tartalma

**Ügyfélszerzés**
- Lead → azonnali válasz: űrlap → CRM → visszaigazoló email → értesítés az
  illetékesnek → 15 perc után eszkalálás
- Ajánlatkérés → ajánlat: CRM-adatokból sablon, PDF, kiküldés, follow-up 3/7/14 nap

**Projektmenedzsment**
- Feladatértesítések: fázisváltás → feladatkiosztás határidővel → csúszásnál
  emlékeztető, majd eszkalálás
- Onboarding: aláírt szerződés → mappák, hozzáférések, adatbekérő, kick-off

**Pénzügy**
- Számlázás: teljesítés → számla → 8/15 nap után emlékeztető → eszkalálás
- Költségfigyelés: napi költés-összesítés, küszöbnél riasztás

**Marketing működés**
- Riportautomatizálás: Ads + Meta adatok → heti/havi riport → PDF az ügyfélnek
- Tartalomnaptár: jóváhagyott poszt → időzítés → teljesítmény visszacsatolás
- Vélemény-begyűjtés: projektzárás → értékeléskérés → pozitív a Google-review
  linkre, negatív belső eszkalálásra
- Adatszinkron: CRM ↔ könyvelés ↔ hirdetési fiókok

## ROI-kalkulátor

Bemenetek (`range` + számmegjelenítés):
- `wfPeople` — hány fő végez ismétlődő adminisztrációt (1–20, alap 3)
- `wfHours` — napi óra fejenként (0,5–4, lépés 0,5, alap 1,5)
- `wfCost` — bruttó órabér-költség Ft (2000–12000, lépés 500, alap 4500)

Számítás (a lapon kiírva):
```
megtakított óra / hó = fő × napi óra × 21 munkanap × 0,7
```
A 0,7 az automatizálható arány feltételezése — **a lapon jelölve**, hogy ez
feltételezés, nem mérés.

Kimenetek: óra/hó, óra/év, Ft/hó, Ft/év, és „ez X fő teljes munkaidejének
felel meg" (óra/hó ÷ 168).

Jegyzet a kalkulátor alatt: a te bemeneteiddel számol, tájékoztató jellegű.

## Technikai megvalósítás

- `workflow-automatizalas.html` a szolgáltatás-oldal sablonjából (head, nav,
  mobil panel, footer, cookie bar azonos)
- `styles.css`: új blokk (`.wf-cat`, `.wf-item`, `.wf-steps`, `.wf-anatomy`,
  `.calc-*`), a meglévő tokenekkel
- `app.js`: két új null-guardolt modul — kalkulátor és katalógus-akkordeon
- Hero-kép: `img/hero-workflow.jpg`. Amíg nincs, a `.phero-bg` gradiens
  fallbackre esik (`background-image: url(...), linear-gradient(...)`)
- Bekötés: `index.html` work-grid csempe, footer Szolgáltatások lista minden
  oldalon, `sitemap.xml`. A nav NEM bővül (a „Szolgáltatások" gyűjtőre mutat)

## Nem cél (YAGNI)

- Valós integráció vagy bekötés az oldalról
- A kalkulátor eredményének emailezése (az űrlap az indexen van)
- Külön esettanulmány-aloldal

## Kész-kritériumok

- Az oldal 0px vízszintes túlcsordulással renderel 375px, 768px és desktop
  szélességen
- Az `app.js` végig lefut az összes oldalon (a `.wa-float` gomb létrejön)
- A kalkulátor minden bemenet-változásra helyesen újraszámol; a kimenet
  magyar számformátumban jelenik meg
- Az akkordeon billentyűzetről is működik, `aria-expanded`-del
- `prefers-reduced-motion` esetén nincs mozgás
- A számpélda-szekció mindkét kötelező jelölést tartalmazza
