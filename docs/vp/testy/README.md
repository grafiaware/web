# Testovací scénáře pro testery

Ruční testy funkcí Veletrhu práce. Jeden soubor = jeden scénář k balíku (nebo několika souvisejícím balíkům),
název `<kódy balíků>-test-<co se testuje>.md`.

| Scénář | Balíky | Přepínače |
|---|---|---|
| [A1-A2-test-registrace-a-prihlaseni.md](A1-A2-test-registrace-a-prihlaseni.md) | A1, A2 (+ krátká kontrola Z1, Z3, Z4) | `vp.registrace-emailem`, `vp.prihlaseni-odkazem` |
| [A3-A6-A8-A9-test-sprava-uctu.md](A3-A6-A8-A9-test-sprava-uctu.md) | A3, A6, A8, A9 – správa účtů a rolí | `vp.uklid-registraci`, `vp.zakladani-uctu`, `vp.osobni-jmeno`, `vp.sprava-roli` |
| [G0-test-program.md](G0-test-program.md) | G0 – program z databáze, přihlášení na akci, administrace programu | `vp.program` |
| [D1-D1c-test-losovani.md](D1-D1c-test-losovani.md) | D1, D1c – losování, skener, papírové lístky | `vp.losovani` |
| [R1-test-rezervace.md](R1-test-rezervace.md) | R1 – rezervace míst na přednáškách pro školy | `vp.rezervace` |

## Celý test nad `master-vp` s postupným zapínáním přepínačů

Scénáře jsou seřazené tak, aby šel celý `master-vp` otestovat znovu od začátku: začne se se vším vypnutým
(stav produkce) a v každé etapě se přidají přepínače dalšího balíku. Přepínače, které už jsou zapnuté, zůstávají
zapnuté, takže se ověří i spolupráce balíků. Na konci se vše vypne a ověří se, že web je jako produkce.

### Prostředí

- **Docker** podle [DOCKER.md](../DOCKER.md) (Windows: [DOCKER-WINDOWS.md](../DOCKER-WINDOWS.md)): Apache + PHP 8.4,
  MariaDB (jako produkce), smtp4dev (http://localhost:5000 – e-maily se nikam neodešlou), HTTPS i z telefonu
  ve stejné Wi-Fi (`https://<LAN-IP>/`, na telefon nainstalovat `docker/certs/ca.pem`). HTTPS je nutné pro skener
  a stránku „Můj kód“ (D1).
- **DB:** dumpy baseline (viz [MIGRACE.md](../MIGRACE.md), obsahují osobní údaje – do repa nepatří). Před testem
  zálohujte – A3 a G0 data mažou.
- Site `NajdiSi` v `app/Site/active-site.php` (necommitovat).
- Přepínače v `app/Site/NajdiSi/ConfigurationFeatures.php` měňte **jen lokálně, necommitujte**. Po změně počkejte
  pár sekund a obnovte stránku.
- Statické stránky se zobrazují přes položky menu v redakčním systému. Pro nové šablony (G0, losování) je potřeba
  položky menu založit – viz příprava ve scénářích.

### Etapy

| Etapa | Přepínače (kumulativně) | Co testovat |
|---|---|---|
| **0** | žádný | `/build`: spustit všechny migrace (auth i events); web vypadá jako produkce: [A1-A2 část C](A1-A2-test-registrace-a-prihlaseni.md#c-s-vypnutými-přepínači-etapa-0), [G0 bod 0.1](G0-test-program.md#0-oprávnění-ve-správě-programu-etapa-0--platí-vždy) (oprávnění platí vždy) |
| **1** | `vp.registrace-emailem`, `vp.prihlaseni-odkazem` | [A1-A2](A1-A2-test-registrace-a-prihlaseni.md) části A, B, D |
| **2** | + `vp.uklid-registraci`, `vp.zakladani-uctu`, `vp.osobni-jmeno` (= K1) | [A3-A9](A3-A6-A8-A9-test-sprava-uctu.md) část 1 (zde vzniknou testovací účty `t_<role>`) |
| **3** | + `vp.sprava-roli` (= K2) | [A3-A9](A3-A6-A8-A9-test-sprava-uctu.md) části 2–5 |
| **4** | + `vp.program` | [G0](G0-test-program.md) body 0.2–0.4 a části 1–7 |
| **5** | + `vp.losovani` | [D1-D1c](D1-D1c-test-losovani.md) části 1–9 |
| **5b** | + `vp.rezervace` | [R1](R1-test-rezervace.md) části 1–6 |
| **6** | vše zpět na `false` | [A3-A9 část 6](A3-A6-A8-A9-test-sprava-uctu.md#6-k0-vše-vypnuto--produkce-etapa-6), [G0 část 8](G0-test-program.md#8-vypnutý-přepínač-etapa-6), [D1-D1c část 10](D1-D1c-test-losovani.md#10-vypnutý-přepínač-etapa-6), [R1 část 7](R1-test-rezervace.md#7-vypnutý-přepínač-etapa-6) |

Úplný seznam přepínačů v etapě 5b (vše zapnuto, jak má být na veletrhu):

```php
'vp.registrace-emailem' => true,
'vp.prihlaseni-odkazem' => true,
'vp.zakladani-uctu' => true,
'vp.uklid-registraci' => true,
'vp.osobni-jmeno' => true,
'vp.sprava-roli' => true,
'vp.program' => true,
'vp.losovani' => true,
'vp.rezervace' => true,
```

**Testovací účty** vzniknou v etapě 2 (`t_events_administrator`, `t_visitor`, `t_representative` …) a používají se
i v etapách 3–6. Testovací návštěvníky pro losování (D1) založte registrací se smyšlenými adresami `…@example.test`.

**Automatické testy** (vývojář před předáním): `vendor/bin/phpunit -c tests/phpunit.xml --testsuite unit`
a `--testsuite integration` proti testovací DB. Pravidla platnosti losů a losování (hranice minut, váhy losů)
pokrývají unit testy, ručně se ověřuje jen chování v aplikaci.

## Jak hlásit výsledky (návrh, čeká na schválení)

1. Tester si scénář zkopíruje a vyplní sloupce **Výsledek** a **Poznámka** (lze i v prohlížeči na GitHubu
   přes tlačítko „Edit“ → zkopírovat obsah). Vyplněný scénář se do repa necommituje – v repu zůstává prázdný předpis.
2. Vyplněný protokol vloží jako **komentář do issue balíku** (A1 #44, A3 #45, A6 #72, A9 #93, D1 #46, D1c #108,
   G0 #116, R1 #48). Tabulky v Markdownu GitHub zobrazí.
3. Každá **CHYBA** se navíc založí jako samostatné issue s názvem `<kód balíku> test: <krátký popis>`,
   s číslem bodu scénáře, postupem, očekávaným a skutečným výsledkem a snímkem obrazovky.
4. Vývojář chybu opraví v pull requestu, který issue uzavře; tester bod zopakuje a výsledek doplní do komentáře.
5. Když očekávaný výsledek ve scénáři není jasný, zapište to do poznámky jako **NEJASNÉ** – vývojář scénář upřesní.
