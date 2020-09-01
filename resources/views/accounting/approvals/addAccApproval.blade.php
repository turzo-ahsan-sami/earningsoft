@extends('layouts/acc_layout')
@section('title', '| Approval Setting')
@section('content')

<style type="text/css">
	.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #444 !important;
    height: 32px !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #444;
    line-height: 16px !important;
    display: block  !important;
    width: 100%  !important;
    height: 32px  !important;
    padding: 6px 12px  !important;
    font-size: 11px  !important;
    text-align: left;
    color: #555;
}
button, input, select, textarea {
    font-family: inherit;
    font-size: inherit;
    line-height: inherit;
}
</style>

<div class="row add-data-form">
	<div class="col-md-12">
		<div class="col-md-2"></div>
		<div class="col-md-8 fullbody">
			<div class="viewTitle" style="border-bottom: 1px solid white;">
				<a href="{{url('viewApprovalSetting/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
				</i>Setting List</a>
			</div>
			<div class="panel panel-default panel-border">
				<div class="panel-heading">
					<div class="panel-title">New Approval Setting</div>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-12">
								{!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
								<!-- <input type = "hidden" name = "_token" value = ""> -->
								<div class="row">
									<div class="col-md-12">
										<div class="col-md-4">
											<div class="form-group">
												{!! Form::label('name', 'Project:', ['class' => 'col-sm-12 control-label']) !!}
												<div class="col-sm-12">
													<select class ="form-control" id = "projectId" name="projectId">
			                                            <option value="0">Please Select Project First</option> 
			                                            @foreach($gnrProjectInfos as $gnrProjectInfo)                  
			                                            <option value="{{$gnrProjectInfo->id}}">{{$gnrProjectInfo->name}}</option> 
			                                            @endforeach                  
			                                        </select>
			                                         <p id='projectNamee' style="max-height:3px;"></p>
												</div>
											</div>	
										</div>
										<div class="col-md-4">
											<div class="form-group">
												{!! Form::label('name', 'Branch:', ['class' => 'col-sm-12 control-label']) !!}
												<div class="col-sm-12">
													<select class ="form-control" id = "branch" name="branch">
														<option value="0">Please Select Branch</option> 
			                                            <option value="Head Office">Head Office</option>                  
			                                            <option value="All Branch">All Branch</option>                  
			                                        </select>
			                                         <p id='branchIde' style="max-height:3px;"></p>
												</div>
											</div>	
										</div>
										<div class="col-md-4">
											<div class="form-group">
												{!! Form::label('name', 'Date:', ['class' => 'col-sm-12 control-label']) !!}
												<div class="col-sm-12">
													<p><input type = "text" readonly class="form-control" id = "datepicker" name="date"></p>
													 <p id='datee' style="max-height:3px;"></p>
												</div>
											</div>	
										</div>
									</div>
								</div>
							</br>
								<div class="row" style="padding-bottom: 20px">
									<div class="col-md-6"  style="padding: 0px 21px 0px 27px;">
										<h4>Department</h4>
										<table id="addDepTable" class="table table-striped table-bordered">
                                            <thead>
	                                            <tr id="headerRowDes">
	                                                <th style="width: 18%;text-align:center;">Step</th>
	                                                <th style="width: 82%;text-align:center;">Department</th>
	                                            </tr>
                                            </thead>
                                            <tbody>
                                            	@if($v_approval_step == 1)    
	                                            <tr>
	                                            	<td>Approver</td>
			                                    	<td>
	                                            		<select class ="form-control select2" id = "approvedByDepId" name="approvedByDepId">
			                                            	<option value="0">Please Select Department First</option>
			                                            	@foreach($departments as  $department) 
			                                            	  <option value="{{$department->id}}">{{$department->name}}</option> 
			                                            	@endforeach                        
			                                        	</select>
			                                    	</td>
	                                            </tr>

	                                            @elseif($v_approval_step == 2)
	                                            <tr>
	                                            	<td>Verifier</td>
			                                    	<td>
	                                            		<select class ="form-control select2" id = "verifiedByDepId" name="verifiedByDepId">
			                                            	<option value="0">Please Select Department First</option>
			                                            	@foreach($departments as  $department) 
			                                            	  <option value="{{$department->id}}">{{$department->name}}</option> 
			                                            	@endforeach                        
			                                        	</select>
			                                    	</td>
	                                            </tr>
												<tr>
	                                            	<td>Approver</td>
	                                            	<td>
	                                            		<select class ="form-control select2" id = "approvedByDepId" name="approvedByDepId">
			                                            	<option value="0">Please Select Department First</option>
			                                            	@foreach($departments as  $department) 
			                                            	  <option value="{{$department->id}}">{{$department->name}}</option> 
			                                            	@endforeach                        
			                                        	</select>
			                                    	</td>	
	                                            </tr>
	                                            @elseif($v_approval_step == 3)
	                                             <tr>
	                                            	<td>Verifier</td>
			                                    	<td>
	                                            		<select class ="form-control select2" id = "verifiedByDepId" name="verifiedByDepId">
			                                            	<option value="0">Please Select Department First</option>
			                                            	@foreach($departments as  $department) 
			                                            	  <option value="{{$department->id}}">{{$department->name}}</option> 
			                                            	@endforeach                        
			                                        	</select>
			                                    	</td>
	                                            </tr>
												<tr>
	                                            	<td>Reviewer</td>
	                                            	<td>
	                                            		<select class ="form-control select2" id = "reviewedByDepId" name="reviewedByDepId">
			                                            	<option value="0">Please Select Department First</option>
			                                            	@foreach($departments as  $department) 
			                                            	  <option value="{{$department->id}}">{{$department->name}}</option> 
			                                            	@endforeach                        
			                                        	</select>
			                                    	</td>	
	                                            </tr>
	                                            <tr>
	                                            	<td>Approver</td>
	                                            	<td>
	                                            		<select class ="form-control select2" id = "approvedByDepId" name="approvedByDepId">
			                                            	<option value="0">Please Select Department First</option>
			                                            	@foreach($departments as  $department) 
			                                            	  <option value="{{$department->id}}">{{$department->name}}</option> 
			                                            	@endforeach                        
			                                        	</select>
			                                    	</td>
	                                            </tr>
	                                            @endif
                                            </tbody>
	                                    </table>
	                                    <p id='reviewedByIde' style="max-height:3px;" align="center"></p>
	                                    <p id='verifiedByIde' style="max-height:3px;" align="center"></p>
	                                    <p id='approvedByIde' style="max-height:3px;" align="center"></p>
									</div>
									<div class="col-md-6"  style="padding: 0px 21px 0px 27px;">
										<h4>Designation</h4>
										<table id="addDesTable" class="table table-striped table-bordered">
                                            <thead>
	                                            <tr id="headerRowDes">
	                                            	<div class="col-md-3">
	                                                <th style="width: 18%;">Step</th>
	                                            	</div>
	                                                <th style="width: 82%;">Designation</th>
	                                            </tr>
                                            </thead>
                                            <tbody>  
                                            	@if($v_approval_step == 1)  
													<tr>
		                                            	<td>Approver</td>
				                                    	<td>
		                                            		<select class ="form-control select2" id = "approvedById" name="approvedById">
				                                            	<option value="0">Please Select Department First</option>                    
				                                        	</select>
				                                    	</td>
		                                            </tr>
                                            	@elseif($v_approval_step == 2)
	                                            	<tr>
		                                            	<td>Verifier</td>
				                                    	<td>
		                                            		<select class ="form-control select2" id = "verifiedById" name="verifiedById">
				                                            	<option value="0">Please Select Department First</option>                    
				                                        	</select>
				                                    	</td>
		                                            </tr> 
		                                            <tr>
		                                            	<td>Approver</td>
		                                            	<td>
		                                            		<select class ="form-control select2" id = "approvedById" name="approvedById">
				                                            	<option value="0">Please Select Department First</option>                    
				                                        	</select>
				                                    	</td>
		                                            </tr>     
                                            	@elseif($v_approval_step == 3)  
                                            		<tr>
		                                            	<td>Verifier</td>
				                                    	<td>
		                                            		<select class ="form-control select2" id = "verifiedById" name="verifiedById">
				                                            	<option value="0">Please Select Department First</option>                    
				                                        	</select>
				                                    	</td>
	                                            	</tr>
		                                            <tr>
		                                            	<td>Reviewer</td>
		                                            	<td>
		                                            		<select class ="form-control select2" id = "reviewedById" name="reviewedById">
				                                            	<option value="0">Please Select Department First</option>                    
				                                        	</select>
				                                    	</td>
		                                            </tr>
		                                            <tr>
		                                            	<td>Approver</td>
		                                            	<td>
		                                            		<select class ="form-control select2" id = "approvedById" name="approvedById">
				                                            	<option value="0">Please Select Department First</option>  	                
				                                        	</select>
				                                    	</td>
		                                            </tr>    
	                                            @endif
                                            </tbody>
	                                    </table>
	                                    <p id='reviewedByIdDes' style="max-height:3px;" align="center"></p>
	                                    <p id='verifiedByIdDes' style="max-height:3px;" align="center"></p>
	                                    <p id='approvedByIdDes' style="max-height:3px;" align="center"></p>
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
									<div class="col-sm-9 text-right" style="padding-right: 45px;">
										{!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
										<a href="{{url('viewApprovalSetting/')}}" class="btn btn-danger closeBtn">Close</a>
										<span id="success" style="color:green; font-size:16px;" class="pull-right"></span>
									</div>
								</div>
								{!! Form::close()  !!}
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
	$(document).ready(function(){
		//date picker script
		$("#datepicker").datepicker({
        	dateFormat: "yy-mm-dd"
   		});

   		//onchange department
	   	$('#verifiedByDepId').on('change',function(){
	        if($(this).val() != ''){
	            var verifiedByDepId = $(this).val();
	            $.ajax({
	                url:'./getPositionVerifiedBy',
	                type: 'GET',
	                data: {verifiedByDepId:verifiedByDepId},
	                dataType: 'json',
	                success: function(data) {
	                    $("#verifiedById").empty(); 
	                    $("#verifiedById").append("<option value=''>Please Select Designation First</option>"); 
	                    $("#verifiedById option[value!='']").remove();
	                    $.each(data,function(key,value){
	                        var name = value.name;
	                        var id = value.id;
	                        $("#verifiedById").append("<option value='" + id + "'>" + name + "</option>");
	                	});
	                }
	            });
	        }
	    });

		//onchange reviewed by id
	    $('#reviewedByDepId').on('change',function(){
	        if($(this).val() != ''){
	            var reviewedByDepId = $(this).val();
	            $.ajax({
	                url:'./getPositionReviewedBy',
	                type: 'GET',
	                data: {reviewedByDepId:reviewedByDepId},
	                dataType: 'json',
	                success: function(data) {
	                    $("#reviewedById").empty(); 
	                    $("#reviewedById").append("<option value=''>Please Select Designation First</option>"); 
	                    $("#reviewedById option[value!='']").remove();
	                    $.each(data,function(key,value){
	                        var name = value.name;
	                        var id = value.id;
	                        $("#reviewedById").append("<option value='" + id + "'>" + name + "</option>");
	               		});
	                }
	            });
	        }
	    });

	    //onchange approved by id
	    $('#approvedByDepId').on('change',function(){
	        if($(this).val() != ''){
	            var approvedByDepId = $(this).val();
	            $.ajax({
	                url:'./getPositionAprovedBy',
	                type: 'GET',
	                data: {approvedByDepId:approvedByDepId},
	                dataType: 'json',
	                success: function(data) {
	                    $("#approvedById").empty(); 
	                    $("#approvedById").append("<option value=''>Please Select Designation First</option>"); 
	                    $("#approvedById option[value!='']").remove();
	                    $.each(data,function(key,value){
	                        var name = value.name;
	                        var id = value.id;
	                        $("#approvedById").append("<option value='" + id + "'>" + name + "</option>");
	               		});
	                }
	            });
	        }
	    });

	    
	    //form submit
		$('form').submit(function( event ) {
	    	event.preventDefault();
	      	
	      	var projectId = $("#projectId").val();
	      	var branch = $("#branch").val();
	      	var date 	   = $("#datepicker").val();
	      	var reviewedById = $("#reviewedById").val();
	      	var verifiedById = $("#verifiedById").val();
	      	var approvedById = $("#approvedById").val();

	      	var reviewedByDepId = $("#reviewedByDepId").val();
	      	var verifiedByDepId = $("#verifiedByDepId").val();
	      	var approvedByDepId = $("#approvedByDepId").val();
	      	
	      	var csrf = "{{csrf_token()}}";

	   	 	formData = new FormData();
	   	 	
	   	 	formData.append('projectId', projectId);
	   	 	formData.append('branch', branch);
	   	 	formData.append('date', date);
	   	 	formData.append('reviewedById', reviewedById);
	   	 	formData.append('verifiedById', verifiedById);
	   	 	formData.append('approvedById', approvedById);

	   	 	formData.append('reviewedByDepId', reviewedByDepId);
	   	 	formData.append('verifiedByDepId', verifiedByDepId);
	   	 	formData.append('approvedByDepId', approvedByDepId);
	   	 	
	   	 	formData.append('_token', csrf);
   	 	
   	 		$.ajax({
	   	 		processData: false,
	            contentType: false,
	            type: 'post',
	            url: './addAccApprovalItem',
	            //data: {projectName:projectName,branchName:branchName,date:date,_token: csrf},
	            data:formData,
	            dataType: 'json',
	            success: function( _response ){
	            	if (_response.errors) {
	            		if (_response.errors['projectId']) {
				            $('#projectNamee').empty();
				            $('#projectNamee').append('<span class="errormsg" style="color:red;">'+_response.errors.projectId+'</span>');
				            return false;
	            		}
	            		if (_response.errors['branch']) {
				            $('#branchIde').empty();
				            $('#branchIde').append('<span class="errormsg" style="color:red;">'+_response.errors.branch+'</span>');
				            return false;
	            		}
	            		if (_response.errors['date']) {
				            $('#datee').empty();
				            $('#datee').append('<span class="errormsg" style="color:red;">'+_response.errors.date+'</span>');
				            return false;
	            		}
	            		// if (_response.errors['reviewedByIdDes']) {
				           //  $('#reviewedByIdDes').empty();
				           //  $('#reviewedByIdDes').append('<span class="errormsg" style="color:red;">'+_response.errors.reviewedByIdDes+'</span>');
				           //  return false;
	            		// }
	            		// if (_response.errors['verifiedByIdDes']) {
				           //  $('#verifiedByIdDes').empty();
				           //  $('#verifiedByIdDes').append('<span class="errormsg" style="color:red;">'+_response.errors.verifiedByIdDes+'</span>');
				           //  return false;
	            		// }
	            		// if (_response.errors['approvedByIdDes']) {
				           //  $('#approvedByIdDes').empty();
				           //  $('#approvedByIdDes').append('<span class="errormsg" style="color:red;">'+_response.errors.approvedByIdDes+'</span>');
				           //  return false;
	            		// }
	            	}else{
	            		$("#projectId").val('');
	            		$("#branch").val('');
	            		$("#date").val('');
	            		//$("#reviewedByIdDes").val('');
	            		//$("#verifiedByIdDes").val('');
	            		//$("#approvedByIdDes").val('');
	            		
	            		$('.error').addClass("hidden");
	            		$('#success').text(_response.responseText);
	            		   window.location.href = '{{url('viewApprovalSetting/')}}';
	            	}
            	},
	            error: function( _response ){
	            	alert(_response.errors);
	        	}
        	});
		});

		//select2 script
    	$('.select2').select2();
	});
</script> 