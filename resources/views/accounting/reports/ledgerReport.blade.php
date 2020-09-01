@extends('layouts/acc_layout')
@section('title', '| Ledger Report')
@section('content')

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
                        <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Ledger Report</h3>
                    </div>

                    <div class="panel-body panelBodyView">

                        <!-- Filtering Start-->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">

                                    {{-- <div class="col-md-2"> --}}
                                        {{-- <div class="row"> --}}

                                            {!! Form::open(array('url' => 'ledgerReport/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'ladgerReportForm', 'method'=>'get')) !!}

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

                                            <div class="col-md-2">
                                                <div class="form-group" style="font-size: 13px; color:#212F3C;">
                                                    {!! Form::label('', 'Ledger:', ['class' => 'control-label col-sm-12']) !!}
                                                    <div class="col-sm-12">
                                                        <select class="form-control selectPicker" name="ledgerId" id="ledgerId" required>
                                                            <option value="">Select Ledger</option>
                                                            @foreach ($childrenLedger as $childLedger)
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
                                                    <div class="col-sm-12" style="padding-top: 13%;">
                                                        {!! Form::submit('search', ['id' => 'ledgerReportSubmit', 'class' => 'btn btn-primary btn-s', 'style'=>'font-size:12px']); !!}
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
                            <script type="text/javascript">
                                jQuery(document).ready(function($)
                                {
                                    /*$("#accLedgerReportTable").dataTable().yadcf([

                                     ]);*/
                                    // $("#accLedgerReportTable").dataTable({
                                    //     "sPaginationType": "full_numbers",
                                    //     "bFilter": true,
                                    //     "sDom":"lrtip",

                                    //     "lengthMenu": [[-1], ["All"]],
                                    //     // "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
                                    //     "oLanguage": {
                                    //         "sEmptyTable": "No Records Available",
                                    //         "sLengthMenu": ""
                                    //         // "sLengthMenu": "Show _MENU_"
                                    //     }
                                    //         // "columns": [
                                    //         //     { "width": "4%" },
                                    //         //     { "width": "6%" },
                                    //         //     { "width": "10%" },
                                    //         //     { "width": "30%" },
                                    //         //     null,
                                    //         //     { "width": "7%" },
                                    //         //     { "width": "7%" },
                                    //         //     { "width": "7%" },
                                    //         //     { "width": "4%" }
                                    //         // ]
                                    // });

                                    // $('#accLedgerReportTable_info').hide();
                                    // $('#accLedgerReportTable_paginate').hide();

                                });
                            </script>

                        </div>

                        <?php
                        if ($searchedLedgerId!=null || $searchedLedgerId!="") {

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
                                $projectTypeIdArray = DB::table('gnr_project_type')->whereIn('projectId', $projectIdArray)->pluck('id')->toArray();
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
                        //         ->where(function ($query) use ($startDate,$endDate){
                        //               $query->where('voucherDate','>=',$startDate)
                        //               ->where('voucherDate','<=',$endDate);
                        //             })->pluck('id')->toArray();

                        // echo "<br/>";
                        // var_dump($voucherIdMatched);
                        // echo "<br/>";

                        // $voucherDetails = DB::table('acc_voucher_details')
                        //         ->whereIn('voucherId',$voucherIdMatched)
                        //         ->where(function ($query) use ($searchedLedgerId){
                        //                   $query->where('debitAcc', $searchedLedgerId)
                        //                   ->orWhere('creditAcc', $searchedLedgerId);
                        //                 })
                        //         ->select('voucherId','debitAcc','creditAcc','amount')
                        //         ->get();

                        // var_dump($voucherDetails);


                        $voucherDetails = DB::table('acc_voucher')
                                    ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                    ->whereIn('acc_voucher.projectId',$projectIdArray)
                                    ->whereIn('acc_voucher.projectTypeId',$projectTypeIdArray)
                                    ->whereIn('acc_voucher.branchId',$branchIdArray)
                                    ->whereIn('acc_voucher.voucherTypeId',$voucherTypeIdArray)
                                    ->where('acc_voucher.status', 1)
                                    ->where(function ($query) use ($startDate, $endDate){
                                          $query->where('acc_voucher.voucherDate','>=',$startDate)
                                          ->where('acc_voucher.voucherDate','<=',$endDate);
                                        })
                                    ->where(function ($query) use ($searchedLedgerId){
                                              $query->where('acc_voucher_details.debitAcc', $searchedLedgerId)
                                              ->orWhere('acc_voucher_details.creditAcc', $searchedLedgerId);
                                            })
                                    ->select('acc_voucher.voucherDate','acc_voucher.voucherCode','acc_voucher.globalNarration','acc_voucher_details.voucherId','acc_voucher_details.debitAcc','acc_voucher_details.creditAcc','acc_voucher_details.amount')
                                    ->orderBy('acc_voucher.voucherDate')
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

                            $openingBalanceAmountByDate = DB::table('acc_opening_balance')->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('ledgerId', $searchedLedgerId)->where('fiscalYearId', @$previousFiscalYearValue->id)->sum('balanceAmount');

                            // $openingBalanceAmountByDate = DB::table('acc_opening_balance')->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('ledgerId', $searchedLedgerId)->where('fiscalYearId', $fiscalYearValue->id)->sum('balanceAmount');
                            // echo "<br/>";
                            // var_dump($openingBalanceAmountByDate);
                            // echo "<br/>";
                            // $temp=$fiscalYearValue->fyStartDate;

                            $voucherIdMatchedForOpBal = DB::table('acc_voucher')
                                    ->whereIn('projectId',$projectIdArray)
                                    ->whereIn('projectTypeId',$projectTypeIdArray)
                                    ->whereIn('branchId',$branchIdArray)
                                    ->where('voucherDate','>=',$fiscalYearValue->fyStartDate)
                                    ->where('voucherDate','<',$startDate)
                                    ->pluck('id')->toArray();

                            // var_dump($voucherIdMatchedForOpBal);
                            // echo "<br/>";

                            $debitAccAmount = DB::table('acc_voucher_details')
                                    ->whereIn('voucherId',$voucherIdMatchedForOpBal)
                                    ->where('debitAcc', $searchedLedgerId)
                                    ->select(
                                        DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                    )
                                    ->get()->keyBy('debitAcc')->toArray();
                            // dd($debitAccAmount);

                            $creditAccAmount = DB::table('acc_voucher_details')
                                    ->whereIn('voucherId',$voucherIdMatchedForOpBal)
                                    ->where('creditAcc', $searchedLedgerId)
                                    ->select(
                                        DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                                    )
                                    ->get()->keyBy('creditAcc')->toArray();

                            // $debitArr = DB::table('acc_voucher')
                            //             ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                            //             ->whereIn('acc_voucher.projectId',$projectIdArray)
                            //             ->whereIn('acc_voucher.projectTypeId',$projectTypeIdArray)
                            //             ->whereIn('acc_voucher.branchId',$branchIdArray)
                            //             ->where('acc_voucher.voucherDate','>=',$fiscalYearValue->fyStartDate)
                            //             ->where('acc_voucher.voucherDate','<',$startDate)
                            //             ->where('acc_voucher_details.debitAcc', $searchedLedgerId)
                            //             ->select(
                            //                 'acc_voucher_details.debitAcc as ledgerId',
                            //                 DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                            //             )
                            //             ->get()->keyBy('ledgerId')->toArray();
                            //
                            // $creditArr = DB::table('acc_voucher')
                            //             ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                            //             ->whereIn('acc_voucher.projectId',$projectIdArray)
                            //             ->whereIn('acc_voucher.projectTypeId',$projectTypeIdArray)
                            //             ->whereIn('acc_voucher.branchId',$branchIdArray)
                            //             ->where('acc_voucher.voucherDate','>=',$fiscalYearValue->fyStartDate)
                            //             ->where('acc_voucher.voucherDate','<',$startDate)
                            //             ->where('acc_voucher_details.creditAcc', $searchedLedgerId)
                            //             ->select(
                            //                 'acc_voucher_details.creditAcc as ledgerId',
                            //                 DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                            //             )
                            //             ->get()->keyBy('ledgerId')->toArray();
                            //
                            $debitAccAmount = isset($debitArr[$searchedLedgerId]) ? $debitArr[$searchedLedgerId]['debitAmount'] : 0;
                            $creditAccAmount = isset($creditArr[$searchedLedgerId]) ? $creditArr[$searchedLedgerId]['creditAmount'] : 0;

                            $totalOpeningBalanceAmount=$openingBalanceAmountByDate+$debitAccAmount-$creditAccAmount;

                            // echo "<br/>openingBalanceAmountByDate:";     var_dump($openingBalanceAmountByDate);
                            // echo "<br/>debitAccAmount:";     var_dump($debitAccAmount);
                            // echo "<br/>creditAccAmount:";    var_dump($creditAccAmount);
                            // echo "<br/>totalOpeningBalanceAmount:";    var_dump($totalOpeningBalanceAmount);


                            // $openingBalanceAmount = DB::table('acc_opening_balance')->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('ledgerId', $searchedLedgerId)->sum('balanceAmount');
                            // $balanceAmount=$openingBalanceAmount;
                            $balanceAmount=$totalOpeningBalanceAmount;
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
                                <span style="text-decoration: underline;  font-size:14px;">Ledger Report</span>
                            </div>

                            <div class="row">       {{-- div for Ledger --}}

                                <div class="col-md-12"  style="font-size: 12px;" >
                                    <?php
                                        $ledgerHead = DB::table('acc_account_ledger')->where('id',$searchedLedgerId)->select('name', 'code')->first();
                                        // $project = DB::table('gnr_project')->where('id',$searchedProjectId)->select('name')->first();
                                        // if($searchedProjectTypeId!=""){
                                        //     $projectType = DB::table('gnr_project_type')->where('id',$searchedProjectTypeId)->select('name')->first();
                                        // }
                                        // $branch = DB::table('gnr_branch')->where('id',$searchedBranchId)->select('name')->first();

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
                                            <span>{{$ledgerHead->name}}</span>
                                        </span>

                                         <span style="color: black; float: right;">
                                            <span style="font-weight: bold;">Branch Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                                            <span>{{$branch}}<?php echo str_repeat('&nbsp;', 15);?></span>
                                        </span>

                                    </span>
                                    <br/>

                                    <span>
                                        <span style="color: black; float: left;">
                                            <span style="font-weight: bold;">Project Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                                            <span>{{$project->name}}</span>
                                        </span>

                                        <span style="color: black; float: right;">
                                            <span style="font-weight: bold;">Reporting Date:<?php echo str_repeat('&nbsp;', 6);?></span>
                                            <span>{{$searchedDateFrom." to ".$searchedDateTo}}</span>
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
                                            <span style="font-weight: bold;">Print Date: <?php echo str_repeat('&nbsp;', 22);?></span>
                                            <span>{{\Carbon\Carbon::now()->format('d-m-Y g:i A')}}</span>
                                        </span>
                                    </span>
                                    <br/>


                                </div>

                            </div>


                            <div class="row" style=" margin: 15px 0px;">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered dt-responsive nowrap" border="1pt solid ash" style="color:black; border-collapse: collapse; padding-bottom: 20px !important; " id="accLedgerReportTable">
                                        <thead>
                                        <tr>
                                            <th style="width: 4%;">SL#</th>
                                            <th style="padding: 12px 20px; width: 6%;">Date</th>
                                            <th style="width: 10%;">Voucher Code</th>
                                            <th style="width: 30%;">Account Head</th>
                                            <th style="width: 25%;">Narration/Cheque Details</th>
                                            <th style="width: 7%;">Debit Amount</th>
                                            <th style="width: 7%;">Credit Amount</th>
                                            <th style="width: 7%;">Balance</th>
                                            <th style="padding: 12px 10px; width: 4%;">Dr/Cr</th>
                                        </tr>
                                        {{ csrf_field() }}
                                        </thead>
                                        <?php
                                            $no=0;
                                            $DebitTotal = 0;
                                            $CreditTotal = 0;
                                        ?>
                                        <tbody>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td style="text-align: left;">Opening Balance</td>
                                            <td></td>
                                            {{-- Debit Credit Atiqul Haque --}}
                                            <td style="text-align: right;">{{number_format($debitAccAmount,2, '.', ',')}}</td>
                                            @php
                                              $DebitTotal = $DebitTotal + $debitAccAmount;
                                            @endphp
                                            <td style="text-align: right;">{{number_format($creditAccAmount,2,  '.', ',')}}</td>
                                            @php
                                              $CreditTotal = $CreditTotal + $creditAccAmount;
                                            @endphp
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

                                            <tr>
                                                <td class="">{{++$no}}</td>
                                                <?php
                                                // dd($voucherDetail);
                                                // $voucherInfo = DB::table('acc_voucher')->where('id', $voucherDetail->voucherId)->select('voucherDate','voucherCode','globalNarration')->first();
                                                ?>
                                                <td style="text-align: left;">{{ Carbon\Carbon::parse($voucherDetail->voucherDate)->format('d-m-Y') }}</td>
                                                <td style="text-align: left;">{{$voucherDetail->voucherCode}}</td>
                                                <td style="text-align: left;">
                                                    <?php
                                                    // dd($searchedLedgerId);
                                                    if($searchedLedgerId!=$voucherDetail->debitAcc){
                                                        // dd(1);
                                                        // dd($voucherDetail->debitAcc);
                                                        // $ledgerInfo = DB::table('acc_account_ledger')->where('id', $voucherDetail->debitAcc)->select('name','code')->first();
                                                        $ledgerNameWithCode=$ledgerInfo[$voucherDetail->debitAcc];
                                                    // }else{
                                                        // dd($voucherDetail->debitAcc);
                                                    }else if($searchedLedgerId!=$voucherDetail->creditAcc){
                                                        // dd(2);
                                                        // $ledgerInfo = DB::table('acc_account_ledger')->where('id', $voucherDetail->creditAcc)->select('name','code')->first();
                                                        $ledgerNameWithCode=$ledgerInfo[$voucherDetail->creditAcc];
                                                    }
                                                    else if($voucherDetail->debitAcc == $voucherDetail->creditAcc) {
                                                        // dd($voucherDetail->creditAcc);
                                                        $ledgerNameWithCode = $ledgerInfo[$voucherDetail->debitAcc];
                                                    }
                                                    // dd($ledgerNameWithCode);
                                                    ?>
                                                    {{$ledgerNameWithCode}}
                                                </td>
                                                <td style="text-align: left;">{{$voucherDetail->globalNarration}}</td>

                                                <?php if($searchedLedgerId==$voucherDetail->debitAcc && $voucherDetail->debitAcc != $voucherDetail->creditAcc){
                                                    // dd(1);
                                                array_push($debitAmountArray,$voucherDetail->amount);
                                                $balanceAmount=$balanceAmount+$voucherDetail->amount;
                                                ?>
                                                <td style="text-align: right;">{{number_format( $voucherDetail->amount, 2, '.', ',')}}</td>
                                                <td style="text-align: right;">-</td>

                                                <?php }else if($searchedLedgerId==$voucherDetail->creditAcc && $voucherDetail->debitAcc != $voucherDetail->creditAcc){
                                                    // dd(2);
                                                array_push($creditAmountArray,$voucherDetail->amount);
                                                $balanceAmount=$balanceAmount-$voucherDetail->amount;
                                                ?>
                                                <td style="text-align: right;">-</td>
                                                <td style="text-align: right;">{{number_format( $voucherDetail->amount, 2, '.', ',')}}</td>

                                                <?php }else if($voucherDetail->debitAcc == $voucherDetail->creditAcc){
                                                    // dd(3);
                                                array_push($debitAmountArray,$voucherDetail->amount);
                                                array_push($creditAmountArray,$voucherDetail->amount);
                                                $balanceAmount=$balanceAmount;
                                                ?>
                                                <td style="text-align: right;">{{number_format( $voucherDetail->amount, 2, '.', ',')}}</td>
                                                <td style="text-align: right;">{{number_format( $voucherDetail->amount, 2, '.', ',')}}</td>

                                                <?php }?>

                                                {{-- <td>{{number_format( $balanceAmount, 2, '.', ',')}}</td> --}}
                                                <td style="text-align: right;">{{number_format( abs($balanceAmount), 2, '.', ',')}}</td>
                                                <td>
                                                    <?php if($balanceAmount<0){ echo "Cr"; }else{ echo "Dr";} ?>
                                                </td>
                                            </tr>

                                        @endforeach

                                            <tr style="font-weight: bold;">
                                                {{-- <td><span style="display: none;">{{++$no}}</span></td> --}}
                                                {{-- <td></td> --}}
                                                {{-- <td></td> --}}
                                                {{-- <td></td> --}}
                                                <td colspan="5" style="background-color: #9cb1d3"> <strong>Sub Total</strong></td>
                                                <td style="text-align: right; background-color: #9cb1d3">{{number_format(array_sum($debitAmountArray), 2, '.', ',')}}</td>
                                                @php
                                                  $DebitTotal = $DebitTotal + array_sum($debitAmountArray);
                                                @endphp
                                                <td style="text-align: right; background-color: #9cb1d3">{{number_format(array_sum($creditAmountArray), 2, '.', ',')}}</td>
                                                @php
                                                  $CreditTotal = $CreditTotal + array_sum($creditAmountArray);
                                                @endphp
                                                <?php
                                                $totalBalanceAmount=((array_sum($debitAmountArray)+$totalOpeningBalanceAmount)-array_sum($creditAmountArray));
                                                ?>
                                                <td style="text-align: right; background-color: #9cb1d3"> {{number_format(abs($totalBalanceAmount), 2, '.', ',')}} </td>
                                                {{-- <td> {{number_format($totalBalanceAmount, 2, '.', ',')}} </td> --}}
                                                <td style="background-color: #9cb1d3">
                                                    <?php if($totalBalanceAmount<0){ echo "Cr";}else{ echo "Dr";} ?>
                                                </td>
                                            </tr>
                                            <tr>
                                              <td colspan="5" style="background-color: #7291C9; font-size: 13px;"> <strong>Total</strong> </td>
                                              <td style="background-color: #7291C9; text-align: right; font-size: 13px;"><strong>{{number_format($DebitTotal, 2, '.', ',')}}</strong></td>
                                              <td style="background-color: #7291C9; text-align: right; font-size: 13px;"><strong>{{number_format($CreditTotal, 2, '.', ',')}}</strong></td>
                                              <td style="background-color: #7291C9; text-align: right; font-size: 13px;"><strong>{{number_format(abs($totalBalanceAmount),2, '.', ',')}}</strong></td>
                                              <td style="background-color: #7291C9; font-size: 13px;">
                                                <strong><?php if($totalBalanceAmount<0){ echo "Cr";}else{ echo "Dr";} ?></strong>
                                              </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>  {{-- TableResponsiveDiv --}}
                                <?php }?>
                            </div> {{-- rowDiv --}}

                        </div> {{-- printDiv --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- @include('dataTableScript') --}}

<script type="text/javascript">
    $(document).ready(function(){

        $(".selectPicker").select2();
        // $("#ledgerId").next("span").css("width","100%");

        $('#ledgerReportSubmit').click(function(){
            var ledgerIdChecked=$('#ledgerId').val();
            if(ledgerIdChecked){$('#ledgerIde').hide();}else{$('#ledgerIde').html('*Required');}
        });
        $('#ledgerId').on('change', function (e) {
            var ledgerId = $("#ledgerId").val();
            if(ledgerId){$('#ledgerIde').hide();}else{$('#ledgerIde').show();}
        });

        var searchedVoucherTypeId = '<?php echo $searchedVoucherTypeId; ?>';
        $('#voucherTypeId').val(searchedVoucherTypeId);

        var searchedLedgerId = '{{$searchedLedgerId}}';
        $('#ledgerId').val(searchedLedgerId);

        // var searchedDateFrom = '<?php echo $searchedDateFrom; ?>';
        // if (searchedDateFrom=="") {
        //     $("#dateFrom").val("<?php echo $dateFromSelected;?>");
        // }else{
        //     $('#dateFrom').val(searchedDateFrom);
        // }

        // var searchedDateTo = '<?php echo $searchedDateTo; ?>';
        // $('#sateTo').val(searchedDateTo);

        function pad (str, max) {
            str = str.toString();
            return str.length < max ? pad("0" + str, max) : str;
        }


        $("#projectId").change(function () {

            $('#projectIde').hide();
            var projectId = this.value;

            if(projectId==""){
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
                    var projectTypeList=data['projectTypeList'];

                    $("#branchId option:gt(2)").remove();

                    // $("#branchId").empty();
                    // $("#branchId").append('<option value="1">000 - Head Office</option>');

                    $.each(branchList, function( key,obj){
                        $('#branchId').append("<option value='"+obj.id+"'>"+pad(obj.branchCode,3)+" - "+obj.name+"</option>");
                    });

                    $("#projectTypeId").empty();
                    $("#projectTypeId").prepend('<option value="">All</option>');

                    $.each(projectTypeList, function( key,obj){
                        $('#projectTypeId').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                        // $('#projectTypeId').append("<option value='"+obj.id+"'>"+obj.projectTypeCode+" - "+obj.name+"</option>");
                    });
                    $("#branchId").trigger('change');
                },
                error: function(_response){
                    alert("Error: Project");
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
            // alert("projectId "+projectId+" branchId "+branchId)

            $.ajax({
                type: 'post',
                url: './getProjectTypeNLedgersInfo',
                data: {projectId: projectId, branchId:branchId, _token: csrf},
                dataType: 'json',
                success: function (data) {
                    // alert(JSON.stringify(data));
                    var projectTypeList=data['projectTypeList'];
                    var ledgersOfAllAccountType=data['ledgersOfAllAccountType'];

                    $("#ledgerId").empty();
                    $("#ledgerId").prepend('<option selected="selected" value="">Select  Ledger</option>');

                    $.each(ledgersOfAllAccountType, function(key, obj){

                        // $('#ledgerId').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                        $('#ledgerId').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                    });

                    // var searchedLedgerId = '<?php echo $searchedLedgerId; ?>';
                    // $('#ledgerId').val(searchedLedgerId);

                },
                error: function(_response){
                    alert("Error: branch");
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

        /* Date Range From */
        $("#dateFrom").datepicker({
                changeMonth: true,
                changeYear: true,
                yearRange : "2010:c",
                minDate: new Date(2010, 07 - 1, 01),
                // maxDate: "dateToday",
                dateFormat: 'dd-mm-yy',
                onSelect: function () {
                    $('#dateFrome').hide();
                    $("#dateTo").datepicker("option","minDate",new Date(toDate($(this).val())));
                    //$( "#dateTo" ).datepicker( "option", "disabled", false );
                }
            });
        /* End of Date Range From */

        // triggering minimum date in date to
        $('.ui-datepicker-current-day').click();

        /* Date Range To */
        $("#dateTo").datepicker({
                changeMonth: true,
                changeYear: true,
                // yearRange : "2017:c",
                minDate: new Date(2010, 07 - 1, 01),
                // maxDate: "dateToday",
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
            //$("#dateTo").datepicker("option","minDate",new Date(toDate(dateFromData)));
            //$("#dateTo").datepicker( "option", "disabled", false );
        }

        //Getting software starting date according to branch

        $("#branchId").change(function(event) {
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

            // $("#accLedgerReportTable").removeClass('table table-striped table-bordered');
            // $('#accLedgerReportTable_info').hide();
            // $('#accLedgerReportTable_paginate').hide();

            // $("#accLedgerReportTable").removeClass('table table-striped table-bordered');

            // var printStyle = '<style>#accLedgerReportTable{float:left;height:auto;padding:0px 0px 20px 0px;width:100%;font-size:11px;border:1pt solid ash;page-break-inside:auto; font-family: arial!important;} tr:last-child { font-weight: bold;} thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;} tbody tr td { text-align:center;vertical-align: middle;padding:3px ;font-size:10px;} tr{ page-break-inside:avoid; page-break-after:auto } </style><style>@page {size: auto;margin: 25;}</style>';

            // var printStyle = '<style>#accLedgerReportTable{float:left;height:auto;padding:0px;width:100% !important;font-size:11px;page-break-inside:auto; font-family: arial!important;}  thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:11px} tr{ page-break-inside:avoid; page-break-after:auto } table tbody tr td.amount{text-align:right !important;}</style><style media="print">@page{size: A4 portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

            var printStyle = '<style>#accLedgerReportTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt solid ash;page-break-inside:auto;font-family: arial!important;} tr:last-child { font-weight: bold;} thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;} tbody tr td { text-align:center;vertical-align: middle;padding:3px ;font-size:10px;} tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{size:portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

            var mainContents = document.getElementById("printDiv").innerHTML;

            // var footerContents = "<br><br><br><div class='row' style='font-size:12px; padding-left:15px; padding-top:40px !important;'>Prepared By <span style='display:inline-block; width: 34%;'></span> Checked By <span style='display:inline-block; width: 34%; padding-right:20px;'></span> Approved By</div>";

            // var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-top:20px;'>Prepared By <span style='display:inline-block; width: 35%; padding-top:40px;'></span> Checked By <span style='display:inline-block; width: 34%; padding-right:15px; padding-top:40px;'></span>Approved By</div>";

            var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-top:20px;'>Prepared By <span style='display:inline-block; width: 35%; padding-top:40px;'></span> Checked By <span style='display:inline-block; width: 34%; padding-right:15px; padding-top:40px;'></span>Approved By</div>";

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

@endsection
