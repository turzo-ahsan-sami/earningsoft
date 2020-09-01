<?php

namespace App\Http\Controllers\microfin\reports;
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

class MfnReportController extends Controller {

	protected $MicroFinance;

	public function __construct() {

		$this->MicroFinance = New MicroFinance;
	}

    public function index($navValue) {

        // $navValue=$navValue;
        $navName="";
        if ($navValue==="1.1") {
            $navName="PKSF POMIS-1";
        }else if ($navValue==="1.2") {
            $navName="PKSF POMIS-2";
        }else if ($navValue==="1.3") {
            $navName="PKSF POMIS-2A";
        }else if ($navValue==="1.4") {
            $navName="PKSF POMIS-3";
        }else if ($navValue==="1.5") {
            $navName="PKSF POMIS-3A";
        }else if ($navValue==="1.6") {
            $navName="PKSF POMIS-5a";
        }else if ($navValue==="2.1") {
            $navName="MRA-MFI-01";
        }else if ($navValue==="2.2") {
            $navName="MRA-MFI-02";
        }else if ($navValue==="2.3") {
            $navName="MRA-MFI-03/A";
        }else if ($navValue==="2.3") {
            $navName="MRA-MFI-03/A";
        }else if ($navValue==="2.4") {
            $navName="MRA-MFI-03/B";
        }else if ($navValue==="2.5") {
            $navName="MRA-MFI-04/A";
        }else if ($navValue==="2.6") {
            $navName="MRA-MFI-04/B";
        }else if ($navValue==="2.7") {
            $navName="MRA-MFI-05";
        }else if ($navValue==="2.8") {
            $navName="MRA-MFI-06";
        }else if ($navValue==="2.9") {
            $navName="MRA-CDB-02/A";
        }else if ($navValue==="2.10") {
            $navName="MRA-CDB-03/A";
        }else if ($navValue==="2.11") {
            $navName="MRA-LLP-01";
        }else if ($navValue==="2.12") {
            $navName="MRA-LLP-02";
        }else if ($navValue==="2.13") {
            $navName="MRA-LLP-03";
        }else if ($navValue==="2.14") {
            $navName="MRA-LLP-03";
        }else if ($navValue==="2.15") {
            $navName="MRA-LLP-05";
        }else if ($navValue==="2.16") {
            $navName="MRA-LLP-06";
        }else if ($navValue==="2.17") {
            $navName="MRA-Monthly Report";
        }else if ($navValue==="3.1") {
            $navName="Daily Collection Component Wise";
        }else if ($navValue==="3.2") {
            $navName="Unauthorized Daily Recoverable & Collection Component Wise";
        }else if ($navValue==="3.3") {
            $navName="Branch Managers";
        }else if ($navValue==="3.4") {
            $navName="Field Officer Report  (Samity & Component Wise)";
        }else if ($navValue==="3.6") {
            $navName="Loan Classification & DMR";
        }else if ($navValue==="3.8") {
            $navName="Samity Wise Monthly Loan & Saving Collection Sheet";
        // }else if ($navValue==="3.10") {
        //     $navName="Periodic Collection Component Wise";
        }else if ($navValue==="3.11") {
            $navName="Samity Wise Monthly Loan & Saving Basic Collection Sheet";
        }else if ($navValue==="3.12") {
            $navName="Monthly Loan & Savings Collection Sheet";
        }else if ($navValue==="3.13") {
            $navName="Daily Recoverable Report (Member Wise)";
        }else if ($navValue==="3.14") {
            $navName="Manual Loan & Savings Collection Sheet";
        }else if ($navValue==="3.15") {
            $navName="Write Off Collection Sheet";
        }else if ($navValue==="3.16") {
            $navName="Consolidated Branch Manager Report";
        }else if ($navValue==="4.1") {
            $navName="Admission register";
        }else if ($navValue==="4.2") {
            $navName="Savings refund register";
        }else if ($navValue==="4.3") {
            $navName="Loan disbursement register";
        }else if ($navValue==="4.4") {
            $navName="Fully paid loan register";
        }else if ($navValue==="4.5") {
            $navName="Member cancellation register";
        }else if ($navValue==="4.6") {
            $navName="Member Wise Subsidiary Loan and Savings Ledger";
        }else if ($navValue==="4.7") {
            $navName="Inactive Member Register";
        }else if ($navValue==="4.8") {
            $navName="Saving Interest Information(Financial Year)";
        }else if ($navValue==="4.9") {
            $navName="Saving Interest Register(On Closing)";
        }else if ($navValue==="4.10") {
            $navName="Saving Interest Provision";
        }else if ($navValue==="4.11") {
            $navName="Due Register";
        }else if ($navValue==="4.12") {
            $navName="Daily Recoverable and Collection Register";
        }else if ($navValue==="4.13") {
            $navName="Daily Recoverable and Collection Register";
        }else if ($navValue==="4.14") {
            $navName="Written Off Register";
        }else if ($navValue==="4.15") {
            $navName="Written Off Collection Register";
        }else if ($navValue==="4.16") {
            $navName="Dual Loanee Register";
        }else if ($navValue==="4.17") {
            $navName="Loan Waiver For Death Members";
        }else if ($navValue==="4.18") {
            $navName="Rebate Register";
        }else if ($navValue==="4.19") {
            $navName="Due Collection Register";
        }else if ($navValue==="4.20") {
            $navName="Loan Adjustment Resister";
        }else if ($navValue==="4.21") {
            $navName="Transfer Register";
        }else if ($navValue==="4.22") {
            $navName="Holiday Due Register";
        }else if ($navValue==="4.23") {
            $navName="Loan Disbursement & Recovery";
        }else if ($navValue==="4.24") {
            $navName="Loan Proposal Register";
        }else if ($navValue==="4.25") {
            $navName="FDR Register";
        }else if ($navValue==="5.1") {
            $navName="Consolidated Balancing";
        }else if ($navValue==="5.2") {
            $navName="Ratio analysis statement";
        }else if ($navValue==="5.3") {
            $navName="Consolidated ratio analysis";
        }else if ($navValue==="6") {
            $navName="Pass Book";
        }else if ($navValue==="7") {
            $navName="Branch Wise Samity List";
        }else if ($navValue==="8") {
            $navName="Samity Wise Member List";
        }else if ($navValue==="10") {
            $navName="Member Migration Balance";
        }else if ($navValue==="13") {
            $navName="Advance Due register";
        }else if ($navValue==="16") {
            $navName="Loan Statement (Recoverable Calculation)";
        }else if ($navValue==="17.1") {
            $navName="Member wise Pass Book Balancing Register";
        }else if ($navValue==="17.2") {
            $navName="Credit Officer wise Pass Book Balancing Register";
        }else if ($navValue==="17.3") {
            $navName="Branch wise Pass Book Balancing Register";
        }else if ($navValue==="17.4") {
            $navName="Pass Book Checking";
        }else if ($navValue==="18.1") {
            $navName="MSP/DPS Register";
        }else if ($navValue==="18.2") {
            $navName="Monthly Progress";
        }else if ($navValue==="18.3") {
            $navName="Monthly Purpose Wise Loan";
        }else if ($navValue==="18.5") {
            $navName="Monthly Target Achievement";
        }else if ($navValue==="18.6") {
            $navName="Monthly Branch Manager";
        }else if ($navValue==="18.10") {
            $navName="District and Upazila wise cumulative loan disbursement";
        }else if ($navValue==="18.11") {
            $navName="Loan Installment Passing";
        }else if ($navValue==="19.1") {
            $navName="Monthly Statement of Agroshor Report(format-01)";
        }else if ($navValue==="19.2") {
            $navName="Half Yearly Purpose Wise Report of Agroshor Activities";
        }else if ($navValue==="19.3") {
            $navName="Half Yearly Statement Of Employment Created by Agrosor Entrepreneur";
        }else if ($navValue==="19.4") {
            $navName="Employment Register Reports (Format- 4)";
        }else if ($navValue==="20") {
            $navName="Disaster Management Fund";
        }else if ($navValue==="21") {
            $navName="MIS and AIS Cross Check";
        }else if ($navValue==="22") {
            $navName="Periodical Progress";
        }



        $userBranchId=Auth::user()->branchId;
        if ($userBranchId==1) {
    	   $branchesOption = $this->MicroFinance->getBranchOptions(1);
        }else{
            $branchesOption=DB::table('gnr_branch')->where('id', $userBranchId)
                        ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                        ->pluck('nameWithCode', 'id')
                        ->all();
        }

        // echo "hello : $navValue";
        // exit();
        
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
            'navValue'   => $navValue, 
    		'navName'   => $navName, 
    		);

    	return view('microfin.reports.demoReportFilteringPart', $filteringArray);
    }

}
