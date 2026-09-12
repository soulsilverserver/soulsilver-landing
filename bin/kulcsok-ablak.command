#!/bin/bash
# Duplán kattintható indító: felugró ablakokban kéri be a kulcsokat.
cd "$(dirname "$0")" || exit 1
exec bash ./setup-keys.sh --ablak
