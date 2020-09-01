<?php

	namespace App\Http\Controllers\microfin\loan;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\loan\MfnLoanRepayPeriod;
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

	class MfnLoanRepayPeriodController extends controller {

		protected $MicroFinance;

		public function __construct() {

			$this->MicroFinance = New MicroFinance;

			$this->TCN = array(
				array('SL No.', 70), 
				array('Name', 0),
				array('In Months', 0),
				array('Status', 70),
				array('Action', 80)
			);	
		}

		public function index() {

			$damageData = array(
				'TCN'	   		   =>  $this->TCN,
				'loanRepayPeriod'  =>  $this->MicroFinance->getLoanRepayPeriodList()
			);

			return view('microfin.loan.loanRepayPeriod.viewLoanRepayPeriod', ['damageData' => $damageData]);
		}

		public function addLoanRepayPeriodForm() {

			return view('microfin.loan.loanRepayPeriod.addLoanRepayPeriod');
		}

		/**
		 * [Insert settings Grace Prioad Information]
		 * 
		 * @param Request $req
		 */
		public function addItem(Request $req) {

			$rules = array(
				'name'      =>  'required|unique:mfn_loan_repay_period,name',
				'inMonths'  =>  'required|unique:mfn_loan_repay_period,inMonths'
			);

			$attributesNames = array(
				'name'      =>  'Loan Repay Period name',
				'inMonths'  =>  'Loan Repay Period inMonth'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
			
				$create = MfnLoanRepayPeriod::create($req->all());

				$data = array(
					'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
					'responseText'   =>  MicroFinance::getMessage('loanRepayPeriodCreateSuccess'),
				);
				
				return response::json($data);
			}
		}

		public function updateRequest(Request $req) {

			$loanRepayPeriod = MfnLoanRepayPeriod::select('id', 'name','inMonths')->where('id', $req->id)->first();

			$data = array(
				'loanRepayPeriod'  =>  $loanRepayPeriod
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
				'name'  =>	'required|unique:mfn_loan_repay_period,name,'.$req->id,
			);
 
			$attributesNames = array(
				'name'  =>	'Loan Repay Period name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$loanRepayPeriod = MfnLoanRepayPeriod::find($req->id);
				$loanRepayPeriod->name = $req->name;
				$loanRepayPeriod->save();

				$data = array(
					'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
					'responseText'   =>  MicroFinance::getMessage('loanRepayPeriodUpdateSuccess'),
				);
				
				return response()->json($data);
			}
		}


		
	}