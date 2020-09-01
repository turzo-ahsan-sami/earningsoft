<style type="text/css">
    .detailsTable th{
        text-align: left !important;
    }
    .detailsTable td{
        text-align: left !important;
    }
</style>
<div id="myModal3" class="modal fade" style="margin-top:2%">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title" style="clear:both"></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class='table table-hover detailsTable'>
                            <tr>
                                <th>Name :</th><td id="paymentName"></td>
                            </tr>
                            
                            <tr>
                                <th>Code :</th><td id="paymentCode"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class='table table-hover detailsTable'>
                            <tr>
                                <th>Description :</th><td id="paymentDescription"></td>
                            </tr>
                           
                        </table>
                    </div>
        		</div>
                    {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-danger pull-right closeBtn', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
        	</div>
		</div>
	</div>
</div>
