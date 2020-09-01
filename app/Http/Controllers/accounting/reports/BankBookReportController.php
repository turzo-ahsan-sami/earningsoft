<?php

namespace App\Http\Controllers\accounting\reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\accounting\AddVoucherType;
use Auth;
use DB;
use Carbon\Carbon;
use App\Service\Service;
use App\gnr\GnrBranch;
use App\accounting\process\AccMonthEnd;
use App\Service\DatabasePartitionHelper;
use Illuminate\Support\Collection;


class BankBookReportController extends Controller {

    public function index() {
        $userCompanyId = Auth::user()->company_id_fk;
        $userBranchId = Auth::user()->branchId;
        $userBranch = Auth::user()->branch;
        $userProjectId = Auth::user()->project_id_fk;
        $headOfficeId = DB::table('gnr_branch')->where('companyId', $userCompanyId)->where('branchCode', 0)->value('id');

        $userBranchData = DB::table('gnr_branch')
                        ->where('id', $userBranchId)
                        ->select('id', DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"))
                        ->first();
        $branchLists = array();
        $projectTypes = array();

        // if (!$headOfficeId) {
        //      $projects = DB::table('gnr_project')->where('companyId', $userCompanyId)->pluck('name','id')->toarray();
            
        // }
        // else {
        //     $projects = ['All']+DB::table('gnr_project')->where('companyId', $userCompanyId)->pluck('name','id')->toarray();
        //     $projectTypes = ['All']+DB::table('gnr_project_type')->where('companyId', $userCompanyId)->pluck('name','id')->toarray();
        //     $branchLists = DB::table('gnr_branch')
        //                     ->where('id', $userBranchId)
        //                     ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
        //                     ->toArray();
        // } 
        if(!$headOfficeId) {
             $projects = DB::table('gnr_project')->where('companyId', $userCompanyId)->pluck('name','id')->toarray();
            
        }
        else {
            $projects = ['All']+DB::table('gnr_project')->where('companyId', $userCompanyId)->pluck('name','id')->toarray();
            $projectTypes = ['All']+DB::table('gnr_project_type')->where('companyId', $userCompanyId)->pluck('name','id')->toarray();
            $branchLists = DB::table('gnr_branch')
                            ->where('id', $userBranchId)
                            ->where('companyId', $userCompanyId)
                            ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                            ->toArray();
        }
        //dd($projectTypes);
        // voucher types
        $voucherTypes = AddVoucherType::select('id','shortName')->get();
        // ledgers
          $ledgers = DB::table('acc_account_ledger')->where('companyIdFk', $userCompanyId)->where('accountTypeId', 5)->where('isGroupHead', 0)->orderBy('code','asc')->select('id','name','code')->get();
          //dd($ledgers);
        // branch date
        // $userBranchStartDate = DB::table('gnr_branch')->where('companyId', $userCompanyId)->where('id', $userBranchId)->value('aisStartDate');
        // $userBranchDate = DB::table('acc_day_end')->where('branchIdFk', $userBranchId)->max('date');

        // if ($userBranchDate == null) {
        //     $userBranchDate = $userBranchStartDate;
        // }

        $userBranchStartDate = GnrBranch::where('companyId', $userCompanyId)->min('aisStartDate');
        //dd($userBranchStartDate);
        $lastMonthEndDate = AccMonthEnd::active()->where('branchIdFk', $userBranch->id)->max('date');
        if ($lastMonthEndDate) {
            $userBranchDate = Carbon::parse($lastMonthEndDate)->addDay()->endOfMonth()->format('Y-m-d');
        } else {
            $userBranchDate = Carbon::parse($userBranchStartDate)->endOfMonth()->format('Y-m-d');
        }
        // dd($userBranchDate);

        $currentFiscalYear = DB::table('gnr_fiscal_year')
                            ->where('companyId', $userCompanyId)
                            ->where('fyStartDate', '<=', $userBranchDate)
                            ->where('fyEndDate', '>=', $userBranchDate)
                            ->select('id','name', 'fyStartDate', 'fyEndDate')
                            ->first();
                            // dd($currentFiscalYear);

        $startDate = $currentFiscalYear != null ? $currentFiscalYear->fyStartDate : $userBranchDate;

        $accBankBookReportArr = array(
            'userBranchId'              => $userBranchId,
            'userBranchData'            => $userBranchData,
            'projects'                  => $projects,
            'projectTypes'              => $projectTypes,
            'branchLists'               => $branchLists,
            'voucherTypes'              => $voucherTypes,
            'ledgers'                   => $ledgers,
            'headOfficeId'              => $headOfficeId,
            'startDate'                 => Carbon::parse($startDate)->format('d-m-Y'),
            'userBranchStartDate'       => $userBranchStartDate,
            'userBranchDate'            => Carbon::parse($userBranchDate)->format('d-m-Y'),
        );
        //dd($accBankBookReportArr);

        return view('accounting.reports.bankBookReportViews.viewBankBookReportFilterForm', $accBankBookReportArr);
    }

    public function getChildrenBankLedgers(Request $request) {

        $service = new Service;
        $user_company_id = Auth::user()->company_id_fk;
        $projectId = $request->projectId;
        $branchId = $request->branchId;
        $headOfficeId = DB::table('gnr_branch')->where('companyId', $user_company_id)->where('branchCode', 0)->value('id');

        if ((int)$projectId == 0) {
            $ledgers = DB::table('acc_account_ledger')->where('companyIdFk', $user_company_id)->where('accountTypeId', 5)->where('isGroupHead', 0)->orderBy('code','asc')->select('id','name','code')->get();
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
            // dd($branchIdArr);

            $ledgers = $service->getLedgerHeaderInfos($projectId, $branchIdArr, $user_company_id)->where('accountTypeId', 5)->where('isGroupHead', 0);
        }
        // dd($ledgers);

        return response()->json($ledgers);
    }

    public function loadBankBookReport(Request $request) {
        $dbhelper = new DatabasePartitionHelper();
        $acc_voucher = $dbhelper->getPartitionWiseDBTableNameForJoin('acc_voucher', 'av');
        
        $service = new Service;
        $user_company_id = Auth::user()->company_id_fk;
        $userBranchId = Auth::user()->branchId;
        $headOfficeId = DB::table('gnr_branch')->where('companyId', $user_company_id)->where('branchCode', 0)->value('id');
        $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();

        // collecting request data
        $projectId = $request->filProject;
        $branchId = $request->filBranch;
        $projectTypeId = $request->filProjectType;
        $ledgerId = $request->ledgerId;
        $voucherTypeId = $request->voucherTypeId;
        $dateFrom = Carbon::parse($request->dateFrom)->format('Y-m-d');
        $dateTo = Carbon::parse($request->dateTo)->format('Y-m-d');
        $userCompanyId = Auth::user()->company_id_fk;
        if ((int)$projectId == 0) {
            $branchIdArr = DB::table('gnr_branch')->where('companyId', $userCompanyId)->pluck('id')->toArray();
        }
        else {

            if ($branchId == null) {
                $branchIdArr =  [$userBranchId => $userBranchId] + DB::table('gnr_branch')->where('companyId', $userCompanyId)->where('projectId', $projectId)->pluck('id', 'id')->toArray();
            }
            elseif ($branchId == 0) {
                $branchIdArr = DB::table('gnr_branch')->where('id', '!=', $headOfficeId)->where('companyId', $userCompanyId)->where('projectId', $projectId)->pluck('id')->toArray();
            }
            else {
                $branchIdArr = [(int)$request->filBranch];
            }

        }
        // dd($branchIdArr);

        // ledger array
        if ($ledgerId == null) {

            if ((int)$projectId == 0) {
                $ledgerIdArr = DB::table('acc_account_ledger')->where('companyIdFk', $userCompanyId)->where('isGroupHead', 0)->where('accountTypeId', 5)->pluck('id')->toArray();
            }
            else {
                $ledgerIdArr = $service->getLedgerHeaderInfos($projectId, $branchIdArr, $user_company_id)->where('isGroupHead', 0)->where('accountTypeId', 5)->pluck('id')->toArray();
            }

        }
        else {
            $ledgerIdArr = [(int) $ledgerId];
        }
        // dd($branchIdArr, $ledgerIdArr);

        // collecting year data
        $fiscalYear = DB::table('gnr_fiscal_year')->where('companyId', $user_company_id)->where('fyStartDate', '<=', $dateFrom)->where('fyEndDate','>=', $dateFrom)->first();
        $lastOpeningDate = $fiscalYear != null ? Carbon::parse($fiscalYear->fyStartDate)->subdays(1)->format('Y-m-d') : Carbon::parse($dateFrom)->subdays(1)->format('Y-m-d');

        $projectName = (int)$projectId == 0 ? 'All' : DB::table('gnr_project')->where('companyId', $userCompanyId)->where('id', $projectId)->value('name');
        $projectTypeName = (int)$projectTypeId == 0 ? 'All' : DB::table('gnr_project_type')->where('companyId', $userCompanyId)->where('id', $projectTypeId)->value('name');

        // show branch name in view
        if ($branchId == null) {
            $branchName = 'All';
        }
        elseif ($branchId == 0) {
            $branchName = 'All Branches';
        }
        else {
            $branchName = DB::table('gnr_branch')->where('id', $branchId)->value(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', TRIM(REPLACE(name, '\t', '')))"));
        }

        // ledger head name
        $ledgerHead = $ledgerId == null ? 'All Bank Ledgers' : DB::table('acc_account_ledger')->where('companyIdFk', $userCompanyId)->where('id', $ledgerId)->value(DB::raw("CONCAT(name, '[', code, ']')"));

        // requested info array
        $bankBookReportLoadTableArr = array(
            'company'                 => $company,
            'dateFrom'                => $dateFrom,
            'dateTo'                  => $dateTo,
            'projectName'             => $projectName,
            'projectType'             => $projectTypeName,
            'branchName'              => $branchName,
            'ledgerHead'              => $ledgerHead,
        );
        // dd($bankBookReportLoadTableArr);

        // ledger array
        $ledgersInfo = DB::table('acc_account_ledger')
                    ->select(DB::raw("CONCAT(name, ' [', code, ']') AS nameWithCode"), 'id')
                    ->where('companyIdFk', $userCompanyId)
                    ->pluck('nameWithCode', 'id')
                    ->toArray();

        ///////////////// opening balance calculations //////////////////
        $openingBalanceAmount = 0;
        $acc_opening_balance = $dbhelper->getPartitionWiseDBTableNameForJoin('acc_opening_balance', 'ob');
        $yearEnds = DB::table($acc_opening_balance)
        ->where('ob.companyIdFk', $user_company_id)
        ->distinct('ob.openingDate')
        ->pluck('ob.openingDate')
        ->toArray();

        if (in_array($lastOpeningDate, $yearEnds)) {
            $acc_opening_balance = $dbhelper->getUserPartitionWiseDBTableName('acc_opening_balance');
            $openingBalanceAmount = DB::table($acc_opening_balance)
            ->where('companyIdFk', $user_company_id)
            ->whereIn('branchId', $branchIdArr)
            ->where('openingDate', $lastOpeningDate)
            ->whereIn('ledgerId', $ledgerIdArr);
            // project filter
            if ($projectId != "0") {
                $openingBalanceAmount->where('projectId', $projectId);
            }
            // project type filter
            if ($projectTypeId != "0") {
                $openingBalanceAmount->where('projectTypeId', $projectTypeId);
            }

            $openingBalanceAmount = $openingBalanceAmount->value(DB::raw('SUM(balanceAmount) as balanceAmount'));
            // dd($openingBalanceAmount);
        }

        // opening data from voucher
        // debit
        $opVoucherDebitAmount = DB::table($acc_voucher)
                                ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                ->where('av.companyId', $user_company_id)
                                ->whereIn('av.branchId', $branchIdArr)
                                ->where('av.voucherDate', '>', $lastOpeningDate)
                                ->where('av.voucherDate', '<', $dateFrom)
                                ->whereIn('acc_voucher_details.debitAcc', $ledgerIdArr);
        // project filter
        if ($projectId != "0") {
            $opVoucherDebitAmount->where('av.projectId', $projectId);
        }
        // project type filter
        if ($projectTypeId != "0") {
            $opVoucherDebitAmount->where('av.projectTypeId', $projectTypeId);
        }
        // voucher type filter
        if ($voucherTypeId != null) {
            $opVoucherDebitAmount->where('av.voucherTypeId', $voucherTypeId);
        }

        $opVoucherDebitAmount = $opVoucherDebitAmount->value(DB::raw('SUM(acc_voucher_details.amount)'));

        // credit
        $opVoucherCreditAmount = DB::table($acc_voucher)
                                ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                                ->where('av.companyId', $user_company_id)
                                ->whereIn('av.branchId', $branchIdArr)
                                ->where('av.voucherDate', '>', $lastOpeningDate)
                                ->where('av.voucherDate', '<', $dateFrom)
                                ->whereIn('acc_voucher_details.creditAcc', $ledgerIdArr);
        // project filter
        if ($projectId != "0") {
            $opVoucherCreditAmount->where('av.projectId', $projectId);
        }
        // project type filter
        if ($projectTypeId != "0") {
            $opVoucherCreditAmount->where('av.projectTypeId', $projectTypeId);
        }
        // voucher type filter
        if ($voucherTypeId != null) {
            $opVoucherCreditAmount->where('av.voucherTypeId', $voucherTypeId);
        }

        $opVoucherCreditAmount = $opVoucherCreditAmount->value(DB::raw('SUM(acc_voucher_details.amount)'));
        // dd($opVoucherDebitInfo, $opVoucherCreditInfo);

        // opening balance from voucher after year ends
        $opVouherBalanceAmount = $opVoucherDebitAmount - $opVoucherCreditAmount;
        // final opening balance
        $openingBalance = $openingBalanceAmount + $opVouherBalanceAmount;
        // dd($ledgerIdArr);

        ///////////// collect all transactions //////////////////
        // debit
        $voucherInfos = DB::table($acc_voucher)
                            ->join('acc_voucher_details', 'av.id', '=', 'acc_voucher_details.voucherId')
                            ->where('av.companyId', $user_company_id)
                            ->whereIn('av.branchId', $branchIdArr)
                            ->where('av.voucherDate', '>=', $dateFrom)
                            ->where('av.voucherDate', '<=', $dateTo)
                            ->where(function ($query) use ($ledgerIdArr){
                                  $query->whereIn('acc_voucher_details.debitAcc', $ledgerIdArr)
                                  ->orWhereIn('acc_voucher_details.creditAcc', $ledgerIdArr);
                              });
        // project filter
        if ($projectId != "0") {
            $voucherInfos->where('av.projectId', $projectId);
        }
        // project type filter
        if ($projectTypeId != "0") {
            $voucherInfos->where('av.projectTypeId', $projectTypeId);
        }
        // voucher type filter
        if ($voucherTypeId != null) {
            $voucherInfos->where('av.voucherTypeId', $voucherTypeId);
        }

        $voucherInfos = $voucherInfos
                        ->select(
                            'av.voucherDate', 'av.voucherCode', 'av.globalNarration', 'acc_voucher_details.voucherId', 'acc_voucher_details.debitAcc', 'acc_voucher_details.creditAcc', 'acc_voucher_details.amount'
                            )
                        ->orderBy('av.voucherDate')
                        ->get();
                        // dd($voucherInfos);

        // view table build up
        // $tableRow = '';
        // $sl = 1;
        // $debitAmount = 0;
        // $creditAmount = 0;
        // $totalDebit = 0;
        // $totalCredit = 0;
        // $balanceAmount = $openingBalance;
        //
        // foreach ($voucherInfos as $voucherInfo) {
        //
        //     // account head
        //     if($ledgerId != $voucherInfo->debitAcc){
        //         $ledgerNameWithCode = $ledgersInfo[$voucherInfo->debitAcc];
        //     }
        //     elseif($ledgerId != $voucherInfo->creditAcc){
        //         $ledgerNameWithCode = $ledgersInfo[$voucherInfo->creditAcc];
        //     }
        //     elseif($voucherInfo->debitAcc == $voucherInfo->creditAcc) {
        //         $ledgerNameWithCode = $ledgersInfo[$voucherInfo->debitAcc];
        //     }
        //
        //     // debit and credit amount
        //     if ($ledgerId == $voucherInfo->debitAcc && $voucherInfo->debitAcc != $voucherInfo->creditAcc) {
        //         $debitAmount = $voucherInfo->amount;
        //         $creditAmount = 0;
        //         $totalDebit += $voucherInfo->amount;
        //         $balanceAmount += $voucherInfo->amount;
        //     }
        //     elseif ($ledgerId == $voucherInfo->creditAcc && $voucherInfo->debitAcc != $voucherInfo->creditAcc) {
        //         $debitAmount = 0;
        //         $creditAmount = $voucherInfo->amount;
        //         $totalCredit += $voucherInfo->amount;
        //         $balanceAmount -= $voucherInfo->amount;
        //     }
        //     elseif ($voucherInfo->debitAcc == $voucherInfo->creditAcc) {
        //         $debitAmount = $voucherInfo->amount;
        //         $creditAmount = $voucherInfo->amount;
        //         $totalDebit += $voucherInfo->amount;
        //         $totalCredit += $voucherInfo->amount;
        //     }
        //
        //     // balance type(dr/cr)
        //     $balanceType = $balanceAmount < 0 ? 'Cr' : 'Dr';
        //
        //     $tableRow .= '<tr>
        //                     <td>'. $sl++ .'</td>
        //                     <td style="text-align: left;">'. Carbon::parse($voucherInfo->voucherDate)->format('d-m-Y') .'</td>
        //                     <td style="text-align: left;">'. $voucherInfo->voucherCode .'</td>
        //                     <td style="text-align: left;">'. $ledgerNameWithCode .'</td>
        //                     <td style="text-align: left;">'. $voucherInfo->globalNarration .'</td>
        //                     <td class="amount">'. $debitAmount .'</td>
        //                     <td class="amount">'. $creditAmount .'</td>
        //                     <td class="amount">'. $balanceAmount .'</td>
        //                     <td>'. $balanceType .'</td>
        //                 </tr>';
        //
        // }

        // structuring data to send information in view file
        $data = array(
            'bankBookReportLoadTableArr'    => $bankBookReportLoadTableArr,
            'ledgerId'                      => $ledgerId,
            'ledgerIdArr'                   => $ledgerIdArr,
            'ledgersInfo'                   => $ledgersInfo,
            'openingBalance'                => $openingBalance,
            // 'totalDebit'                    => $totalDebit,
            // 'totalCredit'                   => $totalCredit,
            // 'balanceAmount'                 => $balanceAmount,
            'voucherInfos'                  => $voucherInfos,
            // 'tableRow'                      => $tableRow,

        );
        // dd($data);

        return view('accounting.reports.bankBookReportViews.viewBankBookReportTable', $data);
    }

}
