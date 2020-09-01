<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\gnr\GnrWorkingArea;
use Validator;
use Response;
use DB;
use Route;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\microfin\MicroFinance;

class GnrWorkingAreaController extends Controller {

	protected $MicroFinance;

	private $TCN;
	public $route;

	public function __construct() {

		$this->route = $this->layoutDynamic();

		$this->MicroFinance = new MicroFinance;

		$this->TCN = array(
			array('SL No.', 70),
			array('Working Area', 120),
				//array('Code', 100),
			array('Branch', 100),
			array('Division', 0),
			array('District', 0),
			array('Upazila', 0),
			array('Union', 0),
			array('Village', 0),
			array('Action', 0)
		);
	}

	public function layoutDynamic() {

		$path = Route::current()->action['prefix'];

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

		$workingAreas = $this->MicroFinance->getAllWorkingArea(Auth::user()->branchId);

		$TCN = $this->TCN;

		return view('gnr.tools.address.workingArea.viewWorkingArea', ['route'=>$this->route, 'workingAreas' => $workingAreas, 'TCN' => $TCN]);
	}

	public function addWorkingArea() {

		if(Auth::user()->branchId==1):
			$branch = $this->MicroFinance->getAllBranchOptions();
		else:
			$branch = $this->MicroFinance->getDefaultBranchOptions(Auth::user()->branchId);
		endif;

		$damageData = array(
			'branch'    =>  $branch,
			'division'  =>  $this->MicroFinance->getAllDivisionOptions()
		);

		return view('gnr.tools.address.workingArea.addWorkingArea', ['route'=>$this->route, 'damageData' => $damageData]);
	}

	public function loadDistrict(Request $req) {

		$data = array(
			'district'  =>  $this->MicroFinance->getDistrictOptions($req->divisionId)
		);

		return response()->json($data);
	}

	public function loadUpzilla(Request $req) {

		$data = array(
			'upzilla'  =>  $this->MicroFinance->getUpzillaOptions($req->divisionId, $req->districtId)
		);

		return response()->json($data);
	}

	public function loadUnion(Request $req) {

		$data = array(
			'union'  =>  $this->MicroFinance->getUnionOptions($req->divisionId, $req->districtId, $req->upazilaId)
		);

		return response()->json($data);
	}

	public function loadVillage(Request $req) {

		$data = array(
			'village'  =>  $this->MicroFinance->getVillageOptions($req->divisionId, $req->districtId, $req->upazilaId, $req->unionId)
		);

		return response()->json($data);
	}

		/*
		|--------------------------------------------------------------------------
		| GNR: ADD WORKING AREA CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function addItem(Request $req) {

			$rules = array(
				'name'		 =>	'required|unique:gnr_working_area,name',
				//'code'		 =>	'required|unique:gnr_working_area,code',
				'branchId'	 =>	'required',
				'divisionId' =>	'required',
				'districtId' =>	'required',
				'upazilaId'	 =>	'required',
				'unionId'	 =>	'required',
				'villageId'	 =>	'required'
			);

			$attributesNames = array(
				'name'		 =>	'working area name',
				//'code'		 =>	'working code name',
				'branchId'	 =>	'branch name',
				'divisionId' =>	'division name',
				'districtId' =>	'district name',
				'upazilaId'	 =>	'upazila name',
				'unionId'	 =>	'union name',
				'villageId'	 =>	'village name'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails())
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
				$create = GnrWorkingArea::create($req->all());
				$logArray = array(
					'moduleId'  => 7,
					'controllerName'  => 'GnrWorkingAreaController',
					'tableName'  => 'gnr_working_area',
					'operation'  => 'insert',
					'primaryIds'  => [DB::table('gnr_working_area')->max('id')]
				);
				Service::createLog($logArray);
				return response::json(['responseText' => 'New working area has been saved successfully.'], 200);
			}
		}

		public function loadDistrictUpzillaUnionVillage(Request $req) {

			$data = array(
				'district' =>  $this->MicroFinance->getDistrictOptions($req->divisionId),
				'upzilla'  =>  $this->MicroFinance->getUpzillaOptions($req->divisionId, $req->districtId),
				'union'    =>  $this->MicroFinance->getUnionOptions($req->divisionId, $req->districtId, $req->upazilaId),
				'village'  =>  $this->MicroFinance->getVillageOptions($req->divisionId, $req->districtId, $req->upazilaId, $req->unionId)
			);

			return response()->json($data);
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: UPDATE WORKING AREA CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function updateItem(Request $req) {

			$rules = array(
				'name'		 =>	'required|unique:gnr_working_area,name,'.$req->id,
				//'code'		 =>	'required',
				'branchId'	 =>	'required',
				'divisionId' =>	'required',
				'districtId' =>	'required',
				'upazilaId'	 =>	'required',
				'unionId'	 =>	'required',
				'villageId'	 =>	'required'
			);

			$attributesNames = array(
				'name'		 =>	'working area name',
				//'code'		 =>	'working code name',
				'branchId'	 =>	'branch name',
				'divisionId' =>	'division name',
				'districtId' =>	'district name',
				'upazilaId'	 =>	'upazila name',
				'unionId'	 =>	'union name',
				'villageId'	 =>	'village name'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails())
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$previousdata = GnrWorkingArea::find ($req->id);
				$workingArea = GnrWorkingArea::find($req->id);
				$workingArea->name = $req->name;
				//$workingArea->code = $req->code;
				$workingArea->branchId = $req->branchId;
				$workingArea->divisionId = $req->divisionId;
				$workingArea->districtId = $req->districtId;
				$workingArea->upazilaId = $req->upazilaId;
				$workingArea->unionId = $req->unionId;
				$workingArea->villageId = $req->villageId;
				$workingArea->save();

				$data = array(
					'responseText' =>   'Working area has been updated successfully.'
				);

				$logArray = array(
					'moduleId'  => 7,
					'controllerName'  => 'GnrWorkingAreaController',
					'tableName'  => 'gnr_working_area',
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
		| GNR: DELETE WORKING AREA CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function deleteItem(Request $req) {

			$isAssigned = (int) DB::table('mfn_samity')
			->where('workingAreaId', $req->id)
			->value('id');
			if ($isAssigned > 0) {
				$data = array(
					'responseTitle' => 'Warning!',
					'responseText'  => 'You can not delete this because it is assign to Samity.'
				);

				return response()->json($data);
			}

			$previousdata=GnrWorkingArea::find($req->id);
			GnrWorkingArea::find($req->id)->delete();

			$data = array(
				'responseTitle' =>  'Success!',
				'responseText'  =>  'Your selected working area deleted successfully.'
			);

			$logArray = array(
				'moduleId'  => 7,
				'controllerName'  => 'GnrWorkingAreaController',
				'tableName'  => 'gnr_working_area',
				'operation'  => 'delete',
				'previousData'  => $previousdata,
				'primaryIds'  => [$previousdata->id]
			);
			Service::createLog($logArray);

			return response()->json($data);
		}
	}
