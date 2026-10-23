# Veletrh práce – vývoj webu

Rozšíření webu pro akci Veletrh práce podle návrhů *Rozšíření služeb veletrhu* a *Zvýšení registrací návštěvníků* (říjen 2026).
Produkčně běží jako site **NajdiSi**.

| Dokument | Obsah |
|---|---|
| [ROADMAP.md](ROADMAP.md) | Větve roadmapy, pracovní balíky, závislosti, vlny |
| [SETUP.md](SETUP.md) | Lokální prostředí pro vývoj |
| [MIGRACE.md](MIGRACE.md) | Pravidla pro migrace DB v balících VP |
| [zadani/](zadani/) | Zadání jednotlivých balíků (`<kód>.md`), šablona `_SABLONA.md` |
| [../../AGENTS.md](../../AGENTS.md) | Pravidla pro lidi i agenty |

## Jak se pracuje

```
master  ← produkce (deploy site NajdiSi)
  └─ master-vp  ← integrace veletrhu, přes pull requesty (přímý push jen na pokyn Petra)
       ├─ vp/z8-prepinace      (řešitel A)
       ├─ vp/d1-losovani       (řešitel B)
       └─ vp/g2-hlasovani      (agent)
hotfix/<kód>  ← naléhavá oprava z master, sloučí se do master i master-vp
```

1. **Výběr:** balík s GitHub issue, přiřadit se jako řešitel. Zkontrolovat, že závislosti jsou v `master-vp`.
2. **Zadání:** `docs/vp/zadani/<kód>.md` podle šablony, schválí Petr (komentářem v issue nebo v PR se zadáním).
3. **Větev:** `vp/<kód>-<popis>` z aktuálního `master-vp`.
4. **Implementace:** kód, migrace, testy; nová funkce za přepínačem, výchozí stav vypnuto.
5. **Pull request do `master-vp`:** šablona PR, rebase na aktuální `master-vp`, zelené testy.
6. **Revize:** jiný člověk nebo agent než autor; lokální ověření podle postupu v PR.
7. **Sloučení:** člověk, squash merge; větev se maže; issue se zavře.
8. **Vydání:** `master-vp` → `master`, migrace na hostingu přes `/build`, deploy NajdiSi, zapnutí přepínače.

### Štítky a milníky na GitHubu

- `vp`: vše k projektu Veletrh práce.
- `vp:Z`, `vp:V1` … `vp:V16`: větev roadmapy (technický základ, registrace, losování …).
- Milníky `VP úpravy` (jedna vlna, pořadí podle závislostí) a `VP později` (odložené balíky).

### Předávání práce

Rozpracovaný balík lze předat jinému vývojáři nebo agentovi: aktualizovat sekci **Stav a další krok** v zadání,
pushnout větev, napsat komentář do issue a změnit řešitele. Přebírající začíná zadáním a diskusí v PR.
