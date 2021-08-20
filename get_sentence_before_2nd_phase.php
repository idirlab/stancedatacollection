<?php

	function randomFloat($min = 0, $max = 1)
	{
    	return $min + mt_rand() / mt_getrandmax() * ($max - $min);
	}
	
	session_start();
	$username = $_SESSION['username'];
	$sentence_id = $_REQUEST['sentence_id'];
	$min_number_of_users = 2;
	include_once("db.php");
	
	if($sentence_id == 0) #sentence_id not mentioned
	{
		$is_screening = (randomFloat() <= 0.10) ? 1 : 0;
		$_SESSION['answered'] = $_SESSION['answered'] + 1;
		if($_SESSION['answered']%10 == 0)
		{
			if($_SESSION['screening_questioned'] == 0)
			{
				$is_screening = 1;				
			}
			$_SESSION['answered'] = 0;
			$_SESSION['screening_questioned'] = 0;
		}
		else if($_SESSION['answered']%10 != 0)
		{
			if($is_screening == 1)
			{
				$_SESSION['screening_questioned'] = 1;
			}			
		}
		
		if($is_screening == 1 && strcmp($username, '"factchecker"') != 0) #screening question
		{
#			$sql = gen_select_query(array('sentence_id'), array('Sentence_User'), array('username = '.$username));	
#			$results = execute($sql, array(), PDO::FETCH_COLUMN);
#			if(count($results))$already_answered = '('.implode(', ', $results).')';
#			else $already_answered = '(-1)';
		
			$sql = gen_select_query(array('min(answered) as min_answered'), array('Sentence', 'Speaker', 'Speaker_File'), array('Sentence.speaker_id = Speaker.id', 'Speaker.id = Speaker_File.speaker_id', 'Sentence.file_id = Speaker_File.file_id', 'Sentence.length >= 5', 'Speaker_File.role = "Interviewee"', 'screening != -3'), array(), array(), array());
		
			$results = execute($sql, array(), PDO::FETCH_ASSOC);
			$min_answered = $results[0]['min_answered'];
			
			$class_screening = randomFloat(1,30);
			if($class_screening <= 10)
			{
				$class_screening = '-1';
			}
			else if($class_screening <= 20)
			{
				$class_screening = '0';
			}
			else if($class_screening <= 30)
			{
				$class_screening = '1';
			}
	
			$sql = gen_select_query(array('Sentence.id', 'Speaker.shortform as name', 'Sentence.text'), array('Sentence', 'Speaker', 'Speaker_File'), array('Sentence.speaker_id = Speaker.id', 'Speaker.id = Speaker_File.speaker_id', 'Sentence.file_id = Speaker_File.file_id', 'Sentence.length >= 5', 'Speaker_File.role = "Interviewee"', 'screening = '.$class_screening), array(), array('RAND()'), array('1'));
		}
		else #regular question
		{
			$sql = gen_select_query(array('sentence_id'), array('Sentence_User'), array('username = '.$username));	
			$results = execute($sql, array(), PDO::FETCH_COLUMN);
			if(count($results))$already_answered = '('.implode(', ', $results).')';
			else $already_answered = '(-1)';
		
			$sql = gen_select_query(array('min(answered) as min_answered'), array('Sentence', 'Speaker', 'Speaker_File'), array('Sentence.speaker_id = Speaker.id', 'Speaker.id = Speaker_File.speaker_id', 'Sentence.file_id = Speaker_File.file_id', 'Sentence.id NOT IN '.$already_answered, 'Sentence.length >= 5', 'Speaker_File.role = "Interviewee"', 'screening = -3'), array(), array(), array());
		
			$results = execute($sql, array(), PDO::FETCH_ASSOC);
			$min_answered = $results[0]['min_answered'];
	
			$sql = gen_select_query(array('Sentence.id', 'Speaker.shortform as name', 'Sentence.text'), array('Sentence', 'Speaker', 'Speaker_File'), array('Sentence.speaker_id = Speaker.id', 'Speaker.id = Speaker_File.speaker_id', 'Sentence.file_id = Speaker_File.file_id', 'Sentence.id NOT IN '.$already_answered, 'Sentence.length >= 5', 'Speaker_File.role = "Interviewee"', 'answered < '.$min_number_of_users, 'screening = -3'), array(), array('Sentence.file_id desc', 'answered', 'RAND()'), array('1'));
		}		
	}
	else #sentence_id mentioned
	{
		$sql = gen_select_query(array('Sentence.id', 'Speaker.shortform as name', 'Sentence.text'), array('Sentence', 'Speaker'), array('Sentence.speaker_id = Speaker.id', 'Sentence.id = '.$sentence_id), array(), array(), array());
	}
	
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
	if(count($results))
	{
		$sql = 'select A.USERNAME, A.ANSWERED,

(case when A.RANK_W <= 0.0 and A.ANSWERED >= 4 then 1
   when A.RANK_W <= 0.3 and A.ANSWERED >= 4  then 2
   when A.RANK_W <= 0.6 and A.ANSWERED >= 4  then 3
   when A.RANK_W > 0.6 and A.ANSWERED >= 4  then 4
	else 0
         end) as REGION

from (select 
    Sentence_User.username as USERNAME, 

	round(-0.2*(sum(if(screening = -1 and response = -1, 1, 0))+sum(if(screening = 0 and response = 0, 1, 0))+sum(if(screening = 1 and response = 1, 1, 0)))/(sum(screening != -3 and response != -2))
	+1.0*(sum(if(screening = 0 and response = 1, 1, 0))+sum(if(screening = 1 and response = 0, 1, 0)))/(sum(screening != -3 and response != -2))
	+1.0*(sum(if(screening = -1 and response = 0, 1, 0))+sum(if(screening = 0 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2))
	+3.0*(sum(if(screening = -1 and response = 1, 1, 0))+sum(if(screening = 1 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2)), 3) as RANK_W,
	
	count(*) as ANSWERED
	
from
    Sentence_User,
    Sentence,
	User
where
    id = sentence_id and
	Sentence_User.username = User.username and
	Sentence_User.username = '.$username.' and
	Sentence_User.response != -2 and
	screening != -3) A';
		$results_status = execute($sql, array(), PDO::FETCH_ASSOC);

		if(1)//$results_status[0]['REGION'] != $_SESSION['REGION']// 1 means for every response, do update.
		{
			$results[0]['REGION'] = $results_status[0]['REGION'];
		}
		else
		{
			$results[0]['REGION'] = 0;
		}
		$_SESSION['REGION'] = $results_status[0]['REGION'];
		
		echo json_encode($results[0]);
		$_SESSION['sentence_id'] = $results[0]['id'];
	}
	else echo "-1";
?>
