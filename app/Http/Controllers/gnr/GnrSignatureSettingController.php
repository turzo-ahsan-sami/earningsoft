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
use App\Http\Controllers\microfin\MicroFin;

class GnrSignatureSettingController extends Controller{

    public function index(){

        $modules = DB::table('gnr_module')->get();
        $groups = DB::table('gnr_group')->get();
        $companies = DB::table('gnr_company')->get();
        $projects = DB::table('gnr_project')->get();

        $sigSettings = GnrSignatureSetting::all();

        $data = array(
            'modules'       => $modules,
            'groups'        => $groups,
            'companies'     => $companies,
            'projects'      => $projects,
            'sigSettings'   => $sigSettings,
        );

        return view('gnr/signatureSetting/signatureSettingList',$data);
        
    }

    public function addSignatureSetting(){
        
        $modules = [''=>'Select'] + MicroFin::getModuleList();
        $groups = [''=>'Select'] + MicroFin::getGroupList();
        $companies = [''=>'Select'];// + MicroFin::getCompanyList();
        $projects = [''=>'Select'];// + MicroFin::getProjectList();

        $data = array(
            'modules'       => $modules,
            'groups'        => $groups,
            'companies'     => $companies,
            'projects'      => $projects,
        );
        return view('gnr/signatureSetting/addSignatureSetting',$data);
    }

    public function checkIsExits(Request $req){
        $isExits = (int) DB::table('gnr_signatory')
        ->where('moduleId',$req->module)
        ->where('groupId',$req->group)
        ->where('companyId',$req->company)
        ->where('projectId',$req->project)
        ->where('isHeadOffice',$req->isHeadOffice)
        ->value('id');

        if ($isExits) {
            return response::json('data alreay exits');
        }
        else{
            return $this->storeSignatureSetting($req);
        }
    }

    public function storeSignatureSetting($req){

        $info = array();

        foreach ($req->sigRole as $key => $roleId) {
            $info[$key]['signatureRoleId'] = $roleId;
            $info[$key]['empPositionId'] = $req->empPosition[$key];
        }

        if ($req->isHeadOffice==1) {
            foreach ($req->empId as $key => $empId) {
                $info[$key]['empId'] = $empId;
            }
        }

        DB::table('gnr_signatory')->insert([
            'moduleId'      => $req->module,
            'groupId'       => $req->group,
            'companyId'     => $req->company,
            'projectId'     => $req->project,
            'isHeadOffice'  => $req->isHeadOffice,
            'signatureNum'  => $req->numOfSignature,
            'signatureInfo' => json_encode($info),
            'createdDate'   => Carbon::now()
        ]);

        $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Data stored successfully.'
        );

        return response::json($data);
    }

    public function updateSignatureSetting(Request $req){

        $info = array();

        foreach ($req->sigRole as $key => $roleId) {
            $info[$key]['signatureRoleId'] = $roleId;
            $info[$key]['empPositionId'] = $req->empPosition[$key];
        }

        if ($req->isHeadOffice==1) {
            foreach ($req->empId as $key => $empId) {
                $info[$key]['empId'] = $empId;
            }
        }

        DB::table('gnr_signatory')
        ->where([
            'moduleId'      => $req->module,
            'groupId'       => $req->group,
            'companyId'     => $req->company,
            'projectId'     => $req->project,
            'isHeadOffice'  => $req->isHeadOffice,
        ])
        ->update([
            'signatureNum'  => $req->numOfSignature,
            'signatureInfo' => json_encode($info)
        ]);

        $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Data updated successfully.'
        );

        return response::json($data);
    }

    public function getRolesForSignature(){
        $signatureRoles = DB::table('gnr_signature_role')->pluck('name','id')->toArray();       
        return response::json($signatureRoles);
    }

    public function getPositionsForSignature(Request $req){
        $empPositionIds = DB::table('hr_emp_org_info')->where('job_status','Present');
        if ($req->groupId!='') {
            $empPositionIds = $empPositionIds->where('group_id_fk',$req->groupId);
        }
        if ($req->companyId!='') {
            $empPositionIds = $empPositionIds->where('company_id_fk',$req->companyId);
        }
        if ($req->projectId!='') {
            $empPositionIds = $empPositionIds->where('project_id_fk',$req->projectId);
        }
        if ($req->isHeadOffice==1) {
            $empPositionIds = $empPositionIds->where('branch_id_fk',1);
        }
        if ($req->isHeadOffice==0) {
            $empPositionIds = $empPositionIds->where('branch_id_fk','!=',1);
        }
        $empPositionIds = $empPositionIds->pluck('position_id_fk')->toArray();

        $empPositions = DB::table('hr_settings_position')->whereIn('id',$empPositionIds)->orderBy('name')->pluck('name','id')->toArray();

        return response::json($empPositions);
    }

    public function getEmpoyeeForSignature(Request $req){
        // this is only applicable for head office, so all employee is taken from head office.
        $empIds = DB::table('hr_emp_org_info')->where('job_status','Present')->where('branch_id_fk',1);
        if ($req->empPositionId!='') {
            $empIds = $empIds->where('position_id_fk',$req->empPositionId);
        }

        $empIds = $empIds->pluck('emp_id_fk')->toArray();

        $employees = DB::table('hr_emp_general_info')
        ->whereIn('id',$empIds)
        ->select(DB::raw("CONCAT(emp_id,' - ',emp_name_english) AS name"),'id')
        ->pluck('name','id')
        ->all();

        return response::json($employees);
    }

    public function getSignatureTemplate(Request $req){

        $moduleId = $req->moduleId;
        $groupId = Auth::user()->group_id_fk;
        $companyId = Auth::user()->company_id_fk;
        $projectId = Auth::user()->project_id_fk;
        $branchId = Auth::user()->branchId;

        if (Auth::user()->id==1) {
            $groupId = 1;
            $companyId = 1;
            $projectId = 1;
            $projectTypeId = 3;
            $branchId = 1;
        }

        $signatorInfo = DB::table('gnr_signatory')
        ->where('moduleId',$moduleId)
        ->where('groupId',$groupId)
        ->where('companyId',$companyId)
        ->where('projectId',$projectId);

        if ($branchId==1) {
            $signatorInfo = $signatorInfo->where('isHeadOffice',1);
        }
        else{
            $signatorInfo = $signatorInfo->where('isHeadOffice',0);
        }

        $signatorInfo = $signatorInfo->value('signatureInfo');

        $signatorInfo = json_decode($signatorInfo);

        // get relative information
        $gnrSigRoles = DB::table('gnr_signature_role')->get();
        $hrEmps = DB::table('hr_emp_general_info')->get();
        $hrEmpOrgInfo = DB::table('hr_emp_org_info')->get();
        $hrPositions = DB::table('hr_settings_position')->get();

        $signatureRoles = array();
        $employeeNames = array();
        $employeeIds = array();
        $positionNames = array();

        if ($branchId==1) {
            foreach ($signatorInfo as $signator) {
                $signatureRole = $gnrSigRoles->where('id',$signator->signatureRoleId)->max('name');

                $hrEmp = $hrEmps->where('id',$signator->empId)->first();
                $employeeName = $hrEmp->emp_name_english;
                $employeeId = $hrEmp->emp_id;
                $positionName = $hrPositions->where('id',$signator->empPositionId)->first()->name;
                
                array_push($signatureRoles,$signatureRole);
                array_push($employeeNames,$employeeName);
                array_push($employeeIds,$employeeId);
                array_push($positionNames,$positionName);
            }
        }
        else{
            foreach ($signatorInfo as $signator) {
                $signatureRole = $gnrSigRoles->where('id',$signator->signatureRoleId)->max('name');


                $hrEmpId = $hrEmpOrgInfo->where('group_id_fk',$groupId)->where('company_id_fk',$companyId)->where('project_id_fk',$projectId)->where('branch_id_fk',$branchId)->where('position_id_fk',(int)$signator->empPositionId)->first()->emp_id_fk;
                $hrEmp = $hrEmps->where('id',$hrEmpId)->first();
                $employeeName = $hrEmp->emp_name_english;
                $employeeId = $hrEmp->emp_id;
                $positionName = $hrPositions->where('id',$signator->empPositionId)->first()->name;
                
                array_push($signatureRoles,$signatureRole);
                array_push($employeeNames,$employeeName);
                array_push($employeeIds,$employeeId);
                array_push($positionNames,$positionName);
            }
        }
        

        $numOfSignators = count($signatureRoles);

        $data = array(
            'employeeNames'     => $employeeNames,
            'employeeIds'       => $employeeIds,
            'signatureRoles'    => $signatureRoles,
            'positionNames'     => $positionNames,
            'numOfSignators'    => $numOfSignators,
        );

        return view('gnr/signatureSetting/signaturetemplate',$data);
    }
}
