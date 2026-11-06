# Test A3: úklid nepotvrzených registrací a čekající zástupci firem

- **Balík:** A3 (#45), zadání [`docs/vp/zadani/A3.md`](../zadani/A3.md)
- **Prostředí:** lokální instalace větve `master-vp`, site NajdiSi, smtp4dev, **kopie DB** (úklid maže data – před testem zálohu `single_login`)
- **Migrace:** auth `20261106000002_registration_confirmed` spuštěná (`/build`)
- **Účty pro test:** `supervisor`, `events_administrator`, `editor`

## Záznam testu

| Údaj | Hodnota |
|---|---|
| Tester | |
| Datum | |
| Commit (`git log -1 --format=%h`) | |
| Prohlížeč | |

Výsledek u každého bodu: **OK** / **CHYBA** / **NEPROVEDENO**; u chyby do poznámky postup, hlášku, snímek obrazovky.

## Příprava

Lokálně (bez commitu) zapněte v `app/Site/NajdiSi/ConfigurationFeatures.php` `'vp.uklid-registraci' => true`
(pro bod 6 i `'vp.registrace-emailem' => true`).

## Po migraci

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 1 | `SELECT COUNT(*), SUM(confirmed IS NULL) FROM registration;` | Potvrzené registrace (mají `credentials`) mají `confirmed` vyplněné, ostatní `NULL` | | |

## Se zapnutým přepínačem

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 2 | supervisor otevře administraci účtů (admin-credentials) | Blok „Nepotvrzené registrace“: tabulka čekajících zástupců firem (účet, firma, e-mail, datum, „Smaže se za“), počet ke smazání (návštěvníci / zástupci), červené tlačítko „Smazat starší než 14 dní“ | | |
| 3 | events_administrator otevře admin-credentials | Stejný výpis, místo tlačítka „Úklid spouští supervisor.“ | | |
| 4 | editor otevře admin-credentials | Blok se nezobrazí | | |
| 5 | supervisor: tlačítko „Smazat…“ → v potvrzovacím okně Zrušit | Nic se nesmaže | | |
| 6 | Nová registrace e-mailem, nepotvrdit | V e-mailu věta „Nedokončená registrace se po 14 dnech smaže.“; ve výpisu se objeví (zástupce firmy, pokud byl zaškrtnutý) se „14 dní“ | | |
| 7 | supervisor: tlačítko „Smazat…“ → OK | Hláška „Smazáno nepotvrzených registrací starších než 14 dní: N.“; N odpovídá náhledu; registrace z bodu 6 (mladší) zůstala | | |
| 8 | `Logs/Auth/UnconfirmedRegistrations.log` (ve složce logů) | Řádek s počtem a jménem supervisora, žádné e-maily | | |
| 9 | Potvrdit registraci z bodu 6 | `confirmed` vyplněné; z výpisu zmizí | | |
| 10 | Účty s credentials (potvrzené, ručně dokončení zástupci, účty z A6) | Úklid je nesmaže | | |

## S vypnutým přepínačem

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 11 | Administrace účtů | Bez bloku „Nepotvrzené registrace“ | | |
| 12 | `POST /auth/v1/admin/unconfirmed/clean` | 404 | | |
| 13 | Registrační e-mail | Bez věty o smazání | | |

## Po testu

Obnovte DB ze zálohy, přepínače vraťte na `false` (soubor necommitujte). Výsledky viz [README.md](README.md).
