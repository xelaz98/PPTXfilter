$(document).ready(function(){

    $('#apiBtn').click(function(event) {

        if($.trim($('.apiField').val()).length == 52) { 
            $.ajax({
                url: 'addAPI.php',
                type: 'POST',
                data: {
                    id: 'done',
                    key: $('.apiField').val()
                },
                success: function(response)
                {
                  console.log('success!');
                  window.location.href = './index.php';
                }      
            });
        }

    });

});