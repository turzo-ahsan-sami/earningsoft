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
            'currentFiscalYear'                 => $currentFiscalYear,
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
                    $dateTo = $branchDate;
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

        $service = new Service;
        $ledgersCollection = $service->getLedgerHeaderInfos($project, $branchIdArr, $user_company_id);
        // dd($ledgersCollection);

        $firstLevelLedgers  = $ledgersCollection->where('level', 1)->whereIn('accountTypeId', [12, 13]);
        $secondLevelLedgers = $ledgersCollection->where('level', 2)->whereIn('accountTypeId', [12, 13]);
        $thirdLevelLedgers  = $ledgersCollection->where('level', 3)->whereIn('accountTypeId', [12, 13]);
        $fourthLevelLedgers = $ledgersCollection->where('level', 4)->whereIn('accountTypeId', [12, 13]);
        $finalLevelLedgers  = $ledgersCollection->where('level', 5)->whereIn('accountTypeId', [12, 13]);
        $finalLevelIncomeExpenseLedgerIdArr  = $finalLevelLedgers->pluck('id')->toArray();
        $finalLevelIncomeLedgerIdArr  = $finalLevelLedgers->where('accountTypeId', 12)->pluck('id')->toArray();
        $finalLevelExpenseLedgerIdArr  = $finalLevelLedgers->where('accountTypeId', 13)->pluck('id')->toArray();
        // dd($finalLevelExpenseLedgerIdArr);

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
                                ->where('acc_month_end_balance.projectId', $project)
                                ->where('acc_month_end_balance.fiscalYearId', $previousFiscalYearId);

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
                                            ->where('acc_voucher.projectId', $project)
                                            ->whereIn('acc_voucher.branchId', $branchIdArr)
                                            ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                            ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                            ->whereIn('acc_voucher_details.debitAcc', $finalLevelIncomeExpenseLedgerIdArr);

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
                                            ->where('acc_voucher.projectId', $project)
                                            ->whereIn('acc_voucher.branchId', $branchIdArr)
                                            ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                            ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                            ->whereIn('acc_voucher_details.creditAcc', $finalLevelIncomeExpenseLedgerIdArr);

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
                                ->where('acc_month_end_balance.projectId', $project)
                                ->where('acc_month_end_balance.fiscalYearId', $thisFiscalYearId)
                                ->where('acc_month_end_balance.monthEndDate', '<=', $dateTo);

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
            foreach ($finalLevelLedgers as $key => $singleLedger) {
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
                            ->where('projectId', $project)
                            ->where('fiscalYearId', $previousFiscalYearId);

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
                                        ->where('acc_voucher.projectId', $project)
                                        ->whereIn('acc_voucher.branchId', $branchIdArr)
                                        ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                        ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.debitAcc', $finalLevelIncomeExpenseLedgerIdArr);

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
                                    ->where('acc_voucher.projectId', $project)
                                    ->whereIn('acc_voucher.branchId', $branchIdArr)
                                    ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                    ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                    ->whereIn('acc_voucher_details.creditAcc', $finalLevelIncomeExpenseLedgerIdArr);

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
                            ->where('acc_voucher.projectId', $project)
                            ->whereIn('acc_voucher.branchId', $branchIdArr)
                            ->where('acc_voucher.voucherDate', '>=', $thisMonthStartDate)
                            ->where('acc_voucher.voucherDate', '<=', $dateTo)
                            ->whereIn('acc_voucher_details.debitAcc', $finalLevelIncomeExpenseLedgerIdArr);

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
                                ->where('acc_voucher.projectId', $project)
                                ->whereIn('acc_voucher.branchId', $branchIdArr)
                                ->where('acc_voucher.voucherDate', '>=', $thisMonthStartDate)
                                ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                ->whereIn('acc_voucher_details.creditAcc', $finalLevelIncomeExpenseLedgerIdArr);

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
            foreach ($finalLevelLedgers as $key => $singleLedger) {
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

        // structuring data to send information in view file
        $data = array(
            'comprehensiveIncomeLoadTableArr'       => $comprehensiveIncomeLoadTableArr,
            'monthEndUnprocessedBranchesByMonth'    => $monthEndUnprocessedBranchesByMonth,
            'firstLevelLedgers'                     => $firstLevelLedgers,
            'secondLevelLedgers'                    => $secondLevelLedgers,
            'thirdLevelLedgers'                     => $thirdLevelLedgers,
            'fourthLevelLedgers'                    => $fourthLevelLedgers,
            'finalLevelLedgers'                     => $finalLevelLedgers,
            'ledgerWiseData'                        => collect($ledgerWiseData),
            'searchType'                            => $searchType,
            'withZero'                              => $withZero,
            'roundUp'                               => $roundUp,
            'depthLevel'                            => $depthLevel
        );
        // dd($data);

        return view('accounting.reports.comprehensiveIncomeStatementViews.viewComprehensiveIncomeTable', $data);
    }

}
