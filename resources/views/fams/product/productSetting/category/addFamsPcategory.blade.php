@extends('layouts/fams_layout')
@section('title', '| Category')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
            <div class="col-md-2"></div>
                <div class="col-md-8 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('viewFramsPctg/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Product Category List</a>
                    </div>
                <div class="panel panel-default panel-border">
                                <div class="panel-heading">
                                    <div class="panel-title">Product Category</div>
                                </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">
                            {!! Form::open(array('url' => 'addProductCategoryItem', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                 
                                <div class="form-group">
                                    {!! Form::label('productGroupId', 'Group:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <?php 
                                        $ProductGroup = array('' => 'Please Select Prduct Group') + DB::table('fams_product_group')->pluck('name','id')->all(); 
                                        ?>      
                                        {!! Form::select('productGroupId', ($ProductGroup), null, array('class'=>'form-control', 'id' => 'productGroupId')) !!}
                                        <p id='productGroupIde' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('name', 'Category:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter product category name']) !!}
                                        <p id='namee' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('categoryCode', 'Category Code:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('categoryCode', $value = null, ['class' => 'form-control', 'id' => 'categoryCode', 'type' => 'text', 'placeholder' => 'Enter product category code']) !!}
                                        <p id='categoryCodee' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9 text-right">
                                        {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                        <a href="{{url('viewFramsPctg/')}}" class="btn btn-danger closeBtn">Close</a>
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
         url: './addFamsPctgItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
            if (_response.accessDenied) {
                showAccessDeniedMessage();
                return false;
            }
            //alert(JSON.stringify(_response));
    if (_response.errors) {
            if (_response.errors['productGroupId']) {
                $('#productGroupIde').empty();
                $('#productGroupIde').append('<span class="errormsg" style="color:red;">'+_response.errors.productGroupId+'</span>');
                
            }
            if (_response.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
                
            }
            if (_response.errors['categoryCode']) {
                $('#categoryCodee').empty();
                $('#categoryCodee').append('<span class="errormsg" style="color:red;">'+_response.errors.categoryCode+'</span>');
                return false;
            }
    } else {
            $("#name").val('');
            $("#categoryCode").val('');
            $("#productGroupId").val('');
            $('.error').addClass("hidden");
            //$('#success').text(_response.responseText);
            window.location.href = '{{url('viewFramsPctg/')}}';
            }
        },
        error: function( _response ){
            // Handle error
            alert("error");
            
        }
    });
});

$("input").keyup(function(){
   /* var name = $("#name").val();
    if(name){$('#namee').hide();}else{$('#namee').show();}
*/
    /* var categoryCode = $("#categoryCode").val();
    if(categoryCode){$('#categoryCodee').hide();}else{$('#categoryCodee').show();}*/
             
    });
$('select').on('change', function (e) {
    var productGroupId = $("#productGroupId").val();
    if(productGroupId){$('#productGroupIde').hide();}else{$('#productGroupIde').show();}
    });

$("#categoryCode").on('input', function() {    
    this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
});

});
</script> 
 


