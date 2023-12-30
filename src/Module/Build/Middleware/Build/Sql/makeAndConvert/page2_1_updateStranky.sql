/* page2_1_updateStranky */
UPDATE stranky
SET stranky.list='{{new_menu_list}}', stranky.poradi='{{new_menu_poradi}}'
WHERE stranky.list = '{{old_menu_list}}'