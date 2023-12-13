/* page5_1_insertIntoMenuRootTable */
-- naplnění tabulky menu_root
  -- kořeny jednotlivých menu
  -- kořen a je přejmenován na block
INSERT INTO menu_root (uid_fk, name)
SELECT uid_fk, list as name FROM
menu_item
WHERE list='{{root}}'
GROUP BY uid_fk;
