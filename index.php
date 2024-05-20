<?php
include('con_params.php');
$mysqli = new mysqli($server, $user, $password, $db, $port);

// get files
$file_stmt = $mysqli->prepare("SELECT * FROM PPT_FILES WHERE IS_DELETED=0 AND IS_OLD=0");
$file_stmt->execute();
$file_stmt_result = $file_stmt->get_result();

// get tags clients
$tag_stmt_clients = $mysqli->prepare("SELECT * FROM PPT_TAGS WHERE TAG_TYPE=0 ORDER BY TAG_NAME ASC;");
$tag_stmt_clients->execute();
$tag_stmt_clients_result = $tag_stmt_clients->get_result();

// get tags charts
$tag_stmt_charts = $mysqli->prepare("SELECT * FROM PPT_TAGS WHERE TAG_TYPE=1 ORDER BY TAG_NAME ASC");
$tag_stmt_charts->execute();
$tag_stmt_charts_result = $tag_stmt_charts->get_result();


mysqli_close($mysqli);

include('header.php');
echo "<link rel='stylesheet' href='./public/css/index-page-styling.css'>";

echo'<div id="mainGrid">
    <aside>
        <div id="searchBar">
            <input id="search" type="text" placeholder="Search..">
        </div>
        <div class="clear-filters">
        Clear filters
        </div>
        <div id="tagList">
            <div class="checkbox-container">
                <h2>Wave</h2>
                <div class="wave-selector">
                    <input type="checkbox" name="wave-1" id="wave-1" class="first" role="wave-selector">
                    <label for="wave-1">One wave</label>
                </div>
                <div class="wave-selector">
                    <input type="checkbox" name="wave-2" id="wave-2" class="second" role="wave-selector">
                    <label for="wave-2">Two waves</label>
                </div>
                <div class="wave-selector">
                    <input type="checkbox" name="wave-3" id="wave-3" class="third" role="wave-selector">
                    <label for="wave-3">Three waves</label>
                </div>
            </div>
            <div id="group_container_clients">
                <h2>Clients</h2>
                <div class="groups" id="group_Clients">';

while($row = $tag_stmt_clients_result->fetch_assoc()){
    $temp_tag_name = $row['TAG_NAME'];
    echo "<div class='tagBtn' data-tag='$temp_tag_name'>$temp_tag_name</div>";
}
echo "</div></div>";
echo "<div id='group_container_generic'><h2>Charts</h2><div class='groupd' id='group_Charts'>";

while($row= $tag_stmt_charts_result->fetch_assoc()){
    $temp_tag_name = $row['TAG_NAME'];
    echo "<div class='tagBtn' data-tag='$temp_tag_name'>$temp_tag_name</div>";
}

echo "</div></div></div></aside>";
echo '
<div id="header">
    <div id="pptContainer">';
    
    while($row = $file_stmt_result->fetch_assoc()){
        $file_id = $row['ID'];
        $file_name = $row['FILE_NAME'];
        $file_path = $row['PPT_FILE_PATH'];
        $tags = $row['FILE_TAGS'];
        $img_path = $row['IMG_PATH'];
        $first_period = $row['FIRST_PERIOD'];
        $second_period = $row['SECOND_PERIOD'];
        $third_period = $row['THIRD_PERIOD'];
        echo "
        <div class='iframeContainer' data-tags='$tags' data-first-period='$first_period' data-second-period='$second_period' data-third-period='$third_period'>
            <input type='checkbox' class='file-checked' data-file='$file_path'>
            <div id='$file_name' class='slideContainer'>
                <img src='$img_path' width='100%' height='350px'/>
            </div>
            <div class='buttons'>
                <a class='pptFB download-btn' href='$file_path' data-file='$file_name'>Download</a>
                <a class='editTag pptFB' data-file='$file_name' data-image='$img_path' href='./edit.php?file=$file_name&img=$img_path'>Edit</a>
            </div>
        </div>";
    }
echo '</div>';
// echo '<div class="pagination_wrapper">
//   <div class="pagination">
//     <div class="previous button">Previous</div>
//     <!-- insert pagination buttons here -->
//     <div class="next button insertbeforer">Next</div>
//   </div>
// </div>';

echo "
</div></div>
<script type='text/javascript' src='./index.js'></script>
</body>
</html>"
?>
