<html>

<head>
	<style type="text/css">
    #registerReportTable thead tr th{
        padding: 2px;
    }
    #registerReportTable tbody tr td.amount{
        text-align: right;
        padding-right: 5px;
    }
     #registerReportTable tbody tr td.name{
        text-align: left;
        padding-left: 5px;
    }
    #registerReportTable tbody tr td.center{
        text-align: center;
    }
    #registerReportTable tfoot tr td.name{
        text-align: center;
        font-weight: bold;
    }
    #registerReportTable tfoot tr td.amount{
        text-align: right;
        padding-right: 5px;
        font-weight: bold;
    }
    #registerReportTable tfoot tr td{
        background-color: #8b8d91;
        line-height: 10px;
    }
    #registerReportTable tfoot tr td:nth-child(1){
        text-align: center;
        font-size: 13px;
    }
    #registerReportTable tbody tr.subTotal td{
        background-color: #9da5b2;
    }
   /* #registerReportTable thead tr th, #registerReportTable body tr td{
      border: 1px solid black;
    }*/
</style>
</head>


<body>

@php
use Carbon\Carbon;

	$bankIds = DB::table('gnr_bank')->pluck('id')->toArray();
	
  	/*$date = Carbon::toDay();
	$startDate = $date->format('Y-m-d');
    $endDate = $date->addDays(2)->format('Y-m-d');*/

    $loanAccountIds = DB::table('acc_loan_register_payment_schedule')->whereBetween('paymentDate',[$startDate,$endDate])->pluck('accId_fk')->toArray();

    /*$accounts = DB::table('acc_loan_register_account')->where('status',1)->whereIn('id',$loanAccountIds)->get();*/

    /*$accounts = DB::table('acc_loan_register_account as t1')
    					->join('acc_loan_register_payment_schedule as t2','t1.id','t2.accId_fk')
    					->where('t1.status',1)
    					->whereBetween('t2.paymentDate',[$startDate,$endDate])
    					->select('t1.*')
    					->orderBy('t2.paymentDate')
    					->get();*/

 	$activeProjectTypeList = array();

    foreach ($accounts as $key => $account) {
       array_push($activeProjectTypeList, $account->projectTypeId_fk);
    }
	$projectTypes = DB::table('gnr_project_type')->whereIn('id',$activeProjectTypeList)->get();



@endphp

<h2>Loan Register Installment Report</h2>
<table id="registerReportTable" class="table table-striped table-bordered" style="color: black;font-size:11px;border-collapse: collapse;margin-bottom:50px;" border= "1px solid black;" cellpadding="0" cellspacing="0" width="100%">
            <thead >
              <tr>
                <th >SL#</th>
                <th >Date</th>
                <th >Bank/Donar</th>
                <th >Account No / Phase/ Cycle</th>
                <th >Loan Amount</th>
                <th >Installment Size</th> 
               {{--  <th colspan="2" width="100">Current Month Loan Installment</th>  --}}           
               {{--  <th colspan="2" width="100">Current Month Loan Installment Re-Payment</th> 
                <th rowspan="2">Current Month Loan <br> Installment Due (Tk)</th> --}}              
                         
              </tr>
              {{-- <tr>
                  <th>Date</th>
                  <th>Amount (Tk)</th>
                  <th>Date</th>
                  <th>Amount (Tk)</th>
                  
              </tr> --}}
              
            
            </thead>
            <tbody>           

             @php             
              $gTloanAmount = 0;
              $gTinstallmentSize = 0;
              $gTloanAmount = 0;
              $gTcurrentMonthPayment = 0;
              $gTcurrentMonthDue = 0;
            @endphp

            @foreach($projectTypes as $projectType)
            <tr>
              <td colspan="6" class="name" style="font-weight: bold;font-size: 12px;">{{$projectType->name}}</td>
            </tr>

            @php
            	$index = 0;              
	            $sTloanAmount = 0;
	            $sTinstallmentSize = 0;
	            $sTcurrentMonthPayment = 0;
	            $sTcurrentMonthDue = 0;
            @endphp

           
                
             
           

            @foreach($accounts as $key => $account)

            @if($account->projectTypeId_fk==$projectType->id)

            @php

              $bankName = DB::table('gnr_bank')->where('id',$account->bankId_fk)->value('name');
              if ($account->phase>0) {
                $accNoPhaseCycleValue = "- / ".str_pad($account->phase,3,'0',STR_PAD_LEFT)." / ".str_pad($account->cycle,3,'0',STR_PAD_LEFT);
              }
              else{
                $accNoPhaseCycleValue = $account->accNo." / - / -";
              }

              


              $installmentSize = (float) DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$account->id)->where('paymentDate','>=',$startDate)->where('paymentDate','<=',$endDate)->value('totalAmount');

              $installmentDate = DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$account->id)->where('paymentDate','>=',$startDate)->where('paymentDate','<=',$endDate)->value('paymentDate');

              if ($installmentDate==null) {
                $installmentDate = "-";
              }
              else{
                $installmentDate = date('d-m-Y',strtotime($installmentDate));
              }

              $paymentDateInThisMonth = DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->where('paymentDate','>=',$startDate)->where('paymentDate','<=',$endDate)->orderBy('id','desc')->value('paymentDate');

              if ($paymentDateInThisMonth==null) {
                $paymentDateInThisMonth = "-";
              }
              else{
                $paymentDateInThisMonth = date('d-m-Y',strtotime($paymentDateInThisMonth));
              }

              $paymentAmountInThisMonth = (float) DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->where('paymentDate','>=',$startDate)->where('paymentDate','<=',$endDate)->sum('totalAmount');

              $thisMonthDue = $installmentSize - $paymentAmountInThisMonth;
              
            @endphp


             <tr>

             <td class="center">{{++$index}}</td> 
             <td class="center">{{$installmentDate}}</td>


             @php
             $count = 1;               

               $isChanged = 0;

               if ($key>0) {
                 if ($accounts[$key-1]->bankId_fk!=$account->bankId_fk) {
                   $isChanged = 1;
                 }
               }

               if ($key == 0 || $isChanged == 1) {

               	//////
               	$count = DB::table('acc_loan_register_account')->where('status',1)->whereIn('id',$loanAccountIds);
               	//////
               
               $count = $count->where('bankId_fk',$account->bankId_fk)->count();
                 
               }
             @endphp


                @if($isChanged==1 || $key==0)                 
                  
                    <td rowspan="{{$count}}" class="name">{{$bankName}}</td>                  
                  
                @endif





             {{-- <td class="name bankName">{{$bankName}}</td>     --}}
             <td class="center">{{$accNoPhaseCycleValue}}</td>
             <td class="amount">{{number_format($account->loanAmount,2,'.',',')}}</td>
             <td class="amount">{{number_format($installmentSize,2,'.',',')}}</td>
             
             {{-- <td class="amount">{{number_format($installmentSize,2,'.',',')}}</td> --}}
             {{-- <td>{{$paymentDateInThisMonth}}</td>
             <td class="amount">{{number_format($paymentAmountInThisMonth,2,'.',',')}}</td>
             <td class="amount">{{number_format($thisMonthDue,2,'.',',')}}</td> --}}
           
             
             </tr>

             @php
               $sTloanAmount = $sTloanAmount + $account->loanAmount;
               $sTinstallmentSize = $sTinstallmentSize + $installmentSize;
               $sTcurrentMonthPayment = $sTcurrentMonthPayment + $paymentAmountInThisMonth;
               $sTcurrentMonthDue = $sTcurrentMonthDue + $thisMonthDue;
             @endphp
            
             @endif
             @endforeach {{-- Account --}}

             <tr class="subTotal">
               <td colspan="4">Sub Total</td>
               <td class="amount">{{number_format($sTloanAmount,2,'.',',')}}</td>
               <td class="amount">{{number_format($sTinstallmentSize,2,'.',',')}}</td>
               {{-- <td></td>
               <td class="amount">{{number_format($sTinstallmentSize,2,'.',',')}}</td> --}}
               {{-- <td></td>
               <td class="amount">{{number_format($sTcurrentMonthPayment,2,'.',',')}}</td>
               <td class="amount">{{number_format($sTcurrentMonthDue,2,'.',',')}}</td> --}}
             </tr>


              @php
               $gTloanAmount = $gTloanAmount + $sTloanAmount;
               $gTinstallmentSize = $gTinstallmentSize + $sTinstallmentSize;
               $gTcurrentMonthPayment = $gTcurrentMonthPayment + $sTcurrentMonthPayment;
               $gTcurrentMonthDue = $gTcurrentMonthDue + $sTcurrentMonthDue;
             @endphp


          @endforeach {{-- Project Type --}}

            </tbody>
            <tfoot>
                <tr>
               <td colspan="4">Total</td>
               <td class="amount">{{number_format($gTloanAmount,2,'.',',')}}</td>
               <td class="amount">{{number_format($gTinstallmentSize,2,'.',',')}}</td>
               {{-- <td></td>
               <td class="amount">{{number_format($gTinstallmentSize,2,'.',',')}}</td> --}}
               {{-- <td></td>
               <td class="amount">{{number_format($gTcurrentMonthPayment,2,'.',',')}}</td>
               <td class="amount">{{number_format($gTcurrentMonthDue,2,'.',',')}}</td> --}}
             </tr>
            </tfoot>
          </table>



</body>
</html>