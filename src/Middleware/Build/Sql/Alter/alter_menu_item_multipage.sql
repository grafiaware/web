ALTER TABLE `menu_item`
ADD COLUMN `multipage` CHAR(10) NOT NULL DEFAULT '' AFTER `auto_generated`;
