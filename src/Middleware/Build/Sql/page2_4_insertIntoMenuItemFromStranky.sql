-- naplnění menu_item selectem z stranky_innodb
-- list je dočasný, slouží pro propojení ve fázi naplňování 'menu_item' tabulky a bude odstraněn
INSERT INTO `menu_item` ( `lang_code_fk`, `list`, `order`,  `title`, `active`, `auto_generated`)
SELECT  'cs' AS lang_code_fk, stranky_innodb.list AS `list`, poradi AS `order`, nazev_lan1 AS title, aktiv_lan1 AS active, aut_gen AS auto_generated FROM stranky_innodb
UNION ALL
SELECT  'en' AS lang_code_fk, stranky_innodb.list AS `list`, poradi AS `order`, nazev_lan2 AS title, aktiv_lan2 AS active, aut_gen AS auto_generated FROM stranky_innodb
UNION ALL
SELECT  'de' AS lang_code_fk, stranky_innodb.list AS `list`, poradi AS `order`, nazev_lan3 AS title, aktiv_lan3 AS active, aut_gen AS auto_generated FROM stranky_innodb
	ORDER BY `list` ASC;

-- doplnění menu_item.type_fk
-- UPDATE menu_item
--         INNER JOIN
--     (SELECT  menu_item_type.`type`, IF( menu_item_type.`type`='paper', 0, IF(menu_item_type.`type`='block', 1, NULL)) AS autogen
--     FROM menu_item_type )  t  ON (menu_item.auto_generated!='0' = t.autogen>0)
-- SET menu_item.type_fk = t.`type`
-- WHERE ISNULL(menu_item.type_fk);

UPDATE menu_item
        INNER JOIN
    (SELECT menu_item.lang_code_fk, menu_item.list, IF(menu_item.auto_generated!='0', 'generated', 'paper') AS newtype
    FROM menu_item )  t  ON (menu_item.lang_code_fk=t.lang_code_fk AND menu_item.list=t.list)
SET menu_item.type_fk = t.`newtype`
WHERE ISNULL(menu_item.type_fk);

-- úprava - nově je active jen 0 nebo 1
UPDATE menu_item
SET active = 1
WHERE active>0;
