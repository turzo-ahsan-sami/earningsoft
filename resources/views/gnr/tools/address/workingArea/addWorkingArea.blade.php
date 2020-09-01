@extends($route['layout'])
@section('title', '| Add Working Area')
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
	</style>
	<div class="row add-data-form">
		<div class="col-md-12">
            <div class="col-md-8 col-md-offset-2 fullbody">
            	<div class="viewTitle" style="border-bottom:1px solid white;">
                    <a href="{{ url($route['path'].'/viewWorkingArea/') }}" class="btn btn-info pull-right addViewBtn">
                    	<i class="glyphicon glyphicon-th-list viewIcon"></i>
                    	Working Area Lists
                    </a>
                </div>
                <div class="panel panel-default panel-border">
                	<div class="panel-heading">
                        <div class="panel-title">New Working Area</div>
                    </div>
                    <div class="panel-body">
                    	<div class="row">
                            <div class="col-md-12">
                        		{!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups')) !!}
                        			<div class="form-group">
                        				{!! Form::label('name', 'Name:', ['class' => 'col-sm-2 control-label']) !!}
                        				<div class="col-sm-6">
                        					{!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text']) !!}
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
			                                {!! Form::select('branchId', ($damageData['branch']), null, array('class'=>'form-control', 'id' => 'branchId')) !!}
			                            </div>
			                        </div>
			                        <div class="form-group">
			                            {!! Form::label('division', 'Division:', ['class' => 'col-sm-2 control-label']) !!}
			                            <div class="col-sm-6">
				                            {!! Form::select('divisionId', array('' => 'Select') + ($damageData['division']), null, array('class'=>'form-control', 'id' => 'divisionId')) !!}
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
                        			<div class="form-group">
                        				<div class="col-sm-10 col-sm-offset-2">
                        					{!! Form::submit('Submit', ['id' => 'submit', 'class' => 'btn btn-info']) !!}
                        					<a href="{{url($route['path'].'/viewWorkingArea/')}}" class="btn btn-danger closeBtn">Close</a>
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
               			$('#districtId').empty().append('<option value="">Select</option>');

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
               			$('#upazilaId').empty().append('<option value="">Select</option>');

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
               			$('#unionId').empty().append('<option value="">Select</option>');

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
               			$('#villageId').empty().append('<option value="">Select</option>');

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

		$('form').submit(function(event){
			event.preventDefault();

			$.ajax({
				type: 'post',
				url: './addWorkingAreaItem',
				data: $('form').serialize(),
				dataType: 'json',
				success: function(_response) {
					if(_response.errors) {
						if(_response.errors['name'])
							toastr.warning(_response.errors.name, null, opts);
						if(_response.errors['code'])
							toastr.warning(_response.errors.code, null, opts);
						if(_response.errors['branchId'])
							toastr.warning(_response.errors.branchId, null, opts);
						if(_response.errors['divisionId'])
							toastr.warning(_response.errors.divisionId, null, opts);
						if(_response.errors['districtId'])
							toastr.warning(_response.errors.districtId, null, opts);
						if(_response.errors['upazilaId'])
							toastr.warning(_response.errors.upazilaId, null, opts);
						if(_response.errors['unionId'])
							toastr.warning(_response.errors.unionId, null, opts);
						if(_response.errors['villageId'])
							toastr.warning(_response.errors.villageId, null, opts);
					} else {
						$('#name').val('');
						$('#code').val('');
						$('#branchId').val('');
						$('#divisionId').val('');
						$('#districtId').val('');
						$('#upazilaId').val('');
						$('#unionId').val('');
						$('#villageId').val('');
						$('.error').addClass('hidden');
						toastr.success(_response.responseText, "Success!", opts);
						
						setTimeout(function(){
							window.location.href = '{{ url($route['path'].'/viewWorkingArea/') }}';
						}, 3000);
					}
				},
				error: function(_response) {
					alert(_response.errors);
				}
			});
		});
	});
</script>