<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
// use App\microfin\employee\HrGnrEmployeeInfo;
// use App\microfin\employee\HrGnrOrganaizationInfo;
use App\User;
use App\gnr\GnrEmployee;
use App\gnr\GnrDepartment;
use App\gnr\GnrPosition;
use Validator;
use Response;
use DB;
use Route;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App;
use App\gnr\GnrBranch;


class EmployeeController extends Controller
{


    public $route;

    public function __construct()
    {

        $this->route = $this->layoutDynamic();
    }

    public function layoutDynamic()
    {

        $path = Route::current()->action['prefix'];

        // dd($path);

        if ($path == '/mfn') {
            $layout = 'layouts/microfin_layout';
        } elseif ($path == '/acc') {
            $layout = 'layouts/acc_layout';
        } elseif ($path == '/gnr') {
            $layout = 'layouts/gnr_layout';
        } elseif ($path == '/inv') {
            $layout = 'layouts/inventory_layout';
        } elseif ($path == '/fams') {
            $layout = 'layouts/fams_layout';
        } elseif ($path == '/pos') {
            $layout = 'layouts/pos_layout';
        }

        $route = array(
            'layout' => $layout,
            'path' => $path
        );

        return $route;

    }

    public function index(Request $req)
    {
        $user = Auth::user();
        Session::put('branchId', $user->branchId);
        Session::put('companyId', $user->company_id_fk);
        $gnrcompanyId = Session::get('companyId');
        $gnrBranchId = Session::get('branchId');
        $logedUserName = $user->name;
       
        
        // $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
        $branchIdArray = GnrBranch::where('companyId', $gnrcompanyId)->pluck('id')->toArray();
        // dd($branchIdArray);

        //dd($req->search_project_id_fk,$req->filter_branch,$req->search_position_id_fk);
        //$users = DB::table('users')->paginate(15);
        $hrAllSearchingEmployeeInfos = GnrEmployee::whereIn('branchId', $branchIdArray);
        // ->join('hr_emp_org_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id');
    //     if ($gnrBranchId != 1) {
    //        /* $hrAllSearchingEmployeeInfos = $hrAllSearchingEmployeeInfos->where('hr_emp_org_info.branch_id_fk', $gnrBranchId);*/

    //        $hrAllSearchingEmployeeInfos = $hrAllSearchingEmployeeInfos->whereIn('hr_emp_org_info.branch_id_fk', $branchIdArray);
    //    }

    //    $hrAllSearchingEmployeeInfos = $hrAllSearchingEmployeeInfos
    //                                 ->select('hr_emp_general_info.*',
    //                                     'hr_emp_org_info.branch_id_fk',
    //                                     'hr_emp_org_info.project_id_fk',
    //                                     'hr_emp_org_info.position_id_fk',
    //                                     'hr_emp_org_info.status',
    //                                     'hr_emp_org_info.joining_date'
    //                                 );

    //    if ($req->search_project_id_fk != null) {
    //     $hrAllEmployeeInfos = $hrAllSearchingEmployeeInfos->where('project_id_fk', $req->search_project_id_fk);
    //     }
        // if ($req->filter_branch != null) {
        //     $hrAllEmployeeInfos = $hrAllSearchingEmployeeInfos->where('branchId', $req->filter_branch);
        // }

        // if ($req->search_position_id_fk != null) {
        //     $hrAllEmployeeInfos = $hrAllSearchingEmployeeInfos->where('position_id_fk', $req->search_position_id_fk);
        // }

        // if ($req->filter_nid_or_birth != null) {
        //     $hrAllEmployeeInfos = $hrAllSearchingEmployeeInfos->where('nationalId', '=', $req->filter_nid_or_birth);
        // }

        if ($req->filter_name_or_id != null) {
            $hrAllEmployeeInfos = $hrAllSearchingEmployeeInfos->where('name', 'like', '%' . $req->filter_name_or_id . '%')->orWhere('employeeId', '=', $req->filter_name_or_id);
        }
        //dd($req->filter_status);
        if ($req->filter_status != null) {
            $hrAllEmployeeInfos = $hrAllSearchingEmployeeInfos->where('status.', '=', $req->filter_status);
        }

        $hrAllEmployeeInfos = $hrAllSearchingEmployeeInfos->where('company_id_fk',$gnrcompanyId)->orderBy('employeeId')->paginate(15);
        // dd($hrAllEmployeeInfos);


            //return view('microfin/employee/employeeList/employeeList',['hrAllEmployeeInfos'=>$hrAllSearchingEmployeeInfos]);

        return view('gnr/employee/employeeList/employeeList', ['route' => $this->route, 'hrAllEmployeeInfos' => $hrAllEmployeeInfos]);

            //return view('microfin/employee/employeeList/employeeList',['hrAllEmployeeInfos'=>$hrAllEmployeeInfos]);

    }

    // public function posAddHrEmployee()
    // {

    //     $userCompanyId = Auth::user()->company_id_fk;
    //     $positions = GnrPosition::where('companyId',  $userCompanyId)->get();
    //     $departments = GnrDepartment::where('companyId', $userCompanyId)->get();
    //     return view('gnr/employee/employeeList/addHrEmployee', [
    //         'route' => $this->route,
    //         'positions' => $positions,
    //         'departments' => $departments,
    //     ]);
    // }


  public function posAddHrEmployee(){
    $userCompanyId = Auth::user()->company_id_fk;
    $positions = GnrPosition::where('companyId',  $userCompanyId)->get();
    $departments = GnrDepartment::where('companyId', $userCompanyId)->get();
    // 1st step
    $customerId = Auth::user()->customer_id;
    $customerUserId = User::where('customer_id', $customerId)->where('user_type', 'master')->value('id');
    $customerPlanId = DB::table('plan_subscriptions')->where('plan_subscriptions.user_id', $customerUserId)->value('plan_id');
    $customerUserLimit = DB::table('plans')->where('id', $customerPlanId)->value(
        'active_users_limit');

    // 2nd step
    //$companyId = DB::table('gnr_company')->where('gnr_company.customer_id', $customerId)->pluck('id')->toArray();
    $userId = DB::table('users')->where('customer_id', $customerId)->count();
    //dd($userId);
    //dd($companyId);
    // dd($customerBranchLimit);

     if ($userId>=$customerUserLimit) {
      return '<script type="text/javascript">alert("User Employee is Limit!");
               window.location.href = "posHrEmployeeList";
             </script>';
         //return back()->with('<script type="text/javascript">alert("hello!");</script>');
      
     }
    
     else{
        return view('gnr/employee/employeeList/addHrEmployee', [
            'route' => $this->route,
            'positions' => $positions,
            'departments' => $departments,
        ]);
    
     }
  }

    public function getBranchInfo(Request $req){
        $data['branches'] = GnrBranch::where('companyId',$req->company_id_fk)->get();
        $data['departments'] = GnrDepartment::where('companyId',$req->company_id_fk)->get();
        //dd($data);
        return response()->json($data);

    }
    // public function getDepartmentInfo(Request $req){
    //     $departments = GnrDepartment::where('companyId',$req->company_id_fk)->get();
    //     dd($req->all());
    //     return response()->json($departments);

    // }
    public function getPositionInfo(Request $req){
        //dd($req->all());
        $positions = GnrPosition::where('dep_id_fk',$req->department_id_fk)->get();
        //dd($positions);
        return response()->json($positions);

    }

    public function addItem(Request $req)
    {
       //dd($req->all());
        $rules = array(
            'emp_id' => 'required',
            'father_name' => 'required',
            'sex' => 'required',
            'date_of_birth' => 'required',
            'present_address' => 'required',
            'permanent_address' => 'required',
            'nid_no' => 'required',
            'mobile_no' => 'required',
            'email' => 'required',
            'company_id_fk' => 'required',
            'position_id_fk' => 'required',
            'employeeName' => 'required',
            'branch_id_fk' => 'required',
            'department_id_fk' => 'required',
            'user_password' => 'required',
            //'c_password' => 'required',
        );

        $attributeNames = array(

            'emp_id' => 'Empolyee id',
            'father_name' => 'Father Name',
            'sex' => 'Gender',
            'date_of_birth' => 'Date of birth ',
            'present_address' => 'Present address',
            'permanent_address' => 'Parmanent address',
            'nid_no' => 'Nid number',
            'mobile_no' => 'Mobile number',
            'email' => 'Email',
            'company_id_fk' => 'company name',
            'position_id_fk' => 'position',
            'user_id' => 'user id',
            'employeeName' => 'employee name',
            'branch_id_fk' => 'branch name',
            'department_id_fk' => 'department name',
            'user_password' => 'user password'
        );


        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails()) {

            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        } else {
            if ($req->file('image')) {
                $file = $req->file('image');
                $filename = str_random(10) . '.' . $file->getClientOriginalExtension();
                $destinationPath = base_path() . '/public/images/employee/';
                $file->move($destinationPath,$filename);
            } 


            $hrGnrEmployeeInfo = new GnrEmployee;
            $hrGnrEmployeeInfo->employeeId = $req->emp_id;
            $hrGnrEmployeeInfo->name = $req->employeeName;
            $hrGnrEmployeeInfo->fatherName = $req->father_name;
            $hrGnrEmployeeInfo->gender = $req->sex;
            $hrGnrEmployeeInfo->dateOfBirth = $req->date_of_birth;
            $hrGnrEmployeeInfo->phone = $req->mobile_no;
            $hrGnrEmployeeInfo->nationalId = $req->nid_no;
            $hrGnrEmployeeInfo->email = $req->email;
            $hrGnrEmployeeInfo->presentAddress = $req->present_address;
            $hrGnrEmployeeInfo->parmanentAddress = $req->permanent_address;
            $hrGnrEmployeeInfo->company_id_fk = $req->company_id_fk;
            $hrGnrEmployeeInfo->branchId = $req->branch_id_fk;
            $hrGnrEmployeeInfo->position_id_fk = $req->position_id_fk;
            $hrGnrEmployeeInfo->department_id_fk = $req->department_id_fk;
            if($req->file('image')){
              $hrGnrEmployeeInfo->image = $filename;
            }
            $hrGnrEmployeeInfo->status = $req->status;
            
            $hrGnrEmployeeInfo->save();
            $user = Auth::user();
            //dd($user);
            $userInfo = new User;
            if($req->user_password == $req->RNPassword){
               $userInfo->password = bcrypt($req->user_password);   
            }
               
            $userInfo->emp_id_fk = $hrGnrEmployeeInfo->id;
            $userInfo->name = $hrGnrEmployeeInfo->name;
            $userInfo->email = $hrGnrEmployeeInfo->email;
            $userInfo->company_id_fk = $hrGnrEmployeeInfo->company_id_fk;
            $userInfo->branchId = $hrGnrEmployeeInfo->branchId;
            $userInfo->customer_id = $user->customer_id;

            $userInfo->save();
            $userInfo->assignRole('customer');
        }

        $logArray = array(
            'moduleId'  => 7,
            'controllerName'  => 'EmployeeController',
            'tableName'  => 'gnr_employee',
            'operation'  => 'insert',
            'primaryIds'  => [DB::table('gnr_employee')->max('id')]
        );
        Service::createLog($logArray);

        return response()->json('success');

    }

        //========START FILTARING FOR ADDRESS====


    public function projectTypeFiltering(Request $req)
    {
        $projectTypeData = DB::table('gnr_project_type')
        ->where('projectId', $req->project_id_fk)->select(DB::raw("CONCAT(projectTypeCode ,' - ', name ) AS name"), 'id')->get();
        $data = array(
            'projectTypeData' => $projectTypeData
        );
        return response()->json($data);
    }


        //END FILTARING FOR ADDRESS==============

    public function employeeEdit($id){
        $employee = GnrEmployee::find($id);
        $userCompanyId = Auth::user()->company_id_fk;
        $positions = GnrPosition::where('companyId',  $userCompanyId)->get();
        $departments = GnrDepartment::where('companyId', $userCompanyId)->get();
        return view('gnr/employee/employeeList/editHrEmployee', [
            'route' => $this->route,
            'positions' => $positions,
            'departments' => $departments,
            'employee' =>$employee
        ]);
    }

    public function employeeGetInfo(Request $req)
    {
        $hrAllEmployeeGetInfos = DB::table('gnr_employee')
                                ->select('gnr_employee.*','users.*')
                                ->join('users', 'users.emp_id_fk', '=', 'gnr_employee.id')
                                ->first();
        //dd($hrAllEmployeeGetInfos);
            //=========START PRESENT ADDRESS GETTING DATA===
        $selectedP = DB::table('gnr_district')->where('divisionId', $hrAllEmployeeGetInfos->pre_div_id)->select('id', 'name')->get();

        // $selectedPreUpzillas = DB::table('gnr_upzilla')->where('divisionId', $hrAllEmployeeGetInfos->pre_div_id)->where('districtId', $hrAllEmployeeGetInfos->pre_dis_id)->select('id', 'name')->get();
        // $selectedPreUnions = DB::table('gnr_union')->where('divisionId', $hrAllEmployeeGetInfos->pre_div_id)->where('districtId', $hrAllEmployeeGetInfos->pre_dis_id)->where('upzillaId', $hrAllEmployeeGetInfos->pre_upa_id)->select('id', 'name')->get();
        //     //=========END PRESENT ADDRESS GETTING DATA=== 

        //     //=========START PARMANENT ADDRESS GETTING DATA=== 
        // $selectedPerDistricts = DB::table('gnr_district')->where('divisionId', $hrAllEmployeeGetInfos->per_div_id)->select('id', 'name')->get();

        // $selectedPerUpzillas = DB::table('gnr_upzilla')->where('divisionId', $hrAllEmployeeGetInfos->per_div_id)->where('districtId', $hrAllEmployeeGetInfos->per_dis_id)->select('id', 'name')->get();
        // $selectedPerUnions = DB::table('gnr_union')->where('divisionId', $hrAllEmployeeGetInfos->per_div_id)->where('districtId', $hrAllEmployeeGetInfos->per_dis_id)->where('upzillaId', $hrAllEmployeeGetInfos->per_upa_id)->select('id', 'name')->get();
        //     //=========END PARMANENT ADDRESS GETTING DATA=== 

        // $selectedPojectTypes = DB::table('gnr_project_type')->where('projectId', $hrAllEmployeeGetInfos->project_id_fk)->select(DB::raw("CONCAT(projectTypeCode ,' - ', name ) AS name"), 'id')->get();


        // $data = array(
        //     'selectedPreDistricts' => $selectedPreDistricts,
        //     'selectedPreUpzillas' => $selectedPreUpzillas,
        //     'selectedPreUnions' => $selectedPreUnions,
        //     'selectedPerDistricts' => $selectedPerDistricts,
        //     'selectedPerUpzillas' => $selectedPerUpzillas,
        //     'selectedPerUnions' => $selectedPerUnions,
        //     'selectedPojectTypes' => $selectedPojectTypes,
        //     'hrAllEmployeeGetInfos' => $hrAllEmployeeGetInfos
        // );
        return response()->json($data);
    }

    public function updateHrEployeeInfo(Request $req)
    {
        //dd($req->all());
         $rules = array(
            'emp_id' => 'required',
            'sex' => 'required',
            'company_id_fk' => 'required',
            'position_id_fk' => 'required',
            'employeeName' => 'required',
            'branch_id_fk' => 'required',
            'department_id_fk' => 'required',
            //'user_password' => 'required',
        );

        $attributeNames = array(

            'emp_id' => 'Empolyee id',
            'sex' => 'Gender',
            'company_id_fk' => 'company name',
            'position_id_fk' => 'position',
            'user_id' => 'user id',
            'employeeName' => 'employee name',
            'branch_id_fk' => 'branch name',
            'department_id_fk' => 'department name',
           // 'user_password' => 'user password'
        );


        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails()) {

            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        } else {
            if ($req->file('image')) {
              $file = $req->file('image');
              $filename = str_random(10) . '.' . $file->getClientOriginalExtension();
              $destinationPath = base_path() . '/public/images/employee/';
              $file->move($destinationPath,$filename);
            } 
            $hrGnrEmployeeInfo = GnrEmployee::find($req->id);
            $hrGnrEmployeeInfo->employeeId = $req->emp_id;
            $hrGnrEmployeeInfo->name = $req->employeeName;
            $hrGnrEmployeeInfo->fatherName = $req->father_name;
            $hrGnrEmployeeInfo->gender = $req->sex;
            $hrGnrEmployeeInfo->dateOfBirth = $req->date_of_birth;
            $hrGnrEmployeeInfo->phone = $req->mobile_no;
            $hrGnrEmployeeInfo->nationalId = $req->nid_no;
            $hrGnrEmployeeInfo->email = $req->email;
            $hrGnrEmployeeInfo->presentAddress = $req->present_address;
            $hrGnrEmployeeInfo->parmanentAddress = $req->permanent_address;
            $hrGnrEmployeeInfo->company_id_fk = $req->company_id_fk;
            $hrGnrEmployeeInfo->branchId = $req->branch_id_fk;
            $hrGnrEmployeeInfo->position_id_fk = $req->position_id_fk;
            $hrGnrEmployeeInfo->department_id_fk = $req->department_id_fk;
            $hrGnrEmployeeInfo->status = $req->status;
            if($req->file('image')){
              $hrGnrEmployeeInfo->image = $filename;
            }
            $hrGnrEmployeeInfo->save();
          
            // $userInfo = User::where('emp_id_fk', $req->id)->first();
            // $userInfo->emp_id_fk = $hrGnrEmployeeInfo->id;
            // $userInfo->name = $hrGnrEmployeeInfo->name;
            // $userInfo->email = $hrGnrEmployeeInfo->email;
            // $userInfo->save();
        
    }
    
    return response()->json('success');
    }

    public function hrDeleteEmployee(Request $req)
    {
    
    GnrEmployee::find($req->id)->delete();
    User::where('emp_id_fk', $req->id)->delete();
   
    return response()->json('success');

    }


    public function hrDetailsEmployee($employeeId)
    {

        $hrAllEmployeeDetails = GnrEmployee::find($employeeId);

        return view('gnr/employee/employeeList/employeeDetails', ['route' => $this->route, 'hrAllEmployeeDetails' => $hrAllEmployeeDetails]);

    }

    public function branchFilteringByProject(Request $req)
    {
        $branchData = DB::table('gnr_branch')
        ->where('projectId', $req->search_project_id_fk)->select('branchCode', 'name', 'id')->where('id', '!=', 1)->get();
        $data = array(
            'branchData' => $branchData
        );
        return response()->json($data);
    }

}