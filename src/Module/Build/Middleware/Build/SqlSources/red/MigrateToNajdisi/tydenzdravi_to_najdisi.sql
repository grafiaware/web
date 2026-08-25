-- =============================================================================
-- Týden zdraví (tydenzdravieu) → NajdiSi schema (data-preserving)
-- Source dump: _db_dumps/tydenzdravieu_structure.sql
-- Target:      _db_dumps/najdisi_structure.sql
--
-- TZ is a pre-2025 generation. This script applies the AlterRedDatabase/2025+2026
-- path as one ordered, data-preserving migration (no DROP of content tables).
--
-- Existing TZ tables kept and upgraded: block, hierarchy, language, menu_adjlist,
-- menu_item, menu_item_type, menu_root, paper, paper_content→paper_section
-- =============================================================================

-- -----------------------------------------------------------------------------
-- PRECHECK
-- -----------------------------------------------------------------------------
-- SELECT id, lang_code_fk, uid_fk FROM menu_item WHERE uid_fk IS NULL OR uid_fk = '';
-- SELECT lang_code_fk, uid_fk, COUNT(*) c FROM menu_item GROUP BY lang_code_fk, uid_fk HAVING c > 1;
-- SELECT type_fk, COUNT(*) FROM menu_item GROUP BY type_fk;
-- SELECT COUNT(*) AS paper_content_rows FROM paper_content;

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================================================
-- A) paper_content → paper_section (2025/04 + 2025/07)
-- =============================================================================
ALTER TABLE `paper_content`
  CHANGE COLUMN `template` `template_name` varchar(100) NULL DEFAULT '',
  ADD COLUMN `template` longtext NULL DEFAULT NULL AFTER `template_name`,
  CHANGE COLUMN `event_time` `event_start_time` date NULL DEFAULT NULL,
  ADD COLUMN `event_end_time` date NULL DEFAULT NULL AFTER `event_start_time`;

ALTER TABLE `paper_content` RENAME TO `paper_section`;

-- =============================================================================
-- B) New tables (final NajdiSi shapes)
-- =============================================================================

CREATE TABLE IF NOT EXISTS `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_item_id_fk` int(11) unsigned NOT NULL,
  `article` longtext,
  `template` varchar(100) DEFAULT '',
  `editor` varchar(20) DEFAULT '',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `menu_item_id_fk3` (`menu_item_id_fk`),
  CONSTRAINT `menu_item_id_fk3` FOREIGN KEY (`menu_item_id_fk`) REFERENCES `menu_item` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `multipage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_item_id_fk` int(11) unsigned NOT NULL,
  `template` varchar(100) DEFAULT '',
  `editor` varchar(20) DEFAULT '',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `menu_item_id_fk4` (`menu_item_id_fk`),
  CONSTRAINT `menu_item_id_fk4` FOREIGN KEY (`menu_item_id_fk`) REFERENCES `menu_item` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `item_action` (
  `item_id` varchar(45) NOT NULL,
  `editor_login_name` varchar(45) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_id`, `editor_login_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `static` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_item_id_fk` int(11) unsigned NOT NULL,
  `path` varchar(250) DEFAULT NULL,
  `template` varchar(150) DEFAULT NULL,
  `creator` varchar(100) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `menu_item_id_fk2` (`menu_item_id_fk`),
  CONSTRAINT `menu_item_id_fk2` FOREIGN KEY (`menu_item_id_fk`) REFERENCES `menu_item` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `asset` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `filepath` varchar(255) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `editor_login_name` varchar(45) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `filepath_idx1` (`filepath`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `menu_item_asset` (
  `menu_item_id_fk` int(11) unsigned NOT NULL,
  `asset_id_fk` int(11) unsigned NOT NULL,
  PRIMARY KEY (`menu_item_id_fk`, `asset_id_fk`),
  KEY `asset_ibfk2_idx` (`asset_id_fk`),
  CONSTRAINT `asset_ibfk2` FOREIGN KEY (`asset_id_fk`) REFERENCES `asset` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_item_ibfk1` FOREIGN KEY (`menu_item_id_fk`) REFERENCES `menu_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `menu_item_api` (
  `module` varchar(20) NOT NULL,
  `generator` varchar(20) NOT NULL,
  PRIMARY KEY (`module`, `generator`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `menu_item_api` (`module`, `generator`) VALUES
  ('red', 'article'),
  ('red', 'paper'),
  ('red', 'multipage'),
  ('red', 'static'),
  ('events', 'static'),
  ('auth', 'static'),
  ('red', 'empty');

INSERT IGNORE INTO `menu_item_type` (`type`) VALUES ('multipage');

-- =============================================================================
-- C) menu_adjlist columns
-- =============================================================================
ALTER TABLE `menu_adjlist`
  ADD COLUMN `poradi` int(11) NOT NULL DEFAULT '0' AFTER `parent`,
  ADD COLUMN `level` int(11) NOT NULL DEFAULT '0' AFTER `poradi`;

-- =============================================================================
-- D) menu_item: type_fk → api_module_fk / api_generator_fk (2025/11)
-- =============================================================================
ALTER TABLE `menu_item` DROP FOREIGN KEY `type_menu_item_type_fk1`;

ALTER TABLE `menu_item`
  CHANGE COLUMN `type_fk` `api_module_fk` varchar(45) NULL DEFAULT NULL,
  ADD COLUMN `api_generator_fk` varchar(20) NULL DEFAULT NULL AFTER `api_module_fk`,
  DROP INDEX `type_menu_item_type_fk1`;

-- Map old type values → module/generator (also accept plain 'static')
-- MySQL uses original column values for all expressions in one UPDATE.
UPDATE `menu_item` SET
  `api_generator_fk` = CASE `api_module_fk`
    WHEN 'article' THEN 'article'
    WHEN 'paper' THEN 'paper'
    WHEN 'multipage' THEN 'multipage'
    WHEN 'static' THEN 'static'
    WHEN 'red_static' THEN 'static'
    WHEN 'events_static' THEN 'static'
    WHEN 'auth_static' THEN 'static'
    WHEN 'empty' THEN 'empty'
    ELSE `api_module_fk`
  END,
  `api_module_fk` = CASE `api_module_fk`
    WHEN 'article' THEN 'red'
    WHEN 'paper' THEN 'red'
    WHEN 'multipage' THEN 'red'
    WHEN 'static' THEN 'red'
    WHEN 'red_static' THEN 'red'
    WHEN 'events_static' THEN 'events'
    WHEN 'auth_static' THEN 'auth'
    WHEN 'empty' THEN 'red'
    ELSE `api_module_fk`
  END;

-- Any unmapped leftover types would break the FK — inspect before continuing:
-- SELECT api_module_fk, api_generator_fk, COUNT(*) FROM menu_item
-- GROUP BY api_module_fk, api_generator_fk;

ALTER TABLE `menu_item`
  MODIFY COLUMN `order` tinyint(1) NOT NULL DEFAULT '0',
  MODIFY COLUMN `prettyuri` varchar(200) DEFAULT NULL;

ALTER TABLE `menu_item`
  ADD INDEX `menu_item_api_fk` (`api_module_fk`, `api_generator_fk`);

ALTER TABLE `menu_item`
  ADD CONSTRAINT `menu_item_api_fk`
    FOREIGN KEY (`api_module_fk`, `api_generator_fk`)
    REFERENCES `menu_item_api` (`module`, `generator`)
    ON UPDATE CASCADE;

-- =============================================================================
-- E) menu_item PK: id → (lang_code_fk, uid_fk) (2025/10)
--    TZ currently: PRIMARY KEY(id), UNIQUE(lang_code_fk, uid_fk)
-- =============================================================================
ALTER TABLE `menu_item` DROP FOREIGN KEY `hierarchy_uid_fk`;
ALTER TABLE `menu_item` DROP FOREIGN KEY `language_lang_code_fk`;
ALTER TABLE `menu_item` DROP FOREIGN KEY `menu_item_api_fk`;

ALTER TABLE `menu_item` DROP INDEX `lang_code_fk`;

ALTER TABLE `menu_item`
  ADD INDEX `id_pk_tmp` (`id`);

ALTER TABLE `menu_item`
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`lang_code_fk`, `uid_fk`);

ALTER TABLE `menu_item`
  DROP INDEX `id_pk_tmp`,
  ADD UNIQUE INDEX `id` (`id`);

-- Indexes hierarchy_uid_fk / menu_item_api_fk typically remain after DROP FOREIGN KEY
ALTER TABLE `menu_item`
  ADD CONSTRAINT `hierarchy_uid_fk`
    FOREIGN KEY (`uid_fk`) REFERENCES `hierarchy` (`uid`) ON UPDATE CASCADE,
  ADD CONSTRAINT `language_lang_code_fk`
    FOREIGN KEY (`lang_code_fk`) REFERENCES `language` (`lang_code`),
  ADD CONSTRAINT `menu_item_api_fk`
    FOREIGN KEY (`api_module_fk`, `api_generator_fk`)
    REFERENCES `menu_item_api` (`module`, `generator`)
    ON UPDATE CASCADE;

-- =============================================================================
-- F) Seed static rows for existing static menu items (2026/14)
-- =============================================================================
INSERT INTO `static` (`menu_item_id_fk`, `template`, `creator`)
SELECT
  mi.id AS menu_item_id_fk,
  LOWER(
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
      LOWER(mi.title),
      'á','a'),'č','c'),'ď','d'),'é','e'),'ě','e'),'í','i'),'ň','n'),'ó','o'),'ř','r'),
      'š','s'),'ť','t'),'ú','u'),'ů','u'),'ý','y'),'ž','z'),'.','-'),' ','-'),'\t','-'),'\n','-')
  ) AS template,
  'transform' AS creator
FROM `menu_item` AS mi
WHERE mi.api_generator_fk = 'static'
  AND NOT EXISTS (
    SELECT 1 FROM `static` AS s WHERE s.menu_item_id_fk = mi.id
  );

-- =============================================================================
-- G) menu_root FK + optional name repair
-- =============================================================================
ALTER TABLE `menu_root` DROP FOREIGN KEY `nested_set_uid_fk2`;
ALTER TABLE `menu_root`
  ADD CONSTRAINT `nested_set_uid_fk2`
    FOREIGN KEY (`uid_fk`) REFERENCES `hierarchy` (`uid`) ON DELETE CASCADE;

UPDATE `menu_root` SET `name`='menu vertical' WHERE `name`='menu_vertical';
UPDATE `menu_root` SET `name`='menu horizontal' WHERE `name`='menu_horizontal';
UPDATE `menu_root` SET `name`='menu redirect' WHERE `name`='menu_redirect';

SET FOREIGN_KEY_CHECKS = 1;

-- Done. Expect runtime content gaps until editors re-check templates/static paths.
-- Re-dump structure and diff against najdisi_structure.sql.
