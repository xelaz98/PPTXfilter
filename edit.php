<?php
include('con_params.php');
include('header.php');
// file to be edited
$file_name = $_GET['file'];
$img_path = $_GET['img'];

// get file information (tags, path)

$mysqli = new mysqli($server, $user, $password, $db, $port);
$tags_stmt = $mysqli->prepare("SELECT FILE_TAGS, FIRST_PERIOD, SECOND_PERIOD, THIRD_PERIOD FROM PPT_FILES WHERE FILE_NAME= ?");
$tags_stmt->bind_param("s", $file_name);
$tags_stmt->execute();
$get_tags_result = $tags_stmt->get_result();
$tags_result = $get_tags_result->fetch_assoc();
$tags_string = $tags_result['FILE_TAGS'];
$first_period = $tags_result['FIRST_PERIOD'];
$second_period = $tags_result['SECOND_PERIOD'];
$third_period = $tags_result['THIRD_PERIOD'];
$tags_array = explode('|||', $tags_string);


echo"

<script src='https://code.jquery.com/jquery-3.7.1.js' integrity='sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=' crossorigin='anonymous'></script>

<script src='https://code.jquery.com/ui/1.13.2/jquery-ui.js'   integrity='sha256-xLD7nhI62fcsEZK2/v8LsBcb4lG7dgULkuXoXB/j91c=' crossorigin='anonymous'></script>

<link rel='stylesheet' href='https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css'>
<div id='formContainer'>
<div class='messages'></div>

";
echo "
<div class='iframeContainer'>
    <div class='new-file-container hide'><p>New file:</p><div class='file-container-wrapper'><p class='file-name-text'></p><p class='remove-btn'>X</p></div></div>

    <div id='$file_name' class='slideContainer'>
        <img src='$img_path' width='100%' height='350px'/>
    </div>
    <div class='management-buttons-container'>
    <div class='update-new-slide pptFB'>Upload New File</div>
    <div id='removeBtn' class='pptFB'>Remove All Tags</div>
    <div class='delete-file pptFB' data-name='$file_name'>Delete Slide</div>
    <div id='doneBtn' class='pptFB'>Apply Changes</div>
    </div>
</div>";

echo "<div class='selected-tags-container'><p>Slide Filters</p><div class='selectedTags'>";
for ($i = 0; $i < count($tags_array); $i++){
    echo "
    <div class='ftre'><span class='file-tag'>$tags_array[$i]</span><span class='removeTag'>X</span></div>";
}
echo "</div></div>";
echo "<div class='checkbox-container'><h2>Period</h2><div class='period-selector'><input type='checkbox' aria-value='$first_period' name='period-1' id='period-1' aria-value=class='first'><label for='period-1'>Period 1</label></div><div class='period-selector'><input type='checkbox' aria-value='$second_period' name='period-2' id='period-2' class='second'><label for='period-2'>Period 2</label></div><div class='period-selector'><input type='checkbox' name='period-3' id='period-3' aria-value='$third_period' class='third'><label for='period-3'>Period 3</label></div></div>";

echo "<div class='search-tag-container'><div class='at'><label for='tags'>Add Filters</label><input type='text' placeholder='Type filters here' class='customTag'><input type='button' class='createTag' value='Add Filter'></div></div>";

echo "</div>
<input type='file' name='file' id='file' accept='.ppt, .pptx'/>
 <script type='text/javascript' src='./editForm.js'></script>
 <link rel='stylesheet' href='./public/css/editForm.css'>
 </body>
 </html>";

?>