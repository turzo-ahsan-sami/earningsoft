@extends('layouts/gnr_layout')
@section('title', '| Notice List')
@section('content')


<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default" style="background-color:#708090;">
			<div class="panel-heading" style="padding-bottom:0px">
				<div class="panel-options">
					<a href="{{ url('addNotice/') }}" class="btn btn-info pull-right addViewBtn">
						<i class="glyphicon glyphicon-plus-sign addIcon"></i> 
						Add Notice
					</a>
				</div>
				<h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">NOTICE LIST</font></h1>
				
			</div>
			<div class="panel-body panelBodyView"> 
				<table class="table table-striped table-bordered" id="areaListView">
					<thead>
						<tr>
							<?php //for($i=0;$i<count($TCN);$i++): ?>
							<th width="<?php //echo $TCN[$i][1]; ?>"><?php //echo $TCN[$i][0]; ?></th>
							<?php //endfor; ?>
						</tr>
					</thead>
					<tbody>
						<?php $SL = 1; ?>
						@foreach($notices as $notice)
						<tr>
							<td>{{ $SL }}</td>
							<td width="30%">{{ $notice->name }}</td>
							<td style="text-align:left;" width="40%">
								<?php 
								$i = 0;
								$branchIdStr = ''; 
								?>
								@foreach($notice->branchId as $branchId)
								<?php
								$branchList = DB::table('gnr_branch')->select('name')->where('id', $branchId)->first();
								$branchIdStr .= $branchId;

								if($i!=count($notice->branchId)-1)
									$branchIdStr .= ',';
								?>
								{{ $branchList->name }}
								@if($i!=count($notice->branchId)-1)
								{{ ', ' }} 
								@endif
								<?php $i++; ?>
								@endforeach
							</td>
							<td>{{date('d-m-Y',strtotime($notice->startDate))}}</td>
							<td>{{date('d-m-Y',strtotime($notice->endDate))}}</td>
							<td> 
								<?php
								$status = $notice->status;
								if($status==0){
									echo "<span class='btn btn-xs btn-danger'>Disabled</span>";
								}else{echo "<span class='btn btn-xs btn-success'>Enabled</span>";
							}
							?>
						</td>

						<td>
							<a href="javascript:;" class="showUpdateForm" data-id="{{ $notice->id }}" data-name="{{ $notice->name }}" data-status="{{ $notice->status }}" data-branchid="{{ $branchIdStr }}" data-startdate="{{ $notice->startDate }}" data-enddate="{{ $notice->endDate }}">
								<i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i>
							</a>

							<a href="javascript:;" class="deleteConfirmation" data-id="{{ $notice->id }}" >
								<i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
							</a>
						</td>
					</tr>
					<?php $SL++; ?>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
</div>
@endsection
<div class="modal fade" id="modal-wrapper">
	<div class="modal-dialog" style="width:900px">
		{!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups','id' => 'editform')) !!}
		<div class="modal-header" style="background-color:#000;color:#FFF;padding:10px 15px!important;">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i style="background:#FFF;" class="fa fa-times" aria-hidden="true"></i></button>
			<h4 class="modal-title">Notice Update</h4>
		</div>
		<div class="modal-content" style="border:none!important;padding:5px 30px!important;">
			<div class="modal-body">
				<div class="form-group hidden">
					<div class="col-sm-6 col-sm-offset-2">
						{!! Form::textarea('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text', 'readonly','rows' => 2]) !!}
					</div>
				</div>
				<div class="form-group">
					{!! Form::label('name', 'Notice Title:', ['class' => 'col-sm-2 control-label']) !!}
					<div class="col-sm-6">
						{!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Notice Title']) !!}
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
						{!! Form::select('status', array('1'=>'Active', '0'=>'Inactive'),  $value = null, array('class'=>'form-control', 'id' => 'status')) !!}
						<p id='statuse' style="max-height:3px;"></p>
					</div>
				</div>

	{{-- 			<div class="form-group">
					{!! Form::label('code', 'Code:', ['class' => 'col-sm-2 control-label']) !!}
					<div class="col-sm-6">
						{!! Form::text('code', $value = null, ['class' => 'form-control', 'id' => 'code', 'type' => 'text', 'data-mask' => '9999', 'placeholder' => 'Enter area code']) !!}
					</div>
				</div> --}}
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
				{{-- 		@if($branch->id==1)
						@continue
						@endif --}}
						<div class="col-sm-3" style="padding-right:0px!important;">
							{!! Form::checkbox('branchId[]', ($branch->id), false, array('class' => 'branchId cbr')) !!}
							<span style="font-size:11px;">  
								{!! Form::label(Illuminate\Support\Str::lower($branch->name), (str_pad($branch->branchCode, 4, '0', STR_PAD_LEFT) . ' - ' . $branch->name)) !!}
							</span>
						</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer" style="background-color:#FFF;color:#FFF;">
			<button type="button" id="submit" class="btn btn-info animated slideInRight" style="margin-bottom:0!important;" onclick="updateData(); this.disabled=true;this.form.submit();">Save</button>
			<button type="button" class="btn btn-white animated slideInRight" data-dismiss="modal" style="margin-bottom:0!important; ">Close</button>
		</div>
		{!! Form::close() !!}
	</div>
</div>
<div class="modal fade" id="delete-confirmation-modal">
	<div class="modal-dialog">
		<div class="modal-header" style="background-color:#000;color:#FFF;padding:10px 15px!important;">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title">Delete Confirmation</h4>
		</div>
		<div class="modal-content" style="border:none!important;padding:5px 30px!important;">
			<div class="modal-body" style="text-align:center">
				Are you sure to delete this item?
			</div>
		</div>
		<div class="modal-footer" style="background-color:#FFF;color:#FFF;">
			<div class="form-group hidden">
				<div class="col-sm-6 col-sm-offset-2">
					{!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text', 'readonly']) !!}
				</div>
			</div>
			<button id="yesBtn" type="button" class="btn btn-info animated slideInRight" style="margin-bottom:0!important;" onclick="deleteData();">Yes</button>
			<button id="noBtn" type="button" class="btn btn-white animated slideInRight" data-dismiss="modal" style="margin-bottom:0!important;">No</button>
		</div>
	</div>
</div>
<div class="modal fade" id="assign-check-modal"></div>
<script src="{{ asset('js/jquery-1.11.1.min.js') }}"></script>
<script type="text/javascript">

	$(document).on('click', '.showUpdateForm', function() {
		
			//	CLEAR ALL THE CHECK SIGN FROM ALL CHECKBOXES.
			$(".modal-content .cbr-replaced .cbr-input").find('input').each(function() {
				$(this).parent('.cbr-input').parent().removeClass('cbr-checked');
			});

			$('#id').val($(this).data('id'));
			$('#name').val($(this).data('name'));
			// $("#startDate").val(data['startDate']);
			// $("#endDate").val(data['endDate']);
			$('#startDate').val($(this).data('startdate'));
			$('#endDate').val($(this).data('enddate'));
			$('#status').val($(this).data('status'));
			
			var branchIdStr = $(this).data('branchid');
			branchIdStr = branchIdStr.toString();
			
			var branchIdArr = branchIdStr.split(",");

			$(".modal-content .cbr-replaced .cbr-input").find('input').each(function() {
				var getBranchId = $(this).val();

				if(branchIdArr.indexOf(getBranchId)>-1==true) {
					$(this).parent('.cbr-input').parent().addClass('cbr-checked');
					$(this).attr('checked', 'checked');
				}
			});

			jQuery('#modal-wrapper').modal('show', {backdrop: 'static'});

		});

	function updateData() {
		$.ajax({
			type: 'post',
			url: './editNoticeItem',
			data: $('form').serialize(),
			dataType: 'json',
			success: function(data) {
				if(data.errors) {
					if(data.errors['name'])
						toastr.warning(data.errors.name, null, opts);
					if(data.errors['status'])
						toastr.warning(data.errors.status, null, opts);
					if(data.errors['branchId'])
						toastr.warning(data.errors.branchId, null, opts);
					if(data.errors['startDate'])
						toastr.warning(data.errors.startDate, null, opts);
					if(data.errors['endDate'])
						toastr.warning(data.errors.endDate, null, opts);
				} else {
					$('#name').val('');
					$('#status').val('');
					$('#branchId').val('');
					$('#startDate').val('');
					$('#endDate').val('');
					$('.error').addClass('hidden');
					toastr.success(data.responseText, "Success!", opts);
					
					setTimeout(function(){
						window.location.href = '{{ url('viewNotice/') }}';
					}, 3000);
				}
			},
			error: function(data) {
				alert(data.errors);
			}
		});
	}

	$(document).on('click', '.checkAll', function(event) {
		if($(this).is(":checked")){		
			//alert('hghg')		
			$('.branchId').prop('checked',true);
		}
		else{
			$('.branchId').prop('checked',false);
		}
	});


	$(document).on('click', '.deleteConfirmation', function() {
		if(hasAccess('deleteNoticeItem')) {
			$('#id').val($(this).data('id'));
			jQuery('#delete-confirmation-modal').modal('show', {backdrop: 'fade'});
		}
	});

	function deleteData() {
		$.ajax({
			type: 'post',
			url: './deleteNoticeItem',
			data: {
				'_token': $('input[name=_token]').val(),
				'id': $('#id').val()
			},
			dataType: 'json',
			success: function(data) {
				if(data.errors) {
					if(data.errors['name'])
						toastr.warning(data.errors.name, null, opts);
				} else {
					$('#name').val('');
					$('.error').addClass('hidden');
					toastr.success(data.responseText, data.responseTitle, opts);
					
					setTimeout(function(){
						window.location.href = '{{ url('viewNotice/') }}';
					}, 3000);
				}
			},
			error: function(data) {
				alert(data.errors);
			}
		});
	}

	function checkDuplicate(getBranchId) {

		//alert(getBranchId);

		$.ajax({
			type: 'post',
			url: './checkNoticeAssignAvailability',
			data: {
				'_token': $('input[name=_token]').val(),
				'getBranchId': getBranchId
			},
			dataType: 'json',
			success: function(data) {
				if(data.errors) {
					if(data.errors['name'])
						toastr.warning(data.errors.name, null, opts);
				} else if(data.alreadyAssign==1) {
					var getAssignCheckModalContent = $('#delete-confirmation-modal').html();
					$('#assign-check-modal').html(getAssignCheckModalContent).css('margin-top','100px');
					$('#assign-check-modal .modal-title').text(data.responseTitle);
					$('#assign-check-modal .modal-body').text(data.responseText);
					$('#assign-check-modal .modal-body').append('<p style="color:#000;">Are you sure to assign this branch in this area?</p>');
					
					$('#assign-check-modal .modal-footer #noBtn').addClass('assignNotPermitted');
					$('#assign-check-modal .modal-footer #yesBtn').addClass('assignPermitted');
					$('#assign-check-modal .modal-footer #yesBtn').removeAttr('onclick');
					$('#assign-check-modal .modal-footer #yesBtn').attr('data-dismiss', 'modal');

					jQuery('#assign-check-modal').modal('show', {backdrop: 'fade'});

					$('.assignNotPermitted').on('click', function(){
						//alert(getBranchId);
						
						$(".modal-content .cbr-replaced .cbr-input").find('input').each(function() {
							var searchBranchId = $(this).val();

							if(getBranchId==searchBranchId) {
								$(this).parent('.cbr-input').parent().removeClass('cbr-checked');
								$(this).removeAttr('checked');
							}
						});
					});

					$('.error').addClass('hidden');
					toastr.success(data.responseText, "Success!", opts);
				}
			},
			error: function(data) {
				alert(data.errors);
			}
		});
	}

</script>
<script type="text/javascript">
	$('#editform').submit(function(){
		$('input[type=submit]').addClass("disabled");
	});

</script>


{{-- <script type="text/javascript">
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
	});
</script>
--}}

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
		});
	</script>



