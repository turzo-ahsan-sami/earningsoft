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
        
        $filBranchIds = $this->getFilteredBranhIds($req->filReportLevel,$req->filBranch,$req->filArea,$req->filZone,$req->filRegion);

        $reportDate = Carbon::parse($req->filDate)->format('Y-m-d');

        /////// For table One -- Note-1
        $serchingFiscalYear = DB::table('gnr_fiscal_year')
                                    ->where('fyStartDate','<=',$reportDate)
                                    ->where('fyEndDate','>=',$reportDate)
                                    ->first();

        $openingBalanceDate = Carbon::parse($serchingFiscalYear->fyStartDate)->subDay();

        /// Cumulative Service Charge Collection
        $cumServiceChargeHeads = [86,89,91,92,95,103,106,110];

        $cumSerChargeCollectionPKSF = $this->calculateCumulativeForFirtTable(1,$filBranchIds,$openingBalanceDate,$cumServiceChargeHeads,$serchingFiscalYear,$reportDate);


        $cumSerChargeCollectionNON_PKSF = $this->calculateCumulativeForFirtTable(0,$filBranchIds,$openingBalanceDate,$cumServiceChargeHeads,$serchingFiscalYear,$reportDate);            

        /// Total Others Income
        $othersIncomeHeads = [116,119,120,126,131,135,141,149,153,157,162,166,169,173,176,178,185,188,192,198,203,206,208,447,448,449,450,523,530,531,532,545,556,566,574,593,594,606,607];

        $totalOthersIncomePKSF = $this->calculateCumulativeForFirtTable(1,$filBranchIds,$openingBalanceDate,$othersIncomeHeads,$serchingFiscalYear,$reportDate);
        $totalOthersIncomeNON_PKSF = $this->calculateCumulativeForFirtTable(0,$filBranchIds,$openingBalanceDate,$othersIncomeHeads,$serchingFiscalYear,$reportDate);

        /// Cumulative Total Expenditure
        $expenseHeads = DB::table('acc_account_ledger')
                                ->where('accountTypeId',13)
                                ->where('isGroupHead',0)
                                ->pluck('id')
                                ->toArray();

        $cumTotalExpenditurePKSF = abs($this->calculateCumulativeForFirtTable(1,$filBranchIds,$openingBalanceDate,$expenseHeads,$serchingFiscalYear,$reportDate));
         /// Fund Account (Surplus)
        $surplusPKSF = $cumSerChargeCollectionPKSF + $totalOthersIncomePKSF  - $cumTotalExpenditurePKSF;

        $cumTotalExpenditureNON_PKSF = abs($this->calculateCumulativeForFirtTable(0,$filBranchIds,$openingBalanceDate,$expenseHeads,$serchingFiscalYear,$reportDate));
        /// Fund Account (Surplus)
        $surplusNON_PKSF = $cumSerChargeCollectionNON_PKSF + $totalOthersIncomeNON_PKSF - $cumTotalExpenditureNON_PKSF;

        /// Reserve Fund
        $reserveFundHeads = DB::table('acc_account_ledger')
                                ->where('accountTypeId',11)
                                ->where('isGroupHead',0)
                                ->pluck('id')
                                ->toArray();

        $reserveFundPKSF = abs($this->calculateCumulativeForFirtTable(1,$filBranchIds,$openingBalanceDate,$reserveFundHeads,$serchingFiscalYear,$reportDate));
        $reserveFundNON_PKSF = abs($this->calculateCumulativeForFirtTable(0,$filBranchIds,$openingBalanceDate,$reserveFundHeads,$serchingFiscalYear,$reportDate));

        /// Cumulative Total Salary Paid
        $staffSalaryHeads = [51,56];
        $staffSalaryPKSF = abs($this->calculateCumulativeForFirtTable(1,$filBranchIds,$openingBalanceDate,$staffSalaryHeads,$serchingFiscalYear,$reportDate));
        $staffSalaryNON_PKSF = abs($this->calculateCumulativeForFirtTable(0,$filBranchIds,$openingBalanceDate,$staffSalaryHeads,$serchingFiscalYear,$reportDate));

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

        $savingsAccounts = DB::table('mfn_savings_account')
                                ->where('softDel',0)
                                ->whereIn('branchIdFk',$filBranchIds)
                                ->where('accountOpeningDate','<=',$reportDate)
                                ->select('id','memberIdFk','depositTypeIdFk')
                                ->get();

        $generalSavingAccounts = $savingsAccounts->where('depositTypeIdFk',1);
        $voluntarySavingAccounts = $savingsAccounts->where('depositTypeIdFk',2);
        $otherSavingAccounts = $savingsAccounts->where('depositTypeIdFk','>',2);

        $allMembers = DB::table('mfn_member_information')
                                ->where('softDel',0)
                                ->whereIn('branchId',$filBranchIds)
                                ->where('admissionDate','<=',$reportDate)
                                ->select('id','gender','primaryProductId')
                                ->get();

        $maleMemberIds = $allMembers->where('gender',1)->pluck('id')->toArray();
        $femaleMemberIds = $allMembers->where('gender',2)->pluck('id')->toArray();

        $pksfProductIds = DB::table('mfn_loans_product as t1')
                            ->join('mfn_funding_organization as t2','t1.fundingOrganizationId','t2.id')
                            ->where('t2.projectIdFk',1)
                            ->where('t2.projectTypeIdFk',2)
                            ->pluck('t1.id')
                            ->toArray();
        $pksfMaleMemberIds = $allMembers->where('gender',1)->whereIn('primaryProductId',$pksfProductIds)->pluck('id')->toArray();
        $pksfFemaleMemberIds = $allMembers->where('gender',2)->whereIn('primaryProductId',$pksfProductIds)->pluck('id')->toArray();

        $nonPksfProductIds = DB::table('mfn_loans_product as t1')
                            ->join('mfn_funding_organization as t2','t1.fundingOrganizationId','t2.id')
                            ->where('t2.projectIdFk',1)
                            ->where('t2.projectTypeIdFk','!=',2)
                            ->pluck('t1.id')
                            ->toArray();
        $nonPksfMaleMemberIds = $allMembers->where('gender',1)->whereIn('primaryProductId',$nonPksfProductIds)->pluck('id')->toArray();
        $nonPksfFemaleMemberIds = $allMembers->where('gender',2)->whereIn('primaryProductId',$nonPksfProductIds)->pluck('id')->toArray();

        $deposits = DB::table('mfn_savings_deposit')
                        ->where('softDel',0)
                        ->whereIn('memberIdFk',$allMembers->pluck('id'))
                        ->where('depositDate','<=',$reportDate)
                        ->select('memberIdFk','amount','accountIdFk')
                        ->get();

        $withdraws = DB::table('mfn_savings_withdraw')
                        ->where('softDel',0)
                        ->whereIn('memberIdFk',$allMembers->pluck('id'))
                        ->where('withdrawDate','<=',$reportDate)
                        ->select('memberIdFk','amount','accountIdFk')
                        ->get();

        //// PKSF
        // General Savings
        $cumGenSavCollMale = $deposits->whereIn('accountIdFk',$generalSavingAccounts->pluck('id'))->whereIn('memberIdFk',$pksfMaleMemberIds)->sum('amount');
        $cumGenSavWithdrawMale = $withdraws->whereIn('accountIdFk',$generalSavingAccounts->pluck('id'))->whereIn('memberIdFk',$pksfMaleMemberIds)->sum('amount');
        $cumGenSavBalMale = $cumGenSavCollMale - $cumGenSavWithdrawMale;

        $cumGenSavCollFemale = $deposits->whereIn('accountIdFk',$generalSavingAccounts->pluck('id'))->whereIn('memberIdFk',$pksfFemaleMemberIds)->sum('amount');
        $cumGenSavWithdrawFemale = $withdraws->whereIn('accountIdFk',$generalSavingAccounts->pluck('id'))->whereIn('memberIdFk',$pksfFemaleMemberIds)->sum('amount');
        $cumGenSavBalFemale = $cumGenSavCollFemale - $cumGenSavWithdrawFemale;

        // Voluntary Savings
        $cumVOlSavCollMale = $deposits->whereIn('accountIdFk',$voluntarySavingAccounts->pluck('id'))->whereIn('memberIdFk',$pksfMaleMemberIds)->sum('amount');
        $cumVOlSavWithdrawMale = $withdraws->whereIn('accountIdFk',$voluntarySavingAccounts->pluck('id'))->whereIn('memberIdFk',$pksfMaleMemberIds)->sum('amount');
        $cumVOlSavBalMale = $cumVOlSavCollMale - $cumVOlSavWithdrawMale;

        $cumVOlSavCollFemale = $deposits->whereIn('accountIdFk',$voluntarySavingAccounts->pluck('id'))->whereIn('memberIdFk',$pksfFemaleMemberIds)->sum('amount');
        $cumVOlSavWithdrawFemale = $withdraws->whereIn('accountIdFk',$voluntarySavingAccounts->pluck('id'))->whereIn('memberIdFk',$pksfFemaleMemberIds)->sum('amount');
        $cumVOlSavBalFemale = $cumVOlSavCollFemale - $cumVOlSavWithdrawFemale;

        // Other Savings
        $cumOthSavCollMale = $deposits->whereIn('accountIdFk',$otherSavingAccounts->pluck('id'))->whereIn('memberIdFk',$pksfMaleMemberIds)->sum('amount');
        $cumOthSavWithdrawMale = $withdraws->whereIn('accountIdFk',$otherSavingAccounts->pluck('id'))->whereIn('memberIdFk',$pksfMaleMemberIds)->sum('amount');
        $cumOthSavBalMale = $cumOthSavCollMale - $cumOthSavWithdrawMale;

        $cumOthSavCollFemale = $deposits->whereIn('accountIdFk',$otherSavingAccounts->pluck('id'))->whereIn('memberIdFk',$pksfFemaleMemberIds)->sum('amount');
        $cumOthSavWithdrawFemale = $withdraws->whereIn('accountIdFk',$otherSavingAccounts->pluck('id'))->whereIn('memberIdFk',$pksfFemaleMemberIds)->sum('amount');
        $cumOthSavBalFemale = $cumOthSavCollFemale - $cumOthSavWithdrawFemale;
        //// End PKSF 

        //// NON-PKSF
        // General Savings
        $cumGenSavCollMaleNonPksf = $deposits->whereIn('accountIdFk',$generalSavingAccounts->pluck('id'))->whereIn('memberIdFk',$nonPksfMaleMemberIds)->sum('amount');
        $cumGenSavWithdrawMaleNonPksf = $withdraws->whereIn('accountIdFk',$generalSavingAccounts->pluck('id'))->whereIn('memberIdFk',$nonPksfMaleMemberIds)->sum('amount');
        $cumGenSavBalMaleNonPksf = $cumGenSavCollMaleNonPksf - $cumGenSavWithdrawMaleNonPksf;

        $cumGenSavCollFemaleNonPksf = $deposits->whereIn('accountIdFk',$generalSavingAccounts->pluck('id'))->whereIn('memberIdFk',$nonPksfFemaleMemberIds)->sum('amount');
        $cumGenSavWithdrawFemaleNonPksf = $withdraws->whereIn('accountIdFk',$generalSavingAccounts->pluck('id'))->whereIn('memberIdFk',$nonPksfFemaleMemberIds)->sum('amount');
        $cumGenSavBalFemaleNonPksf = $cumGenSavCollFemaleNonPksf - $cumGenSavWithdrawFemaleNonPksf;

        // Voluntary Savings
        $cumVOlSavCollMaleNonPksf = $deposits->whereIn('accountIdFk',$voluntarySavingAccounts->pluck('id'))->whereIn('memberIdFk',$nonPksfMaleMemberIds)->sum('amount');
        $cumVOlSavWithdrawMaleNonPksf = $withdraws->whereIn('accountIdFk',$voluntarySavingAccounts->pluck('id'))->whereIn('memberIdFk',$nonPksfMaleMemberIds)->sum('amount');
        $cumVOlSavBalMaleNonPksf = $cumVOlSavCollMaleNonPksf - $cumVOlSavWithdrawMaleNonPksf;

        $cumVOlSavCollFemaleNonPksf = $deposits->whereIn('accountIdFk',$voluntarySavingAccounts->pluck('id'))->whereIn('memberIdFk',$nonPksfFemaleMemberIds)->sum('amount');
        $cumVOlSavWithdrawFemaleNonPksf = $withdraws->whereIn('accountIdFk',$voluntarySavingAccounts->pluck('id'))->whereIn('memberIdFk',$nonPksfFemaleMemberIds)->sum('amount');
        $cumVOlSavBalFemaleNonPksf = $cumVOlSavCollFemaleNonPksf - $cumVOlSavWithdrawFemaleNonPksf;

        // Other Savings
        $cumOthSavCollMaleNonPksf = $deposits->whereIn('accountIdFk',$otherSavingAccounts->pluck('id'))->whereIn('memberIdFk',$nonPksfMaleMemberIds)->sum('amount');
        $cumOthSavWithdrawMaleNonPksf = $withdraws->whereIn('accountIdFk',$otherSavingAccounts->pluck('id'))->whereIn('memberIdFk',$nonPksfMaleMemberIds)->sum('amount');
        $cumOthSavBalMaleNonPksf = $cumOthSavCollMaleNonPksf - $cumOthSavWithdrawMaleNonPksf;

        $cumOthSavCollFemaleNonPksf = $deposits->whereIn('accountIdFk',$otherSavingAccounts->pluck('id'))->whereIn('memberIdFk',$nonPksfFemaleMemberIds)->sum('amount');
        $cumOthSavWithdrawFemaleNonPksf = $withdraws->whereIn('accountIdFk',$otherSavingAccounts->pluck('id'))->whereIn('memberIdFk',$nonPksfFemaleMemberIds)->sum('amount');
        $cumOthSavBalFemaleNonPksf = $cumOthSavCollFemaleNonPksf - $cumOthSavWithdrawFemaleNonPksf;
        //// End NON-PKSF 

        ////// End Table Four & Five -- Note-3

        ////// Table Six & Seven -- Note-4
        $loanProductIds = array();
        foreach ($filBranchIds as $filBranchId) {
            $loanProductIds = array_merge($loanProductIds,explode(',',str_replace(['[',']','"'],'',DB::table('gnr_branch')->where('id',$filBranchId)->value('loanProductId'))));
        }
        $loanProductIds = array_diff($loanProductIds,[""]);
        $loanProductIds = array_unique($loanProductIds);

        /// PKSF
        $pksfLoanProducts = DB::table('mfn_loans_product')
                                ->whereIn('id',$loanProductIds)
                                ->whereIn('id',$pksfProductIds)
                                ->select('id','productCategoryId','name')
                                ->get();
        
        $pksfLoanCategories = DB::table('mfn_loans_product_category')
                                ->whereIn('id',$pksfLoanProducts->pluck('productCategoryId'))
                                ->select('id','name')
                                ->get();

        $pksfLoanDisbursements = DB::table('mfn_loan')
                                    ->where('softDel',0)
                                    ->whereIn('branchIdFk',$filBranchIds)
                                    ->where('disbursementDate','<=',$reportDate)
                                    ->whereIn('productIdFk',$pksfLoanProducts->pluck('id'))
                                    ->select('productIdFk','memberIdFk','loanAmount')
                                    ->get();

        $pksfLoanCollections = DB::table('mfn_loan_collection')
                                    ->where('softDel',0)
                                    ->whereIn('branchIdFk',$filBranchIds)
                                    ->where('collectionDate','<=',$reportDate)
                                    ->whereIn('productIdFk',$pksfLoanProducts->pluck('id'))
                                    ->select('productIdFk','memberIdFk','amount')
                                    ->get();

        /// NON-PKSF
        $nonPksfLoanProducts = DB::table('mfn_loans_product')
                                ->whereIn('id',$loanProductIds)
                                ->whereIn('id',$nonPksfProductIds)
                                ->select('id','productCategoryId')
                                ->get();
        
        $nonPksfLoanCategories = DB::table('mfn_loans_product_category')
                                ->whereIn('id',$nonPksfLoanProducts->pluck('productCategoryId'))
                                ->select('id','name')
                                ->get();

        $nonPksfLoanDisbursements = DB::table('mfn_loan')
                                    ->where('softDel',0)
                                    ->whereIn('branchIdFk',$filBranchIds)
                                    ->where('disbursementDate','<=',$reportDate)
                                    ->whereIn('productIdFk',$nonPksfLoanProducts->pluck('id'))
                                    ->select('productIdFk','memberIdFk','loanAmount')
                                    ->get();

        $nonPksfLoanCollections = DB::table('mfn_loan_collection')
                                    ->where('softDel',0)
                                    ->whereIn('branchIdFk',$filBranchIds)
                                    ->where('collectionDate','<=',$reportDate)
                                    ->whereIn('productIdFk',$nonPksfLoanProducts->pluck('id'))
                                    ->select('productIdFk','memberIdFk','amount')
                                    ->get();

        ////// End Table Six & Seven -- Note-4

        ////// Loan Write Off Section
        ////// End Loan Write Off Section

        $data = array(
            'filBranchIds'                      => $filBranchIds,
            'filReportLevel'                    => $req->filReportLevel,
            'filArea'                           => $req->filArea,
            'filZone'                           => $req->filZone,
            'filRegion'                         => $req->filRegion,
            'filBranch'                         => $req->filBranch,
            'filFund'                           => $req->filFund,
            'filRoundUup'                       => $req->filRoundUup,
            'filDate'                           => $req->filDate,
            'payments'                          => $payments,
            'paymentSchedules'                  => $paymentSchedules,
            'nextPaymentSchedules'              => $nextPaymentSchedules,
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
            'cumGenSavWithdrawMale'             => $cumGenSavWithdrawMale,
            'cumGenSavBalMale'                  => $cumGenSavBalMale,

            'cumGenSavCollFemale'               => $cumGenSavCollFemale,
            'cumGenSavWithdrawFemale'           => $cumGenSavWithdrawFemale,
            'cumGenSavBalFemale'                => $cumGenSavBalFemale,

            'cumVOlSavCollMale'                 => $cumVOlSavCollMale,
            'cumVOlSavWithdrawMale'             => $cumVOlSavWithdrawMale,
            'cumVOlSavBalMale'                  => $cumVOlSavBalMale,

            'cumVOlSavCollFemale'               => $cumVOlSavCollFemale,
            'cumVOlSavWithdrawFemale'           => $cumVOlSavWithdrawFemale,
            'cumVOlSavBalFemale'                => $cumVOlSavBalFemale,

            'cumOthSavCollMale'                 => $cumOthSavCollMale,
            'cumOthSavWithdrawMale'             => $cumOthSavWithdrawMale,
            'cumOthSavBalMale'                  => $cumOthSavBalMale,

            'cumOthSavCollFemale'               => $cumOthSavCollFemale,
            'cumOthSavWithdrawFemale'           => $cumOthSavWithdrawFemale,
            'cumOthSavBalFemale'                => $cumOthSavBalFemale,

            'pksfLoanProducts'                  => $pksfLoanProducts,
            'pksfLoanCategories'                => $pksfLoanCategories,
            'pksfMaleMemberIds'                 => $pksfMaleMemberIds,
            'pksfFemaleMemberIds'               => $pksfFemaleMemberIds,
            'pksfLoanDisbursements'             => $pksfLoanDisbursements,
            'pksfLoanCollections'               => $pksfLoanCollections,
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
            'cumGenSavWithdrawMaleNonPksf'      => $cumGenSavWithdrawMaleNonPksf,
            'cumGenSavBalMaleNonPksf'           => $cumGenSavBalMaleNonPksf,
            'cumGenSavCollFemaleNonPksf'        => $cumGenSavCollFemaleNonPksf,
            'cumGenSavWithdrawFemaleNonPksf'    => $cumGenSavWithdrawFemaleNonPksf,
            'cumGenSavBalFemaleNonPksf'         => $cumGenSavBalFemaleNonPksf,
            'cumVOlSavCollMaleNonPksf'          => $cumVOlSavCollMaleNonPksf,
            'cumVOlSavWithdrawMaleNonPksf'      => $cumVOlSavWithdrawMaleNonPksf,
            'cumVOlSavBalMaleNonPksf'           => $cumVOlSavBalMaleNonPksf,
            'cumVOlSavCollFemaleNonPksf'        => $cumVOlSavCollFemaleNonPksf,
            'cumVOlSavWithdrawFemaleNonPksf'    => $cumVOlSavWithdrawFemaleNonPksf,
            'cumVOlSavBalFemaleNonPksf'         => $cumVOlSavBalFemaleNonPksf,
            'cumOthSavCollMaleNonPksf'          => $cumOthSavCollMaleNonPksf,
            'cumOthSavWithdrawMaleNonPksf'      => $cumOthSavWithdrawMaleNonPksf,
            'cumOthSavBalMaleNonPksf'           => $cumOthSavBalMaleNonPksf,
            'cumOthSavCollFemaleNonPksf'        => $cumOthSavCollFemaleNonPksf,
            'cumOthSavWithdrawFemaleNonPksf'    => $cumOthSavWithdrawFemaleNonPksf,
            'cumOthSavBalFemaleNonPksf'         => $cumOthSavBalFemaleNonPksf,
            'nonPksfLoanProducts'               => $nonPksfLoanProducts,
            'nonPksfLoanCategories'             => $nonPksfLoanCategories,
            'nonPksfMaleMemberIds'              => $nonPksfMaleMemberIds,
            'nonPksfFemaleMemberIds'            => $nonPksfFemaleMemberIds,
            'nonPksfLoanDisbursements'          => $nonPksfLoanDisbursements,
            'nonPksfLoanCollections'            => $nonPksfLoanCollections,
        );

        if ($req->filFund=='' || $req->filFund==null) {
            return view('microfin.reports.pksfPomisReport.pksfPomis5Report.pksfPomisFiveReport',$data);        
        }
        elseif ($req->filFund==1) {
            return view('microfin.reports.pksfPomisReport.pksfPomis5Report.pksfPomisFiveReportPKSF',$data);
        }
        else{
            return view('microfin.reports.pksfPomisReport.pksfPomis5Report.pksfPomisFiveReportNON_PKSF',$data);
        }
    }

    public function getFilteredBranhIds($filReportLevel,$filBranch,$filArea,$filZone,$filRegion){
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
        
        return $filBranchIds;
    }

    public function calculateCumulativeForFirtTable($isPksf,$branchIds,$openingBalanceDate,$ledgerHeads,$fiscalYear,$reportDate){

        $openingBalance = DB::table('acc_opening_balance')
                                ->where('projectId',1)
                                ->whereIn('branchId',$branchIds)
                                ->where('openingDate',$openingBalanceDate)
                                ->whereIn('ledgerId',$ledgerHeads);

        $voucherIds = DB::table('acc_voucher')
                            ->where('projectId',1)
                            ->whereIn('branchId',$branchIds)
                            ->where('voucherDate','>=',$fiscalYear->fyStartDate)
                            ->where('voucherDate','<=',$reportDate)
                            ->where('status',1);

        if ($isPksf==1) {
            $openingBalance = $openingBalance->where('projectTypeId',2);
            $voucherIds = $voucherIds->where('projectTypeId',2);
        }
        else{
            $openingBalance = $openingBalance->where('projectTypeId',3);
            $voucherIds = $voucherIds->where('projectTypeId',3);
        }

        $openingBalance = $openingBalance->sum('balanceAmount');
        $voucherIds = $voucherIds->pluck('id')->toArray();

        $cumServiceChargeDebit = DB::table('acc_voucher_details')
                                    ->whereIn('voucherId',$voucherIds)
                                    ->whereIn('creditAcc',$ledgerHeads)
                                    ->where('status',1)
                                    ->sum('amount');

        $cumServiceChargeCredit = DB::table('acc_voucher_details')
                                    ->whereIn('voucherId',$voucherIds)
                                    ->whereIn('debitAcc',$ledgerHeads)
                                    ->where('status',1)
                                    ->sum('amount');

        $balance = $openingBalance + $cumServiceChargeDebit - $cumServiceChargeCredit;

        return $balance;
    }

}
