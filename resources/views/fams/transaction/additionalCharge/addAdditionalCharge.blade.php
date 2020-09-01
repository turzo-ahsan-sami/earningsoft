@extends('layouts/fams_layout')
@section('title', '| Additional Charge')
@section('content')


<div class="col-sm-1 col-lg-1"></div>
<div class="col-sm-10 col-lg-10">
<div class="add-data-form">    
    <div class="row">
    <div class="col-md-12">
        <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('famsAdditionalCharge/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Additional Charge List</a>
                    </div>
            
        </div>
        
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">Additional Charge</div>
                </div>
                <div class="panel-body">
                    {!! Form::open(array('url' => '', 'enctype' => 'multipart/form-data', 'role' => 'form',  'class'=>'form-horizontal form-groups')) !!}
                        
                         <div class="form-group">
                            {!! Form::label('billNo', 'Bill No:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                                {!! Form::text('billNo', $value = null, ['class' => 'form-control', 'id' => 'billNo', 'readonly','autocomplete'=>'off']) !!}
                            </div>

                             {!! Form::label('totalQuantity', 'Quantity:', ['class' => 'col-sm-2 control-label']) !!}
                             <div class="col-sm-4">
                                 {!! Form::text('totalQuantity', $value = null, ['class' => 'form-control', 'id' => 'totalQuantity','readonly','autocomplete'=>'off']) !!}
                             </div>


                        </div>

                        <div class="form-group">
                            {!! Form::label('branchName', 'Branch Name:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                                {!! Form::text('branchName', $value = null, ['class' => 'form-control','readonly','id'=>'branchName','autocomplete'=>'off']) !!}
                            </div>
                            {!! Form::label('totalAmount', 'Amount:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                                {!! Form::text('totalAmount', $value = null, ['class' => 'form-control', 'id' => 'totalAmount','readonly','autocomplete'=>'off']) !!}
                            </div>

                        </div>

                        <div class="form-group">
                            {!! Form::label('date', 'Date:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                                {!! Form::text('date', null, ['class' => 'form-control','readonly','id'=>'date','autocomplete'=>'off','style'=>'cursor:pointer']) !!}
                                <p><span id="dateE" style="display: none;color: red">*Required</span></p>
                            </div>
                        </div>
                        
                    {!! Form::close()  !!}
                    <br>
                    
                    <div class="col-sm-12 text-right" style="padding-right: 0px">
                    <button id="save" class="btn btn-info">Save</button>
                    <a href="{{url('famsAdditionalCharge/')}}" class="btn btn-danger closeBtn">Close</a>
                    </div>
                  </div>
             </div>
            <div class="panel panel-default panel-border" style="padding-left: 0px;padding-right: 0px;">
                <div class="panel-body">

                    {{-- Filtering Inputs --}}
                    <div class="col-md-12" style="padding-right: 0px;">

                        <div class="form-group col-sm-4" style="padding-right: 2%;">

                            {!! Form::label('branchId', 'Branch:', ['class' => 'control-label']) !!}

                            <select  id="branchId" class="form-control input-sm"  @if($userBranchId!=1) {{"disabled=disabaled"}} @endif>
                                <option value="" selected="selected">Please Select</option>
                                @foreach($branches as $branch)
                                    <option value={{$branch->id}} @if($userBranchId==$branch->id) {{"selected=selected"}} @endif>{{str_pad($branch->branchCode,3,"0",STR_PAD_LEFT).'-'.$branch->name}}</option>
                                @endforeach

                            </select>
                        </div>

                        <div class="form-group col-sm-4" style="padding-right: 2%;">
                            {!! Form::label('productTypeId', 'Product Type:', ['class' => 'control-label']) !!}

                            <select  id="productTypeId" class="form-control input-sm">
                                <option value=""  selected="selected">Please Select</option>
                                @foreach($productTypes as $productType)
                                    <option value={{$productType->id}}>{{str_pad($productType->productTypeCode,3,'0',STR_PAD_LEFT).'-'.$productType->name}}</option>
                                @endforeach

                            </select>
                        </div>



                        <div class="form-group col-sm-4" style="padding-right: 2%;">

                            {!! Form::label('productNameId', 'Product Name:', ['class' => 'control-label',]) !!}

                            <select  id="productNameId" class="form-control input-sm" style="padding-right: 0px;">
                                <option value="" selected="selected">Please Select</option>
                                @foreach($productNames as $productName)
                                    <option value={{$productName->id}}>{{str_pad($productName->productNameCode,3,'0',STR_PAD_LEFT).'-'.$productName->name}}</option>
                                @endforeach
                            </select>

                        </div>

                    </div>
                    {{-- End Filtering Input --}}
                    <div class="col-md-12" style="padding-right: 0px;">
                        <div class="col-md-4" style="padding-right: 2%;">
                            {!! Form::label('product', 'Product:', ['class' => 'control-label']) !!}
                            <select id="product" class="form-control">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{$product->id}}" >{{$prefix.$product->productCode}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4" style="text-align:center">
                            {!! Form::label('addProduct', 'Add to Selected List', ['class' => 'control-label']) !!}
                            <div >
                                {!! Form::button('<i class="fa fa-chevron-right" aria-hidden="true"></i><i class="fa fa-chevron-right" aria-hidden="true"></i>',['class'=>'btn btn-primary btn-xs','id'=>'addToList']) !!}
                            </div>

                        </div>
                        <div class="col-md-4" style="padding-right: 2%;">
                            {!! Form::label('addProduct', 'Selected Product', ['class' => 'control-label']) !!}
                            {!! Form::text('productSelected', $value = null, ['productId'=>null,'class' => 'form-control', 'id' => 'productSelected', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            <p><span id="productSelectede" style="display: none;color: red">*Required</span></p>
                        </div>
                    </div>

                </div>
                </div>
        </div>
    </div>
</div>


<!-- Table to Add Product -->

<div class="panel panel-default panel-border" data-collapsed="0" style="padding-left: 0px;padding-right: 0px;">
        <div class="panel-body">


<div class="row" style="padding-bottom: 1%;">
    <div class="col-md-12" style="padding-left:5%;">
        <div class="col-md-6" style="padding-right:2%;">
            <div class="form-horizontal form-groups">
                <div class="form-group">
                    {!! Form::label('addAdditionalProduct', 'Add Additional Product:', ['class' => 'col-sm-4 control-label','style'=>'color: black']) !!}
                    <div class="col-sm-8 control-label" style="padding-left: 0px;">
                    <button type="button" data-toggle="modal" data-target="#addProduct-modal" class="btn btn-primary btn-xs" style="text-align:center;border-radius:0;width:60px;">Add</button>                                                                      
                    </div>
                </div>
            </div>
        </div> 
    </div>
</div>


        <span id="tableError" style="display: none;"><font color="red">*Please add at least one Product</font></span>



                <div class="col-md-12" style="padding-left: 2%; padding-right: 2%;">

                        <table id="addProductTable" class="table table-bordered responsive">
                        
                            <tr>
                                
                                    <th style="text-align:center;">

                                    <select name="itemName" id="itemName" class="form-control">
                                            <option value="">Select Product</option>
                                            @foreach($additionalProducts as $additionalProduct)
                                            <option value="{{$additionalProduct->id}}">{{$additionalProduct->name}}</option>
                                            @endforeach
                                            
                                        </select>

                                        {{-- {!! Form::text('additionalItem', $value = null, ['class' => 'form-control', 'id' => 'itemName','autocomplete'=>'off','placeholder'=>'Additional Item Name']) !!} --}}

                                    </th>
                                    <th>
                                    <input type="text" id="itemQuantity" name="itemQuantity" placeholder="Quantiy" style="text-align:center;">
                                    </th>
                                    
                                    <th>
                                    <input type="text" id="itemPrice" name="itemPrice" placeholder="Price" style="text-align:center;">
                                    </th> 
                                    <th><input type="text" id="productTotalPriceLabel" placeholder="Total" style="text-align:center;" readonly></th>
                                    <th><button id="addToCart" class='btn btn-primary btn-xs' style='text-align:center;border-radius:0;width:80px;'>Add to Cart</button></th>
                                
                                
                            </tr>
                            <tbody>
                                <tr id="headerRow">
                                    <td>Product Name</td>
                                    <td>Quantity</td>
                                    <td>Price</td>
                                    <td>Total</td>
                                    <td>Action</td>
                                </tr>

                                
                                <tr>
                                    <td>Total Quantity</td>
                                    <td class="quantityTd"><span id="totalQuantitySpan" quantity="0"></span></td>
                                    <td>Total Amount</td>
                                    <td class="amountTd"><span id="totalAmountSpan" amount="0"></span></td>
                                    <td></td>
                                </tr>                                
                                   
                            </tbody>
                        </table>
                        
                </div>
        </div>
</div>


<div class="footerTitle" style="border-top:1px solid white"></div>


<div class="col-sm-1 col-lg-1" ></div>



{{-- Add Product Modal --}}
    <div id="addProduct-modal" class="modal fade addProduct-modal" style="margin-top:3%">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Add Additional Product</h4>
                </div>
                <div class="modal-body">

                   <div class="row" style="padding-bottom: 20px;">

                            <div class="col-md-12" style="padding-left:0px;">

                                <div class="col-md-12" style="padding-right:2%;">
                                    <div class="form-horizontal form-groups">

                                        <div class="form-group">
                                        {!! Form::label('additionalProductCategory', 'Category:', ['class' => 'col-sm-4 control-label','style'=>'color: black']) !!}
                                        <div class="col-sm-8">
                                        <select name="categoryId" id="categoryId" class="form-control">
                                                    <option value="">Select Category</option>
                                                    @foreach($productCategories as $category)
                                                    <option value={{$category->id}}>{{$category->name}}</option>
                                                    @endforeach
                                                </select>
                                                <p><span id="additionalProductCategorye"></span></p>
                                           
                                        </div>
                                        </div>

                                        <div class="form-group">
                                        {!! Form::label('additionalProductName', 'Additional Product Name:', ['class' => 'col-sm-4 control-label','style'=>'color: black']) !!}
                                        <div class="col-sm-8">
                                           {!! Form::text('additionalProductName',"",['class'=>'form-control','id'=>'additionalProductName','autocomplete'=>'off']) !!}
                                           <p><span id="additionalProductNamee"></span></p>

                                        </div>
                                        </div>

                                       

                                        </div>
                                </div>
                            </div>
                      </div>


                      <div class="modal-footer">
                        <button class="btn actionBtn glyphicon glyphicon-check btn-success edit" id="addAdditionalProductButton" type="button"><span> Submit</span></button>
                        <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"><span id=""> Close</span></button>
                    </div>


                </div>
            </div>
        </div>
    </div>

    {{-- End Add Product Modal --}}

    


<!-- Build the Product Table -->
<script type="text/javascript">

    $(document).ready(function() {
        $("#addToCart").click(function () {
            $("#tableError").hide();

            var itemId = $("#itemName  option:selected").val();
            var itemName = $("#itemName  option:selected").html();
            var itemQuantity = parseFloat($("#itemQuantity").val());
            var itemPrice = parseFloat($("#itemPrice").val());

            if (itemName != "" && itemQuantity >0 && itemPrice >0) {
                var total = parseFloat(itemQuantity * itemPrice).toFixed(2);

                var markup = "<tr class='valueRow'><td> <input type='hidden' name='idColumn' class='form-control idColumn' value='"+itemId+"'> <input type='text' name='nameColumn' class='form-control nameColumn' value='" + itemName + "'> </td><td> <input type='text' name='quantityColumn' class='form-control quantityColumn' style='text-align:center' value='" + itemQuantity + "'></td><td> <input type='text' name='priceColumn' class='form-control priceColumn' style='text-align:center' value='" + itemPrice + "'></td><td><input type='text' class='form-control totalColumn' name='totalColumn' style='text-align:center' value=' " + total + "'readonly></td><td><button class='btn btn-danger btn-x removeButton' style='padding: 2px 10px;'>Remove</button></td></tr>";

               /* var markup = "<tr class='valueRow'><td class='nameColumn'>" + itemName + "</td><td class='quantityColumn'>" + itemQuantity + "</td><td class='priceColumn'>" + itemPrice + "</td><td class='totalColumn'>" + total + "</td><td><button class='btn btn-danger btn-x removeButton' style='padding: 2px 10px;'>Remove</button></td></tr>";*/

                $("#headerRow").after(markup);

                $("#itemName").val("");
                $("#itemQuantity").val("");
                $("#itemPrice").val("");
                $("#productTotalPriceLabel").val("");

                var previousQuantity = parseFloat($("#totalQuantitySpan").attr("quantity"));
                var previousAmount = parseFloat($("#totalAmountSpan").attr("amount"));

                var totalQuantity = (parseFloat(previousQuantity)+parseFloat(itemQuantity)).toFixed(2);
                var totalAmount = (parseFloat(previousAmount)+parseFloat(total)).toFixed(2);

                $("#totalQuantitySpan").attr("quantity",totalQuantity);
                $("#totalAmountSpan").attr("amount",totalAmount);

                $("#totalQuantitySpan").text(totalQuantity);
                $("#totalAmountSpan").text(totalAmount);

                $("#totalQuantity").val(totalQuantity);
                $("#totalAmount").val(totalAmount);

            }

        });


        /* Remove Item and update data when Remove Button is pressed*/
        $(document).on('click', 'button.removeButton', function () {

            var tdQuantity = parseFloat($(this).closest('tr').find('.quantityColumn').val());
            var tdTotal = parseFloat($(this).closest('tr').find('.totalColumn').val());


            var previousQuantity = parseFloat($("#totalQuantitySpan").attr("quantity"));
            var previousAmount = parseFloat($("#totalAmountSpan").attr("amount"));

            var totalQuantity = parseFloat(previousQuantity) - parseFloat(tdQuantity);
            var totalAmount = parseFloat(previousAmount) - parseFloat(tdTotal);


            $("#totalQuantitySpan").attr("quantity",totalQuantity);
            $("#totalAmountSpan").attr("amount",totalAmount);

            $("#totalQuantitySpan").text(totalQuantity);
            $("#totalAmountSpan").text(totalAmount);

            $("#totalQuantity").val(totalQuantity);
            $("#totalAmount").val(totalAmount);


            $(this).closest('tr').remove();
            return false;
        });
    });
</script>


<script type="text/javascript">
    $(document).ready(function(){
        $("#save").click(function(){

            
            //Get all the vlaues
            var billNo = $("#billNo").val();
            var branchName = $("#branchName").val();
            var totalQuantity = $("#totalQuantity").val();
            var totalAmount = $("#totalAmount").val();
            var productCode = $("#productSelected").val();
            var productId = $("#productSelected").attr('productId');
            var csrf = "{{csrf_token()}}";
            var date = $("#date").val();

            //alert(billNo);

            if(productCode==""){
                $("#productSelectede").show();
            }

            if(date==""){
                $("#dateE").show();
            }           


            //Get Table Data for Issue Details Table
            var fieldproductName = new Array();
            var fieldproductQuantity = new Array();
            var fieldproductPrice = new Array();
            var fieldproductTotalPrice = new Array();
            $("#addProductTable tr.valueRow").each(function(){
                fieldproductName.push(JSON.stringify($(this).find('.nameColumn').val()));
                fieldproductQuantity.push(JSON.stringify($(this).find('.quantityColumn').val()));
                fieldproductPrice.push(JSON.stringify($(this).find('.priceColumn').val()));
                fieldproductTotalPrice.push(JSON.stringify($(this).find('.totalColumn').val()));
                });

                if(fieldproductName.length<=0){
                    $("#tableError").show();
                return;
                 }


            $.ajax({
            type: 'post',
            url: './storeAdditinalCharge',
            data: {billNo: billNo, branchName: branchName,productId:productId,productCode: productCode, totalQuantity: totalQuantity,totalAmount: totalAmount, _token: csrf, fieldproductName: fieldproductName,fieldproductQuantity: fieldproductQuantity,fieldproductPrice: fieldproductPrice,fieldproductTotalPrice: fieldproductTotalPrice,date:date},
            
             dataType: 'json',
            success: function( _response ) {
                window.location.href = "famsAdditionalCharge";
                
                return false;
            },
            error: function( _response ){
            // Handle error
            //alert('_response.errors');
            }
        }); 



        });
    });
</script>

<script type="text/javascript">
$(document).ready(function(){


    $('#itemQuantity').on('input', function() {
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

        var itemPrice = parseFloat($("#itemPrice").val());
        if(itemPrice>0){
            $("#productTotalPriceLabel").val(itemPrice*this.value);
        }

    });

    $('#itemPrice').on('input', function() {
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

        var itemQuantity = parseFloat($("#itemQuantity").val());
        if(itemQuantity>0){
            $("#productTotalPriceLabel").val(itemQuantity*this.value);
        }

    });

    $("#orderNo").on('input',function(){
        $("#orderNoError").hide();
    });
    $("#issueOrderNo").on('input',function(){
        $("#issueOrderNoError").hide();
    });
    $("#branchId").on('change',function(){
        $("#branchError").hide();
    });

    $("#date").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "1998:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                $('#dateE').hide();               
            }
        });

}); /*ready*/
  

</script>



{{-- Filtering --}}
<script type="text/javascript">
    $(document).ready(function () {
        $( "#branchId" ).trigger("change");
         function pad (str, max) {
              str = str.toString();
              return str.length < max ? pad("0" + str, max) : str;
            }

        /* Change Branch*/

        $("#branchId").change(function(){

            var branchId = $(this).val();
            var productTypeId = $("#productTypeId").val();
            var productNameId =  $("#productNameId").val();
            var csrf =  "{{csrf_token()}}";
            var prefix = "{{$prefix}}";

            $.ajax({
                type: 'post',
                url: './famsOnChangeBranch2',
                data: {branchId:branchId,productNameId: productNameId,productTypeId: productTypeId,_token: csrf},
                dataType: 'json',
                success: function( _response ){


                    $("#product").empty();
                    $("#product").prepend('<option selected="selected" value="">Select Product</option>');


                    $.each(_response, function (key, value) {
                        {

                            if (key == "productList") {
                                $.each(value, function (key1,value1) {

                                    $('#product').append("<option value='"+ value1+"'>"+prefix+key1+"</option>");

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


        /* Change Product Type*/
         $("#productTypeId").change(function(){
            var branchId = $("#branchId option:selected").val();
            var productTypeId = $(this).val();
            var prefix = "{{$prefix}}";

            var csrf = "<?php echo csrf_token(); ?>";
            
            $.ajax({
                type: 'post',
                url: './famsOnChangeProductType',
                data: {branchId: branchId, productTypeId:productTypeId,_token: csrf},
                dataType: 'json',
                success: function( _response ){
                    
                    $("#productNameId").empty();
                    $("#productNameId").prepend('<option selected="selected" value="">Please Select</option>');

                    $("#product").empty();
                    $("#product").prepend('<option selected="selected" value="">Select Product</option>');                   

                    $.each(_response, function (key, value) {
                        {
                            
                            if (key == "productNameList") {
                                $.each(value, function (key1, obj) {

                                    $('#productNameId').append("<option value='"+ obj.id+"'>"+pad(obj.productNameCode,3)+'-'+obj.name+"</option>");
                                });
                            }

                            if (key == "productList") {
                                $.each(value, function (key1,value1) {
                                    $('#product').append("<option value='"+ value1+"'>"+prefix+key1+"</option>");
                                });
                            }
                           
                        }
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Product Type*/


        /*Change Product Name*/
        $("#productNameId").change(function(){

            var branchId = $("#branchId").val();
            var productNameId = $(this).val();
            var prefix = "{{$prefix}}";

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsOnChangeName',
                data: {branchId: branchId,productNameId: productNameId,_token: csrf},
                dataType: 'json',
                success: function( _response ){                    


                    $("#product").empty();
                    $("#product").prepend('<option selected="selected" value="">Select Product</option>');

                    $.each(_response, function (key, value) {
                        {


                            if (key == "productList") {
                                $.each(value, function (key1,value1) {

                                    $('#product').append("<option value='"+ value1+"'>"+prefix+key1+"</option>");

                                });
                            }
                        }
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

        }); /*End Change Product Name*/
    });

</script>
    {{--End Filtering--}}

    <script>
        $(document).ready(function () {
            $("#addToList").on('click',function () {
                $("#productSelectede").hide();
                var productId = $("#product option:selected").val();
                var productCode = $("#product option:selected").html();

                if(productId!=""){
                    $("#productSelected").val(productCode);
                    $("#productSelected").attr('productId',productId);

                    /*Generate BillNo and Branch Name*/
                    var csrf =  "{{csrf_token()}}";
                    var prefix = "{{$prefix}}";

                    var lastAdChargeId = "{{$lastAdChargeId}}";

                    $.ajax({
                        type: 'post',
                        url: './famsAdChargeGetBranch',
                        data: {productId:productId, _token: csrf},
                        dataType: 'json',
                        success: function( data ){
                            $("#billNo").val('EX'+data['branchCode']+lastAdChargeId);
                            $("#branchName").val(data['branchName']);
                        }
                    });

                    $.ajax({
                        
                        type: 'post',
                        url: './famsAdChargeGetProducts',
                        data: {productId:productId, _token: csrf},
                        dataType: 'json',
                        success: function( _response ){
                            //alert("success");
                            $("#itemName").empty();
                            $("#itemName").prepend('<option selected="selected" value="">Please Select</option>');
                            $.each(_response, function (key, value) {
                                if (key == "productList") {
                                $.each(value, function (key1,value1) {
                                    $('#itemName').append("<option value='"+ value1+"'>"+key1+"</option>");
                                });
                            }                                             
                            });
                            
                        },
                        error: function(){
                            alert("Error");
                        }
                    });
                }
            });



            $("#addAdditionalProductButton").click(function() {

                var name = $("#additionalProductName").val();
                var categoryId = $("#categoryId").val();
                var csrf = "{{csrf_token()}}";
                var prefix = "{{$prefix}}";
                //debugger;
                //alert(name);
                var productCode = $("#productSelected").val();
                var productId = $("#productSelected").attr('productId');
                
                    $.ajax({
                        type: 'post',
                        url: './ajaxStoreFamsAdditionalProduct',
                        data: {name:name,categoryId:categoryId, _token: csrf},
                        dataType: 'json',
                        success: function( _response ){
                            if(_response.errors) {
                                if (_response.errors['name']) {

                                    $("#additionalProductNamee").empty();
                                     $('#additionalProductNamee').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');

                                }
                                if (_response.errors['categoryId']) {

                                    $("#additionalProductCategorye").empty();
                                     $('#additionalProductCategorye').append('<span class="errormsg" style="color:red;">'+_response.errors.categoryId+'</span>');

                                }
                            }
                            else{
                                //alert("Product Inserted Successfully");
                                //var lastValue =  parseInt($('#itemName option:last').val())+1;

                                 $.ajax({
                                        type: 'post',
                                        url: './famsAdChargeGetAllProducts',
                                        data: {productId:productId, _token: csrf},
                                        dataType: 'json',
                                        success: function( _response ){
                                            //alert("success");
                                            $("#itemName").empty();
                                            $("#itemName").prepend('<option selected="selected" value="">Please Select</option>');
                                            $.each(_response, function (key, value) {
                                                if (key == "productList") {
                                                $.each(value, function (key1,value1) {
                                                    $('#itemName').append("<option value='"+ value1+"'>"+key1+"</option>");
                                                });
                                            }                                             
                                            });
                                            
                                        },
                                        error: function(){
                                            alert("Error");
                                        }
                                    });

                                //$('#itemName').append("<option value='"+ lastValue+"'>"+name+"</option>");
                                $("#addAdditionalProduct").val("");
                                 $('#addProduct-modal').modal('toggle');
                            }
                            
                        },
                        error: function() {
                            alert("Error");
                        }

                    });
            });


            $("#itemPrice").on('input', function() {
                
                this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

                var itemPrice = parseFloat(this.value);
                var itemQuantity = parseFloat($("#itemQuantity").val());
                if (this.value!="" && itemQuantity!="") {
                    var total = itemPrice * itemQuantity;
                    $("#itemTotal").val(total);
                }
            });

            $("#itemQuantity").on('input', function() {
                
                this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

                var itemQuantity = parseFloat(this.value);
                var itemPrice = parseFloat($("#itemPrice").val());
                if (this.value!="" && itemQuantity!="") {
                    var total = itemPrice * itemQuantity;
                    $("#itemTotal").val(total);
                }
            });

           




        });
    </script>




    <script type="text/javascript">
        $(document).ready(function() {


             function calculateTotal(){
                var totalQuantity = parseFloat(0);
                var totalAmount = parseFloat(0);
                $("#addProductTable tr.valueRow").each(function() {
                    totalQuantity = totalQuantity + parseFloat($(this).find('.quantityColumn').val());
                    totalAmount = totalAmount + parseFloat($(this).find('.totalColumn').val());
                });

                $("#totalQuantitySpan").attr('quantity', totalQuantity);
            $("#totalAmountSpan").attr('amount', totalAmount);

            $("#totalQuantitySpan").html(totalQuantity);
            $("#totalAmountSpan").html(totalAmount);

            $("#totalQuantity").val(totalQuantity);
            $("#totalAmount").val(totalAmount);
                
            }



            $(document).on('input', '.quantityColumn', function() {
                this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

                var price = parseFloat(this.value);
                var quantity = parseFloat($(this).closest('tr').find(".priceColumn").val());

                if(quantity!="" && price!=""){
                    var total = quantity * price;
                    $(this).closest('tr').find(".totalColumn").val(total);
                }

                calculateTotal();
            });



            $(document).on('input', '.priceColumn', function() {
                this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

                var price = parseFloat(this.value);
                var quantity = parseFloat($(this).closest('tr').find(".quantityColumn").val());

                if(quantity!="" && price!=""){
                    var total = quantity * price;
                    $(this).closest('tr').find(".totalColumn").val(total);
                }

                calculateTotal();
            });

        });
    </script>



@endsection





