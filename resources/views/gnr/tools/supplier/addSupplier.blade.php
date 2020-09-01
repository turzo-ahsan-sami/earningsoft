@extends('layouts/gnr_layout')
@section('title', '| Home')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-1"></div>
            <div class="col-md-10 fullbody">
                <div class="viewTitle" style="border-bottom: 1px solid white;">
                    <a href="{{url('viewSupplier/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                    </i>Supplier List</a>
                </div>
                <div class="panel panel-default panel-border">
                        <div class="panel-heading">
                            <div class="panel-title">New Supplier</div>
                        </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                    {!! Form::open(array('url' => 'addSupplierItem', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                    <!-- <input type = "hidden" name = "_token" value = ""> -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('name', 'Name:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Supplier Name']) !!}
                                    <p id='namee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('supplierCompanyName', "Company Name:", ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('supplierCompanyName', $value = null, ['class' => 'form-control', 'id' => 'supplierCompanyName', 'type' => 'text', 'placeholder' => " Enter supplier's company name"]) !!}
                                    <p id='supplierCompanyNamee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('email', 'Email:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('email', $value = null, ['class' => 'form-control', 'id' => 'email', 'type' => 'text', 'placeholder' => 'Enter email address']) !!}
                                   <p id='emaile' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('mailForNotify', 'Emails for Notify:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::textarea('mailForNotify', $value = null, ['class' => 'form-control', 'id' => 'mailForNotify', 'rows' => 2, 'type' => 'text', 'placeholder' => 'Emails for Notify']) !!}
                                   <p id='mailForNotifye' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('phone', 'Mobile:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('phone', $value = null, ['class' => 'form-control', 'id' => 'phone', 'type' => 'text', 'placeholder' => 'Enter phone number']) !!}
                                    <p id='phonee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('address', 'Address:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::textarea('address', $value = null, ['class' => 'form-control', 'id' => 'address', 'rows' => 2, 'type' => 'text', 'placeholder' => 'Enter address']) !!}
                                    <p id='addresse' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('website', 'Website:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('website', $value = null, ['class' => 'form-control', 'id' => 'website', 'type' => 'text', 'placeholder' => 'Enter web address']) !!}
                                <p id='websitee' style="max-height:3px;"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('description', 'Description:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::textarea('description', $value = null, ['class' => 'form-control', 'id' => 'description', 'rows' => 2, 'type' => 'text', 'placeholder' => 'Enter description']) !!}
                                    <p id='descriptione' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('refNo', 'Reference No:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('refNo', $value = null, ['class' => 'form-control', 'id' => 'refNo', 'type' => 'text', 'placeholder' => 'Enter reference number']) !!}
                                    <p id='refNoe' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('attentionFirst', 'Attention 1:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::textarea('attentionFirst', $value = null, ['class' => 'form-control', 'id' => 'attentionFirst', 'rows' => 2, 'type' => 'text', 'placeholder' => 'Attention 1']) !!}
                                    <p id='attentionFirste' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('attentionSecond', 'Attention 2:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::textarea('attentionSecond', $value = null, ['class' => 'form-control', 'id' => 'attentionSecond', 'rows' => 2, 'type' => 'text', 'placeholder' => 'Attention 2']) !!}
                                    <p id='attentionSeconde' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('attentionThird', 'Attention 3:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::textarea('attentionThird', $value = null, ['class' => 'form-control', 'id' => 'attentionThird', 'rows' => 2, 'type' => 'text', 'placeholder' => 'Attention 3']) !!}
                                    <p id='attentionThirde' style="max-height:3px;"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="form-group">        
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-8 text-center">
                            {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                            @if(@$name == 'fromPurchaseF')
                               <a href="{{url('addInvPurchaseRequiF/')}}" class="btn btn-danger closeBtn">Back</a>
                            @else
                                <a href="{{url('viewSupplier/')}}" class="btn btn-danger closeBtn">Close</a>
                                <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                            @endif    
                            </div>
                            <div class="col-sm-2"></div>
                        </div>
                    {!! Form::close() !!}
                                <!-- </div>
                                <div class="col-md-4 emptySpace vert-offset-top-12"></div> -->
                            </div>
                        </div>
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
    $.ajax({
         type: 'post',
         url: './addSupplierItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
            if (_response.errors) {
            if (_response.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
                return false;
            }    
            if (_response.errors['supplierCompanyName']) {
                $('#supplierCompanyNamee').empty();
                $('#supplierCompanyNamee').append('<span class="errormsg" style="color:red;">'+_response.errors.supplierCompanyName+'</span>');
                return false;
            }
            if (_response.errors['email']) {
                $('#emaile').empty();
                $('#emaile').show();
                $('#emaile').append('<span class="errormsg" style="color:red;">'+_response.errors.email+'</span>');
                return false;
            }
            if (_response.errors['phone']) {
                $('#phonee').empty();
                $('#phonee').show();
                $('#phonee').append('<span class="errormsg" style="color:red;">'+_response.errors.phone+'</span>');
                return false;
            }
            if (_response.errors['refNo']) {
                $('#refNoe').empty();
                $('#refNoe').append('<span class="errormsg" style="color:red;">'+_response.errors.refNo+'</span>');
                return false;
            }
            if (_response.errors['attentionFirst']) {
                $('#attentionFirste').empty();
                $('#attentionFirste').append('<span class="errormsg" style="color:red;">'+_response.errors.attentionFirst+'</span>');
                return false;
            }
    } else {
            $("#name").val('');
            $("#supplierCompanyName").val('');
            $("#email").val('');
            $("#phone").val('');
            $("#addresse").val('');
            $("#website").val('');
            $("#mailForNotify").val('');
            $("#description").val('');
            $("#refNo").val('');
            $("#attentionFirst").val('');
            $("#attentionSecond").val('');
            $("#attentionThird").val('');
            $('.error').addClass("hidden");
            $('#success').text(_response.responseText);
            window.location.href = '{{url('viewSupplier/')}}';
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
             var supplierCompanyName = $("#supplierCompanyName").val();
            if(supplierCompanyName){$('#supplierCompanyNamee').hide();}else{$('#supplierCompanyNamee').show();}
             var email = $("#email").val();
            if(email){$('#emaile').hide();}else{$('#emaile').show();}
             var phone = $("#phone").val();
            if(phone){$('#phonee').hide();}else{$('#phonee').show();}
             var website = $("#website").val();
            if(website){$('#websitee').hide();}else{$('#websitee').show();}
            var refNo = $("#refNo").val();
            if(refNo){$('#refNoe').hide();}else{$('#refNoe').show();}
});
$("textarea").keyup(function(){
    var address = $("#address").val();
    if(address){$('#addresse').hide();}else{$('#addresse').show();}
    var mailForNotify = $("#mailForNotify").val();
    if(mailForNotify){$('#mailForNotifye').hide();}else{$('#mailForNotifye').show();}
    var description = $("#description").val();
    if(description){$('#descriptione').hide();}else{$('#descriptione').show();}
    var attentionFirst = $("#attentionFirst").val();
    if(attentionFirst){$('#attentionFirste').hide();}else{$('#attentionFirste').show();}
    var attentionSecond = $("#attentionSecond").val();
    if(attentionSecond){$('#attentionSeconde').hide();}else{$('#attentionSeconde').show();}
    var attentionThird = $("#attentionThird").val();
    if(attentionThird){$('#attentionThirde').hide();}else{$('#attentionThirde').show();}
});

});
</script> 


