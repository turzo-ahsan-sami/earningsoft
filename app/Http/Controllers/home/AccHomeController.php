<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;
use Session;

use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
// use App\Traits\GetSoftwareDate;

class AccHomeController extends Controller
{
    public function index(){

        ///////////////////////////////////
        // $routes = array('pos/posAddProduct',
        // 'pos/posViewProduct',
        // 'pos/posAddProductGroup',
        // 'pos/posViewProductGroup',
        // 'pos/posAddProductCategory',
        // 'pos/posViewProductCategory',
        // 'pos/posAddProductSubCategory',
        // 'pos/posViewProductSubCategory',
        // 'pos/posAddProductBrand',
        // 'pos/posViewProductBrand',
        // 'pos/posAddProductModel',
        // 'pos/posViewProductModel',
        // 'pos/posAddProductSize',
        // 'pos/posViewProductSize',
        // 'pos/posAddProductColor',
        // 'pos/posViewProductColor',
        // 'pos/posAddClient',
        // 'pos/posViewClient',
        // 'pos/posAddProductAssaign',
        // 'pos/posViewProductAssaing',
        // 'pos/addPosSalesRequiF',
        // 'pos/viewPosSalesList',
        // 'pos/sendToSuppler',
        // 'pos/salesInvoicePrint',
        // 'pos/addPosServiceRequiF',
        // 'pos/posViewList',
        // 'pos/serviceInvoicePrint',
        // 'pos/addPosCollection',
        // 'pos/viewPosCollectionList',
        // 'pos/posDayEndList',
        // 'pos/posMonthEndList',
        // 'pos/posSalesNServiceReport',
        // 'pos/posCollectionReport',
        // 'pos/posCollectionClientReport',
        // 'pos/posHrEmployeeList',
        // 'pos/posAddHrEmployee',
        // 'pos/hrDetailsEmployee');
        //
        // $availableRoutes = DB::table('gnr_route_operation')->whereIn('routeName', $routes)->pluck('routeName')->toArray();
        // $routesToAssign = array_diff($routes, $availableRoutes);
        // dd($routesToAssign);


    	return view('homePages.accHomePages.viewAccHome');
    }
    public function loadAccTab1(){
        $data = $this->loadingDivInfos();
        //dd($data);
    	return view('homePages.accHomePages.loadAccTab1', $data);
    }
    public function loadAccTab2(){

    	return view('homePages.accHomePages.loadAccTab2');
    }

    public function loadingDivInfos() {

        // date count
        $today = Carbon::now()->format('Y-m-d');

        // query from tables for general info
        $branchInfos = DB::table('gnr_branch')->select('id', 'name', DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"))->get();

        // branch status collection
        $branchStatusInfos = DB::table('acc_dashboard_report')->get();

        $lastUpdateTime = Carbon::parse($branchStatusInfos->max('updatedDate'))->format('d M, Y g:i A');

        // echo '<pre>'; print_r($loanInfosByComponentArr); echo '</pre>';
        $data = array(
            'lastUpdateFormatedTime'    => $lastUpdateTime,
            'branchInfos'               => $branchInfos,
            'branchStatusInfos'         => $branchStatusInfos,
        );
        // dd($data);
        return $data;
    }

    public function loadAccTab3(){
    	return view('homePages.accHomePages.loadAccTab3');
    }

    public function loadAccTab4(){

       // $branchStatus = new AccBranchStatusController;
        // $branchStatus->accAddBranchStatus(2);
        // $branchStatus->accAddAllBranchStatus();

        // $tableHeaderItemsArray = array(
        //     array('Sl#', '3%'),
        //     array('Branch Name', '14%'),
        //     array('Opening Date', '10%'),
        //     // array('Software Start date', '9%'),
        //     array('Branch Date', '10%'),
        //     array('PREV. MONTH CASH', '10%'),
        //     array('PREV. MONTH BANK', '10%'),
        //     array("CURRENT MONTH CASH", '10%'),
        //     array("CURRENT MONTH BANK", '10%'),
        //     array('TOTAL', '10%'),
        //     array('PROGRESS', '7%'),
        //     array('Lag', '5%')
        // );

        // $userBranchId = Auth::user()->branchId;

        // // date count
        // $today = Carbon::now()->format('Y-m-d');

        // // branch status collection
        // if($userBranchId == 1) {
        //     $branchStatusInfos = DB::table('acc_dashboard_report')->get();
        // }
        // else {
        //     $branchStatusInfos = DB::table('acc_dashboard_report')->where('branchIdFk', $userBranchId)->get();
        // }

        // // dd($branchStatusInfos);

        // if($branchStatusInfos->count() > 0) {    // if status info found
        //     // branch collection
        //     $branchInfos = DB::table('gnr_branch')
        //                     ->select('id', 'name', 'branchCode', 'branchOpeningDate', 'aisStartDate')
        //                     ->get();
        //     $branchesDayEnds = DB::table('acc_day_end')->where('isDayEnd', 0)->distinct('branchIdFk')->select('branchIdFk', 'date')->get();
        //     // dd($branchesDayEnds);

        //     // /->sortBy('branchIdFk')
        //     foreach ($branchStatusInfos->sortBy('branchIdFk') as $key => $info){

        //         $branchInfo = $branchInfos->where('id', $info->branchIdFk)->first();
        //         $branchDayEnd = $branchesDayEnds->where('branchIdFk', $info->branchIdFk)->first();
        //         $softwareStartDate = $branchInfo->aisStartDate;

        //         if ($branchDayEnd) {
        //             $branchDate = $branchDayEnd->date;
        //         }
        //         else {
        //             $branchDate = $softwareStartDate;
        //         }


        //         // progress - lag calculation
        //         $progress = Carbon::parse($branchDate)->diffInDays($softwareStartDate);
        //         $lag = Carbon::now()->diffInDays($branchDate);
        //         // dd($lag);

        //         $branchInfoArr[] = array(
        //             'branchName'           => $branchInfo->name,
        //             'branchCode'           => $branchInfo->branchCode,
        //             'branchOpeningDate'    => $branchInfo->branchOpeningDate,
        //             'branchDate'           => $branchDate,
        //             'today'                => Carbon::now()->format('Y-m-d'),
        //             'previousMonthCash'    => $info->previousMonthCash,
        //             'previousMonthBank'    => $info->previousMonthBank,
        //             'currentMonthCash'     => $info->currentMonthCash,
        //             'currentMonthBank'     => $info->currentMonthBank,
        //             'total'                => $info->currentCashAndBank,
        //             'progress'             => $progress,
        //             'lag'                  => $lag
        //         );

        //     }

        //     $lastUpdateTime = Carbon::parse($branchStatusInfos->max('updatedDate'))->format('d M, Y g:i A');
        //     // dd($lastUpdateTime);

        // }
        // else {    // if status info not found
        //     $branchInfoArr = [];
        // }

        // dd($branchInfoArr);
        // return view('homePages.accHomePages.loadAccTab4')
        //         ->with('tableHeaderItemsArray', $tableHeaderItemsArray)
        //         ->with('branchInfoArr', $branchInfoArr)
        //         ->with('lastUpdateFormatedTime', $lastUpdateTime);
    	return view('homePages.accHomePages.loadAccTab4');
    }

    public function homeTest(){
    	return view('homePages/view_acc_homeTest');
    }

    

}
