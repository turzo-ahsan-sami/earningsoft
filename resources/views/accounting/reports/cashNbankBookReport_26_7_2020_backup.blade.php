@php
if(isset($_GET['projectId'])){
             // dd($ledgerIdsArray);
}
@endphp
@php
$branch = DB::table('gnr_branch')->where('id',Auth::user()->branchId)->select('id','branchCode')->first();
$userBranchCode = $branch->branchCode;
$headOfficeId = DB::table('gnr_branch')->where('companyId', Auth::user()->company_id_fk)->where('branchCode', 0)->value('id');
@endphp

@extends('layouts/acc_layout')
@section('title', '| Cash And Bank Book Report')
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                        <div class="panel-options">
                    {{-- <button id="printIcon" class="btn btn-info pull-right"  target="_blank" style="font-size: 14px; margin-top: 10px; border: 3px solid #525659; border-radius: 25px;">
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
                <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Cash And Bank Book Report</h3>
            </div>

            <div class="panel-body panelBodyView">


                <!-- Filtering Start-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">

                            {{-- <div class="col-md-2"> --}}
                                {{-- <div class="row"> --}}

                                    {!! Form::open(array('url' => 'cashNbankBookReport/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'bankReportForm', 'method'=>'get')) !!}

                                  @if($userBranchCode == 0)
                                  <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Project:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                <select class="form-control" name="projectId" id="projectId" required>
                                                 {{--    <option value="">Select Project</option> --}}
                                                    @foreach ($projects as $project)
                                                    <option value={{$project->id}}>{{str_pad($project->projectCode,3,"0",STR_PAD_LEFT)." - ".$project->name}}</option>
                                                    {{-- <option value={{$project->id}}>{{$project->projectCode." - ".$project->name}}</option> --}}
                                                    @endforeach
                                                </select>
                                                <p id='projectIde' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>
                                
                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Branch:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                <select class="form-control" name="branchId" id="branchId">
                                                    <option value="">All</option>
                                                    <option value="0" @if($branchSelected===0){{"selected=selected"}}@endif >
                                                        All Branch Office
                                                    </option>
                                                    @foreach ($branches as $branch)
                                                    {{-- <option value={{$branch->id}}>{{$branch->name}}</option> --}}
                                                    <option value={{$branch->id}}  @if($branch->id==$branchSelected){{"selected=selected"}}@endif >{{str_pad($branch->branchCode,3,"0",STR_PAD_LEFT)." - ".$branch->name}}</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                  @endif

                                   
                                    <div class="col-md-1" hidden>
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
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

                                   

                                    <div class="col-md-1" >
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Ledger:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                <select class="form-control" name="ledgerId" id="ledgerId" >
                                                    <option value="">All</option>
                                                    @foreach ($childrenLedger as $childLedger)
                                                    {{-- <option value={{$childLedger->id}}>{{$childLedger->name}}</option> --}}
                                                    <option value={{$childLedger->id}}>{{$childLedger->code." - ".$childLedger->name}}</option>
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

                                    <?php $dateFromSelected= date("d-m-Y",strtotime($softwareStartDate));?>
                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('from', 'From:', ['class' => 'col-sm-12 control-label']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::text('dateFrom', $dateFromSelected, ['class' => 'form-control','style'=>'cursor:pointer', 'id' => 'dateFrom', 'readonly'])!!}
                                                <p id='dateFrome' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <?php $dateToSelected= date("d-m-Y",strtotime($softwareDate));?>
                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('from', 'To:', ['class' => 'col-sm-12 control-label']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::text('dateTo', $dateToSelected, ['class' => 'form-control','style'=>'cursor:pointer', 'id' => 'dateTo', 'readonly'])!!}
                                                <p id='dateToe' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="col-md-1"></div> --}}

                                    <div class="col-md-1">
                                        <div class="form-group" style="">
                                            {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12" style="padding-top: 18px;">
                                                {!! Form::submit('Search', ['id' => 'ledgerReportSubmit', 'class' => 'btn btn-primary animated fadeInRight']); !!}
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


                <div>
                </div>

                <?php
                // if ($searchedLedgerId!=null || $searchedLedgerId!="") {
                if(isset($_GET['projectId']) || isset($_GET['ledgerId'])){

                    $projectIdArray = array();
                    $projectTypeIdArray = array();
                    $branchIdArray = array();
                    $voucherTypeIdArray = array();

                    $debitAmountArray=array();
                    $creditAmountArray=array();
                // echo "<br> searchedProjectId:".$searchedProjectId;
                // echo "<br> searchedProjectTypeId:".$searchedProjectTypeId;
                // echo "<br> searchedBranchId:".$searchedBranchId;
                // echo "<br> searchedLedgerId:".$searchedLedgerId;


                //Project
                    if ($searchedProjectId==null) {
                        if ($headOfficeId) {
                            $projectIdArray = DB::table('gnr_project')->where('companyId',Auth::user()->company_id_fk)->pluck('id')->toArray();
                        }
                        else{
                            array_push($projectIdArray, (int) json_decode($user_project_id));
                            $searchedProjectId= (int) json_decode($user_project_id);
                        }
                    }
                    else{
                        array_push($projectIdArray, (int) json_decode($searchedProjectId));
                    }

                //Project Type
                    if ($searchedProjectTypeId==null) {
                        if ($headOfficeId) {
                            $projectTypeIdArray = DB::table('gnr_project_type')->where('companyId',Auth::user()->company_id_fk)->whereIn('projectId', $projectIdArray)->pluck('id')->toArray();
                        }
                        else{
                            array_push($projectTypeIdArray, (int) json_decode($user_project_type_id));
                            $searchedProjectTypeId=(int) json_decode($user_project_type_id);
                        }
                    }
                    else{
                        array_push($projectTypeIdArray, (int) json_decode($searchedProjectTypeId));
                    }

                //Branch
                    if ($searchedBranchId==null) {
                        if ($headOfficeId) {
                            $branchIdArray = DB::table('gnr_branch')->where('companyId',Auth::user()->company_id_fk)->whereIn('projectId', $projectIdArray)->orWhere('id', 1)->pluck('id')->toArray();
                        }
                        else{
                            array_push($branchIdArray, (int) json_decode($user_branch_id));
                            $searchedBranchId=(int) json_decode($user_branch_id);
                        }
                    }
                    elseif ($searchedBranchId==0) {
                        $branchIdArray = DB::table('gnr_branch')->where('companyId',Auth::user()->company_id_fk)->whereIn('projectId', $projectIdArray)->where('id', '!=', 1)->pluck('id')->toArray();
                    }
                    else{
                        array_push($branchIdArray, (int) json_decode($searchedBranchId));
                    }

                //VoucherType
                    if ($searchedVoucherTypeId==null) {
                        $voucherTypeIdArray = DB::table('acc_voucher_type')->pluck('id')->toArray();
                    }
                    else{
                        array_push($voucherTypeIdArray, (int) json_decode($searchedVoucherTypeId));
                    }

                //Get Start Date and End Date
                    $startDate = date('Y-m-d',strtotime($searchedDateFrom));
                    $endDate = date('Y-m-d',strtotime($searchedDateTo));


                // echo "<br/>projectIdArray: ";
                // var_dump($projectIdArray);
                // echo "<br/>projectTypeIdArray: ";
                // var_dump($projectTypeIdArray);
                // echo "<br/>branchIdArray: ";
                // var_dump($branchIdArray);
                // echo "<br/>";
                // var_dump($voucherTypeIdArray);
                // echo "<br/>";
                // echo $startDate;
                // echo "<br/>";
                // echo $endDate;
                // echo "<br/>";
                // echo "<br/>";

                // $voucherIdMatched = DB::table('acc_voucher')
                //         ->whereIn('projectId',$projectIdArray)
                //         ->whereIn('projectTypeId',$projectTypeIdArray)
                //         ->whereIn('branchId',$branchIdArray)
                //         ->whereIn('voucherTypeId',$voucherTypeIdArray)
                //         ->where('status', 1)
                //         ->where(function ($query) use ($startDate,$endDate){
                //               $query->where('voucherDate','>=',$startDate)
                //               ->where('voucherDate','<=',$endDate);
                //             })
                //         ->pluck('id')->toArray();

                // // echo "<br/>";
                // // var_dump($voucherIdMatched);
                // // echo "<br/>";

                // $voucherDetails = DB::table('acc_voucher_details')
                //         ->whereIn('voucherId',$voucherIdMatched)
                //         ->where('status', 1)
                //         ->where(function ($query) use ($searchedLedgerId){
                //                   $query->where('debitAcc', $searchedLedgerId)
                //                   ->orWhere('creditAcc', $searchedLedgerId);
                //                 })
                //         ->select('voucherId','debitAcc','creditAcc','amount')
                //         ->get();

                // echo "<br/>";
                // var_dump($voucherDetails);


                    $voucherDetails = DB::table($acc_voucher_join)
                    ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                    ->whereIn('av.projectId',$projectIdArray)
                    ->whereIn('av.projectTypeId',$projectTypeIdArray)
                    ->whereIn('av.branchId',$branchIdArray)
                    ->whereIn('av.voucherTypeId',$voucherTypeIdArray)
                    ->where('av.status', 1)
                    ->where(function ($query) use ($startDate, $endDate){
                      $query->where('av.voucherDate','>=',$startDate)
                      ->where('av.voucherDate','<=',$endDate);
                  })
                    ->where(function ($query) use ($ledgerIdsArray){
                      $query->whereIn('acc_voucher_details.debitAcc', $ledgerIdsArray)
                      ->orWhereIn('acc_voucher_details.creditAcc', $ledgerIdsArray);
                  })
                    ->select('av.voucherDate','av.voucherCode','av.globalNarration','acc_voucher_details.voucherId','acc_voucher_details.debitAcc','acc_voucher_details.creditAcc','acc_voucher_details.amount')
                    ->orderBy('av.voucherDate')
                    ->get();


                    $ledgerInfo=DB::table('acc_account_ledger')
                    ->select(DB::raw("CONCAT(name, ' [', code, ']') AS nameWithCode"), 'id')
                    ->pluck('nameWithCode', 'id')
                    ->toArray();


                    ?>


                    <?php
                    // echo number_format( , 2, '.', '');

                    $fiscalYearValue = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->select('fyStartDate', 'fyEndDate', 'id')->first();
                    // var_dump($fiscalYearValue);

                    $previousFisStartDate=date('Y-m-d', strtotime("first day of last year".$fiscalYearValue->fyStartDate));
                    $previousFisEndDate=date('Y-m-d', strtotime("last day of -1 year".$fiscalYearValue->fyEndDate));

                    $previousFiscalYearValue = DB::table('gnr_fiscal_year')->where('fyStartDate',$previousFisStartDate)->where('fyEndDate',$previousFisEndDate)->select('fyStartDate', 'fyEndDate', 'id')->first();
                    // var_dump($previousFiscalYearValue);

                    $openingBalanceAmountByDate = DB::table($acc_opening_balance)->where('companyIdFk', $user_company_id)->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('companyIdFk',Auth::user()->company_id_fk)->whereIn('ledgerId', $ledgerIdsArray)->where('fiscalYearId', $previousFiscalYearValue->id)->sum('balanceAmount');
                    // dd($openingBalanceAmountByDate);

                    // $openingBalanceAmountByDate = DB::table('acc_opening_balance')->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('ledgerId', $searchedLedgerId)->where('fiscalYearId', $fiscalYearValue->id)->sum('balanceAmount');
                    // echo "<br/>";
                    // var_dump($openingBalanceAmountByDate);
                    // echo "<br/>";
                    // $temp=$fiscalYearValue->fyStartDate;

                    $voucherIdMatchedForOpBal = DB::table($acc_voucher)
                    ->where('companyId', $user_company_id)
                    ->whereIn('projectId',$projectIdArray)
                    ->whereIn('projectTypeId',$projectTypeIdArray)
                    ->whereIn('branchId',$branchIdArray)
                    ->where('voucherDate','>=',$fiscalYearValue->fyStartDate)
                    ->where('voucherDate','<',$startDate)
                    ->where('status', 1)
                    ->pluck('id')
                    ->toArray();

                    // dd($voucherIdMatchedForOpBal);
                    // var_dump($voucherIdMatchedForOpBal);
                    // echo "<br/>";

                    // dd($ledgerIdsArray);

                    $debitAccAmount = DB::table('acc_voucher_details')
                    ->whereIn('voucherId',$voucherIdMatchedForOpBal)
                    ->whereIn('debitAcc', $ledgerIdsArray)
                    ->where('status', 1)
                    ->sum('amount');

                    // dd($debitAccAmount);
//
                    $creditAccAmount = DB::table('acc_voucher_details')
                    ->whereIn('voucherId',$voucherIdMatchedForOpBal)
                    ->whereIn('creditAcc', $ledgerIdsArray)
                    ->where('status', 1)
                    ->sum('amount');

                    // dd($creditAccAmount);

                    // $totalOpeningBalanceAmount = $openingBalanceAmountByDate + $debitAccAmount - $creditAccAmount;
                    $totalOpeningBalanceAmount = $openingBalanceAmountByDate + $debitAccAmount - $creditAccAmount;
                    // dd($totalOpeningBalanceAmount);

                    // dd($totalOpeningBalanceAmount);

                    // echo "<br/>openingBalanceAmountByDate:";     var_dump($openingBalanceAmountByDate);
                    // echo "<br/>debitAccAmount:";     var_dump($debitAccAmount);
                    // echo "<br/>creditAccAmount:";    var_dump($creditAccAmount);
                    // echo "<br/>totalOpeningBalanceAmount:";    var_dump($totalOpeningBalanceAmount);


                    // $openingBalanceAmount = DB::table('acc_opening_balance')->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('ledgerId', $searchedLedgerId)->sum('balanceAmount');
                    // $balanceAmount=$openingBalanceAmount;
                    $balanceAmount = $totalOpeningBalanceAmount;
                    // var_dump($balanceAmount);
                    ?>

                    <div id="printDiv">
                        <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}
                            <?php
                            $user_company_id = Auth::user()->company_id_fk;
                            $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
                            ?>
                            <span style="font-size:14px;">{{$company->name}}</span><br/>
                            <span style="font-size:11px;">{{$company->address}}</span><br/>
                            <span style="text-decoration: underline;  font-size:14px;">Bank Book Report</span>
                        </div>

                        <div class="row">       {{-- div for Ledger --}}

                            <div class="col-md-12"  style="font-size: 12px;" >
                                <?php
                                // $ledgerHead = DB::table('acc_account_ledger')->whereIn('id',$ledgerIdsArray)->select('name', 'code')->first();

                                if($searchedLedgerId == null){
                                    $ledgerHead = "All ";
                                }else{
                                    $ledgerHead = DB::table('acc_account_ledger')->whereIn('id',$ledgerIdsArray)->value('name');
                                }
                                $project = DB::table('gnr_project')->where('id',$searchedProjectId)->select('name')->first();
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
                                        <span style="font-weight: bold;">Ledger Head:<?php echo str_repeat('&nbsp;', 5);?></span>
                                        <span>{{$ledgerHead}}</span>
                                    </span>
                                    <span style="color: black; float: right;">
                                        <span style="font-weight: bold;">Reporting Date:<?php echo str_repeat('&nbsp;', 6);?></span>
                                        <span>{{$searchedDateFrom." to ".$searchedDateTo}}</span>
                                    </span>
                                </span>
                                <br/>

                                <span>
                                    <span style="color: black; float: left;">
                                        <span style="font-weight: bold;">Project Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                                        <span>{{$project->name}}</span>
                                    </span>
                                </span>
                            
                                <span>
                                    <span style="color: black; float: right;">
                                        <span style="font-weight: bold;">Print Date: <?php echo str_repeat('&nbsp;', 22);?></span>
                                        <span>{{\Carbon\Carbon::now()->format('d-m-Y H:i:s')}}</span>
                                    </span>
                                </span>
                                <br/>

                                <span>
                                    <span style="color: black; float: left;">
                                        <span style="font-weight: bold;">Branch Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                                        <span>{{$branch}}</span>
                                    </span>
                                </span>

                            </div>



                        </div>


                        <div class="row" style=" margin: 15px 0px;">
                            {{-- <div class="table-responsive"> --}}
                                <table class="table table-striped table-bordered" id="accBankBookReportTable" width="100%" border="1pt solid ash" style="border-collapse: collapse;" >
                                    {{-- <table class="table table-striped table-bordered dt-responsive nowrap" border="1pt solid ash" style="border-collapse: collapse;" id="accBankBookReportTable"> --}}
                                        <thead>
                                            <tr>
                                                <th style="width: 4%;">SL#</th>
                                                <th style="padding: 12px 20px; width: 6%;">Date</th>
                                                <th style="width: 10%;">Voucher Code</th>
                                                <th style="width: 30%;">Account Head</th>
                                                <th style="width: 25%;">Narration/Cheque Details</th>
                                                <th style="width: 7%;">Dedit Amount</th>
                                                <th style="width: 7%;">Credit Amount</th>
                                                <th style="width: 7%;">Balance</th>
                                                <th style="padding: 12px 10px; width: 4%;">Dr/Cr</th>
                                            </tr>
                                            {{ csrf_field() }}
                                        </thead>
                                        <?php $no=0; ?>
                                        <tbody>
                                            <tr>
                                                <td>{{++$no}}</td>
                                                <td></td>
                                                <td></td>
                                                <td style="text-align: left;">Opening Balance</td>
                                                <td></td>
                                                <td style="text-align: right;">-</td>
                                                <td style="text-align: right;">-</td>

                                                <td style="text-align: right;">{{number_format(abs($totalOpeningBalanceAmount), 2, '.', ',')}}</td>
                                                {{-- <td style="text-align: right;">{{number_format(abs($openingBalanceAmount), 2, '.', ',')}}</td> --}}
                                                <td>
                                                    <?php
                                                    if($totalOpeningBalanceAmount<0){ echo "Cr"; }else{ echo "Dr"; }
                                        // if($balanceAmount<0){ echo "Cr"; }else{ echo "Dr"; }
                                                    ?>
                                                </td>
                                            </tr>

                                            @foreach ($voucherDetails as $voucherDetail)
                                            @php
                                            $count = 0;
                                            if(in_array($voucherDetail->debitAcc,$ledgerIdsArray)){
                                                $count++;
                                            }
                                            if (in_array($voucherDetail->creditAcc,$ledgerIdsArray)) {
                                                $count++;
                                            }
                                            @endphp

                                            @if($count==1)
                                            <tr>
                                                <td class="">{{++$no}}</td>
                                                <?php
                                            // $voucherInfo = DB::table('acc_voucher')->where('id', $voucherDetail->voucherId)->select('voucherDate','voucherCode','globalNarration')->where('status', 1)->first();
                                                ?>
                                                <td style="text-align: left;">{{ Carbon\Carbon::parse($voucherDetail->voucherDate)->format('d-m-Y') }}</td>
                                                <td style="text-align: left;">{{$voucherDetail->voucherCode}}</td>
                                                <td style="text-align: left;">
                                                    <?php

                                                    if(!in_array($voucherDetail->debitAcc,$ledgerIdsArray)){
                                                        /*if($ledgerId != $voucherDetail->debitAcc){*/
                                                        // $ledgerInfo = DB::table('acc_account_ledger')->where('id', $voucherDetail->debitAcc)->select('name','code')->first();
                                                            $ledgerNameWithCode=$ledgerInfo[$voucherDetail->debitAcc];
                                                            /*}else if($ledgerId!=$voucherDetail->creditAcc){*/
                                                            }else if(!in_array($voucherDetail->creditAcc,$ledgerIdsArray)){
                                                        // $ledgerInfo = DB::table('acc_account_ledger')->where('id', $voucherDetail->creditAcc)->select('name','code')->first();
                                                                $ledgerNameWithCode=$ledgerInfo[$voucherDetail->creditAcc];
                                                            }
                                                            ?>
                                                            {{$ledgerNameWithCode}}
                                                        </td>
                                                        <td style="text-align: left;">{{$voucherDetail->globalNarration}}</td>

                                                        <?php
                                                        if(in_array($voucherDetail->debitAcc,$ledgerIdsArray)){
                                                            /*if($ledgerId==$voucherDetail->debitAcc){*/
                                                                array_push($debitAmountArray,$voucherDetail->amount);
                                                                $balanceAmount=$balanceAmount+$voucherDetail->amount;
                                                                ?>
                                                                <td style="text-align: right;">{{number_format( $voucherDetail->amount, 2, '.', ',')}}</td>
                                                                <td style="text-align: right;">-</td>

                                                                <?php
                                                            }else if(in_array($voucherDetail->creditAcc,$ledgerIdsArray)){
                                                                /*}else if($ledgerId==$voucherDetail->creditAcc){*/
                                                                    array_push($creditAmountArray,$voucherDetail->amount);
                                                                    $balanceAmount=$balanceAmount-$voucherDetail->amount;
                                                                    ?>
                                                                    <td style="text-align: right;">-</td>
                                                                    <td style="text-align: right;">{{number_format( $voucherDetail->amount, 2, '.', ',')}}</td>

                                                                    <?php }else{?>
                                                                    <td style="text-align: right;">-</td>
                                                                    <td style="text-align: right;">-</td>
                                                                    <?php } ?>

                                                                    {{-- <td>{{number_format( $balanceAmount, 2, '.', ',')}}</td> --}}
                                                                    <td style="text-align: right;">{{number_format( abs($balanceAmount), 2, '.', ',')}}</td>
                                                                    <td>
                                                                        <?php if($balanceAmount<0){ echo "Cr"; }else{ echo "Dr";} ?>
                                                                    </td>
                                                                </tr>
                                                                @else

                                                                <tr>
                                                                    <td class="">{{++$no}}</td>
                                                                    <?php
                                            // $voucherInfo = DB::table('acc_voucher')->where('id', $voucherDetail->voucherId)->select('voucherDate','voucherCode','globalNarration')->where('status', 1)->first();
                                                                    ?>
                                                                    <td style="text-align: left;">{{ Carbon\Carbon::parse($voucherDetail->voucherDate)->format('d-m-Y') }}</td>
                                                                    <td style="text-align: left;">{{$voucherDetail->voucherCode}}</td>
                                                                    <td style="text-align: left;">
                                                                        <?php

                                                                        if(!in_array($voucherDetail->debitAcc,$ledgerIdsArray)){
                                                                            /*if($ledgerId != $voucherDetail->debitAcc){*/
                                                        // $ledgerInfo = DB::table('acc_account_ledger')->where('id', $voucherDetail->debitAcc)->select('name','code')->first();
                                                                                $ledgerNameWithCode=$ledgerInfo[$voucherDetail->debitAcc];
                                                                                /*}else if($ledgerId!=$voucherDetail->creditAcc){*/
                                                                                }
                                                                                ?>
                                                                                {{isset($ledgerNameWithCode)? $ledgerNameWithCode : 'demo name'}}
                                                                            </td>
                                                                            <td style="text-align: left;">{{$voucherDetail->globalNarration}}</td>

                                                                            <?php
                                                                            if(in_array($voucherDetail->debitAcc,$ledgerIdsArray)){
                                                                                /*if($ledgerId==$voucherDetail->debitAcc){*/
                                                                                    array_push($debitAmountArray,$voucherDetail->amount);
                                                                                    $balanceAmount=$balanceAmount+$voucherDetail->amount;
                                                                                    ?>
                                                                                    <td style="text-align: right;">{{number_format( $voucherDetail->amount, 2, '.', ',')}}</td>
                                                                                    <td style="text-align: right;">-</td>


                                                                                    <?php }else{?>
                                                                                    <td style="text-align: right;">-</td>
                                                                                    <td style="text-align: right;">-</td>
                                                                                    <?php } ?>

                                                                                    {{-- <td>{{number_format( $balanceAmount, 2, '.', ',')}}</td> --}}
                                                                                    <td style="text-align: right;">{{number_format( abs($balanceAmount), 2, '.', ',')}}</td>
                                                                                    <td>
                                                                                        <?php if($balanceAmount<0){ echo "Cr"; }else{ echo "Dr";} ?>
                                                                                    </td>
                                                                                </tr>

                                                                                <tr>
                                                                                    <td class="">{{++$no}}</td>
                                                                                    <?php
                                            // $voucherInfo = DB::table('acc_voucher')->where('id', $voucherDetail->voucherId)->select('voucherDate','voucherCode','globalNarration')->where('status', 1)->first();
                                                                                    ?>
                                                                                    <td style="text-align: left;">{{ Carbon\Carbon::parse($voucherDetail->voucherDate)->format('d-m-Y') }}</td>
                                                                                    <td style="text-align: left;">{{$voucherDetail->voucherCode}}</td>
                                                                                    <td style="text-align: left;">
                                                                                        <?php

                                                                                        if(!in_array($voucherDetail->creditAcc,$ledgerIdsArray)){
                                                        // $ledgerInfo = DB::table('acc_account_ledger')->where('id', $voucherDetail->creditAcc)->select('name','code')->first();
                                                                                            $ledgerNameWithCode=$ledgerInfo[$voucherDetail->creditAcc];
                                                                                        }
                                                                                        ?>
                                                                                        {{isset($ledgerNameWithCode)? $ledgerNameWithCode : 'demo name'}}
                                                                                    </td>
                                                                                    <td style="text-align: left;">{{$voucherDetail->globalNarration}}</td>

                                                                                    <?php
                                                                                    if(in_array($voucherDetail->creditAcc,$ledgerIdsArray)){
                                                                                        /*}else if($ledgerId==$voucherDetail->creditAcc){*/
                                                                                            array_push($creditAmountArray,$voucherDetail->amount);
                                                                                            $balanceAmount=$balanceAmount-$voucherDetail->amount;
                                                                                            ?>
                                                                                            <td style="text-align: right;">-</td>
                                                                                            <td style="text-align: right;">{{number_format( $voucherDetail->amount, 2, '.', ',')}}</td>

                                                                                            <?php }else{?>
                                                                                            <td style="text-align: right;">-</td>
                                                                                            <td style="text-align: right;">-</td>
                                                                                            <?php } ?>

                                                                                            {{-- <td>{{number_format( $balanceAmount, 2, '.', ',')}}</td> --}}
                                                                                            <td style="text-align: right;">{{number_format( abs($balanceAmount), 2, '.', ',')}}</td>
                                                                                            <td>
                                                                                                <?php if($balanceAmount<0){ echo "Cr"; }else{ echo "Dr";} ?>
                                                                                            </td>
                                                                                        </tr>
                                                                                        @endif


                                                                                        @endforeach
                                                                                        <tr style="font-weight: bold;">
                                                                                            {{-- <td><span style="display: none;">{{++$no}}</span></td> --}}
                                                                                            {{-- <td></td> --}}
                                                                                            {{-- <td></td> --}}
                                                                                            {{-- <td></td> --}}
                                                                                            <td colspan="5">Total</td>
                                                                                            <td style="text-align: right;">{{number_format(array_sum($debitAmountArray), 2, '.', ',')}}</td>
                                                                                            <td style="text-align: right;">{{number_format(array_sum($creditAmountArray), 2, '.', ',')}}</td>
                                                                                            <?php
                                                                                            $totalBalanceAmount = ((array_sum($debitAmountArray) + $totalOpeningBalanceAmount) - array_sum($creditAmountArray));
                                                                                            ?>
                                                                                            <td style="text-align: right;"> {{number_format(abs($totalBalanceAmount), 2, '.', ',')}} </td>
                                                                                            {{-- <td> {{number_format($totalBalanceAmount, 2, '.', ',')}} </td> --}}
                                                                                            <td>
                                                                                                <?php if($totalBalanceAmount<0){ echo "Cr";}else{ echo "Dr";} ?>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            {{-- </div>   --}}
                                                                            {{-- TableResponsiveDiv --}}
                                                                            <?php }?>
                                                                        </div> {{-- rowDiv --}}

                                                                    </div> {{-- printDiv --}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @endsection

                                            <script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>

                                            <script type="text/javascript">
                                                $(document).ready(function(){
                                                $(".selectPicker").select2();
                                                    
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
          download: "Cash And Bank Book Report_"+ today + ".xls"}).appendTo("body").get(0).click();
        e.preventDefault();
    });

                                                 $('#ledgerReportSubmit').click(function(){
                                                    var ledgerIdChecked=$('#ledgerId').val();
                                                    if(ledgerIdChecked){$('#ledgerIde').hide();}/*else{$('#ledgerIde').html('*Required');}*/
                                                });
                                                 $('#ledgerId').on('change', function (e) {
                                                    var ledgerId = $("#ledgerId").val();
                                                    if(ledgerId){$('#ledgerIde').hide();}else{$('#ledgerIde').show();}
                                                });

                                                 var searchedVoucherTypeId = '<?php echo $searchedVoucherTypeId; ?>';
                                                 $('#voucherTypeId').val(searchedVoucherTypeId);

                                                 var searchedLedgerId = '{{$searchedLedgerId}}';
                                                 $('#ledgerId').val(searchedLedgerId);

                                                 var searchedDateFrom = '<?php echo $searchedDateFrom; ?>';
                                                 if (searchedDateFrom=="") {
                                                    $("#dateFrom").val("<?php echo $dateFromSelected;?>");
                                                }else{
                                                    $('#dateFrom').val(searchedDateFrom);
                                                }

                                                var searchedDateTo = '<?php echo $searchedDateTo; ?>';
                                                $('#sateTo').val(searchedDateTo);

                                                function pad (str, max) {
                                                    str = str.toString();
                                                    return str.length < max ? pad("0" + str, max) : str;
                                                }


                                                $("#projectId").change(function () {

                                                    $('#projectIde').hide();
                                                    var projectId = this.value;

                                                    if(projectId==""){
                                                        alert("empty");
                                                        $('#projectIde').html("Please Select Project");
                                                        return false;
                                                    }

                                                    var csrf = "<?php echo csrf_token(); ?>";
                                                    $.ajax({
                                                        type: 'post',
                                                        url: './getBranchNProjectTypeByProject',
                                                        data: {projectId: projectId , _token: csrf},
                                                        dataType: 'json',
                                                        success: function (data) {
                    // alert(JSON.stringify(data));

                    var branchList=data['branchList'];

                    // alert(branchList);

                    var projectTypeList=data['projectTypeList'];

                    // alert(projectTypeList);

                    $("#branchId option:gt(2)").remove();

                    // $("#branchId").empty();
                    // $("#branchId").prepend('<option selected="selected" value="">Select Branch</option>');
                    // $("#branchId").append('<option value="1">000 - Head Office</option>');

                    $.each(branchList, function( key,obj){
                        // $('#branchId').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                        $('#branchId').append("<option value='"+obj.id+"'>"+pad(obj.branchCode,3)+" - "+obj.name+"</option>");
                    });

                    $("#projectTypeId").empty();
                    //$("#projectTypeId").prepend('<option value="">All</option>');

                    $.each(projectTypeList, function( key,obj){
                        $('#projectTypeId').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                        // $('#projectTypeId').append("<option value='"+obj.id+"'>"+obj.projectTypeCode+" - "+obj.name+"</option>");
                    });
                    $("#branchId").trigger('change');
                },
                error: function(_response){
                    alert("Error");
                }
            });
                                                });

        // $("#projectId").trigger('change');

        $("#branchId").change(function () {
            var projectId = $("#projectId option:selected").val();
            var branchId = this.value;
            var csrf = "<?php echo csrf_token(); ?>";

            if(projectId==""){
                $('#projectIde').html("Please Select Project First");
                return false;
            }

            $.ajax({
                type: 'post',
                // url: './getProjectTypeInfo',
                url: './getProjectTypeNLedgersInfo',
                data: {projectId: projectId, branchId:branchId, _token: csrf},
                dataType: 'json',
                success: function (data) {
                    // alert(JSON.stringify(data));
                    // var ledgersOfBank=data['ledgersOfBank'];

                    // // alert(ledgersOfBank);

                    // $("#ledgerId").empty();
                    // $("#ledgerId").prepend('<option selected="selected" value="">All</option>');

                    // $.each(ledgersOfBank, function(key, obj){

                    //     // $('#ledgerId').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                    //     $('#ledgerId').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                    // });

                    // var searchedLedgerId = '<?php echo $searchedLedgerId; ?>';
                    // $('#ledgerId').val(searchedLedgerId);

                },
                error: function(_response){
                    alert("Something is Wrong");
                }
            });
        });

        function toDate(dateStr) {
            var parts = dateStr.split("-");
            return new Date(parts[2], parts[1] - 1, parts[0]);
        }

        //getting software date from controller in string format and converting into date format
        var maxDate = "{{$softwareDate}}";
        var dt = new Date(maxDate);
        // alert(dt);

        // triggering minimum date in date to
        $('.ui-datepicker-current-day').click();

        // $("#datepicker").datepicker("setDate", new Date(2017, 07 - 1, 01));
        // $('#datepicker').datepicker({dateFormat: 'yy-mm-dd'}).datepicker('setDate', 'softwareMinDate');


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

        // $("#datepicker").datepicker("setDate", new Date(toDate(softwareMinDate)));
        // $('#datepicker').datepicker({dateFormat: 'yy-mm-dd'}).datepicker('setDate', new Date(toDate(softwareMinDate)));

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
        $("#dateTo").datepicker( "option", "disabled", false );
    }


        //Getting software starting date according to branch

        $("#searchBranch").change(function(event) {
            /* Act on the event */
            var branchId = $(this).val();
            // alert(branchId);
            $.ajax({
                url: './getSoftwareDateByBranch',
                type: 'POST',
                dataType: 'json',
                data: {branchId: branchId},
            })
            .done(function(response) {
              minDate = response.softwareDate;
              // alert(minDate);
              $("#dateFrom").datepicker("option","minDate",new Date(toDate(minDate)));
          })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });


        });


    });         //document.ready


$(function(){
    $("#printIcon").click(function(){

            // $("#accBankBookReportTable").removeClass('table table-striped table-bordered');
            // $('#accLedgerReportTable_info').hide();
            // $('#accLedgerReportTable_paginate').hide();

            $("#accBankBookReportTable").removeClass('table table-striped table-bordered');

            var printStyle = '<style>#accBankBookReportTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt solid black;page-break-inside:auto;} #accBankBookReportTable tr:last-child { font-weight: bold;} #accBankBookReportTable thead tr th{ font-weight: bold;text-align:center;vertical-align: middle;padding:3px;font-size:11px;} tbody tr td { text-align:center;vertical-align: middle;padding:3px ;font-size:10px;} tr{ page-break-inside:avoid; page-break-after:auto } </style><style>@page {size: auto;margin: 25;}</style>';

            var mainContents = document.getElementById("printDiv").innerHTML;
            var printContents = '<div id="order-details-wrapper" style="padding: 20px;">' + printStyle + mainContents+'</div>';

            // var printContents = document.getElementById("printView").innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML ="" + printContents;
            window.print();
            document.body.innerHTML = originalContents;

            $("#accBankBookReportTable").addClass('table table-striped table-bordered');
            location.reload();

        });
});

</script>
