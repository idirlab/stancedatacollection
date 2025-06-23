<?php
	session_start(['cookie_lifetime' => 86400,]);
	$username = $_SESSION['username'];
	include_once("db.php");
	# TODO: add wildfire version
	// $sql = gen_select_query(array('Sentence.id', 'Speaker.shortform as name', 'Sentence.text', 'Sentence_User.response', 'Sentence_User.time'), 
	// 						array('Sentence', 'Speaker', 'Sentence_User'), 
	// 						array('Sentence_User.username = '.$username, 'Sentence_User.sentence_id = Sentence.id', 'Sentence.speaker_id = Speaker.id', 'Sentence.id not in '.$_SESSION['training_sentences'].' ', 'Sentence_User.time >= '.$_SESSION['fourth_phase_time_start']), 
	// 						array(), array('Sentence_User.time desc'), array());	
	$sql = gen_select_query(array('Sentence.id', 'Sentence.tweet', 'Sentence.claim', 'Sentence_User.response', 'Sentence_User.time'), 
							array('Sentence', 'Sentence_User'), 
							array('Sentence_User.time >= '.$_SESSION['pomodoro_phase_time_start'], 'Sentence_User.username = '.$username, 'Sentence_User.sentence_id = Sentence.id', 'Sentence.id not in '.$_SESSION['training_sentences'].' '), 
							array(), array('Sentence_User.time desc'), array());	

	$results = execute($sql, array(), PDO::FETCH_ASSOC);
	
	$activity_sql = gen_insert_query($tables=array('Activity'), $fields=array('username', 'time', 'action'), $values=array($username, '"'.date("Y-m-d H:i:s").'"', "'USER CLICKED MODIFY PREVIOUS RESPONSES'"));
	$activity_sql_results = execute($activity_sql, array(), PDO::FETCH_ASSOC);
	echo json_encode($results);
?>
