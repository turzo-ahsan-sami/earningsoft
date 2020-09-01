<?php

namespace App\Http\Controllers\hr;

use App\ConstValue;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;


use Auth;
use Validator;
use Illuminate\Support\Facades\Input;
use Response;
use DB;
use App\Http\Controllers\gnr\Service;

use App\gnr\GnrCompany;
use App\hr\ResignInfo;
use App\hr\ReasonList;
use App\hr\EmployeeOrganizationInfo;

use App\Service\EasyCode;
use App\Service\Helper;
use App\User;


class ResignInfoController extends Controller
{
    protected $easycode;

    public function __construct()
    {
        $this->easycode = new EasyCode();
    }

    public function index()
    {
        $data['pageTitle'] = 'Resign List';
        $data['addBtnLabel'] = 'New Resign';
        $data['addBtnUrl'] = url('hr/resignInfo/create');
        $data['utype'] = EasyCode::getUserRole();

        $authUser = Auth::user();
        $model = new ResignInfo;
        $data['model'] = $model->orderby('hr_resign_info.status', 'desc');

        if ($authUser->getRoleId() != ConstValue::ROLE_ID_EMPLOYEE) {
            if ($authUser->branchId != 1) {
                $data['model'] = $data['model']->leftJoin('users', 'hr_resign_info.users_id_fk', '=', 'users.id')->where('users.branchId', $data['utype']['branchid']);
            }
        } else {
            $data['model'] = $data['model']->where('users_id_fk', $authUser->id);
        }

        $data['model'] = $data['model']->paginate(999999999);
        $data['attributes'] = $model->attributes();
        $data['easycode'] = $this->easycode;

        return view('hr.resignInfo.index', ['model' => $model, 'data' => $data]);
    }

    public function create(Request $request)
    {
        $data['pageTitle'] = 'New Resign';
        $data['allRecruitmentTypeLabel'] = 'All List';
        $data['allRecruitmentTypeUrl'] = url('hr/resignInfo');
        $data['closeBtnUrl'] = url('hr/resignInfo');

        $data['utype'] = EasyCode::getUserRole();
        $data['action'] = 'insert';

        $data['companyData'] = GnrCompany::options('Select any');
        $data['reasonData'] = ReasonList::options('Select any', 'Resign');

        $data['model'] = new ResignInfo;
        $data['attributes'] = $data['model']::attributes();
        $data['placeholder'] = $data['model']::placeholder();
        $data['easycode'] = $this->easycode;
        if (isset($_POST['_token'])) {

            $req = $request->all();

            if ($data['utype']['roleid'] == 2) {
                $req['users_id_fk'] = Auth::user()->id;
                $uinfo = User::find(Auth::user()->id);
                $req['position'] = @$uinfo->employee->organization->position->name;
                $req['recruitment_type'] = @$uinfo->employee->organization->recruitmentType->name;
            }

            if ($req['resign_date'] != '') {
                $req['resign_date'] = date("Y-m-d", strtotime($req['resign_date']));
            }

            if ($req['expected_effect_date'] != '') {
                $req['expected_effect_date'] = date("Y-m-d", strtotime($req['expected_effect_date']));
            }

            $req['status'] = 'Pending';

            $req['created_by'] = Auth::user()->id;
            $req['updated_by'] = Auth::user()->id;
            $req['created_at'] = date("Y-m-d H:i:s");
            $req['updated_at'] = date("Y-m-d H:i:s");

            $validator = Validator::make($req, ResignInfo::createRules());
            $validator->setAttributeNames(ResignInfo::attributes());
            $validator->after(function ($validator) use ($data, $req) {
                $chkPrePending = $data['model']->where('users_id_fk', Auth::user()->id)->where('status', 'Pending')->exists();
                if ($chkPrePending) {
                    $validator->errors()->add('note', 'Your previous application is not approved yet');
                }
            });

            if ($validator->fails()) {
                return redirect('/hr/resignInfo/create')->withErrors($validator, 'error')->withInput();
            } else {
                $data['model']->fill($req);

                // Check employee is assigned with samity or not
                $employee = User::find($data['model']->users_id_fk)->employee;
                if(Helper::isAssignedInSamityOfTheEmployeeInTargetDate($employee, $data['model']->expected_effect_date)){
                    return redirect('hr/resignInfo')->with('success', 'This employee is engaged with samity.');
                }

                if ($data['model']->save()) {
                    $logArray = array(
                        'moduleId'  => 5,
                        'controllerName'  => 'ResignInfoController',
                        'tableName'  => 'hr_resign_info',
                        'operation'  => 'insert',
                        'primaryIds'  => [DB::table('hr_resign_info')->max('id')]
                    );
                    Service::createLog($logArray);
                    return redirect('/hr/resignInfo')->with('success', 'Resign created successfully.');
                } else {
                    return redirect('/hr/resignInfo/create')->with('error', 'Failed to create.')->withInput();
                }
            }
        }

        return view('hr.resignInfo.create', ['data' => $data]);
    }

    public function update($id, Request $request)
    {
        $data['utype'] = $this->easycode->getUserRole();

        $data['pageTitle'] = 'Update Resign Information';

        $data['companyData'] = GnrCompany::options('Select any');
        $data['reasonData'] = ReasonList::options('Select any', 'Resign');

        $data['model'] = ResignInfo::where('id', $id)->where('status', 'Pending')->first();
        if (!$data['model']) {
            return redirect('/hr/resignInfo/')->with('error', 'Resign information is not in update stage.');
        }
        $data['model']->company_id_fk = $data['model']->user->company_id_fk;

        if ($data['model']->resign_date != Null) {
            $data['model']->resign_date = date("d-m-Y", strtotime($data['model']->resign_date));
        }

        if ($data['model']->expected_effect_date != Null) {
            $data['model']->expected_effect_date = date("d-m-Y", strtotime($data['model']->expected_effect_date));
        }

        $data['attributes'] = $data['model']::attributes();
        $data['placeholder'] = $data['model']::placeholder();
        $data['easycode'] = $this->easycode;
        $data['allRecruitmentTypeLabel'] = 'All List';
        $data['allRecruitmentTypeUrl'] = url('hr/resignInfo');
        $data['closeBtnUrl'] = url('hr/resignInfo');
        if (isset($_POST['_token'])) {
            $req = $request->all();

            if ($data['utype']['roleid'] == 2) {
                $req['users_id_fk'] = Auth::user()->id;
                $uinfo = User::find(Auth::user()->id);
                $req['position'] = @$uinfo->employee->organization->position->name;
                $req['recruitment_type'] = @$uinfo->employee->organization->recruitmentType->name;
            }

            if ($req['resign_date'] != '') {
                $req['resign_date'] = date("Y-m-d", strtotime($req['resign_date']));
            }

            if ($req['expected_effect_date'] != '') {
                $req['expected_effect_date'] = date("Y-m-d", strtotime($req['expected_effect_date']));
            }

            $req['status'] = 'Pending';

            $req['updated_by'] = Auth::user()->id;
            $req['updated_at'] = date("Y-m-d H:i:s");


            $validator = Validator::make($req, ResignInfo::createRules());
            $validator->setAttributeNames(ResignInfo::attributes());

            if ($validator->fails()) {
                return redirect('/hr/resignInfo/update/' . $id)->withErrors($validator, 'error');
            } else {
                $previousdata = ResignInfo::find($id);
                $data['model'] = ResignInfo::where('id', $id)->where('status', 'Pending')->first();
                $data['model']->fill($req);
                //unset($data['model']->company_id_fk);
                if ($data['model']->save()) {
                    $logArray = array(
                        'moduleId'  => 5,
                        'controllerName'  => 'ResignInfoController',
                        'tableName'  => 'hr_resign_info',
                        'operation'  => 'update',
                        'previousData'  => $previousdata,
                        'primaryIds'  => [$previousdata->id]
                    );
                    Service::createLog($logArray);
                    return redirect('/hr/resignInfo')->with('success', 'Resign information updated successfully.');
                } else {
                    return redirect('/hr/resignInfo/update/' . $id)->with('error', 'Failed to updated resign information settings.')->withInput();
                }
            }
        }
        return view('hr.resignInfo.create', ['data' => $data]);
    }

    public function cancel($id, Request $request)
    {
        $data['pageTitle'] = 'Resign Cancel';

        $data['model'] = ResignInfo::find($id);
        $data['attributes'] = $data['model']::attributes();
        $data['allRecruitmentTypeLabel'] = 'All Resign List';
        $data['allRecruitmentTypeUrl'] = url('hr/resignInfo');
        $data['easycode'] = $this->easycode;

        if (isset($_POST['_token'])) {
            $req = $request->all();

            if ($req['cancel_date'] != '') {
                $req['cancel_date'] = date("Y-m-d", strtotime($req['cancel_date']));
            }

            $req['status'] = 'Canceled';

            $req['updated_by'] = Auth::user()->id;
            $req['updated_at'] = date("Y-m-d H:i:s");


            $validator = Validator::make($req, ResignInfo::cancelRules());
            $validator->setAttributeNames(ResignInfo::attributes());

            if ($validator->fails()) {
                return redirect('/hr/resignInfo/cancel/' . $id)->withErrors($validator, 'error');
            } else {
                $previousdata = ResignInfo::find($id);
                $data['model']->fill($req);
                unset($data['model']->company_id_fk);
                if ($data['model']->save()) {
                    $logArray = array(
                        'moduleId'  => 5,
                        'controllerName'  => 'ResignInfoController',
                        'tableName'  => 'hr_resign_info',
                        'operation'  => 'update',
                        'previousData'  => $previousdata,
                        'primaryIds'  => [$previousdata->id]
                    );
                    Service::createLog($logArray);
                    $this->cancelOganizationEffect($data['model']);
                    return redirect('/hr/resignInfo')->with('success', 'Resign information canceled successfully.');
                } else {
                    return redirect('/hr/resignInfo/cancel/' . $id)->with('error', 'Failed to canceled resign information.')->withInput();
                }
            }
        }

        return view('hr.resignInfo.cancel', ['data' => $data]);
    }

    public function approved($id, Request $request)
    {
        $data['pageTitle'] = 'Approved Resign Information';

        $data['utype'] = EasyCode::getUserRole();

        $data['companyData'] = GnrCompany::options('Select any');
        $data['reasonData'] = ReasonList::options('Select any', 'Resign');

        $data['model'] = ResignInfo::where('id', $id)->where('status', 'Pending')->first();
        if (!$data['model']) {
            return redirect('/hr/resignInfo/')->with('error', 'Resign information is not in approve stage.');
        }

        $data['model']->company_id_fk = $data['model']->user->company_id_fk;
        if ($data['model']->resign_date != Null) {
            $data['model']->resign_date = date("d-m-Y", strtotime($data['model']->resign_date));
        }

        if ($data['model']->expected_effect_date != Null) {
            $data['model']->expected_effect_date = date("d-m-Y", strtotime($data['model']->expected_effect_date));
        }

        $data['action'] = 'Approve';

        $data['attributes'] = $data['model']::attributes();
        $data['placeholder'] = $data['model']::placeholder();
        $data['easycode'] = $this->easycode;
        $data['allRecruitmentTypeLabel'] = 'All List';
        $data['allRecruitmentTypeUrl'] = url('hr/resignInfo');
        $data['closeBtnUrl'] = url('hr/resignInfo');
        if (isset($_POST['_token'])) {
            $req = $request->all();

            if ($req['resign_date'] != '') {
                $req['resign_date'] = date("Y-m-d", strtotime($req['resign_date']));
            }

            if ($req['expected_effect_date'] != '') {
                $req['expected_effect_date'] = date("Y-m-d", strtotime($req['expected_effect_date']));
            }

            if ($req['effect_date'] != '') {
                $req['effect_date'] = date("Y-m-d", strtotime($req['effect_date']));
            }
            $req['status'] = 'Approved';
            $req['approved_id_fk'] = Auth::user()->id;

            $req['updated_by'] = Auth::user()->id;
            $req['updated_at'] = date("Y-m-d H:i:s");


            $validator = Validator::make($req, ResignInfo::createRules());
            $validator->setAttributeNames(ResignInfo::attributes());

            if ($validator->fails()) {
                return redirect('/hr/resignInfo/approved/' . $id)->withErrors($validator, 'error');
            } else {
                $previousdata = ResignInfo::find($id);
                $data['model']->fill($req);
                unset($data['model']->company_id_fk);
                if ($data['model']->save()) {
                    $logArray = array(
                        'moduleId'  => 5,
                        'controllerName'  => 'ResignInfoController',
                        'tableName'  => 'hr_resign_info',
                        'operation'  => 'update',
                        'previousData'  => $previousdata,
                        'primaryIds'  => [$previousdata->id]
                    );
                    Service::createLog($logArray);
                    $this->organizationEffect($data['model']);
                    return redirect('/hr/resignInfo')->with('success', 'Resign information approved successfully.');
                } else {
                    return redirect('/hr/resignInfo/approved/' . $id)->with('error', 'Failed to approved resign information settings.')->withInput();
                }
            }
        }
        return view('hr.resignInfo.create', ['data' => $data]);
    }

    public function organizationEffect($data)
    {
        $model = EmployeeOrganizationInfo::where('emp_id_fk', @$data->user->employee->id)->first();
        $model->terminate_resignation_date = $data->effect_date;
        $model->job_status = 'Resign';
        $model->save();
    }

    public function cancelOganizationEffect($data)
    {
        $model = EmployeeOrganizationInfo::where('emp_id_fk', @$data->user->employee->id)->first();
        $model->terminate_resignation_date = '000-00-00';
        $model->job_status = 'Present';
        $model->save();
    }

    public function view($id, Request $request)
    {
        $data['pageTitle'] = 'Resign Details';

        $data['model'] = ResignInfo::find($id);
        $data['attributes'] = $data['model']::attributes();
        $data['allRecruitmentTypeLabel'] = 'All Resign List';
        $data['allRecruitmentTypeUrl'] = url('hr/resignInfo');
        $data['easycode'] = $this->easycode;
        $data['utype'] = EasyCode::getUserRole();

        return view('hr.resignInfo.view', ['data' => $data]);
    }

    public function delete(Request $request)
    {
        $previousdata = ResignInfo::find($request['id']);
        $item = ResignInfo::find($request['id']);
        if ($item && $item->delete()) {
            $logArray = array(
                'moduleId'  => 5,
                'controllerName'  => 'ResignInfoController',
                'tableName'  => 'hr_resign_info',
                'operation'  => 'delete',
                'previousData'  => $previousdata,
                'primaryIds'  => [$previousdata->id]
            );
            Service::createLog($logArray);
            if ($request->ajax())
                return response::json(['res' => 1, 'msg' => 'Resign information deleted successfully.']);
            else
                return redirect('/hr/resignInfo')->with('success', 'Resign information deleted successfully.');
        } else {
            if ($request->ajax())
                return response::json(['res' => 0, 'msg' => 'Resign information deleted successfully.']);
            else
                return redirect('/hr/resignInfo')->with('error', 'Resign information deleted successfully.');
        }
  
    }

    public function getEmployeeCurrentData(Request $request)
    {
        $data = [];
        $user = User::find($request->id);
        if ($user) {
            $employee = $user->employee ?? null;
            $organization = $employee->organization ?? null;
            $data['fiscal_year'] = [
                'id' => $organization->fiscal_year_fk, 
                'name' => $organization->fiscalYear->name
            ];
            $data['grade'] = [
                'id' => $organization->grade, 
                'name' => $organization->gradeInfo->name
            ];
            $data['level'] = [
                'id' => $organization->level_id_fk, 
                'name' => $organization->level->name
            ];
            $data['position'] = [
                'id' => $organization->position_id_fk, 
                'name' => $organization->position->name
            ];
            $data['recruitment_type'] = [
                'id' => $organization->recruitment_type, 
                'name' => $organization->recruitmentType->name
            ];
            $data['salary_increment_year'] = [
                'id' => $organization->salary_increment_year, 
                'name' => $organization->salary_increment_year
            ];

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
    }
}
