@extends($route['layout'])
@section('title', '| Division')
@section('content')


@php
  $divisionIdsFromEmployeeTablePre = DB::table('hr_emp_general_info')->distinct()->pluck('pre_div_id')->toArray(); 
  $divisionIdsFromEmployeeTablePer = DB::table('hr_emp_general_info')->distinct()->pluck('per_div_id')->toArray(); 

  $divisionIdsFromEmployeeTable = array_unique(array_merge($divisionIdsFromEmployeeTablePre, $divisionIdsFromEmployeeTablePer));

  $divisionIdsFromDistrictTable = db::table('district')->distinct()->pluck('division_id')->toArray();
  
  $foreignDivisionIds = array_unique(array_merge($divisionIdsFromEmployeeTable, $divisionIdsFromDistrictTable));
@endphp


	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default" style="background-color:#708090;">
				<div class="panel-heading" style="padding-bottom:0px">
					<div class="panel-options">
						<a href="{{ url($route['path'].'/addDivision/') }}" class="btn btn-info pull-right addViewBtn">
							<i class="glyphicon glyphicon-plus-sign addIcon"></i> 
							Add Division
						</a>
					</div>
					<h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">DIVISION LIST</font></h1>
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
							@foreach($divisions as $division)
								<tr>
									<td>{{ $SL }}</td>
									<td>{{ $division->name }}</td>
									<td>
										<a href="javascript:;" class="showUpdateForm" data-id="{{ $division->id }}" data-name="{{ $division->name }}">
											<i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i>
										</a>
										@php
				                        if (in_array($division->id, $foreignDivisionIds)) {
				                          $canDelete = 0;
				                        }
				                        else{
				                          $canDelete = 1;
				                        }   
				                      @endphp
										<a href="javascript:;" class="deleteConfirmation" data-id="{{ $division->id }}" @php if($canDelete==0){ echo "style=\"pointer-events: none;cursor: not-allowed;\"";}@endphp>
											<i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
										</a>
									</td>
								</tr>
								<?php $SL++; ?>
							@endforeach
							<?php  
								if(count($divisions)==0)
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
	<div class="modal-dialog" style="width:700px">
		{!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups')) !!}
			<div class="modal-header" style="background-color:#000;color:#FFF;padding:10px 15px!important;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i style="background:#FFF;" class="fa fa-times" aria-hidden="true"></i></button>
				<h4 class="modal-title">Division Update</h4>
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
        					{!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter division name']) !!}
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
<script src="{{ asset('js/jquery-1.11.1.min.js') }}"></script>
<script type="text/javascript">
	$(document).on('click', '.showUpdateForm', function() {
		if(hasAccess('updateDivisionItem')) {
			$('#id').val($(this).data('id'));
			$('#name').val($(this).data('name'));

			jQuery('#modal-wrapper').modal('show', {backdrop: 'static'});
		}
	});

	function updateData() {
		$.ajax({
			type: 'post',
			url: './updateDivisionItem',
			data: $('form').serialize(),
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
						window.location.href = '{{ url('viewDivision/') }}';
					}, 3000);
				}
			},
			error: function(data) {
				alert(data.errors);
			}
		});
	}

	$(document).on('click', '.deleteConfirmation', function() {
		if(hasAccess('deleteDivisionItem')) {
			$('#id').val($(this).data('id'));
			jQuery('#delete-confirmation-modal').modal('show', {backdrop: 'fade'});
		}
	});

	function deleteData() {
		$.ajax({
			type: 'post',
			url: './deleteDivisionItem',
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
						window.location.href = '{{ url('viewDivision/') }}';
					}, 3000);
				}
			},
			error: function(data) {
				alert(data.errors);
			}
		});
	}
</script>