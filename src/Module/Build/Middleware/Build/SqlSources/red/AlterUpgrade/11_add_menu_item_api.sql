/**
 * Author:  pes2704
 * Created: 26. 11. 2023
 */

DROP TABLE IF EXISTS `menu_item_api`;

CREATE TABLE `menu_item_api` (
  `module` varchar(20) NOT NULL,
  `generator` varchar(20) NOT NULL,
  PRIMARY KEY (`module`,`generator`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `menu_item` 
DROP FOREIGN KEY `type_menu_item_type_fk1`;

ALTER TABLE `menu_item` 
CHANGE COLUMN `type_fk` `api_module_fk` VARCHAR(45) NULL DEFAULT NULL ,
ADD COLUMN `api_generator_fk` VARCHAR(20) NULL AFTER `api_module_fk`,
DROP INDEX `type_menu_item_type_fk1` ;

INSERT INTO `menu_item_api` (`module`, `generator`) VALUES ('red', 'article');
INSERT INTO `menu_item_api` (`module`, `generator`) VALUES ('red', 'paper');
INSERT INTO `menu_item_api` (`module`, `generator`) VALUES ('red', 'multipage');
INSERT INTO `menu_item_api` (`module`, `generator`) VALUES ('red', 'static');
INSERT INTO `menu_item_api` (`module`, `generator`) VALUES ('events', 'static');
INSERT INTO `menu_item_api` (`module`, `generator`) VALUES ('auth', 'static');
INSERT INTO `menu_item_api` (`module`, `generator`) VALUES ('red', 'empty');

-- update v2 -> v3

UPDATE menu_item AS mi 
INNER JOIN 
(
SELECT 
lang_code_fk, 
uid_fk,
-- api_module_fk AS oldmod, api_generator_fk AS oldgen, 
if(api_module_fk='article', 'red', 
  if(api_module_fk='paper', 'red', 
    if(api_module_fk='multipage', 'red', 
      if(api_module_fk='red_static', 'red', 
	if(api_module_fk='events_static', 'events', 
  	  if(api_module_fk='auth_static', 'auth', 
  	    if(api_module_fk='empty', 'red', 
			NULL
            )
          )
        )  
      )
    )
  )
) AS api_module_fk,
if(api_module_fk='article', 'article', 
  if(api_module_fk='paper', 'paper', 
    if(api_module_fk='multipage', 'multipage', 
      if(api_module_fk='red_static', 'static', 
	if(api_module_fk='events_static', 'static', 
  	  if(api_module_fk='auth_static', 'static', 
  	    if(api_module_fk='empty', 'empty', 
			api_module_fk
            )
          )
        )  
      )
    )
  )
) AS api_generator_fk 

FROM menu_item
) AS sel 
ON (mi.lang_code_fk=sel.lang_code_fk AND mi.uid_fk=sel.uid_fk)
SET mi.api_module_fk=sel.api_module_fk, mi.api_generator_fk=sel.api_generator_fk;

ALTER TABLE `menu_item` 
CHANGE COLUMN `api_generator_fk` `api_generator_fk` VARCHAR(20) NULL DEFAULT NULL,
ADD INDEX `menu_item_api_fk_idx1` (`api_module_fk` ASC, `api_generator_fk` ASC);

ALTER TABLE `menu_item` 
ADD CONSTRAINT `menu_item_api_fk`
  FOREIGN KEY (`api_module_fk` , `api_generator_fk`)
  REFERENCES `menu_item_api` (`module` , `generator`)
  ON DELETE RESTRICT
  ON UPDATE CASCADE;