<?php

include('con_params.php');
$mysqli = new mysqli($server, $user, $password, $db, $port);



function getNumberOfFiles(){
  global $mysqli;
  $count_stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM PPT_FILES");
  $count_stmt->execute();
  $count_result = $count_stmt->get_result();
  $final = $count_result->fetch_assoc();

  return $final['count'];
}

function uploadFile($newName, $tmp_name, $filter_list, $first_period, $second_period, $third_period, $img_name){
  global $mysqli;
  global $check_api_call;
  $target_dir = "./uploads/"; 
  $encoded_name = hash('sha256', $newName) . ".pptx";
  $encoded_img_name = hash('sha256', $newName) . ".png";
  $target_file = $target_dir . basename($encoded_name);
  if (move_uploaded_file($tmp_name, $target_file)) {
    $stmt = $mysqli->prepare("INSERT INTO PPT_FILES (FILE_NAME, PPT_FILE_PATH, FILE_TAGS) VALUES (?,?,?)");
    $stmt->bind_param("sss", $encoded_name, $target_file, $filter_list);
    $stmt->execute();
    if($first_period !=""){

      $first_period_stmt = $mysqli->prepare("UPDATE PPT_FILES SET FIRST_PERIOD= ? WHERE FILE_NAME= ?");
      $first_period_stmt->bind_param("ss", $first_period, $encoded_name);
      $first_period_stmt->execute();
    }
    if($second_period != ""){
      $second_period_stmt = $mysqli->prepare("UPDATE PPT_FILES SET SECOND_PERIOD= ? WHERE FILE_NAME= ?");
      $second_period_stmt->bind_param("ss", $second_period, $encoded_name);
      $second_period_stmt->execute();
    }
    if($third_period != ""){
      $third_period_stmt = $mysqli->prepare("UPDATE PPT_FILES SET THIRD_PERIOD= ? WHERE FILE_NAME= ?");
      $third_period_stmt->bind_param("ss", $third_period, $encoded_name);
      $third_period_stmt->execute();
    }
    
      $FileHandle = fopen('uploads/'. $encoded_img_name, 'w+');
      $curl = curl_init();
      $instructions = '{
          "parts": [
            {
              "file": "document"
            }
          ],
          "output": {
            "type": "image",
            "format": "png",
            "dpi": 500
          }
      }';     
      curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.pspdfkit.com/build',
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_POSTFIELDS => array(
          'instructions' => $instructions,
          'document' => new CURLFILE('uploads/'. $encoded_name)
        ),
        CURLOPT_HTTPHEADER => array(
          'Authorization: Bearer pdf_live_BMREUue07pxTfsaC1psbE7aVtB0tBhjeGUAkheZSP03'
        ),
        CURLOPT_FILE => $FileHandle,
      ));
      $response = curl_exec($curl);
      curl_close($curl);
      fclose($FileHandle);
      if($response){
          $png_path_stmt = $mysqli->prepare("UPDATE PPT_FILES SET IMG_PATH=? WHERE FILE_NAME= ?");
          $img_path_val = "./uploads/" . $encoded_img_name;
          $png_path_stmt->bind_param("ss", $img_path_val, $encoded_name);
          $png_path_stmt->execute();
          $check_api_call = true;
      }
      else{
        $check_api_call = false;
      }
  } 
}

function updateFilterTable($filter_string, $filter_type){
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT COUNT(*) as found FROM PPT_TAGS WHERE TAG_NAME= ?");
    $stmt->bind_param("s", $filter_string);
    $stmt->execute();
    $stmt_result = $stmt->get_result();
    $final_res = $stmt_result->fetch_assoc();
    
    if($final_res['found'] == 0 || $final_res['found'] == '0'){
      $tag_type_val = (int)$filter_type;
      $add_tag_stmt = $mysqli->prepare("INSERT INTO PPT_TAGS (TAG_NAME, TAG_TYPE) VALUES (? ,?)"); 
      $add_tag_stmt->bind_param("si", $filter_string, $tag_type_val);
      $add_tag_stmt->execute();
    }

}

if(isset($_FILES['files'])){  
  $output_arr = array();
  $first_period = "";
  $second_period = "";
  $third_period = "";
  
  if(isset($_POST["FIRST_PERIOD"])){
    $first_period = $_POST["FIRST_PERIOD"];
  }
  if(isset($_POST["SECOND_PERIOD"])){
    $second_period = $_POST["SECOND_PERIOD"];
  }
  if(isset($_POST["THIRD_PERIOD"])){
    $third_period = $_POST["THIRD_PERIOD"];
  }
  
  $files = $_FILES['files'];
  $filters = $_POST['filters'];
  $periods = $_POST['periods'];
  
  $existing_files = (int)getNumberOfFiles();
  $check_api_call = true;
  $files_uploaded_count = 0;
  for($i = 0; $i < count($files['name']); $i++){
    $newName = "slide_" . $existing_files . ".pptx";
    $img_name = "slide_" . $existing_files . ".png";
    $decodePeriod = json_decode($periods[$i] , true);
    $decodeFilter = json_decode($filters[$i], true);
    $temp_filter_string = "";
    foreach($decodeFilter as $temp_filter){
      $temp_filter_string .= $temp_filter['filter_name'] . '|||';
      updateFilterTable($temp_filter['filter_name'], (int)$temp_filter['filter_type']);
    }
    $temp_filter_string = rtrim($temp_filter_string, '|||');
    var_dump($temp_filter_string);
    $decoded_type = $decodeFilter['filter_type'];
    if(isset($decodePeriod["first"])){
        $first_period = $decodePeriod['first'];
    }
    if(isset($decodePeriod["second"])){
        $second_period = $decodePeriod['second'];
    }
    if(isset($_POST["third"])){
        $third_period = $decodePeriod['third'];
    }
    $existing_files++;
    $tmp_name = $files['tmp_name'][$i];
    uploadFile($newName, $tmp_name, $temp_filter_string, $first_period, $second_period, $third_period, $img_name);
    $output_arr['file - ' . $i] = $files['name'][$i];
    $files_uploaded_count++;
    if(!$check_api_call){
        $files_uploaded_count-=1;
        $output_arr.pop();
        $output_arr['failed'] = "Api calls ran out";
        $output_arr['files_uploaded'] = $files_uploaded_count;
        echo json_encode($output_arr);
        break;
    }
  }
  if($check_api_call){
    $output_arr['success'] = "Failes uploaded";
    $output_arr['files_uploaded'] = $files_uploaded_count;
    echo json_encode($output_arr);
  }
  $mysqli->close();
}
?>