DROP TABLE IF EXISTS `paper`;
DROP TABLE IF EXISTS `menu_item`;
DROP TABLE IF EXISTS `menu_item_type`;
DROP TABLE IF EXISTS `component`;
DROP TABLE IF EXISTS `menu_root`;
DROP TABLE IF EXISTS `menu_adjlist`;
DROP TABLE IF EXISTS `menu_nested_set`;
DROP TABLE IF EXISTS `language`;

CREATE TABLE `language` (
  `lang_code` char(4) NOT NULL,
  `locale` char(25) DEFAULT NULL,
  `name` char(25) DEFAULT NULL,
  `collation` char(35) DEFAULT NULL,
  `state` char(4) DEFAULT NULL,
  PRIMARY KEY (`lang_code`),
  UNIQUE KEY (`lang_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `menu_nested_set` (
  `uid` char(20) NOT NULL,
  `left_node` int(11) NOT NULL,
  `right_node` int(11) NOT NULL,
  `parent_uid` char(20) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `menu_adjlist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,  -- slouží k načítání obsahu tabulky seřazeného v pořadí, ve kterém se vkládalo - nutné pro generování nested setu
  `child` varchar(45) NOT NULL,
  `parent` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`child`),
  CONSTRAINT `menu_adjlist_child_fk1` FOREIGN KEY ( `parent`) REFERENCES `menu_adjlist` (`child`) ON UPDATE CASCADE   -- t.j. on delete restrict (default) a on update cascade - nelze smazat parent, pokud má child
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `menu_item_type` (
  `type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `menu_item` (
  `lang_code_fk` char(25) NOT NULL,  -- bude UNIQUE KEY (`lang_code_fk`,`uid_fk`)
  `uid_fk` varchar(45) DEFAULT NULL,  -- bude změněno na NOT NULL po naplnění hodnot
  `type_fk` varchar(45) DEFAULT NULL,
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `list` varchar(45) DEFAULT NULL,  -- list je vazba pro insert starých stránek do menu_item
  `order` tinyint(80) unsigned NOT NULL DEFAULT '0',
  `title` text, -- default pro db: CHARACTER SET utf8 COLLATE utf8_general_ci
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `show_time` date DEFAULT NULL,
  `hide_time` date DEFAULT NULL,
  `auto_generated` varchar(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  CONSTRAINT `type_menu_item_type_fk1` FOREIGN KEY ( `type_fk`) REFERENCES `menu_item_type` (`type`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `paper` (
  `menu_item_id_fk` int(11) unsigned NOT NULL,
  `list` varchar(45) DEFAULT NULL,  -- list je vazba pro insert starých stránek do paper
  `headline` text,  -- default pro db: CHARACTER SET utf8 COLLATE utf8_general_ci
  `content` longtext,  -- default pro db: CHARACTER SET utf8 COLLATE utf8_general_ci
  `keywords` text,  -- default pro db: CHARACTER SET utf8 COLLATE utf8_general_ci
  `editor` varchar(20) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`menu_item_id_fk`),
  CONSTRAINT `menu_item_id_fk1` FOREIGN KEY ( `menu_item_id_fk`) REFERENCES `menu_item` (`id`),
  FULLTEXT KEY `search` (`headline`,`content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `component` (
  `name` varchar(128) NOT NULL,
  `uid_fk` varchar(45) NOT NULL,
  PRIMARY KEY (`name`),
  CONSTRAINT `nested_set_uid_fk1` FOREIGN KEY ( `uid_fk`) REFERENCES `menu_nested_set` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `menu_root` (
  `name` varchar(128) NOT NULL,
  `uid_fk` varchar(45) NOT NULL,
  PRIMARY KEY (`name`),
  CONSTRAINT `nested_set_uid_fk2` FOREIGN KEY ( `uid_fk`) REFERENCES `menu_nested_set` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;