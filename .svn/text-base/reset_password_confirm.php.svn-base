<?php
	$password = $_REQUEST["password"];
	$username = $_REQUEST["username"];
	$encrypted_password = '"'.md5($password).'"';
	include_once("db.php");	
	
	$sql = gen_update_query(array('User'), array('password', 'verification'), array($encrypted_password, '"verified"'), array('username = "'.$username.'"'));
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
	
	if(true)#will fix this later inshaAllah
	{
		echo "Your password is reset successfully!";
	}
	else
	{
		echo "There is some error with password reset. Please contact classifyfact@gmail.com";
	}
?>
