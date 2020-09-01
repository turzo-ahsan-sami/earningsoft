<?php

namespace App\Http\Controllers\microfin\loan;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\microfin\loan\MfnPurposeCategory;
use App\Http\Controllers\gnr\Service;
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

class MfnPurposeCategoryController extends Controller {

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
			'TCN'	           =>  $this->TCN,
			'purposeCategory'  =>  $this->MicroFinance->getActivePurposeCategory()
		);

		return view('microfin.loan.purposeCategory.viewPurposeCategory', ['damageData' => $damageData]);
	}

	public function addPurposeCategory() {

		return view('microfin.loan.purposeCategory.addPurposeCategory');
	}

		/**
		 * [Insert Loan Purpose Category Information]
		 * 
		 * @param Request $req
		 */
		public function addItem(Request $req) {

			$rules = array(
				'name'  =>  'required|unique:mfn_loans_purpose_category,name',
			);

			$attributesNames = array(
				'name'  =>  'purpose category name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);

				$create = MfnPurposeCategory::create($req->all());

				$data = array(
					'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
					'responseText'   =>  MicroFinance::getMessage('purposeCategoryCreateSuccess'),
				);
				
				return response::json($data);
			}
		}

		public function updateRequest(Request $req) {

			$purposeCategory = MfnPurposeCategory::select('id', 'name')->where('id', $req->id)->first();

			$data = array(
				'purposeCategory'  =>  $purposeCategory
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
				'name'  =>	'required|unique:mfn_loans_purpose_category,name,'.$req->id,
			);

			$attributesNames = array(
				'name'  =>	'purpose category name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$purposeCategory = MfnPurposeCategory::find($req->id);
				$purposeCategory->name = $req->name;
				$purposeCategory->save();

				$data = array(
					'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
					'responseText'   =>  MicroFinance::getMessage('purposeCategoryUpdateSuccess'),
				);
				
				return response()->json($data);
			}
		}



		public function deleteItem(Request $req) {

			$isAssigned = (int) DB::table('mfn_loans_purpose')
			->where('purposeCategoryIdFK',$req->id)
			->value('id');

			if ($isAssigned>0) {
				$data = array(
					'responseTitle' =>  'Warning!',
					'responseText'  =>  'You can not delete this bacause it is assinged to Loan Purpose Category.'
				);

				return response()->json($data);
			}


			MfnPurposeCategory::find($req->id)->delete();

			
			$data = array(
				'responseTitle' =>  'Success!',
				'responseText'  =>  'Your selected Loan Purpose Category deleted successfully.'
			);

			return response()->json($data);
		}
	}