<?php

	namespace App\Http\Controllers\microfin\samity;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\samity\MfnSamityTransfer;
	use App\microfin\samity\MfnSamity;
	use Session;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use App\Http\Controllers\Controller;
	use App\Http\Controllers\microfin\MicroFinance;

	class MfnSamityTransferController extends Controller {

		protected $MicroFinance;

		private $TCN;
		private $TCNDTLS;
		private $samityType;
		private $samityDay;

		public function __construct() {

			$this->MicroFinance = New MicroFinance;

			$this->TCN = array(
				array('SL No.', 70), 
				array('Samity Name', 0),
				array('Samity Code', 120),
				array('Branch', 0),
				array('New Branch', 0),
				array('Transfer Date', 120),
				array('Status', 70),
				array('Action', 80)
			);

			$this->TCNDTLS = array(
				'Samity', 
				'Branch',
				'Code', 
				'Type', 
				'Samity Day', 
				'Total Member',
				'Field Officer' 
			);

			$this->samityType = array(
				'1' => 'Male', 
				'2' => 'Female',
				'3' => 'All'
			);

			$this->samityDay = array(
				1 => 'Saturday', 
				2 => 'Sunday', 
				3 => 'Monday', 
				4 => 'Tuesday', 
				5 => 'Wednesday', 
				6 => 'Thursday', 
				7 => 'Friday'
			);
		}

		public function index() {

			$samityTransfers = MfnSamityTransfer::all();
			
			$TCN = $this->TCN;

			$damageData = array(
				'TCN' 			  => $TCN,
				'samityTransfers' => $samityTransfers
			);

			return view('microfin.samity.samityTransfer.viewSamityTransfer', ['damageData' => $damageData]);
		}

		public function addSamityTransfer() {

			$damageData = array(
				'samities'  =>  $this->MicroFinance->getSamity()
			);

			return view('microfin.samity.samityTransfer.addSamityTransfer', ['damageData' => $damageData]);
		}

		public function loadSamityListByBranch() {

			$create = 1000;
			
			$data = array(
				'responseTitle' =>   'Success!',
				'responseText'  =>   'New samity has been saved successfully.',
				'create'		=>   $create
			);

			return response::json($data); 
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: SINGLE SAMITY DETAILS FOR TRANSFER CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function singleSamityDetailsForTransfer(Request $req) {

			$samityType = $this->samityType;
			$samityDay = $this->samityDay;

			$getSingleSamityDetails = DB::table('mfn_samity')
										  ->select('name', 'branchId', 'code', 'samityTypeId', 'samityDayId', 'maxNumber', 'fieldOfficerId')
										  ->where('id', $req->id)->first();

			$currentBranchId = $getSingleSamityDetails->branchId;
			$getBranchName = DB::table('gnr_branch')->select('name')->where('id', $currentBranchId)->first();
			$getSamityType = $samityType[$getSingleSamityDetails->samityTypeId];
			$getSamityDay = $samityDay[$getSingleSamityDetails->samityDayId];
			$getFieldOfficerName = DB::table('hr_emp_general_info')
									   ->select('emp_id', 'emp_name_english')
									   ->where('id', $getSingleSamityDetails->fieldOfficerId)
									   ->first();

			$getSingleSamityDetails->branchId = $getBranchName->name;
			$getSingleSamityDetails->samityTypeId = $getSamityType;
			$getSingleSamityDetails->samityDayId = $getSamityDay;
			$getSingleSamityDetails->fieldOfficerId = $getFieldOfficerName->emp_name_english .' ('. $getFieldOfficerName->emp_id . ')';
			
			
			//	CREATE THE BRANCH LIST EXCEPT CURRENT BRANCH.
			//$branchList = array('' => 'Select') + DB::table('gnr_branch')->pluck('name','id')->all();

			$TCNDTLS = $this->TCNDTLS;

			$data = array(
				'responseTitle'   => 'Success!',
				'responseText'    => 'New samity has been saved successfully.',
				'currentBranchId' => $currentBranchId,
				'create'		  => $getSingleSamityDetails,
				'TCNDTLS'		  => $TCNDTLS
			);

			return response::json($data); 
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: ADD SAMITY TRANSFER CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function addItem(Request $req) {

			$rules = array(
				'samityId'	   =>	'required',
				'newBranchId'  =>	'required',
				'transferDate' =>	'required',
			);

			$attributesNames = array(
				'samityId'	   =>	'samity name',
				'newBranchId'  =>	'new branch',
				'transferDate' =>	'transfer date',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);

				//	GET THE CODE OF SAMITY.
				$getSamityCodeOBJ = DB::table('mfn_samity')->select('code')->where('id', $req->samityId)->first();
				$req->request->add(['samityCode' => $getSamityCodeOBJ->code]);
				
				//	CHANGE THE TRANSFER DATE FORMAT.
				$transferDate = date_create($req->transferDate);
				$req->request->add(['transferDate' => date_format($transferDate, "Y-m-d")]);
				$create = MfnSamityTransfer::create($req->all());

				$logArray = array(
				    'moduleId'  => 6,
				    'controllerName'  => 'MfnSamityTransferController',
				    'tableName'  => 'mfn_samity_transfer',
				    'operation'  => 'insert',
				    'primaryIds'  => [DB::table('mfn_samity_transfer')->max('id')]
				);
				Service::createLog($logArray);

				
				//	UPDATE THE BRANCH INFORMATION OF THE TRANSFERRED SAMITY.
				$samity = MfnSamity::find($req->samityId);
				$samity->branchId = $req->newBranchId;
				
				//	GET THE NEW BRANCH CODE. 
				$getBranchCodeOBJ = DB::table('gnr_branch')->select('branchCode')->where('id', $req->newBranchId)->first();
				$samity->branchCode = $getBranchCodeOBJ->branchCode;

				//	GET THE SAMITY SL NO. OF TRANSFERRED SAMITY.
				$samitySL = DB::table('mfn_samity')->where('branchId', $req->newBranchId)->max('samitySL') + 1;
				$samity->code = sprintf('%03d', $samity->branchCode) . '.' . sprintf('%04d', $samitySL);
				$samity->samitySL = $samitySL;

				$samity->save();
				

				$data = array(
					'responseTitle' => 'Success!',
					'responseText'  => 'Your selected samity has been transferred successfully.',
					'create'		=> $create
				);
				
				return response::json($data);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: DELETE SAMITY TRANSFER CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function deleteItem(Request $req) {
			

			//MfnSamityTransfer::find($req->id)->delete();

			$previousdata = MfnSamityTransfer::find($req->id);
			$logArray = array(
	            'moduleId'  => 6,
	            'controllerName'  => 'MfnSamityTransferController',
	            'tableName'  => 'mfn_samity_transfer',
	            'operation'  => 'delete',
	            'previousData'  => $previousdata,
	            'primaryIds'  => [$req->id]
	        );
		    $previousdata->delete();    
			Service::createLog($logArray);
			
			$data = array(
				'responseTitle' =>  'Success!',
				'responseText'  =>  'Your selected samity transfer information deleted successfully.'
			);

			return response()->json($data);
		}
	}