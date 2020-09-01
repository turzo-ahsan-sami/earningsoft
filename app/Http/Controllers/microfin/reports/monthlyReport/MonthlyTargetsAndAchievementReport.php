<?php

namespace App\Http\Controllers\microfin\reports\monthlyReport;

use App\Http\Controllers\Controller;
use DB;
use App\Http\Requests;
use Response;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
use App\globalFunction\mfnHalfYearlyEmploymentInfo;

use App\Http\Controllers\microfin\MicroFin;

/**
 *
 */
class MonthlyTargetsAndAchievementReport extends Controller
{
  public function getLevel(){
    $LoanYear = array();
    $LoanYearArray = array();
    $UniqueLoanYear = array();
    $FundingOrganization = array();

    $BranchDatas = DB::table('gnr_branch')->get();

    $ProdCats = DB::table("mfn_loans_product_category")->get();

    $LoanDatas = DB::table('mfn_loan')
                   ->select(DB::raw('EXTRACT(YEAR FROM disbursementDate) as Year'))
                   ->get();
    $FundingOrganization = DB::table('mfn_funding_organization')
                  ->get();

    foreach ($LoanDatas as $key => $LoanData) {
      $LoanYear[] = $LoanData->Year;
    }

    $LoanYearArray = array($LoanYear);

    foreach ($LoanYearArray as $key => $LoanYearArrays) {
        $UniqueLoanYear = array_unique($LoanYearArrays);
    }

    $monthsOption = MicroFin::getMonthsOption();

    return view('microfin.reports.monthlyReportViews.monthlyTargetsAndAchievementReport.monthlyTargetsAndAchievementReportForm', compact('UniqueLoanYear', 'monthsOption'));

  }

  public function getLevelInfo(Request $request){
    $RequestedLevel = $request->id;

    $Areas = array();
    $AreaValues = array();
    $newarray = array();
    $AjaxReturn = array();
    $FinalAjaxReturn = array();
    $FinalAjax = array();

    $AreaName = '';
    $Counter = 0;
    $InnerCounter = 0;

    if ($RequestedLevel == 'Area') {
      $Areas = DB::table('gnr_area')
              ->get();

      foreach ($Areas as $key => $Area) {
        $AreaValues[$Area->name] = preg_split('/[\s,"]+/', $Area->branchId);
      }

      foreach ($AreaValues as $key1 => $AreaValue) {
        $newarray[$key1] = array_slice($AreaValue, 1, -1);
      }

      foreach ($newarray as $key => $newarray1) {
        foreach ($newarray1 as $key1 => $newarray2) {
          $AjaxReturn[$key][] = DB::table('gnr_branch')
                              ->select('name')
                              ->where('id', $newarray2)
                              ->get();
        }
      }
    }
    elseif ($RequestedLevel == 'Branch') {
      $Areas = DB::table('gnr_branch')
              ->get();
    }
    elseif ($RequestedLevel == 'Regions') {
      $AjaxReturn = DB::table('gnr_region')
              ->get();
    }
    elseif ($RequestedLevel == 'Zone') {
      $Areas = DB::table('gnr_zone')
              ->get();

      foreach ($Areas as $key => $Area) {
        $AreaValues[$Area->name] = preg_split('/[\s,"]+/', $Area->areaId);
      }

      foreach ($AreaValues as $key1 => $AreaValue) {
        $newarray[$key1] = array_slice($AreaValue, 1, -1);
      }

      foreach ($newarray as $key => $newarray1) {
        foreach ($newarray1 as $key1 => $newarray2) {
          $AjaxReturn[$key][] = DB::table('gnr_branch')
                              ->select('name')
                              ->where('id', $newarray2)
                              ->get();
        }
      }
    }

    // $newarray = array_slice($AreaValues, 1, -1);

    // dd($AreaName);

    return response()->json($Areas);

  }

  public function getReport(Request $request){
    $requestedLevel = $request->searchLevel;
    $requestedABZ = $request->searchABZ;
    $requestedYear = $request->Year;
    $requestedMonth = $request->searchMonth;

    $endDate = date("Y-m-t", strtotime('01-'.$requestedMonth.'-'.$requestedYear));
    $crateDate = date_create("01-$requestedMonth-$requestedYear");
    $startDate = date_format($crateDate,"Y-m-d");

    return view('microfin.reports.monthlyReportViews.monthlyTargetsAndAchievementReport.monthlyTargetsAndAchievementReportTable', compact('requestedMonth', 'requestedMonth', 'requestedYear'));

  }


}
