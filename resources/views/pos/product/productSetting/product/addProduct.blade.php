@extends('layouts/pos_layout')
@section('title', '| Home')
@section('content')
    <?php
    $user = Auth::user();
    Session::put('branchId', $user->branchId);
    $gnrBranchId = Session::get('branchId');
    $logedUserName = $user->name;
    $company = Auth::user()->company;
    $units = config('constants.product_units');

    ?>
    <style type="text/css">
    .select2-results__option[aria-selected=true] {
        display: none;
    }
</style>
<div class="row add-data-form" style="height:100%">
    <div class="col-md-12">
        <div class="col-md-1"></div>
        <div class="col-md-10 fullbody">
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                <a href="{{url('pos/posViewProduct/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
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
                                        {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter product name','autocomplite'=>'off']) !!}
                                        <p id='namee' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('code', 'Code:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('code', $value = null, ['class' => 'form-control', 'id' => 'code', 'type' => 'text', 'placeholder' => 'Enter product code','autocomplite'=>'off']) !!}
                                        <p id='codee' style="max-height:3px;"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- <div class="form-group">
                                    {!! Form::label('costPrice', 'Cost Price:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('costPrice', $value = null, ['class' => 'form-control', 'id' => 'costPrice', 'type' => 'text', 'placeholder' => 'Enter cost price']) !!}
                                        <p id='costPricee' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('salesPrice', 'Sales Price:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('salesPrice', $value = null, ['class' => 'form-control', 'id' => 'salesPrice', 'type' => 'text', 'placeholder' => 'Enter sales price']) !!}
                                        <p id='salesPricee' style="max-height:3px;"></p>
                                    </div>
                                </div> -->

                                <div class="form-group">
                                    {!! Form::label('Type', 'Type:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <select class="form-control" id="type" name="type">
                                            <option value="0">Select Type</option>
                                            <option value="product">Proudct</option>
                                            <option value="hour">Hour</option>
                                            @if ($company->business_type == 'manufacture' && $company->stock_type == 1)
                                                <option value="raw">Raw Material</option>
                                            @endif
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

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="form-group col-md-4 text-center">
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-12">
                                {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                <a href="{{url('pos/posViewProduct/')}}" class="btn btn-danger closeBtn">Close</a>
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


<script type="text/javascript">

/*Data Insert Start*/
$(document).ready(function(){
    /*Multi Select Start */

    /*Multi Select End*/
    $('#costPrice').on('input', function(event) {
        this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');
    });
    $('#salesPrice').on('input', function(event) {
        this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');
    });
    $('form').submit(function( event ) {
        event.preventDefault();
        var formData = new FormData($(this)[0]);
        $.ajax({
            type: 'post',
            url: './posAddProductItem',
            data: $('form').serialize(),
            dataType: 'json',
            success: function( _response ){

                $('#namee').empty();
                $('#codee').empty();
                $('#typeMsg').empty();
                $('#unitMsg').empty();

                if (_response.errors) {
                    if (_response.errors['name']) {
                        $('#namee').empty();
                        $('#namee').show();
                        $('#namee').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
                    }
                    if (_response.errors['code']) {
                        $('#codee').empty();
                        $('#codee').show();
                        $('#codee').append('<span class="errormsg" style="color:red;">'+_response.errors.code+'</span>');
                    }

                    // if (_response.errors['costPrice']) {
                    //     $('#costPricee').empty();
                    //     $('#costPricee').append('<span class="errormsg" style="color:red;">'+_response.errors.costPrice+'</span>');
                    // }

                    // if (_response.errors['salesPrice']) {
                    //     $('#salesPricee').empty();
                    //     $('#salesPricee').append('<span class="errormsg" style="color:red;">'+_response.errors.salesPrice+'</span>');
                    // }

                    if (_response.errors['type']) {
                        $('#typeMsg').empty();
                        $('#typeMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.type+'</span>');
                    }

                    if (_response.errors['unit']) {
                        $('#unitMsg').empty();
                        $('#unitMsg').show();
                        $('#unitMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.unit+'</span>');
                    }
                }
                else {

                    $("#name").val('');
                    $("#code").val('');

                    // $("#costPrice").val('');
                    // $("#salesPrice").val('');

                    $('.error').addClass("hidden");
                    $('#success').text(_response.responseText);
                    window.location.href = '{{url('pos/posViewProduct/')}}';
                }
            },
            error: function( _response ){
                // Handle error
                //alert(_response.errors);
                //alert("Select Image please");
            }

        });
    });
    /*Data Insert End */
    /*Error Alert remove Ajax Start*/
    $("input").keyup(function(){
        var name = $("#name").val();
        if(name){$('#namee').hide();}else{$('#namee').show();}
        var code = $("#code").val();
        if(code){$('#codee').hide();}else{$('#codee').show();}
        var costPrice = $("#costPrice").val();
        if(costPrice){$('#costPricee').hide();}else{$('#costPricee').show();}
        var salesPrice = $("#salesPrice").val();
        if(salesPrice){$('#salesPricee').hide();}else{$('#salesPricee').show();}
    });


});
/*Error Alert remove Ajax End*/
/* Category Change Start*/

</script>
@endsection
