<?php

	namespace App\Http\Controllers\microfin\loan;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\loan\MfnInterestCalculationMethod;
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

	class MfnInterestCalculationMethodController extends Controller {

		protected $MicroFinance;

		public function __construct() {

			$this->MicroFinance = New MicroFinance;

			$this->TCN = array(
				array('SL No.', 70), 
				array('Name', 0),
				array('Short Name', 0),
				array('Status', 70),
				array('Action', 80)
			);	
		}

		public function index() {

			$damageData = array(
				'TCN'	           			 =>  $this->TCN,
				'interestCalculationMethod'  =>  $this->MicroFinance->getInterestCalculationMethod()
			);

			return view('microfin.loan.interestCalculationmMethod.viewInterestCalculationMethod', ['damageData' => $damageData]);
		}

		public function addLoanInterestCalculationMethod() {

			return view('microfin.loan.interestCalculationmMethod.addInterestCalculationMethod');
		}

		/**
		 * [Insert Loan Purpose Category Information]
		 * 
		 * @param Request $req
		 */
		public function addItem(Request $req) {

			$rules = array(
				'name'	  	 =>  'required|unique:mfn_loan_interest_calculation_method,name',
				'shortName'  =>  'required|unique:mfn_loan_interest_calculation_method,shortName',
			);

			$attributesNames = array(
				'name'	     =>  'interest calculation method name',
				'shortName'  =>  'interest calculation method short name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
			    $create = MfnInterestCalculationMethod::create($req->all());
                
                $data = array(
					'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
					'responseText'   =>  MicroFinance::getMessage('interestCalculationMethodCreateSuccess'),
				);
				
				return response::json($data);
			}
		}

		public function updateRequest(Request $req) {

			$interestCalculationMethod = MfnInterestCalculationMethod::select('id', 'name','shortName')->where('id', $req->id)->first();

			$data = array(
				'interestCalculationMethod'  =>  $interestCalculationMethod
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
				'name'       =>	 'required|unique:mfn_loan_interest_calculation_method,name,'.$req->id,
				'shortName'  =>  'required|unique:mfn_loan_interest_calculation_method,name,'.$req->id,
			);
 
			$attributesNames = array(
				'name'       =>	 'interest calculation method name',
				'shortName'  =>	 'interest calculation method short name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$interestCalculationMethod = MfnInterestCalculationMethod::find($req->id);
				$interestCalculationMethod->name = $req->name;
				$interestCalculationMethod->shortName = $req->shortName;
				$interestCalculationMethod->save();

				$data = array(
					'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
					'responseText'   =>  MicroFinance::getMessage('interestCalculationMethodUpdateSuccess'),
				);
				
				return response()->json($data);
			}
		}

		
	}