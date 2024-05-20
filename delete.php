<?php

    include('con_params.php');

    $mysqli = new mysqli($server, $user, $password, $db, $port);

    $file_to_delete = $_POST['id'];
    echo $file_to_delete;
    // get file location
    $get_stmt = $mysqli->prepare("SELECT PPT_FILE_PATH, IMG_PATH FROM PPT_FILES WHERE FILE_NAME= ?");
    $get_stmt->bind_param("s", $file_to_delete);
    $get_stmt->execute();
    $get_result = $get_stmt->get_result();
    $file_path_result = $get_result->fetch_assoc();
    $file_path = $file_path_result['PPT_FILE_PATH'];
    $img_path = "." . $file_path_result['IMG_PATH'];
    $img_name = str_replace("pptx","png", $file_to_delete);

    // TODO: 
    // 1. Test code below when everything else is ready
    // delete file from DB
    $del_stmt = $mysqli->prepare("UPDATE PPT_FILES SET IS_DELETED= ? WHERE FILE_NAME= ?");
    $set_deleted = 1;
    $del_stmt->bind_param("is",$set_deleted, $file_to_delete);
    $del_stmt->execute();
    
    

    if(file_exists($file_path)){
        $deleted_dir = "./deleted";
        $new_file_path = $deleted_dir . "/" . $file_to_delete;
        $new_img_path = $deleted_dir . "/" . $img_name;
        if(rename($file_path, $new_file_path)){
            echo "file moved successfully" . "\n";
        }
        else{
            echo "failed to move file";
        }
        if(rename($img_path, $new_img_path)){
            echo "img moved successfully";
        }
        else{
            echo "failed to move image";
        }
        
    }
    // delete file from server
    // unlink($file_path);
    // unlink($img_path);
    mysqli_close($mysqli);
    
?>