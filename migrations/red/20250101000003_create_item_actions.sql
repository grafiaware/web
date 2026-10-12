/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  pes2704
 * Created: 3. 9. 2021
 */

CREATE TABLE `item_action` (
  `type_fk` varchar(45) NOT NULL DEFAULT '',
  `item_id` varchar(45) NOT NULL,
  `editor_login_name` varchar(45) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`type_fk`,`item_id`),
  KEY `type_menu_item_type_fk2` (`type_fk`),
  CONSTRAINT `type_menu_item_type_fk2` FOREIGN KEY (`type_fk`) REFERENCES `menu_item_type` (`type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `item_action` 
DROP FOREIGN KEY `type_menu_item_type_fk2`;
ALTER TABLE `item_action` 
DROP COLUMN `type_fk`,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`item_id`),
DROP INDEX `type_menu_item_type_fk2` ;

ALTER TABLE `veletrhprace`.`item_action` 
DROP PRIMARY KEY,
ADD PRIMARY KEY (`item_id`, `editor_login_name`);

-- CREATE TABLE `item_action` (
--   `item_id` varchar(45) NOT NULL,
--   `editor_login_name` varchar(45) NOT NULL,
--   `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`item_id`,`editor_login_name`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
