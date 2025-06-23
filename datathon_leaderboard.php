<?php
include_once "db.php";
include_once "vars.php";

$sql =
    "select avg(length) from Sentence where id in " .
    $todo_sentence_string .
    ";";
$avg_len = execute($sql, [], PDO::FETCH_COLUMN)[0];

/* Datathon statistics */
echo '<div id="pomodoro1" style= "padding-left:15px;">';
echo "<br><h2>A.3 Truthfulness stance annotation</h2><br><b>Performance during UTA Datathon on April 13-14, 2024</b>";

// echo '<H3>Worker performance during annotation campaign workshop <span style="background-color:powderblue;">[Pomodoro day 1, start time: 2023-02-15 12:00:00, end time: 2023-02-15 21:00:00]</span></H3>';

$sql = 'select	USERNAME, EMAIL,
					RANK_W, 
					answered as "#",
					A.avg_user as LEN,
					A.SKIP/A.ANSWERED as SKIP, 
					if(ANSWERED >= 5, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) ),2), 0) as QUALITY,
					if(ANSWERED >= 5, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED) * ANSWERED/100,2), 0) as PAYMENT
					
					
					FROM 	(SELECT		Sentence_User.username as USERNAME, email as EMAIL,
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
									Sentence_User.time >= "2024-04-13 10:00:00" and
									sentence_id not in (select sentence_id from Training)
						GROUP BY Sentence_User.username) A 
					ORDER BY PAYMENT desc, ANSWERED desc;';

$results = execute($sql, [], PDO::FETCH_ASSOC);
//var_dump($results);
$ID = 1;
echo '<table border = "1">';
echo "<tr>";
echo "<th>" . "ID" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PAYRATE_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . "POINTS" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "</tr>";
foreach ($results as $key => $v) {
    // if(in_array($v['USERNAME'], $active_users))
    // {
    // 	echo '<tr bgcolor="#B6D0E2">';
    // }
    // else echo '<tr>';
    echo "<tr>";
    echo "<td>" . $ID . "</td>";
    foreach ($v as $k => $value) {
        echo "<td>" . $value . "</td>";
    }
    echo "</tr>";
    $ID = $ID + 1;
}
echo "<tr>";
echo "<th>" . "ID" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PAYRATE_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . "POINTS" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "</tr>";
echo "</table>";

echo "</div><br>";
?>
