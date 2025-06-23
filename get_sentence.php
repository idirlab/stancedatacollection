<?php

function randomFloat($min = 0, $max = 1)
{
    return $min + (mt_rand() / mt_getrandmax()) * ($max - $min);
}

session_start(["cookie_lifetime" => 86400]);
$username = $_SESSION["username"];
$sentence_id = $_REQUEST["sentence_id"];
$min_number_of_users = 2;
include_once "db.php";

// checking if the user has any sentence available to label.
$sql = gen_select_query(
    ["sentence_id"],
    ["Sentence_User"],
    ["username = " . $username]
);
$results = execute($sql, [], PDO::FETCH_COLUMN);
if (count($results)) {
    $already_answered = "(" . implode(", ", $results) . ")";
} else {
    $already_answered = "(-1)";
}

$training_sentences = "(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16)";

$top_participants_sql =
    'select Sentence_User.username as USERNAME	
			from
				Sentence_User,
				Sentence
			where
				id = sentence_id and
				response != -2 and
				sentence_id not in ' .
    $training_sentences .
    '
				group by Sentence_User.username
			having 
            -0.20*	(	sum(if(screening = 0 and response = 0, 1, 0))+ sum(if(screening = 1 and response = 1, 1, 0))+ sum(if(screening = 2 and response = 2, 1, 0))+ sum(if(screening = 3 and response = 3, 1, 0))+ sum(if(screening = -1 and response = -1, 1, 0)) ) / ( sum(screening != -3 and response != -2))
            +0.50 *	(	sum(if(screening = 0 and response = 3, 1, 0))+ sum(if(screening = 1 and response = 0, 1, 0))+ sum(if(screening = 1 and response = 2, 1, 0))+ sum(if(screening = 2 and response = 1, 1, 0))+ sum(if(screening = 2 and response = 3, 1, 0))+ sum(if(screening = 3 and response = 2, 1, 0))+ sum(if(screening = 3 and response = -1, 1, 0))+ sum(if(screening = -1 and response = 3, 1, 0)) ) / ( sum(screening != -3 and response != -2))
            +0.50*	(	sum(if(screening = 0 and response = 2, 1, 0))+ sum(if(screening = 1 and response = 3, 1, 0))+ sum(if(screening = 2 and response = 0, 1, 0))+ sum(if(screening = 2 and response = -1, 1, 0))+ sum(if(screening = 3 and response = 1, 1, 0))+ sum(if(screening = -1 and response = 2, 1, 0))+ sum(if(screening = 3 and response = 0, 1, 0))+ sum(if(screening = -1 and response = 0, 1, 0)) ) / (sum(screening != -3 and response != -2))
            +1.00*	(	sum(if(screening = 0 and response = 1, 1, 0))+ sum(if(screening = 0 and response = -1, 1, 0)) ) / (sum(screening != -3 and response != -2))
            +2.00*	(	sum(if(screening = 1 and response = -1, 1, 0))+ sum(if(screening = -1 and response = 1, 1, 0)) ) / (sum(screening != -3 and response != -2)) <= 0.0 and count(*) >= 50';

$top_participants = execute($top_participants_sql, [], PDO::FETCH_COLUMN);
$top_participants_string = '("' . implode('","', $top_participants) . '")';

$top_quality_sentences_sql =
    'select sentence_id from (
        select sentence_id, 
                sum(if(response = -1, 1, 0)) as Label_0, 
                sum(if(response = 0, 1, 0)) as Label_1,	
                sum(if(response = 1, 1, 0)) as Label_2,	
                sum(if(response = 2, 1, 0)) as Label_3,	
                sum(if(response = 3, 1, 0)) as Label_4
        from Sentence_User, Sentence 
        where Sentence.id = Sentence_User.sentence_id 
            and screening = -3
            and username in ' .
    $top_participants_string .
    'group by sentence_id
        having		(Label_0 >= 3 and	Label_0 >= 2+Label_1	and	Label_0 >= 2+Label_2	and	Label_0 >= 2+Label_3	and	Label_0 >= 2+Label_4 	and	Label_0 >= round((Label_1 + Label_2 + Label_3) / 2, 1) ) or
        (Label_1 >= 3 and	Label_1 >= 2+Label_0	and	Label_1 >= 2+Label_2	and	Label_1 >= 2+Label_3	and	Label_1 >= 2+Label_4 	and	Label_1 >= round((Label_0 + Label_2 + Label_3) / 2, 1) ) or
        (Label_2 >= 3 and	Label_2 >= 2+Label_0	and	Label_2 >= 2+Label_1	and	Label_2 >= 2+Label_3	and	Label_2 >= 2+Label_4 	and	Label_2 >= round((Label_0 + Label_1 + Label_3) / 2, 1) ) or
        (Label_3 >= 3 and	Label_3 >= 2+Label_0	and	Label_3 >= 2+Label_1	and	Label_3 >= 2+Label_2	and	Label_3 >= 2+Label_4 	and	Label_3 >= round((Label_0 + Label_1 + Label_2) / 2, 1) ) or
        (Label_4>0)) A';

$top_quality_sentences = execute(
    $top_quality_sentences_sql,
    [],
    PDO::FETCH_COLUMN
);
$top_quality_sentences_string =
    '("' . implode('","', $top_quality_sentences) . '")';

// select pairs that not answered and not in top quality pairs
$sql = gen_select_query(
    [
        "Sentence.id",
        "Sentence.tweet",
        "Sentence.tweet_id",
        "Sentence.claim",
        "Sentence.claim_author",
        "Sentence.claim_timestamp",
        "Sentence.tweet_timestamp",
        "Sentence.factcheck_url",
        "Sentence.claim_verdict",
        "Sentence.claim_review",
        "Sentence.tweet_url_title",
        "Sentence.factcheck_author_url",
        "Sentence.factcheck_post_time",
        "Sentence.factcheck_author_info",
    ],
    ["Sentence"],
    // array('Sentence.id NOT IN '.$already_answered, 'Sentence.id NOT IN '.$top_quality_sentences_string, 'screening = -3'),
    [
        "Sentence.id NOT IN " . $already_answered,
        "Sentence.id NOT IN " . $top_quality_sentences_string,
        "Sentence.subset in (0, 1, 2, 3, 4, 5, 6, 7, 8, 9)",
        "Sentence.is_active = 1",
        "Sentence.screening = -3",
    ],
    [],
    ["RAND()"],
    ["1"]
);
$results = execute($sql, [], PDO::FETCH_ASSOC);

//No more sentence available for the user.
if (!count($results) && $sentence_id == 0) {
    $activity_sql = gen_insert_query(
        $tables = ["Activity"],
        $fields = ["username", "time", "action"],
        $values = [
            $username,
            '"' . date("Y-m-d H:i:s") . '"',
            '"NO MORE SENTENCE AVAILABLE"',
        ]
    );
    $activity_sql_results = execute($activity_sql, [], PDO::FETCH_ASSOC);
    echo "-1";
    exit();
}

$is_screening = 0;
// sentence_id not mentioned. Not a 'Change' or 'Go Back' scenario.
if ($sentence_id == 0) {
    // The dynamic screening limit is calculated based on the user’s total number of answered pairs, with a minimum threshold of 10% and a maximum threshold of 30%. This limit is set to a minimum of 10% when the user has answered a maximum of 250 pairs or more.
    $screening_rate_min = 0.1; // minimum screening rate
    $screening_rate_max = 0.3; // maximum screening rate
    $answered_pairs_max = 250; // # of answered pairs to reach the minimum screening rate
    $screening_limit =
        $screening_rate_max -
        (($screening_rate_max - $screening_rate_min) / $answered_pairs_max) *
            min($answered_pairs_max, intval($_SESSION["ANSWERED_message"]));
    $_SESSION["screening_limit"] = $screening_limit;
    $_SESSION["tmp"] = min(
        $answered_pairs_max,
        intval($_SESSION["ANSWERED_message"])
    );

    $is_screening = randomFloat() <= $screening_limit ? 1 : 0;
    $_SESSION["answered"] = $_SESSION["answered"] + 1;
    if ($_SESSION["answered"] % 10 == 0) {
        if ($_SESSION["screening_questioned"] == 0) {
            $is_screening = 1;
        }
        $_SESSION["answered"] = 0;
        $_SESSION["screening_questioned"] = 0;
    } elseif ($_SESSION["answered"] % 10 != 0) {
        if ($is_screening == 1) {
            $_SESSION["screening_questioned"] = 1;
        }
    }
    # screening question
    if ($is_screening == 1 && strcmp($username, '"factchecker"') != 0) {
        $class_screening = randomFloat(0, 100);
        // 17+17+43+17+6
        if ($class_screening <= 17) {
            $class_screening = "-1";
        } elseif ($class_screening <= 34) {
            $class_screening = "0";
        } elseif ($class_screening <= 77) {
            $class_screening = "1";
        } elseif ($class_screening <= 94) {
            $class_screening = "2";
        } elseif ($class_screening <= 100) {
            $class_screening = "3";
        }

        $sql = gen_select_query(
            [
                "Sentence.id",
                "Sentence.tweet",
                "Sentence.tweet_id",
                "Sentence.claim",
                "Sentence.claim_author",
                "Sentence.claim_timestamp",
                "Sentence.tweet_timestamp",
                "Sentence.factcheck_url",
                "Sentence.claim_verdict",
                "Sentence.claim_review",
                "Sentence.tweet_url_title",
                "Sentence.factcheck_author_url",
                "Sentence.factcheck_post_time",
                "Sentence.factcheck_author_info",
            ],
            ["Sentence"],
            [
                "screening = " . $class_screening,
                "Sentence.id not in " . $_SESSION["training_sentences"] . " ",
                "Sentence.is_active = 1",
            ],
            [],
            ["RAND()"],
            ["1"]
        );
    } else {
        #regular question
        $sql = gen_select_query(
            ["sentence_id"],
            ["Sentence_User"],
            ["username = " . $username]
        );
        $results = execute($sql, [], PDO::FETCH_COLUMN);

        if (count($results)) {
            $already_answered = "(" . implode(", ", $results) . ")";
        } else {
            $already_answered = "(-1)";
        }

        $sql =
            'select Sentence_User.username as USERNAME	
					from
						Sentence_User,
						Sentence
					where
						id = sentence_id and
						username not in ("cmavs2015", "sakiforu", "teaphony") and
						response != -2 and
						sentence_id not in ' .
            $training_sentences .
            '
					group by Sentence_User.username
					having 
                    -0.20*	(	sum(if(screening = 0 and response = 0, 1, 0))+ sum(if(screening = 1 and response = 1, 1, 0))+ sum(if(screening = 2 and response = 2, 1, 0))+ sum(if(screening = 3 and response = 3, 1, 0))+ sum(if(screening = -1 and response = -1, 1, 0)) ) / ( sum(screening != -3 and response != -2))
                    +0.50 *	(	sum(if(screening = 0 and response = 3, 1, 0))+ sum(if(screening = 1 and response = 0, 1, 0))+ sum(if(screening = 1 and response = 2, 1, 0))+ sum(if(screening = 2 and response = 1, 1, 0))+ sum(if(screening = 2 and response = 3, 1, 0))+ sum(if(screening = 3 and response = 2, 1, 0))+ sum(if(screening = 3 and response = -1, 1, 0))+ sum(if(screening = -1 and response = 3, 1, 0)) ) / ( sum(screening != -3 and response != -2))
                    +0.50*	(	sum(if(screening = 0 and response = 2, 1, 0))+ sum(if(screening = 1 and response = 3, 1, 0))+ sum(if(screening = 2 and response = 0, 1, 0))+ sum(if(screening = 2 and response = -1, 1, 0))+ sum(if(screening = 3 and response = 1, 1, 0))+ sum(if(screening = -1 and response = 2, 1, 0))+ sum(if(screening = 3 and response = 0, 1, 0))+ sum(if(screening = -1 and response = 0, 1, 0)) ) / (sum(screening != -3 and response != -2))
                    +1.00*	(	sum(if(screening = 0 and response = 1, 1, 0))+ sum(if(screening = 0 and response = -1, 1, 0)) ) / (sum(screening != -3 and response != -2))
                    +2.00*	(	sum(if(screening = 1 and response = -1, 1, 0))+ sum(if(screening = -1 and response = 1, 1, 0)) ) / (sum(screening != -3 and response != -2)) <= 0.0 and count(*) >= 50';

        $top_participants = execute($sql, [], PDO::FETCH_COLUMN);
        $top_participants_string =
            '("' . implode('","', $top_participants) . '")';

        $sql =
            'select sentence_id from (
						select sentence_id, 
								sum(if(response = -1, 1, 0)) as nfs, 
								sum(if(response = 0, 1, 0)) as ufs, 
								sum(if(response = 1, 1, 0)) as cfs
						from Sentence_User, Sentence 
						where Sentence.id = Sentence_User.sentence_id 
							and screening = -3
							and username in ' .
            $top_participants_string .
            'group by sentence_id
						having (nfs >= 3 and nfs >= 2+ufs and nfs >= 2+cfs)
							or (ufs >= 3 and ufs >= 2+nfs and ufs >= 2+cfs)
							or (cfs >= 3 and cfs >= 2+ufs and cfs >= 2+nfs)) A';

        $top_quality_sentences = execute($sql, [], PDO::FETCH_COLUMN);
        $top_quality_sentences_string =
            '("' . implode('","', $top_quality_sentences) . '")';
        $sql = gen_select_query(
            [
                "Sentence.id",
                "Sentence.tweet",
                "Sentence.tweet_id",
                "Sentence.claim",
                "Sentence.claim_author",
                "Sentence.claim_timestamp",
                "Sentence.tweet_timestamp",
                "Sentence.factcheck_url",
                "Sentence.claim_verdict",
                "Sentence.claim_review",
                "Sentence.tweet_url_title",
                "Sentence.factcheck_author_url",
                "Sentence.factcheck_post_time",
                "Sentence.factcheck_author_info",
            ],
            ["Sentence"],
            [
                "Sentence.id NOT IN " . $already_answered,
                "Sentence.id NOT IN " . $top_quality_sentences_string,
                "Sentence.subset in (0, 1, 2, 3, 4, 5, 6, 7, 8, 9)",
                "Sentence.screening = -3",
                "Sentence.is_active = 1",
            ],
            [],
            ["RAND()"],
            ["1"]
        );
    }
} else {
    // sentence_id mentioned; or in training stage
    $action = "'USER CLICKED CHANGE'";
    if ($sentence_id < 0) {
        $action = "'USER CLICKED GO BACK'";
        $sentence_id = $sentence_id * -1;
    }
    $activity_sql = gen_insert_query(
        $tables = ["Activity"],
        $fields = ["username", "time", "action", "sentence_id"],
        $values = [
            $username,
            '"' . date("Y-m-d H:i:s") . '"',
            $action,
            $sentence_id,
        ]
    );
    $activity_sql_results = execute($activity_sql, [], PDO::FETCH_ASSOC);

    $sql = gen_select_query(
        [
            "Sentence.id",
            "Sentence.claim",
            "Sentence.tweet",
            "Sentence.tweet_id",
            "Sentence.claim_author",
            "Sentence.claim_timestamp",
            "Sentence.tweet_timestamp",
            "Sentence.factcheck_url",
            "Sentence.claim_verdict",
            "Sentence.claim_review",
            "Sentence.tweet_url_title",
            "Sentence.factcheck_author_url",
            "Sentence.factcheck_post_time",
            "Sentence.factcheck_author_info",
        ],
        ["Sentence"],
        ["Sentence.id = " . $sentence_id],
        [],
        [],
        []
    );
}

$prev_sql = $sql;
$results = execute($sql, [], PDO::FETCH_ASSOC);
// var_dump($results);
if (count($results)) {
    $sql =
        'select A.USERNAME, A.ANSWERED,
				(case when A.RANK_W <= 0.0 and A.ANSWERED >= 4 then 1
						when A.RANK_W <= 0.3 and A.ANSWERED >= 4  then 2
						when A.RANK_W <= 0.6 and A.ANSWERED >= 4  then 3
						when A.RANK_W > 0.6 and A.ANSWERED >= 4  then 4
						else 0 end) as REGION
				from (select Sentence_User.username as USERNAME, 
							round(-0.20*	(	sum(if(screening = 0 and response = 0, 1, 0))+ sum(if(screening = 1 and response = 1, 1, 0))+ sum(if(screening = 2 and response = 2, 1, 0))+ sum(if(screening = 3 and response = 3, 1, 0))+ sum(if(screening = -1 and response = -1, 1, 0)) ) / ( sum(screening != -3 and response != -2))
                            +0.50 *	(	sum(if(screening = 0 and response = 3, 1, 0))+ sum(if(screening = 1 and response = 0, 1, 0))+ sum(if(screening = 1 and response = 2, 1, 0))+ sum(if(screening = 2 and response = 1, 1, 0))+ sum(if(screening = 2 and response = 3, 1, 0))+ sum(if(screening = 3 and response = 2, 1, 0))+ sum(if(screening = 3 and response = -1, 1, 0))+ sum(if(screening = -1 and response = 3, 1, 0)) ) / ( sum(screening != -3 and response != -2))
                            +0.50*	(	sum(if(screening = 0 and response = 2, 1, 0))+ sum(if(screening = 1 and response = 3, 1, 0))+ sum(if(screening = 2 and response = 0, 1, 0))+ sum(if(screening = 2 and response = -1, 1, 0))+ sum(if(screening = 3 and response = 1, 1, 0))+ sum(if(screening = -1 and response = 2, 1, 0))+ sum(if(screening = 3 and response = 0, 1, 0))+ sum(if(screening = -1 and response = 0, 1, 0)) ) / (sum(screening != -3 and response != -2))
                            +1.00*	(	sum(if(screening = 0 and response = 1, 1, 0))+ sum(if(screening = 0 and response = -1, 1, 0)) ) / (sum(screening != -3 and response != -2))
                            +2.00*	(	sum(if(screening = 1 and response = -1, 1, 0))+ sum(if(screening = -1 and response = 1, 1, 0)) ) / (sum(screening != -3 and response != -2)), 3) as RANK_W,	
							count(*) as ANSWERED
						from
							Sentence_User,
							Sentence,
							User
						where id = sentence_id and
							Sentence_User.username = User.username and
							Sentence_User.username = ' .
        $username .
        ' and
							Sentence_User.response != -2 and
							screening != -3) A';

    $results_status = execute($sql, [], PDO::FETCH_ASSOC);
    if (1) {
        //$results_status[0]['REGION'] != $_SESSION['REGION']// 1 means for every response, do update.
        $results[0]["REGION"] = $results_status[0]["REGION"];
    } else {
        $results[0]["REGION"] = 0;
    }
    $_SESSION["REGION"] = $results_status[0]["REGION"];
    $_SESSION["sentence_id"] = $results[0]["id"];

    $sql = 'select	USERNAME, 
    RANK_W, 
    round(A.avg_user/A.avg_total * A.ANSWERED * sign(0.3 - A.RANK_W)*pow((0.3 - A.RANK_W),2),2) AS RANK_L,
    if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) ),2), 0) as QUALITY,
    A.SKIP/A.ANSWERED as SKIP, 
    if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED),2), 0) as PAYRATE,
    if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED) * ANSWERED/100,2), 0) as PAYMENT,
    A.avg_user as LEN,
    answered as ANSWERED,    
    /*A.avg_total,*/
    pos, neu, neg, dif, prb, pos_pos, pos_neu, pos_neg, pos_dif, pos_prb, neu_pos, neu_neu, neu_neg, neu_dif, neu_prb, neg_pos, neg_neu, neg_neg, neg_dif, neg_prb, dif_pos, dif_neu, dif_neg, dif_dif, dif_prb, prb_pos, prb_neu, prb_neg, prb_dif, prb_prb, USERNAME as "USERNAME "
    FROM 	(SELECT		Sentence_User.username as USERNAME, 
    (SELECT avg(length(tweet))+avg(length(claim)) FROM Sentence WHERE id not in (select sentence_id from Training)) as avg_total,
                round	(-0.20*	(	sum(if(screening = 0 and response = 0, 1, 0))+ sum(if(screening = 1 and response = 1, 1, 0))+ sum(if(screening = 2 and response = 2, 1, 0))+ sum(if(screening = 3 and response = 3, 1, 0))+ sum(if(screening = -1 and response = -1, 1, 0)) ) / ( sum(screening != -3 and response != -2))
                +0.50 *	(	sum(if(screening = 0 and response = 3, 1, 0))+ sum(if(screening = 1 and response = 0, 1, 0))+ sum(if(screening = 1 and response = 2, 1, 0))+ sum(if(screening = 2 and response = 1, 1, 0))+ sum(if(screening = 2 and response = 3, 1, 0))+ sum(if(screening = 3 and response = 2, 1, 0))+ sum(if(screening = 3 and response = -1, 1, 0))+ sum(if(screening = -1 and response = 3, 1, 0)) ) / ( sum(screening != -3 and response != -2))
                +0.50*	(	sum(if(screening = 0 and response = 2, 1, 0))+ sum(if(screening = 1 and response = 3, 1, 0))+ sum(if(screening = 2 and response = 0, 1, 0))+ sum(if(screening = 2 and response = -1, 1, 0))+ sum(if(screening = 3 and response = 1, 1, 0))+ sum(if(screening = -1 and response = 2, 1, 0))+ sum(if(screening = 3 and response = 0, 1, 0))+ sum(if(screening = -1 and response = 0, 1, 0)) ) / (sum(screening != -3 and response != -2))
                +1.00*	(	sum(if(screening = 0 and response = 1, 1, 0))+ sum(if(screening = 0 and response = -1, 1, 0)) ) / (sum(screening != -3 and response != -2))
                +2.00*	(	sum(if(screening = 1 and response = -1, 1, 0))+ sum(if(screening = -1 and response = 1, 1, 0)) ) / (sum(screening != -3 and response != -2))
                , 3) as RANK_W,
            sum(if(Sentence_User.response != -2, 1, 0)) as ANSWERED,
            sum(if(Sentence_User.response = -2, 1, 0)) as SKIP,
            avg(if(Sentence_User.response != -2, length(tweet)+length(claim), null)) as avg_user, /*  LEN */
            sum(if(response = 1,1,0)) as pos,
            sum(if(response = 0,1,0)) as neu,
            sum(if(response = -1,1,0)) as neg,
            sum(if(response = 2,1,0)) as dif,
            sum(if(response = 3,1,0)) as prb,

            sum(if(screening = 1 and response = 1, 1, 0)) as pos_pos,
            sum(if(screening = 1 and response = 0, 1, 0)) as pos_neu,
            sum(if(screening = 1 and response = -1, 1, 0)) as pos_neg,
            sum(if(screening = 1 and response = 2, 1, 0)) as pos_dif,
            sum(if(screening = 1 and response = 3, 1, 0)) as pos_prb,

            sum(if(screening = 0 and response = 1, 1, 0)) as neu_pos,
            sum(if(screening = 0 and response = 0, 1, 0)) as neu_neu,
            sum(if(screening = 0 and response = -1, 1, 0)) as neu_neg,
            sum(if(screening = 0 and response = 2, 1, 0)) as neu_dif,
            sum(if(screening = 0 and response = 3, 1, 0)) as neu_prb,

            sum(if(screening = -1 and response = 1, 1, 0)) as neg_pos,
            sum(if(screening = -1 and response = 0, 1, 0)) as neg_neu,
            sum(if(screening = -1 and response = -1, 1, 0)) as neg_neg,
            sum(if(screening = -1 and response = 2, 1, 0)) as neg_dif,
            sum(if(screening = -1 and response = 3, 1, 0)) as neg_prb,

            sum(if(screening = 2 and response = 1, 1, 0)) as dif_pos,
            sum(if(screening = 2 and response = 0, 1, 0)) as dif_neu,
            sum(if(screening = 2 and response = -1, 1, 0)) as dif_neg,
            sum(if(screening = 2 and response = 2, 1, 0)) as dif_dif,
            sum(if(screening = 2 and response = 3, 1, 0)) as dif_prb,

            sum(if(screening = 3 and response = 1, 1, 0)) as prb_pos,
            sum(if(screening = 3 and response = 0, 1, 0)) as prb_neu,
            sum(if(screening = 3 and response = -1, 1, 0)) as prb_neg,
            sum(if(screening = 3 and response = 2, 1, 0)) as prb_dif,
            sum(if(screening = 3 and response = 3, 1, 0)) as prb_prb
        FROM		Sentence_User,
                    Sentence,
                    User
        WHERE		Sentence.id = sentence_id and
                    Sentence_User.username = User.username and
                    sentence_id not in (select sentence_id from Training) and 
                    Sentence_User.time >= "2024-06-23 17:59:59"
        GROUP BY Sentence_User.username) A 
    ORDER BY PAYMENT desc, ANSWERED desc;';

    if ($_SESSION["message_counter"] == 0) {
        $results_message = execute($sql, [], PDO::FETCH_ASSOC);
        $ANSWERED_message = 0;
        $QUALITY_message = 0;
        $PAYMENT_message = 0;
        $RANK_message = 0;
        $total_message = 0;
        // var_dump($sql);
        // var_dump($results_message);
        foreach ($results_message as $key => $v) {
            $total_message = $total_message + 1;
            if (strcmp('"' . $v["USERNAME"] . '"', $username) == 0) {
                $ANSWERED_message = $v["ANSWERED"];
                $QUALITY_message = $v["QUALITY"];
                $PAYMENT_message = $v["PAYMENT"];
                $RANK_message = $total_message;
            }
        }

        $results[0]["ANSWERED_message"] = $ANSWERED_message;
        $results[0]["QUALITY_message"] = $QUALITY_message;
        $results[0]["PAYMENT_message"] = $PAYMENT_message;
        $results[0]["RANK_message"] = $RANK_message;
        $results[0]["total_message"] = $total_message;

        $_SESSION["ANSWERED_message"] = $ANSWERED_message;
        $_SESSION["QUALITY_message"] = $QUALITY_message;
        $_SESSION["PAYMENT_message"] = $PAYMENT_message;
        $_SESSION["RANK_message"] = $RANK_message;
        $_SESSION["total_message"] = $total_message;

        $_SESSION["message_counter"] = rand(20, 30);

        $leaderboard_sql = 'select	USERNAME, 
        RANK_W, 
        round(A.avg_user/A.avg_total * A.ANSWERED * sign(0.3 - A.RANK_W)*pow((0.3 - A.RANK_W),2),2) AS RANK_L,
        if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) ),2), 0) as QUALITY,
        A.SKIP/A.ANSWERED as SKIP, 
        if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED),2), 0) as PAYRATE,
        if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED) * ANSWERED/100,2), 0) as PAYMENT,
        A.avg_user as LEN,
        answered as ANSWERED,    
        /*A.avg_total,*/
        pos, neu, neg, dif, prb, pos_pos, pos_neu, pos_neg, pos_dif, pos_prb, neu_pos, neu_neu, neu_neg, neu_dif, neu_prb, neg_pos, neg_neu, neg_neg, neg_dif, neg_prb, dif_pos, dif_neu, dif_neg, dif_dif, dif_prb, prb_pos, prb_neu, prb_neg, prb_dif, prb_prb, USERNAME as "USERNAME "
        FROM 	(SELECT		Sentence_User.username as USERNAME, 
        (SELECT avg(length(tweet))+avg(length(claim)) FROM Sentence WHERE id not in (select sentence_id from Training)) as avg_total,
                    round	(-0.20*	(	sum(if(screening = 0 and response = 0, 1, 0))+ sum(if(screening = 1 and response = 1, 1, 0))+ sum(if(screening = 2 and response = 2, 1, 0))+ sum(if(screening = 3 and response = 3, 1, 0))+ sum(if(screening = -1 and response = -1, 1, 0)) ) / ( sum(screening != -3 and response != -2))
                    +0.50 *	(	sum(if(screening = 0 and response = 3, 1, 0))+ sum(if(screening = 1 and response = 0, 1, 0))+ sum(if(screening = 1 and response = 2, 1, 0))+ sum(if(screening = 2 and response = 1, 1, 0))+ sum(if(screening = 2 and response = 3, 1, 0))+ sum(if(screening = 3 and response = 2, 1, 0))+ sum(if(screening = 3 and response = -1, 1, 0))+ sum(if(screening = -1 and response = 3, 1, 0)) ) / ( sum(screening != -3 and response != -2))
                    +0.50*	(	sum(if(screening = 0 and response = 2, 1, 0))+ sum(if(screening = 1 and response = 3, 1, 0))+ sum(if(screening = 2 and response = 0, 1, 0))+ sum(if(screening = 2 and response = -1, 1, 0))+ sum(if(screening = 3 and response = 1, 1, 0))+ sum(if(screening = -1 and response = 2, 1, 0))+ sum(if(screening = 3 and response = 0, 1, 0))+ sum(if(screening = -1 and response = 0, 1, 0)) ) / (sum(screening != -3 and response != -2))
                    +1.00*	(	sum(if(screening = 0 and response = 1, 1, 0))+ sum(if(screening = 0 and response = -1, 1, 0)) ) / (sum(screening != -3 and response != -2))
                    +2.00*	(	sum(if(screening = 1 and response = -1, 1, 0))+ sum(if(screening = -1 and response = 1, 1, 0)) ) / (sum(screening != -3 and response != -2))
                    , 3) as RANK_W,
                sum(if(Sentence_User.response != -2, 1, 0)) as ANSWERED,
                sum(if(Sentence_User.response = -2, 1, 0)) as SKIP,
                avg(if(Sentence_User.response != -2, length(tweet)+length(claim), null)) as avg_user, /*  LEN */
                sum(if(response = 1,1,0)) as pos,
                sum(if(response = 0,1,0)) as neu,
                sum(if(response = -1,1,0)) as neg,
                sum(if(response = 2,1,0)) as dif,
                sum(if(response = 3,1,0)) as prb,

                sum(if(screening = 1 and response = 1, 1, 0)) as pos_pos,
                sum(if(screening = 1 and response = 0, 1, 0)) as pos_neu,
                sum(if(screening = 1 and response = -1, 1, 0)) as pos_neg,
                sum(if(screening = 1 and response = 2, 1, 0)) as pos_dif,
                sum(if(screening = 1 and response = 3, 1, 0)) as pos_prb,

                sum(if(screening = 0 and response = 1, 1, 0)) as neu_pos,
                sum(if(screening = 0 and response = 0, 1, 0)) as neu_neu,
                sum(if(screening = 0 and response = -1, 1, 0)) as neu_neg,
                sum(if(screening = 0 and response = 2, 1, 0)) as neu_dif,
                sum(if(screening = 0 and response = 3, 1, 0)) as neu_prb,

                sum(if(screening = -1 and response = 1, 1, 0)) as neg_pos,
                sum(if(screening = -1 and response = 0, 1, 0)) as neg_neu,
                sum(if(screening = -1 and response = -1, 1, 0)) as neg_neg,
                sum(if(screening = -1 and response = 2, 1, 0)) as neg_dif,
                sum(if(screening = -1 and response = 3, 1, 0)) as neg_prb,

                sum(if(screening = 2 and response = 1, 1, 0)) as dif_pos,
                sum(if(screening = 2 and response = 0, 1, 0)) as dif_neu,
                sum(if(screening = 2 and response = -1, 1, 0)) as dif_neg,
                sum(if(screening = 2 and response = 2, 1, 0)) as dif_dif,
                sum(if(screening = 2 and response = 3, 1, 0)) as dif_prb,

                sum(if(screening = 3 and response = 1, 1, 0)) as prb_pos,
                sum(if(screening = 3 and response = 0, 1, 0)) as prb_neu,
                sum(if(screening = 3 and response = -1, 1, 0)) as prb_neg,
                sum(if(screening = 3 and response = 2, 1, 0)) as prb_dif,
                sum(if(screening = 3 and response = 3, 1, 0)) as prb_prb
            FROM		Sentence_User,
                        Sentence,
                        User
            WHERE		Sentence.id = sentence_id and
                        Sentence_User.username = User.username and
                        sentence_id not in (select sentence_id from Training) and 
                        Sentence_User.time >= "2024-06-23 17:59:59"
            GROUP BY Sentence_User.username) A 
        ORDER BY PAYMENT desc, ANSWERED desc;';
        $leaderboard_results = execute($leaderboard_sql, [], PDO::FETCH_ASSOC);
        $_SESSION["leaderboard_results"] = $leaderboard_results;

        $activity_sql = gen_insert_query(
            $tables = ["Activity"],
            $fields = ["username", "time", "action"],
            $values = [
                $username,
                '"' . date("Y-m-d H:i:s") . '"',
                "'SYSTEM UPDATED LEADERBOARD'",
            ]
        );
        $activity_sql_results = execute($activity_sql, [], PDO::FETCH_ASSOC);
    } else {
        if ($sentence_id == 0) {
            $_SESSION["message_counter"] = $_SESSION["message_counter"] - 1;
        } //decrease only if not a modify request
        if ($_SESSION["just_logged_in"] == 1) {
            $_SESSION["RANK_message"] = null;
            $_SESSION["total_message"] = null;
            $results_message = execute($sql, [], PDO::FETCH_ASSOC);
            $ANSWERED_message = 0;
            $RANK_message = 0;
            foreach ($results_message as $key => $v) {
                if (strcmp('"' . $v["USERNAME"] . '"', $username) == 0) {
                    $_SESSION["ANSWERED_message"] = $v["ANSWERED"];
                }
            }
            $_SESSION["just_logged_in"] = 0;
        }
        $results[0]["ANSWERED_message"] = $_SESSION["ANSWERED_message"];
        $results[0]["QUALITY_message"] = $_SESSION["QUALITY_message"];
        $results[0]["PAYMENT_message"] = $_SESSION["PAYMENT_message"];
        $results[0]["RANK_message"] = $_SESSION["RANK_message"];
        $results[0]["total_message"] = $_SESSION["total_message"];
        $results[0]["just_logged_in"] = $_SESSION["just_logged_in"];
        $results[0]["message_counter"] = $_SESSION["message_counter"];
        $results[0]["answered"] = $_SESSION["answered"];
        $results[0]["is_screening"] = $is_screening;
        $results[0]["screening_limit"] = $_SESSION["screening_limit"];
        $results[0]["tmp"] = $_SESSION["tmp"];
    }
    // $results[0]["sql"] = $sql;
    // $results[0]["prev_sql"] = $prev_sql;
    echo json_encode($results[0]);
} else {
    // no more sentence available
    $activity_sql = gen_insert_query(
        $tables = ["Activity"],
        $fields = ["username", "time", "action"],
        $values = [
            $username,
            '"' . date("Y-m-d H:i:s") . '"',
            '"NO MORE SENTENCE AVAILABLE"',
        ]
    );
    $activity_sql_results = execute($activity_sql, [], PDO::FETCH_ASSOC);
    echo "-1";
}
?>