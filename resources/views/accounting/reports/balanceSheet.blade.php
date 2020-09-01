@extends('layouts/acc_layout')
@section('title', '| Balance Sheet')
@section('content')

<style type="text/css">
    #accBalanceSheetTable{
        font-family: arial !important;
    }
    #accBalanceSheetTable td{
        padding-left: 10px;
        padding-right: 10px;
    }
</style>

<?php

    function roundUpPHP($amount, $searchedRoundUp){
        if($searchedRoundUp==1){
            $roundUpAmount=round($amount);
            $roundUpAmount=number_format($roundUpAmount, 2, '.', ',');
        }elseif($searchedRoundUp==2){
            $roundUpAmount=number_format($amount, 2, '.', ',');
        }
        return $roundUpAmount;
    }

    function eachRow($child, $loopTrack, $voucherIdMatched, $searchedRoundUp, $searchedSearchMethod, $voucherIdMatchedByFIEndDay, $type, $totalCumSurplusOnOneBeforePreviousFisYear, $totalCumSurplusOnPreviousFisYear, $totalSurplusOfPreviousFI, $searchedFiscalYearId, $searchedBranchId, $startDate, $endDate, $previousFisIdOfCurrentFisYear, $projectIdArray, $projectTypeIdArray, $branchIdArray, $previousFisYearOpeningBalanceInfo, $currentFisYearMonthEndInfo) { ?>

    <tr class="item{{$child->id}}" level={{$loopTrack}} type={{$type}}>
        <td style="text-align: left;">

            @php
            if($child->isGroupHead==1) {
                echo '<span style="font-weight: bold">'.strtoupper($child->name)." [".$child->code."] ".'</span>';
            }
            else {
                echo '<span style="font-weight: normal">'.$child->name." [".$child->code."]".'</span>';
            }
            @endphp

        </td>

        @php
        $totalBalance=$debitAccAmount=$creditAccAmount=0;

        if ($searchedBranchId == NULL || $searchedBranchId == 0) {
            $debitAccAmount = $currentFisYearMonthEndInfo->where('ledgerId', $child->id)->sum('debitAmount');
            $creditAccAmount = $currentFisYearMonthEndInfo->where('ledgerId', $child->id)->sum('creditAmount');
        }
        else {
            $voucherInfos = DB::table('acc_voucher_details')
                            ->where('status',1)
                            ->whereIn('voucherId',$voucherIdMatched)
                            ->select('debitAcc', 'creditAcc', 'amount')
                            ->get();

            $debitAccAmount = $voucherInfos->where('debitAcc', $child->id)->sum('amount');
            $creditAccAmount = $voucherInfos->where('creditAcc', $child->id)->sum('amount');
        }

        if($child->accountTypeId >= 1 && $child->accountTypeId <= 5) {

            $totalBalance = $debitAccAmount - $creditAccAmount;

        }
        elseif($child->accountTypeId >= 6 && $child->accountTypeId <= 11) {

            $totalBalance = $creditAccAmount - $debitAccAmount;

        }

        // Fiscal Year
        $previousFisYearOpeningBalanceInfos = $previousFisYearOpeningBalanceInfo->where('fiscalYearId', $previousFisIdOfCurrentFisYear)->where('ledgerId', $child->id);
        $previousFisYearOpeningBalanceDebitAmount = $previousFisYearOpeningBalanceInfos->sum('debitAmount');
        $previousFisYearOpeningBalanceCreditAmount = $previousFisYearOpeningBalanceInfos->sum('creditAmount');


        if($child->accountTypeId >= 1 && $child->accountTypeId <= 5) {
            $previousFisYearOpeningBalanceTotalByFisYr=$previousFisYearOpeningBalanceDebitAmount-$previousFisYearOpeningBalanceCreditAmount;
        }
        elseif($child->accountTypeId >= 6 && $child->accountTypeId <= 11) {
            $previousFisYearOpeningBalanceTotalByFisYr=$previousFisYearOpeningBalanceCreditAmount-$previousFisYearOpeningBalanceDebitAmount;
        }

        $currentFisYearTotalBalanceByFisYr = $totalBalance + $previousFisYearOpeningBalanceTotalByFisYr;

        if($child->id == 512){
            $currentFisYearTotalBalanceByFisYr = $currentFisYearTotalBalanceByFisYr + $totalCumSurplusOnPreviousFisYear;
            $previousFisYearOpeningBalanceTotalByFisYr = $previousFisYearOpeningBalanceTotalByFisYr + $totalCumSurplusOnOneBeforePreviousFisYear;
        }
        @endphp

        <td class="preFiYearColumn" amount={{$previousFisYearOpeningBalanceTotalByFisYr}} style="text-align: right; font-weight: <?php if($child->isGroupHead==1){echo "bold";}elseif($child->isGroupHead==0){echo "normal";} ?>;" >
            {{roundUpPHP($previousFisYearOpeningBalanceTotalByFisYr, $searchedRoundUp)}}
        </td>
        <td class="curFiYearColumn" amount={{$currentFisYearTotalBalanceByFisYr}} style="text-align: right; font-weight: <?php if($child->isGroupHead==1){echo "bold";}elseif($child->isGroupHead==0){echo "normal";} ?>;" >
            {{roundUpPHP($currentFisYearTotalBalanceByFisYr, $searchedRoundUp)}}
        </td>

    </tr>

<?php } ?>


<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
        <div class="panel panel-default" style="background-color:#708090;">
            <div class="panel-heading" style="padding-bottom:0px">
                <div class="panel-options">
                    <button id="printIcon" class="btn btn-info pull-right"  target="_blank" style="font-size: 14px; margin-top: 10px; border: 3px solid #525659; border-radius: 25px;">
                        <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
                    </button>
                </div>
                <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Statement of Financial Position</h3>
            </div>

            <div class="panel-body panelBodyView">

            <!-- Filtering Start-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">

                            {!! Form::open(array('url' => 'balanceSheet/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'ladgerReportForm', 'method'=>'get')) !!}

                            @if($user_branch_id==1)
                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:black; ">
                                    {!! Form::label('', 'Project:', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                        <select class="form-control" name="projectId" id="projectId" required>
                                            {{-- <option value="">Select Project</option> --}}
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
                                <div class="form-group" style="font-size: 13px; color:black; ">
                                    {!! Form::label('', 'Branch:', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                        <select class="form-control" name="branchId" id="branchId">
                                            <option value="">All</option>
                                            <option value="0" @if($branchSelected===0){{"selected=selected"}}@endif >All Branch Office</option>
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
                                <div class="form-group" style="font-size: 13px; color:black; ">
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
                                <div class="form-group" style="font-size: 13px; color:black;">
                                     <div style="text-align: center;" class="col-sm-12">
                                        {!! Form::label('', 'Depth Level:', ['class' => 'control-label pull-left']) !!}
                                    </div>
                                    <div class="col-sm-12">
                                        {!! Form::select('depthLevel',['5'=>'All',1=>'Level-1',2=>'Level-2',3=>'Level-3',4=>'Level-4'],null ,['id'=>'depthLevel','class'=>'form-control']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:black;">
                                    <div style="text-align: center;" class="col-sm-12">
                                        {!! Form::label('', 'Round Up:', ['class' => 'control-label pull-left']) !!}
                                    </div>
                                    <div class="col-sm-12">
                                        {!! Form::select('roundUp',['1'=>'Yes','2'=>'No'], null,['id'=>'roundUp','class'=>'form-control input-sm']) !!}

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:black;">
                                    <div style="text-align: center;" class="col-sm-12">
                                        {!! Form::label('', '\'0\' Balance:', ['class' => 'control-label pull-left']) !!}
                                    </div>
                                    <div class="col-sm-12">
                                        {!! Form::select('withZero',['2'=>'Yes','1'=>'No'], null,['id'=>'withZero','class'=>'form-control input-sm']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:black;">
                                    <div style="text-align: center;" class="col-sm-12">
                                        {!! Form::label('', 'Search By:', ['class' => 'control-label pull-left']) !!}
                                    </div>
                                    <div class="col-sm-12">
                                        {!! Form::select('searchMethod',['1'=>'Fiscal Year','2'=>'Current Year'], null,['id'=>'searchMethod','class'=>'form-control input-sm']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-1" style="display: none;" id="fiscalYearDiv">
                                <div class="form-group" style="font-size: 13px; color:black">
                                    {!! Form::label('', ' ', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12" style="padding-top: 18px;">
                                        {!! Form::select('fiscalYearId', $fiscalYears, null, array('class'=>'form-control input-sm', 'id' => 'fiscalYearId')) !!}
                                    </div>
                                </div>
                            </div>

                            <?php
                                // $toDate= date("d-m-Y");
                                // $toDate = DB::table('acc_day_end')->where('branchIdFk', $user_branch_id)->where('isDayEnd', '=', '0')->value('date');
                                // // dd($toDate);
                                // $currentFiYearStartDate=DB::table('gnr_fiscal_year')->where('fyStartDate','<=', $toDate)->where('fyEndDate','>=', $toDate)->value('fyStartDate');
                                // $currentFiYearEndDate=DB::table('gnr_fiscal_year')->where('fyStartDate','<=', $toDate)->where('fyEndDate','>=', $toDate)->value('fyEndDate');
                                // // dd($currentFiYearStartDate);
                                // if ($searchedSearchMethod == 1) {
                                //     $toDateFrom = $currentFiYearStartDate;
                                //     $toDateTo = $currentFiYearEndDate;
                                // }
                                // elseif ($searchedSearchMethod == 2) {
                                //     $toDateTo = $searchedDateTo;
                                //     $toDateFrom = DB::table('gnr_fiscal_year')->where('fyStartDate','<=', $toDateTo)->where('fyEndDate','>=', $toDateTo)->value('fyStartDate');
                                // }else {
                                //     $toDateTo=$toDate;
                                //     $toDateFrom=$toDate;
                                // }
                                // if ($searchedDateFrom=="") { $toDateFrom=$toDate; }else{ $toDateFrom=$searchedDateFrom; }
                                // if ($searchedDateTo=="") { $toDateTo=$toDate; }else{ $toDateTo=$searchedDateTo; }

                                // dd($toDateFrom);
                            ?>

                            <div class="col-md-1" style="display: none;" id="dateRangeDiv">
                                <div class="form-group" style="font-size: 13px; color:black">
                                    <div style="text-align: center; padding-top: 10px;" class="col-sm-12">
                                        {!! Form::label('', ' ', ['class' => 'control-label']) !!}
                                    </div>

                                    {{-- <div class="col-sm-6" style="padding-top: 7px;">
                                        <div class="form-group">

                                            <div class="col-sm-12">
                                                {!! Form::text('dateFrom', $currentFiYearStartDate, ['id'=>'dateFrom','placeholder'=>'From','class'=>'form-control input-sm','readonly','style'=>'cursor:pointer']) !!}
                                                <p id="dateFrome" style="color: red;display: none;">*Required</p>
                                            </div>
                                        </div>

                                    </div> --}}
                                    <div class="col-sm-12" style="padding-top: 7px;">
                                        <div class="form-group">

                                            <div class="col-sm-12" id="dateToDiv">
                                                {!! Form::text('dateTo', $toDateTo,['id'=>'dateTo','placeholder'=>'To','class'=>'form-control input-sm','readonly','style'=>'cursor:pointer']) !!}
                                                <p id="dateToe" style="color: red;display: none;">*Required</p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            @php
                              // dd($toDateFrom);
                            @endphp
                            {{-- <div class="col-md-1"></div> --}}

                            <div class="col-md-1">
                                <div class="form-group" style="">
                                    {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12" style="padding-top: 13%;">
                                        {!! Form::submit('search', ['id' => 'balanceStatementSearch', 'class' => 'btn btn-primary btn-s', 'style'=>'font-size:12px']); !!}
                                    </div>
                                    <!-- <div class="col-sm-6" style="visibility: hidden">
                                        {!! Form::text('dateFrom', date('d-m-Y',strtotime($toDateFrom)),['id'=>'dateFrom','placeholder'=>'From','class'=>'form-control input-sm','readonly','style'=>'cursor:pointer']) !!}
                                        <p id="dateFrome" style="color: red;display: none;">*Required</p>
                                    </div> -->
                                </div>
                            </div>
                            <div class="col-md-1"></div>

                            {!! Form::close()  !!}

                            {{-- end Div of ledgerSearch --}}

                            @if($user_branch_id!=1)<div class="col-md-3"></div> @endif
                        </div>
                    </div>

                </div>
                <!-- filtering end-->


                @php

                if ($searchedRoundUp!=null || $searchedRoundUp!="") {

                $projectIdArray = array();
                $projectTypeIdArray = array();
                $branchIdArray = array();

                //Project
                if ($searchedProjectId==null) {
                    if ($user_branch_id == 1) {
                        $projectIdArray = DB::table('gnr_project')->pluck('id')->toArray();
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
                    if ($user_branch_id == 1) {
                        $projectTypeIdArray = DB::table('gnr_project_type')->pluck('id')->toArray();
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
                    if ($user_branch_id == 1) {
                        $branchIdArray = DB::table('gnr_branch')->whereIn('projectId', $projectIdArray)->orWhere('id', 1)->pluck('id')->toArray();
                    }
                    else{
                        array_push($branchIdArray, (int) json_decode($user_branch_id));
                        $searchedBranchId=(int) json_decode($user_branch_id);
                    }
                }
                elseif ($searchedBranchId==0) {
                    $branchIdArray = DB::table('gnr_branch')->whereIn('projectId', $projectIdArray)->where('id', '!=', 1)->pluck('id')->toArray();
                }
                else{
                    array_push($branchIdArray, (int) json_decode($searchedBranchId));
                }
                // dd($branchIdArray);

                $previousFisYearOpeningBalanceIds=0;
                $preOfPreviousFisYearOpeningBalanceIds=0;
                $voucherIdMatchedByFIEndDay=0;
                $monthEndsIdArrByFIEndDay=0;
                //===========Get Start Date and End Date===========

                if ($searchedSearchMethod==1) {        //========Fiscal Year

                    $startDate = $toDateFrom;
                    $endDate = $toDateTo;
                    // $startDate = date('Y-m-d', strtotime(DB::table('gnr_fiscal_year')->where('id',$searchedFiscalYearId)->value('fyStartDate')));
                    // $endDate = date('Y-m-d', strtotime(DB::table('gnr_fiscal_year')->where('id',$searchedFiscalYearId)->value('fyEndDate')));
                    $previousFisStartDate=date('Y-m-d', strtotime("first day of last year".$startDate));
                    $previousFisEndDate=date('Y-m-d', strtotime("last day of -1 year".$endDate));
                    $previousDayOfPreviousYearStartDate = \Carbon\Carbon::parse($previousFisStartDate)->subDay()->format('Y-m-d');

                    $currentFisYearId=DB::table('gnr_fiscal_year')->where('fyStartDate',$startDate)->where('fyEndDate',$endDate)->value('id');
                    $previousFisIdOfCurrentFisYear=DB::table('gnr_fiscal_year')->where('fyStartDate',$previousFisStartDate)->where('fyEndDate',$previousFisEndDate)->value('id');
                    $previousFisIdOfPreviousFisYear=DB::table('gnr_fiscal_year')->where('fyEndDate',$previousDayOfPreviousYearStartDate)->value('id');
                    // dd($previousFisIdOfPreviousFisYear);

                    $previousFisYearOpeningBalanceInfo = DB::table('acc_opening_balance')
                                                        ->whereIn('projectId',$projectIdArray)
                                                        ->whereIn('projectTypeId',$projectTypeIdArray)
                                                        ->whereIn('branchId',$branchIdArray)
                                                        ->whereIn('fiscalYearId', [$previousFisIdOfCurrentFisYear, $previousFisIdOfPreviousFisYear])
                                                        ->select('fiscalYearId', 'ledgerId', 'balanceAmount', 'debitAmount', 'creditAmount')
                                                        ->get();

                    $monthEndsInfo = DB::table('acc_month_end_balance')
                                    ->whereIn('projectId',$projectIdArray)
                                    ->whereIn('projectTypeId',$projectTypeIdArray)
                                    ->whereIn('branchId',$branchIdArray)
                                    ->select('monthEndDate', 'fiscalYearId', 'ledgerId', 'balanceAmount', 'debitAmount', 'creditAmount')
                                    ->get();

                    $previousFisYearMonthEndInfo = $monthEndsInfo->where('fiscalYearId', $previousFisIdOfCurrentFisYear);
                    $currentFisYearMonthEndInfo = $monthEndsInfo->where('fiscalYearId', $currentFisYearId);
                    // dd($previousFisYearMonthEndInfo);

                    $currentFiYearName=DB::table('gnr_fiscal_year')->where('id',$searchedFiscalYearId)->value('name');
                    $previousFiYearName=DB::table('gnr_fiscal_year')->where('id',$previousFisIdOfCurrentFisYear)->value('name');
                    // dd($previousFiYearName);

                    if ($branchSelected == NULL || $branchSelected == 0) {
                        $voucherIdMatched = [];
                    }
                    else {
                        $voucherIdMatched = DB::table('acc_voucher')
                                ->where('status',1)
                                ->whereIn('projectId',$projectIdArray)
                                ->whereIn('projectTypeId',$projectTypeIdArray)
                                ->whereIn('branchId',$branchIdArray)
                                ->where(function ($query) use ($startDate,$endDate){
                                      $query->where('voucherDate','>=',$startDate)
                                      ->where('voucherDate','<=',$endDate);
                                    })->pluck('id')->toArray();
                    }

                }
                elseif ($searchedSearchMethod==2) {           //========date range

                    $startDate = $toDateFrom;
                    $endDate = $toDateTo;

                    $currentFiYearName = DB::table('gnr_fiscal_year')->where('fyStartDate','<=', $startDate)->where('fyEndDate','>=', $startDate)->value('name');
                    $previousFiYearStartDate = date('Y-m-d',strtotime($startDate. ' -1 year'));

                    $previousFiYearName = DB::table('gnr_fiscal_year')->where('fyStartDate','<=', $previousFiYearStartDate)->where('fyEndDate','>=', $previousFiYearStartDate)->value('name');
                    $currentFiscalYear = DB::table('gnr_fiscal_year')->where('fyStartDate','<=', $startDate)->where('fyEndDate','>=', $startDate)->select('id','fyStartDate','fyEndDate')->first();
                    $currentEndDay=$currentFiscalYear->fyEndDate;

                    $previousFisStartDate = date('Y-m-d', strtotime("first day of last year".$currentFiscalYear->fyStartDate));
                    $previousFisEndDate = date('Y-m-d', strtotime("last day of -1 year".$currentFiscalYear->fyEndDate));
                    $previousDayOfPreviousYearStartDate = \Carbon\Carbon::parse($previousFisStartDate)->subDay()->format('Y-m-d');
                    // dd($startDate);

                    $previousFisIdOfCurrentFisYear = DB::table('gnr_fiscal_year')->where('fyStartDate', $previousFisStartDate)->where('fyEndDate', $previousFisEndDate)->value('id');
                    $previousFisIdOfPreviousFisYear = DB::table('gnr_fiscal_year')->where('fyEndDate',$previousDayOfPreviousYearStartDate)->value('id');
                    // dd($currentFisYearId);

                    $previousFisYearOpeningBalanceInfo = DB::table('acc_opening_balance')
                                                        ->whereIn('projectId',$projectIdArray)
                                                        ->whereIn('projectTypeId',$projectTypeIdArray)
                                                        ->whereIn('branchId',$branchIdArray)
                                                        ->whereIn('fiscalYearId', [$previousFisIdOfCurrentFisYear, $previousFisIdOfPreviousFisYear])
                                                        ->select('fiscalYearId', 'ledgerId', 'balanceAmount', 'debitAmount', 'creditAmount')
                                                        ->get();

                    $monthEndsInfo = DB::table('acc_month_end_balance')
                                    ->whereIn('projectId',$projectIdArray)
                                    ->whereIn('projectTypeId',$projectTypeIdArray)
                                    ->whereIn('branchId',$branchIdArray)
                                    ->select('monthEndDate', 'fiscalYearId', 'ledgerId', 'balanceAmount', 'debitAmount', 'creditAmount')
                                    ->get();

                    $previousFisYearMonthEndInfo = $monthEndsInfo->where('fiscalYearId', $previousFisIdOfCurrentFisYear);
                    $currentFisYearMonthEndInfo = $monthEndsInfo->where('fiscalYearId', $currentFiscalYear->id)->where('monthEndDate', '<=', $endDate);
                    // dd($currentFisYearMonthEndInfo);

                    if ($branchSelected == NULL || $branchSelected == 0) {
                        $voucherIdMatchedByFIEndDay = [];
                    }
                    else {
                        $voucherIdMatchedByFIEndDay = DB::table('acc_voucher')
                            ->where('status',1)
                            ->whereIn('projectId',$projectIdArray)
                            ->whereIn('projectTypeId',$projectTypeIdArray)
                            ->whereIn('branchId',$branchIdArray)
                            ->where(function ($query) use ($startDate,$endDate){
                                  $query->where('voucherDate','>=',$startDate)
                                  ->where('voucherDate','<',$endDate);
                            })->pluck('id')->toArray();
                    }

                }
    //===============================================Starts Voucher Filtering=========================================================

                if ($branchSelected == NULL || $branchSelected == 0) {
                    $voucherIdMatched = [];
                }
                else {
                    $voucherIdMatched = DB::table('acc_voucher')
                                        ->where('status',1)
                                        ->whereIn('projectId',$projectIdArray)
                                        ->whereIn('projectTypeId',$projectTypeIdArray)
                                        ->whereIn('branchId',$branchIdArray)
                                        ->where(function ($query) use ($startDate,$endDate){
                                            $query->where('voucherDate','>=',$startDate)
                                            ->where('voucherDate','<=',$endDate);
                                        })->pluck('id')->toArray();
                }

    //===============================End Voucher Filtering=========================================

    //===============================Starts Ledger Filtering=========================================

                $allLedgers = DB::table('acc_account_ledger')->select("id","projectBranchId")->get();

                $ledgerMatchedId=array();

                foreach ($allLedgers as $singleLedger) {
                    $splitArray=str_replace(array('[', ']', '"', ''), '',  $singleLedger->projectBranchId);

                    $splitArrayFirstValue = explode(",", $splitArray);
                    $splitArraySecondValue = explode(":", $splitArrayFirstValue[0]);

                    $array_length=substr_count($splitArray, ",");
                    $arrayProjects=array();
                    $temp=null;
                    // $temp1=null;
                    for($i=0; $i<$array_length+1; $i++){

                        $splitArrayFirstValue = explode(",", $splitArray);

                        $splitArraySecondValue = explode(":", $splitArrayFirstValue[$i]);
                        $firstIndexValue=(int)$splitArraySecondValue[0];
                        $secondIndexValue=(int)$splitArraySecondValue[1];

                        if($firstIndexValue==0){
                            if($secondIndexValue==0){
                                array_push($ledgerMatchedId, $singleLedger->id);
                            }
                        }else{
                            // if($firstIndexValue!=$temp){
                                if($firstIndexValue==$searchedProjectId){
                                    if($secondIndexValue==0){
                                        array_push($ledgerMatchedId, $singleLedger->id);
                                    }else{
                                        foreach ($branchIdArray as $checkBranchId) {
                                            if($checkBranchId==$secondIndexValue){
                                                array_push($ledgerMatchedId, $singleLedger->id);
                                            }
                                        }
                                    }
                                }
                            // }
                            // $temp=$firstIndexValue;
                        }
                    }   //for
                }       //foreach

    //===============================================End Ledger Filtering=========================================================

    //===============================================Starts Calculate Surplus===============================================

                if ($branchSelected == NULL || $branchSelected == 0) {
                    // do nothing
                }
                else {
                    $monthEndsIdArr = [];

                    if($searchedSearchMethod == 1){

                        $vouchersInfo = DB::table('acc_voucher_details')
                                        ->where('status',1)
                                        ->whereIn('voucherId',$voucherIdMatched)
                                        ->select('debitAcc', 'creditAcc', 'amount')
                                        ->get();

                    }
                    elseif ($searchedSearchMethod == 2) {

                        $vouchersInfo = DB::table('acc_voucher_details')
                                            ->where('status',1)
                                            ->whereIn('voucherId',$voucherIdMatchedByFIEndDay)
                                            ->select('debitAcc', 'creditAcc', 'amount')
                                            ->get();

                    }
                }
                // dd($currentFisYearMonthEndInfo);

                $incomeLedgerIds = DB::table('acc_account_ledger')->whereIn('id', $ledgerMatchedId)->where('isGroupHead', 0)->where('accountTypeId', 12)->pluck('id')->toArray();
                $expenseLedgerIds = DB::table('acc_account_ledger')->whereIn('id', $ledgerMatchedId)->where('isGroupHead', 0)->where('accountTypeId', 13)->pluck('id')->toArray();
                // dd($expenseLedgerIds);

                $incomeBalanceOfPreviousFisYear = 0;
                $expenceBalanceOfPreviousFisYear = 0;
                $cumIncomeOfPreviousFisYear = 0;
                $cumExpenseOfPreviousFisYear = 0;
                $cumIncomeOfOneBeforePreviousFisYear = 0;
                $cumExpenseOfOneBeforePreviousFisYear = 0;
                $incomeBalance = 0;
                $incomeDebitAmount = 0;
                $incomeCreditAmount = 0;
                $expenseBalance = 0;
                $expenseDebitAmount = 0;
                $expenseCreditAmount = 0;
                $totalSurplusOFCurrentFI = 0;

                // income loop
                //previous year
                $incomeBalanceOfPreviousFisYear += $previousFisYearMonthEndInfo->whereIn('ledgerId', $incomeLedgerIds)->sum('balanceAmount');
                // previous cumulative
                $cumIncomeOfPreviousFisYear += $previousFisYearOpeningBalanceInfo->where('fiscalYearId', $previousFisIdOfCurrentFisYear)->whereIn('ledgerId', $incomeLedgerIds)->sum('balanceAmount');
                $cumIncomeOfOneBeforePreviousFisYear += $previousFisYearOpeningBalanceInfo->where('fiscalYearId', $previousFisIdOfPreviousFisYear)->whereIn('ledgerId', $incomeLedgerIds)->sum('balanceAmount');

                if ($branchSelected == NULL || $branchSelected == 0) {
                    //income
                    $incomeDebitAmount += $currentFisYearMonthEndInfo->whereIn('ledgerId', $incomeLedgerIds)->sum('debitAmount');
                    $incomeCreditAmount += $currentFisYearMonthEndInfo->whereIn('ledgerId', $incomeLedgerIds)->sum('creditAmount');
                }
                else {
                    //income
                    $incomeDebitAmount += $vouchersInfo->whereIn('debitAcc', $incomeLedgerIds)->sum('amount');
                    $incomeCreditAmount += $vouchersInfo->whereIn('creditAcc', $incomeLedgerIds)->sum('amount');
                }

                // expenses loop
                // previous year
                $expenceBalanceOfPreviousFisYear += $previousFisYearMonthEndInfo->whereIn('ledgerId', $expenseLedgerIds)->sum('balanceAmount');
                // previous cumulative
                $cumExpenseOfPreviousFisYear += $previousFisYearOpeningBalanceInfo->where('fiscalYearId', $previousFisIdOfCurrentFisYear)->whereIn('ledgerId', $expenseLedgerIds)->sum('balanceAmount');
                $cumExpenseOfOneBeforePreviousFisYear += $previousFisYearOpeningBalanceInfo->where('fiscalYearId', $previousFisIdOfPreviousFisYear)->whereIn('ledgerId', $expenseLedgerIds)->sum('balanceAmount');

                if ($branchSelected == NULL || $branchSelected == 0) {
                    //expense
                    $expenseDebitAmount += $currentFisYearMonthEndInfo->whereIn('ledgerId', $expenseLedgerIds)->sum('debitAmount');
                    $expenseCreditAmount += $currentFisYearMonthEndInfo->whereIn('ledgerId', $expenseLedgerIds)->sum('creditAmount');
                }
                else {
                    //expense
                    $expenseDebitAmount += $vouchersInfo->whereIn('debitAcc', $expenseLedgerIds)->sum('amount');
                    $expenseCreditAmount += $vouchersInfo->whereIn('creditAcc', $expenseLedgerIds)->sum('amount');
                }

                $incomeBalance = $incomeCreditAmount - $incomeDebitAmount;
                $expenseBalance = $expenseDebitAmount - $expenseCreditAmount;

                $totalCumSurplusOnPreviousFisYear = abs($cumIncomeOfPreviousFisYear) - abs($cumExpenseOfPreviousFisYear);
                $totalCumSurplusOnOneBeforePreviousFisYear = abs($cumIncomeOfOneBeforePreviousFisYear) - abs($cumExpenseOfOneBeforePreviousFisYear);
                $totalSurplusOfPreviousFI = abs($incomeBalanceOfPreviousFisYear) - abs($expenceBalanceOfPreviousFisYear);
                $totalSurplusOFCurrentFI = $incomeBalance - $expenseBalance;
                // dd($totalSurplusOFCurrentFI);
                // if($searchedSearchMethod==2){
                //
                //     $incomeBalanceByCurrFIEndDay=$incomeDebitAmountByCurrFIEndDay=$incomeCreditAmountByCurrFIEndDay=$expenseBalanceByCurrFIEndDay=$expenseDebitAmountByCurrFIEndDay=$expenseCreditAmountByCurrFIEndDay=$totalSurplusOFCurrentFIByCurrFIEndDay=0;
                //
                //     // income loop
                //     foreach ($incomeLedgerIds as $key => $id) {
                //
                //         // $incomeBalanceOfPreviousFisYear += $previousFisYearOpeningBalanceInfo->where('ledgerId', $id)->sum('balanceAmount');
                //
                //         if ($branchSelected == NULL || $branchSelected == 0) {
                //
                //             //income
                //             $incomeDebitAmountByCurrFIEndDay += $monthEndsInfo->where('ledgerId', $id)->sum('debitAmount');
                //             $incomeCreditAmountByCurrFIEndDay += $monthEndsInfo->where('ledgerId', $id)->sum('creditAmount');
                //         }
                //         else {
                //             //income
                //             $incomeDebitAmountByCurrFIEndDay += $vouchersInfo->where('debitAcc', $id)->sum('amount');
                //             $incomeCreditAmountByCurrFIEndDay += $vouchersInfo->where('creditAcc', $id)->sum('amount');
                //         }
                //     }
                //
                //     // expenses loop
                //     foreach ($expenseLedgerIds as $key => $id) {
                //
                //         if ($branchSelected == NULL || $branchSelected == 0) {
                //             //expense
                //             $expenseDebitAmountByCurrFIEndDay += $monthEndsInfo->where('ledgerId', $id)->sum('debitAmount');
                //             $expenseCreditAmountByCurrFIEndDay += $monthEndsInfo->where('ledgerId', $id)->sum('creditAmount');
                //         }
                //         else {
                //             //expense
                //             $expenseDebitAmountByCurrFIEndDay += $vouchersInfo->where('debitAcc', $id)->sum('amount');
                //             $expenseCreditAmountByCurrFIEndDay += $vouchersInfo->where('creditAcc', $id)->sum('amount');
                //         }
                //     }
                //
                //     $incomeBalanceByCurrFIEndDay=$incomeDebitAmountByCurrFIEndDay+$incomeCreditAmountByCurrFIEndDay;
                //     $expenseBalanceByCurrFIEndDay=$expenseDebitAmountByCurrFIEndDay+$expenseCreditAmountByCurrFIEndDay;
                //     $totalSurplusOFCurrentFIByCurrFIEndDay=$incomeBalanceByCurrFIEndDay-$expenseBalanceByCurrFIEndDay;
                //     $cumulativeAmountByCurrYr=$totalSurplusOfPreviousFI+$totalSurplusOFCurrentFIByCurrFIEndDay;
                // }

    //===============================================Ends Calculate Surplus===============================================

                @endphp


                <div id="printDiv">
                    <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; ">
                        <?php
                            $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
                        ?>
                        <span style="font-size:14px;">{{$company->name}}</span><br/>
                        <span style="font-size:11px;">{{$company->address}}</span><br/>
                        <span style="text-decoration: underline;  font-size:14px;">Statement of Financial Position</span><br/>
                        <span style="text-decoration: underline;">As at {{date('jS F, Y',strtotime($endDate))}}</span>
                    </div>

                    <div class="row">

                        <div class="col-md-12"  style="font-size: 12px;" >
                            <?php

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
                                        <span style="font-weight: bold;">Project Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                                        <span>{{$project->name}}</span>
                                    </span>
                                    <span style="color: black; float: right;">
                                        <span style="font-weight: bold;">Branch Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                                        <span>{{$branch}}</span>
                                    </span>

                            </span>
                            <br>
                            <span>
                                <span style="color: black; float: left;">
                                        <span style="font-weight: bold;">Project Type: <?php echo str_repeat('&nbsp;', 5);?></span>
                                        <span>{{$projectType}}</span>
                                </span>

                                <span style="color: black; float: right;">
                                        <span style="font-weight: bold;">Print Date: <?php echo str_repeat('&nbsp;', 3);?></span>
                                        <span>{{\Carbon\Carbon::now()->format('d-m-Y g:i A')}}</span>
                                </span>
                            </span>

                        </div>

                    </div>


                    <div class="row" style=" margin: 15px 0px;">
                        <div class="table-responsive">

                            <table class="table table-striped table-bordered" id="accBalanceSheetTable" border="1pt solid ash" style="border-collapse: collapse;" >
                                <thead>
                                    <tr>
                                        <th style="padding: 0px;">Particular</th>
                                        <th style="padding: 0px; width: 160px;">
                                            Previous Year
                                            <br/> {{"(".$previousFiYearName.")"}}
                                        </th>
                                        <th style="padding: 0px; width: 160px;">
                                            Current Year
                                            <br/> {{"(".$currentFiYearName.")"}}
                                        </th>
                                    </tr>
                                    {{ csrf_field() }}
                                </thead>
                                <tbody>


                                <?php $loopTrack=0; ?>
                                @foreach($ledgers as $key => $ledger)
                                @if(in_array($ledger->id,$ledgerMatchedId))
                                    <?php
                                    $loopTrack=0;
                                    // $type=0;
                                    if ($key==0) {$type="asset";}elseif ($key==1) {$type="liabilities";}elseif ($key==2) {$type="capital";}
                                    // eachRow($ledger, $loopTrack, $voucherIdMatched, $searchedRoundUp, $searchedSearchMethod, $previousFisYearOpeningBalanceIds, $voucherIdMatchedByFIEndDay, $type);
                                    ?>
                                        <tr>
                                            <td level="0" style="font-weight: bold; text-align:left;" colspan="3">
                                            {{-- @if ($key==2) {{str_repeat('&nbsp;', 4)}} @endif --}}
                                            {{strtoupper($ledger->name)." [".$ledger->code."]"}}</td>
                                        </tr>

                                    <?php
                                    $ledgersInfo = DB::table('acc_account_ledger')->orderBy('ordering', 'asc')->select('id', 'parentId', 'code', 'name', 'isGroupHead', 'accountTypeId')->get();
                                    // dd($ledgersInfo);

                                    if($ledger->isGroupHead==1){
                                    $children1= $ledgersInfo->where('parentId', $ledger->id);
                                    ?>
                                    @foreach($children1 as $child1)
                                    @if(in_array($child1->id,$ledgerMatchedId))
                                        <?php
                                        $loopTrack=1;
                                        eachRow($child1, $loopTrack, $voucherIdMatched, $searchedRoundUp, $searchedSearchMethod, $voucherIdMatchedByFIEndDay, $type , $totalCumSurplusOnOneBeforePreviousFisYear, $totalCumSurplusOnPreviousFisYear, $totalSurplusOfPreviousFI, $searchedFiscalYearId, $searchedBranchId, $startDate, $endDate, $previousFisIdOfCurrentFisYear, $projectIdArray, $projectTypeIdArray, $branchIdArray, $previousFisYearOpeningBalanceInfo, $currentFisYearMonthEndInfo);

                                        if($child1->isGroupHead==1){
                                        $children2= $ledgersInfo->where('parentId', $child1->id);

                                        ?>
                                        @foreach($children2 as $child2)
                                        @if(in_array($child2->id,$ledgerMatchedId))
                                            <?php
                                            $loopTrack=2;
                                            eachRow($child2, $loopTrack, $voucherIdMatched, $searchedRoundUp, $searchedSearchMethod, $voucherIdMatchedByFIEndDay, $type, $totalCumSurplusOnOneBeforePreviousFisYear, $totalCumSurplusOnPreviousFisYear, $totalSurplusOfPreviousFI, $searchedFiscalYearId, $searchedBranchId, $startDate, $endDate, $previousFisIdOfCurrentFisYear, $projectIdArray, $projectTypeIdArray, $branchIdArray, $previousFisYearOpeningBalanceInfo, $currentFisYearMonthEndInfo);

                                            if($child2->isGroupHead==1){
                                            $children3= $ledgersInfo->where('parentId', $child2->id);

                                            ?>
                                            @foreach($children3 as $child3)
                                            @if(in_array($child3->id,$ledgerMatchedId))
                                                <?php
                                                $loopTrack=3;
                                                eachRow($child3, $loopTrack, $voucherIdMatched, $searchedRoundUp, $searchedSearchMethod, $voucherIdMatchedByFIEndDay, $type, $totalCumSurplusOnOneBeforePreviousFisYear, $totalCumSurplusOnPreviousFisYear, $totalSurplusOfPreviousFI, $searchedFiscalYearId, $searchedBranchId, $startDate, $endDate, $previousFisIdOfCurrentFisYear, $projectIdArray, $projectTypeIdArray, $branchIdArray, $previousFisYearOpeningBalanceInfo, $currentFisYearMonthEndInfo);

                                                if($child3->isGroupHead==1){
                                                $children4= $ledgersInfo->where('parentId', $child3->id);

                                                ?>
                                                @foreach($children4 as $child4)
                                                @if(in_array($child4->id,$ledgerMatchedId))
                                                    <?php
                                                    $loopTrack=4;
                                                    eachRow($child4, $loopTrack, $voucherIdMatched, $searchedRoundUp, $searchedSearchMethod, $voucherIdMatchedByFIEndDay, $type, $totalCumSurplusOnOneBeforePreviousFisYear, $totalCumSurplusOnPreviousFisYear, $totalSurplusOfPreviousFI, $searchedFiscalYearId, $searchedBranchId, $startDate, $endDate, $previousFisIdOfCurrentFisYear, $projectIdArray, $projectTypeIdArray, $branchIdArray, $previousFisYearOpeningBalanceInfo, $currentFisYearMonthEndInfo);

                                                    if($child4->isGroupHead==1){
                                                    $children5= $ledgersInfo->where('parentId', $child4->id);

                                                    ?>
                                                    @foreach($children5 as $child5)
                                                    @if(in_array($child5->id,$ledgerMatchedId))
                                                        <?php
                                                        $loopTrack=5;
                                                        eachRow($child5, $loopTrack, $voucherIdMatched, $searchedRoundUp, $searchedSearchMethod, $voucherIdMatchedByFIEndDay, $type, $totalCumSurplusOnOneBeforePreviousFisYear, $totalCumSurplusOnPreviousFisYear, $totalSurplusOfPreviousFI, $searchedFiscalYearId, $searchedBranchId, $startDate, $endDate, $previousFisIdOfCurrentFisYear, $projectIdArray, $projectTypeIdArray, $branchIdArray, $previousFisYearOpeningBalanceInfo, $currentFisYearMonthEndInfo);
                                                        ?>
                                                    @endif
                                                    @endforeach <?php }?>          {{-- End foreach loop for Child5 --}}
                                                @endif
                                                @endforeach <?php }?>           {{-- End foreach loop for Child4 --}}
                                            @endif
                                            @endforeach <?php }?>          {{-- End foreach loop for Child3 --}}
                                        @endif
                                        @endforeach <?php }?>         {{-- End foreach loop for Child2 --}}
                                    @endif
                                    @endforeach <?php }?>       {{-- End foreach loop for Child1 --}}

                                    <?php


                                    ?>
{{-- ===================================================Surplus Row For Fiscal Year================================================== --}}
                                    {{-- Fiscal Year --}}

                                        <?php if($key==2){?>
                                            <tr level="1" >
                                                <td style="text-align:left;">  Surplus/Defict from Income Statement</td>
                                                {{-- <td class="preFiYearTotalBalance" amount="" style="text-align:right;  padding-right: 20px;">
                                                </td>  --}}{{-- extra td --}}
                                                <td class="preFiYearColumn" amount="" style="text-align:right;  "></td>
                                                <td class="curFiYearColumn" amount="" style="text-align:right;"></td>
                                            </tr>
                                            <tr level="2" style="font-weight: bold; display:none;" >
                                                <td style="text-align:left;"></td>
                                                {{-- <td class="preFiYearTotalBalance" amount="" style="text-align:right;  padding-right: 20px;">
                                                </td>  --}}{{-- extra td --}}
                                                <td class="preFiYearColumn" amount="" ></td>
                                                <td class="curFiYearColumn" amount="" ></td>
                                            </tr>
                                            <tr level="3" style="font-weight: bold; display:none;" >
                                                <td style="text-align:left;"></td>
                                                {{-- <td class="preFiYearTotalBalance" amount="" style="text-align:right;  padding-right: 20px;">
                                                </td> --}} {{-- extra td --}}
                                                <td class="preFiYearColumn" amount="" ></td>
                                                <td class="curFiYearColumn" amount="" ></td>
                                            </tr>
                                            <tr level="4" style="font-weight: bold; display:none;" >
                                                <td style="text-align:left;"></td>
                                                {{-- <td class="preFiYearTotalBalance" amount="" style="text-align:right;  padding-right: 20px;">

                                                </td> --}} {{-- extra td --}}
                                                <td class="preFiYearColumn" amount="{{$totalSurplusOfPreviousFI}}" >{{$totalSurplusOfPreviousFI}}</td>
                                                <td class="curFiYearColumn" amount="{{$totalSurplusOFCurrentFI}}" >{{$totalSurplusOFCurrentFI}}</td>


                                            </tr>

                                        <?php }?>

                                        <tr level="0" id="<?php if($key==0){echo "totalIncome";}elseif($key==1){echo "totalExpense";}elseif($key==2){echo "totalEquity";} ?>" style="font-weight: bold; ">
                                            <td style="text-align:left;">
                                                <?php if($key==0){echo "TOTAL ASSET";}elseif($key==1){echo "TOTAL LIABILITIES";}elseif($key==2){echo "TOTAL EQUITY/CAPITAL FUND";}?>
                                            </td>
                                            {{-- <td class="preFiYearTotalBalance" amount="" style="text-align:right;  padding-right: 20px;">
                                                </td> --}} {{-- extra td --}}
                                            <td class="preFiYearColumn" amount="" style="text-align:right;"></td>
                                            <td class="curFiYearColumn" amount="" style="text-align:right;"></td>
                                        </tr>


                                @endif
                                @endforeach           {{-- End foreach loop for ledger --}}

{{-- ===================================================Total Liabilities ================================================== --}}
                                    {{-- Fiscal Year --}}
                                        <tr id="grandTotalId" style="font-weight: bold; ">
                                            <td style="text-align:left;">TOTAL LIABILITIES & EQUITY</td>
                                            {{-- <td class="preFiYearTotalBalance" amount="" style="text-align:right;  padding-right: 20px;">
                                                </td> --}} {{-- extra td --}}
                                            <td class="preFiYearColumn" amount="" style="text-align:right;"></td>
                                            <td class="curFiYearColumn" amount="" style="text-align:right;"></td>
                                        </tr>


                                </tbody>
                            </table>
                        </div>      {{-- TableResponsiveDiv --}}
                    </div>      {{-- RowDiv --}}
                </div>      {{-- printDiv --}}
                    <?php }?>
            </div>
        </div>
    </div>
</div>
</div>
</div>

@endsection


<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>


  {{-- Filtering Mehod --}}
<script type="text/javascript">
    $(document).ready(function() {

        var searchedRoundUp="<?php echo $searchedRoundUp;?>";
        if (searchedRoundUp) {
            $("#roundUp").val(searchedRoundUp);
        }

        var searchedDepthLevel="<?php echo $searchedDepthLevel;?>";
        if (searchedDepthLevel) {
            $("#depthLevel").val(searchedDepthLevel);
        }

        var searchedWithZero = "{{$searchedWithZero}}";
        if (searchedWithZero) {
            $("#withZero").val(searchedWithZero);
        }

      $("#searchMethod").change(function(event) {
            var searchMethod = $(this).val();

            //Fiscal Year
            if(searchMethod==1){
                $("#fiscalYearDiv").show();
                $("#dateRangeDiv").hide();
            }
            //Current Year
            else if(searchMethod==2){
                $("#fiscalYearDiv").hide();
                $("#dateRangeDiv").show();
                /*var d = new Date();
                // console.log(d);
                var year = d.getFullYear();
                var month = d.getMonth();
                console.log(month);
                if (month<=5) {
                    year--;
                    month = 6;
                }
                else{
                    month = 6;
                }
                d.setFullYear(year, month, 1);

                var dayEndDate = "<?php $toDate = DB::table('acc_day_end')->where('isDayEnd', '=', '0')->value('date');?>";

                $("#dateFrom").datepicker("option","minDate",new Date(d));
                $("#dateTo").datepicker("option","minDate", dayEndDate);

                $("#dateFrom").datepicker("setDate", new Date(d));
                $("#dateFrom").hide();
                $("#dateFrome").hide();

                $("#dateToDiv").attr("class", "col-sm-12");*/

               /* var dateFrom = $.datepicker.parseDate( 'dd-mm-yy', $("#dateFrom").val());


                var fromMonth = dateFrom.getMonth() + 1;
                var fromYear = dateFrom.getFullYear();
                if (fromMonth<=6) {
                    var maxDate = $.datepicker.parseDate('dd-mm-yy','30-06-'+fromYear);
                    $("#dateTo").datepicker("option","maxDate",maxDate);
                }
                else{
                    fromYear = fromYear+1;
                    var maxDate = $.datepicker.parseDate('dd-mm-yy','30-06-'+fromYear);
                    $("#dateTo").datepicker("option","maxDate",maxDate);
                }*/
          }


        });
        // $("#searchMethod").trigger('change');

        var searchedSearchMethod="<?php echo $searchedSearchMethod;?>";
        // alert(searchedSearchMethod);
        if (searchedSearchMethod) {
            $("#searchMethod").val(searchedSearchMethod);
        }
        $("#searchMethod").trigger('change');
        var searchedFiscalYearId="{{$searchedFiscalYearId}}";
        if (searchedFiscalYearId) {
            $("#fiscalYearId").val(searchedFiscalYearId);
        }

        $("#balanceStatementSearch").click(function(event) {

            if ($("#searchMethod").val()==2) {

                if ($("#dateFrom").val()=="") {
                    event.preventDefault();
                    $("#dateFrome").show();
                }
                if ($("#dateTo").val()=="") {
                    event.preventDefault();
                    $("#dateToe").show();
                }
            }

        });

    });
</script>
  {{-- End Filtering Mehod --}}



<script type="text/javascript">
    $(document).ready(function(){

        //hideCurFiYearColumn function for hiding curFiYearColumn
    //     function hideCurFiYearColumn(){
    //     // var tbl = document.getElementById("accBalanceSheetTable") ;
    //     var tr = document.getElementByTagName("tr");
    //     var tds = tr.getElementsByTagName("td");
    //         for(var i = 0; i < tds.length; i++) {
    //              tds[i].style.color="red";
    // }
    //         $("#accBalanceSheetTable tr").each(function() {
    //         $(this).closest('tr').find('.curFiYearColumn').style.display=none;
    //             var preFiYearColumnAmount=$(this).closest('tr').find('.preFiYearColumn').attr('amount');
    //             var curFiYearColumnAmount=$(this).closest('tr').find('.curFiYearColumn').attr('amount');
    //             if(preFiYearColumnAmount==0){
    //                 $(this).closest('tr').find('.preFiYearColumn').html('-');
    //             }
    //             if(curFiYearColumnAmount==0){
    //                 $(this).closest('tr').find('.curFiYearColumn').html('-');
    //             }
    //             if(preFiYearColumnAmount==0 && curFiYearColumnAmount==0){
    //                 $(this).hide();
    //             }
    //         });
    //     }

        // End of hideCurFiYearColumn

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
                    // $("#branchId").prepend('<option selected="selected" value="">Select Branch</option>');
                    $("#branchId").append('<option value="">All (With HO)</option>');
                    $("#branchId").append('<option value="0">All (WithOut HO)</option>');
                    $("#branchId").append('<option value="1">000 - Head Office</option>');

                    $.each(branchList, function( key,obj){
                        // $('#branchId').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                        $('#branchId').append("<option value='"+obj.id+"'>"+pad(obj.branchCode,3)+" - "+obj.name+"</option>");
                    });

                    $("#projectTypeId").empty();
                    $("#projectTypeId").prepend('<option value="">All</option>');

                    $.each(projectTypeList, function( key,obj){
                        // $('#projectTypeId').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                        $('#projectTypeId').append("<option value='"+obj.id+"'>"+obj.projectTypeCode+" - "+obj.name+"</option>");
                    });

                },
                error: function(_response){
                    alert("Error");
                }
            });
        });

        function toDate(dateStr) {
            var parts = dateStr.split("-");
            return new Date(parts[2], parts[1] - 1, parts[0]);
        }

         /* Date Range From */
        // $("#dateFrom").datepicker({
        //         changeMonth: true,
        //         changeYear: true,
        //         yearRange : "2010:c",
        //         minDate: new Date(2010, 07 - 1, 01),
        //         maxDate: "dateToday",
        //         dateFormat: 'dd-mm-yy',
        //         onSelect: function () {
        //             $('#dateFrome').hide();
        //             $("#dateTo").datepicker("option","minDate",new Date(toDate($(this).val())));
        //             $( "#dateTo" ).datepicker( "option", "disabled", false );
        //             $("#searchMethod").trigger('change');
        //         }
        //     });
        /* Date Range From */


        /* Date Range To */
        $("#dateTo").datepicker({
                changeMonth: true,
                changeYear: true,
                yearRange : "2010:c",
                maxDate: "dateToday",
                dateFormat: 'dd-mm-yy',
                onSelect: function () {
                    $('#dateToe').hide();
                    $("#searchMethod").trigger('change');
                }
            });
    //$( "#dateTo" ).datepicker( "option", "disabled", true );
        /* End Date Range To */

        var dateFromData = $("#dateFrom").val();

         if (dateFromData!="") {
            // $("#dateTo").datepicker("option","minDate",new Date(toDate(dateFromData)));
            //$("#dateTo").datepicker( "option", "disabled", false );
        }

// ==========================================Starts RoundUP Function===========================================
    function roundUp(amount, searchedRoundUp){
            var roundUpAmount;
            if(searchedRoundUp==1){
                // roundUpAmount=amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                roundUpAmount = Math.round(amount);

            }else if(searchedRoundUp==2){
                roundUpAmount=amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            roundUpAmount=roundUpAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            return roundUpAmount;
        }
// ==========================================End RoundUP Function===========================================

//=======================================Starts SUM Function====================================================
        function sumOfEachRow(className, searchedRoundUp){

            var rowCount = $('#accBalanceSheetTable tr').length;
            // alert(rowCount);
            var amount1,amount2,amount3,amount4;
            amount1=amount2=amount3=amount4= 0;

            for(i=rowCount-1 ;i>=1;i--){
                if ($("#accBalanceSheetTable tr").eq(i).attr('level')==4) {
                    var tempAmount4 = parseFloat($("#accBalanceSheetTable tr").eq(i).find('.'+className).attr('amount'));
                    amount4 = amount4 + tempAmount4;

                }
                else if ($("#accBalanceSheetTable tr").eq(i).attr('level')==3) {
                    var type=$("#accBalanceSheetTable tr").eq(i).attr('type');
                    $("#accBalanceSheetTable tr").eq(i).closest('tr').find('.'+className).attr('amount',amount4);
                    // $("#accBalanceSheetTable tr").eq(i).closest('tr').find('.'+className).html(typeCheck(amount4, type, searchedRoundUp));
                    $("#accBalanceSheetTable tr").eq(i).closest('tr').find('.'+className).html(roundUp(amount4, searchedRoundUp));

                    var tempAmount3 = parseFloat($("#accBalanceSheetTable tr").eq(i).closest('tr').find('.'+className).attr('amount'));
                    amount3 = amount3 + tempAmount3;
                    amount4 = 0;
                }
                else if ($("#accBalanceSheetTable tr").eq(i).attr('level')==2) {
                    var type=$("#accBalanceSheetTable tr").eq(i).attr('type');
                    $("#accBalanceSheetTable tr").eq(i).closest('tr').find('.'+className).attr('amount',amount3);
                    $("#accBalanceSheetTable tr").eq(i).closest('tr').find('.'+className).html(roundUp(amount3, searchedRoundUp));

                    var tempAmount2 = parseFloat($("#accBalanceSheetTable tr").eq(i).closest('tr').find('.'+className).attr('amount'));
                    amount2 = amount2 + tempAmount2;
                    amount3 = 0;
                }
                else if ($("#accBalanceSheetTable tr").eq(i).attr('level')==1) {
                    var type=$("#accBalanceSheetTable tr").eq(i).attr('type');
                    $("#accBalanceSheetTable tr").eq(i).closest('tr').find('.'+className).attr('amount',amount2);
                    $("#accBalanceSheetTable tr").eq(i).closest('tr').find('.'+className).html(roundUp(amount2, searchedRoundUp));

                    var tempAmount1 = parseFloat($("#accBalanceSheetTable tr").eq(i).closest('tr').find('.'+className).attr('amount'));
                    amount1 = amount1 + tempAmount1;
                    amount2 = 0;
                }
                else if ($("#accBalanceSheetTable tr").eq(i).attr('level')==0) {
                    // $("#accBalanceSheetTable tr").eq(i).closest('tr').find('.totalOfThisMonthColumn').html(amount1);
                    // $("#accBalanceSheetTable tr").eq(i).closest('tr').find('.totalOfThisMonthColumn').attr('amount',amount1);
                    amount1 = 0;
                }
            }
            var totalAmount1 = 0;

            for(i=1; i<=rowCount; i++){
                if ($("#accBalanceSheetTable tr").eq(i).attr('level')==1) {
                    var tempTotalAmount1 = parseFloat($("#accBalanceSheetTable tr").eq(i).closest('tr').find('.'+className).attr('amount'));
                    totalAmount1 = totalAmount1 + tempTotalAmount1;
                }
                 else if ($("#accBalanceSheetTable tr").eq(i).attr('level')==0) {
                    var type=$("#accBalanceSheetTable tr").eq(i).attr('type');
                    $("#accBalanceSheetTable tr").eq(i).closest('tr').find('.'+className).attr('amount',totalAmount1);
                    $("#accBalanceSheetTable tr").eq(i).closest('tr').find('.'+className).html(roundUp(totalAmount1, searchedRoundUp));
                    totalAmount1 = 0;
                }
            }
        }
//=======================================End SUM Function====================================================

//===============================Starts GrandTotal Function====================================================
        function grandTotal(className, searchedRoundUp){
            var grandTotalAmount,totalExpenseAmount,totalEquityAmount;
            grandTotalAmount=totalIncomeAmount=totalExpenseAmount=0;
            var type="liabilities";

            totalExpenseAmount = parseFloat($("#accBalanceSheetTable #totalExpense").closest('tr').find('.'+className).attr('amount'));
            totalEquityAmount = parseFloat($("#accBalanceSheetTable #totalEquity").closest('tr').find('.'+className).attr('amount'));
            // grandTotalAmount=totalExpenseAmount+totalEquityAmount;

            // Comment By Sharif
            //grandTotalAmount=Math.abs(totalExpenseAmount)+totalEquityAmount;

            // From Sharif
            grandTotalAmount=totalExpenseAmount+totalEquityAmount;

            $("#accBalanceSheetTable #grandTotalId").find('.'+className).attr('amount',grandTotalAmount);
            $("#accBalanceSheetTable #grandTotalId").find('.'+className).html( roundUp(grandTotalAmount, searchedRoundUp));
        }

//===============================End GrandTotal Function====================================================

//===================================Starts Functions for hideRow==============================================
        function hideRowFiYear(){
            $("#accBalanceSheetTable tr").each(function() {
            // preFiYearTotalBalance
                // var preFiYearColumnTotalBalance=$(this).closest('tr').find('.preFiYearTotalBalance').attr('amount');
                var preFiYearColumnAmount= parseFloat($(this).closest('tr').find('.preFiYearColumn').attr('amount')).toFixed(2);
                var curFiYearColumnAmount= parseFloat($(this).closest('tr').find('.curFiYearColumn').attr('amount')).toFixed(2);

                // if(preFiYearColumnTotalBalance==0){
                //     $(this).closest('tr').find('.preFiYearTotalBalance').html('-');
                // }


                if(preFiYearColumnAmount==0){
                    $(this).closest('tr').find('.preFiYearColumn').html('-');
                }

                if(curFiYearColumnAmount==0){
                    $(this).closest('tr').find('.curFiYearColumn').html('-');
                }
                if(/*preFiYearColumnTotalBalance==0 &&*/ preFiYearColumnAmount==0 && curFiYearColumnAmount==0){
                    $(this).hide();
                }
            });
        }
        function hideRowCurrentYear(){
            $("#accBalanceSheetTable tr").each(function() {
                var previousYearColumnAmount= parseFloat($(this).closest('tr').find('.previousYearColumn').attr('amount')).toFixed(2);
                var thisYearColumnAmount= parseFloat($(this).closest('tr').find('.thisYearColumn').attr('amount')).toFixed(2);
                // var cumulativeColumnAmount=$(this).closest('tr').find('.cumulativeColumn').attr('amount');

                if(previousYearColumnAmount==0){
                    $(this).closest('tr').find('.previousYearColumn').html('-');
                }

                if(thisYearColumnAmount==0){
                    $(this).closest('tr').find('.thisYearColumn').html('-');
                }
                // if(cumulativeColumnAmount==0){
                //     $(this).closest('tr').find('.cumulativeColumn').html('-');
                // }
                if(thisYearColumnAmount==0 && previousYearColumnAmount==0){
                    $(this).hide();
                }
            });
        }
        function zeroBalanceToDotFiYear(){
            $("#accBalanceSheetTable tr").each(function() {
                // var preFiYearColumnTotalBalance=$(this).closest('tr').find('.preFiYearTotalBalance').attr('amount');
                var preFiYearColumnAmount= parseFloat($(this).closest('tr').find('.preFiYearColumn').attr('amount')).toFixed(2);
                var curFiYearColumnAmount= parseFloat($(this).closest('tr').find('.curFiYearColumn').attr('amount')).toFixed(2);

                // if(preFiYearColumnTotalBalance==0){
                //     $(this).closest('tr').find('.preFiYearTotalBalance').html('-');
                // }

                if(preFiYearColumnAmount==0){
                    $(this).closest('tr').find('.preFiYearColumn').html('-');
                }

                if(curFiYearColumnAmount==0){
                    $(this).closest('tr').find('.curFiYearColumn').html('-');
                }
            });
        }
        function zeroBalanceToDotCurrentYear(){
            $("#accBalanceSheetTable tr").each(function() {
                var previousYearColumnAmount= parseFloat($(this).closest('tr').find('.previousYearColumn').attr('amount')).toFixed(2);
                var thisYearColumnAmount= parseFloat($(this).closest('tr').find('.thisYearColumn').attr('amount')).toFixed(2);
                // var cumulativeColumnAmount=$(this).closest('tr').find('.cumulativeColumn').attr('amount');

                if(previousYearColumnAmount==0){
                    $(this).closest('tr').find('.previousYearColumn').html('-');
                }

                if(thisYearColumnAmount==0){
                    $(this).closest('tr').find('.thisYearColumn').html('-');
                }
                // if(cumulativeColumnAmount==0){
                //     $(this).closest('tr').find('.cumulativeColumn').html('-');
                // }
            });
        }

//===================================End Functions for hideRow=============================================

        var searchedRoundUp = "{{$searchedRoundUp}}";
        var searchedWithZero = "{{$searchedWithZero}}";
        // alert(searchedWithZero);

        var searchedSearchMethod="{{$searchedSearchMethod}}";
        // if (searchedSearchMethod==1) {
            // sumOfEachRow("preFiYearTotalBalance", searchedRoundUp);
            sumOfEachRow("preFiYearColumn", searchedRoundUp);
            sumOfEachRow("curFiYearColumn", searchedRoundUp);

            // grandTotal("preFiYearTotalBalance", searchedRoundUp);
            grandTotal("preFiYearColumn", searchedRoundUp);
            grandTotal("curFiYearColumn", searchedRoundUp);
            if(searchedWithZero==1){hideRowFiYear();}else if(searchedWithZero==2){zeroBalanceToDotFiYear();}
        // }else if(searchedSearchMethod==2){
        //     sumOfEachRow("previousYearColumn", searchedRoundUp);
        //     sumOfEachRow("thisYearColumn", searchedRoundUp);
        //     // sumOfEachRow("cumulativeColumn", searchedRoundUp);
        //
        //     grandTotal("previousYearColumn", searchedRoundUp);
        //     grandTotal("thisYearColumn", searchedRoundUp);
        //     // grandTotal("cumulativeColumn", searchedRoundUp);
        //     if(searchedWithZero==1){hideRowCurrentYear();}else if(searchedWithZero==2){zeroBalanceToDotCurrentYear();}
        // }

        var searchedDepthLevel = "{{$searchedDepthLevel}}";
        // alert(searchedDepthLevel);
        $("#accBalanceSheetTable tr").each(function(index, value) {
            if($(this).attr('level')>=searchedDepthLevel){
                $(this).hide();
            }
        });
        $("#accBalanceSheetTable #totalExpense").hide();

    });     //document.ready

    $(function(){
        $("#printIcon").click(function(){

            // $("#accBalanceSheetTable").removeClass('table table-striped table-bordered');
            // $('#accBalanceSheetTable').hide();
            // $('#accBalanceSheetTable').hide();

            // $("#accBalanceSheetTable").removeClass('table table-striped table-bordered');



            var printStyle = '<style>#accBalanceSheetTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt solid ash;page-break-inside:auto;font-family: arial!important;} tr:last-child { font-weight: bold;} thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;} tbody tr td { text-align:center;vertical-align: middle;padding:3px ;font-size:10px;} tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{size:portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';


            var mainContents = document.getElementById("printDiv").innerHTML;

            // var footerContents = "<div class='row' style='font-size:12px; padding-left:15px; padding-top:20px;'>Prepared By <span style='display:inline-block; width: 35%;'></span> Checked By <span style='display:inline-block; width: 33%; padding-right:20px;'></span> Approved By</div>";

            var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-top:20px;'>Prepared By <span style='display:inline-block; width: 35%; padding-top:40px;'></span> Checked By <span style='display:inline-block; width: 34%; padding-right:15px; padding-top:40px;'></span> Approved By</div>";

            var printContents = '<div id="order-details-wrapper" style="padding: 10px;">' + printStyle + mainContents + footerContents +'</div>';




            // var printContents = document.getElementById("printView").innerHTML;
            // var originalContents = document.body.innerHTML;
            // document.body.innerHTML ="" + printContents;
            // // window.print();
            // document.body.innerHTML = originalContents;

            // // $("#accBalanceSheetTable").addClass('table table-striped table-bordered');
            // location.reload();

            var win = window.open('','printwindow');
            win.document.write(printContents);
            win.print();
        // $("#reportingTable").addClass('table table-striped table-bordered');
            win.close();
        });


    });


</script>
>
