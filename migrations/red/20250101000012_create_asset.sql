/**
 * Author:  pes2704
 * Created: 19. 12. 2023
 */

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `menu_item_asset`;

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
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;

CREATE TABLE `menu_item_asset` (
  `menu_item_id_fk` int(11) unsigned NOT NULL,
  `asset_id_fk` int(11) unsigned NOT NULL,
  PRIMARY KEY (`menu_item_id_fk`,`asset_id_fk`),
  KEY `asset_ibfk2_idx` (`asset_id_fk`),
  CONSTRAINT `asset_ibfk2` FOREIGN KEY (`asset_id_fk`) REFERENCES `asset` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_item_ibfk1` FOREIGN KEY (`menu_item_id_fk`) REFERENCES `menu_item` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
