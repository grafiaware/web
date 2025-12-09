/* 
 * naplní tabulku static hodnotami z menu_item
 */
/**
 * Author:  pes2704
 * Created: 9. 12. 2025
 */

INSERT INTO static (menu_item_id_fk, template, creator) 
	SELECT 
	id AS menu_item_id_fk, 
	CASE
        WHEN INSTR(prettyuri, '-') = 0
            THEN ''
        ELSE SUBSTRING(prettyuri, INSTR(prettyuri, '-') + 1)
    END AS template,
    'transform' AS creator
    FROM menu_item WHERE api_generator_fk='static';