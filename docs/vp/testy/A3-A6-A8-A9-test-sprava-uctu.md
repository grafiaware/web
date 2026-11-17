# Test: správa účtů a rolí (A3, A6, A8, A9)

Jeden scénář pro administraci účtů: zakládání účtů (A6), osobní jméno (A8), úklid nepotvrzených registrací (A3)
a přidělování rolí (A9). Nahrazuje dřívější samostatné scénáře A3, A6, A8 a A9, které se rozcházely, protože se
oprávnění v těchto balících navzájem ovlivňují.

- **Balíky:** A3 (#45), A6 (#72), A8, A9 (#93); zadání v [`docs/vp/zadani/`](../zadani/)
- **Prostředí:** lokální instalace `master-vp`, site NajdiSi, smtp4dev, **kopie DB** (úklid A3 maže data)
- **Migrace (`/build`):** auth `20261102000001_login_token`, `20261106000001_login_profile`,
  `20261106000002_registration_confirmed`, `20261110000001_role_grantors`, `20261110000002_login_profile_origin_throttle`
- **Logy:** `_www_na_logs/Logs/Auth/` – `RoleChanges.log`, `AccountChanges.log`, `LoginSelf.log`, `LoginAdmin.log`,
  `UnconfirmedRegistrations.log`
- **Ověřeno:** tabulky viditelnosti v částech 2, 4 a 5 byly ověřeny přes HTTP v cloudu (MariaDB, DB z baseline)
  pro všechny role, obě varianty menu, se zapnutým i vypnutým editačním režimem (13. 11. 2026).

## Záznam testu

| Údaj | Hodnota |
|---|---|
| Tester | |
| Datum | |
| Commit (`git log -1 --format=%h`) | |
| Prohlížeč | |
| Adresa ADMIN CREDENTIALS | (viz Příprava 4) |
| Adresa ADMIN ROLE | (viz Příprava 4) |

Výsledek u každého bodu: **OK** / **CHYBA** / **NEPROVEDENO**; u chyby do poznámky postup, hlášku, snímek obrazovky.
Hlášení výsledků viz [README.md](README.md).

## Jak scénář funguje

**Viditelnost menu a oprávnění ke stránce jsou dvě různé věci.** Menu ukazuje, kudy se na stránku dostanete;
o tom, co na stránce uvidíte, rozhoduje stránka sama podle role a přepínačů. Proto se stránky ADMIN CREDENTIALS
a ADMIN ROLE v testu otevírají **přímou adresou** (Příprava 4) a viditelnost menu se kontroluje zvlášť.

**Kombinace přepínačů** (`app/Site/NajdiSi/ConfigurationFeatures.php`, lokálně, necommitovat):

| Kombinace | Zapnuté přepínače | Odpovídá |
|---|---|---|
| **K0** | žádný | dnešní produkce |
| **K1** | `vp.registrace-emailem`, `vp.prihlaseni-odkazem`, `vp.uklid-registraci`, `vp.zakladani-uctu`, `vp.osobni-jmeno` | A-funkce bez A9 |
| **K2** | jako K1 + `vp.sprava-roli` | vše včetně A9 |

Po změně přepínačů počkejte pár sekund a obnovte stránku (PHP může soubor načíst se zpožděním).

**Varianty menu:**

- **(a)** stav po rebuildu DB a migracích: ADMIN CREDENTIALS a ADMIN ROLE jsou v menu **events admin** (pod ADMINISTRACE),
  kořen „menu role grantor“ neexistuje.
- **(b)** kořen **„menu role grantor“** existuje a ADMIN CREDENTIALS a ADMIN ROLE jsou pod ním. Na testovacím prostředí
  ho vytvoří skript `mysql najdisi < scripts/test-menu-role-grantor.sql` (lze spustit opakovaně; na produkci se dělá
  ručně v redakčním systému). Zpět do (a) vede rebuild red DB (`php scripts/db-rebuild.php --target=red`) nebo ruční přesun.

**Menu podle rolí** (platí ve všech kombinacích):

- **supervisor** nemá žádné z „admin“ menu. V **editačním režimu** vidí speciální menu se *všemi* položkami stromu
  včetně kořenů, takže se dostane i na ADMIN CREDENTIALS a ADMIN ROLE. Co na nich uvidí, určuje stránka (tabulky níže).
- **editor** nevidí menu events admin ani menu role grantor, ani v editačním režimu.
- **events_administrator** vidí menu events admin, **red_administrator** menu red admin.
- **auth/red/events_role_grantor a global_role_grantor** vidí menu role grantor – jen ve variantě (b).
  Ve variantě (a) se na stránky dostanou jen přímou adresou.

## Příprava

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| P1 | Záloha DB `single_login` (úklid A3 maže data); migrace v `/build` spuštěné | V `/build` žádná čekající migrace | | |
| P2 | `php scripts/global-role-grantor-hash.php` (heslo aspoň 12 znaků) | Vypíše řádek `'passwordHash' => '…'` | | |
| P3 | Zkopírujte `app/Site/NajdiSi/ConfigurationRoot.local.php.example` jako `ConfigurationRoot.local.php`, `'enabled' => true`, vložte hash | `git status` soubor neukazuje (je v `.gitignore`) | | |
| P4 | Adresy stránek: jako supervisor zapněte editační režim, ve stromu klikněte na ADMIN CREDENTIALS a ADMIN ROLE a adresy z prohlížeče zapište do Záznamu testu | V DB z baseline `web/v1/page/item/6780c42d2a6af` a `web/v1/page/item/695925b0b7391` | | |

## 1. K2, varianta (a): global_role_grantor a založení testovacích účtů

Nastavte K2, menu ve variantě (a).

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 1.1 | Otevřít web nepřihlášen | Nahoře červený pruh „Web se nesmí používat: není vypnuté globální přidělování rolí (global_role_grantor).“ (vidí ho všichni včetně nepřihlášených) | | |
| 1.2 | Přihlásit `global_role_grantor` se špatným heslem | „Neplatné přihlášení!“; v `LoginAdmin.log` neúspěšný pokus bez hesla | | |
| 1.3 | Přihlásit `global_role_grantor` se správným heslem | „Jste přihlášeni jako správce přidělování rolí z konfigurace serveru.“ | | |
| 1.4 | Prohlédnout menu | Menu role grantor **není** (varianta a); menu events admin také ne | | |
| 1.5 | Otevřít ADMIN CREDENTIALS přímou adresou | Bloky „Nepotvrzené registrace“ (s tlačítkem „Smazat…“), „Nový účet“ a tabulka se sloupcem Typ; v nabídce rolí všechny role kromě `global_role_grantor` | | |
| 1.6 | Blokem „Nový účet“ založit pro každou roli testovací účet `t_<role>` s vlastním e-mailem: `auth_role_grantor`, `red_role_grantor`, `events_role_grantor`, `supervisor`, `events_administrator`, `red_administrator`, `editor`, `visitor`, `representative` a jeden „bez role“ | Pro každý „Účet … je založený.“ a v smtp4dev e-mail „Nastavení hesla“; formulář nemá pole pro heslo | | |
| 1.7 | Odkazy z e-mailů nastavit hesla | Každý účet se přihlásí | | |
| 1.8 | Jako `t_events_administrator` otevřít menu events admin a v něm ADMIN CREDENTIALS | Položky v menu jsou, ale stránka ukáže „Stránka je určena pouze pro editaci v administraci.“ (s A9 účty nespravuje) | | |

## 2. K2, varianta (b): viditelnost podle rolí

Spusťte skript pro variantu (b). Každou roli přihlaste zvlášť (bez editačního režimu; supervisor i s ním).

| # | Role | Menu role grantor | Menu events admin | ADMIN CREDENTIALS (adresou) | ADMIN ROLE (adresou) | Výsledek | Poznámka |
|---|---|---|---|---|---|---|---|
| 2.1 | global_role_grantor | ano: CREDENTIALS, ROLE | ne | vše: Nepotvrzené registrace + „Smazat…“, Nový účet, tabulka; role: všechny | tabulka rolí | | |
| 2.2 | t_auth_role_grantor | ano | ne | jako 2.1 | tabulka rolí | | |
| 2.3 | t_red_role_grantor | ano | ne | Nový účet a tabulka, **bez** Nepotvrzených registrací; role jen `editor` | upozornění | | |
| 2.4 | t_events_role_grantor | ano | ne | Nepotvrzené registrace **bez** tlačítka („Úklid spouští správce účtů.“), Nový účet, tabulka; role `representative`, `visitor` | upozornění | | |
| 2.5 | t_supervisor | ne | ne | upozornění | upozornění | | |
| 2.6 | t_supervisor v editačním režimu | (strom: všechny položky) | (strom) | upozornění | upozornění | | |
| 2.7 | t_events_administrator | ne | ano, ale bez CREDENTIALS a ROLE (jsou v menu role grantor) | upozornění | upozornění | | |
| 2.8 | t_red_administrator | ne | ne (má menu red admin) | upozornění | upozornění | | |
| 2.9 | t_editor (i v editačním režimu), t_visitor, t_representative, bez role, nepřihlášený | ne | ne | upozornění | upozornění | | |

„Upozornění“ = „Stránka je určena pouze pro editaci v administraci.“

## 3. K2, varianta (b): funkce

### 3A. Přidělování rolí (nikdo nemění vlastní roli)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 3.1 | Jako `t_auth_role_grantor` nastavit účtu „bez role“ roli `auth_role_grantor` | Uloženo; v `RoleChanges.log` „t_auth_role_grantor → účet …: bez role → auth_role_grantor“ | | |
| 3.2 | Jako `t_auth_role_grantor` vlastní řádek | Role u vlastního účtu není editovatelná | | |
| 3.3 | Přihlásit účet z 3.1 a odebrat roli `t_auth_role_grantor` | Uloženo (varianta B: grantor smí odebrat roli jinému grantorovi) | | |
| 3.4 | Jako `t_events_role_grantor`: řádky editora a supervisora | Role needitovatelná; u visitor, representative a účtů bez role editovatelná | | |
| 3.5 | Jako `t_events_role_grantor` poslat upraveným formulářem roli `editor` (např. úpravou `<option>` v nástrojích prohlížeče) | „Roli editor nemůžete přidělit.“, nic se nezmění | | |
| 3.6 | Jako `t_red_role_grantor` nastavit účtu roli `editor` | Uloženo; jiné role v nabídce nejsou | | |

### 3B. Úpravy účtů a nový účet

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 3.7 | Nový účet bez přihlašovacího jména (obejít povinnost pole) | „Zadejte přihlašovací jméno.“ | | |
| 3.8 | Nový účet se jménem, které už existuje v jiné velikosti písmen | „Přihlašovací jméno … už existuje.“ | | |
| 3.9 | Tabulka, sloupce Typ a heslo | Typ „registrace“ / „administrace“ / „starší účet“; heslo jen „nastaveno“ / „není“, hash se nezobrazuje | | |
| 3.10 | Jako `t_auth_role_grantor`: účet typu „registrace“ | E-mail jen ke čtení; osobní jméno a role editovatelné | | |
| 3.11 | Účet typu „administrace“: změnit e-mail | Uloženo; na **původní** adresu e-mail „Změna e-mailu účtu.“ bez nové adresy; v `AccountChanges.log` žádné adresy | | |
| 3.12 | Starší účet bez e-mailu: doplnit e-mail a poslat „Odkaz na heslo“ | E-mail uložen, odkaz přišel a funguje | | |
| 3.13 | Jako `t_events_role_grantor` | E-mail nejde měnit u žádného účtu | | |

### 3C. Úklid nepotvrzených registrací (A3)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 3.14 | Nová registrace e-mailem, nepotvrdit | V e-mailu „Nedokončená registrace se po 14 dnech smaže.“; ve výpisu (zástupce firmy, je-li zaškrtnutý) se „Smaže se za 14 dní“ | | |
| 3.15 | Jako `t_auth_role_grantor` „Smazat starší než 14 dní“ → v potvrzení Zrušit | Nic se nesmaže | | |
| 3.16 | Znovu → OK | „Smazáno nepotvrzených registrací starších než 14 dní: N.“, N odpovídá náhledu; registrace z 3.14 zůstala; v `UnconfirmedRegistrations.log` počet a jméno, žádné e-maily | | |
| 3.17 | Jako `t_events_role_grantor` poslat `POST /auth/v1/admin/unconfirmed/clean` (např. formulářem z 3.15 zkopírovaným v nástrojích prohlížeče) | „Nemáte oprávnění spustit úklid registrací.“, nic se nesmaže | | |
| 3.18 | Potvrdit registraci z 3.14 | `registration.confirmed` vyplněné; z výpisu zmizí | | |

### 3D. Přihlašování a logy

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 3.19 | 5× špatné heslo k existujícímu účtu | Po 5. pokusu „Neplatné přihlášení! Příliš mnoho neúspěšných pokusů – další pokus za 30 s.“ | | |
| 3.20 | Do 30 s správné heslo v jiném prohlížeči | Odmítnuto: „Příliš mnoho neúspěšných pokusů. Zkuste to znovu za … s.“ | | |
| 3.21 | Po 30 s správné heslo | Přihlášen; počítadlo vynulováno | | |
| 3.22 | Během blokování přihlášení odkazem z e-mailu | Přihlášen (odkaz blokování ruší) | | |
| 3.23 | `global_role_grantor` 6× špatné heslo | Zpomalení se na něj nevztahuje | | |
| 3.24 | Logy | Účty z registrace v `LoginSelf.log`, ostatní v `LoginAdmin.log`; neexistující účet „účet neexistuje“ bez zadaného jména; nikde hesla ani e-maily | | |

### 3E. Osobní jméno (A8)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 3.25 | Registrace s „Vaše jméno (nepovinné)“ = `Jan Novák`, dokončit z e-mailu | V nabídce účtu „Jan Novák“ | | |
| 3.26 | Registrace bez jména → „Zadat své jméno“ → `Jana Nová` → Uložit | „Jméno jsme uložili.“; tlačítko teď „Změnit jméno“ | | |
| 3.27 | „Změnit jméno“ → smazat pole → Uložit | Zobrazí se přihlašovací jméno | | |
| 3.28 | Jméno `<b>Test</b>` a `  Jan    Novák  ` | Zobrazí se doslova jako text; mezery se srovnají na `Jan Novák` | | |
| 3.29 | Editor v editačním režimu / zástupce firmy v akcích firmy | V panelu akcí osobní jméno místo přihlašovacího | | |

### 3F. Vypnutí global_role_grantor

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 3.30 | V `ConfigurationRoot.local.php` `'enabled' => false`, obnovit stránku | Pruh zmizí; přihlášení `global_role_grantor` nejde („Neplatné přihlášení!“) | | |

## 4. K1 (A9 vypnuto), varianta (a)

Nastavte K1, menu ve variantě (a) (rebuild red DB nebo ruční přesun zpět). Testovací účty z části 1 zůstávají.

| # | Role | Menu events admin | ADMIN CREDENTIALS (adresou) | ADMIN ROLE (adresou) | Výsledek | Poznámka |
|---|---|---|---|---|---|---|
| 4.1 | t_supervisor | ne; v editačním režimu strom se všemi položkami | Nepotvrzené registrace + „Smazat…“, Nový účet (s heslem nebo „Heslo mailem“), tabulka; role: všechny | upozornění | | |
| 4.2 | t_events_administrator | ano: CREDENTIALS, ROLE | Nepotvrzené registrace bez tlačítka, Nový účet, tabulka; role `representative`, `visitor` | tabulka rolí | | |
| 4.3 | grantor role (auth, red, events) | ne | upozornění (bez A9 nic nesmí) | upozornění | | |
| 4.4 | t_editor, t_red_administrator, t_visitor, t_representative, bez role | ne (red_administrator má menu red admin) | upozornění | upozornění | | |
| 4.5 | Kdokoli | Žádný červený pruh, i když existuje `ConfigurationRoot.local.php` s `enabled => true` | | | | |

Ve variantě (b) při K1 položky z menu events admin zmizí a events_administrator se na stránky dostane jen adresou.
Variantu (b) proto používejte jen s K2.

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 4.6 | Supervisor: Nový účet, e-mail `test.a6.editor@example.test`, role `editor`, heslo (8+ znaků) | „Účet test_a6_editor (role editor) je založený.“; přihlášení jménem i e-mailem projde | | |
| 4.7 | Supervisor: role `visitor`, bez hesla, „Heslo mailem“, osobní jméno `Jana Testová` | E-mail „Nastavení hesla“ (platnost 72 h); heslo kratší než 8 znaků odmítnuto; po nastavení přihlášen jako „Jana Testová“; odkaz podruhé „Odkaz už neplatí“ | | |
| 4.8 | Dva účty se stejným e-mailem | Oba vzniknou; přihlášení e-mailem vyzve k zadání jména | | |
| 4.9 | events_administrator: upraveným formulářem založit roli `editor` | „Účet s rolí editor nemůžete založit.“ | | |
| 4.10 | events_administrator: v tabulce nastavit účtu roli `supervisor` (upravený `<option>`) | **Odmítnuto** „Roli supervisor nemůžete přidělit.“ (oprava platí i bez A9) | | |
| 4.11 | events_administrator: změnit vlastní roli | „Vlastní roli měnit nemůžete.“ | | |
| 4.12 | events_administrator: úklid upraveným `POST /auth/v1/admin/unconfirmed/clean` | „Nemáte oprávnění spustit úklid registrací.“ | | |
| 4.13 | Supervisor: úklid → OK | Smaže jako 3.16 | | |

## 5. K0 (vše vypnuto = produkce), varianta (a)

| # | Role / co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 5.1 | t_events_administrator: menu events admin, ADMIN CREDENTIALS | Menu s CREDENTIALS a ROLE; stránka jako dřív: jen tabulka CREDENTIALS (bez Nového účtu, e-mailů, Nepotvrzených registrací) | | |
| 5.2 | t_events_administrator: ADMIN ROLE | Tabulka rolí | | |
| 5.3 | t_supervisor (i v editačním režimu přes strom) | Obě stránky: upozornění | | |
| 5.4 | Ostatní role | Obě stránky: upozornění | | |
| 5.5 | `POST /auth/v1/account`, `/auth/v1/account/x/update`, `/auth/v1/admin/unconfirmed/clean` | 404 | | |
| 5.6 | Registrace | Bez pole „Vaše jméno“, bez věty o smazání po 14 dnech | | |

## 6. Kontrola v databázi

| # | Dotaz | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 6.1 | `SELECT COUNT(*), SUM(r.password_hash <> '') FROM registration r;` | Druhé číslo nezvýšené testem (nové účty nemají heslo v `registration`) | | |
| 6.2 | `SELECT login_name_fk, origin, last_login, failed_count FROM login_profile WHERE login_name_fk LIKE 't\_%';` | Účty z části 1 mají `origin = admin`, `last_login` vyplněné | | |
| 6.3 | `SELECT login_name FROM events.login WHERE login_name LIKE 't\_%';` | Založené účty jsou v `events.login` | | |
| 6.4 | `SELECT COUNT(*), SUM(confirmed IS NULL) FROM registration;` | Potvrzené registrace mají `confirmed`, nepotvrzené `NULL` | | |

## Po testu

Přepínače vraťte na `false`, `ConfigurationRoot.local.php` smažte (nic z toho necommitujte), obnovte zálohu DB z P1.
