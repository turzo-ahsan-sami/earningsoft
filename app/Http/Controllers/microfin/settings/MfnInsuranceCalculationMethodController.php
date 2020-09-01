<?php

	namespace App\Http\Controllers\microfin\settings;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\settings\MfnInsuranceCalculationMethod;
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

	class MfnInsuranceCalculationMethodController extends controller {

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
				'insurance'  =>  $this->MicroFinance->getActiveInsuranceCalculation()
			);

			return view('microfin.settings.insuranceCalculationMethod.viewInsuranceCalculationMethod', ['damageData' => $damageData]);
		}

		public function addInsuranceCalculationMethod() {

		  return view('microfin.settings.insuranceCalculationMethod.addInsuranceCalculationMethod');
		}

		/**
		 * [Insert settings lnsurance Calculation Information]
		 * 
		 * @param Request $req
		 */
		public function addItem(Request $req) {

			$rules = array(
				'name'  =>  'required|unique:mfn_insurance_calculation_method,name',
			);

			$attributesNames = array(
				'name'  =>  'Insurance Calculation Method name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
			
				$create = MfnInsuranceCalculationMethod::create($req->all());

				$data = array(
					'responseTitle' =>  'Success!',
					'responseText'  =>  'Your new Insurance Calculation Method has been saved successfully.'
				);
				
				return response::json($data);
			}
		}

		public function updateRequest(Request $req) {

			$insuranceCalculation = MfnInsuranceCalculationMethod::select('id', 'name')->where('id', $req->id)->first();

			$data = array(
				'insuranceCalculation'  =>  $insuranceCalculation
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
				'name'  =>	'required|unique:mfn_insurance_calculation_method,name,'.$req->id,
			);
 
			$attributesNames = array(
				'name'  =>	'Insurance Calculation Method name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$insuranceCalculation = MfnInsuranceCalculationMethod::find($req->id);
				$insuranceCalculation->name = $req->name;
				$insuranceCalculation->save();

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'Insurance Calculation Method has been updated successfully.'
				);
				
				return response()->json($data);
			}
		}


		
	}