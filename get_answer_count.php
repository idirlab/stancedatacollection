<?php
session_start(['cookie_lifetime' => 86400,]);
$username = $_SESSION["username"];
include_once "db.php";

$sql = gen_select_query(
    ["count(*) as count"],
    ["Sentence_User"],
    [
        "username = " . $username,
        "response != -2",
        "sentence_id not in " . $_SESSION["training_sentences"] . " ",
        'time >= '.$_SESSION['pomodoro_phase_time_start'],
    ]
);
$results = execute($sql, [], PDO::FETCH_ASSOC);
if ($results[0]["count"] == 50) {
    $_SESSION["message_counter"] = 0;
}
echo $results[0]["count"] . "^" . $username;
?>
