<?php
function sanitizeFileName($fileName) {
    return preg_replace('/[^A-Za-z0-9.\-]+/', '_', $fileName);
}

if(isset($_FILES['files'])) {
    $tags = $_POST['tags'];
    $files = $_FILES['files']['name'];
    $jsonString = file_get_contents("./pptxtags.json");
    $data = json_decode($jsonString, true);
    for($i = 0; $i < count($_FILES['files']['name']); $i++) {
        $tmpFilePath = $_FILES['files']['tmp_name'][$i];
        if ($tmpFilePath != ""){
            $newFileName = sanitizeFileName($_FILES['files']['name'][$i]);
            $newFilePath = "./uploads/" . $newFileName;
            $data['files'][$newFileName] = array(
                'name' => $newFileName,
                'tags' => $tags[$i]
            );
            if(move_uploaded_file($tmpFilePath, $newFilePath)){
                file_put_contents("./pptxtags.json",  json_encode($data));
                $apiFileName = explode(".", $newFileName); 
                $FileHandle = fopen('uploads/'. $apiFileName[0] . '.png', 'w+');
                $curl = curl_init();
                var_dump($apiFileName);
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
                    'document' => new \CURLFILE('uploads/'. $newFileName)
                  ),
                  CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer pdf_live_BMREUue07pxTfsaC1psbE7aVtB0tBhjeGUAkheZSP03'
                  ),
                  CURLOPT_FILE => $FileHandle,
                ));
                
                $response = curl_exec($curl);
                
                curl_close($curl);
                
                fclose($FileHandle);
            }
            else{
                echo "Failed to upload";
            }
        }
    }
}

?>
