ALTER TABLE `paper_content`
CHANGE COLUMN `template` `template_name` VARCHAR(100) NULL DEFAULT '' ,
ADD COLUMN `template` LONGTEXT NULL DEFAULT NULL AFTER `template_name`,
CHANGE COLUMN `event_time` `event_start_time` DATE NULL DEFAULT NULL ,
ADD COLUMN `event_end_time` DATE NULL DEFAULT NULL AFTER `event_start_time`;
