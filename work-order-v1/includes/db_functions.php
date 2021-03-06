<?php 
	
	$db_user = 'root';
	$db_password = '';
	$db_host = 'localhost';
	$db_name = 'brgtools';
	
	// Make the connection:
	return $dbc = @mysqli_connect ($db_host, $db_user, $db_password, $db_name) OR die ('Could not connect to MySQL' );
?>
