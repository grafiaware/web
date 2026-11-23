# Test F1 + F2: trvalé odkazy s měřením, zdroj registrace, promo kit

- **Balíky:** F1 zdroj registrace, F2 trvalé odkazy a promo kit (#47); zadání [`F1.md`](../zadani/F1.md).
  F3 (QR kódy z QRdirectoru, podklad plakátu, ověření QR) má vlastní scénář v dalším PR.
- **Prostředí:** Docker podle [README.md](README.md#prostředí) (MariaDB, smtp4dev), `master-vp`, site NajdiSi;
  pro část 4 (sociální sítě) veřejná adresa webu s HTTPS – jinak body 4.3–4.4 NEPROVEDENO
- **Etapy** ([README.md](README.md#etapy)): části 1–6 v etapě 5c (`vp.odkazy`), část 7 v etapě 6
- **Migrace (`/build`, etapa 0):** auth `20261124000001_f1_media_manager`, `20261124000002_f1_registration_source`;
  events `20261124000001_f1_network_channel`, `20261124000002_f1_promo_link` (jen přidávají; do `network` přibudou X a TikTok)
- **Účty:** `t_media_manager` (role `media_manager` – přidělí `t_supervisor` nebo `t_auth_role_grantor` ve správě účtů);
  `t_representative` (zástupce firmy); nové registrace se smyšlenými adresami `test.f1.*@example.test`
- **Automaticky pokryto testy:** kód odkazu a kód z názvu firmy, kanály, roboti, cíl jen na tento web, zdroj v cookie,
  souhlas z cookie lišty, souhrn přehledu (unit); počítadlo, historie cíle, odkaz firmy, zdroj registrace (integrační)

## Záznam testu

| Údaj | Hodnota |
|---|---|
| Tester | |
| Datum | |
| Commit (`git log -1 --format=%h`) | |
| Prohlížeče (počítač, telefon) | |

Výsledek u každého bodu: **OK** / **CHYBA** / **NEPROVEDENO** / **NEJASNÉ**. Hlášení výsledků viz [README.md](README.md).

## Příprava

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| P1 | Etapa 5c: `'vp.odkazy' => true` (přepínače z etap 1–5 zůstávají zapnuté, hlavně `vp.registrace-emailem`) | | | |
| P2 | Ve správě účtů přidělit účtu `t_media_manager` roli **media_manager** | Role je v seznamu rolí a jde přidělit | | |
| P3 | V redakčním systému založit položku menu administrace typu statická stránka **„odkazy“** se šablonou `events/admin/promo`. Když položku nejde založit, otevírejte přímo adresu `events/v1/promo` | Stránka se otevře | | |

## 1. Správa odkazů (media manager, počítač)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 1.1 | Otevřít `events/v1/promo` přihlášený jako `t_events_administrator`, pak nepřihlášený | „Přístup odepřen“ (jen media manager) | | |
| 1.2 | Přihlásit `t_media_manager`, otevřít `events/v1/promo` | Seznam odkazů (prázdný), filtr, formulář Nový odkaz | | |
| 1.3 | Nový odkaz: kód `Test Kod` | Odmítnuto – kód jen malá písmena bez diakritiky, číslice, pomlčka | | |
| 1.4 | Nový odkaz: kód `test-vp`, název „Test VP billboard“, od koho Organizátor, cíl „Úvod s oknem registrace“ | Odkaz založen, otevře se jeho stránka; historie cíle má 1 řádek | | |
| 1.5 | Založit druhý odkaz se stejným kódem `test-vp` | Odmítnuto – kód už má jiný odkaz | | |
| 1.6 | U `test-vp` změnit cíl na „Jiná stránka webu“ a vložit adresu `https://www.google.com/` | Odmítnuto – cíl musí být stránka tohoto webu | | |
| 1.7 | Otevřít libovolnou stránku webu (např. program), zkopírovat adresu z adresního řádku a vložit jako cíl | Uloženo; v historii cíle přibude řádek s vaším účtem | | |
| 1.8 | U odkazu, který ještě nemá návštěvy, změnit kód na `test-vp2` a zpět na `test-vp` | Jde to | | |

## 2. Návštěva odkazu a počítání (počítač a telefon)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 2.1 | V anonymním okně otevřít `<web>/events/v1/r/test-vp/fb` | Otevře se cílová stránka z bodu 1.7 | | |
| 2.2 | Na stránce odkazu `test-vp` (obnovit) | Návštěvy: kanál Facebook 1 | | |
| 2.3 | Otevřít `…/r/test-vp/plakat`, `…/r/TEST-VP/li`, `…/r/test-vp`, `…/r/test-vp/nesmysl` | Vždy cílová stránka; v přehledu Plakát 1, Linkedin 1, neuveden 1, jiný (neznámý kód) 1 | | |
| 2.4 | Otevřít `…/r/neexistuje/fb` | Úvodní stránka webu, nic se nezapočítá | | |
| 2.5 | U `test-vp` změnit cíl zpět na „Úvod s oknem registrace“, otevřít `…/r/test-vp/billboard` | Úvodní stránka s otevřeným oknem registrace – stejná adresa vede na nový cíl | | |
| 2.6 | Kód odkazu, který už má návštěvy, zkusit změnit | Pole kódu je zamčené s vysvětlením | | |
| 2.7 | Odkaz vypnout (zrušit „Aktivní“), otevřít `…/r/test-vp/fb` | Úvodní stránka bez okna registrace; počet návštěv se nezmění. Pak odkaz znovu zapnout | | |
| 2.8 | „Návštěvy po dnech“ na stránce odkazu | Dnešní den s počty po kanálech | | |

## 3. Zdroj registrace a souhlas s cookies

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 3.1 | Nové anonymní okno: v cookie liště **odmítnout vše**, otevřít `…/r/test-vp/fb`, zaregistrovat `test.f1.a@example.test` | Registrace proběhne; v přehledu u `test-vp` Registrace 1 (kanál Facebook) | | |
| 3.2 | Registraci `test.f1.a` dokončit odkazem z e-mailu (smtp4dev) – klidně v jiném prohlížeči | Přehled: Potvrzené 1 | | |
| 3.3 | Nové anonymní okno, odmítnout cookies, otevřít `…/r/test-vp/ig`, zavřít okno. Nové okno bez odkazu, zaregistrovat `test.f1.b@example.test` | Registrace se odkazu **nepřipíše** (bez souhlasu jen do zavření prohlížeče) | | |
| 3.4 | Nové anonymní okno: otevřít `…/r/test-vp/ig`, v cookie liště **přijmout vše** (nebo povolit „analytika“). Vývojářské nástroje → Cookies | Cookie `vp_zdroj` s platností 30 dní | | |
| 3.5 | Ve stejném okně v nastavení cookie lišty analytiku vypnout | Cookie `vp_zdroj` zmizí | | |
| 3.6 | Znovu povolit analytiku; přejít na jinou stránku a zaregistrovat `test.f1.c@example.test` | Registrace se připíše `test-vp`, kanál Instagram | | |
| 3.7 | Nové okno, přijmout cookies, otevřít `<web>/?utm_source=facebook&utm_campaign=vpv2027`, zaregistrovat `test.f1.d@example.test` | V přehledu řádek UTM `facebook / vpv2027`, Registrace 1 | | |
| 3.8 | Vývojářské nástroje → hodnota cookie `vp_zdroj` | Jen kód odkazu / kanál / UTM a čas – žádný e-mail ani jméno | | |

## 4. Podklady ke zkopírování (ctrl+c / ctrl+v)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 4.1 | Na stránce odkazu `test-vp`, část „Podklady ke zkopírování“ | Skupiny Tisk, Web, E-mail, Sociální sítě (Facebook, Instagram, Linkedin, Youtube, X, TikTok, Jiná), QR kód | | |
| 4.2 | U Facebooku „Kopírovat text“ a vložit do poznámkového bloku | Text s adresou `…/events/v1/r/test-vp/fb` | | |
| 4.3 | Vložit adresu kanálu `li` do nového (nezveřejněného) příspěvku na LinkedIn / Facebooku | Náhled odkazu se načte (obecný – web zatím nemá značky Open Graph, #143); příspěvek nezveřejňujte | | |
| 4.4 | Otevřít vloženou adresu z aplikace sociální sítě v telefonu | Cílová stránka; v přehledu návštěva v kanálu sítě (náhled sítě se nepočítá) | | |
| 4.5 | „Kopírovat HTML“ u Webu, vložit do testovací stránky v redakčním systému (zdrojový kód) a kliknout | Odkaz vede na cíl, návštěva v kanálu Web | | |
| 4.6 | Upravit text příspěvku u odkazu (Texty ke kopírování) s `{url}` a `{name}` | Podklady ukazují upravený text s doplněnou adresou a názvem | | |

## 5. Promo kit zástupce vystavovatele

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 5.1 | Přihlásit `t_representative`, vybrat firmu, otevřít Profil vystavovatele | Blok „Pozvěte lidi na veletrh“ s odkazem firmy (kód z názvu firmy, bez „s.r.o.“) | | |
| 5.2 | U media managera v seznamu odkazů | Odkaz firmy: od koho Vystavovatel, firma vyplněná, cíl „Úvod s oknem registrace“ | | |
| 5.3 | Zástupce: zkopírovat adresu kanálu `fb`, otevřít v anonymním okně | Úvod s oknem registrace; v promo kitu návštěv 1 | | |
| 5.4 | Znovu otevřít profil vystavovatele | Stejný odkaz (nevznikne nový) | | |
| 5.5 | Media manager změní cíl odkazu firmy na profil firmy (adresa stránky firmy na webu) | Adresa z promo kitu vede na profil firmy | | |
| 5.6 | Zástupce jiné firmy | Vidí jen odkaz a počty své firmy | | |

## 6. Přehled a export

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 6.1 | Přehled `events/v1/promo` – řádek Celkem | Součty návštěv a registrací odpovídají řádkům | | |
| 6.2 | Filtr Od koho = Vystavovatel | Jen odkazy firem, bez řádků UTM | | |
| 6.3 | Filtr období jen na včerejšek | Dnešní návštěvy ani registrace se nezapočítají | | |
| 6.4 | CSV | Soubor otevřený v Excelu / LibreOffice: čeština v pořádku, řádek na odkaz × kanál, řádky UTM | | |
| 6.5 | DB events: tabulka `promo_link_visit` | Jen součty za den a kanál – žádné IP adresy ani údaje o návštěvnících | | |

## 7. Vypnutý přepínač (etapa 6)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 7.1 | `'vp.odkazy' => false`; otevřít `…/events/v1/r/test-vp/fb` a `events/v1/promo` | 404 (funkce vypnutá) | | |
| 7.2 | Profil vystavovatele | Bez bloku „Pozvěte lidi na veletrh“ | | |
| 7.3 | Registrace nového účtu po návštěvě odkazu (s cookie `vp_zdroj` z části 3) | Registrace funguje, zdroj se neukládá | | |
| 7.4 | Profil vystavovatele – sociální sítě firmy | Ve výběru sítí jsou i X a TikTok (nové řádky `network`, přepínač je neskrývá) | | |
