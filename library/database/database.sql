/*
SQLyog Community Edition- MySQL GUI v8.12 
MySQL - 5.0.92-50-log : Database - thirdeye_freedompop
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;



/*Table structure for table `contact` */

DROP TABLE IF EXISTS `contact`;

CREATE TABLE `contact` (
  `contact_id` int(11) NOT NULL auto_increment,
  `email` varchar(128) NOT NULL,
  `first_name` varchar(32) NOT NULL,
  `last_name` varchar(32) NOT NULL,
  `time_created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `owner_id` int(11) default NULL COMMENT 'signed up user is the owner_id ',
  `signup_id` int(11) default NULL COMMENT 'If a contact converts to a signed up user, signup_id is their regsitered id',
  PRIMARY KEY  (`contact_id`),
  KEY `contact_email_idx` (`email`),
  KEY `contact_owner_idx` (`owner_id`,`signup_id`),
  CONSTRAINT `FK_contact` FOREIGN KEY (`owner_id`) REFERENCES `signup` (`signup_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `contact` */

/*Table structure for table `lead` */

DROP TABLE IF EXISTS `lead`;

CREATE TABLE `lead` (
  `lead_id` int(11) NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `emails_sent` int(128) NOT NULL,
  `opt_out` tinyint(1) NOT NULL default '0',
  `signup_id` int(11) NOT NULL,
  `time_created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `time_last_contact` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`lead_id`),
  UNIQUE KEY `lead_email_idx` (`email`),
  KEY `lead_time_idx` (`time_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `lead` */

/*Table structure for table `notifications` */

DROP TABLE IF EXISTS `notifications`;

CREATE TABLE `notifications` (
  `signup_id` int(11) NOT NULL,
  `service` enum('Purchase Receipt','Usage','Termination Alert','Discout Offer','Discout Offer Partner','Service launch') default NULL,
  `status` enum('1','0') default '1',
  KEY `signup` (`signup_id`),
  CONSTRAINT `FK_notifications` FOREIGN KEY (`signup_id`) REFERENCES `signup` (`signup_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `notifications` */

/*Table structure for table `postcode` */

DROP TABLE IF EXISTS `postcode`;

CREATE TABLE `postcode` (
  `zip_code` varchar(200) default NULL,
  `latitude` varchar(200) default NULL,
  `longitude` varchar(200) default NULL,
  `city` varchar(200) default NULL,
  `states` varchar(200) default NULL,
  KEY `city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `postcode` */


/*Table structure for table `signup` */

DROP TABLE IF EXISTS `signup`;

CREATE TABLE `signup` (
  `signup_id` int(11) NOT NULL auto_increment,
  `first_name` varchar(32) NOT NULL,
  `last_name` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` varchar(100) default NULL,
  `email_lower` varchar(128) NOT NULL,
  `postal_code` varchar(12) NOT NULL,
  `time_created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `fpuser_id` int(11) default NULL,
  `opt_out` tinyint(1) NOT NULL default '0',
  `status` enum('1','0') default '1',
  `hear_from` varchar(255) default NULL,
  PRIMARY KEY  (`signup_id`),
  UNIQUE KEY `signup_email_idx` (`email_lower`),
  KEY `signup_time_idx` (`time_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `signup` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;