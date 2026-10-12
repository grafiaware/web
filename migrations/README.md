# Verzované SQL migrace

Inkrementální změny MySQL schématu pro cíle **red**, **auth** a **events**.

## Konvence

| Typ | Formát | Příklad |
|-----|--------|---------|
| Release baseline | `YYYY` | `2025` (produkční výchozí) |
| Soubor | `{YYYYMMDDHHMMSS}_{slug}.sql` | `20260101000015_alter_menu_item_pretty_uri` |

- Žurnál: tabulka `schema_migrations` **v cílové DB** (stav per DB instance, ne per app server).
- Baseline `2025` pokryje soubory s rokem ≤ 2025; novější zůstanou pending.
- Spuštění: Build panel (`/build`) — Stav / Spusť / Baseline 2025.

## Cíle

| Adresář | Databáze (site config) | Poznámka |
|---------|------------------------|----------|
| `red/` | `ConfigurationRed::dbUpgrade()` | počáteční sada z AlterRedDatabase |
| `auth/` | `ConfigurationAuth::dbOld()` | **od teď** — bez dohánění historie |
| `events/` | `ConfigurationEvents::dbEvents()` | alter + alter26 |

## Typický scénář

Produkční dump → vývojová DB → **Baseline 2025** (pokud schema odpovídá prod) → **Spusť migrace**.

Historické `SqlSources/` zůstávají archivem. Podrobnosti: Agent Store `docs/migrace-db.md`.
