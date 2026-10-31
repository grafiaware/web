/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  pes2704
 * Created: 27. 1. 2025
 */

CREATE TABLE `company_parameter` (
  `company_id` int(10) unsigned NOT NULL,
  `job_limit` int(11) DEFAULT NULL,
  PRIMARY KEY (`company_id`),
  CONSTRAINT `fk_company1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
