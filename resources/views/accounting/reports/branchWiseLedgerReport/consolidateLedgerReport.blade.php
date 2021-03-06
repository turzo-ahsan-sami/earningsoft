{{-- <p style="color: black">=============singleSamityReport=============</p> --}}
<?php
use Carbon\Carbon;

    // echo "projectValue: $projectValue <br/>";
    // echo "branchValue: $branchValue <br/>";
    // echo "projectTypeValue: $projectTypeValue <br/>";
    // echo "ledgerIdValue: $ledgerIdValue <br/>";
    // echo "voucherTypeValue: $voucherTypeValue <br/>";
    // echo "startDateValue: $startDateValue <br/>";
    // echo "endDateValue: $endDateValue <br/>";
    // echo "preFiYrStartDate: $preFiYrStartDate <br/>";
    // echo "preFiYrEndDate: $preFiYrEndDate <br/>";
    // echo "preFiYrId: $preFiYrId <br/>";
    // echo "userProjectTypeId: $userProjectTypeId <br/>";
    // echo "endDateValue:".Carbon::parse($endDateValue)->subYear();

// echo "################################################--1--################################################<br/>";

//     echo "<pre>";
//     print_r($branchesInfo);
//     echo "</pre>";

    // $object = new stdClass();
    // $object->name = "My name";
    // echo "<pre>";
    // print_r($object);
    // echo "</pre>";
    // $myArray[] = $object;
    // $myArray[] = new stdClass();
    // $myArray[] = (object) array('name' => 'My name');

// echo "################################################--2--################################################<br/>";

//     echo "<pre>";
//     print_r($myArray);
//     echo "</pre>";

// echo "################################################--3--################################################<br/>";
//     echo "<pre>";
//     print_r($object);
//     echo "</pre>";

    // echo "<pre>";
    // print_r($voucherTypeIdArray);
    // echo "</pre>";
function roundUp($amount, $searchedRoundUp){
    if($searchedRoundUp==1){
        $roundUpAmount=round($amount);
        $roundUpAmount=number_format($roundUpAmount, 2, '.', ',');
    }elseif($searchedRoundUp==2){
        $roundUpAmount=number_format($amount, 2, '.', ',');
    }
    return $roundUpAmount;
}


?>
<style type="text/css">
#trialBalanceReportTable{
    font-family: arial !important;
}
</style>

<div id="printDiv">
    <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}
        <?php 
        $company = DB::table('gnr_company')->where('id',$userCompanyId)->select('name','address')->first();
        ?>
        <span style="font-size:14px;">{{$company->name}}</span><br/>
        <span style="font-size:11px;">{{$company->address}}</span><br/>
        <span style="text-decoration: underline;  font-size:14px;">Branch Wise Ledger Report</span>
    </div>


    <div class="row">       {{-- div for Ledger --}}

        <div class="col-md-12"  style="font-size: 12px;" >
            <?php 
                // $ledgerHead = DB::table('acc_account_ledger')->where('id',$ledgerIdValue)->select('name', 'code')->first();
            $ledgerHead = DB::table('acc_account_ledger')->where('id',$searchedLedgerId)->select('name', 'code')->first();
            $projectName = DB::table('gnr_project')->where('id',$projectValue)->value('name');
            if($projectTypeValue!=-1){
                $projectTypeName = DB::table('gnr_project_type')->where('id',$projectTypeValue)->value('name');
            } 

            if($branchValue>=0){
                $branchName = DB::table('gnr_branch')->where('id',$branchValue)->value('name');
            }                                        
            
            ?>
            
            <p style="color: black; font-weight: bold; font-size: 14px; text-align: center;">{{$ledgerHead->name." - ".$ledgerHead->code}} </p> 
            <span>
                <span style="color: black; float: left;">
                    <span style="font-weight: bold;">Ledger Head:<?php echo str_repeat('&nbsp;', 6);?></span>
                    <span>{{$ledgerHead->name}}</span>
                </span>
                <span style="color: black; float: right;">
                    <span style="font-weight: bold;">
                        <?php 
                        if($searchedReportLevel == 'Branch')
                        {
                            echo "Branch Name:";
                            echo str_repeat('&nbsp;', 3);
                        }else if($searchedReportLevel == 'Area'){
                            echo "Area Name :";
                            echo str_repeat('&nbsp;', 7);
                        }else if($searchedReportLevel == 'Zone'){
                         echo "Zone Name:";   
                     }else{
                        echo "Region Name:";
                    }
                    ?> 
                    
                    
                </span>
                <span>
                    <?php 
                    if($searchedReportLevel == 'Branch')
                    {
                        if($branchValue==-1)
                        { 
                            echo "All with Head Office"; 
                        }else if($branchValue==-2)
                        { 
                            echo "All with out Head Office"; 
                        }else{ 
                            echo $branchName; 
                        }

                    }else if($searchedReportLevel == 'Area'){
                        echo $areaName;
                    }else if($searchedReportLevel == 'Zone'){
                     echo $zoneName;   
                 }else{
                    echo $regionName;
                }

                        // if($branchValue==-1){ echo "All with Head Office"; }else if($branchValue==-2){ echo "All with out Head Office"; }else{ echo $branchName; }
                ?>
            </span>                                                    
        </span>

    </span>
    <br>
    <span>
        <span style="color: black; float: left;">
            <span style="font-weight: bold;">Project Name: <?php echo str_repeat('&nbsp;', 3);?></span>
            <span>{{$projectName}}</span>                                                    
        </span>
        <span style="color: black; float: right;">
            <span style="font-weight: bold;">Reporting Date:<?php echo str_repeat('&nbsp;', 6);?></span>
            <span>{{Carbon::parse($startDateValue)->format('d-m-Y')." to ".Carbon::parse($endDateValue)->format('d-m-Y')}}</span>
        </span> 
    </span>
    <br/>

    <span>
        <span style="color: black; float: left;">
            <span style="font-weight: bold;">Project Type: <?php echo str_repeat('&nbsp;', 5);?></span>
            <span>
                <?php 
                if($projectTypeValue==-1){ echo "All"; }else{ echo $projectTypeName; }
                ?>
            </span>                                                    
        </span>
        <span style="color: black; float: right;">
            <span style="font-weight: bold;">Print Date: <?php echo str_repeat('&nbsp;', 22);?></span>
            <span>{{Carbon::now()->format('d-m-Y H:i:s')}}</span>                                                    
        </span>
        
    </span>
    <br/>

</div>


</div>


<div class="row">
 <div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-striped table-bordered" width="100%" id="reportingTable" border="1pt solid ash" style="color:black; border-collapse: collapse;/*table-layout: fixed;*/ " >
            <thead>
                <tr>
                    <th rowspan="2">#</th>
                    <th rowspan="2">Branch</th>
                    <th rowspan="2">Opening Balance</th>
                    <th colspan="2">Current Period</th>
                            {{-- <th>Debit Amount</th>
                                <th>Credit Amount</th> --}}  
                                <th rowspan="2">Closing Balance</th>
                                
                            </tr>
                            <tr>
                                <th>Debit Amount</th>
                                <th>Credit Amount</th>
                            </tr>
                            
                        </thead>

                        <tbody>

                            @foreach ($branchesInfoObj as $branchInfo)

                            <tr>
                                <td>{{$branchInfo->slNo}}</td>
                                <td class="name">{{$branchInfo->nameWithCode}}</td>
                                {{-- <td class="amount">{{number_format($branchInfo->openingBalance, 2,".",",")}}</td> --}}
                                <td  class="amount openingBalance" amount="{{$branchInfo->openingBalance}}">{{roundUp($branchInfo->openingBalance, $searchedRoundUp)}}</td>
                                {{-- <td class="amount">{{$branchInfo->openingBalance}}</td> --}}
                                {{-- <td class="amount">{{number_format($branchInfo->debitAmount, 2,".",",")}}</td> --}}
                                <td  class="amount debitAmount" amount="{{$branchInfo->debitAmount}}">{{roundUp($branchInfo->debitAmount, $searchedRoundUp)}}</td>
                                {{-- <td class="amount">{{number_format($branchInfo->creditAmount, 2,".",",")}}</td> --}}
                                <td  class="amount creditAmount" amount="{{$branchInfo->creditAmount}}">{{roundUp($branchInfo->creditAmount, $searchedRoundUp)}}</td>
                                {{-- <td class="amount">{{number_format($branchInfo->closingBalance, 2,".",",")}}</td> --}}
                                <td  class="amount closingBalance" amount="{{$branchInfo->closingBalance}}">{{roundUp($branchInfo->closingBalance, $searchedRoundUp)}}</td>
                            </tr>

                            @endforeach


                            <tr>
                                <td></td>
                                <td><strong>Total</strong></td>
                                {{-- <td class="amount"><strong>{{number_format($totalOpeningBalanceAmount, 2,".",",")}}</strong></td> --}}
                                <td class="amount"><strong>{{roundUp($totalOpeningBalanceAmount, $searchedRoundUp)}}</strong></td>
                                {{-- <td class="amount"><strong>{{number_format($totalDebitAmount, 2,".",",")}}</strong></td> --}}
                                <td class="amount"><strong>{{roundUp($totalDebitAmount, $searchedRoundUp)}}</strong></td>
                                {{-- <td class="amount"><strong>{{number_format($totalCreditAmount, 2,".",",")}}</strong></td> --}}
                                <td class="amount"><strong>{{roundUp($totalCreditAmount, $searchedRoundUp)}}</strong></td>
                                {{-- <td class="amount"><strong>{{number_format($totalClosingBalance, 2,".",",")}}</strong></td> --}}
                                <td class="amount"><strong>{{roundUp($totalClosingBalance, $searchedRoundUp)}}</strong></td>
                            </tr>

                        </tbody>
                        


                    </table>
                </div>      {{-- responseDIV --}}

                
            </div> 
        </div>

    </div>		{{-- printDiv DIV --}}

    <script  type="text/javascript">
        $("#printIcon").prop("disabled", false);
        $("#printIcon").show();
        $("#loadingModal").hide();

        var withZero = "{{$searchedWithZero}}";

        
        var rows = $('#reportingTable tr').length;

        if(withZero == 2){

            for(i=rows-2 ;i>=1;i--){

               
                var td1 = parseFloat($("#reportingTable tr").eq(i).closest('tr').find('.openingBalance').attr('amount'));
                var td2 = parseFloat($("#reportingTable tr").eq(i).closest('tr').find('.debitAmount').attr('amount'));
                var td3 = parseFloat($("#reportingTable tr").eq(i).closest('tr').find('.creditAmount').attr('amount'));
                var td4 = parseFloat($("#reportingTable tr").eq(i).closest('tr').find('.closingBalance').attr('amount'));
                
                if (td1==0 && td2==0 && td3==0 && td4  ==0) {
                    $("#reportingTable tr").eq(i).hide();
                }  

            }
        }

    </script>



    <style type="text/css">
    #reportingTable > thead > tr > td, #reportingTable > tbody > tr > td, #reportingTable > thead > tr > th, #reportingTable > tbody > tr > th{
        padding: 2px !important;
        font-size: 11px !important;        
    }

    #reportingTable > thead > tr > th{
        text-transform: capitalize !important;
    }


    #reportingTable > tbody > tr > td.amount{
        text-align:right !important;
    }
    
    #reportingTable > tbody > tr > td.name{
        text-align:left !important;
    }


</style>