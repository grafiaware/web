/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  pes2704
 * Created: 27. 3. 2021
 */

CREATE TABLE `enrolled` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_name` varchar(45) DEFAULT '',
  `eventid` varchar(45) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
