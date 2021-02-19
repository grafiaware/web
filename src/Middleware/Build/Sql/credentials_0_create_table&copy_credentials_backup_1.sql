-- DROP TABLE IF EXISTS `credentials`;
-- DROP TABLE IF EXISTS `registration`;
-- DROP TABLE IF EXISTS `login`;


CREATE TABLE `login` (
  `login_name` varchar(50) NOT NULL,
  PRIMARY KEY (`login_name`),
  UNIQUE KEY `login_name` (`login_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `credentials` (
  `login_name_FK` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `login_name_FK` (`login_name_FK`),
  CONSTRAINT `credentials_ibfk_1` FOREIGN KEY (`login_name_FK`) REFERENCES `login` (`login_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `registration` (
  `login_name_FK` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `email_timestamp` datetime NOT NULL,
  UNIQUE KEY `login_name_FK` (`login_name_FK`),
  CONSTRAINT `registration_ibfk_1` FOREIGN KEY (`login_name_FK`) REFERENCES `login` (`login_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- INSERT `login`(login_name ) SELECT user AS login_name  FROM `wwwgrafia`.`opravneni`;
-- INSERT `credentials`(login_name_FK, password_hash, role) SELECT user AS login_name_FK,  `password` AS password_hash, role FROM `wwwgrafia`.`opravneni`;