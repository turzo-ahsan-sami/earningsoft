@extends('layouts/gnr_layout')
@section('title', '| Working Area')
@section('content')
	<style type="text/css">
		.form-control {
			font-size: 11px !important;
			padding: 5px !important;
		}
		label {
		    font-weight: 400;
		    font-size: 11px !important;
		}
		table tbody tr td {
			text-align: left !important;
			font-size: 11px !important;
		}
	</style>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default" style="background-color:#708090;">
				<div class="panel-heading" style="padding-bottom:0px">
					<div class="panel-options">
						<a href="{{ url('addWorkingArea/') }}" class="btn btn-info pull-right addViewBtn">
							<i class="glyphicon glyphicon-plus-sign addIcon"></i> 
							Add Working Area
						</a>
					</div>
					<h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">WORKING AREA LIST</font></h1>
				</div>
				<div class="panel-body panelBodyView"> 
					<table class="table table-striped table-bordered" id="areaListView">
						<thead>
							<tr>
								<?php for($i=0;$i<count($TCN);$i++): ?>
									<th width="<?php echo $TCN[$i][1]; ?>"><?php echo $TCN[$i][0]; ?></th>
								<?php endfor; ?>
		                    </tr>
		                    {{ csrf_field() }}
						</thead>
						<tbody>
							<?php $SL = 1; ?>
							@foreach($workingAreas as $workingArea)
								<tr>
									<td>{{ $SL }}</td>
									<td>{{ $workingArea->name }}</td>
									<!-- <td>{{ str_pad($workingArea->code, 5, 0, STR_PAD_LEFT) }}</td> -->
									<td>
										<?php
			                                $branchName = DB::table('gnr_branch')->select('name')->where('id', $workingArea->branchId)->first();
			                            ?>
			                            {{ $branchName->name }}
									</td>
									<td>
										<?php
			                                $divisionName = DB::table('gnr_division')->select('name')->where('id', $workingArea->divisionId)->first();
			                            ?>
			                            {{ $divisionName->name }}
									</td>
									<td>
										<?php
			                                $districtName = DB::table('gnr_district')->select('name')->where('id', $workingArea->districtId)->first();
			                            ?>
			                            {{ $districtName->name }}
									</td>
									<td>
										<?php
			                                $upazilaName = DB::table('gnr_upzilla')->select('name')->where('id', $workingArea->upazilaId)->first();
			                            ?>
			                            {{ $upazilaName->name }}
									</td>
									<td>
										<?php
			                                $unionName = DB::table('gnr_union')->select('name')->where('id', $workingArea->unionId)->first();
			                            ?>
			                            {{ $unionName->name }}
									</td>
									<td>
										<?php
			                                $villageName = DB::table('gnr_village')->select('name')->where('id', $workingArea->villageId)->first();
			                            ?>
			                            {{ $villageName->name }}
									</td>
									<td style="text-align:center!important;">
										<a href="javascript:;" class="showUpdateForm" data-id="{{ $workingArea->id }}" data-name="{{ $workingArea->name }}" data-code="{{ $workingArea->code }}" data-branchid="{{ $workingArea->branchId }}" data-divisionid="{{ $workingArea->divisionId }}" data-districtid="{{ $workingArea->districtId }}" data-upazilaid="{{ $workingArea->upazilaId }}" data-unionid="{{ $workingArea->unionId }}" data-villageid="{{ $workingArea->villageId }}">
											<i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i>
										</a>
										<a href="javascript:;" class="deleteConfirmation" data-id="{{ $workingArea->id }}">
											<i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
										</a>
									</td>
								</tr>
								<?php $SL++; ?>
							@endforeach
							<?php  
								if(count($workingAreas)==0)
									echo '<tr><td style="background:#e1e3e8!important;color:#000!important;" colspan="'.count($TCN).'">No Data Found</td></tr>';
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@endsection
<div class="modal fade" id="modal-wrapper">
	<div class="modal-dialog" style="width:900px">
		{!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups')) !!}
			<div class="modal-header" style="background-color:#000;color:#FFF;padding:10px 15px!important;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i style="background:#FFF;" class="fa fa-times" aria-hidden="true"></i></button>
				<h4 class="modal-title">Working Area Update</h4>
			</div>
			<div class="modal-content" style="border:none!important;padding:5px 30px!important;">
				<div class="modal-body">
					<div class="form-group hidden">
						<div class="col-sm-6 col-sm-offset-2">
							{!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text', 'readonly']) !!}
						</div>
					</div>
        			<div class="form-group">
        				{!! Form::label('name', 'Name:', ['class' => 'col-sm-2 control-label']) !!}
        				<div class="col-sm-6">
        					{!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter working area name']) !!}
        				</div>
        			</div>
        			<!-- <div class="form-group">
        				{!! Form::label('code', 'Code:', ['class' => 'col-sm-2 control-label']) !!}
        				<div class="col-sm-6">
        					{!! Form::text('code', $value = null, ['class' => 'form-control', 'id' => 'code', 'type' => 'text', 'data-mask' => '99999', 'placeholder' => 'Enter working area code']) !!}
        				</div>
        			</div> -->
                    <div class="form-group">
                        {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-6">
                            <?php 
                                $branchList = array('' => 'Select') + DB::table('gnr_branch')->pluck('name', 'id')->all(); 
                            ?>      
                            {!! Form::select('branchId', ($branchList), null, array('class'=>'form-control', 'id' => 'branchId')) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('division', 'Division:', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-6">
                            <?php 
                                $divisionList = array('' => 'Select') + DB::table('gnr_division')->pluck('name', 'id')->all(); 
                            ?>      
                            {!! Form::select('divisionId', ($divisionList), null, array('class'=>'form-control', 'id' => 'divisionId')) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('district', 'District:', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-6">
                            {!! Form::select('districtId', array('' => 'Select'), null, array('class'=>'form-control', 'id' => 'districtId')) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('upazila', 'Thana / Upazila:', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-6">
                            {!! Form::select('upazilaId', array('' => 'Select'), null, array('class'=>'form-control', 'id' => 'upazilaId')) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('union', 'Union:', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-6">
                            {!! Form::select('unionId', array('' => 'Select'), null, array('class'=>'form-control', 'id' => 'unionId')) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('village', 'Village:', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-6">
                            {!! Form::select('villageId', array('' => 'Select'), null, array('class'=>'form-control', 'id' => 'villageId')) !!}
                        </div>
                    </div>
				</div>
			</div>
			<div class="modal-footer" style="background-color:#FFF;color:#FFF;">
				<button type="button" class="btn btn-info animated slideInRight" style="margin-bottom:0!important;" onclick="updateData();">Save</button>
				<button type="button" class="btn btn-white animated slideInRight" data-dismiss="modal" style="margin-bottom:0!important;">Close</button>
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
	$(document).ready(function(){
		$(document).on('change', '#divisionId', function() {
        	$('#districtId').empty();
        	
        	$.ajax({
		        type: 'post',
		        url: './reqDistrict',
		        dataType: 'json',
		        data: {
		        	'divisionId': $('#divisionId').val()
		       	}, 
		        success: function(_response) {
                    if(_response.errors) {
                        alert(_response.errors);
                    } else {
               			$.each(_response.district, function(index, value) {
	                        $('#districtId').append('<option value="'+ index +'">' + value + '</option>');
	                    });
                    }
                },
                error: function(_response) {
                    alert(_response.errors);
                }
		    });
        });

        $(document).on('change', '#districtId', function() {
        	$('#upazilaId').empty();

        	$.ajax({
		        type: 'post',
		        url: './reqUpzilla',
		        dataType: 'json',
		        data: {
		        	'divisionId': $('#divisionId').val(),
		        	'districtId': $('#districtId').val()
		       	}, 
		        success: function(_response) {
                    if(_response.errors) {
                        alert(_response.errors);
                    } else {
               			$.each(_response.upzilla, function(index, value) {
	                        $('#upazilaId').append('<option value="'+ index +'">' + value + '</option>');
	                    });
                    }
                },
                error: function(_response) {
                    alert(_response.errors);
                }
		    });
        });

        $(document).on('change', '#upazilaId', function() {
        	$('#unionId').empty();

        	$.ajax({
		        type: 'post',
		        url: './reqUnion',
		        dataType: 'json',
		        data: {
		        	'divisionId': $('#divisionId').val(),
		        	'districtId': $('#districtId').val(),
		        	'upazilaId': $('#upazilaId').val()
		       	}, 
		        success: function(_response) {
                    if(_response.errors) {
                        alert(_response.errors);
                    } else {
               			$.each(_response.union, function(index, value) {
	                        $('#unionId').append('<option value="'+ index +'">' + value + '</option>');
	                    });
                    }
                },
                error: function(_response) {
                    alert(_response.errors);
                }
		    });
        });

        $(document).on('change', '#unionId', function() {
        	$('#villageId').empty();
        	
        	$.ajax({
		        type: 'post',
		        url: './reqVillage',
		        dataType: 'json',
		        data: {
		        	'divisionId': $('#divisionId').val(),
		        	'districtId': $('#districtId').val(),
		        	'upazilaId': $('#upazilaId').val(),
		        	'unionId': $('#unionId').val()
		       	}, 
		        success: function(_response) {
                    if(_response.errors) {
                        alert(_response.errors);
                    } else {
               			$.each(_response.village, function(index, value) {
	                        $('#villageId').append('<option value="'+ index +'">' + value + '</option>');
	                    });
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
	$(document).on('click', '.showUpdateForm', function() {
		if(hasAccess('updateWorkingAreaItem')) {
			$('#id').val($(this).data('id'));
			$('#name').val($(this).data('name'));
			//$('#code').val($(this).data('code'));
			$('#branchId').val($(this).data('branchid'));
			$('#divisionId').val($(this).data('divisionid'));
			$('#districtId').val($(this).data('districtid'));
			$('#upazilaId').val($(this).data('upazilaid'));
			$('#unionId').val($(this).data('unionid'));
			$('#villageId').val($(this).data('villageid'));

        	$.ajax({
		        type: 'post',
		        url: './reqDistrictUpzillaUnionVillage',
		        dataType: 'json',
		        data: {
		        	'divisionId': $('#divisionId').val(),
		        	'districtId': $('#districtId').val(),
		        	'upazilaId': $('#upazilaId').val(),
		        	'unionId': $('#unionId').val()
		       	}, 
		        success: function(_response) {
                    if(_response.errors) {
                        alert(_response.errors);
                    } else {
               			$.each(_response.district, function(index, value) {
	                        $('#districtId').append('<option value="'+ index +'">' + value + '</option>');
	                    });

               			$.each(_response.upzilla, function(index, value) {
	                        $('#upazilaId').append('<option value="'+ index +'">' + value + '</option>');
	                    });

	                    $.each(_response.union, function(index, value) {
	                        $('#unionId').append('<option value="'+ index +'">' + value + '</option>');
	                    });

	                    $.each(_response.village, function(index, value) {
	                        $('#villageId').append('<option value="'+ index +'">' + value + '</option>');
	                    });
                    }
                },
                error: function(_response) {
                    alert(_response.errors);
                }
		    });
			
			jQuery('#modal-wrapper').modal('show', {backdrop: 'static'});
		}
	});

	function updateData() {
		$.ajax({
			type: 'post',
			url: './updateWorkingAreaItem',
			data: $('form').serialize(),
			dataType: 'json',
			success: function(data) {
				if(data.errors) {
					if(data.errors['name'])
						toastr.warning(data.errors.name, null, opts);
					//if(data.errors['code'])
						//toastr.warning(data.errors.code, null, opts);
					if(data.errors['branchId'])
						toastr.warning(data.errors.branchId, null, opts);
					if(data.errors['divisionId'])
						toastr.warning(data.errors.divisionId, null, opts);
					if(data.errors['districtId'])
						toastr.warning(data.errors.districtId, null, opts);
					if(data.errors['upazilaId'])
						toastr.warning(data.errors.upazilaId, null, opts);
					if(data.errors['unionId'])
						toastr.warning(data.errors.unionId, null, opts);
					if(data.errors['villageId'])
						toastr.warning(data.errors.villageId, null, opts);
				} else {
					$('#name').val('');
					//$('#code').val('');
					$('#branchId').val('');
					$('#divisionId').val('');
					$('#districtId').val('');
					$('#upazilaId').val('');
					$('#unionId').val('');
					$('#villageId').val('');
					$('.error').addClass('hidden');
					toastr.success(data.responseText, "Success!", opts);
					
					setTimeout(function(){
						window.location.href = '{{ url('viewWorkingArea/') }}';
					}, 3000);
				}
			},
			error: function(data) {
				alert(data.errors);
			}
		});
	}

	$(document).on('click', '.deleteConfirmation', function() {
		if(hasAccess('deleteWorkingAreaItem')) {
			$('#id').val($(this).data('id'));
			jQuery('#delete-confirmation-modal').modal('show', {backdrop: 'fade'});
		}
	});

	function deleteData() {
		$.ajax({
			type: 'post',
			url: './deleteWorkingAreaItem',
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
						window.location.href = '{{ url('viewWorkingArea/') }}';
					}, 3000);
				}
			},
			error: function(data) {
				alert(data.errors);
			}
		});
	}
</script>