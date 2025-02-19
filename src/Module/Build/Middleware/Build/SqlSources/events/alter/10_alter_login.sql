/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  vlse2610
 * Created: 19. 2. 2025
 */

ALTER TABLE `events`.`login` 
CHANGE COLUMN `modul` `module` VARCHAR(20) NOT NULL ,
ADD COLUMN `url` VARCHAR(250) NOT NULL AFTER `module`,
ADD COLUMN `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `url`,
ADD COLUMN `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created`;
