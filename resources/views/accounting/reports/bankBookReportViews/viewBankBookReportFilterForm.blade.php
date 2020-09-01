@extends('layouts/acc_layout')
@section('title', '| Bank Book Report')
@section('content')
@php
$branch = DB::table('gnr_branch')->where('companyId',Auth::user()->company_id_fk)->where('id',Auth::user()->branchId)->select('id','branchCode')->first();
$userBranchCode = $branch->branchCode;
$headOfficeId = DB::table('gnr_branch')->where('companyId', Auth::user()->company_id_fk)->where('branchCode', 0)->value('id');

@endphp
<style type="text/css">
#bankBookReportTable{
    font-family: arial !important;
}
    /* .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 15px !important;
        } */
        .select2-container .select2-selection--single .select2-selection__rendered {
            padding-top: 0 !important;
        }
        .select2-container--default .select2-selection--single {
            border-radius: 0 !important;
        }
    </style>

    <div class="row">
        <div class="col-md-12">
            <div class="" style="">
                <div class="">
                    <div class="panel panel-default" style="background-color:#708090;">
                        <div class="panel-heading" style="padding-bottom:0px">
                            <div class="panel-options">
                          {{--   <button id="printIcon" class="btn btn-info pull-right print-icon" style="">
                                <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
                            </button> --}}

                            <button id="printIcon" class="btn btn-info pull-left print-icon" style="">
                                <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
                            </button>

                            <button id="btnExportExcel" class="btn btn-info pull-center print-icon"  target="_blank" style="">
                                <i class="fa-file-excel-o fa-lg" aria-hidden="true"> Excel</i>
                            </button>

                            <button  id="btnExportPdf" class="btn btn-info pull-right print-icon"  target="_blank" style="">
                                <i class="fa-file-excel-o fa-lg" aria-hidden="true"> Pdf</i>
                            </button>
                        </div>
                        <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Bank Book Report</h3>
                    </div>

                    <div class="panel-body panelBodyView" ><!--start of panel body-->

                        <!-- Filtering Start-->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    {!! Form::open(array('url' => './loadBankBookReport', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filteringFormId', 'method'=>'get')) !!}
                                    @if($userBranchCode == 0)
                                    <div class="col-md-1" id="projectDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Project:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::select('filProject', $projects, null ,['id'=>'filProject','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required']) !!}
                                                <p id='filProjectE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-1" id="branchDiv">
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
                                    @endif
                                    <div class="col-md-1" id="projectTypeDiv" hidden>
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Project Type:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-md-12">
                                                {!! Form::select('filProjectType', $projectTypes, null ,['id'=>'filProjectType','class'=>'form-control input-sm','autocomplete'=>'off', 'required']) !!}
                                                <p id='filProjectTypeE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C;">
                                            {!! Form::label('', 'Ledger:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                <select class="form-control selectPicker" name="ledgerId" id="ledgerId">
                                                    {{-- <option value="">Select Ledger</option> --}}
                                                    @foreach ($ledgers as $ledger)
                                                    <option value={{$ledger->id}}>{{$ledger->code." - ".$ledger->name}}</option>
                                                    @endforeach
                                                </select>
                                                <p id='ledgerIde' style="max-height:3px; color:red;"></p>

                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Vou. Type:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                <select class="form-control" name="voucherTypeId" id="voucherTypeId">
                                                    <option value="">All</option>
                                                    @foreach ($voucherTypes as $voucherType)
                                                    <option value={{$voucherType->id}}>{{$voucherType->shortName}}</option>
                                                    @endforeach
                                                </select>
                                                <p id='voucherTypeIde' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Start of the from date --}}
                                    <div class="col-md-1" id="dateFromDiv">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                {!! Form::label('','Date From: ', ['class' => 'control-label pull-left', 'style' => 'color:#212F3C']) !!}
                                            </div>
                                            <div class="col-md-12">
                                                {{-- <input type="text" class="form-control input-sm" id="datefrom" name="dateFrom" value="" placeholder="dd-mm-yy" autocomplete="off"> --}}
                                                {!! Form::text('dateFrom', $startDate, ['id'=>'dateFrom','placeholder'=>'From','class'=>'form-control','readonly','style'=>'cursor:pointer', 'autocomplete'=>'off']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    {{-- End of the from date --}}

                                    {{-- Start of the to date --}}
                                    <div class="col-md-1" id="dateToDiv">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                {!! Form::label('','Date To: ', ['class' => 'control-label pull-left', 'style' => 'color:#212F3C']) !!}
                                            </div>
                                            <div class="col-md-12">
                                                {!! Form::text('dateTo', $userBranchDate, ['id'=>'dateTo','placeholder'=>'To','class'=>'form-control','readonly','style'=>'cursor:pointer', 'autocomplete'=>'off']) !!}
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

                                    {!! Form::close() !!}

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

       $("#btnExportExcel").click(function(e) {
        //alert('sdsds');
        var today = new Date();
        var dd = today.getDate();

        var mm = today.getMonth()+1; 
        var yyyy = today.getFullYear();
        if(dd<10) 
        {
            dd='0'+dd;
        } 

        if(mm<10) 
        {
            mm='0'+mm;
        } 
        today = dd+'-'+mm+'-'+yyyy;
        //alert(today);
        let file = new Blob([$('#printDiv').html()], {type:"application/vnd.ms-excel"});
        let url = URL.createObjectURL(file);
        let a = $("<a />", {
          href: url,
          download: "Bank Book Report_"+ today + ".xls"}).appendTo("body").get(0).click();
        e.preventDefault();
    });
    
    var branchDate = "{{ \Carbon\Carbon::parse($userBranchDate)->format('d-m-Y') }}";
    var userBranchStartDate = "{{ \Carbon\Carbon::parse($userBranchStartDate)->format('d-m-Y') }}";
    // select-2 initiate
    $(".selectPicker").select2();

    var userBranchId = "{{ Auth::user()->branchId }}";
    var headOfficeId = "{{ $headOfficeId }}";
    var projectId = $('#filProject').val();
    var branchId = $('#filBranch').val();

    if (headOfficeId) {
        getProjectTypesNBranches(projectId);
    }

    getChildrenLedgers(projectId, branchId);

    $('#filProject').change(function (event){
        var projectId = $('#filProject').val();
        getProjectTypesNBranches(projectId);
        var branchId = $('#filBranch').val();
        getChildrenLedgers(projectId, branchId);
    });

    $('#filBranch').change(function (event){
        var projectId = $('#filProject').val();
        var branchId = $('#filBranch').val();
        getChildrenLedgers(projectId, branchId);
    });

    $("#filteringFormId").submit(function( event ) {

        var branchId = $("#filBranch").val();
        var dateFrom = $("#dateFrom").datepicker('getDate');
        var dateTo = $("#dateTo").datepicker('getDate');
        var dateDiff = Math.ceil((dateTo - dateFrom) / (1000 * 60 * 60 * 24));

        if (branchId == null || branchId == 0) {
            if (dateDiff > 30) {
                alert('Date range must be one month for all branches.');
                return false;
            }
        }
        else {
            if (dateDiff > 365) {
                alert('Date range must be one year for single branch.');
                return false;
            }
        }

        event.preventDefault();
        var serializeValue=$(this).serialize();
        $('#loadingModal').show();
        $("#reportingDiv").load('{{URL::to("./loadBankBookReport")}}'+'?'+serializeValue, function(){
            $('#loadingModal').hide();
        });
    });

    /* Date Range From */
    @php
    $minYear = \Carbon\Carbon::parse($userBranchStartDate)->subYear()->format('Y');
    @endphp
    $("#dateFrom").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange : "{{ $minYear }}:c",
        // maxDate: "dateToday",
        minDate: userBranchStartDate,
        maxDate: branchDate,
        dateFormat: 'dd-mm-yy',
        onSelect: function () {
            $("#dateTo").datepicker("option", "minDate", $(this).datepicker('getDate'));
        }
    });
    /* Date Range From */

    /* Date Range To */
    $("#dateTo").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange : "{{ $minYear }}:c",
        // maxDate: "dateToday",
        minDate: userBranchStartDate,
        maxDate: branchDate,
        dateFormat: 'dd-mm-yy',
        onSelect: function () {
            $("#dateFrom").datepicker("option", "maxDate", $(this).datepicker('getDate'));
        }
    });
    /* End Date Range To */

    // print script
    $("#printIcon").click(function(event) {

        var mainContents = document.getElementById("reportingDiv").innerHTML;

        var headerContents = '';

        var printStyle = '<style>.amount{text-align: right;} .table tbody tr td {text-align: center;vertical-align: middle;} thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:11px;} table, td,th {border:1px solid #222;} .text-bold{font-weight:bold} table{float:left;height:auto;padding:0px;border-collapse: collapse;width:100%;font-size:11px;page-break-inside:auto;font-family: arial!important;} tr:last-child { font-weight: bold;} thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;}  tr{ page-break-inside:avoid; page-break-after:auto }</style><style media="print">@page{margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}} .total td{padding-left: 5px !important;} .sub-total td{font-weight: bold !important;}</style>';

        // var mainContents = document.getElementById("printDiv").innerHTML;

        var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-top:20px;'>Prepared By<span style='display:inline-block; width: 33%; padding-top:40px;'></span> Checked By<span style='display:inline-block; width: 33%; padding-right:15px; padding-top:40px;'></span>Approved By</div>";

        var printContents = '<div id="order-details-wrapper" style="padding: 10px;">' + printStyle + mainContents + footerContents +'</div>';

        var win = window.open('','printwindow');
        win.document.write(printContents);
        win.print();
        win.close();
    });

    $("#loadingModal").hide();

});

function getProjectTypesNBranches(projectId) {

    var csrf = "{{ csrf_token() }}";
    // alert(projectId);

    if (projectId == 0) {
        $("#filBranch").empty();
        $("#filBranch").append('<option value="">All</option>');
        // $("#filBranch").append('<option value="{{ $userBranchData->id }}">{{ $userBranchData->nameWithCode }}</option>');

         $("#filProjectType").empty();
         $('#filProjectType').append("<option value='0'>All</option>");
    }
    else {
        $.ajax({
            type: 'post',
            url: "./getProjectTypesNBranches",
            data: {projectId: projectId , _token: csrf},
            dataType: 'json',
            success: function (data){
                $("#filProjectType").empty();
                //$('#filProjectType').append("<option value='0'>All</option>");
                $.each(data['projectTypes'], function( key,obj){
                    $('#filProjectType').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                });

                $("#filBranch").empty();
                $("#filBranch").append('<option value="">All</option>');
                $("#filBranch").append('<option value="0">All Branches</option>');
                $("#filBranch").append('<option value="{{ $userBranchData->id }}">{{ $userBranchData->nameWithCode }}</option>');

                $.each(data['branches'], function(index,val){
                    $('#filBranch').append("<option value='"+index+"'>"+val+"</option>");
                });

            },
            error:  function (data){

            }
        });
    }
}

function getChildrenLedgers(projectId, branchId) {

    var csrf = "{{ csrf_token() }}";
    // alert(projectId, branchId);

    $.ajax({
        type: 'post',
        url: "./getChildrenBankLedgers",
        data: {projectId: projectId, branchId: branchId, _token: csrf},
        dataType: 'json',
        success: function (data){

            $("#ledgerId").empty();
            $("#ledgerId").append('<option value="">All Bank Ledgers</option>');
            $.each(data, function(index, obj){
                $('#ledgerId').append("<option value='"+obj.id+"'>"+obj.code+' - '+obj.name+"</option>");
            });

        },
        error:  function (data){

        }
    });
}

</script>

@endsection
