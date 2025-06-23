<?php
	include_once("db.php");
	session_start(['cookie_lifetime' => 86400,]);
	$username = $_SESSION['username'];
	
	$sql = gen_update_query(array('User'), array('consent'), array('1'), array('username = '.$username.''));
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
?>
