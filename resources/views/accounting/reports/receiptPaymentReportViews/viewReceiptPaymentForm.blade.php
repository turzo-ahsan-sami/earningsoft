@extends('layouts/acc_layout')
@section('title', '| Receipt Payment Report')
@section('content')

<style type="text/css">
#receiptPaymentReportTable{
    font-family: arial !important;
}
</style>
@php
$branch = DB::table('gnr_branch')->where('id',Auth::user()->branchId)->select('id','branchCode')->first();
$userBranchCode = $branch->branchCode;
//dd($branch)
@endphp

<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                        <div class="panel-options">
                           {{--  <button id="printIcon" class="btn btn-info pull-right print-icon" style="">
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
                        <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Receipt Payment Statement</h3>
                    </div>

                    <div class="panel-body panelBodyView" ><!--start of panel body-->

                        <!-- Filtering Start-->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    {!! Form::open(array('url' => './receiptPaymentLoadReport', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filteringFormId', 'method'=>'get')) !!}

                                    <div class="col-md-1" id="projectDiv" @if ($userBranchCode != 0) style="display: none;"@endif>
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Project:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::select('filProject', $projects, null ,['id'=>'filProject','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required']) !!}
                                                <p id='filProjectE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    @if($userBranchCode == 0)
                                    <div class="col-md-1" hidden>
                                        <div class="form-group" style="font-size: 13px; color:#212F3C" hidden>
                                            <div class="col-md-12">
                                                {!! Form::label('', 'Report Level:', ['class' => 'control-label pull-left']) !!}
                                            </div>
                                            <div class="col-md-12">
                                                {!! Form::select('filReportLevel',$reportLevelList, null ,['id'=>'filReportLevel','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus']) !!}

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1" id="areaDiv" style="display: none;">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            <div class="col-md-12">
                                                {!! Form::label('', 'Area:', ['class' => 'control-label pull-left']) !!}
                                            </div>
                                            <div class="col-md-12">
                                                <select id="filArea" name="filArea" class="form-control input-sm non-microfin-div">
                                                    <option value="">--Select Area--</option>
                                                    @foreach ($areaList as $area)
                                                    <option value="{{$area->id}}">{{$area->name}}</option>
                                                    @php
                                                    $barnchIds = str_replace(['"','[',']'], '', $area->branchId);
                                                    $barnchIds = explode(',', $barnchIds);
                                                    $branches = DB::table('gnr_branch')
                                                    ->whereIn('id',$barnchIds)
                                                    ->select(DB::raw("CONCAT ( LPAD(branchCode,3,0) ,' - ', name ) AS name"))
                                                    ->get();
                                                    @endphp
                                                    @foreach ($branches as $branch)
                                                    <option value="" disabled="disabled">&nbsp {{$branch->name}}</option>
                                                    @endforeach
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1" id="zoneDiv" style="display: none;">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            <div class="col-md-12">
                                                {!! Form::label('', 'Zone:', ['class' => 'control-label pull-left']) !!}
                                            </div>
                                            <div class="col-md-12">
                                                <select id="filZone" name="filZone" class="form-control input-sm non-microfin-div">
                                                    <option value="">--Select Zone--</option>
                                                    @foreach ($zoneList as $zone)
                                                    <option value="{{$zone->id}}">{{$zone->name}}</option>
                                                    @php
                                                    $areaIds = str_replace(['"','[',']'], '', $zone->areaId);
                                                    $areaIds = explode(',', $areaIds);
                                                    $areas = DB::table('gnr_area')
                                                    ->whereIn('id',$areaIds)
                                                    ->select(DB::raw("CONCAT ( LPAD(code,3,0) ,' - ', name ) AS name"))
                                                    ->get();
                                                    @endphp
                                                    @foreach ($areas as $area)
                                                    <option value="" disabled="disabled">&nbsp {{$area->name}}</option>
                                                    @endforeach
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1" id="regionDiv" style="display: none;">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            <div class="col-md-12">
                                                {!! Form::label('', 'Region:', ['class' => 'control-label pull-left']) !!}
                                            </div>
                                            <div class="col-md-12">
                                                <select id="filRegion" name="filRegion" class="form-control input-sm non-microfin-div">
                                                    <option value="">--Select Region--</option>
                                                    @foreach ($regionList as $region)
                                                    <option value="{{$region->id}}">{{$region->name}}</option>
                                                    @php
                                                    $zoneIds = str_replace(['"','[',']'], '', $region->zoneId);
                                                    $zoneIds = explode(',', $zoneIds);
                                                    $zones = DB::table('gnr_zone')
                                                    ->whereIn('id',$zoneIds)
                                                    ->select(DB::raw("CONCAT ( LPAD(code,3,0) ,' - ', name ) AS name"))
                                                    ->get();
                                                    @endphp
                                                    @foreach ($zones as $zone)
                                                    <option value="" disabled="disabled">&nbsp {{$zone->name}}</option>
                                                    @endforeach
                                                    @endforeach
                                                </select>

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
                                            <div class="col-sm-12">
                                                <select class="form-control input-sm" name="filProjectType" id="filProjectType" required>
                                                    {{-- <option value="0">All</option> --}}
                                                </select>
                                                <p id='filProjectTypeE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- voucher type filter --}}
                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C;">
                                            {!! Form::label('', 'V. Type:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::select('voucherType', [''=>'Cash',1=>'All',2=>'Non-Cash'], null,['class'=>'form-control input-sm']) !!}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- start of depth level --}}
                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Depth Level:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::select('depthLevel', [], null ,['id'=>'filDepthLevel','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required'=>'required']) !!}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- end of depth level --}}

                                    <!--start of Round Up section-->
                                    <div class="col-md-1" id="roundUpDiv">
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
                                                {!! Form::select('searchMethod',['' => '--- Select ---', '1'=>'Fiscal Year','2'=>'Current Year', '3' => 'Date Range'], '',['id'=>'searchMethod','class'=>'form-control input-sm', 'required']) !!}
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

                                    <div class="col-md-1" style="display: none;" id="fiscalYearDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', ' ', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12" style="padding-top: 18px;">
                                                {!! Form::select('fiscalYearId', $yearArr, null, array('class'=>'form-control input-sm', 'id' => 'fiscalYearId')) !!}
                                            </div>
                                        </div>
                                    </div>



                                    {{-- Start of the to date --}}
                                    <div class="col-md-1" style="display: none;" id="dateToDiv">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                {!! Form::label('','Date To: ', ['class' => 'control-label pull-left', 'style' => 'color:#212F3C']) !!}
                                            </div>
                                            <div class="col-md-12">
                                                {!! Form::text('dateTo',$branchDate,['id'=>'dateTo','placeholder'=>'To','class'=>'form-control input-sm','readonly','style'=>'cursor:pointer']) !!}
                                                <p id="dateToRequired" style="color: red;display: none;">*Required</p>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- End of the to date --}}
                                    {{-- date range div --}}
                                    <div class="col-md-2" id="dateRangeDiv">
                                        <div class="form-group" style="font-size: 13px; color:black">
                                            <div class="col-sm-12" style="padding-top: 7px;">
                                                <div class="form-group">
                                                    <div style="text-align: center;" class="col-sm-12">
                                                        {!! Form::label('', '', ['class' => 'control-label']) !!}
                                                    </div>
                                                    <div class="col-sm-6">
                                                        {!! Form::text('dateRangeFrom',$branchDate,['id'=>'dateRangeFrom','placeholder'=>'From','class'=>'form-control input-sm','readonly','style'=>'cursor:pointer']) !!}
                                                        <p id="dateFrome" style="color: red;display: none;">*Required</p>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        {!! Form::text('dateRangeTo',$branchDate,['id'=>'dateRangeTo','placeholder'=>'To','class'=>'form-control input-sm','readonly','style'=>'cursor:pointer']) !!}
                                                        <p id="dateToe" style="color: red;display: none;">*Required</p>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
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
    $(document).ready(function(){
        var userBranchId = "{{ Auth::user()->branchId }}";
        var userBranchCode = "{{$userBranchCode }}";
        var projectId = $('#filProject').val();
        var branchId = $('#filBranch').val();

        if (userBranchCode == 0) {
             getProjectTypesNBranches(projectId);
            //alert('ok');
        }

        getChildrenLedgers(projectId, branchId);
        $('#filProject').change(function (event){
            var projectId = $(this).val();
            //alert(projectId);
            getProjectTypesNBranches(projectId);
            var branchId = $('#filBranch').val();
            getChildrenLedgers(projectId, branchId);
            //alert('ok');
        });

        function getProjectTypesNBranches(projectId) {

            var csrf = "{{ csrf_token() }}";
            // alert(projectId);

            if (projectId == 0) {
                $("#filBranch").empty();
                $("#filBranch").append('<option value="">Select Branch</option>');
              

                $("#filProjectType").empty();
                $('#filProjectType').append("<option value='0'>Select Project</option>");
            }
            else {
                $.ajax({
                    type: 'post',
                    url: "./getProjectTypesNBranches",
                    data: {projectId: projectId , _token: csrf},
                    dataType: 'json',
                    success: function (data){
                        $("#filProjectType").empty();
                        // $('#filProjectType').append("<option value='0'>All</option>");
                        $.each(data['projectTypes'], function( key,obj){
                            $('#filProjectType').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                        });

                        $("#filBranch").empty();
                        $("#filBranch").append('<option value="All">All (With HO)</option>');
                        $("#filBranch").append('<option value="0">All (WithOut HO)</option>');
                        // $("#filBranch").append('<option value="0">All Branches</option>');
                        $("#filBranch").append('<option value="{{ $userBranchData->id }}">{{ $userBranchData->nameWithCode }}</option>');
                        console.log(data);
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
                url: "./getChildrenLedgers",
                data: {projectId: projectId, branchId: branchId, _token: csrf},
                dataType: 'json',
                success: function (data){

                    $("#ledgerId").empty();
                    $("#ledgerId").append('<option value="">Select Ledger</option>');
                    $.each(data, function(index, obj){
                        $('#ledgerId').append("<option value='"+obj.id+"'>"+obj.code+' - '+obj.name+"</option>");
                    });

                },
                error:  function (data){

                }
            });
        }
    });
</script>
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
          download: "Receipt Payment Statement Report_"+ today + ".xls"}).appendTo("body").get(0).click();
        e.preventDefault();
    });

       var projectId = $('#filProject').val();
       if (projectId != 0) {
        getLedgerLevelsByProject(projectId);
    }

    $('#filProjectType').append(function(e){
        var projectId = $('#filProject').val();

        if (projectId == 0) {
            $("#filBranch").empty();
            $("#filBranch").append('<option value="All">All (With HO)</option>');

            $("#filProjectType").empty();
            $('#filProjectType').append("<option value='0'>All</option>");

            $("#filDepthLevel").empty();
            $("#filDepthLevel").prepend('<option value="0">All</option>');
        }
        else {
            $.ajax({
                type: 'post',
                url: "./trialBalanceProjectType",
                data: {projectId:projectId},
                success: function (data){

                    $("#filProjectType").empty();
                    // $('#filProjectType').append("<option value='0'>All</option>");
                    $.each(data['projectTypes'], function( key,obj){
                        $('#filProjectType').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                    });

                    $("#filBranch").empty();
                    $("#filBranch").append('<option value="All">All (With HO)</option>');
                    $("#filBranch").append('<option value="0">All (WithOut HO)</option>');
                    $.each(data['branches'], function( index,val){
                        $('#filBranch').append("<option value='"+index+"'>"+val+"</option>");
                    });

                },
                error:  function (data){

                }
            });
        }

    });

    // $('#filProject').change(function (event){

    //     var projectId =$('#filProject').val();
    //     var csrf = "<?php echo csrf_token(); ?>";
    //     // $('#filReportLevel').val("Branch");
    //     changeReportLevel("Branch");
    //     if (projectId != 1) {
    //         $('#filReportLevel').empty();
    //         $('#filReportLevel').append("<option value='Branch'>Branch</option>");
    //     }else{
    //         $('#filReportLevel').empty();
    //         $('#filReportLevel').append("<option value='Branch'>Branch</option>");
    //         $('#filReportLevel').append("<option value='Area'>Area</option>");
    //         $('#filReportLevel').append("<option value='Zone'>Zone</option>");
    //         $('#filReportLevel').append("<option value='Region'>Region</option>");
    //     }

    //     if (projectId == 0) {
    //         $("#filBranch").empty();
    //         $("#filBranch").append('<option value="All">All (With HO)</option>');

    //         $("#filProjectType").empty();
    //           $("#filProjectType").prepend('<option value="0">All</option>');

    //         $("#filDepthLevel").empty();
    //         $("#filDepthLevel").prepend('<option value="0">All</option>');
    //     }
    //     else {
    //         getLedgerLevelsByProject(projectId);

    //         $.ajax({
    //             type: 'post',
    //             url: './getBranchNProjectTypeByProject',
    //             data: {projectId: projectId , _token: csrf},
    //             dataType: 'json',
    //             success: function (data) {

    //                 var branchList=data['branchList'];
    //                 var projectTypeList=data['projectTypeList'];

    //                 $("#filBranch").empty();
    //                 $("#filBranch").append('<option value="All">All (With HO)</option>');
    //                 $("#filBranch").append('<option value="0">All (WithOut HO)</option>');
    //                 $("#filBranch").append('<option value="1">000 - Head Office</option>');

    //                 $.each(branchList, function( key,obj){
    //                     $('#filBranch').append("<option value='"+obj.id+"'>"+pad(obj.branchCode, 3)+" - "+obj.name+"</option>");
    //                 });

    //                 $("#filProjectType").empty();
    //                  // $("#filProjectType").prepend('<option value="0">All</option>');

    //                 $.each(projectTypeList, function( key,obj){
    //                     $('#filProjectType').append("<option value='"+obj.id+"'>"+obj.projectTypeCode+" - "+obj.name+"</option>");
    //                 });

    //             },
    //             error: function(_response){
    //                 alert("Error");
    //             }
    //         });
    //     }

    // });

    $("#filteringFormId").submit(function( event ) {
        event.preventDefault();
        var serializeValue=$(this).serialize();
        $('#loadingModal').show();
        $("#reportingDiv").load('{{URL::to("./receiptPaymentLoadReport")}}'+'?'+serializeValue, function(){
            $('#loadingModal').hide();
        });
    });

    {{-- Filtering Mehod --}}
    if ($("#searchMethod").val() == '') {
        $("#fiscalYearDiv").css('display', 'none');
        $("#dateToDiv").css('display', 'none');
        $("#dateRangeDiv").css('display', 'none');
    }
    // serach method change
    $("#searchMethod").change(function(event) {

        var searchMethod = $(this).val();
        var branchDate = "{{ \Carbon\Carbon::parse($branchDate)->format('d-m-Y') }}";
        var userBranchStartDate = "{{ \Carbon\Carbon::parse($userBranchStartDate)->format('d-m-Y') }}";
        //Fiscal Year
        if (searchMethod == '' || searchMethod == null) {
            $("#fiscalYearDiv").css('display', 'none');
            $("#dateToDiv").css('display', 'none');
            $("#dateRangeDiv").css('display', 'none');
        }
        else if(searchMethod==1){
            $("#fiscalYearDiv").css('display', 'block');
            $("#dateToDiv").css('display', 'none');
            $("#dateRangeDiv").css('display', 'none');
        }
        //current year
        else if(searchMethod==2){
            $("#fiscalYearDiv").css('display', 'none');
            $("#dateRangeDiv").css('display', 'none');
            $("#dateToDiv").css('display', 'block');

            /* Date Range To */
            $("#dateTo").datepicker({
                changeMonth: true,
                changeYear: true,
                minDate: userBranchStartDate,
                maxDate: branchDate,
                dateFormat: 'dd-mm-yy',
                onSelect: function () {
                    // $("#dateRangeFrom").datepicker("option", "maxDate", $(this).datepicker('getDate'));
                }
            });

            /* End Date Range To */
        }
        //Date Range
        else if(searchMethod==3){
            $("#fiscalYearDiv").hide();
            $("#dateToDiv").hide();
            $("#dateRangeDiv").show();

            /* Date Range From */
            $("#dateRangeFrom").datepicker({
                changeMonth: true,
                changeYear: true,
                minDate: userBranchStartDate,
                maxDate: branchDate,
                dateFormat: 'dd-mm-yy',
                onSelect: function () {
                    $("#dateRangeTo").datepicker("option", "minDate", $(this).datepicker('getDate'));
                }
            });
            /* Date Range From */

            /* Date Range To */
            $("#dateRangeTo").datepicker({
                changeMonth: true,
                changeYear: true,
                minDate: userBranchStartDate,
                maxDate: branchDate,
                dateFormat: 'dd-mm-yy',
                onSelect: function () {
                    $("#dateRangeFrom").datepicker("option", "maxDate", $(this).datepicker('getDate'));
                }
            });
        }

    });

    /*Validation*/
    $("#filteringFormSubmit").click(function(event) {

        var searchMethod = $("#searchMethod").val();
        if (searchMethod == 2) {
            if ($("#dateTo").val()=="") {
                event.preventDefault();
                $("#dateToRequired").show();
            }
            else {
                $("#dateToRequired").hide();
            }
        }
        else if (searchMethod == 3) {
            if ($("#dateRangeFrom").val()=="") {
                event.preventDefault();
                $("#dateFrome").show();
            }else {
                $("#dateFrome").hide();
            }
            if ($("#dateRangeTo").val()=="") {
                event.preventDefault();
                $("#dateToe").show();
            }
            else {
                $("#dateToe").hide();
            }
        }
    });
    /*End Validation*/

});

function getLedgerLevelsByProject(projectId){

    var csrf = "<?php echo csrf_token(); ?>";

    $.ajax({
        type: 'post',
        url: './getLedgerLevelsByProject',
        data: {projectId: projectId , _token: csrf},
        dataType: 'json',
        success: function (data) {

            $('#filDepthLevel').empty();

            $.each(data, function(index,val){
                $('#filDepthLevel').append("<option value='" + index + "'>" + val + "</option>");
            });

        },
        error: function(_response){
            alert("Error");
        }
    });

}

function pad (str, max) {
    str = str.toString();
    return str.length < max ? pad("0" + str, max) : str;
}

function changeReportLevel(elm){
    // console.log(elm)
    if (elm=="Branch") {
        $("#branchDiv").show();
        $("#areaDiv").hide();
        $("#zoneDiv").hide();
        $("#regionDiv").hide();

        $("#filArea").prop('required', false);
        $("#filZone").prop('required', false);
        $("#filRegion").prop('required', false);
    }
    else if(elm=="Area"){
        $("#branchDiv").hide();
        $("#areaDiv").show();
        $("#zoneDiv").hide();
        $("#regionDiv").hide();

        //$("#filBranch").prop('required', false);
        $("#filArea").prop('required', true);
        $("#filZone").prop('required', false);
        $("#filRegion").prop('required', false);
    }
    else if(elm=="Zone"){
        $("#branchDiv").hide();
        $("#areaDiv").hide();
        $("#zoneDiv").show();
        $("#regionDiv").hide();

        //$("#filBranch").prop('required', false);
        $("#filArea").prop('required', false);
        $("#filZone").prop('required', true);
        $("#filRegion").prop('required', false);
    }
    else if(elm=="Region"){
        $("#branchDiv").hide();
        $("#areaDiv").hide();
        $("#zoneDiv").hide();
        $("#regionDiv").show();

        //$("#filBranch").prop('required', false);
        $("#filArea").prop('required', false);
        $("#filZone").prop('required', false);
        $("#filRegion").prop('required', true);
    }
}

$(document).ready(function() {

    /*on change filReportLevel hide/show contents*/
    $("#filReportLevel").change(function(event) {
        var reportLevel = $(this).val();
        var project = $('#filProject').val();

        changeReportLevel(reportLevel);

        if(project != 1 && reportLevel != "Branch"){
            $('.non-microfin-div').html("<option>-- Select --</option>");
        }
    });
    /*end on change filReportLevel hide/show contents*/
    // $("#filReportLevel").trigger('change');

    // print script
    $("#printIcon").click(function(event) {

        {{-- codes for depth level --}}
        removeOtherDepthLevels();
        var mainContents = document.getElementById("reportingDiv").innerHTML;
        var headerContents = '';

    var printStyle = '<style>.amount{text-align: right;} thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:11px;} table, td,th {border:1px solid #222;} .text-bold{font-weight:bold} table{float:left;height:auto;padding:0px;border-collapse: collapse;width:100%;font-size:11px;page-break-inside:auto;font-family: arial!important;} tr:last-child { font-weight: bold;} thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;}  tr{ page-break-inside:avoid; page-break-after:auto }</style><style media="print">@page{margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}} .level-1 td, .level-2 td, .level-3 td, .level-4 td, .level-constant td{font-weight: bold !important;padding-left: 5px !important;text-transform: uppercase;}.level-1 td{font-size: 1.04em;}.level-2 td{font-size: 1.00em;/* padding-left: 15px !important; */}.level-3 td{font-size: .96em;/* padding-left: 25px !important; */}.level-4 td{font-size: .92em;/* padding-left: 35px !important; */}.level-constant td{font-size: .88em;/* padding-left: 45px !important; */}.level-transformed td{font-weight: normal !important;font-size: .88em;padding-left: 5px !important;text-transform: capitalize;}.level-final td{font-weight: normal !important;text-transform: none;}.total td{padding-left: 5px !important;font-size: 1.05em;}.cash-N-bank td{font-size: .88em;padding-left: 5px !important;}</style>';

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
