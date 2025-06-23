<script> window.history.replaceState('', '', '/stance_annotation/'); </script>


<!-- Bootstrap core CSS -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- CSS --  source: https://datatables.net/examples/index -->
<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"/><!--  NEW -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css"/> <!--Bootstrap 5 -->
<!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.3/css/dataTables.bootstrap5.min.css"/> Bootstrap 5 -->

<!-- Javascript -->
<link  type="text/javascript" href="https://code.jquery.com/jquery-3.5.1.js"></script><!-- NEW -->
<link  type="text/javascript" href="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script><!-- NEW -->
<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.3/js/dataTables.bootstrap5.min.js"></script> <!--Bootstrap 5 -->

<?php
session_start();
$username = $_SESSION["username"];
// List of allowed usernames
$allowedUsernames = array("\"zzy\"", "\"chengkai\"", "\"ttb\"");
// check if session value is set
if (!in_array($username, $allowedUsernames)) {
    header("Location: index.php");
}

echo '<head><link rel="icon" type="image/png" href="image/chain_logo_fav.png" /></head>';

include_once "db.php";
include_once "vars.php";

echo '<body style = "padding: 25px 30px;">';
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

$timezone = date_default_timezone_get();
$date = date("m/d/Y h:i:s a", time());

$sql = 'SELECT 	USERNAME, min(minute(timediff(now(), time))) as mins 
			FROM	Sentence_User  
			WHERE 	minute(timediff(now(), time)) <= 15 and hour(timediff(now(), time)) = 0 /*and sentence_id NOT IN (select sentence_id from Training)*/
			GROUP BY username
			ORDER BY mins desc;';
$results = execute($sql, [], PDO::FETCH_ASSOC);
// var_dump($results);
// echo 'q:'.$sql;
$active_users = [];
foreach ($results as $key => $v) {
    array_push($active_users, $v["USERNAME"]);
}

/* For db debugging*/
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
ini_set("display_errors", 1); /* set to 0 to stop*/
error_reporting(E_ALL);
// Check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}
// echo "* DB: Connected successfully";

echo '<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>';
echo "Webpage last load on " . $date . ", " . $timezone . "<br><br><br>";

/* General purpose statistics and explanations*/
echo '<H1 style="background-color: #ABBAEA;">  Truthfulness Stance Data Annotation - Control Center </H1>';
echo "<b>Note 1:</b> Active Participants in the last <b>15 mins</b> are highlighted green.<br>";
echo "<mark><b>Note 2:</b> Welcome " . $username . ".</mark><br><br>";

/* General purpose statistics and explanations*/
// source: https://getbootstrap.com/docs/5.0/components/accordion/#always-open
echo '<div class="accordion" id="accordion">';

// -- -- -- -- START: accordion-item -- -- -- --
echo '<div class="accordion-item">
        <div class="accordion-header" id="equations_heading">';
echo '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#equations_collapse" aria-expanded="false" aria-controls="equations_collapse"> '; // CLOSE
//<!-- accordion-Header -->
echo '<H3>Equation Explanations</H3>
      </button></div>';
echo '<div id="equations_collapse" class="accordion-collapse collapse" aria-labelledby="equations_heading">';
echo '<div class="accordion-body">';
//<!-- accordion-Body -->
echo '
    <div style= "padding-left:15px;">
        <b>- <span style="color:#ED00FB;">LEN</span>:</b>  is the average word length of the sentences labelled by the participant<br>
        <b>- <span style="color:blue;">L:</span></b> is the average length of all sentences to be annotated = <span style="color:blue; font-weight:bold; font-size:large;">' .
    $avg_len .
    '</span><br>
        <b>- <span style="color:#1BBC1D;">ANSWERED</span>:</b> is the total number of annotated pairs per user.<br>
        <b><s>- RANK_E:</b> <a href="http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.230.7064&rep=rep1&type=pdf">http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.230.7064&rep=rep1&type=pdf</a> [equation 8]</s><br>
        <b>- <span style="color:#6E05F8;">RANK_W</span>:</b>
            <div style= "padding-left:15px;">
            &#x2022; cost <b>+2.0</b> &#8594; when participant makes "pos_neg", "neg_pos" mistakes. <br>
            &#x2022; cost <b>+1.0</b> &#8594; when participant makes "neu_pos", "neu_neg" mistakes.<br>
            &#x2022; cost <b>+0.5</b> &#8594; when participant makes other mistakes.<br>
            &#x2022; cost <b>-0.2</b> &nbsp;&#8594; when the participant chooses correct label<br>
            </div>
        <b>- <span style="color:#23BD98;">RANK_L</span>:</b> <span style="color:#ED00FB;">LEN</span> / <span style="color:blue;">L</span> * <span style="color:#1BBC1D;">ANSWERED</span>*sign(0.3 - <span style="color:#6E05F8;">RANK_W</span>)*pow((0.3 - <span style="color:#6E05F8;">RANK_W</span>), 2), rounds the output to 2 decimals<br>
        <b>- <span style="color:#C12374;">QUALITY</span>:</b> if <span style="color:#6E05F8;">RANK_W</span> <= 0 then 3-7*<span style="color:#6E05F8;">RANK_W</span>/0.2 elif <span style="color:#6E05F8;">RANK_W</span> <= 0.3 then pow((0.3 - <span style="color:#6E05F8;">RANK_W</span>)/0.3, 2.5)*3 else 0, when <span style="color:#1BBC1D;">ANSWERED</span>>= 20, else 0, rounds the output to 2 decimals<br>
        <b>- <span style="color:#F80505;">SKIP</span>:</b> "number of skipped pairs per user"  / <span style="color:#1BBC1D;">ANSWERED</span><br>
        <b>- <span style="color:#0273FD;">PAYRATE (PRT)</span>:</b>  2.0 * round(<span style="color:#C12374;">QUALITY</span> * pow(<span style="color:#ED00FB;">LEN</span>/<span style="color:blue;">L</span>, 1.5) * pow(0.6, <span style="color:#F80505;">SKIP</span>), 2) when <span style="color:#1BBC1D;">ANSWERED</span>>= 20, else 0<br>
        <b>- <span style="color:#FD6202;">PAYMENT (PMT)</span>:</b>  <span style="color:#0273FD;">PAYRATE</span>  * <span style="color:#1BBC1D;">ANSWERED</span> / 100<br>

        <b>- Table keys</b>
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
        </ul>
    </div><br>

</div>
<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>
Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    '<br>
				</div>
			</div>';
// -- -- -- -- END: accordion-item -- -- -- --

// // -- -- -- -- START: accordion-item -- -- -- --
echo '<div class="accordion-item">
<div class="accordion-header" id="log_heading">';
echo '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#log_collapse" aria-expanded="false" aria-controls="log_collapse">'; // CLOSE
//<!-- accordion-Header -->
echo '<H3>Log History <sup></sup></H3>
    </button>
</div>'; // HEADER
echo '<div id="log_collapse" class="accordion-collapse collapse" aria-labelledby="log_heading">'; // CLOSE
echo '<div class="accordion-body">';
//<!-- accordion-Body -->';
//START
echo '<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>
        Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    "<br><br>";

echo '<div style= "padding-left:15px;">';
echo "<br><h4> The table below shows records of the changes gradually made in the data collection system.</h4><br>";

echo "<div>";
echo '<table id="log_progress" border = "1" class="table table-striped"  style="width:100%">';

echo "<thead>";
echo "<tr>";
echo "<th>Date</th>";
echo "<th>Action</th>";
echo "<th>Comments</th>";
echo "</tr>";
echo "</thead>";

echo "<tbody>";
echo '<tr>
                                <td>2023-08-10</td>
                                <td>Switch to payment version</td>				
                                <td> - </td>
                                </tr>';
echo '<tr>
                                <td>2023-08-25</td>
                                <td>Reset leaderboard, Start payment phase 1</td>				
                                <td>Top quality collected so far: 10.131 pairs -- progress 45.60%</td>
                                </tr>';
echo '<tr>
                                <td>2023-08-31</td>
                                <td>Update get_progress page</td>				
                                <td>Add paid phase 1</td>
                                </tr>';
echo '<tr>
                                <td>2023-09-26</td>
                                <td>Update get_progress page</td>				
                                <td>Add paid phase 2</td>
                                </tr>';
echo '<tr>
                                <td>2023-10-26</td>
                                <td>Update get_progress page</td>				
                                <td>Add paid phase 3</td>
                                </tr>';
echo '<tr>
                                <td>2023-11-26</td>
                                <td>Update get_progress page</td>				
                                <td>Add paid phase 4</td>
                                </tr>';
echo '<tr>
                                <td>2023-12-25</td>
                                <td>Update get_progress page</td>				
                                <td>Add paid phase 5</td>
                                </tr>';
echo '<tr>
                                <td>2024-12-22</td>
                                <td>Update get_progress page</td>				
                                <td>Add initial trail</td>
                                </tr>';
echo "</tbody>";

echo "</table>";
echo "</div>";

echo '<script type="text/javascript"> 
                $(document).ready(function(){  
                    $("#log_progress").DataTable({
                        lengthMenu: [
                            [15, 30, 50, -1],
                            [15, 30, 50, "All"]
                        ],
                        
                        
                        order: [[0, "asc"]],
                    });  
                });  
                </script>';
echo "</div>";

//END
echo "</div>"; // BODY
echo "</div>"; // BODY HEADING
echo "</div>"; // ITEM
// // -- -- -- -- END: accordion-item -- -- -- --

// -- -- -- -- START: accordion-item -- -- -- --
echo '<div class="accordion-item">
				<div class="accordion-header" id="overallProgress_heading">';
echo '<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#overallProgress_collapse" aria-expanded="[false]" aria-controls="overallProgress_collapse">
						<!-- accordion-Header -->
						<H3>Progress per "Subset" </H3>
					</button>
				</div>';

//<div id="overallProgress_collapse" class="accordion-collapse collapse [show]" aria-labelledby="overallProgress_heading">
echo '<div id="overallProgress_collapse" class="accordion-collapse collapse" aria-labelledby="overallProgress_heading">
					<div class="accordion-body">
						<!-- accordion-Body -->';
//START

echo '<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>
						Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    "<br><br>";

echo '<div style= "padding-left:15px;">';
// -- Get annotation status of each subset and overall. --

echo "<br><h4> Overall Annotation Progress per Subset</h4><br>";
$sql =
    'SELECT
        "TOTAL" as subset,
        count(*) as total,
        sum(if(id in (select sentence_id from Training), 1, 0)) as Training,
        sum(if(screening != -3 and id not in (select sentence_id from Training), 1, 0)) as screening, 
        (count(*) - sum(if(screening != -3 or id in (select sentence_id from Training), 1, 0))) as todo,
        sum(if(id in ' .
    $top_quality_sentences_string .
    ', 1, 0)) as finished, 
        sum(if(B.label = 0, 1, 0)) as "neg", 
        sum(if(B.label = 1, 1, 0)) as "neu",
        sum(if(B.label = 2, 1, 0)) as "pos",
        sum(if(B.label = 3, 1, 0)) as "dif",	
        sum(if(B.label = 4, 1, 0)) as "prb",
        (select	count(*) 
         from	Sentence_User su, Sentence s
         where 	su.sentence_id = s.id 
                and s.screening = - 3 
                and su.response != - 2) 
        as total_collected, 
        (select	count(*) 
         from	Sentence_User su, Sentence s
         where 	su.sentence_id = s.id 
                and	s.screening = - 3 
                and su.response != - 2 
                and su.username in ' .
    $top_participants_string .
    ')  
        as top_quality_collected, 
        CONCAT(  round (  (  sum(if(id in ' .
    $top_quality_sentences_string .
    ', 1, 0))  /  (   count(*) - sum(if(screening != -3 or id in (select sentence_id from Training), 1, 0))   )  )*100, 2), " %" ) as progress /* (finished/todo) */
        FROM Sentence
        LEFT OUTER JOIN (' .
    $top_finished_labels .
    ') as B ON Sentence.id = B.sentence_id
        UNION ALL
        SELECT	subset, 
                count(*) as total,
                sum(if(id in (select sentence_id from Training), 1, 0)) as Training,
                sum(if(screening != -3 and id not in (select sentence_id from Training), 1, 0)) as screening, 
                (count(*) - sum(if(screening != -3 or id in (select sentence_id from Training), 1, 0))) as todo ,
                sum(if(id in ' .
    $top_quality_sentences_string .
    ', 1, 0)) as finished, 
                sum(if(B.label = 0, 1, 0)) as "neg",
                sum(if(B.label = 1, 1, 0)) as "neu",
                sum(if(B.label = 2, 1, 0)) as "pos",
                sum(if(B.label = 3, 1, 0)) as "dif",
                sum(if(B.label = 4, 1, 0)) as "prb",
                (select	count(*) 
                 from	Sentence_User su, Sentence s
                 where 	su.sentence_id = s.id 
                        and s.screening = - 3 
                        and su.response != - 2 and Sentence.subset= s.subset
                group by subset)
                as total_collected, 
                (select	count(*) 
                 from	Sentence_User su, Sentence s
                 where 	su.sentence_id = s.id 
                        and	s.screening = - 3 
                        and su.response != - 2 
                        and su.username in ' .
    $top_participants_string .
    ' and Sentence.subset = s.subset
                 group by subset)
                 as top_quality_collected, 											
                CONCAT(  round (  (  sum(if(id in ' .
    $top_quality_sentences_string .
    ', 1, 0))  /  (   count(*) - sum(if(screening != -3 or id in (select sentence_id from Training), 1, 0))   )  )*100, 2), " %" ) as progress
        FROM Sentence
        LEFT OUTER JOIN (' .
    $top_finished_labels .
    ') as B 
    ON Sentence.id = B.sentence_id
    GROUP BY subset
    ORDER BY subset;';
$subsets_completion = execute($sql, [], PDO::FETCH_ASSOC);

echo "<div>";
echo '<table id="overall_progress" border = "1" class="table table-striped"  style="width:100%">';
echo "<thead>";
echo "<tr>";
foreach ($subsets_completion[0] as $key => $value) {
    echo "<th>" . $key . "</th>";
}
echo "</tr>";
echo "</thead>";
echo "<tbody>";
foreach ($subsets_completion as $key => $v) {
    echo "<tr>";
    $count = count($v);
    foreach ($v as $k => $value) {
        echo "<td>" . $value . "</td>";
    }
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
echo "</div>";

echo '<script>  
        $(document).ready(function(){  
            $("#overall_progress").DataTable({
                lengthMenu: [
                    [-1],
                    ["All"],
                ],
                createdRow: function (row, data, index) {
                    if (data[0] == "TOTAL") {		
                        // $(row).css("backgroundColor", "#8DDF00"); 
                        $(row).css("font-weight", "bold");
                    }
                },
            });
        });  
    </script>';
echo "</div>";

//END
echo '</div>
    </div>
</div>';
// -- -- -- -- END: accordion-item -- -- -- --

// -- -- -- -- START: accordion-item -- -- -- --
echo '<div class="accordion-item">
      <div class="accordion-header" id="progressQuestion_heading">';
echo '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#progressQuestion_collapse" aria-expanded="false" aria-controls="progressQuestion_collapse">'; // CLOSE
//<!-- accordion-Header -->
echo '<H3>Progress per "Question" <sup></sup></H3>
					</button>
				</div>';
echo '<div id="progressQuestion_collapse" class="accordion-collapse collapse" aria-labelledby="progressQuestion_heading">'; // CLOSE
echo '<div class="accordion-body">';
//<!-- accordion-Body -->
//START

echo '<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>
					Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    "<br><br>";

echo "<br><b>Note:</b> Considers only annotations from top participants.";
//https://datatables.net/examples/api/multi_filter_select.html
$tmp = $top_participants_string;
$top_participants_string = "('Xiao', 'hzhang', 'chengkai', 'foram', 'zzy')";
$sql =
    'SELECT
        A.sentence_id, A.subset, A.screening, A.claim, A.tweet,
        -- A.claim, A.tweet,
        IFNULL( CONCAT("<b>", A.Label_0, "</b>: ", (select GROUP_CONCAT(username ORDER BY username ASC SEPARATOR ", ")  from Sentence_User where response = -1 and A.sentence_id = sentence_id and username in ' .
    $top_participants_string .
    ' ), "" ), "-") as "-1-neg",								
        IFNULL( CONCAT("<b>", A.Label_1, "</b>: ", (select GROUP_CONCAT(username ORDER BY username ASC SEPARATOR ", ")  from Sentence_User where response = 0 and A.sentence_id = sentence_id and username in ' .
    $top_participants_string .
    ' ), "" ), "-") as "0-neu",
        IFNULL( CONCAT("<b>", A.Label_2, "</b>: ", (select GROUP_CONCAT(username ORDER BY username ASC SEPARATOR ", ")  from Sentence_User where response = 1 and A.sentence_id = sentence_id and username in ' .
    $top_participants_string .
    ' ), "" ), "-") as "1-pos",
        IFNULL( CONCAT("<b>", A.Label_3, "</b>: ", (select GROUP_CONCAT(username ORDER BY username ASC SEPARATOR ", ")  from Sentence_User where response = 2 and A.sentence_id = sentence_id and username in ' .
    $top_participants_string .
    ' ), "" ), "-") as "2-dif",
        IFNULL( CONCAT("<b>", A.Label_4, "</b>: ", (select GROUP_CONCAT(username ORDER BY username ASC SEPARATOR ", ")  from Sentence_User where response = 3 and A.sentence_id = sentence_id and username in ' .
    $top_participants_string .
    ' ), "" ), "-") as "3-prb",
        ( A.Label_0 + A.Label_1 + A.Label_2 + A.Label_3 + A.Label_4) as totalAnnotations, /* TOP_QUALITY_SENTENCES01 */
        IF (A.screening>=-1, "YES", "NO") as Screening,
        IF (A.sentence_id in (select sentence_id from Training), "YES", "NO") as Train,
        IF (A.sentence_id in ' .
    $top_quality_sentences_string .
    ' , "YES", "NO"  ) as Finish,
        IF (A.sentence_id in ' .
    $top_quality_sentences_string .
    ', 
            CASE greatest(A.Label_0, A.Label_1, A.Label_2, A.Label_3, A.Label_4)
                WHEN A.Label_0 THEN "-1-neg"
                WHEN A.Label_1 THEN "0-neu"
                WHEN A.Label_2 THEN "1-pos"
                WHEN A.Label_3 THEN "2-dif"
                WHEN A.Label_4 THEN "3-prb"
        END, "-") as Label
     FROM (select s.id as sentence_id, s.screening, s.subset, s.tweet, s.claim,
                  sum(if(su.response = -1, 1, 0)) as Label_0, 
                  sum(if(su.response = 0, 1, 0)) as Label_1,	
                  sum(if(su.response = 1, 1, 0)) as Label_2,	
                  sum(if(su.response = 2, 1, 0)) as Label_3,	
                  sum(if(su.response = 3, 1, 0)) as Label_4
           from Sentence s 
           LEFT JOIN Sentence_User su  on s.id = su.sentence_id
           where su.sentence_id not in (select sentence_id from Training) and s.subset IN ("0", "1", "2", "3", "4", "5", "6", "7", "8", "9") and su.username in ' .
    $top_participants_string .
    '
    group by su.sentence_id, s.screening, s.subset, s.tweet, s.claim ) A;';
$results = execute($sql, [], PDO::FETCH_ASSOC);

$top_participants_string = $tmp;

echo "<div>";
echo '<table id="example" class="display" style="width:100%">';
echo "<thead>";
echo "<tr>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "sentence_id") == 0) {
        echo "<th>" . "id" . "</th>";
    } elseif (strcmp($key, "totalAnnotations") == 0) {
        echo "<th>" . "total" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "</tr>";
echo "</thead>";
echo "<tbody>";
foreach ($results as $key => $v) {
    echo "<tr>";
    $count = count($v);
    foreach ($v as $k => $value) {
        echo "<td>" . $value . "</td>";
    }
    echo "</tr>";
}
echo "</tbody>";
echo "<tfoot>";
echo "<tr>";
foreach ($results[0] as $key => $value) {
    echo "<th>" . $key . "</th>";
}
echo "</tr>";
echo "</tfoot>";
echo "</table>";
echo "</div>";
echo '
				<script> 
				$(document).ready(function () {
					$(\'#example\').DataTable({
                        lengthMenu: [
                            [25, 100, 200, -1],
                            [25, 100, 200, "All"],
                        ],
						initComplete: function () {
							this.api()
								.columns()
								.every(function () {
									var column = this;
									var select = $(\'<select><option value=""></option></select>\')
										.appendTo($(column.footer()).empty())
										.on(\'change\', function () {
											var val = $.fn.dataTable.util.escapeRegex($(this).val());
					
											column.search(val ? \'^\' + val + \'$\' : \'\', true, false).draw();
										});
					
									column
										.data()
										.unique()
										.sort()
										.each(function (d, j) {
											select.append(\'<option value="\' + d + \'">\' + d.substr(0,18) + \'</option>\');
										});
								});
						},



					});
				});
				</script>';

//END
echo '
					</div>
				</div>
			</div>';
// // -- -- -- -- END: accordion-item -- -- -- --

// -- -- -- -- START: accordion-item -- -- -- --
echo '<div class="accordion-item">
				<div class="accordion-header" id="annotators_heading">';
echo '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#annotators_collapse" aria-expanded="false" aria-controls="annotators_collapse">'; // CLOSE
//<!-- accordion-Header -->
echo '<H3>Label Distribution</H3>
					</button>
				</div>';
// echo '<div id="annotators_collapse" class="accordion-collapse collapse show" aria-labelledby="annotators_heading">'; // OPEN
echo '<div id="annotators_collapse" class="accordion-collapse collapse" aria-labelledby="annotators_heading">'; // CLOSE
echo '<div class="accordion-body">';
//<!-- accordion-Body -->';
//START
echo '<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>
					Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    "<br><br>";

echo "<b>Note:</b> Ignoring the Training phase. <br><br>";
echo '<div style= "padding-left:15px;">';
// echo '- <b><span style="color:#2952FC; font-weight:bold; font-size:large;">'.count($top_participants).'</span> </b> top-quality participants (RANK_W <= 0.0) of total '.$all_participants.'<br>';

$sql =
    'SELECT 	"SCREENING" as username,
                SUM(CASE WHEN screening >=-1 THEN 1 ELSE 0 END) As ANS,
                SUM(CASE WHEN screening = -1 THEN 1 ELSE 0 END) As neg,
                SUM(CASE WHEN screening = 0 THEN 1 ELSE 0 END) As neu,
                SUM(CASE WHEN screening = 1 THEN 1 ELSE 0 END) As pos,
                SUM(CASE WHEN screening = 2 THEN 1 ELSE 0 END) As dif,
                SUM(CASE WHEN screening = 3 THEN 1 ELSE 0 END) As prb,
                round((SUM(CASE WHEN (screening = -1) THEN 1 ELSE 0 END) / SUM(CASE WHEN (screening >=-1 ) THEN 1 ELSE 0 END))*100,2) As neg_percentage,
                round((SUM(CASE WHEN (screening = 0) THEN 1 ELSE 0 END)  / SUM(CASE WHEN (screening >=-1) THEN 1 ELSE 0 END) )*100,2) As neu_percentage,
                round((SUM(CASE WHEN (screening = 1) THEN 1 ELSE 0 END)  / SUM(CASE WHEN (screening >=-1) THEN 1 ELSE 0 END) )*100,2) As pos_percentage,
                round((SUM(CASE WHEN (screening = 2) THEN 1 ELSE 0 END) / SUM(CASE WHEN (screening >=-1 ) THEN 1 ELSE 0 END))*100,2) As dif_percentage,
                round((SUM(CASE WHEN (screening = 3) THEN 1 ELSE 0 END) / SUM(CASE WHEN (screening >=-1 ) THEN 1 ELSE 0 END))*100,2) As prb_percentage,
                "YES/NO" as top_participant
    FROM 		Sentence
    WHERE		id not in (select sentence_id from Training) 
    UNION
    SELECT 		username, 
                SUM(CASE WHEN (response >=-1 ) THEN 1 ELSE 0 END) As ANS,
                SUM(CASE WHEN response = -1 THEN 1 ELSE 0 END) As neg,
                SUM(CASE WHEN response = 0 THEN 1 ELSE 0 END) As neu,
                SUM(CASE WHEN response = 1 THEN 1 ELSE 0 END) As pos,
                SUM(CASE WHEN response = 2 THEN 1 ELSE 0 END) As dif,
                SUM(CASE WHEN response = 3 THEN 1 ELSE 0 END) As prb,
                round((SUM(CASE WHEN (response = -1) THEN 1 ELSE 0 END) / SUM(CASE WHEN (response >=-1 ) THEN 1 ELSE 0 END))*100,2) As neg_percentage,
                round((SUM(CASE WHEN (response = 0) THEN 1 ELSE 0 END)  / SUM(CASE WHEN (response >=-1) THEN 1 ELSE 0 END) )*100,2) As neu_percentage,
                round((SUM(CASE WHEN (response = 1) THEN 1 ELSE 0 END)  / SUM(CASE WHEN (response >=-1) THEN 1 ELSE 0 END) )*100,2) As pos_percentage,
                round((SUM(CASE WHEN (response = 2) THEN 1 ELSE 0 END) / SUM(CASE WHEN (response >=-1 ) THEN 1 ELSE 0 END))*100,2) As dif_percentage,
                round((SUM(CASE WHEN (response = 3) THEN 1 ELSE 0 END) / SUM(CASE WHEN (response >=-1 ) THEN 1 ELSE 0 END))*100,2) As prb_percentage,
                IF(username in ' .
    $top_participants_string .
    ', "YES", "NO") as top_participant
    FROM 		Sentence_User LEFT JOIN Sentence on Sentence_User.sentence_id = Sentence.id
    WHERE 		Sentence_User.sentence_id not in (select sentence_id from Training) 	
    GROUP BY 	username;';
$top_participants_complete = execute($sql, [], PDO::FETCH_ASSOC);

$ID = 1;
echo "<div>";
echo '<table id="participants" border = "1" class="display compact table table-striped"  style="width:100%">';
echo "<thead>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($top_participants_complete[0] as $key => $value) {
    if (strcmp($key, "pos_percentage") == 0) {
        echo "<th>" . "pos(%)" . "</th>";
    } elseif (strcmp($key, "neu_percentage") == 0) {
        echo "<th>" . "neu(%)" . "</th>";
    } elseif (strcmp($key, "neg_percentage") == 0) {
        echo "<th>" . "neg(%)" . "</th>";
    } elseif (strcmp($key, "dif_percentage") == 0) {
        echo "<th>" . "dif(%)" . "</th>";
    } elseif (strcmp($key, "prb_percentage") == 0) {
        echo "<th>" . "prb(%)" . "</th>";
    } elseif (strcmp($key, "top_participant") == 0) {
        echo "<th>" . "top_participant<sup>*</sup>" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "<th>" . "ACTIVE" . "</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
foreach ($top_participants_complete as $key => $v) {
    echo "<tr>";
    echo "<td>" . $ID . "</td>";
    foreach ($v as $k => $value) {
        echo "<td>" . $value . "</td>";
    }

    if (in_array($v["username"], $active_users)) {
        echo "<td> 1 </td>";
    } else {
        echo "<td> 0 </td>";
    }
    echo "</tr>";
    $ID = $ID + 1;
}
echo "</tbody>";
echo "</table>";

echo '<script type="tprb/javascript"> 
        $(document).ready(function(){  
            $("#participants").DataTable({
                lengthMenu: [
                    [15, 30, 50, -1],
                    [15, 30, 50, "All"]
                ],
                createdRow: function (row, data, index) {
                    if (data[11] == "1") {		
                        $(row).css("backgroundColor", "#8DDF00"); 
                    }
                    if (data[1] == "SCREENING") {		
                        $(row).css("backgroundColor", "#EEE8FE"); 
                        // $(row).css("font-weight", "bold");
                    }
                },
                columnDefs: [{ visible: false, targets: [11] }],
                order: [[0, "asc"]],
            });  
        });  
    </script>';
echo "<small><sup>*</sup>RANK_W <= 0.0</small>";
echo "</div>";

echo "</div>";

//END
echo '</div>
				</div>
			</div>';
// -- -- -- -- END: accordion-item -- -- -- --

// -- -- -- -- START: accordion-item -- -- -- --
echo '<div class="accordion-item">
				<div class="accordion-header" id="workers_heading">
					<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#workers_collapse_paid202412" aria-expanded="false" aria-controls="workers_collapse_paid202412">
						<!-- accordion-Header -->
						<H3>Initial trail (12/25/2024 - 01/25/2025) <sup></sup> </H3>
					</button>
				</div>
				<div id="workers_collapse_paid202412" class="accordion-collapse collapse" aria-labelledby="workers_heading">
					<div class="accordion-body">
					<!-- accordion-Body -->';
//START

echo '<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br> Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    "<br><br>";

/* Overall worker performance statistics */
echo '<div style= "padding-left:15px;">';
echo "<br><h4>Answered pairs >= 50</h4>";

$sql = 'select	USERNAME, 
                RANK_W, 
                round(A.avg_user/A.avg_total * A.ANSWERED * sign(0.3 - A.RANK_W)*pow((0.3 - A.RANK_W),2),2) AS RANK_L,
                if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) ),2), 0) as QUALITY,
                A.SKIP/A.ANSWERED as SKIP, 
                if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED),2), 0) as PAYRATE,
                if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED) * ANSWERED/100,2), 0) as PAYMENT,
                A.avg_user as LEN,
                A.email,
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
                        User.email as email,
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
                                Sentence.is_active=1 and 
                                sentence_id not in (select sentence_id from Training) and 
                                Sentence_User.time >= "2024-12-21 17:59:59" and Sentence_User.time <= "2025-01-25 22:00:00"
                    GROUP BY Sentence_User.username) A 
                ORDER BY PAYMENT desc, ANSWERED desc;';

$results = execute($sql, [], PDO::FETCH_ASSOC);

$ID = 1;
echo '<div style="width:100%" >';
echo '<table id="overall_paid202312" border = "1" class="display compact table table-striped"  style="width:100%">';
echo "<thead>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PRT_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . 'PMT_($)' . "</th>";
    } elseif (strcmp($key, "USERNAME") == 0) {
        echo "<th>" . "USER" . "</th>";
    } elseif (strcmp($key, "ANSWERED") == 0) {
        echo "<th>" . "ANS" . "</th>";
    } elseif (strcmp($key, "neg_prb") == 0) {
        echo '<th style = "color:purple;">' . "neg_prb" . "</th>";
    } elseif (strcmp($key, "prb_neg") == 0) {
        echo '<th style = "color:purple;">' . "prb_neg" . "</th>";
    } elseif (strcmp($key, "neg_dif") == 0) {
        echo '<th style = "color:red;">' . "neg_dif" . "</th>";
    } elseif (strcmp($key, "dif_neg") == 0) {
        echo '<th style = "color:red;">' . "dif_neg" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th style = "color:red;">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th style = "color:red;">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "<th>" . "ACTIVE" . "</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
foreach ($results as $key => $v) {
    echo "<tr>";
    echo "<td>" . $ID . "</td>";
    foreach ($v as $k => $value) {
        echo "<td>" . $value . "</td>";
    }
    if (in_array($v["USERNAME"], $active_users)) {
        echo "<td> 1 </td>";
    } else {
        echo "<td> 0 </td>";
    }
    echo "</tr>";
    $ID = $ID + 1;
}
echo "</tbody>";
echo "<tfoot>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PRT_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . 'PMT_($)' . "</th>";
    } elseif (strcmp($key, "USERNAME") == 0) {
        echo "<th>" . "USER" . "</th>";
    } elseif (strcmp($key, "ANSWERED") == 0) {
        echo "<th>" . "ANS" . "</th>";
    } elseif (strcmp($key, "neg_prb") == 0) {
        echo '<th style = "color:purple;">' . "neg_prb" . "</th>";
    } elseif (strcmp($key, "prb_neg") == 0) {
        echo '<th style = "color:purple;">' . "prb_neg" . "</th>";
    } elseif (strcmp($key, "neg_dif") == 0) {
        echo '<th style = "color:red;">' . "neg_dif" . "</th>";
    } elseif (strcmp($key, "dif_neg") == 0) {
        echo '<th style = "color:red;">' . "dif_neg" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th style = "color:red;">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th style = "color:red;">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "<th>" . "ACTIVE" . "</th>";
echo "</tr>";
echo "</tfoot>";
echo "</table>";
echo "</div>";

echo '
<script type="text/javascript"> 
    $(document).ready(function(){  
        $("#overall_paid202412").DataTable({
            // lengthMenu: [
            //     [15, 30, 50, -1],
            //     [15, 30, 50, "All"],
            // ],
            createdRow: function (row, data, index) {
                if (data[41] == "1") {		
                    $(row).css("backgroundColor", "#8DDF00"); 
                }
            },
            // columnDefs: [{ visible: false, targets: [5, 12, 13, 14, 15, 16, 17, 43] }],
            // order: [[10, "desc"],[3, "desc"]],
        });  
});  
</script>';

//END

// echo '<h4>Active Participants in last <b>15</b> minutes are marked below:</h4>';
$sql = 'SELECT 	USERNAME, min(minute(timediff(now(), time))) as mins 
									FROM	Sentence_User  
									WHERE 	minute(timediff(now(), time)) <= 15 and hour(timediff(now(), time)) = 0 /*and sentence_id NOT IN (select sentence_id from Training)*/
									GROUP BY username
									ORDER BY mins desc;';
$results = execute($sql, [], PDO::FETCH_ASSOC);
// var_dump($results);
// echo 'q:'.$sql;
$active_users = [];
foreach ($results as $key => $v) {
    array_push($active_users, $v["USERNAME"]);
}
// var_dump($active_users);
if (!$results) {
    echo "...none<br>";
} else {
    foreach ($results as $key => $v) {
        echo "- [" . $v["mins"] . " mins] " . $v["USERNAME"] . "<br>";
    }
}

// echo '- <b><span style="color:#2952FC; font-weight:bold; font-size:large;">'.$total_labels[0].'</span></b> labels collected so far!<br>';
// echo '- <b><span style="color:#2952FC; font-weight:bold; font-size:large;">'.$total_top_labels[0].'</span></b> are top-quality labels<br><br>';
$sql = 'SELECT	A.username, count(*) as count 
									FROM	(SELECT * FROM Sentence_User WHERE response != -2 and sentence_id NOT IN (select sentence_id from Training) ORDER BY time DESC limit 100) A 
									GROUP BY A.username ORDER BY time DESC/* COUNT(*) DESC */;';
$results = execute($sql, [], PDO::FETCH_ASSOC);
echo "<br><br><h4>Latest (100) pair labelers:</h4>";
foreach ($results as $key => $v) {
    echo "- " . $v["username"] . " (" . $v["count"] . "), <br>";
}
echo '<br><br>
							
							</div><br>
							</div>

							<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>
							Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    '<br>
							</div>

			</div>';
// -- -- -- -- END: accordion-item -- -- -- --

// -- -- -- -- START: accordion-item -- -- -- --
echo '<div class="accordion-item">
				<div class="accordion-header" id="workers_heading">
					<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#workers_collapse_paid202312" aria-expanded="false" aria-controls="workers_collapse_paid202312">
						<!-- accordion-Header -->
						<H3>Paid Phase 5 (12/25/2023 - 01/25/2024) <sup></sup> </H3>
					</button>
				</div>
				<div id="workers_collapse_paid202312" class="accordion-collapse collapse" aria-labelledby="workers_heading">
					<div class="accordion-body">
					<!-- accordion-Body -->';
//START

echo '<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br> Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    "<br><br>";

/* Overall worker performance statistics */
echo '<div style= "padding-left:15px;">';
echo "<br><h4>Answered pairs >= 50</h4>";

$sql = 'select	USERNAME, 
                RANK_W, 
                round(A.avg_user/A.avg_total * A.ANSWERED * sign(0.3 - A.RANK_W)*pow((0.3 - A.RANK_W),2),2) AS RANK_L,
                if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) ),2), 0) as QUALITY,
                A.SKIP/A.ANSWERED as SKIP, 
                if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED),2), 0) as PAYRATE,
                if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED) * ANSWERED/100,2), 0) as PAYMENT,
                A.avg_user as LEN,
                A.email,
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
                        User.email as email,
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
                                Sentence.is_active=1 and 
                                sentence_id not in (select sentence_id from Training) and 
                                Sentence_User.time >= "2023-12-25 17:59:59" and Sentence_User.time <= "2024-01-25 22:00:00"
                    GROUP BY Sentence_User.username) A 
                ORDER BY PAYMENT desc, ANSWERED desc;';

$results = execute($sql, [], PDO::FETCH_ASSOC);

$ID = 1;
echo '<div style="width:100%" >';
echo '<table id="overall_paid202412" border = "1" class="display compact table table-striped"  style="width:100%">';
echo "<thead>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PRT_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . 'PMT_($)' . "</th>";
    } elseif (strcmp($key, "USERNAME") == 0) {
        echo "<th>" . "USER" . "</th>";
    } elseif (strcmp($key, "ANSWERED") == 0) {
        echo "<th>" . "ANS" . "</th>";
    } elseif (strcmp($key, "neg_prb") == 0) {
        echo '<th style = "color:purple;">' . "neg_prb" . "</th>";
    } elseif (strcmp($key, "prb_neg") == 0) {
        echo '<th style = "color:purple;">' . "prb_neg" . "</th>";
    } elseif (strcmp($key, "neg_dif") == 0) {
        echo '<th style = "color:red;">' . "neg_dif" . "</th>";
    } elseif (strcmp($key, "dif_neg") == 0) {
        echo '<th style = "color:red;">' . "dif_neg" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th style = "color:red;">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th style = "color:red;">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "<th>" . "ACTIVE" . "</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
foreach ($results as $key => $v) {
    echo "<tr>";
    echo "<td>" . $ID . "</td>";
    foreach ($v as $k => $value) {
        echo "<td>" . $value . "</td>";
    }
    if (in_array($v["USERNAME"], $active_users)) {
        echo "<td> 1 </td>";
    } else {
        echo "<td> 0 </td>";
    }
    echo "</tr>";
    $ID = $ID + 1;
}
echo "</tbody>";
echo "<tfoot>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PRT_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . 'PMT_($)' . "</th>";
    } elseif (strcmp($key, "USERNAME") == 0) {
        echo "<th>" . "USER" . "</th>";
    } elseif (strcmp($key, "ANSWERED") == 0) {
        echo "<th>" . "ANS" . "</th>";
    } elseif (strcmp($key, "neg_prb") == 0) {
        echo '<th style = "color:purple;">' . "neg_prb" . "</th>";
    } elseif (strcmp($key, "prb_neg") == 0) {
        echo '<th style = "color:purple;">' . "prb_neg" . "</th>";
    } elseif (strcmp($key, "neg_dif") == 0) {
        echo '<th style = "color:red;">' . "neg_dif" . "</th>";
    } elseif (strcmp($key, "dif_neg") == 0) {
        echo '<th style = "color:red;">' . "dif_neg" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th style = "color:red;">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th style = "color:red;">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "<th>" . "ACTIVE" . "</th>";
echo "</tr>";
echo "</tfoot>";
echo "</table>";
echo "</div>";

echo '
<script type="text/javascript"> 
    $(document).ready(function(){  
        $("#overall_paid202312").DataTable({
            // lengthMenu: [
            //     [15, 30, 50, -1],
            //     [15, 30, 50, "All"],
            // ],
            createdRow: function (row, data, index) {
                if (data[41] == "1") {		
                    $(row).css("backgroundColor", "#8DDF00"); 
                }
            },
            // columnDefs: [{ visible: false, targets: [5, 12, 13, 14, 15, 16, 17, 43] }],
            // order: [[10, "desc"],[3, "desc"]],
        });  
});  
</script>';

//END

// echo '<h4>Active Participants in last <b>15</b> minutes are marked below:</h4>';
$sql = 'SELECT 	USERNAME, min(minute(timediff(now(), time))) as mins 
									FROM	Sentence_User  
									WHERE 	minute(timediff(now(), time)) <= 15 and hour(timediff(now(), time)) = 0 /*and sentence_id NOT IN (select sentence_id from Training)*/
									GROUP BY username
									ORDER BY mins desc;';
$results = execute($sql, [], PDO::FETCH_ASSOC);
// var_dump($results);
// echo 'q:'.$sql;
$active_users = [];
foreach ($results as $key => $v) {
    array_push($active_users, $v["USERNAME"]);
}
// var_dump($active_users);
if (!$results) {
    echo "...none<br>";
} else {
    foreach ($results as $key => $v) {
        echo "- [" . $v["mins"] . " mins] " . $v["USERNAME"] . "<br>";
    }
}

// echo '- <b><span style="color:#2952FC; font-weight:bold; font-size:large;">'.$total_labels[0].'</span></b> labels collected so far!<br>';
// echo '- <b><span style="color:#2952FC; font-weight:bold; font-size:large;">'.$total_top_labels[0].'</span></b> are top-quality labels<br><br>';
$sql = 'SELECT	A.username, count(*) as count 
									FROM	(SELECT * FROM Sentence_User WHERE response != -2 and sentence_id NOT IN (select sentence_id from Training) ORDER BY time DESC limit 100) A 
									GROUP BY A.username ORDER BY time DESC/* COUNT(*) DESC */;';
$results = execute($sql, [], PDO::FETCH_ASSOC);
echo "<br><br><h4>Latest (100) pair labelers:</h4>";
foreach ($results as $key => $v) {
    echo "- " . $v["username"] . " (" . $v["count"] . "), <br>";
}
echo '<br><br>
							
							</div><br>
							</div>

							<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>
							Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    '<br>
							</div>

			</div>';
// -- -- -- -- END: accordion-item -- -- -- --

// -- -- -- -- START: accordion-item -- -- -- --
echo '<div class="accordion-item">
				<div class="accordion-header" id="workers_heading">
					<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#workers_collapse_paid202311" aria-expanded="false" aria-controls="workers_collapse_paid202311">
						<!-- accordion-Header -->
						<H3>Paid Phase 4 (11/25/2023 - 12/25/2023) <sup></sup> </H3>
					</button>
				</div>
				<div id="workers_collapse_paid202311" class="accordion-collapse collapse" aria-labelledby="workers_heading">
					<div class="accordion-body">
					<!-- accordion-Body -->';
//START

echo '<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br> Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    "<br><br>";

/* Overall worker performance statistics */
echo '<div style= "padding-left:15px;">';
echo "<br><h4>Answered pairs >= 50</h4>";

$sql = 'select	USERNAME, 
                RANK_W, 
                round(A.avg_user/A.avg_total * A.ANSWERED * sign(0.3 - A.RANK_W)*pow((0.3 - A.RANK_W),2),2) AS RANK_L,
                if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) ),2), 0) as QUALITY,
                A.SKIP/A.ANSWERED as SKIP, 
                if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED),2), 0) as PAYRATE,
                if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED) * ANSWERED/100,2), 0) as PAYMENT,
                A.avg_user as LEN,
                A.email,
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
                        User.email as email,
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
                                Sentence.is_active=1 and 
                                sentence_id not in (select sentence_id from Training) and 
                                Sentence_User.time >= "2023-11-25 23:59:59" and Sentence_User.time <= "2023-12-25 23:59:59"
                    GROUP BY Sentence_User.username) A 
                ORDER BY PAYMENT desc, ANSWERED desc;';

$results = execute($sql, [], PDO::FETCH_ASSOC);

$ID = 1;
echo '<div style="width:100%" >';
echo '<table id="overall_paid202311" border = "1" class="display compact table table-striped"  style="width:100%">';
echo "<thead>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PRT_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . 'PMT_($)' . "</th>";
    } elseif (strcmp($key, "USERNAME") == 0) {
        echo "<th>" . "USER" . "</th>";
    } elseif (strcmp($key, "ANSWERED") == 0) {
        echo "<th>" . "ANS" . "</th>";
    } elseif (strcmp($key, "neg_prb") == 0) {
        echo '<th style = "color:purple;">' . "neg_prb" . "</th>";
    } elseif (strcmp($key, "prb_neg") == 0) {
        echo '<th style = "color:purple;">' . "prb_neg" . "</th>";
    } elseif (strcmp($key, "neg_dif") == 0) {
        echo '<th style = "color:red;">' . "neg_dif" . "</th>";
    } elseif (strcmp($key, "dif_neg") == 0) {
        echo '<th style = "color:red;">' . "dif_neg" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th style = "color:red;">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th style = "color:red;">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "<th>" . "ACTIVE" . "</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
foreach ($results as $key => $v) {
    echo "<tr>";
    echo "<td>" . $ID . "</td>";
    foreach ($v as $k => $value) {
        echo "<td>" . $value . "</td>";
    }
    if (in_array($v["USERNAME"], $active_users)) {
        echo "<td> 1 </td>";
    } else {
        echo "<td> 0 </td>";
    }
    echo "</tr>";
    $ID = $ID + 1;
}
echo "</tbody>";
echo "<tfoot>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PRT_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . 'PMT_($)' . "</th>";
    } elseif (strcmp($key, "USERNAME") == 0) {
        echo "<th>" . "USER" . "</th>";
    } elseif (strcmp($key, "ANSWERED") == 0) {
        echo "<th>" . "ANS" . "</th>";
    } elseif (strcmp($key, "neg_prb") == 0) {
        echo '<th style = "color:purple;">' . "neg_prb" . "</th>";
    } elseif (strcmp($key, "prb_neg") == 0) {
        echo '<th style = "color:purple;">' . "prb_neg" . "</th>";
    } elseif (strcmp($key, "neg_dif") == 0) {
        echo '<th style = "color:red;">' . "neg_dif" . "</th>";
    } elseif (strcmp($key, "dif_neg") == 0) {
        echo '<th style = "color:red;">' . "dif_neg" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th style = "color:red;">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th style = "color:red;">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "<th>" . "ACTIVE" . "</th>";
echo "</tr>";
echo "</tfoot>";
echo "</table>";
echo "</div>";

echo '
<script type="text/javascript"> 
    $(document).ready(function(){  
        $("#overall_paid202311").DataTable({
            // lengthMenu: [
            //     [15, 30, 50, -1],
            //     [15, 30, 50, "All"],
            // ],
            createdRow: function (row, data, index) {
                if (data[41] == "1") {		
                    $(row).css("backgroundColor", "#8DDF00"); 
                }
            },
            // columnDefs: [{ visible: false, targets: [5, 12, 13, 14, 15, 16, 17, 43] }],
            // order: [[10, "desc"],[3, "desc"]],
        });  
});  
</script>';

//END

// echo '<h4>Active Participants in last <b>15</b> minutes are marked below:</h4>';
$sql = 'SELECT 	USERNAME, min(minute(timediff(now(), time))) as mins 
									FROM	Sentence_User  
									WHERE 	minute(timediff(now(), time)) <= 15 and hour(timediff(now(), time)) = 0 /*and sentence_id NOT IN (select sentence_id from Training)*/
									GROUP BY username
									ORDER BY mins desc;';
$results = execute($sql, [], PDO::FETCH_ASSOC);
// var_dump($results);
// echo 'q:'.$sql;
$active_users = [];
foreach ($results as $key => $v) {
    array_push($active_users, $v["USERNAME"]);
}
// var_dump($active_users);
if (!$results) {
    echo "...none<br>";
} else {
    foreach ($results as $key => $v) {
        echo "- [" . $v["mins"] . " mins] " . $v["USERNAME"] . "<br>";
    }
}

// echo '- <b><span style="color:#2952FC; font-weight:bold; font-size:large;">'.$total_labels[0].'</span></b> labels collected so far!<br>';
// echo '- <b><span style="color:#2952FC; font-weight:bold; font-size:large;">'.$total_top_labels[0].'</span></b> are top-quality labels<br><br>';
$sql = 'SELECT	A.username, count(*) as count 
									FROM	(SELECT * FROM Sentence_User WHERE response != -2 and sentence_id NOT IN (select sentence_id from Training) ORDER BY time DESC limit 100) A 
									GROUP BY A.username ORDER BY time DESC/* COUNT(*) DESC */;';
$results = execute($sql, [], PDO::FETCH_ASSOC);
echo "<br><br><h4>Latest (100) pair labelers:</h4>";
foreach ($results as $key => $v) {
    echo "- " . $v["username"] . " (" . $v["count"] . "), <br>";
}
echo '<br><br>
							
							</div><br>
							</div>

							<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>
							Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    '<br>
							</div>

			</div>';
// -- -- -- -- END: accordion-item -- -- -- --

// -- -- -- -- START: accordion-item -- -- -- --
echo '<div class="accordion-item">
				<div class="accordion-header" id="workers_heading">
					<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#workers_collapse_paid202310" aria-expanded="false" aria-controls="workers_collapse_paid202310">
						<!-- accordion-Header -->
						<H3>Paid Phase 3 (10/25/2023 - 11/25/2023) <sup></sup> </H3>
					</button>
				</div>
				<div id="workers_collapse_paid202310" class="accordion-collapse collapse" aria-labelledby="workers_heading">
					<div class="accordion-body">
					<!-- accordion-Body -->';
//START

echo '<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br> Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    "<br><br>";

/* Overall worker performance statistics */
echo '<div style= "padding-left:15px;">';
echo "<br><h4>Answered pairs >= 50</h4>";

$sql = 'select	USERNAME, 
                RANK_W, 
                round(A.avg_user/A.avg_total * A.ANSWERED * sign(0.3 - A.RANK_W)*pow((0.3 - A.RANK_W),2),2) AS RANK_L,
                if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) ),2), 0) as QUALITY,
                A.SKIP/A.ANSWERED as SKIP, 
                if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED),2), 0) as PAYRATE,
                if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED) * ANSWERED/100,2), 0) as PAYMENT,
                A.avg_user as LEN,
                A.email,
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
                        User.email as email,
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
                                Sentence.is_active=1 and 
                                sentence_id not in (select sentence_id from Training) and 
                                Sentence_User.time >= "2023-10-25 23:59:59" and Sentence_User.time <= "2023-11-25 23:59:59"
                    GROUP BY Sentence_User.username) A 
                ORDER BY PAYMENT desc, ANSWERED desc;';

$results = execute($sql, [], PDO::FETCH_ASSOC);

$ID = 1;
echo '<div style="width:100%" >';
echo '<table id="overall_paid202310" border = "1" class="display compact table table-striped"  style="width:100%">';
echo "<thead>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PRT_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . 'PMT_($)' . "</th>";
    } elseif (strcmp($key, "USERNAME") == 0) {
        echo "<th>" . "USER" . "</th>";
    } elseif (strcmp($key, "ANSWERED") == 0) {
        echo "<th>" . "ANS" . "</th>";
    } elseif (strcmp($key, "neg_prb") == 0) {
        echo '<th style = "color:purple;">' . "neg_prb" . "</th>";
    } elseif (strcmp($key, "prb_neg") == 0) {
        echo '<th style = "color:purple;">' . "prb_neg" . "</th>";
    } elseif (strcmp($key, "neg_dif") == 0) {
        echo '<th style = "color:red;">' . "neg_dif" . "</th>";
    } elseif (strcmp($key, "dif_neg") == 0) {
        echo '<th style = "color:red;">' . "dif_neg" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th style = "color:red;">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th style = "color:red;">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "<th>" . "ACTIVE" . "</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
foreach ($results as $key => $v) {
    echo "<tr>";
    echo "<td>" . $ID . "</td>";
    foreach ($v as $k => $value) {
        echo "<td>" . $value . "</td>";
    }
    if (in_array($v["USERNAME"], $active_users)) {
        echo "<td> 1 </td>";
    } else {
        echo "<td> 0 </td>";
    }
    echo "</tr>";
    $ID = $ID + 1;
}
echo "</tbody>";
echo "<tfoot>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PRT_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . 'PMT_($)' . "</th>";
    } elseif (strcmp($key, "USERNAME") == 0) {
        echo "<th>" . "USER" . "</th>";
    } elseif (strcmp($key, "ANSWERED") == 0) {
        echo "<th>" . "ANS" . "</th>";
    } elseif (strcmp($key, "neg_prb") == 0) {
        echo '<th style = "color:purple;">' . "neg_prb" . "</th>";
    } elseif (strcmp($key, "prb_neg") == 0) {
        echo '<th style = "color:purple;">' . "prb_neg" . "</th>";
    } elseif (strcmp($key, "neg_dif") == 0) {
        echo '<th style = "color:red;">' . "neg_dif" . "</th>";
    } elseif (strcmp($key, "dif_neg") == 0) {
        echo '<th style = "color:red;">' . "dif_neg" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th style = "color:red;">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th style = "color:red;">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "<th>" . "ACTIVE" . "</th>";
echo "</tr>";
echo "</tfoot>";
echo "</table>";
echo "</div>";

echo '
<script type="text/javascript"> 
    $(document).ready(function(){  
        $("#overall_paid202310").DataTable({
            // lengthMenu: [
            //     [15, 30, 50, -1],
            //     [15, 30, 50, "All"],
            // ],
            createdRow: function (row, data, index) {
                if (data[41] == "1") {		
                    $(row).css("backgroundColor", "#8DDF00"); 
                }
            },
            // columnDefs: [{ visible: false, targets: [5, 12, 13, 14, 15, 16, 17, 43] }],
            // order: [[10, "desc"],[3, "desc"]],
        });  
});  
</script>';

//END

// echo '<h4>Active Participants in last <b>15</b> minutes are marked below:</h4>';
$sql = 'SELECT 	USERNAME, min(minute(timediff(now(), time))) as mins 
									FROM	Sentence_User  
									WHERE 	minute(timediff(now(), time)) <= 15 and hour(timediff(now(), time)) = 0 /*and sentence_id NOT IN (select sentence_id from Training)*/
									GROUP BY username
									ORDER BY mins desc;';
$results = execute($sql, [], PDO::FETCH_ASSOC);
// var_dump($results);
// echo 'q:'.$sql;
$active_users = [];
foreach ($results as $key => $v) {
    array_push($active_users, $v["USERNAME"]);
}
// var_dump($active_users);
if (!$results) {
    echo "...none<br>";
} else {
    foreach ($results as $key => $v) {
        echo "- [" . $v["mins"] . " mins] " . $v["USERNAME"] . "<br>";
    }
}

// echo '- <b><span style="color:#2952FC; font-weight:bold; font-size:large;">'.$total_labels[0].'</span></b> labels collected so far!<br>';
// echo '- <b><span style="color:#2952FC; font-weight:bold; font-size:large;">'.$total_top_labels[0].'</span></b> are top-quality labels<br><br>';
$sql = 'SELECT	A.username, count(*) as count 
									FROM	(SELECT * FROM Sentence_User WHERE response != -2 and sentence_id NOT IN (select sentence_id from Training) ORDER BY time DESC limit 100) A 
									GROUP BY A.username ORDER BY time DESC/* COUNT(*) DESC */;';
$results = execute($sql, [], PDO::FETCH_ASSOC);
echo "<br><br><h4>Latest (100) pair labelers:</h4>";
foreach ($results as $key => $v) {
    echo "- " . $v["username"] . " (" . $v["count"] . "), <br>";
}
echo '<br><br>
							
							</div><br>
							</div>

							<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>
							Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    '<br>
							</div>

			</div>';
// -- -- -- -- END: accordion-item -- -- -- --

// -- -- -- -- START: accordion-item -- -- -- --
echo '<div class="accordion-item">
				<div class="accordion-header" id="workers_heading">
					<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#workers_collapse_paid202309" aria-expanded="false" aria-controls="workers_collapse_paid202309">
						<!-- accordion-Header -->
						<H3>Paid Phase 2 (09/25/2023 - 10/25/2023) <sup></sup> </H3>
					</button>
				</div>
				<div id="workers_collapse_paid202309" class="accordion-collapse collapse" aria-labelledby="workers_heading">
					<div class="accordion-body">
					<!-- accordion-Body -->';
//START

echo '<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br> Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    "<br><br>";

/* Overall worker performance statistics */
echo '<div style= "padding-left:15px;">';
echo "<br><h4>Answered pairs >= 50</h4>";

$sql = 'select	USERNAME, 
                RANK_W, 
                round(A.avg_user/A.avg_total * A.ANSWERED * sign(0.3 - A.RANK_W)*pow((0.3 - A.RANK_W),2),2) AS RANK_L,
                if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) ),2), 0) as QUALITY,
                A.SKIP/A.ANSWERED as SKIP, 
                if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED),2), 0) as PAYRATE,
                if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED) * ANSWERED/100,2), 0) as PAYMENT,
                A.avg_user as LEN,
                A.email,
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
                        User.email as email,
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
                                Sentence.is_active=1 and 
                                sentence_id not in (select sentence_id from Training) and 
                                Sentence_User.time >= "2023-09-25 22:00:00" and Sentence_User.time <= "2023-10-25 22:00:00"
                    GROUP BY Sentence_User.username) A 
                ORDER BY PAYMENT desc, ANSWERED desc;';

$results = execute($sql, [], PDO::FETCH_ASSOC);

$ID = 1;
echo '<div style="width:100%" >';
echo '<table id="overall_paid202309" border = "1" class="display compact table table-striped"  style="width:100%">';
echo "<thead>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PRT_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . 'PMT_($)' . "</th>";
    } elseif (strcmp($key, "USERNAME") == 0) {
        echo "<th>" . "USER" . "</th>";
    } elseif (strcmp($key, "ANSWERED") == 0) {
        echo "<th>" . "ANS" . "</th>";
    } elseif (strcmp($key, "neg_prb") == 0) {
        echo '<th style = "color:purple;">' . "neg_prb" . "</th>";
    } elseif (strcmp($key, "prb_neg") == 0) {
        echo '<th style = "color:purple;">' . "prb_neg" . "</th>";
    } elseif (strcmp($key, "neg_dif") == 0) {
        echo '<th style = "color:red;">' . "neg_dif" . "</th>";
    } elseif (strcmp($key, "dif_neg") == 0) {
        echo '<th style = "color:red;">' . "dif_neg" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th style = "color:red;">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th style = "color:red;">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "<th>" . "ACTIVE" . "</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
foreach ($results as $key => $v) {
    echo "<tr>";
    echo "<td>" . $ID . "</td>";
    foreach ($v as $k => $value) {
        echo "<td>" . $value . "</td>";
    }
    if (in_array($v["USERNAME"], $active_users)) {
        echo "<td> 1 </td>";
    } else {
        echo "<td> 0 </td>";
    }
    echo "</tr>";
    $ID = $ID + 1;
}
echo "</tbody>";
echo "<tfoot>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PRT_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . 'PMT_($)' . "</th>";
    } elseif (strcmp($key, "USERNAME") == 0) {
        echo "<th>" . "USER" . "</th>";
    } elseif (strcmp($key, "ANSWERED") == 0) {
        echo "<th>" . "ANS" . "</th>";
    } elseif (strcmp($key, "neg_prb") == 0) {
        echo '<th style = "color:purple;">' . "neg_prb" . "</th>";
    } elseif (strcmp($key, "prb_neg") == 0) {
        echo '<th style = "color:purple;">' . "prb_neg" . "</th>";
    } elseif (strcmp($key, "neg_dif") == 0) {
        echo '<th style = "color:red;">' . "neg_dif" . "</th>";
    } elseif (strcmp($key, "dif_neg") == 0) {
        echo '<th style = "color:red;">' . "dif_neg" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th style = "color:red;">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th style = "color:red;">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "<th>" . "ACTIVE" . "</th>";
echo "</tr>";
echo "</tfoot>";
echo "</table>";
echo "</div>";

echo '
<script type="text/javascript"> 
    $(document).ready(function(){  
        $("#overall_paid202309").DataTable({
            // lengthMenu: [
            //     [15, 30, 50, -1],
            //     [15, 30, 50, "All"],
            // ],
            createdRow: function (row, data, index) {
                if (data[41] == "1") {		
                    $(row).css("backgroundColor", "#8DDF00"); 
                }
            },
            // columnDefs: [{ visible: false, targets: [5, 12, 13, 14, 15, 16, 17, 43] }],
            // order: [[10, "desc"],[3, "desc"]],
        });  
});  
</script>';

//END

// echo '<h4>Active Participants in last <b>15</b> minutes are marked below:</h4>';
$sql = 'SELECT 	USERNAME, min(minute(timediff(now(), time))) as mins 
									FROM	Sentence_User  
									WHERE 	minute(timediff(now(), time)) <= 15 and hour(timediff(now(), time)) = 0 /*and sentence_id NOT IN (select sentence_id from Training)*/
									GROUP BY username
									ORDER BY mins desc;';
$results = execute($sql, [], PDO::FETCH_ASSOC);
// var_dump($results);
// echo 'q:'.$sql;
$active_users = [];
foreach ($results as $key => $v) {
    array_push($active_users, $v["USERNAME"]);
}
// var_dump($active_users);
if (!$results) {
    echo "...none<br>";
} else {
    foreach ($results as $key => $v) {
        echo "- [" . $v["mins"] . " mins] " . $v["USERNAME"] . "<br>";
    }
}

// echo '- <b><span style="color:#2952FC; font-weight:bold; font-size:large;">'.$total_labels[0].'</span></b> labels collected so far!<br>';
// echo '- <b><span style="color:#2952FC; font-weight:bold; font-size:large;">'.$total_top_labels[0].'</span></b> are top-quality labels<br><br>';
$sql = 'SELECT	A.username, count(*) as count 
									FROM	(SELECT * FROM Sentence_User WHERE response != -2 and sentence_id NOT IN (select sentence_id from Training) ORDER BY time DESC limit 100) A 
									GROUP BY A.username ORDER BY time DESC/* COUNT(*) DESC */;';
$results = execute($sql, [], PDO::FETCH_ASSOC);
echo "<br><br><h4>Latest (100) pair labelers:</h4>";
foreach ($results as $key => $v) {
    echo "- " . $v["username"] . " (" . $v["count"] . "), <br>";
}
echo '<br><br>
							
							</div><br>
							</div>

							<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>
							Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    '<br>
							</div>

			</div>';
// -- -- -- -- END: accordion-item -- -- -- --

// -- -- -- -- START: accordion-item -- -- -- --
echo '<div class="accordion-item">
				<div class="accordion-header" id="workers_heading">
					<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#workers_collapse_paid202308" aria-expanded="false" aria-controls="workers_collapse_paid202308">
						<!-- accordion-Header -->
						<H3>Paid Phase 1 (08/25/2023 - 09/25/2023) <sup></sup> </H3>
					</button>
				</div>
				<div id="workers_collapse_paid202308" class="accordion-collapse collapse" aria-labelledby="workers_heading">
					<div class="accordion-body">
					<!-- accordion-Body -->';
//START

echo '<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br> Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    "<br><br>";

/* Overall worker performance statistics */
echo '<div style= "padding-left:15px;">';
echo "<br><h4>Answered pairs >= 50</h4>";

$sql = 'select	USERNAME, 
                RANK_W, 
                round(A.avg_user/A.avg_total * A.ANSWERED * sign(0.3 - A.RANK_W)*pow((0.3 - A.RANK_W),2),2) AS RANK_L,
                if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) ),2), 0) as QUALITY,
                A.SKIP/A.ANSWERED as SKIP, 
                if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED),2), 0) as PAYRATE,
                if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED) * ANSWERED/100,2), 0) as PAYMENT,
                A.avg_user as LEN,
                A.email,
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
                        User.email as email,
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
                                Sentence.is_active=1 and 
                                sentence_id not in (select sentence_id from Training) and 
                                Sentence_User.time >= "2023-08-25 22:00:00" and Sentence_User.time <= "2023-09-25 22:00:00"
                    GROUP BY Sentence_User.username) A 
                ORDER BY PAYMENT desc, ANSWERED desc;';

$results = execute($sql, [], PDO::FETCH_ASSOC);

$ID = 1;
echo '<div style="width:100%" >';
echo '<table id="overall_paid202308" border = "1" class="display compact table table-striped"  style="width:100%">';
echo "<thead>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PRT_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . 'PMT_($)' . "</th>";
    } elseif (strcmp($key, "USERNAME") == 0) {
        echo "<th>" . "USER" . "</th>";
    } elseif (strcmp($key, "ANSWERED") == 0) {
        echo "<th>" . "ANS" . "</th>";
    } elseif (strcmp($key, "neg_prb") == 0) {
        echo '<th style = "color:purple;">' . "neg_prb" . "</th>";
    } elseif (strcmp($key, "prb_neg") == 0) {
        echo '<th style = "color:purple;">' . "prb_neg" . "</th>";
    } elseif (strcmp($key, "neg_dif") == 0) {
        echo '<th style = "color:red;">' . "neg_dif" . "</th>";
    } elseif (strcmp($key, "dif_neg") == 0) {
        echo '<th style = "color:red;">' . "dif_neg" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th style = "color:red;">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th style = "color:red;">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "<th>" . "ACTIVE" . "</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
foreach ($results as $key => $v) {
    echo "<tr>";
    echo "<td>" . $ID . "</td>";
    foreach ($v as $k => $value) {
        echo "<td>" . $value . "</td>";
    }
    if (in_array($v["USERNAME"], $active_users)) {
        echo "<td> 1 </td>";
    } else {
        echo "<td> 0 </td>";
    }
    echo "</tr>";
    $ID = $ID + 1;
}
echo "</tbody>";
echo "<tfoot>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PRT_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . 'PMT_($)' . "</th>";
    } elseif (strcmp($key, "USERNAME") == 0) {
        echo "<th>" . "USER" . "</th>";
    } elseif (strcmp($key, "ANSWERED") == 0) {
        echo "<th>" . "ANS" . "</th>";
    } elseif (strcmp($key, "neg_prb") == 0) {
        echo '<th style = "color:purple;">' . "neg_prb" . "</th>";
    } elseif (strcmp($key, "prb_neg") == 0) {
        echo '<th style = "color:purple;">' . "prb_neg" . "</th>";
    } elseif (strcmp($key, "neg_dif") == 0) {
        echo '<th style = "color:red;">' . "neg_dif" . "</th>";
    } elseif (strcmp($key, "dif_neg") == 0) {
        echo '<th style = "color:red;">' . "dif_neg" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th style = "color:red;">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th style = "color:red;">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "<th>" . "ACTIVE" . "</th>";
echo "</tr>";
echo "</tfoot>";
echo "</table>";
echo "</div>";

echo '
<script type="text/javascript"> 
    $(document).ready(function(){  
        $("#overall_paid202308").DataTable({
            // lengthMenu: [
            //     [15, 30, 50, -1],
            //     [15, 30, 50, "All"],
            // ],
            createdRow: function (row, data, index) {
                if (data[41] == "1") {		
                    $(row).css("backgroundColor", "#8DDF00"); 
                }
            },
            // columnDefs: [{ visible: false, targets: [5, 12, 13, 14, 15, 16, 17, 43] }],
            // order: [[10, "desc"],[3, "desc"]],
        });  
});  
</script>';

//END

// echo '<h4>Active Participants in last <b>15</b> minutes are marked below:</h4>';
$sql = 'SELECT 	USERNAME, min(minute(timediff(now(), time))) as mins 
									FROM	Sentence_User  
									WHERE 	minute(timediff(now(), time)) <= 15 and hour(timediff(now(), time)) = 0 /*and sentence_id NOT IN (select sentence_id from Training)*/
									GROUP BY username
									ORDER BY mins desc;';
$results = execute($sql, [], PDO::FETCH_ASSOC);
// var_dump($results);
// echo 'q:'.$sql;
$active_users = [];
foreach ($results as $key => $v) {
    array_push($active_users, $v["USERNAME"]);
}
// var_dump($active_users);
if (!$results) {
    echo "...none<br>";
} else {
    foreach ($results as $key => $v) {
        echo "- [" . $v["mins"] . " mins] " . $v["USERNAME"] . "<br>";
    }
}

// echo '- <b><span style="color:#2952FC; font-weight:bold; font-size:large;">'.$total_labels[0].'</span></b> labels collected so far!<br>';
// echo '- <b><span style="color:#2952FC; font-weight:bold; font-size:large;">'.$total_top_labels[0].'</span></b> are top-quality labels<br><br>';
$sql = 'SELECT	A.username, count(*) as count 
									FROM	(SELECT * FROM Sentence_User WHERE response != -2 and sentence_id NOT IN (select sentence_id from Training) ORDER BY time DESC limit 100) A 
									GROUP BY A.username ORDER BY time DESC/* COUNT(*) DESC */;';
$results = execute($sql, [], PDO::FETCH_ASSOC);
echo "<br><br><h4>Latest (100) pair labelers:</h4>";
foreach ($results as $key => $v) {
    echo "- " . $v["username"] . " (" . $v["count"] . "), <br>";
}
echo '<br><br>
							
							</div><br>
							</div>

							<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>
							Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    '<br>
							</div>

			</div>';
// -- -- -- -- END: accordion-item -- -- -- --

// -- -- -- -- START: accordion-item -- -- -- --
echo '<div class="accordion-item">
				<div class="accordion-header" id="workers_heading">
					<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#workers_collapse_paid2023_comb" aria-expanded="false" aria-controls="workers_collapse_paid2023_comb">
						<!-- accordion-Header -->
						<H3>Combine Paid Phase (08/25/2023 - 01/25/2024) <sup></sup> </H3>
					</button>
				</div>
				<div id="workers_collapse_paid2023_comb" class="accordion-collapse collapse" aria-labelledby="workers_heading">
					<div class="accordion-body">
					<!-- accordion-Body -->';
//START

echo '<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br> Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    "<br><br>";

/* Overall worker performance statistics */
echo '<div style= "padding-left:15px;">';
echo "<br><h4>Answered pairs >= 50</h4>";

$sql = 'select	USERNAME, 
                RANK_W, 
                round(A.avg_user/A.avg_total * A.ANSWERED * sign(0.3 - A.RANK_W)*pow((0.3 - A.RANK_W),2),2) AS RANK_L,
                if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) ),2), 0) as QUALITY,
                A.SKIP/A.ANSWERED as SKIP, 
                if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED),2), 0) as PAYRATE,
                if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED) * ANSWERED/100,2), 0) as PAYMENT,
                A.avg_user as LEN,
                A.email,
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
                        User.email as email,
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
                                Sentence.is_active=1 and 
                                sentence_id not in (select sentence_id from Training) and 
                                Sentence_User.time >= "2023-08-25 22:00:00" and Sentence_User.time <= "2024-01-25 22:00:00"
                    GROUP BY Sentence_User.username) A 
                ORDER BY PAYMENT desc, ANSWERED desc;';

$results = execute($sql, [], PDO::FETCH_ASSOC);

$ID = 1;
echo '<div style="width:100%" >';
echo '<table id="overall_combined_paid" border = "1" class="display compact table table-striped"  style="width:100%">';
echo "<thead>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PRT_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . 'PMT_($)' . "</th>";
    } elseif (strcmp($key, "USERNAME") == 0) {
        echo "<th>" . "USER" . "</th>";
    } elseif (strcmp($key, "ANSWERED") == 0) {
        echo "<th>" . "ANS" . "</th>";
    } elseif (strcmp($key, "neg_prb") == 0) {
        echo '<th style = "color:purple;">' . "neg_prb" . "</th>";
    } elseif (strcmp($key, "prb_neg") == 0) {
        echo '<th style = "color:purple;">' . "prb_neg" . "</th>";
    } elseif (strcmp($key, "neg_dif") == 0) {
        echo '<th style = "color:red;">' . "neg_dif" . "</th>";
    } elseif (strcmp($key, "dif_neg") == 0) {
        echo '<th style = "color:red;">' . "dif_neg" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th style = "color:red;">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th style = "color:red;">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "<th>" . "ACTIVE" . "</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
foreach ($results as $key => $v) {
    echo "<tr>";
    echo "<td>" . $ID . "</td>";
    foreach ($v as $k => $value) {
        echo "<td>" . $value . "</td>";
    }
    if (in_array($v["USERNAME"], $active_users)) {
        echo "<td> 1 </td>";
    } else {
        echo "<td> 0 </td>";
    }
    echo "</tr>";
    $ID = $ID + 1;
}
echo "</tbody>";
echo "<tfoot>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PRT_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . 'PMT_($)' . "</th>";
    } elseif (strcmp($key, "USERNAME") == 0) {
        echo "<th>" . "USER" . "</th>";
    } elseif (strcmp($key, "ANSWERED") == 0) {
        echo "<th>" . "ANS" . "</th>";
    } elseif (strcmp($key, "neg_prb") == 0) {
        echo '<th style = "color:purple;">' . "neg_prb" . "</th>";
    } elseif (strcmp($key, "prb_neg") == 0) {
        echo '<th style = "color:purple;">' . "prb_neg" . "</th>";
    } elseif (strcmp($key, "neg_dif") == 0) {
        echo '<th style = "color:red;">' . "neg_dif" . "</th>";
    } elseif (strcmp($key, "dif_neg") == 0) {
        echo '<th style = "color:red;">' . "dif_neg" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th style = "color:red;">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th style = "color:red;">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "<th>" . "ACTIVE" . "</th>";
echo "</tr>";
echo "</tfoot>";
echo "</table>";
echo "</div>";

echo '
<script type="text/javascript"> 
    $(document).ready(function(){  
        $("#overall_combined_paid").DataTable({
            // lengthMenu: [
            //     [15, 30, 50, -1],
            //     [15, 30, 50, "All"],
            // ],
            createdRow: function (row, data, index) {
                if (data[41] == "1") {		
                    $(row).css("backgroundColor", "#8DDF00"); 
                }
            },
            // columnDefs: [{ visible: false, targets: [5, 12, 13, 14, 15, 16, 17, 43] }],
            // order: [[10, "desc"],[3, "desc"]],
        });  
});  
</script>';

//END

// echo '<h4>Active Participants in last <b>15</b> minutes are marked below:</h4>';
$sql = 'SELECT 	USERNAME, min(minute(timediff(now(), time))) as mins 
									FROM	Sentence_User  
									WHERE 	minute(timediff(now(), time)) <= 15 and hour(timediff(now(), time)) = 0 /*and sentence_id NOT IN (select sentence_id from Training)*/
									GROUP BY username
									ORDER BY mins desc;';
$results = execute($sql, [], PDO::FETCH_ASSOC);
// var_dump($results);
// echo 'q:'.$sql;
$active_users = [];
foreach ($results as $key => $v) {
    array_push($active_users, $v["USERNAME"]);
}
// var_dump($active_users);
if (!$results) {
    echo "...none<br>";
} else {
    foreach ($results as $key => $v) {
        echo "- [" . $v["mins"] . " mins] " . $v["USERNAME"] . "<br>";
    }
}

// echo '- <b><span style="color:#2952FC; font-weight:bold; font-size:large;">'.$total_labels[0].'</span></b> labels collected so far!<br>';
// echo '- <b><span style="color:#2952FC; font-weight:bold; font-size:large;">'.$total_top_labels[0].'</span></b> are top-quality labels<br><br>';
$sql = 'SELECT	A.username, count(*) as count 
									FROM	(SELECT * FROM Sentence_User WHERE response != -2 and sentence_id NOT IN (select sentence_id from Training) ORDER BY time DESC limit 100) A 
									GROUP BY A.username ORDER BY time DESC/* COUNT(*) DESC */;';
$results = execute($sql, [], PDO::FETCH_ASSOC);
echo "<br><br><h4>Latest (100) pair labelers:</h4>";
foreach ($results as $key => $v) {
    echo "- " . $v["username"] . " (" . $v["count"] . "), <br>";
}
echo '<br><br>
							
							</div><br>
							</div>

							<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>
							Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    '<br>
							</div>

			</div>';
// -- -- -- -- END: accordion-item -- -- -- --

// -- -- -- -- START: accordion-item -- -- -- --
echo '<div class="accordion-item">
				<div class="accordion-header" id="workers_heading">
					<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#ranking_information" aria-expanded="false" aria-controls="ranking_information">
						<!-- accordion-Header -->
						<H3>Ranking Information</H3>
					</button>
				</div>
				<div id="ranking_information" class="accordion-collapse collapse" aria-labelledby="workers_heading">
					<div class="accordion-body">
					<!-- accordion-Body -->';
//START

echo '<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>
						Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    "<br><br>";

/* Overall worker performance statistics */
echo '<div style= "padding-left:15px;">';
echo "<br><h4>Answered pairs >= 50</h4>";

$sql = 'select	USERNAME, 
                RANK_W, 
                round(A.avg_user/A.avg_total * A.ANSWERED * sign(0.3 - A.RANK_W)*pow((0.3 - A.RANK_W),2),2) AS RANK_L,
                if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) ),2), 0) as QUALITY,
                A.SKIP/A.ANSWERED as SKIP, 
                if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED),2), 0) as PAYRATE,
                if(ANSWERED >= 50, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED) * ANSWERED/100,2), 0) as PAYMENT,
                A.avg_user as LEN,
                A.email,
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
                        User.email as email,
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
                                Sentence.is_active=1 and 
                                sentence_id not in (select sentence_id from Training)
                    GROUP BY Sentence_User.username) A 
                ORDER BY PAYMENT desc, ANSWERED desc;';

$results = execute($sql, [], PDO::FETCH_ASSOC);
// var_dump($results);

$ID = 1;
echo '<div style="width:100%" >';
echo '<table id="overall" border = "1" class="display compact table table-striped"  style="width:100%">';
echo "<thead>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PRT_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . 'PMT_($)' . "</th>";
    } elseif (strcmp($key, "USERNAME") == 0) {
        echo "<th>" . "USER" . "</th>";
    } elseif (strcmp($key, "ANSWERED") == 0) {
        echo "<th>" . "ANS" . "</th>";
    }
    //dif=4
    elseif (strcmp($key, "neg_prb") == 0) {
        echo '<th style = "color:purple;">' . "neg_prb" . "</th>";
    } elseif (strcmp($key, "prb_neg") == 0) {
        echo '<th style = "color:purple;">' . "prb_neg" . "</th>";
    }
    //dif=3
    elseif (strcmp($key, "neg_dif") == 0) {
        echo '<th style = "color:red;">' . "neg_dif" . "</th>";
    } elseif (strcmp($key, "dif_neg") == 0) {
        echo '<th style = "color:red;">' . "dif_neg" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th style = "color:red;">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th style = "color:red;">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "<th>" . "ACTIVE" . "</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
foreach ($results as $key => $v) {
    echo "<tr>";
    echo "<td>" . $ID . "</td>";
    foreach ($v as $k => $value) {
        echo "<td>" . $value . "</td>";
    }
    if (in_array($v["USERNAME"], $active_users)) {
        echo "<td> 1 </td>";
    } else {
        echo "<td> 0 </td>";
    }
    echo "</tr>";
    $ID = $ID + 1;
}
echo "</tbody>";
echo "<tfoot>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PRT_(&cent;)" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . 'PMT_($)' . "</th>";
    } elseif (strcmp($key, "USERNAME") == 0) {
        echo "<th>" . "USER" . "</th>";
    } elseif (strcmp($key, "ANSWERED") == 0) {
        echo "<th>" . "ANS" . "</th>";
    } elseif (strcmp($key, "neg_prb") == 0) {
        echo '<th style = "color:purple;">' . "neg_prb" . "</th>";
    } elseif (strcmp($key, "prb_neg") == 0) {
        echo '<th style = "color:purple;">' . "prb_neg" . "</th>";
    } elseif (strcmp($key, "neg_dif") == 0) {
        echo '<th style = "color:red;">' . "neg_dif" . "</th>";
    } elseif (strcmp($key, "dif_neg") == 0) {
        echo '<th style = "color:red;">' . "dif_neg" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th style = "color:red;">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th style = "color:red;">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "<th>" . "ACTIVE" . "</th>";
echo "</tr>";
echo "</tfoot>";
echo "</table>";
echo "</div>";

echo '
<script type="text/javascript"> 
    $(document).ready(function(){  
        $("#overall").DataTable({
            // lengthMenu: [
            //     [15, 30, 50, -1],
            //     [15, 30, 50, "All"],
            // ],
            createdRow: function (row, data, index) {
                if (data[41] == "1") {		
                    $(row).css("backgroundColor", "#8DDF00"); 
                }
            },
            // columnDefs: [{ visible: false, targets: [5, 12, 13, 14, 15, 16, 17, 43] }],
            // order: [[10, "desc"],[3, "desc"]],
        });  
});  
</script>';

//END

// echo '<h4>Active Participants in last <b>15</b> minutes are marked below:</h4>';
$sql = 'SELECT 	USERNAME, min(minute(timediff(now(), time))) as mins 
									FROM	Sentence_User  
									WHERE 	minute(timediff(now(), time)) <= 15 and hour(timediff(now(), time)) = 0 /*and sentence_id NOT IN (select sentence_id from Training)*/
									GROUP BY username
									ORDER BY mins desc;';
$results = execute($sql, [], PDO::FETCH_ASSOC);
// var_dump($results);
// echo 'q:'.$sql;
$active_users = [];
foreach ($results as $key => $v) {
    array_push($active_users, $v["USERNAME"]);
}
// var_dump($active_users);
if (!$results) {
    echo "...none<br>";
} else {
    foreach ($results as $key => $v) {
        echo "- [" . $v["mins"] . " mins] " . $v["USERNAME"] . "<br>";
    }
}

// echo '- <b><span style="color:#2952FC; font-weight:bold; font-size:large;">'.$total_labels[0].'</span></b> labels collected so far!<br>';
// echo '- <b><span style="color:#2952FC; font-weight:bold; font-size:large;">'.$total_top_labels[0].'</span></b> are top-quality labels<br><br>';
$sql = 'SELECT	A.username, count(*) as count 
									FROM	(SELECT * FROM Sentence_User WHERE response != -2 and sentence_id NOT IN (select sentence_id from Training) ORDER BY time DESC limit 100) A 
									GROUP BY A.username ORDER BY time DESC/* COUNT(*) DESC */;';
$results = execute($sql, [], PDO::FETCH_ASSOC);
echo "<br><br><h4>Latest (100) pair labelers:</h4>";
foreach ($results as $key => $v) {
    echo "- " . $v["username"] . " (" . $v["count"] . "), <br>";
}
echo '<br><br>
							
							</div><br>
							</div>

							<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>
							Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    '<br>
							</div>

			</div>';
// -- -- -- -- END: accordion-item -- -- -- --

// // -- -- -- -- START: accordion-item -- -- -- --
echo '<div class="accordion-item">
				<div class="accordion-header" id="Training_heading">';
echo '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#Training_collapse" aria-expanded="false" aria-controls="Training_collapse">'; // CLOSE
//<!-- accordion-Header -->
echo '<H3>Training Statistics <sup></sup> </H3>
					</button>
				</div>'; // HEADER
echo '<div id="Training_collapse" class="accordion-collapse collapse" aria-labelledby="Training_heading">'; // CLOSE
echo '<div class="accordion-body">';
//<!-- accordion-Body -->';
//START

echo '<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>
							Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    "<br>";

/* Workshop Training statistics  */
echo "<br>";
echo '<div  style= "padding-left:15px;">';
echo "<H3>Worker performance during Training phase</H3>";

// Same query as procedure
$sql = 'select	USERNAME, 
        RANK_W, 
        round(A.avg_user/A.avg_total * A.ANSWERED * sign(0.3 - A.RANK_W)*pow((0.3 - A.RANK_W),2),2) AS RANK_L,
        if(ANSWERED >= 0, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) ),2), 0) as QUALITY,
        A.SKIP/A.ANSWERED as SKIP, 
        if(ANSWERED >= 0, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED),2), 0) as PAYRATE,
        if(ANSWERED >= 0, 2.0*round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.avg_user/A.avg_total), 2.0)*pow(0.6, A.SKIP/A.ANSWERED) * ANSWERED/100,2), 0) as PAYMENT,
        A.avg_user as LEN,
        A.email,
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
                User.email as email,
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
                        Sentence.is_active=1 and 
                        sentence_id in (select sentence_id from Training)
            GROUP BY Sentence_User.username) A 
        ORDER BY PAYMENT desc, ANSWERED desc;';

$results = execute($sql, [], PDO::FETCH_ASSOC);
//var_dump($results);

$ID = 1;
echo '<div style="width:100%" >';
echo '<table id="training" border = "1" class="display table table-striped"  style="width:100%">';
echo "<thead>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "PAYRATE" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . "PAYMENT" . "</th>";
    } elseif (strcmp($key, "USERNAME") == 0) {
        echo "<th>" . "USER" . "</th>";
    } elseif (strcmp($key, "ANSWERED") == 0) {
        echo "<th>" . "#" . "</th>";
    }
    //dif=4
    elseif (strcmp($key, "neg_prb") == 0) {
        echo '<th style = "color:purple;">' . "neg_prb" . "</th>";
    } elseif (strcmp($key, "prb_neg") == 0) {
        echo '<th style = "color:purple;">' . "prb_neg" . "</th>";
    }
    //dif=3
    elseif (strcmp($key, "neg_dif") == 0) {
        echo '<th style = "color:red;">' . "neg_dif" . "</th>";
    } elseif (strcmp($key, "dif_neg") == 0) {
        echo '<th style = "color:red;">' . "dif_neg" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th style = "color:red;">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th style = "color:red;">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "<th>" . "ACTIVE" . "</th>";
echo "</tr>";
echo "</thead>";

echo "<tbody>";
foreach ($results as $key => $v) {
    echo "<tr>";
    echo "<td>" . $ID . "</td>";
    foreach ($v as $k => $value) {
        echo "<td>" . $value . "</td>";
    }
    if (in_array($v["USERNAME"], $active_users) && $v["ANSWERED"] > 16) {
        echo "<td> 1 </td>";
    } else {
        echo "<td> 0 </td>";
    }
    echo "</tr>";
    $ID = $ID + 1;
}
echo "</tbody>";

echo "<tfoot>";
echo "<tr>";
echo "<th>" . "#" . "</th>";
foreach ($results[0] as $key => $value) {
    if (strcmp($key, "PAYRATE") == 0) {
        echo "<th>" . "QUALITY" . "</th>";
    } elseif (strcmp($key, "PAYMENT") == 0) {
        echo "<th>" . "POINTS" . "</th>";
    } elseif (strcmp($key, "USERNAME") == 0) {
        echo "<th>" . "USER" . "</th>";
    } elseif (strcmp($key, "ANSWERED") == 0) {
        echo "<th>" . "#" . "</th>";
    }
    //dif=4
    elseif (strcmp($key, "neg_prb") == 0) {
        echo '<th style = "color:purple;">' . "neg_prb" . "</th>";
    } elseif (strcmp($key, "prb_neg") == 0) {
        echo '<th style = "color:purple;">' . "prb_neg" . "</th>";
    }
    //dif=3
    elseif (strcmp($key, "neg_dif") == 0) {
        echo '<th style = "color:red;">' . "neg_dif" . "</th>";
    } elseif (strcmp($key, "dif_neg") == 0) {
        echo '<th style = "color:red;">' . "dif_neg" . "</th>";
    } elseif (strcmp($key, "neu_prb") == 0) {
        echo '<th style = "color:red;">' . "neu_prb" . "</th>";
    } elseif (strcmp($key, "prb_neu") == 0) {
        echo '<th style = "color:red;">' . "prb_neu" . "</th>";
    } else {
        echo "<th>" . $key . "</th>";
    }
}
echo "</tr>";
echo "</tfoot>";
echo "</table>";
echo "</div>";

echo '
<script> 
    $(document).ready(function(){  
        $("#Training").DataTable({
            lengthMenu: [
                [15, 30, 50, -1],
                [15, 30, 50, "All"],
            ],
            createdRow: function (row, data, index) {
                if (data[42] == "1") {		
                    $(row).css("backgroundColor", "#8DDF00"); 
                }
            },
            columnDefs: [{ visible: false, targets: [4, 11, 12, 13, 14, 15, 16, 42] }],
            order: [[9, "desc"],[2, "desc"]],
        });  
    });  
</script>';

echo "</div><br>";

//END
echo "</div>"; // BODY

echo '<button class = "button" onclick="location.href=\'get_progress.php\'">Refresh</button><br>
					Webpage last load on ' .
    $date .
    ", " .
    $timezone .
    "<br><br>";

echo "</div>"; // BODY HEADING
echo "</div>"; // ITEM
echo "</div>";
echo "</body>";
?>
<script type="tprb/javascript">  $(window).scrollTop(0); </script> 