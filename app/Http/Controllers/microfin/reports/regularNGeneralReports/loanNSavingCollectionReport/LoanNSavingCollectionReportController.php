<?php

namespace App\Http\Controllers\microfin\reports\regularNGeneralReports\loanNSavingCollectionReport;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\microfin\MicroFinance;

class LoanNSavingCollectionReportController extends Controller {

	protected $MicroFinance;

	public function __construct() {

		$this->MicroFinance = New MicroFinance;
	}

    public function index() {

        $userBranchId=Auth::user()->branchId;
        if ($userBranchId==1) {
            $branchesOption = $this->MicroFinance->getBranchOptions(1);
        }else{
            $branchesOption=DB::table('gnr_branch')
            ->where('id', $userBranchId)
            ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
            ->pluck('nameWithCode', 'id')
            ->all();
        }
        
        $yearsOption = $this->MicroFinance->getYearsOption();
        $monthsOption = $this->MicroFinance->getMonthsOption();
        $samityDayOption = $this->MicroFinance->getSamityDay();
        $productCategoryOption = $this->MicroFinance->getProductCategoryList();

        $filteringArray = array(
          'branchesOption'          => $branchesOption, 
          'yearsOption'             => $yearsOption, 
          'monthsOption'            => $monthsOption, 
          'samityDayOption'         => $samityDayOption, 
          'productCategoryOption'   => $productCategoryOption, 
          'userBranchId'   => $userBranchId, 
      );

        return view('microfin/reports/regularNGeneralReports/loanNSavingCollectionReport/reportFilteringPart', ['filteringArray' => $filteringArray]);
    }
    public function loanNSavingCollectionReport(Request $request) {

    	$branchValue               = (int)$request->filBranch;
    	$yearValue                 = (int)$request->filYear;
    	$monthValue                = (int)$request->filMonth;
    	$samityDayValue            = (int)$request->filSamityDay;
    	$samityValue               = (int)$request->filSamity;
    	$productCategoryValue      = (int)$request->filProductCategory;
    	$productValue              = (int)$request->filProduct;
    	$reportOptionValue         = (int)$request->filReportOption;
    	$memberStatusValue         = (int)$request->filMemberStatus;
    	$modeValue                 = (int)$request->filMode;
    	$memberCodeValue           = (int)$request->filMemberCode;
    	$collectionSheetFilValue   = (int)$request->filCollectionSheet;
        $collectionSheetOptionValue = (int)$request->filCollectionSheetOption;

        $curentMonthFirstDate = Carbon::parse('01-'.$monthValue.'-'.$yearValue)->format('Y-m-d');
        $curentMonthLastDate = Carbon::parse('01-'.$monthValue.'-'.$yearValue)->endOfMonth()->format('Y-m-d');

        $mfnMemberInfo=DB::table('mfn_member_information')->where('samityId', $samityValue)->where('branchId', $branchValue)->where('softDel', 0);
        // $mfnMemberInfo=DB::table('mfn_member_information');

        if ($productCategoryValue==-1) {
            // $productsArr=DB::table('mfn_loans_product')->where('softDel', 0)->pluck('id')->toArray();
            // $memberIdArr=DB::table('mfn_loan')->whereIn('productIdFk', $productsArr)->where('branchIdFk', $branchValue)->where('samityIdFk', $samityValue)->where('softDel', 0)->pluck('id')->toArray();
        }else{
            if ($productValue==-1){
                $productsArr=DB::table('mfn_loans_product')->where('productCategoryId', $productCategoryValue)->where('softDel', 0)->pluck('id')->toArray();
            }else{
                $productsArr=[$productValue];
            }
            $memberIdArr=DB::table('mfn_loan')->whereIn('productIdFk', $productsArr)->where('branchIdFk', $branchValue)->where('samityIdFk', $samityValue)->where('isLoanCompleted', 0)->where('softDel', 0)->pluck('memberIdFk')->toArray();
            // $mfnMemberInfo = $mfnMemberInfo->whereIn('id', $memberIdArr);       
            $mfnMemberInfo = $mfnMemberInfo->where(function($query) use ($productsArr,$memberIdArr){
                $query->whereIn('primaryProductId', $productsArr)
                ->orWhereIn('id',$memberIdArr);
            });

        }
        $mfnMemberInfo = $mfnMemberInfo->select('id','name','spouseFatherSonName','code','primaryProductId')->get();

        $mfnLoanMemberInfo = '';

        if ($reportOptionValue==2) {

            $mfnLoanMemberInfo=DB::table('mfn_member_information')->where('samityId', $samityValue)->where('branchId', $branchValue)->select('id','name','spouseFatherSonName','code','primaryProductId')->where('softDel', 0);

            if ($productCategoryValue==-1) {
                // $loanIdArr=DB::table('mfn_loan_schedule')->where('scheduleDate','>=', $curentMonthFirstDate)->where('scheduleDate','<=',  $curentMonthLastDate)->pluck('loanIdFk')->toArray();

                // $memberIdArr=DB::table('mfn_loan')->whereIn('productIdFk', $productsArr)->where('branchIdFk', $branchValue)->where('samityIdFk', $samityValue)->whereIn('id', $loanIdArr)->where('isLoanCompleted', 0)->where('softDel', 0)->pluck('memberIdFk')->toArray();

                // $loandIdArr=DB::table('mfn_loan')->whereIn('productIdFk', $productsArr)->where('branchIdFk', $branchValue)->where('samityIdFk', $samityValue)->where('isLoanCompleted', 0)->where('softDel', 0)->pluck('id')->toArray();

               /* $loanIdFkArr=DB::table('mfn_loan_schedule')->where('scheduleDate','>=', $curentMonthFirstDate)->where('scheduleDate','<=',  $curentMonthLastDate)->pluck('loanIdFk')->toArray();*/

               $memberIdArr=DB::table('mfn_loan')->where('branchIdFk', $branchValue)->where('samityIdFk', $samityValue)/*->whereIn('id', $loanIdFkArr)*/->where('isLoanCompleted', 0)->where('softDel', 0)->pluck('memberIdFk')->toArray();
           }else{

            if ($productValue==-1){
                $productsArr=DB::table('mfn_loans_product')->where('productCategoryId', $productCategoryValue)->where('softDel', 0)->pluck('id')->toArray();            

            }else{
                $productsArr=[$productValue];

            }

            $loandIdArr=DB::table('mfn_loan')->whereIn('productIdFk', $productsArr)->where('branchIdFk', $branchValue)->where('samityIdFk', $samityValue)->where('isLoanCompleted', 0)->where('softDel', 0)->pluck('id')->toArray();

            /*$loanIdFkArr=DB::table('mfn_loan_schedule')->where('scheduleDate','>=', $curentMonthFirstDate)->where('scheduleDate','<=',  $curentMonthLastDate)->whereIn('loanIdFk', $loandIdArr)->pluck('loanIdFk')->toArray();*/

            $memberIdArr=DB::table('mfn_loan')->whereIn('productIdFk', $productsArr)->where('branchIdFk', $branchValue)->where('samityIdFk', $samityValue)/*->whereIn('id', $loanIdFkArr)*/->where('isLoanCompleted', 0)->where('softDel', 0)->pluck('memberIdFk')->toArray();
                // $mfnLoanMemberInfo = $mfnLoanMemberInfo->whereIn('id', $memberIdArr);            
        }





            // if ($productValue==-1){
            //     $productsArr=DB::table('mfn_loans_product')->where('productCategoryId', $productCategoryValue)->where('softDel', 0)->pluck('id')->toArray();
            // }else{
            //     $productsArr=[$productValue];
            // }


            // $loanIdArr=DB::table('mfn_loan_schedule')->where('scheduleDate','>=', $curentMonthFirstDate)->where('scheduleDate','<=',  $curentMonthLastDate)->pluck('loanIdFk')->toArray();

            // $memberIdArr=DB::table('mfn_loan')->whereIn('productIdFk', $productsArr)->where('branchIdFk', $branchValue)->where('samityIdFk', $samityValue)->whereIn('id', $loanIdArr)->where('isLoanCompleted', 0)->where('softDel', 0)->pluck('memberIdFk')->toArray();

        $mfnLoanMemberInfo=$mfnLoanMemberInfo->whereIn('id', $memberIdArr)->get();

    }

    $allRegSavingsAccounts = DB::table('mfn_savings_account')->where('softDel',0)->where('status',1)->whereIn('memberIdFk',$mfnMemberInfo->pluck('id'))->where('savingsProductIdFk',1)->get();

    $volMonthlySavingsProducts = DB::table('mfn_saving_product')
    ->where('softDel',0)
    ->where('depositTypeIdFk',2)
    ->where('savingCollectionFrequencyIdFk',2)
    ->pluck('id');
    
    $allVolMonthlySavingsAccounts = DB::table('mfn_savings_account')
    ->where('softDel',0)
    ->where('status',1)
    ->whereIn('memberIdFk',$mfnMemberInfo->pluck('id'))
    ->where('accountOpeningDate','<=',$curentMonthFirstDate)
    ->whereIn('savingsProductIdFk',$volMonthlySavingsProducts)
    ->get();

    $allSavAccIds = array_merge($allRegSavingsAccounts->pluck('id')->toArray(),$allVolMonthlySavingsAccounts->pluck('id')->toArray());

    $savDeposits = DB::table('mfn_savings_deposit')
    ->where('softDel',0)
    ->whereIn('accountIdFk',$allSavAccIds)
    ->where('depositDate','<',$curentMonthFirstDate)
    ->select('accountIdFk','depositDate','amount')
    ->get();

    $openingSavingsBalances = DB::table('mfn_opening_savings_account_info')
    ->whereIn('savingsAccIdFk',$allSavAccIds)
    ->select('savingsAccIdFk','openingBalance')
    ->get();

    foreach ($openingSavingsBalances as $openingSavingsBalance) {
        $savDeposits->push([
            'accountIdFk'  => $openingSavingsBalance->savingsAccIdFk,
            'amount'  => $openingSavingsBalance->openingBalance
        ]);
    }

    $savWithdraws = DB::table('mfn_savings_withdraw')
    ->where('softDel',0)
    ->whereIn('accountIdFk',$allSavAccIds)
    ->where('withdrawDate','<',$curentMonthFirstDate)
    ->select('accountIdFk','withdrawDate','amount')
    ->get();

    $allLoanAccounts = DB::table('mfn_loan')
    ->where('softDel',0)
    ->where('disbursementDate','<=',$curentMonthLastDate)
    ->where('isLoanCompleted',0)
    ->whereIn('memberIdFk',$mfnMemberInfo->pluck('id'))
    ->select('id','memberIdFk','disbursementDate','loanAmount','loanCycle','totalRepayAmount','loanCode')
    ->get();


    $loanSchedules = DB::table('mfn_loan_schedule')
    ->where('softDel',0)
    ->where('scheduleDate','<=',$curentMonthLastDate)
    ->whereIn('loanIdFk',$allLoanAccounts->pluck('id'))
    ->select('loanIdFk','scheduleDate','installmentAmount','isCompleted','partiallyPaidAmount')
    ->get();

    $loanCollections = DB::table('mfn_loan_collection')
    ->where('softDel',0)
    ->whereIn('loanIdFk',$allLoanAccounts->pluck('id'))
    ->where('collectionDate','<',$curentMonthFirstDate)
    ->select('loanIdFk','amount')
    ->get();

    $waivers = DB::table('mfn_loan_waivers')
    ->where('softDel',0)
    ->whereIn('loanIdFk',$allLoanAccounts->pluck('id'))
    ->where('date','<',$curentMonthFirstDate)
    ->select('loanIdFk','amount','principalAmount')
    ->get();

    foreach ($waivers as $waiver) {
        $loanCollections->push([
            'loanIdFk'       => $waiver->loanIdFk,
            'amount'         => $waiver->amount
        ]);
    }

    $rebates = DB::table('mfn_loan_rebates')
    ->where('softDel',0)
    ->whereIn('loanIdFk',$allLoanAccounts->pluck('id'))
    ->where('date','<',$curentMonthFirstDate)
    ->select('loanIdFk','amount')
    ->get();

    foreach ($rebates as $rebate) {
        $loanCollections->push([
            'loanIdFk'       => $rebate->loanIdFk,
            'amount'         => $rebate->amount
        ]);
    }

    $writeOffs = DB::table('mfn_loan_write_off')
    ->where('softDel',0)
    ->whereIn('loanIdFk',$allLoanAccounts->pluck('id'))
    ->where('date','<',$curentMonthFirstDate)
    ->select('loanIdFk','amount')
    ->get();

    foreach ($writeOffs as $writeOff) {
        $loanCollections->push([
            'loanIdFk'       => $writeOff->loanIdFk,
            'amount'         => $writeOff->amount
        ]);
    }


    $openingLoanBalances = DB::table('mfn_opening_balance_loan')
    ->whereIn('loanIdFk',$allLoanAccounts->pluck('id'))
    ->where('date','<',$curentMonthFirstDate)
    ->select('loanIdFk','paidLoanAmountOB')
    ->get();

    foreach ($openingLoanBalances as $openingLoanBalance) {
        $loanCollections->push([
            'loanIdFk'  => $openingLoanBalance->loanIdFk,
            'amount'  => $openingLoanBalance->paidLoanAmountOB
        ]);
    }

    $weekDays = $this->MicroFinance->getWeeklyDaysOfMonth($yearValue, $monthValue, $samityDayValue);
    $monthArr = $this->MicroFinance->getMonthsOption();
    $samityDayArr = $this->MicroFinance->getSamityDay();

    $reportingArr = array(
      'branchValue'                     => $branchValue, 
      'yearValue'                       => $yearValue, 
      'monthValue'                      => $monthValue, 
      'monthArr'                        => $monthArr, 
      'samityDayValue'                  => $samityDayValue, 
      'samityValue'                     => $samityValue, 
      'samityDayArr'                    => $samityDayArr, 
      'productCategoryValue'            => $productCategoryValue, 
      'productValue'                    => $productValue, 
      'reportOptionValue'               => $reportOptionValue, 
      'memberStatusValue'               => $memberStatusValue, 
      'modeValue'                       => $modeValue, 
      'memberCodeValue'                 => $memberCodeValue, 
      'collectionSheetFilValue'         => $collectionSheetFilValue, 
      'collectionSheetOptionValue'      => $collectionSheetOptionValue, 
      'weekDays'                        => $weekDays,
            // 'memberIdArr'                        => $memberIdArr,
            // 'primaryProductIdArr'                         => $primaryProductIdArr,
            // 'productsArr'                           => $productsArr,
      'mfnMemberInfo'                   => $mfnMemberInfo,
      'mfnLoanMemberInfo'               => $mfnLoanMemberInfo,

      'savDeposits'                     => $savDeposits,
      'savWithdraws'                    => $savWithdraws,
      'allRegSavingsAccounts'           => $allRegSavingsAccounts,
      'allVolMonthlySavingsAccounts'    => $allVolMonthlySavingsAccounts,
      'allLoanAccounts'                 => $allLoanAccounts,
      'loanSchedules'                   => $loanSchedules,
      'loanCollections'                 => $loanCollections,
  );

    if ($reportOptionValue==1) {

        return view('microfin/reports/regularNGeneralReports/loanNSavingCollectionReport/onePartLoanNSavingCollectionReport', $reportingArr);
    }else if ($reportOptionValue==2) {

       return view('microfin/reports/regularNGeneralReports/loanNSavingCollectionReport/twoPartLoanNSavingCollectionReport', $reportingArr);
   }

    	// return view('microfin.reports.regularNGeneralReports.loanNSavingCollectionReport.loanNSavingCollectionReport');
}

}
