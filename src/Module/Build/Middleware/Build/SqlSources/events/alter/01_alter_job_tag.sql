/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  pes2704
 * Created: 5. 1. 2025
 */

ALTER TABLE `events`.`job_tag` 
ADD COLUMN `color` VARCHAR(45) NOT NULL AFTER `tag`;