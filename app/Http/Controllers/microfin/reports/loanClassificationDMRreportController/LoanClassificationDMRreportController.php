<?php

namespace App\Http\Controllers\microfin\reports\loanClassificationDMRreportController;

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

/**
 *  Controller for Loan Classification & DMR Report
 *  -Created By Atiqul Haque
 *  -Date:19/04/18
 **/
class LoanClassificationDMRreportController extends Controller
{
  public function getBranchName(){
    $LoanYear = array();
    $LoanYearArray = array();
    $UniqueLoanYear = array();

    $BranchDatas = DB::table('gnr_branch')->get();

    $LoanDatas = DB::table('mfn_loan')
                   ->select(DB::raw('EXTRACT(YEAR FROM disbursementDate) as Year'))
                   ->get();

    foreach ($LoanDatas as $key => $LoanData) {
      $LoanYear[] = $LoanData->Year;
    }

    $LoanYearArray = array($LoanYear);

    foreach ($LoanYearArray as $key => $LoanYearArrays) {
        $UniqueLoanYear = array_unique($LoanYearArrays);
    }

    return view('microfin.reports.loanClassificationDMRreportView.LoanClassificationDMRreportForm', compact('BranchDatas', 'UniqueLoanYear'));
  }

  public function getReportTableData(Request $request){
    $RequestBranch = $request->searchBranch;
    $RequestYear = $request->Year;
    $RequestMonth = $request->Month;

    $BranchInfos = array();
    // $ComponentInfos = array();

    $ComponentInfos = DB::table('mfn_loans_product')
                      ->get();

    if ($RequestBranch != 'all') {                //If Requested Branch is not selected as all
      $BranchInfos = DB::table('gnr_branch')
                      ->where('id', $RequestBranch)
                      ->get();
    }
    elseif ($RequestBranch == 'all') {            //If Requested Branch is selected as all
      $BranchInfos = DB::table('gnr_branch')
                      ->get();
    }

    // $ComponentInfos = DB::table('mfn_loan')
    //               ->select('productIdFk')
    //               ->distinct()
    //               ->get();

    // dd($ComponentInfos);

    return view('microfin.reports.loanClassificationDMRreportView.LoanClassificationDMRreportTable', compact('RequestBranch', 'RequestYear', 'RequestMonth', 'BranchInfos', 'ComponentInfos'));

  }

}
