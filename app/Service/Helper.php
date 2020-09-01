<?php

namespace App\Service;

use App\hr\PromotionIncrement;
use DB;
use Auth;
use App\User;
use DateTime;
use DateInterval;
use Carbon\Carbon;
use App\gnr\GnrArea;
use App\gnr\GnrZone;
use App\gnr\GnrBranch;
use App\gnr\GnrFunction;
use App\hr\SalaryGenerate;
use App\hr\SalaryStructure;
use App\hr\StopSalaryBenefit;
use App\accounting\AddLedger;
use App\hr\EmployeeGeneralInfo;
use App\hr\ReportingBossEmployee;
use App\hr\EmployeeOrganizationInfo;
use App\hr\AutoVoucherSettingItem;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\hr\ResignInfo;
use App\hr\TerminateInfo;
use App\hr\RetirementInfo;

/**
 *
 * Helper Class For HR Department
 *
 * @author hafij <hafij.to@gmail.com>
 *
 */
class Helper
{

    public static function epsFindInSalarySheetButNotReceiveTableIn($targetMonth)
    {

        $employeesWhoHasEpsProblem = null;
        $employeesWhoGetEpsInReceieveTable = null;
        $employeesWhoGetEpsInSheet = null;
        $salaryGenerates = SalaryGenerate::where('target_month', $targetMonth)
            ->get();

        $employeesWhoGetEpsInReceieveTable = DB::table('hr_eps_receive')
            ->where('for_month', $targetMonth)
            ->get();
        // dd($employeesWhoGetEpsInReceieveTable);

        foreach ($salaryGenerates as $salaryGenerate) {
            $contents = json_decode($salaryGenerate->contents);
            foreach ($contents as $v) {
                if ($v->eps > 0) {
                    $employeesWhoGetEpsInSheet[] = $v;
                }
            }
        }
        // dd($employeesWhoGetEpsInSheet);

        foreach ($employeesWhoGetEpsInSheet as $v1) {
            $status = false;
            foreach ($employeesWhoGetEpsInReceieveTable as $v2) {
                if ($v1->user_id == $v2->users_id_fk) {
                    $status = true;
                    break;
                }
            }
            if (!$status) {
                $user = User::find($v1->user_id);
                $v1->emp_id = $user->username;
                $employeesWhoHasEpsProblem[] = $v1;
            }
            // dd( $status);
        }
        dd($employeesWhoHasEpsProblem);
    }

    public static function totalEpsInSalarySheet($fromDate, $toDate)
    {
        $total = 0.00;
        $employeesWhoGetEpsInSheet = null;

        // validation
        if (!$fromDate || !$toDate) {
            return $total;
        }

        // Formatting
        $fromDate = date('Y-m-d', strtotime($fromDate));
        $toDate = date('Y-m-t', strtotime($toDate));

        // dd($toDate);
        $salaryGenerates = SalaryGenerate::where('target_month', '>=', $fromDate)
            ->where('project_id_fk', 1)
            ->where('target_month', '<=', $toDate)
            ->get();

        // dd($salaryGenerates);

        foreach ($salaryGenerates as $salaryGenerate) {
            $contents = json_decode($salaryGenerate->contents);
            foreach ($contents as $v) {
                if ($v->eps > 0) {
                    $employeesWhoGetEpsInSheet[] = $v;
                }
            }
        }

        if ($employeesWhoGetEpsInSheet) {
            foreach ($employeesWhoGetEpsInSheet as $sheet) {
                $total += $sheet->eps;
            }
        }

        dd($total);
    }

    public static function epsFindInReceiveTableButNotSalrySheetIn($targetMonth)
    {

        $employeesWhoHasEpsProblem = null;
        $employeesWhoGetEpsInReceieveTable = null;
        $employeesWhoGetEpsInSheet = null;
        $salaryGenerates = SalaryGenerate::where('target_month', $targetMonth)
            ->get();

        $employeesWhoGetEpsInReceieveTable = DB::table('hr_eps_receive')
            ->where('for_month', $targetMonth)
            ->get();
        // dd($employeesWhoGetEpsInReceieveTable);

        foreach ($salaryGenerates as $salaryGenerate) {
            $contents = json_decode($salaryGenerate->contents);
            foreach ($contents as $v) {
                if ($v->eps > 0) {
                    $employeesWhoGetEpsInSheet[] = $v;
                }
            }
        }
        // dd($employeesWhoGetEpsInSheet);

        foreach ($employeesWhoGetEpsInReceieveTable as $v1) {

            $status = false;

            foreach ($employeesWhoGetEpsInSheet as $v2) {
                if ($v2->user_id == $v1->users_id_fk) {
                    $status = true;
                    break;
                }
            }

            if (!$status) {
                $user = User::find($v1->users_id_fk);
                $v1->emp_id = $user->username;
                $employeesWhoHasEpsProblem[] = $v1;
            }
            // dd( $status);
        }
        dd($employeesWhoHasEpsProblem);
    }


    public static function sdFindInReceiveTableButNotSalarySheetIn($targetMonth)
    {

        $employeesWhoHasSDProblem = null;
        $employeesWhoGetSDInReceieveTable = null;
        $employeesWhoGetSDInSheet = null;
        $salaryGenerates = SalaryGenerate::where('target_month', $targetMonth)
            ->get();

        $employeesWhoGetSDInReceieveTable = DB::table('hr_security_money_collection')
            ->where('for_month', $targetMonth)
            ->where('type', '!=', 'Advanced')
            ->get();
        // dd($employeesWhoGetSDInReceieveTable);

        foreach ($salaryGenerates as $salaryGenerate) {
            $contents = json_decode($salaryGenerate->contents);
            foreach ($contents as $v) {
                if ($v->security_money > 0) {
                    $employeesWhoGetSDInSheet[] = $v;
                }
            }
        }
        // dd($employeesWhoGetSDInSheet);

        foreach ($employeesWhoGetSDInReceieveTable as $v2) {
            $user = User::where('emp_id_fk', $v2->emp_id_fk)->first();
            $status = false;
            foreach ($employeesWhoGetSDInSheet as $v1) {
                if ($v1->user_id == $user->id) {
                    $status = true;
                    break;
                }
            }
            if (!$status) {
                $user = User::where('emp_id_fk', $v2->emp_id_fk)->first();
                $v2->emp_id = $user->username;
                $employeesWhoHasSDProblem[] = $v2;
            }
            // dd( $status);
        }
        dd($employeesWhoHasSDProblem);
    }

    public static function sdFindInSalarySheetButNotReceiveTableIn($targetMonth)
    {

        $employeesWhoHasSDProblem = null;
        $employeesWhoGetSDInReceieveTable = null;
        $employeesWhoGetSDInSheet = null;
        $salaryGenerates = SalaryGenerate::where('target_month', $targetMonth)
            ->get();

        $employeesWhoGetSDInReceieveTable = DB::table('hr_security_money_collection')
            ->where('for_month', $targetMonth)
            ->where('type', '!=', 'Advanced')
            ->get();
        // dd($employeesWhoGetSDInReceieveTable);

        foreach ($salaryGenerates as $salaryGenerate) {
            $contents = json_decode($salaryGenerate->contents);
            foreach ($contents as $v) {
                if ($v->security_money > 0) {
                    $employeesWhoGetSDInSheet[] = $v;
                }
            }
        }
        // dd($employeesWhoGetSDInSheet);

        foreach ($employeesWhoGetSDInSheet as $v2) {
            $user = User::where('id', $v2->user_id)->first();
            $status = false;
            foreach ($employeesWhoGetSDInReceieveTable as $v1) {
                if ($v1->emp_id_fk == $user->emp_id_fk) {
                    $status = true;
                    break;
                }
            }
            if (!$status) {
                $employeesWhoHasSDProblem[] = $v2;
            }
            // dd( $status);
        }
        dd($employeesWhoHasSDProblem);
    }

    public static function pfFindInSalarySheetButNotReceiveTableIn($targetMonth)
    {

        $employeesWhoHasSDProblem = null;
        $employeesWhoGetPfInReceieveTable = null;
        $employeesWhoGetPfInSheet = null;
        $salaryGenerates = SalaryGenerate::where('target_month', $targetMonth)
            ->get();

        $employeesWhoGetPfInReceieveTable = DB::table('hr_pf_receive')
            ->where('for_month', $targetMonth)
            ->get();
        // dd($employeesWhoGetPfInReceieveTable);

        foreach ($salaryGenerates as $salaryGenerate) {
            $contents = json_decode($salaryGenerate->contents);
            foreach ($contents as $v) {
                if ($v->pf_self > 0) {
                    $employeesWhoGetPfInSheet[] = $v;
                }
            }
        }
        // dd($employeesWhoGetPfInSheet);

        foreach ($employeesWhoGetPfInSheet as $v2) {
            $status = false;
            foreach ($employeesWhoGetPfInReceieveTable as $v1) {
                if ($v1->users_id_fk == $v2->user_id) {
                    $status = true;
                    break;
                }
            }
            if (!$status) {
                $employeesWhoHasSDProblem[] = $v2;
            }
            // dd( $status);
        }
        dd($employeesWhoHasSDProblem);
    }

    public static function pfDifferenceSalarySheetAndReceiveTable($targetMonth)
    {

        $diffList = [];
        $employeesWhoGetPfInReceieveTable = [];
        $employeesWhoGetPfInSheet = [];

        $salaryGenerates = SalaryGenerate::where('target_month', $targetMonth)
            ->get();

        $employeesWhoGetPfInReceieveTable = DB::table('hr_pf_receive')
            ->where('for_month', $targetMonth)
            ->get();

        // dd($employeesWhoGetPfInReceieveTable);

        foreach ($salaryGenerates as $salaryGenerate) {
            $contents = json_decode($salaryGenerate->contents);
            foreach ($contents as $v) {
                if ($v->pf_self > 0) {
                    $employeesWhoGetPfInSheet[] = $v;
                }
            }
        }

        // dd($employeesWhoGetPfInSheet);

        foreach ($employeesWhoGetPfInSheet as $sheet) {
            foreach ($employeesWhoGetPfInReceieveTable as $receiveTable) {
                if ($receiveTable->users_id_fk == $sheet->user_id) {
                    $totalPfInSheet = $sheet->pf_self + $sheet->pf_org;
                    $totalPfInReceiveTable = $receiveTable->total_amount;
                    if($totalPfInReceiveTable > $totalPfInSheet || $totalPfInReceiveTable < $totalPfInSheet){
                        $s = new \stdClass();
                        $s->receiveTable = $receiveTable;
                        $s->sheet = $sheet;
                        $diffList[] = $s;
                    }
                    break;
                }
            }
        }
        dd($diffList);
    }

    public static function pfFindInReceiveTableButNotSalarySheetIn($targetMonth)
    {

        $employeesWhoHasSDProblem = null;
        $employeesWhoGetPfInReceieveTable = null;
        $employeesWhoGetPfInSheet = null;
        $salaryGenerates = SalaryGenerate::where('target_month', $targetMonth)
            ->get();

        $employeesWhoGetPfInReceieveTable = DB::table('hr_pf_receive')
            ->where('for_month', $targetMonth)
            ->get();
        // dd($employeesWhoGetPfInReceieveTable);

        foreach ($salaryGenerates as $salaryGenerate) {
            $contents = json_decode($salaryGenerate->contents);
            foreach ($contents as $v) {
                if ($v->pf_self > 0) {
                    $employeesWhoGetPfInSheet[] = $v;
                }
            }
        }
        // dd($employeesWhoGetPfInSheet);

        foreach ($employeesWhoGetPfInReceieveTable as $v2) {
            $status = false;
            foreach ($employeesWhoGetPfInSheet as $v1) {
                if ($v1->user_id == $v2->users_id_fk) {
                    $status = true;
                    break;
                }
            }
            if (!$status) {
                $user = User::find($v2->users_id_fk);
                $v2->emp_id = $user->username;
                $employeesWhoHasSDProblem[] = $v2;
            }
            // dd( $status);
        }
        dd($employeesWhoHasSDProblem);
    }

    public static function vehicleLoanFindInReceiveTableButNotSalarySheetIn($targetMonth)
    {

        $employeesWhoHasVehicleLoanProblem = null;
        $employeesWhoGetVehicleLoanInReceieveTable = null;
        $employeesWhoGetVehicleLoanInSheet = null;
        $salaryGenerates = SalaryGenerate::where('target_month', $targetMonth)
            ->get();

        $employeesWhoGetVehicleLoanInReceieveTable = DB::table('hr_vehicle_loan_receive')
            ->where('for_month', $targetMonth)
            ->get();
        // dd($employeesWhoGetVehicleLoanInReceieveTable);

        foreach ($salaryGenerates as $salaryGenerate) {
            $contents = json_decode($salaryGenerate->contents);
            foreach ($contents as $v) {
                if ($v->vehicle_loan > 0) {
                    $employeesWhoGetVehicleLoanInSheet[] = $v;
                }
            }
        }
        // dd($employeesWhoGetVehicleLoanInSheet);

        foreach ($employeesWhoGetVehicleLoanInReceieveTable as $v2) {
            $status = false;
            foreach ($employeesWhoGetVehicleLoanInSheet as $v1) {
                if ($v1->user_id == $v2->users_id_fk) {
                    $status = true;
                    break;
                }
            }
            if (!$status) {
                $user = User::find($v2->users_id_fk);
                $v2->emp_id = $user->username;
                $employeesWhoHasVehicleLoanProblem[] = $v2;
            }
            // dd( $status);
        }
        dd($employeesWhoHasVehicleLoanProblem);
    }

    public static function vehicleLoanFindInSalarySheetButNotReceiveTableIn($targetMonth)
    {

        $employeesWhoHasVehicleLoanProblem = null;
        $employeesWhoGetVehicleLoanInReceieveTable = null;
        $employeesWhoGetVehicleLoanInSheet = null;
        $salaryGenerates = SalaryGenerate::where('target_month', $targetMonth)
            ->get();

        $employeesWhoGetVehicleLoanInReceieveTable = DB::table('hr_vehicle_loan_receive')
            ->where('for_month', $targetMonth)
            ->get();
        // dd($employeesWhoGetVehicleLoanInReceieveTable);

        foreach ($salaryGenerates as $salaryGenerate) {
            $contents = json_decode($salaryGenerate->contents);
            foreach ($contents as $v) {
                if ($v->vehicle_loan > 0) {
                    $employeesWhoGetVehicleLoanInSheet[] = $v;
                }
            }
        }
        // dd($employeesWhoGetVehicleLoanInSheet);

        foreach ($employeesWhoGetVehicleLoanInSheet as $v2) {
            $status = false;
            foreach ($employeesWhoGetVehicleLoanInReceieveTable as $v1) {
                if ($v2->user_id == $v1->users_id_fk) {
                    $status = true;
                    break;
                }
            }
            if (!$status) {
                $user = User::find($v2->user_id);
                $v2->emp_id = $user->username;
                $employeesWhoHasVehicleLoanProblem[] = $v2;
            }
            // dd( $status);
        }
        dd($employeesWhoHasVehicleLoanProblem);
    }

    public static function wfFindInSalarySheetButNotReceiveTableIn($targetMonth)
    {

        $employeesWhoHasWfProblem = null;
        $employeesWhoGetWfInReceieveTable = null;
        $employeesWhoGetWfInSheet = null;
        $salaryGenerates = SalaryGenerate::where('target_month', $targetMonth)
            ->get();

        $employeesWhoGetWfInReceieveTable = DB::table('hr_wf_receive')
            ->where('for_month', $targetMonth)
            ->get();
        // dd($employeesWhoGetWfInReceieveTable);

        foreach ($salaryGenerates as $salaryGenerate) {
            $contents = json_decode($salaryGenerate->contents);
            foreach ($contents as $v) {
                if ($v->wf_self > 0) {
                    $employeesWhoGetWfInSheet[] = $v;
                }
            }
        }
        // dd($employeesWhoGetWfInSheet);

        foreach ($employeesWhoGetWfInSheet as $v2) {
            $status = false;
            foreach ($employeesWhoGetWfInReceieveTable as $v1) {
                if ($v2->user_id == $v1->users_id_fk) {
                    $status = true;
                    break;
                }
            }
            if (!$status) {
                $user = User::find($v2->user_id);
                $v2->emp_id = $user->username;
                $employeesWhoHasWfProblem[] = $v2;
            }
            // dd( $status);
        }
        dd($employeesWhoHasWfProblem);
    }

    public static function wfFindInReceiveTableButNotSalarySheetIn($targetMonth)
    {

        $employeesWhoHasWfProblem = null;
        $employeesWhoGetWfInReceieveTable = null;
        $employeesWhoGetWfInSheet = null;

        $salaryGenerates = SalaryGenerate::where('target_month', $targetMonth)
            ->get();

        $employeesWhoGetWfInReceieveTable = DB::table('hr_wf_receive')
            ->where('for_month', $targetMonth)
            ->get();
        // dd($employeesWhoGetWfInReceieveTable->sum('emp_amount'));
        // dd($employeesWhoGetWfInReceieveTable);

        foreach ($salaryGenerates as $salaryGenerate) {
            $contents = json_decode($salaryGenerate->contents);
            foreach ($contents as $v) {
                if ($v->wf_contri_self > 0) {
                    $employeesWhoGetWfInSheet[] = $v;
                }
            }
        }
        // dd(collect($employeesWhoGetWfInSheet)->sum('wf_self'));
        // dd($employeesWhoGetWfInSheet);


        foreach ($employeesWhoGetWfInReceieveTable as $v1) {
            $status = false;
            foreach ($employeesWhoGetWfInSheet as $v2) {
                if ($v2->user_id == $v1->users_id_fk) {
                    $status = true;
                    break;
                }
            }
            if (!$status) {
                $user = User::find($v1->users_id_fk);
                $v1->emp_id = $user->username;
                $employeesWhoHasWfProblem[] = $v1;
            }
            // dd( $status);
        }
        dd($employeesWhoHasWfProblem);
    }

    public static function pfLoanFindInReceiveTableButNotSalarySheetIn($targetMonth)
    {

        $employeesWhoHasPfLoanProblem = null;
        $employeesWhoGetPfLoanInReceieveTable = null;
        $employeesWhoGetPfLoanInSheet = null;
        $salaryGenerates = SalaryGenerate::where('target_month', $targetMonth)
            ->get();

        $employeesWhoGetPfLoanInReceieveTable = DB::table('hr_pf_loan_receive')
            ->where('for_month', $targetMonth)
            ->get();
        // dd($employeesWhoGetPfLoanInReceieveTable);

        foreach ($salaryGenerates as $salaryGenerate) {
            $contents = json_decode($salaryGenerate->contents);
            foreach ($contents as $v) {
                if ($v->pf_loan > 0) {
                    $employeesWhoGetPfLoanInSheet[] = $v;
                }
            }
        }
        // dd($employeesWhoGetPfLoanInSheet);

        foreach ($employeesWhoGetPfLoanInReceieveTable as $v1) {
            $status = false;
            foreach ($employeesWhoGetPfLoanInSheet as $v2) {
                if ($v2->user_id == $v1->users_id_fk) {
                    $status = true;
                    break;
                }
            }
            if (!$status) {
                $employeesWhoHasPfLoanProblem[] = $v1;
            }
            // dd( $status);
        }
        dd($employeesWhoHasPfLoanProblem);
    }

    public static function pfLoanFindInSalarySheetButNotReceiveTableIn($targetMonth)
    {

        $employeesWhoHasPfLoanProblem = null;
        $employeesWhoGetPfLoanInReceieveTable = null;
        $employeesWhoGetPfLoanInSheet = null;
        $salaryGenerates = SalaryGenerate::where('target_month', $targetMonth)
            ->get();

        $employeesWhoGetPfLoanInReceieveTable = DB::table('hr_pf_loan_receive')
            ->where('for_month', $targetMonth)
            ->get();
        // dd($employeesWhoGetPfLoanInReceieveTable);

        foreach ($salaryGenerates as $salaryGenerate) {
            $contents = json_decode($salaryGenerate->contents);
            foreach ($contents as $v) {
                if ($v->pf_loan > 0) {
                    $employeesWhoGetPfLoanInSheet[] = $v;
                }
            }
        }
        // dd($employeesWhoGetPfLoanInSheet);

        foreach ($employeesWhoGetPfLoanInSheet as $v2) {
            $status = false;
            foreach ($employeesWhoGetPfLoanInReceieveTable as $v1) {
                if ($v2->user_id == $v1->users_id_fk) {
                    $status = true;
                    break;
                }
            }
            if (!$status) {
                $employeesWhoHasPfLoanProblem[] = $v1;
            }
            // dd( $status);
        }
        dd($employeesWhoHasPfLoanProblem);
    }

    public static function advanceSalaryLoanFindInSalarySheetButNotReceiveTableIn($targetMonth)
    {

        $employeesWhoHasAdvanceSalaryLoanProblem = null;
        $employeesWhoGetAdvanceSalaryLoanInReceieveTable = null;
        $employeesWhoGetAdvanceSalaryLoanLoanInSheet = null;
        $salaryGenerates = SalaryGenerate::where('target_month', $targetMonth)
            ->get();

        $employeesWhoGetAdvanceSalaryLoanInReceieveTable = DB::table('hr_advanced_salary_loan_receive')
            ->where('for_month', $targetMonth)
            ->get();
        // dd($employeesWhoGetAdvanceSalaryLoanInReceieveTable);

        foreach ($salaryGenerates as $salaryGenerate) {
            $contents = json_decode($salaryGenerate->contents);
            foreach ($contents as $v) {
                if ($v->advanced_salary_loan > 0) {
                    $employeesWhoGetAdvanceSalaryLoanLoanInSheet[] = $v;
                }
            }
        }
        // dd($employeesWhoGetAdvanceSalaryLoanLoanInSheet);

        foreach ($employeesWhoGetAdvanceSalaryLoanLoanInSheet ?: [] as $v2) {
            $status = false;
            foreach ($employeesWhoGetAdvanceSalaryLoanInReceieveTable as $v1) {
                if ($v2->user_id == $v1->users_id_fk) {
                    $status = true;
                    break;
                }
            }
            if (!$status) {
                $employeesWhoHasAdvanceSalaryLoanProblem[] = $v1;
            }
            // dd( $status);
        }
        dd($employeesWhoHasAdvanceSalaryLoanProblem);
    }

    public static function advanceSalaryLoanFindInReceiveTableButNotSalarySheetIn($targetMonth)
    {

        $employeesWhoHasAdvanceSalaryLoanProblem = null;
        $employeesWhoGetAdvanceSalaryLoanInReceieveTable = null;
        $employeesWhoGetAdvanceSalaryLoanLoanInSheet = null;
        $salaryGenerates = SalaryGenerate::where('target_month', $targetMonth)
            ->get();

        $employeesWhoGetAdvanceSalaryLoanInReceieveTable = DB::table('hr_advanced_salary_loan_receive')
            ->where('for_month', $targetMonth)
            ->get();
        // dd($employeesWhoGetAdvanceSalaryLoanInReceieveTable);

        foreach ($salaryGenerates as $salaryGenerate) {
            $contents = json_decode($salaryGenerate->contents);
            foreach ($contents as $v) {
                if ($v->advanced_salary_loan > 0) {
                    $employeesWhoGetAdvanceSalaryLoanLoanInSheet[] = $v;
                }
            }
        }
        // dd($employeesWhoGetAdvanceSalaryLoanLoanInSheet);

        foreach ($employeesWhoGetAdvanceSalaryLoanInReceieveTable ?: [] as $v1) {
            $status = false;
            foreach ($employeesWhoGetAdvanceSalaryLoanLoanInSheet ?: [] as $v2) {
                if ($v2->user_id == $v1->users_id_fk) {
                    $status = true;
                    break;
                }
            }
            if (!$status) {
                $employeesWhoHasAdvanceSalaryLoanProblem[] = $v1;
            }
            // dd( $status);
        }
        dd($employeesWhoHasAdvanceSalaryLoanProblem);
    }

    /**
     *
     * Get Job Duration
     *
     */
    public static function getJobDurationInDescriptiveFormat($employee)
    {
        $date1 = \Carbon\Carbon::parse($employee->organization->joining_date);
        $date2 = \Carbon\Carbon::parse($employee->organization->terminate_resignation_date);
        return $date1->diff($date2)->format('%y years, %m months and %d days');
    }

    /**
     *
     * Get ordinal number
     * @param int $num
     *
     *
     */
    public static function getOrdinalNumber($num)
    {
        if (!in_array(($num % 100), array(11, 12, 13))) {
            switch ($num % 10) {
                // Handle 1st, 2nd, 3rd
                case 1:
                    return $num . 'st';
                case 2:
                    return $num . 'nd';
                case 3:
                    return $num . 'rd';
            }
        }
        return $num . 'th';
    }


    /**
     *
     * Getting employee
     *
     * @param int $companyId
     * @param int $projectId
     * @param int $branchId
     *
     * @return EmployeeGeneralInfo $employeeList
     *
     */
    public static function getEmployeeByCompanyProjectAndBranchId($companyId, $projectId, $branchId)
    {
        $employeeList = EmployeeGeneralInfo::join('hr_emp_org_info', 'hr_emp_general_info.id', '=', 'hr_emp_org_info.emp_id_fk')
            ->where('hr_emp_org_info.status', 'Active')
            ->where('hr_emp_org_info.job_status', 'Present')
            ->where(function ($q) use ($companyId, $projectId, $branchId) {
                if ($companyId) {
                    $q->where('hr_emp_org_info.company_id_fk', $companyId);
                }
                if ($projectId) {
                    $q->where('hr_emp_org_info.project_id_fk', $projectId);
                }
                if ($branchId) {
                    $q->where('hr_emp_org_info.branch_id_fk', $branchId);
                }
            })->select(
                'hr_emp_general_info.*',
                'hr_emp_org_info.company_id_fk',
                'hr_emp_org_info.project_id_fk',
                'hr_emp_org_info.branch_id_fk'
            )->orderBy('emp_id', 'asc')->get();

        return $employeeList;
    }

    /**
     *
     * Get Ledger
     *
     * @param GnrProject $project
     * @param GnrProjectType $projectType
     * @param StaffType $staffType
     * @param TransactionHead $transactionHead
     *
     * @return AddLedger $ledger
     *
     */
    public static function getLedgerByProjectProjectTypeStaffTypeAndTransactionHeadId($projectId, $projectTypeId, $staffTypeId, $transactionHeadId)
    {
        $ledger = null;
        $item = AutoVoucherSettingItem::join('hr_settings_auto_vouchers', 'hr_settings_auto_vouchers_items.hr_settings_auto_vouchers_id_fk', '=', 'hr_settings_auto_vouchers.id')
            ->where('hr_settings_auto_vouchers.project_id_fk', $projectId)
            ->where('hr_settings_auto_vouchers_items.project_type_id_fk', $projectTypeId)
            ->where('hr_settings_auto_vouchers_items.hr_staff_types_id_fk', $staffTypeId)
            ->where('hr_settings_auto_vouchers_items.hr_transaction_heads_id_fk', $transactionHeadId)
            ->first();

        $ledger = $item ? AddLedger::find($item->acc_account_ledger_id_fk_for_ledger_code_pri) : null;
        return $ledger;
    }

    /**
     *
     * @param Builder $query
     * @return void
     *
     */
    public static function branchScope($query)
    {
        // Current user
        $user = Auth::user();

        // Head Ofice Branch
        $headOfficeBranch = GnrBranch::where('name', 'Head Office')->first();

        // When user branch is not Head office branch
        if ($user->branchId != $headOfficeBranch->id) {
            $query->where('branch_id_fk', '=', $user->branchId);
        }
    }

    /**
     *
     * Get General Function by name
     * @param string $functionName
     * @return GnrFunction $gnrFunction
     */
    public static function getGnrFunctionByName($functionName)
    {
        $gnrFunction = GnrFunction::where('reporting_boss_status', 1)
            ->where('name', $functionName)
            ->first();
        return $gnrFunction;
    }

    /**
     *
     * Get Employee By user
     * @param User $user
     * @return EmployeeGeneralInfo $employee
     */
    public static function getEmployeeByUser($user)
    {
        $employee = null;

        if ($user) {
            $employee = $user->employee;
        }

        return $employee;
    }

    /**
     *
     * Get Organization By user
     * @param User $user
     * @return EmployeeOrganizationInfo $organization
     */
    public static function getOrganizationByUser($user)
    {
        $employee = null;
        $organization = null;

        $employee = static::getEmployeeByUser($user);

        if ($employee) {
            $organization = $employee->organization;
        }

        return $organization;
    }

    /**
     *
     * Get Position By user
     * @param User $user
     * @return Position $position
     */
    public static function getPositionByUser($user)
    {
        $organization = null;
        $position = null;

        $organization = static::getOrganizationByUser($user);

        if ($organization) {
            $position = $organization->position;
        }

        return $position;
    }

    /**
     *
     *  Get employee by designation
     *
     * @param int $designationId
     * @return EmployeeGeneralInfo $employeeGeneralInfo
     *
     */
    public static function getEmployeeByDesignationId($designationId)
    {
        $employeeGeneralInfoStack = null;
        $employeeGeneralInfoStack[0] = 'Select any';

        $employeeIdContainer = EmployeeOrganizationInfo::where('position_id_fk', $designationId)
            ->pluck('emp_id_fk')
            ->toArray();
        $employees = EmployeeGeneralInfo::whereIn('id', $employeeIdContainer)
            ->get();

        foreach ($employees as $employee) {
            $employeeGeneralInfoStack[$employee->id] = $employee->emp_id . ' - ' . $employee->emp_name_english;
        }

        if (!$employeeGeneralInfoStack) {
            $employeeGeneralInfoStack = [];
        }
        return $employeeGeneralInfoStack;
    }


    /**
     *
     *  Benifit is stop or not
     *
     * @param int $userId
     * @param int $loanId
     * @param string $salaryMonth
     * @param string $type
     * @return boolean $hasStopBenifit
     *
     */
    public static function hasStopBenifit($userId, $loanId, $salaryMonth, $type)
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

    /**
     *
     * Employee fiscal year
     *
     * @param EmployeeGeneralInfo $employee
     * @param string $date
     * @return object
     *
     */
    public static function getFiscalYearByDateAndCompanyId($date, $companyId)
    {
        return DB::table('gnr_fiscal_year')
            ->where('fyStartDate', '<=', $date)
            ->where('fyEndDate', '>=', $date)
            ->where('companyId', $companyId)
            ->first();
    }

    /**
     *
     * Employee due slary month
     *
     * @param EmployeeGeneralInfo $employee
     * @param string $lastSalaryDate
     * @return string | null $lastSalaryDateForFinalPayment
     *
     */
    public static function employeeDueSalaryMonthForFinalPayment($employee, $lastSalaryDate)
    {
        $lastSalaryDateForFinalPayment = null;
        $organization = $employee->organization;

        if (
            $organization->terminate_resignation_date == '' ||
            $organization->terminate_resignation_date == '0000-00-00'
        ) {
            return;
        }

        if ($organization) {
            $lastSalaryDate = date('Y-m-d', strtotime($lastSalaryDate));
            $lastSalaryMonth = date('Y-m', strtotime($lastSalaryDate));

            $lastWorkingDate = date('Y-m-d', strtotime($organization->terminate_resignation_date . ' -1 Day'));
            $lastWorkingMonth = date('Y-m', strtotime($organization->terminate_resignation_date . ' -1 Day'));

            if ($lastSalaryMonth != $lastWorkingMonth) {
                $lastSalaryDateForFinalPayment = $lastWorkingDate;
            }
        }

        return $lastSalaryDateForFinalPayment;
    }

    /**
     *
     * User last given salary
     *
     * @param EmployeeGeneralInfo $employee
     * @return array $salaryInfo
     *
     */
    public static function employeeLastGivenSalary($employee)
    {
        $userId = $employee->user->id;
        $salaryInfo = null;

        $salarySheet = SalaryGenerate::whereRaw('contents LIKE :uid', [':uid' => '%"user_id":' . $userId . ',"payment_status":"Paid",%'])
            ->orderby('id', 'desc')
            ->first();

        if ($salarySheet) {
            $contents = json_decode($salarySheet->contents, true);
            foreach ($contents as $info) {
                if ($info['user_id'] == $userId) {
                    $salaryInfo = $info;
                    break;
                }
            }
            $salaryInfo['month'] = $salarySheet->target_month;
        }

        if (empty($salaryInfo)) {
            $salaryInfo = [];
        }

        return $salaryInfo;
    }

    /**
     *
     * User last given salary benifit type A
     *
     * @param EmployeeGeneralInfo $employee
     * @return array $benifitType
     *
     */
    public static function employeeLastGivenSalaryBenifitTypeA($employee)
    {
        $pattern = "/benefit-a-./";
        $benifitType = null;
        $salaryInfo = self::employeeLastGivenSalary($employee);

        foreach ($salaryInfo as $key => $value) {
            if (preg_match($pattern, $key)) {
                $benifitType[$key] = $value;
            }
        }

        if (empty($benifitType)) {
            $benifitType = [];
        }

        return $benifitType;
    }

    /**
     * User last given salary benifit type B
     *
     * @param EmployeeGeneralInfo $employee
     * @return array $benifitType
     *
     */
    public static function employeeLastGivenSalaryBenifitTypeB($employee)
    {
        $pattern = "/benefit-b-./";
        $benifitType = null;
        $salaryInfo = self::employeeLastGivenSalary($employee);
        // dd($salaryInfo);

        foreach ($salaryInfo as $key => $value) {
            if (preg_match($pattern, $key)) {
                $benifitType[$key] = $value;
            }
        }

        if (empty($benifitType)) {
            $benifitType = [];
        }

        return $benifitType;
    }

    /**
     *
     * Array element serach by matching pattern
     *
     * @param array $arr
     * @return string $pattern
     *
     */
    public static function getElementsOf($arr, $pattern)
    {
        $serchedElement = null;

        foreach ($arr as $key => $value) {
            if (preg_match($pattern, $key)) {
                $serchedElement[$key] = $value;
            }
        }

        if (empty($serchedElement)) {
            $serchedElement = [];
        }

        return $serchedElement;
    }

    /**
     *
     * Employee eps effect or not
     *
     * @param EmployeeGeneralInfo $employee
     * @return boolean $isEffect
     *
     */
    public static function isEpsEffectToEmployee($employee)
    {
        $isEffect = false;
        $lastWorkingDate = date("Y-m-d", strtotime($employee->organization->terminate_resignation_date . ' -1 Day'));
        $effectDate = date("Y-m-d", strtotime("2018-07-01"));
        if ($lastWorkingDate > $effectDate) {
            $isEffect = true;
        }
        return $isEffect;
    }

    /**
     *
     * User basic salary in target month
     *
     * @param User $user
     * @param string $targetMonth
     * @return float $basicSalary
     *
     */
    public static function getUserBasicSalaryByTagetMonth($user, $targetMonth)
    {

        $salaryStructure = null;
        $yearlySalaryStructure = null;

        $organization = $user->organization;

        $firstPromotion = PromotionIncrement::where('users_id_fk', $user->id)->first();

        if ($firstPromotion) {
            if ($targetMonth < $firstPromotion->effect_month) {
                $previousData = json_decode($firstPromotion->previous_data, true);
                $salaryStructure = SalaryStructure::where('company_id_fk', $organization->company_id_fk)
                    ->where('project_id_fk', $organization->project_id_fk)
                    ->where('grade_id_fk', $previousData['value']['grade']['id'])
                    ->where('level_id_fk', $previousData['value']['level']['id'])
                    ->where('fiscal_year_fk', $previousData['value']['fiscal_year']['id'])
                    ->where('position_id_fk', 'LIKE', "%{$previousData['value']['position']['id']}%")
                    ->where('recruitment_type_fk', 'LIKE', "%{$previousData['value']['recruitment_type']['id']}%")
                    ->orderby('id', 'desc')
                    ->first();

                $yearlySalaryStructure = SalaryStructure::getYearlySalaryStructure(
                    $previousData['value']['salary_increment_year']['id'] - 1,
                    $salaryStructure->salaryYearlyCal
                );
            } else {
                $promotionIncrement = DB::table('hr_promotion_increment')
                    ->where('users_id_fk', $user->id)
                    ->where('effect_month', '<=', $targetMonth)
                    ->get()
                    ->last();

                $salaryStructure = SalaryStructure::where('company_id_fk', $organization->company_id_fk)
                    ->where('project_id_fk', $organization->project_id_fk)
                    ->where('grade_id_fk', $promotionIncrement->grade_id_fk)
                    ->where('level_id_fk', $promotionIncrement->level_id_fk)
                    ->where('fiscal_year_fk', $promotionIncrement->fiscal_year_fk)
                    ->where('position_id_fk', 'LIKE', "%{$promotionIncrement->position_id_fk}%")
                    ->where('recruitment_type_fk', 'LIKE', "%{$promotionIncrement->recruitment_type_fk}%")
                    ->orderby('id', 'desc')
                    ->first();
                $yearlySalaryStructure = SalaryStructure::getYearlySalaryStructure(
                    $promotionIncrement->salary_increment_year - 1,
                    $salaryStructure->salaryYearlyCal
                );
            }
        } else {
            $salaryStructure = SalaryStructure::where('company_id_fk', $organization->company_id_fk)
                ->where('project_id_fk', $organization->project_id_fk)
                ->where('grade_id_fk', $organization->grade)
                ->where('level_id_fk', $organization->level_id_fk)
                ->where('fiscal_year_fk', $organization->fiscal_year_fk)
                ->whereRaw('FIND_IN_SET(?,position_id_fk)', [$organization->position_id_fk])
                ->whereRaw('FIND_IN_SET(?,recruitment_type_fk)', [$organization->recruitment_type])
                ->orderby('id', 'desc')
                ->first();

            $yearlySalaryStructure = $salaryStructure
                ? SalaryStructure::getYearlySalaryStructure($organization->salary_increment_year - 1, $salaryStructure->salaryYearlyCal)
                : null;
        }


        $basicSalary = round(floatval(
            SalaryStructure::getPaticularItemFromYearlySalaryStructure(
                'Total Basic',
                $yearlySalaryStructure
            )
        ));

        //        if ($user->id == 52) {
        //            dd($basicSalary, $salaryStructure, $salaryStructure->salaryYearlyCal,$yearlySalaryStructure, $promotionIncrement);
        //        }

        return $basicSalary;
    }


    /**
     *
     * Employee Salary Structure
     *
     * @param EmployeeGeneralInfo $employee
     * @param string $targetDate
     * @return string $data
     *
     */
    public static function getEmployeeSalaryStructureByTagetDate($employee, $targetDate)
    {
        $user = $employee->user;
        $orgInfo = $employee->organization;
        $data = [];
        $promotionIncrement = Helper::getEmployeePromotionIncrementByTargetDate($employee, $targetDate);

        if ($promotionIncrement) {
            $previousData = json_decode($promotionIncrement->previous_data, true);
            $q = SalaryStructure::where('company_id_fk', $orgInfo->company_id_fk)
                ->where('project_id_fk', $orgInfo->project_id_fk)
                ->where('grade_id_fk', $previousData['value']['grade']['id'])
                ->where('level_id_fk', $previousData['value']['level']['id'])
                ->where('fiscal_year_fk', $previousData['value']['fiscal_year']['id'])
                ->whereRaw('FIND_IN_SET(?,position_id_fk)', [$orgInfo->position_id_fk])
                ->whereRaw('FIND_IN_SET(?,recruitment_type_fk)', $previousData['value']['recruitment_type']['id'])
                ->orderby('id', 'desc')
                ->limit(1)
                ->first();
        } else {
            $q = SalaryStructure::where('company_id_fk', $orgInfo->company_id_fk)
                ->where('project_id_fk', $orgInfo->project_id_fk)
                ->where('grade_id_fk', $orgInfo->grade)
                ->where('level_id_fk', $orgInfo->level_id_fk)
                ->where('fiscal_year_fk', $orgInfo->fiscal_year_fk)
                ->whereRaw('FIND_IN_SET(?,position_id_fk)', [$orgInfo->position_id_fk])
                ->whereRaw('FIND_IN_SET(?,recruitment_type_fk)', [$orgInfo->recruitment_type])
                ->orderby('id', 'desc')
                ->limit(1)
                ->first();
        }

        $data = $q;
        if (!$data) {
            $data = [];
        }

        return $data;
    }

    /**
     *
     * Employee Promotion increment
     *
     * @param EmployeeGeneralInfo $employee
     * @param string $targetDate
     * @return object $promotionIncrement
     *
     */
    public static function getEmployeePromotionIncrementByTargetDate($employee, $targetDate)
    {
        $promotionIncrement = null;
        $user = $employee->user;

        $promotionIncrement = DB::table('hr_promotion_increment')
            ->where('users_id_fk', $user->id)
            ->where('effect_month', '>', $targetDate)
            ->first();

        return $promotionIncrement;
    }

    /**
     *
     * Count day by date difference
     *
     * @param string $from
     * @param string $to
     * @return string $offset
     *
     */
    public static function howDays($from, $to)
    {
        $first_date = strtotime($from);
        $second_date = strtotime($to);
        $offset = $second_date - $first_date;
        return floor($offset / 60 / 60 / 24);
    }

    /**
     *
     * Get user company info
     *
     * @return object $company
     *
     */
    public static function authUserCompany()
    {
        $company = null;
        $companyId = Auth::user() ? Auth::user()->company_id_fk : null;
        if ($companyId) {
            $company = DB::table('gnr_company')
                ->where('id', $companyId)
                ->first();
        }

        return $company;
    }

    /**
     *
     * Get employee job duration interval
     *
     * @param int $id
     * @param string $endDate
     * @return DateInterval $jobDurationInterval
     *
     */
    public static function getJobDurationIntervalWithTargetDateByEmployeeId($id, $endDate)
    {
        $organization = self::getOrganizationByEmployeeId($id);
        $jobDurationInterval = null;
        if ($organization) {

            if ($organization->job_status == 'Present') {
                $jobDurationInterval = self::dateDiff($organization->joining_date, $endDate);
                // dd($jobDurationInterval);
            }

            if ($organization->job_status == 'Resign') {

                if ($organization->terminate_resignation_date < $endDate) {
                    $resignDate = date('Y-m-d', strtotime($organization->terminate_resignation_date));
                } else {
                    $resignDate = date('Y-m-d', strtotime($endDate));
                }
                $jobDurationInterval = self::dateDiff($organization->joining_date, $resignDate);
            }

            if ($organization->job_status == 'Terminate') {

                if ($organization->terminate_resignation_date < $endDate) {
                    $resignDate = date('Y-m-d', strtotime($organization->terminate_resignation_date));
                } else {
                    $resignDate = date('Y-m-d', strtotime($endDate));
                }
                $jobDurationInterval = self::dateDiff($organization->joining_date, $resignDate);
            }
        }
        return $jobDurationInterval;
    }

    /**
     *
     * Get employee job duration interval
     *
     * @param DateInterval $interval
     * @return string $formatedDuration
     *
     */
    public static function getFormatedJobDuration(DateInterval $interval)
    {
        $formatedDuration = '';

        if ($interval->y > 1) {
            $formatedDuration = $interval->y . ' years and';
        } else {
            $formatedDuration = $interval->y . ' year and';
        }
        if ($interval->m > 1) {
            $formatedDuration = $formatedDuration . ' ' . $interval->m . ' months';
        } else {
            $formatedDuration = $formatedDuration . ' ' . $interval->m . ' month';
        }

        return $formatedDuration;
    }

    /**
     *
     * Get employee job duration month by user id and target date
     *
     * @param int $id
     * @param string $endDate
     * @return int $months
     *
     */
    public static function getJobDurationMonthWithTargetDateByUserId($id, $endDate)
    {
        $months = 0;
        $employeeOrganizationInfo = self::getOrganizationByUserId($id);
        $joiningDateStr = '';
        $terminateDateStr = '';
        $resignDateStr = '';
        if ($employeeOrganizationInfo) {

            $joiningDate = new DateTime($employeeOrganizationInfo->joining_date);
            $resignDate = new DateTime($employeeOrganizationInfo->terminate_resignation_date);

            // set resign date
            // $resignInfo= DB::table('hr_resign_info')
            //     ->where('users_id_fk', $id)
            //     ->first();
            // if( $resignInfo ) {
            //     $resignDate  = new DateTime($resignInfo->resign_date);
            // } else {
            //     $resignDate  = new DateTime($employeeOrganizationInfo->terminate_resignation_date);
            // }

            // modify joining date
            if ($joiningDate) {
                if ($joiningDate->format('d') > 1) {
                    // increase one month
                    $joiningDateStr = $joiningDate->modify('first day of next month')->format('Y-m-d');
                } else {
                    $joiningDateStr = $employeeOrganizationInfo->joining_date;
                }
            }

            // modify resign date
            if ($resignDate->format('d') < $resignDate->format('t')) {
                // decrease one month
                $resignDateStr = $resignDate->modify('last day of previous month')->format('Y-m-d');
            } else {
                $resignDateStr = $employeeOrganizationInfo->terminate_resignation_date;
            }

            if ($employeeOrganizationInfo->job_status == 'Present') {
                $months = self::getDateDiffInMonth($joiningDateStr, $endDate);
                return $months;
            }

            if ($employeeOrganizationInfo->job_status == 'Resign') {
                $months = self::getDateDiffInMonth($joiningDateStr, $resignDateStr);
                return $months;
            }

            if ($employeeOrganizationInfo->job_status == 'Terminate') {
                $months = self::getDateDiffInMonth($joiningDateStr, $resignDateStr);
                return $months;
            }
        }
        return $months;
    }

    /**
     *
     * Get total month from date interval
     *
     * @param DateInterval $interval
     * @return int
     *
     */
    public static function getTotalMonthFromInterval(DateInterval $interval)
    {
        // dd($interval);
        return intval($interval->days / 30);
    }

    /**
     * Calculates how many months is past between two timestamps.
     *
     * @param int $start Start timestamp.
     * @param int $end Optional end timestamp.
     *
     * @return int
     */
    public static function getDateDiffInMonth($start, $end)
    {
        $date1 = Carbon::parse($start);
        $date2 = Carbon::parse($end);
        return $date1->diffInMonths($date2);
    }

    /**
     *
     * convert day to year
     *
     * @param int $days
     * @return int $years_remaining
     *
     */
    public static function daysToYearsCalculate($days)
    {
        //divide by 365 and throw away the remainder
        $years_remaining = intval($days / 365);
        $days_remaining = $days % 365;

        return $years_remaining;
    }

    /**
     *
     * Get Date difference
     *
     * @param string $startDate
     * @param string $endDate
     * @return DateInterval $endDate
     *
     */
    public static function dateDiff($startDate, $endDate)
    {
        $startDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);
        return $endDate->diff($startDate);
    }

    /**
     * Get paginate from collection.
     *
     * @param array|Collection $items
     * @param int $perPage
     * @param int $page
     * @param array $options
     *
     * @return LengthAwarePaginator
     */
    public static function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    /**
     *
     * Get organization by user id
     *
     * @param int $id
     * @return mix
     *
     */
    public static function getOrganizationByUserId($id)
    {
        $employee = self::getEmployeeByUserId($id);

        if ($employee) {
            return self::getOrganizationByEmployeeId($employee->id);
        }

        return null;
    }

    /**
     *
     * Get employee by user id
     *
     * @param int $id
     * @return mix
     *
     */
    public static function getEmployeeByUserId($id)
    {

        $user = DB::table('users')->where('id', '=', $id)->first();
        if ($user) {
            return self::getEmployeeById($user->emp_id_fk);
        }

        return null;
    }

    /**
     *
     * Get employee by id
     *
     * @param int $id
     * @return mix
     *
     */
    public static function getEmployeeById($id)
    {
        return DB::table('hr_emp_general_info')->where('id', '=', $id)->first();
    }

    /**
     *
     * Get organization by employee id
     *
     * @param int $id
     * @return mix
     *
     */
    public static function getOrganizationByEmployeeId($id)
    {
        $employee = self::getEmployeeById($id);

        if ($employee) {
            return DB::table('hr_emp_org_info')->where('emp_id_fk', '=', $employee->id)->first();
        }

        return null;
    }

    /**
     *
     * Get basic salary by user id
     *
     * @param int $id
     * @return float $employeeBasic
     *
     */
    public static function getBasicSalaryByUserId($id)
    {
        $employeeOrganization = self::getOrganizationByUserId($id);
        $employeeBasic = 0.00;
        // dd($employeeOrganization);
        if ($employeeOrganization) {

            $salaryStructure = null;
            $salaryStructureYearlyCal = null;

            $salaryStructure = DB::table('hr_salary_structure')
                ->where('recruitment_type_fk', $employeeOrganization->recruitment_type)
                ->where('project_id_fk', $employeeOrganization->project_id_fk)
                ->where('position_id_fk', 'Like', '%' . $employeeOrganization->position_id_fk . '%')
                ->where('grade_id_fk', $employeeOrganization->grade)
                ->where('level_id_fk', $employeeOrganization->level_id_fk)
                ->where('fiscal_year_fk', $employeeOrganization->fiscal_year_fk)
                ->first();
            // dd($salaryStructure);
            if ($salaryStructure) {
                $salaryStructureYearlyCal = DB::table('hr_salary_structure_yearly_cal')
                    ->where('salary_struc_id_fk', $salaryStructure->id)
                    ->first();
            }
            if ($salaryStructureYearlyCal) {
                $yearStacks = json_decode($salaryStructureYearlyCal->contents);
                // get the basic salary
                foreach ($yearStacks as $year) {
                    if ($employeeOrganization->salary_increment_year == $year[0]->value) {
                        $employeeBasic = $year[3]->value;
                        break;
                    }
                }
            }
        }
        return $employeeBasic;
    }

    /**
     *
     * Get basic salary by user id with target date
     *
     * @param EmployeeGeneralInfo $employee
     * @param string $targetDate
     * @return float $employeeBasicSalary
     *
     */
    public static function getEmployeeBasicSalaryWithTargetDate($employee, $targetDate)
    {
        $employeeBasicSalary = 0.00;
        $organization = $employee->organization;
        $salaryStructure = null;
        $yearlySalaryStructure = null;
        $targetDate = date('Y-m-t', strtotime($targetDate));

        if ($employee) {
            $salaryStructure = self::getEmployeeSalaryStructureByTagetDate($employee, $targetDate);
        }

        if ($salaryStructure) {
            $promotionIncrement = self::getEmployeePromotionIncrementByTargetDate($employee, $targetDate);
            if ($promotionIncrement) {
                $previousData = json_decode($promotionIncrement->previous_data, true);
                $yearlySalaryStructure = SalaryStructure::getYearlySalaryStructure(
                    intval($previousData['value']['salary_increment_year']['name']) - 1,
                    $salaryStructure->salaryYearlyCal
                );
            } else {
                $yearlySalaryStructure = SalaryStructure::getYearlySalaryStructure(
                    intval($organization->salary_increment_year) - 1,
                    $salaryStructure->salaryYearlyCal
                );
            }
        }
        $employeeBasicSalary = round(floatval(
            SalaryStructure::getPaticularItemFromYearlySalaryStructure('Total Basic', $yearlySalaryStructure)
        ));

        return $employeeBasicSalary;
    }

    /**
     *
     * Get Area of a employee by branch id
     *
     * @param int $bid
     * @return GnrArea $model
     *
     */
    public static function getArea($bid)
    {
        $model = GnrArea::whereRaw('branchId Like :bid', [':bid' => '%"' . $bid . '"%'])->first();
        return $model;
    }

    /**
     *
     * Get Zone of a employee by branch id
     *
     * @param int $bid
     * @return GnrZone $zone
     *
     */
    public static function getZone($bid)
    {
        $area = GnrArea::whereRaw('branchId Like :bid', [':bid' => '%"' . $bid . '"%'])->first();
        if (!empty($area)) {
            $zone = GnrZone::whereRaw('areaId Like :aid', [':aid' => '%"' . $area->id . '"%'])->first();
            return $zone;
        }
        return null;
    }

    /**
     *
     * Get Employee job duration in year
     *
     * @param EmployeeGeneralInfo $employee
     * @param string $targetDate
     * @return int $year
     *
     */
    public static function getEmployeeJobDurationInYear($employee, $targetDate)
    {
        $year = 0;
        $organization = $employee->organization;

        if ($organization) {

            $startDate = Carbon::parse($organization->joining_date);
            $endDate = Carbon::parse($targetDate)->addDay();

            // Present employee
            if ($organization->job_status == 'Present') {
                $year = $startDate->diffInYears($endDate);
            }

            // Resign employee
            if ($organization->job_status == 'Resign' || $organization->job_status == 'Terminate') {
                $terminateOrResignationDate = Carbon::parse($organization->terminate_resignation_date)->addDay();
                if ($terminateOrResignationDate->lessThanOrEqualTo($endDate)) {
                    $year = $startDate->diffInYears($terminateOrResignationDate);
                } else {
                    $year = $startDate->diffInYears($endDate);
                }
            }
        }
        return $year;
    }

    /**
     * Employee is resigned in target month or not
     *
     * @param EmployeeGeneralInfo $employee
     * @param string $targetMonth
     *
     * @return boolean $isResignedInTargetmonth
     */
    public static function isEmployeeResigntInTargetMonth($employee, $targetMonth)
    {
        $isResignedInTargetmonth = false;
        $targetMonth = date('Y-m', strtotime($targetMonth));

        $organization = $employee->organization;
        $user = $employee->user;

        $targetMonthFirstDay = date('Y-m-01', strtotime($targetMonth));
        $targetMonthLastDay = date('Y-m-t', strtotime($targetMonth));

        if ($organization->job_status == 'Resign') {
            $resignInfo = ResignInfo::where('users_id_fk', $user->id)
                ->where('status', 'Approved')
                ->where('effect_date', '>=', $targetMonthFirstDay)
                ->where('effect_date', '<=', $targetMonthLastDay)
                ->first();
            if ($resignInfo) {
                $isResignedInTargetmonth = true;
            }
        }

        if ($organization->job_status == 'Terminate') {
            $terminateInfo = TerminateInfo::where('users_id_fk', $user->id)
                ->where('status', 'Approved')
                ->where('effect_date', '>=', $targetMonthFirstDay)
                ->where('effect_date', '<=', $targetMonthLastDay)
                ->first();
            if ($terminateInfo) {
                $isResignedInTargetmonth = true;
            }
        }

        if ($organization->job_status == 'Retirement') {
            $terminateInfo = RetirementInfo::where('users_id_fk', $user->id)
                ->where('status', 'Approved')
                ->where('effect_date', '>=', $targetMonthFirstDay)
                ->where('effect_date', '<=', $targetMonthLastDay)
                ->first();
            if ($terminateInfo) {
                $isResignedInTargetmonth = true;
            }
        }

        return $isResignedInTargetmonth;
    }

    /**
     * Get Employee job duration in year
     *
     * @param EmployeeGeneralInfo $employee
     * @param false $isEqual
     *
     */
    public static function isEqualEmployeeResignAndTargetMonth($employee, $targetMonth)
    {
        $isEqual = true;
        $resignMonth = date('Y-m', strtotime($employee->organization->terminate_resignation_date));
        $targetMonth = date('Y-m', strtotime($targetMonth));

        if ($resignMonth != $targetMonth) {
            $isEqual = false;
        }

        return $isEqual;
    }


    /**
     * Employee is asigned or not in samity in a date
     *
     * @param EmployeeGeneralInfo $employee
     * @param string $date
     *
     * @return boolean $isAssigned
     */
    public static function isAssignedInSamityOfTheEmployeeInTargetDate($employee, $date)
    {
        $isAssigned = false;

        if ($employee && $date) {
            $isAssigned = DB::table('mfn_samity')
                ->join('mfn_samity_field_officer_change', 'mfn_samity_field_officer_change.fieldOfficerId', 'mfn_samity.fieldOfficerId')
                ->where('mfn_samity.softDel', 0)
                ->where('mfn_samity.fieldOfficerId', $employee->id)
                ->where('mfn_samity_field_officer_change.effectiveDate', '>=', date('Y-m-d', strtotime($date)))
                ->get()
                ->filter(function ($item) use ($date) {
                    if ($item->closingDate == '0000-00-00') {
                        return true;
                    } elseif ($item->closingDate >= date('Y-m-d', strtotime($date))) {
                        return true;
                    }
                    return false;
                })->first()
                ? true
                : false;
        }

        return $isAssigned;
    }


}
