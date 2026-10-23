# CLAUDE.md

Pravidla projektu jsou společná pro lidi i všechny agenty a jsou v AGENTS.md:

@AGENTS.md

## Doplněk pro Claude

- Komunikuj česky.
- Na začátku každé session: `git fetch`, přečti otevřená issues a pull requesty (REST API: `gh api repos/grafiaware/web/...`),
  zjisti, na kterém balíku se pracuje, a přečti jeho zadání v `docs/vp/zadani/`.
- Bez schváleného zadání neměň schéma DB. Do `master` nepushuj; do `master-vp` pushuj přímo jen na výslovný pokyn Petra, jinak přes pull request.
- Po každém kroku aktualizuj stav balíku v issue (komentář) a v zadání (sekce Stav a další krok).
