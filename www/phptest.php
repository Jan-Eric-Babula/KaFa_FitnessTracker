<?php include "_sql.php"; ?>
<html><body>

Test: <?php echo("Test"); ?> 
<br>
<?php print_r($_GET); ?>
<br>
<?php
$con = sql_get_connection();
print_r($con);
sql_close_connection($con);
?>
</body></html>
