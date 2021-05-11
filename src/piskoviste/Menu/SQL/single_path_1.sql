SELECT 
	nested_set.uid, nested_set.depth, nested_set.left_node, nested_set.right_node, nested_set.parent_uid,
        menu_item.lang_code_fk, menu_item.type_fk, menu_item.id, menu_item.title, menu_item.active, menu_item.show_time, menu_item.hide_time,
	(ISNULL(menu_item.show_time) OR menu_item.show_time<=CURDATE()) AND (ISNULL(menu_item.hide_time) OR CURDATE()<=menu_item.hide_time) AS actual
FROM
    (SELECT 
        parent.uid, (COUNT(grand_parent.uid) - 1) AS depth, parent.left_node, parent.right_node, parent.parent_uid 
    FROM
        menu_nested_set AS node
    CROSS JOIN menu_nested_set AS parent ON node.left_node BETWEEN parent.left_node AND parent.right_node
    CROSS JOIN menu_nested_set AS grand_parent ON parent.left_node BETWEEN grand_parent.left_node AND grand_parent.right_node
    WHERE node.uid = '5d4be532346ba'
    GROUP BY parent.uid) AS nested_set
        INNER JOIN
    menu_item ON (nested_set.uid = menu_item.uid_fk)
WHERE
    menu_item.lang_code_fk = 'cs'