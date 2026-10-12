/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  pes2704
 * Created: 4. 1. 2026
 */

CREATE TABLE `company_version` (
  `version` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`version`));

ALTER TABLE `company` 
ADD COLUMN `version_fk` VARCHAR(45) NOT NULL AFTER `name`;

INSERT INTO `company_version` (`version`) VALUES ('2025');
INSERT INTO `company_version` (`version`) VALUES ('2026');

UPDATE `company` SET `version_fk` = '2025';

ALTER TABLE `company` 
ADD CONSTRAINT `company_version_fk_version`
  FOREIGN KEY (`version_fk`)
  REFERENCES `company_version` (`version`)
  ON DELETE RESTRICT
  ON UPDATE CASCADE;

ALTER TABLE `company` 
DROP INDEX `name_UNIQUE` ,
ADD UNIQUE INDEX `name_and_version_UNIQUE` (`name` ASC, `version_fk` ASC);
