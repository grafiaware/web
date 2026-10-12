# Migrace DB v balících VP

Systém verzovaných migrací zavádí PR #36 (`master-migrations`): soubory v `migrations/{red|auth|events}/`,
žurnál `schema_migrations` v každé cílové DB, spouštění z Build panelu (`/build`), produkční baseline `2025`.
Obecný popis je v `migrations/README.md` a `docs/migrace-db-docker.md` (po sloučení PR #36).
Tento soubor doplňuje pravidla pro balíky Veletrhu práce.

## Kam a jak

- DB auth (`single_login`) → `migrations/auth/`, DB events → `migrations/events/`.
- Název: `YYYYMMDDHHMMSS_<kod>_<popis>.sql`, např. `20261020120000_d1_create_lottery.sql`.
  Kód balíku v názvu spojuje migraci se zadáním.
- Jedna logická změna = jeden soubor. Soubor, který už je sloučený v `master-vp`, se nemění; oprava = nová migrace.

## Kompatibilita MySQL + MariaDB

Produkce (hosting) běží na MariaDB, vývoj na MySQL 8. Každý skript musí projít na obou.

- Nepoužívat: `ADD COLUMN IF NOT EXISTS`, `DROP COLUMN IF EXISTS`, `DROP FOREIGN KEY IF EXISTS`,
  `DROP INDEX IF EXISTS` (jen MariaDB); `CREATE ... IF NOT EXISTS` u indexů; funkční indexy; `CHECK` s chováním lišícím se mezi verzemi;
  JSON funkce mimo společnou podmnožinu.
- Bez pevného jména databáze v SQL (`ALTER TABLE events.login` ne, `ALTER TABLE login` ano) —
  runner je připojený k cílové DB a na hostingu se databáze jmenují jinak.
- Nové tabulky: `ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci`.
- Sloupce s přihlašovacím jménem: `VARCHAR(100)` jako `events.login.login_name`, stejná znaková sada jako `login`.

## Omezení runneru

`PdoSqlExecutor` dělí skript podle `;` a odstraňuje komentáře `--` a `/* */`. Proto:

- žádné uložené procedury, triggery ani `DELIMITER`,
- žádný `;` ani `--` uvnitř řetězců,
- DDL v MySQL/MariaDB dělá implicitní commit — skript, který spadne v půlce, nechá DB v mezistavu.
  Proto malé soubory, příkazy seřazené tak, aby šly po opravě dokončit, a otestované na kopii produkční struktury.

## Postup v balíku

1. Migraci ověřit na vývojové DB obnovené z produkčního dumpu (`docker/migrations/dumps/`, viz `docs/migrace-db-docker.md`):
   baseline 2025 → spustit migrace → aplikace funguje.
2. V PR uvést seznam migrací a jejich dopad na data.
3. Kód musí fungovat i před spuštěním migrace na produkci, pokud je funkce vypnutá přepínačem
   (nejdřív přidávat, až později odebírat).
4. Při vydání: zálohovat DB → `/build` na produkci → spustit migrace → zapnout přepínač.
