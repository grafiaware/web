-- DROP TABLE IF EXISTS `credentials`;
-- DROP TABLE IF EXISTS `registration`;
-- DROP TABLE IF EXISTS `login`;


CREATE TABLE `login` (
  `login_name` varchar(50) NOT NULL,
  PRIMARY KEY (`login_name`),
  UNIQUE KEY `login_name` (`login_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `credentials` (
  `login_name_fk` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`login_name_fk`),
  UNIQUE KEY `login_name_fk` (`login_name_fk`),
  CONSTRAINT `credentials_ibfk_1` FOREIGN KEY (`login_name_fk`) REFERENCES `login` (`login_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `registration` (
  `login_name_fk` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `email_time` datetime DEFAULT NULL,
  `uid` VARCHAR(20) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`login_name_fk`),
  UNIQUE KEY `login_name_fk` (`login_name_fk`),
  UNIQUE INDEX `uid_UNIQUE` (`uid` ASC),
  CONSTRAINT `registration_ibfk_1` FOREIGN KEY (`login_name_fk`) REFERENCES `login` (`login_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `registration`
ADD COLUMN `info` VARCHAR(250) NULL AFTER `uid`;


-- INSERT `login`(login_name ) SELECT user AS login_name  FROM `wwwgrafia`.`opravneni`;
-- INSERT `credentials`(login_name_fk, password_hash, role) SELECT user AS login_name_fk,  `password` AS password_hash, role FROM `wwwgrafia`.`opravneni`;