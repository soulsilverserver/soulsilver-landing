# Ami hátra van — átadási jegyzet

Állapot dátuma: **2026-09-12**.

---

## 0. A forgalmi fojtás — DIAGNOSZTIZÁLVA ÉS JAVÍTVA (2026-09-12)

### A mért állapot

| | |
|---|---|
| Napi keret | 4 000 Ft → 30 napra 120 000 Ft |
| Ebből elköltve | **8 870 Ft = 7%** |
| Ajánlattételi stratégia (volt) | **Cél CPA, 375,55 Ft** |
| Tényleges költség/konverzió | **634 Ft** |
| Megjelenítés / 30 nap | 227 |
| Kattintás / átl. CPC | 44 / 202 Ft |

**Nem a költségkeret volt a szűk keresztmetszet** — a pénz 93%-a ott maradt.

### Az ok

A Cél CPA 376 Ft volt, a valóság 634 Ft — a tényleges konverziós költség
**69%-kal a cél fölött**. Ilyenkor a Smart Bidding csak a legolcsóbb aukciókba
száll be, a többit kihagyja. Innen a 227 megjelenítés olyan általános egyezésű
kulcsszavakon, mint „marketing cég" és „weboldal marketing", amiknek bőven van
keresésük.

**A csapda:** ezt a 376 Ft-os célt arra a 14 konverzióra kalibrálta a rendszer,
amiről kiderült, hogy nem valódi lead (aug. 30–31-i teszt-beküldések). A licit
tehát egy kitalált számhoz igazodott.

### Amit átállítottunk (2026-09-12)

**Cél CPA → „Kattintások maximalizálása", max CPC-korlát 400 Ft.**

Indok: a Cél CPA gépi tanulásra épül, ahhoz valódi konverziós adat kell — most
pontosan **egy** ilyen van („Kapcsolatfelvételi űrlap"). Egy adatpontból nem
lehet tanulni. Előbb forgalom kell, abból valódi űrlapbeküldés.

**Visszatérési terv:** 15–30 valódi konverzió után vissza Cél CPA-ra, reális,
**3 000–8 000 Ft** körüli célszámmal (ennyi egy ügynökségi lead, nem 376 Ft).
A Google a váltáskor figyelmeztet, hogy „több konverziót érhet el, ha a
konverziókra koncentrál" — ez most szándékosan figyelmen kívül hagyva, mert
nincs mire optimalizálni.

### A 3 új hirdetéscsoport: 0 megjelenítés

Mindhárom **„Jogosult"** (jóváhagyva, nincs elutasítás), de nulla forgalom.
Kifejezés- és pontos egyezésű kulcsszavaik havi 10–50 keresésű kifejezésekre
mennek. A fojtott licittel együtt ez strukturálisan nulla. Ha a
licit-átállítás után sem indulnak be, a következő lépés a tágabb egyezés
vagy tágabb kifejezések — kontrollált negatívokkal.

### Keresési kifejezések + kizáró kulcsszavak — KÉSZ (2026-09-12)

A kampány teljes élettartama (2025. júl. 6. – 2026. szept. 12.): **47 kattintás,
231 megjelenítés, 9 046 Ft**, 81 különböző keresési kifejezés.

**A fő felfedezés: a pénz kétharmada angol nyelvű, általános kifejezésekre ment.**

| Keresési kifejezés | Katt. | Költség |
|---|---|---|
| online promotion | 10 | 1 485 Ft |
| marketing online | 5 | 784 Ft |
| e business marketing | 4 | 532 Ft |
| brand promotion | 3 | 266 Ft |
| online shopping / online store marketing | 4 | 611 Ft |
| további angol általános | ~5 | ~860 Ft |
| **összesen** | **~31** | **~4 540 Ft — a költés 64%-a** |

Egyik sem egy magyar KKV-tulajdonos keresése. Ehhez jött 396 Ft egy
**álláskeresőtől** („marketing ügynökség állás”), plusz a tanuló-típusú
keresések (swot elemzés, marketing terv minta, marketing kampány lépései,
buyer persona, keresőoptimalizálás tanfolyam, social media manager képzés).

**A célközönség mindössze 2 keresésben jelent meg:** „marketing ceg” és
„marketing ügynökség” — 1-1 kattintás, 399 és 390 Ft. A valódi piac tehát
pont a most beállított **400 Ft-os max CPC** környékén van. Ez utólag
igazolja a licit-átállítást.

**Megjegyzés a 14 konverzióról:** mind a 14 ezekre a kifejezésekre oszlik el
(online promotion 4, marketing online 3, marketing programs 2, online store
marketing 2, digital promotion 1, platform marketing 1, **marketing ügynökség
állás 1**). Mivel a postaládába egyetlen valódi megkeresés sem érkezett,
ezek nem valódi leadek. További gyanú-jel: több sor CTR-je 100% fölött van
(brand promotion 150%, online marketing 200%), ami érvénytelen forgalomra
utalhat.

**Beállítva:** 84 kizáró kulcsszó kampányszinten, kifejezésegyezéssel.
A teljes lista: `docs/ads-kizaro-kulcsszavak.txt`. Kategóriák: álláskeresők,
tanulók/DIY/elmélet, ingyenkeresők, követő- és megtekintés-vadászat, angol
nyelvű általános kifejezések, más cégek márkanevei, nem-szolgáltatás.

**Amit szándékosan NEM tiltottunk le,** mert tényleg van rá szolgáltatás:
`weboldal készítés`, `közösségi média`, `branding`, `seo`, `tiktok`,
`instagram`, `videó`. Szintén kimaradt a `munka` (mert a „több munka
építőiparban” típusú keresés pont a célügyfélé) és az `olcsó` (az
árérzékeny érdeklődő is érdeklődő).

**Korlát, amivel számolni kell:** a kizáró kulcsszavak a magyar toldalékolt
alakokat **nem** fogják meg — a „minta” nem blokkolja a „mintát”, ezért
került fel külön az „állás” és az „állások” is. Egy hónap múlva újra át
kell nézni a keresési kifejezéseket és pótolni a toldalékos változatokat.

### Keresési partnerek kikapcsolva (2026-09-12) — ez volt a legnagyobb tétel

A fiókdiagnosztika kapcsán megnéztük a Hálózatok bontást, és kiderült:

| Hálózat | Kattintás | Költség | Átl. CPC |
|---|---|---|---|
| Google-keresés | 20,5% (9) | 39,5% (3 504 Ft) | 390 Ft |
| **Keresési partnerek webhelyei** | **79,5% (35)** | **60,5% (5 366 Ft)** | 153 Ft |

**A kattintások négyötöde nem a Google keresőből jött**, hanem harmadik feles
partneroldalakról. Ez egyben magyarázat mindenre, ami eddig gyanús volt: az
angol „online promotion" típusú kifejezésekre, a 100% fölötti CTR-ekre, a 14
fantom-konverzióra és arra, hogy 47 kattintásból nulla valódi lead lett.

**Beállítva:** „Google keresési partnerek szerepeltetése" kikapcsolva
(Kampánybeállítások → Hálózatok). A Google figyelmeztetett, hogy „a legtöbb
hirdető szerepelteti" — a fenti adat ismeretében szándékosan figyelmen kívül
hagyva. Friss betöltésen ellenőrizve: a Hálózatok sor már csak
**„Google Keresési Hálózat"**.

Fontos: a Hálózatok **riport-kártya** továbbra is mutatja a 20,5/79,5
megoszlást — az a múltbeli 30 nap adata, nem a beállítás.

### Bővített konverziók kikapcsolva (2026-09-12)

A diagnosztika ezt jelezte hibaként. Az ok: a bővített konverzió akkor
működik, ha a konverzió pillanatában kap ügyféladatot — a `koszonjuk.html`-en
viszont nincs semmi, az űrlap tartalma nem utazik át a redirecten, és nincs
mit kiolvasni sem. Be volt kapcsolva, de nulla lefedettséggel.

**Döntés (a felhasználóé): nem adunk adatot a Google-nek.** Az alternatíva az
lett volna, hogy az e-mailt átvisszük a köszönőoldalra és elküldjük — egy
nappal a Consent Mode bevezetése után ez visszalépés lett volna.

Kikapcsolva a `Célok → Konverziók → Konverzióbeállítások` alatt **mindkettő**:

- „Bővített konverziók" → **Még nincs beállítva**
- „A potenciális ügyfelek szerzésére irányuló kibővített konverziók" → **Még nincs beállítva**

A Google jelezte, hogy ettől az offline lead-mérés a **GCLID**-re vált
személyes adat helyett — ez pont a kívánt irány.

**A „+1 probléma" a diagnosztikában** („Az új ajánlattételi stratégia tanulási
fázisban van") **nem hiba**: a saját licitváltásunk okozza, 1–2 hét alatt
magától elmúlik. Ettől „Használható (korlátozott)" a kampány állapota.

**A diagnosztika nem valós idejű** — a bővített konverziós figyelmeztetés még
egy napig látszódhat a kikapcsolás után is.

### Eszközmegoszlás — a mobil dominál

Kattintások: **mobil 84,1%**, számítógép 13,6%, táblagép 2,3%.

### Mobil hero javítva (2026-09-12)

**A hiba:** 375 px-es nézetben az építőipari hero fotóján csak a bal széli
daru látszott — a munkás és a STOP-tábla teljesen kimaradt. A kattintások
84%-a mobilról jön, tehát ezt látta a látogatók többsége.

**A mérés, ami megmagyarázta:** a hero doboz mobilon **375×1304**, vagyis
0.29 arányú, a fotó viszont 1.5. A `background-size:cover` emiatt a kép
szélességének mindössze **19%-át** mutatja (1600-ból 307 px), és a
`background-position:left center` mellett ez pont a daru volt.

**Első kísérlet (átmeneti):** `background-position:29% center` mobilon — ez
a munkás arcára állította a látható sávot. Működött, de a STOP-tábla
kimaradt, és a szöveg a világos mellényre esett.

**A végleges megoldás: saját álló kép mobilra, és NEM `cover`-rel.**

- `img/hero-epitoipari-mobil.jpg` — ChatGPT-vel generált 1024×1536-os álló
  felvétel, alá **sötét kiegészítés toldva 1024×2100-ra**. A fotó alja eleve
  majdnem fekete (fényerő 11–19), a kiegészítés pedig pontosan a
  `.phero-bg` háttérszínére (`#0A0C0F`) van kikeverve — a varratnál mért
  fényerő-ugrás **0,38**, vagyis láthatatlan.
- `background-size:100% auto` + `center top` + **`no-repeat`**: a kép teljes
  szélességben, természetes magassággal ül a hero tetején, alatta a
  háttérszín viszi tovább a sötétet. **Egyáltalán nincs vágás** — a
  kompozíció pont az, amit a generátor adott. A `no-repeat` kötelező:
  enélkül a kép függőlegesen csempézne.
- `.phero--epitoipar .wrap{ padding-top:200px }` — a szöveg a kép alá kerül.
  375 px-en a munkás 305 px-ig tart; 136 px-es padding mellett a morzsamenü
  beleolvadt a sárga mellénybe.

| Nézet | Kép | Méretezés | Pozíció |
|---|---|---|---|
| Asztali (>900px) | `hero-epitoipari.jpg` (fekvő) | `cover`, `fixed` | `left center` |
| Mobil/tablet (≤900px) | `hero-epitoipari-mobil.jpg` (álló) | `100% auto`, `no-repeat` | `center top` |

**Két csapda, amibe belefutottunk:**

1. **Cascade.** A `.phero--epitoipar .wrap` szabálynak a `.phero .wrap`
   **után** kell állnia a médialekérdezésen belül — azonos a specificitás,
   tehát a sorrend dönt. Először elé került, és némán hatástalan maradt.
2. **A padding mértéke.** 320 px-nél a CTA gomb 894 px-re csúszott, a
   hajtás viszont 812 — a gomb eltűnt a képernyőről. 200 px az egyensúly:
   a morzsamenü 328-nál már a mellény alatt van, a gomb pedig 774-nél még
   látszik.

**Amit elvetettünk:** a fekvő fotóból portré vágás — az alany 1169 px széles
az 1600-ból, ez semmilyen álló képarányba nem fér bele az 1067 px-es
magasságból. A meglévő 4:5-ös Meta-vágás is pont ezért csonkolja a tábla
szövegét.

**Ami nyitva maradt:** a hero **1368 px magas** egy 812 px-es kijelzőn. Ez
önmagában UX-kérdés (a következő szekcióig másfél képernyőnyi görgetés), de
már layout-átszabás, nem képvágás.

### Ami még nyitott a forgalomnál

1. **Keresési kifejezések az új hirdetéscsoportokra** — jelenleg mind a 81
   kifejezés az „1. hirdetéscsoport”-hoz tartozik; a három új niche csoport
   nulla forgalmat kapott.
2. Földrajzi célzás és időzítés — nem ellenőrizve.
3. Egy hét múlva ellenőrizni: a 400 Ft-os licit elindította-e a forgalmat,
   és a kizárások után javul-e a kifejezések minősége.

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

## 8. A hirdetési kép — a fiók nem tud képeszközt fogadni (2026-09-12)

A művezetős STOP-táblás kép elkészült és be van vágva az `img/ads/` mappába
(1200×1200, 1200×628, 1080×1350 + mester). A Google Ads képeszközbe **nem megy
be**, de nem a képpel van baj.

### Amit a diagnózis kizárt

A Google a feltöltésnél maga sorolta be a fájlokat, tehát elfogadta őket:

| Fájl | Google besorolása |
|---|---|
| `stop-epitoipar-1200x1200.jpg` | **Négyzet (1:1)** |
| `stop-epitoipar-1200x628.jpg` | **Vízszintes (1,91:1)** |

Méret, méretarány, fájlméret, formátum: mind rendben. A képek bekerülnek a
kampány képtárába és kiválaszthatók.

**Első buktató (megoldva):** a feltöltött kép nem automatikusan van
*kiválasztva*. Rá kell kattintani a bélyegképre a „Javasolt" / „Legutóbbi
eszközök" rácsban — csak ekkor ugrik a számláló `(1/20)`-ra és válik aktívvá
a picker „Mentés" gombja. Enélkül a gomb végig szürke marad, és úgy tűnik,
mintha a feltöltés bukott volna el.

### Ahol tényleg elhasal

A **végső mentésnél**, a szülő űrlap „Mentés" gombjánál. Hálózati szinten:
a `CampaignAssetService.Mutate` hívás **HTTP 200-at ad vissza**, tehát a kérés
átmegy, de a válasz hibát tartalmaz, és a felület csak ennyit ír ki:
`„Hiba történt. Kérjük, próbálja újra később."`

Reprodukálható **egyetlen, szabályos négyzetes képpel is**.

**A döntő jel:** a kampány eszköz-listájában (`/aw/assetreport/associations`)
**nincs is „Kép" szűrőcsempe**. Ott van a Vállalkozás neve, Vállalati logó,
Belső link, Főcím, Leírás, Szöveges felelősségkizárás, Kiemelés, Strukturált
kódrészlet, Hívás, Potenciális ügyfél űrlapja, Üzenet, Hely, Ár, Alkalmazás,
Promóció — **kép nincs**. A „+" menü felkínálja a képfeltöltő űrlapot
(`placeholderType=48&assetFieldType=59`), de a fiók nem tud képeszközt tárolni.

### A legvalószínűbb ok

**A fiók még nem jogosult képeszközre.** A Google a keresési kampányok
képeszközéhez fiókszintű előfeltételeket szab: a fiók kora, tiszta
szabálykövetési előzmény, és egy minimális összköltés-küszöb. Ez a fiók pár
hetes, és 30 nap alatt **6 212 Ft**-ot költött — ettől nagyságrendekkel van
elmaradva.

Ezt a küszöböt NEM láttuk a felületen kiírva, ez a Google dokumentált
feltétele. Ha biztosra kell menni: a Google Ads ügyfélszolgálata egy kérdéssel
megmondja, jogosult-e a fiók képeszközre.

### A szövegfedvény-szabály ettől függetlenül él

A feltöltő űrlap kiírja:

> „A képeknek meg kell felelniük a Google Ads minőségi követelményeinek.
> **Emblémafedvények, szövegfedvények**, GIF-ek, valamint homályos és rosszul
> körbevágott képek **nem használhatók**."

A képen mindkettő rajta van: a tábla nagy feliratos felülete és a mellényen a
SOULSILVER logó. Vitatható, hogy egy *lefényképezett* tábla „szövegfedvény"-e,
de ha a fiók később jogosulttá válik, számítani kell elutasításra.

**Ahol viszont működni fog, és érdemes használni:**
- **Meta (Facebook/Instagram)** — ott a szövegfedvény megengedett; az
  `img/ads/stop-epitoipar-1080x1350.jpg` vágat készen van erre.
- Az `epitoipari-marketing.html` hero-képeként.
- Organikus közösségi posztokhoz.
- A **lead form háttérképeként** (van rá mező a lead form űrlapon) — ez még
  nincs kipróbálva, és más eszköztípus, tehát más elbírálás alá eshet.

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

### Végpontos teszt (2026-09-12) — a mérés bizonyítottan működik

Valódi beküldés az építőipari landingről: átirányítás `/koszonjuk.html`-re
(nem `?hiba=`), a `lead-drop.log` létre sem jött (semmit nem ettek meg a
szűrők), a `forras` mező `epitoipari-marketing`, és a `dataLayer`-ben
elsült a `conversion` a helyes címkével:
`AW-17312625266/i8xVCJzouOscEPLkpr9A`.

**Figyelmeztetés a mérési adatokra:** ez a teszt 1 nem valódi konverziót
adott a fiókhoz (a korábbi 14 mellé).

**Csapda, amibe belefutottunk:** a böngészőpanel hálózati naplója egyetlen
Google-kérést sem mutatott, és elsőre úgy tűnt, a konverzió nem sül el.
A panel egyszerűen nem rögzíti a külső domainek kéréseit — a `dataLayer`-t
kell nézni, nem a network tabot.

## 9b. Consent Mode v2 — KÉSZ (2026-09-12)

**A hiba:** a cookie-sáv látszólag hozzájárulást kezelt, de a Google-mérést
nem gátolta. A fenti teszt során az **„Elutasítom"-ra kattintva is elsült**
a lead-konverzió. GDPR-kockázat, egy marketingügynökség saját oldalán
különösen kínos.

**A megoldás:** Consent Mode v2 mind az 55 mért oldalon (16 magyar forrás +
39 generált fordítás, `tools/i18n/i18n_build.py`-jal újraépítve).

- A fejléc-snippetben `consent default` **minden tiltva**, `wait_for_update: 500`.
  Ez a `config` **előtt** fut — különben a tag már a tiltás előtt sütizne.
- A korábbi döntés visszaállítása is a **fejlécben** van, nem az app.js-ben:
  egy visszatérő, elfogadó látogatónál különben a mérés az app.js
  betöltődéséig tiltva maradna, és a gyors konverziók elvesznének.
- Az `app.js` `consentJelzes()`-e a sáv válaszára küld `consent update`-et.

**Ellenőrizve élesben, mindhárom eset:**

| Eset | `google_tag_data.ics` |
|---|---|
| Nincs döntés | minden `denied` |
| „Elutasítom" | minden `update=false` + kimegy a `consent update` |
| „Elfogadom" | minden `update=true` |
| Visszatérő elfogadó | sorrend: `consent default` → `consent update` → `js` → `config` → `conversion` |

**Ára, amivel számolni kell:** aki elutasítja a sütiket, annak a konverziója
nem mérhető. A Google konverzió-modellezése ezt részben pótolná, de ahhoz
sokkal nagyobb forgalom kell, mint ami most van. Vagyis a mért konverziószám
ezentúl **alulbecsül** — ez nem hiba, hanem a jogszerű működés ára.

**Cache-csapda:** a böngésző az `app.js`-t sokáig cache-eli. Az első
elutasítás-teszt azért látszott hibásnak, mert a fül még a régi app.js-t
futtatta. Ellenőrzésnél `fetch('/app.js',{cache:'reload'})` után újratölteni.

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

### Lead-kézbesítés — ÉLES (2026-09-12)

**Az eszköz „Jogosult", a webhook elmentve és végponttól végpontig tesztelve.**
Minden lead-form beküldés azonnal e-mailben érkezik; nem kell CSV-t letölteni.

Miért kell: integráció nélkül a leadeket kézzel kell CSV-ben letölteni, és a
Google **30 nap után törli** őket. Senki nem kap értesítést.

**Elkészült:** `lead-webhook.php` — fogadja a Google POST-ját, megosztott
kulccsal hitelesít (`hash_equals`), emailt küld (Resend, tartalék `mail()`),
és még a küldés ELŐTT naplóz a `lead-webhook.log`-ba, hogy egy sikertelen
email se jelentsen elveszett leadet.

**Beállítás (ha újra kellene):**

1. Push → a Hostinger automatikusan élesíti a `lead-webhook.php`-t.
2. `'google_lead_key' => '<kulcs>',` a szerver `config.php`-jába.
   **A kulcs sehol nincs a repóban** (az publikus), csak a `config.php`-ban és
   a Google Ads mezőjében él. Új kulcs mindkét helyre egyszerre:
   `php -r "echo bin2hex(random_bytes(16));"` (max. 50 karakter).
   Kulcs nélkül a végpont **mindenre 401-et ad** — ez szándékos.
3. Ads: Eszközök → az eszköz ceruza ikonja → „Potenciális ügyfelek
   exportálása" → „Egyéb adatintegrálási opciók" → Webhook-URL + Kulcs →
   **Tesztadatok küldése** → Mentés.
   URL: `https://soulsilver.hu/lead-webhook.php`

**Buktató:** a Google **nem enged menteni**, amíg a tesztadat-küldés
sikeresen le nem fut („Küldjön tesztadatokat a webhook beállításának
ellenőrzése érdekében"). Ezért az 1. és 2. lépés kötelezően előbb jön.

**Ellenőrzés (2026-09-12, mind lefutott):**

| Teszt | Eredmény |
|---|---|
| `GET /lead-webhook.php` | 405 (csak POST) |
| POST hibás JSON-nal | 400 |
| POST rossz kulccsal | 401 |
| POST jó kulccsal, `is_test` | **200** `{"status":"ok"}` |
| Ads „Tesztadatok küldése" | **„Tesztadatok elküldve."** |
| Mentés utáni újratöltés | URL 38 kar. + kulcs 32 kar. a helyén |

**Fontos a jövőre:** a `bin/setup-keys.sh` nulláról írja újra a `config.php`-t.
Korábban nem ismerte a `google_lead_key`-t, tehát egy futtatás **letörölte
volna** — és a leadek némán elálltak volna. Most megőrzi a meglévőt, és csak
akkor generál újat, ha még nincs (a képernyőre is kiírja).

**Hiba, amit a rebase fogott el:** a `lead-webhook.php` először a `contact.php`
**régi** config-betöltőjét másolta (első LÉTEZŐ fájl). Egy párhuzamos session
épp akkor javította ezt `ss_config()`-ra (első HASZNÁLHATÓ fájl), mert egy
ottfelejtett üres `../config.php` némán elnyomta a jót. A régi mintával a
webhook kulcs nélkül maradt volna → 401 minden leadre. Most a közös
`ss_config()`-ot használja.
— 2026-09-12-én még 404 volt.

### A leadek kézi letöltése (CSV)

Eszközök → Eszközök → a lead form sorában, az eszköz neve alatt **két link**:

- **CSV** — a nyers leadek (név, email, telefon, válaszok).
- **CSV CRM-nél** — ugyanaz + `gclid`, offline konverzió-importáláshoz.

Közvetlen URL:
`https://ads.google.com/aw/assetreport/associations?ocid=7364900281&campaignId=24188369439&assetType=17`

### Ami még nincs beállítva

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
11. A képválasztó **„Mentés" gombja addig szürke marad, amíg rá nem kattintasz
    a bélyegképre** — a feltöltés önmagában nem jelent kiválasztást. A számláló
    `(0/20)` → `(1/20)` váltása az árulkodó jel.
12. Ha a bélyegkép ki van választva és a szülő űrlap mégis
    `„Hiba történt. Kérjük, próbálja újra később."`-t ír: a
    `CampaignAssetService.Mutate` HTTP **200**-at ad vissza hibás payloaddal.
    Ilyenkor NE a képet kezdd cserélgetni — nézd meg, van-e egyáltalán „Kép"
    szűrőcsempe a kampány eszköz-listájában. Ha nincs, a fiók nem jogosult
    képeszközre. Lásd a 8. pontot.
