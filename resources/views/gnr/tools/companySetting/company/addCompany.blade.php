@extends('layouts/gnr_layout')
@section('title', '| New Company')

@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-2"></div>
            <div class="col-md-8 fullbody">
                <div class="viewTitle" style="border-bottom: 1px solid white;">
                    <a href="{{url('viewCompany/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                    </i>Company List</a>
                </div>
                <div class="panel panel-default panel-border">
                    <div class="panel-heading">
                        <div class="panel-title">New Company</div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">
                                {!! Form::open(array('url' => '', 'enctype' => 'multipart/form-data', 'role' => 'form',  'class'=>'form-horizontal form-groups')) !!}
                                    <!-- <input type = "hidden" name = "_token" value = ""> -->
                                    <div class="form-group">
                                        {!! Form::label('name', 'Company Name:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Company Name']) !!}
                                            <p id='namee' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    {{-- <div class="form-group">
                                        {!! Form::label('groupId', 'Group Name:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                       
                                            // $groupId = array('' => 'Please Select Group Name') + DB::table('gnr_group')->pluck('name','id')->all(); 
                                       
                                            {!! Form::select('groupId', ($groupId), null, array('class'=>'form-control', 'id' => 'groupId')) !!}
                                            <p id='groupIde' style="max-height:3px;"></p>
                                        </div>
                                    </div> --}}
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="business_type">Business type:</label>
                                        <div class="col-sm-8">
                                            {!! Form::select('business_type', ['' => 'Select business type'] + config('constants.business_type'), $value = null, ['class' => 'form-control', 'id' => 'business_type']) !!}
                                            <p id='business_typee' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="fy_type">Fiscal Year type:</label>
                                        <div class="col-sm-8">
                                            {!! Form::select('fy_type', ['' => 'Select fiscal year type'] + config('constants.fy_type'), $value = null, ['class' => 'form-control', 'id' => 'fy_type']) !!}
                                            <p id='fy_typee' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="stock_type">Stock calculation:</label>
                                        <div class="col-sm-8">
                                            {!! Form::select('stock_type', ['1' => 'Yes', '0' => 'No'], $value = null, ['class' => 'form-control', 'id' => 'stock_type']) !!}
                                            <p id='stock_typee' style="max-height:3px;"></p>
                                        </div>
                                    </div>

                                     <div class="form-group">
                                        <label class="col-sm-4 control-label" for="voucher_type_step">Voucher appproval step:</label>
                                        <div class="col-sm-8">
                                           <select class="form-control" name="voucher_type_step" id="voucher_type_step">
                                               <option value="">Select step</option>
                                               <option value="0">No step</option>
                                               <option value="1">Single step</option>
                                               <option value="2">Two steps</option>
                                               <option value="3">Three steps</option>
                                           </select>
                                            <p id='voucher_type_stepe' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('email', 'Email:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('email', $value = null, ['class' => 'form-control', 'id' => 'email', 'type' => 'text', 'placeholder' => 'Enter Company Email']) !!}
                                            <p id='emaile' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('phone', 'Mobile:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('phone', $value = null, ['class' => 'form-control', 'id' => 'phone', 'type' => 'text', 'placeholder' => 'Enter Company Phone']) !!}
                                            <p id='phonee' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('address', 'Address:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">                    
                                            {!! Form::textarea('address', $value = null, ['class' => 'form-control', 'id' => 'address', 'rows' => 3, 'placeholder' => 'Enter Address ']) !!}  
                                           <p id='addresse' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('website', 'Website:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('website', $value = null, ['class' => 'form-control', 'id' => 'website', 'type' => 'text', 'placeholder' => 'Enter Website']) !!}
                                            <p id='websitee' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('image', 'Upload Image:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::file('image', $value = null, ['class' => 'form-control', 'id' => 'image', 'type' => 'file']) !!}
                                            <p id='imagee' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('submit', ' ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                            <a href="{{url('viewCompany/')}}" class="btn btn-danger closeBtn">Close</a>
                                            <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                                        </div>
                                    </div>
                                {!! Form::close()  !!}
                            </div>
                         {{--    <div class="col-md-4 emptySpace vert-offset-top-9"><img src="images/image15.png" width="90%" height="" style="float:right"></div> --}}
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
    var formData = new FormData($(this)[0]);
    $.ajax({
         type: 'post',
         url: './addCompanyItem',
         data: formData,
         async: false,
         cache: false,
         contentType: false,
         processData: false,
         dataType: 'json',
        success: function( _response ){
           if (_response.errors) {
            if (_response.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
                return false;
            }
            if (_response.errors['business_type']) {
                $('#business_typee').empty();
                $('#business_typee').append('<span class="errormsg" style="color:red;">'+_response.errors.business_type+'</span>');
                return false;
            }
            if (_response.errors['fy_type']) {
                $('#fy_typee').empty();
                $('#fy_typee').append('<span class="errormsg" style="color:red;">'+_response.errors.fy_type+'</span>');
                return false;
            }
            if (_response.errors['voucher_type_step']) {
                $('#voucher_type_stepe').empty();
                $('#voucher_type_stepe').append('<span class="errormsg" style="color:red;">'+_response.errors.voucher_type_step+'</span>');
                return false;
                //alert('Please Select Voucher Type');
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
            if (_response.errors['address']) {
                $('#addresse').empty();
                $('#addresse').append('<span class="errormsg" style="color:red;">'+_response.errors.address+'</span>');
                return false;
            }
            if (_response.errors['website']) {
                $('#websitee').empty();
                $('#websitee').append('<span class="errormsg" style="color:red;">'+_response.errors.website+'</span>');
                return false;
            }
    } else {
            // $("#groupId").val('');
            $("#name").val('');
            $("#email").val('');
            $("#phone").val('');
            $("#address").val('');
            $("#voucher_type_step").val('');
            $("#website").val('');
            $("#image").val('');
            $('.error').addClass("hidden");
            $('#success').text(_response.responseText);
            window.location.href = '{{url('viewCompany/')}}';
            }
        },
        error: function( _response ){
            //alert("Select Image please");
        }
    });
});

$("input").keyup(function(){
            var name = $("#name").val();
            if(name){$('#namee').hide();}else{$('#namee').show();}
             var email = $("#email").val();
            if(email){$('#emaile').hide();}else{$('#emaile').show();}
             var phone = $("#phone").val();
            if(phone){$('#phonee').hide();}else{$('#phonee').show();}
              var voucher_type_step = $("#voucher_type_step").val();
            if(voucher_type_step){$('#voucher_type_stepe').hide();}else{$('#voucher_type_stepe').show();}
});
$("textarea").keyup(function(){
    var address = $("#address").val();
    if(address){$('#addresse').hide();}else{$('#addresse').show();}
});

// $('select').on('change', function (e) {
//     var groupId = $("#groupId").val();
//     if(groupId){$('#groupIde').hide();}else{$('#groupIde').show();}
// });

});
</script>