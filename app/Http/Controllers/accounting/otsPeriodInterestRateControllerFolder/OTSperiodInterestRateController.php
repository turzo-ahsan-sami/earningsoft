<?php

namespace App\Http\Controllers\accounting\otsPeriodInterestRateControllerFolder;

use App\Http\Controllers\Controller;
use DB;
use App\Http\Requests;
use Response;
use App\Http\Controllers\gnr\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
//
// use Illuminate\Http\Request;
// use App\Http\Requests;
// use Validator;
// use Response;
// use App\Traits\GetSoftwareDate;
// use Illuminate\Support\Facades\Input;
// use Illuminate\Support\Facades\Hash;
// use App\Http\Controllers\Controller;

 // - Controller for OTS Period Add Form
 // - Created By Atiqul Haque
 // - Date:26/04/18

class OTSperiodInterestRateController extends Controller
{
  public function getOTSperiodInterestHistoryTable(){

    // $OTSperiodTable = DB::select("SELECT * FROM `acc_ots_period_inetrest_history`");
    $OTSperiodTable = DB::table('acc_ots_period_inetrest_history')->orderBy('id', 'desc')->paginate(10);
    $OTSperiodName = DB::select("SELECT * FROM `acc_ots_period`");

                  // dd($OTSperiodTable);

    return view('accounting.otsPeriodInterestRate.OTSperiodInterestListView', compact('OTSperiodTable','OTSperiodName'));
  }

  public function getOTSperiodInterestHistoryForm(){
    $Success = 'False';
    $OTSperiod = DB::table('acc_ots_period')
    ->get();

    return view('accounting.otsPeriodInterestRate.OTSperiodInterestListAddForm', compact('OTSperiod', 'Success'));
  }

  public function getOTSperiodInterestHistoryAjaxResponse(Request $request){
    $PeriodId = $request->id;
    $AA = 5;
    $MaxDate = '';

    $MaxDateFrom = DB::select("SELECT MAX(dateFrom) AS maxDate  FROM `acc_ots_period_inetrest_history` WHERE otsPeriodIdFk='$PeriodId' AND status=1");

    $MaxOpeningDate = DB::select("SELECT MAX(openingDate) AS maxDate  FROM `acc_ots_account` WHERE periodId_fk='$PeriodId' AND status=1");

    foreach ($MaxDateFrom as $key => $MaxDateFroms) {
      $MaxDate1 = $MaxDateFroms->maxDate;
    }

    foreach ($MaxOpeningDate as $key => $MaxOpeningDates) {
      $MaxDate2 = $MaxOpeningDates->maxDate;
    }

    if ($MaxDate1 > $MaxDate2) {
      $MaxDate = date("Y-m-d", strtotime("$MaxDate1 +1 day"));
      // $MaxDate = $MaxDate1;
      return response()->json($MaxDate);
    }
    elseif ($MaxDate1 < $MaxDate) {
      $MaxDate = date("Y-m-d", strtotime("$MaxDate2 +1 day"));
      // $MaxDate = $MaxDate2;
      return response()->json($MaxDate);
    }
    else {
      $MaxDate = date("Y-m-d", strtotime("$MaxDate2 +1 day"));
      // $MaxDate = $MaxDate2;
      return response()->json($MaxDate);
    }

  }

  public function getOTSperiodInterestHistorySubmit(Request $request){
    $PeriodId = $request->periodName;
    $InterestRate = $request->interestRate;
    $DateGiven = (string) $request->txtDate1;
    $Date = date_create($DateGiven);
    $DateTo = date_format($Date, 'Y-m-d');


    $EndDate = date('Y-m-d', strtotime('-1 day', strtotime($request->txtDate1)));

    $MaxDateFinds = DB::select("SELECT MAX(dateFrom) AS maxDate FROM `acc_ots_period_inetrest_history` WHERE otsPeriodIdFk='$PeriodId'");

    foreach ($MaxDateFinds as $key => $MaxDateFind) {
      $MaximumDate = $MaxDateFind->maxDate;
    }

    $UpdateDatabaseInfo = DB::update("UPDATE `acc_ots_period_inetrest_history` SET `dateTo`='$EndDate',`status`=0 WHERE dateFrom='$MaximumDate'");

    $UpdateAccAcountInfo = DB::update("UPDATE `acc_ots_period` SET `interestRate`='$InterestRate' WHERE id='$PeriodId'");

    // dd($Date);

    $SuccessQuery = DB::insert("INSERT INTO `acc_ots_period_inetrest_history`(`id`, `otsPeriodIdFk`, `interestRate`, `dateFrom`, `dateTo`, `status`) VALUES (null,'$PeriodId','$InterestRate','$DateTo','',1)");

    $OTSperiod = DB::table('acc_ots_period')
    ->get();
    $logArray = array(
      'moduleId'  => 4,
      'controllerName'  => 'OTSperiodInterestRateController',
      'tableName'  => 'acc_ots_period_inetrest_history',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('acc_ots_period_inetrest_history')->max('id')]
    );
    Service::createLog($logArray);

                  // dd($SuccessQuery);

    if ($SuccessQuery == true) {
      // $Success = 'True';
      $notification = array(
       'message' => 'You successfully inserted the information!',
       'alert-type' => 'success'
     );
    }
    else {
      $notification = array(
       'message' => 'Please full fill the information properly!',
       'alert-type' => 'error'
     );
    }
    // dd($Success);


    // return view('accounting.otsPeriodInterestRate.OTSperiodInterestListAddForm', compact('OTSperiod', 'Success'));
    return redirect()->back()->with($notification);
  }

}
