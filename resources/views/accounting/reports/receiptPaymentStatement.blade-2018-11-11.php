@extends('layouts/acc_layout')
@section('title', '| Receipt Payment Statement')
@section('content')
@include('successMsg')

<style type="text/css">
    #CRPStable{
        font-family: arial !important;
    }

    #CRPStable td{
        padding-left: 10px;
        padding-right: 10px;
    }
</style>
@php
    // dd($openingBalanceInfo);
@endphp

<?php
    function eachRow($child, $info) {
        if ($info['branchSelected']===null || $info['branchSelected']==='') {
            $parentId = DB::table('acc_account_ledger')->where('id',$child->id)->value('parentId');
            if ($parentId==500 || $child->id == 500 || $child->id==133) {
                return false;
            }
        }

?>


    <tr class="item{{$child->id}}" level="{{$info['loopTrack']}}" @if($info['loopTrack']<=3){{"style=font-weight:bold;"}}@endif>
        <td style="text-align: left;">


            <?php
            if($child->isGroupHead==1){
                echo '<span></span>';
            }else{
                echo '<span></span>';
            }
            ?>
            @if($child->isGroupHead==1)
            {{-- {{strtoupper($child->name).' ['.$child->code.']'}} --}}
            @else
            {{$child->name.' ['.$child->code.']'}}
            @endif

        </td>

         @php

        //  $childId = $child->id;
        //
        //  $openingDebit = 0;
        //  $openingCredit = 0;
        //
        //  $currentDebit = 0;
        //  $currentCredit = 0;
        //
        //  $thisMonthDebit = 0;
        //  $thisMonthCredit = 0;
        //
        //  $previousFiscalYearDebit = 0;
        //  $previousFiscalYearCredit = 0;
        //
        //  $debitSum = 0;
        //  $creditSum = 0;
        //
        //  $openingDebitForCumulative = 0;
        //  $currentDebitForCumulative = 0;
        //  $cumulativeDebit = 0;
        //  $cumulativeCredit = 0;
        //
        //  $openingCreditForCumulative = 0;
        //  $currentCreditForCumulative = 0;
        //
        // $amount = 0;
        //
        //     if ($child->isGroupHead==0) {
        //
        //         /*Get Data for Fiscal Year*/
        //         if ($info['searchMethodSelected']==1) {
        //
        //         }
        //         /*End Get Data for Fiscal Year*/
        //
        //         $startDate = $info['startDate'];
        //         $endDate = $info['endDate'];
        //
        //         $currectFiscalYearId = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('id');
        //          //previous fiscal year id
        //         $currentFiscalYearStartDate = DB::table('gnr_fiscal_year')->where('id',$currectFiscalYearId)->value('fyStartDate');
        //         $dateToCompare = date('Y-m-d', strtotime('-1 day', strtotime($currentFiscalYearStartDate)));
        //         $previousfiscalYearId = DB::table('gnr_fiscal_year')->where('fyEndDate',$dateToCompare)->value('id');
        //
        //
        //         $openingBalanceLedgerInfo = $info['openingBalanceInfo']->where('ledgerId', $childId);
        //
        //         $cashNbankTypeLedgers = DB::table('acc_account_ledger')->whereIn('accountTypeId',[4,5])->pluck('id')->toArray();
        //
        //         if ($info['branchSelected']===null || $info['branchSelected']==='' || $info['branchSelected']== 0) {
        //             $thisYearLedgerInfo = $info['thisYearInfo']->where('ledgerId', $childId);
        //             $thisMonthLedgerInfo = $info['thisMonthInfo']->where('ledgerId', $childId);
        //         }
        //         else {
        //             $thisChildVoucherDetails = DB::table('acc_voucher_details')
        //                                     ->where('debitAcc', $childId)
        //                                     ->orWhere('creditAcc', $childId)
        //                                     ->select('debitAcc','creditAcc','voucherId','amount')
        //                                     ->get();
        //         }
        //
        //
        //         if ($info['voucherTypeSelected']=="") {
        //              //Curent Debit Credit
        //             // $currentDebit = DB::table('acc_voucher_details')
        //             //                         ->where('debitAcc', $childId)
        //             //                         ->whereIn('creditAcc',$cashNbankTypeLedgers)
        //             //                         ->whereIn('voucherId',$info['vouchers'])
        //             //                         ->sum('amount');
        //             if ($info['branchSelected']===null || $info['branchSelected']==='' || $info['branchSelected']== 0) {
        //
        //                 $currentDebit = $thisYearLedgerInfo->sum('cashDebit') + $thisYearLedgerInfo->sum('bankDebit') + $thisYearLedgerInfo->sum('ftDebit');
        //                 $currentCredit = $thisYearLedgerInfo->sum('cashCredit') + $thisYearLedgerInfo->sum('bankCredit') + $thisYearLedgerInfo->sum('ftCredit');
        //             }
        //             else {
        //                 $currentDebit = $thisChildVoucherDetails
        //                                        ->where('debitAcc', $childId)
        //                                        ->whereIn('creditAcc',$cashNbankTypeLedgers)
        //                                        ->whereIn('voucherId',$info['vouchers'])
        //                                        ->sum('amount');
        //
        //
        //                // $currentCredit = DB::table('acc_voucher_details')
        //                //                         ->where('creditAcc', $childId)
        //                //                         ->whereIn('debitAcc',$cashNbankTypeLedgers)
        //                //                         ->whereIn('voucherId',$info['vouchers'])
        //                //                         ->sum('amount');
        //
        //                    $currentCredit = $thisChildVoucherDetails
        //                                        ->where('creditAcc', $childId)
        //                                        ->whereIn('debitAcc',$cashNbankTypeLedgers)
        //                                        ->whereIn('voucherId',$info['vouchers'])
        //                                        ->sum('amount');
        //             }
        //
        //
        //         }
        //         else if($info['voucherTypeSelected']==1){
        //
        //             if ($info['branchSelected']===null || $info['branchSelected']==='' || $info['branchSelected']== 0) {
        //
        //                 $currentDebit = $thisYearLedgerInfo->sum('cashDebit') + $thisYearLedgerInfo->sum('bankDebit') + $thisYearLedgerInfo->sum('ftDebit') + $thisYearLedgerInfo->sum('jvDebit');
        //                 $currentCredit = $thisYearLedgerInfo->sum('cashCredit') + $thisYearLedgerInfo->sum('bankCredit') + $thisYearLedgerInfo->sum('ftCredit') + $thisYearLedgerInfo->sum('ftCredit');
        //             }
        //             else {
        //                 $jvouchers = DB::table('acc_voucher')->whereIn('id',$info['vouchers'])->where('voucherTypeId',3)->pluck('id')->toArray();
        //
        //                     // $currentDebitForJV = DB::table('acc_voucher_details')
        //                     //                 ->where('debitAcc', $childId)
        //                     //                 ->whereIn('voucherId',$jvouchers)
        //                     //                 ->sum('amount');
        //
        //                 $currentDebitForJV = $thisChildVoucherDetails
        //                                     ->where('debitAcc', $childId)
        //                                     ->whereIn('voucherId',$jvouchers)
        //                                     ->sum('amount');
        //
        //                      // $currentCreditForJV = DB::table('acc_voucher_details')
        //                      //                ->where('creditAcc', $childId)
        //                      //                ->whereIn('voucherId',$jvouchers)
        //                      //                ->sum('amount');
        //
        //                 $currentCreditForJV = $thisChildVoucherDetails
        //                                     ->where('creditAcc', $childId)
        //                                     ->whereIn('voucherId',$jvouchers)
        //                                     ->sum('amount');
        //
        //
        //
        //                      // $currentDebit = DB::table('acc_voucher_details')
        //                      //                ->where('debitAcc', $childId)
        //                      //                ->whereIn('creditAcc',$cashNbankTypeLedgers)
        //                      //                ->whereIn('voucherId',$info['vouchers'])
        //                      //                ->sum('amount');
        //
        //                 $currentDebit = $thisChildVoucherDetails
        //                                     ->where('debitAcc', $childId)
        //                                     ->whereIn('creditAcc',$cashNbankTypeLedgers)
        //                                     ->whereIn('voucherId',$info['vouchers'])
        //                                     ->sum('amount');
        //
        //                      // $currentCredit = DB::table('acc_voucher_details')
        //                      //                ->where('creditAcc', $childId)
        //                      //                ->whereIn('debitAcc',$cashNbankTypeLedgers)
        //                      //                ->whereIn('voucherId',$info['vouchers'])
        //                      //                ->sum('amount');
        //
        //                  $currentCredit = $thisChildVoucherDetails
        //                                     ->where('creditAcc', $childId)
        //                                     ->whereIn('debitAcc',$cashNbankTypeLedgers)
        //                                     ->whereIn('voucherId',$info['vouchers'])
        //                                     ->sum('amount');
        //
        //
        //                     $currentDebit = $currentDebit + $currentDebitForJV;
        //                     $currentCredit = $currentCredit + $currentCreditForJV;
        //             }
        //
        //
        //
        //
        //         }
        //         else if($info['voucherTypeSelected']==2){
        //
        //             if ($info['branchSelected']===null || $info['branchSelected']==='' || $info['branchSelected']== 0) {
        //
        //                 $currentDebit = $thisYearLedgerInfo->sum('jvDebit');
        //                 $currentCredit = $thisYearLedgerInfo->sum('jvCredit');
        //             }
        //             else {
        //                 $currentDebit = DB::table('acc_voucher_details')
        //                                     ->where('debitAcc', $childId)
        //                                     ->whereIn('voucherId',$info['vouchers'])
        //                                     ->sum('amount');
        //
        //                 $currentCredit = DB::table('acc_voucher_details')
        //                                     ->where('creditAcc', $childId)
        //                                     ->whereIn('voucherId',$info['vouchers'])
        //                                     ->sum('amount');
        //             }
        //
        //         }
        //
        //
        //
        //
        //         //Previous Year Debit Credit
        //
        //         else if($info['voucherTypeSelected']==""){
        //
        //         }
        //
        //
        //          $previousFiscalYearDebit = $openingBalanceLedgerInfo
        //                                     ->where('fiscalYearId',$previousfiscalYearId)
        //                                     ->sum('debitAmount');
        //
        //        /* $previousFiscalYearCredit = DB::table('acc_voucher_details')
        //                                 ->where('creditAcc', $childId)
        //                                 ->whereIn('debitAcc',$cashNbankTypeLedgers)
        //                                 ->whereIn('voucherId',$info['previousfiscalYearVouchers'])
        //                                 ->sum('amount');*/
        //
        //         $previousFiscalYearCredit = $openingBalanceLedgerInfo
        //                                     ->where('fiscalYearId',$previousfiscalYearId)
        //                                     ->sum('creditAmount');
        //
        //         //End Previous Year Debit Credit
        //
        //
        //         //This Month Debit Credit
        //         // $thisMonthDebit = DB::table('acc_voucher_details')
        //         //                         ->where('debitAcc', $childId)
        //         //                         /*->whereIn('creditAcc',$cashNbankTypeLedgers)*/
        //         //                         ->whereIn('voucherId',$info['thisMonthVoucherIds'])
        //         //                         ->sum('amount');
        //
        //         if ($info['branchSelected']===null || $info['branchSelected']==='' || $info['branchSelected']== 0) {
        //
        //             if ($info['voucherTypeSelected']== '') {
        //                 $thisMonthDebit = $thisMonthLedgerInfo->sum('cashDebit') + $thisMonthLedgerInfo->sum('bankDebit') + $thisMonthLedgerInfo->sum('ftDebit');
        //                 $thisMonthCredit = $thisMonthLedgerInfo->sum('cashCredit') + $thisMonthLedgerInfo->sum('bankCredit') + $thisMonthLedgerInfo->sum('ftCredit');
        //             }
        //             elseif ($info['voucherTypeSelected']== 1) {
        //                 $thisMonthDebit = $thisMonthLedgerInfo->sum('cashDebit') + $thisMonthLedgerInfo->sum('bankDebit') + $thisMonthLedgerInfo->sum('ftDebit') + $thisMonthLedgerInfo->sum('jvDebit');
        //                 $thisMonthCredit = $thisMonthLedgerInfo->sum('cashCredit') + $thisMonthLedgerInfo->sum('bankCredit') + $thisMonthLedgerInfo->sum('ftCredit') + $thisMonthLedgerInfo->sum('ftCredit');
        //             }
        //             elseif ($info['voucherTypeSelected']== 2) {
        //                 $thisMonthDebit = $thisMonthLedgerInfo->sum('jvDebit');
        //                 $thisMonthCredit = $thisMonthLedgerInfo->sum('ftCredit');
        //             }
        //
        //         }
        //         else {
        //             $thisMonthDebit = $thisChildVoucherDetails
        //                                     ->where('debitAcc', $childId)
        //                                     ->whereIn('creditAcc',$cashNbankTypeLedgers)
        //                                     ->whereIn('voucherId',$info['thisMonthVoucherIds'])
        //                                     ->sum('amount');
        //
        //             // $thisMonthCredit = DB::table('acc_voucher_details')
        //             //                         ->where('creditAcc', $childId)
        //             //                         /*->whereIn('debitAcc',$cashNbankTypeLedgers)*/
        //             //                         ->whereIn('voucherId',$info['thisMonthVoucherIds'])
        //             //                         ->sum('amount');
        //
        //         $thisMonthCredit = $thisChildVoucherDetails
        //                                     ->where('creditAcc', $childId)
        //                                     ->whereIn('debitAcc',$cashNbankTypeLedgers)
        //                                     ->whereIn('voucherId',$info['thisMonthVoucherIds'])
        //                                     ->sum('amount');
        //         }
        //
        //
        //         //End This Month Debit Credit
        //
        //         //cumulative
        //
        //
        //
        //         if ($previousfiscalYearId!=null) {
        //             $openingDebitForCumulative = $openingBalanceLedgerInfo
        //                                             ->where('fiscalYearId',$previousfiscalYearId)
        //                                             ->sum('debitAmount');
        //
        //             $openingCreditForCumulative = $openingBalanceLedgerInfo
        //                                             ->where('fiscalYearId',$previousfiscalYearId)
        //                                             ->sum('creditAmount');
        //         }
        //         // $currentDebitForCumulative = DB::table('acc_voucher_details')
        //         //                             /*->whereIn('debitAcc',$cashNbankTypeLedgers)*/
        //         //                             ->where('debitAcc',$childId)
        //         //                             ->whereIn('voucherId',$info['vouchers'])
        //         //                             ->sum('amount');
        //         // $currentCreditForCumulative = DB::table('acc_voucher_details')
        //         //                                 /*->whereIn('creditAcc',$cashNbankTypeLedgers)*/
        //         //                                 ->where('creditAcc',$childId)
        //         //                                 ->whereIn('voucherId',$info['vouchers'])
        //         //                                 ->sum('amount');
        //
        //         /* $currentDebitForCumulative = $thisChildVoucherDetails
        //                                     ->where('debitAcc',$childId)
        //                                     ->whereIn('creditAcc',$cashNbankTypeLedgers)
        //                                     ->whereIn('voucherId',$info['vouchers'])
        //                                     ->sum('amount');
        //
        //         $currentCreditForCumulative = $thisChildVoucherDetails
        //                                         ->where('creditAcc',$childId)
        //                                         ->whereIn('debitAcc',$cashNbankTypeLedgers)
        //                                         ->whereIn('voucherId',$info['vouchers'])
        //                                         ->sum('amount'); */
        //
        //         // $cumulativeDebit = $openingDebitForCumulative + $currentDebitForCumulative;
        //         $cumulativeDebit = $openingDebitForCumulative + $currentDebit;
        //         // $cumulativeCredit = $openingCreditForCumulative + $currentCreditForCumulative;
        //         $cumulativeCredit = $openingCreditForCumulative + $currentCredit;
        //         //End cumulative
        //
        //
        //
        //     }



        @endphp

        {{-- If RoundUp Selected --}}



        @if($info['roundUpSelected']==1)

        {{-- Fiscal Year --}}
        @if($info['searchMethodSelected']==1)

        <td></td>
        <td class="previousFiscalYear" amount="@if($info['type']=='receipt'){{$previousFiscalYearDebit}}@else {{$previousFiscalYearCredit}} @endif" style="text-align: right; padding: 0 5px 0 10px;">@if($info['type']=='receipt'){{number_format($previousFiscalYearDebit,2)}}@else {{number_format($previousFiscalYearCredit,2)}} @endif</td>
        <td class="currentFiscalYear" amount="@if($info['type']=='receipt'){{$currentDebit}}@else {{$currentCredit}} @endif" style="text-align: right; padding: 0 5px 0 10px;">@if($info['type']=='receipt'){{number_format($currentDebit,2)}}@else {{number_format($currentCredit,2)}} @endif</td>

        {{-- Current Year --}}
        @elseif($info['searchMethodSelected']==2)
        <td></td>
        <td class="thisMonth" amount="@if($info['type']=='receipt'){{$thisMonthDebit}}@else {{$thisMonthCredit}} @endif" style="text-align: right; padding: 0 5px 0 10px;">@if($info['type']=='receipt'){{number_format($thisMonthDebit,2)}}@else {{number_format($thisMonthCredit,2)}} @endif</td>
        <td class="thisYear" amount="@if($info['type']=='receipt'){{$currentDebit}}@else {{$currentCredit}} @endif" style="text-align: right; padding: 0 5px 0 10px;">@if($info['type']=='receipt'){{number_format($currentDebit,2)}}@else {{number_format($currentCredit,2)}} @endif</td>
        <td class="cumulative" amount="@if($info['type']=='receipt'){{$cumulativeDebit}}@else {{$cumulativeCredit}} @endif" style="text-align: right; padding: 0 5px 0 10px;">@if($info['type']=='receipt'){{number_format($cumulativeDebit,2)}}@else {{number_format($cumulativeCredit,2)}} @endif</td>

        {{-- Date Range --}}
        @elseif($info['searchMethodSelected']==3)
        <td></td>
        <td class="currentPeriod" amount="@if($info['type']=='receipt'){{$currentDebit}}@else {{$currentCredit}} @endif" style="text-align: right; padding: 0 5px 0 10px;">@if($info['type']=='receipt'){{number_format($currentDebit,2)}}@else {{number_format($currentCredit,2)}} @endif</td>
        @endif





        @else
        {{-- If RoundUp Not Selected --}}

       {{-- Fiscal Year --}}
        @if($info['searchMethodSelected']==1)
        <td></td>
        <td class="previousFiscalYear" amount="@if($info['type']=='receipt'){{$previousFiscalYearDebit}}@else {{$previousFiscalYearCredit}} @endif" style="text-align: right; padding: 0 5px 0 10px;">@if($info['type']=='receipt'){{number_format($previousFiscalYearDebit,2,'.','')}}@else {{number_format($previousFiscalYearCredit,2,'.','')}} @endif</td>
        <td class="currentFiscalYear" amount="@if($info['type']=='receipt'){{$currentDebit}}@else {{$currentCredit}} @endif" style="text-align: right; padding: 0 5px 0 10px;">@if($info['type']=='receipt'){{number_format($currentDebit,2,'.','')}}@else {{number_format($currentCredit,2,'.','')}} @endif</td>

        {{-- Current Year --}}
        @elseif($info['searchMethodSelected']==2)
        <td></td>
        <td class="thisMonth" amount="@if($info['type']=='receipt'){{$thisMonthDebit}}@else {{$thisMonthCredit}} @endif" style="text-align: right; padding: 0 5px 0 10px;">@if($info['type']=='receipt'){{number_format($thisMonthDebit,2,'.','')}}@else {{number_format($thisMonthCredit,2,'.','')}} @endif</td>
        <td class="thisYear" amount="@if($info['type']=='receipt'){{$currentDebit}}@else {{$currentCredit}} @endif" style="text-align: right; padding: 0 5px 0 10px;">@if($info['type']=='receipt'){{number_format($currentDebit,2,'.','')}}@else {{number_format($currentCredit,2,'.','')}} @endif</td>
        <td class="cumulative" amount="@if($info['type']=='receipt'){{$cumulativeDebit}}@else {{$cumulativeCredit}} @endif" style="text-align: right; padding: 0 5px 0 10px;">@if($info['type']=='receipt'){{number_format($cumulativeDebit,2,'.','')}}@else {{number_format($cumulativeCredit,2,'.','')}} @endif</td>

        {{-- Date Range --}}
        @elseif($info['searchMethodSelected']==3)
        <td></td>
        <td class="currentPeriod" amount="@if($info['type']=='receipt'){{$currentDebit}}@else {{$currentCredit}} @endif" style="text-align: right; padding: 0 5px 0 10px;">@if($info['type']=='receipt'){{number_format($currentDebit,2,'.','')}}@else {{number_format($currentCredit,2,'.','')}} @endif</td>
        @endif

        @endif

    </tr>

    <?php



     return $child->id;
    }
    ?>


@php
 function printOpeningBalance($obData)
    {
        if ($obData['voucherTypeSelected']==2) {

            if ($obData['searchMethodSelected']==1){
                echo
            "<tr>
                <td style='text-align: left;'>ASSET</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>OPENING BALANCE</td>
                <td></td>
                <td id='obPreviousFiscalYear' amount='0' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format(0,2)."</td>
                <td id='obCurrentFiscalYear' amount='0' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format(0,2)."</td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>CASH IN HAND</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
                <td id='cashInHandForCurrentFiscalYear' amount='0' style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>CASH AT BANK</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
                <td id='cashInBankForCurrentFiscalYear' amount='0' style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
            </tr>"
            ;
            }

            else if($obData['searchMethodSelected']==2){
                echo
            "<tr>
                <td style='text-align: left;font-weight: bold;'>ASSET</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>OPENING BALANCE</td>
                <td></td>
                <td  id='obThisMonth' amount='0' style='text-align: right;padding-right:5px;font-weight:bold;font-weight:bold;'>".number_format(0,2)."</td>
                <td id='obThisYear' amount='0' style='text-align: right;padding-right:5px;font-weight:bold;font-weight:bold;'>".number_format(0,2)."</td>
                <td></td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>CASH IN HAND</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
                <td style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
                <td></td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>CASH AT BANK</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
                <td style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
                <td></td>
            </tr>"
            ;
            }
            else if ($obData['searchMethodSelected']==3) {
                 echo
            "<tr>
                <td style='text-align: left;'>ASSET</td>
                <td></td>
                <td></td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>OPENING BALANCE</td>
                <td></td>
                <td id='obThisPeriod' amount='0' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format(0,2)."</td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>CASH IN HAND</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>CASH AT BANK</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
            </tr>"
            ;
            }

        }



        //If It is not only JV

        else{


        // $startDate = $data['startDate'];
        // $endDate = $data['endDate'];
        //
        // $currectFiscalYearId = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('id');
        // // dd($startDate);
        //
        // //previous fiscal year id
        // $currentFiscalYearStartDate = DB::table('gnr_fiscal_year')->where('id',$currectFiscalYearId)->value('fyStartDate');
        // $dateToCompare = date('Y-m-d', strtotime('-1 day', strtotime($currentFiscalYearStartDate)));
        // $previousfiscalYearId = DB::table('gnr_fiscal_year')->where('fyEndDate',$dateToCompare)->value('id');
        //
        //
        //
        // //previous previous fiscal year id
        // $previousfiscalYearStartDate = DB::table('gnr_fiscal_year')->where('id',$previousfiscalYearId)->value('fyStartDate');
        // $dateToCompareTwo = date('Y-m-d', strtotime('-1 day', strtotime($previousfiscalYearStartDate)));
        // $previousPreviousfiscalYearId = DB::table('gnr_fiscal_year')->where('fyEndDate',$dateToCompareTwo)->value('id');
        // // dd($cashTypeLedgers);
        // //Cash Type Ledgers
        // $cashTypeLedgers = DB::table('acc_account_ledger')->where('accountTypeId',4)->pluck('id')->toArray();
        //
        // //Bank Type Ledgers
        // $bankTypeLedgers = DB::table('acc_account_ledger')->where('accountTypeId',5)->pluck('id')->toArray();

        // $openingbalanceInfo = DB::table('acc_opening_balance')
        //                     ->where('projectId',$data['projectSelected'])
        //                     ->whereIn('projectTypeId',$data['projectTypeId'])
        //                     ->whereIn('branchId',$data['branchId'])
        //                     ->select('ledgerId', 'fiscalYearId', 'balanceAmount')
        //                     ->get();

                            // dd($openingbalanceInfo);


        // IF Search By Fiscal Year
        if ($obData['searchMethodSelected'] == 1) {

            // if ($previousfiscalYearId!=null) {
            //
            //     $cashInHandForCurrentFiscalYear = $data['openingBalanceInfo']
            //                                     ->where('fiscalYearId',$previousfiscalYearId)
            //                                     ->whereIn('ledgerId',$cashTypeLedgers)
            //                                     ->sum('balanceAmount');
            //
            //
            //     if ($previousPreviousfiscalYearId!=null) {
            //         $cashInHandForPreviousFiscalYear = $data['openingBalanceInfo']
            //                                     ->where('fiscalYearId',$previousPreviousfiscalYearId)
            //                                     ->whereIn('ledgerId',$cashTypeLedgers)
            //                                     ->sum('balanceAmount');
            //     }
            //     else{
            //         $cashInHandForPreviousFiscalYear = 0;
            //     }
            //
            // }
            // else{
            //     $cashInHandForCurrentFiscalYear = 0;
            //     $cashInHandForPreviousFiscalYear = 0;
            // }
            //
            //
            // if ($previousfiscalYearId!=null) {
            //
            //     $cashInBankForCurrentFiscalYear = $data['openingBalanceInfo']
            //                                     ->whereIn('ledgerId',$bankTypeLedgers)
            //                                     ->where('fiscalYearId',$previousfiscalYearId)
            //                                     ->sum('balanceAmount');
            //
            //     if ($previousPreviousfiscalYearId!=null) {
            //          $cashInBankForPreviousFiscalYear = $data['openingBalanceInfo']
            //                                          ->whereIn('ledgerId',$bankTypeLedgers)
            //                                          ->where('fiscalYearId',$previousPreviousfiscalYearId)
            //                                          ->sum('balanceAmount');
            //     }
            //     else{
            //         $cashInBankForPreviousFiscalYear = 0;
            //     }
            //
            // }
            // else{
            //     $cashInBankForCurrentFiscalYear = 0;
            //     $cashInBankForPreviousFiscalYear = 0;
            // }
            //
            //
            //
            // if ($data['voucherTypeSelected']==2) {
            //     $cashInHandForPreviousFiscalYear = 0;
            //     $cashInBankForPreviousFiscalYear = 0;
            //
            //
            //     $cashInHandForCurrentFiscalYear = 0;
            //
            //     $cashInBankForCurrentFiscalYear = 0;
            //
            // }

            if ($obData['roundUpSelected']==1) {
                echo
                "<tr>
                <td style='text-align: left;'>ASSET</td>
                <td></td>
                <td></td>
                <td></td>
                </tr>".
                "<tr>
                <td style='text-align: left;'>OPENING BALANCE</td>
                <td></td>
                <td id='obPreviousFiscalYear' amount='".($obData['obPreviousYear'])."' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format($obData['obPreviousYear'],2)."</td>
                <td id='obCurrentFiscalYear' amount='".($obData['obThisYear'])."' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format($obData['obThisYear'],2)."</td>
                </tr>".
                "<tr>
                <td style='text-align: left;'>CASH IN HAND</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format($obData['obPreviousYearCash'],2)."</td>
                <td id='cashInHandForCurrentFiscalYear' amount='".$obData['obThisYearCash']."' style='text-align: right;padding-right:5px;'>".number_format($obData['obThisYearCash'],2)."</td>
                </tr>".
                "<tr>
                <td style='text-align: left;'>CASH AT BANK</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format($obData['obPreviousYearBank'],2)."</td>
                <td id='cashInBankForCurrentFiscalYear' amount='".$obData['obThisYearBank']."' style='text-align: right;padding-right:5px;'>".number_format($obData['obThisYearBank'],2)."</td>
                </tr>"
                ;
            }
            else{

                echo
                "<tr>
                <td style='text-align: left;'>ASSET</td>
                <td></td>
                <td></td>
                <td></td>
                </tr>".
                "<tr>
                <td style='text-align: left;'>OPENING BALANCE</td>
                <td></td>
                <td id='obPreviousFiscalYear' amount='".($obData['obPreviousYear'])."' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format($obData['obPreviousYear'],2,'.','')."</td>
                <td id='obCurrentFiscalYear' amount='".($obData['obThisYear'])."' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format($obData['obThisYear'],2,'.','')."</td>
                </tr>".
                "<tr>
                <td style='text-align: left;'>CASH IN HAND</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format($obData['obPreviousYearCash'],2,'.','')."</td>
                <td style='text-align: right;padding-right:5px;'>".number_format($obData['obThisYearCash'],2,'.','')."</td>
                </tr>".
                "<tr>
                <td style='text-align: left;'>CASH AT BANK</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format($obData['obPreviousYearBank'],2,'.','')."</td>
                <td style='text-align: right;padding-right:5px;'>".number_format($obData['obThisYearBank'],2,'.','')."</td>
                </tr>"
                ;

            }
            // dd($cashInHandForCurrentFiscalYear);

        }

        // IF Search By Current Year
        elseif ($obData['searchMethodSelected'] == 2) {

            // if ($previousfiscalYearId!=null) {
            //     $cashInHandForCurrentFiscalYear = $data['openingBalanceInfo']
            //                                     ->whereIn('ledgerId',$cashTypeLedgers)
            //                                     ->where('fiscalYearId',$previousfiscalYearId)
            //                                     ->sum('balanceAmount');
            // }
            // else{
            //     $cashInHandForCurrentFiscalYear = 0;
            // }
            //
            // if ($previousfiscalYearId!=null) {
            //     $cashInBankForCurrentFiscalYear = $data['openingBalanceInfo']
            //                                     ->whereIn('ledgerId',$bankTypeLedgers)
            //                                     ->where('fiscalYearId',$previousfiscalYearId)
            //                                     ->sum('balanceAmount');
            // }
            // else{
            //     $cashInBankForCurrentFiscalYear = 0;
            // }
            //
            // $obThisMonth = $cashInHandForCurrentFiscalYear+$data['thisPeriodTransactionOfCashTypeDebit']-$data['thisPeriodTransactionOfCashTypeCredit']+$cashInBankForCurrentFiscalYear+$data['thisPeriodTransactionOfBankTypeDebit']-$data['thisPeriodTransactionOfBankTypeCredit'];
            // $obThisYear = $cashInHandForCurrentFiscalYear+$cashInBankForCurrentFiscalYear;

            if ($obData['roundUpSelected']==1) {
                 echo
            "<tr>
                <td style='text-align: left;font-weight: bold;'>ASSET</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>OPENING BALANCE</td>
                <td></td>
                <td  id='obThisMonth' amount='".$obData['obThisMonth']."' style='text-align: right;padding-right:5px;font-weight:bold;font-weight:bold;'>".number_format($obData['obThisMonth'],2)."</td>
                <td id='obThisYear' amount='".$obData['obThisYear']."' style='text-align: right;padding-right:5px;font-weight:bold;font-weight:bold;'>".number_format($obData['obThisYear'],2)."</td>
                <td></td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>CASH IN HAND</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format($obData['obThisMonthCash'],2)."</td>
                <td style='text-align: right;padding-right:5px;'>".number_format($obThisYearCash,2)."</td>
                <td></td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>CASH AT BANK</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format($obData['obThisMonthBank'],2)."</td>
                <td style='text-align: right;padding-right:5px;'>".number_format($obData['obThisYearBank'],2)."</td>
                <td></td>
            </tr>"
            ;
            }
            else{
                 echo
            "<tr>
                <td style='text-align: left;font-weight: bold;'>ASSET</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>OPENING BALANCE</td>
                <td></td>
                <td  id='obThisMonth' amount='".$obData['obThisMonth']."' style='text-align: right;padding-right:5px;font-weight:bold;font-weight:bold;'>".number_format($obData['obThisMonth'],2,'.','')."</td>
                <td id='obThisYear' amount='".$obData['obThisYear']."' style='text-align: right;padding-right:5px;font-weight:bold;font-weight:bold;'>".number_format($obData['obThisYear'],2,'.','')."</td>
                <td></td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>CASH IN HAND</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format($obData['obThisMonthCash'],2,'.','')."</td>
                <td style='text-align: right;padding-right:5px;'>".number_format($obData['obThisYearCash'],2,'.','')."</td>
                <td></td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>CASH AT BANK</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format($obData['obThisMonthBank'],2,'.','')."</td>
                <td style='text-align: right;padding-right:5px;'>".number_format($obData['obThisYearBank'],2,'.','')."</td>
                <td></td>
            </tr>"
            ;
            }

            // dd($cashInBankForCurrentFiscalYear);
        }


        // IF Search By Date Range
        elseif ($obData['searchMethodSelected'] == 3) {


            // if ($previousfiscalYearId!=null) {
            //     $cashInHandForCurrentFiscalYear = $data['openingBalanceInfo']
            //                                     ->whereIn('ledgerId',$cashTypeLedgers)
            //                                     ->where('fiscalYearId',$previousfiscalYearId)
            //                                     ->sum('balanceAmount');
            // }
            // else{
            //     $cashInHandForCurrentFiscalYear = 0;
            // }
            //
            // if ($previousfiscalYearId!=null) {
            //     $cashInBankForCurrentFiscalYear = $data['openingBalanceInfo']
            //                                     ->whereIn('ledgerId',$bankTypeLedgers)
            //                                     ->where('fiscalYearId',$previousfiscalYearId)
            //                                     ->sum('balanceAmount');
            // }
            // else{
            //     $cashInBankForCurrentFiscalYear = 0;
            // }
            //
            //
            //
            // $obThisPeriod = $cashInHandForCurrentFiscalYear+$data['thisPeriodTransactionOfCashTypeDebit']-$data['thisPeriodTransactionOfCashTypeCredit']+$cashInBankForCurrentFiscalYear+$data['thisPeriodTransactionOfBankTypeDebit']-$data['thisPeriodTransactionOfBankTypeCredit'];

            if ($obData['roundUpSelected']==1) {
                echo
            "<tr>
                <td style='text-align: left;'>ASSET</td>
                <td></td>
                <td></td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>OPENING BALANCE</td>
                <td></td>
                <td id='obThisPeriod' amount='".$obData['obThisPeriod']."' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format($obData['obThisPeriod'],2)."</td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>CASH IN HAND</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format($obData['obThisPeriodCash'],2)."</td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>CASH AT BANK</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format($obData['obThisPeriodBank'],2)."</td>
            </tr>"
            ;
            }
            else{
                echo
            "<tr>
                <td style='text-align: left;'>ASSET</td>
                <td></td>
                <td></td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>OPENING BALANCE</td>
                <td></td>
                <td id='obThisPeriod' amount='".$obData['obThisPeriod']."' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format($obData['obThisPeriod'],2,'.','')."</td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>CASH IN HAND</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format($obData['obThisPeriodCash'],2,'.','')."</td>
            </tr>".
            "<tr>
                <td style='text-align: left;'>CASH AT BANK</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format($obData['obThisPeriodBank'],2,'.','')."</td>
            </tr>"
            ;
            }


        }

        }
dd(1);

    }






    function printClosingBalance($data)
    {

        if ($data['voucherTypeSelected']==2) {
            if ($data['searchMethodSelected']==1) {
                echo
                "<tr>
                <td style='text-align: left;'>ASSET</td>
                <td></td>
                <td></td>
                <td></td>
                </tr>".
                "<tr>
                <td style='text-align: left;'>CLOSING BALANCE</td>
                <td></td>
                <td id='cbPreviousFiscalYear' amount='0' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format(0,2)."</td>

                <td id='cbCurrentFiscalYear' amount='0' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format(0,2)."</td>
                </tr>".
                "<tr>
                <td style='text-align: left;'>CASH IN HAND</td>
                <td></td>
                <td id='cashInHandForPreviousFiscalYear' amount='' style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
                <td style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
                </tr>".
                "<tr>
                <td style='text-align: left;'>CASH AT BANK</td>
                <td></td>
                <td id='cashInBankForPreviousFiscalYear' amount='' style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
                <td style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
                </tr>"
                ;
            }
            else if ($data['searchMethodSelected']==2) {
                echo
                "<tr>
                <td style='text-align: left;font-weight: bold;'>ASSET</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                </tr>".
                "<tr>
                <td style='text-align: left;'>CLOSING BALANCE</td>
                <td></td>
                <td  id='cbThisMonth' amount='0' style='text-align: right;padding-right:5px;font-weight:bold;font-weight:bold;'>".number_format(0,2)."</td>
                <td id='cbThisYear' amount='0' style='text-align: right;padding-right:5px;font-weight:bold;font-weight:bold;'>".number_format(0,2)."</td>
                <td id='cbCumulative' amount='0' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format(0,2)."</td>
                </tr>".
                "<tr>
                <td style='text-align: left;'>CASH IN HAND</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
                <td style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
                <td style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>

                </tr>".
                "<tr>
                <td style='text-align: left;'>CASH AT BANK</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
                <td style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
                <td style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>

                </tr>"
                ;
            }
            else if ($data['searchMethodSelected']==3) {
                echo
                "<tr>
                <td style='text-align: left;'>ASSET</td>
                <td></td>
                <td></td>
                </tr>".
                "<tr>
                <td style='text-align: left;'>CLOSING BALANCE</td>
                <td></td>
                <td id='cbThisPeriod' amount='0' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format(0,2)."</td>
                </tr>".
                "<tr>
                <td style='text-align: left;'>CASH IN HAND</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
                </tr>".
                "<tr>
                <td style='text-align: left;'>CASH AT BANK</td>
                <td></td>
                <td style='text-align: right;padding-right:5px;'>".number_format(0,2)."</td>
                </tr>"
                ;
            }



        }



        //If not search by Only JV

        else{


            $startDate = $data['startDate'];
            $endDate = $data['endDate'];

            $currectFiscalYearId = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('id');

            //previous fiscal year id
            $currentFiscalYearStartDate = DB::table('gnr_fiscal_year')->where('id',$currectFiscalYearId)->value('fyStartDate');
            $dateToCompare = date('Y-m-d', strtotime('-1 day', strtotime($currentFiscalYearStartDate)));
            $previousfiscalYearId = DB::table('gnr_fiscal_year')->where('fyEndDate',$dateToCompare)->value('id');



            //previous previous fiscal year id
            $previousfiscalYearStartDate = DB::table('gnr_fiscal_year')->where('id',$previousfiscalYearId)->value('fyStartDate');
            $dateToCompareTwo = date('Y-m-d', strtotime('-1 day', strtotime($previousfiscalYearStartDate)));
            $previousPreviousfiscalYearId = DB::table('gnr_fiscal_year')->where('fyEndDate',$dateToCompareTwo)->value('id');

            //Cash Type Ledgers
            $cashTypeLedgers = DB::table('acc_account_ledger')->where('accountTypeId',4)->pluck('id')->toArray();

            //Bank Type Ledgers
            $bankTypeLedgers = DB::table('acc_account_ledger')->where('accountTypeId',5)->pluck('id')->toArray();


// dd($previousPreviousfiscalYearId);

            // IF Search By Fiscal Year
            if ($data['searchMethodSelected']==1) {

                if ($previousfiscalYearId!=null) {

                    // $cashInHandForCurrentFiscalYear = $data['openingBalanceInfo']
                    //                                 ->whereIn('ledgerId',$cashTypeLedgers)
                    //                                 ->where('fiscalYearId',$previousfiscalYearId)
                    //                                 ->sum('balanceAmount');


                    if ($previousPreviousfiscalYearId!=null) {
                        $cashInHandForPreviousFiscalYear = $data['openingBalanceInfo']
                                                        ->whereIn('ledgerId',$cashTypeLedgers)
                                                        ->where('fiscalYearId',$previousfiscalYearId)
                                                        ->sum('balanceAmount');
                    }
                    else{
                        $cashInHandForPreviousFiscalYear = 0;
                    }

                }
                else{
                    $cashInHandForCurrentFiscalYear = 0;
                    $cashInHandForPreviousFiscalYear = 0;
                }


                if ($previousfiscalYearId!=null) {

                    // $cashInBankForCurrentFiscalYear = $data['openingBalanceInfo']
                    //                                 ->whereIn('ledgerId',$bankTypeLedgers)
                    //                                 ->where('fiscalYearId',$previousfiscalYearId)
                    //                                 ->sum('balanceAmount');

                    if ($previousPreviousfiscalYearId!=null) {
                        $cashInBankForPreviousFiscalYear = $data['openingBalanceInfo']
                                                        ->whereIn('ledgerId',$bankTypeLedgers)
                                                        ->where('fiscalYearId',$previousfiscalYearId)
                                                        ->sum('balanceAmount');
                    }
                    else{
                        $cashInBankForPreviousFiscalYear = 0;
                    }

                }
                else{
                    $cashInBankForCurrentFiscalYear = 0;
                    $cashInBankForPreviousFiscalYear = 0;
                }

                //Transaction
                if ($data['branchSelected'] == Null || $data['branchSelected'] == 0) {

                    // previous
                    // $previousFiscalYearCashDebit = $data['previousYearInfo']->sum('cashDebit');
                    // $previousFiscalYearCashCredit = $data['previousYearInfo']->sum('cashCredit');
                    //
                    // $previousFiscalYearBankDebit = $data['previousYearInfo']->sum('bankDebit');
                    // $previousFiscalYearBankCredit = $data['previousYearInfo']->sum('bankCredit');

                    // current
                    $currentFiscalYearCahDebit = $data['thisYearTransactionOfCashTypeDebit'];
                    $currentFiscalYearCashCredit = $data['thisYearTransactionOfCashTypeCredit'];

                    $currentFiscalYearBankDebit = $data['$thisYearTransactionOfBankTypeDebit'];
                    $currentFiscalYearBankCredit = $data['$thisYearTransactionOfBankTypeCredit'];
                }
                else {
                    // previous
                    // $previousFiscalYearCashDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['previousfiscalYearVouchers'])->whereIn('debitAcc',$cashTypeLedgers)->sum('amount');
                    // $previousFiscalYearCashCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['previousfiscalYearVouchers'])->whereIn('creditAcc',$cashTypeLedgers)->sum('amount');
                    //
                    //
                    // $previousFiscalYearBankDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['previousfiscalYearVouchers'])->whereIn('debitAcc',$bankTypeLedgers)->sum('amount');
                    // $previousFiscalYearBankCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['previousfiscalYearVouchers'])->whereIn('creditAcc',$bankTypeLedgers)->sum('amount');

                    $currentFiscalYearCahDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['vouchers'])->whereIn('debitAcc',$cashTypeLedgers)->sum('amount');

                    // current
                    $currentFiscalYearCashCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['vouchers'])->whereIn('creditAcc',$cashTypeLedgers)->sum('amount');



                    $currentFiscalYearBankDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['vouchers'])->whereIn('debitAcc',$bankTypeLedgers)->sum('amount');

                    $currentFiscalYearBankCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['vouchers'])->whereIn('creditAcc',$bankTypeLedgers)->sum('amount');
                }

                // $previousFiscalYearCash = $previousFiscalYearCashDebit - $previousFiscalYearCashCredit;
                // $previousFiscalYearBank = $previousFiscalYearBankDebit - $previousFiscalYearBankCredit;
                $currentFiscalYearCash = $currentFiscalYearCahDebit - $currentFiscalYearCashCredit;
                $currentFiscalYearBank = $currentFiscalYearBankDebit - $currentFiscalYearBankCredit;
                //End Transaction




                if ($data['roundUpSelected']==1) {
                    echo
                    "<tr>
                    <td style='text-align: left;'>ASSET</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    </tr>".
                    "<tr>
                    <td style='text-align: left;'>CLOSING BALANCE</td>
                    <td></td>
                    <td id='cbPreviousFiscalYear' amount='".($cashInHandForPreviousFiscalYear + $cashInBankForPreviousFiscalYear)."' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format($cashInHandForPreviousFiscalYear + $cashInBankForPreviousFiscalYear,2)."</td>

                    <td id='cbCurrentFiscalYear' amount='".($cashInHandForPreviousFiscalYear + $currentFiscalYearCash + $cashInBankForPreviousFiscalYear + $currentFiscalYearBank)."' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format($cashInHandForPreviousFiscalYear + $currentFiscalYearCash + $cashInBankForPreviousFiscalYear + $currentFiscalYearBank,2)."</td>
                    </tr>".
                    "<tr>
                    <td style='text-align: left;'>CASH IN HAND</td>
                    <td></td>
                    <td id='cashInHandForPreviousFiscalYear' amount='' style='text-align: right;padding-right:5px;'>".number_format($cashInHandForPreviousFiscalYear, 2)."</td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInHandForPreviousFiscalYear + $currentFiscalYearCash,2)."</td>
                    </tr>".
                    "<tr>
                    <td style='text-align: left;'>CASH AT BANK</td>
                    <td></td>
                    <td id='cashInBankForPreviousFiscalYear' amount='' style='text-align: right;padding-right:5px;'>".number_format($cashInBankForPreviousFiscalYear, 2)."</td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInBankForPreviousFiscalYear + $currentFiscalYearBank, 2)."</td>
                    </tr>"
                    ;
                }
                else{
                    echo
                    "<tr>
                    <td style='text-align: left;'>ASSET</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    </tr>".
                    "<tr>
                    <td style='text-align: left;'>CLOSING BALANCE</td>
                    <td></td>
                    <td id='cbPreviousFiscalYear' amount='".($cashInHandForPreviousFiscalYear + $cashInBankForPreviousFiscalYear)."' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format($cashInHandForPreviousFiscalYear + $cashInBankForPreviousFiscalYear,2,'.','')."</td>

                    <td id='cbCurrentFiscalYear' amount='".($cashInHandForPreviousFiscalYear + $currentFiscalYearCash + $cashInBankForPreviousFiscalYear + $currentFiscalYearBank)."' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format($cashInHandForPreviousFiscalYear + $currentFiscalYearCash + $cashInBankForPreviousFiscalYear + $currentFiscalYearBank,2,'.','')."</td>
                    </tr>".
                    "<tr>
                    <td style='text-align: left;'>CASH IN HAND</td>
                    <td></td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInHandForPreviousFiscalYear,2,'.','')."</td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInHandForPreviousFiscalYear+$currentFiscalYearCash,2,'.','')."</td>
                    </tr>".
                    "<tr>
                    <td style='text-align: left;'>CASH AT BANK</td>
                    <td></td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInBankForPreviousFiscalYear,2,'.','')."</td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInBankForPreviousFiscalYear+$currentFiscalYearBank,2,'.','')."</td>
                    </tr>"
                    ;
                }




            }

            // IF Search By Current Year
            elseif ($data['searchMethodSelected']==2) {

                // dd($previousfiscalYearId);
                if ($previousfiscalYearId!=null) {
                    $cashInHandForPreviousFiscalYear = $data['openingBalanceInfo']
                                                    ->whereIn('ledgerId',$cashTypeLedgers)
                                                    ->where('fiscalYearId',$previousfiscalYearId)
                                                    ->sum('balanceAmount');

                    // echo $cashInHandForCurrentFiscalYear;
                    // print_r($cashTypeLedgers);    //348, 412
                    // $previousfiscalYearId = 2;
                    // $data['projectSelected'] = 1;
                    // print_r($data['projectTypeId']) = 1,2,3,7,8,9,10,11,12,13,14;
                    // print_r($data['branchId'])=1;
                    // $cashInHandForCurrentFiscalYear = 24849;
                }
                else{
                    $cashInHandForPreviousFiscalYear = 0;
                }

                if ($previousfiscalYearId!=null) {
                    $cashInBankForPreviousFiscalYear = $data['openingBalanceInfo']
                    ->whereIn('ledgerId',$bankTypeLedgers)
                    ->where('fiscalYearId',$previousfiscalYearId)
                    ->sum('balanceAmount');

                    // echo $data['projectSelected']."<br>";
                    // echo "<br>";
                    // print_r($data['projectTypeId']) ;
                    // echo "<br>";
                    // print_r($data['branchId']) ;
                    // $cashInBankForCurrentFiscalYear=1334207.76 ;
                    // $cashInHandForCurrentFiscalYear=24849 ;
                    // print_r($bankTypeLedgers);
                    // print_r($cashTypeLedgers); = 348,412;
                    // $previousfiscalYearId=2;
                    // projectId=15;
                    //projectType = 1, 2,3 ,7,8,9,10,11,12,13,14
                }
                else{
                    $cashInBankForPreviousFiscalYear = 0;
                }

                $obThisMonth = $cashInHandForPreviousFiscalYear + $data['thisPeriodTransactionOfCashTypeDebit'] - $data['thisPeriodTransactionOfCashTypeCredit'] + $cashInBankForPreviousFiscalYear + $data['thisPeriodTransactionOfBankTypeDebit'] - $data['thisPeriodTransactionOfBankTypeCredit'];
                $obThisYear = $cashInHandForPreviousFiscalYear + $cashInBankForPreviousFiscalYear;

                // echo $cashInHandForCurrentFiscalYear. "<br>";
                // echo $data['thisMonthTransactionOfBankTypeDebit']. "<br>";
                // echo $data['thisMonthTransactionOfBankTypeCredit']. "<br>";
                // echo $data['thisMonthTransactionOfCashTypeDebit']. "<br>";
                // echo $data['thisMonthTransactionOfCashTypeCredit']. "<br>";
                // echo $cashInBankForCurrentFiscalYear. "<br>";




                //Transaction
                if ($data['branchSelected'] == Null || $data['branchSelected'] == 0) {

                    $thisMonthCashDebit = $data['thisMonthTransactionOfCashTypeDebit'];
                    $thisMonthCashCredit = $data['thisMonthTransactionOfCashTypeCredit'];
                    $thisMonthCash = $thisMonthCashDebit - $thisMonthCashCredit;

                    $thisMonthBankDebit = $data['thisMonthTransactionOfBankTypeDebit'];
                    $thisMonthBankCredit = $data['thisMonthTransactionOfBankTypeCredit'];
                    $thisMonthBank = $thisMonthBankDebit - $thisMonthBankCredit;

                    $currentFiscalYearCahDebit = $data['thisYearTransactionOfCashTypeDebit'];
                    $currentFiscalYearCashCredit = $data['thisYearTransactionOfCashTypeCredit'];
                    $currentFiscalYearCash = $currentFiscalYearCahDebit - $currentFiscalYearCashCredit;

                    $currentFiscalYearBankDebit = $data['thisYearTransactionOfBankTypeDebit'];
                    $currentFiscalYearBankCredit = $data['thisYearTransactionOfBankTypeCredit'];
                    $currentFiscalYearBank = $currentFiscalYearBankDebit - $currentFiscalYearBankCredit;

                }
                else {

                    $thisMonthCashDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['thisMonthVoucherIds'])->whereIn('debitAcc',$cashTypeLedgers)->sum('amount');
                    $thisMonthCashCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['thisMonthVoucherIds'])->whereIn('creditAcc',$cashTypeLedgers)->sum('amount');
                    $thisMonthCash = $thisMonthCashDebit - $thisMonthCashCredit;

                    $thisMonthBankDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['thisMonthVoucherIds'])->whereIn('debitAcc',$bankTypeLedgers)->sum('amount');
                    $thisMonthBankCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['thisMonthVoucherIds'])->whereIn('creditAcc',$bankTypeLedgers)->sum('amount');
                    $thisMonthBank = $thisMonthBankDebit - $thisMonthBankCredit;


                    $currentFiscalYearCahDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['vouchers'])->whereIn('debitAcc',$cashTypeLedgers)->sum('amount');

                    $currentFiscalYearCashCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['vouchers'])->whereIn('creditAcc',$cashTypeLedgers)->sum('amount');

                    $currentFiscalYearCash = $currentFiscalYearCahDebit - $currentFiscalYearCashCredit;

                    $currentFiscalYearBankDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['vouchers'])->whereIn('debitAcc',$bankTypeLedgers)->sum('amount');

                    $currentFiscalYearBankCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['vouchers'])->whereIn('creditAcc',$bankTypeLedgers)->sum('amount');

                    $currentFiscalYearBank = $currentFiscalYearBankDebit - $currentFiscalYearBankCredit;
                }

                //End Transaction





                if ($data['roundUpSelected']==1) {
                    echo
                    "<tr>
                    <td style='text-align: left;font-weight: bold;'>ASSET</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    </tr>".
                    "<tr>
                    <td style='text-align: left;'>CLOSING BALANCE</td>
                    <td></td>
                    <td  id='cbThisMonth' amount='".($obThisMonth + $thisMonthCash + $thisMonthBank)."' style='text-align: right;padding-right:5px;font-weight:bold;font-weight:bold;'>".number_format($obThisMonth + $thisMonthCash + $thisMonthBank,2)."</td>
                    <td id='cbThisYear' amount='".($obThisYear + $currentFiscalYearCash + $currentFiscalYearBank)."' style='text-align: right;padding-right:5px;font-weight:bold;font-weight:bold;'>".number_format($obThisYear + $currentFiscalYearCash + $currentFiscalYearBank,2)."</td>
                    <td id='cbCumulative' amount='".($obThisYear + $currentFiscalYearCash + $currentFiscalYearBank)."' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format($obThisYear + $currentFiscalYearCash + $currentFiscalYearBank,2)."</td>
                    </tr>".
                    "<tr>
                    <td style='text-align: left;'>CASH IN HAND</td>
                    <td></td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInHandForPreviousFiscalYear + $data['thisPeriodTransactionOfCashTypeDebit'] - $data['thisPeriodTransactionOfCashTypeCredit'] + $thisMonthCash, 2)."</td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInHandForPreviousFiscalYear + $currentFiscalYearCash,2)."</td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInHandForPreviousFiscalYear + $currentFiscalYearCash,2)."</td>

                    </tr>".
                    "<tr>
                    <td style='text-align: left;'>CASH AT BANK</td>
                    <td></td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInBankForPreviousFiscalYear + $data['thisPeriodTransactionOfBankTypeDebit'] - $data['thisPeriodTransactionOfBankTypeCredit'] + $thisMonthBank,2)."</td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInBankForPreviousFiscalYear + $currentFiscalYearBank,2)."</td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInBankForPreviousFiscalYear + $currentFiscalYearBank,2)."</td>

                    </tr>"
                    ;
                }
                else{
                    echo
                    "<tr>
                    <td style='text-align: left;font-weight: bold;'>ASSET</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    </tr>".
                    "<tr>
                    <td style='text-align: left;'>CLOSING BALANCE</td>
                    <td></td>
                    <td  id='cbThisMonth' amount='".($obThisMonth + $thisMonthCash + $thisMonthBank)."' style='text-align: right;padding-right:5px;font-weight:bold;font-weight:bold;'>".number_format($obThisMonth + $thisMonthCash + $thisMonthBank,2,'.','')."</td>
                    <td id='cbThisYear' amount='".($obThisYear + $currentFiscalYearCash + $currentFiscalYearBank)."' style='text-align: right;padding-right:5px;font-weight:bold;font-weight:bold;'>".number_format($obThisYear + $currentFiscalYearCash + $currentFiscalYearBank,2)."</td>
                    <td id='cbCumulative' amount='".($obThisYear + $currentFiscalYearCash + $currentFiscalYearBank)."' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format($obThisYear + $currentFiscalYearCash + $currentFiscalYearBank,2,'.','')."</td>
                    </tr>".
                    "<tr>
                    <td style='text-align: left;'>CASH IN HAND</td>
                    <td></td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInHandForPreviousFiscalYear + $data['thisPeriodTransactionOfCashTypeDebit'] - $data['thisPeriodTransactionOfCashTypeCredit'] + $thisMonthCash,2,'.','')."</td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInHandForPreviousFiscalYear + $currentFiscalYearCash,2,'.','')."</td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInHandForPreviousFiscalYear + $currentFiscalYearCash,2,'.','')."</td>

                    </tr>".
                    "<tr>
                    <td style='text-align: left;'>CASH AT BANK</td>
                    <td></td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInBankForCurrentFiscalYear+$data['thisMonthTransactionOfBankTypeDebit']-$data['thisMonthTransactionOfBankTypeCredit']+$thisMonthBank,2,'.','')."</td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInBankForPeriodFiscalYear + $currentFiscalYearBank,2,'.','')."</td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInBankForPeriodFiscalYear + $currentFiscalYearBank,2,'.','')."</td>

                    </tr>"
                    ;
                }


            }


            // IF Search By Date Range
            elseif ($data['searchMethodSelected']==3) {


                if ($previousfiscalYearId!=null) {
                    $cashInHandForCurrentFiscalYear = $data['openingBalanceInfo']
                                                    ->whereIn('ledgerId',$cashTypeLedgers)
                                                    ->where('fiscalYearId',$previousfiscalYearId)
                                                    ->sum('balanceAmount');
                }
                else{
                    $cashInHandForCurrentFiscalYear = 0;
                }

                if ($previousfiscalYearId!=null) {
                    $cashInBankForCurrentFiscalYear = $data['openingBalanceInfo']
                                                    ->whereIn('ledgerId',$bankTypeLedgers)
                                                    ->where('fiscalYearId',$previousfiscalYearId)
                                                    ->sum('balanceAmount');
                }
                else{
                    $cashInBankForCurrentFiscalYear = 0;
                }

                $obThisPeriod = $cashInHandForCurrentFiscalYear+$data['thisPeriodTransactionOfCashTypeDebit']-$data['thisPeriodTransactionOfCashTypeCredit']+$cashInBankForCurrentFiscalYear+$data['thisPeriodTransactionOfBankTypeDebit']-$data['thisPeriodTransactionOfBankTypeCredit'];




                //Transaction
                if ($data['branchSelected'] == Null || $data['branchSelected'] == 0) {

                    $thisPeriodCashDebit = $data['dateRangeTransactionOfCashTypeDebit'];
                    $thisPeriodCashCredit = $data['dateRangeTransactionOfCashTypeCredit'];
                    $thisPeriodCash = $thisPeriodCashDebit - $thisPeriodCashCredit;

                    $thisPeriodBankDebit = $data['dateRangeTransactionOfBankTypeDebit'];
                    $thisPeriodBankCredit = $data['dateRangeTransactionOfBankTypeCredit'];
                    $thisPeriodBank = $thisPeriodBankDebit - $thisPeriodBankCredit;
                }
                else {

                    $thisPeriodCashDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['thisPeriodVoucherIds'])->whereIn('debitAcc',$cashTypeLedgers)->sum('amount');
                    $thisPeriodCashCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['thisPeriodVoucherIds'])->whereIn('creditAcc',$cashTypeLedgers)->sum('amount');
                    $thisPeriodCash = $thisPeriodCashDebit - $thisPeriodCashCredit;

                    $thisPeriodBankDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['thisPeriodVoucherIds'])->whereIn('debitAcc',$bankTypeLedgers)->sum('amount');
                    $thisPeriodBankCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$data['thisPeriodVoucherIds'])->whereIn('creditAcc',$bankTypeLedgers)->sum('amount');
                    $thisPeriodBank = $thisPeriodBankDebit - $thisPeriodBankCredit;
                }


                //End Transaction


                if ($data['roundUpSelected']==1) {
                    echo
                    "<tr>
                    <td style='text-align: left;'>ASSET</td>
                    <td></td>
                    <td></td>
                    </tr>".
                    "<tr>
                    <td style='text-align: left;'>CLOSING BALANCE</td>
                    <td></td>
                    <td id='cbThisPeriod' amount='".($obThisPeriod+$thisPeriodCash+$thisPeriodBank)."' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format($obThisPeriod+$thisPeriodCash+$thisPeriodBank,2)."</td>
                    </tr>".
                    "<tr>
                    <td style='text-align: left;'>CASH IN HAND</td>
                    <td></td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInHandForCurrentFiscalYear+$data['thisPeriodTransactionOfCashTypeDebit']-$data['thisPeriodTransactionOfCashTypeCredit']+$thisPeriodCash,2)."</td>
                    </tr>".
                    "<tr>
                    <td style='text-align: left;'>CASH AT BANK</td>
                    <td></td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInBankForCurrentFiscalYear+$data['thisPeriodTransactionOfBankTypeDebit']-$data['thisPeriodTransactionOfBankTypeCredit']+$thisPeriodBank,2)."</td>
                    </tr>"
                    ;
                }
                else{
                    echo
                    "<tr>
                    <td style='text-align: left;'>ASSET</td>
                    <td></td>
                    <td></td>
                    </tr>".
                    "<tr>
                    <td style='text-align: left;'>CLOSING BALANCE</td>
                    <td></td>
                    <td id='cbThisPeriod' amount='".($obThisPeriod+$thisPeriodCash+$thisPeriodBank)."' style='text-align: right;padding-right:5px;font-weight:bold;'>".number_format($obThisPeriod+$thisPeriodCash+$thisPeriodBank,2,'.','')."</td>
                    </tr>".
                    "<tr>
                    <td style='text-align: left;'>CASH IN HAND</td>
                    <td></td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInHandForCurrentFiscalYear+$data['thisPeriodTransactionOfCashTypeDebit']-$data['thisPeriodTransactionOfCashTypeCredit']+$thisPeriodCash,2,'.','')."</td>
                    </tr>".
                    "<tr>
                    <td style='text-align: left;'>CASH AT BANK</td>
                    <td></td>
                    <td style='text-align: right;padding-right:5px;'>".number_format($cashInBankForCurrentFiscalYear+$data['thisPeriodTransactionOfBankTypeDebit']-$data['thisPeriodTransactionOfBankTypeCredit']+$thisPeriodBank,2,'.','')."</td>
                    </tr>"
                    ;
                }

            }

        }

        // dd(1);

    }


@endphp


<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options"> <?php $grandParent=0; ?>
          <div class="panel-options">
                {{-- <a href="" class="btn btn-info pull-right addViewBtn" id="print"><i class="fa fa-print" aria-hidden="true"></i> Print</a> --}}
                <button id="printIcon" class="btn btn-info pull-right"  target="_blank" style="font-size: 14px; margin-top: 10px; border: 3px solid #525659; border-radius: 25px;">
                        <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
                    </button>

            </div>
              {{-- <a href="{{url('addLedger/'.encrypt($grandParent))}}" class="btn btn-info pull-right addViewBtn" style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Ledger</a> --}}
          </div>
          <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Receipt Payment Statement</h3>
        </div>
        {{-- <h5>kxghixd</h5> --}}

        <div class="panel-body panelBodyView">




            <!-- Filtering Start-->
           <div class="row" id="filtering-group">

                                <div class="form-horizontal form-groups" style="padding-right: 0px;">

                                    {!! Form::open(['url' => 'receiptPaymentStatement','method' => 'get']) !!}
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
                                                <select name="searchProject" class="form-control input-sm" id="searchProject">

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
                                                {!! Form::label('', 'Pro. Type:', ['class' => 'control-label pull-left']) !!}
                                            </div>

                                            <div class="col-sm-12">
                                                <select name="searchProjectType" class="form-control input-sm" id="searchProjectType">
                                                    <option value="">All</option>
                                                    @foreach($projectTypes as $projectType)
                                                    <option value="{{$projectType->id}}" @if($projectType->id==$projectTypeSelected){{"selected=selected"}}@endif>{{str_pad($projectType->projectTypeCode,3,"0",STR_PAD_LEFT).'-'.$projectType->name}}</option>
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
                                                <select name="searchBranch" class="form-control input-sm" id="searchBranch">
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

                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                             <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('', 'V. Type:', ['class' => 'control-label pull-left']) !!}
                                            </div>

                                            <div class="col-sm-12">
                                                {!! Form::select('voucherType',[''=>'Without JV',1=>'With JV',2=>'Only JV'],$voucherTypeSelected,['class'=>'form-control']) !!}

                                            </div>
                                        </div>
                                    </div>

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
                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black;">
                                            <div style="text-align: center;" class="col-sm-12">
                                                {!! Form::label('', 'Search By:', ['class' => 'control-label pull-left']) !!}
                                            </div>

                                            <div class="col-sm-12">
                                                {!! Form::select('searchMethod',['1'=>'Fiscal Year','2'=>'Current Year','3'=>'Date Range'],$searchMethodSelected,['id'=>'searchMethod','class'=>'form-control input-sm']) !!}

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1" style="display: none;" id="fiscalYearDiv">
                                        <div class="form-group" style="font-size: 13px; color:black">
                                            {!! Form::label('', ' ', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12" style="padding-top: 18px;">

                                                {!! Form::select('fiscalYear', $fiscalYears, $fiscalYearSelected, array('class'=>'form-control input-sm', 'id' => 'fiscalYear')) !!}

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
                                                        {!! Form::text('dateFrom',$dateFromSelected,['id'=>'dateFrom','placeholder'=>'From','class'=>'form-control input-sm','readonly','style'=>'cursor:pointer']) !!}
                                                        <p id="dateFrome" style="color: red;display: none;">*Required</p>
                                                    </div>
                                                    <div class="col-sm-6" id="dateToDiv">
                                                        {!! Form::text('dateTo',$dateToSelected,['id'=>'dateTo','placeholder'=>'To','class'=>'form-control input-sm','readonly','style'=>'cursor:pointer']) !!}
                                                        <p id="dateToe" style="color: red;display: none;">*Required</p>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:black">

                                            <div class="col-sm-12" style="padding-top: 25px;">

                                                {!! Form::submit('Search',['id'=>'search','class'=>'btn btn-primary btn-s','style'=>'font-size:12px;']) !!}
                                            </div>
                                        </div>
                                        {!! Form::close() !!}
                                    </div>

                                </div>

                        </div> {{-- End Filtering Group --}}
            <!-- filtering end-->



            @if(!$firstRequest)

            {{-- @php
               if($branchSelected===0){
                    $selectedBranchName = "All Branches";
                   }
               else{
                $selectedBranchName = DB::table('gnr_branch')->where('id',$branchSelected)->value('name');
               }
                $selectedProjectName = DB::table('gnr_project')->where('id',$projectSelected)->value('name');
                $selectedProjectTypeName = DB::table('gnr_project_type')->where('id',$projectTypeSelected)->value('name');


            @endphp --}}

            <div id="printingContent">

                <div  class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold;">


                        <?php
                            $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
                        ?>
                        <span style="font-size:14px;">{{$company->name}}</span><br/>
                        <span style="font-size:11px;">{{$company->address}}</span><br/>
                        <span style="text-decoration: underline;  font-size:14px;">Receipt Payment Statement</span><br/>
                        <span style="text-align: center;font-size: 14;padding: 0px;margin: 0px;">{{date('F d, Y',strtotime($endDate))}}</span>
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
                                    <span style="font-weight: bold;">Reporting Peroid : <?php echo str_repeat('&nbsp;', 3);?></span>
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

                {{-- <div  style="display: none;">

                   <p style="padding: 0px;margin: 0px;font-size: 11px;">Branch Name :
                        @php
                            if($selectedBranchName==null){
                                echo "All";
                            }
                            else{
                                echo $selectedBranchName;
                            }
                         @endphp
                         <span style='float: right;'>
                             Reporting Peroid : {{date('d-m-Y',strtotime($startDate))." to ".date('d-m-Y',strtotime($endDate))}}
                         </span>
                    </p>


                    <p style="padding: 0px;margin: 0px;font-size: 11px;">Project Name :
                        @php
                            if($selectedProjectName==null){
                                echo "All";
                            }
                            else{
                                echo $selectedProjectName;
                            }
                        @endphp

                        <span style='float: right;'>
                            Print Date : {{date('F d,Y')}}
                        </span>

                    </p>



                    <p style="padding: 0px;margin: 0px;font-size: 11px;">Project Type :
                            @php
                                if($selectedProjectTypeName==null){
                                    echo "All";
                                }
                                else{
                                    echo $selectedProjectTypeName;
                                }
                             @endphp
                             <span style='float: right;'>
                                Voucher :
                                @php
                                    if ($voucherTypeSelected==null) {
                                        echo "Without JV";
                                    }
                                    elseif($voucherTypeSelected==1){
                                        echo "With JV";
                                    }
                                    elseif ($voucherTypeSelected==2) {
                                        echo "Only JV";
                                    }
                                @endphp
                            </span>

                    </p>

                </div> --}}


<br>

          <table id="CRPStable" class="table table-striped table-bordered" style="color: black;font-size:11px;border-collapse: collapse;margin-bottom:50px;" border= "1px solid black;" width="100%" cellpadding="0" cellspacing="0">
            <thead>
              <tr style="vertical-align: top;">
                <th>Particulars</th>
                @if($searchMethodSelected==1)
                @php
                    $fiscalYearName = DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('name');

                    $curentFiscalYearStartDate = DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyStartDate');
                    $previousfiscalYearDate = strtotime($curentFiscalYearStartDate.' -1 year');
                    $Fdate = date('Y-m-d',$previousfiscalYearDate);
                    $previousfiscalYear = DB::table('gnr_fiscal_year')->where('fyStartDate','=',$Fdate)->value('name');

                @endphp
                <th>Notes</th>
                <th>FI Year <br> ({{$previousfiscalYear}})</th>
                <th>FI Year <br> ({{$fiscalYearName}})</th>

                @elseif($searchMethodSelected==2)
                <th>Notes</th>
                <th>This Month</th>
                <th>This Year</th>
                <th>Cumulative</th>

                @elseif($searchMethodSelected==3)
                <th>Notes</th>
                <th>Time Period <br> {{date('d-m-Y',strtotime($startDate))." to ".date('d-m-Y',strtotime($endDate))}}</th>
                @endif
              </tr>


            </thead>
            <tbody>
                @php
                $obData = array(
                    'searchMethodSelected'                      => $searchMethodSelected,
                    'roundUpSelected'                           => $roundUpSelected,
                    'voucherTypeSelected'                       => $voucherTypeSelected,
                    'obThisMonthCash'                           => $obThisMonthCash,
                    'obThisMonthBank'                           => $obThisMonthBank,
                    'obThisMonth'                               => $obThisMonth,
                    'obThisYearCash'                            => $obThisYearCash,
                    'obThisYearBank'                            => $obThisYearBank,
                    'obThisYear'                                => $obThisYear,
                    'obPreviousYearCash'                        => $obPreviousYearCash,
                    'obPreviousYearBank'                        => $obPreviousYearBank,
                    'obPreviousYear'                            => $obPreviousYear,
                    'obThisPeriodCash'                          => $obThisPeriodCash,
                    'obThisPeriodBank'                          => $obThisPeriodBank,
                    'obThisPeriod'                              => $obThisPeriod,
                );

                $cbData = array(
                    'searchMethodSelected'                      => $searchMethodSelected,
                    'roundUpSelected'                           => $roundUpSelected,
                    'voucherTypeSelected'                       => $voucherTypeSelected
                );
                // dd($data);
                // printClosingBalance($data);
                printOpeningBalance($obData);
                @endphp



                {{-- Receipt --}}

                @if($searchMethodSelected==1)
                    <tr id="receiptBegin"><td colspan="4" style="text-align: left;font-weight: bold;font-size: 14px;">Receipt</td></tr>
                @elseif($searchMethodSelected==2)
                    <tr id="receiptBegin"><td colspan="5" style="text-align: left;font-weight: bold;font-size: 14px;">Receipt</td></tr>
                @elseif($searchMethodSelected==3)
                    <tr id="receiptBegin"><td colspan="3" style="text-align: left;font-weight: bold;font-size: 14px;">Receipt</td></tr>
                @endif

                <?php $no=0; $loopTrack=0; $type = "payment";?>
                @php
                $info = array(
                    'loopTrack'                     => $loopTrack,
                    'vouchers'                      => $vouchers,
                    'previousfiscalYearVouchers'    => $previousfiscalYearVouchers,
                    'openingVouchers'               => $openingVouchers,
                    'roundUpSelected'               => $roundUpSelected,
                    'fiscalYearId'                  => $fiscalYearId,
                    'searchMethodSelected'          => $searchMethodSelected,
                    'thisMonthVoucherIds'           => $thisMonthVoucherIds,
                    'openingBalanceInfo'            => $openingBalanceInfo,
                    'thisMonthInfo'                 => $thisMonthInfo,
                    'thisPeriodInfo'                => $thisPeriodInfo,
                    'thisYearInfo'                  => $thisYearInfo,
                    'type'                          => $type,
                    'startDate'                     => $startDate,
                    'endDate'                       => $endDate,
                    'voucherTypeSelected'           => $voucherTypeSelected,
                    'projectSelected'               => $projectSelected,
                    'projectTypeId'                 => $projectTypeId,
                    'branchSelected'                => $branchSelected,
                    'newBranches'                   => $newBranches
                );
                @endphp




                @foreach($ledgers as $ledger)

                    @if(in_array($ledger->id,$recpeitLegderIds))

                        <?php
                        //$loopTrack=0;
                        $info['loopTrack']=0;
                        eachRow($ledger, $info);
                        ?>

                        <?php
                        if($ledger->isGroupHead==1){
                            $children1=DB::table('acc_account_ledger')->select('id','name','code','isGroupHead')->where('parentId', $ledger->id)->orderBy('ordering', 'asc')->get();
                            ?>
                            @foreach($children1 as $child1)
                                @if(in_array($child1->id,$recpeitLegderIds))
                                    <?php
                                    $info['loopTrack']=1;
                                    eachRow($child1, $info);
                                    ?>

                                    <?php
                                    if($child1->isGroupHead==1){
                                        $children2=DB::table('acc_account_ledger')->select('id','name','code','isGroupHead')->where('parentId', $child1->id)->orderBy('ordering', 'asc')->get();
                                        ?>
                                        @foreach($children2 as $child2)
                                            @if(in_array($child2->id,$recpeitLegderIds))
                                                <?php
                                                $info['loopTrack']=2;
                                                eachRow($child2, $info);
                                                ?>

                                                <?php
                                                if($child2->isGroupHead==1){
                                                    $children3=DB::table('acc_account_ledger')->select('id','name','code','isGroupHead')->where('parentId', $child2->id)->orderBy('ordering', 'asc')->get();
                                                    ?>
                                                    @foreach($children3 as $child3)
                                                        @if(in_array($child3->id,$recpeitLegderIds))
                                                            <?php
                                                            $info['loopTrack']=3;
                                                            eachRow($child3, $info);
                                                            ?>

                                                            <?php
                                                            if($child3->isGroupHead==1){
                                                                $children4=DB::table('acc_account_ledger')->select('id','name','code','isGroupHead')->where('parentId', $child3->id)->orderBy('ordering', 'asc')->get();
                                                                ?>
                                                                @foreach($children4 as $child4)
                                                                    @if(in_array($child4->id,$recpeitLegderIds))
                                                                        <?php
                                                                        $info['loopTrack']=4;
                                                                        $dataForChild3 = eachRow($child4, $info);
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

                                <tr id="totalReceipt" style="font-weight: bold;border-bottom-width: 0px;">
                                    @if($searchMethodSelected==1)
                                        <td colspan="2" style="text-align: left;">Total Receipt</td>
                                        <td class="tRpreviousFY" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                                        <td class="tRcurrentFY" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>

                                    @elseif($searchMethodSelected==2)
                                        <td colspan="2" style="text-align: left;">Total Receipt</td>
                                        <td class="tRthisMonth" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                                        <td class="tRThisYear" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                                        <td class="tRCumulative" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                                    @elseif($searchMethodSelected==3)
                                        <td colspan="2" style="text-align: left;">Total Receipt</td>
                                        <td class="tRThisPeriod" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>

                                    @endif
                                </tr>

                                <tr id="grandTotalReceipt" style="font-weight: bold; border-top-width: 0px;">
                                    @if($searchMethodSelected==1)
                                        <td colspan="2" style="text-align: left;">Total</td>
                                        <td class="gtRpreviousFY" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                                        <td class="gtRcurrentFY" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>

                                    @elseif($searchMethodSelected==2)
                                        <td colspan="2" style="text-align: left;">Total</td>
                                        <td class="gtRthisMonth" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                                        <td class="gtRThisYear" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                                        <td class="gtRCumulative" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                                    @elseif($searchMethodSelected==3)
                                        <td colspan="2" style="text-align: left;">Total</td>
                                        <td class="gtRThisPeriod" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>

                                    @endif
                                </tr>
                                {{-- End Receipt --}}



                                {{-- Payment --}}

                                @if($searchMethodSelected==1)
                                    <tr id="startPayment"><td colspan="4" style="text-align: left;font-weight: bold;font-size: 14px;">Payment</td></tr>
                                @elseif($searchMethodSelected==2)
                                    <tr id="startPayment"><td colspan="5" style="text-align: left;font-weight: bold;font-size: 14px;">Payment</td></tr>
                                @elseif($searchMethodSelected==3)
                                    <tr id="startPayment"><td colspan="3" style="text-align: left;font-weight: bold;font-size: 14px;">Payment</td></tr>
                                @endif

                                <?php $no=0; $loopTrack=0; $info['type'] = "receipt";?>
                                @foreach($ledgers as $ledger)

                                    @if(in_array($ledger->id,$paymentLegderIds))

                                        <?php
                                        $info['loopTrack']=0;
                                        eachRow($ledger, $info);
                                        ?>

                                        <?php
                                        if($ledger->isGroupHead==1){
                                            $children1=DB::table('acc_account_ledger')->select('id','name','code','isGroupHead')->where('parentId', $ledger->id)->orderBy('ordering', 'asc')->get();
                                            ?>
                                            @foreach($children1 as $child1)
                                                @if(in_array($child1->id,$paymentLegderIds))
                                                    <?php
                                                    $info['loopTrack']=1;
                                                    eachRow($child1, $info);
                                                    ?>

                                                    <?php
                                                    if($child1->isGroupHead==1){
                                                        $children2=DB::table('acc_account_ledger')->select('id','name','code','isGroupHead')->where('parentId', $child1->id)->orderBy('ordering', 'asc')->get();
                                                        ?>
                                                        @foreach($children2 as $child2)
                                                            @if(in_array($child2->id,$paymentLegderIds))
                                                                <?php
                                                                $info['loopTrack']=2;
                                                                eachRow($child2, $info);
                                                                ?>

                                                                <?php
                                                                if($child2->isGroupHead==1){
                                                                    $children3=DB::table('acc_account_ledger')->select('id','name','code','isGroupHead')->where('parentId', $child2->id)->orderBy('ordering', 'asc')->get();
                                                                    ?>
                                                                    @foreach($children3 as $child3)
                                                                        @if(in_array($child3->id,$paymentLegderIds))
                                                                            <?php
                                                                            $info['loopTrack']=3;
                                                                            eachRow($child3, $info);
                                                                            ?>

                                                                            <?php
                                                                            if($child3->isGroupHead==1){
                                                                                $children4=DB::table('acc_account_ledger')->select('id','name','code','isGroupHead')->where('parentId', $child3->id)->orderBy('ordering', 'asc')->get();
                                                                                ?>
                                                                                @foreach($children4 as $child4)
                                                                                    @if(in_array($child4->id,$paymentLegderIds))
                                                                                        <?php
                                                                                        $info['loopTrack']=4;
                                                                                        $dataForChild3 = eachRow($child4, $info);
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

                                                <tr id="totalPayment" style="font-weight: bold;">
                                                    @if($searchMethodSelected==1)
                                                        <td colspan="2" style="text-align: left;">Total Payment</td>
                                                        <td class="tPpreviousFY" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                                                        <td class="tPcurrentFY" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>

                                                    @elseif($searchMethodSelected==2)
                                                        <td colspan="2" style="text-align: left;">Total Payment</td>
                                                        <td class="tPthisMonth" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                                                        <td class="tPThisYear" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                                                        <td class="tPCumulative" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                                                    @elseif($searchMethodSelected==3)
                                                        <td colspan="2" style="text-align: left;">Total Payment</td>
                                                        <td class="tPThisPeriod" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>

                                                    @endif

                                                </tr>



                                                {{-- End Payment --}}

                                                @php
                                                printClosingBalance($data);
                                                @endphp

                                                <tr id="grandTotalPayment" style="font-weight: bold; border-top-width: 0px;">
                                                    @if($searchMethodSelected==1)
                                                        <td colspan="2" style="text-align: left;">Total</td>
                                                        <td class="gtPpreviousFY" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                                                        <td class="gtPcurrentFY" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>

                                                    @elseif($searchMethodSelected==2)
                                                        <td colspan="2" style="text-align: left;">Total</td>
                                                        <td class="gtPthisMonth" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                                                        <td class="gtPThisYear" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                                                        <td class="gtPCumulative" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>
                                                    @elseif($searchMethodSelected==3)
                                                        <td colspan="2" style="text-align: left;">Total</td>
                                                        <td class="gtPThisPeriod" amount="0" style="text-align: right;padding: 0 5px 0 10px;"></td>

                                                    @endif
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
    #CRPStable thead tr th{padding: 2px;}
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
         }
     });

    var dateFromData = $("#dateFrom").val();

    /* End Date Range To */




    /*Validation*/
    $("#search").click(function(event) {

    if ($("#dateFrom").val()=="") {
        //changes done here ********************************
        @if ($searchMethodSelected ==2)

        @else
        event.preventDefault();
        $("#dateFrome").show();
        @endif
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

{{-- Filtering Mehod --}}
  <script type="text/javascript">
      $(document).ready(function() {

          $("#searchMethod").change(function(event) {

              var searchMethod = $(this).val();
              if (searchMethod=="") {
                $("#fiscalYearDiv").hide();
                $("#dateRangeDiv").hide();
              }
              //Fiscal Year
              else if(searchMethod==1){
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

                // $("#dateFrom").datepicker("option","minDate",new Date(d));
                // $("#dateTo").datepicker("option","minDate",new Date(d));

                // $("#dateFrom").datepicker("setDate", new Date(d));
                $("#dateFrom").hide();
                $("#dateFrome").hide();

                $("#dateToDiv").attr("class", "col-sm-12");
              }

              //Date Range
              else if(searchMethod==3){
               $("#fiscalYearDiv").hide();
                $("#dateRangeDiv").show();

                 $("#dateToDiv").attr("class", "col-sm-6");
                $("#dateFrom").show();
                //$("#dateFrom").val("");

                $("#dateFrom").datepicker("option","minDate",new Date(Date.parse("2010-07-01")));
              }
          });
          $("#searchMethod").trigger('change');
      });
  </script>
  {{-- End Filtering Mehod --}}




{{-- Print Page --}}
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $("#printIcon").click(function(event) {

            $("#hiddenTitle").show();
            $("#hiddenInfo").show();
            // $("#CRPStable").removeClass('table table-striped table-bordered');

            // var printStyle = '<style>#printingContent{padding-left:10px;} #CRPStable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt solid ash;page-break-inside:auto;font-family: arial!important;} tr:last-child { font-weight: bold;} thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;} tbody tr td { text-align:center;vertical-align: middle;padding:3px ;font-size:10px;} tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{size:portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

            // var mainContents = document.getElementById("printingContent").innerHTML;
            // var headerContents = '';

            // var footerContents = "<div class='row' style='font-size:12px;'>Prepared By <span style='display:inline-block; width: 36%;'></span> Checked By <span style='display:inline-block; width: 36%;'></span> Approved By</div>";

            // printContents = '<div id="order-details-wrapper" style="padding: 10px;">' + headerContents + mainContents + footerContents +'</div>';


             var printStyle = '<style>#CRPStable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt solid ash;page-break-inside:auto;font-family: arial!important;} tr:last-child { font-weight: bold;} thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;} tbody tr td { text-align:center;vertical-align: middle;padding:3px ;font-size:10px;} tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{size:portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';


            var mainContents = document.getElementById("printingContent").innerHTML;

            var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-top:20px;'>Prepared By <span style='display:inline-block; width: 35%;'></span> Checked By <span style='display:inline-block; width: 34%; padding-right:15px;'></span> Approved By</div>";

            var printContents = '<div id="order-details-wrapper" style="padding: 10px;">' +  printStyle + mainContents + footerContents +'</div>';


        // var printStyle = '<style>#CRPStable{float:left;height:auto;padding:0px;width:100% !important;font-size:11px;page-break-inside:auto;}  thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:10px}  tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{size:portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

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

{{-- Calculate Sum --}}
<script type="text/javascript">
    $(document).ready(function() {

        function addComma(value){
            var roundUpSelected = "{{$roundUpSelected}}";
            if (roundUpSelected==1) {
                return value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            else{
                return value.toFixed(2);
            }
        }


        /*Set Opning Balance as a closing balance for fiscal tear*/
        var obCurrentFiscalYear = parseFloat($("#obCurrentFiscalYear").attr('amount'));
        var cashInHandForCurrentFiscalYear = parseFloat($("#cashInHandForCurrentFiscalYear").attr('amount'));
        var cashInBankForCurrentFiscalYear = parseFloat($("#cashInBankForCurrentFiscalYear").attr('amount'));

        $("#cbPreviousFiscalYear").attr('amount',obCurrentFiscalYear);
        $("#cbPreviousFiscalYear").html(addComma(obCurrentFiscalYear));

        $("#cashInHandForPreviousFiscalYear").attr('amount',cashInHandForCurrentFiscalYear);
        $("#cashInHandForPreviousFiscalYear").html(addComma(cashInHandForCurrentFiscalYear));

        $("#cashInBankForPreviousFiscalYear").attr('amount',cashInBankForCurrentFiscalYear);
        $("#cashInBankForPreviousFiscalYear").html(addComma(cashInBankForCurrentFiscalYear));

        ////////////

        function claculateSum(searchById,startRow,endRow,className) {


                var dataLevel4 = 0;
                var dataLevel3 = 0;
                var dataLevel2 = 0;
                var dataLevel1 = 0;
                var dataLevel0 = 0;


                for (i = endRow; i >startRow; i--) {

                    if($("#CRPStable tr").eq(i).attr('level')==4){
                        dataLevel4 = dataLevel4 + parseFloat($("#CRPStable tr").eq(i).find('.'+className).attr('amount'));

                    }
                    else if ($("#CRPStable tr").eq(i).attr('level')==3) {
                        $("#CRPStable tr").eq(i).closest('tr').find('.'+className).html(addComma(dataLevel4));
                        $("#CRPStable tr").eq(i).closest('tr').find('.'+className).attr('amount',dataLevel4);
                        dataLevel3 = dataLevel3 + dataLevel4;
                        dataLevel4 = 0;
                    }
                    else if ($("#CRPStable tr").eq(i).attr('level')==2) {
                        $("#CRPStable tr").eq(i).closest('tr').find('.'+className).html(addComma(dataLevel3));
                        $("#CRPStable tr").eq(i).closest('tr').find('.'+className).attr('amount',dataLevel3);
                        dataLevel2 = dataLevel2 + dataLevel3;
                        dataLevel3 = 0;
                    }
                    else if ($("#CRPStable tr").eq(i).attr('level')==1) {
                        $("#CRPStable tr").eq(i).closest('tr').find('.'+className).html(addComma(dataLevel2));
                        $("#CRPStable tr").eq(i).closest('tr').find('.'+className).attr('amount',dataLevel2);
                        dataLevel1 = dataLevel1 + dataLevel2;
                        dataLevel2 = 0;
                    }
                    else if ($("#CRPStable tr").eq(i).attr('level')==0) {
                        $("#CRPStable tr").eq(i).closest('tr').find('.'+className).html(addComma(dataLevel1));
                        $("#CRPStable tr").eq(i).closest('tr').find('.'+className).attr('amount',dataLevel1);
                        dataLevel0 = dataLevel0 + dataLevel1;
                        dataLevel1 = 0;
                    }


                }


            return dataLevel0;



        }

        var startRow = $("#receiptBegin").index();
        var endRow = $("#totalReceipt").index();

        var searchMethodSelected = "{{$searchMethodSelected}}";

        if (searchMethodSelected==1) {
            var previousFiscalYearData = claculateSum(searchMethodSelected,startRow,endRow,'previousFiscalYear');
            $("#totalReceipt").find('.tRpreviousFY').html(addComma(previousFiscalYearData));
            $("#totalReceipt").find('.tRpreviousFY').attr('amount',previousFiscalYearData);

            var currentFiscalYearData = claculateSum(searchMethodSelected,startRow,endRow,'currentFiscalYear');
            $("#totalReceipt").find('.tRcurrentFY').html(addComma(currentFiscalYearData));
            $("#totalReceipt").find('.tRcurrentFY').attr('amount',currentFiscalYearData);


             /*Calculate Grand Total for Receipt*/
            var obPreviousFiscalYear = parseFloat($("#obPreviousFiscalYear").attr('amount'));
            var obCurrentFiscalYear = parseFloat($("#obCurrentFiscalYear").attr('amount'));
            var totalReceiptPreviousFiscalYear = parseFloat($("#totalReceipt").find('.tRpreviousFY').attr('amount'));
            var totalReceiptCurrentFiscalYear = parseFloat($("#totalReceipt").find('.tRcurrentFY').attr('amount'));

            $("#grandTotalReceipt").find('.gtRpreviousFY').html(addComma(obPreviousFiscalYear+totalReceiptPreviousFiscalYear));
            $("#grandTotalReceipt").find('.gtRpreviousFY').attr('amount',obPreviousFiscalYear+totalReceiptPreviousFiscalYear);

             $("#grandTotalReceipt").find('.gtRcurrentFY').html(addComma(obCurrentFiscalYear+totalReceiptCurrentFiscalYear));
            $("#grandTotalReceipt").find('.gtRcurrentFY').attr('amount',obCurrentFiscalYear+totalReceiptCurrentFiscalYear);
            /*End Calculate Grand Total for Receipt*/

        }
        else if (searchMethodSelected==2) {
            var thisMonth = claculateSum(searchMethodSelected,startRow,endRow,'thisMonth');
            $("#totalReceipt").find('.tRthisMonth').html(addComma(thisMonth));
            $("#totalReceipt").find('.tRthisMonth').attr('amount',thisMonth);

            var thisYear = claculateSum(searchMethodSelected,startRow,endRow,'thisYear');
            $("#totalReceipt").find('.tRThisYear').html(addComma(thisYear));
            $("#totalReceipt").find('.tRThisYear').attr('amount',thisYear);

            var cumulative = claculateSum(searchMethodSelected,startRow,endRow,'cumulative');
            $("#totalReceipt").find('.tRCumulative').html(addComma(cumulative));
            $("#totalReceipt").find('.tRCumulative').attr('amount',cumulative);

             /*Calculate Grand Total for Receipt*/
            var obThisMonth = parseFloat($("#obThisMonth").attr('amount'));
            var obThisYear = parseFloat($("#obThisYear").attr('amount'));
            var totalReceiptThisMonth = parseFloat($("#totalReceipt").find('.tRthisMonth').attr('amount'));
            var totalReceiptThisYear = parseFloat($("#totalReceipt").find('.tRThisYear').attr('amount'));
            var totalReceiptCumulative = parseFloat($("#totalReceipt").find('.tRCumulative').attr('amount'));

            $("#grandTotalReceipt").find('.gtRthisMonth').html(addComma(obThisMonth+totalReceiptThisMonth));
            $("#grandTotalReceipt").find('.gtRthisMonth').attr('amount',obThisMonth+totalReceiptThisMonth);

             $("#grandTotalReceipt").find('.gtRThisYear').html(addComma(obThisYear+totalReceiptThisYear));
            $("#grandTotalReceipt").find('.gtRThisYear').attr('amount',obThisYear+totalReceiptThisYear);

            $("#grandTotalReceipt").find('.gtRCumulative').html(addComma(totalReceiptCumulative));
            $("#grandTotalReceipt").find('.gtRCumulative').attr('amount',totalReceiptCumulative);
            /*End Calculate Grand Total for Receipt*/


        }
        else if (searchMethodSelected==3) {
            var currentPeriod = claculateSum(searchMethodSelected,startRow,endRow,'currentPeriod');
            $("#totalReceipt").find('.tRThisPeriod').html(addComma(currentPeriod));
            $("#totalReceipt").find('.tRThisPeriod').attr('amount',currentPeriod);

            /*Calculate Grand Total for Receipt*/
            var obThisPeriod = parseFloat($("#obThisPeriod").attr('amount'));
            var totalReceiptThisPeriod = parseFloat($("#totalReceipt").find('.tRThisPeriod').attr('amount'));

            $("#grandTotalReceipt").find('.gtRThisPeriod').html(addComma(obThisPeriod+totalReceiptThisPeriod));
            $("#grandTotalReceipt").find('.gtRThisPeriod').attr('amount',obThisPeriod+totalReceiptThisPeriod);

            /*End Calculate Grand Total for Receipt*/

        }









        /*Payment*/

        var startRow = $("#startPayment").index();
        var endRow = $("#totalPayment").index();


       if (searchMethodSelected==1) {
            var previousFiscalYearData = claculateSum(searchMethodSelected,startRow,endRow,'previousFiscalYear');
            $("#totalPayment").find('.tPpreviousFY').html(addComma(previousFiscalYearData));
            $("#totalPayment").find('.tPpreviousFY').attr('amount',previousFiscalYearData);

            var currentFiscalYearData = claculateSum(searchMethodSelected,startRow,endRow,'currentFiscalYear');
            $("#totalPayment").find('.tPcurrentFY').html(addComma(currentFiscalYearData));
            $("#totalPayment").find('.tPcurrentFY').attr('amount',currentFiscalYearData);

            /*Calculate Grand Total for Payment*/
            var cbPreviousFiscalYear = parseFloat($("#cbPreviousFiscalYear").attr('amount'));
            var cbCurrentFiscalYear = parseFloat($("#cbCurrentFiscalYear").attr('amount'));
            var totalPaymentPreviousFiscalYear = parseFloat($("#totalPayment").find('.tPpreviousFY').attr('amount'));
            var totalPaymentCurrentFiscalYear = parseFloat($("#totalPayment").find('.tPcurrentFY').attr('amount'));

            $("#grandTotalPayment").find('.gtPpreviousFY').html(addComma(cbPreviousFiscalYear+totalPaymentPreviousFiscalYear));
            $("#grandTotalPayment").find('.gtPpreviousFY').attr('amount',cbPreviousFiscalYear+totalPaymentPreviousFiscalYear);

             $("#grandTotalPayment").find('.gtPcurrentFY').html(addComma(cbCurrentFiscalYear+totalPaymentCurrentFiscalYear));
            $("#grandTotalPayment").find('.gtPcurrentFY').attr('amount',cbCurrentFiscalYear+totalPaymentCurrentFiscalYear);
            /*End Calculate Grand Total for Payment*/
        }
        else if (searchMethodSelected==2) {
            var thisMonth = claculateSum(searchMethodSelected,startRow,endRow,'thisMonth');
            $("#totalPayment").find('.tPthisMonth').html(addComma(thisMonth));
            $("#totalPayment").find('.tPthisMonth').attr('amount',thisMonth);

            var thisYear = claculateSum(searchMethodSelected,startRow,endRow,'thisYear');
            $("#totalPayment").find('.tPThisYear').html(addComma(thisYear));
            $("#totalPayment").find('.tPThisYear').attr('amount',thisYear);

            var cumulative = claculateSum(searchMethodSelected,startRow,endRow,'cumulative');
            $("#totalPayment").find('.tPCumulative').html(addComma(cumulative));
            $("#totalPayment").find('.tPCumulative').attr('amount',cumulative);

            /*Calculate Grand Total for Payment*/
            var cbThisMonth = parseFloat($("#cbThisMonth").attr('amount'));
            var cbThisYear = parseFloat($("#cbThisYear").attr('amount'));
            var totalPaymentThisMonth = parseFloat($("#totalPayment").find('.tPthisMonth').attr('amount'));
            var totalPaymentThisYear = parseFloat($("#totalPayment").find('.tPThisYear').attr('amount'));
            var totalPaymentCumulative = parseFloat($("#totalPayment").find('.tPCumulative').attr('amount'));

            var totalClosingCumulative =   parseFloat($("#cbCumulative").attr('amount'));

            $("#grandTotalPayment").find('.gtPthisMonth').html(addComma(cbThisMonth+totalPaymentThisMonth));
            $("#grandTotalPayment").find('.gtPthisMonth').attr('amount',cbThisMonth+totalPaymentThisMonth);

             $("#grandTotalPayment").find('.gtPThisYear').html(addComma(cbThisYear+totalPaymentThisYear));
            $("#grandTotalPayment").find('.gtPThisYear').attr('amount',cbThisYear+totalPaymentThisYear);

            $("#grandTotalPayment").find('.gtPCumulative').html(addComma(totalPaymentCumulative+totalClosingCumulative));
            $("#grandTotalPayment").find('.gtPCumulative').attr('amount',totalPaymentCumulative+totalClosingCumulative);
            /*End Calculate Grand Total for Payment*/
        }
        else if (searchMethodSelected==3) {
            var currentPeriod = claculateSum(searchMethodSelected,startRow,endRow,'currentPeriod');
            $("#totalPayment").find('.tPThisPeriod').html(addComma(currentPeriod));
            $("#totalPayment").find('.tPThisPeriod').attr('amount',currentPeriod);

            /*Calculate Grand Total for Payment*/
            var cbThisPeriod = parseFloat($("#cbThisPeriod").attr('amount'));
            var totalPaymentThisPeriod = parseFloat($("#totalPayment").find('.tPThisPeriod').attr('amount'));


            $("#grandTotalPayment").find('.gtPThisPeriod').html(addComma(cbThisPeriod+totalPaymentThisPeriod));
            $("#grandTotalPayment").find('.gtPThisPeriod').attr('amount',cbThisPeriod+totalPaymentThisPeriod);

            /*End Calculate Grand Total for Payment*/
        }


        /*Depth Level and Show Zero*/
        var withZero = "{{$withZeroSelected}}";
        var depthLevel = "{{$depthLevel}}";

        var rows = $('#CRPStable tr').length;

        if(withZero==1){

            for(i=rows-2 ;i>=1;i--){

            if (searchMethodSelected==1) {
            var td1 = parseFloat($("#CRPStable tr").eq(i).closest('tr').find('.previousFiscalYear').attr('amount'));
            var td2 = parseFloat($("#CRPStable tr").eq(i).closest('tr').find('.currentFiscalYear').attr('amount'));
             if (td1==0 && td2==0) {
                $("#CRPStable tr").eq(i).hide();
             }

            }

            else if (searchMethodSelected==2) {
            var td1 = parseFloat($("#CRPStable tr").eq(i).closest('tr').find('.thisMonth').attr('amount'));
            var td2 = parseFloat($("#CRPStable tr").eq(i).closest('tr').find('.thisYear').attr('amount'));
            var td3 = parseFloat($("#CRPStable tr").eq(i).closest('tr').find('.cumulative').attr('amount'));
             if (td1==0 && td2==0 && td3==0) {
                $("#CRPStable tr").eq(i).hide();
             }

            }

            else if (searchMethodSelected==3) {
            var td1 = parseFloat($("#CRPStable tr").eq(i).closest('tr').find('.currentPeriod').attr('amount'));

             if (td1==0) {
                $("#CRPStable tr").eq(i).hide();
             }

            }




        }

        }

        for(i=rows-2 ;i>=1;i--){

        if ($("#CRPStable tr").eq(i).attr('level')>=depthLevel) {
                $("#CRPStable tr").eq(i).hide();
            }
        }


        /*End Depth Level and Show Zero*/




    });
</script>
{{-- End Calculate Sum --}}


@endsection
