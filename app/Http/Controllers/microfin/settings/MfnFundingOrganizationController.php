<?php

	namespace App\Http\Controllers\microfin\settings;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\settings\MfnFundingOrganization;
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

	class MfnFundingOrganizationController extends controller {

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
				'funding'  =>  $this->MicroFinance->getActiveFundingOrganization()
			);

			return view('microfin.settings.fundingOrganization.viewFundingOrganization',['damageData' => $damageData]);
		}

		public function addFundingOrganization() {

			return view('microfin.settings.fundingOrganization.addFundingOrganization');
		}

		/**
		 * [Insert settings Funding Organization Information]
		 * 
		 * @param Request $req
		 */
		public function addItem(Request $req) {

			$rules = array(
				'name'  =>  'required|unique:mfn_funding_organization,name',
			);

			$attributesNames = array(
				'name'  =>  'Funding Organization name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
			
				$create = MfnFundingOrganization::create($req->all());

				$data = array(
					'responseTitle' =>  'Success!',
					'responseText'  =>  'Your new Funding Organization has been saved successfully.'
				);
				
				return response::json($data);
			}
		}

		public function updateRequest(Request $req) {

			$fundingOrganization = MfnFundingOrganization::select('id', 'name')->where('id', $req->id)->first();

			$data = array(
				'fundingOrganization'  =>  $fundingOrganization
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
				'name'  =>	'required|unique:mfn_funding_organization,name,'.$req->id,
			);
 
			$attributesNames = array(
				'name'  =>	'funding organization name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$fundingOrganization = MfnFundingOrganization::find($req->id);
				$fundingOrganization->name = $req->name;
				$fundingOrganization->save();

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'Funding Organization has been updated successfully.'
				);
				
				return response()->json($data);
			}
		}

		
	}