<?php
	session_start();
	$username = $_SESSION['username'];
	$sentence_id=$_REQUEST["sentence_id"];
	$response=$_REQUEST["response"];
	include_once("db.php");
	
	$sql = gen_select_query(array('screening'), array('Sentence'), array('id = '.$sentence_id));
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
	$screening = $results[0]['screening'];
	
	$sql = gen_select_query(array('text'), array('Sentence_Explanation', 'Explanation'), array('sentence_id = '.$sentence_id, 'Sentence_Explanation.explanation_id = Explanation.id'));
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
	$explanation = $results[0]['text'];
	if($screening == $response)
	{
		$explanation = $explanation . "^" . "Correct!";
	}
	else
	{
		$explanation = $explanation . "^" . "Wrong!";
	}

	$sql = gen_select_query(array('idx'), array('User_Training'), array('username = '.$username));
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
	$idx = $results[0]['idx'];
	
	if($idx < 40)
	{
		$idx = $idx+1;
		$sql = gen_update_query($tables=array('User_Training'), $fields=array('idx'), $values=array($idx), $where=array('username = '.$username));
		execute($sql, array(), PDO::FETCH_ASSOC);
	}	
	
	$sql = 'select USERNAME,ANSWERED, INCORRECT,
			if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*pow(0.6, A.SKIPPED/A.ANSWERED),2), -100000) as QUALITY,
			if(ANSWERED >= 0, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*ANSWERED/100*pow(0.6, A.SKIPPED/A.ANSWERED),2), -100000) as PAYMENT
			from (select 
			    Sentence_User.username as USERNAME, 
	
				round(-0.2*(sum(if(screening = -1 and response = -1, 1, 0))+sum(if(screening = 0 and response = 0, 1, 0))+sum(if(screening = 1 and response = 1, 1, 0)))/(sum(screening != -3 and response != -2))
				+0.7*(sum(if(screening = 0 and response = 1, 1, 0))+sum(if(screening = 1 and response = 0, 1, 0)))/(sum(screening != -3 and response != -2))
				+0.7*(sum(if(screening = -1 and response = 0, 1, 0))+sum(if(screening = 0 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2))
				+2.5*(sum(if(screening = -1 and response = 1, 1, 0))+sum(if(screening = 1 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2)), 3) as RANK_W,
	
				sum(if(Sentence_User.response != screening, if(Sentence_User.response != -2, 1, 0), 0)) as INCORRECT,
				sum(if(Sentence_User.response != -2, 1, 0)) as ANSWERED,
				sum(if(Sentence_User.response = -2, 1, 0)) as SKIPPED,
				avg(if(Sentence_User.response != -2, length, null)) as LEN
			from
			    Sentence_User,
			    Sentence,
				User
			where
			    id = sentence_id and			    
				Sentence_User.username = User.username and
				Sentence_User.username = '.$username.' and
				Sentence_User.time >= '.$_SESSION['fourth_phase_time_start'].' and
				sentence_id in '.$_SESSION['training_sentences'].'
			group by Sentence_User.username) A order by PAYMENT desc, ANSWERED desc;';
	
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
	
	echo $explanation. "^" . $idx. "^". $results[0]['INCORRECT']. "^". $results[0]['PAYMENT'];
?>
