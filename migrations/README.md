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
| `events/` | `ConfigurationEvents::dbEvents()` | alter (`2024*`) + alter26 (`2025*` — už v prod dump; ne `2026*`) |

## Typický scénář

Produkční dump → vývojová DB → **Baseline 2025** (pokud schema odpovídá prod) → **Spusť migrace**.

Historické `SqlSources/` zůstávají archivem.

**Lokální ověření (Docker MySQL + Build panel + self-hosted worker):**  
viz [`docs/migrace-db-docker.md`](../docs/migrace-db-docker.md).

Strategický plán projektu: Agent Store `docs/migrace-db.md`.
