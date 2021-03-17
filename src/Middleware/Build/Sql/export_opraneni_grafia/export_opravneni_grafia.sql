-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: localhost    Database: gr_pracovni
-- ------------------------------------------------------
-- Server version	5.6.39-log

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
-- Dumping data for table `login`
--

LOCK TABLES `login` WRITE;
/*!40000 ALTER TABLE `login` DISABLE KEYS */;
INSERT INTO `login` VALUES ('administrator'),('alka2209'),('anta2208'),('anv1305'),('cech'),('jab1408'),('jiji3103'),('krba2001'),('mali1112'),('masl0303'),('miko0301'),('mipi1303'),('pes2704'),('qqq'),('stepi1506'),('vewa2802'),('veza1305'),('vlse2610'),('zase1910');
/*!40000 ALTER TABLE `login` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `credentials`
--

LOCK TABLES `credentials` WRITE;
/*!40000 ALTER TABLE `credentials` DISABLE KEYS */;
INSERT INTO `credentials` VALUES ('administrator','admin0560','adm','2021-03-15 00:11:16','2021-03-15 00:11:16'),('alka2209','alka2209','','2021-03-15 00:11:16','2021-03-15 00:11:16'),('anta2208','anna','sup','2021-03-15 00:11:16','2021-03-15 00:11:16'),('anv1305','anv2014','sup','2021-03-15 00:11:16','2021-03-15 00:11:16'),('cech','Lada5678','','2021-03-15 00:11:16','2021-03-15 00:11:16'),('jab1408','Aagata1234','sup','2021-03-15 00:11:16','2021-03-15 00:11:16'),('jiji3103','jiji','','2021-03-15 00:11:16','2021-03-15 00:11:16'),('krba2001','krba','','2021-03-15 00:11:16','2021-03-15 00:11:16'),('mali1112','mali','','2021-03-15 00:11:16','2021-03-15 00:11:16'),('masl0303','masl0303','sup','2021-03-15 00:11:16','2021-03-15 00:11:16'),('miko0301','miko','','2021-03-15 00:11:16','2021-03-15 00:11:16'),('mipi1303','Sasanka','','2021-03-15 00:11:16','2021-03-15 00:11:16'),('pes2704','K0l0bezka5','sup','2021-03-15 00:11:16','2021-03-15 00:11:16'),('stepi1506','piste1506','sup','2021-03-15 00:11:16','2021-03-15 00:11:16'),('vewa2802','vewa','','2021-03-15 00:11:16','2021-03-15 00:11:16'),('veza1305','vera1305','','2021-03-15 00:11:16','2021-03-15 00:11:16'),('vlse2610','Gra71a2610','sup','2021-03-15 00:11:16','2021-03-15 00:11:16'),('zase1910','neta','','2021-03-15 00:11:16','2021-03-15 00:11:16');
/*!40000 ALTER TABLE `credentials` ENABLE KEYS */;
UNLOCK TABLES;



--
-- Dumping events for database 'gr_pracovni'
--

--
-- Dumping routines for database 'gr_pracovni'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-03-16 17:45:59
