@extends('layouts/acc_layout')
@section('title', '| Cash Flows')
@section('content')

<?php
    function roundUpPHP($amount, $searchedRoundUp){
        if($searchedRoundUp==1){
            $roundUpAmount=number_format($amount, 2, '.', ',');
        }elseif($searchedRoundUp==2){
            $roundUpAmount=number_format($amount, 2, '.', '');
        }
        return $roundUpAmount;
    }

    function structure($matchedLedgerIdsArray, $dataArray){
        $ledgers =  DB::table('acc_account_ledger')->where('parentId', 0)->orderBy('ordering', 'asc')->get();

        $loopTrack=0;
        foreach($ledgers as $ledger):
        if(in_array($ledger->id,$matchedLedgerIdsArray)):

            $loopTrack=0;
            eachRow($ledger, $loopTrack, $dataArray);

            if($ledger->isGroupHead==1){
            $children1=DB::table('acc_account_ledger')->where('parentId', $ledger->id)->orderBy('ordering', 'asc')->get();


            foreach($children1 as $child1):
            if(in_array($child1->id,$matchedLedgerIdsArray)):

                $loopTrack=1;
                eachRow($child1, $loopTrack, $dataArray);

                if($child1->isGroupHead==1){
                $children2=DB::table('acc_account_ledger')->where('parentId', $child1->id)->orderBy('ordering', 'asc')->get();


                foreach($children2 as $child2):
                if(in_array($child2->id,$matchedLedgerIdsArray)):

                    $loopTrack=2;
                     eachRow($child2, $loopTrack, $dataArray);

                    if($child2->isGroupHead==1){
                    $children3=DB::table('acc_account_ledger')->where('parentId', $child2->id)->orderBy('ordering', 'asc')->get();

                    foreach($children3 as $child3):
                    if(in_array($child3->id,$matchedLedgerIdsArray)):

                        $loopTrack=3;
                        eachRow($child3, $loopTrack, $dataArray);

                        if($child3->isGroupHead==1){
                        $children4=DB::table('acc_account_ledger')->where('parentId', $child3->id)->orderBy('ordering', 'asc')->get();

                        foreach($children4 as $child4):
                        if(in_array($child4->id,$matchedLedgerIdsArray)):

                              $loopTrack=4;
                              eachRow($child4, $loopTrack, $dataArray);


                        endif;
                        endforeach;  }           //{{-- End foreach loop for Child4 --}}
                    endif;
                    endforeach;  }          //{{-- End foreach loop for Child3 --}}
                endif;
                endforeach;  }         //{{-- End foreach loop for Child2 --}}
            endif;
            endforeach;  }       //{{-- End foreach loop for Child1 --}}
        endif;
        endforeach;           //{{-- End foreach loop for ledger --}}

    }           //END structure function


    function eachRow($child, $loopTrack, $dataArray ) { ?>

    <tr class="item{{$child->id}}" level="{{$loopTrack}}" style="font-weight: <?php if($child->isGroupHead==1){echo "bold";}elseif($child->isGroupHead==0){echo "normal";} ?>;" >

            <td>
            <?php
            switch ($loopTrack) {
                case "0":
                    echo str_repeat('&nbsp;', 7);
                    break;
                case "1":
                    echo str_repeat('&nbsp;', 7*2);
                    break;
                case "2":
                    echo str_repeat('&nbsp;', (7*3));
                    break;
                case "3":
                    echo str_repeat('&nbsp;',  (7*4));
                    break;
                case "4":
                    echo str_repeat('&nbsp;',  (7*5));
                    break;
                default:
                    echo str_repeat('&nbsp;',   (7*6));
            }
            ?>

            <?php
                if($child->isGroupHead==1){
                    echo $child->name." [".$child->code."]";
                }else{
                    // echo $child->name." [".$child->code."]";
                    echo $child->id."-".$child->name." [".$child->code."]";

                    // var_dump($dataArray);
                }

                    // var_dump($dataArray["stage"]);
                    // var_dump($dataArray["voucherIdsOfCuFY"]);
                    // var_dump($dataArray["voucherIdsOfCuYr"]);
            ?>

        </td>
            <td class="preFiYearColumn" amount="" ></td>
            <td class="curFiYearColumn" amount="" ></td>


    </tr>

    <?php } ?>      {{-- eachRow Function --}}

<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
        <div class="panel panel-default" style="background-color:#708090;">
            <div class="panel-heading" style="padding-bottom:0px">
                <div class="panel-options">
                    <button id="printIcon" class="btn btn-info pull-right"  target="_blank" style="font-size: 14px; margin-top: 10px; border: 3px solid #525659; border-radius: 25px; padding: ">
                        <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
                    </button>
                </div>
                <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Statement of Cash Flows</h3>
            </div>

            <div class="panel-body panelBodyView">

            <!-- Filtering Start-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">

                            {!! Form::open(array('url' => 'cashFlows/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'ladgerReportForm', 'method'=>'get')) !!}

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
                                            <option value="">All (With Head Office)</option>
                                            <option value="0" @if($branchSelected===0){{"selected=selected"}}@endif >All (With Out Head Office)</option>
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

                            {{-- <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:black;">
                                    <div style="text-align: center;" class="col-sm-12">
                                        {!! Form::label('', '\'0\' Balance:', ['class' => 'control-label pull-left']) !!}
                                    </div>
                                    <div class="col-sm-12">
                                        {!! Form::select('withZero',['2'=>'Yes','1'=>'No'], null,['id'=>'withZero','class'=>'form-control input-sm']) !!}
                                    </div>
                                </div>
                            </div> --}}

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
                                $toDate= date("d-m-Y");
                                if ($searchedDateFrom=="") { $toDateFrom=$toDate; }else{ $toDateFrom=$searchedDateFrom; }
                                if ($searchedDateTo=="") { $toDateTo=$toDate; }else{ $toDateTo=$searchedDateTo; }
                            ?>

                            <div class="col-md-2" style="display: none;" id="dateRangeDiv">
                                <div class="form-group" style="font-size: 13px; color:black">
                                    <div style="text-align: center;  padding-top: 10px;" class="col-sm-12">
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
                                        {!! Form::submit('search', ['id' => 'balanceStatementSearch', 'class' => 'btn btn-primary btn-xs', 'style'=>'font-size:15px']); !!}
                                    </div>
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
                // echo "<br> user_project_id:".$user_project_id;
                // echo "<br> user_project_type_id:".$user_project_type_id;
                // echo "<br> user_branch_id:".$user_branch_id;

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

                // echo "<br/>projectIdArray: "; var_dump($projectIdArray);
                // echo "<br/>projectTypeIdArray: "; var_dump($projectTypeIdArray);
                // echo "<br/>branchIdArray: "; var_dump($branchIdArray);

                $capitalFundLedgerIds = DB::table('acc_account_ledger')->whereIn('accountTypeId',[10,11])->where('isGroupHead', 0)->pluck('id')->toArray();
                $retainedSurplusLedgerIds = DB::table('acc_account_ledger')->where('accountTypeId',10)->where('isGroupHead', 0)->pluck('id')->toArray();
                $reserveFundLedgerIds = DB::table('acc_account_ledger')->where('accountTypeId',11)->where('isGroupHead', 0)->pluck('id')->toArray();
                $incomeLedgerIds = DB::table('acc_account_ledger')->where('isGroupHead', 0)->where('accountTypeId', 12)->pluck('id')->toArray();
                $expenseLedgerIds = DB::table('acc_account_ledger')->where('isGroupHead', 0)->where('accountTypeId', 13)->pluck('id')->toArray();
                // echo "<br/>capitalFundLedgerIds: ";var_dump($capitalFundLedgerIds);
                // echo "<br/>incomeLedgerIds: ";var_dump($incomeLedgerIds);
                // echo "<br/>expenseLedgerIds: ";var_dump($expenseLedgerIds);

                //==========================Search By Fiscal Year=================================
                if ($searchedSearchMethod==1) {
                    $startDate = date('Y-m-d', strtotime(DB::table('gnr_fiscal_year')->where('id',$searchedFiscalYearId)->value('fyStartDate')));
                    $endDate = date('Y-m-d', strtotime(DB::table('gnr_fiscal_year')->where('id',$searchedFiscalYearId)->value('fyEndDate')));

                    $firstPreFYStartDate=date('Y-m-d', strtotime("first day of last year".$startDate));
                    $firstPreFYEndDate=date('Y-m-d', strtotime("last day of -1 year".$endDate));
                    $secondPreFYStartDate=date('Y-m-d', strtotime("first day of -2 year".$startDate));
                    $secondPreFYEndDate=date('Y-m-d', strtotime("last day of -2 year".$endDate));

                    $firstPreFYId=DB::table('gnr_fiscal_year')->where('fyStartDate',$firstPreFYStartDate)->where('fyEndDate',$firstPreFYEndDate)->value('id');
                    $secondPreFYId=DB::table('gnr_fiscal_year')->where('fyStartDate',$secondPreFYStartDate)->where('fyEndDate',$secondPreFYEndDate)->value('id');

                    $currentFiYearName=DB::table('gnr_fiscal_year')->where('id',$searchedFiscalYearId)->value('name');
                    $previousFiYearName=DB::table('gnr_fiscal_year')->where('id',$firstPreFYId)->value('name');
                    // var_dump($firstPreFYId);
                }
                //==========================Search By Current Year=================================
                elseif ($searchedSearchMethod==2){
                    $startDate = date('Y-m-d',strtotime($searchedDateFrom));
                    $endDate = date('Y-m-d',strtotime($searchedDateTo));
                    $currentFiscalYear=DB::table('gnr_fiscal_year')->where('fyStartDate','<=', date('Y-m-d'))->where('fyEndDate','>=', date('Y-m-d'))->select('id','fyStartDate','fyEndDate')->first();

                    $currentEndDay=$currentFiscalYear->fyEndDate;

                    $firstPreFYStartDate=date('Y-m-d', strtotime("first day of last year".$startDate));
                    $firstPreFYEndDate=date('Y-m-d', strtotime("last day of -1 year".$currentEndDay));

                    $firstPreFYId=DB::table('gnr_fiscal_year')->where('fyStartDate',$firstPreFYStartDate)->value('id');
// should Remove Below Comment
                    // $firstPreFYId=DB::table('gnr_fiscal_year')->where('fyStartDate',$firstPreFYStartDate)->where('fyEndDate',$firstPreFYEndDate)->value('id');
                }


                $dataArray= ['projectIdArray' => $projectIdArray, 'projectTypeIdArray' => $projectTypeIdArray, 'branchIdArray' => $branchIdArray, 'searchedRoundUp' => $searchedRoundUp, 'searchedSearchMethod' => $searchedSearchMethod];
                // print("<pre>");
                // print_r($dataArray);
                // print("</pre>");

                // var_dump($dataArray['projectTypeIdArray']);

    //===============================Starts Ledger Filtering=========================================

                $allLedgers = DB::table('acc_account_ledger')->select("id","projectBranchId")->get();

                $ledgerMatchedId=array();

                // $allSecondIndexValueArray = DB::table('gnr_branch')->pluck('id')->toArray();
                $secondIndexValueArray = DB::table('gnr_branch')->whereIn('projectId',$projectIdArray)->orWhere('id', 1)->pluck('id')->toArray();

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
                                // $secondIndexValueArray = DB::table('gnr_branch')->pluck('id')->toArray();
                                foreach($branchIdArray as $branch){
                                    if (in_array($branch, $secondIndexValueArray)){
                                        array_push($ledgerMatchedId, $singleLedger->id);
                                        break;
                                    }
                                }
                            }
                        }else{
                            // if($firstIndexValue!=$temp){
                                if($firstIndexValue==$searchedProjectId){
                                    if($secondIndexValue==0){
                                        // $secondIndexValueArray = DB::table('gnr_branch')->where('projectId',$searchedProjectId)->pluck('id')->toArray();
                                        foreach($branchIdArray as $branch){
                                            if (in_array($branch, $secondIndexValueArray)){
                                                array_push($ledgerMatchedId, $singleLedger->id);
                                                break;
                                            }
                                        }

                                    }else{
                                        if (in_array($secondIndexValue, $branchIdArray)){
                                            array_push($ledgerMatchedId, $singleLedger->id);
                                        }
                                    }
                                }
                            // }
                            // $temp=$firstIndexValue;
                        }
                    }   //for
                }       //foreach
    //===============================================End Ledger Filtering=========================================================

                // echo "<br/>ledgerMatchedId: ";var_dump($ledgerMatchedId);
                $ledgerMatchedId=array_unique($ledgerMatchedId);
                // echo "<br/>array_unique_ledgerMatchedId: ";var_dump($ledgerMatchedId);

                function trackLevels($ledgerIdsArray){

                    $matchedLedgerIdsArray = array();
                    foreach ($ledgerIdsArray as $ledgerId) {
                        $ledger = DB::table('acc_account_ledger')->where('id',$ledgerId)->select('id','parentId')->first();
                        array_push($matchedLedgerIdsArray, $ledger->id);
                        for ($i = 1; $i <= 4; $i++) {
                            $ledger = DB::table('acc_account_ledger')->where('id',$ledger->parentId)->select("id","parentId")->first();
                            array_push($matchedLedgerIdsArray, $ledger->id);
                        }
                    }
                    return $matchedLedgerIdsArray;
                }
                function totalSurplusCal($incomeLedgerIds, $expenseLedgerIds, $voucherIds){
                    $incomeAmount=$expenseAmount=$totalSurplus=0;
                    $incomeAmount = (DB::table('acc_voucher_details')
                            ->whereIn('voucherId',$voucherIds)
                            ->whereIn('debitAcc', $incomeLedgerIds)
                            ->sum('amount'))
                        +(DB::table('acc_voucher_details')
                            ->whereIn('voucherId',$voucherIds)
                            ->whereIn('creditAcc', $incomeLedgerIds)
                            ->sum('amount'));

                    $expenseAmount = (DB::table('acc_voucher_details')
                            ->whereIn('voucherId',$voucherIds)
                            ->whereIn('debitAcc', $expenseLedgerIds)
                            ->sum('amount'))
                        +(DB::table('acc_voucher_details')
                            ->whereIn('voucherId',$voucherIds)
                            ->whereIn('creditAcc', $expenseLedgerIds)
                            ->sum('amount'));
                    $totalSurplus=$incomeAmount-$expenseAmount;
                    return $totalSurplus;
                }

                function thirdParentToFifthChild($ledgerMatchedId){
                    $thirdParentIds = DB::table('acc_account_ledger')->whereIn('id',$ledgerMatchedId)->whereIn('code', [14000])->pluck('id')->toArray();
                    $fouthParentId = DB::table('acc_account_ledger')->whereIn('id',$ledgerMatchedId)->whereIn('parentId', $thirdParentIds)->pluck('id')->toArray();
                    // var_dump($loanOutstandingId);
                    $fifthChildIds = DB::table('acc_account_ledger')->whereIn('id',$ledgerMatchedId)->whereIn('parentId', $fouthParentId)->pluck('id')->toArray();
                    // var_dump($fifthChildIds);
                    // $fifthChildIdsArray=array_unique(trackLevels($fifthChildIds));
                    // echo "<br/>fifthChildIdsArray: "; var_dump($fifthChildIdsArray);
                    return $fifthChildIds;
                }
                $childArray=thirdParentToFifthChild($ledgerMatchedId);
                echo "<br/>array_unique_childArray: ";var_dump($childArray);

                ?>


                <div id="printDiv">
                    <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}
                        <?php
                            $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
                        ?>
                        <span style="font-size:14px;">{{$company->name}}</span><br/>
                        <span style="font-size:11px;">{{$company->address}}</span><br/>
                        <span style="text-decoration: underline;  font-size:14px;">Statement of Cash Flows</span>
                    </div>

                    <div class="row">       {{-- div for Reporting Info --}}

                        <div class="col-md-12"  style="font-size: 12px;" >
                            <?php

                                $project = DB::table('gnr_project')->where('id',$searchedProjectId)->value('name');
                                if($searchedProjectTypeId==""){
                                    $projectType = "All";
                                }if($searchedProjectTypeId!=""){
                                    $projectType = DB::table('gnr_project_type')->where('id',$searchedProjectTypeId)->value('name');
                                }
                                if($searchedBranchId==""){
                                    $branch = "All With HeadOffice";
                                }else if($searchedBranchId==0){
                                    $branch = "All With Out HeadOffice";
                                }else{
                                    $branch = DB::table('gnr_branch')->where('id',$searchedBranchId)->value('name');
                                }
                            ?>
                            <table id="reportingInfoTable">
                                <tbody>
                                    <tr>
                                        <td style="font-weight: bold; width: 90px;"> Project Name</td>
                                        <td style="width: 10px;">:</td>
                                        <td style="width: 150px;">{{$project}}</td>
                                        <td style="width: ;"></td>
                                        <td style="font-weight: bold; width: 100px;"> Reporting Period</td>
                                        <td style="width: 10px;">:</td>
                                        <td style="width: 145px;">{{date('d-m-Y',strtotime($startDate))." To ".date('d-m-Y',strtotime($endDate))}}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;"> Project Type</td>
                                        <td>:</td>
                                        <td>{{$projectType}}</td>
                                        <td></td>
                                        <td style="font-weight: bold;"></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;"> Branch Name</td>
                                        <td>:</td>
                                        <td>{{$branch}}</td>
                                        <td></td>
                                        <td style="font-weight: bold;"> Print Date</td>
                                        <td>:</td>
                                        <td>{{\Carbon\Carbon::now()->format('d-m-Y g:i A')}}</td>
                                    {{-- </tr> --}}
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class="row" style=" margin: 15px 0px;">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered " border="1pt solid ash" style="border-collapse: collapse; color:black;" id="accCashFlowTable">
                                <thead>
                                <tr>
                                    <th>Particular</th>
                                    @if($searchedSearchMethod==1)
                                        <th>
                                            FI Year <br/> {{"(".$previousFiYearName.")"}}
                                        </th>
                                        <th>
                                            FI Year <br/> {{"(".$currentFiYearName.")"}}
                                        </th>
                                    @elseif($searchedSearchMethod==2)
                                        <th style="width: 165px">
                                            This Year <br/> {{"( $startDate To $endDate)"}}
                                        </th>
                                    @endif
                                </tr>
                                </thead>

{{-- *********************************************************************************************************************** --}}
{{-- *************************************************Search By Fiscal Year************************************************* --}}
                                @if($searchedSearchMethod==1)
<?php
// ====================================**********Starts Opening Balance**********====================================
    $balanceAmountOfFirstPreFY = DB::table('acc_opening_balance')->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('fiscalYearId', $firstPreFYId)->whereIn('ledgerId',$capitalFundLedgerIds)->sum('balanceAmount');

    $balanceAmountOfSecondPreFY = DB::table('acc_opening_balance')->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('fiscalYearId', $secondPreFYId)->whereIn('ledgerId',$capitalFundLedgerIds)->sum('balanceAmount');
// ====================================**********End Opening Balance**********====================================

// ====================================**********Starts Vouchers**********====================================
    $voucherIdsOfCuFY = DB::table('acc_voucher')
        ->whereIn('projectId',$projectIdArray)
        ->whereIn('projectTypeId',$projectTypeIdArray)
        ->whereIn('branchId',$branchIdArray)
        ->where(function ($query) use ($startDate, $endDate){
            $query->where('voucherDate','>=', $startDate)
            ->where('voucherDate','<=', $endDate);
        })->pluck('id')->toArray();

    $voucherIdsOfPreFY = DB::table('acc_voucher')
        ->whereIn('projectId',$projectIdArray)
        ->whereIn('projectTypeId',$projectTypeIdArray)
        ->whereIn('branchId',$branchIdArray)
        ->where(function ($query) use ($firstPreFYStartDate, $firstPreFYEndDate){
            $query->where('voucherDate','>=', $firstPreFYStartDate)
            ->where('voucherDate','<=', $firstPreFYEndDate);
        })->pluck('id')->toArray();

    $voucherIdsOnlyJVOfCuFY = DB::table('acc_voucher')
        ->whereIn('projectId',$projectIdArray)
        ->whereIn('projectTypeId',$projectTypeIdArray)
        ->whereIn('branchId',$branchIdArray)
        ->where('voucherTypeId', 3)
        ->where(function ($query) use ($startDate, $endDate){
            $query->where('voucherDate','>=', $startDate)
            ->where('voucherDate','<=', $endDate);
        })->pluck('id')->toArray();

    $voucherIdsOnlyJVOfPreFY = DB::table('acc_voucher')
        ->whereIn('projectId',$projectIdArray)
        ->whereIn('projectTypeId',$projectTypeIdArray)
        ->whereIn('branchId',$branchIdArray)
        ->where('voucherTypeId', 3)
        ->where(function ($query) use ($firstPreFYStartDate, $firstPreFYEndDate){
            $query->where('voucherDate','>=', $firstPreFYStartDate)
            ->where('voucherDate','<=', $firstPreFYEndDate);
        })->pluck('id')->toArray();

    $totalSurplusOfCuFY=totalSurplusCal($incomeLedgerIds, $expenseLedgerIds, $voucherIdsOfCuFY);


    $retainedEarningLedgerIds = DB::table('acc_account_ledger')->where('accountTypeId', 10)->where('isGroupHead', 0)->pluck('id')->toArray();
    $totalSurplusOfPreFY = DB::table('acc_opening_balance')->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('fiscalYearId', $firstPreFYId)->whereIn('ledgerId',$retainedEarningLedgerIds)->sum('balanceAmount');

// ====================================**********End Vouchers**********====================================

?>
                                    <tbody>
{{--
|==================================================================================
| 1. Cash flows from operating activies
|==================================================================================
 --}}
                                    <tr id="cashFlowsFromOA" style="font-weight: bold;">
                                        <td style="text-align:left;" colspan="3">A. Cash flows from operating activies</td>
                                        {{-- <td class="preFiYearColumn" amount=""></td>
                                        <td class="curFiYearColumn" amount=""></td> --}}
                                    </tr>
    {{-- 2=====Surplus for the period================================================== --}}
                                    <tr id="surplusForThePeriod" class="">
                                        <td>Surplus for the period</td>
                                        <td class="preFiYearColumn" amount="{{$totalSurplusOfPreFY}}" >{{roundUpPHP($totalSurplusOfPreFY, $searchedRoundUp)}}</td>
                                        <td class="curFiYearColumn" amount="{{$totalSurplusOfCuFY}}" >{{roundUpPHP($totalSurplusOfCuFY, $searchedRoundUp)}}</td>
                                    </tr>
    {{-- 3=====Add: Amount considered as non cash items Expenses================================ --}}
    {{-- =====Expense(Dr-Cr)======================================= --}}
                                    <tr id="" style="font-weight: bold;">
                                        <td style="text-align:left;" colspan="3" >Add: Amount considered as non cash items Expenses</td>
                                        {{-- <td class="preFiYearColumn" amount=""></td>
                                        <td class="curFiYearColumn" amount=""></td> --}}
                                    </tr>
    {{-- 4=====Expenses for Provision & Reserve========================================= --}}
                                    <tr id="">
                                        <td>Expenses for Provision & Reserve</td>
                                        <td class="preFiYearColumn" amount=""></td>
                                        <td class="curFiYearColumn" amount=""></td>
                                    </tr>

    {{--5=====Depreciation  for the year========================================= --}}
                                    <tr id="">
                                        <td>Depreciation  for the year</td>
                                        <td class="preFiYearColumn" amount=""></td>
                                        <td class="curFiYearColumn" amount=""></td>
                                    </tr>

    {{-- 6=====Non cash staff salary & benefits========================================= --}}
                                    <tr id="">
                                        <td>Non cash staff salary & benefits</td>
                                        <td class="preFiYearColumn" amount=""></td>
                                        <td class="curFiYearColumn" amount=""></td>
                                    </tr>
    {{--7=====Non cash General & Administrative Expenses========================================= --}}
                                    <tr id="">
                                        <td>Non cash General & Administrative Expenses</td>
                                        <td class="preFiYearColumn" amount=""></td>
                                        <td class="curFiYearColumn" amount=""></td>
                                    </tr>

    {{-- 8=====Non cash Financial cost========================================= --}}
                                    <tr id="">
                                        <td>Non cash Financial cost</td>
                                        <td class="preFiYearColumn" amount=""></td>
                                        <td class="curFiYearColumn" amount=""></td>
                                    </tr>

    {{-- 9=====Non cash program cost========================================= --}}
                                    <tr id="">
                                        <td>Non cash program cost</td>
                                        <td class="preFiYearColumn" amount=""></td>
                                        <td class="curFiYearColumn" amount=""></td>
                                    </tr>

    {{-- 10=====Sub-total of non cash items expenses================================ --}}
                                    <tr id="subTotalOfExpenses" class="subTotal" style="font-weight: bold;">
                                        <td>Sub-total of non cash items expenses</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>
    {{-- 11=======Less: Amount considered as non cash items Income================================= --}}
    {{-- =====Income (Cr-Dr)======================================= --}}
                                    <tr id="lessAmountConsideredIncome" style="font-weight: bold;">
                                        <td style="text-align:left;" colspan="3" >Less: Amount considered as non cash items Income</td>
                                        {{-- <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td> --}}
                                    </tr>

    {{-- 12=====Non cash FDR & revenue Income( Income all 3rd level)======================================= --}}
                                    <tr id="">
                                        <td>Non cash FDR & revenue Income( Income all 3rd level)</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>
                                    <?php
                                        // $allincomeLedgerIds = DB::table('acc_account_ledger')->whereIn('id',$ledgerMatchedId)->where('accountTypeId', 12)->pluck('id')->toArray();
                                        // $dataArray["stage"] = "lessAmountConsideredIncome";
                                        // structure($allincomeLedgerIds, $dataArray);
                                    ?>
    {{-- 13======Sub-total of non cash items income======================================= --}}
                                    <tr id="subTotalOfIncome" class="subTotal" style="font-weight: bold;">
                                        <td>Sub-total of non cash items income</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>

    {{-- 14====Increase/decrease in disbursement to members========================= --}}
                                    <tr id="disbursementToMembers" class="">
                                        <td>Increase/decrease in disbursement to members</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>
<?php
    // $firstParentId = DB::table('acc_account_ledger')->whereIn('id',$ledgerMatchedId)->where('code', 14000)->value('id');
    // $secondParentId = DB::table('acc_account_ledger')->whereIn('id',$ledgerMatchedId)->where('parentId', $firstParentId)->pluck('id')->toArray();
    // // var_dump($loanOutstandingId);
    // $disMembersIds = DB::table('acc_account_ledger')->whereIn('id',$ledgerMatchedId)->whereIn('parentId', $secondParentId)->pluck('id')->toArray();
    // var_dump($disMembersIds);
    // $disMembersIdsArray=array_unique(trackLevels($disMembersIds));
    // echo "<br/>disMembersIdsArray: "; var_dump($disMembersIdsArray);

    // $dataArray["stage"] = "disbursementToMembers";
    // structure($disMembersIdsArray, $dataArray);
    // thirdParentToFifthChild();
?>
    {{-- 15====Increase/decrease in payment for provision========================= --}}
                                    <tr class="">
                                        <td>Increase/decrease in payment for provision</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>
    {{-- 16====Increase/decrease in loan, advance & prepaid======================== --}}
                                    <tr id="loanNAdvance" class="">
                                        <td>Increase/decrease in loan, advance & prepaid</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>
<?php
    // $firstParentId = DB::table('acc_account_ledger')->whereIn('id',$ledgerMatchedId)->where('code', 16000)->value('id');
    // $secondParentId = DB::table('acc_account_ledger')->whereIn('id',$ledgerMatchedId)->where('parentId', $firstParentId)->pluck('id')->toArray();
    // // var_dump($loanOutstandingId);
    // $loanAdvanceIds = DB::table('acc_account_ledger')->whereIn('id',$ledgerMatchedId)->whereIn('parentId', $secondParentId)->pluck('id')->toArray();
    // var_dump($loanAdvanceIds);
    // $loanAdvanceIdsArray=array_unique(trackLevels($loanAdvanceIds));
    // echo "<br/>loanAdvanceIdsArray: "; var_dump($loanAdvanceIdsArray);

    // $dataArray["stage"] = "loanNAdvance";
    // structure($loanAdvanceIdsArray, $dataArray);
?>
    {{-- 17======Net cash used in operating activities======================================= --}}
                                    <tr id="netCashUsedInOA" class="subTotal" style="font-weight: bold;">
                                        <td>Net cash used in operating activities</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>

    {{-- 18====B. Cash flows from Investing Activies==================================== --}}
    {{-- ======Current & Non-Current Asset(Dr-Cr)======================================= --}}
                                    <tr id="cashFlowsFromIA" class="" style="font-weight: bold;">
                                        <td style="text-align:left;" colspan="3" >B. Cash flows from Investing Activies</td>
                                        {{-- <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td> --}}
                                    </tr>
                                    <?php

                                        // $dataArray["voucherIdsOfCuFY"] = $voucherIdsOfCuFY;
                                        // $dataArray["voucherIdsOfPreFY"] = $voucherIdsOfPreFY;
                                        // $currentNnonCurrentAssetLedgerIds = DB::table('acc_account_ledger')->whereIn('id',$ledgerMatchedId)->whereNotIn('id',[285,348])->whereIn('accountTypeId', [1,2,3])->pluck('id')->toArray();
                                        // $dataArray["stage"] = "cashFlowsFromIA";
                                        // structure($currentNnonCurrentAssetLedgerIds, $dataArray);
                                    ?>

    {{-- 19======Increase/decrease in acquisition of property, plant and equipment======================================= --}}
                                    <tr id="" class="">
                                        <td>Increase/decrease in acquisition of property, plant and equipment</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>

    {{-- 20======Increase/decrease in short term investment======================================= --}}
                                    <tr id="" class="">
                                        <td>Increase/decrease in short term investment</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>

    {{-- 21======Increase/decrease in others current assets======================================= --}}
                                    <tr id="" class="">
                                        <td>Increase/decrease in others current assets</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>

    {{-- 22====Net Cash Used in Investing Activies============================ --}}
                                    <tr id="netCashUsedIA" class="subTotal" style="font-weight: bold;">
                                        <td>Net Cash Used in Investing Activies</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>
    {{-- 23=====C. Cash flows from Financing Activies=================================================== --}}
    {{-- =====Non-Current Liability & Equity (Cr-Dr)======================================= --}}
                                    <tr id="CashFlowsFromFA" class="" style="font-weight: bold;">
                                        <td style="text-align:left;" colspan="3" >C. Cash flows from Financing Activies</td>
                                        {{-- <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td> --}}
                                    </tr>

                                    <?php
                                        // $nonCurrentLiabNEquityLedgerIds = DB::table('acc_account_ledger')->whereIn('id',$ledgerMatchedId)->whereIn('accountTypeId', [6,8,9,10,11])->pluck('id')->toArray();
                                        // $dataArray["stage"] = "CashFlowsFromFA";
                                        // structure($nonCurrentLiabNEquityLedgerIds, $dataArray);
                                    ?>

    {{-- 24======Increase/decrease in long term borrowings-PKSF======================================= --}}
                                    <tr id="" class="">
                                        <td>Increase/decrease in long term borrowings-PKSF</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>

    {{-- 25======Increase/decrease in  long term borrowings-Non PKSF======================================= --}}
                                    <tr id="" class="">
                                        <td>Increase/decrease in  long term borrowings-Non PKSF</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>

    {{-- 26======Increase/decrease in members savings======================================= --}}
                                    <tr id="" class="">
                                        <td>Increase/decrease in members savings</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>

    {{-- 27======Increase/decrease in short term loan & others current liabilities======================================= --}}
                                    <tr id="" class="">
                                        <td>Increase/decrease in short term loan & others current liabilities</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>

    {{-- 28======Increase/decrease in staff EP, EG, EW, & ES fund======================================= --}}
                                    <tr id="" class="">
                                        <td>Increase/decrease in staff EP, EG, EW, & ES fund</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>

    {{-- 29======Increase/decrease in Development Program Fund======================================= --}}
                                    <tr id="" class="">
                                        <td>Increase/decrease in Development Program Fund</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>

    {{-- 30=============================Net Cash Used in Financing Activies====================================== --}}
                                    <tr id="netCashUsedFA" class="subTotal" style="font-weight: bold;">
                                        <td>Net Cash Used in Financing Activies</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>
    {{-- 31==============================D. Net Cash increase/Decrease (A+B+C)==================================== --}}
                                    <tr id="netCashUsedABC" style="font-weight: bold;">
                                        <td>D. Net Cash increase/Decrease (A+B+C)</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>
    {{-- 32=================================Add: Cash and Banck Balance Beginning of the Year================================= --}}
<?php
$bankNcashLedgerIds = DB::table('acc_account_ledger')->whereIn('id',$ledgerMatchedId)->whereIn('accountTypeId', [4,5])->where('isGroupHead',0)->pluck('id')->toArray();
// echo "bankNcashLedgerIds: "; var_dump($bankNcashLedgerIds);
// ====================================**********Starts Opening Balance**********====================================
    $balanceAmountOfBankNCashofFirstPreFY = DB::table('acc_opening_balance')->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('fiscalYearId', $firstPreFYId)->whereIn('ledgerId',$bankNcashLedgerIds)->sum('balanceAmount');

    $balanceAmountOfBankNCashofSecondPreFY = DB::table('acc_opening_balance')->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('fiscalYearId', $secondPreFYId)->whereIn('ledgerId',$bankNcashLedgerIds)->sum('balanceAmount');
// ====================================**********End Opening Balance**********====================================

?>
                                    <tr id="CashBankBeginningOfTheYr">
                                        <td>Add: Cash and Bank Balance Beginning of the Year</td>
                                        <td class="preFiYearColumn" amount="{{$balanceAmountOfBankNCashofSecondPreFY}}" >{{roundUpPHP($balanceAmountOfBankNCashofSecondPreFY, $searchedRoundUp)}}</td>
                                        <td class="curFiYearColumn" amount="{{$balanceAmountOfBankNCashofFirstPreFY}}" >{{roundUpPHP($balanceAmountOfBankNCashofFirstPreFY, $searchedRoundUp)}}</td>
                                    </tr>
    {{-- 33================================Cash and Bank Balance end of the Year======================================== --}}
                                    <tr id="CashBankEndOfTheYr" style="font-weight: bold;">
                                        <td>Cash and Bank Balance end of the Year</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>
                                    </tbody>

{{-- ************************************************************************************************************************ --}}
{{-- *************************************************Search By Current Year************************************************* --}}

                                @elseif($searchedSearchMethod==2)
<?php
// ====================================**********Starts Vouchers**********====================================
    $voucherIdsOfCuYr = DB::table('acc_voucher')
        ->whereIn('projectId',$projectIdArray)
        ->whereIn('projectTypeId',$projectTypeIdArray)
        ->whereIn('branchId',$branchIdArray)
        ->where(function ($query) use ($startDate, $endDate){
            $query->where('voucherDate','>=', $startDate)
            ->where('voucherDate','<=', $endDate);
        })->pluck('id')->toArray();
    $voucherIdsOnlyJVOfCuYr = DB::table('acc_voucher')
        ->whereIn('projectId',$projectIdArray)
        ->whereIn('projectTypeId',$projectTypeIdArray)
        ->whereIn('branchId',$branchIdArray)
        ->where('voucherTypeId', 3)
        ->where(function ($query) use ($startDate, $endDate){
            $query->where('voucherDate','>=', $startDate)
            ->where('voucherDate','<=', $endDate);
        })->pluck('id')->toArray();
// ====================================**********End Vouchers**********====================================
    $totalSurplusOfCuYr=totalSurplusCal($incomeLedgerIds, $expenseLedgerIds, $voucherIdsOfCuYr);

?>

                                <tbody>
{{--
|==================================================================================
| 1. Cash flows from operating activies
|================================================================================== --}}
                                    <tr id="cashFlowsFromOA" style="font-weight: bold;">
                                        <td style="text-align:left;" colspan="2">A. Cash flows from operating activies</td>
                                        {{-- <td class="thisYearColumn" amount=""></td> --}}
                                    </tr>
    {{-- 2===============================Surplus for the period================================================== --}}
                                    <tr id="surplusForThePeriod" >
                                        <td>Surplus for the period</td>
                                        <td class="thisYearColumn" amount="{{$totalSurplusOfCuYr}}" >{{roundUpPHP($totalSurplusOfCuYr, $searchedRoundUp)}}</td>
                                    </tr>
    {{-- 3================================Add: Amount considered as non cash items Expenses================================ --}}
    {{-- =======================================Expense(Dr-Cr)======================================= --}}
                                    <tr id="addAmountConsideredExpenses" style="font-weight: bold;">
                                        <td style="text-align:left;" colspan="2" >Add: Amount considered as non cash items Expenses</td>
                                        {{-- <td class="thisYearColumn" amount=""></td> --}}
                                    </tr>
                                    <?php
                                        $allExpenseLedgerIds = DB::table('acc_account_ledger')->whereIn('id',$ledgerMatchedId)->where('accountTypeId', 13)->pluck('id')->toArray();
                                        $dataArray["voucherIdsOfCuYr"] = $voucherIdsOnlyJVOfCuYr;
                                        $dataArray["stage"] = "addAmountConsideredExpenses";
                                        structure($allExpenseLedgerIds, $dataArray);
                                    ?>
    {{-- 4================================Sub-total of non cash items expenses================================ --}}
                                    <tr id="subTotalOfExpenses" class="subTotal" style="font-weight: bold;">
                                        <td>Sub-total of non cash items expenses</td>
                                        <td class="thisYearColumn" amount="" ></td>
                                    </tr>
    {{-- 5=================================Less: Amount considered as non cash items Income================================= --}}
    {{-- =======================================Income (Cr-Dr)======================================= --}}
                                    <tr id="lessAmountConsideredIncome" style="font-weight: bold;">
                                        <td style="text-align:left;" colspan="2" >Less: Amount considered as non cash items Income</td>
                                        {{-- <td class="thisYearColumn" amount="" ></td> --}}
                                    </tr>
                                    <?php
                                        $allincomeLedgerIds = DB::table('acc_account_ledger')->whereIn('id',$ledgerMatchedId)->where('accountTypeId', 12)->pluck('id')->toArray();
                                        $dataArray["stage"] = "lessAmountConsideredIncome";
                                        structure($allincomeLedgerIds, $dataArray);
                                    ?>
    {{-- 6=======================================Sub-total of non cash items income======================================= --}}
                                    <tr id="subTotalOfIncome" class="subTotal" style="font-weight: bold;">
                                        <td>Sub-total of non cash items income</td>
                                        <td class="thisYearColumn" amount="" ></td>
                                    </tr>
                                    <tr><td colspan="2"> </td></tr>
                                    <tr><td colspan="2"> </td></tr>
                                    <tr><td colspan="2"> </td></tr>

    {{-- 7=======================================Net cash used in operating activities======================================= --}}
                                    <tr id="netCashUsedInOA" class="subTotal" style="font-weight: bold;">
                                        <td>Net cash used in operating activities</td>
                                        <td class="thisYearColumn" amount="" ></td>
                                    </tr>

    {{-- 8=====================================B. Cash flows from Investing Activies==================================== --}}
    {{-- =======================================Current & Non-Current Asset(Dr-Cr)======================================= --}}
                                    <tr id="cashFlowsFromIA" class="" style="font-weight: bold;">
                                        <td style="text-align:left;" colspan="2" >B. Cash flows from Investing Activies</td>
                                        {{-- <td class="thisYearColumn" amount="" ></td> --}}
                                    </tr>
                                    <?php
                                        $currentNnonCurrentAssetLedgerIds = DB::table('acc_account_ledger')->whereIn('id',$ledgerMatchedId)->whereNotIn('id',[285,348])->whereIn('accountTypeId', [1,2,3])->pluck('id')->toArray();
                                        $dataArray["voucherIdsOfCuYr"] = $voucherIdsOfCuYr;
                                        $dataArray["stage"] = "cashFlowsFromIA";
                                        structure($currentNnonCurrentAssetLedgerIds, $dataArray);
                                    ?>
    {{-- 9============================Net Cash Used in Investing Activies============================ --}}
                                    <tr id="netCashUsedIA" class="subTotal" style="font-weight: bold;">
                                        <td>Net Cash Used in Investing Activies</td>
                                        <td class="thisYearColumn" amount="" ></td>
                                    </tr>
    {{-- 10============================C. Cash flows from Financing Activies=================================================== --}}
    {{-- =======================================Non-Current Liability & Equity (Cr-Dr)======================================= --}}
                                    <tr id="CashFlowsFromFA" class="" style="font-weight: bold;">
                                        <td style="text-align:left;" colspan="2" >C. Cash flows from Financing Activies</td>
                                        {{-- <td class="thisYearColumn" amount="" ></td>--}}
                                    </tr>
                                    <?php
                                        $nonCurrentLiabNEquityLedgerIds = DB::table('acc_account_ledger')->whereIn('id',$ledgerMatchedId)->whereIn('accountTypeId', [6,8,9,10,11])->pluck('id')->toArray();
                                        $dataArray["stage"] = "CashFlowsFromFA";
                                        structure($nonCurrentLiabNEquityLedgerIds, $dataArray);
                                    ?>
    {{-- 11=============================Net Cash Used in Financing Activies====================================== --}}
                                    <tr id="netCashUsedFA" class="subTotal" style="font-weight: bold;">
                                        <td>Net Cash Used in Financing Activies</td>
                                        <td class="thisYearColumn" amount="" ></td>
                                    </tr>
    {{-- 12==============================D. Net Cash increase/Decrease (A+B+C)==================================== --}}
                                    <tr id="netCashUsedABC" style="font-weight: bold;">
                                        <td>D. Net Cash increase/Decrease (A+B+C)</td>
                                        <td class="thisYearColumn" amount="" ></td>
                                    </tr>
    {{-- 13=================================Add: Cash and Banck Balance Beginning of the Year================================= --}}
<?php
$bankNcashLedgerIds = DB::table('acc_account_ledger')->whereIn('id',$ledgerMatchedId)->whereIn('accountTypeId', [4,5])->where('isGroupHead',0)->pluck('id')->toArray();
// echo "bankNcashLedgerIds: "; var_dump($bankNcashLedgerIds);
// ====================================**********Starts Opening Balance**********====================================
    $balanceAmountOfBankNCashofFirstPreFY = DB::table('acc_opening_balance')->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('fiscalYearId', $firstPreFYId)->whereIn('ledgerId',$bankNcashLedgerIds)->sum('balanceAmount');
// ====================================**********End Opening Balance**********====================================

?>
                                    <tr id="CashBankBeginningOfTheYr">
                                        <td>Add: Cash and Bank Balance Beginning of the Year</td>
                                        <td class="thisYearColumn" amount="{{$balanceAmountOfBankNCashofFirstPreFY}}" >{{roundUpPHP($balanceAmountOfBankNCashofFirstPreFY, $searchedRoundUp)}}</td>
                                    </tr>
    {{-- 14================================Cash and Bank Balance end of the Year======================================== --}}
                                    <tr id="CashBankEndOfTheYr" style="font-weight: bold;">
                                        <td>Cash and Bank Balance end of the Year</td>
                                        <td class="thisYearColumn" amount="" ></td>
                                    </tr>
                                    </tbody>



                                @endif

                            </table>
                        </div>  {{-- TableResponsiveDiv --}}
                        <?php } ?>
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
                var d = new Date();
                var year = d.getFullYear();
                var month = d.getMonth();
                if (month<=5) {
                    year--;
                    month = 6;
                }
                else{
                    month = 6;
                }
                d.setFullYear(year, month, 1);

                $("#dateFrom").datepicker("option","minDate",new Date(d));
                $("#dateTo").datepicker("option","minDate",new Date(d));

                $("#dateFrom").datepicker("setDate", new Date(d));
                $("#dateFrom").hide();
                $("#dateFrome").hide();

                $("#dateToDiv").attr("class", "col-sm-12");
          }


        });
        // $("#searchMethod").trigger('change');

        var searchedSearchMethod="<?php echo $searchedSearchMethod;?>";
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
        $("#dateFrom").datepicker({
                changeMonth: true,
                changeYear: true,
                yearRange : "2016:c",
                minDate: new Date(2016, 07 - 1, 01),
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
                yearRange : "2016:c",
                maxDate: "dateToday",
                dateFormat: 'dd-mm-yy',
                onSelect: function () {
                    $('#dateToe').hide();
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

            var rowCount = $('#accCashFlowTable tr').length;
            // alert(rowCount);
            var amount1,amount2,amount3,amount4;
            amount1=amount2=amount3=amount4= 0;

            for(i=rowCount-1 ;i>=1;i--){
                if ($("#accCashFlowTable tr").eq(i).attr('level')==4) {
                    var tempAmount4 = parseFloat($("#accCashFlowTable tr").eq(i).find('.'+className).attr('amount'));
                    amount4 = amount4 + tempAmount4;

                }
                else if ($("#accCashFlowTable tr").eq(i).attr('level')==3) {

                    $("#accCashFlowTable tr").eq(i).closest('tr').find('.'+className).attr('amount',amount4);
                    $("#accCashFlowTable tr").eq(i).closest('tr').find('.'+className).html(roundUp(amount4, searchedRoundUp));

                    var tempAmount3 = parseFloat($("#accCashFlowTable tr").eq(i).closest('tr').find('.'+className).attr('amount'));
                    amount3 = amount3 + tempAmount3;
                    amount4 = 0;
                }
                else if ($("#accCashFlowTable tr").eq(i).attr('level')==2) {

                    $("#accCashFlowTable tr").eq(i).closest('tr').find('.'+className).attr('amount',amount3);
                    $("#accCashFlowTable tr").eq(i).closest('tr').find('.'+className).html(roundUp(amount3, searchedRoundUp));

                    var tempAmount2 = parseFloat($("#accCashFlowTable tr").eq(i).closest('tr').find('.'+className).attr('amount'));
                    amount2 = amount2 + tempAmount2;
                    amount3 = 0;
                }
                else if ($("#accCashFlowTable tr").eq(i).attr('level')==1) {

                    $("#accCashFlowTable tr").eq(i).closest('tr').find('.'+className).attr('amount',amount2);
                    $("#accCashFlowTable tr").eq(i).closest('tr').find('.'+className).html(roundUp(amount2, searchedRoundUp));

                    var tempAmount1 = parseFloat($("#accCashFlowTable tr").eq(i).closest('tr').find('.'+className).attr('amount'));
                    amount1 = amount1 + tempAmount1;
                    amount2 = 0;
                }
                else if ($("#accCashFlowTable tr").eq(i).attr('level')==0) {
                    $("#accCashFlowTable tr").eq(i).closest('tr').find('.'+className).attr('amount',amount1);
                    $("#accCashFlowTable tr").eq(i).closest('tr').find('.'+className).html(roundUp(amount1, searchedRoundUp));
                    amount1 = 0;
                    // var tempTotalAmount1 = parseFloat($("#accCashFlowTable tr").eq(i).find('.'+className).attr('amount'));
                    // totalAmount1 = totalAmount1 + tempTotalAmount1;
                }

            }       //For Loop
            var subTotal = 0;
            for(i=1; i<=rowCount; i++){
                if ($("#accCashFlowTable tr").eq(i).attr('level')==0) {
                    var tempSubTotal = parseFloat($("#accCashFlowTable tr").eq(i).closest('tr').find('.'+className).attr('amount'));
                    subTotal = subTotal + tempSubTotal;
                }else if ($("#accCashFlowTable tr").eq(i).attr('class')=="subTotal") {
                    $("#accCashFlowTable tr").eq(i).closest('tr').find('.'+className).attr('amount',subTotal);
                    $("#accCashFlowTable tr").eq(i).closest('tr').find('.'+className).html(roundUp(subTotal, searchedRoundUp));
                    subTotal = 0;
                }
            }       //For Loop


        }           //sumOfEachRow Function
//=======================================End SUM Function====================================================

//===============================Starts totalAmounts Function====================================================
        function totalAmounts(className, searchedRoundUp){
//net Cash Used In (operating+Investing+Financing) Activies Amount===========================
            var netCashUsedInOAAmount, netCashUsedIAAmount, netCashUsedFAAmount, netCashUsedABCAmount;
            netCashUsedInOAAmount=netCashUsedIAAmount=netCashUsedFAAmount=netCashUsedABCAmount=0;

            netCashUsedInOAAmount = parseFloat($("#accCashFlowTable #netCashUsedInOA").closest('tr').find('.'+className).attr('amount'));
            netCashUsedIAAmount = parseFloat($("#accCashFlowTable #netCashUsedIA").closest('tr').find('.'+className).attr('amount'));
            netCashUsedFAAmount = parseFloat($("#accCashFlowTable #netCashUsedFA").closest('tr').find('.'+className).attr('amount'));

            netCashUsedABCAmount=netCashUsedInOAAmount+netCashUsedIAAmount+netCashUsedFAAmount;
            // alert(closingBalanceAmount);
            $("#accCashFlowTable #netCashUsedABC").find('.'+className).attr('amount',netCashUsedABCAmount);
            $("#accCashFlowTable #netCashUsedABC").find('.'+className).html(roundUp(netCashUsedABCAmount, searchedRoundUp));
//CashBankEndOfTheYrAmount===========================
            var CashBankBeginningOfTheYrAmount, CashBankEndOfTheYrAmount;
            netCashUsedABCAmount=CashBankBeginningOfTheYrAmount=CashBankEndOfTheYrAmount=0;

            netCashUsedABCAmount = parseFloat($("#accCashFlowTable #netCashUsedABC").closest('tr').find('.'+className).attr('amount'));
            CashBankBeginningOfTheYrAmount = parseFloat($("#accCashFlowTable #CashBankBeginningOfTheYr").closest('tr').find('.'+className).attr('amount'));
            CashBankEndOfTheYrAmount=netCashUsedABCAmount+CashBankBeginningOfTheYrAmount;
            $("#accCashFlowTable #CashBankEndOfTheYr").find('.'+className).attr('amount',CashBankEndOfTheYrAmount);
            $("#accCashFlowTable #CashBankEndOfTheYr").find('.'+className).html(roundUp(CashBankEndOfTheYrAmount, searchedRoundUp));
        }

//===============================End totalAmounts Function====================================================


        var searchedRoundUp = "{{$searchedRoundUp}}";
        var searchedWithZero = "{{$searchedWithZero}}";
        // alert(searchedWithZero);

        var searchedSearchMethod="{{$searchedSearchMethod}}";
        if (searchedSearchMethod==1) {
            // sumOfEachRow("preFiYearColumn", searchedRoundUp);
            // sumOfEachRow("curFiYearColumn", searchedRoundUp);

            // totalAmounts("preFiYearColumn", searchedRoundUp);
            // totalAmounts("curFiYearColumn", searchedRoundUp);
        }else if(searchedSearchMethod==2){
            // sumOfEachRow("thisYearColumn", searchedRoundUp);

            // totalAmounts("thisYearColumn", searchedRoundUp);
        }
        // var searchedDepthLevel = "{{$searchedDepthLevel}}";
        // alert(searchedDepthLevel);
        // $("#accCashFlowTable tr").each(function(index, value) {
        //     if($(this).attr('level')>=searchedDepthLevel){
        //         $(this).hide();
        //     }
        // });




    });     //document.ready

    $(function(){
        $("#printIcon").click(function(){

            $("#accCashFlowTable").removeClass('table table-striped table-bordered');

            var printStyle = '<style>#accCashFlowTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt solid ash;page-break-inside:auto;} #accCashFlowTable tr:last-child { font-weight: bold;} #accCashFlowTable thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;} #accCashFlowTable tbody tr td { vertical-align: middle;padding:3px ;font-size:10px;} #accCashFlowTable tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{size: auto;margin: 0;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

            var mainContents = document.getElementById("printDiv").innerHTML;
            var printContents = '<div id="order-details-wrapper" style="padding: 30px;">' + printStyle + mainContents+'</div>';

            // var printContents = document.getElementById("printView").innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML ="" + printContents;
            window.print();
            document.body.innerHTML = originalContents;

            location.reload();
        });
    });

</script>

<style>
#accCashFlowTable thead tr th { padding: 0px; }
#accCashFlowTable thead tr th:last-child, #accCashFlowTable thead tr th:nth-child(2) { width: 160px;  }
/*#accCashFlowTable tbody tr { font-size: 12px; }*/
#accCashFlowTable tbody tr td:nth-child(1) { text-align: left; padding-left:5px; }
#accCashFlowTable tbody tr td:last-child, #accCashFlowTable tbody tr td:nth-child(2) { text-align: right; padding-right:5px;  }
#reportingInfoTable{
    color: black;
    font-size: 12px;
    width: 100%;
}
#reportingInfoTable tbody tr td{ text-align: left; }
</style>
