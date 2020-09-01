@extends('layouts/fams_layout')
@section('title', '| Issue')
@section('content')
<?php 
$user = Auth::user();
Session::put('branchId', $user->branchId);
$branchId = Session::get('branchId');
//echo $branchId;
?>
<div class="col-sm-1 col-lg-1"></div>
<div class="col-sm-10 col-lg-10">
<div class="add-data-form">    
    <div class="row">
    <div class="col-md-12">
        <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('famsIssue/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Issue List</a>
                    </div>
            
        </div>
        
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">Issue</div>
                </div>
                <div class="panel-body">
                    {!! Form::open(array('url' => '', 'enctype' => 'multipart/form-data', 'role' => 'form',  'class'=>'form-horizontal form-groups')) !!}
                        
                         <div class="form-group">
                            {!! Form::label('issueNo', 'Issue No:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                                {!! Form::text('issueNo', $issueNo, ['class' => 'form-control', 'id' => 'issueNo', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>

                            
                            {!! Form::label('branchId', 'Branch Name:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                            <select class ="form-control" id = "branchId" autocomplete="off">
                            <option value="0">Select Branch</option>
                                @foreach($branches as $branch)
                                <option value="{{$branch->id}}">{{$branch->name}}</option>
                                @endforeach
                            </select>
                            <span id="branchError" style="display: none;"><font color="red">*Please Select a Branch</font></span>
                                
                            </div>                      


                        </div>

                        <div class="form-group">
                            {!! Form::label('orderNo', 'Order No:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                                {!! Form::text('orderNo', $value = null, ['class' => 'form-control', 'id' => 'orderNo', 'type' => 'text','autocomplete'=>'off']) !!}
                                <span id="orderNoError" style="display: none;"><font color="red">*Required</font></span>
                            </div>

                             {!! Form::label('totalIssueQuantity', 'Issue Quantity:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                                {!! Form::text('totalIssueQuantity', $value = null, ['class' => 'form-control', 'id' => 'totalIssueQuantity', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('issueOrderNo', 'Issue Order No:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                                {!! Form::text('issueOrderNo', $value = null, ['class' => 'form-control', 'id' => 'issueOrderNo', 'type' => 'text','autocomplete'=>'off']) !!}
                                <span id="issueOrderNoError" style="display: none;"><font color="red">*Required</font></span>
                            </div>

                            {!! Form::label('totalIssueAmount', 'Issue Amount:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                                {!! Form::text('totalIssueAmount', $value = null, ['class' => 'form-control', 'id' => 'totalIssueAmount', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>
                        </div>
               

                        
                    {!! Form::close()  !!}
                    <br>
                    
                    <div class="col-sm-12 text-right" style="padding-right: 0px">
                    <button id="save" class="btn btn-info">Save</button>
                    <a href="{{url('famsIssue/')}}" class="btn btn-danger closeBtn">Close</a>
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

                            {!! Form::label('productGroupId', 'Group:', ['class' => 'control-label',]) !!}
                            
                                     <select  id="productGroupId" class="form-control input-sm" style="padding-right: 0px;">
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
                        
                            <tr>
                                
                                    <th style="text-align:center;">
                                    <select id="product" class="form-control">
                            <option value="">Select Product</option>
                                @foreach($products as $product)
                                <option value="{{$product->id}}" >{{$product->productCode}}</option>
                                @endforeach
                            </select></th>
                                    <th>
                                    <input type="text" id="productQuantity" name="productQuantity" placeholder="Product Quantiy" style="text-align:center;">
                                    </th>
                                    
                                    <th>
                                    <input type="hidden" id="productPrice" name="productPrice">
                                    <span id="productPriceLabel"></span>
                                    </th> 
                                    <th><span id="productTotalPriceLabel"></th>
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
                                    <td class="quantityTd"><input type="hidden" id="totalQuantityInput" name="totalQuantityInput" value="0" autocomplete="off"><span id="totalQuantityColumn"></span></td>
                                    <td>Total Amount</td>
                                    <td class="amountTd"><input type="hidden" id="totalAmountInput" name="totalAmountInput" value="0" autocomplete="off"><span id="totalAmountColumn"></span></td>
                                    <td></td>
                                </tr>                                
                                   
                            </tbody>
                        </table>
                        
                </div>
        </div>
</div>

<div class="footerTitle" style="border-top:1px solid white"></div>


<div class="col-sm-1 col-lg-1" ></div>

<script type="text/javascript">
  $(document).ready(function(){
    $("#product").change(function(){
      $("#tableError").hide();
      
      var productId = $("#product option:selected").val();
      var ProductQuantity = $("#productQuantity").val();
      var csrf = "<?php echo csrf_token(); ?>";
      
         $.ajax({
            type: 'post',
            url: './famsIssueGetProtPrice',
            data: {productId:productId,_token:csrf},
            
             dataType: 'json',
            success: function( data ) {
                $("#productPriceLabel").text(data);
                $("#productTotalPriceLabel").text(ProductQuantity*data);
                return false;
                } , 
            error: function( _response ){
            // Handle error
            alert('_response.errors');            
            }
        }); 
    });
  });
</script>


<!-- Build the Product Table -->
<script type="text/javascript">

    $(document).ready(function(){
        $("#addToCart").click(function(){ 
        $("#tableError").hide();              

            var productId = parseInt($("#product option:selected").val());
            var productName = $("#product option:selected").html();
            var ProductQuantity = parseInt($("#productQuantity").val());   
            


            var csrf = "<?php echo csrf_token(); ?>";
            //alert(productId);

            $.ajax({
            type: 'post',
            url: './issueGetProductPrice',
            data: {productId:productId,_token:csrf},
            
             dataType: 'json',
            success: function( data ) {
                //alert(data);
               var ProductPrice = data;



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
                  var markup = "<tr class='valueRow'><td class='nameColumn'><input type='hidden' class='idColumnInput' value='"+productId+"'><input type='hidden' class='nameColumnInput' value='"+productName+"'>"+productName +"</td><td class='quantityColumn'>" + ProductQuantity + "</td><td class='priceColumn'>" + ProductPrice + "</td><td class='totalColumn'>"+total+"</td><td><button class='btn btn-danger btn-x removeButton' style='padding: 2px 10px;'>Remove</button></td></tr>";

                    $("#headerRow").after(markup); 
                }

                $("#totalQuantityColumn").text(grandTotalQuantity);
                $("#totalAmountColumn").text(grandTotalPrice);

                $("#product").val("");
                //$("#product option[value='"+productId+"']").hide();
                $("#productQuantity").val(null);
                $("#productPrice").val(null);

                
                $("#totalIssueQuantity").text(grandTotalQuantity);
                $("#totalIssueQuantity").val(grandTotalQuantity);

                $("#totalIssueAmount").text(grandTotalPrice);
                $("#totalIssueAmount").val(grandTotalPrice);

            }

                return false;
                } /*End Ajax*/
              
          , 
            error: function( _response ){
            // Handle error
            alert('_response.errors');            
            }
        }); 

            $("#productPriceLabel").html("");

        });

             $(document).on('click', 'button.removeButton', function () {
                  
                  var tdQuantity = parseFloat($(this).closest('tr').find('.quantityColumn').text());
                  //var tdAmount = parseFloat($(this).closest('tr').find('.priceColumn').text());
                  var tdTotal = parseFloat($(this).closest('tr').find('.totalColumn').text());
                  
                  $("#totalQuantityColumn").text(parseFloat($("#totalQuantityColumn").text())-tdQuantity);
                  $("#totalAmountColumn").text(parseFloat($("#totalAmountColumn").text())-tdTotal);
                  
                  $("#totalQuantityInput").val(parseFloat($("#totalQuantityInput").val())-tdQuantity);
                  $("#totalAmountInput").val(parseFloat($("#totalAmountInput").val())-tdTotal);

                  $("#totalIssueQuantity").text($("#totalQuantityInput").val());
                  $("#totalIssueQuantity").val($("#totalQuantityInput").val());

                  $("#totalIssueAmount").text($("#totalAmountInput").val());
                  $("#totalIssueAmount").val($("#totalAmountInput").val());

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
            var issueNo = $("#issueNo").val();
            var orderNo = $("#orderNo").val();
            var issueOrderNo = $("#issueOrderNo").val();
            var branchId = $("#branchId option:selected").val();
            var totalIssueQuantity = $("#totalIssueQuantity").val();
            var totalIssueAmount = $("#totalIssueAmount").val();
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
            if(issueOrderNo==""){
                    $("#issueOrderNoError").show();
                return;
            }
            if(branchId==0){
                
                $("#branchError").show();
                return;
            }
                if(fieldproductId.length<=0){                    
                    $("#tableError").show();
                return;
                 }      

        

            $.ajax({
            type: 'post',
            url: './famsStoreIssue',
            data: {issueNo: issueNo, orderNo: orderNo, issueOrderNo: issueOrderNo,branchId: branchId, totalIssueQuantity: totalIssueQuantity, totalIssueAmount: totalIssueAmount, _token: csrf, fieldproductId: fieldproductId, fieldproductName: fieldproductName,fieldproductQuantity: fieldproductQuantity,fieldproductPrice: fieldproductPrice},
            
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
            error: function( _response ){
            // Handle error
            alert('_response.errors');            
            }
        }); 



        });
    });
</script>

<script type="text/javascript">
$(document).ready(function(){

    $('#productQuantity').on('input', function() {
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

        var productPrice = $("#productPriceLabel").html();
        $("#productTotalPriceLabel").html(productPrice*this.value);

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

});
  

</script>



{{-- Filtering --}}
<script type="text/javascript">

        $("#productGroupId").change(function(){ 
             
             var productGroupId = $('#productGroupId').val();

            //alert(productGroupId);
             
       
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

                        //alert(key1);
                        //alert(value1);
                      
                      $('#productCategoryId').append("<option value='"+ value1+"'>"+key1+"</option>");
                       });
                    }

                    if (key == "productSubCategoryList") {
                    $.each(value, function (key1,value1) {

                        alert(key1);
                        alert(value1);
                      
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
                  url: './famsOnCngSubCtgy',
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





