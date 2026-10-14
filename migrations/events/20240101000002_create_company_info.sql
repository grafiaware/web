/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  pes2704
 * Created: 7. 1. 2025
 */

CREATE TABLE `events`.`company_info` (
  `company_id` INT(10) UNSIGNED NOT NULL,
  `introduction` VARCHAR(1000) NULL DEFAULT NULL,
  `video_link` VARCHAR(150) NULL DEFAULT NULL,
  `positives` VARCHAR(1000) NULL DEFAULT NULL,
  `social` VARCHAR(1000) NULL DEFAULT NULL,
  PRIMARY KEY (`company_id`));
