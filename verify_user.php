<?php
	session_start();
	$random_key = $_GET["random_key"];
	$username = $_GET["username"];
	$project = $_GET["project"];
	$_SESSION['project'] = '"'. $project .'"';
	
	include_once("db.php");	
	
	$sql = gen_update_query(array('User'), array('verification'), 
							array('"verified"'), array('verification = "'.$random_key.'"',
							'username = "'.$username.'"'));

	$results = execute($sql, array(), PDO::FETCH_ASSOC);

	$sql = gen_select_query(array('verification'), array('User'), 
	                        array('username = "'.$username.'"'));
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
	
	if(strcmp($results[0]['verification'], 'verified') == 0)
	{
		// TODO: modify the url
		echo "Your account is verified successfully! Please go to <a href='http://idir-server2.uta.edu/classifyfact_survey/'>http://idir-server2.uta.edu/classifyfact_survey/</a> and sign in now. Thanks!";
		return;
	}
	else
	{
		echo "There is some error with verification. Please check the verification link or try Registration again.";
	}
?>
