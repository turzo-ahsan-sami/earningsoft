@extends('layouts/pos_layout')
@section('title', '| Client')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-2"></div>
            <div class="col-md-8 fullbody">
                <div class="viewTitle" style="border-bottom: 1px solid white;">
                    <a href="{{url('pos/posViewClient/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                    </i>Client List</a>
                </div>
                <div class="panel panel-default panel-border">
                    <div class="panel-heading">
                        <div class="panel-title">Client</div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">
                                    {!! Form::open(array('url' => 'addClient', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                    <div class="form-group">
                                        {!! Form::label('clientCompanyName', 'Company Name:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('clientCompanyName', $value = null, ['class' => 'form-control', 'id' => 'clientCompanyName', 'type' => 'text', 'placeholder' => 'Enter Client Company Name','autocomplete'=>'off']) !!}
                                            <p id='clientCompanyNamee' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('companyShortName', 'Short Name:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                             {!! Form::text('companyShortName', $value = null, ['class' => 'form-control', 'id' => 'companyShortName', 'type' => 'text', 'placeholder' => 'Enter Client Company Short Name','autocomplete'=>'off']) !!}
                                            <p id='companyShortNamee' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                
                                    <div class="form-group">
                                        {!! Form::label('clientContactPerson', 'Contact Person :', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('clientContactPerson', $value = null, ['class' => 'form-control', 'id' => 'clientContactPerson', 'type' => 'text', 'placeholder' => 'Enter Client Contact Person','autocomplete'=>'off']) !!}
                                            <p id='namee' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('clientContactPersonDesigntion', 'Desingnation :', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('clientContactPersonDesigntion', $value = null, ['class' => 'form-control', 'id' => 'clientContactPersonDesigntion', 'type' => 'text', 'placeholder' => 'Enter Client Contact Person','autocomplete'=>'off']) !!}
                                            <p id='namee' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('phone', 'Phone :', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('phone', $value = null, ['class' => 'form-control', 'id' => 'phone', 'type' => 'text', 'placeholder' => 'Enter Client Contact Person','autocomplete'=>'off']) !!}
                                            <p id='phonee' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('mobile', 'Mobile :', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('mobile', $value = null, ['class' => 'form-control', 'id' => 'mobile', 'type' => 'text', 'placeholder' => 'Enter Client Mobile No','autocomplete'=>'off']) !!}
                                            <p id='mobilee' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('email', 'Email :', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('email', $value = null, ['class' => 'form-control', 'id' => 'email', 'type' => 'email', 'placeholder' => 'Enter Client Email','autocomplete'=>'off']) !!}
                                            <p id='emaile' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('nationalId', 'National ID :', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('nationalId', $value = null, ['class' => 'form-control', 'id' => 'nationalId', 'type' => 'text', 'placeholder' => 'Enter Client National Id','autocomplete'=>'off']) !!}
                                            <p id='nationalIde' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('address', 'Address :', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('address', $value = null, ['class' => 'form-control', 'id' => 'address', 'type' => 'text', 'placeholder' => 'Enter Client Address','autocomplite'=>'off','autocomplete'=>'off']) !!}
                                            <p id='addresse' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('web', 'Web :', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('web', $value = null, ['class' => 'form-control', 'id' => 'web', 'type' => 'text', 'placeholder' => 'Enter Client Address','autocomplete'=>'off']) !!}
                                            <p id='webe' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                            <a href="{{url('pos/posViewClient/')}}" class="btn btn-danger closeBtn">Close</a>
                                            <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                                <div class="col-md-4 emptySpace vert-offset-top-0">
                                    <img src="../images/catalog/image15.png" width="60%" height="" style="float:right">
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
    $(document).ready(function() {
        $('#phone').on('input', function(event) {
             this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1'); 
        });
        $('#mobile').on('input', function(event) {
             this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1'); 
        });
        $('#nationalId').on('input', function(event) {
            this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1'); 
        });

    $('form').submit(function( event ) {
        event.preventDefault();
        $.ajax({
            type: 'post',
            url: './posAddClientItem',
            data: $('form').serialize(),
            dataType: 'json',
            success: function( _response ){
            if (_response.errors) {
                if (_response.errors['clientCompanyName']) {
                    $('#clientCompanyNamee').empty();
                    $('#clientCompanyNamee').append('<span class="errormsg" style="color:red;">'+_response.errors.clientCompanyName+'</span>');
                }
                if (_response.errors['clientContactPerson']) {
                    $('#namee').empty();
                    $('#namee').append('<span class="errormsg" style="color:red;">'+_response.errors.clientContactPerson+'</span>');
                }
                if (_response.errors['phone']) {
                    $('#phonee').empty();
                    $('#phonee').append('<span class="errormsg" style="color:red;">'+_response.errors.phone+'</span>');
                }
                 if (_response.errors['mobile']) {
                    $('#mobilee').empty();
                    $('#mobilee').append('<span class="errormsg" style="color:red;">'+_response.errors.mobile+'</span>');
                }
                if (_response.errors['email']) {
                    $('#emaile').empty();
                    $('#emaile').append('<span class="errormsg" style="color:red;">'+_response.errors.email+'</span>');
                }
                if (_response.errors['nationalId']) {
                    $('#nationalIde').empty();
                    $('#nationalIde').append('<span class="errormsg" style="color:red;">'+_response.errors.nationalId+'</span>');
                }
                if (_response.errors['address']) {
                    $('#addresse').empty();
                    $('#addresse').append('<span class="errormsg" style="color:red;">'+_response.errors.address+'</span>');
                }
                
                if (_response.errors['name']) {
                    $('#namee').empty();
                    $('#namee').show();
                    $('#namee').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
                }
           } 
            else {
                $('#success').text(_response.responseText);
                window.location.href = '{{url('pos/posViewClient/')}}';
             }
            },
            error: function( _response ){
                // Handle error
                alert(_response.errors);
                
            }
        });
    });
        $("input").keyup(function() {
            var name = $("#clientCompanyName").val();
            if(name){$('#clientCompanyNamee').hide();}else{$('#clientCompanyNamee').show();}
            var purson = $("#clientContactPerson").val();
            if(purson){$('#namee').hide();}else{$('#namee').show();}
            var phone = $("#phone").val();
            if(phone){$('#phonee').hide();}else{$('#phonee').show();}
            var mobile = $("#mobile").val();
            if(mobile){$('#mobilee').hide();}else{$('#mobilee').show();}
            var email = $("#email").val();
            if(email){$('#emaile').hide();}else{$('#emaile').show();}
            var nId = $("#nationalId").val();
            if(nId){$('#nationalIde').hide();}else{$('#nationalIde').show();}
            var address = $("#address").val();
            if(address){$('#addresse').hide();}else{$('#addresse').show();}
        });
    });
</script> 
 


