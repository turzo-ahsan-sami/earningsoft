<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\gnr\GnrDistrict;
use Validator;
use Response;
use DB;
use Route;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class GnrDistrictController extends Controller {

	private $TCN;
	public $route;

	public function __construct() {

		$this->route = $this->layoutDynamic();
		$this->TCN = array(
			array('SL No.', 70),
			array('District', 0),
			array('Division', 0),
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

		$districts = GnrDistrict::all();

		$TCN = $this->TCN;

		return view('gnr.tools.address.district.viewDistrict', ['route'=>$this->route, 'districts' => $districts, 'TCN' => $TCN]);
	}

	public function addDistrict() {

		return view('gnr.tools.address.district.addDistrict', ['route'=>$this->route]);
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
				$logArray = array(
					'moduleId'  => 7,
					'controllerName'  => 'GnrDistrictController',
					'tableName'  => 'gnr_district',
					'operation'  => 'insert',
					'primaryIds'  => [DB::table('gnr_district')->max('id')]
				);
				Service::createLog($logArray);

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
				$previousdata = GnrDistrict::find ($req->id);
				$district = GnrDistrict::find($req->id);
				$district->name = $req->name;
				$district->divisionId = $req->divisionId;
				$district->save();

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'District has been updated successfully.'
				);
				$logArray = array(
					'moduleId'  => 7,
					'controllerName'  => 'GnrDistrictController',
					'tableName'  => 'gnr_district',
					'operation'  => 'update',
					'previousData'  => $previousdata,
					'primaryIds'  => [$previousdata->id]
				);
				Service::createLog($logArray);

				return response()->json($data);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: DELETE DISTRICT CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function deleteItem(Request $req) {

			$isAssigned = (int) DB::table('gnr_upzilla')
			->where('districtId',$req->id)
			->value('id');

			if ($isAssigned>0) {
				$data = array(
					'responseTitle' =>  'Warning!',
					'responseText'  =>  'You can not delete this bacause it is assinged to Thana/Upazila.'
				);

				return response()->json($data);
			}

			$previousdata=GnrDistrict::find($req->id);
			GnrDistrict::find($req->id)->delete();

			$data = array(
				'responseTitle' =>  'Success!',
				'responseText'  =>  'Your selected district deleted successfully.'
			);
			$logArray = array(
				'moduleId'  => 7,
				'controllerName'  => 'GnrDistrictController',
				'tableName'  => 'gnr_district',
				'operation'  => 'delete',
				'previousData'  => $previousdata,
				'primaryIds'  => [$previousdata->id]
			);
			Service::createLog($logArray);

			return response()->json($data);
		}
	}
