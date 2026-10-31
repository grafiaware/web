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
 */

-- 1) zrušit cizí klíče na login
ALTER TABLE `representative` DROP FOREIGN KEY `fk_representative_login1`;
ALTER TABLE `enroll` DROP FOREIGN KEY `login_login_name_fk`;
ALTER TABLE `visitor_job_request` DROP FOREIGN KEY `fk_visitor_data_post_login1`;
ALTER TABLE `visitor_profile` DROP FOREIGN KEY `fk_visitor_profile_login1`;

-- 2) login: nové sloupce a delší login_name
ALTER TABLE `login`
  ADD COLUMN `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `login_name`,
  ADD COLUMN `updated` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created`,
  ADD COLUMN `deleted_due_to_auth` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `updated`,
  CHANGE COLUMN `login_name` `login_name` VARCHAR(100) NOT NULL;

-- 3) dceřiné sloupce na varchar(100) a cizí klíče znovu s ON UPDATE CASCADE
ALTER TABLE `representative`
  CHANGE COLUMN `login_login_name` `login_login_name` VARCHAR(100) NOT NULL,
  ADD CONSTRAINT `fk_representative_login1`
    FOREIGN KEY (`login_login_name`) REFERENCES `login` (`login_name`) ON UPDATE CASCADE;

ALTER TABLE `enroll`
  CHANGE COLUMN `login_login_name_fk` `login_login_name_fk` VARCHAR(100) NOT NULL DEFAULT '',
  ADD CONSTRAINT `login_login_name_fk`
    FOREIGN KEY (`login_login_name_fk`) REFERENCES `login` (`login_name`) ON UPDATE CASCADE;

ALTER TABLE `visitor_job_request`
  CHANGE COLUMN `login_login_name` `login_login_name` VARCHAR(100) NOT NULL,
  ADD CONSTRAINT `fk_visitor_data_post_login1`
    FOREIGN KEY (`login_login_name`) REFERENCES `login` (`login_name`) ON UPDATE CASCADE;

ALTER TABLE `visitor_profile`
  CHANGE COLUMN `login_login_name` `login_login_name` VARCHAR(100) NOT NULL,
  ADD CONSTRAINT `fk_visitor_profile_login1`
    FOREIGN KEY (`login_login_name`) REFERENCES `login` (`login_name`) ON UPDATE CASCADE;
