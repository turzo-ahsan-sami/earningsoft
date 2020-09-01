<?php

	namespace App\Http\Controllers\microfin\samity;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\samity\MfnSamityClosing;
	use App\microfin\samity\MfnSamity;
	use Session;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use App\Http\Controllers\Controller;
	use App\Http\Controllers\microfin\MicroFinance;
	use App\Http\Controllers\gnr\Service;
	use App;

	class MfnSamityClosingController extends Controller {

		protected $MicroFinance;

		private $TCN;
		private $samityType;
		private $samityDay;
		private $samityDaySuperScript;

		public function __construct() {

			$this->MicroFinance = new MicroFinance;

			$this->TCN = array(
				array('SL No.', 70), 
				array('Samity', 0),
				array('Code', 0),
				array('Branch', 0),
				array('Field Officer', 0),
				array('Samity Day', 0),
				array('Type', 0),
				array('Opening Date', 120),
				array('Closing Date', 120),
				array('Action', 80)
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

			$this->samityDaySuperScript = array(
				1 => 'st', 
				2 => 'nd', 
				3 => 'rd', 
				4 => 'th'
			);
		}

		public function index() {
			//$userBranchId = Auth::user()->branchId;

			// dd($userBranchId);

			$samityClosingList = MfnSamityClosing::all();

			//$branchIdArray = Service::getEngagedBranchesByUserId(Auth::user()->id);
			//$samityClosingList = MfnSamityClosing::whereIn('branchId', $branchIdArray)->get();


			// dd($samityClosingList);

			$TCN = $this->TCN;
			$samityType = $this->samityType;
			$samityDay = $this->samityDay;
			$samityDaySuperScript = $this->samityDaySuperScript;

			//	GET ALL THE FIELD OFFICERS LIST.
			$fieldOfficerList = DB::table('hr_emp_org_info')
					                ->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
					                ->where('hr_emp_org_info.position_id_fk', 61)
					                ->pluck('hr_emp_general_info.emp_name_english', 'hr_emp_general_info.id')
					                ->all(); 

			$damageData = array(
				'TCN'					=> $TCN,
				'samityClosingList'		=> $samityClosingList,
				'samityType' 	  		=> $samityType,
				'samityDay' 	  		=> $samityDay,
				'samityDaySuperScript'	=> $samityDaySuperScript,
				'fieldOfficerList'  	=> $fieldOfficerList,
				'MicroFinance'          => $this->MicroFinance,
			);

			// dd($damageData, $fieldOfficerList, $samityType, $samityDay);

			return view('microfin.samity.samityClosing.viewSamityClosing', ['damageData' => $damageData]);
		}

		public function addSamityClosing() {
			$userBranchId = Auth::user()->branchId;
			$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);


			$samities = MfnSamity::select('id', 'code', 'name', 'branchId')->active()->get();

			//	MANUFACTURING ACTIVE SAMITY LIST ARRAY.
			$samityList = array('' => 'Select');

            foreach($samities as $samity):
            	//if ($userBranchId == $samity->branchId) {
            	if (in_array($samity->branchId, $branchIdArray)) {
            		$samityList[$samity->id]     = $samity->code.' - '.$samity->name; 
            		// $samityListCode[$samity->id] = $samity->code;
            		// $samityList[$samity->id] = $samity->name; 
            	}
                // $samityList[$samity->id] = $samity->name; 
            endforeach;

			$damageData = array(
				'samityList'     =>	$samityList,
				// 'samityListCode' => $samityListCode
			);

			return view('microfin.samity.samityClosing.addSamityClosing', ['damageData' => $damageData]);
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: ADD SAMITY CLOSING CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function addItem(Request $req) {

			$rules = array(
				'samityId'	  => 'required|unique:mfn_samity_closing,samityId',
				'closingDate' => 'required',
			);

			$attributesNames = array(
				'samityId'	  => 'samity name',
				'closingDate' => 'samity closing date',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
				
				//	CHANGE THE OPENING DATE FORMAT.
				$closingDate = date_create($req->closingDate);
				$req->request->add(['closingDate' => date_format($closingDate, "Y-m-d")]);
				$create = MfnSamityClosing::create($req->all());

				$logArray = array(
				    'moduleId'  => 6,
				    'controllerName'  => 'MfnSamityClosingController',
				    'tableName'  => 'mfn_samity_closing',
				    'operation'  => 'insert',
				    'primaryIds'  => [DB::table('mfn_samity_closing')->max('id')]
				);
				Service::createLog($logArray);

				//	UPDATE SAMITY TABLE FOR CHANGE status to '0'
				//	FOR SAMITY CLOSED.
				$samity = MfnSamity::find($req->samityId);
				$samity->status = 0;
				$samity->softDel = 1;
				$samity->save();

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'Your selected samity has been closed successfully.',
					'create'		=> $create
				);
				
				return response::json($data);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: UPDATE SAMITY CLOSING CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function updateItem(Request $req) {

			$rules = array(
				'samityId'		=>	'mfn_samity_closing,samityId,'.$req->id,
				'closingDate'	=>	'required'
			);

			$attributesNames = array(
				'closingDate'	 =>	'samity closing date'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$samityClosing = MfnSamityClosing::find($req->id);
				$previousdata = $samityClosing; 

				//	CHANGE THE CLOSING DATE FORMAT.
				$closingDate = date_create($req->closingDate);
				$req->request->add(['closingDate' => date_format($closingDate, "Y-m-d")]);
				
				$samityClosing->closingDate = $req->closingDate;
				$samityClosing->save();
				$logArray = array(
		            'moduleId'  => 6,
		            'controllerName'  => 'MfnSamityClosingController',
		            'tableName'  => 'mfn_samity_closing',
		            'operation'  => 'update',
		            'previousData'  => $previousdata,
		            'primaryIds'  => [$req->id]
		        );
				Service::createLog($logArray);

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'Your selected samity closing information has been updated successfully.'
				);
				
				return response()->json($data);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: DELETE SAMITY CLOSING CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function deleteItem(Request $req) {
			$previousdata = MfnSamityClosing::find($req->id);
			$logArray = array(
		            'moduleId'  => 6,
		            'controllerName'  => 'MfnSamityClosingController',
		            'tableName'  => 'mfn_samity_closing',
		            'operation'  => 'delete',
		            'previousData'  => $previousdata,
		            'primaryIds'  => [$req->id]
		      );
			$previousdata->delete();

			Service::createLog($logArray);
			
			$data = array(
				'responseTitle' =>  'Success!',
				'responseText'  =>  'Your selected samity closing information deleted successfully.'
			);

			return response()->json($data);
		}
	}