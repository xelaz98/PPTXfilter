$(document).ready(function(){
    
    var allTags = [];
    var currentPPTags = [];
    var avaiableTags = [];

    var currentPPTname = $('.slideContainer').attr('id').replace('ppt_','');

    $('#removeBtn').click(function() {

        $('.selectedTags').empty();
    });

    $(".createTag").click(function(){
        let tagVal = $(this).parent().find('input.customTag').val().trim();
        let addToEle = $(this).parent().parent().find('div.selectedTags');
        if(!avaiableTags.includes(tagVal)){
          avaiableTags.push(tagVal);
        }
        var addedTags = $('.ftre').children().text().replace(' ','').split('X');
        addedTags.pop();
        
        if (!addedTags.includes(tagVal)) {
          createTag(tagVal, addToEle);
          $('.customTag').val('')
        }

        $('.customTag').val('')
    });

    $('#doneBtn').click(function() {

        var updatedTags = $('.ftre').children().text().replace(' ','').split('X');
        updatedTags.pop();

        $.ajax({
            url: 'edit.php',
            type: 'POST',
            data: {
                id: 'done',
                pptName: currentPPTname,
                tags: updatedTags.join(',')
            },
            success: function(response)
            {
              window.location.href = './index.php';
            }      
        });
    });

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

            data.files[currentPPTname].tags.split(',').forEach(tag => {
                let eleToAdd = $('.selectedTags');
                createTag(tag, eleToAdd)
            });
            showTags(allTags);
        }
      });

    function createTag(text, ele){
        if (text != '') {
            let output = `<div class='ftre'><span>${text}</span><span class='removeTag'>X</span></div></div>`;
            $(ele).append(output)
            $('.removeTag').on('click', function(){
              $(this).parent().remove();
            })
        }
    }

    function showTags(allFiles) {
        allFiles.forEach(x => {
            if(x != ''){
            $('.tagList').append('<div class=etcTagEle>'+x+'</div>')
            avaiableTags.push(x);
            }
        })
        $('.etcTagEle').addClass('et');
    }

      $('.searchTags').on('input', function(){
        $(this).autocomplete({
            source: avaiableTags,
            minLength: 0,
            scroll: true,
            select: function(event, ui){
              let eleToAdd = $(this).parent().parent().parent().find('div.selectedTags');
              var addedTags = $('.ftre').children().text().replace(' ','').split('X');
              addedTags.pop();
              
              if (!addedTags.includes(ui.item.value)) {
                createTag(ui.item.value, eleToAdd)
              }
                $(this).val('');
                return false;
            }
        })
    })
})