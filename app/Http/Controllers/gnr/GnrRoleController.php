<?php

namespace App\Http\Controllers\gnr;
use Illuminate\Http\Request;

use App\Http\Requests;
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

class GnrRoleController extends Controller {
	
	public function index(){

		$userId = Auth::user()->id;

		if ($userId!=1) {
			$roles = GnrRole::where('id','>',2)->get();
		}
		else{
			$roles = GnrRole::all();				
		}

		return view('gnr/role/viewRole', ['roles' => $roles,'userId'=>$userId]);
	}

	public function addRole() {

		return view('gnr/role/addRole');
	}

	public function addItem(Request $req) {			

		$rules = array(
			'name' => 'required'
		);

		$attributeNames = array(
			'name' => 'User Role Name',
			'description' => 'Role Description'
		);

		$validator = Validator::make(Input::all(), $rules);
		$validator->setAttributeNames($attributeNames);

		$customErrorArray = array();
		if (count($req->moduleIds)<1) {
			$customErrorArray = ['navTabs' => 'Please select atleat one Action.'];
		}

		if($validator->fails() || count($req->moduleIds)<1)
			return response::json(array('errors' => $validator->getMessageBag()->toArray() + $customErrorArray));
		else {			

			$functionalityString = $this->makeFunctionalityString($req->moduleIds,$req->functionIds,$req->subFunctionIds);

			$role = new GnrRole;
			$role->name        		= $req->name;
			$role->description 		= $req->description;
			$role->functionalityId  = $functionalityString;
			$role->createdDate  	= Carbon::now();
			$role->save();
			$logArray = array(
				'moduleId'  => 7,
				'controllerName'  => 'GnrRoleController',
				'tableName'  => 'gnr_role',
				'operation'  => 'insert',
				'primaryIds'  => [DB::table('gnr_role')->max('id')]
			);
			Service::createLog($logArray);

			return response()->json(['responseText' => 'Data saved successfully.'], 200); 
		}
	}



	public function editItem(Request $req) {
		$rules = array(
			'name' => 'required'
		);

		$attributeNames = array(
			'name' => 'Role Name',
			'description' => 'Description'
		);

		$validator = Validator::make(Input::all(), $rules);
		$validator->setAttributeNames($attributeNames);

		$customErrorArray = array();
		if (count($req->moduleIds)<1) {
			$customErrorArray = ['navTabs' => 'Please select atleat one Action.'];
		}

		if($validator->fails() || count($req->moduleIds)<1)
			return response::json(array('errors' => $validator->getMessageBag()->toArray() + $customErrorArray));
		else {	

			$functionalityString = $this->makeFunctionalityString($req->moduleIds,$req->functionIds,$req->subFunctionIds);
			$previousdata = GnrRole::find($req->roleId);
			$role = GnrRole::find($req->roleId);
			$role->name        		= $req->name;
			$role->description 		= $req->description;
			$role->functionalityId  = $functionalityString;
			$role->createdDate  	= Carbon::now();
			$role->save();
			$logArray = array(
				'moduleId'  => 7,
				'controllerName'  => 'GnrRoleController',
				'tableName'  => 'gnr_role',
				'operation'  => 'update',
				'previousData'  => $previousdata,
				'primaryIds'  => [$previousdata->id]
			);
			Service::createLog($logArray);

			return response()->json(['responseText' => 'Data Updated successfully.'], 200);
		}
	}

	  	//delete
	public function deleteItem(Request $req) {
		$previousdata=GnrRole::find($req->roleId);
		GnrRole::find($req->roleId)->delete();
		$logArray = array(
			'moduleId'  => 7,
			'controllerName'  => 'GnrRoleController',
			'tableName'  => 'gnr_role',
			'operation'  => 'delete',
			'previousData'  => $previousdata,
			'primaryIds'  => [$previousdata->id]
		);
		Service::createLog($logArray);
		return response()->json(['responseText' => 'Data Deleted successfully.'], 200);
	}

	private function makeFunctionalityString($moduleIds,$functionIds,$subFunctionIds){
		$functionalityString = "[";
		foreach ($moduleIds as $key => $moduleId) {
			$functionalityString = $functionalityString .'{' . $moduleId.':'.$functionIds[$key].':'.$subFunctionIds[$key].'}';
			if ($key<count($moduleIds)-1) {
				$functionalityString = $functionalityString .',';
			}
		}
		$functionalityString = $functionalityString ."]";

		return $functionalityString;
	}


	public function getRoleInfo(Request $req) {
		$role = GnrRole::find($req->roleId);


		$data = array(
			'roleName' => $role->name,
			'functionality' => explode(',',str_replace(['"','[',']','{','}'], '', $role->functionalityId)),
			'description' => $role->description,
		);

		return response::json($data);
	}

}
