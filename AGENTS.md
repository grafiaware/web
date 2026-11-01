# Pravidla projektu web (grafiaware/web)

Tento soubor čtou lidé i AI agenti (Claude, Cursor, další). Platí pro veškerou práci v repu,
zvlášť pro projekt **Veletrh práce (VP)** popsaný v [`docs/vp/`](docs/vp/README.md).

## Projekt v kostce

- Vlastní PHP framework **PES**: 12 balíků `pes/pes-*` (application, bootstrap, core, database, di, http, logger,
  mail, model, router, session, view) z Packagistu přes `composer install`, PHP 8.4.
- Jeden kód, více webů („sites“): `app/Site/{NajdiSi, Grafia, OtevreneAteliery, VeletrhPrace, TydenZdravi}`.
  Aktivní site se volí v `app/Site/active-site.php`.
- **Produkční Veletrh práce běží jako site NajdiSi.** Deploy = `app/Site/NajdiSi`, `local/site/najdisi`
  (šablony VP v `local/site/najdisi/static/vp`) + společné složky (`src`, `local/site/common`, `public/site/common`).
- Moduly v `src/Module/*`: Auth (DB auth = `single_login`), Events (DB `events`), Red (CMS), Web, Build, Sendmail, Consent…
- Vzor modulu: `Application/Api/Catalog/*RouteCatalog` → `Middleware/*/Controler` → `Model/{Repository, Dao, Hydrator, Entity}`,
  kontejnery v `src/Container`.

## Postup práce (VP)

Úplný popis: [`docs/vp/README.md`](docs/vp/README.md). Zkráceně:

1. Každý pracovní balík (kód např. `Z6`, `A1`, `D1`) má **GitHub issue**. Kdo na něm pracuje, je v issue přiřazený.
   Nezačínej balík, který má přiřazeného někoho jiného.
2. Před kódem vznikne **zadání** `docs/vp/zadani/<kód>.md` podle [`_SABLONA.md`](docs/vp/zadani/_SABLONA.md)
   a schválí ho vlastník projektu (Petr). Bez schváleného zadání se nemění DB.
3. Větev `vp/<kód>-<popis>` z aktuálního `master-vp`. Jeden balík = jedna větev = jeden pull request do `master-vp`.
   Naléhavá oprava produkce: `hotfix/<kód>-<popis>` z `master`, PR do `master` a pak sloučit i do `master-vp`.
4. Před pull requestem: rebase na aktuální `master-vp`, testy zelené, vyplněná šablona PR.
5. Pull request reviduje někdo jiný než autor, slučuje vždy člověk (squash merge).
6. Kdo práci předává, aktualizuje v zadání sekci **Stav a další krok** a pushne větev.

Do `master` se nepushuje přímo. Do `master-vp` standardně přes pull request; přímý push jen na výslovný pokyn Petra
(větev není chráněná, pravidlo je dohoda).

## Pravidla pro kód

- Drž se existujících vzorů modulu, nevymýšlej paralelní architekturu.
- Změna ve sdíleném `src` nesmí změnit chování ostatních sites. Nová funkce VP je mimo NajdiSi vypnutá.
- Každá viditelná nová funkce má **přepínač** v `app/Site/NajdiSi/ConfigurationFeatures.php` s výchozí hodnotou *vypnuto*
  (klíč `vp.<funkce>`). Routy: parametr `$feature` v `RouteDefinition` (vypnutá funkce = 404);
  šablony a kontroléry: `FeatureFlags::fromConfiguration()->isEnabled('vp.<funkce>')`. Popis: `docs/vp/zadani/Z8.md`.
- Texty, hlášky a e-maily česky. E-maily přes sdílené šablony, adresy odesílatelů z konfigurace (ne natvrdo v kontroléru).
- Hesla a tokeny nikdy v čitelné podobě v DB, logu ani e-mailu. Výjimka: stávající dočasné heslo v `registration`
  do potvrzení registrace (záměr kvůli ručnímu dokončení registrace vystavovatele, řeší odložený balík Z6) — nerozšiřovat.
- `app/Site/active-site.php` a `local/site/site-name.less` měň jen lokálně, necommituj je.
- Necommituj dumpy DB s osobními údaji, `vendor/`, `node_modules/`, logy.

## Databáze a migrace

- **SQL vždy tak, aby běželo na MySQL i MariaDB** (produkce MariaDB, vývoj MySQL 8).
  Nepoužívej syntaxi jen jedné z nich (např. `ADD COLUMN IF NOT EXISTS`, `DROP FOREIGN KEY IF EXISTS` umí jen MariaDB).
- Změny schématu jen jako verzované migrace v `migrations/{red|auth|events}/YYYYMMDDHHMMSS_slug.sql`,
  spouštěné z Build panelu (`/build`). Podrobnosti a omezení: [`docs/vp/MIGRACE.md`](docs/vp/MIGRACE.md).
- Nejdřív přidávat, až pozdější verzí odebírat (kód musí fungovat se starou i novou strukturou).
- Nové tabulky v `utf8mb4`. Neměň sloupce, na kterých stojí jiný rozpracovaný balík.

## Testy

- PHPUnit (`tests/phpunit.xml`): sady `unit` a `integration` (mail integrace zvlášť).
- Nová logika = unit test v `tests/Unit`, nové Dao/Repository = integrační test v `tests/Integration`.
- Spuštění: `vendor/bin/phpunit -c tests/phpunit.xml --testsuite unit` (a `integration` proti testovací DB).

## Commity a pull requesty

- Zpráva commitu začíná kódem balíku: `Z8: přepínače funkcí v konfiguraci site`.
- Malé commity, jeden balík na větev, žádné nesouvisející úpravy (formátování celých souborů apod.).
- Pull request: šablona `.github/pull_request_template.md` — odkaz na issue a zadání, migrace, přepínač, postup ověření.
- Agent, který přebírá cizí větev nebo PR: nejdřív přečti zadání, issue a diskusi v PR; změny dávej jako nové commity,
  historii cizí větve nepřepisuj bez domluvy.
