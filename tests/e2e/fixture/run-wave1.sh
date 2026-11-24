#!/usr/bin/env bash
# První vlna Z14: fixture, pak Playwright A + B se zapnutými přepínači, pak C1/C4 s vypnutými.
# Přepínače se na konci vrátí (i když test spadne). Do gitu se nezapisují.
#
#   bash tests/e2e/fixture/run-wave1.sh
#   bash tests/e2e/fixture/run-wave1.sh --reset

set -euo pipefail

FIXTURE_DIR=$(cd "$(dirname "$0")" && pwd)
E2E_DIR=$(cd "$FIXTURE_DIR/.." && pwd)
PREPARE="$FIXTURE_DIR/prepare.sh"

restore() {
  bash "$PREPARE" --flags restore || true
}
trap restore EXIT

args=()
if [[ "${1:-}" == "--reset" ]]; then
  args+=(--reset)
fi

bash "$PREPARE" "${args[@]}"

bash "$PREPARE" --flags on
(
  cd "$E2E_DIR"
  E2E_AUTH_FLAGS=on npx playwright test specs/smoke.spec.ts specs/b-auth.spec.ts
)

bash "$PREPARE" --flags off
(
  cd "$E2E_DIR"
  E2E_AUTH_FLAGS=off npx playwright test specs/b-auth.spec.ts
)
