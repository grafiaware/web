-- dva "rodiče": node potomek parent a node potomek sub_parent, kořen je z sub_parent, parent pak obsahuje všechny rodiče až do ROOT
-- a depth je hloubka v celém stromu (ne jen v podstromu

SELECT 
	nested_set.uid, nested_set.depth, nested_set.left_node, nested_set.right_node, nested_set.parent_uid,
        menu_item.lang_code_fk, menu_item.type_fk, menu_item.id, menu_item.title, menu_item.active, menu_item.show_time, menu_item.hide_time,
	(ISNULL(menu_item.show_time) OR menu_item.show_time<=CURDATE()) AND (ISNULL(menu_item.hide_time) OR CURDATE()<=menu_item.hide_time) AS actual
FROM
    (SELECT 
        node.uid, 
        (COUNT(node.uid) - 1) AS depth, node.left_node, node.right_node, node.parent_uid 
    FROM
        menu_nested_set AS node
			CROSS JOIN 
		menu_nested_set AS parent ON node.left_node BETWEEN parent.left_node AND parent.right_node
			CROSS JOIN 
        menu_nested_set AS sub_parent ON node.left_node BETWEEN sub_parent.left_node AND sub_parent.right_node
    WHERE sub_parent.uid = '5d4be52f596c7' 
    GROUP BY node.uid
	HAVING depth BETWEEN 1 AND 3
    ) AS nested_set
        INNER JOIN
    menu_item ON (nested_set.uid = menu_item.uid_fk)
WHERE
    menu_item.lang_code_fk = 'cs'
        AND depth<6

ORDER BY nested_set.left_node