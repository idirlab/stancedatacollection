<?php
	session_start();
	$username = $_SESSION['username'];
	include_once("db.php");

	if($_SESSION['project']=='"ClaimBuster"') {
		$sql = gen_select_query(array('count(*) as count'), array('Sentence_User'), array('username = '.$username, 'response != -2', 'time >= '.$_SESSION['fourth_phase_time_start'], 'sentence_id not in '.$_SESSION['training_sentences'].' '));	
		$results = execute($sql, array(), PDO::FETCH_ASSOC);
		if($results[0]['count'] == 50) {
			$_SESSION['message_counter'] = 0;
		}
	} elseif ($_SESSION['project']=='"WildFire"') {
		$sql = gen_select_query(array('count(*) as count'), array('Sentence_User'), array('username = '.$username, 'response != -2', 'sentence_id not in '.$_SESSION['training_sentences'].' '));	
		$results = execute($sql, array(), PDO::FETCH_ASSOC);
		if($results[0]['count'] == 50) {
			$_SESSION['message_counter'] = 0;
		}
	}
	echo $results[0]['count'].'^'.$username;
?>
