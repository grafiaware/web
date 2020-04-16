SELECT 
	nested_set.uid, nested_set.depth, 
    menu_item.lang_code_fk, menu_item.type_fk, menu_item.title, menu_item.active, menu_item.show_time, menu_item.hide_time, 
 	(ISNULL(menu_item.show_time) OR menu_item.show_time<=CURDATE()) AND (ISNULL(menu_item.hide_time) OR CURDATE()<=menu_item.hide_time) AS actual
FROM
    (SELECT 
        node.uid, (COUNT(parent.uid) - 1) AS depth, node.left_node
    FROM
        menu_nested_set AS node
			CROSS JOIN 
		menu_nested_set AS parent
    WHERE
        node.left_node BETWEEN parent.left_node AND parent.right_node
    GROUP BY node.uid) AS nested_set
        INNER JOIN
     menu_item AS menu_item
 		INNER JOIN
 	 language AS language
     ON (nested_set.uid = menu_item.uid_fk AND menu_item.lang_code_fk=language.lang_code)
 WHERE
     language.lang_code = 'cs'
ORDER BY nested_set.left_node

