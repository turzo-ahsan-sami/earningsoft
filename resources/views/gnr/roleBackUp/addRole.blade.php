@extends('layouts/gnr_layout')
@section('title', '| Home')
@section('content')
	<div class="row add-data-form" style="height:100%">
        <div class="col-md-1"></div>
            <div class="col-md-10 fullbody">
                <div class="viewTitle" style="border-bottom: 1px solid white;">
                    <a href="{{ url('viewRoleList/') }}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                    </i>Role List</a>
                </div>
                <div class="panel panel-default panel-border">
                    <div class="panel-heading">
                        <div class="panel-title">New Role</div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
								{!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups')) !!}	             
									{{-- <input type="hidden" name="_token" value=""> --}}
			                        <div class="form-group">
			                            {!! Form::label('name', 'User Role:', ['class' => 'col-sm-2 control-label']) !!}
			                            <div class="col-sm-4">
			                                {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter role name']) !!}
			                                <p id='roleName' style="max-height:3px;"></p>
			                            </div>
			                        </div>
			                        <div class="row">
										<div class="col-md-12">
											
										</div>
									</div>
			                        <div class="form-group">
			                            {!! Form::label('name', 'Module:', ['class' => 'col-sm-2 control-label']) !!}
			                            <?php 
                                            $moduleList = array('' => 'Select') + DB::table('gnr_module')->pluck('name', 'id')->all();

                                            /*echo '<pre>';
                                            	print_r($moduleList);
                                            echo '</pre>';*/

                                        ?>  
			                            <div class="col-sm-10" id="erpModuleAccordionList" style="padding:0">
			                            	<ul class="nav nav-tabs nav-tabs-justified">
			                            	<?php $countTab = 1; ?>

												@foreach(App\gnr\GnrModule::all() as $moduleList)
													<li @if($countTab==1) class="active" @endif ><a href="#{{ Illuminate\Support\Str::lower(str_replace(array(' ', '&'), '', $moduleList->name)) }}" data-toggle="tab">
															<span class="visible-xs">
																<i class="fa-home"></i>
															</span>
															<span class="hidden-xs">
																{{  $moduleList->name }}
															</span>
														</a>
													</li>
													<?php $countTab++; ?>
												@endforeach
											</ul>
											<div class="tab-content">
												
													<div class="tab-pane active" id="inventory">
														<div class="col-sm-12" style="padding-bottom: 15px;">
							                            	<div class="form-block" style="padding: 0 12px;">
								                            	<?php
								                            		$functionalityLists = DB::table('gnr_functionality')->where('moduleIdFK', 1)->get();
								                            	?>	
									                            @foreach($functionalityLists as $functionality)
									                            	{!! Form::label(Illuminate\Support\Str::lower($functionality->functionName), $functionality->functionName) !!}
									                            	<br>
									                            	<div class="col-sm-12" style="padding-bottom: 10px;">
										                            	@foreach(App\gnr\GnrSubFunctionality::all() as $subFunctionality)
												                            <div class="col-sm-2">
													                            {!! Form::checkbox('functionalityId[]['.$functionality->id.']' , $subFunctionality->id, null, array('class' => 'cbr')) !!}
													                            {!! Form::label(Illuminate\Support\Str::lower($subFunctionality->subFunctionName), $subFunctionality->subFunctionName) !!}
												                            </div>
										                            	@endforeach
									                            	</div>
								                            	@endforeach
							                            	</div>
														</div>
													</div>
													<div class="tab-pane" id="fams">
														<div class="col-sm-12" style="padding-bottom: 15px;">
							                            	<div class="form-block" style="padding: 0 12px;">
								                            	<?php
								                            		$functionalityLists = DB::table('gnr_functionality')->where('moduleIdFK', 2)->get();
								                            	?>	
									                           @foreach($functionalityLists as $functionality)
									                            	{!! Form::label(Illuminate\Support\Str::lower($functionality->functionName), $functionality->functionName) !!}
									                            	<br>
									                            	<div class="col-sm-12" style="padding-bottom: 10px;">
										                            	@foreach(App\gnr\GnrSubFunctionality::all() as $subFunctionality)
												                            <div class="col-sm-2">
													                            {!! Form::checkbox('functionalityId[]['.$functionality->id.']' , $subFunctionality->id, null, array('class' => 'cbr')) !!}
													                            {!! Form::label(Illuminate\Support\Str::lower($subFunctionality->subFunctionName), $subFunctionality->subFunctionName) !!}
												                            </div>
										                            	@endforeach
									                            	</div>
								                            	@endforeach
							                            	</div>
														</div>
													</div>
													<div class="tab-pane" id="procurement">
														<div class="col-sm-12" style="padding-bottom: 15px;">
							                            	<div class="form-block" style="padding: 0 12px;">
								                            	<?php
								                            		$functionalityLists = DB::table('gnr_functionality')->where('moduleIdFK', 3)->get();
								                            	?>	
									                           @foreach($functionalityLists as $functionality)
									                            	{!! Form::label(Illuminate\Support\Str::lower($functionality->functionName), $functionality->functionName) !!}
									                            	<br>
									                            	<div class="col-sm-12" style="padding-bottom: 10px;">
										                            	@foreach(App\gnr\GnrSubFunctionality::all() as $subFunctionality)
												                            <div class="col-sm-2">
													                            {!! Form::checkbox('functionalityId[]['.$functionality->id.']' , $subFunctionality->id, null, array('class' => 'cbr')) !!}
													                            {!! Form::label(Illuminate\Support\Str::lower($subFunctionality->subFunctionName), $subFunctionality->subFunctionName) !!}
												                            </div>
										                            	@endforeach
									                            	</div>
								                            	@endforeach
							                            	</div>
						                            	</div>
													</div>
													<div class="tab-pane" id="accounting">
														<div class="col-sm-12" style="padding-bottom: 15px;">
							                            	<div class="form-block" style="padding: 0 12px;">
								                            	<?php
								                            		$functionalityLists = DB::table('gnr_functionality')->where('moduleIdFK', 4)->get();
								                            	?>	
									                           @foreach($functionalityLists as $functionality)
									                            	{!! Form::label(Illuminate\Support\Str::lower($functionality->functionName), $functionality->functionName) !!}
									                            	<br>
									                            	<div class="col-sm-12" style="padding-bottom: 10px;">
										                            	@foreach(App\gnr\GnrSubFunctionality::all() as $subFunctionality)
												                            <div class="col-sm-2">
													                            {!! Form::checkbox('functionalityId[]['.$functionality->id.']' , $subFunctionality->id, null, array('class' => 'cbr')) !!}
													                            {!! Form::label(Illuminate\Support\Str::lower($subFunctionality->subFunctionName), $subFunctionality->subFunctionName) !!}
												                            </div>
										                            	@endforeach
									                            	</div>
								                            	@endforeach
							                            	</div>
						                            	</div>
													</div>
													<div class="tab-pane" id="hrpayroll">
														<div class="col-sm-12" style="padding-bottom: 15px;">
							                            	<div class="form-block" style="padding: 0 12px;">
								                            	<?php
								                            		$functionalityLists = DB::table('gnr_functionality')->where('moduleIdFK', 5)->get();
								                            	?>	
									                           @foreach($functionalityLists as $functionality)
									                            	{!! Form::label(Illuminate\Support\Str::lower($functionality->name), $functionality->functionName) !!}
									                            	<br>
									                            	<div class="col-sm-12" style="padding-bottom: 10px;">
										                            	@foreach(App\gnr\GnrSubFunctionality::all() as $subFunctionality)
												                            <div class="col-sm-2">
													                            {!! Form::checkbox('functionalityId[]['.$functionality->id.']' , $subFunctionality->id, null, array('class' => 'cbr')) !!}
													                            {!! Form::label(Illuminate\Support\Str::lower($subFunctionality->subFunctionName), $subFunctionality->subFunctionName) !!}
												                            </div>
										                            	@endforeach
									                            	</div>
								                            	@endforeach
							                            	</div>
														</div>
													</div>
												


												<!-- <div class="tab-pane" id="fams">
													<p>Fulfilled direction use continual set him.</p>
												</div>
												<div class="tab-pane" id="procurement">
													<p>When be draw drew ye. Defective in do recommend.</p>
												</div>
												<div class="tab-pane" id="accounting">
													<p>Luckily friends do ashamed to do suppose.</p>
												</div>
												<div class="tab-pane" id="hr & payroll">
													<p>Carriage quitting securing be appetite it declared.</p>
												</div> -->
												<p id='moduleName' style="max-height:3px;"></p>
											</div>

			                            </div>

			                        </div>
			                        <!-- <div class="form-group">
			                            {!! Form::label('name', 'Function:', ['class' => 'col-sm-2 control-label']) !!}
		                            	<div class="col-sm-10">
			                            	<div class="form-block">
				                            	<?php $i=1; ?>
				                            	@foreach(App\gnr\GnrFunctionality::all() as $functionality)
					                            	{!! Form::label(Illuminate\Support\Str::lower($functionality->functionName), $functionality->functionName) !!}
					                            	<br>
					                            	<div class="col-sm-12">
						                            	<p>
							                            	@foreach(App\gnr\GnrSubFunctionality::all() as $subFunctionality)
									                            <div class="col-sm-2">
										                            {!! Form::checkbox('functionalityId[]['.$functionality->id.']' , $subFunctionality->id) !!}
										                            {!! Form::label(Illuminate\Support\Str::lower($subFunctionality->subFunctionName), $subFunctionality->subFunctionName) !!}
									                            </div>
							                            	@endforeach
						                            	</p>
					                            	</div>
					                            	<?php $i++; ?>
				                            	@endforeach
			                            	</div>
		                            	</div>
			                        </div> -->
			                        <div class="form-group">
			                            {!! Form::label('name', 'Description:', ['class' => 'col-sm-2 control-label']) !!}
			                            <div class="col-sm-4">
			                                {!! Form::text('description', $value = null, ['class' => 'form-control', 'id' => 'description', 'type' => 'text', 'placeholder' => 'Description']) !!}
			                                <p id='descriptione' style="max-height:3px;"></p>
			                            </div>
			                        </div>
			                        <div class="form-group">
                                        <div class="col-sm-10 col-sm-offset-2">
                                            {!! Form::submit('Submit', ['id' => 'addUserRole', 'class' => 'btn btn-info']); !!}
                                            <a href="{{url('viewRoleList/')}}" class="btn btn-danger closeBtn">Close</a>
                                            <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                                        </div>
                                    </div>
                            	{!! Form::close() !!}
                            </div>
                    	</div>
               	 	</div>
            	</div>
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-1"></div>
	</div>
@endsection;

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function() {
	    $('form').submit(function( event ) {
		    event.preventDefault();

		    $.ajax({
				type: 'post',
				url: './addRoleItem',
				data: $('form').serialize(),
				dataType: 'json',
		        success: function( _response ) {
		        	 //alert(JSON.stringify(_response));
		    		if(_response.errors) {
			            if(_response.errors['name']) {
			                $('#roleName').empty();
			                $('#roleName').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
			                
			            }
			            if(_response.errors['functionalityId']) {
			                $('#moduleName').empty();
			                $('#moduleName').append('<span class="errormsg" style="color:red;">Checkbox selection is mandatory</span>');
			                
			            }
			            if(_response.errors['description']) {
			                $('#descriptione').empty();
			                $('#descriptione').append('<span class="errormsg" style="color:red;">'+_response.errors.description+'</span>');
			            }
		    		} else{
			            window.location.href = '{{ url('viewRoleList/') }}';
		    		}
		        },
		        error: function( _response ) {
		            
		             alert(JSON.stringify(data));
		        }
		    }); 
		});

	$("input").keyup(function(){
    var name = $("#name").val();
    if(name){$('#roleName').hide();}else{$('#roleName').show();}

    var description = $("#description").val();
    if(description){$('#descriptione').hide();}else{$('#descriptione').show();}
     });

	$('.cbr').change(function() {
		var atLeastOneIsChecked = $('.cbr:checked').length; //alert(atLeastOneIsChecked);
		if(atLeastOneIsChecked>4){$('#moduleName').hide();}else{$('#moduleName').show();}
	})


	}); 
</script> 
