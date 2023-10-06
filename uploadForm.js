$(document).ready(function(){
    var allTags = [];
    var avaiableTags = [];
    var gdt = new DataTransfer();
    $.ajax({ 
        async: 'false',
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
            showTags(allTags);
        }
      });
        function showTags(allFiles) {
            allFiles.forEach(x => {
              if(x != ''){
                $('.tagList').append('<div class=etcTagEle>'+x+'</div>')
                avaiableTags.push(x);
              }
            })
            $('.etcTagEle').addClass('et');
        }
        $("#file").on('change', function(e){
            showFiles(e.target.files);
            
        })
        function getFileTags(node){
            let tagList = [];
            let tagEle = $(node).find('.selectedTags>.ftre');
            for(let i =0;i < tagEle.length; i++){
            let currentTagTxt = $(tagEle[i]).children().eq(0).text();
            tagList.push(currentTagTxt);
            }
            return tagList.join(',');
        }
        $('#submit').on('click', function(event){
          event.preventDefault();
            var formData = new FormData();
            $('.ftc').each(function(i, obj){
                let file = gdt.files[i];
                let tagSet = getFileTags(obj);
                formData.append('files[]', file);
                formData.append('tags[]', tagSet);

            })
            $.ajax({
              url: 'upload.php',
              type: 'POST',
              data: formData,
              cache: false,
              contentType: false,
              processData: false,
              success: function(data){
                $('#file').val('');
                $('#loadedFiles').html('');
                gdt.clearData();
              }
            })
        })
        function showFiles(fileList){
            var tempDataTransfer = new DataTransfer();
            console.log(fileList)
            for(let i =0; i < fileList.length; i++){
              if(fileList[i].type =='application/vnd.openxmlformats-officedocument.presentationml.presentation'){
                gdt.items.add(fileList[i]);
                tempDataTransfer.items.add(fileList[i]);
             }
            }
            $('#submit').css('display','block')
            for(let i =0; i < tempDataTransfer.files.length; i++){
                $('#loadedFiles').append(`<div class="ftc"><div class="fileContainer"><span>${tempDataTransfer.files[i].name}</span><input class="removeItem" value='X' type='button'></div><div class="tagFilter"><div><p>File Tags</p></div><div class="searchTagContainer"><input type="text" class="searchTags"></div></div><div class="selectedTags"></div><div class='at'><label for='tags'>Add additional tags</label><input type='text' placeholder='Tag name' class='customTag'><input type='button' class='createTag' value='Add Tag'></div></div>`);
            }
        }
        $("#loadedFiles").on('click', '.createTag', function(){
          let tagVal = $(this).parent().find('input.customTag').val().trim();
          let addToEle = $(this).parent().parent().find('div.selectedTags');
          if(!avaiableTags.includes(tagVal) && tagVal.length >0){
            avaiableTags.push(tagVal);
            createTag(tagVal, addToEle);
          }
        })
        $(document).on('drop dragover', function(ev){
          ev.preventDefault();
          if(ev.type == 'drop'){
              showFiles(ev.originalEvent.dataTransfer.files);
          }
        })
        // Remove file from list functionality
        $('#loadedFiles').on('click', '.removeItem', function(e){
            let index = $('.removeItem').index(this);
            let fileList = new Array(gdt.files.length);
            for(let i =0; i < gdt.files.length; i++){
                fileList[i] = gdt.files.item(i);
            }

            const dataTransfer = new DataTransfer();

            for(let i =0 ;i < fileList.length;i++){
                if(i != Number(index)){
                    dataTransfer.items.add(fileList[i]);
                }
            }
            gdt = dataTransfer;
            if(gdt.files.length == 0){
              $('#submit').css('display','none');
            }
            $(this).parent().parent().remove();

        })
        //Autocomplete search functionality
        function createTag(text, ele){
          console.log(text);
          let output = `<div class='ftre'><span>${text}</span><span class='removeTag'>X</span></div></div>`;
          $(ele).append(output)
          $('.removeTag').on('click', function(){
            $(this).parent().remove();
          })
        }
        $('#loadedFiles').on('input', '.searchTags', function(){
            $(this).autocomplete({
                source: avaiableTags,
                minLength: 0,
                scroll: true,
                select: function(event, ui){
                  let eleToAdd = $(this).parent().parent().parent().find('div.selectedTags');
                  createTag(ui.item.value, eleToAdd)
                    $(this).val('');
                    return false;
                }
            })
        })
        $('#chooseFiles').on('click',function(){
            $("#file").click();
        })
})