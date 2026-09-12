#!/bin/bash
#
# SOULSILVER — config.php kulcs-beíró.
#
#   bash bin/setup-keys.sh            → terminálban kérdez (rejtett bevitel)
#   bash bin/setup-keys.sh --ablak    → felugró ablakokban kérdez (macOS)
#
# A titkos kulcsokat SOHA nem írja ki a képernyőre, csak maszkolt
# ellenőrző sort (sk_test_…7890). A gitignore-olt config.php-ba ír, 600-as
# jogokkal.
#
set -u

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
CFG="$ROOT/config.php"
MODE="terminal"
case "${1:-}" in
  --ablak|--gui) MODE="gui" ;;
  --help|-h) awk 'NR>1 && /^#/ {sub(/^# ?/, ""); print; next} NR>1 {exit}' "$0"; exit 0 ;;
esac

B=$'\033[1m'; DIM=$'\033[2m'; G=$'\033[32m'; Y=$'\033[33m'; R=$'\033[31m'; N=$'\033[0m'

# ─────────── meglévő értékek (nem íródnak ki) ───────────
old_resend=''; old_from=''; old_to=''; old_pk=''; old_sk=''; old_whsec=''; old_glk=''
if [ -f "$CFG" ] && command -v php >/dev/null 2>&1; then
  while IFS=$'\t' read -r k v; do
    case "$k" in
      resend_api_key)         old_resend="$v" ;;
      from)                   old_from="$v" ;;
      to)                     old_to="$v" ;;
      stripe_publishable_key) old_pk="$v" ;;
      stripe_secret_key)      old_sk="$v" ;;
      stripe_webhook_secret)  old_whsec="$v" ;;
      google_lead_key)        old_glk="$v" ;;
    esac
  done < <(php -r '
    $c = @require $argv[1];
    if (!is_array($c)) $c = [];
    foreach (["resend_api_key","from","to","stripe_publishable_key","stripe_secret_key","stripe_webhook_secret","google_lead_key"] as $k) {
      echo $k, "\t", (isset($c[$k]) ? str_replace(["\n","\t"], "", (string)$c[$k]) : ""), "\n";
    }' "$CFG" 2>/dev/null)
fi

mask() {
  local v="$1"
  [ -z "$v" ] && { printf '%s' "(üres)"; return; }
  local n=${#v}
  if [ "$n" -le 8 ]; then printf '%s' "****"; else printf '%s…%s (%d karakter)' "${v:0:8}" "${v: -4}" "$n"; fi
}

php_esc() { printf '%s' "$1" | sed -e 's/\\/\\\\/g' -e "s/'/\\\\'/g"; }
# AppleScript-biztos szöveg. A szöveg-literálban nem lehet sortörés, ezért a
# soremeléseket " & return & " konkatenációra fordítjuk (a hívó zárójelezi).
osa_esc() {
  printf '%s' "$1" \
    | sed -e 's/\\/\\\\/g' -e 's/"/\\"/g' \
    | awk 'NR>1{printf "\" & return & \""} {printf "%s", $0}'
}

# ─────────── ablakos bevitel (osascript) ───────────
# ANSWER-be teszi a választ. Visszatérés: 0 = ok, 1 = a felhasználó megszakította.
gui_ask() {           # $1=üzenet  $2=rejtett? (1/0)
  local msg; msg="$(osa_esc "$1")"
  local hid=''; [ "$2" = "1" ] && hid='with hidden answer'
  local out
  out="$(osascript 2>/dev/null <<OSA
try
  set d to display dialog ("$msg") default answer "" $hid with title "SOULSILVER — kulcsok" buttons {"Mégsem", "Tovább"} default button "Tovább" with icon note
  return text returned of d
on error number -128
  return "__MEGSEM__"
end try
OSA
)"
  if [ "$out" = "__MEGSEM__" ]; then ANSWER=''; return 1; fi
  ANSWER="$(printf '%s' "$out" | tr -d '[:space:]')"
  return 0
}

gui_info() {          # $1=üzenet
  local msg; msg="$(osa_esc "$1")"
  osascript >/dev/null 2>&1 <<OSA
display dialog ("$msg") with title "SOULSILVER — kulcsok" buttons {"Rendben"} default button "Rendben" with icon caution
OSA
}

# ─────────── egy kulcs bekérése (mindkét módban) ───────────
# $1=címke  $2=magyarázat  $3=regex  $4=régi érték  $5=rejtett?
ask_key() {
  local label="$1" hint="$2" re="$3" old="$4" hidden="$5" val=''
  while :; do
    if [ "$MODE" = "gui" ]; then
      local msg="$label"$'\n\n'"$hint"
      [ -n "$old" ] && msg="$msg  Ha üresen hagyod, a mostani érték marad ($(mask "$old"))."
      if ! gui_ask "$msg" "$hidden"; then
        gui_info "Megszakítva. A config.php nem változott."
        exit 1
      fi
      val="$ANSWER"
    else
      if [ -n "$old" ]; then
        printf '%s%s%s %s[Enter = marad: %s]%s\n' "$B" "$label" "$N" "$DIM" "$(mask "$old")" "$N"
      else
        printf '%s%s%s\n' "$B" "$label" "$N"
      fi
      printf '  %s%s%s\n  > ' "$DIM" "$hint" "$N"
      if [ "$hidden" = "1" ]; then IFS= read -r -s val; echo; else IFS= read -r val; fi
      val="$(printf '%s' "$val" | tr -d '[:space:]')"
    fi

    if [ -z "$val" ]; then
      ANSWER="$old"
      [ "$MODE" = "terminal" ] && [ -z "$old" ] && printf '  %sKihagyva (üresen marad).%s\n' "$Y" "$N"
      return 0
    fi
    if [[ "$val" =~ $re ]]; then
      ANSWER="$val"
      [ "$MODE" = "terminal" ] && printf '  %s✓ formátum rendben:%s %s\n' "$G" "$N" "$(mask "$val")"
      return 0
    fi
    if [ "$MODE" = "gui" ]; then
      gui_info "Ez nem úgy néz ki, mint egy érvényes kulcs. $hint  Próbáljuk újra."
    else
      printf '  %s✗ Ez nem érvényes kulcs-formátum.%s\n' "$R" "$N"
    fi
  done
}

ask_plain() {         # $1=címke  $2=régi/alap érték
  local label="$1" old="$2" val=''
  if [ "$MODE" = "gui" ]; then
    local out
    out="$(osascript 2>/dev/null <<OSA
try
  set d to display dialog ("$(osa_esc "$label")") default answer ("$(osa_esc "$old")") with title "SOULSILVER — kulcsok" buttons {"Mégsem", "Tovább"} default button "Tovább" with icon note
  return text returned of d
on error number -128
  return "__MEGSEM__"
end try
OSA
)"
    [ "$out" = "__MEGSEM__" ] && { gui_info "Megszakítva. A config.php nem változott."; exit 1; }
    ANSWER="$(printf '%s' "$out" | sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//')"
    [ -z "$ANSWER" ] && ANSWER="$old"
    return 0
  fi
  printf '%s%s%s %s[Enter = %s]%s\n  > ' "$B" "$label" "$N" "$DIM" "${old:-üres}" "$N"
  IFS= read -r val
  ANSWER="$(printf '%s' "${val:-$old}" | sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//')"
}

# ─────────── indulás ───────────
if [ "$MODE" = "terminal" ]; then
cat <<'BANNER'
──────────────────────────────────────────────────────────────
  SOULSILVER — config.php kulcsok
──────────────────────────────────────────────────────────────
A titkos kulcsok beírása rejtett (nem látszik, amit írsz — ez normális).
BANNER
printf '\nCélfájl: %s%s%s\n\n' "$B" "$CFG" "$N"
fi

ask_key "1/6 — Stripe publishable key" \
        "A Stripe Dashboard → Developers → API keys oldalról. pk_test_… (teszt) vagy pk_live_… (éles). Ez publikus kulcs." \
        '^(pk_(test|live)_[A-Za-z0-9]{10,})$' "$old_pk" 0
pk="$ANSWER"

ask_key "2/6 — Stripe secret key" \
        "Ugyanarról az oldalról, a Reveal gomb után. sk_test_… / sk_live_… (vagy rk_… restricted key). EZ TITKOS — senkinek ne add ki." \
        '^((sk|rk)_(test|live)_[A-Za-z0-9]{10,})$' "$old_sk" 1
sk="$ANSWER"

ask_key "3/6 — Stripe webhook signing secret" \
        "Developers → Webhooks → az endpoint Signing secret értéke: whsec_… Ha még nem hoztad létre a webhookot, hagyd üresen." \
        '^(whsec_[A-Za-z0-9_-]{10,})$' "$old_whsec" 1
whsec="$ANSWER"

# test/live keveredés
pk_mode=''; sk_mode=''
[[ "$pk" =~ _(test|live)_ ]] && pk_mode="${BASH_REMATCH[1]}"
[[ "$sk" =~ _(test|live)_ ]] && sk_mode="${BASH_REMATCH[1]}"
if [ -n "$pk_mode" ] && [ -n "$sk_mode" ] && [ "$pk_mode" != "$sk_mode" ]; then
  figy="FIGYELEM: a publishable kulcs $pk_mode módú, a secret kulcs $sk_mode módú. Így a fizetés nem fog működni — mindkettő ugyanabból a módból kell."
  if [ "$MODE" = "gui" ]; then
    valasz="$(osascript 2>/dev/null <<OSA
display dialog ("$(osa_esc "$figy") Mentsem mégis?") with title "SOULSILVER — kulcsok" buttons {"Mégsem", "Mentés"} default button "Mégsem" with icon stop
OSA
)"
    case "$valasz" in *Mentés*) ;; *) gui_info "Megszakítva. A config.php nem változott."; exit 1 ;; esac
  else
    printf '\n%s⚠ %s%s\n  Mégis mentsem? [i/n] ' "$Y" "$figy" "$N"
    read -r goon; case "$goon" in [iIyY]*) ;; *) printf 'Megszakítva.\n'; exit 1 ;; esac
  fi
fi

ask_key "4/6 — Resend API kulcs (email küldés)" \
        "A resend.com/api-keys oldalról: re_… Ez küldi a kapcsolatfelvételi és a fizetési értesítő emaileket." \
        '^(re_[A-Za-z0-9_-]{10,})$' "$old_resend" 1
resend="$ANSWER"

ask_plain "5/6 — Email feladó (from)" "${old_from:-SOULSILVER weboldal <noreply@soulsilver.hu>}"; from="$ANSWER"
ask_plain "6/6 — Értesítések címzettje (to)" "${old_to:-info@soulsilvermarketing.com}"; to="$ANSWER"

# ─────────── Google Ads lead form kulcs ───────────
# Ezt NEM kérdezzük meg: gépi kulcs, nem a felhasználó találja ki. A meglévőt
# megtartjuk — ha felülírnánk, a Google Adsben beállított kulcs elcsúszna tőle,
# és a webhook némán 401-gyel dobna el MINDEN beérkező leadet. Újat csak akkor
# generálunk, ha még nincs.
glk="$old_glk"
glk_uj=0
if [ -z "$glk" ]; then
  if command -v php >/dev/null 2>&1; then
    glk="$(php -r 'echo bin2hex(random_bytes(16));')"
  else
    glk="$(head -c 16 /dev/urandom | od -An -tx1 | tr -d ' \n')"
  fi
  glk_uj=1
fi

# ─────────── írás ───────────
if [ -f "$CFG" ]; then
  bak="$CFG.bak.$(date +%Y%m%d-%H%M%S)"
  cp "$CFG" "$bak" && chmod 600 "$bak"
  [ "$MODE" = "terminal" ] && printf '\n%sBiztonsági másolat:%s %s\n' "$DIM" "$N" "$bak"
fi

umask 077
tmp="$CFG.tmp.$$"
{
  echo "<?php"
  echo "/* SOULSILVER titkos konfiguráció — GITIGNORE-olt, soha ne kerüljön a repóba."
  echo " * Generálta: bin/setup-keys.sh — $(date '+%Y-%m-%d %H:%M') */"
  echo "return ["
  echo "    // Resend (email)"
  echo "    'resend_api_key'         => '$(php_esc "$resend")',"
  echo "    'from'                   => '$(php_esc "$from")',"
  echo "    'to'                     => '$(php_esc "$to")',"
  echo ""
  echo "    // Stripe"
  echo "    'stripe_publishable_key' => '$(php_esc "$pk")',"
  echo "    'stripe_secret_key'      => '$(php_esc "$sk")',"
  echo "    'stripe_webhook_secret'  => '$(php_esc "$whsec")',"
  echo ""
  echo "    // ÁFA-kulcs a nettó árakra (0.27 = 27%) és a saját domain"
  echo "    'afa_kulcs'              => 0.27,"
  echo "    'site_url'               => 'https://soulsilver.hu',"
  echo ""
  echo "    // Google Ads lead form webhook — ugyanennek kell állnia az Ads"
  echo "    // eszköz \"Kulcs\" mezőjében is, különben a lead-webhook.php eldobja."
  echo "    'google_lead_key'        => '$(php_esc "$glk")',"
  echo "];"
} > "$tmp"
chmod 600 "$tmp"
mv "$tmp" "$CFG"

if [ "$glk_uj" = "1" ]; then
  printf '\n%sÚj Google Ads lead form kulcs készült:%s %s\n' "$Y" "$N" "$glk"
  printf '%sÍrd be a Google Adsben is: Eszközök > a lead form eszköz > Potenciális\n' "$DIM"
  printf 'ügyfelek exportálása > Egyéb adatintegrálási opciók > Kulcs.%s\n' "$N"
fi

# ─────────── ellenőrzés + összefoglaló ───────────
php_ok="?"
if command -v php >/dev/null 2>&1; then
  if php -l "$CFG" >/dev/null 2>&1; then php_ok="rendben"; else php_ok="HIBÁS"; fi
fi
mod="teszt"; [[ "$sk" == *_live_* ]] && mod="ÉLES"
[ -z "$sk" ] && mod="nincs secret kulcs"

osszefoglalo="Kész — a kulcsok mentve.

Fájl: $CFG (jogok: $(stat -f '%Sp' "$CFG" 2>/dev/null || echo '?'), PHP szintaxis: $php_ok)
Mód: $mod

publishable: $(mask "$pk")
secret:      $(mask "$sk")
webhook:     $(mask "$whsec")
resend:      $(mask "$resend")
from → to:   $from → $to

Következő lépés: ugyanezt a config.php-t töltsd fel a Hostinger-re, a public_html FÖLÖTTI mappába (hPanel → File Manager)."

if [ "$MODE" = "gui" ]; then
  valasz="$(osascript 2>/dev/null <<OSA
display dialog ("$(osa_esc "$osszefoglalo")") with title "SOULSILVER — kulcsok" buttons {"Kész", "Mutasd a fájlt"} default button "Kész" with icon note
OSA
)"
  case "$valasz" in *"Mutasd a fájlt"*) open -R "$CFG" ;; esac
else
  printf '\n──────────────────────────────────────────────────────────────\n'
  if [ "$php_ok" = "HIBÁS" ]; then
    printf '%s✗ a config.php hibás:%s\n' "$R" "$N"; php -l "$CFG"; exit 1
  fi
  printf '%s%s%s\n' "$G" "$osszefoglalo" "$N"
fi
