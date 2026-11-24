# Test D1 + D1c: losování, skener, papírové anketní lístky

- **Balíky:** D1 (#46) losování pro registrované, D1c (#108) papírové lístky; zadání [`D1.md`](../zadani/D1.md),
  [`D1c.md`](../zadani/D1c.md); revize procesů s organizátorem #74
- **Prostředí:** Docker podle [README.md](README.md#prostředí) – **HTTPS** na počítači i z telefonu (`https://<LAN-IP>/`,
  certifikát `docker/certs/ca.pem` nainstalovaný v telefonu); smtp4dev; `master-vp`, site NajdiSi
- **Etapy** ([README.md](README.md#etapy)): části 1–9 v etapě 5, část 10 v etapě 6
- **Migrace (`/build`, etapa 0):** events `20261116000001_d1_create_lottery`, `20261116000002_d1c_lottery_paper` (jen přidávají)
- **Zařízení:** počítač (správce), 1–2 telefony obsluhy (Android, iPhone) a telefon návštěvníka; tiskárna A4
- **Účty** (role, viz [README](README.md#etapy)): `events_administrator` = správce i obsluha (užší role obsluhy je #90); návštěvníci `test.d1.a@example.test`,
  `test.d1.b@example.test`, `test.d1.c@example.test` (registrace podle A1, role `visitor`); `representative`
- **Automaticky pokryto unit testy:** hranice platnosti losu (30 min, 2 kola, prodloužení jen dopředu), váhy losů,
  náhoda losování, rozpoznání kódu a čísla lístku – ručně se ověřuje chování v aplikaci

## Záznam testu

| Údaj | Hodnota |
|---|---|
| Tester | |
| Datum | |
| Commit (`git log -1 --format=%h`) | |
| Prohlížeč počítače | |
| Telefony (model, systém, prohlížeč) | |
| Čas T (viz Příprava) | |

Výsledek u každého bodu: **OK** / **CHYBA** / **NEPROVEDENO** / **NEJASNÉ**. Hlášení výsledků viz [README.md](README.md).

## Příprava

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| P1 | Etapa 5: `'vp.losovani' => true` (přepínače z etap 1–4 zůstávají zapnuté) | | | |
| P2 | Zvolte **T** = nejbližší celých 10 minut od začátku testu (např. 14:20) a zapište do Záznamu. Kola budou v T+40, T+70 a T+100 min | | | |
| P3 | Zaregistrovat návštěvníky A, B, C (`test.d1.*@example.test`), dokončit registraci z e-mailu | Účty s rolí `visitor` | | |
| P4 | Vstup do administrace: statická stránka `events/admin/lottery` (položka menu, je-li) nebo přímo adresa `events/v1/lottery` | Přehled losování | | |
| P5 | Telefon obsluhy: otevřít `https://<LAN-IP>/`, přihlásit účet s rolí `events_administrator` | Stránka bez varování o certifikátu | | |

## 1. Administrace losování (správce, počítač)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 1.1 | „Nové losování“: název „Losování test“, popis, odkaz na pravidla `https://example.test/pravidla`, zapojit se od (dnes, bez času) do (zítra), stav otevřeno | „Losování uloženo.“; u polí vysvětlivky k odkazu na pravidla a období zapojení | | |
| 1.2 | Kalendář u data | Zavře se kliknutím na den; čas se píše do vedlejšího pole | | |
| 1.3 | Rozpis kol: přidat kolo v čase T+40 (např. `15.00`), T+70 s volbou „losuje se náhradník“ | Kola v seznamu, každé ve vlastním bloku; nové kolo předvyplní datum posledního | | |
| 1.4 | U kola T+40 „Duplikovat“ s časem T+100 | „Kolo zduplikováno i s cenami kola.“; tři kola | | |
| 1.5 | Ceny: „Batoh“ 2 ks bez kola, „Hrnek“ 1 ks pro kolo T+70 | „Cena uložena.“ | | |
| 1.6 | Anketa: otázka jedna volba (povinná, 3 možnosti po řádcích), více voleb (nepovinná), textová | „Otázka uložena.“ | | |
| 1.7 | Duplikovat kolo T+70 s časem T+130 | Nové kolo má náhradníka i cenu Hrnek (ceny kola se kopírují); pak ho smažte | | |
| 1.8 | S rolí `visitor` otevřít `events/v1/lottery` | „Tato stránka je jen pro obsluhu losování. Přihlaste se účtem obsluhy.“, žádná data | | |

## 2. Profil návštěvníka a zapojení (návštěvníci A, B, C)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 2.1 | Nepřihlášen: úvodní stránka | Oddíl „Losování o ceny“: „Zaregistrujte se a zapojte se do losování ve svém profilu…“ (místo „Anketa a slosování“) | | |
| 2.2 | A: profil → „Zapojit se do losování“ bez odpovědi na povinnou otázku | Nezapojí se; „Odpovězte prosím na otázku „…““ | | |
| 2.3 | A: zaškrtnout souhlas s pravidly, vyplnit anketu, odeslat | „Jste zapojeni do losování. Na veletrhu ukažte u vchodu svůj QR kód…“; v profilu QR kód, „Los zatím nebyl vydán.“, odkaz na pravidla funguje | | |
| 2.4 | smtp4dev | E-mail po zapojení s QR kódem a radou uložit si kód | | |
| 2.5 | A: obnovit profil | Tlačítko zapojení už není; kód je stejný jako v e-mailu | | |
| 2.6 | Zapojit B; C se nezapojí | B zapojen | | |
| 2.7 | S rolí `representative`: profil | Blok losování se zapojením není; v konzoli `fetch('events/v1/lottery/<id>/join', {method: 'POST'})` → po obnovení „Do losování se mohou zapojit jen přihlášení návštěvníci.“ | | |
| 2.8 | Správce změní „zapojit se do“ na včerejšek; C zkusí zapojit | Zapojení nejde (mimo období). Pak vrátit na zítřek a C se zapojí | | |

## 3. Můj kód offline (telefon návštěvníka A)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 3.1 | Na telefonu přes HTTPS přihlásit A, profil → „Můj kód (i bez internetu)“ | Stránka „Můj kód pro losování“ s QR | | |
| 3.2 | Zapnout režim letadlo, stránku obnovit (Android Chrome i iPhone Safari) | Stránka se otevře z telefonu i bez připojení | | |
| 3.3 | „Uložit QR do telefonu“ | Obrázek v galerii; jde naskenovat skenerem (bod 4) | | |

## 4. Skener obsluhy (telefon obsluhy)

V detailu losování odkaz na skener; stanoviště zvolte na stránce skeneru. Zapnout kameru (povolit přístup).

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 4.1 | Vchod: naskenovat QR návštěvníka A (v čase ≈ T) | Jméno A, „Los vydán – hraje v kolech T+40, T+70.“ (kolo T+100 ne: nejvýš 2 kola), zvuk/vibrace úspěchu | | |
| 4.2 | Vchod: znovu A | „Přítomnost potvrzena, los platí do <čas skenu + 60 min> – hraje v kolech …“ | | |
| 4.3 | Infostánek: B (zapojen, los nevydán) | „Los zatím nebyl vydán – pošlete návštěvníka ke vchodu.“, zvuk chyby | | |
| 4.4 | Návštěvník s rolí `visitor` (nezapojen) otevře adresu `events/v1/mycode/page` (kód vznikne i bez zapojení); vchod: naskenovat jeho QR | „Není zapojen do losování – ať se zapojí v profilu a přijde znovu.“ | | |
| 4.4b | Ručně zadat neexistující kód `ABCDEFGHJK23` | „Neznámý kód.“ | | |
| 4.5 | Naskenovat QR s jiným textem (např. adresa webu) | „Neplatný kód.“ | | |
| 4.6 | Vchod: B | Los vydán | | |
| 4.7 | **Bez signálu:** zapnout v telefonu obsluhy režim letadlo, naskenovat A na infostánku | Sken uložen v telefonu („Na místě bez signálu“: neodeslané 1) | | |
| 4.8 | Vypnout režim letadlo | Sken se odešle; výsledek prodloužení; čas akce = čas skenu (ne čas odeslání) | | |
| 4.9 | „Vyhledat návštěvníka (bez kódu)“: e-mail C → „Vchod: vydat / prodloužit“ | Los vydán (záloha pro návštěvníka bez telefonu) | | |
| 4.10 | Telefon návštěvníka A: naskenovat vlastní QR fotoaparátem | Stránka „Ukažte kód obsluze“ (návštěvník sám nic nevydá) | | |
| 4.11 | Telefon obsluhy: QR návštěvníka fotoaparátem (mimo skener) | Stránka s návštěvníkem a tlačítkem pro vydání/prodloužení | | |
| 4.12 | Správce přepne losování do stavu uzavřeno; sken | „Losování není otevřené.“; pak vrátit na otevřeno | | |

## 5. Losování kol (telefon obsluhy)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 5.1 | Stránka kola T+40 | „Účastníků s platným losem: N (losů M)“ – N = návštěvníci s vydaným losem (A, B, C) a vydané lístky | | |
| 5.2 | Stránka kola T+100 | A není (jeho los hraje jen v T+40 a T+70), pokud ho mezitím neprodloužil sken po T+40 | | |
| 5.3 | Kolo T+40: „Losovat“ | „Vylosován(a): …“ velkým písmem jméno; Batoh 1. kus | | |
| 5.4 | Losovat znovu | Druhý kus Batohu jinému účastníkovi (nikdo nevyhraje dvakrát) | | |
| 5.5 | U výherce „Převzal na místě“ | Zapsáno s časem a obsluhou | | |
| 5.6 | Kolo T+40 nemá náhradníka | Tlačítko „Nedostavil se – náhradník“ není | | |
| 5.7 | Kolo T+70: Losovat Hrnek, pak „Nedostavil se – náhradník“ | Výhra prvního propadne, „Náhradník: …“ z téhož kola; lze opakovat; když nezbývá nikdo: „Výhra propadla, ale není z koho losovat náhradníka.“ | | |
| 5.8 | Losovat v kole bez účastníků nebo cen | „Není co nebo z koho losovat…“ | | |
| 5.9 | Výherce v profilu | „Vyhráli jste: …“; propadlá výhra „… propadla – nedostavili jste se.“; ve dalších kolech výherce nehraje | | |

## 6. Výhry a export (správce)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 6.1 | Detail losování → výhry | Přehled výherců po kolech se stavem | | |
| 6.2 | Export nevyzvednutých výher (CSV) | Jen nepřevzaté výhry: cena, jméno, e-mail, sloupec Lístek; otevře se v tabulkovém procesoru s diakritikou | | |
| 6.3 | U nevyzvednuté výhry „Převzal v sídle“ | Zmizí z exportu | | |

## 7. Papírové lístky – sada a PDF (D1c, správce)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 7.1 | Detail losování → „Papírové lístky“ → „Nová sada lístků“ 6 ks | „Vytvořeny lístky …“; vytištěno 6, vydáno 0, čísla 0001–0006 | | |
| 7.2 | Další sada 2 ks | Čísla navazují: 0007–0008 | | |
| 7.3 | „Stáhnout PDF“, 4 lístky na A4, rozsah 1–8 | Průběh, pak soubor `losovani-<id>-listky-0001-0008.pdf`; 2 strany; hláška „Hotovo: 8 lístků, 2 strany A4 …“ | | |
| 7.4 | PDF zkontrolovat | Na lístku název losování, Jméno/Příjmení/E-mail, otázky ankety (kroužky u jedné volby, čtverečky u více voleb, čára u textu), souhlas s pravidly s adresou, malé číslo a kód; za čárkovanou čarou ústřižek s QR, velkým číslem a pokyny | | |
| 7.5 | Totéž 2 lístky na A4 | 4 strany, ústřižek vpravo | | |
| 7.6 | Vytisknout 1 stranu v měřítku 100 % | Lístky A6 (4 na A4) odpovídají střihovým čarám | | |
| 7.7 | Přidat do ankety 8 dalších dlouhých otázek a stáhnout PDF 4 na A4 | Buď „…písmo ankety zmenšeno na … %“, nebo „Anketa se na lístek (4 na A4) nevejde. Zvolte 2 lístky na A4…“; pak otázky smazat | | |
| 7.8 | Export CSV lístků | Číslo, kód, adresa QR (`…/events/v1/scan/L…`) | | |
| 7.9 | Vyhledání lístku podle čísla `5` | Lístek č. 0005, los zatím nevydán | | |

## 8. Papírové lístky na místě (D1c, telefon obsluhy)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 8.1 | Vchod: naskenovat QR z ústřižku lístku 0001 (výtisk) | „Lístek č. 0001“, „Los vydán – hraje v kolech …“ | | |
| 8.2 | Vchod: do pole „Kód nebo číslo lístku ručně“ zadat `2` | Lístek č. 0002 vydán | | |
| 8.3 | Infostánek: lístek 0003 (nevydaný) | „Lístek zatím nebyl vydán – lístek se odevzdává u vchodu.“ | | |
| 8.4 | Ručně číslo `99` | „Lístek č. 0099 v tomto losování není.“ | | |
| 8.5 | Druhé losování se sadou lístků; jeho lístek naskenovat ve skeneru prvního losování | „Lístek patří k jinému losování.“ | | |
| 8.6 | Telefon návštěvníka: QR ústřižku fotoaparátem | Text, ať si ústřižek nechá (nic se nevydá). Pozn.: aplikace Fotoaparát v Androidu může u adresy `http://` hlásit „kód není podporován“ – na HTTPS ověřit, že funguje | | |
| 8.7 | Telefon obsluhy: QR ústřižku fotoaparátem | Přehled lístku v administraci | | |
| 8.8 | Losovat kolo, kde hrají lístky 0001, 0002 | Výherce lístek: velkým písmem „Lístek č. 0001“; náhradník v kole s náhradníkem funguje i pro lístky; v exportu nevyzvednutých sloupec Lístek | | |

## 9. Kontrola v databázi (etapa 5)

| # | Dotaz (DB events) | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 9.1 | `SELECT login_login_name_fk, code FROM visitor_code;` | Jeden kód na zapojený účet, 12 znaků; kód není tajný (výjimka v AGENTS.md) | | |
| 9.2 | `SELECT * FROM lottery_presence_event ORDER BY id DESC LIMIT 20;` | Každé vydání a prodloužení s časem, stanovištěm a obsluhou; u skenu z bodu 4.7 čas skenu | | |
| 9.3 | `SELECT number, code FROM lottery_paper_ticket ORDER BY number;` | Lístky 1–8 s unikátními kódy | | |

## 10. Vypnutý přepínač (etapa 6)

`'vp.losovani' => false`.

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 10.1 | Úvodní stránka | Zase „Anketa a slosování“ jako na produkci | | |
| 10.2 | Profil návštěvníka | Bez bloku losování | | |
| 10.3 | `events/v1/lottery`, `events/v1/mycode/page`, `events/v1/scan/ABC` | 404 | | |

## Po testu

Přepínače na `false` (necommitovat); po celém testu obnovte zálohu DB.
