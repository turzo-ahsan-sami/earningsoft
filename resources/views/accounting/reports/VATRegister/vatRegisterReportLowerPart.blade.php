
<div id="printDiv">
  <!--This div is going to print company details and  ots account Statement   !-->
  <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}
   <?php
   $user_company_id = Auth::user()->company_id_fk;
   $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
   ?>
   <span style="text-decoration: underline;  font-size:14px;">VAT Register Report</span></br>
   <span style="font-size:14px;">{{$company->name}}</span><br/>
   <span style="font-size:11px;">{{$company->address}}</span><br/>

   <span style="font-size:11px;">Statement Period: {{Carbon\Carbon::parse($fromDate)->format('d-m-Y ') }}   {{" "}} To {{Carbon\Carbon::parse($toDate)->format('d-m-Y ') }}</span><br/>



 </div>
 <!--This row div ends here   !-->


 <div class="row">
  <div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-striped table-bordered" width="100%" id="reportingTable" border="1pt solid ash" style="color:black; border-collapse: collapse;/*table-layout: fixed;*/ " >
        <thead>
          <tr>
           <th rowspan="2" style="width:50px;">SL NO</th>
           <th rowspan="2">Date</th>
           <th rowspan="2">Supplier</th>
           <th rowspan="2">VAT Rate</th>
           <th rowspan="2">Opening Balance</th>
           <th rowspan="1" colspan="2">Current Period</th>
           <th rowspan="2">Closing Balance</th>

         </tr>
         <th rowspan="1">Amount</th>
         <th rowspan="1">Payment Amount</th>


       </thead>
       <tbody>
         <?php
         use Carbon\Carbon;
         $max=sizeof($generateAndPaymentDates);
         $regAmount=0;
         $payAmount=0;
         $totalOpening=0;
         $totalClosing=0;
         $count=0;
         $closingBalance=0;


         for ($x = 0; $x <$max; $x++)
         {

          ?>
          @foreach($vatGenerates as $vatGenerate)
          @if($vatGenerate->billDate == $generateAndPaymentDates[$x])
          <tr>
           <td>{{++$count}}</td>
           <td>{{Carbon::parse($vatGenerate->billDate)->format('d-m-Y ') }}</td>
           @php $vatGenerate->billDate=null; @endphp
           <td>{{$vatGenerate->name}}</td>
           <td>{{$vatGenerate->vatInterestRate}}</td>
           <td style="text-align:right;">{{$openningBalance}}.00</td>
           @php
           $regAmount=$regAmount+$vatGenerate->vatAmount;
           $totalOpening=$totalOpening+$openningBalance;
           $closingBalance=$openningBalance+$vatGenerate->vatAmount;
           $totalClosing=$totalClosing+$closingBalance;
           $openningBalance=$closingBalance;
           @endphp
           <td style="text-align:right;">{{$vatGenerate->vatAmount}}</td>

           <td style="text-align:right;">0.00</td>

           <!-- <td></td> -->
           <td style="text-align:right;">{{$closingBalance}}.00</td>

         </tr>
         @endif
         @endforeach

         @foreach($vatPayments as $vatPayment)
         @if($vatPayment->paymentDate == $generateAndPaymentDates[$x])
         <tr>
          <td>{{++$count}}</td>
          <td>{{Carbon::parse($vatPayment->paymentDate)->format('d-m-Y ') }}</td>
          @php $vatPayment->paymentDate=null; @endphp
          <td>{{$vatPayment->name}}</td>
          <td>-</td>
          <td style="text-align:right;">{{$openningBalance}}.00</td>
          @php
          $payAmount=$payAmount+$vatPayment->amount;
          $totalOpening=$totalOpening+$openningBalance;
          $closingBalance=$openningBalance-$vatPayment->amount;
          $totalClosing=$totalClosing+$closingBalance;
          $openningBalance=$closingBalance;

          @endphp
          <td style="text-align:right;">0.00</td>

          <td style="text-align:right;">{{$vatPayment->amount}}</td>


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
     <tr>
      <td colspan="4" style="text-align:center; font-weight:bold;">Total</td>
      <td style="text-align:right; padding-right:0px;font-weight:bold">{{$totalOpening}}.00</td>
      <td style="text-align:right; padding-right:0px;font-weight:bold">{{$regAmount}}.00</td>

      <td style="text-align:right; padding-right:0px;font-weight:bold">{{$payAmount}}.00</td>
      <td style="text-align:right; padding-right:0px;font-weight:bold">{{$totalClosing}}.00</td>

    </tr>
  </tfoot>
</div>
</div>
</div>

</div>
