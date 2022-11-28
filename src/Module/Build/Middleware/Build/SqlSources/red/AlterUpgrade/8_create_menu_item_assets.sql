/**
 * Author:  pes2704
 * Created: 24. 11. 2022
 */

CREATE TABLE `menu_item_assets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu_item_id_FK` int(11) unsigned NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `editor_login_name` varchar(45) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `menu_item_fk1_idx` (`menu_item_id_FK`),
  KEY `filepath_idx` (`filepath`),
  CONSTRAINT `menu_item_fk1` FOREIGN KEY (`menu_item_id_FK`) REFERENCES `menu_item` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

