@extends('layouts/fams_layout')
@section('title', '| Additional Charge')
@section('content')

<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('famsAddAdditionalCharge/')}}" class="btn btn-info pull-right addViewBtn" style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Additional Charge</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">ADDITIONAL CHARGE LIST</font></h1>

        </div>

        <div class="panel-body panelBodyView"> 
        
        <div>

          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            /*$("#gnrGrounView").dataTable().yadcf([
    
            ]);*/
             $("#gnrGrounView").dataTable({
                   "oLanguage": {
                  "sEmptyTable": "No Records Available",
                  "sLengthMenu": "Show _MENU_ "
                  }
                });
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="gnrGrounView">
            <thead>
              <tr>
                <th width="32">SL#</th>
                <th>Bill No</th>
                <th>Product</th>
                  <th>Quantity</th>
                <th>Cost</th>
                  <th>Branch</th>
                  <th>Purchase Date</th>
                
                <th id="details" width="100" style="pointer-events:none;">Action</th>
                
              </tr>
              {{ csrf_field() }} 
            </thead>

            <tbody>            
              <?php $no=0; ?>
                    @foreach($additionalCharges as $additionalCharge)
                    @php
                        $productCode = DB::table('fams_product')->where('id',$additionalCharge->productId)->value('productCode');
                    @endphp
                    <tr class="item{{$additionalCharge->id}}">
                        <td class="text-center slNo">{{++$no}}</td>
                        <td>{{$additionalCharge->billNo}}</td>
                        <td>{{$prefix.$productCode}}</td>
                        <td>{{$additionalCharge->quantity}}</td>
                        <td>{{$additionalCharge->amount}}</td>
                    
                        <td>
                            <?php $branchName = DB::table('gnr_branch')->where('id',$additionalCharge->branchId)->value('name'); ?>
                            {{$branchName}}
                        </td>
                        <td>{{date('d-m-Y', strtotime($additionalCharge->purchaseDate))}}</td>
                        
                        <td class="text-center" width="80">

                        <a href="" data-toggle="modal" data-target="#view-modal-{{$additionalCharge->id}}" >
                            <i class="fa fa-eye" aria-hidden="true" ></i>
                          </a>&nbsp

                           @php
                             $hasDep = 0;
                            $data = DB::table('fams_depreciation_details')->where('productId',$additionalCharge->productId)->value('productId');
                            if($data>0){
                                $hasDep = 1;
                            }
                            
                            @endphp

                           @if(!$hasDep)

                          <a href="" data-toggle="modal" data-target="#edit-modal-{{$additionalCharge->id}}" >
                            <span class="glyphicon glyphicon-edit"></span>
                          </a>&nbsp

                          <a href="" data-toggle="modal" data-target="#delete-modal-{{$additionalCharge->id}}" >
                            <span class="glyphicon glyphicon-trash"></span>
                          </a>
                          @endif

                            
                        </td>
                        
                    </tr>
                  @endforeach
            </tbody>
            
          </table>
        </div>
      </div>
  </div>
  </div>
</div>
</div>


@foreach($additionalCharges as $adCharge)

    {{-- View Modal --}}
    <div id="view-modal-{{$adCharge->id}}" class="modal fade view-modal" style="margin-top:3%">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Additional Charge Details</h4>
                </div>
                <div class="modal-body">
                    <div class="row" style="padding-bottom: 20px;">

                        <div class="col-md-12" style="padding-left:0px;">

                            <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                <div class="form-horizontal form-groups">

                                    <div class="form-group">
                                        {!! Form::label('billNo', 'Bill No:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('billNo',$adCharge->billNo,['class'=>'form-control','autocomplete'=>'off','readonly']) !!}

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('branchName', 'Branch Name:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            <?php
                                            $viewModalBranchName = DB::table('gnr_branch')->where('id',$adCharge->branchId)->value('name');
                                            ?>
                                            {!! Form::text('viewModalBranchName', $viewModalBranchName, ['class' => 'form-control','readonly','id'=>'branchName','autocomplete'=>'off']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6" style="padding-right:0px;">{{--2nd col-md-6--}}
                                <div class="form-horizontal form-groups">
                                    <div class="form-group">
                                        {!! Form::label('totalQuantity', 'Quantity:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('totalQuantity',$adCharge->quantity,['class'=>'form-control','id'=>'totalQuantity-'.$adCharge->id,'autocomplete'=>'off','readonly']) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('totalAmount', 'Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('totalAmount',$adCharge->amount,['class'=>'form-control','id'=>'totalAmount-'.$adCharge->id,'autocomplete'=>'off','readonly']) !!}
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>

                    </div>
                    <table class="table table-striped table-bordered">
                        <thead>

                        <tr>

                            <th align="left">Item Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total Price</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($additionalChargeDetails as $additionalChargeDetail)
                            @if($adCharge->billNo==$additionalChargeDetail->additionalChargeBillNoId)
                                <tr>

                                    <td align="left">{{$additionalChargeDetail->productName}}</td>
                                    <td>{{$additionalChargeDetail->quantity}}</td>
                                    <td>{{$additionalChargeDetail->productPrice}}</td>
                                    <td>{{$additionalChargeDetail->totalPrice}}</td>
                                </tr>
                            @endif
                        @endforeach

                        </tbody>
                    </table>

                    <div class="modal-footer">

                        <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" id="footer_action_button_dismis" type="button"><span> Close</span></button>
                    </div>


                </div>
            </div>
        </div>
    </div>
    {{-- End View Modal --}}



    {{-- Edit Modal --}}
    <div id="edit-modal-{{$adCharge->id}}" class="modal fade edit-modal" style="margin-top:3%">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Additional Charge Details</h4>
                </div>
                <div class="modal-body">

                        <div class="row" style="padding-bottom: 20px;">

                            <div class="col-md-12" style="padding-left:0px;">

                                <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                    <div class="form-group">
                                    {!! Form::hidden('editModalPurchaseDate',$adCharge->purchaseDate,['id'=>'editModalPurchaseDate-'.$adCharge->id]) !!}
                                        {!! Form::label('editModalBillNo', 'Bill No:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                           {!! Form::text('editModalBillNo',$adCharge->billNo,['class'=>'form-control','id'=>'editModalBillNo-'.$adCharge->id,'autocomplete'=>'off','readonly']) !!}

                                    </div>
                                        </div>
                                        <div class="form-group">
                                        {!! Form::label('branchName', 'Branch Name:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            <?php
                                            $editModalBranchName = DB::table('gnr_branch')->where('id',$adCharge->branchId)->value('name');
                                            ?>
                                            {!! Form::text('editModalBranchName', $editModalBranchName, ['class' => 'form-control','readonly','id'=>'editModalBranchName-'.$adCharge->id,'autocomplete'=>'off']) !!}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('editModalProductCode', 'Product:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            
                                            {!! Form::text('editModalProductCode', $adCharge->productCode, ['class' => 'form-control','readonly','id'=>'editModalProductCode-'.$adCharge->id,'autocomplete'=>'off']) !!}
                                        </div>
                                    </div>


                                </div>
                                    </div>


                                <div class="col-md-6" style="padding-right:0px;">{{--2nd col-md-6--}}
                                    <div class="form-horizontal form-groups">
                                    <div class="form-group">
                                        {!! Form::label('editModalTotalQuantity', 'Quantity:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('editModalTotalQuantity',$adCharge->quantity,['class'=>'form-control','id'=>'editModalTotalQuantity-'.$adCharge->id,'autocomplete'=>'off','readonly']) !!}
                                        </div>
                                    </div>
                                        <div class="form-group">
                                            {!! Form::label('editModalTotalAmount', 'Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('editModalTotalAmount',$adCharge->amount,['class'=>'form-control','id'=>'editModalTotalAmount-'.$adCharge->id,'autocomplete'=>'off','readonly']) !!}
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>

                    </div>
                   

                    <div style="border-style: solid;border-width: 1px;clear:both;"></div><br>



                    {{-- <h4>Add Additional Product</h4> --}}

                    <div class="row" style="padding-bottom: 2px;display: none;">

                            <div class="col-md-12" style="padding-left:0px;">

                                <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                    <div class="form-group">
                                        {!! Form::label('additionalProductName', 'Additional Product Name:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                           {!! Form::text('additionalProductName',"",['class'=>'form-control','id'=>'addAdditionalProduct-'.$adCharge->id,'autocomplete'=>'off']) !!}
                                           <p id="additionalProductNamee-{{$adCharge->id}}"></p>

                                        </div>
                                        </div>
                                        </div>
                                </div>

                                 <div class="col-md-6" style="padding-right:2%;">
                                 <div class="form-group" style="padding-top: 2px;">
                                     {!! Form::button('Add Product',['id'=>'addAdditionalProductButton-'.$adCharge->id ,'class'=>'btn btn-primary btn-xs','style'=>'text-align:center;border-radius:0;width:80px;']) !!}
                                     </div>
                                 </div>

                            </div>
                    </div>

                    <div style="border-style: solid;border-width: 1px;clear:both; display: none;"></div><br>


<span id="tableError-{{$adCharge->id}}" style="display: none;"><font color="red">*Please add at least one Product</font></span>
                     <table id="addProductTable-{{$adCharge->id}}" class="table table-bordered responsive">
                        
                            <tr>
                                
                                    <th style="text-align:center;">

                                    <select name="itemName" id="itemName-{{$adCharge->id}}" class="form-control">
                                            <option value="">Select Product</option>
                                            @foreach($additionalProducts as $additionalProduct)
                                            <option value="{{$additionalProduct->id}}">{{$additionalProduct->name}}</option>
                                            @endforeach
                                            
                                        </select>

                                       

                                    </th>
                                    <th>
                                    {!! Form::text('itemQuantity', $value = null, ['id' => 'itemQuantity-'.$adCharge->id,'autocomplete'=>'off','placeholder'=>'Quantiy','style'=>'text-align:center;']) !!}
                                    
                                    </th>
                                    
                                    <th>
                                    {!! Form::text('itemPrice', $value = null, ['id' => 'itemPrice-'.$adCharge->id,'autocomplete'=>'off','placeholder'=>'Price','style'=>'text-align:center;']) !!}
                                    </th> 
                                    <th>
                                    {!! Form::text('itemTotal', $value = null, ['id' => 'itemTotal-'.$adCharge->id,'autocomplete'=>'off','placeholder'=>'Total','style'=>'text-align:center;','readonly']) !!}
                                    </th>
                                    <th><button id="addToCart-{{$adCharge->id}}" class='btn btn-primary btn-xs' style='text-align:center;border-radius:0;width:80px;'>Add to Cart</button></th>                                
                                
                            </tr>
                            <tbody>
                                <tr id="headerRow-{{$adCharge->id}}">
                                    <td>Product Name</td>
                                    <td>Quantity</td>
                                    <td>Price</td>
                                    <td>Total</td>
                                    <td>Action</td>
                                </tr>

                                @foreach($additionalChargeDetails as $additionalChargeDetail)
                            @if($adCharge->billNo==$additionalChargeDetail->additionalChargeBillNoId)
                                <tr class="valueRow-{{$adCharge->id}}">
                                    <td >

                                        {!! Form::hidden('idColumn',$additionalChargeDetail->productId,['class'=>'idColumn']) !!}
                                        {!! Form::text('nameColumn',$additionalChargeDetail->productName,['class'=>'form-control nameColumn']) !!}
                                    </td>
                                    <td >
                                        {!! Form::text('quantityColumn',$additionalChargeDetail->quantity,['class'=>'form-control quantityColumn','style'=>'text-align:center']) !!}
                                    </td>
                                    <td >
                                        {!! Form::text('priceColumn',$additionalChargeDetail->productPrice,['class'=>'form-control priceColumn','style'=>'text-align:center']) !!}
                                    </td>
                                    <td >
                                        {!! Form::text('totalColumn',$additionalChargeDetail->totalPrice,['class'=>'form-control totalColumn','style'=>'text-align:center','readonly']) !!}
                                    </td>
                                    <td><button class='btn btn-danger btn-x removeButton' style='padding: 2px 10px;'>Remove</button></td>
                                </tr>
                            @endif
                        @endforeach                         

                                
                                <tr>
                                    <td>Total Quantity</td>
                                    <td class="quantityTd"><span id="totalQuantitySpan-{{$adCharge->id}}" quantity="0"></span></td>
                                    <td>Total Amount</td>
                                    <td class="amountTd"><span id="totalAmountSpan-{{$adCharge->id}}" amount="0"></span></td>
                                    <td></td>
                                </tr>                                
                                   
                            </tbody>
                        </table>
                        

                    <div class="modal-footer">
                        <button class="btn actionBtn glyphicon glyphicon-check btn-success edit" id="save-{{$adCharge->id}}" type="button"><span> Update</span></button>
                        <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" id="footer_action_button_dismis" type="button"><span id=""> Close</span></button>
                    </div>


                </div>
            </div>
        </div>
    </div>
    {{-- End Edit Modal --}}

    {{-- Delete Modal --}}
        <div id="delete-modal-{{$adCharge->id}}" class="modal fade" style="margin-top:3%">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Delete!</h4>
                    </div>
                    <div class="modal-body">
                        <h2>Are You Confirm to Delete This Record?</h2>

                        <div class="modal-footer">
                            {!! Form::open(['url' => 'deleteFamsAdditionalCharge/']) !!}
                            <input type="hidden" name="adChargeId" value={{$adCharge->id}}>
                            <button  type="submit" class="btn actionBtn glyphicon glyphicon-check btn-success"><span id=""> Confirm</button>
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
                            {!! Form::close() !!}

                        </div>

                    </div>
                </div>
            </div>
        </div>
        {{-- End Delete Modal --}}

    {{--Edit Item Table Data--}}
    <script>
        $(document).ready(function () {
            var modalId = "{{$adCharge->id}}";

            /*Add Additional Product*/
            $("#addAdditionalProductButton-"+modalId).click(function() {

                var name = $("#addAdditionalProduct-"+modalId).val();
                var csrf = "{{csrf_token()}}";
                //debugger;
                //alert(name);
                
                    $.ajax({
                        type: 'post',
                        url: './ajaxStoreFamsAdditionalProduct',
                        data: {name:name, _token: csrf},
                        dataType: 'json',
                        success: function( _response ){
                            if(_response.errors) {
                                if (_response.errors['name']) {

                                    $("#additionalProductNamee-"+modalId).empty();
                                     $('#additionalProductNamee-'+modalId).append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
                                     $("#additionalProductNamee-"+modalId).show();

                                }
                            }
                            else{
                                alert("Product Inserted Successfully");
                                var lastValue =  parseInt($('#itemName-'+modalId+" option:last").val())+1;
                                $('#itemName-'+modalId).append("<option value='"+ lastValue+"'>"+name+"</option>");
                                $("#addAdditionalProduct-"+modalId).val("");
                            }
                            
                        },
                        error: function() {
                            alert("Error");
                        }

                    });
            });

           $("#addAdditionalProduct-"+modalId).on('input',function() {
            if (this.value=="") {
                $("#additionalProductNamee-"+modalId).show();
            }
            else{
             $("#additionalProductNamee-"+modalId).hide();   
            }
               
           });
            /*End Add Additional Product*/

            function calculateTotal(){
                var totalQuantity = parseFloat(0);
                var totalAmount = parseFloat(0);
                $("tr.valueRow-"+modalId).each(function() {
                    totalQuantity = totalQuantity + parseFloat($(this).find('.quantityColumn').val());
                    totalAmount = totalAmount + parseFloat($(this).find('.totalColumn').val());
                });

                $("#totalQuantitySpan-"+modalId).attr('quantity', totalQuantity);
            $("#totalAmountSpan-"+modalId).attr('amount', totalAmount);

            $("#totalQuantitySpan-"+modalId).html(totalQuantity);
            $("#totalAmountSpan-"+modalId).html(totalAmount);

                $("#editModalTotalQuantity-"+modalId).val(totalQuantity);
                $("#editModalTotalAmount-"+modalId).val(totalAmount);
            }



            var editModalTotalQuantity = $("#editModalTotalQuantity-"+modalId).val();
            var editModalTotalAmount = $("#editModalTotalAmount-"+modalId).val();

            //debugger;

            $("#totalQuantitySpan-"+modalId).attr('quantity', editModalTotalQuantity);
            $("#totalAmountSpan-"+modalId).attr('amount', editModalTotalAmount);

            $("#totalQuantitySpan-"+modalId).html(editModalTotalQuantity);
            $("#totalAmountSpan-"+modalId).html(editModalTotalAmount);

            $(".valueRow-"+modalId).find(".quantityColumn").on('input',function () {
                this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

                var quantity = parseFloat(this.value);
                var price = parseFloat($(this).closest('tr').find(".priceColumn").val());


                if(quantity!="" && price!=""){
                    var total = quantity * price;
                    $(this).closest('tr').find(".totalColumn").val(total);
                }
                calculateTotal();
            });

            $(".valueRow-"+modalId).find(".priceColumn").on('input',function () {
                this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

                var price = parseFloat(this.value);
                var quantity = parseFloat($(this).closest('tr').find(".quantityColumn").val());

                if(quantity!="" && price!=""){
                    var total = quantity * price;
                    $(this).closest('tr').find(".totalColumn").val(total);
                }

                calculateTotal();
            });


            /*Add to Cart*/
            $("#addToCart-"+modalId).click(function () {
            $("#tableError-"+modalId).hide();

            var itemId = $("#itemName-"+modalId+" option:selected").val();
            var itemName = $("#itemName-"+modalId+" option:selected").html();
            var itemQuantity = parseFloat($("#itemQuantity-"+modalId).val());
            var itemPrice = parseFloat($("#itemPrice-"+modalId).val());

            if (itemName != "" && itemQuantity != "" && itemPrice != "") {
                var total = parseFloat(itemQuantity * itemPrice).toFixed(2);

                var markup = "<tr class='valueRow-"+modalId+"'><td> <input type='hidden' name='idColumn' class='form-control idColumn' value='"+itemId+"'> <input type='text' name='nameColumn' class='form-control nameColumn' value='" + itemName + "'> </td><td> <input type='text' name='quantityColumn' class='form-control quantityColumn' style='text-align:center' value='" + itemQuantity + "'></td><td> <input type='text' name='priceColumn' class='form-control priceColumn' style='text-align:center' value='" + itemPrice + "'></td><td><input type='text' class='form-control totalColumn' name='totalColumn' style='text-align:center' value=' " + total + "'readonly></td><td><button class='btn btn-danger btn-x removeButton' style='padding: 2px 10px;'>Remove</button></td></tr>";

                /*var markup = "<tr class='valueRow'><td class='nameColumn'>" + itemName + "</td><td class='quantityColumn'>" + itemQuantity + "</td><td class='priceColumn'>" + itemPrice + "</td><td class='totalColumn'>" + total + "</td><td><button class='btn btn-danger btn-x removeButton' style='padding: 2px 10px;'>Remove</button></td></tr>";*/

                $("#headerRow-"+modalId).after(markup);

                $("#itemName-"+modalId).val("");
                $("#itemQuantity-"+modalId).val("");
                $("#itemPrice-"+modalId).val("");
                $("#productTotalPriceLabel-"+modalId).html("");

                calculateTotal();

            }

        });

            /*End Add to Cart*/


            /*Remove Button*/
        $(document).on('click', '.valueRow-'+modalId+' button.removeButton', function () {


            $(this).closest('tr').remove();
            calculateTotal();

                  return false;
        });



            /*Update product*/
            $("#addToList-"+modalId).on('click',function () {
                var productId = $("#product-"+modalId+" option:selected").val();
                var productCode = $("#product-"+modalId+" option:selected").html();


                if(productId!=""){
                    $("#productSelected-"+modalId).val(productCode);

                    /*Generate BillNo and Branch Name*/
                    var csrf =  "{{csrf_token()}}";

                    var lastAdChargeId = "{{$lastAdChargeId}}";

                    $.ajax({
                        type: 'post',
                        url: './famsAdChargeGetBranch',
                        data: {productCode:productCode, _token: csrf},
                        dataType: 'json',
                        success: function( data ){

                           $("#editModalBillNo-"+modalId).val('EX'+data['branchCode']+lastAdChargeId);
                            $("#editModalBranchName-"+modalId).val(data['branchName']);

                        }

                    });

                }
            });
            /*End Update product*/

            $("#itemPrice-"+modalId).on('input', function() {
                
                this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

                var itemPrice = parseFloat(this.value);
                var itemQuantity = parseFloat($("#itemQuantity-"+modalId).val());
                if (this.value!="" && itemQuantity!="") {
                    var total = itemPrice * itemQuantity;
                    $("#itemTotal-"+modalId).val(total);
                }
            });

            $("#itemQuantity-"+modalId).on('input', function() {
                
                this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

                var itemQuantity = parseFloat(this.value);
                var itemPrice = parseFloat($("#itemPrice-"+modalId).val());
                if (this.value!="" && itemQuantity!="") {
                    var total = itemPrice * itemQuantity;
                    $("#itemTotal-"+modalId).val(total);
                }
            });



            /*Update the data*/
            
            $("#save-"+modalId).click(function () {

                var billNo = $("#editModalBillNo-"+modalId).val();
                var quantity = $("#editModalTotalQuantity-"+modalId).val();
                var amount = $("#editModalTotalAmount-"+modalId).val();
                var purchaseDate = $("#editModalPurchaseDate-"+modalId).val();
                var csrf =  "{{csrf_token()}}";

                var fieldproductId = new Array();
                var fieldproductName = new Array();
                var fieldproductQuantity = new Array();
                var fieldproductPrice = new Array();
                var fieldproductTotalPrice = new Array();
                $("tr.valueRow-"+modalId).each(function(){
                    fieldproductId.push($(this).find('.idColumn').val());
                    fieldproductName.push($(this).find('.nameColumn').val());
                    fieldproductQuantity.push($(this).find('.quantityColumn').val());
                    fieldproductPrice.push($(this).find('.priceColumn').val());
                    fieldproductTotalPrice.push($(this).find('.totalColumn').val());

                    });   

                    if(fieldproductName.length<=0){                    
                        $("#tableError-"+modalId).show();
                        return;
                       }                   


                    $.ajax({
                        type: 'post',
                        url: './editFamsAdditionalCharge',
                        data: {billNo: billNo, quantity: quantity, amount: amount,purchaseDate: purchaseDate, _token: csrf, fieldproductId: fieldproductId, fieldproductName: fieldproductName,fieldproductQuantity: fieldproductQuantity,fieldproductPrice: fieldproductPrice,fieldproductTotalPrice: fieldproductTotalPrice},
                        
                         dataType: 'json',
                        success: function( data ) {
                                                        
                            window.location.href = "famsAdditionalCharge";
                            return false;
                        }
                        , 
                        error: function( _response ){
                        // Handle error
                        alert('_response.errors');
                        }
                    }); 



            });



            /*End Update the data*/

        });
    </script>
    {{--End Edit Item Table Data--}}


    {{-- Filtering --}}
    <script type="text/javascript">
        $(document).ready(function () {

            var modalId = "{{$adCharge->id}}";

            /* Change Branch*/

            $("#branchId-"+modalId).change(function(){

                var branchId = $(this).val();
                var productGroupId =  $("#productGroupId-"+modalId).val();
                var productSubCategoryId = $("#productSubCategoryId-"+modalId).val();

                var csrf =  "{{csrf_token()}}";

                $.ajax({
                    type: 'post',
                    url: './famsOnChangeBranch',
                    data: {branchId:branchId,productGroupId: productGroupId,productSubCategoryId: productSubCategoryId,_token: csrf},
                    dataType: 'json',
                    success: function( _response ){


                        $("#product-"+modalId).empty();
                        $("#product-"+modalId).prepend('<option selected="selected" value="">Select Product</option>');


                        $.each(_response, function (key, value) {
                            {

                                if (key == "productList") {
                                    $.each(value, function (key1,value1) {

                                        $('#product-'+modalId).append("<option value='"+ value1+"'>"+key1+"</option>");

                                    });
                                }
                            }
                        });

                    },
                    error: function(_response){
                        alert("error");
                    }

                });/*End Ajax*/

            }); /*End Change Branch*/


            /*Change Product Group*/

            $("#productGroupId-"+modalId).change(function(){

                var branchId = $("#branchId-"+modalId).val();
                var productGroupId = $(this).val();
                var productSubCategoryId = $("#productSubCategoryId-"+modalId).val();


                var csrf = "<?php echo csrf_token(); ?>";

                $.ajax({
                    type: 'post',
                    url: './famsOnChangeGroup',
                    data: {branchId: branchId,productGroupId:productGroupId,productSubCategoryId: productSubCategoryId,_token: csrf},
                    dataType: 'json',
                    success: function( _response ){


                        $("#productSubCategoryId-"+modalId).empty();
                        $("#productSubCategoryId-"+modalId).prepend('<option selected="selected" value="">Please Select</option>');


                        $("#product-"+modalId).empty();
                        $("#product-"+modalId).prepend('<option selected="selected" value="">Please Select</option>');

                        $.each(_response, function (key, value) {
                            {


                                if (key == "productSubCategoryList") {
                                    $.each(value, function (key1,value1) {

                                        $('#productSubCategoryId-'+modalId).append("<option value='"+ value1+"'>"+key1+"</option>");
                                    });
                                }


                                if (key == "productList") {
                                    $.each(value, function (key1,value1) {

                                        $('#product-'+modalId).append("<option value='"+ value1+"'>"+key1+"</option>");

                                    });
                                }
                            }
                        });

                    },
                    error: function(_response){
                        alert("error");
                    }

                });/*End Ajax*/

            }); /*End Change Product Group*/



            //Change Sub Category
            $("#productSubCategoryId-"+modalId).change(function(){
                var branchId = $("#branchId-"+modalId).val();
                var productGroupId =  $("#productGroupId-"+modalId).val();
                var productSubCategoryId = $(this).val();


                var csrf = "<?php echo csrf_token(); ?>";

                $.ajax({
                    type: 'post',
                    url: './famsOnChangeBranch',
                    data: {branchId: branchId, productGroupId:productGroupId, productSubCategoryId: productSubCategoryId,_token: csrf},
                    dataType: 'json',
                    success: function( _response ){


                        $("#product-"+modalId).empty();
                        $("#product-"+modalId).prepend('<option selected="selected" value="">Select Product</option>');

                        $.each(_response, function (key, value) {
                            {


                                if (key == "productList") {
                                    $.each(value, function (key1,value1) {

                                        $('#product-'+modalId).append("<option value='"+ value1+"'>"+key1+"</option>");

                                    });
                                }
                            }
                        });

                    },
                    error: function(_response){
                        alert("error");
                    }

                });/*End Ajax*/


            }); /*End Change Sub Category*/
        });

    </script>
    {{--End Filtering--}}


    @endforeach





@include('dataTableScript');
<script type="text/javascript">
  $(document).ready(function(){       
          $("#details").attr("class", "");
          $("#action").attr("class", "");
          $("#filterBranch").attr("class", "");
  });
  
</script>

<script type="text/javascript">
	$(document).ready(function(){
		/*Change Price*/
		$(".selectProduct").change(function(){
      var modalId = $(this).attr('modalId');
      //alert(modalId);
			var productId = $("#product-"+modalId+" option:selected").val();
			var quantity = $(".quantity").val();
			var csrf = "<?php echo csrf_token(); ?>";

      //alert(productId);

			$.ajax({
				type:'post',
				url: './issueGetProductPrice',
				data: {productId: productId, _token: csrf},
				dataType: 'json',
				success: function( price ) {
          $("#price-"+modalId).html(price);
					
          if (price==""){$("#totalPrice-"+modalId).html("");}
          else{$("#totalPrice-"+modalId).html(price*quantity);}
					/*if (price=="") {$(".totalPrice").html("");}
					else{$(".totalPrice").html(price*quantity);}*/
				},
				error: function( _response ){
				}
			});
		});

		/*Change Total Price*/
		$(".quantity").on('input',function(){
			this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

      var modalId = $(this).attr('modalId');

			var productPrice = $("#price-"+modalId).html();
			$("#totalPrice-"+modalId).html(productPrice*this.value);
			
		});

        /*Change Price*/
        $(".editModalQuantity").on('input',function(){
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

            var productPrice = parseInt($(this).closest('tr').find(".priceColumn").html());
            $(this).closest('tr').find(".totalPriceColumn").html(productPrice*this.value);

        });



	});
</script>




 <script type="text/javascript">

  $(document).ready(function(){
    $(".edit-modal").find(".modal-dialog").css("width","80%");
  });
</script>

<script type="text/javascript">
 /* 
  $(document).ready(function(){
    $("#editModalTable tr.valueRow").each(function(){

      var a = $(this).find('.editModalProductId').val();
      $("#product > option[value=="+a+"]").hide();
      
      }); 
  });
  */

</script>

<script type="text/javascript">
  $(document).ready(function(){
    $(".editModalLink").on('click',function(){
      var modalId = this.id;

      $("#editModalTable-"+modalId+" tr.valueRow").each(function(){
        var productId = $(this).find('.editModalProductId').val();
        $("#edit-modal-"+modalId+" .selectProduct option[value='"+productId+"']").hide();
      });

      
    });
  });
</script>

<script type="text/javascript">


    $(document).ready(function(){
        $(".edit-modal,.view-modal").find(".modal-dialog").css("width","80%");
        /*$(".view-modal").find(".modal-dialog").css("width","80%");*/


    });
</script>




@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
