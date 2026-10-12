# Verzované SQL migrace

Inkrementální změny MySQL schématu pro cíle **red**, **auth** a **events**.

## Konvence

- Soubor: `{YYYYMMDDHHMMSS}_{slug}.sql`
- `version` v žurnálu = název souboru bez `.sql`
- Žurnál: tabulka `schema_migrations` v cílové databázi
- Spuštění: Build panel (`/build`) → Stav / Spusť / Baseline

## Cíle

| Adresář | Databáze (site config) |
|---------|------------------------|
| `red/` | `ConfigurationRed::dbUpgrade()` |
| `auth/` | `ConfigurationAuth::dbOld()` |
| `events/` | `ConfigurationEvents::dbEvents()` |

## Baseline

Pokud cílová DB už obsahuje změny z historických `SqlSources` ALTER skriptů, jednou spusť **Baseline bez SQL**, pak už jen **Spusť migrace**.

Historické `src/Module/Build/Middleware/Build/SqlSources/` zůstávají archivem / Build make-convert; nové změny patří sem.

Podrobnosti: projektový plán migrace (Agent Store `docs/migrace-db.md`).
