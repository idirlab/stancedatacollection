<?php
	$feedback=$_REQUEST["feedback"];
	$feedback_type=$_REQUEST['feedback_type'];
	include_once("db.php");
	session_start(['cookie_lifetime' => 86400,]);
	
	if (isset($_SESSION['username']))
	{
		$username = str_replace('"','',$_SESSION['username']);
	}
	else
	{
		$username = "NULL";
	}
	
	if (isset($_SESSION['sentence_id']))
	{
		$sentence_id = $_SESSION['sentence_id'];
	}
	else
	{
		$sentence_id = "NULL";
	}
	
	$time = date("Y-m-d H:i:s");
	
#	$sql = gen_insert_query(array('Feedback'), array('id', 'username', 'sentence_id', 'text', 'time', 'type'), array('NULL', $username, $sentence_id, $feedback, $time, $feedback_type));
	
	$sql = 'INSERT INTO Feedback (id, username, sentence_id, text, time, type) VALUES (?, ?, ?, ?, ?, ?)';
	$results = execute($sql, array('NULL', $username, $sentence_id, $feedback, $time, $feedback_type), PDO::FETCH_ASSOC);	
?>
