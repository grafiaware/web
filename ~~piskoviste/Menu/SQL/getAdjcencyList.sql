-- adjacency list zpetně z nested set
SELECT a.uid, b.uid AS parent
FROM menu_nested_set AS a
LEFT JOIN menu_nested_set AS b ON b.left_node = (
  SELECT MAX( left_node )
  FROM menu_nested_set AS t
  WHERE a.left_node > t.left_node AND a.left_node < t.right_node
)
ORDER BY a.uid;