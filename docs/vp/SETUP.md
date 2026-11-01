# Lokální prostředí pro vývoj VP

## Co je potřeba

- PHP 8.4 (rozšíření pdo_mysql, mbstring, gd, intl, zip), Composer, Node.js (kompilace LESS), Git.
- Webový server s rewrite (Apache s `.htaccess`, nebo ekvivalent) a vhost na kořen repa.
- MySQL 8 nebo MariaDB. Pro migrace doporučeno Docker MySQL podle `docs/migrace-db-docker.md`.
- Framework **PES** se instaluje z Packagistu jako 12 balíků `pes/pes-*` (`composer install`), žádné složky vedle repa nejsou potřeba.

## Instalace

```bash
git clone https://github.com/grafiaware/web.git && cd web
git checkout master-vp
composer install
npm install
npm run less:build
```

## Aktivní site

V `app/Site/active-site.php` nastavit `return 'NajdiSi';` a v `local/site/site-name.less` odpovídající site.
**Tyto dva soubory necommitovat.** (V `master` a `master-vp` je teď commitnutá site Grafia.)

## Databáze

Site NajdiSi používá tři DB: `najdisi` (red / CMS), `single_login` (auth), `events`.
Připojení je v `app/Site/NajdiSi/Configuration{Red,Auth,Events}.php` (vývoj: `127.0.0.1:3306`).

1. Spustit MySQL (Docker: `docker compose -f docker-compose.migrations.yml up -d`).
2. Naimportovat dumpy (baseline nebo anonymizované, viz `docs/vp/MIGRACE.md`, sekce Dumpy a import).
   Import s `--default-character-set=utf8mb4` (nebo `SET NAMES utf8mb4;` na začátku dumpu). Dumpy s osobními údaji necommitovat.
   Dumpy jen se strukturou nejsou seřazené podle cizích klíčů: importovat s `SET FOREIGN_KEY_CHECKS=0;` na začátku.
3. Otevřít `/build` → u každé DB **Baseline 2025** (pokud ji dump ještě nemá) → **Spusť migrace**.

Pozor: struktura DB `events` na hostingu je starší než vývojová (tabulka `login`).
Srovnává ji migrace `events/20260104000003_alter_login_pro_synchro.sql` (oprava pro MariaDB v PR #57).

## Známé problémy lokálního běhu

- **`PROJECT_PATH`** (`index.php`) vzniká odečtením `DOCUMENT_ROOT` od složky projektu a končí `/`.
  Když je projekt přímo kořenem webu (nebo podsložkou na Linuxu), vyjde `/` nebo `/web/`, tedy absolutní cesta od kořene disku,
  a šablony, které ji používají, se nenajdou (`paticka.php` → `layout/gdpr/gdpr.php`, `red/multipage/carousel.php`).
  Na Windows (XAMPP) se cesty se `\` a `/` nespárují, PROJECT_PATH zůstane plná cesta a vše funguje. Nezávisí na jménu stroje.

## Testy

```bash
vendor/bin/phpunit -c tests/phpunit.xml --testsuite unit
vendor/bin/phpunit -c tests/phpunit.xml --testsuite integration
```

Integrační testy potřebují DB `single_login`, `events` a `najdisi` se strukturou a účty z vývojové konfigurace:
uživatele z `docker/migrations/init/01_create_databases.sql` a navíc `events_everyone` / `events_everyone`
s právy na `events` (testy se připojují přes `localhost`, tj. socket). Část testů počítá s daty v DB (číselníky, red obsah).
Mail integrace (`--testsuite mail-integration`) potřebuje `docker-compose.mail-test.yml`.

### E-maily při vývoji — jen fake SMTP

- Všechny site mají `mail.paramsname => 'smtp4dev'` (SMTP na `localhost:25`, nic se nedoručí, e-maily se jen zachytí).
- Lokálně: `docker compose -f docker-compose.mail-test.yml up -d`, zachycené e-maily na http://localhost:5000.
- Cloud (agent): jednoduchý SMTP server v Pythonu (`aiosmtpd`) na `127.0.0.1:25`, e-maily ukládá jako `.eml`.
- Mimo produkční hosting (`PES_RUNNING_ON_PRODUCTION_HOST` = false) jdou pevní příjemci NajdiSi (kopie registrace,
  registrace vystavovatele, testmail) na smyšlené adresy `@najdisi.test`; skutečné adresy jen na produkci.
- `mail.paramsname` pro produkci se zatím přepíná ručně při nasazení (`najdisi`).

## Cloudové prostředí (Claude Code na webu)

Ověřeno 22. 10. a 31. 10. 2026. Kontejner má PHP 8.3 a Docker Hub, PPA ani stahování archivů z GitHubu nejsou dostupné, proto:

1. **PHP 8.4** přeložit ze zdrojů (`git clone --depth 1 --branch php-8.4.<x> https://github.com/php/php-src`,
   build závislosti z apt, `./configure --prefix=/opt/php84 --enable-mbstring --with-openssl --with-curl --with-zlib --with-zip --enable-gd --enable-intl --with-pdo-mysql=mysqlnd --with-mysqli=mysqlnd --with-pdo-sqlite`), cca 10 minut.
   V `php.ini` nastavit `openssl.cafile` a `curl.cainfo` na CA bundle proxy a `pdo_mysql.default_socket=/run/mysqld/mysqld.sock`.
2. **Composer** spouštět s `--prefer-install=source` (balíky se klonují přes git). Balík `phpstan/phpstan` existuje jen jako
   archiv z GitHubu a v cloudu se nestáhne — pro cloud ho lokálně (bez commitu) vyřadit z `composer.json` a `composer.lock`.
3. **DB:** `apt-get install mariadb-server`, spustit `mysqld_safe`, založit DB a účty jako výše, dumpy importovat s `--default-character-set=utf8mb4`.

Výsledek ověření: aplikace naběhne (bootstrap PES z Packagistu, routing, připojení k DB), unit i integrační testy se spustí.
Celý web proti reálným datům potřebuje dump DB red (`najdisi`).

## Agenti

Claude, Cursor i další agenti čtou `AGENTS.md` (Claude přes `CLAUDE.md`, Cursor přes `.cursor/rules/agents.mdc`).
