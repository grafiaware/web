/*
 * Po baseline 2025 tabulka static už existuje a obsahuje path i template
 * (viz docker/migrations/baseline/2025/red.sql).
 *
 * Původní podoba tohoto skriptu (DROP TABLE + CREATE) pochází z
 * SqlSources/red/AlterRedDatabase a běžela jen při prvním vytvoření tabulky
 * nad starou databází. Po importu baseline by stejný DROP smazal path.
 *
 * Schéma z baseline se nemění. Příkaz níže jen ověří, že tabulka a sloupec path jsou.
 */

UPDATE `static` SET `path` = `path` WHERE 0;
