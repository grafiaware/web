# ADR 002: View – šablony `Pages/` v kontroléru místo komponent (View, ViewModel, Renderer)

- **Stav:** přijato dočasně (Petr, 16. 11. 2026) – smí se používat i v dalších modulech, protože je převoditelné
  na původní vzor. O sjednocení se rozhodne v diskusi o architektuře.
- **Týká se:** A1 (`Auth`, 1 stránka), D1/D1c (`Events`, stránky losování pro telefon).

## Původní vzor

Komponenty nad `src/Component` (`View`, `ViewModel`, `Renderer`):

- Komponenta (`Events\Component\View\…Component`, např. `CompanyFamilyCompanyContactListComponent`) se v
  `src/Container/*ContainerConfigurator.php` skládá z ViewModelu (`setListViewModel` / `setItemViewModel`),
  šablony z `ComponentConfiguration::getTemplate(…)` a kontejneru rendererů.
- Oprávnění řeší kontejner při skládání: `AccessPresentation::hasAnyPermission(Komponenta::class)`, jinak
  `NoPermittedContentRenderer`.
- ViewModel načítá data přes Repository (entity) a stav (`StatusViewModel`).
- Komponenta je pojmenovaná (`'companyFamilycompanycontactList' => …`) a vkládá se do obsahu stránky webu,
  tj. do layoutu site s menu.

## Odchylka

Kontrolér sám vyrenderuje PHP šablonu ze složky `Pages/` vedle sebe a vrátí hotové HTML:

- **A1:** `Auth\Middleware\Login\Controler\LoginControlerAbstract` → `Pages/action.php` (výsledek akce
  z odkazu v e-mailu: potvrzení, přihlašovací odkaz).
- **D1:** `Events\Middleware\Events\Controler\Lottery\LotteryControlerAbstract::page()` →
  `Pages/_layout.php` (vlastní jednoduchý layout bez menu webu) + šablona stránky
  (`scanner`, `round`, `mycode`, `scan-landing`, `admin-list`, `admin-lottery`, `winners`, `text`, `_status`).
  Data předává kontrolér jako pole (viz ADR 001), oprávnění kontroluje kontrolér (`isStaff()`, `isAdmin()`),
  pro odepření vrací `deniedPage()` se stavem 403.
- **Není odchylka:** blok losování v profilu návštěvníka (`local/site/najdisi/static/vp/vp-profil-navstevnika/content/losovani.php`)
  je statická šablona site jako ostatní obsah profilu.

## Proč (důvody v době vzniku)

1. **Samostatné stránky pro telefon bez layoutu webu.** Skener obsluhy, losování kola a „Můj kód“ jsou celoobrazovkové
   stránky pro telefon; menu, hlavička a skripty layoutu by zabíraly místo a zdržovaly načítání na slabém připojení.
2. **Offline.** „Můj kód“ se ukládá do service workeru (`/events/v1/mycode/sw`) a musí se otevřít bez internetu –
   stránka musí být malá a bez závislosti na dalších zdrojích layoutu (CSS, skripty, obrázky menu).
3. **Odpovědi akcí, ne obsah menu.** Stránky jsou výsledky rout (`/events/v1/lottery/…`), ne položky menu, do kterých
   se komponenty vkládají; nevěděl jsem o hotovém způsobu, jak komponentu vyrenderovat samostatně bez layoutu site.
4. **Rozsah a termín** (jako v ADR 001).

## Cena a postup převodu na původní vzor

Šablony `Pages/*.php` jsou obyčejné PHP šablony pro `PhpTemplateRenderer`, takže se dají použít jako šablony
komponent téměř beze změny. Převod:

1. Pro každou stránku ViewModel, který načte data (dnes je skládá kontrolér) – po ADR 001 nad Repository.
2. Komponenta + registrace v `EventsContainerConfigurator` s `AccessPresentation` místo `isStaff()` / `isAdmin()`
   (souvisí s užší rolí „obsluha losování“, #90).
3. Šablona přesunutá ze `Pages/` do šablon komponent (`ComponentConfiguration`).
4. Rozhodnout, jak vyrenderovat komponentu jako samostatnou stránku bez layoutu webu (viz otázky). Do té doby
   může kontrolér vyrenderovat komponentu místo šablony a obalit ji `_layout.php`.

Odhad: D1 cca 9 ViewModelů + 9 komponent + registrace; A1 1 stránka.

## Otázky k rozhodnutí

- Je v systému komponent podporovaná samostatná stránka bez layoutu site (pro telefon, offline)? Pokud ne, má se
  doplnit (např. „page“ komponenta s minimálním layoutem), nebo jsou pro tento druh stránek `Pages/` přípustné trvale?
- Kde mají být šablony takových stránek – u modulu (`src/Module/…/Pages`), nebo v `local/site/<site>/…` jako ostatní
  šablony, aby je mohla site přizpůsobit?
- Oprávnění: `AccessPresentation` na úrovni komponenty, nebo kontrola v kontroléru (dnes D1)? Sjednotit s #90.
