 function hasAccess(targetUrl){
    var pathArray = location.href.split( '/' );
    var protocol = pathArray[0];
    var host = pathArray[2];
    var subDomain = pathArray[3] + '/public';
    var url = protocol + '//' + host+'/'+subDomain;
    url = url + '/hasAccess';

    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        async: false,
        data: {targetUrl: targetUrl},
    })
    .done(function(data) {
        if (data.accessDenied) {
           showAccessDeniedMessage();
           accessPermited = 0;
        }
        else{
            accessPermited = 1;                       
        }                    
    })
    .fail(function() {
        alert("error");
    });
    
    return accessPermited;     
}
