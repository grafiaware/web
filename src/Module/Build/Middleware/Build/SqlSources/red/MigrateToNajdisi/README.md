# Migrate red DB schema → NajdiSi baseline

Baseline dump: `_db_dumps/najdisi_structure.sql`  
Historical alter steps (source of truth / inspiration): `../AlterRedDatabase/2025`, `../AlterRedDatabase/2026`

## Strategy: kompletní migrační skript vs. AlterRedDatabase

**Pro nasazení na konkrétní site používej kompletní skript v této složce** (`oa_to_najdisi.sql`, …), ne surové soubory z `AlterRedDatabase`.

Důvody:

1. **AlterRedDatabase skripty nejsou idempotentní** — většina `CREATE TABLE` / `DROP` / `ALTER` při druhém spuštění spadne. Hostingový dump OA už má většinu 2025 změn hotových.
2. **`2026/13_drop_and_create_new_static.sql` maže data** (`DROP TABLE static`) — na OA/VP se starými řádky ve `static` (path) je nebezpečný. Migrační skript dělá bezpečný `ALTER` + `INSERT` chybějících řádků.
3. **Historické skripty jsou často neúplné vůči NajdiSi** (např. `10_alter_menu_item_PK` nemění FK/nullable `uid_fk`; některé mají site-specific komentáře).
4. **Pořadí a mezikroky** (seed `menu_item_api`, mapování `type_fk` → api, …) jsou ve starších skriptech rozházené; kompletní skript je sestaven podle diffu dumpů.

`AlterRedDatabase` ber jako **dokumentaci historie změn** a zdroj inspirace. Kompletní skript = bezpečná, data-preserving cesta z daného dump stavu na NajdiSi.

---

## Auth / Events

Jen po jednom structure dump (`single_login_structure.sql`, `events_structure.sql`).  
Napříč site není co migrovat — společné modulové DB.

---

## Red sites (age → migration script)

| Site | Dump | Script | Distance from NajdiSi |
|------|------|--------|------------------------|
| NajdiSi | `najdisi_structure.sql` | — | baseline |
| Grafia | `gr_upgrade_structure.sql` | `grafia_to_najdisi.sql` | small (mainly `menu_item` PK/FK) |
| Otevřené ateliéry | `oa_upgrade_structure.sql` | `oa_to_najdisi.sql` | medium (+ old `static`, `prettyuri`) |
| Veletrh práce | `veletrhprace_structure.sql` | `veletrhprace_to_najdisi.sql` | larger (+ `menu_adjlist`; drop VP-only tables) |
| Týden zdraví | `tydenzdravieu_structure.sql` | `tydenzdravi_to_najdisi.sql` | full 2025/2026 path |

---

## OA hosting dump ≈ stav před koncem 2025

### Už aplikováno (v `oa_upgrade_structure.sql` je) — **NESPOUŠTĚT znovu**

| AlterRedDatabase | Stav v OA dump |
|------------------|----------------|
| `2025/01_create_static` | ano, ale **stará** podoba (`path`/`folded`/`editor`) |
| `2025/02_create_article` | ano |
| `2025/03_create_item_actions` | ano (finální PK) |
| `2025/04` + `07` paper_content → paper_section | ano |
| `2025/05` + `06` multipage | ano |
| `2025/08` → `12` asset / menu_item_asset | ano (tvar po 12) |
| `2025/11_add_menu_item_api` | ano (`api_*` + tabulka `menu_item_api`) |

### Ještě chybí vůči NajdiSi

| AlterRedDatabase | Co dělat |
|------------------|----------|
| `2025/10_alter_menu_item_PK` | Spustit **jen jako součást** `oa_to_najdisi.sql` (tam je doplněno o `uid_fk NOT NULL` + hierarchy/language FK). Samotný 10 nestačí. |
| `2026/13_drop_and_create_new_static` | **Nespouštět** (`DROP` ztratí path). Nahrazeno ALTER v `oa_to_najdisi.sql`. |
| `2026/14_inset_into_static_from_menu_item` | Ano — seed řádků pro `api_generator_fk='static'`. V migraci i jako `repair_static_from_menu_item.sql`. |
| `2026/15_alter_menu_item,_pretty_uri` | Ano (`varchar(100)` → `200`). |
| `2026/16_repair_menu_root_names` | Volitelně — OA config má stále `rootName => 'menu_vertical'`. Spusť 16 **jen** spolu se změnou config na `'menu vertical'`. |

Další drobnosti jen v `oa_to_najdisi.sql` (nejsou samostatným Alter souborem): `menu_root` FK `ON DELETE CASCADE`.

### Doporučený postup pro OA

1. Backup DB.
2. Pokud ještě **nebyla** migrace: spusť celý `oa_to_najdisi.sql` (už obsahuje 10+13-safe+14+15).
3. Pokud migrace **už běžela** a `static` je prázdná: spusť jen `repair_static_from_menu_item.sql`.
4. `2026/16` zatím nech / spusť až sjednotíš config s NajdiSi názvy rootů.
5. Structure dump → diff vůči `najdisi_structure.sql`.

Pokud bys chtěl jít „ručně“ AlterRedDatabase bez kompletního skriptu, na čistém OA dump stačí v tomto pořadí:

1. upravený **10** (+ FK jako v migraci)  
2. **ne** 13 — místo toho ALTER static z `oa_to_najdisi`  
3. **14** (ideálně s `NOT EXISTS`)  
4. **15**  
5. **16** jen s config změnou  

To je ale přesně to, co `oa_to_najdisi.sql` už skládá — kompletní skript je bezpečnější.

---

## How to run (obecně)

1. Backup the target database.
2. Run the **PRECHECK** queries at the top of the site script; fix problems first.
3. Run the script once in order (MySQL DDL often implicit-commits).
4. Re-dump structure and compare to `najdisi_structure.sql`.

## Intentional leftovers (do not drop)

- **Grafia / OA:** `list_uid`, `stranky*`.`flag_new` — legacy conversion artefacts
- **VP:** `enrolled`, `visitor_data`, `visitor_data_post` — dropped by `veletrhprace_to_najdisi.sql`
- **Legacy** `stranky`, `stranky_innodb`, `menu_item_test` — Build convert only; not required for current red runtime

## Data safety

Scripts prefer `ALTER` / `CREATE TABLE IF NOT EXISTS` / `INSERT … NOT EXISTS`.  
They do **not** `DROP TABLE` with live content except dropping obsolete VP-only tables or columns after copy (`static.folded` / `static.editor` → `creator`).

### Already migrated OA/VP with empty `static`

Run once: `repair_static_from_menu_item.sql`
