<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\GnrUserRole;
use App\gnr\GnrRole;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use App\Http\Controllers\microfin\MicroFin;

class GnrUserRoleController extends Controller
{

	public function index(Request $req)
	{

		$empUserIds = DB::table('hr_emp_general_info AS t1')
		->join('users AS t2', 't1.id', 't2.emp_id_fk')
		->pluck('t2.id')
		->toArray();

		$userRoleLists = DB::table('gnr_user_role')->whereIn('userIdFK', $empUserIds);

		if (isset($req->filBranch)) {
			if ($req->filBranch != '' || $req->filBranch != null) {
				$useIds = DB::table('users')->where('branchId', $req->filBranch)->pluck('id')->toArray();
				$userRoleLists = $userRoleLists->whereIn('userIdFK', $useIds);
			}
		}
		if (isset($req->filRole)) {
			if ($req->filRole != '' || $req->filRole != null) {
				$userRoleLists = $userRoleLists->where('roleId', $req->filRole);
			}
		}
		if (isset($req->filEmpId)) {
			if ($req->filEmpId != '' || $req->filEmpId != null) {
				$empIds = DB::table('hr_emp_general_info')->where('emp_id', 'like', '%' . $req->filEmpId . '%')->pluck('id')->toArray();
				$useIds = DB::table('users')->whereIn('emp_id_fk', $empIds)->pluck('id')->toArray();
				$userRoleLists = $userRoleLists->whereIn('userIdFK', $useIds);
			}
		}

		$userRoleLists = $userRoleLists->paginate(30);

		$userBranchId = Auth::user()->branchId;

		$branchList = MicroFin::getBranchList();

		$roleList = DB::table('gnr_role')->where('id', '>', 2)->pluck('name', 'id')->toArray();

		$data = array(
			'userRoleLists' => $userRoleLists,
			'userBranchId' => $userBranchId,
			'branchList' => $branchList,
			'roleList' => $roleList
		);

		return view('gnr/userRole/viewUserRole', $data);
	}

	public function addUserRole()
	{

		return view('gnr/userRole/addUserRole');
	}

	public function storeUserRole(Request $req)
	{

		$rules = array(
			'userId' => 'required',
			'roleId' => 'required'
		);

		$attributeNames = array(
			'userId' => 'User',
			'roleId' => 'Role'
		);

		$validator = Validator::make(Input::all(), $rules);
		$validator->setAttributeNames($attributeNames);



		if ($validator->fails()) {
			return response::json(array('errors' => $validator->getMessageBag()->toArray()));
		} elseif ( $req->moduleIds == null) {
			$customErrorArray = ['navTabs' => 'Please Select atleast one Action.'];
			return response::json(array('errors' => $customErrorArray));
		} else {

			$functionalityString = $this->makeFunctionalityString($req->moduleIds, $req->functionIds, $req->subFunctionIds);
			$additionalFunctionalityString = $this->getAdditionalFuntionality($req->roleId, $functionalityString);
			$additionalFunctionalityTypeNDateString = $this->createAddFunValidTypeNDate($req->validTypes, $req->dates, $req->times);
			$restrictedFunctionalityString = $this->getRestrictedFuntionality($req->roleId, $functionalityString);

			$userRole = new GnrUserRole;
			$userRole->userIdFK = $req->userId;
			$userRole->roleId = $req->roleId;
			$userRole->additionalFunctionalityId = $additionalFunctionalityString;
			$userRole->additionalFunctionalityTypeNDate = $additionalFunctionalityTypeNDateString;
			$userRole->restrictedFunctionalityId = $restrictedFunctionalityString;
			$userRole->description = $req->description;
			$userRole->createdDate = Carbon::now();
			$userRole->save();
			$logArray = array(
				'moduleId'  => 7,
				'controllerName'  => 'GnrUserRoleController',
				'tableName'  => 'gnr_user_role',
				'operation'  => 'insert',
				'primaryIds'  => [DB::table('gnr_user_role')->max('id')]
			);
			Service::createLog($logArray);

			return response()->json('success');
		}
	}



	public function editUserRole(Request $req)
	{
		$rules = array(
			'userRoleId' => 'required',
			'roleId' => 'required'
		);

		$attributeNames = array(
			'userRoleId' => 'User Role',
			'roleId' => 'Role'
		);

		$validator = Validator::make(Input::all(), $rules);
		$validator->setAttributeNames($attributeNames);
		
		if ($validator->fails()) {
			return response::json(array('errors' => $validator->getMessageBag()->toArray()));
		} elseif ($req->moduleIds == null) {
			
			$customErrorArray = ['navTabs' => 'Please Select atleast one Action.'];
			return response::json(array('errors' => $customErrorArray));
		} else {
			$previousdata = GnrUserRole::find($req->userRoleId);
			$functionalityString = $this->makeFunctionalityString($req->moduleIds, $req->functionIds, $req->subFunctionIds);
			$additionalFunctionalityString = $this->getAdditionalFuntionality($req->roleId, $functionalityString);
			$additionalFunctionalityTypeNDateString = $this->createAddFunValidTypeNDate($req->validTypes, $req->dates, $req->times);
			$restrictedFunctionalityString = $this->getRestrictedFuntionality($req->roleId, $functionalityString);
			$userRole = GnrUserRole::find($req->userRoleId);
			$userRole->roleId = $req->roleId;
			$userRole->additionalFunctionalityId = $additionalFunctionalityString;
			$userRole->additionalFunctionalityTypeNDate = $additionalFunctionalityTypeNDateString;
			$userRole->restrictedFunctionalityId = $restrictedFunctionalityString;
			$userRole->description = $req->description;
			$userRole->createdDate = Carbon::now();
			$userRole->save();
			$logArray = array(
				'moduleId'  => 7,
				'controllerName'  => 'GnrUserRoleController',
				'tableName'  => 'gnr_user_role',
				'operation'  => 'update',
				'previousData'  => $previousdata,
				'primaryIds'  => [$previousdata->id]
			);
			Service::createLog($logArray);
			return response()->json('success');
		}
	}

	//delete
	public function deleteUserRole(Request $req)
	{   
		$previousdata=GnrUserRole::find($req->userRoleId);
		GnrUserRole::find($req->userRoleId)->delete();
		$logArray = array(
			'moduleId'  => 7,
			'controllerName'  => 'GnrUserRoleController',
			'tableName'  => 'gnr_user_role',
			'operation'  => 'delete',
			'previousData'  => $previousdata,
			'primaryIds'  => [$previousdata->id]
		);
		Service::createLog($logArray);
		return response()->json('success');
	}



	private function makeFunctionalityString($moduleIds, $functionIds, $subFunctionIds)
	{
		$functionalityString = "[";
		foreach ($moduleIds as $key => $moduleId) {
			$functionalityString = $functionalityString . '{' . $moduleId . ':' . $functionIds[$key] . ':' . $subFunctionIds[$key] . '}';
			if ($key < count($moduleIds) - 1) {
				$functionalityString = $functionalityString . ',';
			}
		}
		$functionalityString = $functionalityString . "]";

		return $functionalityString;
	}

	private function getRestrictedFuntionality($roleId, $functionalityString)
	{

		$roleFuntionalityString = str_replace(['"', '[', ']'], '', GnrRole::where('id', $roleId)->value('functionalityId'));
		$roleFuntionalityArray = explode(',', $roleFuntionalityString);

		$functionalityString = str_replace(['"', '[', ']'], '', $functionalityString);
		$funtionalityArray = explode(',', $functionalityString);

		$restrictedFuntionality = array_diff($roleFuntionalityArray, $funtionalityArray);

		if (count($restrictedFuntionality) > 0) {
			$restrictedFuntionality = array_values($restrictedFuntionality);
			$restrictedFuntionalityString = "[" . implode(",", $restrictedFuntionality) . "]";
		} else {
			$restrictedFuntionalityString = "";
		}

		return $restrictedFuntionalityString;
	}

	private function getAdditionalFuntionality($roleId, $functionalityString)
	{

		$roleFuntionalityString = str_replace(['"', '[', ']'], '', GnrRole::where('id', $roleId)->value('functionalityId'));
		$roleFuntionalityArray = explode(',', $roleFuntionalityString);

		$functionalityString = str_replace(['"', '[', ']'], '', $functionalityString);
		$funtionalityArray = explode(',', $functionalityString);

		$additionalFuntionalities = array_diff($funtionalityArray, $roleFuntionalityArray);

		if (count($additionalFuntionalities) > 0) {
			$additionalFuntionalities = array_values($additionalFuntionalities);
			$additionalFuntionalityString = "[" . implode(",", $additionalFuntionalities) . "]";
		} else {
			$additionalFuntionalityString = "";
		}

		return $additionalFuntionalityString;
	}

	private function createAddFunValidTypeNDate($validTypes, $dates, $times)
	{

		/*if (count($validTypes)<1) {
	    		$result = "";
	    	}*/

	    	if ($validTypes == null) {
	    		$result = "";
	    	} else {
	    		$result = "[";

	    		foreach ($validTypes as $key => $validType) {

	    			if ($validType == 1) {
	    				$dateString = "";
	    			} else {
	    				$date = Carbon::parse($dates[$key]);
	    				$time = Carbon::parse($times[$key]);
	    				$date->hour = $time->hour;
	    				$date->minute = $time->minute;
	    				$dateString = $date->toDateTimeString();
	    			}

	    			$result =  $result . "{" . $validType . "/" . $dateString . "}";

	    			if ($key < count($validTypes) - 1) {
	    				$result = $result . ",";
	    			}
	    		}

	    		$result = $result . "]";
	    	}

	    	return $result;
	    }


	    public function getUserRoleInfo(Request $req)
	    {
	    	$userRole = GnrUserRole::find($req->userRoleId);

	    	$roleFuntionalityString = DB::table('gnr_role')->where('id', $userRole->roleId)->value('functionalityId');
	    	$roleFuntionalityArray = explode(',', str_replace(['[', ']', '{', '}'], '', $roleFuntionalityString));

	    	$userAddFunArray = explode(',', str_replace(['[', ']', '{', '}'], '', $userRole->additionalFunctionalityId));
	    	$userAddFunTypeNDateArray = explode(',', str_replace(['[', ']', '{', '}'], '', $userRole->additionalFunctionalityTypeNDate));

	    	/*Remove the existing funtionality from*/
	    	$indexToRemove = array();
	    	foreach ($userAddFunArray as $key => $userAddFun) {
	    		if (in_array($userAddFun, $roleFuntionalityArray)) {
	    			unset($userAddFunArray[$key]);
	    			unset($userAddFunTypeNDateArray[$key]);
	    		}
	    	}
	    	/*End Remove the existing funtionality from*/

	    	/*Get the type, date and time seperately*/
	    	$typeArray = array();
	    	/*End Get the type, date and time seperately*/

	    	$restrictedFunArray = explode(',', str_replace(['[', ']', '{', '}'], '', $userRole->restrictedFunctionalityId));

	    	$data = array(
	    		'roleId' => $userRole->roleId,
	    		'roleFuntionalityArray' => $roleFuntionalityArray,
	    		'userAddFunArray' => $userAddFunArray,
	    		'userAddFunTypeNDateArray' => $userAddFunTypeNDateArray,
	    		'restrictedFunArray' => $restrictedFunArray
	    	);

	    	return response::json($data);
	    }
	}
