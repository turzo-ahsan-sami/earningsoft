@extends('layouts/acc_layout')
@section('title', '| Trial Balance')
@section('content')

<?php
    function eachRow($child, $data) {?>

    <tr class="item{{$child->id}}" level="{{$data['loopTrack']}}" @if($data['loopTrack']<=3){{"style=font-weight:bold;"}}@endif>
        <td style="text-align: left; padding-left: 5px;">


            <?php

            if($child->isGroupHead==1){
                echo '<span></span>';
            }else{
                echo '<span></span>';
            }
            ?>

            @if($child->isGroupHead==1)
            {{strtoupper($child->name).' ['.$child->code.']'}}
            @else
            {{$child->name.' ['.$child->code.']'}}
            @endif

        </td>

         @php

         $childId = $child->id;

         $openingDebit = 0;
         $openingCredit = 0;

         $currentDebit = 0;
         $currentCredit = 0;

         $debitSum = 0;
         $creditSum = 0;

        $amount = 0;

            if ($child->isGroupHead==0) {

                //Curent Debit Credit
                $currentDebit = DB::table('acc_voucher_details')
                                        ->where('debitAcc', $childId)
                                        ->where('status',1)
                                        ->whereIn('voucherId',$data['vouchers'])
                                        ->sum('amount');

                $currentCredit = DB::table('acc_voucher_details')
                                        ->where('creditAcc', $childId)
                                        ->where('status',1)
                                        ->whereIn('voucherId',$data['vouchers'])
                                        ->sum('amount');


                //End Curent Debit Credit




                //Opening Balance


                $openingDebitBalance = DB::table('acc_opening_balance')
                                        ->where('ledgerId',$childId)
                                        ->where('fiscalYearId',$data['previousfiscalYearId'])
                                        ->where('projectId',$data['projectSelected'])
                                        ->whereIn('projectTypeId',$data['projectTypeId'])
                                        ->whereIn('branchId',$data['newBranches'])
                                        ->sum('debitAmount');

                $openingCreditBalance = DB::table('acc_opening_balance')
                                        ->where('ledgerId',$childId)
                                        ->where('fiscalYearId',$data['previousfiscalYearId'])
                                        ->where('projectId',$data['projectSelected'])
                                        ->whereIn('projectTypeId',$data['projectTypeId'])
                                        ->whereIn('branchId',$data['newBranches'])
                                        ->sum('creditAmount');


                $openingDebit = $openingDebitBalance;

                $openingCredit = $openingCreditBalance;



               $openingDebitBalanceFromCurentBalance = DB::table('acc_voucher_details')
                                                    ->where('debitAcc', $childId)
                                                    ->where('status',1)
                                                    ->whereIn('voucherId',$data['openingVouchers'])
                                                    ->sum('amount');

              $openingCreditBalanceFromCurentBalance = DB::table('acc_voucher_details')
                                                    ->where('creditAcc', $childId)
                                                    ->where('status',1)
                                                    ->whereIn('voucherId',$data['openingVouchers'])
                                                    ->sum('amount');

             $openingDebit = $openingDebit +  $openingDebitBalanceFromCurentBalance;
             $openingCredit = $openingCredit +  $openingCreditBalanceFromCurentBalance;

               //End Opening Balance

             //Sum
             if (($openingDebit + $currentDebit - $openingCredit - $currentCredit)>0) {
                 $debitSum = abs($openingDebit + $currentDebit - $openingCredit - $currentCredit);
             }
             else{
                $creditSum = abs($openingDebit + $currentDebit - $openingCredit - $currentCredit);
             }

             //End Sum

            }


            if($openingDebit >= $openingCredit){
                $openingDebit = $openingDebit - $openingCredit;
                $openingCredit = 0;
            }else{
                $openingCredit = $openingCredit - $openingDebit ;
                $openingDebit = 0;
            }


        @endphp

        @if($data['roundUpSelected']==1)
        <td></td> {{-- Extra column for Notes --}}
        <td class="openingDebit" amount="{{round($openingDebit,2)}}" style="text-align: right; padding: 0 5px 0 10px;">{{number_format($openingDebit,2)}}</td>
        <td class="openingCredit" amount="{{round($openingCredit,2)}}" style="text-align: right; padding: 0 5px 0 10px;">{{number_format($openingCredit,2)}}</td>

        <td class="currentDebit" amount="{{round($currentDebit,2)}}" style="text-align: right; padding: 0 5px 0 10px;">{{number_format($currentDebit,2)}}</td>
        <td class="currentCredit" amount="{{round($currentCredit,2)}}" style="text-align: right; padding: 0 5px 0 10px;">{{number_format($currentCredit,2)}}</td>

        <td class="sumDebit" amount="{{round($debitSum,2)}}" style="text-align: right; padding: 0 5px 0 10px;">{{number_format($debitSum,2)}}</td>
        <td class="sumCredit" amount="{{round($creditSum,2)}}" style="text-align: right; padding: 0 5px 0 10px;">{{number_format($creditSum,2)}}</td>

        @else
        <td></td> {{-- Extra column for Notes --}}
        <td class="openingDebit" amount="{{round($openingDebit,2)}}" style="text-align: right; padding: 0 5px 0 10px;">{{number_format($openingDebit,2,'.','')}}</td>
        <td class="openingCredit" amount="{{round($openingCredit,2)}}" style="text-align: right; padding: 0 5px 0 10px;">{{number_format($openingCredit,2,'.','')}}</td>

        <td class="currentDebit" amount="{{round($currentDebit,2)}}" style="text-align: right; padding: 0 5px 0 10px;">{{number_format($currentDebit,2,'.','')}}</td>
        <td class="currentCredit" amount="{{round($currentCredit,2)}}" style="text-align: right; padding: 0 5px 0 10px;">{{number_format($currentCredit,2,'.','')}}</td>

        <td class="sumDebit" amount="{{round($debitSum,2)}}" style="text-align: right; padding: 0 5px 0 10px;">{{number_format($debitSum,2,'.','')}}</td>
        <td class="sumCredit" amount="{{round($creditSum,2)}}" style="text-align: right; padding: 0 5px 0 10px;">{{number_format($creditSum,2,'.','')}}</td>
        @endif

    </tr>

    <?php return $child->id;}
?>


<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px;">
          <div class="panel-options">
                <?php $grandParent=0; ?>
              {{-- <div class="panel-options"> --}}
                  <button id="printIcon" class="btn btn-info pull-right"  target="_blank" style="font-size: 14px; margin-top: 10px; border: 3px solid #525659; border-radius: 25px;">
                        <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
                    </button>
              {{-- </div> --}}
          </div>
          {{-- <h3 class="content-heading-style">Consolidate Trial Balance</h3> --}}
         <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; padding-bottom: 5px; color: white;">Consolidate Trial Balance</h3>
        </div>
        {{-- <h5>kxghixd</h5> --}}

        <div class="panel-body panelBodyView">

        @php
            //echo var_dump($projectMatchedId);
            //echo var_dump($vouchers);
        // echo $projectSelected."<br>";
        // echo $projectTypeSelected."<br>";
        // echo $branchSelected."<br>";
         //echo $fiscalYearId."<br>";
        @endphp


            <!-- Filtering Start-->
           <div class="row" id="filtering-group">

                                <div class="form-horizontal form-groups" style="padding-right: 0px;">

                                    {!! Form::open(['url' => 'trialBalanceReport','method' => 'get']) !!}
                                    @php
                                        $userBranchId = Auth::user()->branchId;
                                    @endphp


                                    @if($userBranchId==1)
                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                             <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('', 'Project:', ['class' => 'control-label pull-left']) !!}
                                            </div>

                                            <div class="col-sm-12">
                                                <select name="searchProject" class="form-control" id="searchProject">

                                                    @foreach($projects as $project)
                                                    <option value="{{$project->id}}" @if($project->id==$projectSelected){{"selected=selected"}}@endif>{{str_pad($project->projectCode,3,"0",STR_PAD_LEFT).'-'.$project->name}}</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    @endif



                                    @if($userBranchId==1)
                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                            <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('', 'Branch:', ['class' => 'control-label pull-left']) !!}
                                            </div>

                                            <div class="col-sm-12">
                                                <select name="searchBranch" class="form-control" id="searchBranch">
                                                    <option value="">All</option>
                                                    <option value="0" @if($branchSelected===0){{"selected=selected"}}@endif>All Branches</option>
                                                    @foreach($branches as $branch)
                                                    <option value="{{$branch->id}}" @if($branch->id==$branchSelected){{"selected=selected"}}@endif>{{str_pad($branch->branchCode,3,"0",STR_PAD_LEFT).'-'.$branch->name}}</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                     @if($userBranchId==1)
                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                            <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('', 'Pro. Type:', ['class' => 'control-label pull-left']) !!}
                                            </div>

                                            <div class="col-sm-12">
                                                <select name="searchProjectType" class="form-control" id="searchProjectType">
                                                    <option value="">All</option>
                                                    @foreach($projectTypes as $projectType)
                                                    <option value="{{$projectType->id}}" @if($projectType->id==$projectTypeSelected){{"selected=selected"}}@endif>{{str_pad($projectType->projectTypeCode,3,"0",STR_PAD_LEFT).'-'.$projectType->name}}</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    @endif



                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                             <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('', 'Round Up:', ['class' => 'control-label pull-left']) !!}
                                            </div>

                                            <div class="col-sm-12">
                                                {!! Form::select('roundUp',[1=>'Yes',0=>'No'],$roundUpSelected,['class'=>'form-control']) !!}

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                             <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('depthLevel', 'Depth Level:', ['class' => 'control-label pull-left']) !!}
                                            </div>

                                            <div class="col-sm-12">
                                                {!! Form::select('depthLevel',[''=>'All',1=>'Level-1',2=>'Level-2',3=>'Level-3',4=>'Level-4'],$depthLevelSelected,['class'=>'form-control']) !!}

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                             <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('withZero', '\'0\' Balance:', ['class' => 'control-label pull-left']) !!}
                                            </div>

                                            <div class="col-sm-12">
                                                {!! Form::select('withZero',[''=>'Yes',1=>'No'],$withZeroSelected,['class'=>'form-control']) !!}

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2" id="dateRangeDiv">
                                        <div class="form-group" style="font-size: 13px; color:black">
                                            <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('', ' ', ['class' => 'control-label']) !!}
                                            </div>

                                            <div class="col-sm-12" style="padding-top: 7px;">
                                                <div class="form-group">
                                                    <div class="col-sm-6">
                                                        {!! Form::text('dateFrom',$dateFromSelected,['id'=>'dateFrom','placeholder'=>'From','class'=>'form-control','readonly','style'=>'cursor:pointer']) !!}
                                                        <p id="dateFrome" style="color: red;display: none;">*Required</p>
                                                    </div>
                                                    <div class="col-sm-6" id="dateToDiv">
                                                        {!! Form::text('dateTo',$dateToSelected,['id'=>'dateTo','placeholder'=>'To','class'=>'form-control','readonly','style'=>'cursor:pointer']) !!}
                                                        <p id="dateToe" style="color: red;display: none;">*Required</p>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black">

                                            <div class="col-sm-12" style="padding-top:25px;">

                                                {!! Form::submit('Search',['id'=>'search','class'=>'btn btn-primary btn-s animated fadeInRight','style'=>'font-size:13px;']) !!}
                                            </div>
                                        </div>
                                        {!! Form::close() !!}
                                    </div>

                                </div>

                        </div> {{-- End Filtering Group --}}
            <!-- filtering end-->



            @if(!$firstRequest)



            <div id="printingContent">

                <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}
                    <?php

                                $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
                    ?>
                    <span style="font-size:14px;">{{$company->name}}</span><br/>
                    <span style="font-size:11px;">{{$company->address}}</span><br/>
                    <span style="text-decoration: underline;  font-size:14px;"> Trial Balance</span><br/>
                    <span style="text-decoration: underline;">As at {{date('jS F, Y',strtotime($endDate))}}</span>
                </div>

                <div class="row">       {{-- div for Reporting Info --}}

                    <div class="col-md-12"  style="font-size: 12px;" >
                        <?php

                            // $project = DB::table('gnr_project')->where('id',$searchedProjectId)->select('name')->first();
                            $selectedProjectName = DB::table('gnr_project')->where('id',$projectSelected)->value('name');

                            if($projectTypeSelected==""){
                                $projectType = "All";
                            }if($projectTypeSelected!=""){
                                $projectType = DB::table('gnr_project_type')->where('id',$projectTypeSelected)->value('name');
                                // $projectType = $selectedProjectTypeName;
                            }
                            if($branchSelected==""){
                                $branch = "All ";
                            }else if($branchSelected==0){
                                $branch = "All Branch Office";
                            }else{
                                $branch = DB::table('gnr_branch')->where('id',$branchSelected)->value('name');
                                // $branch = $selectedBranchName;
                            }
                        ?>
                        <span>
                            <span style="color: black; float: left;">
                                <span style="font-weight: bold;">Project Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                                <span>{{$selectedProjectName}}</span>
                            </span>
                            <span style="color: black; float: right;">
                                <span style="font-weight: bold;">Reporting Date : <?php echo str_repeat('&nbsp;', 3);?></span>
                                <span>{{date('d-m-Y',strtotime($startDate))." to ".date('d-m-Y',strtotime($endDate))}}</span>
                            </span>

                        </span>
                        <br>
                        <span>
                            <span style="color: black; float: left;">
                                    <span style="font-weight: bold;">Project Type: <?php echo str_repeat('&nbsp;', 5);?></span>
                                    <span>{{$projectType}}</span>
                            </span>

                        </span>
                        <br>
                        <span>
                            <span style="color: black; float: left;">
                                    <span style="font-weight: bold;">Branch Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                                    <span>{{$branch}}</span>
                            </span>

                            <span style="color: black; float: right;">
                                    <span style="font-weight: bold;">Print Date: <?php echo str_repeat('&nbsp;', 3);?></span>
                                    <span>{{\Carbon\Carbon::now()->format('d-m-Y g:i A')}}</span>
                            </span>
                        </span>


                    </div>

                </div>


<br>

          <table id="trialBalanceReportTable" class="table table-striped table-bordered" style="color:black; border-collapse: collapse;" border= "1px solid ash;">
            <thead >
              <tr style="vertical-align: top;">
                <th rowspan="2">Particulars</th>
                <th rowspan="2">Notes</th> {{-- Extra column for Notes --}}
                <th colspan="2">Balance at the begining</th>
                <th colspan="2">During this period</th>
                <th colspan="2">Closing Balance (Cumulative)</th>
              </tr>
              <tr>
                  <th>Dr</th>
                  <th>Cr</th>
                  <th>Dr</th>
                  <th>Cr</th>
                  <th>Dr</th>
                  <th>Cr</th>
              </tr>

            </thead>
            <tbody>

              <?php $no=0; $loopTrack=0; ?>


              @php
               $data = array(
                'loopTrack' => $loopTrack,
                'vouchers' => $vouchers,
                'openingVouchers' => $openingVouchers,
                'roundUpSelected' => $roundUpSelected,
                'fiscalYearId' => $fiscalYearId,
                'previousfiscalYearId' => $previousfiscalYearId,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'projectSelected' => $projectSelected,
                'projectTypeId' => $projectTypeId,
                'newBranches' => $newBranches
                );
              @endphp


              @foreach($ledgers as $ledger)
              @if(in_array($ledger->id,$ledgerMatchedId))

                  <?php
                      $loopTrack=0;
                      eachRow($ledger, $data);
                  ?>

                <?php
                if($ledger->isGroupHead==1){
                $children1=DB::table('acc_account_ledger')->select('id','name','code','isGroupHead')->where('parentId', $ledger->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children1 as $child1)
                @if(in_array($child1->id,$ledgerMatchedId))
                    <?php
                        $data['loopTrack']=1;
                        eachRow($child1, $data);
                    ?>

                <?php
                if($child1->isGroupHead==1){
                $children2=DB::table('acc_account_ledger')->select('id','name','code','isGroupHead')->where('parentId', $child1->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children2 as $child2)
                @if(in_array($child2->id,$ledgerMatchedId))
                    <?php
                        $data['loopTrack']=2;
                        eachRow($child2, $data);
                    ?>

                <?php
                if($child2->isGroupHead==1){
                $children3=DB::table('acc_account_ledger')->select('id','name','code','isGroupHead')->where('parentId', $child2->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children3 as $child3)
                @if(in_array($child3->id,$ledgerMatchedId))
                    <?php
                        $data['loopTrack']=3;
                        eachRow($child3, $data);
                    ?>

                <?php
                if($child3->isGroupHead==1){
                $children4=DB::table('acc_account_ledger')->select('id','name','code','isGroupHead')->where('parentId', $child3->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children4 as $child4)
                @if(in_array($child4->id,$ledgerMatchedId))
                    <?php
                        $data['loopTrack']=4;
                        $dataForChild3 = eachRow($child4, $data);
                    ?>

                          @endif         {{-- End foreach loop for Child5 --}}
                       @endforeach <?php }?>
                        @endif         {{-- End foreach loop for Child4 --}}
                     @endforeach <?php }?>
                      @endif         {{-- End foreach loop for Child3 --}}
                  @endforeach <?php }?>
                   @endif        {{-- End foreach loop for Child2 --}}
                @endforeach <?php }?>
                @endif     {{-- End foreach loop for Child1 --}}
              @endforeach           {{-- End foreach loop for ledger --}}

              <tr style="font-weight: bold;">
                  <td style="text-align: center;">Total</td>
                  <td></td> {{-- Extra column for Notes --}}
                  <td class="tOpeningDebit" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                  <td class="tOpeningCredit" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                  <td class="tCurrentDebit" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                  <td class="tCurrentCredit" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                  <td class="tSumDebit" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                  <td class="tSumCredit" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
              </tr>

            </tbody>
          </table>
          </div>
          @endif
        </div>
      </div>
  </div>
  </div>
</div>
</div>


<style type="text/css">
    #trialBalanceReportTable thead tr th{padding: 2px;}
</style>



<script type="text/javascript">
    $(document).ready(function() {

        function toDate(dateStr) {
    var parts = dateStr.split("-");
    return new Date(parts[2], parts[1] - 1, parts[0]);
}
         /* Date Range From */
    $("#dateFrom").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2010:c",
            minDate: new Date('2010-07-01'),
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                $('#dateFrome').hide();
                $("#dateTo").datepicker("option","minDate",new Date(toDate($(this).val())));
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
                $("#dateFrom").datepicker("option","maxDate",new Date(toDate($(this).val())));
            }
        });

    /* End Date Range To */

    var firstRequest = "{{$firstRequest}}";
    if (firstRequest==1) {
        $("#dateFrom").datepicker().datepicker("setDate", new Date());
        $("#dateTo").datepicker().datepicker("setDate", new Date());
    }


    /*Validation*/
    $("#search").click(function(event) {
    if ($("#dateFrom").val()=="") {
            event.preventDefault();
            $("#dateFrome").show();
        }
    if ($("#dateTo").val()=="") {
        event.preventDefault();
        $("#dateToe").show();
    }
});
    /*End Validation*/





    });
</script>


{{-- Filtering --}}
<script type="text/javascript">
    jQuery(document).ready(function($) {

        function pad (str, max) {
              str = str.toString();
              return str.length < max ? pad("0" + str, max) : str;
            }

         /* Change Project*/
         $("#searchProject").change(function(){

            var projectId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProject',
                data: {projectId:projectId,_token: csrf},
                dataType: 'json',
                success: function( data ){


                    $("#searchProjectType").empty();
                    $("#searchProjectType").prepend('<option selected="selected" value="">All</option>');

                    $("#searchBranch").empty();
                    $("#searchBranch").prepend('<option value="0">All Branches</option>');
                    $("#searchBranch").prepend('<option selected="selected" value="">All</option>');


                    $.each(data['projectTypeList'], function (key, projectObj) {

                            $('#searchProjectType').append("<option value='"+ projectObj.id+"'>"+pad(projectObj.projectTypeCode,3)+"-"+projectObj.name+"</option>");
                    });

                    $.each(data['branchList'], function (key, branchObj) {

                            $('#searchBranch').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Project*/

         /* Change Project Type*/
         $("#searchProjectType").change(function(){
            var projectId = $("#searchProject").val();
            var projectTypeId = $(this).val();

            // alert(projectTypeId );


            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProjectType',
                data: {projectId:projectId,projectTypeId: projectTypeId,_token: csrf},
                dataType: 'json',
                success: function( data ){

                     $("#searchBranch").empty();
                     $("#searchBranch").prepend('<option value="0">All Branches</option>');
                    $("#searchBranch").prepend('<option selected="selected" value="">All</option>');


                     $.each(data['branchList'], function (key, branchObj) {

                            $('#searchBranch').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Project Type*/



          /* Change Category*/
         $("#searchCategory").change(function(){


            var categoryId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsFixedAssetsDepReportOnChngeCategory',
                data: {categoryId:categoryId,_token: csrf},
                dataType: 'json',
                success: function( data ){


                    $("#searchProductType").empty();
                    $("#searchProductType").prepend('<option selected="selected" value="">All</option>');


                    $.each(data['productTypeList'], function (key, productObj) {


                            $('#searchProductType').append("<option value='"+ productObj.id+"'>"+pad(productObj.productTypeCode,3)+"-"+productObj.name+"</option>");

                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Category*/



    });
</script>
{{-- End Filtering --}}



{{-- Sum the data for the parents --}}
<script type="text/javascript">
    $(document).ready(function() {
        function rightSidePad (str, max) {
              str = str.toString().slice(0,2);
              return str.length < max ? rightSidePad(str + "0" , max) : str;
            }

        function addCommas(nStr)
        {
            nStr += '';
            x = nStr.split('.');
            x1 = x[0];
            x2 = x.length > 1 ? '.' + rightSidePad(x[1],2) : '.' + '00';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            return x1 + x2;
        }



        var rows = $('#trialBalanceReportTable tr').length;


        var tod = 0;
        var toc = 0;
        var tcd = 0;
        var tcc = 0;
        var tsd = 0;
        var tsc = 0;


        //calculate total
        for(i=rows-2 ;i>=2;i--){
            var od = parseFloat($("#trialBalanceReportTable tr").eq(i).find('.openingDebit').attr('amount'));
            tod = tod + od;

            var oc = parseFloat($("#trialBalanceReportTable tr").eq(i).find('.openingCredit').attr('amount'));
            toc = toc + oc;

            var cd = parseFloat($("#trialBalanceReportTable tr").eq(i).find('.currentDebit').attr('amount'));
            tcd = tcd + cd;

            var cc = parseFloat($("#trialBalanceReportTable tr").eq(i).find('.currentDebit').attr('amount'));
            tcc = tcc + cc;

            var sd = parseFloat($("#trialBalanceReportTable tr").eq(i).find('.sumDebit').attr('amount'));
            tsd = tsd + sd;

            var sc = parseFloat($("#trialBalanceReportTable tr").eq(i).find('.sumCredit').attr('amount'));
            tsc = tsc + sc;
        }

        $("#trialBalanceReportTable tr:last").find('.tOpeningDebit').attr('amount',tod.toFixed(2));
        $("#trialBalanceReportTable tr:last").find('.tOpeningCredit').attr('amount',toc.toFixed(2));
        $("#trialBalanceReportTable tr:last").find('.tCurrentDebit').attr('amount',tcd.toFixed(2));
        $("#trialBalanceReportTable tr:last").find('.tCurrentCredit').attr('amount',tcc.toFixed(2));
        $("#trialBalanceReportTable tr:last").find('.tSumDebit').attr('amount',tsd.toFixed(2));
        $("#trialBalanceReportTable tr:last").find('.tSumCredit').attr('amount',tsc.toFixed(2));

        $("#trialBalanceReportTable tr:last").find('.tOpeningDebit').html(addCommas(tod.toFixed(2)));
        $("#trialBalanceReportTable tr:last").find('.tOpeningCredit').html(addCommas(toc.toFixed(2)));
        $("#trialBalanceReportTable tr:last").find('.tCurrentDebit').html(addCommas(tcd.toFixed(2)));
        $("#trialBalanceReportTable tr:last").find('.tCurrentCredit').html(addCommas(tcc.toFixed(2)));
        $("#trialBalanceReportTable tr:last").find('.tSumDebit').html(addCommas(tsd.toFixed(2)));
        $("#trialBalanceReportTable tr:last").find('.tSumCredit').html(addCommas(tsc.toFixed(2)));


        function calculateData(cName,rows){
            var className = cName;
            var rowCount = rows;

        var openingDebit1 = parseFloat(0);
        var openingCredit1 = parseFloat(0);
        var currentDebit1 = parseFloat(0);
        var currentCredit1 = parseFloat(0);
        var sumDebit1 = parseFloat(0);
        var sumCredit1 = parseFloat(0);

        var openingDebit2 = parseFloat(0);
        var openingCredit2 = parseFloat(0);
        var currentDebit2 = parseFloat(0);
        var currentCredit2 = parseFloat(0);
        var sumDebit2 = parseFloat(0);
        var sumCredit2 = parseFloat(0);

        var openingDebit3 = parseFloat(0);
        var openingCredit3 = parseFloat(0);
        var currentDebit3 = parseFloat(0);
        var currentCredit3 = parseFloat(0);
        var sumDebit3 = parseFloat(0);
        var sumCredit3 = parseFloat(0);

        var openingDebit4 = parseFloat(0);
        var openingCredit4 = parseFloat(0);
        var currentDebit4 = parseFloat(0);
        var currentCredit4 = parseFloat(0);
        var sumDebit4 = parseFloat(0);
        var sumCredit4 = parseFloat(0);


        for(i=rowCount-2 ;i>=2;i--){
            if ($("#trialBalanceReportTable tr").eq(i).attr('level')==4) {
                var tOpeningDebit4 = parseFloat($("#trialBalanceReportTable tr").eq(i).find('.'+className).attr('amount'));
                openingDebit4 = openingDebit4 + tOpeningDebit4;

            }
            else if ($("#trialBalanceReportTable tr").eq(i).attr('level')==3) {
                $("#trialBalanceReportTable tr").eq(i).closest('tr').find('.'+className).html(addCommas(openingDebit4));
                $("#trialBalanceReportTable tr").eq(i).closest('tr').find('.'+className).attr('amount',openingDebit4);
                var tOpeningDebit3 = parseFloat($("#trialBalanceReportTable tr").eq(i).closest('tr').find('.'+className).attr('amount'));
                openingDebit3 = openingDebit3 + tOpeningDebit3;
                openingDebit4 = 0;
            }
            else if ($("#trialBalanceReportTable tr").eq(i).attr('level')==2) {
                $("#trialBalanceReportTable tr").eq(i).closest('tr').find('.'+className).html(addCommas(openingDebit3));
                $("#trialBalanceReportTable tr").eq(i).closest('tr').find('.'+className).attr('amount',openingDebit3);
                var tOpeningDebit2 = parseFloat($("#trialBalanceReportTable tr").eq(i).closest('tr').find('.'+className).attr('amount'));
                openingDebit2 = openingDebit2 + tOpeningDebit2;
                openingDebit3 = 0;

            }
            else if ($("#trialBalanceReportTable tr").eq(i).attr('level')==1) {
                $("#trialBalanceReportTable tr").eq(i).closest('tr').find('.'+className).html(addCommas(openingDebit2));
                $("#trialBalanceReportTable tr").eq(i).closest('tr').find('.'+className).attr('amount',openingDebit2);
                var tOpeningDebit1 = parseFloat($("#trialBalanceReportTable tr").eq(i).closest('tr').find('.'+className).attr('amount'));
                openingDebit1 = openingDebit1 + tOpeningDebit1;
                openingDebit2 = 0;

            }
             else if ($("#trialBalanceReportTable tr").eq(i).attr('level')==0) {
                $("#trialBalanceReportTable tr").eq(i).closest('tr').find('.'+className).html(addCommas(openingDebit1));
                $("#trialBalanceReportTable tr").eq(i).closest('tr').find('.'+className).attr('amount',openingDebit1);
                openingDebit1 = 0;

            }
        }

        }

        calculateData('openingDebit',rows);
        calculateData('openingCredit',rows);
        calculateData('currentDebit',rows);
        calculateData('currentCredit',rows);
        calculateData('sumDebit',rows);
        calculateData('sumCredit',rows);



        var withZero = "{{$withZeroSelected}}";
        var depthLevel = "{{$depthLevel}}";



        for(i=rows-2 ;i>=2;i--){

            var od = parseFloat($("#trialBalanceReportTable tr").eq(i).closest('tr').find('.openingDebit').attr('amount'));
            var oc = parseFloat($("#trialBalanceReportTable tr").eq(i).closest('tr').find('.openingCredit').attr('amount'));
            var cd = parseFloat($("#trialBalanceReportTable tr").eq(i).closest('tr').find('.currentDebit').attr('amount'));
            var cc = parseFloat($("#trialBalanceReportTable tr").eq(i).closest('tr').find('.currentCredit').attr('amount'));


            if ($("#trialBalanceReportTable tr").eq(i).attr('level')>=depthLevel) {
                $("#trialBalanceReportTable tr").eq(i).hide();
            }

            if (withZero!="") {
                 if(od==0 && oc==0 && cd==0 && cc==0){
                    $("#trialBalanceReportTable tr").eq(i).hide();
                }
            }


        }


        // Set - if value is zero
        for(i=rows-2 ;i>=2;i--){
            if ($("#trialBalanceReportTable tr").eq(i).closest('tr').find('.openingDebit').attr('amount')==0) {
                $("#trialBalanceReportTable tr").eq(i).closest('tr').find('.openingDebit').html('-');
            }
            if ($("#trialBalanceReportTable tr").eq(i).closest('tr').find('.openingCredit').attr('amount')==0) {
                $("#trialBalanceReportTable tr").eq(i).closest('tr').find('.openingCredit').html('-');
            }
            if ($("#trialBalanceReportTable tr").eq(i).closest('tr').find('.currentDebit').attr('amount')==0) {
                $("#trialBalanceReportTable tr").eq(i).closest('tr').find('.currentDebit').html('-');
            }
            if ($("#trialBalanceReportTable tr").eq(i).closest('tr').find('.currentCredit').attr('amount')==0) {
                $("#trialBalanceReportTable tr").eq(i).closest('tr').find('.currentCredit').html('-');
            }
            if ($("#trialBalanceReportTable tr").eq(i).closest('tr').find('.sumDebit').attr('amount')==0) {
                $("#trialBalanceReportTable tr").eq(i).closest('tr').find('.sumDebit').html('-');
            }
            if ($("#trialBalanceReportTable tr").eq(i).closest('tr').find('.sumCredit').attr('amount')==0) {
                $("#trialBalanceReportTable tr").eq(i).closest('tr').find('.sumCredit').html('-');
            }
        }






        });


</script>
{{-- End Sum the data for the parents --}}



{{-- Print Page --}}
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $("#printIcon").click(function(event) {
            // alert();

            // $("#hiddenTitle").show();
            // $("#hiddenInfo").show();
            // $("#trialBalanceReportTable").removeClass('table table-striped table-bordered');

             // var printStyle = '<style>#trialBalanceReportTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;page-break-inside:auto;}  thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:10px}  tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{size:portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

             var printStyle = '<style>#trialBalanceReportTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt solid ash;page-break-inside:auto;} tr:last-child { font-weight: bold;} thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;} tbody tr td { text-align:center;vertical-align: middle;padding:3px ;font-size:10px;} tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{size:portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';


             var mainContents = document.getElementById("printingContent").innerHTML;
  // var headerContents = '';

          var footerContents = "<div class='row' style='font-size:12px; padding-top:40px;'>Prepared By <span style='display:inline-block; width: 36%; padding-top:40px;'></span> Checked By <span style='display:inline-block; width: 36%; padding-top:40px;'></span> Approved By</div>";

          var printContents = '<div id="order-details-wrapper" style="padding: 10px;">'+ printStyle + mainContents + footerContents +'</div>';




        /*tr:nth-of-type(10n){page-break-after: always;}*/
       // @media print {.element-that-contains-table { overflow: visible !important; }}


  /*document.body.innerHTML = printStyle + printContents;
  window.print();*/

  var win = window.open('','printwindow');
win.document.write(printContents);
win.print();
win.close();
});
    });
</script>
{{-- EndPrint Page --}}



@endsection
