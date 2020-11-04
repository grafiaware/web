-- naplnění menu_item ze starých stránek
-- napevno připraveno pro tři jazyky, cs, en, de
-- typy root, segment - nové kořeny
-- typy paper, block - pro staré položky - načtené ze starých stránek, pokud je hodnota NULL doplní se při naplňování ze starých stránek typ paper jako u ostatních stránek
-- podle hodnoty ve sloupci list (a, l, p, s, trash) se vybírá do mezitabulky menu_adjlist a vytváří se obsah menu_root tabulky
-- kořen root=ROOT, segment=Bloky, trash=Odpad, l= Menu l, p=Menu p (kořen Menu s již existuje v stranky_innodb - a0 je titulní stránka - přejmenuji ji na načte se z stranky_innodb)
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `auto_generated`)
VALUES  ('cs', 'root', '$', 0, 'ROOT', 1, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `auto_generated`)
VALUES  ('en', 'root', '$', 0, 'ROOT', 1, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `auto_generated`)
VALUES  ('de', 'root', '$', 0, 'ROOT', 1, 0);

INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `auto_generated`)
VALUES  ('cs', 'trash', 'trash', 0, 'Odpad', 1, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `auto_generated`)
VALUES  ('en', 'trash', 'trash', 0, 'Trash', 1, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `auto_generated`)
VALUES  ('de', 'trash', 'trash', 0, 'Müll', 1, 0);

INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `auto_generated`)
VALUES  ('cs', 'blocks', 'a', 0, 'Bloky', 1, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `auto_generated`)
VALUES  ('en', 'blocks', 'a', 0, 'Blocks', 1, 0);
INSERT INTO `menu_item` ( `lang_code_fk`, `type_fk`, `list`, `order`,  `title`, `active`, `auto_generated`)
VALUES  ('de', 'blocks', 'a', 0, 'Blocken', 1, 0);
