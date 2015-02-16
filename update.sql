-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.1.50-community - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Version:             9.1.0.4867
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for cme
USE `cme`;

-- Dumping structure for table cme.bounces
CREATE TABLE IF NOT EXISTS `bounces` (
  `email` varchar(200) NOT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  PRIMARY KEY (`email`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Altering structure for table cme.campaigns
ALTER TABLE `campaigns`
 ADD `filters` TEXT NULL,
 ADD `frequency` INT(11) NULL DEFAULT NULL,
 ADD `tested` INT(11) NOT NULL DEFAULT '0',
 ADD `previewed` INT(11) NOT NULL DEFAULT '0',
 ADD `smtp_provider_id` INT(11) NULL DEFAULT NULL,
 ADD `type` ENUM('default','rolling') NOT NULL DEFAULT 'default',
 MODIFY `html_content` TEXT NULL,
 MODIFY `text_content` TEXT NULL,
 MODIFY `send_time` INT(11) NULL DEFAULT NULL,
 MODIFY `send_priority` INT(11) NOT NULL DEFAULT '0',
 MODIFY `status` ENUM('Pending','Queuing','Queued','Sending','Sent','Paused','Aborted') NOT NULL DEFAULT 'Pending',
 ADD `deleted_at` int(11) DEFAULT NULL;

-- Altering structure for table cme.message_queue
ALTER TABLE `message_queue`
 MODIFY `status` ENUM('Pending','Sent','Failed','Paused') NOT NULL DEFAULT 'Pending';


-- Dumping structure for table cme.smtp_providers
CREATE TABLE IF NOT EXISTS `smtp_providers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '0',
  `host` varchar(250) NOT NULL DEFAULT '0',
  `username` varchar(250) NOT NULL DEFAULT '0',
  `password` varchar(250) NOT NULL DEFAULT '0',
  `port` int(11) NOT NULL DEFAULT '0',
  `default` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table cme.unsubscribes
CREATE TABLE IF NOT EXISTS `unsubscribes` (
  `email` varchar(200) NOT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  `list_id` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  PRIMARY KEY (`email`),
  KEY `brand_id` (`brand_id`),
  KEY `campaign_id` (`campaign_id`),
  KEY `list_id` (`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
