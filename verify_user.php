<?php
session_start();
$random_key = $_GET["random_key"];
$username = $_GET["username"];

include_once "db.php";

$sql = gen_update_query(
    ["User"],
    ["verification"],
    ['"verified"'],
    ['verification = "' . $random_key . '"', 'username = "' . $username . '"']
);

$results = execute($sql, [], PDO::FETCH_ASSOC);

$sql = gen_select_query(
    ["verification"],
    ["User"],
    ['username = "' . $username . '"']
);
$results = execute($sql, [], PDO::FETCH_ASSOC);

if (strcmp($results[0]["verification"], "verified") == 0) {
    echo "Your account is verified successfully! Please go to <a href='http://idir.uta.edu/wildfire_annotation_groundtruth/'>http://idir.uta.edu/wildfire_annotation_groundtruth/</a> and sign in now. Thanks!";
    return;
} else {
    echo "There is some error with verification. Please check the verification link or try Registration again.";
}
?>
