# ADR 001: Model – Dao + služby s poli místo Entity/Hydrator/Repository

- **Stav:** přijato dočasně (Petr, 16. 11. 2026) – smí se používat i v dalších modulech, protože je převoditelné
  na původní vzor. O sjednocení se rozhodne v diskusi o architektuře.
- **Týká se:** A1 (`Auth`, malý rozsah), D1/D1c (`Events`, losování).

## Původní vzor

`Model/{Dao, Hydrator, Entity, Repository}` nad `pes/pes-model`:

- `XxxDao` (`DaoEditAbstract`) vrací `RowData`.
- `XxxHydrator` (`TypeHydratorAbstract`) převádí `RowData` ↔ entita (`hydrate` / `extract`).
- `Xxx` + `XxxInterface` – entita se settery a gettery.
- `XxxRepo` (`RepoAbstract`) + `XxxRepoInterface` – `get`, `find…`, `add`, `remove`; drží kolekci entit
  (identity map) a změny zapisuje až ve `flush()`, volaném nejpozději v `__destruct()`.
- Registrace všech čtyř tříd v `src/Container/*ModelContainerConfigurator.php`.

Příklad: `Events\Model\Repository\CompanyContactRepo`, `Events\Model\Hydrator\CompanyContactHydrator`.

## Odchylka

Služba (`Service/…`) volá přímo Dao a pracuje s poli (`array` místo entity):

- **A1:** `Auth\Service\LoginTokenService` nad `Auth\Model\Dao\LoginTokenDao` (zadání A1 přitom uvádělo
  `LoginTokenDao/Repo` a entitu `LoginToken` – odchylka nebyla v zadání ani v PR uvedena).
- **D1:** 10 Dao v `Events\Model\Dao\Lottery\*` (`DaoEditAbstract` + `DaoAutoincrementTrait`) a společný
  `LotteryQueryTrait` s metodami `rows()`, `row()` (výsledek jako pole), `exec()`, `transaction()`, `lastId()`.
  Služby `Events\Service\Lottery\{LotteryService, LotteryDrawService, LotteryScanService}` skládají SQL
  (spojení tabulek, agregace, `FOR UPDATE`) a vrací pole řádků. Kontroléry a šablony pracují s poli.

## Proč (důvody v době vzniku)

1. **Okamžitý zápis v transakci se zámkem.** Losování (`drawOne`, `substitute`) musí v jedné transakci zamknout
   kolo (`SELECT … FOR UPDATE`), spočítat účastníky, zapsat výherce a hned znát jeho `id`. Repository odkládá zápis
   do `flush()` / destruktoru, takže zámek a zápis by se rozešly; dva telefony obsluhy by mohly vylosovat tutéž cenu.
2. **Dotazy přes více tabulek a agregace.** Účastníci kola (přítomnost × počet losů, bez dosavadních výherců),
   stav návštěvníka, přehledy výher – výsledkem nejsou entity jedné tabulky. V původním vzoru by to byly zvláštní
   metody Dao stejně vracející řádky, nebo by se agregace počítala v PHP nad načtenými entitami.
3. **Rozsah a termín.** 10 tabulek × (Entity + Interface + Hydrator + Repo + Interface + registrace) ≈ 50 souborů
   bez nové funkce; termín veletrhu 10. 1. 2027.

## Cena a postup převodu na původní vzor

Převod je mechanický, chování se nemění:

1. Pro tabulky s jednoduchým CRUD (`lottery`, `lottery_round`, `lottery_prize`, `lottery_question`, `visitor_code`,
   `lottery_paper_ticket`, `login_token`) doplnit Entity, Hydrator, Repo a registraci; služby přepnout z polí na entity
   (čtení `$row['name']` → `$entity->getName()`).
2. Dotazy přes více tabulek a agregace ponechat jako pojmenované metody Dao (nebo „read“ Dao), které vrací řádky –
   to je i v původním vzoru přípustné (`RepoReadonlyInterface` / vlastní finder).
3. Zápisy, které musí proběhnout hned v transakci se zámkem (losování, vydání losu), buď ponechat přímo přes Dao,
   nebo zavést v Repo explicitní `flush()` uvnitř transakce – to je otázka k rozhodnutí (viz níže).
4. Kontroléry a šablony přepnout z polí na entity / ViewModel (souvisí s ADR 002).
5. Unit a integrační testy D1 zůstávají a ověří, že se chování nezměnilo.

Odhad: D1 + D1c cca 50–60 nových souborů a úprava 3 služeb, 3 kontrolérů a šablon; A1 jednotky souborů.

## Otázky k rozhodnutí

- Je pro dotazy přes více tabulek a agregace přípustné „čtecí“ Dao vracející pole (bez entit)? Pokud ano,
  sjednotit jeho podobu (jeden trait / základní třída místo `LotteryQueryTrait` jen pro losování).
- Jak v původním vzoru dělat zápis, který musí proběhnout hned v transakci se zámkem (explicitní `flush()`,
  transakce na úrovni Repo, nebo přímo Dao)?
- Má být obecná část `LotteryQueryTrait` (pole z `RowData`, transakce) v `pes/pes-database` / `pes/pes-model`?
