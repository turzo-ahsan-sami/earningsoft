<?php

namespace App\Http\Controllers\accounting\reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\accounting\AddVoucher;
use App\accounting\AddVoucherDetails;
use App\accounting\AddLedger;
use App\accounting\AddVoucherType;
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;
use App\gnr\GnrBranch;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use App\Service\Service;

class AccReceiptPaymentStatementController extends Controller {

    public function index(Request $request) {

        $user_branch_id = Auth::user()->branchId;
        $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');
        $user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectTypeId');
        $user_company_id = Auth::user()->company_id_fk;

        $projectId     = array();
        $projectTypeId = array();
        $branchId      = array();
        $categoryId    = array();
        $productTypeId = array();
        $voucherTypeId = array();

        //Project
        if ($request->searchProject==null) {

            if ($user_branch_id == 1) {
                $projectSelected = 1;
                $projectId = DB::table('gnr_project')->pluck('id');
            }
            else{
                $projectSelected = $user_project_id;
                array_push($projectId, $projectSelected);
            }

        }
        else{
            $projectSelected = (int)json_decode($request->searchProject);
            array_push($projectId, $projectSelected);
        }
        // dd($projectSelected);

        //Project Type
        if ($request->searchProjectType==null) {

            if ($user_branch_id == 1) {
                $projectTypeSelected = 0;
                $projectTypeId = DB::table('gnr_project_type')->pluck('id');
            }
            else{
                $projectTypeSelected = $user_project_type_id;
                array_push($projectTypeId, $projectTypeSelected);
            }
        }
        else{
            $projectTypeSelected = (int) json_decode($request->searchProjectType);
            array_push($projectTypeId, $projectTypeSelected);
        }

        // Branch
        if ($request->searchBranch==null) {

            if ($user_branch_id == 1) {
                $branchSelected = null;
                $branchId = DB::table('gnr_branch')->pluck('id')->toArray();
            }
            else{
                $branchSelected = $user_branch_id;
                array_push($branchId, $branchSelected);
            }

        }
        else{
            $branchSelected = (int) json_decode($request->searchBranch);

            if ($request->searchBranch==0) {
                $branchId = DB::table('gnr_branch')->where('id','!=',1)->pluck('id')->toArray();
            }
            else{
                array_push($branchId, $branchSelected);
            }

        }

        //Search Method
        if ($request->searchMethod==null) {
            $searchMethodSelected = 1;
        }
        else{
            $searchMethodSelected = (int) json_decode($request->searchMethod);
        }

        //Fiscal Year
        if($request->fiscalYear==""){
            $today = Carbon::today()->toDateTimeString();
            $fiscalYearSelected = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$today)->where('fyEndDate','>=',$today)->value('id');
            $startDate =  DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyStartDate');
            $endDate = DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyEndDate');
        }
        else{
            $fiscalYearSelected = (int) json_decode($request->fiscalYear);
            $startDate =  DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyStartDate');
            $endDate = DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyEndDate');
        }


        //Date From
        if ($request->dateFrom==null) {
            $dateFromSelected = null;
            $startDate = null;
            if ($searchMethodSelected==1) {
                $startDate = DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyStartDate');
                $dateFromSelected = $startDate;
            }
        }
        else{
            $dateFromSelected = $request->dateFrom;
            $startDate = date('Y-m-d', strtotime($request->dateFrom));
        }

        // *********** extra line
        // if($searchMethodSelected==2){
        //     $startDate = '2017-07-01';
        //     $dateFromSelected = $startDate;
        // }
        // dd($startDate);
        // *********** extra line

        //Date To
        if ($request->dateTo==null) {
            $dateToSelected = null;
            $endDate = null;
            if ($searchMethodSelected==1) {
                $endDate = DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyEndDate');
                $dateToSelected = $endDate;
            }
        }
        else{
            $dateToSelected = $request->dateTo;
            $endDate = date('Y-m-d', strtotime($request->dateTo));
        }

        $projects = DB::table('gnr_project')->get();
        $projectTypes = DB::table('gnr_project_type')->whereIn('projectId',$projectId)->get();
        $branches = DB::table('gnr_branch')->whereIn('projectId',$projectId)->orWhere('id',1)->get();
        $fiscalYears = DB::table('gnr_fiscal_year')->orderBy('fyStartDate','desc')->pluck('name','id');

        if($branchSelected===0) {
            $newBranches = DB::table('gnr_branch')->whereIn('projectId',$projectId)->where('id','!=',1)->pluck('id')->toArray();
        }
        elseif($branchSelected==null){
            $newBranches = DB::table('gnr_branch')->whereIn('projectId',$projectId)->orWhere('id',1)->pluck('id')->toArray();
            //$newBranches = DB::table('gnr_branch')->where('id',1)->pluck('id')->toArray();
        }
        else{
            $newBranches = DB::table('gnr_branch')->where('id',$branchSelected)->pluck('id')->toArray();
        }

        //Is it the First Request    /// Round Up
        if ($request->roundUp==null) {
            $firstRequest = 1;
            $roundUpSelected = 1;
        }
        else{
            $firstRequest = 0;
            $roundUpSelected = $request->roundUp;
        }


        //Depth Level
        if ($request->depthLevel==null) {
            $depthLevelSelected = null;
            $depthLevel = 5;
        }
        else{
            $depthLevelSelected = $request->depthLevel;
            $depthLevel = $request->depthLevel;
        }

        //With or Without zeros
        if ($request->withZero==null) {
            $withZeroSelected = null;
        }
        else{
            $withZeroSelected = $request->withZero;
        }

        // Voucher Type
        // Without JV
        if ($request->voucherType==null) {
            $voucherTypeSelected = null;
            $voucherTypeId = [1,2,4,5];
        }
        // WIth JV
        elseif($request->voucherType==1){
            $voucherTypeSelected = $request->voucherType;
            $voucherTypeId = [1,2,3,4,5];
        }
        // Only JV
        elseif($request->voucherType==2){
            $voucherTypeSelected = $request->voucherType;
            $voucherTypeId = [3,5];
        }

        // date collection
        if ($searchMethodSelected==1) {
            $startDate = DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyStartDate');
            $endDate = DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyEndDate');
        }

        // fiscacl year dates
        //current
        $currentFiscalYearStartDate = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('fyStartDate');
        $currentFiscalYearEndDate = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('fyEndDate');
        // previous
        $dateToCompare = date('Y-m-d', strtotime('-1 day', strtotime($currentFiscalYearStartDate)));
        $previousfiscalYearId = DB::table('gnr_fiscal_year')->where('fyEndDate',$dateToCompare)->value('id');

        if ($previousfiscalYearId!=null) {
            $previousfiscalYearStartDate = DB::table('gnr_fiscal_year')->where('id',$previousfiscalYearId)->value('fyStartDate');
            $previousfiscalYearEndDate = DB::table('gnr_fiscal_year')->where('id',$previousfiscalYearId)->value('fyEndDate');
        }

        // other dates
        if (!is_null($startDate) && !is_null($endDate)) {
            $thisMonthStartDate = date('Y-m-01', strtotime($endDate));
            $thisperoidStartDate = DB::table('gnr_fiscal_year')->where('id',$previousfiscalYearId)->value('fyEndDate');
            $fiscalYearId = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('id');
            $fiscalEndDate = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('fyEndDate');
            $thisFiscalYearStartDate = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('fyStartDate');
        }
        // dd($endDate);

        ////////// start processing //////////
        if(isset($request->searchMethod)){

            $allLedgers = DB::table('acc_account_ledger')->select("id","projectBranchId")->get();
            $firstLedgerMatchedId=array();

            foreach ($allLedgers as $singleLedger) {
                $splitArray=str_replace(array('[', ']', '"', ''), '',  $singleLedger->projectBranchId);

                $array_length=substr_count($splitArray, ",");
                $arrayProjects=array();

                for($i=0; $i<$array_length+1; $i++){

                    $splitArrayFirstValue = explode(",", $splitArray);

                    $splitArraySecondValue = explode(":", $splitArrayFirstValue[$i]);
                    $firstIndexValue=(int)$splitArraySecondValue[0];
                    $secondIndexValue=(int)$splitArraySecondValue[1];

                    if($firstIndexValue==0){

                        if($secondIndexValue==0){
                            array_push($firstLedgerMatchedId, $singleLedger->id);
                        }
                    }else{

                        if($firstIndexValue==$projectSelected){
                            if ($secondIndexValue==0) {
                                array_push($firstLedgerMatchedId, $singleLedger->id);
                            }
                            if ($branchSelected!=null || $branchSelected!=0) {
                                if ($secondIndexValue==$branchSelected) {
                                    array_push($firstLedgerMatchedId, $singleLedger->id);
                                }
                            }
                            else{
                                foreach ($newBranches as $selectedBranch) {
                                    if ($secondIndexValue==$selectedBranch) {
                                        array_push($firstLedgerMatchedId, $singleLedger->id);
                                    }
                                }
                            }

                        }


                    }
                }   //for
            }       //foreach
            ///////////////////////

            if (sizeof($firstLedgerMatchedId)<=0) {
                array_push($firstLedgerMatchedId, 0);
            }

            // dd($branchId);
            $service = new Service;
            $ledgersCollection = $service->getLedgerHeaderInfos($projectSelected, $branchId, $user_company_id);

            //Get the ledgers which are in selected project and branch and types of Cash or Bank
            $secondLedgerMatchedId = DB::table('acc_account_ledger')->whereIn('id',$firstLedgerMatchedId)->whereIn('accountTypeId',[4,5])->pluck('id')->toArray();
            // dd($secondLedgerMatchedId);

            $ledgers = DB::table('acc_account_ledger')->where('parentId',0)->orderBy('ordering', 'asc')->get();
            $finalLevelLedgers  = $ledgersCollection->where('level', 5);
            //Cash Type Ledgers
            $cashTypeLedgers = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead', 0)->pluck('id')->toArray();
            //Bank Type Ledgers
            $bankTypeLedgers = DB::table('acc_account_ledger')->where('accountTypeId',5)->where('isGroupHead', 0)->pluck('id')->toArray();

            /// for fund transfers get the vouchers
            $bankNCashLedgerIds = DB::table('acc_account_ledger')
            ->where(function ($query) {
                $query->where('accountTypeId',4)
                ->orWhere('accountTypeId',5);
            })
            ->where('isGroupHead',0)
            ->pluck('id')
            ->toArray();
            // dd($bankNCashLedgerIds);

            $recpeitLegderIds = DB::table('acc_account_ledger')->whereNotIn('accountTypeId',[13,4,5])->whereNotIn('id',[285,348])->pluck('id')->toArray();
            $paymentLegderIds = DB::table('acc_account_ledger')->whereNotIn('accountTypeId',[12,4,5])->whereNotIn('id',[285,348])->pluck('id')->toArray();
            $finalLevelReceiptLedgers = $finalLevelLedgers->whereIn('id', $recpeitLegderIds);
            $finalLevelPaymentLedgers = $finalLevelLedgers->whereIn('id', $paymentLegderIds);
            $finalLevelReceiptPaymentLedgers = $finalLevelReceiptLedgers->merge($finalLevelPaymentLedgers)->unique('id')->sortBy('id');
            // dd($finalLevelReceiptPaymentLedgers);

            $dateRangeInfo      = collect();
            $thisMonthInfo      = collect();
            $thisPeriodInfo     = collect();
            $thisYearInfo       = collect();
            $previousYearsInfo  = collect();
            // for all branches data
            if ($branchSelected == null || $branchSelected == 0) {
                // dd(1);

                $debitLedgerMatchedId = [];
                $creditLedgerMatchedId = [];
                $vouchers = [];
                $openingVouchers = [];
                $previousfiscalYearVouchers = [];
                $thisPeriodVouchers = [];
                $thisMonthVoucherIds = [];
                $thisPeriodVoucherIds = [];

                // dd($branchId);
                $allMonthEndsInfo = DB::table('acc_month_end_balance')
                                ->where('projectId', $projectId)
                                ->whereIn('projectTypeId', $projectTypeId)
                                ->whereIn('branchId', $branchId)
                                ->select(
                                    'monthEndDate', 'fiscalYearId', 'ledgerId',
                                    'debitAmount', 'creditAmount',
                                    'cashDebit', 'cashCredit',
                                    'bankDebit', 'bankCredit',
                                    'jvDebit', 'jvCredit'
                                    // 'ftDebit', 'ftCredit'
                                    )
                                ->get();

                $monthEndsInfo = $allMonthEndsInfo->whereIn('fiscalYearId', [$fiscalYearId, $previousfiscalYearId]);
                // dd($allMonthEndsInfo);

                if ($searchMethodSelected == 1) {
                    $thisYearInfo = $monthEndsInfo->where('monthEndDate', '>=', $startDate)->where('monthEndDate', '<=', $endDate);
                    $previousYearsInfo = $monthEndsInfo->where('monthEndDate', '<=', $previousfiscalYearEndDate)->where('monthEndDate', '>=', $previousfiscalYearStartDate);
                }
                elseif ($searchMethodSelected == 2) {
                    $thisMonthInfo = $monthEndsInfo->where('monthEndDate', '>=', $thisMonthStartDate)->where('monthEndDate', '<=', $endDate);
                    $thisPeriodInfo = $monthEndsInfo->where('monthEndDate', '>', $thisperoidStartDate)->where('monthEndDate', '<', $thisMonthStartDate);
                    $thisYearInfo = $monthEndsInfo->where('monthEndDate', '>=', $currentFiscalYearStartDate)->where('monthEndDate', '<=', $endDate);
                    $previousYearsInfo = $allMonthEndsInfo->where('monthEndDate', '<=', $previousfiscalYearEndDate);
                }
                elseif ($searchMethodSelected == 3) {
                    $thisPeriodInfo = $monthEndsInfo->where('monthEndDate', '>', $thisperoidStartDate)->where('monthEndDate', '<', $startDate);
                    $dateRangeInfo = $monthEndsInfo->where('monthEndDate', '>=', $startDate)->where('monthEndDate', '<=', $endDate);
                }
                // dd($previousYearsInfo);

                $thisMonthTransactionOfCashTypeDebit = 0;
                $thisMonthTransactionOfCashTypeCredit = 0;
                $thisPeriodTransactionOfCashTypeDebit = 0;
                $thisPeriodTransactionOfCashTypeCredit = 0;
                $thisYearTransactionOfCashTypeDebit = 0;
                $thisYearTransactionOfCashTypeCredit = 0;
                $previousYearTransactionOfCashTypeDebit = 0;
                $previousYearTransactionOfCashTypeCredit = 0;
                $dateRangeTransactionOfCashTypeDebit = 0;
                $dateRangeTransactionOfCashTypeCredit = 0;
                $thisMonthTransactionOfBankTypeDebit = 0;
                $thisMonthTransactionOfBankTypeCredit = 0;
                $thisPeriodTransactionOfBankTypeDebit = 0;
                $thisPeriodTransactionOfBankTypeCredit = 0;
                $thisYearTransactionOfBankTypeDebit = 0;
                $thisYearTransactionOfBankTypeCredit = 0;
                $previousYearTransactionOfBankTypeDebit = 0;
                $previousYearTransactionOfBankTypeCredit = 0;
                $dateRangeTransactionOfBankTypeDebit = 0;
                $dateRangeTransactionOfBankTypeCredit = 0;

                foreach ($cashTypeLedgers as $key => $ledger) {

                    if ($searchMethodSelected == 1) {
                        // previous year
                        $previousYearTransactionOfCashTypeDebit += $previousYearsInfo->where('ledgerId', $ledger)->sum('debitAmount');
                        $previousYearTransactionOfCashTypeCredit += $previousYearsInfo->where('ledgerId', $ledger)->sum('creditAmount');
                        // this year
                        $thisYearTransactionOfCashTypeDebit += $thisYearInfo->where('ledgerId', $ledger)->sum('debitAmount');
                        $thisYearTransactionOfCashTypeCredit += $thisYearInfo->where('ledgerId', $ledger)->sum('creditAmount');
                    }
                    elseif ($searchMethodSelected == 2) {
                        //this month
                        $thisMonthTransactionOfCashTypeDebit += $thisMonthInfo->where('ledgerId', $ledger)->sum('debitAmount');
                        $thisMonthTransactionOfCashTypeCredit += $thisMonthInfo->where('ledgerId', $ledger)->sum('creditAmount');
                        // this period
                        $thisPeriodTransactionOfCashTypeDebit += $thisPeriodInfo->where('ledgerId', $ledger)->sum('debitAmount');
                        $thisPeriodTransactionOfCashTypeCredit += $thisPeriodInfo->where('ledgerId', $ledger)->sum('creditAmount');
                        // this year
                        $thisYearTransactionOfCashTypeDebit += $thisYearInfo->where('ledgerId', $ledger)->sum('debitAmount');
                        $thisYearTransactionOfCashTypeCredit += $thisYearInfo->where('ledgerId', $ledger)->sum('creditAmount');
                    }
                    elseif ($searchMethodSelected == 3) {
                        // this period
                        $thisPeriodTransactionOfCashTypeDebit += $thisPeriodInfo->where('ledgerId', $ledger)->sum('debitAmount');
                        $thisPeriodTransactionOfCashTypeCredit += $thisPeriodInfo->where('ledgerId', $ledger)->sum('creditAmount');
                        // date range
                        $dateRangeTransactionOfCashTypeDebit += $dateRangeInfo->where('ledgerId', $ledger)->sum('debitAmount');
                        $dateRangeTransactionOfCashTypeCredit += $dateRangeInfo->where('ledgerId', $ledger)->sum('creditAmount');
                    }

                }

                foreach ($bankTypeLedgers as $key => $ledger) {

                    if ($searchMethodSelected == 1) {
                        // previous year
                        $previousYearTransactionOfBankTypeDebit += $previousYearsInfo->where('ledgerId', $ledger)->sum('debitAmount');
                        $previousYearTransactionOfBankTypeCredit += $previousYearsInfo->where('ledgerId', $ledger)->sum('creditAmount');
                        // this year
                        $thisYearTransactionOfBankTypeDebit += $thisYearInfo->where('ledgerId', $ledger)->sum('debitAmount');
                        $thisYearTransactionOfBankTypeCredit += $thisYearInfo->where('ledgerId', $ledger)->sum('creditAmount');
                    }
                    elseif ($searchMethodSelected == 2) {
                        // this month
                        $thisMonthTransactionOfBankTypeDebit += $thisMonthInfo->where('ledgerId', $ledger)->sum('debitAmount');
                        $thisMonthTransactionOfBankTypeCredit += $thisMonthInfo->where('ledgerId', $ledger)->sum('creditAmount');
                        // this period
                        $thisPeriodTransactionOfBankTypeDebit += $thisPeriodInfo->where('ledgerId', $ledger)->sum('debitAmount');
                        $thisPeriodTransactionOfBankTypeCredit += $thisPeriodInfo->where('ledgerId', $ledger)->sum('creditAmount');
                        // this year
                        $thisYearTransactionOfBankTypeDebit += $thisYearInfo->where('ledgerId', $ledger)->sum('debitAmount');
                        $thisYearTransactionOfBankTypeCredit += $thisYearInfo->where('ledgerId', $ledger)->sum('creditAmount');
                    }
                    elseif ($searchMethodSelected == 3) {
                        // this period
                        $thisPeriodTransactionOfBankTypeDebit += $thisPeriodInfo->where('ledgerId', $ledger)->sum('debitAmount');
                        $thisPeriodTransactionOfBankTypeCredit += $thisPeriodInfo->where('ledgerId', $ledger)->sum('creditAmount');
                        // date range
                        $dateRangeTransactionOfBankTypeDebit += $dateRangeInfo->where('ledgerId', $ledger)->sum('debitAmount');
                        $dateRangeTransactionOfBankTypeCredit += $dateRangeInfo->where('ledgerId', $ledger)->sum('creditAmount');
                    }

                }
                // dd($thisYearTransactionOfBankTypeDebit);

            }
            else {
                // dd(2);
                $fundTvoucherIds = DB::table('acc_voucher')->where('voucherTypeId',5)->pluck('id')->toArray();

                // if (count($fundTvoucherIds) > 20000) {
                    $fundTvoucherIdsArr = array_chunk($fundTvoucherIds, 20000);
                // }
                // dd($fundTvoucherIds);
                foreach ($fundTvoucherIdsArr as $key => $fundTvoucherIds) {
                    // Without JV
                    if ($request->voucherType==null) {
                        $excludeVoucherIdsArr = DB::table('acc_voucher_details')
                        ->whereIn('voucherId',$fundTvoucherIds)
                        ->whereNotIn('debitAcc',$bankNCashLedgerIds)
                        ->whereNotIn('creditAcc',$bankNCashLedgerIds)
                        ->pluck('voucherId')
                        ->toArray();
                    }
                    // With JV
                    elseif ($request->voucherType==1) {
                        $excludeVoucherIdsArr = [];
                    }
                    // Only JV
                    else{
                        $excludeVoucherIdsArr = DB::table('acc_voucher_details')
                        ->whereIn('voucherId',$fundTvoucherIds)
                        ->where(function($query) use ($bankNCashLedgerIds){
                            $query->whereIn('debitAcc',$bankNCashLedgerIds)
                            ->orWhereIn('creditAcc',$bankNCashLedgerIds);
                        })
                        ->pluck('voucherId')
                        ->toArray();
                    }

                    foreach ($excludeVoucherIdsArr as $key => $id) {
                        $excludeVoucherIds[] = $id;
                    }
                }
                // dd($excludeVoucherIds);

                ////// Get Vouchers
                if (!is_null($startDate) && !is_null($endDate)) {

                    $vouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->where('voucherDate','>=',$startDate)->where('voucherDate','<=',$endDate)->whereNotIn('id',$excludeVoucherIds)->pluck('id')->toArray();


                    $thisMonthVoucherIds = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->where('voucherDate','>=',$thisMonthStartDate)->where('voucherDate','<=',$endDate)->whereNotIn('id',$excludeVoucherIds)->pluck('id')->toArray();
                }
                else{
                    $vouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->whereNotIn('id',$excludeVoucherIds)->pluck('id')->toArray();
                    $thisMonthVoucherIds = [0];
                }

                if ($previousfiscalYearId!=null) {
                    $previousfiscalYearVouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->where('voucherDate','>=',$previousfiscalYearStartDate)->where('voucherDate','<=',$previousfiscalYearEndDate)->whereNotIn('id',$excludeVoucherIds)->pluck('id')->toArray();
                }
                else{
                    $previousfiscalYearVouchers = [0];
                }



                if (!is_null($startDate) && !is_null($endDate)) {
                    $fiscalYearId = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('id');
                    $fiscalEndDate = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('fyEndDate');
                }
                else{
                    $fiscalYearId=1;
                    $fiscalEndDate="2017-06-30";
                }

                if (!is_null($startDate) && !is_null($endDate)) {
                    $openingVouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->where('voucherDate','<',$startDate)->where('voucherDate','>',$fiscalEndDate)->whereNotIn('id',$excludeVoucherIds)->pluck('id')->toArray();
                }
                else{
                    $openingVouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->whereNotIn('id',$excludeVoucherIds)->pluck('id')->toArray();
                }
                ////// End Get Vouchers


                $jvouchers = DB::table('acc_voucher')->whereIn('id',$vouchers)->where('voucherTypeId',3)->whereNotIn('id',$excludeVoucherIds)->pluck('id')->toArray();



                //Get the legders which are in Receipt
                /* $tempRecpeitLegderIds = array();
                $tempReceiptCreditLedgerIds = DB::table('acc_voucher_details')->whereNotIn('voucherId',$jvouchers)->whereIn('debitAcc',$secondLedgerMatchedId)->pluck('creditAcc')->toArray() + DB::table('acc_voucher_details')->whereIn('voucherId',$jvouchers)->whereIn('debitAcc',$secondLedgerMatchedId)->pluck('debitAcc')->toArray();
                $receiptCreditLedgerIds = array_unique($tempReceiptCreditLedgerIds);
                foreach ($receiptCreditLedgerIds as $receiptCreditLedgerId) {
                array_push($tempRecpeitLegderIds, $receiptCreditLedgerId);//5

                $firstLevelParent = DB::table('acc_account_ledger')->where('id',$receiptCreditLedgerId)->select("id","parentId")->first();
                //array_push($tempRecpeitLegderIds, $firstLevelParent->parentId);

                $secondLevelParent = DB::table('acc_account_ledger')->where('id',$firstLevelParent->parentId)->select("id","parentId")->first();
                array_push($tempRecpeitLegderIds, $secondLevelParent->id);//4

                $thirdLevelParent = DB::table('acc_account_ledger')->where('id',$secondLevelParent->parentId)->select("id","parentId")->first();
                array_push($tempRecpeitLegderIds, $thirdLevelParent->id);//3

                $fourthLevelParent = DB::table('acc_account_ledger')->where('id',$thirdLevelParent->parentId)->select("id","parentId")->first();
                array_push($tempRecpeitLegderIds, $fourthLevelParent->id);//2

                $fifthLevelParent = DB::table('acc_account_ledger')->where('id',$fourthLevelParent->parentId)->select("id")->first();
                array_push($tempRecpeitLegderIds, $fifthLevelParent->id);//1
                }*/
                //$recpeitLegderIds = array_unique($tempRecpeitLegderIds);
                // $recpeitLegderIds = DB::table('acc_account_ledger')->whereNotIn('accountTypeId',[13,4,5])->whereNotIn('id',[285,348])->pluck('id')->toArray();
                //$recpeitLegderIds = DB::table('acc_account_ledger')->pluck('id')->toArray();
                //////


                //Get the legders which are in Payment
                /* $tempPaymentLegderIds = array();
                $tempPaymentDebitLedgerIds = DB::table('acc_voucher_details')->whereNotIn('voucherId',$jvouchers)->whereIn('creditAcc',$secondLedgerMatchedId)->pluck('debitAcc')->toArray() + DB::table('acc_voucher_details')->whereIn('voucherId',$jvouchers)->whereIn('creditAcc',$secondLedgerMatchedId)->pluck('creditAcc')->toArray();
                $receiptDebitLedgerIds = array_unique($tempPaymentDebitLedgerIds);
                foreach ($receiptDebitLedgerIds as $receiptDebitLedgerId) {
                array_push($tempPaymentLegderIds, $receiptDebitLedgerId);//5

                $firstLevelParent = DB::table('acc_account_ledger')->where('id',$receiptDebitLedgerId)->select("id","parentId")->first();
                //array_push($tempRecpeitLegderIds, $firstLevelParent->parentId);

                $secondLevelParent = DB::table('acc_account_ledger')->where('id',$firstLevelParent->parentId)->select("id","parentId")->first();
                array_push($tempPaymentLegderIds, $secondLevelParent->id);//4

                $thirdLevelParent = DB::table('acc_account_ledger')->where('id',$secondLevelParent->parentId)->select("id","parentId")->first();
                array_push($tempPaymentLegderIds, $thirdLevelParent->id);//3

                $fourthLevelParent = DB::table('acc_account_ledger')->where('id',$thirdLevelParent->parentId)->select("id","parentId")->first();
                array_push($tempPaymentLegderIds, $fourthLevelParent->id);//2

                $fifthLevelParent = DB::table('acc_account_ledger')->where('id',$fourthLevelParent->parentId)->select("id")->first();
                array_push($tempPaymentLegderIds, $fifthLevelParent->id);//1
                }*/
                //$paymentLegderIds = array_unique($tempPaymentLegderIds);
                // $paymentLegderIds = DB::table('acc_account_ledger')->whereNotIn('accountTypeId',[12,4,5])->whereNotIn('id',[285,348])->pluck('id')->toArray();
                //$paymentLegderIds = DB::table('acc_account_ledger')->pluck('id')->toArray();
                //////






                //This hold the Ledger ids which are in the selected project and branch and type of Cash and Bank
                //$thirdMatchedLedgerId = array_intersect($firstLedgerMatchedId,$secondLedgerMatchedId);




                // dd($vouchers);

                ///////////////////////
                $voucherMachedDebitLedgerId = array();
                $voucherMachedCreditLedgerId = array();
                //$tempSecondLedgerMatchedId = array();
                $voucherMachedDebitLedgerIds = DB::table('acc_voucher_details')->whereIn('voucherId',$vouchers)->pluck('debitAcc')->toArray();
                $voucherMachedCreditLedgerIds = DB::table('acc_voucher_details')->whereIn('voucherId',$vouchers)->pluck('creditAcc')->toArray();
                //$tempSecondLedgerMatchedId = $voucherMachedDebitLedgerIds + $voucherMachedCreditLedgerIds;

                foreach ($voucherMachedDebitLedgerIds as $secondDebitLedger) {
                    $matchedLedger = DB::table('acc_account_ledger')->where('id',$secondDebitLedger)->select('id','parentId')->first();

                    array_push($voucherMachedDebitLedgerId, $matchedLedger->id);

                    $firstLevelParent = DB::table('acc_account_ledger')->where('id',$matchedLedger->parentId)->select("id","parentId")->first();
                    array_push($voucherMachedDebitLedgerId, $firstLevelParent->id);

                    $secondLevelParent = DB::table('acc_account_ledger')->where('id',$firstLevelParent->parentId)->select("id","parentId")->first();
                    array_push($voucherMachedDebitLedgerId, $secondLevelParent->id);

                    $thirdLevelParent = DB::table('acc_account_ledger')->where('id',$secondLevelParent->parentId)->select("id","parentId")->first();
                    array_push($voucherMachedDebitLedgerId, $thirdLevelParent->id);

                    $fourthLevelParent = DB::table('acc_account_ledger')->where('id',$thirdLevelParent->parentId)->select("id")->first();
                    array_push($voucherMachedDebitLedgerId, $fourthLevelParent->id);
                }

                foreach ($voucherMachedCreditLedgerIds as $secondCeditLedger) {

                    $matchedLedger = DB::table('acc_account_ledger')->where('id',$secondCeditLedger)->select('id','parentId')->first();

                    array_push($voucherMachedCreditLedgerId, $matchedLedger->id);

                    $firstLevelParent = DB::table('acc_account_ledger')->where('id',$matchedLedger->parentId)->select("id","parentId")->first();
                    array_push($voucherMachedCreditLedgerId, $firstLevelParent->id);

                    $secondLevelParent = DB::table('acc_account_ledger')->where('id',$firstLevelParent->parentId)->select("id","parentId")->first();
                    array_push($voucherMachedCreditLedgerId, $secondLevelParent->id);

                    $thirdLevelParent = DB::table('acc_account_ledger')->where('id',$secondLevelParent->parentId)->select("id","parentId")->first();
                    array_push($voucherMachedCreditLedgerId, $thirdLevelParent->id);

                    $fourthLevelParent = DB::table('acc_account_ledger')->where('id',$thirdLevelParent->parentId)->select("id")->first();
                    array_push($voucherMachedCreditLedgerId, $fourthLevelParent->id);
                }
                ///////////////////////
                $thirdMatchedLedgerId = [0];

                $debitLedgerMatchedId = array_intersect($thirdMatchedLedgerId,$voucherMachedDebitLedgerId);
                $creditLedgerMatchedId = array_intersect($thirdMatchedLedgerId,$voucherMachedCreditLedgerId);
                //$ledgerMatchedId = array_intersect($firstLedgerMatchedId,$tempSecondLedgerMatchedId);
                //$ledgerMatchedId = $secondLedgerMatchedId;


                $ledgers = DB::table('acc_account_ledger')->where('parentId',0)->orderBy('ordering', 'asc')->get();

                //Cash Type Ledgers
                $cashTypeLedgers = DB::table('acc_account_ledger')->where('accountTypeId',4)->pluck('id')->toArray();

                //Bank Type Ledgers
                $bankTypeLedgers = DB::table('acc_account_ledger')->where('accountTypeId',5)->pluck('id')->toArray();


                //Get data for Opening Balance for the Current Year Filteing Option
                $thisMonthStartDate = date('Y-m-01', strtotime($endDate));
                $thisFiscalYearStartDate = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('fyStartDate');
                $thisFiscalYearEndDate = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('fyEndDate');

                $thisMonthVouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->where('voucherDate','>=',$thisMonthStartDate)->where('voucherDate','<=',$endDate)/*->whereNotIn('id',$excludeVoucherIds)*/->pluck('id')->toArray();

                $thisYearVouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->where('voucherDate','<=',$thisFiscalYearEndDate)->where('voucherDate','>=',$thisFiscalYearStartDate)/*->whereNotIn('id',$excludeVoucherIds)*/->pluck('id')->toArray();

                $dateRangeVouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->where('voucherDate','>=',$startDate)->where('voucherDate','<=',$endDate)/*->whereNotIn('id',$excludeVoucherIds)*/->pluck('id')->toArray();

                // echo "<br><br><br><br><br>";
                // // print_r(count($thisMonthVouchers));
                // echo $projectSelected;
                // echo "<br>";
                // print_r($projectTypeId);
                // echo "<br>";

                // print_r($voucherTypeId);
                // echo "$thisMonthStartDate<br>";
                // echo "$thisFiscalYearStartDate<br>";

                $cashTypeMatchedDebitLedgerId = array_intersect($debitLedgerMatchedId,$cashTypeLedgers);

                $thisMonthTransactionOfCashTypeDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisMonthVouchers)->whereIn('debitAcc',$cashTypeLedgers)->sum('amount');

                // echo $thisMonthTransactionOfCashTypeDebit;
                // print_r($cashTypeLedgers);


                $thisMonthTransactionOfCashTypeCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisMonthVouchers)->whereIn('creditAcc',$cashTypeLedgers)->sum('amount');

                // echo $thisMonthTransactionOfCashTypeCredit;

                $bankTypeMatchedDebitLedgerId = array_intersect($debitLedgerMatchedId,$bankTypeLedgers);

                $thisMonthTransactionOfBankTypeDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisMonthVouchers)->whereIn('debitAcc',$bankTypeLedgers)->sum('amount');

                // echo $thisMonthTransactionOfBankTypeDebit;

                $thisMonthTransactionOfBankTypeCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisMonthVouchers)->whereIn('creditAcc',$bankTypeLedgers)->sum('amount');



                $thisYearTransactionOfCashTypeDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisYearVouchers)->whereIn('debitAcc',$cashTypeLedgers)->sum('amount');

                // echo $thisMonthTransactionOfCashTypeDebit;
                // print_r($cashTypeLedgers);


                $thisYearTransactionOfCashTypeCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisYearVouchers)->whereIn('creditAcc',$cashTypeLedgers)->sum('amount');

                // echo $thisMonthTransactionOfCashTypeCredit;


                $thisYearTransactionOfBankTypeDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisYearVouchers)->whereIn('debitAcc',$bankTypeLedgers)->sum('amount');

                // echo $thisMonthTransactionOfBankTypeDebit;

                $thisYearTransactionOfBankTypeCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisYearVouchers)->whereIn('creditAcc',$bankTypeLedgers)->sum('amount');


                $dateRangeTransactionOfCashTypeDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$dateRangeVouchers)->whereIn('debitAcc',$cashTypeLedgers)->sum('amount');

                // echo $thisMonthTransactionOfCashTypeDebit;
                // print_r($cashTypeLedgers);


                $dateRangeTransactionOfCashTypeCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$dateRangeVouchers)->whereIn('creditAcc',$cashTypeLedgers)->sum('amount');

                // echo $thisMonthTransactionOfCashTypeCredit;


                $dateRangeTransactionOfBankTypeDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$dateRangeVouchers)->whereIn('debitAcc',$bankTypeLedgers)->sum('amount');

                // echo $thisMonthTransactionOfBankTypeDebit;

                $dateRangeTransactionOfBankTypeCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$dateRangeVouchers)->whereIn('creditAcc',$bankTypeLedgers)->sum('amount');

                // echo $thisMonthTransactionOfBankTypeCredit;



                //Get data for Opening Balance for the Date Range Filteing Option
                if ($previousfiscalYearId!=null) {
                    $thisperoidStartDate = DB::table('gnr_fiscal_year')->where('id',$previousfiscalYearId)->value('fyEndDate');

                    $thisPeriodVouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->where('voucherDate','>',$thisperoidStartDate)->where('voucherDate','<',$thisMonthStartDate)->whereNotIn('id',$excludeVoucherIds)->pluck('id')->toArray();

                    $thisPeriodVoucherIds = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->where('voucherDate','>=',$startDate)->where('voucherDate','<=',$endDate)->whereNotIn('id',$excludeVoucherIds)->pluck('id')->toArray();

                }else{
                    $thisPeriodVouchers = [0];
                    $thisPeriodVoucherIds = [0];
                }
                // dd($startDate);


                $thisPeriodTransactionOfCashTypeDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisPeriodVouchers)->whereIn('debitAcc',$cashTypeLedgers)->sum('amount');

                $thisPeriodTransactionOfCashTypeCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisPeriodVouchers)->whereIn('creditAcc',$cashTypeLedgers)->sum('amount');
                $thisPeriodTransactionOfBankTypeDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisPeriodVouchers)->whereIn('debitAcc',$bankTypeLedgers)->sum('amount');
                $thisPeriodTransactionOfBankTypeCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisPeriodVouchers)->whereIn('creditAcc',$bankTypeLedgers)->sum('amount');
                //////
                ///
            }

            // dd($branchId);
            $openingBalanceInfo = DB::table('acc_opening_balance')
                                ->where('companyIdFk', $user_company_id)
                                ->where('projectId', $projectSelected)
                                ->whereIn('projectTypeId', $projectTypeId)
                                ->whereIn('branchId', $branchId)
                                ->select('ledgerId', 'fiscalYearId', 'debitAmount', 'creditAmount', 'balanceAmount')
                                ->get();
                                // dd($openingBalanceInfo);

            // trial codes from view file
            //////////////// opening balannce///////////////////
            //this month
            $obThisMonthCash = 0;
            $obThisMonthBank = 0;
            $obThisMonth = 0;
            // this year
            $obThisYearCash = 0;
            $obThisYearBank = 0;
            $obThisYear = 0;
            // previous year
            $obPreviousYearCash = 0;
            $obPreviousYearBank = 0;
            $obPreviousYear = 0;
            // this period
            $obThisPeriodCash = 0;
            $obThisPeriodBank = 0;
            $obThisPeriod = 0;
            // if search by fiscal year
            if ($searchMethodSelected == 1) {

                if ($previousfiscalYearId != null) {
                    //opening of current
                    $currentYearOpeningBalance = $openingBalanceInfo->where('fiscalYearId',$previousfiscalYearId);
                    // cash in hand
                    $cashInHandForCurrentFiscalYear = $currentYearOpeningBalance
                                                    ->whereIn('ledgerId',$cashTypeLedgers)
                                                    ->sum('balanceAmount');
                    // cash in bank
                    $cashInBankForCurrentFiscalYear = $currentYearOpeningBalance
                                                    ->whereIn('ledgerId',$bankTypeLedgers)
                                                    ->sum('balanceAmount');

                    //previous previous fiscal year id
                    $dateToCompareTwo = date('Y-m-d', strtotime('-1 day', strtotime($previousfiscalYearStartDate)));
                    $previousPreviousfiscalYearId = DB::table('gnr_fiscal_year')->where('fyEndDate',$dateToCompareTwo)->value('id');
                    // dd($previousPreviousfiscalYearId);
                    //opening of previous
                    if ($previousPreviousfiscalYearId!=null) {
                        //opening of previous
                        $previousYearOpeningBalance = $openingBalanceInfo->where('fiscalYearId',$previousPreviousfiscalYearId);
                        // cash in hand
                        $cashInHandForPreviousFiscalYear = $previousYearOpeningBalance
                                                        ->whereIn('ledgerId',$cashTypeLedgers)
                                                        ->sum('balanceAmount');
                        // cash in bank
                        $cashInBankForPreviousFiscalYear = $previousYearOpeningBalance
                                                        ->whereIn('ledgerId',$bankTypeLedgers)
                                                        ->sum('balanceAmount');
                    }
                    else{
                        $cashInHandForPreviousFiscalYear = 0;
                        $cashInBankForPreviousFiscalYear = 0;
                    }

                }
                else{
                    $cashInHandForCurrentFiscalYear = 0;
                    $cashInBankForCurrentFiscalYear = 0;
                }

                // voucher type only jv
                if ($voucherTypeSelected == 2) {

                    $cashInHandForPreviousFiscalYear = 0;
                    $cashInBankForPreviousFiscalYear = 0;
                    $cashInHandForCurrentFiscalYear = 0;
                    $cashInBankForCurrentFiscalYear = 0;

                }
                // dd($cashInHandForCurrentFiscalYear);
                // this year
                $obThisYearCash = $cashInHandForCurrentFiscalYear;
                $obThisYearBank = $cashInBankForCurrentFiscalYear;
                $obThisYear = $obThisYearCash + $obThisYearBank;
                // previous year
                $obPreviousYearCash = $cashInHandForPreviousFiscalYear;
                $obPreviousYearBank = $cashInBankForPreviousFiscalYear;
                $obPreviousYear = $obPreviousYearCash + $obPreviousYearBank;
            }
            // if search by current year
            elseif ($searchMethodSelected == 2) {

                if ($previousfiscalYearId!=null) {
                    //opening of current
                    $currentYearOpeningBalance = $openingBalanceInfo->where('fiscalYearId',$previousfiscalYearId);
                    // cash in hand
                    $cashInHandForCurrentFiscalYear = $currentYearOpeningBalance
                                                    ->whereIn('ledgerId',$cashTypeLedgers)
                                                    ->sum('balanceAmount');
                    // cash in bank
                    $cashInBankForCurrentFiscalYear = $currentYearOpeningBalance
                                                    ->whereIn('ledgerId',$bankTypeLedgers)
                                                    ->sum('balanceAmount');
                }
                else{
                    $cashInHandForCurrentFiscalYear = 0;
                    $cashInBankForCurrentFiscalYear = 0;
                }
                //this month
                $obThisMonthCash = $cashInHandForCurrentFiscalYear + $thisPeriodTransactionOfCashTypeDebit - $thisPeriodTransactionOfCashTypeCredit;
                $obThisMonthBank = $cashInBankForCurrentFiscalYear + $thisPeriodTransactionOfBankTypeDebit - $thisPeriodTransactionOfBankTypeCredit;
                $obThisMonth = $obThisMonthCash + $obThisMonthBank;
                // this year
                $obThisYearCash = $cashInHandForCurrentFiscalYear;
                $obThisYearBank = $cashInBankForCurrentFiscalYear;
                $obThisYear = $obThisYearCash + $obThisYearBank;

            }
            // IF Search By Date Range
            elseif ($searchMethodSelected == 3) {

                if ($previousfiscalYearId!=null) {
                    //opening of current
                    $currentYearOpeningBalance = $openingBalanceInfo->where('fiscalYearId',$previousfiscalYearId);
                    // cash in hand
                    $cashInHandForCurrentFiscalYear = $currentYearOpeningBalance
                                                    ->whereIn('ledgerId',$cashTypeLedgers)
                                                    ->sum('balanceAmount');
                    // cash in bank
                    $cashInBankForCurrentFiscalYear = $currentYearOpeningBalance
                                                    ->whereIn('ledgerId',$bankTypeLedgers)
                                                    ->sum('balanceAmount');
                }
                else{
                    $cashInHandForCurrentFiscalYear = 0;
                    $cashInBankForCurrentFiscalYear = 0;
                }
                // this period
                $obThisPeriodCash = $cashInHandForCurrentFiscalYear + $thisPeriodTransactionOfCashTypeDebit - $thisPeriodTransactionOfCashTypeCredit;
                $obThisPeriodBank = $cashInBankForCurrentFiscalYear + $thisPeriodTransactionOfBankTypeDebit - $thisPeriodTransactionOfBankTypeCredit;
                $obThisPeriod = $obThisPeriodCash + $obThisPeriodBank;

            }
            // dd($obThisYear);

            ////////////////ledger wise balance /////////////////
            // receipt payment ledgers
            //this month
            if ($branchSelected == null || $branchSelected == 0) {

                $thisMonthCash = $thisMonthTransactionOfCashTypeDebit - $thisMonthTransactionOfCashTypeCredit;
                $thisMonthBank = $thisMonthTransactionOfBankTypeDebit - $thisMonthTransactionOfBankTypeCredit;
                // this year
                $thisYearCash = $thisYearTransactionOfCashTypeDebit - $thisYearTransactionOfCashTypeCredit;
                $thisYearBank = $thisYearTransactionOfBankTypeDebit - $thisYearTransactionOfBankTypeCredit;
                // previous year
                $previousYearCash = $previousYearTransactionOfCashTypeDebit - $previousYearTransactionOfCashTypeCredit;
                $previousYearBank = $previousYearTransactionOfBankTypeDebit - $previousYearTransactionOfBankTypeCredit;
                // this period
                $dateRangeCash = $dateRangeTransactionOfCashTypeDebit - $dateRangeTransactionOfCashTypeCredit;
                $dateRangeBank = $dateRangeTransactionOfBankTypeDebit - $dateRangeTransactionOfBankTypeCredit;
            }
            // else {
            //     $thisMonthCash = 0;
            //     $thisMonthBank = 0;
            //     // this year
            //     $thisYearCash = 0;
            //     $thisYearBank = 0;
            //     // previous year
            //     $previousYearCash = 0;
            //     $previousYearBank = 0;
            //     // this period
            //     $dateRangeCash = 0;
            //     $dateRangeBank = 0;
            // }

            // $finalLevelReceiptPaymentLedgers = $finalLevelReceiptPaymentLedgers->where('id', 411);
            // dd($finalLevelReceiptPaymentLedgers);
            foreach ($finalLevelReceiptPaymentLedgers as $key => $ledger) {

                if ($branchSelected == null || $branchSelected == 0) {

                    $thisYearLedgerInfo = $thisYearInfo->where('ledgerId', $ledger->id);
                    $previousYearLedgerInfo = $previousYearsInfo->where('ledgerId', $ledger->id);
                    $thisMonthLedgerInfo = $thisMonthInfo->where('ledgerId', $ledger->id);

                }
                else {
                    $thisChildVoucherDetails = DB::table('acc_voucher_details')
                    ->where('debitAcc', $ledger->id)
                    ->orWhere('creditAcc', $ledger->id)
                    ->select('debitAcc','creditAcc','voucherId','amount')
                    ->get();

                    // if (in_array($ledger->id, $cashTypeLedgers)) {
                    //
                    //     $thisMonthCash += $thisChildVoucherDetails
                    //                     ->whereIn('voucherId', $thisMonthVoucherIds)
                    //                     ->where('debitAcc', $ledger->id)
                    //                     ->sum('amount') -
                    //                     $thisChildVoucherDetails
                    //                     ->whereIn('voucherId', $thisMonthVoucherIds)
                    //                     ->where('creditAcc', $ledger->id)
                    //                     ->sum('amount');
                    //
                    //     $thisPeriodCash += $thisChildVoucherDetails->sum('balanceAmount');
                    //     $thisYearCash += $thisChildVoucherDetails->sum('balanceAmount');
                    //     $previousYearCash += $thisChildVoucherDetails->sum('balanceAmount');
                    // }
                    // elseif(in_array($ledger->id, $bankTypeLedgers) {
                    //     $thisMonthBank += $thisMonthLedgerInfo->sum('balanceAmount');
                    //     $thisPeriodBank += $thisPeriodLedgerInfo->sum('balanceAmount');
                    //     $thisYearBank += $thisYearLedgerInfo->sum('balanceAmount');
                    //     $previousYearBank += $openingBalanceLedgerInfo->sum('balanceAmount');
                    // }
                }


                if ($voucherTypeSelected == "") {
                    //Curent Debit Credit
                    if ($branchSelected == null || $branchSelected == 0) {
                        // previous
                        $previousFiscalYearDebit = $previousYearLedgerInfo->sum('cashDebit') + $previousYearLedgerInfo->sum('bankDebit');
                        $previousFiscalYearCredit = $previousYearLedgerInfo->sum('cashCredit') + $previousYearLedgerInfo->sum('bankCredit');
                        // current
                        $currentDebit = $thisYearLedgerInfo->sum('cashDebit') + $thisYearLedgerInfo->sum('bankDebit');
                        $currentCredit = $thisYearLedgerInfo->sum('cashCredit') + $thisYearLedgerInfo->sum('bankCredit');
                    }
                    else {
                        $currentDebit = $thisChildVoucherDetails
                        ->where('debitAcc', $ledger->id)
                        ->whereIn('creditAcc',$bankNCashLedgerIds)
                        ->whereIn('voucherId', $vouchers)
                        ->sum('amount');


                        // $currentCredit = DB::table('acc_voucher_details')
                        //                         ->where('creditAcc', $childId)
                        //                         ->whereIn('debitAcc',$bankNCashLedgerIds)
                        //                         ->whereIn('voucherId',$info['vouchers'])
                        //                         ->sum('amount');

                        $currentCredit = $thisChildVoucherDetails
                        ->where('creditAcc', $ledger->id)
                        ->whereIn('debitAcc',$bankNCashLedgerIds)
                        ->whereIn('voucherId',$vouchers)
                        ->sum('amount');

                        $previousFiscalYearDebit = $thisChildVoucherDetails
                        ->where('debitAcc', $ledger->id)
                        ->whereIn('creditAcc',$bankNCashLedgerIds)
                        ->whereIn('voucherId', $previousfiscalYearVouchers)
                        ->sum('amount');


                        // $currentCredit = DB::table('acc_voucher_details')
                        //                         ->where('creditAcc', $childId)
                        //                         ->whereIn('debitAcc',$bankNCashLedgerIds)
                        //                         ->whereIn('voucherId',$info['vouchers'])
                        //                         ->sum('amount');

                        $previousFiscalYearCredit = $thisChildVoucherDetails
                        ->where('creditAcc', $ledger->id)
                        ->whereIn('debitAcc',$bankNCashLedgerIds)
                        ->whereIn('voucherId',$previousfiscalYearVouchers)
                        ->sum('amount');

                    }

                }
                elseif($voucherTypeSelected == 1){

                    if ($branchSelected == null || $branchSelected == 0) {

                        // previous
                        $previousFiscalYearDebit = $previousYearLedgerInfo->sum('cashDebit') + $previousYearLedgerInfo->sum('bankDebit') + $previousYearLedgerInfo->sum('jvDebit');
                        $previousFiscalYearCredit = $previousYearLedgerInfo->sum('cashCredit') + $previousYearLedgerInfo->sum('bankCredit') + $previousYearLedgerInfo->sum('jvCredit');
                        // current
                        $currentDebit = $thisYearLedgerInfo->sum('cashDebit') + $thisYearLedgerInfo->sum('bankDebit') + $thisYearLedgerInfo->sum('jvDebit');
                        $currentCredit = $thisYearLedgerInfo->sum('cashCredit') + $thisYearLedgerInfo->sum('bankCredit') + $thisYearLedgerInfo->sum('jvCredit');
                    }
                    else {
                        $jvouchers = DB::table('acc_voucher')->whereIn('id',$vouchers)->where('voucherTypeId',3)->pluck('id')->toArray();

                        $currentDebitForJV = $thisChildVoucherDetails
                                            ->where('debitAcc', $ledger->id)
                                            ->whereIn('voucherId',$jvouchers)
                                            ->sum('amount');

                        $currentCreditForJV = $thisChildVoucherDetails
                                            ->where('creditAcc', $ledger->id)
                                            ->whereIn('voucherId',$jvouchers)
                                            ->sum('amount');

                        $previousDebitForJV = $thisChildVoucherDetails
                                            ->where('debitAcc', $ledger->id)
                                            ->whereIn('voucherId',$jvouchers)
                                            ->whereIn('voucherId',$previousfiscalYearVouchers)
                                            ->sum('amount');

                        $previousCreditForJV = $thisChildVoucherDetails
                                            ->where('creditAcc', $ledger->id)
                                            ->whereIn('voucherId',$jvouchers)
                                            ->whereIn('voucherId',$previousfiscalYearVouchers)
                                            ->sum('amount');

                        $currentDebit = $thisChildVoucherDetails
                                        ->where('debitAcc', $ledger->id)
                                        ->whereIn('creditAcc',$bankNCashLedgerIds)
                                        ->whereIn('voucherId',$vouchers)
                                        ->sum('amount');

                        $currentCredit = $thisChildVoucherDetails
                                        ->where('creditAcc', $ledger->id)
                                        ->whereIn('debitAcc',$bankNCashLedgerIds)
                                        ->whereIn('voucherId',$vouchers)
                                        ->sum('amount');

                        $previousFiscalYearDebit = $thisChildVoucherDetails
                        ->where('debitAcc', $ledger->id)
                        ->whereIn('creditAcc',$bankNCashLedgerIds)
                        ->whereIn('voucherId', $previousfiscalYearVouchers)
                        ->sum('amount');

                        $previousFiscalYearCredit = $thisChildVoucherDetails
                        ->where('creditAcc', $ledger->id)
                        ->whereIn('debitAcc',$bankNCashLedgerIds)
                        ->whereIn('voucherId',$previousfiscalYearVouchers)
                        ->sum('amount');


                        $currentDebit = $currentDebit + $currentDebitForJV;
                        $currentCredit = $currentCredit + $currentCreditForJV;
                        $previousFiscalYearDebit = $previousFiscalYearDebit + $previousDebitForJV;
                        $previousFiscalYearCredit = $previousFiscalYearCredit + $previousCreditForJV;
                    }

                }
                elseif($voucherTypeSelected == 2){

                    if ($branchSelected == null || $branchSelected == 0) {
                        // previous
                        $previousFiscalYearDebit = $previousYearLedgerInfo->sum('jvDebit');
                        $previousFiscalYearCredit = $previousYearLedgerInfo->sum('jvCredit');
                        // current
                        $currentDebit = $thisYearLedgerInfo->sum('jvDebit');
                        $currentCredit = $thisYearLedgerInfo->sum('jvCredit');

                    }
                    else {
                        $currentDebit = DB::table('acc_voucher_details')
                        ->where('debitAcc', $ledger->id)
                        ->whereIn('voucherId',$vouchers)
                        ->sum('amount');

                        $currentCredit = DB::table('acc_voucher_details')
                        ->where('creditAcc', $ledger->id)
                        ->whereIn('voucherId',$vouchers)
                        ->sum('amount');

                        $previousFiscalYearDebit = $thisChildVoucherDetails
                                            ->where('debitAcc', $ledger->id)
                                            ->whereIn('voucherId',$jvouchers)
                                            ->whereIn('voucherId',$previousfiscalYearVouchers)
                                            ->sum('amount');

                        $previousFiscalYearCredit = $thisChildVoucherDetails
                                            ->where('creditAcc', $ledger->id)
                                            ->whereIn('voucherId',$jvouchers)
                                            ->whereIn('voucherId',$previousfiscalYearVouchers)
                                            ->sum('amount');
                    }

                }
                //Previous Year Debit Credit

                elseif($voucherTypeSelected == ""){

                }

                if ($branchSelected == null || $branchSelected == 0) {

                    if ($voucherTypeSelected == '') {
                        $thisMonthDebit = $thisMonthLedgerInfo->sum('cashDebit') + $thisMonthLedgerInfo->sum('bankDebit');
                        $thisMonthCredit = $thisMonthLedgerInfo->sum('cashCredit') + $thisMonthLedgerInfo->sum('bankCredit') ;
                    }
                    elseif ($voucherTypeSelected == 1) {
                        $thisMonthDebit = $thisMonthLedgerInfo->sum('cashDebit') + $thisMonthLedgerInfo->sum('bankDebit') + $thisMonthLedgerInfo->sum('jvDebit');
                        $thisMonthCredit = $thisMonthLedgerInfo->sum('cashCredit') + $thisMonthLedgerInfo->sum('bankCredit') + $thisMonthLedgerInfo->sum('jvCredit');
                    }
                    elseif ($voucherTypeSelected == 2) {
                        $thisMonthDebit = $thisMonthLedgerInfo->sum('jvDebit');
                        $thisMonthCredit = $thisMonthLedgerInfo->sum('jvCredit');
                    }

                }
                else {
                    $thisMonthDebit = $thisChildVoucherDetails
                    ->where('debitAcc', $ledger->id)
                    ->whereIn('creditAcc',$bankNCashLedgerIds)
                    ->whereIn('voucherId',$thisMonthVoucherIds)
                    ->sum('amount');

                    $thisMonthCredit = $thisChildVoucherDetails
                    ->where('creditAcc', $ledger->id)
                    ->whereIn('debitAcc',$bankNCashLedgerIds)
                    ->whereIn('voucherId',$thisMonthVoucherIds)
                    ->sum('amount');
                }
                //End This Month Debit Credit

                //cumulative
                if ($previousfiscalYearId!=null) {

                    if ($branchSelected == null || $branchSelected == 0) {

                        $openingDebitForCumulative = $previousYearsInfo->where('ledgerId',$ledger->id)->sum('debitAmount');
                        $openingCreditForCumulative = $previousYearsInfo->where('ledgerId',$ledger->id)->sum('creditAmount');
                    }
                    else {
                        $openingBalanceLedgerInfo = $openingBalanceInfo->where('ledgerId', $ledger->id);
                        $openingDebitForCumulative = $openingBalanceLedgerInfo
                                                    ->where('ledgerId',$ledger->id)
                                                    ->sum('debitAmount');

                        $openingCreditForCumulative = $openingBalanceLedgerInfo
                                                    ->where('ledgerId',$ledger->id)
                                                    ->sum('creditAmount');
                    }

                }

                // $cumulativeDebit = $openingDebitForCumulative + $currentDebitForCumulative;
                $cumulativeDebit = $openingDebitForCumulative + $currentDebit;
                // $cumulativeCredit = $openingCreditForCumulative + $currentCreditForCumulative;
                $cumulativeCredit = $openingCreditForCumulative + $currentCredit;
                //End cumulative

                $ledgerWiseData[] = array(
                    'id'                            => $ledger->id,
                    'code'                          => $ledger->code,
                    'name'                          => $ledger->name,
                    'isGroupHead'                   => $ledger->isGroupHead,
                    'parentId'                      => $ledger->parentId,
                    'level'                         => $ledger->level,
                    'currentDebit'                  => $currentDebit,
                    'currentCredit'                 => $currentCredit,
                    'thisMonthDebit'                => $thisMonthDebit,
                    'thisMonthCredit'               => $thisMonthCredit,
                    'previousFiscalYearDebit'       => $previousFiscalYearDebit,
                    'previousFiscalYearCredit'      => $previousFiscalYearCredit,
                    'cumulativeDebit'               => $cumulativeDebit,
                    'cumulativeCredit'              => $cumulativeCredit,
                );
                // dd($ledgerWiseData);
            }
            // dd($ledgerWiseData);

            //////////////// closing balannce///////////////////
            //this month
            $cbThisMonthCash = 0;
            $cbThisMonthBank = 0;
            $cbThisMonth = 0;
            // this year
            $cbThisYearCash = 0;
            $cbThisYearBank = 0;
            $cbThisYear = 0;
            // previous year
            $cbPreviousYearCash = 0;
            $cbPreviousYearBank = 0;
            $cbPreviousYear = 0;
            // this period
            $cbThisPeriodCash = 0;
            $cbThisPeriodBank = 0;
            $cbThisPeriod = 0;

            // $openingBalanceInfo = $openingBalanceInfo
            //                     ->whereIn('ledgerId', $finalLevelReceiptPaymentLedgers->pluck('id'))
            //                     ->whereIn('ledgerId', $cashTypeLedgers);
            // $thisYearLedgerInfo = $thisYearInfo->where('ledgerId', $ledger->id);
            // $thisMonthLedgerInfo = $thisMonthInfo->where('ledgerId', $ledger->id);
            // $dateRangeLedgerInfo = $dateRangeInfo->where('ledgerId', $ledger->id);
            //
            // if (in_array($ledger->id, $cashTypeLedgers)) {
            //     $thisMonthCash += $thisMonthLedgerInfo->sum('balanceAmount');
            //     $dateRangeCash += $dateRangeLedgerInfo->sum('balanceAmount');
            //     $thisYearCash += $thisYearLedgerInfo->sum('balanceAmount');
            //     $previousYearCash += $openingBalanceLedgerInfo->sum('balanceAmount');
            // }
            // elseif (in_array($ledger->id, $bankTypeLedgers)) {
            //     $thisMonthBank += $thisMonthLedgerInfo->sum('balanceAmount');
            //     $dateRangeBank += $dateRangeLedgerInfo->sum('balanceAmount');
            //     $thisYearBank += $thisYearLedgerInfo->sum('balanceAmount');
            //     $previousYearBank += $openingBalanceLedgerInfo->sum('balanceAmount');
            // }

            // if search by fiscal year
            if ($searchMethodSelected == 1) {

                if ($branchSelected == null || $branchSelected == 0) {
                    // this year
                    $cbThisYearCash = $obThisYearCash + $thisYearCash;
                    $cbThisYearBank = $obThisYearBank + $thisYearBank;
                    // previous year
                    $cbPreviousYearCash = $obPreviousYearCash + $previousYearCash;
                    $cbPreviousYearBank = $obPreviousYearBank + $previousYearBank;
                    // $cbPreviousYearCash = $obThisYearCash;
                    // $cbPreviousYearBank = $obThisYearBank;
                }
                else {
                    if ($previousfiscalYearId != null) {
                        //closing of previous
                        $previousYearClosingBalance = $openingBalanceInfo->where('fiscalYearId',$previousfiscalYearId);
                        // cash in hand
                        $cashInHandForPreviousFiscalYear = $previousYearClosingBalance
                                                        ->whereIn('ledgerId',$cashTypeLedgers)
                                                        ->sum('balanceAmount');
                        // cash in bank
                        $cashInBankForPreviousFiscalYear = $previousYearClosingBalance
                                                        ->whereIn('ledgerId',$bankTypeLedgers)
                                                        ->sum('balanceAmount');

                    }
                    else{
                        $cashInHandForPreviousFiscalYear = 0;
                        $cashInBankForPreviousFiscalYear = 0;
                    }
                    //closing of current
                    // cash in hand
                    $cashInHandForCurrentFiscalYear = $thisYearTransactionOfCashTypeDebit - $thisYearTransactionOfCashTypeCredit;
                    // cash in bank
                    $cashInBankForCurrentFiscalYear = $thisYearTransactionOfBankTypeDebit - $thisYearTransactionOfBankTypeCredit;

                    // voucher type only jv
                    if ($voucherTypeSelected == 2) {

                        $cashInHandForPreviousFiscalYear = 0;
                        $cashInBankForPreviousFiscalYear = 0;
                        $cashInHandForCurrentFiscalYear = 0;
                        $cashInBankForCurrentFiscalYear = 0;

                    }
                    // dd($cashInHandForCurrentFiscalYear);
                    // this year
                    $cbThisYearCash = $cashInHandForPreviousFiscalYear + $cashInHandForCurrentFiscalYear;
                    $cbThisYearBank = $cashInBankForPreviousFiscalYear + $cashInBankForCurrentFiscalYear;
                    // previous year
                    $cbPreviousYearCash = $cashInHandForPreviousFiscalYear;
                    $cbPreviousYearBank = $cashInBankForPreviousFiscalYear;

                }

                $cbThisYear = $cbThisYearCash + $cbThisYearBank;
                $cbPreviousYear = $cbPreviousYearCash + $cbPreviousYearBank;

            }
            // if search by current year
            elseif ($searchMethodSelected == 2) {

                if ($branchSelected == null || $branchSelected == 0) {
                    //this month
                    $cbThisMonthCash = $obThisMonthCash + $thisMonthCash;
                    $cbThisMonthBank = $obThisMonthBank + $thisMonthBank;

                    // this year
                    $cbThisYearCash = $cbThisMonthCash;
                    $cbThisYearBank = $cbThisMonthBank;
                }
                else {
                    if ($previousfiscalYearId != null) {
                        //closing of previous
                        $previousYearClosingBalance = $openingBalanceInfo->where('fiscalYearId',$previousfiscalYearId);
                        // cash in hand
                        $cashInHandForPreviousFiscalYear = $previousYearClosingBalance
                                                        ->whereIn('ledgerId',$cashTypeLedgers)
                                                        ->sum('balanceAmount');
                        // cash in bank
                        $cashInBankForPreviousFiscalYear = $previousYearClosingBalance
                                                        ->whereIn('ledgerId',$bankTypeLedgers)
                                                        ->sum('balanceAmount');

                    }
                    else{
                        $cashInHandForPreviousFiscalYear = 0;
                        $cashInBankForPreviousFiscalYear = 0;
                    }
                    //closing of current
                    // cash in hand
                    $cashInHandForCurrentFiscalYear = $thisPeriodTransactionOfCashTypeDebit - $thisPeriodTransactionOfCashTypeCredit + $thisMonthTransactionOfCashTypeDebit - $thisMonthTransactionOfCashTypeCredit;
                    // cash in bank
                    $cashInBankForCurrentFiscalYear = $thisPeriodTransactionOfBankTypeDebit - $thisPeriodTransactionOfBankTypeCredit + $thisMonthTransactionOfBankTypeDebit - $thisMonthTransactionOfBankTypeCredit;
                    //this month
                    $cbThisMonthCash = $cashInHandForPreviousFiscalYear + $cashInHandForCurrentFiscalYear;
                    $cbThisMonthBank = $cashInBankForPreviousFiscalYear + $cashInBankForCurrentFiscalYear;

                    // this year
                    $cbThisYearCash = $cbThisMonthCash;
                    $cbThisYearBank = $cbThisMonthBank;

                }
                $cbThisMonth = $cbThisMonthCash + $cbThisMonthBank;
                $cbThisYear = $cbThisMonth;

            }
            // IF Search By Date Range
            elseif ($searchMethodSelected == 3) {

                if ($branchSelected == null || $branchSelected == 0) {

                    $cbThisPeriodCash = $obThisPeriodCash + $dateRangeCash;
                    $cbThisPeriodBank = $obThisPeriodBank + $dateRangeBank;
                }
                else {
                    if ($previousfiscalYearId!=null) {
                        //closing of this period
                        $previousYearClosingBalance = $openingBalanceInfo->where('fiscalYearId',$previousfiscalYearId);
                        // cash in hand
                        $cashInHandForPreviousFiscalYear = $previousYearClosingBalance
                                                        ->whereIn('ledgerId',$cashTypeLedgers)
                                                        ->sum('balanceAmount');
                        // cash in bank
                        $cashInBankForPreviousFiscalYear = $previousYearClosingBalance
                                                        ->whereIn('ledgerId',$bankTypeLedgers)
                                                        ->sum('balanceAmount');
                    }
                    else{
                        $cashInHandForPreviousFiscalYear = 0;
                        $cashInBankForPreviousFiscalYear = 0;
                    }
                    // this period
                    $cbThisPeriodCash = $cashInHandForPreviousFiscalYear + $thisPeriodTransactionOfCashTypeDebit - $thisPeriodTransactionOfCashTypeCredit +
                                        $dateRangeTransactionOfCashTypeDebit - $dateRangeTransactionOfCashTypeCredit;
                    $cbThisPeriodBank = $cashInBankForPreviousFiscalYear + $thisPeriodTransactionOfBankTypeDebit - $thisPeriodTransactionOfBankTypeCredit +
                                        $dateRangeTransactionOfBankTypeDebit - $dateRangeTransactionOfBankTypeCredit;
                }

                $cbThisPeriod = $cbThisPeriodCash + $cbThisPeriodBank;

            }


            $data = array(
                'ledgers'                                   => $ledgers,
                'projects'                                  => $projects,
                'projectTypes'                              => $projectTypes,
                'branches'                                  => $branches,
                'startDate'                                 => $startDate,
                'endDate'                                   => $endDate,
                'projectSelected'                           => $projectSelected,
                'projectTypeSelected'                       => $projectTypeSelected,
                'branchSelected'                            => $branchSelected,
                'dateFromSelected'                          => $dateFromSelected,
                'dateToSelected'                            => $dateToSelected,
                'firstRequest'                              => $firstRequest,
                'roundUpSelected'                           => $roundUpSelected,
                'depthLevel'                                => $depthLevel,
                'depthLevelSelected'                        => $depthLevelSelected,
                'debitLedgerMatchedId'                      => $debitLedgerMatchedId,
                'creditLedgerMatchedId'                     => $creditLedgerMatchedId,
                'newBranches'                               => $newBranches,
                'vouchers'                                  => $vouchers,
                'openingVouchers'                           => $openingVouchers,
                'openingBalanceInfo'                        => $openingBalanceInfo,
                'fiscalYearId'                              => $fiscalYearId,
                'withZeroSelected'                          => $withZeroSelected,
                'searchMethodSelected'                      => $searchMethodSelected,
                'fiscalYears'                               => $fiscalYears,
                'fiscalYearSelected'                        => $fiscalYearSelected,
                'voucherTypeSelected'                       => $voucherTypeSelected,
                'thisMonthTransactionOfCashTypeDebit'       => $thisMonthTransactionOfCashTypeDebit,
                'thisMonthTransactionOfCashTypeCredit'      => $thisMonthTransactionOfCashTypeCredit,
                'thisMonthTransactionOfBankTypeDebit'       => $thisMonthTransactionOfBankTypeDebit,
                'thisMonthTransactionOfBankTypeCredit'      => $thisMonthTransactionOfBankTypeCredit,
                'thisPeriodTransactionOfCashTypeDebit'      => $thisPeriodTransactionOfCashTypeDebit,
                'thisPeriodTransactionOfCashTypeCredit'     => $thisPeriodTransactionOfCashTypeCredit,
                'thisPeriodTransactionOfBankTypeDebit'      => $thisPeriodTransactionOfBankTypeDebit,
                'thisPeriodTransactionOfBankTypeCredit'     => $thisPeriodTransactionOfBankTypeCredit,
                'thisYearTransactionOfCashTypeDebit'        => $thisYearTransactionOfCashTypeDebit,
                'thisYearTransactionOfCashTypeCredit'       => $thisYearTransactionOfCashTypeCredit,
                'thisYearTransactionOfBankTypeDebit'        => $thisYearTransactionOfBankTypeDebit,
                'thisYearTransactionOfBankTypeCredit'       => $thisYearTransactionOfBankTypeCredit,
                'dateRangeTransactionOfCashTypeDebit'       => $dateRangeTransactionOfCashTypeDebit,
                'dateRangeTransactionOfCashTypeCredit'      => $dateRangeTransactionOfCashTypeCredit,
                'dateRangeTransactionOfBankTypeDebit'       => $dateRangeTransactionOfBankTypeDebit,
                'dateRangeTransactionOfBankTypeCredit'      => $dateRangeTransactionOfBankTypeCredit,
                'previousfiscalYearVouchers'                => $previousfiscalYearVouchers,
                'previousfiscalYearId'                      => $previousfiscalYearId,
                'thisMonthInfo'                             => $thisMonthInfo,
                'thisPeriodInfo'                            => $thisPeriodInfo,
                'thisYearInfo'                              => $thisYearInfo,
                'thisPeriodVouchers'                        => $thisPeriodVouchers,
                'thisMonthVoucherIds'                       => $thisMonthVoucherIds,
                'thisPeriodVoucherIds'                      => $thisPeriodVoucherIds,
                'projectTypeId'                             => $projectTypeId,
                'branchId'                                  => $branchId,
                'recpeitLegderIds'                          => $recpeitLegderIds,
                'paymentLegderIds'                          => $paymentLegderIds,
                'user_company_id'                           => $user_company_id,
                // 'previousYearInfo'                          => $previousYearsInfo,
                // opening data
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
                // closing balance
                'cbThisMonthCash'                           => $cbThisMonthCash,
                'cbThisMonthBank'                           => $cbThisMonthBank,
                'cbThisMonth'                               => $cbThisMonth,
                'cbThisYearCash'                            => $cbThisYearCash,
                'cbThisYearBank'                            => $cbThisYearBank,
                'cbThisYear'                                => $cbThisYear,
                'cbPreviousYearCash'                        => $cbPreviousYearCash,
                'cbPreviousYearBank'                        => $cbPreviousYearBank,
                'cbPreviousYear'                            => $cbPreviousYear,
                'cbThisPeriodCash'                          => $cbThisPeriodCash,
                'cbThisPeriodBank'                          => $cbThisPeriodBank,
                'cbThisPeriod'                              => $cbThisPeriod,
                // ledger wise data
                'ledgerWiseData'                            => $ledgerWiseData,
            );
            // dd($data);
        }
        else{
            $data = array(
                'user_company_id'=> $user_company_id,
                'projects'=>$projects,
                'projectSelected'=>$projectSelected,
                'projectTypes'=>$projectTypes,
                'branches'=>$branches,
                'branchSelected'=>$branchSelected,
                'voucherTypeSelected'=>$voucherTypeSelected,
                'roundUpSelected'=>$roundUpSelected,
                'depthLevel'=>$depthLevel,
                'depthLevelSelected'=>$depthLevelSelected,
                'withZeroSelected'=>$withZeroSelected,
                'searchMethodSelected'=>$searchMethodSelected,
                'fiscalYears'=>$fiscalYears,
                'fiscalYearSelected'=>$fiscalYearSelected,
                'dateFromSelected'=>$dateFromSelected,
                'dateToSelected'=>$dateToSelected,
                'firstRequest'=>$firstRequest,
                'projectTypeSelected'=>$projectTypeSelected,

            );

        }

        return view('accounting.reports.receiptPaymentStatement',$data);

    }



}		//End AccLedgerReportsController
