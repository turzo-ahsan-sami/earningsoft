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
use App\Service\Service;


class AccCashFlowStatementController extends Controller{

    public function index(Request $request) {

        $today = Carbon::now()->format('Y-m-d');
        $fiscalYears = DB::table('gnr_fiscal_year')->select('name','id')->orderBy('id', 'desc')->get();
        $currentFiscalYear = DB::table('gnr_fiscal_year')
                            ->where('fyStartDate', '<=', $today)
                            ->where('fyEndDate', '>=', $today)
                            ->select('id','name', 'fyStartDate', 'fyEndDate')
                            ->first();

        $userBranchId=Auth::user()->branchId;

        /// Report Level
        $reportLevelList = array(
            'Branch'  =>  'Branch',
            'Area'    =>  'Area',
            'Zone'    =>  'Zone',
            'Region'  =>  'Region'
        );

        /// Branch
        $branchLists = DB::table('gnr_branch');

        if ($userBranchId != 1) {
            $branchLists = $branchLists->where('id', $userBranchId);
        }
        else {
            $branches = DB::table('gnr_branch')
                        ->orderBy('branchCode')
                        ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                        ->toArray();
            $branchLists = array("All"=>"All", 0=>"All Branches")+$branches;
        }
        // dd($branchLists);

        /// Area
        $areaList = DB::table('gnr_area')
                        ->select(DB::raw("CONCAT ( LPAD(code,3,0) ,' - ', name ) AS name"), 'id', 'branchId')
                        ->get();
        /// Zone
        $zoneList = DB::table('gnr_zone')
                        ->select(DB::raw("CONCAT ( LPAD(code,3,0) ,' - ', name ) AS name"), 'id', 'areaId')
                        ->get();
        /// Region
        $regionList = DB::table('gnr_region')
                        ->select(DB::raw("CONCAT ( LPAD(code,3,0) ,' - ', name ) AS name"), 'id', 'zoneId')
                        ->get();

        $projects = DB::table('gnr_project')->pluck('name','id')->toarray();

        $cashFlowStatementArr = array(
            'reportLevelList'                   => $reportLevelList,
            'areaList'                          => $areaList,
            'zoneList'                          => $zoneList,
            'regionList'                        => $regionList,
            'branchLists'                       => $branchLists,
            'userBranchId'                      => $userBranchId,
            'today'                             => Carbon::now()->format('d-m-Y'),
            'projects'                          => $projects,
            'fiscalYears'                       => $fiscalYears,
            'currentFiscalYear'                 => $currentFiscalYear,
        );
        // dd($accCashFlowStatementArr);

        return view('accounting.reports.cashFlowStatementViews.viewCashFlowStatementFilterForm', $cashFlowStatementArr);
    }

    public static function cashFlowStatementProjectType(Request $request) {

        $projectTypes = DB::table('gnr_project_type')->select('name','id')
                        ->where('projectId',$request->projectId)
                        ->get();

        $branches = DB::table('gnr_branch')
                        ->where('projectId',$request->projectId)
                        ->orderBy('branchCode')
                        ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                        ->toArray();
        $projectTypesNBranches = array(
            'projectTypes'  => $projectTypes,
            'branches'      => $branches
        );

        return response()->json($projectTypesNBranches);
    }

    public function cashFlowStatementLoadTable(Request $request) {

        $user_company_id = Auth::user()->company_id_fk;
        $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
        $dayEnds = DB::table('acc_day_end')->get();
        $branchDayEnds = collect();
        foreach ($dayEnds as $key => $item) {
            // if ($request->filBranch != 1) {
                if ($item->branchIdFk == $request->filBranch) {
                    $branchDayEnds->push($item);
                }
                else {
                    if ($item->branchIdFk == Auth::user()->branchId) {
                        $branchDayEnds->push($item);
                    }
                }
            // }

        }

        $branchDate = $branchDayEnds->max('date');

        if ($branchDate) {
            $today = $branchDate;
        } else {
            $today = Carbon::now()->format('Y-m-d');
        }

        $currentFiscalYear = DB::table('gnr_fiscal_year')
                            ->where('fyStartDate', '<=', $today)
                            ->where('fyEndDate', '>=', $today)
                            ->select('id','name', 'fyStartDate', 'fyEndDate')
                            ->first();

        // collecting request data
        // dd($request->all());
        $project = $request->filProject;
        $projectType = $request->filProjectType;
        $roundUp = $request->roundUp;
        $withZero = $request->withZero;
        $depthLevel = $request->depthLevel;
        $searchType = (int) $request->searchMethod;
        $fiscalYearId = (int) $request->fiscalYearId;
        $reportLevel = $request->filReportLevel;
        $area = $request->filArea;
        $zone = $request->filZone;
        $region = $request->filRegion;
        if (Auth::user()->branchId != 1) {
            $branch = Auth::user()->branchId;
        }
        else {
            $branch = $request->filBranch;
        }
        // dd($branch);

        $branchIdArr = Service::getFilteredBranchIds($project, $reportLevel, $branch, $area, $zone, $region);
        // dd($branchIdArr);

        // collecting date range
        // common month end date
        $monthEndUnprocessedBranchesByMonth = [];
        $branchNotStarted = [];
        $lastMonthEndDate = '';
        $lastCommonMonthEndDate = DB::table('acc_month_end')->max('date');
        // dd($lastCommonMonthEndDate);
        // branch loop
        foreach ($branchIdArr as $key => $branchId) {

            $aisStartDate = DB::table('gnr_branch')->where('id', $branchId)->value('aisStartDate');

            if ($aisStartDate <= $lastCommonMonthEndDate) {
                $lastMonthEndDate = DB::table('acc_month_end')->where('branchIdFk', $branchId)->max('date');
            }
            else {
                $branchNotStarted[] = $branchId;
            }

            if ($lastMonthEndDate != null) {
                if ($lastMonthEndDate >= $lastCommonMonthEndDate) {
                    $lastCommonMonthEndDate = $lastCommonMonthEndDate;
                }
                elseif($lastMonthEndDate < $lastCommonMonthEndDate) {
                    $lastCommonMonthEndDate = $lastMonthEndDate;
                }
            }

        }
        // dd($lastCommonMonthEndDate);
        // fiscal year search
        if($searchType == 1){

            $dateFrom = DB::table('gnr_fiscal_year')->where('id', $fiscalYearId)->value('fyStartDate');
            $dateTo = DB::table('gnr_fiscal_year')->where('id', $fiscalYearId)->value('fyEndDate');
            $lastMonthEndDateAuth = DB::table('acc_month_end')->where('branchIdFk', Auth::user()->branchId)->max('date');

            if($fiscalYearId == $currentFiscalYear->id){
                // all branch
                if ($request->filBranch == 'All' || $request->filBranch == 0) {
                    // collecting months unprocessed for all branches
                    if ($lastCommonMonthEndDate != $dateTo) {

                        $startDate = strtotime($lastCommonMonthEndDate);
                        $endDate = strtotime($lastMonthEndDateAuth);

                        $current = $startDate;
                        $monthsArr = [];

                        while($current < $endDate ){
                            $value = Carbon::parse(date('Y-m-d', $current))->endOfMonth()->format('Y-m-d');
                            if($lastCommonMonthEndDate != $value){
                                $monthsArr[] = Carbon::parse(date('Y-m-d', $current))->endOfMonth();
                            }
                            $next = date('Y-M-01', $current) . "+1 month";
                            $current = strtotime($next);
                        }

                        // month ends remaining branches
                        foreach ($monthsArr as $key => $month) {
                            $monthEndedBranches = DB::table('acc_month_end')
                                                ->where('date', $month->format('Y-m-d'))->pluck('branchIdFk')
                                                ->toArray();

                            $monthEndUnprocessedBranches = DB::table('gnr_branch')
                                                        ->whereIn('id', $branchIdArr)
                                                        ->whereNotIn('id', $monthEndedBranches)
                                                        ->whereNotIn('id', $branchNotStarted)
                                                        ->orderBy('branchCode')
                                                        ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', TRIM(REPLACE(name, '\t', ''))) AS nameWithCode"))
                                                        ->toArray();

                            $monthEndUnprocessedBranchesByMonth[$month->format('M, Y')] = $monthEndUnprocessedBranches;
                        }
                    }

                    $dateTo = $lastCommonMonthEndDate;

                }
                // single branch
                else {
                    $lastMonthEndDate = DB::table('acc_month_end')->where('branchIdFk', $branch)->max('date');

                    if ($lastMonthEndDate != null) {
                        $dateTo = $lastMonthEndDate;
                    }
                    else {
                        $dateTo = $branchDate;
                    }
                }

            }

        }
        // current year search
        elseif($searchType == 2){

            $dateTo = Carbon::parse($request->dateTo)->format('Y-m-d');
            $fiscalYear = DB::table('gnr_fiscal_year')->where('fyStartDate', '<=', $dateTo)->where('fyEndDate','>=', $dateTo)->first();
            $fiscalYearId = $fiscalYear->id;
            $dateFrom = $fiscalYear->fyStartDate;
            $thisMonthStartDate = Carbon::parse($dateTo)->startOfMonth()->format('Y-m-d');
        }

        $thisFiscalYearInfo = DB::table('gnr_fiscal_year')
                    ->where('fyStartDate', '<=', $dateFrom)
                    ->where('fyEndDate', '>=', $dateFrom)
                    ->select('id','name', 'fyStartDate', 'fyEndDate')
                    ->first();

        $previousFiscalYear = DB::table('gnr_fiscal_year')
                                ->where('fyStartDate', '<=', Carbon::parse($dateFrom)->subyear()->format('Y-m-d'))
                                ->where('fyEndDate','>=', Carbon::parse($dateFrom)->subyear()->format('Y-m-d'))
                                ->first();

        $yearBeforePreviousYear = DB::table('gnr_fiscal_year')
                                ->where('fyStartDate', '<=', Carbon::parse($previousFiscalYear->fyStartDate)->subyear()->format('Y-m-d'))
                                ->where('fyEndDate','>=', Carbon::parse($previousFiscalYear->fyStartDate)->subyear()->format('Y-m-d'))
                                ->first();

        $thisFiscalYearId = $thisFiscalYearInfo->id;
        $previousFiscalYearId = $previousFiscalYear->id;
        $yearBeforePreviousYearId = $yearBeforePreviousYear->id;
        // dd($fiscalYearId, $previousFiscalYearId, $yearBeforePreviousYearId);

        $lastOpeningDate = Carbon::parse($thisFiscalYearInfo->fyStartDate)->subdays(1)->format('Y-m-d');

        $projectName = DB::table('gnr_project')->where('id', $project)->value('name');
        if ((int)$projectType == 0) {
            $projectTypeName = 'All';
        } elseif ((int)$projectType > 0) {
            $projectTypeName = DB::table('gnr_project_type')->where('id', $projectType)->value('name');
        }

        // show branch name in view
        if ($reportLevel == "Branch") {
            if ($branch == 'All') {
                $branchName = 'All';
            }
            elseif ((int)$branch == 0) {
                $branchName = 'All Branches';
            }
            else {
                $branchName = DB::table('gnr_branch')->where('id', $branch)->value(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', TRIM(REPLACE(name, '\t', '')))"));
            }
        }
        else {
            $SelectedBranchArr = DB::table('gnr_branch')
                                ->whereIn('id', $branchIdArr)->orderBy('branchCode')
                                ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', TRIM(REPLACE(name, '\t', ''))) as name"))
                                ->toArray();

            $branchName = implode(", ", $SelectedBranchArr);
        }

        // requested info array
        $cashFlowStatementLoadTableArr = array(
            'company'                       => $company,
            'fiscalId'                      => $fiscalYearId,
            'dateFrom'                      => $dateFrom,
            'dateTo'                        => $dateTo,
            'thisFiscalYearName'            => $thisFiscalYearInfo->name,
            'previousFiscalYearName'        => $previousFiscalYear->name,
            'projectName'                   => $projectName,
            'projectType'                   => $projectTypeName,
            'branchName'                    => $branchName,
        );
        // dd($cashFlowStatementLoadTableArr)

        // data collection start
        $service = new Service;
        // $service->updateOpBalanceGenerateType();
        $cashFlowLedgersCodeArr = [55000, 56000, 51000, 52000, 53000, 54000, 57000, 40000, 14000, 26000, 16000, 11000, 15000, 18000, 22000, 22700, 24000, 23500, 25000, 24700, 27500];
        // sort($cashFlowLedgersCodeArr);
        // get cash flow ledger ids
        $cashFlowLedgersIdArr = $service->getParentsNChildsLedgers($cashFlowLedgersCodeArr);

        $allCashNonCashArr['non-cash-expense'] = $service->getParentsNChildsLedgers([51000, 52000, 53000, 54000, 55000, 56000, 57000]);
        $allCashNonCashArr['non-cash-income'] = $service->getParentsNChildsLedgers([40000]);
        $allCashNonCashArr['cash-operating'] = $service->getParentsNChildsLedgers([14000, 16000, 26000]);
        $allCashNonCashArr['cash-investing'] = $service->getParentsNChildsLedgers([11000, 15000, 18000]);
        $allCashNonCashArr['cash-financing'] = $service->getParentsNChildsLedgers([22000, 22700, 23500, 24000, 24700, 25000, 27500]);
        $allCashArr = array_merge($allCashNonCashArr['cash-operating'], $allCashNonCashArr['cash-investing'], $allCashNonCashArr['cash-financing']);
        $allNonCashArr = array_merge($allCashNonCashArr['non-cash-expense'], $allCashNonCashArr['non-cash-income']);
        // all ledgers
        $ledgersCollection = $service->getLedgerHeaderInfos($project, $branchIdArr, $user_company_id);
        // income and expense
        $finalLevelIncomeExpenseLedgerIdArr  = $ledgersCollection->where('level', 5)->whereIn('accountTypeId', [12, 13])->pluck('id')->toArray();
        // income
        $finalLevelIncomeLedgerIdArr  = $ledgersCollection->where('level', 5)->where('accountTypeId', 12)->pluck('id')->toArray();
        // expense
        $finalLevelExpenseLedgerIdArr  = $ledgersCollection->where('level', 5)->where('accountTypeId', 13)->pluck('id')->toArray();
        // cash n bank
        $cashNBankLedgerIdArr  = $ledgersCollection->where('level', 5)->whereIn('accountTypeId', [4, 5])->pluck('id')->toArray();
        // cash flow ledger collection
        $cashFlowLedgersCollection = $ledgersCollection->whereIn('id', $cashFlowLedgersIdArr);
        // level wise ledger collection
        $firstLevelLedgers  = $cashFlowLedgersCollection->where('level', 1);
        $secondLevelLedgers = $cashFlowLedgersCollection->where('level', 2);
        $thirdLevelLedgers  = $cashFlowLedgersCollection->where('level', 3);
        $fourthLevelLedgers = $cashFlowLedgersCollection->where('level', 4);
        $finalLevelLedgers  = $cashFlowLedgersCollection->where('level', 5);
        // cash flow ledger ids arr
        $finalLevelCashFlowLedgersIdArr = $finalLevelLedgers->pluck('id')->toArray();

        // variables
        $previousYearSurplus = 0;
        $currentYearSurplus = 0;
        $thisMonthSurplus = 0;
        $thisYearSurplus = 0;
        $cumulativeSurplus = 0;
        $previousYearOpeningBalanceCashNBank = 0;
        $currentYearOpeningBalanceCashNBank = 0;
        $thisMonthOpeningCashNBankBalance = 0;
        $thisYearOpeningCashNBankBalance = 0;
        $cumOpeningCashNBankBalance = 0;
        //////////////////////////////=========================surplus calculation===========================////////////////////////////////
        // ===================search type fiscal year==============/////////
        if ($searchType == 1) {
            // income
            $finalLevelLedgersIncomeData = DB::table('acc_month_end_balance')
                                            ->whereIn('ledgerId', $finalLevelIncomeLedgerIdArr)
                                            ->whereIn('branchId', $branchIdArr)
                                            ->where('projectId', $project)
                                            ->whereIn('fiscalYearId', [$fiscalYearId, $previousFiscalYearId])
                                            ->where('monthEndDate', '<=', $dateTo);

            if ($projectType != "0") {
                $finalLevelLedgersIncomeData->where('projectTypeId', $projectType);
            }

            $finalLevelLedgersIncomeData = $finalLevelLedgersIncomeData
                                            ->select(
                                                'fiscalYearId',
                                                DB::raw('SUM(balanceAmount) as incomeBalance')
                                            )
                                            ->groupBy('fiscalYearId')
                                            ->get();

            // expense
            $finalLevelLedgersExpenseData = DB::table('acc_month_end_balance')
                                            ->whereIn('ledgerId', $finalLevelExpenseLedgerIdArr)
                                            ->whereIn('branchId', $branchIdArr)
                                            ->where('projectId', $project)
                                            ->whereIn('fiscalYearId', [$fiscalYearId, $previousFiscalYearId])
                                            ->where('monthEndDate', '<=', $dateTo);

            if ($projectType != "0") {
                $finalLevelLedgersExpenseData->where('projectTypeId', $projectType);
            }

            $finalLevelLedgersExpenseData = $finalLevelLedgersExpenseData
                                            ->select(
                                                'fiscalYearId',
                                                DB::raw('SUM(balanceAmount) as expenseBalance')
                                            )
                                            ->groupBy('fiscalYearId')
                                            ->get();

            // surplus calculation
            // previous year
            $previousYearSurplus = abs($finalLevelLedgersIncomeData->where('fiscalYearId', $previousFiscalYearId)->sum('incomeBalance')) - abs($finalLevelLedgersExpenseData->where('fiscalYearId', $previousFiscalYearId)->sum('expenseBalance'));
            //current year
            $currentYearSurplus = abs($finalLevelLedgersIncomeData->where('fiscalYearId', $fiscalYearId)->sum('incomeBalance')) - abs($finalLevelLedgersExpenseData->where('fiscalYearId', $fiscalYearId)->sum('expenseBalance'));

        }
        // =======================search type current year===========================
        elseif ($searchType == 2) {
            // =================================== year-end data ================================================
            // income
            $yearEndIncome = DB::table('acc_opening_balance')
                            ->whereIn('ledgerId', $finalLevelIncomeLedgerIdArr)
                            ->whereIn('branchId', $branchIdArr)
                            ->where('projectId', $project)
                            ->where('fiscalYearId', $previousFiscalYearId);

            if ($projectType != "0") {
                $yearEndIncome->where('projectTypeId', $projectType);
            }

            $yearEndIncome = $yearEndIncome->select(
                                                DB::raw('SUM(debitAmount) as debitAmount'),
                                                DB::raw('SUM(creditAmount) as creditAmount')
                                            )
                                            ->first();
            // expense
            $yearEndExpense = DB::table('acc_opening_balance')
                            ->whereIn('ledgerId', $finalLevelExpenseLedgerIdArr)
                            ->whereIn('branchId', $branchIdArr)
                            ->where('projectId', $project)
                            ->where('fiscalYearId', $previousFiscalYearId);

            if ($projectType != "0") {
                $yearEndExpense->where('projectTypeId', $projectType);
            }

            $yearEndExpense = $yearEndExpense->select(
                                                DB::raw('SUM(debitAmount) as debitAmount'),
                                                DB::raw('SUM(creditAmount) as creditAmount')
                                            )
                                            ->first();

            //==============================this year data================================
            // income debit
            $thisYearIncomeDebit = DB::table('acc_voucher')
                                ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                ->where('acc_voucher.companyId', $user_company_id)
                                ->where('acc_voucher.projectId', $project)
                                ->whereIn('acc_voucher.branchId', $branchIdArr)
                                ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                ->whereIn('acc_voucher_details.debitAcc', $finalLevelIncomeLedgerIdArr);

            if ($projectType != "0") {
                $thisYearIncomeDebit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisYearIncomeDebit = $thisYearIncomeDebit
                                    ->select(
                                        DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                    )
                                    ->value('debitAmount');
            // income credit
            $thisYearIncomeCredit = DB::table('acc_voucher')
                                ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                ->where('acc_voucher.companyId', $user_company_id)
                                ->where('acc_voucher.projectId', $project)
                                ->whereIn('acc_voucher.branchId', $branchIdArr)
                                ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                ->whereIn('acc_voucher_details.creditAcc', $finalLevelIncomeLedgerIdArr);

            if ($projectType != "0") {
                $thisYearIncomeCredit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisYearIncomeCredit = $thisYearIncomeCredit
                                    ->select(
                                        DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                                    )
                                    ->value('creditAmount');

            // expense debit
            $thisYearExpenseDebit = DB::table('acc_voucher')
                                ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                ->where('acc_voucher.companyId', $user_company_id)
                                ->where('acc_voucher.projectId', $project)
                                ->whereIn('acc_voucher.branchId', $branchIdArr)
                                ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                ->whereIn('acc_voucher_details.debitAcc', $finalLevelExpenseLedgerIdArr);

            if ($projectType != "0") {
                $thisYearExpenseDebit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisYearExpenseDebit = $thisYearExpenseDebit
                                    ->select(
                                        DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                    )
                                    ->value('debitAmount');
            // income credit
            $thisYearExpenseCredit = DB::table('acc_voucher')
                                ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                ->where('acc_voucher.companyId', $user_company_id)
                                ->where('acc_voucher.projectId', $project)
                                ->whereIn('acc_voucher.branchId', $branchIdArr)
                                ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                ->whereIn('acc_voucher_details.creditAcc', $finalLevelExpenseLedgerIdArr);

            if ($projectType != "0") {
                $thisYearExpenseCredit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisYearExpenseCredit = $thisYearExpenseCredit
                                    ->select(
                                        DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                                    )
                                    ->value('creditAmount');

            //====================================this month data=======================================
            // income debit
            $thisMonthIncomeDebit = DB::table('acc_voucher')
                                ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                ->where('acc_voucher.companyId', $user_company_id)
                                ->where('acc_voucher.projectId', $project)
                                ->whereIn('acc_voucher.branchId', $branchIdArr)
                                ->where('acc_voucher.voucherDate', '>=', $thisMonthStartDate)
                                ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                ->whereIn('acc_voucher_details.debitAcc', $finalLevelIncomeLedgerIdArr);

            if ($projectType != "0") {
                $thisMonthIncomeDebit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisMonthIncomeDebit = $thisMonthIncomeDebit
                                    ->select(
                                        DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                    )
                                    ->value('debitAmount');
            // income credit
            $thisMonthIncomeCredit = DB::table('acc_voucher')
                                ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                ->where('acc_voucher.companyId', $user_company_id)
                                ->where('acc_voucher.projectId', $project)
                                ->whereIn('acc_voucher.branchId', $branchIdArr)
                                ->where('acc_voucher.voucherDate', '>=', $thisMonthStartDate)
                                ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                ->whereIn('acc_voucher_details.creditAcc', $finalLevelIncomeLedgerIdArr);

            if ($projectType != "0") {
                $thisMonthIncomeCredit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisMonthIncomeCredit = $thisMonthIncomeCredit
                                    ->select(
                                        DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                                    )
                                    ->value('creditAmount');

            // expense debit
            $thisMonthExpenseDebit = DB::table('acc_voucher')
                                ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                ->where('acc_voucher.companyId', $user_company_id)
                                ->where('acc_voucher.projectId', $project)
                                ->whereIn('acc_voucher.branchId', $branchIdArr)
                                ->where('acc_voucher.voucherDate', '>=', $thisMonthStartDate)
                                ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                ->whereIn('acc_voucher_details.debitAcc', $finalLevelExpenseLedgerIdArr);

            if ($projectType != "0") {
                $thisMonthExpenseDebit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisMonthExpenseDebit = $thisMonthExpenseDebit
                                    ->select(
                                        DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                    )
                                    ->value('debitAmount');
            // income credit
            $thisMonthExpenseCredit = DB::table('acc_voucher')
                                ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                ->where('acc_voucher.companyId', $user_company_id)
                                ->where('acc_voucher.projectId', $project)
                                ->whereIn('acc_voucher.branchId', $branchIdArr)
                                ->where('acc_voucher.voucherDate', '>=', $thisMonthStartDate)
                                ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                ->whereIn('acc_voucher_details.creditAcc', $finalLevelExpenseLedgerIdArr);

            if ($projectType != "0") {
                $thisMonthExpenseCredit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisMonthExpenseCredit = $thisMonthExpenseCredit
                                    ->select(
                                        DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                                    )
                                    ->value('creditAmount');
                                    // dd($thisYearExpenseDebit);

            //=============surplus========///////////
            $thisYearSurplus = ($thisYearIncomeCredit - $thisYearIncomeDebit) - ($thisYearExpenseDebit - $thisYearExpenseCredit);
            $thisMonthSurplus = ($thisMonthIncomeCredit - $thisMonthIncomeDebit) - ($thisMonthExpenseDebit - $thisMonthExpenseCredit);
            // cumulative
            if ($yearEndIncome) {
                $yearEndIncomeBalance = $yearEndIncome->creditAmount - $yearEndIncome->debitAmount;
            }
            else {
                $yearEndIncomeBalance = 0;
            }
            if ($yearEndExpense) {
                $yearEndExpenseBalance = $yearEndExpense->debitAmount - $yearEndExpense->creditAmount;
            }
            else {
                $yearEndExpenseBalance = 0;
            }

            $yearEndSurplus = $yearEndIncomeBalance - $yearEndExpenseBalance;
            $cumulativeSurplus = $yearEndSurplus + $thisYearSurplus;
            // dd($thisYearSurplus);

        }

        ////////////////////////////// ============================cash flow data collection ===========================///////////////////////////
        //================search type fiscal year=====================//////////
        if ($searchType == 1) {
            // previous year
            $previousYearData = DB::table('acc_month_end_balance')
                                // ->join('acc_month_end_balance', 'acc_month_end_balance.ledgerId', '=', 'acc_account_ledger.id')
                                ->whereIn('ledgerId', $finalLevelCashFlowLedgersIdArr)
                                ->whereIn('branchId', $branchIdArr)
                                ->where('projectId', $project)
                                ->where('fiscalYearId', $previousFiscalYearId);

            // project type filter
            if ($projectType != "0") {
                $previousYearData->where('projectTypeId', $projectType);
            }

            $previousYearData = $previousYearData
                                ->select('ledgerId',
                                    DB::raw('SUM(debitAmount) as debitAmount'),
                                    DB::raw('SUM(creditAmount) as creditAmount'),
                                    DB::raw('SUM(cashDebit) as cashDebit'),
                                    DB::raw('SUM(bankDebit) as bankDebit'),
                                    DB::raw('SUM(cashCredit) as cashCredit'),
                                    DB::raw('SUM(bankCredit) as bankCredit')
                                )
                                ->groupBy('ledgerId')
                                ->get()->keyBy('ledgerId')->toArray();

            // current year
            $currentYearData= DB::table('acc_month_end_balance')
                            // ->join('acc_month_end_balance', 'acc_month_end_balance.ledgerId', '=', 'acc_account_ledger.id')
                            ->whereIn('ledgerId', $finalLevelCashFlowLedgersIdArr)
                            ->whereIn('branchId', $branchIdArr)
                            ->where('projectId', $project)
                            ->where('fiscalYearId', $thisFiscalYearId)
                            ->where('monthEndDate', '<=', $dateTo);

            // project type filter
            if ($projectType != "0") {
                $currentYearData->where('projectTypeId', $projectType);
            }

            $currentYearData = $currentYearData
                            ->select('ledgerId',
                                DB::raw('SUM(debitAmount) as debitAmount'),
                                DB::raw('SUM(creditAmount) as creditAmount'),
                                DB::raw('SUM(cashDebit) as cashDebit'),
                                DB::raw('SUM(bankDebit) as bankDebit'),
                                DB::raw('SUM(cashCredit) as cashCredit'),
                                DB::raw('SUM(bankCredit) as bankCredit')
                            )
                            ->groupBy('ledgerId')
                            ->get()->keyBy('ledgerId')->toArray();

            // loop for cash flow ledgers data
            foreach ($finalLevelLedgers as $key => $singleLedger) {
                // check ledger type
                $ledgerType = $service->checkLedgerAccountType($singleLedger->id);
                // this month
                // debit
                $previousYearLedgerDebit = isset($previousYearData[$singleLedger->id])
                                            ? in_array($singleLedger->id, $allCashNonCashArr['non-cash-expense']) || in_array($singleLedger->id, $allCashNonCashArr['non-cash-income'])
                                                ? $previousYearData[$singleLedger->id]->debitAmount - $previousYearData[$singleLedger->id]->cashDebit - $previousYearData[$singleLedger->id]->bankDebit
                                                : $previousYearData[$singleLedger->id]->cashDebit + $previousYearData[$singleLedger->id]->bankDebit
                                            : 0;
                // credit
                $previousYearLedgerCredit = isset($previousYearData[$singleLedger->id])
                                            ? in_array($singleLedger->id, $allCashNonCashArr['non-cash-expense']) || in_array($singleLedger->id, $allCashNonCashArr['non-cash-income'])
                                                ? $previousYearData[$singleLedger->id]->creditAmount - $previousYearData[$singleLedger->id]->cashCredit - $previousYearData[$singleLedger->id]->bankCredit
                                                : $previousYearData[$singleLedger->id]->cashCredit + $previousYearData[$singleLedger->id]->bankCredit
                                            : 0;
                // balance
                $previousYearLedgerValue = 0;
                if ($ledgerType == true) {
                    $previousYearLedgerValue = in_array($singleLedger->id, $allCashNonCashArr['non-cash-expense'])
                                                ? $previousYearLedgerDebit - $previousYearLedgerCredit
                                                : -($previousYearLedgerDebit - $previousYearLedgerCredit);
                }
                else {
                    $previousYearLedgerValue = in_array($singleLedger->id, $allCashNonCashArr['non-cash-income'])
                                                ? -($previousYearLedgerCredit - $previousYearLedgerDebit)
                                                : $previousYearLedgerCredit - $previousYearLedgerDebit;
                }

                // this year
                // debit
                $thisYearLedgerDebit = isset($currentYearData[$singleLedger->id])
                                        ? in_array($singleLedger->id, $allCashNonCashArr['non-cash-expense']) || in_array($singleLedger->id, $allCashNonCashArr['non-cash-income'])
                                            ? $currentYearData[$singleLedger->id]->debitAmount - $currentYearData[$singleLedger->id]->cashDebit - $currentYearData[$singleLedger->id]->bankDebit
                                            : $currentYearData[$singleLedger->id]->cashDebit + $currentYearData[$singleLedger->id]->bankDebit
                                        : 0;
                // credit
                $thisYearLedgerCredit = isset($currentYearData[$singleLedger->id])
                                        ? in_array($singleLedger->id, $allCashNonCashArr['non-cash-expense']) || in_array($singleLedger->id, $allCashNonCashArr['non-cash-income'])
                                            ? $currentYearData[$singleLedger->id]->creditAmount - $currentYearData[$singleLedger->id]->cashCredit - $currentYearData[$singleLedger->id]->bankCredit
                                            : $currentYearData[$singleLedger->id]->cashCredit + $currentYearData[$singleLedger->id]->bankCredit
                                        : 0;
                // balance
                $thisYearLedgerValue = 0;
                if ($ledgerType == true) {
                    $thisYearLedgerValue = in_array($singleLedger->id, $allCashNonCashArr['non-cash-expense'])
                                            ? $thisYearLedgerDebit - $thisYearLedgerCredit
                                            : -($thisYearLedgerDebit - $thisYearLedgerCredit);
                }
                else {
                    $thisYearLedgerValue = in_array($singleLedger->id, $allCashNonCashArr['non-cash-income'])
                                            ? -($thisYearLedgerCredit - $thisYearLedgerDebit)
                                            : $thisYearLedgerCredit - $thisYearLedgerDebit;
                }

                $ledgerWiseData[] = array(
                    'id'                            => $singleLedger->id,
                    'code'                          => $singleLedger->code,
                    'name'                          => $singleLedger->name,
                    'isGroupHead'                   => $singleLedger->isGroupHead,
                    'parentId'                      => $singleLedger->parentId,
                    'level'                         => $singleLedger->level,
                    'previousYearValue'             => $previousYearLedgerValue,
                    'currentYearValue'              => $thisYearLedgerValue
                );

            }
            // dd($ledgerWiseData);

        }
        //================search type current year===================//////////
        elseif ($searchType == 2) {
            // =================================== year-end data ================================================
            $yearEndData = DB::table('acc_opening_balance')
                            ->whereIn('ledgerId', $finalLevelCashFlowLedgersIdArr)
                            ->whereIn('branchId', $branchIdArr)
                            ->where('projectId', $project)
                            ->where('fiscalYearId', $previousFiscalYearId);

            if ($projectType != "0") {
                $yearEndData->where('projectTypeId', $projectType);
            }

            $yearEndData = $yearEndData->groupBy('ledgerId')
                                        ->select(
                                            'ledgerId',
                                            DB::raw('SUM(debitAmount) as debitAmount'),
                                            DB::raw('SUM(creditAmount) as creditAmount'),
                                            DB::raw('SUM(cashDebit) as cashDebit'),
                                            DB::raw('SUM(bankDebit) as bankDebit'),
                                            DB::raw('SUM(cashCredit) as cashCredit'),
                                            DB::raw('SUM(bankCredit) as bankCredit')
                                        )
                                        ->get()->keyBy('ledgerId')->toArray();

            //==============================this year data================================
            //debit
            // non-cash
            $thisYearNonCashDebit = DB::table('acc_voucher')
                                        ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('acc_voucher.companyId', $user_company_id)
                                        ->where('acc_voucher.projectId', $project)
                                        ->whereIn('acc_voucher.branchId', $branchIdArr)
                                        ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                        ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                        // ->where('acc_voucher.voucherTypeId', '!=', 5)
                                        ->whereIn('acc_voucher_details.debitAcc', $allNonCashArr)
                                        ->whereNotIn('acc_voucher_details.creditAcc', $cashNBankLedgerIdArr);

            if ($projectType != "0") {
                $thisYearNonCashDebit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisYearNonCashDebit = $thisYearNonCashDebit
                                    ->groupBy('acc_voucher_details.debitAcc')
                                    ->select(
                                        'acc_voucher_details.debitAcc as ledgerId',
                                        DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                    )
                                    ->get()->keyBy('ledgerId')->toArray();
            // cash
            $thisYearCashDebit = DB::table('acc_voucher')
                                        ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('acc_voucher.companyId', $user_company_id)
                                        ->where('acc_voucher.projectId', $project)
                                        ->whereIn('acc_voucher.branchId', $branchIdArr)
                                        ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                        ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                        // ->where('acc_voucher.voucherTypeId', '!=', 5)
                                        ->whereIn('acc_voucher_details.debitAcc', $allCashArr)
                                        ->whereIn('acc_voucher_details.creditAcc', $cashNBankLedgerIdArr);

            if ($projectType != "0") {
                $thisYearCashDebit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisYearCashDebit = $thisYearCashDebit
                                    ->groupBy('acc_voucher_details.debitAcc')
                                    ->select(
                                        'acc_voucher_details.debitAcc as ledgerId',
                                        DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                    )
                                    ->get()->keyBy('ledgerId')->toArray();
            //credit
            // non cash
            $thisYearNonCashCredit = DB::table('acc_voucher')
                                    ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                    ->where('acc_voucher.companyId', $user_company_id)
                                    ->where('acc_voucher.projectId', $project)
                                    ->whereIn('acc_voucher.branchId', $branchIdArr)
                                    ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                    ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                    // ->where('acc_voucher.voucherTypeId', '!=', 5)
                                    ->whereIn('acc_voucher_details.creditAcc', $allNonCashArr)
                                    ->whereNotIn('acc_voucher_details.debitAcc', $cashNBankLedgerIdArr);

            if ($projectType != "0") {
                $thisYearNonCashCredit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisYearNonCashCredit = $thisYearNonCashCredit
                                    ->groupBy('acc_voucher_details.creditAcc')
                                    ->select(
                                        'acc_voucher_details.creditAcc as ledgerId',
                                        DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                                    )
                                    ->get()->keyBy('ledgerId')->toArray();

            // cash
            $thisYearCashCredit = DB::table('acc_voucher')
                                    ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                    ->where('acc_voucher.companyId', $user_company_id)
                                    ->where('acc_voucher.projectId', $project)
                                    ->whereIn('acc_voucher.branchId', $branchIdArr)
                                    ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                    ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                    // ->where('acc_voucher.voucherTypeId', '!=', 5)
                                    ->whereIn('acc_voucher_details.creditAcc', $allCashArr)
                                    ->whereIn('acc_voucher_details.debitAcc', $cashNBankLedgerIdArr);

            if ($projectType != "0") {
                $thisYearCashCredit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisYearCashCredit = $thisYearCashCredit
                                    ->groupBy('acc_voucher_details.creditAcc')
                                    ->select(
                                        'acc_voucher_details.creditAcc as ledgerId',
                                        DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                                    )
                                    ->get()->keyBy('ledgerId')->toArray();

            //====================================this month data=======================================
            //debit
            //non cash
            $thisMonthNonCashDebit = DB::table('acc_voucher')
                            ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                            ->where('acc_voucher.companyId', $user_company_id)
                            ->where('acc_voucher.projectId', $project)
                            ->whereIn('acc_voucher.branchId', $branchIdArr)
                            ->where('acc_voucher.voucherDate', '>=', $thisMonthStartDate)
                            ->where('acc_voucher.voucherDate', '<=', $dateTo)
                            // ->where('acc_voucher.voucherTypeId', '!=', 5)
                            ->whereIn('acc_voucher_details.debitAcc', $allNonCashArr)
                            ->whereNotIn('acc_voucher_details.creditAcc', $cashNBankLedgerIdArr);

            if ($projectType != "0") {
                $thisMonthNonCashDebit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisMonthNonCashDebit = $thisMonthNonCashDebit
                            ->groupBy('acc_voucher_details.debitAcc')
                            ->select(
                                'acc_voucher_details.debitAcc as ledgerId',
                                DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                            )
                            ->get()->keyBy('ledgerId')->toArray();

            // cash
            $thisMonthCashDebit = DB::table('acc_voucher')
                                    ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                    ->where('acc_voucher.companyId', $user_company_id)
                                    ->where('acc_voucher.projectId', $project)
                                    ->whereIn('acc_voucher.branchId', $branchIdArr)
                                    ->where('acc_voucher.voucherDate', '>=', $thisMonthStartDate)
                                    ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                    // ->where('acc_voucher.voucherTypeId', '!=', 5)
                                    ->whereIn('acc_voucher_details.debitAcc', $allCashArr)
                                    ->whereIn('acc_voucher_details.creditAcc', $cashNBankLedgerIdArr);

            if ($projectType != "0") {
                $thisMonthCashDebit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisMonthCashDebit = $thisMonthCashDebit
                            ->groupBy('acc_voucher_details.debitAcc')
                            ->select(
                                'acc_voucher_details.debitAcc as ledgerId',
                                DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                            )
                            ->get()->keyBy('ledgerId')->toArray();

            // credit
            // non cash
            $thisMonthNonCashCredit = DB::table('acc_voucher')
                                ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                ->where('acc_voucher.companyId', $user_company_id)
                                ->where('acc_voucher.projectId', $project)
                                ->whereIn('acc_voucher.branchId', $branchIdArr)
                                ->where('acc_voucher.voucherDate', '>=', $thisMonthStartDate)
                                ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                // ->where('acc_voucher.voucherTypeId', '!=', 5)
                                ->whereIn('acc_voucher_details.creditAcc', $allNonCashArr)
                                ->whereNotIn('acc_voucher_details.debitAcc', $cashNBankLedgerIdArr);

            if ($projectType != "0") {
                $thisMonthNonCashCredit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisMonthNonCashCredit = $thisMonthNonCashCredit
                            ->groupBy('acc_voucher_details.creditAcc')
                            ->select(
                                'acc_voucher_details.creditAcc as ledgerId',
                                DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                            )
                            ->get()->keyBy('ledgerId')->toArray();

            // cash
            $thisMonthCashCredit = DB::table('acc_voucher')
                                ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                ->where('acc_voucher.companyId', $user_company_id)
                                ->where('acc_voucher.projectId', $project)
                                ->whereIn('acc_voucher.branchId', $branchIdArr)
                                ->where('acc_voucher.voucherDate', '>=', $thisMonthStartDate)
                                ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                // ->where('acc_voucher.voucherTypeId', '!=', 5)
                                ->whereIn('acc_voucher_details.creditAcc', $allCashArr)
                                ->whereIn('acc_voucher_details.debitAcc', $cashNBankLedgerIdArr);

            if ($projectType != "0") {
                $thisMonthCashCredit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisMonthCashCredit = $thisMonthCashCredit
                            ->groupBy('acc_voucher_details.creditAcc')
                            ->select(
                                'acc_voucher_details.creditAcc as ledgerId',
                                DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                            )
                            ->get()->keyBy('ledgerId')->toArray();

            // loop for cash flow ledgers data
            foreach ($finalLevelLedgers as $key => $singleLedger) {
                // check ledger type
                $ledgerType = $service->checkLedgerAccountType($singleLedger->id);
                // this month
                // debit
                if (in_array($singleLedger->id, $allCashArr)) {
                    $thisMonthLedgerDebit = isset($thisMonthCashDebit[$singleLedger->id])
                                            ? $thisMonthCashDebit[$singleLedger->id]->debitAmount
                                            : 0;
                }
                elseif(in_array($singleLedger->id, $allNonCashArr)) {
                    $thisMonthLedgerDebit = isset($thisMonthNonCashDebit[$singleLedger->id])
                                            ? $thisMonthNonCashDebit[$singleLedger->id]->debitAmount
                                            : 0;
                }

                // credit
                if (in_array($singleLedger->id, $allCashArr)) {
                    $thisMonthLedgerCredit = isset($thisMonthCashCredit[$singleLedger->id])
                                            ? $thisMonthCashCredit[$singleLedger->id]->creditAmount
                                            : 0;
                }
                elseif(in_array($singleLedger->id, $allNonCashArr)) {
                    $thisMonthLedgerCredit = isset($thisMonthNonCashCredit[$singleLedger->id])
                                            ? $thisMonthNonCashCredit[$singleLedger->id]->creditAmount
                                            : 0;
                }

                // balance
                $thisMonthLedgerValue = 0;
                if ($ledgerType == true) {
                    $thisMonthLedgerValue = in_array($singleLedger->id, $allCashNonCashArr['non-cash-expense'])
                                            ? $thisMonthLedgerDebit - $thisMonthLedgerCredit
                                            : -($thisMonthLedgerDebit - $thisMonthLedgerCredit);
                }
                else {
                    $thisMonthLedgerValue = in_array($singleLedger->id, $allCashNonCashArr['non-cash-income'])
                                            ? -($thisMonthLedgerCredit - $thisMonthLedgerDebit)
                                            : $thisMonthLedgerCredit - $thisMonthLedgerDebit;
                }

                // this year
                // debit
                if (in_array($singleLedger->id, $allCashArr)) {
                    $thisYearLedgerDebit = isset($thisYearCashDebit[$singleLedger->id])
                                            ? $thisYearCashDebit[$singleLedger->id]->debitAmount
                                            : 0;
                }
                elseif(in_array($singleLedger->id, $allNonCashArr)) {
                    $thisYearLedgerDebit = isset($thisYearNonCashDebit[$singleLedger->id])
                                            ? $thisYearNonCashDebit[$singleLedger->id]->debitAmount
                                            : 0;
                }

                // credit
                if (in_array($singleLedger->id, $allCashArr)) {
                    $thisYearLedgerCredit = isset($thisYearCashCredit[$singleLedger->id])
                                            ? $thisYearCashCredit[$singleLedger->id]->creditAmount
                                            : 0;
                }
                elseif(in_array($singleLedger->id, $allNonCashArr)) {
                    $thisYearLedgerCredit = isset($thisYearNonCashCredit[$singleLedger->id])
                                            ? $thisYearNonCashCredit[$singleLedger->id]->creditAmount
                                            : 0;
                }
                // balance
                $thisYearLedgerValue = 0;
                if ($ledgerType == true) {
                    $thisYearLedgerValue = in_array($singleLedger->id, $allCashNonCashArr['non-cash-expense'])
                                            ? $thisYearLedgerDebit - $thisYearLedgerCredit
                                            : -($thisYearLedgerDebit - $thisYearLedgerCredit);
                }
                else {
                    $thisYearLedgerValue = in_array($singleLedger->id, $allCashNonCashArr['non-cash-income'])
                                            ? -($thisYearLedgerCredit - $thisYearLedgerDebit)
                                            : $thisYearLedgerCredit - $thisYearLedgerDebit;
                }

                // cumulative
                // year end debit
                $yearEndLedgerDebit = isset($yearEndData[$singleLedger->id])
                                        ? $yearEndData[$singleLedger->id]->debitAmount
                                        : 0;
                // year end credit
                $yearEndLedgerCredit = isset($yearEndData[$singleLedger->id])
                                        ? $yearEndData[$singleLedger->id]->creditAmount
                                        : 0;

                // this year
                // debit
                $yearEndLedgerDebit = isset($yearEndData[$singleLedger->id])
                                        ? in_array($singleLedger->id, $allCashNonCashArr['non-cash-expense']) || in_array($singleLedger->id, $allCashNonCashArr['non-cash-income'])
                                            ? $yearEndData[$singleLedger->id]->debitAmount - $yearEndData[$singleLedger->id]->cashDebit - $yearEndData[$singleLedger->id]->bankDebit
                                            : $yearEndData[$singleLedger->id]->cashDebit + $yearEndData[$singleLedger->id]->bankDebit
                                        : 0;
                // credit
                $yearEndLedgerCredit = isset($yearEndData[$singleLedger->id])
                                        ? in_array($singleLedger->id, $allCashNonCashArr['non-cash-expense']) || in_array($singleLedger->id, $allCashNonCashArr['non-cash-income'])
                                            ? $yearEndData[$singleLedger->id]->creditAmount - $yearEndData[$singleLedger->id]->cashCredit - $yearEndData[$singleLedger->id]->bankCredit
                                            : $yearEndData[$singleLedger->id]->cashCredit + $yearEndData[$singleLedger->id]->bankCredit
                                        : 0;
                // balance
                $cumulativeLedgerValue = 0;
                if ($ledgerType == true) {
                    $cumulativeLedgerValue = in_array($singleLedger->id, $allCashNonCashArr['non-cash-expense'])
                                            ? ($yearEndLedgerDebit + $thisYearLedgerDebit) - ($yearEndLedgerCredit + $thisYearLedgerCredit)
                                            : -(($yearEndLedgerDebit + $thisYearLedgerDebit) - ($yearEndLedgerCredit + $thisYearLedgerCredit));
                }
                else {
                    $cumulativeLedgerValue = in_array($singleLedger->id, $allCashNonCashArr['non-cash-income'])
                                            ? -(($yearEndLedgerCredit + $thisYearLedgerCredit) - ($yearEndLedgerDebit + $thisYearLedgerDebit))
                                            : ($yearEndLedgerCredit + $thisYearLedgerCredit) - ($yearEndLedgerDebit + $thisYearLedgerDebit);
                }

                $ledgerWiseData[] = array(
                    'id'                            => $singleLedger->id,
                    'code'                          => $singleLedger->code,
                    'name'                          => $singleLedger->name,
                    'isGroupHead'                   => $singleLedger->isGroupHead,
                    'parentId'                      => $singleLedger->parentId,
                    'level'                         => $singleLedger->level,
                    'thisMonthValue'                => $thisMonthLedgerValue,
                    'thisYearValue'                 => $thisYearLedgerValue,
                    'cumulativeValue'               => $cumulativeLedgerValue
                );
            }
            // dd($ledgerWiseData);

        }

        /////////////////////////////============================cash n bank calculation ==============================////////////////////////
        //====================search type fiscal year==============//////////
        if ($searchType == 1) {
            // op balance cash
            $opBalanceCashNBank = DB::table('acc_opening_balance')
                                ->whereIn('ledgerId', $cashNBankLedgerIdArr)
                                ->whereIn('branchId', $branchIdArr)
                                ->where('projectId', $project)
                                ->whereIn('fiscalYearId', [$yearBeforePreviousYearId, $previousFiscalYearId]);

            if ($projectType != "0") {
                $opBalanceCashNBank->where('projectTypeId', $projectType);
            }

            $opBalanceCashNBank = $opBalanceCashNBank
                                ->select('fiscalYearId', DB::raw('SUM(balanceAmount) as cashNBankBalance'))
                                ->groupBy('fiscalYearId')
                                ->get()->keyBy('fiscalYearId')->toArray();

            // opening balance cash and bank
            //previousYear
            $previousYearOpeningBalanceCashNBank = isset($opBalanceCashNBank[$yearBeforePreviousYearId]) ? $opBalanceCashNBank[$yearBeforePreviousYearId]->cashNBankBalance : 0;
            // current year
            $currentYearOpeningBalanceCashNBank = isset($opBalanceCashNBank[$previousFiscalYearId]) ? $opBalanceCashNBank[$previousFiscalYearId]->cashNBankBalance : 0;

        }
        //=====================search type current year================/////////////
        elseif ($searchType = 2) {
            // =================================== year-end data ================================================
            // op balance cash
            $opBalanceCashNBank = DB::table('acc_opening_balance')
                                ->whereIn('ledgerId', $cashNBankLedgerIdArr)
                                ->whereIn('branchId', $branchIdArr)
                                ->where('projectId', $project)
                                ->where('fiscalYearId', $previousFiscalYearId);

            if ($projectType != "0") {
                $opBalanceCashNBank->where('projectTypeId', $projectType);
            }

            $opBalanceCashNBank = $opBalanceCashNBank->select(DB::raw('SUM(balanceAmount) as cashNBankBalance'))->value('cashNBankBalance');

            //==============================this month opening cash================================
            //cash debit
            $thisMonthOpeningCashNBankDebit = DB::table('acc_voucher')
                                            ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                            ->where('acc_voucher.companyId', $user_company_id)
                                            ->where('acc_voucher.projectId', $project)
                                            ->whereIn('acc_voucher.branchId', $branchIdArr)
                                            ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                            ->where('acc_voucher.voucherDate', '<', $thisMonthStartDate)
                                            ->whereIn('acc_voucher_details.debitAcc', $cashNBankLedgerIdArr);

            if ($projectType != "0") {
                $thisMonthOpeningCashNBankDebit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisMonthOpeningCashNBankDebit = $thisMonthOpeningCashNBankDebit
                                            ->select(DB::raw('SUM(acc_voucher_details.amount) as debitAmount'))->value('debitAmount');
            //cash credit
            $thisMonthOpeningCashNBankCredit = DB::table('acc_voucher')
                                            ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                            ->where('acc_voucher.companyId', $user_company_id)
                                            ->where('acc_voucher.projectId', $project)
                                            ->whereIn('acc_voucher.branchId', $branchIdArr)
                                            ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                            ->where('acc_voucher.voucherDate', '<', $thisMonthStartDate)
                                            ->whereIn('acc_voucher_details.creditAcc', $cashNBankLedgerIdArr);

            if ($projectType != "0") {
                $thisMonthOpeningCashNBankCredit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisMonthOpeningCashNBankCredit = $thisMonthOpeningCashNBankCredit
                                        ->select(DB::raw('SUM(acc_voucher_details.amount) as creditAmount'))->value('creditAmount');

            // month opening cash balance
            $thisMonthOpeningCashNBank = $thisMonthOpeningCashNBankDebit - $thisMonthOpeningCashNBankCredit;

            //this month opening balance
            $thisMonthOpeningCashNBankBalance = $opBalanceCashNBank + $thisMonthOpeningCashNBank;
            // this year opening balance
            $thisYearOpeningCashNBankBalance = $opBalanceCashNBank;

            // cumulative data calculation
            // $cumOpeningCashNBankBalance = DB::table('acc_opening_balance')
            //                             ->whereIn('ledgerId', $cashNBankLedgerIdArr)
            //                             ->whereIn('branchId', $branchIdArr)
            //                             ->where('projectId', $project)
            //                             ->where('generateType', 1);
            //
            // if ($projectType != "0") {
            //     $cumOpeningCashNBankBalance->where('projectTypeId', $projectType);
            // }
            //
            // $cumOpeningCashNBankBalance = $cumOpeningCashNBankBalance->select(DB::raw('SUM(balanceAmount) as balance'))->value('balance');
            $cumOpeningCashNBankBalance = 0;

        }

        // structuring data to send information in view file
        $data = array(
            'cashFlowStatementLoadTableArr'             => $cashFlowStatementLoadTableArr,
            'monthEndUnprocessedBranchesByMonth'        => $monthEndUnprocessedBranchesByMonth,
            'firstLevelLedgers'                         => $firstLevelLedgers,
            'secondLevelLedgers'                        => $secondLevelLedgers,
            'thirdLevelLedgers'                         => $thirdLevelLedgers,
            'fourthLevelLedgers'                        => $fourthLevelLedgers,
            'finalLevelLedgers'                         => $finalLevelLedgers,
            'allCashNonCashArr'                         => $allCashNonCashArr,
            'ledgerWiseData'                            => collect($ledgerWiseData),
            'previousYearSurplus'                       => $previousYearSurplus,
            'currentYearSurplus'                        => $currentYearSurplus,
            'thisMonthSurplus'                          => $thisMonthSurplus,
            'thisYearSurplus'                           => $thisYearSurplus,
            'cumulativeSurplus'                         => $cumulativeSurplus,
            'previousYearOpeningBalanceCashNBank'       => $previousYearOpeningBalanceCashNBank,
            'currentYearOpeningBalanceCashNBank'        => $currentYearOpeningBalanceCashNBank,
            'thisMonthOpeningCashNBankBalance'          => $thisMonthOpeningCashNBankBalance,
            'thisYearOpeningCashNBankBalance'           => $thisYearOpeningCashNBankBalance,
            'cumOpeningCashNBankBalance'                => $cumOpeningCashNBankBalance,
            'searchType'                                => $searchType,
            'withZero'                                  => $withZero,
            'roundUp'                                   => $roundUp,
            'depthLevel'                                => $depthLevel
        );
        // dd($data);

        return view('accounting.reports.cashFlowStatementViews.viewCashFlowStatementTable', $data);
    }

}
