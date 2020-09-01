<?php

	namespace App\Http\Controllers\gnr;
	use Illuminate\Http\Request;
	
	use App\Http\Requests;
	use App\gnr\GnrUserRole;
	use Validator;
	use Response;
	use DB;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use App\Http\Controllers\Controller;

	class GnrUserRoleController extends Controller {
	
		public function index(){
			
			$userRoleLists = GnrUserRole::all();
			return view('gnr/user/viewUserRole', ['userRoleLists' => $userRoleLists]);
		}

		public function addUserRole() {
			
			return view('gnr/user/addUserRole');
		}

		public function addItem(Request $req) {

			//$req->merge(['functionalityId' => implode(',', (array) $req->get('functionalityId'))
    //]);
			
			
			$rules = array(
						   'userId' => 'required',
						   'roleId' => 'required',
						   'functionalityId' => 'required'
						   //'roleId' => 'required'
						   );

			$attributeNames = array(
									'userId' => 'User Name',
									'functionalityId' => 'Role Functionality',
									'roleId' => 'Role'
									);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributeNames);

			if($validator->fails())
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$create = GnrUserRole::create($req->all());
				return response()->json($req); 
			}
		}

		public function getCheckedValue(Request $req){
		 	$subFunctions = GnrUserRole::where('id',$req->id)->get();
		 	return view('gnr/user/checkedView', ['subFunctions' => $subFunctions]);

		}

public function editItem(Request $req) {
      $gnrUserRole = GnrUserRole::find($req->id)->update($req->all());
      return response()->json($gnrUserRole);
  }
//delete
    public function deleteItem(Request $req) {
      GnrUserRole::find($req->id)->delete();
      return response()->json();
    } 
}
