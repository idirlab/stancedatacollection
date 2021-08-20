<?php
	include_once("db.php");
	session_start();
	$username = $_SESSION['username'];
	
	////added momentarily for payment display purpose. March 30, 2016.
	/*$leaderboard_sql = 'select USERNAME,ANSWERED, 
			round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*pow(0.6, A.SKIPPED/A.ANSWERED),2) as QUALITY,
	round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*ANSWERED/100*pow(0.6, A.SKIPPED/A.ANSWERED),2) as PAYMENT
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
				Sentence_User.time >= '.$_SESSION['third_phase_time_start'].'	
			group by Sentence_User.username) A order by PAYMENT desc, ANSWERED desc;'; 
	$leaderboard_results = execute($leaderboard_sql, array(), PDO::FETCH_ASSOC);
	$_SESSION['leaderboard_results'] = $leaderboard_results;*/
	//added momentarily for payment display purpose. March 30, 2016.
	$results = $_SESSION['leaderboard_results'];
	
	$user = -1;
	$winner_10 = array();//array('mcwizard', 'dxl3182', 'nrd1216', 'lawanda18', 'danb11', 'ajdashdash' , 'Alucard', 'anna.prieto', 'Charis92', 'dxl3182', 'JE_4489', 'jsn6522', 'Leaf90', 'nadi', 'sakiforu', 'aminataj', 'AramintaK96', 'benderwd40', 'carcinoPoet', 'danb11', 'dlehddbs92', 'dustinh', 'j.kathryn', 'kidkatie', 'knitty1997', 'Monica_16', 'pec6938', 'sakiforu', 'Sfj8667', 'stefaguas');
	$winner_100 = array();//array('CodyBonBon', 'dariansmith55');
	$winner_200 = array();//array('dxl3182');

	$winner_10 = array_count_values($winner_10);
	$winner_100 = array_count_values($winner_100);
	$winner_200 = array_count_values($winner_200);

	for($i = 0; $i < count($results); $i++)
	{
		$results[$i]['prize'] = "";
		if(array_key_exists($results[$i]['USERNAME'], $winner_200))
		{
			//$winner_200[$i] = $winner_200[$results[$i]['USERNAME']];
			//unset($winner_200[$results[$i]['USERNAME']]);
			for ($j = 0; $j < $winner_200[$results[$i]['USERNAME']]; $j++)
			{
				//if($j < $winner_200[$results[$i]['USERNAME']]-1 )$results[$i]['prize'] = $results[$i]['prize'].'$200, ';
				//else $results[$i]['prize'] = $results[$i]['prize'].'$200';
			
				if(strcmp($results[$i]['prize'], "") != 0)$results[$i]['prize'] = $results[$i]['prize'].', ';
				$results[$i]['prize'] = $results[$i]['prize'].'$200';
			}
		}
	
		if(array_key_exists($results[$i]['USERNAME'], $winner_100))
		{
			//$winner_100[$i] = $winner_100[$results[$i]['USERNAME']];
			//unset($winner_100[$results[$i]['USERNAME']]);
			for ($j = 0; $j < $winner_100[$results[$i]['USERNAME']]; $j++)
			{
#				if($j < $winner_100[$results[$i]['USERNAME']]-1 )$results[$i]['prize'] = $results[$i]['prize'].'$100, ';
#				else $results[$i]['prize'] = $results[$i]['prize'].'$100';

				if(strcmp($results[$i]['prize'], "") != 0)$results[$i]['prize'] = $results[$i]['prize'].', ';
				$results[$i]['prize'] = $results[$i]['prize'].'$100';
			}
		}
	
		if(array_key_exists($results[$i]['USERNAME'], $winner_10))
		{
			//$winner_10[$i] = $winner_10[$results[$i]['USERNAME']];
			//unset($winner_10[$results[$i]['USERNAME']]);
			for ($j = 0; $j < $winner_10[$results[$i]['USERNAME']]; $j++)
			{
#				if($j < $winner_10[$results[$i]['USERNAME']]-1 )$results[$i]['prize'] = $results[$i]['prize'].'$10, ';
#				else $results[$i]['prize'] = $results[$i]['prize'].'$10';
			
				if(strcmp($results[$i]['prize'], "") != 0)$results[$i]['prize'] = $results[$i]['prize'].', ';
				$results[$i]['prize'] = $results[$i]['prize'].'$10';
			}
		}		
	
		if(strcmp($results[$i]['USERNAME'], substr($username,1,strlen($username)-2) ) != 0)
		{
			$results[$i]['USERNAME'] = strtoupper($results[$i]['USERNAME'][0].$results[$i]['USERNAME'][1].$results[$i]['USERNAME'][2])."*****";						
		}
		else
		{
			$results[$i]['USERNAME'] = $results[$i]['USERNAME'];
			$user = $i;
		}
	
		if(floatval($results[$i]['QUALITY']) == -100000)
		{
			$results[$i]['QUALITY'] = 'Not Displayed';
		}
	
		if(floatval($results[$i]['PAYMENT']) == -100000)
		{
			$results[$i]['PAYMENT'] = 'Not Displayed';
		}
		//else $results[$i]['RANK_N'] = $i+1;
	}


	$results[0]['user'] = $user;
	//$results[0]['winner_10'] = $winner_10;
	//$results[0]['winner_100'] = $winner_100;
	$results = json_encode($results);
	echo $results;

	$activity_sql = gen_insert_query($tables=array('Activity'), $fields=array('username', 'time', 'action'), $values=array($username, '"'.date("Y-m-d H:i:s").'"', "'USER CHECKED LEADERBOARD'"));
	$results = execute($activity_sql, array(), PDO::FETCH_ASSOC);
?>
