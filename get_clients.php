<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include('con_params.php');

$mysqli = new mysqli($server, $user, $password, $db, $port);

$tag_stmt = $mysqli->prepare("SELECT TAG_NAME FROM PPT_TAGS WHERE TAG_TYPE=0");
$tag_stmt->execute();
$tag_stmt_results = $tag_stmt->get_result();
$rows = array();

while($row = $tag_stmt_results->fetch_assoc()){
    $rows[] = $row['TAG_NAME'];
}

echo json_encode($rows);
?>