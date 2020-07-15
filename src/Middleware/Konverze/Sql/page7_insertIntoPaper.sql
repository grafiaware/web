-- naplnění paper a paper_content selectem z stranky_innodb join menu_item
-- list slouží pro propojení ve fázi naplňování 'paper' tabulky a bude odstraněn
-- obsah starých stránek (content) se kopíruje do perex, pokud jsou nemá nasteveno časové omezení show_time IS NULL AND hide_time IS NULL
INSERT INTO `paper` ( `menu_item_id_fk`, `list`, `headline`, `perex`, `keywords`, `editor`, `updated`)
SELECT   id AS menu_item_id_fk, stranky_innodb.list, nazev_lan1 AS headline, IF((show_time IS NULL AND hide_time IS NULL), obsah_lan1, '') AS perex, keywords_lan1 AS keywords, editor, zmena  AS updated
FROM
  stranky_innodb
    INNER JOIN
  `menu_item` ON  (menu_item.lang_code_fk='cs' AND stranky_innodb.list=menu_item.list)
UNION ALL
SELECT   id AS menu_item_id_fk, stranky_innodb.list, nazev_lan2 AS headline, IF((show_time IS NULL AND hide_time IS NULL), obsah_lan2, '')  AS perex, keywords_lan2 AS keywords, editor, zmena  AS updated
FROM
  stranky_innodb
    INNER JOIN
  `menu_item`  ON  (menu_item.lang_code_fk='en' AND stranky_innodb.list=menu_item.list)
UNION ALL
SELECT   id AS menu_item_id_fk, stranky_innodb.list, nazev_lan3 AS headline, IF((show_time IS NULL AND hide_time IS NULL), obsah_lan3, '')  AS perex, keywords_lan3 AS keywords, editor, zmena  AS updated
FROM
  stranky_innodb
    INNER JOIN
  `menu_item`  ON  (menu_item.lang_code_fk='de' AND stranky_innodb.list=menu_item.list)
ORDER BY list ASC;


-- paper_content
-- priority je default = 1, event_time je null
-- obsah starých stránek (content) se kopíruje do content, pokud jsou má nasteveno časové omezení show_time IS NOT NULL AND hide_time IS NOT NULL
INSERT INTO `paper_content` ( `paper_id_fk`, `list`, `content`, `active`, `show_time`, `hide_time`, `editor`, `updated`)
SELECT paper.id AS paper_id_fk, stranky_innodb.list, obsah_lan1 AS content, aktiv_lan1 AS active, aktiv_lan1start AS show_time, aktiv_lan1stop AS hide_time, stranky_innodb.editor AS editor, zmena  AS updated
FROM
  stranky_innodb
    INNER JOIN
  `menu_item`  ON  (menu_item.lang_code_fk='cs' AND stranky_innodb.list=menu_item.list)
    INNER JOIN
  paper ON (menu_item.id=paper.menu_item_id_fk)
  WHERE (show_time IS NOT NULL OR hide_time IS NOT NULL)
UNION ALL
SELECT paper.id AS paper_id_fk, stranky_innodb.list, obsah_lan2 AS content, aktiv_lan2 AS active, aktiv_lan2start AS show_time, aktiv_lan2stop AS hide_time, stranky_innodb.editor AS editor, zmena  AS updated
FROM
  stranky_innodb
    INNER JOIN
  `menu_item`  ON  (menu_item.lang_code_fk='en' AND stranky_innodb.list=menu_item.list)
    INNER JOIN
  paper ON (menu_item.id=paper.menu_item_id_fk)
  WHERE (show_time IS NOT NULL OR hide_time IS NOT NULL)
UNION ALL
SELECT paper.id AS paper_id_fk, stranky_innodb.list, obsah_lan3 AS content, aktiv_lan3 AS active, aktiv_lan3start AS show_time, aktiv_lan3stop AS hide_time, stranky_innodb.editor AS editor, zmena  AS updated
FROM
  stranky_innodb
    INNER JOIN
  `menu_item`  ON  (menu_item.lang_code_fk='de' AND stranky_innodb.list=menu_item.list)
    INNER JOIN
  paper ON (menu_item.id=paper.menu_item_id_fk)
  WHERE (show_time IS NOT NULL OR hide_time IS NOT NULL)
ORDER BY list ASC;


-- smaže časy show_time a hide_time pokud jsou stejné - tak to má většina stránek, při založení stránky v RS jako active=1 se zřejmě zapíší aktuální datumy z formuláře
UPDATE paper_content
SET show_time = null, hide_time = null
WHERE show_time=hide_time AND active<2;

-- úprava - nově je active jen 0 nebo 1
UPDATE paper_content
SET active = 1
WHERE active=2;