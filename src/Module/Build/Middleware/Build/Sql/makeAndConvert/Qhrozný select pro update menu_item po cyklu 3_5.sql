-- INSERT INTO `menu_item` ( `lang_code_fk`, `list`, `order`,  `title`, `active`, `auto_generated`)
SELECT  m.lang_code_fk, m.uid_fk, s.`list`, s.`order`,  s.title, s.active, s.auto_generated FROM 
menu_item AS m
INNER JOIN
(SELECT  'cs' AS lang_code_fk, stranky_innodb.list AS `list`, poradi AS `order`, nazev_lan1 AS title, aktiv_lan1 AS active, aut_gen AS auto_generated FROM stranky_innodb
UNION ALL
SELECT  'en' AS lang_code_fk, stranky_innodb.list AS `list`, poradi AS `order`, nazev_lan2 AS title, aktiv_lan2 AS active, aut_gen AS auto_generated FROM stranky_innodb
UNION ALL
SELECT  'de' AS lang_code_fk, stranky_innodb.list AS `list`, poradi AS `order`, nazev_lan3 AS title, aktiv_lan3 AS active, aut_gen AS auto_generated FROM stranky_innodb
	ORDER BY `list` ASC) AS s
INNER JOIN 
list_uid AS l ON m.lang_code_fk=s.lang_code_fk AND m.uid_fk=l.uid AND s.list=l.list


----------------------------------------------------------------------------------

update 
menu_item_copy AS m

INNER JOIN

(SELECT  'cs' AS lang_code_fk, stranky_innodb.list AS `list`, poradi AS `order`, nazev_lan1 AS title, aktiv_lan1 AS active, aut_gen AS auto_generated FROM stranky_innodb
UNION ALL
SELECT  'en' AS lang_code_fk, stranky_innodb.list AS `list`, poradi AS `order`, nazev_lan2 AS title, aktiv_lan2 AS active, aut_gen AS auto_generated FROM stranky_innodb
UNION ALL
SELECT  'de' AS lang_code_fk, stranky_innodb.list AS `list`, poradi AS `order`, nazev_lan3 AS title, aktiv_lan3 AS active, aut_gen AS auto_generated FROM stranky_innodb
	ORDER BY `list` ASC) AS s

INNER JOIN 

list_uid AS l 
ON (m.lang_code_fk=s.lang_code_fk AND m.uid_fk=l.uid AND s.list=l.list)

SET m.list=s.list, m.title=s.title
