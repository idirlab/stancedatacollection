<?php
	session_start();
	require './vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
	
	$email=$_REQUEST["email"];
	$unquoted_email = str_replace('"', '', $email);
	$username = $_REQUEST["username"];
	$password = $_REQUEST["password"];
	$encrypted_password = '"'.md5($password).'"';
	
	$random_key = md5(uniqid(rand()));
	include_once("db.php");
	
	$mail = new PHPMailer;

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'idirlabuta@gmail.com';                 // SMTP username
	$mail->Password = 'Idirerb414500uta';                           // SMTP password
	$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 465;                                 // TCP port to connect to
	$mail->SMTPDebug = 3;

	$mail->From = 'idirlabuta@gmail.com';
	$mail->FromName = 'Wildfire Annotation: forget password: new user';
	$mail->addAddress($unquoted_email);     // Add a recipient
	$mail->addReplyTo('idirlabuta@gmail.com', 'Information');
	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = 'IDIR Lab: Account Verification Message';
		$mail->Body = 'Hello,<br><br>This is an automatic response sent to those who wish to register with Wildfire annotation tool.<br>
					   <br>Thank you for signing up. Please follow the verification link below to get your account verified and complete the registration process.<br>
					   <br>'.'<b>Verification Link:</b> 
					   <a href=http://idir.uta.edu/wildfire_annotation/verify_user.php?random_key='.$random_key.'&username='.str_replace('"','',$username).'>
					   http://idir.uta.edu/wildfire_annotation/verify_user.php?random_key='.$random_key.'&username='.str_replace('"','',$username).'</a>'.'<br>
					   <br><b>Received this message by mistake?</b><br>This message is sent when an email address is registered to a Wildfire annotation account. You may have received this email in error because another customer entered this email address by mistake. Please delete this email. Your email address will not be registered unless you follow the verification link listed above. <br>
					   <br>Regards.<br>IDIR Lab Wildfire Project<br>http://idir.uta.edu/wildfire_annotation/<br>contact: idirlabuta@gmail.com';

	if(!$mail->send())
	{
		var_dump($mail->ErrorInfo);
		echo "We could not send email to your address. Please check the email address and try signing up again.";
		echo 'Mailer Error: ' . $mail->ErrorInfo;
	}
	else
	{
		$sql = gen_insert_query(array('User'), array(), array($username, $encrypted_password, $email, '"'.$random_key.'"', '0', '0', '0','0', '0', '1000' ));
		$results = execute($sql, array(), PDO::FETCH_ASSOC);
		echo $results;
		echo "We have sent an email to your address. Please check your Inbox and verify your account. Sometimes, emails are stored in Spam folder. If you cannot find the email in your Inbox then please check the Spam folder. If you do not find the email at all, then please check the email address and try signing up again.";
	}
?>
