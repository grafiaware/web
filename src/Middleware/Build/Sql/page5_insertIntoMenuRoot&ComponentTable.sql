-- naplnění tabulky menu_root
  -- kořeny jednotlivých menu
  -- kořen a je přejmenován na block
INSERT INTO menu_root (uid_fk, name)
SELECT uid_fk, IF(list='a', 'block', list) as name FROM
menu_item
WHERE list='l' OR  list='p' OR  list='s' OR  list='a' OR  list='trash' OR  list='$'
GROUP BY uid_fk;

-- naplnění tabulky component
  -- potomky kořenové položky 'a' (tj potomky a)
INSERT INTO component (uid_fk, name)
SELECT menu_item.uid_fk, menu_item.list FROM
menu_item
INNER JOIN
(SELECT
list
FROM menu_item
WHERE list='a'
) AS comp ON (
    LENGTH(menu_item.list)>LENGTH(comp.list)
    AND SUBSTR(menu_item.list, 1, 1)=comp.list
    )
GROUP BY uid_fk