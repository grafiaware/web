/* createTemporaryListUidTable */

DROP TABLE IF EXISTS `list_uid` ;
-- CREATE  TEMPORARY  TABLE  `list_uid`  (
CREATE  TABLE  `list_uid`  (
  `list` varchar(20) NOT NULL,
  `uid` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

