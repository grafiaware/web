# Test A1 + A2: registrace e-mailem, přihlášení e-mailem nebo jménem, přihlašovací odkaz

- **Balíky:** A1 (#44) – registrace e-mailem, A2 – přihlašovací odkaz; zadání [`docs/vp/zadani/A1.md`](../zadani/A1.md)
- **Prostředí:** lokální instalace větve `master-vp`, site NajdiSi, MySQL 8, smtp4dev
- **Migrace:** auth `20261102000001_login_token` musí být spuštěná (`/build`)

## Záznam testu

Vyplňte před začátkem:

| Údaj                              | Hodnota |
| --------------------------------- | ------- |
| Tester                            |         |
| Datum                             |         |
| Commit (`git log -1 --format=%h`) |         |
| Prohlížeč                         |         |

Výsledek u každého bodu: **OK** / **CHYBA** / **NEPROVEDENO**. U chyby do poznámky napište, co se stalo,
přesné znění hlášky a případně snímek obrazovky.

## Příprava

1. Lokálně (bez commitu) zapněte přepínače v `app/Site/NajdiSi/ConfigurationFeatures.php`:

   ```php
   'vp.registrace-emailem' => true,
   'vp.prihlaseni-odkazem' => true,
   ```

2. Spusťte smtp4dev: `docker compose -f docker-compose.mail-test.yml up -d`.
   Zachycené e-maily jsou na http://localhost:5000, nic se skutečně neodešle.

3. Používejte smyšlené adresy, např. `test.a1.jan@example.test` – testovací účty se pak snadno najdou a smažou.

## A. Se zapnutými přepínači

| #   | Co udělat                                                                       | Očekávaný výsledek                                                                                                            | Výsledek                      | Poznámka                                                                                                                                                                                      |
| --- | ------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------- | ----------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| A1  | Registrace: e-mail + heslo (aspoň 8 znaků), bez zaškrtnutí „zástupce firmy“     | Hláška „Děkujeme. Na adresu … jsme poslali e-mail…“; v smtp4dev potvrzovací e-mail                                            | okok                          |                                                                                                                                                                                               |
| A2  | Otevřít odkaz z potvrzovacího e-mailu                                           | Stránka s tlačítkem „Dokončit registraci“; samotné otevření nic neprovede                                                     | okok                          |                                                                                                                                                                                               |
| A3  | Kliknout na tlačítko                                                            | Uživatel je přihlášený; přihlašovací jméno je vytvořené z e-mailu (např. `test_a1_jan`); přijde e-mail o dokončení registrace | okok                          |                                                                                                                                                                                               |
| A4  | Otevřít odkaz z potvrzovacího e-mailu znovu                                     | Hláška, že registrace už je dokončená                                                                                         | okok                          |                                                                                                                                                                                               |
| A5  | Odhlásit se, přihlásit e-mailem + heslem                                        | Přihlášení projde                                                                                                             | okok                          |                                                                                                                                                                                               |
| A6  | Odhlásit se, přihlásit jménem (např. `test_a1_jan`) + heslem                    | Přihlášení projde                                                                                                             | okok                          |                                                                                                                                                                                               |
| A7  | Nová registrace bez hesla (jiný e-mail), potvrdit tlačítkem                     | Uživatel je přihlášený; po odhlášení se heslem (ani prázdným) nepřihlásí                                                      | ok                            | jakym asi heslem,?...... kdyz ho nezada<br/>ano, s heslem  prázdným se neprihlasi                                                                                                             |
| A8  | V přihlašovacím okně „Poslat přihlašovací odkaz“ pro účet z A7                  | Přijde e-mail s odkazem; odkaz otevře stránku s tlačítkem; tlačítkem se uživatel přihlásí                                     | ok                            |                                                                                                                                                                                               |
| A9  | Stejný přihlašovací odkaz použít podruhé                                        | Hláška „Odkaz už neplatí“                                                                                                     | ok                            |                                                                                                                                                                                               |
| A10 | Hned po přihlášení odkazem: změna hesla (okno odhlášení), staré heslo nevyplnit | Heslo se nastaví; poté funguje přihlášení e-mailem + heslem                                                                   | ok                            |                                                                                                                                                                                               |
| A11 | Znovu registrovat e-mail z A1                                                   | Stejná hláška jako u nové registrace; druhý účet nevznikne; přijde e-mail s přihlašovacím odkazem                             | ok                            |                                                                                                                                                                                               |
| A12 | Zaregistrovat nový e-mail, nepotvrdit, a registrovat ho znovu                   | Přijde znovu potvrzovací e-mail                                                                                               | ok                            |                                                                                                                                                                                               |
| A13 | „Poslat přihlašovací odkaz“ pro neexistující e-mail                             | Stejná hláška jako u existujícího e-mailu; v smtp4dev nic                                                                     | ok                            |                                                                                                                                                                                               |
| A14 | Pro stejný e-mail si nechat poslat 4 odkazy během 15 minut                      | Čtvrtý odkaz neodejde (limit 3 za 15 minut)                                                                                   | ok                            | modra hlaska stale stejna, ze pokud existuje ucet ze posilame mail.<br/>prislo jen 3x mail                                                                                                    |
| A15 | Registrace se zaškrtnutým „zástupce firmy“                                      | Jako dřív: e-mail organizátorovi (kopie na adresu `@najdisi.test`); roli `representative` přiděluje administrátor ručně       | ok                            | kopie na  webmaster@najdisi.test,<br/>po registrace dokoncena                                                                                                                                 |
| A16 | Přihlášení e-mailem, ke kterému patří víc účtů (v DB je takových 8)             | Výzva „Přihlaste se přihlašovacím jménem“; jménem pak přihlášení projde                                                       | ok?**chyba !!!**              | prisel mail, s nabidkou prihlaseni do uctu, ktere nemaji heslo v registration (!!! ale v credentials maji!!!) !!!.<br/>Prihlaseni z mailu se podarilose .                                     |
| A17 | Starý účet bez e-mailu: přihlášení jménem + heslem                              | Přihlášení projde jako dřív                                                                                                   | ok + **cti poznamku** **---** | zkousen editor s heslem editor, mail nema - prihlaseni OK<br/>**---** Jedinec bez emailu, ktery si nepamatuje heslo - nema cestu jak dal, obraci se na administratora. '?!..nekam poznamenat' |

## B. Kontrola v databázi (auth)

| #   | Dotaz                                                                                                               | Očekávaný výsledek                                                                    | Výsledek                                                  | Poznámka                                                                                                                                                                         |
| --- | ------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------- | --------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| B1  | `SELECT login_name_fk, purpose, created, expires, used, LENGTH(token_hash) FROM login_token ORDER BY created DESC;` | Jen hash tokenu (délka 64), nikde čitelný token; použité odkazy mají vyplněné `used`  | **???** vypsány "nějaké"" záznamy. (~Mozna je to dobře?~) | Ano, ve vysledku selectu neni citelny token, protoze neni vybran.  Nevím , které byly použity, takže nezkontroluju, že je to správně. Rozhodně to neukazuje  "Jen hash tokenu.." |
| B2  | Dotaz níže (nové účty)                                                                                              | U registrace prázdné heslo; role `visitor`; účet z A7 má prázdný hash v `credentials` | ok                                                        | Zaregistrovan ucet bez hesla, prisel mail, dokoncena registrace, -  pak je rovnou prihlasen.  Ano v credantials.password_hash je prazdno.                                        |

```sql
SELECT r.login_name_fk, r.email, r.password_hash = '' AS bez_hesla_v_registraci,
       c.role_fk, c.password_hash = '' AS bez_hesla
FROM registration r LEFT JOIN credentials c ON c.login_name_fk = r.login_name_fk
WHERE r.email LIKE '%@example.test';
```

## C. S vypnutými přepínači (`false`)

| #   | Co udělat                                | Očekávaný výsledek                                 | Výsledek    | Poznámka                                                                                                                                                |
| --- | ---------------------------------------- | -------------------------------------------------- | ----------- | ------------------------------------------------------------------------------------------------------------------------------------------------------- |
| C1  | Otevřít formulář registrace a přihlášení | Vypadají jako dřív; funguje „zapomenuté heslo“     | **ERROR**?? | kdy dřív?- vypadají jako na hostingu.<br/><br/>V kombinaci 'Prihlaseni-tl. zapomenute heslo-vyplnene jmeno-tl. Prihlasit' ... zadny mail neprijde.<br/> |
| C2  | Otevřít adresu `/auth/v1/loginlink/abc`  | 404                                                | ok          |                                                                                                                                                         |
| C3  | Změna hesla s heslem kratším než 8 znaků | Odmítnuto (pravidlo platí i s vypnutými přepínači) | ok          | nelze zadat kratsi nez 8 znaku                                                                                                                          |

## D. Další sloučené balíky (krátká kontrola)

| #       | Co udělat                                                                                        | Očekávaný výsledek                                                                             | Výsledek  | Poznámka                                                                                                                                                                                                                                 |
| ------- | ------------------------------------------------------------------------------------------------ | ---------------------------------------------------------------------------------------------- | --------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| D1      | Z1: nový účet se přihlásí a otevře stránku veletrhu                                              | Účet je v tabulce `events.login`                                                               | ok        | kdyz mam vypnute prepinace- prijde registracni email stary a po odkliknuti uz neprijde dokonceni registrace.------<br/>Pak jsem zapla prepinace .------vysledek  vysel ok<br/>                                                           |
| D2      | Z3: projít e-maily v smtp4dev                                                                    | Správné logo a odesílatel; kopie organizátorovi jdou na `@najdisi.test`, ne na skutečné adresy | **NEVIM** | --V mailech je logo Grafia, nevim jestli je "spravné".<br/>--Odesilatel je  info@najdisi.cz.<br/>--Jeden mail jde na zadanou mail.adresu, a druhy na webmaster@najdisi.test.<br/>--...Nevim co je "skutecna adresa", na kterou to nejde. |
| <br/>D3 | Z4 (jen pokud běžela migrace events `20260104000003_alter_login_pro_synchro`): přejmenovat login | Nové jméno se propíše i do `visitor_profile` a `representative`                                |           |                                                                                                                                                                                                                                          |

## Po testu

- Přepínače vraťte na `false` (soubor necommitujte).
- Testovací účty (`%@example.test`) smaže vývojář.
- Vyplněný protokol a nalezené chyby: viz [README.md](README.md).
