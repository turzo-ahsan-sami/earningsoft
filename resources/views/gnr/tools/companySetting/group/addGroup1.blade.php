@extends('layouts/gnr_layout')
@section('title', '| New Group')
@section('content')
<div class="add-data-form"> 
    <div class="row">
        <div class="col-md-12">
            <a href="{{url('fmgroupname/')}}" class="btn btn-info">Group List</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">New Group</div>
                </div>
                <div class="panel-body">
                    {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                         <input type = "hidden" name = "_token" value = "<?php echo csrf_token(); ?>">
                        <div class="form-group">
                            {!! Form::label('name', 'Group Name:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-5">
                                {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Group Name', 'required'=>'required']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('email', 'Email:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-5">
                                {!! Form::text('email', $value = null, ['class' => 'form-control', 'id' => 'email', 'type' => 'text', 'placeholder' => 'Enter Group Email', 'required'=>'required']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('phone', 'Mobile:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-5">
                                {!! Form::text('phone', $value = null, ['class' => 'form-control', 'id' => 'phone', 'type' => 'text', 'placeholder' => 'Enter Group Phone', 'required'=>'required']) !!}
                                <p class="error text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('address', 'Address:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-5">                    
                                {!! Form::textarea('address', $value = null, ['class' => 'form-control', 'id' => 'address', 'rows' => 3, 'placeholder' => 'Enter Address', 'required'=>'required']) !!}  
                                <p class="error text-center alert alert-danger hidden"></p>  
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('website', 'Website:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-5">
                                {!! Form::text('website', $value = null, ['class' => 'form-control', 'id' => 'website', 'type' => 'text', 'placeholder' => 'Enter Website', 'required'=>'required']) !!}
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

