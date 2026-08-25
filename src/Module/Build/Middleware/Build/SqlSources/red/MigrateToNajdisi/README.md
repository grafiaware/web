# Migrate red DB schema → NajdiSi baseline

Baseline dump: `_db_dumps/najdisi_structure.sql`  
Historical alter steps (inspiration): `../AlterRedDatabase/2025`, `../AlterRedDatabase/2026`

## Auth / Events

Only one structure dump each (`single_login_structure.sql`, `events_structure.sql`).  
There is nothing to migrate between sites for these two DBs — treat them as shared module databases.  
If a deployment has a different auth/events schema, take a structure dump and diff against these files.

## Red sites (age → migration script)

| Site | Dump | Script | Distance from NajdiSi |
|------|------|--------|------------------------|
| NajdiSi | `najdisi_structure.sql` | — | baseline |
| Grafia | `gr_upgrade_structure.sql` | `grafia_to_najdisi.sql` | small (mainly `menu_item` PK/FK) |
| Otevřené ateliéry | `oa_upgrade_structure.sql` | `oa_to_najdisi.sql` | medium (+ old `static`, `prettyuri`) |
| Veletrh práce | `veletrhprace_structure.sql` | `veletrhprace_to_najdisi.sql` | larger (+ `menu_adjlist`, keep VP-only tables) |
| Týden zdraví | `tydenzdravieu_structure.sql` | `tydenzdravi_to_najdisi.sql` | full 2025/2026 path |

## How to run

1. Backup the target database.
2. Run the **PRECHECK** queries at the top of the script; fix any reported problems first.
3. Run the rest of the script in a transaction where possible (`START TRANSACTION` / `COMMIT`). Some DDL in MySQL causes implicit commit — still run in order, once.
4. Re-dump structure and compare to `najdisi_structure.sql`.

## Intentional leftovers (do not drop)

- **Grafia / OA:** `list_uid`, `stranky*`.`flag_new` — legacy conversion artefacts
- **VP:** `enrolled`, `visitor_data`, `visitor_data_post` — dropped by `veletrhprace_to_najdisi.sql` (legacy data lives in events module DB)
- **Legacy tables** `stranky`, `stranky_innodb`, `menu_item_test` — used only by Build convert; not required for current red runtime; scripts do not create them on VP/TZ

## Data safety

Scripts prefer `ALTER` / `CREATE TABLE IF NOT EXISTS` / `INSERT IGNORE`.  
They do **not** `DROP TABLE` with live content except where an obsolete column is removed after copying (`static.folded` / `static.editor` → `creator`).
