<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.1.0 (0x1 00000)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * index-page of the forestPHP framework
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build
 */

/* display options for error reporting, useful for debugging */
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/* activate output buffer for content scripting */
ob_start();

/* initalize cookie settings (still hard coding) */
/* life duration of cookie: 2 days */
session_set_cookie_params(2 * 24 * 60 * 60, '/', '');
session_name('forestPHPSession'); /* unqiue name per web application, install script */
session_start();
session_regenerate_id(false);

/* global vars for exception rendering and debug usage */
global $o_main_exception;
global $b_write_main_exception;
global $b_write_url_info;
global $b_write_security_debug;
global $b_write_post_files;
global $b_debug_sql_query;
global $b_debug_no_select_sql_query;
global $b_write_sql_queries;
global $b_transaction_active;

$b_write_main_exception = false;
$b_write_url_info = false;
$b_write_security_debug = false;
$b_write_post_files = false;
$b_debug_sql_query = false;
$b_debug_no_select_sql_query = false;
$b_write_sql_queries = false;
$b_transaction_active = false;

/* save current microtime globally to calculate runtime duration at the end */
global $start;
$start = microtime(true);

/* include root class forestPHP.php and creating new object of it */
if (@include_once './roots/forestPHP.php') {
	$o_forestPHP = new forestPHP();
} else {
	if (!(@include './roots/forestMaintenance.php')) {
		echo '<body>FATAL ERROR</body>';
	}
}