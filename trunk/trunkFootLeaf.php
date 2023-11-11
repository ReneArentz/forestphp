<?php
/**
 * standard footer file of fphp framework
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestphp.de>
 * @copyright   (c) 2021 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.0.1 stable
 * @link        http://www.forestphp.de/
 * @object-id   0x1 0001D
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version		Developer	Date		Comment
 *              0.1.0 alpha	renatus		2019-08-04	first build
 *              0.9.0 beta	renatus		2020-01-27	added useful information only for RootUser
 */
?>
<div class="d-flex justify-content-center">
&copy; 2021 by forestPHP.de Version 1.0.1 (stable)
</div>
<?php
if ($o_glob->Security->RootUser) {
	global $start;
	$end = microtime(true);
	$f_runtime = $end - $start;
	
	echo '<div>' . "\n";
	echo '<pre>';
	echo '<hr>';
	echo $o_glob->GetTranslation('Runtime', 1) . ' ' . round($f_runtime, 3) . ' ' . $o_glob->GetTranslation('RuntimeSeconds', 1);
	echo '<hr>';
	echo $o_glob->Base->{$o_glob->ActiveBase}->AmountQueries . ' ' . $o_glob->GetTranslation('Queries', 1) . ' [' . $o_glob->Base->{$o_glob->ActiveBase}->BaseGateway . ']';
	echo '<hr>';
	echo 'memory_get_usage(false): ' . getNiceFileSize(memory_get_usage(false)) . '<br>';
	echo 'memory_get_usage(true): ' . getNiceFileSize(memory_get_usage(true)) . '<br>';
	echo 'memory_get_peak_usage(false): ' . getNiceFileSize(memory_get_peak_usage(false)) . '<br>';
	echo 'memory_get_peak_usage(true): ' . getNiceFileSize(memory_get_peak_usage(true)) . '<br>';
	
	global $b_write_sql_queries;

	if ($b_write_sql_queries) {
		echo '<hr>';
		foreach ($o_glob->Base->{$o_glob->ActiveBase}->Queries as $query) {
			echo $query . '<br>';
		}
	}
	
	echo '</pre>';
	echo '</div>' . "\n";
}
?>
</body>
</html>