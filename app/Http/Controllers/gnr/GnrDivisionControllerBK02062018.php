<?php

	namespace App\Http\Controllers\gnr;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\gnr\GnrDivision;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use App\Http\Controllers\Controller;

	class GnrDivisionController extends Controller {

		private $TCN;

		public function __construct() {

			$this->TCN = array(
				array('SL No.', 70), 
				array('Division', 0),
				array('Action', 100)
			);
		}

		public function index() {

			$divisions = GnrDivision::all();
			
			$TCN = $this->TCN;

			return view('gnr.tools.division.viewDivision', ['divisions' => $divisions, 'TCN' => $TCN]);
		}

		public function addDivision() {

			return view('gnr.tools.division.addDivision');
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: ADD DIVISION CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function addItem(Request $req) {

			$rules = array(
				'name'		=>	'required|unique:gnr_division,name'
			);

			$attributesNames = array(
				'name'		=>	'division name'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
				$create = GnrDivision::create($req->all());

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'New division has been saved successfully.'
				);
				
				return response::json($data);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: UPDATE DIVISION CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function updateItem(Request $req) {

			$rules = array(
				'name'		 =>	'required|unique:gnr_division,name,'.$req->id,
			);

			$attributesNames = array(
				'name'		 =>	'division name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$division = GnrDivision::find($req->id);
				$division->name = $req->name;
				$division->save();

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'Division has been updated successfully.'
				);
				
				return response()->json($data);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: DELETE DIVISION CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function deleteItem(Request $req) {
			GnrDivision::find($req->id)->delete();
			
			$data = array(
				'responseTitle' =>  'Success!',
				'responseText'  =>  'Your selected division deleted successfully.'
			);

			return response()->json($data);
		}
	}