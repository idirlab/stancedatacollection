<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">

	<link rel="icon" type="image/png" href="image/idirlogo2-small-icon.png" />
	<title>Wildfire Annotation Tool</title>

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
					<img class='img-responsive claimbuster-logo' src="image/claimbuster_wout_text.png">
				</a>
				<a class="navbar-brand claimbuster-title" href="index.php">Wildfire annotation tool</a>
			</div>

			<div id="navbar_signin_signup" class="navbar-collapse collapse">
				<form class="navbar-form navbar-left">
					<div class="row">
						<div class="col-sm-7">
							<div class="form-group">
								<input type="text" placeholder="AccountID" class="form-control" id="input_username">
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
			<div class='center-block'>
				<p class='justified_para'>
				<h2>Thank you for participating in truthfulness stance annotation!<br></h2>
				<br>
				<!-- <h3>WildFire Stance Detection Annotation Tutorial</h2>
			<div class='row'>
				<div class='col-md-2'></div>
				<div class='col-md-8'>
					<div class="embed-responsive embed-responsive-16by9">
						<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/R30KinquvvQ"></iframe>
					</div>
				</div>
				<div class='col-md-2'></div>
			</div> -->
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
							<div align="center">
								<p>
									<b>UT Arlington
										<br>
										Informed Consent Document</b>
								</p>
							</div>

							<b>CONSENT</b>
							<p class='justified_para'>
								By clicking ???Accept?????? below, you confirm that you are 18 years of age or older and have read or had this document read to you. You have been informed about this study???s purpose, procedures, possible benefits and risks, and you have received a copy of this form. You have been given the opportunity to ask questions before you click ???Accept??????, and you have been told that you can ask other questions at any time.
								<br>
								<br>
								You voluntarily agree to participate in this study. By clicking ???Accept??????, you are not waiving any of your legal rights. Refusal to participate will involve no penalty or loss of benefits to which you are otherwise entitled. You may discontinue participation at any time without penalty or loss of benefits, to which you are otherwise entitled.
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
								You are being asked to participate in a research study about how to detect and counter claims of ``fact?????? which may or may not be true (so-called ???lies, d???ed lies, and statistics???) that we often see in news and ads.?? Your participation is voluntary. Refusal to participate or discontinuing your participation at any time will involve no penalty or loss of benefits to which you are otherwise entitled. Please ask questions if there is anything you do not understand.
							</p>

							<b>PURPOSE</b>
							<p class='justified_para'>
								We want to determine whether a sentence contains a factual statement and whether its truthfulness should be checked. You can help us by telling which factual statements in previous Presidential debates are check-worthy and would benefit the voting public. Your responses will help us collect ???training data??? in developing automatic fact-checking algorithms.
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
								At the society level, the results of this study will benefit research and practice of data-driven fact-checking and decision making, which have a wide range of applications???such as public policy, journalism, urban planning, business intelligence, and health care with benefits to the society.
							</p>

							<b>POSSIBLE RISKS/DISCOMFORTS </b>
							<p class='justified_para'>
								There are no perceived risks or discomforts for participating in this research study. Should you experience any discomfort please inform the researcher, you have the right to quit any study procedures at any time at no consequence.
							</p>

							<b>COMPENSATION </b>
							<p class='justified_para'>
								Your participation in this study will enter you into a random drawing for prizes. The more you participate the more entries you will have in this prize lottery. More specifically, your response to each sentence will give you an entry in the lottery. At the end of this study (after all the 20000 sentences in our dataset have received responses), we will provide prizes accordingly (in Amazon.com electronic gift cards). There will be one grand prize of $200, two prizes of $100 each, and twenty prizes of $10 each. Participants will be notified through emails if they have won prizes.
							</p>
							<b>ALTERNATIVE PROCEDURES</b>
							<p class='justified_para'>
								There are no alternative procedures offered for this study. However, you can elect not to participate in the study or quit at any time at no consequence.
							</p>

							<b>VOLUNTARY PARTICIPATION</b>
							<p class='justified_para'>
								Participation in this research study is voluntary. You have the right to decline participation in any or all study procedures or quit at any time at no consequence.
							</p>

							<b>CONFIDENTIALITY</b>
							<p class='justified_para'>
								Every attempt will be made to see that your study results are kept confidential. All data collected from this study will be stored in the Department of Computer Science and Engineering at the University of Texas at Arlington for at least three (3) years after the end of this research. The results of this study may be published and/or presented at meetings without naming you as a participant. Additional research studies could evolve from the information you have provided, but your information will not be linked to you in anyway; it will be anonymous. Although your rights and privacy will be maintained, the Secretary of the Department of Health and Human Services, the UTA Institutional Review Board (IRB), and personnel particular to this research have access to the study records. Your records will be kept completely confidential according to current legal requirements. They will not be revealed unless required by law, or as noted above. The IRB at UTA has reviewed and approved this study and the information within this consent form. If in the unlikely event it becomes necessary for the Institutional Review Board to review your research records, the University of Texas at Arlington will protect the confidentiality of those records to the extent permitted by law.
							</p>

							<b>CONTACT FOR QUESTIONS</b>
							<p class='justified_para'>
								Questions about this research study may be directed to Chengkai Li at (817) 272-0162 or <a href='mailto:cli@uta.edu'>cli@uta.edu</a>. Any questions you may have about your rights as a research participant or a research-related injury may be directed to the Office of Research Administration; Regulatory Services at 817-272-2105 or <a href='mailto:regulatoryservices@uta.edu'>regulatoryservices@uta.edu</a>.
							</p>

							<b>CONSENT</b>
							<p class='justified_para'>
								By clicking ???Accept?????? below, you confirm that you are 18 years of age or older and have read or had this document read to you. You have been informed about this study???s purpose, procedures, possible benefits and risks, and you have received a copy of this form. You have been given the opportunity to ask questions before you click ???Accept??????, and you have been told that you can ask other questions at any time.
								<br>
								<br>
								You voluntarily agree to participate in this study. By clicking ???Accept??????, you are not waiving any of your legal rights. Refusal to participate will involve no penalty or loss of benefits to which you are otherwise entitled. You may discontinue participation at any time without penalty or loss of benefits, to which you are otherwise entitled.
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
