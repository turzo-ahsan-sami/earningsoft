<?php

namespace App\Http\Controllers\microfin\loan;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\microfin\loan\MfnPurpose;
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

class MfnPurposeController extends controller {

	protected $MicroFinance;

	public function __construct() {

		$this->MicroFinance = New MicroFinance;

		$this->TCN = array(
			array('SL No.', 70), 
			array('Loan Purpose Category', 0),
			array('Name', 0),
			array('Loan Purpose Code', 0),
			array('Status', 70),
			array('Action', 80)
		);	
	}

	public function index() {

		$damageData = array(
			'TCN'	   				   =>  $this->TCN,
			'purpose'  				   =>  $this->MicroFinance->getActivePurpose(),
			'loanPurposeCategoryList'  =>  $this->MicroFinance->getLoanPurposeCategoryList()
		);

		return view('microfin.loan.purpose.viewPurpose', ['damageData' => $damageData]);
	}

	public function addPurpose() {

		$damageData = array(
			'loanPurposeCategoryList'  =>  $this->MicroFinance->getLoanPurposeCategoryList()
		);

		return view('microfin.loan.purpose.addPurpose', ['damageData' => $damageData]);
	}

		/**
		 * [Insert Loan Purpose Information]
		 * 
		 * @param Request $req
		 */
		public function addItem(Request $req) {

			$rules = array(
				'name'	  			   =>  'required|unique:mfn_loans_purpose,name',
				'code'	  			   =>  'required|unique:mfn_loans_purpose,code',
				'purposeCategoryIdFK'  =>  'required'
			);

			$attributesNames = array(
				'name'	  			   =>  'purpose name',
				'code'    			   =>  'purpose code',
				'purposeCategoryIdFK'  =>  'purpose category name'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);

				$create = MfnPurpose::create($req->all());

				$data = array(
					'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
					'responseText'   =>  MicroFinance::getMessage('purposeCreateSuccess'),
				);
				
				return response::json($data);
			}
		}

		public function updateRequest(Request $req) {

			$loanPurpose = MfnPurpose::select('id', 'name','code','purposeCategoryIdFK')->where('id',$req->id)->first();

			$data = array(
				'loanPurpose'  =>  $loanPurpose,
			);
			
			return response::json($data);
		}

		public function updateItem(Request $req) {

			$rules = array(
				'name'	  			   =>  'required',
				'code' 				   =>  'required',
				'purposeCategoryIdFK'  =>  'required',
			);

			$attributesNames = array(
				'name'	 			   =>  'loan purpose name',
				'code'	  		       =>  'loan purpose code',
				'purposeCategoryIdFK'  =>  'loan purpose category',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$purpose = MfnPurpose::find($req->id);
				$purpose->name = $req->name;
				$purpose->code = $req->code;
				$purpose->purposeCategoryIdFK = $req->purposeCategoryIdFK;
				$purpose->updatedDate = Carbon::now();
				$purpose->update();

				$data = array(
					'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
					'responseText'   =>  MicroFinance::getMessage('purposeUpdateSuccess'),
				);
				
				return response()->json($data);
			}
		}


		public function deleteItem(Request $req) {

			$isAssigned = (int) DB::table('mfn_loans_sub_purpose')
			->where('purposeIdFK',$req->id)
			->value('id');

			if ($isAssigned>0) {
				$data = array(
					'responseTitle' =>  'Warning!',
					'responseText'  =>  'You can not delete this bacause it is assinged to Loan Purpose.'
				);

				return response()->json($data);
			}


			MfnPurpose::find($req->id)->delete();

			
			$data = array(
				'responseTitle' =>  'Success!',
				'responseText'  =>  'Your selected Loan Purpose deleted successfully.'
			);

			return response()->json($data);
		}
	}