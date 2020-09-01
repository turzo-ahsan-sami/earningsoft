<?php

namespace App\Service;

use App\gnr\FiscalYear;
use App\hr\HrLeaveApplicationDetails;
use App\hr\HrLeaveType;
use App\hr\HrLeaveTypeTag;
use App\hr\LateLeave;
use App\hr\LeaveSetting;
use App\hr\PromotionIncrement;
use App\User;
use Carbon\Carbon;
use DateTime;

/**
 * Leave Services
 *
 * @author hafij <hafij.to@gmail.com>
 *
 */
class LeaveService
{
    public function getLeaveTypesByUserInCurrentFiscalYear($userId)
    {
        $leaveTypes = null;

        if ($userId) {
            $user = User::find($userId);
            $employee = $user->employee;
            $fiscalYear = FiscalYear::where('fyStartDate', '<=', date('Y-m-d'))
                ->where('fyEndDate', '>=', date('Y-m-d'))
                ->first();
            $leaveTypes = $this->fetchEmployeeLeaveTypesByFiscalYear($employee, $fiscalYear);
        }

        return $leaveTypes;
    }

    public function getLeaveStatusInfoByUserIdFiscalYearAndLeaveType($userId, $fiscalYear, $leaveType, $fiscalYearEndPoint = null)
    {
        $leaveStatus = [];

        foreach ($this->getLeaveStatusInfoByUserIdAndFiscalYear($userId, $fiscalYear, $fiscalYearEndPoint) as $status) {
            if ($leaveType && $status['leaveTypeId'] == $leaveType->id) {
                $leaveStatus = $status;
                break;
            }
        }
        return $leaveStatus;
    }

    /**
     * Get User leave status in current fiscal year
     *
     * @param User $userId
     * @param FiscalYear $fiscalYear
     *
     * @return array $leaveStatusList
     */
    public function getLeaveStatusInfoByUserIdAndFiscalYear($userId, $fiscalYear, $fiscalYearEndPoint = null)
    {
        $leaveStatusList = [];

        if ($userId && $fiscalYear) {


            // Modified fiscal year is needed to calculate consumed leave
            // All other case we use original fiscal year
            $modifiedFiscalYear = clone $fiscalYear;
            if ($fiscalYearEndPoint) {
                $modifiedFiscalYear->fyEndDate = date('Y-m-d', strtotime($fiscalYearEndPoint));
            }

            $user = User::find($userId);
            $employee = $user->employee;

            $employeeLeaveTypes = $this->fetchEmployeeLeaveTypesByFiscalYear($employee, $fiscalYear);

            foreach ($employeeLeaveTypes as $leaveType) {

                $openningLeave = 0;
                $maximumLeave = 0;
                $eligibleLeave = 0;
                $consumedLeave = 0;
                $remainingLeave = 0;

                $tag = $leaveType->tag;

                $preFiscalYearEndDate = $fiscalYear->getPrevious()->fyEndDate;

                $openningLeave = $this->getOpenningLeaveBalanceUpToPreFiscalYearEndDate(
                    $employee,
                    $leaveType,
                    $preFiscalYearEndDate
                );

                $maximumLeave = $this->getEmployeeMaxLeaveOfTheLeaveTypeInTheFiscalYear($employee, $leaveType, $fiscalYear);

                // Eligible Leave
                $eligibleLeave = $this->getEmployeeEligibleLeaveInTheFiscalYear($employee, $leaveType, $fiscalYear);

                // Consumed Leave
                $consumedLeaveRegular = $this->getEmployeeConsumedLeaveByLeaveTypeAndDateRange(
                    $employee,
                    $leaveType,
                    $modifiedFiscalYear->fyStartDate,
                    $modifiedFiscalYear->fyEndDate
                );

                $consumedLeaveLate = $this->getEmployeeConsumedLateLeaveByLeaveTypeAndDateRange(
                    $employee,
                    $leaveType,
                    $modifiedFiscalYear->fyStartDate,
                    $modifiedFiscalYear->fyEndDate
                );

                $consumedLeave = $consumedLeaveRegular + $consumedLeaveLate;

                // Remaining Leave
                $remainingLeave = $this->calculateEmployeeRemainingLeaveOfTheLeaveType(
                    $employee,
                    $leaveType,
                    $openningLeave,
                    $maximumLeave,
                    $consumedLeave
                );

                $leaveStatusList[] = [
                    'name' => $tag->name,
                    'openningLeave' => $openningLeave,
                    'maximumLeave' => $maximumLeave,
                    'eligibleLeave' => $eligibleLeave,
                    'consumedLeave' => $consumedLeave,
                    'remainingLeave' => $remainingLeave,
                    'leaveTypeId' => $leaveType->id,
                    'leaveTagId' => $tag->id,
                ];
            }
        }

        // dd($leaveStatusList);
        return $leaveStatusList;
    }

    public function getOpenningLeaveBalanceUpToPreFiscalYearEndDate($employee, $leaveType, $preFiscalYearEndDate)
    {
        $openningLeave = 0;

        if ($employee && $leaveType && $preFiscalYearEndDate) {
            if ($leaveType->tag->slug == 'earn') {

                $organization = $employee->organization;

                $leaveSetting = LeaveSetting::where('group_id_fk', $organization->group_id_fk)
                    ->where('company_id_fk', $organization->company_id_fk)
                    ->where('project_id_fk', $organization->project_id_fk)
                    ->first();

                if ($leaveSetting) {

                    $startDate = date('Y-m-d', strtotime($organization->joining_date));
                    $endDate = date('Y-m-d', strtotime($preFiscalYearEndDate));

                    $earnLeaveConsumed = $this->getEmployeeConsumedLeaveByLeaveTypeAndDateRange(
                        $employee,
                        $leaveType,
                        $startDate,
                        $endDate
                    );

                    $maxDaysBoundaryForEarnLeave = $leaveSetting->earnLeave->highest_limit_in_days ?? 0;

                    $jobAgeInMonth = $employee->jobDurationInMonthTill(
                        date('Y-m-d', strtotime($preFiscalYearEndDate))
                    );

                    if ($preFiscalYearEndDate > $organization->joining_date) {
                        $openningLeave = $leaveSetting->earnLeave->day_count_per_month * $jobAgeInMonth - $earnLeaveConsumed;
                    }

                    // Dont cross max earn leave
                    if ($openningLeave >= $maxDaysBoundaryForEarnLeave) {
                        $openningLeave = $maxDaysBoundaryForEarnLeave;
                    }
                }
            }
        }

        return $openningLeave;
    }

    /**
     * Get Max-Leave of a employee in a fiscal year
     *
     * @param EmployeeGeneralInfo $employee
     * @param HrLeaveType $leaveType
     * @param FiscalYear $fiscalYear
     *
     * @return int $maximumLeave
     */
    public function getEmployeeMaxLeaveOfTheLeaveTypeInTheFiscalYear($employee, $leaveType, $fiscalYear)
    {
        $maximumLeave = 0;

        if ($employee && $leaveType && $fiscalYear) {

            /*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
            |||||||||||||||||||Default Rules Apply Area\\\\\\\\\\\\\\\\\\\\\
            ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

            $maximumLeave = $leaveType->total_day;

            //check provisional emp
            // dd($employee->organization->recruitment_type);
            if ($employee->organization->recruitment_type == 6) {
                $probationPeriod = $employee->organization->probation_period;
                $maximumLeave = intval(($leaveType->total_day / 12) * $probationPeriod);
            }
            else {
                // Check employee is joined in this fiscal year
                // When he is not regular employee
                // Non regular employee cant get full leave on this fiscal year
                if ($this->isEmployeeJoinedInDateRange($employee, $fiscalYear->fyStartDate, $fiscalYear->fyEndDate)) {
                    $joiningDate = Carbon::parse($employee->organization->joining_date)->subMonth();
                    $fyLastMonth = Carbon::parse($fiscalYear->fyEndDate);
                    $monthDiffJoiningToFyEndDate = $joiningDate->diffInMonths($fyLastMonth);
                    $maximumLeave = intval(($leaveType->total_day / 12) * $monthDiffJoiningToFyEndDate);
                }
            }
            // dd($employee->organization);


            // Check Employee is permanent in this fiscal year
            // Permanent employee maximum leave depends on permanent effect date and fiscal year end date
            $todayDate = date('Y-m-d');
            $promotion = PromotionIncrement::where('promotion_type', 'Permanent')
                ->where('users_id_fk', $employee->user->id)
                ->where('status', 'Active')
                ->where('effect_month', '>=', $fiscalYear->fyStartDate)
                ->where('effect_month', '<=', $todayDate)
                ->first();

            if ($promotion) {
                // $promotionEffectMonth = Carbon::parse($promotion->effect_month)->subMonth();
                // $fyLastMonth = Carbon::parse($fiscalYear->fyEndDate);
                // $monthDiffPermanentToFyEndDate = $promotionEffectMonth->diffInMonths($fyLastMonth);
                // $maximumLeave = intval(($leaveType->total_day / 12) * $monthDiffPermanentToFyEndDate);

                $joiningDate = $employee->organization->joining_date;

                if ($joiningDate >= $fiscalYear->fyStartDate && $joiningDate <= $fiscalYear->fyEndDate) {
                    $joiningMonth = Carbon::parse($joiningDate)->subMonth();
                    $fyLastMonth = Carbon::parse($fiscalYear->fyEndDate);
                    $monthDiffJoiningToFyEndDate = $joiningMonth->diffInMonths($fyLastMonth);
                    $maximumLeave = intval(($leaveType->total_day / 12) * $monthDiffJoiningToFyEndDate);
                }

            }

            /*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
            |||||||||||||||||||Specific Rules Apply Area\\\\\\\\\\\\\\\\\\\\\
            ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

            if ($leaveType->tag) {

                $tag = $leaveType->tag;

                // NPL Leave
                if ($tag->slug == 'npl') {
                    $maximumLeave = 0;
                }

                // NPL Leave
                if ($tag->slug == 'earn') {
                    $organization = $employee->organization;
                    $leaveSetting = LeaveSetting::where('group_id_fk', $organization->group_id_fk)
                        ->where('company_id_fk', $organization->company_id_fk)
                        ->where('project_id_fk', $organization->project_id_fk)
                        ->first();

                    $openningLeave = $this->getOpenningLeaveBalanceUpToPreFiscalYearEndDate(
                        $employee,
                        $leaveType,
                        $fiscalYear->getPrevious()->fyEndDate
                    );

                    if ($leaveSetting) {
                        $maxDaysBoundaryForEarnLeave = $leaveSetting->earnLeave->highest_limit_in_days ?? 0;
                        if ($openningLeave >= $maxDaysBoundaryForEarnLeave) {
                            $maximumLeave = 0;
                        }
                    }
                }

                // Paternity Leave
                if ($tag->slug == 'paternity' && $employee->sex == 'Male') {
                    $startDate = date('Y-m-d', strtotime($employee->organization->joining_date));
                    $endDate = date('Y-m-d');
                    // When no child
                    $totalMaxLeave = $this->employeeChildrenRuleOfMaximumLeave($employee, $leaveType->total_day);
                    $consumedLeave = $this->getEmployeeConsumedLeaveByLeaveTypeAndDateRange($employee, $leaveType, $startDate, $endDate);
                    $maximumLeave = $totalMaxLeave - $consumedLeave;
                }

                // Maternity Leave
                if ($tag->slug == 'maternity' && $employee->sex == 'Female') {

                    $startDate = date('Y-m-d', strtotime($employee->organization->joining_date));
                    $endDate = date('Y-m-d');
                    // When no child
                    $totalMaxLeave = $this->employeeChildrenRuleOfMaximumLeave($employee, $leaveType->total_day);
                    $consumedLeave = $this->getEmployeeConsumedLeaveByLeaveTypeAndDateRange($employee, $leaveType, $startDate, $endDate);
                    $maximumLeave = $totalMaxLeave - $consumedLeave;
                }
            }
        }

        return $maximumLeave;
    }

    public function isEmployeeJoinedInDateRange($employee, $startDate, $endDate)
    {
        $isJoined = false;

        if ($employee && $startDate && $endDate) {
            $organization = $employee->organization;
            if($organization->joining_date >= $startDate && $organization->joining_date <= $endDate){
                $isJoined = true;
            }
        }

        return $isJoined;
    }

    public function employeeChildrenRuleOfMaximumLeave($employee, $maximumLeave)
    {
        $maximumLeave = $maximumLeave;

        // When no child
        if (!$employee->number_of_children) {
            $maximumLeave = $maximumLeave * 2;
        }

        // When One child
        if ($employee->number_of_children == 1) {
            $maximumLeave = $maximumLeave * 1;
        }

        // When multiple child
        if ($employee->number_of_children > 1) {
            $maximumLeave = $maximumLeave * 0;
        }

        return $maximumLeave;
    }

    /**
     * Get Eligible leave of a employee in a fiscal year
     * This function is deprecated
     *
     * @param EmployeeGeneralInfo $employee
     * @param int $maxLeave
     * @param FiscalYear $fiscalYear
     *
     * @return int $maximumLeave
     */
    public function getEmployeeEligibleLeave($employee, $maxLeave, $fiscalYear)
    {
        $eligibleLeave = 0;
        $todayDate = date('Y-m-d');

        if ($employee && $fiscalYear && is_int($maxLeave)) {

            if ($fiscalYear->fyEndDate >= $todayDate) {
                $interval = DateUtility::dateDiff($fiscalYear->fyStartDate, $todayDate);
                $countedMonthFromThisFiscalYearStartDate = $interval->m;
                // If next month start then add this month
                $countedMonthFromThisFiscalYearStartDate += $interval->d > 0 ? 1 : 0;
            } else {
                $countedMonthFromThisFiscalYearStartDate = 12;
            }

            $eligibleLeave = $countedMonthFromThisFiscalYearStartDate != 0
                ? intval(($maxLeave / 12) * $countedMonthFromThisFiscalYearStartDate)
                : 1;
        }

        return $eligibleLeave;
    }

    /**
     * Get employee Eligible leaves in a fiscal year
     *
     * @param EmployeeGeneralInfo $employee
     * @param HrLeaveType $leaveType
     * @param FiscalYear $fiscalYear
     *
     * @return int $maximumLeave
     */
    public function getEmployeeEligibleLeaveInTheFiscalYear($employee, $leaveType, $fiscalYear)
    {
        $eligibleLeave = 0;
        $todayDate = date('Y-m-d');

        if ($employee && $leaveType && $fiscalYear) {
            $eligibleLeave = $this->getEmployeeEligibleLeaveInDateRange($employee, $leaveType, $fiscalYear->fyStartDate, $todayDate);
        }

        return $eligibleLeave;
    }

    /**
     * Get employee Eligible leaves in a date range
     *
     * @param EmployeeGeneralInfo $employee
     * @param HrLeaveType $leaveType
     * @param string $startDate
     * @param string $endDate
     *
     * @return int $maximumLeave
     */
    public function getEmployeeEligibleLeaveInDateRange($employee, $leaveType, $startDate, $endDate)
    {

        $eligibleLeave = 0;

        $startDate = date('Y-m-01', strtotime($startDate));
        $endDate = date('Y-m-t', strtotime($endDate));

        $fiscalYear = FiscalYear::findByDateRange($startDate, $endDate);


        if ($employee && $leaveType && $fiscalYear) {

            $maxLeave = $leaveType->total_day;
            //check provisional emp
            if ($employee->organization->recruitment_type == 6) {
                $probationPeriod = $employee->organization->probation_period;
                $maxLeave = intval(($leaveType->total_day / 12) * $probationPeriod);
            }

            if ($fiscalYear->fyEndDate >= $endDate) {
                $interval = DateUtility::dateDiff($fiscalYear->fyStartDate, $endDate);
                $countedMonthFromThisFiscalYearStartDate = $interval->m;
                // If next month start then add this month
                $countedMonthFromThisFiscalYearStartDate += $interval->d > 0 ? 1 : 0;
            } else {
                $countedMonthFromThisFiscalYearStartDate = 12;
            }

            $eligibleLeave = $countedMonthFromThisFiscalYearStartDate != 0
                ? intval(($maxLeave / 12) * $countedMonthFromThisFiscalYearStartDate)
                : 1;

            if ($leaveType->tag) {

                $tag = $leaveType->tag;
                $organization = $employee->organization;

                // NPL Leave
                if ($tag->slug == 'npl') {
                    $eligibleLeave = 0;
                }

                // Earn Leave
                if ($tag->slug == 'earn') {

                    $jobDurationInYear = $employee->jobDurationInYearTill(date('Y-m-d', strtotime($fiscalYear->fyStartDate . '-1 day')));

                    $leaveSetting = LeaveSetting::where('group_id_fk', $organization->group_id_fk)
                        ->where('company_id_fk', $organization->company_id_fk)
                        ->where('project_id_fk', $organization->project_id_fk)
                        ->first();

                    $jobDurationBoundaryForEarnLeave = $leaveSetting
                        ? $leaveSetting->earnLeave->after_active_in_month / 12
                        : 0;

                    if ($jobDurationInYear < $jobDurationBoundaryForEarnLeave) {
                        $eligibleLeave = 0;
                    }
                }

                // Maternity Leave
                if ($tag->slug == 'maternity') {
                    $maximumLeave = $this->getEmployeeMaxLeaveOfTheLeaveTypeInTheFiscalYear($employee, $leaveType, $fiscalYear);
                    $eligibleLeave = $employee->sex == 'Female' ? $maximumLeave : 0;
                }

                // Paternity Leave
                if ($tag->slug == 'paternity' && $employee->sex == 'Male') {
                    $maximumLeave = $this->getEmployeeMaxLeaveOfTheLeaveTypeInTheFiscalYear($employee, $leaveType, $fiscalYear);
                    $eligibleLeave = $employee->sex == 'Male' ? $maximumLeave : 0;
                }

            }
        }

        return $eligibleLeave;
    }


    public function calculateEmployeeRemainingLeaveOfTheLeaveType($employee, $leaveType, $openningLeave, $maximumLeave, $consumedLeave)
    {
        $remainingLeave = 0;

        $openningLeave = $openningLeave;
        $maximumLeave = $maximumLeave;
        $consumedLeave = $consumedLeave;

        if ($employee && $leaveType) {

            $remainingLeave = $openningLeave + $maximumLeave - $consumedLeave;

            $tag = $leaveType->tag;

            if ($tag->slug == 'npl') {
                $remainingLeave = 0;
            }
        }

        return $remainingLeave;
    }

    public function employeeLeaveTakenAbilityInfoOfTheLeaveTypeInTheDateRange($employee, $leaveType, $fromDate, $toDate, $exceptDetailsList = [])
    {
        $info = [
            'status' => '',
            'flag' => 0,
            'message' => '',
        ];

        $fromDate = date('Y-m-d', strtotime($fromDate));
        $toDate = date('Y-m-d', strtotime($toDate));

        $datetime1 = new DateTime($fromDate);
        $datetime2 = new DateTime($toDate);
        $interval = $datetime1->diff($datetime2);
        $totalDays = $interval->format('%a');
        $totalApplyLeave = $totalDays + 1;

        $nplLeaveTag = HrLeaveTypeTag::getBySlug('npl');

        $fiscalYear = FiscalYear::where('fyStartDate', '<=', $fromDate)
            ->where('fyEndDate', '>=', $toDate)
            ->first();

        // Check already apply or not
        $appliedLeaveInDateRange = $this->getEmployeeAppliedLeaveByLeaveTypeAndDateRange(
            $employee,
            $leaveType,
            $fromDate,
            $toDate,
            $exceptDetailsList
        );

        if ($appliedLeaveInDateRange > 0) {

            $info = [
                'status' => 'error',
                'flag' => 0,
                'message' => "Already applied this date range",
            ];

            return $info;
        }


        // Check abble to get leave
        // 365 is a limit of a year
        $remainTotalLeave = 0;
        $leaveStatus = $this->getLeaveStatusInfoByUserIdFiscalYearAndLeaveType($employee->user->id, $fiscalYear, $leaveType);

        if (!empty($leaveStatus)) {
            if ($leaveStatus['leaveTagId'] == $nplLeaveTag->id) {
                $remainTotalLeave = 365;
            } else {
                $remainTotalLeave = $leaveStatus['remainingLeave'];
            }

        }

        $isAbleToGetTheLeave = $remainTotalLeave >= $totalApplyLeave ? 1 : 0;

        if ($isAbleToGetTheLeave == 1) {

            $info = [
                'status' => 'success',
                'flag' => 1,
                'message' => "You can apply.",
            ];

            return $info;

        } else {

            $info = [
                'status' => 'error',
                'flag' => 0,
                'message' => "You are exceed the leave range.",
            ];

            return $info;
        }
    }

    /**
     * Fetch employee leave type by fiscal yer
     *
     * @param EmployeeGeneralInfo $employee
     * @param FiscalYear $fiscalYear
     *
     * @return Collection $leaveTypes
     */
    public function fetchEmployeeLeaveTypesByFiscalYear($employee, $fiscalYear)
    {
        $leaveTypes = null;

        $organization = $employee ? $employee->organization : null;

        if ($organization) {
            $leaveTypeCollection = HrLeaveType::join('hr_leave_type_tag', 'hr_leave_type.hr_leave_type_tag_id_fk', '=', 'hr_leave_type_tag.id')
                ->select('hr_leave_type.*', 'hr_leave_type_tag.priority')
                ->where('hr_leave_type.status', 'Active')
                ->orderBy('hr_leave_type_tag.priority', 'ASC')
                ->where('hr_leave_type.recruitment_id', '=', $organization->recruitment_type)
                ->get();

            $leaveTypes = $leaveTypeCollection->filter(function ($leaveType) use ($employee, $fiscalYear) {
                $tag = $leaveType->tag;

                // Skipping Late leave
                if ($tag->slug == 'late') {
                    return false;
                }

                // Only male person get paternity leave
                if ($tag->slug == 'paternity') {

                    if ($employee->sex == 'Female') {
                        return false;
                    }

                    if ($employee->maritial_status == 'Unmarried') {
                        return false;
                    }
                }

                // Only female person get maternity leave
                if ($tag->slug == 'maternity') {

                    if ($employee->sex == 'Male') {
                        return false;
                    }

                    if ($employee->maritial_status == 'Unmarried') {
                        return false;
                    }
                }

                return true;
            });
        }

        return $leaveTypes;
    }

    /**
     * Fetch employee appliable leave type by fiscal yer
     *
     * @param EmployeeGeneralInfo $employee
     * @param FiscalYear $fiscalYear
     *
     * @return Collection $leaveTypes
     */
    public function fetchEmployeeApplicableLeaveTypesByFiscalYear($employee, $fiscalYear)
    {
        $leaveTypes = null;

        $organization = $employee ? $employee->organization : null;

        if ($organization) {
            $leaveTypeCollection = HrLeaveType::join('hr_leave_type_tag', 'hr_leave_type.hr_leave_type_tag_id_fk', '=', 'hr_leave_type_tag.id')
                ->select('hr_leave_type.*', 'hr_leave_type_tag.priority', 'hr_leave_type_tag.name as tagName')
                ->where('hr_leave_type.status', 'Active')
                ->orderBy('hr_leave_type_tag.priority', 'ASC')
                ->where('hr_leave_type.recruitment_id', '=', $organization->recruitment_type)
                ->get();

            $leaveTypes = $leaveTypeCollection->filter(function ($leaveType) use ($employee, $fiscalYear) {
                $tag = $leaveType->tag;

                // Skipping Late leave
                if ($tag->slug == 'late') {
                    return false;
                }

                // Only male person get paternity leave
                if ($tag->slug == 'paternity') {

                    if ($employee->sex == 'Female') {
                        return false;
                    }

                    if ($employee->maritial_status == 'Unmarried') {
                        return false;
                    }
                }

                // Only female person get maternity leave
                if ($tag->slug == 'maternity') {

                    if ($employee->sex == 'Male') {
                        return false;
                    }

                    if ($employee->maritial_status == 'Unmarried') {
                        return false;
                    }
                }

                // Earn leave get only 5 years completed employee
                if ($leaveType->tag->slug == 'earn') {
                    if ($employee->jobDurationInYearTill(date('Y-m-d', strtotime($fiscalYear->fyStartDate . '-1 day'))) < 5) {
                        return false;
                    }
                }

                return true;

            });
        }

        return $leaveTypes;
    }

    /**
     * Fetch Leave Type Tag list
     *
     * @return HrLeaveTypeTag
     */
    public function fetchLeaveTypeTag()
    {
        return HrLeaveTypeTag::orderBy('priority', 'ASC')->whereNotIn('slug', ['late'])->get();
    }

    /**
     * Fetch Leave Type Tag For Openning Balance list
     *
     * @return HrLeaveTypeTag
     */
    public function fetchLeaveTypeTagForOpenningBalance()
    {
        return HrLeaveTypeTag::orderBy('priority', 'ASC')->whereIn('slug', ['earn', 'maternity', 'paternity'])->get();
    }

    /**
     * Get Employee Consumed Rugular leave in a date range
     *
     * @param EmployeeGeneralInfo $employee
     * @param HrLeaveType $leaveType
     * @param string $startDate
     * @param string $endDate
     *
     * @return int $countedLeave
     */
    public function getEmployeeConsumedLeaveByLeaveTypeAndDateRange($employee, $leaveType, $startDate, $endDate)
    {
        $countedLeave = 0;

        $user = $employee->user;

        if ($user) {
            $leaveApplicationDetails = new HrLeaveApplicationDetails();
            // Fiscal year has two date: start date and end date
            // Fiscal year date range has four point
            // Start point,
            // End point,
            // Inside start and end point,
            // Outside start and end point
            $userLeaveApplications = $leaveApplicationDetails->getByUserIdAndLeaveType($user->id, $leaveType);

            if ($userLeaveApplications->count() > 0) {
                $startPointLeaves = $userLeaveApplications
                    ->filter(function ($item) use ($startDate, $endDate) {

                        // Fiscal year start point
                        if ($startDate <= $item->to_date && $startDate > $item->from_date) {
                            return true;
                        }

                        return false;
                    });

                if ($startPointLeaves->count() > 0) {
                    foreach ($startPointLeaves as $startPointLeave) {
                        $leaveStartDate = date("Y-m-d", strtotime($startPointLeave->from_date));
                        $leaveEndDate = date("Y-m-d", strtotime($startPointLeave->to_date));
                        $countedLeave = $countedLeave +
                            Carbon::parse($startDate)->diffInDays(Carbon::parse($leaveEndDate)) + 1;
                    }
                }

                $endPointLeaves = $userLeaveApplications
                    ->filter(function ($item) use ($startDate, $endDate) {

                        // Fiscal year end point
                        if ($startDate >= $item->from_date && $endDate < $item->to_date) {
                            return true;
                        }

                        return false;
                    });

                if ($endPointLeaves->count() > 0) {
                    foreach ($endPointLeaves as $endPointLeave) {
                        $leaveStartDate = date("Y-m-d", strtotime($endPointLeave->from_date));
                        $leaveEndDate = date("Y-m-d", strtotime($endPointLeave->to_date));
                        $countedLeave = $countedLeave +
                            Carbon::parse($leaveStartDate)->diffInDays(Carbon::parse($endDate)) + 1;
                    }
                }

                $insidePointLeaves = $userLeaveApplications
                    ->filter(function ($item) use ($startDate, $endDate) {

                        // Fiscal year inside point
                        if ($startDate <= $item->from_date && $endDate >= $item->to_date) {
                            return true;
                        }

                        return false;
                    });

                if ($insidePointLeaves->count() > 0) {
                    foreach ($insidePointLeaves as $insidePointLeave) {
                        $leaveStartDate = date("Y-m-d", strtotime($insidePointLeave->from_date));
                        $leaveEndDate = date("Y-m-d", strtotime($insidePointLeave->to_date));
                        $countedLeave = $countedLeave +
                            Carbon::parse($leaveStartDate)->diffInDays(Carbon::parse($leaveEndDate)) + 1;
                    }
                }

                $outsidePointLeaves = $userLeaveApplications
                    ->filter(function ($item) use ($startDate, $endDate) {

                        // Fiscal year outside point
                        if ($startDate > $item->from_date && $endDate < $item->end_date) {
                            return true;
                        }

                        return false;
                    });

                if ($outsidePointLeaves->count() > 0) {
                    foreach ($outsidePointLeaves as $outsidePointLeave) {
                        $leaveStartDate = date("Y-m-d", strtotime($outsidePointLeave->from_date));
                        $leaveEndDate = date("Y-m-d", strtotime($outsidePointLeave->to_date));
                        $countedLeave = $countedLeave +
                            Carbon::parse($leaveStartDate)->diffInDays(Carbon::parse($leaveEndDate)) + 1;
                    }
                }
            }
        }

        return $countedLeave;
    }

    /**
     * Get Employee Consumed Late leave in month
     *
     * @param EmployeeGeneralInfo $employee
     * @param HrLeaveType $leaveType
     * @param string $month
     *
     * @return int $consumedLeave
     */
    public function getEmployeeConsumedLateLeaveByLeaveTypeAndMonth($employee, $leaveType, $month)
    {
        $consumedLeave = 0;
        $startDate = date('Y-m-01', strtotime($month));
        $endDate = date('Y-m-t', strtotime($month));

        if ($employee) {
            $lateLeaveAssign = new LateLeave();
            $consumedLeave = $lateLeaveAssign->getEmployeeLateLeaveOfTheTypeInDateRange($employee, $leaveType, $startDate, $endDate);
        }

        return $consumedLeave;
    }

    /**
     * Get Employee Consumed Late leave in a date range
     *
     * @param EmployeeGeneralInfo $employee
     * @param HrLeaveType $leaveType
     * @param string $startDate
     * @param string $endDate
     *
     * @return int $consumedLeave
     */
    public function getEmployeeConsumedLateLeaveByLeaveTypeAndDateRange($employee, $leaveType, $startDate, $endDate)
    {
        $consumedLeave = 0;
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));

        if ($employee) {
            $lateLeaveAssign = new LateLeave();
            $consumedLeave = $lateLeaveAssign->getEmployeeLateLeaveOfTheTypeInDateRange($employee, $leaveType, $startDate, $endDate);
        }

        return $consumedLeave;
    }

    /**
     * Get Employee Applied leave in a date range
     *
     * @param EmployeeGeneralInfo $employee
     * @param HrLeaveType $leaveType
     * @param string $startDate
     * @param string $endDate
     * @param array $except
     *
     * @return int $countedLeave
     */
    public function getEmployeeAppliedLeaveByLeaveTypeAndDateRange($employee, $leaveType, $startDate, $endDate, $exceptList = [])
    {
        $countedLeave = 0;

        $user = $employee->user;

        if ($user) {
            $leaveApplicationDetails = new HrLeaveApplicationDetails();
            // Fiscal year has two date: start date and end date
            // Fiscal year date range has four point
            // Start point,
            // End point,
            // Inside start and end point,
            // Outside start and end point
            $userLeaveApplications = $leaveApplicationDetails->getAppliedLeaveDetailsByUserIdAndLeaveType($user->id, $leaveType)
                ->filter(function ($item) use ($exceptList) {
                    foreach ($exceptList as $exceptId) {
                        if ($exceptId == $item->id) {
                            return false;
                        }
                    }
                });

            // dd($userLeaveApplications, $startDate, $endDate);
            if ($userLeaveApplications->count() > 0) {
                $startPointLeaves = $userLeaveApplications
                    ->filter(function ($item) use ($startDate, $endDate) {
                        // Fiscal year start point
                        if ($startDate <= $item->to_date && $startDate > $item->from_date) {
                            return true;
                        }

                        return false;
                    });

                if ($startPointLeaves->count() > 0) {
                    foreach ($startPointLeaves as $startPointLeave) {
                        $leaveStartDate = date("Y-m-d", strtotime($startPointLeave->from_date));
                        $leaveEndDate = date("Y-m-d", strtotime($startPointLeave->to_date));
                        $countedLeave = $countedLeave +
                            Carbon::parse($startDate)->diffInDays(Carbon::parse($leaveEndDate)) + 1;
                    }
                }

                $endPointLeaves = $userLeaveApplications
                    ->filter(function ($item) use ($startDate, $endDate) {

                        // Fiscal year end point
                        if ($startDate >= $item->from_date && $endDate < $item->to_date) {
                            return true;
                        }

                        return false;
                    });

                if ($endPointLeaves->count() > 0) {
                    foreach ($endPointLeaves as $endPointLeave) {
                        $leaveStartDate = date("Y-m-d", strtotime($endPointLeave->from_date));
                        $leaveEndDate = date("Y-m-d", strtotime($endPointLeave->to_date));
                        $countedLeave = $countedLeave +
                            Carbon::parse($leaveStartDate)->diffInDays(Carbon::parse($endDate)) + 1;
                    }
                }

                $insidePointLeaves = $userLeaveApplications
                    ->filter(function ($item) use ($startDate, $endDate) {

                        // Fiscal year inside point
                        if ($startDate <= $item->from_date && $endDate >= $item->to_date) {
                            return true;
                        }

                        return false;
                    });

                if ($insidePointLeaves->count() > 0) {
                    foreach ($insidePointLeaves as $insidePointLeave) {
                        $leaveStartDate = date("Y-m-d", strtotime($insidePointLeave->from_date));
                        $leaveEndDate = date("Y-m-d", strtotime($insidePointLeave->to_date));
                        $countedLeave = $countedLeave +
                            Carbon::parse($leaveStartDate)->diffInDays(Carbon::parse($leaveEndDate)) + 1;
                    }
                }

                $outsidePointLeaves = $userLeaveApplications
                    ->filter(function ($item) use ($startDate, $endDate) {

                        // Fiscal year outside point
                        if ($startDate > $item->from_date && $endDate < $item->end_date) {
                            return true;
                        }

                        return false;
                    });

                if ($outsidePointLeaves->count() > 0) {
                    foreach ($outsidePointLeaves as $outsidePointLeave) {
                        $leaveStartDate = date("Y-m-d", strtotime($outsidePointLeave->from_date));
                        $leaveEndDate = date("Y-m-d", strtotime($outsidePointLeave->to_date));
                        $countedLeave = $countedLeave +
                            Carbon::parse($leaveStartDate)->diffInDays(Carbon::parse($leaveEndDate)) + 1;
                    }
                }
            }
        }

        return $countedLeave;
    }

}
