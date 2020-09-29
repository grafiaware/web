DROP TABLE IF EXISTS `rbac_permission`;
DROP TABLE IF EXISTS `rbac_group`;
DROP TABLE IF EXISTS `rbac_role`;
DROP TABLE IF EXISTS ``;


CREATE TABLE `rbac_permission` (
  `permission` char(25) NOT NULL,
  `allow` tinyint DEFAULT NULL,
  `deny` tinyint DEFAULT NULL,
  `parent_permission_fk` char(25) DEFAULT NULL,
  PRIMARY KEY (`permission`),
  UNIQUE KEY (`permission`),
  CONSTRAINT `parent_permission_fk1` FOREIGN KEY ( `parent_permission_fk`) REFERENCES `rbac_permission` (`permission`)   -- vlastní klíč
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `rbac_group` (
  `group` char(25) NOT NULL,
  `parent_group_fk` char(25) DEFAULT NULL,
  PRIMARY KEY (`group`),
  UNIQUE KEY (`group`)
  CONSTRAINT `parent_group_fk1` FOREIGN KEY ( `parent_group_fk`) REFERENCES `rbac_group` (`group`)   -- vlastní klíč
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `rbac_role` (
  `role` char(25) NOT NULL,
  `parent_role_fk` char(25) DEFAULT NULL,
  `group_fk` char(25) DEFAULT NULL,
  PRIMARY KEY (`role`),
  UNIQUE KEY (`role`),
  CONSTRAINT `parent_role_fk1` FOREIGN KEY ( `parent_role_fk`) REFERENCES `rbac_role` (`role`),   -- vlastní klíč
  CONSTRAINT `rbac_group_group_fk1` FOREIGN KEY ( `group_fk`) REFERENCES `rbac_group` (`group`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `menu_item_acl` (
  `permission_fk` char(25) DEFAULT NULL,
  `group` char(25) DEFAULT NULL,
  `permission` char(25) DEFAULT NULL,
  PRIMARY KEY (`acl_code`),
  UNIQUE KEY (`acl_code`),
  CONSTRAINT `parent_role_fk1` FOREIGN KEY ( `parent_role_fk`) REFERENCES `menu_item_role` (`role`),
  CONSTRAINT `parent_role_fk1` FOREIGN KEY ( `parent_role_fk`) REFERENCES `menu_item_role` (`role`),
  CONSTRAINT `parent_role_fk1` FOREIGN KEY ( `parent_role_fk`) REFERENCES `menu_item_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
