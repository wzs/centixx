SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


DROP TABLE IF EXISTS `daysofweek`;
CREATE TABLE IF NOT EXISTS `daysofweek` (
  `day` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(45) COLLATE utf8_polish_ci NOT NULL,
  `department_manager` int(11) DEFAULT NULL,
  PRIMARY KEY (`department_id`),
  KEY `department_manager` (`department_manager`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(45) COLLATE utf8_polish_ci NOT NULL,
  `group_project` int(11) DEFAULT NULL,
  `group_manager` int(11) DEFAULT NULL,
  PRIMARY KEY (`group_id`),
  KEY `group_manager` (`group_manager`),
  KEY `group_project` (`group_project`),
  KEY `group_project_2` (`group_project`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_from` int(11) NOT NULL,
  `permission_to` int(11) NOT NULL,
  `permission_type` enum('add_ceo') COLLATE utf8_polish_ci DEFAULT NULL,
  `permission_starts` date NOT NULL,
  `permission_ends` date NOT NULL,
  `permission_count` smallint(6) NOT NULL DEFAULT '1',
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `permission_from_2` (`permission_from`,`permission_to`,`permission_type`,`permission_starts`,`permission_ends`),
  KEY `permission_from` (`permission_from`),
  KEY `permission_to` (`permission_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_name` varchar(45) COLLATE utf8_polish_ci NOT NULL,
  `project_start` date NOT NULL,
  `project_stop` date NOT NULL,
  `project_manager` int(11) DEFAULT NULL,
  `project_department` int(11) DEFAULT NULL,
  PRIMARY KEY (`project_id`),
  KEY `project_manager` (`project_manager`),
  KEY `project_department` (`project_department`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(45) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

DROP TABLE IF EXISTS `timesheets`;
CREATE TABLE IF NOT EXISTS `timesheets` (
  `timesheet_id` int(11) NOT NULL AUTO_INCREMENT,
  `timesheet_user` int(11) NOT NULL,
  `timesheet_project` int(11) NOT NULL,
  `timesheet_hours` decimal(10,0) NOT NULL,
  `timesheet_date` date NOT NULL,
  `timesheet_descr` text COLLATE utf8_polish_ci NOT NULL,
  `timesheet_accepted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`timesheet_id`),
  UNIQUE KEY `unique_user_date` (`timesheet_user`,`timesheet_date`),
  KEY `timesheet_project` (`timesheet_project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_value` decimal(7,2) NOT NULL,
  `transaction_title` varchar(45) COLLATE utf8_polish_ci NOT NULL,
  `transaction_date` datetime NOT NULL,
  `transaction_user` int(11) DEFAULT NULL,
  `transaction_account` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL,
  PRIMARY KEY (`transaction_id`),
  KEY `transaction_user` (`transaction_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(45) COLLATE utf8_polish_ci NOT NULL,
  `user_surname` varchar(45) COLLATE utf8_polish_ci NOT NULL,
  `user_hour_rate` decimal(4,2) NOT NULL,
  `user_account` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `user_role` int(11) DEFAULT NULL,
  `user_group` int(11) DEFAULT NULL,
  `user_email` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `user_password` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `user_project` int(11) DEFAULT NULL,
  `user_address` text COLLATE utf8_polish_ci,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_email` (`user_email`),
  KEY `user_project` (`user_project`),
  KEY `user_group` (`user_group`),
  KEY `user_role` (`user_role`),
  KEY `user_role_2` (`user_role`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

DROP TABLE IF EXISTS `users_roles`;
CREATE TABLE IF NOT EXISTS `users_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`role_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;


ALTER TABLE `departments`
  ADD CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`department_manager`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`group_project`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `groups_ibfk_2` FOREIGN KEY (`group_manager`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`permission_from`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permissions_ibfk_2` FOREIGN KEY (`permission_to`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`project_manager`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`project_department`) REFERENCES `departments` (`department_id`) ON DELETE CASCADE;

ALTER TABLE `timesheets`
  ADD CONSTRAINT `timesheets_ibfk_1` FOREIGN KEY (`timesheet_user`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `timesheets_ibfk_2` FOREIGN KEY (`timesheet_project`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE;

ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`transaction_user`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`user_project`) REFERENCES `projects` (`project_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`user_group`) REFERENCES `groups` (`group_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`user_role`) REFERENCES `roles` (`role_id`);

ALTER TABLE `users_roles`
  ADD CONSTRAINT `users_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
