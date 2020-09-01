@extends('layouts/acc_layout')
@section('title', '| Manual Day End')
@section('content')
    <style type="text/css">
    .form-group, .form-control{
        font-size: 11px !important;
        color: black !important;
    }
    .form-control{
        padding: 5px !important;
    }
    #generateButton{
        font-size: 12px;
        margin-top: 20px;
    }
</style>
<script type="text/javascript">
$("#loadingModal").show();
</script>

<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                        <h3  style="font-size: 2em; text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Execute Day End</h3>
                    </div>

                    <div class="panel-body panelBodyView" >
                        <!-- Filtering Start-->
                        {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filterFormId', 'method'=>'get')) !!}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            {!! Form::label('', 'Branch:', ['class' => 'control-label pull-left']) !!}
                                        </div>
                                        <div class="col-md-12">
                                            {!! Form::select('filBranch[]', $branchList, null ,['multiple'=>'multiple', 'id'=>'filBranch','class'=>'form-control input-sm','autocomplete'=>'off', 'required']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        {!! Form::label('', '', ['class' => 'control-label col-md-12']) !!}
                                        <div class="col-md-12">
                                            {!! Form::submit('Generate', ['id' => 'generateButton', 'class' => 'btn btn-primary btn-xs']); !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {!! Form::close()  !!}
                        <!-- filtering end-->

                        <div class="row">
                            <div class="col-md-12"  id="reportingDiv"></div>
                        </div>

                    </div>		{{-- panel-body panelBodyView DIV --}}

                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

$(document).ready(function() {

    $("#filterFormId").submit(function( event ) {
        // alert('1')
        event.preventDefault();
        var serializeValue=$(this).serialize();
        $('#loadingModal').show();
        $("#reportingDiv").load('{{URL::to("./executeDayEnd")}}'+'?'+serializeValue, function(){
            $('#loadingModal').hide();
        });
    });

}); /*Ready*/
</script>

@endsection
