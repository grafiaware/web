/* page3_5_1_updateMenuItemMenuRootsFromConfiguration */

update 
menu_item as m

INNER JOIN 
list_uid AS l 
ON (m.uid_fk=l.uid )
 
 SET 
        m.api_module_fk =  {{menu_root_api_module}},
        m.api_generator_fk =  {{menu_root_api_generator}},
        m.title =  {{menu_root_title}}
    WHERE l.list = {{menu_root_list}} AND 
     ( m.lang_code_fk = 'cs' OR
       m.lang_code_fk = 'en' OR
       m.lang_code_fk = 'de' );

