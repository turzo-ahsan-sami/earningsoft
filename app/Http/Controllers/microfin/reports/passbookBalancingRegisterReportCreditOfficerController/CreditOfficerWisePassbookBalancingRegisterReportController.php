<?php

namespace App\Http\Controllers\microfin\reports\passbookBalancingRegisterReportCreditOfficerController;

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
class CreditOfficerWisePassbookBalancingRegisterReportController extends Controller
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

    return view('microfin.reports.passbookBalancingRegisterReportCreditOfficer.passbookBalancingRegisterReportCreditOfficerForm', compact('BranchDatas', 'ProdCats', 'UniqueLoanYear'));

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

  public function getFieldOfficer(Request $request){
    $BranchId = $request->id;

    $Manager = array();

    $Manager = DB::table('hr_emp_org_info')
              ->select('hr_emp_general_info.emp_name_english', 'hr_emp_general_info.emp_id', 'hr_emp_general_info.id as EmployeeId', 'gnr_branch.id', 'hr_settings_position.name as PositionName')
              ->join('gnr_branch', 'hr_emp_org_info.branch_id_fk', '=', 'gnr_branch.id')
              ->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', '=', 'hr_settings_position.id')
              ->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
              ->where([['gnr_branch.id', $BranchId], ['hr_settings_position.name', '=', 'Credit Officer']])
              ->get();

    return response()->json($Manager);

  }

  // Methods and Querry.............
  public function BranchQuery($requestedBranchID){
    $Branch = array();

    $Branch = DB::table('gnr_branch')
              ->where('id', $requestedBranchID)
              ->get();

    return $Branch;
  }

  public function AreaQuery($requestedBranchID){
    $Areas = array();
    $AreaValues = array();
    $AreaName = '';

    $Areas = DB::table('gnr_area')
            ->get();

    foreach ($Areas as $key => $Area) {
      $AreaValues[$Area->name] = preg_split('/[\s,"]+/', $Area->branchId);
    }

    foreach ($AreaValues as $key1 => $AreaValue) {
      foreach ($AreaValue as $key2 => $AreaVal) {
        if ($AreaVal == $requestedBranchID) {
          $AreaName = $key1;
        }
      }
    }

    // dd($AreaName);

    return $AreaName;
  }

  public function CreditOfficerQuery($branch, $requestedFieldOfficerID){
    $FieldOfficer = array();

    $FieldOfficer = DB::select("SELECT * FROM `hr_emp_general_info` WHERE id='$requestedFieldOfficerID'");

    // dd($FieldOfficer);

    return $FieldOfficer;
  }

  public function SamityQuery($requestedBranchID, $requestedFieldOfficerID, $RequestedProductCategoryID){
    $SamityInfos = array();

    if ($RequestedProductCategoryID == 'All') {
      $SamityInfos = DB::table('mfn_samity')
              ->where([['branchId', $requestedBranchID], ['fieldOfficerId', $requestedFieldOfficerID]])
              ->get();
    }
    else {
      $SamityInfos = DB::table('mfn_samity')
              ->where([['branchId', $requestedBranchID], ['fieldOfficerId', $requestedFieldOfficerID], ])
              ->get();
    }

            // dd($SamityInfos);

    return $SamityInfos;
  }

  public function MemberQuery($requestedBranchID, $requestedFieldOfficerID, $SamityInfos, $RequestedProductCategoryID){
    $MemberInfos = array();

    if ($RequestedProductCategoryID == 'All') {
      foreach ($SamityInfos as $key => $SamityInfo) {
        $MemberInfos[$SamityInfo->id] = DB::table('mfn_member_information')
              ->where([['branchId', $requestedBranchID], ['samityId', $SamityInfo->id]])
              ->count('id');
      }
    }
    else {
      foreach ($SamityInfos as $key => $SamityInfo) {
        // dd($MemberInfos);
        $MemberInfos[$SamityInfo->id] = DB::table('mfn_member_information')
              ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
              ->where([['mfn_member_information.branchId', $requestedBranchID], ['mfn_member_information.samityId', $SamityInfo->id], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]])
              ->count('mfn_member_information.id');
              // ->get();

        // $MemberInfos[$SamityInfo->id] = DB::select("SELECT * FROM `mfn_member_information` JOIN mfn_loans_product ON mfn_member_information.primaryProductId=mfn_loans_product.id WHERE mfn_member_information.branchId='$requestedBranchID' AND mfn_member_information.samityId='$SamityInfo->id' AND mfn_loans_product.productCategoryId='$RequestedProductCategoryID'");
      }
    }
    // dd($MemberInfos);

    return $MemberInfos;
  }

  public function BorrowerQuery($requestedBranchID, $requestedFieldOfficerID, $SamityInfos, $RequestedProductCategoryID){
    $BorrowerInfos = array();

    if ($RequestedProductCategoryID == 'All') {
      foreach ($SamityInfos as $key => $SamityInfo) {
        $BorrowerInfos[$SamityInfo->id] = DB::table('mfn_loan')
              ->where([['branchIdFk', $requestedBranchID], ['samityIdFk', $SamityInfo->id]])
              ->count('id');
      }
    }
    else {
      foreach ($SamityInfos as $key => $SamityInfo) {
        $BorrowerInfos[$SamityInfo->id] = DB::table('mfn_loan')
              ->join('mfn_loans_product', 'mfn_loan.primaryProductIdFk', '=', 'mfn_loans_product.id')
              ->where([['mfn_loan.branchIdFk', $requestedBranchID], ['mfn_loan.samityIdFk', $SamityInfo->id], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]])
              ->count('mfn_loan.id');
      }
    }

    // dd($BorrowerInfos);

    return $BorrowerInfos;
  }

  public function DisbursementAmountQuery($requestedBranchID, $requestedFieldOfficerID, $SamityInfos, $RequestedProductCategoryID){
    $LoanAmountInfos = array();

    if ($RequestedProductCategoryID == 'All') {
      foreach ($SamityInfos as $key => $SamityInfo) {
        $LoanAmountInfos[$SamityInfo->id] = DB::table('mfn_loan')
              ->where([['branchIdFk', $requestedBranchID], ['samityIdFk', $SamityInfo->id]])
              ->sum('loanAmount');
              // ->get();
      }
    }
    else {
      foreach ($SamityInfos as $key => $SamityInfo) {
        $LoanAmountInfos[$SamityInfo->id] = DB::table('mfn_loan')
              ->join('mfn_loans_product', 'mfn_loan.primaryProductIdFk', '=', 'mfn_loans_product.id')
              ->where([['mfn_loan.branchIdFk', $requestedBranchID], ['mfn_loan.samityIdFk', $SamityInfo->id], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]])
              ->sum('mfn_loan.loanAmount');
              // ->get();
      }
    }
    // dd($LoanAmountInfos);

    return $LoanAmountInfos;
  }

  public function LoanOutstandingQuery($requestedBranchID, $requestedFieldOfficerID, $SamityInfos, $RequestedProductCategoryID, $ToDate){
    $LoanOutstanding = array();
    $LoanAmount = array();
    $LoanCollection = array();

    if ($RequestedProductCategoryID == 'All') {
      foreach ($SamityInfos as $key => $SamityInfo) {
        $LoanAmount[$SamityInfo->id] = DB::table('mfn_loan')
              ->where([['branchIdFk', $requestedBranchID], ['samityIdFk', $SamityInfo->id]])
              ->sum('loanAmount');
              // ->get();

        $LoanCollection[$SamityInfo->id] = DB::table('mfn_loan_collection')
              ->where([['branchIdFk', $requestedBranchID], ['samityIdFk', $SamityInfo->id], ['collectionDate', '<=', $ToDate]])
              ->sum('principalAmount');
      }
    }
    else {
      foreach ($SamityInfos as $key => $SamityInfo) {
        $LoanAmount[$SamityInfo->id] = DB::table('mfn_loan')
              ->join('mfn_loans_product', 'mfn_loan.primaryProductIdFk', '=', 'mfn_loans_product.id')
              ->where([['mfn_loan.branchIdFk', $requestedBranchID], ['mfn_loan.samityIdFk', $SamityInfo->id], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]])
              ->sum('mfn_loan.loanAmount');
              // ->get();

        $LoanCollection[$SamityInfo->id] = DB::table('mfn_loan_collection')
              ->join('mfn_loans_product', 'mfn_loan_collection.primaryProductIdFk', '=', 'mfn_loans_product.id')
              ->where([['mfn_loan_collection.branchIdFk', $requestedBranchID], ['mfn_loan_collection.samityIdFk', $SamityInfo->id], ['mfn_loan_collection.collectionDate','<=', $ToDate], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]])
              ->sum('mfn_loan_collection.principalAmount');
      }
    }

    foreach ($LoanAmount as $key1 => $LoanAmount1) {
      foreach ($LoanCollection as $key2 => $LoanCollection1) {
        if ($key1 == $key2) {
          $LoanOutstanding[$key1] = $LoanAmount1 - $LoanCollection1;
        }
      }
    }
    // dd($LoanOutstanding);

    return $LoanOutstanding;
  }

  public function LoanDueQuery($requestedBranchID, $requestedFieldOfficerID, $SamityInfos, $RequestedProductCategoryID, $ToDate){
    $LoanDue = array();
    $LoanScheduleAmount = array();
    $LoanCollection = array();

    if ($RequestedProductCategoryID == 'All') {
      foreach ($SamityInfos as $key => $SamityInfo) {
        $LoanScheduleAmount[$SamityInfo->id] = DB::table('mfn_loan_schedule')
              ->join('mfn_loan', 'mfn_loan_schedule.loanIdFk', '=', 'mfn_loan.id')
              ->where([['mfn_loan.branchIdFk', $requestedBranchID], ['mfn_loan.samityIdFk', $SamityInfo->id], ['mfn_loan_schedule.scheduleDate','<=', $ToDate]])
              ->sum('mfn_loan_schedule.principalAmount');
              // ->get();

        $LoanCollection[$SamityInfo->id] = DB::table('mfn_loan_collection')
              ->where([['branchIdFk', $requestedBranchID], ['samityIdFk', $SamityInfo->id], ['collectionDate','<=', $ToDate]])
              ->sum('principalAmount');
      }
    }
    else {
      foreach ($SamityInfos as $key => $SamityInfo) {
        $LoanScheduleAmount[$SamityInfo->id] = DB::table('mfn_loan_schedule')
              ->join('mfn_loan', 'mfn_loan_schedule.loanIdFk', '=', 'mfn_loan.id')
              ->join('mfn_loans_product', 'mfn_loan.primaryProductIdFk', '=', 'mfn_loans_product.id')
              ->where([['mfn_loan.branchIdFk', $requestedBranchID], ['mfn_loan.samityIdFk', $SamityInfo->id], ['mfn_loan_schedule.scheduleDate','<=', $ToDate], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]])
              ->sum('mfn_loan_schedule.principalAmount');
              // ->get();

        $LoanCollection[$SamityInfo->id] = DB::table('mfn_loan_collection')
              ->join('mfn_loans_product', 'mfn_loan_collection.primaryProductIdFk', '=', 'mfn_loans_product.id')
              ->where([['mfn_loan_collection.branchIdFk', $requestedBranchID], ['mfn_loan_collection.samityIdFk', $SamityInfo->id], ['mfn_loan_collection.collectionDate','<=', $ToDate], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]])
              ->sum('mfn_loan_collection.principalAmount');
      }
    }

    foreach ($LoanScheduleAmount as $key1 => $LoanScheduleAmount1) {
      foreach ($LoanCollection as $key2 => $LoanCollection1) {
        if ($key1 == $key2 and $LoanScheduleAmount1 >= $LoanCollection1) {
          $LoanDue[$key1] = $LoanScheduleAmount1 - $LoanCollection1;
        }
        elseif ($key1 == $key2 and $LoanScheduleAmount1 < $LoanCollection1) {
          $LoanDue[$key1] = 0.00;
        }
      }
    }
    // dd($LoanDue);

    return $LoanDue;
  }

  public function SavingsAmountQuery($requestedBranchID, $requestedFieldOfficerID, $SamityInfos, $RequestedProductCategoryID, $ToDate, $FromDate, $SavingsProduct){
    $SavingsAmount = array();

    // dd($SavingsProduct);

    if ($RequestedProductCategoryID == 'All') {
      foreach ($SamityInfos as $key => $SamityInfo) {
        foreach ($SavingsProduct as $key => $SavingsProduct1) {
          $SavingsAmount[$SamityInfo->id][$SavingsProduct1->id] = DB::table('mfn_savings_deposit')
                  ->join('mfn_saving_product', 'mfn_savings_deposit.productIdFk', '=', 'mfn_saving_product.id')
                  ->where([['mfn_savings_deposit.branchIdFk', $requestedBranchID], ['mfn_savings_deposit.samityIdFk', $SamityInfo->id], ['mfn_savings_deposit.depositDate', '>=', $FromDate], ['mfn_savings_deposit.depositDate', '<=', $ToDate], ['mfn_savings_deposit.productIdFk', $SavingsProduct1->id]])
                  ->sum('mfn_savings_deposit.amount');
        }
      }
    }
    else {
      foreach ($SamityInfos as $key => $SamityInfo) {
        foreach ($SavingsProduct as $key => $SavingsProduct1) {
          // dd($SavingsAmount);
          $SavingsAmount[$SamityInfo->id][$SavingsProduct1->id] = DB::table('mfn_savings_deposit')
                  ->join('mfn_loans_product', 'mfn_savings_deposit.primaryProductIdFk', '=', 'mfn_loans_product.id')
                  ->join('mfn_saving_product', 'mfn_savings_deposit.productIdFk', '=', 'mfn_saving_product.id')
                  ->where([['mfn_savings_deposit.branchIdFk', $requestedBranchID], ['mfn_savings_deposit.samityIdFk', $SamityInfo->id], ['mfn_savings_deposit.depositDate', '>=', $FromDate], ['mfn_savings_deposit.depositDate', '<=', $ToDate], ['mfn_savings_deposit.productIdFk', $SavingsProduct1->id], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]])
                  ->sum('mfn_savings_deposit.amount');
        }
      }
    }

    // dd($SavingsAmount);

    return $SavingsAmount;
  }

  public function ManualOutstandingQuery($requestedBranchID, $requestedFieldOfficerID, $SamityInfos, $RequestedProductCategoryID, $ToDate){
    $ManualOutstanding = array();

    if ($RequestedProductCategoryID == 'All') {
      foreach ($SamityInfos as $key => $SamityInfo) {
        $ManualOutstanding[$SamityInfo->id] = DB::table('manual_pass_book_balance')
                ->where([['branchIdFk', $requestedBranchID], ['samityIdFk', $SamityInfo->id], ['date', $ToDate]])
                ->sum('outstanding');
      }
    }
    else{
      foreach ($SamityInfos as $key => $SamityInfo) {
        $ManualOutstanding[$SamityInfo->id] = DB::table('manual_pass_book_balance')
                ->join('mfn_loans_product', 'manual_pass_book_balance.productIdFk', '=', 'mfn_loans_product.id')
                ->where([['manual_pass_book_balance.branchIdFk', $requestedBranchID], ['manual_pass_book_balance.samityIdFk', $SamityInfo->id], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['date', $ToDate]])
                ->sum('manual_pass_book_balance.outstanding');
      }
    }

    // dd($ManualOutstanding);

    return $ManualOutstanding;
  }

  public function ManualDueQuery($requestedBranchID, $requestedFieldOfficerID, $SamityInfos, $RequestedProductCategoryID, $ToDate){
    $ManualDue = array();

    if ($RequestedProductCategoryID == 'All') {
      foreach ($SamityInfos as $key => $SamityInfo) {
        $ManualDue[$SamityInfo->id] = DB::table('manual_pass_book_balance')
                ->where([['branchIdFk', $requestedBranchID], ['samityIdFk', $SamityInfo->id], ['date', $ToDate]])
                ->sum('due');
      }
    }
    else{
      foreach ($SamityInfos as $key => $SamityInfo) {
        $ManualDue[$SamityInfo->id] = DB::table('manual_pass_book_balance')
                ->join('mfn_loans_product', 'manual_pass_book_balance.productIdFk', '=', 'mfn_loans_product.id')
                ->where([['manual_pass_book_balance.branchIdFk', $requestedBranchID], ['manual_pass_book_balance.samityIdFk', $SamityInfo->id], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['date', $ToDate]])
                ->sum('manual_pass_book_balance.due');
      }
    }

    // dd($ManualDue);

    return $ManualDue;
  }

  public function ManualSavingsQuery($requestedBranchID, $requestedFieldOfficerID, $SamityInfos, $RequestedProductCategoryID, $ToDate, $SavingsProduct){
    $ManualSavings = array();

    if ($RequestedProductCategoryID == 'All') {
      foreach ($SamityInfos as $key => $SamityInfo) {
        foreach ($SavingsProduct as $key => $SavingsProduct1) {
          $ManualSavings[$SamityInfo->id][$SavingsProduct1->id] = DB::table('manual_pass_book_balance')
                  ->join('mfn_savings_account', 'manual_pass_book_balance.savingsCode', '=', 'mfn_savings_account.savingsCode')
                  ->where([['manual_pass_book_balance.branchIdFk', $requestedBranchID], ['manual_pass_book_balance.samityIdFk', $SamityInfo->id], ['manual_pass_book_balance.date', $ToDate], ['mfn_savings_account.savingsProductIdFk', $SavingsProduct1->id]])
                  ->sum('manual_pass_book_balance.savingsBalance');
        }
      }
    }
    else{
      foreach ($SamityInfos as $key => $SamityInfo) {
        foreach ($SavingsProduct as $key => $SavingsProduct1) {
          $ManualSavings[$SamityInfo->id][$SavingsProduct1->id] = DB::table('manual_pass_book_balance')
                  ->join('mfn_loans_product', 'manual_pass_book_balance.productIdFk', '=', 'mfn_loans_product.id')
                  ->join('mfn_savings_account', 'manual_pass_book_balance.savingsCode', '=', 'mfn_savings_account.savingsCode')
                  ->where([['manual_pass_book_balance.branchIdFk', $requestedBranchID], ['manual_pass_book_balance.samityIdFk', $SamityInfo->id], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['date', $ToDate], ['mfn_savings_account.savingsProductIdFk', $SavingsProduct1->id]])
                  ->sum('manual_pass_book_balance.savingsBalance');
        }
      }
    }

    // dd($ManualSavings);

    return $ManualSavings;
  }

  // Main method for calculation...........
  public function getReport(Request $request){
    // Requested data variables.....
    $requestedBranchID = $request->searchBranch;
    // $requestedSamityID = $request->searchSamity;
    $requestedFieldOfficerID = $request->searchFieldOfficer;
    $requestedYear = $request->Year;
    $requestedDateRange = $request->DateRange;
    $RequestedProductCategoryID = $request->searchProCat;

    // dd($RequestedProductCategoryID);

    // Decalred variables........
    $FromDate = '';
    $ToDate = '';
    $Querter = '';

    // Declared Array............
    $SavingsProduct = array();
    $BranchInfos = array();
    $AreaInfos = array();
    $CreditOfficerInfos = array();
    $SamityInfos = array();
    $MemberInfos = array();
    $BorrowerInfos = array();
    $DisbursementAmountInfos = array();
    $LoanOutstanding = array();
    $LoanDue = array();
    $SavingsAmount = array();
    $ManualOutstanding = array();
    $ManualDue = array();
    $ManualSavings = array();

    // Querter and date calculation........
    switch ($requestedDateRange) {
       case '1-3':
           $FromDate = $requestedYear.'-01-01';
           $ToDate = $requestedYear.'-03-31';
           $Querter = '1st';
           break;
       case '4-6':
           $FromDate = $requestedYear.'-04-01';
           $ToDate = $requestedYear.'-06-30';
           $Querter = '2nd';
           break;
       case '7-9':
           $FromDate = $requestedYear.'-07-01';
           $ToDate = $requestedYear.'-09-30';
           $Querter = '3rd';
           break;
       case '10-12':
           $FromDate = $requestedYear.'-10-01';
           $ToDate = $requestedYear.'-12-31';
           $Querter = '4th';
           break;
   }

   // Query calculation and method to call........
   $SavingsProduct = DB::table('mfn_saving_product')
                  ->get();

   $BranchInfos = $this->BranchQuery($requestedBranchID);

   $AreaInfos = $this->AreaQuery($requestedBranchID);

   $CreditOfficerInfos = $this->CreditOfficerQuery($requestedBranchID, $requestedFieldOfficerID);

   $SamityInfos = $this->SamityQuery($requestedBranchID, $requestedFieldOfficerID, $RequestedProductCategoryID);

   $MemberInfos = $this->MemberQuery($requestedBranchID, $requestedFieldOfficerID, $SamityInfos, $RequestedProductCategoryID);

   $BorrowerInfos = $this->BorrowerQuery($requestedBranchID, $requestedFieldOfficerID, $SamityInfos, $RequestedProductCategoryID);

   $DisbursementAmountInfos = $this->DisbursementAmountQuery($requestedBranchID, $requestedFieldOfficerID, $SamityInfos, $RequestedProductCategoryID);

   $LoanOutstanding = $this->LoanOutstandingQuery($requestedBranchID, $requestedFieldOfficerID, $SamityInfos, $RequestedProductCategoryID, $ToDate);

   $LoanDue = $this->LoanDueQuery($requestedBranchID, $requestedFieldOfficerID, $SamityInfos, $RequestedProductCategoryID, $ToDate);

   $SavingsAmount = $this->SavingsAmountQuery($requestedBranchID, $requestedFieldOfficerID, $SamityInfos, $RequestedProductCategoryID, $ToDate, $FromDate, $SavingsProduct);

   $ManualOutstanding = $this->ManualOutstandingQuery($requestedBranchID, $requestedFieldOfficerID, $SamityInfos, $RequestedProductCategoryID, $ToDate);

   $ManualDue = $this->ManualDueQuery($requestedBranchID, $requestedFieldOfficerID, $SamityInfos, $RequestedProductCategoryID, $ToDate);

   $ManualSavings = $this->ManualSavingsQuery($requestedBranchID, $requestedFieldOfficerID, $SamityInfos, $RequestedProductCategoryID, $ToDate, $SavingsProduct);

   // dd($LoanDue);

   return view('microfin.reports.passbookBalancingRegisterReportCreditOfficer.passbookBalancingRegisterReportCreditOfficerTable', compact('SavingsProduct', 'BranchInfos', 'AreaInfos', 'CreditOfficerInfos',
   'Querter', 'requestedDateRange', 'SamityInfos', 'MemberInfos', 'BorrowerInfos', 'DisbursementAmountInfos', 'LoanOutstanding', 'LoanDue', 'SavingsAmount', 'ManualOutstanding', 'ManualDue', 'ManualSavings'));
  }

}
