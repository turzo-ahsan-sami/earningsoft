<?php

namespace App\Http\Controllers\microfin\reports\mra;

use App\Http\Controllers\Controller;
use DB;
use App\Http\Requests;
use Response;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
use App\Http\Controllers\microfin\MicroFin;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App;
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

/* START OF FOUR A CONTROLLER METHODS................................................................................. */
class Mra_DBMS_Four_A_Controller extends Controller
{
  public function getBranchName(){
    //$BranchDatas = array();

   // $BranchDatas = DB::table('gnr_branch')->get();
    $userBranchId = Auth::user()->branchId;
    $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

    if ($userBranchId==1) {
      //$BranchDatas = MicroFin::getBranchList();
      $BranchDatas = DB::table('gnr_branch')->get();
      //dd($BranchDatas);

    }
    else{
      $BranchDatas = DB::table('gnr_branch')
      ->whereIn('id',$branchIdArray )
      ->orderBy('branchCode')
      //->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'gnr_branch.id')
      //->pluck('nameWithCode', 'id')
      ->get();
          //$BranchDatas = array();
      //dd($BranchDatas);
    }

    $FundingOrganization = DB::table('mfn_funding_organization')->get();

    return view('microfin.reports.mra.dbms4A.Mra_DBMS_Four_A_Form', compact('BranchDatas','branchIdArray','FundingOrganization'));

  }




  public function getBranchName_old(){
    $BranchDatas = array();

    $BranchDatas = DB::table('gnr_branch')->get();

    $FundingOrganization = DB::table('mfn_funding_organization')->get();

    return view('microfin.reports.mra.dbms4A.Mra_DBMS_Four_A_Form', compact('BranchDatas', 'FundingOrganization'));

  }

  // Methods to calculate....................................................................................


  // Main method for calculation.............................................................................
  public function getReport(Request $request){
    // Requested data variables.....
    $requestedBranch = $request->searchBranch;
    $requestedPeriod = $request->searchPeriod;
    $requestedFundingOrganization = $request->FundingOrganization;
      //new branch condition user wise//
    $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
    $requestedServiceCharge = $request->searchServiceCharge;
    $requestedZeroORnot = $request->searchZeroORnot;

    // DATE CREATION.........................................................................................

    list($startDate, $endDate) = explode(' to ', $requestedPeriod);

    // DATE CREATION.........................................................................................

    // CALCULATION FOR PURPOSE CATEGORY, PURPOSE AND SUB-PURPOSE.............................................

    $loanSubPurposeId = DB::table('mfn_loan')
    ->select('loanSubPurposeIdFk')
    ->where('disbursementDate', '<=', $endDate)
     //new branch condition user wise//
    ->whereIn('branchIdFk', $branchIdArray)
    ->groupBy('loanSubPurposeIdFk')
    ->pluck('loanSubPurposeIdFk')
    ->toArray();
    //dd($loanSubPurposeId);

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

    // dd($loanPurposeCategoryInfos, $loanPurposeCategory, $loanSubPurposeId);

    // CALCULATION FOR PURPOSE CATEGORY, PURPOSE AND SUB-PURPOSE.............................................

    return view('microfin.reports.mra.dbms4A.Mra_DBMS_Four_A_Report', compact('requestedPeriod', 'loanPurposeCategoryInfos', 'loanPurposeCategory', 'loanPurposeId',
      'requestedBranch', 'startDate', 'endDate', 'requestedFundingOrganization', 'loanSubPurposeId','requestedServiceCharge', 'branchIdArray', 'requestedZeroORnot'));
  }
  /* END OF FOUR A CONTROLLER METHODS................................................................................. */

  /* START OF FOUR B CONTROLLER METHODS................................................................................. */
  public function getFourBFilter(){
    // $BranchDatas = array();

    // $BranchDatas = DB::table('gnr_branch')->get();
   $userBranchId = Auth::user()->branchId;
   $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

   if ($userBranchId==1) {
      //$BranchDatas = MicroFin::getBranchList();
    $BranchDatas = DB::table('gnr_branch')->get();
      //dd($BranchDatas);

  }
  else{
    $BranchDatas = DB::table('gnr_branch')
    ->whereIn('id',$branchIdArray )
    ->orderBy('branchCode')
      //->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'gnr_branch.id')
      //->pluck('nameWithCode', 'id')
    ->get();
          //$BranchDatas = array();
      //dd($BranchDatas);
  }

  $FundingOrganization = DB::table('mfn_funding_organization')->get();

  return view('microfin.reports.mra.dbms4B.Mra_DBMS_Four_B_Form', compact('BranchDatas', 'branchIdArray','FundingOrganization'));

}



public function getFourBFilter_old(){
  $BranchDatas = array();

  $BranchDatas = DB::table('gnr_branch')->get();

  $FundingOrganization = DB::table('mfn_funding_organization')->get();

  return view('microfin.reports.mra.dbms4B.Mra_DBMS_Four_B_Form', compact('BranchDatas', 'FundingOrganization'));

}



public function getFourBReport(Request $request){
    // Requested data variables.....
  $requestedBranch = $request->searchBranch;
  $requestedPeriod = $request->searchPeriod;
  $requestedFundingOrganization = $request->FundingOrganization;
  $requestedServiceCharge = $request->searchServiceCharge;
  $requestedZeroORnot = $request->searchZeroORnot;

    // DATE CREATION.........................................................................................

  list($startDate, $endDate) = explode(' to ', $requestedPeriod);

    // DATE CREATION.........................................................................................

    // CALCULATION FOR PURPOSE CATEGORY, PURPOSE AND SUB-PURPOSE.............................................

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

    // dd($loanPurposeCategoryInfos, $loanPurposeCategory, $loanSubPurposeId);

    // CALCULATION FOR PURPOSE CATEGORY, PURPOSE AND SUB-PURPOSE.............................................

  return view('microfin.reports.mra.dbms4B.Mra_DBMS_Four_B_Report', compact('requestedPeriod', 'loanPurposeCategoryInfos', 'loanPurposeCategory', 'loanPurposeId',
    'requestedBranch', 'startDate', 'endDate', 'requestedFundingOrganization', 'loanSubPurposeId', 'requestedServiceCharge', 'requestedZeroORnot'));
}
/* END OF FOUR B CONTROLLER METHODS................................................................................. */

/* START OF FOUR C CONTROLLER METHODS................................................................................. */
public function getFourCFilter(){
  $BranchDatas = array();

  $BranchDatas = DB::table('gnr_branch')->get();

  $FundingOrganization = DB::table('mfn_funding_organization')->get();

  return view('microfin.reports.mra.dbms4C.Mra_DBMS_Four_C_Form', compact('BranchDatas', 'FundingOrganization'));

}



public function getFourCReport(Request $request){
    // Requested data variables.....
  $requestedBranch = $request->searchBranch;
  $requestedPeriod = $request->searchPeriod;
  $requestedFundingOrganization = $request->FundingOrganization;
  $requestedServiceCharge = $request->searchServiceCharge;
  $requestedZeroORnot = $request->searchZeroORnot;

    // DATE CREATION.........................................................................................

  list($startDate, $endDate) = explode(' to ', $requestedPeriod);

    // DATE CREATION.........................................................................................

    // CALCULATION FOR PURPOSE CATEGORY, PURPOSE AND SUB-PURPOSE.............................................

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

    // dd($loanPurposeCategoryInfos, $loanPurposeCategory, $loanSubPurposeId);

    // CALCULATION FOR PURPOSE CATEGORY, PURPOSE AND SUB-PURPOSE.............................................

  return view('microfin.reports.mra.dbms4C.Mra_DBMS_Four_C_Report', compact('requestedPeriod', 'loanPurposeCategoryInfos', 'loanPurposeCategory', 'loanPurposeId',
    'requestedBranch', 'startDate', 'endDate', 'requestedFundingOrganization', 'loanSubPurposeId', 'requestedServiceCharge', 'requestedZeroORnot'));
}
/* END OF FOUR C CONTROLLER METHODS................................................................................. */

/* START OF FOUR D CONTROLLER METHODS................................................................................. */
public function getFourDFilter(){
  $BranchDatas = array();

  $BranchDatas = DB::table('gnr_branch')->get();

  $FundingOrganization = DB::table('mfn_funding_organization')->get();

  return view('microfin.reports.mra.dbms4D.Mra_DBMS_Four_D_Form', compact('BranchDatas', 'FundingOrganization'));

}



public function getFourDReport(Request $request){
    // Requested data variables.....
  $requestedBranch = $request->searchBranch;
  $requestedPeriod = $request->searchPeriod;
  $requestedFundingOrganization = $request->FundingOrganization;
  $requestedServiceCharge = $request->searchServiceCharge;
  $requestedZeroORnot = $request->searchZeroORnot;

    // DATE CREATION.........................................................................................

  list($startDate, $endDate) = explode(' to ', $requestedPeriod);

    // DATE CREATION.........................................................................................

    // CALCULATION FOR PURPOSE CATEGORY, PURPOSE AND SUB-PURPOSE.............................................

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

    // dd($loanPurposeCategoryInfos, $loanPurposeCategory, $loanSubPurposeId);

    // CALCULATION FOR PURPOSE CATEGORY, PURPOSE AND SUB-PURPOSE.............................................

  return view('microfin.reports.mra.dbms4D.Mra_DBMS_Four_D_Report', compact('requestedPeriod', 'loanPurposeCategoryInfos', 'loanPurposeCategory', 'loanPurposeId',
    'requestedBranch', 'startDate', 'endDate', 'requestedFundingOrganization', 'loanSubPurposeId', 'requestedServiceCharge', 'requestedZeroORnot'));
}
/* END OF FOUR D CONTROLLER METHODS................................................................................. */

}
