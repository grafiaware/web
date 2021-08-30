SELECT 
	nested_set.uid, nested_set.depth, nested_set.left_node, nested_set.right_node, nested_set.parent_uid,
        menu_item.lang_code_fk, menu_item.type_fk, menu_item.id, menu_item.title, menu_item.active, menu_item.show_time, menu_item.hide_time,
 	(ISNULL(menu_item.show_time) OR menu_item.show_time<=CURDATE()) AND (ISNULL(menu_item.hide_time) OR CURDATE()<=menu_item.hide_time) AS actual
FROM
(SELECT
        parent.uid,
parent.left_node, parent.right_node, parent.parent_uid ,
  (SELECT COUNT(*)
   FROM menu_nested_set AS middle_parent
   WHERE middle_parent.left_node BETWEEN grand_parent.left_node AND grand_parent.right_node
     AND parent.left_node BETWEEN middle_parent.left_node AND middle_parent.right_node
   ) AS depth
FROM menu_nested_set AS grand_parent
  INNER JOIN menu_nested_set AS parent ON parent.left_node BETWEEN grand_parent.left_node AND grand_parent.right_node
  INNER JOIN menu_nested_set AS node ON node.left_node BETWEEN parent.left_node AND parent.right_node
WHERE grand_parent.uid='5d4be52f596c7'
  AND node.uid='5d4be532346ba'
  )  AS nested_set
          INNER JOIN
    menu_item ON (nested_set.uid = menu_item.uid_fk)
 WHERE
     menu_item.lang_code_fk = 'cs'
--     AND menu_item.active=1 
--     AND (ISNULL(menu_item.show_time) OR menu_item.show_time<=CURDATE()) AND (ISNULL(menu_item.hide_time) OR CURDATE()<=menu_item.hide_time)
-- ORDER BY nested_set.left_node