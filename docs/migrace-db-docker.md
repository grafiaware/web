# Lokální MySQL (Docker) pro migrace DB

Návod pro **cestu B**: Docker Desktop u vývojáře + volitelně Cursor self-hosted worker.  
Cíl: ověřit verzované migrace (`migrations/`) a Build panel (`/build`) proti lokální MySQL, aniž by se měnil stávající PHP/web stack.

Související soubory v repo:

| Soubor | Účel |
|--------|------|
| `docker-compose.migrations.yml` | Compose služba MySQL |
| `docker/migrations/init/` | SQL při prvním vytvoření volume (DB + účty) |
| `docker/migrations/dumps/` | Sem dávat lokální dumpy (necommittovat citlivá data) |
| `migrations/` | Verzované SQL migrace |
| Agent Store plán | `docs/migrace-db.md` (mimo repo) — strategie baseline / multi-instance |

---

## 1. K čemu kontejnery jsou (a co v nich není)

**Je:** jeden kontejner **MySQL 8** na `127.0.0.1:3306` s databázemi a účty odpovídajícími lokální konfiguraci NajdiSi (`najdisi`, `single_login`, `events`).

**Není:** PHP, Apache/nginx ani celý web. Stávající lokální aplikace (PES, `index.php`, vhost) zůstává jak ji máte — jen se připojí na Docker MySQL místo (nebo vedle) jiné lokální instance.

Proč takhle: v repo už je lokální PHP stack a site konfigurace míří na `127.0.0.1:3306`. Přidání PHP do Compose by hrozilo duplikací a rozbitím cest (`local/`, `vendor/`, PES path repo). MySQL kontejner je izolovaná, resetovatelná DB pro migrace.

> Pokud už na portu **3306** běží jiná MySQL, buď ji zastavte, nebo v compose změňte mapování (např. `"3307:3306"`) a upravte port v site konfiguraci.

---

## 2. Co spustit jako první (vývojář)

1. Nainstalujte a spusťte **Docker Desktop** (nebo ekvivalent s `docker compose`).
2. V kořeni repo:

```bash
docker compose -f docker-compose.migrations.yml up -d
```

3. Počkejte na healthy stav:

```bash
docker compose -f docker-compose.migrations.yml ps
```

4. Ověřte připojení (volitelně):

```bash
docker compose -f docker-compose.migrations.yml exec mysql \
  mysql -uroot -proot -e "SHOW DATABASES;"
```

Měli byste vidět mimo jiné `najdisi`, `single_login`, `events`.

5. Spusťte **stávající lokální PHP/web** tak, jak obvykle (active site např. NajdiSi). Host DB je `127.0.0.1`, port `3306` — sedí s `ConfigurationRed` / `Auth` / `Events`.

Heslo root v kontejneru: `root` / `root` (jen pro lokál, ne pro produkci).

Zastavení / kompletní smazání dat volume:

```bash
docker compose -f docker-compose.migrations.yml down      # kontejner pryč, data zůstanou
docker compose -f docker-compose.migrations.yml down -v   # i volume = čistý start včetně init SQL
```

---

## 3. Prod dump → local

Typický scénář migrací: **schema (a data) z produkční verze na vývoj**.

1. Z produkce (nebo anonymizovaného stagingu) exportujte dump cílové DB (`najdisi` / `single_login` / `events` — podle toho, co testujete).
2. Soubor uložte např. do `docker/migrations/dumps/` (do gitu nedávejte osobní údaje).
3. Import do běžícího kontejneru:

```bash
# příklad: red DB
docker compose -f docker-compose.migrations.yml exec -T mysql \
  mysql -uroot -proot najdisi < docker/migrations/dumps/najdisi.sql
```

4. Po importu je žurnál `schema_migrations` buď:
   - už v dumpu (stav z produkce), nebo
   - chybí → použijete **Baseline 2025** (viz níže).

Init skript (`docker/migrations/init/`) se při importu do existujícího volume **znovu nespouští** — běží jen při prvním vytvoření volume.

---

## 4. Baseline 2025 a migrate

Konvence (viz také `migrations/README.md`):

- **Release baseline** v žurnálu: rok `YYYY`, produkční výchozí **`2025`** (ne `2025.x`, ne timestamp souboru).
- Souborové migrace: `migrations/{red|auth|events}/YYYYMMDDHHMMSS_slug.sql`.
- Marker `2025` znamená: „schema odpovídá roční verzi 2025“ — soubory s rokem ≤ 2025 se považují za pokryté; novější (2026…) zůstanou **pending**.

### V Build panelu (`/build`)

1. Otevřete Build panel lokální aplikace (GET `/build` podle vašeho vhostu / base path).
2. Sekce **Migrace databází**:
   - **Stav migrací (red|auth|events)** — co je v žurnálu, co je pending.
   - **Baseline 2025 bez SQL** — zapíše marker `2025` do `schema_migrations` **té připojené DB**, nespouští ALTER.
   - **Spusť migrace** — vykoná pending SQL soubory a zapíše jejich verze.

Po baseline 2025 na DB s produkčním schema typicky zbývají jen migrace z roku 2026+ (pokud v repo jsou).

Auth: verzujeme **od teď** — na „schema jak je“ stačí Baseline 2025 bez historických skriptů.

---

## 5. Multi-server a žurnál per DB

Žurnál žije **uvnitř MySQL databáze** (`schema_migrations`), ne na app serveru.

- Více app serverů → **jedna** DB = jeden žurnál (v pořádku).
- Více kopií DB (prod, váš Docker, kolegův Docker) = **více žurnálů**. Baseline/migrate na každé kopii zvlášť.
- Docker MySQL u vás je jedna instance; stav se nesdílí s produkcí, dokud neimportujete dump (nebo ručně nenastavíte marker).

Proto status v Build panelu vždy říká stav **právě připojené** DB z site konfigurace.

---

## 6. Cursor self-hosted worker

Cloud Agent v Cursor cloudu **nemá** váš Docker Desktop. Aby agent mohl bez interakce spouštět `docker compose` a (případně) sahat na lokální Build, musí běžet **self-hosted worker na vašem stroji**.

### Co nainstalovat / spustit

1. **Docker Desktop** — musí běžet (ikona Docker aktivní), jinak `docker compose` z agenta selže.
2. **Cursor CLI** (pokud ještě není):

```bash
curl https://cursor.com/install -fsS | bash
```

3. Přihlášení (jednou):

```bash
agent login
```

(Na stroji bez prohlížeče: `agent worker start --api-key "$CURSOR_API_KEY" --name "web-migrations"`.)

4. Z kořene tohoto repo spusťte worker a **nechte ho běžet**:

```bash
cd /cesta/k/web
agent worker start --name "web-migrations"
```

**Finální příkaz (oficiální Agent CLI):** `agent worker start`  
Alternativa ze stejného CLI: `cursor agent worker start` (ekvivalent).

**Nepoužívejte** `cursor worker start` — to je editor CLI, Cursor se jen otevře a **worker se nespustí** ([forum](https://forum.cursor.com/t/worker-start-does-not-start/173384), [My Machines](https://cursor.com/docs/cloud-agent/self-hosted/my-machines)).

Dokumentace: [Self-Hosted Machines](https://cursor.com/docs/cloud-agent/self-hosted) / [My Machines](https://cursor.com/docs/cloud-agent/self-hosted/my-machines).  
Team pool: `agent worker --pool start` + service account klíč.

### Důležité podmínky

| Podmínka | Proč |
|---------|------|
| Stroj **zapnutý**, worker proces **běží** | Agent posílá tool cally na worker; po uspání notebooku / ukončení terminalu worker zmizí |
| Docker Desktop **běží** | Compose a MySQL healthcheck |
| Worker startovaný z **repo web** (nebo `--worker-dir`) | Agent vidí stejné soubory / compose |
| Outbound HTTPS | Worker se připojuje ven; inbound porty netřeba |

S tímto setupem může agent např. spustit `docker compose -f docker-compose.migrations.yml up -d`, zkontrolovat `ps`, případně připravit import dumpu. Build panel (HTTP) ověříte v prohlížeči proti lokálnímu PHP, nebo agentem pokud má přístup k vašemu lokálnímu URL.

---

## 7. Rychlá kontrolní sekvence

```text
Docker Desktop zapnutý
  → docker compose -f docker-compose.migrations.yml up -d
  → (volitelně) import dumpu
  → lokální PHP/web běží, site = NajdiSi (nebo jiná s DB na 127.0.0.1:3306)
  → prohlížeč: /build → Stav migrací
  → Baseline 2025 (jednou na čisté / dump bez žurnálu)
  → Spusť migrace
  → znovu Stav — pending prázdné (nebo jen očekávané nové soubory)
```

Pro agenta navíc: `agent worker start` v repo + běžící Docker.
