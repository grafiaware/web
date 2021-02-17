-- DROP TABLE IF EXISTS `credentials`;

CREATE TABLE `credentials` (
  `login_name` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50)  DEFAULT NULL,
  `email` varchar(100)  DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`login_name`),
  UNIQUE KEY `login_name` (`login_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- INSERT `credentials`(login_name, password_hash, role) SELECT user AS login_name, `password` AS password_hash, role FROM `wwwgrafia`.`opravneni`;