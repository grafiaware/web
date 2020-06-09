DROP VIEW IF EXISTS hierarchy_view;

CREATE VIEW `hierarchy_view` AS
select `uid`, `left_node`, `right_node`,
`lang_code_fk`AS lang_code,
 `uid_fk`,
 `type_fk`,
 `id`,
 `list`,
 `order`,
 `title`,
 `active`,
 `show_time`,
 `hide_time`,
`auto_generated`
from (`hierarchy` left join `menu_item` on((`hierarchy`.`uid` = `menu_item`.`uid_fk`)));