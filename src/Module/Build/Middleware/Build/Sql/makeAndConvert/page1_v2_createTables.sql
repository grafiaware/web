/*
POZOR!  Tabulky stranky a stranky_innodb vytváří skript page0 - kopie ze staré db
Tabulka menu_item je vytvořena bez primárního klíče a později bude změněna
*/

/*
MySQL Data Transfer
Source Host: localhost
Source Database: gr_upgrade
Target Host: localhost
Target Database: gr_upgrade
Date: 04.09.2023 19:01:59
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_item_id_fk` int(11) unsigned NOT NULL,
  `article` longtext,
  `template` varchar(100) DEFAULT '',
  `editor` varchar(20) DEFAULT '',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `menu_item_id_fk3` (`menu_item_id_fk`),
  CONSTRAINT `menu_item_id_fk3` FOREIGN KEY (`menu_item_id_fk`) REFERENCES `menu_item` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for block
-- ----------------------------
DROP TABLE IF EXISTS `block`;
CREATE TABLE `block` (
  `name` varchar(128) NOT NULL,
  `uid_fk` varchar(45) NOT NULL,
  PRIMARY KEY (`name`),
  KEY `nested_set_uid_fk1` (`uid_fk`),
  CONSTRAINT `nested_set_uid_fk1` FOREIGN KEY (`uid_fk`) REFERENCES `hierarchy` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hierarchy
-- ----------------------------
DROP TABLE IF EXISTS `hierarchy`;
CREATE TABLE `hierarchy` (
  `uid` char(20) NOT NULL,
  `left_node` int(11) NOT NULL,
  `right_node` int(11) NOT NULL,
  `parent_uid` char(20) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for item_action
-- ----------------------------
DROP TABLE IF EXISTS `item_action`;
CREATE TABLE `item_action` (
  `type_fk` varchar(45) NOT NULL DEFAULT '',
  `item_id` varchar(45) NOT NULL,
  `editor_login_name` varchar(45) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`type_fk`,`item_id`),
  KEY `type_menu_item_type_fk2` (`type_fk`),
  CONSTRAINT `type_menu_item_type_fk2` FOREIGN KEY (`type_fk`) REFERENCES `menu_item_type` (`type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for language
-- ----------------------------
DROP TABLE IF EXISTS `language`;
CREATE TABLE `language` (
  `lang_code` char(4) NOT NULL,
  `locale` char(25) DEFAULT NULL,
  `name` char(25) DEFAULT NULL,
  `collation` char(35) DEFAULT NULL,
  `state` char(4) DEFAULT NULL,
  PRIMARY KEY (`lang_code`),
  UNIQUE KEY `lang_code` (`lang_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for menu_adjlist
-- ----------------------------
DROP TABLE IF EXISTS `menu_adjlist`;
CREATE TABLE `menu_adjlist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `child` varchar(45) NOT NULL,
  `parent` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `child` (`child`)
) ENGINE=InnoDB AUTO_INCREMENT=223 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for menu_item
-- ----------------------------
DROP TABLE IF EXISTS `menu_item`;
CREATE TABLE `menu_item` (
  `lang_code_fk` char(25) NOT NULL,  -- bude UNIQUE KEY (`lang_code_fk`,`uid_fk`)
  `uid_fk` varchar(45) DEFAULT NULL,  -- bude změněno na NOT NULL po naplnění hodnot
  `type_fk` varchar(45) DEFAULT NULL,
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `list` varchar(45) DEFAULT NULL,  -- list je vazba pro insert starých stránek do menu_item
  `order` tinyint(1) NOT NULL DEFAULT '0',
  `title` text, -- default pro db: CHARACTER SET utf8 COLLATE utf8_general_ci
  `prettyuri` varchar(100) DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `auto_generated` varchar(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`prettyuri`),
  CONSTRAINT `type_menu_item_type_fk1` FOREIGN KEY ( `type_fk`) REFERENCES `menu_item_type` (`type`)
  ON UPDATE CASCADE
  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for menu_item_asset
-- ----------------------------
DROP TABLE IF EXISTS `menu_item_asset`;
CREATE TABLE `menu_item_asset` (
  `menu_item_id_FK` int(11) unsigned NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `editor_login_name` varchar(45) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`menu_item_id_FK`,`filepath`),
  CONSTRAINT `menu_item_fk1` FOREIGN KEY (`menu_item_id_FK`) REFERENCES `menu_item` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for menu_item_test
-- ----------------------------
DROP TABLE IF EXISTS `menu_item_test`;
CREATE TABLE `menu_item_test` (
  `lang_code_fk` char(25) NOT NULL,
  `uid_fk` varchar(45) NOT NULL,
  `type_fk` varchar(45) DEFAULT NULL,
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `list` varchar(45) DEFAULT NULL,
  `order` tinyint(80) NOT NULL DEFAULT '0',
  `title` text,
  `prettyuri` varchar(100) DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `auto_generated` varchar(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lang_code_fk`,`uid_fk`),
  UNIQUE KEY `id_test` (`id`),
  UNIQUE KEY `prettyuri_test` (`prettyuri`),
  KEY `type_menu_item_type_fk_test` (`type_fk`),
  KEY `hierarchy_uid_fk_test` (`uid_fk`),
  CONSTRAINT `hierarchy_uid_fk_test` FOREIGN KEY (`uid_fk`) REFERENCES `hierarchy` (`uid`),
  CONSTRAINT `language_lang_code_fk_test` FOREIGN KEY (`lang_code_fk`) REFERENCES `language` (`lang_code`),
  CONSTRAINT `type_menu_item_type_fk_test` FOREIGN KEY (`type_fk`) REFERENCES `menu_item_type` (`type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for menu_item_type
-- ----------------------------
DROP TABLE IF EXISTS `menu_item_type`;
CREATE TABLE `menu_item_type` (
  `type` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for menu_root
-- ----------------------------
DROP TABLE IF EXISTS `menu_root`;
CREATE TABLE `menu_root` (
  `name` varchar(128) NOT NULL,
  `uid_fk` varchar(45) NOT NULL,
  PRIMARY KEY (`name`),
  KEY `nested_set_uid_fk2` (`uid_fk`),
  CONSTRAINT `nested_set_uid_fk2` FOREIGN KEY (`uid_fk`) REFERENCES `hierarchy` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for multipage
-- ----------------------------
DROP TABLE IF EXISTS `multipage`;
CREATE TABLE `multipage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_item_id_fk` int(11) unsigned NOT NULL,
  `template` varchar(100) DEFAULT '',
  `editor` varchar(20) DEFAULT '',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `menu_item_id_fk4` (`menu_item_id_fk`),
  CONSTRAINT `menu_item_id_fk4` FOREIGN KEY (`menu_item_id_fk`) REFERENCES `menu_item` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for paper
-- ----------------------------
DROP TABLE IF EXISTS `paper`;
CREATE TABLE `paper` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu_item_id_fk` int(11) unsigned NOT NULL,
  `list` varchar(45) DEFAULT NULL,
  `headline` text,
  `perex` longtext,
  `template` varchar(100) DEFAULT '',
  `keywords` text,
  `editor` varchar(20) DEFAULT '',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `menu_item_id_fk1` (`menu_item_id_fk`),
  FULLTEXT KEY `searchpaper` (`headline`,`perex`),
  CONSTRAINT `menu_item_id_fk1` FOREIGN KEY (`menu_item_id_fk`) REFERENCES `menu_item` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=714 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for paper_section
-- ----------------------------
DROP TABLE IF EXISTS `paper_section`;
CREATE TABLE `paper_section` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `paper_id_fk` int(11) unsigned NOT NULL,
  `list` varchar(45) DEFAULT NULL,
  `content` longtext,
  `template_name` varchar(100) DEFAULT '',
  `template` longtext,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `priority` float unsigned NOT NULL DEFAULT '1',
  `show_time` date DEFAULT NULL,
  `hide_time` date DEFAULT NULL,
  `event_start_time` date DEFAULT NULL,
  `event_end_time` date DEFAULT NULL,
  `editor` varchar(20) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `paper_id_fk2` (`paper_id_fk`),
  FULLTEXT KEY `searchcontent` (`content`),
  CONSTRAINT `paper_id_fk2` FOREIGN KEY (`paper_id_fk`) REFERENCES `paper` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- ----------------------------
-- View structure for fulltree
-- ----------------------------
DROP VIEW IF EXISTS `fulltree`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fulltree` AS select `node`.`uid` AS `uid`,(count(`parent`.`uid`) - 1) AS `depth`,`node`.`left_node` AS `left_node`,`node`.`right_node` AS `right_node`,`node`.`parent_uid` AS `parent_uid` from (`hierarchy` `node` join `hierarchy` `parent` on((`node`.`left_node` between `parent`.`left_node` and `parent`.`right_node`))) group by `node`.`uid`;

-- ----------------------------
-- View structure for hierarchy_view
-- ----------------------------
DROP VIEW IF EXISTS `hierarchy_view`;
CREATE ALGORITHM=UNDEFINED DEFINER=`gr_upgrader`@`localhost` SQL SECURITY DEFINER VIEW `hierarchy_view` AS select `hierarchy`.`uid` AS `uid`,`hierarchy`.`left_node` AS `left_node`,`hierarchy`.`right_node` AS `right_node`,`hierarchy`.`parent_uid` AS `parent_uid`,`menu_item`.`lang_code_fk` AS `lang_code`,`menu_item`.`uid_fk` AS `uid_fk`,`menu_item`.`type_fk` AS `type_fk`,`menu_item`.`id` AS `id`,`menu_item`.`list` AS `list`,`menu_item`.`order` AS `order`,`menu_item`.`title` AS `title`,`menu_item`.`active` AS `active`,`menu_item`.`auto_generated` AS `auto_generated` from (`hierarchy` left join `menu_item` on((`hierarchy`.`uid` = `menu_item`.`uid_fk`)));
