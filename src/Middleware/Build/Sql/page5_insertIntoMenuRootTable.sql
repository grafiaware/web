-- naplnění tabulky menu_root
  -- kořeny jednotlivých menu
  -- kořen a je přejmenován na block
INSERT INTO menu_root (uid_fk, name)
SELECT uid_fk, list as name FROM
menu_item
WHERE list='root' OR  list='trash' OR  list='blocks' OR  list='menu_vertical' OR  list='menu_horizontal' OR  list='menu_redirect'
GROUP BY uid_fk;
