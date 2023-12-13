/* page5_3_insertIntoBlockTable */
-- naplnění tabulky block
  --  položky 'a'
INSERT INTO block (uid_fk, name)
SELECT
menu_item.uid_fk, menu_item.list
FROM menu_item
WHERE SUBSTR(menu_item.list, 1, 1)='a'
GROUP BY uid_fk