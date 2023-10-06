<?php

include('header.php');

if ($_POST['id'] == 'done'){

    $data = json_decode(file_get_contents("./API.json"), true);
    $data["keys"][$_POST['key']]["key"] = $_POST['key'];
    $data["keys"][$_POST['key']]["docs"] = '0';
    file_put_contents("./API.json", json_encode($data));

}

echo"
<script src='https://code.jquery.com/jquery-3.7.1.js' integrity='sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=' crossorigin='anonymous'></script>
<script src='https://code.jquery.com/ui/1.13.2/jquery-ui.js'   integrity='sha256-xLD7nhI62fcsEZK2/v8LsBcb4lG7dgULkuXoXB/j91c=' crossorigin='anonymous'></script>
<link rel='stylesheet' href='https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css'>
<div id='formContainer'>
    <input type='text' class='apiField'/>
    <input type='button' value='Add new API key' id='apiBtn'/>

 </div>
 <link rel='stylesheet' href='./public/css/uploadForm.css'>
 <script type='text/javascript' src='./addAPI.js'></script>
 </body></html>";

?>