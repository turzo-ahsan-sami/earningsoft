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
                                <th>Customer Name :</th><td id="customerName"></td>
                            </tr>
                           
                            <tr>
                                <th>Email :</th><td id="customerEmail"></td>
                            </tr>
                           
                            <tr>
                                <th>Address :</th><td id="customerpreAddress"></td>
                            </tr> 
                            

                            <!-- {{-- <tr>
                                <th>Customer Id:</th><td id="Id"></td>
                            </tr> --}} -->
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class='table table-hover detailsTable'>
                            <tr>
                                <th>Mobile :</th><td id="mobile"></td>
                            </tr>
                           
                            <tr>
                                <th>Description:</th><td id="customerDescription"></td>
                            </tr>
                           
                        </table>
                    </div>
                   <!--  {{-- <div class="col-md-4 table-responsive">
                        <table class='table table-hover'>
                            <tr>
                                <th class="text-center">Images view</th>
                            </tr>
                        </table>
                    </div> --}} -->
        		</div>
                    {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-danger pull-right closeBtn', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
        	</div>
		</div>
	</div>
</div>
