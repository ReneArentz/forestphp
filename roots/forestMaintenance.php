<?php
/**
 * maintenance routine if any main exception occurs during runtime
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestany.net>
 * @copyright   (c) 2024 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.1.0 stable
 * @link        https://forestany.net
 * @object-id   0x1 00020
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version			Developer	Date		Comment
 * 				0.1.0 alpha		renea		2019-08-04	first build
 * 				1.1.0 stable	renea		2024-06-05	revision of maintenance page
 * 				1.1.0 stable	renea		2024-08-10	changes for bootstrap 5
 */

/*
 * call global vars for main exception
 */
global $o_main_exception;
global $b_write_main_exception;

echo "\n" . "\t" . '<div class="container">' . "\n";

echo "\t" . "\t" . '<div class="alert alert-warning" role="alert">' . "\n";
echo "\t" . "\t" . "\t" . '<h4 class="alert-heading"><span class="bi bi-cone-striped h2"></span>&nbsp;Maintenance&nbsp;<span class="bi bi-cone-striped h2"></span></h4>' . "\n";
echo "\t" . "\t" . "\t" . '<p>This website is currently in maintenance mode.</p>' . "\n";
  
if (isset($o_main_exception)) {

	echo "\t" . "\t" . "\t" . '<hr>' . "\n";
  	
	/* write exception message for information */
	if (!empty($o_main_exception->getMessage())) {
		echo "\t" . "\t" . "\t" . '<p class="mb-0">' . $o_main_exception->getMessage() . '</p>' . "\n" ;
	}

	/* write detail exception information if global switch has been set to true in index.php */
	if ($b_write_main_exception) {
		echo "\t" . "\t" . "\t" . '<hr>' . "\n";
		echo "\t" . "\t" . "\t" . '<pre>' . $o_main_exception . '</pre>' . "\n" ;
	}
}

echo "\t" . "\t" . '</div>' . "\n";
echo "\t" . '</div>' . "\n";

?>
</body>
</html>