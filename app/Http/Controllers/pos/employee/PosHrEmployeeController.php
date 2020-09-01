<?php
namespace App\Http\Controllers\pos\employee;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\pos\HrGnrEmployeeInfo;
use App\pos\HrGnrOrganaizationInfo;
use App\User;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PosHrEmployeeController extends Controller
{


  public function __construct() {


  }

  public function index(Request $req){
    $user = Auth::user();
    Session::put('branchId', $user->branchId);
    $gnrBranchId = Session::get('branchId');
    $logedUserName = $user->name;
    
        //dd($req->search_project_id_fk,$req->filter_branch,$req->search_position_id_fk);
        //$users = DB::table('users')->paginate(15);
    $hrAllSearchingEmployeeInfos = DB::table('hr_emp_general_info')
    ->join('hr_emp_org_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
    ->select('hr_emp_general_info.*',
      'hr_emp_org_info.branch_id_fk',
      'hr_emp_org_info.project_id_fk',
      'hr_emp_org_info.position_id_fk',
      'hr_emp_org_info.status',
      'hr_emp_org_info.joining_date'
  );

    if($req->search_project_id_fk != null) {
        $hrAllEmployeeInfos =$hrAllSearchingEmployeeInfos->where('project_id_fk', $req->search_project_id_fk); 
    }
    if($req->filter_branch != null) {
        $hrAllEmployeeInfos =$hrAllSearchingEmployeeInfos->where('branch_id_fk', $req->filter_branch); 
    }

    if($req->search_position_id_fk != null) {
        $hrAllEmployeeInfos =$hrAllSearchingEmployeeInfos->where('position_id_fk', $req->search_position_id_fk); 
    }

    if($req->filter_nid_or_birth != null) {
        $hrAllEmployeeInfos =$hrAllSearchingEmployeeInfos->where('nid_no', '=', $req->filter_nid_or_birth)->orWhere('birth_certificate_no',  '=', $req->filter_nid_or_birth); 
    }

    if($req->filter_name_or_id != null) {
        $hrAllEmployeeInfos =$hrAllSearchingEmployeeInfos->where('emp_name_english', 'like', '%' .$req->filter_name_or_id.'%')->orWhere('emp_id','=', $req->filter_name_or_id); 
    }

    if($req->filter_status != null) {
        $hrAllEmployeeInfos =$hrAllSearchingEmployeeInfos->where('hr_emp_org_info.status', '=', $req->filter_status); 
    }

    $hrAllEmployeeInfos = $hrAllSearchingEmployeeInfos->orderBy('emp_id')->paginate(15);


       //return view('pos/employee/employeeList',['hrAllEmployeeInfos'=>$hrAllSearchingEmployeeInfos]);

    return view('pos/employee/employeeList',['hrAllEmployeeInfos'=>$hrAllEmployeeInfos]);

      //return view('pos/employee/employeeList',['hrAllEmployeeInfos'=>$hrAllEmployeeInfos]);
    
}
public function posAddHrEmployee(){


    return view('pos/employee/addHrEmployee');
}

public function addItem(Request $req) {

    $rules = array(
        'emp_id'              => 'required',
        'father_name'         => 'required',
        'sex'                 => 'required',
        'date_of_birth'       => 'required',
        'nid_no'              => 'required',
        'mobile_no'           => 'required',
        'pre_div_id'          => 'required',
        'pre_dis_id'          => 'required',
        'pre_upa_id'          => 'required',
        'present_address'     => 'required',
        'company_id_fk'       => 'required',
        'project_type_id_fk'  => 'required',
        'position_id_fk'      => 'required',
        'user_id'             => 'required',
        'employeeName'        => 'required',
        'mother_name'         => 'required',
        'religion'            => 'required',
        'blood_group'         => 'required',
        'birth_certificate_no'=> 'required',
        'email'               => 'required',
        'per_div_id'          => 'required',
        'per_dis_id'          => 'required',
        'per_upa_id'          => 'required',
        'permanent_address'   => 'required',
        'project_id_fk'       => 'required',
        'branch_id_fk'        => 'required',
        'joining_date'        => 'required',
        'user_password'       => 'required',

    );
    
    $attributeNames = array(

        'emp_id'               => 'Empolyee id',
        'father_name'          => 'father Name',
        'sex'                  => 'sex',
        'date_of_birth'        => 'date of birth',
        'nid_no'               => 'national id',
        'mobile_no'            => 'mobile no',
        'pre_div_id'           => 'present divition name',
        'pre_dis_id'           => 'present distric name',
        'pre_upa_id'           => 'present upozila name',
        'present_addresse'     => 'present addresse',
        'company_id_fk'        => 'company name',
        'project_type_id_fk'   => 'project type',
        'position_id_fk'       => 'position',
        'user_id'              => 'user id',
        'employeeName'         => 'employee name',
        'mother_name'          => 'mother name',
        'religion'             => 'religion name',
        'blood_group'          => 'blood group',
        'birth_certificate_no' => 'birth certificate no',
        'email'                => 'email',
        'per_div_id'           => 'permanent divition name',
        'per_dis_id'           => 'permanent district name',
        'per_upa_id'           => 'permanent upozila name',
        'permanent_address'    => 'permanent address',
        'project_id_fk'        => 'project name',
        'branch_id_fk'         => 'branch name',
        'joining_date'         => 'joining date',
        'user_password'        => 'user password'
    );


    $validator = Validator::make ( Input::all (), $rules);
    $validator->setAttributeNames($attributeNames);
    if ($validator->fails()) {

        return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    } else {

        $hrGnrEmployeeInfo = new HrGnrEmployeeInfo;
        $hrGnrEmployeeInfo->emp_id                    = $req->emp_id;
        $hrGnrEmployeeInfo->emp_name_english          = $req->employeeName;
        $hrGnrEmployeeInfo->father_name_english       = $req->father_name;
        $hrGnrEmployeeInfo->mother_name_english       = $req->mother_name;
        $hrGnrEmployeeInfo->sex                       = $req->sex;
        $hrGnrEmployeeInfo->religion                  = $req->religion;
        $hrGnrEmployeeInfo->date_of_birth             = $req->date_of_birth;
        $hrGnrEmployeeInfo->blood_group               = $req->blood_group;
        $hrGnrEmployeeInfo->mobile_number             = $req->mobile_no;
        $hrGnrEmployeeInfo->nid_no                    = $req->nid_no;
        $hrGnrEmployeeInfo->email                     = $req->email;
        $hrGnrEmployeeInfo->birth_certificate_no      = $req->birth_certificate_no;
        $hrGnrEmployeeInfo->present_address           = $req->present_address;
        $hrGnrEmployeeInfo->permanent_address         = $req->permanent_address;
        $hrGnrEmployeeInfo->pre_div_id                = $req->pre_div_id;
        $hrGnrEmployeeInfo->pre_dis_id                = $req->pre_dis_id;
        $hrGnrEmployeeInfo->pre_upa_id                = $req->pre_upa_id;
        $hrGnrEmployeeInfo->pre_uni_id                = $req->pre_uni_id;
        $hrGnrEmployeeInfo->per_div_id                = $req->per_div_id;
        $hrGnrEmployeeInfo->per_dis_id                = $req->per_dis_id;
        $hrGnrEmployeeInfo->per_upa_id                = $req->per_upa_id;
        $hrGnrEmployeeInfo->per_uni_id                = $req->per_uni_id;
        $hrGnrEmployeeInfo->save();
        
        
        $hrGnrOrganaizationInfo = new HrGnrOrganaizationInfo;

        $hrGnrOrganaizationInfo->emp_id_fk            = $hrGnrEmployeeInfo->id;
        $hrGnrOrganaizationInfo->joining_date         = $req->joining_date;
        $hrGnrOrganaizationInfo->company_id_fk        = $req->company_id_fk;
        $hrGnrOrganaizationInfo->project_id_fk        = $req->project_id_fk;
        $hrGnrOrganaizationInfo->project_type_id_fk   = $req->project_type_id_fk;
        $hrGnrOrganaizationInfo->branch_id_fk         = $req->branch_id_fk;
        $hrGnrOrganaizationInfo->position_id_fk       = $req->position_id_fk;
        $hrGnrOrganaizationInfo->status               = $req->status;
        $hrGnrOrganaizationInfo->save();

        $userInfo = new User;
        $userInfo->username              = $req->user_id;        
        $userInfo->password              = bcrypt($req->user_password);        
        $userInfo->emp_id_fk             = $hrGnrEmployeeInfo->id;        
        $userInfo->company_id_fk         = $req->company_id_fk;        
        $userInfo->project_id_fk         = $req->project_id_fk;        
        $userInfo->project_type_id_fk    = $req->project_type_id_fk;        
        $userInfo->branchId              = $req->branch_id_fk;        
        
        $userInfo->save();
    }

    return response()->json('success');

}

    //========START FILTARING FOR ADDRESS====
public function preDistricFiltering(Request $req) {
    $preDistricData = DB::table('gnr_district')->where('divisionId',$req->pre_div_id)->select('id','name')->get();
    $data =  array('preDistricData' => $preDistricData
);
    return response()->json($data);
}

public function preUpzillaDataFiltering(Request $req) {
    $preUpzillaData = DB::table('gnr_upzilla')->where('divisionId',$req->pre_div_id)->where('districtId',$req->pre_dis_id)->select('id','name')->get();
    $data =  array(
        'preUpzillaData' => $preUpzillaData
    );
    return response()->json($data);
}


public function preUnionDataFiltering(Request $req) {
    $preUnionData = DB::table('gnr_union')->where('divisionId',$req->pre_div_id)->where('districtId',$req->pre_dis_id)->where('upzillaId',$req->pre_upa_id)->select('id','name')->get();
    $data =  array(
        'preUnionData' => $preUnionData
    );
    return response()->json($data);
}


public function projectTypeFiltering(Request $req) {
    $projectTypeData =DB::table('gnr_project_type')
    ->where('projectId',$req->project_id_fk)->select(DB::raw("CONCAT(projectTypeCode ,' - ', name ) AS name"),'id')->get();
    $data =  array(
        'projectTypeData' => $projectTypeData
    );
    return response()->json($data);
}


    //END FILTARING FOR ADDRESS==============


public function employeeGetInfo(Request $req){
 $hrAllEmployeeGetInfos = DB::table('hr_emp_general_info')->select('hr_emp_general_info.*','hr_emp_org_info.company_id_fk','hr_emp_org_info.project_id_fk','hr_emp_org_info.project_type_id_fk','hr_emp_org_info.branch_id_fk','hr_emp_org_info.position_id_fk','hr_emp_org_info.status','hr_emp_org_info.joining_date','users.username')->join('hr_emp_org_info','hr_emp_org_info.emp_id_fk','=','hr_emp_general_info.id')->join('users','hr_emp_general_info.id','=','users.emp_id_fk')->where('hr_emp_general_info.id',$req->epmloyeeId)->first();
         //=========START PRESENT ADDRESS GETTING DATA===  
 $selectedPreDistricts = DB::table('gnr_district')->where('divisionId',$hrAllEmployeeGetInfos->pre_div_id)->select('id','name')->get();

 $selectedPreUpzillas = DB::table('gnr_upzilla')->where('divisionId',$hrAllEmployeeGetInfos->pre_div_id)->where('districtId',$hrAllEmployeeGetInfos->pre_dis_id)->select('id','name')->get();
 $selectedPreUnions = DB::table('gnr_union')->where('divisionId',$hrAllEmployeeGetInfos->pre_div_id)->where('districtId',$hrAllEmployeeGetInfos->pre_dis_id)->where('upzillaId',$hrAllEmployeeGetInfos->pre_upa_id)->select('id','name')->get();
        //=========END PRESENT ADDRESS GETTING DATA=== 

        //=========START PARMANENT ADDRESS GETTING DATA=== 
 $selectedPerDistricts = DB::table('gnr_district')->where('divisionId',$hrAllEmployeeGetInfos->per_div_id)->select('id','name')->get();

 $selectedPerUpzillas = DB::table('gnr_upzilla')->where('divisionId',$hrAllEmployeeGetInfos->per_div_id)->where('districtId',$hrAllEmployeeGetInfos->per_dis_id)->select('id','name')->get();
 $selectedPerUnions = DB::table('gnr_union')->where('divisionId',$hrAllEmployeeGetInfos->per_div_id)->where('districtId',$hrAllEmployeeGetInfos->per_dis_id)->where('upzillaId',$hrAllEmployeeGetInfos->per_upa_id)->select('id','name')->get();
        //=========END PARMANENT ADDRESS GETTING DATA=== 

 $selectedPojectTypes = DB::table('gnr_project_type')->where('projectId',$hrAllEmployeeGetInfos->project_id_fk)->select(DB::raw("CONCAT(projectTypeCode ,' - ', name ) AS name"),'id')->get();


 $data =  array(
    'selectedPreDistricts'=>$selectedPreDistricts,
    'selectedPreUpzillas'=>$selectedPreUpzillas,
    'selectedPreUnions'=>$selectedPreUnions,
    'selectedPerDistricts'=>$selectedPerDistricts,
    'selectedPerUpzillas'=>$selectedPerUpzillas,
    'selectedPerUnions'=>$selectedPerUnions,
    'selectedPojectTypes'=>$selectedPojectTypes,
    'hrAllEmployeeGetInfos' => $hrAllEmployeeGetInfos
);
 return response()->json($data);
}

public function updateHrEployeeInfo(Request $req){
 $rules = array(
    'emp_id'              => 'required',
    'father_name'         => 'required',
    'sex'                 => 'required',
    'date_of_birth'       => 'required',
    'nid_no'              => 'required',
    'mobile_no'           => 'required',
    'pre_div_id'          => 'required',
    'pre_dis_id'          => 'required',
    'pre_upa_id'          => 'required',
    'present_address'     => 'required',
    'company_id_fk'       => 'required',
    'project_type_id_fk'  => 'required',
    'position_id_fk'      => 'required',
    'user_id'             => 'required',
    'employeeName'        => 'required',
    'mother_name'         => 'required',
    'religion'            => 'required',
    'blood_group'         => 'required',
    'birth_certificate_no'=> 'required',
    'email'               => 'required',
    'per_div_id'          => 'required',
    'per_dis_id'          => 'required',
    'per_upa_id'          => 'required',
    'permanent_address'   => 'required',
    'project_id_fk'       => 'required',
    'branch_id_fk'        => 'required',
    'joining_date'        => 'required',

);

 $attributeNames = array(

    'emp_id'               => 'Empolyee id',
    'father_name'          => 'father Name',
    'sex'                  => 'sex',
    'date_of_birth'        => 'date of birth',
    'nid_no'               => 'national id',
    'mobile_no'            => 'mobile no',
    'pre_div_id'           => 'present divition name',
    'pre_dis_id'           => 'present distric name',
    'pre_upa_id'           => 'present upozila name',
    'present_addresse'     => 'present addresse',
    'company_id_fk'        => 'company name',
    'project_type_id_fk'   => 'project type',
    'position_id_fk'       => 'position',
    'user_id'              => 'user id',
    'employeeName'         => 'employee name',
    'mother_name'          => 'mother name',
    'religion'             => 'religion name',
    'blood_group'          => 'blood group',
    'birth_certificate_no' => 'birth certificate no',
    'email'                => 'email',
    'per_div_id'           => 'permanent divition name',
    'per_dis_id'           => 'permanent district name',
    'per_upa_id'           => 'permanent upozila name',
    'permanent_address'    => 'permanent address',
    'project_id_fk'        => 'project name',
    'branch_id_fk'         => 'branch name',
    'joining_date'         => 'joining date',
    'user_password'        => 'user password'
);


 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails()) {

    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
} else {

    $hrGnrEmployeeInfo = HrGnrEmployeeInfo::find($req->EMepmloyeeId);
    $hrGnrEmployeeInfo->emp_id                    = $req->emp_id;
    $hrGnrEmployeeInfo->emp_name_english          = $req->employeeName;
    $hrGnrEmployeeInfo->father_name_english       = $req->father_name;
    $hrGnrEmployeeInfo->mother_name_english       = $req->mother_name;
    $hrGnrEmployeeInfo->sex                       = $req->sex;
    $hrGnrEmployeeInfo->religion                  = $req->religion;
    $hrGnrEmployeeInfo->date_of_birth             = $req->date_of_birth;
    $hrGnrEmployeeInfo->blood_group               = $req->blood_group;
    $hrGnrEmployeeInfo->mobile_number             = $req->mobile_no;
    $hrGnrEmployeeInfo->nid_no                    = $req->nid_no;
    $hrGnrEmployeeInfo->email                     = $req->email;
    $hrGnrEmployeeInfo->birth_certificate_no      = $req->birth_certificate_no;
    $hrGnrEmployeeInfo->present_address           = $req->present_address;
    $hrGnrEmployeeInfo->permanent_address         = $req->permanent_address;
    $hrGnrEmployeeInfo->pre_div_id                = $req->pre_div_id;
    $hrGnrEmployeeInfo->pre_dis_id                = $req->pre_dis_id;
    $hrGnrEmployeeInfo->pre_upa_id                = $req->pre_upa_id;
    $hrGnrEmployeeInfo->pre_uni_id                = $req->pre_uni_id;
    $hrGnrEmployeeInfo->per_div_id                = $req->per_div_id;
    $hrGnrEmployeeInfo->per_dis_id                = $req->per_dis_id;
    $hrGnrEmployeeInfo->per_upa_id                = $req->per_upa_id;
    $hrGnrEmployeeInfo->per_uni_id                = $req->per_uni_id;
    $hrGnrEmployeeInfo->save();

    $hrGnrOrganaizationInfo = HrGnrOrganaizationInfo::where('emp_id_fk',$req->EMepmloyeeId)->first();
    $hrGnrOrganaizationInfo->emp_id_fk            = $req->EMepmloyeeId;
    $hrGnrOrganaizationInfo->joining_date         = $req->joining_date;
    $hrGnrOrganaizationInfo->company_id_fk        = $req->company_id_fk;
    $hrGnrOrganaizationInfo->project_id_fk        = $req->project_id_fk;
    $hrGnrOrganaizationInfo->project_type_id_fk   = $req->project_type_id_fk;
    $hrGnrOrganaizationInfo->branch_id_fk         = $req->branch_id_fk;
    $hrGnrOrganaizationInfo->position_id_fk       = $req->position_id_fk;
    $hrGnrOrganaizationInfo->status               = $req->status;
    $hrGnrOrganaizationInfo->save();

    $userInfo = User::where('emp_id_fk',$req->EMepmloyeeId)->first();
    $userInfo->username              = $req->user_id;
    if($req->user_password !=null){
        $userInfo->password              = bcrypt($req->user_password);
    }
    $userInfo->emp_id_fk             = $req->EMepmloyeeId;       
    $userInfo->company_id_fk         = $req->company_id_fk;        
    $userInfo->project_id_fk         = $req->project_id_fk;        
    $userInfo->project_type_id_fk    = $req->project_type_id_fk;        
    $userInfo->branchId              = $req->branch_id_fk;        
    $userInfo->save();
}
return response()->json('success');
}

public function hrDeleteEmployee(Request $req) {

    HrGnrEmployeeInfo::find($req->id)->delete();
    HrGnrOrganaizationInfo::where('emp_id_fk',$req->id)->delete();
    User::where('emp_id_fk',$req->id)->delete();
    return response()->json('success');

}


public function hrDetailsEmployee($employeeId) {

    $hrAllEmployeeDetails = DB::table('hr_emp_general_info')->select('hr_emp_general_info.*','hr_emp_org_info.company_id_fk','hr_emp_org_info.project_id_fk','hr_emp_org_info.project_type_id_fk','hr_emp_org_info.branch_id_fk','hr_emp_org_info.position_id_fk','hr_emp_org_info.status','hr_emp_org_info.joining_date','users.username')->join('hr_emp_org_info','hr_emp_org_info.emp_id_fk','=','hr_emp_general_info.id')->join('users','hr_emp_general_info.id','=','users.emp_id_fk')->where('hr_emp_general_info.id',$employeeId)->first();

    return view('pos/employee/employeeDetails',['hrAllEmployeeDetails'=>$hrAllEmployeeDetails]);

}

public function branchFilteringByProject(Request $req) {
   $branchData =DB::table('gnr_branch')
   ->where('projectId',$req->search_project_id_fk)->select('branchCode','name','id')->where('id','!=',1)->get();
   $data =  array(
    'branchData' => $branchData
);
   return response()->json($data);
}

}