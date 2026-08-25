-- =============================================================================
-- Grafia red DB → NajdiSi schema (data-preserving)
-- Source dump: _db_dumps/gr_upgrade_structure.sql
-- Target:      _db_dumps/najdisi_structure.sql
--
-- Main gaps vs NajdiSi:
--   * menu_item PK is still `id` (should be lang_code_fk+uid_fk, UNIQUE id)
--   * menu_item.uid_fk is nullable; missing hierarchy/language FKs
--   * static column nullability (cosmetic)
--   * menu_root FK missing ON DELETE CASCADE
-- Leftovers kept: list_uid, stranky_innodb.flag_new
-- =============================================================================

-- -----------------------------------------------------------------------------
-- PRECHECK (must return 0 rows before continuing)
-- -----------------------------------------------------------------------------
-- SELECT id, lang_code_fk, uid_fk FROM menu_item WHERE uid_fk IS NULL OR uid_fk = '';
-- SELECT lang_code_fk, uid_fk, COUNT(*) c FROM menu_item GROUP BY lang_code_fk, uid_fk HAVING c > 1;
-- SELECT mi.id FROM menu_item mi LEFT JOIN hierarchy h ON h.uid = mi.uid_fk WHERE h.uid IS NULL;
-- SELECT mi.lang_code_fk FROM menu_item mi LEFT JOIN language l ON l.lang_code = mi.lang_code_fk WHERE l.lang_code IS NULL;

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- 1) menu_item: uid_fk NOT NULL + primary key like NajdiSi (see 2025/10_alter_menu_item_PK.sql)
-- -----------------------------------------------------------------------------
ALTER TABLE `menu_item`
  MODIFY COLUMN `uid_fk` varchar(45) NOT NULL;

-- Drop FKs that might block PK change (grafia dump has only menu_item_api_fk)
-- Safe if constraint missing: comment out if already absent.
ALTER TABLE `menu_item` DROP FOREIGN KEY `menu_item_api_fk`;

ALTER TABLE `menu_item`
  ADD INDEX `id_pk_tmp` (`id`);

ALTER TABLE `menu_item`
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`lang_code_fk`, `uid_fk`);

ALTER TABLE `menu_item`
  DROP INDEX `id_pk_tmp`,
  ADD UNIQUE INDEX `id` (`id`);

-- Restore FKs to match NajdiSi (index menu_item_api_fk usually remains after DROP FK)
ALTER TABLE `menu_item`
  ADD INDEX `hierarchy_uid_fk` (`uid_fk`);

ALTER TABLE `menu_item`
  ADD CONSTRAINT `hierarchy_uid_fk`
    FOREIGN KEY (`uid_fk`) REFERENCES `hierarchy` (`uid`) ON UPDATE CASCADE,
  ADD CONSTRAINT `language_lang_code_fk`
    FOREIGN KEY (`lang_code_fk`) REFERENCES `language` (`lang_code`),
  ADD CONSTRAINT `menu_item_api_fk`
    FOREIGN KEY (`api_module_fk`, `api_generator_fk`)
    REFERENCES `menu_item_api` (`module`, `generator`)
    ON UPDATE CASCADE;

-- -----------------------------------------------------------------------------
-- 2) static: nullable defaults like NajdiSi (no data loss)
-- -----------------------------------------------------------------------------
ALTER TABLE `static`
  MODIFY COLUMN `path` varchar(250) DEFAULT NULL,
  MODIFY COLUMN `template` varchar(150) DEFAULT NULL,
  MODIFY COLUMN `creator` varchar(100) DEFAULT NULL;

-- -----------------------------------------------------------------------------
-- 3) menu_root: ON DELETE CASCADE like NajdiSi
-- -----------------------------------------------------------------------------
ALTER TABLE `menu_root` DROP FOREIGN KEY `nested_set_uid_fk2`;
ALTER TABLE `menu_root`
  ADD CONSTRAINT `nested_set_uid_fk2`
    FOREIGN KEY (`uid_fk`) REFERENCES `hierarchy` (`uid`) ON DELETE CASCADE;

-- Optional (2026/16): normalize root names if still underscore form
-- UPDATE `menu_root` SET `name`='menu vertical' WHERE `name`='menu_vertical';
-- UPDATE `menu_root` SET `name`='menu horizontal' WHERE `name`='menu_horizontal';
-- UPDATE `menu_root` SET `name`='menu redirect' WHERE `name`='menu_redirect';

SET FOREIGN_KEY_CHECKS = 1;

-- Done. Re-dump structure and diff against najdisi_structure.sql.
