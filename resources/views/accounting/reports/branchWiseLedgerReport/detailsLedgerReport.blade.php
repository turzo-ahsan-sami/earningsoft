
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

                        $voucherIdMatched = DB::table('acc_voucher')
                                ->whereIn('projectId',$projectIdArray)
                                ->whereIn('projectTypeId',$projectTypeIdArray)
                                ->whereIn('branchId',$branchIdArray)
                                ->whereIn('voucherTypeId',$voucherTypeIdArray)
                                ->where(function ($query) use ($startDate,$endDate){
                                      $query->where('voucherDate','>=',$startDate)
                                      ->where('voucherDate','<=',$endDate);
                                    })->pluck('id')->toArray();

                        // echo "<br/>";
                        // var_dump($voucherIdMatched);
                        // echo "<br/>";

                        $voucherDetails = DB::table('acc_voucher_details')
                                ->whereIn('voucherId',$voucherIdMatched)
                                ->where(function ($query) use ($searchedLedgerId){
                                          $query->where('debitAcc', $searchedLedgerId)
                                          ->orWhere('creditAcc', $searchedLedgerId);
                                        })
                                ->select('voucherId','debitAcc','creditAcc','amount')                        
                                ->get();

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

                            $openingBalanceAmountByDate = DB::table('acc_opening_balance')->whereIn('projectId',$projectIdArray)->whereIn('projectTypeId',$projectTypeIdArray)->whereIn('branchId',$branchIdArray)->where('ledgerId', $searchedLedgerId)->where('fiscalYearId', $previousFiscalYearValue->id)->sum('balanceAmount');

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
                                    ->sum('amount');

                            $creditAccAmount = DB::table('acc_voucher_details')
                                    ->whereIn('voucherId',$voucherIdMatchedForOpBal)
                                    ->where('creditAcc', $searchedLedgerId)
                                    ->sum('amount');

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

                                    <span>
                                        <span style="color: black; float: left;">
                                            <span style="font-weight: bold;">Branch Name: <?php echo str_repeat('&nbsp;', 3);?></span>
                                            <span>{{$branch}}</span>                                                    
                                        </span>
                                    </span>

                                </div>                                
                            
                            </div>


                            <div class="row" style=" margin: 15px 0px;">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered dt-responsive nowrap" border="1pt solid ash" style="border-collapse: collapse;" id="accLedgerReportTable">
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

                                            <tr>
                                                <td class="">{{++$no}}</td>
                                                <?php
                                                // $voucherInfo = DB::table('acc_voucher')->where('id', $voucherDetail->voucherId)->select('voucherDate','voucherCode','globalNarration')->first();
                                                ?>
                                                <td style="text-align: left;">{{ Carbon\Carbon::parse($voucherDetail->voucherDate)->format('d-m-Y') }}</td>
                                                <td style="text-align: left;">{{$voucherDetail->voucherCode}}</td>
                                                <td style="text-align: left;">
                                                    <?php
                                                    if($searchedLedgerId!=$voucherDetail->debitAcc){
                                                        // $ledgerInfo = DB::table('acc_account_ledger')->where('id', $voucherDetail->debitAcc)->select('name','code')->first();
                                                        $ledgerNameWithCode=$ledgerInfo[$voucherDetail->debitAcc];
                                                    }else if($searchedLedgerId!=$voucherDetail->creditAcc){
                                                        // $ledgerInfo = DB::table('acc_account_ledger')->where('id', $voucherDetail->creditAcc)->select('name','code')->first();
                                                        $ledgerNameWithCode=$ledgerInfo[$voucherDetail->creditAcc];
                                                    }
                                                    ?>
                                                    {{$ledgerNameWithCode}}
                                                </td>
                                                <td style="text-align: left;">{{$voucherDetail->globalNarration}}</td>

                                                <?php if($searchedLedgerId==$voucherDetail->debitAcc){
                                                array_push($debitAmountArray,$voucherDetail->amount);
                                                $balanceAmount=$balanceAmount+$voucherDetail->amount;
                                                ?>
                                                <td style="text-align: right;">{{number_format( $voucherDetail->amount, 2, '.', ',')}}</td>
                                                <td style="text-align: right;">-</td>

                                                <?php }else if($searchedLedgerId==$voucherDetail->creditAcc){
                                                array_push($creditAmountArray,$voucherDetail->amount);
                                                $balanceAmount=$balanceAmount-$voucherDetail->amount;
                                                ?>
                                                <td style="text-align: right;">-</td>
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
                                                <td colspan="5">Total</td>
                                                <td style="text-align: right;">{{number_format(array_sum($debitAmountArray), 2, '.', ',')}}</td>
                                                <td style="text-align: right;">{{number_format(array_sum($creditAmountArray), 2, '.', ',')}}</td>
                                                <?php
                                                $totalBalanceAmount=((array_sum($debitAmountArray)+$totalOpeningBalanceAmount)-array_sum($creditAmountArray));
                                                ?>
                                                <td style="text-align: right;"> {{number_format(abs($totalBalanceAmount), 2, '.', ',')}} </td>
                                                {{-- <td> {{number_format($totalBalanceAmount, 2, '.', ',')}} </td> --}}
                                                <td>
                                                    <?php if($totalBalanceAmount<0){ echo "Cr";}else{ echo "Dr";} ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>  {{-- TableResponsiveDiv --}}
                                
                            </div> {{-- rowDiv --}}
                           @php
                                }
                            @endphp 

                        </div> {{-- printDiv --}}



{{-- <script  type="text/javascript">
    $("#printIcon").prop("disabled", false);
    $("#printIcon").show();
    $("#loadingModal").hide();
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


</style> --}}


