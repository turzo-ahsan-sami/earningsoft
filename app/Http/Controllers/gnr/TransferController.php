<?php

namespace App\Http\Controllers\hr;

use App\ConstValue;
use App\gnr\GnrBranch;
use App\gnr\GnrCompany;
use App\hr\Transfer;
use App\Http\Controllers\Controller;
use App\Http\Controllers\gnr\Service;
use App\Service\EasyCode;
use App\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;

class TransferController extends Controller
{
    protected $easycode;

    public function __construct()
    {
        $this->easycode = new EasyCode();
    }

    public function index()
    {
        $data['pageTitle'] = 'Transfer List';
        $data['addBtnLabel'] = 'New Transfer';
        $data['addBtnUrl'] = url('hr/transfer/create');

        $model = new Transfer;
        $data['modelPosition'] = new Transfer;
        $data['attributes'] = $model->attributes();

        $authUser = Auth::user();
        $data['easycode'] = $this->easycode;
        $data['model'] = $model->orderbyRaw('hr_transfer.id desc, hr_transfer.status desc');

        if ($authUser->getRoleId() != ConstValue::USER_ID_SUPER_ADMIN) {
            if ($authUser->branchId != ConstValue::BRANCH_ID_HEAD_OFFICE) {
                $data['model'] = $data['model']
                    ->leftJoin('users', 'hr_transfer.users_id_fk', '=', 'users.id')
                    ->where('users.branchId', $authUser->branchId);
            }
        } else {
            $data['model'] = $data['model']->where('users_id_fk', $authUser->id);
        }

        $data['model'] = $data['model']->paginate(env('PAGE_SIZE'));

        return view('hr.transfer.index', ['model' => $model, 'data' => $data]);
    }

    public function create(Request $request)
    {
        $data['pageTitle'] = 'New Transfer';

        $data['companyData'] = GnrCompany::options('Select any');

        $data['modelNew'] = true;
        $data['model'] = new Transfer;
        $data['attributes'] = $data['model']::attributes();
        $data['placeholder'] = $data['model']::placeholder();
        $data['easycode'] = $this->easycode;
        $data['allRecruitmentTypeLabel'] = 'All Transfer List';
        $data['allRecruitmentTypeUrl'] = url('hr/transfer');
        $data['closeBtnUrl'] = url('hr/transfer');

        if (isset($_POST['_token'])) {
            $req = $request->all();

            $req['created_by'] = Auth::user()->id;
            $req['updated_by'] = Auth::user()->id;

            if (isset($req['effect_month']) && $req['effect_month'] != '') {
                $req['effect_month'] = date("Y-m-d", strtotime('01-' . $req['effect_month']));
            }

            if (isset($req['effect_date']) && $req['effect_date'] != '') {
                $req['effect_date'] = date("Y-m-d", strtotime($req['effect_date']));
            }

            if (isset($req['release_date']) && $req['release_date'] != '') {
                $req['release_date'] = date("Y-m-d", strtotime($req['release_date']));
            }

            if (isset($req['joining_date']) && $req['joining_date'] != '') {
                $req['joining_date'] = date("Y-m-d", strtotime($req['joining_date']));
            }

            $req['created_at'] = date("Y-m-d H:i:s");
            $req['updated_at'] = date("Y-m-d H:i:s");
            $req['status'] = 'Pending';

            $validator = Validator::make($req, Transfer::createRules());
            $validator->setAttributeNames(Transfer::attributes());

            if ($validator->fails()) {
                return redirect('/hr/transfer/create')->withErrors($validator, 'error')->withInput();
            } else {
                $data['model']->fill($req);
                if ($data['model']->save()) {
                    $logArray = array(
                        'moduleId' => 5,
                        'controllerName' => 'TransferController',
                        'tableName' => 'hr_transfer',
                        'operation' => 'insert',
                        'primaryIds' => [DB::table('hr_transfer')->max('id')],
                    );
                    Service::createLog($logArray);
                    return redirect('/hr/transfer')->with('success', 'Transfer created successfully.');
                } else {
                    return redirect('/hr/transfer')->with('error', 'Failed to create transfer.')->withInput();
                }
            }
        }
        return view('hr.transfer.create', ['data' => $data]);
    }

    public function update($id, Request $request)
    {
        $data['pageTitle'] = 'Update Transfer';

        $data['companyData'] = GnrCompany::options('Select any');

        $data['model'] = Transfer::find($id);
        if ($data['model']->effect_month != '0000-00-00') {
            $data['model']->effect_month = date("m-Y", strtotime($data['model']->effect_month));
        }

        if ($data['model']->effect_date != '0000-00-00') {
            $data['model']->effect_date = date("d-m-Y", strtotime($data['model']->effect_date));
        }

        if ($data['model']->release_date != null) {
            $data['model']->release_date = date("d-m-Y", strtotime($data['model']->release_date));
        }

        if ($data['model']->joining_date != null) {
            $data['model']->joining_date = date("d-m-Y", strtotime($data['model']->joining_date));
        }

        $data['attributes'] = $data['model']::attributes();
        $data['placeholder'] = $data['model']::placeholder();
        $data['easycode'] = $this->easycode;
        $data['allRecruitmentTypeLabel'] = 'All Transfer List';
        $data['allRecruitmentTypeUrl'] = url('hr/transfer');
        $data['closeBtnUrl'] = url('hr/transfer');

        if (isset($_POST['_token'])) {
            $req = $request->all();

            $req['updated_by'] = Auth::user()->id;

            if (isset($req['effect_month']) && $req['effect_month'] != '') {
                $req['effect_month'] = date("Y-m-d", strtotime('01-' . $req['effect_month']));
            }

            if (isset($req['effect_date']) && $req['effect_date'] != '') {
                $req['effect_date'] = date("Y-m-d", strtotime($req['effect_date']));
            }

            if (isset($req['release_date']) && $req['release_date'] != '') {
                $req['release_date'] = date("Y-m-d", strtotime($req['release_date']));
            }

            if (isset($req['joining_date']) && $req['joining_date'] != '') {
                $req['joining_date'] = date("Y-m-d", strtotime($req['joining_date']));
            }

            $req['updated_at'] = date("Y-m-d H:i:s");
            $req['status'] = 'Pending';

            $validator = Validator::make($req, Transfer::updateRules());
            $validator->setAttributeNames(Transfer::attributes());

            if ($validator->fails()) {
                return redirect('/hr/transfer/update/' . $id)->withErrors($validator, 'error')->withInput();
            } else {
                $previousdata = Transfer::find($id);
                $data['model']->fill($req);
                if ($data['model']->save()) {
                    $logArray = array(
                        'moduleId' => 5,
                        'controllerName' => 'TransferController',
                        'tableName' => 'hr_transfer',
                        'operation' => 'update',
                        'previousData' => $previousdata,
                        'primaryIds' => [$previousdata->id],
                    );
                    Service::createLog($logArray);
                    return redirect('/hr/transfer')->with('success', 'Transfer created successfully.');
                } else {
                    return redirect('/hr/transfer/update/' . $id)->with('error', 'Failed to create transfer.')->withInput();
                }
            }
        }
        return view('hr.transfer.create', ['data' => $data]);
    }

    public function view($id, Request $request)
    {
        $data['pageTitle'] = 'Transfer Details';

        $data['model'] = Transfer::find($id);
        $data['attributes'] = $data['model']::attributes();
        $data['allRecruitmentTypeLabel'] = 'All Transfer List';
        $data['allRecruitmentTypeUrl'] = url('hr/transfer');
        $data['easycode'] = $this->easycode;

        return view('hr.transfer.view', ['data' => $data]);
    }

    public function getEmployeeCurrentData(Request $request)
    {
        $data = [];
        $user = User::find($request->id);
        
        if ($user) {
            $samityAssigned = DB::table('mfn_samity')
                ->where('fieldOfficerId', $user->employee->id)
                ->pluck('fieldOfficerId')
                ->toArray();

            if (sizeof($samityAssigned) > 0) {
                $data['emp_id'] = [
                    'id' => @$user->employee->emp_id,
                    'name' => @$user->employee->emp_id,
                    'position' => @$user->employee->organization->position->name,
                    'samityStatus' => 1
                ];
            } else {
                $data['emp_id'] = [
                    'id' => @$user->employee->emp_id,
                    'name' => @$user->employee->emp_id,
                    'position' => @$user->employee->organization->position->name,
                    'samityStatus' => 0
                ];
            }

            if ($request->ajax()) {
                return response::json(['res' => 1, 'value' => $data]);
            } else {
                return redirect('/hr');
            }
        } else {
            if ($request->ajax()) {
                return response::json(['res' => 0]);
            } else {
                return redirect('/hr');
            }
        }
    }

    public function approved(Request $request)
    {
        $item = Transfer::find(intval($request->id));
        $previousdata = Transfer::find(intval($request->id));

        if (isset($_POST['_token'])) {
            if ($item) {

                $item->status = 'Approved';
                $item->approved_by_fk = Auth::id();
                $item->approved_time = date("Y-m-d H:i:s");
                $item->save();
                $logArray = array(
                    'moduleId' => 5,
                    'controllerName' => 'TransferController',
                    'tableName' => 'hr_transfer',
                    'operation' => 'update',
                    'previousData' => $previousdata,
                    'primaryIds' => [$previousdata->id],
                );
                Service::createLog($logArray);

                if ($request->ajax()) {
                    return response::json(['res' => 1, 'msg' => 'Transfer approved successfully.']);
                } else {
                    return redirect('/hr/transfer/view/' . $item->id)->with('success', 'Transfer approved successfully.');
                }
            } else {
                if ($request->ajax()) {
                    return response::json(['res' => 0, 'msg' => 'Transfer not found.']);
                } else {
                    return redirect('/hr/transfer/view/' . $item->id)->with('error', 'Promotion not found.');
                }
            }
        } else {
            if ($request->ajax()) {
                return response::json(['res' => 0, 'msg' => 'Invalid request.']);
            } else {
                return redirect('/hr/transfer/view/' . $item->id)->with('error', 'Invalid request.');
            }
        }
    }

    public function transferEffect($item)
    {

        $currentBranch = GnrBranch::find($item->cur_branch_id_fk);

        //update user table
        $user = User::find($item->users_id_fk);
        $user->project_id_fk = $item->cur_project_id_fk;
        $user->project_type_id_fk = $currentBranch->projectTypeId ?? $user->project_type_id_fk;
        $user->branchId = $item->cur_branch_id_fk;
        $user->save();

        //update emp
        $emp = $user->employee;
        $emp->emp_id = $item->cur_emp_id;
        $emp->save();

        //update emp org info
        $org = $user->employee->organization;
        $org->project_id_fk = $item->cur_project_id_fk;
        // Project type comes from branch table
        $org->project_type_id_fk = $currentBranch->projectTypeId ?? $org->project_type_id_fk;
        $org->branch_id_fk = $item->cur_branch_id_fk;
        $org->save();
    }

    public function confirmed(Request $request)
    {
        $item = Transfer::where('id', intval($request->id))->where('status', 'Approved')->first();
        if (isset($_POST['_token'])) {
            if ($item) {

                $item->status = 'Confirmed';
                $item->confirmed_by_fk = Auth::id();
                $item->confirmed_time = date("Y-m-d H:i:s");
                if ($item->save()) {
                    $this->transferEffect($item);
                }

                if ($request->ajax()) {
                    return response::json(['res' => 1, 'msg' => 'Transfer confirmed successfully.']);
                } else {
                    return redirect('/hr/transfer/view/' . $item->id)->with('success', 'Transfer confirmed successfully.');
                }
            } else {
                if ($request->ajax()) {
                    return response::json(['res' => 0, 'msg' => 'Transfer not found.']);
                } else {
                    return redirect('/hr/transfer/view/' . $item->id)->with('error', 'Promotion not found.');
                }
            }
        } else {
            if ($request->ajax()) {
                return response::json(['res' => 0, 'msg' => 'Invalid request.']);
            } else {
                return redirect('/hr/transfer/view/' . $item->id)->with('error', 'Invalid request.');
            }
        }
    }
}
