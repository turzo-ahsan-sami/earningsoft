@extends('layouts/pos_layout')
@section('title', '| Home')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-2"></div>
            <div class="col-md-8 fullbody">
                <div class="viewTitle" style="border-bottom: 1px solid white;">
                    <a href="{{url('pos/posViewProductBrand/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                    </i>Product Brand List</a>
                </div>
                <div class="panel panel-default panel-border">
                        <div class="panel-heading">
                            <div class="panel-title">New Product Brand</div>
                        </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">
                    {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                         
                        <div class="form-group">
                            {!! Form::label('name', 'Brand:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter product brand name']) !!}
                                <p id='namee' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('productGroupId', 'Group:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <?php 
                                    $ProductGroup = array('' => 'Please Select Prduct Group') + DB::table('pos_product_group')->pluck('name','id')->all(); 
                                ?>      
                                {!! Form::select('productGroupId', ($ProductGroup), null, array('class'=>'form-control', 'id' => 'productGroupId')) !!}
                                <p id='productGroupIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('productCategoryId', 'Category:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            <?php 
                                $productCategoryId = array('' => 'Please Select Prduct Category') + DB::table('pos_product_category')->pluck('name','id')->all(); 
                            ?>      
                            {!! Form::select('productCategoryId', ($productCategoryId), null, array('class'=>'form-control', 'id' => 'productCategoryId')) !!}
                                <p id='productCategoryIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('productSubCategoryId', 'Sub Catagory:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <?php 
                                    $productSubCategoryId = array('' => 'Please Select Prduct SubCategory') + DB::table('pos_product_sub_category')->pluck('name','id')->all(); 
                                ?>  
                                {!! Form::select('productSubCategoryId', ($productSubCategoryId), null, array('class'=>'form-control', 'id' => 'productSubCategoryId')) !!}
                                <p id='productSubCategoryIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                <a href="{{url('pos/posViewProductBrand/')}}" class="btn btn-danger closeBtn">Close</a>
                                <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                            </div>
                        </div>
                    {!! Form::close() !!}
                            </div>
                            <div class="col-md-4 emptySpace vert-offset-top-0"><img src="../images/catalog/image15.png" width="80%" height="" style="float:right">
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
         url: './addProductBrandItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
    if (_response.errors) {
            if (_response.errors['name']) {
                $('#namee').empty();
                $('#namee').show();
                $('#namee').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
                //return false;
            }
            if (_response.errors['productGroupId']) {
                $('#productGroupIde').empty();
                $('#productGroupIde').append('<span class="errormsg" style="color:red;">'+_response.errors.productGroupId+'</span>');
                //return false;
            }
            if (_response.errors['productCategoryId']) {
                $('#productCategoryIde').empty();
                $('#productCategoryIde').append('<span class="errormsg" style="color:red;">'+_response.errors.productCategoryId+'</span>');
                //return false;
            }
            if (_response.errors['productSubCategoryId']) {
                $('#productSubCategoryIde').empty();
                $('#productSubCategoryIde').append('<span class="errormsg" style="color:red;">'+_response.errors.productSubCategoryId+'</span>');
                //return false;
            }
    } else {
            $("#name").val('');
            $("#productGroupId").val('');
            $("#productCategoryId").val('');
            $("#productSubCategoryId").val('');
            $('.error').addClass("hidden");
            $('#success').text(_response.responseText);
            window.location.href = '{{url('pos/posViewProductBrand/')}}';
            }
        },
        error: function( _response ){
            // Handle error
            alert(_response.errors);
            
        }
    });
});

$("input").keyup(function(){
    var name = $("#name").val();
    if(name){$('#namee').hide();}else{$('#namee').show();}
             
    });
$('select').on('change', function (e) {
    var productGroupId = $("#productGroupId").val();
    if(productGroupId){$('#productGroupIde').hide();}else{$('#productGroupIde').show();}
    var productCategoryId = $("#productCategoryId").val();
    if(productCategoryId){$('#productCategoryIde').hide();}else{$('#productCategoryIde').show();}
    var productSubCategoryId = $("#productSubCategoryId").val();
    if(productSubCategoryId){$('#productSubCategoryIde').hide();}else{$('#productSubCategoryIde').show();}
    });

});

$(document).ready(function(){
        var productGroupId = $('#productGroupId').val();
        if(productGroupId ==''){$("#productCategoryId").prop("disabled", true);}
$("#productGroupId").change(function(){
        $("#productCategoryId").empty();
        $("#productCategoryId").prepend('<option selected="selected" value="">Please Select Product Category</option>');
        var productGroupId = $('#productGroupId').val();
        if(productGroupId!==''){$("#productCategoryId").prop("disabled", false);}else{$("#productCategoryId").prop("disabled", true);}
    $.ajax({
        type: 'post',
        url: './productGroupIdSend',
        data: $('form').serialize(),
        dataType: 'json',   
        success: function( _response ){
            $.each(_response, function( index, value ){
                    $('#productCategoryId').append("<option value='"+index+"'>"+value+"</option>");
                    //alert(value);
                });
                }
            });
        });
});

$(document).ready(function(){
        var productCategoryId = $('#productCategoryId').val();
        if(productCategoryId ==''){$("#productSubCategoryId").prop("disabled", true);}
$("#productCategoryId").change(function(){
        $("#productSubCategoryId").empty();
        $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select Product SubCategory</option>');
        var productCategoryId = $('#productCategoryId').val();
        if(productCategoryId!==''){$("#productSubCategoryId").prop("disabled", false);}else{$("#productSubCategoryId").prop("disabled", true);}
    $.ajax({
        type: 'post',
        url: './productCategoryIdSend',
        data: $('form').serialize(),
        dataType: 'json',   
        success: function( _response ){
            $.each(_response, function( index, value ){
                    $('#productSubCategoryId').append("<option value='"+index+"'>"+value+"</option>");
                    //alert(value);
                });
                }
            });
        });
}); 
</script> 



