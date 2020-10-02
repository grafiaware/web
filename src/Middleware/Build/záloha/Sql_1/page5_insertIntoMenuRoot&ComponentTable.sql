-- naplnění tabulky menu_root
  -- kořeny jednotlivých menu
INSERT INTO menu_root (uid_fk, name)
SELECT uid_fk, list FROM
menu_item
WHERE list='l' OR  list='p' OR  list='s' OR  list='block' OR  list='trash'
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