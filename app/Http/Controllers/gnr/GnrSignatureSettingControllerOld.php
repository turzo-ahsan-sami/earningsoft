<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\GnrProjectType;
use Validator;
use Response;
use DB;
use App\gnr\GnrSignatureSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Auth;

class GnrSignatureSettingController extends Controller{

 /*---------------view List-----------------------------*/
    public function index() {

        $gnrSignatureSettings = GnrSignatureSetting::all();

        return view('gnr/signatureSetting/signatureSettingList',['gnrSignatureSettings'=>$gnrSignatureSettings]);
                
    }

/*--------------create Form--------------------*/

    public function createSignatureSetting() {

        return view('gnr/signatureSetting/createSignatureSettingform');
           

    }
   /* ----------------------- User Branch change -----------------------*/

    public function changeUserBranch (Request $request){

        $branchId = (int)json_decode($request->branchId);
        if($request->branchId=='') {

            $employeeName =  DB::table('hr_emp_general_info')->select('id','emp_name_english','emp_id')->get();

        }else {

            $branches = [$request->branchId];

            $filteredEmployee = DB::table('hr_emp_org_info')->whereIn('branch_id_fk',$branches)->pluck('emp_id_fk');

            $employeeName =  DB::table('hr_emp_general_info')->whereIn('id',$filteredEmployee)->select('id','emp_name_english','emp_id')->get();

        }
          
        $data = array(  

            'employeeName'       => $employeeName,
           
          );

        return response()->json($data);
    }


     /* ----------------------- User Role change -----------------------*/

    public function changeUserRole (Request $request){
     
        $branchId = (int)json_decode($request->branchId);
        $roleId = (int)json_decode($request->roleId);
        if($request->roleId=='') {

            $branches = [$request->branchId];

            $filteredEmployee = DB::table('hr_emp_org_info')->whereIn('branch_id_fk',$branches)->pluck('emp_id_fk');

            $employee =  DB::table('hr_emp_general_info')->whereIn('id',$filteredEmployee)->select('id','emp_name_english','emp_id')->get();
              }
        else {

            //$branches = [$request->roleId];

            $filteredRole = [$request->roleId];
            $branches = [$request->branchId];
            $filtereduserRoleId = DB::table('gnr_user_role')->whereIn('roleId',$filteredRole)->pluck('userIdFK');
        

            $filteredUser = DB::table('users')->whereIn('id',$filtereduserRoleId)->whereIn('branchId', $branches)->pluck('emp_id_fk');

            $employee = DB::table('hr_emp_general_info')->whereIn('id',$filteredUser)->select('id','emp_name_english','emp_id')->get();

          }
          
        $data = array(            
            
            'employee'       => $employee,
           
        );

        return response()->json($data);
    }

        /*-----------------add data---------------------*/

    public function storeSignatureSetting(Request $request) {
  
        $rules = array(

            'module'                   => 'required',
            'group'                    => 'required',
            'company'                  => 'required',
            'project'                  => 'required',
            'projectType'              => 'required',
            'branch'                   => 'required',
            'numOfSignature'           => 'required'
        );

        if($request->numOfSignature=='1' ) {

            $rules = $rules + array('roleName' => 'required','employeeName' => 'required','signatureRoleName' => 'required',);
        }

        elseif($request->numOfSignature=='2' ) {

            $rules = $rules + array('roleName' => 'required','employeeName' => 'required','signatureRoleName' => 'required',);
            $rules = $rules + array('role1' => 'required','employee1' => 'required','signatureRole1' => 'required',);
            
        }

        elseif($request->numOfSignature=='3' ) {

            $rules = $rules + array('roleName' => 'required','employeeName' => 'required','signatureRoleName' => 'required',);
            $rules = $rules + array('role1' => 'required','employee1' => 'required','signatureRole1' => 'required',);
            $rules = $rules + array('role2' => 'required','employee2' => 'required','signatureRole2' => 'required',);
        }

        elseif($request->numOfSignature=='4' ) {

            $rules = $rules + array('roleName' => 'required','employeeName' => 'required','signatureRoleName' => 'required',);
            $rules = $rules + array('role1' => 'required','employee1' => 'required','signatureRole1' => 'required',);
            $rules = $rules + array('role2' => 'required','employee2' => 'required','signatureRole2' => 'required',);  
            $rules = $rules + array('role3' => 'required','employee3' => 'required','signatureRole3' => 'required',);
        }
        
        $attributeNames = array(

            'module'                          => 'Module Name',
            'group'                           => 'Group Name',
            'company'                         => 'Company Name',
            'project'                         => 'Project',
            'projectType'                     => 'ProjectType',
            'branch'                          => 'Branch',
            'numOfSignature'                  => 'Number Of Signature',
            'roleName'                        => 'Role Name',
            'employeeName'                    => 'Employee',
            'signatureRoleName'               => 'Signature Role',
            'role1'                           => 'Role Name',
            'employee1'                       => 'Employee Name',
            'signatureRole1'                  => 'Signature Role',
            'signatureRole2'                  => 'Signature Role',
            'employee2'                       => 'Employee',
            'role2'                           => 'Role'


        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);
        $customErrorArray = array();

        if ($validator->fails()) {

            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }  

        else {

            $signatureSetting = $this->makeSignatureSettingString($request->roleIds,$request->empolyeeIds,$request->signatureRoleIds);
            $gnrSignatureSetting = new GnrSignatureSetting;
            $gnrSignatureSetting->moduleId = $request->module;
            $gnrSignatureSetting->groupId = $request->group;
            $gnrSignatureSetting->companyId = $request->company;
            $gnrSignatureSetting->projectId = $request->project;
            $gnrSignatureSetting->projectTypeId = $request->projectType;
            $gnrSignatureSetting->branchId = $request->branch;
            $gnrSignatureSetting->signatureNum = $request->numOfSignature;
            $gnrSignatureSetting->signatureInfo = $signatureSetting;
            $gnrSignatureSetting->createdDate = Carbon::now();
            $gnrSignatureSetting->save(); 
                         
            return response::json();
        }
        
    }

    private function makeSignatureSettingString($roleIds,$empolyeeIds,$signatureRoleIds) {

        $signatureSetting = "[";
        foreach ($roleIds as $key => $roleId) {
            $signatureSetting = $signatureSetting .'{' . $roleId.':'.$empolyeeIds[$key].':'.$signatureRoleIds[$key].'}';

            if ($key<count($roleIds)-1) {
                     $signatureSetting = $signatureSetting .',';
            }
        }

        $signatureSetting = $signatureSetting ."]";

        return $signatureSetting;
    }



      /*----------------update Data---------------------------*/


    public function signatureSettingInfoupdate(Request $request) {

        $rules = array(
            'module'                      => 'Module Name',
            'group'                       => 'Group Name',
            'company'                     => 'Company Name',
            'project'                     => 'Project',
            'projectType'                 => 'ProjectT ype',
            'branch'                      => 'Branch',
            'numOfSignature'              => 'Number Of Signature'

        );
        $attributeNames = array(
            'module'                      => 'Module Name',
            'group'                       => 'Group Name',
            'company'                     =>'Company Name',
            'project'                     =>'Project',
            'projectType'                 => 'ProjectT ype',
            'branch'                      => 'Branch',
            'numOfSignature'              => 'Number Of Signature'
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()) {

            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        else {

            $signatureSetting = $this->makeSignatureSettingString($request->roleIds,$request->empolyeeIds,$request->signatureRoleIds);

            $gnrSignatureSetting = GnrSignatureSetting::find($request->id);
            $gnrSignatureSetting->moduleId = $request->moduleId;
            $gnrSignatureSetting->groupId = $request->groupId;
            $gnrSignatureSetting->companyId = $request->companyId;
            $gnrSignatureSetting->projectId = $request->projectId;
            $gnrSignatureSetting->projectTypeId = $request->peojectTypeId;
            $gnrSignatureSetting->branchId = $request->branchId;
            $gnrSignatureSetting->signatureNum = $request->signatureNum;
            $gnrSignatureSetting->signatureInfo = $signatureSetting;
            $gnrSignatureSetting->createdDate = Carbon::now();
            $gnrSignatureSetting->save(); 

            return response::json('success');
        }

    }
 

    /*------------ Delete Data--------------------*/

    public function deleteSignatureSetting(Request $request) {

        $gnrSignatureSetting=GnrSignatureSetting::find($request->id);
        $gnrSignatureSetting->delete();

        return response::json('success');

    }

 /*------------------------------View data----------------------------*/

    public function viewSignatureSettingInfo(Request $request) {

        $gnrSignatureSetting = GnrSignatureSetting::find($request->id);

        $moduleName = DB::table('gnr_module')->where('id',$gnrSignatureSetting->moduleId)->value('name');
        $groupName = DB::table('gnr_group')->where('id',$gnrSignatureSetting->groupId)->value('name');
        $companyName = DB::table('gnr_company')->where('id',$gnrSignatureSetting->companyId)->value('name');
        $projectName = DB::table('gnr_project')->where('id',$gnrSignatureSetting->projectId)->value('name');
        $projectTypeName = DB::table('gnr_project_type')->where('id',$gnrSignatureSetting->projectTypeId)->value('name');
        $branchName = DB::table('gnr_branch')->where('id',$gnrSignatureSetting->branchId)->value('name');

        $moduleId = DB::table('gnr_module')->where('id',$gnrSignatureSetting->moduleId)->value('id');
        $groupId = DB::table('gnr_group')->where('id',$gnrSignatureSetting->groupId)->value('id');
        $companyId = DB::table('gnr_company')->where('id',$gnrSignatureSetting->companyId)->value('id');
        $projectId = DB::table('gnr_project')->where('id',$gnrSignatureSetting->projectId)->value('id');
        $projectTypeId = DB::table('gnr_project_type')->where('id',$gnrSignatureSetting->projectTypeId)->value('id');
        $branchId = DB::table('gnr_branch')->where('id',$gnrSignatureSetting->branchId)->value('id');

        $roleId = array();
        $employeeId = array();
        $signatureRoleId = array();

        $signatureinfoIdArray = explode(',',str_replace(['"','[',']','{','}'], '', $gnrSignatureSetting->signatureInfo));


        foreach ($signatureinfoIdArray as $key => $signatureinfoId) {

            $signature = explode(':',$signatureinfoId);

            $roleIdForName = DB::table('gnr_role')->where('id',$signature[0])->value('id');
            $employeeIdForName = DB::table('hr_emp_general_info')->where('id',$signature[1])->value('id');
            $signatureIdForName = DB::table('gnr_signature_role')->where('id',$signature[2])->value('id');

            array_push($roleId,$roleIdForName);
            array_push($employeeId,$employeeIdForName);
            array_push($signatureRoleId,$signatureIdForName);

        }

        $role = array();
        $employee = array();
        $signatureRole = array();

        $signatureinfoArray = explode(',',str_replace(['"','[',']','{','}'], '', $gnrSignatureSetting->signatureInfo));

        foreach ($signatureinfoArray as $key => $signatureinfo) {

            $signature = explode(':',$signatureinfo);

            $roleName = DB::table('gnr_role')->where('id',$signature[0])->value('name');
            $employeeName = DB::table('hr_emp_general_info')->where('id',$signature[1])->value('emp_name_english');
            $signatureName = DB::table('gnr_signature_role')->where('id',$signature[2])->value('name');

            array_push($role,$roleName);
            array_push($employee,$employeeName);
            array_push($signatureRole,$signatureName);
        }

        $data = array(

            'gnrSignatureSetting'       => $gnrSignatureSetting,
            'moduleName'                => $moduleName,
            'groupName'                 => $groupName,
            'role'                      => $role,
            'employee'                  => $employee,
            'signatureRole'             => $signatureRole,
            'companyName'               => $companyName,
            'projectName'               => $projectName,
            'projectTypeName'           => $projectTypeName,
            'branchName'                => $branchName,
            'moduleId'                  => $moduleId,
            'groupId'                   => $groupId,
            'companyId'                 => $companyId,
            'projectId'                 => $projectId,
            'projectTypeId'             => $projectTypeId,
            'branchId'                  => $branchId,
            'roleId'                    =>$roleId,
            'employeeId'                =>$employeeId,
            'signatureRoleId'           =>$signatureRoleId

        );

            return response::json($data);
        
    }

    public function getDataSignatureSetting(Request $request){
        $gnrSignatureSetting = GnrSignatureSetting::find($request->id);

        $data =   array(

            'signatureInfo' => explode(',',str_replace(['"','[',']','{','}'], '', $gnrSignatureSetting->signatureInfo)),
        );

    }

    public function getSignatureTemplate(Request $req){

        $moduleId = $req->moduleId;

        $groupId = Auth::user()->group_id_fk;
        $companyId = Auth::user()->company_id_fk;
        $projectId = Auth::user()->project_id_fk;
        $projectTypeId = Auth::user()->project_type_id_fk;
        $branchId = Auth::user()->branchId;

        if (Auth::user()->id==1) {
            $groupId = 1;
            $companyId = 1;
            $projectId = 1;
            $projectTypeId = 3;
            $branchId = 1;
        }

        $signatorInfos = DB::table('gnr_signatory')
                            ->where('moduleId',$moduleId)
                            ->where('groupId',$groupId)
                            ->where('companyId',$companyId)
                            ->where('projectId',$projectId)
                            ->where('projectTypeId',$projectTypeId)
                            ->where('branchId',$branchId)
                            ->value('signatureInfo');

        $signatorInfos = explode(',',str_replace(['"','[',']','{','}'], '', $signatorInfos));


        $signatureRoles = array();
        $employeeNames = array();
        $positionNames = array();

        $gnrRoles = DB::table('gnr_role')->get();
        $hrEmps = DB::table('hr_emp_general_info')->get();
        $hrEmpOrgInfo = DB::table('hr_emp_org_info')->get();
        $gnrSigRoles = DB::table('gnr_signature_role')->get();
        $hrPositions = DB::table('hr_settings_position')->get();

        foreach ($signatorInfos as $signatorInfo) {
            $signature = explode(':',$signatorInfo);

            $signatureRole = $gnrSigRoles->where('id',$signature[2])->max('name');

            $hrEmp = $hrEmps->where('id',$signature[1])->first();
            $positionId = $hrEmpOrgInfo->where('emp_id_fk',$hrEmp->id)->first()->position_id_fk;

            $employeeName = $hrEmp->emp_name_english;
            $positionName = $hrPositions->where('id',$positionId)->first()->name;

            array_push($signatureRoles,$signatureRole);
            array_push($employeeNames,$employeeName);
            array_push($positionNames,$positionName);
        }

        $numOfSignators = count($signatureRoles);

        $data = array(
            'employeeNames'     => $employeeNames,
            'signatureRoles'    => $signatureRoles,
            'positionNames'     => $positionNames,
            'numOfSignators'    => $numOfSignators,
        );

        return view('gnr/signatureSetting/signaturetemplate',$data);
    }  
}
