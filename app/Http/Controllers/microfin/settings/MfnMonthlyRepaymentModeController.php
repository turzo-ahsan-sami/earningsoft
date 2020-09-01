<?php

	namespace App\Http\Controllers\microfin\settings;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\settings\MfnMonthlyRepaymentMode;
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

	class MfnMonthlyRepaymentModeController extends controller {

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
				'monthlyRepayment'  =>  $this->MicroFinance->getMonthlyRepaymentMode()
			);

			return view('microfin.settings.monthlyRepaymentMode.viewMonthlyRepaymentMode', ['damageData' => $damageData]);
		}

		public function addMonthlyRepaymentModeForm() {

			return view('microfin.settings.monthlyRepaymentMode.addMonthlyRepaymentMode');
		}

		/**
		 * [Insert settings Monthly Repayment Information]
		 * 
		 * @param Request $req
		 */
		public function addItem(Request $req) {

			$rules = array(
				'name'  =>  'required|unique:mfn_monthly_repayment_mode,name',
			);

			$attributesNames = array(
				'name'  =>  'Monthly Repayment name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
			
				$create = MfnMonthlyRepaymentMode::create($req->all());

				$data = array(
					'responseTitle' =>  'Success!',
					'responseText'  =>  'Your new Monthly Repayment has been saved successfully.'
				);
				
				return response::json($data);
			}
		}

		public function updateRequest(Request $req) {

			$monthlyRepayment = MfnMonthlyRepaymentMode::select('id', 'name')->where('id', $req->id)->first();

			$data = array(
				'monthlyRepayment'  =>  $monthlyRepayment
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
				'name'  =>	'required|unique:mfn_monthly_repayment_mode,name,'.$req->id,
			);
 
			$attributesNames = array(
				'name'  =>	'Monthly Repayment name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$monthlyRepayment = MfnMonthlyRepaymentMode::find($req->id);
				$monthlyRepayment->name = $req->name;
				$monthlyRepayment->save();

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'Monthly Repayment has been updated successfully.'
				);
				
				return response()->json($data);
			}
		}


		
	}