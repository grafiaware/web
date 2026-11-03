# Baseline dumpy 2025

Společný start schématu (produkční roční verze **2025**) pro rebuild CLI / Docker.

| Soubor | Target | Obsah | Zdroj (lokálně, gitignore) |
|--------|--------|-------|----------------------------|
| `red.sql` | red | **jen struktura** (+ `DROP TABLE IF EXISTS`) | `dumps_produkce/red_najdisi.sql` |
| `auth.sql` | auth | struktura + seed | `dumps_produkce/login.sql` |
| `events.sql` | events | struktura + seed | `dumps_produkce/events.sql` |

## Pravidla

- **Bez** tabulky `schema_migrations` — žurnál zapisuje rebuild CLI (`Baseline 2025` + migrate).
- **Bez** schema kvalifikátorů (`db`.`table`) a bez `USE` / `CREATE DATABASE`.
- Plný red dump s CMS daty zůstává v `docker/migrations/dumps/` (gitignore); pro rebuild schema stačí struktura.

## Regenerace

```bash
php scripts/prepare-baseline-dumps.php
```

Vyžaduje přítomnost souborů v `docker/migrations/dumps/dumps_produkce/`.

## Použití

```bash
php scripts/db-rebuild.php --target=all --yes
```

Viz `docs/migrace-db-docker.md` (sekce Rebuild CLI).
