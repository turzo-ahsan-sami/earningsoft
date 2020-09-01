@extends('layouts/gnr_layout')
@section('title', '| New Group')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
    		<div class="col-md-2"></div>
    			<div class="col-md-8 fullbody">
    				<div class="viewTitle" style="border-bottom: 1px solid white;">
            			<a href="{{url('viewGroup/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
            			</i>Group List</a>
        			</div>
        		<div class="panel panel-default panel-border">
                				<div class="panel-heading">
                    				<div class="panel-title">New Group</div>
                				</div>
                	<div class="panel-body">
                		<div class="row">
                			<div class="col-md-12">
                				<div class="col-md-8">
					                {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
					                    <!-- <input type = "hidden" name = "_token" value = ""> -->
					                    <div class="form-group">
					                        {!! Form::label('name', 'Group Name:', ['class' => 'col-sm-3 control-label']) !!}
					                        <div class="col-sm-9">
					                            {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Group Name']) !!}
					                            <p id='namee' style="max-height:3px;"></p>
					                        </div>
					                    </div>
					                    <div class="form-group">
					                        {!! Form::label('email', 'Email:', ['class' => 'col-sm-3 control-label']) !!}
					                        <div class="col-sm-9">
					                            {!! Form::text('email', $value = null, ['class' => 'form-control', 'id' => 'email', 'type' => 'text', 'placeholder' => 'Enter Group Email']) !!}
					                            <p id="emaile" style="max-height:3px;"></p>
					                        </div>
					                    </div>
					                    <div class="form-group">
					                        {!! Form::label('phone', 'Mobile:', ['class' => 'col-sm-3 control-label']) !!}
					                        <div class="col-sm-9">
					                            {!! Form::text('phone', $value = null, ['class' => 'form-control', 'id' => 'phone', 'type' => 'text', 'placeholder' => 'Enter Group Phone']) !!}
					                            <p id='phonee' style="max-height:3px;"></p>
					                        </div>
					                    </div>
					                    <div class="form-group">
					                        {!! Form::label('address', 'Address:', ['class' => 'col-sm-3 control-label']) !!}
					                        <div class="col-sm-9">                    
					                            {!! Form::textarea('address', $value = null, ['class' => 'form-control textarea', 'id' => 'address', 'rows' => 3, 'placeholder' => 'Enter Address', 'type' => 'textarea']) !!}  
					                            <p id='addresse' style="max-height:3px;"></p>
					                        </div>
					                    </div>
					                    <div class="form-group">
					                        {!! Form::label('website', 'Website:', ['class' => 'col-sm-3 control-label']) !!}
					                        <div class="col-sm-9">
					                            {!! Form::text('website', $value = null, ['class' => 'form-control', 'id' => 'website', 'type' => 'text', 'placeholder' => 'Enter Website']) !!}
					                            <p id='websitee' style="max-height:3px;"></p>
					                        </div>
					                    </div>
					                    <div class="form-group">
					                        {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
					                        <div class="col-sm-9">
					                            {!! Form::submit('submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                                <a href="{{url('viewGroup/')}}" class="btn btn-danger closeBtn">Close</a>
					                            <span id="success" style="color:green; font-size:16px;" class="pull-right"></span>
					                        </div>
					                    </div>
					                {!! Form::close()  !!}
                    			 </div>
		                    	<div class="col-md-4 emptySpace vert-offset-top-4"><img src="images/image15.png" width="90%" height="" style="float:right"></div>
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
         url: './addItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
    if (_response.errors) {
            if (_response.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
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
            $("#name").val('');
            $("#email").val('');
            $("#phone").val('');
            $("#address").val('');
            $("#website").val('');
            $('.error').addClass("hidden");
            $('#success').text(_response.responseText);
            window.location.href = '{{url('viewGroup/')}}';
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
             var email = $("#email").val();
            if(email){$('#emaile').hide();}else{$('#emaile').show();}
             var phone = $("#phone").val();
            if(phone){$('#phonee').hide();}else{$('#phonee').show();}
             var website = $("#website").val();
            if(website){$('#websitee').hide();}else{$('#websitee').show();}
});
$("textarea").keyup(function(){
    var address = $("#address").val();
    if(address){$('#addresse').hide();}else{$('#addresse').show();}
});

});
</script> 
 
