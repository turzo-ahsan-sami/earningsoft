<?php

namespace App\Http\Controllers\microfin\reports\registerReport\regular;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use App\hr\Exam;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\microfin\MicroFin;
use App\Http\Controllers\microfin\MicroFinance;
use App\microfin\settings\MfnProfession;

class SavingInterestInformationRegisterController extends Controller {

    protected $MicroFinance;

    public function __construct() {
        $this->MicroFinance = new MicroFinance;
    }

    public function index() {

        $userBranchId=Auth::user()->branchId;

        /// Branch
        $branchList = DB::table('gnr_branch');

        if ($userBranchId!=1) {
            $branchList = $branchList->where('id', $userBranchId);
        }
        $branchList = $branchList
                    ->orderBy('branchCode')
                    ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                    ->whereNotIn('id',[1])
                    ->pluck('nameWithCode', 'id')
                    ->all();
        $fiscalYearList = DB::table('gnr_fiscal_year')
                        ->select(
                            DB::raw("CONCAT(fyStartDate, ' to ',fyEndDate) AS fiscalYearTo"),
                            DB::raw("CONCAT(fyStartDate, ',',fyEndDate) AS fiscalYearListValue")
                        )
                        ->pluck('fiscalYearTo','fiscalYearListValue')
                        ->all();

        $samityLists = DB::table('mfn_samity')->where('status', 1)->where('softDel', 0)
                    ->where('branchId', $userBranchId)
                    ->pluck(DB::raw("CONCAT(code, ' - ', name) AS nameWithCode"), 'id')->toArray();

        $filteringArray = array(
            'branchList'                    => $branchList,
            'fiscalYearList'                => $fiscalYearList,
            'userBranchId'                  => $userBranchId,
            'samityLists'                   => $samityLists
        );

        return view('microfin.reports.registerReport.regular.savingInterestInformationRegister.reportFilteringPart', $filteringArray);
    }

    public function getReport(Request $req){
        // dd($req->all());
        $date = explode(',', $req->filDate);
        $dateFrom = $date[0];
        $dateTo = $date[1];
        // dd($dateTo);
        $branchId = (int)$req->filBranch;
        if((int)$req->filSamity > 0) {
            $samityIdArr = [(int)$req->filSamity];
            $samityName = DB::table('mfn_samity')->where('id', (int)$req->filSamity)->value('name');
        }
        else {
            $samityIdArr = DB::table('mfn_samity')->where('status', 1)->where('softDel', 0)->where('branchId', $branchId)->pluck('id')->toArray();
            $samityName = 'All';
        }
        // dd($samityIdArr);

        // saving interest informations
        $savingsInterestsInfo = DB::table('mfn_savings_interest')
                                ->where('branchIdFk', $branchId)
                                ->whereIn('samityIdFk', $samityIdArr)
                                ->where('effectiveDate','>=',$dateFrom)
                                ->where('effectiveDate','<=',$dateTo)
                                ->select(
                                    'memberIdFk', 'primaryProductIdFk', 'accIdFk', 'balanceBefore', 'interestAmount', 'effectiveDate'
                                )
                                ->get();
                                // dd($savingsInterestsInfo);

        $savingsInterestsInfoBySamity = DB::table('mfn_savings_interest')
                                        ->where('branchIdFk', $branchId)
                                        ->whereIn('samityIdFk', $samityIdArr)
                                        ->where('effectiveDate','>=',$dateFrom)
                                        ->where('effectiveDate','<=',$dateTo)
                                        ->select(
                                            'samityIdFk',
                                            DB::raw("SUM(balanceBefore) as savingsBalance"),
                                            DB::raw("SUM(interestAmount) as interestAmount")
                                        )
                                        ->groupBy('samityIdFk')
                                        ->get()->keyBy('samityIdFk')->toArray();
                                        // dd($savingsInterestsInfoBySamity);

        $savingsInterestsInfoByProduct = DB::table('mfn_savings_interest')
                                        ->where('branchIdFk', $branchId)
                                        ->whereIn('samityIdFk', $samityIdArr)
                                        ->where('effectiveDate','>=',$dateFrom)
                                        ->where('effectiveDate','<=',$dateTo)
                                        ->select(
                                            'productIdFk',
                                            // DB::raw("SUM(balanceBefore) as savingsBalance"),
                                            DB::raw("SUM(interestAmount) as interestAmount")
                                        )
                                        ->groupBy('productIdFk')
                                        ->get()->keyBy('productIdFk')->toArray();
                                        // dd($savingsInterestsInfoByProduct);

        $memberIdArr = $savingsInterestsInfo->unique('memberIdFk')->pluck('memberIdFk')->toArray();
        $savingsAcountIdArr = $savingsInterestsInfo->unique('accIdFk')->pluck('accIdFk')->toArray();
        // dd($savingsAcountIdArr);

        // members info
        $membersInfo = DB::table('mfn_member_information')
                    ->whereIn('id', $memberIdArr)
                    ->select('id', 'code', 'name', 'primaryProductId')
                    ->get()->keyBy('id')->toArray();
                    // dd($membersInfo);
        // samity infos
        $samityInfo = DB::table('mfn_samity')
                    ->whereIn('id', $samityIdArr)
                    ->select('id','code','name')
                    ->get()->keyBy('id')->toArray();
        // savings account info
        $savingsAccountInfo = DB::table('mfn_savings_account')
                            ->whereIn('id', $savingsAcountIdArr)
                            ->select('id','savingsCode', 'accountOpeningDate', 'memberIdFk', 'samityIdFk', 'savingsProductIdFk', 'savingsAmount')
                            ->get()->keyBy('id')->toArray();
                            // dd($savingsAccountInfo);
        // savings account info
        $savingsProductNames = DB::table('mfn_saving_product')->pluck('shortName', 'id')->toArray();
        // primary product names
        $primaryProductNames = DB::table('mfn_loans_product')->pluck('shortName', 'id')->toArray();

        $memberWiseData = [];

        // balance calculation for account
        // opening balance
        $accountsOpeningBalance = DB::table('mfn_opening_savings_account_info')
                                ->whereIn('savingsAccIdFk', $savingsAcountIdArr)
                                ->pluck('openingBalance', 'savingsAccIdFk')
                                ->toArray();
                                // dd($accountsOpeningBalance);

        // month closing deposits
        $accountsClosingDeposits = DB::table('mfn_savings_deposit')
                                    ->where('softDel', 0)
                                    ->whereIn('accountIdFk', $savingsAcountIdArr)
                                    ->where('paymentType', '!=', 'Interest')
                                    ->where('depositDate', '<=', $dateTo)
                                    ->groupBy('accountIdFk')
                                    ->select(
                                        'accountIdFk',
                                        DB::raw("sum(amount) as amount")
                                    )
                                    ->get()->keyBy('accountIdFk')->toArray();
                                      // dd($accountsClosingDeposits);

        // month closing withdraws
        $accountsClosingWithdraws = DB::table('mfn_savings_withdraw')
                                    ->where('softDel', 0)
                                    ->whereIn('accountIdFk', $savingsAcountIdArr)
                                    ->where('withdrawDate', '<=', $dateTo)
                                    ->groupBy('accountIdFk')
                                    ->select(
                                        'accountIdFk',
                                        DB::raw("sum(amount) as amount")
                                    )
                                    ->get()->keyBy('accountIdFk')->toArray();
                                    // dd($accountsClosingWithdraws);

        // balance calculation for samity
        // opening balance
        $samityOpeningBalance = DB::table('mfn_opening_savings_account_info')
                                ->whereIn('savingsAccIdFk', $savingsAcountIdArr)
                                ->groupBy('samityIdFk')
                                ->select(
                                    'samityIdFk',
                                    DB::raw("sum(openingBalance) as openingBalance")
                                )
                                ->get()->keyBy('samityIdFk')->toArray();
                                // dd($accountsOpeningBalance);

        // month closing deposits
        $samityClosingDeposits = DB::table('mfn_savings_deposit')
                                    ->where('softDel', 0)
                                    ->whereIn('accountIdFk', $savingsAcountIdArr)
                                    ->where('paymentType', '!=', 'Interest')
                                    ->where('depositDate', '<=', $dateTo)
                                    ->groupBy('samityIdFk')
                                    ->select(
                                        'samityIdFk',
                                        DB::raw("sum(amount) as amount")
                                    )
                                    ->get()->keyBy('samityIdFk')->toArray();
                                      // dd($accountsClosingDeposits);

        // month closing withdraws
        $samityClosingWithdraws = DB::table('mfn_savings_withdraw')
                                    ->where('softDel', 0)
                                    ->whereIn('accountIdFk', $savingsAcountIdArr)
                                    ->where('withdrawDate', '<=', $dateTo)
                                    ->groupBy('samityIdFk')
                                    ->select(
                                        'samityIdFk',
                                        DB::raw("sum(amount) as amount")
                                    )
                                    ->get()->keyBy('samityIdFk')->toArray();
                                    // dd($accountsClosingWithdraws);

        // balance calculation for product
        // opening balance
        $productsOpeningBalance = DB::table('mfn_opening_savings_account_info')
                                ->join('mfn_savings_account', 'mfn_savings_account.id', '=', 'mfn_opening_savings_account_info.savingsAccIdFk')
                                ->whereIn('mfn_opening_savings_account_info.savingsAccIdFk', $savingsAcountIdArr)
                                ->whereIn('mfn_savings_account.id', $savingsAcountIdArr)
                                ->groupBy('mfn_savings_account.savingsProductIdFk')
                                ->select(
                                    'mfn_savings_account.savingsProductIdFk as productId',
                                    DB::raw("sum(mfn_opening_savings_account_info.openingBalance) as openingBalance")
                                )
                                ->get()->keyBy('productId')->toArray();
                                // dd($productsOpeningBalance);

        // month closing deposits
        $productsClosingDeposits = DB::table('mfn_savings_deposit')
                                    ->where('softDel', 0)
                                    ->whereIn('accountIdFk', $savingsAcountIdArr)
                                    ->where('paymentType', '!=', 'Interest')
                                    ->where('depositDate', '<=', $dateTo)
                                    ->groupBy('productIdFk')
                                    ->select(
                                        'productIdFk',
                                        DB::raw("sum(amount) as amount")
                                    )
                                    ->get()->keyBy('productIdFk')->toArray();
                                      // dd($accountsClosingDeposits);

        // month closing withdraws
        $productsClosingWithdraws = DB::table('mfn_savings_withdraw')
                                    ->where('softDel', 0)
                                    ->whereIn('accountIdFk', $savingsAcountIdArr)
                                    ->where('withdrawDate', '<=', $dateTo)
                                    ->groupBy('productIdFk')
                                    ->select(
                                        'productIdFk',
                                        DB::raw("sum(amount) as amount")
                                    )
                                    ->get()->keyBy('productIdFk')->toArray();
                                    // dd($accountsClosingWithdraws);

        // loop for each account
        $memberWiseData = [];

        foreach ($savingsInterestsInfo as $key => $item) {

            $openingBalance = isset($accountsOpeningBalance[$item->accIdFk]) ? $accountsOpeningBalance[$item->accIdFk] : 0;
            $depositAmount = isset($accountsClosingDeposits[$item->accIdFk]) ? $accountsClosingDeposits[$item->accIdFk]->amount : 0;
            $withdrawAmount = isset($accountsClosingWithdraws[$item->accIdFk]) ? $accountsClosingWithdraws[$item->accIdFk]->amount : 0;
            $balance = $openingBalance + $depositAmount - $withdrawAmount;

            $memberWiseData[] = array(
                'memberCode'                => $membersInfo[$item->memberIdFk]->code,
                'memberName'                => $membersInfo[$item->memberIdFk]->name,
                'memberPrimaryProduct'      => $primaryProductNames[$item->primaryProductIdFk],
                'savingsId'                 => $savingsAccountInfo[$item->accIdFk]->savingsCode,
                'savingsOpeningDate'        => $savingsAccountInfo[$item->accIdFk]->accountOpeningDate,
                'savingsBalanceBefore'      => (double) $balance,
                'totalInterestAmount'       => (double) $item->interestAmount,
                'interestDisburseDate'      => Carbon::parse($item->effectiveDate)->format('d-m-Y')
            );

        }
        // dd($memberWiseData);

        // loop for each samity
        $samityWiseData = [];

        foreach ($samityInfo as $key => $samity) {

            $openingBalance = isset($samityOpeningBalance[$key]) ? $samityOpeningBalance[$key]->openingBalance : 0;
            $depositAmount = isset($samityClosingDeposits[$key]) ? $samityClosingDeposits[$key]->amount : 0;
            $withdrawAmount = isset($samityClosingWithdraws[$key]) ? $samityClosingWithdraws[$key]->amount : 0;
            $balance = $openingBalance + $depositAmount - $withdrawAmount;
            // $savingsBalance = isset($savingsInterestsInfoBySamity[$key]) ? $savingsInterestsInfoBySamity[$key]->savingsBalance : 0;
            $interestAmount = isset($savingsInterestsInfoBySamity[$key]) ? $savingsInterestsInfoBySamity[$key]->interestAmount : 0;

            $samityWiseData[] = array(
                'samityCode'                => $samity->code,
                'samityName'                => $samity->name,
                'savingsBalanceBefore'      => (double) $balance,
                'totalInterestAmount'       => (double) $interestAmount,
            );

        }
        // dd($savingsInterestsInfoByProduct);

        // loop for each product
        $productWiseData = [];

        foreach ($savingsInterestsInfoByProduct as $key => $item) {

            $openingBalance = isset($productsOpeningBalance[$key]) ? $productsOpeningBalance[$key]->openingBalance : 0;
            $depositAmount = isset($productsClosingDeposits[$key]) ? $productsClosingDeposits[$key]->amount : 0;
            $withdrawAmount = isset($productsClosingWithdraws[$key]) ? $productsClosingWithdraws[$key]->amount : 0;
            $balance = $openingBalance + $depositAmount - $withdrawAmount;

            $productWiseData[] = array(
                'productName'                => $savingsProductNames[$key],
                'savingsBalanceBefore'      => (double) $balance,
                'totalInterestAmount'       => (double) $item->interestAmount
            );

        }
        // dd($productWiseData);

        $data = array(
            'branchId'                  => $branchId,
            'samityName'                => $samityName,
            'dateFrom'                  => $dateFrom,
            'dateTo'                    => $dateTo,
            'memberWiseData'            => $memberWiseData,
            'samityWiseData'            => $samityWiseData,
            'savingsProductWiseData'    => $productWiseData,
            'savingsProductNames'       => $savingsProductNames
        );
        // dd($data);

        return view('microfin.reports.registerReport.regular.savingInterestInformationRegister.reportPage', $data);
    }

}

?>
