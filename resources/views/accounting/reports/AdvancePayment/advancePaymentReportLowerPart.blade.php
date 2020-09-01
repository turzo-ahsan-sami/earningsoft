<script type="text/javascript">
  $("#loadingModal").hide();
</script>
<div id="printDiv">
  <div id="printDivUpper">
    <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}
      <?php
      $user_company_id = Auth::user()->company_id_fk;
      $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
      ?>

      <span style="font-size:14px;">{{$company->name}}</span><br/>
      <span style="font-size:11px;">{{$company->address}}</span><br/>
      <span style="text-decoration: underline;  font-size:14px;">Advance Payment Report</span></br>

    </div>
    <div class="row" style="text-align: right;color:black;">
      <span style="font-size:13px;padding-right:1%;">Statement Period: {{Carbon\Carbon::parse($fromDate)->format('d-m-Y ') }}   {{" "}} To {{Carbon\Carbon::parse($toDate)->format('d-m-Y ') }}</span><br/>
    </div>

  </div>
  <div id="printDiv1">
    <div class="row">
      <div class="col-md-12">
        <div class="table-responsive">
          <?php use Carbon\Carbon;
          if($tableType ==1)
            { $count=0;
              ?>
              <table class="table table-striped table-bordered" width="100%" id="reportingTable" border="1pt solid ash" style="color:black; border-collapse: collapse;/*table-layout: fixed;*/ " >
                <thead>
                  <tr>
                    <th rowspan="2" style="width:3%;">SL NO</th>

                    <th rowspan="2" style="width:20%">Name</th>
                    <th rowspan="2" style="width:20%">ID Number</th>
                    <th rowspan="2" style="width:11%">Branch</th>


                    <th rowspan="2"style="">Opening Balance</th>
                    <th rowspan="1" colspan="2">Current Period</th>
                    <th rowspan="2">Closing Balance</th>
                  </tr>
                  <th rowspan="1"style="">Payment</th>
                  <th rowspan="1"style="">Received</th>
                </thead>
                <tbody>
                  <?php

                  $registerTypeArray= array();

                  foreach( $collectionsAll as $collectionAll)
                  {
                    array_push($registerTypeArray,$collectionAll["registerType"]);
                  }

                  $registerTypeArray=array_unique($registerTypeArray);
                  sort($registerTypeArray);
                  $registerTypeArrayLength= sizeof($registerTypeArray);

                  $totalAllOpenning=0;
                  $totalAllClosing=0;
                  $totalAllPayment=0;
                  $totalAllreceive=0;
                  ?>
                  @for($x=0;$x<$registerTypeArrayLength;$x++)
                  <tr class="singleRow">
                   @php
                   $registerName= DB::table('acc_adv_register_type')
                   ->select('name')
                   ->where('id',$registerTypeArray[$x])
                   ->first();
                   @endphp
                   <td colspan="8" style="text-align:left;padding-left:5px;font-size:14px;" class="text-bold">{{$registerName->name}}</td>
                 </tr>
                 @php
                 $subtotalAllOpenning=0;
                 $subtotalAllClosing=0;
                 $subtotalAllPayment=0;
                 $subtotalAllReceive=0;
                 @endphp
                 @foreach( $collectionsAll as $collectionAll)
                 @if($collectionAll["registerType"]== $registerTypeArray[$x])
                 <tr><td>{{++$count}}</td>

                  @php
                  $employeeInfo=DB::table('hr_emp_general_info as t1')
                  ->join('hr_emp_org_info as t2', 't1.id','=','t2.emp_id_fk')
                  ->join('gnr_branch as t3', 't3.id','=','t2.branch_id_fk')
                  ->select('t1.emp_name_english','t1.emp_id','t3.name as branchName')
                  ->where('t1.id',$collectionAll["id"])
                  ->first();
                  $supplierInfo=DB::table('gnr_supplier as t1')

                  ->select('t1.name')
                  ->where('t1.id',$collectionAll["id"])
                  ->first();
                  $houseOwnerInfo=DB::table('gnr_house_Owner as t1')
                  ->join('gnr_branch as t2', 't2.id','=','t1.branchId')
                  ->select('t1.houseOwnerName','t2.name as branchName')
                  ->where('t1.id',$collectionAll["id"])
                  ->first();




                  @endphp
                  @if($collectionAll["type"]=="1")

                  <td style="text-align:left;">{{$employeeInfo->emp_name_english}}</td>
                  <td style="text-align:center;">{{$employeeInfo->emp_id}}</td>
                  <td style="text-align:left;">{{$employeeInfo->branchName}}</td>


                  @if($collectionAll["openningBalance"]==0)
                  <td style="text-align:right;padding-right:5px;font-size:14px;"> - </td>
                  @else
                  <td style="text-align:right;">{{$collectionAll["openningBalance"]}}.00</td>
                  @endif
                  @if($collectionAll["amount"]==0)
                  <td style="text-align:right;padding-right:5px;"> - </td>
                  @else
                  <td style="text-align:right;">{{$collectionAll["amount"]}}.00</td>
                  @endif
                  @if($collectionAll["payment"]==0)
                  <td style="text-align:right;">-</td>
                  @else
                  <td style="text-align:right;">{{$collectionAll["payment"]}}.00</td>
                  @endif
                  @if($collectionAll["closingBalance"]==0)
                  <td style="text-align:right;padding-right:5px;">-</td>
                  @else
                  <td style="text-align:right;">{{$collectionAll["closingBalance"]}}.00</td>
                  @endif


                  @endif

                  @if($collectionAll["type"]=="2")

                  <td style="text-align:left;">{{$supplierInfo->name}}</td>
                  <td style="text-align:center;"></td>
                  <td style="text-align:left;"></td>


                  @if($collectionAll["openningBalance"]==0)
                  <td style="text-align:right;padding-right:5px;font-size:14px;"> - </td>
                  @else
                  <td style="text-align:right;">{{$collectionAll["openningBalance"]}}.00</td>
                  @endif
                  @if($collectionAll["amount"]==0)
                  <td style="text-align:right;padding-right:5px;"> - </td>
                  @else
                  <td style="text-align:right;">{{$collectionAll["amount"]}}.00</td>
                  @endif
                  @if($collectionAll["payment"]==0)
                  <td style="text-align:right;">-</td>
                  @else
                  <td style="text-align:right;">{{$collectionAll["payment"]}}.00</td>
                  @endif
                  @if($collectionAll["closingBalance"]==0)
                  <td style="text-align:right;padding-right:5px;">-</td>
                  @else
                  <td style="text-align:right;">{{$collectionAll["closingBalance"]}}.00</td>
                  @endif
                  @endif
                  @if($collectionAll["type"]=="3")

                  <td style="text-align:left;">{{$houseOwnerInfo->houseOwnerName}}</td>
                  <td style="text-align:center;"></td>
                  <td style="text-align:left;">{{$houseOwnerInfo->branchName}}</td>


                  @if($collectionAll["openningBalance"]==0)
                  <td style="text-align:right;padding-right:5px;font-size:14px;"> - </td>
                  @else
                  <td style="text-align:right;">{{$collectionAll["openningBalance"]}}.00</td>
                  @endif
                  @if($collectionAll["amount"]==0)
                  <td style="text-align:right;padding-right:5px;"> - </td>
                  @else
                  <td style="text-align:right;">{{$collectionAll["amount"]}}.00</td>
                  @endif
                  @if($collectionAll["payment"]==0)
                  <td style="text-align:right;">-</td>
                  @else
                  <td style="text-align:right;">{{$collectionAll["payment"]}}.00</td>
                  @endif
                  @if($collectionAll["closingBalance"]==0)
                  <td style="text-align:right;padding-right:5px;">-</td>
                  @else
                  <td style="text-align:right;">{{$collectionAll["closingBalance"]}}.00</td>
                  @endif
                  @endif
                  <?php
                  $subtotalAllOpenning=$subtotalAllOpenning+$collectionAll["openningBalance"];
                  $subtotalAllClosing=$subtotalAllClosing+$collectionAll["closingBalance"];
                  $subtotalAllPayment=$subtotalAllPayment+$collectionAll["payment"];
                  $subtotalAllReceive=$subtotalAllReceive+$collectionAll["amount"];
                  ?>

                </tr>
                @endif


                @endforeach

                <tr class="subTotalData">
                  <td colspan="4" class="text-bold">Subtotal</td>
                  <td style="text-align:right;padding-right:5px;"  class="text-bold">{{$subtotalAllOpenning}}.00</td>

                  <td style="text-align:right;padding-right:5px;" class="text-bold">{{$subtotalAllPayment}}.00</td>
                  <td style="text-align:right;padding-right:5px;" class="text-bold">{{$subtotalAllReceive}}.00</td>
                  <td style="text-align:right;padding-right:5px;" class="text-bold">{{$subtotalAllClosing}}.00</td>
                  @php
                  $totalAllOpenning=$totalAllOpenning+$subtotalAllOpenning;
                  $totalAllClosing=$totalAllClosing+$subtotalAllClosing;
                  $totalAllPayment=$totalAllPayment+$subtotalAllPayment;
                  $totalAllreceive=$totalAllreceive+$subtotalAllReceive;
                  @endphp

                </tr>

                @endfor

              </tbody>

              <tfoot>
                <tr>
                 <td colspan="4" class="name" style="font-weight: bold; text-align:center;font-size:13px;" class="text-bold">Total</td>
                 <td  style="text-align:right;padding-right:5px;font-weight: bold;font-size:12px;"class="text-bold">{{$totalAllOpenning}}.00</td>
                 <td style="text-align:right;padding-right:5px;font-weight: bold;font-size:12px;"class="text-bold">{{$totalAllPayment}}.00</td>
                 <td style="text-align:right;padding-right:5px;font-weight: bold;font-size:12px;"class="text-bold">{{$totalAllreceive}}.00</td>
                 <td style="text-align:right;padding-right:5px;font-weight: bold;font-size:12px;"class="text-bold">{{$totalAllClosing}}.00</td>
               </tr>
             </tfoot>

           </table>
           <?php }?>

           <?php
           if($tableType ==2)
            { $count=0;
              ?>
              <table style="width:100%; font-size: 12px;font-weight: bold; text-align: right; color:black;" id="information"  >
                <tbody>
                  @php
                  $empInfoTableHeader = DB::table('hr_emp_general_info as t1')
                  ->join('hr_emp_org_info as t2','t1.id','t2.emp_id_fk')
                  ->where('t1.id',$empId)
                  ->select('t1.*','t2.branch_id_fk')
                  ->first();
                  $empInfoTableHeaderBranchName = DB::table('gnr_branch')
                  ->select('name')
                  ->where('id',$empInfoTableHeader->branch_id_fk)
                  ->first();
                  @endphp
                  <tr>
                   <td width="6%" style="text-align: left">Name</td>
                   <td style="padding-right:10px">: </td>
                   <td width="60%" style="text-align: left">{{$empInfoTableHeader->emp_name_english}}</td>
                   <td  style="text-align: right">Address</td>
                   <td style="text-align: right">:</td>
                   <td width="12%" style="text-align: right">{{$empInfoTableHeader->email}}</td>
                         <!--<php if($AccountNumber->accStatus == 1)
                                                                {echo "Open";}
                                                                else{echo "Closed";}?>!-->
                                                              </tr>


                                                              <tr>
                                                               <td style="text-align: left">Account No</td>
                                                               <td style="padding-right:10px">:</td>
                                                               <td style="text-align: left">{{$empInfoTableHeader->emp_id}}</td>
                                                               <td style="text-align: right">Phone No</td>
                                                               <td style="text-align: right">:</td>
                                                               <td style="text-align: right">{{$empInfoTableHeader->mobile_number}}</td>
                                                             </tr>


                                                             <tr>
                                                               <td style="text-align: left">Branch</td>
                                                               <td style="padding-right:10px">:</td>
                                                               <td style="text-align: left">{{$empInfoTableHeaderBranchName->name}}</td>
                                                               <td style="text-align: right">Print Date</td>
                                                               <td style="text-align: right">:</td>
                                                               <td style="text-align: right; "><?php  echo date("d/m/Y") ."  ".date("h:i:sa"); ?></td>



                                                             </tr>

                                                           </tbody>

                                                         </table>
                                                         <table class="table table-striped table-bordered" id="reportingTable" border="1pt solid ash" style="color:black; border-collapse: collapse;/*table-layout: fixed;*/ " >
                                                          <thead>
                                                            <tr>
                                                              <th rowspan="2" style="width:50px;">SL NO</th>
                                                              <th rowspan="2"style="width:10%">Date</th>
                                                              <!-- <th rowspan="2" style="width:20%">Employee</th> -->
                                                              <th rowspan="2"style="width:15%">Narration</th>
                                                              <th rowspan="2"style="width:15%">Opening Balance</th>
                                                              <th rowspan="1" colspan="2">Current Period</th>
                                                              <th rowspan="2">Closing Balance</th>
                                                            </tr>
                                                            <th rowspan="1"style="width:15%">Payment</th>
                                                            <th rowspan="1"style="width:15%">Received</th>
                                                          </thead>
                                                          <tbody>

                                                            <?php

                                                            $max=sizeof($generateAndPaymentDatesEmployee);
                                                            $regAmount=0;
                                                            $payAmount=0;
                                                            $totalOpening=0;
                                                            $totalClosing=0;
                                                            $count=0;
                                                            $closingBalance=0;
                                                            $subOpeningEmployee=0;
                                                            $subClosingEmployee=0;
                                                            $subRegEmployee=0;
                                                            $subPayEmployee=0;

                                                            for ($x = 0; $x <$max; $x++)
                                                            {

                                                              ?>
                                                              @foreach($advanceGeneratesEmployee as $advanceGenerateEmployee)
                                                              @if($advanceGenerateEmployee->advPaymentDate == $generateAndPaymentDatesEmployee[$x])
                                                              <tr>
                                                                <td>{{++$count}}</td>
                                                                <td>{{Carbon::parse($advanceGenerateEmployee->advPaymentDate)->format('d-m-Y ') }}</td>
                                                                @php $advanceGenerateEmployee->advPaymentDate=null; @endphp
                                                                {{--    <td>{{$advanceGenerateEmployee->emp_name_english}}</td> --}}
                                                                <td>Payment</td>

                                                                <td style="text-align:right;">{{$openningBalanceEmployee}}.00</td>
                                                                @php
                                                                $regAmount=$regAmount+$advanceGenerateEmployee->amount;
                                                                $totalOpening=$totalOpening+$openningBalanceEmployee;
                                                                $closingBalance=$openningBalanceEmployee+$advanceGenerateEmployee->amount;
                                                                $totalClosing=$totalClosing+$closingBalance;
                                                                $openningBalanceEmployee=$closingBalance;
                                                                @endphp
                                                                <td style="text-align:right;">{{$advanceGenerateEmployee->amount}}.00</td>

                                                                <td style="text-align:right;">0.00</td>

                                                                <!-- <td></td> -->
                                                                <td style="text-align:right;">{{$closingBalance}}.00</td>

                                                              </tr>
                                                              @endif
                                                              @endforeach

                                                              @foreach($advancePaymentsEmployee as $advancePaymentEmployee)

                                                              @if($advancePaymentEmployee->receivePaymentDate == $generateAndPaymentDatesEmployee[$x])
                                                              <tr>
                                                                <td>{{++$count}}</td>
                                                                <td>{{Carbon::parse($advancePaymentEmployee->receivePaymentDate)->format('d-m-Y ') }}</td>
                                                                @php $advancePaymentEmployee->receivePaymentDate=null; @endphp
                                                                {{--    <td>{{$advancePaymentEmployee->emp_name_english}}</td>--}}
                                                                <td>Received</td>


                                                                <td style="text-align:right;">{{$openningBalanceEmployee}}.00</td>
                                                                @php
                                                                $payAmount=$payAmount+$advancePaymentEmployee->amount;
                                                                $totalOpening=$totalOpening+$openningBalanceEmployee;
                                                                $closingBalance=$openningBalanceEmployee-$advancePaymentEmployee->amount;
                                                                $totalClosing=$totalClosing+$closingBalance;
                                                                $openningBalanceEmployee=$closingBalance;

                                                                @endphp
                                                                <td style="text-align:right;">0.00</td>

                                                                <td style="text-align:right;">{{$advancePaymentEmployee->amount}}.00</td>


                                                                <!-- <td></td> -->
                                                                <td style="text-align:right;">{{$closingBalance}}.00</td>

                                                              </tr>
                                                              @endif

                                                              @endforeach








                                                              <?php

                                                            }
                                                            ?>
                                                          </tbody>

                                                          <tfoot>
                                                           <td colspan="3" style="text-align:center;" class="text-bold"> Total</td>
                                                           <td style="text-align:right;padding-right:5px;"class="text-bold">{{$totalOpening}}.00</td>
                                                           <td style="text-align:right;padding-right:5px;"class="text-bold">{{$regAmount}}.00</td>
                                                           <td style="text-align:right;padding-right:5px;"class="text-bold">{{$payAmount}}.00</td>
                                                           <td style="text-align:right;padding-right:5px;"class="text-bold">{{$totalClosing}}.00</td>
                                                         </tfoot>

                                                       </table>
                                                       <?php }?>
                                                       <?php
                                                       if($tableType ==3)
                                                        { $count=0;
                                                          ?>
                                                          <table style="width:100%; font-size: 12px;font-weight: bold; text-align: right; color:black;" id="information"  >
                                                            <tbody>
                                                              @php
                                                              $supInfoTableHeader = DB::table('gnr_supplier as t1')

                                                              ->where('t1.id',$supId)
                                                              ->select('t1.*')
                                                              ->first();

                                                              @endphp
                                                              <tr>
                                                               <td width="6%" style="text-align: left">Name</td>
                                                               <td style="padding-right:10px">: </td>
                                                               <td width="60%" style="text-align: left">{{$supInfoTableHeader->name}}</td>
                                                               <td  style="text-align: right">Email</td>
                                                               <td style="text-align: right">:</td>
                                                               <td width="12%" style="text-align: right">{{$supInfoTableHeader->email}}</td>
                         <!--<php if($AccountNumber->accStatus == 1)
                                                                {echo "Open";}
                                                                else{echo "Closed";}?>!-->
                                                              </tr>


                                                              <tr>
                                                               <td style="text-align: left">Account No</td>
                                                               <td style="padding-right:10px">:</td>
                                                               <td style="text-align: left"></td>
                                                               <td style="text-align: right">Phone No</td>
                                                               <td style="text-align: right">:</td>
                                                               <td style="text-align: right">{{$supInfoTableHeader->phone}}</td>
                                                             </tr>


                                                             <tr>
                                                               <td style="text-align: left">Branch</td>
                                                               <td style="padding-right:10px">:</td>
                                                               <td style="text-align: left"></td>
                                                               <td style="text-align: right">Print Date</td>
                                                               <td style="text-align: right">:</td>
                                                               <td style="text-align: right; "><?php  echo date("d/m/Y") ."  ".date("h:i:sa"); ?></td>



                                                             </tr>

                                                           </tbody>

                                                         </table>
                                                         <table class="table table-striped table-bordered" id="reportingTable" border="1pt solid ash" style="color:black; border-collapse: collapse;/*table-layout: fixed;*/ " >
                                                          <thead>
                                                            <tr>
                                                              <th rowspan="2" style="width:50px;">SL NO</th>
                                                              <th rowspan="2"style="width:10%">Date</th>
                                                              <th rowspan="2" style="width:20%">Narration</th>

                                                              <th rowspan="2"style="width:15%">Opening Balance</th>
                                                              <th rowspan="1" colspan="2">Current Period</th>
                                                              <th rowspan="2">Closing Balance</th>
                                                            </tr>
                                                            <th rowspan="1"style="width:15%">Payment</th>
                                                            <th rowspan="1"style="width:15%">Received</th>
                                                          </thead>
                                                          <tbody>
                                                            <?php

                                                            $max=sizeof($generateAndPaymentDatesSupplier);
                                                            $regAmountSupplier=0;
                                                            $payAmountSupplier=0;
                                                            $totalOpeningSupplier=0;
                                                            $totalClosingSupplier=0;
                                                            $countSupplier=0;
                                                            $closingBalanceSupplier=0;
                                                            $subOpeningSupplier=0;
                                                            $subClosingSupplier=0;
                                                            $subRegSupplier=0;
                                                            $subPaySupplier=0;

                                                            for ($x = 0; $x <$max; $x++)
                                                            {

                                                              ?>
                                                              @foreach($advanceGeneratesSupplier as $advanceGenerateSupplier)
                                                              @if($advanceGenerateSupplier->advPaymentDate == $generateAndPaymentDatesSupplier[$x])
                                                              <tr>
                                                                <td>{{++$countSupplier}}</td>
                                                                <td>{{Carbon::parse($advanceGenerateSupplier->advPaymentDate)->format('d-m-Y ') }}</td>
                                                                @php $advanceGenerateSupplier->advPaymentDate=null; @endphp
                                                                {{--<td>{{$advanceGenerateSupplier->name}}</td>--}}
                                                                <td>Payment</td>

                                                                <td style="text-align:right;">{{$openningBalanceSupplier}}.00</td>
                                                                @php
                                                                $regAmountSupplier=$regAmountSupplier+$advanceGenerateSupplier->amount;
                                                                $totalOpeningSupplier=$totalOpeningSupplier+$openningBalanceSupplier;
                                                                $closingBalanceSupplier=$openningBalanceSupplier+$advanceGenerateSupplier->amount;
                                                                $totalClosingSupplier=$totalClosingSupplier+$closingBalanceSupplier;
                                                                $openningBalanceSupplier=$closingBalanceSupplier;
                                                                @endphp
                                                                <td style="text-align:right;">{{$advanceGenerateSupplier->amount}}.00</td>

                                                                <td style="text-align:right;">0.00</td>

                                                                <!-- <td></td> -->
                                                                <td style="text-align:right;">{{$closingBalanceSupplier}}.00</td>

                                                              </tr>
                                                              @endif
                                                              @endforeach

                                                              @foreach($advancePaymentsSupplier as $advancePaymentSupplier)

                                                              @if($advancePaymentSupplier->receivePaymentDate == $generateAndPaymentDatesSupplier[$x])
                                                              <tr>
                                                                <td>{{++$countSupplier}}</td>
                                                                <td>{{Carbon::parse($advancePaymentSupplier->receivePaymentDate)->format('d-m-Y ') }}</td>
                                                                @php $advancePaymentSupplier->receivePaymentDate=null; @endphp
                                                                {{--<td>{{$advancePaymentSupplier->name}}</td>--}}
                                                                <td>Payment</td>

                                                                <td style="text-align:right;">{{$openningBalanceSupplier}}.00</td>
                                                                @php
                                                                $payAmountSupplier=$payAmountSupplier+$advancePaymentSupplier->amount;
                                                                $totalOpeningSupplier=$totalOpeningSupplier+$openningBalanceSupplier;
                                                                $closingBalanceSupplier=$openningBalanceSupplier-$advancePaymentSupplier->amount;
                                                                $totalClosingSupplier=$totalClosingSupplier+$closingBalanceSupplier;
                                                                $openningBalanceSupplier=$closingBalanceSupplier;

                                                                @endphp
                                                                <td style="text-align:right;">0.00</td>

                                                                <td style="text-align:right;">{{$advancePaymentSupplier->amount}}.00</td>


                                                                <!-- <td></td> -->
                                                                <td style="text-align:right;">{{$closingBalanceSupplier}}.00</td>

                                                              </tr>
                                                              @endif

                                                              @endforeach








                                                              <?php

                                                            }
                                                            ?>
                                                          </tbody>

                                                          <tfoot>
                                                           <td colspan="3" style="text-align:center;" class="text-bold"> Total</td>
                                                           <td style="text-align:right;padding-right:5px;"class="text-bold">{{$totalOpeningSupplier}}.00</td>
                                                           <td style="text-align:right;padding-right:5px;"class="text-bold">{{$regAmountSupplier}}.00</td>
                                                           <td style="text-align:right;padding-right:5px;"class="text-bold">{{$payAmountSupplier}}.00</td>
                                                           <td style="text-align:right;padding-right:5px;"class="text-bold">{{$totalClosingSupplier}}.00</td>
                                                         </tfoot>

                                                       </table>
                                                       <?php }?>
                                                       <?php
                                                       if($tableType ==4)
                                                        { $count=0;
                                                          ?>
                                                          <table style="width:100%; font-size: 12px;font-weight: bold; text-align: right; color:black;" id="information"  >
                                                            <tbody>
                                                              @php
                                                              $houseOwnerInfoTableHeader = DB::table('gnr_house_Owner as t1')
                                                              ->where('t1.id',$houseId)
                                                              ->select('t1.*')
                                                              ->first();
                                                              $houseOwnerInfoTableHeaderBranchName = DB::table('gnr_branch')
                                                              ->select('name')
                                                              ->where('id',$houseOwnerInfoTableHeader->branchId)
                                                              ->first();
                                                              @endphp
                                                              <tr>
                                                               <td width="6%" style="text-align: left">Name</td>
                                                               <td style="padding-right:10px">: </td>
                                                               <td width="60%" style="text-align: left">{{$houseOwnerInfoTableHeader->houseOwnerName}}</td>
                                                               <td  style="text-align: right">Address</td>
                                                               <td style="text-align: right">:</td>
                                                               <td width="12%" style="text-align: right">{{$houseOwnerInfoTableHeader->emailAddress}}</td>
                         <!--<php if($AccountNumber->accStatus == 1)
                                                                {echo "Open";}
                                                                else{echo "Closed";}?>!-->
                                                              </tr>


                                                              <tr>
                                                               <td style="text-align: left">Account No</td>
                                                               <td style="padding-right:10px">:</td>
                                                               <td style="text-align: left"></td>
                                                               <td style="text-align: right">Phone No</td>
                                                               <td style="text-align: right">:</td>
                                                               <td style="text-align: right">{{$houseOwnerInfoTableHeader->phoneNumber}}</td>
                                                             </tr>


                                                             <tr>
                                                               <td style="text-align: left">Branch</td>
                                                               <td style="padding-right:10px">:</td>
                                                               <td style="text-align: left">{{$houseOwnerInfoTableHeaderBranchName->name}}</td>
                                                               <td style="text-align: right">Print Date</td>
                                                               <td style="text-align: right">:</td>
                                                               <td style="text-align: right; "><?php  echo date("d/m/Y") ."  ".date("h:i:sa"); ?></td>



                                                             </tr>

                                                           </tbody>

                                                         </table>
                                                         <table class="table table-striped table-bordered" id="reportingTable" border="1pt solid ash" style="color:black; border-collapse: collapse;/*table-layout: fixed;*/ " >
                                                          <thead>
                                                            <tr>
                                                              <th rowspan="2" style="width:50px;">SL NO</th>
                                                              <th rowspan="2"style="width:10%">Date</th>
                                                              <th rowspan="2" style="width:20%">Narration</th>

                                                              <th rowspan="2"style="width:15%">Opening Balance</th>
                                                              <th rowspan="1" colspan="2">Current Period</th>
                                                              <th rowspan="2">Closing Balance</th>
                                                            </tr>
                                                            <th rowspan="1"style="width:15%">Payment</th>
                                                            <th rowspan="1"style="width:15%">Received</th>
                                                          </thead>
                                                          <tbody>
                                                            <?php

                                                            $max=sizeof($generateAndPaymentDatesHouseOwner);
                                                            $regAmountHouseOwner=0;
                                                            $payAmountHouseOwner=0;
                                                            $totalOpeningHouseOwner=0;
                                                            $totalClosingHouseOwner=0;
                                                            $countHouseOwner=0;
                                                            $closingBalanceHouseOwner=0;
                                                            $subOpeningHouseOwner=0;
                                                            $subClosingHouseOwner=0;
                                                            $subRegHouseOwner=0;
                                                            $subPayHouseOwner=0;

                                                            for ($x = 0; $x <$max; $x++)
                                                            {

                                                              ?>
                                                              @foreach($advanceGeneratesHouseOwner as $advanceGenerateHouseOwner)
                                                              @if($advanceGenerateHouseOwner->advPaymentDate == $generateAndPaymentDatesHouseOwner[$x])
                                                              <tr>
                                                                <td>{{++$countHouseOwner}}</td>
                                                                <td>{{Carbon::parse($advanceGenerateHouseOwner->advPaymentDate)->format('d-m-Y ') }}</td>
                                                                @php $advanceGenerateHouseOwner->advPaymentDate=null; @endphp
                                                                {{--<td>{{$advanceGenerateHouseOwner->houseOwnerName}}</td>--}}
                                                                <td>Payment</td>

                                                                <td style="text-align:right;">{{$openningBalanceHouseOwner}}.00</td>
                                                                @php
                                                                $regAmountHouseOwner=$regAmountHouseOwner+$advanceGenerateHouseOwner->amount;
                                                                $totalOpeningHouseOwner=$totalOpeningHouseOwner+$openningBalanceHouseOwner;
                                                                $closingBalanceHouseOwner=$openningBalanceHouseOwner+$advanceGenerateHouseOwner->amount;
                                                                $totalClosingHouseOwner=$totalClosingHouseOwner+$closingBalanceHouseOwner;
                                                                $openningBalanceHouseOwner=$closingBalanceHouseOwner;
                                                                @endphp
                                                                <td style="text-align:right;">{{$advanceGenerateHouseOwner->amount}}.00</td>

                                                                <td style="text-align:right;">0.00</td>

                                                                <!-- <td></td> -->
                                                                <td style="text-align:right;">{{$closingBalanceHouseOwner}}.00</td>

                                                              </tr>
                                                              @endif
                                                              @endforeach

                                                              @foreach($advancePaymentsHouseOwner as $advancePaymentHouseOwner)

                                                              @if($advancePaymentHouseOwner->receivePaymentDate == $generateAndPaymentDatesHouseOwner[$x])
                                                              <tr>
                                                                <td>{{++$countSupplier}}</td>
                                                                <td>{{Carbon::parse($advancePaymentHouseOwner->receivePaymentDate)->format('d-m-Y ') }}</td>
                                                                @php $advancePaymentHouseOwner->receivePaymentDate=null; @endphp
                                                                {{--<td>{{$advancePaymentHouseOwner->name}}</td>--}}
                                                                <td>Received</td>

                                                                <td style="text-align:right;">{{$openningBalanceHouseOwner}}.00</td>
                                                                @php
                                                                $payAmountHouseOwner=$payAmountHouseOwner+$advancePaymentHouseOwner->amount;
                                                                $totalOpeningHouseOwner=$totalOpeningHouseOwner+$openningBalanceHouseOwner;
                                                                $closingBalanceHouseOwner=$openningBalanceHouseOwner-$advancePaymentHouseOwner->amount;
                                                                $totalClosingHouseOwner=$totalClosingHouseOwner+$closingBalanceHouseOwner;
                                                                $openningBalanceHouseOwner=$closingBalanceHouseOwner;

                                                                @endphp
                                                                <td style="text-align:right;">0.00</td>

                                                                <td style="text-align:right;">{{$advancePaymentHouseOwner->amount}}.00</td>


                                                                <!-- <td></td> -->
                                                                <td style="text-align:right;">{{$closingBalanceHouseOwner}}.00</td>

                                                              </tr>
                                                              @endif

                                                              @endforeach








                                                              <?php

                                                            }
                                                            ?>
                                                          </tbody>

                                                          <tfoot>
                                                           <td colspan="3" style="text-align:center;" class="text-bold"> Total</td>
                                                           <td style="text-align:right;padding-right:5px;" class="text-bold">{{$totalOpeningHouseOwner}}.00</td>
                                                           <td style="text-align:right;padding-right:5px;" class="text-bold">{{$regAmountHouseOwner}}.00</td>
                                                           <td style="text-align:right;padding-right:5px;" class="text-bold">{{$payAmountHouseOwner}}.00</td>
                                                           <td style="text-align:right;padding-right:5px;" class="text-bold">{{$totalClosingHouseOwner}}.00</td>
                                                         </tfoot>
                                                       </table>
                                                       <?php }?>
                                                     </div>
                                                   </div>
                                                 </div>
                                               </div>
                                             </div>
