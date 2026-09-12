# Ami hátra van — átadási jegyzet

Állapot dátuma: **2026-09-12**.

---

## 0. A LEGFONTOSABB: nem a mérés a szűk keresztmetszet, hanem a forgalom

A kampány **30 nap alatt 145 megjelenítést** kapott, és a Google maga írja ki a
diagnosztikában: *„A kampány az elmúlt héten nem tudta elkölteni az átlagos napi
költségkeret nagy részét."* 145 megjelenítésből néhány kattintás lesz, abból
pedig statisztikailag nulla lead. **Amíg ez nem változik, semmilyen kreatív,
eszköz vagy konverziós finomhangolás nem fog leadet hozni** — ezt előbb kell
rendezni, mint bármi mást.

A „12 konverzió" sem valódi lead: a mérés szerint mind **2026. aug. 30–31-én**
keletkezett (akkor épült az űrlap, tehát teszt-beküldések), és azóta **egyetlen
tag-ping sem érkezett**. Google szövege: *„Conversion has not received tag pings
in the last 7 days."*

Amin érdemes dolgozni, sorrendben:
1. Miért korlátozott a megjelenés (ajánlattételi stratégia / licit / minőségi
   pontszám / túl szűk kulcsszavak).
2. Több releváns kulcsszó és szélesebb egyezés, kontrollált negatívokkal.
3. Csak ezután: kreatív és eszközök.

---

## 1. Google Ads: hirdetéscsoportok — KÉSZ (2026-09-11)

**Kampány:** „Marketing Ügynökség - Search - HU", `campaignId=24188369439`
**Fiók:** `ocid=7364900281`
**Létrehozás URL-je:**
`https://ads.google.com/aw/adgroups/new/search?campaignId=24188369439&ocid=7364900281`

### KÉSZ ✅ — mind a 3 niche hirdetéscsoport

2026-09-11-én mindhárom létrejött, RSA-val (15 címsor + 4 leírás) együtt elmentve.
A hirdetéscsoport-lista számlálója `4/1–4.`, mindegyik „Jogosult".

| Hirdetéscsoport | Végső URL | Megjelenítési útvonal |
|---|---|---|
| Ügyfélszerzés | `/ugyfelszerzes.html` | `/ugyfelszerzes` |
| Fogászati marketing | `/fogaszati-marketing.html` | `/fogaszati/marketing` |
| Építőipari marketing | `/epitoipari-marketing.html` | `/epitoipari/marketing` |

**Nyitott ellenőrzés:** a két új hirdetés a létrehozás után „Függőben" (jóváhagyásra
vár), ezért a **Hirdetés ereje** oszlop még üres. Néhány óra múlva vissza kell
nézni a `https://ads.google.com/aw/ads?campaignId=24188369439&ocid=7364900281`
oldalon, hogy legalább „Jó" lett-e. Ha „Gyenge" marad, a leggyorsabb emelés:
több ad group-kulcsszó beleírása a címsorokba.

### Az eredeti szövegek (ha újra kellenek) — „Fogászati marketing"

- **Végső URL:** `https://soulsilver.hu/fogaszati-marketing.html`
- **Megjelenítési útvonal:** `fogaszati` / `marketing` (mezőnként max. 15 kar.)
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

### Az eredeti szövegek — „Építőipari marketing"

- **Végső URL:** `https://soulsilver.hu/epitoipari-marketing.html`
- **Megjelenítési útvonal:** `epitoipari` / `marketing` (mezőnként max. 15 kar.)
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
7. **A megjelenítési útvonal mezője legfeljebb 15 karakter**, útvonalanként.
   A `fogaszati-marketing` (19) és az `epitoipari-marketing` (20) ezért nem fér
   bele — a mentés néma hibával elbukik, és a wizard visszadobja a hirdetés-
   szerkesztőbe („Ez az érték túl hosszú"). Megoldás: két mezőre bontani
   (`fogaszati` + `marketing`). Ez semmit nem ront, a megjelenő URL
   `soulsilver.hu/fogaszati/marketing` lesz.
8. A 2. lépés végén a **„Mentés és folytatás" gomb gyakran a látható terület
   alatt van**, és az egérgörgő nem mindig görgeti a belső konténert. Bevált
   fogás: `javascript_tool`-lal `scrollIntoView({block:'center'})`, majd a
   visszakapott `getBoundingClientRect()`-ből számolt koordinátára kattintani
   (a CSS-koordinátát **1,225-tel kell szorozni**, ez a screenshot-frame
   aránya). A hirdetéskártyát előbb a **„Kész"** gombbal kell lezárni.

### Ellenőrzés

- ✅ A hirdetéscsoport-lista alján a számláló `4/1–4.` (1. hirdetéscsoport +
  a 3 niche), mind „Jogosult".
- ⏳ **Hirdetés ereje**: a két új hirdetés még „Függőben", az érték üres. A cél
  legalább „Jó". Az előző körben az emelte ki a „Gyenge"-ből, hogy **az ad group
  saját kulcsszavai belekerültek a címsorokba** — a fenti listák ezt már
  tartalmazzák. A wizardban a mérő végig „Gyenge"-t mutatott, de ott a
  „népszerű kulcsszavak" feltétel új hirdetéscsoportnál forgalmi adat híján
  nem tud teljesülni — ez nem azonos a mentés utáni, valódi értékkel.
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


---

## 7. Konverziómérés — javítva (2026-09-12)

**Volt:** a „Potenciális ügyfél űrlapjának beküldése" cél **„Beavatkozást
igényel"** állapotban állt, mert az azonos nevű konverziós művelet
**„Rosszul beállított"** volt (utolsó rögzített konverzió: 2026. aug. 31.,
azóta nincs tag-ping). Ráadásul **két elsődleges művelet** mérte ugyanazt az
űrlapbeküldést, tehát minden lead duplán számított a Smart Biddingnek.

**Most:** a célon belül a jól működő **„Kapcsolatfelvételi űrlap"** (Aktív,
egy konverzió / kattintás, 90 nap) maradt az **egyetlen elsődleges** művelet,
a hibás auto-művelet **másodlagos** (csak megfigyelés) lett. Mindkét fiókszintű
cél állapota most **Aktív**.

### Ami még nyitott a konverzióknál

- **Két „WhatsApp kattintás" művelet „Eltávolítva" állapotban**, mégis
  elsődlegesnek jelölve. Az `app.js` a `HlTICL_g_OscEPLkpr9A` címkére küld —
  ellenőrizni kell, hogy ez az **Aktív** „WhatsApp kattintás (1)" művelethez
  tartozik-e, vagy az egyik eltávolítotthoz. Ha az utóbbi, a WhatsApp-
  kattintások a semmibe mennek.
- **Kibővített konverziók:** a Google jelzi, hogy *„Beállítási problémákat
  találtunk"*. Vagy rendbe kell tenni, vagy kikapcsolni.
- Az `app.js` saját kommentje szerint a kattintás-szándék **másodlagos** kellene
  legyen, de a „WhatsApp kattintás (1)" jelenleg **elsődleges**. Érdemes
  másodlagosra tenni, hogy a licitálás a tényleges űrlapbeküldésre menjen.

---

## 8. A hirdetési kép — feltöltés elakadt (2026-09-12)

A művezetős STOP-táblás kép elkészült és be van vágva a `img/ads/` mappába
(1200×1200, 1200×628, 1080×1350 + mester). A Google Ads képeszközhöz való
feltöltése **nem sikerült**: a két fájl felkerült a kampány képtárába
(2/20), de a mentés `„Hiba történt. Kérjük, próbálja újra később."` üzenettel
elbukott, és a „Mentés" gomb végig inaktív maradt.

A valószínű ok ugyanaz, amit a Google a feltöltő űrlapon ki is ír:

> „A képeknek meg kell felelniük a Google Ads minőségi követelményeinek.
> **Emblémafedvények, szövegfedvények**, GIF-ek, valamint homályos és rosszul
> körbevágott képek **nem használhatók**."

A képen mindkettő rajta van: a tábla nagy feliratos felülete és a mellényen a
SOULSILVER logó. (Vitatható, hogy egy *lefényképezett* tábla „szövegfedvény"-e,
de a mentés következetesen elbukott.)

**Ahol viszont működni fog, és érdemes használni:**
- **Meta (Facebook/Instagram)** — ott a szövegfedvény megengedett; az 1080×1350
  vágat készen van erre.
- Az `epitoipari-marketing.html` hero-képeként.
- Organikus közösségi posztokhoz.

**Nyitott döntés a usernek:** a Google feltöltés közben felkínálta az
**AI-címkézést** („Elemek áttekintése"). A kép AI-generált, és egyes régiókban
jogszabály írhatja elő a megjelölést. Ez üzleti/jogi döntés — nem állítottam be.

---

## 9. Űrlap-hardening — kész (2026-09-12)

Lásd a `2474609` commitot. Az idő-csapda eddig a hiányzó/0 `ts` értéket is
eldobta, pedig az nem bot-jel, hanem az alapértelmezett mezőérték — ha az
`app.js` nem futott le, egy valódi érdeklődő űrlapja nyom nélkül eltűnt, ő meg
a főoldalon kötött ki abban a hitben, hogy elküldte. Most fail-open, és a
hibás beküldés visszamegy a saját landing oldalára látható hibaüzenettel.
A csendes eldobások a `lead-drop.log`-ba kerülnek (gitignore-olt).

---

## 10. Lead form eszköz — KÉSZ (2026-09-12)

A „Potenciális ügyfél űrlapja" eszköz **elmentve**, kampányszinten a
`Marketing Ügynökség - Search - HU` kampányon. Állapot: **Függőben /
Ellenőrzés alatt**, hozzáadva 2026. szept. 12. 14:31.

A mentést egy ÁSZF-elfogadás blokkolta („A potenciális ügyfelekhez tartozó új
űrlapbővítmények létrehozásához fogadja el az Általános Szerződési
Feltételeket") — ezt **a user fogadta el**, Claude nem fogadhat el
szerződéses feltételt helyette. Ha új fiókban kell ilyet csinálni, erre
számítani kell.

Megnyitó URL:
`https://ads.google.com/aw/adextensions/new?campaignId=24188369439&ocid=7364900281&placeholderType=40&assetFieldType=17&legacy=false`

### A beállított tartalom (ha újra kell írni)

| Mező | Érték |
|---|---|
| Főcím (30) | Ügyfeleket szerzünk neked |
| Vállalkozás neve (25) | SOULSILVER Marketing |
| Leírás (200) | Ingyenes kapacitás-felmérés: megnézzük, hány új ügyfél fér még be hozzád, és mennyiért hozzuk őket. Fix célszám a szerződésben. |
| Kérdések | Teljes név, E-mail, Telefonszám (telefon-ellenőrzés KI — csökkentené a volument) |
| Egyéni kérdések nyelve | **magyar** (alapból angol volt!) |
| Adatvédelmi URL | https://soulsilver.hu/adatvedelem.html |
| Elküldési üzenet főcím (30) | Köszönjük! Hamarosan hívunk. |
| Elküldési üzenet leírás | 24 órán belül jelentkezünk telefonon vagy emailben. Addig is nézd meg, kiknek hoztunk már ügyfelet. |
| CTA a hirdetésen | **Ajánlat kérése** |
| CTA leírása (30) | Ingyenes kapacitás-felmérés |
| CTA URL (beküldés után) | https://soulsilver.hu/referenciak.html |

### Ami még nincs beállítva

- **Lead-kézbesítés.** Integráció nélkül a leadeket **kézzel kell letölteni**
  CSV-ben a Google Adsből, és **30 nap után törlődnek**. A űrlapon van
  „Potenciális ügyfelek exportálása" szekció: HubSpot / Google Sheets /
  Mailchimp / Salesforce / **webhook**. Egy webhook a soulsilver.hu-ra lenne a
  jó megoldás, hogy a lead azonnal emailben is megérkezzen.
- **Háttérkép** a lead formon (van rá mező) — ide esetleg befér a STOP-táblás
  kép, ha a képeszközbe nem megy át.

---

## 11. Google Ads UI — hibanapló (amibe belefutottunk)

Ezek konkrét, újra előforduló akadályok. Aki folytatja, ezekkel számoljon.

**Környezet**
1. **Ha a Chrome ablak kicsinyítve van, semmi nem működik**: `innerWidth` 0 lesz,
   a Google Ads virtualizált táblái **nem renderelnek semmit**, a screenshot
   `Cannot take screenshot with 0 width` hibával elszáll, és a `resize_window`
   sikert jelez, de nem csinál semmit. Ellenőrzés: `javascript_tool` →
   `innerWidth`. Megoldás: a usernek vissza kell állítania az ablakot.
2. **`Page.captureScreenshot timed out after 30000ms`** — a nehéz Ads oldalakon
   gyakori, nem valódi hiba. Várj 8 másodpercet és próbáld újra.
3. **`javascript_tool` blokkolódhat**: `[BLOCKED: Cookie/query string data]`,
   ha a kód tömegesen olvas `href`-eket vagy query stringet. Kerüld.

**URL-ek**
4. **404-et adnak** (ne használd): `/aw/conversions/summary`,
   `/aw/assets?campaignId=`, `/aw/assetsandextensions/associations`.
5. **Működnek**: `/aw/conversions/all`,
   `/aw/assetreport/associations?campaignId=…&assetType=…`,
   `/aw/adextensions/new?campaignId=…&placeholderType=…&assetFieldType=…`.
6. **placeholderType / assetFieldType párok**: Kép = **48 / 59**,
   Potenciális ügyfél űrlapja = **40 / 17**, Hívás = **2 / 42**.

**Konverziók**
7. **Az elsődleges/másodlagos NEM állítható** a konverziós lista „Elsődleges"
   szövegén — az csak tooltip-target, nem link. A sor **ceruza ikonja csak a
   nevet** szerkeszti. A tömeges „Szerkesztés" menü csak Engedélyezés /
   Eltávolítás. A **konverzió részletoldala csak olvasható**.
   **A működő útvonal:** Célok → Összegzés → „Cél szerkesztése" → a
   „Konverziós művelet optimalizálása" sor kibontása → soronkénti legördülő →
   Mentés.

**Űrlapok**
8. **Soha ne gépelj szabadon és ne nyomj `ctrl+a`-t** az Ads mezőibe: globális
   billentyűparancsot süt el (`G`+`Y` = Javaslatok) és elnavigál. Helyette
   `read_page` → `ref_N` → `form_input`.
9. **Legördülő opció kiválasztása:** a `find`-ból kapott `ref` kattintása gyakran
   nem fog. A képernyőkoordinátás kattintás igen — de **könnyen elcsúszik egy
   sorral**, ezért utána mindig ellenőrizd a kiválasztott értéket (nálunk
   „Ajánlat kérése" helyett „Bemutató kérése" lett elsőre).
10. **A címsor- és leíráslisták virtualizáltak**: egyszerre 4–9 mező van a
    DOM-ban. Ciklus: `form_input` a láthatókra → `scroll` → új `read_page`.

**Képeszköz**
11. A mentés következetesen `„Hiba történt. Kérjük, próbálja újra később."`
    hibával elbukott, és a képválasztó **Mentés gombja végig inaktív maradt**,
    hiába volt 2 kép feltöltve a kampány képtárába. Lásd a 8. pontot: a
    legvalószínűbb ok a szöveg-/embléma-fedvény tilalma.
