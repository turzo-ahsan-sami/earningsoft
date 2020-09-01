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
use App\accounting\process\AccMonthEnd;
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

use App\Service\DatabasePartitionHelper;
use Illuminate\Support\Collection;
use phpDocumentor\Reflection\Types\Null_;

class FinancialPositionStatementController extends Controller{

    public function index(Request $request) {

        $user_company_id = Auth::user()->company_id_fk;
        $companyBranches = GnrBranch::where('companyId', $user_company_id)->pluck('id')->toArray();
        $userBranch = Auth::user()->branch;
        $headOfficeId = DB::table('gnr_branch')->where('companyId', $user_company_id)->where('branchCode', 0)->value('id');
       

        if ($userBranch->branchCode != 0) {
            $projects = GnrProject::where('id', $userBranch->projectId)->pluck('name', 'id')->toarray();
            $userBranchStartDate = GnrBranch::where('id', $userBranch->id)->value('aisStartDate');
            $lastMonthEndDate = AccMonthEnd::active()->where('branchIdFk', $userBranch->id)->max('date');
            if ($lastMonthEndDate) {
                $branchDate = Carbon::parse($lastMonthEndDate)->addDay()->endOfMonth()->format('Y-m-d');
            } else {
                $branchDate = Carbon::parse($userBranchStartDate)->endOfMonth()->format('Y-m-d');
            }
        }
        else {
            $projects = ['0' => 'All'] + GnrProject::where('companyId', $user_company_id)->pluck('name','id')->toarray();
            $userBranchStartDate = GnrBranch::where('companyId', $user_company_id)->min('aisStartDate');
            $lastMonthEndDate = AccMonthEnd::active()->whereIn('branchIdFk', $companyBranches)->max('date');
            if ($lastMonthEndDate) {
                $branchDate = Carbon::parse($lastMonthEndDate)->addDay()->endOfMonth()->format('Y-m-d');
            } else {
                $branchDate = Carbon::parse(GnrBranch::where('companyId', $user_company_id)->max('aisStartDate'))->endOfMonth()->format('Y-m-d');
            }
        }

        $fiscalYears = DB::table('gnr_fiscal_year')
                    ->where('companyId', $user_company_id)
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
        $branchLists = DB::table('gnr_branch')->where('companyId', $user_company_id);

        if ($userBranch->branchCode != 0) {
            $branchLists = $branchLists->where('id', $userBranch->id);
        }
        else {
            $branches = DB::table('gnr_branch')->where('companyId', $user_company_id)
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

        $userBranchId = Auth::user()->branchId;
        $userBranchData = DB::table('gnr_branch')
                        ->where('id', $userBranchId)
                        ->select('id', DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"))
                        ->first();
        
                        //dd($userBranchData);
        $accFinancialPositionArr = array(
            'reportLevelList'                   => $reportLevelList,
            'areaList'                          => $areaList,
            'zoneList'                          => $zoneList,
            'regionList'                        => $regionList,
            'branchLists'                       => $branchLists,
            'userBranchId'                      => $userBranch->id,
            'headOfficeId'                      => $headOfficeId,
            'userBranchCode'                    => $userBranch->branchCode,
            'projects'                          => $projects,
            'fiscalYears'                       => $fiscalYears,
            'userBranchData'                    => $userBranchData ,
            'userBranchStartDate'               => $userBranchStartDate,
            'branchDate'                        => Carbon::parse($branchDate)->format('d-m-Y'),
        );

        // dd($accFinancialPositionArr);

        return view('accounting.reports.financialPositionStatementViews.viewFinancialStatementFilterForm', $accFinancialPositionArr);
    }

    public static function getProjectType(Request $request) {
        
        $user_company_id = Auth::user()->company_id_fk;

        $projectTypes = DB::table('gnr_project_type')
                        ->select('name','id')
                        // ->where('companyId', $user_company_id)
                        ->where('projectId',$request->projectId)
                        ->get();

        $branches = DB::table('gnr_branch')
                    ->where('companyId', $user_company_id)
                    ->where('projectId',$request->projectId)
                    ->orderBy('branchCode')
                    ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                    ->toArray();
                    // dd($branches);

        $projectTypesNBranches = array(
            'projectTypes'  => $projectTypes,
            'branches'      => $branches
        );

        return response()->json($projectTypesNBranches);
    }

    public function financialStatementLoadReport(Request $request) {
        // dd($request->all());
        $dbhelper = new DatabasePartitionHelper();        

        $acc_voucher = $dbhelper->getPartitionWiseDBTableNameForJoin('acc_voucher', 'av');

        $user_company_id = Auth::user()->company_id_fk;
        $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
        $userBranchId = Auth::user()->branchId;
        //dd($userBranchId);
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
        $branch  = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->select('id','branchCode')->first();
        //dd($branch);
        if ($branch->branchCode != 0) {
            $branch = Auth::user()->branchId;
        }
        else {
            $branch = $request->filBranch;
        }
         //dd($branch);

        $branchIdArr = (int)$project == 0 ? DB::table('gnr_branch')->where('companyId', $user_company_id)->pluck('id')->toArray()
                                            : Service::getFilteredBranchIds($project, $reportLevel, $branch, $area, $zone, $region);
        // dd($branchIdArr);

        // branch date
        $lastMonthEndDate = AccMonthEnd::active()->whereIn('branchIdFk', $branchIdArr)->max('date');
        if ($lastMonthEndDate) {
            $branchDate = Carbon::parse($lastMonthEndDate)->addDay()->endOfMonth()->format('Y-m-d');
        } else {
            $branchDate = GnrBranch::whereIn('id', $branchIdArr)->max('aisStartDate');
        }
        // dd($branchDate);

        $currentFiscalYear = DB::table('gnr_fiscal_year')
                            ->where('companyId', $user_company_id)
                            ->where('fyStartDate', '<=', $branchDate)
                            ->where('fyEndDate', '>=', $branchDate)
                            ->select('id','name', 'fyStartDate', 'fyEndDate')
                            ->first();
                            // dd($currentFiscalYear);

        // collecting date range
        // common month end date
        $monthEndUnprocessedBranchesByMonth = [];
        $branchNotStarted = [];
        $lastMonthEndDate = '';
        $lastCommonMonthEndDate = DB::table('acc_month_end')->whereIn('branchIdFk', $branchIdArr)->max('date');
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

            $dateFrom = DB::table('gnr_fiscal_year')->where('companyId', $user_company_id)->where('id', $fiscalYearId)->value('fyStartDate');
            $dateTo = DB::table('gnr_fiscal_year')->where('companyId', $user_company_id)->where('id', $fiscalYearId)->value('fyEndDate');
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
                            $monthEndedBranches = DB::table('acc_month_end')->whereIn('branchIdFk', $branchIdArr)
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
                // dd($dateTo);

            }

        }
        // current year search
        elseif($searchType == 2){

            $dateTo = Carbon::parse($request->dateTo)->format('Y-m-d');
            $fiscalYear = DB::table('gnr_fiscal_year')->where('companyId', $user_company_id)->where('fyStartDate', '<=', $dateTo)->where('fyEndDate','>=', $dateTo)->first();
            $fiscalYearId = $fiscalYear->id;
            $dateFrom = $fiscalYear->fyStartDate;
        }

        $thisFiscalYearInfo = DB::table('gnr_fiscal_year')->where('companyId', $user_company_id)
                    ->where('fyStartDate', '<=', $dateFrom)
                    ->where('fyEndDate', '>=', $dateFrom)
                    ->select('id','name', 'fyStartDate', 'fyEndDate')
                    ->first();

        $previousFiscalYear = DB::table('gnr_fiscal_year')->where('companyId', $user_company_id)
                                ->where('fyStartDate', '<=', Carbon::parse($dateFrom)->subyear()->format('Y-m-d'))
                                ->where('fyEndDate','>=', Carbon::parse($dateFrom)->subyear()->format('Y-m-d'))
                                ->first();

        $yearBeforePreviousYear = DB::table('gnr_fiscal_year')->where('companyId', $user_company_id)
                                ->where('fyStartDate', '<=', Carbon::parse($previousFiscalYear->fyStartDate ?? '')->subyear()->format('Y-m-d'))
                                ->where('fyEndDate','>=', Carbon::parse($previousFiscalYear->fyStartDate ?? '')->subyear()->format('Y-m-d'))
                                ->first();

        $previousFiscalYearId = $previousFiscalYear->id ?? 0;
        $yearBeforePreviousYearId = $yearBeforePreviousYear->id ?? 0;
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
       // dd($branchName);
        // requested info array
        $financialPositionLoadTableArr = array(
            'company'                       => $company,
            'fiscalId'                      => $fiscalYearId,
            'dateFrom'                      => $dateFrom,
            'dateTo'                        => $dateTo,
            'thisFiscalYearName'            => $thisFiscalYearInfo->name,
            'previousFiscalYearName'        => $previousFiscalYear->name ?? null,
            'projectName'                   => $projectName,
            'projectType'                   => $projectTypeName,
            'branchName'                    => $branchName,
        );
    //    dd($financialPositionLoadTableArr);

        // ledgers variable and collection
        $incomeLedgerTypeId = 12;
        $expenseLedgerTypeId = 13;
        $retainedSurplusLedgerId = AddLedger::where('companyIdFk', $user_company_id)->where('accountTypeId', 10)->where('isGroupHead', 0)->value('id');
        $grandParentLedgerIdArr['asset'] = AddLedger::where('companyIdFk', $user_company_id)->where('code', 10000)->value('id');
        $grandParentLedgerIdArr['liability'] = AddLedger::where('companyIdFk', $user_company_id)->where('code', 20000)->value('id');
        $grandParentLedgerIdArr['capitalFund'] = AddLedger::where('companyIdFk', $user_company_id)->where('code', 30000)->value('id');

        $service = new Service;
        $allLedgersCollection = (int)$project == 0 ? DB::table('acc_account_ledger')
                                                    ->where('status', 1)
                                                    ->where('companyIdFk', $user_company_id)
                                                    ->select('id', 'name', 'code', 'accountTypeId', 'isGroupHead', 'level', 'parentId')
                                                    ->orderBy('code')
                                                    ->get()
                                                : $service->getLedgerHeaderInfos($project, $branchIdArr, $user_company_id);

        $incomeExpenseLedgersCollection = $allLedgersCollection->whereIn('accountTypeId', [$incomeLedgerTypeId, $expenseLedgerTypeId]);
        $ledgersCollection = $allLedgersCollection->whereNotIn('accountTypeId', [$incomeLedgerTypeId, $expenseLedgerTypeId]);
        $finalLevelLedgerIdArr  = $ledgersCollection->where('isGroupHead', 0)->pluck('id')->toArray();
        $finalLevelIncomeExpenseLedgerIdArr  = $incomeExpenseLedgersCollection->where('isGroupHead', 0)->pluck('id')->toArray();
        $finalLevelIncomeLedgerIdArr  = $incomeExpenseLedgersCollection->where('accountTypeId', $incomeLedgerTypeId)->pluck('id')->toArray();
        $finalLevelExpenseLedgerIdArr  = $incomeExpenseLedgersCollection->where('accountTypeId', $expenseLedgerTypeId)->pluck('id')->toArray();
        $maxLevel = $ledgersCollection->max('level');
        // dd($ledgersCollection);

        $totalOpDebit = 0;
        $totalOpCredit = 0;
        $totalCurrentDebit = 0;
        $totalCurrentCredit = 0;
        $totalCumulativeDebit = 0;
        $totalCumulativeCredit = 0;

        // final level ledgers previous year informations
        $acc_opening_balance = $dbhelper->getPartitionWiseDBTableNameForJoin('acc_opening_balance', 'ob');   
        $finalLevelLedgersOpData= DB::table('acc_account_ledger')
                                ->join($acc_opening_balance, 'ob.ledgerId', '=', 'acc_account_ledger.id')
                                ->whereIn('acc_account_ledger.id', $finalLevelLedgerIdArr)
                                ->whereIn('ob.branchId', $branchIdArr)
                                ->where('ob.companyIdFk', $user_company_id)
                                ->where('ob.openingDate', $lastOpeningDate);

        // project  filter
        if ($project != "0") {
            $finalLevelLedgersOpData->where('ob.projectId', $project);
        }
        // project type filter
        if ($projectType != "0") {
            $finalLevelLedgersOpData->where('ob.projectTypeId', $projectType);
        }

        $finalLevelLedgersOpData = $finalLevelLedgersOpData
                                    ->select('acc_account_ledger.id',
                                        'acc_account_ledger.name',
                                        'acc_account_ledger.level',
                                        'acc_account_ledger.code',
                                        'acc_account_ledger.parentId',
                                        'acc_account_ledger.isGroupHead',
                                        DB::raw('SUM(ob.debitAmount) as totalOpDebitAmount'),
                                        DB::raw('SUM(ob.creditAmount) as totalOpCreditAmount')
                                    )
                                    ->groupBy('acc_account_ledger.id')
                                    ->get()->keyBy('id')->toArray();
                                    // dd($dateFrom, $dateTo);

        // current year information
        // current period voucher informations
        $currentPeriodVoucherDebitInfo = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>=', $dateFrom)
                                        ->where('av.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.debitAcc', $finalLevelLedgerIdArr);

        if ($project != "0") {
            $currentPeriodVoucherDebitInfo->where('av.projectId', $project);
        }
        if ($projectType != "0") {
            $currentPeriodVoucherDebitInfo->where('av.projectTypeId', $projectType);
        }
        // current period voucher debit
        $currentPeriodVoucherDebitInfo = $currentPeriodVoucherDebitInfo
                                        ->groupBy('acc_voucher_details.debitAcc')
                                        ->select(
                                            'acc_voucher_details.debitAcc as ledgerId',
                                            DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                        )
                                        ->get()->keyBy('ledgerId')->toArray();
                                        // dd($currentPeriodVoucherDebitInfo);

        $currentPeriodVoucherCreditInfo = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>=', $dateFrom)
                                        ->where('av.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.creditAcc', $finalLevelLedgerIdArr);

        if ($project != "0") {
            $currentPeriodVoucherCreditInfo->where('av.projectId', $project);
        }
        if ($projectType != "0") {
            $currentPeriodVoucherCreditInfo->where('av.projectTypeId', $projectType);
        }
        // current period voucher credit
        $currentPeriodVoucherCreditInfo = $currentPeriodVoucherCreditInfo
                                        ->groupBy('acc_voucher_details.creditAcc')
                                        ->select(
                                            'acc_voucher_details.creditAcc as ledgerId',
                                            DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                                        )
                                        ->get()->keyBy('ledgerId')->toArray();
                                        // dd($currentPeriodVoucherCreditInfo);

        // income and expense calculation
        // search type fiscal year
        if ($searchType == 1) {
            // income
            $finalLevelLedgersIncomeData = DB::table('acc_month_end_balance')
                                                    ->whereIn('ledgerId', $finalLevelIncomeLedgerIdArr)
                                                    ->whereIn('branchId', $branchIdArr)
                                                    // ->where('projectId', $project)
                                                    ->whereIn('fiscalYearId', [$fiscalYearId, $previousFiscalYearId])
                                                    ->where('monthEndDate', '<=', $dateTo);

            if ($project != "0") {
                $finalLevelLedgersIncomeData->where('projectId', $project);
            }
            if ($projectType != "0") {
                $finalLevelLedgersIncomeData->where('projectTypeId', $projectType);
            }

            $finalLevelLedgersIncomeData = $finalLevelLedgersIncomeData
                                                    ->select(
                                                        'fiscalYearId',
                                                        DB::raw('SUM(creditAmount)-SUM(debitAmount) as incomeBalance')
                                                    )
                                                    ->groupBy('fiscalYearId')
                                                    ->get();

            // expense
            $finalLevelLedgersExpenseData = DB::table('acc_month_end_balance')
                                                    ->whereIn('ledgerId', $finalLevelExpenseLedgerIdArr)
                                                    ->whereIn('branchId', $branchIdArr)
                                                    // ->where('projectId', $project)
                                                    ->whereIn('fiscalYearId', [$fiscalYearId, $previousFiscalYearId])
                                                    ->where('monthEndDate', '<=', $dateTo);

            if ($project != "0") {
                $finalLevelLedgersExpenseData->where('projectId', $project);
            }
            if ($projectType != "0") {
                $finalLevelLedgersExpenseData->where('projectTypeId', $projectType);
            }

            $finalLevelLedgersExpenseData = $finalLevelLedgersExpenseData
                                            ->select(
                                                'fiscalYearId',
                                                DB::raw('SUM(debitAmount)-SUM(creditAmount) as expenseBalance')
                                            )
                                            ->groupBy('fiscalYearId')
                                            ->get();

            // surplus calculation
            // previous year
            $previousYearSurplus = $finalLevelLedgersIncomeData->where('fiscalYearId', $previousFiscalYearId)->sum('incomeBalance') - $finalLevelLedgersExpenseData->where('fiscalYearId', $previousFiscalYearId)->sum('expenseBalance');
            //current year
            $currentYearSurplus = $finalLevelLedgersIncomeData->where('fiscalYearId', $fiscalYearId)->sum('incomeBalance') - $finalLevelLedgersExpenseData->where('fiscalYearId', $fiscalYearId)->sum('expenseBalance');

        }
        // search type current year
        elseif ($searchType == 2) {
            // previous year debit
            $previousYearDebitInfo = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>=', $previousFiscalYear->fyStartDate ?? '')
                                        ->where('av.voucherDate', '<=', $previousFiscalYear->fyEndDate ?? '')
                                        ->whereIn('acc_voucher_details.debitAcc', $finalLevelIncomeExpenseLedgerIdArr);

            if ($project != "0") {
                $previousYearDebitInfo->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $previousYearDebitInfo->where('av.projectTypeId', $projectType);
            }

            $previousYearDebitInfo = $previousYearDebitInfo
                                            ->groupBy('acc_voucher_details.debitAcc')
                                            ->select(
                                                'acc_voucher_details.debitAcc as ledgerId',
                                                DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                            )
                                            ->get()->keyBy('ledgerId')->toArray();

            // previous year credit
            $previousYearCreditInfo = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>=', $previousFiscalYear->fyStartDate ?? '')
                                        ->where('av.voucherDate', '<=', $previousFiscalYear->fyEndDate ?? '')
                                        ->whereIn('acc_voucher_details.creditAcc', $finalLevelIncomeExpenseLedgerIdArr);

            if ($project != "0") {
                $previousYearCreditInfo->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $previousYearCreditInfo->where('av.projectTypeId', $projectType);
            }

            $previousYearCreditInfo = $previousYearCreditInfo
                                            ->groupBy('acc_voucher_details.creditAcc')
                                            ->select(
                                                'acc_voucher_details.creditAcc as ledgerId',
                                                DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                                            )
                                            ->get()->keyBy('ledgerId')->toArray();

            // current year debit
            $currentYearDebitInfo = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>=', $dateFrom)
                                        ->where('av.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.debitAcc', $finalLevelIncomeExpenseLedgerIdArr);

            if ($project != "0") {
                $currentYearDebitInfo->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $currentYearDebitInfo->where('av.projectTypeId', $projectType);
            }

            $currentYearDebitInfo = $currentYearDebitInfo
                                            ->groupBy('acc_voucher_details.debitAcc')
                                            ->select(
                                                'acc_voucher_details.debitAcc as ledgerId',
                                                DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                            )
                                            ->get()->keyBy('ledgerId')->toArray();

            // current year credit
            $currentYearCreditInfo = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>=', $dateFrom)
                                        ->where('av.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.creditAcc', $finalLevelIncomeExpenseLedgerIdArr);

            if ($project != "0") {
                $currentYearCreditInfo->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $currentYearCreditInfo->where('av.projectTypeId', $projectType);
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
        

        $acc_opening_balance = $dbhelper->getUserPartitionWiseDBTableName('acc_opening_balance');
        // cumulative surplus calculation
        // income

        $cumulativeIncomeData = DB::table($acc_opening_balance)
                                ->whereIn('ledgerId', $finalLevelIncomeLedgerIdArr)
                                ->whereIn('branchId', $branchIdArr)
                                ->where('companyIdFk', $user_company_id)
                                ->whereIn('fiscalYearId', [$yearBeforePreviousYearId, $previousFiscalYearId]);

        if ($project != "0") {
            $cumulativeIncomeData->where('projectId', $project);
        }
        if ($projectType != "0") {
            $cumulativeIncomeData->where('projectTypeId', $projectType);
        }

        $cumulativeIncomeData = $cumulativeIncomeData
                                ->select(
                                    'fiscalYearId',
                                    DB::raw('SUM(creditAmount)-SUM(debitAmount) as incomeBalance')
                                )
                                ->groupBy('fiscalYearId')
                                ->get();
        // expense
        $cumulativeExpenseData = DB::table($acc_opening_balance)
                                ->whereIn('ledgerId', $finalLevelExpenseLedgerIdArr)
                                ->whereIn('branchId', $branchIdArr)
                                ->where('companyIdFk', $user_company_id)
                                ->whereIn('fiscalYearId', [$yearBeforePreviousYearId, $previousFiscalYearId]);

        if ($project != "0") {
            $cumulativeExpenseData->where('projectId', $project);
        }
        if ($projectType != "0") {
            $cumulativeExpenseData->where('projectTypeId', $projectType);
        }

        $cumulativeExpenseData = $cumulativeExpenseData
                                ->select(
                                    'fiscalYearId',
                                    DB::raw('SUM(debitAmount)-SUM(creditAmount) as expenseBalance')
                                )
                                ->groupBy('fiscalYearId')
                                ->get();

        // loop running after collecting all info
        foreach ($ledgersCollection as $key => $singleLedgerData) {

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

            // retained surplus calculation
            if ($singleLedgerData->id == $retainedSurplusLedgerId) {
                // previous year
                $previousYearBalance += $cumulativeIncomeData->where('fiscalYearId', $yearBeforePreviousYearId)->sum('incomeBalance') - $cumulativeExpenseData->where('fiscalYearId', $yearBeforePreviousYearId)->sum('expenseBalance');
                // current year
                $currentYearBalance += $cumulativeIncomeData->where('fiscalYearId', $previousFiscalYearId)->sum('incomeBalance') - $cumulativeExpenseData->where('fiscalYearId', $previousFiscalYearId)->sum('expenseBalance');
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
            $ledgerWiseSumArr[$ledger['id']] = $this->getRecursiveSum($ledgerWiseData, $ledger['id']);
        }
        // dd($ledgerWiseSumArr);
        $treeView = $this->buildTree(0, $ledgers, $ledgerWiseSumArr, $grandParentLedgerIdArr);

        // liability and equity calculation
        $totalLiabilities['previousYear'] = 0;
        $totalLiabilities['currentYear'] = 0;
        $totalCapitalFund['previousYear'] = 0;
        $totalCapitalFund['currentYear'] = 0;

        if(isset($ledgerWiseSumArr[$grandParentLedgerIdArr['liability']])){
            $totalLiabilities['previousYear'] += $ledgerWiseSumArr[$grandParentLedgerIdArr['liability']]['previousYearBalance'];
            $totalLiabilities['currentYear'] += $ledgerWiseSumArr[$grandParentLedgerIdArr['liability']]['currentYearBalance'];
            $totalCapitalFund['previousYear'] += $ledgerWiseSumArr[$grandParentLedgerIdArr['capitalFund']]['previousYearBalance'];
            $totalCapitalFund['currentYear'] += $ledgerWiseSumArr[$grandParentLedgerIdArr['capitalFund']]['currentYearBalance'];
        }

        // structuring data to send information in view file
        $data = array(
            'financialPositionLoadTableArr'         => $financialPositionLoadTableArr,
            'monthEndUnprocessedBranchesByMonth'    => $monthEndUnprocessedBranchesByMonth,
            'maxLevel'                              => $maxLevel,
            'treeView'                              => $treeView,
            'previousYearSurplus'                   => $previousYearSurplus,
            'currentYearSurplus'                    => $currentYearSurplus,
            'totalLiabilities'                      => $totalLiabilities,
            'totalCapitalFund'                      => $totalCapitalFund,
            'searchType'                            => $searchType,
            'withZero'                              => $withZero,
            'roundUp'                               => $roundUp,
            'depthLevel'                            => $depthLevel
        );

        return view('accounting.reports.financialPositionStatementViews.viewFinancialStatementTable', $data);
    }

    // function for all ledgers recursive sum
    public function getRecursiveSum($ledgerWiseData, $ledgerId) {

        $sum['previousYearBalance'] = 0;
        $sum['currentYearBalance'] = 0;
        $childs = $ledgerWiseData->where('parentId', $ledgerId);

        if ($childs->count() > 0) {
            foreach ($childs as $key => $child) {
                $sumArr = $this->getRecursiveSum($ledgerWiseData, $child['id']);
                $sum['previousYearBalance'] += $sumArr['previousYearBalance'];
                $sum['currentYearBalance'] += $sumArr['currentYearBalance'];
            }
        } else {
            $sum['previousYearBalance'] += $ledgerWiseData->where('id', $ledgerId)->sum('previousYearBalance');
            $sum['currentYearBalance'] += $ledgerWiseData->where('id', $ledgerId)->sum('currentYearBalance');
        }

        return $sum;
    }

    // function for html tree view with recursion
    public function buildTree($parents, $ledgers, $ledgerWiseSumArr, $grandParentLedgerIdArr) {

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

                if (!isset($ledgers['parents'][$ledgerId])) {

                    $html .= '<tr class="'.$trStyle.'">
                                <td style="text-align: left;">' .$ledgers['ledgers'][$ledgerId]['name']. '[' . $ledgers['ledgers'][$ledgerId]['code'] . ']</td>
                                <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['previousYearBalance'].'">'.
                                    number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['previousYearBalance'], 2).
                                '</td>
                                <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentYearBalance'] .'">
                                    '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentYearBalance'], 2) .
                                '</td>
                            </tr>';

                }

                if (isset($ledgers['parents'][$ledgerId])) {

                    $html .= '<tr class="'.$trStyle.'">
                                <td style="text-align: left;">' .$ledgers['ledgers'][$ledgerId]['name']. '[' . $ledgers['ledgers'][$ledgerId]['code'] . ']</td>
                                <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['previousYearBalance'].'">'.
                                    number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['previousYearBalance'], 2).
                                '</td>
                                <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentYearBalance'] .'">
                                    '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentYearBalance'], 2) .
                                '</td>
                            </tr>';

                    $html .= $this->buildTree($ledgerId, $ledgers, $ledgerWiseSumArr, $grandParentLedgerIdArr);

                    ///////// total income row ////////////
                    if ($ledgers['ledgers'][$ledgerId]['id'] == $grandParentLedgerIdArr['asset']) {

                        $html .= '<tr class="total text-bold">
                                    <td style="text-align: left;">TOTAL ASSET</td>
                                    <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['previousYearBalance'].'">'.
                                        number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['previousYearBalance'], 2).
                                    '</td>
                                    <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentYearBalance'] .'">
                                        '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentYearBalance'], 2) .
                                    '</td>
                                </tr>';
                    }

                }

            }

        }

        return $html;
    }

}
