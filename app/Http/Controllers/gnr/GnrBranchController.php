<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\GnrBranch;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; 

class GnrBranchController extends Controller
{
  public function index(){
    $branchs = GnrBranch::orderBy('branchCode')->where('companyId', Auth::user()->company_id_fk)->get();
    return view('gnr.tools.companySetting.branch.viewBranch',['branchs' => $branchs]);
  }
  // public function addBranch(){
  //   return view('gnr.tools.companySetting.branch.addBranch');
  // }

   public function addBranch(){
    // 1st step
    $customerId = Auth::user()->customer_id;
    $customerUserId = User::where('customer_id', $customerId)->where('user_type', 'master')->value('id');
    $customerPlanId = DB::table('plan_subscriptions')->where('plan_subscriptions.user_id', $customerUserId)->value('plan_id');
    $customerBranchLimit = DB::table('plans')->where('id', $customerPlanId)->value('active_branch_limit');

    // 2nd step
    //$companyId = DB::table('gnr_company')->where('gnr_company.customer_id', $customerId)->pluck('id')->toArray();
    $branchId = DB::table('gnr_branch')->where('gnr_branch.branchCode', '!=',0)->where('gnr_branch.companyId', Auth::user()->company_id_fk)->count();
    //dd($branchId);
    //dd($companyId);
    //dd($customerBranchLimit);

     if ($branchId>=$customerBranchLimit) {
      return '<script type="text/javascript">alert("User Branch is Limit!");
               window.location.href = "viewBranch";
             </script>';
         //return back()->with('<script type="text/javascript">alert("hello!");</script>');
      
     }
    
     else{
       return view('gnr.tools.companySetting.branch.addBranch');
    
     }
  }

    //add data
  public function addItem(Request $req) 
  {
   $rules = array(
    'name' => 'required',
    //'branchCode' => 'required',
    'branchCode' =>[
              'required',
               Rule::unique('gnr_branch')->where('companyId', Auth::user()->company_id_fk),
            ],
    'projectId' => 'required',
    'projectTypeId' => 'required',
    
  );
   $messages = array(
             // 'phone.required' => 'The Mobile Number Should Be 11 Digits.'
    //'phone.regex' => 'The :attribute number is invalid , Should Be 11 Digits.'
  );
   $attributeNames = array(
     'name'    => 'Brach Name',
      'branchCode' => 'Branch Code',
     //'groupId' => 'Group Name', 
     //'companyId' => 'Company Name', 
     'projectId' => 'Project Name', 
     'projectTypeId' => 'Project Type',
    
   );

   $validator = Validator::make ( Input::all (), $rules);
   //$validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{ 
    $branchOpeningDate  = $req->branchOpeningDate;
    $branchOpenDate     = date('Y-m-d', strtotime($branchOpeningDate));
    $softwareStartDate  = $req->softwareStartDate;
    $softwareStDate     = date('Y-m-d', strtotime($softwareStartDate));
    $now = Carbon::now();
    $req->request->add(['branchOpeningDate' => $branchOpenDate, 'companyId'=>Auth::user()->company_id_fk, 'softwareStartDate' => $softwareStDate, 'aisStartDate' => $softwareStDate, 'createdDate' => $now]);
    $create = GnrBranch::create($req->all());
    $logArray = array(
      'moduleId'  => 7,
      'controllerName'  => 'GnrBranchController',
      'tableName'  => 'gnr_branch',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('gnr_branch')->max('id')]
    );
    Service::createLog($logArray);
    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
  }
}

    //edit 
public function editItem(Request $req) {
 $rules = array(
  'name' => 'required',
  'branchCode' =>[
              'required',
               Rule::unique('gnr_branch')->where('companyId', Auth::user()->company_id_fk).$req->id,
            ],
  //'companyId' => 'required',
  'projectId' => 'required',
  'projectTypeId' => 'required',
);

 $attributeNames = array(
   'name'    => 'Brach Name',
   'branchCode' => 'Branch Code',
   // 'groupId' => 'Group Name', 
   // 'companyId' => 'Company Name', 
   'projectId' => 'Project Name', 
   'projectTypeId' => 'Project Type',
 );

 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{
 $previousdata = GnrBranch::find ($req->id);
 $branchOpeningDate  = $req->branchOpeningDate;
 $branchOpenDate     = date('Y-m-d', strtotime($branchOpeningDate));
 $softwareStartDate  = $req->softwareStartDate;
 $softwareStDate     = date('Y-m-d', strtotime($softwareStartDate));
 $branch = GnrBranch::find ($req->id);
 $branch->name = $req->name;
 $branch->branchCode = $req->branchCode;
 // $branch->groupId = $req->groupId;
 $branch->companyId = Auth::user()->company_id_fk;
 $branch->projectId = $req->projectId;
 $branch->projectTypeId = $req->projectTypeId;
 $branch->contactPerson = $req->contactPerson;
 $branch->email = $req->email;
 $branch->phone = $req->phone;
 $branch->address = $req->address;
 $branch->branchOpeningDate = $branchOpenDate;
 $branch->softwareStartDate = $softwareStDate;
 $branch->aisStartDate = $softwareStDate;
 $branch->status = $req->status;
 //dd($branch);
 $branch->save();
              /*$branchOpeningDate  = $req->branchOpeningDate;
              $branchOpenDate     = date('Y-m-d', strtotime($branchOpeningDate));
              $softwareStartDate  = $req->softwareStartDate;
              $softwareStDate     = date('Y-m-d', strtotime($softwareStartDate));
              $req->request->add(['branchOpeningDate' => $branchOpenDate, 'softwareStartDate' => $softwareStDate]);
              $edit = GnrBranch::find($req->id)->update($req->all());*/
              $groupName        = DB::table('gnr_group')->select('name')->where('id',$req->groupId)->first();
              $companyName      = DB::table('gnr_company')->select('name')->where('id',Auth::user()->company_id_fk)->first();               
              $ProjectName      = DB::table('gnr_project')->select('name')->where('id',$req->projectId)->first();
              $ProjectTypeName  = DB::table('gnr_project_type')->select('name')->where('id',$req->projectTypeId)->first();
              $status           = $req->status;
              if($status==0){
                $status='Inactive';}else{$status='Active';
              } 
              $data = array(
                'branch'          => $branch,
                'companyName'     => $companyName,
                'groupName'       => $groupName,
                'ProjectName'     => $ProjectName,
                'ProjectTypeName' => $ProjectTypeName,
                'status'          => $status,
                'slno'            => $req->slno
              ); 
              $logArray = array(
                'moduleId'  => 7,
                'controllerName'  => 'GnrBranchController',
                'tableName'  => 'gnr_branch',
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
            $previousdata=GnrBranch::find($req->id);
            $voucherExists = DB::table('acc_voucher')->where('companyId',Auth::user()->company_id_fk)->where('branchId',$previousdata->id)->first();
            $employeeExists = DB::table('gnr_employee')->where('company_id_fk',Auth::user()->company_id_fk)->where('branchId',$previousdata->id)->first();
            //dd($voucherExists);
            if($voucherExists) {
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Voucher exists for this branch'

                );
                return response::json($data);
            }elseif($employeeExists){
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Employee exists for this branch'

                );
                return response::json($data);
            }
            GnrBranch::find($req->id)->delete();
            $logArray = array(
              'moduleId'  => 7,
              'controllerName'  => 'GnrBranchController',
              'tableName'  => 'gnr_branch',
              'operation'  => 'delete',
              'previousData'  => $previousdata,
              'primaryIds'  => [$previousdata->id]
            );
            Service::createLog($logArray);
            $data = array(
              'responseTitle' =>  'Success!',
              'responseText'  =>  'Branch Deleted Successfully'
            );
            return response()->json($data);
          }

          public function projectTypeChanged(Request $req){
            if($req->projectId!==''){
              $projectTypeId = DB::table('gnr_project_type')->where('projectId',$req->projectId)->pluck('name', 'id');
              return response()->json($projectTypeId);
            }
            else{
             $projectTypeId = DB::table('gnr_project_type')->pluck('name', 'id');
             return response()->json($projectTypeId);
           }
         }
       }
