UPDATE stranky_innodb
SET stranky_innodb.list='{{new_menu_list}}', stranky_innodb.poradi='{{new_menu_poradi}}'
WHERE stranky_innodb.list = '{{old_menu_list}}'