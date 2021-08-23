<?php
	session_start();
	$username = $_SESSION['username'];
	include_once("db.php");
	
	$sql = gen_select_query(array('idx'), array('User_Training'), array('username = '.$username));
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
	$idx = 0;
	if(count($results) == 0)
	{
		$sql = gen_insert_query($tables=array('User_Training'), $fields=array('username', 'idx'), $values=array($username, $idx));
		execute($sql, array(), PDO::FETCH_ASSOC);		
	}
	else
	{
		$idx = $results[0]['idx'];
	}
	
	if($idx == 40)
	{
		echo "0";
	}
	else
	{
		$idx = $idx + 1;
		$sql = gen_select_query(array('sentence_id'), array('Training'), array('idx = '.$idx));
		$results = execute($sql, array(), PDO::FETCH_ASSOC);
		echo $results[0]['sentence_id'].'^'.$idx;
	}
?>
