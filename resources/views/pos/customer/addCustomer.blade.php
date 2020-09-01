@extends('layouts/pos_layout')
@section('title', '| Add Customer')
@section('content')
    <?php
    $user = Auth::user();
    Session::put('branchId', $user->branchId);
    $gnrBranchId = Session::get('branchId');
    $logedUserName = $user->name;

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
                <a href="{{url('pos/customers/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Customer List</a>
            </div>

            @if(!$setting)
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <h3 align="center" style="font-family: Antiqua;letter-spacing: 2px">Set Voucher Configuration First</h3>
                </div>
            </div>
            @else
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">New Customer</div>
                </div>
                <div class="panel-body">

                    {!! Form::open(array('url' => '' , 'enctype' => 'multipart/form-data',  'role' => 'form', 'class'=>'form-horizontal form-groups','id'=>'customerForm')) !!}

                    <div class="row">
                        <div class="col-md-12">

                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('name', 'Name:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter name','autocomplite'=>'off']) !!}
                                        <p id='namee' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    {!! Form::label('code', 'Code:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('code', $value = null, ['class' => 'form-control', 'id' => 'code', 'type' => 'text', 'placeholder' => 'Enter code','autocomplite'=>'off']) !!}
                                        <p id='codee' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('mobile', 'Mobile:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('mobile', $value = null, ['class' => 'form-control', 'id' => 'mobile', 'type' => 'text', 'placeholder' => 'Enter mobile no','autocomplite'=>'off']) !!}
                                        <p id='mobilee' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                 <div class="form-group">
                                    {!! Form::label('email', 'Email:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('email', $value = null, ['class' => 'form-control', 'id' => 'email', 'type' => 'text', 'placeholder' => 'Enter email']) !!}
                                        <p id='emaile' style="max-height:3px;"></p>
                                    </div>
                                </div>
                              
                            </div>

                            <div class="col-md-6">
                               
                                 <div class="form-group">
                                    {!! Form::label('preAddress', 'Address:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">

                                        {!! Form::textarea('preAddress', $value = null, ['class' => 'form-control', 'id' => 'preAddress', 'type' => 'text', 'placeholder' => 'Enter Address','autocomplite'=>'off']) !!}
                                        <p id='preAddressee' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                 <div class="form-group">
                                    {!! Form::label('desccription', 'Description:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::textarea('cusDes', $value = null, ['class' => 'form-control', 'id' => 'cusDescription', 'type' => 'text', 'placeholder' => 'Enter Description','autocomplite'=>'off']) !!}
                                        <p id='descriptionee' style="max-height:3px;"></p>
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
                                <a href="{{url('pos/customers/')}}" class="btn btn-danger closeBtn">Close</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <span id="success" style="color:green; font-size:20px;"></span>
                        </div>
                    </div>
                    {!! Form::close() !!}

                </div>
            </div>
            @endif
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
        var name               = $("#name").val();
        var code               = $("#code").val();
        var email              = $("#email").val();
        var mobile             = $("#mobile").val();
        var preAddress         = $("#preAddress").val();
        var cusDescription     = $("#cusDescription").val();

        var formData = new FormData();

        formData.append('name',name);
        formData.append('code',code);
        formData.append('email',email);
        formData.append('mobile',mobile);
        formData.append('preAddress',preAddress);
        formData.append('cusDescription',cusDescription);

        $.ajax({
            type: 'post',
            url: './addCustomerItem',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function( _response){
            

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
                    if (_response.errors['email']) {
                        $('#emaile').empty();
                        $('#emaile').show();
                        $('#emaile').append('<span class="errormsg" style="color:red;">'+_response.errors.email+'</span>');
                    }
                    if (_response.errors['mobile']) {
                        $('#mobilee').empty();
                        $('#mobilee').show();
                        $('#mobilee').append('<span class="errormsg" style="color:red;">'+_response.errors.mobile+'</span>');
                    }

                    if (_response.errors['preAddress']) {
                        $('#preAddressee').empty();
                        $('#preAddressee').show();
                        $('#preAddressee').append('<span class="errormsg" style="color:red;">'+_response.errors.preAddress+'</span>');
                    } 
                    if (_response.errors['desccription']) {
                        $('#descriptionee').empty();
                        $('#descriptionee').show();
                        $('#descriptionee').append('<span class="errormsg" style="color:red;">'+_response.errors.desccription+'</span>');
                    }
                    

                }
                else {


                    $("#name").val('');
                    $("#code").val('');
                    $("#nid").val('');
                    $("#mobile").val('');
                    $("#email").val('');
                    $("#preAddress").val('');

                    // $("#costPrice").val('');
                    // $("#salesPrice").val('');

                    $('.error').addClass("hidden");
                    $('#success').text(_response.responseText);
                    window.location.href = '{{url('pos/addSale/')}}';
                }
            },
            error: function( _response ){
                
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
        var email = $("#email").val();
        if(email){$('#emaile').hide();}else{$('#emaile').show();}
        var mobile = $("#mobile").val();
        if(mobile){$('#mobilee').hide();}else{$('#mobilee').show();}
        var nid = $("#nid").val();
        if(nid){$('#nide').hide();}else{$('#nide').show();}

    });


});
/*Error Alert remove Ajax End*/
/* Category Change Start*/

</script>
@endsection
