<?php
	session_start();
	$sentence_id=$_REQUEST["sentence_id"];
	include_once("db.php");
	$sql = gen_select_query(array('Sentence.id', 'Speaker.name', 'Sentence.text'), array('Sentence', 'Speaker'), array('Sentence.speaker_id = Speaker.id', 'Sentence.id ='.$sentence_id.' '), array(), array(), array());
	
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
	echo json_encode($results);
?>
