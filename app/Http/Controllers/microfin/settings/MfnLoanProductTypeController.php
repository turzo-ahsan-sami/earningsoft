<?php

	namespace App\Http\Controllers\microfin\settings;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\settings\MfnLoanProductType;
	use Session;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\File;
	use App\Http\Controllers\Controller;
	use App\Http\Controllers\microfin\MicroFinance;

	class MfnLoanProductTypeController extends controller {

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
				'loanProductType'  =>  $this->MicroFinance->getActiveLoanProductType()
			);

			return view('microfin.settings.loanProductType.viewLoanProductType', ['damageData' => $damageData]);
		}

		public function addLoanProductTypeForm() {

			return view('microfin.settings.loanProductType.addLoanProductType');
		}

		/**
		 * [Insert settings Loan Product Type Information]
		 * 
		 * @param Request $req
		 */
		public function addItem(Request $req) {

			$rules = array(
				'name'  =>  'required|unique:mfn_loan_product_type,name',
			);

			$attributesNames = array(
				'name'  =>  'Loan product type name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
			
				$create = MfnLoanProductType::create($req->all());

				$data = array(
					'responseTitle' =>  'Success!',
					'responseText'  =>  'Your new Loan Product Type has been saved successfully.',
				);
				
				return response::json($data);
			}
		}

		public function updateRequest(Request $req) {

			$loanProductType = MfnLoanProductType::select('id', 'name')->where('id', $req->id)->first();

			$data = array(
				'loanProductType'  =>  $loanProductType
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
				'name'  =>	'required|unique:mfn_loan_product_type,name,'.$req->id,
			);
 
			$attributesNames = array(
				'name'  =>	'Loan Product name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$loanProductType = MfnLoanProductType::find($req->id);
				$loanProductType->name = $req->name;
				$loanProductType->save();

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'Loan Product has been updated successfully.'
				);
				
				return response()->json($data);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: DELETE LOAN PRODUCT TYPE.
		|--------------------------------------------------------------------------
		*/
		public function deleteItem(Request $req) {

			//	CHECK IF THERE IS ANY LOAN PRODUCT TYPE IS IN USED.
			$lockDelete = $this->MicroFinance->checkLoanProductTypeUsed($req->id); 

			if($lockDelete==0):
				MfnLoanProductType::find($req->id)->delete();
			endif;

			$data = array(
				'responseTitle' =>  $lockDelete==0?MicroFinance::getMessage('msgSuccess'):MicroFinance::getMessage('msgWarning'),
				'responseText'  =>  $lockDelete==0?MicroFinance::getMessage('loanProductTypeDelSuccess'):MicroFinance::getMessage('loanProductTypeDelFailed'),
			);

			return response()->json($data);
		}
		
	}