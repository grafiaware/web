-- =============================================================================
-- Otevřené ateliéry (oa_upgrade) → NajdiSi schema (data-preserving)
-- Source dump: _db_dumps/oa_upgrade_structure.sql
-- Target:      _db_dumps/najdisi_structure.sql
--
-- Includes everything from grafia_to_najdisi.sql plus:
--   * menu_item.prettyuri varchar(100) → varchar(200)
--   * static old shape (path, folded, editor) → (path, template, creator)
-- Leftovers kept: list_uid, stranky_innodb.flag_new
-- =============================================================================

-- -----------------------------------------------------------------------------
-- PRECHECK (must return 0 rows before continuing)
-- -----------------------------------------------------------------------------
-- SELECT id, lang_code_fk, uid_fk FROM menu_item WHERE uid_fk IS NULL OR uid_fk = '';
-- SELECT lang_code_fk, uid_fk, COUNT(*) c FROM menu_item GROUP BY lang_code_fk, uid_fk HAVING c > 1;
-- SELECT LENGTH(prettyuri) AS len, COUNT(*) FROM menu_item WHERE prettyuri IS NOT NULL GROUP BY len HAVING len > 100;
-- SELECT mi.id FROM menu_item mi LEFT JOIN hierarchy h ON h.uid = mi.uid_fk WHERE h.uid IS NULL;
-- SELECT mi.lang_code_fk FROM menu_item mi LEFT JOIN language l ON l.lang_code = mi.lang_code_fk WHERE l.lang_code IS NULL;

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- 1) static: migrate old columns → NajdiSi shape (see 2026/13, without DROP TABLE)
-- -----------------------------------------------------------------------------
-- Old: path, folded, editor
-- New: path, template, creator

ALTER TABLE `static`
  ADD COLUMN `template` varchar(150) DEFAULT NULL AFTER `path`,
  ADD COLUMN `creator` varchar(100) DEFAULT NULL AFTER `template`;

-- Preserve editor login into creator; folded has no equivalent in NajdiSi (dropped below)
UPDATE `static`
SET `creator` = NULLIF(`editor`, '')
WHERE (`creator` IS NULL OR `creator` = '')
  AND `editor` IS NOT NULL
  AND `editor` <> '';

ALTER TABLE `static`
  MODIFY COLUMN `path` varchar(250) DEFAULT NULL,
  DROP COLUMN `folded`,
  DROP COLUMN `editor`;

-- Old OA stored the static folder in `path`. NajdiSi resolves WEB_STATIC + path + template/.
-- Move folder name into `template` and clear `path` so paths are not doubled.
UPDATE `static`
SET
  `template` = TRIM(BOTH '/' FROM REPLACE(`path`, '\\', '/')),
  `path` = NULL
WHERE (`template` IS NULL OR `template` = '')
  AND `path` IS NOT NULL
  AND TRIM(`path`) <> '';

-- If both path and template were filled (e.g. prior migration), keep template and clear path.
UPDATE `static`
SET `path` = NULL
WHERE `path` IS NOT NULL
  AND TRIM(`path`) <> ''
  AND `template` IS NOT NULL
  AND TRIM(`template`) <> '';

-- Seed missing static rows from menu_item (2026/14). Old OA often had static menu
-- items without a matching static row; UPDATE alone leaves the table empty.
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

-- Fill empty template on any pre-existing static rows
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
-- 2) menu_item: prettyuri width (2026/15)
-- -----------------------------------------------------------------------------
ALTER TABLE `menu_item`
  MODIFY COLUMN `prettyuri` varchar(200) DEFAULT NULL;

-- -----------------------------------------------------------------------------
-- 3) menu_item: PK + FKs like NajdiSi (2025/10)
-- -----------------------------------------------------------------------------
ALTER TABLE `menu_item`
  MODIFY COLUMN `uid_fk` varchar(45) NOT NULL;

ALTER TABLE `menu_item` DROP FOREIGN KEY `menu_item_api_fk`;

ALTER TABLE `menu_item`
  ADD INDEX `id_pk_tmp` (`id`);

ALTER TABLE `menu_item`
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`lang_code_fk`, `uid_fk`);

ALTER TABLE `menu_item`
  DROP INDEX `id_pk_tmp`,
  ADD UNIQUE INDEX `id` (`id`);

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
-- 4) menu_root: ON DELETE CASCADE
-- -----------------------------------------------------------------------------
ALTER TABLE `menu_root` DROP FOREIGN KEY `nested_set_uid_fk2`;
ALTER TABLE `menu_root`
  ADD CONSTRAINT `nested_set_uid_fk2`
    FOREIGN KEY (`uid_fk`) REFERENCES `hierarchy` (`uid`) ON DELETE CASCADE;

-- -----------------------------------------------------------------------------
-- 5) Optional: menu_root names (2026/16)
--    OA ConfigurationWeb still uses rootName 'menu_vertical' (underscore).
--    Run ONLY if you also change config to NajdiSi names ('menu vertical', …).
-- -----------------------------------------------------------------------------
-- UPDATE `menu_root` SET `name`='menu vertical' WHERE `name`='menu_vertical';
-- UPDATE `menu_root` SET `name`='menu horizontal' WHERE `name`='menu_horizontal';
-- UPDATE `menu_root` SET `name`='menu redirect' WHERE `name`='menu_redirect';

SET FOREIGN_KEY_CHECKS = 1;
