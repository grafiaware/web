/* page0_createStrankyInnoDb */

DROP TABLE IF EXISTS `stranky_innodb`;

-- tabulka stranky_innodb - je innodb a lze používat cizí klíče, má collation defaultní k utf8 - t.j. generalci (mám vícejazyčný obsah)
CREATE TABLE `stranky_innodb` (
  `list` varchar(50) NOT NULL,
  `poradi` tinyint(1) NOT NULL DEFAULT '0',
  `nazev_lan1` text,
  `obsah_lan1` longtext,
  `aktiv_lan1` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `aktiv_lan1start` date DEFAULT NULL,
  `aktiv_lan1stop` date DEFAULT NULL,
  `keywords_lan1` text,
  `nazev_lan2` text,
  `obsah_lan2` longtext,
  `aktiv_lan2` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `aktiv_lan2start` date DEFAULT NULL,
  `aktiv_lan2stop` date DEFAULT NULL,
  `keywords_lan2` text,
  `nazev_lan3` text,
  `obsah_lan3` longtext,
  `aktiv_lan3` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `aktiv_lan3start` date DEFAULT NULL,
  `aktiv_lan3stop` date DEFAULT NULL,
  `keywords_lan3` text,
  `aut_gen` varchar(6)NOT NULL DEFAULT '0',
  `editor` varchar(20)DEFAULT NULL,
  `zmena` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`list`),
  UNIQUE KEY `list` (`list`),
  FULLTEXT KEY `vyhledavani` (`nazev_lan1`,`obsah_lan1`,`nazev_lan2`,`obsah_lan2`,`nazev_lan3`,`obsah_lan3`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



ALTER TABLE `stranky_innodb`
ADD `flag_new` tinyint(1) unsigned NOT NULL DEFAULT '0'; 