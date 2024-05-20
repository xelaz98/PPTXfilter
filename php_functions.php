<?php
function updateFilterTable($filter_string){
  global $mysqli;
  $filter_array = explode('|||',$filter_string);
  for($i = 0; $i < count($filter_array); $i++){
    $stmt = $mysqli->prepare("SELECT COUNT(*) as found FROM PPT_TAGS WHERE TAG_NAME= ?");
    $stmt->bind_param("s", $filter_array[$i]);
    $stmt->execute();
    $stmt_result = $stmt->get_result();
    $final_res = $stmt_result->fetch_assoc();
    if($final_res['found'] == 0 || $final_res['found'] == '0'){
      $temp_filter_name = $filter_array[$i];
      $tag_type_val = 1;
      $add_tag_stmt = $mysqli->prepare("INSERT INTO PPT_TAGS (TAG_NAME, TAG_TYPE) VALUES (? ,?)"); 
      $add_tag_stmt->bind_param("si", $temp_filter_name, $tag_type_val);
      $add_tag_stmt->execute();
    }
  }
}

function getNumberOfFiles(){
  global $mysqli;
  $count_stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM PPT_FILES");
  $count_stmt->execute();
  $count_result = $count_stmt->get_result();
  $final = $count_result->fetch_assoc();

  return $final['count'];
}

function generateImg($encoded_img_name, $encoded_file_name){
    global $mysqli;
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
          'document' => new CURLFILE('uploads/'. $encoded_file_name)
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
        $png_path_stmt->bind_param("ss", $img_path_val, $encoded_file_name);
        $png_path_stmt->execute();
    }
}
?>