<?php

	namespace App\Http\Controllers\microfin\settings;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\settings\MfnRepaymentFrequency;
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

	class MfnRepaymentFrequencyController extends controller {

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
				'repaymentfrequency'  =>  $this->MicroFinance->getRepaymentFrequency()
			);

			return view('microfin.settings.repaymentFrequency.viewRepaymentFrequency', ['damageData' => $damageData]);
		}

		public function addRepaymentFrequencyForm() {

			return view('microfin.settings.repaymentFrequency.addRepaymentFrequency');
		}

		/**
		 * [Insert settings Repayment Frequency Information]
		 * 
		 * @param Request $req
		 */
		public function addItem(Request $req) {

			$rules = array(
				'name'  =>  'required|unique:mfn_repayment_frequency,name',
			);

			$attributesNames = array(
				'name'  =>  'Repayment Frequency name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
			
				$create = MfnRepaymentFrequency::create($req->all());

				$data = array(
					'responseTitle' =>  'Success!',
					'responseText'  =>  'Your new Repayment Frequency has been saved successfully.'
				);
				
				return response::json($data);
			}
		}

		public function updateRequest(Request $req) {

			$repaymentfrequency = MfnRepaymentFrequency::select('id', 'name')->where('id', $req->id)->first();

			$data = array(
				'repaymentfrequency'  =>  $repaymentfrequency
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
				'name'  =>	'required|unique:mfn_repayment_frequency,name,'.$req->id,
			);
 
			$attributesNames = array(
				'name'  =>	'repayment frequency name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$repaymentfrequency = MfnRepaymentFrequency::find($req->id);
				$repaymentfrequency->name = $req->name;
				$repaymentfrequency->save();

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'Repayment Frequency has been updated successfully.'
				);
				
				return response()->json($data);
			}
		}

		
	}