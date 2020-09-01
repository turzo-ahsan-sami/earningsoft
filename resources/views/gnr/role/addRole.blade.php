@extends('layouts/gnr_layout')
@section('title', '| Role')
@section('content')
<div class="row add-data-form">
	<div class="col-md-12">
		<div class="col-md-1"></div>
		<div class="col-md-10 fullbody">
			<div class="viewTitle" style="border-bottom: 1px solid white;">
				<a href="{{url('viewRoleList/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
				</i>Role List</a>
			</div>
			<div class="panel panel-default panel-border">
				<div class="panel-heading">
					<div class="panel-title">New Role</div>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">

							{!! Form::open(['url'=>'','class'=>'form-horizontal form-group']) !!}
							
							<div class="form-group">
								{!! Form::label('name', 'Role Name:', ['class' => 'col-sm-2 control-label']) !!}
								<div class="col-sm-4">
									{!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Role Name']) !!}
								</div>
							</div>

							<div class="form-group">
								{!! Form::label('module', 'Modules:', ['class' => 'col-sm-2 control-label']) !!}

								<div class="col-sm-10">

									@php
									$modules = DB::table('gnr_module')->select('id','name','code')->get();
									$subFunctions = DB::table('gnr_sub_function')->select('id','subfunctionName')->get();
									@endphp
									

									{{-- /////////// --}}
									<ul id="navTabs" class="nav nav-tabs">
										@foreach ($modules as $key => $module1)
										<li class="@if($key==0){{"active"}}@endif">
											<a href="#{{$module1->code}}" data-toggle="tab" aria-expanded="@if($key==0){{"true"}}@else{{"false"}}@endif">
												<span class="visible-xs"><i class="fa-home"></i></span>
												<span class="hidden-xs">{{$module1->name}}</span>
											</a>
										</li>
										@endforeach
									</ul>

									<div class="tab-content">

										@foreach ($modules as $index => $module2)
										<div class="tab-pane @if($index==0){{"active"}}@endif" id="{{$module2->code}}">
											@php
											$functions = DB::table('gnr_function')->where('moduleIdFK',$module2->id)->select('id','name')->orderBy('name')->get();
											@endphp

											{{-- Print Functions --}}
											<div>
												
												@foreach($functions as $function)
												<div class="functionDiv">
													{!! Form::label($function->name,$function->name,['class'=>'textBold']) !!} &nbsp;&nbsp;&nbsp;
													{!! Form::checkbox('checkAll', null, false,['class'=>'checkAll']) !!}
													{!! Form::label($function->name,'Check/Uncheck All') !!} <br>
													<div class="form-group" style="padding-left: 20px;">
														@foreach($subFunctions as $subFunction)
														{!! Form::checkbox($function->name, $subFunction->id, false,['moduleId'=>$module2->id,'functionId'=>$function->id,'class'=>'checkItem']) !!} 
														{!! Form::label($subFunction->subfunctionName,$subFunction->subfunctionName) !!} 
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														@endforeach
													</div>
													<br>
												</div>
												@endforeach
											</div>
											{{-- End Print Functions --}}

										</div>
										@endforeach
									</div>
								</div>
							</div> {{-- form-group --}}
							{{-- ///////////// --}}

							<div class="form-group">
								{!! Form::label('description', 'Description:', ['class' => 'col-sm-2 control-label']) !!}
								<div class="col-sm-8">
									{!! Form::textArea('description', $value = null, ['class' => 'form-control', 'id' => 'description','rows'=>'2', 'placeholder' => 'Enter Description']) !!}
								</div>
							</div>

							<div class="form-group pull-right" style="padding-right: 17%;">
								
								<div class="col-sm-12">
									{!! Form::button('Submit', ['id' => 'submitButton', 'class' => 'btn btn-info']) !!}
									<a href="#" class="btn btn-danger closeBtn">Close</a>
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
</div>

<style type="text/css">
.textBold{
	font-weight: bold;
	font-size: 15px;
}
</style>

<script type="text/javascript">
	$(document).ready(function() {
		$("#submitButton").click(function(event) {
			$(".error").remove();
			var moduleIds = new Array();		
			var functionIds = new Array();		
			var subFunctionIds = new Array();		
			$(".checkItem:checked").each(function(index, el) {

				moduleIds.push($(el).attr('moduleId'));
				functionIds.push($(el).attr('functionId'));
				subFunctionIds.push($(el).val());
			});

			var name = $("#name").val();
			var description = $("#description").val();
			var csrf = "{{csrf_token()}}";

			$.ajax({
				url: './addRoleItem',
				type: 'POST',
				dataType: 'json',
				data: {name: name,description: description, moduleIds: moduleIds, functionIds: functionIds, subFunctionIds: subFunctionIds, _token: csrf},
			})
			.done(function(data) {
				// Print Error
				if(data.errors) {
					$.each(data.errors, function(name, error) {							
						$("#"+name).after("<p class='error' style='color:red;'>* "+data.errors[name]+"</p>");
					});
				}
				else{
					location.href = "viewRoleList";
				}
				
			})
			.fail(function() {
				aler('Response Error');
				console.log("error");
			})
			.always(function() {
				console.log("complete");
			});
			

			
			
		});

		// Hide Eddor
		$(document).on('input', 'input', function() {
			$(this).closest('div').find('.error').remove();
		});
		$(document).on('change', 'select', function() {
			$(this).closest('div').find('.error').remove();
		});


		/*Check/Uncheck All*/
		$(document).on('click', '.checkAll', function(event) {
			if($(this).is(":checked")){				
				$(this).closest('div').find('.checkItem').prop('checked',true);
			}
			else{
				$(this).closest('div').find('.checkItem').prop('checked',false);
			}
		});
		/*End Check/Uncheck All*/

		/*If all checked then make the all check true */
		$(document).on('click', '.checkItem', function(event) {
			var numberOfSubFuntions = $(this).closest('div').find('.checkItem').length;
			var numberOfCheckedSubFuntions = $(this).closest('div').find('.checkItem:checked').length;
			
			if(numberOfSubFuntions == numberOfCheckedSubFuntions){				
				$(this).closest('.functionDiv').find('.checkAll').prop('checked',true);
			}
			else{				
				$(this).closest('.functionDiv').find('.checkAll').prop('checked',false);
			}
		});
		/*end If all checked then make the all check true */

		$(document).on('change', '.checkItem', function(event) {
			
			if ($(this).is(":checked")){
				$("#navTabs").closest('div').find('.error').remove();
			}			
		});
		

	});
</script>
@endsection

