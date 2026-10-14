/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  pes2704
 * Created: 22. 1. 2025
 */

ALTER TABLE `job` 
CHANGE COLUMN `nazev` `nazev` VARCHAR(150) NULL DEFAULT NULL ,
CHANGE COLUMN `popis_pozice` `popis_pozice` VARCHAR(2000) NULL DEFAULT NULL ,
CHANGE COLUMN `pozadujeme` `pozadujeme` VARCHAR(2000) NULL DEFAULT NULL ,
CHANGE COLUMN `nabizime` `nabizime` VARCHAR(2000) NULL DEFAULT NULL ;
