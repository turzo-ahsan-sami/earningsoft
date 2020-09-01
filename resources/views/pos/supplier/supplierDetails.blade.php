<style type="text/css">
    .detailsTable th{
        text-align: left !important;
    }
    .detailsTable td{
        text-align: left !important;
    }
</style>
<div id="myModal2" class="modal fade" style="margin-top:2%">
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
                                <th>Supplier Name :</th><td id="supplierName"></td>
                            </tr>
                             <tr>
                                <th>Supplier Company :</th><td id="supplierCompany"></td>
                            </tr>
                            <tr>
                                <th>Supplier Code :</th><td id="supplierCode"></td>
                            </tr>
                            <tr>
                                <th>Email :</th><td id="email"></td>
                            </tr>

                            <tr>
                                <th>Supplier Id:</th><td id="Id"></td>
                            </tr>
                            <tr>
                                <th>Website :</th><td id="supplierWebsite"></td>
                            </tr> 
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class='table table-hover detailsTable'>
                            <tr>
                                <th>Address :</th><td id="supplierAddress"></td>
                            </tr> 
                            <tr>
                                <th>Description :</th><td id="supplierDescription"></td>
                            </tr>
                            <tr>
                                <th>Reference No :</th><td id="supplierRefNo"></td>
                            </tr>
                            <tr>
                                <th>Mobile :</th><td id="mobile"></td>
                            </tr>
                        </table>
                    </div>
        		</div>
                    {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-danger pull-right closeBtn', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
        	</div>
		</div>
	</div>
</div>
