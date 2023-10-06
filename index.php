<?php

include('header.php');
echo'
<div id="tagList"></div>
<div id="header">
    <div id="pptContainer">';

function show_files($start) {
    $contents = scandir($start);
    array_splice($contents, 0,2);
    if (count($contents) == 0) {
        file_put_contents("./pptxtags.json", json_encode(""));
    }
    
    foreach ( $contents as $item ) {
        if ( is_dir("$start/$item") && (substr($item, 0,1) != '.')) {
            $itemFilename = explode(".",$item)[0]; 
            //echo "<div id='ppt_$item' class='iframeContainer'><iframe src='https://view.officeapps.live.com/op/embed.aspx?src=https://www.cquest-share.com/pptfilter/uploads/$item' width='100%' height='350px' frameborder='0'></iframe></div>";
            echo "<div class='iframeContainer'><div id='ppt_$item' class='slideContainer'><img src='https://www.cquest-share.com/pptfilter/uploads/$itemFilename.png' width='100%' height='350px'/></div><div class='buttons'><a class='pptFB' id='downloadBtn' href='https://www.cquest-share.com/pptfilter/uploads/$itemFilename.pptx'>Download</a><a id='editBtn' class='editTag pptFB'>Edit Tags</a><div class='trashImg'><img src='https://www.cquest-share.com/pptfilter/public/images/deleteBtn.png' width='40%' /></div></div></div>";
            show_files("$start/$item");
        } else {
            if ((substr($item, -4) == 'pptx')) {
                $itemFilename = explode(".",$item)[0]; 

                //echo "<div id='ppt_$item' class='iframeContainer'><iframe src='https://view.officeapps.live.com/op/embed.aspx?src=https://www.cquest-share.com/pptfilter/uploads/$item' width='100%' height='350px' frameborder='0'></iframe></div>";
                echo "<div class='iframeContainer'><div id='ppt_$item' class='slideContainer'><img src='https://www.cquest-share.com/pptfilter/uploads/$itemFilename.png' width='100%' height='350px'/></div><div class='buttons'><a class='pptFB' id='downloadBtn' href='https://www.cquest-share.com/pptfilter/uploads/$itemFilename.pptx'>Download</a><a id='editBtn' class='editTag pptFB'>Edit Tags</a><div class='trashImg'><img src='https://www.cquest-share.com/pptfilter/public/images/deleteBtn.png' width='40%' /></div></div></div>";
            }
        }
    }
}

show_files('./uploads');

echo "<script type='text/javascript'>
$(document).ready(function(){


    $('.trashImg').click(function(event) {
        var result = confirm('Please confirm you want to delete this item.');

        var fileName = $(event.target).parent().parent().parent().find('.slideContainer').attr('id').replace('ppt_','');

        if (result) {

            $.ajax({
                url: 'delete.php',
                type: 'POST',
                data: {
                    id: fileName
                },
                success: function(html)
                {
                    //console.log(fileName);
                    window.location.href = './index.php';
                }      
            });
        }
    });

    $('.editTag').click(function(event) {

        var imgName = $(event.target).parent().parent().find('.slideContainer').children().attr('src').split('/').slice(-1)[0];

        $.ajax({
            url: 'edit.php',
            type: 'POST',
            data: {
                id: imgName
            },
            success: function(response)
            {
                window.location.href = './edit.php?target=' + imgName;
            }      
        });

    });

    var allTags = [];
  
    $.ajax({ 
      type: 'GET', 
      url: 'https://www.cquest-share.com/pptfilter/pptxtags.json', 
      data: { get_param: 'value' }, 
      dataType: 'json',
      success: function (data) { 
          var allFiles = Object.keys(data.files);
          allFiles.forEach(file => {
            var fileTags = data.files[file].tags.split(',')
            fileTags.forEach(tag => {
              if (!allTags.includes(tag)) {
                allTags.push(tag);
              }
            });
        });
        RenderBtns(allTags);
        UpdateTags(data);
      }
    });

    function RenderBtns(tags) {
        document.getElementById('tagList').innerHTML = '';
        tags.forEach(tag => {
            if (tag != '') {
              document.getElementById('tagList').innerHTML += `<input type='button' class='tagBtn' value='` + tag + `' />`;
            }
          });

        const filterBtns = document.querySelectorAll('.tagBtn');
        const filters = [];

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function handleClick(event) {
                if (!event.target.classList.contains('active')) {
                    event.target.classList.add('active');
                    filters.push(event.target.value);
                    UpdatePPTx(filters);
                } else {
                    event.target.classList.remove('active');
                    removeItemOnce(filters, event.target.value);
                    UpdatePPTx(filters);
                }
                });
        });

        
    }

    function removeItemOnce(arr, value) {
      var index = arr.indexOf(value);
      if (index > -1) {
        arr.splice(index, 1);
      }
      return arr;
    }

    function UpdateTags(allFiles) {
      var updatedTags = [];
      var allImgs = document.getElementsByTagName('img')
      var allObjectsImgFiles = []
      for (var i = 0; i < allImgs.length; i++) {
        var fileName = allImgs[i].src.split('/').slice(-1)[0].split('.')[0] + '.pptx';
        allObjectsImgFiles.push(fileName);
        if (allFiles.files[fileName]) {
          allImgs[i].id = allFiles.files[fileName].tags;
          allFiles.files[fileName].tags.split(',').forEach(tags => {
              if(!updatedTags.includes(tags)) {
                updatedTags.push(tags);
              }
          });
        }

      }

      for (var i = 0; i < Object.keys(allFiles).length; i++) { 

 
        if (!allObjectsImgFiles.includes(Object.keys(allFiles)[i])) {

            $.ajax({
              url: 'delete.php',
              type: 'POST',
              data: {
                  id: Object.keys(allFiles)[i]
              },
              success: function(html)
              {
              }      
          });
        }
      }
      
     // RenderBtns(updatedTags);

      for (var i = 0; i < allObjectsImgFiles.length; i++) { 
        if (!Object.keys(allFiles).includes(allObjectsImgFiles[i])) {
          $.ajax({
            url: 'upload.php',
            type: 'POST',
            data: {
                file: allObjectsImgFiles[i].split('.')[0]
            },
            success: function(html)
            {
            }      
        });
        }
      }

    }
  
    function UpdatePPTx(filters) {

        var tags = document.querySelectorAll('.slideContainer > img');
        tags.forEach(tag => {
            var show = 1;
            filters.forEach(filter => {
                tagNames = tag.id.split(',')
                var targetID = tag.src.split('/').slice(-1)[0].replace('png','pptx');
                if (targetID) {
                if (!tagNames.includes(filter)) {
                    show = 0;
                    document.getElementById('ppt_' + targetID).parentElement.style.display = 'none';
                    return;
                } else if (show == 1) {
                    document.getElementById('ppt_' + targetID).parentElement.style.display = 'block';
                    return;
                }
            }           
        });
        
    })

      if (filters.length == 0) {
        tags.forEach(tag => {
            var targetID = tag.src.split('/').slice(-1)[0].replace('png','pptx');
            document.getElementById('ppt_' + targetID).parentElement.style.display = 'block';
        });
    }

    }

  }); 
</script></div></body></html>"
?>
