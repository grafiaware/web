/* createTemporaryListUidTable */

CREATE  TEMPORARY  TABLE  `list_uid` IF NOT EXIST (
  `list` varchar(20) NOT NULL,
  `uid` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


