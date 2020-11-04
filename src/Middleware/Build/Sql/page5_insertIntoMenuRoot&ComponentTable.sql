-- naplnění tabulky menu_root
  -- kořeny jednotlivých menu
  -- kořen a je přejmenován na block
INSERT INTO menu_root (uid_fk, name)
SELECT uid_fk, list as name FROM
menu_item
WHERE list='root' OR  list='trash' OR  list='blocks' OR  list='menu_vertical' OR  list='menu_horizontal' OR  list='menu_redirect'
GROUP BY uid_fk;

-- naplnění tabulky block
  -- potomky kořenové položky 'a' (tj potomky a)
INSERT INTO block (uid_fk, name)
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