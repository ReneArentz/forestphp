<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.1.2 (0x1 0001C)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * standard head file of fphp framework
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build
 * 0.1.1 alpha	renatus		2019-08-15	added navigation
 */
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Title of the document</title>
	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel="stylesheet" href="./src/bootstrap.min.css">
	<link rel="stylesheet" href="./src/jquery-ui.min.css">
	<link rel="stylesheet" href="./src/forestPHP.css">
	
	<script src="./src/jquery.min.js"></script>
	<script src="./src/jquery.validate.min.js"></script>
	<script src="./src/jquery-ui.min.js"></script>
	<script src="./src/bootstrap.min.js"></script>
	
	<script src="./src/forestPHP.js"></script>
	
</head>
<body>
<?php
if ($o_glob->URL->ShowNavigation) {
	$o_glob->Navigation->RenderNavigation();
}
?>

<div class="container-fluid">

	<?php if (!is_null($o_glob->URL->BranchTitle)) : ?>
	<h1><?php echo $o_glob->URL->BranchTitle; ?></h1>
	<?php endif; ?>