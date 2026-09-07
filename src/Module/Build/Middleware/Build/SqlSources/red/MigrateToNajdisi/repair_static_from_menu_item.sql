-- =============================================================================
-- Repair: seed static rows missing after oa_to_najdisi (or VP) migration
-- Safe to re-run: inserts only where no static row exists for the menu_item.
-- Source: AlterRedDatabase/2026/14_inset_into_static_from_menu_item.sql
-- =============================================================================

-- PRECHECK (optional):
-- SELECT mi.id, mi.title, mi.api_module_fk, mi.api_generator_fk
-- FROM menu_item mi
-- LEFT JOIN static s ON s.menu_item_id_fk = mi.id
-- WHERE mi.api_generator_fk = 'static' AND s.id IS NULL;

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

-- VERIFY:
-- SELECT s.id, s.menu_item_id_fk, s.template, s.path, mi.title
-- FROM static s
-- JOIN menu_item mi ON mi.id = s.menu_item_id_fk
-- WHERE mi.id IN (490, 541, 574, 580);
