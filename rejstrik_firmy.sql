/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  pes2704
 * Created: 6. 10. 2026
 */

/**
* Pro uložení kompletních dat z Veřejného rejstříku (všechny rejstříkové entity z dataor.justice.cz – Obchodní rejstřík, Spolkový rejstřík, Nadační rejstřík atd.) je potřeba navrhnout normalizované relokční schéma.
* Data v XML z Justice.cz mají hlubokou stromovou strukturu (jeden subjekt má více statutárních orgánů, společníků, oborů činností, provozoven, akcionářů i historických zápisů).
* Níže je připraven kompletní SQL skript pro MySQL / MariaDB navržený pro:
* Maximální rychlost vkládání (dávkové INSERT / ON DUPLICATE KEY UPDATE z PHP skriptu).
* Efektivní relační dotazování (pomocí cizích klíčů a indexů pro PDO).
* Přípravu pro Elasticsearch (sloupec updated_at a tabulka sync_queue pro inkrementální indexaci).
*/

-- Vytvoření databáze s plnou podporou české diakritiky
CREATE DATABASE IF NOT EXISTS `rejstrik_firmy`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `rejstrik_firmy`;

-- Vypnutí kontroly cizích klíčů během vytváření schématu
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- 1. Hlavní tabulka subjektů (Firma / Spolek / Družstvo)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `subjekty`;
CREATE TABLE `subjekty` (
    `ico` VARCHAR(8) NOT NULL,
    `spisova_znacka` VARCHAR(50) DEFAULT NULL,
    `obchodni_jmeno` VARCHAR(500) NOT NULL,
    `pravni_forma_kod` VARCHAR(10) DEFAULT NULL,
    `pravni_forma_nazev` VARCHAR(255) DEFAULT NULL,
    `datum_vzniku` DATE DEFAULT NULL,
    `datum_zaniku` DATE DEFAULT NULL,
    
    -- Sídlo
    `ulice` VARCHAR(255) DEFAULT NULL,
    `cislo_domovni` VARCHAR(20) DEFAULT NULL,
    `cislo_orientacni` VARCHAR(20) DEFAULT NULL,
    `obec` VARCHAR(255) DEFAULT NULL,
    `cast_obce` VARCHAR(255) DEFAULT NULL,
    `psc` VARCHAR(10) DEFAULT NULL,
    `stat_kod` VARCHAR(3) DEFAULT 'CZE',
    `textova_adresa` VARCHAR(500) DEFAULT NULL,

    -- Finanční a daňové údaje
    `dic` VARCHAR(20) DEFAULT NULL,
    `platce_dph` TINYINT(1) DEFAULT 0,
    `stav_subjektu` ENUM('AKTIVNI', 'V_LIKVIDACI', 'ZANIKLY', 'INSOLVENCE') DEFAULT 'AKTIVNI',

    -- Metadata pro synchronizaci s Elasticsearch / API
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`ico`),
    
    -- Indexy pro B-Tree vyhledávání přes PDO
    INDEX `idx_obchodni_jmeno` (`obchodni_jmeno`(100)),
    INDEX `idx_obec` (`obec`),
    INDEX `idx_psc` (`psc`),
    INDEX `idx_pravni_forma` (`pravni_forma_kod`),
    INDEX `idx_datum_vzniku` (`datum_vzniku`),
    INDEX `idx_updated_at` (`updated_at`) -- Klíčové pro CDC / Elasticsearch sync
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------
-- 2. Statutární orgány a angažované osoby (Osoby / Firmy)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `osoby_angazovane`;
CREATE TABLE `osoby_angazovane` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ico_subjektu` VARCHAR(8) NOT NULL,
    `typ_angazma` VARCHAR(100) NOT NULL, -- např. 'STATUTARNI_ORGAN', 'SPOLECNIK', 'PROKURA', 'DOZORCI_RADA'
    `funkce` VARCHAR(150) DEFAULT NULL,   -- např. 'jednatel', 'předseda představenstva'
    
    -- Údaje o fyzické nebo právnické osobě
    `typ_osoby` ENUM('FYZICKA', 'PRAVNICKA') NOT NULL DEFAULT 'FYZICKA',
    `jmeno` VARCHAR(100) DEFAULT NULL,
    `prijmeni` VARCHAR(100) DEFAULT NULL,
    `titul_pred` VARCHAR(20) DEFAULT NULL,
    `titul_za` VARCHAR(20) DEFAULT NULL,
    `datum_narozeni` DATE DEFAULT NULL,
    `osoba_ico` VARCHAR(8) DEFAULT NULL,   -- Pokud je členem statutáru jiná právnická osoba
    `nazev_firmy` VARCHAR(255) DEFAULT NULL,

    -- Bydliště / Sídlo osoby
    `adresa_text` VARCHAR(500) DEFAULT NULL,
    `obec` VARCHAR(100) DEFAULT NULL,
    
    -- Platnost funkce
    `vznik_funkce` DATE DEFAULT NULL,
    `zanik_funkce` DATE DEFAULT NULL,

    FOREIGN KEY (`ico_subjektu`) REFERENCES `subjekty` (`ico`) ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Indexy pro vyhledávání vazeb (Kdo kde figuruje)
    INDEX `idx_ico_subjektu` (`ico_subjektu`),
    INDEX `idx_osoba_jmeno` (`prijmeni`, `jmeno`),
    INDEX `idx_osoba_ico` (`osoba_ico`),
    INDEX `idx_datum_narozeni` (`datum_narozeni`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------
-- 3. Obory činnosti a předměty podnikání
-- --------------------------------------------------------
DROP TABLE IF EXISTS `predmety_podnikani`;
CREATE TABLE `predmety_podnikani` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ico_subjektu` VARCHAR(8) NOT NULL,
    `popis` TEXT NOT NULL,
    `druh_cinnosti` ENUM('PREDMET_PODNIKANI', 'PREDMET_CINNOSTI', 'UCELL') DEFAULT 'PREDMET_PODNIKANI',
    
    FOREIGN KEY (`ico_subjektu`) REFERENCES `subjekty` (`ico`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_ico_subjektu` (`ico_subjektu`),
    FULLTEXT INDEX `ft_popis` (`popis`) -- MySQL Fulltext pro rychlé hledání v oborech
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------
-- 4. Kapitulace a podíly společníků (Základní kapitál)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `kapital_a_podily`;
CREATE TABLE `kapital_a_podily` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ico_subjektu` VARCHAR(8) NOT NULL,
    `vyska_kapitalu` DECIMAL(15,2) DEFAULT NULL,
    `mena` VARCHAR(3) DEFAULT 'CZK',
    `splaceno_percent` DECIMAL(5,2) DEFAULT NULL,
    `text_podilu` TEXT DEFAULT NULL,
    
    FOREIGN KEY (`ico_subjektu`) REFERENCES `subjekty` (`ico`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_ico_subjektu` (`ico_subjektu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------
-- 5. Tabulka fronty pro synchronizaci s Elasticsearch (CDC Pattern)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `es_sync_queue`;
CREATE TABLE `es_sync_queue` (
    `ico` VARCHAR(8) NOT NULL,
    `action` ENUM('INDEX', 'DELETE') NOT NULL DEFAULT 'INDEX',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ico`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------
-- Trigger: Automatická registrace změny pro Elasticsearch
-- --------------------------------------------------------
DELIMITER //

CREATE TRIGGER `trg_subjekty_after_insert`
AFTER INSERT ON `subjekty`
FOR EACH ROW
BEGIN
    INSERT INTO `es_sync_queue` (`ico`, `action`) 
    VALUES (NEW.ico, 'INDEX')
    ON DUPLICATE KEY UPDATE `created_at` = CURRENT_TIMESTAMP;
END //

CREATE TRIGGER `trg_subjekty_after_update`
AFTER UPDATE ON `subjekty`
FOR EACH ROW
BEGIN
    INSERT INTO `es_sync_queue` (`ico`, `action`) 
    VALUES (NEW.ico, 'INDEX')
    ON DUPLICATE KEY UPDATE `created_at` = CURRENT_TIMESTAMP;
END //

DELIMITER ;

SET FOREIGN_KEY_CHECKS = 1;