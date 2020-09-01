@extends('layouts/gnr_layout')
@section('title', '| User Role')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-1"></div>
        <div class="col-md-10 fullbody">
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                <a href="{{url('viewGnrUserRole/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>User Role List</a>
            </div>
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">New User Role</div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                        @php
                        	$assingedUsers = DB::table('gnr_user_role')->pluck('userIdFK')->toArray();
                        	$users = DB::table('users as t1')
                        					->join('hr_emp_general_info as t2','t1.emp_id_fk','t2.id')
                        					->whereNotIn('t1.id',$assingedUsers)
                        					->select('t1.id as id','t2.emp_id as emp_id','t2.emp_name_english as emp_name_english')
                        					->orderBy('emp_id')
                        					->get();

                        	/*$users = DB::table('hr_emp_general_info')->where('status',1)->whereIn('emp_id_fk',$userList)->select('id','emp_id','emp_name_english')->get();*/

                        	$roles = array(''=>'Select Role') + DB::table('gnr_role')->where('id','>=',2)->pluck('name','id')->toArray();
                        	$modules = DB::table('gnr_module')->select('id','name','code')->get();
                        	$subFunctions = DB::table('gnr_sub_function')->select('id','subfunctionName')->get();
                        @endphp

                        {!! Form::open(['url'=>'','class'=>'form-horizontal form-group']) !!}

                        	<div class="form-group">
                        		{!! Form::label('userId', 'User:', ['class' => 'col-sm-2 control-label']) !!}
                        		<div class="col-sm-4">
		                        	<select id="userId" name="userId" class="form-control">
		                        		<option value="">Select User</option>
		                        		@foreach ($users as $user)
		                        			<option value="{{$user->id}}">{{$user->emp_id.'-'.$user->emp_name_english}}</option>
		                        		@endforeach
		                        	</select>
                        		</div>
                        	</div>

                        	<div class="form-group">
                        		{!! Form::label('roleId', 'Role:', ['class' => 'col-sm-2 control-label']) !!}
                        		<div class="col-sm-4">
		                        	{!! Form::select('roleId',$roles,null,['id'=>'roleId','class'=>'form-control']) !!}
                        		</div>
                        	</div>
                            
                           

	                        <div class="form-group">
	                        	{!! Form::label('module', 'Modules:', ['class' => 'col-sm-2 control-label']) !!}

	                        	<div class="col-sm-10">

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
		                        			{!! Form::label($function->name,$function->name) !!} <br>	                        			
		                        			<div class="form-group" style="padding-left: 20px;">
		                        			@foreach($subFunctions as $subFunction)
		                        				<span class="checkboxSpan">
		                        				{!! Form::checkbox($function->name, $subFunction->id, false,['moduleId'=>$module2->id,'functionId'=>$function->id]) !!} 
		                        				{!! Form::label($subFunction->subfunctionName,$subFunction->subfunctionName) !!}
		                        				</span>
		                        				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                        			@endforeach
		                        			</div>
		                        			<br>
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


{{-- Valid Till Modal --}}
        <div id="validTillModal" class="modal fade" style="margin-top:3%" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Validation Period</h4>
                    </div>
                    <div class="modal-body">
                    	<div class="row">
                    		<div class="form-horizontal form-group" style="padding-left: 20px;padding-right: 20px;">
                    			<div class="form-group">
		                            {!! Form::label('validTill', 'Valid Till:', ['class' => 'col-sm-2 control-label']) !!}
		                            <div class="col-sm-3">
		                                {!! Form::radio('validType', '1', true) !!}
		                                {!! Form::label('', 'Life Time') !!}
		                            </div>
		                            <div class="col-sm-2">
		                                {!! Form::radio('validType', '2', false) !!}
		                                {!! Form::label('', 'Limited Time') !!}
		                            </div>
		                            <div class="col-sm-3">
		                                {!! Form::text('validTillDate', $value = null, ['class' => 'form-control', 'id' => 'validTillDate','placeholder' => 'Enter Valadation Date','readonly']) !!}
		                            </div>
		                            <div class="col-sm-2">
		                                {!! Form::text('validTillTime', $value = null, ['class' => 'form-control', 'id' => 'validTillTime','placeholder' => 'Enter Valadation Time','readonly']) !!}
		                            </div>
		                        </div>
                    		</div>
                    	</div>
                        

                        <div class="modal-footer">
                            
                            <input type="hidden" name="actionTracker" id="actionTracker" moduleId="" functionId="" subFunctionId="">
                            
                            <button id="modalSubmit" type="submit" class="btn actionBtn glyphicon glyphicon-check btn-success"><span > Confirm</button>
                            <button id="modalClose" class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
                            

                        </div>

                    </div>
                </div>
            </div>
        </div>
{{-- Valid Till Modal --}}

<script type="text/javascript">
	$(document).ready(function() {
	

		// Hide Eddor
		$(document).on('input', 'input', function() {
			$(this).closest('div').find('.error').remove();
		});
		$(document).on('change', 'select', function() {
			$(this).closest('div').find('.error').remove();
		});

		/*make the selected action on role select*/
		$("#roleId").change(function(event) {
			var roleId = $(this).val();
			$("input:checkbox").prop('checked',false);
			$(".checkboxSpan").find('label').css('color','black');
			$("input:checkbox").unbind("click");
			
			if(roleId!=''){

				var csrf = "{{csrf_token()}}";

				$.ajax({
					url: './getGnrRoleInfo',
					type: 'POST',
					dataType: 'json',
					data: {roleId: roleId, _token: csrf},
				})
				.done(function(data) {
					
					var functionalityArray = data['functionality'];
					var moduleIds = new Array();
					var functionIds = new Array();
					var subFunctionIds = new Array();

					var defaultElements = new Array();

					$.each(functionalityArray, function(index, functionalityString) {
						var functionality = functionalityString.split(':');
						moduleIds.push(functionality[0]);
						functionIds.push(functionality[1]);
						subFunctionIds.push(functionality[2]);
					});
					$.each(moduleIds, function(index, moduleId) {
						var element = $("input:checkbox[moduleId="+moduleId+"][functionId="+functionIds[index]+"][value="+subFunctionIds[index]+"]");
						element.prop('checked',true);
						element.closest('span').find('label').css('color', 'green');

						var elementIdString = "input:checkbox[moduleId="+moduleId+"][functionId="+functionIds[index]+"][value="+subFunctionIds[index]+"]";
						defaultElements.push(elementIdString);
					});


					
					$("input:checkbox").not(defaultElements.join(',')).click(function(event) {
						if ($(this).is(":checked")) {
							$(this).closest('span').find('label').css('color', 'orange');

							$("#actionTracker").attr('moduleId',$(this).attr('moduleId'));
							$("#actionTracker").attr('functionId',$(this).attr('functionId'));
							$("#actionTracker").attr('subFunctionId',$(this).val());

							$(this).removeAttr('validType');
							$(this).removeAttr('date');
							$(this).removeAttr('time');
							$("#validTillModal").modal('show');
							$('#validTillModal').modal({backdrop: 'static', keyboard: false});
						}
						else{
							$(this).closest('span').find('label').css('color', 'black');
						}
						
					});


					
				}) /*End Success*/
				.fail(function() {
					alert("response error");
				})
				
				
				
			}
		});
		/*end make the selected action on role select*/


		/*Validation Hour*/
		$("#validTillDate").datepicker({
	            changeMonth: true,
	            changeYear: true,
	            yearRange : "c:c+5",
	            minDate: "dateToday",
	            dateFormat: 'dd-mm-yy',
	            disabled: true
        });

        $('#validTillTime').timepicker({
	        'showDuration': true,
	        'timeFormat': 'g:ia'
	    });
		/*End Validation Date*/


		$("input[name=validType]").trigger('change');
		/*Valid Type Change*/
		$("input[name=validType]").change(function(event) {
			if($(this).val()==2){
				$("#validTillDate").datepicker("option","disabled",false);
				$("#validTillDate").css('cursor', 'pointer');

				$("#validTillTime").css('cursor', 'pointer');			
				$("#validTillTime").css('pointer-events', 'auto');
			}
			else{
				$("#validTillDate").datepicker("option","disabled",true);
				$("#validTillDate").css('cursor', 'not-allowed');

				$("#validTillTime").css('cursor', 'no-drop');
				$("#validTillTime").css('pointer-events', 'none');

			}
		});
		/*End Valid Type Change*/

		/*On Click Modal Submit Button add time validation attr of the checkbox*/
		$("#modalSubmit").click(function(event) {
			var validType = $("input[name=validType]:checked").val();
			var date = $("#validTillDate").val();
			var time = $("#validTillTime").val();;
			var moduleId = $("#actionTracker").attr('moduleId');
			var functionId = $("#actionTracker").attr('functionId');
			var subFunctionId = $("#actionTracker").attr('subFunctionId');

			element = $("input:checkbox[moduleId="+moduleId+"][functionId="+functionId+"][value="+subFunctionId+"]");

			if (validType==2) {				
				element.attr('validType',2);
				element.attr('date',date);
				element.attr('time',time);
			}
			else{
				element.attr('validType',1);
				element.attr('date','');
				element.attr('time','');
			}

			$("#validTillModal").modal('hide');
		});
		/*End On Click Modal Submit Button add time validation attr of the checkbox*/

		/*If close the modal uncheck the action*/
		$("#modalClose").click(function(event) {
			var moduleId = $("#actionTracker").attr('moduleId');
			var functionId = $("#actionTracker").attr('functionId');
			var subFunctionId = $("#actionTracker").attr('subFunctionId');

			element = $("input:checkbox[moduleId="+moduleId+"][functionId="+functionId+"][value="+subFunctionId+"]");

			element.prop('checked', false);
			element.closest('span').find('label').css('color','black');
		});

		/*End If close the modal uncheck the action*/



		/*Submit the data*/
		$("#submitButton").click(function(event) {
			$(".error").remove();

			var userId = $("#userId").val();
			var roleId = $("#roleId").val();
			var description = $("#description").val();

			var moduleIds = new Array();		
			var functionIds = new Array();		
			var subFunctionIds = new Array();	
			var validTypes = new Array();
			var dates = new Array();
			var times = new Array();

			$("input[type=checkbox]:checked").each(function(index, el) {
				if ($(el).val()!='on') {
					moduleIds.push($(el).attr('moduleId'));
					functionIds.push($(el).attr('functionId'));
					subFunctionIds.push($(el).val());

					if($(el).get(0).hasAttribute('validType')){
						validTypes.push($(el).attr('validType'));
						dates.push($(el).attr('date'));
						times.push($(el).attr('time'));
					}
									
				}				
			});

			var csrf = "{{csrf_token()}}";

			$.ajax({
				url: './storeGnrUserRole',
				type: 'POST',
				dataType: 'json',
				data: {userId: userId, roleId: roleId, moduleIds: moduleIds, functionIds: functionIds, subFunctionIds: subFunctionIds, validTypes: validTypes, dates: dates, times: times, description: description, _token: csrf},
			})
			.done(function(data) {

				// Print Error
					if(data.errors) {
						$.each(data.errors, function(name, error) {							
							 $("#"+name).after("<p class='error' style='color:red;'>* "+data.errors[name]+"</p>");
						});
					}
					else{
						location.href = "viewGnrUserRole";
					}
				
			})
			.fail(function() {
				alert('Response Error');
			})			
			
		});
		/*End Submit the data*/

		
		

	});/*Ready*/
</script>
@endsection

