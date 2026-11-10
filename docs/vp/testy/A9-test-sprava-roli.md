# Test A9: správa účtů a přidělování rolí

- **Balík:** A9 (#93), zadání [`docs/vp/zadani/A9.md`](../zadani/A9.md); navazuje na A6 (zakládání účtů) a A8 (osobní jméno)
- **Prostředí:** lokální instalace větve `vp/a9-sprava-roli`, site NajdiSi, smtp4dev
- **Migrace (`/build`):** auth `20261106000001_login_profile` (A8), `20261110000001_role_grantors`, `20261110000002_login_profile_origin_throttle`
- **Logy:** `_www_na_logs/Logs/Auth/` – `RoleChanges.log`, `AccountChanges.log`, `LoginSelf.log`, `LoginAdmin.log`

## Záznam testu

| Údaj | Hodnota |
|---|---|
| Tester | |
| Datum | |
| Commit (`git log -1 --format=%h`) | |
| Prohlížeč | |

Výsledek u každého bodu: **OK** / **CHYBA** / **NEPROVEDENO**; u chyby do poznámky postup, hlášku, snímek obrazovky.

## Příprava

1. Lokálně (bez commitu) zapněte v `app/Site/NajdiSi/ConfigurationFeatures.php`:

   ```php
   'vp.registrace-emailem' => true,
   'vp.prihlaseni-odkazem' => true,
   'vp.zakladani-uctu' => true,
   'vp.osobni-jmeno' => true,
   'vp.sprava-roli' => true,
   ```

2. Hash hesla pro `global_role_grantor`: `php scripts/global-role-grantor-hash.php` (heslo aspoň 12 znaků).
3. Zkopírujte `app/Site/NajdiSi/ConfigurationRoot.local.php.example` jako `ConfigurationRoot.local.php`, nastavte
   `'enabled' => true` a vložte hash. Soubor se necommituje (je v `.gitignore`) – `git status` ho nesmí ukázat.
4. V redakčním systému (supervisor, editační režim) založte kořen menu **`menu role grantor`** a přesuňte pod něj
   položky ADMIN CREDENTIALS a ADMIN ROLE.

## A. global_role_grantor z konfigurace

| # | Postup | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| A1 | Otevřete libovolnou stránku webu (nepřihlášen) | Nahoře červený pruh „Web se nesmí používat: není vypnuté globální přidělování rolí (global_role_grantor).“ | | |
| A2 | Přihlaste se jménem `global_role_grantor` a heslem z přípravy | „Jste přihlášeni jako správce přidělování rolí…“; v menu je „menu role grantor“ s ADMIN CREDENTIALS a ADMIN ROLE; menu events admin nevidí | | |
| A3 | Přihlaste se `global_role_grantor` se špatným heslem | „Neplatné přihlášení!“; v `LoginAdmin.log` řádek NEÚSPĚŠNÉ bez hesla | | |
| A4 | Otevřete ADMIN CREDENTIALS | Blok „Nový účet“ a tabulka účtů; v nabídce rolí všechny role kromě `global_role_grantor` | | |
| A5 | Nastavte v `ConfigurationRoot.local.php` `'enabled' => false`, obnovte stránku | Červený pruh zmizí; přihlášení `global_role_grantor` už nejde | | |

## B. Nový účet (typ admin)

Přihlášen jako `global_role_grantor` (nebo `auth_role_grantor`).

| # | Postup | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| B1 | Odešlete Nový účet bez přihlašovacího jména | Prohlížeč pole vyžaduje; po obejití „Zadejte přihlašovací jméno.“ | | |
| B2 | Jméno `test_spravce1`, e-mail, osobní jméno, role `auth_role_grantor` | „Účet test_spravce1 (role auth_role_grantor) je založený…“; v smtp4dev e-mail „Nastavení hesla“ | | |
| B3 | Jméno `test_bezrole1`, role „bez role“ | Účet založen „bez role“, e-mail s odkazem přišel | | |
| B4 | Formulář nemá pole pro heslo | Heslo nastaví uživatel odkazem z e-mailu | | |
| B5 | Odkazem z B2 nastavte heslo a přihlaste se jako `test_spravce1` | Přihlášen; menu role grantor viditelné | | |
| B6 | V tabulce: sloupec Typ u `test_spravce1` | „administrace“; hash hesla se v tabulce nezobrazuje („nastaveno“ / „není“) | | |

## C. Přidělování rolí (varianta B, nikdo nemění vlastní roli)

| # | Postup | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| C1 | Jako `test_spravce1` (auth_role_grantor) nastavte `test_bezrole1` roli `auth_role_grantor` | Uloženo; v `RoleChanges.log` „test_spravce1 → účet test_bezrole1: bez role → auth_role_grantor“ | | |
| C2 | Jako `test_spravce1` zkuste změnit vlastní roli | U vlastního řádku není pole role editovatelné | | |
| C3 | Přihlaste se jako `test_bezrole1` (odkaz na heslo z tabulky) a odeberte roli `test_spravce1` | Uloženo; `test_spravce1` je bez role | | |
| C4 | Účtu přidělte `events_role_grantor`, přihlaste se jím | Nabídka rolí jen visitor, representative, bez role; u editora a supervisora role needitovatelná | | |
| C5 | Účtu přidělte `red_role_grantor`, přihlaste se jím | Nabídka jen editor, bez role | | |
| C6 | Přihlaste se jako `supervisor` a `events_administrator` | Menu role grantor nevidí; ADMIN CREDENTIALS (přímou adresou) jen upozornění | | |

## D. Úpravy údajů v tabulce

Přihlášen jako `auth_role_grantor`.

| # | Postup | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| D1 | Účet typu „registrace“ | E-mail jen ke čtení; osobní jméno a role editovatelné | | |
| D2 | Účet typu „administrace“: změňte e-mail | Uloženo; na **původní** adresu přišel e-mail „Změna e-mailu účtu“ (nová adresa v něm není); `AccountChanges.log` bez adres | | |
| D3 | Účet typu „starší účet“ bez e-mailu: doplňte e-mail, pošlete „Odkaz na heslo“ | E-mail uložen, odkaz přišel | | |
| D4 | Jako `events_role_grantor` | E-mail nejde měnit u žádného účtu | | |

## E. Zpomalené přihlašování a logy

| # | Postup | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| E1 | 5× špatné heslo k existujícímu účtu | Po 5. pokusu „Příliš mnoho neúspěšných pokusů – další pokus za 30 s“ | | |
| E2 | Do 30 s zkuste **správné** heslo v jiném prohlížeči | Odmítnuto „Zkuste to znovu za … s“ | | |
| E3 | Po 30 s správné heslo | Přihlášen; počítadlo vynulováno | | |
| E4 | Přihlášení odkazem z e-mailu během blokování | Přihlášen (odkaz blokování ruší) | | |
| E5 | Logy | Účty z registrace v `LoginSelf.log`, ostatní v `LoginAdmin.log`; neexistující účet jako „neznámý účet“ bez zadaného jména; nikde hesla ani e-maily | | |

## F. Vypnutý přepínač

| # | Postup | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| F1 | `vp.sprava-roli` => false | Chování jako po A6 (supervisor zakládá účty), žádný pruh, global_role_grantor neexistuje | | |
| F2 | Jako `events_administrator` v tabulce CREDENTIALS nastavte účtu roli `supervisor` | **Odmítnuto** „Roli supervisor nemůžete přidělit.“ (oprava platí i bez přepínače) | | |

Po testu vraťte přepínače na `false` a smažte nebo vypněte `ConfigurationRoot.local.php`.
