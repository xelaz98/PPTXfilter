<?php
include('con_params.php');
$file = $_POST['file_name'];
$file_name = explode('.', $file)[0];
$img = explode('./uploads/',$_POST['img_name'])[1];
$img_name = explode('.', $img)[0];
$mysqli = new mysqli($server, $user, $password, $db, $port);

$check_file_stmt = $mysqli->prepare("SELECT COUNT(*) as res FROM PPT_FILES WHERE FILE_NAME=?");
$check_file_stmt->bind_param('s', $file);
$check_file_stmt->execute();
$check_result = $check_file_stmt->get_result();
$fetch = $check_result->fetch_assoc();
if($file_name != $img_name){
    echo json_encode(array('failed' => 'Image or file name is not valid'));
}
else{
    if($fetch['res'] == '0' || $fetch['res'] == 0){
        echo json_encode(array('failed' => 'File does not exist'));
    }
    else{
        echo json_encode(array('success' => 'File exists'));
    }
}
?>