# Návod: Docker Desktop na Windows (PowerShell) — krok za krokem

Pro kolegu, který má **Docker Desktop** a potřebuje spustit web (Apache + PHP + MariaDB + fake SMTP) včetně **HTTPS z mobilu** (QR).  
Technický přehled: [DOCKER.md](DOCKER.md).

**Týmový standard (NetBeans):** projekt leží v **`C:\Apacheroot\web`**. V této složce už je `.git` (NetBeans s gitem pracuje přímo v projektu).  
Příkazy níže pište do **Windows PowerShell** (ne do CMD), pokud není řečeno jinak.

> **Cursor / VS Code (jen Petr):** jiné lokální repo, typicky `…\git_repo_for_vscode\web`. Stejné příkazy, jen místo `C:\Apacheroot\web` použijte svou cestu.

---

## 0. Co musí být nainstalované předem

| Software | Povinné? | Poznámka |
|----------|----------|----------|
| [Docker Desktop pro Windows](https://www.docker.com/products/docker-desktop/) | **ano** | Po instalaci **spusťte** Docker Desktop a počkejte, až ikona ve Windows ukáže „Engine running“. |
| NetBeans + projekt v `C:\Apacheroot\web` | **ano** (tým) | Git v projektu řeší NetBeans — **Git for Windows není nutný**, pokud nechcete `git` spouštět z PowerShellu. |
| [mkcert](https://github.com/FiloSottile/mkcert#windows) | **silně doporučeno** | Bez něj HTTPS na mobilu u QR často nefunguje spolehlivě. Nejpohodlněji přes Chocolatey (níže): `choco install mkcert`. Alternativa: stáhnout release z GitHubu. |
| [Chocolatey](https://chocolatey.org/install) (Community) | ne (volitelné) | Správce balíčků pro Windows — usnadní instalaci mkcert. Postup instalace v §0.1. |
| Node.js LTS | ne (volitelné) | Jen pokud chcete kompilovat LESS na Windows. Jinak LESS přes Docker (krok 8). |
| PHP / Composer na Windows | **ne** | V tomto návodu běží PHP uvnitř Dockeru. |
| [Git for Windows](https://git-scm.com/download/win) | **ne** | Potřeba jen když chcete klonovat / `git pull` z příkazové řádky. S NetBeans stačí Team → Git. |

### 0.1 Chocolatey Community (pokud ještě nemáte)

Oficiální instalace **Chocolatey Community** (open-source, zdarma): https://chocolatey.org/install  

1. Klávesa **Windows**, napište `PowerShell`.
2. U položky **Windows PowerShell** zvolte **Spustit jako správce** (Run as administrator).
3. Ověřte, že Chocolatey ještě nemáte:

```powershell
choco -v
```

Pokud vypíše verzi, máte ho — přejděte k instalaci mkcert níže. Pokud hlásí, že příkaz neexistuje, pokračujte.

4. V administrátorském PowerShellu spusťte oficiální instalační příkaz (Community):

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force
[System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072
iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))
```

5. Zavřete administrátorský PowerShell. Otevřete **nové** (klidně neadmin) PowerShell okno a ověřte:

```powershell
choco -v
```

6. Instalace mkcert:

```powershell
choco install mkcert -y
```

Pak v **novém** PowerShellu ověřte `mkcert -help`. (Bez nového okna někdy Windows ještě nevidí PATH.)

Ověření Dockeru (v PowerShellu):

```powershell
docker version
docker compose version
```

Oba příkazy musí vypsat verze **bez chyby**. Pokud „docker is not recognized“, Docker Desktop neběží nebo není v PATH — restartujte Desktop / PC.

**Konflikt s lokálním Apache / MySQL** — před `docker compose up` zastavte:

- **Apache** (obvykle ve **WAMP**, případně XAMPP / vlastní na `C:\Apacheroot`) na portech 80/443 — ve WAMP Stop, nebo v `.env` změňte porty (krok 9).
- **MySQL** na portu 3306 — v týmu je MySQL **lokálně instalovaná mimo WAMP/XAMPP**. Zastavte ji přes ikonu **MySQL Notifier** v systémové liště Windows (tray): pravý klik → Stop Instance / Stop MySQL. Dokud Notifier ukazuje běžící instanci, Docker DB na 3306 nenasadí (nebo bude konflikt).

---

## 1. NetBeans: máte už projekt s gitem (běžný případ)

Většina týmu už v NetBeans otevírá projekt **`C:\Apacheroot\web`** a git je ve složce projektu (`.git`).

### 1.1 Aktualizujte větev v NetBeans

1. Otevřete projekt `web` v NetBeans.
2. **Team → Git → Branch / Switch to Branch** (nebo Git panel) → přepněte na **`master-vp`** (nebo větev, na které máte pracovat).
3. **Team → Git → Pull** (stáhněte aktuální změny včetně Docker souborů a tohoto návodu).

Když NetBeans hlásí konflikt nebo neznámou větev, nechte si pomoct kolegou — dál v návodu pokračujte až když máte aktuální `master-vp` (nebo domluvenou větev) a v kořeni projektu existuje soubor `docker-compose.yml`.

### 1.2 Otevřete PowerShell v kořeni projektu

1. Klávesa **Windows** → napište `PowerShell` → **Windows PowerShell**.
2. Přepněte se do složky projektu:

```powershell
cd C:\Apacheroot\web
```

Ověření (musí vypsat soubor):

```powershell
Get-ChildItem docker-compose.yml
```

Pokud PowerShell napíše, že cesta neexistuje, zkontrolujte v NetBeans **pravý klik na projekt → Properties** (nebo průzkumník), kde projekt skutečně leží — u týmu má být `C:\Apacheroot\web`.

Dál pokračujte krokem **2**.

---

## 1b. Ještě nemáte klon (výjimečně)

Pokud skládáte PC z nuly a NetBeans projekt ještě není:

1. Vytvořte složku `C:\Apacheroot` (pokud není).
2. Klon přes NetBeans (**Team → Git → Clone**) do `C:\Apacheroot\web`, větev `master-vp`,  
   **nebo** (jen s Git for Windows) v PowerShellu:

```powershell
cd C:\Apacheroot
git clone https://github.com/grafiaware/web.git web
cd web
git checkout master-vp
```

Pak otevřete složku jako projekt v NetBeans a pokračujte krokem **2**.

---

## 2. Zjistěte LAN IP notebooku (pro mobil a QR)

Notebook i telefon musí být ve **stejné Wi‑Fi**.

```powershell
Get-NetIPConfiguration |
  Where-Object { $_.IPv4DefaultGateway -ne $null -and $_.NetAdapter.Status -eq "Up" } |
  Select-Object -ExpandProperty IPv4Address |
  Select-Object -ExpandProperty IPAddress
```

Zapamatujte si výsledek, např. `192.168.1.42`.  
Toto **není** `0.0.0.0` ani `127.0.0.1` — ty do mobilu nezadávejte.

| Kde prohlížíte | Adresa do prohlížeče |
|----------------|----------------------|
| Stejný notebook (Docker) | `https://localhost/` nebo `https://ps.dev.local/` (vaše `WEB_PUBLIC_HOST`) |
| Telefon ve stejné Wi‑Fi | `https://ps.dev.local/` (po DNS/DHCP nebo překladu jména) · nouzově `https://192.168.1.42/` |

---

## 3. Soubor `.env` (jednou)

V PowerShellu stále v `C:\Apacheroot\web`:

```powershell
Copy-Item .env.docker.example .env
```

Doplňte LAN IP do `.env` (volitelné, ale vhodné pro certifikát):

```powershell
notepad .env
```

Najděte řádek `WEB_LAN_IP=` a doplňte, např.:

```env
WEB_LAN_IP=192.168.1.42
```

Uložte a zavřete Notepad. Soubor `.env` **necommitujte** do gitu (NetBeans: nepřidávejte ho do commitů).

---

## 4. Jméno vývojového stroje (QR / PDF) a TLS

Aplikace do QR kódů a PDF dává absolutní URL. Aby v nich nebyl `localhost` ani holá LAN IP, nastavte v `.env` **jméno tohoto PC** a v síti ho namapujte na jeho LAN IP (DNS/DHCP, nebo aplikace na telefonu).

Výchozí jména týmu: `ps.dev.local`, `vs.dev.local`, `ks.dev.local` (každý vývojář má svoje).

V `.env` (Notepad):

```env
# JEDNO jméno tohoto stroje — použije se v QR, PDF a odkazech v e-mailech
WEB_PUBLIC_HOST=ps.dev.local
WEB_PUBLIC_SCHEME=https

# Všechna jména týmu (certifikát + Apache)
WEB_DEV_HOSTS=ps.dev.local,vs.dev.local,ks.dev.local

WEB_SERVER_NAME=ps.dev.local
WEB_LAN_IP=192.168.1.42
WEB_CERT_HOSTS=localhost,ps.dev.local,vs.dev.local,ks.dev.local
```

`WEB_PUBLIC_HOST` změňte na své jméno (`vs.dev.local` / `ks.dev.local`).  
V DNS/DHCP (kde to jde) přidejte A záznam: `ps.dev.local` → LAN IP tohoto notebooku. Na telefonu bez DNS použijte aplikaci pro lokální překlad jména → IP.

Po změně `.env` vždy znovu spusťte stack (jinak kontejner staré hodnoty nevidí):

```powershell
docker compose up -d --force-recreate web
```

Ověření, že PHP vidí host (v prohlížeči by QR/PDF měly dávat `https://ps.dev.local/…`, ne `http://localhost:8080`):

```powershell
docker compose exec web printenv WEB_PUBLIC_HOST
```

(Musí vypsat `ps.dev.local`. Aplikace navíc čte `.env` z disku jako zálohu.)

### 4.1 TLS certifikáty (HTTPS) pro všechna vývojová jména

V PowerShellu **v `C:\Apacheroot\web`**:

```powershell
# Pokud Windows hlásí, že skripty jsou zakázané (jednou za uživatele):
Set-ExecutionPolicy -Scope CurrentUser RemoteSigned

# Skript načte .env (WEB_DEV_HOSTS / WEB_CERT_HOSTS / WEB_LAN_IP) sám
.\docker\certs\generate-certs.ps1
```

Skript **nemění** aktuální složku PowerShellu — zůstáváte v `C:\Apacheroot\web`.  
Do certifikátu zahrne `localhost` + všechna jména z `WEB_DEV_HOSTS` (jeden cert pro celý tým).

Očekávaný výsledek: ve složce `docker\certs\` vzniknou soubory `cert.pem`, `key.pem`, `ca.pem`.

Kontrola (stále z `C:\Apacheroot\web`):

```powershell
cd C:\Apacheroot\web
Get-ChildItem docker\certs\*.pem
```

Musíte vidět aspoň `cert.pem`, `key.pem`, `ca.pem`.

- S **mkcert**: na notebooku se CA nainstaluje sama; na telefon přenesete `docker\certs\ca.pem` (krok 11).
- Bez mkcert: self-signed — desktop často jen varování „pokračovat“, mobil u QR/HTTPS často **selže**, dokud nenainstalujete mkcert a skript nespustíte znovu.

Soubory `*.pem` do gitu nepatří (jsou v gitignore).

---

## 5. Spusťte Docker stack

Docker Desktop musí běžet. V `C:\Apacheroot\web`:

```powershell
docker compose up -d --build
```

První běh stahuje image a staví PHP — může trvat několik minut.  
Hotovo, když příkaz skončí bez červené chyby.

Stav kontejnerů:

```powershell
docker compose ps
```

Očekávejte služby `web`, `db`, `mail` ve stavu running / healthy (u `db`).

NetBeans dál editujete soubory v `C:\Apacheroot\web` — Docker je jen **připojuje** (bind-mount). Uložená změna v NetBeans je hned na disku a web v Dockeru ji vidí (u PHP obvykle po obnovení stránky).

---

## 6. Composer (závislosti PHP) — uvnitř Dockeru

Nemusíte mít Composer na Windows:

```powershell
docker compose exec web composer install
```

Pokud Composer hlásí chybu sítě / Packagist, zkontrolujte internet a firewall Docker Desktop.

---

## 7. Aktivní site NajdiSi (lokálně, necommittovat)

V **NetBeans** otevřete:

- `app/Site/active-site.php` → musí být:

```php
return 'NajdiSi';
```

- `local/site/site-name.less` → odkomentujte / nastavte sitename pro NajdiSi podle zvyku týmu (typicky `@sitename: "najdisi";` — podívejte se na zakomentované řádky v souboru).

Tyto dva soubory **necommitujte**.

---

## 8. Kompilace LESS (styly)

**Varianta A — Node na Windows** (pokud máte Node.js):

```powershell
npm install
npm run less:build
```

**Varianta B — jen Docker** (bez Node na PC):

```powershell
docker run --rm -v "${PWD}:/work" -w /work node:20-bookworm bash -lc "npm install && npm run less:build"
```

(`${PWD}` v PowerShellu v `C:\Apacheroot\web` ukáže aktuální složku — jste-li jinde, nejdřív `cd C:\Apacheroot\web`.)

---

## 9. Ověření v prohlížeči na notebooku

1. http://localhost/ — má načíst web (nebo přihlášení / obsah podle dumpů DB).
2. https://localhost/ — po přijetí certifikátu (nebo s mkcert bez varování) totéž.
3. http://localhost:5000/ — smtp4dev (fake maily; nic se nedoručuje ven).

Pokud localhost:80 nefunguje: často běží **Apache ve WAMP** (nebo IIS). Zastavte ho ve WAMP, nebo v `.env` změňte např. `WEB_HTTP_PORT=8080` a `WEB_HTTPS_PORT=8443`, pak:

```powershell
docker compose up -d
```

a v prohlížeči použijte `http://localhost:8080/` / `https://localhost:8443/`.

---

## 10. Databáze (stručně)

Prázdné DB a účty vzniknou při prvním startu (`docker/migrations/init`).  
Pro plný web potřebujete dumpy a `/build` — postup v [SETUP.md](SETUP.md) a [migrace-db-docker.md](../migrace-db-docker.md).

Rychlá kontrola, že DB žije:

```powershell
docker compose exec db mariadb -uroot -proot -e "SHOW DATABASES;"
```

Musíte vidět mimo jiné `najdisi`, `single_login`, `events`.

---

## 11. Telefon: HTTPS a instalace `ca.pem`

### 11.1 Adresa v mobilním prohlížeči

Stejná Wi‑Fi jako notebook. Preferujte jméno z `.env`:

```text
https://ps.dev.local/
```

(nahraďte svým `WEB_PUBLIC_HOST`; DNS/DHCP nebo překlad na telefonu musí mířit na LAN IP notebooku).  
Nouzově bez DNS: `https://192.168.1.42/` (LAN IP z kroku 2).

QR kódy generované aplikací používají `WEB_PUBLIC_HOST` (ne `localhost`) — viz krok 4.

`http://` na mobilu pro vývoj QR často nestačí (aplikace vyžadují HTTPS).

### 11.2 Co je `ca.pem`

Soubor `C:\Apacheroot\web\docker\certs\ca.pem` je **kořenová certifikační autorita** pro váš lokální vývoj.  
Nestačí ho „někam zkopírovat“ — musíte ho na telefonu **nainstalovat jako důvěryhodnou CA**. Bez toho mobilní prohlížeč / WebView často HTTPS z QR odmítne.

Přenos do telefonu: e-mail sobě, OneDrive/Google Drive, USB, AirDrop (iPhone) atd.  
Na PC: Průzkumník → `C:\Apacheroot\web\docker\certs\ca.pem`.

### 11.3 Android (orientační postup; cesty se liší podle výrobce)

1. Přeneste `ca.pem` do telefonu (např. Download).
2. **Nastavení** → **Zabezpečení** (nebo Hesla a zabezpečení) → **Šifrování a údaje** / **Přihlašovací údaje**.
3. **Instalovat certifikát** → typ **CA certifikát** (ne „certifikát VPN/aplikace“, pokud máte na výběr).
4. Vyberte `ca.pem` a potvrďte název (např. „Grafia local“).
5. Otevřete Chrome a zkuste `https://<LAN-IP>/`.

Poznámka: novější Android může CA označit jako „uživatelskou“ — některé aplikace jí nedůvěřují; prohlížeč obvykle ano. Pro firemní WebView může být potřeba debuggable build / síťová konfigurace — to je mimo tento návod.

### 11.4 iPhone / iPad

1. Přeneste `ca.pem` (nebo `ca.cer`) do iPhonu a **otevřete** ho (Mail, Soubory, Safari).
2. Systém nabídne **instalaci profilu** — potvrďte.
3. **Nastavení → Obecné → VPN a správa zařízení** (nebo **Profily**) → nainstalovaný profil → Install.
4. Pak **Nastavení → Obecné → Informace → Důvěra certifikátů** → zapněte důvěru u této CA.
5. Safari: `https://<LAN-IP>/`.

Bez kroku „Důvěra certifikátů“ iOS certifikát nainstaluje, ale HTTPS stále nedůvěřuje.

### 11.5 Windows firewall (když mobil stránku nevidí)

Na notebooku může firewall blokovat příchozí 80/443 z LAN.  
Dočasně ověřte: vypnout firewall pro privátní síť, nebo v **Brána Windows Defender Firewall** povolit příchozí TCP 80 a 443 (nebo porty z `.env`) pro Docker / soukromý profil.  
Veřejný internet stack **nevystavujte**.

---

## 12. Běžné denní použití

**Start (Docker Desktop už běží):**

```powershell
cd C:\Apacheroot\web
docker compose up -d
```

**Stop (data DB zůstanou):**

```powershell
docker compose down
```

**Logy webu při chybě:**

```powershell
docker compose logs -f web
```

**Úplný reset DB** (smaže data ve volume — pozor):

```powershell
docker compose down -v
docker compose up -d
```

**Git dál v NetBeans:** commit / push / pull jako obvykle (Team → Git). Docker příkazy jsou jen v PowerShellu; do gitu nepatří `.env` ani `docker\certs\*.pem`.

---

## 13. Časté chyby

| Problém | Co zkontrolovat |
|---------|-----------------|
| `docker: command not found` / není rozpoznán | Spusťte Docker Desktop, počkejte na „running“, nový PowerShell. |
| Port is already allocated (80/443) | Běží Apache ve WAMP? Stop ve WAMP, nebo změňte porty v `.env`. |
| Port is already allocated (3306) | Lokální MySQL (mimo WAMP) — zastavte přes **MySQL Notifier** v liště. Nesmí běžet současně `docker-compose.migrations.yml`. |
| `cd C:\Apacheroot\web` — cesta neexistuje | Ověřte umístění projektu v NetBeans; týmová cesta je `C:\Apacheroot\web`. |
| `Get-ChildItem` hledá `…\docker\certs\docker\certs` | Jste uvnitř `docker\certs` (starší skript měnil složku). `cd C:\Apacheroot\web` a znovu `Get-ChildItem docker\certs\*.pem`. Aktualizovaný skript složku nemění. |
| Web na notebooku OK, mobil timeout | Stejná Wi‑Fi? Správná LAN IP? Firewall Windows? |
| Mobil HTTPS „nedůvěryhodný“ / QR neotevře | Nainstalovaná CA (`ca.pem`)? U iOS zapnutá **Důvěra certifikátů**? Cert vydán i pro vaši LAN IP (`WEB_LAN_IP` + znovu `generate-certs.ps1` + `docker compose up -d`)? |
| Bílá stránka / chybějící vendor | `docker compose exec web composer install` |
| Šablony / cesty rozbité | Stack musí nastavit `WEB_PROJECT_PATH` (compose to dělá); nepřepisujte DocumentRoot ručně. |
| Skript `.ps1` zakázán | `Set-ExecutionPolicy -Scope CurrentUser RemoteSigned` |
| `mkcert.exe : Created a new local CA` + `NativeCommandError` (červeně) | Není chyba mkcert — PowerShell hlášku na stderr bral jako fatální. Aktualizujte skript (`git pull` / Pull v NetBeans) a spusťte `.\docker\certs\generate-certs.ps1` znovu. CA už může být vytvořená; skript ji jen znovu použije. |
| `grafia-web-apache` restartuje: `exec …/grafia-web-entrypoint.sh: no such file or directory` | Na Windows měl `entrypoint.sh` často konce řádků CRLF. Pull oprav + `docker compose build --no-cache web` a `docker compose up -d`. |
| `Cannot define multiple Listeners on the same IP:port` (ports.conf / 443) | Duplicitní `Listen 443`. Pull oprav Dockerfile + `docker compose build --no-cache web` a `docker compose up -d`. |

---

## 14. Kam dál

- Přehled služeb a variant PHP/MySQL: [DOCKER.md](DOCKER.md)
- Dumpy, `/build`, testy: [SETUP.md](SETUP.md)
- Jen DB bez celého webu: [migrace-db-docker.md](../migrace-db-docker.md)
- Playwright (klikací testy): složka `tests\e2e\` — viz DOCKER.md
