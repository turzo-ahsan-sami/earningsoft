<?php

	namespace App\Http\Controllers\microfin\settings;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\settings\MfnYearsEligibleWriteOff;
	use Session;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Auth;
	use App\Http\Controllers\Controller;
	use App\Http\Controllers\microfin\MicroFinance;

	class MfnYearsEligibleWriteOffController extends controller {

		protected $MicroFinance;

		public function __construct() {

			$this->MicroFinance = New MicroFinance;

			$this->TCN = array(
				array('SL No.', 70), 
				array('Name', 0),
				array('Status', 70),
				array('Action', 80)
			);	
		}

		public function index() {

			$damageData = array(
				'TCN'	   =>  $this->TCN,
				'yearsEligible'  =>  $this->MicroFinance->getActiveYearsEligibleWriteOff()
			);

			return view('microfin.settings.yearsEligibleWriteOff.viewYearsEligibleWriteOff', ['damageData' => $damageData]);
		}

		public function addYearEligableWriteOff() {

			return view('microfin.settings.yearsEligibleWriteOff.addYearsEligibleWriteOff');
		}

		/**
		 * [Insert settings Years Eligible Write Off Information]
		 * 
		 * @param Request $req
		 */
		public function addItem(Request $req) {

			$rules = array(
				'name'  =>  'required|unique:mfn_years_eligible_write_off,name',
			);

			$attributesNames = array(
				'name'  =>  'Years Eligible Write Off name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
			
				$create = MfnYearsEligibleWriteOff::create($req->all());

				$data = array(
					'responseTitle' =>  'Success!',
					'responseText'  =>  'Your new Years Eligible Write Off has been saved successfully.'
				);
				
				return response::json($data);
			}
		}


		public function updateRequest(Request $req) {

			$yearsEligible = MfnYearsEligibleWriteOff::select('id', 'name')->where('id', $req->id)->first();

			$data = array(
				'yearsEligible'  =>  $yearsEligible
			);
			
			return response::json($data);
		}

		/**
		 * [Update Funding Organization Information]
		 * 
		 * @param Request $req
		 */
		public function updateItem(Request $req) {

			$rules = array(
				'name'  =>	'required|unique:mfn_years_eligible_write_off,name,'.$req->id,
			);
 
			$attributesNames = array(
				'name'  =>	'years eligible write off name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$yearsEligible = MfnYearsEligibleWriteOff::find($req->id);
				$yearsEligible->name = $req->name;
				$yearsEligible->save();

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'years eligible write off name has been updated successfully.'
				);
				
				return response()->json($data);
			}

		
	}
}