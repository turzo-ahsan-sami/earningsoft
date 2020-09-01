<?php

namespace App\Http\Controllers\hr;

use App\ConstValue;
use Auth;
use App\User;
use Response;
use App\Http\Controllers\gnr\Service;
use DB;

use Validator;
use App\hr\ReasonList;
use App\Http\Requests;
use App\gnr\GnrCompany;

use App\hr\TerminateInfo;
use App\Service\EasyCode;
use Illuminate\Http\Request;
use App\hr\EmployeeOrganizationInfo;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;


class TerminateInfoController extends Controller
{
    protected $easycode;

    public function __construct()
    {
        $this->easycode = new EasyCode();
    }

    public function index()
    {
        $data['pageTitle'] = 'Terminate List';
        $data['addBtnLabel'] = 'New Terminate';
        $data['addBtnUrl'] = url('hr/terminateInfo/create');
        $data['utype'] = $this->easycode->getUserRole();

        $authUser = Auth::user();
        $model = new TerminateInfo;
        $data['model'] = $model->orderby('hr_terminate_info.status', 'desc');

        if ($authUser->getRoleId() != ConstValue::ROLE_ID_EMPLOYEE) {
            if ($authUser->branchId != ConstValue::BRANCH_ID_HEAD_OFFICE){
                $data['model'] = $data['model']->leftJoin('users', 'hr_terminate_info.users_id_fk', '=', 'users.id')->where('users.branchId', $data['utype']['branchid']);
            }
        } else {
            $data['model'] = $data['model']->where('users_id_fk', $authUser->id);
        }
        $data['model'] = $data['model']->paginate(999999999);

        $data['attributes'] = $model->attributes();
        $data['easycode'] = $this->easycode;

        return view('hr.terminateInfo.index', ['model' => $model, 'data' => $data]);
    }

    public function create(Request $request)
    {
        $data['pageTitle'] = 'New Terminate';

        $data['companyData'] = GnrCompany::options('Select any');
        $data['reasonData'] = ReasonList::options('Select any', 'Terminate');

        $data['model'] = new TerminateInfo;
        $data['attributes'] = $data['model']::attributes();
        $data['placeholder'] = $data['model']::placeholder();
        $data['easycode'] = $this->easycode;
        $data['allRecruitmentTypeLabel'] = 'All List';
        $data['allRecruitmentTypeUrl'] = url('hr/terminateInfo');
        $data['closeBtnUrl'] = url('hr/terminateInfo');
        if (isset($_POST['_token'])) {
            $req = $request->all();

            if ($req['terminate_date'] != '') {
                $req['terminate_date'] = date("Y-m-d", strtotime($req['terminate_date']));
            }

            $req['status'] = 'Pending';

            $req['created_by'] = Auth::user()->id;
            $req['updated_by'] = Auth::user()->id;
            $req['created_at'] = date("Y-m-d H:i:s");
            $req['updated_at'] = date("Y-m-d H:i:s");

            $validator = Validator::make($req, TerminateInfo::createRules());
            $validator->setAttributeNames(TerminateInfo::attributes());

            //$errors = $validator->messages()->merge($uservalidator->messages());
            if ($validator->fails()) {
                return redirect('/hr/terminateInfo/create')->withErrors($validator, 'error')->withInput();
            } else {
                $data['model']->fill($req);
                if ($data['model']->save()) {
                   $logArray = array(
                    'moduleId'  => 5,
                    'controllerName'  => 'TerminateInfoController',
                    'tableName'  => 'hr_terminate_info',
                    'operation'  => 'insert',
                    'primaryIds'  => [DB::table('hr_terminate_info')->max('id')]
                );
                   Service::createLog($logArray);
                   return redirect('/hr/terminateInfo')->with('success', 'Terminate created successfully.');
               } else {
                return redirect('/hr/terminateInfo/create')->with('error', 'Failed to create.')->withInput();
            }
        }
    }
    return view('hr.terminateInfo.create', ['data' => $data]);
}

public function update($id, Request $request)
{
    $data['pageTitle'] = 'Update Terminate Information';

    $data['companyData'] = GnrCompany::options('Select any');
    $data['reasonData'] = ReasonList::options('Select any', 'Terminate');

    $data['model'] = TerminateInfo::where('id', $id)->where('status', 'Pending')->first();
    if (count($data['model']) == 0) {
        return redirect('/hr/terminateInfo/')->with('error', 'Terminate information is not in update stage.');
    }
    $data['model']->company_id_fk = $data['model']->user->company_id_fk;

    if ($data['model']->terminate_date != Null) {
        $data['model']->terminate_date = date("d-m-Y", strtotime($data['model']->terminate_date));
    }

    $data['attributes'] = $data['model']::attributes();
    $data['placeholder'] = $data['model']::placeholder();
    $data['easycode'] = $this->easycode;
    $data['allRecruitmentTypeLabel'] = 'All List';
    $data['allRecruitmentTypeUrl'] = url('hr/terminateInfo');
    $data['closeBtnUrl'] = url('hr/terminateInfo');
    if (isset($_POST['_token'])) {
    $req = $request->all();

    if ($req['terminate_date'] != '') {
    $req['terminate_date'] = date("Y-m-d", strtotime($req['terminate_date']));
}

$req['status'] = 'Pending';

$req['updated_by'] = Auth::user()->id;
$req['updated_at'] = date("Y-m-d H:i:s");

$validator = Validator::make($req, TerminateInfo::createRules());
$validator->setAttributeNames(TerminateInfo::attributes());

if ($validator->fails()) {
return redirect('/hr/terminateInfo/update/' . $id)->withErrors($validator, 'error');
} else {
$previousdata = TerminateInfo::find($id);
$data['model']->fill($req);
unset($data['model']->company_id_fk);
if ($data['model']->save()) {
$logArray = array(
'moduleId'  => 5,
'controllerName'  => 'TerminateInfoController',
'tableName'  => 'hr_terminate_info',
'operation'  => 'update',
'previousData'  => $previousdata,
'primaryIds'  => [$previousdata->id]
);
Service::createLog($logArray);
return redirect('/hr/terminateInfo')->with('success', 'Terminate information updated successfully.');
} else {
return redirect('/hr/terminateInfo/update/' . $id)->with('error', 'Failed to updated resign information settings.')->withInput();
}
}
}
return view('hr.terminateInfo.create', ['data' => $data]);
}

public function cancel($id, Request $request)
{
    $data['pageTitle'] = 'Terminate Cancel';

    $data['model'] = TerminateInfo::find($id);
    $data['attributes'] = $data['model']::attributes();
    $data['allRecruitmentTypeLabel'] = 'All Terminate List';
    $data['allRecruitmentTypeUrl'] = url('hr/terminateInfo');
    $data['easycode'] = $this->easycode;

    if (isset($_POST['_token'])) {
    $req = $request->all();

    if ($req['cancel_date'] != '') {
    $req['cancel_date'] = date("Y-m-d", strtotime($req['cancel_date']));
}

$req['status'] = 'Canceled';

$req['updated_by'] = Auth::user()->id;
$req['updated_at'] = date("Y-m-d H:i:s");


$validator = Validator::make($req, TerminateInfo::cancelRules());
$validator->setAttributeNames(TerminateInfo::attributes());

if ($validator->fails()) {
return redirect('/hr/terminateInfo/cancel/' . $id)->withErrors($validator, 'error');
} else {
$previousdata = TerminateInfo::find($id);
$data['model']->fill($req);
unset($data['model']->company_id_fk);
if ($data['model']->save()) {
$logArray = array(
'moduleId'  => 5,
'controllerName'  => 'TerminateInfoController',
'tableName'  => 'hr_terminate_info',
'operation'  => 'update',
'previousData'  => $previousdata,
'primaryIds'  => [$previousdata->id]
);
Service::createLog($logArray);
$this->cancelOganizationEffect($data['model']);
return redirect('/hr/terminateInfo')->with('success', 'Terminate information canceled successfully.');
} else {
return redirect('/hr/terminateInfo/cancel/' . $id)->with('error', 'Failed to canceled terminate information.')->withInput();
}
}
}

return view('hr.terminateInfo.cancel', ['data' => $data]);
}

public function cancelOganizationEffect($data)
{
    $model = EmployeeOrganizationInfo::where('emp_id_fk', @$data->user->employee->id)->first();
    $model->terminate_resignation_date = '000-00-00';
    $model->job_status = 'Present';
    $model->save();
}

public function approved($id, Request $request)
{
    $data['pageTitle'] = 'Approved Terminate Information';

    $data['companyData'] = GnrCompany::options('Select any');
    $data['reasonData'] = ReasonList::options('Select any', 'Terminate');

    $data['model'] = TerminateInfo::where('id', $id)->where('status', 'Pending')->first();
    if (!$data['model']) {
    return redirect('/hr/terminateInfo/')->with('error', 'Terminate information is not in approve stage.');
}

$data['model']->company_id_fk = $data['model']->user->company_id_fk;
if ($data['model']->terminate_date != Null) {
$data['model']->terminate_date = date("d-m-Y", strtotime($data['model']->terminate_date));
}

$data['action'] = 'Approve';

$data['attributes'] = $data['model']::attributes();
$data['placeholder'] = $data['model']::placeholder();
$data['easycode'] = $this->easycode;
$data['allRecruitmentTypeLabel'] = 'All List';
$data['allRecruitmentTypeUrl'] = url('hr/terminateInfo');
$data['closeBtnUrl'] = url('hr/terminateInfo');
if (isset($_POST['_token'])) {
$req = $request->all();

if ($req['terminate_date'] != '') {
$req['terminate_date'] = date("Y-m-d", strtotime($req['terminate_date']));
}

if ($req['effect_date'] != '') {
$req['effect_date'] = date("Y-m-d", strtotime($req['effect_date']));
}

$req['status'] = 'Approved';
$req['approved_id_fk'] = Auth::user()->id;

$req['updated_by'] = Auth::user()->id;
$req['updated_at'] = date("Y-m-d H:i:s");


$validator = Validator::make($req, TerminateInfo::createRules());
$validator->setAttributeNames(TerminateInfo::attributes());

if ($validator->fails()) {
return redirect('/hr/terminateInfo/approved/' . $id)->withErrors($validator, 'error');
} else {
$previousdata = TerminateInfo::find($id);
$data['model']->fill($req);
unset($data['model']->company_id_fk);
if ($data['model']->save()) {
$logArray = array(
'moduleId'  => 5,
'controllerName'  => 'TerminateInfoController',
'tableName'  => 'hr_terminate_info',
'operation'  => 'update',
'previousData'  => $previousdata,
'primaryIds'  => [$previousdata->id]
);
Service::createLog($logArray);
$this->organizationEffect($data['model']);
return redirect('/hr/terminateInfo')->with('success', 'Terminate information approved successfully.');
} else {
return redirect('/hr/terminateInfo/approved/' . $id)->with('error', 'Failed to approved terminate information.')->withInput();
}
}
}
return view('hr.terminateInfo.create', ['data' => $data]);
}

public function organizationEffect($data)
{
    $model = EmployeeOrganizationInfo::where('emp_id_fk', @$data->user->employee->id)->first();
    $model->terminate_resignation_date = $data->effect_date;
    $model->job_status = 'Terminate';
    $model->save();
}

public function view($id, Request $request)
{
    $data['pageTitle'] = 'Terminate Details';

    $data['model'] = TerminateInfo::find($id);
    $data['attributes'] = $data['model']::attributes();
    $data['allRecruitmentTypeLabel'] = 'All Terminate List';
    $data['allRecruitmentTypeUrl'] = url('hr/terminateInfo');
    $data['easycode'] = $this->easycode;

    return view('hr.terminateInfo.view', ['data' => $data]);
}

public function delete(Request $request)
{
    $item = TerminateInfo::find($request['id']);
    $previousdata = TerminateInfo::find($request['id']);
    if (isset($_POST['_token'])) {
    if ($item && $item->delete()) {
    $logArray = array(
    'moduleId'  => 5,
    'controllerName'  => 'TerminateInfoController',
    'tableName'  => 'hr_terminate_info',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
    );
    Service::createLog($logArray);
    if ($request->ajax())
    return response::json(['res' => 1, 'msg' => 'Terminate information deleted successfully.']);
    else
    return redirect('/hr/terminateInfo')->with('success', 'Terminate information deleted successfully.');
} else {
if ($request->ajax())
return response::json(['res' => 0, 'msg' => 'Terminate information deleted successfully.']);
else
return redirect('/hr/terminateInfo')->with('error', 'Terminate information deleted successfully.');
}
} else {
if ($request->ajax())
return response::json(['res' => 0, 'msg' => 'Invalid request.']);
else
return redirect('/hr/terminateInfo')->with('error', 'Invalid request.');
}
}

public function getEmployeeCurrentData(Request $request)
{
    $user = User::find(intval($request->id));
    if (isset($_POST['_token'])) {
    if (count($user) > 0) {

    $data = [];
    $data['fiscal_year'] = ['id' => $user->employee->organization->fiscal_year_fk, 'name' => $user->employee->organization->fiscalYear->name];
    $data['grade'] = ['id' => $user->employee->organization->grade, 'name' => $user->employee->organization->gradeInfo->name];
    $data['level'] = ['id' => $user->employee->organization->level_id_fk, 'name' => $user->employee->organization->level->name];
    $data['position'] = ['id' => $user->employee->organization->position_id_fk, 'name' => $user->employee->organization->position->name];
    $data['recruitment_type'] = ['id' => $user->employee->organization->recruitment_type, 'name' => $user->employee->organization->recruitmentType->name];
    $data['salary_increment_year'] = ['id' => $user->employee->organization->salary_increment_year, 'name' => $user->employee->organization->salary_increment_year];

    if ($request->ajax())
    return response::json(['res' => 1, 'value' => $data]);
    else
    return redirect('/hr');
} else {
if ($request->ajax())
return response::json(['res' => 0]);
else
return redirect('/hr');
}
} else {
if ($request->ajax())
return response::json(['res' => 0, 'msg' => 'Invalid request.']);
else
return redirect('/hr');
}
}

}
