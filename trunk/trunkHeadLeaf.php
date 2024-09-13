<?php
/**
 * standard head file of fphp framework
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestany.net>
 * @copyright   (c) 2024 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.1.0 stable
 * @link        https://forestany.net
 * @object-id   0x1 0001C
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version			Developer	Date		Comment
 * 				0.1.0 alpha		renea		2019-08-04	first build
 * 				0.1.1 alpha		renea		2019-08-15	added navigation
 * 				0.1.3 alpha		renea		2019-08-15	added fphp_data_validator
 * 				0.1.4 alpha		renea		2019-09-28	added fphp_dropzone and fphp_richtext
 * 				0.9.0 beta		renea		2020-01-27	added bootstrap 4 and font awesome
 * 				1.1.0 stable	renea		2024-08-10	changes for bootstrap 5
 * 				1.1.0 stable	renea		2024-08-11	added language for html tag
 * 				1.1.0 stable	renea		2024-08-11	added body top anchor and flex classes
 *				1.1.0 stable	renea		2024-08-13	added meta tags with additional information
 */
?>
<!DOCTYPE html>
<html lang="de" class="h-100">
<head>
	<meta charset="UTF-8">
	<title>Title of website</title>
	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Description.">
	<meta property="og:locale" content="de_DE">
	<meta property="og:type" content="website">
	<meta property="og:site_name" content="Title of website">
	<meta property="og:description" content="Description.">
	<meta property="og:url" content="https://test.net/">
	<meta name="generator" content="forestPHP 1.1.0">
	
	<link rel="stylesheet" href="./src/adopted/bootstrap-5.3.3/css/bootstrap.min.css">
	<link rel="stylesheet" href="./src/adopted/jquery-ui-1.14.0/themes/base/jquery-ui.min.css">
	<link rel="stylesheet" href="./src/adopted/bootstrap-icons-1.11.3/font/bootstrap-icons.min.css">
	<link rel="stylesheet" href="./src/forestPHP.css">
	
	<script src="./src/adopted/jquery-3.7.1/jquery.min.js"></script>
	<script src="./src/adopted/jquery-validation-1.21.0/jquery.validate.min.js"></script>
	<script src="./src/adopted/jquery-ui-1.14.0/jquery-ui.min.js"></script>
	<script src="./src/adopted/bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
	
	<script src="./src/forestPHP.js"></script>
	<script src="./src/fphp_dropzone.js"></script>
	<script src="./src/fphp_richtext.js"></script>
	<script src="./src/fphp_data_validator.js"></script>
</head>
<body id="topAnchor" class="d-flex flex-column h-100">