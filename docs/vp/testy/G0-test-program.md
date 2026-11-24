# Test G0: program z databáze, přihlášení na akci, administrace programu

- **Balík:** G0 (#116); zadání [`docs/vp/zadani/G0.md`](../zadani/G0.md); oprava mazání používaných číselníků #125
- **Prostředí:** Docker podle [README.md](README.md#prostředí), `master-vp`, site NajdiSi
- **Etapy** ([README.md](README.md#etapy)): bod 0.1 v etapě 0 (oprávnění platí vždy), body 0.2–0.4 a části 1–7 v etapě 4,
  část 8 v etapě 6
- **Migrace (`/build`, etapa 0):** events `20261117000001_g0_reset_event_data` – **smaže** `enroll`, `event`, `event_content`
  a vynuluje čítače (stará data programu se nepoužijí). Před spuštěním zálohovat.
- **Účty** (role, viz [README](README.md#etapy)): `events_administrator` (správce programu), `visitor` (dva návštěvníci –
  testovací účet a např. účet z A1), `representative` (vystavovatel) – testovací účty vznikají v etapě 2 (A3-A9 část 1)

## Záznam testu

| Údaj | Hodnota |
|---|---|
| Tester | |
| Datum | |
| Commit (`git log -1 --format=%h`) | |
| Prohlížeč | |

Výsledek u každého bodu: **OK** / **CHYBA** / **NEPROVEDENO** / **NEJASNÉ**. Hlášení výsledků viz [README.md](README.md).

## Příprava

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| P1 | Po migraci G0: `SELECT (SELECT COUNT(*) FROM enroll), (SELECT COUNT(*) FROM event), (SELECT COUNT(*) FROM event_content);` v DB events | `0 0 0` | | |
| P2 | V redakčním systému (editační režim) založit položky menu typu statická stránka se šablonami: správa `vp/event-content-type`, `vp/institution-type`, `vp/institution`, `vp/event-link-phase`, `vp/event-link`, `vp/event-content`, `vp/event` (do menu administrace) a program `vp/program`, `vp/program-prednasky`, `vp/program-poradny` (do veřejného menu). Pokud statické stránky přes menu ještě nefungují (oprava Petr), části 1–7 označte NEPROVEDENO | Položky jsou v menu a stránky se otevřou | | |
| P3 | Etapa 4: zapnout `'vp.program' => true` (ostatní přepínače z etap 1–3 zůstávají zapnuté) | | | |
| P4 | Pro bod 6: účet s rolí `representative` přiřadit jako zástupce k jedné firmě (events admin → zástupci firem) a zapsat si přesný název firmy | Zástupce vidí profil vystavovatele | | |

**Hlášky** se zobrazují v pruhu hlášek stránky (po odeslání formuláře a návratu na stránku).

**Odeslání požadavku z konzole prohlížeče** (body 0.x a 5.x): na kterékoli stránce webu otevřete nástroje vývojáře
(F12) → Konzole a vložte příkaz; pak stránku obnovte, aby se ukázala hláška.

## 0. Oprávnění ve správě programu (etapa 0 – platí vždy)

Kontroly oprávnění platí bez ohledu na přepínače. Dřív (do G0) mohl i nepřihlášený měnit a mazat instituce,
obsahy a odkazy. **V etapě 0 jen bod 0.1** (testovací účty ještě nejsou); body 0.2–0.4 na začátku etapy 4.

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 0.1 | Nepřihlášen: v konzoli `fetch('events/v1/institutiontype', {method: 'POST', body: new URLSearchParams({institutionType: 'TEST-anonym'})})` | Po obnovení hláška „Nemáte oprávnění.“; `SELECT * FROM institution_type WHERE institution_type LIKE 'TEST-%';` nic nevrátí | | |
| 0.2 | Totéž přihlášen s rolí `visitor` (s `institutionType: 'TEST-navstevnik'`) | „Nemáte oprávnění.“, nic nevznikne | | |
| 0.3 | S rolí `visitor` otevřít stránku správy, např. `vp/institution` (je-li položka menu) | „Stránka je určena pouze pro administraci programu.“, žádné formuláře | | |
| 0.4 | S rolí `events_administrator` stejný příkaz jako 0.1 s `institutionType: 'TEST-spravce'` | Typ vznikne (správce smí); smažte ho ve správě typů institucí (bod 1.5) | | |

## 1. Číselníky (etapa 4, správce)

Přihlášen účtem s rolí `events_administrator`.

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 1.1 | `vp/event-content-type`: přidat kód `prednaska` / název `Přednáška`, `poradna` / `Poradna`, `prezentace` / `Firemní prezentace` | Typy jsou v seznamu, lze je upravit | | |
| 1.2 | `vp/institution-type`: přidat `Úřad práce`, `Firma` | V seznamu | | |
| 1.3 | `vp/institution`: přidat `Úřad práce Plzeň` (typ Úřad práce) a instituci se **stejným názvem jako firma z P4** (typ Firma) | V seznamu, seřazeno podle názvu | | |
| 1.4 | `vp/event-link-phase`: přidat `Vstoupit`; `vp/event-link`: odkaz `https://example.test/mistnost` s fází Vstoupit, zobrazit ano | V seznamu | | |
| 1.5 | Smazat typ instituce `TEST-spravce` (z 0.4) | Smazán (potvrzení prohlížeče → OK) | | |
| 1.6 | Smazat typ instituce `Úřad práce` (používá ho instituce) | Hláška „Typ instituce mají přiřazený instituce, nejde ho smazat.“; typ zůstane | | |
| 1.7 | Smazat fázi `Vstoupit` (používá ji odkaz) | „Fázi používají odkazy, nejde ji smazat.“; zůstane | | |
| 1.8 | Do názvu instituce zadat `<b>Test</b>` | Zobrazí se doslova jako text, ne tučně (všude ve správě i v programu) | | |

## 2. Obsahy (etapa 4, správce)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 2.1 | `vp/event-content`: obsah typu Přednáška „Jak napsat životopis“, popis s odřádkováním a textem `<i>kurzíva</i>`, vystupující „Jana Nováková“, instituce Úřad práce Plzeň | Uložen | | |
| 2.2 | Obsah typu Poradna „Kariérní poradna“, vystupující „Petr Svoboda“, bez instituce | Uložen | | |
| 2.3 | Obsah typu Firemní prezentace „Představení firmy“, instituce = firma z P4 | Uložen | | |
| 2.4 | Smazat typ obsahu `prednaska` (používá ho obsah) | „Typ obsahu používají obsahy eventů, nejde ho smazat.“ | | |

## 3. Eventy – rozpis programu (etapa 4, správce)

`vp/event`. Datum se vybírá v kalendáři (zavře se kliknutím na den), čas se píše (`10:30`, `10.30`, `1030`, `10`).
D = zítřejší datum.

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 3.1 | Event: D, od `9.30` do `1015`, obsah Přednáška…, odkaz pro vstup = odkaz z 1.4, zveřejnit | Uložen jako 9:30–10:15; nový formulář předvyplní datum D | | |
| 3.2 | Event: D, 10:30–12, Kariérní poradna, zveřejnit | Uložen 10:30–12:00 | | |
| 3.3 | Event: D, 13:00–13:45, Představení firmy, zveřejnit | Uložen | | |
| 3.4 | Event: D, 14:00–14:30, Kariérní poradna, **nezveřejnit** | Uložen, nezveřejněný | | |
| 3.5 | Event: včerejší datum, 10:00–11:00, Jak napsat životopis, zveřejnit | Uložen (proběhlý) | | |
| 3.6 | Event s koncem před začátkem (11:00–10:00) | „Zadejte datum a čas začátku i konce (konec po začátku), např. 10:30.“; neuloží se | | |
| 3.7 | Seznam eventů | Seřazen podle začátku; u každého „přihlášeno: 0“ | | |

## 4. Program na webu (etapa 4)

Nepřihlášen.

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 4.1 | `vp/program` | Den „<den v týdnu> D“; eventy 3.1, 3.2, 3.3 v pořadí podle času s typem, ikonou, názvem, popisem (`<i>` jako text, odřádkování zachované), vystupujícím a institucí; **bez** 3.4 (nezveřejněný) a 3.5 (proběhlý) | | |
| 4.2 | Text nad programem | „Po přihlášení ke svému účtu se můžete na akce přihlásit…“; tlačítko Přihlásit se nikde | | |
| 4.3 | U eventu 3.1 tlačítko „Vstoupit“ | Otevře `https://example.test/mistnost` v nové kartě | | |
| 4.4 | `vp/program-prednasky` | Jen přednáška 3.1 | | |
| 4.5 | `vp/program-poradny` | Jen poradna 3.2, tabulkové zobrazení (Název poradny / Poradí vám) | | |
| 4.6 | Správce zruší zveřejnění 3.1, obnovit program | 3.1 zmizí; pak znovu zveřejnit | | |

## 5. Přihlášení na akci a Můj harmonogram (etapa 4)

Přihlášen účtem s rolí `visitor` (a v 5.6 druhý návštěvník).

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 5.1 | `vp/program`: u 3.1 „Přihlásit se“ | „Přihlášeno: Jak napsat životopis. Akci najdete ve svém profilu v harmonogramu.“; tlačítko teď „Odhlásit se“ | | |
| 5.2 | Přihlásit se i na 3.2 | Přihlášeno | | |
| 5.3 | Profil návštěvníka → „Můj harmonogram“ | Jen 3.1 a 3.2, s tlačítkem „Odhlásit se“ | | |
| 5.4 | V harmonogramu u 3.2 „Odhlásit se“ | „Odhlášeno: …“; 3.2 z harmonogramu zmizí | | |
| 5.5 | V DB `INSERT INTO enroll VALUES ('<přihlašovací jméno návštěvníka>', <id eventu 3.5>);` a obnovit profil | Harmonogram ukáže i proběhlý event 3.5 (včerejší den), bez tlačítek; na stránce programu 3.5 není | | |
| 5.6 | Druhý návštěvník se přihlásí na 3.1 | Přihlášen; ve správě eventů u 3.1 „přihlášeno: 2“ | | |
| 5.7 | Jako `t_visitor` v konzoli `fetch('events/v1/enroll', {method: 'POST', body: new URLSearchParams({event_enroll: '<id 3.5>'})})`, obnovit | „Akce už proběhla.“ | | |
| 5.8 | Totéž s id nezveřejněného 3.4 | „Akce nebyla nalezena.“ | | |
| 5.9 | Nepřihlášen: totéž s id 3.1 | „Pro přihlášení na akci se nejdřív přihlaste ke svému účtu.“; v `enroll` nic nového | | |

## 6. Harmonogram vystavovatele (etapa 4)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 6.1 | S rolí `representative` profil vystavovatele | Oddíl „Náš program“ s eventem 3.3 (instituce se jmenuje stejně jako firma) a „Přihlášeno: 0“; jiné eventy ne | | |
| 6.2 | Návštěvník se přihlásí na 3.3, vystavovatel obnoví profil | „Přihlášeno: 1“ | | |

## 7. Mazání (etapa 4, správce)

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 7.1 | Smazat event 3.1 (má přihlášené) | „Event má přihlášené návštěvníky, nelze ho smazat. Zrušte zveřejnění.“ | | |
| 7.2 | Smazat event 3.4 (bez přihlášených) | Smazán | | |
| 7.3 | Smazat obsah „Kariérní poradna“ (používá ho 3.2) | Potvrzení „Eventy s tímto obsahem zůstanou bez obsahu a zmizí z programu.“ → OK; 3.2 zmizí z programu i harmonogramu, ve správě eventů zůstane „(bez obsahu)“ | | |
| 7.4 | Smazat odkaz z 1.4 | Event 3.1 zůstane bez tlačítka „Vstoupit“ | | |

## 8. Vypnutý přepínač (etapa 6)

`'vp.program' => false`.

| # | Co udělat | Očekávaný výsledek | Výsledek | Poznámka |
|---|---|---|---|---|
| 8.1 | `vp/program`, `vp/program-prednasky`, `vp/program-poradny` | „Program zatím není zveřejněn.“ | | |
| 8.2 | Profil návštěvníka a vystavovatele | Bez „Můj harmonogram“ a „Náš program“ | | |
| 8.3 | `fetch('events/v1/enroll', {method: 'POST', body: new URLSearchParams({event_enroll: '1'})}).then(r => console.log(r.status))` | `404` | | |
| 8.4 | Správa (`vp/event` aj.) jako správce | Funguje (správa přepínač nemá) | | |
| 8.5 | Body 0.1 a 0.2 | Stále „Nemáte oprávnění.“ | | |

## Po testu

Testovací data programu zůstanou pro etapu 5 (nevadí); po celém testu obnovte zálohu DB.
