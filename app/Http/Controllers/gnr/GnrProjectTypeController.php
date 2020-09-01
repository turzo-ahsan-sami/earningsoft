<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
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

class GnrProjectTypeController extends Controller
{
  public function index(){
    $userCompanyId = Auth::user()->company_id_fk;
    $projectTypes = GnrProjectType::where('companyId',$userCompanyId)->orderBy('projectTypeCode')->get();
    return view('gnr.tools.companySetting.projectType.viewProjectType',['projectTypes' => $projectTypes]);
  }

  public function addProjectType(){
    return view('gnr.tools.companySetting.projectType.addProjectType');
  }

    //add data
  public function addItem(Request $req) 
  {
    //dd($req->all());
   $rules = array(
    'name' => 'required',
    // 'groupId' => 'required',
    // 'companyId' => 'required',
    'projectId' => 'required',
    'projectTypeCode' =>[
              'required',
              // Rule::unique('gnr_project')->where(function($query) {
              //       $query->where('companyId', Auth::user()->company_id_fk);
              // })
               Rule::unique('gnr_project_type')->where('companyId', Auth::user()->company_id_fk),
      ]
  );
   $attributeNames = array(
     'name'      => 'Project Type',
     // 'groupId'   => 'Group Name',
     // 'companyId' => 'company Name',
     'projectId' => 'Project Name',
     'projectTypeCode' => 'project Type Code'
   );

   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $now = Carbon::now();
    $userCompanyId = Auth::user()->company_id_fk;
    $userCustomerId = Auth::user()->customer_id;
    //dd($userCompanyId);
    $create = new GnrProjectType();

    $create->name = $req->name;
    $create->projectTypeCode = $req->projectTypeCode;
    $create->projectId = $req->projectId;
    $create->customerId = $userCustomerId;
    $create->companyId =  $userCompanyId;
    $create->companyId =  $userCompanyId;
    $create->createdDate =  $now;
    $create->save();
    // $req->request->add(['createdDate' => $now]);
    // $create = GnrProjectType::create($req->all());
    // $logArray = array(
    //   'moduleId'  => 7,
    //   'controllerName'  => 'GnrProjectTypeController',
    //   'tableName'  => 'gnr_project_type',
    //   'operation'  => 'insert',
    //   'primaryIds'  => [DB::table('gnr_project_type')->max('id')]
    // );
    // Service::createLog($logArray);
    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
  }
}

//edit 
public function editItem(Request $req) {

  $rules = array(
    'name' => 'required',
    // 'groupId' => 'required',
    // 'companyId' => 'required',
    'projectId' => 'required',
    //'projectTypeCode' => 'required'
    'projectTypeCode' =>[
              'required',
              // Rule::unique('gnr_project')->where(function($query) {
              //       $query->where('companyId', Auth::user()->company_id_fk);
              // })
               Rule::unique('gnr_project_type')->where('companyId', Auth::user()->company_id_fk).$req->id,
      ]
  );
  $attributeNames = array(
   'name'      => 'Project Type',
   // 'groupId'   => 'Group Name',
   // 'companyId' => 'company Name',
   'projectId' => 'Project Name',
   'projectTypeCode' => 'project Type Code'
 );
  $validator = Validator::make ( Input::all (), $rules);
  $validator->setAttributeNames($attributeNames);
  if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
   
    $userCompanyId = Auth::user()->company_id_fk;
    $userCustomerId = Auth::user()->customer_id;
    
    $previousdata = GnrProjectType::find ($req->id);
    $projectType = GnrProjectType::find ($req->id);
    $projectType->name = $req->name;
    $projectType->projectTypeCode = $req->projectTypeCode;
    $projectType->customerId = $userCustomerId;
    $projectType->companyId = $userCompanyId;
    $projectType->projectId = $req->projectId;
    $projectType->save();

    // $gnrGroupName       = DB::table('customers')->select('business_name')->where('id',  $userCustomerId)->first();
    // $gnrCompanyName     = DB::table('gnr_company')->select('name')->where('id',$userCompanyId)->first();
    // $projectName        = DB::table('gnr_project')->select('name')->where('id',$req->projectId)->first();

    $data = array(
      'id'                      => $req->id,
      'name'                    => $req->name,
      'projectTypeCode'         => $req->projectTypeCode,
      'customerId'              => $userCustomerId,
      'companyId'               => $userCompanyId,
      'projectId'               => $req->projectId,
      // 'gnrGroupName'            => $gnrGroupName,
      // 'gnrCompanyName'          => $gnrCompanyName,
      // 'projectName'             => $projectName,
      'slno'                    => $req->slno
    );
    //dd($data );
    $logArray = array(
      'moduleId'  => 7,
      'controllerName'  => 'GnrProjectTypeController',
      'tableName'  => 'gnr_project_type',
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
  $previousdata=GnrProjectType::find($req->id);
  GnrProjectType::find($req->id)->delete();
  $logArray = array(
    'moduleId'  => 7,
    'controllerName'  => 'GnrProjectTypeController',
    'tableName'  => 'gnr_project_type',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return response()->json();
}  

}
