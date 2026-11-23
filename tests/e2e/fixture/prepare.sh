#!/usr/bin/env bash
# Fixture první vlny Z14. Klikací testy nespouští.
#
# Z kořene repa nebo odkudkoli:
#   bash tests/e2e/fixture/prepare.sh              # schéma, pokud chybí, a migrace přes /build
#   bash tests/e2e/fixture/prepare.sh --reset      # docker compose down -v a znovu
#   bash tests/e2e/fixture/prepare.sh --dump DIR   # volitelný dump mimo repo, pak /build
#   bash tests/e2e/fixture/prepare.sh --flags on|off|restore
#
# Seed účtů pro vrstvy C a D tahle vlna nedělá.
# Na Windows: Git Bash nebo WSL. Přepínače v gitu zůstávají vypnuté (restore).

set -euo pipefail

FIXTURE_DIR=$(cd "$(dirname "$0")" && pwd)
ROOT=$(cd "$FIXTURE_DIR/../../.." && pwd)
cd "$ROOT"

FEATURES="app/Site/NajdiSi/ConfigurationFeatures.php"
BACKUP="$FIXTURE_DIR/.features.php.bak"
RESET=0
DUMP=""
FLAGS=""

while [[ $# -gt 0 ]]; do
  case "$1" in
    --reset) RESET=1; shift ;;
    --dump) DUMP="${2:-}"; shift 2 ;;
    --flags) FLAGS="${2:-}"; shift 2 ;;
    *) echo "Neznámý argument: $1" >&2; exit 2 ;;
  esac
done

sql_client() {
  if docker compose exec -T db mariadb --version >/dev/null 2>&1; then
    echo mariadb
  else
    echo mysql
  fi
}

wait_http() {
  local i code
  for i in $(seq 1 60); do
    code=$(docker compose exec -T web curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1/ || true)
    if [[ "$code" =~ ^[0-9]+$ && "$code" != "000" ]]; then
      return 0
    fi
    sleep 2
  done
  echo "Web v kontejneru neodpověděl na http://127.0.0.1/." >&2
  exit 1
}

reload_web_php() {
  if docker compose ps --status running --services 2>/dev/null | grep -qx web; then
    docker compose exec -T web apachectl graceful >/dev/null 2>&1 || true
    sleep 3
  fi
}

apply_flags() {
  local mode="$1"
  if [[ "$mode" == "restore" ]]; then
    if [[ -f "$BACKUP" ]]; then
      cp "$BACKUP" "$FEATURES"
      rm -f "$BACKUP"
      reload_web_php
    fi
    return 0
  fi
  if [[ "$mode" != "on" && "$mode" != "off" ]]; then
    echo "--flags chce on, off nebo restore." >&2
    exit 2
  fi
  if [[ ! -f "$BACKUP" ]]; then
    cp "$FEATURES" "$BACKUP"
  fi
  python3 "$FIXTURE_DIR/set-flags.py" "$FEATURES" "$mode"
  reload_web_php
}

post_build() {
  local action="$1" target="$2" html
  html=$(docker compose exec -T web curl -fsS -X POST "http://127.0.0.1/build/migrations/${action}" --data "target=${target}")
  if grep -q 'Blokováno' <<<"$html"; then
    echo "/build ${action} ${target} je blokované (struktura nesedí na baseline)." >&2
    exit 1
  fi
  printf '%s' "$html"
}

assert_no_pending() {
  local target="$1" html
  html=$(post_build status "$target")
  if ! grep -q 'Čekající soubory (pending) (0)' <<<"$html"; then
    echo "Po fixture má ${target} pořád pending migrace." >&2
    exit 1
  fi
}

pick_dump() {
  local dir="$1" name
  shift
  for name in "$@"; do
    if [[ -f "$dir/$name" ]]; then
      echo "$dir/$name"
      return 0
    fi
  done
  return 1
}

import_dump() {
  local dir="$1" client auth events red
  client=$(sql_client)
  auth=$(pick_dump "$dir" auth.sql single_login.sql) || { echo "V dumpu chybí auth.sql nebo single_login.sql." >&2; exit 1; }
  events=$(pick_dump "$dir" events.sql) || { echo "V dumpu chybí events.sql." >&2; exit 1; }
  red=$(pick_dump "$dir" red.sql najdisi.sql) || { echo "V dumpu chybí red.sql nebo najdisi.sql." >&2; exit 1; }
  docker compose exec -T db "$client" -uroot -proot --default-character-set=utf8mb4 single_login < "$auth"
  docker compose exec -T db "$client" -uroot -proot --default-character-set=utf8mb4 events < "$events"
  docker compose exec -T db "$client" -uroot -proot --default-character-set=utf8mb4 najdisi < "$red"
}

table_count() {
  local client schema table
  client=$(sql_client)
  schema="$1"
  table="$2"
  docker compose exec -T db "$client" -uroot -proot -N -e \
    "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${schema}' AND table_name='${table}'"
}

if [[ -n "$FLAGS" ]]; then
  apply_flags "$FLAGS"
  exit 0
fi

if [[ "$RESET" -eq 1 ]]; then
  docker compose down -v
fi

docker compose up -d --wait
wait_http

site=$(docker compose exec -T web php -r 'echo require "app/Site/active-site.php";')
if [[ "$site" != "NajdiSi" ]]; then
  echo "Aktivní site je '${site}'. E2E první vlny čeká NajdiSi (soubor neměňte v gitu)." >&2
  exit 1
fi

if ! docker compose exec -T web test -f vendor/autoload.php; then
  echo "Chybí vendor/. V kontejneru: docker compose exec web composer install" >&2
  exit 1
fi

if [[ -n "$DUMP" ]]; then
  import_dump "$DUMP"
else
  reg=$(table_count single_login registration || echo 0)
  if [[ "${reg//[[:space:]]/}" == "0" ]]; then
    docker compose exec -T web php scripts/db-rebuild.php --target=all --yes
  fi
fi

for target in auth events red; do
  post_build migrate "$target" >/dev/null
  assert_no_pending "$target"
done

echo "Fixture: auth, events a red nemají pending migrace. Seed účtů pro vrstvy C a D první vlna nedělá."
