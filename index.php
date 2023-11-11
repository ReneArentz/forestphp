<?php
/**
 * index-page of the forestPHP framework
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestphp.de>
 * @copyright   (c) 2021 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.0.1 stable
 * @link        http://www.forestphp.de/
 * @object-id   0x1 00000
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version		Developer	Date		Comment
 *              0.1.0 alpha	renatus		2019-08-04	first build
 *              0.9.0 beta	renatus		2020-01-27	added general functions
 *              1.0.0 stable	renatus		2020-02-10	added global flags for testing DDL and filling data from SQLite3 DB into a MongoDB
 *              1.0.1 stable	renatus		2021-04-09	added support for samesite cookie property and automatically retrieve domain information for cookie settings
 *              1.0.1 stable	renatus		2021-04-10	added new helper function arrayToInlineString
 */

/* display options for error reporting, useful for debugging */
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/* activate output buffer for content scripting */
ob_start();

/* initalize cookie settings */
$l_maxlifetime = 2 * 24 * 60 * 60; /* life duration of cookie: 2 days */
$s_host = ( (strpos($_SERVER['HTTP_HOST'], ':') === false) ? $_SERVER['HTTP_HOST'] : explode(':', $_SERVER['HTTP_HOST'])[0] ); /* get domain from server http host */
$b_secure = true; /* if you only want to receive the cookie over HTTPS */
$b_httponly = true; /* prevent JavaScript access to session cookie */

/**
 * browsers will not enforce SameSite rules at all. Even if browsers
 * start to treat cookies without this flag present as Lax (which is
 * the case for Chrome 80 and later), setting None will disable this protection.
 */
//$s_samesite = 'None';

/**
 * that cookie will not be passed for any cross-domain requests
 * unless it's a regular link that navigates user to the target site.
 * Other requests methods (such as POST and PUT) and XHR requests
 * will not contain this cookie.
 */
//$s_samesite = 'Lax';

/**
 * that cookie will not be sent for any cross-domain requests whatsoever.
 * Even if the user simply navigates to the target site with a regular link,
 * the cookie will not be sent. This might lead to some confusing or downright
 * impractical user experiences, so be careful if you use Strict cookies.
 */
$s_samesite = 'Strict';

session_name('forestPHPSession'); /* unqiue name per web application */

if (PHP_VERSION_ID < 70300) {
	session_set_cookie_params($l_maxlifetime, '/; samesite=' . $s_samesite, $s_host, $b_secure, $b_httponly);
} else {
	session_set_cookie_params([
		'lifetime' => $l_maxlifetime,
		'path' => '/',
		'domain' => $s_host,
		'secure' => $b_secure,
		'httponly' => $b_httponly,
		'samesite' => $s_samesite
	]);
}

session_start();
session_regenerate_id(false);

/* global vars for exception rendering and debug usage */
global $o_main_exception;
global $b_write_main_exception;
global $b_write_url_info;
global $b_write_debug_globals;
global $b_write_security_debug;
global $b_write_post_files;
global $b_debug_sql_query;
global $b_debug_no_select_sql_query;
global $b_write_sql_queries;
global $b_transaction_active;
global $b_run_testddl;
global $b_run_testddl_embedded;
global $b_fill_mongodb_from_sqlite3;

$b_write_main_exception = false;
$b_write_url_info = false;
$b_write_debug_globals = false;
$b_write_security_debug = false;
$b_write_post_files = false;
$b_debug_sql_query = false;
$b_debug_no_select_sql_query = false;
$b_write_sql_queries = false;
$b_transaction_active = false;
$b_run_testddl = false;
$b_run_testddl_embedded = false;
$b_fill_mongodb_from_sqlite3 = false;

/* save current microtime globally to calculate runtime duration at the end */
global $start;
$start = microtime(true);

/* include root class forestPHP.php and creating new object of it */
if (@include_once './roots/forestPHP.php') {
	$o_forestPHP = new fPHP\Roots\forestPHP();
} else {
	if (!(@include './roots/forestMaintenance.php')) {
		echo '<body>FATAL ERROR</body>';
	}
}

/**
 **************************************
 ********* GENERAL FUNCTIONS **********
 **************************************
 */

/**
 * helper function - get value of 2-dimensional key array
 *
 * @param array $p_a_var  array variable
 * @param string $p_s_key  string index which must exist in array
 *
 * @return object  object behind index in array or null
 *
 * @access public
 * @static no
 */
function get(array $p_a_var, $p_s_key) {
    if (array_key_exists($p_s_key, $p_a_var)) {
    	return $p_a_var[$p_s_key];
    }
	
    return null;
}

/**
 * helper function - check if forestString is empty - even if 'NULL' is set
 *
 * @param string $p_s_str  string value which will be checked
 *
 * @return bool  true - string isset, false - string is not set
 *
 * @access public
 * @static no
 */
function issetStr($p_s_str) {
	if ($p_s_str === true) {
		return true;
	}
	
	if ( (empty($p_s_str)) || ($p_s_str == 'NULL') ) {
		return false;
	} else {
		return true;
	}
}

/**
 * helper function - debug array object and print elements with print_r function to output
 *
 * @param array $p_a_array  array object
 *
 * @return string  printed contents of array object
 *
 * @access public
 * @static no
 */
function debugArray(array $p_a_array) {
	echo '<br><pre>';
	print_r($p_a_array);
	echo '</pre><br>';
}

/**
 * helper function - debug array object and print elements as inline string to output
 *
 * @param array $p_a_array  array object
 *
 * @return string  printed contents of array object as inline string
 *
 * @access public
 * @static no
 */
function arrayToInlineString(array $p_a_array) {
	$s_foo = '[';
	
	foreach ($p_a_array as $o_value) {
		$s_foo .= $o_value . ',';
	}
	
	if (\fPHP\Helper\forestStringLib::EndsWith($s_foo, ',')) {
		$s_foo = substr($s_foo, 0, -1);
	}
	
	$s_foo .= ']';
	
	return $s_foo;
}

/**
 * helper function - get class name string of an object if its origin is unkown
 *
 * @param string $p_o_value  object as instance of an class
 *
 * @return string  class name
 *
 * @access public
 * @static no
 */
function getClass($p_o_value) {
	return get_class($p_o_value);
}

/**
 * help function: debug to javascript console
 *
 * @param string $p_s_msg  message which will be displayed in javascript console
 *
 * @return string  debug message with script tags
 *
 * @access public
 * @static no
 */
function d2c($p_s_msg) {
    $p_s_msg = str_replace('"', "''", $p_s_msg);
    echo "<script>console.debug( \"forestPHP DEBUG: $p_s_msg\" );</script>";
}

/**
 * help function: format file size to readable values
 *
 * @param int $bytes  amount of bytes
 * @param bool $binaryPrefix
 *
 * @return float
 *
 * @access public
 * @static no
 */
function getNiceFileSize($bytes, $binaryPrefix=true) {
	if ($binaryPrefix) {
		$unit=array('B','KiB','MiB','GiB','TiB','PiB');
		if ($bytes==0) return '0 ' . $unit[0];
		return @round($bytes/pow(1024,($i=floor(log($bytes,1024)))),2) .' '. (isset($unit[$i]) ? $unit[$i] : 'B');
	} else {
		$unit=array('B','KB','MB','GB','TB','PB');
		if ($bytes==0) return '0 ' . $unit[0];
		return @round($bytes/pow(1000,($i=floor(log($bytes,1000)))),2) .' '. (isset($unit[$i]) ? $unit[$i] : 'B');
	}
}
