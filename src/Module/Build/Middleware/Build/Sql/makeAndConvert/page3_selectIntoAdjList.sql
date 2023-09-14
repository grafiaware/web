/*
 * -- druhá úroveň stromu menu jsou kořeny jednotlivých menu - 'trash', 'blocks', 'menu_vertical', 'menu_horizontal', 'menu_redirect'
 * -- do sloupce parent je zapsáno root - jeich společným rodičem je kořen celého stromu
 * -- další úrovně vloženy ze selectu z tabulky menu_item
 *
 */

-- používám přímo menu_item.lang_code_fk='cs'
-- řadím podle listu rodiče, pak pořadí pokud je zadáno - pokud je nula dávám na konec (999), pak list potomka - pro duplicitní pořadí a nezadaná pořadí
-- INSERT INTO menu_adjlist ( child, parent) VALUES ('root', NULL);

INSERT INTO menu_adjlist (child, parent)
	SELECT child, parent FROM (
		SELECT 'root' AS child, NULL AS parent, 0 AS poradi, 1 AS level
        UNION
		SELECT DISTINCT


        childrens.list AS child, parents.list AS parent,
        if(childrens.order<0, 0, childrens.order ) AS poradi,
        parents.level AS level
        FROM

        (SELECT menu_item.list, menu_item.lang_code_fk,
			if(menu_item.list='root', 1,
				if(menu_item.list IN ('trash', 'blocks', 'menu_vertical', 'menu_horizontal', 'menu_redirect'), 2,
                    LENGTH(menu_item.list)/3+2 )) AS level
			FROM menu_item) AS parents
        CROSS JOIN
        (SELECT lang_code_fk, list, menu_item.order
			FROM menu_item) AS childrens
        WHERE parents.lang_code_fk='cs' AND childrens.lang_code_fk='cs' AND
            (
                parents.list='root' AND childrens.list IN ('trash', 'blocks', 'menu_vertical', 'menu_horizontal', 'menu_redirect')
                OR
                parents.list='menu_vertical' AND SUBSTRING(childrens.list,1,1)='s' AND LENGTH(childrens.list) BETWEEN 2 AND 3
                OR
                parents.list='menu_horizontal' AND SUBSTRING(childrens.list,1,1)='p' AND LENGTH(childrens.list) BETWEEN 2 AND 3
                OR
                parents.list='menu_redirect' AND SUBSTRING(childrens.list,1,1)='l' AND LENGTH(childrens.list) BETWEEN 2 AND 3
                OR
                parents.list='blocks' AND SUBSTRING(childrens.list,1,1)='a' AND LENGTH(childrens.list) BETWEEN 2 AND 3
                -- parents.list=SUBSTRING(childrens.list,1,1)  AND LENGTH(childrens.list) BETWEEN 2 AND 3
                OR
                parents.list=SUBSTRING(childrens.list,1,LENGTH(childrens.list)-3) AND SUBSTRING(  childrens.list,4,1)='_'
                OR
                parents.list = 'root' AND childrens.list <> 'root' AND LENGTH(childrens.list) > 3 AND SUBSTRING(  childrens.list,4,1)<>'_'
            )
        ORDER BY level ASC,
        parent ASC,
        poradi ASC,
        child ASC
    	) AS adjlist;

