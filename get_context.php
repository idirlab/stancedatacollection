<?php
	session_start();
	$sentence_id=$_REQUEST["sentence_id"];

	include_once("db.php");

	$sql = gen_select_query(array('Sentence.claim_author', 'Sentence.tweet_author', 'Sentence.claim_timestamp', 'Sentence.factcheck_timestamp', 'Sentence.factcheck_source', 'Sentence.claim_verdict'), 
							array('Sentence'), 
							array('Sentence.id = '.$sentence_id.' '), 
							array(), array(), array());
							
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
	echo json_encode($results);
?>
