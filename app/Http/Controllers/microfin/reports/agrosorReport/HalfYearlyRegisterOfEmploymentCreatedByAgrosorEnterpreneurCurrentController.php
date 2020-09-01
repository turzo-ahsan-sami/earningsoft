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

class HalfYearlyRegisterOfEmploymentCreatedByAgrosorEnterpreneurCurrentController extends Controller
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

    return view('microfin.reports.agrosorReportsView.halfYearlyRegisterOfEmploymentCreatedByAgrosorEnterpreneurCurrent.halfYearlyRegisterOfEmploymentCreatedByAgrosorEnterpreneurCurrentForm', compact('BranchDatas', 'FundingOrganization', 'UniqueLoanYear',
     'monthsOption'));
  }

  public function getSamity(Request $request){
    $BranchId = $request->id;

    $SamityName = array();

    if ($BranchId != 'All') {
      $SamityName = DB::table('mfn_samity')
                      ->where('branchId', $BranchId)
                      ->get();
    }
    elseif ($BranchId == 'All') {
      $SamityName = DB::table('mfn_samity')->get();
    }

    return response()->json($SamityName);

  }

  // METHOD'S AND QUERY FOR CALCULATION........................................................................................

  // MAIN(getReport) METHOD TO CALL............................................................................................
  public function getReport(Request $request){
    // REQUESTED DATA VARIABLES................................................................................................
    $requestedBranch = $request->searchBranch;
    $requestedSamity = $request->Samity;
    $requestedYear = $request->searchYear;
    $requestedmonth = $request->searchPeriod;
    $requestedType = $request->searchProCat;

    // DECLARED VARIABLE.......................................................................................................
    $Date = '';
    $startDate = '';
    $previousDate = '';

    // DATE CREATION...........................................................................................................
    switch ($requestedmonth) {
    case "1-6":
        $Date = $requestedYear.'-06-30';
        $startDate = $requestedYear.'-01-01';
        $_previousDate = date("Y-m-d", strtotime("-1 months", strtotime($startDate)));
        $previousDate = date("Y-m-t", strtotime($_previousDate));
        break;
    case "7-12":
        $Date = $requestedYear.'-12-31';
        $startDate = $requestedYear.'-07-01';
        $_previousDate = date("Y-m-d", strtotime("-1 months", strtotime($startDate)));
        $previousDate = date("Y-m-t", strtotime($_previousDate));
        break;
    }


    return view('microfin.reports.agrosorReportsView.halfYearlyRegisterOfEmploymentCreatedByAgrosorEnterpreneurCurrent.halfYearlyRegisterOfEmploymentCreatedByAgrosorEnterpreneurCurrentReport', compact('Date', 'requestedBranch', 'previousDate', 'startDate', 'requestedSamity'));
  }


}
