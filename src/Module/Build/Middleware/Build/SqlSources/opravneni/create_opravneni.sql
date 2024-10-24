/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  pes2704
 * Created: 9. 11. 2020
 */

CREATE TABLE `opravneni` (
  `user` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  `password` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `role` varchar(5) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `pormenu` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `newlist` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `dellist` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `chpass` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `edun` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `staffer` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `a0` tinyint(1) unsigned DEFAULT '0',
  `a1` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `a2` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `a3` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `s01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_01_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_01_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_01_03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s04` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s06` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s07` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s08` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s09` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `l01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `p01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `p02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `p03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `p04` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `p05` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `l02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `l03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s12` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s09_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s07_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s07_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s07_03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s09_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s09_03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s09_04` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s09_05` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s09_06` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s03_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s03_03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s03_04` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s03_05` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_01_04` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s06_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s03_06` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s03_07` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s03_08` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s03_09` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s03_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s06_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s04_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_04` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_05` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_06` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s09_07` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s02_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s04_04` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s04_05` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s02_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s02_03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_07` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_07_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_07_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_07_03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s08_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s08_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s08_03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s08_04` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s05` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s12_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_06_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_06_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_06_03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_06_04` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_06_05` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_06_06` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_09` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_10` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s04_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_06_07` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_11` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_12` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_14` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s10` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_13` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_15` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_16` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s11` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `p06` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s13` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s14` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s08_05` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s15` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `HARMONOGRAM` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_17` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_18` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `p07` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s16` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s16_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s16_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `p08` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_05_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_05_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_05_03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_05_04` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_05_05` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_05_06` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_05_07` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s16_03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_17_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_17_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_17_03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s17` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s16_04` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s18` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s18_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s19` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s18_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_19` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s18_03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s18_04` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s18_05` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s18_06` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s20` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s18_07` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s18_08` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s16_05` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s21` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s18_09` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s18_10` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s18_11` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s22` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s08_06` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s23` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s16_06` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s16_07` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s16_08` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s16_06_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s16_06_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s16_06_03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s16_06_04` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s16_06_05` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s16_06_06` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s16_06_07` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s16_06_08` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s24` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s24_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s25` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s24_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s24_03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s24_04` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s24_05` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s24_06` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s24_07` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s24_08` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s24_09` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s24_10` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s24_11` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s26` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s09_08` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s27` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s08_07` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s08_08` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s28` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s09_09` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s09_10` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s29` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `p09` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s30` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_01_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_03` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_04` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_05` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_06` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_07` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_08` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_09` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_10` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_11` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_12` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_13` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s31` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s32` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s33` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_14` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_15` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s34` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s35` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s36` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_16` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_17` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_18` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_19` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_20` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_21` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_22` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_23` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s37` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_24` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_20` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s01_08_25` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s32_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s38` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s32_02` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s08_09` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s08_10` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `p10` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `a4` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `a5` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s08_11` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s08_12` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s39` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s08_13` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s08_14` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s08_13_01` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user`),
  UNIQUE KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
