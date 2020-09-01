@extends($route['layout'])
@section('title', '| Org./Branch/Samity Holiday')
@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default" style="background-color:#708090;">
				<div class="panel-heading" style="padding-bottom:0px">
					<div class="panel-options">
						<a href="{{ url($route['path'].'/addOrgBranchSamityHoliday/') }}" class="btn btn-info pull-right addViewBtn">
							<i class="glyphicon glyphicon-plus-sign addIcon"></i> 
							Add Holiday
						</a>
					</div>
				</div>
				
				<div class="panel-body panelBodyView"> 
					<table class="table table-striped table-bordered view" id="areaListView">
						<thead>
							<tr>
								<th width="60">SL#</th>
								<th>Applicable For</th>
								<th>Name</th>
								<th>Holiday From</th>
								<th>Holiday To</th>
								<th>Holiday Type</th>
								<th>Description</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@foreach($holidays as $key => $holiday)
							@php
							if ($holiday->isOrgHoliday==1) {
								$applicableFor = "Organization";
								$applicableForName = DB::table('gnr_company')->where('id',$holiday->ogrIdFk)->value('name');
							}
							elseif($holiday->isBranchHoliday==1){
								$applicableFor = "Branch";
								$applicableForName = DB::table('gnr_branch')->where('id',$holiday->branchIdFk)->value('name');
							}
							elseif($holiday->isSamityHoliday==1){
								$applicableFor = "Samity";
								$applicableForName = DB::table('mfn_samity')->where('id',$holiday->samityIdFk)->value('name');
							}
							@endphp
								<tr>
									<td>{{$key+1}}</td>
									<td>{{$applicableFor}}</td>
									<td class="name">{{$applicableForName}}</td>
									<td>{{date('d-m-Y',strtotime($holiday->dateFrom))}}</td>
									<td>{{date('d-m-Y',strtotime($holiday->dateTo))}}</td>
									<td>{{$holiday->holidayType}}</td>
									<td>{{$holiday->description}}</td>
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

	<div class="modal fade" id="show-update-modal">
		<div class="modal-dialog" style="width:700px;margin-top:10%">
			{!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups')) !!}
				

				<div class="modal-heading" style="background-color:#000;color:#FFF;padding:10px 15px!important;">
					<h4 class="modal-title">Holiday Update</h4>
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
						<div class="form-group">
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
        			</div>
        			@php
                        $branches = DB::table('gnr_branch')->select('name','id','branchCode')->orderBy('branchCode')->get();
                    @endphp
                    <div class="form-group branchDiv">
                        {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                           <select id="branch" name="branch" class="form-control">
                               <option value=''>Select</option>
                               @foreach($branches as $branch)
                                    <option value='{{$branch->id}}'>{{str_pad($branch->branchCode,3,'0',STR_PAD_LEFT) .'-'.$branch->name}}</option>
                               @endforeach
                           </select>
                        </div>
                    </div>

                    @php
	                    $samities = DB::table('mfn_samity')->where('status','1')->select('name','id','code')->orderBy('code')->get();
	                @endphp
	                <div class="form-group samityDiv">
	                    {!! Form::label('samity', 'Samity:', ['class' => 'col-sm-4 control-label']) !!}
	                    <div class="col-sm-8">
	                       <select id="samity" name="samity" class="form-control">
	                           <option value=''>Select</option>
	                           @foreach($samities as $samity)
	                                <option value='{{$samity->id}}'>{{$samity->code.'-'.$samity->name}}</option>
	                           @endforeach
	                       </select>
	                    </div>
	                </div>
	                <div class="form-group">
	                    {!! Form::label('holidayDateFrom', 'Holiday Date From:', ['class' => 'col-sm-4 control-label']) !!}
	                    <div class="col-sm-8">
	                        {!! Form::text('dateFrom',null,['id'=>'dateFrom','class'=>'form-control','style'=>'cursor:pointer;','readonly']) !!}
	                    </div>
	                </div>
	                <div class="form-group">
	                    {!! Form::label('holidayDateTo', 'Holiday Date To:', ['class' => 'col-sm-4 control-label']) !!}
	                    <div class="col-sm-8">
	                        {!! Form::text('dateTo',null,['id'=>'dateTo','class'=>'form-control','readonly','style'=>'cursor:pointer;']) !!}
	                    </div>
	                </div>
	                @php
	                    $holidayTypes = array(''=>'Select','Holiday'=>'Holiday','Others'=>'Others');
	                @endphp
	                <div class="form-group">
	                    {!! Form::label('holidayType', 'Holiday Type:', ['class' => 'col-sm-4 control-label']) !!}
	                    <div class="col-sm-8">
	                        {!! Form::select('holidayType',$holidayTypes,null,['id'=>'holidayType','class'=>'form-control']) !!}
	                    </div>
	                </div>
	                <div class="form-group">
	                    {!! Form::label('description', 'Description:', ['class' => 'col-sm-4 control-label']) !!}
	                    <div class="col-sm-8">
	                        {!! Form::textArea('description',null,['id'=>'description','class'=>'form-control','rows'=>2]) !!}
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


{{-- Delete Modal --}}
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
{{-- End Delete Modal --}}

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
					url: './getOrgHolidayDetaisToUpdate',
					data: {
			        	'_token': "{{ csrf_token() }}",
			        	'id': $(this).data('id')
			       	}, 
					dataType: 'json',
					success: function(data) {
						$("input[name='applicableFor'][value='"+data['radioValue']+"']").prop('checked', true).trigger('change');
						$("#branch").val(data['holiday'].branchIdFk);
						$("#samity").val(data['holiday'].samityIdFk);
						$("#dateFrom").val(data['dateFrom']);
						$("#dateTo").val(data['dateTo']);
						$("#holidayType").val(data['holiday'].holidayType);
						$("#holidayType").val(data['holiday'].holidayType);
						$("#description").val(data['holiday'].description);
						jQuery('#show-update-modal').modal('show', {backdrop: 'static'});
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
            		url: './updateOrgBranchSamityHoliday',
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
        		url: './deleteOrgBranchSamityHoliday',
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