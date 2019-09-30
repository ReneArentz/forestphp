<p align="center">
  <a href="https://forestphp.de/" target="_blank" >
    <img alt="forestPHP" src="https://forestphp.de/images/forestPHP.png" width="400" />
  </a>
</p>

[forestPHP Framework](https://forestphp.de) is a project for creating a PHP web framework for programmers and advanced web hosts. It serves to support the management and control of data in relational databases such as e.g. MariaDB.

**Project objectives**

* a **easy manipulation** and editing of records using an extensive database access layer
* handling **all administrative settings in the same web application** without using any other database management software (except for the initial installation and setup)
* for a **consistent presentation** forestPHP also uses freely available projects such as [Bootstrap](https://getbootstrap.com/), [jQuery](https://jquery.com/), [jQuery Validate](https://jqueryvalidation.org/), [jQuery UI](https://jqueryui.com/) und [Font Awesome](https://fontawesome.com/)
* **fixed data types** with specially developed PHP classes and access functions prevent incorrect entry of data in relational database systems
* **scope for development** - with a broad range of functions as a basis, forestPHP provides enough interfaces in the source code to extend, replace and, if necessary, completely rebuild standard functions. Furthermore, enough provided configuration options try to cover as many use cases as possible.

forestPHP follows the **MVC model** and sets some conventions to keep the development work straightforward. The organization(model) and the representation(view) of the data are separated and controlled by central functions(controller).

Following **database systems** are supported by forestPHP:

* MariaDB/MySQL

forestPHP framework will be released under the **GPLv3 license** and the **MIT license**. Thus it is freely possible to use forestPHP in other projects - projects with free software or in commercial projects.

## Releases

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

After downloading the [current version](https://forestphp.de/index.html#download) or obtaining a copy from [GitHub](https://github.com/ReneArentz/forestphp) you can copy the forestPHP-directory in a folder of the web server or upload this.

On the database system MariaDB / MySQL you have to create a new database for the use of forestPHP (e.g. *forestphp*). In the forestPHP-directory you can find in folder **install** a SQL-file: **forestPHP_Vanilla_SQL_MariaDB.sql**. This file contains all SQL-Queries for initial use of forestPHP, which must be executed in the previously created database. Subsequently, forestPHP can be called up and used on the web server.

In file [forestPHP.php](https://forestphp.de/docu/0_1_0/en/docu.html#forestPHP) in *constructor* of the class you have to configure the connection to the database, by entering database, user and password:

``` php
$o_glob->Base->Add(new forestBase(forestBase::MariaSQL, 'host_ip', 'database', 'db_user', 'db_pw'), 'forestPHPMariaSQLBase');
```
Make sure that you specify the key of the new connection as the active base gateway: `php $o_glob->ActiveBase = 'forestPHPMariaSQLBase';`
In general, it is possible to create several connections to different databases with forestPHP. 

## Tests

* **Windows**
	* Windows NT 10.0 build 18362 (Windows 10) AMD64
	* Apache/2.4.38 (Win64)
	* PHP/7.3.2;10.1.38-MariaDB
	* mysqlnd 5.0.12-dev
