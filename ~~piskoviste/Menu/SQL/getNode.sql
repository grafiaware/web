SELECT 
	nested_set.uid, nested_set.depth, nested_set.left_node, nested_set.right_node, nested_set.parent_uid,
        menu_item.lang_code_fk, menu_item.type_fk, menu_item.id, menu_item.title, menu_item.active, menu_item.show_time, menu_item.hide_time,
	(ISNULL(menu_item.show_time) OR menu_item.show_time<=CURDATE()) AND (ISNULL(menu_item.hide_time) OR CURDATE()<=menu_item.hide_time) AS actual
        FROM
                (SELECT
        node.uid, (COUNT(parent.uid) - 1) AS depth, node.left_node, node.right_node, node.parent_uid 
                FROM
                    menu_nested_set AS node
                CROSS JOIN menu_nested_set AS parent ON node.left_node BETWEEN parent.left_node AND parent.right_node
                GROUP BY node.uid
                ) AS nested_set
                    INNER JOIN
                menu_item AS menu_item ON (nested_set.uid = menu_item.uid_fk)
            WHERE 
            menu_item.lang_code_fk = 'cs' AND nested_set.uid='5d4be52f596c7'