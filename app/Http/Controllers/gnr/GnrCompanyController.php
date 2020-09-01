<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\GnrCompany;
use App\Admin\Customer;
use App\Service\Service as Services;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;
use App\gnr\GnrBranch;
use App\gnr\GnrEmployee;
use App\Service\LedgerCreationHelper;

class GnrCompanyController extends Controller
{
	public function index(){
    $customerId = Auth::user()->customer_id;
   
    if(Auth::user()->user_type == 'master'){
      $companys = GnrCompany::where('customer_id', $customerId)->get();
    }else{
      $companys = GnrCompany::where('id', Auth::user()->company_id_fk)->get();
    }
    //dd($companys);
    return view('gnr.tools.companySetting.company.viewCompany',['companys' => $companys]);
  }	

  public function addCompany(){
    return view('gnr.tools.companySetting.company.addCompany');
  }

  public function addItem(Request $req){
   $rules = array(
    'name' => 'required',
    'business_type' => 'required',
    'fy_type' => 'required',
    'email' => 	'required|email',
    'phone' => 'required|regex:/(01)[0-9]{9}$/',
    'address' => 'required',
     'voucher_type_step' => 'required',
     'website' => 'required'
  );
   $messages = array(
             // 'phone.required' => 'The Mobile Number Should Be 11 Digits.'
    'phone.regex' => 'The :attribute number is invalid , accepted format: 01xxxxxxxxx Should Be 11 Digits.'
  );
   $attributeNames = array(
     'name'    => 'Company Name',
      'business_type'    => 'Business type',
     'fy_type'    => 'Fiscal Year type',
     'email'   => 'Email',
     'phone'   => 'Phone',
     'address' => 'Address',
     'voucher_type_step' => 'Voucher appproval step',   
     'website' => 'Website'   
   );

   $validator = Validator::make ( Input::all (), $rules, $messages);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{

    if ($req->file('image')) {
      $file = $req->file('image');
        //$filename = $file->getClientOriginalName();
      $filename = str_random(10) . '.' . $file->getClientOriginalExtension();
      $destinationPath = base_path() . '/public/images/company/';
      $file->move($destinationPath,$filename);
    } 

    $now = Carbon::now();
    $req->request->add(['createdDate' => $now]);
    $create = $req->all();
    $create['customer_id'] = Auth::user()->customer_id;
    
    if($req->file('image')){
      $create['image'] = $filename;
    }
    
    $company = GnrCompany::create($create);
    // fiscal year generate
    Services::generateFiscalYear($company->id);
    Services::generateNextFiscalYear($company->id);
    // project create
    $projectData['name'] = 'General';
    $projectData['projectCode'] = 1;
    $projectData['companyId'] = $company->id;
    $projectData['customerId'] = $company->customer_id;
    $projectData['createdDate'] = Carbon::now();
    $project = GnrProject::create($projectData);

    // project type create
    $projectTypeData['name'] = 'General';
    $projectTypeData['projectTypeCode'] = 1;
    $projectTypeData['companyId'] = $company->id;
    $projectTypeData['customerId'] = $company->customer_id;
    $projectTypeData['projectId'] = $project->id;
    $projectTypeData['createdDate'] = Carbon::now();
    $projectType = GnrProjectType::create($projectTypeData);

    // create branch
    $branchData['name'] = 'Head Office';
    $branchData['branchCode'] = 0;
    $branchData['companyId'] = $company->id;
    $branchData['projectId'] = $project->id;
    $branchData['projectTypeId'] = $projectType->id;
    $branchData['branchOpeningDate'] = Carbon::now()->format('Y-m-d');
    $branchData['softwareStartDate'] = Carbon::now()->format('Y-m-d');
    $branchData['aisStartDate'] = Carbon::now()->format('Y-m-d');
    $branchData['createdDate'] = Carbon::now();
    $branch = GnrBranch::create($branchData);

    // create employee

    // $employeeData['name'] = $businessData['business_holder_name'];
    // $employeeData['branchId'] = $branch->id;
    // $employeeData['company_id_fk'] = $company->id;
    // $employeeData['presentAddress'] = $businessData['business_address'];
    // $employee = GnrEmployee::create($employeeData);

    $logArray = array(
      'moduleId'  => 7,
      'controllerName'  => 'GnrCompanyController',
      'tableName'  => 'gnr_company',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('gnr_company')->max('id')]
    );
    Service::createLog($logArray); 
    LedgerCreationHelper::generateLedgerTree($company->id);
    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
  }
}

   //edit function
public function editItem(Request $req) {
  //dd(Auth::user()->company_id_fk);
 $rules = array(
  'name' => 'required',
  'business_type' => 'required',
  // 'fy_type' => 'required',
  // 'groupId' => 'required',
  'voucher_type_step' => 'required',
  'email' =>  'required|email',
  'phone' => 'required|regex:/(01)[0-9]{9}$/',
  'address' => 'required'
);
 $messages = array(
             // 'phone.required' => 'The Mobile Number Should Be 11 Digits.'
  'phone.regex' => 'The :attribute number is invalid , accepted format: 01xxxxxxxxx Should Be 11 Digits.'
);
 $attributeNames = array(
   'name'    => 'Company Name',
    'business_type' => 'Business type',
    // 'fy_type' => 'Fiscal year type',
   'email'   => 'Email',
   'phone'   => 'Phone',
   'address' => 'Address',
  //  'groupId' => 'Group Name'  
  'voucher_type_step' => 'Voucher appproval step'    
 );

 $validator = Validator::make ( Input::all (), $rules, $messages);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{
  if ($req->file('image')) {
    $file = $req->file('image');
            //$filename = $file->getClientOriginalName();
    $filename = str_random(10) . '.' . $file->getClientOriginalExtension();
    $destinationPath = base_path() . '/public/images/company/';
    $file->move($destinationPath,$filename);
  }
  $previousdata = GnrCompany::find ($req->id);
  $create = GnrCompany::find ($req->id);
  $create->name    = $req->name;
  $create->business_type    = $req->business_type;
  // $create->fy_type    = $req->fy_type;
  // $create->groupId = $req->groupId;
  $create->email   = $req->email;
  $create->phone   = $req->phone;
  $create->address = $req->address;
  $create->website = $req->website;
  $create->voucher_type_step = $req->voucher_type_step;
  if($req->file('image')){
    $create->image   = $filename;
  }
  $create->save();

  // $groupName = DB::table('gnr_group')->select('name')->where('id',$req->groupId)->first();
  $data = array(
    'create'        =>$create,
    // 'groupName'     =>$groupName,
    'slno'          => $req->slno
  );
  $logArray = array(
    'moduleId'  => 7,
    'controllerName'  => 'GnrCompanyController',
    'tableName'  => 'gnr_company',
    'operation'  => 'update',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  //LedgerCreationHelper::generateLedgerTree($company->id);
  return response()->json($data);
}
}

   //delete
public function deleteItem(Request $req) {
 // $imgName = GnrCompany::select('image')->where('id',$req->id)->first();
  $previousdata=GnrCompany::find($req->id);
  $existsProject = GnrProject::where('companyId',$previousdata->id)->first();
  $existsProjectType = GnrProjectType::where('companyId',$previousdata->id)->first();
  $existsBranch = GnrBranch::where('companyId',$previousdata->id)->first();
  $existsFiscalYear = GnrBranch::where('companyId',$previousdata->id)->first();
  //dd($existsProject);
  if($existsProject){
    $data = array(
      'responseTitle' =>  'Warning!',
      'responseText'  =>  'Project exists for this Company'
    );
    return response::json($data);
  }elseif($existsProjectType){
    $data = array(
      'responseTitle' =>  'Warning!',
      'responseText'  =>  'projectType exists for this Company'
    );
    return response::json($data);
  }elseif($existsBranch){
    $data = array(
      'responseTitle' =>  'Warning!',
      'responseText'  =>  'Branch exists for this Company'
    );
    return response::json($data);
  }elseif($existsFiscalYear){
    $data = array(
      'responseTitle' =>  'Warning!',
      'responseText'  =>  'Fiscal Year exists for this Company'
    );
    return response::json($data);
  }
  GnrCompany::find($req->id)->delete();
  DB::table('gnr_fiscal_year')->where('companyId', $req->id)->delete();
  $logArray = array(
    'moduleId'  => 7,
    'controllerName'  => 'GnrCompanyController',
    'tableName'  => 'gnr_company',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return response()->json($imgName);
} 

public function imageDelete(Request $req) {
  $image = "{$req->replacedImage1}";
  \File::delete($image);
  return response()->json($image);
}  
}

