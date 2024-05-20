<?php
include('con_params.php');
include('php_functions.php');

// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

$mysqli = new mysqli($server, $user, $password, $db, $port);

if(isset($_POST['tags']) && isset($_POST['fileName'])) {
    $file_name = $_POST['fileName'];

    // get file info
    $file_info = $mysqli->prepare("SELECT FILE_NAME, PPT_FILE_PATH, IMG_PATH FROM PPT_FILES WHERE FILE_NAME= ?");
    $file_info->bind_param("s", $file_name);
    $file_info->execute();
    $file_info_result = $file_info->get_result();
    $final_file_info = $file_info_result->fetch_assoc();


    $tags = $_POST['tags'];
    $first_period = $_POST['first_period'];
    $second_period = $_POST['second_period'];
    $third_period = $_POST['third_period'];

    $file =  $old_file_name = $new_file_tmp_name = '';

    if(isset($_FILES['file'])){
        if($_FILES['file']['type'] != 'application/vnd.openxmlformats-officedocument.presentationml.presentation'){
            echo json_encode(array('error' => "Invalid file type."));
        }
        else{
            // change the new file's name
            $total_files = (int)getNumberOfFiles();
            $uploaded_file_changed_name = 'slide_' . $total_files;
            $uploaded_file_name = hash('sha256', $uploaded_file_changed_name) . '.pptx';
            $uploaded_file_img_name = str_replace('pptx', 'png', $uploaded_file_name);

            // declare the new file's temp storage name and actual file object
            $new_file = $_FILES['file'];
            $new_file_tmp_name = $new_file['tmp_name'];

            // change the old file's name, location, png name and location
            $date = "_" . date('m_d_Y_h_i', time()) . ".pptx";
            $old_file_name_change = str_replace(".pptx", $date, $file_name);
            $old_file_current_location = './uploads/' . $file_name;
            $old_file_new_location = './old/' . $old_file_name_change;
            $old_file_new_img_path = str_replace('pptx', 'png', $old_file_new_location);

            $upload_dir = './uploads/' . $uploaded_file_name;
            // move the old file and image to /old/
            if(rename($old_file_current_location, $old_file_new_location) && rename($final_file_info['IMG_PATH'], $old_file_new_img_path)){
                $rename_stmt = $mysqli->prepare("UPDATE PPT_FILES SET FILE_NAME=?, PPT_FILE_PATH=?, IMG_PATH= ?, IS_OLD=1 WHERE FILE_NAME= ?");
                $rename_stmt->bind_param("ssss", $old_file_name_change, $old_file_new_location, $old_file_new_img_path, $file_name);
                $rename_stmt->execute();
            }

            // upload the new file and generate an image, add to database
            if(move_uploaded_file($new_file_tmp_name, $upload_dir)){
                $insert_stmt = $mysqli->prepare("INSERT INTO PPT_FILES(FILE_NAME, PPT_FILE_PATH, FILE_TAGS,  FIRST_PERIOD, SECOND_PERIOD, THIRD_PERIOD) VALUES (?,?,?,?,?,?)");
                $insert_stmt->bind_param("ssssss", $uploaded_file_name, $upload_dir, $tags, $first_period, $second_period, $third_period);
                $insert_stmt->execute();
                generateImg($uploaded_file_img_name, $uploaded_file_name);
                updateFilterTable($tags);
                
            }
            echo json_encode(array('success_uploaded' => "Slide updated."));
        }
    }
    else{
        // fetch file data
        $fetch_stmt = $mysqli->prepare("SELECT FILE_TAGS, FIRST_PERIOD, SECOND_PERIOD, THIRD_PERIOD FROM PPT_FILES WHERE FILE_NAME=?");
        $fetch_stmt->bind_param("s", $file_name);
        $fetch_stmt->execute();
        $result = $fetch_stmt->get_result();
        $current_data = $result->fetch_assoc();
        $temp_tag_string = implode("|||" , $tags);
        // update attributes which are changed
        // update tags
        if($current_data['FILE_TAGS'] != $temp_tag_string){
            $update_tags_stmt = $mysqli->prepare("UPDATE PPT_FILES SET FILE_TAGS= ? WHERE FILE_NAME= ?");
            $update_tags_stmt->bind_param("ss", $temp_tag_string, $file_name);
            $update_tags_stmt->execute();
            updateFilterTable($temp_tag_string);
        }

        // update first period

        if((int)$current_data['FIRST_PERIOD'] != (int)$first_period){
            $temp_int = (int)$first_period;
            $update_first_period_stmt = $mysqli->prepare("UPDATE PPT_FILES SET FIRST_PERIOD= ? WHERE FILE_NAME= ?");
            $update_first_period_stmt->bind_param("is", $first_period, $file_name);
            $update_first_period_stmt->execute();
        }

        // update second period
        if((int)$current_data['SECOND_PERIOD'] != (int)$second_period){
            $temp_int = (int)$second_period;
            $update_second_period_stmt = $mysqli->prepare("UPDATE PPT_FILES SET SECOND_PERIOD= ? WHERE FILE_NAME= ?");
            $update_second_period_stmt->bind_param("is", $second_period, $file_name);
            $update_second_period_stmt->execute();
        }

        // update second period
        if((int)$current_data['THIRD_PERIOD'] != (int)$third_period){
            $temp_int = (int)$third_period;
            $update_third_period_stmt = $mysqli->prepare("UPDATE PPT_FILES SET THIRD_PERIOD= ? WHERE FILE_NAME= ?");
            $update_third_period_stmt->bind_param("is", $third_period, $file_name);
            $update_third_period_stmt->execute();
        }
        echo json_encode(array('success_updated' => "Updated."));
    }
    
}
?>