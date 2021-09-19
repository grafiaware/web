CREATE TABLE `static` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_item_id_fk` int(11) unsigned NOT NULL,
  `path` varchar(150) NOT NULL,
  `folded` tinyint(1) NOT NULL DEFAULT '0',
  `editor` varchar(20) DEFAULT '',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `menu_item_id_fk2` (`menu_item_id_fk`),
  CONSTRAINT `menu_item_id_fk2` FOREIGN KEY (`menu_item_id_fk`) REFERENCES `menu_item` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
