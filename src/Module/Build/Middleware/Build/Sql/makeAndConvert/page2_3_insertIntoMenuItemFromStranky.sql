/* page2_3_insertIntoMenuItemFromStranky */
-- naplnění menu_item selectem z stranky_innodb
-- list je dočasný, slouží pro propojení ve fázi naplňování 'menu_item' tabulky a bude odstraněn
INSERT INTO `menu_item` ( `lang_code_fk`, `list`, `order`,  `title`, `active`, `auto_generated`)
SELECT  'cs' AS lang_code_fk, stranky_innodb.list AS `list`, poradi AS `order`, nazev_lan1 AS title, aktiv_lan1 AS active, aut_gen AS auto_generated FROM stranky_innodb
UNION ALL
SELECT  'en' AS lang_code_fk, stranky_innodb.list AS `list`, poradi AS `order`, nazev_lan2 AS title, aktiv_lan2 AS active, aut_gen AS auto_generated FROM stranky_innodb
UNION ALL
SELECT  'de' AS lang_code_fk, stranky_innodb.list AS `list`, poradi AS `order`, nazev_lan3 AS title, aktiv_lan3 AS active, aut_gen AS auto_generated FROM stranky_innodb
	ORDER BY `list` ASC;
