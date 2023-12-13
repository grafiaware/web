/* page2_4_updateMenuItemTypes&Active  */
-- doplnění menu_item.type_fk
-- UPDATE menu_item
--         INNER JOIN
--     (SELECT  menu_item_type.`type`, IF( menu_item_type.`type`='paper', 0, IF(menu_item_type.`type`='block', 1, NULL)) AS autogen
--     FROM menu_item_type )  t  ON (menu_item.auto_generated!='0' = t.autogen>0)
-- SET menu_item.type_fk = t.`type`
-- WHERE ISNULL(menu_item.type_fk);

UPDATE menu_item
        INNER JOIN
    (SELECT menu_item.lang_code_fk, menu_item.list, 
        IF(menu_item.auto_generated!='0', NULL, 'red') AS api_module,
        IF(menu_item.auto_generated!='0', 'generated', 'paper') AS api_generator
    FROM menu_item )  t  ON (menu_item.lang_code_fk=t.lang_code_fk AND menu_item.list=t.list)
SET menu_item.api_module_fk = t.`api_module`, menu_item.api_generator_fk = t.`api_generator`
WHERE ISNULL(menu_item.api_module_fk) AND ISNULL(menu_item.api_generator_fk);

-- úprava - nově je active jen 0 nebo 1
UPDATE menu_item
SET active = 1
WHERE active>0;
