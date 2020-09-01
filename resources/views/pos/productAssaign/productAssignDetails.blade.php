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
                    <div class="col-md-12">
                        <table class='table table-hover detailsTable'>
                             <input id="VMproductAssignId" type="hidden"  value="" />
                            <tr>
                                <th width="25%">Company Name :</th><td id="VMcompanyName"></td>
                            </tr>
                            <tr>
                                <th>Product Name:</th><td id="VMproductName"></td>
                            </tr>
                            <tr id ="VMproductPackaged">
                                <th>Product Package:</th><td id="VMproductPakage"></td>
                            </tr>
                            <tr>
                                <th style="font-size:15px;">Seles Price:</th><td id=""></td>
                            </tr>
                             <tr>
                                <th>Sales Person:</th><td id="VMsalesPerson"></td>
                            </tr>
                            <tr>
                                <th>Head Office:</th><td id="VMsalesPriceHo"></td>
                            </tr>
                           
                            <tr>
                                <th>Branch:</th><td id="VMsalesPriceBo"></td>
                            </tr>
                            <tr>
                                <th style="font-size:15px;">Service Charge:</th><td id=""></td>
                            </tr>
                            <tr>
                                <th>Service Person:</th><td id="VMservicePerson"></td>
                            </tr>
                            <tr>
                                <th>Head Office:</th><td id="VMserviceChargeHo"></td>
                            </tr>
                            <tr>
                                <th>Branch:</th><td id="VMserviceChargeBo"></td>
                            </tr>
                        </table>
                    </div>
        		</div>
                    {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-danger pull-right closeBtn', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
        	</div>
		</div>
	</div>
</div>





