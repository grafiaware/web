/* page3_7_updateMenuItemTypes&Active  */
UPDATE menu_item
        INNER JOIN
    (SELECT menu_item.lang_code_fk, menu_item.list, 
        IF(menu_item.auto_generated!='0', 'rs', 'red') AS api_module,
        IF(menu_item.auto_generated!='0', 'generated', 'paper') AS api_generator
    FROM menu_item )  t  ON (menu_item.lang_code_fk=t.lang_code_fk AND menu_item.list=t.list)
SET menu_item.api_module_fk = t.`api_module`, menu_item.api_generator_fk = t.`api_generator`
WHERE ISNULL(menu_item.api_module_fk) AND ISNULL(menu_item.api_generator_fk);

-- úprava - nově je active jen 0 nebo 1
UPDATE menu_item
SET active = 1
WHERE active>0;
