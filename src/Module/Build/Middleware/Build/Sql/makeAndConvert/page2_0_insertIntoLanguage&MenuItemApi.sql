/* page2_0_insertIntoLanguage&MenuItemType  */
INSERT INTO `language` (`lang_code`, `name`, `locale`, `collation`, `state`) VALUES ( 'cs', 'Čeština', 'cs-CZ', 'utf8_czech_ci', 'cz');
INSERT INTO `language` (`lang_code`, `name`, `locale`, `collation`, `state`) VALUES ( 'en', 'English', 'en-US', 'utf8_general_ci', 'gb');
INSERT INTO `language` (`lang_code`, `name`, `locale`, `collation`, `state`) VALUES ( 'de', 'Deutsch', 'de-DE', 'utf8_german2_ci', 'de');

-- INSERT INTO `menu_item_type` (`type`) VALUES ( 'root' );  -- typ root - jen pro jeden item odpovídající kořenu nested set - položka v menu_item musí být jinak nebude kořen v selectech, které jsou inner join s menu_item
-- INSERT INTO `menu_item_type` (`type`) VALUES ( 'empty' );  -- typ empty - obsah není vytvořen, bude nahrazen konkrétním typem
-- INSERT INTO `menu_item_type` (`type`) VALUES ( 'redirect' );  -- typ redirect - dojde k přesměrování na jinou url
-- INSERT INTO `menu_item_type` (`type`) VALUES ( 'static' );  -- typ static - obsah bude renderován ze statického obsahu (template)
-- INSERT INTO `menu_item_type` (`type`) VALUES ( 'paper' );  -- typ paper - obsah částí bude načten z db tabulky paper a paper content, delete přesune do menu trash
-- INSERT INTO `menu_item_type` (`type`) VALUES ( 'article' );  -- typ article - obsah bude poprvé generován z šablony, editovatelný a ukládán do db tabulky article, delete přesune do menu trash
-- INSERT INTO `menu_item_type` (`type`) VALUES ( 'multipage' );  -- typ multipage - obsah bude generován složením obsahů potomkovských stránek
-- INSERT INTO `menu_item_type` (`type`) VALUES ( 'trash' );  -- typ trash - koš - obsah bude načten z db tabulky paper, delete maže


INSERT INTO `menu_item_api` (`module`, `generator`) VALUES ('rs', 'generated');
INSERT INTO `menu_item_api` (`module`, `generator`) VALUES ('red', 'root');
INSERT INTO `menu_item_api` (`module`, `generator`) VALUES ('red', 'empty');
INSERT INTO `menu_item_api` (`module`, `generator`) VALUES ('red', 'select');
INSERT INTO `menu_item_api` (`module`, `generator`) VALUES ('red', 'article');
INSERT INTO `menu_item_api` (`module`, `generator`) VALUES ('red', 'paper');
INSERT INTO `menu_item_api` (`module`, `generator`) VALUES ('red', 'multipage');
INSERT INTO `menu_item_api` (`module`, `generator`) VALUES ('red', 'static');
INSERT INTO `menu_item_api` (`module`, `generator`) VALUES ('events', 'static');
INSERT INTO `menu_item_api` (`module`, `generator`) VALUES ('auth', 'static');
