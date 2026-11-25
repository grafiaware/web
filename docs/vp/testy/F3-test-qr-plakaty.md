# Test F3: QR kódy z QRdirectoru, QR webu, podklad plakátu, ověření QR

- **Balík:** F3 (#47); zadání [`F1.md`](../zadani/F1.md) (kap. Rozsah – F3). Navazuje na scénář [F1-F2](F1-F2-test-odkazy.md)
  – odkazy, účet `t_media_manager` a odkaz firmy z promo kitu už existují.
- **Prostředí:** Docker podle [README.md](README.md#prostředí), `master-vp`, site NajdiSi; telefon se čtečkou QR (fotoaparát)
  na stejné síti – pro body se skenem HTTPS podle [DOCKER-WINDOWS.md, kap. 11](../DOCKER-WINDOWS.md#11-telefon-https-a-instalace-capem)
- **Etapy** ([README.md](README.md#etapy)): části 1–5 v etapě 5d (`vp.odkazy` + `vp.qr-plakaty`), část 6 v etapě 6
- **Migrace (`/build`, etapa 0):** events `20261125000001_f3_promo_qr_svg` (jen přidává tabulku)
- **Soubory:** SVG s QR kódem z QRdirectoru (export SVG; když QRdirector není po ruce, vytvořte QR SVG libovolným generátorem
  s adresou odkazu), grafický program pro PDF (Illustrator, Inkscape, Affinity…) – pro bod 3.5 stačí i prohlížeč PDF
- **Účty:** `t_media_manager` (role `media_manager`), `representative` (zástupce firmy s promo kitem), `events_administrator`
- **Automaticky pokryto testy:** vyčištění SVG (skripty, odkazy, styly, entity), adresa přečtená z QR, veřejná https adresa,
  adresa firmy (unit); nahrání a smazání SVG, zástupce firmy, data podkladu plakátu se zveřejněnými sítěmi (integrační)

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
| P1 | Etapa 5d: `'vp.odkazy' => true`, `'vp.qr-plakaty' => true` | | | |
| P2 | `t_media_manager`: otevřít `events/v1/promo`, založit odkaz `f3-test` (vlastník organizátor, cíl úvod s registrací) | Detail odkazu má oddíl **„QR kódy a plakát“** | | |
| P3 | V QRdirectoru vytvořit dynamický QR s cílem `https://<adresa webu>/events/v1/r/f3-test/billboard` a stáhnout SVG | Soubor `.svg` | | |

## 1. QR z QRdirectoru (media manager, počítač)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 1.1 | Oddíl „QR kódy a plakát“ → „Nahrát QR z QRdirectoru“: bez souboru | Tlačítko Nahrát nejde stisknout | | |
| 1.2 | Vybrat SVG z P3 | Náhled QR a text „QR vede na: https://… (adresa QRdirectoru) … ověřte skenem telefonem“ | | |
| 1.3 | Kanál Billboard, popisek „billboard test“ → Nahrát | „QR kód nahrán.“; v tabulce náhled, popisek, kanál, přečtená adresa, „jiná adresa (QRdirector)“ | | |
| 1.4 | Naskenovat náhled v tabulce telefonem | Telefon otevře QRdirector a ten přesměruje na web (úvod s oknem registrace); v přehledu odkazu přibude návštěva v kanálu `billboard` | | |
| 1.5 | Nahrát jako SVG obrázek, který není QR (např. logo ve formátu SVG), kanál Leták | „QR kód nahrán.“ a upozornění „QR kód se v prohlížeči nepodařilo přečíst…“; v tabulce „nepřečteno – ověřte skenem“ | | |
| 1.6 | Nahrát soubor PNG nebo textový soubor přejmenovaný na `.svg` | „Soubor není SVG.“ (nebo „SVG nejde přečíst“), nic se neuloží | | |
| 1.7 | U QR z bodu 1.5 „Smazat“ → potvrdit | „QR kód smazán.“, řádek zmizel | | |
| 1.8 | Přihlášený `events_administrator` pošle formulář nahrání (otevřená stránka z jiné karty, nebo upravený formulář) | „QR kódy může nahrávat jen media manager.“ | | |

## 2. QR webu (PNG)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 2.1 | Media manager: „QR webu (PNG)“ | Stáhne se `qr-f3-test.png`, pod QR adresa odkazu s `/qr` | | |
| 2.2 | Naskenovat PNG telefonem | Otevře web; v přehledu návštěva v kanálu `qr` | | |
| 2.3 | Nepřihlášený / `events_administrator`: adresa `events/v1/promo/<id>/qr.png` | „Přístup odepřen“ | | |

## 3. Podklad plakátu PDF

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 3.1 | Upravit odkaz → Texty ke kopírování → „Text plakátu“: vlastní text s `{url}` → Uložit | „Odkaz uložen.“ | | |
| 3.2 | „Podklad plakátu PDF A4“ | Stáhne se `plakat-podklad-f3-test-a4.pdf`; hláška „Hotovo…“ | | |
| 3.3 | PDF zkontrolovat | Pod sebou s malými šedými popisky: QR webu (kanál `plakat`), QR z QRdirectoru (billboard), text plakátu s krátkou adresou místo `{url}`, adresa k opsání; žádný design | | |
| 3.4 | „Podklad plakátu PDF A3“ | Totéž na A3, větší QR a písmo | | |
| 3.5 | Otevřít PDF v grafickém programu | QR kódy a texty jsou samostatné objekty – jdou vybrat, přesunout a zvětšit bez ztráty kvality (vektor) | | |
| 3.6 | Vytisknout A4 a oba QR naskenovat telefonem | QR webu → web (návštěva v kanálu `plakat`); QR z QRdirectoru → přes QRdirector na web (kanál `billboard`) | | |

## 4. Promo kit zástupce vystavovatele

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 4.1 | Zástupce firmy (`representative`): profil vystavovatele → „Pozvěte lidi na veletrh“ | Nový blok „QR kód a plakát“ s tlačítky „QR kód (PNG)“ a „Podklad plakátu PDF A4 / A3“ | | |
| 4.2 | V profilu firmy zveřejnit Facebook, nezveřejnit Instagram (oba s adresou); uložit | | | |
| 4.3 | „Podklad plakátu PDF A4“ | PDF: QR webu, text plakátu, adresa k opsání, **název firmy**, **adresa firmy**, **Facebook** s adresou; **Instagram ne** (není zveřejněný); QR z QRdirectoru ne (jen u organizátora) | | |
| 4.4 | „QR kód (PNG)“ | QR odkazu firmy s kanálem `qr` | | |
| 4.5 | Zástupce firmy otevře adresu podkladu jiného odkazu `events/v1/promo/<id odkazu f3-test>/poster` | Chyba „Podklad plakátu stáhne jen media manager nebo zástupce firmy odkazu.“ | | |

## 5. Ověření QR (media manager)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 5.1 | V hlavičce stránek odkazů „Ověření QR“ (`events/v1/qr-check`) | Skupiny: Odkazy (F2) – QR webu u aktivních odkazů, QRdirector (F3) – nahraná SVG, Tištěné materiály 2026 – starý QR registrace; se zapnutým `vp.losovani` i Losování (vzorový kód návštěvníka a ústřižek) | | |
| 5.2 | Sloupec Kontrola | QR webu: „https, veřejná adresa webu“ (na `http://` lokálně „není https…“ – očekávané); QRdirector: „adresa QRdirectoru – ověřte skenem…“; starý QR: „jiná adresa (tištěné materiály 2026)…“ | | |
| 5.3 | Naskenovat několik QR z obrazovky telefonem | Otevřou uvedené adresy; vzorové kódy losování nic nezapíšou | | |
| 5.4 | `events_administrator` otevře `events/v1/qr-check` | „Přístup odepřen“ | | |

## 6. Vypnutý přepínač (etapa 6)

`'vp.qr-plakaty' => false` (`vp.odkazy` zatím zapnuté).

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 6.1 | Detail odkazu | Oddíl „QR kódy a plakát“ není; v úpravě odkazu není „Text plakátu“; po uložení odkazu zůstane text plakátu zachovaný (znovu zapnout a ověřit) | | |
| 6.2 | `events/v1/promo/<id>/qr.png`, `…/poster` | 404 | | |
| 6.3 | Promo kit v profilu | Bez bloku „QR kód a plakát“ | | |
| 6.4 | Pak i `'vp.odkazy' => false` a `'vp.losovani' => false`: `events/v1/qr-check` | 404 | | |

## Po testu

Přepínače na `false` (necommitovat); smazat testovací odkaz `f3-test` jen vypnutím (odkazy se nemažou); po celém testu obnovte zálohu DB.
