<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;
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

class GnrProjectController extends Controller
{
 public function index(){
  $userCompanyId = Auth::user()->company_id_fk;
  $projects = GnrProject::where('companyId',$userCompanyId)->orderBy('projectCode')->get();
  return view('gnr.tools.companySetting.project.viewProject',['projects' => $projects]);
}

public function addProject(){
  return view('gnr.tools.companySetting.project.addProject');
}
//add data
public function addItem(Request $req) 
{
  //dd($req->all());
  $userCompanyId = Auth::user()->company_id_fk;
 $rules = array(
  'name' => 'required',
  'projectCode' =>[
            'required',
            // Rule::unique('gnr_project')->where(function($query) {
            //       $query->where('companyId', Auth::user()->company_id_fk);
            // })
             Rule::unique('gnr_project')->where('companyId', Auth::user()->company_id_fk),
  ],
  
);
 $attributeNames = array(
   'name'      => 'Project Name',
   // 'groupId'   => 'Group Name',
   // 'companyId' => 'Company Name',
   'projectCode' => 'Project Code' 
 );

 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{
  $now = Carbon::now();
  $req->request->add(['createdDate' => $now]);
  $userCompanyId = Auth::user()->company_id_fk;
  $userCustomerId = Auth::user()->customer_id;
  //dd($userCompanyId);
  $create = new GnrProject();

  $create->name = $req->name;
  $create->projectCode = $req->projectCode;
  $create->customerId = $userCustomerId;
  $create->companyId =  $userCompanyId;
  $create->createdDate =  $now;
  $create->save();
  
  $projectType = new GnrProjectType();

  $projectType->name = $req->name;
  $projectType->projectTypeCode = $req->projectCode;
  $projectType->companyId = $userCompanyId;
  $projectType->customerId = $userCustomerId;
  $projectType->projectId = $create->id;

  //dd($projectType);
  $projectType->save();


  // $logArray = array(
  //   'moduleId'  => 7,
  //   'controllerName'  => 'GnrProjectController',
  //   'tableName'  => 'gnr_project',
  //   'operation'  => 'insert',
  //   'primaryIds'  => [DB::table('gnr_project')->max('id')]
  // );
  // Service::createLog($logArray);
  return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
}
}

    //edit function
public function editItem(Request $req) {
  $rules = array(
    'name' => 'required',
    // 'groupId' => 'required',
    // 'companyId' => 'required',
    'projectCode' =>[
            'required',
            Rule::unique('gnr_project')->where('companyId', Auth::user()->company_id_fk).$req->id,
            
    ]
    //'projectCode' => 'required|unique:gnr_project,projectCode,'.$req->id
  );
  $attributeNames = array(
   'name'      => 'Project Name',
   // 'groupId'   => 'Group Name',
   // 'companyId' => 'Company Name',
   'projectCode' => 'Project Code' 
 );
  $userCompanyId = Auth::user()->company_id_fk;
  $userCustomerId = Auth::user()->customer_id;
  //dd($userCustomerId);
  $validator = Validator::make ( Input::all (), $rules);
  $validator->setAttributeNames($attributeNames);
  if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
   $previousdata = GnrProject::find ($req->id);
   $project = GnrProject::find ($req->id);
   $project->name = $req->name;
   $project->projectCode = $req->projectCode;
   $project->save();

   //projectType insert

  $projectType = GnrProjectType::where('projectId',$req->id)->first();;
  $projectType->name = $req->name;
  $projectType->projectTypeCode = $req->projectCode;
  $projectType->customerId = Auth::user()->customer_id;
  $projectType->projectId = $project->id;

  //dd($projectType);
  $projectType->save();

   $gnrGroupName            = DB::table('customers')->select('business_name')->where('id',$userCustomerId)->first();
   $gnrCompanyName          = DB::table('gnr_company')->select('name')->where('id',$userCompanyId)->first();

   $data = array(
    'project'                 => $project,
    'gnrGroupName'            => $gnrGroupName,
    'gnrCompanyName'          => $gnrCompanyName,
    'slno'                    => $req->slno
  );
   $logArray = array(
    'moduleId'  => 7,
    'controllerName'  => 'GnrProjectController',
    'tableName'  => 'gnr_project',
    'operation'  => 'update',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
   Service::createLog($logArray);
      //$projects = GnrProject::all();
   return response()->json($data); 
 }
} 

    //delete
public function deleteItem(Request $req) {
 $previousdata=GnrProject::find($req->id);
 $voucherExists = DB::table('acc_voucher')->where('companyId',Auth::user()->company_id_fk)->where('projectId',$previousdata->id)->first();
 if ($voucherExists) {
    $data = array(
        'responseTitle' =>  'Warning!',
        'responseText'  =>  'Voucher exists for this project'

    );
      return response::json($data);
  }
   GnrProject::find($req->id)->delete();
   $logArray = array(
    'moduleId'  => 7,
    'controllerName'  => 'GnrProjectController',
    'tableName'  => 'gnr_project',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
 Service::createLog($logArray);
 $data = array(
    'responseTitle' =>  'Success!',
    'responseText'  =>  'Project Deleted Successfully'
  );
 return response()->json($data);
}  
}
