$(document).ready(function () {
    var allFilters = [];
    let clients = [];
    var availableFilters = [];
    var gdt = new DataTransfer();

    $.ajax({
        type: 'GET',
        url: './get_clients.php',
        success: function (data) {
            let result = JSON.parse(data);
            clients = result;
        }
    })


    $.ajax({
        type: 'GET',
        url: './get_tags.php',
        success: function (data) {
            let result = JSON.parse(data);
            allFilters = result;
        }
    });
    $("#file").on('change', function (e) {
        showFiles(e.target.files);
        $('.add-new-client').addClass('show');
        $('#submit').css('display', 'block');
    })
    $("#client-name").on('input', function(){
        $(".tooltip-msg").removeClass('client-exists');
        $(".tooltip-msg").text('');
    })
    $('.add-client').on('click', function () {
        let clientName = $("#client-name").val().trim();
        if (clientName.length > 0 && !clients.includes(clientName)) {
            let clientContainers = $('.client-selector-container');
            clients.push(clientName);
            for(let i = 0; i < clientContainers.length; i++){
                $(clientContainers[i]).append(`<div class="client"><input type="checkbox" id="${clientName}-${i}" aria-type="0" class="client-selector" data-value="${clientName}"><label for="${clientName}-${i}">${clientName}</label></div>`)
            }
            $("#client-name").val('');
        }
        else{
            $(".tooltip-msg").addClass('client-exists');
            $(".tooltip-msg").text('Client already exists');
            setTimeout(() => {
                $(".tooltip-msg").removeClass('client-exists');
                $(".tooltip-msg").text('');
            }, 3500);
        }
    })
    function getFilePeriods(node) {
        let periods = {};
        let periodList = $(node).find('input[type="checkbox"]:not(".client-selector"):checked');
        for (let i = 0; i < periodList.length; i++) {
            let periodQ = $(periodList[i]).attr('class');
            periods[periodQ] = 1;
        }
        return periods;
    }
    function getFileTags(node) {
        let tagList = [];
        let tagEle = $(node).find('.selectedTags>.ftre');
        for (let i = 0; i < tagEle.length; i++) {
            let tagType = $(tagEle[i]).attr('aria-type');
            let currentTagTxt = $(tagEle[i]).children().eq(0).text();
            let filter = {
                filter_name: currentTagTxt, 
                filter_type: tagType
            };
            tagList.push(filter);
        }
        return tagList;
    }

    $('#submit').on('click', function (event) {
        $(this).removeClass('show');
        event.preventDefault();
        var formData = new FormData();
        $('.ftc').each(function (i, obj) {
            let file = gdt.files[i];
            let tagSet = JSON.stringify(getFileTags(obj));
            let periodList = JSON.stringify(getFilePeriods(obj));
            formData.append('files[]', file);
            formData.append('filters[]', tagSet);
            formData.append('periods[]', periodList);
        })
        $.ajax({
            url: 'upload.php',
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $(".messages").text('Your files are being processed please be patient and do not refresh the page.');
                $('.messages').removeClass('error');
                $('.messages').removeClass('success');
                $('.messages').addClass('processing');
            },
            success: function (data) {
                let res = JSON.parse(data);

                if (res.success){
                    $('#file').val('');
                    $(".messages").text('Your files have been successfully uploaded and the page will now automatically refresh.');
                    $('.messages').removeClass('error')
                    $('.messages').addClass('success')
                    $('.messages').removeClass('processing')
                    $('#loadedFiles').html('');
                    $('.add-new-client').remove('show');
                    setTimeout(() => {
                        location.reload();
                    }, 2500);
                    gdt.clearData();

                }
                else{
                    $('#file').val('');
                    $(".messages").text('Failed to upload one or more files. Refresh the page and try again. The page will automatically refresh in 5 seconds.');
                    $('.messages').removeClass('success')
                    $('.messages').addClass('error')
                    $('.messages').removeClass('processing')
                    $('#loadedFiles').html('');
                    $('.add-new-client').remove('show');
                    gdt.clearData();
                    setTimeout(() => {
                        location.reload();
                    }, 5000);
                }
            },
            error: function () {
                $(".messages").html("Something went wrong, contact an administrator.");
                $('.messages').addClass('error')
                $('.messages').removeClass('success')
                $('.messages').removeClass('processing')
            }

        })
    })
    $(document).on("ajaxSend", function () {
        $("#messages").html("Your files are being processed please be patient.");
        $("#loadedFiles").hide();
        $("submit").removeClass('show');
    }).on("ajaxComplete", function () {
        $("#loadedFiles").show();
    });
    function createClientContainer(fileN) {
        let html = `<div class='client-selector-wrapper'><p>Select client/s</p><div class='client-selector-container'>`;
        for (let i = 0; i < clients.length; i++) {
            html += `<div class='client'><input type='checkbox' id='${clients[i]}-${fileN}' aria-type='0' class='client-selector' data-value='${clients[i]}'><label for='${clients[i]}-${fileN}'>${clients[i]}</label></div>`;
        }

        html += `</div></div>`;
        return html;
    }
    function showFiles(fileList) {
        var tempDataTransfer = new DataTransfer();
        for (let i = 0; i < fileList.length; i++) {
            if (fileList[i].type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {

                let fileExists = Array.from(gdt.files).some(file => file.name === fileList[i].name);
                if (!fileExists) {
                    gdt.items.add(fileList[i]);
                    tempDataTransfer.items.add(fileList[i]);
                }
            }

        }
        let globalItemLength = parseInt(gdt.files.length);
        let tempItemLength = parseInt(tempDataTransfer.files.length);
        $('#submit').addClass('show')
        for (let i = globalItemLength; i < globalItemLength + tempItemLength; i++) {
            let fileName = `<div class="file-wrapper"><p>File name</p><div class='file-container'><span>${tempDataTransfer.files[i - globalItemLength].name}</span><input class='removeItem' value='X' type='button'></div></div>`;
            let selectedTags = `<div class='selectedTagsContainer'><p>Selected Filters</p><div class='selectedTags'></div></div>`
            let addTags = `<div class='at'><p>Add file filters</p><input type='text' placeholder='Type filters here' class='customTag'><input type='button' class='createTag' value='Add Filter'></div>`;
            let periodSelector = `<div class="checkbox-container"><h2>Period</h2><div class="period-selector"><input type="checkbox" name="period-1-${i}" id="period-1-${i}" class="first"><label for="period-1-${i}">Period 1</label></div><div class="period-selector"><input type="checkbox" name="period-2-${i}" id="period-2-${i}" class="second"> <label for="period-2-${i}">Period 2</label></div><div class="period-selector"><input type="checkbox" name="period-3-${i}" id="period-3-${i}" class="third"><label for="period-3-${i}">Period 3</label></div></div>`;
            let clientSelector = createClientContainer(i);

            let html = `<div class="ftc">${fileName}${selectedTags}${addTags}${periodSelector}${clientSelector}</div>`;

            $('#loadedFiles').append(html);
        }
    }


    $("#loadedFiles").on('paste', '.customTag', function (e) {
        let tags = e.originalEvent.clipboardData.getData('text').split("|||").map(function (item) {
            return item.trim();
        });
        let addToEle = $(this).parent().parent().find('div.selectedTags');
        tags.forEach((x) => {
            if (x.length > 0) {
                createFilter(x, addToEle, '1');
            }
        });
        setTimeout(() => {
            $(this).val("")
        }, 100);
    })
    $("#loadedFiles").on('click', '.createTag', function () {

        let tagVal = $(this).parent().find('input.customTag').val().trim();

        let addToEle = $(this).parent().parent().find('div.selectedTags');

        if (tagVal.length > 0) {
            createFilter(tagVal, addToEle, '1');
            $(this).parent().find('input.customTag').val("");
        }
    })

    $(document).on('drop dragover', function (ev) {
        ev.preventDefault();
        if (ev.type == 'drop') {
            showFiles(ev.originalEvent.dataTransfer.files);
        }
    })

    // Remove file from list functionality
    $('#loadedFiles').on('click', '.removeItem', function (e) {
        let index = $('.removeItem').index(this);
        const dataTransfer = new DataTransfer();

        Array.from(gdt.files)
            .filter((file, i) => i !== Number(index))
            .forEach(file => dataTransfer.items.add(file));

        gdt = dataTransfer;

        if (gdt.files.length == 0) {
            $('#submit').css('display', 'none');
            $('.add-new-client').removeClass('show');
        }

        $(this).parent().parent().parent().remove();
    });

    // check if filter exists before adding it to the autocomplete array
    function checkExistingFilter(text, ele) {
        let children = $(ele).children();
        let tagExists = false;
        for (let i = 0; i < children.length; i++) {
            if ($(children[i]).attr('aria-tags') == text) {
                tagExists = true;
            }
        }
        return tagExists;
    }

    // create an HTML element based on the selected filter
    function createFilter(text, ele, tagtype) {

        let output = `<div class='ftre' aria-tags='${text}' aria-type='${tagtype}'><span>${text}</span><span class='removeTag'>X</span></div></div>`;
        if (!availableFilters.includes(text)) {
            availableFilters.push(text);
        }
        let createdTags = $(ele).children();
        if (createdTags.length > 0) {
            for (let i = 0; i < createdTags.length; i++) {
                if (!checkExistingFilter(text, ele)) {
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
    $("#loadedFiles").on('change', '.client-selector', function (e) {
        if ($(this).prop('checked') == true) {
            let filterContainer = $(this).parent().parent().parent().parent().find('div.selectedTags');
            let filterText = $(this).attr('id').split('-')[0];
            createFilter(filterText, filterContainer, '0');
        }
        else {
            let temp = $(this).parent().parent().parent().parent().find('div.selectedTags');
            let filterText = $(this).attr('id').split('-')[0];
            let tagToRemove = $(temp).children().filter(function () {
                return $(this).attr('aria-tags') == filterText;
            });
            $(tagToRemove).remove();
        }
    })
    // autofill search
    $('#loadedFiles').on('input click', '.customTag', function () {
        $(this).autocomplete({
            source: allFilters,
            minLength: 0,
            scroll: true,
            select: function (event, ui) {
                let eleToAdd = $(this).parent().parent().find('div.selectedTags');
                createFilter(ui.item.value, eleToAdd, '1')
                $(this).val('');
                return false;
            }
        })
    })

    // show file explorer
    $('#chooseFiles').on('click', function () {
        $("#file").click();
    })
})