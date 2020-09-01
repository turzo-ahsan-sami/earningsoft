<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\gnr\GnrZone;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class GnrZoneController extends Controller {

	private $TCN;

	public function __construct() {

		$this->TCN = array(
			array('SL No.', 70), 
			array('Zone', 100),
			array('Code', 100),
			array('Areas', 0),
			array('Action', 100)
		);
	}

	public function index() {

		$zones = GnrZone::all();

		$TCN = $this->TCN;

		return view('gnr.tools.zone.viewZone', ['zones' => $zones, 'TCN' => $TCN]);
	}

	public function addZone() {

		return view('gnr.tools.zone.addZone');
	}

		/*
		|--------------------------------------------------------------------------
		| GNR: ADD ZONE CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function addItem(Request $req) {

			$rules = array(
				'name'		=>	'required|unique:gnr_zone,name',
				'code'		=>	'required|unique:gnr_zone,code',
				'areaId'	=>	'required'
			);

			$attributesNames = array(
				'name'		=>	'zone name',
				'code'		=>	'zone code',
				'areaId'	=>	'area'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
				$create = GnrZone::create($req->all());
				$logArray = array(
					'moduleId'  => 7,
					'controllerName'  => 'GnrZoneController',
					'tableName'  => 'gnr_zone',
					'operation'  => 'insert',
					'primaryIds'  => [DB::table('gnr_zone')->max('id')]
				);
				Service::createLog($logArray);
				return response::json(['responseText' => 'Data successfully saved.'], 200);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: ZONE UPDATE CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function updateItem(Request $req) {

			$rules = array(
				'name' 		=> 'required|unique:gnr_zone,name,'.$req->id,
				'code'		=> 'required',
				'areaId'	=> 'required'				
			);

			$attributesNames = array(
				'name'		=>	'zone name',
				'code'		=>	'zone code',
				'areaId'	=>	'area'	
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$zoneNewAssign = GnrZone::find($req->id);
				$previousdata = GnrZone::find($req->id);
				$zoneNewAssign->name = $req->name;
				$zoneNewAssign->code = $req->code;
				$zoneNewAssign->areaId = $req->areaId;


				$checkAreaAvailability = GnrZone::all();
				$areaIdArr = json_decode($checkAreaAvailability);

				foreach($areaIdArr as $areaId):
					$areaId = get_object_vars($areaId);
					
					$formattedAreaId = array();
					
					foreach($req->areaId as $comAreaId):
						$checkAreaIdArr = array_diff($req->areaId, array(0));
						
						for($j=0;$j<count($checkAreaIdArr);$j++):
							$i = 0;
							if(in_array($checkAreaIdArr[$j], $areaId['areaId'])):
								$updatedAreaId = array_diff($areaId['areaId'], $checkAreaIdArr);
								
								$k = 0;
								foreach($updatedAreaId as $arr):
									$formattedAreaId[$k] =  $arr;
									$k++;
								endforeach;

								$zoneWithdrawAssign = GnrZone::find($areaId['id']);
								$zoneWithdrawAssign->name = $areaId['name'];
								$zoneWithdrawAssign->code = $areaId['code'];
								$zoneWithdrawAssign->areaId = $formattedAreaId;
								$zoneWithdrawAssign->save();
								$i++;
								break;
							endif;
						endfor;
					endforeach;
				endforeach;

				$zoneNewAssign->save();

				$data = array(
					'area'		   =>	$zoneNewAssign,
					'responseText' =>   $req->areaId
					//'responseText' =>   $areaId['areaId']
					//'responseText' =>  'Data successfully updated.'
				);
				$logArray = array(
					'moduleId'  => 7,
					'controllerName'  => 'GnrZoneController',
					'tableName'  => 'gnr_zone',
					'operation'  => 'update',
					'previousData'  => $previousdata,
					'primaryIds'  => [$previousdata->id]
				);
				Service::createLog($logArray);
				
				return response()->json($data);
				




				/*$zone = GnrZone::find($req->id);
				$zone->name = $req->name;
				$zone->code = $req->code;
				$zone->areaId = $req->areaId;
				$zone->save();

				$data = array(
					'zone'	=>	$zone
				);
				
				return response()->json($data);*/
			}
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: ZONE DELETE CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function deleteItem(Request $req) {
			
			$zoneUsedCheck = DB::table('gnr_region')->select('zoneId')->get();
			$zoneUsedCheck = json_decode($zoneUsedCheck, true);

			$zoneUsed = 0;
			
			foreach($zoneUsedCheck as $zoneUsedId):
				$zoneUsedIdArr = json_decode($zoneUsedId['zoneId'], true);
				if(in_array($req->id, $zoneUsedIdArr)):
					$zoneUsed = 1;
					break;
				endif;
			endforeach;

			if($zoneUsed==0):
				$previousdata=GnrZone::find($req->id);
				GnrZone::find($req->id)->delete();
				$responseTitle = 'Success!';
				$responseText = 'Your selected zone deleted successfully.';
			else:
				$responseTitle = 'Warning!';
				$responseText = 'You are not permitted to delete this zone.<br/> It is used in another region.';
			endif;
			
			$data = array(
				'responseTitle' =>  $responseTitle,
				'responseText'  =>  $responseText
			);
			$logArray = array(
				'moduleId'  => 7,
				'controllerName'  => 'GnrZoneController',
				'tableName'  => 'gnr_zone',
				'operation'  => 'delete',
				'previousData'  => $previousdata,
				'primaryIds'  => [$previousdata->id]
			);
			Service::createLog($logArray);

			return response()->json($data);
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: CHECK ZONE ASSIGN CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function checkZoneAssign(Request $req) {

			$checkAreaAvailability = DB::table('gnr_zone')->select('areaId')->get();
			$areaIdArr = json_decode($checkAreaAvailability, true);

			$alreadyAssign = 0;

			foreach($areaIdArr as $areaId):
				$areaId = json_decode($areaId['areaId'], true);
				
				if(in_array($req->getAreaId, $areaId)):
					$alreadyAssign = 1;
					break;
				else:
					$alreadyAssign = 0;
				endif;
			endforeach;

			$data = array(
				'alreadyAssign' =>  $alreadyAssign,
				'responseTitle' =>  'Warning!',
				'responseText'  =>  'This area is already assigned to another zone.'
			);

			return response()->json($data, 200);
		}

		public function assignPermitted(Request $req) {

			$checkAreaAvailability = GnrZone::all();
			$areaIdArr = json_decode($checkAreaAvailability);

			$alreadyAssign = 0;
			foreach($areaIdArr as $areaId):
				$areaId = get_object_vars($areaId);

				if(in_array($req->getAreaId, $areaId['areaId'])):
					$alreadyAssign = 1;

					for($i=0;$i<count($areaId['areaId']);$i++):
						if($req->getAreaId==$areaId['areaId'][$i]):
							unset($areaId['areaId'][$i]);

							$zone = GnrZone::find($areaId['id']);
							$zone->name = $areaId['name'];
							$zone->code = $areaId['code'];
							$zone->areaId = $areaId['areaId'];
							$zone->save();
							break;
						endif;
					endfor;
					break;
				endif;
			endforeach;

			return response()->json(['responseText' => $areaId['id']], 200);
		}

	}
