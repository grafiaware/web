/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  pes2704
 * Created: 27. 1. 2025
 */

ALTER TABLE `events`.`visitor_job_request` 
DROP COLUMN `position_name`;

ALTER TABLE `events`.`visitor_job_request` 
ADD COLUMN `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `letter_document`;
