# Auth migrace

**Rozhodnutí: verzovat od teď.** Historické skripty v `SqlSources/auth/` se sem nepřevádějí.

- Nové změny: `{YYYYMMDDHHMMSS}_{slug}.sql` v tomto adresáři.
- Na existující auth DB (schema „jak je“ u produkční verze) stačí Build → **Baseline 2025** (marker v žurnálu této DB instance).
- Žurnál je per DB instance — při více serverech / kopiích DB baseline/migrate na každé zvlášť.
