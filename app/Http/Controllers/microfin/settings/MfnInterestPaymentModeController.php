<?php

	namespace App\Http\Controllers\microfin\settings;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\settings\MfnInterestPaymentMode;
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

	class MfnInterestPaymentModeController extends controller {

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
				'interestPayment'  =>  $this->MicroFinance->getInterestPayment()
			);

			return view('microfin.settings.interestPaymentMode.viewInterestPaymentMode', ['damageData' => $damageData]);
		}

		public function addInterestPaymentModeForm() {

			return view('microfin.settings.interestPaymentMode.addInterestPaymentMode');
		}

		/**
		 * [Insert settings Funding Organization Information]
		 * 
		 * @param Request $req
		 */
		public function addItem(Request $req) {

			$rules = array(
				'name'  =>  'required|unique:mfn_interest_payment_mode,name',
			);

			$attributesNames = array(
				'name'  =>  'Interest Payment Mode name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
			
				$create = MfnInterestPaymentMode::create($req->all());

				$data = array(
					'responseTitle' =>  'Success!',
					'responseText'  =>  'Your new Interest Payment Mode has been saved successfully.'
				);
				
				return response::json($data);
			}
		}

		public function updateRequest(Request $req) {

			$interestPaymentMode = MfnInterestPaymentMode::select('id', 'name')->where('id', $req->id)->first();

			$data = array(
				'interestPaymentMode'  =>  $interestPaymentMode
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
				'name'  =>	'required|unique:mfn_interest_payment_mode,name,'.$req->id,
			);
 
			$attributesNames = array(
				'name'  =>	'Interest Payment Mode name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$interestPaymentMode = MfnInterestPaymentMode::find($req->id);
				$interestPaymentMode->name = $req->name;
				$interestPaymentMode->save();

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'Interest Payment Mode has been updated successfully.'
				);
				
				return response()->json($data);
			}
		}


		
	}