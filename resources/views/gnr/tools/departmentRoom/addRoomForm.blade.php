@extends('layouts/gnr_layout')
@section('title', '| Home')
@section('content')
<?php 
$user = Auth::user();
Session::put('branchId', $user->branchId);
 $gnrBranchId = Session::get('branchId');
 $logedUserName = $user->name;

//echo $grnBranchId;
?>
<div class="row add-data-form">
    <div class="col-md-12">
            <div class="col-md-2"></div>
                <div class="col-md-8 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('viewRoomList/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Room List</a>
                    </div>
                <div class="panel panel-default panel-border">
                                <div class="panel-heading">
                                    <div class="panel-title">Room</div>
                                </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">                
                    {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                         <!-- <input type = "hidden" name = "_token" value = ""> -->
                        <div class="form-group">
                            {!! Form::label('name', 'Room Name.:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter room number']) !!}
                                <p id='namee' style="max-height:3px;"></p>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('departmentId', 'Department Name:', ['class' => 'col-sm-3 control-label']) !!}
                               <div class="col-sm-9">
                                        <div class="col-sm-12">
                                            <p>
                                                @foreach(App\gnr\GnrDepartment::all() as $departmentId)
                                                    <div class="col-sm-4">
                                                        {!! Form::checkbox("departmentId[]", $departmentId->id, null, array('class' => 'cbr')) !!} &nbsp
                                                        {!! Form::label('departmentId', $departmentId->name, ['class' => 'control-label']) !!}
                                                    </div>
                                                @endforeach
                                            </p>
                                        </div>
                                </div>
                        </div>

                        <div class="form-group hidden">
                            {!! Form::label('branchId', 'branchId:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('branchId', $value = $gnrBranchId, ['class' => 'form-control', 'id' => 'branchId', 'type' => 'text']) !!}
                                <p id='namee' style="max-height:3px;"></p>
                            </div>
                        </div>
                        
                        <div class="form-group" style="text-align: right;">
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                <a href="{{url('viewRoomList/')}}" class="btn btn-danger closeBtn">Close</a>
                                <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                            </div>
                        </div>
                    {!! Form::close()  !!}
                                </div>
                                <div class="col-md-4 emptySpace vert-offset-top-2"><img src="images/image15.png" width="60%" height="" style="float:right">
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
             url: './addRoomItem',//
             data: $('form').serialize(), // Remember that you need to have your csrf token included
             dataType: 'json',
            success: function( _response ){
                //alert(JSON.stringify(_response));
            if (_response.errors) {
                if (_response.errors['name']) {
                    $('#namee').empty();
                    $('#namee').append('<span class="errormsg" style="color:red">'+_response.errors.name+'</span>');
                    return false;
                }
                if (_response.errors['departmentId']) {
                    $('#departmentIde').empty();
                    $('#departmentIde').append('<span class="errormsg" style="color:red">'+_response.errors.departmentId+'</span>');
                    return false;
                }
                
        } else {
                $("#name").val('');
                    window.location.href = '{{url('viewRoomList/')}}';
                }
            },
            error: function( _response ){
                alert('errors');
            }
        });
    });

    $("input").keyup(function(){
        var name = $("#name").val();
        if(name){$('#namee').hide();}else{$('#namee').show();}
    });

    $('select').on('change', function (e) {
        var departmentId = $("#departmentId").val();
        if(departmentId){$('#departmentIde').hide();}else{$('#departmentIde').show();}
    });

});
</script> 



