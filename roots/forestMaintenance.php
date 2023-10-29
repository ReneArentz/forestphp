<?php
/**
 * maintenance routine if any main exception occurs during runtime
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestphp.de>
 * @copyright   (c) 2019 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.0.0 stable
 * @link        http://www.forestphp.de/
 * @object-id   0x1 00020
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version		Developer	Date		Comment
 * 		0.1.0 alpha	renatus		2019-08-04	first build
 */
/*
 * call global vars for main exception
 */
global $o_main_exception;
global $b_write_main_exception;

?>
	<h1>Maintenance In Progress...</h1>
<?php
if (isset($o_main_exception)) {
	/* write exception message for information */
	if (!empty($o_main_exception->getMessage())) {
		echo '<br>' . "\t" . '<b>' . $o_main_exception->getMessage() . '</b>';
	}
	
	/* write detail exception information if global switch has been set to true in index.php */
	if ($b_write_main_exception) {
		echo '<br>' . "\t" . '<pre>' . $o_main_exception . '</pre>';
	}
}
?>
</body>
</html>