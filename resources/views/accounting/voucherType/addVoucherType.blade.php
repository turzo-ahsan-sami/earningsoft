@extends('layouts/acc_layout')
@section('title', '| New Voucher Type')
@section('content')
<div class="row add-data-form"  style="padding-bottom: 1%">
    <div class="col-md-12">
    		<div class="col-md-2"></div>
    			<div class="col-md-8 fullbody">
    				<div class="viewTitle" style="border-bottom: 1px solid white;">
            			<a href="{{url('viewVoucherType/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
            			</i>Voucher Type List</a>
        			</div>
        		<div class="panel panel-default panel-border">
                				<div class="panel-heading">
                    				<div class="panel-title">Add Voucher Type</div>
                				</div>
                	<div class="panel-body">
                		<div class="row">
                			<div class="col-md-12">
                				<div class="col-md-8">

					                {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}

					                    <div class="form-group">
                                            {!! Form::label('name', 'Name:', ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-9">
                                                {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Voucher Name','autocomplete' => 'off']) !!}
                                                <p id='namee' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('titleName', 'Title Name:', ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-9">
                                                {!! Form::text('titleName', $value = null, ['class' => 'form-control', 'id' => 'titleName', 'type' => 'text', 'placeholder' => 'Enter Voucher Title Name','autocomplete' => 'off']) !!}
                                                <p id='titleNamee' style="max-height:3px;"></p>
                                            </div>
                                        </div>

                                        <div class="form-group">
					                        {!! Form::label('shortName', 'Short Name:', ['class' => 'col-sm-3 control-label']) !!}
					                        <div class="col-sm-9">
					                            {!! Form::text('shortName', $value = null, ['class' => 'form-control', 'id' => 'shortName', 'type' => 'text', 'placeholder' => 'Enter Voucher Short Name','autocomplete' => 'off']) !!}
					                            <p id='shortNamee' style="max-height:3px;"></p>
					                        </div>
					                    </div>   

					                    <div class="form-group">
					                        {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
					                        <div class="col-sm-9 text-right">
					                            {!! Form::submit('submit', ['id' => 'add', 'class' => 'btn btn-info']) !!}
                                                {{ Form::reset('Reset', ['class' => 'btn btn-warning']) }}
                                                <a href="{{url('viewAccountType/')}}" class="btn btn-danger closeBtn">Close</a>
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
         url: './addVoucherTypeItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
            // alert(JSON.stringify(_response));
            if (_response.errors) {
                if (_response.errors['name']) {
                    $('#namee').empty();
                    $('#namee').append('<span style="color:red;">'+_response.errors.name+'</span>');
                    return false;
                }
                if (_response.errors['titleName']) {
                    $('#titleNamee').empty();
                    $('#titleNamee').append('<span style="color:red;">'+_response.errors.titleName+'</span>');
                    return false;
                }
                if (_response.errors['shortName']) {
                    $('#shortNamee').empty();
                    $('#shortNamee').append('<span style="color:red;">'+_response.errors.shortName+'</span>');
                    return false;
                }

            }else {
                
                window.location.href = '{{url('viewVoucherType/')}}';
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

     var titleName = $("#titleName").val();
    if(titleName){$('#titleNamee').hide();}else{$('#titleNamee').show();}

     var shortName = $("#shortName").val();
    if(shortName){$('#shortNamee').hide();}else{$('#shortNamee').show();}
});


});
</script> 
 
