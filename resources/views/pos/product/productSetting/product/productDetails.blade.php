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
                                <th>Product Name :</th><td id="ProductName"></td>
                            </tr>
                            <tr>
                                <th>Product Code :</th><td id="ProductCode"></td>
                            </tr>
                            <!-- <tr>
                                <th>Product Id:</th><td id="Id"></td>
                            </tr> -->
                            {{-- <tr>
                                <th>Description:</th><td id="Description"></td>
                            </tr>

                            <tr>
                                <th>Group:</th><td id="Group"></td>
                            </tr>
                            <tr>
                                <th>Catagory:</th><td id="Catagory"></td>
                            </tr>
                            <tr>
                                <th>Sub Catagory:</th><td id="SubCatagory"></td>
                            </tr>
                            <tr>
                                <th>Brand:</th><td id="Brand"></td>
                            </tr>
                            <tr>
                                <th>Model :</th><td id="Model"></td>
                            </tr>
                            <tr>
                                <th>Size :</th><td id="Size"></td>
                            </tr>
                            <tr>
                                <th>Color :</th><td id="Color"></td>
                            </tr> --}}
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class='table table-hover detailsTable'>
                            <!-- <tr>
                                <th>Cost Price:</th><td id="CostPrice"></td>
                            </tr>
                            <tr>
                                <th>Sales Price:</th><td id="SalesPrice"></td>
                            </tr> -->
                            <tr>
                                <th>Type:</th><td id="productType"></td>
                            </tr>
                            <tr>
                                <th>Unit:</th><td id="productUnit"></td>
                            </tr>
                             {{-- <tr>
                                <th>Package List:</th><td id="productPackageList"></td>
                            </tr> --}}
                        </table>
                    </div>
                    {{-- <div class="col-md-4 table-responsive">
                        <table class='table table-hover'>
                            <tr>
                                <th class="text-center">Images view</th>
                            </tr>
                        </table>
                    </div> --}}
        		</div>
                    {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-danger pull-right closeBtn', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
        	</div>
		</div>
	</div>
</div>
