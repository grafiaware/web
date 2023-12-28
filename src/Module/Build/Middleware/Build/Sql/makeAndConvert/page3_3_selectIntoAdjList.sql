/**
 * Author:  pes2704
 * Created: 28. 12. 2023
 */

-- první úroveň menu
INSERT INTO menu_adjlist (child, parent, poradi, level)

		SELECT DISTINCT


        childrens.list AS child, parents.list AS parent,
        if(childrens.order<0, 0, childrens.order ) AS poradi,
        parents.level AS level
        FROM

        (SELECT menu_item.list, menu_item.lang_code_fk,
			if(menu_item.list='{{root}}', 1,
				if(menu_item.list IN ({{in_menu_roots}}), 2,
                    LENGTH(menu_item.list)/3+2 )) AS level
			FROM menu_item) AS parents
        CROSS JOIN
        (SELECT lang_code_fk, list, menu_item.order
			FROM menu_item) AS childrens
        WHERE parents.lang_code_fk='cs' AND childrens.lang_code_fk='cs' AND
            (
                parents.list='{{menu_root}}' AND SUBSTRING(childrens.list,1,1)='{{prefix}}' AND LENGTH(childrens.list) BETWEEN 2 AND 3
            )
