<?php
session_start(["cookie_lifetime" => 86400]);
$sentence_id = $_REQUEST["sentence_id"];
include_once "db.php";
$sql = gen_select_query(
    ["Sentence.id", "Speaker.name", "Sentence.text"],
    ["Sentence", "Speaker"],
    ["Sentence.speaker_id = Speaker.id", "Sentence.id =" . $sentence_id . " "],
    [],
    [],
    []
);

$results = execute($sql, [], PDO::FETCH_ASSOC);
echo json_encode($results);
?>
