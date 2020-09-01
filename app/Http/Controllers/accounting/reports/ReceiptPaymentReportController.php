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


class ReceiptPaymentReportController extends Controller{

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
        } else {
            $projects = ['0' => 'All'] + GnrProject::where('companyId', $user_company_id)->pluck('name', 'id')->toarray();
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
            ->select('name', 'id')->orderBy('id', 'desc')->get();

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
        } else {
            $branches = DB::table('gnr_branch')->where('companyId', $user_company_id)
            ->orderBy('branchCode')
            ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
            ->toArray();
            $branchLists = array("All" => "All", 0 => "All Branches") + $branches;
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
        $accReceiptPaymentArr = array(
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
            'userBranchStartDate'               => $userBranchStartDate,
            'userBranchData'                    => $userBranchData,
            'branchDate'                        => Carbon::parse($branchDate)->format('d-m-Y'),
        );
        // dd($accReceiptPaymentArr);

        return view('accounting.reports.receiptPaymentReportViews.viewReceiptPaymentForm', $accReceiptPaymentArr);
    }

    public static function getProjectType(Request $request) {

        $user_company_id = Auth::user()->company_id_fk;

        $projectTypes = DB::table('gnr_project_type')
                        ->select('name', 'id')
                        // ->where('companyId', $user_company_id)
                        ->where('projectId',$request->projectId)
                        ->get();

        $branches = DB::table('gnr_branch')
                    ->where('companyId', $user_company_id)
                    ->where('projectId', $request->projectId)
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

    public function receiptPaymentLoadReport(Request $request) {
        $dbhelper = new DatabasePartitionHelper(); 
        $acc_opening_balance = $dbhelper->getUserPartitionWiseDBTableName('acc_opening_balance');
        $acc_voucher = $dbhelper->getPartitionWiseDBTableNameForJoin('acc_voucher', 'av');
                               
        $user_company_id = Auth::user()->company_id_fk;
        $userBranchId = Auth::user()->branchId;
        $company = DB::table('gnr_company')->where('id', $user_company_id)->select('name','address')->first();

        // collecting request data
        // dd($request->all());
        $project = $request->filProject;
        $projectType = $request->filProjectType;
        $roundUp = $request->roundUp;
        $withZero = $request->withZero;
        $depthLevel = $request->depthLevel;
        $searchType = (int) $request->searchMethod;
        $voucherType = (int) $request->voucherType;
        $fiscalYearId = (int) $request->fiscalYearId;
        $reportLevel = $request->filReportLevel;
        $area = $request->filArea;
        $zone = $request->filZone;
        $region = $request->filRegion;
        $branch  = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->select('id', 'branchCode')->first();
        //dd($branch);
        if ($branch->branchCode != 0) {
            $branch = Auth::user()->branchId;
        } else {
            $branch = $request->filBranch;
        }
        //dd($branch);

        $branchIdArr = (int) $project == 0 ? DB::table('gnr_branch')->where('companyId', $user_company_id)->pluck('id')->toArray()
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
            ->select('id', 'name', 'fyStartDate', 'fyEndDate')
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
            } else {
                $branchNotStarted[] = $branchId;
            }

            if ($lastMonthEndDate != null) {
                if ($lastMonthEndDate >= $lastCommonMonthEndDate) {
                    $lastCommonMonthEndDate = $lastCommonMonthEndDate;
                } elseif ($lastMonthEndDate < $lastCommonMonthEndDate) {
                    $lastCommonMonthEndDate = $lastMonthEndDate;
                }
            }
        }
        // dd($lastCommonMonthEndDate);
        // fiscal year search
        if ($searchType == 1) {

            $dateFrom = DB::table('gnr_fiscal_year')->where('companyId', $user_company_id)->where('id', $fiscalYearId)->value('fyStartDate');
            $dateTo = DB::table('gnr_fiscal_year')->where('companyId', $user_company_id)->where('id', $fiscalYearId)->value('fyEndDate');
            $lastMonthEndDateAuth = DB::table('acc_month_end')->whereIn('branchIdFk', $branchIdArr)->max('date');

            if ($fiscalYearId == $currentFiscalYear->id) {
                // all branch
                if (count($branchIdArr) > 1) {
                    // collecting months unprocessed for all branches
                    if ($lastCommonMonthEndDate != $dateTo) {

                        $startDate = strtotime($lastCommonMonthEndDate);
                        $endDate = strtotime($lastMonthEndDateAuth);

                        $current = $startDate;
                        $monthsArr = [];

                        while ($current < $endDate) {
                            $value = Carbon::parse(date('Y-m-d', $current))->endOfMonth()->format('Y-m-d');
                            if ($lastCommonMonthEndDate != $value) {
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
                    } else {
                        $dateTo = $branchDate;
                    }
                }
                // dd($dateTo);

            }
        }
        // current year search
        elseif ($searchType == 2) {

            $dateTo = Carbon::parse($request->dateTo)->format('Y-m-d');
            $fiscalYear = DB::table('gnr_fiscal_year')->where('companyId', $user_company_id)->where('fyStartDate', '<=', $dateTo)->where('fyEndDate', '>=', $dateTo)->first();
            $fiscalYearId = $fiscalYear->id;
            $dateFrom = $fiscalYear->fyStartDate;
            $thisMonthStartDate = Carbon::parse($dateTo)->startOfMonth()->format('Y-m-d');
        }
        elseif($searchType == 3){
            $dateFrom = Carbon::parse($request->dateRangeFrom)->format('Y-m-d');
            $dateTo = Carbon::parse($request->dateRangeTo)->format('Y-m-d');
            $fiscalYear = DB::table('gnr_fiscal_year')->where('companyId', $user_company_id)->where('fyStartDate', '<=', $dateTo)->where('fyEndDate','>=', $dateTo)->first();
            $fiscalYearId = $fiscalYear->id;

        }

        $thisFiscalYearInfo = DB::table('gnr_fiscal_year')->where('companyId', $user_company_id)
                            ->where('fyStartDate', '<=', $dateFrom)
                            ->where('fyEndDate', '>=', $dateFrom)
                            ->select('id', 'name', 'fyStartDate', 'fyEndDate')
                            ->first();

        $previousFiscalYear = DB::table('gnr_fiscal_year')->where('companyId', $user_company_id)
                            ->where('fyStartDate', '<=', Carbon::parse($dateFrom)->subyear()->format('Y-m-d'))
                            ->where('fyEndDate', '>=', Carbon::parse($dateFrom)->subyear()->format('Y-m-d'))
                            ->first();

        $yearBeforePreviousYear = DB::table('gnr_fiscal_year')->where('companyId', $user_company_id)
                                ->where('fyStartDate', '<=', Carbon::parse($previousFiscalYear->fyStartDate ?? '')->subyear()->format('Y-m-d'))
                                ->where('fyEndDate', '>=', Carbon::parse($previousFiscalYear->fyStartDate ?? '')->subyear()->format('Y-m-d'))
                                ->first();

        $thisFiscalYearId = $thisFiscalYearInfo->id;
        $previousFiscalYearId = $previousFiscalYear->id ?? 0;
        $yearBeforePreviousYearId = $yearBeforePreviousYear->id ?? 0;
        // dd($fiscalYearId, $previousFiscalYearId, $yearBeforePreviousYearId);

        $lastYearEndDate = Carbon::parse($thisFiscalYearInfo->fyStartDate)->subdays(1)->format('Y-m-d');

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
        $receiptPaymentLoadTableArr = array(
            'company'                       => $company,
            'fiscalId'                      => $fiscalYearId,
            'dateFrom'                      => $dateFrom,
            'dateTo'                        => $dateTo,
            'thisFiscalYearName'            => $thisFiscalYearInfo->name,
            'previousFiscalYearName'        => $previousFiscalYear->name ?? '',
            'projectName'                   => $projectName,
            'projectType'                   => $projectTypeName,
            'branchName'                    => $branchName,
        );
        // dd($receiptPaymentLoadTableArr);

        // ledgers variable and collection
        $cashLedgerTypeId = 4;
        $bankLedgerTypeId = 5;
        $cashNBankBalanceLedgerIdArr = [DB::table('acc_account_ledger')->where('companyIdFk', $user_company_id)->where('code', 19000)->value('id')];

        if ($request->filBranch == 'All') {
            $withoutFundTr = 1;
        }
        else {
            $withoutFundTr = 0;
        }

        $service = new Service;
        $ledgersCollection = (int)$project == 0 ? DB::table('acc_account_ledger')
                                                ->where('status', 1)
                                                ->where('companyIdFk', $user_company_id)
                                                // ->where('code', 'NOT LIKE', '275%')
                                                // ->where('code', 'NOT LIKE', '276%')
                                                // ->where('code', 'NOT LIKE', '54%')
                                                ->select('id', 'name', 'code', 'accountTypeId', 'isGroupHead', 'level', 'parentId')
                                                ->orderBy('code')
                                                ->get()
                                                : $service->getLedgerHeaderInfos($project, $branchIdArr, $user_company_id, $withoutFundTr);
                                                // last optional parameter to remove fund transfer ledger
                                                // dd($ledgersCollection);

        $maxLevel = $ledgersCollection->max('level');

        $receiptAndPaymentLedgerIds = DB::table('acc_account_ledger')->whereNotIn('accountTypeId',[$cashLedgerTypeId,$bankLedgerTypeId])->whereNotIn('id', $cashNBankBalanceLedgerIdArr)->pluck('id')->toArray();
        $receiptAndPaymentLedgers = $ledgersCollection->whereIn('id', $receiptAndPaymentLedgerIds);
        $receiptAndPaymentLedgerIdArr = $receiptAndPaymentLedgers->pluck('id')->toArray();

        $cashLedgerIdArr  = $ledgersCollection->where('accountTypeId', $cashLedgerTypeId)->pluck('id')->toArray();
        $bankLedgerIdArr  = $ledgersCollection->where('accountTypeId', $bankLedgerTypeId)->pluck('id')->toArray();
        $cashNBankLedgerIdArr = array_merge($cashLedgerIdArr, $bankLedgerIdArr);
        $grandParentLedgerIdArr = $ledgersCollection->where('parentId', 0)->pluck('id', 'id')->toArray();
        // dd($grandParentLedgerIdArr);

        // calculation start
        // ============================initial variable============================
        // ===============cash and bank===============
        // search type fiscal year
        $previousYearOpeningBalanceCash = 0;
        $previousYearOpeningBalanceBank = 0;
        $currentYearOpeningBalanceCash = 0;
        $currentYearOpeningBalanceBank = 0;
        $previousYearClosingBalanceCash = 0;
        $previousYearClosingBalanceBank = 0;
        $currentYearClosingBalanceCash = 0;
        $currentYearClosingBalanceBank = 0;
        // search type current year
        $thisMonthOpeningCash = 0;
        $thisMonthOpeningBank = 0;
        $thisYearOpeningCash = 0;
        $thisYearOpeningBank = 0;
        $thisMonthClosingBalanceCash = $thisYearClosingBalanceCash = $cumulativeClosingBalanceCash = 0;
        $thisMonthClosingBalanceBank = $thisYearClosingBalanceBank = $cumulativeClosingBalanceBank = 0;
        // date range
        $dateRangeOpeningCash = 0;
        $dateRangeOpeningBank = 0;
        $dateRangeClosingBalanceCash = 0;
        $dateRangeClosingBalanceBank = 0;

        // ===============receipt payment=================
        // search type fiscal year
        $previousYearReceiptData = $previousYearPaymentData = $currentYearReceiptData = $currentYearPaymentData = [];
        // search type current year
        $yearEndData = [];
        $thisMonthReceipt = $thisMonthPayment = $thisYearReceipt = $thisYearPayment = [];
        // search type date range
        $dateRangeReceipt = $dateRangePayment = [];

        // =========================================calculation ========================================
        // search type fiscal year
        if ($searchType == 1) {
            // ==================================cash and bank calculation=====================================
            // op balance cash
            $opBalanceCash = DB::table($acc_opening_balance)
                                ->whereIn('ledgerId', $cashLedgerIdArr)
                                ->whereIn('branchId', $branchIdArr)
                                ->where('companyIdFk', $user_company_id)
                                ->whereIn('fiscalYearId', [$yearBeforePreviousYearId, $previousFiscalYearId, $thisFiscalYearId]);

            if ($project != "0") {
                $opBalanceCash->where('projectId', $project);
            }
            if ($projectType != "0") {
                $opBalanceCash->where('projectTypeId', $projectType);
            }

            $opBalanceCash = $opBalanceCash
                            ->select('fiscalYearId', DB::raw('SUM(balanceAmount) as cashBalance'))
                            ->groupBy('fiscalYearId')
                            ->get()->keyBy('fiscalYearId')->toArray();

            // op balance bank
            $opBalanceBank = DB::table($acc_opening_balance)
                                ->whereIn('ledgerId', $bankLedgerIdArr)
                                ->whereIn('branchId', $branchIdArr)
                                ->where('companyIdFk', $user_company_id)
                                ->whereIn('fiscalYearId', [$yearBeforePreviousYearId, $previousFiscalYearId, $thisFiscalYearId]);

            if ($project != "0") {
                $opBalanceBank->where('projectId', $project);
            }
            if ($projectType != "0") {
                $opBalanceBank->where('projectTypeId', $projectType);
            }

            $opBalanceBank = $opBalanceBank
                            ->select('fiscalYearId', DB::raw('SUM(balanceAmount) as bankBalance'))
                            ->groupBy('fiscalYearId')
                            ->get()->keyBy('fiscalYearId')->toArray();

            // current year
            $currentYearCash = 0;
            $currentYearBank = 0;
            // if search for running year
            if ($thisFiscalYearId == $currentFiscalYear->id) {
                // current year cash
                $currentYearCash = DB::table('acc_month_end_balance')
                                    ->whereIn('ledgerId', $cashLedgerIdArr)
                                    ->whereIn('branchId', $branchIdArr)
                                    // ->where('projectId', $project)
                                    ->where('fiscalYearId', $thisFiscalYearId)
                                    ->where('monthEndDate', '<=', $dateTo);

                if ($project != "0") {
                    $currentYearCash->where('projectId', $project);
                }
                if ($projectType != "0") {
                    $currentYearCash->where('projectTypeId', $projectType);
                }

                $currentYearCash = $currentYearCash
                                ->select(DB::raw('SUM(balanceAmount) as currentYearCash'))
                                ->value('currentYearCash');
                // current year bank
                $currentYearBank = DB::table('acc_month_end_balance')
                                    ->whereIn('ledgerId', $bankLedgerIdArr)
                                    ->whereIn('branchId', $branchIdArr)
                                    // ->where('projectId', $project)
                                    ->where('fiscalYearId', $thisFiscalYearId)
                                    ->where('monthEndDate', '<=', $dateTo);

                if ($project != "0") {
                    $currentYearBank->where('projectId', $project);
                }
                if ($projectType != "0") {
                    $currentYearBank->where('projectTypeId', $projectType);
                }

                $currentYearBank = $currentYearBank
                                ->select(DB::raw('SUM(balanceAmount) as currentYearBank'))
                                ->value('currentYearBank');
            }
            else {
                $currentYearCash = $opBalanceCash[$thisFiscalYearId]->cashBalance;
                $currentYearBank = $opBalanceBank[$thisFiscalYearId]->bankBalance;
            }

            // opening balance cash and bank
            //previousYear
            $previousYearOpeningBalanceCash = isset($opBalanceCash[$yearBeforePreviousYearId]) ? $opBalanceCash[$yearBeforePreviousYearId]->cashBalance : 0;
            $previousYearOpeningBalanceBank = isset($opBalanceBank[$yearBeforePreviousYearId]) ? $opBalanceBank[$yearBeforePreviousYearId]->bankBalance : 0;
            // current year
            $currentYearOpeningBalanceCash = isset($opBalanceCash[$previousFiscalYearId]) ? $opBalanceCash[$previousFiscalYearId]->cashBalance : 0;
            $currentYearOpeningBalanceBank = isset($opBalanceBank[$previousFiscalYearId]) ? $opBalanceBank[$previousFiscalYearId]->bankBalance : 0;

            // closing balance cash and bank
            $previousYearClosingBalanceCash = $currentYearOpeningBalanceCash;
            $previousYearClosingBalanceBank = $currentYearOpeningBalanceBank;
            // current year
            if ($thisFiscalYearId == $currentFiscalYear->id) {
                $currentYearClosingBalanceCash = $currentYearOpeningBalanceCash + $currentYearCash;
                $currentYearClosingBalanceBank = $currentYearOpeningBalanceBank + $currentYearBank;
            }
            else {
                $currentYearClosingBalanceCash = $currentYearCash;
                $currentYearClosingBalanceBank = $currentYearBank;
            }

            if ($voucherType == 2) {
                $previousYearOpeningBalanceCash = $previousYearOpeningBalanceBank = $currentYearOpeningBalanceCash = $currentYearOpeningBalanceBank = 0;
                $previousYearClosingBalanceCash = $previousYearClosingBalanceBank = $currentYearClosingBalanceCash = $currentYearClosingBalanceBank = 0;
            }

            // ===================================receipt payment calculation=================================
            // previous year receipt
            $previousYearReceiptData = DB::table('acc_month_end_balance')
                                        ->whereIn('ledgerId', $receiptAndPaymentLedgerIdArr)
                                        ->whereIn('branchId', $branchIdArr)
                                        // ->where('projectId', $project)
                                        ->where('fiscalYearId', $previousFiscalYearId);

            if ($project != "0") {
                $previousYearReceiptData->where('projectId', $project);
            }
            if ($projectType != "0") {
                $previousYearReceiptData->where('projectTypeId', $projectType);
            }

            $previousYearReceiptData = $previousYearReceiptData
                                        ->select(
                                            'ledgerId',
                                            DB::raw('SUM(cashCredit) as cashCredit'),
                                            DB::raw('SUM(bankCredit) as bankCredit'),
                                            DB::raw('SUM(jvCredit) as jvCredit'),
                                            DB::raw('SUM(ftCredit) as ftCredit')
                                        )
                                        ->groupBy('ledgerId')
                                        ->get()->keyBy('ledgerId')->toArray();

            // previous year payment
            $previousYearPaymentData = DB::table('acc_month_end_balance')
                                        ->whereIn('ledgerId', $receiptAndPaymentLedgerIdArr)
                                        ->whereIn('branchId', $branchIdArr)
                                        // ->where('projectId', $project)
                                        ->where('fiscalYearId', $previousFiscalYearId);

            if ($project != "0") {
                $previousYearPaymentData->where('projectId', $project);
            }
            if ($projectType != "0") {
                $previousYearPaymentData->where('projectTypeId', $projectType);
            }

            $previousYearPaymentData = $previousYearPaymentData
                                        ->select(
                                            'ledgerId',
                                            DB::raw('SUM(cashDebit) as cashDebit'),
                                            DB::raw('SUM(bankDebit) as bankDebit'),
                                            DB::raw('SUM(jvDebit) as jvDebit'),
                                            DB::raw('SUM(ftDebit) as ftDebit')
                                        )
                                        ->groupBy('ledgerId')
                                        ->get()->keyBy('ledgerId')->toArray();


            // current year receipt
            $currentYearReceiptData = DB::table('acc_month_end_balance')
                                        ->whereIn('ledgerId', $receiptAndPaymentLedgerIdArr)
                                        ->whereIn('branchId', $branchIdArr)
                                        // ->where('projectId', $project)
                                        ->where('fiscalYearId', $thisFiscalYearId);

            if ($project != "0") {
                $currentYearReceiptData->where('projectId', $project);
            }
            if ($projectType != "0") {
                $currentYearReceiptData->where('projectTypeId', $projectType);
            }
            // for running fiscal year
            if ($thisFiscalYearId == $currentFiscalYear->id) {
                $currentYearReceiptData->where('monthEndDate', '<=', $dateTo);
            }

            $currentYearReceiptData = $currentYearReceiptData
                                        ->select(
                                            'ledgerId',
                                            DB::raw('SUM(cashCredit) as cashCredit'),
                                            DB::raw('SUM(bankCredit) as bankCredit'),
                                            DB::raw('SUM(jvCredit) as jvCredit'),
                                            DB::raw('SUM(ftCredit) as ftCredit')
                                        )
                                        ->groupBy('ledgerId')
                                        ->get()->keyBy('ledgerId')->toArray();
                                        // dd($currentYearReceiptData[214]);

            // current year payment
            $currentYearPaymentData = DB::table('acc_month_end_balance')
                                        ->whereIn('ledgerId', $receiptAndPaymentLedgerIdArr)
                                        ->whereIn('branchId', $branchIdArr)
                                        // ->where('projectId', $project)
                                        ->where('fiscalYearId', $thisFiscalYearId);

            if ($project != "0") {
                $currentYearPaymentData->where('projectId', $project);
            }
            if ($projectType != "0") {
                $currentYearPaymentData->where('projectTypeId', $projectType);
            }
            // for running fiscal year
            if ($thisFiscalYearId == $currentFiscalYear->id) {
                $currentYearPaymentData->where('monthEndDate', '<=', $dateTo);
            }

            $currentYearPaymentData = $currentYearPaymentData
                                        ->select(
                                            'ledgerId',
                                            DB::raw('SUM(cashDebit) as cashDebit'),
                                            DB::raw('SUM(bankDebit) as bankDebit'),
                                            DB::raw('SUM(jvDebit) as jvDebit'),
                                            DB::raw('SUM(ftDebit) as ftDebit')
                                        )
                                        ->groupBy('ledgerId')
                                        ->get()->keyBy('ledgerId')->toArray();

            foreach ($receiptAndPaymentLedgers as $key => $singleLedgerData) {
                // without jv
                if ($voucherType == 0) {
                    // receipt
                    $previousYearReceiptLedgerValue = isset($previousYearReceiptData[$singleLedgerData->id])
                                                    ? ($previousYearReceiptData[$singleLedgerData->id]->cashCredit + $previousYearReceiptData[$singleLedgerData->id]->bankCredit )
                                                    : 0;
                    $currentYearReceiptLedgerValue = isset($currentYearReceiptData[$singleLedgerData->id])
                                                    ? ($currentYearReceiptData[$singleLedgerData->id]->cashCredit + $currentYearReceiptData[$singleLedgerData->id]->bankCredit )
                                                    : 0;
                    // payment
                    $previousYearPaymentLedgerValue = isset($previousYearPaymentData[$singleLedgerData->id])
                                                    ? ($previousYearPaymentData[$singleLedgerData->id]->cashDebit + $previousYearPaymentData[$singleLedgerData->id]->bankDebit )
                                                    : 0;
                    $currentYearPaymentLedgerValue = isset($currentYearPaymentData[$singleLedgerData->id])
                                                    ? ($currentYearPaymentData[$singleLedgerData->id]->cashDebit + $currentYearPaymentData[$singleLedgerData->id]->bankDebit )
                                                    : 0;
                }
                // with jv
                elseif ($voucherType == 1) {
                    // receipt
                    $previousYearReceiptLedgerValue = isset($previousYearReceiptData[$singleLedgerData->id])
                                                    ? ($previousYearReceiptData[$singleLedgerData->id]->cashCredit + $previousYearReceiptData[$singleLedgerData->id]->bankCredit + $previousYearReceiptData[$singleLedgerData->id]->jvCredit )
                                                    : 0;
                    $currentYearReceiptLedgerValue = isset($currentYearReceiptData[$singleLedgerData->id])
                                                    ? ($currentYearReceiptData[$singleLedgerData->id]->cashCredit + $currentYearReceiptData[$singleLedgerData->id]->bankCredit + $currentYearReceiptData[$singleLedgerData->id]->jvCredit )
                                                    : 0;
                    // payment
                    $previousYearPaymentLedgerValue = isset($previousYearPaymentData[$singleLedgerData->id])
                                                    ? ($previousYearPaymentData[$singleLedgerData->id]->cashDebit + $previousYearPaymentData[$singleLedgerData->id]->bankDebit + $previousYearPaymentData[$singleLedgerData->id]->jvDebit )
                                                    : 0;
                    $currentYearPaymentLedgerValue = isset($currentYearPaymentData[$singleLedgerData->id])
                                                    ? ($currentYearPaymentData[$singleLedgerData->id]->cashDebit + $currentYearPaymentData[$singleLedgerData->id]->bankDebit + $currentYearPaymentData[$singleLedgerData->id]->jvDebit )
                                                    : 0;
                }
                // only jv
                elseif ($voucherType == 2) {
                    // receipt
                    $previousYearReceiptLedgerValue = isset($previousYearReceiptData[$singleLedgerData->id])
                                                    ? ($previousYearReceiptData[$singleLedgerData->id]->jvCredit)
                                                    : 0;
                    $currentYearReceiptLedgerValue = isset($currentYearReceiptData[$singleLedgerData->id])
                                                    ? ($currentYearReceiptData[$singleLedgerData->id]->jvCredit)
                                                    : 0;
                    // payment
                    $previousYearPaymentLedgerValue = isset($previousYearPaymentData[$singleLedgerData->id])
                                                    ? ($previousYearPaymentData[$singleLedgerData->id]->jvDebit)
                                                    : 0;
                    $currentYearPaymentLedgerValue = isset($currentYearPaymentData[$singleLedgerData->id])
                                                    ? ($currentYearPaymentData[$singleLedgerData->id]->jvDebit)
                                                    : 0;
                }

                $ledgerWiseData[] = array(
                    'id'                            => $singleLedgerData->id,
                    'code'                          => $singleLedgerData->code,
                    'name'                          => $singleLedgerData->name,
                    'isGroupHead'                   => $singleLedgerData->isGroupHead,
                    'parentId'                      => $singleLedgerData->parentId,
                    'level'                         => $singleLedgerData->level,
                    'previousYearReceipt'           => $previousYearReceiptLedgerValue,
                    'currentYearReceipt'            => $currentYearReceiptLedgerValue,
                    'previousYearPayment'           => $previousYearPaymentLedgerValue,
                    'currentYearPayment'            => $currentYearPaymentLedgerValue
                );
            }
            // dd(collect($ledgerWiseData)->where('id', 214));

        }

        // search type current year
        elseif ($searchType == 2) {
            // =================================== year-end data ================================================
            // op balance cash
            $opBalanceCash = DB::table($acc_opening_balance)
                                ->whereIn('ledgerId', $cashLedgerIdArr)
                                ->whereIn('branchId', $branchIdArr)
                                ->where('companyIdFk', $user_company_id)
                                ->where('fiscalYearId', $previousFiscalYearId);

            if ($project != "0") {
                $opBalanceCash->where('projectId', $project);
            }
            if ($projectType != "0") {
                $opBalanceCash->where('projectTypeId', $projectType);
            }

            $opBalanceCash = $opBalanceCash->select(DB::raw('SUM(balanceAmount) as cashBalance'))->value('cashBalance');

            // op balance bank
            $opBalanceBank = DB::table($acc_opening_balance)
                                ->whereIn('ledgerId', $bankLedgerIdArr)
                                ->whereIn('branchId', $branchIdArr)
                                ->where('companyIdFk', $user_company_id)
                                ->where('fiscalYearId', $previousFiscalYearId);

            if ($project != "0") {
                $opBalanceBank->where('projectId', $project);
            }
            if ($projectType != "0") {
                $opBalanceBank->where('projectTypeId', $projectType);
            }

            $opBalanceBank = $opBalanceBank->select(DB::raw('SUM(balanceAmount) as bankBalance'))->value('bankBalance');

            //==============================this month opening cash================================
            //cash debit
            $thisMonthOpeningCashDebit = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>=', $dateFrom)
                                        ->where('av.voucherDate', '<', $thisMonthStartDate)
                                        ->whereIn('acc_voucher_details.debitAcc', $cashLedgerIdArr);

            if ($project != "0") {
                $thisMonthOpeningCashDebit->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $thisMonthOpeningCashDebit->where('av.projectTypeId', $projectType);
            }

            $thisMonthOpeningCashDebit = $thisMonthOpeningCashDebit
                                        ->select(DB::raw('SUM(acc_voucher_details.amount) as debitAmount'))->value('debitAmount');
            //cash credit
            $thisMonthOpeningCashCredit = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>=', $dateFrom)
                                        ->where('av.voucherDate', '<', $thisMonthStartDate)
                                        ->whereIn('acc_voucher_details.creditAcc', $cashLedgerIdArr);

            if ($project != "0") {
                $thisMonthOpeningCashCredit->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $thisMonthOpeningCashCredit->where('av.projectTypeId', $projectType);
            }

            $thisMonthOpeningCashCredit = $thisMonthOpeningCashCredit
                                        ->select(DB::raw('SUM(acc_voucher_details.amount) as creditAmount'))->value('creditAmount');

            // month opening cash balance
            $thisMonthOpeningCash = $thisMonthOpeningCashDebit - $thisMonthOpeningCashCredit;

            //==================================this month opening bank==================================
            //bank debit
            $thisMonthOpeningBankDebit = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>=', $dateFrom)
                                        ->where('av.voucherDate', '<', $thisMonthStartDate)
                                        ->whereIn('acc_voucher_details.debitAcc', $bankLedgerIdArr);

            if ($project != "0") {
                $thisMonthOpeningBankDebit->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $thisMonthOpeningBankDebit->where('av.projectTypeId', $projectType);
            }

            $thisMonthOpeningBankDebit = $thisMonthOpeningBankDebit
                                        ->select(DB::raw('SUM(acc_voucher_details.amount) as debitAmount'))->value('debitAmount');
            //bank credit
            $thisMonthOpeningBankCredit = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>=', $dateFrom)
                                        ->where('av.voucherDate', '<', $thisMonthStartDate)
                                        ->whereIn('acc_voucher_details.creditAcc', $bankLedgerIdArr);

            if ($project != "0") {
                $thisMonthOpeningBankCredit->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $thisMonthOpeningBankCredit->where('av.projectTypeId', $projectType);
            }

            $thisMonthOpeningBankCredit = $thisMonthOpeningBankCredit
                                        ->select(DB::raw('SUM(acc_voucher_details.amount) as creditAmount'))->value('creditAmount');

            // month opening bank balance
            $thisMonthOpeningBank = $thisMonthOpeningBankDebit - $thisMonthOpeningBankCredit;

            //====================================this month cash=======================================
            //cash debit
            $thisMonthCashDebit = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>=', $thisMonthStartDate)
                                        ->where('av.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.debitAcc', $cashLedgerIdArr);

            if ($project != "0") {
                $thisMonthCashDebit->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $thisMonthCashDebit->where('av.projectTypeId', $projectType);
            }

            $thisMonthCashDebit = $thisMonthCashDebit
                                        ->select(DB::raw('SUM(acc_voucher_details.amount) as debitAmount'))->value('debitAmount');
            //cash credit
            $thisMonthCashCredit = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>=', $thisMonthStartDate)
                                        ->where('av.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.creditAcc', $cashLedgerIdArr);

            if ($project != "0") {
                $thisMonthCashCredit->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $thisMonthCashCredit->where('av.projectTypeId', $projectType);
            }

            $thisMonthCashCredit = $thisMonthCashCredit
                                        ->select(DB::raw('SUM(acc_voucher_details.amount) as creditAmount'))->value('creditAmount');

            // cash balance
            $thisMonthCash = $thisMonthCashDebit - $thisMonthCashCredit;

            //===================================this month bank======================================
            //bank debit
            $thisMonthBankDebit = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>=', $thisMonthStartDate)
                                        ->where('av.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.debitAcc', $bankLedgerIdArr);

            if ($project != "0") {
                $thisMonthBankDebit->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $thisMonthBankDebit->where('av.projectTypeId', $projectType);
            }

            $thisMonthBankDebit = $thisMonthBankDebit
                                        ->select(DB::raw('SUM(acc_voucher_details.amount) as debitAmount'))->value('debitAmount');
            //bank credit
            $thisMonthBankCredit = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>=', $thisMonthStartDate)
                                        ->where('av.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.creditAcc', $bankLedgerIdArr);

            if ($project != "0") {
                $thisMonthBankCredit->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $thisMonthBankCredit->where('av.projectTypeId', $projectType);
            }

            $thisMonthBankCredit = $thisMonthBankCredit
                                        ->select(DB::raw('SUM(acc_voucher_details.amount) as creditAmount'))->value('creditAmount');

            // bank balance
            $thisMonthBank = $thisMonthBankDebit - $thisMonthBankCredit;

            //==================================cash and bank opening and closing calculation====================================
            /////////opening balance
            // cash
            $thisMonthOpeningCash = $opBalanceCash + $thisMonthOpeningCash;
            $thisYearOpeningCash = $opBalanceCash;
            //bank
            $thisMonthOpeningBank = $opBalanceBank + $thisMonthOpeningBank;
            $thisYearOpeningBank = $opBalanceBank;
            //////////closing Balance
            //cash
            $thisMonthClosingBalanceCash = $thisYearClosingBalanceCash = $cumulativeClosingBalanceCash = $thisMonthOpeningCash + $thisMonthCash;
            // bank
            $thisMonthClosingBalanceBank = $thisYearClosingBalanceBank = $cumulativeClosingBalanceBank = $thisMonthOpeningBank + $thisMonthBank;

            if ($voucherType == 2) {
                $thisMonthOpeningCash = $thisYearOpeningCash = $thisMonthOpeningBank = $thisYearOpeningBank = 0;
                $thisMonthClosingBalanceCash = $thisYearClosingBalanceCash = $cumulativeClosingBalanceCash = 0;
                $thisMonthClosingBalanceBank = $thisYearClosingBalanceBank = $cumulativeClosingBalanceBank = 0;
            }

            //==================================repceipt payment calculation====================================================
            //====================================this month data=======================================
            //================= payment
            $thisMonthPayment = DB::table($acc_voucher)
                                ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                ->where('av.companyId', $user_company_id)
                                // ->where('av.projectId', $project)
                                ->whereIn('av.branchId', $branchIdArr)
                                ->where('av.voucherDate', '>=', $thisMonthStartDate)
                                ->where('av.voucherDate', '<=', $dateTo)
                                // ->where('av.voucherTypeId', '!=', 5)
                                ->whereIn('acc_voucher_details.debitAcc', $receiptAndPaymentLedgerIdArr);

            if ($project != "0") {
                $thisMonthPayment->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $thisMonthPayment->where('av.projectTypeId', $projectType);
            }
            // voucher type condition apply
            if ($voucherType == 0) {
                $thisMonthPayment->where('av.voucherTypeId', '!=', 3)->whereIn('acc_voucher_details.creditAcc', $cashNBankLedgerIdArr);
            }
            elseif ($voucherType == 1) {
                $thisMonthPayment->where(function($q) use($cashNBankLedgerIdArr) {
                    $q->whereIn('acc_voucher_details.creditAcc', $cashNBankLedgerIdArr)
                        ->orWhere('av.voucherTypeId', 3);
                });
            }
            elseif ($voucherType == 2) {
                $thisMonthPayment->where('av.voucherTypeId', 3);
            }

            $thisMonthPayment = $thisMonthPayment
                                ->select('acc_voucher_details.debitAcc as ledgerId',
                                    DB::raw('SUM(acc_voucher_details.amount) as amount')
                                )
                                ->groupBy('ledgerId')
                                ->get()->keyBy('ledgerId')->toArray();
                                // dd($thisMonthPayment);

            //============= receipt
            $thisMonthReceipt = DB::table($acc_voucher)
                                ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                ->where('av.companyId', $user_company_id)
                                // ->where('av.projectId', $project)
                                ->whereIn('av.branchId', $branchIdArr)
                                ->where('av.voucherDate', '>=', $thisMonthStartDate)
                                ->where('av.voucherDate', '<=', $dateTo)
                                // ->where('av.voucherTypeId', '!=', 5)
                                ->whereIn('acc_voucher_details.creditAcc', $receiptAndPaymentLedgerIdArr);

            if ($project != "0") {
                $thisMonthReceipt->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $thisMonthReceipt->where('av.projectTypeId', $projectType);
            }
            // voucher type condition apply
            if ($voucherType == 0) {
                $thisMonthReceipt->where('av.voucherTypeId', '!=', 3)->whereIn('acc_voucher_details.debitAcc', $cashNBankLedgerIdArr);
            }
            elseif ($voucherType == 1) {
                $thisMonthReceipt->where(function($q) use($cashNBankLedgerIdArr) {
                    $q->whereIn('acc_voucher_details.debitAcc', $cashNBankLedgerIdArr)
                        ->orWhere('av.voucherTypeId', 3);
                });
            }
            elseif ($voucherType == 2) {
                $thisMonthReceipt->where('av.voucherTypeId', 3);
            }

            $thisMonthReceipt = $thisMonthReceipt
                                ->select('acc_voucher_details.creditAcc as ledgerId',
                                    DB::raw('SUM(acc_voucher_details.amount) as amount')
                                )
                                ->groupBy('ledgerId')
                                ->get()->keyBy('ledgerId')->toArray();

            //====================================this Year data=======================================
            //================= payment
            $thisYearPayment = DB::table($acc_voucher)
                                ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                ->where('av.companyId', $user_company_id)
                                // ->where('av.projectId', $project)
                                ->whereIn('av.branchId', $branchIdArr)
                                ->where('av.voucherDate', '>=', $dateFrom)
                                ->where('av.voucherDate', '<=', $dateTo)
                                // ->where('av.voucherTypeId', '!=', 5)
                                ->whereIn('acc_voucher_details.debitAcc', $receiptAndPaymentLedgerIdArr);

            if ($project != "0") {
                $thisYearPayment->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $thisYearPayment->where('av.projectTypeId', $projectType);
            }
            // voucher type condition apply
            if ($voucherType == 0) {
                $thisYearPayment->where('av.voucherTypeId', '!=', 3)->whereIn('acc_voucher_details.creditAcc', $cashNBankLedgerIdArr);
            }
            elseif ($voucherType == 1) {
                $thisYearPayment->where(function($q) use($cashNBankLedgerIdArr) {
                    $q->whereIn('acc_voucher_details.creditAcc', $cashNBankLedgerIdArr)
                        ->orWhere('av.voucherTypeId', 3);
                });
            }
            elseif ($voucherType == 2) {
                $thisYearPayment->where('av.voucherTypeId', 3);
            }

            $thisYearPayment = $thisYearPayment
                                ->select('acc_voucher_details.debitAcc as ledgerId',
                                    DB::raw('SUM(acc_voucher_details.amount) as amount')
                                )
                                ->groupBy('ledgerId')
                                ->get()->keyBy('ledgerId')->toArray();

            //============= receipt
            $thisYearReceipt = DB::table($acc_voucher)
                                ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                ->where('av.companyId', $user_company_id)
                                // ->where('av.projectId', $project)
                                ->whereIn('av.branchId', $branchIdArr)
                                ->where('av.voucherDate', '>=', $dateFrom)
                                ->where('av.voucherDate', '<=', $dateTo)
                                // ->where('av.voucherTypeId', '!=', 5)
                                ->whereIn('acc_voucher_details.creditAcc', $receiptAndPaymentLedgerIdArr);

            if ($project != "0") {
                $thisYearReceipt->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $thisYearReceipt->where('av.projectTypeId', $projectType);
            }
            // voucher type condition apply
            if ($voucherType == 0) {
                $thisYearReceipt->where('av.voucherTypeId', '!=', 3)->whereIn('acc_voucher_details.debitAcc', $cashNBankLedgerIdArr);
            }
            elseif ($voucherType == 1) {
                $thisYearReceipt->where(function($q) use($cashNBankLedgerIdArr) {
                    $q->whereIn('acc_voucher_details.debitAcc', $cashNBankLedgerIdArr)
                        ->orWhere('av.voucherTypeId', 3);
                });
            }
            elseif ($voucherType == 2) {
                $thisYearReceipt->where('av.voucherTypeId', 3);
            }

            $thisYearReceipt = $thisYearReceipt
                                ->select('acc_voucher_details.creditAcc as ledgerId',
                                    DB::raw('SUM(acc_voucher_details.amount) as amount')
                                )
                                ->groupBy('ledgerId')
                                ->get()->keyBy('ledgerId')->toArray();
                                // dd($thisMonthReceipt[328], $thisMonthPayment[328]);

            
            // cumulative data calculation
            $manualOpeningBalanceData = DB::table($acc_opening_balance)
                                        // ->whereIn('ledgerId', $cashNBankLedgerIdArr)
                                        ->whereIn('branchId', $branchIdArr)
                                        ->where('companyIdFk', $user_company_id)
                                        ->where('generateType', 1);

            if ($project != "0") {
                $manualOpeningBalanceData->where('projectId', $project);
            }
            if ($projectType != "0") {
                $manualOpeningBalanceData->where('projectTypeId', $projectType);
            }

            $manualOpeningBalanceData = $manualOpeningBalanceData->select(
                                            'ledgerId',
                                            DB::raw('SUM(debitAmount) as cashDebit'),
                                            DB::raw('SUM(creditAmount) as cashCredit')
                                        )
                                        ->groupBy('ledgerId')
                                        ->get()->keyBy('ledgerId')->toArray();
            // dd($manualOpeningBalanceData);

            $yearEndData = DB::table($acc_opening_balance)
                            ->whereIn('ledgerId', $receiptAndPaymentLedgerIdArr)
                            ->whereIn('branchId', $branchIdArr)
                            ->where('companyIdFk', $user_company_id)
                            ->where('fiscalYearId', $previousFiscalYearId);

            if ($project != "0") {
                $yearEndData->where('projectId', $project);
            }
            if ($projectType != "0") {
                $yearEndData->where('projectTypeId', $projectType);
            }

            $yearEndData = $yearEndData->select(
                            'ledgerId',
                            DB::raw('SUM(cashDebit) as cashDebit'),
                            DB::raw('SUM(bankDebit) as bankDebit'),
                            DB::raw('SUM(jvDebit) as jvDebit'),
                            DB::raw('SUM(ftDebit) as ftDebit'),
                            DB::raw('SUM(cashCredit) as cashCredit'),
                            DB::raw('SUM(bankCredit) as bankCredit'),
                            DB::raw('SUM(jvCredit) as jvCredit'),
                            DB::raw('SUM(ftCredit) as ftCredit')
                        )
                        ->groupBy('ledgerId')
                        ->get()->keyBy('ledgerId')->toArray();
                        // dd($yearEndData);

            // loop for final data
            foreach ($receiptAndPaymentLedgers as $key => $singleLedgerData) {
                // receipt
                //this month
                $thisMonthReceiptLedgerValue = isset($thisMonthReceipt[$singleLedgerData->id])
                                                ? $thisMonthReceipt[$singleLedgerData->id]->amount
                                                : 0;
                // this year
                $thisYearReceiptLedgerValue = isset($thisYearReceipt[$singleLedgerData->id])
                                                ? $thisYearReceipt[$singleLedgerData->id]->amount
                                                : 0;
                // payment
                // this month
                $thisMonthPaymentLedgerValue = isset($thisMonthPayment[$singleLedgerData->id])
                                                ? $thisMonthPayment[$singleLedgerData->id]->amount
                                                : 0;
                // this year
                $thisYearPaymentLedgerValue = isset($thisYearPayment[$singleLedgerData->id])
                                                ? $thisYearPayment[$singleLedgerData->id]->amount
                                                : 0;

                if ($voucherType == 0) {
                    $cummulativeReceiptLedgerValue = isset($yearEndData[$singleLedgerData->id])
                                                    ? ($yearEndData[$singleLedgerData->id]->cashCredit + $yearEndData[$singleLedgerData->id]->bankCredit + $thisYearReceiptLedgerValue)
                                                    : $thisYearReceiptLedgerValue;
                    // cumulative manual cash data
                    $cummulativeReceiptLedgerValue = isset($manualOpeningBalanceData[$singleLedgerData->id])
                                                    ? $cummulativeReceiptLedgerValue + $manualOpeningBalanceData[$singleLedgerData->id]->cashCredit
                                                    : $cummulativeReceiptLedgerValue;

                    $cummulativePaymentLedgerValue = isset($yearEndData[$singleLedgerData->id])
                                                    ? ($yearEndData[$singleLedgerData->id]->cashDebit + $yearEndData[$singleLedgerData->id]->bankDebit + $thisYearPaymentLedgerValue)
                                                    : $thisYearPaymentLedgerValue;

                    // cumulative manual cash data
                    $cummulativePaymentLedgerValue = isset($manualOpeningBalanceData[$singleLedgerData->id])
                                                    ? $cummulativePaymentLedgerValue + $manualOpeningBalanceData[$singleLedgerData->id]->cashDebit
                                                    : $cummulativePaymentLedgerValue;
                }
                elseif ($voucherType == 1) {

                    $cummulativeReceiptLedgerValue = isset($yearEndData[$singleLedgerData->id])
                                                    ? ($yearEndData[$singleLedgerData->id]->cashCredit + $yearEndData[$singleLedgerData->id]->bankCredit + $yearEndData[$singleLedgerData->id]->jvCredit + $thisYearReceiptLedgerValue)
                                                    : $thisYearReceiptLedgerValue;

                    // cumulative manual cash data
                    $cummulativeReceiptLedgerValue = isset($manualOpeningBalanceData[$singleLedgerData->id])
                                                    ? $cummulativeReceiptLedgerValue + $manualOpeningBalanceData[$singleLedgerData->id]->cashCredit
                                                    : $cummulativeReceiptLedgerValue;

                    $cummulativePaymentLedgerValue = isset($yearEndData[$singleLedgerData->id])
                                                    ? ($yearEndData[$singleLedgerData->id]->cashDebit + $yearEndData[$singleLedgerData->id]->bankDebit + $yearEndData[$singleLedgerData->id]->jvDebit + $thisYearPaymentLedgerValue)
                                                    : $thisYearPaymentLedgerValue;

                    // cumulative manual cash data
                    $cummulativePaymentLedgerValue = isset($manualOpeningBalanceData[$singleLedgerData->id])
                                                    ? $cummulativePaymentLedgerValue + $manualOpeningBalanceData[$singleLedgerData->id]->cashDebit
                                                    : $cummulativePaymentLedgerValue;

                }
                elseif ($voucherType == 2) {
                    $cummulativeReceiptLedgerValue = isset($yearEndData[$singleLedgerData->id])
                                                    ? $yearEndData[$singleLedgerData->id]->jvCredit + $thisYearReceiptLedgerValue
                                                    : $thisYearReceiptLedgerValue;
                    $cummulativePaymentLedgerValue = isset($yearEndData[$singleLedgerData->id])
                                                    ? $yearEndData[$singleLedgerData->id]->jvDebit + $thisYearPaymentLedgerValue
                                                    : $thisYearPaymentLedgerValue;
                }

                $ledgerWiseData[] = array(
                    'id'                            => $singleLedgerData->id,
                    'code'                          => $singleLedgerData->code,
                    'name'                          => $singleLedgerData->name,
                    'isGroupHead'                   => $singleLedgerData->isGroupHead,
                    'parentId'                      => $singleLedgerData->parentId,
                    'level'                         => $singleLedgerData->level,
                    'thisMonthReceipt'              => $thisMonthReceiptLedgerValue,
                    'thisMonthPayment'              => $thisMonthPaymentLedgerValue,
                    'thisYearReceipt'               => $thisYearReceiptLedgerValue,
                    'thisYearPayment'               => $thisYearPaymentLedgerValue,
                    'cumulativeReceipt'            => $cummulativeReceiptLedgerValue,
                    'cumulativePayment'            => $cummulativePaymentLedgerValue
                );
            }
            // dd(collect($ledgerWiseData)->where('id', 51));
        }

        // search type date range
        elseif ($searchType == 3) {
            // ==================================cash and bank calculation====================================
            // =================================== year-end data ================================================
            // op balance cash
            $opBalanceCash = DB::table($acc_opening_balance)
                                ->whereIn('ledgerId', $cashLedgerIdArr)
                                ->whereIn('branchId', $branchIdArr)
                                ->where('companyIdFk', $user_company_id)
                                ->where('fiscalYearId', $previousFiscalYearId);

            if ($project != "0") {
                $opBalanceCash->where('projectId', $project);
            }
            if ($projectType != "0") {
                $opBalanceCash->where('projectTypeId', $projectType);
            }

            $opBalanceCash = $opBalanceCash->select(DB::raw('SUM(balanceAmount) as cashBalance'))->value('cashBalance');

            // op balance bank
            $opBalanceBank = DB::table($acc_opening_balance)
                                ->whereIn('ledgerId', $bankLedgerIdArr)
                                ->whereIn('branchId', $branchIdArr)
                                ->where('companyIdFk', $user_company_id)
                                ->where('fiscalYearId', $previousFiscalYearId);

            if ($project != "0") {
                $opBalanceBank->where('projectId', $project);
            }
            if ($projectType != "0") {
                $opBalanceBank->where('projectTypeId', $projectType);
            }

            $opBalanceBank = $opBalanceBank->select(DB::raw('SUM(balanceAmount) as bankBalance'))->value('bankBalance');

            //==============================date range opening cash================================
            //cash debit
            $dateRangeOpeningCashDebit = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>', $lastYearEndDate)
                                        ->where('av.voucherDate', '<', $dateFrom)
                                        ->whereIn('acc_voucher_details.debitAcc', $cashLedgerIdArr);

            if ($project != "0") {
                $dateRangeOpeningCashDebit->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $dateRangeOpeningCashDebit->where('av.projectTypeId', $projectType);
            }

            $dateRangeOpeningCashDebit = $dateRangeOpeningCashDebit
                                        ->select(DB::raw('SUM(acc_voucher_details.amount) as debitAmount'))->value('debitAmount');
            //cash credit
            $dateRangeOpeningCashCredit = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>', $lastYearEndDate)
                                        ->where('av.voucherDate', '<', $dateFrom)
                                        ->whereIn('acc_voucher_details.creditAcc', $cashLedgerIdArr);

            if ($project != "0") {
                $dateRangeOpeningCashCredit->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $dateRangeOpeningCashCredit->where('av.projectTypeId', $projectType);
            }

            $dateRangeOpeningCashCredit = $dateRangeOpeningCashCredit
                                        ->select(DB::raw('SUM(acc_voucher_details.amount) as creditAmount'))->value('creditAmount');

            // date Range opening cash balance
            $dateRangeOpeningCash = $dateRangeOpeningCashDebit - $dateRangeOpeningCashCredit;

            //==================================date range opening bank==================================
            //bank debit
            $dateRangeOpeningBankDebit = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>', $lastYearEndDate)
                                        ->where('av.voucherDate', '<', $dateFrom)
                                        ->whereIn('acc_voucher_details.debitAcc', $bankLedgerIdArr);

            if ($project != "0") {
                $dateRangeOpeningBankDebit->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $dateRangeOpeningBankDebit->where('av.projectTypeId', $projectType);
            }

            $dateRangeOpeningBankDebit = $dateRangeOpeningBankDebit
                                        ->select(DB::raw('SUM(acc_voucher_details.amount) as debitAmount'))->value('debitAmount');
            //bank credit
            $dateRangeOpeningBankCredit = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>', $lastYearEndDate)
                                        ->where('av.voucherDate', '<', $dateFrom)
                                        ->whereIn('acc_voucher_details.creditAcc', $bankLedgerIdArr);

            if ($project != "0") {
                $dateRangeOpeningBankCredit->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $dateRangeOpeningBankCredit->where('av.projectTypeId', $projectType);
            }

            $dateRangeOpeningBankCredit = $dateRangeOpeningBankCredit
                                        ->select(DB::raw('SUM(acc_voucher_details.amount) as creditAmount'))->value('creditAmount');

            // month opening bank balance
            $dateRangeOpeningBank = $dateRangeOpeningBankDebit - $dateRangeOpeningBankCredit;

            //====================================date range cash=======================================
            //cash debit
            $dateRangeCashDebit = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>=', $dateFrom)
                                        ->where('av.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.debitAcc', $cashLedgerIdArr);

            if ($project != "0") {
                $dateRangeCashDebit->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $dateRangeCashDebit->where('av.projectTypeId', $projectType);
            }

            $dateRangeCashDebit = $dateRangeCashDebit
                                        ->select(DB::raw('SUM(acc_voucher_details.amount) as debitAmount'))->value('debitAmount');
            //cash credit
            $dateRangeCashCredit = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>=', $dateFrom)
                                        ->where('av.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.creditAcc', $cashLedgerIdArr);

            if ($project != "0") {
                $dateRangeCashCredit->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $dateRangeCashCredit->where('av.projectTypeId', $projectType);
            }

            $dateRangeCashCredit = $dateRangeCashCredit
                                        ->select(DB::raw('SUM(acc_voucher_details.amount) as creditAmount'))->value('creditAmount');

            // cash balance
            $dateRangeCash = $dateRangeCashDebit - $dateRangeCashCredit;

            //===================================date range bank======================================
            //bank debit
            $dateRangeBankDebit = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>=', $dateFrom)
                                        ->where('av.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.debitAcc', $bankLedgerIdArr);

            if ($project != "0") {
                $dateRangeBankDebit->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $dateRangeBankDebit->where('av.projectTypeId', $projectType);
            }

            $dateRangeBankDebit = $dateRangeBankDebit
                                        ->select(DB::raw('SUM(acc_voucher_details.amount) as debitAmount'))->value('debitAmount');
            //bank credit
            $dateRangeBankCredit = DB::table($acc_voucher)
                                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('av.companyId', $user_company_id)
                                        // ->where('av.projectId', $project)
                                        ->whereIn('av.branchId', $branchIdArr)
                                        ->where('av.voucherDate', '>=', $dateFrom)
                                        ->where('av.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.creditAcc', $bankLedgerIdArr);

            if ($project != "0") {
                $dateRangeBankCredit->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $dateRangeBankCredit->where('av.projectTypeId', $projectType);
            }

            $dateRangeBankCredit = $dateRangeBankCredit
                                        ->select(DB::raw('SUM(acc_voucher_details.amount) as creditAmount'))->value('creditAmount');

            // bank balance
            $dateRangeBank = $dateRangeBankDebit - $dateRangeBankCredit;

            //==================================cash and bank opening and closing calculation====================================
            /////////opening balance
            // cash
            $dateRangeOpeningCash = $opBalanceCash + $dateRangeOpeningCash;
            //bank
            $dateRangeOpeningBank = $opBalanceBank + $dateRangeOpeningBank;
            //////////closing Balance
            //cash
            $dateRangeClosingBalanceCash = $dateRangeOpeningCash + $dateRangeCash;
            // bank
            $dateRangeClosingBalanceBank = $dateRangeOpeningBank + $dateRangeBank;

            if ($voucherType == 2) {
                $dateRangeOpeningCash = $dateRangeOpeningBank = $dateRangeClosingBalanceCash = $dateRangeClosingBalanceBank = 0;
            }

            // =====================================date range receipt payment calculation =================================
            //================= receipt
            $dateRangeReceipt = DB::table($acc_voucher)
                                ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                ->where('av.companyId', $user_company_id)
                                // ->where('av.projectId', $project)
                                ->whereIn('av.branchId', $branchIdArr)
                                ->where('av.voucherDate', '>=', $dateFrom)
                                ->where('av.voucherDate', '<=', $dateTo)
                                // ->where('av.voucherTypeId', '!=', 5)
                                ->whereIn('acc_voucher_details.creditAcc', $receiptAndPaymentLedgerIdArr);

            if ($project != "0") {
                $dateRangeReceipt->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $dateRangeReceipt->where('av.projectTypeId', $projectType);
            }
            // voucher type condition apply
            if ($voucherType == 0) {
                $dateRangeReceipt->where('av.voucherTypeId', '!=', 3)->whereIn('acc_voucher_details.debitAcc', $cashNBankLedgerIdArr);
            }
            elseif ($voucherType == 1) {
                $dateRangeReceipt->where(function($q) use($cashNBankLedgerIdArr) {
                    $q->whereIn('acc_voucher_details.debitAcc', $cashNBankLedgerIdArr)
                        ->orWhere('av.voucherTypeId', 3);
                });
            }
            elseif ($voucherType == 2) {
                $dateRangeReceipt->where('av.voucherTypeId', 3);
            }

            $dateRangeReceipt = $dateRangeReceipt
                                ->select('acc_voucher_details.creditAcc as ledgerId',
                                    DB::raw('SUM(acc_voucher_details.amount) as amount')
                                )
                                ->groupBy('ledgerId')
                                ->get()->keyBy('ledgerId')->toArray();

            //============= payment
            $dateRangePayment = DB::table($acc_voucher)
                                ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                ->where('av.companyId', $user_company_id)
                                // ->where('av.projectId', $project)
                                ->whereIn('av.branchId', $branchIdArr)
                                ->where('av.voucherDate', '>=', $dateFrom)
                                ->where('av.voucherDate', '<=', $dateTo)
                                // ->where('av.voucherTypeId', '!=', 5)
                                ->whereIn('acc_voucher_details.debitAcc', $receiptAndPaymentLedgerIdArr);

            if ($project != "0") {
                $dateRangePayment->where('av.projectId', $project);
            }
            if ($projectType != "0") {
                $dateRangePayment->where('av.projectTypeId', $projectType);
            }
            // voucher type condition apply
            if ($voucherType == 0) {
                $dateRangePayment->where('av.voucherTypeId', '!=', 3)->whereIn('acc_voucher_details.creditAcc', $cashNBankLedgerIdArr);
            }
            elseif ($voucherType == 1) {
                $dateRangePayment->where(function($q) use($cashNBankLedgerIdArr) {
                    $q->whereIn('acc_voucher_details.creditAcc', $cashNBankLedgerIdArr)
                        ->orWhere('av.voucherTypeId', 3);
                });
            }
            elseif ($voucherType == 2) {
                $dateRangePayment->where('av.voucherTypeId', 3);
            }

            $dateRangePayment = $dateRangePayment
                                ->select('acc_voucher_details.debitAcc as ledgerId',
                                    DB::raw('SUM(acc_voucher_details.amount) as amount')
                                )
                                ->groupBy('ledgerId')
                                ->get()->keyBy('ledgerId')->toArray();
           
            // loop for final data
            foreach ($receiptAndPaymentLedgers as $key => $singleLedgerData) {
                // receipt
                $dateRangeReceiptLedgerValue = isset($dateRangeReceipt[$singleLedgerData->id])
                                                ? $dateRangeReceipt[$singleLedgerData->id]->amount
                                                : 0;
                // payment
                $dateRangePaymentLedgerValue = isset($dateRangePayment[$singleLedgerData->id])
                                                ? $dateRangePayment[$singleLedgerData->id]->amount
                                                : 0;

                $ledgerWiseData[] = array(
                    'id'                            => $singleLedgerData->id,
                    'code'                          => $singleLedgerData->code,
                    'name'                          => $singleLedgerData->name,
                    'isGroupHead'                   => $singleLedgerData->isGroupHead,
                    'parentId'                      => $singleLedgerData->parentId,
                    'level'                         => $singleLedgerData->level,
                    'dateRangeReceipt'              => $dateRangeReceiptLedgerValue,
                    'dateRangePayment'              => $dateRangePaymentLedgerValue
                );
            }
            // dd(collect($ledgerWiseData)->where('id', 51));
            // dd($ledgerWiseData);
        }
        //dd($ledgerWiseData);
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
        // dd($ledgerWiseSumArr);
        // total receipt payment calculation
        $totalReceipt = [
                            'previousYear' => 0,
                            'currentYear' => 0,
                            'thisMonth' => 0,
                            'thisYear' => 0,
                            'cumulative' => 0,
                            'dateRange' => 0
                        ];
        $totalPayment = [
                            'previousYear' => 0,
                            'currentYear' => 0,
                            'thisMonth' => 0,
                            'thisYear' => 0,
                            'cumulative' => 0,
                            'dateRange' => 0
                        ];

        foreach ($grandParentLedgerIdArr as $key => $ledgerId) {

            // receipt
            $totalReceipt['previousYear'] += $ledgerWiseSumArr[$ledgerId]['previousYearReceipt'];
            $totalReceipt['currentYear'] += $ledgerWiseSumArr[$ledgerId]['currentYearReceipt'];
            $totalReceipt['thisMonth'] += $ledgerWiseSumArr[$ledgerId]['thisMonthReceipt'];
            $totalReceipt['thisYear'] += $ledgerWiseSumArr[$ledgerId]['thisYearReceipt'];
            $totalReceipt['cumulative'] += $ledgerWiseSumArr[$ledgerId]['cumulativeReceipt'];
            $totalReceipt['dateRange'] += $ledgerWiseSumArr[$ledgerId]['dateRangeReceipt'];
            // payment
            $totalPayment['previousYear'] += $ledgerWiseSumArr[$ledgerId]['previousYearPayment'];
            $totalPayment['currentYear'] += $ledgerWiseSumArr[$ledgerId]['currentYearPayment'];
            $totalPayment['thisMonth'] += $ledgerWiseSumArr[$ledgerId]['thisMonthPayment'];
            $totalPayment['thisYear'] += $ledgerWiseSumArr[$ledgerId]['thisYearPayment'];
            $totalPayment['cumulative'] += $ledgerWiseSumArr[$ledgerId]['cumulativePayment'];
            $totalPayment['dateRange'] += $ledgerWiseSumArr[$ledgerId]['dateRangePayment'];
        }

        // generate html tree
        $receiptTreeView = $this->buildTree(0, $ledgers, $ledgerWiseSumArr, $searchType, $treeType = 'Receipt');
        $paymentTreeView = $this->buildTree(0, $ledgers, $ledgerWiseSumArr, $searchType, $treeType = 'Payment');

        // structuring data to send information in view file
        $data = array(
            'receiptPaymentLoadTableArr'            => $receiptPaymentLoadTableArr,
            'monthEndUnprocessedBranchesByMonth'    => $monthEndUnprocessedBranchesByMonth,
            'searchType'                            => $searchType,
            'withZero'                              => $withZero,
            'roundUp'                               => $roundUp,
            'depthLevel'                            => $depthLevel,
            'maxLevel'                              => $maxLevel,
            // ==========================table data=============================
            // ============cash and bank=======
            // search type fiscal year
            'previousYearOpeningBalanceCash'        => $previousYearOpeningBalanceCash,
            'previousYearOpeningBalanceBank'        => $previousYearOpeningBalanceBank,
            'currentYearOpeningBalanceCash'         => $currentYearOpeningBalanceCash,
            'currentYearOpeningBalanceBank'         => $currentYearOpeningBalanceBank,
            'previousYearClosingBalanceCash'        => $previousYearClosingBalanceCash,
            'previousYearClosingBalanceBank'        => $previousYearClosingBalanceBank,
            'currentYearClosingBalanceCash'         => $currentYearClosingBalanceCash,
            'currentYearClosingBalanceBank'         => $currentYearClosingBalanceBank,
            // search type current year
            'thisMonthOpeningCash'                  => $thisMonthOpeningCash,
            'thisMonthOpeningBank'                  => $thisMonthOpeningBank,
            'thisYearOpeningCash'                   => $thisYearOpeningCash,
            'thisYearOpeningBank'                   => $thisYearOpeningBank,
            'thisMonthClosingBalanceCash'           => $thisMonthClosingBalanceCash,
            'thisYearClosingBalanceCash'            => $thisYearClosingBalanceCash,
            'cumulativeClosingBalanceCash'          => $cumulativeClosingBalanceCash,
            'thisMonthClosingBalanceBank'           => $thisMonthClosingBalanceBank,
            'thisYearClosingBalanceBank'            => $thisYearClosingBalanceBank,
            'cumulativeClosingBalanceBank'          => $cumulativeClosingBalanceBank,
            // search type date range
            'dateRangeOpeningCash'                  => $dateRangeOpeningCash,
            'dateRangeOpeningBank'                  => $dateRangeOpeningBank,
            'dateRangeClosingBalanceCash'           => $dateRangeClosingBalanceCash,
            'dateRangeClosingBalanceBank'           => $dateRangeClosingBalanceBank,
            // ===============receipt payment tree===========
            'totalReceipt'                          => $totalReceipt,
            'totalPayment'                          => $totalPayment,
            'receiptTreeView'                       => $receiptTreeView,
            'paymentTreeView'                       => $paymentTreeView,
        );
        // dd($data);

        return view('accounting.reports.receiptPaymentReportViews.viewReceiptPaymentTable', $data);
    }

    // function for all ledgers recursive sum
    public function getRecursiveSum($ledgerWiseData, $ledgerId, $searchType) {

        $sum['previousYearReceipt'] = 0;
        $sum['currentYearReceipt'] = 0;
        $sum['previousYearPayment'] = 0;
        $sum['currentYearPayment'] = 0;
        $sum['thisMonthReceipt'] = 0;
        $sum['thisYearReceipt'] = 0;
        $sum['cumulativeReceipt'] = 0;
        $sum['thisMonthPayment'] = 0;
        $sum['thisYearPayment'] = 0;
        $sum['cumulativePayment'] = 0;
        $sum['dateRangeReceipt'] = 0;
        $sum['dateRangePayment'] = 0;

        $childs = $ledgerWiseData->where('parentId', $ledgerId);

        //////////// search type fiscal year ////////////
        if ($searchType == 1) {
            if ($childs->count() > 0) {
                foreach ($childs as $key => $child) {
                    $sumArr = $this->getRecursiveSum($ledgerWiseData, $child['id'], $searchType);
                    $sum['previousYearReceipt'] += $sumArr['previousYearReceipt'];
                    $sum['previousYearPayment'] += $sumArr['previousYearPayment'];
                    $sum['currentYearReceipt'] += $sumArr['currentYearReceipt'];
                    $sum['currentYearPayment'] += $sumArr['currentYearPayment'];
                }
            } else {
                $sum['previousYearReceipt'] += $ledgerWiseData->where('id', $ledgerId)->sum('previousYearReceipt');
                $sum['previousYearPayment'] += $ledgerWiseData->where('id', $ledgerId)->sum('previousYearPayment');
                $sum['currentYearReceipt'] += $ledgerWiseData->where('id', $ledgerId)->sum('currentYearReceipt');
                $sum['currentYearPayment'] += $ledgerWiseData->where('id', $ledgerId)->sum('currentYearPayment');
            }

        }
        //////////// search type current year ////////////
        elseif ($searchType == 2) {

            if ($childs->count() > 0) {
                foreach ($childs as $key => $child) {
                    $sumArr = $this->getRecursiveSum($ledgerWiseData, $child['id'], $searchType);
                    $sum['thisMonthReceipt'] += $sumArr['thisMonthReceipt'];
                    $sum['thisYearReceipt'] += $sumArr['thisYearReceipt'];
                    $sum['cumulativeReceipt'] += $sumArr['cumulativeReceipt'];
                    $sum['thisMonthPayment'] += $sumArr['thisMonthPayment'];
                    $sum['thisYearPayment'] += $sumArr['thisYearPayment'];
                    $sum['cumulativePayment'] += $sumArr['cumulativePayment'];
                }
            } else {
                $sum['thisMonthReceipt'] += $ledgerWiseData->where('id', $ledgerId)->sum('thisMonthReceipt');
                $sum['thisYearReceipt'] += $ledgerWiseData->where('id', $ledgerId)->sum('thisYearReceipt');
                $sum['cumulativeReceipt'] += $ledgerWiseData->where('id', $ledgerId)->sum('cumulativeReceipt');
                $sum['thisMonthPayment'] += $ledgerWiseData->where('id', $ledgerId)->sum('thisMonthPayment');
                $sum['thisYearPayment'] += $ledgerWiseData->where('id', $ledgerId)->sum('thisYearPayment');
                $sum['cumulativePayment'] += $ledgerWiseData->where('id', $ledgerId)->sum('cumulativePayment');
            }

        }
        //////////// search type date range ////////////
        elseif ($searchType == 3) {

            if ($childs->count() > 0) {
                foreach ($childs as $key => $child) {
                    $sumArr = $this->getRecursiveSum($ledgerWiseData, $child['id'], $searchType);
                    $sum['dateRangeReceipt'] += $sumArr['dateRangeReceipt'];
                    $sum['dateRangePayment'] += $sumArr['dateRangePayment'];
                }
            } else {
                $sum['dateRangeReceipt'] += $ledgerWiseData->where('id', $ledgerId)->sum('dateRangeReceipt');
                $sum['dateRangePayment'] += $ledgerWiseData->where('id', $ledgerId)->sum('dateRangePayment');
            }

        }

        return $sum;
    }

    // function for html tree view with recursion
    public function buildTree($parents, $ledgers, $ledgerWiseSumArr, $searchType, $treeType) {

        $html = "";
        $previousYearValue = 'previousYear'. $treeType;
        $currentYearValue = 'currentYear'. $treeType;
        $thisMonthValue = 'thisMonth'. $treeType;
        $thisYearValue = 'thisYear'. $treeType;
        $cumulativeValue = 'cumulative'. $treeType;
        $dateRangeValue = 'dateRange'. $treeType;

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
                                    <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$previousYearValue].'">'.
                                        number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$previousYearValue], 2).
                                    '</td>
                                    <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$currentYearValue] .'">
                                        '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$currentYearValue], 2) .
                                    '</td>
                                </tr>';

                    }

                    if (isset($ledgers['parents'][$ledgerId])) {

                        $html .= '<tr class="'.$trStyle.'">
                                    <td style="text-align: left;">' .$ledgers['ledgers'][$ledgerId]['name']. '[' . $ledgers['ledgers'][$ledgerId]['code'] . ']</td>
                                    <td></td>
                                    <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$previousYearValue].'">'.
                                        number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$previousYearValue], 2).
                                    '</td>
                                    <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$currentYearValue] .'">
                                        '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$currentYearValue], 2) .
                                    '</td>
                                </tr>';

                        $html .= $this->buildTree($ledgerId, $ledgers, $ledgerWiseSumArr, $searchType, $treeType);

                    }

                }
                ///////////// search type current year ///////////////
                elseif ($searchType == 2) {

                    if (!isset($ledgers['parents'][$ledgerId])) {

                        $html .= '<tr class="'.$trStyle.'">
                                    <td style="text-align: left;">' .$ledgers['ledgers'][$ledgerId]['name']. '[' . $ledgers['ledgers'][$ledgerId]['code'] . ']</td>
                                    <td></td>
                                    <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$thisMonthValue].'">'.
                                        number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$thisMonthValue], 2).
                                    '</td>
                                    <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$thisYearValue] .'">
                                        '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$thisYearValue], 2) .
                                    '</td>
                                    <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$cumulativeValue] .'">
                                        '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$cumulativeValue], 2) .
                                    '</td>
                                </tr>';

                    }

                    if (isset($ledgers['parents'][$ledgerId])) {

                        $html .= '<tr class="'.$trStyle.'">
                                    <td style="text-align: left;">' .$ledgers['ledgers'][$ledgerId]['name']. '[' . $ledgers['ledgers'][$ledgerId]['code'] . ']</td>
                                    <td></td>
                                    <td class="amount" data-amount="' . $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$thisMonthValue].'">'.
                                        number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$thisMonthValue], 2).
                                    '</td>
                                    <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$thisYearValue] .'">
                                        '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$thisYearValue], 2) .
                                    '</td>
                                    <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$cumulativeValue] .'">
                                        '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$cumulativeValue], 2) .
                                    '</td>
                                </tr>';

                        $html .= $this->buildTree($ledgerId, $ledgers, $ledgerWiseSumArr, $searchType, $treeType);

                    }

                }
                ///////// serach type date range ///////////
                elseif ($searchType == 3) {

                    if (!isset($ledgers['parents'][$ledgerId])) {

                        $html .= '<tr class="'.$trStyle.'">
                                    <td style="text-align: left;">' .$ledgers['ledgers'][$ledgerId]['name']. '[' . $ledgers['ledgers'][$ledgerId]['code'] . ']</td>
                                    <td></td>
                                    <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$dateRangeValue] .'">
                                        '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$dateRangeValue], 2) .
                                    '</td>
                                </tr>';

                    }

                    if (isset($ledgers['parents'][$ledgerId])) {

                        $html .= '<tr class="'.$trStyle.'">
                                    <td style="text-align: left;">' .$ledgers['ledgers'][$ledgerId]['name']. '[' . $ledgers['ledgers'][$ledgerId]['code'] . ']</td>
                                    <td></td>
                                    <td class="amount" data-amount="'. $ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$dateRangeValue] .'">
                                        '.number_format($ledgerWiseSumArr[$ledgers['ledgers'][$ledgerId]['id']][$dateRangeValue], 2) .
                                    '</td>
                                </tr>';

                        $html .= $this->buildTree($ledgerId, $ledgers, $ledgerWiseSumArr, $searchType, $treeType);

                    }
                }

            }

        }

        return $html;
    }

}
