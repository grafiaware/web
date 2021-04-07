/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  pes2704
 * Created: 1. 4. 2021
 */

CREATE TABLE `visitor_data_post` (
  `login_name` varchar(45) NOT NULL,
  `short_name` varchar(45) NOT NULL,
  `prefix` varchar(45) DEFAULT NULL,
  `name` varchar(90) DEFAULT NULL,
  `surname` varchar(90) DEFAULT NULL,
  `postfix` varchar(45) DEFAULT NULL,
  `email` varchar(90) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `cv_education_text` varchar(3000) DEFAULT NULL,
  `cv_skills_text` varchar(3000) DEFAULT NULL,
  `cv_document` mediumblob,
  `cv_document_filename` varchar(200) DEFAULT NULL,
  `cv_document_mimetype` varchar(45) DEFAULT NULL,
  `letter_document` mediumblob,
  `letter_document_filename` varchar(200) DEFAULT NULL,
  `letter_document_mimetype` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`login_name`,`short_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- musí být prázdná tabulka - nesmí být NULL hodnota 
ALTER TABLE `veletrhprace`.`visitor_data_post`
CHANGE COLUMN `position_name` `position_name` VARCHAR(200) NOT NULL ,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`login_name`, `short_name`, `position_name`);