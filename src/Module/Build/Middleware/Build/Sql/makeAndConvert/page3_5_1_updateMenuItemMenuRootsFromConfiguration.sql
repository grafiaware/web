/* page3_5_1_updateMenuItemMenuRootsFromConfiguration */

update menu_item

SET 
    menu_item.api_module_fk =   {{menu_root_api_module}},
    menu_item.api_generator_fk =  {{menu_root_api_generator}},
    menu_item.title =   {{menu_root_title}}

WHERE 	menu_item.list =  {{menu_root_list}}  and 
		 ( menu_item.lang_code_fk = 'cs' or
		   menu_item.lang_code_fk = 'en' or
		   menu_item.lang_code_fk = 'de' )



/*
INSERT INTO `menu_item` ( `lang_code_fk`, `api_module_fk`, `api_generator_fk`, `list`, `order`,  `title`, `active`, `auto_generated`)
VALUES  ('en', {{menu_root_api_module}}, {{menu_root_api_generator}}, {{menu_root_list}}, 0, {{menu_root_title}}, 1, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `api_module_fk`, `api_generator_fk`, `list`, `order`,  `title`, `active`, `auto_generated`)
VALUES  ('de', {{menu_root_api_module}}, {{menu_root_api_generator}}, {{menu_root_list}}, 0, {{menu_root_title}}, 1, 0);
*/

