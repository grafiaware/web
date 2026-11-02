/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  vlse2610
 * Created: 13. 5. 2026
 */

ALTER TABLE `representative` 
DROP FOREIGN KEY `fk_representative_login1`;
ALTER TABLE `representative` 
CHANGE COLUMN `login_login_name` `login_login_name` VARCHAR(100) NOT NULL ;
ALTER TABLE `representative` 
ADD CONSTRAINT `fk_representative_login1`
  FOREIGN KEY (`login_login_name`)
  REFERENCES `login` (`login_name`)
  ON UPDATE CASCADE;
  
  
ALTER TABLE `enroll` 
DROP FOREIGN KEY `login_login_name_fk`;
ALTER TABLE `enroll` 
CHANGE COLUMN `login_login_name_fk` `login_login_name_fk` VARCHAR(100) NOT NULL DEFAULT '' ;
ALTER TABLE `enroll` 
ADD CONSTRAINT `login_login_name_fk`
  FOREIGN KEY (`login_login_name_fk`)
  REFERENCES `login` (`login_name`)
  ON UPDATE CASCADE;


ALTER TABLE `visitor_job_request` 
DROP FOREIGN KEY `fk_visitor_data_post_login1`;
ALTER TABLE `visitor_job_request` 
CHANGE COLUMN `login_login_name` `login_login_name` VARCHAR(100) NOT NULL ;
ALTER TABLE `visitor_job_request` 
ADD CONSTRAINT `fk_visitor_data_post_login1`
  FOREIGN KEY (`login_login_name`)
  REFERENCES `login` (`login_name`)
  ON UPDATE CASCADE;
  

ALTER TABLE `visitor_profile` 
DROP FOREIGN KEY `fk_visitor_profile_login1`;
ALTER TABLE `visitor_profile` 
CHANGE COLUMN `login_login_name` `login_login_name` VARCHAR(100) NOT NULL ;
ALTER TABLE `visitor_profile` 
ADD CONSTRAINT `fk_visitor_profile_login1`
  FOREIGN KEY (`login_login_name`)
  REFERENCES `login` (`login_name`)
  ON UPDATE CASCADE;
  
  
    SET FOREIGN_KEY_CHECKS = 0;
ALTER TABLE `login` 
ADD COLUMN `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `login_name`,
ADD COLUMN `updated` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created`,
ADD COLUMN `deleted_due_to_auth` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `updated`,
CHANGE COLUMN `login_name` `login_name` VARCHAR(100) NOT NULL ;
    SET FOREIGN_KEY_CHECKS = 1;
  
  