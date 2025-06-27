var universities = [
    "Duke University",
    "University of Texas at Arlington",
    "Other",
];

var text_response = {
    "-1": "The tweet believes the factual claim is false.",
    0: "The tweet expresses a neutral or no stance towards the factual claim's truthfulness.",
    1: "The tweet believes the factual claim is true.",
    "-2": "Skip this pair",
    2: "The tweet and the claim discuss different topics.",
    3: 'The tweet is created for sarcasm or parody only, or the tweet is problematic (e.g., hyperlink<br>leading to "page not found" error, page content of the hyperlink behind paywall).',
};
var logged_out = 1;
var is_training = 1;
var training_index = -1;
var displayed_tweet = false;

window.onbeforeunload = function (e) {
    e = e || window.event;
    // For IE and Firefox prior to version 4
    if (e && !logged_out) {
        e.returnValue =
            "Refreshing/Leaving this webpage will log you out from the survey.";
    }
    // For Safari
    if (!logged_out)
        return "Refreshing/Leaving this webpage will log you out from the survey.";
};

$(document).ajaxStart(function () {
    //$('#modal_ajax_loader').modal('show');
});

$(document).ajaxStop(function () {
    //$('#modal_ajax_loader').modal('hide');
});

$(document).ready(function () {
    window.twttr = (function (d, s, id) {
        var js,
            fjs = d.getElementsByTagName(s)[0],
            t = window.twttr || {};
        if (d.getElementById(id)) return t;
        js = d.createElement(s);
        js.id = id;
        js.src = "https://platform.twitter.com/widgets.js";
        fjs.parentNode.insertBefore(js, fjs);
        $("blockquote.twitter-tweet").remove();
        t._e = [];
        t.ready = function (f) {
            t._e.push(f);
        };

        return t;
    })(document, "script", "twitter-wjs");

    $("#input_new_user_email").tooltip({
        trigger: "focus",
        title: "Enter a valid email address.",
    });
    $("#input_new_user_username").tooltip({
        trigger: "focus",
        title: "Allowed characters are 0-9, a-z, A-Z, PERIOD(.) and UNDERSCORE(_). Must be smaller than 16 characters.",
    });
    $("#input_new_user_password").tooltip({
        trigger: "focus",
        title: "Allowed characters are 0-9, a-z, A-Z, PERIOD(.) and UNDERSCORE(_). Must be smaller than 16 characters.",
    });
    $("#input_new_user_confirm_password").tooltip({
        trigger: "focus",
        title: "Both passwords should match.",
    });

    $(".reference-options li a").click(function () {
        $("#button_reference").text($(this).text());
    });

    $(".feedback-options li a").click(function () {
        $("#button_feedback_type").text($(this).text());
    });

    change_navbar(0);
    $("#button_sign_in").click(function () {
        check_sign_in_information();
    });

    $(document).keypress(function (e) {
        if (e.which == 13) {
            $("#button_sign_in").click();
        }
    });

    $("#button_new_user_submit").click(function () {
        /*if(!check_full_name())
	   	{
	   		alert('FN');
	   		return;
	   	}*/
        if (!check_email()) {
            show_rules(1);
            return;
        }
        if (!check_username()) {
            show_rules(1);
            return;
        }
        if (!check_password()) {
            show_rules(1);
            return;
        }
        if (!check_confirm_password()) {
            show_rules(1);
            return;
        }
        check_username_exist();
    });

    $("#button_log_out").click(function () {
        $.ajax({
            url: "clear_session.php",
            method: "POST",
            success: function (data) {
                logged_out = 1;
                $(location).attr("href", "index.php");
            },
        });
    });

    $("#button_survey").click(function () {
        init_survey();
    });

    $("#button_forgot_password").click(function () {
        forgot_password();
    });

    $("#button_feedback_send").click(function () {
        send_feedback();
    });
});

function forgot_password() {
    var container = $(".jumbotron .container").first();
    container.empty();
    container.append(
        "<br><div><br><div class='row'><div class='col-md-3'><label for='new_user_email'>Enter Email Address</label></div> <div class='col-md-5'><input type='email' class='form-control' id='input_forgot_email' placeholder='Enter Email Address'></div><div class='col-md-2'><button id='button_forgot_email' class='btn btn-primary' role='button'>Submit</button></div></div></div>"
    );

    $("#button_forgot_email").click(function () {
        $.ajax({
            url: "forgot_password.php",
            method: "POST",
            data: { email: '"' + $("#input_forgot_email").val() + '"' },
            dataType: "text",
            success: function (data) {
                alert(data);
            },
        });
    });
}

function reset_password(username) {
    var valid_characters = /^[0-9a-zA-Z]{1,15}/;
    if ($("#input_reset_password").val().match(valid_characters)) {
        if (
            $("#input_reset_password").val() ==
            $("#input_reset_password_confirm").val()
        ) {
            $.ajax({
                url: "reset_password_confirm.php",
                method: "POST",
                data: {
                    username: username,
                    password:
                        '"' + $("#input_reset_password_confirm").val() + '"',
                },
                dataType: "text",
                success: function (data) {
                    alert(data);
                    window.location.href = "index.php";
                },
            });
        } else show_rules(2);
    } else show_rules(2);
}

function endsWith(str, suffix) {
    return str.indexOf(suffix, str.length - suffix.length) !== -1;
}

function check_email() {
    var valid_characters =
        /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return valid_characters.test($("#input_new_user_email").val());
}

function check_full_name() {
    var valid_characters = /^[0-9a-zA-Z \.,]+$/;
    if ($("#input_new_user_full_name").val().match(valid_characters)) {
        return true;
    } else return false;
}

function check_username() {
    var valid_characters = /^[0-9a-zA-Z\._]{1,15}/;
    if ($("#input_new_user_username").val().match(valid_characters)) {
        return true;
    } else return false;
}

function check_password() {
    var valid_characters = /^[0-9a-zA-Z\._]{1,15}/;
    if ($("#input_new_user_password").val().match(valid_characters)) {
        return true;
    } else return false;
}

function check_confirm_password() {
    return (
        $("#input_new_user_password").val() ==
        $("#input_new_user_confirm_password").val()
    );
}

function show_rules(number) {
    if (number == 1)
        alert(
            "Email Address: A valid email address.\n\nUsername: Allowed characters are 0-9, a-z, A-Z, PERIOD(.) and UNDERSCORE(_). Must be smaller than 16 characters.\n\nPassword: Allowed characters are 0-9, a-z, A-Z, PERIOD(.) and UNDERSCORE(_). Must be smaller than 16 characters. Both passwords should exactly match."
        );
    else if (number == 2)
        alert(
            "Password: Allowed characters are 0-9, a-z, A-Z, PERIOD(.) and UNDERSCORE(_). Must be smaller than 16 characters. Both passwords should match exactly."
        );
}

function check_username_exist() {
    $.ajax({
        url: "check_username_exist.php",
        method: "POST",
        data: {
            username: '"' + $("#input_new_user_username").val() + '"',
            email: '"' + $("#input_new_user_email").val() + '"',
        },
        dataType: "text",
        success: function (data) {
            console.log("check username exist returned:", data);
            if (data.localeCompare("0") == 0) {
                insert_new_user();
            } else {
                alert(
                    "Username or Email Address already exist. Please select another one."
                );
            }
        },
    });
}

function insert_new_user() {
    $.ajax({
        url: "insert_new_user.php",
        method: "POST",
        data: {
            username: '"' + $("#input_new_user_username").val() + '"',
            password: '"' + $("#input_new_user_password").val() + '"',
            email: '"' + $("#input_new_user_email").val() + '"',
        },
        dataType: "text",
        success: function (data) {
            console.log("insert_new_user:", data);
            $("#modal_new_user").modal("hide");
            alert(data);
        },
    });
}

function check_sign_in_information() {
    $.ajax({
        url: "check_sign_in_information.php",
        method: "POST",
        data: {
            username: '"' + $("#input_username").val() + '"',
            password: '"' + $("#input_password").val() + '"',
        },
        dataType: "text",
        success: function (data) {
            console.log("check_sign_in_information.php returned:", data);
            if (data.localeCompare("-1") == 0) {
                alert("Invalid Username or Password. Please Try Again.");
            } else if (data.localeCompare("-2") == 0) {
                alert(
                    "Account not verified. Please check your email and verify the account."
                );
            } else if (data.localeCompare("1") == 0) {
                console.log("check_sign_in_information passed");
                logged_out = 0;
                init_survey();
                var username = $("#span_username").text();
                ga("set", "&uid", username); // Set the user ID using signed-in user_id.
            }
        },
    });
}

function get_answer_count() {
    $.ajax({
        url: "get_answer_count.php",
        method: "POST",
        dataType: "text",
        success: function (data) {
            console.log("get_answer_count.php returned:", data);
            data = data.split("^");
            $("#span_badge").text(" " + data[0] + " ");
            $("#span_username").text(
                "" + data[1].replace('"', "").replace('"', "" + " labeled")
            );
        },
    });
}

function get_leaderboard() {
    $.ajax({
        url: "get_leaderboard.php",
        method: "POST",
        dataType: "text",
        success: function (data) {
            data = jQuery.parseJSON(data);
            console.log("get_leaderboard:", data);
            $("#modal_leaderboard").modal("show");
            $("#selectable_leaderboard").empty();
            $("#selectable_leaderboard").append(
                '<li class="list-group-item"><div class="row"><div class="col-md-3">' +
                    "<b>USERNAME</b>" +
                    '</div><div class="col-md-3">' +
                    "<b>ANSWERED</b>" +
                    '</div><div class="col-md-3">' +
                    "<b>WORK QUALITY SCORE</b>" +
                    '</div><div class="col-md-3">' +
                    "<b>TOTAL POINTS</b>" +
                    "</div></div></li>"
            );
            // $("#selectable_leaderboard").append(
            //     '<li class="list-group-item"><div class="row"><div class="col-md-3">' +
            //         "<b>USERNAME</b>" +
            //         '</div><div class="col-md-3">' +
            //         "<b>ANSWERED</b>" +
            //         '</div><div class="col-md-3">' +
            //         "<b>PAY RATE</b>" +
            //         '</div><div class="col-md-3">' +
            //         "<b>PAYMENT ($)</b>" +
            //         "</div></div></li>"
            // );

            user = data[0].user;

            for (i = 0; i < data.length; i++) {
                if (i != user) {
                    if (data[i].prize.localeCompare("") != 0) {
                        $("#selectable_leaderboard").append(
                            '<li class="list-group-item winner"><div class="row"><div class="col-md-3"><b>' +
                                data[i].USERNAME +
                                "<br>" +
                                data[i].prize +
                                '</div><div class="col-md-3">' +
                                data[i].ANSWERED +
                                '</div><div class="col-md-3">' +
                                data[i].PAYRATE +
                                '</div><div class="col-md-3">' +
                                data[i].PAYMENT +
                                "</b></div></div></li>"
                        );
                    } else
                        $("#selectable_leaderboard").append(
                            '<li class="list-group-item"><div class="row"><div class="col-md-3">' +
                                data[i].USERNAME +
                                '</div><div class="col-md-3">' +
                                data[i].ANSWERED +
                                '</div><div class="col-md-3">' +
                                data[i].PAYRATE +
                                '</div><div class="col-md-3">' +
                                data[i].PAYMENT +
                                "</div></div></li>"
                        );
                } else {
                    if (data[i].prize.localeCompare("") != 0) {
                        $("#selectable_leaderboard").append(
                            '<li class="list-group-item winner"><div class="row"><div class="col-md-3"><b>' +
                                data[i].USERNAME +
                                "<br>" +
                                data[i].prize +
                                '</div><div class="col-md-3">' +
                                data[i].ANSWERED +
                                '</div><div class="col-md-3">' +
                                data[i].PAYRATE +
                                '</div><div class="col-md-3">' +
                                data[i].PAYMENT +
                                "</b></div></div></li>"
                        );
                    } else
                        $("#selectable_leaderboard").append(
                            '<li class="list-group-item user"><div class="row"><div class="col-md-3"><b>' +
                                data[i].USERNAME +
                                '</div><div class="col-md-3">' +
                                data[i].ANSWERED +
                                '</div><div class="col-md-3">' +
                                data[i].PAYRATE +
                                '</div><div class="col-md-3">' +
                                data[i].PAYMENT +
                                "</b></div></div></li>"
                        );
                }
            }
        },
    });
}

function get_consent() {
    $.ajax({
        url: "get_consent.php",
        method: "POST",
        dataType: "text",
        success: function (data) {
            console.log("get_consent.php returned:", data);
            if (data.localeCompare("0") == 0) {
                $("#modal_consent").modal({
                    backdrop: "static",
                    position: "absolute",
                    keyboard: false,
                });
                $("#modal_consent").modal("show");
                console.log("consent not set");
                $(".button_consent_accept").click(function () {
                    set_consent();
                });
            }
        },
    });
}

function set_consent() {
    $.ajax({
        url: "set_consent.php",
        method: "POST",
        dataType: "text",
        success: function (data) {
            $("#modal_consent").modal("hide");
        },
    });
}

function init_survey() {
    change_navbar(1);
    get_answer_count();
    get_consent();
    load_survey_instructions();
}

function change_navbar(login_status) {
    if (login_status) {
        //user just logged in.
        $("#button_navbar").attr("data-target", "#navbar_logout");
        $("#navbar_signin_signup").attr("style", "display:none !important");
        // $("#navbar_logout").show();
        $("#navbar_info_form").show();
        $("#button_leaderboard").click(function () {
            get_leaderboard();
        });
        $("#button_load_survey_instructions").click(function (event) {
            event.stopPropagation();
            load_survey_instructions();
        });
        var menuText = document.getElementById("navbar_menu_text");
        menuText.textContent = "User Info";
    } //user just logged out
    else {
        $("#button_navbar").attr("data-target", "#navbar_signin_signup");
        // $("#navbar_logout").attr("style", "display:none !important");
        $("#navbar_info_form").attr("style", "display:none !important");

        // $("#navbar_signin_signup").show();
    }
}

function load_survey_instructions() {
    $("#button_leaderboard").prop("disabled", true);
    var container = $(".jumbotron .container").first();
    container.empty();
    container.append(
        '<p class="justified_para top_margin"> \
		<h2>Annotation Instructions</h2> \
        <div style="font-size: 140% !important;"> \
        Below is a screenshot of the annotation task. You will be shown a tweet-claim pair. In each pair, the claim on the left is fact-checked by PolitiFact, \
        and the tweet on the right is displayed as it would appear on Twitter. Your task is to decide the truthfulness stance of the tweet towards the factual claim, \
        i.e., whether the tweet believes the factual claim is true or false. Your five annotation options are as follows. \
            <br><img src="image/instruction_screenshot.png" class="img-responsive center-block with-border" alt="Figure 1"> \
            <span class="center-block" style="font-size:115%">Truthfulness stance detection annotation example</span>\
        </div> \
		\
        <div style="font-size: 160%; !important;"> \
            <div id="accordion" style="min-width: 1100px;">\
            <button class="btn btn-success" type="button" style="float: right;" data-toggle="collapse" data-target=".multiCollapse" aria-expanded="true" aria-controls="collapse_positive collapse_neutral collapse_negative collapse_problematic"><span style="font-size:115%">Toggle all example boxes</span></button>\
            <br><br>\
            <div class="card">\
                <ul class="list-group">\
                    <div class="card-header" id="heading_important">\
                        <button class="btn btn-primary btn-lg btn-block" style="text-align:left; padding: 10px 16px 10px; !important;" data-toggle="collapse" data-target="#collapse_positive" aria-expanded="true" aria-controls="collapse_positive">\
                        <p><h3><b>The tweet believes the factual claim is true.</b></h3></p>\
                        </button>\
                    </div>\
                    <div id="collapse_positive" class="collapse in multiCollapse" aria-labelledby="heading_important" data-parent="#accordion">\
                        <li class="list-group-item">\
                            <div class="p-3 mb-2" >\
                                <ul>\
                                    <hr style="height:7pt; margin:0; border: 0; visibility:hidden;">\
                                    <b style="margin-left: -30px">Example 1</b>\
                                    <li>\
                                        <b>Factual claim:</b> \'The largest bust in U.S. history\' 412 Muslims arrested from Michigan!<br>\
                                    </li>\
                                    <li> \
                                        <b>Tweet:</b> 412 Michigan Muslims Arrested In Fed\'s \'LARGEST BUST IN U.S. HISTORY\' After Uncovering Deadly Hidden Secret https://leadpatriot.com/412-michigan-arrested-in-feds-largest-bust-in-u-s-history-after-uncovering-deadly-hidden-secret/206/ \
                                    </li>\
                                    <div style="padding-left:5px; color:#31708f">\
                                        <b>Explanation:</b> The tweet rephrases the claim and capitalizes various words. It also contains a link to a webpage that provides further details on the claim. Based on these we can deduce that the tweet believes the factual claim is true.\
                                    </div>\
                                    <hr style="height:7pt; margin:0; border: 0; visibility:hidden;">\
                                    <b style="margin-left: -30px">Example 2</b>\
                                    <li>\
                                        <b>Factual claim:</b> Ohio student suspended for staying in class during National Walkout Day.<br>\
                                    </li>\
                                    <li> \
                                        <b>Tweet:</b> Patriot attorney needed.  Reach out to this family.  Ohio student suspended for staying in class during National Walkout Day https://truepundit.com/ohio-student-suspended-staying-class-national-walkout-day/… \
                                    </li>\
                                    <div style="padding-left:5px; color:#31708f">\
                                        <b>Explanation:</b> The tweet calls for supporting the student who was allegedly suspended based on the claim. This suggests the tweet believes the claim is true.\
                                    </div>\
                                    <hr style="height:7pt; margin:0; border: 0; visibility:hidden;">\
                                    <b style="margin-left: -30px">Example 3</b>\
                                    <li>\
                                        <b>Factual claim:</b> KAMALA HARRIS Says Schools in Berkeley Weren\'t Integrated When She Was a Kid — But Yearbook Pictures Prove She\'s Lying.<br>\
                                    </li>\
                                    <li> \
                                        <b>Tweet:</b> It is like these morons don\'t know how the Internet works.  We can look up your lies!  Unreal...  KAMALA HARRIS Says Schools in Berkeley Weren\'t Integrated When She Was a Kid -- But Yearbook Pictures Prove She\'s Lying. \
                                    </li>\
                                    <div style="padding-left:5px; color:#31708f">\
                                        <b>Explanation:</b> The tweet believes Kamala Harris lied and that her alleged lie could be easily refuted by looking up online. For that reason, the tweet also calls Harris a "moron". All these signals indicate that the tweet believes the claim is true.\
                                    </div>\
                                </ul></p>\
                            </div>\
                            <hr style="height:7pt; margin:0; border: 0; visibility:hidden;" />\
                        </li>\
                    </div>\
                </ul>\
            </div>\
            <div class="card">\
                <ul class="list-group">\
                    <div class="card-header" id="heading_unimportant">\
                        <button class="btn btn-primary btn-lg btn-block" style="text-align:left; padding: 10px 16px 10px; !important;" data-toggle="collapse" data-target="#collapse_neutral" aria-expanded="true" aria-controls="collapse_neutral">\
                        <p><h3><b>The tweet expresses a neutral or no stance towards the factual claim\'s truthfulness.</b></h3></p>\
                        </button>\
                    </div>\
                    <div id="collapse_neutral" class="collapse in multiCollapse" aria-labelledby="heading_unimportant" data-parent="#accordion">\
                        <li class="list-group-item">\
                            <div class="p-3 mb-2" >\
                                <ul>\
                                    <hr style="height:7pt; margin:0; border: 0; visibility:hidden;">\
                                    <b style="margin-left: -30px">Example 1</b>\
                                    <li>\
                                        <b>Factual claim:</b> NASA confirms Earth will experience 15 days of darkness In November 2017.<br>\
                                    </li>\
                                    <li> \
                                        <b>Tweet:</b> How true is this..NASA Confirms Earth Will Experience 15 Days Of Complete Darkness in November 2015 https://newswatch33.com/science/nasa-confirms-earth-will-experience-15-days-of-complete-darkness-in-november-2015/ \
                                    </li>\
                                    <div style="padding-left:5px; color:#31708f">\
                                        <b>Explanation:</b> The URL in the tweet suggests that the linked page discusses the same claim. Further reading the page content verifies that. The tweet inquires about the claim\'s veracity and it appears to be a genuine inquiry. Hence, it seems the tweet is not certain about the claim\'s truthfulness. We bundle "neutral stance" and "no stance" together as one answer option. Still, they are not the same. This example is a case of neutral stance (rather than no stance) since the tweet is directly asking about the claim\'s truthfulness.\
                                    </div>\
                                    <hr style="height:7pt; margin:0; border: 0; visibility:hidden;">\
                                    <b style="margin-left: -30px">Example 2</b>\
                                    <li>\
                                        <b>Factual claim:</b> The media distorted what happened with a baby at his rally.<br>\
                                    </li>\
                                    <li> \
                                        <b>Tweet:</b> Trump says \'dishonest\' media distorted his \'baby joke\': He referred to the event at a rally in Des Moines, Io... http://bit.ly/2aVwNUx \
                                    </li>\
                                    <div style="padding-left:5px; color:#31708f">\
                                        <b>Explanation:</b> The tweet only stated that former U.S. President Donald Trump made the claim. It expresses no stance regarding the claim\'s veracity.\
                                    </div>\
                                    <hr style="height:7pt; margin:0; border: 0; visibility:hidden;">\
                                    <b style="margin-left: -30px">Example 3</b>\
                                    <li>\
                                        <b>Factual claim:</b> Paul Ryan has blocked all action to strengthen our gun laws.<br>\
                                    </li>\
                                    <li> \
                                        <b>Tweet:</b> Wisconsin students are marching 50 miles to Paul Ryan\'s hometown for action on gun laws https://buff.ly/2up74Pn \
                                    </li>\
                                    <div style="padding-left:5px; color:#31708f">\
                                        <b>Explanation:</b> The tweet and the claim are topic related since they both mention Paul Ryan\'s actions (or lack thereof) on gun laws. However, the tweet does not express any stance on whether Paul Ryan has blocked such actions. It only says the students are pressing him for actions.\
                                    </div>\
                                        <hr style="height:7pt; margin:0; border: 0; visibility:hidden;">\
                                        <b style="margin-left: -30px">Example 4</b>\
                                    <li>\
                                        <b>Factual claim:</b> Is the Red Cross \'Not Helping California Wildfire Victims\'?<br>\
                                    </li>\
                                    <li> \
                                        <b>Tweet:</b> Don\'t forget tomorrow is HAT DAY! Your $2 donation to the Red Cross will help California wildfire victims! \
                                    </li>\
                                    <div style="padding-left:5px; color:#31708f">\
                                        <b>Explanation:</b> The tweet and the claim are related because they both concern the topic of the Red Cross\'s aid to California wildfire victims. The tweet advocates for people to donate so that the Red Cross can better help the victims. Therefore, if the tweet user is to express their stance toward the claim\'s truthfulness, they will say they believe the Red Cross was helping. However, the tweet itself does not express any stance toward the claim. That\'s why this is an example of "no stance". Note that the fact-check article presents the claim in the form of a question. The background is that there was a claim spreading online that said the Red Cross was not helping the wildfire victims. Regardless of whether the claim said the Red Cross was helping or not, the tweet expresses no stance toward the claim, although the tweet user would have clear stance if they express the stance.\
                                    </div>\
                                </ul>\
                            </div>\
                            <hr style="height:7pt; margin:0; border: 0; visibility:hidden;" />\
                        </li>\
                    </div>\
                </ul>\
            </div>\
        <div>\
        <div class="card">\
            <ul class="list-group">\
                <div class="card-header" id="heading_no_factual_claim">\
                    <button class="btn btn-primary btn-lg btn-block" style="text-align:left; padding: 10px 16px 10px; !important;" data-toggle="collapse" data-target="#collapse_negative" aria-expanded="true" aria-controls="collapse_negative">\
                    <p><h3><b>The tweet believes the factual claim is false.</b></h3></p>\
                    </button>\
                </div>\
                <div id="collapse_negative" class="collapse in multiCollapse" aria-labelledby="heading_no_factual_claim" data-parent="#accordion">\
                    <li class="list-group-item">\
                        <div class="p-3 mb-2" >\
                            <ul>\
                            <hr style="height:7pt; margin:0; border: 0; visibility:hidden;">\
                            <b style="margin-left: -30px;">Example 1</b>\
                            <li>\
                                <b>Factual claim:</b> We got rid of the Johnson Amendment.<br>\
                            </li>\
                            <li> \
                                <b>Tweet:</b> Donald Trump: "We got rid of the Johnson Amendment." Rated Four Pinocchios by Washington Post – Via FactStream #BillionDollarLoser  https://factstream.co/factcheck/20452 \
                            </li>\
                            <div style="padding-left:5px; color:#31708f">\
                                <b>Explanation:</b> The Washington Post uses "Pinocchios" as a measure of veracity of claims in their fact-checks. Four Pinocchios is their way of saying something is outright false. The tweet refers to WaPo\'s "four Pinocchios" rating of this claim from the former U.S. President Donald Trump. It shows the tweet believes the claim is false.\
                            </div>\
                            <hr style="height:7pt; margin:0; border: 0; visibility:hidden;">\
                            <b style="margin-left: -30px">Example 2</b>\
                            <li>\
                            <b>Factual claim:</b> Rick Scott won and he won by a lot.<br>\
                        </li>\
                        <li> \
                            <b>Tweet:</b> Rick Scott is in office today because he won his election in that large state, Florida, by a mere 10,033 votes. He\'s in pretty far over his skis, ego-wise, as a member of the Just Lucky To Be Here Caucus. \
                            </li>\
                            <div style="padding-left:5px; color:#31708f">\
                                <b>Explanation:</b> The tweet states that Scott only won "by a mere 10,033 votes" and also considers him "just lucky". This contradicts with the claim that he "won by a lot". We can conclude that the tweet did not believe the factual claim was true. \
                            </div>\
                            <hr style="height:7pt; margin:0; border: 0; visibility:hidden;">\
                            <b style="margin-left: -30px">Example 3</b>\
                            <li>\
                                <b>Factual claim:</b> Jennifer Lawrence links 9/11 to Trump\'s election.<br>\
                            </li>\
                            <li> \
                                <b>Tweet:</b> A New Conspiracy Theory Falsely Claims Jennifer Lawrence Blamed Trump for 9/11. A meme with over 11,000 shares quotes Lawrence as blaming Trump for September 11 because he stole the election, but it\'s totally bogus. \
                            </li>\
                            <div style="padding-left:5px; color:#31708f">\
                                <b>Explanation:</b> The tweet mentioned "conspiracy theory", "falsely claims" and "totally bogus". It is apparent the tweet believes the claim is false.\
                            </div>\
                            </ul>\
                        </div>\
                        <hr style="height:7pt; margin:0; border: 0; visibility:hidden;" />\
                    </li>\
                </div>\
            </ul>\
        </div>\
        <div class="card">\
            <ul class="list-group">\
                <div class="card-header" id="heading_no_factual_claim">\
                    <button class="btn btn-primary btn-lg btn-block" style="text-align:left; padding: 10px 16px 10px; !important;" data-toggle="collapse" data-target="#collapse_unrelated" aria-expanded="true" aria-controls="collapse_unrelated">\
                    <p><h3><b>The tweet and the claim discuss different topics.</b></h3></p>\
                    </button>\
                </div>\
                <div id="collapse_unrelated" class="collapse in multiCollapse" aria-labelledby="heading_no_factual_claim" data-parent="#accordion">\
                    <li class="list-group-item">\
                        <div class="p-3 mb-2" >\
                            <ul>\
                                <hr style="height:7pt; margin:0; border: 0; visibility:hidden;">\
                                <b style="margin-left: -30px;">Example 1</b>\
                                <li>\
                                    <b>Factual claim:</b> New Jersey "will be out of gas for a week."<br>\
                                </li>\
                                <li> \
                                    <b>Tweet:</b> I will not be at my High School reunion this weekend in New Jersey. We ran into too many commitments.Have a nice time Scotch Plains- Fanwood \
                                </li>\
                                <div style="padding-left:5px; color:#31708f">\
                                    <b>Explanation:</b> The tweet and the claim discuss apparently unrelated topics.\
                                </div>\
                                <hr style="height:7pt; margin:0; border: 0; visibility:hidden;">\
                                <b style="margin-left: -30px">Example 2</b>\
                                <li>\
                                    <b>Factual claim:</b> Joe Biden and Democrats "have not legitimately won" the presidency.<br>\
                                </li>\
                                <li> \
                                    <b>Tweet:</b> Biden fumes over Cuomo\'s DNC speech, book claims "Every four years, Democrats asked themselves the same question about the NY Gov: \'How is Andrew Cuomo going to f- -k us this time?\' write the authors of \'Lucky: How Joe Biden Barely Won the Presidency.\' https://nypost.com/2021/03/02/book-says-biden-camp-fumed-over-cuomos-dnc-speech/ \
                                </li>\
                                <div style="padding-left:5px; color:#31708f">\
                                    <b>Explanation:</b> The tweet and the claim address different topics. The tweet focuses on Biden\'s and Democrats\' attitude or concern regarding Cuomo, but the claim focuses on the legitimacy of Joe Biden\'s presidential win.\
                                </div>\
                                <hr style="height:7pt; margin:0; border: 0; visibility:hidden;">\
                                    <b style="margin-left: -30px;">Example 3</b>\
                                <li>\
                                <b>Factual claim:</b> There is a "0.05% chance of dying from COVID."<br>\
                                </li>\
                                <li> \
                                    <b>Tweet:</b> "My mom is not the only person COVID has killed this week," said Thompson. "She is not a statistic. She is a human being. She was supposed to make a comeback, but she never got a chance." \
                                </li>\
                                <div style="padding-left:5px; color:#31708f">\
                                    <b>Explanation:</b> The claim and the tweet are related in that they both discuss COVID deaths. However, they largely discuss different topics. The claim is about death rate, while the tweet is a reflection on family loss to the virus.\
                                </div>\
                            </ul>\
                        </div>\
                        <hr style="height:7pt; margin:0; border: 0; visibility:hidden;" />\
                    </li>\
                </div>\
            </ul>\
        </div>\
        <div class="card">\
            <ul class="list-group">\
                <div class="card-header" id="heading_problematic_factual_claim">\
                    <button class="btn btn-primary btn-lg btn-block" style="text-align:left; padding: 10px 16px 10px; !important;" data-toggle="collapse" data-target="#collapse_problematic" aria-expanded="true" aria-controls="collapse_problematic">\
                    <p><h3><b>The tweet is created for sarcasm or parody only, or the tweet is problematic (e.g., hyperlink<br>leading to "page not found" error, page content of the hyperlink behind paywall).</b></h3></p>\
                    </button>\
                </div>\
                <div id="collapse_problematic" class="collapse in multiCollapse" aria-labelledby="heading_problematic_factual_claim" data-parent="#accordion">\
                    <li class="list-group-item">\
                        <div class="p-3 mb-2" >\
                            <ul>\
                            <hr style="height:7pt; margin:0; border: 0; visibility:hidden;">\
                            <b style="margin-left: -30px">Example 1</b>\
                            <li>\
                            <b>Factual claim:</b> Bill Gates talked about using vaccines to control population growth" in an "unedited 2010 TED Talk video.<br>\
                        </li>\
                        <li> \
                            <b>Tweet:</b> $$ Thats his middle name all the Way to Hell! Idiot. Its been said he has been saying this. https://share.newsbreak.com/1u38cjfg \
                            </li>\
                            <div style="padding-left:5px; color:#31708f">\
                                <b>Explanation:</b> The tweet\'s hyperlink leads to a 404 (page not found) error.\
                            </div>\
                            <hr style="height:7pt; margin:0; border: 0; visibility:hidden;">\
                            <b style="margin-left: -30px">Example 2</b>\
                            <li>\
                            <b>Factual claim:</b> Nick Freitas supports a plan letting insurance companies deny coverage for preexisting conditions like asthma or diabetes.<br>\
                        </li>\
                        <li> \
                            <b>Tweet:</b> Checking the facts on Virginia Del. and Congressional Candidate Nick Freitas who "supports a plan letting insurance companies deny coverage for preexisting conditions like asthma or diabetes." https://starexponent.com/news/checking-the-facts-on-freitas-and-health-care/article_67f429c1-83a9-517f-ad5d-6d66e3671775.html?utm_medium=social&utm_source=twitter&utm_campaign=user-share via @culpeperse \
                            </li>\
                            <div style="padding-left:5px; color:#31708f">\
                                <b>Explanation:</b> The page content at the hyperlink is behind paywall. </p>\
                            </div>\
                            <hr style="height:7pt; margin:0; border: 0; visibility:hidden;">\
                            <b style="margin-left: -30px">Example 3</b>\
                            <li>\
                            <b>Factual claim:</b> If we stopped testing right now, we\'d have very few cases, if any.<br>\
                        </li>\
                        <li> \
                            <b>Tweet:</b> "If we stopped testing, we\'d have very few cases." - Donald Trump    He\'s right \
                            </li>\
                            <div style="padding-left:5px; color:#31708f">\
                                <b>Explanation:</b> The tweet is created for sarcasm. </p>\
                            </div>\
                            </ul>\
                        </div>\
                        <hr style="height:7pt; margin:0; border: 0; visibility:hidden;" />\
                    </li>\
                </div>\
            </ul>\
        </div>\
        </div> \
	</p>'
    );
    // There will be 16 training questions at the beginning. We will show the correct answer after you submit your response. The actual data collection will start after you are done with the initial 16 training questions. <br><br>\
    // Please feel free to use the <b>"Feedback"</b> button (to the right of the browser window) to inform us your suggestions and/or report errors. \
    // You can also contact us at <a href="mailto:idirlab@uta.edu">idirlab@uta.edu</a>. Thanks! \

    container.append(
        '<div class="center-align"><input id="input_agree" type="checkbox"><label><b>I have carefully read and understood all the instructions.</b></label><br><br><button id="button_start_survey" type="button" class="btn btn-primary btn-lg" disabled>Start</button><br><br> </div>'
    );

    $("input").iCheck({
        checkboxClass: "icheckbox_square-blue",
        radioClass: "iradio_square-blue",
        uncheckedCheckboxClass: "hover",
    });

    $("#button_start_survey").click(function () {
        console.log("click button_start_survey");
        start_training();
    });

    $("#input_agree").on("ifChecked", function (event) {
        $("#button_start_survey").prop("disabled", false);
    });

    $("#input_agree").on("ifUnchecked", function (event) {
        $("#button_start_survey").prop("disabled", true);
    });
}

function start_training() {
    $.ajax({
        url: "get_training_index.php",
        method: "POST",
        dataType: "text",
        success: function (data) {
            console.log("get_training_index.php returned: ", data);
            if (data == 0) {
                is_training = 0;
            } else {
                training_index = data.split("^")[1];
                data = data.split("^")[0];
            }
            get_sentence(data);
        },
    });
}

function show_sentence(
    sentence_id,
    sentence,
    REGION,
    ANSWERED_message,
    QUALITY_message,
    PAYMENT_message,
    RANK_message,
    total_message,
    tweet_id,
    tweet,
    tweet_timestamp
) {
    var container = $(".jumbotron .container").first();
    container.empty();
    container.append(
        '<br><div id="top_well" class="well message highlight"><span id="span_REGION_status"></span></div><div class="panel panel-primary">  \
						<div class="panel-body"><div id="div_sentence" class="well">' +
            sentence +
            '</div> \
						<div class="panel-title">What is the truthfulness stance of the tweet towards the factual claim?</div><br>  \
						<ul class="list-group"> \
						<li class="list-group-item"> \
							<input type="radio" name="iCheck" id="1"><label>' +
            text_response["1"] +
            '</label> \
						</li> \
						<li class="list-group-item"> \
							<input type="radio" name="iCheck" id="0"><label>' +
            text_response["0"] +
            '</label> \
						</li> \
						<li class="list-group-item"> \
							<input type="radio" name="iCheck" id="-1"><label>' +
            text_response["-1"] +
            '</label> \
						</li> \
						<li class="list-group-item"> \
							<input type="radio" name="iCheck" id="2"><label>' +
            text_response["2"] +
            '</label> \
						</li> \
                        <li class="list-group-item"> \
							<input type="radio" name="iCheck" id="3"><label>' +
            text_response["3"] +
            '</label> \
						</li> \
						</ul></div><div class="panel-footer"><button id="button_submit_answer" type="button" class="btn btn-primary">Submit</button>&nbsp&nbsp \
						<button id="button_skip" type="button" class="btn btn-info">' +
            text_response["-2"] +
            '\
            </button> \
            <button id="button_previous_answers" type="button" class="btn btn-primary pull-right">Modify My Previous Responses</button></div></div> \
            <div class="well tips justified_para" style = "font-size: 140% !important;"> \
            <span id="span_tips"> \
                <h3>Tips for improving your work quality score!</h3>\
                <ul class="list-unstyled">\
                    <li>(1) Carefully examine each pair of factual claim and tweet.</li>\
                    <li>(2) Contextual information (such as the fact-check summary, claimant information, hyperlink title and content) may help you form answers.</li>\
                    <li>(3) Review the <a onclick="load_survey_instructions()" id="button_load_survey_instructions" style="cursor:pointer;">instructions</a> to understand the examples.</li>\
                    <li>(4) Don\'t guess. Skip the pairs that you are not sure about.</li>\
                    <li>(5) Modify previous responses if necessary.</li>\
                    <li>(6) You may be tempted to pick easy/short claims to work on by clicking "Skip this pair". Keep in mind that our work quality calculation formula has a component that accounts for the length/complexity of claims as well as how many pairs are skipped. We discourage excessive skipping. Nevertheless, if you are not confident about a question, it is still better to skip, because every single mistake will lower your work quality score.</li>\
                </ul>\
                \
                <br>Whenever you make one mistake, our algorithm lowers your work quality score, which means you get less points for every pair you have annotated. It takes multiple correct answers to make up for every single mistake and get the work quality score back to the previous value. If your current work quality score is 0 or very low, it is because our algorithm detected many mistakes in your answers. The best thing to do is to review your answers and modify them if necessary. If your work quality score is 0, it might actually be negative internally. If you continue to answer new questions, it will take MANY questions before you can see positive and improving work quality score.\
                <br><br>If you have only annotated 50-150 pairs, the work quality score based on the small sample may not reflect your true work quality. It will become more robust once you have annotated more pairs.\
            </span>\
            <br><br><br>\
        </div>'
    );

    $("input").iCheck({
        checkboxClass: "icheckbox_square-blue",
        radioClass: "iradio_square-blue",
        increaseArea: "20%", // optional
    });

    $("#button_submit_answer").on("click", function () {
        // scroll to the top of the page
        $("html, body").animate({ scrollTop: 0 }, "slow");
        context_seen = 0;
        post_response(sentence_id, context_seen);
    });

    $("#button_skip").on("click", function () {
        context_seen = 0;
        skip_sentence(sentence_id, context_seen);
    });

    $("#button_previous_answers").on("click", function () {
        get_previous_answers(sentence_id);
    });

    //update_region_status(REGION);
    update_payrate_message(
        ANSWERED_message,
        QUALITY_message,
        PAYMENT_message,
        RANK_message,
        total_message
    );
    $("body").scrollTop(0);

    if (is_training == 1) {
        $("#button_previous_answers").hide();
        $("#button_skip").hide();
    } else {
        $("#button_previous_answers").show();
        $("#button_skip").show();
    }

    function generate_dummy_user() {
        let dummyUser = faker.name.findName();
        let dummyHandle = faker.internet.userName();
        return [dummyUser, dummyHandle];
    }
    // generate dummy username and handle
    let dummy = generate_dummy_user();
    let dummyUser = dummy[0];
    let dummyHandle = dummy[1];
    // format the date from 2022-07-20T03:41:07.000Z to 3:41 PM · July 20, 2022
    let date = new Date(tweet_timestamp).toLocaleDateString();
    console.log(dummyUser, dummyHandle, date);
    load_tweet_into_div(tweet_id, tweet, dummyUser, dummyHandle, date);
}

function load_tweet_into_div(
    tweet_id,
    tweet,
    dummyUser,
    dummyHandle,
    date,
    attempt = 0
) {
    twttr.ready(function loadTweet(twttr) {
        const fetchData = twttr.widgets
            .createTweet(tweet_id, document.getElementById("twitter-tweet"), {
                linkColor: "#55acee",
                theme: "light",
            })
            .then(function (result) {
                // Display a loading image while the tweet is being loaded
                // document.getElementById("twitter-tweet").innerHTML = `
                // <div id="loading_img" style="text-align: center; margin-top: 50px;">
                //     <img src="image/loading.gif" alt="Loading..." style="width: 50px; height: 50px;">
                // </div>
                // `;
                // if the result is undefined, then call the loadTweet function again
                if (result == undefined) {
                    if (attempt < 4) {
                        load_tweet_into_div(
                            tweet_id,
                            tweet,
                            dummyUser,
                            dummyHandle,
                            date,
                            attempt + 1
                        );
                    } else {
                        document.getElementById(
                            "twitter-tweet"
                        ).innerHTML = `<div style="border: 1px solid #e1e8ed; border-radius: 10px; padding: 15px; max-width: 600px; margin: auto; font-family: Arial, sans-serif;background-color: #FFFFFF">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center;">
                                <img src="https://abs.twimg.com/sticky/default_profile_images/default_profile_normal.png" style="border-radius: 50%; width: 48px; height: 48px;">
                                <div style="margin-left: 10px;">
                                    <b style="font-size: 15px;">${dummyUser}</b> 
                                    <span style="color: #657786; font-size: 15px;">${dummyHandle}</span> 
                                </div>
                                </div>
                                <button style="background-color: #1da1f2; color: white; border: none; border-radius: 15px; padding: 5px 10px; font-size: 14px; cursor: pointer;">Follow</button>
                            </div>
                            
                            <p style="margin: 10px 0 15px; font-size: 15px; color: #0f1419; line-height: 20px;">
                                ${tweet}
                            </p>
                            
                            <p style="color: #657786; font-size: 13px;">${date}</p>
                            </div>`;
                    }
                }
            });
        function withTimeout(promise, timeout) {
            // Create a promise that rejects after the given timeout
            const timeoutPromise = new Promise((_, reject) => {
                const timer = setTimeout(() => {
                    reject(new Error("Promise timed out"));
                    document.getElementById(
                        "twitter-tweet"
                    ).innerHTML = `<div style="border: 1px solid #e1e8ed; border-radius: 10px; padding: 15px; max-width: 600px; margin: auto; font-family: Arial, sans-serif;background-color: #FFFFFF">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center;">
                            <img src="https://abs.twimg.com/sticky/default_profile_images/default_profile_normal.png" style="border-radius: 50%; width: 48px; height: 48px;">
                            <div style="margin-left: 10px;">
                                <b style="font-size: 15px;">${dummyUser}</b> 
                                <span style="color: #657786; font-size: 15px;">${dummyHandle}</span> 
                            </div>
                            </div>
                            <button style="background-color: #1da1f2; color: white; border: none; border-radius: 15px; padding: 5px 10px; font-size: 14px; cursor: pointer;">Follow</button>
                        </div>
                        
                        <p style="margin: 10px 0 15px; font-size: 15px; color: #0f1419; line-height: 20px;">
                            ${tweet}
                        </p>
                        
                        <p style="color: #657786; font-size: 13px;">${date}</p>
                        </div>`;
                }, timeout);

                // Clear the timer if the original promise resolves or rejects
                promise.finally(() => clearTimeout(timer));
            });

            // Race the original promise against the timeout promise
            return Promise.race([promise, timeoutPromise]);
        }
        withTimeout(fetchData, 3000)
            .then((data) => console.log(data))
            .catch((err) => console.error(err.message));
    });
}

function update_region_status(REGION) {
    switch (parseInt(REGION)) {
        case 1:
            $("#span_REGION_status").css("background-color", "#66FF66");
            $("#span_REGION_status").text(
                "You are doing an amazing job. Please keep up the good work."
            );
            break;
        case 2:
            //		$('#span_REGION_status').text("You are doing amazing. Please keep up the good work!");
            break;
        case 3:
            $("#span_REGION_status").css("background-color", "#FFFFCC");
            $("#span_REGION_status").text(
                'Our tool detected that the quality of your responses is not high. If the quality falls below a certain threshold, we won\'t be able to consider your response for compensation. You can modify your previous responses, in order to improve the quality. Please spend more time on each sentence and read it more carefully. If you unsure about the answer, use the "skip" button.'
            );
            break;
        case 4:
            $("#span_REGION_status").css("background-color", "#FF6666");
            $("#span_REGION_status").text(
                'Our tool detected that the quality of your responses is poor. If it cannot be improved, we won\'t be able to consider your response for compensation. In order to improve the quality, please modify your previous responses. Please spend more time on each sentence and read it more carefully. If you unsure about the answer, use the "skip" button.'
            );
            break;
        default:
            $("#span_REGION_status").text("");
    }
}

function update_payrate_message(
    ANSWERED_message,
    QUALITY_message,
    PAYMENT_message,
    RANK_message,
    total_message
) {
    console.log("answer message: " + ANSWERED_message);
    console.log("rank message: " + RANK_message);
    if (is_training == 1) {
        var remaining = 16 - training_index;
        $("#span_REGION_status").html(
            "Training Question: " +
                training_index +
                " (remaining " +
                remaining +
                ")<br>This is the training phase. Correct answer and explanation will be shown in this panel. The system only starts to calculate the leaderboard and work quality after the training phase is over."
        );
        $("#top_well").addClass("highlight");
        return;
    }
    if (ANSWERED_message == null) {
        $("#button_leaderboard").prop("disabled", true);
        $("#span_REGION_status").html(
            "The <b>leaderboard</b> is enabled after your initial 50 pairs. After that, this message and the <b>leaderboard</b> are updated every 15-30 pairs. </b>See tips for improving your work quality at the bottom of this page.</b>"
        );
    } else if (ANSWERED_message >= 5 && RANK_message != null) {
        $("#button_leaderboard").prop("disabled", false);
        $("#span_REGION_status").html(
            "Your estimated total points are " +
                PAYMENT_message.toString() +
                ". Your work quality score is approximately " +
                QUALITY_message.toString() +
                ", what are the points receive per pair on average. Your total points are ranked " +
                RANK_message.toString() +
                " out of " +
                total_message.toString() +
                " participants. Click 'Leaderboard' to see details. This message and the leaderboard are updated every 15-30 pairs. <b>See tips for improving your work quality score at the bottom of this page.</b>"
        );
    } else {
        $("#button_leaderboard").prop("disabled", true);
        $("#span_REGION_status").html(
            "The <b>leaderboard</b> is enabled after your initial 50 pairs. After that, this message and the <b>leaderboard</b> are updated every 15-30 pairs. </b>See tips for improving your work quality at the bottom of this page.</b>"
        );
    }
}

function get_sentence(sentence_id) {
    get_answer_count();
    $("input").iCheck("enable");
    sentence_id = typeof sentence_id !== "undefined" ? sentence_id : 0;
    $.ajax({
        url: "get_sentence.php",
        method: "POST",
        dataType: "text",
        data: { sentence_id: sentence_id },
        success: function (data) {
            if (data.localeCompare("-1") == 0) {
                alert(
                    "No more pairs available for you. Thanks for your participation. We may notify you in case there is a third phase of the data collection."
                );
                get_previous_answers(0);
                return;
            }
            data = jQuery.parseJSON(data);
            tweet_url_title = data.tweet_url_title;
            if (
                data["tweet_url_title"] == null ||
                data["tweet_url_title"] == "[]" ||
                data["tweet_url_title"] == "" ||
                data["tweet_url_title"].length <= 13
            ) {
                data["tweet_url_title"] = "No link available";
            } else {
                // split the string into an array by the delimiter "!@#"
                var arr = data["tweet_url_title"].split("!@#$");
                // create a link tag for each element in the array
                var html = "";
                for (var i = 0; i < arr.length; i += 2) {
                    // add text to display the arr[i]
                    html +=
                        '<span style="overflow-wrap: break-word; word-break: break-word;">' +
                        arr[i] +
                        ":</span>";
                    html += "<br>";
                    html +=
                        "<a href='" +
                        arr[i] +
                        "' target='_blank'>" +
                        arr[i + 1] +
                        "</a><hr>";
                }
                data["tweet_url_title"] = html;
            }
            sentence_html =
                "\
				<table style = 'width: 100%; table-layout: fixed;'>\
					<tr>\
						<td style = 'width: 50%; vertical-align: top; border-left: 6px solid rgb(255,0,68); padding: 0px 15px;'>\
                            <img src='image/PolitiFact_logo.png' style = 'width: 45%; height: auto;  margin-left: 1px;'>\
                            <br><small style='text-align:left;  !important'><a href='" +
                data["factcheck_url"] +
                "' target='_blank'>Go to the fact-check</a></small><br>\
							<a href='" +
                data["factcheck_author_url"] +
                "' target='_blank'><h3>" +
                data["claim_author"] +
                "</h3></a>\
							<a href='" +
                data["factcheck_author_url"] +
                "' target='_blank'><small style='text-align:left;  color:rgb(128,129,130);  !important'>" +
                data["factcheck_post_time"] +
                "</small></a><br>\
                            <br><big>" +
                data.claim +
                "</big><br>\
                            <br><br><b style='color:rgb(69,121,178);'>PolitiFact rating and ruling summary</b><br>\
							<img src=image/" +
                data.claim_verdict +
                ".jpg alt=" +
                data.claim_verdict +
                " style=' width: 25%; height: auto; margin-right: 15px; margin-bottom: 15px;'>\
							<div style='height: 200px; padding: 10px; overflow-y: auto; border: 1px solid #ccc;'>\
								<small style='vertical-align: top; height: 100%; color:rgb(60,60,60);'>" +
                data.claim_review +
                "</small>\
							</div>\
                            <br><b style='color:rgb(69,121,178);'>Claimant info</b><br><small>" +
                data.factcheck_author_info +
                " <i>&Lt;Retrieved from PolitiFact&Gt;</i> </small><br>\
						</td>\
						<td style = 'width: 50%; vertical-align: top; border-left: 6px solid #0099eb; padding-left: 15px;'>\
                            <img src='image/twitter.png' style = 'width: 15%; height: auto;  margin-left: 1px;'>\
                            <div style='position: relative; width: 100%; padding-bottom: 120%;'>\
                                <div id='twitter-tweet' style='position: absolute; top: 0; left: 0; width: 100%; height: 100%;'></div>\
                            </div>\
                            <br><b style='color:rgb(69,121,178);'>Hyperlinks and titles in the tweet</b><br><small>" +
                data["tweet_url_title"] +
                "</small><br>\
						</td>\
					</tr>\
				</table>\
			\
			";
            // console.log(data);
            show_sentence(
                data.id,
                sentence_html,
                data.REGION,
                data.ANSWERED_message,
                data.QUALITY_message,
                data.PAYMENT_message,
                data.RANK_message,
                data.total_message,
                data.tweet_id,
                data.tweet,
                data.tweet_timestamp
            );
        },
    });
}

function skip_sentence(sentence_id, context_seen) {
    $.ajax({
        url: "set_response.php",
        method: "POST",
        data: {
            sentence_id: sentence_id,
            response: "-2",
            context_seen: context_seen,
        },
        dataType: "text",
        success: function (data) {
            get_sentence(0);
        },
    });
}

function post_response(sentence_id, context_seen) {
    if ($("input[type=radio]:checked").size() == 0) {
        alert("Please select one option.");
        return;
    }
    $("input").iCheck("disable");
    $("#button_submit_answer").prop("disabled", true);
    $.ajax({
        url: "set_response.php",
        method: "POST",
        data: {
            sentence_id: sentence_id,
            response: $("input[type=radio]:checked")[0].id,
            context_seen: context_seen,
        },
        dataType: "text",
        success: function (data) {
            if (is_training == 1) {
                show_training_message(sentence_id);
            } else get_sentence(0);
        },
    });
}

function show_training_message(sentence_id) {
    $.ajax({
        url: "set_training_index.php",
        method: "POST",
        data: {
            sentence_id: sentence_id,
            response: $("input[type=radio]:checked")[0].id,
        },
        dataType: "text",
        success: function (data) {
            data = data.split("^");
            console.log("show_training_message:", data);
            $("#top_well").removeClass("highlight");
            if (data[1].localeCompare("Correct!") == 0) {
                $("#top_well").addClass("correct");
            } else {
                $("#top_well").addClass("wrong");
            }
            $("#span_REGION_status").html(
                data[1] +
                    "<br>" +
                    data[0] +
                    "<br>" +
                    '<button id="button_next" type="button" class="btn btn-primary pull-left">' +
                    "Next" +
                    "</button>&nbsp&nbsp"
            );

            $("#button_next").on("click", function () {
                if (data[2].localeCompare("16") == 0) {
                    $("#top_well").removeClass("correct wrong");
                    $("#span_REGION_status").html(
                        "You have completed the training phase. You have made " +
                            data[3] +
                            " mistakes. In actual data collection mode, you would have earned $" +
                            data[4] +
                            ". The actual data collection phase will start now." +
                            "<br>" +
                            '<button id="button_next" type="button" class="btn btn-primary pull-left" onClick="start_training();">' +
                            "Start" +
                            "</button>&nbsp&nbsp"
                    );
                } else {
                    console.log("Start training");
                    start_training();
                }
            });
        },
    });
}

function get_previous_answers(sentence_id) {
    $('[data-toggle="popover"]').popover("hide");
    $.ajax({
        url: "get_previous_answers.php",
        method: "POST",
        dataType: "text",
        data: { sentence_id: sentence_id },
        success: function (data) {
            console.log("get_previous_answers", data);
            data = jQuery.parseJSON(data);
            var container = $(".jumbotron .container").first();
            container.empty();
            container.append(
                '<br><br><div class="row"><div class="col-md-12"><button id="button_go_back" class="btn btn-primary">Go Back</button></br><h2>Previous Answers </h2><h4>(latest responses are at the top)</h4></div></div>'
            );

            container.append('<ul id="selectable" class="list-group"></ul>');
            for (i = 0; i < data.length; i++) {
                $("#selectable").append(
                    '<li class="list-group-item"><div class="row"><div class="col-md-12"><b>Factual claim</b>:' +
                        data[i].claim +
                        "</br><b>Tweet</b>: " +
                        data[i].tweet +
                        '</div></div><br><div class="row"><div class="col-md-1"><button id="button_change_' +
                        data[i].id +
                        '"class="btn btn-primary">Change</button></div><div class="col-md-11">' +
                        "Your Response at " +
                        data[i].time +
                        ": " +
                        text_response[data[i].response] +
                        "</div></div></li><br>"
                );

                $("#button_change_" + data[i].id).click(function () {
                    get_sentence(parseInt(this.id.split("_")[2]));
                });
            }

            $("#button_go_back").click(function () {
                get_sentence(sentence_id * -1); //restore to last sentence after go back
            });
        },
    });
}

function send_feedback() {
    $.ajax({
        url: "send_feedback.php",
        method: "POST",
        data: {
            feedback: $("#textarea_feedback").val(),
            feedback_type: $("#button_feedback_type").text(),
        },
        dataType: "text",
        success: function (data) {
            $("#feedback_modal").modal("hide");
        },
    });
}

// add event listener to the enter button
document.addEventListener("keydown", function (event) {
    if (event.keyCode == 13) {
        // if navbar_signin_signup is displayed, then click button_sign_in button
        if ($("#navbar_signin_signup").is(":visible")) {
            $("#button_sign_in").click();
        } else {
            // if button_start_survey/button_submit_answer exists, then click it
            if ($("#button_start_survey").length > 0) {
                $("#button_start_survey").click();
            } else if ($("#button_submit_answer").length > 0) {
                $("#button_submit_answer").click();
            }
        }
        // prevent other default enter button behavior
        event.preventDefault();
    }
});
