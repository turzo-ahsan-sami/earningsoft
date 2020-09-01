<?php

namespace App\Traits;
use Auth;
use DB;
use Carbon\Carbon;

trait GetSoftwareDate {

    // Get Microfinance Software Date
    public static function getMicrofinSoftwareDate(){
        self::getSoftwareDate();
    }

    //get Accounting SoftwareDate
    public static function getAccountingSoftwareDate(){
        $userBarnchId = Auth::user()->branchId;

        $softwareDate = DB::table('acc_day_end')->where('branchIdFk',$userBarnchId)->where('isDayEnd',0)->value('date');
        if ($softwareDate=='' || $softwareDate==null) {
            $softwareDate = DB::table('gnr_branch')->where('id',$userBarnchId)->value('aisStartDate');
            // $softwareDate = DB::table('acc_opening_balance')->where('branchId',$userBarnchId)->value('openingDate');
        }

        return $softwareDate;
    }

    public static function getAccountingSoftwareDateByBranchId($branchId)
    {
        $userBarnchId = $branchId;

        $softwareDate = DB::table('acc_day_end')->where('branchIdFk', $userBarnchId)->where('isDayEnd', 0)->value('date');
        if ($softwareDate == '' || $softwareDate == null) {
                $softwareDate = DB::table('gnr_branch')->where('id',$userBarnchId)->value('aisStartDate');
            // $softwareDate = DB::table('acc_opening_balance')->where('branchId', $userBarnchId)->value('openingDate');
        }

        return $softwareDate;
    }



    /*public static function getSoftwareDate(){
        $userBarnchId = Auth::user()->branchId;

        $softwareDate = DB::table('mfn_day_end')->where('branchIdFk',$userBarnchId)->where('isLocked',0)->value('date');
        if ($softwareDate=='' || $softwareDate==null) {
            $softwareDate = DB::table('gnr_branch')->where('id',$userBarnchId)->value('softwareStartDate');
        }

        return $softwareDate;
    }*/

    public static function getSoftwareDate(){
        $userBarnchId = Auth::user()->branchId;

        $softwareDate = DB::table('mfn_day_end')->where('branchIdFk',$userBarnchId)->where('isLocked',0)->value('date');

        if ($softwareDate=='' || $softwareDate==null) {

            $softwareDate = DB::table('gnr_branch')->where('id',$userBarnchId)->value('softwareStartDate');
            $softwareDate = Carbon::parse($softwareDate);

            $nextDay = '';
            $isNextDayHoliday = 1;
            $nextDay = $softwareDate->copy();

            while (self::isHoliday($softwareDate->format('Y-m-d'),$userBarnchId)==1) {
                $softwareDate->addDay();                    
            }
        }
        else{
            $softwareDate = Carbon::parse($softwareDate);                
        }
        $softwareDate = $softwareDate->format('Y-m-d');

        return $softwareDate;
    }

    public static function getSoftwareDateInFormat(){
        $userBarnchId = Auth::user()->branchId;

        $softwareDate = DB::table('mfn_day_end')->where('branchIdFk',$userBarnchId)->where('isLocked',0)->value('date');
        if ($softwareDate=='' || $softwareDate==null) {

            $softwareDate = DB::table('gnr_branch')->where('id',$userBarnchId)->value('softwareStartDate');
            $softwareDate = Carbon::parse($softwareDate);

            $nextDay = '';
            $isNextDayHoliday = 1;
            $nextDay = $softwareDate->copy();

            while (self::isHoliday($softwareDate->format('Y-m-d'),$userBarnchId)==1) {
                $softwareDate->addDay();                    
            }
        }
        else{
            $softwareDate = Carbon::parse($softwareDate);                
        }
        $softwareDate = $softwareDate->format('d-m-Y');

        return $softwareDate;
    }

    public static function isHoliday($date,$targetBranchId){
        $date = Carbon::parse($date)->format('Y-m-d');
        $isHoliday = 0;
        //get holidays
        $holiday = (int) DB::table('mfn_setting_holiday')->where('status',1)->where('date',$date)->value('id');
        if ($holiday>0) {
            $isHoliday = 1;
        }

        // get the organazation id and branch id of the loggedin user
        if ($isHoliday!=1) {
            $userBranchId = Auth::user()->branchId;
            $userOrgId = Auth::user()->company_id_fk;

            if($targetBranchId!=1){
                $userBranchId = $targetBranchId;
                $userOrgId = DB::table('gnr_branch')->where('id',$targetBranchId)->value('companyId');
            }

            $holiday = (int) DB::table('mfn_setting_orgBranchSamity_holiday')
                                    ->where('status',1)
                                    ->where(function ($query) use ($userBranchId,$userOrgId) {
                                        $query->where('ogrIdFk', '=', $userOrgId)
                                                ->orWhere('branchIdFk', '=', $userBranchId);
                                    })
                                    ->where('dateFrom','<=',$date)
                                    ->where('dateTo','>=',$date)
                                    ->value('id');
            if($holiday>0){
                $isHoliday = 1;
            }
        }

        return $isHoliday;
        
    }

    public static function getOpeningInformationActive() {

        $dayEndCheck = DB::table('mfn_day_end')->where('branchIdFk', Auth::user()->branchId)->where('isLocked', 1)->count();
        $branchStatus = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->where('branchOpeningDate', '<', DB::raw('softwareStartDate'))->count();

        return $openingInformationActive = ($dayEndCheck<1 && $branchStatus==1)?1:0;
    }

}
