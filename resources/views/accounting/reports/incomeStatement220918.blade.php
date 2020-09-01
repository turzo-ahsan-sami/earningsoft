@extends('layouts/acc_layout')
@section('title', '| Income Statement')
@section('content')


<style type="text/css">
    #accIncomeStatementTable{
        font-family: arial !important;
    }

    #accIncomeStatementTable td{
        padding-left: 10px;
        padding-right: 10px;
    }
</style>

<?php
// $array = array(
//     1 => "a",
//     "1" => "b",
//     1.5 => "c",
//     true => "d"
// );
 //var_dump($array);

// echo '<pre>';
// print_r($array);
// echo '<pre>';
//echo "<br/>";
// var_dump($array);

?>

<?php
    function eachRow($child, $loopTrack, $voucherIdMatched, $searchedRoundUp, $searchedSearchMethod, $previousFisYearOpeningBalanceIds, $voucherIdMatchedByCurrMonth, $preFisYearOpeningBalanceIds){ ?>



    <tr class="item{{$child->id}}" level="{{$loopTrack}}" accountType="{{$child->accountTypeId}}">
        <td style="text-align: left;">



            <?php

                if($child->isGroupHead==1){
                    echo '<span style="font-weight: bold">'.strtoupper($child->name)." [".$child->code."]".'</span>';
                }else{
                    echo '<span style="font-weight: normal">'.$child->name." [".$child->code."]".'</span>';
                    // echo '<span style="font-weight: normal">'.$child->id." ".$child->name." [".$child->code."]".$child->accountTypeId.'</span>';
                }
            ?>
        </td>

        <?php
            // echo "<br/>";
            // var_dump($voucherIdMatched);
            // echo "<br/>";

            $debitAccAmount=$creditAccAmount=0;

            $debitAccAmount = DB::table('acc_voucher_details')
                    ->where('status',1)
                    ->whereIn('voucherId',$voucherIdMatched)
                    ->where('debitAcc', $child->id)
                    ->sum('amount');


            $creditAccAmount = DB::table('acc_voucher_details')
                    ->where('status',1)
                    ->whereIn('voucherId',$voucherIdMatched)
                    ->where('creditAcc', $child->id)
                    ->sum('amount');
            if($child->accountTypeId==12){
                $totalBalance=$creditAccAmount-$debitAccAmount;
            }elseif($child->accountTypeId==13){
                $totalBalance=$debitAccAmount-$creditAccAmount;
            }

            // echo "<br/>debitAccAmount: ";
            // var_dump($debitAccAmount);
            // echo "<br/>creditAccAmount: ";
            // var_dump($creditAccAmount);
            // echo "<br/>totalBalance: ";
            // var_dump($totalBalance);
            // echo "<br/>";

        ?>

        {{-- Fiscal Year --}}
        @if($searchedSearchMethod==1)
            @php
                $openingBalanceDebitAmount = DB::table('acc_opening_balance')->whereIn('id',$previousFisYearOpeningBalanceIds)->where('ledgerId', $child->id)->sum('debitAmount');
                $openingBalanceCreditAmount = DB::table('acc_opening_balance')->whereIn('id',$previousFisYearOpeningBalanceIds)->where('ledgerId', $child->id)->sum('creditAmount');
                if($child->accountTypeId==12){
                    $openingBalanceTotalByFisYr=$openingBalanceCreditAmount-$openingBalanceDebitAmount;
                }else if($child->accountTypeId==13){
                    $openingBalanceTotalByFisYr=$openingBalanceDebitAmount-$openingBalanceCreditAmount;
                }

            @endphp
            <td class="preFiYearColumn" amount={{$openingBalanceTotalByFisYr}} style="text-align: right; font-weight: <?php if($child->isGroupHead==1){echo "bold";}elseif($child->isGroupHead==0){echo "normal";} ?>;" >

                @if($searchedRoundUp==1)
                    {{number_format($openingBalanceTotalByFisYr, 2, '.', ',')}}
                @elseif($searchedRoundUp==2)
                    {{number_format($openingBalanceTotalByFisYr, 2, '.', '')}}
                @endif
            </td>
            <td class="curFiYearColumn" amount={{$totalBalance}} style="text-align: right; font-weight: <?php if($child->isGroupHead==1){echo "bold";}elseif($child->isGroupHead==0){echo "normal";} ?>;" >
                @if($searchedRoundUp==1)
                    {{number_format($totalBalance, 2, '.', ',')}}
                @elseif($searchedRoundUp==2)
                    {{number_format($totalBalance, 2, '.', '')}}
                @endif
            </td>

        {{-- Date Range --}}
        @elseif($searchedSearchMethod==2)
            <td class="thisYearColumn" amount={{$totalBalance}} style="text-align: right; font-weight: <?php if($child->isGroupHead==1){echo "bold";}elseif($child->isGroupHead==0){echo "normal";} ?>;" >
                @if($searchedRoundUp==1)
                    {{number_format($totalBalance, 2, '.', ',')}}
                @elseif($searchedRoundUp==2)
                    {{number_format($totalBalance, 2, '.', '')}}
                @endif

            </td>

        {{-- current Year --}}
        @elseif($searchedSearchMethod==3)

            @php
                $currentMonthDebitAccAmount = DB::table('acc_voucher_details')
                    ->where('status',1)
                    ->whereIn('voucherId',$voucherIdMatchedByCurrMonth)
                    ->where('debitAcc', $child->id)
                    ->sum('amount');

                $currentMonthCreditAccAmount = DB::table('acc_voucher_details')
                    ->where('status',1)
                    ->whereIn('voucherId',$voucherIdMatchedByCurrMonth)
                    ->where('creditAcc', $child->id)
                    ->sum('amount');
                if($child->accountTypeId==12){
                    $currentMonthTotalAmount=$currentMonthCreditAccAmount-$currentMonthDebitAccAmount;
                }elseif($child->accountTypeId==13){
                    $currentMonthTotalAmount=$currentMonthDebitAccAmount-$currentMonthCreditAccAmount;
                }

            @endphp

            <td class="thisMonthColumn" amount={{$currentMonthTotalAmount}} style="text-align: right; font-weight: <?php if($child->isGroupHead==1){echo "bold";}elseif($child->isGroupHead==0){echo "normal";} ?>;" >

                @if($searchedRoundUp==1)
                    {{number_format($currentMonthTotalAmount, 2, '.', ',')}}
                @elseif($searchedRoundUp==2)
                    {{number_format($currentMonthTotalAmount, 2, '.', '')}}
                @endif

            </td>
            <td class="thisYearColumn" amount={{$totalBalance}} style="text-align: right; font-weight: <?php if($child->isGroupHead==1){echo "bold";}elseif($child->isGroupHead==0){echo "normal";} ?>;" >
                @if($searchedRoundUp==1)
                    {{number_format($totalBalance, 2, '.', ',')}}
                @elseif($searchedRoundUp==2)
                    {{number_format($totalBalance, 2, '.', '')}}
                @endif
            </td>

            @php
                $currentFisYearOpeningBalanceDebitAmount = DB::table('acc_opening_balance')->whereIn('id',$preFisYearOpeningBalanceIds)->where('ledgerId', $child->id)->sum('debitAmount');

                $currentFisYearOpeningBalanceCreditAmount = DB::table('acc_opening_balance')->whereIn('id',$preFisYearOpeningBalanceIds)->where('ledgerId', $child->id)->sum('creditAmount');

                // print_r($preFisYearOpeningBalanceIds);


                $cumulativeTotal=$totalBalance+$currentFisYearOpeningBalanceDebitAmount+$currentFisYearOpeningBalanceCreditAmount;
            @endphp

            <td class="cumulativeColumn" amount={{$cumulativeTotal}} style="text-align: right; font-weight: <?php if($child->isGroupHead==1){echo "bold";}elseif($child->isGroupHead==0){echo "normal";} ?>;" >

                @if($searchedRoundUp==1)
                    {{number_format($cumulativeTotal, 2, '.', ',')}}
                @elseif($searchedRoundUp==2)
                    {{number_format($cumulativeTotal, 2, '.', '')}}
                @endif

            </td>
        @endif

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
            <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Statement of Comprehensive Income</h3>
        </div>

        <div class="panel-body panelBodyView">

            <!-- Filtering Start-->
            <div class="row">
                <div class="col-md-12">
                    <div class="row">

                        {!! Form::open(array('url' => 'incomeStatement/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'ladgerReportForm', 'method'=>'get')) !!}

                        @if($user_branch_id==1)
                        <div class="col-md-1">
                            <div class="form-group" style="font-size: 13px; color:#212F3C">
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
                            <div class="form-group" style="font-size: 13px; color:#212F3C">
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
                                    {!! Form::select('withZero',['1'=>'No','2'=>'Yes'], null,['id'=>'withZero','class'=>'form-control input-sm']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <div class="form-group" style="font-size: 13px; color:black;">
                                <div style="text-align: center;" class="col-sm-12">
                                    {!! Form::label('', 'Search By:', ['class' => 'control-label pull-left']) !!}
                                </div>
                                <div class="col-sm-12">
                                    {!! Form::select('searchMethod',['1'=>'Fiscal Year','2'=>'Date Range','3'=>'Current Year'], null,['id'=>'searchMethod','class'=>'form-control input-sm']) !!}
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
                            $toDate= date("d-m-Y");
                            if ($searchedDateFrom=="") { $toDateFrom=$toDate; }else{ $toDateFrom=$searchedDateFrom; }
                            if ($searchedDateTo=="") { $toDateTo=$toDate; }else{ $toDateTo=$searchedDateTo; }
                        ?>

                        <div class="col-md-2" style="display: none;" id="dateRangeDiv">
                            <div class="form-group" style="font-size: 13px; color:black">
                                <div style="text-align: center; padding-top: 10px;" class="col-sm-12">
                                    {!! Form::label('', ' ', ['class' => 'control-label']) !!}
                                </div>

                                <div class="col-sm-12" style="padding-top: 7px;">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            {!! Form::text('dateFrom', $toDateFrom,['id'=>'dateFrom','placeholder'=>'From','class'=>'form-control input-sm','readonly','style'=>'cursor:pointer']) !!}
                                            <p id="dateFrome" style="color: red;display: none;">*Required</p>
                                        </div>
                                        <div class="col-sm-6" id="dateToDiv">
                                            {!! Form::text('dateTo', $toDateTo,['id'=>'dateTo','placeholder'=>'To','class'=>'form-control input-sm','readonly','style'=>'cursor:pointer']) !!}
                                            <p id="dateToe" style="color: red;display: none;">*Required</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- <div class="col-md-1"></div> --}}

                        <div class="col-md-1">
                            <div class="form-group" style="">
                                {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                                <div class="col-sm-12" style="padding-top: 13%;">
                                    {!! Form::submit('search', ['id' => 'incomeStatementSearch', 'class' => 'btn btn-primary btn-s', 'style'=>'font-size:12px']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2"></div>

                        {!! Form::close()  !!}

                        {{-- end Div of ledgerSearch --}}

                        <div class="col-md-10"></div>
                    </div>
                </div>

            </div>
            <!-- filtering end-->


            <?php
            if ($searchedRoundUp!=null || $searchedRoundUp!="") {

            $projectIdArray = array();
            $projectTypeIdArray = array();
            $branchIdArray = array();

            // echo "<br> searchedProjectId:".$searchedProjectId;
            // echo "<br> searchedProjectTypeId:".$searchedProjectTypeId;
            // echo "<br> searchedBranchId:".$searchedBranchId;
            // echo "<br> searchedRoundUp:".$searchedRoundUp;
            // echo "<br> searchedDepthLevel:".$searchedDepthLevel;
            // echo "<br> searchedSearchMethod:".$searchedSearchMethod;
            // echo "<br> searchedFiscalYearId:".$searchedFiscalYearId;
            // echo "<br> searchedDateFrom:".$searchedDateFrom;
            // echo "<br> searchedDateTo:".$searchedDateTo;

                //Project
                $numOfAllProjects = DB::table('gnr_project')->pluck('id')->toArray();
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


            $previousFisYearOpeningBalanceIds=0;
            $voucherIdMatchedByCurrMonth=0;
            $preFisYearOpeningBalanceIds=0;
            //===========Get Start Date and End Date===========

            if ($searchedSearchMethod==1) {        //========Fiscal Year
                $startDate = date('Y-m-d', strtotime(DB::table('gnr_fiscal_year')->where('id',$searchedFiscalYearId)->value('fyStartDate')));
                $endDate = date('Y-m-d', strtotime(DB::table('gnr_fiscal_year')->where('id',$searchedFiscalYearId)->value('fyEndDate')));
                $previousFisStartDate=date('Y-m-d', strtotime("first day of last year".$startDate));
                $previousFisEndDate=date('Y-m-d', strtotime("last day of -1 year".$endDate));

                $previousFisId=DB::table('gnr_fiscal_year')->where('fyStartDate',$previousFisStartDate)->where('fyEndDate',$previousFisEndDate)->value('id');

                $previousFisYearOpeningBalanceIds = DB::table('acc_opening_balance')->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('fiscalYearId', $previousFisId)->pluck('id')->toArray();

                // print_r($preFisYearOpeningBalanceIds);


                $currentFiYearName=DB::table('gnr_fiscal_year')->where('id',$searchedFiscalYearId)->value('name');
                $previousFiYearName=DB::table('gnr_fiscal_year')->where('id',$previousFisId)->value('name');
            } elseif ($searchedSearchMethod==2 ) {        //========Date Range
                $startDate = date('Y-m-d',strtotime($searchedDateFrom));
                $endDate = date('Y-m-d',strtotime($searchedDateTo));
            } elseif ($searchedSearchMethod==3) {           //========Current Year
                $startDate = date('Y-m-d',strtotime($searchedDateFrom));
                $endDate = date('Y-m-d',strtotime($searchedDateTo));

                $currentMonthStartDate=date('Y-m-d', strtotime("first day of this month".$endDate));
                $currentMonthEndDate=$endDate;

                $voucherIdMatchedByCurrMonth = DB::table('acc_voucher')
                            ->where('status',1)
                            ->whereIn('projectTypeId',$projectTypeIdArray)
                            ->whereIn('branchId',$branchIdArray)
                            ->where(function ($query) use ($currentMonthStartDate,$currentMonthEndDate){
                                  $query->where('voucherDate','>=',$currentMonthStartDate)
                                  ->where('voucherDate','<=',$currentMonthEndDate);
                                });

                if (count($numOfAllProjects)==count($projectIdArray)) {
                    $voucherIdMatchedByCurrMonth = $voucherIdMatchedByCurrMonth->whereIn('projectId',$projectIdArray);
                }

                $voucherIdMatchedByCurrMonth = $voucherIdMatchedByCurrMonth->pluck('id')->toArray();

                $currentFiscalYear=DB::table('gnr_fiscal_year')->where('fyStartDate','<=', $startDate)->where('fyEndDate','>=', $startDate)->select('id', 'fyStartDate', 'fyEndDate')->first();

                $previousFisStartDate=date('Y-m-d', strtotime("first day of last year".$currentFiscalYear->fyStartDate));
                $previousFisEndDate=date('Y-m-d', strtotime("last day of -1 year".$currentFiscalYear->fyEndDate));

                $previousFiscalYearId=DB::table('gnr_fiscal_year')->where('fyStartDate','<=', $previousFisStartDate)->where('fyEndDate','>=', $previousFisEndDate)->value('id');
                // echo $currentFiscalYearId=DB::table('gnr_fiscal_year')->where('fyStartDate','<=', date('Y-m-d'))->where('fyEndDate','>=', date('Y-m-d'))->value('id');

                $preFisYearOpeningBalanceIds = DB::table('acc_opening_balance')->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('fiscalYearId', $previousFiscalYearId)->pluck('id')->toArray();

                // $currentFisYearOpeningBalanceCreditAmount = DB::table('acc_opening_balance')->whereIn('id',$preFisYearOpeningBalanceIds)->where('ledgerId', $child->id)->sum('creditAmount');

                // print_r($projectTypeIdArray);

            }


            // Today being 2012-05-31
//All the following return 2012-04-30
// echo "<br/>";
// echo date('Y-m-d', strtotime("last day of last month".$startDate));
// echo "<br/>";
// echo date('Y-m-d', strtotime("first day of -1 month".$startDate));
// echo "<br/>";
// echo "<br/>";
// echo "<br/>";
// echo date('Y-m-d', strtotime("last day of last month".$endDate));
// echo "<br/>";
// echo date('Y-m-d', strtotime("first day of this month".$endDate));
// echo "<br/>";
// echo "<br/>";
// echo date('Y-m-d', strtotime("first day of last year".$startDate));
// echo "<br/>";
// echo date('Y-m-d', strtotime("last day of -1 year".$endDate));
// echo "<br/>";
// echo var_dump($previousFisId);
// echo "<br/>";
// echo var_dump($previousFisYearOpeningBalanceIds);
// echo "<br/>";


            // echo "<br/>projectIdArray: ";
            // var_dump($projectIdArray);
            // echo "<br/>projectTypeIdArray: ";
            // var_dump($projectTypeIdArray);
            // echo "<br/>branchIdArray: ";
            // var_dump($branchIdArray);
            // echo "<br/>";
            // echo $startDate;
            // echo "<br/>";
            // echo $endDate;
            // echo "<br/>";
            // echo "<br/>";

//===============================================Starts Voucher Filtering=========================================================

            $voucherIdMatched = DB::table('acc_voucher')
                    ->where('status',1)
                    ->whereIn('projectId',$projectIdArray)
                    ->whereIn('projectTypeId',$projectTypeIdArray)
                    ->whereIn('branchId',$branchIdArray)
                    ->where(function ($query) use ($startDate,$endDate){
                          $query->where('voucherDate','>=',$startDate)
                          ->where('voucherDate','<=',$endDate);
                        })->pluck('id')->toArray();

            // echo "<br/>";
            // print_r($projectIdArray);
            // echo "<br/>";
            // print_r($projectTypeIdArray);
            // echo "<br/>";
            // print_r($branchIdArray);
            // echo "<br/>";
            // echo "$startDate";
            // echo "<br/>";
            // echo "$endDate";
            // echo "<br/>";
            // foreach($voucherIdMatched as $voucherId){
            //     echo "$voucherId,";

            // }
            // echo "<br/>";
            // print_r(count($voucherIdMatched));
            // echo "<br/>";
//===============================================End Voucher Filtering=========================================================

//===============================================Starts Ledger Filtering=========================================================

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

            /*echo "Ledger ID: ".count($ledgers->pluck('id')->toArray());
            echo "Ledger ID: ".count($ledgerMatchedId);*/
            //echo "Ledger ID: ".count(array_intersect($ledgers->pluck('id')->toArray(),$ledgerMatchedId));
//===============================================End Ledger Filtering=========================================================

            ?>

            <div id="printDiv">
                <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}
                    <?php
                        $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
                    ?>
                    <span style="font-size:14px;">{{$company->name}}</span><br/>
                    <span style="font-size:11px;">{{$company->address}}</span><br/>
                    <span style="text-decoration: underline;  font-size:14px;">Statement of Comprehensive Income</span><br>
                    <span>
                        <span style="font-weight: bold;">For the Year Ended :<?php echo str_repeat('&nbsp;', 1);?></span>
                        <span>{{date('d F, Y',strtotime($endDate))}}</span>
                    </span>
                </div>


                <div class="row">       {{-- div for Reporting Info --}}

                    <div class="col-md-12"  style="font-size: 12px;" >
                        <?php

                            $project = DB::table('gnr_project')->where('id',$searchedProjectId)->select('name')->first();
                            if($searchedProjectTypeId==""){
                                $projectType = "All";
                            }if($searchedProjectTypeId!=""){
                                $projectType = DB::table('gnr_project_type')->where('id',$searchedProjectTypeId)->value('name');
                            }
                            if($searchedBranchId==""){
                                $branch = "All";
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
                        <br/>

                        <span>
                            <span style="color: black; float: left;">
                                <span style="font-weight: bold;">Project Type: <?php echo str_repeat('&nbsp;', 5);?></span>
                                <span>{{$projectType}}</span>
                            </span>
                            <span style="color: black; float: right;">
                                <span style="font-weight: bold;">Print Date: <?php echo str_repeat('&nbsp;', 5);?></span>
                                <span>{{\Carbon\Carbon::now()->format('d-m-Y g:i A')}}</span>
                            </span>
                        </span>

                    </div>

                </div>


                <div class="row" style=" margin: 15px 0px;">
                    <div class="table-responsive">

                        <table class="table table-striped table-bordered" id="accIncomeStatementTable" border="1pt solid ash" style="border-collapse: collapse;" >
                            <thead>
                                <tr>
                                    <th style="padding: 0px;">Particular</th>
                                    {{-- <th width="" class="projectColumn">Project</th> --}}
                                    @if($searchedSearchMethod==1)
                                        <?php


                                        ?>
                                        <th style="padding: 0px; width: 160px;">
                                            {{-- FI Year --}}Previous Year <br/> {{"(".$previousFiYearName.")"}}
                                        </th>
                                        <th style="padding: 0px; width: 160px;">
                                            {{-- FI Year  --}}Current Year<br/> {{"(".$currentFiYearName.")"}}
                                        </th>
                                    @elseif($searchedSearchMethod==2)
                                        <th style="padding: 0px; width: 160px;">
                                            This Year <br/> {{"(".date('d-m-Y',strtotime($startDate))." To ".date('d-m-Y',strtotime($endDate)).")"}}
                                        </th>
                                    @elseif($searchedSearchMethod==3)
                                        <th style="padding: 0px; width: 160px;">
                                            This Month <br/>{{"(".date('d-m-Y',strtotime($currentMonthStartDate))." To ".date('d-m-Y',strtotime($currentMonthEndDate)).")"}}
                                        </th>
                                        <th style="padding: 0px; width: 160px;">
                                            This Year <br/> {{"(".date('d-m-Y',strtotime($startDate))." To ".date('d-m-Y',strtotime($endDate)).")"}}
                                        </th>
                                        <th style="padding: 0px; width: 140px;">Cumulative Amount</th>
                                    @endif
                                </tr>
                                {{ csrf_field() }}
                            </thead>
                            <tbody>

                            <?php $loopTrack=0; ?>
                            @foreach($ledgers as $key => $ledger)
                            @if(in_array($ledger->id,$ledgerMatchedId))
                                <?php
                                    $loopTrack=0;
                                    // eachRow($ledger, $loopTrack, $voucherIdMatched, $searchedRoundUp, $searchedSearchMethod, $previousFisYearOpeningBalanceIds, $voucherIdMatchedByCurrMonth, $preFisYearOpeningBalanceIds);
                                 ?>

                                {{-- Fiscal Year --}}
                                <?php if($searchedSearchMethod==1){ ?>
                                    <tr>
                                        <td level="0" style="font-weight: bold; text-align:left;" colspan="3">{{strtoupper($ledger->name)." [".$ledger->code."]"}}</td>
                                    </tr>
                                {{-- Date Range --}}
                                <?php }elseif($searchedSearchMethod==2){ ?>
                                    <tr>
                                        <td level="0" style="font-weight: bold; text-align:left;" colspan="2">{{strtoupper($ledger->name)." [".$ledger->code."]"}}</td>
                                    </tr>
                                {{-- Current Year --}}
                                <?php }elseif($searchedSearchMethod==3){ ?>
                                    <tr>
                                        <td level="0" style="font-weight: bold; text-align:left;" colspan="4">{{strtoupper($ledger->name)." [".$ledger->code."]"}}</td>
                                    </tr>
                                <?php } ?>


                                <?php
                                if($ledger->isGroupHead==1){
                                $children1=DB::table('acc_account_ledger')->where('parentId', $ledger->id)->orderBy('ordering', 'asc')->select('id', 'code', 'name', 'isGroupHead', 'accountTypeId')->get();

                                ?>
                                @foreach($children1 as $child1)
                                @if(in_array($child1->id,$ledgerMatchedId))
                                    <?php
                                    $loopTrack=1;
                                    eachRow($child1, $loopTrack, $voucherIdMatched, $searchedRoundUp, $searchedSearchMethod, $previousFisYearOpeningBalanceIds, $voucherIdMatchedByCurrMonth, $preFisYearOpeningBalanceIds);

                                    if($child1->isGroupHead==1){
                                    $children2=DB::table('acc_account_ledger')->where('parentId', $child1->id)->orderBy('ordering', 'asc')->select('id', 'code', 'name', 'isGroupHead', 'accountTypeId')->get();

                                    ?>
                                    @foreach($children2 as $child2)
                                    @if(in_array($child2->id,$ledgerMatchedId))
                                        <?php
                                        $loopTrack=2;
                                        eachRow($child2, $loopTrack, $voucherIdMatched, $searchedRoundUp, $searchedSearchMethod, $previousFisYearOpeningBalanceIds, $voucherIdMatchedByCurrMonth, $preFisYearOpeningBalanceIds);

                                        if($child2->isGroupHead==1){
                                        $children3=DB::table('acc_account_ledger')->where('parentId', $child2->id)->orderBy('ordering', 'asc')->select('id', 'code', 'name', 'isGroupHead', 'accountTypeId')->get();

                                        ?>
                                        @foreach($children3 as $child3)
                                        @if(in_array($child3->id,$ledgerMatchedId))
                                            <?php
                                                $loopTrack=3;
                                                eachRow($child3, $loopTrack, $voucherIdMatched, $searchedRoundUp, $searchedSearchMethod, $previousFisYearOpeningBalanceIds, $voucherIdMatchedByCurrMonth, $preFisYearOpeningBalanceIds);

                                            if($child3->isGroupHead==1){
                                            $children4=DB::table('acc_account_ledger')->where('parentId', $child3->id)->orderBy('ordering', 'asc')->select('id', 'code', 'name', 'isGroupHead', 'accountTypeId')->get();

                                            ?>
                                            @foreach($children4 as $child4)
                                            @if(in_array($child4->id,$ledgerMatchedId))
                                                <?php
                                                $loopTrack=4;
                                                eachRow($child4, $loopTrack, $voucherIdMatched, $searchedRoundUp, $searchedSearchMethod, $previousFisYearOpeningBalanceIds, $voucherIdMatchedByCurrMonth, $preFisYearOpeningBalanceIds);

                                                if($child4->isGroupHead==1){
                                                $children5=DB::table('acc_account_ledger')->where('parentId', $child4->id)->orderBy('ordering', 'asc')->select('id', 'code', 'name', 'isGroupHead', 'accountTypeId')->get();

                                                ?>
                                                @foreach($children5 as $child5)
                                                @if(in_array($child5->id,$ledgerMatchedId))
                                                    <?php
                                                    $loopTrack=5;
                                                    eachRow($child5, $loopTrack, $voucherIdMatched, $searchedRoundUp, $searchedSearchMethod, $previousFisYearOpeningBalanceIds, $voucherIdMatchedByCurrMonth, $preFisYearOpeningBalanceIds);
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

                                {{-- Fiscal Year --}}
                                <?php if($searchedSearchMethod==1){ ?>
                                    <tr level="0" id="<?php if($key==0){echo "totalIncome";}elseif($key==1){echo "totalExpense";} ?>" style="font-weight: bold; ">
                                        <td style="text-align:left;">
                                            <?php if($key==0){echo "TOTAL INCOME";}elseif($key==1){echo "TOTAL EXPENSE";}?>
                                        </td>
                                        <td class="preFiYearColumn" amount="" style="text-align:right;"></td>
                                        <td class="curFiYearColumn" amount="" style="text-align:right;"></td>
                                    </tr>
                                {{-- Date Range --}}
                                <?php }elseif($searchedSearchMethod==2){ ?>
                                    <tr level="0" id="<?php if($key==0){echo "totalIncome";}elseif($key==1){echo "totalExpense";} ?>" style="font-weight: bold; ">
                                        <td style="text-align:left;">
                                            <?php if($key==0){echo "TOTAL INCOME";}elseif($key==1){echo "TOTAL EXPENSE";}?>
                                        </td>
                                        <td class="thisYearColumn" amount="" style="text-align:right;"></td>
                                    </tr>
                                {{-- Current Year --}}
                                <?php }elseif($searchedSearchMethod==3){ ?>
                                    <tr level="0" id="<?php if($key==0){echo "totalIncome";}elseif($key==1){echo "totalExpense";} ?>" style="font-weight: bold;" >
                                        <td style="text-align:left;">
                                            <?php if($key==0){echo "TOTAL INCOME";}elseif($key==1){echo "TOTAL EXPENSE";}?>
                                        </td>
                                        <td class="thisMonthColumn" amount="" style="text-align:right;"></td>
                                        <td class="thisYearColumn" amount="" style="text-align:right;"></td>
                                        <td class="cumulativeColumn" amount="" style="text-align:right;"></td>
                                    </tr>
                                <?php } ?>


                                @endif
                              @endforeach           {{-- End foreach loop for ledger --}}


                                {{-- Fiscal Year --}}
                                <?php if($searchedSearchMethod==1){ ?>
                                    <tr id="grandTotalId" style="font-weight: bold; ">
                                        <td style="text-align:left;">
                                            SURPLUS/DEFICIT
                                        </td>
                                        <td class="preFiYearColumn" amount="" style="text-align:right;"></td>
                                        <td class="curFiYearColumn" amount="" style="text-align:right;"></td>
                                    </tr>
                                {{-- Date Range --}}
                                <?php }elseif($searchedSearchMethod==2){ ?>
                                    <tr id="grandTotalId" style="font-weight: bold; ">
                                        <td style="text-align:left;">
                                            SURPLUS/DEFICIT
                                        </td>
                                        <td class="thisYearColumn" amount="" style="text-align:right;"></td>
                                    </tr>
                                {{-- Current Year --}}
                                <?php }elseif($searchedSearchMethod==3){ ?>
                                    <tr id="grandTotalId" style="font-weight: bold;" >
                                        <td style="text-align:left;">
                                            SURPLUS/DEFICIT
                                        </td>
                                        <td class="thisMonthColumn" amount="" style="text-align:right;"></td>
                                        <td class="thisYearColumn" amount="" style="text-align:right;"></td>
                                        <td class="cumulativeColumn" amount="" style="text-align:right;"></td>
                                    </tr>
                                <?php } ?>



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

            //Date Range
            else if(searchMethod==2){
                $("#fiscalYearDiv").hide();
                $("#dateRangeDiv").show();

                $("#dateToDiv").attr("class", "col-sm-6");
                $("#dateFrom").show();
                //$("#dateFrom").val("");

                $("#dateFrom").datepicker("option","minDate",new Date(Date.parse("2010-07-01")));
            }
            //Current Year
            else if(searchMethod==3){

                $("#fiscalYearDiv").hide();
                $("#dateRangeDiv").show();
                /*var d = new Date();
                var year = d.getFullYear();
                var month = d.getMonth();
                if (month<=5) {
                    year--;
                    month = 6;
                }
                else{
                    month = 6;
                }
                d.setFullYear(year, month, 1);*/

                /*$("#dateFrom").datepicker("option","minDate",new Date(d));
                $("#dateTo").datepicker("option","minDate",new Date(d));

                $("#dateFrom").datepicker("setDate", new Date(d));
                $("#dateFrom").hide();
                $("#dateFrome").hide();

                $("#dateToDiv").attr("class", "col-sm-12");*/

                // var dateFrom = $("#dateFrom").datepicker('getDate');
                // var dateFrom = Date.parse('yy-mm-dd',$("#dateFrom").val());
                var dateFrom = $.datepicker.parseDate( 'dd-mm-yy', $("#dateFrom").val());


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
                }
            }
        });

        $("#searchMethod").trigger('change');

        var searchedSearchMethod="<?php echo $searchedSearchMethod;?>";
        if (searchedSearchMethod) {
            $("#searchMethod").val(searchedSearchMethod);
        }
        $("#searchMethod").trigger('change');
        var searchedFiscalYearId="{{$searchedFiscalYearId}}";
        if (searchedFiscalYearId) {
            $("#fiscalYearId").val(searchedFiscalYearId);
        }

        $("#incomeStatementSearch").click(function(event) {

            if ($("#searchMethod").val()==2 || $("#searchMethod").val()==3) {

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
                    $("#branchId").append('<option value="">All </option>');
                    $("#branchId").append('<option value="0">All Branch Office</option>');
                    $("#branchId").append('<option value="1">000 - Head Office</option>');

                    $.each(branchList, function( key,obj){
                        // $('#branchId').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                        $('#branchId').append("<option value='"+obj.id+"'>"+pad(obj.branchCode,3)+" - "+obj.name+"</option>");
                    });

                    $("#projectTypeId").empty();
                    $("#projectTypeId").prepend('<option value="">All</option>');

                    $.each(projectTypeList, function( key,obj){
                        $('#projectTypeId').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                        // $('#projectTypeId').append("<option value='"+obj.id+"'>"+obj.projectTypeCode+" - "+obj.name+"</option>");
                    });

                    // var searchedBranchId="<?php echo $searchedBranchId;?>";
                    // if (searchedBranchId=="") {
                    //     $("#branchId").val("");
                    // }else{
                    //     $("#branchId").val(searchedBranchId);
                    // }

                    // var searchedProjectTypeId="<?php echo $searchedProjectTypeId;?>";
                    // if (searchedProjectTypeId) {
                    //     $("#projectTypeId").val(searchedProjectTypeId);
                    // }
                },
                error: function(_response){
                    alert("Error");
                }
            });
        });
        // $("#projectId").trigger('change');
        // var searchedProjectTypeId="<?php echo $searchedProjectTypeId;?>";
        // if (searchedProjectTypeId=="") {
        //     $("#projectTypeId").val();
        // }else{
        //     $("#projectTypeId").val(searchedProjectTypeId);
        // }


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
                    $("#searchMethod").trigger('change');
                }
            });
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
            $("#dateTo").datepicker("option","minDate",new Date(toDate(dateFromData)));
            //$("#dateTo").datepicker( "option", "disabled", false );
        }





// ==========================================Starts RoundUP Function===========================================
    function roundUp(amount, searchedRoundUp){
            var roundUpAmount;
            if(searchedRoundUp==1){
                roundUpAmount=amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }else if(searchedRoundUp==2){
                roundUpAmount=amount.toFixed(2);
            }
            return roundUpAmount;
        }
// ==========================================End RoundUP Function===========================================

//=======================================Starts SUM Function====================================================
        function sumOfEachRow(className, searchedRoundUp){

            var rowCount = $('#accIncomeStatementTable tr').length;
            // alert(rowCount);
            var amount1,amount2,amount3,amount4;
            amount1=amount2=amount3=amount4= 0;

            for(i=rowCount-1 ;i>=1;i--){
                if ($("#accIncomeStatementTable tr").eq(i).attr('level')==4) {
                    var tempAmount4 = parseFloat($("#accIncomeStatementTable tr").eq(i).find('.'+className).attr('amount'));
                    amount4 = amount4 + tempAmount4;

                }
                else if ($("#accIncomeStatementTable tr").eq(i).attr('level')==3) {
                    $("#accIncomeStatementTable tr").eq(i).closest('tr').find('.'+className).attr('amount',amount4);
                    $("#accIncomeStatementTable tr").eq(i).closest('tr').find('.'+className).html(roundUp(amount4, searchedRoundUp));

                    var tempAmount3 = parseFloat($("#accIncomeStatementTable tr").eq(i).closest('tr').find('.'+className).attr('amount'));
                    amount3 = amount3 + tempAmount3;
                    amount4 = 0;
                }
                else if ($("#accIncomeStatementTable tr").eq(i).attr('level')==2) {
                    $("#accIncomeStatementTable tr").eq(i).closest('tr').find('.'+className).attr('amount',amount3);
                    $("#accIncomeStatementTable tr").eq(i).closest('tr').find('.'+className).html(roundUp(amount3, searchedRoundUp));

                    var tempAmount2 = parseFloat($("#accIncomeStatementTable tr").eq(i).closest('tr').find('.'+className).attr('amount'));
                    amount2 = amount2 + tempAmount2;
                    amount3 = 0;
                }
                else if ($("#accIncomeStatementTable tr").eq(i).attr('level')==1) {
                    $("#accIncomeStatementTable tr").eq(i).closest('tr').find('.'+className).attr('amount',amount2);
                    $("#accIncomeStatementTable tr").eq(i).closest('tr').find('.'+className).html(roundUp(amount2, searchedRoundUp));

                    var tempAmount1 = parseFloat($("#accIncomeStatementTable tr").eq(i).closest('tr').find('.'+className).attr('amount'));
                    amount1 = amount1 + tempAmount1;
                    amount2 = 0;
                }
                else if ($("#accIncomeStatementTable tr").eq(i).attr('level')==0) {
                    // $("#accIncomeStatementTable tr").eq(i).closest('tr').find('.totalOfThisMonthColumn').html(amount1);
                    // $("#accIncomeStatementTable tr").eq(i).closest('tr').find('.totalOfThisMonthColumn').attr('amount',amount1);
                    amount1 = 0;
                }
            }

            var totalAmount1 = 0;

            for(i=1; i<=rowCount; i++){
                if ($("#accIncomeStatementTable tr").eq(i).attr('level')==1) {
                    var tempTotalAmount1 = parseFloat($("#accIncomeStatementTable tr").eq(i).closest('tr').find('.'+className).attr('amount'));
                    totalAmount1 = totalAmount1 + tempTotalAmount1;
                }
                 else if ($("#accIncomeStatementTable tr").eq(i).attr('level')==0) {
                    $("#accIncomeStatementTable tr").eq(i).closest('tr').find('.'+className).attr('amount',totalAmount1);
                    $("#accIncomeStatementTable tr").eq(i).closest('tr').find('.'+className).html(roundUp(totalAmount1, searchedRoundUp));
                    totalAmount1 = 0;
                }
            }
        }
//=======================================End SUM Function====================================================


//===============================Starts GrandTotal Function====================================================
        function grandTotal(className, searchedRoundUp){
            var grandTotalAmount,totalIncomeAmount,totalExpenseAmount;
            grandTotalAmount=totalIncomeAmount=totalExpenseAmount=0;

            totalIncomeAmount = parseFloat($("#accIncomeStatementTable #totalIncome").closest('tr').find('.'+className).attr('amount'));
            totalExpenseAmount = parseFloat($("#accIncomeStatementTable #totalExpense").closest('tr').find('.'+className).attr('amount'));
            grandTotalAmount=totalIncomeAmount-totalExpenseAmount;
            $("#accIncomeStatementTable #grandTotalId").find('.'+className).html(roundUp(grandTotalAmount, searchedRoundUp));
            $("#accIncomeStatementTable #grandTotalId").find('.'+className).attr('amount',grandTotalAmount);
        }

//===============================End GrandTotal Function====================================================

//===================================Starts Functions for hideRow==============================================
        function hideRowFiYear(){
            $("#accIncomeStatementTable tr").each(function() {
                var preFiYearColumnAmount=$(this).closest('tr').find('.preFiYearColumn').attr('amount');
                var curFiYearColumnAmount=$(this).closest('tr').find('.curFiYearColumn').attr('amount');
                if(preFiYearColumnAmount==0){
                    $(this).closest('tr').find('.preFiYearColumn').html('-');
                }
                if(curFiYearColumnAmount==0){
                    $(this).closest('tr').find('.curFiYearColumn').html('-');
                }
                if(preFiYearColumnAmount==0 && curFiYearColumnAmount==0){
                    $(this).hide();
                }
            });
        }
        function hideRowDateRange(){
            $("#accIncomeStatementTable tr").each(function() {
                var thisYearColumnAmount=$(this).closest('tr').find('.thisYearColumn').attr('amount');
                if(thisYearColumnAmount==0){
                    $(this).closest('tr').find('.thisYearColumn').html('-');
                }
                if(thisYearColumnAmount==0){
                    $(this).hide();
                }
            });
        }
        function hideRowCurrentYear(){
            $("#accIncomeStatementTable tr").each(function() {
                var thisMonthColumnAmount=$(this).closest('tr').find('.thisMonthColumn').attr('amount');
                var thisYearColumnAmount=$(this).closest('tr').find('.thisYearColumn').attr('amount');
                var cumulativeColumnAmount=$(this).closest('tr').find('.cumulativeColumn').attr('amount');
                if(thisMonthColumnAmount==0){
                    $(this).closest('tr').find('.thisMonthColumn').html('-');
                }
                if(thisYearColumnAmount==0){
                    $(this).closest('tr').find('.thisYearColumn').html('-');
                }
                if(cumulativeColumnAmount==0){
                    $(this).closest('tr').find('.cumulativeColumn').html('-');
                }
                if(thisMonthColumnAmount==0 && thisYearColumnAmount==0 && cumulativeColumnAmount==0){
                    $(this).hide();
                }
            });
        }
        function zeroBalanceToDotFiYear(){
            $("#accIncomeStatementTable tr").each(function() {
                var preFiYearColumnAmount=$(this).closest('tr').find('.preFiYearColumn').attr('amount');
                var curFiYearColumnAmount=$(this).closest('tr').find('.curFiYearColumn').attr('amount');
                if(preFiYearColumnAmount==0){
                    $(this).closest('tr').find('.preFiYearColumn').html('-');
                }
                if(curFiYearColumnAmount==0){
                    $(this).closest('tr').find('.curFiYearColumn').html('-');
                }
            });
        }
        function zeroBalanceToDotDateRange(){
            $("#accIncomeStatementTable tr").each(function() {
                var thisYearColumnAmount=$(this).closest('tr').find('.thisYearColumn').attr('amount');
                if(thisYearColumnAmount==0){
                    $(this).closest('tr').find('.thisYearColumn').html('-');
                }
            });
        }
        function zeroBalanceToDotCurrentYear(){
            $("#accIncomeStatementTable tr").each(function() {
                var thisMonthColumnAmount=$(this).closest('tr').find('.thisMonthColumn').attr('amount');
                var thisYearColumnAmount=$(this).closest('tr').find('.thisYearColumn').attr('amount');
                var cumulativeColumnAmount=$(this).closest('tr').find('.cumulativeColumn').attr('amount');
                if(thisMonthColumnAmount==0){
                    $(this).closest('tr').find('.thisMonthColumn').html('-');
                }
                if(thisYearColumnAmount==0){
                    $(this).closest('tr').find('.thisYearColumn').html('-');
                }
                if(cumulativeColumnAmount==0){
                    $(this).closest('tr').find('.cumulativeColumn').html('-');
                }
            });
        }

//===================================End Functions for hideRow=============================================

        var searchedRoundUp = "{{$searchedRoundUp}}";
        var searchedWithZero = "{{$searchedWithZero}}";
        // alert(searchedWithZero);

        var searchedSearchMethod="{{$searchedSearchMethod}}";
        if (searchedSearchMethod==1) {
            sumOfEachRow("preFiYearColumn", searchedRoundUp);
            sumOfEachRow("curFiYearColumn", searchedRoundUp);
            grandTotal("preFiYearColumn", searchedRoundUp);
            grandTotal("curFiYearColumn", searchedRoundUp);
            if(searchedWithZero==1){hideRowFiYear();}else if(searchedWithZero==2){zeroBalanceToDotFiYear();}
        }else if(searchedSearchMethod==2){
            sumOfEachRow("thisYearColumn", searchedRoundUp);
            grandTotal("thisYearColumn", searchedRoundUp);
            if(searchedWithZero==1){hideRowDateRange();}else if(searchedWithZero==2){zeroBalanceToDotDateRange();}
        }else if(searchedSearchMethod==3){
            sumOfEachRow("thisMonthColumn", searchedRoundUp);
            sumOfEachRow("thisYearColumn", searchedRoundUp);
            sumOfEachRow("cumulativeColumn", searchedRoundUp);

            grandTotal("thisMonthColumn", searchedRoundUp);
            grandTotal("thisYearColumn", searchedRoundUp);
            grandTotal("cumulativeColumn", searchedRoundUp);
            if(searchedWithZero==1){hideRowCurrentYear();}else if(searchedWithZero==2){zeroBalanceToDotCurrentYear();}
        }

        var searchedDepthLevel = "{{$searchedDepthLevel}}";
        // alert(searchedDepthLevel);
        $("#accIncomeStatementTable tr").each(function(index, value) {
            if($(this).attr('level')>=searchedDepthLevel){
                $(this).hide();
            }
        });



    });     //document.ready

    $(function(){
        $("#printIcon").click(function(){

            // $("#accIncomeStatementTable").removeClass('table table-striped table-bordered');
            // $('#accIncomeStatementTable').hide();
            // $('#accIncomeStatementTable').hide();

            // $("#accIncomeStatementTable").removeClass('table table-striped table-bordered');

            // var printStyle = '<style>#accIncomeStatementTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt solid ash;page-break-inside:auto; font-family: arial!important;} tr:last-child { font-weight: bold;} thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;} tbody tr td { text-align:center;vertical-align: middle;padding:3px ;font-size:10px;} tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{size:portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

            var printStyle = '<style>#accIncomeStatementTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt solid ash;page-break-inside:auto;font-family: arial!important;} tr:last-child { font-weight: bold;} thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;} tbody tr td { text-align:center;vertical-align: middle;padding:3px ;font-size:10px;} tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{size:portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

            var mainContents = document.getElementById("printDiv").innerHTML;

            // var footerContents = "<div class='row' style='font-size:12px; padding-left:15px; padding-top:20px;'>Prepared By <span style='display:inline-block; width: 36%;'></span> Checked By <span style='display:inline-block; width: 34%; padding-right:20px;'></span> Approved By</div>";

            var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-top:20px;'>Prepared By <span style='display:inline-block; width: 35%; padding-top:40px;'></span> Checked By <span style='display:inline-block; width: 34%; padding-right:15px; padding-top:40px;'></span>Approved By</div>";

            var printContents = '<div id="order-details-wrapper" style="padding: 10px;">' + printStyle + mainContents + footerContents +'</div>';

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

            // // $("#accIncomeStatementTable").addClass('table table-striped table-bordered');
            // location.reload();
        });
    });


</script>
