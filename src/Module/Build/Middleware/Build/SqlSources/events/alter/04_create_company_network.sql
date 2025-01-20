/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  pes2704
 * Created: 13. 1. 2025
 */

CREATE TABLE `company_to_network` (
  `company_id` int(10) unsigned NOT NULL,
  `network_id` int(10) unsigned NOT NULL,
  `link` varchar(200) DEFAULT '',
  PRIMARY KEY (`company_id`,`network_id`),
  KEY `network_company_fk_idx` (`company_id`),
  KEY `network_fk_idx` (`network_id`),
  CONSTRAINT `network_company_fk` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `network_fk` FOREIGN KEY (`network_id`) REFERENCES `network` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `events`.`company_to_network` 
ADD COLUMN `published` TINYINT(1) NULL DEFAULT 0 AFTER `link`;

-- -----------------------------------
CREATE TABLE `network` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `icon` varchar(45) NOT NULL,
  `embed_code_template` varchar(1000) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `company_to_network` (
  `company_id` int(10) unsigned NOT NULL,
  `network_id` int(10) unsigned NOT NULL,
  `link` varchar(200) DEFAULT '',
  `published` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`company_id`,`network_id`),
  KEY `network_company_fk_idx` (`company_id`),
  KEY `network_fk_idx` (`network_id`),
  CONSTRAINT `network_company_fk` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `network_fk` FOREIGN KEY (`network_id`) REFERENCES `network` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

