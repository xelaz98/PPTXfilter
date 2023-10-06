$(document).ready(function(){
  var allTags = [];
  $.ajax({ 
    type: 'GET', 
    url: 'https://www.cquest-share.com/pptfilter/pptxtags.json', 
    data: { get_param: 'value' }, 
    dataType: 'json',
    success: function (data) { 
        console.log('index')
       console.log(data);
    }
  });
});
  
  