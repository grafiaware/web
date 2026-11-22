# Docker vývojové prostředí (Apache + PHP + DB + SMTP)

Cíl: celý stack v **Docker Desktop**, zdrojáky dál na disku v git repu (IDE beze změny), web z prohlížeče na hostu i z **LAN / mobilu přes HTTP i HTTPS** (QR, WebView).

**Windows (PowerShell / NetBeans, cesta `C:\Apacheroot\web`) — podrobný návod:** [DOCKER-WINDOWS.md](DOCKER-WINDOWS.md)  
Související: [SETUP.md](SETUP.md) (Composer, site, dumpy), [migrace-db-docker.md](../migrace-db-docker.md) (jen DB).

## Co běží

| Služba | Kontejner | Porty (výchozí) | Účel |
|--------|-----------|-----------------|------|
| `web` | Apache + PHP 8.4 | 80, 443 | Aplikace, bind-mount `./` |
| `db` | MariaDB 11.4 | 3306 | `najdisi`, `single_login`, `events` + účty z init SQL |
| `mail` | smtp4dev | 25 (SMTP), 5000 (UI/API) | Fake SMTP — maily se nedoručují |
| `playwright` | (profil `e2e`) | — | Klikací testy |

Konfigurace sites dál míří na `127.0.0.1:3306` a SMTP `localhost:25`. V kontejneru `web` je **socat** přesměruje na služby `db` a `mail`, takže `Configuration*.php` neměníte.

## Rychlý start (Docker Desktop)

Na **Windows** raději celý postup podle [DOCKER-WINDOWS.md](DOCKER-WINDOWS.md) (PowerShell, certifikáty `.ps1`, mobil).

```bash
# 1) Env (volitelné)
cp .env.docker.example .env

# 2) TLS certifikáty (HTTPS pro mobil)
# Windows PowerShell:  .\docker\certs\generate-certs.ps1
./docker/certs/generate-certs.sh
# ideálně s mkcert: https://github.com/FiloSottile/mkcert

# 3) Stack
docker compose up -d --build

# 4) Composer / LESS (na hostu, nebo: docker compose exec web composer install)
composer install
npm install && npm run less:build

# 5) Aktivní site lokálně (necommittovat)
# app/Site/active-site.php → return 'NajdiSi';
# local/site/site-name.less → odpovídající @sitename
```

Ověření:

- http://localhost/ a https://localhost/
- smtp4dev UI: http://localhost:5000
- z telefonu ve stejné Wi‑Fi: `https://<LAN-IP>/` (po instalaci `docker/certs/ca.pem` na telefon)

```bash
docker compose ps
docker compose exec web php -v
docker compose exec db mariadb -uroot -proot -e "SHOW DATABASES;"
```

Import dumpů a `/build`: stejně jako v [SETUP.md](SETUP.md) / [migrace-db-docker.md](../migrace-db-docker.md), jen kontejner DB se jmenuje `grafia-web-db` (compose project `grafia-web`).

## HTTPS, vývojová jména a QR

V `.env` nastavte **jméno tohoto stroje** (výchozí sada: `ps.dev.local`, `vs.dev.local`, `ks.dev.local`):

```env
WEB_PUBLIC_HOST=ps.dev.local
WEB_PUBLIC_SCHEME=https
WEB_DEV_HOSTS=ps.dev.local,vs.dev.local,ks.dev.local
WEB_CERT_HOSTS=localhost,ps.dev.local,vs.dev.local,ks.dev.local
```

- `WEB_PUBLIC_HOST` — jde do QR, PDF a absolutních odkazů v e-mailech (`Web\Service\PublicBaseUrl`), i když prohlížíte přes `localhost`. PHP bere hodnotu z env kontejneru a jako zálohu ze souboru `.env` v kořeni repo.
- `WEB_DEV_HOSTS` — všechna jména týmu v Apache `ServerAlias` a (pokud není `WEB_CERT_HOSTS`) v TLS SAN.
- Mapování jména → LAN IP: DNS/DHCP, nebo aplikace na telefonu.
- Po změně `.env`: `docker compose up -d --force-recreate web` a ověření `docker compose exec web printenv WEB_PUBLIC_HOST`.

1. Certifikáty: `generate-certs.ps1` / `.sh` (načte `.env`, preferujte **mkcert**).
2. Na telefon **nainstalujte** `docker/certs/ca.pem` jako důvěryhodnou CA — [DOCKER-WINDOWS.md](DOCKER-WINDOWS.md) §11.
3. Notebook: `https://ps.dev.local/` · Mobil totéž (po DNS) · nouzově `https://<LAN-IP>/`.

Bez důvěryhodné CA mobilní prohlížeče a WebView často HTTPS odkazy z QR odmítnou.

Porty 80/443 jsou mapované na všechna rozhraní PC (`0.0.0.0` = bind, ne URL) — firewall musí LAN povolit. Na veřejný internet stack nevystavujte.

## Varianty PHP a DB

| Varianta | Příkaz |
|----------|--------|
| PHP 8.4 + MariaDB 11 (výchozí) | `docker compose up -d --build` |
| MySQL 8 | `DB_IMAGE=mysql:8.0 docker compose -f docker-compose.yml -f docker-compose.mysql.yml up -d --build` |
| PHP 8.3 (experiment) | `docker compose -f docker-compose.yml -f docker-compose.php83.yml up -d --build` |

Verze: `.env` → `PHP_VERSION`, `DB_IMAGE` (viz `.env.docker.example`).

**Souběh s `docker-compose.migrations.yml`:** oba chtějí port 3306. Buď plný stack (`docker-compose.yml`), nebo jen migrace-DB — ne obojí najednou.

## Fake SMTP a testy mailů

- UI: http://localhost:5000
- Aplikace: `mail.paramsname => smtp4dev` (už v sites)
- PHPUnit mail integrace: stejně jako dřív (SMTP :25, API :5000)
- Tenký compose jen pro mail: `docker compose -f docker-compose.mail-test.yml up -d` (když neběží plný stack)

## Klikací testy (Playwright)

Adresář `tests/e2e/` — kouřový test + příklad kontroly mailů ve smtp4dev.

Na hostu (stack už běží):

```bash
cd tests/e2e
npm install
npx playwright install chromium   # jednou
PLAYWRIGHT_BASE_URL=https://localhost SMTP4DEV_API_URL=http://localhost:5000 npm test
```

V Dockeru:

```bash
docker compose --profile e2e run --rm playwright
```

(`PLAYWRIGHT_BASE_URL` výchozí v compose míří na službu `web`; upravte v `.env` podle potřeby.)

Scénáře „klikáním“ přidávejte do `tests/e2e/specs/`. Pro kontrolu odeslaných mailů volejte smtp4dev API (`/api/Messages`) — viz `mail-capture.example.spec.ts`.

## PROJECT_PATH v Dockeru

Na Linuxu v kontejneru by klasický výpočet z `DOCUMENT_ROOT` dal `/` a rozbil include šablon (issue Z11).  
`index.php` respektuje `WEB_PROJECT_PATH` (compose + Apache `SetEnv` → `/var/www/html/`).  
Hostí XAMPP s `RewriteBase /web/` se nemění — v kontejneru je overlay `docker/apache/htaccess.dev` (`RewriteBase /`).

## Užitečné příkazy

```bash
docker compose logs -f web
docker compose exec web composer install
docker compose exec web vendor/bin/phpunit -c tests/phpunit.xml --testsuite unit
docker compose down          # data DB zůstanou
docker compose down -v       # smaže volume DB (init SQL znovu)
```

## Soubory

| Cesta | Účel |
|-------|------|
| `docker-compose.yml` | Plný stack |
| `docker-compose.mysql.yml` | Override MySQL 8 |
| `docker-compose.php83.yml` | Override PHP 8.3 |
| `.env.docker.example` | Vzor pro `.env` |
| `docker/php/` | Dockerfile, entrypoint (socat), php.ini |
| `docker/apache/` | vhost HTTP/HTTPS, htaccess.dev |
| `docker/certs/` | generování TLS (`generate-certs.ps1` / `.sh`; necommitovat PEM) |
| `docs/vp/DOCKER-WINDOWS.md` | Podrobný Windows / PowerShell / mobil návod |
| `tests/e2e/` | Playwright |
