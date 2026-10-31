# Migrace DB v balících VP

Systém verzovaných migrací (PR #36, #56): soubory v `migrations/{red|auth|events}/`,
žurnál `schema_migrations` v každé cílové DB, spouštění z Build panelu (`/build`), produkční baseline `2025`.
Obecný popis je v `migrations/README.md` a `docs/migrace-db-docker.md`.

Jména databází se liší podle site a prostředí, proto **SQL nikdy neobsahuje jméno databáze (schématu)**;
cílovou DB určuje jen složka (`red/`, `auth/`, `events/`) a připojení z konfigurace site.
Pro NajdiSi: vývoj `najdisi`, `single_login`, `events`; hosting má jména generovaná (viz `app/Site/NajdiSi/Configuration*.php`).
Tento soubor doplňuje pravidla pro balíky Veletrhu práce.

## Kam a jak

- DB auth (`single_login`) → `migrations/auth/`, DB events → `migrations/events/`.
- Název: `YYYYMMDDHHMMSS_<kod>_<popis>.sql`, např. `20261020120000_d1_create_lottery.sql`.
  Kód balíku v názvu spojuje migraci se zadáním.
- Jedna logická změna = jeden soubor. Soubor, který už běžel na produkci, se nemění; oprava = nová migrace.
  Migraci, která na produkci ještě neproběhla, lze po dohodě opravit přímo (tak se opravila Z4, #39).

## Kompatibilita MySQL + MariaDB

Produkce (hosting) běží na MariaDB, vývoj na MySQL 8. Každý skript musí projít na obou.

- Nepoužívat: `ADD COLUMN IF NOT EXISTS`, `DROP COLUMN IF EXISTS`, `DROP FOREIGN KEY IF EXISTS`,
  `DROP INDEX IF EXISTS` (jen MariaDB); `CREATE ... IF NOT EXISTS` u indexů; funkční indexy; `CHECK` s chováním lišícím se mezi verzemi;
  JSON funkce mimo společnou podmnožinu.
- Bez pevného jména databáze v SQL (`ALTER TABLE events.login` ne, `ALTER TABLE login` ano) —
  runner je připojený k cílové DB a na hostingu se databáze jmenují jinak.
- Nové tabulky: `ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci`.
- Sloupce s přihlašovacím jménem: `VARCHAR(100)` jako `login.login_name` v DB events, stejná znaková sada jako `login`.
- **Změna sloupce, na který vedou cizí klíče:** nejdřív cizí klíče zrušit, pak sloupec změnit, pak klíče znovu založit.
  MariaDB změnu odmítne i při `SET FOREIGN_KEY_CHECKS = 0` (chyba 1833), MySQL 8 ji projde — proto vždy testovat i na MariaDB.

## Omezení runneru

`PdoSqlExecutor` dělí skript podle `;` a odstraňuje komentáře `--` a `/* */`. Proto:

- žádné uložené procedury, triggery ani `DELIMITER`,
- žádný `;` ani `--` uvnitř řetězců,
- DDL v MySQL/MariaDB dělá implicitní commit — skript, který spadne v půlce, nechá DB v mezistavu.
  Proto malé soubory, příkazy seřazené tak, aby šly po opravě dokončit, a otestované na kopii produkční struktury.

## Postup v balíku

1. Migraci ověřit na DB obnovené z dumpu (`docker/migrations/dumps/`, viz `docs/migrace-db-docker.md`) na MySQL 8 i MariaDB:
   baseline 2025 → spustit migrace → aplikace funguje. Pro změny, které na hostingu ještě neproběhly, vrátit DB do stavu hostingu.
2. V PR uvést seznam migrací a jejich dopad na data.
3. Kód musí fungovat i před spuštěním migrace na produkci, pokud je funkce vypnutá přepínačem
   (nejdřív přidávat, až později odebírat).
4. Při vydání: zálohovat DB → `/build` na produkci → spustit migrace → zapnout přepínač.

## Dumpy a import

- Dumpy ve stavu **baseline** (31. 10. 2026, po migracích lokálně v Dockeru) má Petr ve složce `Veletrh prace\sql\dumps baseline\`
  (`najdisi.sql`, `single_login.sql`, `events.sql`). Obsahují osobní údaje — **do repa nepatří**.
  Anonymizované dumpy z aktuální produkce (před migrací) jsou úkol Z10 (#58).
- Dumpy nemají `SET NAMES`; tabulky mají znakovou sadu definovanou správně. Klient MariaDB má výchozí `latin1`,
  takže import padá na „Data too long“ (čeština). Řešení: importovat s `--default-character-set=utf8mb4`,
  nebo na začátek dumpu přidat `SET NAMES utf8mb4;`. Klient MySQL 8 má výchozí `utf8mb4`, proto tam import projde.

## Stav žurnálů v dumpech baseline (31. 10. 2026)

| DB | Žurnál | Čeká |
|---|---|---|
| events | `2025`, `20260104000003_alter_login_pro_synchro` | — (na hostingu čeká `alter_login_pro_synchro`, opraveno v PR #57) |
| najdisi (red) | `2025`, `20260101000013` … `16` | `20260101000017_drop_pomocne_tabulky` |
| single_login (auth) | prázdný (žádná změna zatím neproběhla) | — |
