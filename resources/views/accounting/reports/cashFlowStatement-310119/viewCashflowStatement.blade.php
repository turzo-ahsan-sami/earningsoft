@extends('layouts/acc_layout')
@section('title', '| Cash Flow Statement Report')
@section('content')

<style type="text/css">
    #trialBalanceReportTable{
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
                        <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Cash Flow Statement Report</h3>
                    </div>

                    <div class="panel-body panelBodyView" ><!--start of panel body-->

                        <!-- Filtering Start-->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    {!! Form::open(array('url' => './cashFlowStatementLoadTable', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filteringFormId', 'method'=>'get')) !!}

                                    <div class="col-md-1" id="projectDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Project:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::select('filProject', $projects, null ,['id'=>'filProject','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required']) !!}
                                                <p id='filProjectE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-1" id="projectTypeDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Project Type:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                <select class="form-control input-sm" name="filProjectType" id="filProjectType" required>
                                                    {{-- <option value="0">All</option> --}}
                                                </select>
                                                <p id='filProjectTypeE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!--start of Branch section-->
                                    <div class="col-md-1" id="branchDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Branch:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">

                                                {!! Form::select('filBranch',$branchLists,null ,['id'=>'filBranch','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required'=>'required']) !!}
                                                <p id='filBranchE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!--end of Branch section-->

                                    <!--start of Round Up section-->
                                    <div class="col-md-1" id="branchDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Round Up:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                {{--<!-- {!! Form::select('filAccountNumber', $otsAccount, null ,['id'=>'filAccountNumber','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required']) !!} -->--}}
                                                <select name="roundUp" class="form-control input-sm" >
                                                    {{-- <!-- {!! Form::select('filBranch', $branchList, null ,['id'=>'filBranch','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required']) !!} -->--}}
                                                    <option  value="0">No</option>
                                                    <option  value="1">Yes</option>
                                                </select>

                                                <p id='filAccountNumberE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end of Round Up section-->

                                    {{-- start of zero balance --}}
                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('', '\'0\' Balance:', ['class' => 'control-label pull-left']) !!}
                                            </div>
                                            <div class="col-sm-12">
                                                {!! Form::select('withZero',['1'=>'Yes','0'=>'No'], null,['id'=>'withZero','class'=>'form-control input-sm']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    {{-- end of zero balance --}}

                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C;">
                                            <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('', 'Search By:', ['class' => 'control-label pull-left']) !!}
                                            </div>
                                            <div class="col-sm-12">
                                                {!! Form::select('searchMethod',['' => '--- Select ---', '1'=>'Fiscal Year','2'=>'Current Year'], null,['id'=>'searchMethod','class'=>'form-control input-sm', 'required']) !!}
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $fyNames = $fiscalYears->pluck('name')->toArray();
                                        $fyValues = $fiscalYears->pluck('id')->toArray();
                                    @endphp

                                    <div class="col-md-1" style="display: none;" id="fiscalYearDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', ' ', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12" style="padding-top: 18px;">
                                                {!! Form::select('fiscalYearId', [ $fyValues[0] => $fyNames[0], $fyValues[1] => $fyNames[1], $fyValues[2] => $fyNames[2], $fyValues[3] => $fyNames[3]], null, array('class'=>'form-control input-sm', 'id' => 'fiscalYearId')) !!}
                                            </div>
                                        </div>
                                    </div>

                                            {{-- Start of the to date --}}
                                            <div class="col-md-1" style="display: none;" id="dateRangeDiv">
                                              <div class="form-group">
                                                <div class="col-md-12">
                                                    {!! Form::label('','Date To: ', ['class' => 'control-label pull-left', 'style' => 'color:#212F3C']) !!}
                                                </div>
                                                <div class="col-md-12">
                                                  <input type="text" class="form-control input-sm" id="dateTo" name="dateTo" value="" placeholder="dd-mm-yy" autocomplete="off">
                                                </div>
                                              </div>
                                            </div>
                                            {{-- End of the to date --}}

                                    <div class="col-md-2">
                                        <div class="form-group" style="font-size: 13px; padding: 4px 12px;">
                                            {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12" style="padding-top: 13px;">
                                                {!! Form::submit('Search', ['id' => 'filteringFormSubmit', 'class' => 'btn btn-primary btn-s animated fadeInRight', 'style'=>'font-size:12px']); !!}
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

    $('#filProjectType').append(function(e){
        var projectId = $('#filProject').val();

        $.ajax({
            type: 'post',
            url: "./cashFlowStatementProjectType",
            data: {projectId:projectId},
            success: function (data){

                console.log(data);
                $("#filProjectType").empty();
                $('#filProjectType').append("<option value='0'>All</option>");
                $.each(data, function( key,obj){
                    $('#filProjectType').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                });

            },
            error:  function (data){

            }
        });
    });

    $('#filProject').change(function (event){
        var projectId =$('#filProject').val();

        $.ajax({
            type: 'post',
            url: "./cashFlowStatementProjectType",
            data: {projectId:projectId},
            success: function (data){

                console.log(data);
                $("#filProjectType").empty();
                $('#filProjectType').append("<option value='0'>All</option>");
                $.each(data, function( key,obj){
                    $('#filProjectType').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                });

            },
            error:  function (data){

            }
        });

    });

    $("#filteringFormId").submit(function( event ) {
        event.preventDefault();
        var serializeValue=$(this).serialize();
        $('#loadingModal').show();
        $("#reportingDiv").load('{{URL::to("./cashFlowStatementLoadTable")}}'+'?'+serializeValue, function(){
            $('#loadingModal').hide();
        });
    });

    {{-- Filtering Mehod --}}
    $("#searchMethod").change(function(event) {
        var searchMethod = $(this).val();
        var startDate = "{{ $currentFiscalYear->fyStartDate }}";

        //Fiscal Year
        if (searchMethod == '') {
            $("#fiscalYearDiv").css('display', 'none');
            $("#dateRangeDiv").css('display', 'none');
        }
        else if(searchMethod==1){
            $("#fiscalYearDiv").css('display', 'block');
            $("#dateRangeDiv").css('display', 'none');
        }
        //Current Year
        else if(searchMethod==2){
            $("#fiscalYearDiv").css('display', 'none');
            $("#dateRangeDiv").css('display', 'block');
            $("#dateRangeDiv").attr('required', 'required');

            $.datepicker.setDefaults({
                dateFormat: "dd-mm-yy",
                showOtherMonths: true,
                selectOtherMonths: true,
                changeMonth: true,
                changeYear: true,
                yearRange: "-50:+0",
                maxDate: "dateToday",
                minDate: new Date(startDate),
            });

            $("#dateTo").datepicker();
        }

    });

});

$(document).ready(function() {

  $("#printIcon").click(function(event) {

    var mainContents = document.getElementById("reportingDiv").innerHTML;
    var headerContents = '';

    var printStyle = '<style>.amount{text-align: right;} thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:11px;} table, td,th {border:1px solid #222;} .text-bold{font-weight:bold} table{float:left;height:auto;padding:0px;border-collapse: collapse;width:100%;font-size:11px;page-break-inside:auto;font-family: arial!important;} tr:last-child { font-weight: bold;} thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;} tbody tr td { text-align:center;vertical-align: middle;padding:3px ;font-size:10px;} tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

    var mainContents = document.getElementById("printDiv").innerHTML;

    var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-top:20px;'>Prepared By<span style='display:inline-block; width: 33%; padding-top:40px;'></span> Checked By<span style='display:inline-block; width: 33%; padding-right:15px; padding-top:40px;'></span>Approved By</div>";

    var printContents = '<div id="order-details-wrapper" style="padding: 10px;">' + printStyle + mainContents + footerContents +'</div>';

    var win = window.open('','printwindow');
    win.document.write(printContents);
    win.print();
    win.close();
  });

  $("#loadingModal").hide();

}); /* Ready to print */
</script>

@endsection
