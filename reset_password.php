<?php
	$random_key = $_GET["random_key"];
	$username = $_GET["username"];
	include_once("db.php");	
	
	/*$sql = gen_update_query(array('User'), array('verification'), array('"verified"'), array('verification = "'.$random_key.'"', 'username = "'.$username.'"'));
	$results = execute($sql, array(), PDO::FETCH_ASSOC);*/
	
	$sql = gen_select_query(array('verification'), array('User'), array('username = "'.$username.'"'));
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
	
	if(strcmp($results[0]['verification'], $random_key) == 0)
	{
		?>
		
		<!DOCTYPE html>
		<html lang="en">
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<meta name="description" content="">
				<meta name="author" content="">

				<link rel="icon" type="image/png" href="image/wildfire_wout_text.png"/>
				<title>Truthfulness Stance Annotation</title>
				<!-- Bootstrap core CSS -->
				<link href="bootstrap-3.3.2-dist/css/bootstrap.min.css" rel="stylesheet">
				<link href="iCheck/skins/square/blue.css" rel="stylesheet">
				<link href="css/index.css" rel="stylesheet">
			</head>

			<body>
				<nav class="navbar navbar-inverse navbar-fixed-top">
					<div class="container">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							<a class="navbar-brand" href="index.php">Truthfulness Stance Annotation</a>
						</div>						
					</div>
				</nav>				

				<div class="jumbotron">
					<div class="container">
						<br>
						<h2>Please Reset Your Password</h2>
						<br>
						<div class='row'>
							<div class='col-md-4'>
								<input type="password" placeholder="Enter Password" class="form-control" id="input_reset_password">
							</div>
							<div class='col-md-4'>
								<input type="password" placeholder="Confirm Password" class="form-control" id="input_reset_password_confirm">
							</div>
							<div class='col-md-2'>
								<button id="button_reset_password" type="button" class="btn btn-success pull_top" onclick="reset_password('<?php echo $username;?>')">Reset Password</button>
							</div>
						</div>						
					</div>
				</div>

				<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
				<script src="bootstrap-3.3.2-dist/js/bootstrap.min.js"></script>
				<script src="js/index.js"></script>
				<script src="iCheck/icheck.js"></script>
			</body>
		</html>

		<?php
	}
	else
	{
		echo "There is some error. Please check the password reset link and try again.";
	}
?>
