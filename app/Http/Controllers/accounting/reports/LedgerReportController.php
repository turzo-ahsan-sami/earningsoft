<?php

namespace App\Http\Controllers\accounting\reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\accounting\AddVoucherType;
use App\accounting\process\AccMonthEnd;
use App\gnr\GnrBranch;
use App\gnr\GnrProject;
use Auth;
use DB;
use Carbon\Carbon;
use App\Service\Service;

use App\Service\DatabasePartitionHelper;
use Illuminate\Support\Collection;

class LedgerReportController extends Controller {

    public function index() {        
        $userBranchId = Auth::user()->branchId;
        $userCompanyId = Auth::user()->company_id_fk;
        $companyBranches = GnrBranch::where('companyId', $userCompanyId)->pluck('id')->toArray();
        $userBranch = Auth::user()->branch;
        $userBranchData = DB::table('gnr_branch')
                        ->where('id', $userBranchId)
                        ->select('id', DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"))
                        ->first();
        $branchLists = array();
        $projectTypes = array();

        // if ($userBranch->branchCode != 0) {
        //     $projects = ['0' => 'All'] + DB::table('gnr_project')->where('companyId', $userCompanyId)->pluck('name','id')->toarray();
        // }
        // else {
        //     $projects = ['0' => 'All'] + DB::table('gnr_project')->where('companyId', $userCompanyId)->pluck('name','id')->toarray();
        //     $projectTypes = DB::table('gnr_project_type')->where('companyId', $userCompanyId)->pluck('name','id')->toarray();
        //     $branchLists = DB::table('gnr_branch')
        //                     ->where('id', $userBranchId)
        //                     ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
        //                     ->toArray();
        // }

        if ($userBranch->branchCode != 0) {
            $projects = GnrProject::where('id', $userBranch->projectId)->pluck('name', 'id')->toarray();
            $projectTypes = DB::table('gnr_project_type')->where('projectId', $userBranch->projectId)->pluck('name', 'id')->toarray();
            $branchLists = DB::table('gnr_branch')
                            ->where('id', $userBranchId)
                            ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                            ->toArray();
            $userBranchStartDate = GnrBranch::where('id', $userBranch->id)->value('aisStartDate');
            $lastMonthEndDate = AccMonthEnd::active()->where('branchIdFk', $userBranch->id)->max('date');
            if ($lastMonthEndDate) {
                $userBranchDate = Carbon::parse($lastMonthEndDate)->addDay()->endOfMonth()->format('Y-m-d');
            } else {
                $userBranchDate = Carbon::parse($userBranchStartDate)->endOfMonth()->format('Y-m-d');
            }
        } else {
            $projects = ['0' => 'All'] + GnrProject::where('companyId', $userCompanyId)->pluck('name', 'id')->toarray();
            $projectTypes = DB::table('gnr_project_type')->where('companyId', $userCompanyId)->pluck('name', 'id')->toarray();
            $branchLists = DB::table('gnr_branch')
                            ->where('id', $userBranchId)
                            ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                            ->toArray();
            $userBranchStartDate = GnrBranch::where('companyId', $userCompanyId)->min('aisStartDate');
            $lastMonthEndDate = AccMonthEnd::active()->whereIn('branchIdFk', $companyBranches)->max('date');
            if ($lastMonthEndDate) {
                $userBranchDate = Carbon::parse($lastMonthEndDate)->addDay()->endOfMonth()->format('Y-m-d');
            } else {
                $userBranchDate = Carbon::parse(GnrBranch::where('companyId', $userCompanyId)->max('aisStartDate'))->endOfMonth()->format('Y-m-d');
            }
        }
        
        // voucher types
        $voucherTypes = AddVoucherType::select('id','shortName')->get();
        
        // ledgers
        $ledgers = DB::table('acc_account_ledger')->where('isGroupHead', 0)->where('companyIdFk', $userCompanyId)->orderBy('code','asc')->select('id','name','code')->get();

        $currentFiscalYear = DB::table('gnr_fiscal_year')
                            ->where('companyId', $userCompanyId)
                            ->where('fyStartDate', '<=', $userBranchDate)
                            ->where('fyEndDate', '>=', $userBranchDate)
                            ->select('id','name', 'fyStartDate', 'fyEndDate')
                            ->first();
                            //dd($projectTypes);

        $startDate = $currentFiscalYear != null ? $currentFiscalYear->fyStartDate : $userBranchDate;

        $accLedgerReportArr = array(
            'userBranchId'              => $userBranchId,
            'userBranchData'            => $userBranchData,
            'projects'                  => $projects,
            'projectTypes'              => $projectTypes,
            'branchLists'               => $branchLists,
            'voucherTypes'              => $voucherTypes,
            'ledgers'                   => $ledgers,
            'startDate'                 => Carbon::parse($startDate)->format('d-m-Y'),
            'userBranchStartDate'       => $userBranchStartDate,
            'userBranchDate'            => Carbon::parse($userBranchDate)->format('d-m-Y'),
        );
        // dd($accLedgerReportArr);

        return view('accounting.reports.ledgerReportViews.viewLedgerReportFilterForm', $accLedgerReportArr);
    }

    public function getProjectTypesNBranches(Request $request) {
        
        $headOfficeId = DB::table('gnr_branch')->where('companyId', Auth::user()->company_id_fk)->where('branchCode', 0)->value('id');

        $projectTypes = DB::table('gnr_project_type')->select('name','id')
                        ->where('projectId',$request->projectId)
                        ->get();

        $branches = DB::table('gnr_branch')
                    ->where('id', '!=', $headOfficeId)
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

    public function getChildrenLedgers(Request $request) {

        $service = new Service;
        $user_company_id = Auth::user()->company_id_fk;
        $headOfficeId = DB::table('gnr_branch')->where('companyId', $user_company_id)->where('branchCode', 0)->value('id');
        $projectId = $request->projectId;
        $branchId = $request->branchId;

        if ((int)$projectId == 0) {
            $ledgers = DB::table('acc_account_ledger')->where('isGroupHead', 0)->where('companyIdFk', $user_company_id)->orderBy('code','asc')->select('id','name','code')->get();
        }
        else {

            if ($branchId == null) {
                $branchIdArr = DB::table('gnr_branch')->where('companyId', $user_company_id)->where('projectId', $projectId)->pluck('id')->toArray();
            }
            elseif ($branchId == 0) {
                $branchIdArr = DB::table('gnr_branch')->where('companyId', $user_company_id)->where('id', '!=', $headOfficeId)->where('projectId', $projectId)->pluck('id')->toArray();
            }
            else {
                $branchIdArr = [$branchId];
            }

            $ledgers = $service->getLedgerHeaderInfos($projectId, $branchIdArr, $user_company_id)->where('isGroupHead', 0);
        }
        // dd($ledgers);

        return response()->json($ledgers);
    }

    public function loadLedgerReport(Request $request) {
        $dbhelper = new DatabasePartitionHelper();        
        
        $user_company_id = Auth::user()->company_id_fk;
        $headOfficeId = DB::table('gnr_branch')->where('companyId', $user_company_id)->where('branchCode', 0)->value('id');
        $userBranchId = Auth::user()->branchId;
        $company = Auth::user()->company;

        // collecting request data
        $projectId = $request->filProject;
        $branchId = $request->filBranch;
        $projectTypeId = $request->filProjectType;
        $ledgerId = $request->ledgerId;
        $voucherTypeId = $request->voucherTypeId;
        $dateFrom = Carbon::parse($request->dateFrom)->format('Y-m-d');
        $dateTo = Carbon::parse($request->dateTo)->format('Y-m-d');

        // branch data
        if ((int)$projectId == 0) {
            $branchIdArr = DB::table('gnr_branch')->where('companyId', $user_company_id)->pluck('id')->toArray();
        }
        else {
            if ($branchId == null) $branchIdArr =  [$userBranchId => $userBranchId] + DB::table('gnr_branch')->where('companyId', $user_company_id)->where('projectId', $projectId)->pluck('id', 'id')->toArray();
            elseif ($branchId == 0) $branchIdArr = DB::table('gnr_branch')->where('companyId', $user_company_id)->where('id', '!=', $headOfficeId)->where('projectId', $projectId)->pluck('id')->toArray();
            else  $branchIdArr = [(int)$request->filBranch];
        }
        
        if ($branchId == '') $branchName = 'All';        
        elseif ($branchId == 0) $branchName = 'All Branches';        
        else  $branchName = DB::table('gnr_branch')->where('id', $branchId)->value(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', TRIM(REPLACE(name, '\t', '')))"));

        // year data
        $fiscalYear = DB::table('gnr_fiscal_year')->where('companyId', $user_company_id)->where('fyStartDate', '<=', $dateFrom)->where('fyEndDate','>=', $dateFrom)->first();
        $lastOpeningDate = $fiscalYear != null ? Carbon::parse($fiscalYear->fyStartDate)->subdays(1)->format('Y-m-d') : Carbon::parse($dateFrom)->subdays(1)->format('Y-m-d');

        $projectName = (int)$projectId == 0 ? 'All' : DB::table('gnr_project')->where('id', $projectId)->value('name');
        $projectTypeName = (int)$projectTypeId == 0 ? 'All' : DB::table('gnr_project_type')->where('id', $projectTypeId)->value('name');
        
        // ledger head name
        $ledgerHead = DB::table('acc_account_ledger')->where('id', $ledgerId)->value(DB::raw("CONCAT(name, '[', code, ']')"));

        // requested info array
        $ledgerReportLoadTableArr = array(
            'company'                 => $company,
            'dateFrom'                => $dateFrom,
            'dateTo'                  => $dateTo,
            'projectName'             => $projectName,
            'projectType'             => $projectTypeName,
            'branchName'              => $branchName,
            'ledgerHead'              => $ledgerHead,
        );
        
        // ledger array
        $ledgersInfo = DB::table('acc_account_ledger')
        ->where('companyIdFk', $user_company_id)
        ->select(DB::raw("CONCAT(name, ' [', code, ']') AS nameWithCode"), 'id')
        ->pluck('nameWithCode', 'id')
        ->toArray();

        ///////////////// opening balance calculations //////////////////
        
        $acc_opening_balance = $dbhelper->getUserPartitionWiseDBTableName('acc_opening_balance');
        
        $yearEnds = DB::table($acc_opening_balance)
        ->where('companyIdFk', $user_company_id)
        ->distinct('openingDate')
        ->pluck('openingDate')
        ->toArray();

        $openingBalanceAmount = 0;
        if (in_array($lastOpeningDate, $yearEnds)) {
            $openingBalanceAmount = DB::table($acc_opening_balance)
            ->where('companyIdFk', $user_company_id)
            ->whereIn('branchId', $branchIdArr)
            ->where('openingDate', $lastOpeningDate)
            ->where('ledgerId', $ledgerId);
            
            if ($projectId != "0") $openingBalanceAmount->where('projectId', $projectId);            
            if ($projectTypeId != "0") $openingBalanceAmount->where('projectTypeId', $projectTypeId);
            
            $openingBalanceAmount = $openingBalanceAmount->value(DB::raw('SUM(balanceAmount) as balanceAmount'));
        }
        
        // opening data from voucher        
        $acc_voucher = $dbhelper->getPartitionWiseDBTableNameForJoin('acc_voucher', 'av');

        // debit
        $opVoucherDebitAmount = DB::table($acc_voucher)
                                ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                ->where('av.companyId', $user_company_id)
                                ->whereIn('av.branchId', $branchIdArr)
                                ->where('av.voucherDate', '>', $lastOpeningDate)
                                ->where('av.voucherDate', '<', $dateFrom)
                                ->where('acc_voucher_details.debitAcc', $ledgerId);

        if ($projectId != "0") $opVoucherDebitAmount->where('av.projectId', $projectId);        
        if ($projectTypeId != "0") $opVoucherDebitAmount->where('av.projectTypeId', $projectTypeId);        
        if ($voucherTypeId != null) $opVoucherDebitAmount->where('av.voucherTypeId', $voucherTypeId);
                
        $opVoucherDebitAmount = floatval($opVoucherDebitAmount->value(DB::raw('SUM(acc_voucher_details.amount)')));

        // credit
        $opVoucherCreditAmount = DB::table($acc_voucher)
                                ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                ->where('av.companyId', $user_company_id)
                                ->whereIn('av.branchId', $branchIdArr)
                                ->where('av.voucherDate', '>', $lastOpeningDate)
                                ->where('av.voucherDate', '<', $dateFrom)
                                ->where('acc_voucher_details.creditAcc', $ledgerId);

        if ($projectId != "0") $opVoucherCreditAmount->where('av.projectId', $projectId);
        if ($projectTypeId != "0") $opVoucherCreditAmount->where('av.projectTypeId', $projectTypeId);
        if ($voucherTypeId != null) $opVoucherCreditAmount->where('av.voucherTypeId', $voucherTypeId);
        
        $opVoucherCreditAmount = floatval($opVoucherCreditAmount->value(DB::raw('SUM(acc_voucher_details.amount)')));
        // dd($opVoucherDebitAmount, $opVoucherCreditAmount);

        // opening balance from voucher after year ends
        $opVouherBalanceAmount = $opVoucherDebitAmount - $opVoucherCreditAmount;
        // final opening balance
        $openingBalance = $openingBalanceAmount + $opVouherBalanceAmount;
        // dd($openingBalance);

        ///////////// collect all transactions //////////////////

        $voucherInfos = DB::table($acc_voucher)
                        ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                        ->where('av.companyId', $user_company_id)
                        ->whereIn('av.branchId', $branchIdArr)
                        ->where('av.voucherDate', '>=', $dateFrom)
                        ->where('av.voucherDate', '<=', $dateTo)
                        ->where(function ($query) use ($ledgerId){
                                $query->where('acc_voucher_details.debitAcc', $ledgerId)
                                ->orWhere('acc_voucher_details.creditAcc', $ledgerId);
                        });

        if ($projectId != "0") $voucherInfos->where('av.projectId', $projectId);
        if ($projectTypeId != "0") $voucherInfos->where('av.projectTypeId', $projectTypeId);
        if ($voucherTypeId != null) $voucherInfos->where('av.voucherTypeId', $voucherTypeId);

        $voucherInfos = $voucherInfos
                        ->select('av.voucherDate', 'av.voucherCode', 'av.globalNarration', 'acc_voucher_details.voucherId', 'acc_voucher_details.debitAcc', 'acc_voucher_details.creditAcc', 'acc_voucher_details.amount')
                        ->orderBy('av.voucherDate')
                        ->get();
        

        $data = array(
            'ledgerReportLoadTableArr'      => $ledgerReportLoadTableArr,
            'ledgerId'                      => $ledgerId,
            'ledgersInfo'                   => $ledgersInfo,
            'openingBalance'                => $openingBalance,           
            'voucherInfos'                  => $voucherInfos,
        );
        
        return view('accounting.reports.ledgerReportViews.viewLedgerReportTable', $data);
    }

}
