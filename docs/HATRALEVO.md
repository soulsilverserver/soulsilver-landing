# Ami hátra van — átadási jegyzet

Állapot dátuma: **2026-09-11**. Ez a fájl önmagában elég ahhoz, hogy egy új
munkamenet folytassa a munkát: minden konkrét szöveg, kulcsszó és URL benne van,
nem kell visszaolvasni a beszélgetést.

---

## 1. Google Ads: 2 hirdetéscsoport hiányzik (ez a legsürgősebb)

**Kampány:** „Marketing Ügynökség - Search - HU", `campaignId=24188369439`
**Fiók:** `ocid=7364900281`
**Létrehozás URL-je:**
`https://ads.google.com/aw/adgroups/new/search?campaignId=24188369439&ocid=7364900281`

### KÉSZ ✅ — „Ügyfélszerzés" hirdetéscsoport

Létrejött 2026-09-11-én, RSA-val együtt elmentve. Végső URL:
`https://soulsilver.hu/ugyfelszerzes.html`, megjelenítési útvonal: `/ugyfelszerzes`.

### HIÁNYZIK ❌ — „Fogászati marketing"

- **Végső URL:** `https://soulsilver.hu/fogaszati-marketing.html`
- **Megjelenítési útvonal 1:** `fogaszati-marketing`
- **Kulcsszavak** (soronként, ahogy a beviteli mezőbe kell illeszteni):

```
[fogászati marketing]
"fogászati marketing"
"fogorvosi marketing"
"fogászat marketing"
"fogászati rendelő marketing"
"fogászati hirdetés"
"páciensszerzés fogászat"
```

- **Címsorok (15, max. 30 karakter):**

```
Pácienseket Hozunk Neked
Fogászati Marketing
Fogászati Marketing Cégnek
Új Páciensek A Rendelőbe
Implantátum Páciensek
Fogszabályozás Páciensek
Fix Páciensszám Havonta
Fogorvosi Marketing
30 Mp Alatt Visszahívjuk
Nem Lájkokat, Pácienst
Rendelőre Szabott Kampány
Kérj Ingyenes Felmérést
Google És Meta Fogászatra
Telt Naptár, Nem Néma Telefon
SOULSILVER Marketing
```

- **Leírások (4, max. 90 karakter):**

```
Pácienseket hozunk a rendelődbe, nem lájkokat. Fix célszám a szerződésben.
Implantátum és fogszabályozás kampányok: a nagy értékű kezelésekre megy a költés.
Minden érdeklődőt 30 másodpercen belül visszajelzünk. Kevesebb elveszett páciens.
Ingyenes felmérés a rendelődre. Megnézzük, hány új páciens fér még be.
```

### HIÁNYZIK ❌ — „Építőipari marketing"

- **Végső URL:** `https://soulsilver.hu/epitoipari-marketing.html`
- **Megjelenítési útvonal 1:** `epitoipari-marketing`
- **Kulcsszavak:**

```
[építőipari marketing]
"építőipari marketing"
"építőipari ügyfélszerzés"
"kivitelező marketing"
"építőipari hirdetés"
"építőipari cég marketing"
```

- **Címsorok (15):**

```
Megrendeléseket Hozunk
Építőipari Marketing
Építőipari Ügyfélszerzés
Tele Naptár Egész Évben
Kivitelezőknek, Szakiknak
Árral Szűrt Érdeklődők
Kevesebb Üres Kiszállás
Nem Néma Telefon
Fix Célszám A Szerződésben
Építőipari Cég Marketing
Google És Meta Kampányok
Kérj Ingyenes Felmérést
Komoly Megrendelők
Telt Kapacitás, Tervezhető
SOULSILVER Marketing
```

- **Leírások (4):**

```
Megrendeléseket hozunk kivitelezőknek. Árral előszűrt érdeklődők, kevesebb üres út.
Nem lájkokat számolunk, hanem ajánlatkérést. Fix célszám a szerződésben.
Google és Meta kampány, landing oldal, mérés. Egy felelős csapat, havi riport.
Ingyenes kapacitás-felmérés: megnézzük, mennyi munkát bírsz még elvállalni.
```

### Amit a Google Ads UI-ban tudni kell (buktatók, már megtapasztalva)

1. **Ne használj `ctrl+a`-t vagy szabad szövegbeírást** a Google Ads
   űrlapmezőibe, ha nincs biztosan fókuszban a mező: a Google Ads globális
   billentyűparancsokat futtat (`G`+`Y` = Javaslatok), és elnavigál az oldalról.
   Helyette: `read_page` → `ref_N` → **`form_input`**. Ez mindig működött.
2. A **címsor- és leíráslista virtualizált**: egyszerre csak 4-9 mező van a
   DOM-ban. Ciklus: `form_input` a látható mezőkre → `scroll down 4-5` →
   új `read_page` → következő adag.
3. A 2. lépésben a **Google előkitölti az új hirdetést az ad group 1 meglévő
   RSA-jából** (mind a 15 címsor + 4 leírás). Tehát nem beírni kell, hanem
   **átírni**. Vigyázz a duplikátumra: az 1. pozícióba beírt szöveg ütközhet egy
   lentebbi, eredeti címsorral — a Google nem engedi két egyforma címsort.
4. A **Végső URL** a 2. lépésben `https://soulsilver.hu`-ra áll be. Át kell írni
   az aloldalra, különben minden hirdetés a főoldalra megy.
5. Mentés közben feljöhet a **„Erősítse meg, hogy Ön az"** személyazonosság-
   igazoló ablak. **Ezt a usernek kell elvégeznie**, Claude nem hitelesít
   helyette. 2026-09-13 után nem lehet kihagyni („Kihagyás" gomb eltűnik) —
   érdemes előbb elvégezni, különben blokkolja a mentéseket.
6. A „Keresési kifejezés egyeztetése (BÉTA)" maradjon **„Csak a kulcsszavak és
   az egyezési típusok használata"** — ez tiltja a széles automatikus
   kiterjesztést, ami a 4 000 Ft/nap büdzsénél pénzt égetne.

### Ellenőrzés a végén

- A hirdetéscsoport-lista alján a számláló `4/1–4.` legyen (1. hirdetéscsoport +
  a 3 niche).
- Minden új hirdetésnél nézd meg a **Hirdetés ereje** értékét. A cél legalább
  „Jó". Az előző körben az emelte ki a „Gyenge"-ből, hogy **az ad group saját
  kulcsszavai belekerültek a címsorokba** — a fenti listák ezt már tartalmazzák.
- A landing oldalak élnek (2026-09-11-én mind a 3-ra 200-as válasz jött):
  `/ugyfelszerzes.html`, `/fogaszati-marketing.html`, `/epitoipari-marketing.html`.

---

## 2. Kreatív: a rendőrös STOP-tábla kép NEM használható

A felajánlott AI-kép (magyar rendőr egyenruhában, „RENDŐRSÉG" feliratos autó,
magyar címer, kezében „STOP ÉPÍTŐIPAROSOK! ÜGYFÉLSZERZÉS ITT." tábla)
**nem mehet ki hirdetésbe**:

1. **Jogi:** állami jelkép (címer) és rendőrségi jelzés kereskedelmi reklámban —
   engedélyköteles, reklámcélra gyakorlatilag nem adják meg.
2. **Hirdetéspolitika:** a Google Ads és a Meta is tiltja a hatósági kapcsolatot
   vagy állami jóváhagyást sugalló kreatívot (Google: „Megtévesztés"). Ismételt
   elutasításnál fiókfelfüggesztés — a most induló fióknál ez nagy kockázat.
3. **Üzenet:** rendőr + STOP = *megállítottak, baj van*. Az építőipari
   vállalkozó rossz érzéssel kattint.

**Javasolt csere ugyanazzal a vizuális erővel:** **művezető sárga mellényben és
sisakban**, kezében ugyanaz a piros nyolcszögletű STOP-tábla, állványos épület
előtt. A tábla szövege maradhat szó szerint. Nulla jogi expozíció, és a
célközönség magára ismer, nem a hatóságra.

---

## 3. ÁSZF: 3 kitöltendő pont maradt

`aszf.html`-ben még placeholder van. A user 2026-09-11-én azt kérte, erre
később térjünk vissza. Ami kell:

- **fizetési feltételek** (előre / utólag, fizetési határidő napokban, késedelmi
  kamat)
- **bankszámlaszám** (Bartek Dávid e.v.)
- **lemondási / elállási feltételek** (mennyi idővel előre, mi a díj)

Ha kitöltöd, **ugyanazt a hibát ne csináld**, amit korábban: a fordítási
szótárak kulcsai a **teljes belső HTML-re** egyeznek, így a placeholder
kitöltése elnémítja a fordítást. Minden módosított sorhoz **új kulcs/érték párt
kell felvenni** ide:
`tools/i18n/translations/{en,de,es}/{_kozos,legal1,legal2}.json`,
majd `python tools/i18n/i18n_build.py`.

Már kitöltött, valós cégadat (ezt ne írd át):

| Mező | Érték |
|---|---|
| Név | Bartek Dávid e.v. |
| Székhely | Regiposta utca 10, 2481 Velence |
| Adószám | 91718613-1-27 |
| Nyilvántartási szám | 61773671 |
| Képviselő | Bartek Dávid |
| Telefon | +36 20 260 7810 |

---

## 4. Referencia-média: 20 fájl hiányzik

`img/ref/`-ben **1 kész a 21-ből**: `sara-landry.jpg` (106 KB, 1600×900) és
`sara-landry.mp4` (17,6 MB). A `tools/audit_static.py` ezért jelzi a
`referenciak.html` hiányzó képeit — ez a **jelenleg egyetlen ismert audit-hiba**,
és szándékos.

Bevált munkafolyamat:

1. Claude megkeresi a Drive-ban a kész fájlt (Google Drive MCP).
2. **Nagy videót a user tölt le kézzel** a `Downloads` mappába — a
   `download_file_content` már egy 99 KB-os JPEG-nél is túllépi a token-limitet.
3. Claude tömöríti. A Sara Landry-nál használt, jónak bizonyult beállítás
   (127 MB `.mov` → 17,6 MB `.mp4`):

```bash
ffmpeg -i "YOUTOPIAxSARA LANDRY.mov" -vf "scale=1280:-2,fps=30" -c:v libx264 -preset slow -crf 23 -pix_fmt yuv420p -c:a aac -b:a 128k -movflags +faststart out.mp4
```

---

## 5. Kisebb, még nyitott tételek

- **A szolgáltatás-aloldalak CTA-gombjai még `mailto:`-ra mennek** (pl.
  `ppc-hirdeteskezeles.html`). Ugyanaz a néma hiba, amit az `arak.html`-en már
  javítottunk: ha a látogatónak nincs beállított levelezőprogramja, a gomb
  **semmit nem tesz**. Javítás: `mailto:` → `index.html#kapcsolat`. Az
  „Írok e-mailt" jellegű másodlagos gomb maradhat `mailto:`.
- **Google Ads eszközök:** a Promóció, valamint a Kép / Vállalati logó asset
  nincs feltöltve. Nem blokkoló, de emeli a hirdetés erejét.
- **Karbantartás mód** most **KI** van (`ed204d5`). Bekapcsolás: a
  dokumentumgyökérbe fel kell tenni egy `.maintenance` nevű üres fájlt.
  Előnézeti bypass-token: `?preview=MF5sJsPnofjyXwhDjodWxtOR` (1440 percig
  érvényes sütit állít). A szabályok az `.htaccess`-ben vannak, a kiszolgált
  oldal a `karbantartas.html`.

---

## 6. Fontos repo-szabályok (hogy ne legyen újra baj)

- **Soha ne szerkeszd közvetlenül az `/en/`, `/de/`, `/es/` HTML-eket.** Ezek
  generáltak. A magyar forrást írd, a szótárat frissítsd, majd:
  `python tools/i18n/i18n_build.py`.
- A `karbantartas.html` és a 3 niche landing oldal **szándékosan kimarad** a
  fordításból (`NEM_FORDITANDO` az `i18n_build.py`-ban).
- Minden módosítás után: `python tools/audit_static.py`. Elfogadható kimenet
  jelenleg **csak** a `referenciak.html` hiányzó `img/ref/*` fájljai.
- **Nincs staging.** A Hostinger a `main` branch minden pushát azonnal élesíti a
  soulsilver.hu-n. Commitolni bátran lehet, **pusholni csak akkor, ha a user
  kéri**.
