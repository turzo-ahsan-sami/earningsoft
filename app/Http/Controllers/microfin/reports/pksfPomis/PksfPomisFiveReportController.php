<?php

namespace App\Http\Controllers\microfin\reports\pksfPomis;
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

use App\Http\Controllers\microfin\MicroFin;

class PksfPomisFiveReportController extends Controller {

    public function index() {

        $userBranchId=Auth::user()->branchId;

        /// Report Level
        $reportLevelList = array(
            'Branch'  =>  'Branch',
            'Area'    =>  'Area',
            'Zone'    =>  'Zone',
            'Region'  =>  'Region'
        );

        /// Branch
        $branchList = DB::table('gnr_branch');

        if ($userBranchId!=1) {
            $branchList = $branchList->where('id', $userBranchId);
        }

        $branchList = $branchList
        ->orderBy('branchCode')
        ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
        ->pluck('nameWithCode', 'id')
        ->all();
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

        /// Funding Organization List
        $fundingOrgList = array(
            ''      => 'All',
            '1'     => 'PKSF',
            '2'     => 'Non-PKSF',
        );

        $filteringArray = array(
            'reportLevelList'     => $reportLevelList,
            'areaList'            => $areaList,
            'zoneList'            => $zoneList,
            'regionList'          => $regionList,
            'branchList'          => $branchList,
            'fundingOrgList'      => $fundingOrgList,
            'userBranchId'        => $userBranchId
        );

        return view('microfin.reports.pksfPomisReport.pksfPomis5Report.reportFilteringPart', $filteringArray);
    }

    public function getReport(Request $req){

        $filBranchIds = $this->getFilteredBranhIds($req->filReportLevel,$req->filBranch,$req->filArea,$req->filZone,$req->filRegion,$req->filFund);

        $reportDate = Carbon::parse($req->filDate)->format('Y-m-d');

        // Get the branches which have not done the month end process
        $branchIdsHavingMonthEnd = DB::table('mfn_month_end')
        ->where('date',$reportDate)
        ->pluck('branchIdFk')
        ->toArray();

        $monthEndPendingBranchIds = array_diff($filBranchIds, $branchIdsHavingMonthEnd);

        $monthEndPendingBranches = DB::table('gnr_branch')
        ->whereIn('id',$monthEndPendingBranchIds)
        ->orderBy('branchCode')
        ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"))
        ->pluck('nameWithCode')
        ->all();

        // consider the branch ids which are in month_end table
        if (Auth::user()->id!=1) {
            $filBranchIds = array_intersect($filBranchIds,$branchIdsHavingMonthEnd); 
        }        

        /////// For table One -- Note-1
        $serchingFiscalYear = DB::table('gnr_fiscal_year')
        ->where('fyStartDate','<=',$reportDate)
        ->where('fyEndDate','>=',$reportDate)
        ->first();

        if ($serchingFiscalYear==null) {
            return "<div style='text-align:center;font-size:16px;'>Report not available.</div><script type='text/javascript'>$('#loadingModal').hide();</script>";
        }

        $openingBalanceDate = Carbon::parse($serchingFiscalYear->fyStartDate)->subDay();

        
        $voucherDetails = DB::table('acc_voucher_details as vd')
        ->join('acc_voucher as v', 'vd.voucherId','v.id')
        ->whereIn('v.branchId',$filBranchIds)
        ->where('v.voucherDate','>=',$serchingFiscalYear->fyStartDate)
        ->where('v.voucherDate','<=',$reportDate)
        ->whereIn('v.projectTypeId',[2,3])
        ->where('v.status',1)
        ->where('vd.status',1)
        ->groupBy('v.projectTypeId','vd.debitAcc','vd.creditAcc')
        ->select(DB::raw('v.projectTypeId,vd.debitAcc,vd.creditAcc,SUM(vd.amount) AS amount'))
        ->get();

        $openingBalance = DB::table('acc_opening_balance')
        ->where('projectId',1)
        ->whereIn('branchId',$filBranchIds)
        ->where('openingDate',$openingBalanceDate)
        ->whereIn('projectTypeId',[2,3])
        ->groupBy('projectTypeId','ledgerId')
        ->select(DB::raw('projectTypeId,ledgerId,SUM(balanceAmount) AS balanceAmount'))
        ->get();
        

        /// Cumulative Service Charge Collection
        // $cumServiceChargeHeads = [86,89,91,92,95,103,106,110];
        $cumServiceChargeHeads = $serviceChargeConfig = DB::table('acc_mfn_av_config_loan')->pluck('ledgerIdForInterest')->toArray();
        $cumServiceChargeHeads = array_unique($cumServiceChargeHeads);
        
        // projectTypeId = 2 means PKSF, projectTypeId = 3 means NON-PKSF
        $projectTypeIdPksf = 2;
        $projectTypeIdNonPksf = 3;
        
        $cumSerChargeCollectionPKSF = $this->calculateCumulativeForFirtTable($openingBalance,$voucherDetails,$projectTypeIdPksf,$cumServiceChargeHeads);

        $cumSerChargeCollectionNON_PKSF = $this->calculateCumulativeForFirtTable($openingBalance,$voucherDetails,$projectTypeIdNonPksf,$cumServiceChargeHeads);

        /// Total Others Income
        $othersIncomeHeads = [116,119,120,126,131,135,141,149,153,157,162,166,169,173,176,178,185,188,192,198,203,206,208,447,448,449,450,523,530,531,532,545,556,566,574,593,594,606,607];

        
        $totalOthersIncomePKSF = $this->calculateCumulativeForFirtTable($openingBalance,$voucherDetails,$projectTypeIdPksf,$othersIncomeHeads);

        
        $totalOthersIncomeNON_PKSF = $this->calculateCumulativeForFirtTable($openingBalance,$voucherDetails,$projectTypeIdNonPksf,$othersIncomeHeads);

        /// Cumulative Total Expenditure
        $expenseHeads = DB::table('acc_account_ledger')
        ->where('accountTypeId',13)
        ->where('isGroupHead',0)
        ->pluck('id')
        ->toArray();

        
        $cumTotalExpenditurePKSF = abs($this->calculateCumulativeForFirtTable($openingBalance,$voucherDetails,$projectTypeIdPksf,$expenseHeads));
         /// Fund Account (Surplus)
        $surplusPKSF = $cumSerChargeCollectionPKSF + $totalOthersIncomePKSF  - $cumTotalExpenditurePKSF;

        
        $cumTotalExpenditureNON_PKSF = abs($this->calculateCumulativeForFirtTable($openingBalance,$voucherDetails,$projectTypeIdNonPksf,$expenseHeads));
        
        /// Fund Account (Surplus)
        $surplusNON_PKSF = $cumSerChargeCollectionNON_PKSF + $totalOthersIncomeNON_PKSF - $cumTotalExpenditureNON_PKSF;

        /// Reserve Fund
        $reserveFundHeads = DB::table('acc_account_ledger')
        ->where('accountTypeId',11)
        ->where('isGroupHead',0)
        ->pluck('id')
        ->toArray();

        
        $reserveFundPKSF = abs($this->calculateCumulativeForFirtTable($openingBalance,$voucherDetails,$projectTypeIdPksf,$reserveFundHeads));

        
        $reserveFundNON_PKSF = abs($this->calculateCumulativeForFirtTable($openingBalance,$voucherDetails,$projectTypeIdNonPksf,$othersIncomeHeads));

        /// Cumulative Total Salary Paid
        $staffSalaryHeads = [51,56];
        
        $staffSalaryPKSF = abs($this->calculateCumulativeForFirtTable($openingBalance,$voucherDetails,$projectTypeIdPksf,$staffSalaryHeads));

        
        $staffSalaryNON_PKSF = abs($this->calculateCumulativeForFirtTable($openingBalance,$voucherDetails,$projectTypeIdNonPksf,$staffSalaryHeads));


        ////// End Table One -- Note-1

        ////// Table Two & Three -- Note-2
        $fundAccounts = DB::table('acc_loan_register_account')
        ->where('projectId_fk',1)
        ->whereIn('projectTypeId_fk',[2,3,13])
        ->where('loanDate','<=',$reportDate)
        ->select('id','loanDate','loanAmount','repayAmount','loanProductId_fk','projectTypeId_fk')
        ->get();

        $pksfFundAccounts = $fundAccounts->where('projectTypeId_fk',2);
        $pksfFundLoanProducts = DB::table('gnr_loan_product')
        ->whereIn('id',$pksfFundAccounts->pluck('loanProductId_fk'))
        ->select('id','name')
        ->get();

        $non_pksfFundAccounts = $fundAccounts->whereIn('projectTypeId_fk',[3,13]);
        $non_pksfFundLoanProducts = DB::table('gnr_loan_product')
        ->whereIn('id',$non_pksfFundAccounts->pluck('loanProductId_fk'))
        ->select('id','name')
        ->get();

        $payments = DB::table('acc_loan_register_payments')
        ->whereIn('accId_fk',$fundAccounts->pluck('id'))
        ->where('paymentDate','<=',$reportDate)
        ->select('accId_fk','totalAmount','principalAmount','interestAmount')
        ->get();

        $paymentSchedules = DB::table('acc_loan_register_payment_schedule')
        ->whereIn('accId_fk',$fundAccounts->pluck('id'))
        ->where('paymentDate','<=',$reportDate)
        ->select('accId_fk','principalAmount','interestAmount','totalAmount')
        ->get();

        $nextYearDate = Carbon::parse($reportDate)->addYear()->format('Y-m-d');
        $nextPaymentSchedules = DB::table('acc_loan_register_payment_schedule')
        ->whereIn('accId_fk',$fundAccounts->pluck('id'))
        ->where('paymentDate','>=',$reportDate)
        ->where('paymentDate','<=',$nextYearDate)
        ->select('accId_fk','principalAmount','interestAmount','totalAmount')
        ->get();

        ////// End Table Two & Three -- Note-2

        
        ////// Table Four & Five -- Note-3
        $time_start = microtime(true);

        $deposits = DB::table('mfn_savings_deposit AS sd')
        ->join('mfn_member_information AS mi', 'mi.id', 'sd.memberIdFk')
        ->join('mfn_savings_account AS savAcc', 'savAcc.id', 'sd.accountIdFk')
        ->where('sd.softDel',0)
        ->where('mi.softDel',0)
        ->where('savAcc.softDel',0)
        ->whereIn('sd.branchIdFk',$filBranchIds)
        ->where('sd.depositDate','<=',$reportDate)
        ->groupBy('sd.primaryProductIdFk','sd.productIdFk','mi.gender')
        ->select(DB::raw('sd.primaryProductIdFk,sd.productIdFk, mi.gender, SUM(sd.amount) AS amount'))
        ->get();

        $openingSavingsInfos = DB::table('mfn_opening_info_savings')
        ->whereIn('branchIdFk',$filBranchIds)
        ->select('productIdFk','savingProductIdFk','genderTypeId','depositCollection','interestAmount','savingRefund')
        ->get();

        $withdraws = DB::table('mfn_savings_withdraw AS sw')
        ->join('mfn_member_information AS mi', 'mi.id', 'sw.memberIdFk')
        ->join('mfn_savings_account AS savAcc', 'savAcc.id', 'sw.accountIdFk')
        ->where('sw.softDel',0)
        ->where('mi.softDel',0)
        ->where('savAcc.softDel',0)
        ->whereIn('sw.branchIdFk',$filBranchIds)
        ->where('sw.withdrawDate','<=',$reportDate)
        ->groupBy('sw.primaryProductIdFk','sw.productIdFk','mi.gender')
        ->select(DB::raw('sw.primaryProductIdFk,sw.productIdFk, mi.gender, SUM(sw.amount) AS amount'))
        ->get();

        $savProducts = DB::table('mfn_saving_product')
        ->select('id','depositTypeIdFk')
        ->get();

        $pksfProductIds = DB::table('mfn_loans_product as t1')
        ->join('mfn_funding_organization as t2','t1.fundingOrganizationId','t2.id')
        ->where('t2.projectIdFk',1)
        ->where('t2.projectTypeIdFk',2)
        ->pluck('t1.id')
        ->toArray();

        $nonPksfProductIds = DB::table('mfn_loans_product as t1')
        ->join('mfn_funding_organization as t2','t1.fundingOrganizationId','t2.id')
        ->where('t2.projectIdFk',1)
        ->where('t2.projectTypeIdFk',3)
        ->pluck('t1.id')
        ->toArray();

        if ($req->filFund==1) {
            $nonPksfProductIds = [];
            $filFundingOrgName = 'PKSF';
        }
        elseif ($req->filFund==2) {
            $pksfProductIds = [];
            $filFundingOrgName = 'Non-PKSF';
        }
        else{
            $filFundingOrgName = 'All';
        }

        $genSavProductsIds = $savProducts->where('depositTypeIdFk',1)->pluck('id')->toArray();
        $volSavProductsIds = $savProducts->where('depositTypeIdFk',2)->pluck('id')->toArray();
        $otherSavProductsIds = $savProducts->where('depositTypeIdFk','>',2)->pluck('id')->toArray();

        //// PKSF
        // General Savings
        $cumGenSavCollMale = $deposits->whereIn('primaryProductIdFk',$pksfProductIds)->whereIn('productIdFk',$genSavProductsIds)->where('gender',1)->sum('amount');
        $cumGenSavCollMale += $openingSavingsInfos->whereIn('productIdFk',$pksfProductIds)->whereIn('savingProductIdFk',$genSavProductsIds)->where('genderTypeId',1)->sum('depositCollection');

        // WHEN INEREST WILL BE GENERATED THEN IT WILL BE ADDED. NOW ONLY INEREST FROM OPENING BALANCE IS CONSIDERED.
        $cumGenSavInterestMale = $openingSavingsInfos->whereIn('productIdFk',$pksfProductIds)->whereIn('savingProductIdFk',$genSavProductsIds)->where('genderTypeId',1)->sum('interestAmount');

        $cumGenSavWithdrawMale = $withdraws->whereIn('primaryProductIdFk',$pksfProductIds)->whereIn('productIdFk',$genSavProductsIds)->where('gender',1)->sum('amount');
        $cumGenSavWithdrawMale += $openingSavingsInfos->whereIn('productIdFk',$pksfProductIds)->whereIn('savingProductIdFk',$genSavProductsIds)->where('genderTypeId',1)->sum('savingRefund');

        $cumGenSavBalMale = $cumGenSavCollMale + $cumGenSavInterestMale - $cumGenSavWithdrawMale;

        $cumGenSavCollFemale = $deposits->whereIn('primaryProductIdFk',$pksfProductIds)->whereIn('productIdFk',$genSavProductsIds)->where('gender',2)->sum('amount');
        $cumGenSavCollFemale += $openingSavingsInfos->whereIn('productIdFk',$pksfProductIds)->whereIn('savingProductIdFk',$genSavProductsIds)->where('genderTypeId',2)->sum('depositCollection');

        // WHEN INEREST WILL BE GENERATED THEN IT WILL BE ADDED. NOW ONLY INEREST FROM OPENING BALANCE IS CONSIDERED.
        $cumGenSavInterestFemale = $openingSavingsInfos->whereIn('productIdFk',$pksfProductIds)->whereIn('savingProductIdFk',$genSavProductsIds)->where('genderTypeId',2)->sum('interestAmount');

        $cumGenSavWithdrawFemale = $withdraws->whereIn('primaryProductIdFk',$pksfProductIds)->whereIn('productIdFk',$genSavProductsIds)->where('gender',2)->sum('amount');
        $cumGenSavWithdrawFemale += $openingSavingsInfos->whereIn('productIdFk',$pksfProductIds)->whereIn('savingProductIdFk',$genSavProductsIds)->where('genderTypeId',2)->sum('savingRefund');

        $cumGenSavBalFemale = $cumGenSavCollFemale + $cumGenSavInterestFemale - $cumGenSavWithdrawFemale;

        // VOluntary Savings
        $cumVOlSavCollMale = $deposits->whereIn('primaryProductIdFk',$pksfProductIds)->whereIn('productIdFk',$volSavProductsIds)->where('gender',1)->sum('amount');
        $cumVOlSavCollMale += $openingSavingsInfos->whereIn('productIdFk',$pksfProductIds)->whereIn('savingProductIdFk',$volSavProductsIds)->where('genderTypeId',1)->sum('depositCollection');

        // WHEN INEREST WILL BE GENERATED THEN IT WILL BE ADDED. NOW ONLY INEREST FROM OPENING BALANCE IS CONSIDERED.
        $cumVOlSavInterestMale = $openingSavingsInfos->whereIn('productIdFk',$pksfProductIds)->whereIn('savingProductIdFk',$volSavProductsIds)->where('genderTypeId',1)->sum('interestAmount');

        $cumVOlSavWithdrawMale = $withdraws->whereIn('primaryProductIdFk',$pksfProductIds)->whereIn('productIdFk',$volSavProductsIds)->where('gender',1)->sum('amount');
        $cumVOlSavWithdrawMale += $openingSavingsInfos->whereIn('productIdFk',$pksfProductIds)->whereIn('savingProductIdFk',$volSavProductsIds)->where('genderTypeId',1)->sum('savingRefund');

        $cumVOlSavBalMale = $cumVOlSavCollMale + $cumVOlSavInterestMale - $cumVOlSavWithdrawMale;

        $cumVOlSavCollFemale = $deposits->whereIn('primaryProductIdFk',$pksfProductIds)->whereIn('productIdFk',$volSavProductsIds)->where('gender',2)->sum('amount');
        $cumVOlSavCollFemale += $openingSavingsInfos->whereIn('productIdFk',$pksfProductIds)->whereIn('savingProductIdFk',$volSavProductsIds)->where('genderTypeId',2)->sum('depositCollection');

        // WHEN INEREST WILL BE GENERATED THEN IT WILL BE ADDED. NOW ONLY INEREST FROM OPENING BALANCE IS CONSIDERED.
        $cumVOlSavInterestFemale = $openingSavingsInfos->whereIn('productIdFk',$pksfProductIds)->whereIn('savingProductIdFk',$volSavProductsIds)->where('genderTypeId',2)->sum('interestAmount');

        $cumVOlSavWithdrawFemale = $withdraws->whereIn('primaryProductIdFk',$pksfProductIds)->whereIn('productIdFk',$volSavProductsIds)->where('gender',2)->sum('amount');
        $cumVOlSavWithdrawFemale += $openingSavingsInfos->whereIn('productIdFk',$pksfProductIds)->whereIn('savingProductIdFk',$volSavProductsIds)->where('genderTypeId',2)->sum('savingRefund');

        $cumVOlSavBalFemale = $cumVOlSavCollFemale + $cumVOlSavInterestFemale - $cumVOlSavWithdrawFemale;

        // Other Savings
        $cumOthSavCollMale = $deposits->whereIn('primaryProductIdFk',$pksfProductIds)->whereIn('productIdFk',$otherSavProductsIds)->where('gender',1)->sum('amount');
        $cumOthSavCollMale += $openingSavingsInfos->whereIn('productIdFk',$pksfProductIds)->whereIn('savingProductIdFk',$otherSavProductsIds)->where('genderTypeId',1)->sum('depositCollection');

        // WHEN INEREST WILL BE GENERATED THEN IT WILL BE ADDED. NOW ONLY INEREST FROM OPENING BALANCE IS CONSIDERED.
        $cumOthSavInterestMale = $openingSavingsInfos->whereIn('productIdFk',$pksfProductIds)->whereIn('savingProductIdFk',$otherSavProductsIds)->where('genderTypeId',1)->sum('interestAmount');

        $cumOthSavWithdrawMale = $withdraws->whereIn('primaryProductIdFk',$pksfProductIds)->whereIn('productIdFk',$otherSavProductsIds)->where('gender',1)->sum('amount');
        $cumOthSavWithdrawMale += $openingSavingsInfos->whereIn('productIdFk',$pksfProductIds)->whereIn('savingProductIdFk',$otherSavProductsIds)->where('genderTypeId',1)->sum('savingRefund');

        $cumOthSavBalMale = $cumOthSavCollMale + $cumOthSavInterestMale - $cumOthSavWithdrawMale;

        $cumOthSavCollFemale = $deposits->whereIn('primaryProductIdFk',$pksfProductIds)->whereIn('productIdFk',$otherSavProductsIds)->where('gender',2)->sum('amount');
        $cumOthSavCollFemale += $openingSavingsInfos->whereIn('productIdFk',$pksfProductIds)->whereIn('savingProductIdFk',$otherSavProductsIds)->where('genderTypeId',2)->sum('depositCollection');

        // WHEN INEREST WILL BE GENERATED THEN IT WILL BE ADDED. NOW ONLY INEREST FROM OPENING BALANCE IS CONSIDERED.
        $cumOthSavInterestFemale = $openingSavingsInfos->whereIn('productIdFk',$pksfProductIds)->whereIn('savingProductIdFk',$otherSavProductsIds)->where('genderTypeId',2)->sum('interestAmount');

        $cumOthSavWithdrawFemale = $withdraws->whereIn('primaryProductIdFk',$pksfProductIds)->whereIn('productIdFk',$otherSavProductsIds)->where('gender',2)->sum('amount');
        $cumOthSavWithdrawFemale += $openingSavingsInfos->whereIn('productIdFk',$pksfProductIds)->whereIn('savingProductIdFk',$otherSavProductsIds)->where('genderTypeId',2)->sum('savingRefund');

        $cumOthSavBalFemale = $cumOthSavCollFemale + $cumOthSavInterestFemale - $cumOthSavWithdrawFemale;
        

        //// NON-PKSF
        // General Savings
        $cumGenSavCollMaleNonPksf = $deposits->whereIn('primaryProductIdFk',$nonPksfProductIds)->whereIn('productIdFk',$genSavProductsIds)->where('gender',1)->sum('amount');
        $cumGenSavCollMaleNonPksf += $openingSavingsInfos->whereIn('productIdFk',$nonPksfProductIds)->whereIn('savingProductIdFk',$genSavProductsIds)->where('genderTypeId',1)->sum('depositCollection');

        // WHEN INEREST WILL BE GENERATED THEN IT WILL BE ADDED. NOW ONLY INEREST FROM OPENING BALANCE IS CONSIDERED.
        $cumGenSavInterestMaleNonPksf = $openingSavingsInfos->whereIn('productIdFk',$nonPksfProductIds)->whereIn('savingProductIdFk',$genSavProductsIds)->where('genderTypeId',1)->sum('interestAmount');

        $cumGenSavWithdrawMaleNonPksf = $withdraws->whereIn('primaryProductIdFk',$nonPksfProductIds)->whereIn('productIdFk',$genSavProductsIds)->where('gender',1)->sum('amount');
        $cumGenSavWithdrawMaleNonPksf += $openingSavingsInfos->whereIn('productIdFk',$nonPksfProductIds)->whereIn('savingProductIdFk',$genSavProductsIds)->where('genderTypeId',1)->sum('savingRefund');

        $cumGenSavBalMaleNonPksf = $cumGenSavCollMaleNonPksf + $cumGenSavInterestMaleNonPksf - $cumGenSavWithdrawMaleNonPksf;

        $cumGenSavCollFemaleNonPksf = $deposits->whereIn('primaryProductIdFk',$nonPksfProductIds)->whereIn('productIdFk',$genSavProductsIds)->where('gender',2)->sum('amount');
        $cumGenSavCollFemaleNonPksf += $openingSavingsInfos->whereIn('productIdFk',$nonPksfProductIds)->whereIn('savingProductIdFk',$genSavProductsIds)->where('genderTypeId',2)->sum('depositCollection');

        // WHEN INEREST WILL BE GENERATED THEN IT WILL BE ADDED. NOW ONLY INEREST FROM OPENING BALANCE IS CONSIDERED.
        $cumGenSavInterestFemaleNonPksf = $openingSavingsInfos->whereIn('productIdFk',$nonPksfProductIds)->whereIn('savingProductIdFk',$genSavProductsIds)->where('genderTypeId',2)->sum('interestAmount');

        $cumGenSavWithdrawFemaleNonPksf = $withdraws->whereIn('primaryProductIdFk',$nonPksfProductIds)->whereIn('productIdFk',$genSavProductsIds)->where('gender',2)->sum('amount');
        $cumGenSavWithdrawFemaleNonPksf += $openingSavingsInfos->whereIn('productIdFk',$nonPksfProductIds)->whereIn('savingProductIdFk',$genSavProductsIds)->where('genderTypeId',2)->sum('savingRefund');

        $cumGenSavBalFemaleNonPksf = $cumGenSavCollFemaleNonPksf + $cumGenSavInterestFemaleNonPksf - $cumGenSavWithdrawFemaleNonPksf;

        // VOluntary Savings
        $cumVOlSavCollMaleNonPksf = $deposits->whereIn('primaryProductIdFk',$nonPksfProductIds)->whereIn('productIdFk',$volSavProductsIds)->where('gender',1)->sum('amount');
        $cumVOlSavCollMaleNonPksf += $openingSavingsInfos->whereIn('productIdFk',$nonPksfProductIds)->whereIn('savingProductIdFk',$volSavProductsIds)->where('genderTypeId',1)->sum('depositCollection');

        // WHEN INEREST WILL BE GENERATED THEN IT WILL BE ADDED. NOW ONLY INEREST FROM OPENING BALANCE IS CONSIDERED.
        $cumVOlSavInterestMaleNonPksf = $openingSavingsInfos->whereIn('productIdFk',$nonPksfProductIds)->whereIn('savingProductIdFk',$volSavProductsIds)->where('genderTypeId',1)->sum('interestAmount');

        $cumVOlSavWithdrawMaleNonPksf = $withdraws->whereIn('primaryProductIdFk',$nonPksfProductIds)->whereIn('productIdFk',$volSavProductsIds)->where('gender',1)->sum('amount');
        $cumVOlSavWithdrawMaleNonPksf += $openingSavingsInfos->whereIn('productIdFk',$nonPksfProductIds)->whereIn('savingProductIdFk',$volSavProductsIds)->where('genderTypeId',1)->sum('savingRefund');

        $cumVOlSavBalMaleNonPksf = $cumVOlSavCollMaleNonPksf + $cumVOlSavInterestMaleNonPksf - $cumVOlSavWithdrawMaleNonPksf;

        $cumVOlSavCollFemaleNonPksf = $deposits->whereIn('primaryProductIdFk',$nonPksfProductIds)->whereIn('productIdFk',$volSavProductsIds)->where('gender',2)->sum('amount');
        $cumVOlSavCollFemaleNonPksf += $openingSavingsInfos->whereIn('productIdFk',$nonPksfProductIds)->whereIn('savingProductIdFk',$volSavProductsIds)->where('genderTypeId',2)->sum('depositCollection');

        // WHEN INEREST WILL BE GENERATED THEN IT WILL BE ADDED. NOW ONLY INEREST FROM OPENING BALANCE IS CONSIDERED.
        $cumVOlSavInterestFemaleNonPksf = $openingSavingsInfos->whereIn('productIdFk',$nonPksfProductIds)->whereIn('savingProductIdFk',$volSavProductsIds)->where('genderTypeId',2)->sum('interestAmount');

        $cumVOlSavWithdrawFemaleNonPksf = $withdraws->whereIn('primaryProductIdFk',$nonPksfProductIds)->whereIn('productIdFk',$volSavProductsIds)->where('gender',2)->sum('amount');
        $cumVOlSavWithdrawFemaleNonPksf += $openingSavingsInfos->whereIn('productIdFk',$nonPksfProductIds)->whereIn('savingProductIdFk',$volSavProductsIds)->where('genderTypeId',2)->sum('savingRefund');

        $cumVOlSavBalFemaleNonPksf = $cumVOlSavCollFemaleNonPksf + $cumVOlSavInterestFemaleNonPksf - $cumVOlSavWithdrawFemaleNonPksf;

        // Other Savings
        $cumOthSavCollMaleNonPksf = $deposits->whereIn('primaryProductIdFk',$nonPksfProductIds)->whereIn('productIdFk',$otherSavProductsIds)->where('gender',1)->sum('amount');
        $cumOthSavCollMaleNonPksf += $openingSavingsInfos->whereIn('productIdFk',$nonPksfProductIds)->whereIn('savingProductIdFk',$otherSavProductsIds)->where('genderTypeId',1)->sum('depositCollection');

        // WHEN INEREST WILL BE GENERATED THEN IT WILL BE ADDED. NOW ONLY INEREST FROM OPENING BALANCE IS CONSIDERED.
        $cumOthSavInterestMaleNonPksf = $openingSavingsInfos->whereIn('productIdFk',$nonPksfProductIds)->whereIn('savingProductIdFk',$otherSavProductsIds)->where('genderTypeId',1)->sum('interestAmount');

        $cumOthSavWithdrawMaleNonPksf = $withdraws->whereIn('primaryProductIdFk',$nonPksfProductIds)->whereIn('productIdFk',$otherSavProductsIds)->where('gender',1)->sum('amount');
        $cumOthSavWithdrawMaleNonPksf += $openingSavingsInfos->whereIn('productIdFk',$nonPksfProductIds)->whereIn('savingProductIdFk',$otherSavProductsIds)->where('genderTypeId',1)->sum('savingRefund');

        $cumOthSavBalMaleNonPksf = $cumOthSavCollMaleNonPksf + $cumOthSavInterestMaleNonPksf - $cumOthSavWithdrawMaleNonPksf;

        $cumOthSavCollFemaleNonPksf = $deposits->whereIn('primaryProductIdFk',$nonPksfProductIds)->whereIn('productIdFk',$otherSavProductsIds)->where('gender',2)->sum('amount');
        $cumOthSavCollFemaleNonPksf += $openingSavingsInfos->whereIn('productIdFk',$nonPksfProductIds)->whereIn('savingProductIdFk',$otherSavProductsIds)->where('genderTypeId',2)->sum('depositCollection');

        // WHEN INEREST WILL BE GENERATED THEN IT WILL BE ADDED. NOW ONLY INEREST FROM OPENING BALANCE IS CONSIDERED.
        $cumOthSavInterestFemaleNonPksf = $openingSavingsInfos->whereIn('productIdFk',$nonPksfProductIds)->whereIn('savingProductIdFk',$otherSavProductsIds)->where('genderTypeId',2)->sum('interestAmount');

        $cumOthSavWithdrawFemaleNonPksf = $withdraws->whereIn('primaryProductIdFk',$nonPksfProductIds)->whereIn('productIdFk',$otherSavProductsIds)->where('gender',2)->sum('amount');
        $cumOthSavWithdrawFemaleNonPksf += $openingSavingsInfos->whereIn('productIdFk',$nonPksfProductIds)->whereIn('savingProductIdFk',$otherSavProductsIds)->where('genderTypeId',2)->sum('savingRefund');

        $cumOthSavBalFemaleNonPksf = $cumOthSavCollFemaleNonPksf + $cumOthSavInterestFemaleNonPksf - $cumOthSavWithdrawFemaleNonPksf;

        // echo('Total Execution Time: '.(microtime(true) - $time_start).' Sec');
        //// End NON-PKSF

        ////// End Table Four & Five -- Note-3

        ////// Table Six & Seven -- Note-4

        $loans = DB::table('mfn_loan AS loan')
        ->join('mfn_member_information AS member', 'member.id', 'loan.memberIdFk')
        ->where('loan.softDel',0)
        ->where('loan.isFromOpening',0)
        ->whereIn('loan.branchIdFk',$filBranchIds)
        ->where('loan.disbursementDate','<=',$reportDate)
        ->groupBy('loan.productIdFk','member.gender')
        ->select(DB::raw('loan.productIdFk,member.gender,SUM(loan.loanAmount) AS loanAmount'))
        ->get();

        $loanCollections = DB::table('mfn_loan_collection AS coll')
        ->join('mfn_member_information AS member', 'member.id', 'coll.memberIdFk')
        ->where('coll.softDel',0)
        ->whereIn('coll.branchIdFk',$filBranchIds)
        ->where('coll.collectionDate','<=',$reportDate)
        ->groupBy('coll.productIdFk','member.gender')
        ->select(DB::raw('coll.productIdFk,member.gender,SUM(coll.principalAmount) AS principalAmount'))
        ->get();

        $waivers = DB::table('mfn_loan_waivers AS waiver')
        ->join('mfn_member_information AS member', 'member.id', 'waiver.memberIdFk')
        ->where('waiver.softDel',0)
        ->whereIn('waiver.branchIdFk',$filBranchIds)
        ->where('waiver.date','<=',$reportDate)
        ->groupBy('waiver.productIdFk','member.gender')
        ->select(DB::raw('waiver.productIdFk,member.gender,SUM(waiver.principalAmount) AS principalAmount'))
        ->get();

        foreach ($waivers as $waiver) {
            $loanCollections->push([
                'productIdFk'       => $waiver->productIdFk,
                'gender'            => $waiver->gender,
                'principalAmount'   => $waiver->principalAmount
            ]);
        }

        $writeOffs = DB::table('mfn_loan_write_off AS writeOff')
        ->join('mfn_member_information AS member', 'member.id', 'writeOff.memberIdFk')
        ->where('writeOff.softDel',0)
        ->whereIn('writeOff.branchIdFk',$filBranchIds)
        ->where('writeOff.date','<=',$reportDate)
        ->groupBy('writeOff.productIdFk','member.gender')
        ->select(DB::raw('writeOff.productIdFk,member.gender,SUM(writeOff.principalAmount) AS principalAmount,count(writeOff.loanIdFk) AS writeOffNo'))
        ->get();

        foreach ($writeOffs as $writeOff) {
            $loanCollections->push([
                'productIdFk'       => $writeOff->productIdFk,
                'gender'            => $writeOff->gender,
                'principalAmount'   => $writeOff->principalAmount
            ]);
        }


        $branchOpeningLoanInfos = DB::table('mfn_opening_info_loan')
        ->whereIn('branchIdFk',$filBranchIds)
        ->select('productIdFk','genderTypeId','disbursedAmount','principalRecoveryAmount','recoveryAmount','writeOffAmountPrincipal','writeOffNo')
        ->get();

        // dd($loans);

        foreach ($branchOpeningLoanInfos as $branchOpeningLoanInfo) {

            $loans->push([
                'productIdFk'   => $branchOpeningLoanInfo->productIdFk,
                'gender'        => $branchOpeningLoanInfo->genderTypeId,
                'loanAmount'    => $branchOpeningLoanInfo->disbursedAmount
            ]);

            $loanCollections->push([
                'productIdFk'       => $branchOpeningLoanInfo->productIdFk,
                'gender'            => $branchOpeningLoanInfo->genderTypeId,
                'principalAmount'   => $branchOpeningLoanInfo->principalRecoveryAmount
            ]);

            $writeOffs->push([
                'productIdFk'       => $branchOpeningLoanInfo->productIdFk,
                'gender'            => $branchOpeningLoanInfo->genderTypeId,
                'principalAmount'   => $branchOpeningLoanInfo->writeOffAmountPrincipal,
                'writeOffNo'        => $branchOpeningLoanInfo->writeOffNo
            ]);
        }      
        

        $loanProductIds = array_unique(array_merge($loans->unique('productIdFk')->pluck('productIdFk')->toArray(), $loanCollections->unique('productIdFk')->pluck('productIdFk')->toArray()));

        $pksfLoanProducts = DB::table('mfn_loans_product')
        ->whereIn('id',$loanProductIds)
        ->whereIn('id',$pksfProductIds)
        ->select('id','productCategoryId','name')
        ->get();

        $pksfLoanCategories = DB::table('mfn_loans_product_category')
        ->whereIn('id',$pksfLoanProducts->pluck('productCategoryId'))
        ->select('id','name')
        ->get();

        $nonPksfLoanProducts = DB::table('mfn_loans_product')
        ->whereIn('id',$loanProductIds)
        ->whereIn('id',$nonPksfProductIds)
        ->select('id','productCategoryId')
        ->get();

        $nonPksfLoanCategories = DB::table('mfn_loans_product_category')
        ->whereIn('id',$nonPksfLoanProducts->pluck('productCategoryId'))
        ->select('id','name')
        ->get();


        ////// End Table Six & Seven -- Note-4


        ////// Loan Write Off Section

        // add opening disbursement amount to calculate cumulative disbursement amount and write off calculation
        $branchOpeningLoanInfo = DB::table('mfn_opening_info_loan')
        ->whereIn('branchIdFk',$filBranchIds)
        ->select('productIdFk','genderTypeId','disbursedAmount','principalRecoveryAmount','recoveryAmount','writeOffAmountPrincipal','writeOffNo')
        ->get();

        $serviceChargeConfig = DB::table('acc_mfn_av_config_loan')->get();

        /// PKSF

        $pksfLoanProductsForServiceCharge = DB::table('mfn_loans_product')
        ->whereIn('id',$pksfProductIds)
        ->select('id','productCategoryId','name')
        ->get();

        $pksfLoanCategoriesForServiceCharge = DB::table('mfn_loans_product_category')
        ->whereIn('id',$pksfLoanProductsForServiceCharge->pluck('productCategoryId'))
        ->select('id','name')
        ->get();

        $pksfCatgCumServiceCharges = array();

        foreach ($pksfLoanCategoriesForServiceCharge as $pksfLoanCategory) {
            $thisCategoryProductIds = $pksfLoanProductsForServiceCharge->where('productCategoryId',$pksfLoanCategory->id)->pluck('id')->toArray();

            $maleMemberWriteOffNo = $writeOffs->whereIn('productIdFk',$thisCategoryProductIds)->where('gender',1)->sum('writeOffNo');
            $maleMemberWriteOffAmount = $writeOffs->whereIn('productIdFk',$thisCategoryProductIds)->where('gender',1)->sum('principalAmount');

            $femaleMemberWriteOffNo = $writeOffs->whereIn('productIdFk',$thisCategoryProductIds)->where('gender',2)->sum('writeOffNo');
            $femaleMemberWriteOffAmount = $writeOffs->whereIn('productIdFk',$thisCategoryProductIds)->where('gender',2)->sum('principalAmount');

            $ledgerIdsForInterest = $serviceChargeConfig->whereIn('loanProductId',$thisCategoryProductIds)->pluck('ledgerIdForInterest')->toArray();
            
            $cumSerCharge = $this->calculateCumulativeForFirtTable($openingBalance,$voucherDetails,$projectTypeIdPksf,$ledgerIdsForInterest);

            $result = array(
                'categoryId' => $pksfLoanCategory->id,
                'cumSerCharge' => $cumSerCharge,
                'maleWriteOffNo' => $maleMemberWriteOffNo,
                'maleWriteOffAmount' => $maleMemberWriteOffAmount,
                'femaleWriteOffNo' => $femaleMemberWriteOffNo,
                'femaleWriteOffAmount' => $femaleMemberWriteOffAmount
            );
            if (max($cumSerCharge,$maleMemberWriteOffNo,$maleMemberWriteOffAmount,$femaleMemberWriteOffNo,$femaleMemberWriteOffAmount)>0) {
                array_push($pksfCatgCumServiceCharges, $result);
            }            
        }
        $pksfCatgCumServiceCharges = collect($pksfCatgCumServiceCharges);

        /// NON-PKSF
        $nonPksfLoanProductsForServiceCharge = DB::table('mfn_loans_product')
        ->whereIn('id',$nonPksfProductIds)
        ->select('id','productCategoryId')
        ->get();

        $nonPksfLoanCategoriesForServiceCharge = DB::table('mfn_loans_product_category')
        ->whereIn('id',$nonPksfLoanProductsForServiceCharge->pluck('productCategoryId'))
        ->select('id','name')
        ->get();

        $nonPksfCatgCumServiceCharges = array();

        foreach ($nonPksfLoanCategoriesForServiceCharge as $nonPksfLoanCategory) {
            $thisCategoryProductIds = $nonPksfLoanProductsForServiceCharge->where('productCategoryId',$nonPksfLoanCategory->id)->pluck('id')->toArray();

            $maleMemberWriteOffNo = $writeOffs->whereIn('productIdFk',$thisCategoryProductIds)->where('gender',1)->sum('writeOffNo');
            $maleMemberWriteOffAmount = $writeOffs->whereIn('productIdFk',$thisCategoryProductIds)->where('gender',1)->sum('principalAmount');

            $femaleMemberWriteOffNo = $writeOffs->whereIn('productIdFk',$thisCategoryProductIds)->where('gender',2)->sum('writeOffNo');
            $femaleMemberWriteOffAmount = $writeOffs->whereIn('productIdFk',$thisCategoryProductIds)->where('gender',2)->sum('principalAmount');

            $ledgerIdsForInterest = $serviceChargeConfig->whereIn('loanProductId',$thisCategoryProductIds)->pluck('ledgerIdForInterest')->toArray();
            
            $cumSerCharge = $this->calculateCumulativeForFirtTable($openingBalance,$voucherDetails,$projectTypeIdNonPksf,$ledgerIdsForInterest);
            $result = array(
                'categoryId' => $nonPksfLoanCategory->id,
                'cumSerCharge' => $cumSerCharge,
                'maleWriteOffNo' => $maleMemberWriteOffNo,
                'maleWriteOffAmount' => $maleMemberWriteOffAmount,
                'femaleWriteOffNo' => $femaleMemberWriteOffNo,
                'femaleWriteOffAmount' => $femaleMemberWriteOffAmount
            );
            if (max($cumSerCharge,$maleMemberWriteOffNo,$maleMemberWriteOffAmount,$femaleMemberWriteOffNo,$femaleMemberWriteOffAmount)>0) {
                array_push($nonPksfCatgCumServiceCharges, $result);
            }            
        }
        $nonPksfCatgCumServiceCharges = collect($nonPksfCatgCumServiceCharges);
        ////// End Loan Write Off Section

        $filBranch = isset($req->filBranch) ? $req->filBranch : Auth::user()->branchId;
        
        $data = array(

            'loans'    => $loans,
            'loanCollections'    => $loanCollections,


            'filBranchIds'                      => $filBranchIds,
            'filReportLevel'                    => $req->filReportLevel,
            'filArea'                           => $req->filArea,
            'filZone'                           => $req->filZone,
            'filRegion'                         => $req->filRegion,
            'filBranch'                         => $filBranch,
            'filServiceCharge'                  => $req->filServiceCharge,
            'filFund'                           => $req->filFund,
            'filRoundUup'                       => $req->filRoundUup,
            'filDate'                           => $req->filDate,
            'payments'                          => $payments,
            'paymentSchedules'                  => $paymentSchedules,
            'nextPaymentSchedules'              => $nextPaymentSchedules,
            'pksfCatgCumServiceCharges'         => $pksfCatgCumServiceCharges,
            'nonPksfCatgCumServiceCharges'      => $nonPksfCatgCumServiceCharges,
            'monthEndPendingBranches'           => $monthEndPendingBranches,
            'filFundingOrgName'                 => $filFundingOrgName
        );

        $data += array(
            'cumSerChargeCollectionPKSF'        => $cumSerChargeCollectionPKSF,
            'totalOthersIncomePKSF'             => $totalOthersIncomePKSF,
            'cumTotalExpenditurePKSF'           => $cumTotalExpenditurePKSF,
            'surplusPKSF'                       => $surplusPKSF,
            'reserveFundPKSF'                   => $reserveFundPKSF,
            'staffSalaryPKSF'                   => $staffSalaryPKSF,
            'pksfFundLoanProducts'              => $pksfFundLoanProducts,
            'pksfFundAccounts'                  => $pksfFundAccounts,

            'cumGenSavCollMale'                 => $cumGenSavCollMale,
            'cumGenSavInterestMale'             => $cumGenSavInterestMale,
            'cumGenSavWithdrawMale'             => $cumGenSavWithdrawMale,
            'cumGenSavBalMale'                  => $cumGenSavBalMale,

            'cumGenSavCollFemale'               => $cumGenSavCollFemale,
            'cumGenSavInterestFemale'           => $cumGenSavInterestFemale,
            'cumGenSavWithdrawFemale'           => $cumGenSavWithdrawFemale,
            'cumGenSavBalFemale'                => $cumGenSavBalFemale,

            'cumVOlSavCollMale'                 => $cumVOlSavCollMale,
            'cumVOlSavInterestMale'             => $cumVOlSavInterestMale,
            'cumVOlSavWithdrawMale'             => $cumVOlSavWithdrawMale,
            'cumVOlSavBalMale'                  => $cumVOlSavBalMale,

            'cumVOlSavCollFemale'               => $cumVOlSavCollFemale,
            'cumVOlSavInterestFemale'           => $cumVOlSavInterestFemale,
            'cumVOlSavWithdrawFemale'           => $cumVOlSavWithdrawFemale,
            'cumVOlSavBalFemale'                => $cumVOlSavBalFemale,

            'cumOthSavCollMale'                 => $cumOthSavCollMale,
            'cumOthSavInterestMale'             => $cumOthSavInterestMale,
            'cumOthSavWithdrawMale'             => $cumOthSavWithdrawMale,
            'cumOthSavBalMale'                  => $cumOthSavBalMale,

            'cumOthSavCollFemale'               => $cumOthSavCollFemale,
            'cumOthSavInterestFemale'           => $cumOthSavInterestFemale,
            'cumOthSavWithdrawFemale'           => $cumOthSavWithdrawFemale,
            'cumOthSavBalFemale'                => $cumOthSavBalFemale,

            'pksfLoanProducts'                  => $pksfLoanProducts,
            'pksfLoanCategories'                => $pksfLoanCategories,
        );

        $data += array(
            'cumSerChargeCollectionNON_PKSF'    => $cumSerChargeCollectionNON_PKSF,
            'totalOthersIncomeNON_PKSF'         => $totalOthersIncomeNON_PKSF,
            'cumTotalExpenditureNON_PKSF'       => $cumTotalExpenditureNON_PKSF,
            'surplusNON_PKSF'                   => $surplusNON_PKSF,
            'reserveFundNON_PKSF'               => $reserveFundNON_PKSF,
            'staffSalaryNON_PKSF'               => $staffSalaryNON_PKSF,
            'non_pksfFundLoanProducts'          => $non_pksfFundLoanProducts,
            'non_pksfFundAccounts'              => $non_pksfFundAccounts,

            'cumGenSavCollMaleNonPksf'          => $cumGenSavCollMaleNonPksf,
            'cumGenSavInterestMaleNonPksf'      => $cumGenSavInterestMaleNonPksf,
            'cumGenSavWithdrawMaleNonPksf'      => $cumGenSavWithdrawMaleNonPksf,
            'cumGenSavBalMaleNonPksf'           => $cumGenSavBalMaleNonPksf,

            'cumGenSavCollFemaleNonPksf'        => $cumGenSavCollFemaleNonPksf,
            'cumGenSavInterestFemaleNonPksf'    => $cumGenSavInterestFemaleNonPksf,
            'cumGenSavWithdrawFemaleNonPksf'    => $cumGenSavWithdrawFemaleNonPksf,
            'cumGenSavBalFemaleNonPksf'         => $cumGenSavBalFemaleNonPksf,

            'cumVOlSavCollMaleNonPksf'          => $cumVOlSavCollMaleNonPksf,
            'cumVOlSavInterestMaleNonPksf'      => $cumVOlSavInterestMaleNonPksf,
            'cumVOlSavWithdrawMaleNonPksf'      => $cumVOlSavWithdrawMaleNonPksf,
            'cumVOlSavBalMaleNonPksf'           => $cumVOlSavBalMaleNonPksf,

            'cumVOlSavCollFemaleNonPksf'        => $cumVOlSavCollFemaleNonPksf,
            'cumVOlSavInterestFemaleNonPksf'    => $cumVOlSavInterestFemaleNonPksf,
            'cumVOlSavWithdrawFemaleNonPksf'    => $cumVOlSavWithdrawFemaleNonPksf,
            'cumVOlSavBalFemaleNonPksf'         => $cumVOlSavBalFemaleNonPksf,

            'cumOthSavCollMaleNonPksf'          => $cumOthSavCollMaleNonPksf,
            'cumOthSavInterestMaleNonPksf'      => $cumOthSavInterestMaleNonPksf,
            'cumOthSavWithdrawMaleNonPksf'      => $cumOthSavWithdrawMaleNonPksf,
            'cumOthSavBalMaleNonPksf'           => $cumOthSavBalMaleNonPksf,

            'cumOthSavCollFemaleNonPksf'        => $cumOthSavCollFemaleNonPksf,
            'cumOthSavInterestFemaleNonPksf'    => $cumOthSavInterestFemaleNonPksf,
            'cumOthSavWithdrawFemaleNonPksf'    => $cumOthSavWithdrawFemaleNonPksf,
            'cumOthSavBalFemaleNonPksf'         => $cumOthSavBalFemaleNonPksf,

            'nonPksfLoanProducts'               => $nonPksfLoanProducts,
            'nonPksfLoanCategories'             => $nonPksfLoanCategories,
            
        );

        return view('microfin/reports/pksfPomisReport/pksfPomis5Report/pksfPomisFiveReport',$data);

    }

    public function getFilteredBranhIds($filReportLevel,$filBranch,$filArea,$filZone,$filRegion,$filFundingOrg){
        $userBranchId = Auth::user()->branchId;
        
        if ($userBranchId!=1) {
            $filBranchIds = [$userBranchId];
        }
        else{
            /// Report Level Branch
            if ($filReportLevel=="Branch") {             
                if ($filBranch!='' || $filBranch!=null) {
                    $filBranch = (int) $filBranch;
                    $filBranchIds = [$filBranch];
                }
                else{
                    $filBranchIds = array_map('intval',DB::table('gnr_branch')->pluck('id')->toArray());
                }
            }
            
            /// Report Level Area
            elseif ($filReportLevel=="Area") {
                $filBranchIds = array_map('intval',explode(',',str_replace(['"','[',']'],'',DB::table('gnr_area')->where('id',$filArea)->value('branchId'))));
            }
            /// Report Level Zone
            elseif ($filReportLevel=="Zone") {                
                $areaIds =  explode(',',str_replace(['"','[',']'],'',DB::table('gnr_zone')->where('id',$filZone)->value('areaId')));

                $filBranchIds = array();
                foreach ($areaIds as $key => $areaId) {
                    $branchIds = array_map('intval',explode(',',str_replace(['"','[',']'],'',DB::table('gnr_area')->where('id',$areaId)->value('branchId'))));
                    $filBranchIds = array_merge($filBranchIds,$branchIds);
                    $filBranchIds = array_unique($filBranchIds);                  
                }   
            }
            /// Report Level Region
            elseif ($filReportLevel=="Region") {
                $zoneIds = explode(',',str_replace(['"','[',']'],'',DB::table('gnr_region')->where('id',$filRegion)->value('zoneId')));

                $filBranchIds = array();
                foreach ($zoneIds as $zoneId) {
                    $areaIds =  explode(',',str_replace(['"','[',']'],'',DB::table('gnr_zone')->where('id',$zoneId)->value('areaId')));                
                    foreach ($areaIds as $key => $areaId) {
                        $branchIds = array_map('intval',explode(',',str_replace(['"','[',']'],'',DB::table('gnr_area')->where('id',$areaId)->value('branchId'))));
                        $filBranchIds = array_merge($filBranchIds,$branchIds);
                        $filBranchIds = array_unique($filBranchIds);                 
                    }
                }
            }
        }

        ///// FILTER THE BRANCES ACCORDING TO THE FUNDING ORGANIZATION
        // ONLY CONSIDER THE PKSF PROJECT BRANCHES.
        $pksfBranchIds = DB::table('gnr_branch')->where('projectId',1)->pluck('id')->toArray();
        $filBranchIds = array_intersect($filBranchIds,$pksfBranchIds);
        if ($filFundingOrg!='') {

            if ($filFundingOrg==2) {
                // IF IT IS OUTHES THEN INCLUDE THE 3,8 NO BRANCH IDS.
                $projectIds = DB::table('mfn_funding_organization')->where('id',$filFundingOrg)->groupBy('projectIdFk')->pluck('projectIdFk')->toArray();
                $projectTypeIds = DB::table('mfn_funding_organization')->where('id',$filFundingOrg)->groupBy('projectTypeIdFk')->pluck('projectTypeIdFk')->toArray();
                $fundingBranchIds = DB::table('gnr_branch')
                ->whereIn('projectId',$projectIds)
                ->whereIn('projectTypeId',$projectTypeIds)
                ->orWhereIn('id',[3,8])
                ->pluck('id')->toArray();
            }
            else{
                $projectIds = DB::table('mfn_funding_organization')->where('id',$filFundingOrg)->groupBy('projectIdFk')->pluck('projectIdFk')->toArray();
                $projectTypeIds = DB::table('mfn_funding_organization')->where('id',$filFundingOrg)->groupBy('projectTypeIdFk')->pluck('projectTypeIdFk')->toArray();
                $fundingBranchIds = DB::table('gnr_branch')
                ->whereIn('projectId',$projectIds)
                ->whereIn('projectTypeId',$projectTypeIds)
                ->pluck('id')->toArray();
            }

            $filBranchIds = array_intersect($filBranchIds,$fundingBranchIds);
        } 
        
        return $filBranchIds;
    }

    public function calculateCumulativeForFirtTable($openingBalance,$voucherDetails,$projectTypeId,$ledgerHeads){

        if ($projectTypeId==0) {
            $balance = - $openingBalance->whereIn('ledgerId',$ledgerHeads)->sum('balanceAmount') + $voucherDetails->whereIn('creditAcc',$ledgerHeads)->sum('amount') - $voucherDetails->whereIn('debitAcc',$ledgerHeads)->sum('amount');
        }
        else{
            $balance = - $openingBalance->where('projectTypeId',$projectTypeId)->whereIn('ledgerId',$ledgerHeads)->sum('balanceAmount') + $voucherDetails->where('projectTypeId',$projectTypeId)->whereIn('creditAcc',$ledgerHeads)->sum('amount') - $voucherDetails->where('projectTypeId',$projectTypeId)->whereIn('debitAcc',$ledgerHeads)->sum('amount');
        }        

        return $balance;
    }


}
