# Lokální prostředí pro vývoj VP

## Co je potřeba

- PHP 8.4 (rozšíření pdo_mysql, mbstring, gd, intl), Composer, Node.js (kompilace LESS), Git.
- Webový server s rewrite (Apache s `.htaccess`, nebo ekvivalent) a vhost na kořen repa.
- MySQL 8 nebo MariaDB. Pro migrace doporučeno Docker MySQL podle `docs/migrace-db-docker.md` (PR #36).
- Framework **PES** vedle repa: `composer.json` očekává path repozitáře `../pes/pes-application`, `../pes/pes-core`, … `../pes/pes-mail`.
  Zdroj frameworku a přístup k němu domluvit s Petrem.

```
<pracovní složka>/
├─ pes/            ← pes-application, pes-bootstrap, pes-core, pes-database, pes-di, pes-http,
│                    pes-logger, pes-router, pes-session, pes-view, pes-model, pes-mail
└─ web/            ← tento repozitář
```

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
**Tyto dva soubory necommitovat.**

## Databáze

Site NajdiSi používá tři DB: `najdisi` (red / CMS), `single_login` (auth), `events`.
Připojení je v `app/Site/NajdiSi/Configuration{Red,Auth,Events}.php` (vývoj: `127.0.0.1:3306`).

1. Spustit MySQL (Docker: `docker compose -f docker-compose.migrations.yml up -d`).
2. Naimportovat dumpy struktur (případně anonymizovaných dat) z produkce. Dumpy s osobními údaji necommitovat.
3. Otevřít `/build` → u každé DB **Baseline 2025** → **Spusť migrace**.

Pozor: struktura DB `events` na hostingu je starší než vývojová (tabulka `login`).
Srovnává ji migrace `events/20260104000003_alter_login_pro_synchro.sql` z PR #36.

## Testy

```bash
vendor/bin/phpunit -c tests/phpunit.xml --testsuite unit
vendor/bin/phpunit -c tests/phpunit.xml --testsuite integration
```

Integrační testy potřebují testovací DB; mail integrace (`--testsuite mail-integration`) potřebuje `docker-compose.mail-test.yml`.

## Agenti

Claude, Cursor i další agenti čtou `AGENTS.md` (Claude přes `CLAUDE.md`, Cursor přes `.cursor/rules/agents.mdc`).
