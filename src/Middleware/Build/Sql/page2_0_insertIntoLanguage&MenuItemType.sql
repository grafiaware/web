INSERT INTO `language` (`lang_code`, `name`, `locale`, `collation`, `state`) VALUES ( 'cs', 'Čeština', 'cs-CZ', 'utf8_czech_ci', 'cz');
INSERT INTO `language` (`lang_code`, `name`, `locale`, `collation`, `state`) VALUES ( 'en', 'English', 'en-US', 'utf8_general_ci', 'gb');
INSERT INTO `language` (`lang_code`, `name`, `locale`, `collation`, `state`) VALUES ( 'de', 'Deutsch', 'de-DE', 'utf8_german2_ci', 'de');

INSERT INTO `menu_item_type` (`type`) VALUES ( 'root' );  -- typ root - jen pro jeden item odpovídající kořenu nested set - položka v menu_item musí být jinak nebude kořen v selectech, které jsou inner join s menu_item
INSERT INTO `menu_item_type` (`type`) VALUES ( 'empty' );  -- typ empty - obsah není vytvořen, bude nahrazen konkrétním typem
INSERT INTO `menu_item_type` (`type`) VALUES ( 'redirect' );  -- typ redirect - dojde k přesměrování na jinou url
INSERT INTO `menu_item_type` (`type`) VALUES ( 'static' );  -- typ static - obsah bude renderován ze statického obsahu (template)
INSERT INTO `menu_item_type` (`type`) VALUES ( 'paper' );  -- typ paper - obsah bude načten z db tabulky paper, delete mění typ na trash
INSERT INTO `menu_item_type` (`type`) VALUES ( 'trash' );  -- typ trash - koš - obsah bude načten z db tabulky paper, delete maže
INSERT INTO `menu_item_type` (`type`) VALUES ( 'generated' );  -- typ generated - obsah bude generován jiným generátorem
