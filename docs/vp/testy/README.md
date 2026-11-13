# Testovací scénáře pro testery

Ruční testy funkcí Veletrhu práce. Jeden soubor = jeden scénář k balíku (nebo několika souvisejícím balíkům),
název `<kódy balíků>-test-<co se testuje>.md`.

| Scénář | Balíky |
|---|---|
| [A1-A2-test-registrace-a-prihlaseni.md](A1-A2-test-registrace-a-prihlaseni.md) | A1, A2 (+ krátká kontrola Z1, Z3, Z4) |
| [A8-test-osobni-jmeno.md](A8-test-osobni-jmeno.md) | A8 |
| [A6-test-zakladani-uctu.md](A6-test-zakladani-uctu.md) | A6 |
| [A3-test-uklid-registraci.md](A3-test-uklid-registraci.md) | A3 |
| [A9-test-sprava-roli.md](A9-test-sprava-roli.md) | A9 |

## Jak hlásit výsledky (návrh, čeká na schválení)

1. Tester si scénář zkopíruje a vyplní sloupce **Výsledek** a **Poznámka** (lze i v prohlížeči na GitHubu
   přes tlačítko „Edit“ → zkopírovat obsah).
2. Vyplněný protokol vloží jako **komentář do issue balíku** (např. #44 pro A1). Tabulky v Markdownu GitHub zobrazí.
3. Každá **CHYBA** se navíc založí jako samostatné issue s názvem `<kód balíku> test: <krátký popis>`,
   s číslem bodu scénáře, postupem, očekávaným a skutečným výsledkem a snímkem obrazovky.
4. Vývojář chybu opraví v pull requestu, který issue uzavře; tester bod zopakuje a výsledek doplní do komentáře.
