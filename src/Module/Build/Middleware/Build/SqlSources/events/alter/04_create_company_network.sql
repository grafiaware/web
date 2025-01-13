/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  pes2704
 * Created: 13. 1. 2025
 */

CREATE TABLE `events`.`company_social_network` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` INT(10) UNSIGNED NOT NULL,
  `icon` VARCHAR(45) NULL DEFAULT '',
  `link` VARCHAR(45) NULL DEFAULT '',
  `embed_code` VARCHAR(1000) NULL DEFAULT '',
  PRIMARY KEY (`id`),
  INDEX `network_company_fk_idx` (`company_id` ASC),
  CONSTRAINT `network_company_fk`
    FOREIGN KEY (`company_id`)
    REFERENCES `events`.`company` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

