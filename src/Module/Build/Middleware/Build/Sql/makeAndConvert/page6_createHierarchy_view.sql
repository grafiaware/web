/* page6_createHierarchy_view */

DROP VIEW IF EXISTS hierarchy_view;

-- CREATE VIEW `hierarchy_view` AS
-- select `uid`, `left_node`, `right_node`, `parent_uid`,
-- `lang_code_fk`AS lang_code,
--  `uid_fk`,
--  `type_fk`,
--  `id`,
--  `list`,
--  `order`,
--  `title`,
--  `active`,
-- `auto_generated`
-- from (`hierarchy` left join `menu_item` on((`hierarchy`.`uid` = `menu_item`.`uid_fk`)));

-- TOTO JE NOVÁ VERZE - VERZE V3

CREATE VIEW `hierarchy_view` AS 
select 
`hierarchy`.`uid` AS `uid`,
`hierarchy`.`left_node` AS `left_node`,
`hierarchy`.`right_node` AS `right_node`,
`hierarchy`.`parent_uid` AS `parent_uid`,
`menu_item`.`lang_code_fk` AS `lang_code`,
`menu_item`.`uid_fk` AS `uid_fk`,
`menu_item`.`id` AS `id`,
`menu_item`.`api_module_fk` AS `api_module_fk`,
`menu_item`.`api_generator_fk` AS `api_generator_fk`,
`menu_item`.`list` AS `list`,
`menu_item`.`order` AS `order`,
`menu_item`.`title` AS `title`,
`menu_item`.`active` AS `active`,
`menu_item`.`auto_generated` AS `auto_generated` 
from (`hierarchy` left join `menu_item` on((`hierarchy`.`uid` = `menu_item`.`uid_fk`)));


-- ----------------------------
-- View structure for fulltree
-- ----------------------------
DROP VIEW IF EXISTS `fulltree`;
CREATE VIEW `fulltree` AS select `node`.`uid` AS `uidqq`,(count(`parent`.`uid`) - 1) AS `depth`,`node`.`left_node` AS `left_node`,`node`.`right_node` AS `right_node`,`node`.`parent_uid` AS `parent_uid` from (`hierarchy` `node` join `hierarchy` `parent` on((`node`.`left_node` between `parent`.`left_node` and `parent`.`right_node`))) group by `node`.`uid`;
