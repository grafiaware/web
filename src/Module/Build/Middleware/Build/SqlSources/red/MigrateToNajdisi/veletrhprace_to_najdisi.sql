-- =============================================================================
-- Veletrh práce → NajdiSi schema (data-preserving)
-- Source dump: _db_dumps/veletrhprace_structure.sql
-- Target:      _db_dumps/najdisi_structure.sql
--
-- Gaps vs NajdiSi:
--   * menu_adjlist missing poradi, level
--   * menu_item: api_module_fk width, `order` tinyint, prettyuri width, FK ON UPDATE
--   * static old shape (path/folded/editor)
--   * drop legacy VP-only tables (data not migrated to events module): enrolled,
--     visitor_data, visitor_data_post
-- Not created (legacy Build convert only): stranky, stranky_innodb, menu_item_test
-- =============================================================================

-- -----------------------------------------------------------------------------
-- PRECHECK
-- -----------------------------------------------------------------------------
-- SELECT id, lang_code_fk, uid_fk FROM menu_item WHERE uid_fk IS NULL OR uid_fk = '';
-- SELECT lang_code_fk, uid_fk, COUNT(*) c FROM menu_item GROUP BY lang_code_fk, uid_fk HAVING c > 1;
-- SELECT LENGTH(prettyuri) AS len, COUNT(*) FROM menu_item WHERE prettyuri IS NOT NULL GROUP BY len HAVING len > 100;
-- SELECT LENGTH(api_module_fk) AS len, api_module_fk FROM menu_item WHERE api_module_fk IS NOT NULL GROUP BY api_module_fk HAVING len > 20;

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- 0) Remove legacy VP-only tables (all rows discarded)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `enrolled`;
DROP TABLE IF EXISTS `visitor_data_post`;
DROP TABLE IF EXISTS `visitor_data`;

-- -----------------------------------------------------------------------------
-- 1) menu_adjlist: add poradi + level
-- -----------------------------------------------------------------------------
ALTER TABLE `menu_adjlist`
  ADD COLUMN `poradi` int(11) NOT NULL DEFAULT '0' AFTER `parent`,
  ADD COLUMN `level` int(11) NOT NULL DEFAULT '0' AFTER `poradi`;

-- Heuristic fill of level from parent chain depth (safe default 0 already set).
-- Optional refinement after visual check of menus.

-- -----------------------------------------------------------------------------
-- 2) static: old → NajdiSi shape
-- -----------------------------------------------------------------------------
ALTER TABLE `static`
  ADD COLUMN `template` varchar(150) DEFAULT NULL AFTER `path`,
  ADD COLUMN `creator` varchar(100) DEFAULT NULL AFTER `template`;

UPDATE `static`
SET `creator` = NULLIF(`editor`, '')
WHERE (`creator` IS NULL OR `creator` = '')
  AND `editor` IS NOT NULL
  AND `editor` <> '';

ALTER TABLE `static`
  MODIFY COLUMN `path` varchar(250) DEFAULT NULL,
  DROP COLUMN `folded`,
  DROP COLUMN `editor`;

-- Seed missing static rows from menu_item (2026/14)
INSERT INTO `static` (`menu_item_id_fk`, `template`, `creator`)
SELECT
  mi.id AS menu_item_id_fk,
  LOWER(
	REPLACE(
            REPLACE(
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(
                                            REPLACE(
                                                REPLACE(
                                                    REPLACE(
                                                        REPLACE(
                                                            REPLACE(
                                                                REPLACE(
                                                                    REPLACE(
                                                                        REPLACE(
                                                                            REPLACE(
                                                                                REPLACE(LOWER(mi.title),'á','a'),
                                                                            'č','c'),
                                                                        'ď','d'),
                                                                    'é','e'),
                                                                'ě','e'),
                                                            'í','i'),
                                                        'ň','n'),
                                                    'ó','o'),
                                                'ř','r'),
                                            'š','s'),
                                        'ť','t'),
                                    'ú','u'),
                                'ů','u'),
                            'ý','y'),
                        'ž','z'),
                    '.', '-'),
                ' ', '-'),
            '\t', '-'),
        '\n', '-')
    ) AS template,
  'transform' AS creator
FROM `menu_item` AS mi
WHERE mi.api_generator_fk = 'static'
  AND NOT EXISTS (
    SELECT 1 FROM `static` AS s WHERE s.menu_item_id_fk = mi.id
  );

UPDATE `static` AS s
INNER JOIN `menu_item` AS mi ON mi.id = s.menu_item_id_fk
SET s.template = LOWER(
	REPLACE(
            REPLACE(
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(
                                            REPLACE(
                                                REPLACE(
                                                    REPLACE(
                                                        REPLACE(
                                                            REPLACE(
                                                                REPLACE(
                                                                    REPLACE(
                                                                        REPLACE(
                                                                            REPLACE(
                                                                                REPLACE(LOWER(mi.title),'á','a'),
                                                                            'č','c'),
                                                                        'ď','d'),
                                                                    'é','e'),
                                                                'ě','e'),
                                                            'í','i'),
                                                        'ň','n'),
                                                    'ó','o'),
                                                'ř','r'),
                                            'š','s'),
                                        'ť','t'),
                                    'ú','u'),
                                'ů','u'),
                            'ý','y'),
                        'ž','z'),
                    '.', '-'),
                ' ', '-'),
            '\t', '-'),
        '\n', '-')
    ),
  s.creator = IF(s.creator IS NULL OR s.creator = '', 'transform', s.creator)
WHERE mi.api_generator_fk = 'static'
  AND (s.template IS NULL OR s.template = '');

-- -----------------------------------------------------------------------------
-- 3) menu_item column widths / types
-- -----------------------------------------------------------------------------
ALTER TABLE `menu_item`
  MODIFY COLUMN `api_module_fk` varchar(45) DEFAULT NULL,
  MODIFY COLUMN `order` tinyint(1) NOT NULL DEFAULT '0',
  MODIFY COLUMN `prettyuri` varchar(200) DEFAULT NULL;

-- -----------------------------------------------------------------------------
-- 4) menu_item indexes / FKs aligned with NajdiSi
--    VP already has PK (lang_code_fk, uid_fk) and UNIQUE id — keep them.
-- -----------------------------------------------------------------------------
ALTER TABLE `menu_item` DROP FOREIGN KEY `menu_item_api_fk`;
ALTER TABLE `menu_item` DROP FOREIGN KEY `hierarchy_uid_fk`;
-- language_lang_code_fk already present on VP

-- Drop redundant / differently named indexes if they exist
ALTER TABLE `menu_item` DROP INDEX `menu_item_api_fk_idx`;
ALTER TABLE `menu_item` DROP INDEX `menu_item_api_generator_fk_idx`;
ALTER TABLE `menu_item` DROP INDEX `menu_item_api_fk_idx1`;

ALTER TABLE `menu_item`
  ADD INDEX `menu_item_api_fk` (`api_module_fk`, `api_generator_fk`);

ALTER TABLE `menu_item`
  ADD CONSTRAINT `hierarchy_uid_fk`
    FOREIGN KEY (`uid_fk`) REFERENCES `hierarchy` (`uid`) ON UPDATE CASCADE,
  ADD CONSTRAINT `menu_item_api_fk`
    FOREIGN KEY (`api_module_fk`, `api_generator_fk`)
    REFERENCES `menu_item_api` (`module`, `generator`)
    ON UPDATE CASCADE;

-- Ensure api seed rows exist (INSERT IGNORE)
INSERT IGNORE INTO `menu_item_api` (`module`, `generator`) VALUES
  ('red', 'article'),
  ('red', 'paper'),
  ('red', 'multipage'),
  ('red', 'static'),
  ('events', 'static'),
  ('auth', 'static'),
  ('red', 'empty');

-- -----------------------------------------------------------------------------
-- 5) menu_root ON DELETE CASCADE
-- -----------------------------------------------------------------------------
ALTER TABLE `menu_root` DROP FOREIGN KEY `nested_set_uid_fk2`;
ALTER TABLE `menu_root`
  ADD CONSTRAINT `nested_set_uid_fk2`
    FOREIGN KEY (`uid_fk`) REFERENCES `hierarchy` (`uid`) ON DELETE CASCADE;

-- Optional root name repair (2026/16)
-- UPDATE `menu_root` SET `name`='menu vertical' WHERE `name`='menu_vertical';
-- UPDATE `menu_root` SET `name`='menu horizontal' WHERE `name`='menu_horizontal';
-- UPDATE `menu_root` SET `name`='menu redirect' WHERE `name`='menu_redirect';

SET FOREIGN_KEY_CHECKS = 1;
