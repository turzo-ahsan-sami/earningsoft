<?php

namespace App\Http\Controllers\microfin\reports\branchWiseSamityReportController;

use App\Http\Controllers\Controller;
use DB;
use App\Http\Requests;
use Response;
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


 // - Controller for Branch Wise Samity Report
 // - Created By Atiqul Haque
 // - Date:11/04/18

class BranchWiseSamityReportController extends Controller
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
    // dd($UniqueLoanYear);
    // return 'Hello';
    return view('microfin.reports.branchWiseSamityReportViews.BranchWiseSamityReportForm', compact('BranchDatas', 'FundingOrganization', 'UniqueLoanYear'));
  }

  public function getStatus(Request $request ){
    $SamityStatus = DB::table('mfn_samity')
                    ->select('status')
                    ->distinct()
                    ->get();

    return response()->json($SamityStatus);
  }

  public function getType(Request $request){
    $SamityType = DB::table('mfn_samity')
                    ->select('samityTypeId')
                    ->distinct()
                    ->get();

    return response()->json($SamityType);
  }

  public function getTable(Request $request){
    $RequestBranchID = $request->searchBranch;
    $RequestStatus = $request->Status;
    $RequestType = $request->Type;
    $RequestFromDate = $request->txtDate;
    $RequestToDate = $request->txtDate1;

    $FirstDateInfo = array();

    // $FirstDateInfo = DB::table('mfn_samity')
    //                // ->where('branchId', $RequestBranchID)
    //                ->where('status', $RequestStatus)
    //                ->where('samityTypeId', $RequestType)
    //                // ->where('openingDate', '>=', $RequestFromDate)
    //                ->where('openingDate', '<=', $RequestToDate)
    //                // ->limit(1)
    //                // ->get();
    //                ->orderBy('openingDate', 'asc')
    //                ->first();
    //                dd($FirstDateInfo);

    if ($RequestBranchID == 100) {
      if ($RequestFromDate !=null and $RequestToDate != null) {
        $SamityInfos = DB::table('mfn_samity')
                       ->where('status', $RequestStatus)
                       ->where('samityTypeId', $RequestType)
                       ->where('openingDate', '>=', $RequestFromDate)
                       ->where('openingDate', '<=', $RequestToDate)
                       ->get();

                       // dd($SamityInfos);

        $BranchInfos = DB::table('gnr_branch')
                       // ->where('id', $RequestBranchID)
                       ->get();
      }
      elseif ($RequestFromDate ==null and $RequestToDate != null) {
        $SamityInfos = DB::table('mfn_samity')
                       // ->where('branchId', $RequestBranchID)
                       ->where('status', $RequestStatus)
                       ->where('samityTypeId', $RequestType)
                       // ->where('openingDate', '>=', $RequestFromDate)
                       ->where('openingDate', '<=', $RequestToDate)
                       ->get();

                       // dd($SamityInfos);
       $FirstDateInfo = DB::table('mfn_samity')
                      // ->where('branchId', $RequestBranchID)
                      ->where('status', $RequestStatus)
                      ->where('samityTypeId', $RequestType)
                      // ->where('openingDate', '>=', $RequestFromDate)
                      ->where('openingDate', '<=', $RequestToDate)
                      // ->limit(1)
                      // ->get();
                      ->orderBy('openingDate', 'asc')
                      ->first();
                      // dd($FirstDateInfo);

        $BranchInfos = DB::table('gnr_branch')
                       // ->where('id', $RequestBranchID)
                       ->get();
      }
      elseif ($RequestFromDate !=null and $RequestToDate == null) {
        $RequestToDate = date("Y-m-d");
        $SamityInfos = DB::table('mfn_samity')
                       // ->where('branchId', $RequestBranchID)
                       ->where('status', $RequestStatus)
                       ->where('samityTypeId', $RequestType)
                       ->where('openingDate', '>=', $RequestFromDate)
                       ->where('openingDate', '<=', $RequestToDate)
                       ->get();

                       // dd($SamityInfos);

        $BranchInfos = DB::table('gnr_branch')
                       // ->where('id', $RequestBranchID)
                       ->get();
      }
      elseif ($RequestFromDate ==null and $RequestToDate == null) {
        // $RequestToDate = date("Y-m-d");
        $SamityInfos = DB::table('mfn_samity')
                       ->where('status', $RequestStatus)
                       ->where('samityTypeId', $RequestType)
                       // ->where('openingDate', '>=', $RequestFromDate)
                       // ->where('openingDate', '<=', $RequestToDate)
                       ->get();

                       // dd($SamityInfos);

        $BranchInfos = DB::table('gnr_branch')
                       // ->where('id', $RequestBranchID)
                       ->get();
      }
    }
    else {
      if ($RequestFromDate !=null and $RequestToDate != null) {
        $SamityInfos = DB::table('mfn_samity')
                       ->where('branchId', $RequestBranchID)
                       ->where('status', $RequestStatus)
                       ->where('samityTypeId', $RequestType)
                       ->where('openingDate', '>=', $RequestFromDate)
                       ->where('openingDate', '<=', $RequestToDate)
                       ->get();

                       // dd($SamityInfos);

        $BranchInfos = DB::table('gnr_branch')
                       ->where('id', $RequestBranchID)
                       ->get();
      }
      elseif ($RequestFromDate ==null and $RequestToDate != null) {
        $SamityInfos = DB::table('mfn_samity')
                       ->where('branchId', $RequestBranchID)
                       ->where('status', $RequestStatus)
                       ->where('samityTypeId', $RequestType)
                       // ->where('openingDate', '>=', $RequestFromDate)
                       ->where('openingDate', '<=', $RequestToDate)
                       ->get();

                       // dd($SamityInfos);
       $FirstDateInfo = DB::table('mfn_samity')
                      ->where('branchId', $RequestBranchID)
                      ->where('status', $RequestStatus)
                      ->where('samityTypeId', $RequestType)
                      // ->where('openingDate', '>=', $RequestFromDate)
                      ->where('openingDate', '<=', $RequestToDate)
                      // ->limit(1)
                      // ->get();
                      ->orderBy('openingDate', 'asc')
                      ->first();
                      // dd($FirstDateInfo);

        $BranchInfos = DB::table('gnr_branch')
                       ->where('id', $RequestBranchID)
                       ->get();
      }
      elseif ($RequestFromDate !=null and $RequestToDate == null) {
        $RequestToDate = date("Y-m-d");
        $SamityInfos = DB::table('mfn_samity')
                       ->where('branchId', $RequestBranchID)
                       ->where('status', $RequestStatus)
                       ->where('samityTypeId', $RequestType)
                       ->where('openingDate', '>=', $RequestFromDate)
                       ->where('openingDate', '<=', $RequestToDate)
                       ->get();

                       // dd($SamityInfos);

        $BranchInfos = DB::table('gnr_branch')
                       ->where('id', $RequestBranchID)
                       ->get();
      }
      elseif ($RequestFromDate ==null and $RequestToDate == null) {
        // $RequestToDate = date("Y-m-d");
        $SamityInfos = DB::table('mfn_samity')
                       ->where('branchId', $RequestBranchID)
                       ->where('status', $RequestStatus)
                       ->where('samityTypeId', $RequestType)
                       // ->where('openingDate', '>=', $RequestFromDate)
                       // ->where('openingDate', '<=', $RequestToDate)
                       ->get();

                       // dd($SamityInfos);

        $BranchInfos = DB::table('gnr_branch')
                       ->where('id', $RequestBranchID)
                       ->get();
      }
    }

    // dd($SamityInfos);
    // dd($BranchInfos);
    // dd($FirstDateInfo);

    return view('microfin.reports.branchWiseSamityReportViews.BranchWiseSamityReportTable', compact('SamityInfos', 'BranchInfos', 'RequestBranchID', 'RequestFromDate', 'RequestToDate', 'FirstDateInfo'));
  }


}
