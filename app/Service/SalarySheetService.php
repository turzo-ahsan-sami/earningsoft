<?php

namespace App\Service;

use App\gnr\FiscalYear;
use App\hr\AdvancedSalaryLoan;
use App\hr\AdvancedSalaryLoanReceive;
use App\hr\BenefitType;
use App\hr\Edps;
use App\hr\EdpsReceive;
use App\hr\EpsReceive;
use App\hr\HrLeaveApplication;
use App\hr\HrLeaveTypeTag;
use App\hr\IncomeTax;
use App\hr\IncomeTaxReceive;
use App\hr\InsuranceReceive;
use App\hr\LateLeave;
use App\hr\OpeningBalanceFund;
use App\hr\OpeningBalanceLoanAdvancedSalary;
use App\hr\OpeningBalanceLoanPf;
use App\hr\OpeningBalanceLoanPfSchedule;
use App\hr\OpeningBalanceLoanVehicle;
use App\hr\OpeningBalanceLoanVehicleSchedule;
use App\hr\OsfReceive;
use App\hr\Other;
use App\hr\PfLoanReceive;
use App\hr\ProvidentFundLoan;
use App\hr\ProvidentFundLoanReview;
use App\hr\ProvidentFundLoanSchedule;
use App\hr\ProvidentFundReceive;
use App\hr\SalaryDeductionInformation;
use App\hr\SalaryDeductionReceive;
use App\hr\SalaryGenerate;
use App\hr\SalaryGenerateDueDetail;
use App\hr\SecurityMoney;
use App\hr\SecurityMoneyCollection;
use App\hr\StopSalaryBenefit;
use App\hr\VehicleLoan;
use App\hr\VehicleLoanReceive;
use App\hr\VehicleLoanSchedule;
use App\hr\WelfareFundReceive;
use App\Service\EasyCode;
use App\Service\Helper;
use App\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use \Carbon\Carbon;

/**
 * Salary Sheet Services
 *
 * @author hafij <hafij.to@gmail.com>
 *
 */
class SalarySheetService
{
    protected $easycode;

    public function __construct()
    {
        $this->easycode = new EasyCode();
    }

    public function checkingAttendence($request, $data)
    {
        // Checking existing data
        $data['modelAttendenceData'] = $data['attendenceModel']->select('hr_attendence.*')
            ->join('users', 'hr_attendence.users_id_fk', '=', 'users.id')->where(function ($q) use ($request) {
                if ($request['branchId'] != '') {
                    $q->where('users.branchId', '=', $request['branchId']);
                }

                if ($request['attendence_date'] != '') {
                    $q->where('hr_attendence.attendence_date', date("Y-m-d", strtotime($request['attendence_date'])));
                }
            })->orderby('hr_attendence.created_at', 'desc')->paginate(env('PAGE_SIZE'));

        if (count($data['modelAttendenceData']) > 0) {
            return $data['modelAttendenceData'];
        } else {
            return false;
        }
    }

    public function getWorkingDays()
    {
        return 30;
    }

    public function getPresentDays($user, $salaryTargetMonth, $request)
    {
        $presentDay = 30;

        $nplForLateLeave = 0;

        $organization = $user->employee->organization;

        $salaryMonth = date('Y-m', strtotime($salaryTargetMonth));
        $joiningMonth = date('Y-m', strtotime($organization->joining_date));
        $joiningDay = date('d', strtotime($organization->joining_date));

        if ($salaryMonth == $joiningMonth) {
            $presentDay = $presentDay - intval($joiningDay) + 1;
        }

        $getLastMonthGenerateDate = SalaryGenerate::where('company_id_fk', $request['company_id_fk'])
                ->where('project_id_fk', $request['project_id_fk'])
                ->where('branch_id_fk', $request['branchId'])
                ->orderby('id', 'desc')
                ->first()->created_at ?? null;

        $countLWOP = HrLeaveApplication::checkLwop(
            $user,
            $salaryTargetMonth,
            $getLastMonthGenerateDate,
            $request['created_date']
        );

        $nplLeaveTag = HrLeaveTypeTag::getBySlug('npl');
        $nplLeaveType = $nplLeaveTag->leaveTypeOfTheEmployee($user->employee);


        if ($nplLeaveType) {
            $lateLeave = LateLeave::join('hr_late_leave_details', 'hr_late_leaves.id', '=', 'hr_late_leave_details.late_leaves_id_fk')
                ->where('hr_late_leaves.month', date('Y-m-01', strtotime($salaryTargetMonth)))
                ->where('hr_late_leave_details.leave_type_id_fk', $nplLeaveType->id)
                ->where('hr_late_leaves.users_id_fk', $user->id)
                ->select('hr_late_leaves.*', 'hr_late_leave_details.day')
                ->first();
            // if($user->id == 1776){
            //     dd($nplLeaveType,$lateLeave,$salaryTargetMonth );
            // }
            $nplForLateLeave = $lateLeave->day ?? 0;
        }

        return intval($presentDay - $countLWOP - $nplForLateLeave);
    }

    public function saveEDPS($salaryTargetDate, $userId, $amount, $paymentStatus = 'Paid', $created_at, $transactionMonth = null)
    {
        if ($amount > 0) {

            $forMonth = date("Y-m-d", strtotime($salaryTargetDate));
            $tMonth = date("Y-m-01", strtotime($transactionMonth));

            $activeEdps = Edps::where('status', 'Active')
                ->where('users_id_fk', $userId)
                ->whereRaW('? BETWEEN `start_month` and `end_month`', [$forMonth])
                ->get();
            if (count($activeEdps) > 0) {
                foreach ($activeEdps as $aedps) {
                    $model = new EdpsReceive;
                    $chkExist = $model->where('edps_id_fk', $aedps->id)
                        ->where('users_id_fk', $userId)
                        ->where('for_month', $forMonth)
                        ->get();
                    if (count($chkExist) > 0) {
                        $model->where('edps_id_fk', $aedps->id)
                            ->where('users_id_fk', $userId)
                            ->where('for_month', $forMonth)
                            ->delete();
                    }
                    $model->edps_id_fk = $aedps->id;
                    $model->users_id_fk = $userId;
                    $model->transaction_date = date("Y-m-d", strtotime($created_at));
                    $model->amount = floatval($aedps->amount);
                    $model->fiscal_id_fk = @$this->easycode->getCurrentFiscalYear()['id'];
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
                    $model->transaction_month = $tMonth;
                    $model->payment_status = $paymentStatus;
                    if (floatval($model->amount) > 0) {
                        $model->save();
                        $this->checkSavePreviousEdps($aedps, $forMonth, $paymentStatus, $created_at);
                    }
                }
            }
        }
    }

    public function saveEPS($salaryTargetDate, $userId, $amount, $paymentStatus = 'Paid', $settingsEpsId, $created_at, $transactionMonth, $type = null)
    {
        if (floatval($amount) > 0) {
            $forMonth = date("Y-m-01", strtotime($salaryTargetDate));
            $tMonth = date("Y-m-01", strtotime($transactionMonth));
            $model = new EpsReceive();
            $model->settings_eps_id_fk = $settingsEpsId;
            $model->users_id_fk = $userId;
            $model->transaction_date = date("Y-m-d", strtotime($created_at));
            $model->transaction_month = $tMonth;
            $model->amount = floatval($amount);
            $model->fiscal_id_fk = FiscalYear::getCurrent()->id;
            $model->for_month = $forMonth;
            $model->received_branch_id = intval(User::find($userId)->branchId);
            $model->created_by = Auth::user()->id;
            $model->updated_at = date("Y-m-d H:i:s");
            $model->updated_by = Auth::user()->id;
            $model->payment_status = $paymentStatus;
            $model->type = $type ?? 'Regular';
            if (floatval($model->amount) > 0) {
                $model->save();
            }
        }
    }

    public function saveArearEps($arearRow, $created_at, $transactionMonth = null)
    {
        $data['otherSettings'] = Other::where('status', 1)->first();
        if ($data['otherSettings'] && $data['otherSettings']->eps_enable == 1) {
            if (count($arearRow) > 0) {
                foreach ($arearRow as $row) {
                    $user = User::find($row['user_id']);
                    $settingsEps = DB::table('hr_settings_eps')
                        ->where('grade_id_fk', $user->organization->grade)
                        ->whereRaw('FIND_IN_SET(?,recruitment_type_fk)', [$user->organization->recruitment_type])
                        ->orderby('effect_date', 'desc')
                        ->first();
                    if ($settingsEps) {
                        if (isset($row['eps'])) {
                            foreach ($row['eps'] as $epsList) {
                                foreach ($epsList as $month => $value) {
                                    $this->saveEPS(
                                        date('01-m-Y', strtotime($month)),
                                        $row['user_id'],
                                        $value,
                                        'Paid',
                                        $settingsEps->id,
                                        $created_at,
                                        $transactionMonth,
                                        'Arear'
                                    );
                                }
                            }
                        }
                    }
                }
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
                $chkExist = $model->where('edps_id_fk', $aedps->id)
                    ->where('users_id_fk', $userId)
                    ->where('for_month', $forMonth)
                    ->get();
                if ($chkExist->count() == 0) {
                    $model->edps_id_fk = $aedps->id;
                    $model->users_id_fk = $userId;
                    $model->transaction_date = date("Y-m-d", strtotime($created_at));
                    $model->amount = floatval($aedps->amount);
                    $model->fiscal_id_fk = @$this->easycode->getCurrentFiscalYear()['id'];
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

    public function getEdpsAmount($user, $salaryTargetDate)
    {
        /*if(isset($user->employee->organization->edps_amount)){
        return $user->employee->organization->edps_amount;
        }*/

        $this->adjustEdps($user->id, $salaryTargetDate);

        /* Check security deposit stop or not */
        if (StopSalaryBenefit::checkStopSalaryBenefit($user->id, $salaryTargetDate, 'EDPS')) {
            return 0;
        }

        $edps = Edps::select(\DB::raw('SUM(amount) as amount'))
            ->where('status', 'Active')
            ->where('users_id_fk', $user->id)
            ->whereRaW('? BETWEEN `start_month` and `end_month`', [date('Y-m-d', strtotime($salaryTargetDate))])->first();
        return $installment = floatval(@$edps->amount);

        /* Get total deposit money */
        $edpsReceived = EdpsReceive::select(\DB::raw('SUM(hr_edps_receive.amount) as amount'))
            ->leftJoin('hr_edps', 'hr_edps_receive.edps_id_fk', '=', 'hr_edps.id')
            ->where('hr_edps.status', '=', 'Active')
            ->where('hr_edps_receive.users_id_fk', $user->id)
            ->whereRaW('? BETWEEN `hr_edps`.`start_month` and `hr_edps`.`end_month`', [date('Y-m-d', strtotime($salaryTargetDate))])
            ->first();
        $totalDeposited = floatval(@$edpsReceived->amount);

        $opFund = OpeningBalanceFund::where('users_id_fk', $user->id)->first();
        if (count($opFund) > 0) {
            $totalDeposited += $opFund->edps;
        }

        /* Get total deposit money */

        /* Get total expected money */
        $activeEdps = Edps::where('status', 'Active')
            ->where('users_id_fk', $user->id)
            ->whereRaW('? BETWEEN `start_month` and `end_month`', [date('Y-m-d', strtotime($salaryTargetDate))])->get();
        $acpetedAmount = 0;
        if (count($activeEdps) > 0) {
            foreach ($activeEdps as $aedps) :
                $start_month = date("Y-m-d", strtotime($aedps->start_month));
                $cur_month = date("Y-m-10", strtotime('10-' . $salaryTargetDate));
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

    public function getSecurityAmount($user, $salaryMonth)
    {
        $securityMoney = $installment = 0;

        $forMonth = date("Y-m-d", strtotime($salaryMonth));

        $this->adjustSecurityAmount($user->emp_id_fk, $forMonth);

        if (StopSalaryBenefit::checkStopSalaryBenefit($user->id, $salaryMonth, 'Security Deposit')) {
            return 0;
        }

        $organization = $user->employee->organization;

        $securityMoney = floatval(
            SecurityMoney::getSecurityMoney(
                $organization->company_id_fk,
                $organization->project_id_fk,
                $organization->grade,
                $organization->level_id_fk
            )
        );

        $installment = floatval($organization->installment_amount);

        if ($user->emp_id_fk > 0) {
            $totalDeposit = 0;

            $opFund = OpeningBalanceFund::where('users_id_fk', $user->id)->first();
            if ($opFund) {
                $totalDeposit += $opFund->security_deposit;
            }

            $collection = SecurityMoneyCollection::select(\DB::raw('SUM(amount) as amount'))->where('emp_id_fk', $user->emp_id_fk)->groupby('emp_id_fk')->first();
            if (isset($collection->amount)) {
                $totalDeposit += $collection->amount;
            }
        } else {
            $totalDeposit = 0;
        }

        $balance = floatval($securityMoney) - floatval($totalDeposit);

        $joining_date = $organization->joining_date;
        if ($joining_date != '0000-00-00') {
            $joining_date = date("Y-m-01", strtotime($joining_date));
            $cur_month = date("Y-m-10", strtotime($salaryMonth));
            $totalMonth = Carbon::parse($joining_date)->diffInMonths(Carbon::parse($cur_month));
            $actualDeposit = floatval($installment * $totalMonth);
            if ($actualDeposit > floatval($securityMoney)) {
                $actualDeposit = floatval($securityMoney);
            }
        } else {
            $actualDeposit = 0;
        }

        $dueDeposit = $actualDeposit - floatval($totalDeposit);

        if ($dueDeposit > 0 && $dueDeposit < $installment) {
            return round($dueDeposit);
        } else if ($balance > 0 && $balance >= $installment) {
            return round($installment);
        } else {
            return round($balance);
        }
    }

    public function saveSecurityMoney($salaryTargetDate, $employeeId, $amount, $paymentStatus = 'Paid', $transactionMonth)
    {
        $forMonth = date("Y-m-01", strtotime($salaryTargetDate));
        $model = new SecurityMoneyCollection;
        $model->emp_id_fk = $employeeId;
        $model->type = 'Installment';
        $model->amount = floatval($amount);
        $model->for_month = $forMonth;
        $model->transaction_month = $transactionMonth;
        $model->received_branch_id = User::where('emp_id_fk', $employeeId)->first()->branchId;
        $model->created_by = Auth::user()->id;
        $model->updated_at = date("Y-m-d H:i:s");
        $model->updated_by = Auth::user()->id;
        $model->received_by = Auth::user()->id;
        $model->received_at = date("Y-m-d");
        $model->payment_status = $paymentStatus;
        if ($model->amount > 0) {
            $model->save();
        }
    }

    public function getAdvancedSalaryLoanAmount($user, $salaryMonth)
    {

        $lastDateMonth = date("Y-m-t", strtotime($salaryMonth));
        $forMonth = date("Y-m-d", strtotime($salaryMonth));
        $this->adjustAdvancedSalaryLoanAmount($user->id, $lastDateMonth, $forMonth);


        // Check security deposit stop or not
        if (StopSalaryBenefit::checkStopSalaryBenefit($user->id, $salaryMonth, 'Advanced Salary Loan')) {
            return 0;
        }

        $loanAmount = 0;

        $vl = AdvancedSalaryLoan::where('users_id_fk', $user->id)->where('status', 'Approved')
            ->where('approved_installment_start_month', '<=', $lastDateMonth)
            ->get();


        foreach ($vl as $ld) {

            $chkExist = AdvancedSalaryLoanReceive::where('type', 'Regular')
                ->where('advanced_salary_loan_fk', $ld->id)
                ->where('users_id_fk', $user->id)
                ->where('transaction_date', $forMonth);


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
            $qtdAmount = AdvancedSalaryLoanReceive::select(\DB::raw('SUM(amount) as amount'))
                ->where('advanced_salary_loan_fk', $ld->id)
                ->where('users_id_fk', $user->id)
                ->where(function ($q) {
                    $q->orWhere('type', 'Regular');
                    $q->orWhere('type', 'Advanced');
                })->sum('amount');

            /* get total deposited money */
            $balance = $expectedMoney - floatval($qtdAmount);

            if ($balance < $ld->approved_installment) {
                $loanAmount += $balance;
            } else {
                $loanAmount += $ld->approved_installment;
            }

            // Advance salary loan amount dont add to salary sheet
            // $loanAmount += floatval($this->checkSavePreviousAdvancedSalaryLoan($ld, $forMonth, 'Regular', 'Paid', 'return'));


        }


        $op = OpeningBalanceLoanAdvancedSalary::where('users_id_fk', $user->id)->get();
        if (count($op) > 0) {
            foreach ($op as $opv) :

                $chkExist = AdvancedSalaryLoanReceive::where('type', 'Opening Balance')->where('advanced_salary_loan_fk', $opv->id)->where('users_id_fk', $user->id)->where('transaction_date', $forMonth);
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
                $qtd = AdvancedSalaryLoanReceive::select(\DB::raw('SUM(amount) as amount'))->where('type', 'Opening Balance')->where('advanced_salary_loan_fk', $opv->id)->where('users_id_fk', $user->id)->first();
                /* get total deposited money */

                $balance = $expectedMoney - floatval(@$qtd->amount);

                if ($balance < $opv->approved_installment) {
                    $loanAmount += $balance;
                } else {
                    $loanAmount += $opv->approved_installment;
                }

                // Advanced salary loan amount dont add to salary sheet
                // $loanAmount += floatval($this->checkSavePreviousAdvancedSalaryLoan($opv, $forMonth, 'Opening Balance', 'Paid', 'return'));
            endforeach;
        }

        return round($loanAmount);
    }

    public function saveAdvancedSalaryLoanAmount($user, $salaryTargetDate, $paymentStatus = 'Paid', $created_at, $transactionMonth)
    {

        if (StopSalaryBenefit::checkStopSalaryBenefit($user->id, $salaryTargetDate, 'Advanced Salary Loan')) {
            return 0;
        }

        $lastDateMonth = date("Y-m-t", strtotime($salaryTargetDate));
        $forMonth = date("Y-m-d", strtotime($salaryTargetDate));

        $vl = AdvancedSalaryLoan::where('users_id_fk', $user->id)
            ->where('status', 'Approved')
            ->where('approved_installment_start_month', '<=', $lastDateMonth)
            ->get();

        if ($vl->count() > 0) {
            foreach ($vl as $ld) {
                $loanAmount = 0;
                $start_month = date("Y-m-01", strtotime($ld->approved_installment_start_month));
                $cur_month = date("Y-m-10", strtotime($forMonth));
                $totalMonth = Carbon::parse($start_month)->diffInMonths(Carbon::parse($cur_month));
                $expectedMoney = floatval($ld->approved_amount);

                $qtd = AdvancedSalaryLoanReceive::select(\DB::raw('SUM(amount) as amount'))
                    ->where('advanced_salary_loan_fk', $ld->id)
                    ->where('users_id_fk', $user->id)
                    ->where(function ($q) {
                        $q->orWhere('type', 'Regular');
                        $q->orWhere('type', 'Advanced');
                    })->first();

                $balance = $expectedMoney - floatval(@$qtd->amount);

                if ($balance < $ld->approved_installment) {
                    $loanAmount += $balance;
                } else {
                    $loanAmount += $ld->approved_installment;
                }

                $model = new AdvancedSalaryLoanReceive;
                $model->advanced_salary_loan_fk = $ld->id;
                $model->users_id_fk = $user->id;
                $model->transaction_date = $forMonth;
                $model->amount = floatval($loanAmount);
                $model->fiscal_id_fk = @$this->easycode->getCurrentFiscalYear()['id'];
                $model->for_month = $forMonth;
                $model->type = 'Regular';
                $model->received_branch_id = $user->branchId;
                $model->created_by = Auth::user()->id;
                $model->updated_at = date("Y-m-d H:i:s");
                $model->updated_by = Auth::user()->id;
                $model->approved_by = Auth::user()->id;
                $model->approved_at = date("Y-m-d H:i:s");
                $model->payment_status = $paymentStatus;
                $model->generate_date = $created_at;
                if (floatval($model->amount) > 0) {
                    $model->save();
                }

                // Advanced salary dont save when generate salary
                /*$this->checkSavePreviousAdvancedSalaryLoan(
                    $ld,
                    $forMonth,
                    'Regular',
                    $paymentStatus,
                    'save',
                    $created_at
                );*/
            }

            foreach ($vl as $ld) {
                $noOfCompletedInstallment = AdvancedSalaryLoanReceive::where('type', 'Regular')
                    ->where('advanced_salary_loan_fk', $ld->id)
                    ->get();

                $nvl = AdvancedSalaryLoan::find($ld->id);
                $nvl->completed_no_of_installment = count($noOfCompletedInstallment);
                $nvl->save();
            }
        }

        $op = OpeningBalanceLoanAdvancedSalary::where('users_id_fk', $user->id)->get();
        if ($op->count() > 0) {
            foreach ($op as $ld) {
                $loanAmount = 0;
                $start_month = date("Y-m-d", strtotime($ld->approved_installment_start_month));
                $cur_month = date("Y-m-10", strtotime($forMonth));
                $totalMonth = Carbon::parse($start_month)->diffInMonths(Carbon::parse($cur_month));
                $expectedMoney = floatval($ld->ob_amount);

                $qtd = AdvancedSalaryLoanReceive::select(\DB::raw('SUM(amount) as amount'))
                    ->where('type', 'Opening Balance')
                    ->where('advanced_salary_loan_fk', $ld->id)
                    ->where('users_id_fk', $user->id)
                    ->first();

                $balance = $expectedMoney - floatval(@$qtd->amount);

                if ($balance < $ld->approved_installment) {
                    $loanAmount += $balance;
                } else {
                    $loanAmount += $ld->approved_installment;
                }

                $model = new AdvancedSalaryLoanReceive;
                $model->advanced_salary_loan_fk = $ld->id;
                $model->users_id_fk = $user->id;
                $model->transaction_date = $forMonth;
                $model->amount = floatval($loanAmount);
                $model->fiscal_id_fk = @$this->easycode->getCurrentFiscalYear()['id'];
                $model->for_month = $forMonth;
                $model->type = 'Opening Balance';
                $model->received_branch_id = $user->branchId;
                $model->created_by = Auth::user()->id;
                $model->updated_at = date("Y-m-d H:i:s");
                $model->updated_by = Auth::user()->id;
                $model->approved_by = Auth::user()->id;
                $model->approved_at = date("Y-m-d H:i:s");
                $model->payment_status = $paymentStatus;
                $model->generate_date = $created_at;
                if (floatval($model->amount) > 0) {
                    $model->save();
                }

                // Advanced salary dont save when generate salary
               /* $this->checkSavePreviousAdvancedSalaryLoan(
                    $ld,
                    $forMonth,
                    'Opening Balance',
                    $paymentStatus,
                    'save',
                    $created_at
                );*/
            }

            foreach ($op as $ld) {
                $noOfCompletedInstallment = AdvancedSalaryLoanReceive::where('type', 'Opening Balance')
                    ->where('advanced_salary_loan_fk', $ld->id)
                    ->get();

                $nvl = OpeningBalanceLoanAdvancedSalary::find($ld->id);
                $nvl->no_of_installment_completed = $nvl->no_of_installment_completed + count($noOfCompletedInstallment);
                $nvl->save();
            }
        }
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

                $totalPaid = AdvancedSalaryLoanReceive::select(\DB::raw('SUM(amount) as amount'))
                    ->where('type', $type)->where('advanced_salary_loan_fk', $aedps->id)->first();

                $forMonth = date("Y-m-d", $month);

                if ($totalDue > $totalPaid->amount) {
                    $model = new AdvancedSalaryLoanReceive;
                    $chkExist = $model->where('type', $type)->where('advanced_salary_loan_fk', $aedps->id)
                        ->where('users_id_fk', $userId)->where('for_month', $forMonth)
                        ->get();
                    if (count($chkExist) == 0) {
                        $model->advanced_salary_loan_fk = $aedps->id;
                        $model->users_id_fk = $userId;
                        $model->transaction_date = date("Y-m-d", strtotime($created_at));
                        $returnAmount += floatval($aedps->approved_installment);
                        $model->amount = floatval($aedps->approved_installment);
                        $model->fiscal_id_fk = @$this->easycode->getCurrentFiscalYear()['id'];
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

    public function getIncomeTax($user, $salaryMonth)
    {
        $lastDateMonth = date("Y-m-t", strtotime($salaryMonth));
        $forMonth = date("Y-m-d", strtotime($salaryMonth));
        $transactionMonth = date("Y-m-d", strtotime($salaryMonth));

        // dd($lastDateMonth);
        $this->adjustIncomeTax($user->id, $lastDateMonth, $forMonth);

        /* Check security deposit stop or not */
        if (StopSalaryBenefit::checkStopSalaryBenefit($user->id, $salaryMonth, 'Income Tax')) {
            return 0;
        }

        // dd($forMonth);
        $vl = IncomeTax::where('users_id_fk', $user->id)
            ->where('status', 'Approved')
            ->where('acpected_installment_start_month', '<=', $lastDateMonth)
            ->get();

        $loanAmount = 0;
        if ($vl->count() > 0) {
            foreach ($vl as $ld) :

                $chkExist = IncomeTaxReceive::where('income_tax_id', $ld->id)
                    ->where('users_id_fk', $user->id)
                    ->where('transaction_date', $forMonth);

                if ($chkExist->get()->count() > 0) {
                    $chkExist->delete();
                }

                // dd($chkExist->get());

                /* get total expected money*/
                $start_month = date("Y-m-01", strtotime($ld->acpected_installment_start_month));
                $cur_month = date("Y-m-10", strtotime($forMonth));
                $totalMonth = Carbon::parse($start_month)->diffInMonths(Carbon::parse($cur_month));
                $expectedMoney = floatval($ld->acpected_amount);
                /* get total expected money*/

                /* get total deposited money */
                $qtd = IncomeTaxReceive::select(\DB::raw('SUM(amount) as amount'))
                    ->where('income_tax_id', $ld->id)
                    ->where('users_id_fk', $user->id)
                    ->first();
                /* get total deposited money */
                $balance = $expectedMoney - floatval(@$qtd->amount);

                if ($balance < $ld->acpected_installment) {
                    $loanAmount += $balance;
                } else {
                    $loanAmount += $ld->acpected_installment;
                }

                $loanAmount += floatval($this->checkSavePreviousIncomeTax($ld, $forMonth, $transactionMonth, 'Paid', 'return'));

            endforeach;
        }
        return round($loanAmount);
    }

    public function saveIncomeTax($user, $salaryTargetDate, $paymentStatus = 'Paid', $created_at, $transactionMonth)
    {
        if (StopSalaryBenefit::checkStopSalaryBenefit($user->id, $salaryTargetDate, 'Income Tax')) {
            return 0;
        }

        $lastDateMonth = date("Y-m-t", strtotime($salaryTargetDate));
        $forMonth = date("Y-m-d", strtotime($salaryTargetDate));

        $vl = IncomeTax::where('users_id_fk', $user->id)
            ->where('status', 'Approved')
            ->where('acpected_installment_start_month', '<=', $lastDateMonth)
            ->get();

        if ($vl->count() > 0) {
            foreach ($vl as $ld) {
                $loanAmount = 0;
                $start_month = date("Y-m-01", strtotime($ld->acpected_installment_start_month));
                $cur_month = date("Y-m-10", strtotime($forMonth));
                $totalMonth = Carbon::parse($start_month)->diffInMonths(Carbon::parse($cur_month));
                $expectedMoney = floatval($ld->acpected_amount);

                $qtd = IncomeTaxReceive::select(\DB::raw('SUM(amount) as amount'))
                    ->where('income_tax_id', $ld->id)
                    ->where('users_id_fk', $user->id)
                    ->first();

                $balance = $expectedMoney - floatval(@$qtd->amount);

                if ($balance < $ld->acpected_installment) {
                    $loanAmount += $balance;
                } else {
                    $loanAmount += $ld->acpected_installment;
                }

                $model = new IncomeTaxReceive;
                $model->income_tax_id = $ld->id;
                $model->users_id_fk = $user->id;
                $model->transaction_date = date("Y-m-d", strtotime($created_at));
                $model->amount = $loanAmount;
                $model->fiscal_id_fk = @$this->easycode->getCurrentFiscalYear()['id'];
                $model->for_month = $forMonth;
                $model->transaction_month = $transactionMonth;
                $model->created_at = $created_at
                    ? date("Y-m-d H:i:s", strtotime($created_at))
                    : date("Y-m-d H:i:s");
                $model->created_by = Auth::user()->id;
                $model->updated_at = date("Y-m-d H:i:s");
                $model->updated_by = Auth::user()->id;
                $model->payment_status = $paymentStatus;
                if (floatval($model->amount) > 0) {
                    $model->save();
                }
                $this->checkSavePreviousIncomeTax(
                    $ld,
                    $forMonth,
                    $transactionMonth,
                    $paymentStatus,
                    'save',
                    $created_at
                );
            }
        }
    }

    public function checkSavePreviousIncomeTax($aedps, $for_Month, $transactionMonth, $paymentStatus = 'Paid', $rtype = 'save', $created_at = null)
    {
        $returnAmount = 0;
        $start_month = $month = strtotime($aedps->acpected_installment_start_month);
        $end_month = strtotime($for_Month);
        $userId = $aedps->users_id_fk;

        if ($start_month <= $end_month) {
            while ($month < $end_month) {

                $totalDue = $aedps->acpected_amount;

                $totalPaid = IncomeTaxReceive::select(\DB::raw('SUM(amount) as amount'))
                    ->where('income_tax_id', $aedps->id)
                    ->first();

                $forMonth = date("Y-m-d", $month);

                if ($totalDue > $totalPaid->amount) {
                    $model = new IncomeTaxReceive;
                    $chkExist = $model->where('income_tax_id', $aedps->id)->where('users_id_fk', $userId)->where('for_month', $forMonth)->get();
                    if (count($chkExist) == 0) {
                        $model->income_tax_id = $aedps->id;
                        $model->users_id_fk = $userId;
                        $model->transaction_date = date("Y-m-d", strtotime($created_at));
                        $returnAmount += floatval($aedps->acpected_installment);
                        $model->amount = floatval($aedps->acpected_installment);
                        $model->fiscal_id_fk = @$this->easycode->getCurrentFiscalYear()['id'];
                        $model->for_month = $forMonth;
                        $model->transaction_month = $transactionMonth;
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

    public function saveArearPf($arearRow, $created_at, $transactionMonth = null)
    {
        if (count($arearRow) > 0) {
            foreach ($arearRow as $row) {
                if (isset($row['pf_org']) || isset($row['pf_self'])) {
                    foreach ($row['pf_org'] as $i => $pfOrgList) {
                        foreach ($pfOrgList as $month => $value) {
                            $this->savePF(
                                date('01-m-Y', strtotime($month)),
                                $row['user_id'], $value,
                                $row['pf_self'][$i][$month],
                                'Arear',
                                $row['payment_status'],
                                $created_at,
                                $transactionMonth
                            );
                        }
                    }
                }
            }
        }
    }

    public function saveArearInsurance($arearRow, $created_at, $transactionMonth = null)
    {
        if (count($arearRow) > 0) {
            foreach ($arearRow as $row) {
                if (isset($row['insurance_org']) || isset($row['insurance_self'])) {
                    foreach ($row['insurance_org'] as $i => $insuranceOrgList) {
                        foreach ($insuranceOrgList as $month => $value) {
                            $this->saveInsurance(
                                date('01-m-Y', strtotime($month)),
                                $row['user_id'], $value,
                                floatval(@$row['insurance_self'][$i][$month]),
                                'Arear', $row['payment_status'],
                                $created_at,
                                $transactionMonth
                            );
                        }
                    }
                }
            }
        }
    }

    public function saveArearOsf($arearRow, $created_at, $transactionMonth = null)
    {
        if (count($arearRow) > 0) {
            foreach ($arearRow as $row) {
                if (isset($row['osf_org']) || isset($row['osf_self'])) {
                    foreach ($row['osf_org'] as $i => $osfOrgList) {
                        foreach ($osfOrgList as $month => $value) {
                            $this->saveOsf(
                                date('d-m-Y', strtotime($month)),
                                $row['user_id'],
                                $value,
                                floatval(@$row['osf_self'][$i][$month]),
                                'Arear',
                                $row['payment_status'],
                                $created_at,
                                $transactionMonth
                            );
                        }
                    }
                }
            }
        }
    }

    public function savePF($salaryTargetDate, $userId, $orgAmount, $selfAmount, $type = 'Regular', $paymentStatus = 'Paid', $generateDate, $transactionMonth = null)
    {
        $model = new ProvidentFundReceive;

        $forMonth = date("Y-m-d", strtotime($salaryTargetDate));
        $tMonth = date("Y-m-01", strtotime($transactionMonth));

        $chkExist = $model->where('users_id_fk', $userId)
            ->where('for_month', $forMonth)
            ->where('type', $type)
            ->get();

        if ($chkExist->count() > 0) {
            $model->where('users_id_fk', $userId)
                ->where('for_month', $forMonth)
                ->where('type', $type)
                ->delete();
        }
        $model->users_id_fk = $userId;
        $model->transaction_date = date("Y-m-d", strtotime($generateDate));
        $model->org_amount = floatval($orgAmount);
        $model->emp_amount = floatval($selfAmount);
        $model->total_amount = $model->org_amount + $model->emp_amount;
        $model->fiscal_id_fk = FiscalYear::getCurrent()->id ?? 0;
        $model->for_month = $forMonth;
        $model->transaction_month = $tMonth;
        $model->type = $type;
        $model->received_branch_id = User::find($userId)->branchId;
        $model->payment_status = $paymentStatus;
        $model->created_by = Auth::user()->id;
        $model->updated_at = date("Y-m-d H:i:s");
        $model->updated_by = Auth::user()->id;
        if ($model->total_amount > 0) {
            $model->save();
        }
    }

    public function saveInsurance($salaryTargetDate, $userId, $orgAmount, $selfAmount, $type = 'Regular', $paymentStatus = 'Paid', $created_at, $transactionMonth = null)
    {
        $forMonth = date("Y-m-01", strtotime($salaryTargetDate));
        $tMonth = date("Y-m-01", strtotime($transactionMonth));
        $model = new InsuranceReceive;
        $chkExist = $model->where('users_id_fk', $userId)->where('for_month', $forMonth)->where('type', $type)->get();
        if (count($chkExist) > 0) {
            $model->where('users_id_fk', $userId)->where('for_month', $forMonth)->where('type', $type)->delete();
        }
        $model->users_id_fk = $userId;
        $model->transaction_date = date("Y-m-d", strtotime($created_at));
        $model->transaction_month = $tMonth;
        $model->org_amount = floatval($orgAmount);
        $model->emp_amount = floatval($selfAmount);
        $model->total_amount = $model->org_amount + $model->emp_amount;
        $model->fiscal_id_fk = @$this->easycode->getCurrentFiscalYear()['id'];
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

    public function saveOsf($salaryTargetDate, $userId, $orgAmount, $selfAmount, $type = 'Regular', $paymentStatus = 'Paid', $created_at, $transactionMonth = null)
    {
        $forMonth = date("Y-m-d", strtotime($salaryTargetDate));
        $tMonth = date("Y-m-01", strtotime($transactionMonth));

        $model = new OsfReceive;
        $chkExist = $model->where('users_id_fk', $userId)->where('for_month', $forMonth)->where('type', $type)->get();
        if (count($chkExist) > 0) {
            $model->where('users_id_fk', $userId)->where('for_month', $forMonth)->where('type', $type)->delete();
        }
        $model->users_id_fk = $userId;
        $model->transaction_date = date("Y-m-d", strtotime($created_at));
        $model->transaction_month = $tMonth;
        $model->org_amount = floatval($orgAmount);
        $model->emp_amount = floatval($selfAmount);
        $model->total_amount = $model->org_amount + $model->emp_amount;
        $model->fiscal_id_fk = @$this->easycode->getCurrentFiscalYear()['id'];
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

    public function saveArearWf($arearRow, $created_at, $transactionMonth = null)
    {
        if (count($arearRow) > 0) {
            $otherSettings = Other::where('status', 1)->first();
            foreach ($arearRow as $row) {
                $user = User::find($row['user_id']);
                if ($user->employee->organization->wf_active == 1 && $otherSettings && $otherSettings->wf_enable == '1') {
                    if (isset($row['wf_org']) || isset($row['wf_self']) || isset($row['wf_contri_self'])) {
                        foreach ($row['wf_org'] as $i => $wfOrgList) {
                            foreach ($wfOrgList as $month => $value) {
                                $this->saveWF(
                                    date('Y-m-01', strtotime($month)),
                                    $row['user_id'],
                                    $value,
                                    floatval(@$row['wf_self'][$i][$month]),
                                    floatval(@$row['wf_contri_self'][$i][$month]),
                                    'Arear',
                                    $row['payment_status'],
                                    $created_at,
                                    $transactionMonth
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    public function saveWF($salaryTargetMonth, $userId, $orgAmount, $selfAmount, $selfContriAmount, $type = 'Regular', $status = 'Paid', $salaryGenerateDate, $salaryTransactionMonth)
    {
        $tMonth = date("Y-m-01", strtotime($salaryTransactionMonth));
        $transactionDate = date("Y-m-d", strtotime($salaryGenerateDate));
        $salaryTargetMonth = date("Y-m-01", strtotime($salaryTargetMonth));

        $user = User::find($userId);

        $totalAmount = $orgAmount + $selfContriAmount + $selfAmount;

        $model = new WelfareFundReceive;
        $model->users_id_fk = $user->id;
        $model->transaction_date = $transactionDate;
        $model->org_contri_amount = floatval($orgAmount);
        $model->emp_contri_amount = floatval($selfContriAmount);
        $model->emp_amount = floatval($selfAmount);
        $model->total_amount = $totalAmount;
        $model->fiscal_id_fk = FiscalYear::getCurrent()->id ?? 0;
        $model->for_month = $salaryTargetMonth;
        $model->transaction_month = $tMonth;
        $model->type = $type;
        $model->received_branch_id = $user->branchId;
        $model->created_by = Auth::user()->id;
        $model->updated_by = Auth::user()->id;
        $model->payment_status = $status;

        if ($totalAmount > 0) {
            $model->save();
        }
    }

    public function getPfLoanAmount($user, $salaryMonth)
    {
        $loanAmount = 0;

        $forMonth = date("Y-m-t", strtotime('01-' . $salaryMonth));

        $tdate = date("Y-m-d", strtotime('01-' . $salaryMonth));

        $this->adjustPfLoanAmount($user->id, $tdate, $forMonth);

        if (StopSalaryBenefit::checkStopSalaryBenefit($user->id, '01-' . $salaryMonth, 'PF Loan')) {
            return 0;
        }

        $vl = ProvidentFundLoan::select('t1.*', 't2.id as review_id', 't2.inst_amount', 't2.completed_no_of_installment')
            ->from('hr_pf_loan as t1')
            ->where('t1.users_id_fk', $user->id)
            ->where('t1.status', 'Approved')
            ->where('t1.settlement_type', '!=', 'Settlement')
            ->where('t1.settlement_type', '!=', 'Return')
            ->whereRaw('t2.completed_no_of_installment != t2.no_of_inst')
            ->where('t2.inst_start_month', '<=', $forMonth)
            ->join('hr_pf_loan_review as t2', 't2.pf_loan_id_fk', '=', 't1.id')
            ->get();
//         if($user->id == 321){
//             dd($vl);
//         }

        if ($vl->count() > 0) {
            foreach ($vl as $ld) {
                ProvidentFundLoanSchedule::where('pf_loan_review_fk', $ld->review_id)
                    ->where('transaction_date', $tdate)
                    ->update(['payment_status' => 'Unpaid', 'transaction_date' => '0000-00-00']);

                $pfReExists = PfLoanReceive::where('pf_loan_fk', $ld->id)
                    ->where('type', 'Regular')
                    ->where('users_id_fk', $user->id)
                    ->where('transaction_date', $tdate);

                if ($pfReExists->count() > 0) {
                    $pfReExists->delete();
                }

                $schedule = ProvidentFundLoanSchedule::select(\DB::raw('sum(total_payment) as total_payment'))
                    ->where('payment_date', '<=', $forMonth)
                    ->where('payment_status', '=', 'Unpaid')
                    ->where('pf_loan_review_fk', '=', $ld->loanReview->id)->first();
                $loanAmount += $schedule->total_payment;
            }
        }

        // Opening Balance
        $op = OpeningBalanceLoanPf::where('users_id_fk', $user->id)->where('installment_start_date', '<=', $forMonth)->get();
        if ($op->count() > 0) {
            foreach ($op as $oppf) {

                OpeningBalanceLoanPfSchedule::where('ob_loan_pf_fk', $oppf->id)
                    ->where('transaction_date', $tdate)
                    ->update(['payment_status' => 'Unpaid', 'transaction_date' => '0000-00-00']);

                $pfReExists = PfLoanReceive::where('pf_loan_fk', $oppf->id)
                    ->where('type', 'Opening Balance')
                    ->where('users_id_fk', $user->id)
                    ->where('transaction_date', $tdate);

                if ($pfReExists->count() > 0) {
                    $pfReExists->delete();
                }

                $schedule = OpeningBalanceLoanPfSchedule::select(\DB::raw('sum(total_payment) as total_payment'))
                    ->where('payment_date', '<=', $forMonth)
                    ->where('payment_status', '=', 'Unpaid')
                    ->where('ob_loan_pf_fk', '=', $oppf->id)
                    ->first();

                $loanAmount += $schedule->total_payment;
            }
        }

        // if ($user->id == 780) {
        //     dd($loanAmount, $forMonth, $tdate);
        // }

        return round($loanAmount);
    }

    public function savePfLoanAmount($user, $salaryTargetDate, $paymentStatus = 'Paid', $created_at = null)
    {
        // Check pf loan amount stop or not
        if (StopSalaryBenefit::checkStopSalaryBenefit($user->id, '01-' . $salaryTargetDate, 'PF Loan')) {
            return 0;
        }

        $lastDateMonth = date("Y-m-t", strtotime($salaryTargetDate));
        $forMonth = date("Y-m-d", strtotime($salaryTargetDate));

        $vl = ProvidentFundLoan::select('t1.*', 't2.inst_amount', 't2.completed_no_of_installment')
            ->from('hr_pf_loan as t1')
            ->where('t1.users_id_fk', $user->id)
            ->where('t1.status', 'Approved')
            ->where('t1.settlement_type', '!=', 'Settlement')
            ->where('t1.settlement_type', '!=', 'Return')
            ->whereRaw('t2.completed_no_of_installment != t2.no_of_inst')
            ->where('t2.inst_start_month', '<=', $lastDateMonth)
            ->join('hr_pf_loan_review as t2', 't2.pf_loan_id_fk', '=', 't1.id')
            ->get();

        if ($vl->count() > 0) {
            foreach ($vl as $ld) {

                $schedule = ProvidentFundLoanSchedule::where('payment_date', '<=', $lastDateMonth)
                    ->where('payment_status', '=', 'Unpaid')
                    ->where('pf_loan_review_fk', '=', $ld->loanReview->id);

                if ($schedule->get()->count() > 0) {
                    foreach ($schedule->get() as $sch) {
                        $model = new PfLoanReceive;
                        $model->pf_loan_fk = $ld->id;
                        $model->users_id_fk = $user->id;
                        // $model->transaction_date = date("Y-m-d", strtotime($created_at));
                        $model->transaction_date = $forMonth;
                        $model->amount = round($sch->total_payment);
                        $model->principal_amount = floatval($sch->principal);

                        $model->interest_amount = floatval($sch->interest);
                        $model->fiscal_id_fk = @$this->easycode->getCurrentFiscalYear()['id'];
                        $model->for_month = date('Y-m-d', strtotime($sch->payment_date));
                        $model->received_branch_id = $user->branchId;
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
                        $model->generate_date = $created_at;
                        if ($model->amount > 0) {
                            $model->save();
                            $schPaid = ProvidentFundLoanSchedule::find($sch->id);
                            $schPaid->payment_status = 'Paid';
                            $schPaid->transaction_date = $model->transaction_date;
                            $schPaid->generate_date = $model->generate_date;
                            $schPaid->save();
                        }

                        // Count no of complete installment
                        $noOfCompletedInstallment = PfLoanReceive::where('pf_loan_fk', $ld->id)->where('type', 'Regular');

                        // Update no of completed installment
                        $nvl = ProvidentFundLoanReview::where('pf_loan_id_fk', $ld->id)->first();
                        $nvl->completed_no_of_installment = $noOfCompletedInstallment->count();
                        $nvl->save();
                    }
                }
            }
        }

        // Opening Balance
        $op = OpeningBalanceLoanPf::where('users_id_fk', $user->id)
            ->where('installment_start_date', '<=', $lastDateMonth)
            ->get();

        if ($op->count() > 0) {

            foreach ($op as $oppf) {
                $schedule = OpeningBalanceLoanPfSchedule::where('payment_date', '<=', $lastDateMonth)
                    ->where('payment_status', '=', 'Unpaid')
                    ->where('ob_loan_pf_fk', '=', $oppf->id);

                if ($schedule->count() > 0) {
                    foreach ($schedule->get() as $sch) {
                        $model = new PfLoanReceive;
                        $model->pf_loan_fk = $oppf->id;
                        $model->users_id_fk = $user->id;
                        $model->transaction_date = $forMonth;
                        $model->amount = floatval($sch->total_payment);
                        $model->principal_amount = floatval($sch->principal);
                        $model->interest_amount = floatval($sch->interest);
                        $model->fiscal_id_fk = @$this->easycode->getCurrentFiscalYear()['id'];
                        $model->for_month = date('Y-m-d', strtotime($sch->payment_date));
                        $model->received_branch_id = $user->branchId;
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
                        $model->generate_date = $created_at;
                        if ($model->amount > 0) {
                            $model->save();
                            $schPaid = OpeningBalanceLoanPfSchedule::find($sch->id);
                            $schPaid->payment_status = 'Paid';
                            $schPaid->transaction_date = date('Y-m-d', strtotime($model->transaction_date));
                            $schPaid->generate_date = date('Y-m-d', strtotime($model->generate_date));
                            $schPaid->save();
                        }
                    }
                }
            }
        }
    }

    public function getVehicleLoanAmount($user, $salaryMonth)
    {

        $forMonth = date("Y-m-01", strtotime($salaryMonth));

        $tdate = date("Y-m-d", strtotime($salaryMonth));

        $this->adjustVehicleLoanAmount($user->id, $salaryMonth, $forMonth);

        // REGULAR LOAN
        $vl = VehicleLoan::where('users_id_fk', $user->id)->where('status', 'Approved')
            ->whereRaw('completed_no_of_installment!=approved_no_of_installment')
            ->where('installment_start_month', '<=', $forMonth)
            ->get();

        $loanAmount = 0;
        if ($vl->count() > 0) {
            foreach ($vl as $ld) :

                // Stop benifit
                if ($this->isStopVehicleLoan($user->id, $ld->id, $forMonth, 'Regular')) {
                    continue;
                }

                VehicleLoanSchedule::where('vehicle_loan_fk', $ld->id)
                    ->where('transaction_date', $tdate)
                    ->where('payment_status', '!=', 'Settlement')
                    ->where('payment_status', '!=', 'Return')
                    ->update(['payment_status' => 'Unpaid', 'transaction_date' => '0000-00-00']);

                $pfReExists = VehicleLoanReceive::where('vehicle_loan_fk', $ld->id)
                    ->where('type', 'Regular')
                    ->where('users_id_fk', $user->id)
                    ->where('transaction_date', $tdate);

                if ($pfReExists->count() > 0) {
                    $pfReExists->delete();
                }

                $schedule = VehicleLoanSchedule::select(\DB::raw('sum(total_payment) as total_payment'))->where('payment_date', '<=', $forMonth)->where('payment_status', '=', 'Unpaid')->where('vehicle_loan_fk', '=', $ld->id)->first();
                $loanAmount += $schedule->total_payment;
            endforeach;
        }

        // OPENNING BALANCE LOAN
        $op = OpeningBalanceLoanVehicle::where('users_id_fk', $user->id)
            ->where('installment_start_date', '<=', $forMonth)
            ->get();

        if ($op->count() > 0) {
            foreach ($op as $opv) :

                // Stop benifit
                if ($this->isStopVehicleLoan($user->id, $opv->id, $forMonth, 'Openning Balance')) {
                    continue;
                }

                // SCHEDULE UPDATE
                OpeningBalanceLoanVehicleSchedule::where('ob_loan_vehicle_fk', $opv->id)
                    ->where('payment_status', '!=', 'Settlement')
                    ->where('payment_status', '!=', 'Return')
                    ->where('transaction_date', $tdate)
                    ->update(['payment_status' => 'Unpaid', 'transaction_date' => '0000-00-00']);

                // RECEIVE UPDATE
                $pfReExists = VehicleLoanReceive::where('vehicle_loan_fk', $opv->id)
                    ->where('type', 'Opening Balance')
                    ->where('users_id_fk', $user->id)
                    ->where('transaction_date', $tdate);
                if (count($pfReExists->get()) > 0) {
                    $pfReExists->delete();
                }

                // LOAN AMOUNT
                $schedule = OpeningBalanceLoanVehicleSchedule::select(\DB::raw('sum(total_payment) as total_payment'))
                    ->where('payment_date', '<=', $forMonth)
                    ->where('payment_status', '=', 'Unpaid')
                    ->where('ob_loan_vehicle_fk', '=', $opv->id)
                    ->first();
                $loanAmount += $schedule->total_payment;
            endforeach;
        }

        return round($loanAmount);
    }

    public function saveVehicleLoanAmount($user, $salaryTargetDate, $paymentStatus = 'Paid', $salaryGenerateDate, $transactionMonth)
    {
        $lastDateMonth = date("Y-m-t", strtotime($salaryTargetDate));
        $forMonth = date("Y-m-01", strtotime($salaryTargetDate));

        $vl = VehicleLoan::where('users_id_fk', $user->id)
            ->where('status', 'Approved')
            ->whereRaw('completed_no_of_installment!=approved_no_of_installment')
            ->where('installment_start_month', '<=', $lastDateMonth)
            ->get();

        if ($vl->count() > 0) {
            foreach ($vl as $ld) {

                if ($this->isStopVehicleLoan($user->id, $ld->id, $salaryTargetDate, 'Regular')) {
                    continue;
                }

                $scheduleList = VehicleLoanSchedule::where('payment_date', '<=', $lastDateMonth)
                    ->where('payment_status', '=', 'Unpaid')
                    ->where('vehicle_loan_fk', '=', $ld->id)
                    ->get();

                foreach ($scheduleList as $scheduleItem) {
                    $model = new VehicleLoanReceive;
                    $model->vehicle_loan_fk = $ld->id;
                    $model->users_id_fk = $user->id;
                    $model->transaction_date = $forMonth;
                    $model->transaction_month = $transactionMonth;
                    $model->amount = floatval($scheduleItem->total_payment);
                    $model->principal_amount = floatval($scheduleItem->principal);
                    $model->interest_amount = floatval($scheduleItem->interest);
                    $model->fiscal_id_fk = FiscalYear::getCurrent()->id ?? 0;
                    $model->for_month = date('Y-m-d', strtotime($scheduleItem->payment_date));
                    $model->type = 'Regular';
                    $model->received_branch_id = $user->branchId;
                    $model->created_by = Auth::user()->id;
                    $model->updated_at = date("Y-m-d H:i:s");
                    $model->updated_by = Auth::user()->id;
                    $model->payment_status = $paymentStatus;
                    $model->generate_date = $salaryGenerateDate;
                    if ($model->amount > 0) {
                        $model->save();
                        $vLoanSchedule = VehicleLoanSchedule::find($scheduleItem->id);
                        $vLoanSchedule->payment_status = 'Paid';
                        $vLoanSchedule->transaction_date = $model->transaction_date;
                        $vLoanSchedule->transaction_month = $transactionMonth;
                        $vLoanSchedule->generate_date = $model->generate_date;
                        $vLoanSchedule->save();
                    }

                    $vLoanReceive = VehicleLoanReceive::where('type', 'Regular')
                        ->where('vehicle_loan_fk', $ld->id)
                        ->get();

                    $vLoan = VehicleLoan::find($ld->id);
                    $vLoan->completed_no_of_installment = $vLoanReceive->count();
                    $vLoan->save();
                }
            }
        }

        // Opening balance
        $op = OpeningBalanceLoanVehicle::where('users_id_fk', $user->id)
            ->where('installment_start_date', '<=', $lastDateMonth)
            ->get();

        if ($op->count() > 0) {
            foreach ($op as $opv) {

                if ($this->isStopVehicleLoan($user->id, $opv->id, $salaryTargetDate, 'Openning Balance')) {
                    continue;
                }

                $schedule = OpeningBalanceLoanVehicleSchedule::where('payment_date', '<=', $lastDateMonth)
                    ->where('payment_status', '=', 'Unpaid')
                    ->where('ob_loan_vehicle_fk', '=', $opv->id);

                if ($schedule->count() > 0) {
                    foreach ($schedule->get() as $sch) {
                        $model = new VehicleLoanReceive;
                        $model->vehicle_loan_fk = $opv->id;
                        $model->users_id_fk = $user->id;
                        $model->transaction_date = $forMonth;
                        $model->transaction_month = $transactionMonth;
                        $model->amount = floatval($sch->total_payment);
                        $model->principal_amount = floatval($sch->principal);
                        $model->interest_amount = floatval($sch->interest);
                        $model->fiscal_id_fk = @$this->easycode->getCurrentFiscalYear()['id'];
                        $model->for_month = date('Y-m-d', strtotime($sch->payment_date));
                        $model->type = 'Opening Balance';
                        $model->received_branch_id = $user->branchId;
                        $model->created_by = Auth::user()->id;
                        $model->updated_at = date("Y-m-d H:i:s");
                        $model->updated_by = Auth::user()->id;
                        $model->payment_status = $paymentStatus;
                        $model->generate_date = $salaryGenerateDate;
                        if (floatval($model->amount) > 0) {
                            $model->save();
                            // Schedule paid
                            $schPaid = OpeningBalanceLoanVehicleSchedule::find($sch->id);
                            $schPaid->payment_status = 'Paid';
                            $schPaid->transaction_date = $model->transaction_date;
                            $schPaid->transaction_month = $transactionMonth;
                            $schPaid->generate_date = $model->generate_date;
                            $schPaid->save();
                        }
                    }
                }
            }
        }
    }

    /**
     *  Benifit is stop or not
     *
     * @param int $userId
     * @param int $loanId
     * @param string $salaryMonth
     * @param string $type
     *
     * @return boolean $hasStopBenifit
     */
    public function isStopVehicleLoan($userId, $loanId, $salaryMonth, $type)
    {
        $hasStopBenifit = false;
        $stopSalaryBenifitList = StopSalaryBenefit::getStopSalaryBenefit($userId, $salaryMonth, 'Vehicle Loan');
        foreach ($stopSalaryBenifitList as $stopSalaryBenifit) {
            if ($stopSalaryBenifit) {
                $stopSalaryBenifitNotes = json_decode($stopSalaryBenifit->notes, true)['Vehicle Loans'];
            }

            if (!empty($stopSalaryBenifitNotes)) {
                foreach ($stopSalaryBenifitNotes as $stopSalaryBenifitNote) {
                    if ($loanId == $stopSalaryBenifitNote['loan_id'] && $stopSalaryBenifitNote['type'] == $type) {
                        $hasStopBenifit = true;
                        break 2;
                    }
                }
            }
        }
        return $hasStopBenifit;
    }

    public function getSalarySheetData($id, $request, $salarySheet)
    {

        $data['easycode'] = $this->easycode;
        $data['userModel'] = new User;

        $data['benefitType'] = BenefitType::where('status', 1)->get();
        $data['aBenefitType'] = BenefitType::where('status', 1)->where('type', 'A')->get();
        $data['bBenefitType'] = BenefitType::where('status', 1)->where('type', 'B')->get();

        $data['salarySheet'] = $salarySheet;

        $data['salarySheetTargetMonth'] = date('Y-m', strtotime($data['salarySheet']->target_month));
        $data['epsSettingsMinEffectMonth'] = date('Y-m', strtotime(DB::table('hr_settings_eps')->min('effect_date')));

        $data['row'] = [];
        $empList = json_decode($data['salarySheet']->contents, true);

        $data['managementStaffCount'] = 0;
        foreach ($empList as $k => $row) {
            if ($row['payment_status'] == 'Paid') {
                if ($row['position_type'] == 'Management Staffs') {
                    $data['row'][] = $row;
                    $data['managementStaffCount']++;
                }
            }
        }

        $data['otherStaffCount'] = 0;
        foreach ($empList as $k => $row) {
            if ($row['payment_status'] == 'Paid') {
                if ($row['position_type'] != 'Management Staffs') {
                    $data['row'][] = $row;
                    $data['otherStaffCount']++;
                }
            }
        }

        $data['benefitsCol'] = count($data['bBenefitType']);
        $data['organizationContributionCol'] = 1;
        $data['deductionCol'] = 8;
        $data['managementStaffCol'] = 23 + count($data['benefitType']);

        $data['otherSettings'] = Other::where('status', 1)->first();
        if (@$data['otherSettings']->wf_enable == '1') {
            $data['organizationContributionCol']++;
            $data['benefitsCol']++;
            $data['deductionCol'] += 3;
            $data['managementStaffCol'] += 4;
        }
        if (@$data['otherSettings']->insurance_enable == '1') {
            $data['organizationContributionCol']++;
            $data['benefitsCol']++;
            $data['deductionCol'] += 2;
            $data['managementStaffCol'] += 3;
        }
        if (@$data['otherSettings']->insurance_enable == '1') {
            $data['organizationContributionCol']++;
            $data['benefitsCol']++;
            $data['deductionCol'] += 2;
            $data['managementStaffCol'] += 3;
        }

        return $data;
    }

    public function saveOtherSalaryDeduction($otherSalaryDeductionData, $targetMonthDate, $generateDate, $transactionMonth)
    {
        if (count($otherSalaryDeductionData) > 0) {
            foreach ($otherSalaryDeductionData as $otherSalaryDeductionDataItem) {
                $mSalaryDeductionReceive = new SalaryDeductionReceive();
                $mSalaryDeductionReceive->hr_salary_deduction_informations_id_fk = $otherSalaryDeductionDataItem['id'];
                $mSalaryDeductionReceive->installment_amount = $otherSalaryDeductionDataItem['amount'];
                $mSalaryDeductionReceive->salary_target_month_date = $targetMonthDate;
                $mSalaryDeductionReceive->salary_generate_date = $generateDate;
                $mSalaryDeductionReceive->transaction_month = $transactionMonth;
                $mSalaryDeductionReceive->save();
            }
        }
    }

    public function getOtherSalaryDeductionData($user, $targetMonth)
    {
        $data = [];
        if ($user) {

            $employeeId = $user->emp_id_fk;
            $targetMonthDate = date('Y-m-d', strtotime($targetMonth));

            $salaryDeductionInformations = SalaryDeductionInformation::where(
                'hr_emp_general_info_id_fk',
                $user->emp_id_fk
            )->where(
                'effect_month_date',
                '<=',
                $targetMonthDate
            )->get();

            foreach ($salaryDeductionInformations as $index => $salaryDeductionInformation) {

                $amount = $this->getCalculateOtherDeductionAmount(
                    $salaryDeductionInformation,
                    $employeeId,
                    $targetMonthDate
                );

                if ($amount) {
                    $data[$index]['id'] = $salaryDeductionInformation->id;
                    $data[$index]['amount'] = $amount;
                }
            }
        }
        return $data;
    }

    public function getCalculateOtherDeductionAmount($salaryDeductionInformation, $employeeId, $targetMonthDate)
    {
        $receivedAmount = $salaryDeductionInformation->salaryDeductionReceives->sum('installment_amount');
        $totalDeductionAmount = $salaryDeductionInformation->total_amount;
        $totalInstallmentNo = $salaryDeductionInformation->installment_no;

        $leftAmount = $totalDeductionAmount - $receivedAmount;
        $installmentAmount = round($totalDeductionAmount / $totalInstallmentNo);
        if ($receivedAmount == $totalDeductionAmount) {
            return 0.00;
        }

        if ($receivedAmount > $totalDeductionAmount) {
            return 0.00;
        }

        if ($leftAmount < $installmentAmount) {
            return round($leftAmount);
        }

        return round($installmentAmount, 2);
    }

    public function adjustOhterDeduction($userId, $transactionMonth)
    {
        $user = User::find($userId);
        $mSalaryDeductionReceive = new SalaryDeductionReceive;
        $salaryDeductionReceives = SalaryDeductionInformation::join(
            'hr_salary_deduction_receives',
            'hr_salary_deduction_informations.id',
            'hr_salary_deduction_receives.hr_salary_deduction_informations_id_fk'
        )
            ->where('hr_salary_deduction_informations.hr_emp_general_info_id_fk', $user->emp_id_fk)
            ->where('hr_salary_deduction_receives.transaction_month', $transactionMonth)
            ->select('hr_salary_deduction_receives.*')
            ->get();
        foreach ($salaryDeductionReceives as $salaryDeductionReceive) {
            $mSalaryDeductionReceive->where('id', $salaryDeductionReceive->id)->delete();
        }
    }

    public function adjustEdps($userId, $salaryMonth)
    {
        $model = new EdpsReceive;
        $chkExist = $model->where('users_id_fk', $userId)->where('transaction_month', date("Y-m-d", strtotime($salaryMonth)));
        if ($chkExist->get()->count() > 0) {
            $chkExist->delete();
        }
    }

    public function adjustEps($userId, $transactionMonth)
    {
        return EpsReceive::where('users_id_fk', $userId)
            ->where('transaction_month', date("Y-m-d", strtotime($transactionMonth)))
            ->delete();
    }

    public function adjustSecurityAmount($emp_id_fk, $transactionMonth)
    {

        return SecurityMoneyCollection::where('emp_id_fk', $emp_id_fk)
            ->where('type', 'Installment')
            ->where('transaction_month', $transactionMonth)
            ->delete();
    }

    public function adjustPfLoanAmount($userId, $forMonth, $tdate)
    {
        $vl = ProvidentFundLoan::select('t1.*', 't2.id as review_id', 't2.inst_amount', 't2.completed_no_of_installment')
            ->from('hr_pf_loan as t1')
            ->where('t1.users_id_fk', $userId)
            ->where('t1.status', 'Approved')
            ->where('t2.inst_start_month', '<=', $forMonth)
            ->join('hr_pf_loan_review as t2', 't2.pf_loan_id_fk', '=', 't1.id')
            ->get();

        if ($vl->count() > 0) {
            foreach ($vl as $ld) {
                ProvidentFundLoanSchedule::where('pf_loan_review_fk', $ld->review_id)
                    ->where('payment_status', '!=', 'Settlement')
                    ->where('payment_status', '!=', 'Return')
                    ->where('transaction_date', $tdate)
                    ->update(['payment_status' => 'Unpaid', 'transaction_date' => '0000-00-00']);

                $pfReExists = PfLoanReceive::where('pf_loan_fk', $ld->id)
                    ->where('type', 'Regular')
                    ->where('users_id_fk', $userId)
                    ->where('transaction_date', $tdate);

                $pfReExists->delete();

                // Adjustment pf loan installment
                $noOfCompletedInstallment = PfLoanReceive::where('pf_loan_fk', $ld->id)->where('type', 'Regular');
                $nvl = ProvidentFundLoanReview::where('pf_loan_id_fk', $ld->id)->first();
                $nvl->completed_no_of_installment = $noOfCompletedInstallment->count();
                $nvl->save();
            }
        }

        // Opening Balance
        $op = OpeningBalanceLoanPf::where('users_id_fk', $userId)->where('installment_start_date', '<=', $forMonth)->get();
        if ($op->count() > 0) {
            foreach ($op as $oppf) {
                OpeningBalanceLoanPfSchedule::where('ob_loan_pf_fk', $oppf->id)
                    ->where('payment_status', '!=', 'Settlement')
                    ->where('payment_status', '!=', 'Return')
                    ->where('transaction_date', $tdate)
                    ->update(['payment_status' => 'Unpaid', 'transaction_date' => '0000-00-00']);

                $pfReExists = PfLoanReceive::where('pf_loan_fk', $oppf->id)
                    ->where('type', 'Opening Balance')
                    ->where('users_id_fk', $userId)
                    ->where('transaction_date', $tdate);

                if ($pfReExists->count() > 0) {
                    $pfReExists->delete();
                }
            }
        }
    }

    public function adjustVehicleLoanAmount($userId, $transactionMonth, $transactionDate)
    {
        // Opening
        $opVehicleLoanList = OpeningBalanceLoanVehicle::where('users_id_fk', $userId)
            ->where('installment_start_date', '<=', $transactionMonth)
            ->get();

        if ($opVehicleLoanList->count() > 0) {
            foreach ($opVehicleLoanList as $opVehicleLoan) {
                OpeningBalanceLoanVehicleSchedule::where('ob_loan_vehicle_fk', $opVehicleLoan->id)
                    ->where('payment_status', '!=', 'Settlement')
                    ->where('payment_status', '!=', 'Return')
                    ->where('transaction_month', $transactionMonth)
                    ->update(['payment_status' => 'Unpaid', 'transaction_date' => '0000-00-00']);

                VehicleLoanReceive::where('vehicle_loan_fk', $opVehicleLoan->id)
                    ->where('type', 'Opening Balance')
                    ->where('users_id_fk', $userId)
                    ->where('transaction_month', $transactionMonth)
                    ->delete();
            }
        }

        // Regular
        $vLoanList = VehicleLoan::where('users_id_fk', $userId)
            ->where('status', 'Approved')
            ->where('installment_start_month', '<=', $transactionMonth)
            ->get();

        if ($vLoanList->count() > 0) {
            foreach ($vLoanList as $vLoan) {
                VehicleLoanSchedule::where('vehicle_loan_fk', $vLoan->id)
                    ->where('payment_status', '!=', 'Settlement')
                    ->where('payment_status', '!=', 'Return')
                    ->where('transaction_month', $transactionMonth)
                    ->update(['payment_status' => 'Unpaid', 'transaction_date' => '0000-00-00']);

                VehicleLoanReceive::where('vehicle_loan_fk', $vLoan->id)
                    ->where('type', 'Regular')
                    ->where('users_id_fk', $userId)
                    ->where('transaction_month', $transactionMonth)
                    ->delete();

                $noOfCompletedInstallment = VehicleLoanReceive::where('type', 'Regular')
                    ->where('vehicle_loan_fk', $vLoan->id)
                    ->count();

                $vLoan->completed_no_of_installment = $noOfCompletedInstallment;
                $vLoan->save();
            }
        }
    }

    public function adjustIncomeTax($userId, $salaryTargetMonth, $transactionMonth)
    {
        $lastDateMonth = date('Y-m-t', strtotime($salaryTargetMonth));

        $vl = IncomeTax::where('users_id_fk', $userId)
            ->where('status', 'Approved')
            ->where('acpected_installment_start_month', '<=', date('Y-m-d', strtotime($lastDateMonth)))
            ->get();

        foreach ($vl as $ld) {
            IncomeTaxReceive::where('income_tax_id', $ld->id)
                ->where('users_id_fk', $userId)
                ->where('transaction_month', $transactionMonth)
                ->delete();
        }
    }

    public function adjustAdvancedSalaryLoanAmount($userId, $salaryTargetmonth, $transactionMonth)
    {
        $lastDateMonth = date('Y-m-t', strtotime($salaryTargetmonth));

        // Openning
        $op = OpeningBalanceLoanAdvancedSalary::where('users_id_fk', $userId)->get();
        foreach ($op as $opv) {
            AdvancedSalaryLoanReceive::where('type', 'Opening Balance')
                ->where('advanced_salary_loan_fk', $opv->id)
                ->where('users_id_fk', $userId)
                ->where('transaction_date', $transactionMonth)
                ->delete();
        }

        // Regular
        $vl = AdvancedSalaryLoan::where('users_id_fk', $userId)
            ->where('status', 'Approved')
            ->where('approved_installment_start_month', '<=', $lastDateMonth)
            ->get();
        foreach ($vl as $ld) {
            AdvancedSalaryLoanReceive::where('type', 'Regular')
                ->where('advanced_salary_loan_fk', $ld->id)
                ->where('users_id_fk', $userId)
                ->where('transaction_date', $transactionMonth)
                ->delete();
        }
    }

    public function adjustPf($userId, $transactionMonth)
    {
        return ProvidentFundReceive::where('users_id_fk', $userId)->where('transaction_month', $transactionMonth)->delete();
    }

    public function adjustInsurance($userId, $transactionMonth)
    {
        return InsuranceReceive::where('users_id_fk', $userId)->where('transaction_month', $transactionMonth)->delete();
    }

    public function adjustOsf($userId, $transactionMonth)
    {
        return OsfReceive::where('users_id_fk', $userId)->where('transaction_month', $transactionMonth)->delete();
    }

    public function adjustWf($userId, $transactionMonth)
    {
        return WelfareFundReceive::where('users_id_fk', $userId)
            ->where('transaction_month', date("Y-m-01", strtotime($transactionMonth)))
            ->delete();
    }

    /**
     * Get benefit type b from salary sheet
     *
     * @param SalaryGenerate $salaryInfo
     *
     * @return array $benifitType
     */
    public function getBenefitTypeBFromSalarySheet($salaryInfo)
    {
        $benifitType = [];

        $pattern = "/benefit-b-./";

        foreach ($salaryInfo as $key => $value) {
            if (preg_match($pattern, $key)) {
                $benifitType[$key] = $value;
            }
        }

        return $benifitType;
    }

    public function adjustDueSalarySheet($user, $salaryTargetMonth, $salaryTransactionMonth)
    {
        // Remove Due Salary Sheet that was generated salary month
        SalaryGenerateDueDetail::where('target_month', $salaryTargetMonth)
            ->where('users_id_fk', $user->id)
            ->where('status', 'Pending')
            ->delete();

        // Adjust due details that was paid in salary month
        $dueSalaryCurrentMonthPaidList = SalaryGenerateDueDetail::where('transaction_month', $salaryTransactionMonth)
            ->where('users_id_fk', $user->id)
            ->get();

        foreach ($dueSalaryCurrentMonthPaidList as $dueSalaryCurrentMonthPaid) {
            $dueSalaryCurrentMonthPaid->status = 'Pending';
            $dueSalaryCurrentMonthPaid->save();
        }
    }
}
