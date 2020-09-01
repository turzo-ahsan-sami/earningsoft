@extends('layouts/acc_layout')
@section('title', '| MIS Configuration')
@section('content')

<?php 
// var_dump($moduleOption);
?>

<div class="row add-data-form"  style="padding-bottom: 1%">
    <div class="col-md-12">
        <div class="col-md-2"></div>
        <div class="col-md-8 fullbody">
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                <a href="{{url('viewMisConfiguration/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Mis Configuration List</a>
            </div>
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">Add MIS Configuration</div>
                </div>

                <div class="panel-body">
                    {!! Form::open(array('url' => '', 'id' => 'misConfigurFormId', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}

                        <div class="form-group">
                            {!! Form::label('moduleId', 'Module:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-5">
                                {!! Form::select('moduleId', $moduleOption, null,['id'=>'moduleId','class'=>'form-control input-sm']) !!}
                                <p id='moduleIdE' style="max-height:3px;"></p>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('misTypeId_Fk', 'MIS Type:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-5">
                                {!! Form::select('misTypeId_Fk', [''=>'--Select Module First--'], null,['id'=>'misTypeId_Fk','class'=>'form-control input-sm']) !!}
                                <p id='misTypeId_FkE' style="max-height:3px;"></p>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('misName', 'MIS Name:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-5">
                                {!! Form::text('misName', $value = null, ['class' => 'form-control', 'id' => 'misName', 'type' => 'text', 'placeholder' => 'Enter MIS Name']) !!}
                                <p id='misNameE' style="max-height:3px;"></p>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('tableFieldName', 'Table Field Name:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-5">
                                {!! Form::text('tableFieldName', $value = null, ['class' => 'form-control', 'id' => 'tableFieldName', 'type' => 'text', 'placeholder' => 'Enter Table Field Name']) !!}
                                <p id='tableFieldNameE' style="max-height:3px;"></p>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-5 text-right">
                                {!! Form::submit('Submit', ['id' => 'submitButton', 'class' => 'btn btn-info']) !!}
                                {{ Form::reset('Reset', ['class' => 'btn btn-warning']) }}
                                <a href="{{url('viewMisConfiguration/')}}" class="btn btn-danger closeBtn">Close</a>
                                <span id="success" style="color:green; font-size:16px;" class="pull-right"></span>
                            </div>
                        </div>

                    {!! Form::close()  !!}
                </div>


            </div>
        <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>

{{-- <script src="{{asset('js/jquery-1.11.1.min.js')}}"></script> --}}

<script type="text/javascript">
$(document).ready(function(){

    var csrf = "{{csrf_token()}}";

    $('#moduleId').on('change', function () {

        var moduleId=$(this).val();
        if (!moduleId) {            
            $('#misTypeId_Fk').empty();
            $('#misTypeId_Fk').prepend("<option value=''>--Select Module First--</option>");
            return false;
        }

        $.ajax({
            url: './getMisTypeOption',
            type: 'POST',
            dataType: 'json',
            data: {moduleId: moduleId, _token: csrf},
        })
        .done(function(misTypeOption) {
            // alert(JSON.stringify(misTypeOption));
            // console.log("success");

            $('#misTypeId_Fk').empty();
            $('#misTypeId_Fk').prepend("<option value=''>--Select MIS Type--</option>");

            $.each(misTypeOption, function(id, name){
                $('#misTypeId_Fk').append("<option value='"+id+"'>"+name+"</option>");
            });

        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
        
    })

       
    $('form').submit(function( event ) {
        event.preventDefault();
        // alert($(this).serialize());
        $("#submitButton").prop("disabled", true);

        $.ajax({
             type: 'post',
             url: './addMisConfigurationItem',
             data: $('form').serialize(),
             dataType: 'json',
            success: function( _response ){
                // alert(JSON.stringify(_response));
                if (_response.errors) {
                    $("#submitButton").prop("disabled", false);


                    $.each(_response.errors, function(name, error) {
                        $('#'+name+'E').empty();
                        $('#'+name+'E').show();
                        $('#'+name+'E').append('<span style="color:red;">'+_response.errors[name]+'</span>');
                        return false;
                        // $("#"+name).after("<p class='error' style='color:red;'>* "+_response.errors[name]+"</p>");
                    });

                    // if (_response.errors['moduleId']) {
                    //     $('#moduleIdE').empty();
                    //     $('#moduleIdE').show();
                    //     $('#moduleIdE').append('<span style="color:red;">'+_response.errors.moduleId+'</span>');
                    //     return false;
                    // }

                } else {                    
                    window.location.href = '{{url('viewMisConfiguration/')}}';
                }
            },
            error: function( _response ){
                // Handle error
                alert(_response.errors);
            }
        });     //ajax
    });     //submit

    $("input").keyup(function(){
        var misName = $("#misName").val();
        if(misName){$('#misNameE').hide();}else{$('#misNameE').show();}

        var tableFieldName = $("#tableFieldName").val();
        if(tableFieldName){$('#tableFieldNameE').hide();}else{$('#tableFieldNameE').show();}
    });

    $('select').on('change', function (e) {
        var moduleId = $("#moduleId").val();
        if(moduleId){$('#moduleIdE').hide();}else{$('#moduleIdE').show();}

        var misType = $("#misTypeId_Fk").val();
        if(misType){$('#misTypeId_FkE').hide();}else{$('#misTypeId_FkE').show();}
    });

        

});
</script> 
 
@endsection 
