<?php
global $start;
global $b_write_sql_queries;
$end = microtime(true);
$laufzeit = $end - $start;
?>
</div>
<div>
&copy; 2019 by forestPHP.de Version 0.3.0 (beta)
</div>

<?php
if ($b_write_sql_queries) {
	echo '<hr><pre>';
	foreach ($o_glob->Base->{$o_glob->ActiveBase}->Queries as $query) {
		echo $query . '<br>';
	}
	echo '</pre>';
}
?>
</body>
</html>