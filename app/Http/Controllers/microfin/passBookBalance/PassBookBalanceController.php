<?php

namespace App\Http\Controllers\microfin\passBookBalance;

use App\Http\Controllers\Controller;
use DB;
use App\Http\Requests;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
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

class PassBookBalanceController extends Controller
{
  public function getBranch(){
    $LoanYear = array();
    $LoanYearArray = array();
    $UniqueLoanYear = array();
    $FundingOrganization = array();

    $SuccessQuery = 0;

    $BranchDatas = DB::table('gnr_branch')->get();

    // dd($BranchDatas);

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

    return view('microfin.passBookBalanceViews.PassBookBalanceForm', compact('BranchDatas', 'UniqueLoanYear', 'SuccessQuery'));
  }

  public function getSamity(Request $request ){
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

  public function getAddForm(Request $request){
    $resquestedBranch = $request->searchBranch;
    $requestedSamity = $request->Samity;
    $requestedQuerter = $request->searchQuerter;
    $requestedYear = $request->Year;

    $startDate = '';

    switch ($requestedQuerter) {
       case '1-3':
           $startDate = $requestedYear.'-03-31';
           break;
       case '4-6':
           $startDate = $requestedYear.'-06-30';
           break;
       case '7-9':
           $startDate = $requestedYear.'-09-30';
           break;
       case '10-12':
           $startDate = $requestedYear.'-12-31';
           break;
   }

    $memberInformations = array();
    $memberLoanInformations = array();
    $memberSavingsInformations = array();

    $memberInformations = DB::table('mfn_member_information')
           ->select('id', 'code', 'name', 'branchId', 'samityId', 'primaryProductId')
           // ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.memberIdFk')
           ->where('samityId', $requestedSamity)
           ->get();

    // dd($memberInformations);

    $memberLoanInformations = DB::table('mfn_member_information')
           ->select('mfn_member_information.id', 'mfn_member_information.code', 'mfn_member_information.name', 'mfn_loan.loanCode', 'mfn_loan.id as LoanId')
           ->join('mfn_loan', 'mfn_member_information.id', '=', 'mfn_loan.memberIdFk')
           ->where([['mfn_member_information.samityId', $requestedSamity], ['mfn_loan.isLoanCompleted', '=', 0]])
           // ->orWhere([['mfn_member_information.samityId', $requestedSamity], ['mfn_loan.isLoanCompleted', '!=', 1]])
           ->get();

    // DD($memberLoanInformations);

    $memberSavingsInformations = DB::table('mfn_member_information')
           ->select('mfn_member_information.id', 'mfn_member_information.code', 'mfn_member_information.name', 'mfn_savings_account.savingsCode', 'mfn_savings_account.id as savingsId')
           // ->join('mfn_loan', 'mfn_member_information.id', '=', 'mfn_loan.memberIdFk')
           ->join('mfn_savings_account', 'mfn_member_information.id', '=', 'mfn_savings_account.memberIdFk')
           // ->where([['mfn_member_information.samityId', $requestedSamity], ['mfn_savings_account.status', '=', 1], ['mfn_loan.isLoanCompleted', '=', 0]])
           ->orWhere([['mfn_member_information.samityId', $requestedSamity], ['mfn_savings_account.status', '=', 1]])
           ->get();

    // dd($memberSavingsInformations);

    return view('microfin.passBookBalanceViews.PassBookBalanceAddForm', compact('memberInformations', 'memberLoanInformations', 'memberSavingsInformations', 'startDate'));
  }

  public function submitAddForm(Request $request){
    $LoanYear = array();
    $LoanYearArray = array();
    $UniqueLoanYear = array();
    $FundingOrganization = array();

    $memberName = array();
    $loan = 0;
    $save = 0;

    // dd($request);

    $Count = sizeof($request->memberName);

    for ($i=0; $i < $Count; $i++) {
      $SuccessQuery = DB::table('manual_pass_book_balance')->insert(
        [
          'date' => $request->date[$i],
          'branchIdFk' => $request->branchName[$i],
          'samityIdFk' => $request->samityId[$i],
          'memberIdFk' => $request->id[$i],
          'loanCode' => $request->memberLoanID[$i],
          'productIdFk' => $request->productId[$i],
          'outstanding' => $request->memberOutstanding[$i],
          'due' => $request->memberDue[$i],
          'savingsCode' =>  $request->memberSavingsID[$i],
          'savingsBalance' => $request->memberBalance[$i]
        ]
      );
    }

    $BranchDatas = DB::table('gnr_branch')->get();

    // dd($BranchDatas);

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

    /* Success or Error */
    // dd($SuccessQuery);
    if ($SuccessQuery == true) {
      // $Success = 'True';
      $notification = array(
       'message' => 'You have successfully inserted the information!',
       'alert-type' => 'success'
      );
    }
    else {
      $notification = array(
       'message' => 'Please full fill the information properly!',
       'alert-type' => 'error'
      );
    }

    return redirect()->back()->with($notification);

    // return view('microfin.passBookBalanceViews.PassBookBalanceForm', compact('BranchDatas', 'UniqueLoanYear'));
  }

  /*  */

  public function getPassBookList(Request $request){
    $RequestedBranch = $request->searchBranch;
    $RequestedSamity = $request->Samity;
    $RequestedQuerter = $request->searchQuerter;
    $RequestedYear = $request->Year;
    $RequestedSubmit = $request->Submit;

    $startDate = '';
    $month = '';

    $SamityInformations = array();

    switch ($RequestedQuerter) {
       case '1-3':
           $startDate = $RequestedYear.'-03-31';
           $month = 'March';
           break;
       case '4-6':
           $startDate = $RequestedYear.'-06-30';
           $month = 'June';
           break;
       case '7-9':
           $startDate = $RequestedYear.'-09-30';
           $month = 'September';
           break;
       case '10-12':
           $startDate = $RequestedYear.'-12-31';
           $month = 'December';
           break;
   }

   if ($RequestedSamity == 'All') {
     $SamityInformations = DB::table('manual_pass_book_balance')
              ->select('mfn_samity.name', 'mfn_samity.id', 'mfn_samity.code')
              ->join('mfn_samity', 'manual_pass_book_balance.samityIdFk', '=', 'mfn_samity.id')
              ->where([['manual_pass_book_balance.branchIdFk', $RequestedBranch], ['manual_pass_book_balance.date', $startDate]])
              ->groupBy('manual_pass_book_balance.samityIdFk')
              ->get();
   }
   else {
     $SamityInformations = DB::table('manual_pass_book_balance')
              ->select('mfn_samity.name', 'mfn_samity.id', 'mfn_samity.code')
              ->join('mfn_samity', 'manual_pass_book_balance.samityIdFk', '=', 'mfn_samity.id')
              ->where([['manual_pass_book_balance.samityIdFk', $RequestedSamity], ['manual_pass_book_balance.date', $startDate]])
              ->groupBy('manual_pass_book_balance.samityIdFk')
              ->get();
   }

   // dd($SamityInformations);

   return view('microfin.passBookBalanceViews.PassBookBalanceReport', compact('SamityInformations', 'month', 'RequestedYear'));
  }

  public function getTableView(Request $request){
    // dd($request->id);
    $requestedSamityId = $request->id;
    $requestedMonth = $request->Month;
    $requestedYear = $request->RequestedYear;

    $startDate = '';

    $SamityInformations = array();
    $SamityInfos = array();

    switch ($requestedMonth) {
       case '3':
           $startDate = $requestedYear.'-03-31';
           break;
       case '6':
           $startDate = $requestedYear.'-06-30';
           break;
       case '9':
           $startDate = $requestedYear.'-09-30';
           break;
       case '12':
           $startDate = $requestedYear.'-12-31';
           break;
   }
   $SamityInfos = DB::table('manual_pass_book_balance')
            ->join('mfn_samity', 'manual_pass_book_balance.samityIdFk', '=', 'mfn_samity.id')
            ->where([['manual_pass_book_balance.samityIdFk', $requestedSamityId], ['manual_pass_book_balance.date', $startDate]])
            ->groupBy('manual_pass_book_balance.samityIdFk')
            ->get();

   $SamityInformations = DB::table('manual_pass_book_balance')
            ->join('mfn_member_information', 'manual_pass_book_balance.memberIdFk', '=', 'mfn_member_information.id')
            ->where([['manual_pass_book_balance.samityIdFk', $requestedSamityId], ['manual_pass_book_balance.date', $startDate]])
            ->groupBy('manual_pass_book_balance.savingsCode')
            ->get();

            // dd($SamityInfos);

    return view('microfin.passBookBalanceViews.PassBookBalanceReportView', compact('SamityInformations', 'SamityInfos'));
  }


  public function getTableEdit(Request $request){
    // dd($request->id);
    $requestedSamityId = $request->id;
    $requestedMonth = $request->Month;
    $requestedYear = $request->RequestedYear;

    $startDate = '';

    $SamityInformations = array();
    $SamityInfos = array();

    switch ($requestedMonth) {
       case '3':
           $startDate = $requestedYear.'-03-31';
           break;
       case '6':
           $startDate = $requestedYear.'-06-30';
           break;
       case '9':
           $startDate = $requestedYear.'-09-30';
           break;
       case '12':
           $startDate = $requestedYear.'-12-31';
           break;
   }

   $SamityInfos = DB::table('manual_pass_book_balance')
            ->join('mfn_samity', 'manual_pass_book_balance.samityIdFk', '=', 'mfn_samity.id')
            ->where([['manual_pass_book_balance.samityIdFk', $requestedSamityId], ['manual_pass_book_balance.date', $startDate]])
            ->groupBy('manual_pass_book_balance.samityIdFk')
            ->get();

   $SamityInformations = DB::table('manual_pass_book_balance')
            ->join('mfn_member_information', 'manual_pass_book_balance.memberIdFk', '=', 'mfn_member_information.id')
            ->where([['manual_pass_book_balance.samityIdFk', $requestedSamityId], ['manual_pass_book_balance.date', $startDate]])
            ->groupBy('manual_pass_book_balance.savingsCode')
            ->get();

            // dd($SamityInfos);

    return view('microfin.passBookBalanceViews.PassBookBalanceReportEdit', compact('SamityInformations', 'SamityInfos', 'startDate'));
  }

  public function getTableUpdate(Request $request){
    // dd($request);

    $LoanYear = array();
    $LoanYearArray = array();
    $UniqueLoanYear = array();
    $FundingOrganization = array();

    $memberName = array();
    $loan = 0;
    $save = 0;

    // dd($request);

    $Count = sizeof($request->memberName);

    for ($i=0; $i < $Count; $i++) {
      $SuccessQuery = DB::table('manual_pass_book_balance')
        ->where([['memberIdFk', $request->mId[$i]], ['date', $request->date[$i]]])
        ->update(
          [
            'date' => $request->date[$i],
            'branchIdFk' => $request->branchName[$i],
            'samityIdFk' => $request->samityId[$i],
            'memberIdFk' => $request->id[$i],
            'loanCode' => $request->memberLoanID[$i],
            'productIdFk' => $request->productId[$i],
            'outstanding' => $request->memberOutstanding[$i],
            'due' => $request->memberDue[$i],
            'savingsCode' =>  $request->memberSavingsID[$i],
            'savingsBalance' => $request->memberBalance[$i]
          ]
        );
    }

    $BranchDatas = DB::table('gnr_branch')->get();

    // dd($BranchDatas);

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

    // return view('microfin.passBookBalanceViews.PassBookBalanceForm', compact('BranchDatas', 'UniqueLoanYear'));

    /* Success or Error */
    // dd($SuccessQuery);
    if ($SuccessQuery == 0) {
      // $Success = 'True';
      $notification = array(
       'message' => 'You have successfully updated the information!',
       'alert-type' => 'success'
      );
    }
    else {
      $notification = array(
       'message' => 'Please full fill the information properly!',
       'alert-type' => 'error'
      );
    }

    return redirect()->back()->with($notification);

    // return "OK";

  }

  public function getTableDelete(Request $request) {
    $LoanYear = array();
    $LoanYearArray = array();
    $UniqueLoanYear = array();
    $FundingOrganization = array();

    $requestedSamityId = $request->id;
    $requestedMonth = $request->Month;
    $requestedYear = $request->RequestedYear;

    $memberName = array();
    $SamityInformations = array();
    $SamityInfos = array();

    $loan = 0;
    $save = 0;

    // dd($request);

    $startDate = '';

    switch ($requestedMonth) {
       case '3':
           $startDate = $requestedYear.'-03-31';
           break;
       case '6':
           $startDate = $requestedYear.'-06-30';
           break;
       case '9':
           $startDate = $requestedYear.'-09-30';
           break;
       case '12':
           $startDate = $requestedYear.'-12-31';
           break;
   }

    $SuccessQuery = DB::table('manual_pass_book_balance')
      ->where([['samityIdFk', $requestedSamityId], ['date', $startDate]])
      ->delete();

    $BranchDatas = DB::table('gnr_branch')->get();

    // dd($BranchDatas);

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

    // dd($SuccessQuery);
    //
    // return view('microfin.passBookBalanceViews.PassBookBalanceForm', compact('BranchDatas', 'UniqueLoanYear', 'SuccessQuery'));

    /* Success or Error */
    // dd($SuccessQuery);
    if ($SuccessQuery > 0) {
      // $Success = 'True';
      $notification = array(
       'message' => 'You have successfully deleted the information!',
       'alert-type' => 'success'
      );
    }
    else {
      $notification = array(
       'message' => 'Please full fill the information properly!',
       'alert-type' => 'error'
      );
    }

    return redirect()->back()->with($notification);
  }

}
