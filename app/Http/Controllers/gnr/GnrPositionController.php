<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\GnrSupplier;
use App\gnr\GnrDepartment;
use App\gnr\GnrPosition;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; 

class GnrPositionController extends Controller
{
  public function index(){
    
    $userCompanyId = Auth::user()->company_id_fk;
    $positions = GnrPosition::where('companyId',  $userCompanyId)->get();
    $departments = GnrDepartment::where('companyId',$userCompanyId)->get();
    //dd($positions, $departments);
    return view('gnr.tools.position.viewPosition',['positions'=>$positions]);
  } 

  public function addPosition(){
    $userCompanyId = Auth::user()->company_id_fk;
    $departments = GnrDepartment::where('companyId', $userCompanyId)->get();
    return view('gnr.tools.position.addPosition',['departments'=>$departments]);
  }

  public function getDepartmentInfo(Request $request){
    $departments = GnrDepartment::where('companyId',$request->companyId)->get();
    //dd($departments);
    return response()->json($departments);
  }

     //add data
  public function addItem(Request $req) 
  {
    //dd($req->companyId);
    $userCompanyId = Auth::user()->company_id_fk;
    if(Auth::user()->user_type == 'master'){
      $rules = array(
        'name' =>[
              'required',
              Rule::unique('gnr_position')->where('companyId',$req->companyId),
          ],
        'department' => 'required',
        'status' => 'required'
      );
    }else{
      $rules = array(
        'name' =>[
              'required',
              Rule::unique('gnr_position')->where('companyId', Auth::user()->company_id_fk),
          ],
        'department' => 'required',
        'status' => 'required'
      );
    }
   
  
   $attributeNames = array(
     'name'    => 'Position Name',
     'department'   => 'Department',
     'status'   => 'Status',
   );
   
  

   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
     return response::json(array('errors' => $validator->getMessageBag()->toArray()));
   else{

    $now = Carbon::now();
    $position = new GnrPosition();
    $position->name = $req->name;
    $position->dep_id_fk = $req->department;
    if(Auth::user()->user_type == 'master'){
      $position->companyId = $req->companyId;
    }else{
      $position->companyId = $userCompanyId;
    }
    
    $position->status = $req->status;
    $position->createdDate = $now;
    //dd($position);
    $position->save();
    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
  }
}

public function editPosition($id){
    //dd($id);
    $previousdata=GnrPosition::find($id);
    $userCompanyId = Auth::user()->company_id_fk;
    $departments = GnrDepartment::where('companyId', $userCompanyId)->get();

    return view('gnr.tools.position.editPosition',[
      'departments'=>$departments,
      'previousdata'=>$previousdata
    ]);
}
   //edit 
public function updateItem(Request $req) 

  {
    //dd($req->all());
    
   $rules = array(
    //'name' => 'required',
    'name' =>[
          'required',
           Rule::unique('gnr_position')->where('companyId', Auth::user()->company_id_fk).$req->positinId,
      ],
    'department' => 'required',
    'status' => 'required'
  );
  
   $attributeNames = array(
     'name'    => 'Position Name',
     'department'   => 'Department',
     'status'   => 'Status',
   );
   
  

   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
     return response::json(array('errors' => $validator->getMessageBag()->toArray()));
   else{
    $now = Carbon::now();
   $position=GnrPosition::find($req->positinId);
  //dd($previousdata);
    $position->name = $req->name;
    $position->dep_id_fk = $req->department;
    $position->status = $req->status;
    $position->createdDate = $now;
    //dd($position);
    $position->save();
    return response()->json(['responseText' => 'Data updated successfully!'], 200); 
  }
}



   //delete
public function deletePositiontem(Request $req) {
  $previousdata=GnrPosition::find($req->id);
  $employeeExists = DB::table('gnr_employee')->where('company_id_fk',Auth::user()->company_id_fk)->where('position_id_fk',$previousdata->id)->first();

  if ($employeeExists) {
    $data = array(
        'responseTitle' =>  'Warning!',
        'responseText'  =>  'Employee exists for this position'

    );
      return response::json($data);
  }
 GnrPosition::find($req->id)->delete();
  $logArray = array(
    'moduleId'  => 7,
    'controllerName'  => 'GnrPositionController',
    'tableName'  => 'gnr_position',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
 $data = array(
    'responseTitle' =>  'Success!',
    'responseText'  =>  'Position Deleted Successfully'
  );
  return response::json($data);
// return response()->json(['responseText' => 'Data Deleted successfully!'], 200); 
}  
}
