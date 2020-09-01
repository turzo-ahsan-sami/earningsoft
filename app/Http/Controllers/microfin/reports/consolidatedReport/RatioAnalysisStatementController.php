<?php

namespace App\Http\Controllers\microfin\reports\consolidatedReport;

use App\Http\Controllers\Controller;
use DB;
use App\Http\Requests;
use Response;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use App\Http\Controllers\microfin\MicroFin;

class RatioAnalysisStatementController extends Controller
{
    public $firstMonth;
    public $sencodMonth;
    public $thirdMonth;
    public $firstFY;
    public $sencodFY;
    public $thirdFY;

    function __construct(){
        // repeated data will be save to these arrays
        $this->firstMonth = array();
        $this->sencodMonth = array();
        $this->thirdMonth = array();
        $this->firstFY = array();
        $this->sencodFY = array();
        $this->thirdFY = array();        
    }

    
    public function index(){

    	$userBranchId = Auth::user()->branchId;
        
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
        /// Year
        $yearsOption = MicroFin::getYearsOption();

        /// Month
        $monthsOption = MicroFin::getMonthsOption();

        /// Funding Organization List
        $fundingOrgList = DB::table('mfn_funding_organization')
                                ->pluck('name','id')
                                ->toArray();

        $data = array(
            'reportLevelList'     => $reportLevelList,
            'areaList'            => $areaList,
            'zoneList'            => $zoneList,
            'regionList'          => $regionList,
            'branchList'          => $branchList,
            'yearsOption'         => $yearsOption,
            'monthsOption'        => $monthsOption,
            'fundingOrgList'      => $fundingOrgList,
            'userBranchId'        => $userBranchId
        );

        return view('microfin.reports.ratioAnalysisStatement.reportFilteringPart',$data);
    }

    public function getReport(Request $req){

        $start = microtime(true);

    	$filBranchIds = $this->getFilteredBranhIds($req->filReportLevel,$req->filBranch,$req->filArea,$req->filZone,$req->filRegion);

    	$userCompanyId = Auth::user()->company_id_fk;
    	if (Auth::user()->id==1) {
    		$userCompanyId = 1;
    	}

    	$month = str_pad($req->filMonth, 2, '0',STR_PAD_LEFT);
    	$filDate = Carbon::parse('01-'.$month.'-'.$req->filYear)->endOfMonth()->format('Y-m-d');

    	$fiscalYears = DB::table('gnr_fiscal_year')->where('companyId',$userCompanyId)->get();
    	$lastFiscalYearDate = '';
    	$yearEndPendingBranchIds = [];
    	if (count($filBranchIds)>1) {
    		$pendindNFiscalYearInfo = $this->getYearEndPendingsBranchIds($filBranchIds,$filDate,$fiscalYears);
    		$yearEndPendingBranchIds = $pendindNFiscalYearInfo['pendingBranchIds'];
    		$lastFiscalYearDate = $pendindNFiscalYearInfo['lastFiscalYear'];
    	}
    	else{
    		$lastFiscalYearDate = $this->getLastCompleteFiscalYearForABranch($filBranchIds[0],$filDate,$fiscalYears);
    	}

    	$branchIds = array_diff($filBranchIds, $yearEndPendingBranchIds);

    	Carbon::useMonthsOverflow(false);

    	$ratioAnalysisCategories = DB::table('mfn_ratio_analysis_category')->get();
        $ratioAnalysisComponents = DB::table('mfn_ratio_analysis_contents')->get();

        $reportingDate = Carbon::parse($filDate);
        $previous1stMonth = $reportingDate->copy()->subMonth()->endOfMonth();
        $previous2ndMonth = $previous1stMonth->copy()->subMonth()->endOfMonth();

        $currentFiscalYear = $fiscalYears->where('fyStartDate','<=',$lastFiscalYearDate)->where('fyEndDate','>=',$lastFiscalYearDate)->first();

        $previous1stYear = Carbon::parse($lastFiscalYearDate)->subYear();

        $previous1stFiscalYear = $fiscalYears->where('fyStartDate','<=',$previous1stYear->format('Y-m-d'))->where('fyEndDate','>=',$previous1stYear->format('Y-m-d'))->first();

        $previous2ndYear = $previous1stYear->copy()->subYear();

        $previous2ndFiscalYear = $fiscalYears->where('fyStartDate','<=',$previous2ndYear->format('Y-m-d'))->where('fyEndDate','>=',$previous2ndYear->format('Y-m-d'))->first();

        ///// for the problem normalization into the view file, here all data is organized according to the serial number of the components into the view file.
        // initialize the components first
        for($i=0; $i<count($ratioAnalysisComponents); $i++){
        	$currMonth[$i] = 0;
        	$pre1stMonth[$i] = 0;
        	$pre2ndMonth[$i] = 0;
        	$currFY[$i] = 0;
        	$pre1stFY[$i] = 0;
        	$pre2ndFY[$i] = 0;
        }

        // get the max date among the periods, according to the max date get the month_end summery info
        $maxDate = max([$reportingDate->format('Y-m-d'),$previous1stMonth->format('Y-m-d'),$previous2ndMonth->format('Y-m-d'),$lastFiscalYearDate,$previous1stYear->format('Y-m-d'),$previous2ndYear->format('Y-m-d')]);
        $minDate = min([$reportingDate->format('Y-m-d'),$previous1stMonth->format('Y-m-d'),$previous2ndMonth->format('Y-m-d'),$lastFiscalYearDate,$previous1stYear->format('Y-m-d'),$previous2ndYear->format('Y-m-d')]);

        $monthEndLoanInfo = DB::table('mfn_month_end_process_loans')
    								->whereIn('branchIdFk',$branchIds)
    								->where('date','<=',$maxDate);

    	$monthEndMemberInfo = DB::table('mfn_month_end_process_total_members')
    									->whereIn('branchIdFk',$branchIds)
    									->where('date','<=',$maxDate);

    	if ($req->filFundingOrg!='' || $req->filFundingOrg!=null) {
    		$loanProductIds = MicroFin::getFundungOrgWiseLoanProductIds($req->filFundingOrg);
    		$monthEndLoanInfo = $monthEndLoanInfo->whereIn('productIdFk',$loanProductIds);
    		$monthEndMemberInfo = $monthEndMemberInfo->where('fundingOrgIdFk',$req->filFundingOrg);
    	}

        if ($req->filServiceCharge==1) {
            $monthEndLoanInfo = $monthEndLoanInfo->select('date','productIdFk','borrowerNo','closingBorrowerNo','recoverableAmount','regularAmount','closingOutstandingAmountWithServicesCharge AS closingOutstandingAmount','watchfulOutstandingWithServicesCharge AS watchfulOutstanding','substandardOutstandingWithServicesCharge AS substandardOutstanding','doubtfullOutstandingWithServicesCharge AS doubtfullOutstanding','badOutstandingWithServicesCharge as badOutstanding','watchfulOverdueWithServicesCharge AS watchfulOverdue','substandardOverdueWithServicesCharge AS substandardOverdue','doubtfullOverdueWithServicesCharge AS doubtfullOverdue','badOverdueWithServicesCharge AS badOverdue','disbursedAmount','recoveryAmount')->get();
        }
        else{
            $monthEndLoanInfo = $monthEndLoanInfo->select('date','productIdFk','borrowerNo','closingBorrowerNo','principalRecoverableAmount AS recoverableAmount','principalRegularAmount AS regularAmount','closingOutstandingAmount','watchfulOutstanding','substandardOutstanding','doubtfullOutstanding','badOutstanding','watchfulOverdue','substandardOverdue','doubtfullOverdue','badOverdue','disbursedAmount','principalRecoveryAmount AS recoveryAmount')->get();
        }

    	$monthEndMemberInfo = $monthEndMemberInfo->get();        

        $vouchers = DB::table('acc_voucher')
                            ->where('status',1)
                            ->whereIn('branchId',$branchIds)
                            ->where('voucherDate','<=',$maxDate);

        $accOpeningBalances = DB::table('acc_opening_balance')
                                    ->whereIn('branchId',$branchIds)
                                    ->where('openingDate','<=',$maxDate);

        if ($req->filFundingOrg!='' || $req->filFundingOrg!=null) {
            $funOrg = DB::table('mfn_funding_organization')->where('id',$req->filFundingOrg)->first();
            $vouchers = $vouchers->where('projectId',$funOrg->projectIdFk)->where('projectTypeId',$funOrg->projectTypeIdFk);
            $accOpeningBalances = $accOpeningBalances->where('projectId',$funOrg->projectIdFk)->where('projectTypeId',$funOrg->projectTypeIdFk);
        }

        $vouchers = $vouchers->select('id','voucherDate')->get();
        $accOpeningBalances = $accOpeningBalances->get();

        $voucherDetails = DB::table('acc_voucher_details')
                                ->whereIn('voucherId',$vouchers->pluck('id'))
                                ->select('voucherId','debitAcc','creditAcc','amount')
                                ->get();

        $paymentSchedules = null;

        if (in_array(1,$branchIds)) {
            $loanRegisterAccounts = DB::table('acc_loan_register_account')
                                        ->where('agreementDate','<=',$maxDate);

            if ($req->filFundingOrg!='' || $req->filFundingOrg!=null) {
                $funOrg = DB::table('mfn_funding_organization')->where('id',$req->filFundingOrg)->first();
                $loanRegisterAccounts = $loanRegisterAccounts->where('projectId_fk',$funOrg->projectIdFk)->where('projectTypeId_fk',$funOrg->projectTypeIdFk);
            }
            $loanRegisterAccounts = $loanRegisterAccounts->get();

            $paymentSchedules = DB::table('acc_loan_register_payment_schedule')
                                    ->whereIn('accId_fk',$loanRegisterAccounts->pluck('id'))
                                    ->where('paymentDate','>',$minDate)
                                    ->select('paymentDate','principalAmount','interestAmount','totalAmount')
                                    ->get();
        }

        //// get data for current month
        // 0 = onTimeRealization
        $currMonth[0] = $this->onTimeRealization($this->firstMonth,$monthEndLoanInfo,$filDate,$filDate);
        // 1 = Cumulative Recovery
        $currMonth[1] = $this->cumulativeRecovery($this->firstMonth,$monthEndLoanInfo,$filDate);
        // 2 = 	Portfolio At Risk (PAR)
        $currMonth[2] = $this->portfolioAtRisk($this->firstMonth,$monthEndLoanInfo,$filDate);
        // 3 = 	Delinquency Ratio
        $currMonth[3] = $this->delinquencyRatio($this->firstMonth,$monthEndLoanInfo,$filDate);
        // 4 = 	Average Loan Size Ratio (Urban & Rural-MFP)
        $currMonth[4] = $this->averageLoanSizeRatio($this->firstMonth,$monthEndLoanInfo,$filDate);
        // 5 = 	Member Per Branch
        $currMonth[5] = $this->memberPerBranch($this->firstMonth,$monthEndMemberInfo,$filDate);
        // 6 = 	Member Per CO
        $currMonth[6] = $this->memberPerCO($this->firstMonth,$monthEndMemberInfo,$filDate);
        // 7 = 	Loanee Per CO
        $currMonth[7] = $this->loaneePerCO($this->firstMonth,$monthEndMemberInfo,$monthEndLoanInfo,$filDate);
        // 8 = 	Portfolio Per CO
        $currMonth[8] = $this->portfolioPerCO($this->firstMonth,$monthEndLoanInfo,$filDate);
        // 9 =  Portfolio Per Borrower
        $currMonth[9] = $this->portfolioPerBorrower($this->firstMonth,$monthEndLoanInfo,$filDate);
        // 10 =  Borrower Coverage % (Borrower & Member)
        $currMonth[10] = $this->borrowerCoverage($this->firstMonth,$monthEndMemberInfo,$monthEndLoanInfo,$filDate);
        // 11 = Operating Cost Ratio
        $fyFirstDate = $reportingDate->copy()->subYear()->format('Y-m-d');
        $currMonth[11] = $this->operatingCostRatio($this->firstMonth,$monthEndLoanInfo,$vouchers,$voucherDetails,$fyFirstDate,$filDate);
        // 12 = Operational Self-Sufficiency(OSS)
        $currMonth[12] = $this->operationalSelfSufficiency($this->firstMonth,$vouchers,$voucherDetails,$fyFirstDate,$filDate);
        // 13 = Financial Self-Sufficiency (FSS)
        $currMonth[13] = $this->financialSelfSufficiency($this->firstMonth,$vouchers,$voucherDetails,$fyFirstDate,$filDate);
        // 14 = Current Ratio
        $currMonth[14] = $this->currentRatio($this->firstMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$filDate);
        // 15 = Debt To Capital Ratio
        $currMonth[15] = $this->debtToCapitalRatio($this->firstMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$filDate);
        // 16 = Debt To Asset Ratio
        $currMonth[16] = $this->debtToAssetRatio($this->firstMonth);
        // 17 = Liquidity To Savings Ratio
        $currMonth[17] = $this->liquidityToSavingsRatio($this->firstMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$filDate);
        // 18 = Capital Adequacy Ratio
        $currMonth[18] = $this->capitalAdequacyRatio($this->firstMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$filDate);
        // 19 = Debt Service Cover Ratio
        $currMonth[19] = $this->debtServiceCoverRatio($this->firstMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$filDate,$paymentSchedules);
        // 20 = Rate Of Return On Capital
        $currMonth[20] = $this->rateOfReturnOnCapital($this->firstMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$filDate);
        // 21 = Return On Total Asset (ROTA)
        $currMonth[21] = $this->returnOnTotalAsset($this->firstMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$filDate);
        // 22 = Yeild On Portfolio
        $currMonth[22] = $this->yeildOnPortfolio($this->firstMonth);
        // 23 = Yeild On Portfolio
        $currMonth[23] = $this->costPerUnitOfLoanRecovery($this->firstMonth,$monthEndLoanInfo,$fyFirstDate,$filDate);
        // 24 = Bad OD As A % Loan Outstanding
        $currMonth[24] = $this->badODLoanOutstanding($monthEndLoanInfo,$filDate);



        //// get data for previous1st month
        // 0 = onTimeRealization
        $pre1stMonth[0] = $this->onTimeRealization($this->sencodMonth,$monthEndLoanInfo,$previous1stMonth->format('Y-m-d'),$previous1stMonth->format('Y-m-d'));
        // 1 = Cumulative Recovery
        $pre1stMonth[1] = $this->cumulativeRecovery($this->sencodMonth,$monthEndLoanInfo,$previous1stMonth->format('Y-m-d'));
        // 2 = 	Portfolio At Risk (PAR)
        $pre1stMonth[2] = $this->portfolioAtRisk($this->sencodMonth,$monthEndLoanInfo,$previous1stMonth->format('Y-m-d'));
        // 3 = 	Delinquency Ratio
        $pre1stMonth[3] = $this->delinquencyRatio($this->sencodMonth,$monthEndLoanInfo,$previous1stMonth->format('Y-m-d'));
        // 4 = 	Average Loan Size Ratio (Urban & Rural-MFP)
        $pre1stMonth[4] = $this->averageLoanSizeRatio($this->sencodMonth,$monthEndLoanInfo,$previous1stMonth->format('Y-m-d'));
        // 5 = 	Member Per Branch
        $pre1stMonth[5] = $this->memberPerBranch($this->sencodMonth,$monthEndMemberInfo,$previous1stMonth->format('Y-m-d'));
        // 6 = 	Member Per CO
        $pre1stMonth[6] = $this->memberPerCO($this->sencodMonth,$monthEndMemberInfo,$previous1stMonth->format('Y-m-d'));
        // 7 = 	Loanee Per CO
        $pre1stMonth[7] = $this->loaneePerCO($this->sencodMonth,$monthEndMemberInfo,$monthEndLoanInfo,$previous1stMonth->format('Y-m-d'));
        // 8 = 	Portfolio Per CO
        $pre1stMonth[8] = $this->portfolioPerCO($this->sencodMonth,$monthEndLoanInfo,$previous1stMonth->format('Y-m-d'));
        // 9 =  Portfolio Per Borrower
        $pre1stMonth[9] = $this->portfolioPerBorrower($this->sencodMonth,$monthEndLoanInfo,$previous1stMonth->format('Y-m-d'));
        // 10 =  Borrower Coverage % (Borrower & Member)
        $pre1stMonth[10] = $this->borrowerCoverage($this->sencodMonth,$monthEndMemberInfo,$monthEndLoanInfo,$previous1stMonth->format('Y-m-d'));
        // 11 = Operating Cost Ratio
        $fyFirstDate = $reportingDate->copy()->subYear()->subMonth()->endOfMonth()->format('Y-m-d');
        $pre1stMonth[11] = $this->operatingCostRatio($this->sencodMonth,$monthEndLoanInfo,$vouchers,$voucherDetails,$fyFirstDate,$previous1stMonth->format('Y-m-d'));
        // 12 = Operational Self-Sufficiency(OSS)
        $pre1stMonth[12] = $this->operationalSelfSufficiency($this->sencodMonth,$vouchers,$voucherDetails,$fyFirstDate,$previous1stMonth->format('Y-m-d'));
        // 13 = Financial Self-Sufficiency (FSS)
        $pre1stMonth[13] = $this->financialSelfSufficiency($this->sencodMonth,$vouchers,$voucherDetails,$fyFirstDate,$previous1stMonth->format('Y-m-d'));
        // 14 = Current Ratio
        $pre1stMonth[14] = $this->currentRatio($this->sencodMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$previous1stMonth->format('Y-m-d'));
        // 15 = Debt To Capital Ratio
        $pre1stMonth[15] = $this->debtToCapitalRatio($this->sencodMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$previous1stMonth->format('Y-m-d'));
        // 16 = Debt To Asset Ratio
        $pre1stMonth[16] = $this->debtToAssetRatio($this->sencodMonth);
        // 17 = Liquidity To Savings Ratio
        $pre1stMonth[17] = $this->liquidityToSavingsRatio($this->sencodMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$previous1stMonth->format('Y-m-d'));
        // 18 = Capital Adequacy Ratio
        $pre1stMonth[18] = $this->capitalAdequacyRatio($this->sencodMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$previous1stMonth->format('Y-m-d'));
        // 19 = Debt Service Cover Ratio
        $pre1stMonth[19] = $this->debtServiceCoverRatio($this->sencodMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$previous1stMonth->format('Y-m-d'),$paymentSchedules);
        // 20 = Rate Of Return On Capital
        $pre1stMonth[20] = $this->rateOfReturnOnCapital($this->sencodMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$previous1stMonth->format('Y-m-d'));
        // 21 = Return On Total Asset (ROTA)
        $pre1stMonth[21] = $this->returnOnTotalAsset($this->sencodMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$previous1stMonth->format('Y-m-d'));
        // 22 = Yeild On Portfolio
        $pre1stMonth[22] = $this->yeildOnPortfolio($this->sencodMonth);
        // 23 = Yeild On Portfolio
        $pre1stMonth[23] = $this->costPerUnitOfLoanRecovery($this->sencodMonth,$monthEndLoanInfo,$fyFirstDate,$previous1stMonth->format('Y-m-d'));
        // 24 = Bad OD As A % Loan Outstanding
        $pre1stMonth[24] = $this->badODLoanOutstanding($monthEndLoanInfo,$previous1stMonth->format('Y-m-d'));


        //// get data for previous2nd month
        // 0 = onTimeRealization
        $pre2ndMonth[0] = $this->onTimeRealization($this->thirdMonth,$monthEndLoanInfo,$previous2ndMonth->format('Y-m-d'),$previous2ndMonth->format('Y-m-d'));
        // 1 = Cumulative Recovery
        $pre2ndMonth[1] = $this->cumulativeRecovery($this->thirdMonth,$monthEndLoanInfo,$previous2ndMonth->format('Y-m-d'));
        // 2 = 	Portfolio At Risk (PAR)
        $pre2ndMonth[2] = $this->portfolioAtRisk($this->thirdMonth,$monthEndLoanInfo,$previous2ndMonth->format('Y-m-d'));
        // 3 = 	Delinquency Ratio
        $pre2ndMonth[3] = $this->delinquencyRatio($this->thirdMonth,$monthEndLoanInfo,$previous2ndMonth->format('Y-m-d'));
        // 4 = 	Average Loan Size Ratio (Urban & Rural-MFP)
        $pre2ndMonth[4] = $this->averageLoanSizeRatio($this->thirdMonth,$monthEndLoanInfo,$previous2ndMonth->format('Y-m-d'));
        // 5 = 	Member Per Branch
        $pre2ndMonth[5] = $this->memberPerBranch($this->thirdMonth,$monthEndMemberInfo,$previous2ndMonth->format('Y-m-d'));
        // 6 = 	Member Per CO
        $pre2ndMonth[6] = $this->memberPerCO($this->thirdMonth,$monthEndMemberInfo,$previous2ndMonth->format('Y-m-d'));
        // 7 = 	Loanee Per CO
        $pre2ndMonth[7] = $this->loaneePerCO($this->thirdMonth,$monthEndMemberInfo,$monthEndLoanInfo,$previous2ndMonth->format('Y-m-d'));
        // 8 = 	Portfolio Per CO
        $pre2ndMonth[8] = $this->portfolioPerCO($this->thirdMonth,$monthEndLoanInfo,$previous2ndMonth->format('Y-m-d'));
        // 9 =  Portfolio Per Borrower
        $pre2ndMonth[9] = $this->portfolioPerBorrower($this->thirdMonth,$monthEndLoanInfo,$previous2ndMonth->format('Y-m-d'));
        // 10 =  Borrower Coverage % (Borrower & Member)
        $pre2ndMonth[10] = $this->borrowerCoverage($this->thirdMonth,$monthEndMemberInfo,$monthEndLoanInfo,$previous2ndMonth->format('Y-m-d'));
        // 11 = Operating Cost Ratio
        $fyFirstDate = $reportingDate->copy()->subYear()->subMonths(2)->endOfMonth()->format('Y-m-d');
        $pre2ndMonth[11] = $this->operatingCostRatio($this->thirdMonth,$monthEndLoanInfo,$vouchers,$voucherDetails,$fyFirstDate,$previous2ndMonth->format('Y-m-d'));
        // 12 = Operational Self-Sufficiency(OSS)
        $pre2ndMonth[12] = $this->operationalSelfSufficiency($this->thirdMonth,$vouchers,$voucherDetails,$fyFirstDate,$previous2ndMonth->format('Y-m-d'));
        // 13 = Financial Self-Sufficiency (FSS)
        $pre2ndMonth[13] = $this->financialSelfSufficiency($this->thirdMonth,$vouchers,$voucherDetails,$fyFirstDate,$previous2ndMonth->format('Y-m-d'));
        // 14 = Current Ratio
        $pre2ndMonth[14] = $this->currentRatio($this->thirdMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$previous2ndMonth->format('Y-m-d'));
        // 15 = Debt To Capital Ratio
        $pre2ndMonth[15] = $this->debtToCapitalRatio($this->thirdMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$previous2ndMonth->format('Y-m-d'));
        // 16 = Debt To Asset Ratio
        $pre2ndMonth[16] = $this->debtToAssetRatio($this->thirdMonth);
        // 17 = Liquidity To Savings Ratio
        $pre2ndMonth[17] = $this->liquidityToSavingsRatio($this->thirdMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$previous2ndMonth->format('Y-m-d'));
        // 18 = Capital Adequacy Ratio
        $pre2ndMonth[18] = $this->capitalAdequacyRatio($this->thirdMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$previous2ndMonth->format('Y-m-d'));
        // 19 = Debt Service Cover Ratio
        $pre2ndMonth[19] = $this->debtServiceCoverRatio($this->thirdMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$previous2ndMonth->format('Y-m-d'),$paymentSchedules);
        // 20 = Rate Of Return On Capital
        $pre2ndMonth[20] = $this->rateOfReturnOnCapital($this->thirdMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$previous2ndMonth->format('Y-m-d'));
        // 21 = Return On Total Asset (ROTA)
        $pre2ndMonth[21] = $this->returnOnTotalAsset($this->thirdMonth,0,$accOpeningBalances,$vouchers,$voucherDetails,$previous2ndMonth->format('Y-m-d'));
        // 22 = Yeild On Portfolio
        $pre2ndMonth[22] = $this->yeildOnPortfolio($this->thirdMonth);
        // 23 = Yeild On Portfolio
        $pre2ndMonth[23] = $this->costPerUnitOfLoanRecovery($this->thirdMonth,$monthEndLoanInfo,$fyFirstDate,$previous2ndMonth->format('Y-m-d'));
        // 24 = Bad OD As A % Loan Outstanding
        $pre2ndMonth[24] = $this->badODLoanOutstanding($monthEndLoanInfo,$previous2ndMonth->format('Y-m-d'));


        //// get data for current fiscal year
        if (count($currentFiscalYear)>0) {
        	// 0 = onTimeRealization
        	$currFY[0] = $this->onTimeRealization($this->firstFY,$monthEndLoanInfo,$currentFiscalYear->fyStartDate,$currentFiscalYear->fyEndDate);
        	// 1 = Cumulative Recovery
        	$currFY[1] = $this->cumulativeRecovery($this->firstFY,$monthEndLoanInfo,$currentFiscalYear->fyEndDate);
        	// 2 = 	Portfolio At Risk (PAR)
        	$currFY[2] = $this->portfolioAtRisk($this->firstFY,$monthEndLoanInfo,$currentFiscalYear->fyEndDate);
        	// 3 = 	Delinquency Ratio
        	$currFY[3] = $this->delinquencyRatio($this->firstFY,$monthEndLoanInfo,$currentFiscalYear->fyEndDate);
        	// 4 = 	Average Loan Size Ratio (Urban & Rural-MFP)
        	$currFY[4] = $this->averageLoanSizeRatio($this->firstFY,$monthEndLoanInfo,$currentFiscalYear->fyEndDate);
        	// 5 = 	Member Per Branch
        	$currFY[5] = $this->memberPerBranch($this->firstFY,$monthEndMemberInfo,$currentFiscalYear->fyEndDate);
        	// 6 = 	Member Per CO
        	$currFY[6] = $this->memberPerCO($this->firstFY,$monthEndMemberInfo,$currentFiscalYear->fyEndDate);
        	// 7 = 	Loanee Per CO
        	$currFY[7] = $this->loaneePerCO($this->firstFY,$monthEndMemberInfo,$monthEndLoanInfo,$currentFiscalYear->fyEndDate);
        	// 8 = 	Portfolio Per CO
            $currFY[8] = $this->portfolioPerCO($this->firstFY,$monthEndLoanInfo,$currentFiscalYear->fyEndDate);
            // 9 =  Portfolio Per Borrower
            $currFY[9] = $this->portfolioPerBorrower($this->firstFY,$monthEndLoanInfo,$currentFiscalYear->fyEndDate);
            // 10 =  Borrower Coverage % (Borrower & Member)
        	$currFY[10] = $this->borrowerCoverage($this->firstFY,$monthEndMemberInfo,$monthEndLoanInfo,$currentFiscalYear->fyEndDate);
            // 11 = Operating Cost Ratio
            $currFY[11] = $this->operatingCostRatio($this->firstFY,$monthEndLoanInfo,$vouchers,$voucherDetails,$currentFiscalYear->fyStartDate,$currentFiscalYear->fyEndDate);
            // 12 = Operational Self-Sufficiency(OSS)
            $currFY[12] = $this->operationalSelfSufficiency($this->firstFY,$vouchers,$voucherDetails,$currentFiscalYear->fyStartDate,$currentFiscalYear->fyEndDate);
            // 13 = Financial Self-Sufficiency (FSS)
            $currFY[13] = $this->financialSelfSufficiency($this->firstFY,$vouchers,$voucherDetails,$currentFiscalYear->fyStartDate,$currentFiscalYear->fyEndDate);
            // 14 = Current Ratio
            $currFY[14] = $this->currentRatio($this->firstFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$currentFiscalYear->fyEndDate);
            // 15 = Debt To Capital Ratio
            $currFY[15] = $this->debtToCapitalRatio($this->firstFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$currentFiscalYear->fyEndDate);
            // 16 = Debt To Asset Ratio
            $currFY[16] = $this->debtToAssetRatio($this->firstFY);
            // 17 = Liquidity To Savings Ratio
            $currFY[17] = $this->liquidityToSavingsRatio($this->firstFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$currentFiscalYear->fyEndDate);
            // 18 = Capital Adequacy Ratio
            $currFY[18] = $this->capitalAdequacyRatio($this->firstFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$currentFiscalYear->fyEndDate);
            // 19 = Debt Service Cover Ratio
            $currFY[19] = $this->debtServiceCoverRatio($this->firstFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$currentFiscalYear->fyEndDate,$paymentSchedules);
            // 20 = Rate Of Return On Capital
            $currFY[20] = $this->rateOfReturnOnCapital($this->firstFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$currentFiscalYear->fyEndDate);
            // 21 = Return On Total Asset (ROTA)
            $currFY[21] = $this->returnOnTotalAsset($this->firstFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$currentFiscalYear->fyEndDate);
            // 22 = Yeild On Portfolio
            $currFY[22] = $this->yeildOnPortfolio($this->firstFY);
            // 23 = Yeild On Portfolio
            $currFY[23] = $this->costPerUnitOfLoanRecovery($this->firstFY,$monthEndLoanInfo,$currentFiscalYear->fyStartDate,$currentFiscalYear->fyEndDate);
            // 24 = Bad OD As A % Loan Outstanding
            $currFY[24] = $this->badODLoanOutstanding($monthEndLoanInfo,$currentFiscalYear->fyEndDate);
        }



        //// get data for previous first fiscal year
        if (count($previous1stFiscalYear)>0) {
        	// 0 = onTimeRealization
        	$pre1stFY[0] = $this->onTimeRealization($this->sencodFY,$monthEndLoanInfo,$previous1stFiscalYear->fyStartDate,$previous1stFiscalYear->fyEndDate);
        	// 1 = Cumulative Recovery
        	$pre1stFY[1] = $this->cumulativeRecovery($this->sencodFY,$monthEndLoanInfo,$previous1stFiscalYear->fyEndDate);
        	// 2 = 	Portfolio At Risk (PAR)
        	$pre1stFY[2] = $this->portfolioAtRisk($this->sencodFY,$monthEndLoanInfo,$previous1stFiscalYear->fyEndDate);
        	// 3 = 	Delinquency Ratio
        	$pre1stFY[3] = $this->delinquencyRatio($this->sencodFY,$monthEndLoanInfo,$previous1stFiscalYear->fyEndDate);
        	// 4 = 	Average Loan Size Ratio (Urban & Rural-MFP)
        	$pre1stFY[4] = $this->averageLoanSizeRatio($this->sencodFY,$monthEndLoanInfo,$previous1stFiscalYear->fyEndDate);
        	// 5 = 	Member Per Branch
        	$pre1stFY[5] = $this->memberPerBranch($this->sencodFY,$monthEndMemberInfo,$previous1stFiscalYear->fyEndDate);
        	$pre1stFY[5] = $this->memberPerCO($this->sencodFY,$monthEndMemberInfo,$previous1stFiscalYear->fyEndDate);
        	// 6 = 	Member Per CO
        	$pre1stFY[6] = $this->memberPerCO($this->sencodFY,$monthEndMemberInfo,$previous1stFiscalYear->fyEndDate);
        	// 7 = 	Loanee Per CO
        	$pre1stFY[7] = $this->loaneePerCO($this->sencodFY,$monthEndMemberInfo,$monthEndLoanInfo,$previous1stFiscalYear->fyEndDate);
        	// 8 = 	Portfolio Per CO
            $pre1stFY[8] = $this->portfolioPerCO($this->sencodFY,$monthEndLoanInfo,$previous1stFiscalYear->fyEndDate);
            // 9 =  Portfolio Per Borrower
            $pre1stFY[9] = $this->portfolioPerBorrower($this->sencodFY,$monthEndLoanInfo,$previous1stFiscalYear->fyEndDate);
            // 10 =  Borrower Coverage % (Borrower & Member)
        	$pre1stFY[10] = $this->borrowerCoverage($this->sencodFY,$monthEndMemberInfo,$monthEndLoanInfo,$previous1stFiscalYear->fyEndDate);
            // 11 = Operating Cost Ratio
            $pre1stFY[11] = $this->operatingCostRatio($this->sencodFY,$monthEndLoanInfo,$vouchers,$voucherDetails,$previous1stFiscalYear->fyStartDate,$previous1stFiscalYear->fyEndDate);
            // 12 = Operational Self-Sufficiency(OSS)
            $pre1stFY[12] = $this->operationalSelfSufficiency($this->sencodFY,$vouchers,$voucherDetails,$previous1stFiscalYear->fyStartDate,$previous1stFiscalYear->fyEndDate);
            // 13 = Financial Self-Sufficiency (FSS)
            $pre1stFY[13] = $this->financialSelfSufficiency($this->sencodFY,$vouchers,$voucherDetails,$previous1stFiscalYear->fyStartDate,$previous1stFiscalYear->fyEndDate);
            // 14 = Current Ratio
            $pre1stFY[14] = $this->currentRatio($this->sencodFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$previous1stFiscalYear->fyEndDate);
            // 15 = Debt To Capital Ratio
            $pre1stFY[15] = $this->debtToCapitalRatio($this->sencodFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$previous1stFiscalYear->fyEndDate);
            // 16 = Debt To Asset Ratio
            $pre1stFY[16] = $this->debtToAssetRatio($this->sencodFY);
            // 17 = Liquidity To Savings Ratio
            $pre1stFY[17] = $this->liquidityToSavingsRatio($this->sencodFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$previous1stFiscalYear->fyEndDate);
            // 18 = Capital Adequacy Ratio
            $pre1stFY[18] = $this->capitalAdequacyRatio($this->sencodFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$previous1stFiscalYear->fyEndDate);
            // 19 = Debt Service Cover Ratio
            $pre1stFY[19] = $this->debtServiceCoverRatio($this->sencodFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$previous1stFiscalYear->fyEndDate,$paymentSchedules);
            // 20 = Rate Of Return On Capital
            $pre1stFY[20] = $this->rateOfReturnOnCapital($this->sencodFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$previous1stFiscalYear->fyEndDate);
            // 21 = Return On Total Asset (ROTA)
            $pre1stFY[21] = $this->returnOnTotalAsset($this->sencodFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$previous1stFiscalYear->fyEndDate);
            // 22 = Yeild On Portfolio
            $pre1stFY[22] = $this->yeildOnPortfolio($this->sencodFY);
            // 23 = Yeild On Portfolio
            $pre1stFY[23] = $this->costPerUnitOfLoanRecovery($this->sencodFY,$monthEndLoanInfo,$previous1stFiscalYear->fyStartDate,$previous1stFiscalYear->fyEndDate);
            // 24 = Bad OD As A % Loan Outstanding
            $pre1stFY[24] = $this->badODLoanOutstanding($monthEndLoanInfo,$previous1stFiscalYear->fyEndDate);
        }



        //// get data for previous second fiscal year
        if (count($previous2ndFiscalYear)>0) {
        	// 0 = onTimeRealization
        	$pre2ndFY[0] = $this->onTimeRealization($this->thirdFY,$monthEndLoanInfo,$previous2ndFiscalYear->fyStartDate,$previous2ndFiscalYear->fyEndDate);
        	// 1 = Cumulative Recovery
        	$pre2ndFY[1] = $this->cumulativeRecovery($this->thirdFY,$monthEndLoanInfo,$previous2ndFiscalYear->fyEndDate);
        	// 2 = 	Portfolio At Risk (PAR)
        	$pre2ndFY[2] = $this->portfolioAtRisk($this->thirdFY,$monthEndLoanInfo,$previous2ndFiscalYear->fyEndDate);
        	// 3 = 	Delinquency Ratio
        	$pre2ndFY[3] = $this->delinquencyRatio($this->thirdFY,$monthEndLoanInfo,$previous2ndFiscalYear->fyEndDate);
        	// 4 = 	Average Loan Size Ratio (Urban & Rural-MFP)
        	$pre2ndFY[4] = $this->averageLoanSizeRatio($this->thirdFY,$monthEndLoanInfo,$previous2ndFiscalYear->fyEndDate);
        	// 5 = 	Member Per Branch
        	$pre2ndFY[5] = $this->memberPerBranch($this->thirdFY,$monthEndMemberInfo,$previous2ndFiscalYear->fyEndDate);
        	// 6 = 	Member Per CO
        	$pre2ndFY[6] = $this->memberPerCO($this->thirdFY,$monthEndMemberInfo,$previous2ndFiscalYear->fyEndDate);
        	// 7 = 	Loanee Per CO
        	$pre2ndFY[7] = $this->loaneePerCO($this->thirdFY,$monthEndMemberInfo,$monthEndLoanInfo,$previous2ndFiscalYear->fyEndDate);
            // 8 =  Portfolio Per CO
            $pre2ndFY[8] = $this->portfolioPerCO($this->thirdFY,$monthEndLoanInfo,$previous2ndFiscalYear->fyEndDate);
        	// 9 = 	Portfolio Per Borrower
            $pre2ndFY[9] = $this->portfolioPerBorrower($this->thirdFY,$monthEndLoanInfo,$previous2ndFiscalYear->fyEndDate);
            // 10 =  Borrower Coverage % (Borrower & Member)
        	$pre2ndFY[10] = $this->borrowerCoverage($this->thirdFY,$monthEndMemberInfo,$monthEndLoanInfo,$previous2ndFiscalYear->fyEndDate);
            // 11 = Operating Cost Ratio
            $pre2ndFY[11] = $this->operatingCostRatio($this->thirdFY,$monthEndLoanInfo,$vouchers,$voucherDetails,$previous2ndFiscalYear->fyStartDate,$previous2ndFiscalYear->fyEndDate);
            // 12 = Operational Self-Sufficiency(OSS)
            $pre2ndFY[12] = $this->operationalSelfSufficiency($this->thirdFY,$vouchers,$voucherDetails,$previous2ndFiscalYear->fyStartDate,$previous2ndFiscalYear->fyEndDate);
            // 13 = Financial Self-Sufficiency (FSS)
            $pre2ndFY[13] = $this->financialSelfSufficiency($this->thirdFY,$vouchers,$voucherDetails,$previous2ndFiscalYear->fyStartDate,$previous2ndFiscalYear->fyEndDate);
            // 14 = Current Ratio
            $pre2ndFY[14] = $this->currentRatio($this->thirdFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$previous2ndFiscalYear->fyEndDate);
            // 15 = Debt To Capital Ratio
            $pre2ndFY[15] = $this->debtToCapitalRatio($this->thirdFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$previous2ndFiscalYear->fyEndDate);
            // 16 = Debt To Asset Ratio
            $pre2ndFY[16] = $this->debtToAssetRatio($this->thirdFY);
            // 17 = Liquidity To Savings Ratio
            $pre2ndFY[17] = $this->liquidityToSavingsRatio($this->thirdFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$previous2ndFiscalYear->fyEndDate);
            // 18 = Capital Adequacy Ratio
            $pre2ndFY[18] = $this->capitalAdequacyRatio($this->thirdFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$previous2ndFiscalYear->fyEndDate);
            // 19 = Debt Service Cover Ratio
            $pre2ndFY[19] = $this->debtServiceCoverRatio($this->thirdFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$previous2ndFiscalYear->fyEndDate,$paymentSchedules);
            // 20 = Rate Of Return On Capital
            $pre2ndFY[20] = $this->rateOfReturnOnCapital($this->thirdFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$previous2ndFiscalYear->fyEndDate);
            // 21 = Return On Total Asset (ROTA)
            $pre2ndFY[21] = $this->returnOnTotalAsset($this->thirdFY,1,$accOpeningBalances,$vouchers,$voucherDetails,$previous2ndFiscalYear->fyEndDate);
            // 22 = Yeild On Portfolio
            $pre2ndFY[22] = $this->yeildOnPortfolio($this->thirdFY);
            // 23 = Yeild On Portfolio
            $pre2ndFY[23] = $this->costPerUnitOfLoanRecovery($this->thirdFY,$monthEndLoanInfo,$previous2ndFiscalYear->fyStartDate,$previous2ndFiscalYear->fyEndDate);
            // 24 = Bad OD As A % Loan Outstanding
            $pre2ndFY[24] = $this->badODLoanOutstanding($monthEndLoanInfo,$previous2ndFiscalYear->fyEndDate);
        }
        
        $data = array(
            'start'                     => $start,
            'filBranch' 				=> $req->filBranch,
            'filDate' 					=> $filDate,
            'ratioAnalysisCategories' 	=> $ratioAnalysisCategories,
            'ratioAnalysisComponents' 	=> $ratioAnalysisComponents,

            'reportingDate' 			=> $reportingDate,
        	'previous1stMonth' 			=> $previous1stMonth,
        	'previous2ndMonth' 			=> $previous2ndMonth,
        	'currentFiscalYear' 		=> $currentFiscalYear,
        	'previous1stFiscalYear' 	=> $previous1stFiscalYear,
        	'previous2ndFiscalYear' 	=> $previous2ndFiscalYear,
        	'yearEndPendingBranchIds' 	=> $yearEndPendingBranchIds,

        	'currMonth' 				=> $currMonth,
        	'pre1stMonth' 				=> $pre1stMonth,
        	'pre2ndMonth' 				=> $pre2ndMonth,
        	'currFY' 					=> $currFY,
        	'pre1stFY' 					=> $pre1stFY,
        	'pre2ndFY' 					=> $pre2ndFY
        );

        return view('microfin.reports.ratioAnalysisStatement.reportBody',$data);
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
                    $filBranchIds = $filBranchIds +  $branchIds;                   
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
                        $filBranchIds = $filBranchIds +  $branchIds;                   
                    }
                }
            }
        }
        
        return $filBranchIds;
    }

    public function getLastCompleteFiscalYearForABranch($branchId,$monthEndDate,$fiscalYears){    	

    	$date = Carbon::parse($monthEndDate);
    	$fiscalYearEndMonth = Carbon::parse($fiscalYears->max('fyEndDate'))->month;

    	// if fiscalYear is June-July
    	if ($fiscalYearEndMonth==6) {
    		if ($date->month<=6) {
    			$yearEndMonth = Carbon::parse('30-06-'.($date->year));
    			// $yearEndMonth = Carbon::parse('31-05-'.($date->year));
    		}
    		else{
    			$yearEndMonth = Carbon::parse('30-06-'.($date->year+1));    			
    			// $yearEndMonth = Carbon::parse('31-05-'.($date->year+1));    			
    		}
    	}
    	// if fiscalYear is January-December
    	else{
			$yearEndMonth = Carbon::parse('31-12-'.($date->year));
    	}
    	
    	$hasMonthEnd = (int) DB::table('mfn_month_end')
		    					->where('date','>=',$yearEndMonth->format('Y-m-d'))
		    					->where('branchIdFk',$branchId)
		    					->value('id');

    	if ($hasMonthEnd==0) {
    		$monthEndDate = Carbon::parse($monthEndDate)->subYear()->format('Y-m-d');
    		return $this->getLastCompleteFiscalYearForABranch($branchId,$monthEndDate,$fiscalYears);
    	}
    	else{
    		return $yearEndMonth->format('Y-m-d');
    	}

    }

    public function getYearEndPendingsBranchIds($branchIds,$monthEndDate,$fiscalYears){

    	$date = Carbon::parse($monthEndDate);
    	$fiscalYearEndMonth = Carbon::parse($fiscalYears->where('fyEndDate','<',$monthEndDate)->max('fyEndDate'))->month;

    	// if fiscalYear is June-July
    	if ($fiscalYearEndMonth==6) {
    		if ($date->month<=6) {
    			$yearEndMonth = Carbon::parse('30-06-'.($date->year-1));
    			// $yearEndMonth = Carbon::parse('31-05-'.($date->year));
    		}
    		else{
    			$yearEndMonth = Carbon::parse('30-06-'.($date->year));    			
    			// $yearEndMonth = Carbon::parse('31-05-'.($date->year+1));    			
    		}
    	}
    	// if fiscalYear is January-December
    	else{
			$yearEndMonth = Carbon::parse('31-12-'.($date->year-1));
    	}
    	
    	$monthEndBranchIds = DB::table('mfn_month_end')
		    					->where('date','>=',$yearEndMonth->format('Y-m-d'))
		    					->whereIn('branchIdFk',$branchIds)
		    					->pluck('branchIdFk')
		    					->toArray();

    	$pendingBranchIds = array_diff($branchIds, $monthEndBranchIds);

    	if (count($pendingBranchIds)==count($branchIds)) {
    		$monthEndDate = Carbon::parse($monthEndDate)->subYear()->format('Y-m-d');
    		return $this->getYearEndPendingsBranchIds($branchIds,$monthEndDate,$fiscalYears);
    	}
    	else{
    		$data = array(
    			'pendingBranchIds' => $pendingBranchIds,
    			'lastFiscalYear' => $yearEndMonth->format('Y-m-d')
    		);
    		return $data;
    	}

    }

    public function getAllChildsOfAParentInLedger($parentLedgerId){
        $childs = [];
        $isGroupHead = 1;
        $parentLedgerIds = [$parentLedgerId];
        
        while($isGroupHead==1) {
            $immediateChilds = DB::table('acc_account_ledger')->whereIn('parentId',$parentLedgerIds)->select('id','isGroupHead')->get();
            $parentLedgerIds = $immediateChilds->pluck('id')->toArray();
            $isGroupHead = $immediateChilds->max('isGroupHead');
            if ($isGroupHead==0) {
                $childs = $immediateChilds->pluck('id')->toArray();
            }
        }

        return $childs;
    }

    // 1
    public function onTimeRealization(&$dataArray,$monthEndLoanInfo,$startDate,$endDate){

    	$loanInfo = $monthEndLoanInfo
						->where('date','>=',$startDate)
						->where('date','<=',$endDate);

    	if (count($loanInfo)==0) {
    		return '-';
    	}

    	$recoverableAmount = $loanInfo->sum('recoverableAmount');
    	$regularRecoveryAmount = $loanInfo->sum('regularAmount');

    	if ($recoverableAmount==0) {
    		return '100%';
    	}
    	$result = $regularRecoveryAmount/$recoverableAmount*100;
    	if ($result==100) {
    		$result = intval($result*100)/100;	
    	}
    	else{
    		$result = number_format(intval($result*100)/100,2);
    	}
    	return $result.'%';
    }

    // 2
    public function cumulativeRecovery(&$dataArray,$monthEndLoanInfo,$endDate){
    	
    	$loanInfo = $monthEndLoanInfo->where('date','<=',$endDate);

    	if (count($loanInfo)==0) {
    		return '-';
    	}

    	$recoverableAmount = $loanInfo->sum('recoverableAmount');
    	$regularRecoveryAmount = $loanInfo->sum('regularAmount');

    	if ($recoverableAmount==0) {
    		return '100%';
    	}
    	$result = $regularRecoveryAmount/$recoverableAmount*100;
    	if ($result==100) {
    		$result = intval($result*100)/100;	
    	}
    	else{
    		$result = number_format(intval($result*100)/100,2);
    	}
    	return $result.'%';
    }

    // 3
    public function portfolioAtRisk(&$dataArray,$monthEndLoanInfo,$endDate){
    	
    	$loanInfo = $monthEndLoanInfo->where('date','=',$endDate);

    	if (count($loanInfo)==0) {
    		return '-';
    	}

    	$closingOutstandingAmount = $loanInfo->sum('closingOutstandingAmount');
    	$watchfulOutstanding = $loanInfo->sum('watchfulOutstanding');
    	$substandardOutstanding = $loanInfo->sum('substandardOutstanding');
    	$doubtfullOutstanding = $loanInfo->sum('doubtfullOutstanding');
    	$badOutstanding = $loanInfo->sum('badOutstanding');

    	if ($closingOutstandingAmount==0) {
    		return '0.00%';
    	}

    	$result = ($watchfulOutstanding+$substandardOutstanding+$doubtfullOutstanding+$badOutstanding)/$closingOutstandingAmount*100;
    	if ($result==100) {
    		$result = intval($result*100)/100;	
    	}
    	else{
    		$result = number_format(intval($result*100)/100,2);
    	}
    	return $result.'%';
    }

    // 4
    public function delinquencyRatio(&$dataArray,$monthEndLoanInfo,$endDate){
    	
    	$loanInfo = $monthEndLoanInfo->where('date','=',$endDate);

    	if (count($loanInfo)==0) {
    		return '-';
    	}

    	$closingOutstandingAmount = $loanInfo->sum('closingOutstandingAmount');
    	$watchfulOverdue = $loanInfo->sum('watchfulOverdue');
    	$substandardOverdue = $loanInfo->sum('substandardOverdue');
    	$doubtfullOverdue = $loanInfo->sum('doubtfullOverdue');
    	$badOverdue = $loanInfo->sum('badOverdue');

    	if ($closingOutstandingAmount==0) {
    		return '0.00%';
    	}

    	$result = ($watchfulOverdue+$substandardOverdue+$doubtfullOverdue+$badOverdue)/$closingOutstandingAmount*100;
    	if ($result==100) {
    		$result = intval($result*100)/100;	
    	}
    	else{
    		$result = number_format(intval($result*100)/100,2);
    	}
    	return $result.'%';
    }

    // 5
    public function averageLoanSizeRatio(&$dataArray,$monthEndLoanInfo,$endDate){

    	// get the loan information of Jagoron loan category, e.g. Jagoron means Urban & Rural-MFP
    	$jagoronProductIds = DB::table('mfn_loans_product')
    								->where('productCategoryId',1)
    								->pluck('id')
    								->toArray();

        
    	
    	$loanInfo = $monthEndLoanInfo
							->where('date','<=',$endDate)
							->whereIn('productIdFk',$jagoronProductIds);

    	if (count($loanInfo)==0) {
    		return '-';
    	}

    	$borrowerNo = $loanInfo->sum('closingBorrowerNo');
    	$disbursedAmount = $loanInfo->sum('disbursedAmount');

        if ($borrowerNo==0) {
            return '-';
        }

    	$result = $disbursedAmount/$borrowerNo;
    	$result = round($result);
    	return $result;
    }

    // 6
    public function memberPerBranch(&$dataArray,$monthEndMemberInfo,$endDate){
    	
    	$memberInfo = $monthEndMemberInfo->where('date','=',$endDate);

    	if (count($memberInfo)==0) {
    		return '-';
    	}

    	$memberNo = $memberInfo->sum('fClosingMember') + $memberInfo->sum('mClosingMember');
    	$branchNo = $memberInfo->unique('branchIdFk')->count();

    	$result = $memberNo/$branchNo;
    	$result = round($result);
    	return $result;
    }

    // 7
    public function memberPerCO(&$dataArray,$monthEndMemberInfo,$endDate){
    	
    	$memberInfo = $monthEndMemberInfo->where('date','=',$endDate);

    	if (count($memberInfo)==0) {
    		return '-';
    	}

    	$memberNo = $memberInfo->sum('fClosingMember') + $memberInfo->sum('mClosingMember');

    	$branchIds = $monthEndMemberInfo->unique('branchIdFk')->pluck('branchIdFk')->toArray();

    	$samityIds = DB::table('mfn_samity')
    					->where('softDel',0)
    					->whereIn('branchId',$branchIds)
    					->where('openingDate','<=',$endDate)
    					->pluck('id')
    					->toArray();

    	$creditOfficerIds = MicroFin::getOnDateFieldOfficerIds($samityIds,$endDate);

    	$creditOfficerNo = count($creditOfficerIds);

        $dataArray['creditOfficerNo'] = $creditOfficerNo;

    	$result = $memberNo/$creditOfficerNo;
    	$result = round($result);
    	return $result;
    }

    // 8
    public function loaneePerCO(&$dataArray,$monthEndMemberInfo,$monthEndLoanInfo,$endDate){
    	
    	$memberInfo = $monthEndMemberInfo->where('date','=',$endDate);
    	$loanInfo = $monthEndLoanInfo->where('date','=',$endDate);

    	if (count($memberInfo)==0) {
    		return '-';
    	}

    	$closingBorrowerNo = $loanInfo->sum('closingBorrowerNo');

        if (isset($dataArray['creditOfficerNo'])) {
            $creditOfficerNo = $dataArray['creditOfficerNo'];
        }
        else{
            $branchIds = $monthEndMemberInfo->unique('branchIdFk')->pluck('branchIdFk')->toArray();

            $samityIds = DB::table('mfn_samity')
                            ->where('softDel',0)
                            ->whereIn('branchId',$branchIds)
                            ->where('openingDate','<=',$endDate)
                            ->pluck('id')
                            ->toArray();

            $creditOfficerIds = MicroFin::getOnDateFieldOfficerIds($samityIds,$endDate);

            $creditOfficerNo = count($creditOfficerIds);
        }

    	$result = $closingBorrowerNo/$creditOfficerNo;
    	$result = round($result);
    	return $result;
    }

    // 9
    public function portfolioPerCO(&$dataArray,$monthEndLoanInfo,$endDate){
    	
    	$loanInfo = $monthEndLoanInfo->where('date','=',$endDate);

    	if (count($loanInfo)==0) {
    		return '-';
    	}

    	$closingOutstandingAmount = $loanInfo->sum('closingOutstandingAmount');
    	
    	$branchIds = $monthEndLoanInfo->unique('branchIdFk')->pluck('branchIdFk')->toArray();

    	$samityIds = DB::table('mfn_samity')
    					->where('softDel',0)
    					->whereIn('branchId',$branchIds)
    					->where('openingDate','<=',$endDate)
    					->pluck('id')
    					->toArray();

    	$creditOfficerIds = MicroFin::getOnDateFieldOfficerIds($samityIds,$endDate);

    	$creditOfficerNo = count($creditOfficerIds);

        if ($creditOfficerNo==0) {
            return '-';
        }

    	$result = $closingOutstandingAmount/$creditOfficerNo;
    	$result = number_format($result,2);
    	return $result;
    }

    // 10
    public function portfolioPerBorrower(&$dataArray,$monthEndLoanInfo,$endDate){
        
        $loanInfo = $monthEndLoanInfo->where('date','=',$endDate);

        if (count($loanInfo)==0) {
            return '-';
        }

        $closingOutstandingAmount = $loanInfo->sum('closingOutstandingAmount');
        $borrowerNo = $loanInfo->sum('cumBorrowerNo');

        if ($borrowerNo==0) {
            return '-';
        }

        $result = $closingOutstandingAmount/$borrowerNo;
        $result = number_format($result,2);
        return $result;
    }

    // 11
    public function borrowerCoverage(&$dataArray,$monthEndMemberInfo,$monthEndLoanInfo,$endDate){
        
        $memberInfo = $monthEndMemberInfo->where('date','=',$endDate);
        $loanInfo = $monthEndLoanInfo->where('date','=',$endDate);

        if (count($memberInfo)==0) {
            return '-';
        }

        $closingBorrowerNo = $loanInfo->sum('closingBorrowerNo');    
        $memberNo = $memberInfo->sum('fClosingMember') + $memberInfo->sum('mClosingMember');

        $result = $closingBorrowerNo/$memberNo*100;
        $result = number_format($result,2);
        return $result;
    }

    // 12
    public function operatingCostRatio(&$dataArray,$monthEndLoanInfo,$vouchers,$voucherDetails,$startDate,$endDate){
        // total operating cost is-
        // all heads under Salary & Benefits(Code 51000) and General & Administrative Expenses(Code 52000).
        // Salary & Benefits id = 6, Administrative Expenses id = 7
        $operatingCostLedgerHeads = $this->getAllChildsOfAParentInLedger(6);
        $operatingCostLedgerHeads += $this->getAllChildsOfAParentInLedger(7);

        $cVouchers = $vouchers->where('voucherDate','>=',$startDate)->where('voucherDate','<=',$endDate);
        $cVoucherDetails = $voucherDetails->whereIn('voucherId',$cVouchers->pluck('id')->toArray());

        $operatingCost = $cVoucherDetails->whereIn('debitAcc',$operatingCostLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('creditAcc',$operatingCostLedgerHeads)->sum('amount');

        $firstMothDate = $monthEndLoanInfo->where('date','>=',$startDate)->sortBy('date')->first()->date;

        $firstLoanOutstanging = $monthEndLoanInfo->where('date','=',$firstMothDate)->sum('closingOutstandingAmount');
        $secondLoanOutstanging = $monthEndLoanInfo->where('date','=',$endDate)->sum('closingOutstandingAmount');
        $avgLoanOutstanging = ($firstLoanOutstanging+$secondLoanOutstanging)/2;

        $dataArray['operatingCost'] = $operatingCost;
        $dataArray['avgLoanOutstanging'] = $avgLoanOutstanging;

        $result = $operatingCost/$avgLoanOutstanging*100;
        $result = number_format($result,2);
        return $result.'%';
    }

    // 13
    public function operationalSelfSufficiency(&$dataArray,$vouchers,$voucherDetails,$startDate,$endDate){

        $cVouchers = $vouchers->where('voucherDate','>=',$startDate)->where('voucherDate','<=',$endDate);
        $cVoucherDetails = $voucherDetails->whereIn('voucherId',$cVouchers->pluck('id')->toArray());


        // operating income is-
        // all heads under Service Charge Income(Code 41000).
        // Service Charge Income id = 45
        $operatingIncomeLedgerHeads = $this->getAllChildsOfAParentInLedger(45);

        $operatingIncome = $cVoucherDetails->whereIn('creditAcc',$operatingIncomeLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('debitAcc',$operatingIncomeLedgerHeads)->sum('amount');
        // note: incase of income sum will be credit minus(-) debit;

        // Financial Cost is-
        // all heads under  Financial Expenses(Code 53000).
        // Service Charge Income id = 8
        $financialExpensesLedgerHeads = $this->getAllChildsOfAParentInLedger(8);
        $financialCost = $cVoucherDetails->whereIn('debitAcc',$financialExpensesLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('creditAcc',$financialExpensesLedgerHeads)->sum('amount');

        // total operating cost is-
        // all heads under Salary & Benefits(Code 51000) and General & Administrative Expenses(Code 52000).
        // Salary & Benefits id = 6, Administrative Expenses id = 7
        $operatingCostLedgerHeads = $this->getAllChildsOfAParentInLedger(6);
        $operatingCostLedgerHeads += $this->getAllChildsOfAParentInLedger(7);

        $operatingCost = $cVoucherDetails->whereIn('debitAcc',$operatingCostLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('creditAcc',$operatingCostLedgerHeads)->sum('amount');

        // LLP is Loan Loss Provision Expense(Code 55101)
        // ledger id is 337

        $llp = $cVoucherDetails->where('debitAcc',337)->sum('amount') - $cVoucherDetails->where('creditAcc',337)->sum('amount');

        $dataArray['financialCost'] = $financialCost;
        $dataArray['operatingCost'] = $operatingCost;
        $dataArray['llp'] = $llp;


        if ($financialCost+$operatingCost+$llp==0) {
            return '0.00%';
        }
        
        $result = $operatingIncome/($financialCost+$operatingCost+$llp)*100;        
        $result = number_format($result,2);

        return $result.'%';
    }

    // 14
    public function financialSelfSufficiency(&$dataArray,$vouchers,$voucherDetails,$startDate,$endDate){

        $cVouchers = $vouchers->where('voucherDate','>=',$startDate)->where('voucherDate','<=',$endDate);
        $cVoucherDetails = $voucherDetails->whereIn('voucherId',$cVouchers->pluck('id')->toArray());

        // total income is-
        // all heads under Total Charge Income(Code 40000).
        // total Charge Income id = 4
        $totalIncomeLedgerHeads = $this->getAllChildsOfAParentInLedger(4);

        $totalIncome = $cVoucherDetails->whereIn('creditAcc',$totalIncomeLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('debitAcc',$totalIncomeLedgerHeads)->sum('amount');
        // note: incase of income sum will be credit minus(-) debit;

        $dataArray['totalIncome'] = $totalIncome;
         
        $financialCost = $dataArray['financialCost'];
        $operatingCost = $dataArray['operatingCost'];
        $llp = $dataArray['llp'];      

        
        if ($financialCost+$operatingCost+$llp==0) {
            return '0.00%';
        }
        
        $result = $totalIncome/($financialCost+$operatingCost+$llp)*100;        
        $result = number_format($result,2);

        return $result.'%';
    }

    // 15
    public function currentRatio(&$dataArray,$isCompleteFiscalYear,$accOpeningBalances,$vouchers,$voucherDetails,$endDate){
        // Asset is-
        // all heads under Asset(Code 10000).
        // Asset id = 1
        $assetLedgerHeads = $this->getAllChildsOfAParentInLedger(1);

        // Liabilities is-
        // all heads under Liabilities(Code 20000).
        // Liabilities id = 2
        $liabilitiesLedgerHeads = $this->getAllChildsOfAParentInLedger(2);

        if ($isCompleteFiscalYear==1) {
            $cOpeningBalance = $accOpeningBalances->where('openingDate',$endDate);
            $assetAmount = abs($cOpeningBalance->whereIn('ledgerId',$assetLedgerHeads)->sum('balanceAmount'));
            $liabilitiesAmount = abs($cOpeningBalance->whereIn('ledgerId',$liabilitiesLedgerHeads)->sum('balanceAmount'));
        }
        else{
            $cOpeningBalanceDate = $accOpeningBalances->where('openingDate','<',$endDate)->sortByDesc('openingDate')->max('openingDate');
            if ($cOpeningBalanceDate!=null) {
                $cOpeningBalance = $accOpeningBalances->where('openingDate',$cOpeningBalanceDate);
                $cVouchers = $vouchers->where('voucherDate','>',$cOpeningBalanceDate)->where('voucherDate','<=',$endDate);
                $cVoucherDetails = $voucherDetails->whereIn('voucherId',$cVouchers->pluck('id')->toArray());

                $assetAmount = $cOpeningBalance->whereIn('ledgerId',$assetLedgerHeads)->sum('creditAmount') - $cOpeningBalance->whereIn('ledgerId',$assetLedgerHeads)->sum('debitAmount');
                $assetAmount += $cVoucherDetails->whereIn('creditAcc',$assetLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('debitAcc',$assetLedgerHeads)->sum('amount');

                $liabilitiesAmount = $cOpeningBalance->whereIn('ledgerId',$liabilitiesLedgerHeads)->sum('balanceAmount');
                $liabilitiesAmount += $cVoucherDetails->whereIn('debitAcc',$liabilitiesLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('creditAcc',$liabilitiesLedgerHeads)->sum('amount');

            }
            else{
                $cVouchers = $vouchers->where('voucherDate','<=',$endDate);
                $cVoucherDetails = $voucherDetails->whereIn('voucherId',$cVouchers->pluck('id')->toArray());
                $assetAmount = $cVoucherDetails->whereIn('creditAcc',$assetLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('debitAcc',$assetLedgerHeads)->sum('amount');
                $liabilitiesAmount = $cVoucherDetails->whereIn('debitAcc',$liabilitiesLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('creditAcc',$liabilitiesLedgerHeads)->sum('amount');                
            }            
        }

        $dataArray['liabilitiesAmount'] = $liabilitiesAmount;
        $dataArray['assetAmount'] = $assetAmount;

        if ($liabilitiesAmount==0) {
            $result = '-';
        }
        else{
            $result = round($assetAmount/$liabilitiesAmount,2);                
        }

        return $result;
    }

    // 16
    public function debtToCapitalRatio(&$dataArray,$isCompleteFiscalYear,$accOpeningBalances,$vouchers,$voucherDetails,$endDate){
        // all type of loan goes to liabilities
        $liabilitiesAmount = $dataArray['liabilitiesAmount'];

        // Net Worth is-
        // all heads under Capital Fund(Code 30000).
        // Capital Fund id = 3
        $capitalFundLedgerHeads = $this->getAllChildsOfAParentInLedger(3);

        if ($isCompleteFiscalYear==1) {
            $cOpeningBalance = $accOpeningBalances->where('openingDate',$endDate);
            $capitalFundAmount = $cOpeningBalance->whereIn('ledgerId',$capitalFundLedgerHeads)->sum('balanceAmount');
        }
        else{

            $cOpeningBalanceDate = $accOpeningBalances->where('openingDate','<',$endDate)->sortByDesc('openingDate')->max('openingDate');
            if ($cOpeningBalanceDate!=null) {
                $cOpeningBalance = $accOpeningBalances->where('openingDate',$cOpeningBalanceDate);
                $cVouchers = $vouchers->where('voucherDate','>',$cOpeningBalanceDate)->where('voucherDate','<=',$endDate);
                $cVoucherDetails = $voucherDetails->whereIn('voucherId',$cVouchers->pluck('id')->toArray());

                $capitalFundAmount = $cOpeningBalance->whereIn('ledgerId',$capitalFundLedgerHeads)->sum('balanceAmount');
                $capitalFundAmount += $cVoucherDetails->whereIn('debitAcc',$capitalFundLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('creditAcc',$capitalFundLedgerHeads)->sum('amount');

            }
            else{
                $cVouchers = $vouchers->where('voucherDate','<=',$endDate);
                $cVoucherDetails = $voucherDetails->whereIn('voucherId',$cVouchers->pluck('id')->toArray());
                $capitalFundAmount = $cVoucherDetails->whereIn('debitAcc',$capitalFundLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('creditAcc',$capitalFundLedgerHeads)->sum('amount');               
            }
        }

        $dataArray['capitalFundAmount'] = $capitalFundAmount;

        if ($capitalFundAmount==0) {
            $result = '-';
        }
        else{
            $result = round($liabilitiesAmount/$capitalFundAmount,2);                
        }

        return $result;
    }

    // 17
    public function debtToAssetRatio($dataArray){
        // all type of loan goes to liabilities, liabilities = Debt
        $liabilitiesAmount = $dataArray['liabilitiesAmount'];
        $assetAmount = $dataArray['assetAmount'];

        if ($assetAmount==0) {
            return '-';
        }

        $result = round($liabilitiesAmount/$assetAmount*100,2);
        return $result.'%';
    }

    // 18
    public function liquidityToSavingsRatio(&$dataArray,$isCompleteFiscalYear,$accOpeningBalances,$vouchers,$voucherDetails,$endDate){
        // Savings FDR is-
        // all heads under Investment on Savings(Code 15100).
        // Capital Fund id = 300
        $savingsFDRLedgerHeads = $this->getAllChildsOfAParentInLedger(300);

        // Savings Outstanding is-
        // all heads under Members Savings(Code 24000).
        // Capital Fund id = 112
        $memberSavingsLedgerHeads = $this->getAllChildsOfAParentInLedger(112);

        if ($isCompleteFiscalYear==1) {
            $cOpeningBalance = $accOpeningBalances->where('openingDate',$endDate);
            $savingsFDRAmount = $cOpeningBalance->whereIn('ledgerId',$savingsFDRLedgerHeads)->sum('balanceAmount');
            $savOutstandingAmount = $cOpeningBalance->whereIn('ledgerId',$memberSavingsLedgerHeads)->sum('balanceAmount');
        }
        else{
            $cOpeningBalanceDate = $accOpeningBalances->where('openingDate','<',$endDate)->sortByDesc('openingDate')->max('openingDate');
            if ($cOpeningBalanceDate!=null) {
                $cOpeningBalance = $accOpeningBalances->where('openingDate',$cOpeningBalanceDate);
                $cVouchers = $vouchers->where('voucherDate','>',$cOpeningBalanceDate)->where('voucherDate','<=',$endDate);
                $cVoucherDetails = $voucherDetails->whereIn('voucherId',$cVouchers->pluck('id')->toArray());

                $savingsFDRAmount = $cOpeningBalance->whereIn('ledgerId',$savingsFDRLedgerHeads)->sum('creditAmount') - $cOpeningBalance->whereIn('ledgerId',$savingsFDRLedgerHeads)->sum('debitAmount');
                $savingsFDRAmount += $cVoucherDetails->whereIn('creditAcc',$savingsFDRLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('debitAcc',$savingsFDRLedgerHeads)->sum('amount');

                $savOutstandingAmount = $cOpeningBalance->whereIn('ledgerId',$memberSavingsLedgerHeads)->sum('debitAmount') - $cOpeningBalance->whereIn('ledgerId',$memberSavingsLedgerHeads)->sum('creditAmount');
                $savOutstandingAmount += $cVoucherDetails->whereIn('debitAcc',$memberSavingsLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('creditAcc',$memberSavingsLedgerHeads)->sum('amount');
            }
            else{
                $cVouchers = $vouchers->where('voucherDate','<=',$endDate);
                $cVoucherDetails = $voucherDetails->whereIn('voucherId',$cVouchers->pluck('id')->toArray());
                $savingsFDRAmount = $cVoucherDetails->whereIn('creditAcc',$savingsFDRLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('debitAcc',$savingsFDRLedgerHeads)->sum('amount');
                $savOutstandingAmount = $cVoucherDetails->whereIn('debitAcc',$memberSavingsLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('creditAcc',$memberSavingsLedgerHeads)->sum('amount');
            }        
        }

        $dataArray['savingsFDRAmount'] = $savingsFDRAmount;

        if ($savOutstandingAmount==0) {
            return '-';
        }

        $result = round($savingsFDRAmount/$savOutstandingAmount*100,2);
        return $result.'%';
    }

    // 19
    public function capitalAdequacyRatio(&$dataArray,$isCompleteFiscalYear,$accOpeningBalances,$vouchers,$voucherDetails,$endDate){
        // Net Worths
        $capitalFundAmount = $dataArray['capitalFundAmount'];

        // Asset
        $assetAmount = $dataArray['assetAmount'];

        // Savings FDR
        $savingsFDRAmount = $dataArray['savingsFDRAmount'];

        // Cash & Bank Balance is-
        // all heads under Cash & Bank Balance(Code 19000).
        // Capital Fund id = 285
        $cashNBankLedgerHeads = $this->getAllChildsOfAParentInLedger(285);

        if ($isCompleteFiscalYear==1) {
            $cOpeningBalance = $accOpeningBalances->where('openingDate',$endDate);
            $cashNBankAmount = $cOpeningBalance->whereIn('ledgerId',$cashNBankLedgerHeads)->sum('balanceAmount');
        }
        else{
            $cOpeningBalanceDate = $accOpeningBalances->where('openingDate','<',$endDate)->sortByDesc('openingDate')->max('openingDate');
            if ($cOpeningBalanceDate!=null) {
                $cOpeningBalance = $accOpeningBalances->where('openingDate',$cOpeningBalanceDate);
                $cVouchers = $vouchers->where('voucherDate','>',$cOpeningBalanceDate)->where('voucherDate','<=',$endDate);
                $cVoucherDetails = $voucherDetails->whereIn('voucherId',$cVouchers->pluck('id')->toArray());

                $cashNBankAmount = $cOpeningBalance->whereIn('ledgerId',$cashNBankLedgerHeads)->sum('creditAmount') - $cOpeningBalance->whereIn('ledgerId',$cashNBankLedgerHeads)->sum('debitAmount');
                $cashNBankAmount += $cVoucherDetails->whereIn('creditAcc',$cashNBankLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('debitAcc',$cashNBankLedgerHeads)->sum('amount');
            }
            else{
                $cVouchers = $vouchers->where('voucherDate','<=',$endDate);
                $cVoucherDetails = $voucherDetails->whereIn('voucherId',$cVouchers->pluck('id')->toArray());
                $cashNBankAmount = $cVoucherDetails->whereIn('creditAcc',$cashNBankLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('debitAcc',$cashNBankLedgerHeads)->sum('amount');
            }        
        }

        $dataArray['capitalFundAmount'] = $capitalFundAmount;

        $divider = $assetAmount + $savingsFDRAmount + $cashNBankAmount;

        if ($divider==0) {
            return '-';
        }

        $result = round($capitalFundAmount/$divider*100,2);
        return $result.'%';
    }

    // 20
    public function debtServiceCoverRatio(&$dataArray,$isCompleteFiscalYear,$accOpeningBalances,$vouchers,$voucherDetails,$endDate,$paymentSchedules){

        $startDate = Carbon::parse($endDate)->subYear()->format('Y-m-d');

        // Surplus is-
        // all heads under Retained Surplus(Code 31000).
        // Retained Surplus id = 49
        $retainedSurplusLedgerHeads = $this->getAllChildsOfAParentInLedger(49);

        if ($isCompleteFiscalYear==1) {
            $cOpeningBalanceEnd = $accOpeningBalances->where('openingDate',$endDate);
            $surplusAmount = $cOpeningBalanceEnd->whereIn('ledgerId',$retainedSurplusLedgerHeads)->sum('balanceAmount');
        }
        else{
            
            $cOpeningBalanceDateEnd = $accOpeningBalances->where('openingDate','<',$endDate)->sortByDesc('openingDate')->max('openingDate');
            if ($cOpeningBalanceDateEnd!=null) {
                $cOpeningBalance = $accOpeningBalances->where('openingDate',$cOpeningBalanceDateEnd);
                $cVouchers = $vouchers->where('voucherDate','>',$cOpeningBalanceDateEnd)->where('voucherDate','<=',$endDate);
                $cVoucherDetails = $voucherDetails->whereIn('voucherId',$cVouchers->pluck('id')->toArray());

                $surplusAmount = $cOpeningBalance->whereIn('ledgerId',$retainedSurplusLedgerHeads)->sum('debitAmount') - $cOpeningBalance->whereIn('ledgerId',$retainedSurplusLedgerHeads)->sum('creditAmount');
                $surplusAmount += $cVoucherDetails->whereIn('debitAcc',$retainedSurplusLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('creditAcc',$retainedSurplusLedgerHeads)->sum('amount');
            }
            else{
                $cVouchers = $vouchers->where('voucherDate','<=',$endDate);
                $cVoucherDetails = $voucherDetails->whereIn('voucherId',$cVouchers->pluck('id')->toArray());
                $surplusAmount = $cVoucherDetails->whereIn('debitAcc',$retainedSurplusLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('creditAcc',$retainedSurplusLedgerHeads)->sum('amount');
            }
        }

        $dataArray['surplusAmount'] = $surplusAmount;

        $nextYearDate = Carbon::parse($endDate)->addYear()->format('Y-m-d');

        if ($paymentSchedules==null) {
            return '-';
        }

        $next12MonthPayableAmount = $paymentSchedules->where('paymentDate','>',$endDate)->where('paymentDate','<=',$nextYearDate)->sum('totalAmount');

        $result = ($surplusAmount+$next12MonthPayableAmount)/$next12MonthPayableAmount;
        return $result;
    }

    // 21
    public function rateOfReturnOnCapital(&$dataArray,$isCompleteFiscalYear,$accOpeningBalances,$vouchers,$voucherDetails,$endDate){
        $surplusAmount = $dataArray['surplusAmount'];
        $capitalFundAmountEnd = $dataArray['capitalFundAmount'];
        
        // get the capital fund for the begining date
        $startDate = Carbon::parse($endDate)->subYear()->format('Y-m-d');
        // Capital Fund is-
        // all heads under Capital Fund(Code 30000).
        // Capital Fund id = 3
        $capitalFundLedgerHeads = $this->getAllChildsOfAParentInLedger(3);

        if ($isCompleteFiscalYear==1) {
            $cOpeningBalance = $accOpeningBalances->where('openingDate',$startDate);
            $capitalFundAmountBeg = $cOpeningBalance->whereIn('ledgerId',$capitalFundLedgerHeads)->sum('balanceAmount');
        }
        else{

            $cOpeningBalanceDate = $accOpeningBalances->where('openingDate','<',$endDate)->sortByDesc('openingDate')->max('openingDate');
            if ($cOpeningBalanceDate!=null) {
                $cOpeningBalance = $accOpeningBalances->where('openingDate',$cOpeningBalanceDate);
                $cVouchers = $vouchers->where('voucherDate','>',$cOpeningBalanceDate)->where('voucherDate','<=',$endDate);
                $cVoucherDetails = $voucherDetails->whereIn('voucherId',$cVouchers->pluck('id')->toArray());

                $capitalFundAmountBeg = $cOpeningBalance->whereIn('ledgerId',$capitalFundLedgerHeads)->sum('balanceAmount');
                $capitalFundAmountBeg += $cVoucherDetails->whereIn('debitAcc',$capitalFundLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('creditAcc',$capitalFundLedgerHeads)->sum('amount');

            }
            else{
                $cVouchers = $vouchers->where('voucherDate','<=',$endDate);
                $cVoucherDetails = $voucherDetails->whereIn('voucherId',$cVouchers->pluck('id')->toArray());
                $capitalFundAmountBeg = $cVoucherDetails->whereIn('debitAcc',$capitalFundLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('creditAcc',$capitalFundLedgerHeads)->sum('amount');               
            }
        }

        $capitalFundAmount = ($capitalFundAmountBeg+$capitalFundAmountEnd)/2;

        if ($capitalFundAmount==0) {
            return '-';
        }

        $result = $surplusAmount/$capitalFundAmount;
        return $result;
    }

    // 22
    public function returnOnTotalAsset(&$dataArray,$isCompleteFiscalYear,$accOpeningBalances,$vouchers,$voucherDetails,$endDate){
        $surplusAmount = $dataArray['surplusAmount'];
        $assetAmountEnd = $dataArray['assetAmount'];

        $startDate = Carbon::parse($endDate)->subYear()->format('Y-m-d');

        // Asset is-
        // all heads under Asset(Code 10000).
        // Asset id = 1
        $assetLedgerHeads = $this->getAllChildsOfAParentInLedger(1);

        if ($isCompleteFiscalYear==1) {
            $cOpeningBalance = $accOpeningBalances->where('openingDate',$startDate);
            $assetAmountBeg = abs($cOpeningBalance->whereIn('ledgerId',$assetLedgerHeads)->sum('balanceAmount'));
        }
        else{
            $cOpeningBalanceDate = $accOpeningBalances->where('openingDate','<',$startDate)->sortByDesc('openingDate')->max('openingDate');
            if ($cOpeningBalanceDate!=null) {
                $cOpeningBalance = $accOpeningBalances->where('openingDate',$cOpeningBalanceDate);
                $cVouchers = $vouchers->where('voucherDate','>',$cOpeningBalanceDate)->where('voucherDate','<',$startDate);
                $cVoucherDetails = $voucherDetails->whereIn('voucherId',$cVouchers->pluck('id')->toArray());

                $assetAmountBeg = $cOpeningBalance->whereIn('ledgerId',$assetLedgerHeads)->sum('creditAmount') - $cOpeningBalance->whereIn('ledgerId',$assetLedgerHeads)->sum('debitAmount');
                $assetAmountBeg += $cVoucherDetails->whereIn('creditAcc',$assetLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('debitAcc',$assetLedgerHeads)->sum('amount');
            }
            else{
                $cVouchers = $vouchers->where('voucherDate','<',$startDate);
                $cVoucherDetails = $voucherDetails->whereIn('voucherId',$cVouchers->pluck('id')->toArray());
                $assetAmountBeg = $cVoucherDetails->whereIn('creditAcc',$assetLedgerHeads)->sum('amount') - $cVoucherDetails->whereIn('debitAcc',$assetLedgerHeads)->sum('amount');
            }            
        }

        $avgAssetAmount = ($assetAmountBeg + $assetAmountEnd)/2;

        if ($avgAssetAmount==0) {
            return '-';
        }

        $result = number_format(abs($surplusAmount/$avgAssetAmount),2);
        return $result;
    }

    // 23
    public function yeildOnPortfolio(&$dataArray){
        $totalIncome = $dataArray['totalIncome'];
        $avgLoanOutstanging = $dataArray['avgLoanOutstanging'];

        if ($avgLoanOutstanging==0) {
            return '-';
        }

        $result = number_format($totalIncome/$avgLoanOutstanging*100,2);
        return $result.'%';

    }

    // 24
    public function costPerUnitOfLoanRecovery(&$dataArray,$monthEndLoanInfo,$startDate,$endDate){
        $operatingCost = $dataArray['operatingCost'];

        $loanInfo = $monthEndLoanInfo
                        ->where('date','>=',$startDate)
                        ->where('date','<=',$endDate);

        $recoveryAmount = $loanInfo->sum('recoveryAmount');

        if ($recoveryAmount==0) {
            return '-';
        }

        $result = number_format($operatingCost/$recoveryAmount*100,2);
        return $result.'%';
    }

    // 25 Bad OD As A % Loan Outstanding
    public function badODLoanOutstanding($monthEndLoanInfo,$endDate){
        $loanInfo = $monthEndLoanInfo->where('date','=',$endDate);

        $badOutstanding = $loanInfo->sum('badOutstanding');
        $closingOutstandingAmount = $loanInfo->sum('closingOutstandingAmount');

        if ($closingOutstandingAmount==0) {
            return '-';
        }

        $result = number_format($badOutstanding/$closingOutstandingAmount*100);
        return $result.'%';
    }

}
