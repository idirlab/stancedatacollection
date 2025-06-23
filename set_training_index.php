<?php
session_start(['cookie_lifetime' => 86400,]);
$username = $_SESSION["username"];
$sentence_id = $_REQUEST["sentence_id"];
$response = $_REQUEST["response"];
include_once "db.php";

$sql = gen_select_query(["screening"], ["Sentence"], ["id = " . $sentence_id]);
$results = execute($sql, [], PDO::FETCH_ASSOC);
$screening = $results[0]["screening"];

$sql = gen_select_query(
    ["text"],
    ["Sentence_Explanation", "Explanation"],
    [
        "sentence_id = " . $sentence_id,
        "Sentence_Explanation.explanation_id = Explanation.id",
    ]
);
$results = execute($sql, [], PDO::FETCH_ASSOC);
$explanation = $results[0]["text"];
if ($screening == $response) {
    $explanation = $explanation . "^" . "Correct!";
} else {
    $explanation = $explanation . "^" . "Wrong!";
}

$sql = gen_select_query(
    ["idx"],
    ["User_Training"],
    ["username = " . $username]
);
$results = execute($sql, [], PDO::FETCH_ASSOC);
$idx = $results[0]["idx"];

if ($idx < 16) {
    $idx = $idx + 1;
    $sql = gen_update_query(
        $tables = ["User_Training"],
        $fields = ["idx"],
        $values = [$idx],
        $where = ["username = " . $username]
    );
    execute($sql, [], PDO::FETCH_ASSOC);
}

$sql =
    'select USERNAME,ANSWERED, INCORRECT,
            if(ANSWERED >= 0, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) ),2), 0) as QUALITY,
			if(ANSWERED >= 0, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )* ANSWERED/100,2), 0) as PAYMENT
			from (select 
			    Sentence_User.username as USERNAME, 
	
				round(-0.20*	(	sum(if(screening = 0 and response = 0, 1, 0))+ sum(if(screening = 1 and response = 1, 1, 0))+ sum(if(screening = 2 and response = 2, 1, 0))+ sum(if(screening = 3 and response = 3, 1, 0))+ sum(if(screening = -1 and response = -1, 1, 0)) ) / ( sum(screening != -3 and response != -2))
                +0.50 *	(	sum(if(screening = 0 and response = 3, 1, 0))+ sum(if(screening = 1 and response = 0, 1, 0))+ sum(if(screening = 1 and response = 2, 1, 0))+ sum(if(screening = 2 and response = 1, 1, 0))+ sum(if(screening = 2 and response = 3, 1, 0))+ sum(if(screening = 3 and response = 2, 1, 0))+ sum(if(screening = 3 and response = -1, 1, 0))+ sum(if(screening = -1 and response = 3, 1, 0)) ) / ( sum(screening != -3 and response != -2))
                +0.50*	(	sum(if(screening = 0 and response = 2, 1, 0))+ sum(if(screening = 1 and response = 3, 1, 0))+ sum(if(screening = 2 and response = 0, 1, 0))+ sum(if(screening = 2 and response = -1, 1, 0))+ sum(if(screening = 3 and response = 1, 1, 0))+ sum(if(screening = -1 and response = 2, 1, 0))+ sum(if(screening = 3 and response = 0, 1, 0))+ sum(if(screening = -1 and response = 0, 1, 0)) ) / (sum(screening != -3 and response != -2))
                +1.00*	(	sum(if(screening = 0 and response = 1, 1, 0))+ sum(if(screening = 0 and response = -1, 1, 0)) ) / (sum(screening != -3 and response != -2))
                +2.00*	(	sum(if(screening = 1 and response = -1, 1, 0))+ sum(if(screening = -1 and response = 1, 1, 0)) ) / (sum(screening != -3 and response != -2)), 3) as RANK_W,
	
				sum(if(Sentence_User.response != screening, if(Sentence_User.response != -2, 1, 0), 0)) as INCORRECT,
				sum(if(Sentence_User.response != -2, 1, 0)) as ANSWERED,
				sum(if(Sentence_User.response = -2, 1, 0)) as SKIPPED
			from
			    Sentence_User,
			    Sentence,
				User
			where
			    id = sentence_id and		
                Sentence_User.time >= "2023-08-25 12:00:00" and
				Sentence_User.username = User.username and
				Sentence_User.username = ' .
    $username .
    ' and sentence_id in (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16) group by Sentence_User.username) A order by PAYMENT desc, ANSWERED desc;';

$results = execute($sql, [], PDO::FETCH_ASSOC);

echo $explanation .
    "^" .
    $idx .
    "^" .
    $results[0]["INCORRECT"] .
    "^" .
    $results[0]["PAYMENT"];
?>
