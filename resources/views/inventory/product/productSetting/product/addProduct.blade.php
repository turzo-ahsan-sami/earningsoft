@extends('layouts/inventory_layout')
@section('title', '| Home')
@section('content')
<?php 
 $user = Auth::user();
 Session::put('branchId', $user->branchId);
 $gnrBranchId = Session::get('branchId');
 $logedUserName = $user->name;

?>
<div class="row add-data-form" style="height:100%">
    <div class="col-md-12">
            <div class="col-md-1"></div>
                <div class="col-md-10 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('viewProduct/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Product List</a>
                    </div>
                <div class="panel panel-default panel-border">
                        <div class="panel-heading">
                            <div class="panel-title">New Product</div>
                        </div>
                    <div class="panel-body">
                        
                    {!! Form::open(array('url' => '' , 'enctype' => 'multipart/form-data',  'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                         
                <div class="row">
                <div class="col-md-12">

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('name', 'Product:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter product name']) !!}
                                <p id='namee' style="max-height:3px;"></p>
                        </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('supplierId', 'Supplier Name:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <?php 
                                    $supplierName = array('' => 'Please Select Supplier Name') + DB::table('gnr_supplier')->pluck('supplierCompanyName','id')->all(); 
                                ?>
                                {!! Form::select('supplierId', ($supplierName), null, array('class'=>'form-control', 'id' => 'supplierId')) !!}
                                <p id='supplierIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('groupId', 'Group', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <?php 
                                    $GroupName = array('' => 'Please Select Product Group') + DB::table('inv_product_group')->pluck('name','id')->all(); 
                                ?>
                                {!! Form::select('groupId', ($GroupName), null, array('class'=>'form-control', 'id' => 'groupId')) !!}
                                <p id='groupIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('categoryId', 'Category:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            <?php 
                                $productCategoryId = array('' => 'Please Select Prduct Category') + DB::table('inv_product_category')->pluck('name','id')->all(); 
                            ?>      
                            {!! Form::select('categoryId', ($productCategoryId), null, array('class'=>'form-control', 'id' => 'categoryId')) !!}
                                <p id='productCategoryIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('subCategoryId', 'Sub Catagory:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            <?php 
                                $productSubCategoryId = array('' => 'Please Select Prduct SubCategory') + DB::table('inv_product_sub_category')->pluck('name','id')->all(); 
                            ?>  
                                {!! Form::select('subCategoryId', ($productSubCategoryId), null, array('class'=>'form-control', 'id' => 'subCategoryId')) !!}
                                <p id='productSubCategoryIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('brandId', 'Brand:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            <?php 
                                $productBrandId = array('' => 'Please Select Prduct Brand') + DB::table('inv_product_brand')->pluck('name','id')->all(); 
                            ?>
                                {!! Form::select('brandId', ($productBrandId), null, array('class'=>'form-control', 'id' => 'brandId')) !!}
                                <p id='productBrandIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('modelId', 'Model:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            <?php 
                                $productModelId = array('' => 'Please Select Prduct Model') + DB::table('inv_product_model')->pluck('name','id')->all(); 
                            ?>
                                {!! Form::select('modelId', ($productModelId), null, array('class'=>'form-control', 'id' => 'modelId')) !!}
                                <p id='productModelIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('sizeId', 'Size:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            <?php 
                                $productSizeId = array('' => 'Please Select Prduct Size') + DB::table('inv_product_size')->pluck('name','id')->all(); 
                            ?>
                                {!! Form::select('sizeId', ($productSizeId), null, array('class'=>'form-control', 'id' => 'sizeId')) !!}
                                <p id='productSizeIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('colorId', 'Color:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <?php 
                                    $productColorId = array('' => 'Please Select Prduct Color') + DB::table('inv_product_color')->pluck('name','id')->all(); 
                                ?>
                                {!! Form::select('colorId', ($productColorId), null, array('class'=>'form-control', 'id' => 'colorId')) !!}
                                <p id='colorIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('uomId', 'UOM:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            <?php 
                                $productUomId = array('' => 'Please Select UOM') + DB::table('inv_product_uom')->pluck('name','id')->all(); 
                            ?>
                                {!! Form::select('uomId', ($productUomId), null, array('class'=>'form-control', 'id' => 'uomId')) !!}
                                <p id='uomIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                                                           
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('costPrice', 'Cost Price:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('costPrice', $value = null, ['class' => 'form-control', 'id' => 'costPrice', 'type' => 'text', 'placeholder' => 'Enter cost price']) !!}
                                <p id='costPricee' style="max-height:3px;"></p>
                            </div>
                        </div>
                        {{-- <div class="form-group">
                            {!! Form::label('salesPrice', 'Sales Price:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('salesPrice', $value = null, ['class' => 'form-control', 'id' => 'salesPrice', 'type' => 'text', 'placeholder' => 'Enter sales price']) !!}
                                <p id='salesPricee' style="max-height:3px;"></p>
                            </div>
                        </div> --}}
                        <div class="form-group">
                            {!! Form::label('openingStock', 'Opening Stock:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('openingStock', $value = null, ['class' => 'form-control', 'id' => 'openingStock', 'type' => 'text', 'placeholder' => 'Enter opening stock']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('openingStockAmount', 'O. Stock Amount:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('openingStockAmount', $value = null, ['class' => 'form-control', 'id' => 'openingStockAmount', 'type' => 'text', 'placeholder' => 'Enter opening stock amount']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('minimumStock', 'Minimum Stock:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('minimumStock', $value = null, ['class' => 'form-control', 'id' => 'minimumStock', 'type' => 'text', 'placeholder' => 'Enter minimum stock']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        {{-- <div class="form-group">
                            {!! Form::label('vat', 'VAT:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('vat', $value = null, ['class' => 'form-control', 'id' => 'vat', 'type' => 'text', 'placeholder' => 'Enter VAT']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div> --}}
                        {{-- <div class="form-group">
                            {!! Form::label('barcode', 'Barcode:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('barcode', $value = null, ['class' => 'form-control', 'id' => 'barcode', 'type' => 'text', 'placeholder' => 'Enter Barcode']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div> --}}
                        {{-- <div class="form-group">
                            {!! Form::label('systemBarcode', 'System Barcode:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('systemBarcode', $value = null, ['class' => 'form-control', 'id' => 'systemBarcode', 'type' => 'text', 'placeholder' => 'Enter system barcode']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div> --}}
                        <div class="form-group">
                           {!! Form::label('warranty', 'Warranty:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('warranty', $value = null, ['class' => 'form-control', 'id' => 'warranty', 'type' => 'text', 'placeholder' => 'Enter warranty']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                           {!! Form::label('serviceWarranty', 'Service Warranty:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('serviceWarranty', $value = null, ['class' => 'form-control', 'id' => 'serviceWarranty', 'type' => 'text', 'placeholder' => 'Enter service warranty']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        {{-- <div class="form-group">
                            {!! Form::label('compresserWarranty', 'Compresser Warranty:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('compresserWarranty', $value = null, ['class' => 'form-control', 'id' => 'compresserWarranty', 'type' => 'text', 'placeholder' => 'Enter compresser warranty']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div> --}}
                        <div class="form-group">
                            {!! Form::label('description', 'Decscription:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">                    
                                {!! Form::textarea('description', $value = null, ['class' => 'form-control', 'id' => 'description', 'rows' => 2, 'placeholder' => 'Enter description']) !!}  
                                <p id='descriptione' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('image', 'Upload Image:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::file('image', $value = null, ['class' => 'form-control', 'id' => 'image', 'type' => 'file']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group hidden">
                            {!! Form::label('branchId', 'branchId:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('branchId', $value = $gnrBranchId, ['class' => 'form-control', 'id' => 'branchId', 'type' => 'text', 'placeholder' => 'Enter compresser warranty']) !!}
                                
                            </div>
                        </div>

                    </div>         
                         
                </div>
                </div>


                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="form-group col-md-4 text-center">
                                {!! Form::label('submit', ' ', ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-12">
                                    {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                    <a href="{{url('viewProduct/')}}" class="btn btn-danger closeBtn">Close</a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <span id="success" style="color:green; font-size:20px;"></span>
                            </div>
                        </div>    
                {!! Form::close() !!}
                            
                    </div>
                </div>
                <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>
@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
<script type="text/javascript">

$(document).ready(function(){

$('form').submit(function( event ) {
    event.preventDefault();
    var formData = new FormData($(this)[0]);
    $.ajax({
         type: 'post',
         url: './addProductItem',
         data: formData,
         async: false,
         cache: false,
         contentType: false,
         processData: false,
         dataType: 'json',
        success: function( _response ){
            //alert(JSON.stringify(_response));
           if (_response.errors) {
            if (_response.errors['name']) {
                $('#namee').empty();
                $('#namee').show();
                $('#namee').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
                //return false;
            }
            if (_response.errors['supplierId']) {
                $('#supplierIde').empty();
                $('#supplierIde').append('<span class="errormsg" style="color:red;">'+_response.errors.supplierId+'</span>');
                //return false;
            }
            if (_response.errors['groupId']) {
                $('#groupIde').empty();
                $('#groupIde').append('<span class="errormsg" style="color:red;">'+_response.errors.groupId+'</span>');
                //return false;
            }
            if (_response.errors['categoryId']) {
                $('#productCategoryIde').empty();
                $('#productCategoryIde').append('<span class="errormsg" style="color:red;">'+_response.errors.categoryId+'</span>');
                //return false;
            }
            if (_response.errors['subCategoryId']) {
                $('#productSubCategoryIde').empty();
                $('#productSubCategoryIde').append('<span class="errormsg" style="color:red;">'+_response.errors.subCategoryId+'</span>');
                //return false;
            }
            if (_response.errors['brandId']) {
                $('#productBrandIde').empty();
                $('#productBrandIde').append('<span class="errormsg" style="color:red;">'+_response.errors.brandId+'</span>');
                //return false;
            }
            if (_response.errors['modelId']) {
                $('#productModelIde').empty();
                $('#productModelIde').append('<span class="errormsg" style="color:red;">'+_response.errors.modelId+'</span>');
                //return false;
            }
            if (_response.errors['sizeId']) {
                $('#productSizeIde').empty();
                $('#productSizeIde').append('<span class="errormsg" style="color:red;">'+_response.errors.sizeId+'</span>');
                //return false;
            }
            if (_response.errors['colorId']) {
                $('#colorIde').empty();
                $('#colorIde').append('<span class="errormsg" style="color:red;">'+_response.errors.colorId+'</span>');
                //return false;
            }
            if (_response.errors['uomId']) {
                $('#uomIde').empty();
                $('#uomIde').append('<span class="errormsg" style="color:red;">'+_response.errors.uomId+'</span>');
                //return false;
            }
            /*if (_response.errors['costPrice']) {
                $('#costPricee').empty();
                $('#costPricee').append('<span class="errormsg" style="color:red;">'+_response.errors.costPrice+'</span>');
                //return false;
            }
            if (_response.errors['salesPrice']) {
                $('#salesPricee').empty();
                $('#salesPricee').append('<span class="errormsg" style="color:red;">'+_response.errors.salesPrice+'</span>');
                //return false;
            }*/
    } else {
            $("#groupId").val('');
            $("#name").val('');
            $("#supplierId").val('');
            $("#categoryId").val('');
            $("#subCategoryId").val('');
            $("#brandId").val('');
            $("#modelId").val('');
            $("#sizeId").val('');
            $("#colorId").val('');
            $("#uomId").val('');
            $("#costPrice").val('');
            $("#salesPrice").val('');
            $("#description").val('');
            $("#barcode").val('');
            $("#systemBarcode").val('');
            $("#vat").val('');
            $("#openingStock").val('');
            $("#openingStockAmount").val('');
            $("#minimumStock").val('');
            $("#warranty").val('');
            $("#serviceWarranty").val('');
            $("#compresserWarranty").val('');
            $('.error').addClass("hidden");
            $('#success').text(_response.responseText);
            window.location.href = '{{url('viewProduct/')}}';
            }
        },
        error: function( _response ){
            // Handle error
            //alert(_response.errors);
            alert("Select Image please");
            
        }
        
    });
});

$("input").keyup(function(){
            var name = $("#name").val();
            if(name){$('#namee').hide();}else{$('#namee').show();}
             /*var costPrice = $("#costPrice").val();
            if(costPrice){$('#costPricee').hide();}else{$('#costPricee').show();}
             var salesPrice = $("#salesPrice").val();
            if(salesPrice){$('#salesPricee').hide();}else{$('#salesPricee').show();}*/
});
$('select').on('change', function (e) {
    var groupId = $("#groupId").val();
    if(groupId){$('#groupIde').hide();}else{$('#groupIde').show();}
    var supplierId = $("#supplierId").val();
    if(supplierId){$('#supplierIde').hide();}else{$('#supplierIde').show();}
    var categoryId = $("#categoryId").val();
    if(categoryId){$('#productCategoryIde').hide();}else{$('#productCategoryIde').show();}
    var subCategoryId = $("#subCategoryId").val();
    if(subCategoryId){$('#productSubCategoryIde').hide();}else{$('#productSubCategoryIde').show();}
    var brandId = $("#brandId").val();
    if(brandId){$('#productBrandIde').hide();}else{$('#productBrandIde').show();}
    var modelId = $("#modelId").val();
    if(modelId){$('#productModelIde').hide();}else{$('#productModelIde').show();}
    var sizeId = $("#sizeId").val();
    if(sizeId){$('#productSizeIde').hide();}else{$('#productSizeIde').show();}
    var colorId = $("#colorId").val();
    if(colorId){$('#colorIde').hide();}else{$('#colorIde').show();}
    var uomId = $("#uomId").val();
    if(uomId){$('#uomIde').hide();}else{$('#uomIde').show();}
});

});
</script>

/* Change Category*/
      <script type="text/javascript">
      $(document).ready(function(){
        $("#categoryId").change(function(){
            var productCategoryId = $(this).val();
            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './invCategoryChange',
                data: {productCategoryId:productCategoryId,_token: csrf},
                async: false,
                dataType: 'json',
                success: function( data ){
                    $("#subCategoryId").empty();
                    $("#subCategoryId").prepend('<option selected="selected" value="">All</option>');

                 $.each(data['subCategories'], function (key, productObj) {                       
                    $('#subCategoryId').append("<option value='"+ productObj.id+"'>"+(productObj.name)+"</option>");
                        
                    });

                  
                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Category*/

        });
     </script> 

