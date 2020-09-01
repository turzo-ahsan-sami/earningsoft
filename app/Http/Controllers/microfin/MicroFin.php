<?php

namespace App\Http\Controllers\microfin;

use DB;
use Carbon\Carbon;
use Auth;
use App\microfin\loan\MfnLoanSchedule;

class MicroFin
{

    public static function getGroupList()
    {
        return DB::table('gnr_group')
            ->pluck('name', 'id')
            ->all();
    }

    public static function getCompanyList()
    {
        return DB::table('gnr_company')
            ->pluck('name', 'id')
            ->all();
    }

    public static function getGroupuWiseCompanyList($groupId)
    {
        $company = DB::table('gnr_company');
        if ($groupId != null || $groupId != '') {
            $company = $company->where('groupId', $groupId);
        }
        $company = $company->pluck('name', 'id')->all();
        return $company;
    }

    public static function getProjectList()
    {
        return DB::table('gnr_project')
            ->orderBy('projectCode')
            ->select(DB::raw("CONCAT(LPAD(projectCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
            ->pluck('nameWithCode', 'id')
            ->all();
    }

    public static function getGroupCompanyWiseProjectList($groupId, $companyId)
    {
        $project = DB::table('gnr_project');
        if ($groupId != null || $groupId != '') {
            $project = $project->where('groupId', $groupId);
        }
        if ($companyId != null || $companyId != '') {
            $project = $project->where('companyId', $companyId);
        }
        $project = $project->orderBy('projectCode')
            ->select(DB::raw("CONCAT(LPAD(projectCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
            ->pluck('nameWithCode', 'id')
            ->all();

        return $project;
    }

    public static function getProjectTypeList()
    {
        return DB::table('gnr_project_type')
            ->orderBy('projectTypeCode')
            ->select(DB::raw("CONCAT(LPAD(projectTypeCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
            ->pluck('nameWithCode', 'id')
            ->all();
    }

    public static function getProjectWiseProjectTypeList($projectId)
    {
        $projectTypeList = DB::table('gnr_project_type');
        if ($projectId > 0) {
            $projectTypeList = $projectTypeList->where('projectId', $projectId);
        }
        $projectTypeList = $projectTypeList
            ->orderBy('projectTypeCode')
            ->select(DB::raw("CONCAT(LPAD(projectTypeCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
            ->pluck('nameWithCode', 'id')
            ->all();
        return $projectTypeList;
    }

    public static function getBranchList()
    {
        return DB::table('gnr_branch')
            ->orderBy('branchCode')
            ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
            ->pluck('nameWithCode', 'id')
            ->all();
    }

    public static function getModuleList()
    {
        return DB::table('gnr_module')
            ->where('status', 1)
            ->pluck('name', 'id')
            ->all();
    }

    public static function getAllSamityList()
    {
        return DB::table('mfn_samity')
            ->where('softDel', 0)
            ->select(DB::raw("CONCAT(code,' - ',name) AS name"), 'id')
            ->orderBy('code')
            ->pluck('name', 'id')
            ->all();
    }

    public static function getBranchWiseSamityList($branchId)
    {
        $samityList = DB::table('mfn_samity')
            ->where('softDel', 0);
        if ($branchId != '' || $branchId != null) {
            if (is_array($branchId)) {
                $samityList = $samityList->whereIn('branchId', $branchId);
            } else {
                $samityList = $samityList->where('branchId', $branchId);
            }
        }
        $samityList = $samityList->select(DB::raw("CONCAT(code,' - ',name) AS name"), 'id')
            ->orderBy('code')
            ->pluck('name', 'id')
            ->all();
        return $samityList;
    }

    public static function getAllFieldOfficerList()
    {
        $fieldOfficerIds = DB::table('mfn_samity')
            ->where('softDel', 0)
            ->groupBy('fieldOfficerId')
            ->pluck('fieldOfficerId')
            ->all();

        return DB::table('hr_emp_general_info')
            ->whereIn('id', $fieldOfficerIds)
            ->select(DB::raw("CONCAT(emp_id,' - ',emp_name_english) AS name"), 'id')
            ->pluck('name', 'id')
            ->all();
    }

    public static function getBranchWiseFieldOfficerList($branchId)
    {
        $fieldOfficerIds = DB::table('mfn_samity')
            ->where('softDel', 0);
        if ($branchId != '' || $branchId != null) {
            $fieldOfficerIds = $fieldOfficerIds->where('branchId', $branchId);
        }
        $fieldOfficerIds = $fieldOfficerIds->groupBy('fieldOfficerId')->pluck('fieldOfficerId')->all();

        $fieldOfficerList = DB::table('hr_emp_general_info')
            ->whereIn('id', $fieldOfficerIds)
            ->select(DB::raw("CONCAT(emp_id,' - ',emp_name_english) AS name"), 'id')
            ->pluck('name', 'id')
            ->all();
        return $fieldOfficerList;
    }

    public static function getAllProductCategoryList()
    {
        return DB::table('mfn_loans_product_category')
            ->pluck('name', 'id')
            ->all();
    }

    public static function getFunOrgWiseCategoryList($funOrgId)
    {
        $productCategoryIds = DB::table('mfn_loans_product')
            ->where('softDel', 0);

        if ($funOrgId != '' || $funOrgId != null) {
            $productCategoryIds = $productCategoryIds->where('fundingOrganizationId', $funOrgId);
        }
        $productCategoryIds = $productCategoryIds->pluck('productCategoryId')->all();

        $productCategoryList = DB::table('mfn_loans_product_category')
            ->whereIn('id', $productCategoryIds)
            ->pluck('name', 'id')
            ->all();
        return $productCategoryList;
    }

    public static function getFunOrgWisePrimaryProductList($funOrgId)
    {
        $primaryProductList = DB::table('mfn_loans_product')
            ->where('softDel', 0)
            ->where('isPrimaryProduct', 1);

        if ($funOrgId != '' || $funOrgId != null) {
            $primaryProductList = $primaryProductList->where('fundingOrganizationId', $funOrgId);
        }
        $primaryProductList = $primaryProductList->pluck('name', 'id')->all();

        return $primaryProductList;
    }

    public static function getCategoryWiseProductList($categoryId)
    {
        $productList = DB::table('mfn_loans_product')
            ->where('softDel', 0);

        if ($categoryId != '' || $categoryId != null) {
            $productList = $productList->where('productCategoryId', $categoryId);
        }
        $productList = $productList
            ->select(DB::raw("CONCAT(code,' - ',shortName) AS name"), 'id')
            ->pluck('name', 'id')
            ->all();

        return $productList;
    }

    public static function getAllMemberList()
    {
        return DB::table('mfn_member_information')
            ->where('softDel', 0)
            ->select(DB::raw("CONCAT(code,' - ',name) AS name"), 'id')
            ->orderBy('code')
            ->pluck('name', 'id')
            ->all();
    }

    public static function getBranchWiseMemberList($branchId)
    {
        return DB::table('mfn_member_information')
            ->where('softDel', 0)
            ->where('branchId', $branchId)
            ->select(DB::raw("CONCAT(code,' - ',name) AS name"), 'id')
            ->orderBy('code')
            ->pluck('name', 'id')
            ->all();
    }

    public static function getSamityWiseMemberList($samityId)
    {
        return DB::table('mfn_member_information')
            ->where('softDel', 0)
            ->where('samityId', $samityId)
            ->select(DB::raw("CONCAT(code,' - ',name) AS name"), 'id')
            ->orderBy('code')
            ->pluck('name', 'id')
            ->all();
    }

    public static function getAllPrimaryProductList()
    {
        return DB::table('mfn_loans_product')
            ->where('isPrimaryProduct', 1)
            ->select(DB::raw("CONCAT(code,' - ',shortName) AS name"), 'id')
            ->orderBy('code')
            ->pluck('name', 'id')
            ->all();
    }

    public static function getAllLoanProductList()
    {
        return DB::table('mfn_loans_product')
            ->select(DB::raw("CONCAT(code,' - ',shortName) AS name"), 'id')
            ->orderBy('code')
            ->pluck('name', 'id')
            ->all();
    }

    public static function getAllSavingsProductList()
    {
        return DB::table('mfn_saving_product')
            ->where('status', 1)
            ->select(DB::raw("CONCAT(code,' - ',shortName) AS name"), 'id')
            ->orderBy('code')
            ->pluck('name', 'id')
            ->all();
    }

    public static function getSoftwareDateBranchWise($branchId)
    {

        $softwareDate = DB::table('mfn_day_end')->where('branchIdFk', $branchId)->where('isLocked', 0)->value('date');

        if ($softwareDate == '' || $softwareDate == null) {

            $softwareDate = DB::table('gnr_branch')->where('id', $branchId)->value('softwareStartDate');
            $softwareDate = Carbon::parse($softwareDate);

            //$nextDay = '';
            //$isNextDayHoliday = 1;
            //$nextDay = $softwareDate->copy();

            while (self::isHoliday($softwareDate->format('Y-m-d'), $branchId) == 1) {
                $softwareDate->addDay();
            }
        }
        //else{
        $softwareDate = Carbon::parse($softwareDate);
        //}
        $softwareDate = $softwareDate->format('Y-m-d');

        return $softwareDate;
    }

    public static function isHoliday($date, $targetBranchId)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        $isHoliday = 0;
        //get holidays
        $holiday = (int) DB::table('mfn_setting_holiday')->where('status', 1)->where('date', $date)->value('id');
        if ($holiday > 0) {
            $isHoliday = 1;
        }

        // get the organazation id and branch id of the loggedin user
        if ($isHoliday != 1) {
            $userBranchId = Auth::user()->branchId;
            $userOrgId = Auth::user()->company_id_fk;

            /*print_r($userBranchId);
            print_r($userOrgId);
            exit();*/

            if ($targetBranchId != 1) {
                $userBranchId = $targetBranchId;
                $userOrgId = DB::table('gnr_branch')->where('id', $targetBranchId)->value('companyId');
            }

            $holiday = (int) DB::table('mfn_setting_orgBranchSamity_holiday')
                ->where('status', 1)
                ->where(function ($query) use ($userBranchId, $userOrgId) {
                    $query->where('ogrIdFk', '=', $userOrgId)
                        ->orWhere('branchIdFk', '=', $userBranchId);
                })
                ->where('dateFrom', '<=', $date)
                ->where('dateTo', '>=', $date)
                ->value('id');
            if ($holiday > 0) {
                $isHoliday = 1;
            }
        }

        return $isHoliday;
    }

    public static function getYearsOption()
    {

        $yearsOption = array_combine(range(date("Y") + 1, 2012), range(date("Y") + 1, 2012));

        return $yearsOption;
    }

    public static function getMonthsOption()
    {

        $monthsOption = array(
            1   =>  'January',
            2   =>  'February',
            3   =>  'March',
            4   =>  'April',
            5   =>  'May',
            6   =>  'June',
            7   =>  'July',
            8   =>  'August',
            9   =>  'September',
            10  =>  'October',
            11  =>  'November',
            12  =>  'December'
        );

        return $monthsOption;
    }

    public static function getWorkingWeeksBaseOnYearNMonth($year, $month)
    {
        $month = str_pad($month, 2, '0', STR_PAD_LEFT);
        $startDate = Carbon::parse($year . '-' . $month . '-01');
        $endDate = $startDate->copy()->endOfMonth();

        $weeklyHolidays = DB::table('mfn_setting_holiday')
            ->where('softDel', 0)
            ->where('isWeeklyHoliday', 1)
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->pluck('date')
            ->toArray();

        ///////////
        $weeksArray = array();
        foreach ($weeklyHolidays as $key => $weeklyHoliday) {
            $weeklyHoliday = Carbon::parse($weeklyHoliday);

            if ($weeklyHoliday->eq($startDate)) {
                $startDate->addDay();
                continue;
            } else if ($weeklyHoliday->gt($startDate)) {
                $indexString = $startDate->format('Y-m-d') . ',' . $weeklyHoliday->copy()->subDay()->format('Y-m-d');
                $valueString = $startDate->format('Y-m-d') . ' to ' . $weeklyHoliday->copy()->subDay()->format('Y-m-d');
                $weeksArray[$indexString] = $valueString;
                $startDate = $weeklyHoliday->addDay();
            }
        }
        // get the last index
        if ($endDate->gte($startDate)) {
            $indexString = $startDate->format('Y-m-d') . ',' . $endDate->format('Y-m-d');
            $valueString = $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d');
            $weeksArray[$indexString] = $valueString;
        }
        ///////////
        return $weeksArray;
    }

    public static function getLoanAccountsOfParticularMember($memberId)
    {
        return DB::table('mfn_loan')
            ->where('softDel', 0)
            ->where('memberIdFk', $memberId)
            ->orderBy('loanCode')
            ->pluck('loanCode', 'id')
            ->all();
    }

    public static function getActiveLoanAccountsOfParticularMember($memberId)
    {
        return DB::table('mfn_loan')
            ->where('softDel', 0)
            ->where('isLoanCompleted', 0)
            ->where('memberIdFk', $memberId)
            ->orderBy('loanCode')
            ->pluck('loanCode', 'id')
            ->all();
    }

    public static function getRegularLoanIdsByDate($date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        $loanIds = DB::select("SELECT DISTINCT `loanIdFk` FROM `mfn_loan_schedule` as t1 WHERE `scheduleDate` = (SELECT MAX(`scheduleDate`) FROM mfn_loan_schedule WHERE `loanIdFk`=t1.`loanIdFk`) AND softDel=0 AND `scheduleDate`>?", [$date]);
        $loanIds = collect($loanIds);
        $completeLoanIds = DB::table('mfn_loan')
            ->where('softDel', 0)
            ->where('isLoanCompleted', 1)
            ->where('loanCompletedDate', '<=', $date)
            ->pluck('id')
            ->toArray();

        return array_diff($loanIds->pluck('loanIdFk')->toArray(), $completeLoanIds);
    }

    public static function getRegularLoanIdsByDateNLoanIds($date, $targetLoanIds)
    {
        return array_intersect(self::getRegularLoanIdsByDate($date), $targetLoanIds);
    }

    public static function getFundungOrgWiseLoanProductIds($funOrgId)
    {
        return DB::table('mfn_loans_product')
            ->where('softDel', 0)
            ->where('fundingOrganizationId', $funOrgId)
            ->pluck('id')
            ->toArray();
    }

    public static function getOnDateFieldOfficerIds($samityIds, $date)
    {


        $samityFieldOfficerInfo = DB::select("SELECT t1.`id`,IF(t2.fieldOfficerId>0,t2.`fieldOfficerId`,t1.fieldOfficerId) AS `fieldOfficerId` FROM `mfn_samity` AS t1
            LEFT JOIN (SELECT samityId,fieldOfficerId FROM mfn_samity_field_officer_change WHERE effectiveDate>? ORDER BY effectiveDate) AS t2 ON t1.id = t2.samityId
            WHERE t1.`openingDate`<=?", [$date, $date]);

        $samityFieldOfficerInfo = collect($samityFieldOfficerInfo);

        $samityFieldOfficerInfo = $samityFieldOfficerInfo->whereIn('id', $samityIds);

        return $samityFieldOfficerInfo->unique('fieldOfficerId')->pluck('fieldOfficerId')->toArray();
    }

    public static function getAllChildsOfAParentInLedger($parentLedgerId)
    {
        $childs = [];
        $isGroupHead = 1;
        $parentLedgerIds = [$parentLedgerId];

        while ($isGroupHead == 1) {
            $immediateChilds = DB::table('acc_account_ledger')->whereIn('parentId', $parentLedgerIds)->select('id', 'isGroupHead')->get();
            $parentLedgerIds = $immediateChilds->pluck('id')->toArray();
            $isGroupHead = $immediateChilds->max('isGroupHead');
            if ($isGroupHead == 0) {
                $childs = $immediateChilds->pluck('id')->toArray();
            }
        }

        return $childs;
    }

    public static function getAllChildsOfAParentInLedgerByCode($parentLedgerCode)
    {
        $childs = [];
        $isGroupHead = 1;
        $parentLedgerId = DB::table('acc_account_ledger')->where('code', $parentLedgerCode)->first()->id;
        $parentLedgerIds = [$parentLedgerId];

        while ($isGroupHead == 1) {
            $immediateChilds = DB::table('acc_account_ledger')->whereIn('parentId', $parentLedgerIds)->select('id', 'isGroupHead')->get();
            $parentLedgerIds = $immediateChilds->pluck('id')->toArray();
            $isGroupHead = $immediateChilds->max('isGroupHead');
            if ($isGroupHead == 0) {
                $childs = $immediateChilds->pluck('id')->toArray();
            }
        }

        return $childs;
    }

    public static function getFundingOrgList()
    {
        return DB::table('mfn_funding_organization')
            ->pluck('name', 'id')
            ->all();
    }

    public static function getFiscalYearList()
    {
        return DB::table('gnr_fiscal_year')
            ->orderBy('fyStartDate')
            ->pluck('name', 'id')
            ->toArray();
    }


    public static function getFilteredBranhIds($filReportLevel, $filBranch, $filArea, $filZone, $filRegion)
    {
        $userBranchId = Auth::user()->branchId;

        if ($userBranchId != 1) {
            $filBranchIds = [$userBranchId];
        } else {
            /// Report Level Branch
            if ($filReportLevel == "Branch") {
                if ($filBranch != '' || $filBranch != null) {
                    $filBranch = (int) $filBranch;
                    $filBranchIds = [$filBranch];
                } else {
                    $filBranchIds = array_map('intval', DB::table('gnr_branch')->pluck('id')->toArray());
                }
            }

            /// Report Level Area
            elseif ($filReportLevel == "Area") {
                $filBranchIds = array_map('intval', explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_area')->where('id', $filArea)->value('branchId'))));
            }
            /// Report Level Zone
            elseif ($filReportLevel == "Zone") {
                $areaIds =  explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_zone')->where('id', $filZone)->value('areaId')));

                $filBranchIds = array();
                foreach ($areaIds as $key => $areaId) {
                    $branchIds = array_map('intval', explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_area')->where('id', $areaId)->value('branchId'))));
                    $filBranchIds = array_merge($filBranchIds, $branchIds);
                }
                $filBranchIds = array_unique($filBranchIds);
            }
            /// Report Level Region
            elseif ($filReportLevel == "Region") {
                $zoneIds = explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_region')->where('id', $filRegion)->value('zoneId')));

                $filBranchIds = array();
                foreach ($zoneIds as $zoneId) {
                    $areaIds =  explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_zone')->where('id', $zoneId)->value('areaId')));
                    foreach ($areaIds as $key => $areaId) {
                        $branchIds = array_map('intval', explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_area')->where('id', $areaId)->value('branchId'))));
                        $filBranchIds = array_merge($filBranchIds, $branchIds);
                    }
                }
                $filBranchIds = array_unique($filBranchIds);
            }
        }

        return $filBranchIds;
    }

    public static function getFilteredLedgerIds($projectId, $branchIds, $companyId)
    {
        $allLedgers = DB::table('acc_account_ledger')
            // ->where('isGroupHead', 0)
            ->select('id', 'projectBranchId')
            ->get();

        $matchedIds = array();

        foreach ($allLedgers as $singleLedger) {
            $splitArray = str_replace(array('[', ']', '"', ''), '',  $singleLedger->projectBranchId);

            $splitArrayFirstValue = explode(",", $splitArray);
            $splitArraySecondValue = explode(":", $splitArrayFirstValue[0]);

            $array_length = substr_count($splitArray, ",");
            $arrayProjects = array();
            $temp = null;
            for ($i = 0; $i < $array_length + 1; $i++) {

                $splitArrayFirstValue = explode(",", $splitArray);

                $splitArraySecondValue = explode(":", $splitArrayFirstValue[$i]);
                $firstIndexValue = (int) $splitArraySecondValue[0];
                $secondIndexValue = (int) $splitArraySecondValue[1];

                if ($firstIndexValue == 0) {
                    if ($secondIndexValue == 0) {
                        array_push($matchedIds, $singleLedger->id);
                    }
                } else {
                    if ($firstIndexValue == $projectId) {
                        if ($secondIndexValue == 0) {
                            array_push($matchedIds, $singleLedger->id);
                        } elseif (in_array($secondIndexValue, $branchIds)) {
                            array_push($matchedIds, $singleLedger->id);
                        }
                    }
                }
            }   //for
        }       //foreach

        /*$ledgers = DB::table('acc_account_ledger')
        ->whereIn('acc_account_ledger.id', $matchedIds)
        ->where('status', 1)
        ->where('companyIdFk', $companyId)
        ->select('id', 'name', 'code', 'accountTypeId', 'isGroupHead', 'level', 'parentId')
        ->orderBy('code')
        ->get();*/

        return $matchedIds;
    }

    public static function updateLoanStatusNSchedule($loanId)
    {

        $totalRepayAmount = DB::table('mfn_loan')
            ->where('id', $loanId)
            ->sum('totalRepayAmount');

        $collections = DB::table('mfn_loan_collection')
            ->where('softDel', 0)
            ->where('amount', '>', 0)
            ->where('loanIdFk', $loanId)
            ->select('amount', 'collectionDate')
            ->get();

        $rebate = DB::table('mfn_loan_rebates')
            ->where('softDel', 0)
            ->where('loanIdFk', $loanId)
            ->select('amount', 'date')
            ->get();

        $waiver = DB::table('mfn_loan_waivers')
            ->where('softDel', 0)
            ->where('loanIdFk', $loanId)
            ->select('amount', 'date')
            ->get();

        $writeOff = DB::table('mfn_loan_write_off')
            ->where('softDel', 0)
            ->where('loanIdFk', $loanId)
            ->select('amount', 'date')
            ->get();

        $collectionAmount = DB::table('mfn_opening_balance_loan')
            ->where('softDel', 0)
            ->where('loanIdFk', $loanId)
            ->sum('paidLoanAmountOB');

        $collectionAmount += $collections->sum('amount');

        $collectionAmount += $rebate->sum('amount');
        $collectionAmount += $waiver->sum('amount');
        $collectionAmount += $writeOff->sum('amount');


        if ($collectionAmount >= $totalRepayAmount) {
            $loanCompleteDate = max($collections->max('collectionDate'), $rebate->max('date'), $waiver->max('date'), $writeOff->max('date'));

            DB::table('mfn_loan')->where('id', $loanId)->update([
                'isLoanCompleted' => 1,
                'loanCompletedDate' => $loanCompleteDate
            ]);

            DB::table('mfn_loan_schedule')->where('loanIdFk', $loanId)->update([
                'isCompleted' => 1,
                'isPartiallyPaid' => 0,
                'partiallyPaidAmount' => 0,
            ]);
        } else {
            DB::table('mfn_loan')->where('id', $loanId)->update([
                'isLoanCompleted' => 0,
                'loanCompletedDate' => '0000-00-00'
            ]);

            $shedules = MfnLoanSchedule::active()->where('loanIdFk', $loanId)->get();
            $totalCollectionAmount = $collectionAmount;

            foreach ($shedules as $key => $shedule) {
                if ($totalCollectionAmount >= $shedule->installmentAmount) {
                    $shedule->isCompleted = 1;
                    $shedule->isPartiallyPaid = 0;
                    $shedule->partiallyPaidAmount = 0;
                    $shedule->save();
                } elseif ($totalCollectionAmount > 0) {
                    $shedule->isCompleted = 0;
                    $shedule->isPartiallyPaid = 1;
                    $shedule->partiallyPaidAmount = $totalCollectionAmount;
                    $shedule->save();
                } else {
                    $shedule->isCompleted = 0;
                    $shedule->isPartiallyPaid = 0;
                    $shedule->partiallyPaidAmount = 0;
                    $shedule->save();
                }

                $totalCollectionAmount = $totalCollectionAmount - $shedule->installmentAmount;
            }
        }
    }

    /**
     * [createLog description]
     * @param  [string] $tableName    
     * @param  [string] $operation    
     * @param  [array] $primaryIds   
     * @param  [object] $previousData 
     * @return [boolean]               
     */
    public static function createLog($tableName, $operation, $primaryIds, $previousData = null)
    {

        $queryString = "SHOW KEYS FROM $tableName WHERE Key_name = 'PRIMARY'";

        $primaryKey = DB::select($queryString);
        $primaryKey = collect($primaryKey);
        $primaryKey = $primaryKey[0]->Column_name;

        if ($operation != 'insert' && $previousData == null) {
            return false;
        }

        $previousData = json_encode($previousData);
        $currentData = json_encode(DB::table($tableName)->whereIn(DB::raw($primaryKey), $primaryIds)->get());
        $isInserted = DB::table('log')->insert([
            'user_id'       => Auth::user()->id,
            'time'          => Carbon::now(),
            'ip_address'    => $_SERVER['REMOTE_ADDR'],
            'table_name'    => $tableName,
            'operation'     => $operation,
            'primary_ids'   => implode($primaryIds),
            'previous_data' => $previousData,
            'current_data'  => $currentData
        ]);

        if ($isInserted) {
            return true;
        } else {
            return false;
        }
    }

    public static function getLoanOutstanding($loanId)
    {
        $loan = DB::table('mfn_loan')->where('id', $loanId)->select('id', 'totalRepayAmount', 'loanAmount')->first();

        $openingBalance = DB::table('mfn_opening_balance_loan')
            ->where('softDel', 0)
            ->where('loanIdFk', $loan->id)
            ->select('paidLoanAmountOB', 'principalAmountOB')
            ->get();

        $collections = DB::table('mfn_loan_collection')
            ->where('softDel', 0)
            ->where('loanIdFk', $loan->id)
            ->select(DB::raw('SUM(amount) AS amount, SUM(principalAmount) AS principalAmount'))
            ->get();

        $waivers = DB::table('mfn_loan_waivers')
            ->where('softDel', 0)
            ->where('loanIdFk', $loan->id)
            ->select('amount', 'principalAmount')
            ->get();

        $rebates = DB::table('mfn_loan_rebates')
            ->where('softDel', 0)
            ->where('loanIdFk', $loan->id)
            ->select('amount')
            ->get();

        $writeOffs = DB::table('mfn_loan_write_off')
            ->where('softDel', 0)
            ->where('loanIdFk', $loan->id)
            ->select('amount', 'principalAmount')
            ->get();

        $paidAmount = $openingBalance->sum('paidLoanAmountOB') + $collections->sum('amount') + $waivers->sum('amount') + $rebates->sum('amount') + $writeOffs->sum('amount');

        $paidAmountPrincipal = $openingBalance->sum('principalAmountOB') + $collections->sum('principalAmount') + $waivers->sum('principalAmount') + $writeOffs->sum('principalAmount');

        $outstanding = $loan->totalRepayAmount - $paidAmount;

        $outstandingPrincipal = $loan->loanAmount - $paidAmountPrincipal;

        $outstandingInterest = $outstanding - $outstandingPrincipal;

        return array(
            'paidAmount'            => $paidAmount,
            'paidAmountPrincipal'   => $paidAmountPrincipal,
            'outstanding'           => $outstanding,
            'outstandingPrincipal'  => $outstandingPrincipal,
            'outstandingInterest'   => $outstandingInterest
        );
    }

    /**
     * This function returns loan outstanding except something e.g. loan waiver, rebate, writeoff or a specific collection. this is mainly required when updateing these.
     * @param  [int] $loanId    
     * @param  [string] $tableName 
     * @param  [int] $primaryId 
     * @return [float]            
     */
    public static function getLoanOutstandingExceptSomething($loanId, $tableName, $primaryId)
    {
        $loan = DB::table('mfn_loan')->where('id', $loanId)->select('id', 'totalRepayAmount', 'loanAmount')->first();

        $openingBalance = DB::table('mfn_opening_balance_loan')
            ->where('softDel', 0)
            ->where('loanIdFk', $loan->id)
            ->select('paidLoanAmountOB', 'principalAmountOB')
            ->get();

        $collections = DB::table('mfn_loan_collection');
        $waivers = DB::table('mfn_loan_waivers');
        $rebates = DB::table('mfn_loan_rebates');
        $writeOffs = DB::table('mfn_loan_write_off');

        if ($tableName == 'mfn_loan_collection') {
            $collections = $collections->where('id', '!=', $primaryId);
        } elseif ($tableName == 'mfn_loan_waivers') {
            $waivers = $waivers->where('id', '!=', $primaryId);
            $rebates = $rebates->where('waiverId', '!=', $primaryId);
        } elseif ($tableName == 'mfn_loan_rebates') {
            $rebates = $rebates->where('waiverId', '!=', $primaryId);
        } elseif ($tableName == 'mfn_loan_write_off') {
            $writeOffs = $writeOffs->where('waiverId', '!=', $primaryId);
        }

        $collections = $collections
            ->where('softDel', 0)
            ->where('loanIdFk', $loan->id)
            ->select(DB::raw('SUM(amount) AS amount, SUM(principalAmount) AS principalAmount'))
            ->get();

        $waivers = $waivers
            ->where('softDel', 0)
            ->where('loanIdFk', $loan->id)
            ->select('amount', 'principalAmount')
            ->get();

        $rebates = $rebates
            ->where('softDel', 0)
            ->where('loanIdFk', $loan->id)
            ->select('amount')
            ->get();

        $writeOffs = $writeOffs
            ->where('softDel', 0)
            ->where('loanIdFk', $loan->id)
            ->select('amount', 'principalAmount')
            ->get();

        $outstanding = $loan->totalRepayAmount - $openingBalance->sum('paidLoanAmountOB') - $collections->sum('amount') - $waivers->sum('amount') - $rebates->sum('amount') - $writeOffs->sum('amount');

        $outstandingPrincipal = $loan->loanAmount - $openingBalance->sum('principalAmountOB') - $collections->sum('principalAmount') - $waivers->sum('principalAmount') - $writeOffs->sum('principalAmount');

        $outstandingInterest = $outstanding - $outstandingPrincipal;

        return array(
            'outstanding'           => $outstanding,
            'outstandingPrincipal'  => $outstandingPrincipal,
            'outstandingInterest'   => $outstandingInterest
        );
    }

    /**
     * This function returns loan's advance amount and due amount according to a date
     * @param  [int] $loanId
     * @return [array]
     */
    public static function getLoanAdvanceDue($loanId = null, $date = null)
    {

        if ($loanId == null || $date == null) {
            return false;
        }

        $date = Carbon::parse($date)->format('Y-m-d');

        $loan = DB::table('mfn_loan')->where('id', $loanId)->select('id', 'totalRepayAmount', 'loanAmount')->first();

        $openingBalance = DB::table('mfn_opening_balance_loan')
            ->where('softDel', 0)
            ->where('loanIdFk', $loan->id)
            ->select('paidLoanAmountOB', 'principalAmountOB')
            ->get();

        $collections = DB::table('mfn_loan_collection')
            ->where('softDel', 0)
            ->where('loanIdFk', $loan->id)
            ->where('collectionDate', '<=', $date)
            ->select(DB::raw('SUM(amount) AS amount, SUM(principalAmount) AS principalAmount'))
            ->get();

        $waivers = DB::table('mfn_loan_waivers')
            ->where('softDel', 0)
            ->where('loanIdFk', $loan->id)
            ->where('date', '<=', $date)
            ->select('amount', 'principalAmount')
            ->get();

        $rebates = DB::table('mfn_loan_rebates')
            ->where('softDel', 0)
            ->where('loanIdFk', $loan->id)
            ->where('date', '<=', $date)
            ->select('amount')
            ->get();

        $writeOffs = DB::table('mfn_loan_write_off')
            ->where('softDel', 0)
            ->where('loanIdFk', $loan->id)
            ->where('date', '<=', $date)
            ->select('amount', 'principalAmount')
            ->get();

        $schedules = DB::table('mfn_loan_schedule')
            ->where('softDel', 0)
            ->where('loanIdFk', $loan->id)
            ->where('scheduleDate', '<=', $date)
            ->select(DB::raw('SUM(installmentAmount) AS installmentAmount, SUM(principalAmount) AS principalAmount'))
            ->get();

        $dueAmount = $schedules->sum('installmentAmount') - $openingBalance->sum('paidLoanAmountOB') - $collections->sum('amount') - $waivers->sum('amount') - $rebates->sum('amount') - $writeOffs->sum('amount');

        $advanceAmount = -$dueAmount;

        $dueAmount = $dueAmount <=  0 ? 0 : $dueAmount;
        $advanceAmount = $advanceAmount <=  0 ? 0 : $advanceAmount;

        $dueAmountPrincipal = $schedules->sum('principalAmount') - $openingBalance->sum('principalAmountOB') - $collections->sum('principalAmount') - $waivers->sum('principalAmount') - $writeOffs->sum('principalAmount');

        $advanceAmountPrincipal = -$dueAmountPrincipal;

        $dueAmountPrincipal = $dueAmountPrincipal <=  0 ? 0 : $dueAmountPrincipal;
        $advanceAmountPrincipal = $advanceAmountPrincipal <=  0 ? 0 : $advanceAmountPrincipal;

        return array(
            'dueAmount'                 => $dueAmount,
            'dueAmountPrincipal'        => $dueAmountPrincipal,
            'advanceAmount'             => $advanceAmount,
            'advanceAmountPrincipal'    => $advanceAmountPrincipal
        );
    }


	/**
	 * this function returns the holidays date
	 *
	 * @param [date] $dateFrom it is optional, if it is given then this function will return holidays from this date.
	 * @return array
	 */
	public static function getHolidaysForOrg($dateFrom = null)
	{
		$holidays = array();

		$holidays = DB::table('mfn_setting_holiday')
			->where('softDel', 0);

		if ($dateFrom != null) {
			$dateFrom = Carbon::parse($dateFrom)->format('Y-m-d');
			$holidays = $holidays->where('date', '>=', $dateFrom);
		}

		$holidays = $holidays->pluck('date')->toArray();

		$orgHolidays = DB::table('mfn_setting_orgBranchSamity_holiday')
			->where('softDel', 0)
			->where('isOrgHoliday', 1);

		if ($dateFrom != null) {
			$orgHolidays = $orgHolidays->where('dateTo', '>=', $dateFrom);
		}

		$orgHolidays = $orgHolidays->select('dateFrom', 'dateTo')->get();

		foreach ($orgHolidays as $orgHoliday) {
			$dateFrom = Carbon::parse($orgHoliday->dateFrom);
			$dateTo = Carbon::parse($orgHoliday->dateTo);

			while ($dateFrom->lte($dateTo)) {
				array_push($holidays, $dateFrom->format('Y-m-d'));
				$dateFrom->addDay();
			}
		}

		$holidays = array_unique($holidays);

		return $holidays;
	}

	/**
	 * this function returns the holidays date
	 *
	 * @param [int] $branchId 
	 * @param [date] $dateFrom it is optional, if it is given then this function will return holidays from this date.
	 * @return array
	 */
	public static function getHolidaysForBranch($branchId, $dateFrom = null)
	{
		$holidays = array();

		$holidays = DB::table('mfn_setting_holiday')
			->where('softDel', 0);

		if ($dateFrom != null) {
			$dateFrom = Carbon::parse($dateFrom)->format('Y-m-d');
			$holidays = $holidays->where('date', '>=', $dateFrom);
		}

		$holidays = $holidays->pluck('date')->toArray();

		$branchHolidays = DB::table('mfn_setting_orgBranchSamity_holiday')
			->where('softDel', 0)
			->where(function ($query) use ($branchId) {
				$query->where('branchIdFk', $branchId)
					->orWhere('isOrgHoliday', 1);
			});

		if ($dateFrom != null) {
			$branchHolidays = $branchHolidays->where('dateTo', '>=', $dateFrom);
		}

		$branchHolidays = $branchHolidays->select('dateFrom', 'dateTo')->get();

		foreach ($branchHolidays as $branchHoliday) {
			$dateFrom = Carbon::parse($branchHoliday->dateFrom);
			$dateTo = Carbon::parse($branchHoliday->dateTo);

			while ($dateFrom->lte($dateTo)) {
				array_push($holidays, $dateFrom->format('Y-m-d'));
				$dateFrom->addDay();
			}
		}

		$holidays = array_unique($holidays);

		return $holidays;
	}

	/**
	 * this function returns the holidays date
	 *
	 * @param [int] $samityId 
	 * @param [date] $dateFrom it is optional, if it is given then this function will return holidays from this date.
	 * @return array
	 */
	public static function getHolidaysForSamity($samityId, $dateFrom = null)
	{
		$holidays = array();

		$holidays = DB::table('mfn_setting_holiday')
			->where('softDel', 0);

		if ($dateFrom != null) {
			$dateFrom = Carbon::parse($dateFrom)->format('Y-m-d');
			$holidays = $holidays->where('date', '>=', $dateFrom);
		}

		$holidays = $holidays->pluck('date')->toArray();

		$branchId = DB::table('mfn_samity')->where('id', $samityId)->first()->branchId;

		$samityHolidays = DB::table('mfn_setting_orgBranchSamity_holiday')
			->where('softDel', 0)
			->where(function ($query) use ($branchId, $samityId) {
				$query->where('branchIdFk', $branchId)
					->orWhere('samityIdFk', $samityId)
					->orWhere('isOrgHoliday', 1);
			});

		if ($dateFrom != null) {
			$samityHolidays = $samityHolidays->where('dateTo', '>=', $dateFrom);
		}

		$samityHolidays = $samityHolidays->select('dateFrom', 'dateTo')->get();

		foreach ($samityHolidays as $samityHoliday) {
			$dateFrom = Carbon::parse($samityHoliday->dateFrom);
			$dateTo = Carbon::parse($samityHoliday->dateTo);

			while ($dateFrom->lte($dateTo)) {
				array_push($holidays, $dateFrom->format('Y-m-d'));
				$dateFrom->addDay();
			}
		}

		$holidays = array_unique($holidays);

		return $holidays;
	}

	public static function getFixedGovHolidaysByYears($yearFrom, $yearTo)
	{
		$fixedGovHolidays = DB::table('mfn_setting_gov_holiday')->pluck('date')->toArray();

		$dates = array();
		while ($yearFrom <= $yearTo) {
			foreach ($fixedGovHolidays as $fixedGovHoliday) {
				array_push($dates, Carbon::parse($fixedGovHoliday . '-' . $yearFrom)->format('Y-m-d'));
			}
			$yearFrom++;
		}

		return $dates;
	}

	public static function getWeeklyHolidaysByYears($yearFrom, $yearTo)
	{
		$weekMap = [
			1 => Carbon::SATURDAY, // SA
			2 => Carbon::SUNDAY, // SU
			3 => Carbon::MONDAY, // MO
			4 => Carbon::TUESDAY, // TU
			5 => Carbon::WEDNESDAY, // WE
			6 => Carbon::THURSDAY, // TH
			7 => Carbon::FRIDAY  // FR
		];

		$startDate = Carbon::parse('01-01-'.$yearFrom);
		$endDate = Carbon::parse('31-12-'. $yearTo);

		$weeklyHolidaysSettings = DB::table('mfn_setting_weekly_holiday')
		->where(function ($query) use ($startDate)
		{
			$query->where('dateTo', '>=', $startDate->format('Y-m-d'))
			->orWhere('dateTo','0000-00-00');
		})
		->where('dateFrom','<=', $endDate->format('Y-m-d'))
		->orderBy('dateFrom')
		->get();

		$holidays = [];

		foreach ($weeklyHolidaysSettings as $weeklyHolidaysSetting) {
			$weekDays = explode(',', $weeklyHolidaysSetting->weeklyHolidayIds);
			$dateFrom = Carbon::parse(max($startDate->format('Y-m-d'), $weeklyHolidaysSetting->dateFrom));

			if ($weeklyHolidaysSetting->dateTo == '0000-00-00') {
				$dateTo = $endDate;
			}
			else{
				$dateTo = min($endDate->format('Y-m-d'), $weeklyHolidaysSetting->dateTo);
			}
			$dateTo = Carbon::parse($dateTo);

			$dayFromWeekDay = $dateFrom->dayOfWeek == 6 ? 1 : $dateFrom->dayOfWeek + 2;

			if (in_array($dayFromWeekDay, $weekDays)) {
				array_push($holidays, $dateFrom->format('Y-m-d'));
			}

			while ($dateFrom->lte($dateTo)) {
				foreach ($weekDays as $weekDay) {
					array_push($holidays, $dateFrom->copy()->next($weekMap[$weekDay])->format('Y-m-d'));			
				}
				$dateFrom->addDays(7);
			}

			// REMOVE THE CONTENTS WHICH IS LARGER THAN THE $dateTo
			$holidays = array_filter($holidays, function ($value) use ($dateTo) {
				return $value <= $dateTo->format('Y-m-d');
			});
		}
		return $holidays;
	}

	/**
	 * this function generates loan schedule for a loan, if sceesfully generated then return true otherwise false.
	 *
	 * @param [int] $loanId
	 * @return true/false
	 */
	public static function generateLoanSchedule($loanId)
	{
		$schedules = self::makeLoanSchedule($loanId);
		if (isset($schedules['error'])) {
			return $schedules;
		}
		$info = self::storeLoanSchedule($loanId, $schedules);
		self::updateLoanScheduleStstus($loanId);

		return $info;
	}

	/**
	 * IT GENERATES LOAN SCHEDULE FOR A LOAN
	 *
	 * @param [int] $loanId
	 * @param [array] $holidays
	 * @return array
	 */
	public static function makeLoanSchedule($loanId, &$holidays = null, $isOnlyForDates = null)
	{
		// echo "Loan ID: $loanId <br>";

		$loan = DB::table('mfn_loan')->where('id', $loanId)->select('id', 'loanTypeId', 'productIdFk', 'branchIdFk', 'samityIdFk', 'repaymentFrequencyIdFk', 'repaymentNo', 'actualNumberOfInstallment', 'disbursementDate', 'firstRepayDate', 'loanAmount', 'interestRateIndex', 'totalRepayAmount', 'extraInstallmentAmount', 'actualInstallmentAmount', 'loanRepayPeriodIdFk')->first();

		if ($holidays == null) {
			// GET THE HOLIDAYS
			$holidays = self::getHolidaysForSamity($loan->samityIdFk, $loan->disbursementDate);

			// FIXED GOV. HOLIDAYS MAY NOT ASSIGN TO THE CALLENDER. BUT WE WILL CONSIDER THE FIXED GOV. HOLIDAYS FOR THE UPCOMMING YEARS. WE WILL CALCULATE THE EXPECTED LOAN COMPLETE YEAR. FOR THE SAFETY PURPOSE WE WILL GET THE HOLIDAYS FOR THE NET ONE YEAR OF THE EXPEDTED LOAN COMPLETE DATE.
			$loanPeriodInMonths = DB::table('mfn_loan_repay_period')->where('id', $loan->loanRepayPeriodIdFk)->first()->inMonths;
			$expectedLoanCompleteYear = Carbon::parse($loan->firstRepayDate)->addMonthsNoOverflow($loanPeriodInMonths)->format('Y');
			$maxGovHolidayYearFromCallender = (int) DB::table('mfn_setting_holiday')->where('isGovHoliday', 1)->max('year');

			if ($maxGovHolidayYearFromCallender < $expectedLoanCompleteYear + 1) {
				$fixedGovHolidays = self::getFixedGovHolidaysByYears(Carbon::parse($loan->disbursementDate)->format('Y'), $expectedLoanCompleteYear + 1);
				$weeklyHolidays = self::getWeeklyHolidaysByYears(Carbon::parse($loan->disbursementDate)->format('Y'), $expectedLoanCompleteYear + 1);
				$holidays = array_unique(array_merge($holidays, $fixedGovHolidays, $weeklyHolidays));
			}

			sort($holidays);
		}

		// dd($holidays);

		if ($loan->repaymentNo != $loan->actualNumberOfInstallment) {
			return ['error' => true, 'msg' => 'repaymentNo and actualNumberOfInstallment are not equal.'];
		}

		// NOTE: loanTypeId 1 MEANS IT IS REGULAR LOAN AND 2 MEANS ONE TIME LOAN.
		if ($loan->loanTypeId == 1) {
			// MAKE SCHEDULE FOR REGULAR LOAN

			// GET THE INSTALLMENT INFORMATION
			$actualInstallmentAmount = round($loan->totalRepayAmount / $loan->actualNumberOfInstallment, 2);
			$extraInstallmentAmount = $loan->extraInstallmentAmount;
			$installmentAmount = $actualInstallmentAmount + $extraInstallmentAmount;
			$principalAmount = round($installmentAmount / $loan->interestRateIndex, 5);
			$interestAmount = $installmentAmount - $principalAmount;

			// IN REGULAR LOAN THERE ARE TWO TYPES OF LOAN, ONE IS WEEKLY LOAN AND ANOTHER IS MONTHLY LOAN. repaymentFrequencyIdFk 1 means it is weekly loan and 2 means it is monthly loan.
			if ($loan->repaymentFrequencyIdFk == 1) {
				$scheduleDates = self::makeLoanScheduleForWeeklyLoan($loan, $holidays);
			} else {
				$scheduleDates = self::makeLoanScheduleForMonthlyLoan($loan, $holidays);
			}
		} else {
			// MAKE SCHEDULE FOR ONLE TIME LOAN.
			return ['error' => true, 'msg' => 'It is One Time Loan.'];
		}

		// MAKE THE SCHEDULE ARRAY
		$schedules = collect();
		for ($i = 0; $i < $loan->actualNumberOfInstallment; $i++) {

			// IF IT IS THE LAST INSTALLMENT THEN THE AMOUNT FIGURE WILL BE CHANGE.
			if ($i == $loan->actualNumberOfInstallment - 1) {
				$installmentAmount = $loan->totalRepayAmount - ($loan->actualNumberOfInstallment - 1) * $installmentAmount;				
				$principalAmount = $loan->loanAmount - ($loan->actualNumberOfInstallment - 1) * $principalAmount;
				$interestAmount = $installmentAmount - $principalAmount;
				$actualInstallmentAmount = 0;
				$extraInstallmentAmount = 0;
			}

			$data = array(
				'installmentSl'	=> $i + 1,
				'installmentAmount'	=> $installmentAmount,
				'actualInstallmentAmount'	=> $actualInstallmentAmount,
				'extraInstallmentAmount'	=> $extraInstallmentAmount,
				'principalAmount'	=> $principalAmount,
				'interestAmount'	=> $interestAmount,
				'scheduleDate'	=> $scheduleDates[$i]
			);

			$schedules->push($data);
		}

		// CHECK SOME DATA TO VALIDATE THAT THIS LOAN SCHEDULE IS CORRECT.
		if ($schedules->count() != $loan->actualNumberOfInstallment) {
			return ['error' => true, 'msg' => 'Number of Schedules Dates does not match with the actualNumberOfInstallment.'];
		}

		// IF IT IS REGULAR MONTHLY LOAN THE CHECK SCHEDULE IS GENERATED IN EVERY MONTH OR NOT
		if ($loan->loanTypeId == 1 && $loan->repaymentFrequencyIdFk == 2) {
			$disbursementDate = Carbon::parse($loan->disbursementDate);
			$compareingDate = $disbursementDate;
			foreach ($schedules as $schedule) {
				$scheduleDate = Carbon::parse($schedule['scheduleDate'])->endOfMonth()->subDays(10);
				if ($compareingDate->startOfMonth()->addDays(10)->diffInMonths($scheduleDate) != 1) {
					dd($compareingDate->format('Y-m-d'), $scheduleDate->format('Y-m-d'));
					return ['error' => true, 'msg' => 'Schedule Dates are not correct.'];
					break;
				}
				$compareingDate = $scheduleDate->startOfMonth();
			}
		}

		if ($isOnlyForDates == null) {
			if ($schedules->last()['installmentAmount'] > $schedules->first()['installmentAmount']) {
				return ['error' => true, 'msg' => 'Last Installment Amount is greater than the Installment Amount.'];
			}
			if ($schedules->sum('installmentAmount') != $loan->totalRepayAmount) {
				return ['error' => true, 'msg' => 'Total Repay Amount(' . $loan->totalRepayAmount . ') is not matched with the total Installment amount(' . $schedules->sum('installmentAmount') . ').'];
			}
			if (round($schedules->sum('principalAmount')) != $loan->loanAmount) {
				return ['error' => true, 'msg' => 'Loan Amount: ' . $loan->loanAmount . ' is not matched with the total Installment principal amount: .' . $schedules->sum('principalAmount')];
			}
		}
		

		return $schedules;
	}

	public static function makeLoanScheduleForWeeklyLoan($loan, &$holidays)
	{
		$weekMap = [
			1 => Carbon::SATURDAY, // SA
			2 => Carbon::SUNDAY, // SU
			3 => Carbon::MONDAY, // MO
			4 => Carbon::TUESDAY, // TU
			5 => Carbon::WEDNESDAY, // WE
			6 => Carbon::THURSDAY, // TH
			7 => Carbon::FRIDAY  // FR
		];

		$scheduleDates = array();
		$scheduleDate = Carbon::parse($loan->firstRepayDate);
		$scheduleDate->setWeekStartsAt(Carbon::SATURDAY);
		$scheduleDate->setWeekEndsAt(Carbon::FRIDAY);

		$samityDayChanges = DB::table('mfn_samity_day_change')
			->where('samityId', $loan->samityIdFk)
			->where('effectiveDate', '>', $loan->firstRepayDate)
			->groupBy('samityId', 'effectiveDate')
			->orderBy('effectiveDate')
			->get();

		// IF ANY SAMITY DAY CHANGES AFTER THE FIRST REPAY DATE THEN WE NEED TO CONSIDER THE SAMITY DAY CHANGES
		if (count($samityDayChanges) > 0) {
			$currentSamityDayChangeId = 0;
			for ($i = 0; $i < $loan->actualNumberOfInstallment; $i++) {

				// echo $scheduleDate->format('d-m-Y') . ' - ' . $scheduleDate->format('l') . '<br>';

				$currentSamityDayChangeId = self::reassignScheduleDateBaseOnSamityDayChangeForWeeklyLoan($samityDayChanges, $scheduleDate, $currentSamityDayChangeId, $weekMap, $holidays);

				while (in_array($scheduleDate->format('Y-m-d'), $holidays)) {
					$scheduleDate->addDays(7);
					$currentSamityDayChangeId = self::reassignScheduleDateBaseOnSamityDayChangeForWeeklyLoan($samityDayChanges, $scheduleDate, $currentSamityDayChangeId, $weekMap, $holidays);
				}

				array_push($scheduleDates, $scheduleDate->format('Y-m-d'));
				$scheduleDate->addDays(7);
			}
		} else {
			for ($i = 0; $i < $loan->actualNumberOfInstallment; $i++) {

				while (in_array($scheduleDate->format('Y-m-d'), $holidays)) {
					$scheduleDate->addDays(7);
				}
				array_push($scheduleDates, $scheduleDate->format('Y-m-d'));
				$scheduleDate->addDays(7);
			}
		}

		return $scheduleDates;
	}

	public static function reassignScheduleDateBaseOnSamityDayChangeForWeeklyLoan($samityDayChanges, $scheduleDate, $currentSamityDayChangeId, $weekMap, $holidays)
	{
		$samityDayChange = $samityDayChanges->where('effectiveDate', '<=', $scheduleDate->format('Y-m-d'))->sortByDesc('effectiveDate')->first();

		if ($samityDayChange != null && $currentSamityDayChangeId != $samityDayChange->id) {
			$ischnaged = 1;
			$currentSamityDayChangeId = $samityDayChange->id;
		} else {
			$ischnaged = 0;
		}

		if ($ischnaged == 1) {
			$scheduleDate->startOfWeek();
			if ($scheduleDate->dayOfWeek != $scheduleDate->copy()->next($weekMap[$samityDayChange->newSamityDayId])->dayOfWeek) {
				$scheduleDate->next($weekMap[$samityDayChange->newSamityDayId]);
			}
		}
		return $currentSamityDayChangeId;
	}

	public static function makeLoanScheduleForMonthlyLoan($loan, &$holidays, $customSamityDayChange = null)
	{
		$weekMap = [
			1 => Carbon::SATURDAY, // SA
			2 => Carbon::SUNDAY, // SU
			3 => Carbon::MONDAY, // MO
			4 => Carbon::TUESDAY, // TU
			5 => Carbon::WEDNESDAY, // WE
			6 => Carbon::THURSDAY, // TH
			7 => Carbon::FRIDAY  // FR
		];

		$samityDayIdOrigin = DB::table('mfn_samity')->where('id', $loan->samityIdFk)->first()->samityDayId;

		if ($customSamityDayChange != null) {
			$samityDayIdOrigin = $customSamityDayChange->newSamityDayId;
		}

		$scheduleDates = array();
		$scheduleDate = Carbon::parse($loan->firstRepayDate);
		$scheduleDate->setWeekStartsAt(Carbon::SATURDAY);
		$scheduleDate->setWeekEndsAt(Carbon::FRIDAY);
		$firstRepayDate = Carbon::parse($loan->firstRepayDate);
		$disbursementDate = Carbon::parse($loan->disbursementDate);

		$samityDayChanges = DB::table('mfn_samity_day_change')
			->where('samityId', $loan->samityIdFk)
			->where('effectiveDate', '>', $loan->disbursementDate)
			->groupBy('samityId', 'effectiveDate')
			->orderBy('effectiveDate')
			->select('effectiveDate', 'samityDayId', 'newSamityDayId')
			->get();

		if ($customSamityDayChange != null) {
			$samityDayChanges->push($customSamityDayChange);
		}

		for ($i = 0; $i < $loan->actualNumberOfInstallment; $i++) {

			$scheduleDate = $firstRepayDate->copy()->addMonthNoOverflow($i);
			$monthFirstDate = $scheduleDate->copy()->startOfMonth();
			$monthLastDate = $scheduleDate->copy()->endOfMonth();

			$samityDayChange = $samityDayChanges->where('effectiveDate', '>=', $scheduleDate->format('Y-m-d'))->sortBy('effectiveDate')->first();

			$samityDayId = $samityDayChange != null ? $samityDayChange->samityDayId : $samityDayIdOrigin;

			$scheduleDate->startOfWeek();

			if ($scheduleDate->dayOfWeek != $scheduleDate->copy()->next($weekMap[$samityDayId])->dayOfWeek || $scheduleDate->lt($monthFirstDate)) {
				$scheduleDate->next($weekMap[$samityDayId]);
			}
			if ($scheduleDate->gt($monthLastDate)) {
				$scheduleDate->subDays(7);
			}

			// FIRST SCHEDULE DATE SHOULD BE AFTER 30 DAYS IF NOT HOLIDAY OCCURS
			if ($i == 0) {
				while ($disbursementDate->diffInDays($scheduleDate) < 30 && $scheduleDate->copy()->addDays(7)->lte($monthLastDate)) {
					$scheduleDate->addDays(7);
				}
			}

			// IF THE SCHEDULE DATE IN HOLIDAY THEN FIND THE ALTERNATIVE SAMITY DAYS IN THIS MONTH
			if (in_array($scheduleDate->format('Y-m-d'), $holidays)) {

				$alternativeDays = [];
				$monthFirstDateCopy = $monthFirstDate->copy();
				while ($monthFirstDateCopy->lte($monthLastDate)) {
					if ($monthFirstDateCopy->dayOfWeek == $scheduleDate->dayOfWeek) {
						array_push($alternativeDays, $monthFirstDateCopy->format('Y-m-d'));
						$monthFirstDateCopy->addDays(7);
					} else {
						$monthFirstDateCopy->addDay();
					}
				}
				// REMOVE THE HOLIDAYS
				$alternativeDays = array_diff($alternativeDays, $holidays);

				// IF ANY ALTERNATIVE DAY FOUND, THEN FIRST TRY TO ASSIGN THE NEXT CLOSET DATE, IF NEXT DATES ARE NOT AVAILABE THEN ASSIGN THE PREVIOUS NEARREST DAY.
				if (count($alternativeDays) > 0) {

					$nextAlternativeDays = array_filter($alternativeDays, function ($value) use ($scheduleDate) {
						return $value > $scheduleDate->format('Y-m-d');
					});

					if (count($nextAlternativeDays) > 0) {
						$scheduleDate = Carbon::parse(min($nextAlternativeDays));
					} else {
						$previousAlternativeDays = array_filter($alternativeDays, function ($value) use ($scheduleDate) {
							return $value < $scheduleDate->format('Y-m-d');
						});
						$scheduleDate = Carbon::parse(max($previousAlternativeDays));
					}
				}
				// IF NO ALTERNATIVE DAY FOUND THEN ASSIGN TO ANY DAY AFTER OR BEFORE SCHEDULE DATE.
				else {
					$isScheduleAssigned = 0;
					$scheduleDateCopy = $scheduleDate->copy()->addDay();
					while ($scheduleDateCopy->lte($monthLastDate)) {
						if (!in_array($scheduleDateCopy->format('Y-m-d'), $holidays)) {
							$scheduleDate = $scheduleDateCopy;
							$isScheduleAssigned = 1;
							break;
						}
						$scheduleDateCopy->addDay();
					}
					if ($isScheduleAssigned == 0) {
						$scheduleDateCopy = $scheduleDate->copy()->subDay();
						while ($scheduleDateCopy->gte($monthFirstDate)) {
							if (!in_array($scheduleDateCopy->format('Y-m-d'), $holidays)) {
								$scheduleDate = $scheduleDateCopy;
								break;
							}
							$scheduleDateCopy->subDay();
						}
					}
				}
			}

			array_push($scheduleDates, $scheduleDate->format('Y-m-d'));
		}

		return $scheduleDates;
	}

	public static function getFirstRepayDateForMonthlyLoan($samityId, $disbursementDate, $productId = null)
	{
		$weekMap = [
			1 => Carbon::SATURDAY, // SA
			2 => Carbon::SUNDAY, // SU
			3 => Carbon::MONDAY, // MO
			4 => Carbon::TUESDAY, // TU
			5 => Carbon::WEDNESDAY, // WE
			6 => Carbon::THURSDAY, // TH
			7 => Carbon::FRIDAY  // FR
		];

		$disbursementDate = Carbon::parse($disbursementDate);

		// FIRST REPAY DATE SHOULD AFTER THE GRACE PERIOD

		// GRACE PERIOD WOULD BE TAKEN FROM THE PRODCT CONFIGURATION, NOW IT IS TAKEN AS 30 DAYS
		$gracePeriodInDays = 30;
		$firstRepayDate = $disbursementDate->copy()->addDays($gracePeriodInDays);

		if ($disbursementDate->copy()->endOfMonth()->format('Y-m-d') == $firstRepayDate->copy()->endOfMonth()->format('Y-m-d')) {
			$firstRepayDate = $disbursementDate->copy()->addMonthNoOverflow();
		}

		// IF FRACE PERIOD IS 30 DAYS, FIRST REPAY DATE SHOULD BE INTO NEXT MONTH
		if ($gracePeriodInDays == 30 && $disbursementDate->copy()->addMonthNoOverflow()->endOfMonth()->format('Y-m-d') < $firstRepayDate->copy()->endOfMonth()->format('Y-m-d')) {
			$firstRepayDate = $disbursementDate->copy()->addMonthNoOverflow();
		}

		$monthFirstDate = $firstRepayDate->copy()->startOfMonth();
		$monthEndDate = $firstRepayDate->copy()->endOfMonth();

		$samity = DB::table('mfn_samity')->where('id', $samityId)->select('id', 'samityDayId')->first();
		$samityDayChange = DB::table('mfn_samity_day_change')->where('samityId', $samityId)->where('effectiveDate', '>', $firstRepayDate->format('Y-m-d'))->orderBy('effectiveDate')->first();

		$samityDayId = $samityDayChange != null ? $samityDayChange->samityDayId : $samity->samityDayId;

		// FIRST REPAY DATE SHOULD BE BETWEEN $monthFirstDate AND $monthEndDate DATE AND IT SHOULD BE INTO SAMITY DAY.
		if ($firstRepayDate->dayOfWeek != $firstRepayDate->copy()->next($weekMap[$samityDayId])->dayOfWeek && $firstRepayDate->copy()->next($weekMap[$samityDayId])->lte($monthEndDate)) {

			$samityDayChange = DB::table('mfn_samity_day_change')->where('samityId', $samityId)->where('effectiveDate', '>', $firstRepayDate->copy()->next($weekMap[$samityDayId])->format('Y-m-d'))->orderBy('effectiveDate')->first();
			$samityDayId = $samityDayChange != null ? $samityDayChange->samityDayId : $samity->samityDayId;

			$firstRepayDate->next($weekMap[$samityDayId]);
		} elseif ($firstRepayDate->dayOfWeek != $firstRepayDate->copy()->next($weekMap[$samityDayId])->dayOfWeek) {
			$firstRepayDate->subDays(7);

			$samityDayChange = DB::table('mfn_samity_day_change')->where('samityId', $samityId)->where('effectiveDate', '>', $firstRepayDate->copy()->next($weekMap[$samityDayId])->format('Y-m-d'))->orderBy('effectiveDate')->first();
			$samityDayId = $samityDayChange != null ? $samityDayChange->samityDayId : $samity->samityDayId;

			$firstRepayDate->next($weekMap[$samityDayId]);
		}
		return $firstRepayDate->format('Y-m-d');
	}

	public static function storeLoanSchedule($loanId, $schedules)
	{
		$loan = DB::table('mfn_loan')->where('id', $loanId)->select('id', 'loanTypeId')->first();
		// STORE DATA TO SCHEDULE TABLE		
		DB::beginTransaction();

		try {
			// IF SCHEDULE OF THIS LOAN IS ALREAY EXITS INTO THE DATABASE, THEN UPDATE IT BUT IF THE NUMBER OF SCHEDULE DOES NOT MATCH WITH THE CURRENT INSTALLMENT NUMBER THEN FIRST DELETE THE EXISTING SCHEDULE AND THEN SAVE IT.
			$numberOfExistingSchedule = DB::table('mfn_loan_schedule')
				->where('softDel', 0)
				->where('loanIdFk', $loan->id)
				->count();
			if ($numberOfExistingSchedule > 0 && $numberOfExistingSchedule != count($schedules)) {
				DB::table('mfn_loan_schedule')->where('loanIdFk', $loan->id)->delete();
			}

			$isScheduleExits = 0;
			if ($numberOfExistingSchedule > 0 && $numberOfExistingSchedule == count($schedules)) {
				$isScheduleExits = 1;
			}


			if ($isScheduleExits == 1) {
				foreach ($schedules as $schedule) {
					DB::table('mfn_loan_schedule')
						->where('loanIdFk', $loan->id)
						->where('installmentSl', $schedule['installmentSl'])
						->update([
							'installmentAmount' => $schedule['installmentAmount'],
							'actualInstallmentAmount' => $schedule['actualInstallmentAmount'],
							'extraInstallmentAmount' => $schedule['extraInstallmentAmount'],
							'principalAmount' => $schedule['principalAmount'],
							'interestAmount' => $schedule['interestAmount'],
							'scheduleDate' => $schedule['scheduleDate']
						]);
				}
			} else {
				
				foreach ($schedules as $schedule) {
					$scheduleObj = new MfnLoanSchedule;
					$scheduleObj->loanIdFk 				= $loan->id;
					$scheduleObj->loanTypeId 				= $loan->loanTypeId;
					$scheduleObj->installmentSl 			= $schedule['installmentSl'];
					$scheduleObj->installmentAmount 		= $schedule['installmentAmount'];
					$scheduleObj->actualInstallmentAmount 	= $schedule['actualInstallmentAmount'];
					$scheduleObj->extraInstallmentAmount 	= $schedule['extraInstallmentAmount'];
					$scheduleObj->principalAmount 			= $schedule['principalAmount'];
					$scheduleObj->interestAmount 			= $schedule['interestAmount'];
					$scheduleObj->scheduleDate 				= $schedule['scheduleDate'];
					$scheduleObj->isCompleted 				= 0;
					$scheduleObj->isPartiallyPaid 			= 0;
					$scheduleObj->partiallyPaidAmount 		= 0;
					$scheduleObj->createdDate 				= Carbon::now();
					$scheduleObj->updatedDate 				= Carbon::now();
					$scheduleObj->status 					= 1;
					$scheduleObj->save();
				}
			}

			// UPDATE LAST INSTALLMENT DATE OF LOAN
			DB::table('mfn_loan')->where('id', $loan->id)->update(['lastInstallmentDate' => $schedules->max('scheduleDate')]);

			DB::commit();
			return ['error' => false, 'msg' => 'Loan Schedule Generated Successfully.'];
		} catch (\Exception $e) {
			DB::rollback();
			return ['error' => true, 'msg' => $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine()];
		}
	}

	public static function updateLoanScheduleStstus($loanId, $collectionAmount = null)
	{
		$loan = DB::table('mfn_loan')
			->where('id', $loanId)
			->select('id', 'totalRepayAmount', 'loanAmount')
			->first();

		if ($collectionAmount == null) {
			$collectionAmount = DB::table('mfn_loan_collection')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('amount');
			$collectionAmount += DB::table('mfn_opening_balance_loan')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('paidLoanAmountOB');
			$collectionAmount += DB::table('mfn_loan_waivers')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('amount');
			$collectionAmount += DB::table('mfn_loan_write_off')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('amount');
			$collectionAmount += DB::table('mfn_loan_rebates')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('amount');
		}

		$schedules = DB::table('mfn_loan_schedule')
			->where('softDel', 0)
			->where('loanIdFk', $loan->id)
			->orderBy('installmentSl')
			->select('id', 'installmentAmount')
			->get();

		foreach ($schedules as $schedule) {
			if ($collectionAmount >= $schedule->installmentAmount) {
				DB::table('mfn_loan_schedule')->where('id', $schedule->id)->update(['isCompleted' => 1, 'isPartiallyPaid' => 0, 'partiallyPaidAmount' => 0]);
			} elseif ($collectionAmount > 0) {
				DB::table('mfn_loan_schedule')->where('id', $schedule->id)->update(['isCompleted' => 0, 'isPartiallyPaid' => 1, 'partiallyPaidAmount' => $collectionAmount]);
			} else {
				DB::table('mfn_loan_schedule')->where('id', $schedule->id)->update(['isCompleted' => 0, 'isPartiallyPaid' => 0, 'partiallyPaidAmount' => 0]);
			}

			$collectionAmount -= $schedule->installmentAmount;
		}
	}

	/**
	 * Undocumented function
	 *
	 * @param [int] $loanId
	 * @param [collection object] $schedules
	 * @param [string] $updateFrom
	 * @return true/false
	 */
	public static function updateLoanScheduleDates($loanId, $updateFrom = null)
	{
		if($updateFrom == null){
			$updateFrom = '2019-09-01';
		}
		
		$isOnlyForDates = 1;
		$holidays = null;
		$schedules = self::makeLoanSchedule($loanId, $holidays , $isOnlyForDates);
		if (isset($schedules['success'])) {
			dd($schedules);
		}
		dd($loanId, $schedules, $updateFrom);
		if ($updateFrom == null) {
			foreach ($schedules as $schedule) {
				DB::table('mfn_loan_schedule')->where('softDel', 0)->where('loanIdFk', $loanId)->where('installmentSl', $schedule['installmentSl'])->update(['scheduleDate' => $schedule['scheduleDate']]);
			}
		}
		else{
			$updateFrom = Carbon::parse($updateFrom)->format('Y-m-d');
			$schedules = $schedules->where('scheduleDate', '>=', $updateFrom);
			foreach ($schedules as $schedule) {
				DB::table('mfn_loan_schedule')->where('softDel', 0)->where('scheduleDate', '>=', $updateFrom)->where('loanIdFk', $loanId)->where('installmentSl', $schedule['installmentSl'])->update(['scheduleDate' => $schedule['scheduleDate']]);
			}
		}

		return true;
	}
}
