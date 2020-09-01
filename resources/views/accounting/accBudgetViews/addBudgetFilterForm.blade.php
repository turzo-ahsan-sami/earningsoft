@extends('layouts/acc_layout')
@section('title', '| Add Budget')
@section('content')

    <style type="text/css">
    #budgetTable{
        font-family: arial !important;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                        <div class="panel-options">
                            <button id="printIcon" class="btn btn-info pull-right print-icon" style="">
                                <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
                            </button>
                        </div>
                        <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Add Budget</h3>
                    </div>

                    <div class="panel-body panelBodyView" ><!--start of panel body-->

                        <div class="viewTitle">
                            <a href="{{url('/viewBudget')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                            </i>Budget List</a>
                        </div>

                        <!-- Filtering Start-->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    {!! Form::open(array('url' => './loadBudget', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filteringFormId', 'method'=>'get')) !!}

                                    <div class="col-md-1" id="projectDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Project:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::select('filProject', $projects, null ,['id'=>'filProject','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required']) !!}
                                                <p id='filProjectE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-1" id="branchDiv" @if ($userBranchId != 1) style="display: none;"@endif>
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            <div class="col-md-12">
                                                {!! Form::label('', 'Branch:', ['class' => 'control-label pull-left']) !!}
                                            </div>
                                            <div class="col-md-12">
                                                {!! Form::select('filBranch', $branchLists, null ,['id'=>'filBranch','class'=>'form-control input-sm','autocomplete'=>'off']) !!}
                                                <p id='filBranchE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    {{--  acc project div --}}
                                    {{-- <div class="col-md-2" id="accProjectDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Project:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                <select class="form-control" name="filAccProject" id="filAccProject">
                                                    <option value="0">All</option>
                                                    @foreach ($accProjects as $project)
                                                        <option value={{$project->id}}  >{{str_pad($project->projectCode,3,"0",STR_PAD_LEFT)." - ".$project->name}}</option>
                                                    @endforeach
                                                </select>
                                                <p id='filAccProjectE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div> --}}

                                    {{--  acc project div --}}
                                    <div class="col-md-1" id="accountTypeDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Account Type:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                <select class="form-control" name="filAccountType" id="filAccountType">
                                                    <option value="0">All</option>
                                                    @foreach ($accLedgerTypes as $key => $name)
                                                        <option value={{ $key }}  >{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                                <p id='filAccountTypeE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                    $yearArr = [];
                                    foreach ($fiscalYears->sortByDesc('name') as $key => $year) {
                                        $yearArr[$year->id] = $year->name;
                                    }
                                    // dd($yearArr);
                                    @endphp

                                    <div class="col-md-1" id="fiscalYearDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('fiscalYearId', 'Fiscal Year', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::select('fiscalYearId', $yearArr, null, array('class'=>'form-control input-sm', 'id' => 'fiscalYearId')) !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group" style="font-size: 13px; padding: 4px 12px;">
                                            {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12" style="padding-top: 13px;">
                                                {!! Form::submit('Proceed', ['id' => 'filteringFormSubmit', 'class' => 'btn btn-primary btn-s animated fadeInRight', 'style'=>'font-size:12px']); !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2"></div>

                                    {!! Form::close() !!}
                                    {{-- </div> --}}
                                    {{-- </div>     --}}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12"  id="reportingDiv"></div>
                        </div>
                    </div><!--end of panel body-->
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

$(document).ready(function() {


    var userBranchId = "{{ Auth::user()->branchId }}";
    var projectId = $('#filProject').val();

    if (userBranchId == 1) {
        getProjectWiseBranches(projectId);
    }

    $('#filProject').change(function (event){
        var projectId = $('#filProject').val();
        getProjectWiseBranches(projectId);
        var branchId = $('#filBranch').val();
    });

    $("#filteringFormId").submit(function( event ) {
        event.preventDefault();
        var serializeValue=$(this).serialize();
        $('#loadingModal').show();
        $("#reportingDiv").load('{{URL::to("./loadBudget")}}'+'?'+serializeValue, function(){
            $('#loadingModal').hide();
        });
    });

    // print script
    $("#printIcon").click(function(event) {

        var mainContents = document.getElementById("reportingDiv").innerHTML;

        var headerContents = '';

        var printStyle = '<style>.amount{text-align: right;} thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:11px;} table, td,th {border:1px solid #222;} .text-bold{font-weight:bold} table{float:left;height:auto;padding:0px;border-collapse: collapse;width:100%;font-size:11px;page-break-inside:auto;font-family: arial!important;} tr:last-child { font-weight: bold;} thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;}  tr{ page-break-inside:avoid; page-break-after:auto }</style><style media="print">@page{margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}} .level-1 td, .level-2 td, .level-3 td, .level-4 td, .level-constant td{font-weight: bold !important;padding-left: 5px !important;text-transform: uppercase;}.level-1 td{font-size: 1.04em;}.level-2 td{font-size: 1.00em;/* padding-left: 15px !important; */}.level-3 td{font-size: .96em;/* padding-left: 25px !important; */}.level-4 td{font-size: .92em;/* padding-left: 35px !important; */}.level-constant td{font-size: .88em;/* padding-left: 45px !important; */}.level-transformed td{font-weight: normal !important;font-size: .88em;padding-left: 5px !important;text-transform: capitalize;}.level-final td{font-weight: normal !important;text-transform: none;}.total td{padding-left: 5px !important;font-size: 1.05em;}</style>';

        // var mainContents = document.getElementById("printDiv").innerHTML;

        var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-top:20px;'>Prepared By<span style='display:inline-block; width: 33%; padding-top:40px;'></span> Checked By<span style='display:inline-block; width: 33%; padding-right:15px; padding-top:40px;'></span>Approved By</div>";

        var printContents = '<div id="order-details-wrapper" style="padding: 10px;">' + printStyle + mainContents + footerContents +'</div>';

        var win = window.open('','printwindow');
        win.document.write(printContents);
        win.print();
        win.close();
    });

    $("#loadingModal").hide();

}); /* Ready to print */

function getProjectWiseBranches(projectId) {

    var csrf = "{{ csrf_token() }}";

    $.ajax({
        type: 'post',
        url: "./getProjectWiseBranches",
        data: {projectId: projectId , _token: csrf},
        dataType: 'json',
        success: function (data){

            $("#filBranch").empty();

            $.each(data['branches'], function(index,val){
                $('#filBranch').append("<option value='"+index+"'>"+val+"</option>");
            });

        },
        error:  function (data){

        }
    });

}
</script>

@endsection
