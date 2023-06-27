CREATE DATABASE  IF NOT EXISTS `gr_upgrade` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `gr_upgrade`;
-- MySQL dump 10.13  Distrib 5.7.12, for Win64 (x86_64)
--
-- Host: localhost    Database: gr_upgrade
-- ------------------------------------------------------
-- Server version	5.6.35-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `article`
--

DROP TABLE IF EXISTS `article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_item_id_fk` int(11) unsigned NOT NULL,
  `article` longtext,
  `template` varchar(100) DEFAULT '',
  `editor` varchar(20) DEFAULT '',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `menu_item_id_fk3` (`menu_item_id_fk`),
  CONSTRAINT `menu_item_id_fk3` FOREIGN KEY (`menu_item_id_fk`) REFERENCES `menu_item` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `block`
--

DROP TABLE IF EXISTS `block`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `block` (
  `name` varchar(128) NOT NULL,
  `uid_fk` varchar(45) NOT NULL,
  PRIMARY KEY (`name`),
  KEY `nested_set_uid_fk1` (`uid_fk`),
  CONSTRAINT `nested_set_uid_fk1` FOREIGN KEY (`uid_fk`) REFERENCES `hierarchy` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `fulltree`
--

DROP TABLE IF EXISTS `fulltree`;
/*!50001 DROP VIEW IF EXISTS `fulltree`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `fulltree` AS SELECT 
 1 AS `uid`,
 1 AS `depth`,
 1 AS `left_node`,
 1 AS `right_node`,
 1 AS `parent_uid`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `hierarchy`
--

DROP TABLE IF EXISTS `hierarchy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hierarchy` (
  `uid` char(20) NOT NULL,
  `left_node` int(11) NOT NULL,
  `right_node` int(11) NOT NULL,
  `parent_uid` char(20) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `hierarchy_view`
--

DROP TABLE IF EXISTS `hierarchy_view`;
/*!50001 DROP VIEW IF EXISTS `hierarchy_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `hierarchy_view` AS SELECT 
 1 AS `uid`,
 1 AS `left_node`,
 1 AS `right_node`,
 1 AS `parent_uid`,
 1 AS `lang_code`,
 1 AS `uid_fk`,
 1 AS `type_fk`,
 1 AS `id`,
 1 AS `list`,
 1 AS `order`,
 1 AS `title`,
 1 AS `active`,
 1 AS `auto_generated`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `item_action`
--

DROP TABLE IF EXISTS `item_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_action` (
  `type_fk` varchar(45) NOT NULL DEFAULT '',
  `item_id` varchar(45) NOT NULL,
  `editor_login_name` varchar(45) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`type_fk`,`item_id`),
  KEY `type_menu_item_type_fk2` (`type_fk`),
  CONSTRAINT `type_menu_item_type_fk2` FOREIGN KEY (`type_fk`) REFERENCES `menu_item_type` (`type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `language` (
  `lang_code` char(4) NOT NULL,
  `locale` char(25) DEFAULT NULL,
  `name` char(25) DEFAULT NULL,
  `collation` char(35) DEFAULT NULL,
  `state` char(4) DEFAULT NULL,
  PRIMARY KEY (`lang_code`),
  UNIQUE KEY `lang_code` (`lang_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu_adjlist`
--

DROP TABLE IF EXISTS `menu_adjlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_adjlist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `child` varchar(45) NOT NULL,
  `parent` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `child` (`child`)
) ENGINE=InnoDB AUTO_INCREMENT=223 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu_item`
--

DROP TABLE IF EXISTS `menu_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_item` (
  `lang_code_fk` char(25) NOT NULL,
  `uid_fk` varchar(45) NOT NULL,
  `type_fk` varchar(45) DEFAULT NULL,
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `list` varchar(45) DEFAULT NULL,
  `order` tinyint(80) NOT NULL DEFAULT '0',
  `title` text,
  `prettyuri` varchar(100) DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `auto_generated` varchar(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lang_code_fk`,`uid_fk`),
  UNIQUE KEY `lang_code_fk` (`lang_code_fk`,`uid_fk`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `prettyuri` (`prettyuri`),
  KEY `type_menu_item_type_fk1` (`type_fk`),
  KEY `hierarchy_uid_fk` (`uid_fk`),
  CONSTRAINT `hierarchy_uid_fk` FOREIGN KEY (`uid_fk`) REFERENCES `hierarchy` (`uid`),
  CONSTRAINT `language_lang_code_fk` FOREIGN KEY (`lang_code_fk`) REFERENCES `language` (`lang_code`),
  CONSTRAINT `type_menu_item_type_fk1` FOREIGN KEY (`type_fk`) REFERENCES `menu_item_type` (`type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=769 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu_item_asset`
--

DROP TABLE IF EXISTS `menu_item_asset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_item_asset` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu_item_id_FK` int(11) unsigned NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `editor_login_name` varchar(45) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `filepath_idx` (`filepath`),
  KEY `menu_item_fk1_idx` (`menu_item_id_FK`),
  CONSTRAINT `menu_item_fk1` FOREIGN KEY (`menu_item_id_FK`) REFERENCES `menu_item` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu_item_test`
--

DROP TABLE IF EXISTS `menu_item_test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_item_test` (
  `lang_code_fk` char(25) NOT NULL,
  `uid_fk` varchar(45) NOT NULL,
  `type_fk` varchar(45) DEFAULT NULL,
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `list` varchar(45) DEFAULT NULL,
  `order` tinyint(80) NOT NULL DEFAULT '0',
  `title` text,
  `prettyuri` varchar(100) DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `auto_generated` varchar(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lang_code_fk`,`uid_fk`),
  UNIQUE KEY `id_test` (`id`),
  UNIQUE KEY `prettyuri_test` (`prettyuri`),
  KEY `type_menu_item_type_fk_test` (`type_fk`),
  KEY `hierarchy_uid_fk_test` (`uid_fk`),
  CONSTRAINT `hierarchy_uid_fk_test` FOREIGN KEY (`uid_fk`) REFERENCES `hierarchy` (`uid`),
  CONSTRAINT `language_lang_code_fk_test` FOREIGN KEY (`lang_code_fk`) REFERENCES `language` (`lang_code`),
  CONSTRAINT `type_menu_item_type_fk_test` FOREIGN KEY (`type_fk`) REFERENCES `menu_item_type` (`type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu_item_type`
--

DROP TABLE IF EXISTS `menu_item_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_item_type` (
  `type` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu_root`
--

DROP TABLE IF EXISTS `menu_root`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_root` (
  `name` varchar(128) NOT NULL,
  `uid_fk` varchar(45) NOT NULL,
  PRIMARY KEY (`name`),
  KEY `nested_set_uid_fk2` (`uid_fk`),
  CONSTRAINT `nested_set_uid_fk2` FOREIGN KEY (`uid_fk`) REFERENCES `hierarchy` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `multipage`
--

DROP TABLE IF EXISTS `multipage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `multipage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_item_id_fk` int(11) unsigned NOT NULL,
  `template` varchar(100) DEFAULT '',
  `editor` varchar(20) DEFAULT '',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `menu_item_id_fk4` (`menu_item_id_fk`),
  CONSTRAINT `menu_item_id_fk4` FOREIGN KEY (`menu_item_id_fk`) REFERENCES `menu_item` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paper`
--

DROP TABLE IF EXISTS `paper`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paper` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu_item_id_fk` int(11) unsigned NOT NULL,
  `list` varchar(45) DEFAULT NULL,
  `headline` text,
  `perex` longtext,
  `template` varchar(100) DEFAULT '',
  `keywords` text,
  `editor` varchar(20) DEFAULT '',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `menu_item_id_fk1` (`menu_item_id_fk`),
  FULLTEXT KEY `searchpaper` (`headline`,`perex`),
  CONSTRAINT `menu_item_id_fk1` FOREIGN KEY (`menu_item_id_fk`) REFERENCES `menu_item` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=714 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paper_section`
--

DROP TABLE IF EXISTS `paper_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paper_section` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `paper_id_fk` int(11) unsigned NOT NULL,
  `list` varchar(45) DEFAULT NULL,
  `content` longtext,
  `template_name` varchar(100) DEFAULT '',
  `template` longtext,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `priority` float unsigned NOT NULL DEFAULT '1',
  `show_time` date DEFAULT NULL,
  `hide_time` date DEFAULT NULL,
  `event_start_time` date DEFAULT NULL,
  `event_end_time` date DEFAULT NULL,
  `editor` varchar(20) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `paper_id_fk2` (`paper_id_fk`),
  FULLTEXT KEY `searchcontent` (`content`),
  CONSTRAINT `paper_id_fk2` FOREIGN KEY (`paper_id_fk`) REFERENCES `paper` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stranky`
--

DROP TABLE IF EXISTS `stranky`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stranky` (
  `list` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `poradi` tinyint(80) unsigned NOT NULL DEFAULT '0',
  `nazev_lan1` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `obsah_lan1` longtext CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `aktiv_lan1` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `aktiv_lan1start` date DEFAULT NULL,
  `aktiv_lan1stop` date DEFAULT NULL,
  `keywords_lan1` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `nazev_lan2` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `obsah_lan2` longtext CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `aktiv_lan2` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `aktiv_lan2start` date DEFAULT NULL,
  `aktiv_lan2stop` date DEFAULT NULL,
  `keywords_lan2` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `nazev_lan3` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `obsah_lan3` longtext CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `aktiv_lan3` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `aktiv_lan3start` date DEFAULT NULL,
  `aktiv_lan3stop` date DEFAULT NULL,
  `keywords_lan3` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `aut_gen` varchar(6) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL DEFAULT '0',
  `editor` varchar(20) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `zmena` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`list`),
  UNIQUE KEY `list` (`list`),
  FULLTEXT KEY `vyhledavani` (`nazev_lan1`,`obsah_lan1`,`nazev_lan2`,`obsah_lan2`,`nazev_lan3`,`obsah_lan3`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stranky_innodb`
--

DROP TABLE IF EXISTS `stranky_innodb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stranky_innodb` (
  `list` varchar(50) NOT NULL,
  `poradi` tinyint(80) NOT NULL DEFAULT '0',
  `nazev_lan1` text,
  `obsah_lan1` longtext,
  `aktiv_lan1` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `aktiv_lan1start` date DEFAULT NULL,
  `aktiv_lan1stop` date DEFAULT NULL,
  `keywords_lan1` text,
  `nazev_lan2` text,
  `obsah_lan2` longtext,
  `aktiv_lan2` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `aktiv_lan2start` date DEFAULT NULL,
  `aktiv_lan2stop` date DEFAULT NULL,
  `keywords_lan2` text,
  `nazev_lan3` text,
  `obsah_lan3` longtext,
  `aktiv_lan3` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `aktiv_lan3start` date DEFAULT NULL,
  `aktiv_lan3stop` date DEFAULT NULL,
  `keywords_lan3` text,
  `aut_gen` varchar(6) NOT NULL DEFAULT '0',
  `editor` varchar(20) DEFAULT NULL,
  `zmena` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`list`),
  UNIQUE KEY `list` (`list`),
  FULLTEXT KEY `vyhledavani` (`nazev_lan1`,`obsah_lan1`,`nazev_lan2`,`obsah_lan2`,`nazev_lan3`,`obsah_lan3`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'gr_upgrade'
--

--
-- Dumping routines for database 'gr_upgrade'
--

--
-- Final view structure for view `fulltree`
--

/*!50001 DROP VIEW IF EXISTS `fulltree`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `fulltree` AS select `node`.`uid` AS `uid`,(count(`parent`.`uid`) - 1) AS `depth`,`node`.`left_node` AS `left_node`,`node`.`right_node` AS `right_node`,`node`.`parent_uid` AS `parent_uid` from (`hierarchy` `node` join `hierarchy` `parent` on((`node`.`left_node` between `parent`.`left_node` and `parent`.`right_node`))) group by `node`.`uid` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `hierarchy_view`
--

/*!50001 DROP VIEW IF EXISTS `hierarchy_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`gr_upgrader`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `hierarchy_view` AS select `hierarchy`.`uid` AS `uid`,`hierarchy`.`left_node` AS `left_node`,`hierarchy`.`right_node` AS `right_node`,`hierarchy`.`parent_uid` AS `parent_uid`,`menu_item`.`lang_code_fk` AS `lang_code`,`menu_item`.`uid_fk` AS `uid_fk`,`menu_item`.`type_fk` AS `type_fk`,`menu_item`.`id` AS `id`,`menu_item`.`list` AS `list`,`menu_item`.`order` AS `order`,`menu_item`.`title` AS `title`,`menu_item`.`active` AS `active`,`menu_item`.`auto_generated` AS `auto_generated` from (`hierarchy` left join `menu_item` on((`hierarchy`.`uid` = `menu_item`.`uid_fk`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-06-12 16:59:53
