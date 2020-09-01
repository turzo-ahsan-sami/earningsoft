<?php

namespace App\Http\Controllers\accounting\reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\accounting\AddVoucherType;
use Auth;
use DB;
use Carbon\Carbon;
use App\Service\Service;

class BankBookReportController extends Controller {

    public function index() {

        $userBranchId = Auth::user()->branchId;
        $userProjectId = Auth::user()->project_id_fk;
        $userBranchData = DB::table('gnr_branch')
                        ->where('id', $userBranchId)
                        ->select('id', DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"))
                        ->first();
        $branchLists = array();
        $projectTypes = array();

        if ($userBranchId == 1) {
            $projects = [0 => 'All'] + DB::table('gnr_project')->pluck('name','id')->toarray();
        }
        else {
            $projects = DB::table('gnr_project')->where('id', $userProjectId)->pluck('name','id')->toarray();
            $projectTypes = ['0' => 'All'] + DB::table('gnr_project_type')->where('projectId', $userProjectId)->pluck('name','id')->toarray();
            $branchLists = DB::table('gnr_branch')
                            ->where('id', $userBranchId)
                            ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                            ->toArray();
        }
        // dd($headOfficeData);
        // voucher types
        $voucherTypes = AddVoucherType::select('id','shortName')->get();
        // ledgers
        $ledgers = DB::table('acc_account_ledger')->where('accountTypeId', 5)->where('isGroupHead', 0)->orderBy('code','asc')->select('id','name','code')->get();
        // branch date
        $userBranchStartDate = DB::table('gnr_branch')->where('id', $userBranchId)->value('aisStartDate');
        $userBranchDate = DB::table('acc_day_end')->where('branchIdFk', $userBranchId)->max('date');

        if ($userBranchDate == null) {
            $userBranchDate = $userBranchStartDate;
        }
        // dd($userBranchDate);

        $currentFiscalYear = DB::table('gnr_fiscal_year')
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
            'startDate'                 => Carbon::parse($startDate)->format('d-m-Y'),
            'userBranchStartDate'       => $userBranchStartDate,
            'userBranchDate'            => Carbon::parse($userBranchDate)->format('d-m-Y'),
        );
        // dd($accBankBookReportArr);

        return view('accounting.reports.bankBookReportViews.viewBankBookReportFilterForm', $accBankBookReportArr);
    }

    public function getChildrenBankLedgers(Request $request) {

        $service = new Service;
        $user_company_id = Auth::user()->company_id_fk;
        $projectId = $request->projectId;
        $branchId = $request->branchId;

        if ((int)$projectId == 0) {
            $ledgers = DB::table('acc_account_ledger')->where('accountTypeId', 5)->where('isGroupHead', 0)->orderBy('code','asc')->select('id','name','code')->get();
        }
        else {

            if ($branchId == null) {
                $branchIdArr = DB::table('gnr_branch')->where('projectId', $projectId)->pluck('id')->toArray();
            }
            elseif ($branchId == 0) {
                $branchIdArr = DB::table('gnr_branch')->where('id', '!=', 1)->where('projectId', $projectId)->pluck('id')->toArray();
            }
            else {
                $branchIdArr = [$branchId];
            }

            $ledgers = $service->getLedgerHeaderInfos($projectId, $branchIdArr, $user_company_id)->where('accountTypeId', 5)->where('isGroupHead', 0);
        }
        // dd($ledgers);

        return response()->json($ledgers);
    }

    public function loadBankBookReport(Request $request) {

        // dd($request->all());
        $service = new Service;
        $user_company_id = Auth::user()->company_id_fk;
        $userBranchId = Auth::user()->branchId;
        $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();

        // collecting request data
        $projectId = $request->filProject;
        $branchId = $request->filBranch;
        $projectTypeId = $request->filProjectType;
        $ledgerId = $request->ledgerId;
        $voucherTypeId = $request->voucherTypeId;
        $dateFrom = Carbon::parse($request->dateFrom)->format('Y-m-d');
        $dateTo = Carbon::parse($request->dateTo)->format('Y-m-d');

        if ((int)$projectId == 0) {
            $branchIdArr = DB::table('gnr_branch')->pluck('id')->toArray();
        }
        else {

            if ($branchId == null) {
                $branchIdArr =  [$userBranchId => $userBranchId] + DB::table('gnr_branch')->where('projectId', $projectId)->pluck('id', 'id')->toArray();
            }
            elseif ($branchId == 0) {
                $branchIdArr = DB::table('gnr_branch')->where('id', '!=', 1)->where('projectId', $projectId)->pluck('id')->toArray();
            }
            else {
                $branchIdArr = [(int)$request->filBranch];
            }

        }
        // dd($branchIdArr);

        // ledger array
        if ($ledgerId == null) {

            if ((int)$projectId == 0) {
                $ledgerIdArr = DB::table('acc_account_ledger')->where('isGroupHead', 0)->where('accountTypeId', 5)->pluck('id')->toArray();
            }
            else {
                $ledgerIdArr = $service->getLedgerHeaderInfos($projectId, $branchIdArr, $user_company_id)->where('isGroupHead', 0)->where('accountTypeId', 5)->pluck('id')->toArray();
            }

        }
        else {
            $ledgerIdArr = [$ledgerId];
        }
        // dd($ledgerIdArr);

        // collecting year data
        $fiscalYear = DB::table('gnr_fiscal_year')->where('fyStartDate', '<=', $dateFrom)->where('fyEndDate','>=', $dateFrom)->first();
        $lastOpeningDate = $fiscalYear != null ? Carbon::parse($fiscalYear->fyStartDate)->subdays(1)->format('Y-m-d') : Carbon::parse($dateFrom)->subdays(1)->format('Y-m-d');

        $projectName = (int)$projectId == 0 ? 'All' : DB::table('gnr_project')->where('id', $projectId)->value('name');
        $projectTypeName = (int)$projectTypeId == 0 ? 'All' : DB::table('gnr_project_type')->where('id', $projectTypeId)->value('name');

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
        $ledgerHead = $ledgerId == null ? 'All Bank Ledgers' : DB::table('acc_account_ledger')->where('id', $ledgerId)->value(DB::raw("CONCAT(name, '[', code, ']')"));

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
                    ->pluck('nameWithCode', 'id')
                    ->toArray();

        ///////////////// opening balance calculations //////////////////
        $openingBalanceAmount = 0;
        $yearEnds = DB::table('acc_opening_balance')->distinct('openingDate')->pluck('openingDate')->toArray();

        if (in_array($lastOpeningDate, $yearEnds)) {

            $openingBalanceAmount = DB::table('acc_opening_balance')
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
        $opVoucherDebitAmount = DB::table('acc_voucher')
                                ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                ->where('acc_voucher.companyId', $user_company_id)
                                ->whereIn('acc_voucher.branchId', $branchIdArr)
                                ->where('acc_voucher.voucherDate', '>', $lastOpeningDate)
                                ->where('acc_voucher.voucherDate', '<', $dateFrom)
                                ->whereIn('acc_voucher_details.debitAcc', $ledgerIdArr);
        // project filter
        if ($projectId != "0") {
            $opVoucherDebitAmount->where('acc_voucher.projectId', $projectId);
        }
        // project type filter
        if ($projectTypeId != "0") {
            $opVoucherDebitAmount->where('acc_voucher.projectTypeId', $projectTypeId);
        }
        // voucher type filter
        if ($voucherTypeId != null) {
            $opVoucherDebitAmount->where('acc_voucher.voucherTypeId', $voucherTypeId);
        }

        $opVoucherDebitAmount = $opVoucherDebitAmount->value(DB::raw('SUM(acc_voucher_details.amount)'));

        // credit
        $opVoucherCreditAmount = DB::table('acc_voucher')
                                ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                ->where('acc_voucher.companyId', $user_company_id)
                                ->whereIn('acc_voucher.branchId', $branchIdArr)
                                ->where('acc_voucher.voucherDate', '>', $lastOpeningDate)
                                ->where('acc_voucher.voucherDate', '<', $dateFrom)
                                ->whereIn('acc_voucher_details.creditAcc', $ledgerIdArr);
        // project filter
        if ($projectId != "0") {
            $opVoucherCreditAmount->where('acc_voucher.projectId', $projectId);
        }
        // project type filter
        if ($projectTypeId != "0") {
            $opVoucherCreditAmount->where('acc_voucher.projectTypeId', $projectTypeId);
        }
        // voucher type filter
        if ($voucherTypeId != null) {
            $opVoucherCreditAmount->where('acc_voucher.voucherTypeId', $voucherTypeId);
        }

        $opVoucherCreditAmount = $opVoucherCreditAmount->value(DB::raw('SUM(acc_voucher_details.amount)'));
        // dd($opVoucherDebitInfo, $opVoucherCreditInfo);

        // opening balance from voucher after year ends
        $opVouherBalanceAmount = $opVoucherDebitAmount - $opVoucherCreditAmount;
        // final opening balance
        $openingBalance = $openingBalanceAmount + $opVouherBalanceAmount;
        // dd($openingBalance);

        ///////////// collect all transactions //////////////////
        // debit
        $voucherInfos = DB::table('acc_voucher')
                            ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                            ->where('acc_voucher.companyId', $user_company_id)
                            ->whereIn('acc_voucher.branchId', $branchIdArr)
                            ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                            ->where('acc_voucher.voucherDate', '<=', $dateTo)
                            ->where(function ($query) use ($ledgerIdArr){
                                  $query->whereIn('acc_voucher_details.debitAcc', $ledgerIdArr)
                                  ->orWhereIn('acc_voucher_details.creditAcc', $ledgerIdArr);
                              });
        // project filter
        if ($projectId != "0") {
            $voucherInfos->where('acc_voucher.projectId', $projectId);
        }
        // project type filter
        if ($projectTypeId != "0") {
            $voucherInfos->where('acc_voucher.projectTypeId', $projectTypeId);
        }
        // voucher type filter
        if ($voucherTypeId != null) {
            $voucherInfos->where('acc_voucher.voucherTypeId', $voucherTypeId);
        }

        $voucherInfos = $voucherInfos
                        ->select(
                            'acc_voucher.voucherDate', 'acc_voucher.voucherCode', 'acc_voucher.globalNarration', 'acc_voucher_details.voucherId', 'acc_voucher_details.debitAcc', 'acc_voucher_details.creditAcc', 'acc_voucher_details.amount'
                            )
                        ->orderBy('acc_voucher.voucherDate')
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
