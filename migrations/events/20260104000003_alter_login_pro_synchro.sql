/**
 * Author:  vlse2610
 * Created: 13. 5. 2026
 *
 * Tabulka login a cizí klíče na ni: login_name varchar(100), sloupce created, updated, deleted_due_to_auth
 * (potřebuje Events LoginDao / Synchro), cizí klíče ON UPDATE CASCADE (přejmenování loginu při synchronizaci).
 *
 * Upraveno 1. 11. 2026 (Z4, #39): pořadí kroků kvůli MariaDB. MariaDB nedovolí změnit sloupec login.login_name,
 * na který vede cizí klíč, ani při FOREIGN_KEY_CHECKS = 0 (chyba 1833). Proto nejdřív zrušit cizí klíče,
 * pak změnit login, pak dceřiné sloupce a cizí klíče znovu založit. Běží na MySQL i MariaDB.
 *
 * Upraveno 4. 11. 2026: DROP/ADD FK a ADD COLUMN přes information_schema + PREPARE (MySQL 5.6 nemá
 * DROP FOREIGN KEY IF EXISTS / ADD COLUMN IF NOT EXISTS). Bezpečné při chybějícím FK (retry po
 * částečném běhu, odchylka od baseline dump) i na MySQL 8 / MariaDB.
 */

-- 1) zrušit cizí klíče na login (jen pokud existují)
SET @fk_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'representative'
    AND CONSTRAINT_NAME = 'fk_representative_login1'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sqlstmt = IF(@fk_exists > 0,
  'ALTER TABLE `representative` DROP FOREIGN KEY `fk_representative_login1`',
  'SELECT 1');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @fk_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'enroll'
    AND CONSTRAINT_NAME = 'login_login_name_fk'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sqlstmt = IF(@fk_exists > 0,
  'ALTER TABLE `enroll` DROP FOREIGN KEY `login_login_name_fk`',
  'SELECT 1');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @fk_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'visitor_job_request'
    AND CONSTRAINT_NAME = 'fk_visitor_data_post_login1'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sqlstmt = IF(@fk_exists > 0,
  'ALTER TABLE `visitor_job_request` DROP FOREIGN KEY `fk_visitor_data_post_login1`',
  'SELECT 1');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @fk_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'visitor_profile'
    AND CONSTRAINT_NAME = 'fk_visitor_profile_login1'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sqlstmt = IF(@fk_exists > 0,
  'ALTER TABLE `visitor_profile` DROP FOREIGN KEY `fk_visitor_profile_login1`',
  'SELECT 1');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2) login: nové sloupce (jen chybějící) a delší login_name
SET @col_exists = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'login'
    AND COLUMN_NAME = 'created'
);
SET @sqlstmt = IF(@col_exists = 0,
  'ALTER TABLE `login` ADD COLUMN `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `login_name`',
  'SELECT 1');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'login'
    AND COLUMN_NAME = 'updated'
);
SET @sqlstmt = IF(@col_exists = 0,
  'ALTER TABLE `login` ADD COLUMN `updated` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created`',
  'SELECT 1');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'login'
    AND COLUMN_NAME = 'deleted_due_to_auth'
);
SET @sqlstmt = IF(@col_exists = 0,
  'ALTER TABLE `login` ADD COLUMN `deleted_due_to_auth` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `updated`',
  'SELECT 1');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

ALTER TABLE `login`
  CHANGE COLUMN `login_name` `login_name` VARCHAR(100) NOT NULL;

-- 3) dceřiné sloupce na varchar(100) a cizí klíče znovu s ON UPDATE CASCADE (ADD jen pokud chybí)
ALTER TABLE `representative`
  CHANGE COLUMN `login_login_name` `login_login_name` VARCHAR(100) NOT NULL;

SET @fk_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'representative'
    AND CONSTRAINT_NAME = 'fk_representative_login1'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sqlstmt = IF(@fk_exists = 0,
  'ALTER TABLE `representative` ADD CONSTRAINT `fk_representative_login1` FOREIGN KEY (`login_login_name`) REFERENCES `login` (`login_name`) ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

ALTER TABLE `enroll`
  CHANGE COLUMN `login_login_name_fk` `login_login_name_fk` VARCHAR(100) NOT NULL DEFAULT '';

SET @fk_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'enroll'
    AND CONSTRAINT_NAME = 'login_login_name_fk'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sqlstmt = IF(@fk_exists = 0,
  'ALTER TABLE `enroll` ADD CONSTRAINT `login_login_name_fk` FOREIGN KEY (`login_login_name_fk`) REFERENCES `login` (`login_name`) ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

ALTER TABLE `visitor_job_request`
  CHANGE COLUMN `login_login_name` `login_login_name` VARCHAR(100) NOT NULL;

SET @fk_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'visitor_job_request'
    AND CONSTRAINT_NAME = 'fk_visitor_data_post_login1'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sqlstmt = IF(@fk_exists = 0,
  'ALTER TABLE `visitor_job_request` ADD CONSTRAINT `fk_visitor_data_post_login1` FOREIGN KEY (`login_login_name`) REFERENCES `login` (`login_name`) ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

ALTER TABLE `visitor_profile`
  CHANGE COLUMN `login_login_name` `login_login_name` VARCHAR(100) NOT NULL;

SET @fk_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'visitor_profile'
    AND CONSTRAINT_NAME = 'fk_visitor_profile_login1'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sqlstmt = IF(@fk_exists = 0,
  'ALTER TABLE `visitor_profile` ADD CONSTRAINT `fk_visitor_profile_login1` FOREIGN KEY (`login_login_name`) REFERENCES `login` (`login_name`) ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
