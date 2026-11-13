# Test A8: osobní jméno uživatele

- **Balík:** A8 (#85), zadání [`docs/vp/zadani/A8.md`](../zadani/A8.md)
- **Prostředí:** lokální instalace větve `master-vp`, site NajdiSi, smtp4dev
- **Migrace:** auth `20261106000001_login_profile` musí být spuštěná (`/build`)

## Záznam testu

| Údaj | Hodnota |
|---|---|
| Tester | |
| Datum | |
| Commit (`git log -1 --format=%h`) | |
| Prohlížeč | |

Výsledek u každého bodu: **OK** / **CHYBA** / **NEPROVEDENO**; u chyby do poznámky postup, hlášku, snímek obrazovky.

## Příprava

Lokálně (bez commitu) zapněte v `app/Site/NajdiSi/ConfigurationFeatures.php`:

```php
'vp.registrace-emailem' => true,
'vp.prihlaseni-odkazem' => true,
'vp.osobni-jmeno' => true,
```

## Se zapnutými přepínači

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 1 | Registrace: e-mail, „Vaše jméno (nepovinné)“ = `Jan Novák`, dokončit registraci z e-mailu | V nabídce účtu (ikona odhlášení) je „Jan Novák“ | | |
| 2 | Registrace bez jména, dokončit | V nabídce účtu je přihlašovací jméno (např. `test_a8_x`); tlačítko „Zadat své jméno“ | | |
| 3 | U účtu z bodu 2: „Zadat své jméno“ → `Jana Nová` → Uložit | Hláška „Jméno jsme uložili.“; v nabídce „Jana Nová“; tlačítko teď „Změnit jméno“ | | |
| 4 | „Změnit jméno“ → změnit na `Jana Nováková` | Nové jméno se zobrazí hned | | |
| 5 | „Změnit jméno“ → smazat obsah pole → Uložit | Hláška o odebrání; v nabídce přihlašovací jméno | | |
| 6 | Odhlásit, přihlásit znovu (e-mailem + heslem nebo odkazem) | Zobrazí se uložené osobní jméno | | |
| 7 | Starý účet přihlašovaný jménem + heslem (např. editor): zadat jméno | Jméno se uloží a zobrazí; přihlašuje se dál jménem + heslem | | |
| 8 | Editor v režimu editace / zástupce firmy v akcích firmy | V panelu akcí je osobní jméno místo přihlašovacího | | |
| 9 | Jméno `<b>Test</b>` | Zobrazí se doslova jako text, ne tučně | | |
| 10 | Jméno s mezerami navíc `  Jan    Novák  ` | Uloží se `Jan Novák` | | |

## S vypnutým přepínačem `vp.osobni-jmeno`

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 11 | Otevřít registraci a nabídku účtu | Pole „Vaše jméno“ ani tlačítko „Zadat své jméno“ nejsou; zobrazuje se přihlašovací jméno | | |

## Po testu

Přepínače vraťte na `false` (soubor necommitujte). Výsledky viz [README.md](README.md).
