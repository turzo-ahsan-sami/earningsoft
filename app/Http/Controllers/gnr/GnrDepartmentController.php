<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\GnrDepartment;
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

class GnrDepartmentController extends Controller
{
  public function index(){
    $userCompanyId = Auth::user()->company_id_fk;
    $departments = GnrDepartment::where('companyId', $userCompanyId)->get();
    return view('gnr/tools/departmentRoom/viewDepartment',['departments' => $departments]);
  }

  public function addDepartmentForm(){
    return view('gnr/tools/departmentRoom/addDepartmentForm');
  }

  public function addItem(Request $req) 
  
  {
   // dd($req->companyId);
    

  if(Auth::user()->user_type == 'master'){
    //dd('ok');
    $rules = array(  
      'name' =>[
              'required',
               Rule::unique('gnr_department')->where('companyId', $req->companyId),
      ],
      'companyId' => 'required',
    );
  }else{
    //dd('not ok');
    $rules = array(  
      'name' =>[
              'required',
               Rule::unique('gnr_department')->where('companyId', Auth::user()->company_id_fk),
      ],
     
   );
  }

   $attributeNames = array(
     'name'       => 'Department Name',   
     'companyId'  => 'Company Name'   
   );

   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $createdDate = Carbon::now();
    $userCompanyId = Auth::user()->company_id_fk;
    $department = new GnrDepartment();
    $department->name = $req->name;

    if(Auth::user()->user_type == 'master'){
      $department->companyId = $req->companyId;
      //dd('ok');
    }else{
      //dd('not ok');
      $department->companyId = $userCompanyId;
    }
   
    $department->createdDate = $createdDate;
    
    $department->save();
    $logArray = array(
      'moduleId'  => 7,
      'controllerName'  => 'GnrDepartmentController',
      'tableName'  => 'gnr_department',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('gnr_department')->max('id')]
    );
    Service::createLog($logArray);
    return response()->json($createdDate); 
  }
}

//edit function
public function editItem(Request $req) {
  //dd($req->all());
 $rules = array(
  //'name'       => 'required|unique:gnr_department,name,'.$req->id
  'name' =>[
              'required',
               Rule::unique('gnr_department')->where('companyId', Auth::user()->company_id_fk).$req->id,
      ]
);

 $attributeNames = array(
  'name'       => 'Department Name'
);
 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{
  $previousdata = GnrDepartment::find ($req->id);

  $department = GnrDepartment::find ($req->id);
  $department->name = $req->name;
  $department->save();

  $data = array(
    'department'      => $department,
    'slno'            => $req->slno
  );
  $logArray = array(
    'moduleId'  => 7,
    'controllerName'  => 'GnrDepartmentController',
    'tableName'  => 'gnr_department',
    'operation'  => 'update',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);

  return response()->json($data);
}
}

 //delete
public function deleteItem(Request $req) {
  $previousdata=GnrDepartment::find($req->id);
  $employeeExists = DB::table('gnr_employee')->where('company_id_fk',Auth::user()->company_id_fk)->where('department_id_fk',$previousdata->id)->first();
  $positionExists = DB::table('gnr_position')->where('companyId',Auth::user()->company_id_fk)->where('dep_id_fk',$previousdata->id)->first();

  if ($employeeExists) {
    $data = array(
        'responseTitle' =>  'Warning!',
        'responseText'  =>  'Employee exists for this department'

    );
      return response::json($data);
  }elseif($positionExists){
      $data = array(
        'responseTitle' =>  'Warning!',
        'responseText'  =>  'Position exists for this department, delete first position before department'
    );
    return response::json($data);
  }

  GnrDepartment::find($req->id)->delete();
  
  $logArray = array(
    'moduleId'  => 7,
    'controllerName'  => 'GnrDepartmentController',
    'tableName'  => 'gnr_department',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  $data = array(
    'responseTitle' =>  'Success!',
    'responseText'  =>  'Department Deleted Successfully'
  );

  return response::json($data);
}

}
