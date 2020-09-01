<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;
use App\Http\Controllers\home\MfnBranchStatusController;
use App\Console\Commands\MfnDashboardReport;
use Session;
use DB;
use Auth;
use Carbon\Carbon;

class MfnHomeController extends Controller {

    public function index() {
        return view('homePages.mfnHomePages.viewMfnHome');
    }

    public function loadMfnTab1(){
        return view('homePages.mfnHomePages.loadMfnTab1');
    }

    public function loadMfnTab2(){

        $data = $this->loadingDivInfos();

        return view('homePages.mfnHomePages.loadMfnTab2', $data);
    }

    public function loadingDivInfos() {

        // date count
        $today = Carbon::now()->format('Y-m-d');

        // query from tables for general info
        $branchInfos = DB::table('gnr_branch')
                    ->select('id', 'name', DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"))
                    ->get();
        $totalSamity = DB::table('mfn_samity')->count('id');
        $totalBranch = $branchInfos->where('id', '!=', 1)->count('id');

        //  query from table for loan & saving info
        // branch status collection
        $branchStatusInfos = DB::table('mfn_dashboard_report')->get();

        $lastUpdateTime = $branchStatusInfos->max('updatedDate');
        $lastUpdateFormatedTime = Carbon::parse($lastUpdateTime)->format('d M, Y g:i A');

        // component wise loan
        $loanInfosByProduct = DB::table('mfn_componentwise_loans_info')->get();
        $components = DB::table('mfn_loans_product_category')->select('id', 'name')->get();

        foreach ($components as $key => $item) {
            $loanInfosByComponentArr[] = array(
                'componentName'     => $item->name,
                'totalDisbursement' => $loanInfosByProduct->where('componentId', $item->id)->sum('totalDisbursement'),
                'totalRecovery'     => $loanInfosByProduct->where('componentId', $item->id)->sum('totalRecovery'),
                'totalOutstanding'  => $loanInfosByProduct->where('componentId', $item->id)->sum('totalOutstanding')
            );
        }

        // echo '<pre>'; print_r($loanInfosByComponentArr); echo '</pre>';
        $data = array(
            'lastUpdateFormatedTime'    => $lastUpdateFormatedTime,
            'branchInfos'               => $branchInfos,
            'totalBranch'               => $totalBranch,
            'totalSamity'               => $totalSamity,
            'branchStatusInfos'         => $branchStatusInfos,
            'components'                => $components,
            'loanInfosByComponentArr'   => collect($loanInfosByComponentArr)->sortByDesc('totalOutstanding'),
        );

        return $data;
    }

    public function loadMfnTab3(){
        return view('homePages.mfnHomePages.loadMfnTab3');
    }

    public function loadMfnTab4(){

        $branchStatus = new MfnBranchStatusController;
        $updateDashboard = new MfnDashboardReport;
        // $updateDashboard->handle();
        // $array = $branchStatus->mfnAddAllBranchStatus();
        // $array = $branchStatus->mfnAddBranchStatus(4);
        // $branchStatus->mfnComponentWiseLoanInfo();
        // dd($array);

        $tableHeaderItemsArray = array(
            array('Sl#', '3%'),
            array('Branch Name', '14%'),
            array('Branch Opening Date', '9%'),
            array('Software Start date', '9%'),
            array('Branch Date', '9%'),
            array('Total Member', '7%'),
            array('Total Borrower', '7%'),
            array("Last Month's Due (Pr.)", '9%'),
            array("Today's Due (Pr.)", '9%'),
            array('Total Due (Pr.)', '9%'),
            array('Total Outstanding (Pr.)', '9%'),
            array('Lag', '6%')
        );

        $userBranchId = Auth::user()->branchId;

        // date count
        $today = Carbon::now()->format('Y-m-d');

        // branch status collection
        if($userBranchId == 1) {
            $branchStatusInfos = DB::table('mfn_dashboard_report')->get();
        }
        else {
            $branchStatusInfos = DB::table('mfn_dashboard_report')->where('branchIdFk', $userBranchId)->get();
        }
        // dd($branchStatusInfos);

        if($branchStatusInfos->count() > 0) {    // if status info found
            // branch collection
            $branchInfos = DB::table('gnr_branch')
                            ->select('id', 'name', 'branchCode', 'softwareStartDate', 'branchOpeningDate')
                            ->get();

            // day end collection
            $branchDayEnds = DB::table('mfn_day_end')->where('isLocked', 0)->select('date', 'branchIdFk')->get();

            foreach ($branchStatusInfos->sortBy('branchIdFk') as $key => $info){

                $branchInfo = $branchInfos->where('id', $info->branchIdFk)->first();
                if($branchInfo){
                    $branchName = $branchInfo->name;
                    $branchCode = $branchInfo->branchCode;
                    $softwareStartDate = $branchInfo->softwareStartDate;
                    $branchOpeningDate = $branchInfo->branchOpeningDate;
                }

                // branch date code
                $branchDayEnd = $branchDayEnds->where('branchIdFk', $info->branchIdFk)->first();

                if ($branchDayEnd){
                    $branchDate = $branchDayEnd->date;
                    // dd($branchDate);
                }
                else {
                    $branchDate = $softwareStartDate;
                }

                // lag calculation
                $branchDateCarbon = Carbon::parse($branchDate);
                $lag = Carbon::now()->diffInDays($branchDateCarbon);

                // member count
                $memberCount = $info->totalActiveMaleMember + $info->totalActiveFemaleMember;

                // loanee count
                $borrowerCount = $info->totalMaleLoanee + $info->totalFemaleLoanee;

                // today due
                $todayDue = $info->todayDueAmount;

                // last month due
                $lastMonthDue = $info->lastMonthDueAmount;

                // Total due
                $totalDue = $info->totalDueAmount;

                // total outstanding
                $totalOutstandning = $info->totalOutstanding;

                // collecting info in an array

                $branchInfoArr[] = array(
                    'branchName'           => $branchName,
                    'branchCode'           => $branchCode,
                    'softwareStartDate'    => $softwareStartDate,
                    'branchOpeningDate'    => $branchOpeningDate,
                    'branchDate'           => $branchDate,
                    'memberCount'          => $memberCount,
                    'borrowerCount'        => $borrowerCount,
                    'todayDue'             => $todayDue,
                    'lastMonthDue'         => $lastMonthDue,
                    'totalDue'             => $totalDue,
                    'totalOutstandning'    => $totalOutstandning,
                    'lag'                  => $lag
                );

            }

            $lastUpdateTime = $branchStatusInfos->max('updatedDate');
            $lastUpdateFormatedTime = Carbon::parse($lastUpdateTime)->format('d M, Y g:i A');

        }
        else {    // if status info not found
            $branchInfoArr = [];
        }
        // dd($branchInfoArr);

        return view('homePages.mfnHomePages.loadMfnTab4')
                ->with('tableHeaderItemsArray', $tableHeaderItemsArray)
                ->with('branchInfoArr', $branchInfoArr)
                ->with('lastUpdateFormatedTime', $lastUpdateFormatedTime);

    }
}
