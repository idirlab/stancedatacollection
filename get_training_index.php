<?php
session_start(['cookie_lifetime' => 86400,]);
$username = $_SESSION["username"];
include_once "db.php";

$sql = gen_select_query(
    ["idx"],
    ["User_Training"],
    ["username = " . $username]
);
$results = execute($sql, [], PDO::FETCH_ASSOC);
$idx = 0;
if (count($results) == 0) {
    $sql = gen_insert_query(
        $tables = ["User_Training"],
        $fields = ["username", "idx"],
        $values = [$username, $idx]
    );
    execute($sql, [], PDO::FETCH_ASSOC);
} else {
    $idx = $results[0]["idx"];
}

if ($idx == 16) {
    echo "0";
} else {
    $idx = $idx + 1;
    $sql = gen_select_query(["sentence_id"], ["Training"], ["idx = " . $idx]);
    $results = execute($sql, [], PDO::FETCH_ASSOC);
    echo $results[0]["sentence_id"] . "^" . $idx;
}
?>
