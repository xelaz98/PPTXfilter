$(document).ready(function(){

    // var count_items = $("#pptContainer .iframeContainer").length;

    // // Keep a record of current page.
    // var current_page = 1;

    // // divide items by 4
    // var separate_items = Math.ceil(count_items / 4);

    // // create empty variable
    // var page_division = "";

    // if (separate_items % 1 != 0) {
    //     page_division = separate_items + 1;
    //     console.log(page_division);
    // } else {
    //     page_division = separate_items;
    //     console.log(page_division);
    // }

    // for (var items_pagination = 1; items_pagination < page_division; items_pagination++) {
    //     $(".pagination .insertbeforer").before("<div class='button page" + items_pagination + "'>" + items_pagination + "</div>");
    // };

    // // hide all items
    // $('.iframeContainer').addClass('hideme');

    // // display first 10 items
    // $.each($('.iframeContainer'), function (index, value) {
    //     if (index <= 4) {
    //         $(this).toggleClass('hideme')
    //     }
    // });

    // // display items from 1-10
    // $(".page1").click(function () {
    //     current_page = 1;
    //     $('.iframeContainer').addClass('hideme');
    //     for (var item = 1; item < 11; item++) {
    //         $(".iframeContainer:nth-of-type(" + item + ")").removeClass('hideme');
    //     };
    // });
    // // display items from 11-20
    // $(".page2").click(function () {
    //     current_page = 2;
    //     $('.iframeContainer').addClass('hideme');
    //     for (var item = 11; item < 21; item++) {
    //         $(".iframeContainer:nth-of-type(" + item + ")").removeClass('hideme');
    //     };
    // });
    // // display items from 21-30
    // $(".page3").click(function () {
    //     current_page = 3;
    //     $('.iframeContainer').addClass('hideme');
    //     for (var item = 21; item < 31; item++) {
    //         $(".iframeContainer:nth-of-type(" + item + ")").removeClass('hideme');
    //     };
    // });
    // // display items from 31-33
    // $(".page4").click(function () {
    //     current_page = 4;
    //     $('.iframeContainer').addClass('hideme');
    //     for (var item = 31; item < 41; item++) {
    //         $(".iframeContainer:nth-of-type(" + item + ")").removeClass('hideme');
    //     };
    // });
    // $('.next').click(function () {
    //     //count_items=total
    //     if ((current_page) * 10 >= count_items) {
    //         return;
    //     }
    //     $('.iframeContainer').addClass('hideme');

    //     for (var item = ((current_page) * 10 + 1); item < ((current_page + 1) * 10 + 1); item++) {
    //         $(".iframeContainer:nth-of-type(" + item + ")").removeClass('hideme');
    //     };
    //     current_page += 1;
    // })
    // $('.previous').click(function () {


    //     if (current_page == 1) {
    //         return;
    //     }
    //     $('.iframeContainer').addClass('hideme');
    //     current_page -= 1;
    //     for (var item = ((current_page - 1) * 10 + 1); item < ((current_page) * 10 + 1); item++) {
    //         $(".iframeContainer:nth-of-type(" + item + ")").removeClass('hideme');
    //     };

    // })




    // -------------------------------------------------------------
    $('.iframeContainer').show();
    $('.file-checked').on('change', function(){
        if($(this).prop('checked')){
            $(this).parent().addClass('file-for-download')
        }
        else{
            $(this).parent().removeClass('file-for-download');
        }
    })
    $('#search').on('keyup', function () {
        applyFilters();
        var searchText = $(this).val().toLowerCase().trim()
        searchText = searchText.length > 0 ? searchText.split(' ') : [];
        $('#group_Charts .tagBtn').each(function () {
            var itemTxt = $(this).attr('data-tag').toLowerCase().trim();
            var isMatch = false;
            for (var i = 0; i < searchText.length; i++) {
                if (searchText[i].length > 0 && itemTxt.indexOf(searchText[i]) != -1) {
                    isMatch = true;
                    break;
                }
            }
            if (isMatch || searchText.length == 0) {
                $(this).removeClass('hidden');
            } else {
                $(this).addClass('hidden');
            }
        });
    });

    // clear filters
    $('.clear-filters').on('click', function(){
        $("input[type='checkbox']").prop('checked', false);
        $('.tagBtn').removeClass('active');
        $('#search').val("");
        $('.tagBtn').removeClass('hidden');
        $('.iframeContainer').show();
    })

    function applyFilters() {
        var selectedTags = [];
        let search_bar_filters = $("#search").val().split(",").map(ele => ele.trim().toLowerCase());
        if(selectedTags.length == 0){
            selectedTags = search_bar_filters;
        }
        else{
            selectedTags.concat(search_bar_filters);
        }
        $('.tagBtn.active').each(function() {
            selectedTags.push($(this).attr('data-tag').toLowerCase());
        });

        let selectedBoxes = $("input[role='wave-selector']:checked");
        var first = $('.first').is(":checked") ? "1" : "0";
        var second = $('.second').is(":checked") ? "1" : "0";
        var third = $('.third').is(":checked") ? "1" : "0";
        $('.iframeContainer').each(function() {
            var iframeTags = $(this).attr('data-tags').trim().toLowerCase();
            var match = false;
            for (var i = 0; i < selectedTags.length; i++) {
                if(iframeTags.includes(selectedTags[i])){
                    match = true;
                    break;
                }``
            }
    
            if (match || selectedTags.length === 0) {
                if(first == "0" && second == "0" && third == "0") {
                    $(this).show();
                }
                else if(selectedBoxes.length == 1){
                    let selectedBoxClass = $(selectedBoxes[0]).attr('class');
                    if($(this).attr(`data-${selectedBoxClass}-period`) === '1') {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                } 
                else if(selectedBoxes.length == 2){
                    let firstSelectedBoxClass = $(selectedBoxes[0]).attr('class');
                    let secondSelectedBoxClass = $(selectedBoxes[1]).attr('class');
                    if ($(this).attr(`data-${firstSelectedBoxClass}-period`) === '1' || $(this).attr(`data-${secondSelectedBoxClass}-period`) === '1') {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                }
                else {
                    if($(this).attr("data-first-period") === first || $(this).attr("data-second-period") === second || $(this).attr("data-third-period") === third) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                }
            } else {
                $(this).hide();
            }
        });
    }
    
    $('.tagBtn').on('click', function() {
        if(!$(this).hasClass('active')){
            $(this).addClass('active');
        }
        else{
            $(this).removeClass('active');
        }
        applyFilters();
    });
    
    $("input[type='checkbox']").change(applyFilters);
}); 