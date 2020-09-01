@extends('layouts/fams_layout')
@section('title', '| Return Issue')
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
                        <a href="{{url('famsIssueReturned/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Issue Return List</a>
          </div>
          </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">Issue Return</div>
                </div>
                <div class="panel-body">
                    {!! Form::open(array('url' => '', 'enctype' => 'multipart/form-data', 'role' => 'form',  'class'=>'form-horizontal form-groups')) !!}
                        
                         <div class="form-group">
                            {!! Form::label('issueReturnBillNo', 'Issue Return Bill No:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-5">
                            <input type="text" name="issueReturnBillNo" id="issueReturnBillNo" value={{$issueReturnBillNo}} class="form-control" autocomplete="off" readonly>
                                
                            </div>
                        </div>                     


                        <div class="form-group">
                            {!! Form::label('branchId', 'Branch Name:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-5">
                            <input type="text" name="branchName" value={{$branchName}} class="form-control" autocomplete="off" readonly>
                                
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('totalIssueQuantity', 'Return Issue Quantity:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-5">
                                {!! Form::text('totalIssueQuantity', $value = null, ['class' => 'form-control', 'id' => 'totalIssueQuantity', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>
                        </div>

                                        

                        <div class="form-group">
                            
                            <div class="col-sm-4">
                                
                                
                            </div>
                        </div>
                    {!! Form::close()  !!}
                    <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                    <div class="col-sm-7 text-right" style="padding-right: 10px">
                    <button id="save" class="btn btn-info">Save</button>
                    <a href="{{url('famsIssueReturned/')}}" class="btn btn-danger closeBtn">Close</a>
                    </div>
                  </div>
             </div>
        </div>
    </div>
</div>

{!! Form::hidden('totalIssueAmount', $value = null, ['class' => 'form-control', 'id' => 'totalIssueAmount', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
<!-- Table to Add Product -->

<div class="panel panel-primary" style="padding-left: 0px;padding-right: 0px;">
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
                                    <th style="display:none;"></th>
                                </tr>
                                
                            {{-- </thead> --}}
                        
                            
                            <tbody>

                            
                               <tr id="headerRow">
                                    <td style="text-align:center;" class="col-sm-3">Item Name</td>
                                    <td style="text-align:center;" class="col-sm-2">Qty</td>
                                    <td style="text-align:center;" class="col-sm-3 ">Action</td>
                                    <td style="text-align:center;" class="col-sm-3 hidden">Price</td>
                                    
                                </tr>
                                

                                
                                <tr>
                                    <td>Total Quantity</td>
                                    <td class="quantityTd"><input type="hidden" id="totalQuantityInput" name="totalQuantityInput" value="0" autocomplete="off"><span id="totalQuantityColumn"></span></td>
                                    <td></td>
                                    <td style="display:none;"></td>
                                    
                                </tr>                                
                                   
                            </tbody>
                        </table>
                        
                </div>
        </div>

</div>
<div class="footerTitle" style="border-top:1px solid white"></div>

<div class="col-sm-1 col-lg-1"></div>

<!-- Build the Product Table -->
<script type="text/javascript">

    $(document).ready(function(){
        $("#addToCart").click(function(){ 
         
                             

            var productId = $("#product option:selected").val();
            var productName = $("#product option:selected").html();
            var ProductQuantity = parseInt($("#productQuantity").val());
            var ProductPrice = $("#product option:selected").attr('price');
            
            var total = parseFloat(ProductQuantity) * parseFloat(ProductPrice);
            var grandTotalQuantity = $("#totalQuantityInput").val();
            

            

            if(productId>0 && ProductQuantity>0){
                
    
                grandTotalQuantity = parseFloat(grandTotalQuantity) + parseFloat(ProductQuantity);
                $("#totalQuantityInput").val(grandTotalQuantity);

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

                  var markup = "<tr class='valueRow'><td class='nameColumn'><input type='hidden' class='idColumnInput' value='"+productId+"'><input type='hidden' class='nameColumnInput' value='"+productName+"'>"+productName +"</td><td class='quantityColumn'>" + ProductQuantity + "</td><td><button class='btn btn-danger btn-xs removeButton'>Remove</button></td><td class='priceColumn' style='display:none;'>"+ProductPrice+"</td></tr>";

                $("#headerRow").after(markup);

                }            

  
                $("#tableError").hide();               

                $("#totalQuantityColumn").text(grandTotalQuantity);
                

                $("#product").val("");
                //$("#product option[value='"+productId+"']").hide();
                $("#productQuantity").val(null);
                

                
                $("#totalIssueQuantity").text(grandTotalQuantity);
                $("#totalIssueQuantity").val(grandTotalQuantity);

                return false;

            }

        })

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

<!-- Get The vlaue of Table data for Issue Details Table -->


<script type="text/javascript">
    $(document).ready(function(){
        $("#save").click(function(){
            
            
            
            //Get all the vlaues
            var issueReturnBillNo = $("#issueReturnBillNo").val();
            var branchId = {{$branchId}};
            var totalIssueQuantity = $("#totalIssueQuantity").val();
            var csrf = "<?php echo csrf_token(); ?>";

             

            //Get Table Data for Issue Details Table
            var fieldproductId = new Array();
            var fieldproductName = new Array();
            var fieldproductQuantity = new Array();
            var fieldproductPrice = new Array();

            $("#addProductTable tr.valueRow").each(function(){
                fieldproductId.push(JSON.stringify($(this).find('.idColumnInput').val()));
                fieldproductName.push(JSON.stringify($(this).find('.nameColumnInput').val()));
                fieldproductQuantity.push(JSON.stringify($(this).find('.quantityColumn').html()));
                fieldproductPrice.push(JSON.stringify($(this).find('.priceColumn').html()));
                });      

                
            if(fieldproductId.length<=0){                    
                    $("#tableError").show();
                return;
                 }   

            $.ajax({
            type: 'post',
            url: './famsStoreIssueReturn',
            data: {issueReturnBillNo: issueReturnBillNo,branchId: branchId, totalIssueQuantity: totalIssueQuantity, _token: csrf, fieldproductId: fieldproductId, fieldproductName: fieldproductName,fieldproductQuantity: fieldproductQuantity,fieldproductPrice: fieldproductPrice},
            
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

<script type="text/javascript">
  $(document).ready(function(){
    $("#product").change(function(){
      $("#tableError").hide();
      });
  });
</script>

{{-- Hide the Product from the select option those are exsits in the table --}}
{{-- <script type="text/javascript">
  $(document).ready(function(){
    $("#addProductTable tr.valueRow").each(function(){
      var productId = $(this).find('.idColumnInput').val();
      $("#product option[value=="+productId+"]").hide();
    });

  });
</script> --}}

@endsection





