<?php

namespace App\Http\Controllers\microfin\reports\agrosorReport;

use App\Http\Controllers\Controller;
use DB;
use App\Http\Requests;
use Response;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;

use App\Http\Controllers\microfin\MicroFin;

//
// use Illuminate\Http\Request;
// use App\Http\Requests;
// use Validator;
// use Response;
// use App\Traits\GetSoftwareDate;
// use Illuminate\Support\Facades\Input;
// use Illuminate\Support\Facades\Hash;
// use App\Http\Controllers\Controller;


 // - Controller for Branch Wise Samity Report
 // - Created By Atiqul Haque
 // - Date:11/04/18

class HalfYearlyPurposeWiseReportOfAgroshorActivities extends Controller
{
  public function getBranchName(){
    $LoanYear = array();
    $LoanYearArray = array();
    $UniqueLoanYear = array();

    $BranchDatas = DB::table('gnr_branch')->get();
    $FundingOrganization = DB::table('mfn_funding_organization')->get();
    $LoanDatas = DB::table('mfn_loan')
                   ->select(DB::raw('EXTRACT(YEAR FROM disbursementDate) as Year'))
                   ->get();

    foreach ($LoanDatas as $key => $LoanData) {
      $LoanYear[] = $LoanData->Year;
    }
    $LoanYearArray = array($LoanYear);

    foreach ($LoanYearArray as $key => $LoanYearArrays) {
      // foreach ($LoanYearArrays as $key => $LoanYearArrayss) {
        $UniqueLoanYear = array_unique($LoanYearArrays);
      // }
    }

    $monthsOption = MicroFin::getMonthsOption();

    $loanSubPurposeId = DB::table('mfn_loan')
      ->select('loanSubPurposeIdFk')
      ->where([['softDel', '=', 0]])
      ->groupBy('loanSubPurposeIdFk')
      ->pluck('loanSubPurposeIdFk')
      ->toArray();

    $loanPurposeId = DB::table('mfn_loans_sub_purpose')
      ->select('purposeIdFK', 'name', 'id')
      ->whereIn('id', $loanSubPurposeId)
      ->get();

    $loanPurposeIdArray = $loanPurposeId->pluck('purposeIdFK')->toArray();

    $loanPurposeCategory = DB::table('mfn_loans_purpose')
      ->select('purposeCategoryIdFK', 'name', 'id')
      ->whereIn('id', $loanPurposeIdArray)
      ->get();

    $loanPurposeCategoryArray = $loanPurposeCategory->pluck('purposeCategoryIdFK')->toArray();

    $loanPurposeCategoryInfos = DB::table('mfn_loans_purpose_category')
      ->select('id', 'name')
      ->whereIn('id', $loanPurposeCategoryArray)
      ->get();

    return view('microfin.reports.agrosorReportsView.halfYearlyPurpos WiseReportOfAgroshorActivities.halfYearlyPurposeWiseReportOfAgroshorActivitiesForm', compact('BranchDatas', 'FundingOrganization', 'UniqueLoanYear',
     'monthsOption', 'loanPurposeCategoryInfos'));
  }

  // METHOD'S AND QUERY FOR CALCULATION........................................................................................
  // START OF THE BRANCH INFOS.................................................................................................
  public function branchQuery($requestedBranch){
    $branchInfos = array();

    if ($requestedBranch == 'All') {
      $branchInfos = DB::table('gnr_branch')
        ->select('id', 'branchCode', 'name', 'address')
        ->get();
    }
    else {
      $branchInfos = DB::table('gnr_branch')
        ->select('id', 'branchCode', 'name', 'address')
        ->where('id', $requestedBranch)
        ->get();
    }

    return $branchInfos;
  }
  // END OF THE BRANCH INFOS.................................................................................................

  // START OF THE PURPOSE CATEGORY..............................................................................
  public function purposeCategory($requestedBranch, $startDate, $endDate) {
    $loanSubPurposeId = DB::table('mfn_loan')
      ->select('loanSubPurposeIdFk')
      ->where('disbursementDate', '<=', $endDate)
      ->groupBy('loanSubPurposeIdFk')
      ->pluck('loanSubPurposeIdFk')
      ->toArray();

    $loanPurposeId = DB::table('mfn_loans_sub_purpose')
      ->select('purposeIdFK', 'name', 'id')
      ->whereIn('id', $loanSubPurposeId)
      ->get();

    $loanPurposeIdArray = $loanPurposeId->pluck('purposeIdFK')->toArray();

    $loanPurposeCategory = DB::table('mfn_loans_purpose')
      ->select('purposeCategoryIdFK', 'name', 'id')
      ->whereIn('id', $loanPurposeIdArray)
      ->get();

    $loanPurposeCategoryArray = $loanPurposeCategory->pluck('purposeCategoryIdFK')->toArray();

    $loanPurposeCategoryInfos = DB::table('mfn_loans_purpose_category')
      ->select('id', 'name')
      ->whereIn('id', $loanPurposeCategoryArray)
      ->get();

      // dd($loanPurposeCategoryInfos);

    return $loanSubPurposeId;
  }
  // END OF THE PURPOSE CATEGORY................................................................................

  // START OF THE LOAN SUB PURPOSE...........................................................................................
  public function loanSubPurposeId($requestedPuposeCategory) {
    $loanSubPurposeId = array();

    if ($requestedPuposeCategory == 'All') {
      $loanSubPurposeId = DB::table('mfn_loans_sub_purpose')
        ->select('mfn_loans_sub_purpose.id', 'mfn_loans_purpose.name')
        ->join('mfn_loans_purpose', 'mfn_loans_sub_purpose.purposeIdFK', '=', 'mfn_loans_purpose.id')
        ->get();
    }
    else {
      $loanSubPurposeId = DB::table('mfn_loans_sub_purpose')
        ->select('mfn_loans_sub_purpose.id', 'mfn_loans_purpose.name')
        ->join('mfn_loans_purpose', 'mfn_loans_sub_purpose.purposeIdFK', '=', 'mfn_loans_purpose.id')
        ->where('mfn_loans_purpose.purposeCategoryIdFK', $requestedPuposeCategory)
        ->get();
    }

    return $loanSubPurposeId;
  }
  // END OF THE LOAN SUB PURPOSE.............................................................................................

  // START OF THE CUMMULATIVE LOAN DISBURSEMENT..............................................................................
  public function cummulativeLoanDisbursement ($requestedBranch, $startDate, $endDate, $purposeCategory, $requestedType) {
    $cummulativeLoanDisbursement = array();

    if ($requestedBranch == 'All') {
      if ($requestedType == 'All') {
        $cummulativeLoanDisbursement = DB::table('mfn_loan')
          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
          ->where([['mfn_loan.disbursementDate', '<=', $endDate], ['mfn_loans_product.productCategoryId', 2]])
          ->whereIn('mfn_loan.loanSubPurposeIdFk', $purposeCategory)
          ->sum('mfn_loan.totalRepayAmount');
      }
      else {
        $cummulativeLoanDisbursement = DB::table('mfn_loan')
          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
          ->where([['mfn_loan.disbursementDate', '<=', $endDate], ['mfn_loans_product.productCategoryId', 2]])
          ->whereIn('mfn_loan.loanSubPurposeIdFk', $purposeCategory)
          ->sum('mfn_loan.loanAmount');
      }
    }
    else {
      if ($requestedType == 'All') {
        $cummulativeLoanDisbursement = DB::table('mfn_loan')
          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
          ->where([['mfn_loan.disbursementDate', '<=', $endDate], ['mfn_loan.branchIdFk', $requestedBranch], ['mfn_loans_product.productCategoryId', 2]])
          ->whereIn('mfn_loan.loanSubPurposeIdFk', $purposeCategory)
          ->sum('mfn_loan.totalRepayAmount');
      }
      else {
        $cummulativeLoanDisbursement = DB::table('mfn_loan')
          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
          ->where([['mfn_loan.disbursementDate', '<=', $endDate], ['mfn_loan.branchIdFk', $requestedBranch], ['mfn_loans_product.productCategoryId', 2]])
          ->whereIn('mfn_loan.loanSubPurposeIdFk', $purposeCategory)
          ->sum('mfn_loan.loanAmount');
      }
    }

    return $cummulativeLoanDisbursement;
  }
  // END OF THE CUMMULATIVE LOAN DISBURSEMENT................................................................................


  // MAIN(getReport) METHOD TO CALL............................................................................................
  public function getReport(Request $request){
    // REQUESTED DATA VARIABLES................................................................................................
    $requestedBranch = $request->searchBranch;
    $requestedYear = $request->searchYear;
    $requestedmonth = $request->searchMonth;
    $requestedType = $request->searchProCat;
    $requestedPuposeCategory = $request->searchPurCat;

    // DATE CREATION............................................................................................................
    $endDate = date("Y-m-t", strtotime('01-'.$requestedmonth.'-'.$requestedYear));
    $crateDate = date_create("01-$requestedmonth-$requestedYear");
    $startDate = date_format($crateDate,"Y-m-d");

    // METHODS TO CALL..........................................................................................................
    $branchInfos = $this->branchQuery($requestedBranch);

    $purposeCategory = $this->purposeCategory($requestedBranch, $startDate, $endDate);

    $loanSubPurposeId = $this->loanSubPurposeId($requestedPuposeCategory);

    $cummulativeLoanDisbursement = $this->cummulativeLoanDisbursement ($requestedBranch, $startDate, $endDate, $purposeCategory, $requestedType);

    // dd($loanSubPurposeId);

    return view('microfin.reports.agrosorReportsView.halfYearlyPurpos WiseReportOfAgroshorActivities.halfYearlyPurposeWiseReportOfAgroshorActivitiesReport', compact('branchInfos', 'requestedBranch',
    'startDate', 'endDate', 'loanSubPurposeId', 'requestedType'));
  }


}
