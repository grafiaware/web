-- obsah je pro MySQL - v případě jiné databáze nutno upravit
INSERT INTO `language` (`lang_code`, `name`, `locale`, `collation`, `state`) VALUES ( 'cs', 'Čeština', 'cs-CZ', 'utf8_czech_ci', 'cz');
INSERT INTO `language` (`lang_code`, `name`, `locale`, `collation`, `state`) VALUES ( 'en', 'English', 'en-US', 'utf8_general_ci', 'gb');
INSERT INTO `language` (`lang_code`, `name`, `locale`, `collation`, `state`) VALUES ( 'de', 'Deutsch', 'de-DE', 'utf8_german2_ci', 'de');

INSERT INTO `menu_item_type` (`type`) VALUES ( 'root' );  -- typ root - jen pro item odpovídající kořenu nested set - položka v menu_item musí být jinak nebude kořen v selectech, které jsou inner join s menu_item
INSERT INTO `menu_item_type` (`type`) VALUES ( 'empty' );  -- typ empty - obsah není vytvořen, bude nahrazen konkrétním typem
INSERT INTO `menu_item_type` (`type`) VALUES ( 'redirect' );  -- typ redirect - dojde k přesměrování na jinou url
INSERT INTO `menu_item_type` (`type`) VALUES ( 'paper' );  -- typ paper - obsah bude (načten z db tabulky paper, delete přesune do koše
INSERT INTO `menu_item_type` (`type`) VALUES ( 'trash' );  -- typ trash - obsah bude (načten z db tabulky paper, delete maže
INSERT INTO `menu_item_type` (`type`) VALUES ( 'block' );  -- typ block - obsah určený ke vložení do komponenty block nebo do slotu


-- naplnění menu_item a paper ze starých stránek
-- napevno připraveno pro tři jazyky, cs, en, de
-- typy root, trash, slot
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
VALUES  ('cs', 'root', '$', 0, 'ROOT', 1,  NULL,  NULL, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
VALUES  ('en', 'root', '$', 0, 'ROOT', 1,  NULL,  NULL, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
VALUES  ('de', 'root', '$', 0, 'ROOT', 1,  NULL,  NULL, 0);

INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
VALUES  ('cs', 'trash', 'trash', 0, 'Odpad', 1,  NULL,  NULL, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
VALUES  ('en', 'trash', 'trash', 0, 'Trash', 1,  NULL,  NULL, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
VALUES  ('de', 'trash', 'trash', 0, 'Müll', 1,  NULL,  NULL, 0);

INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
VALUES  ('cs', 'block', 'block', 0, 'Bloky', 1,  NULL,  NULL, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
VALUES  ('en', 'block', 'block', 0, 'Blocks', 1,  NULL,  NULL, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
VALUES  ('de', 'block', 'block', 0, 'Blocken', 1,  NULL,  NULL, 0);

-- kořeny jednotlivých menu - kořen a=Components: kořeny l= Menu l, p=Menu P, kořen menu s je titulní stránka - načte se z stranky_innodb
-- type_fk je NULL, bude doplněno automaticky paper jako u ostatních stránek
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
VALUES  ('cs', NULL, 'a', 0, 'Komponenty', 1,  NULL,  NULL, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
VALUES  ('en', NULL, 'a', 0, 'Components', 1,  NULL,  NULL, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
VALUES  ('de', NULL, 'a', 0, 'Componenten', 1,  NULL,  NULL, 0);

INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
VALUES  ('cs', NULL, 'l', 0, 'Menu l', 1,  NULL,  NULL, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
VALUES  ('en', NULL, 'l', 0, 'Menu l', 1,  NULL,  NULL, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
VALUES  ('de', NULL, 'l', 0, 'Menu l', 1,  NULL,  NULL, 0);

INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
VALUES  ('cs', NULL, 'p', 0, 'Menu p', 1,  NULL,  NULL, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
VALUES  ('en', NULL, 'p', 0, 'Menu p', 1,  NULL,  NULL, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
VALUES  ('de', NULL, 'p', 0, 'Menu p', 1,  NULL,  NULL, 0);


-- naplnění menu_item selectem z stranky_innodb
-- list je dočasný, slouží pro propojení ve fázi naplňování 'menu_item' tabulky a bude odstraněn
INSERT INTO `menu_item` ( `lang_code_fk`, `list`, `order`,  `title`, `active`, `show_time`, `hide_time`, `auto_generated`)
SELECT  'cs' AS lang_code_fk, list, poradi AS `order`, nazev_lan1 AS title, aktiv_lan1 AS active,  aktiv_lan1start AS show_time,  aktiv_lan1stop AS hide_time, aut_gen AS auto_generated FROM stranky_innodb
UNION ALL
SELECT  'en' AS lang_code_fk, list, poradi AS `order`, nazev_lan2 AS title, aktiv_lan2 AS active,  aktiv_lan2start AS show_time,  aktiv_lan2stop AS hide_time, aut_gen AS auto_generated FROM stranky_innodb
UNION ALL
SELECT  'de' AS lang_code_fk, list, poradi AS `order`, nazev_lan3 AS title, aktiv_lan3 AS active,  aktiv_lan3start AS show_time,  aktiv_lan3stop AS hide_time, aut_gen AS auto_generated FROM stranky_innodb
	ORDER BY list ASC;

-- doplnění menu_item.type_fk
UPDATE menu_item
        INNER JOIN
    (SELECT  menu_item_type.`type`, IF( menu_item_type.`type`='paper', 0, IF(menu_item_type.`type`='block', 1, NULL)) AS autogen
    FROM menu_item_type )  t  ON (menu_item.auto_generated!='0' = t.autogen>0)
SET menu_item.type_fk = t.`type`
WHERE ISNULL(menu_item.type_fk);

-- smaže časy show_time a hide_time pokud jsou stejné - tak to má většina stránek, při založení stránky v RS jako active=1 se zřejmě zapíší aktuální datumy z formuláře
UPDATE menu_item
SET show_time = null, hide_time = null
WHERE show_time=hide_time AND active<2;

-- úprava - nově je active jen 0 nebo 1
UPDATE menu_item
SET active = 1
WHERE active=2;

-- naplnění paper selectem z stranky_innodb join menu_item
-- list a title jsou dočasné, list slouží pro propojení ve fázi naplňování 'paper' tabulky a bude odstraněn, title obsahuje duplicitně hodnotu,
-- která správně patří jen do menu_item - ve starých stránkám měla stránka vždy title, je možno toto title zkonvertovat do headline paper
INSERT INTO `paper` ( `menu_item_id_fk`, `list`, `headline`, `content`, `keywords`, `editor`, `updated`)
SELECT   id AS menu_item_id_fk, stranky_innodb.list, nazev_lan1 AS headline, obsah_lan1 AS content, keywords_lan1 AS keywords, editor, zmena  AS updated FROM stranky_innodb  INNER JOIN `menu_item`  ON  (menu_item.lang_code_fk='cs' AND stranky_innodb.list=menu_item.list)
UNION ALL
SELECT   id AS menu_item_id_fk, stranky_innodb.list, nazev_lan2 AS headline, obsah_lan2 AS content, keywords_lan2 AS keywords, editor, zmena  AS updated FROM stranky_innodb  INNER JOIN `menu_item`  ON  (menu_item.lang_code_fk='en' AND stranky_innodb.list=menu_item.list)
UNION ALL
SELECT   id AS menu_item_id_fk, stranky_innodb.list, nazev_lan3 AS headline, obsah_lan3 AS content, keywords_lan3 AS keywords, editor, zmena  AS updated FROM stranky_innodb  INNER JOIN `menu_item`  ON  (menu_item.lang_code_fk='de' AND stranky_innodb.list=menu_item.list)
ORDER BY list ASC;
