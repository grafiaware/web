/* page5_2_insertHomeIntoBlockTable */
-- naplnění tabulky block
  --  položka home page
INSERT INTO block (uid_fk, name)
SELECT
menu_item.uid_fk, '{{home_name}}'
FROM menu_item
WHERE menu_item.list='{{home_list}}'
GROUP BY uid_fk