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
	$mail->Username = 'idirlabuta@gmail.com';                 // SMTP username
	$mail->Password = 'Idirerb414500uta';                           // SMTP password
	$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 465;                                 // TCP port to connect to
	$mail->SMTPDebug = 0;
	
	$mail->From = 'idirlabuta@gmail.com';
	$mail->FromName = 'Wildfire Annotation: forget password';
	$mail->addAddress($unquoted_email);     // Add a recipient
	$mail->addReplyTo('idirlabuta@gmail.com', 'Information');

	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = 'IDIR Lab: Password Information';
	$mail->Body    = 'Hello,<br><br>Your username is '.$username.',<br>Please click on the following link to reset your password.<br><br>'.'<a href=http://idir.uta.edu/wildfire_annotation/reset_password.php?random_key='.$random_key.'&username='.str_replace('"','',$username).'>http://idir.uta.edu/wildfire_annotation/reset_password.php?random_key='.$random_key.'&username='.str_replace('"','',$username).'</a>'.'<br><br>Regards.<br>IDIR Wildfire Team<br>http://idir.uta.edu/wildfire_annotation/';

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
