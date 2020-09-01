<?php

namespace App\Http\Controllers\accounting\reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use App\Http\Controllers\home\AccBranchStatusController;
use App\Http\Requests;
use App\accounting\AddVoucher;
use App\accounting\AddVoucherDetails;
use App\accounting\AddLedger;
use App\accounting\AddVoucherType;
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;
use App\gnr\GnrBranch;
use App\Service\EasyCode;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;


class AccCashFlowStatementController extends Controller{

    public function cashFlowStatementView(Request $request) {

        $today = Carbon::now()->format('Y-m-d');
        $projects = DB::table('gnr_project')->pluck('name','id')->toarray();
        $branches = DB::table('gnr_branch')
                    ->orderBy('branchCode')
                    ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                    ->toArray();
        $branchLists = array(0=>"All")+$branches;
        $fiscalYears = DB::table('gnr_fiscal_year')->select('name','id')->orderBy('id', 'desc')->get();
        $currentFiscalYear = DB::table('gnr_fiscal_year')
                            ->where('fyStartDate', '<=', $today)
                            ->where('fyEndDate', '>=', $today)
                            ->select('id','name', 'fyStartDate', 'fyEndDate')
                            ->first();

        $accCashFlowStatementArr = array(
            'projects'            => $projects,
            'branchLists'         => $branchLists,
            'fiscalYears'         => $fiscalYears,
            'currentFiscalYear'   => $currentFiscalYear,
        );
        // dd($accCashFlowStatementArr);

        return view('accounting.reports.cashFlowStatement.viewCashflowStatement',$accCashFlowStatementArr);
    }

    public function cashFlowStatementProjectType(Request $request) {

        $projectTypes = DB::table('gnr_project_type')->select('name','id')
                        ->where('projectId',$request->projectId)
                        ->get();

        return response()->json($projectTypes);
    }

    public function checkLedgerAccountType($ledgerId){

        $accountTypeIdArr = DB::table('acc_account_type')->pluck('id')->toArray();
        $accountLedgerType = DB::table('acc_account_ledger')->where('id', $ledgerId)->value('accountTypeId');
        $positiveTypeArr = array(1,2,3,4,5,13);
        $negetiveTypeArr = array_values(array_diff($accountTypeIdArr, $positiveTypeArr));

        if (in_array($accountLedgerType, $positiveTypeArr)) {
            return true;
        }
        elseif (in_array($accountLedgerType, $negetiveTypeArr)) {
            return false;
        }
    }

    public function finalLevelLedgers($firstChildId){

        $grandChildrenLedgers = DB::table('acc_account_ledger')
        ->select('id', 'code', 'isGroupHead')
        ->where('parentId', $firstChildId)
        ->orderBy('ordering', 'asc')
        ->get();
        // dd($grandChildrenLedgers);

        foreach ($grandChildrenLedgers as $key => $grandChild) {

            if ($grandChild->isGroupHead == 0) {
                $finalLedgers = $grandChildrenLedgers->pluck('id')->toArray();
                // dd($finalLedgers);
            }
            elseif ($grandChild->isGroupHead == 1) {

                $lastGrandChildrenLedgers = DB::table('acc_account_ledger')
                ->select('id', 'code', 'isGroupHead')
                ->where('parentId', $grandChild->id)
                ->orderBy('ordering', 'asc')
                ->get();
                // dd($lastGrandChildrenLedgers);

                foreach ($lastGrandChildrenLedgers as $key => $lastGrandChild) {

                    if ($lastGrandChild->isGroupHead == 0) {
                        $finalLedgers = $lastGrandChildrenLedgers->pluck('id')->toArray();
                        // dd($finalLedgers);
                    }
                    elseif ($lastGrandChild->isGroupHead == 1) {
                        $finalGrandChildrenLedgers = DB::table('acc_account_ledger')
                        ->select('id', 'code', 'isGroupHead')
                        ->where('parentId', $lastGrandChild->id)
                        ->orderBy('ordering', 'asc')
                        ->get();
                        // dd($finalGrandChildrenLedgers);

                        $finalLedgers = $finalGrandChildrenLedgers->pluck('id')->toArray();
                    }
                }
            }
        }
        // dd($finalLedgers);

        return $finalLedgers;
    }

    public function openingBalance($ledger, $date, $project, $projectType, $branch){
        $openingBalanceInfo = DB::table('acc_opening_balance')
                        ->where('ledgerId', $ledger)
                        ->where('openingDate', $date)
                        ->get();
                        // dd($openingBalanceInfo);

        // filter project
        if ((int)$project > 0) {
            $openingBalanceInfo = $openingBalanceInfo->where('projectId', $project);
        }

        //filter project type
        if ((int)$projectType > 0) {
            $openingBalanceInfo = $openingBalanceInfo->where('projectTypeId', $projectType);
        }

        // filter branch
        if ((int)$branch > 0) {
            $openingBalanceInfo = $openingBalanceInfo->where('branchId', $branch);
        }
        // dd($openingBalanceInfo);

        $openingDebitAmount = $openingBalanceInfo->sum('debitAmount');
        $openingCreditAmount = $openingBalanceInfo->sum('creditAmount');
        // dd($openingDebitAmount);

        $data = array(
            'debit' => $openingDebitAmount,
            'credit' => $openingCreditAmount,
        );

        return $data;
    }

    public function cashFlowStatementLoadTable(Request $request) {

        $user_company_id = Auth::user()->company_id_fk;
        $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
        $branchDate = DB::table('acc_day_end')->where('branchIdFk', Auth::user()->branchId)->max('date');
        $today = $branchDate;
        // $today = Carbon::now()->format('Y-m-d');
        $currentFiscalYear = DB::table('gnr_fiscal_year')
                            ->where('fyStartDate', '<=', $today)
                            ->where('fyEndDate', '>=', $today)
                            ->select('id','name', 'fyStartDate', 'fyEndDate')
                            ->first();

        // collecting requests
        // dd($request->all());
        $project = $request->filProject;
        $projectType = $request->filProjectType;
        $branch = $request->filBranch;
        $roundUp = $request->roundUp;
        $withZero = $request->withZero;
        $searchType = (int) $request->searchMethod;
        $fiscalId = (int) $request->fiscalYearId;
        $dateTo = $request->dateTo;
        // dd($projectType);

        if($searchType == 1){

            $fiscalId = $fiscalId;
            $dateFrom = DB::table('gnr_fiscal_year')->where('id', $fiscalId)->value('fyStartDate');
            if($fiscalId == $currentFiscalYear->id){
                $dateTo = $branchDate;
            }
            else {
                $dateTo = DB::table('gnr_fiscal_year')->where('id', $fiscalId)->value('fyEndDate');
            }

        }
        elseif($searchType == 2){

            $fiscalId = $currentFiscalYear->id;
            $dateFrom = $currentFiscalYear->fyStartDate;

            if(!$dateTo){
                $dateTo = $today;
            }
            else {
                $dateTo = Carbon::parse($dateTo)->format('Y-m-d');
            }
        }

        if($fiscalId == 1) {
            $fiscalId2 = $fiscalId;
        }
        else {
            $fiscalId2 = $fiscalId - 1;
        }

        $fiscalYearsSelected1 = DB::table('gnr_fiscal_year')->where('id',$fiscalId)->select('name','id')->first();
        $fiscalYearsSelected2 = DB::table('gnr_fiscal_year')->where('id',$fiscalId2)->select('name','id')->first();
        $projectName = DB::table('gnr_project')->where('id', $project)->value('name');
        if ((int)$projectType == 0) {
            $projectTypeName = 'All';
        } elseif ((int)$projectType > 0) {
            $projectTypeName = DB::table('gnr_project_type')->where('id', $projectType)->value('name');
        }
        if ((int)$branch == 0) {
            $branchName = 'All';
        } elseif ((int)$branch > 0) {
            $branchName = DB::table('gnr_branch')->where('id', $branch)
                        ->value(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name)"));
        }

        $cashFlowStatementLoadTableArr = array(
            'company'                 => $company,
            'fiscalId'                => $fiscalId,
            'dateFrom'                => $dateFrom,
            'dateTo'                  => $dateTo,
            'projectName'             => $projectName,
            'projectType'             => $projectTypeName,
            'branchName'              => $branchName,
            'fiscalYearsSelected1'    => $fiscalYearsSelected1,
            'fiscalYearsSelected2'    => $fiscalYearsSelected2
        );
        // dd($cashFlowStatementLoadTableArr);

        $previousYearStartDate = Carbon::parse($dateFrom)->subYear(1)->format('Y-m-d');
        $previousYearEndDate = DB::table('gnr_fiscal_year')->where('fyStartDate',$previousYearStartDate)->value('fyEndDate');
        $prePreviousYearEndDate = Carbon::parse($previousYearStartDate)->subDays(1)->format('Y-m-d');
        $thisMonthStart = Carbon::parse($dateTo)->startOfmonth()->format('Y-m-d');
        $lastOpeningDate = $previousYearEndDate;
        $lastCommonMonthEndDate = DB::table('acc_month_end')->max('date');
        $branchIdArr = DB::table('gnr_branch')->pluck('id')->toArray();
        // dd($branchIdArr);
        foreach ($branchIdArr as $key => $branchId) {

            $lastMonthEndDate = DB::table('acc_month_end')->where('branchIdFk', $branchId)->max('date');

            if ($lastMonthEndDate >= $lastCommonMonthEndDate) {
                $lastCommonMonthEndDate = $lastCommonMonthEndDate;
            }
            elseif($lastMonthEndDate < $lastCommonMonthEndDate) {
                $lastCommonMonthEndDate = $lastMonthEndDate;
            }

        }
        // dd($lastCommonMonthEndDate);

        // ledgers collection
        $ledgersCollection = DB::table('acc_account_ledger')
                            ->where('companyIdFk', $user_company_id)
                            ->select('id', 'name', 'code', 'accountTypeId', 'level')
                            ->get();
                            // dd($ledgersCollection);

        // vouchers collection
        // $voucherInfo = DB::table('acc_voucher')
        //                 ->join('acc_voucher_details','acc_voucher_details.voucherId', '=', 'acc_voucher.id')
        //                 ->where('companyId', $user_company_id)
        //                 // ->where('projectId', $project)
        //                 // ->where('branchId', $branch)
        //                 // ->where('projectTypeId', $projectType)
        //                 // ->where('voucherDate', '<=', $dateTo)
        //                 // ->where('voucherDate', '>=', $dateFrom)
        //                 ->select(
        //                     'acc_voucher_details.debitAcc',
        //                     'acc_voucher_details.creditAcc',
        //                     'acc_voucher_details.amount',
        //                     'acc_voucher.projectId',
        //                     'acc_voucher.branchId',
        //                     'acc_voucher.projectTypeId',
        //                     'acc_voucher.voucherTypeId',
        //                     'acc_voucher.voucherDate'
        //                     )
        //                 ->get();
                        // dd($voucherInfo);
            // opening balance
            $lastOpeningInfo = DB::table('acc_opening_balance')
                                ->where('companyIdFk', $user_company_id)
                                // ->where('ledgerId', $ledger->id)
                                // ->whereIn('branchId', $branchIdArr)
                                ->where('projectId', $project)
                                ->where('openingDate', $lastOpeningDate)
                                ->select('ledgerId', 'projectTypeId', 'branchId', 'debitAmount', 'creditAmount')
                                ->get();
                                // dd($lastOpeningInfo);

        $monthEndDatas = DB::table('acc_month_end_balance')
                        // ->where('ledgerId', $ledger->id)
                        ->where('companyIdFk', $user_company_id)
                        ->where('projectId', $project)
                        ->where('monthEndDate', '>=', $previousYearStartDate)
                        ->where('monthEndDate', '<=', $lastCommonMonthEndDate)
                        ->where('monthEndDate', '<=', $dateTo)
                        ->select('ledgerId', 'projectTypeId', 'branchId', 'debitAmount', 'creditAmount', 'monthEndDate')
                        ->get();
                        // dd($monthEndDatas);

        // filter project
        // if ((int)$project > 0) {
        //     // $voucherInfo = $voucherInfo->where('projectId', $project);
        // }

        //filter project type
        if ((int)$projectType > 0) {
            // $voucherInfo = $voucherInfo->where('projectTypeId', $projectType);
            $monthEndDatas = $monthEndDatas->where('projectTypeId', $projectType);
        }

        // filter branch
        if ((int)$branch > 0) {
            // $voucherInfo = $voucherInfo->where('branchId', $branch);
            $monthEndDatas = $monthEndDatas->where('branchId', $branch);
        }
        // dd($monthEndDate);

        // $currentYearVouchers = $voucherInfo->where('voucherDate', '<=', $dateTo)->where('voucherDate', '>=', $dateFrom);
        // $previousYearVouchers = $voucherInfo->where('voucherDate', '<=', $previousYearEndDate);
        //                         // ->where('voucherDate', '>=', $previousYearStartDate);
        // $thisMonthVouchers = $voucherInfo->where('voucherDate', '<=', $dateTo)->where('voucherDate', '>=', $thisMonthStart);
        // $cumulativeVouchers = $voucherInfo;

        $currentYearData = $monthEndDatas->where('monthEndDate', '<=', $dateTo)->where('monthEndDate', '>', $lastOpeningDate);
        $previousYearData = $monthEndDatas->where('monthEndDate', '<=', $previousYearEndDate)->where('monthEndDate', '>=', $previousYearStartDate);
                                // ->where('voucherDate', '>=', $previousYearStartDate);
        $thisMonthData = $monthEndDatas->where('monthEndDate', '<=', $dateTo)->where('monthEndDate', '>=', $thisMonthStart);
        // $cumulativeData = $monthEndDatas;
        // dd($thisMonthStart);

        // surplus calculation
        $incomeLedgers = $ledgersCollection->where('accountTypeId', 12)->where('level', 5)->pluck('id')->toArray();
        $expensesLedgers = $ledgersCollection->where('accountTypeId', 13)->where('level', 5)->pluck('id')->toArray();
        // dd($incomeLedgers);

        // foreach ($incomeLedgers as $key => $ledgers) {

            // current year
            $cyIncomeDebitAmount  = $currentYearData->whereIn('ledgerId', $incomeLedgers)->sum('debitAmount');
            $cyIncomeCreditAmount = $currentYearData->whereIn('ledgerId', $incomeLedgers)->sum('creditAmount');
            $cyIncomeBalanceArr = $cyIncomeCreditAmount - $cyIncomeDebitAmount;
            // previous year
            $pyIncomeDebitAmount  = $previousYearData->whereIn('ledgerId', $incomeLedgers)->sum('debitAmount');
            $pyIncomeCreditAmount = $previousYearData->whereIn('ledgerId', $incomeLedgers)->sum('creditAmount');
            $pyIncomeBalanceArr = $pyIncomeCreditAmount - $pyIncomeDebitAmount;
            // this month
            $thisMonthIncomeDebitAmount  = $thisMonthData->whereIn('ledgerId', $incomeLedgers)->sum('debitAmount');
            $thisMonthIncomeCreditAmount = $thisMonthData->whereIn('ledgerId', $incomeLedgers)->sum('creditAmount');
            $thisMonthIncomeBalanceArr = $thisMonthIncomeCreditAmount - $thisMonthIncomeDebitAmount;
            // cumulative
            $cumulativeIncomeDebitAmount  = $lastOpeningInfo->whereIn('ledgerId', $incomeLedgers)->sum('debitAmount') + $cyIncomeDebitAmount;
            $cumulativeIncomeCreditAmount = $lastOpeningInfo->whereIn('ledgerId', $incomeLedgers)->sum('creditAmount') + $cyIncomeCreditAmount;
            $cumulativeIncomeBalanceArr = $cumulativeIncomeCreditAmount - $cumulativeIncomeDebitAmount;
        // }
        // dd($cyIncomeBalanceArr);

        // foreach ($expensesLedgers as $key => $ledgers) {

            // current year
            $cyExpensesDebitAmount  = $currentYearData->whereIn('ledgerId', $expensesLedgers)->sum('debitAmount');
            $cyExpensesCreditAmount = $currentYearData->whereIn('ledgerId', $expensesLedgers)->sum('creditAmount');
            $cyExpensesBalanceArr = $cyExpensesCreditAmount - $cyExpensesDebitAmount;
            // previous year
            $pyExpensesDebitAmount  = $previousYearData->whereIn('ledgerId', $expensesLedgers)->sum('debitAmount');
            $pyExpensesCreditAmount = $previousYearData->whereIn('ledgerId', $expensesLedgers)->sum('creditAmount');
            $pyExpensesBalanceArr = $pyExpensesCreditAmount - $pyExpensesDebitAmount;
            // this month
            $thisMonthExpensesDebitAmount  = $thisMonthData->whereIn('ledgerId', $expensesLedgers)->sum('debitAmount');
            $thisMonthExpensesCreditAmount = $thisMonthData->whereIn('ledgerId', $expensesLedgers)->sum('creditAmount');
            $thisMonthExpensesBalanceArr = $thisMonthExpensesCreditAmount - $thisMonthExpensesDebitAmount;
            // cumulative
            $cumulativeExpensesDebitAmount  = $lastOpeningInfo->whereIn('ledgerId', $expensesLedgers)->sum('debitAmount') + $cyIncomeDebitAmount;
            $cumulativeExpensesCreditAmount = $lastOpeningInfo->whereIn('ledgerId', $expensesLedgers)->sum('creditAmount') + $cyIncomeCreditAmount;
            $cumulativeExpensesBalanceArr = $cumulativeExpensesCreditAmount - $cumulativeExpensesDebitAmount;
        // }
        // dd($cyExpensesBalanceArr);

        $cySurplusAmount = $cyIncomeBalanceArr - $cyExpensesBalanceArr;
        $pySurplusAmount = $pyIncomeBalanceArr - $pyExpensesBalanceArr;
        $thisMonthSurplusAmount = $thisMonthIncomeBalanceArr - $thisMonthExpensesBalanceArr;
        $cumulativeSurplusAmount = $cumulativeIncomeBalanceArr - $cumulativeExpensesBalanceArr;
        // $cySurplusAmount = array_sum($cyIncomeBalanceArr) - array_sum($cyExpensesBalanceArr);
        // $pySurplusAmount = array_sum($pyIncomeBalanceArr) - array_sum($pyExpensesBalanceArr);
        // $thisMonthSurplusAmount = array_sum($thisMonthIncomeBalanceArr) - array_sum($thisMonthExpensesBalanceArr);
        // $cumulativeSurplusAmount = array_sum($cumulativeIncomeBalanceArr) - array_sum($cumulativeExpensesBalanceArr);
        // dd($cySurplusAmount);

        // non cash expenses calculations
        $nonCashItemsExpensesLedgersArr = array('55000', '56000', '51000', '52000', '53000', '57000');
        $nonCashExpensesFinalLevelLedgers = [];
        
        foreach ($nonCashItemsExpensesLedgersArr as $key => $value) {

            // $ledgers = $ledgersCollection->whereIn('code', $nonCashItemsExpensesLedgersArr)->pluck('id')->toArray();
            $ledgers = $ledgersCollection->where('code', $value)->pluck('id')->toArray();
            $childrenLedgers = DB::table('acc_account_ledger')
                                ->select('id', 'code','isGroupHead', 'level')
                                ->where('code', '!=', $value)
                                // ->whereIn('code', '!=', $nonCashItemsExpensesLedgersArr)
                                ->whereIn('parentId', $ledgers)
                                ->orderBy('ordering', 'asc')
                                ->get();
                                // dd($ledgers);
            // $nonCashExpensesLedgers = $ledgersCollection->whereIn('code', $value)

            foreach ($childrenLedgers as $key => $child) {

                if ($child->isGroupHead == 0) {
                    $finalLedgers = $childrenLedgers->pluck('id')->toarray();
                }
                elseif($child->isGroupHead == 1) {
                    $finalLedgers = $this->finalLevelLedgers($child->id);
                }

                array_push($nonCashExpensesFinalLevelLedgers, $finalLedgers);
            }
        }
        dd($nonCashExpensesFinalLevelLedgers);

        foreach ($nonCashItemsExpensesLedgersArr as $key => $value) {

            // $ledgers = $ledgersCollection->whereIn('code', $nonCashItemsExpensesLedgersArr)->pluck('id')->toArray();
            $ledgers = $ledgersCollection->where('code', $value)->pluck('id')->toArray();
            $childrenLedgers = DB::table('acc_account_ledger')
                                ->select('id', 'code','isGroupHead', 'level')
                                ->where('code', '!=', $value)
                                // ->whereIn('code', '!=', $nonCashItemsExpensesLedgersArr)
                                ->whereIn('parentId', $ledgers)
                                ->orderBy('ordering', 'asc')
                                ->get();
                                // dd($ledgers);
            // $nonCashExpensesLedgers = $ledgersCollection->whereIn('code', $value)

            foreach ($childrenLedgers as $key => $child) {

                if ($child->isGroupHead == 0) {
                    $nonCashExpensesLedgers = $childrenLedgers->pluck('id')->toarray();
                }
                elseif($child->isGroupHead == 1) {
                    $nonCashExpensesLedgers = $this->finalLevelLedgers($child->id);
                }
                dd($nonCashExpensesLedgers);


                if($nonCashExpensesLedgers){
                    foreach ($nonCashExpensesLedgers as $key => $ledger) {

                        // current year
                        if($currentYearVouchers->count() > 0){
                            $cyNonCashExpensesDebitAmount = $currentYearVouchers->where('debitAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                            $cyNonCashExpensesCreditAmount = $currentYearVouchers->where('creditAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                        } else {
                            $obCYInfo = $this->openingBalance($ledger, $previousYearEndDate, $project, $projectType, $branch);
                            $cyNonCashExpensesDebitAmount = $obCYInfo['debit'];
                            $cyNonCashExpensesCreditAmount = $obCYInfo['credit'];
                        }

                        $cyNonCashExpensesBalance[] = $cyNonCashExpensesDebitAmount - $cyNonCashExpensesCreditAmount;

                        // previous year
                        if($previousYearVouchers->count() > 0){
                            $pyNonCashExpensesDebitAmount = $previousYearVouchers->where('debitAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                            $pyNonCashExpensesCreditAmount = $previousYearVouchers->where('creditAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                        } else {
                            $obPYInfo = $this->openingBalance($ledger, $prePreviousYearEndDate, $project, $projectType, $branch);
                            $pyNonCashExpensesDebitAmount = $obPYInfo['debit'];
                            $pyNonCashExpensesCreditAmount = $obPYInfo['credit'];
                        }

                        $pyNonCashExpensesBalance[] = $pyNonCashExpensesDebitAmount - $pyNonCashExpensesCreditAmount;

                        // this month
                        $thisMonthNonCashExpensesDebitAmount  = $thisMonthVouchers->where('debitAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                        $thisMonthNonCashExpensesCreditAmount = $thisMonthVouchers->where('creditAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                        $thisMonthNonCashExpensesBalance[] = $thisMonthNonCashExpensesDebitAmount - $thisMonthNonCashExpensesCreditAmount;
                        // cumulative
                        $cumulativeNonCashExpensesDebitAmount  = $cumulativeVouchers->where('debitAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                        $cumulativeNonCashExpensesCreditAmount = $cumulativeVouchers->where('creditAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                        $cumulativeNonCashExpensesBalance[] = $cumulativeNonCashExpensesDebitAmount - $cumulativeNonCashExpensesCreditAmount;
                    }
                }
                else {
                    $cyNonCashExpensesBalance = [];
                    $pyNonCashExpensesBalance = [];
                    $thisMonthNonCashExpensesBalance = [];
                    $cumulativeNonCashExpensesBalance = [];
                }
            }
            // dd($cyNonCashExpensesBalance);

            $nonCashItemsExpensesBalance[] = array(
                'code'                  => $value,
                'name'                  => $ledgersCollection->where('code', $value)->first()->name,
                'cyBalance'             => array_sum($cyNonCashExpensesBalance),
                'pyBalance'             => array_sum($pyNonCashExpensesBalance),
                'thisMonthBalance'      => array_sum($thisMonthNonCashExpensesBalance),
                'cumulativeBalance'     => array_sum($cumulativeNonCashExpensesBalance),
            );

            $cyNonCashExpensesBalance = [];
            $pyNonCashExpensesBalance = [];
            $thisMonthNonCashExpensesBalance = [];
            $cumulativeNonCashExpensesBalance = [];
        }
        // dd($nonCashItemsExpensesBalance);

        // non cash incomes calculations
        $nonCashItemIncomeLedgerCode = '40000';
        $ledgers = $ledgersCollection->where('code', $nonCashItemIncomeLedgerCode)->pluck('id')->toArray();
        $childrenLedgers = DB::table('acc_account_ledger')
                            ->select('id', 'code','isGroupHead')
                            ->where('code', '!=', $nonCashItemIncomeLedgerCode)
                            ->whereIn('parentId', $ledgers)
                            ->orderBy('ordering', 'asc')
                            ->get();
                            // dd($childrenLedgers);

        foreach ($childrenLedgers as $key => $child) {

            if ($child->isGroupHead == 0) {
                $nonCashIncomeLedgers = $childrenLedgers->pluck('id')->toArray();
                // dd($nonCashIncomeLedgers);
            }
            elseif($child->isGroupHead == 1) {
                $nonCashIncomeLedgers = $this->finalLevelLedgers($child->id);
            }
            // dd($nonCashIncomeLedgers);

            if ($nonCashIncomeLedgers) {
                foreach ($nonCashIncomeLedgers as $key => $ledger) {

                    // current year
                    if ($currentYearVouchers->count() > 0) {
                        $cyNonCashIncomeDebitAmount = $currentYearVouchers->where('debitAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                        $cyNonCashIncomeCreditAmount = $currentYearVouchers->where('creditAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                    }
                    else {
                        $obCYInfo = $this->openingBalance($ledger, $previousYearEndDate, $project, $projectType, $branch);
                        $cyNonCashIncomeDebitAmount = $obCYInfo['debit'];
                        $cyNonCashIncomeCreditAmount = $obCYInfo['credit'];
                    }

                    $cyNonCashIncomeBalance[] = $cyNonCashIncomeCreditAmount - $cyNonCashIncomeDebitAmount;

                    // previous year
                    if ($previousYearVouchers->count() > 0) {
                        $pyNonCashIncomeDebitAmount = $previousYearVouchers->where('debitAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                        $pyNonCashIncomeCreditAmount = $previousYearVouchers->where('creditAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                    }
                    else {
                        $obPYInfo = $this->openingBalance($ledger, $prePreviousYearEndDate, $project, $projectType, $branch);
                        $pyNonCashIncomeDebitAmount = $obPYInfo['debit'];
                        $pyNonCashIncomeCreditAmount = $obPYInfo['credit'];
                    }

                    $pyNonCashIncomeBalance[] = $pyNonCashIncomeCreditAmount - $pyNonCashIncomeDebitAmount;
                    // this month
                    $thisMonthNonCashIncomeDebitAmount  = $thisMonthVouchers->where('debitAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                    $thisMonthNonCashIncomeCreditAmount = $thisMonthVouchers->where('creditAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                    $thisMonthNonCashIncomeBalance[] = $thisMonthNonCashIncomeCreditAmount - $thisMonthNonCashIncomeDebitAmount;
                    // cumulative
                    $cumulativeNonCashIncomeDebitAmount  = $cumulativeVouchers->where('debitAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                    $cumulativeNonCashIncomeCreditAmount = $cumulativeVouchers->where('creditAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                    $cumulativeNonCashIncomeBalance[] = $cumulativeNonCashIncomeCreditAmount - $cumulativeNonCashIncomeDebitAmount;
                }
            }
            else {
                $cyNonCashIncomeBalance = [];
                $pyNonCashIncomeBalance = [];
                $thisMonthNonCashIncomeBalance = [];
                $cumulativeNonCashIncomeBalance = [];
            }

        }
        // dd($cyNonCashIncomeBalance);

        $nonCashItemsIncomeBalance = array(
            'code'                  => $nonCashItemIncomeLedgerCode,
            'name'                  => $ledgersCollection->where('code', $nonCashItemIncomeLedgerCode)->first()->name,
            'cyBalance'             => array_sum($cyNonCashIncomeBalance),
            'pyBalance'             => array_sum($pyNonCashIncomeBalance),
            'thisMonthBalance'      => array_sum($thisMonthNonCashIncomeBalance),
            'cumulativeBalance'     => array_sum($cumulativeNonCashIncomeBalance),
        );
        // dd($nonCashItemsIncomeBalance);

        // net cash operating calculations
        $netCashOperatingLedgersArr = array('14000', '26000', '16000');

        foreach ($netCashOperatingLedgersArr as $key => $value) {

            $ledgers = $ledgersCollection->where('code', $value)->pluck('id')->toArray();
            $childrenLedgers = DB::table('acc_account_ledger')
                                ->select('id', 'code','isGroupHead')
                                ->where('code', '!=', $value)
                                ->whereIn('parentId', $ledgers)
                                ->orderBy('ordering', 'asc')
                                ->get();
                                // dd($childrenLedgers);

            foreach ($childrenLedgers as $key => $child) {

                if ($child->isGroupHead == 0) {
                    $netCashOperatingLedgers = $childrenLedgers->pluck('id')->toarray();
                }
                elseif($child->isGroupHead == 1) {
                    $netCashOperatingLedgers = $this->finalLevelLedgers($child->id);
                }
                // dd($nonCashExpensesLedgers);

                if ($netCashOperatingLedgers) {
                    foreach ($netCashOperatingLedgers as $key => $ledger) {

                        // current year
                        if ($currentYearVouchers->count() > 0) {
                            $cyNetCashOperatingDebitAmount = $currentYearVouchers->where('debitAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                            $cyNetCashOperatingCreditAmount = $currentYearVouchers->where('creditAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                        } else {
                            $obCYInfo = $this->openingBalance($ledger, $previousYearEndDate, $project, $projectType, $branch);
                            $cyNetCashOperatingDebitAmount = $obCYInfo['debit'];
                            $cyNetCashOperatingCreditAmount = $obCYInfo['credit'];
                        }

                        // previous year
                        if ($previousYearVouchers->count() > 0) {
                            $pyNetCashOperatingDebitAmount = $previousYearVouchers->where('debitAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                            $pyNetCashOperatingCreditAmount = $previousYearVouchers->where('creditAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                        }
                        else {
                            $obPYInfo = $this->openingBalance($ledger, $prePreviousYearEndDate, $project, $projectType, $branch);
                            $pyNetCashOperatingDebitAmount = $previousYearVouchers->where('debitAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                            $pyNetCashOperatingCreditAmount = $previousYearVouchers->where('creditAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                        }

                        // this month
                        $thisMonthNetCashOperatingDebitAmount  = $thisMonthVouchers->where('debitAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                        $thisMonthNetCashOperatingCreditAmount = $thisMonthVouchers->where('creditAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');

                        // cumulative
                        $cumulativeNetCashOperatingDebitAmount  = $cumulativeVouchers->where('debitAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');
                        $cumulativeNetCashOperatingCreditAmount = $cumulativeVouchers->where('creditAcc', $ledger)->where('voucherTypeId', 3)->sum('amount');

                        // check ledger type
                        $ledgerType = $this->checkLedgerAccountType($ledger);
                        if ($ledgerType == true) {
                            $cyNetCashOperatingBalance[] = $cyNetCashOperatingDebitAmount - $cyNetCashOperatingCreditAmount;
                            $pyNetCashOperatingBalance[] = $pyNetCashOperatingDebitAmount - $pyNetCashOperatingCreditAmount;
                            $thisMonthNetCashOperatingBalance[] = $thisMonthNetCashOperatingDebitAmount - $thisMonthNetCashOperatingCreditAmount;
                            $cumulativeNetCashOperatingBalance[] = $cumulativeNetCashOperatingDebitAmount - $cumulativeNetCashOperatingCreditAmount;
                        }
                        elseif ($ledgerType == false) {
                            $cyNetCashOperatingBalance[] = $cyNetCashOperatingCreditAmount - $cyNetCashOperatingDebitAmount;
                            $pyNetCashOperatingBalance[] = $pyNetCashOperatingCreditAmount - $pyNetCashOperatingDebitAmount;
                            $thisMonthNetCashOperatingBalance[] = $thisMonthNetCashOperatingCreditAmount - $thisMonthNetCashOperatingDebitAmount;
                            $cumulativeNetCashOperatingBalance[] = $cumulativeNetCashOperatingCreditAmount - $cumulativeNetCashOperatingDebitAmount;
                        }
                    }
                }
                else {
                    $cyNetCashOperatingBalance = [];
                    $pyNetCashOperatingBalance = [];
                    $thisMonthNetCashOperatingBalance = [];
                    $cumulativeNetCashOperatingBalance = [];
                }
            }

            $netCashOperatingActivitiesBalance[] = array(
                'code'                  => $value,
                'name'                  => $ledgersCollection->where('code', $value)->first()->name,
                'cyBalance'             => array_sum($cyNetCashOperatingBalance),
                'pyBalance'             => array_sum($pyNetCashOperatingBalance),
                'thisMonthBalance'      => array_sum($thisMonthNetCashOperatingBalance),
                'cumulativeBalance'     => array_sum($cumulativeNetCashOperatingBalance)
            );

            $cyNetCashOperatingBalance = [];
            $pyNetCashOperatingBalance = [];
            $thisMonthNetCashOperatingBalance = [];
            $cumulativeNetCashOperatingBalance = [];
        }
        // dd($netCashOperatingActivitiesBalance);

        // net cash investing calculations
        $netCashInvestingLedgersArr = array('11000', '15000', '18000');

        foreach ($netCashInvestingLedgersArr as $key => $value) {

            $ledgers = $ledgersCollection->where('code', $value)->pluck('id')->toArray();
            $childrenLedgers = DB::table('acc_account_ledger')
                                ->select('id', 'code','isGroupHead')
                                ->where('code', '!=', $value)
                                ->whereIn('parentId', $ledgers)
                                ->orderBy('ordering', 'asc')
                                ->get();
                                // dd($childrenLedgers);

            foreach ($childrenLedgers as $key => $child) {

                if ($child->isGroupHead == 0) {
                    $netCashInvestingLedgers = $childrenLedgers->pluck('id')->toarray();
                }
                elseif($child->isGroupHead == 1) {
                    $netCashInvestingLedgers = $this->finalLevelLedgers($child->id);
                }
                // dd($netCashInvestingLedgers);

                if ($netCashInvestingLedgers) {
                    foreach ($netCashInvestingLedgers as $key => $ledger) {

                        // current year
                        if ($currentYearVouchers->count() > 0) {
                            $cyNetCashInvestingDebitAmount = $currentYearVouchers->where('debitAcc', $ledger)->whereIn('voucherTypeId', [1,2])->sum('amount');
                            $cyNetCashInvestingCreditAmount = $currentYearVouchers->where('creditAcc', $ledger)->whereIn('voucherTypeId', [1,2])->sum('amount');
                        }
                        else {
                            $obCYInfo = $this->openingBalance($ledger, $previousYearEndDate, $project, $projectType, $branch);
                            $cyNetCashInvestingDebitAmount = $obCYInfo['debit'];
                            $cyNetCashInvestingCreditAmount = $obCYInfo['credit'];
                        }

                        $cyNetCashInvestingBalance[] = $cyNetCashInvestingDebitAmount - $cyNetCashInvestingCreditAmount;

                        // previous year
                        if ($previousYearVouchers->count() > 0) {
                            $pyNetCashInvestingDebitAmount = $previousYearVouchers->where('debitAcc', $ledger)->whereIn('voucherTypeId', [1,2])->sum('amount');
                            $pyNetCashInvestingCreditAmount = $previousYearVouchers->where('creditAcc', $ledger)->whereIn('voucherTypeId', [1,2])->sum('amount');
                        }
                        else {
                            $obPYInfo = $this->openingBalance($ledger, $prePreviousYearEndDate, $project, $projectType, $branch);
                            $pyNetCashInvestingDebitAmount = $obPYInfo['debit'];
                            $pyNetCashInvestingCreditAmount = $obPYInfo['credit'];
                        }

                        $pyNetCashInvestingBalance[] = $pyNetCashInvestingDebitAmount - $pyNetCashInvestingCreditAmount;

                        // this month
                        $thisMonthNetCashInvestingDebitAmount  = $thisMonthVouchers->where('debitAcc', $ledger)->whereIn('voucherTypeId', [1,2])->sum('amount');
                        $thisMonthNetCashInvestingCreditAmount = $thisMonthVouchers->where('creditAcc', $ledger)->whereIn('voucherTypeId', [1,2])->sum('amount');
                        $thisMonthNetCashInvestingBalance[] = $thisMonthNetCashInvestingDebitAmount - $thisMonthNetCashInvestingCreditAmount;
                        // cumulative
                        $cumulativeNetCashInvestingDebitAmount  = $cumulativeVouchers->where('debitAcc', $ledger)->whereIn('voucherTypeId', [1,2])->sum('amount');
                        $cumulativeNetCashInvestingCreditAmount = $cumulativeVouchers->where('creditAcc', $ledger)->whereIn('voucherTypeId', [1,2])->sum('amount');
                        $cumulativeNetCashInvestingBalance[] = $cumulativeNetCashInvestingDebitAmount - $cumulativeNetCashInvestingCreditAmount;
                    }
                }
                else {
                    $cyNetCashInvestingBalance = [];
                    $pyNetCashInvestingBalance = [];
                    $thisMonthNetCashInvestingBalance = [];
                    $cumulativeNetCashInvestingBalance = [];
                }
            }

            $netCashInvestingActivitiesBalance[] = array(
                'code'                  => $value,
                'name'                  => $ledgersCollection->where('code', $value)->first()->name,
                'cyBalance'             => array_sum($cyNetCashInvestingBalance),
                'pyBalance'             => array_sum($pyNetCashInvestingBalance),
                'thisMonthBalance'      => array_sum($thisMonthNetCashInvestingBalance),
                'cumulativeBalance'     => array_sum($cumulativeNetCashInvestingBalance)
            );

            $cyNetCashInvestingBalance = [];
            $pyNetCashInvestingBalance = [];
            $thisMonthNetCashInvestingBalance = [];
            $cumulativeNetCashInvestingBalance = [];
        }
        // dd($netCashInvestingActivitiesBalance);

        // net cash financing calculations
        $netCashFinancingLedgersArr = array('22000', '22700', '24000', '23500', '25000', '24700');

        foreach ($netCashFinancingLedgersArr as $key => $value) {

            $ledgers = $ledgersCollection->where('code', $value)->pluck('id')->toArray();
            $childrenLedgers = DB::table('acc_account_ledger')
                                ->select('id', 'code','isGroupHead')
                                ->where('code', '!=', $value)
                                ->whereIn('parentId', $ledgers)
                                ->orderBy('ordering', 'asc')
                                ->get();
                                // dd($childrenLedgers);

            foreach ($childrenLedgers as $key => $child) {

                if ($child->isGroupHead == 0) {
                    $netCashFinancingLedgers = $childrenLedgers->pluck('id')->toarray();
                }
                elseif($child->isGroupHead == 1) {
                    $netCashFinancingLedgers = $this->finalLevelLedgers($child->id);
                }
                // dd($netCashFinancingLedgers);

                if ($netCashFinancingLedgers) {
                    foreach ($netCashFinancingLedgers as $key => $ledger) {

                        // current year
                        if ($currentYearVouchers->count() > 0) {
                            $cyNetCashFinancingDebitAmount = $currentYearVouchers->where('debitAcc', $ledger)->whereIn('voucherTypeId', [1,2])->sum('amount');
                            $cyNetCashFinancingCreditAmount = $currentYearVouchers->where('creditAcc', $ledger)->whereIn('voucherTypeId', [1,2])->sum('amount');
                        }
                        else {
                            $obCYInfo = $this->openingBalance($ledger, $previousYearEndDate, $project, $projectType, $branch);
                            $cyNetCashFinancingDebitAmount = $obCYInfo['debit'];
                            $cyNetCashFinancingCreditAmount = $obCYInfo['credit'];
                        }

                        // previous year
                        if ($previousYearVouchers->count() > 0) {
                            $pyNetCashFinancingDebitAmount = $previousYearVouchers->where('debitAcc', $ledger)->whereIn('voucherTypeId', [1,2])->sum('amount');
                            $pyNetCashFinancingCreditAmount = $previousYearVouchers->where('creditAcc', $ledger)->whereIn('voucherTypeId', [1,2])->sum('amount');
                        }
                        else {
                            $obPYInfo = $this->openingBalance($ledger, $prePreviousYearEndDate, $project, $projectType, $branch);
                            $pyNetCashFinancingDebitAmount = $obPYInfo['debit'];
                            $pyNetCashFinancingCreditAmount = $obPYInfo['credit'];
                        }

                        // this month
                        $thisMonthNetCashFinancingDebitAmount  = $thisMonthVouchers->where('debitAcc', $ledger)->whereIn('voucherTypeId', [1,2])->sum('amount');
                        $thisMonthNetCashFinancingCreditAmount = $thisMonthVouchers->where('creditAcc', $ledger)->whereIn('voucherTypeId', [1,2])->sum('amount');
                        // cumulative
                        $cumulativeNetCashFinancingDebitAmount  = $cumulativeVouchers->where('debitAcc', $ledger)->whereIn('voucherTypeId', [1,2])->sum('amount');
                        $cumulativeNetCashFinancingCreditAmount = $cumulativeVouchers->where('creditAcc', $ledger)->whereIn('voucherTypeId', [1,2])->sum('amount');

                        // check ledger type
                        $ledgerType = $this->checkLedgerAccountType($ledger);
                        if ($ledgerType == true) {
                            $cyNetCashFinancingBalance[] = $cyNetCashFinancingDebitAmount - $cyNetCashFinancingCreditAmount;
                            $pyNetCashFinancingBalance[] = $pyNetCashFinancingDebitAmount - $pyNetCashFinancingCreditAmount;
                            $thisMonthNetCashFinancingBalance[] = $thisMonthNetCashFinancingDebitAmount - $thisMonthNetCashFinancingCreditAmount;
                            $cumulativeNetCashFinancingBalance[] = $cumulativeNetCashFinancingDebitAmount - $cumulativeNetCashFinancingCreditAmount;
                        }
                        elseif ($ledgerType == false) {
                            $cyNetCashFinancingBalance[] = $cyNetCashFinancingCreditAmount - $cyNetCashFinancingDebitAmount;
                            $pyNetCashFinancingBalance[] = $pyNetCashFinancingCreditAmount - $pyNetCashFinancingDebitAmount;
                            $thisMonthNetCashFinancingBalance[] = $thisMonthNetCashFinancingCreditAmount - $thisMonthNetCashFinancingDebitAmount;
                            $cumulativeNetCashFinancingBalance[] = $cumulativeNetCashFinancingCreditAmount - $cumulativeNetCashFinancingDebitAmount;
                        }
                    }
                }
                else {
                    $cyNetCashFinancingBalance = [];
                    $pyNetCashFinancingBalance = [];
                    $thisMonthNetCashFinancingBalance = [];
                    $cumulativeNetCashFinancingBalance = [];
                }
            }

            $netCashFinancingActivitiesBalance[] = array(
                'code'                  => $value,
                'name'                  => $ledgersCollection->where('code', $value)->first()->name,
                'cyBalance'             => array_sum($cyNetCashFinancingBalance),
                'pyBalance'             => array_sum($pyNetCashFinancingBalance),
                'thisMonthBalance'      => array_sum($thisMonthNetCashFinancingBalance),
                'cumulativeBalance'     => array_sum($cumulativeNetCashFinancingBalance)
            );

            $cyNetCashFinancingBalance = [];
            $pyNetCashFinancingBalance = [];
            $thisMonthNetCashFinancingBalance = [];
            $cumulativeNetCashFinancingBalance = [];
        }
        // dd($netCashFinancingActivitiesBalance);

        // cash and bank calculations
        $cashAndBankLegderCode = '19000';
        $ledgers = $ledgersCollection->where('code', $cashAndBankLegderCode)->pluck('id')->toArray();
        $childrenLedgers = DB::table('acc_account_ledger')
                            ->select('id', 'code','isGroupHead')
                            ->where('code', '!=', $cashAndBankLegderCode)
                            ->whereIn('parentId', $ledgers)
                            ->orderBy('ordering', 'asc')
                            ->get();
                            // dd($childrenLedgers);

        foreach ($childrenLedgers as $key => $child) {

            if ($child->isGroupHead == 0) {
                $cashAndBankLedgers = $childrenLedgers->pluck('id')->toarray();
            }
            elseif($child->isGroupHead == 1) {
                $cashAndBankLedgers = $this->finalLevelLedgers($child->id);
            }
            // dd($cashAndBankLedgers);

            if ($cashAndBankLedgers) {
                foreach ($cashAndBankLedgers as $key => $ledger) {



                    // current year
                    $obCYInfo = $this->openingBalance($ledger, $previousYearEndDate, $project, $projectType, $branch);
                    $cyCashAndBankDebitAmount = $currentYearVouchers->where('debitAcc', $ledger)->sum('amount') + $obCYInfo['debit'];
                    $cyCashAndBankCreditAmount = $currentYearVouchers->where('creditAcc', $ledger)->sum('amount') + $obCYInfo['credit'];
                    // $cyCashAndBankDebitAmount = $openingDebitAmount;
                    // $cyCashAndBankCreditAmount = $openingCreditAmount;
                    $cyCashAndBankBalance[] = $cyCashAndBankDebitAmount - $cyCashAndBankCreditAmount;
                    // previous year
                    $obPYInfo = $this->openingBalance($ledger, $prePreviousYearEndDate, $project, $projectType, $branch);
                    $pyCashAndBankDebitAmount = $previousYearVouchers->where('debitAcc', $ledger)->sum('amount') + $obPYInfo['debit'];
                    $pyCashAndBankCreditAmount = $previousYearVouchers->where('creditAcc', $ledger)->sum('amount') + $obPYInfo['credit'];
                    $pyCashAndBankBalance[] = $pyCashAndBankDebitAmount - $pyCashAndBankCreditAmount;
                    // this month
                    $thisMonthCashAndBankDebitAmount  = $thisMonthVouchers->where('debitAcc', $ledger)->sum('amount');
                    $thisMonthCashAndBankCreditAmount = $thisMonthVouchers->where('creditAcc', $ledger)->sum('amount');
                    $thisMonthCashAndBankBalance[] = $thisMonthCashAndBankDebitAmount - $thisMonthCashAndBankCreditAmount;
                    // cumulative
                    $cumulativeCashAndBankDebitAmount  = $cumulativeVouchers->where('debitAcc', $ledger)->sum('amount');
                    $cumulativeCashAndBankCreditAmount = $cumulativeVouchers->where('creditAcc', $ledger)->sum('amount');
                    $cumulativeCashAndBankBalance[] = $cumulativeCashAndBankDebitAmount - $cumulativeCashAndBankCreditAmount;
                }
            }
            else {
                $cyCashAndBankBalance = [];
                $pyCashAndBankBalance = [];
                $thisMonthCashAndBankBalance = [];
                $cumulativeCashAndBankBalance = [];
            }
        }

        $netCashAndBankBalance[] = array(
            'code'                  => $cashAndBankLegderCode,
            'name'                  => $ledgersCollection->where('code', $cashAndBankLegderCode)->first()->name,
            'cyBalance'             => array_sum($cyCashAndBankBalance),
            'pyBalance'             => array_sum($pyCashAndBankBalance),
            'thisMonthBalance'      => array_sum($thisMonthCashAndBankBalance),
            'cumulativeBalance'     => array_sum($cumulativeCashAndBankBalance)
        );

        $data = array(
            // 'dateTo'                                => $dateTo,
            'searchType'                            => $searchType,
            'withZero'                              => $withZero,
            'roundUp'                               => $roundUp,
            'cySurplusAmount'                       => $cySurplusAmount,
            'pySurplusAmount'                       => $pySurplusAmount,
            'thisMonthSurplusAmount'                => $thisMonthSurplusAmount,
            'cumulativeSurplusAmount'               => $cumulativeSurplusAmount,
            'cashFlowStatementLoadTableArr'         => $cashFlowStatementLoadTableArr,
            'nonCashItemsExpensesBalance'           => $nonCashItemsExpensesBalance,
            'nonCashItemsIncomeBalance'             => $nonCashItemsIncomeBalance,
            'netCashOperatingActivitiesBalance'     => $netCashOperatingActivitiesBalance,
            'netCashInvestingActivitiesBalance'     => $netCashInvestingActivitiesBalance,
            'netCashFinancingActivitiesBalance'     => $netCashFinancingActivitiesBalance,
            'netCashAndBankBalance'                 => $netCashAndBankBalance,
        );

        return view('accounting.reports.cashFlowStatement.viewCashflowStatementTable',$data);
    }

}
