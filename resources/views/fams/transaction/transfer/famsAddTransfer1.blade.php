
@extends('welcome')
@section('title', '| Transfer')
@section('content')
<div class="col-sm-1 col-lg-1"></div>
<div class="col-sm-10 col-lg-10">
<div class="add-data-form">    
    <div class="row">
        <div class="col-md-12">
        <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('famsTransfer/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Transfer List</a>
                    </div>
            
        </div>
    </div>
    <div class="row">
    
        <div class="col-md-12">
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">Transfer</div>
                </div>
                <div class="panel-body">
                    {!! Form::open(array('url' => 'storeTransfer', 'enctype' => 'multipart/form-data', 'role' => 'form',  'class'=>'form-horizontal form-groups')) !!}
                        
                         <div class="form-group">
                            {!! Form::label('transferNo', 'Transfer No:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                                {!! Form::text('transferNo', $taransferNo, ['class' => 'form-control', 'id' => 'transferNo', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>

                            {!! Form::label('branchFrom', 'Branch From:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="branchFrom" id="branchFrom" value={{DB::table('gnr_branch')->where('id',$branchId)->value('name')}} value2={{DB::table('gnr_branch')->where('id',$branchId)->value('id')}} autocomplete="off" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('orderNo', 'Order No:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                                {!! Form::text('orderNo', $value = null, ['class' => 'form-control', 'id' => 'orderNo', 'type' => 'text','autocomplete'=>'off']) !!}
                                <span id="orderNoError" style="display: none;"><font color="red">*Please Fill</font></span>
                            </div>

                            {!! Form::label('branchTo', 'Branch To:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                            <select class ="form-control" id = "branchTo" autocomplete="off">
                            <option value="0">Select Branch</option>
                                @foreach($branches as $branch)
                                <option value="{{$branch->id}}">{{$branch->name}}</option>
                                @endforeach
                            </select>
                            <span id="branchToError" style="display: none;"><font color="red">*Please Select a Branch</font></span>
                                
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('transferOrderNo', 'Transfer Order No:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                                {!! Form::text('transferOrderNo', $value = null, ['class' => 'form-control', 'id' => 'transferOrderNo', 'type' => 'text','autocomplete'=>'off']) !!}
                                <span id="transferOrderNoError" style="display: none;"><font color="red">*Please Fill</font></span>
                            </div>

                            {!! Form::label('totalTransferQuantity', 'Transfer Quantity:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                                {!! Form::text('totalTransferQuantity', $value = null, ['class' => 'form-control', 'id' => 'totalTransferQuantity', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>
                        </div>


                        <div class="form-group" style='display:none;'>
                            {!! Form::label('totalTransferAmount', 'Transfer Amount:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-5">
                                {!! Form::text('totalTransferAmount', $value = null, ['class' => 'form-control', 'id' => 'totalTransferAmount', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>
                        </div>                  

                    {!! Form::close()  !!}
                    <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                    <div class="col-sm-12 text-right" style="padding-right: 0px">
                    <button id="save" class="btn btn-info">Save</button>
                    <a href="{{url('famsTransfer/')}}" class="btn btn-danger closeBtn">Close</a>
                    </div>
                  </div>
             </div>
        </div>
    </div>
</div>
<!-- Table to Add Product -->

<div class="panel panel-primary" data-collapsed="0" style="padding-left: 0px;padding-right: 0px;">
        <div class="panel-body">
        <span id="tableError" style="display: none;"><font color="red">*Please add at least one Product</font></span>
            {{-- Filtering Inputs --}}
                <div class="col-md-12" style="padding-right: 0px;">
                          <div class="form-group col-sm-3" style="padding-right: 2%;">
                            {!! Form::label('productGroupId', 'Group:', ['class' => 'control-label']) !!}
                            
                                     <select  id="productGroupId" class="form-control input-sm">
                                         <option value="" selected="selected">Please Select</option>
                                         @foreach($productGroups as $productGroup)
                                            <option value={{$productGroup->id}}>{{$productGroup->name}}</option>
                                         @endforeach
                                     </select>
                              
                           </div>

                           <div class="form-group col-sm-3" style="padding-right: 2%;">
                            {!! Form::label('productCategoryId', 'Category:', ['class' => 'control-label']) !!}
                            
                                     <select  id="productCategoryId" class="form-control input-sm">
                                         <option value="" selected="selected">Please Select</option>
                                         @foreach($productCategories as $productCategory)
                                            <option value={{$productCategory->id}}>{{$productCategory->name}}</option>
                                         @endforeach
                                         
                                     </select>                                
                           </div>

                           <div class="form-group col-sm-3" style="padding-right: 2%;">
                            {!! Form::label('productSubCategoryId', 'Sub Category:', ['class' => 'control-label']) !!}
                            
                                     <select  id="productSubCategoryId" class="form-control input-sm">
                                         <option value=""  selected="selected">Please Select</option>
                                         @foreach($productSubCategories as $productSubCategory)
                                            <option value={{$productSubCategory->id}}>{{$productSubCategory->name}}</option>
                                         @endforeach
                                         
                                     </select>                                
                           </div>
            
                            <div class="form-group col-sm-3" style="padding-right: 2%;">
                            {!! Form::label('productBrandId', 'Brand:', ['class' => 'control-label']) !!}
                            
                                     <select  id="productBrandId" class="form-control input-sm">
                                         <option value="" selected="selected">Please Select</option>
                                         @foreach($productBrands as $productBrand)
                                            <option value={{$productBrand->id}}>{{$productBrand->name}}</option>
                                         @endforeach
                                         
                                     </select>                                
                           </div>                  
                           
                                                      
                        </div>
                        {{-- End Filtering Input --}}


                        
                <div class="col-md-12" style="padding-left: 2%; padding-right: 2%;">
                        <table id="addProductTable" class="table table-bordered responsive">
                        
                        {{-- <thead> --}}
                                
                           {{--  </thead> --}}

                           <tr >
                                
                                    <th style="text-align:center;"><select id ="product" class="form-control">
                                        <option value="">Select Product</option>
                                            @foreach($products as $product)
                                            <option value="{{$product->id}}" price="{{$product->costPrice}}">{{$product->name}}</option>
                                            @endforeach
                                        </select>
                                    </th>
                                    <th>
                                    <input type="text" id="productQuantity" name="productQuantity" placeholder="Product Quantiy" style="text-align:center;">
                                    </th>

                                    <th><button id="addToCart" class='btn btn-primary btn-xs' style='text-align:center;border-radius:0;width:80px;'>Add to Cart</button></th>
                                    
                                    <th style="display:none;">
                                    <input type="hidden" id="productPrice" name="productPrice" placeholder="Product Price">
                                    
                                    </th> 
                                    <th style="display:none;"></th>                                    
                                
                                
                            </tr>
                           
                            <tbody>

                             

                            <tr id="headerRow">
                                    <td >Item Name</td>
                                    <td >Qty</td>
                                    <td >Action</td>
                                    <td class=" hidden">Price</td>
                                    <td class=" hidden">Total</td>
                                </tr>
                                

                                
                                <tr>
                                    <td>Total Quantity</td>
                                    <td class="quantityTd"><input type="hidden" id="totalQuantityInput" name="totalQuantityInput" value="0" autocomplete="off"><span id="totalQuantityColumn"></span></td>
                                    <td></td>
                                    <td style="display:none;" class="amountTd"><input type="hidden" id="totalAmountInput" name="totalAmountInput" value="0" autocomplete="off"><span id="totalAmountColumn"></span></td>
                                    <td style="display:none;"></td>
                                </tr>                                
                                   
                            </tbody>
                        </table>
                        
                </div>
                
        </div>


</div>
<div class="footerTitle" style="border-top:1px solid white"></div>


<div class="col-sm-1 col-lg-1" ></div>




<!-- Build the Product Table -->
<script type="text/javascript">

    $(document).ready(function(){
        $("#addToCart").click(function(){
            $("#tableError").hide();     

            var productId = $("#product option:selected").val();
            var productName = $("#product option:selected").html();
            var ProductQuantity = parseFloat($("#productQuantity").val());            
            var ProductPrice = parseFloat($("#product option:selected").attr('price'));

            var grandTotalQuantity = $("#totalQuantityInput").val();
            var grandTotalPrice = $("#totalAmountInput").val();

            

            if(productId>0 && ProductQuantity>0){
                
    
                grandTotalQuantity = parseFloat(grandTotalQuantity) + parseFloat(ProductQuantity);
                $("#totalQuantityInput").val(grandTotalQuantity);
                

                var total = parseFloat(ProductQuantity) * parseFloat(ProductPrice);
                grandTotalPrice = parseFloat(grandTotalPrice) + total;
                $("#totalAmountInput").val(grandTotalPrice);

                var flag = true;

                if ($("#addProductTable tr.valueRow").length > 0) {                  

                  $("#addProductTable tr.valueRow").each(function(){
                    if(parseInt($(this).find('.idColumnInput').val()) == productId){                  

                    var previousQuantity =  parseInt($(this).find('.quantityColumn').html());
                    var previousTotal =  parseInt($(this).find('.totalColumn').html());
                      
                      $(this).find('.quantityColumn').html("");
                      $(this).find('.quantityColumn').html(previousQuantity+ProductQuantity);
                      $(this).find('.totalColumn').html("");
                      $(this).find('.totalColumn').html(previousTotal+total);

                      flag = false;
                      
                    }

                });   

                }

                if(flag==true){

                  var markup = "<tr class='valueRow'><td class='nameColumn'><input type='hidden' class='idColumnInput' value='"+productId+"'><input type='hidden' class='nameColumnInput' value='"+productName+"'>"+productName +"</td><td class='quantityColumn'>" + ProductQuantity + "</td><td><button class='btn btn-danger btn-xs removeButton' style='width:80px'>Remove</button></td><td style='display:none' class='priceColumn'>" + ProductPrice + "</td><td style='display:none' class='totalColumn'>"+total+"</td></tr>";

                $("#headerRow").after(markup); 

                }
                

                               

                $("#totalQuantityColumn").text(grandTotalQuantity);
                $("#totalAmountColumn").text(grandTotalPrice);

                $("#product").val("");
                //$("#product option[value='"+productId+"']").hide();
                $("#productQuantity").val(null);
                $("#productPrice").val(null);

                $("#totalTransferQuantity").text(grandTotalQuantity);
                $("#totalTransferQuantity").val(grandTotalQuantity);
                


                $("#totalTransferAmount").text(grandTotalPrice);
                $("#totalTransferAmount").val(grandTotalPrice);

            }

        });

             $(document).on('click', 'button.removeButton', function () {
                  
                  var tdQuantity = parseFloat($(this).closest('tr').find('.quantityColumn').text());
                  //var tdAmount = parseFloat($(this).closest('tr').find('.priceColumn').text());
                  var tdTotal = parseFloat($(this).closest('tr').find('.totalColumn').text());
                  
                  $("#totalQuantityColumn").text(parseFloat($("#totalQuantityColumn").text())-tdQuantity);
                  $("#totalAmountColumn").text(parseFloat($("#totalAmountColumn").text())-tdTotal);
                  
                  $("#totalQuantityInput").val(parseFloat($("#totalQuantityInput").val())-tdQuantity);
                  $("#totalAmountInput").val(parseFloat($("#totalAmountInput").val())-tdTotal);

                  $("#totalTransferQuantity").text($("#totalQuantityInput").val());
                  $("#totalTransferQuantity").val($("#totalQuantityInput").val());

                  $("#totalTransferAmount").text($("#totalAmountInput").val());
                  $("#totalTransferAmount").val($("#totalAmountInput").val());

                  var productId = $(this).closest('tr').find(".idColumnInput").val();

                  $("#product option[value='"+productId+"']").show();

                  
                  $(this).closest('tr').remove();
                  return false;
              });
    }); 
</script>


<script type="text/javascript">
    $(document).ready(function(){
        $("#save").click(function(){
            

            
            //Get all the vlaues
            var transferNo = $("#transferNo").val();
            var orderNo = $("#orderNo").val();
            var transferOrderNo = $("#transferOrderNo").val();
            var branchFrom = $("#branchFrom").attr('value2');
            var branchTo = $("#branchTo option:selected").val();
            var totalTransferQuantity = $("#totalTransferQuantity").val();
            var totalTransferAmount = $("#totalTransferAmount").val();
            var csrf = "<?php echo csrf_token(); ?>";

             

            //Get Table Data for Issue Details Table
            var fieldproductId = new Array();
            var fieldproductName = new Array();
            var fieldproductQuantity = new Array();
            var fieldproductPrice = new Array();
            var fieldproductTotalPrice = new Array();
            $("#addProductTable tr.valueRow").each(function(){
                fieldproductId.push(JSON.stringify($(this).find('.idColumnInput').val()));
                fieldproductName.push(JSON.stringify($(this).find('.nameColumnInput').val()));
                fieldproductQuantity.push(JSON.stringify($(this).find('.quantityColumn').html()));
                fieldproductPrice.push(JSON.stringify($(this).find('.priceColumn').html()));
                fieldproductTotalPrice.push(JSON.stringify($(this).find('.totalColumn').html()));

                });    


                if(orderNo==""){
                    $("#orderNoError").show();
                return;
            }
            if(transferOrderNo==""){
                    $("#transferOrderNoError").show();
                return;
            }
            if(branchTo==0){
                
                $("#branchToError").show();
                return;
            }
                if(fieldproductId.length<=0){                    
                    $("#tableError").show();
                return;
                 }      
            
            // alert(transferNo);
            // alert(orderNo);
            // alert(transferOrderNo);
            // alert(totalTransferQuantity);
            // alert(totalTransferAmount);
            // alert(csrf);
            // alert(fieldproductName);
            // alert(fieldproductQuantity);
            // alert(fieldproductPrice);

           $.ajax({
            type: 'post',
            url: './famsStoreTransfer',
            data: {transferNo: transferNo, orderNo: orderNo, transferOrderNo: transferOrderNo,branchFrom: branchFrom,branchTo:branchTo, totalTransferQuantity: totalTransferQuantity, totalTransferAmount: totalTransferAmount, _token: csrf, fieldproductId: fieldproductId, fieldproductName: fieldproductName,fieldproductQuantity: fieldproductQuantity,fieldproductPrice: fieldproductPrice},
            
             dataType: 'json',
            success: function( _response ) {
                alert("Success");
                if (_response.errors) {
                if (_response.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
                return false;
            }
            }}, 
            error: function( _response){
            // Handle error
            alert('Error');            
            }
        }); 



        });
    });
</script>

<script type="text/javascript">
$(document).ready(function(){

    $('#productQuantity').on('input', function() {
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    });

    $("#orderNo").on('input',function(){
        $("#orderNoError").hide();
    });
    $("#transferOrderNo").on('input',function(){
        $("#transferOrderNoError").hide();
    });
    $("#branchTo").on('change',function(){
        $("#branchToError").hide();
    });

});
  

</script>

{{-- Filtering --}}
<script type="text/javascript">

        $("#productGroupId").change(function(){ 
             
             var productGroupId = $('#productGroupId').val();
             
       
              var csrf = "<?php echo csrf_token(); ?>";
              
              $.ajax({
                  type: 'post',
                  url: './famsOnChangeGroup',
                  data: {productGroupId:productGroupId,_token: csrf},
                  dataType: 'json',   
                  success: function( _response ){

                    $("#productCategoryId").empty();
                    $("#productCategoryId").prepend('<option selected="selected" value="">Please Select</option>');

                    $("#productSubCategoryId").empty();
                    $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select</option>');

                    $("#productBrandId").empty();
                    $("#productBrandId").prepend('<option selected="selected" value="">Please Select</option>');
                    
                    $("#product").empty();
                    $("#product").prepend('<option selected="selected" value="">Please Select</option>');
                
                $.each(_response, function (key, value) {
                  {
                  if (key == "productCategoryList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#productCategoryId').append("<option value='"+ value1+"'>"+key1+"</option>");
                       });
                    }

                    if (key == "productSubCategoryList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#productSubCategoryId').append("<option value='"+ value1+"'>"+key1+"</option>");
                       });
                    }

                    if (key == "productBrandList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#productBrandId').append("<option value='"+ value1+"'>"+key1+"</option>");
                       });
                    }
              
                if (key == "productList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#product').append("<option value='"+ value1+"'>"+key1+"</option>");
                                            
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



        //Change Category
        $("#productCategoryId").change(function(){ 
             var productGroupId = $('#productGroupId').val();
             var productCategoryId = $('#productCategoryId').val();
             
              
              var csrf = "<?php echo csrf_token(); ?>";

              //alert(productGroupId);
              //alert(productCategoryId);
              
              $.ajax({
                  type: 'post',
                  url: './famsOnChangeCategory',
                  data: {productGroupId:productGroupId, productCategoryId:productCategoryId,_token: csrf},
                  dataType: 'json',   
                  success: function( _response ){

                    $("#productSubCategoryId").empty();
                    $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select</option>');

                    $("#productBrandId").empty();
                    $("#productBrandId").prepend('<option selected="selected" value="">Please Select</option>');
                    
                    $("#product").empty();
                    $("#product").prepend('<option selected="selected" value="">Please Select</option>');
                
                $.each(_response, function (key, value) {
                  {
                  if (key == "productSubCategoryList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#productSubCategoryId').append("<option value='"+ value1+"'>"+key1+"</option>");
                       });
                    }

                    if (key == "productBrandList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#productBrandId').append("<option value='"+ value1+"'>"+key1+"</option>");
                       });
                    }
              
                if (key == "productList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#product').append("<option value='"+ value1+"'>"+key1+"</option>");
                                            
                    });
                  } 
              }
            });
                
                },
                error: function(_response){
                  alert("error");
                }

              });/*End Ajax*/
              
            
        }); /*End Change Category*/


        //Change Sub Category
        $("#productSubCategoryId").change(function(){ 
             var productGroupId = $('#productGroupId').val();
             var productCategoryId = $('#productCategoryId').val();
             var productSubCategoryId = $('#productSubCategoryId').val();
             
              
              var csrf = "<?php echo csrf_token(); ?>";
              
              $.ajax({
                  type: 'post',
                  url: './famsOnChangeSubCategory',
                  data: {productGroupId:productGroupId, productCategoryId:productCategoryId, productSubCategoryId: productSubCategoryId,_token: csrf},
                  dataType: 'json',   
                  success: function( _response ){

                    $("#productBrandId").empty();
                    $("#productBrandId").prepend('<option selected="selected" value="">Please Select</option>');
                    
                    $("#product").empty();
                    $("#product").prepend('<option selected="selected" value="">Please Select</option>');
                
                $.each(_response, function (key, value) {
                  {
                  if (key == "productBrandList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#productBrandId').append("<option value='"+ value1+"'>"+key1+"</option>");
                       });
                    }
              
                if (key == "productList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#product').append("<option value='"+ value1+"'>"+key1+"</option>");
                                            
                    });
                  } 
              }
            });
                
                },
                error: function(_response){
                  alert("error");
                }

              });/*End Ajax*/          

            
        }); /*End Change Category*/


        //Change Sub Brand
        $("#productBrandId").change(function(){ 
             var productGroupId = $('#productGroupId').val();
             var productCategoryId = $('#productCategoryId').val();
             var productSubCategoryId = $('#productSubCategoryId').val();
             var productBrandId = $('#productBrandId').val();
             
              
              var csrf = "<?php echo csrf_token(); ?>";
              
              $.ajax({
                  type: 'post',
                  url: './famsOnChangeBrand',
                  data: {productGroupId:productGroupId, productCategoryId:productCategoryId, productSubCategoryId: productSubCategoryId,productBrandId: productBrandId,_token: csrf},
                  dataType: 'json',   
                  success: function( _response ){
                    
                    $("#product").empty();
                    $("#product").prepend('<option selected="selected" value="">Please Select</option>');
                
                $.each(_response, function (key, value) {
                  {
                  
              
                if (key == "productList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#product').append("<option value='"+ value1+"'>"+key1+"</option>");
                                            
                    });
                  } 
              }
            });
                
                },
                error: function(_response){
                  alert("error");
                }

              });/*End Ajax*/          

            
        }); /*End Change Brand*/
    
</script>


@endsection






