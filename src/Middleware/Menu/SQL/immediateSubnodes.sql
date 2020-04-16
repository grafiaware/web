SELECT
	nested_set.uid, nested_set.depth, nested_set.nsdepth, nested_set.left_node, nested_set.right_node, nested_set.parent_uid,
        menu_item.lang_code_fk, menu_item.type_fk, menu_item.id, menu_item.title, menu_item.active, menu_item.show_time, menu_item.hide_time,
	(ISNULL(menu_item.show_time) OR menu_item.show_time<=CURDATE()) AND (ISNULL(menu_item.hide_time) OR CURDATE()<=menu_item.hide_time) AS actual
FROM
                (SELECT
                    node.uid, (COUNT(parent.uid) -1) AS depth, (COUNT(parent.uid) - sub_tree.depth -1) AS nsdepth, node.left_node, node.right_node, node.parent_uid 
                FROM
                    menu_nested_set AS node
                    CROSS JOIN
                    menu_nested_set AS parent ON node.left_node BETWEEN parent.left_node AND parent.right_node
                    CROSS JOIN
                    menu_nested_set AS sub_parent ON node.left_node BETWEEN sub_parent.left_node AND sub_parent.right_node
                    CROSS JOIN
                    (SELECT
						node.uid, (COUNT(parent.uid) - 1) AS depth
						FROM
							menu_nested_set AS node
							CROSS JOIN 
							menu_nested_set AS parent ON node.left_node BETWEEN parent.left_node AND parent.right_node
						WHERE
							node.uid = '5d4be52fac340'
						GROUP BY node.uid
						ORDER BY node.left_node) AS sub_tree
                WHERE sub_parent.uid = sub_tree.uid
                GROUP BY node.uid
                HAVING nsdepth BETWEEN 1 AND 2) AS nested_set
                    INNER JOIN
                menu_item ON (nested_set.uid = menu_item.uid_fk)
            WHERE menu_item.lang_code_fk = 'cs'
				-- AND menu_item.active = 1 
                -- AND (ISNULL(menu_item.show_time) OR menu_item.show_time<=CURDATE()) 
                -- AND (ISNULL(menu_item.hide_time) OR CURDATE()<=menu_item.hide_time) ORDER BY nested_set.left_node