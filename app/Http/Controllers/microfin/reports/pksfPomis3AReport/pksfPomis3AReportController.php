<?php

namespace App\Http\Controllers\microfin\reports\pksfPomis3AReport;

use App\Http\Controllers\Controller;
use DB;
use App\Http\Requests;
use Response;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
use App\globalFunction\mfnHalfYearlyEmploymentInfo;
//include ('App/globalFunction/mfnHalfYearlyEmploymentInfo.php');
// use Illuminate\Http\Request;
// use App\Http\Requests;
// use Validator;
// use Response;
// use App\Traits\GetSoftwareDate;
// use Illuminate\Support\Facades\Input;
// use Illuminate\Support\Facades\Hash;
// use App\Http\Controllers\Controller;

/**
 *
 */
class pksfPomis3AReportController extends Controller
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

    return view('microfin.reports.pksfPomisReport.pksfPomis3AReportViews.pksfPomis3AReportForm', compact('UniqueLoanYear'));

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

  public function BranchQuery($RequestedName, $RequestedMonth, $RequestedYear, $RequestedOptions, $branchIdsHavingMonthEnd){
    $Date = '';

    $Branch = array();
    $DataSelf = array();
    $DataWages = array();

    switch ($RequestedMonth) {
    case "1-6":
        $Date = $RequestedYear.'-06-30';
        break;
    case "7-12":
        $Date = $RequestedYear.'-12-31';
        break;
    }

    // dd($Date);

    if ($RequestedOptions == 'LoanProduct') {
      $Branch[$RequestedName] = DB::table('mfn_half_yearly_employment_info')
                ->select('mfn_loans_product.name', 'mfn_loans_product.shortName', 'mfn_loans_product.id')
                ->join('mfn_loans_product', 'mfn_half_yearly_employment_info.productIdFk', '=', 'mfn_loans_product.id')
                ->where([['mfn_half_yearly_employment_info.branchIdFk', $branchIdsHavingMonthEnd[0]], ['mfn_half_yearly_employment_info.date', $Date]])
                ->GroupBy('mfn_loans_product.name')
                ->get();
    }
    elseif ($RequestedOptions == 'LoanProductCategory') {
      $Branch[$RequestedName] = DB::table('mfn_half_yearly_employment_info')
                ->select('mfn_loans_product_category.name', 'mfn_loans_product_category.shortName', 'mfn_loans_product_category.id')
                ->join('mfn_loans_product_category', 'mfn_half_yearly_employment_info.productCategoryIdFk', '=', 'mfn_loans_product_category.id')
                ->where([['mfn_half_yearly_employment_info.branchIdFk', $branchIdsHavingMonthEnd[0]], ['mfn_half_yearly_employment_info.date', $Date]])
                ->GroupBy('mfn_loans_product_category.name')
                ->get();
    }

    return $Branch;
  }

  public function BranchQueryAll($RequestedMonth, $RequestedYear, $RequestedOptions, $branchIdsHavingMonthEnd){
    $Date = '';

    $Branch = array();
    $DataSelf = array();
    $DataWages = array();

    switch ($RequestedMonth) {
    case "1-6":
        $Date = $RequestedYear.'-06-30';
        break;
    case "7-12":
        $Date = $RequestedYear.'-12-31';
        break;
    }

    // dd($Date);

    if ($RequestedOptions == 'LoanProduct') {
      $Branch[] = DB::table('mfn_half_yearly_employment_info')
                ->select('mfn_loans_product.name', 'mfn_loans_product.shortName', 'mfn_loans_product.id')
                ->join('mfn_loans_product', 'mfn_half_yearly_employment_info.productIdFk', '=', 'mfn_loans_product.id')
                ->where([['mfn_half_yearly_employment_info.date', $Date]])
                ->whereIn('mfn_half_yearly_employment_info.branchIdFk', $branchIdsHavingMonthEnd)
                ->GroupBy('mfn_loans_product.name')
                ->get();
    }
    elseif ($RequestedOptions == 'LoanProductCategory') {
      $Branch[] = DB::table('mfn_half_yearly_employment_info')
                ->select('mfn_loans_product_category.name', 'mfn_loans_product_category.shortName', 'mfn_loans_product_category.id')
                ->join('mfn_loans_product_category', 'mfn_half_yearly_employment_info.productCategoryIdFk', '=', 'mfn_loans_product_category.id')
                ->where([['mfn_half_yearly_employment_info.date', $Date]])
                ->whereIn('mfn_half_yearly_employment_info.branchIdFk', $branchIdsHavingMonthEnd)
                ->GroupBy('mfn_loans_product_category.name')
                ->get();
    }
    // dd($Branch);

    return $Branch;
  }


  public function AreaQuery($RequestedName, $RequestedMonth, $RequestedYear, $RequestedOptions, $branchIdsHavingMonthEnd){
    $Date = '';

    $Branch = array();
    $DataSelf = array();
    $DataWages = array();

    switch ($RequestedMonth) {
    case "1-6":
        $Date = $RequestedYear.'-06-30';
        break;
    case "7-12":
        $Date = $RequestedYear.'-12-31';
        break;
    }

    // dd($Date);

    if ($RequestedOptions == 'LoanProduct') {
      $Branch[$RequestedName] = DB::table('mfn_half_yearly_employment_info')
                ->select('mfn_loans_product.name', 'mfn_loans_product.shortName', 'mfn_loans_product.id')
                ->join('mfn_loans_product', 'mfn_half_yearly_employment_info.productIdFk', '=', 'mfn_loans_product.id')
                ->where([['mfn_half_yearly_employment_info.areaIdFk', $branchIdsHavingMonthEnd[0]], ['mfn_half_yearly_employment_info.date', $Date]])
                ->GroupBy('mfn_loans_product.name')
                ->get();
    }
    elseif ($RequestedOptions == 'LoanProductCategory') {
      $Branch[$RequestedName] = DB::table('mfn_half_yearly_employment_info')
                ->select('mfn_loans_product_category.name', 'mfn_loans_product_category.shortName', 'mfn_loans_product_category.id')
                ->join('mfn_loans_product_category', 'mfn_half_yearly_employment_info.productCategoryIdFk', '=', 'mfn_loans_product_category.id')
                ->where([['mfn_half_yearly_employment_info.areaIdFk', $branchIdsHavingMonthEnd[0]], ['mfn_half_yearly_employment_info.date', $Date]])
                ->GroupBy('mfn_loans_product_category.name')
                ->get();
    }

    return $Branch;
  }

  public function AreaQueryAll($RequestedMonth, $RequestedYear, $RequestedOptions, $branchIdsHavingMonthEnd){
    $Date = '';

    $Branch = array();
    $DataSelf = array();
    $DataWages = array();

    switch ($RequestedMonth) {
    case "1-6":
        $Date = $RequestedYear.'-06-30';
        break;
    case "7-12":
        $Date = $RequestedYear.'-12-31';
        break;
    }

    // dd($Date);

    if ($RequestedOptions == 'LoanProduct') {
      $Branch[] = DB::table('mfn_half_yearly_employment_info')
                ->select('mfn_loans_product.name', 'mfn_loans_product.shortName', 'mfn_loans_product.id')
                ->join('mfn_loans_product', 'mfn_half_yearly_employment_info.productIdFk', '=', 'mfn_loans_product.id')
                ->where([['mfn_half_yearly_employment_info.date', $Date]])
                ->whereIn('mfn_half_yearly_employment_info.branchIdFk', $branchIdsHavingMonthEnd)
                ->GroupBy('mfn_loans_product.name')
                ->get();
    }
    elseif ($RequestedOptions == 'LoanProductCategory') {
      $Branch[] = DB::table('mfn_half_yearly_employment_info')
                ->select('mfn_loans_product_category.name', 'mfn_loans_product_category.shortName', 'mfn_loans_product_category.id')
                ->join('mfn_loans_product_category', 'mfn_half_yearly_employment_info.productCategoryIdFk', '=', 'mfn_loans_product_category.id')
                ->where([['mfn_half_yearly_employment_info.date', $Date]])
                ->whereIn('mfn_half_yearly_employment_info.branchIdFk', $branchIdsHavingMonthEnd)
                ->GroupBy('mfn_loans_product_category.name')
                ->get();
    }

    // dd($Branch);

    return $Branch;
  }

  public function ZoneQuery($RequestedName, $RequestedMonth, $RequestedYear, $RequestedOptions, $branchIdsHavingMonthEnd){
    $Date = '';

    $Branch = array();
    $DataSelf = array();
    $DataWages = array();

    switch ($RequestedMonth) {
    case "1-6":
        $Date = $RequestedYear.'-06-30';
        break;
    case "7-12":
        $Date = $RequestedYear.'-12-31';
        break;
    }

    // dd($Date);

    if ($RequestedOptions == 'LoanProduct') {
      $Branch[$RequestedName] = DB::table('mfn_half_yearly_employment_info')
                ->select('mfn_loans_product.name', 'mfn_loans_product.shortName', 'mfn_loans_product.id')
                ->join('mfn_loans_product', 'mfn_half_yearly_employment_info.productIdFk', '=', 'mfn_loans_product.id')
                ->where([['mfn_half_yearly_employment_info.zoneIdFk', $branchIdsHavingMonthEnd[0]], ['mfn_half_yearly_employment_info.date', $Date]])
                ->GroupBy('mfn_loans_product.name')
                ->get();
    }
    elseif ($RequestedOptions == 'LoanProductCategory') {
      $Branch[$RequestedName] = DB::table('mfn_half_yearly_employment_info')
                ->select('mfn_loans_product_category.name', 'mfn_loans_product_category.shortName', 'mfn_loans_product_category.id')
                ->join('mfn_loans_product_category', 'mfn_half_yearly_employment_info.productCategoryIdFk', '=', 'mfn_loans_product_category.id')
                ->where([['mfn_half_yearly_employment_info.zoneIdFk', $branchIdsHavingMonthEnd[0]], ['mfn_half_yearly_employment_info.date', $Date]])
                ->GroupBy('mfn_loans_product_category.name')
                ->get();
    }

    return $Branch;
  }

  public function ZoneQueryAll($RequestedMonth, $RequestedYear, $RequestedOptions, $branchIdsHavingMonthEnd){
    $Date = '';

    $Branch = array();
    $DataSelf = array();
    $DataWages = array();

    switch ($RequestedMonth) {
    case "1-6":
        $Date = $RequestedYear.'-06-30';
        break;
    case "7-12":
        $Date = $RequestedYear.'-12-31';
        break;
    }

    // dd($Date);

    if ($RequestedOptions == 'LoanProduct') {
      $Branch[] = DB::table('mfn_half_yearly_employment_info')
                ->select('mfn_loans_product.name', 'mfn_loans_product.shortName', 'mfn_loans_product.id')
                ->join('mfn_loans_product', 'mfn_half_yearly_employment_info.productIdFk', '=', 'mfn_loans_product.id')
                ->where([['mfn_half_yearly_employment_info.date', $Date]])
                ->whereIn('mfn_half_yearly_employment_info.branchIdFk', $branchIdsHavingMonthEnd)
                ->GroupBy('mfn_loans_product.name')
                ->get();
    }
    elseif ($RequestedOptions == 'LoanProductCategory') {
      $Branch[] = DB::table('mfn_half_yearly_employment_info')
                ->select('mfn_loans_product_category.name', 'mfn_loans_product_category.shortName', 'mfn_loans_product_category.id')
                ->join('mfn_loans_product_category', 'mfn_half_yearly_employment_info.productCategoryIdFk', '=', 'mfn_loans_product_category.id')
                ->where([['mfn_half_yearly_employment_info.date', $Date]])
                ->whereIn('mfn_half_yearly_employment_info.branchIdFk', $branchIdsHavingMonthEnd)
                ->GroupBy('mfn_loans_product_category.name')
                ->get();
    }

    return $Branch;
  }

  public function getReport(Request $request){
    $RequestedLevel = $request->searchLevel;
    $RequestedName = $request->searchABZ;
    $RequestedMonth = $request->searchMonth;
    $RequestedYear = $request->Year;
    $RequestedOptions = $request->loanOptions;
    $Date = '';
    $startDate = '';

    switch ($RequestedMonth) {
    case "1-6":
        $startDate = $RequestedYear.'-01-01';
        $Date = $RequestedYear.'-06-30';
        break;
    case "7-12":
        $startDate = $RequestedYear.'-07-01';
        $Date = $RequestedYear.'-12-31';
        break;
    }

    if ($RequestedName == 'All') {
      $branchIdsHavingMonthEnd = DB::table('mfn_month_end')
        ->where('date',$Date)
        ->pluck('branchIdFk')
        ->toArray();

      $monthEndPendingBranches = DB::table('gnr_branch')
        ->whereNotIn('id',$branchIdsHavingMonthEnd)
        ->orderBy('branchCode')
        ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"))
        ->pluck('nameWithCode')
        ->all();
    }
    else {
      $branchIdsHavingMonthEnd = DB::table('mfn_month_end')
        ->where([['date',$Date], ['branchIdFk', $RequestedName]])
        ->pluck('branchIdFk')
        ->toArray();

      if (sizeof($branchIdsHavingMonthEnd) == 0) {
        $monthEndPendingBranches = DB::table('gnr_branch')
          ->where('id',$RequestedName)
          ->orderBy('branchCode')
          ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"))
          ->pluck('nameWithCode')
          ->all();
      }
      else {
        $monthEndPendingBranches = array();
      }
    }

    // dd($monthEndPendingBranches);

    $BranchInfos = array();
    $OpeningSelf = array();
    $OpeningWages = array();

    if (sizeof($branchIdsHavingMonthEnd) > 0) {
      if ($RequestedLevel == 'Branch') {
        if ($RequestedName == 'All') {
          $BranchInfos = $this->BranchQueryAll($RequestedMonth, $RequestedYear, $RequestedOptions, $branchIdsHavingMonthEnd);
        }
        else {
          $BranchInfos = $this->BranchQuery($RequestedName, $RequestedMonth, $RequestedYear, $RequestedOptions, $branchIdsHavingMonthEnd);
        }
      }
      elseif ($RequestedLevel == 'Area') {
        if ($RequestedName == 'All') {
          $BranchInfos = $this->AreaQueryAll($RequestedMonth, $RequestedYear, $RequestedOptions, $branchIdsHavingMonthEnd);
        }
        else {
          $BranchInfos = $this->AreaQuery($RequestedName, $RequestedMonth, $RequestedYear, $RequestedOptions, $branchIdsHavingMonthEnd);
        }

      }
      elseif ($RequestedLevel == 'Zone') {
        if ($RequestedName == 'All') {
          $BranchInfos = $this->ZoneQueryAll($RequestedMonth, $RequestedYear, $RequestedOptions, $branchIdsHavingMonthEnd);
        }
        else {
          $BranchInfos = $this->ZoneQuery($RequestedName, $RequestedMonth, $RequestedYear, $RequestedOptions, $branchIdsHavingMonthEnd);
        }

      }
    }
    
    // dd($BranchInfos, $Date, $startDate, $RequestedLevel, $RequestedName, $RequestedOptions);

    return view('microfin.reports.pksfPomisReport.pksfPomis3AReportViews.pksfPomis3AReport', compact('BranchInfos', 'OpeningSelf', 'OpeningWages', 'Date',
    'startDate', 'RequestedOptions', 'RequestedName', 'RequestedLevel', 'RequestedMonth', 'RequestedYear', 'monthEndPendingBranches', 'branchIdsHavingMonthEnd'));
  }


}
