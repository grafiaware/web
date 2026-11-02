-- Baseline dump 2025 — target=red
-- Source: dumps_produkce/red_najdisi.sql
-- STRUCTURE ONLY — full data dump remains gitignored under docker/migrations/dumps/dumps_produkce/
-- No schema_migrations (journal written by rebuild CLI after import).
-- No schema-qualified identifiers. Import into the target DB connection.

SET FOREIGN_KEY_CHECKS=0;

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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `asset`;
CREATE TABLE `asset` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `filepath` varchar(255) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `editor_login_name` varchar(45) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `filepath_idx1` (`filepath`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `block`;
CREATE TABLE `block` (
  `name` varchar(128) NOT NULL,
  `uid_fk` varchar(45) NOT NULL,
  PRIMARY KEY (`name`),
  KEY `nested_set_uid_fk1` (`uid_fk`),
  CONSTRAINT `nested_set_uid_fk1` FOREIGN KEY (`uid_fk`) REFERENCES `hierarchy` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `hierarchy`;
CREATE TABLE `hierarchy` (
  `uid` char(20) NOT NULL,
  `left_node` int(11) NOT NULL,
  `right_node` int(11) NOT NULL,
  `parent_uid` char(20) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `item_action`;
CREATE TABLE `item_action` (
  `item_id` varchar(45) NOT NULL,
  `editor_login_name` varchar(45) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_id`,`editor_login_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

DROP TABLE IF EXISTS `menu_adjlist`;
CREATE TABLE `menu_adjlist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `child` varchar(45) NOT NULL,
  `parent` varchar(45) DEFAULT NULL,
  `poradi` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `child` (`child`)
) ENGINE=InnoDB AUTO_INCREMENT=374 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `menu_item`;
CREATE TABLE `menu_item` (
  `lang_code_fk` char(25) NOT NULL,
  `uid_fk` varchar(45) NOT NULL,
  `api_module_fk` varchar(45) DEFAULT NULL,
  `api_generator_fk` varchar(20) DEFAULT NULL,
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `list` varchar(45) DEFAULT NULL,
  `order` tinyint(1) NOT NULL DEFAULT '0',
  `title` text,
  `prettyuri` varchar(200) DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `auto_generated` varchar(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lang_code_fk`,`uid_fk`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `prettyuri` (`prettyuri`),
  KEY `menu_item_api_fk` (`api_module_fk`,`api_generator_fk`),
  KEY `hierarchy_uid_fk` (`uid_fk`),
  CONSTRAINT `hierarchy_uid_fk` FOREIGN KEY (`uid_fk`) REFERENCES `hierarchy` (`uid`) ON UPDATE CASCADE,
  CONSTRAINT `language_lang_code_fk` FOREIGN KEY (`lang_code_fk`) REFERENCES `language` (`lang_code`),
  CONSTRAINT `menu_item_api_fk` FOREIGN KEY (`api_module_fk`, `api_generator_fk`) REFERENCES `menu_item_api` (`module`, `generator`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2396 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `menu_item_api`;
CREATE TABLE `menu_item_api` (
  `module` varchar(20) NOT NULL,
  `generator` varchar(20) NOT NULL,
  PRIMARY KEY (`module`,`generator`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `menu_item_asset`;
CREATE TABLE `menu_item_asset` (
  `menu_item_id_fk` int(11) unsigned NOT NULL,
  `asset_id_fk` int(11) unsigned NOT NULL,
  PRIMARY KEY (`menu_item_id_fk`,`asset_id_fk`),
  KEY `asset_ibfk2_idx` (`asset_id_fk`),
  CONSTRAINT `asset_ibfk2` FOREIGN KEY (`asset_id_fk`) REFERENCES `asset` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_item_ibfk1` FOREIGN KEY (`menu_item_id_fk`) REFERENCES `menu_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

DROP TABLE IF EXISTS `menu_item_type`;
CREATE TABLE `menu_item_type` (
  `type` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `menu_root`;
CREATE TABLE `menu_root` (
  `name` varchar(128) NOT NULL,
  `uid_fk` varchar(45) NOT NULL,
  PRIMARY KEY (`name`),
  KEY `nested_set_uid_fk2` (`uid_fk`),
  CONSTRAINT `nested_set_uid_fk2` FOREIGN KEY (`uid_fk`) REFERENCES `hierarchy` (`uid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=2812 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `static`;
CREATE TABLE `static` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_item_id_fk` int(11) unsigned NOT NULL,
  `path` varchar(250) DEFAULT NULL,
  `template` varchar(150) DEFAULT NULL,
  `creator` varchar(100) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `menu_item_id_fk2` (`menu_item_id_fk`),
  CONSTRAINT `menu_item_id_fk2` FOREIGN KEY (`menu_item_id_fk`) REFERENCES `menu_item` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=248 DEFAULT CHARSET=utf8;

DROP VIEW IF EXISTS `fulltree`;
CREATE SQL SECURITY INVOKER VIEW `fulltree` AS
select `node`.`uid` AS `uidqq`,
  (count(`parent`.`uid`) - 1) AS `depth`,
  `node`.`left_node` AS `left_node`,
  `node`.`right_node` AS `right_node`,
  `node`.`parent_uid` AS `parent_uid`
from (`hierarchy` `node` join `hierarchy` `parent`
  on (`node`.`left_node` between `parent`.`left_node` and `parent`.`right_node`))
group by `node`.`uid`;

DROP VIEW IF EXISTS `hierarchy_view`;
CREATE SQL SECURITY INVOKER VIEW `hierarchy_view` AS
select
  `hierarchy`.`uid` AS `uid`,
  `hierarchy`.`left_node` AS `left_node`,
  `hierarchy`.`right_node` AS `right_node`,
  `hierarchy`.`parent_uid` AS `parent_uid`,
  `menu_item`.`lang_code_fk` AS `lang_code`,
  `menu_item`.`uid_fk` AS `uid_fk`,
  `menu_item`.`id` AS `id`,
  `menu_item`.`api_module_fk` AS `api_module_fk`,
  `menu_item`.`api_generator_fk` AS `api_generator_fk`,
  `menu_item`.`list` AS `list`,
  `menu_item`.`order` AS `order`,
  `menu_item`.`title` AS `title`,
  `menu_item`.`prettyuri` AS `prettyuri`,
  `menu_item`.`active` AS `active`,
  `menu_item`.`auto_generated` AS `auto_generated`
from (`hierarchy` left join `menu_item` on (`hierarchy`.`uid` = `menu_item`.`uid_fk`));

SET FOREIGN_KEY_CHECKS=1;
