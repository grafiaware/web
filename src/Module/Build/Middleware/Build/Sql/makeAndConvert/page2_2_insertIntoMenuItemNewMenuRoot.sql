/* page2_2_insertIntoMenuItemNewMenuRoot */
INSERT INTO `menu_item` ( `lang_code_fk`, `api_module_fk`, `api_generator_fk`, `list`, `order`,  `title`, `active`, `auto_generated`)
VALUES  ('cs', {{menu_root_api_module}}, {{menu_root_api_generator}}, {{menu_root_list}}, 0, {{menu_root_title}}, 1, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `api_module_fk`, `api_generator_fk`, `list`, `order`,  `title`, `active`, `auto_generated`)
VALUES  ('en', {{menu_root_api_module}}, {{menu_root_api_generator}}, {{menu_root_list}}, 0, {{menu_root_title}}, 1, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `api_module_fk`, `api_generator_fk`, `list`, `order`,  `title`, `active`, `auto_generated`)
VALUES  ('de', {{menu_root_api_module}}, {{menu_root_api_generator}}, {{menu_root_list}}, 0, {{menu_root_title}}, 1, 0);

