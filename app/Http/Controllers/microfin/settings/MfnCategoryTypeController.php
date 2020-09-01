<?php

	namespace App\Http\Controllers\microfin\settings;

	use Illuminate\Http\Request;
	use App\Http\Requests;
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
	use App\microfin\settings\MfnCategoryType;

	class MfnCategoryTypeController extends Controller {

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
				'TCN'	        =>  $this->TCN,
				'categoryType'  =>  $this->MicroFinance->getActiveCategoryType()
			);

			return view('microfin.settings.categoryType.viewCategoryType', ['damageData' => $damageData]);
		}

		public function addSettingsCategoryType() {

			return view('microfin.settings.categoryType.addCategoryType');
		}

		public function addItem(Request $req) {

			$rules = array(
				'name'	  => 'required|unique:mfn_loans_purpose_category,name',
			);

			$attributesNames = array(
				'name'	  => 'purpose category name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
			
				$create = MfnCategoryType::create($req->all());

				$data = array(
					'responseTitle' =>  'Success!',
					'responseText'  =>  'Your new category type has been saved successfully.'
				);
				
				return response::json($data);
			}
			
		}

		public function updateRequest(Request $req) {

			$categoryType = MfnCategoryType::select('id', 'name')->where('id', $req->id)->first();

			$data = array(
				'categoryType'  =>  $categoryType
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
				'name'  =>	'category type name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$categoryType = MfnCategoryType::find($req->id);
				$categoryType->name = $req->name;
				$categoryType->save();

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'category type has been updated successfully.'
				);
				
				return response()->json($data);
			}
		}

	}