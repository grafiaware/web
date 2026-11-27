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
| [F1-F2-test-odkazy.md](F1-F2-test-odkazy.md) | F1, F2 – trvalé odkazy s měřením, zdroj registrace, promo kit | `vp.odkazy` |
| [F3-test-qr-plakaty.md](F3-test-qr-plakaty.md) | F3 – QR z QRdirectoru, QR webu, podklad plakátu PDF, ověření QR | `vp.qr-plakaty` |

Vyplněné protokoly z proběhlých testů: [protokoly/](protokoly/README.md).

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
| **2** | + `vp.uklid-registraci`, `vp.zakladani-uctu`, `vp.osobni-jmeno` (= K1) | [A3-A9](A3-A6-A8-A9-test-sprava-uctu.md) část 1 (zde vzniknou testovací účty, pro každou roli jeden) |
| **3** | + `vp.sprava-roli` (= K2) | [A3-A9](A3-A6-A8-A9-test-sprava-uctu.md) části 2–5 |
| **4** | + `vp.program` | [G0](G0-test-program.md) body 0.2–0.4 a části 1–7 |
| **5** | + `vp.losovani` | [D1-D1c](D1-D1c-test-losovani.md) části 1–9 |
| **5b** | + `vp.rezervace` | [R1](R1-test-rezervace.md) části 1–6 |
| **5c** | + `vp.odkazy` | [F1-F2](F1-F2-test-odkazy.md) části 1–6 |
| **5d** | + `vp.qr-plakaty` | [F3](F3-test-qr-plakaty.md) části 1–5 |
| **6** | vše zpět na `false` | [A3-A9 část 6](A3-A6-A8-A9-test-sprava-uctu.md#6-k0-vše-vypnuto--produkce-etapa-6), [G0 část 8](G0-test-program.md#8-vypnutý-přepínač-etapa-6), [D1-D1c část 10](D1-D1c-test-losovani.md#10-vypnutý-přepínač-etapa-6), [R1 část 7](R1-test-rezervace.md#7-vypnutý-přepínač-etapa-6), [F1-F2 část 7](F1-F2-test-odkazy.md#7-vypnutý-přepínač-etapa-6), [F3 část 6](F3-test-qr-plakaty.md#6-vypnutý-přepínač-etapa-6) |

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
'vp.odkazy' => true,
'vp.qr-plakaty' => true,
```

**Účty a role:** scénáře uvádějí **roli**, se kterou se má tester přihlásit (např. „přihlásit se účtem s rolí
`events_administrator`“). Role se jmenují bez prefixu: `events_administrator`, `visitor`, `representative`, `supervisor` …
Pro každou roli vznikne v etapě 2 ([A3-A9](A3-A6-A8-A9-test-sprava-uctu.md) bod 1.2) jeden testovací účet;
jeho přihlašovací jméno je `t_` + název role (účet `t_events_administrator` má roli `events_administrator`).
Prefix `t_` mají jen jména těchto účtů, ne role. Kdo testovací účty nemá, použije libovolný účet s danou rolí.
Testovací účty se používají i v etapách 3–6. Testovací návštěvníky pro losování (D1) založte registrací se smyšlenými adresami `…@example.test`.

**Automatické testy** (vývojář před předáním): `vendor/bin/phpunit -c tests/phpunit.xml --testsuite unit`
a `--testsuite integration` proti testovací DB. Pravidla platnosti losů a losování (hranice minut, váhy losů)
pokrývají unit testy, ručně se ověřuje jen chování v aplikaci.

**E2E / Playwright:** kostra v `tests/e2e/` (Docker), plán postupné automatizace ručních scénářů —
[E2E-PLAN.md](E2E-PLAN.md).

## Aktuální kolo testů: kolo 2 (od 26. 11. 2026)

Obsah kola je uzavřený – další balíky se do něj nepřidávají; F3 (QR v plakátech) se dokončí a testuje
v dalším kole.

Opakovaný test po opravách z 1. kola (nálezy v [protokoly/](protokoly/README.md)). Postup jako v části
[Jak hlásit výsledky](#jak-hlásit-výsledky): nová větev testu z **aktuálního** `master-vp`, např. `master-vp_test-kolo2`.

| Pořadí | Scénář | Co testovat | Na co se zaměřit (opraveno od 1. kola) |
|---|---|---|---|
| 1 | [D1-D1c](D1-D1c-test-losovani.md) | celý (části 1–10; body 2.1 a 10.1 vyřazeny) | P5 telefon přes HTTPS (návod v DOCKER-WINDOWS kap. 11) – bez něj nejdou části 3, 4 a 8; 1.5–1.6 hlášky po uložení; 2.2–2.3 volby ankety a vysvětlivky; 7.4 vysvětlivky na lístku; 7.6 tisk „Přizpůsobit“ a řezové značky; 7.7 délka otázky |
| 2 | [A3-A9](A3-A6-A8-A9-test-sprava-uctu.md) | body 1.9–1.11 a 4.8 | nálezy A6 z 13. 11. (osobní jméno nepovinné, e-mail povinný, „Heslo mailem“ jde zaškrtnout, druhé otevření odkazu na heslo) |
| 3 | [R1](R1-test-rezervace.md) | celý – 1. kolo | nový balík; texty souhlasů jsou zatím „[Nutno nahradit]“ (#140) – to není chyba |
| 4 | [F1-F2](F1-F2-test-odkazy.md) | celý – 1. kolo | nový balík |
| – | [G0](G0-test-program.md) | 2. průchod hotový 26. 11. ([protokol](protokoly/2026-11-26-G0-test-program.md)) | nálezy #187–#191, scénář #192; části 3–7 většinou NEPROVEDENO kvůli #191 – opakovat po opravách |

Etapy přepínačů podle tabulky [Etapy](#etapy). Každý scénář = vlastní protokol (soubor scénáře vyplněný ve větvi testu);
commitujte průběžně, po každé části.

## Jak hlásit výsledky

Testeři pracují jen v gitu, GitHub (issues, pull requesty) nepoužívají.

1. **Větev testu:** tester si z aktuálního `master-vp` založí vlastní větev (např. `master-vp_D1-test`) a lokálně
   zapíná přepínače podle etap. Do `master-vp` se z ní nic neslučuje.
2. **Protokol:** tester vyplní ve scénáři sloupce **Výsledek** (OK / CHYBA / NEPROVEDENO / NEJASNÉ) a **Poznámka**,
   tabulku Záznam testu (tester, datum, commit) a commituje do své větve – průběžně, aby nic nezůstalo jen lokálně.
   Když očekávaný výsledek není jasný, zapíše **NEJASNÉ** a vlastními slovy, co viděl.
3. **Převzetí:** vývojář (agent) protokol převezme z větve testera do archivu [`protokoly/`](protokoly/README.md)
   jako `YYYY-MM-DD-<balíky>-test-<co>.md` s hlavičkou (tester, datum, testovaný commit, zdrojová větev),
   bez lokálních přepínačů a dalších změn prostředí (`composer.lock`, konfigurace). Scénáře v této složce zůstávají
   prázdné předpisy.
4. **Issues:** z každé CHYBY a NEJASNÉ položky založí vývojář issue s názvem `<kód balíku> test: <krátký popis>`
   a odkazem na řádek archivovaného protokolu; v issue balíku a v zadání (Stav a další krok) odkáže na nálezy.
5. **Oprava a opakování:** chyba se opraví v pull requestu, který issue uzavře. Tester bod zopakuje v nové větvi
   testu; nový protokol jde do archivu jako další soubor (archivované protokoly se nemění).
