@extends('layouts/gnr_layout')
@section('title', '| Add Area')
@section('content')
	<div class="row add-data-form">
		<div class="col-md-12">
            <div class="col-md-8 col-md-offset-2 fullbody">
            	<div class="viewTitle" style="border-bottom:1px solid white;">
                    <a href="{{ url('viewZone/') }}" class="btn btn-info pull-right addViewBtn">
                    	<i class="glyphicon glyphicon-th-list viewIcon"></i>
                    	Zone Lists
                    </a>
                </div>
                <div class="panel panel-default panel-border">
                	<div class="panel-heading">
                        <div class="panel-title">New Zone</div>
                    </div>
                    <div class="panel-body">
                    	<div class="row">
                            <div class="col-md-12">
                        		{!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups')) !!}
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
			                            {!! Form::label('area', 'Area:', ['class' => 'col-sm-2 control-label']) !!}
			                            <div class="col-sm-10" style="padding-left:0!important;">
                                            <?php 
                                                //  GET ALL THE AREAS.
                                                $areaList = DB::table('gnr_area')->select('name', 'code', 'id')->get(); 
                                            ?>
                                            @foreach($areaList as $area)
                                                <div class="col-sm-3" style="padding-right:5px!important;">
                                                    {!! Form::checkbox('areaId[]', ($area->id), false, array('class' => 'areaId cbr')) !!}
                                                    <span style="font-size:11px;">  
                                                        {!! Form::label(Illuminate\Support\Str::lower($area->name), (str_pad($area->code, 5, '0', STR_PAD_LEFT) . ' - ' . $area->name)) !!}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
			                        </div>
                        			<div class="form-group">
                        				<div class="col-sm-10 col-sm-offset-2">
                        					{!! Form::submit('Submit', ['id' => 'submit', 'class' => 'btn btn-info']) !!}
                        					<a href="{{url('viewZone/')}}" class="btn btn-danger closeBtn">Close</a>
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
                url: './addZoneItem',
                data: $('form').serialize(),
                dataType: 'json',
                success: function(_response) {
                    if(_response.errors) {
                        var opts = {
                                    "closeButton": true,
                                    "debug": false,
                                    "positionClass": "toast-top-right",
                                    "onclick": null,
                                    "showDuration": "300",
                                    "hideDuration": "1000",
                                    "timeOut": "5000",
                                    "extendedTimeOut": "1000",
                                    "showEasing": "swing",
                                    "hideEasing": "linear",
                                    "showMethod": "fadeIn",
                                    "hideMethod": "fadeOut"
                                };

                        if(_response.errors['name'])
                            toastr.warning(_response.errors.name, null, opts);
                        if(_response.errors['code'])
                            toastr.warning(_response.errors.code, null, opts);
                        if(_response.errors['areaId'])
                            toastr.warning(_response.errors.areaId, null, opts);
                    } else {
                        var opts = {
                                    "closeButton": true,
                                    "debug": false,
                                    "positionClass": "toast-top-right",
                                    "onclick": null,
                                    "showDuration": "300",
                                    "hideDuration": "1000",
                                    "timeOut": "5000",
                                    "extendedTimeOut": "1000",
                                    "showEasing": "swing",
                                    "hideEasing": "linear",
                                    "showMethod": "fadeIn",
                                    "hideMethod": "fadeOut"
                                };

                        $('#name').val('');
                        $('#code').val('');
                        $('#areaId').val('');
                        $('.error').addClass('hidden');
                        toastr.success(_response.responseText, "Success!", opts);
                        
                        setTimeout(function(){
                            window.location.href = '{{ url('viewZone/') }}';
                        }, 6000);
                    }
                },
                error: function(_response) {
                    alert(_response.errors);
                }
            });
        });
    });
</script>