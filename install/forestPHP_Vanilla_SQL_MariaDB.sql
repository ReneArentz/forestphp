-- Adminer 4.7.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE `forestphp` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `forestphp`;

DROP TABLE IF EXISTS `sys_fphp_action`;
CREATE TABLE `sys_fphp_action` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `BranchId` int(10) unsigned NOT NULL,
  `Name` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UNIQUE1` (`BranchId`,`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

INSERT INTO `sys_fphp_action` (`Id`, `BranchId`, `Name`) VALUES
(1,	0,	'init');

DROP TABLE IF EXISTS `sys_fphp_branch`;
CREATE TABLE `sys_fphp_branch` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `ParentBranch` int(10) unsigned DEFAULT NULL,
  `Title` varchar(255) NOT NULL,
  `Navigation` tinyint(1) NOT NULL,
  `NavigationOrder` int(10) unsigned NOT NULL,
  `Filename` varchar(255) DEFAULT NULL,
  `Table` varchar(36) DEFAULT NULL,
  `StandardView` smallint(6) DEFAULT NULL,
  `Filter` bit(1) DEFAULT NULL,
  `KeepFilter` bit(1) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UNIQUE1` (`Name`,`ParentBranch`,`NavigationOrder`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;

INSERT INTO `sys_fphp_branch` (`Id`, `Name`, `ParentBranch`, `Title`, `Navigation`, `NavigationOrder`, `Filename`, `Table`, `StandardView`, `Filter`, `KeepFilter`) VALUES
(1,	'index',	NULL,	'Home',	1,	1,	NULL,	NULL,	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `sys_fphp_session`;
CREATE TABLE `sys_fphp_session` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UUID` varchar(36) NOT NULL,
  `Timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UNIQUE1` (`UUID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- 2019-07-11 07:01:23
