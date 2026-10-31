# Roadmapa změn webu – Veletrh práce

Stav k 12. 10. 2026, aktualizováno 22. 10. 2026 (PES z Packagistu, kontrola testů). Z obou návrhů změn akce potřebuje vývoj webu 51 změn. Jsou rozdělené do 16 větví roadmapy
nad technickým základem Z. Větve V1–V12 jdou do produkce samostatně (potřebují jen Z), V13–V16 začínají až za uzlem,
kde se spojí jejich předpoklady.

Větve roadmapy nejsou git větve, ale skupiny balíků (štítky `vp:Z`, `vp:V1` … na GitHubu).
Každý balík (kód jako `A1`, `D1`) se vyvíjí ve vlastní git větvi a do `master-vp` jde samostatně.

## Větve a balíky

| Větev | Balíky | Závisí na | Samostatně do produkce |
|---|---|---|---|
| **Z Základ** | Z1 automatická synchronizace účtu auth → events při potvrzení registrace (místo tlačítka Synchro); Z2 plánovač úloh (cron); Z3 sdílené e-mailové šablony, odesílatel v konfiguraci; Z4 migrace DB events na hostingu (tabulka `login` a cizí klíče na ni; oprava pro MariaDB v PR #57); Z5 přístupnost (WCAG) a mobil; Z6 *(později)* hashovat heslo hned při registraci, neposílat ho e-mailem, mazat nepotvrzené registrace; Z7 testy v CI (PES je od 10/2026 na Packagistu, CI už nic neblokuje; nejdřív opravit stávající chyby testů, viz níže); Z8 přepínače funkcí; Z9 *(později)* produkční přístupy do DB mimo repozitář; Z10 anonymizované dumpy DB z aktuální produkce (#58) | — | ano |
| **V1 Registrace** | A1 registrace jméno + e-mail, heslo volitelně, `login_name` z části e-mailu před zavináčem; A2 přihlášení odkazem z e-mailu; A3 úklid nedokončených registrací; A4 texty o ochraně dat; A5 mobilní formuláře; A7 Google/Seznam/MojeID (2. fáze) | Z1, Z8 | ano |
| **V2 Losování** | D1 losy vázané na účet, bonus za úplný profil, administrace slosování, výherci; D1b online kategorie | Z1, Z8 | ano |
| **V3 Souhlasy** | C1 granulární souhlasy v DB (text, verze, čas); C2 smazání účtu a doba uchování; C3 export vlastních dat | Z1, Z2 | ano |
| **V4 QR vstupenka** | B1 osobní QR token, vstupenka e-mailem a v profilu; B2 odbavení na vstupu, evidence vstupů, role obsluha | Z1 | ano |
| **V5 Stánky** | S1 číslo stánku a zóna (company_parameter); S2 QR stánku a pozice k tisku; S3 navštívené stánky v profilu; S4 mapa haly s filtrem | — (S3: Z1) | ano |
| **V6 Program** | G0 převod programu z array modelů do DB events (event, event_content, institution, enroll) s administrací; G1 tematické linie a bloky; G2 hlasování o tématech; G3 hodnocení přednášek; G4 záznamy a materiály jen po přihlášení | — (G1, G3, G4 po G0) | ano |
| **V7 Rezervace** | jádro slotů s kapacitou; R1 sloty pro školy; R2 schůzky u stánku; R3 kontrola CV, fotokoutek, poradna, pitch; R4 online slot s odkazem na hovor | Z1 | ano |
| **V8 Firmy** | I1 rozšířený profil (benefity, fotky, praxe); I1b mzdové rozpětí a příznaky pozic + filtry; I2 balíčky účasti a zvýrazněné pozice; I3 prodloužená online fáze (+ pohled `pocet_pozic` bez pevného ročníku) | — | ano |
| **V9 Měření** | F1 zdroj registrace (UTM, kód odkazu); F2 vlastní odkazy a promo kit; F3 QR plakáty s měřením; F4 přehled KPI a trychtýř | Z1, Z8 | ano |
| **V10 Reakce** | H5 stav reakce v profilu; I3r správa reakcí u vystavovatele; I4 export kandidátů CSV; H6 souhrnný e-mail po veletrhu | Z3 | ano |
| **V11 Profil** | H2 strukturovaný profil; H2b ukazatel úplnosti; H1 oblíbené = osobní plán; H3 doporučení; H4 upozornění na nové pozice | Z1; H4 + V3, Z2; přednášky v H1 po G0 | ano |
| **V12 Na místě** | J1 PWA; J4 živé obrazovky | — | ano |
| **V13 Leady** | B3 sken QR návštěvníka zástupcem firmy, předání CV se souhlasem; B3b zkrácená registrace u stánku; I4b export leadů; I5 report pro zaměstnavatele; D2 losy za stánky; I14 pozvánka během dne | uzel U1: V4 + V3 | ne |
| **V14 Studenti** | E1 studentský účet, kód třídy, role učitel, souhlas zákonného zástupce; E2 studentská mise; E3 žebříček a report pro školu; E4 převod na plný profil | uzel U2: V5 + V2 + V3 | ne |
| **V15 Vitrína** | I8 vitrína kandidátů; C4 diskrétní režim; I7 matching a pozvánka ke stánku | uzel U3: V11 + V3 + V7 | ne |
| **V16 Online** | K2 tematické online veletrhy; K3 videohovory; I13 chat; I12 API pro ATS; H7 předvyplnění z CV; H8 kontrola CV | uzel U4: V7 + V11 + V6 | ne |

## Pořadí

Všechny úpravy jdou v jedné vlně; větve nemají prioritu, pořadí určují jen technické závislosti.

1. **Bez závislostí, hned a souběžně:** Z8 přepínače, Z3 e-mailové šablony, Z7 oprava testů + CI, Z2 plánovač, Z4 (PR #57), Z10 anonymizované dumpy.
2. **Po Z4:** Z1 automatická synchronizace účtů.
3. **Samostatné větve V1–V12** souběžně, každý balík jakmile má své závislosti (Z1, Z8, Z3…); v V6 nejdřív G0, pak G1, G3, G4.
4. **Uzly:** U1 → V13 (po V3 + V4), U2 → V14 (po V2 + V3 + V5), U3 → V15 (po V3 + V7 + V11), U4 → V16 (po V6 + V7 + V11).

**Později (mimo tuto vlnu):** Z6 – heslo je v `registration` do potvrzení záměrně dočasně v čitelné podobě, aby šlo
ručně dokončit nepovedenou registraci vystavovatele; Z9 – přístupy do DB mimo repozitář (v produkci je DB dostupná jen z PHP skriptů).
A1 (registrace bez hesla) se ukládání hesla netýká.

## Zjištění ze struktur DB (12. 10. 2026)

| Zjištění | Dopad | Balík |
|---|---|---|
| Heslo leží v `registration.password_hash` nehashované do potvrzení; e-mail Grafii o zástupci vystavovatele ho obsahuje čitelně | záměrný dočasný stav kvůli ručnímu dokončení registrace vystavovatele; řešit později | Z6 |
| `events.login` na hostingu má jen `login_name` varchar(50), cizí klíče RESTRICT; vývojová DB a kód mají `created`, `updated`, `deleted_due_to_auth`, varchar(100), `ON UPDATE CASCADE` | synchronizace na produkci selže do migrace | Z4, Z1 |
| E-mail je jen v `registration`, bez unikátního indexu | přihlášení e-mailem vyžaduje unikátní e-mail | A1, A2 |
| Jedna role na účet (`credentials.role_fk`) | role obsluha a učitel | B2, E1 |
| CV a dopisy jako `mediumblob`, reakce nesou kopii profilu | mazání a retence musí zahrnout kopie | C2 |
| Cizí klíče na `login` bez `ON DELETE` | smazání účtu v pořadí nebo anonymizace přejmenováním | C2 |
| Firmy po ročnících (`company.version_fk`) | stánek a balíček do `company_parameter` | S1, I2 |
| Pohled `pocet_pozic` s pevným ročníkem '2026' | ruční změna každý rok | I3 |
| Tabulky programu (event…) na produkci nepoužité, program běží z array modelů | nejdřív převod G0 | G0 |

## Stav testů (22. 10. 2026, PHP 8.4, PES z Packagistu)

Předpoklad pro Z7: testy musí být zelené. Příčiny chyb jsou v kódu a testech repa (autoload, fixtures, chybějící soubory), ne v balících PES.

| Sada | Výsledek | Příčiny chyb |
|---|---|---|
| unit | opraveno v Z7 (PR) – 76 testů, 0 chyb, 1 přeskočený (kampaň 2021, chybí šablona) | dříve: chybějící PSR-4 `Form\\`, mail fixtures s prázdným logem, `HtmlMessage` TypeError u vnořených polí, chybějící šablona `podekovani-odkazy-igelitka2.php` |
| integration | 497 testů, 60 chyb, 9 selhání (prázdné DB se strukturou) | většina: test očekává data nebo DB red (`najdisi`); `Events\Model\Entity\Login::getDeletedDueToAuth()` vrací `string`, z DB přijde `null`/int (týká se Z1); zastaralé testy Red (`ContextProviderMock::changeContext`, `ItemAction::setTypeFk`) |

## Změny akce → balíky webu

Zdroj: **S** = Návrh rozšíření služeb, **R** = Návrh zvýšení registrací, číslo = kapitola.

| Změna akce | Zdroj | Balík | Větev |
|---|---|---|---|
| Registrace ve 30 s (jméno + e-mail) | R 2, R 10 | A1 | V1 |
| Přihlášení bez hesla | R 2 | A2 | V1 |
| Přihlášení přes Google, Apple, Seznam, MojeID | R 2 | A7 | V1 |
| Jasný text o ochraně dat, mobil na prvním místě | R 2, R 8 | A4, A5 | V1 |
| Losování jen pro registrované, losy za profil, losování v aplikaci | R 3.1, R 4, S 5 | D1 | V2 |
| Online losování pro nepřítomné | R 7 | D1b | V2 |
| Oddělené souhlasy, smazání účtu, doba uchování | R 8, S 6.4 | C1–C3 | V3 |
| Reaktivační kampaň minulých ročníků | R 5.5 | Sendmail + C1 | V3 |
| Osobní QR, vstupenka, rychlý vstup | R 3.1, S 2.1 | B1, B2 | V4 |
| Podíl registrovaných mezi návštěvníky | R 9, S 6.6 | B2 | V4 |
| QR stánku, nástěnka pozic s QR | S 5 | S2 | V5 |
| Mapa haly s filtrem oborů | S 6.5, S 2.2 | S1, S4 | V5 |
| Seznam navštívených stánků | S 2.2, S 5 | S3 | V5 |
| Tematické linie a časové bloky | S 0, S 4.1 | G0, G1 | V6 |
| Hlasování o tématech předem | S 4.3 | G2 | V6 |
| Hodnocení přednášek | S 4.3 | G3 | V6 |
| Záznamy, stream a materiály jen po přihlášení | R 3.2, R 7, S 4.3 | G4 | V6 |
| Časové sloty pro školy | S 3.2, S 7 | R1 | V7 |
| Rezervace schůzky u stánku | S 1.1, R 3.1 | R2 | V7 |
| Rezervace kontroly CV, fotokoutku, poradny, pitche | R 3.1, S 2.2 | R3 | V7 |
| Videohovor se stánkem | R 7 | R4 | V7 |
| Balíčky účasti, zvýrazněné pozice | S 1.1, S 6.3 | I2 | V8 |
| Rozšířený profil firmy | S 1.1 | I1, I1b | V8 |
| Označení pro skupiny (úvazky, cizinci, OZP, 50+, okamžitý nástup), studentská zóna | S 2.4, S 3.2 | I1b | V8 |
| Prodloužená online fáze | S 1.3 | I3 | V8 |
| Promo kit a vlastní odkazy firem, škol, ÚP | R 5.1, R 5.3 | F1, F2 | V9 |
| QR plakáty | R 4, R 5.4 | F3 | V9 |
| Trychtýř registrací, KPI | R 9, S 6.6 | F4 | V9 |
| Stav reakce v profilu | R 3.2, S 2.3 | H5 | V10 |
| Follow-up firem, export kandidátů | S 1.3 | I3r, I4 | V10 |
| Souhrnný e-mail po veletrhu | S 2.3, S 7 | H6 | V10 |
| Postupné doplňování profilu | R 2 | H2b | V11 |
| Doporučení firem, pozic, přednášek | S 2.1 | H2, H3 | V11 |
| Osobní plán návštěvy | S 2.1, R 3.1 | H1 | V11 |
| Upozornění na nové pozice | S 2.3, R 3.2 | H4 | V11 + V3 |
| Mobilní web / PWA | S 2.2 | J1 | V12 |
| Živá tabule „Dnes nabíráme“ | S 1.2, S 5 | J4 | V12 |
| Skenování QR návštěvníků firmou, CV jedním skenem | S 1.2, R 3.1 | B3 | V13 |
| Sken neregistrovaného → zkrácená registrace | R 4 | B3b | V13 + V1 |
| Report pro zaměstnavatele | S 1.3 | I5 | V13 |
| Losování vázané na návštěvu stánku | S 1.2, S 5 | D2 | V13 + V2 |
| Okamžitá pozvánka během dne | S 5 | I14 | V13 |
| Studentský účet, kód třídy, ochrana nezletilých | R 5.2, R 6, S 3.4 | E1 | V14 |
| Studentská mise, žebříček tříd | S 3.2, S 5 | E2, E3 | V14 |
| Portál a report pro školy | S 3.3 | E3 | V14 |
| Vitrína kandidátů | S 2.2, S 6.3 | I8 | V15 |
| Diskrétní režim | S 2.4, R 8 | C4 | V15 |
| Předběžný matching | S 1.1 | I7 | V15 |
| Tematické online veletrhy | S 6.1, R 7 | K2 | V16 |
| Chat se zaměstnavatelem, ATS | S 5, S 1.3 | I13, I12 | V16 |
| Předvyplnění z CV, kontrola CV | R 2, S 2.1 | H7, H8 | V16 |
