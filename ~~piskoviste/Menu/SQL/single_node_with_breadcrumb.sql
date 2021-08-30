SELECT 
    menu_item_for_title.title,
    node.uid,
    (COUNT(parent.uid)) AS depth,
    GROUP_CONCAT(DISTINCT menu_item_for_breadcrumb.uid_fk,
        CONCAT(menu_item_for_breadcrumb.uid_fk,
                ' | ',
                menu_item_for_breadcrumb.title)
        ORDER BY menu_item_for_breadcrumb.uid_fk ASC
        SEPARATOR ' / ') AS breadcrumb
FROM
    menu_nested_set AS node
        CROSS JOIN
    menu_nested_set AS parent ON node.left_node BETWEEN parent.left_node AND parent.right_node
        INNER JOIN
    menu_item AS menu_item_for_breadcrumb ON (parent.uid = menu_item_for_breadcrumb.uid_fk)
        INNER JOIN
    menu_item AS menu_item_for_title ON (node.uid = menu_item_for_title.uid_fk)
WHERE node.uid = '5d4be532346ba'
        AND menu_item_for_breadcrumb.lang_code_fk = 'cs'
        AND menu_item_for_title.lang_code_fk = 'cs'