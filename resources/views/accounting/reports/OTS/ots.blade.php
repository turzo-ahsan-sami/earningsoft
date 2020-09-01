
<!--***********************************************
* Programmer: Himel Dey                           *
* Ambala IT                                      *
* Topic: OTS Statement Report                    *
***********************************************!-->

<div id="printDiv">
    <!--This div is going to print company details and  ots account Statement   !-->
    <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}
           <?php
              $user_company_id = Auth::user()->company_id_fk;
              $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
           ?>
           <span style="font-size:14px;">{{$company->name}}</span><br/>
           <span style="font-size:11px;">{{$company->address}}</span><br/>
           <span style="text-decoration: underline;  font-size:14px;">OTS Account Statement</span></br>
           <?php
              $BranchName= DB::table('gnr_branch')
                          ->select('gnr_branch.name')
                          ->where('gnr_branch.id', '=', $Branch)
                          ->first();
           ?>
           <span style="text-decoration: none;  font-size:15px;">Statement Period: {{Carbon\Carbon::parse($startDateValue)->format('d-m-Y ') }}   {{" "}} - {{Carbon\Carbon::parse($endDateValue)->format('d-m-Y ') }}</span>
    </div>
    <!--This row div ends here   !-->


    <div class="row">
      <!--This div is going to calculate initial ots account Information according to the accountNumber   !-->
       <div class="col-md-12" style="font-size: 12px;" >
         <?php
             $AccountNumber = DB::table('acc_ots_account')
                             ->join('acc_ots_member','acc_ots_account.id','=','acc_ots_member.id')
                             ->select('acc_ots_account.accNO','acc_ots_account.openingDate','acc_ots_member.*','acc_ots_account.status as accStatus')
                             ->where('acc_ots_account.id', '=', $accountNumber)
                             ->first();
         ?>
         <table style="width:100%; font-size: 12px;font-weight: bold; text-align: right; color:black;" id="information"  >
           <tbody>
            <tr>
              <td width="6%" style="text-align: left">Account Name</td>
              <td style="padding-right:10px">: </td>
              <td width="60%" style="text-align: left"> {{$AccountNumber->name}}</td>
              <td  style="text-align: right">Address</td>
              <td style="text-align: right">:</td>
              <td width="10%" style="text-align: right">{{$AccountNumber->address}}</td>
              <!--<php if($AccountNumber->accStatus == 1)
                                                     {echo "Open";}
                                                 else{echo "Closed";}?>!-->
            </tr>


            <tr>
              <td style="text-align: left">Account No</td>
              <td style="padding-right:10px">:</td>
              <td style="text-align: left">{{ $AccountNumber->accNO}}</td>
              <td style="text-align: right">Phone No</td>
              <td style="text-align: right">:</td>
              <td style="text-align: right"> {{$AccountNumber->mobileNo}}</td>
           </tr>


          <tr>
            <td style="text-align: left">Branch</td>
            <td style="padding-right:10px">:</td>
            <td style="text-align: left">{{ $BranchName->name}}</td>
            <td style="text-align: right">Print Date</td>
            <td style="text-align: right">:</td>
            <td style="text-align: right; "><?php  echo date("d/m/Y") ."  ".date("h:i:sa"); ?></td>



        </tr>

        </tbody>

     </table>
    </div>
  </div>




    <div class="row">
    	<div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="reportingTable" border="1pt solid ash" style="color:black; border-collapse: collapse;/*table-layout: fixed;*/ " >
                    <thead>
                        <tr>
                           <th rowspan="1" style="width:50px;">SL NO</th>
                            <th rowspan="2">Date</th>
                            <th rowspan="2">Narration</th>

                          <!--  <th colspan="2">Current Period</th>
                            {{-- <th>Debit Amount</th>
                            <th>Credit Amount</th> --}} !-->
                            <th>Debit Amount</th>
                            <th>Credit Amount</th>
                            <th rowspan="2">Balance</th>
                        </tr>
                        <tr></tr>

                    </thead>

                    <tbody>
                      <tr>
                        <td class="name"></td>
                          <td class="name"style="text-align:center;padding-right:3px;">{{$openingBalanceAmount->name}}</td>

                          {{-- <td class="amount"></td> --}}
                          <td  class="amount openingBalance" amount=""></td>
                          {{-- <td class="amount"></td> --}}
                          {{-- <td class="amount"></td> --}}
                          <td  class="amount debitAmount" amount="">{{$debitOpenning}}.00</td>
                          {{-- <td class="amount"></td> --}}
                          <td  class="amount creditAmount" amount="">{{$creditOpenning}}.00</td>
                          {{-- <td class="amount"></td> --}}
                          <td  class="amount closingBalance" amount="">{{ $sum}}.00</td>
                      </tr>
                    <?php
                         use Carbon\Carbon;
                         $max=sizeof($traDateArray);
                         $debit=0;
                         $credit=0;
                         $count=1;
                         for ($x = 0; $x <$max; $x++) {  ?>
                           <?php
                              $payment= DB::table('acc_ots_payment_details')
                                        ->select('id')
                                        ->where('paymentDate', '=', $traDateArray[$x])
                                        ->where('accId_fk','=',$accountNumber)
                                        ->first();

                            $interest= DB::table('acc_ots_interest_details')
                                       ->select('id')
                                       ->where('generateDate', '=', $traDateArray[$x])
                                       ->where('accId_fk','=',$accountNumber)
                                       ->first();
                          ?>
                         <?php
                            if($payment){?>
                               <tr>

                                  <td> {{$count}}</td>
                                 <td class="name"style="text-align:center;padding-right:3px;">
                                   <?php

                                   $count++;
                                   $traDateArray[$x]=Carbon::parse($traDateArray[$x])->format('d-m-Y ');

                                     echo  $traDateArray[$x]; ?>
                                 </td>
                                <td class="name">
                                   Money Widraw Occured
                                </td>


                        <td  class="amount debitAmount" amount="">  <?php $payment= DB::table('acc_ots_payment_details')
                           ->select('amount')
                           ->where('id', '=', $payment->id)

                           ->first();
                           echo $payment->amount;echo ".00";

                           $debit=$debit+ $payment->amount;
                           $sum=$sum-$payment->amount;


                           ?></td>

                        <td  class="amount creditAmount" amount="">0.00</td>

                        <td  class="amount closingBalance" amount="">{{$sum}}.00</td>
                      </tr>
                      <?php }?>



                      <?php    if($interest){    ?>
                         <tr>
                        <td> {{$count}}</td>

                        <td class="name"style="text-align:center;padding-right:3px;">

                        <?php

                        $count++;
                         $traDateArray[$x]=Carbon::parse($traDateArray[$x])->format('d-m-Y ');
                        echo $traDateArray[$x];  ?>
                      </td>
                      <td class="name">
                         Auto Interest Added With Balance
                      </td>
                      {{-- <td class="amount"></td> --}}


                      <td  class="amount debitAmount" amount="">0.00</td>

                      <td  class="amount creditAmount" amount=""><?php $interest= DB::table('acc_ots_interest_details')
                      ->select('amount')
                      ->where('id', '=', $interest->id)
                      ->first();
                      echo $interest->amount;echo ".00";
                      $credit=$credit+$interest->amount;

                      $sum=$sum+$interest->amount;

                      ?></td>

                      <td  class="amount closingBalance" amount="">{{$sum}}.00</td>
                      </tr>
                      <?php }?>


                      <?php }?>


                        <?php $principal= DB::table('acc_ots_principal_payment')
                        ->select('amount','dueAmount','accClosingCharge','closingDate','ledgerId_fk','chequeNumber')

                        ->where('accId_fk','=',$accountNumber)
                        ->where(function ($query) use ($startDateValue,$endDateValue){
                            $query->where('closingDate','<=',$endDateValue);
                          })
                        ->first();
                        if($principal)
                        {
                          $ledger=DB::table('acc_account_ledger')
                                      ->select('name')
                                      ->where('id','=',$principal->ledgerId_fk)
                                      ->first();
                        //$principalAmount=$principal->amount+$principal->dueAmount;
                        //$principalAmount=$principalAmount+$principal->accClosingCharge;
                        $sum=$sum-$principal->amount;

                      }

                        ?>
                        @if($principal)
                          <tr>
                         <td class="name" style="text-align:center;padding-right:3px;">{{$count}} <?php $count++;?></td>
                     <td class="name"style="text-align:center;padding-right:3px;">
                          {{Carbon::parse($principal->closingDate)->format('d-m-Y ')}}
                   </td>
                   <td class="name">
                       Principal Balance Widraw {{" "}} Cheque Number:
                         {{$principal->chequeNumber}}
                        {{$ledger->name}}

                   </td>
                   {{-- <td class="amount"></td> --}}




                   <td  class="amount debitAmount" amount=""><?php

                   echo $principal->amount;echo ".00";
                   //$debit=$debit+$principal->amount;



                   ?></td>
                    <td  class="amount creditAmount" amount="">0.00</td>

                   <td  class="amount closingBalance" amount="">{{$sum}}.00</td>
                   </tr>







                   <tr>
                   <td class="name" style="text-align:center;padding-right:3px;">{{$count}} <?php $count++;?></td>
                   <td class="name" style="text-align:center;padding-right:3px;">
                   {{Carbon::parse($principal->closingDate)->format('d-m-Y ')}}
                   </td>
                   <td class="name">
                   Due Amount

                   </td>
                   {{-- <td class="amount"></td> --}}




                   <td  class="amount debitAmount" amount=""><?php

                    echo $principal->dueAmount;
                   $sum= $sum-$principal->dueAmount;



                   ?></td>
                   <td  class="amount creditAmount" amount="">0.00</td>

                   <td  class="amount closingBalance" amount="">{{$sum}}.00</td>
                   </tr>





                   <tr>
                   <td class="name" style="text-align:center;padding-right:3px;">{{$count}} <?php $count++;?></td>
                   <td class="name"style="text-align:center;padding-right:3px;">
                   {{Carbon::parse($principal->closingDate)->format('d-m-Y ')}}
                   </td>
                   <td class="name">
                    Account Closing Charge

                   </td>
                   {{-- <td class="amount"></td> --}}




                   <td  class="amount debitAmount" amount=""><?php

                    echo $principal->accClosingCharge;
                   //$debit=$debit+$principalAmount;
                       $sum=$sum-$principal->accClosingCharge;


                   ?></td>
                   <td  class="amount creditAmount" amount="">0.00</td>

                   <td  class="amount closingBalance" amount="">{{$sum}}.00</td>
                   </tr>

                   @endif
                   <tr>
                   </tr>

                    </tbody>
                    <tfoot>
                        <tr>
                          <td colspan="3" class="name" style="font-weight: bold; text-align:center;">Sub Total</td>
                          <td  class="amount debitAmount" style="font-weight: bold; padding-right:3px; text-align:right;"amount="">{{$debit}}.00</td>

                          <td  class="amount creditAmount" style="font-weight: bold;padding-right:3px;text-align:right;"amount="">{{$credit}}.00</td>

                          <td  class="amount closingBalance" style="font-weight: bold;padding-right:3px;text-align:right;"amount="">{{$sum}}.00</td>
                        </tr>


                        <tr>
                          <td colspan="3" class="name" style="font-weight: bold; text-align:center;">Total</td>
                          <td  class="amount debitAmount" style="font-weight: bold; padding-right:3px; text-align:right;"amount="">{{$debit+$debitOpenning}}.00</td>

                          <td  class="amount creditAmount" style="font-weight: bold;padding-right:3px;text-align:right;"amount="">{{$credit+$creditOpenning}}.00</td>

                          <td  class="amount closingBalance" style="font-weight: bold;padding-right:3px;text-align:right;"amount="">{{$sum}}.00</td>
                        </tr>
                    </tfoot>



                </table>


            </div>      {{-- responseDIV --}}


    	</div>
    </div>



</div>
