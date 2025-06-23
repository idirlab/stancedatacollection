<?php
include_once "db.php";
session_start(["cookie_lifetime" => 86400]);
$username = $_SESSION["username"];

//added momentarily for payment display purpose. March 30, 2016.
$results = $_SESSION["leaderboard_results"];
// var_dump($results);

$user = -1;
$winner_10 = []; //array('mcwizard', 'dxl3182', 'nrd1216', 'lawanda18', 'danb11', 'ajdashdash' , 'Alucard', 'anna.prieto', 'Charis92', 'dxl3182', 'JE_4489', 'jsn6522', 'Leaf90', 'nadi', 'sakiforu', 'aminataj', 'AramintaK96', 'benderwd40', 'carcinoPoet', 'danb11', 'dlehddbs92', 'dustinh', 'j.kathryn', 'kidkatie', 'knitty1997', 'Monica_16', 'pec6938', 'sakiforu', 'Sfj8667', 'stefaguas');
$winner_100 = []; //array('CodyBonBon', 'dariansmith55');
$winner_200 = []; //array('dxl3182');

$winner_10 = array_count_values($winner_10);
$winner_100 = array_count_values($winner_100);
$winner_200 = array_count_values($winner_200);

for ($i = 0; $i < count($results); $i++) {
    $results[$i]["prize"] = "";
    if (array_key_exists($results[$i]["USERNAME"], $winner_200)) {
        for ($j = 0; $j < $winner_200[$results[$i]["USERNAME"]]; $j++) {
            if (strcmp($results[$i]["prize"], "") != 0) {
                $results[$i]["prize"] = $results[$i]["prize"] . ", ";
            }
            $results[$i]["prize"] = $results[$i]["prize"] . '$200';
        }
    }

    if (array_key_exists($results[$i]["USERNAME"], $winner_100)) {
        for ($j = 0; $j < $winner_100[$results[$i]["USERNAME"]]; $j++) {
            if (strcmp($results[$i]["prize"], "") != 0) {
                $results[$i]["prize"] = $results[$i]["prize"] . ", ";
            }
            $results[$i]["prize"] = $results[$i]["prize"] . '$100';
        }
    }

    if (array_key_exists($results[$i]["USERNAME"], $winner_10)) {
        for ($j = 0; $j < $winner_10[$results[$i]["USERNAME"]]; $j++) {
            if (strcmp($results[$i]["prize"], "") != 0) {
                $results[$i]["prize"] = $results[$i]["prize"] . ", ";
            }
            $results[$i]["prize"] = $results[$i]["prize"] . '$10';
        }
    }

    if (
        strcmp(
            $results[$i]["USERNAME"],
            substr($username, 1, strlen($username) - 2)
        ) != 0
    ) {
        $results[$i]["USERNAME"] =
            strtoupper(
                $results[$i]["USERNAME"][0] .
                    $results[$i]["USERNAME"][1] .
                    $results[$i]["USERNAME"][2]
            ) . "*****";
    } else {
        $results[$i]["USERNAME"] = $results[$i]["USERNAME"];
        $user = $i;
    }

    if (floatval($results[$i]["QUALITY"]) == -100000) {
        $results[$i]["QUALITY"] = "Not Displayed";
    }

    if (floatval($results[$i]["PAYMENT"]) == -100000) {
        $results[$i]["PAYMENT"] = "Not Displayed";
    }
    //else $results[$i]['RANK_N'] = $i+1;
}

$results[0]["user"] = $user;
//$results[0]['winner_10'] = $winner_10;
//$results[0]['winner_100'] = $winner_100;
$results = json_encode($results);
echo $results;

$activity_sql = gen_insert_query(
    $tables = ["Activity"],
    $fields = ["username", "time", "action"],
    $values = [
        $username,
        '"' . date("Y-m-d H:i:s") . '"',
        "'USER CHECKED LEADERBOARD'",
    ]
);
$results = execute($activity_sql, [], PDO::FETCH_ASSOC);
?>
