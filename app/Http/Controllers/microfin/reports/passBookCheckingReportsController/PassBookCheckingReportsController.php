<?php

namespace App\Http\Controllers\microfin\reports\passBookCheckingReportsController;

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
 *
 */
class PassBookCheckingReportsController extends Controller
{
  public function getBranchName(){
    $LoanYear = array();
    $LoanYearArray = array();
    $UniqueLoanYear = array();

    $BranchDatas = DB::table('gnr_branch')->get();

    $ProdCats = DB::table("mfn_loans_product_category")->get();

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

    return view('microfin.reports.passBookCheckingReports.PassBookCheckingReportsForm', compact('BranchDatas', 'ProdCats', 'UniqueLoanYear'));

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

  // Main method for calculation...........
  public function getReport(Request $request){
    // Requested data variables.....
    $requestedBranch = $request->searchBranch;
    $requestedSamity = $request->searchSamity;
    $requestedMember = $request->searchMember;

    // Declared array...................
    $CategoryInfos = array();
    $CategoryName = array();
    $MemberInfos = array();
    $BranchInfos = array();
    $SamityInfos = array();

    // Query calculation...............
    if ($requestedMember == 'All') {

      $CategoryInfos = DB::table('manual_pass_book_balance')
        ->select('mfn_loans_product.productCategoryId')
        ->join('mfn_loans_product', 'manual_pass_book_balance.productIdFk', '=', 'mfn_loans_product.id')
        ->where([['manual_pass_book_balance.branchIdFk', $requestedBranch], ['manual_pass_book_balance.samityIdFk', $requestedSamity]])
        ->groupBy('mfn_loans_product.productCategoryId')
        ->get();

    }
    else {

      $CategoryInfos = DB::table('manual_pass_book_balance')
        ->select('mfn_loans_product.productCategoryId')
        ->join('mfn_loans_product', 'manual_pass_book_balance.productIdFk', '=', 'mfn_loans_product.id')
        ->where([['manual_pass_book_balance.branchIdFk', $requestedBranch], ['manual_pass_book_balance.samityIdFk', $requestedSamity], ['manual_pass_book_balance.loanCode', '!=', '']])
        ->groupBy('mfn_loans_product.productCategoryId')
        ->get();

    }

    foreach ($CategoryInfos as $key => $CategoryInfo) {
      $CategoryName[$CategoryInfo->productCategoryId] = DB::table('mfn_loans_product_category')
        ->where('id', $CategoryInfo->productCategoryId)
        ->get();

      $MemberInfos[$CategoryInfo->productCategoryId] = DB::table('manual_pass_book_balance')
        ->select('mfn_member_information.id', 'mfn_member_information.name', 'mfn_member_information.code', 'mfn_member_information.spouseFatherSonName')
        ->join('mfn_loans_product', 'manual_pass_book_balance.productIdFk', '=', 'mfn_loans_product.id')
        ->join('mfn_member_information', 'manual_pass_book_balance.memberIdFk', 'mfn_member_information.id')
        ->where([['manual_pass_book_balance.branchIdFk', $requestedBranch], ['manual_pass_book_balance.samityIdFk', $requestedSamity], ['mfn_loans_product.productCategoryId', $CategoryInfo->productCategoryId]])
        ->groupBy('mfn_member_information.id')
        ->get();
    }

    $BranchInfos = DB::table('gnr_branch')
      ->where('id', $requestedBranch)
      ->get();

    $SamityInfos = DB::table('mfn_samity')
      ->where('id', $requestedSamity)
      ->get();

    // dd($MemberInfos);

    return view('microfin.reports.passBookCheckingReports.PassBookCheckingReportsTable', compact('CategoryName', 'MemberInfos', 'BranchInfos', 'SamityInfos'));
  }

}
