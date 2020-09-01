<?php

	namespace App\Http\Controllers\gnr;
	use Illuminate\Http\Request;
	
	use App\Http\Requests;
	use App\gnr\GnrRole;
	use Validator;
	use Response;
	use DB;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use App\Http\Controllers\Controller;

	class GnrRoleController extends Controller {
	
		public function index(){
			
			$roleList = GnrRole::all();
			return view('gnr/role/viewRole', ['roleList' => $roleList]);
		}

		public function addRole() {
			
			return view('gnr/role/addRole');
		}

		public function addItem(Request $req) {

			//$req->merge(['functionalityId' => implode(',', (array) $req->get('functionalityId'))
    //]);
			
			$rules = array(
						   'name' => 'required',
						   'functionalityId' => 'required',
						   'description' => 'required'
						   );

			$attributeNames = array(
									'name' => 'User Role Name',
									'functionalityId' => 'Role Functionality',
									'description' => 'Role Description'
									);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributeNames);

			if($validator->fails())
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$create = GnrRole::create($req->all());
				return response()->json(['responseText' => 'Data saved successfully.'], 200); 
			}
		}

		public function roleCheckedValue(Request $req){
			 	$subFunctions = GnrRole::where('id',$req->id)->get();
			 	return view('gnr/role/checkedView', ['subFunctions' => $subFunctions]);
			}

		public function editItem(Request $req) {
			$rules = array(
						   'name' => 'required',
						   'functionalityId' => 'required',
						   'description' => 'required'
						   );

			$attributeNames = array(
									'name' => 'User Role Name',
									'functionalityId' => 'Role Functionality',
									'description' => 'Role Description'
									);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributeNames);

			if($validator->fails())
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {	
	      $gnrRole = GnrRole::find($req->id)->update($req->all());
			//$id = $req->id;
	      return response()->json($gnrRole);
	  	}
	  }

	  //delete
	    public function deleteItem(Request $req) {
	      GnrRole::find($req->id)->delete();
	      return response()->json($req->id);
	    }
  	
}
