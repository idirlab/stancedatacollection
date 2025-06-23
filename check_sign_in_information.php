<?php
session_start();
$username = $_REQUEST["username"];
$password = $_REQUEST["password"];
// $encrypted_password = '"' . md5($password) . '"';
include_once "db.php";

$sql = gen_select_query(
    ["username", "count(*) as count"],
    ["User"],
    ["username = " . $username, "password = " . $password]
);
$results = execute($sql, [], PDO::FETCH_ASSOC);

if (strcmp($results[0]["count"], "0") == 0) {
    echo "-1";
    exit();
} #wrong information

$sql = gen_select_query(
    ["verification"],
    ["User"],
    ["username = " . $username, "password = " . $password]
);

$results = execute($sql, [], PDO::FETCH_ASSOC);
if (strcmp($results[0]["verification"], "verified") == 0) {
    $_SESSION["username"] = $username;
    $_SESSION["answered"] = 0;
    $_SESSION["screening_questioned"] = 0;
    $_SESSION["message_counter"] = rand(15, 20);
    $_SESSION["just_logged_in"] = 1;

    $_SESSION["REGION"] = 0;
    $_SESSION['pomodoro_phase_time_start'] = "'2024-06-23 17:59:59'"; # first phrase

    $get_training_sentence_sql = gen_select_query(
        ["sentence_id"],
        ["Training"],
    );
    $_SESSION["training_sentences"] = '(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16)';
    $activity_sql = gen_insert_query(
        $tables = ["Activity"],
        $fields = ["username", "time", "action"],
        $values = [
            $username,
            '"' . date("Y-m-d H:i:s") . '"',
            "'USER LOGEED-IN'",
        ]
    );
    $results = execute($activity_sql, [], PDO::FETCH_ASSOC);
    echo "1";
    exit();
} else {
    echo "-2"; #unverified account
    exit();
}
?>
