var universities = [
		  "Duke University",
		  "University of Texas at Arlington",
		  "Other"
	];
	
var text_response = {'-1': 'There is <b>no</b> factual claim in this sentence.', '0': 'There is a factual claim but it is <b>unimportant</b>', '1': 'There is an <b>important</b> factual claim.', '-2': 'Skip this sentence'};
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
	//$('#button_profession').tooltip({'trigger':'focus','title':"Select your profession. If your profession is not in the list, select 'Other' and specify in the textbox. If you are a 'Student', select your major."});
	
	$(".reference-options li a").click(function()
	{
		$("#button_reference").text($(this).text());
  	});
  	
	
  	$('#input_other_profession').hide();
  	$('#form_major').hide();
  	$('#form_university').hide();
  	$(".profession-options li a").click(function()
	{
		$("#button_profession").text($(this).text());
		if($(this).text().localeCompare('Other') == 0)
		{
			$('#input_other_profession').show();
		}
		else
		{
			$('#input_other_profession').hide();
		}
		
		if($(this).text().localeCompare('Student') == 0 || $(this).text().localeCompare('Professor') == 0)
		{
			$('#form_major').show();
			$('#form_university').show();
		}
		else
		{
			$('#form_major').hide();
			$('#form_university').hide();
		}
		
  	});
  	
  	
  	$(".university-options li a").click(function()
	{
		$("#button_university").text($(this).text());
  	});
  	
  	$(".major-options li a").click(function()
	{
		$("#button_major").text($(this).text());
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
	   	if(!check_profession())
	   	{
	   		show_rules(1);
	   		return;
	   	}
	   	if(!check_major())
	   	{
	   		show_rules(1);
	   		return;
	   	}
	   	
	   	if(!check_university())
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

function check_reference()
{
	return $('#button_reference').text().localeCompare("How did you hear about this Survey?");
}

function check_profession()
{
	if($('#button_profession').text().localeCompare("Other") == 0)
	{
		if($('#input_other_profession').val().localeCompare("Please Specify") == 0)
		{
			return false;
		}
		else return true;
	}
	return $('#button_profession').text().localeCompare("Select Your Profession");
}

function check_major()
{
	if($('#button_profession').text().localeCompare("Student") == 0 || $('#button_profession').text().localeCompare("Professor") == 0)
	{
		return $('#button_major').text().localeCompare("Select Your Major");
	}
	return true;
}

function check_university()
{
	if($('#button_profession').text().localeCompare("Student") == 0 || $('#button_profession').text().localeCompare("Professor") == 0)
	{
		return $('#button_university').text().localeCompare("Select Your University");
	}
	return true;
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
	/*$('#button_new_user_submit').popover({
        html: true,
        content: "<p><ul><li>Username: Allowed characters are 0-9, a-z and A-Z. Must be smaller than 16 characters.</li>\
        		         <li>Password: Allowed characters are 0-9, a-z and A-Z. Must be smaller than 16 characters. Both passwords\ 						should exactly match.</li></p>"
    });
    $('#button_new_user_submit').popover('show');*/
    if(number == 1)alert("Email Address: A valid email address.\n\nUsername: Allowed characters are 0-9, a-z, A-Z, PERIOD(.) and UNDERSCORE(_). Must be smaller than 16 characters.\n\nPassword: Allowed characters are 0-9, a-z, A-Z, PERIOD(.) and UNDERSCORE(_). Must be smaller than 16 characters. Both passwords should exactly match.\n\nSelect your profession. If your profession is not in the list, select 'Other' and specify in the textbox. If you are a 'Professor/Student', then please select your university and major.");
    else if(number == 2)alert("Password: Allowed characters are 0-9, a-z, A-Z, PERIOD(.) and UNDERSCORE(_). Must be smaller than 16 characters. Both passwords should match exactly.");
}

function check_username_exist()
{
	$.ajax({
		url: "check_username_exist.php",
		method: "POST",
		data: { username : '"'+$('#input_new_user_username').val()+'"', email : '"'+$('#input_new_user_email').val()+'"'},
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
	var profession = $('#button_profession').text();
	if(profession.localeCompare('Other') == 0)
	{
		profession = $('#input_other_profession').val();
	}
	var major = 'NULL';
	var university = 'NULL';
	if(profession.localeCompare('Student') == 0 || profession.localeCompare('Professor') == 0)
	{
		major = '"'+$('#button_major').text()+'"';
		university = '"'+$('#button_university').text()+'"';
	}
	$.ajax({
		url: "insert_new_user.php",
		method: "POST",
		data: { username : '"'+$('#input_new_user_username').val()+'"',
				password : '"'+$('#input_new_user_password').val()+'"' ,
				email : '"'+$('#input_new_user_email').val()+'"',
				profession : '"'+profession+'"',
				university : university,
				major : major},
		dataType: "text",
		success: function(data)
		{
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
				password : '"'+$('#input_password').val()+'"' },
		dataType: "text",
		success: function(data)
		{
			console.log(data)
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
			//console.log(data);
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

//function get_progress()
//{
//	$.ajax({
//		url: "get_progress.php",
//		method: "POST",
//		dataType: "text",
//		success: function(data)
//		{
//			$('#selectable_leaderboard').append('Survey Progress <div class="progress">  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 50%;">    60%  </div></div>');
//		}
//	});
//}

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
			//alert("This feature is disabled momentarily.");
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
	
	container.append('<p class="justified_para top_margin">You will be shown actual sentences from Presidential debates. All you need to do is decide whether the sentence contains an important factual claim, an unimportant factual claim or no factual claim. Your three options will always be:<br><br><img src="image/1.png" class="img-responsive center-block with-border" alt="Figure 1"><span class="center-block">Figure 1</span><br><b>Examples of important factual claims:</b><br>“We spend less on the military today than at any time in our history.”<br>“The President’s position on gay marriage has changed.”<br>“More people are unemployed today than four years ago.”<br><br><b>Examples of unimportant factual claims:</b><br>“I was in Iowa yesterday.”<br>“My mother enjoys cooking.”<br>“I ran for President once before.”<br><br><b>Examples of sentences with no factual claims (just opinions, questions & declarations):</b><br>“Iran must not get nuclear weapons.”<br>“7% unemployment is too high.”<br>“My opponent is wishy-washy.”<br>“I will be tough on crime.”<br>"Why should we do that?"<br>“Hello, New Hampshire!”<br>“Our plan is to reduce tax rate by 10%.”<br><br><b>TIP:</b> If a sentence contains an opinion and a factual claim, then you should NOT select the “no factual claim” option.<br><b>Example:</b>“Unemployment rate is too high and in last four years it increased from 8% to 10%.”<br><br>Even though this sentence contains opinion (“Unemployment rate is too high”) it also contains a factual claim (“in last four years it increased from 8% to 10%”). The correct answer for this would be “There is an important factual claim.”<br><br>If you need more information to decide on your answer please click the “More context” button to see the sentences that were spoken before the sentence you are being asked about. If you are still unsure you can select the “skip sentence” button.<br><br>There will be 40 training questions at the beginning. We will show the correct answer and a brief explanation after you submit your response. The actual data collection will start after you are done with the initial 40 training questions. <br><br>Please feel free to use the “Feedback” button (to the right of the browser window) to inform us your suggestions and/or report errors. You can also contact us at <a href="mailto:classifyfact@gmail.com">classifyfact@gmail.com</a>. Thanks!</p>');
	
	//<br><b>Prizes</b><br>Your participation in this study will enter you into a random drawing for prizes. The more sentences you complete the more entries you will have in this prize lottery. By the end of this study, we will provide grand prizes accordingly.<br><br>• One grand prize of $200<br>• Two prizes of $100 each (one already awarded) <br>• Twenty prizes of $10 each (fifteen already awarded) <br><br>Please follow the directions carefully to be eligible for the random prize drawings. It may be tempting to rush through without giving your answers any thought but our software can identify volunteers who are not following directions and who are randomly selecting answers. We reserve the right to exclude anyone from prizes. To maximize your winning chances, please follow all directions carefully and try your best.<br>
	
	
//	container.append('<p class="justified_para top_margin">Please follow the directions carefully to be eligible for the random prize drawings. It may be tempting to rush through without giving your answers any thought but our software can identify volunteers who are not following directions and who are randomly selecting answers. To maximize your winning chances, please follow all directions carefully and try your best.</p>');
//	
//	container.append('<h2><u>Survey Instructions</u></h2><br>');
//	
//	container.append('<p class="justified_para">We will present one sentence at a time as shown in Figure 1. Your job is to decide whether the sentence is a check-worthy factual statement or not. There are three options. You can select one of them. After selecting one option, you have to click the "Submit" button. You can skip labeling a sentence by clicking the "Skip this sentence" button.<br><br><img src="image/1.png" class="img-responsive center-block with-border" alt="Figure 1"><span class="center-block">Figure 1</span><br>If you find it difficult to decide, there is a "More Context" button to give you a little context about the sentence. Please be gently reminded that even if the "More Context" button shows you 5 sentences, your job is to decide check-worthiness of the main sentence which is in bold-font. Please see Figure 2 for example.<br><br><img src="image/2.png" class="img-responsive center-block with-border" alt="Figure 2"><span class="center-block">Figure 2</span><br>You can click the "Modify My Previous Responses" button to see the sentences you have labeled so far. Clicking that button will present a list like Figure 3. You can click the "Change" button to modify your response for any sentence. "Go Back" button will take you to the survey again.<br><br><img src="image/3.png" class="img-responsive center-block with-border" alt="Figure 3"><span class="center-block">Figure 3</span><br>You can use the "Feedback" button to report any problem about the website or data. Also, you can give us your valuable suggestions.</p>');
//	
//	container.append('<h2><u>Prizes</u></h2><br>');
//	
//	container.append('<p>Your participation in this study will enter you into a random drawing for prizes. The more sentences you complete the more entries you will have in this prize lottery. At the end of this study, we will provide grand prizes accordingly.</p><br><ul><li>One grand prize of $200</li><li>Two prizes of $100 each</li><li>Twenty prizes of $10 each</li></ul><br><p>Please again be reminded that we want genuine effort from participants, and we will apply various approaches to detect spam and low-quality participants, and we reserve the right to exclude anyone from prizes.</p>');
	
	container.append('<div class="center-align"><input id="input_agree" type="checkbox"><label><b>I have carefully read and understood all the instructions.</b></label><br><br><button id="button_start_survey" type="button" class="btn btn-primary btn-lg" disabled>Start</button><br><br> </div>');
	
	$('input').iCheck({
		checkboxClass: 'icheckbox_square-blue',
		radioClass: 'iradio_square-blue',
		uncheckedCheckboxClass: 'hover'
	});	

	$('#button_start_survey').click(function(){
		start_training();
		//get_sentence(0);
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
//	container.append('');
	container.append('<br><div id="top_well" class="well message highlight"><span id="span_REGION_status"></span></div><div class="panel panel-primary">  <div class="panel-body"><div id="div_sentence" class="well">'+sentence+'</div><button id="button_context" type="button" class="btn btn-xs btn-primary">More Context</button><br><br><div class="panel-title">Will the general public be interested in knowing whether (part of) this sentence is true or false?</div><br>  <ul class="list-group"><li class="list-group-item"><input type="radio" name="iCheck" id="-1"><label>'+text_response['-1']+'</label></li><li class="list-group-item"><input type="radio" name="iCheck" id="0"><label>'+text_response['0']+'</label></li><li class="list-group-item"><input type="radio" name="iCheck" id="1"><label>'+text_response['1']+'</label></li><!--<li class="list-group-item"><input type="radio" name="iCheck" id="-2"><label>'+text_response['-2']+'</label></li>--></ul></div><div class="panel-footer"><button id="button_submit_answer" type="button" class="btn btn-primary">Submit</button>&nbsp&nbsp<button id="button_skip" type="button" class="btn btn-info">'+text_response['-2']+'</button><button id="button_previous_answers" type="button" class="btn btn-primary pull-right">Modify My Previous Responses</button></div></div><div class="well tips justified_para"><span id="span_tips">Tips for improving your payrate: (1) Carefully examine each sentence. (2) Read instructions carefully (click the button above) to understand examples belonging to each category. (3) Don\'t guess. Skip sentences that you are not sure about. (4) Contextual sentences (by clicking “More Context”) may help you form answers.) (5) Modify previous responses if necessary. (6) You may be tempted to pick easy/short questions to work on by "skipping". However, your payrate will get lower, since our payrate calculation formula has a component that accounts for the length/complexity of sentences. Basically, we discourage excessive skipping. Nevertheless, if you are not confident on a question, it is still better to skip, because every single mistake will lower your payrate. (See (3) above related to "Don\'t guess".)<br><br>Whenever you make 1 mistake, our algorithm lowers your pay rate, which means you get paid less for every sentence you have labeled. It takes multiple correct answers to make up for every single mistake and get the pay rate back to the previous value. If you current pay rate is 0 or very low, it is because our algorithm detected many mistakes in your answers so far. The best thing to do is to go back and review all your answers so far and modify them if necessary. If your payrate is 0, it might actually be negative internally. If you continue to answer new questions, it will take MANY questions before you can see positive and improving payrate.<br><br>If you have labeled 50-150 sentences, the quality measure based on the small number of sentences may not be statistically significant. Hence, your current pay rate may not reflect your true work quality. It will become more accurate once you have labeled more sentences.</span></div>');

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
	
	var context_seen = 0;
	$('#button_context').click(function(){
		if($('#button_context').text().localeCompare("More Context") == 0)
		{
			context = get_context(sentence_id);
			$('#button_context').text('Less Context');
			context_seen = 1;
		}
		else
		{
			$('#div_sentence').html(sentence);
			$('#button_context').text('More Context');
		}
		
	})
		
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
		$('#span_REGION_status').text("Our tool detected that the quality of your responses is not high. If the quality falls below a certain threshold, we won’t be able to consider your response for prize drawing. You can modify your previous responses, in order to improve the quality. Please spend more time on each sentence and read it more carefully. If necessary, use the “more context button”; if unsure, use the “skip” button.");
		break;
	case 4:
		//console.log(REGION);
		$('#span_REGION_status').css('background-color',"#FF6666");
		$('#span_REGION_status').text("Our tool detected that the quality of your responses is poor. If it cannot be improved, we won’t be able to consider your response for prize drawing. In order to improve the quality, please modify your previous responses. Please spend more time on each sentence and read it more carefully. If necessary, use the “more context button”; if unsure, use the “skip” button.");
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
//	$('#button_leaderboard').prop('disabled', false);
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
			if(data.localeCompare("-1") == 0)
			{
				alert('No more sentences available for you. Thanks for your participation. We may notify you in case there is a third phase of the data collection.');
				get_previous_answers(0);
				return;
			}
			data = jQuery.parseJSON(data);
			//show_sentence(data.id, "<b>"+data.name+": "+data.text+"</b>", data.REGION);
			show_sentence(data.id, "<b>"+data.name+": "+data.text+"</b>", data.REGION, data.ANSWERED_message, data.QUALITY_message, data.PAYMENT_message, data.RANK_message, data.total_message);
//			show_sentence(data.id, "<b>"+data.text+"</b>", data.REGION, data.ANSWERED_message, data.QUALITY_message, data.PAYMENT_message, data.RANK_message, data.total_message);			
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
//			get_answer_count();
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
//			get_answer_count();
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
			data = data.split('^');
			$('#top_well').removeClass("highlight");
			if(data[1].localeCompare("Correct!") == 0)
			{
				$('#top_well').addClass("correct");
			}
			else
			{
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

function get_context(sentence_id)
{
    $.ajax({
		url: "get_context.php",
		method: "POST",
		data: {sentence_id: sentence_id},
		dataType: "text",
		success: function(data)
		{
			var data = jQuery.parseJSON(data);
			var context = "";
			for (i = 0; i < data.length-1; i++)
			{
				context += data[i].name + ": " + data[i].text + "</br>";
//				context += data[i].text + "</br>";
			}
			
			context += "<b>"+data[i].name + ": " + data[i].text + "</b></br>";
//			context += "<b>"+data[i].text + "</b></br>";
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
			data = jQuery.parseJSON(data);
			var container = $('.jumbotron .container').first();
			container.empty();			
			container.append('<br><br><div class="row"><div class="col-md-12"><button id="button_go_back" class="btn btn-primary">Go Back</button></br><h2>Previous Answers </h2><h4>(latest responses are at the top)</h4></div></div>');
			
			container.append('<ul id="selectable" class="list-group"></ul>');			
			for(i = 0; i < data.length; i++)
			{
				$('#selectable').append('<li class="list-group-item"><div class="row"><div class="col-md-12">'+data[i].name+": "+data[i].text+'</div></div><hr><div class="row"><div class="col-md-1"><button id="button_change_'+data[i].id+'"class="btn btn-primary">Change</button></div><div class="col-md-11">'+'Your Response at '+data[i].time+': '+text_response[ data[i].response ]+'</div></div></li><br>');
//				$('#selectable').append('<li class="list-group-item"><div class="row"><div class="col-md-12">'+data[i].text+'</div></div><hr><div class="row"><div class="col-md-8">'+'Response: '+text_response[ data[i].response ]+'</div><div class="col-md-2"></div><div class="col-md-2"><button id="button_change_'+data[i].id+'"class="btn btn-primary pull-right">Change</button></div></div></li><br>');
				
				$('#button_change_'+data[i].id).click(function(){
					get_sentence(parseInt(this.id.split('_')[2]));
				});
			}
			
			$('#button_go_back').click(function(){
				get_sentence(sentence_id*(-1)); //restore to last sentence after go back
				//get_sentence(0); //for pseudo skip
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
