@extends('layouts/gnr_layout')
@section('title', '| Position')
@section('content')
    <div class="row add-data-form">
        <div class="col-md-12">
            <div class="col-md-2"></div>
            <div class="col-md-8 fullbody">
                <div class="viewTitle" style="border-bottom: 1px solid white;">
                    <a href="{{url('gnr/viewPosition/')}}" class="btn btn-info pull-right addViewBtn">
                        <i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Position List</a>
                </div>
                <div class="panel panel-default panel-border">
                    <div class="panel-heading">
                        <div class="panel-title">Create Position</div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">
                                    {!! Form::open(array('url' => '' , 'enctype' => 'multipart/form-data',  'role' => 'form', 'class'=>'form-horizontal form-groups','id'=>'positionForm')) !!}
                                    <div class="form-group">
                                        {!! Form::label('name', 'Name : ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('name', null, ['class' => 'form-control','id'=>'name', 'autocomplete'=>'off','placeholder' => 'Enter Full Name']) !!}
                                            <p id='namee' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    @if(Auth::user()->user_type == 'master')
                                    <div class="form-group">
                                        {!! Form::label('companyId', 'Company Name:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            <?php
                                            $gnrCompanys = DB::table('gnr_company')->where('customer_id',Auth::user()->customer_id)->get();
                                            ?>
                                            <select class="form-control" id="companyId" name="companyId">
                                                <option value="">Select company</option>
                                                @foreach($gnrCompanys as $gnrCompany)
                                                <option value="{{$gnrCompany->id}}">{{$gnrCompany->name}}</option>
                                                @endforeach
                                            </select>
                                            <p id='companyIde' style="max-height:3px; color:red;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('status', 'Department : ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                           <select class="form-control" id="department" name="department">
                                               <option value="">Select Department</option>
                                           </select>
                                            <p id='departmentee' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    @else
                                        <div class="form-group">
                                            {!! Form::label('status', 'Department : ', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                            <select class="form-control" id="department" name="department">
                                                <option value="">Select Department</option>
                                                @foreach($departments as $department)
                                                        <option value="{{$department->id}}">{{$department->name}}</option>
                                                @endforeach
                                            </select>
                                                <p id='departmentee' style="max-height:3px;"></p>
                                            </div>
                                        </div>
                                    @endif
                                     <div class="form-group">
                                        {!! Form::label('status', 'Status : ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                           <select class="form-control" id="status" name="status">
                                               <option value="">Select Status</option>
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                             <p id='statusee' style="max-height:3px;"></p>
                                        </div>
                                        
                                    </div>

                                    <div class="form-group pull-right">
                                        <div class="col-sm-12">
                                            {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                            <a href="{{url('gnr/viewPosition/')}}"
                                               class="btn btn-danger closeBtn">Close</a>
                                            <span id="success" style="color:green; font-size:20px;"
                                                  class="pull-right"></span>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
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
@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
{{-- company wise branch --}}
<script type="text/javascript">
    $(function(){
        $(document).on('change','#companyId',function(){
            var companyId = $(this).val();
            $.ajax({
                type:"GET",
                url:'./getDepartmentInfo',
                data:{companyId:companyId},
                success:function(data){
                    var html = '<option value="">Select Department</option>';

                    $.each(data,function(key,v){
                         html += '<option value="'+v.id+'">'+v.name+'</option>';
                    });

                    $('#department').html(html);   
                }
            });
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function(){
       $('form').submit(function( event ) {
        event.preventDefault();
        var name               = $("#name").val();
        var department         = $("#department").val();
        var status             = $("#status").val();
        var companyId             = $("#companyId").val();
       
        var formData = new FormData();

        formData.append('name',name);
        formData.append('department',department);
        formData.append('status',status);
        formData.append('companyId',companyId);
       

        $.ajax({
            type: 'post',
            url: './addPositionItem',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function( _response){
                if (_response.errors) {
                    if (_response.errors['name']) {
                        $('#namee').empty();
                        $('#namee').show();
                        $('#namee').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
                    }
                    if (_response.errors['department']) {
                        $('#departmentee').empty();
                        $('#departmentee').show();
                        $('#departmentee').append('<span class="errormsg" style="color:red;">'+_response.errors.department+'</span>');
                    }
                    if (_response.errors['status']) {
                        $('#statusee').empty();
                        $('#statusee').show();
                        $('#statusee').append('<span class="errormsg" style="color:red;">'+_response.errors.status+'</span>');
                    }
                   
                }
                else {


                    $("#name").val('');
                    $("#department").val('');
                    $("#status").val('');
                   

                    // $("#costPrice").val('');
                    // $("#salesPrice").val('');

                    $('.error').addClass("hidden");
                    $('#success').text(_response.responseText);
                    window.location.href = '{{url('gnr/viewPosition/')}}';
                }
            },
            error: function( _response ){
                
            }

        });
        });
    });

</script> 

