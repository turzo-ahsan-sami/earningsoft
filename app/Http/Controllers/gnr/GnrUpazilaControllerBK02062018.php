<?php

	namespace App\Http\Controllers\gnr;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\gnr\GnrUpazila;
	use Validator;
	use Response;
	use DB;
	use Route;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use App\Http\Controllers\Controller;

	class GnrUpazilaController extends Controller {

		private $TCN;
		public $route;

		public function __construct() {

			$this->route = $this->layoutDynamic();
			$this->TCN = array(
				array('SL No.', 70), 
				array('Upazila', 0),
				array('Division', 0),
				array('District', 0),
				array('Action', 100)
			);
		}

		public function layoutDynamic() {

            $path = Route::current()->action['prefix'];

            // dd($path);

            if($path=='/mfn') {
                $layout = 'layouts/microfin_layout';
            } elseif($path == '/acc') {
                $layout = 'layouts/acc_layout';
            } elseif($path == '/gnr'){
                $layout = 'layouts/gnr_layout';
            } elseif($path == '/inv'){
                $layout = 'layouts/inventory_layout';
            } elseif($path == '/fams'){
                $layout = 'layouts/fams_layout';
            } elseif($path == '/pos'){
                $layout = 'layouts/pos_layout';
            }

            $route = array(
                'layout'        => $layout,
                'path'          => $path
            );

            // dd($route);

            return $route;

        }

		public function index() {

			$upazilas = GnrUpazila::all();
			
			$TCN  = $this->TCN;

			return view('gnr.tools.upazila.viewUpazila', ['route'=>$this->route, 'upazilas' => $upazilas, 'TCN' => $TCN]);
		}

		public function addUpazila() {

			return view('gnr.tools.upazila.addUpazila', ['route'=>$this->route]);
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: ADD UPAZILA CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function addItem(Request $req) {

			$rules = array(
				'name'		 =>	'required|unique:gnr_upzilla,name',
				'divisionId' =>	'required',
				'districtId' =>	'required'
			);

			$attributesNames = array(
				'name'		 =>	'upazila name',
				'divisionId' =>	'division name',
				'districtId' =>	'district name'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
				$create = GnrUpazila::create($req->all());

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'New upazila has been saved successfully.'
				);
				
				return response::json($data);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: UPDATE UPAZILA CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function updateItem(Request $req) {

			$rules = array(
				'name'		 =>	'required|unique:gnr_upzilla,name,'.$req->id,
				'divisionId' =>	'required',
				'districtId' =>	'required'
			);

			$attributesNames = array(
				'name'		 =>	'upazila name',
				'divisionId' =>	'division name',
				'districtId' =>	'district name'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$upazila = GnrUpazila::find($req->id);
				$upazila->name = $req->name;
				$upazila->divisionId = $req->divisionId;
				$upazila->districtId = $req->districtId;
				$upazila->save();

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'Upazila has been updated successfully.'
				);
				
				return response()->json($data);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: DELETE UPAZILA CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function deleteItem(Request $req) {
			GnrUpazila::find($req->id)->delete();
			
			$data = array(
				'responseTitle' =>  'Success!',
				'responseText'  =>  'Your selected upazila deleted successfully.'
			);

			return response()->json($data);
		}
	}