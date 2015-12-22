/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры базы данных smstools
DROP DATABASE IF EXISTS `smstools`;
CREATE DATABASE IF NOT EXISTS `smstools` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `smstools`;


-- Дамп структуры для таблица smstools.sms
DROP TABLE IF EXISTS `sms`;
CREATE TABLE IF NOT EXISTS `sms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `phonenumber` varchar(32) NOT NULL DEFAULT '',
  `tstamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dt` datetime DEFAULT NULL,
  `full_msg` text,
  `msg` text,
  `result` varchar(64) DEFAULT NULL,
  `int_id` int(11) DEFAULT NULL,
  `direction` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1-outgoing, 0-incoming',
  `process` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'is processed',
  `method` enum('gsm','smpp') NOT NULL DEFAULT 'gsm',
  PRIMARY KEY (`id`),
  KEY `phonenumber` (`phonenumber`),
  KEY `method` (`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Дамп структуры для таблица smstools.sms_rights
DROP TABLE IF EXISTS `sms_rights`;
CREATE TABLE IF NOT EXISTS `sms_rights` (
  `right` varchar(64) NOT NULL,
  `descr` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`right`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы smstools.sms_rights: ~4 rows (приблизительно)
/*!40000 ALTER TABLE `sms_rights` DISABLE KEYS */;
INSERT INTO `sms_rights` (`right`, `descr`) VALUES
	('SMS_ACCESS', 'Доступ к веб-интерфейсу SMS'),
	('SMS_ADMIN', 'Доступ к интерфейсу управления'),
	('SMS_APISEND', 'Доступ к отправке SMS через API'),
	('SMS_WEBSEND', 'Доступ к отправке SMS через веб');
/*!40000 ALTER TABLE `sms_rights` ENABLE KEYS */;


-- Дамп структуры для таблица smstools.sms_users
DROP TABLE IF EXISTS `sms_users`;
CREATE TABLE IF NOT EXISTS `sms_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(64) DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  `ip` varchar(256) DEFAULT NULL,
  `interface` enum('api','web') NOT NULL DEFAULT 'web',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы smstools.sms_users: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `sms_users` DISABLE KEYS */;
INSERT INTO `sms_users` (`id`, `login`, `password`, `ip`, `interface`) VALUES
	(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '*', 'web');
/*!40000 ALTER TABLE `sms_users` ENABLE KEYS */;


-- Дамп структуры для таблица smstools.sms_users_log
DROP TABLE IF EXISTS `sms_users_log`;
CREATE TABLE IF NOT EXISTS `sms_users_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `ip` varchar(128) DEFAULT NULL,
  `tstamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `descr` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Дамп структуры для таблица smstools.sms_users_rights
DROP TABLE IF EXISTS `sms_users_rights`;
CREATE TABLE IF NOT EXISTS `sms_users_rights` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `right` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `right` (`right`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы smstools.sms_users_rights: ~4 rows (приблизительно)
/*!40000 ALTER TABLE `sms_users_rights` DISABLE KEYS */;
INSERT INTO `sms_users_rights` (`id`, `user_id`, `right`) VALUES
	(16, 1, 'SMS_ACCESS'),
	(17, 1, 'SMS_WEBSEND'),
	(18, 1, 'SMS_ADMIN');
/*!40000 ALTER TABLE `sms_users_rights` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
