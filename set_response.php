<?php
	$sentence_id=$_REQUEST["sentence_id"];
	$response=$_REQUEST["response"];
	$context_seen=$_REQUEST["context_seen"];
	include_once("db.php");
	session_start(['cookie_lifetime' => 86400,]);
	$username = $_SESSION['username'];
	
	$sql = gen_select_query(array('count(*) as count', 'response'), array('Sentence_User'), 
							array('Sentence_User.username = '.$username, 'Sentence_User.sentence_id = '.$sentence_id), array(), array(), array());	
	// var_dump($sql);
	if($sentence_id && $username) $results = execute($sql, array(), PDO::FETCH_ASSOC);
	// var_dump($results);
	if($results[0]['count'] == 0) # a new response
	{
		if(strcmp($response, '-2') != 0 && strcmp($username,'"factchecker"') != 0)
		{
			$sql = gen_update_query(array('Sentence'), array('answered'), array('answered+1'), array('id = '.$sentence_id));
			// var_dump($sql);
			if($sentence_id) $results = execute($sql, array(), PDO::FETCH_ASSOC);
		}
		$sql = gen_insert_query(array('Sentence_User'), array('sentence_id', 'username', 'response', 'context_seen', 'time'), array($sentence_id, $username, $response, $context_seen, '"'.date("Y-m-d H:i:s").'"'));		
	}
	else if($results[0]['count'] == 1) #responded before
	{
		if($results[0]['response'] == -2 && strcmp($username,'"factchecker"') != 0)
		{
			if(strcmp($response, '-2') != 0)
			{
				$sql = gen_update_query(array('Sentence'), array('answered'), array('answered+1'), array('id = '.$sentence_id));
				if($sentence_id)$results = execute($sql, array(), PDO::FETCH_ASSOC);
			}
		}
		else if($results[0]['response'] != -2 && strcmp($username,'"factchecker"') != 0)
		{
			if(strcmp($response, '-2') == 0)
			{
				$sql = gen_update_query(array('Sentence'), array('answered'), array('answered-1'), array('id = '.$sentence_id));
				if($sentence_id)$results = execute($sql, array(), PDO::FETCH_ASSOC);
			}
		}
		
		$sql = gen_update_query(array('Sentence_User'), array('response', 'context_seen', 'time'), array($response, $context_seen, '"'.date("Y-m-d H:i:s").'"'), array('Sentence_User.username = '.$username, 'Sentence_User.sentence_id = '.$sentence_id));
	}
		
	if($sentence_id && $username)$results = execute($sql, array(), PDO::FETCH_ASSOC);
	
	$action = "'USER LABELLED'";
	if(strcmp($response, '-2') == 0)
	{
		$action = "'USER SKIPPED'";
	}
	$activity_sql = gen_insert_query($tables=array('Activity'), $fields=array('username', 'time', 'action', 'sentence_id', 'response', 'context_seen'), $values=array($username, '"'.date("Y-m-d H:i:s").'"', $action, $sentence_id, $response, $context_seen));
	if($sentence_id && $username)$results = execute($activity_sql, array(), PDO::FETCH_ASSOC);
?>
