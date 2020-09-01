@extends('layouts/pos_layout')
@section('title', '| Product')
@section('content')
@include('successMsg')
<style type="text/css">
    .select2-results__option[aria-selected=true] {
    display: none;
}
</style>
<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                        <div class="panel-options">
                          <a href="{{url('pos/posAddProduct/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Product</a>
                        </div>

                        {{-- <div class="row" id="filtering-group">
                            <div class="form-horizontal form-groups" style="padding-right: 0px;">
                                {!! Form::open(['url' => 'pos/posViewProduct','method' => 'get']) !!}
                                {{ csrf_field() }}
                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:black;">
                                    <div style="text-align: center;" class="col-sm-12">

                                        {!! Form::label('', 'Group:', ['class' => 'control-label pull-left']) !!}
                                    </div>
                                    <div class="col-sm-12">
                                        <select name="searchGroup" class="form-control input-sm" id="searchGroup">

                                            <option value="">All</option>

                                            @foreach($groups as $group)
                                                <option value="{{$group->id}}" @if($group->id==$groupSelected){{"selected=selected"}}@endif>{{$group->name}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:black;">
                                    <div style="text-align: center;" class="col-sm-12">
                                        {!! Form::label('', 'Category:', ['class' => 'control-label pull-left']) !!}
                                    </div>
                                    <div class="col-sm-12">
                                        <select name="searchCategory" class="form-control input-sm" id="searchCategory">
                                            <option value="">All</option>
                                            @foreach($categories as $category)
                                                <option value="{{$category->id}}" @if($category->id==$categorySelected){{"selected=selected"}}@endif>{{$category->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group" style="font-size: 13px; color:black;">
                                    <div style="text-align: center;" class="col-sm-12">
                                        {!! Form::label('', 'Sub Category:', ['class' => 'control-label pull-left']) !!}
                                    </div>

                                    <div class="col-sm-12">
                                        <select name="searchSubCategory" class="form-control input-sm" id="searchSubCategory">
                                            <option value="">All</option>
                                            @foreach($subCategories as $subCategory)
                                                <option value="{{$subCategory->id}}" @if($subCategory->id==$subCategorySelected){{"selected=selected"}}@endif>{{$subCategory->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:black;">
                                    <div style="text-align: center;" class="col-sm-12">
                                        {!! Form::label('', 'Brand:',['class' => 'control-label pull-left']) !!}
                                    </div>
                                    <div class="col-sm-12">
                                        <select name="searchBrand" class="form-control input-sm" id="searchBrand">
                                            <option value="">All</option>
                                            @foreach($brands as $brand)
                                              <option value="{{$brand->id}}" @if($brand->id==$brandSelected){{"selected=selected"}}@endif>{{$brand->name}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:black;">
                                    <div style="text-align: center;" class="col-sm-12">
                                        {!! Form::label('', 'Model:', ['class' => 'control-label pull-left']) !!}
                                    </div>
                                    <div class="col-sm-12">
                                        <select name="searchModel" class="form-control input-sm" id="searchModel">
                                            <option value="">All</option>
                                            @foreach($models as $model)
                                                <option value="{{$model->id}}" @if($model->id==$modelSelected){{"selected=selected"}}@endif>{{$model->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:black;">
                                    <div style="text-align: center;" class="col-sm-12">
                                        {!! Form::label('', 'Size:', ['class' => 'control-label pull-left']) !!}
                                    </div>
                                    <div class="col-sm-12">
                                        <select name="searchSize" class="form-control input-sm" id="searchSize">
                                            <option value="">All</option>
                                                @foreach($sizes as $size)
                                                    <option value="{{$size->id}}" @if($size->id==$sizeSelected){{"selected=selected"}}@endif>{{$size->name}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:black;">
                                    <div style="text-align: center;" class="col-sm-12">
                                        {!! Form::label('', 'Color:', ['class' => 'control-label pull-left']) !!}
                                    </div>
                                    <div class="col-sm-12">
                                        <select name="searchColor" class="form-control input-sm" id="searchColor">
                                            <option value="">All</option>
                                            @foreach($colors as $color)
                                                <option value="{{$color->id}}" @if($color->id==$colorSelected){{"selected=selected"}}@endif>{{$color->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:black">
                                    <div class="col-sm-12" style="padding-top: 25px;">
                                        {!! Form::submit('Search',['id'=>'search','class'=>'btn btn-primary btn-xs','style'=>'font-size:15px;']) !!}
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div> --}}
                    <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">PRODUCT LIST</font></h1>
                </div>
        <div class="panel-body panelBodyView">
        <div>
         <div>

              <script type="text/javascript">
                jQuery(document).ready(function($)  {
                  $("#productView").dataTable({
                          "oLanguage": {
                            "sEmptyTable": "No Records Available",
                            "sLengthMenu": "Show _MENU_ "
                          }
                  });
                 });

              </script>

        </div>
        </div>
            <table class="table table-striped table-bordered" id="productView" style="color:black;">
                <thead>
                    <tr>
                        <th width="80">SL#</th>
                        <th>Product Name</th>
                        <th>Code</th>
                        {{-- <th>Catagory</th>
                        <th>Sub Catagory</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Size</th>
                        <th>Color</th> --}}
                        <!-- <th>Cost Price</th>
                        <th>Seles Price</th> -->
                        <th>Action</th>
                    </tr>
                    {{ csrf_field() }}
                </thead>
                <tbody>
                    <?php $no=0; ?>
                    @foreach($products as $product)
                        <tr class="item{{$product->id}}">
                            <td >{{++$no}}</td>
                            <td style="text-align: left; padding-left: 5px;">{{$product->name}}</td>
                            <td style="text-align: center;">{{$product->code}}</td>
                            
                            <!-- <td style="text-align: right; padding-right: 5px;">{{number_format($product->costPrice,2)}}</td>
                            <td style="text-align: right; padding-right: 5px;">{{number_format($product->salesPrice,2)}}</td> -->
                            <td class="text-center" width="100">

                                <a href="javascript:;" class="form5" data-token="" data-id="{{$product->id}}">
                                <span class="fa fa-eye"></span>
                                </a>

                                &nbsp
                                <a href="javascript:;" class="edit-modal" productId="{{$product->id}}">

                                <span class="glyphicon glyphicon-edit"></span>
                                </a>&nbsp
                                <a id="deleteIcone" href="javascript:;" class="delete-modal" productId="{{$product->id}}">
                                <span class="glyphicon glyphicon-trash"></span>
                                </a>
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
        <!-- Edit Modal -->
<div id="editModal" class="modal fade" style="margin-top:3%;">
  <div class="modal-dialog">
   <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Update Product</h4>
      </div>
      <div class="modal-body">
       {!! Form::open(array('url' => '','id'=>'entryForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
        <input id="EMproductId" type="hidden"  value=""/>

        <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('name', 'Product Name:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'EMname', 'type' => 'text', 'placeholder' => 'Enter product name']) !!}
                                <p id='namee' style="max-height:3px;"></p>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('code', 'Product Code:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('code', $value = null, ['class' => 'form-control', 'id' => 'EMcode', 'type' => 'text', 'placeholder' => 'Enter product code']) !!}
                                <p id='codee' style="max-height:3px;"></p>
                            </div>
                        </div>
                        
                    </div>

                    <div class="col-md-6">
                        <!-- <div class="form-group">
                            {!! Form::label('costPrice', 'Cost Price:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('costPrice', $value = null, ['class' => 'form-control', 'id' => 'EMcostPrice', 'type' => 'text', 'placeholder' => 'Enter cost price']) !!}
                                <p id='costPricee' style="max-height:3px;"></p>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('salesPrice', 'Seles Price:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('salesPrice', $value = null, ['class' => 'form-control', 'id' =>'EMsalesPrice', 'type' => 'text', 'placeholder' => 'Enter seles price']) !!}
                                <p id='selesPricee' style="max-height:3px;"></p>
                            </div>
                        </div> -->

                        <div class="form-group">
                            {!! Form::label('Type', 'Type:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <select class="form-control" id="type" name="type">
                                    <option value="0">Select Type</option>
                                    @foreach ($productTypes as $key => $type)
                                        <option value="{{ $key }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                                <p id='typeMsg' style="max-height:3px;"></p>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('unit', 'Unit:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::select('unit', ['Select unit']+$units, $value = null, ['class' => 'form-control', 'id' => 'unit']) !!}
                                <p id='unitMsg' style="max-height:3px;"></p>
                            </div>
                        </div>

                        
                    </div>

                    <div class="modal-footer">
                        <input id="EMproductId" type="hidden" name="productId" value="">
                        <button type="button" id="updateButton" class="btn btn-success"><span class="glyphicon glyphicon-edit" style="padding-right:4px;"></span>Update</button>
                        <button type="button" class="btn btn-danger " data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
                    </div>
                  </div>
               </div>
            </div>
        </div>
     </div>
  </div>

   <div id="deleteModal" class="modal fade" style="margin-top:3%;">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Delete Product</h4>
          </div>

         <div class="modal-body ">
           <div class="row" style="padding-bottom:20px;"> </div>
            <h2>Are You Confirm to Delete This Record?</h2>

          <div class="modal-footer">
             <input id="DMproductPackageId" type="hidden"  value=""/>
             <button type="button" class="btn btn-danger"  id="DMproduct"  data-dismiss="modal">confirm</button>
             <button type="button" class="btn btn-warning"  data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
          </div>

         </div>
        </div>
      </div>
   </div>



@include('pos/product/productSetting/product/productDetails')

<script>

 //$(".select2-selection__rendered").show();
 $(document).ready(function(){
$('#productPackge').select2();
   $("#productPackge").on("select2:select", function (evt) {
    var element = evt.params.data.element;
    var $element = $(element);
    $element.detach();
    $(this).append($element);
    $(this).trigger("change");

});
 $('#productPackge').next("span").css("width","100%");

$(document).on('click', '.delete-modal', function(){
        $("#DMproductPackageId").val($(this).attr('productId'));
        $('#deleteModal').modal('show');
      });
        $("#DMproduct").on('click',  function() {
            var productId= $("#DMproductPackageId").val();
            var csrf = "{{csrf_token()}}";
              $.ajax({
                   url: './posDeleteProduct',
                   type: 'POST',
                   dataType: 'json',
                   data: {id:productId, _token:csrf},
              })
              .done(function(data) {

                   location.reload();
                   window.location.href = '{{url('pos/posViewProduct/')}}';
               })
              .fail(function(){
                  console.log("error");
              })
              .always(function() {
                  console.log("complete");
              });
        });
    });
$( document ).ready(function() {
//update image show
     $('#EMcostPrice').on('input', function(event) {
           this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');
     });
     $('#EMsalesPrice').on('input', function(event) {
           this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');
     });
  $(document).on('click', '.edit-modal', function() {
               var productId = $(this).attr('productId');
               var csrf = "{{csrf_token()}}";
               $("#EMproductId").val(productId);

                  $.ajax({
                     url: './getProductInfo',
                     type: 'POST',
                     dataType: 'json',
                     data: {id:productId , _token: csrf},
                     success: function(data) {
                        // $("#EMdescription").val(data['product'].description);
                        $("#EMname").val(data['product'].name);
                        $("#EMcode").val(data['product'].code);
                        // $("#EMgroupId").val(data['groupName'].id);
                        // $("#EMcategoryId").val(data['categoryName'].id);
                        // $("#EMsubCategoryId").val(data['subCategoryName'].id);
                        // $("#EMbrandId").val(data['productBrandName'].id);
                        // $("#EMmodelId").val(data['productModelName'].id);
                        // $("#EMsizeId").val(data['ProductSizeName'].id);
                        // $("#EMcolorId").val(data['productColorName'].id);
                        $("#EMcostPrice").val(data['product'].costPrice);
                        $("#EMsalesPrice").val(data['product'].salesPrice);
                        // $("#EMdescription").val(data['product'].description);

                        $('#type').val(data['product'].type);
                        console.log(data['product'].unit);

                        if (data['product'].unit != '' && data['product'].unit != null) {
                            $('#unit').val(data['product'].unit);
                        }
                        
                        // $('#type').append('<option value="0">Select Type</option>');
                        
                        // if(data['product'].type == 'product')
                        //     $('#type').append('<option value="product" selected>Product</option>');
                        
                        // if(data['product'].type == 'hour')     
                        //     $('#type').append('<option value="hour" selected>Hour</option>');
                    

                        var productId = data['product'].id;// Data Array
                        // $('#productPackge').val(productId);
                        // // Create a DOM Option and pre-select by default
                        // var newOption = new Option(productId.text, productId.id, true, true);
                        //         // Append it to the select
                        //     $('#productPackge').append(newOption).trigger('change');
                        //     $("#productPackge option[selected]").remove();

                            $("#editModal").find('.modal-dialog').css('width', '80%');
                            $('#editModal').modal('show');

                          },
                               error: function(argument) {
                                //alert('response error');
                    }

                });
      });



    $("#updateButton").on('click', function() {
        //  $("#updateButton").prop("disabled", true);
           var productId         = $("#EMproductId").val();
           var name              = $("#EMname").val();
           var code              = $("#EMcode").val();
           // var groupId           = $("#EMgroupId").val();
           // var categoryId        = $("#EMcategoryId").val();
           // var subCategoryId     = $("#EMsubCategoryId").val();
           // var brandId           = $("#EMbrandId").val();
        //    var salesPrice        = $("#EMsalesPrice").val();
           // var modelId           = $("#EMmodelId").val();
           // var sizeId            = $("#EMsizeId").val();
           // var colorId           = $("#EMcolorId").val();
        //    var costPrice         = $("#EMcostPrice").val();

           var type = $("#type").val();
           var unit = $("#unit").val();
           // var description       = $("#EMdescription").val();
           // var productPackage    = $("#productPackge").val();
           var csrf = "{{csrf_token()}}";

           $.ajax({
               url: './updateProductinfo',
               type: 'POST',
               dataType: 'json',
               data: {id:productId,name:name,code:code,type:type,unit:unit,_token: csrf},
          })
           .done(function(data) {
            //alert(JSON.stringify(data.errors));
            console.log(data);
                   if (data.errors) {
                     if (data.errors['name']) {
                        $("#namee").empty();
                        $("#namee").append('<span class="errormsg" style="color:red;">'+data.errors['name']);
                     }
                     if (data.errors['code']) {
                        $("#codee").empty();
                        $("#codee").append('<span class="errormsg" style="color:red;">'+data.errors['code']);
                     }

                    //  if (data.errors['salesPrice']) {
                    //     $("#selesPricee").empty();
                    //     $("#selesPricee").append('<span class="errormsg" style="color:red;">'+data.errors['salesPrice']);
                    //  }
                    //  if (data.errors['costPrice']) {
                    //     $("#costPricee").empty();
                    //     $("#costPricee").append('<span class="errormsg" style="color:red;">'+data.errors['costPrice']);
                    //  }
                     if (data.errors['type']) {
                        $("#typeMsg").empty();
                        $("#typeMsg").append('<span class="errormsg" style="color:red;">'+data.errors['type']);
                     }
                     if (data.errors['unit']) {
                        $("#unitMsg").empty();
                        $("#unitMsg").append('<span class="errormsg" style="color:red;">'+data.errors['unit']);
                     }
                    }
                   else {
                     location.reload();
                   }
                    // console.log("success");
                 })
               .fail(function() {
                  console.log("error");
               })
               .always(function() {
                  console.log("complete");
               })
        });


  $(document).on('click', '.form5', function() {

    $('#imageShow3').empty();
    var id = ($(this).data('id'));
    var crsf = ($(this).data('token'));
    $.ajax({
         type: 'post',
         url: './posProductDetail',
         data: {
            '_token': $('input[name=_token]').val(),
            'id': id
         },
         dataType: 'json',
        success: function( data ){

            console.log(data.productType);

            $.each(data, function( index, value ){
                $('#ProductName').text(data.productName);
                $('#ProductCode').text(data.productCode);
                $('#Id').text(data.productIdNo);
                $('#productType').text(data.productType);
                $('#productUnit').text(data.productUnit);
                // $('#Description').text(data.description);
                // $('#Group').text(data["groupName"].name);
                // $('#Catagory').text(data["categoryName"].name);
                // $('#SubCatagory').text(data["subCategoryName"].name);
                // $('#Brand').text(data["productBrandName"].name);
                // $('#Model').text(data["productModelName"].name);
                // $('#Size').text(data["ProductSizeName"].name);
                // $('#Color').text(data["productColorName"].name);
                // $('#CostPrice').text(data.costPrice);
                // $('#SalesPrice').text(data.salesPrice);
                // $('#VMproductPakage').text(data.productPackageName);
            });

                $('.modal-title').text('Product Details');
                $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
                $('.modal-dialog').css('width','90%');
                $('.form-horizontal').show();
                $('#myModal2').modal('show');
        },
        error: function( data ){
        alert();
        }
    });
  });

 $("input").keyup(function(){
      var name = $("#EMname").val();
      if(name){$('#namee').hide();}else{$('#namee').show();}
      var code = $("#EMcode").val();
      if(code){$('#codee').hide();}else{$('#codee').show();}
      var costPrice = $("#EMcostPrice").val();
      if(costPrice){$('#costPricee').hide();}else{$('#costPricee').show();}
      var salesPrice = $("#EMsalesPrice").val();
      if(salesPrice){$('#selesPricee').hide();}else{$('#selesPricee').show();}
 });


 });//ready function end


 </script>

@include('dataTableScript')
@endsection
