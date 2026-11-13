# Test A6: zakládání účtů administrátorem

- **Balík:** A6 (#72), zadání [`docs/vp/zadani/A6.md`](../zadani/A6.md); navazuje na A8 (osobní jméno)
- **Prostředí:** lokální instalace větve `master-vp`, site NajdiSi, smtp4dev
- **Migrace:** auth `20261102000001_login_token` a `20261106000001_login_profile` spuštěné (`/build`)
- **Účty pro test:** jeden účet s rolí `supervisor`, jeden s rolí `events_administrator`

## Záznam testu

| Údaj                              | Hodnota |
| --------------------------------- | ------- |
| Tester                            |         |
| Datum                             |         |
| Commit (`git log -1 --format=%h`) |         |
| Prohlížeč                         |         |

Výsledek u každého bodu: **OK** / **CHYBA** / **NEPROVEDENO**; u chyby do poznámky postup, hlášku, snímek obrazovky.

## Příprava

Lokálně (bez commitu) zapněte v `app/Site/NajdiSi/ConfigurationFeatures.php`:

```php
'vp.registrace-emailem' => true,
'vp.prihlaseni-odkazem' => true,
'vp.zakladani-uctu' => true,
'vp.osobni-jmeno' => true,
```

Stránka: administrace účtů (admin-credentials) – blok „Nový účet“ nad tabulkou CREDENTIALS.

## Se zapnutými přepínači

| #   | Co udělat                                                                                                   | Očekávaný výsledek                                                                                                    | Výsledek  | Poznámka                                                                                                                                                                                    |
| --- | ----------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------- | --------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 1   | Jako supervisor otevřít admin-credentials                                                                   | Blok „Nový účet“; v nabídce rolí všechny role; v tabulce sloupec e-mail a u účtů s e-mailem tlačítko „Odkaz na heslo“ | ok        | ale jsem auth_role-grantor                                                                                                                                                                  |
| 2   | Supervisor: e-mail `test.a6.editor@example.test`, jméno nevyplnit, role `editor`, heslo (8+ znaků), Založit | Hláška „Účet test_a6_editor (role editor) je založený.“; účet je v tabulce                                            | ok ?      | <mark><u>auth_role_grantor</u></mark></mark><br/>Jmeno nutno vyplnit, bez nej to nejde odeslat. Hlaska ok. Prijde mail s vyzvou na zalozeni hesla. Kliknuti na odkaz ,  nastaveno heslo. ok |
| 3   | Odhlásit se, přihlásit jménem z bodu 2 + heslem                                                             | Přihlášení projde                                                                                                     | ok        | <br/>Jakpak bych se asi hlasila se jmenem, ktere melo byt nevyplneno??<br/><br/>                                                                                                            |
| 4   | Přihlásit e-mailem z bodu 2 + heslem                                                                        | Projde (e-mail má jen tento účet)                                                                                     | ok        |                                                                                                                                                                                             |
| 5   | Supervisor: přihlašovací jméno `test_repre01`, role `representative`, heslo                                 | Vznikne login `test_repre01`                                                                                          | ok?       | <mark><u>auth_role_grantor</u></mark><br/>Bez emailu to nejde .<br/>Login vznikl. Heslo nenastavovala.                                                                                      |
| 6   | Znovu založit `TEST_REPRE01` (jiná velikost písmen)                                                         | Hláška „Přihlašovací jméno TEST_REPRE01 už existuje.“, nic nového v tabulce                                           | ok        |                                                                                                                                                                                             |
| 7   | Založit dva účty se stejným e-mailem (různá jména)                                                          | Oba vzniknou, oba se přihlásí jménem; přihlášení e-mailem vyzve k zadání jména                                        | ok?       | Vznikly. Oba se přihlásily jmenem. Pri prihlaseni emailem  se neprihlasi, a napise ze je nutno se hlasit jmenem. - takto jste to mysleli? - nebo melo vylezt nejake okynko? - nevylezlo,    |
| 8   | Účet z bodu 5 přiřadit jako zástupce k firmě (administrace events) a pak k druhé firmě                      | Účet je hned v nabídce zástupců, jde přiřadit k oběma firmám                                                          | ok        | <mark>events_administrato</mark>r<br/>                                                                                                                                                      |
| 9   | Supervisor: role `visitor`, heslo nevyplnit, zaškrtnout „Heslo mailem“, osobní jméno `Jana Testová`         | Hláška o odeslání odkazu; v smtp4dev e-mail „Nastavení hesla“ s tlačítkem, platnost 72 hodin                          | ok?       | <mark><u>auth_role_grantor</u></mark> <br/>v nelze zaskrtnout heslo mailem. V mailu je zminka o platnosti  odkazu  72 hod .                                                                 |
| 10  | Otevřít odkaz z e-mailu, zadat heslo kratší než 8 znaků                                                     | Stránka zůstane, chyba „Heslo musí mít nejméně 8 znaků.“                                                              | ok        | hlaska ok, dal jsem nedelala nic                                                                                                                                                            |
| 11  | Zadat platné heslo                                                                                          | Přihlášení; v nabídce účtu „Jana Testová“                                                                             | ?         | odkazem z mailu jsem zadala heslo, prihlasilo se                                                                                                                                            |
| 12  | Odkaz z bodu 9 otevřít znovu                                                                                | „Odkaz už neplatí“                                                                                                    | **error** | normalne napsal ze ho nastavil, ... mozna proto ze....?... v bode 10 jsem nedokoncila to nastaveni hesla, jen se objevila ta hlaska ze je kratke  ?                                         |
| 13  | V tabulce u účtu z bodu 9 tlačítko „Odkaz na heslo“                                                         | Přijde nový e-mail s odkazem, odkaz funguje                                                                           |           |                                                                                                                                                                                             |
| 14  | Jako events_administrator otevřít admin-credentials                                                         | Blok „Nový účet“, v nabídce rolí jen `representative` a `visitor`                                                     |           | eee                                                                                                                                                                                         |
| 15  | events_administrator: pokusit se založit roli `editor` (např. upraveným formulářem)                         | Hláška „Účet s rolí editor nemůžete založit.“, nic se nezapíše                                                        |           |                                                                                                                                                                                             |
| 16  | Jako editor nebo visitor otevřít admin-credentials                                                          | „Stránka je určena pouze pro editaci v administraci.“                                                                 |           |                                                                                                                                                                                             |
| 17  | Chybné vstupy: neplatný e-mail, bez role, bez hesla i bez „heslo mailem“, jméno s diakritikou               | Srozumitelná hláška, nic se nezapíše                                                                                  |           |                                                                                                                                                                                             |
| 18  | `POST /auth/v1/register1`                                                                                   | 404                                                                                                                   |           |                                                                                                                                                                                             |

## Kontrola v databázi

| #   | Dotaz                                                                                                                                                                                        | Očekávaný výsledek                                                                                                                  | Výsledek | Poznámka                                             |
| --- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------- | -------- | ---------------------------------------------------- |
| 19  | `SELECT c.login_name_fk, c.role_fk, c.password_hash = '' AS bez_hesla, r.email, r.password_hash FROM credentials c JOIN registration r USING (login_name_fk) WHERE r.email LIKE 'test.a6%';` | V `registration.password_hash` vždy prázdné; v `credentials` jen hash (začíná `$2y$`) nebo prázdné u „heslo mailem“ před nastavením |          | asi, nevim,<br/><br/>kdyz je hash tak zacina spravne |
| 20  | `SELECT login_name FROM events.login WHERE login_name LIKE 'test%';`                                                                                                                         | Založené účty jsou v events.login hned po založení                                                                                  | ok       |                                                      |

## S vypnutým přepínačem `vp.zakladani-uctu`

| #   | Co udělat                                     | Očekávaný výsledek                                               | Výsledek | Poznámka |
| --- | --------------------------------------------- | ---------------------------------------------------------------- | -------- | -------- |
| 21  | events_administrator otevře admin-credentials | Stránka jako dřív (bez bloku „Nový účet“ a bez e-mailů)          |          |          |
| 22  | supervisor otevře admin-credentials           | Jako dřív: „Stránka je určena pouze pro editaci v administraci.“ |          |          |
| 23  | `POST /auth/v1/account`                       | 404                                                              |          |          |

## Po testu

Přepínače vraťte na `false` (soubor necommitujte). Výsledky viz [README.md](README.md).
