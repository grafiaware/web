/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  pes2704
 * Created: 1. 1. 2026
 */

UPDATE `menu_root` SET `name`='menu vertical' WHERE `name`='menu_vertical';
INSERT INTO `menu_root` (`name`, `uid_fk`) VALUES ('root', '65aff921880ba');

INSERT INTO `menu_item_api` (`module`, `generator`) VALUES ('red', 'menu');


UPDATE `menu_item` SET `api_generator_fk`='menu' WHERE `lang_code_fk`='cs' and`uid_fk`='65aff921880ba';
UPDATE `menu_item` SET `api_generator_fk`='menu' WHERE `lang_code_fk`='cs' and`uid_fk`='65aff9218b378';
UPDATE `menu_item` SET `api_generator_fk`='menu' WHERE `lang_code_fk`='cs' and`uid_fk`='65aff9218e693';
UPDATE `menu_item` SET `api_generator_fk`='menu' WHERE `lang_code_fk`='cs' and`uid_fk`='65aff92192171';
