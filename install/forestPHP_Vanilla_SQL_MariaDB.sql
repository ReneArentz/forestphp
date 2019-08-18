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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

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
(1,	'index',	NULL,	'Home',	1,	1,	NULL,	NULL,	NULL,	NULL,	NULL),
(29,	'forestphp',	1,	'forestPHP',	1,	15,	NULL,	NULL,	1,	CONV('0', 2, 10) + 0,	CONV('0', 2, 10) + 0),
(31,	'settings',	29,	'Settings',	1,	2,	NULL,	NULL,	1,	CONV('0', 2, 10) + 0,	CONV('0', 2, 10) + 0),
(32,	'useradmin',	29,	'User Administration',	1,	3,	NULL,	NULL,	1,	CONV('0', 2, 10) + 0,	CONV('0', 2, 10) + 0),
(37,	'trunk',	31,	'Trunk',	1,	2,	NULL,	'0afdb7e6-4721-11e9-8210-1062e50d1fcb',	10,	CONV('0', 2, 10) + 0,	CONV('0', 2, 10) + 0),
(39,	'session',	32,	'Sessions',	1,	1,	NULL,	'0afad5e0-4721-11e9-8210-1062e50d1fcb',	1,	CONV('1', 2, 10) + 0,	CONV('0', 2, 10) + 0);

DROP TABLE IF EXISTS `sys_fphp_forestdata`;
CREATE TABLE `sys_fphp_forestdata` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UUID` varchar(36) NOT NULL,
  `Name` varchar(255) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `uuid` (`UUID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

INSERT INTO `sys_fphp_forestdata` (`Id`, `UUID`, `Name`) VALUES
(1,	'1098ab89-4717-11e9-8210-1062e50d1fcb',	'forestString'),
(2,	'13d65b83-4717-11e9-8210-1062e50d1fcb',	'forestList'),
(3,	'169479d0-4717-11e9-8210-1062e50d1fcb',	'forestNumericString'),
(4,	'18d92e02-4717-11e9-8210-1062e50d1fcb',	'forestInt'),
(5,	'1b9785a9-4717-11e9-8210-1062e50d1fcb',	'forestFloat'),
(6,	'1e7bae52-4717-11e9-8210-1062e50d1fcb',	'forestBool'),
(7,	'22b91b86-4717-11e9-8210-1062e50d1fcb',	'forestArray'),
(8,	'266a1078-4717-11e9-8210-1062e50d1fcb',	'forestObject'),
(9,	'3e0f992f-4717-11e9-8210-1062e50d1fcb',	'forestObject(\'forestDateTime\')'),
(10,	'45e9023f-4717-11e9-8210-1062e50d1fcb',	'forestNumericString(1)');

DROP TABLE IF EXISTS `sys_fphp_formelement`;
CREATE TABLE `sys_fphp_formelement` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UUID` varchar(36) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `JSONEncodedSettings` text,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `uuid` (`UUID`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

INSERT INTO `sys_fphp_formelement` (`Id`, `UUID`, `Name`, `JSONEncodedSettings`) VALUES
(1,	'5d044167-4717-11e9-8210-1062e50d1fcb',	'form',	'{\n						\"FormTabConfiguration\": {\n							\"Tab\" : true,\n							\"TabMenuClass\" : \"nav nav-tabs\",\n							\"TabActiveClass\" : \"active\",\n							\"TabToggle\" : \"tab\",\n							\"TabContentClass\" : \"tab-content\",\n							\"TabFooterClass\" : \"tab-footer\",\n							\"TabElementClass\" : \"tab-pane fade\",\n							\"TabElementActiveClass\" : \"tab-pane fade in active\",\n							\"TabsInfo\" : [\n								{\"TabId\" : \"general\", \"TabTitle\" : \"#001.formTabTitle#\"}\n							]\n						},\n						\n						\"FormModalConfiguration\" : {\n							\"Modal\" : true,\n							\"ModalClass\" : \"modal fade\",\n							\"ModalId\" : \"fphp_Modal\",\n							\"ModalTitle\" : \"forestPHP Modal Form\",\n							\"ModalRole\" : \"dialog\",\n							\"ModalDialogClass\" : \"modal-dialog modal-xl\",\n							\"ModalDialogContentClass\" : \"modal-content\",\n							\"ModalHeaderClass\" : \"modal-header\",\n							\"ModalHeaderCloseClass\" : \"close\",\n							\"ModalHeaderDismissClass\" : \"modal\",\n							\"ModalHeaderCloseContent\" : \"×\",\n							\"ModalBodyClass\" : \"modal-body\",\n							\"ModalFooterClass\" : \"modal-footer\"\n						},\n						\n						\"Class\" : \"form-horizontal\",\n						\"FormGroupClass\" : \"form-group\",\n						\"LabelClass\" : \"col-sm-3 control-label\",\n						\"FormElementClass\" : \"col-sm-9\",\n						\"ClassAll\" : \"form-control\",\n						\"RadioClass\" : \"radio\",\n						\"CheckboxClass\" : \"checkbox\",\n						\"UseCaptcha\" : false\n					}'),
(2,	'ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'text',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false\r\n			}'),
(3,	'ac1cf0c5-4717-11e9-8210-1062e50d1fcb',	'list',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false\r\n			}'),
(4,	'ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'hidden',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false\r\n			}'),
(5,	'ac1dd12e-4717-11e9-8210-1062e50d1fcb',	'password',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false\r\n			}'),
(7,	'ac1e7f1c-4717-11e9-8210-1062e50d1fcb',	'radio',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false,\r\n				\r\n				\"Break\" : true,\r\n				\"RadioClass\" : \"NULL\"\r\n			}'),
(8,	'ac1ed318-4717-11e9-8210-1062e50d1fcb',	'checkbox',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false,\r\n				\r\n				\"Break\" : true,\r\n				\"CheckboxClass\" : \"NULL\"\r\n			}'),
(9,	'ac1f3289-4717-11e9-8210-1062e50d1fcb',	'color',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false\r\n			}'),
(10,	'ac1f8478-4717-11e9-8210-1062e50d1fcb',	'email',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false\r\n			}'),
(11,	'ac1fe7b5-4717-11e9-8210-1062e50d1fcb',	'url',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false\r\n			}'),
(12,	'ac20497d-4717-11e9-8210-1062e50d1fcb',	'date',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false\r\n			}'),
(13,	'ac20a373-4717-11e9-8210-1062e50d1fcb',	'datetime-local',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false\r\n			}'),
(14,	'ac20f552-4717-11e9-8210-1062e50d1fcb',	'month',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false\r\n			}'),
(15,	'ac21769b-4717-11e9-8210-1062e50d1fcb',	'number',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false\r\n			}'),
(16,	'ac21cc6a-4717-11e9-8210-1062e50d1fcb',	'range',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false\r\n			}'),
(17,	'ac2220ee-4717-11e9-8210-1062e50d1fcb',	'search',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false\r\n			}'),
(18,	'ac226e0e-4717-11e9-8210-1062e50d1fcb',	'phone',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false\r\n			}'),
(19,	'ac22bdc3-4717-11e9-8210-1062e50d1fcb',	'time',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false\r\n			}'),
(20,	'ac230060-4717-11e9-8210-1062e50d1fcb',	'week',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false\r\n			}'),
(21,	'ac234a4c-4717-11e9-8210-1062e50d1fcb',	'textarea',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Rows\" : 0,\r\n				\"Cols\" : 0,\r\n				\"Dirname\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Wrap\" : false\r\n			}'),
(22,	'ac239d7f-4717-11e9-8210-1062e50d1fcb',	'select',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Multiple : false,	\r\n				\"Options : {},\r\n				\"Size : 1,\r\n				\"Data : \"NULL\"\r\n			}'),
(25,	'ac24a5bd-4717-11e9-8210-1062e50d1fcb',	'description',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\"\r\n			}'),
(26,	'ac24f45b-4717-11e9-8210-1062e50d1fcb',	'button',	'{\r\n				\"FormGroupClass\" : \"NULL\",\r\n				\"Label\" : \"NULL\",\r\n				\"LabelClass\" : \"NULL\",\r\n				\"LabelFor\" : \"NULL\",\r\n				\"FormElementClass\" : \"NULL\",\r\n				\r\n				\"Class\" : \"NULL\",\r\n				\"Description\" : \"NULL\",\r\n				\"DescriptionClass\" : \"NULL\",\r\n				\"Disabled\" : false,\r\n				\"Id\" : \"NULL\",\r\n				\"Name\" : \"NULL\",\r\n				\"AutoFocus\" : false,\r\n				\"Required\" : false,\r\n				\"Style\" : \"NULL\",\r\n				\"Value\" : \"NULL\",\r\n				\"ValMessage\" : \"NULL\",\r\n				\r\n				\"Accept\" : \"NULL\",\r\n				\"AutoComplete\" : true,\r\n				\"Capture\" : \"NULL\",\r\n				\"Dirname\" : \"NULL\",\r\n				\"List\" : \"NULL\",\r\n				\"Max\" : \"NULL\",\r\n				\"Min\" : \"NULL\",\r\n				\"Multiple\" : false,\r\n				\"Options\" : {},\r\n				\"Pattern\" : \"NULL\",\r\n				\"PatternTitle\" : \"NULL\",\r\n				\"Placeholder\" : \"NULL\",\r\n				\"Readonly\" : false,\r\n				\"Size\" : 0,\r\n				\"Step\" : 0,\r\n				\r\n				\"Form\" : \"NULL\",\r\n				\"FormAction\" : \"NULL\",\r\n				\"FormEnctype\" : \"NULL\",\r\n				\"FormMethod\" : \"NULL\",\r\n				\"FormTarget\" : \"NULL\",\r\n				\"FormNoValidate\" : false,\r\n				\r\n				\"Type\" : \"NULL\",\r\n				\"Data\" : \"NULL\",\r\n				\"ButtonText\" : \"NULL\",\r\n				\"NoFormGroup\" : \"NULL\",\r\n				\"WrapSpanClass\" : \"NULL\"\r\n			}'),
(27,	'f2b8a6a0-495b-11e9-a835-1062e50d1fcb',	'submit',	'{\r\n	\"Class\" : \"btn btn-success btn-default pull-right\",\r\n	\"Id\" : \"sys_fphp_SubmitStandard\",\r\n	\"ButtonText\" : \"<span class=\\\"glyphicon glyphicon-ok\\\"></span> #001.formSubmit#\",\r\n	\"NoFormGroup\" : true\r\n}'),
(28,	'f87dc690-495b-11e9-a835-1062e50d1fcb',	'cancel',	'{\r\n	\"Class\" : \"btn btn-danger btn-default pull-right\",\r\n	\"Id\" : \"sys_fphp_CancelStandard\",\r\n	\"Style\" : \"margin-left: 10px;\",\r\n	\"Data\" : \"dismiss=\\\"modal\\\"\",\r\n	\"ButtonText\" : \"<span class=\\\"glyphicon glyphicon-remove\\\"></span> #001.formCancel#\",\r\n	\"NoFormGroup\" : true\r\n}'),
(29,	'8f08df6d-4a31-11e9-9584-1062e50d1fcb',	'yes',	'{\r\n	\"Class\" : \"btn btn-success btn-default pull-right\",\r\n	\"Id\" : \"sys_fphp_SubmitStandard\",\r\n	\"ButtonText\" : \"<span class=\\\"glyphicon glyphicon-ok\\\"></span> #001.formYes#\",\r\n	\"NoFormGroup\" : true\r\n}'),
(30,	'999d4c71-4a31-11e9-9584-1062e50d1fcb',	'no',	'{\r\n	\"Class\" : \"btn btn-danger btn-default pull-right\",\r\n	\"Id\" : \"sys_fphp_CancelStandard\",\r\n	\"Style\" : \"margin-left: 10px;\",\r\n	\"Data\" : \"dismiss=\\\"modal\\\"\",\r\n	\"ButtonText\" : \"<span class=\\\"glyphicon glyphicon-remove\\\"></span> #001.formNo#\",\r\n	\"NoFormGroup\" : true\r\n}');

DROP TABLE IF EXISTS `sys_fphp_formelement_forestdata`;
CREATE TABLE `sys_fphp_formelement_forestdata` (
  `formelementUUID` varchar(36) NOT NULL,
  `forestdataUUID` varchar(36) NOT NULL,
  PRIMARY KEY (`formelementUUID`,`forestdataUUID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sys_fphp_formelement_forestdata` (`formelementUUID`, `forestdataUUID`) VALUES
('ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb'),
('ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'169479d0-4717-11e9-8210-1062e50d1fcb'),
('ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'18d92e02-4717-11e9-8210-1062e50d1fcb'),
('ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'1b9785a9-4717-11e9-8210-1062e50d1fcb'),
('ac1cf0c5-4717-11e9-8210-1062e50d1fcb',	'13d65b83-4717-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'13d65b83-4717-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'169479d0-4717-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'18d92e02-4717-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'1b9785a9-4717-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'1e7bae52-4717-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'22b91b86-4717-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'266a1078-4717-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'3e0f992f-4717-11e9-8210-1062e50d1fcb'),
('ac1dd12e-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb'),
('ac1e7f1c-4717-11e9-8210-1062e50d1fcb',	'18d92e02-4717-11e9-8210-1062e50d1fcb'),
('ac1ed318-4717-11e9-8210-1062e50d1fcb',	'18d92e02-4717-11e9-8210-1062e50d1fcb'),
('ac1ed318-4717-11e9-8210-1062e50d1fcb',	'1e7bae52-4717-11e9-8210-1062e50d1fcb'),
('ac1f3289-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb'),
('ac1f8478-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb'),
('ac1fe7b5-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb'),
('ac20497d-4717-11e9-8210-1062e50d1fcb',	'3e0f992f-4717-11e9-8210-1062e50d1fcb'),
('ac20a373-4717-11e9-8210-1062e50d1fcb',	'3e0f992f-4717-11e9-8210-1062e50d1fcb'),
('ac20f552-4717-11e9-8210-1062e50d1fcb',	'3e0f992f-4717-11e9-8210-1062e50d1fcb'),
('ac21769b-4717-11e9-8210-1062e50d1fcb',	'18d92e02-4717-11e9-8210-1062e50d1fcb'),
('ac21cc6a-4717-11e9-8210-1062e50d1fcb',	'18d92e02-4717-11e9-8210-1062e50d1fcb'),
('ac2220ee-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb'),
('ac2220ee-4717-11e9-8210-1062e50d1fcb',	'169479d0-4717-11e9-8210-1062e50d1fcb'),
('ac2220ee-4717-11e9-8210-1062e50d1fcb',	'18d92e02-4717-11e9-8210-1062e50d1fcb'),
('ac2220ee-4717-11e9-8210-1062e50d1fcb',	'1b9785a9-4717-11e9-8210-1062e50d1fcb'),
('ac226e0e-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb'),
('ac22bdc3-4717-11e9-8210-1062e50d1fcb',	'3e0f992f-4717-11e9-8210-1062e50d1fcb'),
('ac230060-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb'),
('ac234a4c-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb'),
('ac239d7f-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb');

DROP TABLE IF EXISTS `sys_fphp_formelement_sqltype`;
CREATE TABLE `sys_fphp_formelement_sqltype` (
  `formelementUUID` varchar(36) NOT NULL,
  `sqltypeUUID` varchar(36) NOT NULL,
  PRIMARY KEY (`formelementUUID`,`sqltypeUUID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sys_fphp_formelement_sqltype` (`formelementUUID`, `sqltypeUUID`) VALUES
('ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'159229d5-4718-11e9-8210-1062e50d1fcb'),
('ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'1592a0e1-4718-11e9-8210-1062e50d1fcb'),
('ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'15931a91-4718-11e9-8210-1062e50d1fcb'),
('ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'15940ac2-4718-11e9-8210-1062e50d1fcb'),
('ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'159449b4-4718-11e9-8210-1062e50d1fcb'),
('ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'159489e2-4718-11e9-8210-1062e50d1fcb'),
('ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb'),
('ac1cf0c5-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'1591cb35-4718-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'159229d5-4718-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'1592a0e1-4718-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'15931a91-4718-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'15937c51-4718-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'1593ce61-4718-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'15940ac2-4718-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'159449b4-4718-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'159489e2-4718-11e9-8210-1062e50d1fcb'),
('ac1d5e17-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb'),
('ac1dd12e-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb'),
('ac1e7f1c-4717-11e9-8210-1062e50d1fcb',	'159229d5-4718-11e9-8210-1062e50d1fcb'),
('ac1e7f1c-4717-11e9-8210-1062e50d1fcb',	'1592a0e1-4718-11e9-8210-1062e50d1fcb'),
('ac1e7f1c-4717-11e9-8210-1062e50d1fcb',	'15931a91-4718-11e9-8210-1062e50d1fcb'),
('ac1ed318-4717-11e9-8210-1062e50d1fcb',	'159229d5-4718-11e9-8210-1062e50d1fcb'),
('ac1ed318-4717-11e9-8210-1062e50d1fcb',	'1592a0e1-4718-11e9-8210-1062e50d1fcb'),
('ac1ed318-4717-11e9-8210-1062e50d1fcb',	'15931a91-4718-11e9-8210-1062e50d1fcb'),
('ac1ed318-4717-11e9-8210-1062e50d1fcb',	'159489e2-4718-11e9-8210-1062e50d1fcb'),
('ac1f3289-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb'),
('ac1f8478-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb'),
('ac1fe7b5-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb'),
('ac20497d-4717-11e9-8210-1062e50d1fcb',	'15937c51-4718-11e9-8210-1062e50d1fcb'),
('ac20a373-4717-11e9-8210-1062e50d1fcb',	'15937c51-4718-11e9-8210-1062e50d1fcb'),
('ac20f552-4717-11e9-8210-1062e50d1fcb',	'15937c51-4718-11e9-8210-1062e50d1fcb'),
('ac21769b-4717-11e9-8210-1062e50d1fcb',	'159229d5-4718-11e9-8210-1062e50d1fcb'),
('ac21769b-4717-11e9-8210-1062e50d1fcb',	'1592a0e1-4718-11e9-8210-1062e50d1fcb'),
('ac21769b-4717-11e9-8210-1062e50d1fcb',	'15931a91-4718-11e9-8210-1062e50d1fcb'),
('ac21cc6a-4717-11e9-8210-1062e50d1fcb',	'159229d5-4718-11e9-8210-1062e50d1fcb'),
('ac21cc6a-4717-11e9-8210-1062e50d1fcb',	'1592a0e1-4718-11e9-8210-1062e50d1fcb'),
('ac21cc6a-4717-11e9-8210-1062e50d1fcb',	'15931a91-4718-11e9-8210-1062e50d1fcb'),
('ac2220ee-4717-11e9-8210-1062e50d1fcb',	'15940ac2-4718-11e9-8210-1062e50d1fcb'),
('ac2220ee-4717-11e9-8210-1062e50d1fcb',	'159449b4-4718-11e9-8210-1062e50d1fcb'),
('ac2220ee-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb'),
('ac226e0e-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb'),
('ac22bdc3-4717-11e9-8210-1062e50d1fcb',	'1593ce61-4718-11e9-8210-1062e50d1fcb'),
('ac230060-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb'),
('ac234a4c-4717-11e9-8210-1062e50d1fcb',	'1591cb35-4718-11e9-8210-1062e50d1fcb'),
('ac239d7f-4717-11e9-8210-1062e50d1fcb',	'159229d5-4718-11e9-8210-1062e50d1fcb'),
('ac239d7f-4717-11e9-8210-1062e50d1fcb',	'1592a0e1-4718-11e9-8210-1062e50d1fcb'),
('ac239d7f-4717-11e9-8210-1062e50d1fcb',	'15931a91-4718-11e9-8210-1062e50d1fcb'),
('ac239d7f-4717-11e9-8210-1062e50d1fcb',	'159489e2-4718-11e9-8210-1062e50d1fcb'),
('ac239d7f-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb');

DROP TABLE IF EXISTS `sys_fphp_language`;
CREATE TABLE `sys_fphp_language` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UUID` varchar(36) NOT NULL,
  `Code` varchar(8) NOT NULL,
  `Language` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UNQIUE_UUID` (`UUID`),
  UNIQUE KEY `UNQIUE_Code_Language` (`Code`,`Language`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO `sys_fphp_language` (`Id`, `UUID`, `Code`, `Language`) VALUES
(1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'de-DE',	'Deutsch, Deutschland'),
(2,	'942b5547-6cd9-11e9-b874-1062e50d1fcb',	'en-US',	'English, United States'),
(3,	'966996d3-6cd9-11e9-b874-1062e50d1fcb',	'en-GB',	'English, Großbritannien');

DROP TABLE IF EXISTS `sys_fphp_session`;
CREATE TABLE `sys_fphp_session` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UUID` varchar(36) NOT NULL,
  `Timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UNIQUE1` (`UUID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `sys_fphp_sqltype`;
CREATE TABLE `sys_fphp_sqltype` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UUID` varchar(36) NOT NULL,
  `Name` varchar(255) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `uuid` (`UUID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

INSERT INTO `sys_fphp_sqltype` (`Id`, `UUID`, `Name`) VALUES
(1,	'd8796ff4-4717-11e9-8210-1062e50d1fcb',	'text [255]'),
(2,	'1591cb35-4718-11e9-8210-1062e50d1fcb',	'text'),
(3,	'159229d5-4718-11e9-8210-1062e50d1fcb',	'integer [small]'),
(4,	'1592a0e1-4718-11e9-8210-1062e50d1fcb',	'integer [int]'),
(5,	'15931a91-4718-11e9-8210-1062e50d1fcb',	'integer [big]'),
(6,	'15937c51-4718-11e9-8210-1062e50d1fcb',	'datetime'),
(7,	'1593ce61-4718-11e9-8210-1062e50d1fcb',	'time'),
(8,	'15940ac2-4718-11e9-8210-1062e50d1fcb',	'double'),
(9,	'159449b4-4718-11e9-8210-1062e50d1fcb',	'decimal'),
(10,	'159489e2-4718-11e9-8210-1062e50d1fcb',	'bool');

DROP TABLE IF EXISTS `sys_fphp_systemmessage`;
CREATE TABLE `sys_fphp_systemmessage` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UUID` varchar(36) NOT NULL,
  `IdInternal` int(10) unsigned NOT NULL,
  `LanguageCode` varchar(36) NOT NULL,
  `Message` varchar(256) DEFAULT NULL,
  `Type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UNIQUE1` (`IdInternal`,`LanguageCode`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=latin1;

INSERT INTO `sys_fphp_systemmessage` (`Id`, `UUID`, `IdInternal`, `LanguageCode`, `Message`, `Type`) VALUES
(2,	'bf35b8f1-7312-11e9-be59-1062e50d1fcb',	268436992,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Session nicht gefunden oder ungültig. Bitte melden Sie sich erneut an.',	'error'),
(3,	'bf35b9ab-7312-11e9-be59-1062e50d1fcb',	268438528,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Ungültiger Wert um alle Spalten zu durchsuchen. Bitte benutzen Sie nicht folgende Zeichen im Suchbegriff: [%0].',	'info'),
(4,	'bf35b9fc-7312-11e9-be59-1062e50d1fcb',	268440576,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Es konnten keine Datensätze gefunden werden.',	'info'),
(5,	'bf35ba3e-7312-11e9-be59-1062e50d1fcb',	268440577,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Datensatz konnte nicht gefunden werden. [%0]',	'warning'),
(6,	'bf35ba7a-7312-11e9-be59-1062e50d1fcb',	268440578,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Der Datensatz konnte nicht hinzugefügt werden.',	'warning'),
(7,	'bf35bab6-7312-11e9-be59-1062e50d1fcb',	268440579,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Datensatz konnte wegen eines Duplikats nicht hinzugefügt werden. [%0]',	'warning'),
(8,	'bf35baf4-7312-11e9-be59-1062e50d1fcb',	268440580,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Der Datensatz wurde erfolgreich hinzugefügt.',	'info'),
(9,	'bf35bb2d-7312-11e9-be59-1062e50d1fcb',	268440581,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Datensatz konnte wegen eines Duplikats nicht geändert werden. [%0]',	'warning'),
(10,	'bf35bb71-7312-11e9-be59-1062e50d1fcb',	268440582,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Keine Änderungen angegeben. Der Datensatz wurde nicht geändert.',	'info'),
(11,	'bf35bbd9-7312-11e9-be59-1062e50d1fcb',	268440583,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Der Datensatz wurde erfolgreich geändert.',	'info'),
(43,	'bf35c3bf-7312-11e9-be59-1062e50d1fcb',	268440615,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Ein Datensatz wurde erfolgreich gelöscht.',	'info'),
(44,	'bf35c3f7-7312-11e9-be59-1062e50d1fcb',	268440616,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Die Datensätze wurden erfolgreich gelöscht.',	'info');

DROP TABLE IF EXISTS `sys_fphp_table`;
CREATE TABLE `sys_fphp_table` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UUID` varchar(36) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Unique` varchar(2048) DEFAULT NULL,
  `SortOrder` varchar(2048) DEFAULT NULL,
  `Interval` smallint(6) DEFAULT NULL,
  `View` varchar(2048) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `uuid` (`UUID`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

INSERT INTO `sys_fphp_table` (`Id`, `UUID`, `Name`, `Unique`, `SortOrder`, `Interval`, `View`) VALUES
(3,	'0af5dace-4721-11e9-8210-1062e50d1fcb',	'sys_fphp_action',	NULL,	NULL,	50,	NULL),
(4,	'0af64123-4721-11e9-8210-1062e50d1fcb',	'sys_fphp_branch',	NULL,	NULL,	NULL,	NULL),
(6,	'0af7016f-4721-11e9-8210-1062e50d1fcb',	'sys_fphp_forestdata',	'Unique_Name=288bf780-7554-11e9-9305-1062e50d1fcb',	'true;288bf780-7554-11e9-9305-1062e50d1fcb',	50,	'288bf780-7554-11e9-9305-1062e50d1fcb'),
(7,	'0af750ee-4721-11e9-8210-1062e50d1fcb',	'sys_fphp_formelement',	'Unique_Name=b848c6c1-7552-11e9-9305-1062e50d1fcb',	'true;b848c6c1-7552-11e9-9305-1062e50d1fcb',	50,	'b848c6c1-7552-11e9-9305-1062e50d1fcb'),
(9,	'0af7f536-4721-11e9-8210-1062e50d1fcb',	'sys_fphp_formelement_forestdata',	NULL,	NULL,	NULL,	NULL),
(10,	'0af84f8f-4721-11e9-8210-1062e50d1fcb',	'sys_fphp_formelement_sqltype',	NULL,	NULL,	NULL,	NULL),
(12,	'0af8f35c-4721-11e9-8210-1062e50d1fcb',	'sys_fphp_language',	NULL,	NULL,	0,	NULL),
(18,	'0afad5e0-4721-11e9-8210-1062e50d1fcb',	'sys_fphp_session',	NULL,	'true;42886706-7592-11e9-980a-54bf640e09ee:false;6a5dcaa7-7592-11e9-980a-54bf640e09ee',	50,	'42886706-7592-11e9-980a-54bf640e09ee;6a5dcaa7-7592-11e9-980a-54bf640e09ee'),
(19,	'0afb2800-4721-11e9-8210-1062e50d1fcb',	'sys_fphp_sqltype',	'Unique_Name=3ff05d3c-7554-11e9-9305-1062e50d1fcb',	'true;3ff05d3c-7554-11e9-9305-1062e50d1fcb',	50,	'3ff05d3c-7554-11e9-9305-1062e50d1fcb'),
(21,	'0afbdaa4-4721-11e9-8210-1062e50d1fcb',	'sys_fphp_systemmessage',	'Unique_IdInternal_LanguageCode=bf10cdc8-7580-11e9-9305-1062e50d1fcb;7e9b0336-7580-11e9-9305-1062e50d1fcb',	'true;7e9b0336-7580-11e9-9305-1062e50d1fcb:true;bf10cdc8-7580-11e9-9305-1062e50d1fcb',	50,	'bf10cdc8-7580-11e9-9305-1062e50d1fcb;7e9b0336-7580-11e9-9305-1062e50d1fcb;e3af52e9-7580-11e9-9305-1062e50d1fcb;2b0db223-7581-11e9-9305-1062e50d1fcb'),
(22,	'0afc33bd-4721-11e9-8210-1062e50d1fcb',	'sys_fphp_table',	NULL,	NULL,	50,	NULL),
(23,	'0afc8f93-4721-11e9-8210-1062e50d1fcb',	'sys_fphp_tablefield',	NULL,	NULL,	NULL,	NULL),
(25,	'0afd586d-4721-11e9-8210-1062e50d1fcb',	'sys_fphp_translation',	'Unique=835410a5-e7a7-2381-a8bd-f20c05502fdd;51074834-d7f3-9e94-23f9-fcd829ea7410;11e34a7c-81f4-ca8c-05ec-5e1c678be25b',	'true;835410a5-e7a7-2381-a8bd-f20c05502fdd:true;51074834-d7f3-9e94-23f9-fcd829ea7410:true;11e34a7c-81f4-ca8c-05ec-5e1c678be25b',	50,	'51074834-d7f3-9e94-23f9-fcd829ea7410;11e34a7c-81f4-ca8c-05ec-5e1c678be25b;ed2e3a42-29a6-7dc9-331b-a6bd57988c66'),
(26,	'0afdb7e6-4721-11e9-8210-1062e50d1fcb',	'sys_fphp_trunk',	NULL,	NULL,	1,	'4975e4e3-7574-11e9-9305-1062e50d1fcb;4a2fa8b1-7575-11e9-9305-1062e50d1fcb;5e95cc53-7575-11e9-9305-1062e50d1fcb;6e1cc647-7575-11e9-9305-1062e50d1fcb;855e5411-7575-11e9-9305-1062e50d1fcb;a749a58b-7575-11e9-9305-1062e50d1fcb;355837a3-7575-11e9-9305-1062e50d1fcb;3a0d8e37-7576-11e9-9305-1062e50d1fcb;4ea1eef9-7576-11e9-9305-1062e50d1fcb;09f60fd4-3363-6fd9-e2bc-655aa28f2063;36c43a38-7577-11e9-9305-1062e50d1fcb;61a08f02-7577-11e9-9305-1062e50d1fcb;72c7ef3d-7577-11e9-9305-1062e50d1fcb;7da3190b-7577-11e9-9305-1062e50d1fcb;f5c6548f-7575-11e9-9305-1062e50d1fcb;070e32c8-7576-11e9-9305-1062e50d1fcb;1ea48201-7576-11e9-9305-1062e50d1fcb;2ae84af7-7576-11e9-9305-1062e50d1fcb;c2db1b54-7577-11e9-9305-1062e50d1fcb');

DROP TABLE IF EXISTS `sys_fphp_tablefield`;
CREATE TABLE `sys_fphp_tablefield` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UUID` varchar(36) NOT NULL,
  `TableUUID` varchar(36) NOT NULL,
  `FieldName` varchar(255) NOT NULL,
  `FormElementUUID` varchar(36) NOT NULL,
  `SqlTypeUUID` varchar(36) DEFAULT NULL,
  `ForestDataUUID` varchar(36) DEFAULT NULL,
  `TabId` varchar(255) DEFAULT NULL,
  `JSONEncodedSettings` text,
  `FooterElement` bit(1) DEFAULT NULL,
  `SubRecordField` varchar(255) DEFAULT NULL,
  `Order` smallint(6) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `uuid` (`UUID`),
  UNIQUE KEY `unique` (`TableUUID`,`FieldName`),
  UNIQUE KEY `unique2` (`TableUUID`,`Order`)
) ENGINE=InnoDB AUTO_INCREMENT=291 DEFAULT CHARSET=latin1;

INSERT INTO `sys_fphp_tablefield` (`Id`, `UUID`, `TableUUID`, `FieldName`, `FormElementUUID`, `SqlTypeUUID`, `ForestDataUUID`, `TabId`, `JSONEncodedSettings`, `FooterElement`, `SubRecordField`, `Order`) VALUES
(189,	'4975e4e3-7574-11e9-9305-1062e50d1fcb',	'0afdb7e6-4721-11e9-8210-1062e50d1fcb',	'LanguageCode',	'ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb',	'general',	'{\r\n	\"Label\" : \"#formLanguageCodeLabel#\",\r\n	\"SortHeader\" : \"#sortLanguageCode#\",\r\n	\"Id\" : \"sys_fphp_trunk_LanguageCode\"\r\n}',	CONV('0', 2, 10) + 0,	NULL,	1),
(195,	'355837a3-7575-11e9-9305-1062e50d1fcb',	'0afdb7e6-4721-11e9-8210-1062e50d1fcb',	'SessionIntervalGuest',	'ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb',	'useradmin',	'{\r\n	\"Label\" : \"#formSessionIntervalGuestLabel#\",\r\n	\"SortHeader\" : \"#sortSessionIntervalGuest#\",\r\n	\"Id\" : \"sys_fphp_trunk_SessionIntervalGuest\",\r\n	\"ValMessage\" : \"#formSessionIntervalGuestValMessage#\",\r\n	\"Placeholder\" : \"#formSessionIntervalGuestPlaceholder#\",\r\n	\"DateIntervalFormat\" : true\r\n}',	CONV('0', 2, 10) + 0,	NULL,	11),
(196,	'4a2fa8b1-7575-11e9-9305-1062e50d1fcb',	'0afdb7e6-4721-11e9-8210-1062e50d1fcb',	'DateTimeSqlFormat',	'ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb',	'general',	'{\r\n	\"Label\" : \"#formDateTimeSqlFormatLabel#\",\r\n	\"SortHeader\" : \"#sortDateTimeSqlFormat#\",\r\n	\"Id\" : \"sys_fphp_trunk_DateTimeSqlFormat\",\r\n	\"ValMessage\" : \"#formDateTimeSqlFormatValMessage#\",\r\n	\"Placeholder\" : \"#formDateTimeSqlFormatPlaceholder#\"\r\n}',	CONV('0', 2, 10) + 0,	NULL,	2),
(198,	'5e95cc53-7575-11e9-9305-1062e50d1fcb',	'0afdb7e6-4721-11e9-8210-1062e50d1fcb',	'DateTimeFormat',	'ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb',	'general',	'{\r\n	\"Label\" : \"#formDateTimeFormatLabel#\",\r\n	\"SortHeader\" : \"#sortDateTimeFormat#\",\r\n	\"Id\" : \"sys_fphp_trunk_DateTimeFormat\",\r\n	\"ValMessage\" : \"#formDateTimeFormatValMessage#\",\r\n	\"Placeholder\" : \"#formDateTimeFormatPlaceholder#\"\r\n}',	CONV('0', 2, 10) + 0,	NULL,	3),
(199,	'6e1cc647-7575-11e9-9305-1062e50d1fcb',	'0afdb7e6-4721-11e9-8210-1062e50d1fcb',	'DateFormat',	'ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb',	'general',	'{\r\n	\"Label\" : \"#formDateFormatLabel#\",\r\n	\"SortHeader\" : \"#sortDateFormat#\",\r\n	\"Id\" : \"sys_fphp_trunk_DateFormat\",\r\n	\"ValMessage\" : \"#formDateFormatValMessage#\",\r\n	\"Placeholder\" : \"#formDateFormatPlaceholder#\"\r\n}',	CONV('0', 2, 10) + 0,	NULL,	5),
(202,	'855e5411-7575-11e9-9305-1062e50d1fcb',	'0afdb7e6-4721-11e9-8210-1062e50d1fcb',	'TimeFormat',	'ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb',	'general',	'{\r\n	\"Label\" : \"#formTimeFormatLabel#\",\r\n	\"SortHeader\" : \"#sortTimeFormat#\",\r\n	\"Id\" : \"sys_fphp_trunk_TimeFormat\",\r\n	\"ValMessage\" : \"#formTimeFormatValMessage#\",\r\n	\"Placeholder\" : \"#formTimeFormatPlaceholder#\"\r\n}',	CONV('0', 2, 10) + 0,	NULL,	6),
(203,	'a749a58b-7575-11e9-9305-1062e50d1fcb',	'0afdb7e6-4721-11e9-8210-1062e50d1fcb',	'CheckUniqueUUID',	'ac1ed318-4717-11e9-8210-1062e50d1fcb',	'159489e2-4718-11e9-8210-1062e50d1fcb',	'1e7bae52-4717-11e9-8210-1062e50d1fcb',	'general',	'{\r\n	\"Label\" : \"#formCheckUniqueUUIDLabel#\",\r\n	\"Id\" : \"sys_fphp_trunk_CheckUniqueUUID\",\r\n	\"ValMessage\" : \"#formCheckUniqueUUIDValMessage#\",\r\n	\"Options\" : { \"#formCheckUniqueUUIDOptionLabel00#\" : \"1\" }\r\n}',	CONV('0', 2, 10) + 0,	NULL,	7),
(206,	'f5c6548f-7575-11e9-9305-1062e50d1fcb',	'0afdb7e6-4721-11e9-8210-1062e50d1fcb',	'IncContentUTF8Decode',	'ac1ed318-4717-11e9-8210-1062e50d1fcb',	'159489e2-4718-11e9-8210-1062e50d1fcb',	'1e7bae52-4717-11e9-8210-1062e50d1fcb',	'other',	'{\r\n	\"Label\" : \"#formIncContentUTF8DecodeLabel#\",\r\n	\"Id\" : \"sys_fphp_trunk_IncContentUTF8Decode\",\r\n	\"ValMessage\" : \"#formIncContentUTF8DecodeValMessage#\",\r\n	\"Options\" : { \"#formIncContentUTF8DecodeOptionLabel00#\" : \"1\" }\r\n}',	CONV('0', 2, 10) + 0,	NULL,	30),
(207,	'070e32c8-7576-11e9-9305-1062e50d1fcb',	'0afdb7e6-4721-11e9-8210-1062e50d1fcb',	'IncContentUTF8Encode',	'ac1ed318-4717-11e9-8210-1062e50d1fcb',	'159489e2-4718-11e9-8210-1062e50d1fcb',	'1e7bae52-4717-11e9-8210-1062e50d1fcb',	'other',	'{\r\n	\"Label\" : \"#formIncContentUTF8EncodeLabel#\",\r\n	\"Id\" : \"sys_fphp_trunk_IncContentUTF8Encode\",\r\n	\"ValMessage\" : \"#formIncContentUTF8EncodeValMessage#\",\r\n	\"Options\" : { \"#formIncContentUTF8EncodeOptionLabel00#\" : \"1\" }\r\n}',	CONV('0', 2, 10) + 0,	NULL,	31),
(208,	'1ea48201-7576-11e9-9305-1062e50d1fcb',	'0afdb7e6-4721-11e9-8210-1062e50d1fcb',	'OutContentUTF8Decode',	'ac1ed318-4717-11e9-8210-1062e50d1fcb',	'159489e2-4718-11e9-8210-1062e50d1fcb',	'1e7bae52-4717-11e9-8210-1062e50d1fcb',	'other',	'{\r\n	\"Label\" : \"#formOutContentUTF8DecodeLabel#\",\r\n	\"Id\" : \"sys_fphp_trunk_OutContentUTF8Decode\",\r\n	\"ValMessage\" : \"#formOutContentUTF8DecodeValMessage#\",\r\n	\"Options\" : { \"#formOutContentUTF8DecodeOptionLabel00#\" : \"1\" }\r\n}',	CONV('0', 2, 10) + 0,	NULL,	32),
(209,	'2ae84af7-7576-11e9-9305-1062e50d1fcb',	'0afdb7e6-4721-11e9-8210-1062e50d1fcb',	'OutContentUTF8Encode',	'ac1ed318-4717-11e9-8210-1062e50d1fcb',	'159489e2-4718-11e9-8210-1062e50d1fcb',	'1e7bae52-4717-11e9-8210-1062e50d1fcb',	'other',	'{\r\n	\"Label\" : \"#formOutContentUTF8EncodeLabel#\",\r\n	\"Id\" : \"sys_fphp_trunk_OutContentUTF8Encode\",\r\n	\"ValMessage\" : \"#formOutContentUTF8EncodeValMessage#\",\r\n	\"Options\" : { \"#formOutContentUTF8EncodeOptionLabel00#\" : \"1\" }\r\n}',	CONV('0', 2, 10) + 0,	NULL,	33),
(212,	'36c43a38-7577-11e9-9305-1062e50d1fcb',	'0afdb7e6-4721-11e9-8210-1062e50d1fcb',	'NavbarAdditionalClass',	'ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb',	'navbar',	'{\r\n	\"Label\" : \"#formNavbarAdditionalClassLabel#\",\r\n	\"SortHeader\" : \"#sortNavbarAdditionalClass#\",\r\n	\"Id\" : \"sys_fphp_trunk_NavbarAdditionalClass\",\r\n	\"ValMessage\" : \"#formNavbarAdditionalClassValMessage#\",\r\n	\"Placeholder\" : \"#formNavbarAdditionalClassPlaceholder#\"\r\n}',	CONV('0', 2, 10) + 0,	NULL,	18),
(213,	'61a08f02-7577-11e9-9305-1062e50d1fcb',	'0afdb7e6-4721-11e9-8210-1062e50d1fcb',	'NavbarAlign',	'ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb',	'navbar',	'{\r\n	\"Label\" : \"#formNavbarAlignLabel#\",\r\n	\"SortHeader\" : \"#sortNavbarAlign#\",\r\n	\"Id\" : \"sys_fphp_trunk_NavbarAlign\",\r\n	\"ValMessage\" : \"#formNavbarAlignValMessage#\",\r\n	\"Placeholder\" : \"#formNavbarAlignPlaceholder#\"\r\n}',	CONV('0', 2, 10) + 0,	NULL,	19),
(214,	'72c7ef3d-7577-11e9-9305-1062e50d1fcb',	'0afdb7e6-4721-11e9-8210-1062e50d1fcb',	'NavbarBrandTitle',	'ac1c4d63-4717-11e9-8210-1062e50d1fcb',	'd8796ff4-4717-11e9-8210-1062e50d1fcb',	'1098ab89-4717-11e9-8210-1062e50d1fcb',	'navbar',	'{\r\n	\"Label\" : \"#formNavbarBrandTitleLabel#\",\r\n	\"SortHeader\" : \"#sortNavbarBrandTitle#\",\r\n	\"Id\" : \"sys_fphp_trunk_NavbarBrandTitle\",\r\n	\"ValMessage\" : \"#formNavbarBrandTitleValMessage#\",\r\n	\"Placeholder\" : \"#formNavbarBrandTitlePlaceholder#\"\r\n}',	CONV('0', 2, 10) + 0,	NULL,	20),
(215,	'7da3190b-7577-11e9-9305-1062e50d1fcb',	'0afdb7e6-4721-11e9-8210-1062e50d1fcb',	'NavbarMaxLevel',	'ac21769b-4717-11e9-8210-1062e50d1fcb',	'1592a0e1-4718-11e9-8210-1062e50d1fcb',	'18d92e02-4717-11e9-8210-1062e50d1fcb',	'navbar',	'{\r\n	\"Label\" : \"#formNavbarMaxLevelLabel#\",\r\n	\"SortHeader\" : \"#sortNavbarMaxLevel#\",\r\n	\"Id\" : \"sys_fphp_trunk_NavbarMaxLevel\",\r\n	\"ValMessage\" : \"#formNavbarMaxLevelValMessage#\",\r\n	\"Min\" : \"1\",\r\n	\"Max\" : \"100\"\r\n}',	CONV('0', 2, 10) + 0,	NULL,	21),
(228,	'6a5dcaa7-7592-11e9-980a-54bf640e09ee',	'0afad5e0-4721-11e9-8210-1062e50d1fcb',	'Timestamp',	'ac20a373-4717-11e9-8210-1062e50d1fcb',	'15937c51-4718-11e9-8210-1062e50d1fcb',	'3e0f992f-4717-11e9-8210-1062e50d1fcb',	'general',	'{\r\n	\"Label\" : \"#formTimestampLabel#\",\r\n	\"SortHeader\" : \"#sortTimestamp#\",\r\n	\"Id\" : \"sys_fphp_timestamp_Timestamp\",\r\n	\"ValMessage\" : \"#formTimestampValMessage#\"\r\n}',	CONV('0', 2, 10) + 0,	NULL,	2),
(290,	'1119141b-6d93-277c-d43b-efc0e6189c68',	'0afdb7e6-4721-11e9-8210-1062e50d1fcb',	'form',	'5d044167-4717-11e9-8210-1062e50d1fcb',	NULL,	NULL,	'general',	'{\"FormTabConfiguration\":{\"Tab\":true,\"TabMenuClass\":\"nav nav-tabs\",\"TabActiveClass\":\"active\",\"TabToggle\":\"tab\",\"TabContentClass\":\"tab-content\",\"TabFooterClass\":\"tab-footer\",\"TabElementClass\":\"tab-pane fade\",\"TabElementActiveClass\":\"tab-pane fade in active\",\"TabsInfo\":[{\"TabId\":\"general\",\"TabTitle\":\"#001.formTabTitle#\"},{\"TabId\":\"useradmin\",\"TabTitle\":\"#formUserTitle#\"},{\"TabId\":\"navbar\",\"TabTitle\":\"#formNavbarTitle#\"},{\"TabId\":\"other\",\"TabTitle\":\"#formOtherTitle#\"}]},\"FormModalConfiguration\":{\"Modal\":true,\"ModalClass\":\"modal fade\",\"ModalId\":\"fphp_Modal\",\"ModalTitle\":\"forestPHP Modal Form\",\"ModalRole\":\"dialog\",\"ModalDialogClass\":\"modal-dialog modal-xl\",\"ModalDialogContentClass\":\"modal-content\",\"ModalHeaderClass\":\"modal-header\",\"ModalHeaderCloseClass\":\"close\",\"ModalHeaderDismissClass\":\"modal\",\"ModalHeaderCloseContent\":\"×\",\"ModalBodyClass\":\"modal-body\",\"ModalFooterClass\":\"modal-footer\"},\"Class\":\"form-horizontal\",\"FormGroupClass\":\"form-group\",\"LabelClass\":\"col-sm-3 control-label\",\"FormElementClass\":\"col-sm-9\",\"ClassAll\":\"form-control\",\"RadioClass\":\"radio\",\"CheckboxClass\":\"checkbox\",\"UseCaptcha\":false,\"Id\":\"sys_fphp_trunk_form\"}',	CONV('0', 2, 10) + 0,	NULL,	37);

DROP TABLE IF EXISTS `sys_fphp_translation`;
CREATE TABLE `sys_fphp_translation` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UUID` varchar(36) NOT NULL,
  `BranchId` int(10) unsigned NOT NULL,
  `LanguageCode` varchar(255) DEFAULT NULL,
  `Name` varchar(255) NOT NULL,
  `Value` varchar(255) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UNIQUE1` (`BranchId`,`LanguageCode`,`Name`),
  KEY `asd` (`BranchId`)
) ENGINE=InnoDB AUTO_INCREMENT=1093 DEFAULT CHARSET=latin1;

INSERT INTO `sys_fphp_translation` (`Id`, `UUID`, `BranchId`, `LanguageCode`, `Name`, `Value`) VALUES
(1,	'efba8e04-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Home',	'Startseite'),
(6,	'efba9247-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'btnNewText',	'Neu'),
(7,	'efba92b2-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'btnEditText',	'Ändern'),
(8,	'efba9318-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'btnDeleteText',	'Löschen'),
(9,	'efba937b-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'btnViewText',	'Datensatz anzeigen'),
(10,	'efba93df-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'btnDetailsText',	'Details'),
(11,	'efba9440-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'btnAddColumnsText',	'Spalten hinzufügen'),
(12,	'efba94a3-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'btnListText',	'Auflistung'),
(13,	'efba9665-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'AddColumnsModalTitle',	'Spalte/n hinzufügen'),
(14,	'efba96d4-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'AddColumnsLabel',	'Verfügbare Spalten:'),
(15,	'efba9733-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'AddColumnsValMessage',	'Bitte wählen Sie eine Spalte aus.'),
(16,	'efba97d7-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'AddColumnsDescription',	'Es werden bereits alle verfügbaren Spalten angezeigt.'),
(17,	'efba9842-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'FilterDropDownText',	'Filtern nach'),
(18,	'efba98a4-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'FilterAllColumnsText',	'Alle Spalten'),
(19,	'efba9904-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'FilterAllColumnsUsedText',	'Alle Spalten verwendet'),
(20,	'efba9967-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'FilterInputPlaceholder',	'...'),
(23,	'efba9a93-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Runtime',	'Laufzeit:'),
(24,	'efba9af8-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'RuntimeSeconds',	'Sekunden'),
(25,	'efba9b58-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Queries',	'Abfragen'),
(26,	'efba9bba-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'BranchTitleRecord',	'Datensatz'),
(27,	'efba9c19-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'NewModalTitle',	'Neuer Datensatz'),
(28,	'efba9c79-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'EditModalTitle',	'Datensatz ändern'),
(29,	'efba9cd7-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'DeleteModalTitle',	'Löschen'),
(30,	'efba9d37-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'DeleteModalDescriptionOne',	'Wollen Sie den Datensatz wirklich löschen?'),
(31,	'efba9db3-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'DeleteModalDescriptionMultiple',	'Wollen Sie die %0 Datensätze wirklich löschen?'),
(32,	'efba9e16-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'ValRequiredMessage',	'Bitte geben Sie einen Wert an.'),
(33,	'efba9e77-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'limitPage',	'Seite'),
(34,	'efba9eb6-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'limitFirstPage',	'Erste Seite'),
(35,	'efba9ef1-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'limitPreviousPage',	'Vorherige Seite'),
(36,	'efbaa336-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'limitNextPage',	'Nächste Seite'),
(37,	'efbaa38e-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'limitLastPage',	'Letzte Seite'),
(38,	'efbaa3c9-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formTabTitle',	'Allgemein'),
(39,	'efbaa406-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formSubmit',	'Senden'),
(40,	'efbaa43e-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formCancel',	'Abbrechen'),
(41,	'efbaa477-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formYes',	'Ja'),
(42,	'efbaa4b2-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formNo',	'Nein'),
(52,	'efbaa710-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'monthJanuary',	'Januar'),
(53,	'efbaa749-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'monthFebruary',	'Februar'),
(54,	'efbaa783-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'monthMarch',	'März'),
(55,	'efbaa7ba-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'monthApril',	'April'),
(56,	'efbaa7f0-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'monthMay',	'Mai'),
(57,	'efbaa849-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'monthJune',	'Juni'),
(58,	'efbaa883-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'monthJuly',	'Juli'),
(59,	'efbaa8ba-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'monthAugust',	'August'),
(60,	'efbaa8f3-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'monthSeptember',	'September'),
(61,	'efbaa92d-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'monthOctober',	'Oktober'),
(62,	'efbaa962-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'monthNovember',	'November'),
(63,	'efbaa99a-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'monthDecember',	'Dezember'),
(64,	'efbaa9d1-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'calenderWeek',	'KW'),
(469,	'efbb79a9-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Translation',	'Translation'),
(470,	'efbb79ee-6cdc-11e9-b874-1062e50d1fcb',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Language',	'Language'),
(544,	'08f76cb1-92fb-f4df-9e38-1bcf6825be2f',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'btnBack',	'Zurück'),
(657,	'f019b81b-5b09-4082-4fde-909a17086db4',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'forestPHP',	'forestPHP'),
(659,	'd2a65fae-2bfe-aca9-3a99-83ce487203f9',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Settings',	'Einstellungen'),
(660,	'4fa0efdd-1e16-c8da-857a-cae438e215e6',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'User Administration',	'Benutzereinstellungen'),
(665,	'06891fa2-dfd1-db1c-fa6f-9b3a41b54811',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Trunk',	'Trunk'),
(666,	'6cfd14ce-a35c-baaf-7feb-4619ca54eb07',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'System Messages',	'System Messages'),
(667,	'f66eef32-9998-7358-1779-f10cc1d8c6f1',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'Sessions',	'Sessions'),
(698,	'4ba9c658-4ec9-6d27-8884-d925e71aa374',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formLanguageCodeLabel',	'Sprachcode:'),
(699,	'1ef5b70e-a3ca-f4ac-71ba-7f0f6993384d',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'sortLanguageCode',	'Sprachcode'),
(706,	'65802af1-e14c-c6a9-97f2-34f2d6d6bf36',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formUUIDUsergroupValMessage',	'Bitte geben Sie eine Standard-Benutzergruppe an.'),
(707,	'ac52a57f-1911-a90c-5beb-b68ca661f835',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formUUIDUsergroupPlaceholder',	'Standard-Benutzergruppe...'),
(716,	'0040b3e8-5c7f-7cbb-7430-481e05704d3d',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formSessionIntervalGuestLabel',	'Session-Intervall:'),
(717,	'63cb7218-657a-03a1-ceb8-a4d3dbd089d7',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'sortSessionIntervalGuest',	'Session-Intervall'),
(718,	'598ebd36-af88-aa68-98f2-173f7aab8822',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formSessionIntervalGuestValMessage',	'Bitte geben Sie eine Session-Intervall an.'),
(719,	'a7a72376-1cdc-ff4e-e0b2-cb4750f7cf68',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formSessionIntervalGuestPlaceholder',	'PT12M'),
(720,	'da108af0-4d50-f1e0-a068-db27f895722e',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formDateTimeSqlFormatLabel',	'DateTime-SQL-Format:'),
(721,	'f0cae2f9-4e8f-8854-998d-ad4aee3d2748',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'sortDateTimeSqlFormat',	'DateTime-SQL-Format'),
(722,	'f629c989-b571-e26f-e7eb-6641c9b3039e',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formDateTimeSqlFormatValMessage',	'Bitte geben Sie das DateTime-SQL-Format an.'),
(723,	'8598ec89-17e3-d756-b306-657f2e831c8a',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formDateTimeSqlFormatPlaceholder',	'DateTime-SQL-Format...'),
(724,	'9e53784f-6ba3-1417-12af-fbbbe50ce036',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formDateTimeFormatLabel',	'DateTime-Format:'),
(725,	'92f05e6c-5814-b207-c735-8a6ee1af5ff8',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'sortDateTimeFormat',	'DateTime-Format'),
(726,	'6ad46125-104f-975e-d23a-e6b57017c763',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formDateTimeFormatValMessage',	'Bitte geben Sie das DateTime-Format an.'),
(727,	'4d20d763-a247-1756-dd09-53be62a6f760',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formDateTimeFormatPlaceholder',	'DateTime-Format...'),
(728,	'2354fc28-68dd-5cf3-8292-0d6f70a048d7',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formDateFormatLabel',	'Date-Format:'),
(729,	'2e26c845-6f7f-eca8-5358-e1db27d888c6',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'sortDateFormat',	'Date-Format'),
(730,	'8a69299c-5528-2ad8-832e-4c8a7e339eef',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formDateFormatValMessage',	'Bitte geben Sie das Date-Format an.'),
(731,	'a7425b9e-c966-8c7a-5413-3ce38d70936a',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formDateFormatPlaceholder',	'Date-Format...'),
(732,	'79a163f4-56e7-6713-596a-7b04214d5227',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formTimeFormatLabel',	'Time-Format:'),
(733,	'8242e18e-a753-ccd6-a9bd-2e1d84e4d93a',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'sortTimeFormat',	'Time-Format'),
(734,	'8f817b9d-b242-542d-aff1-cf6da8a81b88',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formTimeFormatValMessage',	'Bitte geben Sie das Time-Format an.'),
(735,	'81e54845-8370-4900-ccc9-b5c4ce8e8520',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formTimeFormatPlaceholder',	'Time-Format...'),
(736,	'138be86b-590d-0aa2-6e4e-a7fcae357554',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formCheckUniqueUUIDLabel',	' '),
(737,	'42e95ecb-5feb-6a62-0b6a-75f214d12228',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formCheckUniqueUUIDValMessage',	'Bitte aktivieren Sie die Überprüfung der UUID-Generierung.'),
(738,	'ca218551-ea45-0188-0be9-1b303fdfe00b',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formCheckUniqueUUIDOptionLabel00',	'UUID-Generierung überprüfen'),
(744,	'080591e3-b881-d868-56e0-98ce3a618bdf',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formIncContentUTF8DecodeLabel',	' '),
(745,	'99b867c0-7ff6-b14c-3782-ef389e6b11d3',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formIncContentUTF8DecodeValMessage',	'Bitte aktivieren Sie die eingehende UTF-8 Dekodierung.'),
(746,	'a591a727-e104-b5e0-4b93-408c7588d505',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formIncContentUTF8DecodeOptionLabel00',	'Inc. Content UTF-8 Decode'),
(747,	'f76b869b-b49b-76fc-3f71-234b46beb17e',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formIncContentUTF8EncodeLabel',	' '),
(748,	'7d9c815f-0504-3af4-e021-2abcb7badec8',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formIncContentUTF8EncodeValMessage',	'Bitte aktivieren Sie die eingehende UTF-8 Kodierung.'),
(749,	'0bc3895a-b998-39c6-f3bc-70376c1b39f5',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formIncContentUTF8EncodeOptionLabel00',	'Inc. Content UTF-8 Encode'),
(750,	'7cd431a0-106e-4150-bd51-fe2efd0f8c32',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formOutContentUTF8DecodeLabel',	' '),
(751,	'6fcf0748-d32e-3b75-99d7-fa8f1a9f2f31',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formOutContentUTF8DecodeValMessage',	'Bitte aktivieren Sie die ausgehende UTF-8 Dekodierung.'),
(752,	'cc4cf9e3-b601-5dbd-7cab-58b9af6a2ac2',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formOutContentUTF8DecodeOptionLabel00',	'Outg. Content UTF-8 Decode'),
(753,	'60a99f52-bbb9-2dbd-bf2d-ecd28f9a9e37',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formOutContentUTF8EncodeLabel',	' '),
(754,	'9d204768-d8b2-04b7-507b-0fce6e6f3747',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formOutContentUTF8EncodeValMessage',	'Bitte aktivieren Sie die ausgehende UTF-8 Kodierung.'),
(755,	'9b350f01-2543-8373-8292-e7e125a4e56b',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formOutContentUTF8EncodeOptionLabel00',	'Outg. Content UTF-8 Encode'),
(763,	'24d7e556-b24b-86aa-4d01-566312003a66',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formNavbarAdditionalClassLabel',	'Navbar-Additional-Class:'),
(764,	'b050d24d-c060-7a3d-d0fd-4d0761efe097',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'sortNavbarAdditionalClass',	'Navbar-Additional-Class'),
(765,	'4f39b151-3d35-711d-9059-c073d7edc292',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formNavbarAdditionalClassValMessage',	'Bitte geben Sie einen Wert für Navbar-Additional-Class an.'),
(766,	'337a7a31-b0db-5fec-adc6-f5bfec3c1873',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formNavbarAdditionalClassPlaceholder',	'Navbar-Additional-Class...'),
(767,	'63cabbf7-71bd-793a-1acc-b06da130dad9',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formNavbarAlignLabel',	'Navbar-Align:'),
(768,	'bcc3c505-25c6-f235-4708-7578f07f2ad8',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'sortNavbarAlign',	'Navbar-Align'),
(769,	'1d58a54c-02ac-d543-0880-bf8f1d621f4f',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formNavbarAlignValMessage',	'Bitte geben Sie einen Wert für Navbar-Align an.'),
(770,	'e43707cf-b71f-6990-21dc-043329462606',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formNavbarAlignPlaceholder',	'navbar-fixed-top / navbar-fixed-bottom'),
(771,	'29fd893a-8a1e-845f-82b9-af354bb250d1',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formNavbarBrandTitleLabel',	'Navbar-Brand-Title:'),
(772,	'cf6664ab-e27a-2f7b-c7e0-39d9a81d07ea',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'sortNavbarBrandTitle',	'Navbar-Brand-Title'),
(773,	'3444f6c8-335a-13ea-0f4e-4e29c82ac84e',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formNavbarBrandTitleValMessage',	'Bitte geben Sie einen Wert für Navbar-Brand-Title an.'),
(774,	'df0f8b9a-0495-02ee-a9bf-206b5b5f2ed5',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formNavbarBrandTitlePlaceholder',	'Website-Title...'),
(775,	'542bc6fe-c8ff-254d-006c-ee7003447b78',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formNavbarMaxLevelLabel',	'Navbar-Max-Level:'),
(776,	'c73dcbd3-32aa-d8b2-63ef-19b257b817e1',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'sortNavbarMaxLevel',	'Navbar-Max-Level'),
(777,	'86e0edf5-3358-b5d4-b022-be6cf613bb1f',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formNavbarMaxLevelValMessage',	'Bitte geben Sie das max. anzuzeigende Level in der Navigationsleiste an.'),
(806,	'f0a67477-9055-78af-513b-614af1d47d8c',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'sortIncContentUTF8Decode',	'Inc. Content UTF-8 Decode'),
(807,	'699532aa-10f8-7c37-b79b-0752260344b6',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'sortIncContentUTF8Encode',	'Inc. Content UTF-8 Encode'),
(808,	'8d9f8a78-077c-f642-c010-b6ef9f9cb498',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'sortOutContentUTF8Decode',	'Outg. Content UTF-8 Decode'),
(809,	'bce5ab12-468c-5b75-efd0-4c5e07222e38',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'sortOutContentUTF8Encode',	'Outg. Content UTF-8 Encode'),
(811,	'f0dc7555-44d7-749b-7679-33c689cf959d',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'sortCheckUniqueUUID',	' UUID-Generierung überprüfen'),
(824,	'6f58d0ef-3822-1aca-6664-f7eb3c87b8a2',	39,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formUserUUIDLabel',	'User-UUID:'),
(825,	'8c90d035-7912-8332-83bb-30217c789eec',	39,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'sortUserUUID',	'User-UUID'),
(826,	'2ac4adba-f63b-35e6-60be-b11d2530e9e0',	39,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formTimestampLabel',	'Zeitstempel:'),
(827,	'2bc89f8a-3664-cc04-375e-1e060a15413c',	39,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'sortTimestamp',	'Zeitstempel'),
(828,	'8133abc2-7541-f776-53c2-78c2c8e33fb6',	39,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formTimestampValMessage',	'Bitte geben Sie einen Zeitstempel an.'),
(1007,	'9111c790-8151-08db-ef43-555b3e229978',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'dateIntervalYear',	'Jahr(e)'),
(1008,	'6b6262bf-a2c2-1800-2e50-3a0d9d100fb3',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'dateIntervalMonth',	'Monat(e)'),
(1009,	'0ca60131-ab4d-ade4-74e7-75e0379736d6',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'dateIntervalDay',	'Tag(e)'),
(1010,	'7187fe22-60f5-9aea-b915-77fe2f4a8d9e',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'dateIntervalHour',	'Stunde(n)'),
(1011,	'ee151e03-53ce-7317-e0e7-f1a7e51e9645',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'dateIntervalMinute',	'Minute(n)'),
(1012,	'22824a32-9b91-418d-3be6-21089ec8b6b1',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'dateIntervalSecond',	'Sekunde(n)'),
(1051,	'12001cb5-69a0-56ce-c6fc-0ae82a0f56dd',	1,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'NoRecords',	'Es konnten keine Datensätze gefunden werden.'),
(1089,	'87325a7d-b0ee-5386-a8b3-d963d388017d',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formUserTitle',	'Session'),
(1091,	'd92907bf-6c16-c156-4f73-6dc8f3fee175',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formNavbarTitle',	'Navbar'),
(1092,	'cf5cbd87-9a08-0f86-0777-6fe9229f8810',	37,	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'formOtherTitle',	'Sonstiges');

DROP TABLE IF EXISTS `sys_fphp_trunk`;
CREATE TABLE `sys_fphp_trunk` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UUID` varchar(36) NOT NULL,
  `LanguageCode` varchar(255) DEFAULT NULL,
  `SessionIntervalGuest` varchar(255) DEFAULT NULL,
  `DateTimeSqlFormat` varchar(255) DEFAULT NULL,
  `DateTimeFormat` varchar(255) DEFAULT NULL,
  `DateFormat` varchar(255) DEFAULT NULL,
  `TimeFormat` varchar(255) DEFAULT NULL,
  `CheckUniqueUUID` bit(1) DEFAULT NULL,
  `IncContentUTF8Decode` bit(1) DEFAULT NULL,
  `IncContentUTF8Encode` bit(1) DEFAULT NULL,
  `OutContentUTF8Decode` bit(1) DEFAULT NULL,
  `OutContentUTF8Encode` bit(1) DEFAULT NULL,
  `NavbarAdditionalClass` varchar(255) DEFAULT NULL,
  `NavbarAlign` varchar(255) NOT NULL,
  `NavbarBrandTitle` varchar(255) NOT NULL,
  `NavbarMaxLevel` smallint(6) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO `sys_fphp_trunk` (`Id`, `UUID`, `LanguageCode`, `SessionIntervalGuest`, `DateTimeSqlFormat`, `DateTimeFormat`, `DateFormat`, `TimeFormat`, `CheckUniqueUUID`, `IncContentUTF8Decode`, `IncContentUTF8Encode`, `OutContentUTF8Decode`, `OutContentUTF8Encode`, `NavbarAdditionalClass`, `NavbarAlign`, `NavbarBrandTitle`, `NavbarMaxLevel`) VALUES
(1,	'29fcb1b8-6cdc-11e9-b874-1062e50d1fcb',	'9230337b-6cd9-11e9-b874-1062e50d1fcb',	'P2D',	'Y-m-d H:i:s',	'd.m.Y H:i:s',	'd.m.Y',	'H:i:s',	CONV('0', 2, 10) + 0,	CONV('1', 2, 10) + 0,	CONV('0', 2, 10) + 0,	CONV('0', 2, 10) + 0,	CONV('1', 2, 10) + 0,	'navbar-inverse',	'navbar-fixed-top',	'forestPHP Framework',	10);

-- 2019-07-11 07:29:49
