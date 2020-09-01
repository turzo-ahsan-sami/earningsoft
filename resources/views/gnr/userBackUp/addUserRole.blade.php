@extends('layouts/gnr_layout')
@section('title', '| Home')
@section('content')
	<div class="row add-data-form" style="height:100%">
	    <div class="col-md-12">
            <div class="col-md-1"></div>
                <div class="col-md-10 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{ url('viewGnrUserRole/') }}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>User Role List</a>
                    </div>
	                <div class="panel panel-default panel-border">
                        <div class="panel-heading">
                            <div class="panel-title">New User Role</div>
                        </div>
	                    <div class="panel-body">
	                        <div class="row">
	                            
									{!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups')) !!}	             
										
				                        <div class="form-group">
				                            {!! Form::label('userId', 'User Name:', ['class' => 'col-sm-2 control-label']) !!}
				                            <div class="col-sm-4">
				                            <?php 
                                    			$userName = array('' => 'Select User Name') + DB::table('users')->pluck('name','id')->all(); 
                                			?>      
                                			{!! Form::select('userId', ($userName), null, array('class'=>'form-control', 'id' => 'userId')) !!}
				                                <p id='userIde' style="max-height:3px;"></p>
				                            </div>
				                        </div>
				                        <div class="form-group">
				                            {!! Form::label('roleId', 'Role:', ['class' => 'col-sm-2 control-label']) !!}
				                            <div class="col-sm-4">
				                            	<?php 
	                                                $roleId = array('' => 'Select') + DB::table('gnr_role')->pluck('name', 'id')->all(); 
	                                            ?>  
	                                            {!! Form::select('roleId', ($roleId), null, array('class'=>'form-control', 'id' => 'roleId')) !!}
	                                            <p id='roleIde' style="max-height:3px;"></p>
				                            </div>
				                        </div>
				                        <div class="form-group">
				                            {!! Form::label('functionalityId', 'Function:', ['class' => 'col-sm-2 control-label']) !!}
			                            	<div class="col-sm-10">
				                            	<div class="form-block">

				                            	<?php $i=1; ?>
					                            	@foreach(App\gnr\GnrFunctionality::all() as $functionality) 
					                            	
						                            	{!! Form::label(Illuminate\Support\Str::lower($functionality->functionName), $functionality->functionName) !!}
						                            	<br>
						                            	<div class="col-sm-12">
							                            	<p>
								                            	@foreach(App\gnr\GnrSubFunctionality::all() as $subFunctionality)
										                            <div class="col-sm-3">
											                            {!! Form::checkbox("functionalityId[][$i]", $subFunctionality->id, null, array('class' => 'cbr')) !!}
											                            {!! Form::label(Illuminate\Support\Str::lower($subFunctionality->subFunctionName), $subFunctionality->subFunctionName) !!}
										                            </div>
								                            	@endforeach
							                            	</p>
						                            	</div>
						                            	<?php $i++; ?>
					                            	@endforeach
				                            	</div>
			                            	</div>
				                        </div>
				                        
				                        <div class="form-group">
                                            <div class="col-sm-10 col-sm-offset-2">
                                                {!! Form::submit('Submit', ['id' => 'addUserRole', 'class' => 'btn btn-info']); !!}
                                                <a href="{{url('viewGnrUserRole/')}}" class="btn btn-danger closeBtn">Close</a>
                                                <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                                            </div>
                                        </div>
	                            	{!! Form::close() !!}
	                            
                        	</div>
                   	 	</div>
                	</div>
                <div class="footerTitle" style="border-top:1px solid white"></div>
	        </div>
	        <div class="col-md-1"></div>
	    </div>
	</div>
@endsection;

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function() {
	    $('form').submit(function( event ) {
	    	//var data = $('form').serializeArray();
		    event.preventDefault();
		    //alert($('form').serialize());
		    $.ajax({
				type: 'post',
				url: './addGnrUserRoleItem',
				data: $('form').serialize(),
				dataType: 'json',
		        success: function( _response ) {
		        	//alert(JSON.stringify(_response));
		    		if(_response.errors) {
			            if (_response.errors['userId']) {
			                $('#userIde').empty();
			                $('#userIde').append('<span class="errormsg" style="color:red;">'+_response.errors.userId+'</span>');  
			                return false;
			            }
			            if (_response.errors['roleId']) {
			                $('#roleIde').empty();
			                $('#roleIde').append('<span class="errormsg" style="color:red;">'+_response.errors.roleId+'</span>');  
			                return false;
			            }
			            if (_response.errors['functionalityId']) {
			            	alert('Checked checkbox');
			                return false;
			            } 
		    		} else {
			           
			            window.location.href = '{{ url('/viewGnrUserRole') }}';
		            }
		        },
		        error: function( _response ) {
		            // ERROR HANDLER
		            alert(_response.errors);
		        }
		    });
		});
$('select').on('change', function (e) {
    var userId = $("#userId").val();
    if(userId){$('#userIde').hide();}else{$('#userIde').show();}
     var roleId = $("#roleId").val();
    if(roleId){$('#roleIde').hide();}else{$('#roleIde').show();}
});






}); 
</script> 
