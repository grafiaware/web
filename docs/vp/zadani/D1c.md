# D1c Papírové anketní lístky v losování

- **Větev roadmapy:** V2 Losování (viz [ROADMAP.md](../ROADMAP.md))
- **Issue:** #108 (navazuje na D1 #46)
- **Řešitel:** Claude
- **Stav zadání:** schváleno (Petr, 16. 11. 2026)

## Cíl

Návštěvník bez chytrého telefonu hraje v témž slosování jako registrovaní: u vchodu odevzdá vyplněný anketní lístek
s předtištěným kódem, obsluha kód naskenuje, ústřižek s velkým číslem si nechá. Při výhře moderátor vyvolá číslo.
Web vytvoří tisková data lístků i s otázkami ankety z D1.
Termín: spolu s D1 nejpozději 10. 1. 2027 (veletrh 27. 1. 2027), jinak zůstává dosavadní papírová anketa a osudí.

## Současný stav

- Papírová anketa (jméno, příjmení, e-mail, otázky) se odevzdá u vchodu a lístky se losují z osudí.
- D1 přináší losování, kola, platnost losu, skener se stanovišti, anketu a losovací službu nad obecným účastníkem.

## Rozsah

Patří sem:
- **Sada lístků k losování:** organizátor zadá počet (např. 300); web vytvoří lístky s pořadovým **číslem**
  (0001, 0002 … v rámci losování) a náhodným **kódem** (12 znaků, stejná abeceda jako kód návštěvníka v D1).
  Další sadu lze přidat (čísla navazují). Kód je identifikátor, ne tajný údaj (výjimka v AGENTS.md).
- **Tisková data:** stránka pro tisk z prohlížeče (A4, CSS pro tisk, „Uložit jako PDF“), na stránce N lístků:
  - **anketní díl:** název losování, kolonky jméno, příjmení, e-mail, otázky ankety z D1 s políčky, souhlas
    s pravidly (text a odkaz), malý kód a číslo;
  - **ústřižek** (oddělený čarou se střihem): **velké číslo**, QR kód, „Při výhře vás vyvoláme číslem. Ústřižek si
    nechte – je dokladem výhry.“, odkaz na registraci na webu;
  - QR obsahuje adresu skenu lístku (`…/events/v1/scan/L<kód>`), odliší se od kódu návštěvníka prefixem.
  - Navíc **export CSV** (číslo, kód, adresa QR) pro vlastní aplikaci na tisk štítků.
- **Sken lístku:** skener D1 rozpozná lístek; u vchodu vydá los lístku (stejné okno 30 min / 2 kola), na infostánku
  prodlouží; výsledek „Lístek 0427 – los vydán“. Lístek ze sady jiného losování nebo neznámý kód → chyba.
  Funguje i fronta skenů offline.
- **Losování:** lístky jsou účastníci téhož slosování (1 los na lístek); vylosovaný lístek se zobrazí jako
  **„Lístek č. 0427“**. Převzetí, náhradník a export nevyzvednutých výher jako v D1 (u lístku bez e-mailu – kontakt
  dohledá organizátor v odevzdané anketě podle čísla).
- **Přehled lístků:** počet vytištěných / vydaných / platných, vyhledání podle čísla.

Nepatří sem:
- přepis odpovědí z papírové ankety do webu (zůstávají na papíře);
- **připojení lístku k profilu** při registraci („Máte lístek? Zadejte kód z ústřižku“) – etapa 2 po veletrhu;
  účet zakládá návštěvník sám (bez zakládání účtů z ručně psaných e-mailů);
- kontrola, že jedna osoba nehraje s lístkem i s profilem (pravidla soutěže; zaznamená se až při připojení v etapě 2).

## Závislosti

D1 (#46) – tabulky losování, skener, `LotteryPresence`, `LotteryDraw`, anketa. Vyvíjí se ve stejné větvi
`vp/d1-losovani` a jednom PR jako samostatné commity (`D1c: …`) – rozhodl Petr 16. 11. kvůli termínu.

## Databáze

Migrace `migrations/events/2026112xxxxxxx_lottery_paper.sql` (po migraci D1, jen přidává):

```sql
CREATE TABLE lottery_paper_ticket (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  lottery_id_fk INT UNSIGNED NOT NULL,
  number INT UNSIGNED NOT NULL,
  code CHAR(12) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  login_login_name_fk VARCHAR(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY lottery_paper_ticket_number_unique (lottery_id_fk, number),
  UNIQUE KEY lottery_paper_ticket_code_unique (code),
  KEY lottery_paper_ticket_login_idx (login_login_name_fk),
  CONSTRAINT lottery_paper_ticket_lottery_fk FOREIGN KEY (lottery_id_fk) REFERENCES lottery (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT lottery_paper_ticket_login_fk FOREIGN KEY (login_login_name_fk) REFERENCES login (login_name) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE lottery_presence
  ADD COLUMN paper_ticket_id_fk INT UNSIGNED NULL DEFAULT NULL,
  ADD UNIQUE KEY lottery_presence_paper_unique (paper_ticket_id_fk),
  ADD CONSTRAINT lottery_presence_paper_fk FOREIGN KEY (paper_ticket_id_fk) REFERENCES lottery_paper_ticket (id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE lottery_winner
  ADD COLUMN paper_ticket_id_fk INT UNSIGNED NULL DEFAULT NULL,
  ADD KEY lottery_winner_paper_idx (paper_ticket_id_fk),
  ADD CONSTRAINT lottery_winner_paper_fk FOREIGN KEY (paper_ticket_id_fk) REFERENCES lottery_paper_ticket (id) ON DELETE CASCADE ON UPDATE CASCADE;
```

- Přítomnost a výhra mají vyplněný buď login (D1), nebo lístek (D1c) – hlídá kód (CHECK se nepoužije kvůli
  rozdílům verzí MySQL/MariaDB).
- `login_login_name_fk` u lístku je pro etapu 2 (připojení k profilu), v D1c zůstává prázdný.
- Návrat zpět (cizí klíč se ruší zvlášť před indexem a sloupcem, kvůli MySQL):
  `ALTER TABLE lottery_winner DROP FOREIGN KEY lottery_winner_paper_fk;`
  `ALTER TABLE lottery_winner DROP COLUMN paper_ticket_id_fk;`
  `ALTER TABLE lottery_presence DROP FOREIGN KEY lottery_presence_paper_fk;`
  `ALTER TABLE lottery_presence DROP INDEX lottery_presence_paper_unique, DROP COLUMN paper_ticket_id_fk;`
  `DROP TABLE lottery_paper_ticket;`

## Rozhraní

- Model: `LotteryPaperTicket…` (Dao, Repo, Entity, hydrátor).
- Účastník losování `PaperTicketParticipant` (rozhraní z D1); `LotteryScan` rozpozná prefix `L`.
- Routy (`$feature = 'vp.losovani'`, obsluha / `events_administrator`):

| Metoda | Routa | Kdo | Co |
|---|---|---|---|
| POST | `/events/v1/lottery/:id/paper` | `events_administrator` | vytvoření sady lístků (počet) |
| GET | `/events/v1/lottery/:id/paper/print?from=&to=` | `events_administrator` | tisková stránka |
| GET | `/events/v1/lottery/:id/paper.csv` | `events_administrator` | export CSV pro tisk štítků |
| GET | `/events/v1/lottery/:id/paper?q=` | obsluha | přehled, vyhledání podle čísla |

- Šablony: tisková stránka (A4, lístek + ústřižek, QR jako obrázek), přehled lístků; zobrazení „Lístek č.“
  ve skeneru, losování a exportu.

## Přepínač

Stejný jako D1: `vp.losovani`.

## Akceptační kritéria

1. Vytvoření sady 300 lístků dá čísla 0001–0300 s různými kódy; další sada pokračuje 0301…
2. Tisková stránka vytiskne lístky s otázkami ankety z D1 a ústřižkem s velkým číslem a QR; QR jde naskenovat
   skenerem D1 i z výtisku na běžné kancelářské tiskárně.
3. Sken lístku u vchodu vydá los se stejnou platností jako u registrovaného; opakovaný sken prodlouží; neznámý
   kód nebo lístek jiného losování → chyba. Offline fronta funguje i pro lístky.
4. V losování hraje lístek s platným losem stejně jako registrovaný (1 los); výherce se zobrazí „Lístek č. 0427“;
   převzetí a náhradník fungují; export nevyzvednutých výher obsahuje číslo lístku.
5. Vypnutý přepínač = nic z toho není vidět.

## Testy

- Unit: generátor sady (čísla navazují, kódy unikátní), rozpoznání kódu lístku ve skenu, losování se smíšenými
  účastníky (váhy).
- Integrační: Dao lístků, unikátnost čísla a kódu, přítomnost a výhra s lístkem.
- Ruční: tisk 2 stran na tiskárně, sken výtisku telefonem (Android, iPhone), losování s 2 registrovanými a 3 lístky.

## Stav a další krok

16. 11. 2026: návrh zadání podle rozhodnutí Petra (15. 11.): ústřižek s velkým číslem a QR, vyvolání číslem,
lístky jako účastníci téhož slosování, generátor tiskových dat ve webu, anketa z D1. SQL ověřeno na MariaDB 10.11,
MySQL 8 zatím ne. Další krok: schválení (spolu s revizí D1), implementace po modelu D1.

## Otevřené otázky

Zodpovězeno 16. 11. 2026: 4 lístky na A4 (může se změnit); D1 a D1c v jedné větvi `vp/d1-losovani` a jednom PR,
oddělené commity.
