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


class FinancialPositionStatementController extends Controller{

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

        $accFinancialPositionArr = array(
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

        return view('accounting.reports.financialPositionStatementViews.viewFinancialStatementFilterForm', $accFinancialPositionArr);
    }

    public static function getProjectType(Request $request) {

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

    public function financialStatementLoadReport(Request $request) {

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
            $lastMonthEndDateAuth = DB::table('acc_month_end')->whereIn('branchIdFk', $branchIdArr)->max('date');

            if($fiscalYearId == $currentFiscalYear->id){
                // all branch
                if (count($branchIdArr) > 0) {
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
                // dd($dateTo);

            }

        }
        // current year search
        elseif($searchType == 2){

            $dateTo = Carbon::parse($request->dateTo)->format('Y-m-d');
            $fiscalYear = DB::table('gnr_fiscal_year')->where('fyStartDate', '<=', $dateTo)->where('fyEndDate','>=', $dateTo)->first();
            $fiscalYearId = $fiscalYear->id;
            $dateFrom = $fiscalYear->fyStartDate;
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
        $financialPositionLoadTableArr = array(
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
        // dd($trialbalanceLoadTableArr);

        $service = new Service;
        $ledgersCollection = $service->getLedgerHeaderInfos($project, $branchIdArr, $user_company_id);
        // dd($ledgersCollection);

        $firstLevelLedgers  = $ledgersCollection->where('level', 1)->whereNotIn('accountTypeId', [12, 13]);
        $secondLevelLedgers = $ledgersCollection->where('level', 2)->whereNotIn('accountTypeId', [12, 13]);
        $thirdLevelLedgers  = $ledgersCollection->where('level', 3)->whereNotIn('accountTypeId', [12, 13]);
        $fourthLevelLedgers = $ledgersCollection->where('level', 4)->whereNotIn('accountTypeId', [12, 13]);
        $finalLevelLedgers  = $ledgersCollection->where('level', 5)->whereNotIn('accountTypeId', [12, 13]);
        $finalLevelLedgerIdArr  = $ledgersCollection->where('level', 5)->whereNotIn('accountTypeId', [12, 13])->pluck('id')->toArray();
        $finalLevelIncomeExpenseLedgerIdArr  = $ledgersCollection->where('level', 5)->whereIn('accountTypeId', [12, 13])->pluck('id')->toArray();
        $finalLevelIncomeLedgerIdArr  = $ledgersCollection->where('level', 5)->where('accountTypeId', 12)->pluck('id')->toArray();
        $finalLevelExpenseLedgerIdArr  = $ledgersCollection->where('level', 5)->where('accountTypeId', 13)->pluck('id')->toArray();
        // dd($finalLevelIncomeAndExpenseLedgerIdArr);

        $totalOpDebit = 0;
        $totalOpCredit = 0;
        $totalCurrentDebit = 0;
        $totalCurrentCredit = 0;
        $totalCumulativeDebit = 0;
        $totalCumulativeCredit = 0;

        // final level ledgers previous year informations
        $finalLevelLedgersOpData= DB::table('acc_account_ledger')
                                ->join('acc_opening_balance', 'acc_opening_balance.ledgerId', '=', 'acc_account_ledger.id')
                                ->whereIn('acc_account_ledger.id', $finalLevelLedgerIdArr)
                                ->whereIn('acc_opening_balance.branchId', $branchIdArr)
                                ->where('acc_opening_balance.projectId', $project)
                                ->where('acc_opening_balance.openingDate', $lastOpeningDate);

        // project type filter
        if ($projectType != "0") {
            $finalLevelLedgersOpData->where('acc_opening_balance.projectTypeId', $projectType);
        }

        $finalLevelLedgersOpData = $finalLevelLedgersOpData
                                    ->select('acc_account_ledger.id',
                                        'acc_account_ledger.name',
                                        'acc_account_ledger.level',
                                        'acc_account_ledger.code',
                                        'acc_account_ledger.parentId',
                                        'acc_account_ledger.isGroupHead',
                                        DB::raw('SUM(acc_opening_balance.debitAmount) as totalOpDebitAmount'),
                                        DB::raw('SUM(acc_opening_balance.creditAmount) as totalOpCreditAmount')
                                    )
                                    ->groupBy('acc_account_ledger.id')
                                    ->get()->keyBy('id')->toArray();
                                    // dd($finalLevelLedgersOpData[380]);

        // current year information
        // current period voucher informations
        $currentPeriodVoucherDebitInfo = DB::table('acc_voucher')
                                        ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('acc_voucher.companyId', $user_company_id)
                                        ->where('acc_voucher.projectId', $project)
                                        ->whereIn('acc_voucher.branchId', $branchIdArr)
                                        ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                        ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.debitAcc', $finalLevelLedgerIdArr);

        if ($projectType != "0") {
            $currentPeriodVoucherDebitInfo->where('acc_voucher.projectTypeId', $projectType);
        }
        // current period voucher debit
        $currentPeriodVoucherDebitInfo = $currentPeriodVoucherDebitInfo
                                        ->groupBy('acc_voucher_details.debitAcc')
                                        ->select(
                                            'acc_voucher_details.debitAcc as ledgerId',
                                            DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                        )
                                        ->get()->keyBy('ledgerId')->toArray();

        $currentPeriodVoucherCreditInfo = DB::table('acc_voucher')
                                        ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('acc_voucher.companyId', $user_company_id)
                                        ->where('acc_voucher.projectId', $project)
                                        ->whereIn('acc_voucher.branchId', $branchIdArr)
                                        ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                        ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.creditAcc', $finalLevelLedgerIdArr);

        if ($projectType != "0") {
            $currentPeriodVoucherCreditInfo->where('acc_voucher.projectTypeId', $projectType);
        }
        // current period voucher credit
        $currentPeriodVoucherCreditInfo = $currentPeriodVoucherCreditInfo
                                        ->groupBy('acc_voucher_details.creditAcc')
                                        ->select(
                                            'acc_voucher_details.creditAcc as ledgerId',
                                            DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                                        )
                                        ->get()->keyBy('ledgerId')->toArray();

        // income and expense calculation
        // search type fiscal year
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
        // search type current year
        elseif ($searchType == 2) {
            // previous year debit
            $previousYearDebitInfo = DB::table('acc_voucher')
                                        ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('acc_voucher.companyId', $user_company_id)
                                        ->where('acc_voucher.projectId', $project)
                                        ->whereIn('acc_voucher.branchId', $branchIdArr)
                                        ->where('acc_voucher.voucherDate', '>=', $previousFiscalYear->fyStartDate)
                                        ->where('acc_voucher.voucherDate', '<=', $previousFiscalYear->fyEndDate)
                                        ->whereIn('acc_voucher_details.debitAcc', $finalLevelIncomeExpenseLedgerIdArr);

            if ($projectType != "0") {
                $previousYearDebitInfo->where('acc_voucher.projectTypeId', $projectType);
            }

            $previousYearDebitInfo = $previousYearDebitInfo
                                            ->groupBy('acc_voucher_details.debitAcc')
                                            ->select(
                                                'acc_voucher_details.debitAcc as ledgerId',
                                                DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                            )
                                            ->get()->keyBy('ledgerId')->toArray();

            // previous year credit
            $previousYearCreditInfo = DB::table('acc_voucher')
                                        ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('acc_voucher.companyId', $user_company_id)
                                        ->where('acc_voucher.projectId', $project)
                                        ->whereIn('acc_voucher.branchId', $branchIdArr)
                                        ->where('acc_voucher.voucherDate', '>=', $previousFiscalYear->fyStartDate)
                                        ->where('acc_voucher.voucherDate', '<=', $previousFiscalYear->fyEndDate)
                                        ->whereIn('acc_voucher_details.creditAcc', $finalLevelIncomeExpenseLedgerIdArr);

            if ($projectType != "0") {
                $previousYearCreditInfo->where('acc_voucher.projectTypeId', $projectType);
            }

            $previousYearCreditInfo = $previousYearCreditInfo
                                            ->groupBy('acc_voucher_details.creditAcc')
                                            ->select(
                                                'acc_voucher_details.creditAcc as ledgerId',
                                                DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                                            )
                                            ->get()->keyBy('ledgerId')->toArray();

            // current year debit
            $currentYearDebitInfo = DB::table('acc_voucher')
                                        ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('acc_voucher.companyId', $user_company_id)
                                        ->where('acc_voucher.projectId', $project)
                                        ->whereIn('acc_voucher.branchId', $branchIdArr)
                                        ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                        ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.debitAcc', $finalLevelIncomeExpenseLedgerIdArr);

            if ($projectType != "0") {
                $currentYearDebitInfo->where('acc_voucher.projectTypeId', $projectType);
            }

            $currentYearDebitInfo = $currentYearDebitInfo
                                            ->groupBy('acc_voucher_details.debitAcc')
                                            ->select(
                                                'acc_voucher_details.debitAcc as ledgerId',
                                                DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                            )
                                            ->get()->keyBy('ledgerId')->toArray();

            // current year credit
            $currentYearCreditInfo = DB::table('acc_voucher')
                                        ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('acc_voucher.companyId', $user_company_id)
                                        ->where('acc_voucher.projectId', $project)
                                        ->whereIn('acc_voucher.branchId', $branchIdArr)
                                        ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                        ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.creditAcc', $finalLevelIncomeExpenseLedgerIdArr);

            if ($projectType != "0") {
                $currentYearCreditInfo->where('acc_voucher.projectTypeId', $projectType);
            }

            $currentYearCreditInfo = $currentYearCreditInfo
                                            ->groupBy('acc_voucher_details.creditAcc')
                                            ->select(
                                                'acc_voucher_details.creditAcc as ledgerId',
                                                DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                                            )
                                            ->get()->keyBy('ledgerId')->toArray();
                                            // dd($previousYearDebitInfo);
            // previous year debit
            $previousYearTotalDebitIncome = 0;
            $previousYearTotalDebitExpense = 0;
            foreach ($previousYearDebitInfo as $key => $item) {
                // income
                if (in_array($key, $finalLevelIncomeLedgerIdArr)) {
                    $previousYearTotalDebitIncome += $item->debitAmount;
                }
                // expense
                elseif(in_array($key, $finalLevelExpenseLedgerIdArr)) {
                    $previousYearTotalDebitExpense += $item->debitAmount;
                }
            }
            // previous year credit
            $previousYearTotalCreditIncome = 0;
            $previousYearTotalCreditExpense = 0;
            foreach ($previousYearCreditInfo as $key => $item) {
                // income
                if (in_array($key, $finalLevelIncomeLedgerIdArr)) {
                    $previousYearTotalCreditIncome += $item->creditAmount;
                }
                // expense
                elseif(in_array($key, $finalLevelExpenseLedgerIdArr)) {
                    $previousYearTotalCreditExpense += $item->creditAmount;
                }
            }
            // current year debit
            $currentYearTotalDebitIncome = 0;
            $currentYearTotalDebitExpense = 0;
            foreach ($currentYearDebitInfo as $key => $item) {
                // income
                if (in_array($key, $finalLevelIncomeLedgerIdArr)) {
                    $currentYearTotalDebitIncome += $item->debitAmount;
                }
                // expense
                elseif(in_array($key, $finalLevelExpenseLedgerIdArr)) {
                    $currentYearTotalDebitExpense += $item->debitAmount;
                }
            }
            // current year credit
            $currentYearTotalCreditIncome = 0;
            $currentYearTotalCreditExpense = 0;
            foreach ($currentYearCreditInfo as $key => $item) {
                // income
                if (in_array($key, $finalLevelIncomeLedgerIdArr)) {
                    $currentYearTotalCreditIncome += $item->creditAmount;
                }
                // expense
                elseif(in_array($key, $finalLevelExpenseLedgerIdArr)) {
                    $currentYearTotalCreditExpense += $item->creditAmount;
                }
            }

            // surplus calculation
            // previous year
            $previousYearSurplus = ($previousYearTotalCreditIncome - $previousYearTotalDebitIncome) - ($previousYearTotalDebitExpense - $previousYearTotalCreditExpense);
            // current year
            $currentYearSurplus = ($currentYearTotalCreditIncome - $currentYearTotalDebitIncome) - ($currentYearTotalDebitExpense - $currentYearTotalCreditExpense);

        }

        // cumulative surplus calculation
        // income
        $cumulativeIncomeData = DB::table('acc_opening_balance')
                                ->whereIn('ledgerId', $finalLevelIncomeLedgerIdArr)
                                ->whereIn('branchId', $branchIdArr)
                                ->where('projectId', $project)
                                ->whereIn('fiscalYearId', [$yearBeforePreviousYearId, $previousFiscalYearId]);

        if ($projectType != "0") {
            $cumulativeIncomeData->where('projectTypeId', $projectType);
        }

        $cumulativeIncomeData = $cumulativeIncomeData
                                ->select(
                                    'fiscalYearId',
                                    DB::raw('SUM(balanceAmount) as incomeBalance')
                                )
                                ->groupBy('fiscalYearId')
                                ->get();
        // expense
        $cumulativeExpenseData = DB::table('acc_opening_balance')
                                ->whereIn('ledgerId', $finalLevelExpenseLedgerIdArr)
                                ->whereIn('branchId', $branchIdArr)
                                ->where('projectId', $project)
                                ->whereIn('fiscalYearId', [$yearBeforePreviousYearId, $previousFiscalYearId]);

        if ($projectType != "0") {
            $cumulativeExpenseData->where('projectTypeId', $projectType);
        }

        $cumulativeExpenseData = $cumulativeExpenseData
                                ->select(
                                    'fiscalYearId',
                                    DB::raw('SUM(balanceAmount) as expenseBalance')
                                )
                                ->groupBy('fiscalYearId')
                                ->get();

        // loop running after collecting all info
        foreach ($finalLevelLedgers as $key => $singleLedgerData) {

            $ledgerType = Service::checkLedgerAccountType($singleLedgerData->id);

            // previous year debit and credit
            $previousYearDebitAmount = isset($finalLevelLedgersOpData[$singleLedgerData->id]) ? $finalLevelLedgersOpData[$singleLedgerData->id]->totalOpDebitAmount : 0;
            $previousYearCreditAmount = isset($finalLevelLedgersOpData[$singleLedgerData->id]) ? $finalLevelLedgersOpData[$singleLedgerData->id]->totalOpCreditAmount : 0;

            // balance calculation
            if ($ledgerType == true) {
                $previousYearBalance = $previousYearDebitAmount - $previousYearCreditAmount;
            }
            elseif($ledgerType == false) {
                $previousYearBalance = $previousYearCreditAmount - $previousYearDebitAmount;
            }

            // current period debit and credit
            $currentPeriodDebitAmount = 0;
            $currentPeriodCreditAmount = 0;

            if (isset($currentPeriodVoucherDebitInfo[$singleLedgerData->id])) {
                $currentPeriodDebitAmount += $currentPeriodVoucherDebitInfo[$singleLedgerData->id]->debitAmount;
            }

            if (isset($currentPeriodVoucherCreditInfo[$singleLedgerData->id])) {
                $currentPeriodCreditAmount += $currentPeriodVoucherCreditInfo[$singleLedgerData->id]->creditAmount;
            }

            // cumulative
            $cumulativeDebitAmount = $previousYearDebitAmount + $currentPeriodDebitAmount;
            $cumulativeCreditAmount = $previousYearCreditAmount + $currentPeriodCreditAmount;

            // balance calculation
            if ($ledgerType == true) {
                $currentYearBalance = $cumulativeDebitAmount - $cumulativeCreditAmount;
            }
            elseif($ledgerType == false) {
                $currentYearBalance = $cumulativeCreditAmount - $cumulativeDebitAmount;
            }

            if ($singleLedgerData->id == 512) {
                // previous year
                $previousYearBalance += abs($cumulativeIncomeData->where('fiscalYearId', $yearBeforePreviousYearId)->sum('incomeBalance')) - abs($cumulativeExpenseData->where('fiscalYearId', $yearBeforePreviousYearId)->sum('expenseBalance'));
                // current year
                $currentYearBalance += abs($cumulativeIncomeData->where('fiscalYearId', $previousFiscalYearId)->sum('incomeBalance')) - abs($cumulativeExpenseData->where('fiscalYearId', $previousFiscalYearId)->sum('expenseBalance'));
            }

            // final ledger information array
            $ledgerWiseData[] = array(
                'id'                            => $singleLedgerData->id,
                'code'                          => $singleLedgerData->code,
                'name'                          => $singleLedgerData->name,
                'isGroupHead'                   => $singleLedgerData->isGroupHead,
                'parentId'                      => $singleLedgerData->parentId,
                'level'                         => $singleLedgerData->level,
                'previousYearBalance'           => $previousYearBalance,
                'currentYearBalance'            => $currentYearBalance
            );
        }
        // dd($ledgerWiseData);

        // structuring data to send information in view file
        $data = array(
            'financialPositionLoadTableArr'         => $financialPositionLoadTableArr,
            'monthEndUnprocessedBranchesByMonth'    => $monthEndUnprocessedBranchesByMonth,
            'firstLevelLedgers'                     => $firstLevelLedgers,
            'secondLevelLedgers'                    => $secondLevelLedgers,
            'thirdLevelLedgers'                     => $thirdLevelLedgers,
            'fourthLevelLedgers'                    => $fourthLevelLedgers,
            'finalLevelLedgers'                     => $finalLevelLedgers,
            'ledgerWiseData'                        => collect($ledgerWiseData),
            'previousYearSurplus'                   => $previousYearSurplus,
            'currentYearSurplus'                    => $currentYearSurplus,
            'searchType'                            => $searchType,
            'withZero'                              => $withZero,
            'roundUp'                               => $roundUp,
            'depthLevel'                            => $depthLevel
        );

        return view('accounting.reports.financialPositionStatementViews.viewFinancialStatementTable', $data);
    }

}
