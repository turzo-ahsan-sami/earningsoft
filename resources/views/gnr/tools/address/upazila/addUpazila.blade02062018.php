@extends('layouts/gnr_layout')
@section('title', '| Add Upazila')
@section('content')
	<div class="row add-data-form">
		<div class="col-md-12">
            <div class="col-md-8 col-md-offset-2 fullbody">
            	<div class="viewTitle" style="border-bottom:1px solid white;">
                    <a href="{{ url('viewUpazila/') }}" class="btn btn-info pull-right addViewBtn">
                    	<i class="glyphicon glyphicon-th-list viewIcon"></i>
                    	Upazila Lists
                    </a>
                </div>
                <div class="panel panel-default panel-border">
                	<div class="panel-heading">
                        <div class="panel-title">New Upazila</div>
                    </div>
                    <div class="panel-body">
                    	<div class="row">
                            <div class="col-md-12">
                        		{!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups')) !!}
                        			<div class="form-group">
                        				{!! Form::label('name', 'Name:', ['class' => 'col-sm-2 control-label']) !!}
                        				<div class="col-sm-6">
                        					{!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter upazila name']) !!}
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
                                            <?php 
                                                $districtList = array('' => 'Select') + DB::table('gnr_district')->pluck('name', 'id')->all(); 
                                            ?>      
                                            {!! Form::select('districtId', ($districtList), null, array('class'=>'form-control', 'id' => 'districtId')) !!}
                                        </div>
                                    </div>
                        			<div class="form-group">
                        				<div class="col-sm-10 col-sm-offset-2">
                        					{!! Form::submit('Submit', ['id' => 'submit', 'class' => 'btn btn-info']) !!}
                        					<a href="{{ url('viewUpazila/') }}" class="btn btn-danger closeBtn">Close</a>
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
        $('form').submit(function(event){
            event.preventDefault();

            $.ajax({
                type: 'post',
                url: './addUpazilaItem',
                data: $('form').serialize(),
                dataType: 'json',
                success: function(_response) {
                    if(_response.errors) {
                        if(_response.errors['name'])
                            toastr.warning(_response.errors.name, null, opts);
                        if(_response.errors['divisionId'])
                            toastr.warning(_response.errors.divisionId, null, opts);
                        if(_response.errors['districtId'])
                            toastr.warning(_response.errors.districtId, null, opts);
                    } else {
                        $('#name').val('');
                        $('#divisionId').val('');
                        $('.error').addClass('hidden');
                        toastr.success(_response.responseText, _response.responseTitle, opts);
                        
                        setTimeout(function(){
                            window.location.href = '{{ url('viewUpazila/') }}';
                        }, 3000);
                    }
                },
                error: function(_response) {
                    alert(_response.errors);
                }
            });
        });
    });
</script>s