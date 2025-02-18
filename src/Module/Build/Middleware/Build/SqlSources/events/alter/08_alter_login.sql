/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  vlse2610
 * Created: 18. 2. 2025
 */

ALTER TABLE `events`.`login` 
ADD COLUMN `role` VARCHAR(50) NULL DEFAULT NULL AFTER `login_name`,
ADD COLUMN `email` VARCHAR(100) NULL DEFAULT NULL AFTER `role`,
ADD COLUMN `info` VARCHAR(250) NULL DEFAULT NULL AFTER `email`,
ADD COLUMN `modul` VARCHAR(20) NOT NULL DEFAULT 'events' AFTER `info`;

