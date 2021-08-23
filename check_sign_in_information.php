<?php
	session_start();
	$username = $_REQUEST["username"];
	$password = $_REQUEST["password"];
	$project = $_REQUEST["project"];
	$_SESSION['project'] = $project;

	$encrypted_password = '"'.md5($password).'"';
	include_once("db.php");
	$db_tmp = getConnect();
	
	$sql = gen_select_query(array('username', 'count(*) as count'), array('User'), array('username = '.$username, 'password = '.$encrypted_password));
	
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
	
	
	if(strcmp($results[0]['count'], "0") == 0) echo "-1";#wrong information
	else
	{
		$sql = gen_select_query(array('verification'), array('User'), array('username = '.$username, 'password = '.$encrypted_password));
		$results = execute($sql, array(), PDO::FETCH_ASSOC);
		if(strcmp($results[0]['verification'], "verified") == 0)
		{	
			$_SESSION['username'] = $username;
			$_SESSION['answered'] = 0;
			$_SESSION['screening_questioned'] = 0;
			
			$_SESSION['message_counter'] =  rand(20, 30);
			$_SESSION['just_logged_in'] =  1;
			
			$_SESSION['REGION'] = 0;
			$_SESSION['second_phase_time_start'] = '"2015-06-30 00:00:00"';
			$_SESSION['third_phase_time_start'] = '"2015-08-27 00:00:00"';
			$_SESSION['fourth_phase_time_start'] = '"2016-05-08 00:00:00"'; /*May 3, 2016*/
			if($_SESSION['project']=='"ClaimBuster"') {
				$_SESSION['training_sentences'] = '(129, 1576, 3110, 3429, 4390, 5553, 5562, 5654, 5974, 6002, 6483, 7600, 9017, 9355, 9862, 10060, 10762, 10863, 11025, 11112, 14933, 611, 15445, 15602, 15763, 16014, 16015, 16258, 16828, 17000, 17159, 17420, 17509, 21636, 24352, 26145, 27100, 27828, 27986, 28777)'; /*May 26, 2016*/
			} elseif(($_SESSION['project']=='"WildFire"')) {
				$_SESSION['training_sentences'] = '(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40)'; /*May 26, 2016*/
			}
			
			
			$activity_sql = gen_insert_query($tables=array('Activity'), $fields=array('username', 'time', 'action'), $values=array($username, '"'.date("Y-m-d H:i:s").'"', "'USER LOGEED-IN'"));
			$results = execute($activity_sql, array(), PDO::FETCH_ASSOC);
		}
		else
		{
			echo "-2";#unverified account
		}
	}
?>
