# SOULSILVER — állapot és átvétel

Utolsó frissítés: 2026-09-13

> **Ha új sessionként veszed át:** ez a fájl a kiindulás. Olvasd végig a
> „Kezdd itt" és a „Csapdák" szakaszt, mielőtt bármit módosítasz — a Csapdák
> szakasz olyan dolgokat rögzít, amiket drága volt megtalálni.

---

## Kezdd itt

**Mi ez:** a SOULSILVER Marketing Agency weboldala. Statikus, többoldalas
HTML/CSS/JS, build lépés nélkül. 17 tartalmi oldal.

**Repo:** `soulsilverserver/soulsilver-landing`, branch `main`.
A git identitás a repóban `soulsilverserver` (NEM a globális fiók).

**Deploy:** Hostinger hPanel → GIT integráció, auto-deployment BE.
**Minden `main`-re pusholt commit azonnal élesedik** a soulsilver.hu-n.
Ezért: commitolj bátran, de **pushot csak akkor, ha a user kéri**.

**Helyi futtatás:**

```bash
python -m http.server 8643
```

A `.claude/launch.json` ezt `soulsilver-static` néven tartalmazza (port 8643).

**Első dolgod átvételkor:**

```bash
git log --oneline -15
git status --short
git log --oneline origin/main..HEAD    # van-e nem pusholt commit
git worktree list
```

Több session dolgozott már párhuzamosan ezen a repón, és egyszer ütköztek is.
Ne hidd el vakon, amit ez a fájl ír — ellenőrizd a repó tényleges állapotát.

---

## Referencia-média feltöltés — 2026-09-13-án lezárva

**Google Drive MCP ebben a sessionben nem állt rendelkezésre** (más szerver-ID,
nem csatlakozott). Helyette a Browser pane-nel bejelentkezés nélkül megnyitható
a megosztott Drive-mappa (https://drive.google.com/drive/folders/14JKD5Zm0nPWb_LGkFkLUiKpwxlyQXBod),
és a fájl `data-id` attribútuma (DOM-ból `document.querySelectorAll('[data-id]')`,
vagy a fájl-előnézet iframe-jének `drivesharing/clientmodel?id=...` URL-je)
adja a Drive fileId-t. Onnan a klasszikus `https://drive.google.com/uc?export=download&id=<ID>`
curl-lel közvetlenül letölthető — **~100 MB fölött** a Drive egy
"Virus scan warning" HTML-oldalt ad vissza `confirm` tokennel és `uuid`-vel;
ezeket kgodolva (`grep -o 'name="uuid" value="[^"]*"'`) a végleges URL
`https://drive.usercontent.google.com/download?id=<ID>&export=download&confirm=t&uuid=<UUID>`,
amit **ffmpeg/ffprobe közvetlenül streamel is** (nem kell előbb a teljes
nyers fájlt lementeni — a `mov`/`mp4` demuxer HTTP range-kéréssel eléri a
moov atomot). Ez sokkal gyorsabb, mint a base64 tool-result-fájlos workaround,
amit egy korábbi session használt (az a módszer emlékben marad, ha a Drive
MCP mégis elérhető lesz egy jövőbeli sessionben).

**Kész, éles** (`img/ref/`):
- `sara-landry.jpg` + `.mp4` — korábbi session töltötte fel.
- `barabas-bio-hungary.jpg` + `.mp4` — a poszter már megvolt; a videó a
  Weboldalak/`barabasbiohungaryweboldal.mov`-ból készült (1,79 GB nyers
  4K/120fps képernyőfelvétel a weboldalról → 1280px széles, 30fps, ~1,8 Mbps
  h264, hang nélkül, 27,7 MB, 2:09 perc).
- `epitoipar.jpg` + `.mp4` — a Videók/FQSD/`FQSD-FÜZÉRKAJATA.mov`-ból (123 MB
  nyers 1080p60 → 1280px, 30fps, ~2,2 Mbps, 14,8 MB, 53 mp). A poszter a
  Fénykép/FQSD/`FQSD-FÜZÉRKAJATA-Cover.jpg`.
- `foqusd.jpg` — csak kép (nincs hozzá videó); a `data-lb-type` `image`-re
  állítva a referenciak.html-ben.
- `hirdetesi-kreativok.jpg` — a Grafikák,Logók stb. mappából, `Tuzkar.png`
  (tűzkármentesítés hirdetési kreatív, SOULSILVER Építőipari Vállalkozás
  branddel — ugyanaz a SoulSilver-brand, nem csak a FoQuSD ügyfél).
- `gldn-street.jpg` — **csak kép, NEM videó.** A `GLDNSTREET.mov` fájl az
  Animáció mappában létezik, DE a tartalma **teljesen más, félrenevezett
  anyag** (egy "Pistike alapít egy családot" c. animált magyarázó videó
  "GYÖNGYÖS" felirattal, semmi köze GLDN Streethez). **Ne használd ezt a
  fájlt** — ha újra előkerül, ellenőrizd tartalmilag, mielőtt felteszed. A
  `referenciak.html`-ben a kártya emiatt `data-lb-type="image"`-re állítva,
  a szöveg "Animáció"-ról "Márkagrafika"-ra/közösségimédia-grafikára javítva
  (a korábbi szöveg mozgógrafikai animációt ígért, amink nincs).
- `parton-tali.jpg` — a Fénykép/PartonTaliDrónkép mappából (nem a korábban
  feljegyzett `_DSC0151.JPG`, hanem egy másik fájl ugyanabból a mappából:
  légi felvétel a Budai Várról/Vársétányról napnyugtakor, tömeggel).
- `rendezvenyek.jpg` — a Videók/NextLevel pres.HOTSPOT-Aug20 mappa
  `RNI-Films-IMG-*.jpg` képei közül a legjobban exponált táncparketti kép.

## Referencia-média — 2. kör (ugyanaznap este) — mind a 12+2 kártya éles

Kiderült, hogy a "Videók" mappának, amit korábban böngésztem, félrekattintva
tényleg a **Fénykép** mappa tartalmát adta vissza (a két mappa root-listája
vizuálisan egyforma pozícióban van). A valódi **Videók** mappa (Drive fileId
`13aam3jOr_0i2l9oX3yr_fxixLZUkNUFK`) sokkal rendezettebb, ügyfelenkénti
almappákkal: `Építőipar`, `Goat`, `GYM`, `Kowalsky Meg a Vega BP Park`,
`Rendezvény`, `Sara Landry after movie`, `Zenés Videó klip`. **Ha legközelebb
Drive-ban keresel, navigálj közvetlenül fileId-vel** (`drive.google.com/drive/folders/<id>`),
ne koordináta-alapú kattintással a listában — a lista virtualizált, és a
scrollpozíció session közben csúszhat.

**Ekkor pótolt/frissített kártyák:**
- `kowalsky-bp-park.jpg` + `.mp4` — a Videók/Rendezvény mappában talált
  `KOWA-bppark.mov` (193 MB, 4K30, 47,6 mp) → 1280px, ~2 Mbps, 12,9 MB.
  A korábbi feljegyzés (221 MB/755 MB/15 GB verziók) más fájlokra
  vonatkozott ugyanabban a mappában — ezt a kisebb, kész változatot nem
  vette észre a korábbi kör.
- `buzz-sneaker.jpg` + `.mp4` — az AI mappa `BUZZ_sneaker_store_.mp4`-jéből
  (100 MB, portré 4K60, 14,6 mp, **nem igényelt** confirm-tokent, 100 MB
  alatt van) → 720px széles portré, 5,2 MB.
- `goat-espana.jpg` + `.mp4` — a Videók/Goat/`goat benidorm shakira.mov`-ból
  (53 MB, 1080p30, 29 mp) → 1280px, 7,9 MB. Ez a fájl a bento-kártyán ÉS a
  `.vband` parallax videó-sávon is szerepel — mindkét helyen ugyanaz a
  fájlnév, nem kellett külön kezelni.
- `rendezvenyek.jpg` + **új `.mp4`** — a Videók/Rendezvény/`Czaga Tiszafüred
  Dj Tour Fest.MOV`-ból (215 MB, 4K30, 58,3 mp — a Rendezvény mappa **kb. 35
  aftermovie-t** tartalmaz különböző DJ-eseményekről, ez csak egy közülük,
  a leírásban explicit megnevezett helyszín miatt választva) → 1280px,
  16 MB. A kártya `data-lb-type` `image`-ről `video`-ra váltva, `bento-play`
  ikon hozzáadva. A poszterkép is cserélve egy pirotechnikás koncert-
  jelenetre (a régi tömegkép a RNI-Films fotókból volt, ez a valódi
  aftermovie-ból kivágott kocka).

**Két ÚJ kártya, a user kifejezett kérésére (13. és 14.):**
- **Cserna Barbershop** (`cserna-barbershop.jpg`+`.mp4`, `data-cat="web marka"`)
  — a Weboldalak/`CSERNA BARBER` weboldal-felvételből (189 MB, 3456×1708 60fps,
  43,8 mp, néma → 720p, 9,2 MB) + poszter a Fénykép/`Barber` mappa egyik
  fekete-fehér fotójából (ugyanaz a mappa, amit a `Barber` fotósorozat linkje
  igazolt: https://drive.google.com/drive/folders/17WwLaIMXj-YT-7ydKS6wlUYUwZ32-8ex).
- **Patron Barbershop** (`patron-barbershop.jpg`+`.mp4`, `data-cat="social marka"`,
  `tall` méret a portré-videó miatt) — ez volt a user által "patronxii"-ként
  említett anyag; a helyes link
  https://drive.google.com/drive/folders/1mFDgvTu1insKpWtVNO0sEgIl7h3ND1B2
  ("Patronxiii Barber shop" mappanév) alatt volt, **NEM** a fő REFERENCIÁK
  mappában. 2 db portré (1080×1920 60fps, ~27 mp) klip AI-generált avatár-
  modellel, aki a szalon terében "dolgozik" — összefűzve (`ffmpeg concat`)
  egy 720×1280, ~54 mp, 16,9 MB videóvá.

**Mind a 14 kártya éles** — az `eurama` az egyetlen szándékos kivétel
(user kérésére kihagyva, `eurama.png` van hozzá a Drive-ban, de nem
használandó).

**Fel nem használt, de talált anyag — érdemes lehet később megnézni:**
- A Videók/Rendezvény mappa **~35 további aftermovie-t** tartalmaz más
  DJ-eseményekről (BONS-DRUMCLUB, ÉBERKÓMA, T-POSE@SUPERSONIC, DJ BUDAI/
  GREGA/DUBMORE MIDSUMMER stb.) — ha a "Nyári rendezvényszezon" kártyát bővíteni
  kell, vagy egy második rendezvény-kártya kellene, itt van bőven választék.
  Van egy `Kowalsky Meg a Vega-Egy Világon Át |Koncertfelvétel|-Móri Bornapok.MOV`
  is (720 MB) — másik Kowalsky-anyag, nem használtuk.
- A Videók/`GYM` és Videók/`Zenés Videó klip` mappákat **nem néztem meg** —
  ha ezek meglévő ügyfelek, akiknek jelenleg nincs kártyája, új referencia-
  kártya lehet belőlük.
- A Videók/`Goat` mappában sok további nyers klip van (`1212(5).MOV` stb.,
  20–175 MB egyenként) — a `goat benidorm shakira.mov` mellett további
  variáció készíthető, ha kell.

**Fájlméret-eredmények:** minden új videó jóval a ~30 MB-os önhosztolási
szabály alatt maradt (5–28 MB), mert a forrás felbontását/fps-ét levágtuk
720–1280px/30fps-re és ~2 Mbps-re tömörítettük libx264-gyel. Az `img/ref/`
mappa teljes mérete jelenleg ~124 MB (14 videó + 14 poszterkép).

---

## Jelenlegi állapot

**Élő:** 22 magyar oldal + 3 nyelvi változat (**88 oldal**). index, arak, referenciak, crm, koszonjuk, 8 szolgáltatás-oldal
(ppc-hirdeteskezeles, workflow-automatizalas, markaidentitas, kozossegi-media,
weboldalkeszites, dronfelvetel, aftermovie, termekfotozas), 4 jogi oldal
(impresszum, adatvedelem, aszf, cookie-szabalyzat), blog listaoldal + 4 cikk.

**Blog (2026-09-04):** `blog.html` + `blog-leadbol-ugyfel.html`,
`blog-google-ads-koltseg.html`, `blog-kozossegi-media-strategia.html`,
`blog-weboldal-sebesseg-seo.html`. A cikkek **kötve vannak az `arak.html`-hez**:
ugyanaz a 4 munkaóra, ugyanaz a négy lead-feltétel, ugyanaz a 80 × 6,25% = 5
számpélda. **Ha az `arak.html` ajánlata változik, a blogot is át kell nézni** —
egyszer már szétcsúszott (lásd Csapdák).

**A site üzenete (2026-09-03-tól):** ügyfélszerzés, nem „full-service
ügynökség". A főoldal heroja és az `arak.html` is erre épül: „Megtöltjük a
naptárad." Az ajánlat egy garanciamechanizmus — a lead-célszám a **szerződésben**
van, nem a weboldalon (lásd Csapdák: miért nem hirdetünk 80 leadet).

**Négy nyelven (2026-09-05-től):** magyar (gyökér) · `/en/` · `/de/` · `/es/`.
A fordítás **generált**, nem kézzel írt: a forrás a magyar oldal, a szótárak a
`tools/i18n/translations/<nyelv>/*.json` fájlokban vannak, a build pedig a
`tools/i18n/i18n_build.py`. **Soha ne szerkeszd közvetlenül az `/en/`,
`/de/`, `/es/` HTML-eket** — a következő build felülírja őket. Ha szöveg
változik: írd át a magyart, futtasd az `i18n_extract.py`-t, fordítsd le az új
egységeket, majd `i18n_build.py`. A `i18n_check.py` megmondja, mi hiányzik.

**Mérve és rendben:**

- 0px vízszintes túlcsordulás 320 / 375 / 414 / 768 / 1280px-en, mind a 17 oldalon
- nincs levágott szöveg, nincs 24×24px alatti tap-target
- minden oldalon pontosan egy `h1`, nincs címsorszint-kihagyás
- az `app.js` mindenhol végig lefut (jelzőbója: a `.wa-float` gomb létrejön)
- nincs törött belső link, halott horgony, duplikált `id`, `alt` nélküli kép
- az árak egyeznek mind a négy helyen (lásd Csapdák)
- mind a 88 oldal 0px túlcsordulással, 320/375/414/1280px-en
- egyik nyelvi oldalon sincs magyar szöveg (`őű` karakter-ellenőrzés)
- a hreflang kölcsönös: mind a 88 oldalon pontosan 5 sor (hu/en/de/es/x-default)
- a kalkulátor számformázása és mértékegységei az oldal nyelvét követik
- az `arak.html` árazása kártyák + igazi táblázat (a korábbi ártartomány-
  diagramok kikerültek: a sáv semmit nem mondott, amit a szám nem, a csomagok
  tartalma viszont egyáltalán nem volt az oldalon)
- a workflow-oldal ROI-kalkulátora **működik**: kitöltött csúszkasáv, min/max
  címkék, élő jelölő a görbén, és a címke egyik szélen sem csúszik ki

**Jogi oldalak cégadata kitöltve (2026-09-11):** impresszum, adatvédelem, ÁSZF
— Bartek Dávid e.v., 91718613-1-27, cégjegyzék/nyilv. szám 61773671, székhely
Regiposta utca 10, 2481 Velence, telefon +36 20 260 7810 (ugyanaz az e.v., ami
a proteinbazis.com-ot is üzemelteti). Mind a 4 nyelven (a `tools/i18n`
szótárakba is bekerült, nem csak a magyar forrásba — lásd Nyitott döntések #4
a még hiányzó ÁSZF üzleti feltételekről).

**Nem pusholt commitok lehetnek** — ellenőrizd a fenti git paranccsal.

---

## Nyitott döntések (a user-re várnak, ne döntsd el helyette)

| # | Kérdés | A javasolt megoldás |
|---|---|---|
| 1 | **Saját fotók a heroba?** A szolgáltatás-oldalak heroja most rétegzett gradiens. A generált fotók kikerültek (AI-klisék, halandzsa szöveggel). | A Drive `REFERENCIÁK/Fénykép` mappájából a **saját anyag** — drón lapra valódi drónfotó stb. A gradiens marad fallbacknek. |
| 2 | **OG-kép** | A mostani `img/og-image.jpg` **1200×1335, portré**. Az Open Graph 1200×630-at vár, így a megosztásokból levágja a „SOUL SILVER" és a „MARKETING AGENCY" feliratot. Javaslat: 1200×630 a site dizájnjából. |
| 3 | **Mint akcentus szövegként** | `--mint-deep` (`#0A9E77`) világos háttéren **2,98:1** → megbukik a WCAG AA 4,5:1-en. Javaslat: külön `--mint-text: #087B5D` (4,58:1) csak a szöveges használatra; grafikai elemként a mostani marad (ott a 3:1 elég). 22 helyen érinti. |
| 4 | **Az ÁSZF üzleti feltételei** (2026-09-11 óta nyitott). A cégadatok ki vannak töltve (Bartek Dávid e.v.), de a fizetési feltételek (előleg %, hátralék, határidő), a bankszámlaszám és a lemondási/elállási feltételek még `[…]` placeholderek — lásd `aszf.html` „Kitöltendő" doboz. | A user adja meg ezeket; addig jogilag hiányos az ÁSZF, ezt ne töltsd ki kitalált adattal. |

---

## Hátralévő munka (prioritás szerint)

1. **Referencia-média** — 2026-09-13-án lezárva (ld. fent mindkét
   „Referencia-média" szakaszt). Mind a 14 kártya éles anyaggal megy
   (a 12 eredeti + 2 új: Cserna Barbershop, Patron Barbershop). Egyedül az
   `eurama` marad szándékosan üresen, a user kérésére. Van még fel nem
   használt nyers anyag a Drive-ban (~35 további aftermovie, `GYM` és
   `Zenés Videó klip` mappák át sem nézve) — ld. a „Fel nem használt, de
   talált anyag" listát fent, ha bővíteni kell.
2. **Ügyféllogók** — a `referenciak.html` marquee-ja most **platformlogókat**
   mutat („Platformok, amelyeken dolgozunk"), mert valós ügyféllogó nincs.
   Ha lesz engedélyezett logó: `img/logos/ugyfel/`, az eyebrow átírása
   „Ügyfeleink"-re, és a lista **kétszer** felsorolva (a végtelen csúszáshoz).
3. **A workflow-oldal árai** — 90 000 / 180 000 / 490 000 Ft-tól + 39 000 Ft/hó
   üzemeltetés. Ezeket az előző session tette be a többi nagyságrendjéhez
   illesztve; a user **nem hagyta jóvá számszerűen**.
4. **A referenciák oldal hero-statisztikái** — 30+ projekt / 14 rendezvény /
   7 szolgáltatási terület. Ezek a Drive **mappáiból számolva** készültek, nem
   könyvelésből. Ellenőrizendő.
5. **`koszonjuk.html`** — nincs menü, csak a logó (szándékos: konverziós
   oldal, ahol minden további link elterelne; a logó visszavisz a főoldalra).
   Az `audit_static.py`-ban ez már rögzített kivétel, nem hibaként jelenik meg.

---

## Csapdák — ezeket drága volt megtalálni

**Tartalmi szabály, ne írd felül.** A user kétszer is jóváhagyta, hogy kitalált
eredményszámot ne tegyünk ki valós ügyféleredményként. A
`workflow-automatizalas.html` számpéldája ezért kötelezően `SZÁMPÉLDA ·
modellezett forgatókönyv` címkét és záró jegyzetet visel — **ezt a két
jelölést nem szabad eltávolítani** (Fttv. 2008. évi XLVII. tv., uniós UCPD).
Ha konkrét eredményszámot kér, kérdezz rá, hogy valós-e, és ajánlj fel hármat:
valós esettanulmány hozzájárulással · jelölt modellezett példa · kalkulátor a
látogató saját számaival.

**A 80 minősített lead NEM hirdetési állítás.** A user szerződésébe kerül, de
mérési előzmény nincs mögötte, ezért a weboldal **csak a mechanizmust** mondja
el („a lead-célszám a szerződésben van"), számot nem. Ha valaki ki akarja tenni
a 80-at a főoldalra, az ugyanabba a jogi kategóriába esik, mint a kitalált
ügyféleredmény.

**Az árak NÉGY helyen vannak, automatikus kapcsolat nélkül:**
a szolgáltatás-oldal `.price-grid`-je (a forrás) · `tools/gen_charts.py`
`PROJEKT`/`HAVI` listája · az `arak.html` `.svc-table`-je ·
`tools/gen_arlista_pdf.py` `SERVICES` listája. Ha árat módosítasz, mind a négyet
frissítsd, majd futtasd: `python3 tools/audit_prices.py` — ez kimutatja az
eltérést.

**Az `audit_prices.py` a HTML-markupot parseolja, tehát a szerkezet átírása
csendben elnémítja.** Az árazás átépítésekor a `.price-grid` helyére
`.svc-table` került, és a szkript onnantól *nulla* árat talált az
`arak.html`-ben — vagyis mind a kilenc szolgáltatásra eltérést jelentett, ami
könnyen összemosódik a valódi hibával. Most sorszámot is ellenőriz (9 sor), és
figyelmeztet, ha nem annyit olvas be. Ha átírod az árazás markupját, futtasd le
és nézd meg, hogy a „tabla" oszlop nem üres-e.

**A CSS grid `1fr` nem véd a túlcsordulástól.** A `1fr` valójában
`minmax(auto, 1fr)`, és az `auto` alsó határa a tartalom min-content
szélessége — egyetlen hosszú, törhetetlen szó szétfeszítheti a sávot a
konténeren túl. Magyarul ez nem jött elő, németül igen: a `.feature-grid`
sávja 303px lett egy 257px-es konténerben. A rács-elemekre `min-width:0` kell.
Négy nyelven bármelyik nyelv bármikor hozhat hosszabb szót — ez nem német-specifikus.

**A JS-ben összefűzött szöveg NEM megy át a fordításon.** A kalkulátor
mértékegységei (`+ ' Ft'`, `+ ' óra'`) futásidőben keletkeznek, ezért a
kinyerő nem látja őket, és magyarul mentek volna ki mind a három idegen nyelvű
oldalra. Ha új futásidejű szöveget írsz az `app.js`-be, tedd az `U` szótárba.

**Az SVG-jelölő címkéjét nem lehet fix arányú küszöbbel a plotban tartani.**
A ROI-görbe két változatban van (520 és 240 egység széles viewBox), és
ugyanaz a szöveg a szűkebbikben arányosan szélesebb — a régi „a plot jobb 28%-án
fordulj balra" szabály a mobilon levágta a „hó"-t. A tényleges szöveghosszt kell
mérni `getComputedTextLength()`-tel, viewBox-egységben.

**A diagram jelölő-színe NEM a téma tokene.** `--chart-mark: #0A9E77`, és
szándékosan ugyanez világos és sötét témában. A sötét téma `--mint-deep`-je
(`#12C592`) megbukik a dataviz-validátor világosság-sávján (OKLCH L 0,73 a
0,48–0,67 helyett). Ne „javítsd vissza" a tokenre.

**A `clip-path` nem lehet azon az elemen, amit IntersectionObserver figyel.**
A klip kinullázza a metszetet, és a reveal sosem tüzel. Ezért van a
`referenciak.html` bento-kártyáin egy külön `.bento-inner` wrapper.

**Az SVG-szöveg a konténerrel skálázódik, tehát nem lehet reszponzív.**
Bármelyik viewBox-méretnél valamelyik készülékszélességen elromlik a betűméret.
Ezért az `arak.html` mobil ártartomány-diagramja **HTML/CSS sávlista**, nem SVG
(`tools/gen_charts.py` → `chart_compact`). A ROI-görbe maradt SVG, de 240
egységes viewBox-szal, hogy a nagyítás minden telefonon ≥0,93 legyen.

**A `.phero-bg` `background-attachment: fixed`**, tehát a gradiens a
**viewporthoz** méretezett, nem az elemhez — a százalékos pozíciók a képernyőre
értendők. A `.phero-scrim` a jobb oldalon is ~44%-ot elnyel, ezért a hero-fények
0,7–0,95 alfájúak és éles kifutásúak; halványabbal sík sötét sávnak látszanak.

**A `requestAnimationFrame` nem fut a Claude Code preview-paneljében.** Ezért a
láthatóság-váltások `void el.offsetWidth` reflow-t használnak, nem rAF-ot
(`app.js`: lightbox nyitás, bento szűrő). Ugyanezért **a panel képernyőképe nem
lát a hajtás alá** — legörgetve üres képet ad. Mérj DOM-ból, és ha látképet
akarsz egy lentebbi szekcióról, ideiglenesen rejtsd el a fölötte lévőket JS-sel.

**A panel nem kap OS-szintű billentyűfókuszt**, ezért a `:focus` állapot ott nem
tesztelhető (a Tab a `BODY`-n marad). A skip-link stílusa ellenőrzött, a
kiváltása nem.

**A PATH-on lévő `python` az Inkscape-é**, nincs benne pip. Használd:
`/c/Users/SOULSILVER/scoop/shims/python3.exe` (van pip, van reportlab,
pdfplumber, pypdfium2).

**A Bash tool heredocjai megeszik a backslasht és a backticket**, ha a Python-kód
dupla idézőjelben megy át. Több szkript és egy commit-üzenet is elromlott már
ettől. Hosszabb Python-kódot **írj fájlba** (Write tool), és úgy futtasd.

**A `styles.css` és az `app.js` a böngésző cache-éből jön**, ha csak a HTML-en
van cache-buster. Mérés előtt:
`await fetch('/styles.css',{cache:'reload'})` és ugyanez az `app.js`-re.

**Karbantartás mód (2026-09-11-től létezik).** A gyökérben lévő `.htaccess`
+ `karbantartas.html` egy kapcsolható "site offline" módot ad: ha a
`.maintenance` fájl létezik a gyökérben, minden látogató a
`karbantartas.html`-t kapja 503-mal, kivéve akinél megvan az
`ss_preview` süti (`?preview=<token>`-nel állítható be egyszer, a token
az `.htaccess`-ben, 24 órára szól). **Alapállapotban a `.maintenance`
fájl NEM létezik** — az `.htaccess` ekkor semmit nem csinál, minden
oldal simán megy. Kapcsolás: hozz létre/törölj egy üres `.maintenance`
fájlt, commit + push (a hosting nem érti a repo-n kívüli fájlokat, ez a
fájl is a repóban van, tehát a git history-ban látszik, mikor volt be-
és kikapcsolva). **`.htaccess`-t módosítva mindig ellenőrizd rögtön
push után, hogy az oldal él-e** — egy szintaxishiba az EGÉSZ siten
500-as hibát okozhat, nem csak egy oldalon, és ezt sem a `php -l`, sem
semmilyen helyi eszköz nem szűri ki (nincs Apache telepítve helyben).

---

## Ellenőrző szkriptek

```bash
python3 tools/audit_static.py     # linkek, id-k, alt, nav, sitemap, canonical
python3 tools/audit_prices.py     # az árak egyeznek-e a négy helyen
```

Böngészős méréshez (túlcsordulás, levágott szöveg, tap-target) a bevált módszer:
iframe-ekben betölteni az oldalakat különböző szélességen, és a DOM-ból mérni.
A `tools/audit_browser.js` tartalmazza a snippetet — másold be a
`javascript_tool`-ba a preview-lapon.

## Generátorok

```bash
python3 tools/gen_charts.py       # arak.html: 2 SVG (desktop) + 2 HTML sávlista (mobil)
python3 tools/gen_roi.py          # workflow ROI-görbe: széles + mobil
python3 tools/gen_arlista_pdf.py  # letölthető árlista PDF (a Downloads mappába)
```

A kimenet a `tools/_out/` mappába megy (gitignore-olt); onnan kell bemásolni a
megfelelő HTML-be. Részletek: `tools/README.md`.

---

# Történet (visszamenőleg)

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
| 13 | Cserna Barbershop | web, marka | `cserna-barbershop` |
| 14 | Patron Barbershop | social, marka | `patron-barbershop` |

A két videó-sáv: YOUTOPIA × Sara Landry és THE G.O.A.T. España.

13–14: 2026-09-13 este hozzáadva, a user kifejezett kérésére (nem az eredeti
12-ből valók). Lásd a fenti „Referencia-média — 2. kör" szakaszt.

Hero statisztikák a Drive-mappák alapján számolva: 30+ lezárt projekt,
14 dokumentált rendezvény, 7 szolgáltatási terület. **Ezeket érdemes
ellenőrizni** — mappaszámlálásból származnak, nem könyvelésből.

Kikerült az oldalról a `img/hero-dronfelvetel.jpg`: az a szolgáltatás-oldal
dekorációja, nem valós referenciaanyag, ezért nem szerepelhet referenciaként.

## Hátralévő teendők a referenciák oldalon

1. **Média feltöltése** az `img/ref/` mappába — 2026-09-13-án lezárva mind a
   14 kártyára (ld. fent mindkét „Referencia-média" szakaszt). Egyedül az
   `eurama` marad üres, a user kérésére.
2. **Ügyféllogók**: a marquee továbbra is platformlogókat mutat
   („Platformok, amelyeken dolgozunk"). Ha lesz engedélyezett ügyféllogó:
   `img/logos/ugyfel/`, az eyebrow átírása „Ügyfeleink"-re, és a `.logo-item`
   képek cseréje (a listát kétszer kell felsorolni a végtelen csúszás miatt).
3. **Szövegek ellenőrzése**: a leírásokat a Drive mappa- és fájlnevekből
   vezettem le. Ahol a projekt tartalma pontosabban megfogalmazható, javítsd.
4. **Hero-statisztikák újraszámolása** — a „30+ lezárt projekt" számláló a
   2026-09-01-i mappaszámlálásból származik; két új kártya (Cserna, Patron
   Barbershop) került fel azóta, de a „30+" jelölés ettől még helytálló marad.
   Ha pontosabb számot szeretne a user, újra kell számolni a Drive-mappákat.

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


## Javítva (2026-09-02)

A fenti hibakeresés összes túlcsordulási hibája megszűnt. **Mind a 16 oldal
0px vízszintes túlcsordulást mér 375px, 768px és 1280px szélességen.**

| Commit | Mit javított |
|---|---|
| `ca80166` | `.foot-grid > * { min-width:0 }` + tördelhető email → a site-szintű 35px; `.phero`/`.pband` `100vw` → `100%`; `.reveal` vízszintes eltolás mobilon; `.subnav` tördelés; `.price-grid` 1180px töréspont; hosszú magyar szavak `overflow-wrap` |
| `2f6ec48` | `.table-scroll` görgethető keret a jogi táblázatoknak (330px és 51px); a négy jogi oldal bekerült a `sitemap.xml`-be |

A `.claude/worktrees/sharp-pike-731f5a` worktree beolvasztva a `main`-be
(`git rebase main` konfliktus nélkül), a branch törölve. A `.claude/worktrees/`
alatt maradt egy üres, zárolt könyvtár — gitignore-olt, ártalmatlan.

**Nyitva maradt, alacsony prioritás:** a `koszonjuk.html`-en nincs `canonical`
(az oldal `Disallow`-olt a `robots.txt`-ben, ezért SEO-szempontból nem sürgős).

## Workflow-automatizálás oldal (2026-09-02)

Spec: [2026-09-02-workflow-automatizalas-design.md](superpowers/specs/2026-09-02-workflow-automatizalas-design.md)
Terv: [2026-09-02-workflow-automatizalas.md](superpowers/plans/2026-09-02-workflow-automatizalas.md)

Új oldal: `workflow-automatizalas.html` — a 8. szolgáltatás-oldal.

- Hero kapacitás-üzenettel, „Hol szivárog el az idő?" 6 kártyával
- Workflow-katalógus: 10 workflow 4 kategóriában, akkordeonként kinyitható,
  mindegyik trigger → 5 lépés → eredmény szerkezetben
- Inline SVG folyamatábra: a lead-válasz workflow anatómiája
- ROI-kalkulátor: 3 csúszka, élő számítás, a lapon kiírt formulával
- Modellezett számpélda a kötelező jelölésekkel
- „Mit NEM automatizálunk" 4 kártya, folyamat 5 lépés, 3 árcsomag,
  5 GYIK + `FAQPage` JSON-LD
- `styles.css`: új `WORKFLOW AUTOMATIZÁLÁS OLDAL` blokk
- `app.js`: 2 új null-guardolt modul (akkordeon, kalkulátor)
- Bekötve: `index.html` work-grid csempe + footer Szolgáltatások lista mind a
  15 oldalon, `sitemap.xml`

**Pozicionálás:** kapacitás („ugyanennyi emberrel többet"), NEM
létszámcsökkentés. A kért „2 embert elbocsátottunk, +30% bevétel" eset
**modellezett példaként** került ki, `SZÁMPÉLDA · modellezett forgatókönyv`
címkével és záró jegyzettel — a user megerősítette, hogy nem valós ügyfél
adata. Ezt a két jelölést nem szabad eltávolítani (Fttv./UCPD).

**Ellenőrizve:** a kalkulátor kimenete kézzel visszaszámolva egyezik
(3 fő × 1,5 óra × 21 munkanap × 0,7 = 66 óra/hó, 297 675 Ft/hó; 10 fő × 2 óra
× 6000 Ft = 294 óra/hó, 1 764 000 Ft/hó); az akkordeon `aria-expanded`-del
nyit és zár; az `app.js` mind a 16 oldalon végig lefut.

**Hátra van ezen az oldalon:**

1. **Hero-kép** (`img/hero-workflow.jpg`) — amíg nincs, a gradiens fallback
   látszik, ami önmagában is jól néz ki. A `chatgpt-parallax` skillel
   generálható, mint a többi szolgáltatás-oldal heroja.
2. **Árak jóváhagyása** — 90 000 / 180 000 / 490 000 Ft-tól + 39 000 Ft/hó
   üzemeltetés. Ezeket én tettem be a többi oldal nagyságrendjéhez illesztve,
   a user nem hagyta jóvá számszerűen.
3. **Valós esettanulmány**, ha lesz dokumentált eset ügyfél-hozzájárulással —
   akkor a modellezett számpélda kiváltható.

## Mobilnézet-audit (2026-09-02)

Programozott mérés 17 oldalon × 5 szélességen (320/375/414/768/1280) = 85 mérés.
Mért szempontok: dokumentum-túlcsordulás, saját konténerén kilógó/levágott
szöveg, 24×24px alatti tap-target, apró betűk, átfedő elemek, kilógó képek,
és hogy az `app.js` végig lefut-e.

### Talált és javított hibák

| Hol | Mi volt | Javítás |
|---|---|---|
| `arak.html`, workflow ROI | Az SVG-diagram feliratai **~7,5px-en** jelentek meg mobilon (a 760/520 egységes viewBox 246px-es keretbe zsugorodott), és a diagram 41%-a látszott egyszerre | Az ártartomány-diagram mobil változata **HTML/CSS sávlista** (az SVG-szöveg a konténerrel skálázódik, tehát nem lehet reszponzív); a ROI-görbe mobil viewBox-a 240 egység |
| workflow-katalógus | 320px-en a címke-pill a workflow nevét **31px-re** szorította | A pill a név alá kerül 520px alatt; a név `flex:1 1 0`-val a chevronnal egy sorban marad |
| `referenciak.html` | A `.bento-title` levágódott 320px-en (a kártya `overflow:hidden`) | `overflow-wrap:anywhere` a bento szövegeken |
| `aszf.html` | **25px túlcsordulás**: a „Nyilvántartási/cégjegyzékszám:" nem tört (a `/` nem törési pont) | `overflow-wrap:anywhere` a `.legal-content` listákon |
| kalkulátor | A csúszkák **16px** magasak voltak — ujjal alig fogható | 44px magas sáv, 24px-es fogantyú (webkit + moz) |
| footer, jogi oldalak | Listalinkek **20–22px** magasak | `padding-block` → 31–36px (`.foot-grid`, `.legal-content`, `.foot-legal`) |
| mobil fejléc | A logó (135px) + CTA-gomb (117px, két sorba tördelve) + hamburger (44px) = 323px a 309px-es helyen; a gomb a logóhoz tapadt, a fejléc 84px magas lett | A fejléc-CTA 620px alatt elrejtve — a Kapcsolat a hamburger-menüben és a WhatsApp gombban is elérhető |
| szűrő-chipek | 30px magasak | 620px alatt nagyobb padding |

### Vakriasztás (nem hiba)

- `input.hp-field` — a kapcsolati űrlap honeypotja. `left:-9999px`, `opacity:0`,
  `tabindex="-1"`, `aria-hidden` → helyesen van elrejtve, csak geometriája van.
- A logó-marquee képei „kilógnak", de a `.logo-marquee` `overflow:hidden`-je levágja.

### Tudatosan nyitva hagyva

- **Desktop nav-linkek 21px magasak** (csak 1280px-en jelez). A köztük lévő
  térköz 32px, ami kimeríti a WCAG 2.5.8 spacing-kivételét, és egérrel pontos —
  a fejléc dizájnját nem érdemes ezért átszabni.
- **A lebegő WhatsApp gomb** 320px-en a lap-alji CTA-gomb sarkát ~3×19px-en
  fedi (a link 1%-a). Bármely lap-alji CTA-val előfordul; nem tap-hiba.

**Eredmény: 320/375/414px-en nulla túlcsordulás, nulla levágott szöveg, nulla
24px alatti tap-target mind a 17 oldalon.**

## Szolgáltatás-hero képek kivéve (2026-09-02)

A generált „hightech iroda" fotók kikerültek mind a 9 oldalról (8 szolgáltatás
+ CRM). A user szerint AI-klisék voltak, és a mérés ezt alátámasztja: a
`hero-ppc.jpg`-n a monitoron **halandzsa szöveg** van (a „ROAS" után
olvashatatlan karakterek), ami klasszikus generálási artifact, és ugyanez a
`hero-markaidentitas.jpg` papírjain.

Helyettük **rétegzett gradiens** minden oldalon, `.phero-bg.bg-*` osztályokkal
a `styles.css`-ben. Két dolog kellett hozzá:

1. A `.phero-bg` beállítása `background-attachment: fixed`, tehát a gradiens a
   **viewporthoz** méretezett, nem az elemhez — a százalékos pozíciók a
   képernyőre értendők, nem a hero-sávra.
2. A `.phero-scrim` a jobb oldalon is ~44%-ot elnyel (0,30 vízszintes +
   0,15–0,35 függőleges réteg). Ezért az első próbálkozás (0,2–0,3 alfa) sík
   sötét sávnak látszott. Éles kifutású, 0,7–0,95 alfájú fénypont kellett,
   hogy szándékos megvilágításnak olvasódjon. A fény a jobb oldalon van, ahol
   a scrim a legvilágosabb; balra a scrim 94%, oda kerül a fehér szöveg.

Oldalanként más a fénypont helye és színe — ellenőrizve, hogy mind a 9 háttér
különböző. A 8 hero-JPEG (900 KB) törölve, mert hivatkozás nélkül maradt; a
git történetében megvan, ha kellene.

Ha később mégis fotó kell: a jó megoldás **nem** AI-generálás, hanem a Drive
`REFERENCIÁK` mappájában lévő **saját anyag** (Fénykép: 23 projektmappa, drón-
és rendezvényfotókkal). Az egyszerre hiteles és portfólió is.

### Nyitott döntés: az og-image.jpg

A közösségi megosztásokhoz használt `img/og-image.jpg` **1200×1335, tehát
portré**. Az Open Graph 1200×630-at vár (1,91:1), így a platformok a középső
sávot vágják ki: a megosztásokból pont a „SOUL SILVER" felirat és a „MARKETING
AGENCY" esik le, és nagyrészt a tenger meg az „ARTIFICIAL INTELLIGENCE" marad.
A bézs/barna színvilág a site grafit + mint palettájához sem passzol.
Mind a 16 oldal ezt a képet hivatkozza (`og:image` és `twitter:image`).

### Nyitott döntés: a mint akcentus szövegként

A `--mint-deep` (`#0A9E77`) világos háttéren **2,98:1** — megbukik a WCAG AA
4,5:1-en, sőt a nagy szövegre vonatkozó 3:1-en is egy hajszállal. 22 helyen
használjuk szövegszínként világos háttéren (`eyebrow`, `price-name`,
lépés-számok, `ref-metric`, jogi linkek, hover-állapotok). A javítás egy külön
`--mint-text: #087B5D` token lenne (4,58:1) — ugyanaz a zöld, sötétebb.
Grafikai elemként (diagram-sávok, gombháttér) a jelenlegi érték megfelel a
3:1-nek, tehát csak a szöveges használatot kell átváltani.
