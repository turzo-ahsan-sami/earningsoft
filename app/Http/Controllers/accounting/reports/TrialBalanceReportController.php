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
use App\accounting\process\AccMonthEnd;
use App\Service\DatabasePartitionHelper;
use Illuminate\Support\Collection;


class TrialBalanceReportController extends Controller{

    public function index(Request $request) {

        $userBranchId = Auth::user()->branchId;
        $userCompanyId = Auth::user()->company_id_fk;

        $userBranchInfo = DB::table('gnr_branch')->where('id', $userBranchId)->first();
        $userBranch = Auth::user()->branch;
        //dd($branch);
        if ($userBranch->branchCode == 0) {
            $projects = ['All'] + DB::table('gnr_project')->where('companyId', $userCompanyId)->pluck('name','id')->toarray();
            // $userBranchStartDate = DB::table('gnr_branch')->where('companyId', $userCompanyId)->min('aisStartDate');
            // $branchDate = DB::table('acc_day_end')->max('date');
            $userBranchStartDate = GnrBranch::where('id', $userBranch->id)->value('aisStartDate');
            $lastMonthEndDate = AccMonthEnd::active()->where('branchIdFk', $userBranch->id)->max('date');
            if ($lastMonthEndDate) {
                $branchDate = Carbon::parse($lastMonthEndDate)->addDay()->endOfMonth()->format('Y-m-d');
            } else {
                $branchDate = Carbon::parse($userBranchStartDate)->endOfMonth()->format('Y-m-d');
            }
        }
        else {
            $projects = GnrProject::where('id', $userBranch->projectId)->pluck('name', 'id')->toarray();
            $userBranchStartDate = DB::table('gnr_branch')->where('id', $userBranchId)->value('aisStartDate');
            $branchDate = DB::table('acc_day_end')->where('branchIdFk', $userBranchId)->max('date');
        }

        //dd($branchDate);

        $branchDate = $branchDate == null ? $userBranchStartDate : $branchDate;
        $fiscalYears = DB::table('gnr_fiscal_year')
        ->where('companyId', $userCompanyId)
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
        $branchLists = DB::table('gnr_branch')->where('id', $userBranchId);

       if ($userBranch->branchCode != 0) {
            $branchLists = $branchLists->where('id', $userBranchId);
        }
        else {
            $branches = DB::table('gnr_branch')->where('companyId', $userCompanyId)
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
       // dd($userBranchData);
        $accTrialBalanceArr = array(
            'reportLevelList'           => $reportLevelList,
            'areaList'                  => $areaList,
            'zoneList'                  => $zoneList,
            'regionList'                => $regionList,
            'branchLists'               => $branchLists,
            'userBarnchId'              => $userBranchId,
            'userBranchCode'            => $userBranch->branchCode,
            'projects'                  => $projects,
            'fiscalYears'               => $fiscalYears,
            'userBranchData'            => $userBranchData,
            'userBranchStartDate'       => $userBranchStartDate,
            'branchDate'                => Carbon::parse($branchDate)->format('d-m-Y'),
        );
        // dd($accTrialBalanceArr);

        return view('accounting.reports.trialBalanceViews.viewTrialBalanceFilterForm', $accTrialBalanceArr);
    }

    public function trialBalanceProjectType(Request $request) {
        $userBranchId = Auth::user()->branchId;
        $userCompanyId = Auth::user()->company_id_fk;

        $projectTypes = DB::table('gnr_project_type')->select('name','id')
        ->where('companyId', $userCompanyId)
        ->where('projectId',$request->projectId)
        ->get();

        $branches = DB::table('gnr_branch')
        // ->where('id', $userBranchId)
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

    public function trialBalanceLoadReport(Request $request) {
        $dbhelper = new DatabasePartitionHelper();        
        $partitionName = DatabasePartitionHelper::getCompanyWisePartitionName(Auth::user()->company_id_fk);
        $acc_opening_balance = DatabasePartitionHelper::getPartitionWiseDBTableNameForJoin('acc_opening_balance', 'ob');   
        //$acc_voucher = DatabasePartitionHelper::getUserPartitionWiseDBTableName('acc_voucher');
        $acc_voucher = DatabasePartitionHelper::getPartitionWiseDBTableNameForJoin('acc_voucher', 'av');
        
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
        $branch  = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->select('id','branchCode')->first();
        //dd($branch);
        if ($branch->branchCode != 0) {
            $branch = $userBranchId;
        }
        else {
            $branch = $request->filBranch;
        }

        $area = $request->filArea;
        $zone = $request->filZone;
        $region = $request->filRegion;
        // dd((int)$branch);

        $branchIdArr = (int)$project == 0 ? DB::table('gnr_branch')->pluck('id')->toArray()
                                            : Service::getFilteredBranchIds($project, $reportLevel, $branch, $area, $zone, $region);
        // dd(count($branchIdArr));

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
            $fyStartDate = $dateFrom;
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
            $dateFrom = Carbon::parse($request->dateFrom)->format('Y-m-d');
            $fiscalYear = DB::table('gnr_fiscal_year')->where('fyStartDate', '<=', $dateFrom)->where('fyEndDate','>=', $dateFrom)->first();
            $fiscalYearId = $fiscalYear->id;
            $fyStartDate = $fiscalYear->fyStartDate;
        }

        $lastOpeningDate = Carbon::parse($fyStartDate)->subdays(1)->format('Y-m-d');

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
        $trialBalanceLoadTableArr = array(
            'company'                 => $company,
            'fiscalId'                => $fiscalYearId,
            'dateFrom'                => $dateFrom,
            'dateTo'                  => $dateTo,
            'projectName'             => $projectName,
            'projectType'             => $projectTypeName,
            'branchName'              => $branchName,
        );
        // dd($branchIdArr);

        // ledgers variable and collection
        $service = new Service;
        $ledgersCollection = (int)$project == 0 ? DB::table('acc_account_ledger')
                                                    ->where('status', 1)
                                                    ->where('companyIdFk', $user_company_id)
                                                    ->select('id', 'name', 'code', 'accountTypeId', 'isGroupHead', 'level', 'parentId')
                                                    ->orderBy('code')
                                                    ->get()
                                                : $service->getLedgerHeaderInfos($project, $branchIdArr, $user_company_id);
                                                // dd($ledgersCollection);

        $finalLevelLedgerIdArr  = $ledgersCollection->where('isGroupHead', 0)->pluck('id')->toArray();
        $maxLevel = $ledgersCollection->max('level');

        $totalOpDebit = 0;
        $totalOpCredit = 0;
        $totalCurrentDebit = 0;
        $totalCurrentCredit = 0;
        $totalCumulativeDebit = 0;
        $totalCumulativeCredit = 0;

        // final level ledgers opening balance informations
        $finalLevelLedgersOpData= DB::table('acc_account_ledger')
                                ->leftJoin($acc_opening_balance, 'ob.ledgerId', '=', 'acc_account_ledger.id')
                                ->whereIn('acc_account_ledger.id', $finalLevelLedgerIdArr)
                                ->whereIn('ob.branchId', $branchIdArr)
                                ->where('ob.companyIdFk', $user_company_id)
                                ->where('ob.openingDate', $lastOpeningDate);
        
        // project filter
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
        // dd($finalLevelLedgersOpData);

        if ($searchType == 2) {
            // opening voucher informations
            $opVoucherDebitInfo = DB::table($acc_voucher)
                                ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                ->where('av.companyId', $user_company_id)
                                // ->where('av.projectId', $project)
                                ->whereIn('av.branchId', $branchIdArr)
                                ->where('av.voucherDate', '>', $lastOpeningDate)
                                ->where('av.voucherDate', '<', $dateFrom)
                                ->whereIn('acc_voucher_details.debitAcc', $finalLevelLedgerIdArr);

            if ($project != "0") {
                $opVoucherDebitInfo->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $opVoucherDebitInfo->where('av.projectTypeId', $projectType);
            }

            //debit
            $opVoucherDebitInfo = $opVoucherDebitInfo
                                ->groupBy('acc_voucher_details.debitAcc')
                                ->select(
                                    'acc_voucher_details.debitAcc as ledgerId',
                                    DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                )
                                ->get()->keyBy('ledgerId')->toArray();
                                // dd($opVoucherDebitInfo);
            // credit
            $opVoucherCreditInfo = DB::table($acc_voucher)
                                ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                ->where('av.companyId', $user_company_id)
                                // ->where('av.projectId', $project)
                                ->whereIn('av.branchId', $branchIdArr)
                                ->where('av.voucherDate', '>', $lastOpeningDate)
                                ->where('av.voucherDate', '<', $dateFrom)
                                ->whereIn('acc_voucher_details.creditAcc', $finalLevelLedgerIdArr);

            if ($project != "0") {
                $opVoucherCreditInfo->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $opVoucherCreditInfo->where('av.projectTypeId', $projectType);
            }

            $opVoucherCreditInfo = $opVoucherCreditInfo
                                ->groupBy('acc_voucher_details.creditAcc')
                                ->select(
                                    'acc_voucher_details.creditAcc as ledgerId',
                                    DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                                )
                                ->get()->keyBy('ledgerId')->toArray();

            // current period voucher informations
            $currentPeriodVoucherDebitInfo = DB::table($acc_voucher)
                                            ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                            ->where('av.companyId', $user_company_id)
                                            // ->where('av.projectId', $project)
                                            ->whereIn('av.branchId', $branchIdArr)
                                            ->where('av.voucherDate', '>=', $dateFrom)
                                            ->where('av.voucherDate', '<=', $dateTo)
                                            ->whereIn('acc_voucher_details.debitAcc', $finalLevelLedgerIdArr);

            if ($projectType != "0") {
                $currentPeriodVoucherDebitInfo->where('av.projectTypeId', $projectType);
            }
            if ($project != "0") {
                $currentPeriodVoucherDebitInfo->where('av.projectId', $project);
            }
            // current period voucher debit
            $currentPeriodVoucherDebitInfo = $currentPeriodVoucherDebitInfo
                                            ->groupBy('acc_voucher_details.debitAcc')
                                            ->select(
                                                'acc_voucher_details.debitAcc as ledgerId',
                                                DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                            )
                                            ->get()->keyBy('ledgerId')->toArray();

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

        }
        else {
            // current year information
            $finalLevelLedgersCurrentData = DB::table('acc_account_ledger')
                                        ->join('acc_month_end_balance', 'acc_month_end_balance.ledgerId', '=', 'acc_account_ledger.id')
                                        ->whereIn('acc_account_ledger.id', $finalLevelLedgerIdArr)
                                        ->whereIn('acc_month_end_balance.branchId', $branchIdArr)
                                        // ->where('acc_month_end_balance.projectId', $project)
                                        ->where('acc_month_end_balance.monthEndDate', '>', $lastOpeningDate)
                                        ->where('acc_month_end_balance.monthEndDate', '<=', $dateTo);

            if ($project != "0") {
                $finalLevelLedgersCurrentData->where('acc_month_end_balance.projectId', $project);
            }
            if ($projectType != "0") {
                $finalLevelLedgersCurrentData->where('acc_month_end_balance.projectTypeId', $projectType);
            }

            $finalLevelLedgersCurrentData = $finalLevelLedgersCurrentData
                                            ->select('acc_account_ledger.id',
                                                DB::raw('SUM(acc_month_end_balance.debitAmount) as totalCurrentDebitAmount'),
                                                DB::raw('SUM(acc_month_end_balance.creditAmount) as totalCurrentCreditAmount')
                                            )
                                            ->groupBy('acc_account_ledger.id')
                                            ->get()->keyBy('id')->toArray();
        }

        // loop running after collecting all info
        foreach ($ledgersCollection as $key => $singleLedgerData) {

            $ledgerType = Service::checkLedgerAccountType($singleLedgerData->id);

            // // opening balance
            $openingDebitAmount = isset($finalLevelLedgersOpData[$singleLedgerData->id])
                                    ?$finalLevelLedgersOpData[$singleLedgerData->id]->totalOpDebitAmount
                                    : 0;
            $openingCreditAmount = isset($finalLevelLedgersOpData[$singleLedgerData->id])
                                    ? $finalLevelLedgersOpData[$singleLedgerData->id]->totalOpCreditAmount
                                    : 0;

            if ($searchType == 2) {
                 // for date range collect voucher info before date from
                if (isset($opVoucherDebitInfo[$singleLedgerData->id])) {
                    $openingDebitAmount += $opVoucherDebitInfo[$singleLedgerData->id]->debitAmount;
                }

                if (isset($opVoucherCreditInfo[$singleLedgerData->id])) {
                    $openingCreditAmount += $opVoucherCreditInfo[$singleLedgerData->id]->creditAmount;
                }

            }

            // balance calculation
            if ($ledgerType == true) {
                $openingDebitBalance = $openingDebitAmount - $openingCreditAmount;
                $openingCreditBalance = 0;

                if ($openingDebitBalance < 0) {
                    $openingCreditBalance = abs($openingDebitBalance);
                    $openingDebitBalance = 0;
                }
            }
            elseif($ledgerType == false) {
                $openingCreditBalance = $openingCreditAmount - $openingDebitAmount;
                $openingDebitBalance = 0;

                if ($openingCreditBalance < 0) {
                    $openingDebitBalance = abs($openingCreditBalance);
                    $openingCreditBalance = 0;
                }
            }
            // end of opening balance

            // balance during date range/ current period
            $currentPeriodDebitAmount = 0;
            $currentPeriodCreditAmount = 0;

            if ($searchType == 1) {

                if (isset($finalLevelLedgersCurrentData[$singleLedgerData->id])) {
                    $currentPeriodDebitAmount += $finalLevelLedgersCurrentData[$singleLedgerData->id]->totalCurrentDebitAmount;
                    $currentPeriodCreditAmount += $finalLevelLedgersCurrentData[$singleLedgerData->id]->totalCurrentCreditAmount;
                }

            }
            else {

                if (isset($currentPeriodVoucherDebitInfo[$singleLedgerData->id])) {
                    $currentPeriodDebitAmount += $currentPeriodVoucherDebitInfo[$singleLedgerData->id]->debitAmount;
                }

                if (isset($currentPeriodVoucherCreditInfo[$singleLedgerData->id])) {
                    $currentPeriodCreditAmount += $currentPeriodVoucherCreditInfo[$singleLedgerData->id]->creditAmount;
                }

            }

            // cumulative
            $cumulativeDebitAmount = $openingDebitAmount + $currentPeriodDebitAmount;
            $cumulativeCreditAmount = $openingCreditAmount + $currentPeriodCreditAmount;

            // balance calculation
            if ($ledgerType == true) {
                $cumulativeDebitBalance = $cumulativeDebitAmount - $cumulativeCreditAmount;
                $cumulativeCreditBalance = 0;

                if ($cumulativeDebitBalance < 0) {
                    $cumulativeCreditBalance = abs($cumulativeDebitBalance);
                    $cumulativeDebitBalance = 0;
                }
            }
            elseif($ledgerType == false) {
                $cumulativeCreditBalance = $cumulativeCreditAmount - $cumulativeDebitAmount;
                $cumulativeDebitBalance = 0;

                if ($cumulativeCreditBalance < 0) {
                    $cumulativeDebitBalance = abs($cumulativeCreditBalance);
                    $cumulativeCreditBalance = 0;
                }
            }

            $totalOpDebit += $openingDebitBalance;
            $totalOpCredit += $openingCreditBalance;
            $totalCurrentDebit += $currentPeriodDebitAmount;
            $totalCurrentCredit += $currentPeriodCreditAmount;
            $totalCumulativeDebit += $cumulativeDebitBalance;
            $totalCumulativeCredit += $cumulativeCreditBalance;

            // checking round up
            if ((int)$roundUp == 1) {
                $openingDebitBalance = round($openingDebitBalance);
                $openingCreditBalance = round($openingCreditBalance);
                $currentPeriodDebitAmount = round($currentPeriodDebitAmount);
                $currentPeriodCreditAmount = round($currentPeriodCreditAmount);
                $cumulativeDebitBalance = round($cumulativeDebitBalance);
                $cumulativeCreditBalance = round($cumulativeCreditBalance);
            }

            // final ledger information array
            $ledgerWiseData[] = array(
                'id'                            => $singleLedgerData->id,
                'code'                          => $singleLedgerData->code,
                'name'                          => $singleLedgerData->name,
                'isGroupHead'                   => $singleLedgerData->isGroupHead,
                'parentId'                      => $singleLedgerData->parentId,
                'level'                         => $singleLedgerData->level,
                'openingDebit'                  => $openingDebitBalance,
                'openingCredit'                 => $openingCreditBalance,
                'currentPeriodDebitAmount'      => $currentPeriodDebitAmount,
                'currentPeriodCreditAmount'     => $currentPeriodCreditAmount,
                'cumulativeDebitAmount'         => $cumulativeDebitBalance,
                'cumulativeCreditAmount'        => $cumulativeCreditBalance,
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

        // generate html tree
        $treeView = $this->buildTree(0, $ledgers, $ledgerWiseSumArr);

        // total balance
        $totalBalanceArr = array(
            'totalOpDebit'              => $totalOpDebit,
            'totalOpCredit'             => $totalOpCredit,
            'totalCurrentDebit'         => $totalCurrentDebit,
            'totalCurrentCredit'        => $totalCurrentCredit,
            'totalCumulativeDebit'      => $totalCumulativeDebit,
            'totalCumulativeCredit'     => $totalCumulativeCredit
        );
        // structuring data to send information in view file
        $data = array(
            'trialBalanceLoadTableArr'              => $trialBalanceLoadTableArr,
            'maxLevel'                              => $maxLevel,
            'treeView'                              => $treeView,
            'totalBalanceArr'                       => $totalBalanceArr,
            'monthEndUnprocessedBranchesByMonth'    => $monthEndUnprocessedBranchesByMonth,
            'searchType'                            => $searchType,
            'withZero'                              => $withZero,
            'roundUp'                               => $roundUp,
            'depthLevel'                            => $depthLevel
        );
        // dd($data);

        return view('accounting.reports.trialBalanceViews.viewTrialBalanceReportTable', $data);
    }

    // function for all ledgers recursive sum
    public function getRecursiveSum($ledgerWiseData, $ledgerId) {

        $sum['openingDebit'] = 0;
        $sum['openingCredit'] = 0;
        $sum['currentPeriodDebitAmount'] = 0;
        $sum['currentPeriodCreditAmount'] = 0;
        $sum['cumulativeDebitAmount'] = 0;
        $sum['cumulativeCreditAmount'] = 0;
        $childs = $ledgerWiseData->where('parentId', $ledgerId);

        if ($childs->count() > 0) {
            foreach ($childs as $key => $child) {
                $sumArr = $this->getRecursiveSum($ledgerWiseData, $child['id']);
                $sum['openingDebit'] += $sumArr['openingDebit'];
                $sum['openingCredit'] += $sumArr['openingCredit'];
                $sum['currentPeriodDebitAmount'] += $sumArr['currentPeriodDebitAmount'];
                $sum['currentPeriodCreditAmount'] += $sumArr['currentPeriodCreditAmount'];
                $sum['cumulativeDebitAmount'] += $sumArr['cumulativeDebitAmount'];
                $sum['cumulativeCreditAmount'] += $sumArr['cumulativeCreditAmount'];
            }
        } else {
            $sum['openingDebit'] += $ledgerWiseData->where('id', $ledgerId)->sum('openingDebit');
            $sum['openingCredit'] += $ledgerWiseData->where('id', $ledgerId)->sum('openingCredit');
            $sum['currentPeriodDebitAmount'] += $ledgerWiseData->where('id', $ledgerId)->sum('currentPeriodDebitAmount');
            $sum['currentPeriodCreditAmount'] += $ledgerWiseData->where('id', $ledgerId)->sum('currentPeriodCreditAmount');
            $sum['cumulativeDebitAmount'] += $ledgerWiseData->where('id', $ledgerId)->sum('cumulativeDebitAmount');
            $sum['cumulativeCreditAmount'] += $ledgerWiseData->where('id', $ledgerId)->sum('cumulativeCreditAmount');
        }

        return $sum;
    }

    // function for html tree view with recursion
    public function buildTree($parents, $ledgers, $ledgerWiseSumArr) {

        $html = "";

        if (isset($ledgers['parents'][$parents])) {

            foreach ($ledgers['parents'][$parents] as $ledgerId) {

                if ($ledgers['ledgers'][$ledgerId]['level'] > 4) {

                    if ($ledgers['ledgers'][$ledgerId]['isGroupHead'] == 0) {
                        $trStyle = 'ledgerTr level level-final level-constant';
                    }
                    else {
                        $trStyle = 'ledgerTr level level-constant level-'. $ledgers['ledgers'][$ledgerId]['level'];
                    }
                }
                else {
                    if ($ledgers['ledgers'][$ledgerId]['isGroupHead'] == 0) {
                        $trStyle = 'ledgerTr level level-final level-constant';
                    }
                    else {
                        $trStyle = 'ledgerTr level level-'. $ledgers['ledgers'][$ledgerId]['level'];
                    }
                }

                if (!isset($ledgers['parents'][$ledgerId])) {

                    $html .= '<tr class="'.$trStyle.'">
                                <td style="text-align: left;">' .$ledgers['ledgers'][$ledgerId]['name']. '[' . $ledgers['ledgers'][$ledgerId]['code'] . ']</td>
                                <td></td>
                                <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['openingDebit'].'">'.
                                    number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['openingDebit'], 2).
                                '</td>
                                <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['openingCredit'] .'">
                                    '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['openingCredit'], 2) .
                                '</td>
                                <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentPeriodDebitAmount'].'">'.
                                    number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentPeriodDebitAmount'], 2).
                                '</td>
                                <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentPeriodCreditAmount'] .'">
                                    '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentPeriodCreditAmount'], 2) .
                                '</td>
                                <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['cumulativeDebitAmount'].'">'.
                                    number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['cumulativeDebitAmount'], 2).
                                '</td>
                                <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['cumulativeCreditAmount'] .'">
                                    '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['cumulativeCreditAmount'], 2) .
                                '</td>
                            </tr>';

                }

                if (isset($ledgers['parents'][$ledgerId])) {

                    $html .= '<tr class="'.$trStyle.'">
                                <td style="text-align: left;">' .$ledgers['ledgers'][$ledgerId]['name']. '[' . $ledgers['ledgers'][$ledgerId]['code'] . ']</td>
                                <td></td>
                                <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['openingDebit'].'">'.
                                    number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['openingDebit'], 2).
                                '</td>
                                <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['openingCredit'] .'">
                                    '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['openingCredit'], 2) .
                                '</td>
                                <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentPeriodDebitAmount'].'">'.
                                    number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentPeriodDebitAmount'], 2).
                                '</td>
                                <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentPeriodCreditAmount'] .'">
                                    '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['currentPeriodCreditAmount'], 2) .
                                '</td>
                                <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['cumulativeDebitAmount'].'">'.
                                    number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['cumulativeDebitAmount'], 2).
                                '</td>
                                <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['cumulativeCreditAmount'] .'">
                                    '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']]['cumulativeCreditAmount'], 2) .
                                '</td>
                            </tr>';

                    $html .= $this->buildTree($ledgerId, $ledgers, $ledgerWiseSumArr);

                }

            }

        }

        return $html;
    }

}
