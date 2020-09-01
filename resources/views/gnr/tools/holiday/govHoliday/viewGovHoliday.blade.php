@extends($route['layout'])
@section('title', '| Fixed Gov. Holiday List')
@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default" style="background-color:#708090;">
				<div class="panel-heading" style="padding-bottom:0px">
					<div class="panel-options">
						<a href="{{ url($route['path'].'/addGovHoliday/') }}" class="btn btn-info pull-right addViewBtn">
							<i class="glyphicon glyphicon-plus-sign addIcon"></i> 
							Add Fixed Gov. Holiday
						</a>
					</div>
					<h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">Fixed GOV. HOLIDAY LIST</font></h1>
				</div>				
				<div class="panel-body panelBodyView"> 
					<table class="table table-striped table-bordered view" id="areaListView">
						<thead>
							<tr>
								<th width="60">SL#</th>
								<th>Title</th>
								<th>Date</th>
								<th>Description</th>
								<th width="80">Action</th>
							</tr>
						</thead>
						<tbody>
							@foreach($holidays as $key => $holiday)
							<tr>
								<td>{{$key+1}}</td>
								<td class="name" width="20%">{{$holiday->title}}</td>
								<td width="100">{{$holiday->date}}</td>
								<td class="name">{{$holiday->description}}</td>
								<td>
									<a href="javascript:;" class="showUpdateForm" data-id="{{ $holiday->id }}">
										<i class="fa fa-pencil fa-lg" aria-hidden="true"></i>
									</a>
									<a href="javascript:;" class="deleteConfirmation" data-id="{{ $holiday->id }}">
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

{{-- Update Modal --}}
	<div class="modal fade" id="show-update-modal">
		<div class="modal-dialog" style="width:500px;margin-top:10%">
			{!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups')) !!}
				

				<div class="modal-heading" style="background-color:#000;color:#FFF;padding:10px 15px!important;">
					<h4 class="modal-title">Holiday Update</h4>
					<div class="panel-options">
						<a class="close" href="#" data-dismiss="modal">X</a>
					</div>
				</div>	

				<div class="modal-content" style="border:none!important;padding:5px 30px!important;">
					<div class="modal-body">
						
						{!! Form::hidden('holidayId',null,['id'=>'holidayId']) !!}

						<div class="form-group">
	        				{!! Form::label('title', 'Title:', ['class' => 'col-sm-4 control-label']) !!}
	        				<div class="col-sm-8">
	        					{!! Form::text('title', null, ['class' => 'form-control', 'id' => 'title','placeholder' => 'Enter Title']) !!}
	        				</div>
	        			</div>
	        			<div class="form-group">
	        				{!! Form::label('date', 'Date:', ['class' => 'col-sm-4 control-label']) !!}
	        				<div class="col-sm-8">
	        					{!! Form::text('date', null, ['class' => 'form-control', 'id' => 'date','placeholder' => 'Enter Start Date','readonly','style'=>'cursor:pointer;']) !!}
	        				</div>
	        			</div>
	        			
	        			<div class="form-group">
	        				{!! Form::label('description', 'Description:', ['class' => 'col-sm-4 control-label']) !!}
	        				<div class="col-sm-8">
	        					{!! Form::textArea('description', null, ['class' => 'form-control', 'id' => 'description','placeholder' => 'Enter Description','rows'=>2]) !!}
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
	{{-- End Update Modal --}}


	<style type="text/css">
		.ui-datepicker-year{
	        display:none;
	    }
	</style>

	{{--  <script src="{{ asset('js/jquery-1.11.1.min.js') }}"></script> --}} 
	<script type="text/javascript">
		$(document).ready(function() {

			$('form').submit(function(event) {
				event.preventDefault();
				
				$.ajax({
					url: './updateGovHoliday',
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
	                    }, 2000);
					}

					
				})
				.fail(function() {
					alert("error");
				});
				
			});

			$(document).on('click', '.showUpdateForm', function() {

				$("#holidayId").val($(this).data('id'));
				$.ajax({
					type: 'post',
					url: './getHolidayDetails',
					data: {
			        	'_token': "{{ csrf_token() }}",
			        	'id': $(this).data('id')
			       	}, 
					dataType: 'json',
					success: function(holiday) {

						$("#title").val(holiday.title);
						$("#date").val(holiday.date);
						$("#description").val(holiday.description);

						
						jQuery('#show-update-modal').modal('show', {backdrop: 'static'});
						
					},
					error: function(_response) {
						alert(_response.errors);
					}
				});
			});


			$("#date").datepicker({
                changeMonth: true,
                changeYear: true,
                yearRange : "c:c",
                dateFormat: 'dd-mm',
                onSelect: function() {                    
                    $(this).css('border-color', 'black');                                      
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


             /*Delete Holiday*/
        $("#yesBtn").click(function(event) {
        	$.ajax({
        		url: './deleteGovHoliday',
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
        /*End Delete Holiday*/




		}); /*end ready*/
		

	</script>
@endsection


	{{-- Delete Modal --}}
<div class="modal fade" id="delete-confirmation-modal">
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
      <div class="form-group hidden">
        <div class="col-sm-6 col-sm-offset-2">
          {!! Form::text('id', null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text', 'readonly']) !!}
        </div>
      </div>
      <button id="yesBtn" type="button" class="btn btn-info animated slideInRight" style="margin-bottom:0!important;" >Yes</button>
      <button id="noBtn" type="button" class="btn btn-white animated slideInRight" data-dismiss="modal" style="margin-bottom:0!important;">No</button>
    </div>
  </div>
</div>
{{-- End Delete Modal --}}