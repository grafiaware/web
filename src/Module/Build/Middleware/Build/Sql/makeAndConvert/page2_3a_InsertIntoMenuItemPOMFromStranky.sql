DROP TABLE IF EXISTS `menu_item_POM`;

CREATE TABLE `menu_item_POM` (
  `lang_code_fk` char(25) NOT NULL,  -- bude UNIQUE KEY (`lang_code_fk`,`uid_fk`)
  `uid_fk` varchar(45) DEFAULT NULL,  -- bude změněno na NOT NULL po naplnění hodnot
  `api_module_fk` varchar(45) DEFAULT NULL,
  `api_generator_fk` varchar(20) DEFAULT NULL,
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `list` varchar(45) DEFAULT NULL,  -- list je vazba pro insert starých stránek do menu_item
  `order` tinyint(1) NOT NULL DEFAULT '0',
  `title` text, -- default pro db: CHARACTER SET utf8 COLLATE utf8_general_ci
  `prettyuri` varchar(100) DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `auto_generated` varchar(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
  -- UNIQUE KEY (`prettyuri`),
  -- CONSTRAINT `menu_item_api_fk` Fapi_module_fk`, `api_generator_fk`) REFERENCES `menu_item_api` (`module`, `generator`) ON UPDATE CASCADE
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



/* page2_3_a_insertIntoMenuItemPOMFromStranky */
-- naplnění menu_item selectem z stranky_innodb
-- list je dočasný, slouží pro propojení ve fázi naplňování 'menu_item' tabulky a bude odstraněn
INSERT INTO `menu_item_POM` ( `lang_code_fk`, `list`, `order`,  `title`, `active`, `auto_generated`)
SELECT  'cs' AS lang_code_fk, stranky_innodb.list AS `list`, poradi AS `order`, nazev_lan1 AS title, aktiv_lan1 AS active, aut_gen AS auto_generated FROM stranky_innodb
UNION ALL
SELECT  'en' AS lang_code_fk, stranky_innodb.list AS `list`, poradi AS `order`, nazev_lan2 AS title, aktiv_lan2 AS active, aut_gen AS auto_generated FROM stranky_innodb
UNION ALL
SELECT  'de' AS lang_code_fk, stranky_innodb.list AS `list`, poradi AS `order`, nazev_lan3 AS title, aktiv_lan3 AS active, aut_gen AS auto_generated FROM stranky_innodb
	ORDER BY `list` ASC;

