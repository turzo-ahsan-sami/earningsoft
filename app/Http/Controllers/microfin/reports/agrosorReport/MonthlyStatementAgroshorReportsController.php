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

class MonthlyStatementAgroshorReportsController extends Controller
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

    return view('microfin.reports.agrosorReportsView.monthlyStatementAgroshorReportsViews.monthlyStatementAgroshorReportsForm', compact('BranchDatas', 'FundingOrganization', 'UniqueLoanYear',
     'monthsOption'));
  }

  // METHOD'S AND QUERY FOR CALCULATION........................................................................................
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

  public function migratedMaleMemberAdmissionQuery($requestedBranch, $startDate, $endDate) {
    $directMemberAdmissionMale = array();

    if ($requestedBranch == 'All') {
      $directMemberAdmissionMale = DB::select("SELECT SUM(`newMemberAdmissionNo_bt`)+SUM(`newMemberAdmissionNo_bpt`) AS totalMigratedMember FROM
        `mfn_month_end_process_members` WHERE genderTypeId=1 AND `date`<='$endDate' AND `loanProductIdFk` IN(7,8,9,10)");
    }
    else {
      $directMemberAdmissionMale = DB::select("SELECT SUM(`newMemberAdmissionNo_bt`)+SUM(`newMemberAdmissionNo_bpt`) AS totalMigratedMember FROM
        `mfn_month_end_process_members` WHERE genderTypeId=1 AND `date`<='$endDate' AND `loanProductIdFk` IN(7,8,9,10) AND `branchIdFk`='$requestedBranch'");
    }

    return $directMemberAdmissionMale;
  }

  public function directMaleMemberAdmissionQuery($requestedBranch, $startDate, $endDate) {
    $directMemberAdmissionMale = array();

    if ($requestedBranch == 'All') {
      $directMemberAdmissionMale = DB::table('mfn_month_end_process_members')
        ->join('mfn_loans_product', 'mfn_month_end_process_members.loanProductIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_members.date', '<=', $endDate], ['mfn_month_end_process_members.genderTypeId', '=', 1], ['mfn_loans_product.productCategoryId', '=', 2]])
        ->sum('mfn_month_end_process_members.newMemberAdmissionNo');
    }
    else {
      $directMemberAdmissionMale = DB::table('mfn_month_end_process_members')
        ->join('mfn_loans_product', 'mfn_month_end_process_members.loanProductIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_members.date', '<=', $endDate], ['mfn_month_end_process_members.genderTypeId', '=', 1], ['mfn_loans_product.productCategoryId', '=', 2], ['mfn_month_end_process_members.branchIdFk', $requestedBranch]])
        ->sum('mfn_month_end_process_members.newMemberAdmissionNo');
    }

    return $directMemberAdmissionMale;
  }

  public function migratedFemaleMemberAdmissionQuery($requestedBranch, $startDate, $endDate) {
    $directMemberAdmissionMale = array();

    if ($requestedBranch == 'All') {
      $directMemberAdmissionMale = DB::select("SELECT SUM(`newMemberAdmissionNo_bt`)+SUM(`newMemberAdmissionNo_bpt`) AS totalMigratedMember FROM
        `mfn_month_end_process_members` WHERE genderTypeId=2 AND `date`<='$endDate' AND `loanProductIdFk` IN(7,8,9,10)");
    }
    else {
      $directMemberAdmissionMale = DB::select("SELECT SUM(`newMemberAdmissionNo_bt`)+SUM(`newMemberAdmissionNo_bpt`) AS totalMigratedMember FROM
        `mfn_month_end_process_members` WHERE genderTypeId=2 AND `date`<='$endDate' AND `loanProductIdFk` IN(7,8,9,10) AND `branchIdFk`='$requestedBranch'");
    }

    return $directMemberAdmissionMale;
  }

  public function directFemaleMemberAdmissionQuery($requestedBranch, $startDate, $endDate) {
    $directMemberAdmissionMale = array();

    if ($requestedBranch == 'All') {
      $directMemberAdmissionMale = DB::table('mfn_month_end_process_members')
        ->join('mfn_loans_product', 'mfn_month_end_process_members.loanProductIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_members.date', '<=', $endDate], ['mfn_month_end_process_members.genderTypeId', '=', 2], ['mfn_loans_product.productCategoryId', '=', 2]])
        ->sum('mfn_month_end_process_members.newMemberAdmissionNo');
    }
    else {
      $directMemberAdmissionMale = DB::table('mfn_month_end_process_members')
        ->join('mfn_loans_product', 'mfn_month_end_process_members.loanProductIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_members.date', '<=', $endDate], ['mfn_month_end_process_members.genderTypeId', '=', 2], ['mfn_loans_product.productCategoryId', '=', 2], ['mfn_month_end_process_members.branchIdFk', $requestedBranch]])
        ->sum('mfn_month_end_process_members.newMemberAdmissionNo');
    }

    return $directMemberAdmissionMale;
  }

  public function migratedMaleLoaneeQuery($requestedBranch, $startDate, $endDate) {
    $directMemberAdmissionMale = array();

    if ($requestedBranch == 'All') {
      $directMemberAdmissionMale = DB::select("SELECT SUM(`borrowerNo_bt`) AS borrowerNo_bt FROM
        `mfn_month_end_process_loans` WHERE genderTypeId=1 AND `date`<='$endDate' AND `productIdFk` IN(7,8,9,10)");
    }
    else {
      $directMemberAdmissionMale = DB::select("SELECT SUM(`borrowerNo_bt`) AS borrowerNo_bt FROM
        `mfn_month_end_process_loans` WHERE genderTypeId=1 AND `date`<='$endDate' AND `productIdFk` IN(7,8,9,10) AND `branchIdFk`='$requestedBranch'");
    }

    return $directMemberAdmissionMale;
  }

  public function directMaleLoaneeQuery($requestedBranch, $startDate, $endDate) {
    $directMemberAdmissionMale = array();

    if ($requestedBranch == 'All') {
      $directMemberAdmissionMale = DB::table('mfn_month_end_process_loans')
        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_loans.date', '<=', $endDate], ['mfn_month_end_process_loans.genderTypeId', '=', 1], ['mfn_loans_product.productCategoryId', '=', 2] ])
        ->sum('mfn_month_end_process_loans.borrowerNo');
    }
    else {
      $directMemberAdmissionMale = DB::table('mfn_month_end_process_loans')
        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_loans.date', '<=', $endDate], ['mfn_month_end_process_loans.genderTypeId', '=', 1], ['mfn_loans_product.productCategoryId', '=', 2],  ['mfn_month_end_process_loans.branchIdFk', $requestedBranch]])
        ->sum('mfn_month_end_process_loans.borrowerNo');
    }

    return $directMemberAdmissionMale;
  }

  public function migratedFemaleLoaneeQuery($requestedBranch, $startDate, $endDate) {
    $directMemberAdmissionMale = array();

    if ($requestedBranch == 'All') {
      $directMemberAdmissionMale = DB::select("SELECT SUM(`borrowerNo_bt`) AS borrowerNo_bt FROM
        `mfn_month_end_process_loans` WHERE genderTypeId=2 AND `date`<='$endDate' AND `productIdFk` IN(7,8,9,10)");
    }
    else {
      $directMemberAdmissionMale = DB::select("SELECT SUM(`borrowerNo_bt`) AS borrowerNo_bt FROM
        `mfn_month_end_process_loans` WHERE genderTypeId=2 AND `date`<='$endDate' AND `productIdFk` IN(7,8,9,10) AND `branchIdFk`='$requestedBranch'");
    }

    return $directMemberAdmissionMale;
  }

  public function directFemaleLoaneeQuery($requestedBranch, $startDate, $endDate) {
    $directMemberAdmissionMale = array();

    if ($requestedBranch == 'All') {
      $directMemberAdmissionMale = DB::table('mfn_month_end_process_loans')
        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_loans.date', '<=', $endDate], ['mfn_month_end_process_loans.genderTypeId', '=', 2], ['mfn_loans_product.productCategoryId', '=', 2]])
        ->sum('mfn_month_end_process_loans.borrowerNo');
    }
    else {
      $directMemberAdmissionMale = DB::table('mfn_month_end_process_loans')
        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_loans.date', '<=', $endDate], ['mfn_loans_product.productCategoryId', '=', 2], ['mfn_month_end_process_loans.genderTypeId', '=', 2], ['mfn_month_end_process_loans.branchIdFk', $requestedBranch]])
        ->sum('mfn_month_end_process_loans.borrowerNo');
    }

    return $directMemberAdmissionMale;
  }

  // CURRENT MONTH LOANEE QUERY..................................................................................................
  public function migratedMaleCurrentLoaneeQuery($requestedBranch, $startDate, $endDate) {
    $directMemberAdmissionMale = array();

    if ($requestedBranch == 'All') {
      $directMemberAdmissionMale = DB::select("SELECT SUM(`borrowerNo_bt`) AS borrowerNo_bt FROM
        `mfn_month_end_process_loans` WHERE genderTypeId=1 AND `date`>='$startDate' AND `date`<='$endDate' AND `productIdFk` IN(7,8,9,10)");
    }
    else {
      $directMemberAdmissionMale = DB::select("SELECT SUM(`borrowerNo_bt`) AS borrowerNo_bt FROM
        `mfn_month_end_process_loans` WHERE genderTypeId=1 AND `date`>='$startDate' AND `date`<='$endDate' AND `productIdFk` IN(7,8,9,10) AND `branchIdFk`='$requestedBranch'");
    }

    return $directMemberAdmissionMale;
  }

  public function directMaleCurrentLoaneeQuery($requestedBranch, $startDate, $endDate) {
    $directMemberAdmissionMale = array();

    if ($requestedBranch == 'All') {
      $directMemberAdmissionMale = DB::table('mfn_month_end_process_loans')
        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_loans.date', '>=', $startDate], ['mfn_month_end_process_loans.date', '<=', $endDate], ['mfn_loans_product.productCategoryId', '=', 2], ['mfn_month_end_process_loans.genderTypeId', '=', 1]])
        ->sum('mfn_month_end_process_loans.borrowerNo');
    }
    else {
      $directMemberAdmissionMale = DB::table('mfn_month_end_process_loans')
        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_loans.date', '>=', $startDate], ['mfn_month_end_process_loans.date', '<=', $endDate], ['mfn_loans_product.productCategoryId', '=', 2], ['mfn_month_end_process_loans.genderTypeId', '=', 1], ['branchIdFk', $requestedBranch]])
        ->sum('mfn_month_end_process_loans.borrowerNo');
    }

    return $directMemberAdmissionMale;
  }

  public function migratedFemaleCurrentLoaneeQuery($requestedBranch, $startDate, $endDate) {
    $directMemberAdmissionMale = array();

    if ($requestedBranch == 'All') {
      $directMemberAdmissionMale = DB::select("SELECT SUM(`borrowerNo_bt`) AS borrowerNo_bt FROM
        `mfn_month_end_process_loans` WHERE genderTypeId=2 AND `date`>='$startDate' AND `date`<='$endDate'AND `productIdFk` IN(7,8,9,10)");
    }
    else {
      $directMemberAdmissionMale = DB::select("SELECT SUM(`borrowerNo_bt`) AS borrowerNo_bt FROM
        `mfn_month_end_process_loans` WHERE genderTypeId=2 AND `date`>='$startDate' AND `date`<='$endDate'AND `productIdFk` IN(7,8,9,10) AND `branchIdFk`='$requestedBranch'");
    }

    return $directMemberAdmissionMale;
  }

  public function directFemaleCurrentLoaneeQuery($requestedBranch, $startDate, $endDate) {
    $directMemberAdmissionMale = array();

    if ($requestedBranch == 'All') {
      $directMemberAdmissionMale = DB::table('mfn_month_end_process_loans')
        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_loans.date', '>=', $startDate], ['mfn_month_end_process_loans.date', '<=', $endDate], ['mfn_loans_product.productCategoryId', '=', 2], ['mfn_month_end_process_loans.genderTypeId', '=', 2]])
        ->sum('mfn_month_end_process_loans.borrowerNo');
    }
    else {
      $directMemberAdmissionMale = DB::table('mfn_month_end_process_loans')
        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_loans.date', '>=', $startDate], ['mfn_month_end_process_loans.date', '<=', $endDate], ['mfn_loans_product.productCategoryId', '=', 2], ['mfn_month_end_process_loans.genderTypeId', '=', 2], ['mfn_month_end_process_loans.branchIdFk', $requestedBranch]])
        ->sum('mfn_month_end_process_loans.borrowerNo');
    }

    return $directMemberAdmissionMale;
  }

  public function migratedMaleCurrentDisburementAmountQuery($requestedBranch, $startDate, $endDate) {
    $directMemberAdmissionMale = array();

    if ($requestedBranch == 'All') {
      $directMemberAdmissionMale = DB::select("SELECT SUM(`disbursedAmount_bt`) AS disbursedAmount_bt FROM
        `mfn_month_end_process_loans` WHERE genderTypeId=1 AND `date`>='$startDate' AND `date`<='$endDate' AND `productIdFk` IN(7,8,9,10)");
    }
    else {
      $directMemberAdmissionMale = DB::select("SELECT SUM(`disbursedAmount_bt`) AS disbursedAmount_bt FROM
        `mfn_month_end_process_loans` WHERE genderTypeId=1 AND `date`>='$startDate' AND `date`<='$endDate' AND `productIdFk` IN(7,8,9,10) AND `branchIdFk`='$requestedBranch'");
    }

    return $directMemberAdmissionMale;
  }

  public function directMaleCurrentDisburementAmountQuery($requestedBranch, $startDate, $endDate) {
    $directMemberAdmissionMale = array();

    if ($requestedBranch == 'All') {
      $directMemberAdmissionMale = DB::table('mfn_month_end_process_loans')
        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_loans.date', '>=', $startDate], ['mfn_month_end_process_loans.date', '<=', $endDate], ['mfn_loans_product.productCategoryId', '=', 2], ['mfn_month_end_process_loans.genderTypeId', '=', 1]])
        ->sum('mfn_month_end_process_loans.disbursedAmount');
    }
    else {
      $directMemberAdmissionMale = DB::table('mfn_month_end_process_loans')
        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_loans.date', '>=', $startDate], ['mfn_month_end_process_loans.date', '<=', $endDate], ['mfn_loans_product.productCategoryId', '=', 2], ['mfn_month_end_process_loans.genderTypeId', '=', 1], ['mfn_month_end_process_loans.branchIdFk', $requestedBranch]])
        ->sum('mfn_month_end_process_loans.disbursedAmount');
    }

    return $directMemberAdmissionMale;
  }

  public function migratedFemaleCurrentDisburementAmountQuery($requestedBranch, $startDate, $endDate) {
    $directMemberAdmissionMale = array();

    if ($requestedBranch == 'All') {
      $directMemberAdmissionMale = DB::select("SELECT SUM(`disbursedAmount_bt`) AS disbursedAmount_bt FROM
        `mfn_month_end_process_loans` WHERE genderTypeId=2 AND `date`>='$startDate' AND `date`<='$endDate' AND `productIdFk` IN(7,8,9,10)");
    }
    else {
      $directMemberAdmissionMale = DB::select("SELECT SUM(`disbursedAmount_bt`) AS disbursedAmount_bt FROM
        `mfn_month_end_process_loans` WHERE genderTypeId=2 AND `date`>='$startDate' AND `date`<='$endDate' AND `productIdFk` IN(7,8,9,10) AND `branchIdFk`='$requestedBranch'");
    }

    return $directMemberAdmissionMale;
  }

  public function directFemaleCurrentDisburementAmountQuery($requestedBranch, $startDate, $endDate) {
    $directMemberAdmissionMale = array();

    if ($requestedBranch == 'All') {
      $directMemberAdmissionMale = DB::table('mfn_month_end_process_loans')
        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_loans.date', '>=', $startDate], ['mfn_month_end_process_loans.date', '<=', $endDate], ['mfn_loans_product.productCategoryId', '=', 2], ['mfn_month_end_process_loans.genderTypeId', '=', 2]])
        ->sum('mfn_month_end_process_loans.disbursedAmount');
    }
    else {
      $directMemberAdmissionMale = DB::table('mfn_month_end_process_loans')
        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_loans.date', '>=', $startDate], ['mfn_month_end_process_loans.date', '<=', $endDate], ['mfn_loans_product.productCategoryId', '=', 2], ['mfn_month_end_process_loans.genderTypeId', '=', 2], ['mfn_month_end_process_loans.branchIdFk', $requestedBranch]])
        ->sum('mfn_month_end_process_loans.disbursedAmount');
    }

    return $directMemberAdmissionMale;
  }

  public function migratedCummulativeLoanDisbursementQuery($requestedBranch, $startDate, $endDate) {
    $migratedCummulativeLoanDisbursement = array();

    if ($requestedBranch == 'All') {
      $migratedCummulativeLoanDisbursement = DB::select("SELECT SUM(`disbursedAmount_bt`) AS disbursedAmount_bt FROM
        `mfn_month_end_process_loans` WHERE `date`<='$endDate' AND `productIdFk` IN(7,8,9,10)");
    }
    else {
      $migratedCummulativeLoanDisbursement = DB::select("SELECT SUM(`disbursedAmount_bt`) AS disbursedAmount_bt FROM
        `mfn_month_end_process_loans` WHERE `date`<='$endDate' AND `productIdFk` IN(7,8,9,10) AND `branchIdFk`='$requestedBranch'");
    }

    return $migratedCummulativeLoanDisbursement;
  }

  public function cummulativeLoanDisbursementQuery($requestedBranch, $startDate, $endDate) {
    $cummulativeLoanDisbursement = array();

    // dd($endDate);

    if ($requestedBranch == 'All') {
      $cummulativeLoanDisbursement = DB::table('mfn_month_end_process_loans')
        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_loans.date', '<=', $endDate], ['mfn_loans_product.productCategoryId', '=', 2]])
        ->sum('mfn_month_end_process_loans.disbursedAmount');
    }
    else {
      $cummulativeLoanDisbursement = DB::table('mfn_month_end_process_loans')
        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_loans.date', '<=', $endDate], ['mfn_loans_product.productCategoryId', '=', 2], ['mfn_month_end_process_loans.branchIdFk', $requestedBranch]])
        ->sum('mfn_month_end_process_loans.disbursedAmount');
    }

    // dd();

    return $cummulativeLoanDisbursement;
  }

  public function migratedCurrentOutstandingAmountQuery($requestedBranch, $startDate, $endDate) {
    $migratedCurrentOutstandingAmount = array();

    if ($requestedBranch == 'All') {
      $migratedCurrentOutstandingAmount = DB::select("SELECT SUM(`closingOutstandingAmountWithServicesCharge`) AS closingOutstandingAmountWithServicesCharge FROM
        `mfn_month_end_process_loans` WHERE `date`='$endDate'");
    }
    else {
      $migratedCurrentOutstandingAmount = DB::select("SELECT SUM(`closingOutstandingAmountWithServicesCharge`) AS closingOutstandingAmountWithServicesCharge FROM
        `mfn_month_end_process_loans` WHERE `date`='$endDate' AND `branchIdFk`='$requestedBranch'");
    }

    return $migratedCurrentOutstandingAmount;
  }

  public function currentOutstandingAmountQuery($requestedBranch, $startDate, $endDate) {
    $currentOutstandingAmount = array();

    if ($requestedBranch == 'All') {
      $currentOutstandingAmount = DB::table('mfn_month_end_process_loans')
        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_loans.date', '=', $endDate], ['mfn_loans_product.productCategoryId', '=', 2]])
        ->sum('mfn_month_end_process_loans.closingOutstandingAmountWithServicesCharge');
    }
    else {
      $currentOutstandingAmount = DB::table('mfn_month_end_process_loans')
        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
        ->where([['mfn_month_end_process_loans.date', '=', $endDate], ['mfn_loans_product.productCategoryId', '=', 2], ['mfn_month_end_process_loans.branchIdFk', $requestedBranch]])
        ->sum('mfn_month_end_process_loans.closingOutstandingAmountWithServicesCharge');
    }

    // dd($currentOutstandingAmount);

    return $currentOutstandingAmount;
  }

  // MAIN(getReport) METHOD TO CALL............................................................................................
  public function getReport(Request $request){
    // REQUESTED DATA VARIABLES................................................................................................
    $requestedBranch = $request->searchBranch;
    $requestedYear = $request->searchYear;
    $requestedmonth = $request->searchMonth;
    $requestedType = $request->searchProCat;

    // DECLARED ARRAY..........................................................................................................
    $branchInfos = array();

    $directMemberAdmissionMale = array();
    $migratedMaleMemberAdmission = array();
    $directMemberAdmissionFemale = array();
    $migratedFemaleMemberAdmission = array();

    $migratedMaleLoanee = array();
    $directMaleLoanee = array();
    $migratedFemaleLoanee = array();
    $directFemaleLoanee = array();

    $migratedMaleCurrentLoanee = array();
    $directMaleCurrentLoanee = array();
    $migratedFemaleCurrentLoanee = array();
    $directFemaleCurrentLoanee = array();

    $migratedMaleCurrentDisburementAmount = array();
    $directMaleCurrentDisburementAmount = array();
    $migratedFemaleCurrentDisburementAmount = array();
    $directFemaleCurrentDisburementAmount = array();

    $migratedCummulativeLoanDisbursement = array();
    $cummulativeLoanDisbursement = array();

    $migratedCurrentOutstandingAmount = array();
    $currentOutstandingAmount = array();

    // CREATED DATE............................................................................................................
    // $endDate = Carbon::parse('01-'.$requestedmonth.'-'.$requestedYear)->endOfMonth()->format('Y-m-d');
    $endDate = date("Y-m-t", strtotime('01-'.$requestedmonth.'-'.$requestedYear));
    $crateDate = date_create("01-$requestedmonth-$requestedYear");
    $startDate = date_format($crateDate,"Y-m-d");

    // USE OF METHOD'S AND QUERY TO CALL.......................................................................................
    $branchInfos = $this->branchQuery($requestedBranch);
    // dd($branchInfos);

    $directMemberAdmissionMale = $this->directMaleMemberAdmissionQuery($requestedBranch, $startDate, $endDate);

    $migratedMaleMemberAdmission = $this->migratedMaleMemberAdmissionQuery($requestedBranch, $startDate, $endDate);

    $directMemberAdmissionFemale = $this->directFemaleMemberAdmissionQuery($requestedBranch, $startDate, $endDate);

    $migratedFemaleMemberAdmission = $this->migratedFemaleMemberAdmissionQuery($requestedBranch, $startDate, $endDate);

    $migratedMaleLoanee = $this->migratedMaleLoaneeQuery($requestedBranch, $startDate, $endDate);

    $directMaleLoanee = $this->directMaleLoaneeQuery($requestedBranch, $startDate, $endDate);

    $migratedFemaleLoanee = $this->migratedFemaleLoaneeQuery($requestedBranch, $startDate, $endDate);

    $directFemaleLoanee = $this->directFemaleLoaneeQuery($requestedBranch, $startDate, $endDate);

    $migratedMaleCurrentLoanee = $this->migratedMaleCurrentLoaneeQuery($requestedBranch, $startDate, $endDate);

    $directMaleCurrentLoanee = $this->directMaleCurrentLoaneeQuery($requestedBranch, $startDate, $endDate);

    $migratedFemaleCurrentLoanee = $this->migratedFemaleCurrentLoaneeQuery($requestedBranch, $startDate, $endDate);

    $directFemaleCurrentLoanee = $this->directFemaleCurrentLoaneeQuery($requestedBranch, $startDate, $endDate);

    $migratedMaleCurrentDisburementAmount = $this->migratedMaleCurrentDisburementAmountQuery($requestedBranch, $startDate, $endDate);

    $directMaleCurrentDisburementAmount = $this->directMaleCurrentDisburementAmountQuery($requestedBranch, $startDate, $endDate);

    $migratedFemaleCurrentDisburementAmount = $this->migratedFemaleCurrentDisburementAmountQuery($requestedBranch, $startDate, $endDate);

    $directFemaleCurrentDisburementAmount = $this->directFemaleCurrentDisburementAmountQuery($requestedBranch, $startDate, $endDate);

    $migratedCummulativeLoanDisbursement = $this->migratedCummulativeLoanDisbursementQuery($requestedBranch, $startDate, $endDate);

    $cummulativeLoanDisbursement = $this->cummulativeLoanDisbursementQuery($requestedBranch, $startDate, $endDate);

    $migratedCurrentOutstandingAmount = $this->migratedCurrentOutstandingAmountQuery($requestedBranch, $startDate, $endDate);

    $currentOutstandingAmount = $this->currentOutstandingAmountQuery($requestedBranch, $startDate, $endDate);

    // dd($currentOutstandingAmount);
    // WILL START FROM HERE.....................................
    // $directMemberAdmissionMale = DB::table('mfn_month_end_process_members')
    //   ->where([['genderTypeId'], ['date', '>=', $startDate], ['date', '<=', ])

    // dd($currentOutstandingAmount);

    if ($requestedBranch == 'All') {
      if ($requestedType == 1) {
        // code...
      }
      elseif ($requestedType == 2) {
        // code...
      }
    }
    else {
      if ($requestedType == 1) {
        // code...
      }
      elseif ($requestedType == 2) {
        // code...
      }
    }

    // return 'OK!';
    return view('microfin.reports.agrosorReportsView.monthlyStatementAgroshorReportsViews.monthlyStatementAgroshorReportsTable', compact('branchInfos',
      'requestedBranch', 'startDate', 'endDate', 'directMemberAdmissionMale', 'migratedMaleMemberAdmission', 'migratedFemaleMemberAdmission', 'directMemberAdmissionFemale',
      'directMaleLoanee', 'migratedMaleLoanee', 'migratedFemaleLoanee', 'directFemaleLoanee', 'migratedMaleCurrentLoanee', 'directMaleCurrentLoanee', 'migratedFemaleCurrentLoanee',
      'directFemaleCurrentLoanee', 'migratedMaleCurrentDisburementAmount', 'directMaleCurrentDisburementAmount', 'migratedFemaleCurrentDisburementAmount', 'directFemaleCurrentDisburementAmount',
      'migratedCummulativeLoanDisbursement', 'cummulativeLoanDisbursement', 'migratedCurrentOutstandingAmount', 'currentOutstandingAmount'));
  }


}
