<?php

include('header.php');

echo"

<script src='https://code.jquery.com/jquery-3.7.1.js' integrity='sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=' crossorigin='anonymous'></script>

<script src='https://code.jquery.com/ui/1.13.2/jquery-ui.js'   integrity='sha256-xLD7nhI62fcsEZK2/v8LsBcb4lG7dgULkuXoXB/j91c=' crossorigin='anonymous'></script>

<link rel='stylesheet' href='https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css'>

<div id='formContainer'>

<form id='uploadbanner' enctype='multipart/form-data' method='post' action='admin.php'>
<div class='messages'></div>
    <div class='upload-container'>

    <div id='dropZone'>
    <p>Drag one or more files to this <i>dropzone</i></p>
    <div><input type='button' id='chooseFiles' value='Choose files'></div></div>
    <div class='add-new-client'><span class='tooltip-msg'></span><input type='text' placeholder='Enter client name' id='client-name'><button class='add-client pptFB' type='button'>Add New Client</button></div>
    <div id='loadedFiles'></div>

    <input type='file' name='files[]' id='file' accept='.ppt, .pptx' multiple/>

    </div>

    <div class='tagSettings'>

    

    </div>

    <input type='submit' value='Upload files' name='submit' id='submit'>

    

 </form>



 </div>
<div class='info-box-text'>
You can use CTRL+V to paste multiple filters for a slide separated by a comma, or you can type your own and then click the 'Add filter' button.
</div>
 <link rel='stylesheet' href='./public/css/uploadForm.css'>

 <script type='text/javascript' src='./uploadForm.js'></script>

 </body></html>";

?>

