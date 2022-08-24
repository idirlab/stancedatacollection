<?php
session_start();
$username = $_REQUEST["username"];
$password = $_REQUEST["password"];
echo "dada";
$encrypted_password = '"' . md5($password) . '"';
include_once "db.php";
echo "didi";
$sql = gen_select_query(
    ["username", "count(*) as count"],
    ["User"],
    ["username = " . $username, "password = " . $encrypted_password]
);
echo "qqq";
echo $sql;
$results = execute($sql, [], PDO::FETCH_ASSOC);
echo "111";
if (strcmp($results[0]["count"], "0") == 0) {
    echo "-1";
} #wrong information
echo "---";
$sql = gen_select_query(
    ["verification"],
    ["User"],
    ["username = " . $username, "password = " . $encrypted_password]
);
echo "000";
$results = execute($sql, [], PDO::FETCH_ASSOC);
echo "222";
if (strcmp($results[0]["verification"], "verified") == 0) {
    $_SESSION["username"] = $username;
    $_SESSION["answered"] = 0;
    $_SESSION["screening_questioned"] = 0;

    $_SESSION["message_counter"] = rand(20, 30);
    $_SESSION["just_logged_in"] = 1;

    $_SESSION["REGION"] = 0;
    $_SESSION["training_sentences"] =
        "(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40)"; /*May 26, 2016*/

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
} else {
    echo "-2"; #unverified account
}
echo "333";
?>
