$(document).ready(function () {

    let allTags = [];
    let avaiableTags = [];
    
    $.ajax({
        url: "./get_tags.php",
        type: "GET",
        success: function (data) {
            let result = JSON.parse(data);
            allTags = result;
            $(`input[type="checkbox"][aria-value='1']`).prop('checked', true);
        }
    })
    $('.update-new-slide').on('click', function(){
        $('#file').click();
    })

    $("#file").on('change', function(e){
        let fileName = $('.iframeContainer').children().eq(0).attr('id');
        $(".file-name-text").text(e.target.files[0].name);
        $('.new-file-container').addClass('show');
    })
    $('.remove-btn').on('click', function(){
        $("#file").val('');
        $('.new-file-container').removeClass('show');
    })
    $('.delete-file').click(function (event) {
        let result = confirm('Please confirm you want to delete this item.');

        let fileName = $(this).attr('data-name');
        if (result) {
            $.ajax({
                url: 'delete.php',
                type: 'POST',
                data: {
                    id: fileName
                },
                success: function (html) {
                    // location.reload();
                }
            });
        }
    });
    $('#removeBtn').click(function () {
        $('.selectedTags').empty();
    });

    $(".customTag").on('paste', function (e) {
        let tags = e.originalEvent.clipboardData.getData('text').split("|||").map(function (item) {
            return item.trim();
        });
        let addToEle = $('.selectedTags')
        tags.forEach((x) => {
            if (x.length > 0) {
                createTag(x, addToEle);
            }
        });
        setTimeout(() => {
            $(this).val("")
        }, 100);
    })
    $(".createTag").click(function () {
        let tagVal = $(this).parent().find('input.customTag').val().trim();
        let addToEle = $('.selectedTags');
        if (!avaiableTags.includes(tagVal)) {
            avaiableTags.push(tagVal);
        }
        avaiableTags.sort((a, b) => a.localeCompare(b));
        let addedTags = $('.ftre').children().text().replace(' ', '').split('X');
        addedTags.pop();

        if (!addedTags.includes(tagVal) && tagVal.length > 0) {
            createTag(tagVal, addToEle);
            $('.customTag').val('')
        }

        $('.customTag').val('')
    });

    $('#doneBtn').click(function () {
        let first = $("#period-1").is(":checked") ? 1 : 0;
        let second = $("#period-2").is(":checked") ? 1 : 0;
        let third = $("#period-3").is(":checked") ? 1 : 0;

        let fileTags = $('.file-tag').map(function () {return $(this).text().trim();}).toArray();
        let fileName = $('.slideContainer').attr('id');
        let data = new FormData();
        data.append('fileName', fileName);
        for(let i =0;i < fileTags.length;i++){
            data.append('tags[]', fileTags[i]);
        }
        data.append('first_period', first);
        data.append('second_period', second);
        data.append('third_period', third);
        if($("#file").val().length > 0){
            data.append('file', $("#file").prop('files')[0]);
        }
        $.ajax({
            url: './update_slide.php',
            type: 'POST',
            processData: false,
            contentType: false,
            data: data,
            beforeSend: function () {
                $(".messages").text('The slide is being updated please do not close or refresh the browser.');
                $('.messages').removeClass('error');
                $('.messages').removeClass('success');
                $('.messages').addClass('processing');
            },
            success: function (response) {
                let decodeResp = JSON.parse(response);
                if (decodeResp.error){
                    $(".messages").text(decodeResp.error);
                    $('.messages').addClass('error')
                    $('.messages').removeClass('success')
                    $('.messages').removeClass('processing')
                }
                else if (decodeResp.success_updated){
                    $(".messages").text('The slide has been successfully updated');
                    $('.messages').removeClass('error')
                    $('.messages').addClass('success')
                    $('.messages').removeClass('processing')
                }
                else if(decodeResp.success_uploaded){
                    $(".messages").text('The new slide has been successfully uploaded. The page will now refresh');
                    $('.messages').removeClass('error')
                    $('.messages').addClass('success')
                    $('.messages').removeClass('processing')
                    setTimeout(() => {
                        location.reload();
                    }, 2500);
                }
            },
            error: function (msg) {
                $(".messages").text("Server error, please contact an administrator.");
                $('.messages').addClass('error')
                $('.messages').removeClass('success')
                $('.messages').removeClass('processing')
            }
        });
    });


    function checkExistingTag(text, ele) {
        let children = $(ele).children();
        let tagExists = false;
        for (let i = 0; i < children.length; i++) {
            if ($(children[i]).attr('aria-tags') == text) {
                tagExists = true;
            }
        }
        return tagExists;
    }
    function createTag(text, ele) {
        let output = `<div class='ftre' aria-tags='${text}'><span class='file-tag'>${text}</span><span class='removeTag'>X</span></div></div>`;
        if (!avaiableTags.includes(text)) {
            avaiableTags.push(text);
        }
        let createdTags = $(ele).children();
        if (createdTags.length > 0) {
            for (let i = 0; i < createdTags.length; i++) {
                if (!checkExistingTag(text, ele)) {
                    $(ele).append(output);
                    $('.removeTag').on('click', function () {
                        $(this).parent().remove();
                    })
                }
            }
        }
        else {
            $(ele).append(output);
            $('.removeTag').on('click', function () {
                $(this).parent().remove();
            })
        }
    }
    $('.removeTag').on('click', function () {
        $(this).parent().remove();
    })
    $('.customTag').on('input', function () {
        $(this).autocomplete({
            source: allTags,
            minLength: 0,
            scroll: true,
            select: function (event, ui) {
                let eleToAdd = $('.selectedTags');

                let addedTags = $('.file-tag').map(function () {return $(this).text().trim();}).toArray();
                if (!addedTags.includes(ui.item.value)) {
                    createTag(ui.item.value, eleToAdd)
                }
                $(this).val('');
                return false;
            }
        })
    })
})