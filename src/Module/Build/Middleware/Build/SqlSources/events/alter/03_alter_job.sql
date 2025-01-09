/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  pes2704
 * Created: 8. 1. 2025
 */

ALTER TABLE `events`.`job` 
ADD COLUMN `published` TINYINT(1) NOT NULL DEFAULT 0 AFTER `company_id`;
