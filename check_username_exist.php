<?php
	session_start(['cookie_lifetime' => 86400,]);
	$username=$_REQUEST["username"];
	$email=$_REQUEST["email"];

	include_once("db.php");
	
	$sql = gen_select_query(array('count(*) as count'), array('User'), array('(username = '.$username.' OR email = '.$email.')'));
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
	echo $results[0]['count'];
?>
