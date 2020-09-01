@extends('layouts/gnr_layout')
@section('title', '| Home')
@section('content')
<div class="add-data-form">  
    <div class="row">
        <div class="col-md-12">
            <a href="{{url('fmgroupname/')}}" class="btn btn-info">Project List</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">New Project</div>
                </div>
                <div class="panel-body">
                    {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                         <input type = "hidden" name = "_token" value = "<?php echo csrf_token(); ?>">
                        <div class="form-group">
                            {!! Form::label('groupId', 'Group Name:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-5">
                                {!! Form::select('groupId', array('' => 'Select','L' => 'Large', 'S' => 'Small'), null, array('class'=>'form-control', 'id' => 'groupId','required'=>'required')) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('companyId', 'Company Name::', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-5">
                                {!! Form::select('companyId', array('' => 'Select','L' => 'Large', 'S' => 'Small'), null, array('class'=>'form-control', 'id' => 'companyId','required'=>'required')) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('name', 'Project Name:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-5">
                                {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter project name', 'required'=>'required']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                                {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                            </div>
                        </div>
                    {!! Form::close()  !!}
                  </div>
             </div>
        </div>
    </div>
</div>

@endsection 

