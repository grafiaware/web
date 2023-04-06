/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  pes2704
 * Created: 3. 1. 2023
 */

ALTER TABLE `menu_item`
ADD INDEX `id` (`id` ASC),
DROP PRIMARY KEY;

ALTER TABLE `menu_item`
ADD PRIMARY KEY (`lang_code_fk`, `uid_fk`);

ALTER TABLE `menu_item`
DROP INDEX `id` ,
ADD UNIQUE INDEX `id` (`id` ASC);