-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.1.50-community - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Version:             8.3.0.4843
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for cme
DROP DATABASE IF EXISTS `cme`;
CREATE DATABASE IF NOT EXISTS `cme` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `cme`;


-- Dumping structure for table cme.brands
DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(225) NOT NULL DEFAULT '0',
  `brand_sender_email` varchar(225) NOT NULL DEFAULT '0',
  `brand_sender_name` varchar(225) NOT NULL DEFAULT '0',
  `brand_website_url` varchar(225) NOT NULL DEFAULT '0',
  `brand_domain_name` varchar(225) NOT NULL DEFAULT '0',
  `brand_unsubscribe_url` varchar(225) NOT NULL DEFAULT '0',
  `brand_logo` varchar(225) NOT NULL DEFAULT '0',
  `brand_created` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table cme.campaigns
DROP TABLE IF EXISTS `campaigns`;
CREATE TABLE IF NOT EXISTS `campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(500) NOT NULL,
  `from` varchar(225) NOT NULL,
  `html_content` text NOT NULL,
  `text_content` text,
  `list_id` int(11) NOT NULL DEFAULT '0',
  `brand_id` int(11) NOT NULL DEFAULT '0',
  `send_time` int(11) NOT NULL,
  `send_priority` int(11) NOT NULL DEFAULT '0',
  `status` enum('Pending','Queuing','Queued','Sent') NOT NULL DEFAULT 'Pending',
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table cme.campaign_events
DROP TABLE IF EXISTS `campaign_events`;
CREATE TABLE IF NOT EXISTS `campaign_events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) DEFAULT '0',
  `list_id` int(11) DEFAULT '0',
  `subscriber_id` int(11) DEFAULT '0',
  `event_type` enum('failed','sent','opened','bounced','unsubscribed','clicked') DEFAULT NULL,
  `reference` varchar(500) DEFAULT NULL,
  `time` int(11) DEFAULT '0',
  PRIMARY KEY (`event_id`),
  KEY `campaign_id` (`campaign_id`),
  KEY `list_id` (`list_id`),
  KEY `subscriber_id` (`subscriber_id`),
  KEY `event_type` (`event_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table cme.campaign_queue
DROP TABLE IF EXISTS `campaign_queue`;
CREATE TABLE IF NOT EXISTS `campaign_queue` (
  `id` int(11) DEFAULT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `locked_by` varchar(225) DEFAULT NULL,
  `processed` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Campaign send queue, when a campaign is sent, it queued here';

-- Data exporting was unselected.


-- Dumping structure for table cme.import_queue
DROP TABLE IF EXISTS `import_queue`;
CREATE TABLE IF NOT EXISTS `import_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL DEFAULT '0',
  `type` enum('api','csv','file') NOT NULL,
  `source` varchar(225) DEFAULT NULL,
  `locked_by` varchar(225) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table cme.lists
DROP TABLE IF EXISTS `lists`;
CREATE TABLE IF NOT EXISTS `lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(225) NOT NULL DEFAULT '0',
  `description` varchar(225) NOT NULL DEFAULT '0',
  `endpoint` varchar(225) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table cme.message_queue
DROP TABLE IF EXISTS `message_queue`;
CREATE TABLE IF NOT EXISTS `message_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(500) NOT NULL,
  `from` varchar(225) NOT NULL,
  `to` varchar(225) DEFAULT NULL,
  `html_content` text NOT NULL,
  `text_content` text NOT NULL,
  `subscriber_id` int(11) NOT NULL DEFAULT '0',
  `list_id` int(11) NOT NULL DEFAULT '0',
  `brand_id` int(11) NOT NULL DEFAULT '0',
  `campaign_id` int(11) NOT NULL DEFAULT '0',
  `status` enum('Pending','Sent','Failed') NOT NULL DEFAULT 'Pending',
  `send_time` int(11) DEFAULT NULL,
  `send_priority` int(11) DEFAULT NULL,
  `locked_by` varchar(225) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`),
  KEY `brand_id` (`brand_id`),
  KEY `list_id` (`list_id`),
  KEY `status` (`status`),
  KEY `locked_by` (`locked_by`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Data exporting was unselected.


-- Dumping structure for table cme.ranges
DROP TABLE IF EXISTS `ranges`;
CREATE TABLE IF NOT EXISTS `ranges` (
  `list_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `start` int(11) NOT NULL,
  `end` int(11) DEFAULT NULL,
  `locked_by` varchar(225) DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  PRIMARY KEY (`list_id`,`campaign_id`,`start`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table cme.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `role` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
