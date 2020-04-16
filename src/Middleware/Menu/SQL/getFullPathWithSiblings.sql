SELECT 
	nested_set.uid, nested_set.depth, nested_set.left_node, nested_set.right_node, nested_set.parent_uid,
        menu_item.lang_code_fk, menu_item.type_fk, menu_item.id, menu_item.title, menu_item.active, menu_item.show_time, menu_item.hide_time,
	(ISNULL(menu_item.show_time) OR menu_item.show_time<=CURDATE()) AND (ISNULL(menu_item.hide_time) OR CURDATE()<=menu_item.hide_time) AS actual
    FROM
    (SELECT 
    sub.uid, (COUNT(sub_parent.uid) ) AS depth, sub.left_node, sub.right_node, sub.parent_uid
FROM
    menu_nested_set AS node
        CROSS JOIN
    menu_nested_set AS parent
        CROSS JOIN
    menu_nested_set AS sub
        CROSS JOIN
    menu_nested_set AS sub_parent
WHERE
    node.left_node BETWEEN parent.left_node AND parent.right_node
        AND node.uid = '5d4be532346ba'
        AND sub.left_node BETWEEN parent.left_node AND parent.right_node
        AND sub_parent.uid = parent.uid
        AND sub.left_node BETWEEN sub_parent.left_node AND sub_parent.right_node
GROUP BY sub.uid
ORDER BY sub.left_node
    ) AS nested_set
        INNER JOIN
    menu_item ON (nested_set.uid = menu_item.uid_fk)
WHERE
    menu_item.lang_code_fk = 'cs'
ORDER BY nested_set.left_node