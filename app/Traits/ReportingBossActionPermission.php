<?php

namespace App\Traits;

use App\ConstValue;
use App\hr\FunctionList;
use App\hr\ReportingBossEmployee;
use App\hr\ReportingBossFunction;
use App\hr\ReportingBossHistory;
use App\Service\ReportingBossService;
use App\User;
use Auth;
use \Carbon\Carbon;
use DB;

/**
 * Action permission utility for reporting boss
 */
trait ReportingBossActionPermission
{
    /**
     * Ordinal numbers by index
     *
     * @param int $index
     *
     * @return string
     */
    public function getOrdinalNumberByIndex($index)
    {
        return config('reportingboss.ordinal_numbers')[$index];
    }

    /**
     * All Ordinal numbers
     *
     * @return array
     */
    public function getOrdinalNumber()
    {
        return config('reportingboss.ordinal_numbers');
    }

    /**
     * Reject button permission
     *
     * @param User $bossUser
     *
     * @return boolean
     */
    public function hasRejectButtonPermission($bossUser)
    {
        // We assume the authentication user is boss user
        $bossUser = $bossUser;
        return $this->users_id_fk != $bossUser->id;
    }

    /**
     * Proceed button permission
     *
     * @param User $bossUser
     * @param ReportingBossFunction $function
     *
     * @return boolean
     */
    public function hasProceedButtonPermission($bossUser, $function)
    {
        $reportingBossService = new ReportingBossService();

        // We assume the authentication user is boss user
        $bossUser = $bossUser;
        $employeeUser = $this->user;

        // boss user department //
        $bossUserDepartment = (int) DB::table('hr_emp_org_info')->where('emp_id_fk', $bossUser->emp_id_fk)->value('department');

        $reportingBossEmployee = $reportingBossService->findSettingAccordingToEmployee($employeeUser, $function, $this);

        $bosses = $reportingBossEmployee->bosses ?? null;

        if ($bossUser->id == ConstValue::USER_ID_SUPER_ADMIN) {
            return true;
        }

        if ($bosses->count() > 0) {
            if ($bosses->last()->position_id_fk != $bossUser->currentPosition()->id || $bosses->last()->department_id_fk != $bossUserDepartment) {
                return true;
            }
        }

        return false;
    }

    /**
     * Approve button permission
     *
     * @param User $bossUser
     * @param ReportingBossFunction $function
     *
     * @return boolean
     */
    public function hasApproveButtonPermission($bossUser, $function)
    {
        $reportingBossService = new ReportingBossService();

        $employeeUser = $this->user;
        // boss user department //
        $bossUserDepartment = (int) DB::table('hr_emp_org_info')->where('emp_id_fk', $bossUser->emp_id_fk)->value('department');

        $reportingBossEmployee = $reportingBossService->findSettingAccordingToEmployee($employeeUser, $function, $this);

        $bosses = $reportingBossEmployee->bosses ?? null;

        if ($bossUser->id == ConstValue::USER_ID_SUPER_ADMIN) {
            return true;
        }

        if ($bosses->count() > 0) {
            if ($bosses->last()->position_id_fk == $bossUser->currentPosition()->id && $bosses->last()->department_id_fk == $bossUserDepartment) {
                return true;
            }
        }

        return false;
    }

    /**
     * Comment Box permission of boss employee
     *
     * @param User $authUser
     * @param ReportingBossEmployee $reportingBossEmployee
     *
     * @return boolean
     */
    public function hasPermissionToSeeCommentBox($authUser)
    {
        $bossUser = $authUser;
        if ($bossUser->id == ConstValue::USER_ID_SUPER_ADMIN) {
            return true;
        }

        if($this->status == 'Approved'){
            return false;
        }

        return true;
    }

    /**
     * View file action buttons permission
     *
     * @param User $authUser
     *
     * @return boolean
     */
    public function hasPermissionToSeeViewActionButton($authUser)
    {
        // We assume the authentication user is boss user
        $bossUser = $authUser;

        $reportingBossHistory = ReportingBossHistory::where('module_functions_entity_name', $this['table'])
            ->where('module_functions_entity_id', $this->id)
            ->get();

        // Specific Level Condition 1
        if ($this->users_id_fk == $bossUser->id) {
            return false;
        }


        // Specific Level Condition 2
        if ($bossUser->id == ConstValue::USER_ID_SUPER_ADMIN) {
            return true;
        }


        // Specific Level Condition 3 deprecated
      /*  if ($reportingBossHistory->contains('boss_users_id_fk', $bossUser->id)) {
            return false;
        }
        */

        // General Condition
        if (!$reportingBossHistory->contains('status', 'Approved')) {
            return true;
        }

        return false;
    }

}
