@extends('layouts/gnr_layout')
@section('title', '| Home')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
            <div class="col-md-2"></div>
                <div class="col-md-8 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('viewDepartmentList/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Department List</a>
                    </div>
                <div class="panel panel-default panel-border">
                                <div class="panel-heading">
                                    <div class="panel-title">Department</div>
                                </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">                
                    {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                         <!-- <input type = "hidden" name = "_token" value = ""> -->
                        <div class="form-group">
                            {!! Form::label('name', 'Department Name:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter department name']) !!}
                                <p id='namee' style="max-height:3px;"></p>
                            </div>
                        </div>
                        @if(Auth::user()->user_type == 'master')
                        <div class="form-group">
                            {!! Form::label('companyId', 'Company Name:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
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
                        @endif
                            <div class="form-group" style="text-align: right;">
                                {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                    <a href="{{url('viewProject/')}}" class="btn btn-danger closeBtn">Close</a>
                                    <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                                </div>
                            </div>
                       
                    {!! Form::close()  !!}
                                </div>
                                <div class="col-md-4 emptySpace vert-offset-top-2">
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
<script type="text/javascript">
$(document).ready(function(){
   
    $('form').submit(function( event ) {
        event.preventDefault();
        $.ajax({
             type: 'post',
             url: './addDepartmentItem',//
             data: $('form').serialize(), // Remember that you need to have your csrf token included
             dataType: 'json',
            success: function( _response ){
                //alert(JSON.stringify(_response));
                var user_type = "{{Auth::user()->user_type}}";
                if (_response.errors) {
               if(user_type == 'master'){
                if (_response.errors['name']) {
                    $('#namee').empty();
                    $('#namee').show();
                    $('#namee').append('<span class="errormsg" style="color:red">'+_response.errors.name+'</span>');
                    return false;
                }
                else if (_response.errors['companyId']) {
                    $('#companyIde').empty();
                    $('#companyIde').show();
                    $('#companyIde').append('<span class="errormsg" style="color:red">'+_response.errors.companyId+'</span>');
                    return false;
                }
               }else{
                if (_response.errors['name']) {
                    $('#namee').empty();
                    $('#namee').show();
                    $('#namee').append('<span class="errormsg" style="color:red">'+_response.errors.name+'</span>');
                    return false;
                }
               }
                
        } else {
                $("#name").val('');
                window.location.href = '{{url('viewDepartmentList/')}}';
                }
            },
            error: function( _response ){
                //alert(_response.errors);
            }
        });
    });

    $("input").keyup(function(){
        var name = $("#name").val();
        if(name){$('#namee').hide();}else{$('#namee').show();}
    });

});
</script> 



