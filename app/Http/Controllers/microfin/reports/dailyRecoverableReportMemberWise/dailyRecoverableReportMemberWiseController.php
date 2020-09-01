<?php

	namespace App\Http\Controllers\microfin\reports\dailyRecoverableReportMemberWise;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\loan\MfnLoan;
	use App\microfin\loan\MfnProduct;
	use App\microfin\loan\MfnLoanSchedule;
	use App\microfin\loan\MfnLoanReschedule;
	use App\microfin\loan\MfnGracePeriod;
	use App\microfin\loan\MfnFees;
	use App\microfin\settings\MfnLoanProductInterestRate;
	use App\microfin\configuration\openingBalance\MfnloanOpeningBalance;
	use Session;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Auth;
	use App\Http\Controllers\Controller;
	use App\Traits\GetSoftwareDate;
	use App\Http\Controllers\microfin\MicroFinance;

	class dailyRecoverableReportMemberWiseController extends Controller {

		protected $MicroFinance;
		
		use GetSoftwareDate;

		public function __construct() {

			$this->MicroFinance = New MicroFinance;

			$this->TCN = array(
				array('field-worker' => array('ID', 'name')), 
				array('samity' => array('code', 'name')), 
				array('member' => array('ID', 'name')), 
				array('component' => array()),
				array('savings-deposit' => array('cash', 'bank', 'total')), 
				array('interest-on-savings' => 'interest-on-savings'),
				array('savings-refund' => array('cash', 'bank', 'total')), 
				array('disbursement-amount' => array()),
				array('loan-recoverable' => array()),
				array('loan-collection' => array('regular', 'due', 'advance', 'rebate', 'total-collection' => array('loan-pri' => array('cash', 'bank'), 'interrest' => array('cash', 'bank'), 'total'))), 
			);	
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: DAILY RECOVERABLE REPORT (MEMBER WISE)
		|--------------------------------------------------------------------------
		*/
		public function index(Request $req) {

			

			$damageData = array(
				'TCN'               	  =>  $this->TCN,
				'branch'  		    	  =>  $this->MicroFinance->getAllBranchOptions(),
				'primaryProductCategory'  =>  $this->MicroFinance->getProductCategoryList(), 
				'MicroFinance'      	  =>  $this->MicroFinance
			);

			return view('microfin.reports.dailyRecoverableReportMemberWise.dailyRecoverableReportMemberWise', $damageData);
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: LOAD FIELD OFFICER OPTION.
		|--------------------------------------------------------------------------
		*/
		public function loadFieldOfficerOption(Request $req) {

			$data = array(
				'fieldOfficer'  =>  $this->MicroFinance->getFieldOfficerOptionBranchWise($req->branchId)
			);

			return response::json($data); 
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: LOAD SAMITY OPTION.
		|--------------------------------------------------------------------------
		*/
		public function loadSamityOption(Request $req) {

			$data = array(
				'samity'  =>  $this->MicroFinance->getFieldOfficerWiseSamityOptions($req->fieldOfficerId),
			);

			return response::json($data); 
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: LOAD PRIMARY PRODUCT OPTION.
		|--------------------------------------------------------------------------
		*/
		public function loadPrimaryProductOption(Request $req) {

			$data = array(
				'primaryProduct'  =>  $this->MicroFinance->getBranchAndCategoryWiseActiveLoanPrimaryProductOptions($req->branchId, $req->productCategoryId),
			);

			return response::json($data); 
		}

		public function loadReport(Request $req) {

			$fieldOfficer = $this->MicroFinance->getFieldOfficerListBranchWiseFORR($req->branchId);
			$samityOB = [];
			$memberOB = [];
			$isFieldOfficerSet = 0;
			$isSamitySet = 0;

			if($req->has('fieldOfficerId')):
				$fieldOfficerListOB = (array) $this->MicroFinance->getMultipleValueForId($table='hr_emp_general_info', $req->fieldOfficerId, ['id', 'emp_id', 'emp_name_english']);
				
				$fieldOfficer = [];
				array_push($fieldOfficer, $fieldOfficerListOB);

				$samityOB = $this->MicroFinance->getSamityListFieldOfficerWiseFORR($req->fieldOfficerId);
				$memberOB = $this->MicroFinance->getMemberListSamityWiseFORR($req->samityId);	
				$isFieldOfficerSet = 1;	
			endif;

			if($req->has('samityId')):
				$samityListOB = (array) $this->MicroFinance->getMultipleValueForId($table='mfn_samity', $req->samityId, ['id', 'code', 'name']);
				
				$samityOB = [];
				array_push($samityOB, $samityListOB);
				$isSamitySet = 1;
			endif;

			$data = array(
				'TCN'  			    =>  $this->TCN,
				'primaryProduct'  	=>  $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductShortNameOptions($req->branchId),
				'branchId'  	    =>  $req->branchId,
				'fieldOfficerId'    =>  $req->fieldOfficerId,
				//'samityId'    		=>  $req->samityId,
				'fieldOfficerList'  =>  $fieldOfficer,
				'samityList'  		=>  $samityOB,
				'memberList'  		=>  $memberOB,
				'isFieldOfficerSet' =>	$isFieldOfficerSet,
				'isSamitySet'		=>	$isSamitySet,
				'MicroFinance'      =>  $this->MicroFinance
			);

			return view('microfin.reports.dailyRecoverableReportMemberWise.dailyRecoverableReportMemberWiseTPL', $data);
		}

		

		
		
	}