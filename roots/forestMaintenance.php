<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.5.0 (0x1 0001D)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * Maintenance routine if any main exception occurs during runtime
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build
 */

/*
 * call global vars for main exception
 */
global $o_main_exception;
global $b_write_main_exception;

?>
<body>
<h1>Maintenance In Progress...</h1>
<?php
if (isset($o_main_exception)) {
	/* write exception message for information */
	if (!empty($o_main_exception->getMessage())) {
		echo '<br /><b>' . $o_main_exception->getMessage() . '</b>';
	}
	
	/* write detail exception information if global switch has been set to true in index.php */
	if ($b_write_main_exception) {
		echo '<br /><pre>' . $o_main_exception . '</pre>';
	}
}
?>
</body>