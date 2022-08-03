var universities = [
		  "Duke University",
		  "University of Texas at Arlington",
		  "Other"
	];
	
var text_response = {'-1': 'Tweet doesn\'t believe the truthfulness/veracity of factual claim', 
					 '0': 'Tweet holds neutral attitude or cannot confirm the truthfulness of the factual claim', 
					 '1': 'Tweet believes the truthfulness/veracity of factual claim', 
					 '-2': 'Skip this sentence',
					 '2': 'Tweet discusses unrelated topic to factual claim',
					 '3': 'Tweet-claim pair is problematic(tweet is not in Engllish or not understandable, claim is noun phrase/question)'
					};
var logged_out = 1;
var is_training = 1;
var training_index = -1;

window.onbeforeunload = function (e) {
    e = e || window.event;
    // For IE and Firefox prior to version 4
    if (e && !logged_out) {
        e.returnValue = 'Refreshing/Leaving this webpage will log you out from the survey.';
    }
    // For Safari
    if(!logged_out)return 'Refreshing/Leaving this webpage will log you out from the survey.';
};

$( document ).ajaxStart(function() {
	//$('#modal_ajax_loader').modal('show');	
});

$( document ).ajaxStop(function() {
	//$('#modal_ajax_loader').modal('hide');
});

$(document).ready(function(){				
	$('#input_new_user_email').tooltip({'trigger':'focus', 'title': 'Enter a valid email address.'});
	$('#input_new_user_username').tooltip({'trigger':'focus', 'title': 'Allowed characters are 0-9, a-z, A-Z, PERIOD(.) and UNDERSCORE(_). Must be smaller than 16 characters.'});
	$('#input_new_user_password').tooltip({'trigger':'focus', 'title': 'Allowed characters are 0-9, a-z, A-Z, PERIOD(.) and UNDERSCORE(_). Must be smaller than 16 characters.'});
	$('#input_new_user_confirm_password').tooltip({'trigger':'focus', 'title': 'Both passwords should match.'});
	
	$(".reference-options li a").click(function()
	{
		$("#button_reference").text($(this).text());
  	});
  	
  	$(".feedback-options li a").click(function()
	{
		$("#button_feedback_type").text($(this).text());
  	});
  	
	change_navbar(0);
	$('#button_sign_in').click(function(){
		check_sign_in_information();
	});
	
	$(document).keypress(function(e) {
		if(e.which == 13) {
		    $('#button_sign_in').click()
		}
	});
	
	$('#button_new_user_submit').click(function(){
	   	/*if(!check_full_name())
	   	{
	   		alert('FN');
	   		return;
	   	}*/
	   	if(!check_email())
	   	{
	   		show_rules(1);
	   		return;
	   	}
	   	if(!check_username())
	   	{
	   		show_rules(1);
	   		return;
	   	}
	   	if(!check_password())
	   	{
	   		show_rules(1);
	   		return;
	   	}
	   	if(!check_confirm_password())
	   	{
	   		show_rules(1);
	   		return;
	   	}
	   	check_username_exist();
	   	
	});

	$('#button_log_out').click(function(){
		$.ajax({
			url: "clear_session.php",
			method: "POST",
			success: function(data)
			{
				logged_out = 1;
				$(location).attr('href','index.php');
			}
		});		
	})
		
	$('#button_survey').click(function(){
		init_survey();
	})
	
	$('#button_forgot_password').click(function(){
		forgot_password();
	})
	
	$('#button_feedback_send').click(function(){
		send_feedback();
	})
		
});

function forgot_password()
{
	var container = $('.jumbotron .container').first();
	container.empty();
	container.append("<br><div><br><div class='row'><div class='col-md-3'><label for='new_user_email'>Enter Email Address</label></div> <div class='col-md-5'><input type='email' class='form-control' id='input_forgot_email' placeholder='Enter Email Address'></div><div class='col-md-2'><button id='button_forgot_email' class='btn btn-primary' role='button'>Submit</button></div></div></div>");
	
	$('#button_forgot_email').click(function(){
		$.ajax({
			url: "forgot_password.php",
			method: "POST",
			data: { email : '"'+$('#input_forgot_email').val()+'"'},
			dataType: "text",
			success: function(data)
			{
				alert(data);
			}
		});
	})
}

function reset_password(username)
{
	var valid_characters = /^[0-9a-zA-Z]{1,15}$/;
	if($('#input_reset_password').val().match(valid_characters))
	{
		if($('#input_reset_password').val() == $('#input_reset_password_confirm').val())
		{
			$.ajax({
				url: "reset_password_confirm.php",
				method: "POST",
				data: { username : username,
						password : '"'+$('#input_reset_password_confirm').val()+'"'},
				dataType: "text",
				success: function(data)
				{
					alert(data);
					window.location.href = 'index.php';
				}
			});
		}
		else show_rules(2);
	}
	else show_rules(2);
}

function endsWith(str, suffix) {
    return str.indexOf(suffix, str.length - suffix.length) !== -1;
}

function check_email()
{
	var valid_characters = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return valid_characters.test($('#input_new_user_email').val());
}

function check_full_name()
{
	var valid_characters = /^[0-9a-zA-Z \.,]+$/;
	if($('#input_new_user_full_name').val().match(valid_characters))
	{
		return true;
	}
	else return false;
}

function check_username()
{
	var valid_characters = /^[0-9a-zA-Z\._]{1,15}$/;
	if($('#input_new_user_username').val().match(valid_characters))
	{
		return true;
	}
	else return false;
}

function check_password()
{
	var valid_characters = /^[0-9a-zA-Z\._]{1,15}$/;
	if($('#input_new_user_password').val().match(valid_characters))
	{
		return true;
	}
	else return false;
}

function check_confirm_password()
{
	return $('#input_new_user_password').val() == $('#input_new_user_confirm_password').val();
}

function show_rules(number)
{
    if(number == 1)alert("Email Address: A valid email address.\n\nUsername: Allowed characters are 0-9, a-z, A-Z, PERIOD(.) and UNDERSCORE(_). Must be smaller than 16 characters.\n\nPassword: Allowed characters are 0-9, a-z, A-Z, PERIOD(.) and UNDERSCORE(_). Must be smaller than 16 characters. Both passwords should exactly match.");
    else if(number == 2)alert("Password: Allowed characters are 0-9, a-z, A-Z, PERIOD(.) and UNDERSCORE(_). Must be smaller than 16 characters. Both passwords should match exactly.");
}

function check_username_exist()
{
	$.ajax({
		url: "check_username_exist.php",
		method: "POST",
		data: { username : '"'+$('#input_new_user_username').val()+'"', 
				email    : '"'+$('#input_new_user_email').val()+'"',
			},
		dataType: "text",
		success: function(data)
		{
			console.log('check username exist:', data);
			if(data.localeCompare("0") == 0)
			{
				insert_new_user();
			}
			else
			{
				alert('Username or Email Address already exist. Please select another one.');				
			}
		}
	});
}

function insert_new_user()
{
	$.ajax({
		url: "insert_new_user.php",
		method: "POST",
		data: { username : '"'+$('#input_new_user_username').val()+'"',
				password : '"'+$('#input_new_user_password').val()+'"' ,
				email : '"'+$('#input_new_user_email').val()+'"',
			},
		dataType: "text",
		success: function(data)
		{
			console.log("insert_new_user:", data);
			$('#modal_new_user').modal('hide');
			alert(data);
		}
	});
}

function check_sign_in_information()
{
	$.ajax({
		url: "check_sign_in_information.php",
		method: "POST",
		data: { username : '"'+$('#input_username').val()+'"',
				password : '"'+$('#input_password').val()+'"',
			},
		dataType: "text",
		success: function(data)
		{
			console.log("check_sign_in_information:", data)
			if(data.localeCompare("-1") == 0)
			{
				alert('Invalid Username or Password. Please Try Again.');
			}
			else if(data.localeCompare("-2") == 0)
			{				
				alert('Account not verified. Please check your email and verify the account.');
			}
			else
			{
				console.log("check_sign_in_information passed")
				logged_out = 0;
				init_survey();
				var username = $('#span_username').text();
				ga('set', '&uid', username); // Set the user ID using signed-in user_id.
			}
		}
	});
}

function get_answer_count()
{
	$.ajax({
		url: "get_answer_count.php",
		method: "POST",
		dataType: "text",
		success: function(data)
		{
			console.log("answer_count:", data);
			data = data.split('^');
			$('#span_badge').text(" "+data[0]+" ");
			$('#span_username').text(""+data[1].replace('"','').replace('"',''+" labeled"));
		}
	});
}

function get_leaderboard()
{
	$.ajax({
		url: "get_leaderboard.php",
		method: "POST",
		dataType: "text",
		success: function(data)
		{
			data = jQuery.parseJSON(data);
			console.log("get_leaderboard:", data);
			$('#modal_leaderboard').modal('show');
			$('#selectable_leaderboard').empty();
			$('#selectable_leaderboard').append('<li class="list-group-item"><div class="row"><div class="col-md-3">'+'<b>USERNAME</b>'+'</div><div class="col-md-3">'+'<b>ANSWERED</b>'+'</div><div class="col-md-3">'+'<b>PAY RATE (&cent;)</b>'+'</div><div class="col-md-3">'+'<b>PAYMENT ($)</b>'+'</div></div></li>');
			
			user = data[0].user;
			
			for(i = 0; i < data.length; i++)
			{
				if(i != user)
				{
					if(data[i].prize.localeCompare("") != 0)
					{
						$('#selectable_leaderboard').append('<li class="list-group-item winner"><div class="row"><div class="col-md-3"><b>'+data[i].USERNAME+'<br>'+data[i].prize+'</div><div class="col-md-3">'+data[i].ANSWERED+'</div><div class="col-md-3">'+data[i].QUALITY+'</div><div class="col-md-3">'+data[i].PAYMENT+'</b></div></div></li>');		
					}
					else $('#selectable_leaderboard').append('<li class="list-group-item"><div class="row"><div class="col-md-3">'+data[i].USERNAME+'</div><div class="col-md-3">'+data[i].ANSWERED+'</div><div class="col-md-3">'+data[i].QUALITY+'</div><div class="col-md-3">'+data[i].PAYMENT+'</div></div></li>');
				}
				else
				{
					if(data[i].prize.localeCompare("") != 0)
					{
						$('#selectable_leaderboard').append('<li class="list-group-item winner"><div class="row"><div class="col-md-3"><b>'+data[i].USERNAME+'<br>'+data[i].prize+'</div><div class="col-md-3">'+data[i].ANSWERED+'</div><div class="col-md-3">'+data[i].QUALITY+'</div><div class="col-md-3">'+data[i].PAYMENT+'</b></div></div></li>');		
					}
					
					else $('#selectable_leaderboard').append('<li class="list-group-item user"><div class="row"><div class="col-md-3"><b>'+data[i].USERNAME+'</div><div class="col-md-3">'+data[i].ANSWERED+'</div><div class="col-md-3">'+data[i].QUALITY+'</div><div class="col-md-3">'+data[i].PAYMENT+'</b></div></div></li>');
				}				
			}
		}
	});
}

function get_consent()
{
	$.ajax({
		url: "get_consent.php",
		method: "POST",
		dataType: "text",
		success: function(data)
		{
			if(data.localeCompare('0') == 0)
			{
				$('#modal_consent').modal({
					backdrop: 'static',
					position: 'absolute',
					keyboard: false
				});
				$('#modal_consent').modal('show');
				$('.button_consent_accept').click(function(){					
					set_consent();
				});
			}
		}
	});
}

function set_consent()
{
	$.ajax({
		url: "set_consent.php",
		method: "POST",
		dataType: "text",
		success: function(data)
		{
			$('#modal_consent').modal('hide');
		}
	});
}

function init_survey()
{
	change_navbar(1);	
	get_answer_count();
	get_consent();
	load_survey_instructions();
}

function change_navbar(login_status)
{
	if(login_status) //user just logged in.
	{
		$('#button_navbar').attr('data-target','#navbar_logout');
		$('#navbar_signin_signup').attr('style','display:none !important');
		$('#navbar_logout').show();
		$('#button_leaderboard').click(function(){
			get_leaderboard();
		});
		$('#button_load_survey_instructions').click(function(event){
			event.stopPropagation();
			load_survey_instructions();
		});
	}
	else //user just logged out
	{
		$('#button_navbar').attr('data-target','#navbar_signin_signup');		
		$('#navbar_logout').attr('style','display:none !important');
		$('#navbar_signin_signup').show();
	}
}

function load_survey_instructions()
{		
	$('#button_leaderboard').prop('disabled', true); //commented momentarily for payment display purpose. March 30, 2016.
	var container = $('.jumbotron .container').first();
	container.empty();
	container.append('<p class="justified_para top_margin"><h2>Annotate Instructions:</h2>You will be shown a tweet-claim pair, \
						all you need to do is to <b>decide whether the tweet author believes the veracity of factual claim.</b> \
						Your five options will always be:<br>\
						<br><img src="image/1.png" class="img-responsive center-block with-border" alt="Figure 1"><span class="center-block">Figure 1</span> \
						<h3><b>Option meanings and examples:</b></h3>\
						\
						- <b>Tweet author believes the veracity of factual claim</b>: Based on the pair\'s text and your experience, you can deduce the tweet author believes the veracity of the factual claim.\
						<br>&emsp;<b>Examples of positive tweet-claim pairs:</b> \
						<br><b>&emsp;1. The tweet is capitalizing various words and adding extra ! characters so we can deduce that the tweet author is excited about the news. They also refer to the arrest as "hitting the motherload" which is slang for finding something that makes you happy.</b> \
						<br>&emsp;&emsp;Factual claim: The largest bust in U.S. history’ 412 Muslims arrested from Michigan! \
						<br>&emsp;&emsp;Tweet: 412 Michigan Muslims Arrested ‘LARGEST BUST IN U.S. HISTORY’!! They Hit The MOTHERLOAD! https://freedomdaily.com/?p=360808 via @Freedom_Daily \
						<br><b>&emsp;2. The tweet author is clearly calling for supportive action for the student who was suspended in the claim. This indicates the author believes the situation is real and warrants the attention of "a patriot attorney".</b> \
						<br>&emsp;&emsp;Factual claim: "Ohio student suspended for staying in class during National Walkout Day" \
						<br>&emsp;&emsp;Tweet: Patriot attorney needed.  Reach out to this family.  Ohio student suspended for staying in class during National Walkout Day https://truepundit.com/ohio-student-suspended-staying-class-national-walkout-day/… \
						<br><b>&emsp;3. The tweet author wants to bring the police findings to the readers attention by asking a rhetorical question before re-posting the claim, because they are trying to make others aware of the claim we can deduce the tweet author supports the claim\'s veracity.</b> \
						<br>&emsp;&emsp;Factual claim: Police find 19 white female bodies in freezers with ‘Black Lives Matter’ carved into skin.\
						<br>&emsp;&emsp;Tweet: Did you know: #Police Find 19 White Female Bodies In Freezers With “Black Lives Matter” Carved Into Skin. \
						<br><b>&emsp;4. The core statement of the claim is that Kamala Harris has been caught in a lie, the tweet author calls Kamala Harris a "moron" for the act of lying because there was no chance she could get away with it due to the fact that "we can look up your lies". This clearly indicates that the tweet author supports the veracity of the claim.</b> \
						<br>&emsp;&emsp;Factual claim: KAMALA HARRIS Says Schools in Berkeley Weren’t Integrated When She Was a Kid — But Yearbook Pictures Prove She’s Lying.\
						<br>&emsp;&emsp;Tweet: It is like these morons don\'t know how the Internet works.  We can look up your lies!  Unreal...  KAMALA HARRIS Says Schools in Berkeley Weren\'t Integrated When She Was a Kid -- But Yearbook Pictures Prove She\'s Lying. \
						\
						<br><br>- <b>Tweet author doesn\'t believe the veracity of factual claim</b>: The tweet author directly denies the veracity of the factual claim or we can deduce tweet author doesn\'t believe the factual claim by tweet\'s content and tone. \
						<br>&emsp;<b>Examples of negative tweet-claim pairs:</b> \
						<br><b>&emsp;1. The tweet author thinks the factual claim worths 4 more Pinocchios and Pinocchios is a misleading rating scale used by Washington Post, a claim contains more misleading claims will receive more Pinocchios. The tweet author also shows fact that the Johnson Amendment is still on the book.</b> \
						<br>&emsp;&emsp;Factual claim: We got rid of the Johnson Amendment. \
						<br>&emsp;&emsp;Tweet: That\'s 4 more Pinocchios for @realDonaldTrump as it pertains to his shifting claim that ‘we got rid’ of the Johnson Amendment. Fact: The Johnson Amendment is still a thing. "It\'s still on the books." \
						<br><b>&emsp;2. The tweet author shows that fact that Scott only won by around 1/800 which is far from won by a lot.</b> \
						<br>&emsp;&emsp;Factual claim: Rick Scott won and he won by a lot. \
						<br>&emsp;&emsp;Tweet: @realDonaldTrump on Fox News: “Rick Scott won and he won by a lot." Reality: Scott won by 10,033 votes. Out of more than 8 million cast. #AlternativeFacts \
						<br><b>&emsp;3. The tweet author uses sacarsm and jokes about how ridiculous the facual claim is. We can deduce that the tweet author doesn\'t believe ISIS can be distributed in 32 contries.</b> \
						<br>&emsp;&emsp;Factual claim: ISIS is in 32 countries. \
						<br>&emsp;&emsp;Tweet: "ISIS is in 32 countries."  I think he meant IKEA. \
						<br><b>&emsp;4. The tweet author points out Jennifer Lawrence blamed Trump for 9/11 was a false claim and the meme is fake, so we can deduce the tweet author doesn\'t believe Jennifer Lawrence ever links 9/11 to Trump\'s election.</b> \
						<br>&emsp;&emsp;Factual claim: Jennifer Lawrence links 9/11 to Trump\'s election.\
						<br>&emsp;&emsp;Tweet: A New Conspiracy Theory Falsely Claims Jennifer Lawrence Blamed Trump for 9/11. A meme with over 11,000 shares quotes Lawrence as blaming Trump for September 11 because he stole the election, but it\'s totally bogus. \
						\
						<br><br>- <b>Tweet author cannot confirm the veracity of the factual claim</b>: The tweet author holds neutral attitude and we cannot deduce whether tweet author believes the veracity of the factual claim.\
						<br>&emsp;<b>Examples of neutral tweet-claim pairs:</b> \
						<br><b>&emsp;1. Tweet almost only repeats the content of factual claim and doesn’t add more contextual information.</b> \
						<br>&emsp;&emsp;Factual claim: the media distorted what happened with a baby at his rally. \
						<br>&emsp;&emsp;Tweet: DONALD TRUMP Says the MEDIA (reporters) distorted what happened with a baby at his rally.  @cspanwj #tcot \
						<br><b>&emsp;2. Tweet almost only repeats the content of factual claim and doesn’t add more contextual information.</b> \
						<br>&emsp;&emsp;Factual claim: "Google search spike suggests many people don’t know why they voted for Brexit." \
						<br>&emsp;&emsp;Tweet: Google search spike suggests people don\'t know why they Brexited #brexit http://theverge.com/2016/6/24/12022880/google-search-spike-brexit-why-leave-eu?utm_campaign=theverge&utm_content=article&utm_medium=social&utm_source=twitter…\
						\
						<br><br>- <b>Tweet discusses unrelated topic with regard to factual claim</b>\
						<br><b>Examples of unrelated tweet-claim pairs:</b> \
						<br><b>&emsp;1. The factual claim is talking about low chances of being killed by refugee\'sterrorist act while the tweet focuses on the fact that two people was killed and ruled as suicide.</b> \
						<br>&emsp;&emsp;Factual claim: The chances of being killed by a refugee committing a terrorist act is 1 in 3.6 billion.\
						<br>&emsp;&emsp;Tweet: @ItizBiz and @TB_Timesshe was decapitated and ruled a suicide!!??? \
						<br><b>&emsp;2. Even the tweet talks about the Paul Ryan and gun laws, it doesn’t focus on whether Paul Ryan has blocked gun laws or not. </b> \
						<br>&emsp;&emsp;Factual claim: Paul Ryan has blocked all action to strengthen our gun laws. \
						<br>&emsp;&emsp;Tweet: Wisconsin students are marching 50 miles to Paul Ryan&apos;s hometown for action on gun laws… https://goo.gl/fb/6SPZtQ http://bitly.com/2sjBBbW \
						\
						<br><br>- <b>Tweet-claim pair is problematic: tweet is not in English or not understandable, claim is noun phrase or question.</b> \
						<br><b>Examples of problematic tweet-claim pairs:</b> \
						<br><b>&emsp;1. Tweet is apprently not in English.</b> \
						<br>&emsp;&emsp;Factual claim: Winston Churchill said, "The fascists of the future will call themselves anti-fascists."\
						<br>&emsp;&emsp;Tweet: Links mag alles, denken ze, nee zelfs \'weten\' ze.... #verpletterendeverantwoordelijkheid @jdecleulaer @vanranstmarc \
						<br><b>&emsp;2. Factual claim is a question.</b> \
						<br>&emsp;&emsp;Factual claim: Is the Red Cross \'Not Helping California Wildfire Victims\' \
						<br>&emsp;&emsp;Tweet: Don’t forget tomorrow is HAT DAY! Your $2 donation to the Red Cross will help California wildfire victims! \
						<br><b>&emsp;3. Factual claim is a noun phrase.</b> \
						<br>&emsp;&emsp;Factual claim: Fake News about the Florida School Shooting. \
						<br>&emsp;&emsp;Tweet: Reading: YouTube\'s crackdown on fake news: Promoting bonkers Florida school shooting conspiracies \
						\
						<br><br>If you are unsure about the answer you can select the “skip sentence” button.<br><br>There will be 40 training questions at the beginning. \
						We will show the correct answer after you submit your response. \
						The actual data collection will start after you are done with the initial 40 training questions. \
						<br><br>Please feel free to use the “Feedback” button (to the right of the browser window) to inform us your suggestions and/or report errors. \
						You can also contact us at <a href="mailto:idirlabuta@gmail.com">idirlabuta@gmail.com</a>. Thanks!</p>');
	
	container.append('<div class="center-align"><input id="input_agree" type="checkbox"><label><b>I have carefully read and understood all the instructions.</b></label><br><br><button id="button_start_survey" type="button" class="btn btn-primary btn-lg" disabled>Start</button><br><br> </div>');
	
	$('input').iCheck({
		checkboxClass: 'icheckbox_square-blue',
		radioClass: 'iradio_square-blue',
		uncheckedCheckboxClass: 'hover'
	});	

	$('#button_start_survey').click(function(){
		start_training();
	});
	
	$('#input_agree').on('ifChecked', function(event){
		$('#button_start_survey').prop('disabled', false);
	});
	
	$('#input_agree').on('ifUnchecked', function(event){
		$('#button_start_survey').prop('disabled', true);
	});
}

function start_training()
{
	$.ajax({
		url: "get_training_index.php",
		method: "POST",
		dataType: "text",
		success: function(data)
		{
			console.log("get_training_index", data);
			if(data == 0)
			{
				is_training = 0;
			}
			else
			{
				training_index = data.split('^')[1];
				data = data.split('^')[0];
			}
			get_sentence(data);
		}
	});
}

function show_sentence(sentence_id, sentence, REGION, ANSWERED_message, QUALITY_message, PAYMENT_message, RANK_message, total_message)
{
	var container = $('.jumbotron .container').first();
	container.empty();
	container.append('<br><div id="top_well" class="well message highlight"><span id="span_REGION_status"></span></div><div class="panel panel-primary">  \
						<div class="panel-body"><div id="div_sentence" class="well">'+sentence+'</div> \
						<button id="button_context" type="button" class="btn btn-xs btn-primary">More Context</button><br><br> \
						<div class="panel-title">What is the truthfulness stance of tweet towards factual claim?</div><br>  \
						<ul class="list-group"> \
						<li class="list-group-item"> \
							<input type="radio" name="iCheck" id="1"><label>'+text_response['1']+'</label> \
						</li> \
						<li class="list-group-item"> \
							<input type="radio" name="iCheck" id="0"><label>'+text_response['0']+'</label> \
						</li> \
						<li class="list-group-item"> \
							<input type="radio" name="iCheck" id="-1"><label>'+text_response['-1']+'</label> \
						</li> \
						<li class="list-group-item"> \
							<input type="radio" name="iCheck" id="2"><label>'+text_response['2']+'</label> \
						</li> \
						<li class="list-group-item"> \
							<input type="radio" name="iCheck" id="3"><label>'+text_response['3']+'</label> \
						</li> \
						</ul></div><div class="panel-footer"><button id="button_submit_answer" type="button" class="btn btn-primary">Submit</button>&nbsp&nbsp \
						<button id="button_skip" type="button" class="btn btn-info">'+text_response['-2']+'</button> \
						<button id="button_previous_answers" type="button" class="btn btn-primary pull-right">Modify My Previous Responses</button></div></div> \
						<div class="well tips justified_para"> <span id="span_tips">Tips for improving your payrate: (1) Carefully examine each sentence. (2) Read instructions carefully (click the button above) to understand examples belonging to each category. (3) Don\'t guess. Skip sentences that you are not sure about. (4) Contextual sentences (by clicking “More Context”) may help you form answers.) (5) Modify previous responses if necessary. (6) You may be tempted to pick easy/short questions to work on by "skipping". However, your payrate will get lower, since our payrate calculation formula has a component that accounts for the length/complexity of sentences. Basically, we discourage excessive skipping. Nevertheless, if you are not confident on a question, it is still better to skip, because every single mistake will lower your payrate. (See (3) above related to "Don\'t guess".)<br><br>Whenever you make 1 mistake, our algorithm lowers your pay rate, which means you get paid less for every sentence you have labeled. It takes multiple correct answers to make up for every single mistake and get the pay rate back to the previous value. If you current pay rate is 0 or very low, it is because our algorithm detected many mistakes in your answers so far. The best thing to do is to go back and review all your answers so far and modify them if necessary. If your payrate is 0, it might actually be negative internally. If you continue to answer new questions, it will take MANY questions before you can see positive and improving payrate.<br><br>If you have labeled 50-150 sentences, the quality measure based on the small number of sentences may not be statistically significant. Hence, your current pay rate may not reflect your true work quality. It will become more accurate once you have labeled more sentences.</span></div>');

	$('input').iCheck({
		checkboxClass: 'icheckbox_square-blue',
		radioClass: 'iradio_square-blue',
		increaseArea: '20%' // optional		
	});	
	
	$('#button_submit_answer').on('click', function(){
		post_response(sentence_id, context_seen);		
	})
	
	$('#button_skip').on('click', function(){
		skip_sentence(sentence_id, context_seen);		
	})
	
	$('#button_previous_answers').on('click', function(){
		get_previous_answers(sentence_id);
	})
	// TODO: remove context button
	var context_seen = 0;
	$('#button_context').click(function(){
		if($('#button_context').text().localeCompare("More Context") == 0)
		{
			context = get_context(sentence, sentence_id);
			$('#button_context').text('Less Context');
			context_seen = 1;
		}
		else
		{
			$('#div_sentence').html(sentence);
			$('#button_context').text('More Context');
		}
		
	})
	console.log('answered_message:', ANSWERED_message);
	//update_region_status(REGION);
	update_payrate_message(ANSWERED_message, QUALITY_message, PAYMENT_message, RANK_message, total_message);	
	
	$('body').scrollTop(0);
	
	if(is_training == 1)
	{
		$('#button_previous_answers').hide();
		$('#button_skip').hide();
	}
	else
	{
		$('#button_previous_answers').show();
		$('#button_skip').show();
	}
}

function update_region_status(REGION)
{
	switch(parseInt(REGION)) {
	case 1:
		//console.log(REGION);
		$('#span_REGION_status').css('background-color',"#66FF66");
		$('#span_REGION_status').text("You are doing an amazing job. Please keep up the good work.");
		break;
	case 2:
		//console.log(REGION);
		//		$('#span_REGION_status').text("You are doing amazing. Please keep up the good work!");
		break;
	case 3:
		//console.log(REGION);
		$('#span_REGION_status').css('background-color',"#FFFFCC");
		$('#span_REGION_status').text("Our tool detected that the quality of your responses is not high. If the quality falls below a certain threshold, we won’t be able to consider your response for prize drawing. You can modify your previous responses, in order to improve the quality. Please spend more time on each sentence and read it more carefully. If you unsure about the answer, use the “skip” button.");
		break;
	case 4:
		//console.log(REGION);
		$('#span_REGION_status').css('background-color',"#FF6666");
		$('#span_REGION_status').text("Our tool detected that the quality of your responses is poor. If it cannot be improved, we won’t be able to consider your response for prize drawing. In order to improve the quality, please modify your previous responses. Please spend more time on each sentence and read it more carefully. If you unsure about the answer, use the “skip” button.");
		break;	
	default:
		//console.log(REGION);
		$('#span_REGION_status').text("");
	}
}

function update_payrate_message(ANSWERED_message, QUALITY_message, PAYMENT_message, RANK_message, total_message)
{
	if(is_training == 1)
	{
		var remaining = 40 - training_index;
		$('#span_REGION_status').html("Training Question: "+training_index+" (remaining "+remaining+" )<br>This is the training phase. Correct answer and explanation will be shown in this panel. The system only starts to calculate the leaderboard and payrate after the training phase is over.");
		$('#top_well').addClass("highlight");
		return;
	}
	if(ANSWERED_message == null)
	{
		$('#button_leaderboard').prop('disabled', true);
		$('#span_REGION_status').html("Click 'Leaderboard' to see your pay rate. The leaderboard is enabled after your initial 50 sentences. After that, this message and the leaderboard are updated every 15-30 sentences. </b>See tips for improving your pay rate at the bottom of this page.</b>");
	}
	else if(ANSWERED_message >= 50)
	{
		$('#button_leaderboard').prop('disabled', false);
		$('#span_REGION_status').html("Your estimated total payment is $"+PAYMENT_message.toString()+". You are paid approximately "+QUALITY_message.toString()+"&cent; per sentence on average. Your total payment is ranked "+RANK_message.toString()+" out of "+total_message.toString()+" participants. Click 'Leaderboard' to see details. This message and the leaderboard are updated every 15-30 sentences. <b>See tips for improving your pay rate at the bottom of this page.</b>");
	}	
	else
	{
		$('#button_leaderboard').prop('disabled', true);
		$('#span_REGION_status').html("Click 'Leaderboard' to see your pay rate. The leaderboard is enabled after your initial 50 sentences. After that, this message and the leaderboard are updated every 15-30 sentences. </b>See tips for improving your pay rate at the bottom of this page.</b>");
	}
}

function get_sentence(sentence_id)
{
	get_answer_count();
	$('input').iCheck('enable');
	sentence_id = typeof sentence_id !== 'undefined' ? sentence_id : 0;
	$.ajax({
		url: "get_sentence.php",
		method: "POST",
		dataType: "text",
		data: {sentence_id: sentence_id},
		success: function(data)
		{
			console.log("get_sentence:", data);
			if(data.localeCompare("-1") == 0)
			{
				alert('No more sentences available for you. Thanks for your participation. We may notify you in case there is a third phase of the data collection.');
				get_previous_answers(0);
				return;
			}
			data = jQuery.parseJSON(data);
			console.log(data.claim, data.tweet)
			show_sentence(data.id, "<b>Factual claim:</b> "+data.claim+"<br><br><b>Tweet</b>: "+data.tweet, data.REGION, data.ANSWERED_message, data.QUALITY_message, data.PAYMENT_message, data.RANK_message, data.total_message);
		}
	});
}

function skip_sentence(sentence_id, context_seen)
{
	$.ajax({
		url: "set_response.php",
		method: "POST",
		data: { sentence_id: sentence_id, response: '-2', context_seen: context_seen},
		dataType: "text",
		success: function(data)
		{
			get_sentence(0);
		}
	});
}

function post_response(sentence_id, context_seen)
{
	if($('input[type=radio]:checked').size() == 0)
	{
		alert('Please select one option.');
		return;
	}
	$('input').iCheck('disable');
	$("#button_submit_answer").prop("disabled",true);
	$.ajax({
		url: "set_response.php",
		method: "POST",
		data: { sentence_id: sentence_id, response: $('input[type=radio]:checked')[0].id, context_seen: context_seen},
		dataType: "text",
		success: function(data)
		{
			console.log("set_response:", data);
			if(is_training == 1)
			{
				show_training_message(sentence_id);
			}
			else get_sentence(0);
		}
	});
}

function show_training_message(sentence_id)
{
	$.ajax({
		url: "set_training_index.php",
		method: "POST",
		data: { sentence_id: sentence_id, response: $('input[type=radio]:checked')[0].id},
		dataType: "text",
		success: function(data)
		{
			console.log('show_training_message:', data);
			data = data.split('^');
			$('#top_well').removeClass("highlight");
			if(data[1].localeCompare("Correct!") == 0) {
				$('#top_well').addClass("correct");
			}
			else {
				$('#top_well').addClass("wrong");
			}
			$('#span_REGION_status').html(data[1]+"<br>"+data[0]+"<br>"+'<button id="button_next" type="button" class="btn btn-primary pull-left">'+"Next"+'</button>&nbsp&nbsp');
			
			$('#button_next').on('click', function(){
				if(data[2].localeCompare("40") == 0)
				{
					$('#top_well').removeClass("correct wrong");
					$('#span_REGION_status').html("You have completed the training phase. You have made "+data[3]+" mistakes. In actual data collection mode, you would have earned $"+data[4]+". The actual data collection phase will start now."+"<br>"+'<button id="button_next" type="button" class="btn btn-primary pull-left" onClick="start_training();">'+"Start"+'</button>&nbsp&nbsp');
				}
				else start_training();	
			})
		}
	});
}

function get_context(sentence, sentence_id)
{
    $.ajax({
		url: "get_context.php",
		method: "POST",
		data: {sentence_id: sentence_id},
		dataType: "text",
		success: function(data)
		{
			console.log("get_context:", data);
			// Different from claimbuster's multiple claim, wildfire only has one record in data
			var data = jQuery.parseJSON(data)[0];
			var context = sentence+"</br>";
			for(var key in data) {
				context += key + ": " + data[key] + "</br>";
			}
			$('#div_sentence').html(context);
		}		
    });          
}

function get_previous_answers(sentence_id)
{
	$('[data-toggle="popover"]').popover('hide');
	$.ajax({
		url: "get_previous_answers.php",
		method: "POST",
		dataType: "text",
		success: function(data)
		{
			console.log("get_previous_answers", data);
			data = jQuery.parseJSON(data);
			var container = $('.jumbotron .container').first();
			container.empty();			
			container.append('<br><br><div class="row"><div class="col-md-12"><button id="button_go_back" class="btn btn-primary">Go Back</button></br><h2>Previous Answers </h2><h4>(latest responses are at the top)</h4></div></div>');
			
			container.append('<ul id="selectable" class="list-group"></ul>');			
			for(i = 0; i < data.length; i++)
			{
				$('#selectable').append('<li class="list-group-item"><div class="row"><div class="col-md-12"><b>Factual claim</b>:'+data[i].claim+"</br><b>Tweet</b>: "+data[i].tweet+'</div></div><br><div class="row"><div class="col-md-1"><button id="button_change_'+data[i].id+'"class="btn btn-primary">Change</button></div><div class="col-md-11">'+'Your Response at '+data[i].time+': '+text_response[ data[i].response ]+'</div></div></li><br>')
				
				$('#button_change_'+data[i].id).click(function(){
					get_sentence(parseInt(this.id.split('_')[2]));
				});
			}
			
			$('#button_go_back').click(function(){
				get_sentence(sentence_id*(-1)); //restore to last sentence after go back
			});
		}
	});
}

function send_feedback()
{	
	$.ajax({
		url: "send_feedback.php",
		method: "POST",
		data: { feedback : $('#textarea_feedback').val(), feedback_type : $('#button_feedback_type').text()},
		dataType: "text",
		success: function(data)
		{
			$('#feedback_modal').modal('hide');
		}
	});
}
