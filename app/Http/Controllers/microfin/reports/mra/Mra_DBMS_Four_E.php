<?php

namespace App\Http\Controllers\microfin\reports\mra;

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
class Mra_DBMS_Four_E extends Controller
{
  public function getBranchName(){
    $BranchDatas = array();

    $BranchDatas = DB::table('gnr_branch')->get();

    $FundingOrganization = DB::table('mfn_funding_organization')->get();

    return view('microfin.reports.mra.dbms4E.dbms4Eform', compact('BranchDatas', 'FundingOrganization'));

  }

  // Methods to calculate....................................................................................


  // Main method for calculation.............................................................................
  public function getReport(Request $request){
    // Requested data variables.....
    $requestedBranch = $request->searchBranch;
    $requestPeriod = $request->searchPeriod;
    $requestedFundingOrganization = $request->FundingOrganization;

    // DECLARED VARIABLES....................................................................................
    $uptoFiveKdisbursedNumberF = 0;
    $FiveKToUptoTenKdisbursedNumberF = 0;
    $TenKToUptoThirtyKdisbursedNumberF = 0;
    $ThirtyKToUptoFiftyKdisbursedNumberF = 0;
    $FiftyKToOneLakhdisbursedNumberF = 0;
    $OneLakhToThreeLakhdisbursedNumberF = 0;
    $ThreeLakhToMoredisbursedNumberF = 0;

    $uptoFiveKdisbursedNumberM = 0;
    $FiveKToUptoTenKdisbursedNumberM = 0;
    $TenKToUptoThirtyKdisbursedNumberM = 0;
    $ThirtyKToUptoFiftyKdisbursedNumberM = 0;
    $FiftyKToOneLakhdisbursedNumberM = 0;
    $OneLakhToThreeLakhdisbursedNumberM = 0;
    $ThreeLakhToMoredisbursedNumberM = 0;

    $uptoFiveKdisbursedAmountF = 0;
    $FiveKToUptoTenKdisbursedAmountF = 0;
    $TenKToUptoThirtyKdisbursedAmountF = 0;
    $ThirtyKToUptoFiftyKdisbursedAmountF = 0;
    $FiftyKToOneLakhdisbursedAmountF = 0;
    $OneLakhToThreeLakhdisbursedAmountF = 0;
    $ThreeLakhToMoredisbursedAmountF = 0;

    $uptoFiveKdisbursedAmountM = 0;
    $FiveKToUptoTenKdisbursedAmountM = 0;
    $TenKToUptoThirtyKdisbursedAmountM = 0;
    $ThirtyKToUptoFiftyKdisbursedAmountM = 0;
    $FiftyKToOneLakhdisbursedAmountM = 0;
    $OneLakhToThreeLakhdisbursedAmountM = 0;
    $ThreeLakhToMoredisbursedAmountM = 0;

    $uptoFiveKOutstandingF = 0;
    $FiveKToUptoTenKOutstandingF = 0;
    $TenKToUptoThirtyKOutstandingF = 0;
    $ThirtyKToUptoFiftyKOutstandingF = 0;
    $FiftyKToOneLakhOutstandingF = 0;
    $OneLakhToThreeLakhOutstandingF = 0;
    $ThreeLakhToMoreOutstandingF = 0;

    $uptoFiveKOutstandingM = 0;
    $FiveKToUptoTenKOutstandingM = 0;
    $TenKToUptoThirtyKOutstandingM = 0;
    $ThirtyKToUptoFiftyKOutstandingM = 0;
    $FiftyKToOneLakhOutstandingM = 0;
    $OneLakhToThreeLakhOutstandingM = 0;
    $ThreeLakhToMoreOutstandingM = 0;

    $disbursedAmountF = 0;
    $disbursedAmountM = 0;
    $withdraw = 0;
    $deposite = 0;

    // DECLARED ARRAY........................................................................................

    // CALCULATION HAST STARTED..............................................................................
    list($startDate, $endDate) = explode(' to ', $requestPeriod);

    $loanId = DB::table('mfn_loan')
      ->select('id', 'loanAmount', 'totalRepayAmount')
      ->where([['softDel', '=', 0], ['disbursementDate', '<=', $endDate]])
      ->get();

      // dd($loanId);

    if ($requestedBranch == 'All') {
      // FOR ALL BRANCH......................................................................................
      $loanIdF = DB::table('mfn_loan')
        ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
        ->select('mfn_loan.id', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount')
        ->where([['mfn_loan.softDel', '=', 0], ['mfn_loan.disbursementDate', '<=', $endDate], ['mfn_member_information.gender', '=', 2]])
        ->get();

      $loanIdM = DB::table('mfn_loan')
        ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
        ->select('mfn_loan.id', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount')
        ->where([['mfn_loan.softDel', '=', 0], ['mfn_loan.disbursementDate', '<=', $endDate], ['mfn_member_information.gender', '=', 1]])
        ->get();


      // END OF IF...........................................................................................
    }
    else {
      // FOR SELECTED BRANCH.................................................................................
      $loanIdF = DB::table('mfn_loan')
        ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
        ->select('mfn_loan.id', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount')
        ->where([['mfn_loan.softDel', '=', 0], ['mfn_loan.disbursementDate', '<=', $endDate], ['mfn_loan.branchIdFk', $requestedBranch], ['mfn_member_information.gender', '=', 2]])
        ->get();

      $loanIdM = DB::table('mfn_loan')
        ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
        ->select('mfn_loan.id', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount')
        ->where([['mfn_loan.softDel', '=', 0], ['mfn_loan.disbursementDate', '<=', $endDate], ['mfn_loan.branchIdFk', $requestedBranch], ['mfn_member_information.gender', '=', 1]])
        ->get();

      // END OF ELSE.........................................................................................
    }

    $loanIdFf = $loanIdF->pluck('id');
    $loanIdMm = $loanIdM->pluck('id');

    if ($requestedFundingOrganization != 'All' and $requestedFundingOrganization != -1) {
      $loanIdF = DB::table('mfn_loan')
        ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
        ->select('mfn_loan.id', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount')
        ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
        ->whereIn('mfn_loan.id', $loanIdFf)
        ->get();

      $loanIdM = DB::table('mfn_loan')
        ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
        ->select('mfn_loan.id', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount')
        ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
        ->whereIn('mfn_loan.id', $loanIdMm)
        ->get();
    }
    elseif ($requestedFundingOrganization == -1) {
      $loanIdF = DB::table('mfn_loan')
        ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
        ->select('mfn_loan.id', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount')
        ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
        ->whereIn('mfn_loan.id', $loanIdFf)
        ->get();

      $loanIdM = DB::table('mfn_loan')
        ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
        ->select('mfn_loan.id', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount')
        ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
        ->whereIn('mfn_loan.id', $loanIdMm)
        ->get();
    }

    // dd($loanIdF, $loanIdM);

    $collection = 0;
    $collectionF = 0;
    $collectionM = 0;
    $collectionArrayF = 0;
    $collectionArrayM = 0;
    $waiversM = 0;
    $waiversF = 0;

    // CALCULATION STARTED...................................................................................
    foreach ($loanIdF as $key => $loanIdF1) {
      $disbursedAmountF = $loanIdF1->loanAmount;
      $totalRepayAmountF = $loanIdF1->totalRepayAmount;

      $loanCollectionF = DB::table('mfn_loan_collection')
        ->where([['loanIdFk', $loanIdF1->id], ['collectionDate', '<=', $endDate], ['softDel', '=', 0]])
        ->sum('principalAmount');

      $writtenOff = DB::table('mfn_loan_write_off')
        ->where([['date', '<=', $endDate], ['loanIdFk', $loanIdF1->id], ['softDel', '=', 0]])
        ->sum('principalAmount');

      $waivers = DB::table('mfn_loan_waivers')
        ->where([['date', '<=', $endDate], ['loanIdFk', $loanIdF1->id]])
        ->sum('principalAmount');

      if (($writtenOff + $waivers) > $loanCollectionF) {
        $loanCollection = $writtenOff + $waivers + $loanCollectionF;
        // $loanCollection = ($writtenOff + $waivers);
      }
      else {
        $loanCollection = $loanCollectionF - ($writtenOff + $waivers);
      }

      $collection = $collection + $loanCollection;
      $collectionF = $collectionF + $loanCollection;

      if ($disbursedAmountF <= 5000) {
        $uptoFiveKdisbursedNumberF = $uptoFiveKdisbursedNumberF + 1;
        $uptoFiveKdisbursedAmountF  = $uptoFiveKdisbursedAmountF  + $disbursedAmountF;
        $uptoFiveKOutstandingF = $uptoFiveKOutstandingF + ($disbursedAmountF-$loanCollection);
      }
      elseif ($disbursedAmountF >= 5001 and $disbursedAmountF <= 10000) {
        $FiveKToUptoTenKdisbursedNumberF = $FiveKToUptoTenKdisbursedNumberF + 1;
        $FiveKToUptoTenKdisbursedAmountF  = $FiveKToUptoTenKdisbursedAmountF  + $disbursedAmountF;
        $FiveKToUptoTenKOutstandingF = $FiveKToUptoTenKOutstandingF + ($disbursedAmountF-$loanCollection);
      }
      elseif ($disbursedAmountF >= 10001 and $disbursedAmountF <= 30000) {
        $TenKToUptoThirtyKdisbursedNumberF = $TenKToUptoThirtyKdisbursedNumberF + 1;
        $TenKToUptoThirtyKdisbursedAmountF  = $TenKToUptoThirtyKdisbursedAmountF  + $disbursedAmountF;
        $TenKToUptoThirtyKOutstandingF = $TenKToUptoThirtyKOutstandingF + ($disbursedAmountF-$loanCollection);
      }
      elseif ($disbursedAmountF >= 30001 and $disbursedAmountF <= 50000) {
        $ThirtyKToUptoFiftyKdisbursedNumberF = $ThirtyKToUptoFiftyKdisbursedNumberF + 1;
        $ThirtyKToUptoFiftyKdisbursedAmountF  = $ThirtyKToUptoFiftyKdisbursedAmountF  + $disbursedAmountF;
        $ThirtyKToUptoFiftyKOutstandingF = $ThirtyKToUptoFiftyKOutstandingF + ($disbursedAmountF-$loanCollection);
      }
      elseif ($disbursedAmountF >= 50001 and $disbursedAmountF <= 100000) {
        $FiftyKToOneLakhdisbursedNumberF = $FiftyKToOneLakhdisbursedNumberF + 1;
        $FiftyKToOneLakhdisbursedAmountF  = $FiftyKToOneLakhdisbursedAmountF  + $disbursedAmountF;
        $FiftyKToOneLakhOutstandingF = $FiftyKToOneLakhOutstandingF + ($disbursedAmountF-$loanCollection);
      }
      elseif ($disbursedAmountF >= 100001 and $disbursedAmountF <= 300000) {
        $OneLakhToThreeLakhdisbursedNumberF = $OneLakhToThreeLakhdisbursedNumberF + 1;
        $OneLakhToThreeLakhdisbursedAmountF  = $OneLakhToThreeLakhdisbursedAmountF  + $disbursedAmountF;
        $OneLakhToThreeLakhOutstandingF = $OneLakhToThreeLakhOutstandingF + ($disbursedAmountF-$loanCollection);
      }
      elseif ($disbursedAmountF >= 300001) {
        $ThreeLakhToMoredisbursedNumberF = $ThreeLakhToMoredisbursedNumberF + 1;
        $ThreeLakhToMoredisbursedAmountF  = $ThreeLakhToMoredisbursedAmountF  + $disbursedAmountF;
        $ThreeLakhToMoreOutstandingF = $ThreeLakhToMoreOutstandingF + ($disbursedAmountF-$loanCollection);
      }
    }

    foreach ($loanIdM as $key => $loanIdM1) {
      $disbursedAmountM = $loanIdM1->loanAmount;
      $totalRepayAmountM = $loanIdM1->totalRepayAmount;

      $loanCollectionM = DB::table('mfn_loan_collection')
        ->where([['loanIdFk', $loanIdM1->id], ['collectionDate', '<=', $endDate], ['softDel', '=', 0]])
        ->sum('principalAmount');

      $writtenOff = DB::table('mfn_loan_write_off')
        ->where([['date', '<=', $endDate], ['loanIdFk', $loanIdM1->id], ['softDel', '=', 0]])
        ->sum('principalAmount');

      $waivers = DB::table('mfn_loan_waivers')
        ->where([['date', '<=', $endDate], ['loanIdFk', $loanIdM1->id]])
        ->sum('principalAmount');

      if (($writtenOff + $waivers) > $loanCollectionM) {
        $loanCollection = $writtenOff + $waivers + $loanCollectionM;
        // $loanCollection = ($writtenOff + $waivers);
      }
      else {
        $loanCollection = $loanCollectionM - ($writtenOff + $waivers);
      }

      $collection = $collection + $loanCollection;
      $collectionM = $collectionM + $loanCollection;

      if ($disbursedAmountM <= 5000) {
        $uptoFiveKdisbursedNumberM = $uptoFiveKdisbursedNumberM + 1;
        $uptoFiveKdisbursedAmountM  = $uptoFiveKdisbursedAmountM  + $disbursedAmountM;
        $uptoFiveKOutstandingM  = $uptoFiveKOutstandingM  + ($disbursedAmountM-$loanCollection);
      }
      elseif ($disbursedAmountM >= 5001 and $disbursedAmountM <= 10000) {
        $FiveKToUptoTenKdisbursedNumberM = $FiveKToUptoTenKdisbursedNumberM + 1;
        $FiveKToUptoTenKdisbursedAmountM  = $FiveKToUptoTenKdisbursedAmountM  + $disbursedAmountM;
        $FiveKToUptoTenKOutstandingM  = $FiveKToUptoTenKOutstandingM  + ($disbursedAmountM-$loanCollection);
      }
      elseif ($disbursedAmountM >= 10001 and $disbursedAmountM <= 30000) {
        $TenKToUptoThirtyKdisbursedNumberM = $TenKToUptoThirtyKdisbursedNumberM + 1;
        $TenKToUptoThirtyKdisbursedAmountM  = $TenKToUptoThirtyKdisbursedAmountM  + $disbursedAmountM;
        $TenKToUptoThirtyKOutstandingM  = $TenKToUptoThirtyKOutstandingM  + ($disbursedAmountM-$loanCollection);
      }
      elseif ($disbursedAmountM >= 30001 and $disbursedAmountM <= 50000) {
        $ThirtyKToUptoFiftyKdisbursedNumberM = $ThirtyKToUptoFiftyKdisbursedNumberM + 1;
        $ThirtyKToUptoFiftyKdisbursedAmountM  = $ThirtyKToUptoFiftyKdisbursedAmountM  + $disbursedAmountM;
        $ThirtyKToUptoFiftyKOutstandingM  = $ThirtyKToUptoFiftyKOutstandingM  + ($disbursedAmountM-$loanCollection);
      }
      elseif ($disbursedAmountM >= 50001 and $disbursedAmountM <= 100000) {
        $FiftyKToOneLakhdisbursedNumberM = $FiftyKToOneLakhdisbursedNumberM + 1;
        $FiftyKToOneLakhdisbursedAmountM  = $FiftyKToOneLakhdisbursedAmountM  + $disbursedAmountM;
        $FiftyKToOneLakhOutstandingM  = $FiftyKToOneLakhOutstandingM  + ($disbursedAmountM-$loanCollection);
      }
      elseif ($disbursedAmountM >= 100001 and $disbursedAmountM <= 300000) {
        $OneLakhToThreeLakhdisbursedNumberM = $OneLakhToThreeLakhdisbursedNumberM + 1;
        $OneLakhToThreeLakhdisbursedAmountM  = $OneLakhToThreeLakhdisbursedAmountM  + $disbursedAmountM;
        $OneLakhToThreeLakhOutstandingM  = $OneLakhToThreeLakhOutstandingM  + ($disbursedAmountM-$loanCollection);
      }
      elseif ($disbursedAmountM >= 300001) {
        $ThreeLakhToMoredisbursedNumberM = $ThreeLakhToMoredisbursedNumberM + 1;
        $ThreeLakhToMoredisbursedAmountM  = $ThreeLakhToMoredisbursedAmountM  + $disbursedAmountM;
        $ThreeLakhToMoreOutstandingM  = $ThreeLakhToMoreOutstandingM  + ($disbursedAmountM-$loanCollection);
      }
    }

    $loanProductCategory = DB::table('mfn_loans_product_category')->select('id', 'name')->get();



    // dd($collection, $collectionM, $collectionF);

    return view('microfin.reports.mra.dbms4E.dbms4Ereport', compact('uptoFiveKdisbursedNumberF', 'FiveKToUptoTenKdisbursedNumberF', 'TenKToUptoThirtyKdisbursedNumberF',
      'ThirtyKToUptoFiftyKdisbursedNumberF', 'FiftyKToOneLakhdisbursedNumberF', 'OneLakhToThreeLakhdisbursedNumberF', 'ThreeLakhToMoredisbursedNumberF', 'uptoFiveKdisbursedNumberM',
      'FiveKToUptoTenKdisbursedNumberM', 'TenKToUptoThirtyKdisbursedNumberM', 'ThirtyKToUptoFiftyKdisbursedNumberM', 'FiftyKToOneLakhdisbursedNumberM', 'OneLakhToThreeLakhdisbursedNumberM',
      'ThreeLakhToMoredisbursedNumberM', 'uptoFiveKdisbursedAmountF', 'FiveKToUptoTenKdisbursedAmountF', 'TenKToUptoThirtyKdisbursedAmountF', 'ThirtyKToUptoFiftyKdisbursedAmountF',
      'FiftyKToOneLakhdisbursedAmountF', 'OneLakhToThreeLakhdisbursedAmountF', 'ThreeLakhToMoredisbursedAmountF', 'uptoFiveKdisbursedAmountM', 'FiveKToUptoTenKdisbursedAmountM',
      'TenKToUptoThirtyKdisbursedAmountM', 'ThirtyKToUptoFiftyKdisbursedAmountM', 'FiftyKToOneLakhdisbursedAmountM', 'OneLakhToThreeLakhdisbursedAmountM', 'ThreeLakhToMoredisbursedAmountM',
      'uptoFiveKOutstandingF', 'FiveKToUptoTenKOutstandingF', 'TenKToUptoThirtyKOutstandingF', 'ThirtyKToUptoFiftyKOutstandingF', 'FiftyKToOneLakhOutstandingF', 'OneLakhToThreeLakhOutstandingF',
      'ThreeLakhToMoreOutstandingF', 'uptoFiveKOutstandingM', 'FiveKToUptoTenKOutstandingM', 'TenKToUptoThirtyKOutstandingM', 'TenKToUptoThirtyKOutstandingM', 'ThirtyKToUptoFiftyKOutstandingM',
      'FiftyKToOneLakhOutstandingM', 'OneLakhToThreeLakhOutstandingM', 'ThreeLakhToMoreOutstandingM', 'collection', 'collectionArrayM', 'collectionArrayF', 'loanProductCategory', 'endDate', 'startDate',
      'requestedBranch', 'requestedFundingOrganization', 'requestPeriod'));
  }

}
