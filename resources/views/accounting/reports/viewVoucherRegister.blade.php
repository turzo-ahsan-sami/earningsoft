@extends('layouts/acc_layout')
@section('title', '| Voucher Register')
@section('content')

<style>
#viewVoucherForm select, #viewVoucherForm input{
    height:30px;
    border-radius: 5px;
    cursor: pointer;
}
.disabled {
    pointer-events: none;
    cursor: default;
    opacity: 0.6;
}
#voucherViewTable > thead >tr> th{
    padding:5px;
}
.form-group{
    color: black;
    font-size: 11px;
}
.form-control {
    padding: 5px;
    font-size: 11px;
}

</style>

<?php
$userId=Auth::user()->id;
$userBranchId=Auth::user()->branchId;
// echo $userBranchId;

// echo $userId;
// echo $userBranchId;
// echo $page;
// echo "checkFirstLoad $checkFirstLoad";
// echo "<br/>user_branch_id $user_branch_id";
// dd($searchedProjectTypeId);
?>
<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading"  style="padding-bottom:0px">
                        <div class="panel-options">
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
                    <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">VOUCHER REGISTER REPORT</h3>
                </div>
                <div class="panel-body panelBodyView">


                    <!-- Filtering Start-->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                {{-- <div class="col-md-2"> --}}
                                    {{-- <div class="row"> --}}

                                        {!! Form::open(array('url' => 'voucherRegisterReport/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'viewVoucherForm', 'method'=>'get')) !!}
                                        <input type="hidden" name="checkFirstLoad" value="1">

                                        @if($user_branch_id==1)
                                        <div class="col-md-1">
                                            <div class="form-group" style="">
                                                {!! Form::label('', 'Project:', ['class' => 'control-label col-sm-12']) !!}
                                                <div class="col-sm-12">
                                                    <select class="form-control" name="projectId" id="projectId">
                                                        <option value="">All</option>
                                                        @foreach ($projects as $project)
                                                        <option value={{$project->id}} @if($project->id==$projectSelected){{"selected=selected"}}@endif >{{str_pad($project->projectCode,3,"0",STR_PAD_LEFT)." - ".$project->name}}</option>
                                                        {{-- <option value={{$project->id}}>{{$project->projectCode." - ".$project->name}}</option> --}}
                                                        @endforeach
                                                    </select>
                                                    <p id='projectIde' style="max-height:3px; color:red;"></p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        @if($user_branch_id==1)
                                        <div class="col-md-1">
                                            <div class="form-group" style="">
                                                {!! Form::label('', 'Branch:', ['class' => 'control-label col-sm-12']) !!}
                                                <div class="col-sm-12">
                                                    <select class="form-control" name="branchId" id="branchId">
                                                        <option value=""  @if($branchSelected==""){{"selected=selected"}}@endif >All </option>
                                                        {{-- <option value="">All </option> --}}
                                                        {{-- <option value="0">All Branches</option> --}}
                                                        <option value="0"  @if($branchSelected===0){{"selected=selected"}}@endif >All Branches</option>
                                                        @foreach ($branches as $branch)
                                                        {{-- <option value={{$branch->id}}>{{$branch->name}}</option> --}}
                                                        <option value={{$branch->id}}  @if($branch->id==$branchSelected){{"selected=selected"}}@endif >{{str_pad($branch->branchCode,3,"0",STR_PAD_LEFT)." - ".$branch->name}}</option>
                                                        @endforeach
                                                    </select>

                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        @if($user_branch_id==1)
                                        <div class="col-md-1">
                                            <div class="form-group" style="">
                                                {!! Form::label('', 'Pro. Type:', ['class' => 'control-label col-sm-12']) !!}
                                                <div class="col-sm-12">
                                                    <select class="form-control" name="projectTypeId" id="projectTypeId">
                                                        <option value="">All</option>
                                                        @foreach ($projectTypes as $projectType)
                                                        <option value={{$projectType->id}} @if($projectType->id==$projectTypeSelected){{"selected=selected"}}@endif >{{str_pad($projectType->projectTypeCode,3,"0",STR_PAD_LEFT)." - ".$projectType->name}}</option>
                                                        {{-- <option value={{$projectType->id}}>{{$projectType->projectTypeCode." - ".$projectType->name}}</option> --}}
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="col-md-1">
                                            <div class="form-group" style="">
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

                                        <div class="col-md-1">
                                            <div class="form-group" style="">
                                                {!! Form::label('from', 'From:', ['class' => 'col-sm-12 control-label']) !!}
                                                <div class="col-sm-12">
                                                    {!! Form::text('dateFrom', $startDateSelected, ['class' => 'form-control','style'=>'cursor:pointer', 'id' => 'dateFrom', 'readonly'])!!}
                                                    <p id='dateFrome' style="max-height:3px; color:red;"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-1">
                                            <div class="form-group" style="">
                                                {!! Form::label('from', 'To:', ['class' => 'col-sm-12 control-label']) !!}
                                                <div class="col-sm-12">
                                                    {!! Form::text('dateTo', $endDateSelected, ['class' => 'form-control','style'=>'cursor:pointer', 'id' => 'dateTo', 'readonly'])!!}
                                                    <p id='dateToe' style="max-height:3px; color:red;"></p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <div class="col-md-1"></div> --}}

                                        <div class="col-md-1">
                                            <div class="form-group" style="">
                                                {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                                                <div class="col-sm-12" style="padding-top: 13%;">
                                                    {!! Form::submit('Search', ['id' => 'ledgerReportSubmit', 'class' => 'btn btn-primary btn-s', 'style'=>'font-size:12px']); !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2"></div>

                                        {!! Form::close()  !!}
                                    {{-- </div> --}}
                                {{-- </div>     --}}

                                {{-- end Div of ledgerSearch --}}

                                <div class="col-md-10"></div>
                            </div>
                        </div>

                    </div>
                    <!-- filtering end-->
                    {{-- @endif --}}
                    {{-- @if($checkFirstLoad!= 0)    --}}
                    @if($firstLoad)

                    <div id="printDiv">
                        <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}
                            <?php
                            $user_company_id = Auth::user()->company_id_fk;
                            $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
                            ?>
                            <span style="font-size:14px;">{{$company->name}}</span><br/>
                            <span style="font-size:11px;">{{$company->address}}</span><br/>
                            <span style="text-decoration: underline;  font-size:14px;">Voucher Register Report</span>
                        </div>

                        <div class="row">       {{-- div for Ledger --}}

                            <div class="col-md-12"  style="font-size: 12px;" >
                                <!-- report header -->
                                <?php
                                // $ledgerHead = DB::table('acc_account_ledger')->where('id',$searchedLedgerId)->select('name', 'code')->first();

                                // DB::table('acc_voucher_type')->where('id',$voucher->voucherTypeId)->value('name');





                                if($searchedVoucherTypeId==""){
                                    $voucherType = "All";
                                }if($searchedVoucherTypeId!=""){
                                    $voucherType = DB::table('acc_voucher_type')->where('id',$searchedVoucherTypeId)->value('name');
                                }

                                if($searchedProjectId==""){
                                    $project = "All";
                                }if($searchedProjectId!=""){
                                    $project = DB::table('gnr_project')->where('id',$searchedProjectId)->value('name');
                                }

                                if($searchedProjectTypeId==""){
                                    $projectType = "All";
                                }if($searchedProjectTypeId!=""){
                                    $projectType = DB::table('gnr_project_type')->where('id',$searchedProjectTypeId)->value('name');
                                }
                                if($searchedBranchId==""){
                                    $branch = "All ";
                                }else if($searchedBranchId==0){
                                    $branch = "All Branch Office";
                                }else{
                                    $branch = DB::table('gnr_branch')->where('id',$searchedBranchId)->value('name');
                                }
                                ?>

                                <span>

                                    <span style="color: black; float: left;">
                                        <span style="font-weight: bold;">Project Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                                        <span>{{$project}}</span>
                                    </span>
                                    <span style="color: black; float: right;">
                                        <span style="font-weight: bold;">Branch Name: <?php echo str_repeat('&nbsp;', 24);?></span>
                                        <span>{{$branch}}<?php echo str_repeat('&nbsp;', 15);?></span>
                                    </span>

                                </span>
                                <br/>

                                <span>

                                    <span style="color: black; float: left;">
                                        <span style="font-weight: bold;">Project Type: <?php echo str_repeat('&nbsp;', 5);?></span>
                                        <span>
                                            {{$projectType}}

                                        </span>
                                    </span>
                                    <span style="color: black; float: right;">
                                        <span style="font-weight: bold;">Reporting Date:<?php echo str_repeat('&nbsp;', 6);?></span>
                                        <span>{{$searchedDateFrom." to ".$searchedDateTo}}</span>
                                    </span>
                                </span>
                                <br/>

                                <span>
                                    <span style="color: black; float: left;">
                                        <span style="font-weight: bold;">Voucher Type:<?php echo str_repeat('&nbsp;', 4);?></span>
                                        <span>{{$voucherType}}</span>
                                    </span>
                                    <span style="color: black; float: right;">
                                        <span style="font-weight: bold;">Print Date: <?php echo str_repeat('&nbsp;', 22);?></span>
                                        <span>{{\Carbon\Carbon::now()->format('d-m-Y g:i A')}}</span>
                                    </span>
                                </span>
                                <br/>
                            </div>
                        </div>

                        <div>
                            <script type="text/javascript"></script>
                        </div>
                        <div class="row" style=" margin: 15px 0px;">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered dt-responsive nowrap" width="100%" id="voucherRegisterViewTable" border="1pt solid ash" style="border-collapse: collapse; padding-bottom: 20px !important; color: black;">
                                    <thead>
                                        <tr>
                                            <th style="width:4%">SL#</th>
                                            <th style="width:7%">Voucher Date</th>
                                            <th style="width:12%">Voucher Code</th>
                                            <th style="width:9%">Voucher Type</th>
                                            <th style="width:12%">Project Type</th>
                                            <th style="width:8%">Amount</th>
                                            <th style="width:">Global Narration</th>
                                            <th style="width:7%">Entry By</th>
                                        </tr>
                                        {{ csrf_field() }}
                                    </thead>
                                    <tbody>

                                        @php
                                        $no=0;
                                        @endphp
                                        @if (!$vouchers->count())
                                        <tr>
                                            <td colspan="10">No Voucher Available In This Search Range</td>
                                        </tr>
                                        @endif

                                        @foreach($vouchers as $voucher)
                                        @php
                                        $debitAmount = DB::table('acc_voucher_details')->where('voucherId', $voucher->id)->value('debitAcc');
                                        $creditAmount = DB::table('acc_voucher_details')->where('voucherId', $voucher->id)->value('creditAcc');
                                        $ftFrom = DB::table('acc_voucher_details')->where('voucherId', $voucher->id)->value('ftFrom');
                                        @endphp

                                        <tr class="item{{$voucher->id}}">


                                            <td>{{++$no}}</td>
                                            <td>{{date('d-m-Y',strtotime($voucher->voucherDate))}}</td>
                                            <td>{{$voucher->voucherCode}}</td>
                                            <td>
                                                {{DB::table('acc_voucher_type')->where('id',$voucher->voucherTypeId)->value('name')}}
                                            </td>
                                            <td class='name' >
                                                {{DB::table('gnr_project_type')->where('id',$voucher->projectTypeId)->value('name')}}
                                            </td>
                                            <td class="amount" style="padding-right: 5px; ">
                                                {{number_format( DB::table('acc_voucher_details')->where('voucherId', $voucher->id)->sum('amount'), 2, '.', ',')}}
                                            </td>
                                            {{-- <td>{{$voucher->amount}}</td> --}}
                                            <td class="name" style="padding-left: 5px;">{{$voucher->globalNarration}}</td>
                                            <td class="name" style="padding-left: 5px;">
                                                <?php
                                                $userIdOfpreBy=DB::table('users')->where('id', $voucher->prepBy)->value('emp_id_fk');
                                                ?>
                                                {{DB::table('hr_emp_general_info')->where('id', $userIdOfpreBy)->value('emp_name_english')}}
                                            </td>

                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>  <!-- End of table-responsive div -->
                        </div>
                    </div>  <!-- End of print div -->
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<script type="text/javascript">
    $(document).ready(function(){


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
          download: "Voucher Register Report_"+ today + ".xls"}).appendTo("body").get(0).click();
        e.preventDefault();
    });

        var voucherTypeId="{{$searchedVoucherTypeId}}";
        $("#voucherTypeId").val(voucherTypeId);


        function toDate(dateStr) {
            var parts = dateStr.split("-");
            return new Date(parts[2], parts[1] - 1, parts[0]);
        }

        /* Date Range From */
        $("#dateFrom").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2010:c",
            minDate: new Date(2010, 07 - 1, 01),
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                $('#dateFrome').hide();
                $("#dateTo").datepicker("option","minDate",new Date(toDate($(this).val())));
                $( "#dateTo" ).datepicker( "option", "disabled", false );
            }
        });
        /* Date Range From */

        /* Date Range To */
        $("#dateTo").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2010:c",
            minDate: new Date(2010, 07 - 1, 01),
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                $('#dateToe').hide();
                $("#dateFrom").datepicker("option","maxDate",new Date(toDate($(this).val())));
            }
        });
//$( "#dateTo" ).datepicker( "option", "disabled", true );
/* End Date Range To */

var dateFromData = $("#dateFrom").val();

if (dateFromData!="") {
    $("#dateTo").datepicker("option","minDate",new Date(toDate(dateFromData)));
        //$("#dateTo").datepicker( "option", "disabled", false );
    }


    function pad (str, max) {
        str = str.toString();
        return str.length < max ? pad("0" + str, max) : str;
    }

    $("#projectId").change(function () {
        $('#projectIde').hide();
        var projectId = this.value;

        var csrf = "<?php echo csrf_token(); ?>";
        $.ajax({
            type: 'post',
            url: './getBranchNProjectTypeByProject',
            data: {projectId: projectId , _token: csrf},
            dataType: 'json',
            success: function (data) {
                // alert(JSON.stringify(data));
                var branchList=data['branchList'];
                var projectTypeList=data['projectTypeList'];

                $("#branchId").empty();
                $("#branchId").append('<option value="">All </option>');
                $("#branchId").append('<option value="0">All Branches</option>');
                $("#branchId").append('<option value="1">000 - Head Office</option>');

                $.each(branchList, function( key,obj){
                    $('#branchId').append("<option value='"+obj.id+"'>"+pad(obj.branchCode,3)+" - "+obj.name+"</option>");
                });

                $("#projectTypeId").empty();
                $("#projectTypeId").prepend('<option value="">All</option>');

                $.each(projectTypeList, function( key,obj){
                    $('#projectTypeId').append("<option value='"+obj.id+"'>"+obj.projectTypeCode+" - "+obj.name+"</option>");
                });

            },
            error: function(_response){
                alert("Error");
            }
        });
    });


});//ready function end



    $(function(){
        $("#printIcon").click(function(){

            // $("#accLedgerReportTable").removeClass('table table-striped table-bordered');
            // $('#accLedgerReportTable_info').hide();
            // $('#accLedgerReportTable_paginate').hide();

            // $("#accLedgerReportTable").removeClass('table table-striped table-bordered');

            // var printStyle = '<style>#voucherRegisterViewTable{float:left;height:auto;padding:0px 0px 20px 0px;width:100%;font-size:11px;border:1pt solid ash;page-break-inside:auto; font-family: arial!important;} tr:last-child { font-weight: bold;} thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;} tbody tr td { text-align:center;vertical-align: middle;padding:3px ;font-size:10px;} tr{ page-break-inside:avoid; page-break-after:auto } </style><style>@page {size: auto;margin: 25;}</style>';

            var printStyle = '<style>#voucherRegisterViewTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt solid ash;page-break-inside:auto;font-family: arial!important;} tr:last-child { font-weight: bold;} thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;} tbody tr td { text-align:center;vertical-align: middle;padding:3px ;font-size:10px;} tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{size:portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

            var mainContents = document.getElementById("printDiv").innerHTML;

            // var footerContents = "<br><br><br><div class='row' style='font-size:12px; padding-left:15px; padding-top:40px !important;'>Prepared By <span style='display:inline-block; width: 34%;'></span> Checked By <span style='display:inline-block; width: 34%; padding-right:20px;'></span> Approved By</div>";

            var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-top:40px;'>Prepared By <span style='display:inline-block; width: 36%; padding-top:40px;'></span> Checked By <span style='display:inline-block; width: 34%; padding-right:15px; padding-top:40px;'></span>Approved By</div>";

            var printContents = '<div id="order-details-wrapper" style="padding: 10px;">' + printStyle + mainContents+footerContents+'</div>';

            var win = window.open('','printwindow');
            win.document.write(printContents);
            win.print();
        // $("#reportingTable").addClass('table table-striped table-bordered');
        win.close();

            // var printContents = document.getElementById("printView").innerHTML;
            // var originalContents = document.body.innerHTML;
            // document.body.innerHTML ="" + printContents;
            // window.print();
            // document.body.innerHTML = originalContents;

            // $("#accLedgerReportTable").addClass('table table-striped table-bordered');
            // location.reload();

        });


    });


</script>
{{-- <script src="{{asset('js/jquery-1.11.1.min.js')}}"></script> --}}

@endsection
