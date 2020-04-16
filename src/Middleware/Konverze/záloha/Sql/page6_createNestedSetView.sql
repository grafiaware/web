DROP VIEW IF EXISTS menu_nested_set_view;

CREATE VIEW `menu_nested_set_view` AS
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
from (`menu_nested_set` left join `menu_item` on((`menu_nested_set`.`uid` = `menu_item`.`uid_fk`)));