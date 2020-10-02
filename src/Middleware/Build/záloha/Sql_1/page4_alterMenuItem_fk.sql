-- nastav v menu_item uid_fk not null
ALTER TABLE menu_item MODIFY `uid_fk` varchar(45) NOT NULL;  -- not null

-- dokončení menu_item sloupců lan_code_fk a uid_fk jako kompozitního primárního klíče, s tím, že lan_code_fk je cizí klíč lang_code a uid_fk je cizí klíč menu_nested_set
ALTER TABLE menu_item ADD PRIMARY KEY (`lang_code_fk`,`uid_fk`);  -- nejdriv PK
ALTER TABLE menu_item ADD UNIQUE KEY (`lang_code_fk`,`uid_fk`);
ALTER TABLE menu_item ADD CONSTRAINT `menu_nested_set_uid_fk` FOREIGN KEY (`uid_fk`) REFERENCES `menu_nested_set` (`uid`);  -- potom FK
ALTER TABLE menu_item ADD CONSTRAINT `language_lang_code_fk` FOREIGN KEY (`lang_code_fk`) REFERENCES `language` (`lang_code`);
