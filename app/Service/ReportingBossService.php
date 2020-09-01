<?php


namespace App\Service;


use App\ConstValue;
use App\gnr\GnrArea;
use App\gnr\GnrBranch;
use App\gnr\GnrRegion;
use App\gnr\GnrResponsibility;
use App\gnr\GnrZone;
use App\hr\EmployeeOrganizationInfo;
use App\hr\Position;
use App\hr\ReportingBossEmployee;
use App\hr\ReportingBossHistory;
use App\hr\ReportingBossRule;
use App\User;

class ReportingBossService
{

    public function findSettingAccordingToEmployee($employeeUser, $function, $model)
    {
        $reportingBossEmployee = null;

        $empOrg = $employeeUser->organization;
        $empDepartment = $empOrg->departmentInfo ?? null;
        $empPosition = $employeeUser->currentPosition();

        $firstReportingBossHistory = ReportingBossHistory::getByModel($model)->first();

        if ($firstReportingBossHistory) {
            $reportingBossEmployee = $firstReportingBossHistory->reportingBossEmployee;
        } else {
            $reportingBossEmployeeList = ReportingBossEmployee::where('group_id_fk', $empOrg->group_id_fk)
                ->where('company_id_fk', $empOrg->company_id_fk)
                ->where('project_id_fk', $empOrg->project_id_fk)
                ->where('position_id_fk', $empPosition->id)
                ->where(function ($q) use ($empDepartment) {
                    if ($empDepartment) {
                        $q->where('department_id_fk', $empDepartment->id);
                    }
                })
                ->where(function ($q) use ($empOrg) {
                    if ($empOrg->branch_id_fk == ConstValue::BRANCH_ID_HEAD_OFFICE) {
                        $q->where('settings_for', 'Head Office');
                    } else {
                        $q->where('settings_for', 'Branch');
                    }

                })
                ->where('hr_reporting_boss_functions_id_fk', $function->id)
                ->orderByDesc('effect_date')
                ->where('effect_date', '<=', date('Y-m-d', strtotime($model->created_at)))
                ->get();
            $applicationTagId = $model->applicationDetails->first()->leaveType->hr_leave_type_tag_id_fk;
            $reportingBossEmployee = $reportingBossEmployeeList->filter(function ($item) use ($function, $applicationTagId, &$t, $model) {
                if ($function->slug == 'leave' && !empty($item->criteria_data)) {
                    $criteriaDataLeaveTypeTagIdList = json_decode($item->criteria_data)->leave_type_tag_id_list;
                    if (in_array($applicationTagId, $criteriaDataLeaveTypeTagIdList)) {
                        return true;
                    }
                }
                return false;
            })->sortByDesc('effect_date')->first();
            // dd($reportingBossEmployee);
        }

        return $reportingBossEmployee;
    }

    public function findSettingAccordingToBoss($bossUser, $function, $model, $hasAccessAllData)
    {
        if ($hasAccessAllData || $bossUser->isSuperAdminUser()) {
            $reportingBossEmployeeList = ReportingBossEmployee::all();
        } else {
            $reportingBossRule = new ReportingBossRule();

            if ($reportingBossRule->isSuperBoss($bossUser)) {
                $reportingBossEmployeeList = ReportingBossEmployee::all();
            } else {
                $bossOrg = $bossUser->organization;
                $bossDepartment = $bossOrg->departmentInfo ?? null;
                $bossPosition = $bossUser->currentPosition();

                $reportingBossEmployeeList = ReportingBossEmployee::where('group_id_fk', $bossOrg->group_id_fk)
                    ->where('company_id_fk', $bossOrg->company_id_fk)
                    ->where('project_id_fk', $bossOrg->project_id_fk)
                    ->whereHas('bosses', function ($q) use ($bossDepartment, $bossPosition) {
                        $q->where('position_id_fk', $bossPosition->id);
                        if ($bossDepartment) {
                            $q->where('department_id_fk', $bossDepartment->id);
                        }
                    })->where('hr_reporting_boss_functions_id_fk', $function->id)
                    ->orderBy('effect_date', 'DESC')
                    ->get();
            }
        }

        return $reportingBossEmployeeList;
    }

    public function findSettingAccordingToEmployeeAndBoss($employeeUser, $bossUser, $function, $model)
    {

        $reportingBossEmployee = null;

        $empOrg = $employeeUser->organization;

        $empPosition = $employeeUser->currentPosition();
        $bossUserPosition = $bossUser->currentPosition();

        $firstReportingBossHistory = ReportingBossHistory::getByModel($model)->first();

        if ($firstReportingBossHistory) {
            $reportingBossEmployee = $firstReportingBossHistory->reportingBossEmployee;
        } else {
            $reportingBossEmployee = ReportingBossEmployee::where(function ($q) use ($empOrg) {
                if ($empOrg->branch_id_fk == ConstValue::BRANCH_ID_HEAD_OFFICE) {
                    $q->where('settings_for', 'Head Office');
                } else {
                    $q->where('settings_for', 'Branch');
                }
            })
                ->where('group_id_fk', $empOrg->group_id_fk)
                ->where('company_id_fk', $empOrg->company_id_fk)
                ->where('position_id_fk', $empPosition->id)
                ->where('department_id_fk', $empOrg->department)
                ->orderByDesc('effect_date')
                ->where('effect_date', '<=', date('Y-m-d', strtotime($model->created_at)))
                ->where('hr_reporting_boss_functions_id_fk', $function->id)
                ->whereHas('bosses', function ($q) use ($bossUserPosition) {
                    $q->where('position_id_fk', $bossUserPosition->id);
                })
                ->get()
                ->filter(function ($item) use ($model) {
                    $criteriaDataLeaveTypeTagIdList = json_decode($item->criteria_data)->leave_type_tag_id_list;
                    $empApplicationLeaveTagId = $model->applicationDetails->first()->leaveType->hr_leave_type_tag_id_fk ?? 0;
                    if (in_array($empApplicationLeaveTagId, $criteriaDataLeaveTypeTagIdList)) {
                        return true;
                    } else {
                        return false;
                    }
                })->first();

        }
        return $reportingBossEmployee;
    }

    public function getBossUser($employeeDepartment, $employeeBranch, $model, $reportingBossBoss)
    {
        // This state contain the boss user.
        $bossUser = null;

        // This state describe the boss is ordinary or not.
        $isOrdinaryBoss = true;

        $reportingBossRule = new ReportingBossRule();

        $bossPosition = Position::find($reportingBossBoss->position_id_fk);


        $isSuperBoss = in_array($bossPosition->id, $reportingBossRule->getPositionByKeyWord(ConstValue::REPORTING_BOSS_RULE_KEY_SUPER_BOSS)->pluck('id')->toArray());
        $isProgramBoss = in_array($bossPosition->id, $reportingBossRule->getPositionByKeyWord(ConstValue::REPORTING_BOSS_RULE_KEY_PROGRAM_BOSS)->pluck('id')->toArray());
        $isAreaBoss = in_array($bossPosition->id, $reportingBossRule->getPositionByKeyWord(ConstValue::REPORTING_BOSS_RULE_KEY_AREA_BOSS)->pluck('id')->toArray());
        $isZonalBoss = in_array($bossPosition->id, $reportingBossRule->getPositionByKeyWord(ConstValue::REPORTING_BOSS_RULE_KEY_ZONAL_BOSS)->pluck('id')->toArray());
        $isBranchBoss = in_array($bossPosition->id, $reportingBossRule->getPositionByKeyWord(ConstValue::REPORTING_BOSS_RULE_KEY_BRANCH_BOSS)->pluck('id')->toArray());

        // Super boss
        if ($isSuperBoss) {
            $isOrdinaryBoss = false;
            $organization = EmployeeOrganizationInfo::where('position_id_fk', $bossPosition->id)
                ->where('department', $reportingBossBoss->department_id_fk)
                ->first();
            $bossUser = $organization ? User::where('emp_id_fk', $organization->emp_id_fk)->first() : null;
        }

        // Program Manager
        if ($isProgramBoss) {
            $isOrdinaryBoss = false;
            $area = GnrArea::whereRaw('branchId Like :bid', [':bid' => '%"' . $employeeBranch->id . '"%'])->first();
            if ($area) {
                $zone = GnrZone::whereRaw('areaId Like :aid', [':aid' => '%"' . $area->id . '"%'])->first();
                if ($zone) {
                    $region = GnrRegion::whereRaw('zoneId Like :zid', [':zid' => '%"' . $zone->id . '"%'])->first();
                    if ($region) {
                        $bossUser = GnrResponsibility::whereRaw('id_list Like :rid', [':rid' => '%"' . $region->id . '"%'])
                                ->get()
                                ->where('type_code', ConstValue::RESPONSIBILITY_TYPE_CODE_REGION)
                                ->first()
                                ->employee
                                ->user
                            ?? null;
                    }
                }
            }
            // dd($bossUser->employee);
        }

        // Area Manager
        if ($isAreaBoss) {
            $isOrdinaryBoss = false;
            $area = GnrArea::whereRaw('branchId Like :bid', [':bid' => '%"' . $employeeBranch->id . '"%'])->first();
            $bossOrganization = EmployeeOrganizationInfo::where('group_id_fk', $reportingBossBoss->reportingBossEmployee->group_id_fk)
                ->where('company_id_fk', $reportingBossBoss->reportingBossEmployee->company_id_fk)
                ->where('project_id_fk', $reportingBossBoss->reportingBossEmployee->project_id_fk);
                if ($area) {
                    $bossOrganization->whereIn('branch_id_fk', $area->branchId);
                }
                // ->whereIn('branch_id_fk', $area->branchId)
                $bossOrganization->where('status', 'Active')
                ->get()->filter(function ($item) use ($bossPosition) {
                    $currentPosition = $item->user ? $item->user->currentPosition() : null;
                    if ($currentPosition && $currentPosition->id == $bossPosition->id) {
                        return true;
                    } else {
                        return false;
                    }
                })->first();

//            dd($bossOrganization,$reportingBossBoss->reportingBossEmployee, $area->branchId,$bossPosition);
            $bossUser = $bossOrganization->user ?? null;
        }

        // Zonal Manager
        if ($isZonalBoss) {
            $branchIdList = [];
            $isOrdinaryBoss = false;
            $area = GnrArea::whereRaw('branchId Like :bid', [':bid' => '%"' . $employeeBranch->id . '"%'])->first();
            if ($area) {
                $zone = GnrZone::whereRaw('areaId Like :aid', [':aid' => '%"' . $area->id . '"%'])->first();
                $areas = $zone ? GnrArea::find($zone->areaId) : collect();
                foreach ($areas as $area) {
                    foreach ($area->branchId as $branchId) {
                        $branchIdList[] = $branchId;
                    }
                }
                $bossOrganization = EmployeeOrganizationInfo::where('group_id_fk', $reportingBossBoss->reportingBossEmployee->group_id_fk)
                    ->where('company_id_fk', $reportingBossBoss->reportingBossEmployee->company_id_fk)
                    ->where('project_id_fk', $reportingBossBoss->reportingBossEmployee->project_id_fk)
                    ->whereIn('branch_id_fk', $branchIdList)
                    ->where('status', 'Active')
                    ->get()->filter(function ($item) use ($bossPosition) {
                        $currentPosition = $item->user ? $item->user->currentPosition() : null;
                        if ($currentPosition && $currentPosition->id == $bossPosition->id) {
                            return true;
                        } else {
                            return false;
                        }
                    })->first();

                $bossUser = $bossOrganization->user ?? null;
            }
        }


        // Branch Manager
        if ($isBranchBoss) {
            $isOrdinaryBoss = false;
            $bossOrganization = EmployeeOrganizationInfo::where('group_id_fk', $reportingBossBoss->reportingBossEmployee->group_id_fk)
                ->with(['user.actingPosition'])
                ->where('company_id_fk', $reportingBossBoss->reportingBossEmployee->company_id_fk)
                ->where('project_id_fk', $reportingBossBoss->reportingBossEmployee->project_id_fk)
                ->where('branch_id_fk', $employeeBranch->id)
                ->where('status', 'Active')
                // ->where('job_status', 'Present')
                ->get()->filter(function ($item) use ($bossPosition) {
                    $currentPosition = $item->user ? $item->user->currentPosition() : null;
                    if ($currentPosition && $currentPosition->id == $bossPosition->id) {
                        return true;
                        return true;
                    } else {
                        return false;
                    }
                })->first();

            $bossUser = $bossOrganization->user ?? null;

        }

        // Ordinary Boss
        if ($isOrdinaryBoss) {
            $employeeUser = User::find($model->users_id_fk);
            $employeeOrganizations = EmployeeOrganizationInfo::where('emp_id_fk', $employeeUser->emp_id_fk)->first();
            $employeeBranch = $employeeOrganizations->branch;
            $bossUser = User::join('hr_emp_org_info', 'users.emp_id_fk', 'hr_emp_org_info.emp_id_fk')
                ->where('hr_emp_org_info.group_id_fk', $reportingBossBoss->reportingBossEmployee->group_id_fk)
                ->where('hr_emp_org_info.company_id_fk', $reportingBossBoss->reportingBossEmployee->company_id_fk)
                ->where(function ($q) use ($employeeBranch, $reportingBossBoss) {
                    if ($employeeBranch->id != ConstValue::BRANCH_ID_HEAD_OFFICE) {
                        $q->where('hr_emp_org_info.project_id_fk', $reportingBossBoss->reportingBossEmployee->project_id_fk);
                    }
                })
                ->where('hr_emp_org_info.position_id_fk', $bossPosition->id)
                ->where('hr_emp_org_info.status', 'Active')
                // ->where('hr_emp_org_info.job_status', 'Present')
                ->where(function ($q) use ($employeeDepartment, $reportingBossBoss) {
                    if ($reportingBossBoss->department_id_fk) {
                        $q->where('hr_emp_org_info.department', $reportingBossBoss->department_id_fk);
                    } else {
                        $q->where('hr_emp_org_info.department', $employeeDepartment->id);
                    }
                })
                ->select('users.*')
                ->first();
        }

        return $bossUser;
    }
}
