@extends('layouts/gnr_layout')
@section('title', '| Region')
@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default" style="background-color:#708090;">
				<div class="panel-heading" style="padding-bottom:0px">
					<div class="panel-options">
						<a href="{{ url('addRegion/') }}" class="btn btn-info pull-right addViewBtn">
							<i class="glyphicon glyphicon-plus-sign addIcon"></i> 
							Add Region
						</a>
					</div>
					<h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">REGION LIST</font></h1>
				</div>
				<div class="panel-body panelBodyView"> 
					<table class="table table-striped table-bordered" id="regionListView">
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
							@foreach($regions as $region)
								<tr>
									<td>{{ $SL }}</td>
									<td>{{ $region->name }}</td>
									<td>{{ str_pad($region->code, 6, 0, STR_PAD_LEFT) }}</td>
									<td style="text-align:left;">
										<?php 
											$i = 0; 
											$zoneIdStr = ''; 
										?>
										@foreach($region->zoneId as $zoneId)
											<?php
												$zoneList = DB::table('gnr_zone')->select('name')->where('id', $zoneId)->first();
												$zoneIdStr .= $zoneId;
												
												if($i!=count($region->zoneId)-1)
													$zoneIdStr .= ',';
											?>
											{{ $zoneList->name }}
											@if($i!=count($region->zoneId)-1)
												{{ ', ' }} 
											@endif
											<?php $i++; ?>
										@endforeach
									</td>
									<td>
										<a href="javascript:;" class="showUpdateForm" data-id="{{ $region->id }}" data-name="{{ $region->name }}" data-code="{{ $region->code }}" data-zoneid="{{ $zoneIdStr }}">
											<i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i>
										</a>
										<a href="javascript:;" class="deleteConfirmation" data-id="{{ $region->id }}">
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
			<div class="modal-header animated" style="background-color:#000;color:#FFF;padding:10px 15px!important;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i style="background:#FFF;" class="fa fa-times" aria-hidden="true"></i></button>
				<h4 class="modal-title">Region Update</h4>
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
        					{!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter region name']) !!}
        					<p id='name' style="max-height:3px;"></p>
        				</div>
        			</div>
        			<div class="form-group">
        				{!! Form::label('code', 'Code:', ['class' => 'col-sm-2 control-label']) !!}
        				<div class="col-sm-6">
        					{!! Form::text('code', $value = null, ['class' => 'form-control', 'id' => 'code', 'type' => 'text', 'data-mask' => '999999', 'placeholder' => 'Enter region code']) !!}
        					<p id='code' style="max-height:3px;"></p>
        				</div>
        			</div>
        			<div class="form-group">
                        {!! Form::label('zone', 'Zone:', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10" style="padding-left:0!important;">
                            <?php 
                                //  GET ALL THE ZONES.
                                $zoneList = DB::table('gnr_zone')->select('name', 'code', 'id')->get(); 
                            ?>
                            @foreach($zoneList as $zone)
                                <div class="col-sm-3" style="padding-right:5px!important;">
                                    {!! Form::checkbox('zoneId[]', ($zone->id), false, array('class' => 'zoneId cbr', 'onchange' => 'checkDuplicate($(this).val())')) !!}
                                    <span style="font-size:11px;">  
                                        {!! Form::label(Illuminate\Support\Str::lower($zone->name), (str_pad($zone->code, 6, '0', STR_PAD_LEFT) . ' - ' . $zone->name)) !!}
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
		if(hasAccess('updateRegionItem')) {
			//	CLEAR ALL THE CHECK SIGN FROM ALL CHECKBOXES.
			$(".modal-content .cbr-replaced .cbr-input").find('input').each(function() {
				$(this).parent('.cbr-input').parent().removeClass('cbr-checked');
			});

			$('#id').val($(this).data('id'));
			$('#name').val($(this).data('name'));
			$('#code').val($(this).data('code'));
			
			var zoneIdStr = $(this).data('zoneid');
			zoneIdStr = zoneIdStr.toString();
			
			var zoneIdArr = zoneIdStr.split(",");

			$(".modal-content .cbr-replaced .cbr-input").find('input').each(function() {
				var getZoneId = $(this).val();

				if(zoneIdArr.indexOf(getZoneId)>-1==true) {
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
			url: './updateRegionItem',
			data: $('form').serialize(),
			dataType: 'json',
			success: function(data) {
				if(data.errors) {
					if(data.errors['name'])
						toastr.warning(data.errors.name, null, opts);
					if(data.errors['code'])
						toastr.warning(data.errors.code, null, opts);
					if(data.errors['zoneid'])
						toastr.warning(data.errors.zoneid, null, opts);
				} else {
					$('#name').val('');
					$('#code').val('');
					$('#zoneid').val('');
					$('.error').addClass('hidden');
					toastr.success(data.responseText, "Success!", opts);
					
					setTimeout(function(){
						window.location.href = '{{ url('viewRegion/') }}';
					}, 6000);
				}
			},
			error: function(data) {
				alert(data.errors);
			}
		});
	}

	$(document).on('click', '.deleteConfirmation', function() {
		if(hasAccess('deleteRegionItem')) {
			$('#id').val($(this).data('id'));
			jQuery('#delete-confirmation-modal').modal('show', {backdrop: 'fade'});
		}
	});

	function deleteData() {
		$.ajax({
			type: 'post',
			url: './deleteRegionItem',
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
						window.location.href = '{{ url('viewRegion/') }}';
					}, 6000);
				}
			},
			error: function(data) {
				alert(data.errors);
			}
		});
	}

	function checkDuplicate(getZoneId) {

		//alert(getZoneId);

		$.ajax({
			type: 'post',
			url: './checkRegionAssignAvailability',
			data: {
			      '_token': $('input[name=_token]').val(),
			      'getZoneId': getZoneId
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
					$('#assign-check-modal .modal-body').append('<p style="color:#000;">Are you sure to assign this zone in this region?</p>');
					
					$('#assign-check-modal .modal-footer #noBtn').addClass('assignNotPermitted');
					$('#assign-check-modal .modal-footer #yesBtn').addClass('assignPermitted');
					$('#assign-check-modal .modal-footer #yesBtn').removeAttr('onclick');
					$('#assign-check-modal .modal-footer #yesBtn').attr('data-dismiss', 'modal');

					jQuery('#assign-check-modal').modal('show', {backdrop: 'fade'});

					$('.assignNotPermitted').on('click', function(){
						//alert(getZoneId);
						
						$(".modal-content .cbr-replaced .cbr-input").find('input').each(function() {
							var searchZoneId = $(this).val();

							if(getZoneId==searchZoneId) {
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