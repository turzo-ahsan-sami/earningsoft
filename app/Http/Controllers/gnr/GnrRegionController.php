<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\gnr\GnrRegion;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class GnrRegionController extends Controller {

	private $TCN;

	public function __construct() {

		$this->TCN = array(
			array('SL No.', 70), 
			array('Region', 100),
			array('Code', 100),
			array('Zones', 0),
			array('Action', 100)
		);
	}

	public function index() {

		$regions = GnrRegion::all();

		$TCN = $this->TCN;

		return view('gnr.tools.region.viewRegion', ['regions' => $regions, 'TCN' => $TCN]);
	}

	public function addRegion() {

		return view('gnr.tools.region.addRegion');
	}

		/*
		|--------------------------------------------------------------------------
		| GNR: ADD REGION CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function addItem(Request $req) {

			$rules = array(
				'name'		=>	'required|unique:gnr_region,name',
				'code'		=>	'required|unique:gnr_region,code',
				'zoneId'	=>	'required'
			);

			$attributesNames = array(
				'name'		=>	'region name',
				'code'		=>	'region code',
				'zoneId'	=>	'zone'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
				$create = GnrRegion::create($req->all());
				$logArray = array(
					'moduleId'  => 7,
					'controllerName'  => 'GnrRegionController',
					'tableName'  => 'gnr_region',
					'operation'  => 'insert',
					'primaryIds'  => [DB::table('gnr_region')->max('id')]
				);
				Service::createLog($logArray);
				return response::json(['responseText' => 'Data successfully saved.'], 200);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: REGION UPDATE CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function updateItem(Request $req) {

			$rules = array(
				'name' 		=> 'required|unique:gnr_region,name,'.$req->id,
				'code'		=> 'required',
				'zoneId'	=> 'required'				
			);

			$attributesNames = array(
				'name'		=>	'region name',
				'code'		=>	'region code',
				'zoneId'	=>	'zone'	
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$previousdata = GnrRegion::find ($req->id);
				$regionNewAssign = GnrRegion::find($req->id);
				$regionNewAssign->name = $req->name;
				$regionNewAssign->code = $req->code;
				$regionNewAssign->zoneId = $req->zoneId;


				$checkRegionAvailability = GnrRegion::all();
				$zoneIdArr = json_decode($checkRegionAvailability);

				foreach($zoneIdArr as $zoneId):
					$zoneId = get_object_vars($zoneId);
					
					$formattedZoneId = array();
					
					foreach($req->zoneId as $comZoneId):
						$checkZoneIdArr = array_diff($req->zoneId, array(0));
						
						for($j=0;$j<count($checkZoneIdArr);$j++):
							$i = 0;
							if(in_array($checkZoneIdArr[$j], $zoneId['zoneId'])):
								$updatedZoneId = array_diff($zoneId['zoneId'], $checkZoneIdArr);
								
								$k = 0;
								foreach($updatedZoneId as $arr):
									$formattedZoneId[$k] =  $arr;
									$k++;
								endforeach;

								$regionWithdrawAssign = GnrRegion::find($zoneId['id']);
								$regionWithdrawAssign->name = $zoneId['name'];
								$regionWithdrawAssign->code = $zoneId['code'];
								$regionWithdrawAssign->zoneId = $formattedZoneId;
								$regionWithdrawAssign->save();
								$i++;
								break;
							endif;
						endfor;
					endforeach;
				endforeach;

				$regionNewAssign->save();

				$data = array(
					'area'		   =>	$regionNewAssign,
					'responseText' =>   $req->zoneId
					//'responseText' =>   $zoneId['zoneId']
					//'responseText' =>  'Data successfully updated.'
				);
				$logArray = array(
					'moduleId'  => 7,
					'controllerName'  => 'GnrRegionController',
					'tableName'  => 'gnr_region',
					'operation'  => 'update',
					'previousData'  => $previousdata,
					'primaryIds'  => [$previousdata->id]
				);
				Service::createLog($logArray);
				
				return response()->json($data);
				




				/*$region = GnrRegion::find($req->id);
				$region->name = $req->name;
				$region->code = $req->code;
				$region->zoneId = $req->zoneId;
				$region->save();

				$data = array(
					'region'	=>	$region
				);
				
				return response()->json($data);*/
			}
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: REGION DELETE CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function deleteItem(Request $req) {
			
			$data = array(
				'responseTitle' =>  'Success!',
				'responseText'  =>  'Your selected region deleted successfully.'
			);
			$previousdata=GnrRegion::find($req->id);

			GnrRegion::find($req->id)->delete();
			$logArray = array(
				'moduleId'  => 7,
				'controllerName'  => 'GnrRegionController',
				'tableName'  => 'gnr_region',
				'operation'  => 'delete',
				'previousData'  => $previousdata,
				'primaryIds'  => [$previousdata->id]
			);
			Service::createLog($logArray);
			return response()->json($data, 200);
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: CHECK REGION ASSIGN CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function checkRegionAssign(Request $req) {

			$checkzoneAvailability = DB::table('gnr_region')->select('zoneId')->get();
			$zoneIdArr = json_decode($checkzoneAvailability, true);

			$alreadyAssign = 0;

			foreach($zoneIdArr as $zoneId):
				$zoneId = json_decode($zoneId['zoneId'], true);
				
				if(in_array($req->getZoneId, $zoneId)):
					$alreadyAssign = 1;
					break;
				else:
					$alreadyAssign = 0;
				endif;
			endforeach;

			$data = array(
				'alreadyAssign' =>  $alreadyAssign,
				'responseTitle' =>  'Warning!',
				'responseText'  =>  'This zone is already assigned to another region.'
			);

			return response()->json($data, 200);
		}

		public function assignPermitted(Request $req) {

			$checkZoneAvailability = GnrRegion::all();
			$zoneIdArr = json_decode($checkZoneAvailability);

			$alreadyAssign = 0;
			foreach($zoneIdArr as $zoneId):
				$zoneId = get_object_vars($zoneId);

				if(in_array($req->getZoneId, $zoneId['zoneId'])):
					$alreadyAssign = 1;

					for($i=0;$i<count($zoneId['zoneId']);$i++):
						if($req->getZoneId==$zoneId['zoneId'][$i]):
							unset($zoneId['zoneId'][$i]);

							$region = GnrRegion::find($zoneId['id']);
							$region->name = $zoneId['name'];
							$region->code = $zoneId['code'];
							$region->zoneId = $zoneId['zoneId'];
							$region->save();
							break;
						endif;
					endfor;
					break;
				endif;
			endforeach;

			return response()->json(['responseText' => $zoneId['id']], 200);
		}
		
	}