@extends('layouts/fams_layout')
@section('title', '| Product Name')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
            <div class="col-md-2"></div>
                <div class="col-md-8 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('viewFamsPname/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Product Type List</a>
                    </div>
                <div class="panel panel-default panel-border">
                                <div class="panel-heading">
                                    <div class="panel-title">Product Name</div>
                                </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">
                            {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                <div class="form-group">
                                        {!! Form::label('productGroupId', 'Group:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                            <?php 
                                                $ProductGroup = array('' => 'Please Select Product Group') + DB::table('fams_product_group')->pluck('name','id')->all(); 
                                            ?>      
                                            {!! Form::select('productGroupId', ($ProductGroup), null, array('class'=>'form-control', 'id' => 'productGroupId')) !!}
                                            <p id='productGroupIde' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                <div class="form-group">
                                        {!! Form::label('productCategoryId', 'Category:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                        <?php 
                                            $productCategoryId = array('' => 'Please Select Product Category') + DB::table('fams_product_category')->pluck('name','id')->all(); 
                                        ?>      
                                        {!! Form::select('productCategoryId', ($productCategoryId), null, array('class'=>'form-control', 'id' => 'productCategoryId')) !!}
                                            <p id='productCategoryIde' style="max-height:3px;"></p>
                                        </div>
                                </div>
                                <div class="form-group">
                                        {!! Form::label('productSubCategoryId', 'Sub Category:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                        <?php 
                                            $productSubCategoryId = array('' => 'Please Select Product Sub Category') + DB::table('fams_product_sub_category')->pluck('name','id')->all(); 
                                        ?>      
                                        {!! Form::select('productSubCategoryId', ($productSubCategoryId), null, array('class'=>'form-control', 'id' => 'productSubCategoryId')) !!}
                                            <p id='productSubCategoryIde' style="max-height:3px;"></p>
                                        </div>
                                </div>
                                <div class="form-group">
                                        {!! Form::label('productTypeId', 'Product Type:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                        <?php 
                                            $productTypeId = array('' => 'Please Select Product Sub Category') + DB::table('fams_product_type')->pluck('name','id')->all(); 
                                        ?>      
                                        {!! Form::select('productTypeId', ($productTypeId), null, array('class'=>'form-control', 'id' => 'productTypeId')) !!}
                                            <p id='productTypeIde' style="max-height:3px;"></p>
                                        </div>
                                </div>
                                
                                <div class="form-group">
                                    {!! Form::label('productName', 'Product Name:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Product Type Name']) !!}
                                        <p id='namee' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                    <div class="form-group">
                                        {!! Form::label('productNameCode', 'Product Name Code:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('productNameCode', $value = null, ['class' => 'form-control', 'id' => 'productNameCode', 'type' => 'text', 'placeholder' => 'Enter Product Type Code']) !!}
                                            <p id='productNameCodee' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                <div class="form-group">
                                    {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9 text-right">
                                        {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']) !!}
                                        <a href="{{url('viewFamsPname/')}}" class="btn btn-danger closeBtn">Close</a>
                                        <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                                    </div>
                                </div>
                            {!! Form::close() !!}
                            </div>
                            <div class="col-md-4 emptySpace vert-offset-top-0"><img src="images/catalog/image15.png" width="60%" height="" style="float:right">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
            <div class="col-md-2"></div>
    </div>
</div>
@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function(){
   
$('form').submit(function( event ) {
    event.preventDefault();
    $.ajax({
         type: 'post',
         url: './storeFamsPname',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
            //alert(JSON.stringify(_response));
    if (_response.errors) {
        if (_response.errors['name']) {
            $('#namee').empty();
            $('#namee').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
            //return false;
        }
            if (_response.errors['productNameCode']) {
                $('#productNameCodee').empty();
                $('#productNameCodee').show();
                $('#productNameCodee').append('<span class="errormsg" style="color:red;">'+_response.errors.productNameCode+'</span>');
                //return false;
            }
            if (_response.errors['productGroupId']) {
                $('#productGroupIde').empty();
                $('#productGroupIde').show();
                $('#productGroupIde').append('<span class="errormsg" style="color:red;">'+_response.errors.productGroupId+'</span>');
                //return false;
            }
            if (_response.errors['productCategoryId']) {
                $('#productCategoryIde').empty();
                $('#productCategoryIde').show();
                $('#productCategoryIde').append('<span class="errormsg" style="color:red;">'+_response.errors.productCategoryId+'</span>');
                //return false;
            }
            if (_response.errors['productSubCategoryId']) {
                $('#productSubCategoryIde').empty();
                $('#productSubCategoryIde').show();
                $('#productSubCategoryIde').append('<span class="errormsg" style="color:red;">'+_response.errors.productSubCategoryId+'</span>');
                //return false;
            }
            if (_response.errors['productTypeId']) {
                $('#productTypeIde').empty();
                $('#productTypeIde').show();
                $('#productTypeIde').append('<span class="errormsg" style="color:red;">'+_response.errors.productTypeId+'</span>');
                //return false;
            }

    }
        else{
        window.location.href = '{{url('viewFamsPname/')}}';
    }
        },
        error: function( _response ){
            // Handle error
            alert("error");
            
        }
    });
});

$("input").keyup(function(){
    var name = $("#name").val();
    if(name){$('#namee').hide();}else{$('#namee').show();}

    var productNameCode = $("#productNameCode").val();
    if(productNameCode){$('#productNameCodee').hide();}else{$('#productNameCodee').show();}
             
});

$('select').on('change', function () {
    var productGroupId = $("#productGroupId").val();
    if(productGroupId){$('#productGroupIde').hide();}else{$('#productGroupIde').show();}
    var productCategoryId = $("#productCategoryId").val();
    if(productCategoryId){$('#productCategoryIde').hide();}else{$('#productCategoryIde').show();}
    var productSubCategoryId = $("#productSubCategoryId").val();
    if(productSubCategoryId){$('#productSubCategoryIde').hide();}else{$('#productSubCategoryIde').show();}
    var productTypeId = $("#productTypeId").val();
    if(productTypeId){$('#productTypeIde').hide();}else{$('#productTypeIde').show();}
    });

$("#productNameCode").on('input',function () {
    this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
});


});
</script> 


{{-- Filtering --}}
<script type="text/javascript">
    $(document).ready(function() {
        /* Change Product Group*/
         $("#productGroupId").change(function(){
            var productGroupId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeGroup',
                data: {productGroupId:productGroupId,_token: csrf},
                dataType: 'json',
                success: function( _response ){

                     $("#productCategoryId").empty();
                    $("#productCategoryId").prepend('<option selected="selected" value="">Please Select Product Category</option>');


                    $("#productSubCategoryId").empty();
                    $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select Product Sub Category</option>');
                   

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
                           
                        }
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Product Group*/


         /* Change Product Category*/
         $("#productCategoryId").change(function(){
            var productCategoryId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeCategory',
                data: {productCategoryId:productCategoryId,_token: csrf},
                dataType: 'json',
                success: function( _response ){
                    
                    $("#productSubCategoryId").empty();
                    $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select Product Sub Category</option>');                   

                    $.each(_response, function (key, value) {
                        {
                            
                            if (key == "productSubCategoryList") {
                                $.each(value, function (key1,value1) {
                                    $('#productSubCategoryId').append("<option value='"+ value1+"'>"+key1+"</option>");
                                });
                            }
                           
                        }
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Product Category*/
    });
</script>
{{-- End Filtering --}}
 


