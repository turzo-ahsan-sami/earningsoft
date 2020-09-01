<?php

	namespace App\Http\Controllers\microfin\samity;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\samity\MfnSamityFieldOfficerChange;
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
	use Illuminate\Support\Facades\Auth;

	class MfnSamityFieldOfficerChangeController extends Controller {

		protected $MicroFinance;

		private $TCN;

		public function __construct() {

			$this->MicroFinance = new MicroFinance;

			$this->TCN = array(
					array('SL No.', 70),
					array('Samity Name', 0),
					array('Previous Field Officer', 0),
					array('New Field Officer', 0),
					array('Effective Date', 0),
					array('Action', 80)
				);
		}

		public function index() {

			// $samityFieldOfficerChanges = new MfnSamityFieldOfficerChange;

			$samityFieldOfficerChanges = MfnSamityFieldOfficerChange::paginate(20);

			$TCN = $this->TCN;

			$damageData = array(
				'TCN' 	  					 =>  $TCN,
				'samityFieldOfficerChanges'	 =>  $samityFieldOfficerChanges,
				'MicroFinance'      		 =>  $this->MicroFinance
			);

			return view('microfin.samity.samityFieldOfficerChange.viewSamityFieldOfficerChange', ['damageData' => $damageData]);
		}

		public function addSamityFieldOfficerChange() {

			$samityList = $this->MicroFinance->getSamity();
			// dd($samityList);
            $damageData = array(
				'samityList'  =>  $samityList
			);

			return view('microfin.samity.samityFieldOfficerChange.addSamityFieldOfficerChange', ['damageData' => $damageData]);
		}

		public function getSamityByBranch (Request $req) {
			$getSamity = DB::table('mfn_samity')
				->where('branchId', $req->id)
				// ->select(DB::raw("CONCAT(code, ' - ', name) AS nameWithCode"), 'id')
				// ->get()
				// ->pluck('nameWithCode', 'id')
				// ->all();
				->select(DB::raw("CONCAT(code, ' - ', name) AS nameWithCode"), 'id')
				->get();

			return response()->json($getSamity);
		}

		public function getCurFieldOfficer(Request $req) {
			// dd($req);
			$curFieldOfficer = $this->MicroFinance->getFieldOfficerNameOptionOfSamity($req->id);

			if (Auth::user()->branchId != 1) {
				$newFieldOfficer = $this->MicroFinance->getFieldOfficerList();
			}
			else {
				$checkExistsFieldOfficer = DB::table('hr_emp_org_info')
					->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
					->where('hr_emp_org_info.branch_id_fk', $req->branchId)
					->count();

				if($checkExistsFieldOfficer>0):
					$newFieldOfficer = DB::table('hr_emp_org_info')
						->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
						->where('hr_emp_org_info.branch_id_fk', $req->branchId)
						->select(DB::raw("CONCAT(hr_emp_general_info.emp_name_english, ' - ', hr_emp_general_info.emp_id) AS nameWithCode"), 'hr_emp_general_info.id')
						->get()
						->pluck('nameWithCode', 'id')
						->all();
				else:
					$newFieldOfficer = [];
				endif;
			}

			//	REMOVE CURRENT FIELD OFFICER FROM THE NEW FIELD OFFICER LIST.
			$newFieldOfficer = array_diff($newFieldOfficer, $curFieldOfficer);

			$data = array(
				'curFieldOfficer' =>  $curFieldOfficer,
				'newFieldOfficer' =>  $newFieldOfficer
			);

			// dd($data);

			return response::json($data);
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: ADD SAMITY FIELD OFFICER CHANGE CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function addItem(Request $req) {

			$rules = array(
				'samityId'		 	 =>	'required',
				'fieldOfficerId' 	 =>	'required',
				'newFieldOfficerId'	 =>	'required',
				'effectiveDate'	 	 =>	'required'
			);

			$attributesNames = array(
				'samityId'		 	 =>	'samity name',
				'newFieldOfficerId'	 =>	'new field officer',
				'effectiveDate'	 	 =>	'effective date'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails())
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);

				//	CHANGE THE EFFECTIVE DATE FORMAT.
				$effectiveDate = date_create($req->effectiveDate);
				$req->request->add(['effectiveDate' => date_format($effectiveDate, "Y-m-d")]);
				$create = MfnSamityFieldOfficerChange::create($req->all());

				//	UPDATE FIELD OFFICER ID IN mfn_samity TABLE.
				$samity = MfnSamity::find($req->samityId);
				$samity->fieldOfficerId = $req->newFieldOfficerId;
				$samity->save();

				$data = array(
					'responseTitle' =>	'Success!',
					'responseText'  =>  'New field officer has been saved successfully.'
				);

				return response::json($data);
			}
		}
	}
