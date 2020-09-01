<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\gnr\GnrArea;
use App\Http\Controllers\gnr\Service;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class GnrAreaController extends Controller {

	public function index() {
		
		$areas = GnrArea::all();
		$tableColName = array(
			'SL No.', 
			'Area', 
			'Code', 
			'Branches', 
			'Action'
		);
		
		return view('gnr.tools.area.viewArea', ['areas' => $areas, 'tableColName' => $tableColName]);
	}

	public function addArea() {

		return view('gnr.tools.area.addArea');
	}

		/*
		|--------------------------------------------------------------------------
		| GNR: ADD AREA CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function addItem(Request $req) {

			$rules = array(
				'name'		=>	'required',
				'code'		=>	'required',
				'branchId'	=>	'required'				
			);

			$attributesNames = array(
				'name'		=>	'area name',
				'code'		=>	'area code',
				'branchId'	=>	'branch'	
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
				$create = GnrArea::create($req->all());
				$logArray = array(
					'moduleId'  => 7,
					'controllerName'  => 'GnrAreaController',
					'tableName'  => 'gnr_area',
					'operation'  => 'insert',
					'primaryIds'  => [DB::table('gnr_area')->max('id')]
				);
				Service::createLog($logArray);
				return response::json(['responseText' => 'Data successfully saved.'], 200);
			}
		}

		public function updateItem(Request $req) {

			$rules = array(
				'name' 		=> 'required|unique:gnr_area,name,'.$req->id,
				'code'		=> 'required',
				'branchId'	=> 'required'				
			);

			$attributesNames = array(
				'name'		=>	'area name',
				'code'		=>	'area code',
				'branchId'	=>	'branch'	
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$previousdata = GnrArea::find($req->id);
				$areaNewAssign = GnrArea::find($req->id);
				$areaNewAssign->name = $req->name;
				$areaNewAssign->code = $req->code;
				$areaNewAssign->branchId = $req->branchId;


				$checkBranchAvailability = GnrArea::all();
				$branchIdArr = json_decode($checkBranchAvailability);

				foreach($branchIdArr as $branchId):
					$branchId = get_object_vars($branchId);
					
					$formattedBranchId = array();
					
					foreach($req->branchId as $comBranchId):
						$checkBranchIdArr = array_diff($req->branchId, array(0));
						//$comBranchId  = get_object_vars($comBranchId);
						
						for($j=0;$j<count($checkBranchIdArr);$j++):
							$i = 0;
							if(in_array($checkBranchIdArr[$j], $branchId['branchId'])):
								//$updatedBranchId = array_diff($branchId['branchId'], array($checkBranchIdArr[$j]));

								//$ccc = array("2", "3", "4", "5", "62", "63");
								//$ddd = array("62", "63", "64", "65");

								$updatedBranchId = array_diff($branchId['branchId'], $checkBranchIdArr);
								//$updatedBranchId = array_diff($ddd, $ccc);
								
								//if(count($updatedBranchId)==0)
								//	$updatedBranchId = $checkBranchIdArr;

								$k = 0;
								foreach($updatedBranchId as $arr):
									$formattedBranchId[$k] =  $arr;
									$k++;
								endforeach;

								
								$areaWithdrawAssign = GnrArea::find($branchId['id']);
								$areaWithdrawAssign->name = $branchId['name'];
								$areaWithdrawAssign->code = $branchId['code'];
								$areaWithdrawAssign->branchId = $formattedBranchId;
								$areaWithdrawAssign->save();
								$i++;
								break;
							endif;
						endfor;


						/*if(in_array($comBranchId, $branchId['branchId'])):
							$updatedBranchId = array_diff($branchId['branchId'], array($comBranchId));
							
							foreach($updatedBranchId as $arr):
								$formattedBranchId[] =  $arr;
							endforeach;

							if($i==count($req->branchId)-1):
								$areaWithdrawAssign = GnrArea::find($branchId['id']);
								$areaWithdrawAssign->name = $branchId['name'];
								$areaWithdrawAssign->code = $branchId['code'];
								$areaWithdrawAssign->branchId = $formattedBranchId;
								$areaWithdrawAssign->save();
							endif;

						endif;
						$i++;*/
					endforeach;
				endforeach;

				$areaNewAssign->save();

				$data = array(
					'area'		   =>	$areaNewAssign,
					'responseText' =>   $req->branchId
					//'responseText' =>   $branchId['branchId']
					//'responseText' =>  'Data successfully updated.'
				);
				$logArray = array(
					'moduleId'  => 7,
					'controllerName'  => 'GnrAreaController',
					'tableName'  => 'gnr_area',
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
		| GNR: AREA DELETE CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function deleteItem(Request $req) {
			
			$areaUsedCheck = DB::table('gnr_zone')->select('areaId')->get();
			$areaUsedCheck = json_decode($areaUsedCheck, true);

			$areaUsed = 0;
			
			foreach($areaUsedCheck as $areaUsedId):
				$areaUsedArr = json_decode($areaUsedId['areaId'], true);
				if(in_array($req->id, $areaUsedArr)):
					$areaUsed = 1;
					break;
				endif;
			endforeach;

			if($areaUsed==0):
				$previousdata=GnrArea::find($req->id);
				GnrArea::find($req->id)->delete();
				$responseTitle = 'Success!';
				$responseText = 'Your selected area deleted successfully.';
			else:
				$responseTitle = 'Warning!';
				$responseText = 'You are not permitted to delete this area.<br/> It is used in another zone.';
			endif;
			
			$data = array(
				'responseTitle' =>  $responseTitle,
				'responseText'  =>  $responseText
			);

			$logArray = array(
				'moduleId'  => 7,
				'controllerName'  => 'GnrAreaController',
				'tableName'  => 'gnr_area',
				'operation'  => 'delete',
				'previousData'  => $previousdata,
				'primaryIds'  => [$previousdata->id]
			);
			Service::createLog($logArray);

			return response()->json($data);
		}

		/*
		|--------------------------------------------------------------------------
		| GNR: CHECK AREA ASSIGN CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function checkAreaAssign(Request $req) {

			$checkBranchAvailability = DB::table('gnr_area')->select('branchId')->get();
			$branchIdArr = json_decode($checkBranchAvailability, true);

			$alreadyAssign = 0;

			foreach($branchIdArr as $branchId):
				$branchId = json_decode($branchId['branchId'], true);
				
				if(in_array($req->getBranchId, $branchId)):
					$alreadyAssign = 1;
					break;
				else:
					$alreadyAssign = 0;
				endif;
			endforeach;

			$data = array(
				'alreadyAssign' =>  $alreadyAssign,
				'responseTitle' =>  'Warning!',
				'responseText'  =>  'This branch is already assigned to another Area.'
			);

			return response()->json($data, 200);
		}

		public function assignPermitted(Request $req) {

			$checkBranchAvailability = GnrArea::all();
			$branchIdArr = json_decode($checkBranchAvailability);

			$alreadyAssign = 0;
			foreach($branchIdArr as $branchId):
				$branchId = get_object_vars($branchId);
				
				if(in_array($req->getBranchId, $branchId['branchId'])):
					$alreadyAssign = 1;

					for($i=0;$i<count($branchId['branchId']);$i++):
						if($req->getBranchId==$branchId['branchId'][$i]):
							unset($branchId['branchId'][$i]);

							$area = GnrArea::find($branchId['id']);
							$area->name = $branchId['name'];
							$area->code = $branchId['code'];
							$area->branchId = $branchId['branchId'];
							$area->save();
							break;
						endif;
					endfor;
					break;
				endif;
			endforeach;

			return response()->json(['responseText' => $branchId['id']], 200);
		}
	}