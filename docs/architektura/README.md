# Architektura – záznamy rozhodnutí (ADR)

Rozhodnutí o architektuře a návrhových vzorech celého projektu web. Diskuse se vede odděleně od balíků VP
(pravidla v [`AGENTS.md`](../../AGENTS.md), sekce *Architektura a návrhové vzory*).

- Jeden soubor = jedno rozhodnutí: `NNN-<slug>.md` (kontext, možnosti, rozhodnutí, důsledky, stav).
- Issues se štítkem `architektura`, větve `arch/<kód>-<popis>`, commity s kódem `ARCH:`.
- Stav záznamu: *navrženo* → *přijato dočasně* (smí se používat, rozhodnutí o sjednocení čeká) → *přijato* / *zamítnuto*
  (pak záznam uvádí, jak a kdy se kód převede).

| ADR | Téma | Stav |
|-----|------|------|
| [001](001-model-dao-sluzby-bez-entit.md) | Model: Dao + služby s poli místo Entity/Hydrator/Repository | přijato dočasně (16. 11. 2026) |
| [002](002-stranky-pages-bez-komponent.md) | View: šablony `Pages/` místo komponent (View, ViewModel, Renderer) | přijato dočasně (16. 11. 2026) |
