@extends('layouts/gnr_layout')
@section('title', '| Add Notice')
@section('content')
<div class="row add-data-form">
	<div class="col-md-12">
		<div class="col-md-8 col-md-offset-2 fullbody">
			<div class="viewTitle" style="border-bottom:1px solid white;">
				<a href="{{ url('viewNotice/') }}" class="btn btn-info pull-right addViewBtn">
					<i class="glyphicon glyphicon-th-list viewIcon"></i>
					Notice List
				</a>
			</div>
			<div class="panel panel-default panel-border">
				<div class="panel-heading">
					<div class="panel-title">New Notice</div>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							{!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups','id' => 'noticeForm')) !!}
							<div class="form-group">
								{!! Form::label('name', 'Notice Title:', ['class' => 'col-sm-2 control-label']) !!}
								<div class="col-sm-6">
									{!! Form::textarea('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Notice Title','rows' => 2]) !!}
									<p id='namee' style="max-height:3px;"></p>
								</div>
							</div>

							<div class="form-group">
								{!! Form::label('startDate', 'Start Date:', ['class' => 'col-sm-2 control-label']) !!}
								<div class="col-sm-6">
									{!! Form::text('startDate',$value = null,['id'=>'startDate','class'=>'form-control','style'=>'cursor:pointer;','readonly']) !!}
								</div>
							</div>

							<div class="form-group">
								{!! Form::label('endDate', 'End Date:', ['class' => 'col-sm-2 control-label']) !!}
								<div class="col-sm-6">
									{!! Form::text('endDate',$value = null,['id'=>'endDate','class'=>'form-control','readonly','style'=>'cursor:pointer;']) !!}
								</div>
							</div>

							<div class="form-group">
								{!! Form::label('status', 'Status:', ['class' => 'col-sm-2 control-label']) !!}
								<div class="col-sm-6">     
									{!! Form::select('status', array('1'=>'Active', '0'=>'Inactive'), null, array('class'=>'form-control', 'id' => 'status')) !!}
									<p id='statuse' style="max-height:3px;"></p>
								</div>
							</div>
							<div class="form-group">
								{!! Form::label('branch', 'Branch:', ['class' => 'col-sm-2 control-label']) !!}
								&nbsp;&nbsp;&nbsp;&nbsp;{!! Form::checkbox('checkAll', null, false,['class'=>'checkAll']) !!} All Branch

								<div class="col-sm-10" style="padding-left:0!important;">

									<?php 
				                            	//	GET ALL THE BRANCHES.
									$branchList = DB::table('gnr_branch')->select('name', 'branchCode', 'id')->get(); 
									?>
									@foreach($branchList as $branch)
									{{-- BRANCH LISTING EXCEPT HEAD OFFICE --}}
								{{-- 	@if($branch->id==1)
									@continue
									@endif --}}
									<div class="col-sm-3" style="padding-right:0px!important;">
										{!! Form::checkbox('branchId[]', ($branch->id), false, array('class' => 'branchId')) !!}

										<span style="font-size:11px;">  

											{!! Form::label(Illuminate\Support\Str::lower($branch->name), (str_pad($branch->branchCode, 4, '0', STR_PAD_LEFT) . ' - ' . $branch->name)) !!}
										</span>
									</div>
									@endforeach
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-10 col-sm-offset-2">
									{!! Form::submit('Submit', ['id' => 'submit', 'class' => 'btn btn-info','onclick' => 'this.disabled=true;this.form.submit()']) !!}
									<a href="{{ url('viewNotice/') }}" class="btn btn-danger closeBtn">Close</a>
								</div>
							</div>
							{!! Form::close() !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#startDate").datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange : "c-10:c+10",
			dateFormat: 'dd-mm-yy',
			onSelect: function() {
				var date = $(this).datepicker('getDate');
				$("#endDate").datepicker('option','minDate',date);
				$(this).closest('div').find('.error').remove();
			}
		});

		$("#endDate").datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange : "c-10:c+10",
			dateFormat: 'dd-mm-yy',
			onSelect: function() {
				var date = $(this).datepicker('getDate');
				$("#startDate").datepicker('option','maxDate',date);
				$(this).closest('div').find('.error').remove();                           
			}
		});

		 // Hide Eddor
		 $(document).on('input', 'input', function() {
		 	$(this).closest('div').find('.error').remove();
		 });
		 $(document).on('change', 'select', function() {
		 	$(this).closest('div').find('.error').remove();
		 });

		 $('form').submit(function(event){
		 	event.preventDefault();

		 	$.ajax({
		 		type: 'post',
		 		url: './addNoticeItem',
		 		data: $('form').serialize(),
		 		dataType: 'json',
		 		success: function(_response) {
		 			if(_response.errors) {
		 				var opts = {
		 					"closeButton": true,
		 					"debug": false,
		 					"positionClass": "toast-top-right",
		 					"onclick": null,
		 					"showDuration": "300",
		 					"hideDuration": "1000",
		 					"timeOut": "5000",
		 					"extendedTimeOut": "1000",
		 					"showEasing": "swing",
		 					"hideEasing": "linear",
		 					"showMethod": "fadeIn",
		 					"hideMethod": "fadeOut"
		 				};

		 				if(_response.errors['name'])
		 					toastr.warning(_response.errors.name, null, opts);
		 				if(_response.errors['status'])
		 					toastr.warning(_response.errors.status, null, opts);
		 				if(_response.errors['branchId'])
		 					toastr.warning(_response.errors.branchId, null, opts);
		 			} else {
		 				var opts = {
		 					"closeButton": true,
		 					"debug": false,
		 					"positionClass": "toast-top-right",
		 					"onclick": null,
		 					"showDuration": "300",
		 					"hideDuration": "1000",
		 					"timeOut": "5000",
		 					"extendedTimeOut": "1000",
		 					"showEasing": "swing",
		 					"hideEasing": "linear",
		 					"showMethod": "fadeIn",
		 					"hideMethod": "fadeOut"
		 				};

		 				$('#name').val('');
		 				$('#status').val('');
		 				$('#branchId').val('');
		 				$('#startDate').val('');
		 				$('#endDate').val('');
		 				$('.error').addClass('hidden');
		 				toastr.success(_response.responseText, "Success!", opts);
		 				
		 				setTimeout(function(){
		 					window.location.href = '{{ url('viewNotice/') }}';
		 				}, 6000);
		 			}
		 		},
		 		error: function(_response) {
		 			alert(_response.errors);
		 		}
		 	});
		 });
		});
	</script>

	<script type="text/javascript">
		$(document).on('click', '.checkAll', function(event) {
			if($(this).is(":checked")){		
			//alert('branchId')		
			$('.branchId').prop('checked',true);
		}
		else{
			$('.branchId').prop('checked',false);
		}
	});


		$('#noticeForm').submit(function(){
			$('input[type=submit]').addClass("disabled");
		});





	// $('#noticeForm').submit(function(){
	// 	$(this).find('input[type=submit]').prop('disabled', true);
	// });



</script>
