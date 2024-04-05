DROP TABLE IF EXISTS `menu_adjlist`;
CREATE TABLE `menu_adjlist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `child` varchar(45) NOT NULL,
  `parent` varchar(45) DEFAULT NULL,
  `poradi` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
