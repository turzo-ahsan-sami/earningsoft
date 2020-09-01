<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\gnr\GnrDivision;
use App\Http\Controllers\gnr\Service;
use Validator;
use Response;
use DB;
use Route;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class GnrDivisionController extends Controller {

	private $TCN;
	public $route;

	public function __construct() {

		$this->route = $this->layoutDynamic();
		$this->TCN = array(
			array('SL No.', 70), 
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

		$divisions = GnrDivision::all();
		
		$TCN = $this->TCN;

		return view('gnr.tools.address.division.viewDivision', ['route'=>$this->route, 'divisions' => $divisions, 'TCN' => $TCN]);
	}

	public function addDivision() {
		//dd();

		return view('gnr.tools.address.division.addDivision', ['route'=>$this->route]);
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

				$logArray = array(
					'moduleId'  => 7,
					'controllerName'  => 'GnrDivisionController',
					'tableName'  => 'gnr_division',
					'operation'  => 'insert',
					'primaryIds'  => [DB::table('gnr_division')->max('id')]
				);
				//dd($logArray);

				Service::createLog($logArray);
				

				
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
				$previousdata=GnrDivision::find($req->id);
				$division = GnrDivision::find($req->id);
				$division->name = $req->name;
				$division->save();



				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'Division has been updated successfully.'
				);
				
				$logArray = array(
					'moduleId'  => 7,
					'controllerName'  => 'GnrDivisionController',
					'tableName'  => 'gnr_division',
					'operation'  => 'update',
					'previousData'  => $previousdata,
					'primaryIds'  => [$previousdata->id]
				);
				//dd($logArray);

				Service::createLog($logArray);
				return response()->json($data);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: DELETE DIVISION CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function deleteItem(Request $req) {

			$isAssigned = (int) DB::table('gnr_district')
			->where('division_id',$req->id)
			->value('id');

			if ($isAssigned>0) {
				$data = array(
					'responseTitle' =>  'Warning!',
					'responseText'  =>  'You can not delete this bacause it is assinged to District.'
				);

				return response()->json($data);
			}

			$previousdata=GnrDivision::find($req->id);
			GnrDivision::find($req->id)->delete();
			$logArray = array(
				'moduleId'  => 7,
				'controllerName'  => 'GnrDivisionController',
				'tableName'  => 'gnr_division',
				'operation'  => 'delete',
				'previousData'  => $previousdata,
				'primaryIds'  => [$previousdata->id]
			);
				//dd($logArray);

			Service::createLog($logArray);
			
			$data = array(
				'responseTitle' =>  'Success!',
				'responseText'  =>  'Your selected division deleted successfully.'
			);

			return response()->json($data);
		}
	}