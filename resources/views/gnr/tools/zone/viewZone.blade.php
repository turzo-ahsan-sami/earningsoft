@extends('layouts/gnr_layout')
@section('title', '| Zone')
@section('content')

@php
	$zoneIdsArray = DB::table('gnr_region')->pluck('zoneId')->toArray();

	$zoneForeignIds = array();

	foreach ($zoneIdsArray as $zoneIdArray) {
		$zoneIdArrayString = str_replace(['[',']','"'], '', $zoneIdArray);
		$zoneIds = explode(',', $zoneIdArrayString);
		foreach ($zoneIds as $zoneId) {
			array_push($zoneForeignIds,(int) $zoneId);
		}
	}

	$zoneForeignIds = array_unique($zoneForeignIds);

@endphp



	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default" style="background-color:#708090;">
				<div class="panel-heading" style="padding-bottom:0px">
					<div class="panel-options">
						<a href="{{ url('addZone/') }}" class="btn btn-info pull-right addViewBtn">
							<i class="glyphicon glyphicon-plus-sign addIcon"></i> 
							Add Zone
						</a>
					</div>

					<h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">ZONE LIST</font></h1>
				</div>
				<div class="panel-body panelBodyView"> 
					<table class="table table-striped table-bordered" id="zoneListView">
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
							@foreach($zones as $zone)
								<tr>
									<td>{{ $SL }}</td>
									<td>{{ $zone->name }}</td>
									<td>{{ str_pad($zone->code, 5, 0, STR_PAD_LEFT) }}</td>
									<td style="text-align:left;">
										<?php 
											$i = 0;
											$areaIdStr = ''; 
										?>
										@foreach($zone->areaId as $areaId)
											<?php
												$areaList = DB::table('gnr_area')->select('name')->where('id', $areaId)->first();
												$areaIdStr .= $areaId;
												
												if($i!=count($zone->areaId)-1)
													$areaIdStr .= ',';
											?>
											{{ $areaList->name }}
											@if($i!=count($zone->areaId)-1)
												{{ ', ' }} 
											@endif
											<?php $i++; ?>
										@endforeach
									</td>
									<td>
										<a href="javascript:;" class="showUpdateForm" data-id="{{ $zone->id }}" data-name="{{ $zone->name }}" data-code="{{ $zone->code }}" data-areaid="{{ $areaIdStr }}">
											<i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i>
										</a>

										@php
				                        if (in_array($zone->id, $zoneForeignIds)) {
				                          $canDelete = 0;
				                        }
				                        else{
				                          $canDelete = 1;
				                        }   
				                      @endphp

										<a href="javascript:;" class="deleteConfirmation" data-id="{{ $zone->id }}" @php if($canDelete==0){ echo "style=\"pointer-events: none;cursor: not-allowed;\"";}@endphp>
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
		{!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups')) !!}
			<div class="modal-header" style="background-color:#000;color:#FFF;padding:10px 15px!important;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i style="background:#FFF;" class="fa fa-times" aria-hidden="true"></i></button>
				<h4 class="modal-title">Zone Update</h4>
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
        					{!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter zone name']) !!}
        				</div>
        			</div>
        			<div class="form-group">
        				{!! Form::label('code', 'Code:', ['class' => 'col-sm-2 control-label']) !!}
        				<div class="col-sm-6">
        					{!! Form::text('code', $value = null, ['class' => 'form-control', 'id' => 'code', 'type' => 'text', 'data-mask' => '99999', 'placeholder' => 'Enter zone code']) !!}
        				</div>
        			</div>
        			<div class="form-group">
                        {!! Form::label('area', 'Area:', ['class' => 'col-sm-2 control-label', 'style' => 'padding-top:0!important']) !!}
                        <div class="col-sm-10" style="padding-left:0!important;">
                            <?php 
                                //  GET ALL THE AREAS.
                                $areaList = DB::table('gnr_area')->select('name', 'code', 'id')->get(); 
                            ?>
                            @foreach($areaList as $area)
                                <div class="col-sm-3" style="padding-right:5px!important;">
                                    {!! Form::checkbox('areaId[]', ($area->id), false, array('class' => 'areaId cbr', 'onchange' => 'checkDuplicate($(this).val())')) !!}
                                    <span style="font-size:11px;">  
                                        {!! Form::label(Illuminate\Support\Str::lower($area->name), (str_pad($area->code, 5, '0', STR_PAD_LEFT) . ' - ' . $area->name)) !!}
                                    </span>
                                </div>
                            @endforeach
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
	$(document).on('click', '.showUpdateForm', function() {
		if(hasAccess('updateZoneItem')) {
			//	CLEAR ALL THE CHECK SIGN FROM ALL CHECKBOXES.
			$(".modal-content .cbr-replaced .cbr-input").find('input').each(function() {
				$(this).parent('.cbr-input').parent().removeClass('cbr-checked');
			});

			$('#id').val($(this).data('id'));
			$('#name').val($(this).data('name'));
			$('#code').val($(this).data('code'));
			
			var areaIdStr = $(this).data('areaid');
			areaIdStr = areaIdStr.toString();
			
			var areaIdArr = areaIdStr.split(",");

			$(".modal-content .cbr-replaced .cbr-input").find('input').each(function() {
				var getAreaId = $(this).val();

				if(areaIdArr.indexOf(getAreaId)>-1==true) {
					$(this).parent('.cbr-input').parent().addClass('cbr-checked');
					$(this).attr('checked', 'checked');
				}
	    	});

			jQuery('#modal-wrapper').modal('show', {backdrop: 'static'});
		}
	});

	function updateData() {
		$.ajax({
			type: 'post',
			url: './updateZoneItem',
			data: $('form').serialize(),
			dataType: 'json',
			success: function(data) {
				if(data.errors) {
					if(data.errors['name'])
						toastr.warning(data.errors.name, null, opts);
					if(data.errors['code'])
						toastr.warning(data.errors.code, null, opts);
					if(data.errors['areaId'])
						toastr.warning(data.errors.areaId, null, opts);
				} else {
					$('#name').val('');
					$('#code').val('');
					$('#areaId').val('');
					$('.error').addClass('hidden');
					toastr.success(data.responseText, "Success!", opts);
					
					setTimeout(function(){
						window.location.href = '{{ url('viewZone/') }}';
					}, 6000);
				}
			},
			error: function(data) {
				alert(data.errors);
			}
		});
	}

	$(document).on('click', '.deleteConfirmation', function() {
		if(hasAccess('deleteZoneItem')) {
			$('#id').val($(this).data('id'));
			jQuery('#delete-confirmation-modal').modal('show', {backdrop: 'fade'});
		}
	});

	function deleteData() {
		
		$.ajax({
			type: 'post',
			url: './deleteZoneItem',
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
						window.location.href = '{{ url('viewZone/') }}';
					}, 3000);
				}
			},
			error: function(data) {
				alert(data.errors);
			}
		});
	}

	function checkDuplicate(getAreaId) {

		//alert(getAreaId);

		$.ajax({
			type: 'post',
			url: './checkZoneAssignAvailability',
			data: {
			      '_token': $('input[name=_token]').val(),
			      'getAreaId': getAreaId
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
					$('#assign-check-modal .modal-body').append('<p style="color:#000;">Are you sure to assign this area in this zone?</p>');
					
					$('#assign-check-modal .modal-footer #noBtn').addClass('assignNotPermitted');
					$('#assign-check-modal .modal-footer #yesBtn').addClass('assignPermitted');
					$('#assign-check-modal .modal-footer #yesBtn').removeAttr('onclick');
					$('#assign-check-modal .modal-footer #yesBtn').attr('data-dismiss', 'modal');

					jQuery('#assign-check-modal').modal('show', {backdrop: 'fade'});

					$('.assignNotPermitted').on('click', function(){
						//alert(getAreaId);
						
						$(".modal-content .cbr-replaced .cbr-input").find('input').each(function() {
							var searchAreaId = $(this).val();

							if(getAreaId==searchAreaId) {
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