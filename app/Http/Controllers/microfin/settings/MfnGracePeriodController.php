<?php

	namespace App\Http\Controllers\microfin\settings;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\settings\MfnGracePeriod;
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

	class MfnGracePeriodController extends controller {

		protected $MicroFinance;

		public function __construct() {

			$this->MicroFinance = New MicroFinance;

			$this->TCN = array(
				array('SL No.', 70), 
				array('Name', 0),
				array('In Days', 0),
				array('Status', 70),
				array('Action', 80)
			);	
		}

		public function index() {

			$damageData = array(
				'TCN'	   =>  $this->TCN,
				'gracePrioad'  =>  $this->MicroFinance->getGracePeriod()
			);

			return view('microfin.settings.gracePeriod.viewGracePeriod', ['damageData' => $damageData]);
		}

		public function addGracePeriodForm() {

			return view('microfin.settings.gracePeriod.addGracePeriod');
		}

		/**
		 * [Insert settings Grace Prioad Information]
		 * 
		 * @param Request $req
		 */
		public function addItem(Request $req) {

			$rules = array(
				'name'  =>  'required|unique:mfn_grace_period,name',
				'inDays'=>  'required|unique:mfn_grace_period,inDays'
			);

			$attributesNames = array(
				'name'  =>  'Grace Period name',
				'inDays'=>  'Grace Period in days',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
			
				$create = MfnGracePeriod::create($req->all());

				$data = array(
					'responseTitle' =>  'Success!',
					'responseText'  =>  'Your new Grace Period has been saved successfully.'
				);
				
				return response::json($data);
			}
		}

		public function updateRequest(Request $req) {

			$gracePrioad = MfnGracePeriod::select('id', 'name','inDays')->where('id', $req->id)->first();

			$data = array(
				'gracePrioad'  =>  $gracePrioad
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
				'name'  =>	'required|unique:mfn_grace_period,name,'.$req->id,
				'inDays'  =>	'required|unique:mfn_grace_period,inDays,'.$req->id,
			);
 
			$attributesNames = array(
				'name'  =>	'grace period name',
				'inDays'  =>	'grace period in Days',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$gracePrioad = MfnGracePeriod::find($req->id);
				$gracePrioad->name = $req->name;
				$gracePrioad->inDays = $req->inDays;
				$gracePrioad->save();

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'Grace Period has been updated successfully.'
				);
				
				return response()->json($data);
			}
		}

	}