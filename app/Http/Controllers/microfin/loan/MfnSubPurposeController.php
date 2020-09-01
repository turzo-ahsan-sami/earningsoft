<?php

namespace App\Http\Controllers\microfin\loan;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\microfin\loan\MfnSubPurpose;
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

class MfnSubPurposeController extends Controller {

	protected $MicroFinance;

	public function __construct() {

		$this->MicroFinance = New MicroFinance;

		$this->TCN = array(
			array('SL No.', 70), 
			array('Name', 0),
			array('Code', 200),
			array('Purpose Name', 0),
			array('Status', 70),
			array('Action', 80)
		);	
	}

	public function index() {

		$damageData = array(
			'TCN'	      	   =>  $this->TCN,
			'subPurpose'  	   =>  $this->MicroFinance->getActiveSubPurpose(),
			'loanPurposeList'  =>  $this->MicroFinance->getLoanPurposeList()
		);

		return view('microfin.loan.subPurpose.viewSubPurpose', ['damageData' => $damageData]);
	}

	public function addSubPurpose() {

		$damageData = array(
			'loanPurposeList'  =>  $this->MicroFinance->getLoanPurposeList()
		);

		return view('microfin.loan.subPurpose.addSubPurpose', ['damageData' => $damageData]);
	}

		/**
		 * [Insert Loan Sub Purpose Information]
		 * 
		 * @param Request $req
		 */
		public function addItem(Request $req) {

			$rules = array(
				// 'name'	  	   =>  'required|unique:mfn_loans_sub_purpose,name',
				'name'	  	   =>  'required',
				'code'	  	   =>  'required|unique:mfn_loans_sub_purpose,code',
				'purposeIdFK'  =>  'required'
			);

			$attributesNames = array(
				'name'	  	   =>  'sub purpose name',
				'code'    	   =>  'sub purpose code',
				'purposeIdFK'  =>  'purpose name'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
				
				$create = MfnSubPurpose::create($req->all());

				$data = array(
					'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
					'responseText'   =>  MicroFinance::getMessage('subPurposeCreateSuccess'),
				);
				
				return response::json($data);
			}
		}

		public function updateRequest(Request $req) {

			$loanSubPurpose = MfnSubPurpose::select('id', 'name','code','purposeIdFK')->where('id',$req->id)->first();

			$data = array(
				'loanSubPurpose'  =>  $loanSubPurpose,
			);
			
			return response::json($data);
		}

		public function updateItem(Request $req) {

			$rules = array(
				'name'	  	   =>  'required',
				'code' 		   =>  'required',
				'purposeIdFK'  =>  'required',
			);
			
			$attributesNames = array(
				'name'	 	   =>  'loan purpose name',
				'code'	  	   =>  'loan purpose code',
				'purposeIdFK'  =>  'loan purpose category',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$subPurpose = MfnSubPurpose::find($req->id);
				$subPurpose->name = $req->name;
				$subPurpose->code = $req->code;
				$subPurpose->purposeIdFK = $req->purposeIdFK;
				$subPurpose->updatedDate = Carbon::now();
				$subPurpose->update();

				$data = array(
					'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
					'responseText'   =>  MicroFinance::getMessage('subPurposeUpdateSuccess'),
				);
				
				return response()->json($data);
			}
		}



		public function deleteItem(Request $req) {

			$isAssigned = (int) DB::table('mfn_loan')
			->where('loanSubPurposeIdFk',$req->id)
			->value('id');

			if ($isAssigned>0) {
				$data = array(
					'responseTitle' =>  'Warning!',
					'responseText'  =>  'You can not delete this bacause it is assinged to Loan Sub Purpose.'
				);

				return response()->json($data);
			}


			MfnSubPurpose::find($req->id)->delete();

			
			$data = array(
				'responseTitle' =>  'Success!',
				'responseText'  =>  'Your selected Loan Sub Purpose Purpose deleted successfully.'
			);

			return response()->json($data);
		}
	}