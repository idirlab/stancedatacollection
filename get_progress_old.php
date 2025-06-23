<script> window.history.replaceState('', '', '/stance_annotation/'); </script>

<?php
include_once "db.php";
include_once "vars.php";

echo '<style>
		.button {
		display: inline-block;
		padding: 10px 20px;
		font-size: 20px;
		cursor: pointer;
		tprb-align: center;
		tprb-decoration: none;
		outline: none;
		color: #fff;
		background-color: #7dc600;
		border: none;
		border-radius: 10px;
		box-shadow: 0 5px #999;
		margin: 10px 20px 10px 5px;
		}
		.button:hover {background-color: #257d07}
		.button:active {
		background-color: #257d07;
		box-shadow: 0 3px #666;
		transform: translateY(4px);
		}
	</style>';

// echo '<div style = "float: right;">
// 	<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br><br>
// </div>';

/* For db debugging*/
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
ini_set("display_errors", 1); /* set to 0 to stop*/
error_reporting(E_ALL);
// Check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

/* General purpose statistics and explanations*/
echo '<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>';
echo "<H1>Truthfulness Stance Detection Annotation Progress</H1>";
echo $top_participants_sql;
echo "<H3>Ranking Equation Explanations:</H3>";
echo '<div style= "padding-left:15px;">';
echo '<b>- <span style="color:#ED00FB;">LEN</span>:</b> is the average word length of the tweet and claim labelled by the participant.<br>';
echo '<b>- <span style="color:blue;">L:</span></b> is the average length of all pairs to be annotated = <span style="color:blue; font-weight:bold; font-size:large;">' .
    $avg_len .
    "</span> characters.<br>";
echo '<b>- <span style="color:#1BBC1D;">ANSWERED</span>:</b> is the total number of annotated pairs per user.<br>';
echo '<b><s>- RANK_E:</b> <a href="http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.230.7064&rep=rep1&type=pdf">http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.230.7064&rep=rep1&type=pdf</a> [equation 8]</s><br>';
echo '<b>- <span style="color:#6E05F8;">RANK_W</span>:</b>
					<div style= "padding-left:15px;">
					&#x2022; cost <b>+2.0</b> &#8594; when participant makes "pos_neg", "neg_pos" mistakes. <br>
					&#x2022; cost <b>+1.0</b> &#8594; when participant makes "neu_pos", "neu_neg" mistakes.<br>
					&#x2022; cost <b>+0.5</b> &#8594; when participant makes other mistakes.<br>
					&#x2022; cost <b>-0.2</b> &nbsp;&#8594; when the participant chooses correct label<br>
					</div>';
echo '<b>- <span style="color:#23BD98;">RANK_L</span>:</b> <span style="color:#ED00FB;">LEN</span> / <span style="color:blue;">L</span> * <span style="color:#1BBC1D;">ANSWERED</span>*sign(0.3 - <span style="color:#6E05F8;">RANK_W</span>)*pow((0.3 - <span style="color:#6E05F8;">RANK_W</span>), 2), rounds the output to 2 decimals<br>';
echo '<b>- <span style="color:#C12374;">QUALITY</span>:</b> if <span style="color:#6E05F8;">RANK_W</span> <= 0 then 3-7*<span style="color:#6E05F8;">RANK_W</span>/0.2 elif <span style="color:#6E05F8;">RANK_W</span> <= 0.3 then pow((0.3 - <span style="color:#6E05F8;">RANK_W</span>)/0.3, 2.5)*3 else 0, when <span style="color:#1BBC1D;">ANSWERED</span>>= 20, else 0, rounds the output to 2 decimals<br>';
// echo '<b>- <span style="color:#C12374;">QUALITY</span>:</b> if(<span style="color:#6E05F8;">RANK_W</span> <= 0   ,  3-7*<span style="color:#6E05F8;">RANK_W</span>/0.2, if (<span style="color:#6E05F8;">RANK_W</span> <= 0.3 then pow((0.3 - <span style="color:#6E05F8;">RANK_W</span>)/0.3, 2.5)*3  ,0)) <br>';
echo '<b>- <span style="color:#F80505;">SKIP</span>:</b> "number of skipped pairs per user"  / <span style="color:#1BBC1D;">ANSWERED</span><br>';
echo '<b>- <span style="color:#0273FD;">PAYRATE</span>:</b>  2.0 * round(<span style="color:#C12374;">QUALITY</span> * pow(<span style="color:#ED00FB;">LEN</span>/<span style="color:blue;">L</span>, 2.0) * pow(0.6, <span style="color:#F80505;">SKIP</span>), 2) when <span style="color:#1BBC1D;">ANSWERED</span>>= 20, else 0<br>';
echo '<b>- <span style="color:#FD6202;">PAYMENT</span>:</b>  <span style="color:#0273FD;">PAYRATE</span>  * <span style="color:#1BBC1D;">ANSWERED</span> / 100<br>';

echo '<b>- Table keys</b>
				<ul>
					<li><b>"pos"</b>: total # of positive responses,  	 </li>
					<li><b>"neu"</b>: total # of neutral/no stance responses, </li>
					<li><b>"neg"</b>: total # of negative responses, </li>
					<li><b>"dif"</b>: total # of different topic responses, </li>
					<li><b>"prb"</b>: total # of problematic responses.  </li>

					<li><b>"pos_pos"</b>: total # of positive response annotated as positive,  </li>
					<li><b>"pos_neu"</b>: total # of positive response annotated as neutral/no stance,  </li>
					<li><b>"pos_neg"</b>: total # of positive response annotated as negative,  </li>
					<li><b>"pos_dif"</b>: total # of positive response annotated as different topic,  </li>
					<li><b>"pos_prb"</b>: total # of positive response annotated as problematic.  </li>

					<li><b>"neu_pos"</b>: total # of neutral/no stance responses annotated as positive,  </li>
					<li><b>"neu_neu"</b>: total # of neutral/no stance responses annotated as neutral/no stance,  </li>
					<li><b>"neu_neg"</b>: total # of neutral/no stance responses annotated as negative,  </li>
					<li><b>"neu_dif"</b>: total # of neutral/no stance responses annotated as different topic,  </li>
					<li><b>"neu_prb"</b>: total # of neutral/no stance responses annotated as problematic.  </li>

					<li><b>"neg_pos"</b>: total # of negative responses annotated as positive,  </li>
					<li><b>"neg_neu"</b>: total # of negative responses annotated as neutral/no stance,  </li>
					<li><b>"neg_neg"</b>: total # of negative responses annotated as negative,  </li>
					<li><b>"neg_dif"</b>: total # of negative responses annotated as different topic,  </li>
					<li><b>"neg_prb"</b>: total # of negative responses annotated as problematic.  </li>

					<li><b>"dif_pos"</b>: total # of different topic responses annotated as positive,  </li>
					<li><b>"dif_neu"</b>: total # of different topic responses annotated as neutral/no stance,  </li>
					<li><b>"dif_neg"</b>: total # of different topic responses annotated as negative,  </li>
					<li><b>"dif_dif"</b>: total # of different topic responses annotated as different topic,  </li>
					<li><b>"dif_prb"</b>: total # of different topic responses annotated as problematic.  </li>

					<li><b>"prb_pos"</b>: total # of problematic responses annotated as positive,  </li>
					<li><b>"prb_neu"</b>: total # of problematic responses annotated as neutral/no stance,  </li>
					<li><b>"prb_neg"</b>: total # of problematic responses annotated as negative,  </li>
					<li><b>"prb_dif"</b>: total # of problematic responses annotated as different topic,  </li>
					<li><b>"prb_prb"</b>: total # of problematic responses annotated as problematic. </li>
				</ul>';
echo "</div><br>";

echo '<H3>Worker\'s Performance<sup>*</sup>:</H3>';
// echo $top_participants_string;
// echo $total_labels[0];
echo '<div style= "padding-left:15px;">';
/* Get annotation status of each subset and overall. */
$sql =
    'SELECT 	"TOTAL" subset, 
							count(*) as total,
							sum(if(id in (select sentence_id from Training), 1, 0)) as training,
							sum(if(screening != -3 and id not in (select sentence_id from Training), 1, 0)) as screening, 
							( count(*) - sum(if(screening!=-3 or id in (select sentence_id from Training), 1, 0)) ) as todo,
							sum(if(id in ' .
    $top_quality_sentences_string .
    ', 1, 0)) as finished, CONCAT(  round (  100*(  sum(if(id in ' .$top_quality_sentences_string .
    ', 1, 0))  /  ( count(*) - sum(if(screening!=-3 or id in (select sentence_id from Training), 1, 0)) )  ), 2), " %" ) as progress /* (finished/todo) */
					FROM Sentence
					UNION ALL
					SELECT	subset, 
							count(*) as total,
							sum(if(id in (select sentence_id from Training), 1, 0)) as training,
							sum(if(screening != -3 and id not in (select sentence_id from Training), 1, 0)) as screening, 
							( count(*) - sum(if(screening!=-3 or id in (select sentence_id from Training), 1, 0)) ) as todo,
							sum(if(id in ' .
    $top_quality_sentences_string .
    ', 1, 0)) as finished, 
							CONCAT(  round (  100*(  sum(if(id in ' .
    $top_quality_sentences_string .
    ', 1, 0))  /  ( count(*) - sum(if(screening!=-3 or id in (select sentence_id from Training), 1, 0)) )  ), 2), " %" ) as progress /* (finished/todo) */
					FROM Sentence
					GROUP BY subset
					ORDER BY subset;';
$subsets_completion = execute($sql, [], PDO::FETCH_ASSOC);

echo '<table border = "1">';
echo "<tr>";
foreach ($subsets_completion[0] as $key => $value) {
    echo "<th>" . $key . "</th>";
}
echo "</tr>";
foreach ($subsets_completion as $key => $v) {
    echo "<tr>";
    $count = count($v);
    foreach ($v as $k => $value) {
        echo "<td>" . $value . "</td>";
    }
    echo "</tr>";
}
echo "<tr>";
foreach ($subsets_completion[0] as $key => $value) {
    echo "<th>" . $key . "</th>";
}
echo "</tr>";
echo "</table>";

echo '- <b><span style="color:blue; font-weight:bold; font-size:large;">' .
    count($top_participants) .
    "</span> </b> top-quality participants (RANK_W <= 0.0) of total " .
    $all_participants .
    "<br>";
echo '- <b><span style="color:blue; font-weight:bold; font-size:large;">' .
    $total_labels[0] .
    "</span></b> labels collected so far!<br>";
echo '- <b><span style="color:blue; font-weight:bold; font-size:large;">' .
    $total_top_labels[0] .
    "</span></b> are top-quality labels<br><br>";
$sql =
    'SELECT	A.username, count(*) as count FROM	(SELECT * FROM Sentence_User WHERE response != -2 /*and time >= "2023-02-09 00:00:00"*/ ORDER BY time DESC limit 100) A GROUP BY A.username ORDER BY time DESC/* COUNT(*) DESC */;';
$results = execute($sql, [], PDO::FETCH_ASSOC);

echo "<b>Latest (100) pair labelers:</b>";
foreach ($results as $key => $v) {
    echo "<br>- " . $v["username"] . " (" . $v["count"] . "), ";
}
echo "<br><br>";

echo "<b>Active Participants in last 5 minutes are marked below:</b>";
$sql = 'SELECT 	username, min(minute(timediff(now(), time))) as mins 
						FROM	Sentence_User  
						WHERE 	minute(timediff(now(), time)) <= 5 and hour(timediff(now(), time)) = 0 
						GROUP BY username
						ORDER BY mins desc';
$results = execute($sql, [], PDO::FETCH_ASSOC);
// var_dump($results);
// echo 'q:'.$sql;
$active_users = [];
foreach ($results as $key => $v) {
    array_push($active_users, $v["username"]);
}
// var_dump($active_users);
if (!$results) {
    echo "<br>...none";
} else {
    foreach ($results as $key => $v) {
        echo "<br>- [" . $v["mins"] . " mins] " . $v["username"];
    }
}
// echo '<br><br>';

echo "</div><br>";

/* Current evaluation for finished pairs based on majority from top performers*/
echo '<div style= "padding-left:15px;">';
echo "<H3>Current: Finished pairs<sup>*</sup> based on majority from top performers</H3>";
$sql =
    'SELECT	sentence_id as id, claim, tweet, screening, Label_0 as "[0] - positive", Label_1 as "[1] - neutral/no stance", Label_2 as "[2] - negative", Label_3 as "[3] - different topic", Label_4 as "[4] - problematic"
				FROM	(SELECT	su.sentence_id, s.tweet, s.claim, s.screening,
								sum(if(su.response = 1, 1, 0)) as Label_0, 
							 	sum(if(su.response = 0, 1, 0)) as Label_1,	
							 	sum(if(su.response = -1, 1, 0)) as Label_2,	
							 	sum(if(su.response = 2, 1, 0)) as Label_3,	
							 	sum(if(su.response = 3, 1, 0)) as Label_4
						FROM	Sentence_User as su, Sentence s 
						WHERE	s.id = su.sentence_id and
								-- s.								s.screening = -3 and
								su.sentence_id not in (select sentence_id from Training) and
								su.username in ' .
    $top_participants_string .
    '
						GROUP BY su.sentence_id
						HAVING	(Label_0 >= 3 and	Label_0 >= 2+Label_1	and	Label_0 >= 2+Label_2	and	Label_0 >= 2+Label_3	and	Label_0 >= 2+Label_4 	and	Label_0 >= round((Label_1 + Label_2 + Label_3) / 2, 1) ) or
								(Label_1 >= 3 and	Label_1 >= 2+Label_0	and	Label_1 >= 2+Label_2	and	Label_1 >= 2+Label_3	and	Label_1 >= 2+Label_4 	and	Label_1 >= round((Label_0 + Label_2 + Label_3) / 2, 1)  ) or
								(Label_2 >= 3 and	Label_2 >= 2+Label_0	and	Label_2 >= 2+Label_1	and	Label_2 >= 2+Label_3	and	Label_2 >= 2+Label_4 	and	Label_2 >= round((Label_0 + Label_1 + Label_3) / 2, 1)  ) or
								(Label_3 >= 3 and	Label_3 >= 2+Label_0	and	Label_3 >= 2+Label_1	and	Label_3 >= 2+Label_2	and	Label_3 >= 2+Label_4 	and	Label_3 >= round((Label_0 + Label_1 + Label_2) / 2, 1)  ) or
								(Label_4 >= 3 and	Label_4 >= 2+Label_0	and	Label_4 >= 2+Label_1	and	Label_4 >= 2+Label_2	and	Label_4 >= 2+Label_3 	and	Label_4 >= round((Label_0 + Label_1 + Label_2 + Label_3) / 2, 1)  ) )A limit 50;';

$results = execute($sql, [], PDO::FETCH_ASSOC);
// var_dump($results);
$ID = 1;
// if results is empty, then no need to print the table header
if ($results) {
    echo '<table border = "1">';
    echo "<tr>";
    echo "<th>" . "#" . "</th>";

    foreach ($results[0] as $key => $value) {
        if (strcmp($key, "claim") == 0) {
            echo '<th style="width: 30%;">' . "Claim" . "</th>";
        } elseif (strcmp($key, "tweet") == 0) {
            echo '<th style="width: 30%;">' . "Tweet" . "</th>";
        } else {
            echo "<th>" . $key . "</th>";
        }
    }
    echo "</tr>";
    foreach ($results as $key => $v) {
        if ($v["screening"] >= 0) {
            echo '<tr bgcolor="#d9d9d9">';
            $ID = $ID - 1;
            echo '<td style="tprb-align: center;"> - </td>';
        } else {
            echo '<tr> <td style="tprb-align: center;">' . $ID . "</td>";
        }
        foreach ($v as $k => $value) {
            echo '<td style="width: 5%;">' . $value . "</td>";
        }
        echo "</tr>";
        $ID = $ID + 1;
    }
    echo "<tr>";
    echo "<th>" . "#" . "</th>";
    foreach ($results[0] as $key => $value) {
        echo "<th>" . $key . "</th>";
    }
    echo "</tr>";
    echo "</table>";
    echo "<small><sup>*</sup>Limit Top 50 pairs </small>";
} else {
    echo "<p>No results found.</p>";
}
echo "</div><br>";
echo "</div><br>";

/* Overall worker performance statistics */
echo '<div style= "padding-left:15px;">';
echo "<H3>Overall worker statistics (answered pairs>=20)</H3>";

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
									sentence_id not in (select sentence_id from Training)
						GROUP BY Sentence_User.username) A 
					ORDER BY PAYMENT desc, ANSWERED desc;';

$results = execute($sql, [], PDO::FETCH_ASSOC);
// var_dump($results);
$ID = 1;
echo '<table border = "1">';
echo "<tr>";
echo "<th>" . "ID" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PAYRATE_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . 'PAYMENT_($)' . "</th>";
    }
    //dif=4
    elseif (strcmp($key, "pos_prb") == 0) {
        echo '<th bgcolor="#FB0000" style = "color:white;">' .
            "pos_prb" .
            "</th>";
    } elseif (strcmp($key, "prb_pos") == 0) {
        echo '<th bgcolor="#FB0000" style = "color:white;">' .
            "prb_pos" .
            "</th>";
    }
    //dif=3
    elseif (strcmp($key, "pos_dif") == 0) {
        echo '<th bgcolor="#FA9EA1">' . "pos_dif" . "</th>";
    } elseif (strcmp($key, "dif_pos") == 0) {
        echo '<th bgcolor="#FA9EA1">' . "dif_pos" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th bgcolor="#FA9EA1">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th bgcolor="#FA9EA1">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "</tr>";

foreach ($results as $key => $v) {
    if (in_array($v["USERNAME"], $active_users)) {
        echo '<tr bgcolor="#8DDF00">';
    } else {
        echo "<tr>";
    }
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
        echo "<th>" . 'PAYMENT_($)' . "</th>";
    }
    //dif=4
    elseif (strcmp($key, "pos_prb") == 0) {
        echo '<th bgcolor="#FB0000" style = "color:white;">' .
            "pos_prb" .
            "</th>";
    } elseif (strcmp($key, "prb_pos") == 0) {
        echo '<th bgcolor="#FB0000" style = "color:white;">' .
            "prb_pos" .
            "</th>";
    }
    //dif=3
    elseif (strcmp($key, "pos_dif") == 0) {
        echo '<th bgcolor="#FA9EA1">' . "pos_dif" . "</th>";
    } elseif (strcmp($key, "dif_pos") == 0) {
        echo '<th bgcolor="#FA9EA1">' . "dif_pos" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th bgcolor="#FA9EA1">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th bgcolor="#FA9EA1">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "</tr>";
echo "</table>";
echo "</div><br>";


/* Workshop statistics (pomodoro1) */
echo '<br>';
echo '<div id="pomodoro1" style= "padding-left:15px;">';
echo '<H3>Worker performance during annotation campaign workshop <span style="background-color:powderblue;">[Pomodoro day 1, start time: 2023-02-15 12:00:00, end time: 2023-02-15 21:00:00]</span></H3>';

$sql = 'select	USERNAME, 
					RANK_W, 
					round(A.avg_user/A.avg_total * A.ANSWERED * sign(0.3 - A.RANK_W)*pow((0.3 - A.RANK_W),2),2) AS RANK_L,
					if(ANSWERED >= 20, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) ),2), 0) as QUALITY,
					A.SKIP/A.ANSWERED as SKIP, 
					if(ANSWERED >= 20, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED),2), 0) as PAYRATE,
					if(ANSWERED >= 20, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED) * ANSWERED/100,2), 0) as PAYMENT,
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
									Sentence_User.time >= "2023-02-15 12:00:00" and
									sentence_id not in (select sentence_id from Training)
						GROUP BY Sentence_User.username) A 
					ORDER BY PAYMENT desc, ANSWERED desc;';


$results = execute($sql, array(), PDO::FETCH_ASSOC);
//var_dump($results);
$ID = 1;
echo '<table border = "1">';
echo '<tr>';
echo '<th>'.'ID'.'</th>';
foreach($results[0] as $key=>$value)
{
	if(strcmp($key, 'PAYRATE') == 0)echo '<th>'.'PAYRATE_(&cent;)'.'</th>';
	else if(strcmp($key, 'PAYMENT') == 0)echo '<th>'.'PAYMENT_($)'.'</th>';
	else echo '<th>'.$key.'</th>';
}
echo '</tr>';
foreach($results as $key=>$v)
{
	// if(in_array($v['USERNAME'], $active_users))
	// {
	// 	echo '<tr bgcolor="#B6D0E2">';	
	// }
	// else echo '<tr>';
	echo '<tr>';
	echo '<td>'.$ID.'</td>';
	foreach($v as $k=>$value) {
		echo '<td>'.$value.'</td>';
	}
	echo '</tr>';
	$ID = $ID + 1;
}
echo '<tr>';
echo '<th>'.'ID'.'</th>';
foreach($results[0] as $key=>$value)
{
	if(strcmp($key, 'PAYRATE') == 0)echo '<th>'.'PAYRATE_(&cent;)'.'</th>';
	else if(strcmp($key, 'PAYMENT') == 0)echo '<th>'.'PAYMENT_($)'.'</th>';
	else echo '<th>'.$key.'</th>';
}
echo '</tr>';
echo '</table>';
echo '<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>';

echo '</div><br>';


$timezone = date_default_timezone_get();
$date = date("m/d/Y h:i:s a", time());
echo "<br><br>Webpage last load on " . $date . ", " . $timezone . "<br>";
?>
