/**
 * Author:  pes2704
 * Created: 28. 12. 2023
 */

-- bloky
-- potomci s list s podrtžítky
-- ostatní položky s různými hodnotami list
INSERT INTO menu_adjlist (child, parent, poradi, level)

		SELECT DISTINCT


        childrens.list AS child, parents.list AS parent,
        if(childrens.order<0, 0, childrens.order ) AS poradi,
        parents.level AS level
        FROM

        (SELECT menu_item.list, menu_item.lang_code_fk,
			if(menu_item.list={{root}}, 1,
				if(menu_item.list IN ({{in_menu_roots}}), 2,
                    LENGTH(menu_item.list)/3+2 )) AS level
			FROM menu_item) AS parents
        CROSS JOIN
        (SELECT lang_code_fk, list, menu_item.order
			FROM menu_item) AS childrens
        WHERE parents.lang_code_fk='cs' AND childrens.lang_code_fk='cs' AND
            (
                parents.list=SUBSTRING(childrens.list,1,LENGTH(childrens.list)-3) AND SUBSTRING(  childrens.list,4,1)='_'
                OR
                parents.list = {{root}} AND childrens.list <> {{root}} AND childrens.list NOT IN ({{in_menu_roots}}) AND LENGTH(childrens.list) > 3 AND SUBSTRING(  childrens.list,4,1)<>'_'
            )