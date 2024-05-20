<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include ('con_params.php');

$mysqli = new mysqli($server, $user, $password, $db, $port);
$file_name = $_POST['file_name'];
$get_periods_stmt = $mysqli->prepare("SELECT FIRST_PERIOD,SECOND_PERIOD,THIRD_PERIOD FROM PPT_FILES WHERE FILE_NAME= ?");
$get_periods_stmt->bind_param("s", $file_name);
$get_periods_stmt->execute();
$get_periods_result = $get_periods_stmt->get_result();
$fetch = $get_periods_result->fetch_assoc();

$temp_array = array(
    'first' => $fetch['FIRST_PERIOD'],
    'second' => $fetch['SECOND_PERIOD'],
    'third' => $fetch['THIRD_PERIOD']
);

echo json_encode($temp_array);
?>