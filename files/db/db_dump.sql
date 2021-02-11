-- --------------------------------------------------------
-- Hostitel:                     127.0.0.1
-- Verze serveru:                10.4.10-MariaDB - mariadb.org binary distribution
-- OS serveru:                   Win64
-- HeidiSQL Verze:               11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Exportování struktury databáze pro
DROP DATABASE IF EXISTS `web_media_survey`;
CREATE DATABASE IF NOT EXISTS `web_media_survey` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `web_media_survey`;

-- Exportování struktury pro tabulka web_media_survey.survey
DROP TABLE IF EXISTS `survey`;
CREATE TABLE IF NOT EXISTS `survey` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Exportování dat pro tabulku web_media_survey.survey: 0 rows
DELETE FROM `survey`;
/*!40000 ALTER TABLE `survey` DISABLE KEYS */;
/*!40000 ALTER TABLE `survey` ENABLE KEYS */;

-- Exportování struktury pro tabulka web_media_survey.survey_choice
DROP TABLE IF EXISTS `survey_choice`;
CREATE TABLE IF NOT EXISTS `survey_choice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` int(10) unsigned DEFAULT NULL,
  `choice` varchar(512) NOT NULL,
  `count` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `survey_id` (`survey_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Exportování dat pro tabulku web_media_survey.survey_choice: 0 rows
DELETE FROM `survey_choice`;
/*!40000 ALTER TABLE `survey_choice` DISABLE KEYS */;
/*!40000 ALTER TABLE `survey_choice` ENABLE KEYS */;

-- Exportování struktury pro tabulka web_media_survey.survey_user_tree
DROP TABLE IF EXISTS `survey_user_tree`;
CREATE TABLE IF NOT EXISTS `survey_user_tree` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `survey_choice_id` int(10) unsigned NOT NULL,
  `date_insert` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `survey_id_user_id` (`survey_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Exportování dat pro tabulku web_media_survey.survey_user_tree: 0 rows
DELETE FROM `survey_user_tree`;
/*!40000 ALTER TABLE `survey_user_tree` DISABLE KEYS */;
/*!40000 ALTER TABLE `survey_user_tree` ENABLE KEYS */;

-- Exportování struktury pro tabulka web_media_survey.user
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(40) NOT NULL,
  `hash` varchar(32) NOT NULL,
  `other` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip_hash` (`ip`,`hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Exportování dat pro tabulku web_media_survey.user: 0 rows
DELETE FROM `user`;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
