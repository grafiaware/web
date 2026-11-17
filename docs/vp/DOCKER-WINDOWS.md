# Návod: Docker Desktop na Windows (PowerShell) — krok za krokem

Pro kolegu, který má **Docker Desktop** a potřebuje spustit web (Apache + PHP + MariaDB + fake SMTP) včetně **HTTPS z mobilu** (QR).  
Technický přehled: [DOCKER.md](DOCKER.md).

Příkazy níže pište **přesně** do **Windows PowerShell** (ne do CMD), pokud není řečeno jinak.  
Cestu ke klonu upravte podle svého PC — v příkladech je `C:\Users\JMENO\git\web`.

---

## 0. Co musí být nainstalované předem

| Software | Povinné? | Poznámka |
|----------|----------|----------|
| [Docker Desktop pro Windows](https://www.docker.com/products/docker-desktop/) | **ano** | Po instalaci **spusťte** Docker Desktop a počkejte, až ikona ve Windows ukáže „Engine running“. |
| [Git for Windows](https://git-scm.com/download/win) | **ano** | Na klonování repozitáře. |
| [mkcert](https://github.com/FiloSottile/mkcert#windows) | **silně doporučeno** | Bez něj HTTPS na mobilu u QR často nefunguje spolehlivě. Instalace např. přes [Chocolatey](https://chocolatey.org/): `choco install mkcert`, nebo stáhnout release z GitHubu. |
| Node.js LTS | ne (volitelné) | Jen pokud chcete kompilovat LESS na Windows. Jinak LESS přes Docker (krok 8). |
| PHP / Composer / XAMPP na Windows | **ne** | V tomto návodu běží PHP uvnitř Dockeru. |

Ověření Dockeru (v PowerShellu):

```powershell
docker version
docker compose version
```

Oba příkazy musí vypsat verze **bez chyby**. Pokud „docker is not recognized“, Docker Desktop neběží nebo není v PATH — restartujte Desktop / PC.

---

## 1. Otevřete PowerShell a přepněte se do složky s projektem

1. Stiskněte klávesu **Windows**, napište `PowerShell`, otevřete **Windows PowerShell**.
2. Pokud ještě nemáte klon:

```powershell
cd $HOME\git
```

Pokud složka `git` neexistuje:

```powershell
New-Item -ItemType Directory -Path $HOME\git -Force
cd $HOME\git
```

```powershell
git clone https://github.com/grafiaware/web.git
cd web
git checkout master-vp
git pull origin master-vp
```

3. Pokud už klon máte jinde (např. Cursor / VS Code workspace), přepněte se tam:

```powershell
cd C:\Users\JMENO\git\web
```

(`JMENO` nahraďte svým uživatelským jménem Windows, nebo použijte cestu, kterou vidíte v průzkumníku.)

Ověření, že jste v kořeni repo (musí existovat `docker-compose.yml`):

```powershell
Get-ChildItem docker-compose.yml
```

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
| Stejný notebook (Docker) | `http://localhost/` nebo `https://localhost/` |
| Telefon ve stejné Wi‑Fi | `https://192.168.1.42/` (vaše LAN IP) |

---

## 3. Soubor `.env` (jednou)

V kořeni repo (stále ve složce `web`):

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

Uložte a zavřete Notepad. Soubor `.env` **necommitujte** do gitu.

---

## 4. TLS certifikáty (HTTPS)

V PowerShellu **v kořeni repo**:

```powershell
# Pokud Windows hlásí, že skripty jsou zakázané (jednou za uživatele):
Set-ExecutionPolicy -Scope CurrentUser RemoteSigned

# LAN IP pro certifikát (stejná jako v kroku 2; pokud už je v .env, skript ji často detekuje sám)
$env:WEB_LAN_IP = "192.168.1.42"

.\docker\certs\generate-certs.ps1
```

Očekávaný výsledek: ve složce `docker\certs\` vzniknou soubory `cert.pem`, `key.pem`, `ca.pem`.

Kontrola:

```powershell
Get-ChildItem docker\certs\*.pem
```

Musíte vidět aspoň `cert.pem`, `key.pem`, `ca.pem`.

- S **mkcert**: na notebooku se CA nainstaluje sama; na telefon přenesete `docker\certs\ca.pem` (krok 11).
- Bez mkcert: self-signed — desktop často jen varování „pokračovat“, mobil u QR/HTTPS často **selže**, dokud nenainstalujete mkcert a skript nespustíte znovu.

---

## 5. Spusťte Docker stack

Docker Desktop musí běžet. V kořeni repo:

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

---

## 6. Composer (závislosti PHP) — uvnitř Dockeru

Nemusíte mít Composer na Windows:

```powershell
docker compose exec web composer install
```

Pokud Composer hlásí chybu sítě / Packagist, zkontrolujte internet a firewall Docker Desktop.

---

## 7. Aktivní site NajdiSi (lokálně, necommittovat)

V editoru (Cursor / VS Code / Notepad) otevřete:

- `app\Site\active-site.php` → musí být:

```php
return 'NajdiSi';
```

- `local\site\site-name.less` → odkomentujte / nastavte sitename pro NajdiSi podle zvyku týmu (typicky `@sitename: "najdisi";` — podívejte se na zakomentované řádky v souboru).

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

---

## 9. Ověření v prohlížeči na notebooku

1. http://localhost/ — má načíst web (nebo přihlášení / obsah podle dumpů DB).
2. https://localhost/ — po přijetí certifikátu (nebo s mkcert bez varování) totéž.
3. http://localhost:5000/ — smtp4dev (fake maily; nic se nedoručuje ven).

Pokud localhost:80 nefunguje: jiná aplikace drží port 80 (IIS, jiný Apache). V `.env` změňte např. `WEB_HTTP_PORT=8080` a `WEB_HTTPS_PORT=8443`, pak:

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

Stejná Wi‑Fi jako notebook. Do adresního řádku:

```text
https://192.168.1.42/
```

(nahraďte svou LAN IP z kroku 2; pokud jste v `.env` změnili HTTPS port na 8443, pak `https://192.168.1.42:8443/`).

Do QR kódů dávejte **stejnou HTTPS URL**.

`http://` na mobilu pro vývoj QR často nestačí (aplikace vyžadují HTTPS).

### 11.2 Co je `ca.pem`

Soubor `docker\certs\ca.pem` je **kořenová certifikační autorita** pro váš lokální vývoj.  
Nestačí ho „někam zkopírovat“ — musíte ho na telefonu **nainstalovat jako důvěryhodnou CA**. Bez toho mobilní prohlížeč / WebView často HTTPS z QR odmítne.

Přenos do telefonu: e-mail sobě, OneDrive/Google Drive, USB, AirDrop (iPhone), QR na soubor atd.  
Na PC soubor najdete průzkumníkem ve složce projektu: `docker\certs\ca.pem`.

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
cd C:\Users\JMENO\git\web
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

---

## 13. Časté chyby

| Problém | Co zkontrolovat |
|---------|-----------------|
| `docker: command not found` / není rozpoznán | Spusťte Docker Desktop, počkejte na „running“, nový PowerShell. |
| Port is already allocated (80/443/3306) | Jiný web/MySQL na PC; změňte porty v `.env` nebo ukončete konfliktní službu. Nesmí běžet současně `docker-compose.migrations.yml` (také 3306). |
| Web na notebooku OK, mobil timeout | Stejná Wi‑Fi? Správná LAN IP? Firewall Windows? |
| Mobil HTTPS „nedůvěryhodný“ / QR neotevře | Nainstalovaná CA (`ca.pem`)? U iOS zapnutá **Důvěra certifikátů**? Cert vydán i pro vaši LAN IP (`WEB_LAN_IP` + znovu `generate-certs.ps1` + `docker compose up -d`)? |
| Bílá stránka / chybějící vendor | `docker compose exec web composer install` |
| Šablony / cesty rozbité | Stack musí nastavit `WEB_PROJECT_PATH` (compose to dělá); nepřepisujte DocumentRoot ručně. |
| Skript `.ps1` zakázán | `Set-ExecutionPolicy -Scope CurrentUser RemoteSigned` |

---

## 14. Kam dál

- Přehled služeb a variant PHP/MySQL: [DOCKER.md](DOCKER.md)
- Dumpy, `/build`, testy: [SETUP.md](SETUP.md)
- Jen DB bez celého webu: [migrace-db-docker.md](../migrace-db-docker.md)
- Playwright (klikací testy): složka `tests\e2e\` — viz DOCKER.md
