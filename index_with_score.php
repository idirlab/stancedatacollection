<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="page-identifier" content="stance_annotation_index">

	<link rel="icon" type="image/png" href="image/wildfire_wout_text.png" />
	<title>Truthfulness Stance Annotation</title>

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
					<span id="navbar_menu_text" style="color:white">Sign Up/Log in</span>
				</button>
				<a class="navbar-brand" href="index.php" title="StanceAnnotation">
					<img class='img-responsive wildfire-logo' src="image/wildfire_wout_text.png">
				</a>
				<a class="navbar-brand claimbuster-title" href="index.php" style="font-size: 20px">Truthfulness Stance Annotation</a>
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
						<div class="form-group col-sm-3">
							<button id="button_sign_in" type="button" class="btn btn-success pull_top">Log In</button>
							<button id="button_forgot_password" type="button" class="btn btn-link">Forgot username/password?</button>
						</div>
						<div class="col-sm-2">
							<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_new_user">Sign up</button>
						</div>
					</div>
				</form>
			</div>

			<!--/.navbar-collapse -->
			<div id="navbar_logout" class="navbar-collapse collapse">
				<form id="navbar_info_form" class="navbar-form navbar-right">
					<div class="row">
						<ul class="nav nav-pills" role="tablist">
							<li role="presentation">
								<span id="span_username"></span> <span id="span_badge" class="badge">0</span><span> pairs</span>
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
				
			<div class='row'>
				<div class='col-md-2'></div>
				<div class='col-md-8'>
					<!-- embed logo image-->					
					<div class='center-block' >
						<!-- make the image smaller -->
						<img src="image/wildfire_wout_text.png" class="img-responsive" style='width:80%'>
				</div>
				<div class='col-md-2'></div>
			</div>
			</div>
		</div>
	</div>
	<br><br><br><br><br><br><br>
	<div id="disqus_thread"></div>
	<script>
		/**
		*  RECOMMENDED CONFIGURATION VARIABLES: EDIT AND UNCOMMENT THE SECTION BELOW TO INSERT DYNAMIC VALUES FROM YOUR PLATFORM OR CMS.
		*  LEARN WHY DEFINING THESE VARIABLES IS IMPORTANT: https://disqus.com/admin/universalcode/#configuration-variables    */
		var disqus_config = function () {
		this.page.url = 'https://idir.uta.edu/stance_annotation/';  // Replace PAGE_URL with your page's canonical URL variable
		this.page.identifier = 'https://idir.uta.edu/stance_annotation'; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
		};
		(function() { // DON'T EDIT BELOW THIS LINE
		var d = document, s = d.createElement('script');
		s.src = 'https://idir-stance-annotation.disqus.com/embed.js';
		s.setAttribute('data-timestamp', +new Date());
		(d.head || d.body).appendChild(s);
		})();
	</script>
	<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
	
	<footer id="contact" style=" padding-top:15px; border-top:3px solid grey;">
		<div class="container">
			<div class="row">
				<!-- <div class="col-md-1"></div> -->
				<div class="col-md-3" style="text-align:left">
				<a target="_blank" href="https://idir.uta.edu/"> <img src="image/Idir_full_logo.png" alt="iDiR Logo" style=" width:90%;height:90%;"></a>
				</div>
				<div class="col-md-5" style="text-align:center; color:#1A1A1A; font-size: 100% !important;">
						Engineering Research Building (ERB), Room 414<br>
						500 UTA Boulevard<br>
							
						Arlington, TX 76019-0015
						<br>
						Email: <a href="mailto:idirlab@uta.edu">idirlab@uta.edu</a><p></p>
				</div>
				<div class="col-md-4">
					<div style="text-align:center; color:#1A1A1A; font-size: 180% !important;">
						<a target="_blank" href="https://www.facebook.com/idiruta/" class="fa fa-facebook"></a>
						<a target="_blank" href="https://twitter.com/Chengkai_Li" class="fa fa-twitter"></a>
						<a target="_blank" href="https://www.linkedin.com/in/chengkaili/" class="fa fa-linkedin"></a>
					</div>
					<div style="text-align:center; color:#1A1A1A; font-size: 100%;">
						© <a target="_blank" href="https://www.uta.edu/uta/">The University of Texas at Arlington</a> 2007-2023. <br>All rights reserved.
					</div>
				</div>
			</div>
		</div>
	</footer>

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
						<div>
							<p>
								<b>UT Arlington
									<br>
									Informed Consent Document
								</b>
							</p>
						</div>

						<b>CONSENT</b>
						<p class='justified_para'>
							By clicking “Accept’’ below, you confirm that you are 18 years of age or older and have read or had this document read to you. You have been informed about this study’s purpose, procedures, possible benefits and risks, and you have received a copy of this form. You have been given the opportunity to ask questions before you click “Accept’’, and you have been told that you can ask other questions at any time.
							<br>
							<br>
							You voluntarily agree to participate in this study. By clicking “Accept’’, you are not waiving any of your legal rights. Refusal to participate will involve no penalty or loss of benefits to which you are otherwise entitled. You may discontinue participation at any time without penalty or loss of benefits, to which you are otherwise entitled.
						</p>

						<b>PRINCIPAL INVESTIGATOR</b>
						<p class='justified_para'>
							Chengkai Li<br>
							Associate Professor<br>
							Department of Computer Science and Engineering<br>
							(817) 272-0162<br>
							<a href='mailto:cli@uta.edu'>cli@uta.edu</a><br>
						</p>
						<br>
						<b>STUDENT PERSONNEL</b>
						<p class='justified_para'>
							Zhengyuan Zhu<br>
							Ph.D. Candidate Student<br>
							Department of Computer Science and Engineering<br>
							(682) 259-5848<br>
							<a href='mailto:zhengyuan.zhu@mavs.uta.edu'>zhengyuan.zhu@mavs.uta.edu</a><br>
						</p>

						<b>IMPORTANT INFORMATION ABOUT THIS RESEARCH PROJECT</b>
						<p class='justified_para'>
							The research team above is conducting a research study that aims to collect human annotations that will be used to train a machine learning model for measuring the truthfulness stance that depicts whether a social media post believes a factual claim is true, false, or expresses neutral stance. Specifically, you will be provided with pairs of sentences to annotate. Each pair consists of a factual claim from PolitiFact and a tweet from Twitter. You will be asked to decide the truthfulness stance of a tweet toward a factual claim. 

							For that, you choose one of the five options: The tweet believes the factual claim is false; The tweet expresses a neutral or no stance toward the factual claim’s truthfulness; The tweet believes the factual claim is true; The tweet and the claim discuss different topicsThe tweet discusses unrelated topic to the claim; The tweet-claim pair is problematic; Skip this pair. The machine learning model can become useful in automating fact-checking. Imagine if a fact checker wants to understand public opinion and reaction toward a factual claim. When a Twitter account posts a tweet that is related to a factual claim, the machine learning model can help identify whether the tweet is believe the factual claim is true or not.

							You can choose to participate in this research study if you are over the age of 18 and fluent in English. Please note that you are not eligible for this study if you are not an English fluent speaker or are younger than 18.

							You might want to participate in this study if you would like to get a taste of how technology might help fact-checkers understand the spreading of factual claims and familiarize yourself with the fact-checking process. However, you might not want to participate in this study if you do not have the time or are not interested in the fact-checking process.

							This study has been reviewed and approved by an Institutional Review Board (IRB). An IRB is an ethics committee that reviews research with the goal of protecting the rights and welfare of human research subjects. Your most important right as a human subject is informed consent.  You should take your time to consider the information provided by this form and the research team and ask questions about anything you do not fully understand before making your decision about participating.
						</p>


						<b>TIME COMMITMENT</b>
						<p class='justified_para'>
							If you decide to participate in this study, you should know that you have the full freedom to decide the duration of your participation. From a couple of minutes to as many times as you want until the end of the study.
						</p>

						<b>PROCEDURES </b>
						<p class='justified_para'>
							If you decide to participate in this research study, this is the list of activities that we will ask you to perform as part of the research:
								1.	Go to https://idir.uta.edu/stance_annotation and click on the “Sign Up” button at the top right corner.
								2.	This will open this consent form that you need to accept if you want to participate in the study. If you decide that you do not wish to participate, click the “Decline” button, or close your web browser.
								3.	If you click the “Accept” button you will be presented with the account registration form to create an account by providing a username, an email address, and a password. Note that only the first three letters of your username will be visible to other participants in this study. We will use your email address only to communicate with you regarding this study. We will delete your username, email, and password once we have concluded the study.
								4.	We will send you a verification email. Once you click the verification link in the email, your account will be activated. If you cannot find it please check your junk/spam email box.
								5.	Using the registered account, you will log into our data annotation website to the instructions page. Read the instructions carefully and continue to the annotation page. On the top right corner of the webpage, there is a link to the instructions page if you need to reread the instructions at any time.
								6.	On the annotation page, you will be asked to read a pair of sentences (a factual claim and a tweet). Your task is to decide what is the truthfulness stance of the tweet toward the factual claim. For that, you should choose one of the six options: The tweet author believes the factual claim is false; The tweet author expresses a neutral or no stance toward the factual claim’s truthfulness; The tweet author believes the factual claim is true; The tweet and the claim discuss different topics; Skip this pair. The data annotation website will record your choices as well as the timestamp of each interaction with the website.
								7.	After submitting your choice, a new pair of sentences will appear for annotation. This process will continue until you decide to stop by clicking the “Log Out” button or by closing the web browser. 
								8.	If you want to modify the choice you made on a previous pair, click “Modify My Previous Responses”, where you can see all pairs on which you provided annotations, ordered by the timestamps. After your modification, your work quality score may become different.
								9.	You will be asked to complete 40 training annotations. Upon submitting your answer to a training example, the website will display a message indicating whether the answer is correct or not and provide a justification for the correct answer. After the first 50 annotations, the “Leaderboard” button will be activated. By clicking it, a pop-up window will show your work quality score, total points, and where you stand among other participants. Only the first three letters of usernames (instead of full names) will be displayed on the leaderboard. The leaderboard calculation is updated every 15-30 pairs reflecting your new work quality score and total points.

							To gauge the quality of your choices, we use several “gold standard” sentence pairs, for which we have “correct choices” selected by research experts on the subject. Your choices will be compared with the “gold standard” choices to estimate your work quality level. There is no visual distinction between a pair from the “gold standard” set or a pair outside that set.

							Below is a list of tips for improving your work quality score. You can find a copy of this list under each annotation pair.
							(1) Carefully examine each pair of factual claim and tweet.
							(2) Contextual information (such as the fact-check summary, claimant information, hyperlink title and content) may help you form answers.
							(3) Review the instructions to understand the examples. 
							(4) Don't guess. Skip the pairs that you are not sure about.
							(5) Modify previous responses if necessary.
							(6) You may be tempted to pick easy/short claims to work on by clicking "Skip this pair". Keep in mind that our work quality calculation formula has a component that accounts for the length/complexity of claims as well as how many pairs are skipped. We discourage excessive skipping. Nevertheless, if you are not confident about a question, it is still better to skip, because every single mistake will lower your work quality score.
							Whenever you make one mistake, our algorithm lowers your work quality score, which means you get less points for every pair you have annotated. It takes multiple correct answers to make up for every single mistake and get the work quality score back to the previous value. If your current work quality score is 0 or very low, it is because our algorithm detected many mistakes in your answers. The best thing to do is to review your answers and modify them if necessary. If your work quality score is 0, it might actually be negative internally. If you continue to answer new questions, it will take MANY questions before you can see positive and improving work quality score.
							If you have labeled 50-150 pairs, the work quality score based on the small sample may not reflect your true work quality. It will become more robust once you have labeled more pairs.
							We may email you about our optional data annotation training workshops in our Lab (ERB - Room 414) or online through MS Teams, which are available to all participants. The purpose of the workshops is to review the information provided on the instructions page, discuss any questions you might have, and annotate some pairs. We expect this activity to be helpful in improving annotation quality. Note that other participants may also be present in the data annotation workshops.
						</p>

						<b>POSSIBLE BENEFITS </b>
						<p class='justified_para'>
							You will get a taste of how technology might help fact-checkers understand the spreading of factual claims. AI technologies will be used for processing the tweet and claim text and extracting information from the processed text. We will also use AI technology to model the truthfulness stance detection based on the annotations from subjects.  Furthermore, the research outcome might lead to advancement in data-driven fact-checking, which tackles an important sciential challenge.
						</p>

						<b>POSSIBLE RISKS/DISCOMFORTS </b>
						<p class='justified_para'>
							If the data is lost or stolen, you may be exposed as a research subject in the study. In addition, there can be a risk of undue influence because a professor figure is recruiting a student. Since there is an authority difference between these two parties, it is possible that you might feel compelled to participate in the research against your best interests. You may worry about your grades, future research opportunities, etc. if they decline.

							There may be psychological risks because you may be affected by the factual claims’ and tweets’ content. A sizable proportion of factual claims is misinformation. You may potentially misunderstand and consider misinformation as fact. Furthermore, although highly rare given the way we collected tweets for the study, it is possible for a tweet to contain filthy language, profanity, and hate speech. You may feel anger or embarrassment during the annotation. 

							This research study is not expected to pose any additional risks beyond what you would normally experience in your regular everyday life. However, if you experience discomfort, please inform the research team, and quit the study without any consequence to you.

							To minimize the risk to privacy or confidentiality, we are storing the data on UTA servers and will limit its access to the research team only. To minimize the risk of undue influence we confirm your decision to participate or not participate will not influence your grades or future research opportunities in any way. To minimize the risk of psychological influence, we have removed tweets that contain images, GIFs, or videos.
						</p>

						<b>Compensation</b>
						<p class='justified_para'>
							You will not be compensated for your participation.
						</p>

						<b>ALTERNATIVE OPTIONS</b>
						<p class='justified_para'>
							There are no alternative procedures offered for this study. However, you can elect not to participate in the study or quit at any time at no consequence.
						</p>

						<b>CONFIDENTIALITY</b>
						<p class='justified_para'>
						The research team is committed to protecting your rights and privacy as a research participant.  All paper and electronic data collected from this study will be stored in a secure location on the UTA campus and/or a secure UTA server for at least three (3) years after the end of this research.

						The results of this study may be published and/or presented without naming you as a participant.  The data collected about you for this study may be used for future research studies that are not described in this consent form. If that occurs, an IRB would first evaluate the use of any information that is identifiable to you, and confidentiality protection would be maintained.

						While absolute confidentiality cannot be guaranteed, the research team will make every effort to protect the confidentiality of your records as described here and to the extent permitted by law.  In addition to the research team, the following entities may have access to your records, but only on a need-to-know basis: the U.S. Department of Health and Human Services and the FDA (federal regulating agencies), the reviewing IRB, and sponsors of the study.
						</p>

						<b>CONTACT FOR QUESTIONS</b>
						<p class='justified_para'>
							Questions about this research study may be directed to Dr. Chengkai Li at (817) 272-0162 or cli@uta.edu and Zhengyuan Zhu at zhengyuan.zhu@mavs.uta.edu. Any questions you may have about your rights as a research subject or complaints about the research may be directed to the Office of Research Administration; Regulatory Services at 817-272-3723 or regulatoryservices@uta.edu.  
						</p>

						<b>CONSENT</b>
						<p class='justified_para'>
							By clicking “Accept’’ below, you confirm that you are 18 years of age or older and have read or had this document read to you. You have been informed about this study’s purpose, procedures, possible benefits and risks, and you have received a copy of this form. You have been given the opportunity to ask questions before you click “Accept’’, and you have been told that you can ask other questions at any time.
							<br>
							<br>
							You voluntarily agree to participate in this study. By clicking “Accept’’, you are not waiving any of your legal rights. Refusal to participate will involve no penalty or loss of benefits to which you are otherwise entitled. You may discontinue participation at any time without penalty or loss of benefits, to which you are otherwise entitled.
						</p>

						<button type="button" class="btn btn-primary button_consent_accept pull-right">Accept</button>
						<br>
						<br>
					</div>
				</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="bootstrap-3.3.2-dist/js/bootstrap.min.js"></script>
	<script src="iCheck/icheck.js"></script>
	<script src="js/index.js"></script>
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