<?php
	require './vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
	
	$email=$_REQUEST["email"];
	$unquoted_email = str_replace('"', '', $email);
	
	include_once("db.php");
	
	$sql = gen_select_query(array('username'), array('User'), array('email = '.$email ));
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
	if(count($results))$username = $results[0]['username'];
	else
	{
		echo "Email Address not found!.";
		return;
	}
	
	$random_key = md5(uniqid(rand()));	
	
	$mail = new PHPMailer;

	//$mail->SMTPDebug = 3;                               // Enable verbose debug output

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	// $mail->Username = 'classifyfact@gmail.com';                 // Previous SMTP username
	// $mail->Password = 'classifyfact123';                           // Previous SMTP password
	$mail->Username = 'zhuzhengyuan824@gmail.com';                 // SMTP username
	$mail->Password = 'kobe81kobe81';                           // SMTP password
	$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 465;                                 // TCP port to connect to
	$mail->SMTPDebug = 0;

	// $mail->From = 'classifyfact@gmail.com';
	// $mail->FromName = 'ClassifyFact Account';
	$mail->From = 'zhuzhengyuan824@gmail.com';
	$mail->FromName = 'Wildfire Annotation: forget password';
	$mail->addAddress($unquoted_email);     // Add a recipient
	$mail->addReplyTo('zhuzhengyuan824@gmail.com', 'Information');

	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = 'IDIR Lab: Password Information';
	$mail->Body    = 'Hello,<br><br>Your username is '.$username.',<br>Please click on the following link to reset your password.<br><br>'.'<a href=http://idir-server2.uta.edu/classifyfact_survey/reset_password.php?random_key='.$random_key.'&username='.str_replace('"','',$username).'>http://idir-server2.uta.edu/classifyfact_survey/reset_password.php?random_key='.$random_key.'&username='.str_replace('"','',$username).'</a>'.'<br><br>Regards.<br>IDIR ClassifyFact Team<br>http://idir-server2.uta.edu/classifyfact_survey/';

	if(!$mail->send())
	{
		echo "We could not send email to your address. Please check the email address and try again.";
		echo 'Mailer Error: ' . $mail->ErrorInfo;
	}
	else
	{
		echo "We have sent an email to your address. Please follow the instructions there to reset your password.";
		
		$sql = gen_update_query(array('User'), array('verification'), array('"'.$random_key.'"'), array('email = '.$email));
		$results = execute($sql, array(), PDO::FETCH_ASSOC);
	}
?>
