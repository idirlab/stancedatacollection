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
	
	
	// checking if the user has any sentence available to label.
	$sql = gen_select_query(array('sentence_id'), array('Sentence_User'), array('username = '.$username));	
	$results = execute($sql, array(), PDO::FETCH_COLUMN);
	if(count($results)) $already_answered = '('.implode(', ', $results).')';
	else $already_answered = '(-1)';

	if ($_SESSION['project']=='"ClaimBuster"') {
		$training_sentences = '(129, 1576, 3110, 3429, 4390, 5553, 5562, 5654, 5974, 6002, 6483, 7600, 9017, 9355, 9862, 10060, 10762, 10863, 11025, 11112, 14933, 611, 15445, 15602, 15763, 16014, 16015, 16258, 16828, 17000, 17159, 17420, 17509, 21636, 24352, 26145, 27100, 27828, 27986, 28777)'; /*May 26, 2016*/
		$sql = 'select Sentence_User.username as USERNAME	
			from
				Sentence_User,
				Sentence
			where
				id = sentence_id and
				username not in ("cmavs2015", "sakiforu", "teaphony") and
				response != -2 and
				sentence_id not in '.$training_sentences.'
				group by Sentence_User.username
			having 
				-0.2*(sum(if(screening = -1 and response = -1, 1, 0))+sum(if(screening = 0 and response = 0, 1, 0))+sum(if(screening = 1 and response = 1, 1, 0)))/(sum(screening != -3 and response != -2))
				+0.7*(sum(if(screening = 0 and response = 1, 1, 0))+sum(if(screening = 1 and response = 0, 1, 0)))/(sum(screening != -3 and response != -2))
				+0.7*(sum(if(screening = -1 and response = 0, 1, 0))+sum(if(screening = 0 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2))
				+2.5*(sum(if(screening = -1 and response = 1, 1, 0))+sum(if(screening = 1 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2)) <= 0.0 and count(*) >= 50';	
		$top_participants = execute($sql, array(), PDO::FETCH_COLUMN);
		$top_participants_string = '("'.implode('","', $top_participants).'")';

		$sql = 'select sentence_id from (
				select sentence_id, 
				sum(if(response = -1, 1, 0)) as nfs, 
				sum(if(response = 0, 1, 0)) as ufs, 
				sum(if(response = 1, 1, 0)) as cfs
				from Sentence_User, Sentence 
				where Sentence.id = Sentence_User.sentence_id 
				and screening = -3
				and username in '.$top_participants_string.'group by sentence_id
				having (nfs >= 3 and nfs >= 2+ufs and nfs >= 2+cfs)
				or (ufs >= 3 and ufs >= 2+nfs and ufs >= 2+cfs)
				or (cfs >= 3 and cfs >= 2+ufs and cfs >= 2+nfs)
			) A';

		$top_quality_sentences = execute($sql, array(), PDO::FETCH_COLUMN);
		$top_quality_sentences_string = '("'.implode('","', $top_quality_sentences).'")';

		$sql = gen_select_query(array('Sentence.id', 'Speaker.shortform as name', 'Sentence.text'), 
								array('Sentence', 'Speaker', 'Speaker_File'), 
								array('Sentence.speaker_id = Speaker.id', 'Speaker.id = Speaker_File.speaker_id', 'Sentence.file_id = Speaker_File.file_id', 'Sentence.id NOT IN '.$already_answered, 'Sentence.id NOT IN '.$top_quality_sentences_string, 'Sentence.length >= 5', 'Speaker_File.role = "Interviewee"', 'screening = -3'), 
								array(), array('RAND()'), array('1'));
		
		$results = execute($sql, array(), PDO::FETCH_ASSOC);
		
		if(!count($results) && $sentence_id == 0)//No more sentence available for the user.
		{
			$activity_sql = gen_insert_query($tables=array('Activity'), $fields=array('username', 'time', 'action'), $values=array($username, '"'.date("Y-m-d H:i:s").'"', '"NO MORE SENTENCE AVAILABLE"'));
			$activity_sql_results = execute($activity_sql, array(), PDO::FETCH_ASSOC);
			echo "-1";
			exit();
		}

		if($sentence_id == 0) #sentence_id not mentioned. Not a 'Change' or 'Go Back' scenario.
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
				$class_screening = randomFloat(0,30);
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
		
				$sql = gen_select_query(array('Sentence.id', 'Speaker.shortform as name', 'Sentence.text'), array('Sentence', 'Speaker', 'Speaker_File'), array('Sentence.speaker_id = Speaker.id', 'Speaker.id = Speaker_File.speaker_id', 'Sentence.file_id = Speaker_File.file_id', 'Sentence.length >= 5', 'Speaker_File.role = "Interviewee"', 'screening = '.$class_screening, 'Sentence.id not in '.$_SESSION['training_sentences'].' '), array(), array('RAND()'), array('1'));
			}
			else #regular question
			{
				$sql = gen_select_query(array('sentence_id'), array('Sentence_User'), array('username = '.$username));	
				$results = execute($sql, array(), PDO::FETCH_COLUMN);
				if(count($results))$already_answered = '('.implode(', ', $results).')';
				else $already_answered = '(-1)';
			
				$sql = 'select 
					Sentence_User.username as USERNAME	
				from
					Sentence_User,
					Sentence
				where
					id = sentence_id and
					username not in ("cmavs2015", "sakiforu", "teaphony") and
					response != -2 and
					sentence_id not in '.$training_sentences.'
				group by Sentence_User.username

				having 

					-0.2*(sum(if(screening = -1 and response = -1, 1, 0))+sum(if(screening = 0 and response = 0, 1, 0))+sum(if(screening = 1 and response = 1, 1, 0)))/(sum(screening != -3 and response != -2))
					+0.7*(sum(if(screening = 0 and response = 1, 1, 0))+sum(if(screening = 1 and response = 0, 1, 0)))/(sum(screening != -3 and response != -2))
					+0.7*(sum(if(screening = -1 and response = 0, 1, 0))+sum(if(screening = 0 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2))
					+2.5*(sum(if(screening = -1 and response = 1, 1, 0))+sum(if(screening = 1 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2)) <= 0.0 and count(*) >= 50';	
				$top_participants = execute($sql, array(), PDO::FETCH_COLUMN);
				$top_participants_string = '("'.implode('","', $top_participants).'")';

				$sql = 'select sentence_id from (
				select sentence_id, 
				sum(if(response = -1, 1, 0)) as nfs, 
				sum(if(response = 0, 1, 0)) as ufs, 
				sum(if(response = 1, 1, 0)) as cfs
				from Sentence_User, Sentence 
				where Sentence.id = Sentence_User.sentence_id 
				and screening = -3
				and username in '.$top_participants_string.'group by sentence_id
				having (nfs >= 3 and nfs >= 2+ufs and nfs >= 2+cfs)
				or (ufs >= 3 and ufs >= 2+nfs and ufs >= 2+cfs)
				or (cfs >= 3 and cfs >= 2+ufs and cfs >= 2+nfs)
				) A';

				$top_quality_sentences = execute($sql, array(), PDO::FETCH_COLUMN);
				$top_quality_sentences_string = '("'.implode('","', $top_quality_sentences).'")';

				$sql = gen_select_query(array('Sentence.id', 'Speaker.shortform as name', 'Sentence.text'), array('Sentence', 'Speaker', 'Speaker_File'), array('Sentence.speaker_id = Speaker.id', 'Speaker.id = Speaker_File.speaker_id', 'Sentence.file_id = Speaker_File.file_id', 'Sentence.id NOT IN '.$already_answered, 'Sentence.id NOT IN '.$top_quality_sentences_string, 'Sentence.length >= 5', 'Speaker_File.role = "Interviewee"', 'screening = -3'), array(), array('answered', 'RAND()'), array('1'));
			}		
		}
		else #sentence_id mentioned
		{				
			$action = "'USER CLICKED CHANGE'";
			if($sentence_id < 0)
			{
				$action = "'USER CLICKED GO BACK'";
				$sentence_id = $sentence_id * (-1);
			}
			$activity_sql = gen_insert_query($tables=array('Activity'), $fields=array('username', 'time', 'action', 'sentence_id'), $values=array($username, '"'.date("Y-m-d H:i:s").'"', $action, $sentence_id));
			$activity_sql_results = execute($activity_sql, array(), PDO::FETCH_ASSOC);
			
			$sql = gen_select_query(array('Sentence.id', 'Speaker.shortform as name', 'Sentence.text'), array('Sentence', 'Speaker'), array('Sentence.speaker_id = Speaker.id', 'Sentence.id = '.$sentence_id), array(), array(), array());
		}
		
		$results = execute($sql, array(), PDO::FETCH_ASSOC); // [id, name, text]
		
		if(count($results)) {
			$sql = 'select A.USERNAME, A.ANSWERED,
					(case when A.RANK_W <= 0.0 and A.ANSWERED >= 4 then 1
						when A.RANK_W <= 0.3 and A.ANSWERED >= 4  then 2
						when A.RANK_W <= 0.6 and A.ANSWERED >= 4  then 3
						when A.RANK_W > 0.6 and A.ANSWERED >= 4  then 4
						else 0
							end) as REGION
					from (select Sentence_User.username as USERNAME, 
						round(-0.2*(sum(if(screening = -1 and response = -1, 1, 0))+sum(if(screening = 0 and response = 0, 1, 0))+sum(if(screening = 1 and response = 1, 1, 0)))/(sum(screening != -3 and response != -2))
						+0.7*(sum(if(screening = 0 and response = 1, 1, 0))+sum(if(screening = 1 and response = 0, 1, 0)))/(sum(screening != -3 and response != -2))
						+0.7*(sum(if(screening = -1 and response = 0, 1, 0))+sum(if(screening = 0 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2))
						+2.5*(sum(if(screening = -1 and response = 1, 1, 0))+sum(if(screening = 1 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2)), 3) as RANK_W,	
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
			
			
			$_SESSION['sentence_id'] = $results[0]['id'];
			
			$sql = 'select USERNAME,ANSWERED,
					if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*pow(0.6, A.SKIPPED/A.ANSWERED),2), 0) as QUALITY,
					if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*ANSWERED/100*pow(0.6, A.SKIPPED/A.ANSWERED),2), 0) as PAYMENT
					from (select 
					Sentence_User.username as USERNAME, 
					
					round(-0.2*(sum(if(screening = -1 and response = -1, 1, 0))+sum(if(screening = 0 and response = 0, 1, 0))+sum(if(screening = 1 and response = 1, 1, 0)))/(sum(screening != -3 and response != -2))
					+0.7*(sum(if(screening = 0 and response = 1, 1, 0))+sum(if(screening = 1 and response = 0, 1, 0)))/(sum(screening != -3 and response != -2))
					+0.7*(sum(if(screening = -1 and response = 0, 1, 0))+sum(if(screening = 0 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2))
					+2.5*(sum(if(screening = -1 and response = 1, 1, 0))+sum(if(screening = 1 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2)), 3) as RANK_W,
					
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
						Sentence_User.username != "factchecker" and
						Sentence_User.time >= '.$_SESSION['fourth_phase_time_start'].' and
						sentence_id not in '.$_SESSION['training_sentences'].'
						group by Sentence_User.username) A order by PAYMENT desc, ANSWERED desc;';
			
			if($_SESSION['message_counter'] == 0)
			{
				$results_message = execute($sql, array(), PDO::FETCH_ASSOC);
				$ANSWERED_message = 0;
				$QUALITY_message = 0;
				$PAYMENT_message = 0;
				$RANK_message = 0;
				$total_message = 0;
				foreach($results_message as $key=>$v)
				{
					$total_message = $total_message + 1;
					if(strcmp('"'.$v['USERNAME'].'"', $username) == 0)
					{
						$ANSWERED_message = $v['ANSWERED'];
						$QUALITY_message = $v['QUALITY'];
						$PAYMENT_message = $v['PAYMENT'];
						$RANK_message = $total_message;
					}							
				}
			
				$results[0]['ANSWERED_message'] = $ANSWERED_message;
				$results[0]['QUALITY_message'] = $QUALITY_message;
				$results[0]['PAYMENT_message'] = $PAYMENT_message;
				$results[0]['RANK_message'] = $RANK_message;
				$results[0]['total_message'] = $total_message;
				
				$_SESSION['ANSWERED_message'] = $ANSWERED_message;
				$_SESSION['QUALITY_message'] = $QUALITY_message;
				$_SESSION['PAYMENT_message'] = $PAYMENT_message;
				$_SESSION['RANK_message'] = $RANK_message; 
				$_SESSION['total_message'] = $total_message;
				
				$_SESSION['message_counter'] = rand(20, 30);
				
				$leaderboard_sql = 'select USERNAME,ANSWERED,
				if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*pow(0.6, A.SKIPPED/A.ANSWERED),2), -100000) as QUALITY,
				if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*ANSWERED/100*pow(0.6, A.SKIPPED/A.ANSWERED),2), -100000) as PAYMENT
				from (select 
					Sentence_User.username as USERNAME, 
		
					round(-0.2*(sum(if(screening = -1 and response = -1, 1, 0))+sum(if(screening = 0 and response = 0, 1, 0))+sum(if(screening = 1 and response = 1, 1, 0)))/(sum(screening != -3 and response != -2))
					+0.7*(sum(if(screening = 0 and response = 1, 1, 0))+sum(if(screening = 1 and response = 0, 1, 0)))/(sum(screening != -3 and response != -2))
					+0.7*(sum(if(screening = -1 and response = 0, 1, 0))+sum(if(screening = 0 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2))
					+2.5*(sum(if(screening = -1 and response = 1, 1, 0))+sum(if(screening = 1 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2)), 3) as RANK_W,
		
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
					Sentence_User.username != "factchecker" and
					Sentence_User.time >= '.$_SESSION['fourth_phase_time_start'].' and
					sentence_id not in '.$_SESSION['training_sentences'].'
				group by Sentence_User.username) A order by PAYMENT desc, ANSWERED desc;'; 
				$leaderboard_results = execute($leaderboard_sql, array(), PDO::FETCH_ASSOC);
				$_SESSION['leaderboard_results'] = $leaderboard_results;
				
				$activity_sql = gen_insert_query($tables=array('Activity'), $fields=array('username', 'time', 'action'), $values=array($username, '"'.date("Y-m-d H:i:s").'"', "'SYSTEM UPDATED LEADERBOARD'"));
				$activity_sql_results = execute($activity_sql, array(), PDO::FETCH_ASSOC);
			}
			else
			{
				if($sentence_id == 0) $_SESSION['message_counter'] = $_SESSION['message_counter'] - 1;//decrease only if not a modify request
				
				if($_SESSION['just_logged_in'] ==  1)
				{
					$_SESSION['ANSWERED_message'] = null;
					$_SESSION['QUALITY_message'] = null;
					$_SESSION['PAYMENT_message'] = null;
					$_SESSION['RANK_message'] = null;
					$_SESSION['total_message'] = null;
					$_SESSION['just_logged_in'] =  0;
				}
				$results[0]['ANSWERED_message'] = $_SESSION['ANSWERED_message'];
				$results[0]['QUALITY_message'] = $_SESSION['QUALITY_message'];
				$results[0]['PAYMENT_message'] = $_SESSION['PAYMENT_message'];
				$results[0]['RANK_message'] = $_SESSION['RANK_message'];
				$results[0]['total_message'] = $_SESSION['total_message'];						
			}						
			
			echo json_encode($results[0]);
		}
		else
		{
			$activity_sql = gen_insert_query($tables=array('Activity'), $fields=array('username', 'time', 'action'), $values=array($username, '"'.date("Y-m-d H:i:s").'"', '"NO MORE SENTENCE AVAILABLE"'));
			$activity_sql_results = execute($activity_sql, array(), PDO::FETCH_ASSOC);
			echo "-1";
		}
	} elseif ($_SESSION['project']=='"WildFire"') {
		// TODO: change training sentences idx
		// $training_sentences = '(129, 1576, 3110, 3429, 4390, 5553, 5562, 5654, 5974, 6002, 6483, 7600, 9017, 9355, 9862, 10060, 10762, 10863, 11025, 11112, 14933, 611, 15445, 15602, 15763, 16014, 16015, 16258, 16828, 17000, 17159, 17420, 17509, 21636, 24352, 26145, 27100, 27828, 27986, 28777)';
		$training_sentences = '(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40)';

		$sql = 'select Sentence_User.username as USERNAME	
				from
					Sentence_User,
					Sentence
				where
					id = sentence_id and
					response != -2 and
					sentence_id not in '.$training_sentences.'
					group by Sentence_User.username
				having 
					-0.2*(sum(if(screening = -1 and response = -1, 1, 0))+sum(if(screening = 0 and response = 0, 1, 0))+sum(if(screening = 1 and response = 1, 1, 0)))/(sum(screening != -3 and response != -2))
					+0.7*(sum(if(screening = 0 and response = 1, 1, 0))+sum(if(screening = 1 and response = 0, 1, 0)))/(sum(screening != -3 and response != -2))
					+0.7*(sum(if(screening = -1 and response = 0, 1, 0))+sum(if(screening = 0 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2))
					+2.5*(sum(if(screening = -1 and response = 1, 1, 0))+sum(if(screening = 1 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2)) <= 0.0 and count(*) >= 50';

		$top_participants = execute($sql, array(), PDO::FETCH_COLUMN);
		$top_participants_string = '("'.implode('","', $top_participants).'")';

		$sql = 'select sentence_id from (
					select sentence_id, sum(if(response = -1, 1, 0)) as nfs, sum(if(response = 0, 1, 0)) as ufs, sum(if(response = 1, 1, 0)) as cfs
					from Sentence_User, Sentence 
					where Sentence.id = Sentence_User.sentence_id 
						and screening = -3
						and username in '.$top_participants_string.'group by sentence_id
					having (nfs >= 3 and nfs >= 2+ufs and nfs >= 2+cfs)
						or (ufs >= 3 and ufs >= 2+nfs and ufs >= 2+cfs)
						or (cfs >= 3 and cfs >= 2+ufs and cfs >= 2+nfs)
				) A';

		$top_quality_sentences = execute($sql, array(), PDO::FETCH_COLUMN);
		$top_quality_sentences_string = '("'.implode('","', $top_quality_sentences).'")';

		$sql = gen_select_query(array('Sentence.id', 'Speaker.shortform as name', 'Sentence.text'), array('Sentence', 'Speaker', 'Speaker_File'), array('Sentence.speaker_id = Speaker.id', 'Speaker.id = Speaker_File.speaker_id', 'Sentence.file_id = Speaker_File.file_id', 'Sentence.id NOT IN '.$already_answered, 'Sentence.id NOT IN '.$top_quality_sentences_string, 'Sentence.length >= 5', 'Speaker_File.role = "Interviewee"', 'screening = -3'), array(), array('RAND()'), array('1'));
		$results = execute($sql, array(), PDO::FETCH_ASSOC);
		//No more sentence available for the user.
		if(!count($results) && $sentence_id == 0) {
			$activity_sql = gen_insert_query($tables=array('Activity'), $fields=array('username', 'time', 'action'), $values=array($username, '"'.date("Y-m-d H:i:s").'"', '"NO MORE SENTENCE AVAILABLE"'));
			$activity_sql_results = execute($activity_sql, array(), PDO::FETCH_ASSOC);
			echo "-1";
			exit();
		}
		#sentence_id not mentioned. Not a 'Change' or 'Go Back' scenario.
		if($sentence_id == 0) 
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
				$class_screening = randomFloat(0,30);
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
				$sql = gen_select_query(array('Sentence.id', 'Speaker.shortform as name', 'Sentence.text'), array('Sentence', 'Speaker', 'Speaker_File'), array('Sentence.speaker_id = Speaker.id', 'Speaker.id = Speaker_File.speaker_id', 'Sentence.file_id = Speaker_File.file_id', 'Sentence.length >= 5', 'Speaker_File.role = "Interviewee"', 'screening = '.$class_screening, 'Sentence.id not in '.$_SESSION['training_sentences'].' '), array(), array('RAND()'), array('1'));
			}
			else  { #regular question
				$sql = gen_select_query(array('sentence_id'), array('Sentence_User'), array('username = '.$username));	
				$results = execute($sql, array(), PDO::FETCH_COLUMN);
				if(count($results))$already_answered = '('.implode(', ', $results).')';
				else $already_answered = '(-1)';
			
				$sql = 'select Sentence_User.username as USERNAME	
						from
							Sentence_User,
							Sentence
						where
							id = sentence_id and
							username not in ("cmavs2015", "sakiforu", "teaphony") and
							response != -2 and
							sentence_id not in '.$training_sentences.'
						group by Sentence_User.username
						having 
							-0.2*(sum(if(screening = -1 and response = -1, 1, 0))+sum(if(screening = 0 and response = 0, 1, 0))+sum(if(screening = 1 and response = 1, 1, 0)))/(sum(screening != -3 and response != -2))
							+0.7*(sum(if(screening = 0 and response = 1, 1, 0))+sum(if(screening = 1 and response = 0, 1, 0)))/(sum(screening != -3 and response != -2))
							+0.7*(sum(if(screening = -1 and response = 0, 1, 0))+sum(if(screening = 0 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2))
							+2.5*(sum(if(screening = -1 and response = 1, 1, 0))+sum(if(screening = 1 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2)) <= 0.0 and count(*) >= 50';	

				$top_participants = execute($sql, array(), PDO::FETCH_COLUMN);
				$top_participants_string = '("'.implode('","', $top_participants).'")';

				$sql = 'select sentence_id from (
							select sentence_id, 
								   sum(if(response = -1, 1, 0)) as nfs, 
								   sum(if(response = 0, 1, 0)) as ufs, 
								   sum(if(response = 1, 1, 0)) as cfs
							from Sentence_User, Sentence 
							where Sentence.id = Sentence_User.sentence_id 
								and screening = -3
								and username in '.$top_participants_string.'group by sentence_id
							having (nfs >= 3 and nfs >= 2+ufs and nfs >= 2+cfs)
								or (ufs >= 3 and ufs >= 2+nfs and ufs >= 2+cfs)
								or (cfs >= 3 and cfs >= 2+ufs and cfs >= 2+nfs)) A';

				$top_quality_sentences = execute($sql, array(), PDO::FETCH_COLUMN);
				$top_quality_sentences_string = '("'.implode('","', $top_quality_sentences).'")';

				$sql = gen_select_query(array('Sentence.id', 'Speaker.shortform as name', 'Sentence.text'), array('Sentence', 'Speaker', 'Speaker_File'), array('Sentence.speaker_id = Speaker.id', 'Speaker.id = Speaker_File.speaker_id', 'Sentence.file_id = Speaker_File.file_id', 'Sentence.id NOT IN '.$already_answered, 'Sentence.id NOT IN '.$top_quality_sentences_string, 'Sentence.length >= 5', 'Speaker_File.role = "Interviewee"', 'screening = -3'), array(), array('answered', 'RAND()'), array('1'));
			}		
		}
		else #sentence_id mentioned
		{				
			$action = "'USER CLICKED CHANGE'";
			if($sentence_id < 0)
			{
				$action = "'USER CLICKED GO BACK'";
				$sentence_id = $sentence_id * (-1);
			}
			$activity_sql = gen_insert_query($tables=array('Activity'), $fields=array('username', 'time', 'action', 'sentence_id'), $values=array($username, '"'.date("Y-m-d H:i:s").'"', $action, $sentence_id));
			$activity_sql_results = execute($activity_sql, array(), PDO::FETCH_ASSOC);
			
			$sql = gen_select_query(array('Sentence.id', 'Speaker.shortform as name', 'Sentence.text'), array('Sentence', 'Speaker'), array('Sentence.speaker_id = Speaker.id', 'Sentence.id = '.$sentence_id), array(), array(), array());
		}
		
		$results = execute($sql, array(), PDO::FETCH_ASSOC); // [id, name, text]
		
		if(count($results)) {
			$sql = 'select A.USERNAME, A.ANSWERED,
					(case when A.RANK_W <= 0.0 and A.ANSWERED >= 4 then 1
						when A.RANK_W <= 0.3 and A.ANSWERED >= 4  then 2
						when A.RANK_W <= 0.6 and A.ANSWERED >= 4  then 3
						when A.RANK_W > 0.6 and A.ANSWERED >= 4  then 4
						else 0
							end) as REGION
					from (select Sentence_User.username as USERNAME, 
								round(-0.2*(sum(if(screening = -1 and response = -1, 1, 0))+sum(if(screening = 0 and response = 0, 1, 0))+sum(if(screening = 1 and response = 1, 1, 0)))/(sum(screening != -3 and response != -2))
								+0.7*(sum(if(screening = 0 and response = 1, 1, 0))+sum(if(screening = 1 and response = 0, 1, 0)))/(sum(screening != -3 and response != -2))
								+0.7*(sum(if(screening = -1 and response = 0, 1, 0))+sum(if(screening = 0 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2))
								+2.5*(sum(if(screening = -1 and response = 1, 1, 0))+sum(if(screening = 1 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2)), 3) as RANK_W,	
								count(*) as ANSWERED
						  from
						  	  Sentence_User,
							  Sentence,
							  User
						  where id = sentence_id and
								Sentence_User.username = User.username and
								Sentence_User.username = '.$username.' and
								Sentence_User.response != -2 and
								screening != -3) A';

			$results_status = execute($sql, array(), PDO::FETCH_ASSOC);
			if(1){ //$results_status[0]['REGION'] != $_SESSION['REGION']// 1 means for every response, do update.
				$results[0]['REGION'] = $results_status[0]['REGION'];
			} else {
				$results[0]['REGION'] = 0;
			}
			$_SESSION['REGION'] = $results_status[0]['REGION'];
			$_SESSION['sentence_id'] = $results[0]['id'];
			
			$sql = 'select USERNAME,ANSWERED,
						   if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*pow(0.6, A.SKIPPED/A.ANSWERED),2), 0) as QUALITY,
						   if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*ANSWERED/100*pow(0.6, A.SKIPPED/A.ANSWERED),2), 0) as PAYMENT
					from (select 
					Sentence_User.username as USERNAME, 
					
					round(-0.2*(sum(if(screening = -1 and response = -1, 1, 0))+sum(if(screening = 0 and response = 0, 1, 0))+sum(if(screening = 1 and response = 1, 1, 0)))/(sum(screening != -3 and response != -2))
					+0.7*(sum(if(screening = 0 and response = 1, 1, 0))+sum(if(screening = 1 and response = 0, 1, 0)))/(sum(screening != -3 and response != -2))
					+0.7*(sum(if(screening = -1 and response = 0, 1, 0))+sum(if(screening = 0 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2))
					+2.5*(sum(if(screening = -1 and response = 1, 1, 0))+sum(if(screening = 1 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2)), 3) as RANK_W,
					
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
						Sentence_User.username != "factchecker" and
						Sentence_User.time >= '.$_SESSION['fourth_phase_time_start'].' and
						sentence_id not in '.$_SESSION['training_sentences'].'
						group by Sentence_User.username) A order by PAYMENT desc, ANSWERED desc;';
			
			if($_SESSION['message_counter'] == 0)
			{
				$results_message = execute($sql, array(), PDO::FETCH_ASSOC);
				$ANSWERED_message = 0;
				$QUALITY_message = 0;
				$PAYMENT_message = 0;
				$RANK_message = 0;
				$total_message = 0;
				foreach($results_message as $key=>$v)
				{
					$total_message = $total_message + 1;
					if(strcmp('"'.$v['USERNAME'].'"', $username) == 0)
					{
						$ANSWERED_message = $v['ANSWERED'];
						$QUALITY_message = $v['QUALITY'];
						$PAYMENT_message = $v['PAYMENT'];
						$RANK_message = $total_message;
					}							
				}
			
				$results[0]['ANSWERED_message'] = $ANSWERED_message;
				$results[0]['QUALITY_message'] = $QUALITY_message;
				$results[0]['PAYMENT_message'] = $PAYMENT_message;
				$results[0]['RANK_message'] = $RANK_message;
				$results[0]['total_message'] = $total_message;
				
				$_SESSION['ANSWERED_message'] = $ANSWERED_message;
				$_SESSION['QUALITY_message'] = $QUALITY_message;
				$_SESSION['PAYMENT_message'] = $PAYMENT_message;
				$_SESSION['RANK_message'] = $RANK_message; 
				$_SESSION['total_message'] = $total_message;
				
				$_SESSION['message_counter'] = rand(20, 30);
				
				$leaderboard_sql = 'select USERNAME,ANSWERED,
				if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*pow(0.6, A.SKIPPED/A.ANSWERED),2), -100000) as QUALITY,
				if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*ANSWERED/100*pow(0.6, A.SKIPPED/A.ANSWERED),2), -100000) as PAYMENT
				from (select 
					Sentence_User.username as USERNAME, 
		
					round(-0.2*(sum(if(screening = -1 and response = -1, 1, 0))+sum(if(screening = 0 and response = 0, 1, 0))+sum(if(screening = 1 and response = 1, 1, 0)))/(sum(screening != -3 and response != -2))
					+0.7*(sum(if(screening = 0 and response = 1, 1, 0))+sum(if(screening = 1 and response = 0, 1, 0)))/(sum(screening != -3 and response != -2))
					+0.7*(sum(if(screening = -1 and response = 0, 1, 0))+sum(if(screening = 0 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2))
					+2.5*(sum(if(screening = -1 and response = 1, 1, 0))+sum(if(screening = 1 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2)), 3) as RANK_W,
		
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
					Sentence_User.username != "factchecker" and
					Sentence_User.time >= '.$_SESSION['fourth_phase_time_start'].' and
					sentence_id not in '.$_SESSION['training_sentences'].'
				group by Sentence_User.username) A order by PAYMENT desc, ANSWERED desc;'; 
				$leaderboard_results = execute($leaderboard_sql, array(), PDO::FETCH_ASSOC);
				$_SESSION['leaderboard_results'] = $leaderboard_results;
				
				$activity_sql = gen_insert_query($tables=array('Activity'), $fields=array('username', 'time', 'action'), $values=array($username, '"'.date("Y-m-d H:i:s").'"', "'SYSTEM UPDATED LEADERBOARD'"));
				$activity_sql_results = execute($activity_sql, array(), PDO::FETCH_ASSOC);
			}
			else
			{
				if($sentence_id == 0) $_SESSION['message_counter'] = $_SESSION['message_counter'] - 1;//decrease only if not a modify request
				
				if($_SESSION['just_logged_in'] ==  1)
				{
					$_SESSION['ANSWERED_message'] = null;
					$_SESSION['QUALITY_message'] = null;
					$_SESSION['PAYMENT_message'] = null;
					$_SESSION['RANK_message'] = null;
					$_SESSION['total_message'] = null;
					$_SESSION['just_logged_in'] =  0;
				}
				$results[0]['ANSWERED_message'] = $_SESSION['ANSWERED_message'];
				$results[0]['QUALITY_message'] = $_SESSION['QUALITY_message'];
				$results[0]['PAYMENT_message'] = $_SESSION['PAYMENT_message'];
				$results[0]['RANK_message'] = $_SESSION['RANK_message'];
				$results[0]['total_message'] = $_SESSION['total_message'];						
			}						
			
			echo json_encode($results[0]);
		}
		else
		{
			$activity_sql = gen_insert_query($tables=array('Activity'), $fields=array('username', 'time', 'action'), $values=array($username, '"'.date("Y-m-d H:i:s").'"', '"NO MORE SENTENCE AVAILABLE"'));
			$activity_sql_results = execute($activity_sql, array(), PDO::FETCH_ASSOC);
			echo "-1";
		}
	}
?>
