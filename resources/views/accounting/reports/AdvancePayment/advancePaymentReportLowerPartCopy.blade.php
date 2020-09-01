
<div id="printDivUpper">
    <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}
        <?php
        $user_company_id = Auth::user()->company_id_fk;
        $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
        ?>
        <span style="text-decoration: underline;  font-size:14px;">Advance Payment Report</span></br>
        <span style="font-size:14px;">{{$company->name}}</span><br/>
        <span style="font-size:11px;">{{$company->address}}</span><br/>
        <span style="font-size:11px;">Statement Period: {{Carbon\Carbon::parse($fromDate)->format('d-m-Y ') }}   {{" "}} To {{Carbon\Carbon::parse($toDate)->format('d-m-Y ') }}</span><br/>
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
                    <table class="table table-striped table-bordered" id="reportingTable" border="1pt solid ash" style="color:black; border-collapse: collapse;/*table-layout: fixed;*/ " >
                        <thead>
                            <tr>
                                <th rowspan="2" style="width:50px;">SL NO</th>

                                <th rowspan="2" style="width:10%">Employee</th>
                                <th rowspan="2" style="width:10%">ID</th>
                                <th rowspan="2" style="width:10%">Branch</th>


                                <th rowspan="2"style="width:15%">Opening Balance</th>
                                <th rowspan="1" colspan="2">Current Period</th>
                                <th rowspan="2">Closing Balance</th>
                            </tr>
                            <th rowspan="1"style="width:15%">Amount</th>
                            <th rowspan="1"style="width:15%">Payment Amount</th>
                        </thead>
                        <tbody>
                            <?php
                               $registerTypeEmployeeArray= array();
                                foreach( $collectionsEmployee as $collectionEmployee)
                                {
                                      array_push($registerTypeEmployeeArray,$collectionEmployee["registerType"]);
                                }

                                $registerTypeEmployeeArray=array_unique($registerTypeEmployeeArray);
                                sort($registerTypeEmployeeArray);
                                $registerTypeEmployeeArrayLength= sizeof($registerTypeEmployeeArray);

                            ?>
                            @for($x=0;$x<$registerTypeEmployeeArrayLength;$x++)
                               <tr>
                                   @php $registerEmployeeName= DB::table('acc_adv_register_type')
                                                                    ->select('name')
                                                                    ->where('id',$registerTypeEmployeeArray[$x])
                                                                    ->first();

                                  @endphp
                                   <td colspan="8" style="text-align:left;padding-left:5px;font-size:18px;">{{$registerEmployeeName->name}}</td>
                               </tr>
                             @foreach( $collectionsEmployee as $collectionEmployee)
                                  @if($collectionEmployee["registerType"]== $registerTypeEmployeeArray[$x])
                                       <tr><td>{{++$count}}</td>

                                        @php
                                        $employeeInfo=DB::table('hr_emp_general_info as t1')
                                                        ->join('hr_emp_org_info as t2', 't1.id','=','t2.emp_id_fk')
                                                        ->join('gnr_branch as t3', 't3.id','=','t2.branch_id_fk')
                                                        ->select('t1.emp_name_english','t1.emp_id','t3.name as branchName')
                                                        ->where('t1.id',$collectionEmployee["id"])
                                                        ->first();



                                        @endphp<td style="text-align:left;">{{$employeeInfo->emp_name_english}}</td>
                                        <td style="text-align:center;">{{$employeeInfo->emp_id}}</td>
                                        <td style="text-align:left;">{{$employeeInfo->branchName}}</td>


                                        @if($collectionEmployee["openningBalance"]==0)
                                            <td style="text-align:right;padding-right:5px;font-size:14px;"> - </td>
                                        @else
                                            <td style="text-align:right;">{{$collectionEmployee["openningBalance"]}}.00</td>
                                        @endif
                                        @if($collectionEmployee["amount"]==0)
                                           <td style="text-align:right;padding-right:5px;"> - </td>
                                        @else
                                           <td style="text-align:right;">{{$collectionEmployee["amount"]}}.00</td>
                                        @endif
                                        @if($collectionEmployee["payment"]==0)
                                          <td style="text-align:right;">-</td>
                                        @else
                                        <td style="text-align:right;">{{$collectionEmployee["payment"]}}.00</td>
                                        @endif
                                        @if($collectionEmployee["closingBalance"]==0)
                                          <td style="text-align:right;padding-right:5px;">-</td>
                                        @else
                                        <td style="text-align:right;">{{$collectionEmployee["closingBalance"]}}.00</td>
                                        @endif

                                    </tr>
                                  @endif
                            @endforeach
                          @endfor

                        </tbody>

                        <tfoot>
                            <tr>
                               <td colspan="4" class="name" style="font-weight: bold; text-align:center;">Sub Total</td>
                               <td style="text-align:right;">{{$subTotalOpBalanceEmp}}.00</td>
                               <td style="text-align:right;">{{$subTotalDebitAmountEmp}}.00</td>
                               <td style="text-align:right;">{{$subTotalCreditAmountEmp}}.00</td>
                               <td style="text-align:right;">{{$subTotalClosingBalanceEmp}}.00</td>
                           </tr>
                        </tfoot>

                    </table>
                <?php }?>

                <?php
                if($tableType ==2)
                { $count=0;
                    ?>
                    <table class="table table-striped table-bordered" id="reportingTable" border="1pt solid ash" style="color:black; border-collapse: collapse;/*table-layout: fixed;*/ " >
                        <thead>
                            <tr>
                                <th rowspan="2" style="width:50px;">SL NO</th>
                                <th rowspan="2"style="width:10%">Date</th>
                                <th rowspan="2" style="width:20%">Employee</th>

                                <th rowspan="2"style="width:15%">Opening Balance</th>
                                <th rowspan="1" colspan="2">Current Period</th>
                                <th rowspan="2">Closing Balance</th>
                            </tr>
                            <th rowspan="1"style="width:15%">Amount</th>
                            <th rowspan="1"style="width:15%">Payment Amount</th>
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
                                    <td>{{$advanceGenerateEmployee->emp_name_english}}</td>

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
                                    <td>{{$advancePaymentEmployee->emp_name_english}}</td>

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
                             <td colspan="3" style="text-align:center;"> Total</td>
                             <td style="text-align:right;">{{$totalOpening}}.00</td>
                              <td style="text-align:right;">{{$regAmount}}.00</td>
                               <td style="text-align:right;">{{$payAmount}}.00</td>
                                <td style="text-align:right;">{{$totalClosing}}.00</td>
                        </tfoot>

                    </table>
                <?php }?>
                <?php
                if($tableType ==3)
                { $count=0;
                    ?>
                    <table class="table table-striped table-bordered" id="reportingTable" border="1pt solid ash" style="color:black; border-collapse: collapse;/*table-layout: fixed;*/ " >
                        <thead>
                            <tr>
                                <th rowspan="2" style="width:50px;">SL NO</th>
                                <th rowspan="2"style="width:10%">Date</th>
                                <th rowspan="2" style="width:20%">Supplier</th>

                                <th rowspan="2"style="width:15%">Opening Balance</th>
                                <th rowspan="1" colspan="2">Current Period</th>
                                <th rowspan="2">Closing Balance</th>
                            </tr>
                            <th rowspan="1"style="width:15%">Amount</th>
                            <th rowspan="1"style="width:15%">Payment Amount</th>
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
                                    <td>{{$advanceGenerateSupplier->name}}</td>

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
                                    <td>{{$advancePaymentSupplier->name}}</td>

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
                             <td colspan="3" style="text-align:center;"> Total</td>
                             <td style="text-align:right;">{{$totalOpeningSupplier}}.00</td>
                              <td style="text-align:right;">{{$regAmountSupplier}}.00</td>
                               <td style="text-align:right;">{{$payAmountSupplier}}.00</td>
                                <td style="text-align:right;">{{$totalClosingSupplier}}.00</td>
                        </tfoot>

                    </table>
                <?php }?>
                <?php
                if($tableType ==4)
                { $count=0;
                    ?>
                    <table class="table table-striped table-bordered" id="reportingTable" border="1pt solid ash" style="color:black; border-collapse: collapse;/*table-layout: fixed;*/ " >
                        <thead>
                            <tr>
                                <th rowspan="2" style="width:50px;">SL NO</th>
                                <th rowspan="2"style="width:10%">Date</th>
                                <th rowspan="2" style="width:20%">House Owner</th>

                                <th rowspan="2"style="width:15%">Opening Balance</th>
                                <th rowspan="1" colspan="2">Current Period</th>
                                <th rowspan="2">Closing Balance</th>
                            </tr>
                            <th rowspan="1"style="width:15%">Amount</th>
                            <th rowspan="1"style="width:15%">Payment Amount</th>
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
                                    <td>{{$advanceGenerateHouseOwner->houseOwnerName}}</td>

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
                                    <td>{{$advancePaymentHouseOwner->name}}</td>

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
                             <td colspan="3" style="text-align:center;"> Total</td>
                             <td style="text-align:right;">{{$totalOpeningHouseOwner}}.00</td>
                              <td style="text-align:right;">{{$regAmountHouseOwner}}.00</td>
                               <td style="text-align:right;">{{$payAmountHouseOwner}}.00</td>
                                <td style="text-align:right;">{{$totalClosingHouseOwner}}.00</td>
                        </tfoot>
                    </table>
                <?php }?>
            </div>
        </div>
    </div>
</div>

<div id="printDiv2">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <?php

                if($tableType ==1)
                { $count=0;
                    ?>
                    <table class="table table-striped table-bordered" id="reportingTable" border="1pt solid ash" style="color:black; border-collapse: collapse;/*table-layout: fixed;*/ " >
                        <thead>
                            <tr>
                                <th rowspan="2" style="width:50px;">SL NO</th>
                                <th rowspan="2"style="width:10%">Date</th>
                                <th rowspan="2"style="width:20%">Supplier</th>

                                <th rowspan="2"style="width:15%;">Opening Balance</th>
                                <th rowspan="1" colspan="2">Current Period</th>
                                <th rowspan="2">Closing Balance</th>
                            </tr>
                            <th rowspan="1"style="width:15%">Amount</th>
                            <th rowspan="1"style="width:15%">Payment Amount</th>
                        </thead>
                        <tbody>
                            <?php
                               $registerTypeSupplierArray= array();
                                foreach( $collectionsSupplier as $collectionSupplier)
                                {
                                      array_push($registerTypeSupplierArray,$collectionSupplier["registerType"]);
                                }

                                $registerTypeSupplierArray=array_unique($registerTypeSupplierArray);
                                sort($registerTypeSupplierArray);
                                $registerTypeSupplierArrayLength= sizeof($registerTypeSupplierArray);

                            ?>
                            @for($x=0;$x<$registerTypeSupplierArrayLength;$x++)
                                <tr>
                                    @php $registerSupplierName= DB::table('acc_adv_register_type')
                                                                     ->select('name')
                                                                     ->where('id',$registerTypeSupplierArray[$x])
                                                                     ->first();
                                                                     //returns Type
                                   @endphp
                                    <td colspan="8" style="text-align:left;padding-left:5px;">{{$registerSupplierName->name}}</td>
                                </tr>
                                    @foreach( $collectionsSupplier as $collectionSupplier)
                                        @if($collectionSupplier["registerType"]== $registerTypeSupplierArray[$x])
                                            <tr><td>{{++$count}}</td>
                                                <td></td>
                                                @php
                                                $supplierInfo=DB::table('gnr_supplier')
                                                                    ->select('name')
                                                                    ->where('id',$collectionSupplier["id"])
                                                                    ->first();


                                                @endphp<td>{{$supplierInfo->name}}</td>
                                                <td style="text-align:right;">{{$collectionSupplier["openningBalance"]}}.00</td>
                                                <td style="text-align:right;">{{$collectionSupplier["amount"]}}.00</td>
                                                <td style="text-align:right;">{{$collectionSupplier["payment"]}}.00</td>
                                                <td style="text-align:right;">{{$collectionSupplier["closingBalance"]}}.00</td>

                                            </tr>
                                      @endif
                                 @endforeach
                               @endfor

                        </tbody>
                        <tfoot>
                            <tr>
                                   <td colspan="3" class="name" style="font-weight: bold; text-align:center;">Sub Total</td>
                                   <td style="text-align:right;">{{$subTotalOpBalanceSupplier}}.00</td>
                                   <td style="text-align:right;">{{$subTotalDebitAmountSupplier}}.00</td>
                                   <td style="text-align:right;">{{$subTotalCreditAmountSupplier}}.00</td>
                                   <td style="text-align:right;">{{$subTotalClosingBalanceSupplier}}.00</td>
                               </tr>

                        </tfoot>
                    </table>
                <?php }?>

            </div>
        </div>
    </div>
</div>

<div id="printDiv3">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <?php

                if($tableType ==1)
                { $count=0;
                    ?>
                    <table class="table table-striped table-bordered" id="reportingTable" border="1pt solid ash" style="color:black; border-collapse: collapse;" >
                        <thead>
                            <tr>
                                <th rowspan="2" style="width:50px;">SL NO</th>
                                <th rowspan="2"style="width:10%">Date</th>
                                <th rowspan="2"style="width:20%">House Owner</th>

                                <th rowspan="2"style="width:15%">Opening Balance</th>
                                <th rowspan="1" colspan="2">Current Period</th>
                                <th rowspan="2">Closing Balance</th>
                            </tr>
                            <th rowspan="1"style="width:15%">Amount</th>
                            <th rowspan="1"style="width:15%">Payment Amount</th>
                        </thead>
                        <tbody>
                            <?php
                               $registerTypeHouseOwnerArray= array();

                                foreach( $collectionsHouseOwner as $collectionHouseOwner)
                                {
                                      array_push($registerTypeHouseOwnerArray,$collectionHouseOwner["registerType"]);
                                }


                                $registerTypeHouseOwnerArray=array_unique($registerTypeHouseOwnerArray);
                                sort($registerTypeHouseOwnerArray);
                                $registerTypeHouseOwnerArrayLength= sizeof($registerTypeHouseOwnerArray);

                            ?>
                            @for($x=0;$x<$registerTypeHouseOwnerArrayLength;$x++)
                                <tr>
                                    @php $registerHouseOwnerName= DB::table('acc_adv_register_type')
                                                                     ->select('name')
                                                                     ->where('id',$registerTypeHouseOwnerArray[$x])
                                                                     ->first();

                                   @endphp
                                    <td colspan="8" style="text-align:left;padding-left:5px;">{{$registerHouseOwnerName->name}}</td>
                                </tr>

                                    @foreach( $collectionsHouseOwner as $collectionHouseOwner)
                                         @if($collectionHouseOwner["registerType"]== $registerTypeHouseOwnerArray[$x])
                                            <tr><td>{{++$count}}</td>
                                                <td></td>
                                                @php
                                                $houseOwnerInfo=DB::table('gnr_house_Owner')
                                                ->select('houseOwnerName')
                                                ->where('id',$collectionHouseOwner["id"])
                                                ->first();


                                                @endphp<td>{{$houseOwnerInfo->houseOwnerName}}</td>
                                                <td style="text-align:right;">{{$collectionHouseOwner["openningBalance"]}}.00</td>
                                                <td style="text-align:right;">{{$collectionHouseOwner["amount"]}}.00</td>
                                                <td style="text-align:right;">{{$collectionHouseOwner["payment"]}}.00</td>
                                                <td style="text-align:right;">{{$collectionHouseOwner["closingBalance"]}}.00</td>

                                            </tr>
                                      @endif
                                 @endforeach
                           @endfor
                        </tbody>
                        <tfoot>
                            <tr>
                                   <td colspan="3" class="name" style="font-weight: bold; text-align:center;">Sub Total</td>
                                   <td style="text-align:right;">{{$subTotalOpBalanceHouseOwner}}.00</td>
                                   <td style="text-align:right;">{{$subTotalDebitAmountHouseOwner}}.00</td>
                                   <td style="text-align:right;">{{$subTotalCreditAmountHouseOwner}}.00</td>
                                   <td style="text-align:right;">{{$subTotalClosingBalanceHouseOwner}}.00</td>
                               </tr>
                               <tr>
                                   <td colspan="3" class="name" style="font-weight: bold; text-align:center;">Grand Total</td>
                                   <td style="text-align:right;">
                                       {{$subTotalOpBalanceHouseOwner+$subTotalOpBalanceSupplier+$subTotalOpBalanceEmp}}.00</td>
                                   <td style="text-align:right;">
                                       {{$subTotalDebitAmountHouseOwner+$subTotalDebitAmountSupplier+$subTotalDebitAmountEmp}}.00</td>
                                   <td style="text-align:right;">
                                       {{$subTotalCreditAmountHouseOwner+$subTotalCreditAmountSupplier+$subTotalCreditAmountEmp}}.00</td>
                                   <td style="text-align:right;">
                                      {{$subTotalClosingBalanceHouseOwner+$subTotalClosingBalanceSupplier+$subTotalClosingBalanceEmp}}.00
                                  </td>
                               </tr>


                        </tfoot>

                    </table>
                <?php }?>
            </div>
        </div>
    </div>
</div>
