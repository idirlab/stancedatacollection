<?php
include_once "db.php";
include getcwd() . "/GLOBAL.php";
session_start(["cookie_lifetime" => 86400]);
require "./vendor/phpmailer/phpmailer/PHPMailerAutoload.php";

$email = $_REQUEST["email"];
$unquoted_email = str_replace('"', "", $email);
$username = $_REQUEST["username"];
$password = $_REQUEST["password"];
if ($GROUNDTRUTH_ENV) {
    $redirect_url_prefix =
        "http://idir.uta.edu/stance_groundtruth_annotation";
} else {
    $redirect_url_prefix = "http://idir.uta.edu/stance_annotation";
}

$random_key = md5(uniqid(rand()));

$mail = new PHPMailer();
$mail->isSMTP(); // Set mailer to use SMTP
$mail->Host = "smtp.gmail.com"; // Specify main and backup SMTP servers
$mail->SMTPAuth = true; // Enable SMTP authentication
$mail->Username = "idirlabuta@gmail.com"; // SMTP username
$mail->Password = "lxgpwyhdqivyrbst"; // SMTP password
$mail->SMTPSecure = "ssl"; // Enable TLS encryption, `ssl` also accepted
$mail->Port = 465; // TCP port to connect to
$mail->SMTPDebug = 0;

$mail->From = "idirlabuta@gmail.com";
$mail->FromName = "Truthfulness Stance Annotation: new user";
$mail->addAddress($unquoted_email); // Add a recipient
$mail->addReplyTo("idirlabuta@gmail.com", "Information");
$mail->isHTML(true); // Set email format to HTML

$mail->Subject = "IDIR Lab: Account Verification Message";
$mail->Body =
    'Hello,<br><br>
     This is an automatic response sent to those who wish to register with Truthfulness Stance Annotation.<br><br>
     Thank you for signing up. Please follow the verification link below to get your account verified and complete the registration process.<br><br>
     <b>Verification Link:</b> <a href=' .
    $redirect_url_prefix .
    "/verify_user.php?random_key=" .
    $random_key .
    "&username=" .
    str_replace('"', "", $username) .
    ">" .
    $redirect_url_prefix .
    "/verify_user.php?random_key=" .
    $random_key .
    "&username=" .
    str_replace('"', "", $username) .
    "</a><br><br>" .
    '<b>Received this message by mistake?</b><br>This message is sent when an email address is registered to a truthfulness stance annotation account. You may have received this email in error because another customer entered this email address by mistake. Please delete this email. Your email address will not be registered unless you follow the verification link listed above. <br><br>
     Regards.<br>IDIR Lab Wildfire Project<br>' .
    $redirect_url_prefix .
    '<br>
     contact: idirlabuta@gmail.com';

if (!$mail->send()) {
    var_dump($mail->ErrorInfo);
    echo "We could not send email to your address. Please check the email address and try signing up again.";
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    $sql = gen_insert_query(
        ["User"],
        [],
        [
            $username,
            $password,
            $email,
            '"' . $random_key . '"',
            "0",
            "0",
            "0",
            "0",
            "0",
            "1000",
        ]
    );
    $results = execute($sql, [], PDO::FETCH_ASSOC);
    echo "We have sent an email to your address. Please check your Inbox and verify your account. Sometimes, emails are stored in Spam folder. If you cannot find the email in your Inbox then please check the Spam folder. If you do not find the email at all, then please check the email address and try signing up again.";
}
?>
