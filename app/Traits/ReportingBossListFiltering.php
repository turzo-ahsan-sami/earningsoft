<?php

namespace App\Traits;

use App\ConstValue;
use App\gnr\GnrArea;
use App\gnr\GnrBranch;
use App\gnr\GnrRegion;
use App\gnr\GnrResponsibility;
use App\gnr\GnrZone;
use App\hr\EmployeeOrganizationInfo;
use App\hr\ReportingBossEmployee;
use App\hr\ReportingBossHistory;
use App\hr\ReportingBossRule;
use App\Service\ReportingBossService;
use App\Service\Service;
use App\User;
use Auth;

trait ReportingBossListFiltering
{

    /**
     * Model filtering by boss branch wise
     *
     * @param $query
     * @return ReportingBossListFiltering
     */
    public function queryBranchWiseFilter($query)
    {
        $bossUser = Auth::user();
        $bossUserOrganization = $bossUser->employee->organization;
        $bossUserBranch = $bossUserOrganization->branch;

        $query->orderByRaw("FIELD({$this['table']}.status , 'Pending', 'Processing', 'Rejected', 'Approved') ASC");

        if ($bossUserBranch->id == ConstValue::BRANCH_ID_HEAD_OFFICE) {
            $query->join('users', $this['table'] . '.users_id_fk', '=', 'users.id')
                ->join('hr_emp_org_info', 'users.emp_id_fk', '=', 'hr_emp_org_info.emp_id_fk')
                // ->where('hr_emp_org_info.job_status', 'Present')
                ->where('hr_emp_org_info.status', 'Active')
                ->select($this['table'] . '.*');
        }

        if ($bossUserBranch->id != ConstValue::BRANCH_ID_HEAD_OFFICE) {
            $query->join('users', $this['table'] . '.users_id_fk', '=', 'users.id')
                ->join('hr_emp_org_info', 'users.emp_id_fk', '=', 'hr_emp_org_info.emp_id_fk')
                // ->where('hr_emp_org_info.job_status', 'Present')
                ->where('hr_emp_org_info.status', 'Active')
                ->whereIn('hr_emp_org_info.branch_id_fk', $this->getEngagedBranchByUser($bossUser))
                ->select($this['table'] . '.*');
        }

        return $query;
    }

    /**
     * Get engaged branch of a user
     *
     * @param \App\User $bossUser
     *
     * @return array
     */
    public function getEngagedBranchByUser($bossUser)
    {
        $engagedBranchList = [];
        if ($bossUser->isSuperAdminUser() || $bossUser->isGuestUser()) {
            $engagedBranchList = GnrBranch::pluck('id')->toArray();
        } else {
            $reportingBossRule = new ReportingBossRule();
            if($reportingBossRule->isSuperBoss($bossUser)){
                $engagedBranchList = GnrBranch::pluck('id')->toArray();
            } else {
                $engagedBranchList = Service::getEngagedBranchesByUserId($bossUser->id);
            }

        }
        return $engagedBranchList;
    }

    /**
     * Has permission to the model according to boss user
     *
     * - Full
     *     - Permission Priority : 1
     * - Own
     *     - Permission Priority : 2
     * - All Data
     *     - Permission Priority : 3
     * - Other
     *     - Permission Priority : 4
     *
     * @param ReportingBossFunction $function
     *
     * @param boolean $hasAccessAllData
     *
     * @return boolean
     */
    public function hasPermission($function, $hasAccessAllData)
    {

        $authUser = Auth::user();

        // Level - 1
        // Full permission
        // All data permission or superAdmin have goes all elements
        if ($authUser->isSuperAdminUser()) {
            return true;
        }

        // Level - 2
        // Own permission
        // User can get his model
        if ($this->users_id_fk == $authUser->id) {
            return true;
        }

        // Level - 3
        // All Data
        // User can get his model and others created model
        if ($hasAccessAllData) {
            $reportingBossHistories = ReportingBossHistory::where('module_functions_entity_name', $this['table'])
                ->where('module_functions_entity_id', $this->id)
                ->get();
            if ($this->created_by == $authUser->id && $reportingBossHistories->count() == 0) {
                return true;
            }

            if ($this->status == 'Approved' || $this->status == 'Rejected') {
                return true;
            }
        }


        // Level - 4
        // Other permission
        // Approved | Rejected model find only Level 1 and 2
        if ($this->status == 'Approved' || $this->status == 'Rejected') {
            return false;
        }


        // Employee info
        $employeeUser = User::where('id', $this->users_id_fk)->first();
        $employee = $employeeUser ? $employeeUser->employee : null;
        $employeeOrganization = $employee ? $employee->organization : null;

        $employeePosition = $employeeUser->currentPosition();
        $empPositionId = $employeePosition->id;

        $employeeDepartment = $employeeOrganization->department ?? null;
        $employeeBranch = $employeeOrganization->branch ?? null;

        // Boss info
        $bossUser = $authUser;
        $bossUserEmployee = $bossUser ? $bossUser->employee : null;
        $bossUserOrganization = $bossUserEmployee ? $bossUserEmployee->organization : null;
        $bossUserPosition = $bossUser->currentPosition();

        $reportingBossEmployee = null;
        $reportingBossBosses = null;
        $reportingBossHistories = null;
        $reportingBossService = new ReportingBossService();


        if ($function && $employeePosition) {
            $reportingBossEmployee = $reportingBossService->findSettingAccordingToEmployeeAndBoss($employeeUser, $bossUser, $function, $this);
        }



        if ($reportingBossEmployee) {
            $reportingBossBosses = $reportingBossEmployee->bosses;
        }

        $gnrResponsibilityEmployeeBranchWise = null;

        // Find boss responsibility for region rule
        $bossReponsibility = GnrResponsibility::where('position_id_fk', $bossUserPosition->id)
            ->where('type_code', ConstValue::RESPONSIBILITY_TYPE_CODE_REGION)
            ->get()->filter(function ($item) use ($bossUserEmployee) {
                if ($item->emp_id_fk) {
                    if ($item->emp_id_fk == $bossUserEmployee->id) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return true;
                }
            })->first();

        // Find boss responsibility employee branch wise for region rule
        $area = GnrArea::whereRaw('branchId Like :bid', [':bid' => '%"' . $employeeBranch->id . '"%'])->first();
        if ($area) {
            $zone = GnrZone::whereRaw('areaId Like :aid', [':aid' => '%"' . $area->id . '"%'])->first();
            if ($zone) {
                $region = GnrRegion::whereRaw('zoneId Like :zid', [':zid' => '%"' . $zone->id . '"%'])->first();
                if ($region) {
                    $gnrResponsibilityEmployeeBranchWise = GnrResponsibility::whereRaw('id_list Like :rid', [':rid' => '%"' . $region->id . '"%'])
                            ->get()
                            ->where('type_code', ConstValue::RESPONSIBILITY_TYPE_CODE_REGION)
                            ->first()
                        ?? null;
                }
            }
        }

        // dd($bossReponsibility,$gnrResponsibilityEmployeeBranchWise);

        $reportingBossHistories = ReportingBossHistory::where('module_functions_entity_name', $this['table'])
            ->where('module_functions_entity_id', $this->id)
            ->get();

        $numberOfRowsReportingBossHistory = $reportingBossHistories->count();

//        if($this->id == 2164){
//            dd($reportingBossEmployee->bosses()->with(['position'])->get());
//        }

        // Other permission
        // When there has no history row
        if ($numberOfRowsReportingBossHistory < 1) {
            if ($numberOfRowsReportingBossHistory < 1) {
                foreach ($reportingBossBosses ?? [] as $boss) {
                    $searchedBossUser = $boss->getBossUserByDepartmentAndBranchAndModel(
                        $employeeDepartment,
                        $employeeBranch,
                        $this,
                        $boss->step_index
                    );
                    if ($searchedBossUser) {
                        $searchedBossUserOrg = EmployeeOrganizationInfo::where('emp_id_fk', $searchedBossUser->emp_id_fk)->first();
                        if ($searchedBossUserOrg) {

                            $searchedBossUserPositionId = $searchedBossUser->currentPosition()->id;

                            if (
                                $bossUserPosition->id == $searchedBossUserPositionId &&
                                $bossUserOrganization->department == $searchedBossUserOrg->department
                            ) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    }
                }
            }
        }


        if ($numberOfRowsReportingBossHistory > 0) {
            if ($reportingBossHistories->last()->status == 'Rejected') {
                return false;
            }
        }

        // Other permission
        // When there has history row
        if ($numberOfRowsReportingBossHistory > 0) {
            if ($reportingBossBosses) {

                $totalSteps = $reportingBossBosses->count();

                $lastReportingBossHistory = $reportingBossHistories->last();
                $currentBossHistoryStep = $reportingBossBosses
                    ->where('position_id_fk', $lastReportingBossHistory->boss_position_id_fk)
                    ->filter(function ($item) use ($lastReportingBossHistory, $bossReponsibility, $gnrResponsibilityEmployeeBranchWise) {
                        $user = User::find($lastReportingBossHistory->boss_users_id_fk);
                        if ($user) {
                            $organization = $user->organization;
                            if ($organization) {
                                if ($item->department_id_fk == $organization->department) {
                                    if ($bossReponsibility && $gnrResponsibilityEmployeeBranchWise) {
                                        if ($bossReponsibility->emp_id_fk == $gnrResponsibilityEmployeeBranchWise->emp_id_fk) {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    } else {
                                        return true;
                                    }
                                }
                            }
                        }
                        return false;
                    })
                    ->first();

                if ($currentBossHistoryStep) {
                    $nextStep = $currentBossHistoryStep->step_index + 1;
                    $nextReportingBossStep = $currentBossHistoryStep
                        ? $reportingBossBosses->where('step_index', $nextStep)->first()
                        : null;

                    $isPositionExist = $nextReportingBossStep
                        ? EmployeeOrganizationInfo::where('position_id_fk', $nextReportingBossStep->position_id_fk)->first()
                        : null;
                    $isBossExist = $nextReportingBossStep
                        ? $nextReportingBossStep->getBossUserByDepartmentAndBranchAndModel($employeeDepartment, $employeeBranch, $this)
                        : null;

                    while ($isPositionExist == null || $isBossExist == null) {
                        $nextStep += 1;
                        $nextReportingBossStep = $currentBossHistoryStep
                            ? $reportingBossBosses->where('step_index', $nextStep)->first()
                            : null;

                        $isPositionExist = $nextReportingBossStep ? EmployeeOrganizationInfo::where('position_id_fk', $nextReportingBossStep->position_id_fk)->first() : null;
                        $isBossExist = $nextReportingBossStep ? $nextReportingBossStep->getBossUserByDepartmentAndBranchAndModel(
                            $employeeDepartment,
                            $employeeBranch,
                            $this
                        ) : null;

                        if ($totalSteps < $nextStep) {
                            break;
                        }
                    }

                    if ($nextReportingBossStep) {
                        if (
                            $nextReportingBossStep->position_id_fk == $bossUserPosition->id &&
                            $nextReportingBossStep->department_id_fk == $bossUserOrganization->department
                        ) {
                            if ($bossReponsibility) {
                                if ($bossReponsibility->emp_id_fk == $gnrResponsibilityEmployeeBranchWise->emp_id_fk) {
                                    return true;
                                } else {
                                    return false;
                                }
                            } else {
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }
}
