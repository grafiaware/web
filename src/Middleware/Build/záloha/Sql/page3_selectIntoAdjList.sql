/*
 * -- druhá úroveň stromu menu jsou kořeny jednotlivých starých menu - zapsány jednotlivými inserty (a, l, p, s) a koš (Trash)  HARMONOGRAM??
 * -- do sloupce parent je zapsáno $ - jeich společným rodičem je kořen celého stromu
 * -- další úrovně vloženy ze selectu z tabulky menu_item
 * -- stránky, které jsou určeny pro sloty (HARMONOGRAM, ...) nejsou přidány do menu_adjlist, jsou jen v menu_item - dodatečně se pro ně musí nastavit menu_item.type na slot
 *
 */

-- používám přímo menu_item.lang_code_fk='cs'
-- řadím podle listu rodiče, pak pořadí pokud je zadáno - pokud je nula dávám na konec (999), pak list potomka - pro duplicitní pořadí a nezadaná pořadí
INSERT INTO menu_adjlist ( child, parent) VALUES ('$', NULL);

INSERT INTO menu_adjlist (child, parent)
    SELECT child, parent FROM (
SELECT DISTINCT childrens.list AS child, parents.list AS parent,
    -- IF(LENGTH(childrens.list) = 1, '$', IF(LENGTH(childrens.list) <= 3, SUBSTRING(parents.list,1, 1), parents.list)) AS parent
    if(childrens.order=0, 999, childrens.order ) AS poradi
    FROM menu_item AS parents
    CROSS JOIN
    (SELECT lang_code_fk, list, menu_item.order FROM menu_item) AS childrens
    WHERE parents.lang_code_fk='cs' AND childrens.lang_code_fk='cs' AND
        (
            parents.list='$' AND childrens.list IN ('a', 'l', 'p', 's', 'trash', 'slot')
            OR parents.list=SUBSTRING(childrens.list,1,1)  AND LENGTH(childrens.list) BETWEEN 2 AND 3
            OR parents.list=SUBSTRING(childrens.list,1,LENGTH(childrens.list)-3) AND SUBSTRING(  childrens.list,4,1)='_'
            OR parents.list = '$' AND LENGTH(childrens.list) > 3 AND SUBSTRING(  childrens.list,4,1)<>'_'
        )
    ORDER BY parent ASC,
    poradi ASC,
    child ASC
	) AS adjlist;

