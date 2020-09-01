@extends('layouts/gnr_layout')
@section('title', '| Home')

@section('stylesheets')
    <style>
        .select2-selection__rendered{
            overflow: hidden !important;
            height: auto !important;
        }
    </style>
@endsection
@section('content')
    <div class="row add-data-form">
        <div class="col-md-12">
            <div class="col-md-2"></div>
            <div class="col-md-8 fullbody">
                <div class="viewTitle" style="border-bottom: 1px solid white;">
                    <a href="{{ route('gnrResponsibility.index') }}"
                       class="btn btn-info pull-right addViewBtn">
                        <i class="glyphicon glyphicon-th-list viewIcon"></i>List
                    </a>
                </div>
                <div class="panel panel-default panel-border">
                    <div class="panel-heading">
                        <div class="panel-title">Create Responsible Employee</div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">
                                    {!! Form::open(array('url' => route('gnrResponsibility.store'), 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                    <div class="form-group">
                                        {!! Form::label('position_id_fk', 'Position : ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::select('position_id_fk', $positions, null, ['class' => 'form-control','id'=>'position', 'autocomplete'=>'off']) !!}
                                            @if ($errors->has('position_id_fk'))
                                                <span style="color: red;">{{ $errors->first('position_id_fk') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('emp_id_fk', 'Employee Specify : ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            Yes {!! Form::radio('employee_specify','Yes',true , ['class' => 'employee-specify', 'autocomplete'=>'off']) !!}
                                            <span style="padding: 0 2px;"></span>
                                            No {!! Form::radio('employee_specify','No',false , ['class' => 'employee-specify', 'autocomplete'=>'off']) !!}
                                        </div>
                                    </div>

                                    <div class="form-group employee-container">
                                        {!! Form::label('emp_id_fk', 'Employee : ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::select('emp_id_fk', [], null, ['class' => 'form-control','id'=>'employee', 'autocomplete'=>'off']) !!}
                                            @if ($errors->has('emp_id_fk'))
                                                <span style="color: red;">{{ $errors->first('emp_id_fk') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('type_code', 'Type Code : ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::select('type_code', $responsibleForList, null, ['class' => 'form-control','id'=>'type-code', 'autocomplete'=>'off']) !!}
                                            @if ($errors->has('type_code'))
                                                <span style="color: red;">{{ $errors->first('type_code') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{--Boundary--}}
                                    <div class="form-group">
                                        {!! Form::label('id_list', 'Boundary : ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8" >
                                            {!! Form::select('id_list[]', [], null, ['class' => 'form-control select2','id'=>'boundary', 'multiple'=>'multiple','autocomplete'=>'off']) !!}
                                            @if ($errors->has('id_list'))
                                                <span style="color: red;">{{ $errors->first('id_list') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group pull-right">
                                        <div class="col-sm-12">
                                            {!! Form::submit('Save', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                            <a href="{{route('gnrResponsibility.index')}}"
                                               class="btn btn-danger closeBtn">
                                                Close
                                            </a>
                                            <span id="success" style="color:green; font-size:20px;"
                                                  class="pull-right"></span>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                                <div class="col-md-4 emptySpace vert-offset-top-0">
                                    <img src="{{ asset('images/catalog/image15.png') }}"
                                         width="80%" height=""
                                         style="float:right">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footerTitle" style="border-top:1px solid white"></div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            $(".employee-specify").change(function (e) {
                console.log($(this).val())
                if ($(this).val() == 'Yes') {
                    $(".employee-container").show();
                } else {
                    $(".employee-container").hide();
                }
            });

            $("#position").change(function (e) {
                var positionSelector = $(this);
                var employeeSelector = $("#employee");
                var positionId = positionSelector.val();

                var option = {
                    url: "{{ route('gnrResponsibility.index') }}",
                    type: 'GET',
                    data: {positionId: positionId},
                    success: function (response) {
                        console.log(response);
                        setOption(response, employeeSelector)

                    },
                    error: function (response) {
                        console.log(response);
                    }
                };

                $.ajax(option);
            });

            $("#type-code").change(function (e) {
                var typeCodeSelector = $(this);
                var boundarySelector = $("#boundary");

                var typeCode = typeCodeSelector.val();

                var option = {
                    url: "{{ route('gnrResponsibility.index') }}",
                    type: 'GET',
                    data: {typeCode: typeCode},
                    success: function (response) {
                        console.log(response);
                        setOption(response, boundarySelector)

                    },
                    error: function (response) {
                        console.log(response);
                    }
                };

                $.ajax(option);
            });

            function setOption(data, targetAttr, selected = null) {
                var options="<option value=''>Select One</option>";
                $.each(data,function(key,val){
                    var selectedData='';
                    if(selected!=null && val.id==selected){
                        selectedData = 'selected="selected"';
                    }
                    options += '<option '+selectedData+' value="'+val.id+'">'+val.name+'</option>';
                });
                targetAttr.empty()
                targetAttr.html(options);

            }
        });

    </script>
@endsection


