# Test R1: rezervace míst na přednáškách pro školy

- **Balík:** R1 (#48) – zadání [`docs/vp/zadani/R1.md`](../zadani/R1.md)
- **Prostředí:** Docker podle [README.md](README.md#prostředí) (MariaDB, smtp4dev na http://localhost:5000, HTTPS i z telefonu), větev `master-vp`, site NajdiSi
- **Etapa:** 5b – viz [README.md](README.md#etapy); část 7 v etapě 6 (vše vypnuto)
- **Migrace:** events `20261122000001_r1_school_reservation` (spouští se v etapě 0 s ostatními přes `/build`)
- **Navazuje na:** G0 (rozpis programu, správa eventů `vp/event`), A1 (registrace e-mailem) – testovací účty z etapy 2
- **Účty:** testovací účty z [A3–A9](A3-A6-A8-A9-test-sprava-uctu.md) bod 1.2. Jméno účtu je `t_` + název role, který účet má:
  účet `t_events_administrator` má roli `events_administrator`, účet `t_visitor` roli `visitor`. Role samy prefix `t_` nemají.
  Kdo účty nemá, použije libovolný účet s danou rolí.
- **Kdo co dělá:** přípravu a části 1 a 4 (správa rezervací) dělá účet s rolí **events_administrator** – jen ta roli správy rezervací má;
  pedagog v části 2 je nepřihlášený

## Záznam testu

| Údaj                              | Hodnota |
| --------------------------------- | ------- |
| Tester                            |         |
| Datum                             |         |
| Commit (`git log -1 --format=%h`) |         |
| Prohlížeč / telefon               |         |

Výsledek u každého bodu: **OK** / **CHYBA** / **NEPROVEDENO** / **NEJASNÉ**.

## Příprava

| #  | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
| -- | --------- | ------------------ | -------- | -------- |
| P1 | Lokálně (bez commitu) v `ConfigurationFeatures.php` zapnout `'vp.rezervace' => true` (ostatní přepínače z předchozích etap zůstávají zapnuté) | – | | |
| P2 | V redakčním systému založit položky menu typu statická stránka: **„rezervace škol“** se šablonou `vp/rezervace-skol` (veřejné menu – formulář pro pedagogy) a **„rezervace“** se šablonou `events/admin/reservation` (menu administrace). Když statické stránky přes menu ještě nefungují, veřejný formulář označte NEPROVEDENO a administraci otevírejte přímo adresou `events/v1/reservation` | Stránky se otevřou | | |
| P3 | Přihlásit se účtem s rolí `events_administrator` (`t_events_administrator`). Otevřít v menu administrace položku se šablonou `vp/event-content-type` (založená v [G0](G0-test-program.md) P2) | V seznamu typů obsahu je typ **Přednáška** (kód `prednaska`); když chybí, přidat ho (G0 bod 1.1) | | |
| P3a | Položka menu `vp/event-content`: založit 3 obsahy typu Přednáška (např. „Jak napsat životopis“, „Pohovor nanečisto“, „Práce v zahraničí“). Když z testu G0 už jsou, použít je | Obsahy jsou v seznamu | | |
| P3b | Položka menu `vp/event`: pro každý obsah z P3a „Nový event“ – Datum = den akce (stejný den, který zadáte v 1.1 jako den nabídky), Od/Do např. 9:30–10:15, 10:30–11:15, 13:00–13:45, Obsah = přednáška, zaškrtnout **Zveřejnit na webu**, Kapacitu zatím nevyplňovat | Tři eventy v seznamu; formulář eventu má pole **Kapacita** | | |
| P4 | V `vp/event` u eventů z P3b nastavit kapacitu: první přednáška 40, druhá 30, třetí nechat prázdnou | Uloží se; v přehledu eventu „kapacita: 40“; neplatná kapacita (např. `abc`) → hláška „Kapacita musí být celé kladné číslo…“ | | |

## 1. Nabídka a výběr přednášek (účet s rolí `events_administrator`)

| #   | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
| --- | --------- | ------------------ | -------- | -------- |
| 1.1 | Otevřít administraci rezervací (`events/admin/reservation` nebo `events/v1/reservation`), založit nabídku (název, kód `skoly-2027`, den akce) | Nabídka ve stavu „připravuje se“; uzávěrka změn se předvyplní na 2 dny před akcí 23:59 | | |
| 1.2 | V části „Přednášky v nabídce“ zaškrtnout všechny tři přednášky a uložit | V přehledu po přednáškách tři přednášky; u třetí „Bez kapacity – nelze rezervovat“ | | |
| 1.3 | Otevřít veřejný formulář, dokud je nabídka „připravuje se“ | „Rezervace pro školy zatím nejsou otevřené.“ | | |
| 1.4 | Nastavit stav „otevřená“ a uložit | V záhlaví nabídky „formulář na webu přijímá rezervace“ | | |
| 1.5 | Nastavit „Rezervace od“ na zítřek, uložit, otevřít formulář; pak pole vymazat a uložit | Formulář: „Rezervace začnou …“; po vymazání zase přijímá | | |
| 1.6 | Přihlásit se účtem s rolí `visitor` (`t_visitor`), otevřít `events/v1/reservation` | „Tato stránka je jen pro správce rezervací…“ | | |

## 2. Rezervace pedagogem bez přihlášení (telefon i počítač)

Odhlásit se (nebo anonymní okno). Testujte aspoň jednou z telefonu.

| #   | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
| --- | --------- | ------------------ | -------- | -------- |
| 2.1 | Otevřít formulář | Texty souhlasů a odkaz na zásady s upozorněním „Nutno nahradit“ (do vyřešení #140); výběr přednášky s časem a „volno N míst“; třetí přednáška „nelze rezervovat“ (nejde vybrat); cílové obory podle zadání; počet pedagogů předvyplněný 1 | | |
| 2.2 | Odeslat bez povinných údajů | Prohlížeč nepustí (povinná pole) | | |
| 2.3 | Vyplnit vše (přednáška 1, 30 žáků, 2 pedagogové, smyšlený e-mail `…@example.test`), telefon **bez** souhlasu s telefonem | Hláška, že telefon se uloží jen se souhlasem; vyplněné údaje zůstanou ve formuláři | | |
| 2.4 | Zaškrtnout souhlas s telefonem a odeslat | Stránka „Rezervace přednášky“ se souhrnem a hláškou „Rezervace je uložená…“; telefon je v souhrnu; výzva k registraci | | |
| 2.5 | smtp4dev | Odesílatel `rezervace@najdisi.test` (vývoj). E-mail pedagogovi: souhrn, odkaz „Správa rezervace“, uzávěrka, pozvánka k registraci, **bez telefonu**. Upozornění organizátorovi (při vývoji na `rezervace@najdisi.test`): souhrn **bez telefonu a bez odkazu** | | |
| 2.6 | Formulář znovu: přednáška 1, 9 žáků + 1 pedagog (zbývá 8 míst) | Rezervace se neuloží: „Na přednášce zbývá jen 8 míst, vaše skupina má 10 osob… vyberte prosím jinou.“ Údaje zůstanou vyplněné | | |
| 2.7 | Vybrat přednášku 2 a odeslat | Uloží se; ve formuláři u přednášky 2 ubyla volná místa | | |
| 2.8 | Z téhož e-mailu vytvořit další rezervace, až jich je 6 | Šestá se neuloží: „Z jednoho e-mailu lze mít nejvýš 5 rezervací…“ | | |
| 2.9 | Obsadit přednášku úplně (aby volno = 0) | Ve formuláři je přednáška „obsazeno“ a nejde vybrat | | |

## 3. Správa rezervace odkazem z e-mailu

| #   | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
| --- | --------- | ------------------ | -------- | -------- |
| 3.1 | Otevřít odkaz z e-mailu (2.5) | Souhrn, formulář „Změnit rezervaci“ a „Zrušit rezervaci“; samotné otevření nic nezmění | | |
| 3.2 | Zvětšit skupinu nad volnou kapacitu | „Na přednášce zbývá jen …“; nic se nezmění | | |
| 3.3 | Přesunout na jinou přednášku s volnými místy, změnit třídu | „Rezervace je přesunutá…“; e-mail pedagogovi o změně (s odkazem) a organizátorovi | | |
| 3.4 | Vymazat telefon a uložit | Telefon „nezadán“ | | |
| 3.5 | Odkaz s pozměněným koncem (jiný znak) | „Odkaz pro správu rezervace neplatí…“ | | |
| 3.6 | Zrušit rezervaci (potvrdit dialog) | „Rezervace je zrušená“; e-maily o zrušení; místa se ve formuláři uvolnila | | |
| 3.7 | Ve správě nabídky nastavit uzávěrku změn na včerejšek, otevřít odkaz jiné rezervace | Jen souhrn a „Uzávěrka změn uplynula… kontaktujte organizátora“; formuláře nejsou. Uzávěrku vrátit | | |

## 4. Administrace (účet s rolí `events_administrator`, také na telefonu)

| #   | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
| --- | --------- | ------------------ | -------- | -------- |
| 4.1 | Otevřít nabídku | U každé přednášky rezervováno / kapacita / volno, pruh obsazenosti, školy s oborem, třídou, počty, jménem, e-mailem a tlačítkem **zavolat** (jen u rezervací s telefonem) | | |
| 4.2 | Na telefonu klepnout na „zavolat“ | Otevře se vytáčení s číslem | | |
| 4.3 | „Upravit“ rezervaci, zvýšit počet žáků nad kapacitu, uložit s e-mailem | Uloží se s upozorněním „Kapacita přednášky je překročena…“; u přednášky „Kapacita překročena“; pedagogovi e-mail „Organizátor upravil vaši rezervaci“ | | |
| 4.4 | Přesunout rezervaci na jinou přednášku | Rezervace je u nové přednášky | | |
| 4.5 | Zrušit rezervaci správcem s e-mailem | Řádek přeškrtnutý jako zrušený; pedagogovi e-mail „Organizátor zrušil vaši rezervaci“ | | |
| 4.6 | Export CSV | Soubor se otevře v Excelu / LibreOffice s diakritikou; jen aktivní rezervace; **žádný telefon** | | |
| 4.7 | Odškrtnout přednášku s aktivními rezervacemi a uložit výběr | Přednáška v nabídce zůstane s upozorněním | | |
| 4.8 | Ve správě eventů zkusit smazat event s rezervacemi | „Event má rezervace škol, nelze ho smazat…“ | | |

## 5. Telefony a osobní údaje

| #   | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
| --- | --------- | ------------------ | -------- | -------- |
| 5.1 | „Smazat telefony“ | „Smazáno telefonů: N“; u rezervací „telefon smazán“ | | |
| 5.2 | „Anonymizovat po akci“ před dnem akce | „Anonymizovat lze až po skončení dne akce.“ | | |
| 5.3 | Automatické mazání: v DB (dotaz 5.3) posunout den akce i přednášky do minulosti, pak otevřít formulář nebo administraci | Všechny telefony rezervací přednášek z minulého dne jsou smazané (`phone_deleted_at` vyplněné) | | |
| 5.4 | Anonymizovat (po 5.3) | E-maily, jména a poznámky prázdné, škola, obor, třída a počty zůstaly; odkaz pro správu: „Rezervace už není dostupná…“ | | |
| 5.5 | Dotaz 5.5 | Sloupec `manage_token_hash` má 64 znaků, čitelný token nikde v tabulce není | | |

```sql
-- 5.3 (DB events) – jen na testovacích datech! Přednášky a nabídka o rok zpět
UPDATE event SET start = start - INTERVAL 1 YEAR, end = end - INTERVAL 1 YEAR WHERE id IN (SELECT event_id_fk FROM reservation_offer_event WHERE offer_id_fk = <id nabídky>);
UPDATE reservation_offer SET event_date = event_date - INTERVAL 1 YEAR WHERE id = <id nabídky>;

-- 5.5
SELECT id, status, contact_email, contact_phone, phone_deleted_at, LENGTH(manage_token_hash) AS delka_hashe, anonymized_at
FROM school_reservation ORDER BY id DESC LIMIT 20;
```

## 6. Registrace pedagoga a profil

| #   | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
| --- | --------- | ------------------ | -------- | -------- |
| 6.1 | Zaregistrovat se (A1) se stejným e-mailem jako rezervace a potvrdit registraci | – | | |
| 6.2 | Přihlášený otevřít profil návštěvníka | Blok „Rezervace školy“ s rezervacemi z toho e-mailu (i zrušené, zašedlé); telefon se nezobrazuje | | |
| 6.3 | Přihlášený otevřít formulář rezervací | Škola, obec, obor, třída, jméno a e-mail předvyplněné; **telefon ne** | | |

## 7. Vypnutý přepínač (etapa 6)

| #   | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
| --- | --------- | ------------------ | -------- | -------- |
| 7.1 | `vp.rezervace` → `false`, otevřít formulář a `events/admin/reservation` | „Rezervace nejsou zapnuté.“ | | |
| 7.2 | Adresy `events/v1/reservation` a odkaz pro správu z e-mailu | 404 | | |
| 7.3 | Správa eventů | Pole Kapacita ani „kapacita: …“ se nezobrazují; uložení eventu kapacitu nezmění (v DB zůstane) | | |
| 7.4 | Profil návštěvníka | Bez bloku „Rezervace školy“ | | |

## Po testu

- Přepínače vraťte na `false` (soubor necommitujte). Testovací rezervace a nabídku smaže vývojář
  (`DELETE FROM school_reservation WHERE contact_email LIKE '%@example.test'`, pak nabídku).
- Vyplněný protokol a nalezené chyby: viz [README.md](README.md#jak-hlásit-výsledky-návrh-čeká-na-schválení) (issue R1 #48).
