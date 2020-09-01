 function showAccessDeniedMessage(){
    
    var markUp = "<div id='accessDeniedModal' class='modal fade' style='margin-top:3%'>"+
    "<div class='modal-dialog'>"+
    "<div class='modal-content'>"+
    "<div class='modal-header'>"+
    "<h4 class='modal-title' style='text-align: center;clear:both;background-color:#bf3e37;color:white; padding:10px'>Access Denied!</h4>"+
    "</div>"+
    "<div class='modal-body'> "+
    "<h3>Sorry, You don't have Access.</h3>"+
    "<div class='modal-footer'>"+
    "<input type='hidden' name='productId' id='deleteModalProductId'>"+
    "<button class='btn btn-danger glyphicon glyphicon-remove' data-dismiss='modal' type='button'> Close</button>"+
    "</div>"+
    "</div>"+
    "</div>"+
    "</div>"+
    "</div>";
    if ($("#accessDeniedModal").length<1) {
    	$('body').append(markUp);
    }
    
    $("#accessDeniedModal").modal('show');
}
