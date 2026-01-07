/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  pes2704
 * Created: 7. 1. 2026
 */

ALTER TABLE `company_info` 
ADD CONSTRAINT `fk_info_company1`
  FOREIGN KEY (`company_id`)
  REFERENCES `events`.`company` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
