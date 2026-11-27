# Plán automatických „klikacích“ (E2E) testů

Návrh postupu (zatím **bez implementace**). Vznikl po dokončení Docker vývojového prostředí
([DOCKER.md](../DOCKER.md), [DOCKER-WINDOWS.md](../DOCKER-WINDOWS.md)). Ruční scénáře zůstávají
v [README.md](README.md) a souborech `*-test-*.md`; E2E je má **postupně automatizovat**, ne nahradit najednou.

Související chat (kontext Docker + QR host):  
https://cursor.com/agents/bc-2c5d3d2d-692f-4277-8fdc-4556794aa2fb

## Co už neblokuje start

- Stack: Apache + PHP 8.4, MariaDB, smtp4dev, HTTPS, `WEB_PUBLIC_HOST` / `*.dev.local`.
- Kostra Playwright: `tests/e2e/` (kouřový test + příklad smtp4dev API), spuštění viz [DOCKER.md](../DOCKER.md).
- Ruční etapy a přepínače: [README.md](README.md).

Brzdí spíš **rozsah a stabilní fixture data** než chybějící infrastruktura.

## QR „naskenuj telefonem“ v automatech

| Co | Jak |
|---|---|
| **Reálný sken kamerou** | Zůstává **ruční** (HTTPS, oprávnění kamery, fyzický QR, Virtual Host app). |
| **Automat (doporučeno)** | QR nese URL (`…/events/v1/scan/…` nebo kód návštěvníka). Playwright **otevře stejnou adresu** v druhém kontextu (viewport / UA mobil). Simuluje requestovou část toku, ne kameru. |
| **Skener UI** (`getUserMedia` + jsQR) | Volitelně později (ruční zadání kódu / injekce výsledku). **Není nutné** pro první vlnu. |

## smtp4dev

- **Hlavní cesta:** HTTP API (`GET /api/Messages` atd.) — stabilní, už v `tests/e2e`.
- **UI smtp4dev:** maximálně kouřově; ne jako hlavní assert v regresích.

## Cílový rozsah (postupně)

Celý tok „od nuly“:

1. Import baseline DB (dumpy mimo repo — osobní údaje; viz [MIGRACE.md](../MIGRACE.md)).
2. Migrace přes `/build` (nebo později CLI ekvivalent).
3. Red CMS: menu, položky paper / article / multipage, nové menu, statické stránky administrace.
4. Auth: registrace, login, credentials, role; maily přes smtp4dev API.
5. Funkce za přepínači podle etap v [README.md](README.md) (A1–A2, A3–A9, G0, D1…).
6. Losování: zapojení → URL z QR → `page.goto(scanUrl)` jako „sken“.

**První vlna nemá dělat vše.** Celé zakládání Red menu + všech typů položek z UI je drahé a křehké —
nejdřív seed / tenké smoke, plné CMS až ve 2. vlně.

## Navrhované vrstvy Playwright

| Vrstva | Obsah | Poznámka |
|---|---|---|
| **A** | Kouř: web + smtp4dev API | Už skoro hotové |
| **B** | Auth: registrace → mail v API → confirm → login / odkaz | Mapovat na A1–A2 |
| **C** | Role a správa účtů (podmnožina) | Mapovat na A6/A9 |
| **D** | Losování bez kamery (QR URL → goto) | Mapovat na D1/D1c |
| **E** | CMS menu / paper / article / multipage / statické stránky | Až po stabilním seedu |

Odděleně od „klikání“: **fixture pipeline** (`docker compose exec` / skript): reset volume → baseline → migrace → seed účtů/rolí.

## Co zůstane ruční

- Fyzický telefon a sken QR.
- Instalace `ca.pem`, DNS / Virtual Host app.
- Reálná kamera skeneru obsluhy.
- Plné ruční protokoly z `*-test-*.md` do archivu `protokoly/` (dokud E2E bod nepokrývá).

## Organizační postup (ne implementace)

1. Založit GitHub issue + zadání balíku (kód např. Z14 / „e2e“) podle [zadani/_SABLONA.md](../zadani/_SABLONA.md): cíle, mimo scope, závislost na Docker a `WEB_PUBLIC_HOST`.
2. Schválit zadání (Petr), teprve potom větší kód.
3. Implementovat ve vlastním chatu / Cursor projektu „Testování e2e“ podle tohoto dokumentu — ne v chatu Docker.
4. Malé PR: nejdřív fixture + vrstva A/B, pak C/D, až nakonec E.
5. Po každém kroku aktualizovat sekci **Stav** níže a issue.

## Stav a další krok

- **Stav:** návrh uložen; implementace nezačala.
- **Další krok:** nové Cursor projekt / chat „Testování e2e“ → draft zadání + issue; nejdřív vrstvy A–B a fixture pipeline, ne celé Red CMS.
