<?php
	include_once("db.php");
	session_start();
	$username = $_SESSION['username'];
	
	$sql = gen_select_query(array('consent'), array('User'), array('username = '.$username.''));
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
	echo $results[0]['consent'];
?>
