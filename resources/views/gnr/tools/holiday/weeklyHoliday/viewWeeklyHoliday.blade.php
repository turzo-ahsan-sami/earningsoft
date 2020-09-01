@extends($route['layout'])
@section('title', '| Weekly Holiday')
@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default" style="background-color:#708090;">
			<div class="panel-heading" style="padding-bottom:0px">
				<div class="panel-options">
					<a href="{{ url($route['path'].'/addWeeklyHoliday/') }}" class="btn btn-info pull-right addViewBtn">
						<i class="glyphicon glyphicon-plus-sign addIcon"></i> 
						Add Weekly Holiday
					</a>
				</div>
			</div>

			<div class="panel-body panelBodyView"> 
				<table class="table table-striped table-bordered view" id="areaListView">
					<thead>
						<tr>
							<th width="60">SL#</th>
							<th>Weekly Holiday From</th>
							<th>Weekly Holiday To</th>
							<th>Day</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@foreach($weeklyholidays as $key => $weeklyholiday)

						<tr>
							<td>{{$key+1}}</td>
							<td>{{date('d-m-Y',strtotime($weeklyholiday->dateFrom))}}</td>
							<td>{{-- {{date('d-m-Y',strtotime($weeklyholiday->dateTo))}} --}}{{$weeklyholiday->dateTo}}</td>
							{{-- <td>{{$weeklyholiday->weeklyHolidayIds}}</td> --}}
							<td>
								<?php
								$str = "$weeklyholiday->weeklyHolidayIds";
								$weeklyholidayArr=explode(",",$str);
								foreach ($weeklyholidayArr as $item) {
									if($item=='1'){
										echo 'Saturday'.',';

									}
									elseif($item=='2')
									{
										echo 'Sunday'.',';
									}
									elseif($item=='3')
									{
										echo 'Monday'.',';
									}
									elseif($item=='4')
									{
										echo 'Tuesday'.',';
									} 
									elseif($item=='5')
									{
										echo 'Wednesday'.',';
									} 
									elseif($item=='6')
									{
										echo 'Truesday'.',';
									} 
									elseif($item=='7')
									{
										echo 'Friday';
									}       



								}


								?> 
							</td>
							<td>
								<a href="javascript:;" class="showUpdateForm" data-id="{{$weeklyholiday->id}}">
									<i class="fa fa-pencil fa-lg" aria-hidden="true"></i>
								</a>
								<a href="javascript:;" class="deleteConfirmation" data-id="{{$weeklyholiday->id}}">
									<i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
								</a>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="show-update-modal">
	<div class="modal-dialog" style="width:1200px;margin-top:10%">
		{!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups')) !!}


		<div class="modal-heading" style="background-color:#000;color:#FFF;padding:10px 15px!important;">
			<h4 class="modal-title">Weekly Holiday Update</h4>
			<div class="panel-options">
				<a class="close" href="#" data-dismiss="modal">X</a>
			</div>
		</div>

		<div class="modal-content" style="border:none!important;padding:5px 30px!important;">
			<div class="modal-body">
				<div class="form-group hidden">
					<div class="col-sm-8 col-sm-offset-2">
						{!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text', 'readonly']) !!}
					</div>
				</div>
			{{-- 	<div class="form-group">
					{!! Form::label('applicableFor', 'Applicable For:', ['class' => 'col-sm-4 control-label']) !!}
					<div class="col-sm-8">
						{!! Form::radio('applicableFor', 'org', true) !!}
						{!! Form::label('organization', 'Organization', ['class' => 'control-label']) !!}  &nbsp &nbsp
						{!! Form::radio('applicableFor', 'branch', false) !!}
						{!! Form::label('branch', 'Branch', ['class' => 'control-label']) !!}
						&nbsp &nbsp
						{!! Form::radio('applicableFor', 'samity', false) !!}
						{!! Form::label('samity', 'Samity', ['class' => 'control-label']) !!}
					</div>
				</div> --}}


				<div class="form-group">
					{!! Form::label('weeklyHolidayIds', 'Day:', ['class' => 'col-sm-2 control-label']) !!}
					<div class="col-sm-8">
						{!! Form::checkbox('weeklyHolidayIds[]', '1', false) !!}
						{!! Form::label('saturday', 'Saturday', ['class' => 'control-label']) !!}  &nbsp &nbsp
						{!! Form::checkbox('weeklyHolidayIds[]', '2', false) !!}
						{!! Form::label('sunday', 'Sunday', ['class' => 'control-label']) !!}  &nbsp &nbsp
						{!! Form::checkbox('weeklyHolidayIds[]', '3', false) !!}
						{!! Form::label('monday', 'Monday', ['class' => 'control-label']) !!}
						&nbsp &nbsp
						{!! Form::checkbox('weeklyHolidayIds[]', '4', false) !!}
						{!! Form::label('tuesday', 'Tuesday', ['class' => 'control-label']) !!}
						&nbsp &nbsp
						{!! Form::checkbox('weeklyHolidayIds[]', '5', false) !!}
						{!! Form::label('wednesday', 'Wednesday', ['class' => 'control-label']) !!}
						&nbsp &nbsp
						{!! Form::checkbox('weeklyHolidayIds[]', '6', false) !!}
						{!! Form::label('thursday', 'Thursday', ['class' => 'control-label']) !!}
						&nbsp &nbsp
						{!! Form::checkbox('weeklyHolidayIds[]', '7', false) !!}
						{!! Form::label('thursday', 'Friday', ['class' => 'control-label']) !!}
					</div>
				</div>


			{{-- 	<div class="form-group">
					{!! Form::label('holidayDateFrom', 'Holiday Date From:', ['class' => 'col-sm-4 control-label']) !!}
					<div class="col-sm-8">
						{!! Form::text('dateFrom',null,['id'=>'dateFrom','class'=>'form-control','style'=>'cursor:pointer;','readonly']) !!}
					</div>
				</div> --}}

				<div class="form-group">

					{!! Form::label('dateFrom', 'Weekly Holiday Date From:', ['class' => 'col-sm-2 control-label']) !!}
					<div class="col-sm-6">
						{!! Form::text('dateFrom',null,['id'=>'dateFrom','class'=>'form-control','style'=>'cursor:pointer;','readonly']) !!}
					</div>
				</div>




			</div>
		</div>
		<div class="modal-footer" style="background-color:#FFF;color:#FFF;">
			<button type="submit" class="btn btn-info animated slideInRight" style="margin-bottom:0!important;">Save</button>
			<button type="button" class="btn btn-white animated slideInRight" data-dismiss="modal" style="margin-bottom:0!important;">Close</button>
		</div>
		{!! Form::close() !!}
	</div>
</div>



<div class="modal fade" id="delete-confirmation-modal" style="padding-top: 10%;">
	<div class="modal-dialog">
		<div class="modal-header">
			<h4 class="modal-title">Delete Confirmation</h4>
			<a class="close" href="#" data-dismiss="modal">X</a>
		</div>
		<div class="modal-content">
			<div class="modal-body">
				Are you sure to delete this item?
			</div>
		</div>
		<div class="modal-footer">

			<button id="yesBtn" type="button" class="btn btn-info animated slideInRight" style="margin-bottom:0!important;" >Yes</button>
			<button id="noBtn" type="button" class="btn btn-white animated slideInRight" data-dismiss="modal" style="margin-bottom:0!important;">No</button>
		</div>
	</div>
</div>


<style type="text/css">
.branchDiv,.samityDiv{
	display: none;
}
</style>


{{-- <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script> --}}
<script type="text/javascript">
	$(document).ready(function() {
		/* hide/show branch,samity div*/
		$(document).on('change', 'input[name="applicableFor"]', function(event) {
			var selectedValue = $('input[name="applicableFor"]:checked').val();
			if (selectedValue=='org') {
				$(".branchDiv").hide();
				$(".samityDiv").hide();
			}
			else if(selectedValue=='branch'){
				$(".branchDiv").show();
				$(".samityDiv").hide();  
			}
			else if(selectedValue=='samity'){
				$(".branchDiv").hide();
				$(".samityDiv").show();  
			}
		});
		/* end hide/show branch,samity div*/

		/*update modal*/
		$(document).on('click', '.showUpdateForm', function() {
			$("#id").val($(this).data('id'));
			$.ajax({
				type: 'post',
				url: './getWeeklyHolidayDetails',
				data: {
					'_token': "{{ csrf_token() }}",
					'id': $(this).data('id')
				}, 
				dataType: 'json',
				success: function(data) {
					//$("input[name='weeklyHolidayIds[]'][value='"+data['checkboxValue']+"']").prop('checked', true).trigger('change');
					$("input:checkbox[name='weeklyHolidayIds[]']").each(function(){
						
						if(jQuery.inArray($(this).val(),data['checkboxValue']) !== -1)
							
							$(this).prop( "checked", true );

					});
					$("#dateFrom").val(data['dateFrom']);
					jQuery('#show-update-modal').modal('show', {backdrop: 'static'});
					//console.log(data['checkboxValue']);
				},
				error: function(_response) {
					alert(_response.errors);
				}
			});
		});
		/*end update modal*/

		/*update data*/
		$('form').submit(function(event) {
			event.preventDefault();

			$.ajax({
				url: './updateWeeklyHoliday',
				type: 'POST',
				dataType: 'json',
				data: $(this).serialize(),
			})
			.done(function(data) {
            		// Print Error
            		if(data.errors) {
            			$.each(data.errors, function(name, error) {
            				$("#"+name).after("<p class='error' style='color:red;'>* "+data.errors[name]+"</p>");
            			});
            		}
            		else{
            			toastr.success(data.responseText, data.responseTitle, opts);                            
            			setTimeout(function(){
            				location.reload();
            			}, 3000);
            		}

            		
            	})
			.fail(function() {
				alert("error");
			});


		});
		/*end update data*/

		$("#dateFrom").datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange : "c-10:c+10",
			dateFormat: 'dd-mm-yy',
			onSelect: function() {
				var date = $(this).datepicker('getDate');
				$("#dateTo").datepicker('option','minDate',date);
				$(this).closest('div').find('.error').remove();
			}
		});

		$("#dateTo").datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange : "c-10:c+10",
			dateFormat: 'dd-mm-yy',
			onSelect: function() {
				var date = $(this).datepicker('getDate');
				$("#dateFrom").datepicker('option','maxDate',date);
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


            /*Delete Modal*/
            $(document).on('click', '.deleteConfirmation', function() {
            	$('#id').val($(this).data('id'));
            	jQuery('#delete-confirmation-modal').modal('show', {backdrop: 'fade'});
            });
            /*End Delete Modal*/

            /*Delete data*/
            $("#yesBtn").click(function(event) {
            	$.ajax({
            		url: './deleteWeeklyHoliday',
            		type: 'POST',
            		dataType: 'json',
            		data: {
            			_token: "{{csrf_token()}}",
            			id: $('#id').val()
            		},
            	})
            	.done(function(data) {
            		toastr.success(data.responseText, data.responseTitle, opts);

            		setTimeout(function(){
            			location.reload();
            		}, 3000);
            	})
            	.fail(function() {
            		alert("error");
            	})


            });
            /*End Delete data*/


        }); /*end ready*/



    </script>
    @endsection