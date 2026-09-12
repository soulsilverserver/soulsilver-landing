# Kártyás fizetés (Stripe Checkout)

A soulsilver.hu fix árú csomagjai bankkártyával fizethetők. Nincs SDK és nincs
composer — a Stripe REST API-t hívjuk közvetlenül cURL-lel, mert a Hostinger-en
nincs build lépés.

## Fájlok

| Fájl | Mit csinál |
|---|---|
| `stripe-csomagok.php` | **Az árlista egyetlen hiteles forrása.** Csomag-azonosító → név, nettó ár, típus (`egyszeri`/`havi`), szolgáltatás-oldal. |
| `stripe-lib.php` | config.php betöltés, Stripe API hívás, ÁFA/összeg-számítás, naplózás, Resend email. |
| `fizetes.php` | A fizetési oldal: kártyák a csomagokból, gomb → `stripe-checkout.php`. |
| `stripe-checkout.php` | Checkout session létrehozása, majd átirányítás a Stripe fizetőoldalára. |
| `fizetes-siker.php` | A vevő visszairányítási oldala. Csak visszajelzés — **nem** ez könyveli a rendelést. |
| `stripe-webhook.php` | A Stripe eseményeit fogadja, aláírást ellenőriz, és elküldi a **számlázási értesítőt**. |
| `bin/setup-keys.sh` | Beírja a kulcsokat a `config.php`-ba (terminál vagy `--ablak`). |

## Két szabály, amit ne írj át

1. **Az árat mindig a szerver dönti el.** A böngésző csak a csomag
   azonosítóját küldi (`csomag=crm-belepo`); az összeget a
   `stripe-csomagok.php`-ból olvassuk. Ha az árat a formból fogadnánk el,
   a látogató átírhatná 1 forintra.

2. **`webhely = soulsilver.hu` metaadat.** A Stripe fiók közös a Protein Bázis
   webshoppal, tehát ennek a webhooknak a *másik* bolt eseményei is
   megérkeznek. Minden fizetésre rákerül ez a jelölő, és a
   `stripe-webhook.php` mindent eldob, amin nincs rajta. Ezt a szűrőt ne
   vedd ki, különben a Protein Bázis rendelései SOULSILVER-értesítőt
   generálnak.

## A számlázás menete

Számlázó rendszer NINCS bekötve: minden fizetés után **emailt kapsz**, és a
számlát te állítod ki kézzel. Az email (a config.php `to` címére megy) mindent
tartalmaz, amit be kell írni a számlázóba:

- vevő neve / **cégneve**, **adószáma**, számlázási címe, email, telefon
- a tétel megnevezése, **nettó**, **ÁFA összege**, **bruttó**
- a fizetés időpontja, a csomag azonosítója, a Stripe azonosító és egy
  közvetlen link a Dashboardba

Ehhez a Checkout be is kéri a szükséges adatokat: `billing_address_collection`
(kötelező cím), `name_collection` (magánszemély neve kötelező, cégnév
opcionális), `tax_id_collection` (adószám, opcionális — magánszemélynek nincs),
`phone_number_collection`.

Ezek közül a `name_collection` újabb keletű. Ha a fiók API-verziója nem ismerné,
a `stripe-checkout.php` **eldobja az ismeretlen paramétert és újrapróbálja** —
egy ilyen apróság soha ne akadályozzon meg egy fizetést. A naplóban ilyenkor
megjelenik, hogy melyik paramétert hagyta ki.

### Nem érkezik két levél ugyanarról

A Stripe legalább egyszer kézbesít: ha a 200-as válaszunk nem ér vissza hozzá,
újraküldi ugyanazt az eseményt. Számlázásnál ez két számlát jelentene, ezért az
eseményazonosítót a **sikeres küldés után** rögzítjük
(`soulsilver-stripe-esemenyek.txt`, az utolsó 500 azonosító), és a már látott
eseményeket kihagyjuk.

Ha az email küldése **nem sikerül**, a webhook szándékosan **500-at** ad vissza
és NEM rögzíti az eseményt — így a Stripe újrapróbálja, és az értesítő később
mégis megérkezik. Enélkül egy rendelés némán elveszne.

### Havidíjas csomagok számlázása

Előfizetésnél **minden hónapban** kapsz egy ilyen levelet (`invoice.paid`),
tehát havonta kell számlázni. Az előfizetés első számláját nem jelentjük
kétszer: azt már a `checkout.session.completed` jelezte.

### Amit a vevő kap

Semmit, amíg be nem kapcsolod a Stripe automatikus visszaigazolóját:
Dashboard → Settings → Customer emails → „Successful payments". Ez viszont
**nyugta jellegű visszaigazolás, nem számla** — a számlát akkor is neked kell
kiküldeni.

## Árazás és ÁFA

Az árlistában (és a `stripe-csomagok.php`-ban) **nettó** árak vannak. A Stripe
a **bruttót** terheli: `bruttó = round(nettó × (1 + afa_kulcs))`, ahol az
`afa_kulcs` a config.php-ban állítható, alapértéke `0.27` (27%).

A HUF a Stripe-nál töltésnél kétdecimális pénznem, ezért az összeget
**fillérben** kell megadni: `Ft × 100`. (A „100-cal osztható" szabály csak a
manuális payoutra igaz, a terhelésre nem.) Legkisebb terhelhető összeg: 175 Ft.

Az „egyedi ár" csomagok szándékosan nincsenek a listában — azokra ajánlat
készül, nem lehet kattintásra megvenni.

Az árak öt helyen szerepelnek; a `python3 tools/audit_prices.py` ellenőrzi,
hogy mind az öt egyezik (a `stripe-csomagok.php` az 5.).

## Havidíjas csomagok

A `tipus = havi` csomagok Stripe **előfizetésként** mennek
(`mode=subscription`, `recurring[interval]=month`), tehát a kártyát havonta
automatikusan terheli, amíg fel nem mondják. A felmondás most kézi: a vevő ír,
és a Stripe Dashboardban kell törölni az előfizetést. Ha ez sok lesz, egy
vevői portál (Stripe Customer Portal) a következő lépés.

## Beállítás

### 1. Kulcsok

```bash
bash bin/setup-keys.sh            # terminálban
bash bin/setup-keys.sh --ablak    # felugró ablakban (macOS)
```

Ez a gitignore-olt `config.php`-t írja. A `config.example.php` mutatja a
szerkezetet. A kulcsokat a Stripe Dashboard → Developers → API keys adja.
Teszthez `pk_test_`/`sk_test_`, élesben `pk_live_`/`sk_live_` — a kettőt soha
ne keverd.

A `config.php`-t a szerveren a `public_html` **fölötti** mappába tedd, ha
lehet (a `stripe-lib.php` előbb ott keresi). Így böngészőből nem elérhető.

### 2. Webhook

Stripe Dashboard → Developers → Webhooks → Add endpoint:

- URL: `https://soulsilver.hu/stripe-webhook.php`
- Események: `checkout.session.completed`, `invoice.paid`
- A megjelenő **Signing secret** (`whsec_…`) kerül a config.php-ba.

Signing secret nélkül a webhook **minden** kérést eldob (400) — szándékosan:
enélkül bárki hamisíthatna „sikeres fizetés" értesítőt.

### 3. Teszt

A Stripe teszt-kulcsaival a `4242 4242 4242 4242` kártya (bármilyen jövőbeli
lejárat és CVC) sikeres fizetést ad. A `fizetes.php` teszt módban külön
figyelmeztető sávot mutat.

Webhook teszt lokálisan: `stripe listen --forward-to localhost:8787/stripe-webhook.php`
(Stripe CLI), vagy a Dashboard „Send test webhook" gombja.

## Napló

`ss_log()` a `public_html` fölötti `soulsilver-stripe.log`-ba ír, ha oda tud
(különben `stripe.log` a mappában). A `*.log` gitignore-olt.

## Ami MÉG NINCS KÉSZ — élesítés előtt rendezni kell

(Az impresszum cégadatai 2026-09-12-én elkészültek — Bartek Dávid e.v.,
adószám `91718613-1-27` —, ez tehát már nem hiányzik.)

- **Számla.** A Stripe visszaigazolása nem NAV-kompatibilis számla. Jelenleg
  kézi számlázás van: a webhook emailje minden szükséges adatot elküld, és ki is
  írja emlékeztetőül. Ha sok lesz, egy számlázó (Billingo / Szamlazz.hu)
  bekötése a következő lépés.
- **ÁFA — ezt nézd meg ELŐSZÖR.** Az impresszum szerint az adószám
  `91718613-1-27`. A magyar adószám 9. jegye (az első kötőjel utáni) az
  ÁFA-kód, és itt **1** áll, ami *alanyi adómentességet* jelent (általános
  szabályok szerinti adóalanynál 2 lenne). Ha ez így van, a 27% felszámítása
  hibás: `'afa_kulcs' => 0` kell a config.php-ba. A kód ezt kezeli — ilyenkor
  a fizetési oldal és az értesítő is „alanyi adómentes, nincs ÁFA" szöveget
  mutat, és a fizetendő összeg a nettó árral egyezik. **A könyvelőddel
  erősítsd meg, mielőtt élesben fogadsz fizetést.**
- **ÁSZF.** A §4 csak banki átutalást említ, és két kitöltetlen placeholder van
  benne: `[bankszámlaszám]` és `[Lemondási / elállási feltételek]`. A kártyás
  fizetést, az elállási jogot és a havidíj felmondását bele kell írni — a
  `fizetes.php` alján lévő szöveg az ÁSZF-re hivatkozik.
- **Idegen nyelvű oldalak.** A `fizetes.php` csak magyarul van meg, és a
  fizetés gomb csak a magyar `arak.html`-ben szerepel — az `en/`, `de/`, `es/`
  árlistákban nincs. Ha külföldre is árulni akarsz, ezt pótolni kell.
- **Google Ads konverzió.** A `fizetes-siker.php` egy GA4-stílusú `purchase`
  eseményt küld. Ha a vásárlást a Google Ads-ben is mérni akarod, ott létre
  kell hozni egy konverziós műveletet, és a `send_to` címkét beírni (ahogy a
  `koszonjuk.html` teszi a leaddel).
