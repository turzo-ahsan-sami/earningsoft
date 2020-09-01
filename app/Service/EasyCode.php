<?php

namespace App\Service;

use App\accounting\AccAdvanceReceive;
use App\accounting\AccAdvRegister;
use App\gnr\FiscalYear;
use App\gnr\GnrBranch;
use App\gnr\GnrProject;
use App\gnr\GnrRole;
use App\gnr\GnrUserRole;
use App\hr\ActingBenefit;
use App\hr\AdvancedSalaryLoan;
use App\hr\AdvancedSalaryLoanReceive;
use App\hr\BenefitType;
use App\hr\Department;
use App\hr\Edps;
use App\hr\EdpsReceive;
use App\hr\EmployeeGeneralInfo;
use App\hr\Grade;
use App\hr\HrLeaveApplication;
use App\hr\IncomeTax;
use App\hr\IncomeTaxReceive;
use App\hr\InsuranceReceive;
use App\hr\InsuranceSettings;
use App\hr\OpeningBalanceFund;
use App\hr\OpeningBalanceLoanAdvancedSalary;
use App\hr\OpeningBalanceLoanPf;
use App\hr\OpeningBalanceLoanPfSchedule;
use App\hr\OpeningBalanceLoanVehicle;
use App\hr\OpeningBalanceLoanVehicleSchedule;
use App\hr\OsfReceive;
use App\hr\OsfSettings;
use App\hr\PfLoanReceive;
use App\hr\Position;
use App\hr\PromotionIncrement;
use App\hr\ProvidentFundInterestReceive;
use App\hr\ProvidentFundLoan;
use App\hr\ProvidentFundLoanReview;
use App\hr\ProvidentFundLoanSchedule;
use App\hr\ProvidentFundReceive;
use App\hr\ProvidentFundSettings;
use App\hr\ResignInfo;
use App\hr\RetirementInfo;
use App\hr\SalaryGenerate;
use App\hr\SalaryStructure;
use App\hr\SecurityMoney;
use App\hr\SecurityMoneyCollection;
use App\hr\StopSalaryBenefit;
use App\hr\TerminateInfo;
use App\hr\VehicleLoan;
use App\hr\VehicleLoanReceive;
use App\hr\VehicleLoanSchedule;

use App\Traits\ValidateRouteAccess;
use App\hr\VehicleType;
use App\hr\WelfareFundReceive;
use App\hr\WelfareFundSettings;
use App\accounting\process\AccYearEnd;
use App\User;
use Auth;
use DB;
use Route;
use \Carbon\Carbon;

class EasyCode
{
    use ValidateRouteAccess;
    //for employee information directory
    public $empInfoDirBase;

    //for employee information directory
    public $empInfoDirUrl;

    //for document manager
    public $documentManagerDirBase;
    public $documentManagerDirUrl;

    //for get status
    public $statusType = 'button';

    public function __construct($empInfoDirBase = null, $empInfoDirUrl = null, $statusType = null, $documentManagerDirBase = null, $documentManagerDirUrl = null)
    {

        if ($empInfoDirBase == null) {
            $this->empInfoDirBase = base_path('storage/app/public/employee-information');
        } else {
            $this->empInfoDirBase = $empInfoDirBase;
        }

        if ($empInfoDirUrl == null) {
            $this->empInfoDirUrl = asset('/../storage/app/public/employee-information');
        } else {
            $this->empInfoDirUrl = $empInfoDirUrl;
        }

        if ($documentManagerDirBase == null) {
            $this->documentManagerDirBase = base_path('storage/app/public/document-manager');
        } else {
            $this->documentManagerDirBase = $documentManagerDirBase;
        }

        if ($documentManagerDirUrl == null) {
            $this->documentManagerDirUrl = asset('/../storage/app/public/document-manager');
        } else {
            $this->documentManagerDirUrl = $documentManagerDirUrl;
        }

        if ($statusType == null) {
            $this->statusType = 'button';
        } else {
            $this->statusType = $statusType;
        }

    }

    public function getUserOtherAdvance($emp)
    {
        $advRegister = floatval(@AccAdvRegister::select(\DB::raw('SUM(amount) as amount'))
            ->where('employeeId', $emp->id)
            ->first()->amount);
        $advReveiced = floatval(@AccAdvanceReceive::select(\DB::raw('SUM(amount) as amount'))
            ->where('employeeId', $emp->id)
            ->first()->amount);
        return $advRegister - $advReveiced;
    }

    public function getUserAdvancedLoanBalance($uid)
    {
        $opbalance = floatval(@OpeningBalanceLoanAdvancedSalary::select(\DB::raw('SUM(ob_amount) as ob_amount'))
            ->where('users_id_fk', $uid)->first()->ob_amount);

        $curbalance = floatval(@AdvancedSalaryLoan::select(\DB::raw('SUM(approved_amount) as approved_amount'))
            ->where('users_id_fk', $uid)->where('status', 'Approved')->first()->approved_amount);

        $collection = floatval(@AdvancedSalaryLoanReceive::select(\DB::raw('SUM(amount) as amount'))
            ->where('users_id_fk', $uid)->where('payment_status', 'Paid')->first()->amount);

        return ($opbalance + $curbalance) - $collection;
    }

    public function getUserAdvancedLoanData($uid)
    {

        $advancedLoanOpeningBalance = DB::table('hr_ob_loan_vehicle')->where('users_id_fk', $uid)->get();
        $advancedloans = DB::table('hr_advanced_salary_loan')->where('users_id_fk', $uid)->where('status', 'Approved')->get();

        if ($advancedloans->count() > 0) {
            $loanType = 'asloan';
            $loanId = $advancedloans->first()->id;
        } elseif ($advancedLoanOpeningBalance->count() > 0) {
            $loanType = 'obasloan';
            $loanId = $advancedLoanOpeningBalance->first()->id;
        } else {
            $loanType = '';
            $loanId = 0;
        }

        $opbalance = floatval(@OpeningBalanceLoanAdvancedSalary::select(\DB::raw('SUM(ob_amount) as ob_amount'))->where('users_id_fk', $uid)->first()->ob_amount);

        $curbalance = floatval(@AdvancedSalaryLoan::select(\DB::raw('SUM(approved_amount) as approved_amount'))
            ->where('users_id_fk', $uid)->where('status', 'Approved')->first()->approved_amount);

        $collection = floatval(@AdvancedSalaryLoanReceive::select(\DB::raw('SUM(amount) as amount'))
            ->where('users_id_fk', $uid)->where('payment_status', 'Paid')->first()->amount);

        $opCollection = DB::table('hr_ob_loan_advanced_salary')->where('users_id_fk', $uid)->sum('ob_amount');

        // dd(($opbalance + $curbalance) - ($collection + $opCollection));
        $balance = array(
            'loanType' => $loanType,
            'loanId' => $loanId,
            'loanAmount' => $opbalance + $curbalance,
            'collectionAmount' => $collection + $opCollection,
            'outstanding' => ($opbalance + $curbalance) - ($collection + $opCollection)
        );

        return $balance;
    }

    public function getUserVehicleLoanBalance($uid, $type)
    {
        $opbalance = floatval(@OpeningBalanceLoanVehicle::select(\DB::raw('SUM(ob_amount) as ob_amount'))->where('users_id_fk', $uid)->where('vehicle_type_fk', $type)->first()->ob_amount);

        $curbalance = floatval(@VehicleLoan::select(\DB::raw('SUM(total_receivable) as total_receivable'))->where('users_id_fk', $uid)->where('status', 'Approved')->where('vehicle_type_fk', $type)->first()->total_receivable);

        $opcollection = floatval(@VehicleLoanReceive::select(\DB::raw('SUM(hr_vehicle_loan_receive.amount) as amount'))->leftJoin('hr_ob_loan_vehicle', 'hr_vehicle_loan_receive.vehicle_loan_fk', '=', 'hr_ob_loan_vehicle.id')->where('hr_vehicle_loan_receive.users_id_fk', $uid)->where('hr_ob_loan_vehicle.vehicle_type_fk', $type)->where('type', 'Opening Balance')->first()->amount);

        $curcollection = floatval(@VehicleLoanReceive::select(\DB::raw('SUM(hr_vehicle_loan_receive.amount) as amount'))->leftJoin('hr_vehicle_loan', 'hr_vehicle_loan_receive.vehicle_loan_fk', '=', 'hr_vehicle_loan.id')->where('hr_vehicle_loan_receive.users_id_fk', $uid)->where('hr_vehicle_loan.vehicle_type_fk', $type)->where('type', 'Regular')->first()->amount);

        return ($opbalance + $curbalance) - ($opcollection + $curcollection);
    }

    public function getUserVehicleLoanData($uid, $type)
    {

        $vehicleOpeningBalance = OpeningBalanceLoanVehicle
            ::where('users_id_fk', $uid)
            ->where('settlement_type', '!=', 'Settlement')
            ->where('settlement_type', '!=', 'Return')
            ->where('vehicle_type_fk', $type)
            ->get();

        $vehicleloans = VehicleLoan
            ::where('users_id_fk', $uid)
            ->where('status', 'Approved')
            ->where('vehicle_type_fk', $type)
            ->where('settlement_type', '!=', 'Settlement')
            ->where('settlement_type', '!=', 'Return')
            ->get();
        // dd($vehicleOpeningBalance);

        $loanId = 0;
        $opLoanId = 0;
        if ($vehicleloans->count() > 0) {
            $loanId = $vehicleloans->first()->id;
        }
        if ($vehicleOpeningBalance->count() > 0) {
            $opLoanId = $vehicleOpeningBalance->first()->id;
        }
        // dd($opLoanId);

        $opbalance = $vehicleOpeningBalance->sum('approved_amount');
        $curbalance = $vehicleloans->sum('approved_amount');
        $opcollection = $vehicleOpeningBalance->sum('ob_total');

        $curcollection = VehicleLoanReceive::where('hr_vehicle_loan_receive.users_id_fk', '=', $uid)
            ->whereIn('vehicle_loan_fk', $vehicleloans->pluck('id')->toArray())
            ->get();

        if ($curcollection->count() == 0) {
            $curcollection = VehicleLoanReceive::where('hr_vehicle_loan_receive.users_id_fk', '=', $uid)
                ->whereIn('vehicle_loan_fk', $vehicleOpeningBalance->pluck('id')->toArray())
                ->get();
        }

        $curcollection = $curcollection->sum('amount');

        $balance = array(
            'loanId' => $loanId,
            'opLoanId' => $opLoanId,
            'loanAmount' => $curbalance,
            'opLoanAmount' => $opbalance,
            'opCollectionAmount' => $opcollection,
            'collectionAmount' => $curcollection,
            'opOutstanding' => $opbalance - $opcollection,
            'outstanding' => $curbalance - $curcollection
        );

        return $balance;
    }

    public static function chkHasAccess($uid = null, $module = null, $function = null, $subfunction = null)
    {
        return ValidateRouteAccess::hasAccess($uid, $module, $function, $subfunction);
    }

    public static function getUserAllDataAccess($uid = null)
    {
        if ($uid == null) {
            $uid = Auth::user()->id;
        }

        $data = @GnrUserRole::where('userIdFk', $uid)->first();
    }

    public static function getUserRole($uid = null)
    {
        if ($uid == null) {
            $uid = Auth::user()->id;
        }

        $getRoleId = @GnrUserRole::where('userIdFk', $uid)->first();
        if ($uid == 1) {
            return [
                'roleid' => 1,
                'rolename' => 'Super Admin',
                'branchid' => 1,
                'branchname' => 'Head Office',
                'departmentid' => '',
                'departmentName' => '',
                'username' => 'superadmin',
                'empIdFk' => '',
                'emp_id' => '',
                'positionname' => '',
                'positionid' => '',
            ];
        } else {
            $userInfo = User::find($uid);

            return [
                'roleid' => @$getRoleId->roleId,
                'rolename' => @GnrRole::find($getRoleId->roleId)->name,
                'branchid' => @$userInfo->employee->organization->branch_id_fk,
                'branchname' => @$userInfo->employee->organization->branch->name,
                'departmentid' => @$userInfo->employee->organization->department,
                'departmentName' => @$userInfo->employee->organization->departmentInfo->name,
                'username' => $userInfo->username,
                'empIdFk' => $userInfo->emp_id_fk,
                'emp_id' => @$userInfo->employee->emp_id,
                'positionname' => @$userInfo->employee->organization->position->name,
                'positionid' => @$userInfo->employee->organization->position_id_fk,
            ];
        }
    }

    public static function getEmployeeLoanData($id)
    {
        $userInfo = User::find($id);

        $data = [];
        if (!$userInfo) {
            return $data;
        }

        /* organization info */
        $orgInfo = $userInfo->employee->organization;

        //$data['job_age'] = Carbon::parse($orgInfo->joining_date)->diffForHumans(Carbon::now());
        $data['job_age'] = self::get_date_diff($orgInfo->joining_date, date("Y-m-d H:i:s"));

        $pfReceiveLoan = ProvidentFundReceive::select(\DB::raw('SUM(org_amount) as org_amount, SUM(emp_amount) as emp_amount'))->where('users_id_fk', $userInfo->id)->groupby('users_id_fk')->limit(1)->first();
        $data['org_amount'] = $pfReceiveLoan['org_amount'];
        $data['emp_amount'] = $pfReceiveLoan['emp_amount'];

        $pfInterest = ProvidentFundInterestReceive::join('hr_pf_interest', 'hr_pf_interest_receive.interest_id_fk', '=', 'hr_pf_interest.id')
            ->where('hr_pf_interest_receive.users_id_fk', $userInfo->id)
            ->select('hr_pf_interest_receive.*')
            ->get();
        $data['pf_interest'] = $pfInterest->sum('profit_amount');

        $pfOpening = OpeningBalanceFund::where('users_id_fk', $userInfo->id)->first();
        if (floatval(@$pfOpening->pf_own) > 0) {
            $data['emp_amount'] += floatval($pfOpening->pf_own);
        }

        if (floatval(@$pfOpening->pf_org) > 0) {
            $data['org_amount'] += floatval($pfOpening->pf_org);
        }

        if (floatval(@$pfOpening->pf_interest) > 0) {
            $data['pf_interest'] += floatval($pfOpening->pf_interest);
        }

        /* Cal max pf loan amount */
        $pfcal['pfSettings'] = ProvidentFundSettings::where('project_id_fk', $userInfo->employee->organization->project_id_fk)->whereRaw('FIND_IN_SET(?,recruitment_type)', [$userInfo->employee->organization->recruitment_type])->first();

        if (@$userInfo->employee->organization->joining_date == '0000-00-00') {
            $pfcal['empJobAgeYear'] = 0;
        } else {
            $empJobAgeYear = self::get_date_diff_in_year(@$userInfo->employee->organization->joining_date, date("Y-m-d H:i:s"));
            $pfcal['empJobAgeYear'] = rtrim($empJobAgeYear, ' years');
        }

        $pfcal['storedAmount'] = $data;
        $pfcal['totalStoredAmount'] = floatval($pfcal['storedAmount']['org_amount']) + floatval($pfcal['storedAmount']['emp_amount']) + floatval($pfcal['storedAmount']['pf_interest']);

        if ($pfcal['pfSettings']) {
            $data['max_installment_amount'] = 0;
            if ($pfcal['empJobAgeYear'] >= $pfcal['pfSettings']->org_withdraw_min_job_year) {
                $data['max_installment_amount'] += (floatval($pfcal['storedAmount']['org_amount']) + floatval($pfcal['storedAmount']['pf_interest'])) / 100 * $pfcal['pfSettings']->loan_withdraw_percent;
            }

            if ($pfcal['empJobAgeYear'] >= $pfcal['pfSettings']->emp_withdraw_min_job_year) {
                $data['max_installment_amount'] += floatval($pfcal['storedAmount']['emp_amount']) / 100 * $pfcal['pfSettings']->loan_withdraw_percent;
            }

            /* Check existing approved pf loan */
            $existingBalange = self::getUserPfLoanBalance($userInfo->id);
            $data['max_installment_amount'] -= floatval($existingBalange);
            if ($data['max_installment_amount'] < 0) {
                $data['max_installment_amount'] = 0;
            }
            /* Check existing approved pf loan */

        }
        /* Cal max pf loan amount */

        return $data;
    }

    public static function getUserPfLoanBalance($uid)
    {
        // dd($uid);
        $balance = 0;
        $pfloan = ProvidentFundLoan::where('users_id_fk', $uid)->where('status', 'Approved')->get();
        // dd($pfloan);

        if (count($pfloan) > 0) {
            foreach ($pfloan as $loan):
                $loanamount = floatval(@$loan->loanReview->approved_loan_amount);
                $receivedLoanData = PfLoanReceive::select(\DB::raw('SUM(principal_amount) as amount'))
                    ->where('users_id_fk', $uid)
                    ->where('pf_loan_fk', $loan->id)
                    ->where('type', 'Regular')
                    ->groupby('users_id_fk')->limit(1)
                    ->first();

                $balance += floatval($loanamount) - floatval(@$receivedLoanData['amount']);
            endforeach;
        }

        $oppfloan = OpeningBalanceLoanPf::where('users_id_fk', $uid)->get();
        if (count($pfloan) > 0) {
            foreach ($pfloan as $loan):
                $loanamount = floatval(@$loan->loan_amount);
                $receivedLoanData = PfLoanReceive::select(\DB::raw('SUM(principal_amount) as amount'))
                    ->where('users_id_fk', $uid)
                    ->where('pf_loan_fk', $loan->id)
                    ->where('type', 'Opening Balance')
                    ->groupby('users_id_fk')->limit(1)->first();
                $balance += floatval($loanamount) - floatval(@$receivedLoanData['amount']);
            endforeach;
            // dd($uid);
        }
        // dd($balance);
        return $balance;
    }

    public static function getUserPfLoanData($uid)
    {

        // $uid = 48;
        // opening balance
        $pfOpeningBalance = DB::table('hr_ob_loan_pf')
            ->where('settlement_type', '!=', 'Settlement')
            ->where('settlement_type', '!=', 'Return')
            ->where('users_id_fk', $uid)
            ->get();

        $pfloans = DB::table('hr_pf_loan')
            ->where('users_id_fk', $uid)
            ->where('settlement_type', '!=', 'Settlement')
            ->where('settlement_type', '!=', 'Return')
            ->where('status', 'Approved')
            ->get();
        // dd($pfOpeningBalance);

        $opLoanId = 0;
        $loanId = 0;
        if ($pfloans->count() > 0) {
            $loanType = 'pf';
            $loanId = $pfloans->first()->id;
        }
        if ($pfOpeningBalance->count() > 0) {
            $loanType = 'obpf';
            $opLoanId = $pfOpeningBalance->first()->id;
        }
        // dd($loanIds);

        // loan amount
        $pfloanAmount = $pfloans->sum('accepted_amount');
        $pfloanOpAmount = $pfOpeningBalance->sum('loan_amount');

        // interest amount
        $pfLoanReviews = DB::table('hr_pf_loan_review')->where('pf_loan_id_fk', $loanId)->get();
        $pfloanInterestAmount = $pfLoanReviews->sum('interest_amount');
        $pfloanOpInterestAmount = $pfOpeningBalance->sum('interest_amount');
        // dd($pfloanInterestAmount);

        // collected amounts
        $pfReceive = DB::table('hr_pf_loan_receive')
            ->where('users_id_fk', $uid)
            ->get();

        $pfLoanReceivePri = $pfReceive->where('pf_loan_fk', $loanId)->where('type', 'Regular')->sum('principal_amount');
        $pfLoanOpReceivePri = $pfOpeningBalance->sum('ob_principal') + $pfReceive
                ->where('pf_loan_fk', $opLoanId)
                ->where('type', 'Opening Balance')
                ->sum('principal_amount');

        $pfLoanReceiveInt = $pfReceive->where('pf_loan_fk', $loanId)->where('type', 'Regular')->sum('interest_amount');
        $pfLoanOpReceiveInt = $pfOpeningBalance->sum('ob_interest') + $pfReceive
                ->where('pf_loan_fk', $opLoanId)
                ->where('type', 'Opening Balance')
                ->sum('interest_amount');

        // outstanding
        $pfLoanOutstanding = $pfloanAmount - $pfLoanReceivePri;
        $pfLoanOpOutstanding = $pfloanOpAmount - $pfLoanOpReceivePri;
        // $pfLoanOutstanding = round($pfLoanOutstanding);
        // dd($pfloanAmount + $pfloanInterestAmount);

        $balance = array(
            'loanId' => $loanId,
            'opLoanId' => $opLoanId,
            'principal' => $pfloanAmount,
            'opPrincipal' => $pfloanOpAmount,
            'collectedPrincipal' => $pfLoanReceivePri,
            'opCollectedPrincipal' => $pfLoanOpReceivePri,
            'collectedInterest' => $pfLoanReceiveInt,
            'opCollectedInterest' => $pfLoanOpReceiveInt,
            'outstanding' => $pfLoanOutstanding,
            'opOutstanding' => $pfLoanOpOutstanding,
        );
        // dd($balance);
        return $balance;
    }

    public function getReligionData()
    {
        return [
            'Islam' => 'Islam',
            'Hindu' => 'Hindu',
            'Buddha' => 'Buddha',
            'Christian' => 'Christian',
        ];
    }

    public function getCurrentFiscalYear($date = '', $companyId = 1)
    {
        if ($date == '') {
            $date = date("Y-m-d");
        }
        $findGnr = FiscalYear::where('companyId', $companyId)
            ->whereRaW('(? BETWEEN `fyStartDate` and `fyEndDate`)', [$date])->first();
        return ['id' => $findGnr->id, 'name' => $findGnr->name];
    }

    public function currencyFormat($amount)
    {
        return number_format($amount, 2);
    }

    public function currencyPlaceholder($amount, $placeholder = 0)
    {
        $result = 0;
        if ($amount < 0) {
            $result = '(' . str_replace('-', '', $this->currencyFormat($amount)) . ')';
        } else if ($amount == 0) {
            if ($placeholder != '') {
                $result = $this->currencyFormat($amount);
            } else {
                $result = $placeholder;
            }

        } else {
            $result = $this->currencyFormat($amount);
        }
        return $result;
        //return ($amount!=0)?$this->currencyFormat($amount):$placeholder;
    }

    public function getSalaryStructureInfo($userId)
    {
        $user = User::find($userId);
        $grade = (isset($user->employee->organization->grade)) ? $user->employee->organization->grade : '';
        $level = (isset($user->employee->organization->level_id_fk)) ? $user->employee->organization->level_id_fk : '';
        $position = (isset($user->employee->organization->position_id_fk)) ? $user->employee->organization->position_id_fk : '';
        return SalaryStructure::where('grade_id_fk', $grade)->where('level_id_fk', $level)
            ->where('position_id_fk', 'like', '%' . $position . '%')->first();
    }

    /* Employee information base directory */
    public function getDocumentManagerDirBasePath()
    {
        if (!file_exists($this->documentManagerDirBase)) {
            mkdir($this->documentManagerDirBase, 0777, true);
        }
        return $this->documentManagerDirBase;
    }

    /* Employee information url directory */
    public function getDocumentManagerDirBaseUrl()
    {
        return $this->documentManagerDirUrl;
    }

    /* Employee information base directory */
    public function getEmpInfoDirBasePath()
    {
        if (!file_exists($this->empInfoDirBase)) {
            mkdir($this->empInfoDirBase, 0777, true);
        }
        return $this->empInfoDirBase;
    }

    /* Employee information url directory */
    public function getEmpInfoDirBaseUrl()
    {
        return $this->empInfoDirUrl;
    }

    /* Generate File name */
    public function genFileName($file)
    {
        //return rand(5,50).'.'.$file->getClientOriginalExtension();
        return time() . rand(1000, 9000) . csrf_token() . '.' . $file->getClientOriginalExtension();
    }

    /* Status showing function */
    public function getStatus($status)
    {
        if ($this->statusType == 'button') {
            if ($status == '1') {
                return "<span class='btn btn-xs btn-success'>Enabled</span>";
            } else if ($status == '0') {
                return "<span class='btn btn-xs btn-danger'>Disabled</span>";
            } else {
                return $status;
            }
        }
    }

    public function getStatusOptions()
    {
        return [
            '' => 'Select any',
            '1' => 'Enable',
            '0' => 'Disable',
        ];
    }

    /* Employee Requisition function */
    public function getEmpRequisitionStatus($status)
    {
        if ($this->statusType == 'button') {
            if ($status == '3') {
                return "<span class='btn btn-xs btn-success'>Job Circular Approved</span>";
            } else if ($status == '2') {
                return "<span class='btn btn-xs btn-info'>Job Circular Pending</span>";
            } else if ($status == '1') {
                return "<span class='btn btn-xs btn-warning'>Approved</span>";
            } else if ($status == '0') {
                return "<span class='btn btn-xs btn-danger'>Pending</span>";
            } else {
                return $status;
            }
        }
    }

    public function getEmpRequisitionStatusOptions()
    {
        return [
            '' => 'Select any',
            '1' => 'Approved',
            '0' => 'Pending',
            '2' => 'Job Circular Pending',
            '3' => 'Job Circular Approved',
        ];
    }

    public function genRequisitionNumber($prefix = null)
    {
        if ($prefix == null) {
            $prefix = date('ym');
        }

        $model = new \App\hr\EmployeeRequisition;
        $model = $model->orderby('id', 'desc')->first();
        if ($model) {
            $modelId = $model->id;
        } else {
            $modelId = 0;
        }

        return $prefix . $modelId + 1;
    }
    /* Employee Requisition function */

    /* Job Circular function */
    public function getJobCircularStatus($status)
    {
        if ($this->statusType == 'button') {
            if ($status == '1') {
                return "<span class='btn btn-xs btn-success'>Approved</span>";
            } else if ($status == '0') {
                return "<span class='btn btn-xs btn-danger'>Pending</span>";
            } else {
                return $status;
            }
        }
    }

    public function genCircularNumber($prefix = null)
    {
        if ($prefix == null) {
            $prefix = date('ym');
        }

        $model = new \App\hr\JobCircular;
        $model = $model->orderby('id', 'desc')->first();
        if ($model) {
            $modelId = $model->id;
        } else {
            $modelId = 0;
        }

        return $prefix . $modelId + 1;
    }

    /* Job Circular function */

    public function getControllerName()
    {
        $route = explode('@', Route::getCurrentRoute()->getActionName());
        $route = explode('\\', $route[0]);
        return trim(end($route));
    }

    public function getActionName()
    {
        $route = explode('@', Route::getCurrentRoute()->getActionName());
        return trim($route[1]);
    }

    public function showDepartmentName($deptIds)
    {
        $ids = explode(',', $deptIds);
        $data = '';
        foreach ($ids as $id) {
            $dept = \App\hr\Department::find($id);
            if ($dept) {
                $data .= $dept->name . ', ';
            }

        }
        return rtrim($data, ', ');
    }

    public function dueSalaryMonth($datas, $lastSalaryDate, $save = false)
    {
        if ($datas->organization->terminate_resignation_date == '' or $datas->organization->terminate_resignation_date == '0000-00-00') {
            return;
        }

        $lastSalaryDate = date('Y-m', strtotime($lastSalaryDate));
        $lastWorkingDate = date('Y-m', strtotime($datas->organization->terminate_resignation_date . ' -1 Day'));
        $data = [];
        if ($lastSalaryDate != $lastWorkingDate) {
            $user = $datas->user;
            $target_month = date('m-Y', strtotime($lastWorkingDate . '-01'));
            $info = $this->makeVirtualSalary($user, $target_month, $save);
            $data['totalday'] = $info['present_days'];
            $data['totaldue'] = $info['net_payable'];
            // dd($info);
        }
        return $data;
    }

    public function makeVirtualSalary($emp, $target_month, $saveData = false)
    {
        $req['target_month'] = $target_month;
        $perRow = [];
        $arearRow = [];

        $this->adjustEdps($emp->id, '01-' . $req['target_month']);
        $this->adjustSecurityAmount($emp->emp_id_fk, '01-' . $req['target_month']);
        $this->adjustPfLoanAmount($emp->id, date("Y-m-t", strtotime('01-' . $req['target_month'])), date("Y-m-d", strtotime('01-' . $req['target_month'])));
        $this->adjustVehicleLoanAmount($emp->id, date("Y-m-t", strtotime('01-' . $req['target_month'])), date("Y-m-d", strtotime('01-' . $req['target_month'])));
        $this->adjustAdvancedSalaryLoanAmount($emp->id, date("Y-m-t", strtotime('01-' . $req['target_month'])), date("Y-m-d", strtotime('01-' . $req['target_month'])));
        $this->adjustPf($emp->id, '01-' . $req['target_month']);
        $this->adjustInsurance($emp->id, '01-' . $req['target_month']);
        $this->adjustOsf($emp->id, '01-' . $req['target_month']);
        $this->adjustWf($emp->id, '01-' . $req['target_month']);
        $this->adjustIncomeTax($emp->id, '01-' . $req['target_month'], date("Y-m-d", strtotime('01-' . $req['target_month'])));

        $perRow['arrear'] = 0;
        /* Arear checking */
        $isArear = PromotionIncrement::checkArear($emp, $target_month);
        /* promotion and increment update */
        if (count($isArear) > 0) {
            if ($isArear->type == 'Promotion') {
                PromotionIncrement::promotionEffect($isArear);
            } else {
                PromotionIncrement::incrementEffect($isArear);
            }

        }

        $perRow['user_id'] = $arearRow['user_id'] = $emp->id;

        $perRow['payment_status'] = $arearRow['payment_status'] = 'Paid';

        // get employee salary structure
        if (@count($emp->employee->organization) > 0) {
            $salaryStructure = SalaryStructure::getEmployeeSalaryStructure($emp->employee->organization);
        } else {
            $salaryStructure = array();
        }

        // get yearly salary structure
        if (@count($salaryStructure) > 0) {
            $yearlySalaryStructure = SalaryStructure::getYearlySalaryStructure(intval($emp->employee->organization->salary_increment_year) - 1, $salaryStructure->salaryYearlyCal);
        } else {
            $yearlySalaryStructure = array();
        }

        // set employee name
        $perRow['name'] = $arearRow['name'] = (isset($emp->employee->emp_name_english)) ? $emp->employee->emp_name_english : 'N/A';

        // set employee id number
        $perRow['emp_id'] = $arearRow['emp_id'] = (isset($emp->employee->emp_id)) ? $emp->employee->emp_id : 'N/A';

        $perRow['basic_salary'] = round(floatval(SalaryStructure::getPaticularItemFromYearlySalaryStructure('Total Basic', $yearlySalaryStructure)));

        /* Arear calculation */
        if (count($isArear) > 0) {
            $arearEffectMonth = $month = strtotime($isArear->effect_month);
            $salaryMonth = strtotime('01-' . $target_month);
            while ($month < $salaryMonth) {
                $targetMonth = date('Y-m-d', $month);
                $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                $perRow['arrear'] += floatval($perRow['basic_salary']) - floatval($empSalaryFromSheet->basic_salary);

                $arearRow['basic_salary'] = [$targetMonth => floatval($perRow['basic_salary']) - floatval($empSalaryFromSheet->basic_salary)];

                $arearRow['working_days'] = [$targetMonth => $empSalaryFromSheet->working_days];
                $arearRow['present_days'] = [$targetMonth => $empSalaryFromSheet->present_days];

                //print_r($empSalaryFromSheet);
                $month = strtotime('+ 1 Month', $month);
            }
            //print_r($arearRow);
            //exit;
        }
        /* Arear calculation */

        //set working days
        $perRow['working_days'] = 30;
        // dd($req['target_month']);
        $perRow['present_days'] = $this->getPresentDays($emp, $req['target_month'], $req);

        /* A type allowance */
        $sectionA = 0;
        if (@count($salaryStructure->salaryAllow) > 0) {
            foreach ($salaryStructure->salaryAllow as $salaryAllow) {
                if ($salaryAllow->benefit_section == 'A') {
                    $baamount = floatval(SalaryStructure::getPaticularItemFromYearlySalaryStructure('', $yearlySalaryStructure, $salaryAllow->benefit_type_fk));

                    $perRow['benefit-a-' . $salaryAllow->benefit_type_fk] = $baamount;
                    $sectionA += $perRow['benefit-a-' . $salaryAllow->benefit_type_fk];

                    /* Arear calculation */
                    if (count($isArear) > 0) {
                        $arearEffectMonth = $month = strtotime($isArear->effect_month);
                        $salaryMonth = strtotime('01-' . $req['target_month']);
                        while ($month < $salaryMonth) {
                            $targetMonth = date('Y-m-d', $month);
                            $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                            $v = 'benefit-a-' . $salaryAllow->benefit_type_fk;

                            $perRow['arrear'] += floatval($perRow[$v]) - floatval(@$empSalaryFromSheet->$v);

                            $arearRow[$v] = [$targetMonth => floatval($perRow[$v]) - floatval(@$empSalaryFromSheet->$v)];

                            //print_r($empSalaryFromSheet);
                            $month = strtotime('+ 1 Month', $month);
                        }

                    }
                    /* Arear calculation */
                }
            }
        }
        /* A type allowance */

        //set gross salary
        $perRow['gross_salary'] = floatval($sectionA) + floatval($perRow['basic_salary']);

        //set gross payable
        if ($perRow['present_days'] > 0) {
            $perRow['gross_payable'] = round((floatval($perRow['gross_salary']) / floatval($perRow['working_days']) * floatval($perRow['present_days'])));
        } else {
            $perRow['gross_payable'] = 0;
        }

        //set arrear
        //$perRow['arrear']=0;

        //set acting benefit
        $perRow['acting_benefit'] = ActingBenefit::getActingBenefit($emp, @$emp->employee->organization);

        $actingBenefitData = ActingBenefit::getActingPosition($emp);

        if (count($actingBenefitData) > 0) {
            $perRow['position_type'] = $arearRow['position_type'] = @$actingBenefitData['positionType'];

            // set employee designation
            $perRow['designation'] = $arearRow['designation'] = @$actingBenefitData['positionName'];
        } else {
            $perRow['position_type'] = $arearRow['position_type'] = @$emp->employee->organization->position->type;

            // set employee designation
            $perRow['designation'] = $arearRow['designation'] = (isset($emp->employee->organization->position->name)) ? $emp->employee->organization->position->name : 'N/A';
        }

        /* B type allowance */
        $sectionB = 0;
        if (@count($salaryStructure->salaryAllow) > 0) {
            foreach ($salaryStructure->salaryAllow as $salaryAllow) {
                if ($salaryAllow->benefit_section == 'B') {
                    $isEligible = BenefitType::checkEligibleForBenefit($salaryAllow->benefit_type_fk, $emp);
                    if ($isEligible) {

                        if ($perRow['present_days'] > 0) {
                            $bbamount = BenefitType::getPariculatAmountForSalaryGenerate($salaryAllow->benefit_type_fk, @$emp->employee->organization->benefit_type_fk, @$emp->employee->organization->benefit_type_amount, $perRow['present_days']);
                        } else {
                            $bbamount = 0;
                        }

                        $perRow['benefit-b-' . $salaryAllow->benefit_type_fk] = $bbamount;
                        $sectionB += $perRow['benefit-b-' . $salaryAllow->benefit_type_fk];

                        /* Arear calculation */
                        if (count($isArear) > 0) {
                            $arearEffectMonth = $month = strtotime($isArear->effect_month);
                            $salaryMonth = strtotime('01-' . $req['target_month']);
                            while ($month < $salaryMonth) {
                                $targetMonth = date('Y-m-d', $month);
                                $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                                $v = 'benefit-b-' . $salaryAllow->benefit_type_fk;

                                $va = BenefitType::getPariculatAmountForSalaryGenerate($salaryAllow->benefit_type_fk, @$emp->employee->organization->benefit_type_fk, @$emp->employee->organization->benefit_type_amount, $arearRow['present_days'][$targetMonth]);

                                $perRow['arrear'] += floatval($va) - floatval(@$empSalaryFromSheet->$v);

                                $arearRow[$v] = [$targetMonth => floatval($va) - floatval(@$empSalaryFromSheet->$v)];

                                //print_r($empSalaryFromSheet);
                                $month = strtotime('+ 1 Month', $month);
                            }

                        }
                        /* Arear calculation */
                    }
                }
            }
        }
        /* B type allowance */

        //set provident fund org
        $perRow['pf_org'] = ProvidentFundSettings::getPFOrg($req['target_month'], $emp, $perRow['basic_salary']);
        /* Arear calculation */
        if (count($isArear) > 0) {
            $arearEffectMonth = $month = strtotime($isArear->effect_month);
            $salaryMonth = strtotime('01-' . $req['target_month']);
            while ($month < $salaryMonth) {
                $targetMonth = date('Y-m-d', $month);
                $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                $perRow['arrear'] += floatval($perRow['pf_org']) - floatval(@$empSalaryFromSheet->pf_org);

                $arearRow['pf_org'] = [$targetMonth => floatval($perRow['pf_org']) - floatval(@$empSalaryFromSheet->pf_org)];

                //print_r($empSalaryFromSheet);
                $month = strtotime('+ 1 Month', $month);
            }
        }
        /* Arear calculation */

        /* insurance org */
        if (@$data['otherSettings']->insurance_enable == '1') {
            //set provident fund org
            $perRow['insurance_org'] = InsuranceSettings::getINOrg($req['target_month'], $emp, $perRow['basic_salary']);

            if (count($isArear) > 0) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] += floatval($perRow['insurance_org']) - floatval(@$empSalaryFromSheet->insurance_org);

                    $arearRow['insurance_org'] = [$targetMonth => floatval($perRow['insurance_org']) - floatval(@$empSalaryFromSheet->insurance_org)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }
            }
        } else {
            $perRow['insurance_org'] = 0;
        }
        /* insurance org */

        /* osf org */
        if (@$data['otherSettings']->osf_enable == '1') {
            //set provident fund org
            $perRow['osf_org'] = OsfSettings::getOSFOrg($req['target_month'], $emp, $perRow['basic_salary']);

            if (count($isArear) > 0) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] += floatval($perRow['osf_org']) - floatval(@$empSalaryFromSheet->osf_org);

                    $arearRow['osf_org'] = [$targetMonth => floatval($perRow['osf_org']) - floatval(@$empSalaryFromSheet->osf_org)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }
            }
        } else {
            $perRow['osf_org'] = 0;
        }
        /* osf org */

        /* WF org */
        if (@$data['otherSettings']->wf_enable == '1') {
            //set wf org
            if (@$emp->employee->organization->wf_active == '1') {
                $perRow['wf_org'] = WelfareFundSettings::getWFOrg($req['target_month'], $emp, $perRow['basic_salary']);
            } else {
                $perRow['wf_org'] = 0;
            }
            /* Arear calculation */
            if (count($isArear) > 0) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] += floatval($perRow['wf_org']) - floatval(@$empSalaryFromSheet->wf_org);

                    $arearRow['wf_org'] = [$targetMonth => floatval($perRow['wf_org']) - floatval(@$empSalaryFromSheet->wf_org)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }
            }
            /* Arear calculation */
        } else {
            $perRow['wf_org'] = 0;
        }
        /* WF Org */

        //set total salary
        $perRow['total_salary'] = round(floatval($perRow['gross_payable']) + floatval($perRow['arrear']) + floatval($perRow['acting_benefit']));

        /* Deduction Start */

        //pf calculation
        $perRow['pf_self'] = ProvidentFundSettings::getPFSelf($req['target_month'], $emp, $perRow['basic_salary']);
        /* Arear calculation */
        if (count($isArear) > 0) {
            $arearEffectMonth = $month = strtotime($isArear->effect_month);
            $salaryMonth = strtotime('01-' . $req['target_month']);
            while ($month < $salaryMonth) {
                $targetMonth = date('Y-m-d', $month);
                $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                $perRow['arrear'] -= floatval($perRow['pf_self']) - floatval(@$empSalaryFromSheet->pf_self);

                $perRow['arrear'] -= floatval($perRow['pf_org']) - floatval(@$empSalaryFromSheet->pf_org);

                $arearRow['pf_self'] = [$targetMonth => floatval($perRow['pf_self']) - floatval(@$empSalaryFromSheet->pf_self)];

                $arearRow['pf_org_deduct'] = [$targetMonth => floatval($perRow['pf_org']) - floatval(@$empSalaryFromSheet->pf_org)];

                //print_r($empSalaryFromSheet);
                $month = strtotime('+ 1 Month', $month);
            }

        }
        /* Arear calculation */

        //pf loan
        $perRow['pf_loan'] = $this->getPfLoanAmount($emp, $req['target_month']);

        /* Insurance Self */
        if (@$data['otherSettings']->insurance_enable == '1') {
            //insurance self calculation
            $perRow['insurance_self'] = InsuranceSettings::getINSelf($req['target_month'], $emp, $perRow['basic_salary']);
            /* Arear calculation */
            if (count($isArear) > 0) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] -= floatval($perRow['insurance_self']) - floatval(@$empSalaryFromSheet->insurance_self);

                    $perRow['arrear'] -= floatval($perRow['insurance_org']) - floatval(@$empSalaryFromSheet->insurance_org);

                    $arearRow['insurance_self'] = [$targetMonth => floatval($perRow['insurance_self']) - floatval(@$empSalaryFromSheet->insurance_self)];

                    $arearRow['insurance_org_deduct'] = [$targetMonth => floatval($perRow['insurance_org']) - floatval(@$empSalaryFromSheet->insurance_org)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }

            }
            /* Arear calculation */
        } else {
            $perRow['insurance_self'] = 0;
        }
        /* Insurance Self */

        /* OSF Self */
        if (@$data['otherSettings']->osf_enable == '1') {
            //insurance self calculation
            $perRow['osf_self'] = InsuranceSettings::getINSelf($req['target_month'], $emp, $perRow['basic_salary']);
            /* Arear calculation */
            if (count($isArear) > 0) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] -= floatval($perRow['osf_self']) - floatval(@$empSalaryFromSheet->insurance_self);

                    $perRow['arrear'] -= floatval($perRow['osf_org']) - floatval(@$empSalaryFromSheet->osf_org);

                    $arearRow['osf_self'] = [$targetMonth => floatval($perRow['osf_self']) - floatval(@$empSalaryFromSheet->osf_self)];

                    $arearRow['osf_org_deduct'] = [$targetMonth => floatval($perRow['osf_org']) - floatval(@$empSalaryFromSheet->osf_org)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }

            }
            /* Arear calculation */
        } else {
            $perRow['osf_self'] = 0;
        }
        /* OSF Self */

        /* WF Self */
        if (@$data['otherSettings']->wf_enable == '1') {
            //wf calculation
            if (@$emp->employee->organization->wf_active == 1) {
                $perRow['wf_self'] = WelfareFundSettings::getWFSelf($req['target_month'], $emp, $perRow['basic_salary'], 'WF');
            } else {
                $perRow['wf_self'] = 0;
            }
            /* Arear calculation */
            if (count($isArear) > 0) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] -= floatval($perRow['wf_self']) - floatval(@$empSalaryFromSheet->wf_self);

                    $arearRow['wf_self'] = [$targetMonth => floatval($perRow['wf_self']) - floatval(@$empSalaryFromSheet->wf_self)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }

            }
            /* Arear calculation */
        } else {
            $perRow['wf_self'] = 0;
        }
        /* WF Self */

        /* WF contri */
        if (@$data['otherSettings']->wf_enable == '1') {
            //wf contri. calculation
            if (@$emp->employee->organization->wf_active == 1) {
                $perRow['wf_contri_self'] = WelfareFundSettings::getWFSelf($req['target_month'], $emp, $perRow['basic_salary'], 'WF Contri.');
            } else {
                $perRow['wf_contri_self'] = 0;
            }

            /* Arear calculation */
            if (count($isArear) > 0) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] -= floatval($perRow['wf_contri_self']) - floatval(@$empSalaryFromSheet->wf_contri_self);

                    $arearRow['wf_contri_self'] = [$targetMonth => floatval($perRow['wf_contri_self']) - floatval(@$empSalaryFromSheet->wf_contri_self)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }

            }
            /* Arear calculation */
        } else {
            $perRow['wf_contri_self'] = 0;
        }
        /* WF contri */

        $perRow['edps'] = $this->getEdpsAmount($emp, $req['target_month']);

        $perRow['vehicle_loan'] = $this->getVehicleLoanAmount($emp, $req['target_month']);

        $perRow['security_money'] = $this->getSecurityAmount($emp, $req['target_month']);

        $perRow['advanced_salary_loan'] = $this->getAdvancedSalaryLoanAmount($emp, $req['target_month']);

        $perRow['income_tax'] = $this->getIncomeTax($emp, $req['target_month']);

        /* Deduction End */
        //total salary + benefits
        $perRow['total_salary_benefits'] = round(floatval($perRow['total_salary']) + floatval($sectionB) + floatval($perRow['pf_org']) + floatval($perRow['wf_org']) + floatval($perRow['insurance_org']) + floatval($perRow['osf_org']));

        //echo "<pre>";print_r($arearRow);print_r($perRow);echo "</pre>";exit;
        $perRow['total_deductions'] = floatval($perRow['pf_self']) + floatval($perRow['pf_loan']) + floatval($perRow['pf_org']) + floatval($perRow['wf_org']) + floatval($perRow['wf_self']) + floatval($perRow['wf_contri_self']) + floatval($perRow['edps']) + floatval($perRow['vehicle_loan']) + floatval($perRow['security_money']) + floatval($perRow['advanced_salary_loan']) + floatval($perRow['income_tax']) + floatval($perRow['insurance_org']) + floatval($perRow['osf_org']) + floatval($perRow['insurance_self']) + floatval($perRow['osf_self']);

        $perRow['net_payable'] = floatval($perRow['total_salary_benefits']) - floatval($perRow['total_deductions']);

        if ($saveData) {
            //save pf
            $this->savePF($req['target_month'], $perRow['user_id'], $perRow['pf_org'], $perRow['pf_self'], 'Regular', $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            //save pf loan
            $this->savePfLoanAmount($emp, $req['target_month'], $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            //save insurance
            if (@$data['otherSettings']->insurance_enable == '1') {
                $this->saveInsurance($req['target_month'], $perRow['user_id'], $perRow['insurance_org'], $perRow['insurance_self'], 'Regular', $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);
            }
            //save osf
            if (@$data['otherSettings']->osf_enable == '1') {
                $this->saveOsf($req['target_month'], $perRow['user_id'], $perRow['osf_org'], $perRow['osf_self'], 'Regular', $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);
            }
            //save wf
            if (@$emp->employee->organization->wf_active == 1 && @$data['otherSettings']->wf_enable == '1') {
                $this->saveWF($req['target_month'], $perRow['user_id'], $perRow['wf_org'], $perRow['wf_self'], $perRow['wf_contri_self'], 'Regular', $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);
            }

            //save edps
            $this->saveEDPS($req['target_month'], $perRow['user_id'], $perRow['edps'], $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            //save vechicle loan
            $this->saveVehicleLoanAmount($emp, $req['target_month'], $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            //save secutiry amount
            $this->saveSecurityMoney($req['target_month'], $emp->emp_id_fk, $perRow['security_money'], $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            //save advanced salary loan
            $this->saveAdvancedSalaryLoanAmount($emp, $req['target_month'], $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            //save income tax
            $this->saveIncomeTax($emp, $req['target_month'], $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            $employeeModel = EmployeeGeneralInfo::find($emp->employee->id);
            $employeeModel->final_salary_content = json_encode($perRow);
            $employeeModel->final_arear_content = json_encode($arearRow);
            $employeeModel->save();

        }

        $data['row'][] = $perRow;
        $data['arearRow'][] = $arearRow;
        return $perRow;
    }

    public function getPfLoanAmount($emp, $salaryMonth)
    {

        $forMonth = date("Y-m-t", strtotime('01-' . $salaryMonth));
        //$tdate = date("Y-m",strtotime($forMonth));
        $tdate = date("Y-m-d", strtotime('01-' . $salaryMonth));

        $this->adjustPfLoanAmount($emp->id, $forMonth, $tdate);

        /* Check pf loan amount stop or not */
        if (StopSalaryBenefit::checkStopSalaryBenefit($emp->id, '01-' . $salaryMonth, 'PF Loan')) {
            return 0;
        }

        $vl = ProvidentFundLoan::select('t1.*', 't2.id as review_id', 't2.inst_amount', 't2.completed_no_of_installment')->from('hr_pf_loan as t1')->where('t1.users_id_fk', $emp->id)->where('t1.status', 'Approved')->whereRaw('t2.completed_no_of_installment!=t2.no_of_inst')->where('t2.inst_start_month', '<=', $forMonth)->join('hr_pf_loan_review as t2', 't2.pf_loan_id_fk', '=', 't1.id')->get();
        $loanAmount = 0;
        if (count($vl) > 0) {
            foreach ($vl as $ld):
                ProvidentFundLoanSchedule::where('pf_loan_review_fk', $ld->review_id)->where('transaction_date', $tdate)->update(['payment_status' => 'Unpaid', 'transaction_date' => '0000-00-00']);

                $pfReExists = PfLoanReceive::where('pf_loan_fk', $ld->id)->where('type', 'Regular')->where('users_id_fk', $emp->id)->where('transaction_date', $tdate);
                if (count($pfReExists->get()) > 0) {
                    $pfReExists->delete();
                }
                $schedule = ProvidentFundLoanSchedule::select(\DB::raw('sum(total_payment) as total_payment'))->where('payment_date', '<=', $forMonth)->where('payment_status', '=', 'Unpaid')->where('pf_loan_review_fk', '=', $ld->loanReview->id)->first();
                $loanAmount += $schedule->total_payment;
            endforeach;
        }

        //Opening balance
        $op = OpeningBalanceLoanPf::where('users_id_fk', $emp->id)->where('installment_start_date', '<=', $forMonth)->get();
        if (count($op) > 0) {
            foreach ($op as $oppf) {
                OpeningBalanceLoanPfSchedule::where('ob_loan_pf_fk', $oppf->id)->where('transaction_date', $tdate)->update(['payment_status' => 'Unpaid', 'transaction_date' => '0000-00-00']);

                $pfReExists = PfLoanReceive::where('pf_loan_fk', $oppf->id)->where('type', 'Opening Balance')->where('users_id_fk', $emp->id)->where('transaction_date', $tdate);
                if (count($pfReExists->get()) > 0) {
                    $pfReExists->delete();
                }
                $schedule = OpeningBalanceLoanPfSchedule::select(\DB::raw('sum(total_payment) as total_payment'))->where('payment_date', '<=', $forMonth)->where('payment_status', '=', 'Unpaid')->where('ob_loan_pf_fk', '=', $oppf->id)->first();
                $loanAmount += $schedule->total_payment;
            }
        }
        return round($loanAmount);
    }

    public function savePfLoanAmount($emp, $salaryMonth, $paymentStatus = 'Paid', $created_at = null)
    {
        /* Check pf loan amount stop or not */
        if (StopSalaryBenefit::checkStopSalaryBenefit($emp->id, '01-' . $salaryMonth, 'PF Loan')) {
            return 0;
        }
        $lastDateMonth = date("Y-m-t", strtotime('01-' . $salaryMonth));
        $forMonth = date("Y-m-d", strtotime('01-' . $salaryMonth));
        $vl = ProvidentFundLoan::select('t1.*', 't2.inst_amount', 't2.completed_no_of_installment')->from('hr_pf_loan as t1')->where('t1.users_id_fk', $emp->id)->where('t1.status', 'Approved')->whereRaw('t2.completed_no_of_installment!=t2.no_of_inst')->where('t2.inst_start_month', '<=', $lastDateMonth)->join('hr_pf_loan_review as t2', 't2.pf_loan_id_fk', '=', 't1.id')->get();

        if (count($vl) > 0) {
            foreach ($vl as $ld):

                $schedule = ProvidentFundLoanSchedule::where('payment_date', '<=', $lastDateMonth)->where('payment_status', '=', 'Unpaid')->where('pf_loan_review_fk', '=', $ld->loanReview->id);

                if (count($schedule->get()) > 0) {
                    foreach ($schedule->get() as $sch):
                        $model = new PfLoanReceive;
                        $model->pf_loan_fk = $ld->id;
                        $model->users_id_fk = $emp->id;
                        $model->transaction_date = $forMonth;
                        $model->amount = floatval($sch->total_payment);
                        $model->principal_amount = floatval($sch->principal);
                        $model->interest_amount = floatval($sch->interest);
                        $model->fiscal_id_fk = @$this->getCurrentFiscalYear()['id'];
                        $model->for_month = date('Y-m-d', strtotime($sch->payment_date));
                        $model->received_branch_id = $emp->branchId;
                        if ($created_at != null) {
                            $model->created_at = date("Y-m-d H:i:s", strtotime($created_at));
                        } else {
                            $model->created_at = date("Y-m-d H:i:s");
                        }

                        $model->created_by = Auth::user()->id;
                        $model->updated_at = date("Y-m-d H:i:s");
                        $model->updated_by = Auth::user()->id;
                        $model->type = 'Regular';
                        $model->payment_status = $paymentStatus;
                        if (floatval($model->amount) > 0) {
                            $model->save();

                            //schedule paid
                            $schPaid = ProvidentFundLoanSchedule::find($sch->id);
                            $schPaid->payment_status = 'Paid';
                            $schPaid->transaction_date = $model->transaction_date;
                            $schPaid->save();
                        }

                        //count no of complete installment
                        $noOfCompletedInstallment = PfLoanReceive::where('pf_loan_fk', $ld->id)->where('type', 'Regular');

                        //update no of completed installment
                        $nvl = ProvidentFundLoanReview::where('pf_loan_id_fk', $ld->id)->first();
                        $nvl->completed_no_of_installment = count($noOfCompletedInstallment);
                        $nvl->save();
                    endforeach;
                }
            endforeach;
        }

        //Opening balance
        $op = OpeningBalanceLoanPf::where('users_id_fk', $emp->id)->where('installment_start_date', '<=', $lastDateMonth)->get();

        if (count($op) > 0) {

            foreach ($op as $oppf) {
                $schedule = OpeningBalanceLoanPfSchedule::where('payment_date', '<=', $lastDateMonth)->where('payment_status', '=', 'Unpaid')->where('ob_loan_pf_fk', '=', $oppf->id);
                if (count($schedule->get()) > 0) {
                    foreach ($schedule->get() as $sch):
                        $model = new PfLoanReceive;
                        $model->pf_loan_fk = $oppf->id;
                        $model->users_id_fk = $emp->id;
                        $model->transaction_date = $forMonth;
                        $model->amount = floatval($sch->total_payment);
                        $model->principal_amount = floatval($sch->principal);
                        $model->interest_amount = floatval($sch->interest);
                        $model->fiscal_id_fk = @$this->getCurrentFiscalYear()['id'];
                        $model->for_month = date('Y-m-d', strtotime($sch->payment_date));
                        $model->received_branch_id = $emp->branchId;
                        if ($created_at != null) {
                            $model->created_at = date("Y-m-d H:i:s", strtotime($created_at));
                        } else {
                            $model->created_at = date("Y-m-d H:i:s");
                        }
                        $model->created_by = Auth::user()->id;
                        $model->updated_at = date("Y-m-d H:i:s");
                        $model->updated_by = Auth::user()->id;
                        $model->type = 'Opening Balance';
                        $model->payment_status = $paymentStatus;
                        if (floatval($model->amount) > 0) {
                            $model->save();

                            //schedule paid
                            $schPaid = OpeningBalanceLoanPfSchedule::find($sch->id);
                            $schPaid->payment_status = 'Paid';
                            $schPaid->transaction_date = date('Y-m-d', strtotime($model->transaction_date));
                            $schPaid->save();
                        }
                    endforeach;
                }
            }
        }
    }

    public function saveEDPS($salaryMonth, $userId, $amount, $paymentStatus = 'Paid', $created_at = null)
    {
        if (floatval($amount) > 0) {
            $forMonth = date("Y-m-d", strtotime('01-' . $salaryMonth));

            $activeEdps = Edps::where('status', 'Active')->where('users_id_fk', $userId)->whereRaW('? BETWEEN `start_month` and `end_month`', [$forMonth])->get();
            if (count($activeEdps) > 0) {
                foreach ($activeEdps as $aedps):
                    $model = new EdpsReceive;
                    $chkExist = $model->where('edps_id_fk', $aedps->id)->where('users_id_fk', $userId)->where('for_month', $forMonth)->get();
                    if (count($chkExist) > 0) {
                        $model->where('edps_id_fk', $aedps->id)->where('users_id_fk', $userId)->where('for_month', $forMonth)->delete();
                    }
                    $model->edps_id_fk = $aedps->id;
                    $model->users_id_fk = $userId;
                    $model->transaction_date = date('Y-m-d');
                    $model->amount = floatval($aedps->amount);
                    $model->fiscal_id_fk = @$this->getCurrentFiscalYear()['id'];
                    $model->for_month = $forMonth;
                    $model->received_branch_id = intval(User::find($userId)->branchId);
                    if ($created_at != null) {
                        $model->created_at = date("Y-m-d H:i:s", strtotime($created_at));
                    } else {
                        $model->created_at = date("Y-m-d H:i:s");
                    }

                    $model->created_by = Auth::user()->id;
                    $model->updated_at = date("Y-m-d H:i:s");
                    $model->updated_by = Auth::user()->id;
                    $model->transaction_month = $forMonth;
                    $model->payment_status = $paymentStatus;
                    if (floatval($model->amount) > 0) {
                        $model->save();
                        $this->checkSavePreviousEdps($aedps, $forMonth, $paymentStatus, $created_at);
                    }
                endforeach;
            }
        }
    }

    public function checkSavePreviousEdps($aedps, $for_Month, $paymentStatus = 'Paid', $created_at = null)
    {
        $start_month = $month = strtotime($aedps->start_month);
        $end_month = strtotime($for_Month);
        $userId = $aedps->users_id_fk;
        if ($start_month <= $end_month && $end_month <= strtotime($aedps->end_month)) {
            while ($month < $end_month) {
                $forMonth = date("Y-m-d", $month);
                $model = new EdpsReceive;
                $chkExist = $model->where('edps_id_fk', $aedps->id)->where('users_id_fk', $userId)->where('for_month', $forMonth)->get();
                if (count($chkExist) == 0) {
                    $model->edps_id_fk = $aedps->id;
                    $model->users_id_fk = $userId;
                    $model->transaction_date = date('Y-m-d');
                    $model->amount = floatval($aedps->amount);
                    $model->fiscal_id_fk = @$this->getCurrentFiscalYear()['id'];
                    $model->for_month = $forMonth;
                    $model->received_branch_id = intval(User::find($userId)->branchId);
                    if ($created_at != null) {
                        $model->created_at = date("Y-m-d H:i:s", strtotime($created_at));
                    } else {
                        $model->created_at = date("Y-m-d H:i:s");
                    }

                    $model->created_by = Auth::user()->id;
                    $model->updated_at = date("Y-m-d H:i:s");
                    $model->updated_by = Auth::user()->id;
                    $model->transaction_month = date("Y-m-d", $end_month);
                    $model->payment_status = $paymentStatus;
                    if (floatval($model->amount) > 0) {
                        $model->save();
                    }
                }
                $month = strtotime("+1 Month", strtotime($forMonth));
            }
        }
    }

    public function saveIncomeTax($emp, $salaryMonth, $paymentStatus = 'Paid', $created_at = null)
    {

        /* Check security deposit stop or not */
        if (StopSalaryBenefit::checkStopSalaryBenefit($emp->id, '01-' . $salaryMonth, 'Income Tax')) {
            return 0;
        }
        $lastDateMonth = date("Y-m-t", strtotime('01-' . $salaryMonth));
        $forMonth = date("Y-m-d", strtotime('01-' . $salaryMonth));

        $vl = IncomeTax::where('users_id_fk', $emp->id)->where('status', 'Approved')->where('acpected_installment_start_month', '<=', $lastDateMonth)->get();
        if (count($vl) > 0) {
            foreach ($vl as $ld):
                $loanAmount = 0;
                /* get total expected money*/
                $start_month = date("Y-m-01", strtotime($ld->acpected_installment_start_month));
                $cur_month = date("Y-m-10", strtotime($forMonth));
                $totalMonth = Carbon::parse($start_month)->diffInMonths(Carbon::parse($cur_month));
                //$expectedMoney = floatval($ld->approved_installment*$totalMonth);
                $expectedMoney = floatval($ld->acpected_amount);
                /* get total expected money*/

                /* get total deposited money */
                $qtd = IncomeTaxReceive::select(\DB::raw('SUM(amount) as amount'))->where('income_tax_id', $ld->id)->where('users_id_fk', $emp->id)->first();
                /* get total deposited money */
                $balance = $expectedMoney - floatval(@$qtd->amount);

                if ($balance < $ld->acpected_installment) {
                    $loanAmount += $balance;
                } else {
                    $loanAmount += $ld->acpected_installment;
                }

                $model = new IncomeTaxReceive;
                $model->income_tax_id = $ld->id;
                $model->users_id_fk = $emp->id;
                $model->transaction_date = $forMonth;
                $model->amount = floatval($loanAmount);
                $model->fiscal_id_fk = @$this->getCurrentFiscalYear()['id'];
                $model->for_month = $forMonth;
                if ($created_at != null) {
                    $model->created_at = date("Y-m-d H:i:s", strtotime($created_at));
                } else {
                    $model->created_at = date("Y-m-d H:i:s");
                }
                $model->created_by = Auth::user()->id;
                $model->updated_at = date("Y-m-d H:i:s");
                $model->updated_by = Auth::user()->id;
                $model->payment_status = $paymentStatus;
                if (floatval($model->amount) > 0) {
                    $model->save();
                }

                $this->checkSavePreviousIncomeTax($ld, $forMonth, $paymentStatus, 'save', $created_at);
            endforeach;
        }
    }

    public function saveVehicleLoanAmount($emp, $salaryMonth, $paymentStatus = 'Paid', $created_at = null)
    {

        /* Check pf loan amount stop or not */
        if (StopSalaryBenefit::checkStopSalaryBenefit($emp->id, '01-' . $salaryMonth, 'Vehicle Loan')) {
            return 0;
        }

        $lastDateMonth = date("Y-m-t", strtotime('01-' . $salaryMonth));
        $forMonth = date("Y-m-d", strtotime('01-' . $salaryMonth));

        $vl = VehicleLoan::where('users_id_fk', $emp->id)->where('status', 'Approved')->whereRaw('completed_no_of_installment!=approved_no_of_installment')->where('installment_start_month', '<=', $lastDateMonth)->get();
        $loanAmount = 0;
        if (count($vl) > 0) {
            foreach ($vl as $ld):
                $schedule = VehicleLoanSchedule::where('payment_date', '<=', $lastDateMonth)->where('payment_status', '=', 'Unpaid')->where('vehicle_loan_fk', '=', $ld->id);
                if (count($schedule->get()) > 0) {
                    foreach ($schedule->get() as $sch):
                        $model = new VehicleLoanReceive;
                        $model->vehicle_loan_fk = $ld->id;
                        $model->users_id_fk = $emp->id;
                        $model->transaction_date = $forMonth;
                        $model->amount = floatval($sch->total_payment);
                        $model->principal_amount = floatval($sch->principal);
                        $model->interest_amount = floatval($sch->interest);
                        $model->fiscal_id_fk = @$this->getCurrentFiscalYear()['id'];
                        $model->for_month = date('Y-m-d', strtotime($sch->payment_date));
                        $model->type = 'Regular';
                        $model->received_branch_id = intval(@$emp->branchId);
                        if ($created_at != null) {
                            $model->created_at = date("Y-m-d H:i:s", strtotime($created_at));
                        } else {
                            $model->created_at = date("Y-m-d H:i:s");
                        }

                        $model->created_by = Auth::user()->id;
                        $model->updated_at = date("Y-m-d H:i:s");
                        $model->updated_by = Auth::user()->id;
                        $model->payment_status = $paymentStatus;
                        if (floatval($model->amount) > 0) {
                            $model->save();

                            //schedule paid
                            $schPaid = VehicleLoanSchedule::find($sch->id);
                            $schPaid->payment_status = 'Paid';
                            $schPaid->transaction_date = $model->transaction_date;
                            $schPaid->save();
                        }

                        //count no of complete installment
                        $noOfCompletedInstallment = VehicleLoanReceive::where('type', 'Regular')->where('vehicle_loan_fk', $ld->id);

                        //update no of completed installment
                        $nvl = VehicleLoan::find($ld->id);
                        $nvl->completed_no_of_installment = count($noOfCompletedInstallment);
                        $nvl->save();
                    endforeach;
                }
            endforeach;
        }

        // Opening balance
        $op = OpeningBalanceLoanVehicle::where('users_id_fk', $emp->id)->where('installment_start_date', '<=', $lastDateMonth)->get();
        if (count($op) > 0) {
            foreach ($op as $opv):
                $schedule = OpeningBalanceLoanVehicleSchedule::where('payment_date', '<=', $lastDateMonth)->where('payment_status', '=', 'Unpaid')->where('ob_loan_vehicle_fk', '=', $opv->id);
                if (count($schedule->get()) > 0) {
                    foreach ($schedule->get() as $sch):
                        $model = new VehicleLoanReceive;
                        $model->vehicle_loan_fk = $opv->id;
                        $model->users_id_fk = $emp->id;
                        $model->transaction_date = $forMonth;
                        $model->amount = floatval($sch->total_payment);
                        $model->principal_amount = floatval($sch->principal);
                        $model->interest_amount = floatval($sch->interest);
                        $model->fiscal_id_fk = @$this->getCurrentFiscalYear()['id'];
                        $model->for_month = date('Y-m-d', strtotime($sch->payment_date));
                        $model->type = 'Opening Balance';
                        $model->received_branch_id = intval(@$emp->branchId);
                        if ($created_at != null) {
                            $model->created_at = date("Y-m-d H:i:s", strtotime($created_at));
                        } else {
                            $model->created_at = date("Y-m-d H:i:s");
                        }
                        $model->created_by = Auth::user()->id;
                        $model->updated_at = date("Y-m-d H:i:s");
                        $model->updated_by = Auth::user()->id;
                        $model->payment_status = $paymentStatus;
                        if (floatval($model->amount) > 0) {
                            $model->save();

                            //schedule paid
                            $schPaid = OpeningBalanceLoanVehicleSchedule::find($sch->id);
                            $schPaid->payment_status = 'Paid';
                            $schPaid->transaction_date = $model->transaction_date;
                            $schPaid->save();
                        }
                    endforeach;
                }
            endforeach;
        }

        return $loanAmount;
    }

    public function saveSecurityMoney($salaryMonth, $empId, $amount, $paymentStatus = 'Paid', $created_at = null)
    {
        $forMonth = date("Y-m-d", strtotime('01-' . $salaryMonth));
        $model = new SecurityMoneyCollection;
        $model->emp_id_fk = $empId;
        $model->type = 'Installment';
        $model->amount = floatval($amount);
        $model->for_month = $forMonth;
        $model->received_branch_id = User::where('emp_id_fk', $empId)->first()->branchId;
        if ($created_at != null) {
            $model->created_at = date("Y-m-d H:i:s", strtotime($created_at));
        } else {
            $model->created_at = date("Y-m-d H:i:s");
        }
        $model->created_by = Auth::user()->id;
        $model->updated_at = date("Y-m-d H:i:s");
        $model->updated_by = Auth::user()->id;
        $model->received_by = Auth::user()->id;
        $model->received_at = date("Y-m-d");
        $model->payment_status = $paymentStatus;
        if (floatval($model->amount) > 0) {
            $model->save();
        }

    }

    public function saveAdvancedSalaryLoanAmount($emp, $salaryMonth, $paymentStatus = 'Paid', $created_at = null)
    {

        /* Check security deposit stop or not */
        if (StopSalaryBenefit::checkStopSalaryBenefit($emp->id, '01-' . $salaryMonth, 'Advanced Salary Loan')) {
            return 0;
        }
        $lastDateMonth = date("Y-m-t", strtotime('01-' . $salaryMonth));
        $forMonth = date("Y-m-d", strtotime('01-' . $salaryMonth));

        $vl = AdvancedSalaryLoan::where('users_id_fk', $emp->id)->where('status', 'Approved')->where('approved_installment_start_month', '<=', $lastDateMonth)->get();
        if (count($vl) > 0) {
            foreach ($vl as $ld):
                $loanAmount = 0;
                /* get total expected money*/
                $start_month = date("Y-m-01", strtotime($ld->approved_installment_start_month));
                $cur_month = date("Y-m-10", strtotime($forMonth));
                $totalMonth = Carbon::parse($start_month)->diffInMonths(Carbon::parse($cur_month));
                //$expectedMoney = floatval($ld->approved_installment*$totalMonth);
                $expectedMoney = floatval($ld->approved_amount);
                /* get total expected money*/

                /* get total deposited money */
                //$qtd = AdvancedSalaryLoanReceive::select(\DB::raw('SUM(amount) as amount'))->where('type','Regular')->orWhere('type','Advanced')->where('advanced_salary_loan_fk',$ld->id)->where('users_id_fk',$emp->id)->first();
                $qtd = AdvancedSalaryLoanReceive::select(\DB::raw('SUM(amount) as amount'))->where('advanced_salary_loan_fk', $ld->id)->where('users_id_fk', $emp->id)->where(function ($q) {
                    $q->orWhere('type', 'Regular');
                    $q->orWhere('type', 'Advanced');
                })->first();
                /* get total deposited money */
                $balance = $expectedMoney - floatval(@$qtd->amount);

                if ($balance < $ld->approved_installment) {
                    $loanAmount += $balance;
                } else {
                    $loanAmount += $ld->approved_installment;
                }

                $model = new AdvancedSalaryLoanReceive;
                $model->advanced_salary_loan_fk = $ld->id;
                $model->users_id_fk = $emp->id;
                $model->transaction_date = $forMonth;
                $model->amount = floatval($loanAmount);
                $model->fiscal_id_fk = @$this->getCurrentFiscalYear()['id'];
                $model->for_month = $forMonth;
                $model->type = 'Regular';
                $model->received_branch_id = $emp->branchId;
                if ($created_at != null) {
                    $model->created_at = date("Y-m-d H:i:s", strtotime($created_at));
                } else {
                    $model->created_at = date("Y-m-d H:i:s");
                }
                $model->created_by = Auth::user()->id;
                $model->updated_at = date("Y-m-d H:i:s");
                $model->updated_by = Auth::user()->id;
                $model->approved_by = Auth::user()->id;
                $model->approved_at = date("Y-m-d H:i:s");
                $model->payment_status = $paymentStatus;
                if (floatval($model->amount) > 0) {
                    $model->save();
                }

                $this->checkSavePreviousAdvancedSalaryLoan($ld, $forMonth, 'Regular', $paymentStatus, 'save', $created_at);
            endforeach;

            foreach ($vl as $ld):
                //count no of complete installment
                $noOfCompletedInstallment = AdvancedSalaryLoanReceive::where('type', 'Regular')->where('advanced_salary_loan_fk', $ld->id)->get();

                //update no of completed installment
                $nvl = AdvancedSalaryLoan::find($ld->id);
                $nvl->completed_no_of_installment = count($noOfCompletedInstallment);
                $nvl->save();
            endforeach;
        }

        //$op = OpeningBalanceLoanAdvancedSalary::where('users_id_fk',$emp->id)->whereRaw('no_of_installment_completed!=approved_no_of_installment')->get();
        $op = OpeningBalanceLoanAdvancedSalary::where('users_id_fk', $emp->id)->get();
        if (count($op) > 0) {
            foreach ($op as $ld):
                $loanAmount = 0;
                /* get total expected money*/
                $start_month = date("Y-m-d", strtotime($ld->approved_installment_start_month));
                $cur_month = date("Y-m-10", strtotime($forMonth));
                $totalMonth = Carbon::parse($start_month)->diffInMonths(Carbon::parse($cur_month));
                $expectedMoney = floatval($ld->ob_amount);
                /* get total expected money */

                /* get total deposited money */
                $qtd = AdvancedSalaryLoanReceive::select(\DB::raw('SUM(amount) as amount'))->where('type', 'Opening Balance')->where('advanced_salary_loan_fk', $ld->id)->where('users_id_fk', $emp->id)->first();
                /* get total deposited money */

                $balance = $expectedMoney - floatval(@$qtd->amount);

                if ($balance < $ld->approved_installment) {
                    $loanAmount += $balance;
                } else {
                    $loanAmount += $ld->approved_installment;
                }

                $model = new AdvancedSalaryLoanReceive;
                $model->advanced_salary_loan_fk = $ld->id;
                $model->users_id_fk = $emp->id;
                $model->transaction_date = $forMonth;
                $model->amount = floatval($loanAmount);
                $model->fiscal_id_fk = @$this->getCurrentFiscalYear()['id'];
                $model->for_month = $forMonth;
                $model->type = 'Opening Balance';
                $model->received_branch_id = $emp->branchId;
                if ($created_at != null) {
                    $model->created_at = date("Y-m-d H:i:s", strtotime($created_at));
                } else {
                    $model->created_at = date("Y-m-d H:i:s");
                }
                $model->created_by = Auth::user()->id;
                $model->updated_at = date("Y-m-d H:i:s");
                $model->updated_by = Auth::user()->id;
                $model->approved_by = Auth::user()->id;
                $model->approved_at = date("Y-m-d H:i:s");
                $model->payment_status = $paymentStatus;
                if (floatval($model->amount) > 0) {
                    $model->save();
                }

                $this->checkSavePreviousAdvancedSalaryLoan($ld, $forMonth, 'Opening Balance', $paymentStatus, 'save', $created_at);
            endforeach;

            foreach ($op as $ld):
                //count no of complete installment
                $noOfCompletedInstallment = AdvancedSalaryLoanReceive::where('type', 'Opening Balance')->where('advanced_salary_loan_fk', $ld->id)->get();

                //update no of completed installment
                $nvl = OpeningBalanceLoanAdvancedSalary::find($ld->id);
                $nvl->no_of_installment_completed = $nvl->no_of_installment_completed + count($noOfCompletedInstallment);
                $nvl->save();
            endforeach;
        }
    }

    public function adjustWf($userId, $salaryMonth)
    {
        $forMonth = date("Y-m-d", strtotime($salaryMonth));
        $model = new WelfareFundReceive;
        $chkExist = $model->where('users_id_fk', $userId)->where('for_month', $forMonth);
        if (count($chkExist->get()) > 0) {
            $chkExist->delete();
        }
    }

    public function adjustOsf($userId, $salaryMonth)
    {
        $forMonth = date("Y-m-d", strtotime($salaryMonth));
        $model = new OsfReceive;
        $chkExist = $model->where('users_id_fk', $userId)->where('for_month', $forMonth);
        if (count($chkExist->get()) > 0) {
            $chkExist->delete();
        }
    }

    public function adjustInsurance($userId, $salaryMonth)
    {
        $forMonth = date("Y-m-d", strtotime($salaryMonth));
        $model = new InsuranceReceive;
        $chkExist = $model->where('users_id_fk', $userId)->where('for_month', $forMonth);
        if (count($chkExist->get()) > 0) {
            $chkExist->delete();
        }
    }

    public function adjustPf($userId, $salaryMonth)
    {
        $forMonth = date("Y-m-d", strtotime($salaryMonth));
        $model = new ProvidentFundReceive;
        $chkExist = $model->where('users_id_fk', $userId)->where('for_month', $forMonth);
        if (count($chkExist->get()) > 0) {
            $chkExist->delete();
        }
    }

    public function adjustPfLoanAmount($userId, $forMonth, $tdate)
    {
        $vl = ProvidentFundLoan::select('t1.*', 't2.id as review_id', 't2.inst_amount', 't2.completed_no_of_installment')->from('hr_pf_loan as t1')->where('t1.users_id_fk', $userId)->where('t1.status', 'Approved')->whereRaw('t2.completed_no_of_installment!=t2.no_of_inst')->where('t2.inst_start_month', '<=', $forMonth)->join('hr_pf_loan_review as t2', 't2.pf_loan_id_fk', '=', 't1.id')->get();
        $loanAmount = 0;
        if (count($vl) > 0) {
            foreach ($vl as $ld):
                ProvidentFundLoanSchedule::where('pf_loan_review_fk', $ld->review_id)->where('transaction_date', $tdate)->update(['payment_status' => 'Unpaid', 'transaction_date' => '0000-00-00']);

                $pfReExists = PfLoanReceive::where('pf_loan_fk', $ld->id)->where('type', 'Regular')->where('users_id_fk', $userId)->where('transaction_date', $tdate);
                if (count($pfReExists->get()) > 0) {
                    $pfReExists->delete();
                }
            endforeach;
        }

        //Opening balance
        $op = OpeningBalanceLoanPf::where('users_id_fk', $userId)->where('installment_start_date', '<=', $forMonth)->get();
        if (count($op) > 0) {
            foreach ($op as $oppf) {
                OpeningBalanceLoanPfSchedule::where('ob_loan_pf_fk', $oppf->id)->where('transaction_date', $tdate)->update(['payment_status' => 'Unpaid', 'transaction_date' => '0000-00-00']);

                $pfReExists = PfLoanReceive::where('pf_loan_fk', $oppf->id)->where('type', 'Opening Balance')->where('users_id_fk', $userId)->where('transaction_date', $tdate);
                if (count($pfReExists->get()) > 0) {
                    $pfReExists->delete();
                }
            }
        }
    }

    public function getIncomeTax($emp, $salaryMonth)
    {
        $lastDateMonth = date("Y-m-t", strtotime('01-' . $salaryMonth));
        $forMonth = date("Y-m-d", strtotime('01-' . $salaryMonth));
        $this->adjustIncomeTax($emp->id, $lastDateMonth, $forMonth);

        /* Check security deposit stop or not */
        if (StopSalaryBenefit::checkStopSalaryBenefit($emp->id, '01-' . $salaryMonth, 'Income Tax')) {
            return 0;
        }

        $vl = IncomeTax::where('users_id_fk', $emp->id)->where('status', 'Approved')->where('acpected_installment_start_month', '<=', $lastDateMonth)->get();
        $loanAmount = 0;
        if (count($vl) > 0) {
            foreach ($vl as $ld):

                $chkExist = IncomeTaxReceive::where('income_tax_id', $ld->id)->where('users_id_fk', $emp->id)->where('transaction_date', $forMonth);
                if (count($chkExist->get()) > 0) {
                    $chkExist->delete();
                }

                /* get total expected money*/
                $start_month = date("Y-m-01", strtotime($ld->acpected_installment_start_month));
                $cur_month = date("Y-m-10", strtotime($forMonth));
                $totalMonth = Carbon::parse($start_month)->diffInMonths(Carbon::parse($cur_month));
                $expectedMoney = floatval($ld->acpected_amount);
                /* get total expected money*/

                /* get total deposited money */
                $qtd = IncomeTaxReceive::select(\DB::raw('SUM(amount) as amount'))->where('income_tax_id', $ld->id)->where('users_id_fk', $emp->id)->first();
                /* get total deposited money */
                $balance = $expectedMoney - floatval(@$qtd->amount);

                if ($balance < $ld->acpected_installment) {
                    $loanAmount += $balance;
                } else {
                    $loanAmount += $ld->acpected_installment;
                }

                $loanAmount += floatval($this->checkSavePreviousIncomeTax($ld, $forMonth, 'Paid', 'return'));

            endforeach;
        }

        return round($loanAmount);
    }

    public function checkSavePreviousIncomeTax($aedps, $for_Month, $paymentStatus = 'Paid', $rtype = 'save', $created_at = null)
    {
        $returnAmount = 0;
        $start_month = $month = strtotime($aedps->acpected_installment_start_month);
        $end_month = strtotime($for_Month);
        $userId = $aedps->users_id_fk;

        if ($start_month <= $end_month) {
            while ($month < $end_month) {

                $totalDue = $aedps->acpected_amount;

                $totalPaid = IncomeTaxReceive::select(\DB::raw('SUM(amount) as amount'))->where('income_tax_id', $aedps->id)->first();

                $forMonth = date("Y-m-d", $month);

                if ($totalDue > $totalPaid->amount) {
                    $model = new IncomeTaxReceive;
                    $chkExist = $model->where('income_tax_id', $aedps->id)->where('users_id_fk', $userId)->where('for_month', $forMonth)->get();
                    if (count($chkExist) == 0) {
                        $model->income_tax_id = $aedps->id;
                        $model->users_id_fk = $userId;
                        $model->transaction_date = $for_Month;
                        $returnAmount += floatval($aedps->acpected_installment);
                        $model->amount = floatval($aedps->acpected_installment);
                        $model->fiscal_id_fk = @$this->getCurrentFiscalYear()['id'];
                        $model->for_month = $forMonth;
                        if ($created_at != null) {
                            $model->created_at = date("Y-m-d H:i:s", strtotime($created_at));
                        } else {
                            $model->created_at = date("Y-m-d H:i:s");
                        }
                        $model->created_by = Auth::user()->id;
                        $model->updated_at = date("Y-m-d H:i:s");
                        $model->updated_by = Auth::user()->id;
                        $model->payment_status = $paymentStatus;
                        if (floatval($model->amount) > 0 && $rtype == 'save') {
                            $model->save();
                        }
                    }
                }
                $month = strtotime("+1 Month", strtotime($forMonth));
            }
        }
        if ($rtype != 'save') {
            return $returnAmount;
        }

    }

    public function adjustIncomeTax($userId, $lastDateMonth, $forMonth)
    {
        $vl = IncomeTax::where('users_id_fk', $userId)->where('status', 'Approved')->where('acpected_installment_start_month', '<=', $lastDateMonth)->get();
        $loanAmount = 0;
        if (count($vl) > 0) {
            foreach ($vl as $ld):
                $chkExist = IncomeTaxReceive::where('income_tax_id', $ld->id)->where('users_id_fk', $userId)->where('transaction_date', $forMonth);
                if (count($chkExist->get()) > 0) {
                    $chkExist->delete();
                }
            endforeach;
        }
    }

    public function getAdvancedSalaryLoanAmount($emp, $salaryMonth)
    {

        $lastDateMonth = date("Y-m-t", strtotime('01-' . $salaryMonth));
        $forMonth = date("Y-m-d", strtotime('01-' . $salaryMonth));
        $this->adjustAdvancedSalaryLoanAmount($emp->id, $lastDateMonth, $forMonth);

        /* Check security deposit stop or not */
        if (StopSalaryBenefit::checkStopSalaryBenefit($emp->id, '01-' . $salaryMonth, 'Advanced Salary Loan')) {
            return 0;
        }

        $vl = AdvancedSalaryLoan::where('users_id_fk', $emp->id)->where('status', 'Approved')->where('approved_installment_start_month', '<=', $lastDateMonth)->get();
        $loanAmount = 0;
        if (count($vl) > 0) {
            foreach ($vl as $ld):

                $chkExist = AdvancedSalaryLoanReceive::where('type', 'Regular')->where('advanced_salary_loan_fk', $ld->id)->where('users_id_fk', $emp->id)->where('transaction_date', $forMonth);
                if (count($chkExist->get()) > 0) {
                    $chkExist->delete();
                }

                /* get total expected money*/
                $start_month = date("Y-m-01", strtotime($ld->approved_installment_start_month));
                $cur_month = date("Y-m-10", strtotime($forMonth));
                $totalMonth = Carbon::parse($start_month)->diffInMonths(Carbon::parse($cur_month));
                //$expectedMoney = floatval($ld->approved_installment*$totalMonth);
                $expectedMoney = floatval($ld->approved_amount);
                /* get total expected money*/

                /* get total deposited money */
                //$qtd = AdvancedSalaryLoanReceive::select(\DB::raw('SUM(amount) as amount'))->where('type','Regular')->orWhere('type','Advanced')->where('advanced_salary_loan_fk',$ld->id)->where('users_id_fk',$emp->id)->first();
                $qtd = AdvancedSalaryLoanReceive::select(\DB::raw('SUM(amount) as amount'))->where('advanced_salary_loan_fk', $ld->id)->where('users_id_fk', $emp->id)->where(function ($q) {
                    $q->orWhere('type', 'Regular');
                    $q->orWhere('type', 'Advanced');
                })->first();
                /* get total deposited money */
                $balance = $expectedMoney - floatval(@$qtd->amount);

                if ($balance < $ld->approved_installment) {
                    $loanAmount += $balance;
                } else {
                    $loanAmount += $ld->approved_installment;
                }

                $loanAmount += floatval($this->checkSavePreviousAdvancedSalaryLoan($ld, $forMonth, 'Regular', 'Paid', 'return'));

            endforeach;
        }

        $op = OpeningBalanceLoanAdvancedSalary::where('users_id_fk', $emp->id)->get();
        if (count($op) > 0) {
            foreach ($op as $opv):

                $chkExist = AdvancedSalaryLoanReceive::where('type', 'Opening Balance')->where('advanced_salary_loan_fk', $opv->id)->where('users_id_fk', $emp->id)->where('transaction_date', $forMonth);
                if (count($chkExist->get()) > 0) {
                    $chkExist->delete();
                }

                /* get total expected money*/
                $start_month = date("Y-m-d", strtotime($opv->approved_installment_start_month));
                $cur_month = date("Y-m-10", strtotime($forMonth));
                $totalMonth = Carbon::parse($start_month)->diffInMonths(Carbon::parse($cur_month));
                $expectedMoney = floatval($opv->ob_amount);
                /* get total expected money */

                /* get total deposited money */
                $qtd = AdvancedSalaryLoanReceive::select(\DB::raw('SUM(amount) as amount'))->where('type', 'Opening Balance')->where('advanced_salary_loan_fk', $opv->id)->where('users_id_fk', $emp->id)->first();
                /* get total deposited money */

                $balance = $expectedMoney - floatval(@$qtd->amount);

                if ($balance < $opv->approved_installment) {
                    $loanAmount += $balance;
                } else {
                    $loanAmount += $opv->approved_installment;
                }

                $loanAmount += floatval($this->checkSavePreviousAdvancedSalaryLoan($opv, $forMonth, 'Opening Balance', 'Paid', 'return'));
            endforeach;
        }

        return round($loanAmount);
    }

    public function checkSavePreviousAdvancedSalaryLoan($aedps, $for_Month, $type, $paymentStatus = 'Paid', $rtype = 'save', $created_at = null)
    {
        $returnAmount = 0;
        $start_month = $month = strtotime($aedps->approved_installment_start_month);
        $end_month = strtotime($for_Month);
        $userId = $aedps->users_id_fk;

        if ($start_month <= $end_month) {
            while ($month < $end_month) {

                if ($type == 'Opening Balance') {
                    $totalDue = $aedps->ob_amount;
                } else {
                    $totalDue = $aedps->approved_amount;
                }

                $totalPaid = AdvancedSalaryLoanReceive::select(\DB::raw('SUM(amount) as amount'))->where('type', $type)->where('advanced_salary_loan_fk', $aedps->id)->first();

                $forMonth = date("Y-m-d", $month);

                if ($totalDue > $totalPaid->amount) {
                    $model = new AdvancedSalaryLoanReceive;
                    $chkExist = $model->where('type', $type)->where('advanced_salary_loan_fk', $aedps->id)->where('users_id_fk', $userId)->where('for_month', $forMonth)->get();
                    if (count($chkExist) == 0) {
                        $model->advanced_salary_loan_fk = $aedps->id;
                        $model->users_id_fk = $userId;
                        $model->transaction_date = $for_Month;
                        $returnAmount += floatval($aedps->approved_installment);
                        $model->amount = floatval($aedps->approved_installment);
                        $model->fiscal_id_fk = @$this->getCurrentFiscalYear()['id'];
                        $model->for_month = $forMonth;
                        $model->type = $type;
                        $model->received_branch_id = intval(User::find($userId)->branchId);
                        if ($created_at != null) {
                            $model->created_at = date("Y-m-d H:i:s", strtotime($created_at));
                        } else {
                            $model->created_at = date("Y-m-d H:i:s");
                        }
                        $model->created_by = Auth::user()->id;
                        $model->updated_at = date("Y-m-d H:i:s");
                        $model->updated_by = Auth::user()->id;
                        $model->payment_status = $paymentStatus;
                        if (floatval($model->amount) > 0 && $rtype == 'save') {
                            $model->save();
                        }
                    }
                }
                $month = strtotime("+1 Month", strtotime($forMonth));
            }
        }
        if ($rtype != 'save') {
            return $returnAmount;
        }

    }

    public function adjustAdvancedSalaryLoanAmount($userId, $lastDateMonth, $forMonth)
    {
        $vl = AdvancedSalaryLoan::where('users_id_fk', $userId)->where('status', 'Approved')->where('approved_installment_start_month', '<=', $lastDateMonth)->get();
        $loanAmount = 0;
        if (count($vl) > 0) {
            foreach ($vl as $ld):
                $chkExist = AdvancedSalaryLoanReceive::where('type', 'Regular')->where('advanced_salary_loan_fk', $ld->id)->where('users_id_fk', $userId)->where('transaction_date', $forMonth);
                if (count($chkExist->get()) > 0) {
                    $chkExist->delete();
                }
            endforeach;
        }

        $op = OpeningBalanceLoanAdvancedSalary::where('users_id_fk', $userId)->get();
        if (count($op) > 0) {
            foreach ($op as $opv):
                $chkExist = AdvancedSalaryLoanReceive::where('type', 'Opening Balance')->where('advanced_salary_loan_fk', $opv->id)->where('users_id_fk', $userId)->where('transaction_date', $forMonth);
                if (count($chkExist->get()) > 0) {
                    $chkExist->delete();
                }
            endforeach;
        }
    }

    public function getSecurityAmount($emp, $salaryMonth)
    {
        $securityMoney = $installment = 0;

        $forMonth = date("Y-m-d", strtotime('01-' . $salaryMonth));

        $this->adjustSecurityAmount($emp->emp_id_fk, $forMonth);

        /* Check security deposit stop or not */
        if (StopSalaryBenefit::checkStopSalaryBenefit($emp->id, '01-' . $salaryMonth, 'Security Deposit')) {
            return 0;
        }

        $securityMoney = floatval(SecurityMoney::getSecurityMoney(@$emp->employee->organization->company_id_fk, @$emp->employee->organization->project_id_fk, @$emp->employee->organization->grade, @$emp->employee->organization->level_id_fk));

        $installment = floatval(@$emp->employee->organization->installment_amount);

        if ($emp->emp_id_fk > 0) {
            $totalDeposit = 0;

            $opFund = OpeningBalanceFund::where('users_id_fk', $emp->id)->first();
            if ($opFund) {
                $totalDeposit += $opFund->security_deposit;
            }

            $collection = SecurityMoneyCollection::select(\DB::raw('SUM(amount) as amount'))->where('emp_id_fk', $emp->emp_id_fk)->groupby('emp_id_fk')->first();
            if (isset($collection->amount)) {
                $totalDeposit += $collection->amount;
            }

        } else {
            $totalDeposit = 0;
        }

        //return $securityMoney.'-'.$totalDeposit;

        $balance = floatval($securityMoney) - floatval($totalDeposit);

        $joining_date = @$emp->employee->organization->joining_date;
        if ($joining_date != '0000-00-00') {
            $joining_date = date("Y-m-01", strtotime($joining_date));
            $cur_month = date("Y-m-10", strtotime('10-' . $salaryMonth));
            $totalMonth = Carbon::parse($joining_date)->diffInMonths(Carbon::parse($cur_month));
            $actualDeposit = floatval($installment * $totalMonth);
            if ($actualDeposit > floatval($securityMoney)) {
                $actualDeposit = floatval($securityMoney);
            }

        } else {
            $actualDeposit = 0;
        }

        $dueDeposit = $actualDeposit - floatval($totalDeposit);

        //return $securityMoney.'-'.$balance.'-'.$installment.'-'.$dueDeposit.'-'.$actualDeposit.'-'.$totalDeposit;

        if ($dueDeposit > 0 && $dueDeposit < $installment) {
            return round($dueDeposit);
        } else if ($balance > 0 && $balance >= $installment) {
            return round($installment);
        } else {
            return round($balance);
        }

        //if($dueDeposit>0){
        //$installment += $dueDeposit;
        //}

        /*if($installment<=0){
    return round($balance);
    }else if($balance>0 && $balance >= $installment){
    return round($installment);
    }else{
    return round($balance);
    }*/
    }

    public function adjustSecurityAmount($emp_id_fk, $forMonth)
    {
        $chkExist = SecurityMoneyCollection::where('emp_id_fk', $emp_id_fk)->where('type', 'Installment')->where('for_month', $forMonth);
        if (count($chkExist->get()) > 0) {
            $chkExist->delete();
        }
    }

    public function getEdpsAmount($emp, $salaryMonth)
    {
        /*if(isset($emp->employee->organization->edps_amount)){
        return $emp->employee->organization->edps_amount;
        }*/

        $this->adjustEdps($emp->id, '01-' . $salaryMonth);

        /* Check security deposit stop or not */
        if (StopSalaryBenefit::checkStopSalaryBenefit($emp->id, '01-' . $salaryMonth, 'EDPS')) {
            return 0;
        }

        $edps = Edps::select(\DB::raw('SUM(amount) as amount'))->where('status', 'Active')->where('users_id_fk', $emp->id)->whereRaW('? BETWEEN `start_month` and `end_month`', [date('Y-m-d', strtotime('01-' . $salaryMonth))])->first();
        return $installment = floatval(@$edps->amount);

        /* Get total deposit money */
        $edpsReceived = EdpsReceive::select(\DB::raw('SUM(hr_edps_receive.amount) as amount'))
            ->leftJoin('hr_edps', 'hr_edps_receive.edps_id_fk', '=', 'hr_edps.id')
            ->where('hr_edps.status', '=', 'Active')
            ->where('hr_edps_receive.users_id_fk', $emp->id)
            ->whereRaW('? BETWEEN `hr_edps`.`start_month` and `hr_edps`.`end_month`', [date('Y-m-d', strtotime('01-' . $salaryMonth))])
            ->first();
        $totalDeposited = floatval(@$edpsReceived->amount);

        $opFund = OpeningBalanceFund::where('users_id_fk', $emp->id)->first();
        if (count($opFund) > 0) {
            $totalDeposited += $opFund->edps;
        }

        /* Get total deposit money */

        /* Get total expected money */
        $activeEdps = Edps::where('status', 'Active')->where('users_id_fk', $emp->id)->whereRaW('? BETWEEN `start_month` and `end_month`', [date('Y-m-d', strtotime('01-' . $salaryMonth))])->get();
        $acpetedAmount = 0;
        if (count($activeEdps) > 0) {
            foreach ($activeEdps as $aedps):
                $start_month = date("Y-m-d", strtotime($aedps->start_month));
                $cur_month = date("Y-m-10", strtotime('10-' . $salaryMonth));
                $totalMonth = Carbon::parse($start_month)->diffInMonths(Carbon::parse($cur_month));
                $acpetedAmount += floatval($aedps->amount * $totalMonth);
            endforeach;
        }
        /* Get total expected money */

        $dueDeposit = $acpetedAmount - floatval($totalDeposited);
        if ($dueDeposit > 0) {
            $installment += $dueDeposit;
        }

        //return $totalMonth.'-'.$dueDeposit.'-'.$installment.'-'.$acpetedAmount.'-'.$totalDeposited;

        if ($installment <= 0) {
            return 0;
        } else {
            return round($installment);
        }
    }

    public function adjustEdps($userId, $salaryMonth)
    {
        $model = new EdpsReceive;
        $chkExist = $model->where('users_id_fk', $userId)->where('transaction_month', date("Y-m-d", strtotime($salaryMonth)));
        if (count($chkExist->get()) > 0) {
            $chkExist->delete();
        }
    }

    public function getVehicleLoanAmount($emp, $salaryMonth)
    {

        $forMonth = date("Y-m-t", strtotime('01-' . $salaryMonth));
        //$tdate = date("Y-m",strtotime($forMonth));
        $tdate = date("Y-m-d", strtotime('01-' . $salaryMonth));

        $this->adjustVehicleLoanAmount($emp->id, $forMonth, $tdate);

        /* Check pf loan amount stop or not */
        if (StopSalaryBenefit::checkStopSalaryBenefit($emp->id, '01-' . $salaryMonth, 'Vehicle Loan')) {
            return 0;
        }

        $vl = VehicleLoan::where('users_id_fk', $emp->id)->where('status', 'Approved')->whereRaw('completed_no_of_installment!=approved_no_of_installment')->where('installment_start_month', '<=', $forMonth)->get();
        $loanAmount = 0;
        if (count($vl) > 0) {
            foreach ($vl as $ld):
                VehicleLoanSchedule::where('vehicle_loan_fk', $ld->id)->where('transaction_date', $tdate)->update(['payment_status' => 'Unpaid', 'transaction_date' => '0000-00-00']);

                $pfReExists = VehicleLoanReceive::where('vehicle_loan_fk', $ld->id)->where('type', 'Regular')->where('users_id_fk', $emp->id)->where('transaction_date', $tdate);
                if (count($pfReExists->get()) > 0) {
                    $pfReExists->delete();
                }

                $schedule = VehicleLoanSchedule::select(\DB::raw('sum(total_payment) as total_payment'))->where('payment_date', '<=', $forMonth)->where('payment_status', '=', 'Unpaid')->where('vehicle_loan_fk', '=', $ld->id)->first();
                $loanAmount += $schedule->total_payment;
            endforeach;
        }

        // Opening balance
        $op = OpeningBalanceLoanVehicle::where('users_id_fk', $emp->id)->where('installment_start_date', '<=', $forMonth)->get();
        if (count($op) > 0) {
            foreach ($op as $opv):
                OpeningBalanceLoanVehicleSchedule::where('ob_loan_vehicle_fk', $opv->id)->where('transaction_date', $tdate)->update(['payment_status' => 'Unpaid', 'transaction_date' => '0000-00-00']);

                $pfReExists = VehicleLoanReceive::where('vehicle_loan_fk', $opv->id)->where('type', 'Opening Balance')->where('users_id_fk', $emp->id)->where('transaction_date', $tdate);
                if (count($pfReExists->get()) > 0) {
                    $pfReExists->delete();
                }
                $schedule = OpeningBalanceLoanVehicleSchedule::select(\DB::raw('sum(total_payment) as total_payment'))->where('payment_date', '<=', $forMonth)->where('payment_status', '=', 'Unpaid')->where('ob_loan_vehicle_fk', '=', $opv->id)->first();
                $loanAmount += $schedule->total_payment;
            endforeach;
        }

        return round($loanAmount);
    }

    public function adjustVehicleLoanAmount($userId, $forMonth, $tdate)
    {
        $vl = VehicleLoan::where('users_id_fk', $userId)->where('status', 'Approved')->whereRaw('completed_no_of_installment!=approved_no_of_installment')->where('installment_start_month', '<=', $forMonth)->get();
        $loanAmount = 0;
        if (count($vl) > 0) {
            foreach ($vl as $ld):
                VehicleLoanSchedule::where('vehicle_loan_fk', $ld->id)->where('transaction_date', $tdate)->update(['payment_status' => 'Unpaid', 'transaction_date' => '0000-00-00']);

                $pfReExists = VehicleLoanReceive::where('vehicle_loan_fk', $ld->id)->where('type', 'Regular')->where('users_id_fk', $userId)->where('transaction_date', $tdate);
                if (count($pfReExists->get()) > 0) {
                    $pfReExists->delete();
                }
            endforeach;
        }

        // Opening balance
        $op = OpeningBalanceLoanVehicle::where('users_id_fk', $userId)->where('installment_start_date', '<=', $forMonth)->get();
        if (count($op) > 0) {
            foreach ($op as $opv):
                OpeningBalanceLoanVehicleSchedule::where('ob_loan_vehicle_fk', $opv->id)->where('transaction_date', $tdate)->update(['payment_status' => 'Unpaid', 'transaction_date' => '0000-00-00']);

                $pfReExists = VehicleLoanReceive::where('vehicle_loan_fk', $opv->id)->where('type', 'Opening Balance')->where('users_id_fk', $userId)->where('transaction_date', $tdate);
                if (count($pfReExists->get()) > 0) {
                    $pfReExists->delete();
                }
            endforeach;
        }
    }

    public function getPresentDays($emp, $salary_month)
    {
        // dd($salary_month);
        $salaryMonth = date('Y-m', strtotime('01-' . $salary_month));
        $joiningMonth = date('Y-m', strtotime($emp->employee->organization->joining_date));

        $joiningDay = date('d', strtotime($emp->employee->organization->joining_date));

        $presentDay = 30;
        if ($salaryMonth == $joiningMonth) {
            $presentDay = $presentDay - intval($joiningDay) + 1;
        }

        $getLastMonthGenerateDate = @SalaryGenerate::where('company_id_fk', $emp->employee->organization->company_id_fk)
            ->where('project_id_fk', $emp->employee->organization->project_id_fk)
            ->where('branch_id_fk', $emp->employee->organization->branch_id_fk)
            ->orderby('id', 'desc')
            ->first()->created_at;
        // dd($getLastMonthGenerateDate);
        $countLwop = HrLeaveApplication::checkLwop($emp, $salary_month, $getLastMonthGenerateDate, $emp->employee->organization->terminate_resignation_date);
        // dd($presentDay);

        return intval($presentDay - $countLwop);
    }

    public function saveArearPf($arearRow, $created_at = null)
    {
        if (count($arearRow) > 0) {
            foreach ($arearRow as $row) {
                if (isset($row['pf_org']) || isset($row['pf_self'])) {
                    foreach ($row['pf_org'] as $month => $value) {
                        $this->savePF(date('m-Y', strtotime($month)), $row['user_id'], $value, floatval(@$row['pf_self'][$month]), 'Arear', $row['payment_status'], $created_at);
                    }
                }
            }
        }
    }

    public function saveArearInsurance($arearRow, $created_at = null)
    {
        if (count($arearRow) > 0) {
            foreach ($arearRow as $row) {
                if (isset($row['insurance_org']) || isset($row['insurance_self'])) {
                    foreach ($row['insurance_org'] as $month => $value) {
                        $this->saveInsurance(date('m-Y', strtotime($month)), $row['user_id'], $value, floatval(@$row['insurance_self'][$month]), 'Arear', $row['payment_status'], $created_at);
                    }
                }
            }
        }
    }

    public function saveArearOsf($arearRow, $created_at = null)
    {
        if (count($arearRow) > 0) {
            foreach ($arearRow as $row) {
                if (isset($row['osf_org']) || isset($row['osf_self'])) {
                    foreach ($row['osf_org'] as $month => $value) {
                        $this->saveOsf(date('m-Y', strtotime($month)), $row['user_id'], $value, floatval(@$row['osf_self'][$month]), 'Arear', $row['payment_status'], $created_at);
                    }
                }
            }
        }
    }

    public function savePF($salaryMonth, $userId, $orgAmount, $selfAmount, $type = 'Regular', $paymentStatus = 'Paid', $created_at = null)
    {
        $forMonth = date("Y-m-d", strtotime('01-' . $salaryMonth));
        $model = new ProvidentFundReceive;
        $chkExist = $model->where('users_id_fk', $userId)->where('for_month', $forMonth)->where('type', $type)->get();
        if (count($chkExist) > 0) {
            $model->where('users_id_fk', $userId)->where('for_month', $forMonth)->where('type', $type)->delete();
        }
        $model->users_id_fk = $userId;
        $model->transaction_date = date('Y-m-d');
        $model->org_amount = floatval($orgAmount);
        $model->emp_amount = floatval($selfAmount);
        $model->total_amount = $model->org_amount + $model->emp_amount;
        $model->fiscal_id_fk = @$this->getCurrentFiscalYear()['id'];
        $model->for_month = $forMonth;
        $model->type = $type;
        $model->received_branch_id = intval(User::find($userId)->branchId);
        $model->payment_status = $paymentStatus;
        if ($created_at != null) {
            $model->created_at = date("Y-m-d H:i:s", strtotime($created_at));
        } else {
            $model->created_at = date("Y-m-d H:i:s");
        }
        $model->created_by = Auth::user()->id;
        $model->updated_at = date("Y-m-d H:i:s");
        $model->updated_by = Auth::user()->id;
        if (floatval($model->total_amount) > 0) {
            $model->save();
        }

    }

    public function saveInsurance($salaryMonth, $userId, $orgAmount, $selfAmount, $type = 'Regular', $paymentStatus = 'Paid', $created_at = null)
    {
        $forMonth = date("Y-m-d", strtotime('01-' . $salaryMonth));
        $model = new InsuranceReceive;
        $chkExist = $model->where('users_id_fk', $userId)->where('for_month', $forMonth)->where('type', $type)->get();
        if (count($chkExist) > 0) {
            $model->where('users_id_fk', $userId)->where('for_month', $forMonth)->where('type', $type)->delete();
        }
        $model->users_id_fk = $userId;
        $model->transaction_date = date('Y-m-d');
        $model->org_amount = floatval($orgAmount);
        $model->emp_amount = floatval($selfAmount);
        $model->total_amount = $model->org_amount + $model->emp_amount;
        $model->fiscal_id_fk = @$this->getCurrentFiscalYear()['id'];
        $model->for_month = $forMonth;
        $model->type = $type;
        $model->received_branch_id = intval(User::find($userId)->branchId);
        $model->payment_status = $paymentStatus;
        if ($created_at != null) {
            $model->created_at = date("Y-m-d H:i:s", strtotime($created_at));
        } else {
            $model->created_at = date("Y-m-d H:i:s");
        }

        $model->created_by = Auth::user()->id;
        $model->updated_at = date("Y-m-d H:i:s");
        $model->updated_by = Auth::user()->id;
        if (floatval($model->total_amount) > 0) {
            $model->save();
        }

    }

    public function saveOsf($salaryMonth, $userId, $orgAmount, $selfAmount, $type = 'Regular', $paymentStatus = 'Paid', $created_at = null)
    {
        $forMonth = date("Y-m-d", strtotime('01-' . $salaryMonth));
        $model = new OsfReceive;
        $chkExist = $model->where('users_id_fk', $userId)->where('for_month', $forMonth)->where('type', $type)->get();
        if (count($chkExist) > 0) {
            $model->where('users_id_fk', $userId)->where('for_month', $forMonth)->where('type', $type)->delete();
        }
        $model->users_id_fk = $userId;
        $model->transaction_date = date('Y-m-d');
        $model->org_amount = floatval($orgAmount);
        $model->emp_amount = floatval($selfAmount);
        $model->total_amount = $model->org_amount + $model->emp_amount;
        $model->fiscal_id_fk = @$this->getCurrentFiscalYear()['id'];
        $model->for_month = $forMonth;
        $model->type = $type;
        $model->received_branch_id = intval(User::find($userId)->branchId);
        $model->payment_status = $paymentStatus;
        if ($created_at != null) {
            $model->created_at = date("Y-m-d H:i:s", strtotime($created_at));
        } else {
            $model->created_at = date("Y-m-d H:i:s");
        }

        $model->created_by = Auth::user()->id;
        $model->updated_at = date("Y-m-d H:i:s");
        $model->updated_by = Auth::user()->id;
        if (floatval($model->total_amount) > 0) {
            $model->save();
        }

    }

    public function saveArearWf($arearRow, $created_at = null)
    {
        if (count($arearRow) > 0) {
            $data['otherSettings'] = Other::where('status', 1)->first();
            foreach ($arearRow as $row) {
                $emps = User::find($row['user_id']);
                if (@$emps->employee->organization->wf_active == 1 && @$data['otherSettings']->wf_enable == '1') {
                    if (isset($row['wf_org']) || isset($row['wf_self']) || isset($row['wf_contri_self'])) {
                        foreach ($row['wf_org'] as $month => $value) {

                            $this->saveWF(date('m-Y', strtotime($month)), $row['user_id'], $value, floatval(@$row['wf_self'][$month]), floatval(@$row['wf_contri_self'][$month]), 'Arear', $row['payment_status'], $created_at);
                        }
                    }
                }
            }
        }
    }

    public function saveWF($salaryMonth, $userId, $orgAmount, $selfAmount, $selfContiAmount, $type = 'Regular', $paymentStatus = 'Paid', $created_at)
    {
        $forMonth = date("Y-m-d", strtotime('01-' . $salaryMonth));
        $model = new WelfareFundReceive;
        $chkExist = $model->where('users_id_fk', $userId)->where('for_month', $forMonth)->where('type', $type)->get();
        if (count($chkExist) > 0) {
            $model->where('users_id_fk', $userId)->where('for_month', $forMonth)->where('type', $type)->delete();
        }
        $model->users_id_fk = $userId;
        $model->transaction_date = date('Y-m-d');
        $model->org_contri_amount = floatval($orgAmount);
        $model->emp_contri_amount = floatval($selfContiAmount);
        $model->emp_amount = floatval($selfAmount);
        $model->total_amount = $model->org_contri_amount + $model->emp_contri_amount + $model->emp_amount;
        $model->fiscal_id_fk = @$this->getCurrentFiscalYear()['id'];
        $model->for_month = $forMonth;
        $model->type = $type;
        $model->received_branch_id = User::find($userId)->branchId;
        if ($created_at != null) {
            $model->created_at = date("Y-m-d H:i:s", strtotime($created_at));
        } else {
            $model->created_at = date("Y-m-d H:i:s");
        }

        $model->created_by = Auth::user()->id;
        $model->updated_at = date("Y-m-d H:i:s");
        $model->updated_by = Auth::user()->id;
        $model->payment_status = $paymentStatus;
        if (floatval($model->total_amount) > 0) {
            $model->save();
        }

    }

    //job age in month
    public function jobAge($joining_date, $last_date = null)
    {
        if ($joining_date != '0000-00-00') {
            if ($last_date == null) {
                $last_date = Carbon::now();
            }

            return Carbon::parse($joining_date)->diffInMonths($last_date);
            //return Carbon::parse($joining_date)->diffForHumans(Carbon::now(),true);
        }
    }

    public static function userLastSalary($user_id_fk)
    {
        $salarySheet = SalaryGenerate::whereRaw('contents LIKE :uid', [':uid' => '%"user_id":' . $user_id_fk . ',"payment_status":"Paid",%'])->orderby('id', 'desc')->first();
        // dd($salarySheet);
        if ($salarySheet) {
            $data = json_decode(@$salarySheet->contents);
            $res = [];
            $res['month'] = $salarySheet->target_month;
            $res['abenefit'] = 0;
            $res['bbenefit'] = 0;
            foreach ($data as $info):
                if ($info->user_id == $user_id_fk) {
                    // dd($info);
                    foreach ($info as $k => $v):
                        if (strpos($k, 'benefit-a') !== false) {
                            $res['abenefit'] += floatval($v);
                        } else if (strpos($k, 'benefit-b') !== false) {
                            $res['bbenefit'] += floatval($v);
                        }

                    endforeach;
                    $res['total'] = $info->net_payable;
                }
            endforeach;
            return $res;
        }
    }

    public static function discontinuationReason($model)
    {
        if ($model->organization->job_status == 'Resign') {
            return @ResignInfo::where('users_id_fk', $model->user->id)->where('status', 'Approved')->first()->note;
        } else if ($model->organization->job_status == 'Terminate') {
            // dd($model->organization->job_status);
            return @TerminateInfo::where('users_id_fk', $model->user->id)->where('status', 'Approved')->first()->note;
        } else if ($model->organization->job_status == 'Retirement') {
            return @RetirementInfo::where('users_id_fk', $model->user->id)->where('status', 'Approved')->first()->note;
        }
    }

    public static function employeeStatusOption()
    {
        return [
            '' => '-- Status --',
            '1' => 'Active',
            '0' => 'Inactive',
        ];
    }

    public function employeeStatus($type, $status)
    {
        if ($type == 'symbol') {
            if ($status == 1 || $status == 'Active') {
                return "<span class='btn btn-success btn-xs'><i class='fa fa-check'></i></span>";
            } else {
                return "<span class='btn btn-danger btn-xs'><i class='fa fa-exclamation-circle'></i></span>";
            }
        }
    }

    public static function getBranch()
    {
        $data = array();
        $data[''] = '--- Branch ---';
        $data[1] = '000-Head Office';
        $branch = GnrBranch::where('id', '!=', 1)->get();
        foreach ($branch as $br) {
            $data[$br->id] = $br->branchCode . '-' . $br->name;
        }
        return $data;
    }

    public static function getPosition()
    {
        $position = Position::all();
        $data[''] = '--- Designation ---';
        foreach ($position as $br) {
            $data[$br->id] = $br->name;
        }
        return $data;
    }

    public static function getDepartment()
    {
        $position = Department::orderBy('name')->get();
        $data[''] = '--- Department ---';
        foreach ($position as $br) {
            $data[$br->id] = $br->name;
        }
        return $data;
    }

    public static function getGrade()
    {
        $position = Grade::all();
        $data[''] = '--- Grade ---';
        foreach ($position as $br) {
            $data[$br->id] = $br->name;
        }
        return $data;
    }

    public static function getProject()
    {
        $project = GnrProject::all();
        $data[''] = '--- Project ---';
        foreach ($project as $br) {
            $data[$br->id] = $br->name;
        }
        return $data;
        return response::json($data);
    }

    public static function getVehicleType()
    {
        $project = VehicleType::all();
        $data[''] = 'Vehicle Type';
        foreach ($project as $br) {
            $data[$br->id] = $br->name;
        }
        return $data;
        //return response::json($data);
    }

    public static function get_date_diff($time1, $time2, $precision = 2)
    {
        // If not numeric then convert timestamps
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }
        // If time1 > time2 then swap the 2 values
        if ($time1 > $time2) {
            list($time1, $time2) = array($time2, $time1);
        }
        // Set up intervals and diffs arrays
        $intervals = array('year', 'month', 'day', 'hour', 'minute', 'second');
        $diffs = array();
        foreach ($intervals as $interval) {
            // Create temp time from time1 and interval
            $ttime = strtotime('+1 ' . $interval, $time1);
            // Set initial values
            $add = 1;
            $looped = 0;
            // Loop until temp time is smaller than time2
            while ($time2 >= $ttime) {
                // Create new temp time from time1 and interval
                $add++;
                $ttime = strtotime("+" . $add . " " . $interval, $time1);
                $looped++;
            }
            $time1 = strtotime("+" . $looped . " " . $interval, $time1);
            $diffs[$interval] = $looped;
        }
        $count = 0;
        $times = array();
        foreach ($diffs as $interval => $value) {
            // Break if we have needed precission
            if ($count >= $precision) {
                break;
            }
            // Add value and interval if value is bigger than 0
            if ($value > 0) {
                if ($value != 1) {
                    $interval .= "s";
                }
                // Add value and interval to times array
                $times[] = $value . " " . $interval;
                $count++;
            }
        }
        // Return string with times
        return implode(", ", $times);
    }

    public static function get_date_diff_in_year($time1, $time2, $precision = 2)
    {
        // If not numeric then convert timestamps
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }
        // If time1 > time2 then swap the 2 values
        if ($time1 > $time2) {
            list($time1, $time2) = array($time2, $time1);
        }
        // Set up intervals and diffs arrays
        $intervals = array('year');
        $diffs = array();
        foreach ($intervals as $interval) {
            // Create temp time from time1 and interval
            $ttime = strtotime('+1 ' . $interval, $time1);
            // Set initial values
            $add = 1;
            $looped = 0;
            // Loop until temp time is smaller than time2
            while ($time2 >= $ttime) {
                // Create new temp time from time1 and interval
                $add++;
                $ttime = strtotime("+" . $add . " " . $interval, $time1);
                $looped++;
            }
            $time1 = strtotime("+" . $looped . " " . $interval, $time1);
            $diffs[$interval] = $looped;
        }
        $count = 0;
        $times = array();
        foreach ($diffs as $interval => $value) {
            // Break if we have needed precission
            if ($count >= $precision) {
                break;
            }
            // Add value and interval if value is bigger than 0
            if ($value > 0) {
                if ($value != 1) {
                    $interval .= "s";
                }
                // Add value and interval to times array
                $times[] = $value . " " . $interval;
                $count++;
            }
        }
        // Return string with times
        return implode(", ", $times);
    }

    public function get_date_diff_in_month($time1, $time2, $precision = 2)
    {
        // If not numeric then convert timestamps
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }
        // If time1 > time2 then swap the 2 values
        if ($time1 > $time2) {
            list($time1, $time2) = array($time2, $time1);
        }
        // Set up intervals and diffs arrays
        $intervals = array('month');
        $diffs = array();
        foreach ($intervals as $interval) {
            // Create temp time from time1 and interval
            $ttime = strtotime('+1 ' . $interval, $time1);
            // Set initial values
            $add = 1;
            $looped = 0;
            // Loop until temp time is smaller than time2
            while ($time2 >= $ttime) {
                // Create new temp time from time1 and interval
                $add++;
                $ttime = strtotime("+" . $add . " " . $interval, $time1);
                $looped++;
            }
            $time1 = strtotime("+" . $looped . " " . $interval, $time1);
            $diffs[$interval] = $looped;
        }
        $count = 0;
        $times = array();
        foreach ($diffs as $interval => $value) {
            // Break if we have needed precission
            if ($count >= $precision) {
                break;
            }
            // Add value and interval if value is bigger than 0
            if ($value > 0) {
                if ($value != 1) {
                    $interval .= "s";
                }
                // Add value and interval to times array
                $times[] = $value . " " . $interval;
                $count++;
            }
        }
        // Return string with times
        return implode(", ", $times);
    }

    public function get_date_diff_in_day($time1, $time2, $precision = 2)
    {
        // If not numeric then convert timestamps
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }
        // If time1 > time2 then swap the 2 values
        if ($time1 > $time2) {
            list($time1, $time2) = array($time2, $time1);
        }
        // Set up intervals and diffs arrays
        $intervals = array('day');
        $diffs = array();
        foreach ($intervals as $interval) {
            // Create temp time from time1 and interval
            $ttime = strtotime('+1 ' . $interval, $time1);
            // Set initial values
            $add = 1;
            $looped = 0;
            // Loop until temp time is smaller than time2
            while ($time2 >= $ttime) {
                // Create new temp time from time1 and interval
                $add++;
                $ttime = strtotime("+" . $add . " " . $interval, $time1);
                $looped++;
            }
            $time1 = strtotime("+" . $looped . " " . $interval, $time1);
            $diffs[$interval] = $looped;
        }
        $count = 0;
        $times = array();
        foreach ($diffs as $interval => $value) {
            // Break if we have needed precission
            if ($count >= $precision) {
                break;
            }
            // Add value and interval if value is bigger than 0
            if ($value > 0) {
                /*if( $value != 1 ){
                $interval .= "s";
                }*/
                // Add value and interval to times array
                //$times[] = $value . " " . $interval;
                $times[] = $value;
                $count++;
            }
        }
        // Return string with times
        return implode(", ", $times);
    }

    public function amountInWords($number)
    {
        //$no = round($number);
        //$point = round($number - $no, 2) * 100;
        //number_format('11234.50',2,'.','')
        $no = number_format($number, 2, '.', '');
        $point = explode('.', $no);
        if ((int)$point[1] > 0) {
            $point = $point[1];
        } else {
            $point = '';
        }

        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        $words = array('0' => '', '1' => 'one', '2' => 'two',
            '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
            '7' => 'seven', '8' => 'eight', '9' => 'nine',
            '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
            '13' => 'thirteen', '14' => 'fourteen',
            '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
            '18' => 'eighteen', '19' => 'nineteen', '20' => 'twenty',
            '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
            '60' => 'sixty', '70' => 'seventy',
            '80' => 'eighty', '90' => 'ninety');
        $digits = array('', 'hundred', 'thousand', 'lac', 'crore');
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? '' : null;
                //$plural='';
                if ($point != '') {
                    $hundred = null;
                } else {
                    $hundred = ($counter == 1 && $str[0]) ? 'and ' : null;
                }

                $str[] = ($number < 21) ? $words[$number] .
                    " " . $digits[$counter] . $plural . " " . $hundred :
                    $words[floor($number / 10) * 10]
                    . " " . $words[$number % 10] . " "
                    . $digits[$counter] . $plural . " " . $hundred;
            } else {
                $str[] = null;
            }

        }
        $str = array_reverse($str);
        $result = implode('', $str);

        /* for paisa */
        $hundred_2 = null;
        $afterPoint = $point;
        $digits_2 = strlen($point);
        $i_2 = 0;
        $str_2 = array();
        while ($i_2 < $digits_2) {
            $divider_2 = ($i_2 == 2) ? 10 : 100;
            $number_2 = floor($point % $divider_2);
            $point = floor($point / $divider_2);
            $i_2 += ($divider_2 == 10) ? 1 : 2;
            if ($number_2) {
                $plural_2 = (($counter_2 = count($str_2)) && $number_2 > 9) ? '' : null;
                //$plural='';
                $hundred_2 = ($counter_2 == 1 && $str_2[0]) ? 'and ' : null;
                $str_2[] = ($number_2 < 21) ? $words[$number_2] .
                    " " . $digits_2[$counter_2] . $plural_2 . " " . $hundred_2 :
                    $words[floor($number_2 / 10) * 10]
                    . " " . $words[$number_2 % 10] . " "
                    . $digits_2[$counter_2] . $plural_2 . " " . $hundred_2;
            } else {
                $str_2[] = null;
            }

        }
        $str_2 = array_reverse($str_2);
        $paisa = implode('', $str_2);
        /* for paisa */

        /* $points = ($point) ?
        $words[$point / 10] . " " .
        $words[$point = $point % 10] : ''; */
        $ff = $result . " ";
        if ((int)$afterPoint > 0) {
            $ff = $ff . " and " . $paisa . " Paisa";
        }
        /* if(trim($points)!=''){
        $ff = $ff." and ".$points . " Paisa";
        } */
        $ff .= ' Only';
        return ucwords($ff);
    }

    public function yearEndExecute($BranchId, $fiscalId)
    {

        $CompanyId = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->first()->companyId;

        if ($BranchId == 1) {
            $projectIdArr = DB::table('gnr_project')->pluck('id')->toArray();
        } else {
            $projectIdArr = DB::table('gnr_branch')->where('id', $BranchId)->pluck('projectId')->toArray();
        }

        $fiscalYearInfo = DB::table('gnr_fiscal_year')->where('companyId', $CompanyId)->where('id', $fiscalId)->first();
        $yearStartDate = $fiscalYearInfo->fyStartDate;
        $yearEndDate = $fiscalYearInfo->fyEndDate;
        $lastFiscalYearEndDate = Carbon::parse($yearStartDate)->subDays(1)->format('Y-m-d');
        // dd($lastFiscalYearEndDate);

        $yearEnd = new AccYearEnd;

        $yearEnd->date = $fiscalYearInfo->fyEndDate;
        $yearEnd->branchIdFk = $BranchId;
        $yearEnd->companyId = $CompanyId;
        $yearEnd->fiscalYearId = $fiscalId;
        $yearEnd->startDate = $fiscalYearInfo->fyStartDate;
        $yearEnd->endDate = $fiscalYearInfo->fyEndDate;
        $yearEnd->createdAt = Carbon::now();
        // dd($yearEnd);

        $yearEnd->save();

        // send information to opening balance for next fiscal year
        foreach ($projectIdArr as $projectId) {
            // dd($projectIdArr);
            $ledgers = $this->getLedgerHeaderInfo($projectId, $BranchId, $CompanyId);
            $projectTypeIdArr = DB::table('gnr_project_type')
                ->where('projectId', $projectId)
                ->pluck('id')->toArray();
            // dd($ledgers);
            foreach ($projectTypeIdArr as $projectTypeId) {
                // dd($projectTypeIdArr);
                $voucherInfos = $this->getVoucherInfo($projectTypeId, $projectId, $BranchId, $CompanyId, $yearStartDate, $yearEndDate);
                // dd($voucherInfos);

                $array_size = count($ledgers);
                for ($i = 0; $i < $array_size; $i++) {
                    $openingBalanceInfo = DB::table('acc_opening_balance')
                        ->where('ledgerId', $ledgers[$i]->id)
                        ->where('projectId', $projectId)
                        ->where('projectTypeId', $projectTypeId)
                        ->where('branchId', $BranchId)
                        ->where('openingDate', $lastFiscalYearEndDate)
                        ->first();
                    // dd($openingBalanceInfo);
                    if ($openingBalanceInfo) {
                        $openingDebitAmount = $openingBalanceInfo->debitAmount;
                        $openingCreditAmount = $openingBalanceInfo->creditAmount;
                    } else {
                        $openingDebitAmount = 0;
                        $openingCreditAmount = 0;
                    }

                    $obId = $fiscalId . "." . $projectId . "." . $BranchId . "." . $projectTypeId;
                    // dd($obId);
                    $debitAmount = $voucherInfos->where('debitAcc', $ledgers[$i]->id)->sum('amount') + $openingDebitAmount;
                    // dd($debitAmount);
                    $creditAmount = $voucherInfos->where('creditAcc', $ledgers[$i]->id)->sum('amount') + $openingCreditAmount;
                    // dd($creditAmount);

                    $data = array(

                        'obId' => $obId,
                        'projectId' => $projectId,
                        'branchId' => $BranchId,
                        'companyIdFk' => $CompanyId,
                        'projectTypeId' => $projectTypeId,
                        'openingDate' => $yearEndDate,
                        'fiscalYearId' => $fiscalId,
                        'ledgerId' => $ledgers[$i]->id,
                        'debitAmount' => round((float)$debitAmount, 2),
                        'creditAmount' => round((float)$creditAmount, 2),
                        'balanceAmount' => round((float)$debitAmount - $creditAmount, 2),
                        'createdDate' => Carbon::now()
                    );

                    // dd($data);
                    DB::table('acc_opening_balance')->insert($data);

                } // for loop ledger
            }  // foreach loop projectTypeId
        }   // foreach loop projectId
    }

    public function getLedgerHeaderInfo($projectId, $branchId, $companyId)
    {
        $allLedgers = DB::table('acc_account_ledger')
            ->where('isGroupHead', 0)
            ->select('id', 'projectBranchId')
            ->get();

        $matchedId = array();

        foreach ($allLedgers as $singleLedger) {
            $splitArray = str_replace(array('[', ']', '"', ''), '', $singleLedger->projectBranchId);

            $splitArrayFirstValue = explode(",", $splitArray);
            $splitArraySecondValue = explode(":", $splitArrayFirstValue[0]);

            $array_length = substr_count($splitArray, ",");
            $arrayProjects = array();
            $temp = null;
            for ($i = 0; $i < $array_length + 1; $i++) {

                $splitArrayFirstValue = explode(",", $splitArray);

                $splitArraySecondValue = explode(":", $splitArrayFirstValue[$i]);
                $firstIndexValue = (int)$splitArraySecondValue[0];
                $secondIndexValue = (int)$splitArraySecondValue[1];

                if ($firstIndexValue == 0) {
                    if ($secondIndexValue == 0) {
                        array_push($matchedId, $singleLedger->id);
                    }
                } else {
                    // dd($projectId);
                    if ($firstIndexValue == $projectId) {
                        if ($secondIndexValue == 0) {
                            array_push($matchedId, $singleLedger->id);
                        } elseif ($secondIndexValue == $branchId) {
                            array_push($matchedId, $singleLedger->id);
                        }
                    }
                }
            }   //for
        }       //foreach

        $ledgers = DB::table('acc_account_ledger')
            ->whereIn('acc_account_ledger.id', $matchedId)
            ->where('companyIdFk', $companyId)
            ->select('id')
            ->get();
        // dd($ledgers);

        return $ledgers;
    }

    public function getVoucherInfo($projectTypeId, $projectId, $branchId, $companyId, $yearStartDate, $yearEndDate)
    {
        $voucherInfo = DB::table('acc_voucher')
            ->join('acc_voucher_details', 'acc_voucher_details.voucherId', '=', 'acc_voucher.id')
            ->where('voucherDate', '>=', $yearStartDate)
            ->where('voucherDate', '<=', $yearEndDate)
            ->where('companyId', $companyId)
            ->where('projectId', $projectId)
            ->where('branchId', $branchId)
            ->where('projectTypeId', $projectTypeId)
            ->select(
                'acc_voucher_details.debitAcc',
                'acc_voucher_details.creditAcc',
                'acc_voucher_details.amount',
                'acc_voucher.projectId',
                'acc_voucher.projectTypeId'
            )
            ->get();

        return $voucherInfo;
    }

    public function deleteYearEnd($yearEndId)
    {

        $yearEnd = AccYearEnd::find($yearEndId);

        $accOpeningBalance = DB::table('acc_opening_balance')
            ->where('branchId', $yearEnd->branchIdFk)
            ->where('openingDate', $yearEnd->date)
            ->delete();

        $yearEnd->delete();
    }

    public static function negativeReplace($value)
    {

        $replaced = preg_replace('/(-)([\d\.\,]+)/ui', '($2)', number_format($value, 2));

        return $replaced;
    }
}
