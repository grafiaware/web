# Test A1 + A2: registrace e-mailem, přihlášení e-mailem nebo jménem, přihlašovací odkaz

- **Balíky:** A1 (#44) – registrace e-mailem, A2 – přihlašovací odkaz; zadání [`docs/vp/zadani/A1.md`](../zadani/A1.md)
- **Prostředí:** Docker podle [README.md](README.md#prostředí) (MariaDB, smtp4dev na http://localhost:5000), větev `master-vp`, site NajdiSi
- **Etapy:** část C v etapě 0 (vše vypnuto), části A, B, D v etapě 1 – viz [README.md](README.md#etapy)
- **Migrace:** auth `20261102000001_login_token` (spouští se v etapě 0 s ostatními přes `/build`)
- **Minulý test:** 6. 11. 2026 (vyplněný protokol v historii souboru, commit `0563dec22`); nejasné body jsou teď upřesněné
  (A16, B1, C1, D2, D3)

## Záznam testu

| Údaj                              | Hodnota |
| --------------------------------- | ------- |
| Tester                            |         |
| Datum                             |         |
| Commit (`git log -1 --format=%h`) |         |
| Prohlížeč                         |         |

Výsledek u každého bodu: **OK** / **CHYBA** / **NEPROVEDENO** / **NEJASNÉ**. U chyby do poznámky napište, co se stalo,
přesné znění hlášky a případně snímek obrazovky.

## Příprava

1. Etapa 1 – lokálně (bez commitu) zapněte v `app/Site/NajdiSi/ConfigurationFeatures.php`:

   ```php
   'vp.registrace-emailem' => true,
   'vp.prihlaseni-odkazem' => true,
   ```

   Ostatní přepínače zůstávají vypnuté.
2. smtp4dev běží v Docker stacku (http://localhost:5000); bez plného stacku `docker compose -f docker-compose.mail-test.yml up -d`.
3. Používejte smyšlené adresy, např. `test.a1.jan@example.test` – testovací účty se pak snadno najdou a smažou.

## A. Se zapnutými přepínači (etapa 1)

| #   | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
| --- | --------- | ------------------ | -------- | -------- |
| A1  | Registrace: e-mail + heslo (aspoň 8 znaků), bez zaškrtnutí „zástupce firmy“ | Hláška „Děkujeme. Na adresu … jsme poslali e-mail…“; v smtp4dev potvrzovací e-mail | | |
| A2  | Otevřít odkaz z potvrzovacího e-mailu | Stránka s tlačítkem „Dokončit registraci“; samotné otevření nic neprovede | | |
| A3  | Kliknout na tlačítko | Uživatel je přihlášený; přihlašovací jméno je vytvořené z e-mailu (např. `test_a1_jan`); přijde e-mail o dokončení registrace | | |
| A4  | Otevřít odkaz z potvrzovacího e-mailu znovu | Hláška, že registrace už je dokončená | | |
| A5  | Odhlásit se, přihlásit e-mailem + heslem | Přihlášení projde | | |
| A6  | Odhlásit se, přihlásit jménem (např. `test_a1_jan`) + heslem | Přihlášení projde | | |
| A7  | Nová registrace **bez hesla** (jiný e-mail), potvrdit tlačítkem | Uživatel je přihlášený. Účet heslo nemá, takže po odhlášení se heslem přihlásit nejde (ani s prázdným polem) – přihlásí se jen odkazem (A8) | | |
| A8  | V přihlašovacím okně „Poslat přihlašovací odkaz“ pro účet z A7 | Přijde e-mail s odkazem; odkaz otevře stránku s tlačítkem; tlačítkem se uživatel přihlásí | | |
| A9  | Stejný přihlašovací odkaz použít podruhé | Hláška „Odkaz už neplatí“ | | |
| A10 | Hned po přihlášení odkazem: změna hesla (okno odhlášení), staré heslo nevyplnit | Heslo se nastaví; poté funguje přihlášení e-mailem + heslem | | |
| A11 | Znovu registrovat e-mail z A1 | Stejná hláška jako u nové registrace; druhý účet nevznikne; přijde e-mail s přihlašovacím odkazem | | |
| A12 | Zaregistrovat nový e-mail, nepotvrdit, a registrovat ho znovu | Přijde znovu potvrzovací e-mail | | |
| A13 | „Poslat přihlašovací odkaz“ pro neexistující e-mail | Stejná hláška jako u existujícího e-mailu; v smtp4dev nic | | |
| A14 | Pro stejný e-mail si nechat poslat 4 odkazy během 15 minut | Hláška je pokaždé stejná (neprozradí, zda účet existuje); v smtp4dev jen 3 e-maily (limit 3 za 15 minut) | | |
| A15 | Registrace se zaškrtnutým „zástupce firmy“ | E-mail organizátorovi (při vývoji na adresu `…@najdisi.test`) a e-mail žadateli, ať počká; po potvrzení má účet roli `visitor`, roli `representative` přiděluje administrátor ručně | | |
| A16a | Přihlášení **e-mailem + heslem**, ke kterému patří víc účtů (v DB baseline je takových 8 – dotaz B3) | Hláška „K tomuto e-mailu patří více účtů. Přihlaste se přihlašovacím jménem.“; jménem pak přihlášení projde | | |
| A16b | Pro stejný e-mail „Poslat přihlašovací odkaz“ | Jeden e-mail s odkazem pro **každý účet s dokončenou registrací** (má řádek v `credentials`). U takových účtů je heslo v `registration` prázdné – to je správně, heslo je v `credentials`. Účty bez dokončené registrace v e-mailu nejsou | | |
| A17 | Starý účet bez e-mailu: přihlášení jménem + heslem | Přihlášení projde jako dřív. (Kdo nemá e-mail a zapomene heslo, nemá vlastní cestu – obrací se na administrátora; řeší A6, „Odkaz na heslo“ po doplnění e-mailu) | | |

## B. Kontrola v databázi (auth, etapa 1)

| #   | Dotaz | Očekávaný výsledek | Výsledek | Poznámka |
| --- | ----- | ------------------ | -------- | -------- |
| B1  | Dotaz B1 níže | Tabulka nemá sloupec s čitelným tokenem (jen `token_hash`); u všech řádků `delka_hashe = 64`; odkazy použité v A2–A3, A8–A9 mají `pouzity = 1`, nepoužité (A14, A16b) `0` | | |
| B2  | Dotaz B2 níže (nové účty) | U potvrzených registrací prázdné heslo v `registration`; role `visitor`; účet z A7 má prázdný hash i v `credentials` | | |
| B3  | Dotaz B3 níže | Seznam e-mailů s více účty (pro A16) | | |

```sql
-- B1
SELECT login_name_fk, purpose, created, expires, used IS NOT NULL AS pouzity, LENGTH(token_hash) AS delka_hashe
FROM login_token ORDER BY created DESC LIMIT 30;

-- B2
SELECT r.login_name_fk, r.email, r.password_hash = '' AS bez_hesla_v_registraci,
       c.role_fk, c.password_hash = '' AS bez_hesla
FROM registration r LEFT JOIN credentials c ON c.login_name_fk = r.login_name_fk
WHERE r.email LIKE '%@example.test';

-- B3
SELECT r.email, COUNT(*) AS uctu FROM registration r JOIN credentials c ON c.login_name_fk = r.login_name_fk
GROUP BY r.email HAVING COUNT(*) > 1;
```

## C. S vypnutými přepínači (etapa 0)

Platí i na konci v etapě 6.

| #   | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
| --- | --------- | ------------------ | -------- | -------- |
| C1  | Otevřít formulář registrace a přihlášení | Stejné jako na produkci (hostingu): bez pole „Vaše jméno“, bez „Poslat přihlašovací odkaz“ | | |
| C2  | „Zapomenuté heslo“: vyplnit přihlašovací jméno účtu, který má registraci s e-mailem | Hláška „Na Vaši email.adresu jsme odeslali nové přihlašovací údaje.“; v smtp4dev e-mail s novým heslem (původní chování produkce) | | |
| C3  | „Zapomenuté heslo“: jméno účtu bez registrace (např. `editor`) / neexistující jméno | „Váš účet nebyl zaregistrován, neznáme Vaši email adresu…“ / „Neplatné jméno!“; žádný e-mail | | |
| C4  | Otevřít adresu `/auth/v1/loginlink/abc` | 404 | | |
| C5  | Změna hesla s heslem kratším než 8 znaků | Odmítnuto (pravidlo platí i s vypnutými přepínači) | | |

## D. Další sloučené balíky (krátká kontrola, etapa 1)

| #   | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
| --- | --------- | ------------------ | -------- | -------- |
| D1  | Z1: nový účet se přihlásí a otevře stránku veletrhu | Účet je v tabulce `events.login` (`SELECT * FROM events.login WHERE login_name = '…';`) | | |
| D2  | Z3: projít e-maily v smtp4dev | Odesílatel `info@najdisi.cz` (nebo jiný z konfigurace, ne prázdný); logo Grafia (jediné logo v konfiguraci, je správně); e-maily pro organizátora (kopie, žádost zástupce firmy) jdou při vývoji jen na smyšlené adresy `…@najdisi.test`, nikdy na skutečné adresy organizátora | | |
| D3  | Z4: přejmenovat login přímo v DB events (dotaz D3 níže, v transakci) | Nové jméno se propíše do `visitor_profile`, `visitor_job_request`, `representative` a `enroll` (cizí klíče ON UPDATE CASCADE). Přejmenování v DB `single_login` se do DB events nepropisuje (jiná databáze) – to není chyba | | |

```sql
-- D3 (v DB events; ROLLBACK vrátí změnu)
START TRANSACTION;
UPDATE login SET login_name = 'test_z4_prejmenovany' WHERE login_name = '<testovací účet se záznamem v visitor_profile>';
SELECT 'visitor_profile', COUNT(*) FROM visitor_profile WHERE login_login_name = 'test_z4_prejmenovany'
UNION ALL SELECT 'visitor_job_request', COUNT(*) FROM visitor_job_request WHERE login_login_name = 'test_z4_prejmenovany'
UNION ALL SELECT 'representative', COUNT(*) FROM representative WHERE login_login_name = 'test_z4_prejmenovany';
ROLLBACK;
```

## Po testu

- Přepínače zůstávají zapnuté pro další etapy (viz [README.md](README.md#etapy)); na konci celého testu je vraťte
  na `false` (soubor necommitujte).
- Testovací účty (`%@example.test`) smaže vývojář.
- Vyplněný protokol a nalezené chyby: viz [README.md](README.md).
