/**
 * Změna events databáze pro migraci produkční verze s ArrayModel dp databáze
 */

/**
 * Author:  pes2704
 * Created: 21. 3. 2022
 */

ALTER TABLE `events`.`enroll`
DROP COLUMN `id`,
CHANGE COLUMN `login_name` `login_login_name_fk` VARCHAR(50) NOT NULL DEFAULT '' ,
CHANGE COLUMN `eventid` `event_id_fk` INT(11) UNSIGNED NOT NULL ,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`login_login_name_fk`),
ADD UNIQUE INDEX `login_name_UNIQUE` (`login_login_name_fk` ASC),
ADD UNIQUE INDEX `event_id_fk_UNIQUE` (`event_id_fk` ASC);
ALTER TABLE `events`.`enroll`
ADD CONSTRAINT `login_login_name_fk`
  FOREIGN KEY (`login_login_name_fk`)
  REFERENCES `events`.`login` (`login_name`)
  ON DELETE RESTRICT
  ON UPDATE CASCADE,
ADD CONSTRAINT `event_id_fk`
  FOREIGN KEY (`event_id_fk`)
  REFERENCES `events`.`event` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

