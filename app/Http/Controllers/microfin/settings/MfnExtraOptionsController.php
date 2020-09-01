<?php

	namespace App\Http\Controllers\microfin\settings;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\settings\MfnExtraOptions;
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

	class MfnExtraOptionsController extends controller {

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
				'extraOptions'  =>  $this->MicroFinance->getExtraOptions()
			);

			return view('microfin.settings.extraOptions.viewExtraOptions', ['damageData' => $damageData]);
		}

		public function addExtraOptionForm() {

			return view('microfin.settings.extraOptions.addExtraOptions');
		}

		/**
		 * [Insert settings Extra Option  Information]
		 * 
		 * @param Request $req
		 */
		public function addItem(Request $req) {

			$rules = array(
				'name'  =>  'required|unique:mfn_extra_options,name',
			);

			$attributesNames = array(
				'name'  =>  'Extra Option name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
			
				$create = MfnExtraOptions::create($req->all());

				$data = array(
					'responseTitle' =>  'Success!',
					'responseText'  =>  'Your new Extra option  has been saved successfully.'
				);
				
				return response::json($data);
			}
		}

		public function updateRequest(Request $req) {

			$extraOptions = MfnExtraOptions::select('id', 'name')->where('id', $req->id)->first();

			$data = array(
				'extraOptions'  =>  $extraOptions
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
				'name'  =>	'required|unique:mfn_extra_options,name,'.$req->id,
			);
 
			$attributesNames = array(
				'name'  =>	'extra option name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$extraOptions = MfnExtraOptions::find($req->id);
				$extraOptions->name = $req->name;
				$extraOptions->save();

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'Extra option has been updated successfully.'
				);
				
				return response()->json($data);
			}
		}


		
	}