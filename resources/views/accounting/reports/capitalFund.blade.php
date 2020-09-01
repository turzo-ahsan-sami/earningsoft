@extends('layouts/acc_layout')
@section('title', '| Capital Fund')
@section('content')
@php
$branch = DB::table('gnr_branch')->where('id',Auth::user()->branchId)->select('id','branchCode')->first();
$userBranchCode = $branch->branchCode;
@endphp
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
                    case "5":
                    echo str_repeat('&nbsp;',  (7*6));
                    break;
                    case "6":
                    echo str_repeat('&nbsp;',  (7*7));
                    break;
                    default:
                    echo str_repeat('&nbsp;',   (7*8));
                }
                ?>

                <?php
                if($child->isGroupHead==1){
                    // echo '<span style="font-weight: bold">'.strtoupper($child->name)." [".$child->code."]".'</span>';
                    // echo '<span style="font-weight: bold">'.$child->name." [".$child->code."]".'</span>';
                    echo $child->name." [".$child->code."]";
                }else{
                    // echo '<span style="font-weight: normal">'.$child->name." [".$child->code."]".'</span>';
                    // echo '<span style="font-weight: normal">'.$child->id."-".$child->name." [".$child->code."]".'</span>';
                    echo $child->name." [".$child->code."]";
                    // echo $child->id."-".$child->name." [".$child->code."]";

                    // var_dump($dataArray["stage"]);
                }
                ?>

            </td>

            <?php
            $searchedRoundUp=$dataArray["searchedRoundUp"];
            ?>

            @if($dataArray["searchedSearchMethod"]==1)
            <?php
            $voucherIdsOfCuFY=$dataArray["voucherIdsOfCuFY"];
            $voucherIdsOfPreFY=$dataArray["voucherIdsOfPreFY"];

            //array chunk
            if (count($voucherIdsOfCuFY) > 50000) {
                $voucherIdsOfCuFY = array_chunk($voucherIdsOfCuFY, 50000);
            }
            if (count($voucherIdsOfPreFY) > 50000) {
                $voucherIdsOfPreFY = array_chunk($voucherIdsOfPreFY, 50000);
            }

            if($dataArray["stage"] == "addAdjustment"){
                $amountOfCuFY = DB::table('acc_voucher_details')
                ->whereIn('voucherId', $voucherIdsOfCuFY)
                ->where('debitAcc', $child->id)
                ->sum('amount');
                $amountOfPreFY = DB::table('acc_voucher_details')
                ->whereIn('voucherId', $voucherIdsOfPreFY)
                ->where('debitAcc', $child->id)
                ->sum('amount');
            }elseif($dataArray["stage"] == "lessAdjustment"){
                $amountOfCuFY = DB::table('acc_voucher_details')
                ->whereIn('voucherId', $voucherIdsOfCuFY)
                ->where('creditAcc', $child->id)
                ->sum('amount');
                $amountOfPreFY = DB::table('acc_voucher_details')
                ->whereIn('voucherId', $voucherIdsOfPreFY)
                ->where('creditAcc', $child->id)
                ->sum('amount');
            }elseif($dataArray["stage"] == "lessTransferrend"){
                $amountOfCuFY = DB::table('acc_voucher_details')
                ->whereIn('voucherId', $voucherIdsOfCuFY)
                ->where('debitAcc', $child->id)
                ->sum('amount');
                $amountOfPreFY = DB::table('acc_voucher_details')
                ->whereIn('voucherId', $voucherIdsOfPreFY)
                ->where('debitAcc', $child->id)
                ->sum('amount');
            }elseif($dataArray["stage"] == "addReserve"){
                $amountOfCuFY = DB::table('acc_voucher_details')
                ->whereIn('voucherId', $voucherIdsOfCuFY)
                ->where('creditAcc', $child->id)
                ->sum('amount');
                $amountOfPreFY = DB::table('acc_voucher_details')
                ->whereIn('voucherId', $voucherIdsOfPreFY)
                ->where('creditAcc', $child->id)
                ->sum('amount');
            }
            ?>
            <td class="preFiYearColumn" amount="{{$amountOfPreFY}}" >{{roundUpPHP($amountOfPreFY, $searchedRoundUp)}}</td>
            <td class="curFiYearColumn" amount="{{$amountOfCuFY}}" >{{roundUpPHP($amountOfCuFY, $searchedRoundUp)}}</td>

            @elseif($dataArray["searchedSearchMethod"]==2)
            <?php
            $voucherIdsOfCuYr=$dataArray["voucherIdsOfCuYr"];
            //array chunk
            if (count($voucherIdsOfCuYr) > 50000) {
                $voucherIdsOfCuYr = array_chunk($voucherIdsOfCuYr, 50000);
            }

            if($dataArray["stage"] == "addAdjustment"){
                $amountOfCuYr = DB::table('acc_voucher_details')
                ->whereIn('voucherId', $voucherIdsOfCuYr)
                ->where('debitAcc', $child->id)
                ->sum('amount');
            }elseif($dataArray["stage"] == "lessAdjustment"){
                $amountOfCuYr = DB::table('acc_voucher_details')
                ->whereIn('voucherId', $voucherIdsOfCuYr)
                ->where('creditAcc', $child->id)
                ->sum('amount');
            }elseif($dataArray["stage"] == "lessTransferrend"){
                $amountOfCuYr = DB::table('acc_voucher_details')
                ->whereIn('voucherId', $voucherIdsOfCuYr)
                ->where('debitAcc', $child->id)
                ->sum('amount');
            }elseif($dataArray["stage"] == "addReserve"){
                $amountOfCuYr = DB::table('acc_voucher_details')
                ->whereIn('voucherId', $voucherIdsOfCuYr)
                ->where('creditAcc', $child->id)
                ->sum('amount');
            }
            ?>

            <td class="thisYearColumn" amount="{{$amountOfCuYr}}" >{{roundUpPHP($amountOfCuYr, $searchedRoundUp)}}</td>


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
                   <!--  <button id="printIcon" class="btn btn-info pull-right"  target="_blank" style="font-size: 14px; margin-top: 10px; border: 3px solid #525659; border-radius: 25px; padding: ">
                        <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
                    </button> -->

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
                <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Capital Fund</h3>
            </div>

            <div class="panel-body panelBodyView">

                <!-- Filtering Start-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">

                            {!! Form::open(array('url' => 'capitalFund/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'ladgerReportForm', 'method'=>'get')) !!}

                            @if($userBranchCode==0)
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

                            @if($userBranchCode==0)
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

                            @if($userBranchCode==0)
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

                        @if($userBranchCode!=0)<div class="col-md-3"></div> @endif
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
                    if ($userBranchCode == 0) {
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
                    if ($userBranchCode == 0) {
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
                    if ($userBranchCode == 0) {
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

                // echo "<br/>projectIdArray: ";
                // var_dump($projectIdArray);
                // echo "<br/>projectTypeIdArray: ";
                // var_dump($projectTypeIdArray);
                // echo "<br/>branchIdArray: ";
                // var_dump($branchIdArray);

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
                }
                //==========================Search By Current Year=================================
                elseif ($searchedSearchMethod==2){
                    $startDate = date('Y-m-d',strtotime($searchedDateFrom));
                    $endDate = date('Y-m-d',strtotime($searchedDateTo));
                    $currentFiscalYear=DB::table('gnr_fiscal_year')->where('fyStartDate','<=', date('Y-m-d'))->where('fyEndDate','>=', date('Y-m-d'))->select('id','fyStartDate','fyEndDate')->first();

                    $currentEndDay=$currentFiscalYear->fyEndDate;

                    $firstPreFYStartDate=date('Y-m-d', strtotime("first day of last year".$startDate));
                    $firstPreFYEndDate=date('Y-m-d', strtotime("last day of -1 year".$currentEndDay));

                    //for Test
                    // $firstPreFYId=DB::table('gnr_fiscal_year')->where('fyStartDate',$firstPreFYStartDate)->value('id');

                    $firstPreFYId=DB::table('gnr_fiscal_year')->where('fyStartDate',$firstPreFYStartDate)->where('fyEndDate',$firstPreFYEndDate)->value('id');
                }


                $dataArray= ['projectIdArray' => $projectIdArray, 'projectTypeIdArray' => $projectTypeIdArray, 'branchIdArray' => $branchIdArray, 'searchedRoundUp' => $searchedRoundUp, 'searchedSearchMethod' => $searchedSearchMethod];
                // print("<pre>");
                // print_r($dataArray);
                // print("</pre>");

                // var_dump($dataArray['projectTypeIdArray']);

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

                    if (count($voucherIds) > 50000) {
                        $voucherIds = array_chunk($voucherIds, 50000);
                    }
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

                ?>


                <div id="printDiv">
                    <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}
                        <?php
                        $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
                        ?>
                        <span style="font-size:14px;">{{$company->name}}</span><br/>
                        <span style="font-size:11px;">{{$company->address}}</span><br/>
                        <span style="text-decoration: underline;  font-size:14px;">Statement of Changes in Capital Fund</span>
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
                            <table class="table table-striped table-bordered " border="1pt solid ash" style="border-collapse: collapse; color:black;" id="accCapitalFundTable">
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
                                $balanceAmountOfFirstPreFY = DB::table($acc_opening_balance)->where('companyIdFk', $user_company_id)->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('fiscalYearId', $firstPreFYId)->whereIn('ledgerId',$capitalFundLedgerIds)->sum('balanceAmount');

                                $balanceAmountOfSecondPreFY = DB::table($acc_opening_balance)->where('companyIdFk', $user_company_id)->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('fiscalYearId', $secondPreFYId)->whereIn('ledgerId',$capitalFundLedgerIds)->sum('balanceAmount');
// ====================================**********End Opening Balance**********====================================

// ====================================**********Starts Vouchers**********====================================
                                $voucherIdsOfCuFY = DB::table($acc_voucher)
                                ->where('companyId', $user_company_id)
                                ->whereIn('projectId',$projectIdArray)
                                ->whereIn('projectTypeId',$projectTypeIdArray)
                                ->whereIn('branchId',$branchIdArray)
                                ->where(function ($query) use ($startDate, $endDate){
                                    $query->where('voucherDate','>=', $startDate)
                                    ->where('voucherDate','<=', $endDate);
                                })->pluck('id')->toArray();

                                $voucherIdsOfPreFY = DB::table($acc_voucher)
                                ->where('companyId', $user_company_id)
                                ->whereIn('projectId',$projectIdArray)
                                ->whereIn('projectTypeId',$projectTypeIdArray)
                                ->whereIn('branchId',$branchIdArray)
                                ->where(function ($query) use ($firstPreFYStartDate, $firstPreFYEndDate){
                                    $query->where('voucherDate','>=', $firstPreFYStartDate)
                                    ->where('voucherDate','<=', $firstPreFYEndDate);
                                })->pluck('id')->toArray();

                                $voucherIdsOfTwoFYs = DB::table($acc_voucher)
                                ->where('companyId', $user_company_id)
                                ->whereIn('projectId',$projectIdArray)
                                ->whereIn('projectTypeId',$projectTypeIdArray)
                                ->whereIn('branchId',$branchIdArray)
                                ->where(function ($query) use ($firstPreFYStartDate, $endDate){
                                    $query->where('voucherDate','>=', $firstPreFYStartDate)
                                    ->where('voucherDate','<=', $endDate);
                                })->pluck('id')->toArray();

                                $totalSurplusOfCuFY=totalSurplusCal($incomeLedgerIds, $expenseLedgerIds, $voucherIdsOfCuFY);

                                $dataArray["voucherIdsOfCuFY"] = $voucherIdsOfCuFY;
                                $dataArray["voucherIdsOfPreFY"] = $voucherIdsOfPreFY;

                                $retainedEarningLedgerIds = DB::table('acc_account_ledger')->where('accountTypeId', 10)->where('isGroupHead', 0)->pluck('id')->toArray();
                                $totalSurplusOfPreFY = DB::table($acc_opening_balance)->where('companyIdFk', $user_company_id)->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('fiscalYearId', $firstPreFYId)->whereIn('ledgerId',$retainedEarningLedgerIds)->sum('balanceAmount');

// ====================================**********End Vouchers**********====================================

//=======================================Starts Add: Adjustment during the Year========================================
// =======Starts Retained Surplus Credit, All Others Debit(Display Others)=========

                                if (count($voucherIdsOfTwoFYs) > 50000) {
                                    $voucherIdsOfTwoFYs = array_chunk($voucherIdsOfTwoFYs, 50000);
                                }

                                $reSurplusCrOppositeIds = DB::table('acc_voucher_details')->whereIn('voucherId',$voucherIdsOfTwoFYs)->whereIn('creditAcc',$retainedSurplusLedgerIds)->pluck('debitAcc')->toArray();
                                $reSurplusCrOppositeIds=array_unique($reSurplusCrOppositeIds);
    // echo "<br/>reSurplusCrOppositeIds: ";var_dump($reSurplusCrOppositeIds);

                                $reSurplusCrOppositeIdsArray=trackLevels($reSurplusCrOppositeIds);
                                $reSurplusCrOppositeIdsArray=array_unique($reSurplusCrOppositeIdsArray);
    // echo "<br/>reSurplusCrOppositeIdsArray: "; var_dump($reSurplusCrOppositeIdsArray);
// ==========================End Add: Adjustment during the Year===========================

// =======================================Starts Less: Adjustment during the Year=======================================
// =======Retained Surplus Debit, All Others Credit(Display Others)========
                                $reSurplusDrOppositeIds = DB::table('acc_voucher_details')->whereIn('voucherId',$voucherIdsOfTwoFYs)->whereIn('debitAcc',$retainedSurplusLedgerIds)->pluck('creditAcc')->toArray();
                                $reSurplusDrOppositeIds=array_unique($reSurplusDrOppositeIds);
    // echo "<br/>reSurplusDrOppositeIds: ";var_dump($reSurplusDrOppositeIds);

                                $reSurplusDrOppositeIdsArray=trackLevels($reSurplusDrOppositeIds);
                                $reSurplusDrOppositeIdsArray=array_unique($reSurplusDrOppositeIdsArray);
    // echo "<br/>reSurplusDrOppositeIdsArray: "; var_dump($reSurplusDrOppositeIdsArray);
// =======================================End Less: Adjustment during the Year=======================================

// =======================================Starts Less: Transferrend to reserve=======================================
// =====================Retained Surplus Credit, Reserve Fund Debit(Display Reserve Fund)======================
                                $reSurplusCrReFundDrIds = DB::table('acc_voucher_details')->whereIn('voucherId',$voucherIdsOfTwoFYs)->whereIn('creditAcc',$retainedSurplusLedgerIds)->whereIn('debitAcc',$reserveFundLedgerIds)->pluck('debitAcc')->toArray();
                                $reSurplusCrReFundDrIds=array_unique($reSurplusCrReFundDrIds);
    // echo "<br/>reSurplusCrReFundDrIds: ";var_dump($reSurplusCrReFundDrIds);

                                $reSurplusCrReFundDrIdsArray=trackLevels($reSurplusCrReFundDrIds);
                                $reSurplusCrReFundDrIdsArray=array_unique($reSurplusCrReFundDrIdsArray);
    // echo "<br/>reSurplusCrReFundDrIdsArray: "; var_dump($reSurplusCrReFundDrIdsArray);
// =======================================End Less: Transferrend to reserve=======================================

// =============================================Starts Add: Reserve fund=============================================
// =====================Retained Surplus Debit, Reserve Fund Credit(Display Reserve Fund)======================
                                $reSurplusDrReFundCrIds = DB::table('acc_voucher_details')->whereIn('voucherId',$voucherIdsOfTwoFYs)->whereIn('creditAcc',$retainedSurplusLedgerIds)->whereIn('debitAcc',$reserveFundLedgerIds)->pluck('debitAcc')->toArray();
                                $reSurplusDrReFundCrIds=array_unique($reSurplusDrReFundCrIds);
    // echo "<br/>reSurplusDrReFundCrIds: ";var_dump($reSurplusDrReFundCrIds);

                                $reSurplusDrReFundCrIdsArray=trackLevels($reSurplusDrReFundCrIds);
                                $reSurplusDrReFundCrIdsArray=array_unique($reSurplusDrReFundCrIdsArray);
    // echo "<br/>reSurplusDrReFundCrIdsArray: "; var_dump($reSurplusDrReFundCrIdsArray);
// =============================================End Add: Reserve fund=============================================

                                ?>
                                {{-- ====================================**********Opening Balance**********==================================== --}}
                                <tbody>
                                    <tr id="openingBalance" style="font-weight: bold;">
                                        <td>Opening Balance</td>
                                        <td class="preFiYearColumn" amount="{{$balanceAmountOfSecondPreFY}}">{{roundUpPHP($balanceAmountOfSecondPreFY, $searchedRoundUp)}}</td>
                                        <td class="curFiYearColumn" amount="{{$balanceAmountOfFirstPreFY}}">{{roundUpPHP($balanceAmountOfFirstPreFY, $searchedRoundUp)}}</td>
                                    </tr>
                                    {{-- ===============================Opening Balance*********Add: Opening Adjustment=============================== --}}
                                    <tr id="addOpeningAdjustment" >
                                        <td>Add: Opening Adjustment</td>
                                        <td class="preFiYearColumn" amount="0">0.00</td>
                                        <td class="curFiYearColumn" amount="0">0.00</td>
                                    </tr>
                                    {{-- ================================Opening Balance*********Prior Year Adjustment================================ --}}
                                    <tr id="priorYearAdjustment" >
                                        <td>Prior Year Adjustment</td>
                                        <td class="preFiYearColumn" amount="0">0.00</td>
                                        <td class="curFiYearColumn" amount="0">0.00</td>
                                    </tr>
                                    {{-- ================================Opening Balance*********Surplus for the Year================================ --}}
                                    <tr id="surplusForTheYear">
                                        <td>Surplus for the Year</td>
                                        <td class="preFiYearColumn" amount="{{$totalSurplusOfPreFY}}" >{{roundUpPHP($totalSurplusOfPreFY, $searchedRoundUp)}}</td>
                                        <td class="curFiYearColumn" amount="{{$totalSurplusOfCuFY}}" >{{roundUpPHP($totalSurplusOfCuFY, $searchedRoundUp)}}</td>
                                    </tr>
                                    {{-- ====================================**********Closing Balance**********==================================== --}}
                                    <tr id="closingBalance" style="font-weight: bold;">
                                        <td>Closing Balance</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>
                                    {{-- =======================================Add: Adjustment during the Year======================================= --}}
                                    {{-- ==========================Retained Surplus Credit, All Others Debit(Display Others)=========================== --}}
                                    <tr id="addAdjustment" class="seniorParent">
                                        <td>Add: Adjustment during the Year</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>

                                    <?php
                                    $dataArray["stage"] = "addAdjustment";
                                    structure($reSurplusCrOppositeIdsArray, $dataArray);
                                    ?>

                                    {{-- =======================================Less: Adjustment during the Year======================================= --}}
                                    {{-- ==========================Retained Surplus Debit, All Others Credit(Display Others)=========================== --}}
                                    <tr id="lessAdjustment" class="seniorParent">
                                        <td>Less: Adjustment during the Year</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>
                                    <?php
                                    $dataArray["stage"] = "lessAdjustment";
                                    structure($reSurplusDrOppositeIdsArray, $dataArray);
                                    ?>
                                    {{-- =======================================Less: Transferrend to reserve======================================= --}}
                                    {{-- =====================Retained Surplus Credit, Reserve Fund Debit(Display Reserve Fund)====================== --}}
                                    <tr id="lessTransferrend" class="seniorParent">
                                        <td>Less: Transferrend to reserve</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>
                                    <?php
                                    $dataArray["stage"] = "lessTransferrend";
                                    structure($reSurplusCrReFundDrIdsArray, $dataArray);
                                    ?>
                                    {{-- ============================**********Total Fund(After transfer to reserve)**********============================ --}}
                                    <tr id="totalFund" style="font-weight: bold;">
                                        <td>Total Fund(After transfer to reserve)</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>
                                    {{-- =============================================Add: Reserve fund============================================= --}}
                                    {{-- =====================Retained Surplus Debit, Reserve Fund Credit(Display Reserve Fund)====================== --}}
                                    <tr id="addReserve" class="seniorParent">
                                        <td>Add: Reserve fund</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>
                                    <?php
                                    $dataArray["stage"] = "addReserve";
                                    structure($reSurplusDrReFundCrIdsArray, $dataArray);
                                    ?>
                                    {{-- ====================================**********Balance as On**********==================================== --}}
                                    <tr id="balanceAsOnDate" style="font-weight: bold;">
                                        <td>Balance as on {{date('d-m-Y',strtotime($endDate))}}</td>
                                        <td class="preFiYearColumn" amount="" ></td>
                                        <td class="curFiYearColumn" amount="" ></td>
                                    </tr>
                                </tbody>

                                {{-- ************************************************************************************************************************ --}}
                                {{-- *************************************************Search By Current Year************************************************* --}}

                                @elseif($searchedSearchMethod==2)
                                <?php
// ====================================**********Starts Vouchers**********====================================
                                $voucherIdsOfCuYr = DB::table($acc_voucher)
                                ->where('companyId', $user_company_id)
                                ->whereIn('projectId',$projectIdArray)
                                ->whereIn('projectTypeId',$projectTypeIdArray)
                                ->whereIn('branchId',$branchIdArray)
                                ->where(function ($query) use ($startDate, $endDate){
                                    $query->where('voucherDate','>=', $startDate)
                                    ->where('voucherDate','<=', $endDate);
                                })->pluck('id')->toArray();
                                $dataArray["voucherIdsOfCuYr"] = $voucherIdsOfCuYr;
// ====================================**********End Vouchers**********====================================

// ====================================**********Starts Opening Balance**********====================================
                                $balanceAmountOfFirstPreFY = DB::table($acc_opening_balance)->where('companyIdFk', $user_company_id)->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('fiscalYearId', $firstPreFYId)->whereIn('ledgerId',$capitalFundLedgerIds)->sum('balanceAmount');
// ====================================**********End Opening Balance**********====================================


// =======================================Starts Add: Adjustment during the Year=======================================
// ==========================Retained Surplus Credit, All Others Debit(Display Others)===========================

                                if (count($voucherIdsOfCuYr) > 50000) {
                                    $voucherIdsOfCuYr = array_chunk($voucherIdsOfCuYr, 50000);
                                }
                                $reSurplusCrOppositeIds = DB::table('acc_voucher_details')->whereIn('voucherId',$voucherIdsOfCuYr)->whereIn('creditAcc',$retainedSurplusLedgerIds)->pluck('debitAcc')->toArray();
                                $reSurplusCrOppositeIds=array_unique($reSurplusCrOppositeIds);
    // echo "<br/>reSurplusCrOppositeIds: ";var_dump($reSurplusCrOppositeIds);

                                $reSurplusCrOppositeIdsArray=trackLevels($reSurplusCrOppositeIds);
                                $reSurplusCrOppositeIdsArray=array_unique($reSurplusCrOppositeIdsArray);
    // echo "<br/>reSurplusCrOppositeIdsArray: "; var_dump($reSurplusCrOppositeIdsArray);

// =======================================End Add: Adjustment during the Year=======================================

// =======================================Starts Less: Adjustment during the Year=======================================
// ==========================Retained Surplus Debit, All Others Credit(Display Others)===========================
                                $reSurplusDrOppositeIds = DB::table('acc_voucher_details')->whereIn('voucherId',$voucherIdsOfCuYr)->whereIn('debitAcc',$retainedSurplusLedgerIds)->pluck('creditAcc')->toArray();
                                $reSurplusDrOppositeIds=array_unique($reSurplusDrOppositeIds);
    // echo "<br/>reSurplusDrOppositeIds: ";var_dump($reSurplusDrOppositeIds);

                                $reSurplusDrOppositeIdsArray=trackLevels($reSurplusDrOppositeIds);
                                $reSurplusDrOppositeIdsArray=array_unique($reSurplusDrOppositeIdsArray);
    // echo "<br/>reSurplusDrOppositeIdsArray: "; var_dump($reSurplusDrOppositeIdsArray);

// =======================================End Less: Adjustment during the Year=======================================

//  =======================================Starts Less: Transferrend to reserve=======================================
//  =====================Retained Surplus Credit, Reserve Fund Debit(Display Reserve Fund)======================
                                $reSurplusCrReFundDrIds = DB::table('acc_voucher_details')->whereIn('voucherId',$voucherIdsOfCuYr)->whereIn('creditAcc',$retainedSurplusLedgerIds)->whereIn('debitAcc',$reserveFundLedgerIds)->pluck('debitAcc')->toArray();
                                $reSurplusCrReFundDrIds=array_unique($reSurplusCrReFundDrIds);
    // echo "<br/>reSurplusCrReFundDrIds: ";var_dump($reSurplusCrReFundDrIds);

                                $reSurplusCrReFundDrIdsArray=trackLevels($reSurplusCrReFundDrIds);
                                $reSurplusCrReFundDrIdsArray=array_unique($reSurplusCrReFundDrIdsArray);
    // echo "<br/>reSurplusCrReFundDrIdsArray: "; var_dump($reSurplusCrReFundDrIdsArray);

//  =======================================End Less: Transferrend to reserve=======================================

// =============================================Starts Add: Reserve fund=============================================
// =====================Retained Surplus Debit, Reserve Fund Credit(Display Reserve Fund)======================
                                $reSurplusDrReFundCrIds = DB::table('acc_voucher_details')->whereIn('voucherId',$voucherIdsOfCuYr)->whereIn('creditAcc',$retainedSurplusLedgerIds)->whereIn('debitAcc',$reserveFundLedgerIds)->pluck('debitAcc')->toArray();
                                $reSurplusDrReFundCrIds=array_unique($reSurplusDrReFundCrIds);
    // echo "<br/>reSurplusDrReFundCrIds: ";var_dump($reSurplusDrReFundCrIds);

                                $reSurplusDrReFundCrIdsArray=trackLevels($reSurplusDrReFundCrIds);
                                $reSurplusDrReFundCrIdsArray=array_unique($reSurplusDrReFundCrIdsArray);
    // echo "<br/>reSurplusDrReFundCrIdsArray: "; var_dump($reSurplusDrReFundCrIdsArray);

// =============================================End Add: Reserve fund=============================================

                                ?>
                                {{-- ====================================**********Opening Balance**********==================================== --}}
                                <tbody>
                                    <tr id="openingBalance" style="font-weight: bold;">
                                        <td>Opening Balance</td>
                                        <td class="thisYearColumn" amount="{{$balanceAmountOfFirstPreFY}}" >{{roundUpPHP($balanceAmountOfFirstPreFY, $searchedRoundUp)}}</td>
                                    </tr>
                                    {{-- ===============================Opening Balance*********Add: Opening Adjustment=============================== --}}
                                    <tr id="addOpeningAdjustment" >
                                        <td>Add: Opening Adjustment</td>
                                        <td class="thisYearColumn" amount="0" >0.00</td>
                                    </tr>
                                    {{-- ================================Opening Balance*********Prior Year Adjustment================================ --}}
                                    <tr id="priorYearAdjustment" >
                                        <td>Prior Year Adjustment</td>
                                        <td class="thisYearColumn" amount="0" >0.00</td>
                                    </tr>
                                    {{-- ================================Opening Balance*********Surplus for the Year================================ --}}
                                    <?php
                                    $totalSurplusOfCuYr=totalSurplusCal($incomeLedgerIds, $expenseLedgerIds, $voucherIdsOfCuYr);
                                    ?>
                                    <tr id="surplusForTheYear">
                                        <td>Surplus for the Year</td>
                                        <td class="thisYearColumn" amount="{{$totalSurplusOfCuYr}}" >{{roundUpPHP($totalSurplusOfCuYr, $searchedRoundUp)}}</td>
                                    </tr>
                                    {{-- ====================================**********Closing Balance**********==================================== --}}
                                    <tr id="closingBalance" style="font-weight: bold;">
                                        <td>Closing Balance</td>
                                        <td class="thisYearColumn" amount="" ></td>
                                    </tr>
                                    {{-- =======================================Add: Adjustment during the Year======================================= --}}
                                    {{-- ==========================Retained Surplus Credit, All Others Debit(Display Others)=========================== --}}
                                    <tr id="addAdjustment" class="seniorParent" >
                                        <td>Add: Adjustment during the Year</td>
                                        <td class="thisYearColumn" amount="" ></td>
                                    </tr>
                                    <?php
                                    $dataArray["stage"] = "addAdjustment";
                                    structure($reSurplusCrOppositeIdsArray, $dataArray);
                                    ?>
                                    {{-- =======================================Less: Adjustment during the Year======================================= --}}
                                    {{-- ==========================Retained Surplus Debit, All Others Credit(Display Others)=========================== --}}
                                    <tr id="lessAdjustment" class="seniorParent" >
                                        <td>Less: Adjustment during the Year</td>
                                        <td class="thisYearColumn" amount="" ></td>
                                    </tr>
                                    <?php
                                    $dataArray["stage"] = "lessAdjustment";
                                    structure($reSurplusDrOppositeIdsArray, $dataArray);
                                    ?>
                                    {{-- =======================================Less: Transferrend to reserve======================================= --}}
                                    {{-- =====================Retained Surplus Credit, Reserve Fund Debit(Display Reserve Fund)====================== --}}
                                    <tr id="lessTransferrend" class="seniorParent" >
                                        <td>Less: Transferrend to reserve</td>
                                        <td class="thisYearColumn" amount="" ></td>
                                    </tr>
                                    <?php
                                    $dataArray["stage"] = "lessTransferrend";
                                    structure($reSurplusCrReFundDrIdsArray, $dataArray);
                                    ?>
                                    {{-- ============================**********Total Fund(After transfer to reserve)**********============================ --}}
                                    <tr id="totalFund" style="font-weight: bold;">
                                        <td>Total Fund(After transfer to reserve)</td>
                                        <td class="thisYearColumn" amount="" ></td>
                                    </tr>
                                    {{-- =============================================Add: Reserve fund============================================= --}}
                                    {{-- =====================Retained Surplus Debit, Reserve Fund Credit(Display Reserve Fund)====================== --}}
                                    <tr id="addReserve" class="seniorParent" >
                                        <td>Add: Reserve fund</td>
                                        <td class="thisYearColumn" amount="" ></td>
                                    </tr>
                                    <?php
                                    $dataArray["stage"] = "addReserve";
                                    structure($reSurplusDrReFundCrIdsArray, $dataArray);
                                    ?>
                                    {{-- ====================================**********Balance as **********==================================== --}}
                                    <tr id="balanceAsOnDate" style="font-weight: bold;">
                                        <td>Balance as on {{date('d-m-Y',strtotime($endDate))}}</td>
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
          download: "capital fund Report_"+ today + ".xls"}).appendTo("body").get(0).click();
        e.preventDefault();
    });


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

    var rowCount = $('#accCapitalFundTable tr').length;
            // alert(rowCount);
            var amount1,amount2,amount3,amount4;
            amount1=amount2=amount3=amount4= 0;
            var totalAmount1=0;

            for(i=rowCount-1 ;i>=1;i--){
                if ($("#accCapitalFundTable tr").eq(i).attr('level')==4) {
                    var tempAmount4 = parseFloat($("#accCapitalFundTable tr").eq(i).find('.'+className).attr('amount'));
                    amount4 = amount4 + tempAmount4;

                }
                else if ($("#accCapitalFundTable tr").eq(i).attr('level')==3) {

                    $("#accCapitalFundTable tr").eq(i).closest('tr').find('.'+className).attr('amount',amount4);
                    $("#accCapitalFundTable tr").eq(i).closest('tr').find('.'+className).html(roundUp(amount4, searchedRoundUp));

                    var tempAmount3 = parseFloat($("#accCapitalFundTable tr").eq(i).closest('tr').find('.'+className).attr('amount'));
                    amount3 = amount3 + tempAmount3;
                    amount4 = 0;
                }
                else if ($("#accCapitalFundTable tr").eq(i).attr('level')==2) {

                    $("#accCapitalFundTable tr").eq(i).closest('tr').find('.'+className).attr('amount',amount3);
                    $("#accCapitalFundTable tr").eq(i).closest('tr').find('.'+className).html(roundUp(amount3, searchedRoundUp));

                    var tempAmount2 = parseFloat($("#accCapitalFundTable tr").eq(i).closest('tr').find('.'+className).attr('amount'));
                    amount2 = amount2 + tempAmount2;
                    amount3 = 0;
                }
                else if ($("#accCapitalFundTable tr").eq(i).attr('level')==1) {

                    $("#accCapitalFundTable tr").eq(i).closest('tr').find('.'+className).attr('amount',amount2);
                    $("#accCapitalFundTable tr").eq(i).closest('tr').find('.'+className).html(roundUp(amount2, searchedRoundUp));

                    var tempAmount1 = parseFloat($("#accCapitalFundTable tr").eq(i).closest('tr').find('.'+className).attr('amount'));
                    amount1 = amount1 + tempAmount1;
                    amount2 = 0;
                }
                else if ($("#accCapitalFundTable tr").eq(i).attr('level')==0) {
                    $("#accCapitalFundTable tr").eq(i).closest('tr').find('.'+className).attr('amount',amount1);
                    $("#accCapitalFundTable tr").eq(i).closest('tr').find('.'+className).html(roundUp(amount1, searchedRoundUp));
                    amount1 = 0;
                    var tempTotalAmount1 = parseFloat($("#accCapitalFundTable tr").eq(i).find('.'+className).attr('amount'));
                    totalAmount1 = totalAmount1 + tempTotalAmount1;
                }

                if ($("#accCapitalFundTable tr").eq(i).attr('class')=="seniorParent") {
                    $("#accCapitalFundTable tr").eq(i).closest('tr').find('.'+className).attr('amount',totalAmount1);
                    $("#accCapitalFundTable tr").eq(i).closest('tr').find('.'+className).html(roundUp(totalAmount1, searchedRoundUp));
                    totalAmount1=0;
                    // alert($("#accCapitalFundTable tr").eq(i).closest('tr').attr("id"));
                }


            }


        }
//=======================================End SUM Function====================================================

//===============================Starts totalAmounts Function====================================================
function totalAmounts(className, searchedRoundUp){
//closingBalanceAmount===========================
var opBalanceAmount, addOpAdjustmentAmount, priorYrAdjustAmount, surplusAmount, closingBalanceAmount;
opBalanceAmount=addOpAdjustmentAmount=priorYrAdjustAmount=surplusAmount=closingBalanceAmount=0;

opBalanceAmount = parseFloat($("#accCapitalFundTable #openingBalance").closest('tr').find('.'+className).attr('amount'));
addOpAdjustmentAmount = parseFloat($("#accCapitalFundTable #addOpeningAdjustment").closest('tr').find('.'+className).attr('amount'));
priorYrAdjustAmount = parseFloat($("#accCapitalFundTable #priorYearAdjustment").closest('tr').find('.'+className).attr('amount'));
surplusAmount = parseFloat($("#accCapitalFundTable #surplusForTheYear").closest('tr').find('.'+className).attr('amount'));

closingBalanceAmount=opBalanceAmount+addOpAdjustmentAmount+priorYrAdjustAmount+surplusAmount;
            // alert(closingBalanceAmount);
            $("#accCapitalFundTable #closingBalance").find('.'+className).attr('amount',closingBalanceAmount);
            $("#accCapitalFundTable #closingBalance").find('.'+className).html(roundUp(closingBalanceAmount, searchedRoundUp));
//totalFundAmount===========================
var addAdjustAmount, lessAdjustAmount, lessTransferredAmount, totalFundAmount;
closingBalanceAmount=addAdjustAmount=lessAdjustAmount=lessTransferredAmount=totalFundAmount=0;

closingBalanceAmount = parseFloat($("#accCapitalFundTable #closingBalance").closest('tr').find('.'+className).attr('amount'));
addAdjustAmount = parseFloat($("#accCapitalFundTable #addAdjustment").closest('tr').find('.'+className).attr('amount'));
lessAdjustAmount = parseFloat($("#accCapitalFundTable #lessAdjustment").closest('tr').find('.'+className).attr('amount'));
lessTransferredAmount = parseFloat($("#accCapitalFundTable #lessTransferrend").closest('tr').find('.'+className).attr('amount'));

totalFundAmount=closingBalanceAmount+addAdjustAmount-lessAdjustAmount-lessTransferredAmount;
            // alert(totalFundAmount);
            $("#accCapitalFundTable #totalFund").find('.'+className).attr('amount',totalFundAmount);
            $("#accCapitalFundTable #totalFund").find('.'+className).html(roundUp(totalFundAmount, searchedRoundUp));
//balanceAsOnDateAmount===========================
var addReseveFundAmount, balanceAsOnDateAmount;
totalFundAmount=addReseveFundAmount=balanceAsOnDateAmount=0;

totalFundAmount = parseFloat($("#accCapitalFundTable #totalFund").closest('tr').find('.'+className).attr('amount'));
addReseveFundAmount = parseFloat($("#accCapitalFundTable #addReserve").closest('tr').find('.'+className).attr('amount'));

balanceAsOnDateAmount=totalFundAmount+addReseveFundAmount;
            // alert(balanceAsOnDateAmount);
            $("#accCapitalFundTable #balanceAsOnDate").find('.'+className).attr('amount',balanceAsOnDateAmount);
            $("#accCapitalFundTable #balanceAsOnDate").find('.'+className).html(roundUp(balanceAsOnDateAmount, searchedRoundUp));
        }

//===============================End totalAmounts Function====================================================


var searchedRoundUp = "{{$searchedRoundUp}}";
var searchedWithZero = "{{$searchedWithZero}}";
        // alert(searchedWithZero);

        var searchedSearchMethod="{{$searchedSearchMethod}}";
        if (searchedSearchMethod==1) {
            sumOfEachRow("preFiYearColumn", searchedRoundUp);
            sumOfEachRow("curFiYearColumn", searchedRoundUp);

            totalAmounts("preFiYearColumn", searchedRoundUp);
            totalAmounts("curFiYearColumn", searchedRoundUp);
        }else if(searchedSearchMethod==2){
            sumOfEachRow("thisYearColumn", searchedRoundUp);

            totalAmounts("thisYearColumn", searchedRoundUp);
        }
        var searchedDepthLevel = "{{$searchedDepthLevel}}";
        // alert(searchedDepthLevel);
        $("#accCapitalFundTable tr").each(function(index, value) {
            if($(this).attr('level')>=searchedDepthLevel){
                $(this).hide();
            }
        });


    });     //document.ready

$(function(){
    $("#printIcon").click(function(){

        $("#accCapitalFundTable").removeClass('table table-striped table-bordered');

        var printStyle = '<style>#accCapitalFundTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt solid ash;page-break-inside:auto;} #accCapitalFundTable tr:last-child { font-weight: bold;} #accCapitalFundTable thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;} #accCapitalFundTable tbody tr td { vertical-align: middle;padding:3px ;font-size:10px;} #accCapitalFundTable tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{size: auto;margin: 0;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

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
#accCapitalFundTable thead tr th { padding: 0px; }
#accCapitalFundTable thead tr th:last-child, #accCapitalFundTable thead tr th:nth-child(2) { width: 160px;  }
/*#accCapitalFundTable tbody tr { font-size: 12px; }*/
#accCapitalFundTable tbody tr td:nth-child(1) { text-align: left; padding-left:5px; }
#accCapitalFundTable tbody tr td:last-child, #accCapitalFundTable tbody tr td:nth-child(2) { text-align: right; padding-right:5px;  }
#reportingInfoTable{
    color: black;
    font-size: 12px;
    width: 100%;
}
#reportingInfoTable tbody tr td{ text-align: left; }
</style>
