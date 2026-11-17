# Test: správa účtů a rolí (A3, A6, A8, A9)

Jeden scénář pro administraci účtů: zakládání účtů (A6), osobní jméno (A8), úklid nepotvrzených registrací (A3)
a přidělování rolí (A9). Oprávnění se v těchto balících navzájem ovlivňují, proto jsou v jednom scénáři.

- **Balíky:** A3 (#45), A6 (#72), A8, A9 (#93); zadání v [`docs/vp/zadani/`](../zadani/)
- **Prostředí:** Docker podle [README.md](README.md#prostředí) (MariaDB, smtp4dev), `master-vp`, site NajdiSi,
  **záloha DB** (úklid A3 maže data)
- **Etapy** ([README.md](README.md#etapy)): část 1 v etapě 2 (K1), části 2–5 v etapě 3 (K2), část 6 v etapě 6 (K0)
- **Migrace (`/build`, etapa 0):** auth `20261102000001_login_token`, `20261106000001_login_profile`,
  `20261106000002_registration_confirmed`, `20261110000001_role_grantors`, `20261110000002_login_profile_origin_throttle`
- **Logy:** `_www_na_logs/Logs/Auth/` – `RoleChanges.log`, `AccountChanges.log`, `LoginSelf.log`, `LoginAdmin.log`,
  `UnconfirmedRegistrations.log`
- **Ověřeno vývojářem:** tabulky viditelnosti v částech 1, 3 a 6 ověřeny přes HTTP (MariaDB, DB z baseline) pro všechny
  role, obě varianty menu, se zapnutým i vypnutým editačním režimem (13. 11. 2026)

## Záznam testu

| Údaj | Hodnota |
|---|---|
| Tester | |
| Datum | |
| Commit (`git log -1 --format=%h`) | |
| Prohlížeč | |
| Adresa ADMIN CREDENTIALS | (viz Příprava P4) |
| Adresa ADMIN ROLE | (viz Příprava P4) |

Výsledek u každého bodu: **OK** / **CHYBA** / **NEPROVEDENO** / **NEJASNÉ**; u chyby do poznámky postup, hlášku,
snímek obrazovky. Hlášení výsledků viz [README.md](README.md).

## Jak scénář funguje

**Viditelnost menu a oprávnění ke stránce jsou dvě různé věci.** Menu ukazuje, kudy se na stránku dostanete;
o tom, co na stránce uvidíte, rozhoduje stránka sama podle role a přepínačů. Proto se stránky ADMIN CREDENTIALS
a ADMIN ROLE v testu otevírají **přímou adresou** (Příprava P4) a viditelnost menu se kontroluje zvlášť.

**Kombinace přepínačů** (`app/Site/NajdiSi/ConfigurationFeatures.php`, lokálně, necommitovat):

| Kombinace | Etapa | Zapnuté přepínače | Odpovídá |
|---|---|---|---|
| **K1** | 2 | `vp.registrace-emailem`, `vp.prihlaseni-odkazem`, `vp.uklid-registraci`, `vp.zakladani-uctu`, `vp.osobni-jmeno` | A-funkce bez A9 |
| **K2** | 3 | jako K1 + `vp.sprava-roli` | vše včetně A9 |
| **K0** | 6 | žádný | dnešní produkce |

`vp.program` a `vp.losovani` jsou v etapách 2 a 3 ještě vypnuté.

**Varianty menu:**

- **(a)** stav po rebuildu DB a migracích: ADMIN CREDENTIALS a ADMIN ROLE jsou v menu **events admin** (pod ADMINISTRACE),
  kořen „menu role grantor“ neexistuje.
- **(b)** kořen **„menu role grantor“** existuje a ADMIN CREDENTIALS a ADMIN ROLE jsou pod ním. Na testovacím prostředí
  ho vytvoří skript `mysql najdisi < scripts/test-menu-role-grantor.sql` (lze spustit opakovaně; na produkci se dělá
  ručně v redakčním systému). Zpět do (a) vede rebuild red DB (`php scripts/db-rebuild.php --target=red`) nebo ruční přesun.
  Ve variantě (b) při K1 položky z menu events admin zmizí a events_administrator se na stránky dostane jen adresou –
  proto se (b) používá jen s K2.

**Menu podle rolí** (platí ve všech kombinacích):

- **supervisor** nemá žádné z „admin“ menu. V **editačním režimu** vidí speciální menu se *všemi* položkami stromu
  včetně kořenů, takže se dostane i na ADMIN CREDENTIALS a ADMIN ROLE. Co na nich uvidí, určuje stránka (tabulky níže).
- **editor** nevidí menu events admin ani menu role grantor, ani v editačním režimu.
- **events_administrator** vidí menu events admin, **red_administrator** menu red admin.
- **auth/red/events_role_grantor a global_role_grantor** vidí menu role grantor – jen ve variantě (b).
  Ve variantě (a) se na stránky dostanou jen přímou adresou.

„Upozornění“ v tabulkách = „Stránka je určena pouze pro editaci v administraci.“

## Příprava

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| P1 | Záloha DB `single_login` a `events` (úklid A3 maže data); migrace v `/build` spuštěné (etapa 0) | V `/build` žádná čekající migrace | | |
| P2 | Účet **supervisora** z DB baseline: přihlásit se. Neznáte-li heslo, nastavte ho na kopii DB: hash z `php scripts/global-role-grantor-hash.php` a `UPDATE credentials SET password_hash = '<hash>' WHERE login_name_fk = '<supervisor>';` | Supervisor přihlášen | | |
| P3 | `php scripts/global-role-grantor-hash.php` (heslo aspoň 12 znaků); zkopírujte `app/Site/NajdiSi/ConfigurationRoot.local.php.example` jako `ConfigurationRoot.local.php`, `'enabled' => true`, vložte hash (potřeba až v části 2) | `git status` soubor neukazuje (je v `.gitignore`) | | |
| P4 | Adresy stránek: jako supervisor zapněte editační režim, ve stromu klikněte na ADMIN CREDENTIALS a ADMIN ROLE a adresy z prohlížeče zapište do Záznamu testu | V DB z baseline `web/v1/page/item/6780c42d2a6af` a `web/v1/page/item/695925b0b7391` | | |

## 1. K1 (etapa 2, A9 vypnuto), varianta (a)

Nastavte K1, menu ve variantě (a).

### 1A. Testovací účty (supervisor)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 1.1 | Jako supervisor ADMIN CREDENTIALS přímou adresou | Bloky „Nepotvrzené registrace“ (s tlačítkem „Smazat…“), „Nový účet“ (heslo nebo „Heslo mailem“) a tabulka se sloupcem Typ; v nabídce rolí všechny role kromě `global_role_grantor` | | |
| 1.2 | Blokem „Nový účet“ s volbou „Heslo mailem“ založit pro každou roli testovací účet `t_<role>` s vlastním e-mailem `t.<role>@example.test`: `auth_role_grantor`, `red_role_grantor`, `events_role_grantor`, `supervisor`, `events_administrator`, `red_administrator`, `editor`, `visitor`, `representative` a jeden „bez role“ (`t_bezrole`) | Pro každý „Účet … je založený.“ a v smtp4dev e-mail „Nastavení hesla“ | | |
| 1.3 | Odkazy z e-mailů nastavit hesla | Každý účet se přihlásí | | |

### 1B. Viditelnost podle rolí

| # | Role | Menu events admin | ADMIN CREDENTIALS (adresou) | ADMIN ROLE (adresou) | Výsledek | Poznámka |
|---|---|---|---|---|---|---|
| 1.4 | t_supervisor | ne; v editačním režimu strom se všemi položkami | jako 1.1 | upozornění | | |
| 1.5 | t_events_administrator | ano: CREDENTIALS, ROLE | Nepotvrzené registrace bez tlačítka, Nový účet, tabulka; role `representative`, `visitor` | tabulka rolí | | |
| 1.6 | grantor role (auth, red, events) | ne | upozornění (bez A9 nic nesmí) | upozornění | | |
| 1.7 | t_editor, t_red_administrator, t_visitor, t_representative, t_bezrole | ne (red_administrator má menu red admin) | upozornění | upozornění | | |
| 1.8 | Kdokoli | Žádný červený pruh, i když existuje `ConfigurationRoot.local.php` s `enabled => true` | | | | |

### 1C. Funkce bez A9

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 1.9 | Supervisor: Nový účet, e-mail `test.a6.editor@example.test`, role `editor`, heslo (8+ znaků) | „Účet test_a6_editor (role editor) je založený.“; přihlášení jménem i e-mailem projde | | |
| 1.10 | Supervisor: role `visitor`, bez hesla, „Heslo mailem“, osobní jméno `Jana Testová` | E-mail „Nastavení hesla“ (platnost 72 h); heslo kratší než 8 znaků odmítnuto; po nastavení přihlášen jako „Jana Testová“; odkaz podruhé „Odkaz už neplatí“ | | |
| 1.11 | Dva účty se stejným e-mailem | Oba vzniknou; přihlášení e-mailem vyzve k zadání jména | | |
| 1.12 | events_administrator: upraveným formulářem (nástroje prohlížeče, `<option>`) založit roli `editor` | „Účet s rolí editor nemůžete založit.“ | | |
| 1.13 | events_administrator: v tabulce nastavit účtu roli `supervisor` (upravený `<option>`) | Odmítnuto „Roli supervisor nemůžete přidělit.“ | | |
| 1.14 | events_administrator: změnit vlastní roli | „Vlastní roli měnit nemůžete.“ | | |
| 1.15 | events_administrator: úklid upraveným `POST /auth/v1/admin/unconfirmed/clean` | „Nemáte oprávnění spustit úklid registrací.“ | | |
| 1.16 | Nová registrace e-mailem, nepotvrdit | V e-mailu „Nedokončená registrace se po 14 dnech smaže.“ | | |
| 1.17 | Supervisor: „Smazat starší než 14 dní“ → v potvrzení Zrušit, pak znovu → OK | Po Zrušit nic; po OK „Smazáno nepotvrzených registrací starších než 14 dní: N.“, N odpovídá náhledu; registrace z 1.16 zůstala; v `UnconfirmedRegistrations.log` počet a jméno, žádné e-maily | | |

### 1D. Osobní jméno (A8)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 1.18 | Registrace s „Vaše jméno (nepovinné)“ = `Jan Novák`, dokončit z e-mailu | V nabídce účtu „Jan Novák“ | | |
| 1.19 | Registrace bez jména → „Zadat své jméno“ → `Jana Nová` → Uložit | „Jméno jsme uložili.“; tlačítko teď „Změnit jméno“ | | |
| 1.20 | „Změnit jméno“ → smazat pole → Uložit | Zobrazí se přihlašovací jméno | | |
| 1.21 | Jméno `<b>Test</b>` a `  Jan    Novák  ` | Zobrazí se doslova jako text; mezery se srovnají na `Jan Novák` | | |
| 1.22 | Editor v editačním režimu / zástupce firmy v akcích firmy | V panelu akcí osobní jméno místo přihlašovacího | | |

## 2. K2 (etapa 3), varianta (a): global_role_grantor

Zapněte `vp.sprava-roli` (= K2), menu zůstává ve variantě (a).

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 2.1 | Otevřít web nepřihlášen | Nahoře červený pruh „Web se nesmí používat: není vypnuté globální přidělování rolí (global_role_grantor).“ (vidí ho všichni včetně nepřihlášených) | | |
| 2.2 | Přihlásit `global_role_grantor` se špatným heslem | „Neplatné přihlášení!“; v `LoginAdmin.log` neúspěšný pokus bez hesla | | |
| 2.3 | Přihlásit `global_role_grantor` se správným heslem | „Jste přihlášeni jako správce přidělování rolí z konfigurace serveru.“ | | |
| 2.4 | Prohlédnout menu | Menu role grantor **není** (varianta a); menu events admin také ne | | |
| 2.5 | Otevřít ADMIN CREDENTIALS přímou adresou | Nepotvrzené registrace (s „Smazat…“), Nový účet (bez pole pro heslo – jen e-mail s odkazem), tabulka se sloupcem Typ; role všechny kromě `global_role_grantor` | | |
| 2.6 | Jako `t_events_administrator` otevřít menu events admin a v něm ADMIN CREDENTIALS | Položky v menu jsou, ale stránka ukáže upozornění (s A9 účty nespravuje) | | |

## 3. K2, varianta (b): viditelnost podle rolí

Spusťte skript pro variantu (b). Každou roli přihlaste zvlášť (bez editačního režimu; supervisor i s ním).

| # | Role | Menu role grantor | Menu events admin | ADMIN CREDENTIALS (adresou) | ADMIN ROLE (adresou) | Výsledek | Poznámka |
|---|---|---|---|---|---|---|---|
| 3.1 | global_role_grantor | ano: CREDENTIALS, ROLE | ne | vše: Nepotvrzené registrace + „Smazat…“, Nový účet, tabulka; role: všechny | tabulka rolí | | |
| 3.2 | t_auth_role_grantor | ano | ne | jako 3.1 | tabulka rolí | | |
| 3.3 | t_red_role_grantor | ano | ne | Nový účet a tabulka, **bez** Nepotvrzených registrací; role jen `editor` | upozornění | | |
| 3.4 | t_events_role_grantor | ano | ne | Nepotvrzené registrace **bez** tlačítka („Úklid spouští správce účtů.“), Nový účet, tabulka; role `representative`, `visitor` | upozornění | | |
| 3.5 | t_supervisor | ne | ne | upozornění | upozornění | | |
| 3.6 | t_supervisor v editačním režimu | (strom: všechny položky) | (strom) | upozornění | upozornění | | |
| 3.7 | t_events_administrator | ne | ano, ale bez CREDENTIALS a ROLE (jsou v menu role grantor) | upozornění | upozornění | | |
| 3.8 | t_red_administrator | ne | ne (má menu red admin) | upozornění | upozornění | | |
| 3.9 | t_editor (i v editačním režimu), t_visitor, t_representative, t_bezrole, nepřihlášený | ne | ne | upozornění | upozornění | | |

## 4. K2, varianta (b): funkce

### 4A. Přidělování rolí (nikdo nemění vlastní roli)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 4.1 | Jako `t_auth_role_grantor` nastavit účtu `t_bezrole` roli `auth_role_grantor` | Uloženo; v `RoleChanges.log` „t_auth_role_grantor → účet …: bez role → auth_role_grantor“ | | |
| 4.2 | Jako `t_auth_role_grantor` vlastní řádek | Role u vlastního účtu není editovatelná | | |
| 4.3 | Přihlásit účet z 4.1 a odebrat roli `t_auth_role_grantor` | Uloženo (grantor smí odebrat roli jinému grantorovi) | | |
| 4.4 | Jako `t_events_role_grantor`: řádky editora a supervisora | Role needitovatelná; u visitor, representative a účtů bez role editovatelná | | |
| 4.5 | Jako `t_events_role_grantor` poslat upraveným formulářem roli `editor` (úprava `<option>` v nástrojích prohlížeče) | „Roli editor nemůžete přidělit.“, nic se nezmění | | |
| 4.6 | Jako `t_red_role_grantor` nastavit účtu roli `editor` | Uloženo; jiné role v nabídce nejsou | | |
| 4.7 | Vrátit roli `t_auth_role_grantor` (z 4.3) a `t_bezrole` (z 4.1) | Účty mají zase původní role – potřeba v etapách 4 a 5 | | |

### 4B. Úpravy účtů a nový účet

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 4.8 | Nový účet bez přihlašovacího jména (obejít povinnost pole) | „Zadejte přihlašovací jméno.“ | | |
| 4.9 | Nový účet se jménem, které už existuje v jiné velikosti písmen | „Přihlašovací jméno … už existuje.“ | | |
| 4.10 | Tabulka, sloupce Typ a heslo | Typ „registrace“ / „administrace“ / „starší účet“; heslo jen „nastaveno“ / „není“, hash se nezobrazuje | | |
| 4.11 | Jako `t_auth_role_grantor`: účet typu „registrace“ | E-mail jen ke čtení; osobní jméno a role editovatelné | | |
| 4.12 | Účet typu „administrace“: změnit e-mail | Uloženo; na **původní** adresu e-mail „Změna e-mailu účtu.“ bez nové adresy; v `AccountChanges.log` žádné adresy | | |
| 4.13 | Starší účet bez e-mailu: doplnit e-mail a poslat „Odkaz na heslo“ | E-mail uložen, odkaz přišel a funguje | | |
| 4.14 | Jako `t_events_role_grantor` | E-mail nejde měnit u žádného účtu | | |

### 4C. Úklid nepotvrzených registrací (A3)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 4.15 | Nová registrace e-mailem, nepotvrdit | Ve výpisu (zástupce firmy, je-li zaškrtnutý) „Smaže se za 14 dní“ | | |
| 4.16 | Jako `t_auth_role_grantor` „Smazat starší než 14 dní“ → OK | Smaže jako 1.17; registrace z 4.15 zůstala | | |
| 4.17 | Jako `t_events_role_grantor` poslat `POST /auth/v1/admin/unconfirmed/clean` (formulář z 4.16 zkopírovaný v nástrojích prohlížeče) | „Nemáte oprávnění spustit úklid registrací.“, nic se nesmaže | | |
| 4.18 | Potvrdit registraci z 4.15 | `registration.confirmed` vyplněné; z výpisu zmizí | | |

### 4D. Přihlašování a logy

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 4.19 | 5× špatné heslo k existujícímu účtu | Po 5. pokusu „Neplatné přihlášení! Příliš mnoho neúspěšných pokusů – další pokus za 30 s.“ | | |
| 4.20 | Do 30 s správné heslo v jiném prohlížeči | Odmítnuto: „Příliš mnoho neúspěšných pokusů. Zkuste to znovu za … s.“ | | |
| 4.21 | Po 30 s správné heslo | Přihlášen; počítadlo vynulováno | | |
| 4.22 | Během blokování přihlášení odkazem z e-mailu | Přihlášen (odkaz blokování ruší) | | |
| 4.23 | `global_role_grantor` 6× špatné heslo | Zpomalení se na něj nevztahuje | | |
| 4.24 | Logy | Účty z registrace v `LoginSelf.log`, ostatní v `LoginAdmin.log`; neexistující účet „účet neexistuje“ bez zadaného jména; nikde hesla ani e-maily | | |

### 4E. Vypnutí global_role_grantor

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 4.25 | V `ConfigurationRoot.local.php` `'enabled' => false`, obnovit stránku | Pruh zmizí; přihlášení `global_role_grantor` nejde („Neplatné přihlášení!“). Takto nechte i pro etapy 4 a 5 | | |

## 5. Kontrola v databázi (etapa 3)

| # | Dotaz | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 5.1 | `SELECT COUNT(*), SUM(r.password_hash <> '') FROM registration r;` | Druhé číslo nezvýšené testem (nové účty nemají heslo v `registration`) | | |
| 5.2 | `SELECT login_name_fk, origin, last_login, failed_count FROM login_profile WHERE login_name_fk LIKE 't\_%';` | Účty z části 1 mají `origin = admin`, `last_login` vyplněné | | |
| 5.3 | `SELECT login_name FROM events.login WHERE login_name LIKE 't\_%';` | Založené účty jsou v `events.login` | | |
| 5.4 | `SELECT COUNT(*), SUM(confirmed IS NULL) FROM registration;` | Potvrzené registrace mají `confirmed`, nepotvrzené `NULL` | | |

## 6. K0 (vše vypnuto = produkce, etapa 6)

Na konci celého testu vypněte všechny přepínače (i `vp.program`, `vp.losovani`), menu ve variantě (a).

| # | Role / co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 6.1 | t_events_administrator: menu events admin, ADMIN CREDENTIALS | Menu s CREDENTIALS a ROLE; stránka jako dřív: jen tabulka CREDENTIALS (bez Nového účtu, e-mailů, Nepotvrzených registrací) | | |
| 6.2 | t_events_administrator: ADMIN ROLE | Tabulka rolí | | |
| 6.3 | t_supervisor (i v editačním režimu přes strom) | Obě stránky: upozornění | | |
| 6.4 | Ostatní role | Obě stránky: upozornění | | |
| 6.5 | `POST /auth/v1/account`, `/auth/v1/account/x/update`, `/auth/v1/admin/unconfirmed/clean` | 404 | | |
| 6.6 | Registrace | Bez pole „Vaše jméno“, bez věty o smazání po 14 dnech | | |

## Po testu

Na konci celého testu (etapa 6): přepínače na `false`, `ConfigurationRoot.local.php` smažte (nic z toho necommitujte),
obnovte zálohu DB z P1.
