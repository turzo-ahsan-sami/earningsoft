@extends('layouts/acc_layout')
@section('title', '| Add Ledger Relation')
@section('content')

    <style media="screen">
        .select2-container .select2-selection--single .select2-selection__rendered {
            padding-top: 0 !important;
        }
        .select2-container--default .select2-selection--single {
            border-radius: 0 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 2.428571;
        }
    </style>

    <div class="row add-data-form"  style="padding-bottom: 1%">
        <div class="col-md-12">
            <div class="col-md-2"></div>
            <div class="col-md-8 fullbody">
                <div class="viewTitle" style="border-bottom: 1px solid white;">
                    <a href="{{url('viewLedgerRelation/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                    </i>Ledger Relation List</a>
                </div>
                <div class="panel panel-default panel-border">
                    <div class="panel-heading">
                        <div class="panel-title">Add Ledger Relation</div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">
                                    {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}


                                    <div class="form-group" style="font-size: 13px; color:#212F3C">
                                        {!! Form::label('', 'Project:', ['class' => 'control-label col-sm-3']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::select('projectId', [0 => 'Select Project']+$projects, null ,['id'=>'projectId','class'=>'form-control input-sm', 'autofocus', 'required']) !!}
                                            <p id='filProjectE' style="max-height:3px; color:red;"></p>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('ledger1', 'First Ledger:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">

                                            <select class ="form-control selectPicker select2" id = "ledger1" name="ledger1">
                                                <option value="">Select Ledger</option>
                                                {{-- @foreach($ledgers as $ledger)
                                                    <option value={{ $ledger->id }}>{{ $ledger->code." - ".$ledger->name }}</option>
                                                @endforeach --}}
                                            </select>
                                            <p id='ledger1e' style="max-height:3px;"></p>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('ledger2', 'Second Ledger:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">

                                            <select class ="form-control selectPicker select2" id = "ledger2" name="ledger2">
                                                <option value="">Select Ledger</option>

                                                {{-- @foreach($ledgers as $ledger)
                                                    <option value={{$ledger->id}}>{{ $ledger->code." - ".$ledger->name }}</option>
                                                @endforeach --}}
                                            </select>
                                            <p id='ledger2e' style="max-height:3px;"></p>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        {!! Form::label('relation', 'Relation:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('relation', $value = null, ['class' => 'form-control', 'id' => 'relation', 'rows' => 5, 'placeholder' => 'Enter relation', 'autocomplete'=>'off']) !!}
                                            <p id='relatione' style="max-height:3px;"></p>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9 text-right">
                                            {!! Form::submit('submit', ['id' => 'add', 'class' => 'btn btn-info']) !!}
                                            {{ Form::reset('Reset', ['class' => 'btn btn-warning']) }}
                                            <a href="{{url('viewLedgerRelation/')}}" class="btn btn-danger closeBtn">Close</a>
                                            <span id="success" style="color:green; font-size:16px;" class="pull-right"></span>
                                        </div>
                                    </div>
                                    {!! Form::close()  !!}
                                </div>
                                <div class="col-md-4 emptySpace vert-offset-top-4"><img src="images/image15.png" width="90%" height="" style="float:right"></div>
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

    $(".selectPicker").select2();

    $('#projectId').change(function (event){

        var projectId = $('#projectId').val();
        var csrf = "<?php echo csrf_token(); ?>";

        if (projectId == 0) {

            $('#ledger1').empty();
            $('#ledger1').append("<option value=''>Select Ledger</option>");
            $('#ledger2').empty();
            $('#ledger2').append("<option value=''>Select Ledger</option>");

        } else {

            $.ajax({
                type: 'post',
                url: './getLedgersByProject',
                data: {projectId: projectId , _token: csrf},
                dataType: 'json',
                success: function (data) {
                    $("#ledger1").empty();
                    $("#ledger2").empty();
                    $("#ledger1").append('<option value="">Select Ledger</option>');
                    $("#ledger2").append('<option value="">Select Ledger</option>');

                    $.each(data, function(key,obj){
                        $('#ledger1').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                    });

                    $.each(data, function(key,obj){
                        $('#ledger2').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                    });

                },
                error: function(_response){
                    alert("Error");
                }
            });
        }

    });


    $('form').submit(function( event ) {
        event.preventDefault();
        $.ajax({
            type: 'post',
            url: './addLedgerRelationItem',
            data: $('form').serialize(),
            dataType: 'json',
            success: function( _response ){
                if (_response.accessDenied) {
                    showAccessDeniedMessage();
                    return false;
                }
                // alert(JSON.stringify(_response.responseText));
                if (_response.errors) {
                    // if (_response.errors['name']) {
                    //     $('#namee').empty();
                    //     $('#namee').append('<span style="color:red;">'+_response.errors.name+'</span>');
                    //     return false;
                    // }

                } else {
                    toastr.success(_response.responseText, opts);
                    setTimeout(function(){
                        window.location.href = '{{url('viewLedgerRelation/')}}';
                    }, 2000);

                }
            },
            error: function( _response ){
                // Handle error
                alert(_response.errors);
            }
        });
    });



    // $("textarea").keyup(function(){
    //              var description = $("#description").val();
    //             if(description){$('#descriptione').hide();}else{$('#descriptione').show();}
    // });

});
</script>
