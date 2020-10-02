DROP VIEW IF EXISTS hierarchy_view;

CREATE VIEW `hierarchy_view` AS
select `uid`, `left_node`, `right_node`, `parent_uid`,
`lang_code_fk`AS lang_code,
 `uid_fk`,
 `type_fk`,
 `id`,
 `list`,
 `order`,
 `title`,
 `active`,
`auto_generated`
from (`hierarchy` left join `menu_item` on((`hierarchy`.`uid` = `menu_item`.`uid_fk`)));