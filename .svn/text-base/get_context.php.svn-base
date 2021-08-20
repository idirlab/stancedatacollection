<?php
	$sentence_id=$_REQUEST["sentence_id"];
	include_once("db.php");
	
	$sql = gen_select_query(array('Sentence.id', 'Speaker.shortform as name', 'Sentence.text'), array('Sentence', 'Speaker'), array('Sentence.speaker_id = Speaker.id', 'Sentence.id <='.$sentence_id.' ', 'Sentence.id >='.$sentence_id.'-4'), array(), array(), array());
#	echo $sql;	
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
	echo json_encode($results);
?>
