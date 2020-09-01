@extends('layouts/fams_layout')
@section('title', '| Model')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-2"></div>
            <div class="col-md-8 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('viewFamsPmodel/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Product Model List</a>
                    </div>
                <div class="panel panel-default panel-border">
                        <div class="panel-heading">
                            <div class="panel-title">Product Model</div>
                        </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">
                                {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                    <!-- <input type = "hidden" name = "_token" value = ""> -->
                                    <div class="form-group">
                                        {!! Form::label('productBrandId', 'Brand:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                        <?php 
                                            $productBrandId = array('' => 'Please Select Prduct Brand') + DB::table('fams_product_brand')->pluck('name','id')->all(); 
                                        ?>
                                            {!! Form::select('productBrandId', ($productBrandId), null, array('class'=>'form-control', 'id' => 'productBrandId')) !!}
                                            <p id='productBrandIde' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        {!! Form::label('name', 'Model:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter product model name']) !!}
                                            <p id='namee' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group hidden">
                                        {!! Form::label('modelCode', 'Model Code:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('modelCode', $value = null, ['class' => 'form-control', 'id' => 'modelCode', 'type' => 'text', 'placeholder' => 'Enter product model code']) !!}
                                            <p id='modelCodee' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9 text-right">
                                            {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                            <a href="{{url('viewFamsPmodel/')}}" class="btn btn-danger closeBtn">Close</a>
                                             <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                                        </div>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                            <div class="col-md-4 emptySpace vert-offset-top-2"><img src="images/catalog/image15.png" width="80%" height="" style="float:right">
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
         url: './addFamsPmodelItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
            if (_response.accessDenied) {
                showAccessDeniedMessage();
                return false;
            }
    if (_response.errors) {
            if (_response.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
                return false;
            }
            if (_response.errors['productBrandId']) {
                $('#productBrandIde').empty();
                $('#productBrandIde').append('<span class="errormsg" style="color:red;">'+_response.errors.productBrandId+'</span>');
                return false;
            }
    } else {
            $("#name").val('');
            $("#productGroupId").val('');
            $("#productCategoryId").val('');
            $("#productSubCategoryId").val('');
            $("#productBrandId").val('');
            $('.error').addClass("hidden");
            $('#success').text(_response.responseText);
            window.location.href = '{{url('viewFamsPmodel/')}}';
            }
        },
        error: function( _response ){
            // Handle error
            alert('_response.errors');
            
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
    var productBrandId = $("#productBrandId").val();
    if(productBrandId){$('#productBrandIde').hide();}else{$('#productBrandIde').show();}
    });

});


</script> 

