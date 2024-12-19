CREATE TABLE `company_parameter` (
  `company_id` int(10) unsigned NOT NULL,
  `job_limit` int(11) DEFAULT NULL,
  PRIMARY KEY (`company_id`),
  CONSTRAINT `fk_company1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
