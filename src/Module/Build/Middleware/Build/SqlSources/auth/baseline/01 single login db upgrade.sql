-- 1. vytvorení tabulky role
CREATE TABLE `role` (
  `role` varchar(50) NOT NULL,
  PRIMARY KEY (`role`),
  UNIQUE KEY `role_UNIQUE` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 2. naplnění daty - pouzitymi ve sloupci credantials.role
INSERT INTO role SELECT DISTINCT role FROM credentials WHERE (role IS NOT NULL) AND (role<>'');

-- 3. do tabulky credentials
UPDATE credentials 
SET credentials.role = NULL
WHERE credentials.role = '';

-- 4. změna sloupce role 
-- zmena jmena + na cizi klic + ondelete/update

ALTER TABLE `credentials` 
CHANGE COLUMN `role` `role_fk` VARCHAR(50) NULL DEFAULT NULL ;

ALTER TABLE `credentials` 
ADD INDEX `c_idx` (`role_fk` ASC);
ALTER TABLE `credentials` 
ADD CONSTRAINT `credentials_ibfk_2`
  FOREIGN KEY (`role_fk`)
  REFERENCES `role` (`role`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
  
  ALTER TABLE `credentials` 
DROP FOREIGN KEY `credentials_ibfk_2`;
ALTER TABLE `credentials` 
ADD CONSTRAINT `credentials_ibfk_2`
  FOREIGN KEY (`role_fk`)
  REFERENCES `role` (`role`)
  ON DELETE RESTRICT
  ON UPDATE CASCADE;