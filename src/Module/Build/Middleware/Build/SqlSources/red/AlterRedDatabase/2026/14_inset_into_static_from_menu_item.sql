/* 
 * naplní tabulku static hodnotami z menu_item
 */
/**
 * Author:  pes2704
 * Created: 9. 12. 2025
 */

-- MySQL neumí regulární výrazy


INSERT INTO static (menu_item_id_fk, template, creator) 

SELECT
id AS menu_item_id_fk, 
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
                                                                                REPLACE(lower(title),'á','a'),
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
FROM najdisi.menu_item WHERE api_generator_fk='static';