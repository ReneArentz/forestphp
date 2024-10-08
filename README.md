<p align="center">
  <a href="https://forestany.net/" target="_blank">
    <img alt="forestPHP" src="https://forestany.net/pngs/fphp-logo.png" width="400">
  </a>
</p>

[forestPHP Framework](https://forestany.net/fphp.php) is a project for creating a PHP web framework for programmers and advanced web hosts. It serves to support the management and control of data in relational databases such as e.g. MariaDB.

**Project objectives**

* an **easy manipulation** and editing of records using an extensive database access layer
* handling **all administrative settings in the same web application** without using any other database management software (except for the initial installation and setup)
* for a **consistent presentation** forestPHP also uses freely available projects such as [Bootstrap](https://getbootstrap.com/), [jQuery](https://jquery.com/), [jQuery Validate](https://jqueryvalidation.org/) and [jQuery UI](https://jqueryui.com/)
* **fixed data types** with specially developed PHP classes and access functions prevent incorrect entry of data in relational database systems
* **scope for development** - with a broad range of functions as a basis, forestPHP provides enough interfaces in the source code to extend, replace and, if necessary, completely rebuild standard functions. Furthermore, enough provided configuration options try to cover as many use cases as possible.

forestPHP follows the **MVC model** and sets some conventions to keep the development work straightforward. The organization(model) and the representation(view) of the data are separated and controlled by central functions(controller).

Following **database systems** are supported by forestPHP:

* MariaDB/MySQL
* SQLite3
* MSSQL
* OracleDB
* PostgreSQL
* MongoDB

At the core of forestPHP measures have been taken to implement **security best practices** and to maximize the security of a web application. This helps prevent attacks such as SQL injection, CRFS, XSS, and form manipulation.

forestPHP framework will be released under the **GPLv3 license** and the **MIT license**. Thus it is freely possible to use forestPHP in other projects - projects with free software or in commercial projects.

## Releases

### 1.1.0 (stable)
New features: Upgrade to Bootstrap 5, Action Chain Processing, Branch Templates, Static page functionality, Introduction of cookie consent, Updated footer, Maintenance page revision, Record order by identifier, Optional navbar logo, Reload button for captcha on sign up page *09/2024*

### 1.0.1 (stable)
New features: Settings for samesite cookie, Images with thumbnail and detail view support, forestLookup table field with datalist/list form element, Add optional validation rule with json settings, Supporting rename table, forestDateTime will be compared for uniqueness check functionality, Options to block images incoming with ctrl+v and auto resize images with drag and drop in richtext element *11/2023*

### 1.0.0 (stable)
New features: Database Support extended to: SQLite3 + MSSQL + OracleDB + PostgreSQL + MongoDB, Tested on linux, Issues resolved, Backward Compatibility to PHP 5.x, Preparation for Statistics *10/2023*

### 0.9.0 (beta)
New features: Upgrade to Bootstrap 4, Navigation Sidebar + Full-screen, Implementation of PHP namespaces, Completion of English Translation, Restructure of comments in forestPHP Source Code *09/2023*

### 0.8.0 (beta)
New features: fPHP Flex, Implementation of Logs, Account settings. *02/2020*

### 0.7.0 (beta)
New features: Identifier Administration, Maintenance Mode, Money-Format, forestCombination FILENAME + FILEVERSION. *01/2020*

### 0.6.0 (beta)
New features: Created + Modified information columns, Versioning + History of files, Submit-button time delay. *01/2020*

### 0.5.0 (beta)
New features: Checkout of records, Honeypot Fields, Administration of Form-Elements + ForestData + SqlType + Validation Rules. *12/2019*

### 0.4.0 (beta)
New features: User Administration, Usergroups, Roles + Permissions, Permission Inheritance, Truncate twig, Transfer twig. *11/2019*

### 0.3.0 (beta)
New features: Administration of Tablefields, Administration of Sub Constraints + Sub Records, Handling Translations, Unique Keys, Sort Order for tables, Administration of Validation Rules for tablefields. *11/2019*

### 0.2.0 (beta)
New features: New features: Root Menu, Implementation SQL DDL, Administration of Branches, Administration of Actions, Administration of Twigs. *10/2019*

### 0.1.5 (alpha)
New features: Sub Constraints + Sub Records, Implementation of forestCombination, Implementation of forestLookup, MoveUp + MoveDown actions of records, Thumbnail view for form file elements, Captcha element. *10/2019*

### 0.1.4 (alpha)
New features: Detail modal view, Richtext element, Dropzone element, File Upload Handling, File Replacement. *09/2019*

### 0.1.3 (alpha)
New features: jQuery Validate Implementation, form-key, Language, Translation, System Messages. *09/2019*

### 0.1.2 (alpha)
New features: CRUD Actions, View + List-View, Sorting, Paging, Filtering of records. *09/2019*

### 0.1.1 (alpha)
New features: Trunk Settings, Dynamic Landing page, DateTime + DateInterval handling, forestForm + Elements, Navigation bar, Dynamic Tablefields. *08/2019*

### 0.1.0 (alpha)
After 12 years of planning, conception and development as a hobby project in leisure time. First release of the forestPHP Framework 0.1.0 (alpha). Provision of foundation files + support MariaDB / MySQL. *08/2019*

## Installation

After downloading the [current version](https://forestany.net/fphp.php#scrollspyToSection02) or obtaining a copy from [GitHub](https://github.com/ReneArentz/forestphp) you can copy the forestPHP-directory in a folder of the web server or upload this.

On the database system MariaDB / MySQL you have to create a new database for the use of forestPHP (e.g. *forestphp*). In the forestPHP-directory you can find in folder **install** a SQL-file: **forestPHP_Vanilla_SQL_MariaDB.sql**. This file contains all SQL-Queries for initial use of forestPHP, which must be executed in the previously created database. Subsequently, forestPHP can be called up and used on the web server.

In file [forestPHP.php](https://forestany.net/fphp/docu.php#forestPHP) in *constructor* of the class you have to configure the connection to the database, by entering database, user and password:

``` php
$o_glob->Base->Add(new \fPHP\Base\forestBase(\fPHP\Base\forestBase::MariaSQL, 'host_ip', 'forestphp_1_1_0', 'db_user', 'db_pw'), 'forestPHPMariaSQLBase');
```
Make sure that you specify the key of the new connection as the active base gateway: `php $o_glob->ActiveBase = 'forestPHPMariaSQLBase';`
In general, it is possible to create several connections to different databases with forestPHP. 

## Tests

* **Windows**
	* Windows NT 10.0 build 18362 (Windows 10) AMD64
	* Apache/2.4.38 (Win64)
	* PHP/7.3.2;10.1.38-MariaDB
	* mysqlnd 5.0.12-dev
