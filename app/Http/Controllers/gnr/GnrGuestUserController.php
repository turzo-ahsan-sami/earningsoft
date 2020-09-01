<?php

namespace App\Http\Controllers\gnr;

use App\ConstValue;
use App\gnr\GnrBranch;
use App\gnr\GnrCompany;
use App\gnr\GnrProject;
use App\gnr\GnrRole;
use App\hr\Constants;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\GnrUserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use Carbon\Carbon;
use DB;
use App\Http\Controllers\Controller;
use Exception;

class GnrGuestUserController extends Controller
{

    public function index(Request $request)
    {
        $branchSelected = null;
        $roleSelected = null;
        $empIdSelected = null;

        $userQuery = User::query();
        $userQuery->where('roleId', ConstValue::ROLE_ID_GUEST)->orderBy('id', 'DESC');

        $branchList = GnrBranch::all()->pluck('name','id')->toArray();
        $roleList = GnrRole::where('id', ConstValue::ROLE_ID_GUEST)->pluck('name', 'id')->toArray();

        if (!empty($request->filBranch)) {
            $branchSelected = $request->filBranch;
            $userQuery->where('branchId', $request->filBranch);
        }

        if (!empty($request->filRole)) {
            $roleSelected = $request->filRole;
            if(Constants::ROLE_ID_SUPER_ADMIN == $request->filRole || Constants::ROLE_ID_GUEST == $request->filRole) {
                $userQuery->where('roleId', $request->filRole);
            } else {
                $userQuery->whereHas('role', function($q) use($request){
                    return $q->where('roleId', $request->filRole);
                });
            }
        }

        if (!empty($request->filEmpId)) {
            $empIdSelected = $request->filEmpId;
            $userQuery->where('username', $request->filEmpId);
        }

        $users = $userQuery->paginate(15);

        return view('gnr.guestUser.index', [
            'users' => $users,
            'branchList' => $branchList,
            'roleList' => $roleList,
            'branchSelected' => $branchSelected,
            'roleSelected' => $roleSelected,
            'empIdSelected' => $empIdSelected,
            'gnrRole'=> new GnrRole()

        ]);
    }

    public function create(Request $request)
    {
        $companies = GnrCompany::all()->pluck('name', 'id')->toArray();
        $projects = GnrProject::all()->pluck('name', 'id')->toArray();
        $branches = GnrBranch::all()->pluck('name', 'id')->toArray();
        $roles = GnrRole::whereIn('id',[Constants::ROLE_ID_GUEST])->pluck('name', 'id')->toArray();
        return view('gnr.guestUser.create', [
            'companies' => $companies,
            'projects' => $projects,
            'branches' => $branches,
            'roles' => $roles
        ]);

    }

    public function edit($id, Request $request)
    {
        $user = User::findOrFail($id);
        $companies = GnrCompany::all()->pluck('name', 'id')->toArray();
        $projects = GnrProject::all()->pluck('name', 'id')->toArray();
        $branches = GnrBranch::all()->pluck('name', 'id')->toArray();
        $roles = GnrRole::whereIn('id',[Constants::ROLE_ID_SUPER_ADMIN, Constants::ROLE_ID_GUEST])->pluck('name', 'id')->toArray();
        return view('gnr.guestUser.edit', [
            'companies' => $companies,
            'projects' => $projects,
            'branches' => $branches,
            'roles' => $roles,
            'user'=>$user
        ]);

    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'user_name' => 'required|max:255',
            'email' => 'required|email|unique:users'. ',id,' . $id,
            'role' => 'required',
            'project' => 'required',
            'branch' => 'required',
            'company' => 'required',
            'status' => 'required',
            'password' => 'required|confirmed|min:5',
        ]);
        $previousdata = User::find ($id);
        $user = User::find($id);
        $user->name = $request->name;
        $user->username = $request->user_name;
        $user->email = $request->email;
        $user->roleId = $request->role;
        $user->company_id_fk = $request->company;
        $user->project_id_fk = $request->project;
        $user->password = Hash::make($request->password);
        $user->branchId = $request->branch;
        $user->status = $request->status;

        if($user->save()){
           $logArray = array(
            'moduleId'  => 7,
            'controllerName'  => 'GnrGuestUserController',
            'tableName'  => 'users',
            'operation'  => 'update',
            'previousData'  => $previousdata,
            'primaryIds'  => [$previousdata->id]
        );
           Service::createLog($logArray);
           return redirect()->route('guestUser.index')->with('success', 'Successfully updated a user');
       } else {
        return redirect()->route('guestUser.index')->with('error', 'Something went wrong');
    }
}

public function store(Request $request)
{
    $this->validate($request, [
        'name' => 'required|max:255',
        'user_name' => 'required|max:255',
        'email' => 'required|email|unique:users',
        'role' => 'required',
        'project' => 'required',
        'branch' => 'required',
        'company' => 'required',
        'status' => 'required',
        'password' => 'required|confirmed|min:5',
    ]);

    $user = new User;
    $user->name = $request->name;
    $user->username = $request->user_name;
    $user->email = $request->email;
    $user->password = Hash::make($request->password);
    $user->roleId = $request->role;
    $user->company_id_fk = $request->company;
    $user->project_id_fk = $request->project;
    $user->branchId = $request->branch;
    $user->status = $request->status;

    if($user->save()){
       $logArray = array(
          'moduleId'  => 7,
          'controllerName'  => 'GnrGuestUserController',
          'tableName'  => 'users',
          'operation'  => 'insert',
          'primaryIds'  => [DB::table('users')->max('id')]
      );
       Service::createLog($logArray); 
       return redirect()->route('guestUser.index')->with('success', 'Successfully create a user');
   } else {
    return redirect()->route('guestUser.index')->with('error', 'Something went wrong');
}
}

public function isDestroyAble()
{
    if(Auth::user()->id == ConstValue::USER_ID_SUPER_ADMIN){
        return true;
    } else {
        throw new Exception("You have not delete permission");
    }
}

public function delete(Request $request, $id)
{
    $data = [];
    $code = ConstValue::STATUS_CODE_SUCCESS;
    $previousdata=User::findOrFail($id);
    $user = User::findOrFail($id);
    $logArray = array(
      'moduleId'  => 7,
      'controllerName'  => 'GnrGuestUserController',
      'tableName'  => 'users',
      'operation'  => 'delete',
      'previousData'  => $previousdata,
      'primaryIds'  => [$previousdata->id]
  );
    Service::createLog($logArray);
    try {
        if($this->isDestroyAble()) {
            $data['success'] = true;
            $data['message'] = 'Successfully Deleted';
            $user->delete();
            return Response::json($data, $code);
        }
    } catch (Exception $e){
        $code = ConstValue::STATUS_CODE_SERVER_ERROR;
        $data['success'] = false;
        $data['message'] = $e->getMessage();

        return Response::json($data, $code);
    }
}

public function getProjectsByCompanyId($id, Request $request)
{
    $list = [];
    $projects = GnrProject::where('companyId', $id)->get();

    foreach ($projects as $project) {
        $list[] = [
            'id' => $project->id,
            'name' => $project->name
        ];
    }

    return $list;
}
}
