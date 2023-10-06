<?php

include('header.php');

if ($_POST['id'] == 'done'){
    $data = json_decode(file_get_contents("./pptxtags.json"), true);
    $data["files"][$_POST['pptName']]["name"] = $_POST['pptName'];
    $data["files"][$_POST['pptName']]["tags"] = $_POST['tags'];
    file_put_contents("./pptxtags.json", json_encode($data));
}

echo"
<script src='https://code.jquery.com/jquery-3.7.1.js' integrity='sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=' crossorigin='anonymous'></script>
<script src='https://code.jquery.com/ui/1.13.2/jquery-ui.js'   integrity='sha256-xLD7nhI62fcsEZK2/v8LsBcb4lG7dgULkuXoXB/j91c=' crossorigin='anonymous'></script>
<link rel='stylesheet' href='https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css'>
<div id='formContainer'>";

$fileName = $_GET["target"];
$pptxName = str_replace("png","pptx",$_GET["target"]);

echo "<div class='iframeContainer'><div id='ppt_$pptxName' class='slideContainer'><img src='https://www.cquest-share.com/pptfilter/uploads/$fileName' width='100%' height='350px'/></div><a id='doneBtn' class='pptFB'>Done</a><a id='removeBtn' class='pptFB'>Remove All</a></div>";
echo "<div class='selectedTags'></div><div class='tagFilter'><div><p id='fileTags'>File Tags</p></div><div class='searchTagContainer'><input type='text' class='searchTags'></div></div><div class='at'><label for='tags' style='text-align:left;padding-left:5px'>Add additional tags</label><input type='text' placeholder='Tag name' class='customTag'><input type='button' class='createTag' value='Add Tag'></div>";
echo "</div>
 <script type='text/javascript' src='./editForm.js'></script>
 <link rel='stylesheet' href='./public/css/uploadForm.css'>
 <link rel='stylesheet' href='./public/css/editForm.css'>
 </body></html>";
?>