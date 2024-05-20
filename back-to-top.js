$(document).ready(function(){
    $(window).on('scroll', function(){
        scrollFunction();
    })
    $("#back-to-top").on('click', topFunction);
    function scrollFunction(){
        if ($("body").scrollTop() > 100 || $(document).scrollTop() > 100){
            $('#back-to-top').addClass('visible-back-to-top')
        }
        else{
            $("#back-to-top").removeClass('visible-back-to-top')
        }
    }

    function topFunction(){
        $("html, body").animate({scrollTop:0}, 'slow');
        $("#back-to-top").removeClass('visible-back-to-top')
    }
})