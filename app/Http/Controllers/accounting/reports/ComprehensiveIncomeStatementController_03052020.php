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


class ComprehensiveIncomeStatementController extends Controller{

    public function index(Request $request) {

        $userBranchId = Auth::user()->branchId;
        $userBranchInfo = DB::table('gnr_branch')->where('id', $userBranchId)->first();
        if ($userBranchId == 1) {
            $projects = ['All'] + DB::table('gnr_project')->pluck('name','id')->toarray();
            $userBranchStartDate = DB::table('gnr_branch')->min('aisStartDate');
            $branchDate = DB::table('acc_day_end')->max('date');
        }
        else {
            $projects = DB::table('gnr_project')->where('id', $userBranchInfo->projectId)->pluck('name','id')->toarray();
            $userBranchStartDate = DB::table('gnr_branch')->where('id', $userBranchId)->value('aisStartDate');
            $branchDate = DB::table('acc_day_end')->where('branchIdFk', $userBranchId)->max('date');
        }

        $branchDate = $branchDate == null ? $userBranchStartDate : $branchDate;
        $fiscalYears = DB::table('gnr_fiscal_year')
                    ->where('fyEndDate', '>=', $userBranchStartDate)
                    ->where('fyStartDate', '<=', $branchDate)
                    ->select('name','id')->orderBy('id', 'desc')->get();

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

        $comprehensiveIncomeArr = array(
            'reportLevelList'                   => $reportLevelList,
            'areaList'                          => $areaList,
            'zoneList'                          => $zoneList,
            'regionList'                        => $regionList,
            'branchLists'                       => $branchLists,
            'userBranchId'                      => $userBranchId,
            'today'                             => Carbon::now()->format('d-m-Y'),
            'projects'                          => $projects,
            'fiscalYears'                       => $fiscalYears,
            'userBranchStartDate'               => $userBranchStartDate,
            'branchDate'                        => Carbon::parse($branchDate)->format('d-m-Y'),
        );
        // dd($accCashFlowStatementArr);

        return view('accounting.reports.comprehensiveIncomeStatementViews.viewComprehensiveIncomeFilterForm', $comprehensiveIncomeArr);
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

    public function comprehensiveIncomeLoadReport(Request $request) {

        $user_company_id = Auth::user()->company_id_fk;
        $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
        $userBranchId = Auth::user()->branchId;

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

        $branchIdArr = (int)$project == 0 ? DB::table('gnr_branch')->pluck('id')->toArray()
                                            : Service::getFilteredBranchIds($project, $reportLevel, $branch, $area, $zone, $region);
        // dd($branchIdArr);

        // branch date
        $branchDate = DB::table('acc_day_end')->whereIn('branchIdFk', $branchIdArr)->max('date');
        $branchDate = $branchDate == null ? DB::table('gnr_branch')->whereIn('id', $branchIdArr)->max('aisStartDate') : $branchDate;
        // dd($branchDate);

        $currentFiscalYear = DB::table('gnr_fiscal_year')
                            ->where('fyStartDate', '<=', $branchDate)
                            ->where('fyEndDate', '>=', $branchDate)
                            ->select('id','name', 'fyStartDate', 'fyEndDate')
                            ->first();

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

            if ($lastMonthEndDate) {
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
                if (count($branchIdArr) > 1) {
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
                    $lastMonthEndDate = DB::table('acc_month_end')->where('branchIdFk', $branch)->where('date', '>', $currentFiscalYear->fyStartDate)->max('date');

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

        $projectName = (int)$project == 0 ? 'All' : DB::table('gnr_project')->where('id', $project)->value('name');
        $projectTypeName = (int)$projectType == 0 ? 'All' : DB::table('gnr_project_type')->where('id', $projectType)->value('name');

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
        $comprehensiveIncomeLoadTableArr = array(
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
        // dd($comprehensiveIncomeLoadTableArr);

        // ledgers variable and collection
        $incomeLedgerTypeId = 12;
        $expenseLedgerTypeId = 13;
        $grandParentLedgerIdArr['income'] = 4;
        $grandParentLedgerIdArr['expense'] = 5;

        $service = new Service;
        $ledgersCollection = (int)$project == 0 ? DB::table('acc_account_ledger')
                                                    ->where('status', 1)
                                                    ->where('companyIdFk', $user_company_id)
                                                    ->select('id', 'name', 'code', 'accountTypeId', 'isGroupHead', 'level', 'parentId')
                                                    ->orderBy('code')
                                                    ->get()
                                                : $service->getLedgerHeaderInfos($project, $branchIdArr, $user_company_id);
        $ledgersCollection = $ledgersCollection->whereIn('accountTypeId', [$incomeLedgerTypeId, $expenseLedgerTypeId]);
        $maxLevel = $ledgersCollection->max('level');
        $finalLevelLedgers  = $ledgersCollection->where('isGroupHead', 0);
        $finalLevelIncomeExpenseLedgerIdArr  = $finalLevelLedgers->pluck('id')->toArray();
        $finalLevelIncomeLedgerIdArr  = $finalLevelLedgers->where('accountTypeId', $incomeLedgerTypeId)->pluck('id')->toArray();
        $finalLevelExpenseLedgerIdArr  = $finalLevelLedgers->where('accountTypeId', $expenseLedgerTypeId)->pluck('id')->toArray();
        // dd($finalLevelExpenseLedgerIdArr);

        // calculation variable
        $totalOpDebit = 0;
        $totalOpCredit = 0;
        $totalCurrentDebit = 0;
        $totalCurrentCredit = 0;
        $totalCumulativeDebit = 0;
        $totalCumulativeCredit = 0;
        // dd($dateFrom, $dateTo);
        // search type fiscal year
        if ($searchType == 1) {
            // previous year
            $previousYearData = DB::table('acc_account_ledger')
                                ->join('acc_month_end_balance', 'acc_month_end_balance.ledgerId', '=', 'acc_account_ledger.id')
                                ->whereIn('acc_account_ledger.id', $finalLevelIncomeExpenseLedgerIdArr)
                                ->whereIn('acc_month_end_balance.branchId', $branchIdArr)
                                // ->where('acc_month_end_balance.projectId', $project)
                                ->where('acc_month_end_balance.fiscalYearId', $previousFiscalYearId);

            // project  filter
            if ($project != "0") {
                $previousYearData->where('acc_month_end_balance.projectId', $project);
            }
            // project type filter
            if ($projectType != "0") {
                $previousYearData->where('acc_month_end_balance.projectTypeId', $projectType);
            }

            $previousYearData = $previousYearData
                                ->select('acc_account_ledger.id',
                                    DB::raw('SUM(acc_month_end_balance.debitAmount) as debitAmount'),
                                    DB::raw('SUM(acc_month_end_balance.creditAmount) as creditAmount')
                                )
                                ->groupBy('acc_account_ledger.id')
                                ->get()->keyBy('id')->toArray();

            // current Year
            if ($thisFiscalYearId == $currentFiscalYear->id) {
                // debit
                $currentYearVoucherDebit = DB::table('acc_voucher')
                                            ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                            ->where('acc_voucher.companyId', $user_company_id)
                                            // ->where('acc_voucher.projectId', $project)
                                            ->whereIn('acc_voucher.branchId', $branchIdArr)
                                            ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                            ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                            ->whereIn('acc_voucher_details.debitAcc', $finalLevelIncomeExpenseLedgerIdArr);

                if ($project != "0") {
                    $currentYearVoucherDebit->where('acc_voucher.projectId', $project);
                }
                if ($projectType != "0") {
                    $currentYearVoucherDebit->where('acc_voucher.projectTypeId', $projectType);
                }
                // current period voucher debit
                $currentYearVoucherDebit = $currentYearVoucherDebit
                                        ->groupBy('acc_voucher_details.debitAcc')
                                        ->select(
                                            'acc_voucher_details.debitAcc as ledgerId',
                                            DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                        )
                                        ->get()->keyBy('ledgerId')->toArray();

                // credit
                $currentYearVoucherCredit = DB::table('acc_voucher')
                                            ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                            ->where('acc_voucher.companyId', $user_company_id)
                                            // ->where('acc_voucher.projectId', $project)
                                            ->whereIn('acc_voucher.branchId', $branchIdArr)
                                            ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                            ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                            ->whereIn('acc_voucher_details.creditAcc', $finalLevelIncomeExpenseLedgerIdArr);

                if ($project != "0") {
                    $currentYearVoucherCredit->where('acc_voucher.projectId', $project);
                }
                if ($projectType != "0") {
                    $currentYearVoucherCredit->where('acc_voucher.projectTypeId', $projectType);
                }
                // current period voucher credit
                $currentYearVoucherCredit = $currentYearVoucherCredit
                                            ->groupBy('acc_voucher_details.creditAcc')
                                            ->select(
                                                'acc_voucher_details.creditAcc as ledgerId',
                                                DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                                            )
                                            ->get()->keyBy('ledgerId')->toArray();
            }
            else {
                $currentYearData= DB::table('acc_account_ledger')
                                ->join('acc_month_end_balance', 'acc_month_end_balance.ledgerId', '=', 'acc_account_ledger.id')
                                ->whereIn('acc_account_ledger.id', $finalLevelIncomeExpenseLedgerIdArr)
                                ->whereIn('acc_month_end_balance.branchId', $branchIdArr)
                                // ->where('acc_month_end_balance.projectId', $project)
                                ->where('acc_month_end_balance.fiscalYearId', $thisFiscalYearId)
                                ->where('acc_month_end_balance.monthEndDate', '<=', $dateTo);

                // project  filter
                if ($project != "0") {
                    $currentYearData->where('acc_month_end_balance.projectId', $project);
                }
                // project type filter
                if ($projectType != "0") {
                    $currentYearData->where('acc_month_end_balance.projectTypeId', $projectType);
                }

                $currentYearData = $currentYearData
                                    ->select('acc_account_ledger.id',
                                        DB::raw('SUM(acc_month_end_balance.debitAmount) as debitAmount'),
                                        DB::raw('SUM(acc_month_end_balance.creditAmount) as creditAmount')
                                    )
                                    ->groupBy('acc_account_ledger.id')
                                    ->get()->keyBy('id')->toArray();
            }


            // final loop for income and expense ledgers
            foreach ($ledgersCollection as $key => $singleLedger) {
                // previous year
                $previousYearLedgerValue = isset($previousYearData[$singleLedger->id])
                                            ? in_array($singleLedger->id, $finalLevelIncomeLedgerIdArr)
                                                ? $previousYearData[$singleLedger->id]->creditAmount - $previousYearData[$singleLedger->id]->debitAmount
                                                : $previousYearData[$singleLedger->id]->debitAmount - $previousYearData[$singleLedger->id]->creditAmount
                                            : 0;
                // current year
                if ($thisFiscalYearId == $currentFiscalYear->id) {   // for running year
                    // debit
                    $currentYearDebitValue = isset($currentYearVoucherDebit[$singleLedger->id])
                                                ? $currentYearVoucherDebit[$singleLedger->id]->debitAmount
                                                : 0;
                    // credit
                    $currentYearCreditValue = isset($currentYearVoucherCredit[$singleLedger->id])
                                                ? $currentYearVoucherCredit[$singleLedger->id]->creditAmount
                                                : 0;

                    if (in_array($singleLedger->id, $finalLevelIncomeLedgerIdArr)) {  // income
                        $currentYearLedgerValue = $currentYearCreditValue - $currentYearDebitValue;
                    }
                    else {  // expense
                        $currentYearLedgerValue = $currentYearDebitValue - $currentYearCreditValue;
                    }

                }
                else {
                    $currentYearLedgerValue = isset($currentYearData[$singleLedger->id])
                                                ? in_array($singleLedger->id, $finalLevelIncomeLedgerIdArr)
                                                    ? $currentYearData[$singleLedger->id]->creditAmount - $currentYearData[$singleLedger->id]->debitAmount
                                                    : $currentYearData[$singleLedger->id]->debitAmount - $currentYearData[$singleLedger->id]->creditAmount
                                                : 0;
                }

                $ledgerWiseData[] = array(
                    'id'                            => $singleLedger->id,
                    'code'                          => $singleLedger->code,
                    'name'                          => $singleLedger->name,
                    'isGroupHead'                   => $singleLedger->isGroupHead,
                    'parentId'                      => $singleLedger->parentId,
                    'level'                         => $singleLedger->level,
                    'previousYearValue'             => $previousYearLedgerValue,
                    'currentYearValue'              => $currentYearLedgerValue
                );
            }

        }
        // search type current year
        elseif ($searchType == 2) {
            // =================================== year-end data ================================================
            $yearEndData = DB::table('acc_opening_balance')
                            ->whereIn('ledgerId', $finalLevelIncomeExpenseLedgerIdArr)
                            ->whereIn('branchId', $branchIdArr)
                            // ->where('projectId', $project)
                            ->where('fiscalYearId', $previousFiscalYearId);
            // project  filter
            if ($project != "0") {
                $yearEndData->where('projectId', $project);
            }
            // project type filter
            if ($projectType != "0") {
                $yearEndData->where('projectTypeId', $projectType);
            }

            $yearEndData = $yearEndData->groupBy('ledgerId')
                                        ->select(
                                            'ledgerId',
                                            DB::raw('SUM(debitAmount) as debitAmount'),
                                            DB::raw('SUM(creditAmount) as creditAmount')
                                        )
                                        ->get()->keyBy('ledgerId')->toArray();

            //==============================this year data================================
            //debit
            $thisYearDebit = DB::table('acc_voucher')
                                        ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('acc_voucher.companyId', $user_company_id)
                                        // ->where('acc_voucher.projectId', $project)
                                        ->whereIn('acc_voucher.branchId', $branchIdArr)
                                        ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                        ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.debitAcc', $finalLevelIncomeExpenseLedgerIdArr);
            // project  filter
            if ($project != "0") {
                $thisYearDebit->where('acc_voucher.projectId', $project);
            }
            // project type filter
            if ($projectType != "0") {
                $thisYearDebit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisYearDebit = $thisYearDebit
                                    ->groupBy('acc_voucher_details.debitAcc')
                                    ->select(
                                        'acc_voucher_details.debitAcc as ledgerId',
                                        DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                    )
                                    ->get()->keyBy('ledgerId')->toArray();
            //cash credit
            $thisYearCredit = DB::table('acc_voucher')
                                    ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                    ->where('acc_voucher.companyId', $user_company_id)
                                    // ->where('acc_voucher.projectId', $project)
                                    ->whereIn('acc_voucher.branchId', $branchIdArr)
                                    ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                    ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                    ->whereIn('acc_voucher_details.creditAcc', $finalLevelIncomeExpenseLedgerIdArr);
            // project  filter
            if ($project != "0") {
                $thisYearCredit->where('acc_voucher.projectId', $project);
            }
            // project type filter
            if ($projectType != "0") {
                $thisYearCredit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisYearCredit = $thisYearCredit
                                    ->groupBy('acc_voucher_details.creditAcc')
                                    ->select(
                                        'acc_voucher_details.creditAcc as ledgerId',
                                        DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                                    )
                                    ->get()->keyBy('ledgerId')->toArray();

            //====================================this month data=======================================
            //debit
            $thisMonthDebit = DB::table('acc_voucher')
                            ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                            ->where('acc_voucher.companyId', $user_company_id)
                            // ->where('acc_voucher.projectId', $project)
                            ->whereIn('acc_voucher.branchId', $branchIdArr)
                            ->where('acc_voucher.voucherDate', '>=', $thisMonthStartDate)
                            ->where('acc_voucher.voucherDate', '<=', $dateTo)
                            ->whereIn('acc_voucher_details.debitAcc', $finalLevelIncomeExpenseLedgerIdArr);
            // project  filter
            if ($project != "0") {
                $thisMonthDebit->where('acc_voucher.projectId', $project);
            }
            // project type filter
            if ($projectType != "0") {
                $thisMonthDebit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisMonthDebit = $thisMonthDebit
                            ->groupBy('acc_voucher_details.debitAcc')
                            ->select(
                                'acc_voucher_details.debitAcc as ledgerId',
                                DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                            )
                            ->get()->keyBy('ledgerId')->toArray();
            // credit
            $thisMonthCredit = DB::table('acc_voucher')
                                ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                ->where('acc_voucher.companyId', $user_company_id)
                                // ->where('acc_voucher.projectId', $project)
                                ->whereIn('acc_voucher.branchId', $branchIdArr)
                                ->where('acc_voucher.voucherDate', '>=', $thisMonthStartDate)
                                ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                ->whereIn('acc_voucher_details.creditAcc', $finalLevelIncomeExpenseLedgerIdArr);
            // project  filter
            if ($project != "0") {
                $thisMonthCredit->where('acc_voucher.projectId', $project);
            }
            // project type filter
            if ($projectType != "0") {
                $thisMonthCredit->where('acc_voucher.projectTypeId', $projectType);
            }

            $thisMonthCredit = $thisMonthCredit
                            ->groupBy('acc_voucher_details.creditAcc')
                            ->select(
                                'acc_voucher_details.creditAcc as ledgerId',
                                DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                            )
                            ->get()->keyBy('ledgerId')->toArray();

            // final loop for income and expense ledgers
            foreach ($ledgersCollection as $key => $singleLedger) {
                // this month
                // debit
                $thisMonthLedgerDebit = isset($thisMonthDebit[$singleLedger->id])
                                        ? $thisMonthDebit[$singleLedger->id]->debitAmount
                                        : 0;
                // credit
                $thisMonthLedgerCredit = isset($thisMonthCredit[$singleLedger->id])
                                        ? $thisMonthCredit[$singleLedger->id]->creditAmount
                                        : 0;
                // balance
                $thisMonthLedgerValue = in_array($singleLedger->id, $finalLevelIncomeLedgerIdArr)
                                        ? $thisMonthLedgerCredit - $thisMonthLedgerDebit
                                        : $thisMonthLedgerDebit - $thisMonthLedgerCredit;

                // this year
                // debit
                $thisYearLedgerDebit = isset($thisYearDebit[$singleLedger->id])
                                        ? $thisYearDebit[$singleLedger->id]->debitAmount
                                        : 0;
                // credit
                $thisYearLedgerCredit = isset($thisYearCredit[$singleLedger->id])
                                        ? $thisYearCredit[$singleLedger->id]->creditAmount
                                        : 0;
                // balance
                $thisYearLedgerValue = in_array($singleLedger->id, $finalLevelIncomeLedgerIdArr)
                                        ? $thisYearLedgerCredit - $thisYearLedgerDebit
                                        : $thisYearLedgerDebit - $thisYearLedgerCredit;

                // cumulative
                // year end debit
                $yearEndLedgerDebit = isset($yearEndData[$singleLedger->id])
                                        ? $yearEndData[$singleLedger->id]->debitAmount
                                        : 0;
                // year end credit
                $yearEndLedgerCredit = isset($yearEndData[$singleLedger->id])
                                        ? $yearEndData[$singleLedger->id]->creditAmount
                                        : 0;

                // balance
                $cumulativeLedgerValue = in_array($singleLedger->id, $finalLevelIncomeLedgerIdArr)
                                        ? ($yearEndLedgerCredit + $thisYearLedgerCredit) - ($yearEndLedgerDebit + $thisYearLedgerDebit)
                                        : ($yearEndLedgerDebit + $thisYearLedgerDebit) - ($yearEndLedgerCredit + $thisYearLedgerCredit);

                $ledgerWiseData[] = array(
                    'id'                            => $singleLedger->id,
                    'code'                          => $singleLedger->code,
                    'name'                          => $singleLedger->name,
                    'isGroupHead'                   => $singleLedger->isGroupHead,
                    'parentId'                      => $singleLedger->parentId,
                    'level'                         => $singleLedger->level,
                    'thisMonthValue'                => $thisMonthLedgerValue,
                    'thisYearValue'                 => $thisYearLedgerValue,
                    'cumulativeValue'               => round($cumulativeLedgerValue, 6)
                );
            }

        }

        ////////////////////// level wise data collection //////////////////////
        // echo "<pre>";
        // print_r($ledgerWiseData);
        // echo "</pre>";
        // exit();
        $ledgerWiseData = collect($ledgerWiseData);
        $ledgers = ['ledgers' => [], 'parents' => []];

        foreach ($ledgerWiseData as $ledger) {
            $ledgers['ledgers'][$ledger['id']] = $ledger;
            $ledgers['parents'][$ledger['parentId']][] = $ledger['id'];
        }
        // dd($ledgers);

        // recursion for sum
        $ledgerWiseSumArr = [];
        foreach ($ledgerWiseData as $key => $ledger) {
            $ledgerWiseSumArr[$ledger['id']] = $this->getRecursiveSum($ledgerWiseData, $ledger['id'], $searchType);
        }
        // dd($ledgerWiseData);

        // generate html tree
        $treeView = $this->buildTree(0, $ledgers, $ledgerWiseSumArr, $searchType, $grandParentLedgerIdArr);
        // surplus calculation
        $surplus['previousYear'] = $ledgerWiseSumArr[$grandParentLedgerIdArr['income']]['previousYearValue'] - $ledgerWiseSumArr[$grandParentLedgerIdArr['expense']]['previousYearValue'];
        $surplus['currentYear'] = $ledgerWiseSumArr[$grandParentLedgerIdArr['income']]['currentYearValue'] - $ledgerWiseSumArr[$grandParentLedgerIdArr['expense']]['currentYearValue'];
        $surplus['thisMonth'] = $ledgerWiseSumArr[$grandParentLedgerIdArr['income']]['thisMonthValue'] - $ledgerWiseSumArr[$grandParentLedgerIdArr['expense']]['thisMonthValue'];
        $surplus['thisYear'] = $ledgerWiseSumArr[$grandParentLedgerIdArr['income']]['thisYearValue'] - $ledgerWiseSumArr[$grandParentLedgerIdArr['expense']]['thisYearValue'];
        $surplus['cumulative'] = $ledgerWiseSumArr[$grandParentLedgerIdArr['income']]['cumulativeValue'] - $ledgerWiseSumArr[$grandParentLedgerIdArr['expense']]['cumulativeValue'];
        // dd($surplus);

        // structuring data to send information in view file
        $data = array(
            'comprehensiveIncomeLoadTableArr'       => $comprehensiveIncomeLoadTableArr,
            'monthEndUnprocessedBranchesByMonth'    => $monthEndUnprocessedBranchesByMonth,
            'maxLevel'                              => $maxLevel,
            'treeView'                              => $treeView,
            'surplus'                               => $surplus,
            'searchType'                            => $searchType,
            'withZero'                              => $withZero,
            'roundUp'                               => $roundUp,
            'depthLevel'                            => $depthLevel
        );
        // dd($data);

        return view('accounting.reports.comprehensiveIncomeStatementViews.viewComprehensiveIncomeTable', $data);
    }

    // function for all ledgers recursive sum
    public function getRecursiveSum($ledgerWiseData, $ledgerId, $searchType) {

        $sum['previousYearValue'] = 0;
        $sum['currentYearValue'] = 0;
        $sum['thisMonthValue'] = 0;
        $sum['thisYearValue'] = 0;
        $sum['cumulativeValue'] = 0;
        $childs = $ledgerWiseData->where('parentId', $ledgerId);

        //////////// search type fiscal year ////////////
        if ($searchType == 1) {
            if ($childs->count() > 0) {
                foreach ($childs as $key => $child) {
                    $sumArr = $this->getRecursiveSum($ledgerWiseData, $child['id'], $searchType);
                    $sum['previousYearValue'] += $sumArr['previousYearValue'];
                    $sum['currentYearValue'] += $sumArr['currentYearValue'];
                }
            } else {
                $sum['previousYearValue'] += $ledgerWiseData->where('id', $ledgerId)->sum('previousYearValue');
                $sum['currentYearValue'] += $ledgerWiseData->where('id', $ledgerId)->sum('currentYearValue');
            }

        }
        //////////// search type current year ////////////
        elseif ($searchType == 2) {

            if ($childs->count() > 0) {
                foreach ($childs as $key => $child) {
                    $sumArr = $this->getRecursiveSum($ledgerWiseData, $child['id'], $searchType);
                    $sum['thisMonthValue'] += $sumArr['thisMonthValue'];
                    $sum['thisYearValue'] += $sumArr['thisYearValue'];
                    $sum['cumulativeValue'] += $sumArr['cumulativeValue'];
                }
            } else {
                $sum['thisMonthValue'] += $ledgerWiseData->where('id', $ledgerId)->sum('thisMonthValue');
                $sum['thisYearValue'] += $ledgerWiseData->where('id', $ledgerId)->sum('thisYearValue');
                $sum['cumulativeValue'] += $ledgerWiseData->where('id', $ledgerId)->sum('cumulativeValue');
            }

        }

        return $sum;
    }

    // function for html tree view with recursion
    public function buildTree($parents, $ledgers, $ledgerWiseSumArr, $searchType, $grandParentLedgerIdArr) {

        $html = "";

        if (isset($ledgers['parents'][$parents])) {

            foreach ($ledgers['parents'][$parents] as $ledgerId) {

                if ($ledgers['ledgers'][$ledgerId]['level'] > 4) {

                    if ($ledgers['ledgers'][$ledgerId]['isGroupHead'] == 0) {
                        $trStyle = 'level level-final level-constant';
                    }
                    else {
                        $trStyle = 'level level-constant level-'. $ledgers['ledgers'][$ledgerId]['level'];
                    }
                }
                else {
                    if ($ledgers['ledgers'][$ledgerId]['isGroupHead'] == 0) {
                        $trStyle = 'level level-final level-constant';
                    }
                    else {
                        $trStyle = 'level level-'. $ledgers['ledgers'][$ledgerId]['level'];
                    }
                }

                ///////////// search type fiscal year ///////////////
                if ($searchType == 1) {

                    if (!isset($ledgers['parents'][$ledgerId])) {

                        $html .= '<tr class="'.$trStyle.'">
                                    <td style="text-align: left;">' .$ledgers['ledgers'][$ledgerId]['name']. '[' . $ledgers['ledgers'][$ledgerId]['code'] . ']</td>
                                    <td></td>
                                    <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['previousYearValue'].'">'.
                                        number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['previousYearValue'], 2).
                                    '</td>
                                    <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentYearValue'] .'">
                                        '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentYearValue'], 2) .
                                    '</td>
                                </tr>';

                    }

                    if (isset($ledgers['parents'][$ledgerId])) {

                        $html .= '<tr class="'.$trStyle.'">
                                    <td style="text-align: left;">' .$ledgers['ledgers'][$ledgerId]['name']. '[' . $ledgers['ledgers'][$ledgerId]['code'] . ']</td>
                                    <td></td>
                                    <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['previousYearValue'].'">'.
                                        number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['previousYearValue'], 2).
                                    '</td>
                                    <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentYearValue'] .'">
                                        '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentYearValue'], 2) .
                                    '</td>
                                </tr>';

                        $html .= $this->buildTree($ledgerId, $ledgers, $ledgerWiseSumArr, $searchType, $grandParentLedgerIdArr);

                        ///////// total income row ////////////
                        if ($ledgers['ledgers'][$ledgerId]['id'] == $grandParentLedgerIdArr['income']) {

                            $html .= '<tr class="total text-bold">
                                        <td style="text-align: left;">TOTAL INCOME</td>
                                        <td></td>
                                        <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['previousYearValue'].'">'.
                                            number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['previousYearValue'], 2).
                                        '</td>
                                        <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentYearValue'] .'">
                                            '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentYearValue'], 2) .
                                        '</td>
                                    </tr>';
                        }
                        ///////// total expenditure row ////////////
                        elseif ($ledgers['ledgers'][$ledgerId]['id'] == $grandParentLedgerIdArr['expense']) {

                            $html .= '<tr class="total text-bold">
                                        <td style="text-align: left;">TOTAL EXPENDITURE</td>
                                        <td></td>
                                        <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['previousYearValue'].'">'.
                                            number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['previousYearValue'], 2).
                                        '</td>
                                        <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentYearValue'] .'">
                                            '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentYearValue'], 2) .
                                        '</td>
                                    </tr>';
                        }
                    }

                }
                ///////////// search type current year ///////////////
                elseif ($searchType == 2) {

                    if (!isset($ledgers['parents'][$ledgerId])) {

                        $html .= '<tr class="'.$trStyle.'">
                                    <td style="text-align: left;">' .$ledgers['ledgers'][$ledgerId]['name']. '[' . $ledgers['ledgers'][$ledgerId]['code'] . ']</td>
                                    <td></td>
                                    <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['thisMonthValue'].'">'.
                                        number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['thisMonthValue'], 2).
                                    '</td>
                                    <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['thisYearValue'] .'">
                                        '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['thisYearValue'], 2) .
                                    '</td>
                                    <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['cumulativeValue'] .'">
                                        '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['cumulativeValue'], 2) .
                                    '</td>
                                </tr>';

                    }

                    if (isset($ledgers['parents'][$ledgerId])) {

                        $html .= '<tr class="'.$trStyle.'">
                                    <td style="text-align: left;">' .$ledgers['ledgers'][$ledgerId]['name']. '[' . $ledgers['ledgers'][$ledgerId]['code'] . ']</td>
                                    <td></td>
                                    <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['thisMonthValue'].'">'.
                                        number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['thisMonthValue'], 2).
                                    '</td>
                                    <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['thisYearValue'] .'">
                                        '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['thisYearValue'], 2) .
                                    '</td>
                                    <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['cumulativeValue'] .'">
                                        '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['cumulativeValue'], 2) .
                                    '</td>
                                </tr>';

                        $html .= $this->buildTree($ledgerId, $ledgers, $ledgerWiseSumArr, $searchType, $grandParentLedgerIdArr);

                        ///////// total income row ////////////
                        if ($ledgers['ledgers'][$ledgerId]['id'] == $grandParentLedgerIdArr['income']) {

                            $html .= '<tr class="total text-bold">
                                        <td style="text-align: left;">TOTAL INCOME</td>
                                        <td></td>
                                        <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['thisMonthValue'].'">'.
                                            number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['thisMonthValue'], 2).
                                        '</td>
                                        <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['thisYearValue'] .'">
                                            '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['thisYearValue'], 2) .
                                        '</td>
                                        <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['cumulativeValue'] .'">
                                            '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['cumulativeValue'], 2) .
                                        '</td>
                                    </tr>';
                        }
                        ///////// total expenditure row ////////////
                        elseif ($ledgers['ledgers'][$ledgerId]['id'] == $grandParentLedgerIdArr['expense']) {

                            $html .= '<tr class="total text-bold">
                                        <td style="text-align: left;">TOTAL EXPENDITURE</td>
                                        <td></td>
                                        <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['thisMonthValue'].'">'.
                                            number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['thisMonthValue'], 2).
                                        '</td>
                                        <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['thisYearValue'] .'">
                                            '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['thisYearValue'], 2) .
                                        '</td>
                                        <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['cumulativeValue'] .'">
                                            '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['cumulativeValue'], 2) .
                                        '</td>
                                    </tr>';
                        }

                    }

                }


            }

        }

        return $html;
    }

}
