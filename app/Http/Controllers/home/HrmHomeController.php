<?php
namespace App\Http\Controllers\home;

use App\gnr\FiscalYear;
use App\gnr\GnrBranch;
use App\hr\EdpsReceive;
use App\hr\EmployeeGeneralInfo;
use App\hr\EpsReceive;
use App\hr\GratuityItem;
use App\hr\ProvidentFundReceive;
use App\hr\SecurityMoneyCollection;
use App\hr\WelfareFundReceive;
use App\Http\Controllers\Controller;
use App\Service\EasyCode;
use App\Service\LeaveService;
use App\Service\Service;
use App\User;
use Auth;
use DB;
use Route;
use stdClass;

class HrmHomeController extends Controller
{

    public function __construct()
    {
        $this->easycode = new EasyCode;
        $this->service = new Service;
    }

    public function index()
    {

        $path = Route::current()->uri();
        $data['utype'] = $this->easycode->getUserRole();

        if ($data['utype']['roleid'] == 2) {
            return view('homePages.hrmHomePages.empHrmHome');
        } elseif ($data['utype']['roleid'] == 13) { // condition for admin
            return view('homePages.hrmHomePages.adminHrmHome');
        } elseif ($data['utype']['roleid'] == 1) { // condition for superadmin
            return view('homePages.hrmHomePages.superAdminHrmHome');
        } else {
            return view('homePages.hrmHomePages.viewHrmHome')->with('path', $path);
        }
    }

    public function loadLoan()
    {
        // dd(Auth::user()->id);
        $data['motorcyleloan'] = $this->service->getUserVehicleLoanData(Auth::user()->id, 1);
        $data['bicyleloan'] = $this->service->getUserVehicleLoanData(Auth::user()->id, 2);
        $data['advancedloan'] = $this->service->getUserAdvancedLoanData(Auth::user()->id);
        $data['pfloan'] = $this->service->getUserPfLoanData(Auth::user()->id);
        // dd($data['pfloan']);

        return view('homePages.hrmHomePages.loadLoan', ['data' => $data]);
    }

    public function loadAdvance()
    {

        $empId = User::find(Auth::user()->id)->employee->id;
        $advTypes = DB::table('acc_adv_register_type')->get();

        foreach ($advTypes as $key => $item) {

            $advRegister = DB::table('acc_adv_register')->where('employeeId', $empId)->where('advRegType', $item->id)->sum('amount');
            $advReceive = DB::table('acc_adv_receive')->where('employeeId', $empId)->where('regTypeId', $item->id)->sum('amount');

            $data[] = array(
                'empId' => $empId,
                'advId' => $item->id,
                'type' => $item->name,
                'payment' => $advRegister,
                'receive' => $advReceive,
                'balance' => $advRegister - $advReceive,
            );

        }
        // dd($data);

        return view('homePages.hrmHomePages.loadAdvance', ['data' => $data]);
    }

    public function loadAdvanceDetails($empId, $advId)
    {

        $advTypeName = DB::table('acc_adv_register_type')->where('id', $advId)->first()->name;
        $advRegister = DB::table('acc_adv_register')->where('employeeId', $empId)->where('advRegType', $advId)->get();
        $advReceive = DB::table('acc_adv_receive')->where('employeeId', $empId)->where('regTypeId', $advId)->get();
        // dd($advTypeName);

        foreach ($advRegister as $key => $item) {
            $regData[] = array(
                'date' => $item->advPaymentDate,
                'payment' => $item->amount,
                'receive' => 0,
            );
        }

        if ($advReceive->count() > 0) {
            foreach ($advReceive as $key => $item) {
                $recData[] = array(
                    'date' => $item->receivePaymentDate,
                    'payment' => 0,
                    'receive' => $item->amount,
                );
            }
        } else {
            $recData = [];
        }

        $data = array_merge($regData, $recData);
        $data = collect($data)->sortBy('date');
        // dd($data);

        return view('homePages.hrmHomePages.loadAdvanceDetails', ['data' => $data, 'advTypeName' => $advTypeName]);
    }

    public function loadDepositDetails($userId)
    {

        $path = Route::current()->uri();
        $depositType = explode('/', $path)[2];
        $service = new Service;

        // pf deposit collection
        if ($depositType == 'pfdeposit') {

            $pfInfo = $service->getUserPfInfo($userId);

            return view('homePages.hrmHomePages.loadPfDetails')->with('pfInfo', $pfInfo);
        }

        // edps collection
        elseif ($depositType == 'edpsdeposit') {

            $edpsInfo = $service->getUserEdpsInfo($userId);

            return view('homePages.hrmHomePages.loadEdpsDetails')->with('edpsInfo', $edpsInfo);
        }

        // security fund collection
        elseif ($depositType == 'securitydeposit') {

            $sfInfo = $service->getUserSecurityFundInfo($userId);

            return view('homePages.hrmHomePages.loadSfDetails')->with('sfInfo', $sfInfo);
        }

        // welfare fund collection
        elseif ($depositType == 'welfaredeposit') {

            $wfInfo = $service->getUserWelfareFundInfo($userId);

            return view('homePages.hrmHomePages.loadWfDetails')->with('wfInfo', $wfInfo);
        }

        // pension scheme collection
        elseif ($depositType == 'eps') {

            $epsInfo = $service->getUserEpsInfo($userId);

            return view('homePages.hrmHomePages.loadEpsDetails')->with('epsInfo', $epsInfo);
        }

        // gratuity collection
        elseif ($depositType == 'gratuity') {

            $gratuityInfo = $service->getUserGratuityInfo($userId);

            return view('homePages.hrmHomePages.loadGratuityDetails')->with('gratuityInfo', $gratuityInfo);
        }

    }

    public function loadDeposit()
    {
        $data['pfdeposit'] = ProvidentFundReceive::getUserPfReveice(Auth::user()->id);
        $data['securitydeposit'] = SecurityMoneyCollection::getUserSdReveice(Auth::user()->id);
        $data['edpsdeposit'] = EdpsReceive::getUserEdpsReveice(Auth::user()->id);
        $data['edpsdepositWithdraw'] = DB::table('hr_emp_edps_withdraw')->where('users_id_fk', Auth::user()->id)->sum('total');

        // $data['gratuitydeposit'] = GratuityItem::getUserGratuityReveice(Auth::user()->id);
        $data['gratuitydeposit'] = GratuityItem::where('users_id_fk', Auth::user()->id)->where('job_duration_month', '>=', 60)->max('total_amount');
        if (!$data['gratuitydeposit']) {
            $data['gratuitydeposit'] = 0;
        }

        $data['welfaredeposit'] = WelfareFundReceive::getUserWfReveice(Auth::user()->id);
        $data['eps'] = EpsReceive::getUserEpsReveice(Auth::user()->id);
        // dd($data['pfdeposit']);
        $data['userId'] = Auth::user()->id;

        return view('homePages.hrmHomePages.loadDeposit', ['data' => $data]);
    }

    public function loadProfile()
    {
        $data['utype'] = $this->easycode->getUserRole();
        $data['model'] = EmployeeGeneralInfo::find($data['utype']['empIdFk']);
        $data['attributes'] = $data['model']::attributes();
        $data['easycode'] = $this->easycode;
        // dd($data['model']);

        return view('homePages.hrmHomePages.loadProfile', ['data' => $data]);
    }

    // public function loadLeave(){
    //
    //     $userId = Auth::user()->id;
    //     $employeeId = DB::table('users')->where('id', $userId)->value('emp_id_fk');
    //     $recruitmentType = DB::table('hr_emp_org_info')->where('emp_id_fk', $employeeId)->value('recruitment_type');
    //     $gender = DB::table('hr_emp_general_info')->where('id', $employeeId)->value('sex');
    //
    //     $leaveType = DB::table('hr_leave_type')
    //                 ->where('recruitment_id', $recruitmentType)
    //                 ->where('name', '!=', 'NPL')
    //                 ->select('id', 'name', 'total_day')
    //                 ->get();
    //                 // dd($leaveType);
    //
    //     if ($gender == 'Male') {
    //         $leaveType = $leaveType->where('name', '!=', 'Maternity Leave');
    //     }
    //     elseif($gender == 'Female')
    //     {
    //         $leaveType = $leaveType->where('name', '!=', 'Paternity Leave');
    //     }
    //
    //     $leavesInfo = DB::table('hr_leave_emp')->where('user_id_fk', $userId)
    //                 ->whereIn('leave_type_id', $leaveType->pluck('id')->toArray())
    //                 ->select('leave_type_id', 'total_leave', 'remaining_leave')
    //                 ->get();
    //
    //     foreach ($leaveType as $key => $type) {
    //
    //         $leaveData = $leavesInfo->where('leave_type_id', $type->id)->first();
    //         if($leaveData){
    //             $spentLeave = $leaveData->total_leave;
    //         }
    //         else {
    //             $spentLeave = 0;
    //         }
    //
    //         $data[] = array(
    //             'leaveType'         => $type->name,
    //             'totalLeave'        => $type->total_day,
    //             'totalSpentLeave'   => $spentLeave,
    //             'remaining_leave'   => $type->total_day - $spentLeave,
    //         );
    //     }
    //
    //     return view('homePages.hrmHomePages.loadLeave')->with('data', $data);
    // }

    public function loadLeave()
    {

        $_data = new stdClass;

        $selectedBranch = GnrBranch::find(Auth::user()->branchId) ?? (object) ['id' => '*'];
        $userBranchId = $selectedBranch->id;
        // branch date
        $userBranchStartDate = DB::table('gnr_branch')->where('id', $userBranchId)->value('aisStartDate');
        $userBranchDate = DB::table('acc_day_end')->where('branchIdFk', $userBranchId)->max('date');

        if ($userBranchDate == null) {
            $userBranchDate = $userBranchStartDate;
        }

        $currentFiscalYear = FiscalYear::getCurrent();
        $selectedUser = User::find(Auth::user()->id) ?? (object) ['id' => '*'];
        $selectedFiscalYear = $currentFiscalYear;

        $user = User::with([
            'employee',
            'employee.organization',
            'employee.organization.recruitmentType',
            'employee.organization.position',
            'employee.organization.branch',
        ])->where('id', $userId = Auth::user()->id)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Sorry, Do not find the user');
        }

        $leaveService = new LeaveService();
        $leaveTypes = $leaveService->fetchEmployeeLeaveTypesByFiscalYear($user->employee, $selectedFiscalYear);

        $_data->fiscalYearMonthList = $this->dateRangeList($selectedFiscalYear->fyStartDate, $selectedFiscalYear->fyEndDate);
        $_data->user = $user;
        $_data->leaveTypes = $leaveTypes;
        $_data->leaveService = $leaveService;
        $_data->selectedFiscalYear = $selectedFiscalYear;
        // dd($_data);

        return view('homePages.hrmHomePages.loadLeave', [
            'selectedFiscalYear' => $selectedFiscalYear,
            'selectedBranch' => $selectedBranch,
            'selectedUser' => $selectedUser,
            '_data' => $_data,
        ]);
    }

    public function dateRangeList($date1, $date2)
    {
        $list = [];

        $date1FirstDay = date('Y-m-d', strtotime($date1));
        $date2LastDay = date('Y-m-t', strtotime($date2));

        while ($date1FirstDay < $date2LastDay) {
            $month = date('Y-m-d', strtotime($date1FirstDay));
            $list[] = $month;
            $date1FirstDay = date('Y-m-d', strtotime($month . '+ 1 Month'));
        }

        return $list;
    }

    public function loadHrmTab1()
    {
        $data['utype'] = $this->easycode->getUserRole();
        return view('homePages.hrmHomePages.loadHrmTab1')->with('role', $data['utype']['roleid']);
    }

    public function loadHrmTab2()
    {
        return view('homePages.hrmHomePages.loadHrmTab2');
    }

    public function loadHrmTab3()
    {
        return view('homePages.hrmHomePages.loadHrmTab3');
    }

    public function loadHrmTab4()
    {
        return view('homePages.hrmHomePages.loadHrmTab4');
    }
}
