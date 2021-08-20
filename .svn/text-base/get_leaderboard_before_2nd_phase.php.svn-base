<?php
	include_once("db.php");
	session_start();
	$username = $_SESSION['username'];
	
	$sql = 'select USERNAME, ANSWERED, if(ANSWERED >= 50, A.ANSWERED*sign(0.3 - A.RANK_W)*pow((0.3 - A.RANK_W),2), -100000) AS RANK_N   from(select 
    Sentence_User.username as USERNAME, count(*) as ANSWERED,

	-0.2*(sum(if(screening = -1 and response = -1, 1, 0))+sum(if(screening = 0 and response = 0, 1, 0))+sum(if(screening = 1 and response = 1, 1, 0)))/(sum(screening != -3 and response != -2))
	+1.0*(sum(if(screening = 0 and response = 1, 1, 0))+sum(if(screening = 1 and response = 0, 1, 0)))/(sum(screening != -3 and response != -2))
	+1.0*(sum(if(screening = -1 and response = 0, 1, 0))+sum(if(screening = 0 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2))
	+3.0*(sum(if(screening = -1 and response = 1, 1, 0))+sum(if(screening = 1 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2)) as RANK_W
from
    Sentence_User,
    Sentence,
	User
where
    id = sentence_id and
	Sentence_User.username = User.username and
	Sentence_User.username != "factchecker" and
	Sentence_User.response != -2
group by Sentence_User.username

order by RANK_W, ANSWERED desc) A order by RANK_N desc, ANSWERED desc'; 
	$results = execute($sql, array(), PDO::FETCH_ASSOC);

	$user = -1;
	$winner_10 = array('mcwizard', 'dxl3182', 'nrd1216', 'lawanda18', 'danb11', 'ajdashdash' , 'Alucard', 'anna.prieto', 'Charis92', 'dxl3182', 'JE_4489', 'jsn6522', 'Leaf90', 'nadi', 'sakiforu', 'aminataj', 'AramintaK96', 'benderwd40', 'carcinoPoet', 'danb11', 'dlehddbs92', 'dustinh', 'j.kathryn', 'kidkatie', 'knitty1997', 'Monica_16', 'pec6938', 'sakiforu', 'Sfj8667', 'stefaguas');
	$winner_100 = array('CodyBonBon', 'dariansmith55');
	$winner_200 = array('dxl3182');
	
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
		
		if(floatval($results[$i]['RANK_N']) == -100000)$results[$i]['RANK_N'] = 'Rank Not Displayed';
		else $results[$i]['RANK_N'] = $i+1;
	}
	
	
	$results[0]['user'] = $user;
	//$results[0]['winner_10'] = $winner_10;
	//$results[0]['winner_100'] = $winner_100;
	$results = json_encode($results);
	echo $results;
?>
