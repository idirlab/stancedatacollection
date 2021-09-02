<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">

	<link rel="icon" type="image/png" href="image/idirlogo2-small-icon.png" />
	<title>IDIR Annotation Tool</title>

	<!-- Bootstrap core CSS -->
	<link href="bootstrap-3.3.2-dist/css/bootstrap.css" rel="stylesheet">
	<link href="iCheck/skins/square/blue.css" rel="stylesheet">
	<link href="css/index.css" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
</head>

<body>

	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button id="button_navbar" type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="index.php" title="ClaimBuster">
        			<img  class='img-responsive claimbuster-logo' src="image/claimbuster_wout_text.png">
    			</a>
				<a class="navbar-brand claimbuster-title" href="index.php">Wildfre annotation tool</a>
			</div>
			
			<div id="navbar_signin_signup" class="navbar-collapse collapse">
				<form class="navbar-form navbar-left">
					<div class="row">
						<div class="col-sm-7">
							<div class="form-group">
								<input type="text" placeholder="Username" class="form-control" id="input_username">
								<input type="password" placeholder="Password" class="form-control" id="input_password" for='inputsm'>
							</div>
						</div>
						<div class="form-group col-lg-2">
							<button id="button_sign_in" type="button" class="btn btn-success pull_top">Sign In</button>
							<button id="button_forgot_password" type="button" class="btn btn-link">Forgot username/password?</button>
						</div>
						<div class="col-sm-2">
							<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_new_user">New User</button>
						</div>
					</div>
				</form>
			</div>
			
			<!--/.navbar-collapse -->
			<div id="navbar_logout" class="navbar-collapse collapse">
				<form class="navbar-form navbar-right">
					<div class="row">
						<ul class="nav nav-pills" role="tablist">
							<li role="presentation">
								<span id="span_username"></span> <span id="span_badge" class="badge">0</span><span> sentences</span>
							</li>
							<li role="presentation">
								<button id="button_leaderboard" type="button" class="btn btn-success">Leaderboard</button>
							</li>
							<li>
								<button id="button_load_survey_instructions" type="button" class="btn btn-primary">Instructions</button>
							</li>
							<li role="presentation">
								<button id="button_log_out" type="button" class="btn btn-primary">Log Out</button>
							</li>
						</ul>
					</div>
				</form>
			</div>
			<!--/.navbar-collapse -->
		</div>
	</nav>

	<div class="modal fade" id="modal_new_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">New User Sign Up</h4>
				</div>
				<div class="modal-body">
					<form>
						<!--<div class="form-group">
								<label for="new_user_full_name">Full Name</label>
								<input type="text" class="form-control" id="input_new_user_full_name" placeholder="Enter Full Name">
							</div>-->
						<div class="form-group">
							<label for="input_new_user_email">Email Address</label>
							<input type="email" class="form-control" id="input_new_user_email" placeholder="Enter Email Address">
						</div>
						<div class="form-group">
							<label for="input_new_user_username">Username</label>
							<input type="text" class="form-control" id="input_new_user_username" placeholder="Enter Username">
						</div>
						<div class="form-group">
							<label for="input_new_user_password">Password</label>
							<input type="password" class="form-control" id="input_new_user_password" placeholder="Enter Password">
						</div>
						<div class="form-group">
							<label for="input_new_user_confirm_password">Confirm Password</label>
							<input type="password" class="form-control" id="input_new_user_confirm_password" placeholder="Enter Password Again">
						</div>
						<div class="form-group">
							<div class='row'>
								<div class='col-md-3'>
									<label for="button_profession">Profession</label>
								</div>
								<div class='col-md-5'>
									<div class="btn-group">
										<button class="btn" id="button_profession" disabled>Select Your Profession</button>
										<button class="btn dropdown-toggle" data-toggle="dropdown">
											<span class="caret"></span>
										</button>								
										<ul class="dropdown-menu profession-options" role="menu" aria-labelledby="dropdownMenu">
											<li><a tabindex="-1">Student</a></li>
											<li><a tabindex="-1">Professor</a></li>
											<li><a tabindex="-1">Journalist/Reporter</a></li>
											<li><a tabindex="-1">Other</a></li>
										</ul>
									</div>
								</div>
								<div class='col-md-3'>
									<input type="text" class="form-control" id="input_other_profession" placeholder="Please Specify">
								</div>
							</div>								
						</div>
						
						<div class="form-group" id = 'form_university'>
							<label for="button_university">University</label><br>
							<div class="btn-group">
								<button class="btn" id="button_university" disabled>Select Your University</button>
								<button class="btn dropdown-toggle" data-toggle="dropdown">
									<span class="caret"></span>
								</button>								
								<ul id='ul_university_options' class="dropdown-menu university-options" role="menu" aria-labelledby="dropdownMenu">																				
									<li><a tabindex="-1">Duke University</a></li>
									<li><a tabindex="-1">University of Texas at Arlington</a></li>
									<li><a tabindex="-1">Other</a></li>
								</ul>
							</div>						
						</div>
						
						<div class="form-group" id = 'form_major'>
							<label for="button_major">Major</label><br>
							<div class="btn-group">
								<button class="btn" id="button_major" disabled>Select Your Major</button>
								<button class="btn dropdown-toggle" data-toggle="dropdown">
									<span class="caret"></span>
								</button>								
								<ul id='ul_major_options' class="dropdown-menu major-options" role="menu" aria-labelledby="dropdownMenu">
																				<li><a tabindex="-1">Academic Partnership Ed Curriculum & Instruction - MEd Mathematics</a></li>
									<li><a tabindex="-1">Academic Partnership Ed Curriculum & Instruction - MEd Science</a></li>
									<li><a tabindex="-1">Academic Partnership Ed Curriculum & Instruction - MEd Triple Literacy</a></li>
									<li><a tabindex="-1">Academic Partnership Ed Leadership & Policy Studies - MEd</a></li>
									<li><a tabindex="-1">Academic Partnership Nurse Educator - MSN</a></li>
									<li><a tabindex="-1">Academic Partnership, Nurse Family Practitioner - MSN</a></li>
									<li><a tabindex="-1">Academic Partnership Nursing Administration - MSN</a></li>
									<li><a tabindex="-1">Academic Partnership Online Master of Public Administration - MPA</a></li>
									<li><a tabindex="-1">Accounting - BBA</a></li>
									<li><a tabindex="-1">Accounting - BS</a></li>
									<li><a tabindex="-1">Accounting - MS</a></li>
									<li><a tabindex="-1">Accounting - PhD</a></li>
									<li><a tabindex="-1">Aerospace Engineering - BSASE</a></li>
									<li><a tabindex="-1">Aerospace Engineering - BS to PhD</a></li>
									<li><a tabindex="-1">Aerospace Engineering Fast Track - BSASE</a></li>
									<li><a tabindex="-1">Aerospace Engineering - MEngr</a></li>
									<li><a tabindex="-1">Aerospace Engineering - MS</a></li>
									<li><a tabindex="-1">Aerospace Engineering - PhD</a></li>
									<li><a tabindex="-1">Anthropology - BA</a></li>
									<li><a tabindex="-1">Anthropology - MA</a></li>
									<li><a tabindex="-1">Architecture - BS</a></li>
									<li><a tabindex="-1">Architecture - M.Arch.</a></li>
									<li><a tabindex="-1">Art - BA</a></li>
									<li><a tabindex="-1">Art - BFA</a></li>
									<li><a tabindex="-1">Art History - BA</a></li>
									<li><a tabindex="-1">Art - MFA</a></li>
									<li><a tabindex="-1">Art (with teacher certification) - BFA</a></li>
									<li><a tabindex="-1">Athletic Training - MS</a></li>
									<li><a tabindex="-1">Biochemistry - BS</a></li>
									<li><a tabindex="-1">Biological Chemistry - BS</a></li>
									<li><a tabindex="-1">Biology - BA</a></li>
									<li><a tabindex="-1">Biology - BS</a></li>
									<li><a tabindex="-1">Biology - MS</a></li>
									<li><a tabindex="-1">Biology Teaching - BA</a></li>
									<li><a tabindex="-1">Biology Teaching - BS</a></li>
									<li><a tabindex="-1">Biomedical Engineering - BS</a></li>
									<li><a tabindex="-1">Biomedical Engineering - BS to PhD</a></li>
									<li><a tabindex="-1">Biomedical Engineering - MS</a></li>
									<li><a tabindex="-1">Biomedical Engineering - PhD</a></li>
									<li><a tabindex="-1">Business Administration - MBA (Flexible Format)</a></li>
									<li><a tabindex="-1">Business Administration - MBA (Professional Cohort)</a></li>
									<li><a tabindex="-1">Business Administration - PhD</a></li>
									<li><a tabindex="-1">Chemistry - BA</a></li>
									<li><a tabindex="-1">Chemistry - BS</a></li>
									<li><a tabindex="-1">Chemistry - BS to PhD</a></li>
									<li><a tabindex="-1">Chemistry - MS</a></li>
									<li><a tabindex="-1">Chemistry - PhD</a></li>
									<li><a tabindex="-1">Chemistry Teaching - BA</a></li>
									<li><a tabindex="-1">Chemistry Teaching - BS</a></li>
									<li><a tabindex="-1">City & Regional Planning - MCIRP</a></li>
									<li><a tabindex="-1">Civil Engineering - BSCE</a></li>
									<li><a tabindex="-1">Civil Engineering - MEngr</a></li>
									<li><a tabindex="-1">Civil Engineering - MEngr Fast Track</a></li>
									<li><a tabindex="-1">Civil Engineering - MS</a></li>
									<li><a tabindex="-1">Civil Engineering - PhD</a></li>
									<li><a tabindex="-1">Communication - Advertising - BA</a></li>
									<li><a tabindex="-1">Communication - Broadcasting - BA</a></li>
									<li><a tabindex="-1">Communication - Communication Technology - BA</a></li>
									<li><a tabindex="-1">Communication - Journalism - BA</a></li>
									<li><a tabindex="-1">Communication - Journalism - Secondary Teaching Certification - BA</a></li>
									<li><a tabindex="-1">Communication - Public Relations - BA</a></li>
									<li><a tabindex="-1">Communications - MA</a></li>
									<li><a tabindex="-1">Communication Studies - Organizational Communication - BA</a></li>
									<li><a tabindex="-1">Communication Studies - Speech Communication - BA</a></li>
									<li><a tabindex="-1">Communication Studies - Speech Communication - Secondary Teaching Certification - BA</a></li>
									<li><a tabindex="-1">Computer Engineering - BSCSE</a></li>
									<li><a tabindex="-1">Computer Engineering - BS to PhD</a></li>
									<li><a tabindex="-1">Computer Engineering - MS</a></li>
									<li><a tabindex="-1">Computer Engineering - PhD</a></li>
									<li><a tabindex="-1">Computer Science-BSCS - BSCS</a></li>
									<li><a tabindex="-1">Computer Science - BS to PhD</a></li>
									<li><a tabindex="-1">Computer Science - MS</a></li>
									<li><a tabindex="-1">Computer Science - PhD</a></li>
									<li><a tabindex="-1">Criminology and Criminal Justice - BA</a></li>
									<li><a tabindex="-1">Criminology and Criminal Justice - MA</a></li>
									<li><a tabindex="-1">Criminology & Criminal Justice - Bachelor - BCRCJ</a></li>
									<li><a tabindex="-1">Critical Languages and International Studies - BA</a></li>
									<li><a tabindex="-1">Doctor of Nursing Practice - DNP</a></li>
									<li><a tabindex="-1">Earth and Environmental Science - BS to PhD</a></li>
									<li><a tabindex="-1">Earth and Environmental Science/Geology - MS</a></li>
									<li><a tabindex="-1">Earth and Environmental Science - PhD</a></li>
									<li><a tabindex="-1">Economics - BBA</a></li>
									<li><a tabindex="-1">Economics - BS</a></li>
									<li><a tabindex="-1">Economics - MA</a></li>
									<li><a tabindex="-1">Ed Curriculum & Instruction,  Mathematics - MEd</a></li>
									<li><a tabindex="-1">Ed Curriculum & Instruction - MEd</a></li>
									<li><a tabindex="-1">Ed Curriculum & Instruction, Science - MEd</a></li>
									<li><a tabindex="-1">Ed Curriculum & Instruction, Writing Focus - MEd</a></li>
									<li><a tabindex="-1">Ed Leadership & Policy Studies (K-16) - PhD</a></li>
									<li><a tabindex="-1">Ed Leadership & Policy Studies - MEd</a></li>
									<li><a tabindex="-1">Electrical Engineering - BSEE</a></li>
									<li><a tabindex="-1">Electrical Engineering - BS to PhD</a></li>
									<li><a tabindex="-1">Electrical Engineering Fast Track - BSEE</a></li>
									<li><a tabindex="-1">Electrical Engineering - MEngr </a></li>
									<li><a tabindex="-1">Electrical Engineering - MS</a></li>
									<li><a tabindex="-1">Electrical Engineering - MS Fast Track</a></li>
									<li><a tabindex="-1">Electrical Engineering - PhD</a></li>
									<li><a tabindex="-1">Engineering Management - MS</a></li>
									<li><a tabindex="-1">English - BA</a></li>
									<li><a tabindex="-1">English - MA</a></li>
									<li><a tabindex="-1">English - PhD</a></li>
									<li><a tabindex="-1">English Teaching - BA</a></li>
									<li><a tabindex="-1">English With Creative Writing Minor - BA</a></li>
									<li><a tabindex="-1">English With Writing Minor - BA</a></li>
									<li><a tabindex="-1">Environmental and Earth Science - BS</a></li>
									<li><a tabindex="-1">Executive Business Administration - MBA</a></li>
									<li><a tabindex="-1">Exercise Science - BS</a></li>
									<li><a tabindex="-1">Exercise Science - MS</a></li>
									<li><a tabindex="-1">Finance - BBA</a></li>
									<li><a tabindex="-1">Finance (Business Administration) - PhD</a></li>
									<li><a tabindex="-1">French - BA</a></li>
									<li><a tabindex="-1">French Teaching - BA</a></li>
									<li><a tabindex="-1">Geoinformatics - BS</a></li>
									<li><a tabindex="-1">Geoinformatics - BS</a></li>
									<li><a tabindex="-1">Geology - BA</a></li>
									<li><a tabindex="-1">Geology - BS</a></li>
									<li><a tabindex="-1">Geology Teaching - BA</a></li>
									<li><a tabindex="-1">Health Care Administration - MS</a></li>
									<li><a tabindex="-1">History - BA</a></li>
									<li><a tabindex="-1">History - MA</a></li>
									<li><a tabindex="-1">History Pre-Law BA - BA</a></li>
									<li><a tabindex="-1">History Teaching - BA</a></li>
									<li><a tabindex="-1">History Teaching with Social Studies - BA</a></li>
									<li><a tabindex="-1">Human Resource Management - MS</a></li>
									<li><a tabindex="-1">Industrial Engineering - BSIE</a></li>
									<li><a tabindex="-1">Industrial Engineering - BS to PhD</a></li>
									<li><a tabindex="-1">Industrial Engineering - MEngr</a></li>
									<li><a tabindex="-1">Industrial Engineering - MS</a></li>
									<li><a tabindex="-1">Industrial Engineering - PhD</a></li>
									<li><a tabindex="-1">Information Systems - BBA</a></li>
									<li><a tabindex="-1">Information Systems - BS</a></li>
									<li><a tabindex="-1">Information Systems (Business Administration) - PhD</a></li>
									<li><a tabindex="-1">Information Systems - MS</a></li>
									<li><a tabindex="-1">Interdisciplinary Science - MA</a></li>
									<li><a tabindex="-1">Interdisciplinary Studies - BAIS</a></li>
									<li><a tabindex="-1">Interdisciplinary Studies - BSIS</a></li>
									<li><a tabindex="-1">Interdisciplinary Studies - Education - BAIS</a></li>
									<li><a tabindex="-1">Interdisciplinary Studies - Education - BAIS</a></li>
									<li><a tabindex="-1">Interdisciplinary Studies Education - BSIS</a></li>
									<li><a tabindex="-1">Interior Design - BS</a></li>
									<li><a tabindex="-1">International Business Administration - French - BBA</a></li>
									<li><a tabindex="-1">International Business Administration - German - BBA</a></li>
									<li><a tabindex="-1">International Business Administration - Russian - BBA</a></li>
									<li><a tabindex="-1">International Business Administration - Spanish - BBA</a></li>
									<li><a tabindex="-1">Kinesiology All-Level - BA</a></li>
									<li><a tabindex="-1">Kinesiology - BA</a></li>
									<li><a tabindex="-1">Landscape Architecture - MLA</a></li>
									<li><a tabindex="-1">Linguistics - BA</a></li>
									<li><a tabindex="-1">Linguistics - MA</a></li>
									<li><a tabindex="-1">Linguistics - PhD</a></li>
									<li><a tabindex="-1">Logistics - MS</a></li>
									<li><a tabindex="-1">Management - BBA</a></li>
									<li><a tabindex="-1">Management (Business Administration) - PhD</a></li>
									<li><a tabindex="-1">Marketing - BBA</a></li>
									<li><a tabindex="-1">Marketing (Business Administration) - PhD</a></li>
									<li><a tabindex="-1">Marketing Research - MS</a></li>
									<li><a tabindex="-1">Materials Science and Engineering - BS to PhD</a></li>
									<li><a tabindex="-1">Materials Science and Engineering - MEngr</a></li>
									<li><a tabindex="-1">Materials Science and Engineering - MS</a></li>
									<li><a tabindex="-1">Materials Science and Engineering - PhD</a></li>
									<li><a tabindex="-1">Mathematical Sciences, Computer Science - PhD</a></li>
									<li><a tabindex="-1">Mathematical Sciences, Information Systems - PhD</a></li>
									<li><a tabindex="-1">Mathematical Sciences, Mathematics - PhD</a></li>
									<li><a tabindex="-1">Mathematics - BA</a></li>
									<li><a tabindex="-1">Mathematics - BS</a></li>
									<li><a tabindex="-1">Mathematics-BS Actuarial Science Option - BS</a></li>
									<li><a tabindex="-1">Mathematics-BS Biology Option - BS</a></li>
									<li><a tabindex="-1">Mathematics-BS Computer Science Option - BS</a></li>
									<li><a tabindex="-1">Mathematics-BS Industrial & Applied Mathematics Option - BS</a></li>
									<li><a tabindex="-1">Mathematics-BS Management Science/Operations Research Option - BS</a></li>
									<li><a tabindex="-1">Mathematics-BS Pure Math Option - BS</a></li>
									<li><a tabindex="-1">Mathematics-BS Statistics Option - BS</a></li>
									<li><a tabindex="-1">Mathematics (General Mathematics) - BS to PhD</a></li>
									<li><a tabindex="-1">Mathematics (General Mathematics) - MS</a></li>
									<li><a tabindex="-1">Mathematics (General Mathematics) - PhD</a></li>
									<li><a tabindex="-1">Mathematics (General Statistics) - BS to PhD</a></li>
									<li><a tabindex="-1">Mathematics (General Statistics) - MS</a></li>
									<li><a tabindex="-1">Mathematics (General Statistics) - PhD</a></li>
									<li><a tabindex="-1">Mathematics - MA</a></li>
									<li><a tabindex="-1">Mathematics Teaching - BA</a></li>
									<li><a tabindex="-1">Mathematics Teaching - MA</a></li>
									<li><a tabindex="-1">Mechanical Engineering - BSME</a></li>
									<li><a tabindex="-1">Mechanical Engineering - BS to PhD</a></li>
									<li><a tabindex="-1">Mechanical Engineering - MEngr</a></li>
									<li><a tabindex="-1">Mechanical Engineering - MS</a></li>
									<li><a tabindex="-1">Mechanical Engineering - PhD</a></li>
									<li><a tabindex="-1">Medical Technology - BS</a></li>
									<li><a tabindex="-1">Microbiology - BS</a></li>
									<li><a tabindex="-1">Mind, Brain and Education - MEd</a></li>
									<li><a tabindex="-1">Modern Languages - BA</a></li>
									<li><a tabindex="-1">Modern Languages (Spanish or French concentration) - MA</a></li>
									<li><a tabindex="-1">Music, All Level Band Emphasis - BM</a></li>
									<li><a tabindex="-1">Music, All Level Choral/Keyboard Concentration - BM</a></li>
									<li><a tabindex="-1">Music, All Level Choral/Voice Concentration - BM</a></li>
									<li><a tabindex="-1">Music, All Level Orchestra Emphasis - BM</a></li>
									<li><a tabindex="-1">Music, Business - BM</a></li>
									<li><a tabindex="-1">Music, Composition Concentration - BM</a></li>
									<li><a tabindex="-1">Music, Instrumental Orchestra Performance - BM</a></li>
									<li><a tabindex="-1">Music, Instrumental Performance Band - BM</a></li>
									<li><a tabindex="-1">Music, Jazz Studies - BM</a></li>
									<li><a tabindex="-1">Music, Keyboard Pedagogy - BM</a></li>
									<li><a tabindex="-1">Music, Keyboard Performance - BM</a></li>
									<li><a tabindex="-1">Music, Media - BM</a></li>
									<li><a tabindex="-1">Music - MM</a></li>
									<li><a tabindex="-1">Music Performance - MM</a></li>
									<li><a tabindex="-1">Music, Theatre - BM</a></li>
									<li><a tabindex="-1">Music, Theory Concentration - BM</a></li>
									<li><a tabindex="-1">Music, Voice Pedagogy - BM</a></li>
									<li><a tabindex="-1">Music, Voice Performance - BM</a></li>
									<li><a tabindex="-1">Nurse Practitioner, Adult / Gerontology Primary Care - MSN</a></li>
									<li><a tabindex="-1">Nurse Practitioner, MSN</a></li>
									<li><a tabindex="-1">Nursing Administration - BSN to PhD</a></li>
									<li><a tabindex="-1">Nursing Administration, MSN</a></li>
									<li><a tabindex="-1">Nursing - BSN</a></li>
									<li><a tabindex="-1">Nursing, Clinical - BSN to PhD</a></li>
									<li><a tabindex="-1">Nursing Education, MSN</a></li>
									<li><a tabindex="-1">Nursing Educator - BSN to PhD</a></li>
									<li><a tabindex="-1">Nursing - PhD</a></li>
									<li><a tabindex="-1">Nursing RN to BSN - BSN</a></li>
									<li><a tabindex="-1">Online Business Administration - Online MBA</a></li>
									<li><a tabindex="-1">Operations Management - BBA</a></li>
									<li><a tabindex="-1">Philosophy - BA</a></li>
									<li><a tabindex="-1">Philosophy General Track - BA</a></li>
									<li><a tabindex="-1">Philosophy Pre-Professional Track - BA</a></li>
									<li><a tabindex="-1">Physics and Applied Physics - BS to PhD</a></li>
									<li><a tabindex="-1">Physics and Applied Physics - PhD</a></li>
									<li><a tabindex="-1">Physics - BA</a></li>
									<li><a tabindex="-1">Physics - BS</a></li>
									<li><a tabindex="-1">Physics - MS</a></li>
									<li><a tabindex="-1">Physics Teaching - BA</a></li>
									<li><a tabindex="-1">Political Science - BA</a></li>
									<li><a tabindex="-1">Political Science - MA</a></li>
									<li><a tabindex="-1">Professional Accounting - MPA</a></li>
									<li><a tabindex="-1">Psychology - BA</a></li>
									<li><a tabindex="-1">Psychology - BS</a></li>
									<li><a tabindex="-1">Psychology, Experimental - MS</a></li>
									<li><a tabindex="-1">Psychology, Health/Neuroscience - MS</a></li>
									<li><a tabindex="-1">Psychology, Industrial and Organizational - MS</a></li>
									<li><a tabindex="-1">Public Administration - MPA</a></li>
									<li><a tabindex="-1">Public & Urban Administration - PhD</a></li>
									<li><a tabindex="-1">Quantitative Biology - BS to PhD</a></li>
									<li><a tabindex="-1">Quantitative Biology - PhD</a></li>
									<li><a tabindex="-1">Quantitative Finance - MS</a></li>
									<li><a tabindex="-1">Reading Specialist - MEd</a></li>
									<li><a tabindex="-1">Real Estate - BBA</a></li>
									<li><a tabindex="-1">Social Work - BSW</a></li>
									<li><a tabindex="-1">Social Work - MSSW</a></li>
									<li><a tabindex="-1">Social Work - MSSW  Advanced Specialty</a></li>
									<li><a tabindex="-1">Social Work - PhD</a></li>
									<li><a tabindex="-1">Sociology - BA</a></li>
									<li><a tabindex="-1">Sociology - MA</a></li>
									<li><a tabindex="-1">Software Engineering - BS</a></li>
									<li><a tabindex="-1">Software Engineering - MSWEN</a></li>
									<li><a tabindex="-1">Spanish - BA</a></li>
									<li><a tabindex="-1">Spanish Teaching - BA</a></li>
									<li><a tabindex="-1">Systems Engineering - MS</a></li>
									<li><a tabindex="-1">Taxation - MS</a></li>
									<li><a tabindex="-1">Teaching - Early Childhood - MEdT</a></li>
									<li><a tabindex="-1">Teaching English to Speakers of Other Languages (TESOL) - MA</a></li>
									<li><a tabindex="-1">Teaching - Middle Level - MEdT</a></li>
									<li><a tabindex="-1">Teaching - Secondary Level - MEdT​</a></li>
									<li><a tabindex="-1">Theatre Arts - BA</a></li>
									<li><a tabindex="-1">Theatre Arts - BFA</a></li>
									<li><a tabindex="-1">Theatre Arts Teaching - BA</a></li>
									<li><a tabindex="-1">Transatlantic History - BA to PhD</a></li>
									<li><a tabindex="-1">Transatlantic History - PhD</a></li>
									<li><a tabindex="-1">Urban Affairs - MA</a></li>
									<li><a tabindex="-1">Urban Planning & Public Policy - PhD</a></li>
									<li><a tabindex="-1">Other</a></li>
								</ul>
							</div>						
						</div>
						
						<!--<div class="form-group">
							<div class="btn-group">
								<button class="btn" id="button_reference" disabled>How did you hear about this Survey? [optional]</button>
								<button class="btn dropdown-toggle" data-toggle="dropdown">
									<span class="caret"></span>
								</button>								
								<ul class="dropdown-menu reference-options" role="menu" aria-labelledby="dropdownMenu">
									<li><a tabindex="-1">How did you hear about this Survey? [optional]</a></li>
									<li><a tabindex="-1">Social Media (Facebook/Twitter/Google+)</a></li>
									<li><a tabindex="-1">UTA, CSE Department</a></li>
									<li><a tabindex="-1">UTA, Communication Department</a></li>
									<li><a tabindex="-1">Other</a></li>
								</ul>
							</div>
						</div>-->
					</form>
				</div>
				<div class="modal-footer">
					<a id="button_new_user_submit" tabindex="0" class="btn btn-primary" role="button">Submit</a>
				</div>
			</div>
		</div>
	</div>

	<div id="modal_ajax_loader" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<img src="image/ajax-loader.gif" class="img-responsive">
				</div>
			</div>
		</div>
	</div>

	<!-- Main jumbotron for a primary marketing message or call to action -->
	<div class="jumbotron">
		<div class="container">
			<br>
			<br>
			<br>
			<div class='center-block'><p class='justified_para'><h2>Thank you for participating in data collection for IDIR project!<br></h2><!--<h4>ClaimBuster uses computational power to do tasks that are tedious and time-consuming for fact-checkers, such as finding claims by politicians that should be checked.<br>Note: Participants are paid for every labeled sentence. Pay rate depends on work quality and the length/complexity of labeled sentences.</h4>--></p></div>
			<br>
			<!--<p class='justified_para'><h2>ClaimBuster data collection is PAUSED for now. If you have contributed, you can login and see your payment by clicking the "Leaderboard" button.</h2></p>-->
			<h3>1. ClaimBuster Data Annotation</h2>
			<div class='row'>
				<div class='col-md-2'></div>
				<div class='col-md-8'>
					<div class="embed-responsive embed-responsive-16by9">
						<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/R30KinquvvQ"></iframe>
					</div>
				</div>
				<div class='col-md-2'></div>
			</div>

			<h3>2. WildFire Stance Detection Annotation</h2>
			<div class='row'>
				<div class='col-md-2'></div>
				<div class='col-md-8'>
					<div class="embed-responsive embed-responsive-16by9">
						<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/R30KinquvvQ"></iframe>
					</div>
				</div>
				<div class='col-md-2'></div>
			</div>
			
		<!--	<br>
			<p class='justified_para'>Politicians make factual statements all the time. Some of these factual statements are interesting & important to many people and some are not. Also, not all factual statements are true. Journalists and reporters spend good amount of time to check veracity/truthfulness of interesting factual statements.</p>
			<br><div class="well"><p class='justified_para'>For example, <i>"Our Air Force is older and smaller than at any time since it was founded in 1947."</i	>, said Republican candidate Mitt Romney on <a href="http://www.debates.org/index.php?page=october-22-2012-the-third-obama-romney-presidential-debate">third presidential debate in 2012</a>. Little did he knew that journalists will fact-check the statement and <a href="http://www.politifact.com/truth-o-meter/statements/2012/jan/18/mitt-romney/mitt-romney-says-us-navy-smallest-1917-air-force-s/">prove it false</a>. Similar false statements were also made by the other candidate, President Barack Obama. List of false statements in third presidential debate made by either candidates can be found <a href="http://www.politifact.com/truth-o-meter/article/2012/oct/22/fact-checking-third-presidential-debate/">here</a>.</p></div>
			<p class='justified_para'><br>We want to determine  whether a sentence contains a factual statement and whether its truthfulness should be checked. You can help us by telling which factual statements in previous Presidential debates are check-worthy and would benefit the voting public. We believe this can be useful for journalist and reporters.
			<br>
			<br>Please feel free to use the 'Feedback' button (to the right of the browser window) to inform us your suggestions and/or report errors. You can also contact us at <a href="mailto:classifyfact@gmail.com">classifyfact@gmail.com</a>. Thanks!</p>

			<button id="button_survey" type="button" class="btn btn-primary">Go to Survey</button>-->
		</div>
	</div>

	<div class="modal fade" id="feedback_modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div class="form-group">
						<div class="btn-group">
							<button class="btn" id="button_feedback_type" disabled>This is about---</button>
							<button class="btn dropdown-toggle" data-toggle="dropdown">
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu feedback-options" role="menu" aria-labelledby="dropdownMenu">
								<li><a tabindex="-1">Reporting problem</a></li>
								<li><a tabindex="-1">Suggestion</a></li>
								<li><a tabindex="-1">Something else</a></li>
							</ul>
						</div>
					</div>
					<textarea id="textarea_feedback" class="form-control" rows="3" placeholder="Please report any problem or give your thoughts!"></textarea>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button id="button_feedback_send" type="button" class="btn btn-primary">Send</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	<div class="modal fade" id="modal_leaderboard">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Leaderboard</h4>
				</div>
				<div class="modal-body">
					<div class='row'>
						<!--<div class='col-md-3'>
							<div class='winner'> Award winners</div>
						</div>-->
						<!--<div class='col-md-3'>
							<div class='winner_10'>$10 winner</div>
						</div>-->
					</div>
					<ul id="selectable_leaderboard" class="list-group top_margin"></ul>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<div class="modal fade" id="modal_consent">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div>
						<button type="button" class="btn btn-primary button_consent_accept pull-right">Accept</button>
						<div align = "center">
							<p>
								<b>UT Arlington
								<br>
								Informed Consent Document</b>
							</p>
						</div>
						
						<b>CONSENT</b>
						<p class='justified_para'>
							By clicking “Accept’’ below, you confirm that you are 18 years of age or older and have read or had this document read to you.  You have been informed about this study’s purpose, procedures, possible benefits and risks, and you have received a copy of this form. You have been given the opportunity to ask questions before you click “Accept’’, and you have been told that you can ask other questions at any time.
							<br>
							<br>
You voluntarily agree to participate in this study.  By clicking “Accept’’, you are not waiving any of your legal rights.  Refusal to participate will involve no penalty or loss of benefits to which you are otherwise entitled.  You may discontinue participation at any time without penalty or loss of benefits, to which you are otherwise entitled.
						</p>
						
						<b>PRINCIPAL INVESTIGATOR</b>
						<p class='justified_para'>
							Chengkai Li<br>
							Associate Professor<br>
							Department of Computer Science and Engineering<br>
							(817) 272-0162<br>
							<a href='mailto:cli@uta.edu'>cli@uta.edu</a><br>
						</p>
						
						<b>TITLE OF PROJECT</b>
						<p class='justified_para'>
							From Answering Questions to Questioning Answers (and Questions)---Perturbation Analysis of Database Queries
						</p>

						<b>INTRODUCTION</b>
						<p class='justified_para'>
							You are being asked to participate in a research study about how to detect and counter claims of ``fact’’ which may or may not be true (so-called “lies, d—ed lies, and statistics”) that we often see in news and ads.  Your participation is voluntary.  Refusal to participate or discontinuing your participation at any time will involve no penalty or loss of benefits to which you are otherwise entitled.  Please ask questions if there is anything you do not understand.
						</p>

						<b>PURPOSE</b>
						<p class='justified_para'>
							We want to determine whether a sentence contains a factual statement and whether its truthfulness should be checked. You can help us by telling which factual statements in previous Presidential debates are check-worthy and would benefit the voting public. Your responses will help us collect “training data” in developing automatic fact-checking algorithms.
						</p>

						<b>DURATION</b>
						<p class='justified_para'>
							Participants in this study have the full freedom to decide the duration of their participation, from a couple of minutes to as long as they prefer. It is requested that you participate in the study for at least 25 minutes, but you are free to discontinue your participation at any time. You will earn an entry in our prize lottery for each sentence task that you complete. You can participate as long as you would like in order to continue earning entries into the prize lottery. 
						</p>

						<b>NUMBER OF PARTICIPANTS</b>
						<p class='justified_para'>
							The maximum number of anticipated participants in this research study is 1000.
						</p>

						<b>PROCEDURES </b>
						<p class='justified_para'>
							The procedures which will involve you as a research participant include:						
							<br>
							<br>
							1. Complete a short registration form with basic information, such as your name, email address. This step should take less than 5 minutes to complete. We will use your name and email only for sharing research results with you and for soliciting participation in future related studies. We will delete your name and email once we have determined that we do not need to follow up with any of our participants.
							<br>
							<br>
							2. Using the registered account, you will log into our website. On this website, you will be asked to read sentences and determine if each sentence contains a factual statement and if its truthfulness should be checked. The website will record your responses as well as the timestamps of your interactions with the website. The website will not request or access any personal information. Participants in this study have the full freedom to decide the duration of their participation, from a couple of minutes to as long as they prefer. The participants are expected to spend at least 25 minutes.
						</p>

						<b>POSSIBLE BENEFITS </b>
						<p class='justified_para'>
							At the personal level, a participant may learn to appreciate data and quantitative analysis, and to interpret results critically.
							<br>
							<br>
							At the society level, the results of this study will benefit research and practice of data-driven fact-checking and decision making, which have a wide range of applications—such as public policy, journalism, urban planning, business intelligence, and health care with benefits to the society.
						</p>

						<b>POSSIBLE RISKS/DISCOMFORTS </b>
						<p class='justified_para'>
							There are no perceived risks or discomforts for participating in this research study.  Should you experience any discomfort please inform the researcher, you have the right to quit any study procedures at any time at no consequence. 
						</p>

						<b>COMPENSATION </b>
						<p class='justified_para'>	
							Your participation in this study will enter you into a random drawing for prizes. The more you participate the more entries you will have in this prize lottery. More specifically, your response to each sentence will give you an entry in the lottery. At the end of this study (after all the 20000 sentences in our dataset have received responses), we will provide prizes accordingly (in Amazon.com electronic gift cards). There will be one grand prize of $200, two prizes of $100 each, and twenty prizes of $10 each. Participants will be notified through emails if they have won prizes.
						</p>
						<b>ALTERNATIVE PROCEDURES</b>
						<p class='justified_para'>	
							There are no alternative procedures offered for this study.  However, you can elect not to participate in the study or quit at any time at no consequence.
						</p>

						<b>VOLUNTARY PARTICIPATION</b>
						<p class='justified_para'>	
							Participation in this research study is voluntary. You have the right to decline participation in any or all study procedures or quit at any time at no consequence.
						</p>
						
						<b>CONFIDENTIALITY</b>
						<p class='justified_para'>
							Every attempt will be made to see that your study results are kept confidential.  All data collected from this study will be stored in the Department of Computer Science and Engineering at the University of Texas at Arlington for at least three (3) years after the end of this research.  The results of this study may be published and/or presented at meetings without naming you as a participant.  Additional research studies could evolve from the information you have provided, but your information will not be linked to you in anyway; it will be anonymous.  Although your rights and privacy will be maintained, the Secretary of the Department of Health and Human Services, the UTA Institutional Review Board (IRB), and personnel particular to this research have access to the study records.  Your records will be kept completely confidential according to current legal requirements.  They will not be revealed unless required by law, or as noted above.  The IRB at UTA has reviewed and approved this study and the information within this consent form.  If in the unlikely event it becomes necessary for the Institutional Review Board to review your research records, the University of Texas at Arlington will protect the confidentiality of those records to the extent permitted by law.
						</p>
						
						<b>CONTACT FOR QUESTIONS</b>
						<p class='justified_para'>
							Questions about this research study may be directed to Chengkai Li at (817) 272-0162 or <a href='mailto:cli@uta.edu'>cli@uta.edu</a>.  Any questions you may have about your rights as a research participant or a research-related injury may be directed to the Office of Research Administration; Regulatory Services at 817-272-2105 or <a href='mailto:regulatoryservices@uta.edu'>regulatoryservices@uta.edu</a>.
						</p>
						
						<b>CONSENT</b>
						<p class='justified_para'>
							By clicking “Accept’’ below, you confirm that you are 18 years of age or older and have read or had this document read to you.  You have been informed about this study’s purpose, procedures, possible benefits and risks, and you have received a copy of this form. You have been given the opportunity to ask questions before you click “Accept’’, and you have been told that you can ask other questions at any time.
							<br>
							<br>
You voluntarily agree to participate in this study.  By clicking “Accept’’, you are not waiving any of your legal rights.  Refusal to participate will involve no penalty or loss of benefits to which you are otherwise entitled.  You may discontinue participation at any time without penalty or loss of benefits, to which you are otherwise entitled.
						</p>
						
						<button type="button" class="btn btn-primary button_consent_accept pull-right">Accept</button>
						<br>
						<br>
						<!--<br>
						IRB  Approval Date:
                        <br>                                                          
						IRB Expiration Date:-->
					</div>
				</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	<div id="feedback">
		<a href="#" data-toggle="modal" data-target="#feedback_modal">
			<img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSI5LjI5OXB4IiBoZWlnaHQ9IjUwLjYyNXB4IiB2aWV3Qm94PSIwIDAgOS4yOTkgNTAuNjI1IiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCA5LjI5OSA1MC42MjUiIHhtbDpzcGFjZT0icHJlc2VydmUiPjxnPjxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik0zLjUxNiw0Ni44N3YxLjYzNWg1LjY2N3YwLjk3NEgzLjUxNnYxLjE0N2gtMC40NGwtMC4zNTItMS4xNDdIMi4zNjdDMC43ODksNDkuNDc4LDAsNDguNzg4LDAsNDcuNDA5YzAtMC4zNCwwLjA2OC0wLjczOCwwLjIwNS0xLjE5NWwwLjc3OSwwLjI1MmMtMC4xMjEsMC4zNzUtMC4xODIsMC42OTUtMC4xODIsMC45NjFjMCwwLjM2NywwLjEyMiwwLjY0LDAuMzY2LDAuODE0YzAuMjQ0LDAuMTc2LDAuNjM2LDAuMjY0LDEuMTc1LDAuMjY0SDIuNzZWNDYuODdIMy41MTZ6Ii8+PHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTkuMjk5LDQyLjk4NWMwLDAuOTQ5LTAuMjg5LDEuNjk3LTAuODY2LDIuMjQ2Yy0wLjU3OCwwLjU0OS0xLjM4MiwwLjgyNC0yLjQwOCwwLjgyNGMtMS4wMzYsMC0xLjg1Ny0wLjI1Ni0yLjQ2Ny0wLjc2NmMtMC42MS0wLjUxMS0wLjkxNC0xLjE5My0wLjkxNC0yLjA1NGMwLTAuODA1LDAuMjY1LTEuNDQsMC43OTQtMS45MDljMC41MjktMC40NywxLjIyOC0wLjcwMywyLjA5NS0wLjcwM2gwLjYxNXY0LjQyNGMwLjc1NC0wLjAyMSwxLjMyNi0wLjIxMSwxLjcxNy0wLjU3MlM4LjQ1LDQzLjYwNiw4LjQ1LDQyLjk1YzAtMC42OTEtMC4xNDUtMS4zNzUtMC40MzQtMi4wNTFoMC44NjZjMC4xNDgsMC4zNDQsMC4yNTUsMC42NjgsMC4zMTksMC45NzVDOS4yNjcsNDIuMTgxLDkuMjk5LDQyLjU1MSw5LjI5OSw0Mi45ODV6IE0zLjQ1Nyw0My4yNDljMCwwLjUxNywwLjE2OCwwLjkyNiwwLjUwNCwxLjIzMnMwLjgwMSwwLjQ4OCwxLjM5NiwwLjU0M3YtMy4zNTdjLTAuNjEzLDAtMS4wODMsMC4xMzgtMS40MSwwLjQxQzMuNjIxLDQyLjM1LDMuNDU3LDQyLjc0MSwzLjQ1Nyw0My4yNDl6Ii8+PHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTkuMjk5LDM2LjI1MmMwLDAuOTQ5LTAuMjg5LDEuNjk3LTAuODY2LDIuMjQ2Yy0wLjU3OCwwLjU0OS0xLjM4MiwwLjgyNC0yLjQwOCwwLjgyNGMtMS4wMzYsMC0xLjg1Ny0wLjI1Ni0yLjQ2Ny0wLjc2NmMtMC42MS0wLjUxLTAuOTE0LTEuMTkzLTAuOTE0LTIuMDUzYzAtMC44MDUsMC4yNjUtMS40NDEsMC43OTQtMS45MXMxLjIyOC0wLjcwMywyLjA5NS0wLjcwM2gwLjYxNXY0LjQyNGMwLjc1NC0wLjAyLDEuMzI2LTAuMjExLDEuNzE3LTAuNTcyczAuNTg2LTAuODY5LDAuNTg2LTEuNTI1YzAtMC42OTEtMC4xNDUtMS4zNzUtMC40MzQtMi4wNTFoMC44NjZjMC4xNDgsMC4zNDQsMC4yNTUsMC42NjgsMC4zMTksMC45NzZTOS4yOTksMzUuODE5LDkuMjk5LDM2LjI1MnogTTMuNDU3LDM2LjUxNmMwLDAuNTE2LDAuMTY4LDAuOTI2LDAuNTA0LDEuMjMyYzAuMzM2LDAuMzA4LDAuODAxLDAuNDg4LDEuMzk2LDAuNTQzdi0zLjM1N2MtMC42MTMsMC0xLjA4MywwLjEzNy0xLjQxLDAuNDFDMy42MjEsMzUuNjE4LDMuNDU3LDM2LjAwOCwzLjQ1NywzNi41MTZ6Ii8+PHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTguMzE5LDI3Ljg2MnYwLjA1M2MwLjY1MiwwLjQ0OSwwLjk3OSwxLjEyMSwwLjk3OSwyLjAxN2MwLDAuODQtMC4yODYsMS40OTMtMC44NiwxLjk2UzcuMDQ3LDMyLjU5LDUuOTg5LDMyLjU5Yy0xLjA1OCwwLTEuODgxLTAuMjMzLTIuNDY3LTAuNzAyYy0wLjU4Ni0wLjQ3LTAuODc5LTEuMTIxLTAuODc5LTEuOTU3YzAtMC44NzEsMC4zMTYtMS41MzksMC45NDktMi4wMDVWMjcuODVsLTAuNDYzLDAuMDQxbC0wLjQ1MSwwLjAyM0gwLjA2NHYtMC45NzRoOS4xMTd2MC43OTFMOC4zMTksMjcuODYyeiBNOC40ODMsMjkuODA3YzAtMC42NjQtMC4xOC0xLjE0Ni0wLjU0MS0xLjQ0NHMtMC45NDQtMC40NDgtMS43NS0wLjQ0OEg1Ljk4N2MtMC45MDksMC0xLjU1OSwwLjE1MS0xLjk0NywwLjQ1NWMtMC4zODgsMC4zMDMtMC41ODMsMC43ODUtMC41ODMsMS40NDljMCwwLjU3LDAuMjIyLDEuMDA4LDAuNjY1LDEuMzExUzUuMTksMzEuNTgzLDYsMzEuNTgzYzAuODE5LDAsMS40MzgtMC4xNDksMS44NTYtMC40NTFDOC4yNzQsMzAuODMxLDguNDgzLDMwLjM4OSw4LjQ4MywyOS44MDd6Ii8+PHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTIuNjU0LDIxLjg5MWMwLTAuODQ0LDAuMjg4LTEuNDk5LDAuODY0LTEuOTY2YzAuNTc2LTAuNDY3LDEuMzkyLTAuNywyLjQ0Ni0wLjdjMS4wNTUsMCwxLjg3NCwwLjIzNSwyLjQ1OCwwLjcwNmMwLjU4NCwwLjQ3MSwwLjg3NiwxLjEyNCwwLjg3NiwxLjk2YzAsMC40MTgtMC4wNzYsMC44LTAuMjMsMS4xNDZjLTAuMTU0LDAuMzQ2LTAuMzkzLDAuNjM2LTAuNzEzLDAuODd2MC4wN2wwLjgyNiwwLjIwNXYwLjY5N0gwLjA2NHYtMC45NzNoMi4yMTVjMC40OTYsMCwwLjk0MSwwLjAxNiwxLjMzNiwwLjA0N3YtMC4wNDdDMi45NzUsMjMuNDU0LDIuNjU0LDIyLjc4MiwyLjY1NCwyMS44OTF6IE0zLjQ2OSwyMi4wMzJjMCwwLjY2NCwwLjE5LDEuMTQzLDAuNTcxLDEuNDM2czEuMDIyLDAuNDM5LDEuOTI1LDAuNDM5YzAuOTAzLDAsMS41NDgtMC4xNSwxLjkzNy0wLjQ1MWMwLjM5LTAuMzAxLDAuNTg0LTAuNzgzLDAuNTg0LTEuNDQ3YzAtMC41OTgtMC4yMTktMS4wNDMtMC42NTMtMS4zMzZzLTEuMDYyLTAuNDM5LTEuODc4LTAuNDM5Yy0wLjgzNiwwLTEuNDU5LDAuMTQ2LTEuODY5LDAuNDM5UzMuNDY5LDIxLjQxOCwzLjQ2OSwyMi4wMzJ6Ii8+PHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTkuMTgzLDEzLjU3N0w4LjI2OSwxMy43N3YwLjA0N2MwLjQwMSwwLjMyLDAuNjc0LDAuNjQsMC44MTYsMC45NThzMC4yMTQsMC43MTYsMC4yMTQsMS4xOTJjMCwwLjYzNy0wLjE2NCwxLjEzNi0wLjQ5MSwxLjQ5N2MtMC4zMjgsMC4zNjEtMC43OTUsMC41NDItMS40LDAuNTQyYy0xLjI5NywwLTEuOTc3LTEuMDM3LTIuMDM5LTMuMTExbC0wLjAzNS0xLjA5SDQuOTM0Yy0wLjUwNCwwLTAuODc2LDAuMTA4LTEuMTE2LDAuMzI1Yy0wLjI0LDAuMjE3LTAuMzYsMC41NjMtMC4zNiwxLjA0YzAsMC41MzUsMC4xNjQsMS4xNDEsMC40OTIsMS44MTZsLTAuNzQ0LDAuMjk5Yy0wLjE3Mi0wLjMxNi0wLjMwNy0wLjY2My0wLjQwNC0xLjA0Yy0wLjA5Ny0wLjM3Ny0wLjE0Ni0wLjc1NS0wLjE0Ni0xLjEzNGMwLTAuNzY2LDAuMTctMS4zMzMsMC41MS0xLjcwMmMwLjM0LTAuMzY5LDAuODg1LTAuNTU0LDEuNjM1LTAuNTU0aDQuMzg0djAuNzIySDkuMTgzeiBNOC40OTYsMTUuNzc0YzAtMC42MDUtMC4xNjYtMS4wODEtMC40OTgtMS40MjdjLTAuMzMyLTAuMzQ2LTAuNzk3LTAuNTE5LTEuMzk2LTAuNTE5aC0wLjU4bDAuMDQxLDAuOTczYzAuMDI3LDAuNzczLDAuMTQ3LDEuMzMxLDAuMzYsMS42NzNjMC4yMTQsMC4zNDIsMC41NDQsMC41MTMsMC45OTMsMC41MTNjMC4zNTIsMCwwLjYxOS0wLjEwNiwwLjgwMy0wLjMxOVM4LjQ5NiwxNi4xNTcsOC40OTYsMTUuNzc0eiIvPjxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik05LjI5OSw4LjI4NWMwLDAuOTMtMC4yODYsMS42NDktMC44NTgsMi4xNTljLTAuNTcxLDAuNTEtMS4zODEsMC43NjUtMi40MjgsMC43NjVjLTEuMDc1LDAtMS45MDUtMC4yNTktMi40OTEtMC43NzZTMi42NDMsOS4xNzgsMi42NDMsOC4yMjFjMC0wLjMwOSwwLjAzMy0wLjYxNywwLjEtMC45MjZDMi44MSw2Ljk4NiwyLjg4OCw2Ljc0NCwyLjk3Nyw2LjU2OGwwLjgyNiwwLjI5OUMzLjcxNyw3LjA4MiwzLjY0Niw3LjMxNiwzLjU4OSw3LjU3QzMuNTMyLDcuODI0LDMuNTA0LDguMDQ5LDMuNTA0LDguMjQ0YzAsMS4zMDUsMC44MzIsMS45NTcsMi40OTYsMS45NTdjMC43ODksMCwxLjM5Ni0wLjE1OSwxLjgxNS0wLjQ3OGMwLjQyMi0wLjMxOSwwLjYzNC0wLjc5LDAuNjM0LTEuNDE1YzAtMC41MzUtMC4xMTUtMS4wODQtMC4zNDctMS42NDZoMC44NjFDOS4xODgsNy4wOTIsOS4yOTksNy42MzMsOS4yOTksOC4yODV6Ii8+PHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTUuODk2LDQuMTc4QzUuNjU2LDQuMDEsNS4zNDQsMy43NTQsNC45NTcsMy40MUwyLjc2LDEuMzM2VjAuMTgybDIuNzM2LDIuNjAyTDkuMTgzLDB2MS4xNzhMNi4xNDYsMy40NDVsMC42MzMsMC43MzJoMi40MDJ2MC45NjFIMC4wNjR2LTAuOTZoNC44MzRjMC4yMTUsMCwwLjU0NiwwLjAxNiwwLjk5NiwwLjA0N1Y0LjE3OEg1Ljg5NnoiLz48L2c+PC9zdmc+" alt="Feedback" title="Feedback Button" height="70px" />
		</a>
	</div>


	<!--<div class="footer">
        	<div class="container">        						
				<div class="row">
					<div class="col-md-5"> </div>					
					<div class="col-md-2">
						<a href="http://idir.uta.edu/"><img class="img-responsive" id="idir_logo" src="image/Idirlogo.png" alt="IDIR Logo"></a>
					</div>
					<div class="col-md-5"> </div>
				</div>								
      		</div>
    	</div>-->


	<!-- Bootstrap core JavaScript
		================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="bootstrap-3.3.2-dist/js/bootstrap.min.js"></script>
	<script src="js/index.js"></script>
	<script src="iCheck/icheck.js"></script>
	<script>
		(function(i, s, o, g, r, a, m) {
			i['GoogleAnalyticsObject'] = r;
			i[r] = i[r] || function() {
				(i[r].q = i[r].q || []).push(arguments)
			}, i[r].l = 1 * new Date();
			a = s.createElement(o),
				m = s.getElementsByTagName(o)[0];
			a.async = 1;
			a.src = g;
			m.parentNode.insertBefore(a, m)
		})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

		ga('create', 'UA-52917502-3', 'auto');
		ga('send', 'pageview');
	</script>
</body>

</html>
