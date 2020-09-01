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
class Mra_Mfi_Three_A_Controller extends Controller
{
  public function getBranchName(){
    $userBranchId = Auth::user()->branchId;
    $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
    //$BranchDatas = array();
    //dd($userBranchId);

    //$BranchDatas = DB::table('gnr_branch')->get();
    //dd($BranchDatas);
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

    return view('microfin.reports.mra.mra_mfi_three_A_views.mra_mfi_three_A_form', compact('BranchDatas','branchIdArray', 'FundingOrganization'));

  }



  public function getBranchName_old(){
    $BranchDatas = array();

    $BranchDatas = DB::table('gnr_branch')->get();

    $FundingOrganization = DB::table('mfn_funding_organization')->get();

    return view('microfin.reports.mra.mra_mfi_three_A_views.mra_mfi_three_A_form', compact('BranchDatas', 'FundingOrganization'));

  }


  // Methods to calculate....................................................................................


  // Main method for calculation.............................................................................
  public function getReport(Request $request){
    // Requested data variables.....
    $requestedBranch = $request->searchBranch;

    $requestPeriod = $request->searchPeriod;
    $requestedFundingOrganization = $request->FundingOrganization;
    //new branch condition user wise//
    $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
    //dd($requestedBranch);

    // DECLARED VARIABLES....................................................................................
    $startDate = '';
    $endDate = '';
    // $compulsoryLoaneeMembersTotal = 0;
    // $compulsoryNotLoaneeMembersTotal = 0;
    $compulsoryLoaneeMembersSavings = 0;

    // DECLARED ARRAY.........................................................................................
    $savingsProduct = array();
    $compulsoryLoaneeMembers = array();
    $compulsoryNotLoaneeMembers = array();
    $compulsoryLoaneeMembersSavingsTotal = array();
    $compulsoryNotLoaneeMembersSavingsTotal = array();
    $others = array();
    $loaneeMembers = array();
    $notLoaneeMembers = array();
    $compolsoryDepositMembers = array();
    $voluntaryDepositMembers = array();

    $voluntar = collect([2]);
    $term = collect([3,4]);

    // dd($requestPeriod);

    // CALCULATION HAST STARTED...............................................................................
    list($startDate, $endDate) = explode(' to ', $requestPeriod);

    // dd($requestedBranch);

    // $actualMembers = DB::select(  "SELECT COUNT(DISTINCT(memberIdFk)) AS memberIdFk FROM `mfn_savings_account` WHERE accountOpeningDate<='$endDate' AND softDel=1");

    $savingsProduct = DB::table('mfn_saving_product')
    ->select('id')
    ->get();

    if ($requestedBranch == 'All') {
      // FOR IN BETWEEN DATES..........................................
      $compolsoryDepositMembers = DB::table('mfn_savings_account')
      ->select('memberIdFk', 'id AS acId')
      ->where([['softDel', '=', 0],
        //['branchIdFk', $branchIdArray],
        ['savingsProductIdFk', '=', 1],
        ['accountOpeningDate', '<=', $endDate]])
       //new branch condition user wise//
      ->whereIn('branchIdFk',$branchIdArray )
      //dd($branchIdArray );
      ->groupBy('memberIdFk')
      ->get();

      //dd($branchIdArray);

      $voluntaryDepositMembers = DB::table('mfn_savings_account')
      ->select('memberIdFk', 'id AS acId')
      ->where([['softDel', '=', 0],
        ['accountOpeningDate', '<=', $endDate]])
      ->whereIn('savingsProductIdFk', $voluntar)
      ->groupBy('memberIdFk')
      ->get();

        // dd($voluntaryDepositMembers);

      $termDepositMembers = DB::table('mfn_savings_account')
      ->select('memberIdFk', 'id AS acId')
      ->where([['softDel', '=', 0],
        ['accountOpeningDate', '<=', $endDate]])
      ->whereIn('savingsProductIdFk', $term)
      ->groupBy('memberIdFk')
      ->get();

      $compolsoryloaneeMembersLoan = DB::table('mfn_loan')
      ->join('mfn_savings_deposit', 'mfn_loan.primaryProductIdFk', '=', 'mfn_savings_deposit.primaryProductIdFk')
      ->select('mfn_loan.memberIdFk', 'mfn_savings_deposit.accountIdFk AS acId')
      ->where([['mfn_loan.softDel', '=', 0],
        ['mfn_loan.disbursementDate', '<=', $endDate],
        ['mfn_savings_deposit.productIdFk', 1]])
      ->groupBy('mfn_loan.memberIdFk')
      ->get();

      $voluntaryloaneeMembersLoan = DB::table('mfn_loan')
      ->join('mfn_savings_deposit', 'mfn_loan.primaryProductIdFk', '=', 'mfn_savings_deposit.primaryProductIdFk')
      ->select('mfn_loan.memberIdFk', 'mfn_savings_deposit.accountIdFk AS acId')
      ->where([['mfn_loan.softDel', '=', 0],
        ['mfn_loan.disbursementDate', '<=', $endDate]])
      ->whereIn('mfn_savings_deposit.productIdFk', $voluntar)
      ->groupBy('mfn_loan.memberIdFk')
      ->get();

      $termloaneeMembersLoan = DB::table('mfn_loan')
      ->join('mfn_savings_deposit', 'mfn_loan.primaryProductIdFk', '=', 'mfn_savings_deposit.primaryProductIdFk')
      ->select('mfn_loan.memberIdFk', 'mfn_savings_deposit.accountIdFk AS acId')
      ->where([['mfn_loan.softDel', '=', 0],
        ['mfn_loan.disbursementDate', '<=', $endDate]])
      ->whereIn('mfn_savings_deposit.productIdFk', $term)
      ->groupBy('mfn_loan.memberIdFk')
      ->get();

        // dd($voluntaryDepositMembers, $voluntaryloaneeMembersLoan);
      // FOR IN BETWEEN DATES.....................................................................

      // FOR START OF THE HALF YEAR...............................................................

      $compolsoryDepositMembersSTART = DB::table('mfn_savings_account')
      ->select('memberIdFk')
      ->where([['softDel', '=', 0],
        ['savingsProductIdFk', '=', 1],
        ['accountOpeningDate', '<', $startDate]])
      ->groupBy('memberIdFk')
      ->get();

        // dd($compolsoryDepositMembersSTART);

      $voluntaryDepositMembersSTART = DB::table('mfn_savings_account')
      ->select('memberIdFk')
      ->where([['softDel', '=', 0],
        ['accountOpeningDate', '<', $startDate]])
      ->whereIn('savingsProductIdFk', $voluntar)
      ->groupBy('memberIdFk')
      ->get();

        // dd($voluntaryDepositMembers);

      $termDepositMembersSTART = DB::table('mfn_savings_account')
      ->select('memberIdFk')
      ->where([['softDel', '=', 0],
        ['accountOpeningDate', '<', $startDate]])
      ->whereIn('savingsProductIdFk', $term)
      ->groupBy('memberIdFk')
      ->get();

      $compolsoryloaneeMembersLoanSTART = DB::table('mfn_loan')
      ->join('mfn_savings_deposit', 'mfn_loan.primaryProductIdFk', '=', 'mfn_savings_deposit.primaryProductIdFk')
      ->select('mfn_loan.memberIdFk')
      ->where([['mfn_loan.softDel', '=', 0],
        ['mfn_loan.disbursementDate', '<', $startDate],
        ['mfn_savings_deposit.productIdFk', '=', 1]])
      ->groupBy('mfn_loan.memberIdFk')
      ->get();

        // dd($compolsoryloaneeMembersLoan);

      $voluntaryloaneeMembersLoanSTART = DB::table('mfn_loan')
      ->join('mfn_savings_deposit', 'mfn_loan.primaryProductIdFk', '=', 'mfn_savings_deposit.primaryProductIdFk')
      ->select('mfn_loan.memberIdFk')
      ->where([['mfn_loan.softDel', '=', 0],
        ['mfn_loan.disbursementDate', '<', $startDate]])
      ->whereIn('mfn_savings_deposit.productIdFk', $voluntar)
      ->groupBy('mfn_loan.memberIdFk')
      ->get();

      $termloaneeMembersLoanSTART = DB::table('mfn_loan')
      ->join('mfn_savings_deposit', 'mfn_loan.primaryProductIdFk', '=', 'mfn_savings_deposit.primaryProductIdFk')
      ->select('mfn_loan.memberIdFk')
      ->where([['mfn_loan.softDel', '=', 0],
        ['mfn_loan.disbursementDate', '<', $startDate]])
      ->whereIn('mfn_savings_deposit.productIdFk', $term)
      ->groupBy('mfn_loan.memberIdFk')
      ->get();

      // FOR START OF THE HALF YEAR...............................................................
    }
    else {
      // FOR IN BETWEEN DATES.....................................................................
      $compolsoryDepositMembers = DB::table('mfn_savings_account')
      ->select('memberIdFk', 'id AS acId')
      ->where([['softDel', '=', 0],
        ['branchIdFk', $requestedBranch],
        ['savingsProductIdFk', '=', 1],
        ['accountOpeningDate', '<=', $endDate]])
      ->groupBy('memberIdFk')
      ->get();

      //dd($compolsoryDepositMembers);

      $voluntaryDepositMembers = DB::table('mfn_savings_account')
      ->select('memberIdFk', 'id AS acId')
      ->where([['softDel', '=', 0],
        ['branchIdFk', $requestedBranch],
        ['accountOpeningDate', '<=', $endDate]])
      ->whereIn('savingsProductIdFk', $voluntar)
      ->groupBy('memberIdFk')
      ->get();

        // dd($voluntaryDepositMembers);

      $termDepositMembers = DB::table('mfn_savings_account')
      ->select('memberIdFk', 'id AS acId')
      ->where([['softDel', '=', 0],
        ['branchIdFk', $requestedBranch],
        ['accountOpeningDate', '<=', $endDate]])
      ->whereIn('savingsProductIdFk', $term)
      ->groupBy('memberIdFk')
      ->get();

        // dd($endDate);

      $compolsoryloaneeMembersLoan = DB::table('mfn_loan')
      ->join('mfn_savings_deposit', 'mfn_loan.primaryProductIdFk', '=', 'mfn_savings_deposit.primaryProductIdFk')
      ->select('mfn_loan.memberIdFk', 'mfn_savings_deposit.accountIdFk AS acId')
      ->where([['mfn_loan.softDel', '=', 0],
        ['mfn_loan.branchIdFk', $requestedBranch],
        ['mfn_loan.disbursementDate', '<=', $endDate],
        ['mfn_savings_deposit.productIdFk', '=', 1]])
      ->groupBy('mfn_loan.memberIdFk')
      ->get();

        // dd($compolsoryloaneeMembersLoan);

      $voluntaryloaneeMembersLoan = DB::table('mfn_loan')
      ->join('mfn_savings_deposit', 'mfn_loan.primaryProductIdFk', '=', 'mfn_savings_deposit.primaryProductIdFk')
      ->select('mfn_loan.memberIdFk', 'mfn_savings_deposit.accountIdFk AS acId')
      ->where([['mfn_loan.softDel', '=', 0],
        ['mfn_loan.branchIdFk', $requestedBranch],
        ['mfn_loan.disbursementDate', '<=', $endDate]])
      ->whereIn('mfn_savings_deposit.productIdFk', $voluntar)
      ->groupBy('mfn_loan.memberIdFk')
      ->get();

        // dd($voluntaryloaneeMembersLoan);

      $termloaneeMembersLoan = DB::table('mfn_loan')
      ->join('mfn_savings_deposit', 'mfn_loan.primaryProductIdFk', '=', 'mfn_savings_deposit.primaryProductIdFk')
      ->select('mfn_loan.memberIdFk', 'mfn_savings_deposit.accountIdFk AS acId')
      ->where([['mfn_loan.softDel', '=', 0],
        ['mfn_loan.branchIdFk', $requestedBranch],
        ['mfn_loan.disbursementDate', '<=', $endDate]])
      ->whereIn('mfn_savings_deposit.productIdFk', $term)
      ->groupBy('mfn_loan.memberIdFk')
      ->get();

        // dd($termloaneeMembersLoan);
        // FOR IN BETWEEN DATES.....................................................................

        // FOR START OF THE HALF YEAR...............................................................

      $compolsoryDepositMembersSTART = DB::table('mfn_savings_account')
      ->select('memberIdFk')
      ->where([['softDel', '=', 0],
        ['branchIdFk', $requestedBranch],
        ['savingsProductIdFk', '=', 1],
        ['accountOpeningDate', '<', $startDate]])
      ->groupBy('memberIdFk')
      ->get();

          // dd($compolsoryDepositMembers);

      $voluntaryDepositMembersSTART = DB::table('mfn_savings_account')
      ->select('memberIdFk')
      ->where([['softDel', '=', 0],
        ['branchIdFk', $requestedBranch],
        ['accountOpeningDate', '<', $startDate]])
      ->whereIn('savingsProductIdFk', $voluntar)
      ->groupBy('memberIdFk')
      ->get();

          // dd($voluntaryDepositMembers);

      $termDepositMembersSTART = DB::table('mfn_savings_account')
      ->select('memberIdFk')
      ->where([['softDel', '=', 0],
        ['branchIdFk', $requestedBranch],
        ['accountOpeningDate', '<', $startDate]])
      ->whereIn('savingsProductIdFk', $term)
      ->groupBy('memberIdFk')
      ->get();

          //dd($startDate);

      $compolsoryloaneeMembersLoanSTART = DB::table('mfn_loan')
      ->join('mfn_savings_deposit', 'mfn_loan.primaryProductIdFk', '=', 'mfn_savings_deposit.primaryProductIdFk')
      ->select('mfn_loan.memberIdFk')
      ->where([['mfn_loan.softDel', '=', 0],
        ['mfn_loan.branchIdFk', $requestedBranch],
        ['mfn_loan.disbursementDate', '<', $startDate],
        ['mfn_savings_deposit.productIdFk', '=', 1]])
      ->groupBy('mfn_loan.memberIdFk')
      ->get();

          // dd($compolsoryloaneeMembersLoan);

      $voluntaryloaneeMembersLoanSTART = DB::table('mfn_loan')
      ->join('mfn_savings_deposit', 'mfn_loan.primaryProductIdFk', '=', 'mfn_savings_deposit.primaryProductIdFk')
      ->select('mfn_loan.memberIdFk')
      ->where([['mfn_loan.softDel', '=', 0],
        ['mfn_loan.branchIdFk', $requestedBranch],
        ['mfn_loan.disbursementDate', '<', $startDate]])
      ->whereIn('mfn_savings_deposit.productIdFk', $voluntar)
      ->groupBy('mfn_loan.memberIdFk')
      ->get();

          // dd($voluntaryloaneeMembersLoan);

      $termloaneeMembersLoanSTART = DB::table('mfn_loan')
      ->join('mfn_savings_deposit', 'mfn_loan.primaryProductIdFk', '=', 'mfn_savings_deposit.primaryProductIdFk')
      ->select('mfn_loan.memberIdFk')
      ->where([['mfn_loan.softDel', '=', 0],
        ['mfn_loan.branchIdFk', $requestedBranch],
        ['mfn_loan.disbursementDate', '<', $startDate]])
      ->whereIn('mfn_savings_deposit.productIdFk', $term)
      ->groupBy('mfn_loan.memberIdFk')
      ->get();

        // FOR START OF THE HALF YEAR...............................................................
    }
    $savingsProduct = $savingsProduct->pluck('id');

    $compolsoryloaneeMembersLoan1 = $compolsoryloaneeMembersLoan->pluck('memberIdFk');

    $compolsoryloaneeMembersLoanSTART1 = $compolsoryloaneeMembersLoanSTART->pluck('memberIdFk');

    // START OF THE COMPOLSORY DEPOSIT MEMBER CALCULATION......................................................................
    $compolsoryDepositMembers1 = $compolsoryDepositMembers->pluck('memberIdFk');

    $compulsoryNotLoaneeMembers = $compolsoryDepositMembers1->diff($compolsoryloaneeMembersLoan1);

    $compulsoryLoaneeMembers = $compolsoryDepositMembers1->diff($compulsoryNotLoaneeMembers);
    // dd($compulsoryLoaneeMembers, $compulsoryNotLoaneeMembers);

    $compolsoryDepositMembersSTART1 = $compolsoryDepositMembersSTART->pluck('memberIdFk');

    $compulsoryNotLoaneeMembersSTART = $compolsoryDepositMembersSTART1->diff($compolsoryloaneeMembersLoanSTART1);

    $compulsoryLoaneeMembersSTART = $compolsoryDepositMembersSTART1->diff($compulsoryNotLoaneeMembersSTART);
    // dd($compulsoryLoaneeMembersSTART, $compulsoryNotLoaneeMembersSTART);

    // FUNDING ORGANIZATION....................................................................................................

    if ($requestedFundingOrganization != 'All' and $requestedFundingOrganization != -1) {
      $compulsoryLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
      ->whereIn('mfn_member_information.id', $compulsoryLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $compulsoryNotLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
      ->whereIn('mfn_member_information.id', $compulsoryNotLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $compulsoryLoaneeMembersSTART = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
      ->whereIn('mfn_member_information.id', $compulsoryLoaneeMembersSTART)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $compulsoryNotLoaneeMembersSTART = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
      ->whereIn('mfn_member_information.id', $compulsoryNotLoaneeMembersSTART)
      ->pluck('mfn_member_information.id')
      ->toArray();
    }
    elseif ($requestedFundingOrganization == -1) {
      $compulsoryLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
      ->whereIn('mfn_member_information.id', $compulsoryLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $compulsoryNotLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
      ->whereIn('mfn_member_information.id', $compulsoryNotLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $compulsoryLoaneeMembersSTART = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
      ->whereIn('mfn_member_information.id', $compulsoryLoaneeMembersSTART)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $compulsoryNotLoaneeMembersSTART = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
      ->whereIn('mfn_member_information.id', $compulsoryNotLoaneeMembersSTART)
      ->pluck('mfn_member_information.id')
      ->toArray();
    }

    // dd($compulsoryLoaneeMembers, $compolsoryloaneeMembersLoan);

    // END OF FUNDING ORGANIZATION.............................................................................................

    // compulsory Savings Total Begining........................................................................................
    $compulsoryLoaneeMembersSavingsDepositeBeginning = DB::table('mfn_savings_deposit')
    ->where([['depositDate', '<', $startDate], ['productIdFk', '=', 1], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $compulsoryLoaneeMembersSTART)
    ->sum('amount');
      // ->get();

      // dd($compulsoryLoaneeMembersSTART);

    $compulsoryNotLoaneeMembersSavingsDepositeBeginning = DB::table('mfn_savings_deposit')
    ->where([['depositDate', '<', $startDate], ['productIdFk', '=', 1], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $compulsoryNotLoaneeMembersSTART)
    ->sum('amount');

      // dd($compulsoryLoaneeMembersSavingsDepositeBeginning, $startDate);

    $compulsoryLoaneeMembersSavingsRefundBeginning = DB::table('mfn_savings_withdraw')
    ->where([['withdrawDate', '<', $startDate], ['productIdFk', '=', 1], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $compulsoryLoaneeMembersSTART)
    ->sum('amount');

      // dd($compulsoryLoaneeMembersSTART, $compulsoryNotLoaneeMembersSTART);

    $compulsoryNotLoaneeMembersSavingsRefundBeginning = DB::table('mfn_savings_withdraw')
    ->where([['withdrawDate', '<', $startDate], ['productIdFk', '=', 1], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $compulsoryNotLoaneeMembersSTART)
    ->sum('amount');

    $compulsorySavingsInterestLoaneePaidBeginning = DB::table('mfn_savings_interest')
    ->where([['date', '<', $startDate], ['productIdFk', '=', 1]])
    ->whereIn('memberIdFk', $compulsoryLoaneeMembersSTART)
    ->sum('interestAmount');

    $compulsorySavingsInterestNotLoaneePaidBeginning = DB::table('mfn_savings_interest')
    ->where([['date', '<', $startDate], ['productIdFk', '=', 1]])
    ->whereIn('memberIdFk', $compulsoryNotLoaneeMembersSTART)
    ->sum('interestAmount');

    $compulsoryLoaneeMembersSavingsTotal = 0;
    $compulsoryNotLoaneeMembersSavingsTotal = 0;

    $compulsoryLoaneeMembersSavingsTotal = ($compulsoryLoaneeMembersSavingsDepositeBeginning + $compulsorySavingsInterestLoaneePaidBeginning) - $compulsoryLoaneeMembersSavingsRefundBeginning;

    $compulsoryNotLoaneeMembersSavingsTotal = ($compulsoryNotLoaneeMembersSavingsDepositeBeginning + $compulsorySavingsInterestNotLoaneePaidBeginning) - $compulsoryNotLoaneeMembersSavingsRefundBeginning;

      // dd($compulsoryLoaneeMembersSavingsTotal);

    //...........................................................................................................................

    // compulsory Savings Collection Total In Between............................................................................
    $compulsoryLoaneeMembersSavingsTotalInBetween = DB::table('mfn_savings_deposit')
    ->where([['depositDate', '>=', $startDate], ['depositDate', '<=', $endDate], ['productIdFk', '=', 1], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $compulsoryLoaneeMembers)
    ->sum('amount');
      // ->get();

      // dd($compulsoryLoaneeMembers, $compulsoryNotLoaneeMembers);

      // $temp = array_diff($compulsoryLoaneeMembersSTART, $compulsoryLoaneeMembers);
      //
      // $temp2 = array_diff($compulsoryLoaneeMembers,$compulsoryLoaneeMembersSTART);

      // $temp1 = $compulsoryLoaneeMembersSTART->diff($compulsoryLoaneeMembers);
      // $temp2 = $compulsoryLoaneeMembers->diff($compulsoryLoaneeMembersSTART);
      //
      // dd($temp1, $temp2, $compulsoryLoaneeMembers, $compulsoryLoaneeMembersSTART);


    $compulsoryNotLoaneeMembersSavingsTotalInBetween = DB::table('mfn_savings_deposit')
    ->where([['depositDate', '>=', $startDate], ['depositDate', '<=', $endDate], ['productIdFk', '=', 1], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $compulsoryNotLoaneeMembers)
    ->sum('amount');

      // dd($compulsoryNotLoaneeMembersSavingsTotalInBetween, $startDate, $endDate);
    //

    // compulsory Savings Refund Total In Between
    $compulsoryLoaneeMembersSavingsRefundTotalInBetween = DB::table('mfn_savings_withdraw')
    ->where([['withdrawDate', '>=', $startDate], ['withdrawDate', '<=', $endDate], ['productIdFk', '=', 1], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $compulsoryLoaneeMembers)
    ->sum('amount');

    $compulsoryNotLoaneeMembersSavingsRefundTotalInBetween = DB::table('mfn_savings_withdraw')
    ->where([['withdrawDate', '>=', $startDate], ['withdrawDate', '<=', $endDate], ['productIdFk', '=', 1], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $compulsoryNotLoaneeMembers)
    ->sum('amount');
    //

    // compulsory Savings Interest payable & paid Total In Between

    $compulsorySavingsInterestLoaneePayable = DB::table('mfn_savings_probation_interest')
    ->where([['date', '>=', $startDate], ['date', '<=', $endDate], ['productIdFk', '=', 1]])
    ->whereIn('memberIdFk', $compulsoryLoaneeMembers)
    ->sum('interestAmount');

    $compulsorySavingsInterestNotLoaneePayable = DB::table('mfn_savings_probation_interest')
    ->where([['date', '>=', $startDate], ['date', '<=', $endDate], ['productIdFk', '=', 1]])
    ->whereIn('memberIdFk', $compulsoryNotLoaneeMembers)
    ->sum('interestAmount');

    $compulsorySavingsInterestLoaneePaid = DB::table('mfn_savings_interest')
    ->where([['date', '>=', $startDate], ['date', '<=', $endDate], ['productIdFk', '=', 1]])
    ->whereIn('memberIdFk', $compulsoryLoaneeMembers)
    ->sum('interestAmount');

    $compulsorySavingsInterestNotLoaneePaid = DB::table('mfn_savings_interest')
    ->where([['date', '>=', $startDate], ['date', '<=', $endDate], ['productIdFk', '=', 1]])
    ->whereIn('memberIdFk', $compulsoryNotLoaneeMembers)
    ->sum('interestAmount');

    //

    $compulsoryMaleLoaneeMembersTotal = DB::table('mfn_member_information')
    ->where([['gender', 1], ['softDel', '=', 0]])
    ->whereIn('id', $compulsoryLoaneeMembers)
    ->count('id');

    $compulsoryFemaleLoaneeMembersTotal = DB::table('mfn_member_information')
    ->where([['gender', 2], ['softDel', '=', 0]])
    ->whereIn('id', $compulsoryLoaneeMembers)
    ->count('id');

    $compulsoryMaleNotLoaneeMembersTotal = DB::table('mfn_member_information')
    ->where([['gender', 1], ['softDel', '=', 0]])
    ->whereIn('id', $compulsoryNotLoaneeMembers)
    ->count('id');

    $compulsoryFemaleNotLoaneeMembersTotal = DB::table('mfn_member_information')
    ->where([['gender', 2], ['softDel', '=', 0]])
    ->whereIn('id', $compulsoryNotLoaneeMembers)
    ->count('id');

    $compolsoryInterestRateLoanee = 0;
    $compolsoryInterestRateLoanee = DB::table('mfn_savings_account')
    ->select('savingsInterestRate')
    ->where([['savingsProductIdFk', '=', 1]])
    ->groupBy('savingsInterestRate')
    ->get();

    $compolsoryInterestRateNotLoanee = 0;
    $compolsoryInterestRateNotLoanee = DB::table('mfn_savings_account')
    ->select('savingsInterestRate')
    ->where([['savingsProductIdFk', '=', 1]])
    ->groupBy('savingsInterestRate')
    ->get();

      // dd($compolsoryInterestRateNotLoanee);


    // END OF THE COMPOLSORY DEPOSIT MEMBER CALCULATION......................................................................

    // START OF THE VOLUNTARY DEPOSIT MEMBER CALCULATION.....................................................................
    $voluntaryloaneeMembersLoan1 = $voluntaryloaneeMembersLoan->pluck('memberIdFk');

    $voluntaryDepositMembers1 = $voluntaryDepositMembers->pluck('memberIdFk');

    $voluntaryNotLoaneeMembers = $voluntaryDepositMembers1->diff($voluntaryloaneeMembersLoan1);

    $voluntaryLoaneeMembers = $voluntaryDepositMembers1->diff($voluntaryNotLoaneeMembers);

    // dd($voluntaryDepositMembers, $voluntaryloaneeMembersLoan, $voluntaryLoaneeMembers, $voluntaryNotLoaneeMembers);

    $voluntaryloaneeMembersLoanSTART1 = $voluntaryloaneeMembersLoanSTART->pluck('memberIdFk');

    $voluntaryDepositMembersSTART1 = $voluntaryDepositMembersSTART->pluck('memberIdFk');

    $voluntaryNotLoaneeMembersSTART = $voluntaryDepositMembersSTART1->diff($voluntaryloaneeMembersLoanSTART1);

    $voluntaryLoaneeMembersSTART = $voluntaryDepositMembersSTART1->diff($voluntaryNotLoaneeMembers);

    // FUNDING ORGANIZATION..........................................................................................

    if ($requestedFundingOrganization != 'All' and $requestedFundingOrganization != -1) {
      $voluntaryLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
      ->whereIn('mfn_member_information.id', $voluntaryLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $voluntaryNotLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
      ->whereIn('mfn_member_information.id', $voluntaryNotLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $voluntaryLoaneeMembersSTART = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
      ->whereIn('mfn_member_information.id', $voluntaryLoaneeMembersSTART)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $voluntaryNotLoaneeMembersSTART = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
      ->whereIn('mfn_member_information.id', $voluntaryNotLoaneeMembersSTART)
      ->pluck('mfn_member_information.id')
      ->toArray();
    }
    elseif ($requestedFundingOrganization == -1) {
      $voluntaryLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
      ->whereIn('mfn_member_information.id', $voluntaryLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $voluntaryNotLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
      ->whereIn('mfn_member_information.id', $voluntaryNotLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $voluntaryLoaneeMembersSTART = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
      ->whereIn('mfn_member_information.id', $voluntaryLoaneeMembersSTART)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $voluntaryNotLoaneeMembersSTART = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
      ->whereIn('mfn_member_information.id', $voluntaryNotLoaneeMembersSTART)
      ->pluck('mfn_member_information.id')
      ->toArray();
    }

    // FUNDING ORGANIZATION..........................................................................................

    // voluntary Savings Total begining..............................................................................
    $voluntaryLoaneeMembersSavingsDepositeBeginning = DB::table('mfn_savings_deposit')
    ->where([['depositDate', '<', $startDate], ['productIdFk', '=', 2], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $voluntaryLoaneeMembersSTART)
    ->sum('amount');

    // $voluntaryLoaneeMembersSavingsDepositeBeginning2 = DB::table('mfn_savings_deposit')
    //   ->where([['depositDate', '<', $startDate], ['productIdFk', '=', 3], ['softDel', '=', 0]])
    //   ->whereIn('memberIdFk', $voluntaryLoaneeMembersSTART)
    //   ->sum('amount');

    // $voluntaryLoaneeMembersSavingsDepositeBeginning = $voluntaryLoaneeMembersSavingsDepositeBeginning1 + $voluntaryLoaneeMembersSavingsDepositeBeginning2;

      // dd($compulsoryLoaneeMembersSavingsDepositeBeginning);

    $voluntaryNotLoaneeMembersSavingsDepositeBeginning = DB::table('mfn_savings_deposit')
    ->where([['depositDate', '<', $startDate], ['productIdFk', '=', 2], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $voluntaryNotLoaneeMembersSTART)
    ->sum('amount');

    // $voluntaryNotLoaneeMembersSavingsDepositeBeginning2 = DB::table('mfn_savings_deposit')
    //   ->where([['depositDate', '<', $startDate], ['productIdFk', '=', 3], ['softDel', '=', 0]])
    //   ->whereIn('memberIdFk', $voluntaryNotLoaneeMembersSTART)
    //   ->sum('amount');

    // $voluntaryNotLoaneeMembersSavingsDepositeBeginning = $voluntaryNotLoaneeMembersSavingsDepositeBeginning1 + $voluntaryNotLoaneeMembersSavingsDepositeBeginning2;

    $voluntaryLoaneeMembersSavingsRefundBeginning = DB::table('mfn_savings_withdraw')
    ->where([['withdrawDate', '<', $startDate], ['productIdFk', '=', 2], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $voluntaryLoaneeMembersSTART)
    ->sum('amount');

    // $voluntaryLoaneeMembersSavingsRefundBeginning2 = DB::table('mfn_savings_withdraw')
    //   ->where([['withdrawDate', '<', $startDate], ['productIdFk', '=', 3], ['softDel', '=', 0]])
    //   ->whereIn('memberIdFk', $voluntaryLoaneeMembersSTART)
    //   ->sum('amount');

    // $voluntaryLoaneeMembersSavingsRefundBeginning = $voluntaryLoaneeMembersSavingsRefundBeginning1 + $voluntaryLoaneeMembersSavingsRefundBeginning2;

      // dd($voluntaryLoaneeMembersSavingsRefundBeginning);

    $voluntaryNotLoaneeMembersSavingsRefundBeginning = DB::table('mfn_savings_withdraw')
    ->where([['withdrawDate', '<', $startDate], ['productIdFk', '=', 2], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $voluntaryNotLoaneeMembersSTART)
    ->sum('amount');

    // $voluntaryNotLoaneeMembersSavingsRefundBeginning2 = DB::table('mfn_savings_withdraw')
    //   ->where([['withdrawDate', '<', $startDate], ['productIdFk', '=', 3], ['softDel', '=', 0]])
    //   ->whereIn('memberIdFk', $voluntaryNotLoaneeMembersSTART)
    //   ->sum('amount');

    // $voluntaryNotLoaneeMembersSavingsRefundBeginning = $voluntaryNotLoaneeMembersSavingsRefundBeginning1 + $voluntaryNotLoaneeMembersSavingsRefundBeginning2;

    $voluntarySavingsInterestLoaneePaidBeginning = DB::table('mfn_savings_interest')
    ->where([['date', '<', $startDate], ['productIdFk', '=', 2]])
    ->whereIn('memberIdFk', $voluntaryLoaneeMembersSTART)
    ->sum('interestAmount');

    $voluntarySavingsInterestNotLoaneePaidBeginning = DB::table('mfn_savings_interest')
    ->where([['date', '<', $startDate], ['productIdFk', '=', 2]])
    ->whereIn('memberIdFk', $voluntaryNotLoaneeMembersSTART)
    ->sum('interestAmount');

    $voluntaryLoaneeMembersSavingsTotal = ($voluntaryLoaneeMembersSavingsDepositeBeginning + $voluntarySavingsInterestLoaneePaidBeginning) - $voluntaryLoaneeMembersSavingsRefundBeginning;

    $voluntaryNotLoaneeMembersSavingsTotal = ($voluntaryNotLoaneeMembersSavingsDepositeBeginning + $voluntarySavingsInterestNotLoaneePaidBeginning) - $voluntaryNotLoaneeMembersSavingsRefundBeginning;

      // dd($compulsoryLoaneeMembersSavingsTotal);

    //..................................................................................................................

    // voluntary Savings Total In Between
    $voluntaryLoaneeMembersSavingsTotalInBetween = DB::table('mfn_savings_deposit')
    ->where([['depositDate', '>=', $startDate], ['depositDate', '<=', $endDate], ['productIdFk', '=', 2], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $voluntaryLoaneeMembers)
    ->sum('amount');

    // $voluntaryLoaneeMembersSavingsTotalInBetween2 = DB::table('mfn_savings_deposit')
    //   ->where([['depositDate', '>=', $startDate], ['depositDate', '<=', $endDate], ['productIdFk', '=', 3], ['softDel', '=', 0]])
    //   ->whereIn('memberIdFk', $voluntaryLoaneeMembers)
    //   ->sum('amount');

    // $voluntaryLoaneeMembersSavingsTotalInBetween = $voluntaryLoaneeMembersSavingsTotalInBetween1 + $voluntaryLoaneeMembersSavingsTotalInBetween2;

    $voluntaryNotLoaneeMembersSavingsTotalInBetween = DB::table('mfn_savings_deposit')
    ->where([['depositDate', '>=', $startDate], ['depositDate', '<=', $endDate], ['productIdFk', '=', 2], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $voluntaryNotLoaneeMembers)
    ->sum('amount');

    // $voluntaryNotLoaneeMembersSavingsTotalInBetween2 = DB::table('mfn_savings_deposit')
    //   ->where([['depositDate', '>=', $startDate], ['depositDate', '<=', $endDate], ['productIdFk', '=', 3], ['softDel', '=', 0]])
    //   ->whereIn('memberIdFk', $voluntaryNotLoaneeMembers)
    //   ->sum('amount');

    // $voluntaryNotLoaneeMembersSavingsTotalInBetween = $voluntaryNotLoaneeMembersSavingsTotalInBetween1 + $voluntaryNotLoaneeMembersSavingsTotalInBetween2;
    //

    // voluntary Savings Refund Total In Between
    $voluntaryLoaneeMembersSavingsRefundTotalInBetween = DB::table('mfn_savings_withdraw')
    ->where([['withdrawDate', '>=', $startDate], ['withdrawDate', '<=', $endDate], ['productIdFk', '=', 2], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $voluntaryLoaneeMembers)
    ->sum('amount');

    // $voluntaryLoaneeMembersSavingsRefundTotalInBetween2 = DB::table('mfn_savings_withdraw')
    //   ->where([['withdrawDate', '>=', $startDate], ['withdrawDate', '<=', $endDate], ['productIdFk', '=', 3], ['softDel', '=', 0]])
    //   ->whereIn('memberIdFk', $voluntaryLoaneeMembers)
    //   ->sum('amount');

    // $voluntaryLoaneeMembersSavingsRefundTotalInBetween = $voluntaryLoaneeMembersSavingsRefundTotalInBetween1 + $voluntaryLoaneeMembersSavingsRefundTotalInBetween2;

    $voluntaryNotLoaneeMembersSavingsRefundTotalInBetween = DB::table('mfn_savings_withdraw')
    ->where([['withdrawDate', '>=', $startDate], ['withdrawDate', '<=', $endDate], ['productIdFk', '=', 2], ['softDel', '=', 0]])
      // ->orWhere([['withdrawDate', '>=', $startDate], ['withdrawDate', '<=', $endDate], ['productIdFk', '=', 3], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $voluntaryNotLoaneeMembers)
    ->sum('amount');

    // $voluntaryNotLoaneeMembersSavingsRefundTotalInBetween2 = DB::table('mfn_savings_withdraw')
    //   // ->where([['withdrawDate', '>=', $startDate], ['withdrawDate', '<=', $endDate], ['productIdFk', '=', 2], ['softDel', '=', 0]])
    //   ->where([['withdrawDate', '>=', $startDate], ['withdrawDate', '<=', $endDate], ['productIdFk', '=', 3], ['softDel', '=', 0]])
    //   ->whereIn('memberIdFk', $voluntaryNotLoaneeMembers)
    //   ->sum('amount');

      // $voluntaryNotLoaneeMembersSavingsRefundTotalInBetween = $voluntaryNotLoaneeMembersSavingsRefundTotalInBetween1 + $voluntaryNotLoaneeMembersSavingsRefundTotalInBetween2;
    //

    // voluntary Savings Interest payable and paid //

    $voluntarySavingsInterestLoaneePayable = DB::table('mfn_savings_probation_interest')
    ->where([['date', '>=', $startDate], ['date', '<=', $endDate], ['productIdFk', '=', 2]])
    ->whereIn('memberIdFk', $voluntaryLoaneeMembers)
    ->sum('interestAmount');

    $voluntarySavingsInterestNotLoaneePayable = DB::table('mfn_savings_probation_interest')
    ->where([['date', '>=', $startDate], ['date', '<=', $endDate], ['productIdFk', '=', 2]])
    ->whereIn('memberIdFk', $voluntaryNotLoaneeMembers)
    ->sum('interestAmount');

    $voluntarySavingsInterestLoaneePaid = DB::table('mfn_savings_interest')
    ->where([['date', '>=', $startDate], ['date', '<=', $endDate], ['productIdFk', '=', 2]])
    ->whereIn('memberIdFk', $voluntaryLoaneeMembers)
    ->sum('interestAmount');

    $voluntarySavingsInterestNotLoaneePaid = DB::table('mfn_savings_interest')
    ->where([['date', '>=', $startDate], ['date', '<=', $endDate], ['productIdFk', '=', 2]])
    ->whereIn('memberIdFk', $voluntaryNotLoaneeMembers)
    ->sum('interestAmount');

    //

    $voluntaryMaleLoaneeMembersTotal = DB::table('mfn_member_information')
    ->where([['gender', 1], ['softDel', '=', 0]])
    ->whereIn('id', $voluntaryLoaneeMembers)
    ->count('id');

    $voluntaryFemaleLoaneeMembersTotal = DB::table('mfn_member_information')
    ->where([['gender', 2], ['softDel', '=', 0]])
    ->whereIn('id', $voluntaryLoaneeMembers)
    ->count('id');

    $voluntaryMaleNotLoaneeMembersTotal = DB::table('mfn_member_information')
    ->where([['gender', 1], ['softDel', '=', 0]])
    ->whereIn('id', $voluntaryNotLoaneeMembers)
    ->count('id');

    $voluntaryFemaleNotLoaneeMembersTotal = DB::table('mfn_member_information')
    ->where([['gender', 2], ['softDel', '=', 0]])
    ->whereIn('id', $voluntaryNotLoaneeMembers)
    ->count('id');

    $voluntaryInterestRateLoanee = 0;
    $voluntaryInterestRateLoanee = DB::table('mfn_savings_account')
    ->select('savingsInterestRate')
      // ->where([['memberIdFk', $voluntaryLoaneeMembers]])
    ->whereIn('savingsProductIdFk', $voluntar)
    ->groupBy('savingsInterestRate')
    ->get();

    $voluntaryInterestRateNotLoanee = 0;
    $voluntaryInterestRateNotLoanee = DB::table('mfn_savings_account')
    ->select('savingsInterestRate')
      // ->where([['memberIdFk', $voluntaryNotLoaneeMembers]])
    ->whereIn('savingsProductIdFk', $voluntar)
    ->groupBy('savingsInterestRate')
    ->get();

      // dd($voluntaryInterestRateLoanee);
    // END OF THE VOLUNTARY DEPOSIT MEMBER CALCULATION......................................................................

    // START OF THE TERM DEPOSIT MEMBER CALCULATION.....................................................................
    $termloaneeMembersLoan1 = $termloaneeMembersLoan->pluck('memberIdFk');

    $termDepositMembers1 = $termDepositMembers->pluck('memberIdFk');

    $termNotLoaneeMembers = $termDepositMembers1->diff($termloaneeMembersLoan1);

    $termLoaneeMembers = $termDepositMembers1->diff($termNotLoaneeMembers);

    $termloaneeMembersLoanSTART1 = $termloaneeMembersLoanSTART->pluck('memberIdFk');

    $termDepositMembersSTART1 = $termDepositMembersSTART->pluck('memberIdFk');

    $termNotLoaneeMembersSTART = $termDepositMembersSTART1->diff($termloaneeMembersLoanSTART1);

    $termLoaneeMembersSTART = $termDepositMembersSTART1->diff($termNotLoaneeMembers);

    // FUNDING ORGANIZATION..........................................................................................

    if ($requestedFundingOrganization != 'All' and $requestedFundingOrganization != -1) {
      $termLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
      ->whereIn('mfn_member_information.id', $termLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $termNotLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
      ->whereIn('mfn_member_information.id', $termNotLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $termLoaneeMembersSTART = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
      ->whereIn('mfn_member_information.id', $termLoaneeMembersSTART)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $termNotLoaneeMembersSTART = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
      ->whereIn('mfn_member_information.id', $termNotLoaneeMembersSTART)
      ->pluck('mfn_member_information.id')
      ->toArray();
    }
    elseif ($requestedFundingOrganization == -1) {
      $termLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
      ->whereIn('mfn_member_information.id', $termLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $termNotLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
      ->whereIn('mfn_member_information.id', $termNotLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $termLoaneeMembersSTART = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
      ->whereIn('mfn_member_information.id', $termLoaneeMembersSTART)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $termNotLoaneeMembersSTART = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
      ->whereIn('mfn_member_information.id', $termNotLoaneeMembersSTART)
      ->pluck('mfn_member_information.id')
      ->toArray();
    }

    // FUNDING ORGANIZATION..........................................................................................

    // term Savings Total Begining..........................................................................................
    $termLoaneeMembersSavingsDepositeBeginning1 = DB::table('mfn_savings_deposit')
    ->where([['depositDate', '<', $startDate], ['productIdFk', '=', 3], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $termLoaneeMembersSTART)
    ->sum('amount');

    $termLoaneeMembersSavingsDepositeBeginning2 = DB::table('mfn_savings_deposit')
    ->where([['depositDate', '<', $startDate], ['productIdFk', '=', 4], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $termLoaneeMembersSTART)
    ->sum('amount');

    $termLoaneeMembersSavingsDepositeBeginning = $termLoaneeMembersSavingsDepositeBeginning1 + $termLoaneeMembersSavingsDepositeBeginning2;

      // dd($compulsoryLoaneeMembersSavingsDepositeBeginning);

    $termNotLoaneeMembersSavingsDepositeBeginning1 = DB::table('mfn_savings_deposit')
    ->where([['depositDate', '<', $startDate], ['productIdFk', '=', 3], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $termNotLoaneeMembersSTART)
    ->sum('amount');

    $termNotLoaneeMembersSavingsDepositeBeginning2 = DB::table('mfn_savings_deposit')
    ->where([['depositDate', '<', $startDate], ['productIdFk', '=', 4], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $termNotLoaneeMembersSTART)
    ->sum('amount');

    $termNotLoaneeMembersSavingsDepositeBeginning = $termNotLoaneeMembersSavingsDepositeBeginning1 + $termNotLoaneeMembersSavingsDepositeBeginning2;

    $termLoaneeMembersSavingsRefundBeginning1 = DB::table('mfn_savings_withdraw')
    ->where([['withdrawDate', '<', $startDate], ['productIdFk', '=', 3], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $termLoaneeMembersSTART)
    ->sum('amount');

    $termLoaneeMembersSavingsRefundBeginning2 = DB::table('mfn_savings_withdraw')
    ->where([['withdrawDate', '<', $startDate], ['productIdFk', '=', 4], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $termLoaneeMembersSTART)
    ->sum('amount');

    $termLoaneeMembersSavingsRefundBeginning = $termLoaneeMembersSavingsRefundBeginning1 + $termLoaneeMembersSavingsRefundBeginning2;

      // dd($termLoaneeMembersSavingsRefundBeginning);

    $termNotLoaneeMembersSavingsRefundBeginning1 = DB::table('mfn_savings_withdraw')
    ->where([['withdrawDate', '<', $startDate], ['productIdFk', '=', 3], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $termNotLoaneeMembersSTART)
    ->sum('amount');

    $termNotLoaneeMembersSavingsRefundBeginning2 = DB::table('mfn_savings_withdraw')
    ->where([['withdrawDate', '<', $startDate], ['productIdFk', '=', 4], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $termNotLoaneeMembersSTART)
    ->sum('amount');

    $termNotLoaneeMembersSavingsRefundBeginning = $termNotLoaneeMembersSavingsRefundBeginning1 + $termNotLoaneeMembersSavingsRefundBeginning2;

    $termSavingsInterestLoaneePaidBeginning1 = DB::table('mfn_savings_interest')
    ->where([['date', '<', $startDate], ['productIdFk', '=', 3]])
    ->whereIn('memberIdFk', $termLoaneeMembersSTART)
    ->sum('interestAmount');

    $termSavingsInterestLoaneePaidBeginning2 = DB::table('mfn_savings_interest')
    ->where([['date', '<', $startDate], ['productIdFk', '=', 4]])
    ->whereIn('memberIdFk', $termLoaneeMembersSTART)
    ->sum('interestAmount');

    $termSavingsInterestLoaneePaidBeginning = $termSavingsInterestLoaneePaidBeginning1 + $termSavingsInterestLoaneePaidBeginning2;

    $termSavingsInterestNotLoaneePaidBeginning1 = DB::table('mfn_savings_interest')
    ->where([['date', '<', $startDate], ['productIdFk', '=', 3]])
    ->whereIn('memberIdFk', $termNotLoaneeMembersSTART)
    ->sum('interestAmount');

    $termSavingsInterestNotLoaneePaidBeginning2 = DB::table('mfn_savings_interest')
    ->where([['date', '<', $startDate], ['productIdFk', '=', 4]])
    ->whereIn('memberIdFk', $termNotLoaneeMembersSTART)
    ->sum('interestAmount');

    $termSavingsInterestNotLoaneePaidBeginning = $termSavingsInterestNotLoaneePaidBeginning1 + $termSavingsInterestNotLoaneePaidBeginning2;

    $termLoaneeMembersSavingsTotal = ($termLoaneeMembersSavingsDepositeBeginning + $termSavingsInterestLoaneePaidBeginning) - $termLoaneeMembersSavingsRefundBeginning;

    $termNotLoaneeMembersSavingsTotal = ($termNotLoaneeMembersSavingsDepositeBeginning + $termSavingsInterestNotLoaneePaidBeginning) - $termNotLoaneeMembersSavingsRefundBeginning;

      // dd($compulsoryLoaneeMembersSavingsTotal);

    //.......................................................................................................................

    // term Savings Total In Between
    $termLoaneeMembersSavingsTotalInBetween1 = DB::table('mfn_savings_deposit')
    ->where([['depositDate', '>=', $startDate], ['depositDate', '<=', $endDate], ['productIdFk', '=', 3], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $termLoaneeMembers)
    ->sum('amount');

    $termLoaneeMembersSavingsTotalInBetween2 = DB::table('mfn_savings_deposit')
    ->where([['depositDate', '>=', $startDate], ['depositDate', '<=', $endDate], ['productIdFk', '=', 4], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $termLoaneeMembers)
    ->sum('amount');

    $termLoaneeMembersSavingsTotalInBetween = $termLoaneeMembersSavingsTotalInBetween1 + $termLoaneeMembersSavingsTotalInBetween2;

    $termNotLoaneeMembersSavingsTotalInBetween1 = DB::table('mfn_savings_deposit')
    ->where([['depositDate', '>=', $startDate], ['depositDate', '<=', $endDate], ['productIdFk', '=', 3], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $termNotLoaneeMembers)
    ->sum('amount');

    $termNotLoaneeMembersSavingsTotalInBetween2 = DB::table('mfn_savings_deposit')
    ->where([['depositDate', '>=', $startDate], ['depositDate', '<=', $endDate], ['productIdFk', '=', 4], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $termNotLoaneeMembers)
    ->sum('amount');

    $termNotLoaneeMembersSavingsTotalInBetween = $termNotLoaneeMembersSavingsTotalInBetween1 + $termNotLoaneeMembersSavingsTotalInBetween2;
    //

    // term Savings Refund Total In Between
    $termLoaneeMembersSavingsRefundTotalInBetween1 = DB::table('mfn_savings_withdraw')
    ->where([['withdrawDate', '>=', $startDate], ['withdrawDate', '<=', $endDate], ['productIdFk', '=', 3], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $termLoaneeMembers)
    ->sum('amount');

    $termLoaneeMembersSavingsRefundTotalInBetween2 = DB::table('mfn_savings_withdraw')
    ->where([['withdrawDate', '>=', $startDate], ['withdrawDate', '<=', $endDate], ['productIdFk', '=', 4], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $termLoaneeMembers)
    ->sum('amount');

    $termLoaneeMembersSavingsRefundTotalInBetween = $termLoaneeMembersSavingsRefundTotalInBetween1 + $termLoaneeMembersSavingsRefundTotalInBetween2;

    $termNotLoaneeMembersSavingsRefundTotalInBetween1 = DB::table('mfn_savings_withdraw')
    ->where([['withdrawDate', '>=', $startDate], ['withdrawDate', '<=', $endDate], ['productIdFk', '=', 3], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $termNotLoaneeMembers)
    ->sum('amount');

    $termNotLoaneeMembersSavingsRefundTotalInBetween2 = DB::table('mfn_savings_withdraw')
    ->where([['withdrawDate', '>=', $startDate], ['withdrawDate', '<=', $endDate], ['productIdFk', '=', 4], ['softDel', '=', 0]])
    ->whereIn('memberIdFk', $termNotLoaneeMembers)
    ->sum('amount');

    $termNotLoaneeMembersSavingsRefundTotalInBetween = $termNotLoaneeMembersSavingsRefundTotalInBetween1 + $termNotLoaneeMembersSavingsRefundTotalInBetween2;
    //

    // term Savings Interest payable and paid //

    $termSavingsInterestLoaneePayable1 = DB::table('mfn_savings_probation_interest')
    ->where([['date', '>=', $startDate], ['date', '<=', $endDate], ['productIdFk', '=', 3]])
    ->whereIn('memberIdFk', $termLoaneeMembers)
    ->sum('interestAmount');

    $termSavingsInterestLoaneePayable2 = DB::table('mfn_savings_probation_interest')
    ->where([['date', '>=', $startDate], ['date', '<=', $endDate], ['productIdFk', '=', 4]])
    ->whereIn('memberIdFk', $termLoaneeMembers)
    ->sum('interestAmount');

    $termSavingsInterestLoaneePayable = $termSavingsInterestLoaneePayable1 + $termSavingsInterestLoaneePayable2;

    $termSavingsInterestNotLoaneePayable1 = DB::table('mfn_savings_probation_interest')
    ->where([['date', '>=', $startDate], ['date', '<=', $endDate], ['productIdFk', '=', 3]])
    ->whereIn('memberIdFk', $termNotLoaneeMembers)
    ->sum('interestAmount');

    $termSavingsInterestNotLoaneePayable2 = DB::table('mfn_savings_probation_interest')
    ->where([['date', '>=', $startDate], ['date', '<=', $endDate], ['productIdFk', '=', 4]])
    ->whereIn('memberIdFk', $termNotLoaneeMembers)
    ->sum('interestAmount');

    $termSavingsInterestNotLoaneePayable = $termSavingsInterestNotLoaneePayable1 + $termSavingsInterestNotLoaneePayable2;

    $termSavingsInterestLoaneePaid1 = DB::table('mfn_savings_interest')
    ->where([['date', '>=', $startDate], ['date', '<=', $endDate], ['productIdFk', '=', 3]])
    ->whereIn('memberIdFk', $termLoaneeMembers)
    ->sum('interestAmount');

    $termSavingsInterestLoaneePaid2 = DB::table('mfn_savings_interest')
    ->where([['date', '>=', $startDate], ['date', '<=', $endDate], ['productIdFk', '=', 4]])
    ->whereIn('memberIdFk', $termLoaneeMembers)
    ->sum('interestAmount');

    $termSavingsInterestLoaneePaid = $termSavingsInterestLoaneePaid1 + $termSavingsInterestLoaneePaid2;

    $termSavingsInterestNotLoaneePaid1 = DB::table('mfn_savings_interest')
    ->where([['date', '>=', $startDate], ['date', '<=', $endDate], ['productIdFk', '=', 3]])
    ->whereIn('memberIdFk', $termNotLoaneeMembers)
    ->sum('interestAmount');

    $termSavingsInterestNotLoaneePaid2 = DB::table('mfn_savings_interest')
    ->where([['date', '>=', $startDate], ['date', '<=', $endDate], ['productIdFk', '=', 4]])
    ->whereIn('memberIdFk', $termNotLoaneeMembers)
    ->sum('interestAmount');

    $termSavingsInterestNotLoaneePaid = $termSavingsInterestNotLoaneePaid1 + $termSavingsInterestNotLoaneePaid2;

    //

    $termMaleLoaneeMembersTotal = DB::table('mfn_member_information')
    ->where([['gender', 1], ['softDel', '=', 0]])
    ->whereIn('id', $termLoaneeMembers)
    ->count('id');

    $termFemaleLoaneeMembersTotal = DB::table('mfn_member_information')
    ->where([['gender', 2], ['softDel', '=', 0]])
    ->whereIn('id', $termLoaneeMembers)
    ->count('id');

    $termMaleNotLoaneeMembersTotal = DB::table('mfn_member_information')
    ->where([['gender', 1], ['softDel', '=', 0]])
    ->whereIn('id', $termNotLoaneeMembers)
    ->count('id');

    $termFemaleNotLoaneeMembersTotal = DB::table('mfn_member_information')
    ->where([['gender', 2], ['softDel', '=', 0]])
    ->whereIn('id', $termNotLoaneeMembers)
    ->count('id');

    $termInterestRateLoanee = 0;
    $termInterestRateLoanee = DB::table('mfn_savings_account')
    ->select('savingsInterestRate')
    ->where([['savingsProductIdFk', '=', 4]])
    ->groupBy('savingsInterestRate')
    ->get();

    $termInterestRateNotLoanee = 0;
    $termInterestRateNotLoanee = DB::table('mfn_savings_account')
    ->select('savingsInterestRate')
    ->where([['savingsProductIdFk', '=', 4]])
    ->groupBy('savingsInterestRate')
    ->get();

      // dd($termInterestRateNotLoanee);
    // END OF THE TERM DEPOSIT MEMBER CALCULATION.....................................................................

    // dd($actualMembers);


//    SECOND TABLE CALCULATION.........................................................................................
    $compolsoryloaneeMembersLoan = $compolsoryloaneeMembersLoan->pluck('memberIdFk');

    $compolsoryDepositMembers = $compolsoryDepositMembers->pluck('memberIdFk');

    $compulsoryNotLoaneeMembers = $compolsoryDepositMembers->diff($compolsoryloaneeMembersLoan);

    $compulsoryLoaneeMembers = $compolsoryDepositMembers->diff($compulsoryNotLoaneeMembers);



    $voluntaryloaneeMembersLoan = $voluntaryloaneeMembersLoan->pluck('memberIdFk');

    $voluntaryDepositMembers = $voluntaryDepositMembers->pluck('memberIdFk');

    $voluntaryNotLoaneeMembers = $voluntaryDepositMembers->diff($voluntaryloaneeMembersLoan);

    $voluntaryLoaneeMembers = $voluntaryDepositMembers->diff($voluntaryNotLoaneeMembers);



    $termloaneeMembersLoan = $termloaneeMembersLoan->pluck('memberIdFk');

    $termDepositMembers = $termDepositMembers->pluck('memberIdFk');

    $termNotLoaneeMembers = $termDepositMembers->diff($termloaneeMembersLoan);

    $termLoaneeMembers = $termDepositMembers->diff($termNotLoaneeMembers);



    // FUNDING ORGANIZATION................................................................................................

    if ($requestedFundingOrganization != 'All' and $requestedFundingOrganization != -1) {
      $compulsoryLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
      ->whereIn('mfn_member_information.id', $compulsoryLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $compulsoryNotLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
      ->whereIn('mfn_member_information.id', $compulsoryNotLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();
      $voluntaryLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
      ->whereIn('mfn_member_information.id', $voluntaryLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $voluntaryNotLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
      ->whereIn('mfn_member_information.id', $voluntaryNotLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $termLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
      ->whereIn('mfn_member_information.id', $termLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $termNotLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', $requestedFundingOrganization)
      ->whereIn('mfn_member_information.id', $termNotLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $savingsAccount = array_merge($compulsoryLoaneeMembers, $compulsoryNotLoaneeMembers, $voluntaryLoaneeMembers, $voluntaryNotLoaneeMembers, $termLoaneeMembers, $termNotLoaneeMembers);

    }
    elseif ($requestedFundingOrganization == -1) {
      $compulsoryLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
      ->whereIn('mfn_member_information.id', $compulsoryLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $compulsoryNotLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
      ->whereIn('mfn_member_information.id', $compulsoryNotLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();
      $voluntaryLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
      ->whereIn('mfn_member_information.id', $voluntaryLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $voluntaryNotLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
      ->whereIn('mfn_member_information.id', $voluntaryNotLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $termLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
      ->whereIn('mfn_member_information.id', $termLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $termNotLoaneeMembers = DB::table('mfn_member_information')
      ->select('mfn_member_information.id')
      ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
      ->where('mfn_loans_product.fundingOrganizationId', '!=', 3)
      ->whereIn('mfn_member_information.id', $termNotLoaneeMembers)
      ->pluck('mfn_member_information.id')
      ->toArray();

      $savingsAccount = array_merge($compulsoryLoaneeMembers, $compulsoryNotLoaneeMembers, $voluntaryLoaneeMembers, $voluntaryNotLoaneeMembers, $termLoaneeMembers, $termNotLoaneeMembers);
    }
    else {
      $compulsoryLoaneeMembersArray = $compulsoryLoaneeMembers->toArray();
      $compulsoryNotLoaneeMembersArray = $compulsoryNotLoaneeMembers->toArray();
      $voluntaryLoaneeMembersArray = $voluntaryLoaneeMembers->toArray();
      $voluntaryNotLoaneeMembersArray = $voluntaryNotLoaneeMembers->toArray();
      $termLoaneeMembersArray = $termLoaneeMembers->toArray();
      $termNotLoaneeMembersArray = $termNotLoaneeMembers->toArray();

      $savingsAccount = array_merge($compulsoryLoaneeMembersArray, $compulsoryNotLoaneeMembersArray, $voluntaryLoaneeMembersArray, $voluntaryNotLoaneeMembersArray, $termLoaneeMembersArray, $termNotLoaneeMembersArray);
    }

    // FUNDING ORGANIZATION................................................................................................

    $uptoTwoKdepositeNumber = 0;
    $TwoKToUptoFiveKdepositeNumber = 0;
    $FiveKToUptoTenKdepositeNumber = 0;
    $TenKToUptoTwentyKdepositeNumber = 0;
    $TwentyKToMoredepositeNumber = 0;

    $uptoTwoKdepositeAmount = 0;
    $TwoKToUptoFiveKdepositeAmount = 0;
    $FiveKToUptoTenKdepositeAmount = 0;
    $TenKToUptoTwentyKdepositeAmount = 0;
    $TwentyKToMoredepositeAmount = 0;

    $savingsBalanceTotal = 0;
    $withdraw = 0;
    $deposite = 0;

    $count = 0;

    $savingsArray = array();

    $savingsAccount = array_unique($savingsAccount);

    foreach ($savingsAccount as $key => $savingsAccount1) {
      // ++$count;
      $depositeAmount = DB::table('mfn_savings_deposit')
      ->where([['memberIdFk', $savingsAccount1], ['depositDate', '<=', $endDate], ['softDel', '=', 0]])
      ->sum('amount');

      $withdrawAmount = DB::table('mfn_savings_withdraw')
      ->where([['memberIdFk', $savingsAccount1], ['withdrawDate', '<=', $endDate], ['softDel', '=', 0]])
      ->sum('amount');

      $withdraw = $withdraw + $withdrawAmount;
      $deposite = $deposite + $depositeAmount;

      $savingsArray[] = $savingsAccount1;

      // print($savingsAccount1.'');

      $savingsBalanceTotal = $depositeAmount - $withdrawAmount;

      if ($savingsBalanceTotal <= 2000) {
        $uptoTwoKdepositeNumber = $uptoTwoKdepositeNumber + 1;
        $uptoTwoKdepositeAmount = $uptoTwoKdepositeAmount + $savingsBalanceTotal;
      }
      elseif ($savingsBalanceTotal >= 2001 and $savingsBalanceTotal <= 5000) {
        $TwoKToUptoFiveKdepositeNumber = $TwoKToUptoFiveKdepositeNumber + 1;
        $TwoKToUptoFiveKdepositeAmount = $TwoKToUptoFiveKdepositeAmount + $savingsBalanceTotal;
      }
      elseif ($savingsBalanceTotal >= 5001 and $savingsBalanceTotal <= 10000) {
        $FiveKToUptoTenKdepositeNumber = $FiveKToUptoTenKdepositeNumber + 1;
        $FiveKToUptoTenKdepositeAmount = $FiveKToUptoTenKdepositeAmount + $savingsBalanceTotal;
      }
      elseif ($savingsBalanceTotal >= 10001 and $savingsBalanceTotal <= 20000) {
        $TenKToUptoTwentyKdepositeNumber = $TenKToUptoTwentyKdepositeNumber + 1;
        $TenKToUptoTwentyKdepositeAmount = $TenKToUptoTwentyKdepositeAmount + $savingsBalanceTotal;
      }
      elseif ($savingsBalanceTotal >= 20001) {
        $TwentyKToMoredepositeNumber = $TwentyKToMoredepositeNumber + 1;
        $TwentyKToMoredepositeAmount = $TwentyKToMoredepositeAmount + $savingsBalanceTotal;
      }
      $savingsBalanceTotal = 0;

      // $countUniqueValues = array_count_values($savingsArray);
      //
      // if ($countUniqueValues[$savingsAccount1] == 1) {
      //   if ($savingsBalanceTotal <= 2000) {
      //     // $uptoTwoKdepositeNumber = $uptoTwoKdepositeNumber + 1;
      //     $uptoTwoKdepositeAmount = $uptoTwoKdepositeAmount + $savingsBalanceTotal;
      //   }
      //   elseif ($savingsBalanceTotal >= 2001 and $savingsBalanceTotal <= 5000) {
      //     // $TwoKToUptoFiveKdepositeNumber = $TwoKToUptoFiveKdepositeNumber + 1;
      //     $TwoKToUptoFiveKdepositeAmount = $TwoKToUptoFiveKdepositeAmount + $savingsBalanceTotal;
      //   }
      //   elseif ($savingsBalanceTotal >= 5001 and $savingsBalanceTotal <= 10000) {
      //     // $FiveKToUptoTenKdepositeNumber = $FiveKToUptoTenKdepositeNumber + 1;
      //     $FiveKToUptoTenKdepositeAmount = $FiveKToUptoTenKdepositeAmount + $savingsBalanceTotal;
      //   }
      //   elseif ($savingsBalanceTotal >= 10001 and $savingsBalanceTotal <= 20000) {
      //     // $TenKToUptoTwentyKdepositeNumber = $TenKToUptoTwentyKdepositeNumber + 1;
      //     $TenKToUptoTwentyKdepositeAmount = $TenKToUptoTwentyKdepositeAmount + $savingsBalanceTotal;
      //   }
      //   elseif ($savingsBalanceTotal >= 20001) {
      //     // $TwentyKToMoredepositeNumber = $TwentyKToMoredepositeNumber + 1;
      //     $TwentyKToMoredepositeAmount = $TwentyKToMoredepositeAmount + $savingsBalanceTotal;
      //   }
      //   $savingsBalanceTotal = 0;
      // }

    }
    // dd($countUniqueValues, $count);

    // dd($uptoTwoKdepositeAmount, $TwoKToUptoFiveKdepositeAmount, $FiveKToUptoTenKdepositeAmount, $TenKToUptoTwentyKdepositeAmount, $TwentyKToMoredepositeAmount);
// END OF SECOND TABLE CALCULATION...............................................................................................

    return view('microfin.reports.mra.mra_mfi_three_A_views.mra_mfi_three_A_report', compact('requestPeriod', 'compulsoryLoaneeMembersSavingsTotal',
      'compulsoryNotLoaneeMembersSavingsTotal', 'compulsoryMaleLoaneeMembersTotal', 'compulsoryFemaleLoaneeMembersTotal', 'compulsoryMaleNotLoaneeMembersTotal',
      'compulsoryFemaleNotLoaneeMembersTotal', 'voluntaryMaleLoaneeMembersTotal', 'voluntaryFemaleLoaneeMembersTotal', 'voluntaryMaleNotLoaneeMembersTotal',
      'voluntaryFemaleNotLoaneeMembersTotal', 'termMaleLoaneeMembersTotal', 'termFemaleLoaneeMembersTotal', 'termMaleNotLoaneeMembersTotal', 'termFemaleNotLoaneeMembersTotal',
      'compulsoryLoaneeMembersSavingsTotal', 'compulsoryNotLoaneeMembersSavingsTotal', 'voluntaryLoaneeMembersSavingsTotal', 'voluntaryNotLoaneeMembersSavingsTotal',
      'termLoaneeMembersSavingsTotal', 'termNotLoaneeMembersSavingsTotal', 'compulsoryLoaneeMembersSavingsTotalInBetween', 'compulsoryNotLoaneeMembersSavingsTotalInBetween',
      'voluntaryLoaneeMembersSavingsTotalInBetween', 'voluntaryNotLoaneeMembersSavingsTotalInBetween', 'termLoaneeMembersSavingsTotalInBetween', 'termNotLoaneeMembersSavingsTotalInBetween',
      'compulsoryLoaneeMembersSavingsRefundTotalInBetween', 'compulsoryNotLoaneeMembersSavingsRefundTotalInBetween', 'voluntaryLoaneeMembersSavingsRefundTotalInBetween',
      'voluntaryNotLoaneeMembersSavingsRefundTotalInBetween', 'termLoaneeMembersSavingsRefundTotalInBetween', 'termNotLoaneeMembersSavingsRefundTotalInBetween', 'startDate', 'endDate',
      'requestedBranch', 'compulsorySavingsInterestLoaneePayable', 'compulsorySavingsInterestNotLoaneePayable', 'voluntarySavingsInterestLoaneePayable', 'voluntarySavingsInterestNotLoaneePayable',
      'termSavingsInterestLoaneePayable', 'termSavingsInterestNotLoaneePayable', 'compulsorySavingsInterestLoaneePaid', 'compulsorySavingsInterestNotLoaneePaid', 'voluntarySavingsInterestLoaneePaid',
      'voluntarySavingsInterestNotLoaneePaid', 'termSavingsInterestLoaneePaid', 'termSavingsInterestNotLoaneePaid', 'compolsoryInterestRateLoanee', 'compolsoryInterestRateNotLoanee', 'voluntaryInterestRateLoanee',
      'voluntaryInterestRateNotLoanee', 'termInterestRateLoanee', 'termInterestRateNotLoanee', 'uptoTwoKdepositeNumber', 'TwoKToUptoFiveKdepositeNumber', 'FiveKToUptoTenKdepositeNumber', 'TenKToUptoTwentyKdepositeNumber',
      'TwentyKToMoredepositeNumber', 'uptoTwoKdepositeAmount', 'TwoKToUptoFiveKdepositeAmount', 'FiveKToUptoTenKdepositeAmount', 'TenKToUptoTwentyKdepositeAmount', 'TwentyKToMoredepositeAmount','branchIdArray', 'requestedBranch'));
  }

}
