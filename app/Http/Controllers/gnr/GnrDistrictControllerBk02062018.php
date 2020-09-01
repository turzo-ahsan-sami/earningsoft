<?php

	namespace App\Http\Controllers\gnr;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\gnr\GnrDistrict;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use App\Http\Controllers\Controller;

	class GnrDistrictController extends Controller {

		private $TCN;

		public function __construct() {

			$this->TCN = array(
				array('SL No.', 70),
				array('District', 0),
				array('Division', 0),
				array('Action', 100)
			);
		}

		public function index() {

			$districts = GnrDistrict::all();

			$TCN = $this->TCN;

			return view('gnr.tools.district.viewDistrict', ['districts' => $districts, 'TCN' => $TCN]);
		}

		public function addDistrict() {
dd('hello');
			return view('gnr.tools.district.addDistrict');
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: ADD DISTRICT CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function addItem(Request $req) {

			$rules = array(
				'name'		 =>	'required|unique:gnr_district,name',
				'divisionId' =>	'required'
			);

			$attributesNames = array(
				'name'		 =>	'district name',
				'divisionId' =>	'division name'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails())
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
				$create = GnrDistrict::create($req->all());

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'New district has been saved successfully.'
				);

				return response::json($data);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: UPDATE DISTRICT CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function updateItem(Request $req) {

			$rules = array(
				'name'		 =>	'required|unique:gnr_district,name,'.$req->id,
				'divisionId' =>	'required'
			);

			$attributesNames = array(
				'name'		 =>	'district name',
				'divisionId' =>	'division name'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails())
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$district = GnrDistrict::find($req->id);
				$district->name = $req->name;
				$district->divisionId = $req->divisionId;
				$district->save();

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'District has been updated successfully.'
				);

				return response()->json($data);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: DELETE DISTRICT CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function deleteItem(Request $req) {
			GnrDistrict::find($req->id)->delete();

			$data = array(
				'responseTitle' =>  'Success!',
				'responseText'  =>  'Your selected district deleted successfully.'
			);

			return response()->json($data);
		}
	}
