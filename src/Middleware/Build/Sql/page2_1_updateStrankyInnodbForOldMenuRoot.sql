UPDATE stranky_innodb
SET stranky_innodb.list='{{new_menu_list}}'
WHERE stranky_innodb.list = '{{old_menu_list}}'