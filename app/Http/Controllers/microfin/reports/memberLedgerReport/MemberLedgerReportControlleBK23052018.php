<?php

namespace App\Http\Controllers\microfin\reports\memberLedgerReport;

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
class MemberLedgerReportController extends Controller
{

  public function getBranchName(){
    $BranchDatas = DB::table('gnr_branch')->get();
    // $SamityDatas = DB::table('mfn_samity')->get();
    // $ProductCategorys = DB::table('mfn_loans_product_category')->get();
    // $Products = DB::table('mfn_loans_product')->get();

    return view('microfin.reports.memberLedgerReportView.MemberLedgerReportForm', compact('BranchDatas'));
  }

  public function getSamity(Request $request){
    $SamityInfo = DB::table('mfn_samity')
                    ->select('id', 'name', 'code', 'branchId')
                    ->get();
    return response()->json($SamityInfo);
  }

  public function getMember(Request $request){
    $MemberInfo = DB::table('mfn_member_information')
                   ->select('id', 'name', 'samityId', 'primaryProductId', 'code')
                   ->get();
    return response()->json($MemberInfo);
  }

  public function getLoanProduct(Request $request){
    $LoanInfo = DB::table('mfn_loan')
                   ->select('mfn_loan.id', 'mfn_loan.memberIdFk', 'mfn_loans_product.id as loansProductId', 'mfn_loans_product.shortName', 'mfn_loans_product.code')
                   ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                   ->get();
    return response()->json($LoanInfo);
  }

  public function getLoanAccount(Request $request){
    $LoanAccountInfo = DB::table('mfn_loan')
                   ->select('id', 'loanCode', 'memberIdFk')
                   ->get();
    return response()->json($LoanAccountInfo);
  }

  public function getSavingsProduct(Request $request){
    $SavingsProductnInfo = DB::table('mfn_savings_account')
                   ->select('mfn_savings_account.id', 'mfn_savings_account.memberIdFk', 'mfn_saving_product.id as savingsProductId', 'mfn_saving_product.shortName')
                   ->join('mfn_saving_product', 'mfn_savings_account.savingsProductIdFk', '=', 'mfn_saving_product.id')
                   ->get();
    return response()->json($SavingsProductnInfo);
  }

  public function getSavingsAccount(Request $request){
    $SavingsAccountInfo = DB::table('mfn_savings_account')
                   ->select('mfn_savings_account.id', 'mfn_savings_account.memberIdFk', 'mfn_savings_account.savingsCode', 'mfn_saving_product.id as savingsProductId', 'mfn_saving_product.shortName')
                   ->join('mfn_saving_product', 'mfn_savings_account.savingsProductIdFk', '=', 'mfn_saving_product.id')
                   ->get();
    return response()->json($SavingsAccountInfo);
  }


  public function getMemberLedgerTable(Request $request){
    // dd($request);
    $Compare1 = '';
    $Compare2 = '';
    $MargeValue = array();
    $SavingsProductCombinedUniqueInfo = array();
    $data1 = array();
    $date2 = array();
    $Date1 = array();
    $Date2 = array();
    $Date3 = array();
    $Date4 = array();
    $date1 = array();
    $date2 = array();
    $date3 = array();
    $date4 = array();
    $MfnLoanTableInfo = array();
    $SavingsCollectionBalanceBefore = array();
    $MfnLoan = array();
    $WithDrawBalanceAmountForBalance = array();
    $MfnLoanBeforeAmountCollection = array();
    $SavingsSumAmount = array();

    $RequestBranchName = $request->searchBranch;
    $RequestSamityName = $request->searchSamity;
    $RequestMember = $request->searchMember;
    $RequestLoanProduct = $request->product;
    $RequestLoanAccount = $request->LoanAccount;
    $RequestSavingsProduct = $request->SavingsProduct;
    $RequestSavingsAccount = $request->SavingsAccount;
    $RequestFromDate = $request->txtDate;
    $RequestToDate = $request->txtDate1;
    $SearchType = $request->WithOvalue;

    $as = 0;
    $Sum = 0;
    $SumDate = '';

    $MfnLoan = DB::table('mfn_loans_product')
                          ->select('id', 'name', 'shortName', 'code')
                          ->get();

    $BranchInfo = DB::table('gnr_branch')
                    ->select('id', 'branchCode', 'name')
                    ->where('id', $RequestBranchName)
                    ->get();

    $SamityInfo = DB::table('mfn_samity')
                    ->select('id', 'code', 'name', 'branchId')
                    ->where('id', $RequestSamityName)
                    ->get();
                    // dd($SamityInfo);

    $MemberInfo = DB::table('mfn_member_information')
                    ->where('id', $RequestMember)
                    ->get();

    if($RequestSavingsAccount == 100){
      $SavingsProductInfo = DB::table('mfn_savings_account')
                      ->join('mfn_saving_product', 'mfn_savings_account.savingsProductidFk', '=', 'mfn_saving_product.id')
                      ->where('mfn_savings_account.memberIdFk', $RequestMember)
                      ->get();
    }
    else{
      $SavingsProductInfo = DB::table('mfn_savings_account')
                      ->join('mfn_saving_product', 'mfn_savings_account.savingsProductidFk', '=', 'mfn_saving_product.id')
                      ->where('mfn_savings_account.id', $RequestSavingsProduct)
                      ->get();
    }
                    // dd($SavingsProductInfo);

    $SavingsProductnInformations = DB::table('mfn_savings_account')
                   ->distinct('savingsProductIdFk')
                   ->where('memberIdFk', $RequestMember)
                   ->get();
                   // dd($SavingsProductnInfo);

    foreach ($SavingsProductInfo as $key => $SavingsProduct) {
      $memberID = $SavingsProduct->memberIdFk;
      $samityID = $SavingsProduct->samityIdFk;
      $branchID = $SavingsProduct->branchIdFk;
      $SavingsProductID = $SavingsProduct->savingsProductIdFk;
      $savingsCodeID = $SavingsProduct->savingsCode;
      foreach ($SavingsProductnInformations as $key => $SavingsProductnInformation) {
        $SavingsProductIDFK = $SavingsProductnInformation->savingsProductIdFk;
        if($RequestSavingsAccount == 100 and $Compare1 == null){         //If user select all the savings acount at the time of search
          if ($RequestFromDate == null and $RequestToDate == null) {
            $SavingsProductDepositeInfos[$savingsCodeID] = DB::table('mfn_savings_deposit')
                         ->select('productIdFk', 'memberIdFk', 'samityIdFk', 'branchIdFk', 'depositDate', 'amount')
                         ->where('memberIdFk', $memberID)
                         ->where('samityIdFk', $samityID)
                         ->where('branchIdFk', $branchID)
                         ->where('productIdFk', $SavingsProductIDFK)
                         ->get();
           $SavingsProductWithDrawInfos[$savingsCodeID] = DB::table('mfn_savings_withdraw')
                        ->select('productIdFk', 'memberIdFk', 'samityIdFk', 'branchIdFk', 'withdrawDate', 'amount')
                        ->where('memberIdFk', $memberID)
                        ->where('samityIdFk', $samityID)
                        ->where('branchIdFk', $branchID)
                        ->get();
         // $SavingsProductCombinedUniqueInfo = array_merge_recursive($SavingsProductDepositeInfos,$SavingsProductWithDrawInfos);

                      // dd($SavingsProductCombinedUniqueInfo);
          }
          else {
            $SavingsProductDepositeInfos[$savingsCodeID] = DB::table('mfn_savings_deposit')
                         ->select('productIdFk', 'memberIdFk', 'samityIdFk', 'branchIdFk', 'depositDate', 'amount')
                         ->where('memberIdFk', $memberID)
                         ->where('samityIdFk', $samityID)
                         ->where('branchIdFk', $branchID)
                         ->where('depositDate', '>=', $RequestFromDate)
                         ->where('depositDate', '<=', $RequestToDate)
                         ->get();
           $SavingsProductWithDrawInfos[$savingsCodeID] = DB::table('mfn_savings_withdraw')
                        ->select('productIdFk', 'memberIdFk', 'samityIdFk', 'branchIdFk', 'withdrawDate', 'amount')
                        ->where('memberIdFk', $memberID)
                        ->where('samityIdFk', $samityID)
                        ->where('branchIdFk', $branchID)
                        ->where('withdrawDate', '>=', $RequestFromDate)
                        ->where('withdrawDate', '<=', $RequestToDate)
                        ->get();
          $SavingsCollectionBalanceBefore[$savingsCodeID] = DB::table('mfn_savings_deposit')
                       // ->select('depositDate','balanceBeforeDeposit')
                       ->where('memberIdFk', $memberID)
                       ->where('samityIdFk', $samityID)
                       ->where('branchIdFk', $branchID)
                       ->where('depositDate', '<', $RequestFromDate)
                       ->where('productIdFk', $SavingsProductID)
                       // ->orderBy('depositDate', 'desc')
                       // ->limit(1)
                       // ->get();
                       ->sum('amount');
         $WithDrawBalanceAmountForBalance[$savingsCodeID] = DB::table('mfn_savings_withdraw')
                       ->select('withdrawDate','balanceBeforeWithdraw')
                       ->where('memberIdFk', $memberID)
                       ->where('samityIdFk', $samityID)
                       ->where('branchIdFk', $branchID)
                       ->where('withdrawdate', '>=', $RequestFromDate)
                       ->where('productIdFk', $SavingsProductID)
                       ->limit(1)
                       ->get();
          }
        }
        else {
          if ($RequestFromDate == null and $RequestToDate == null) {
            $SavingsProductDepositeInfos[$savingsCodeID] = DB::table('mfn_savings_deposit')
                         ->select('productIdFk', 'memberIdFk', 'samityIdFk', 'branchIdFk', 'depositDate', 'amount')
                         ->where('memberIdFk', $memberID)
                         ->where('samityIdFk', $samityID)
                         ->where('branchIdFk', $branchID)
                         ->where('productIdFk', $SavingsProductID)
                         ->get();
           $SavingsProductWithDrawInfos[$savingsCodeID] = DB::table('mfn_savings_withdraw')
                        ->select('productIdFk', 'memberIdFk', 'samityIdFk', 'branchIdFk', 'withdrawDate', 'amount')
                        ->where('memberIdFk', $memberID)
                        ->where('samityIdFk', $samityID)
                        ->where('branchIdFk', $branchID)
                        ->where('productIdFk', $SavingsProductID)
                        ->get();
          }
          else {
            $SavingsProductDepositeInfos[$savingsCodeID] = DB::table('mfn_savings_deposit')
                         ->select('productIdFk', 'memberIdFk', 'samityIdFk', 'branchIdFk', 'depositDate', 'amount')
                         ->where('memberIdFk', $memberID)
                         ->where('samityIdFk', $samityID)
                         ->where('branchIdFk', $branchID)
                         ->where('productIdFk', $SavingsProductID)
                         ->where('depositDate', '>=', $RequestFromDate)
                         ->where('depositDate', '<=', $RequestToDate)
                         ->get();
           $SavingsProductWithDrawInfos[$savingsCodeID] = DB::table('mfn_savings_withdraw')
                        ->select('productIdFk', 'memberIdFk', 'samityIdFk', 'branchIdFk', 'withdrawDate', 'amount')
                        ->where('memberIdFk', $memberID)
                        ->where('samityIdFk', $samityID)
                        ->where('branchIdFk', $branchID)
                        ->where('productIdFk', $SavingsProductID)
                        ->where('withdrawDate', '>=', $RequestFromDate)
                        ->where('withdrawDate', '<=', $RequestToDate)
                        ->get();
          $SavingsCollectionBalanceBefore[$savingsCodeID] = DB::table('mfn_savings_deposit')
                       // ->select('depositDate','balanceBeforeDeposit')
                       ->where('memberIdFk', $memberID)
                       ->where('samityIdFk', $samityID)
                       ->where('branchIdFk', $branchID)
                       ->where('depositDate', '<', $RequestFromDate)
                       ->where('productIdFk', $SavingsProductID)
                       // ->orderBy('depositDate', 'desc')
                       // ->limit(1)
                       // ->get();
                       ->sum('amount');

         $WithDrawBalanceAmountForBalance[$savingsCodeID] = DB::table('mfn_savings_withdraw')
                       ->select('withdrawDate','balanceBeforeWithdraw')
                       ->where('memberIdFk', $memberID)
                       ->where('samityIdFk', $samityID)
                       ->where('branchIdFk', $branchID)
                       ->where('withdrawdate', '>=', $RequestFromDate)
                       ->where('productIdFk', $SavingsProductID)
                       ->limit(1)
                       ->get();
          }
        }
        $Compare1 = $SavingsProductIDFK;

        $SavingsProductCombinedUniqueInfo = array_merge_recursive($SavingsProductDepositeInfos,$SavingsProductWithDrawInfos);

        foreach ($SavingsProductCombinedUniqueInfo as $key => $SavingsProductCombinedUniqueInfos){
          foreach ($SavingsProductCombinedUniqueInfos as $key => $SavingsProductCombinedUniqueInfoss){
            $data[$savingsCodeID] = array($SavingsProductCombinedUniqueInfoss);
          }
        }
      }

      $Compare1 = '';
    }
    // dd($data);
    // dd(sizeof($SavingsProductDepositeInfos));

    if ($SavingsProductWithDrawInfos != null) {
      // if ($SavingsProductDepositeInfos != null) {
        foreach ($SavingsProductDepositeInfos as $key => $SavingsProductDepositeInfo) {
          foreach ($SavingsProductDepositeInfo as $key1 => $SavingsProductDepositeInfoss) {
            $date1[] = $SavingsProductDepositeInfoss->depositDate;
          }
        }

        foreach ($SavingsProductWithDrawInfos as $key => $SavingsProductWithDrawInfo) {
          foreach ($SavingsProductWithDrawInfo as $key => $SavingsProductWithDrawInfoss) {
            $date2[] = $SavingsProductWithDrawInfoss->withdrawDate;
          }
      // }
    }

    // dd($date1);

      // if ($date1 != null and $date2 != null) {
        $Date3 = array_merge($date1,$date2);
        usort($Date3, function ($time1, $time2){
          if (strtotime($time1) > strtotime($time2))
              return 1;
          else if (strtotime($time1) < strtotime($time2))
              return -1;
          else
              return 0;
        });

        $date4 = array_unique($Date3);
      // }
      // dd($date4);

    }

    // dd($SavingsProductDepositeInfos);
    // dd($SavingsCollectionBalanceBefore);

    // Loan Table Program start from here

    if ($RequestLoanAccount !=null) {
      if ($RequestLoanAccount == 100) {
        if ($RequestFromDate == null and $RequestToDate == null){

          $MfnLoanTableInfo = DB::table('mfn_loan')
                              ->select('mfn_loans_product.name', 'mfn_loans_product.code', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount')
                              ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                              ->where('mfn_loan.memberIdFk', $RequestMember)
                              ->where('mfn_loan.samityIdFk', $RequestSamityName)
                              ->where('mfn_loan.branchIdFk', $RequestBranchName)
                              ->get();

          $MfnLoanCollectionInfo = DB::table('mfn_loan')
                              ->select('mfn_loan_collection.collectionDate', 'mfn_loan_collection.principalAmount', 'mfn_loan_collection.interestAmount', 'mfn_loan.totalRepayAmount')
                              ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                              ->where('mfn_loan.memberIdFk', $RequestMember)
                              ->where('mfn_loan.samityIdFk', $RequestSamityName)
                              ->where('mfn_loan.branchIdFk', $RequestBranchName)
                              ->get();

          $MfnLoanRebateInfo = DB::table('mfn_loan')
                              ->select('mfn_loan_rebates.amount', 'mfn_loan_rebates.date')
                              ->join('mfn_loan_rebates', 'mfn_loan.id', '=', 'mfn_loan_rebates.loanIdFk')
                              ->where('mfn_loan.memberIdFk', $RequestMember)
                              ->where('mfn_loan.samityIdFk', $RequestSamityName)
                              ->where('mfn_loan.branchIdFk', $RequestBranchName)
                              ->get();
        }
        else {

          $MfnLoanTableInfo = DB::table('mfn_loan')
                              ->select('mfn_loans_product.name', 'mfn_loans_product.code', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount')
                              ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                              ->where('mfn_loan.memberIdFk', $RequestMember)
                              ->where('mfn_loan.samityIdFk', $RequestSamityName)
                              ->where('mfn_loan.branchIdFk', $RequestBranchName)
                              ->get();

          $MfnLoanCollectionInfo = DB::table('mfn_loan')
                              ->select('mfn_loan_collection.collectionDate', 'mfn_loan_collection.principalAmount', 'mfn_loan_collection.interestAmount', 'mfn_loan.totalRepayAmount')
                              ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                              ->where('mfn_loan.memberIdFk', $RequestMember)
                              ->where('mfn_loan.samityIdFk', $RequestSamityName)
                              ->where('mfn_loan.branchIdFk', $RequestBranchName)
                              ->where('mfn_loan_collection.collectionDate', '>=', $RequestFromDate)
                              ->where('mfn_loan_collection.collectionDate', '<=', $RequestToDate)
                              ->get();

          $MfnLoanRebateInfo = DB::table('mfn_loan')
                              ->select('mfn_loan_rebates.amount', 'mfn_loan_rebates.date')
                              ->join('mfn_loan_rebates', 'mfn_loan.id', '=', 'mfn_loan_rebates.loanIdFk')
                              ->where('mfn_loan.memberIdFk', $RequestMember)
                              ->where('mfn_loan.samityIdFk', $RequestSamityName)
                              ->where('mfn_loan.branchIdFk', $RequestBranchName)
                              ->get();

           $MfnLoanBeforeAmountCollection = DB::table('mfn_loan_collection')
                               ->where('memberIdFk', $RequestMember)
                               ->where('samityIdFk', $RequestSamityName)
                               ->where('branchIdFk', $RequestBranchName)
                               ->where('collectionDate', '<', $RequestFromDate)
                               ->sum('amount');
        }
      }
      else {
        if ($RequestFromDate == null and $RequestToDate == null){

          $MfnLoanTableInfo = DB::table('mfn_loan')
                              ->select('mfn_loans_product.name', 'mfn_loans_product.code', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount')
                              ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                              ->where('mfn_loan.memberIdFk', $RequestMember)
                              ->where('mfn_loan.samityIdFk', $RequestSamityName)
                              ->where('mfn_loan.branchIdFk', $RequestBranchName)
                              ->get();

          $MfnLoanCollectionInfo = DB::table('mfn_loan')
                              ->select('mfn_loan_collection.collectionDate', 'mfn_loan_collection.principalAmount', 'mfn_loan_collection.interestAmount', 'mfn_loan.totalRepayAmount')
                              ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                              ->where('mfn_loan.memberIdFk', $RequestMember)
                              ->where('mfn_loan.samityIdFk', $RequestSamityName)
                              ->where('mfn_loan.branchIdFk', $RequestBranchName)
                              ->get();

          $MfnLoanRebateInfo = DB::table('mfn_loan')
                              ->select('mfn_loan_rebates.amount', 'mfn_loan_rebates.date')
                              ->join('mfn_loan_rebates', 'mfn_loan.id', '=', 'mfn_loan_rebates.loanIdFk')
                              ->where('mfn_loan.memberIdFk', $RequestMember)
                              ->where('mfn_loan.samityIdFk', $RequestSamityName)
                              ->where('mfn_loan.branchIdFk', $RequestBranchName)
                              ->get();
        }
        else {

          $MfnLoanTableInfo = DB::table('mfn_loan')
                              ->select('mfn_loans_product.name', 'mfn_loans_product.code', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount')
                              ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                              ->where('mfn_loan.memberIdFk', $RequestMember)
                              ->where('mfn_loan.samityIdFk', $RequestSamityName)
                              ->where('mfn_loan.branchIdFk', $RequestBranchName)
                              ->get();

          $MfnLoanCollectionInfo = DB::table('mfn_loan')
                              ->select('mfn_loan_collection.collectionDate', 'mfn_loan_collection.principalAmount', 'mfn_loan_collection.interestAmount', 'mfn_loan.totalRepayAmount')
                              ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                              ->where('mfn_loan.memberIdFk', $RequestMember)
                              ->where('mfn_loan.samityIdFk', $RequestSamityName)
                              ->where('mfn_loan.branchIdFk', $RequestBranchName)
                              ->where('mfn_loan_collection.collectionDate', '>=', $RequestFromDate)
                              ->where('mfn_loan_collection.collectionDate', '<=', $RequestToDate)
                              ->get();

          $MfnLoanRebateInfo = DB::table('mfn_loan')
                              ->select('mfn_loan_rebates.amount', 'mfn_loan_rebates.date')
                              ->join('mfn_loan_rebates', 'mfn_loan.id', '=', 'mfn_loan_rebates.loanIdFk')
                              ->where('mfn_loan.memberIdFk', $RequestMember)
                              ->where('mfn_loan.samityIdFk', $RequestSamityName)
                              ->where('mfn_loan.branchIdFk', $RequestBranchName)
                              ->get();

          $MfnLoanBeforeAmountCollection = DB::table('mfn_loan_collection')
                              ->where('memberIdFk', $RequestMember)
                              ->where('samityIdFk', $RequestSamityName)
                              ->where('branchIdFk', $RequestBranchName)
                              ->where('collectionDate', '<', $RequestFromDate)
                              ->sum('amount');
        }
      }
    }

    // dd($SavingsProductWithDrawInfos);
    // dd($SavingsProductDepositeInfos);
    // var_dump($MfnLoanRebateInfo);

    return view('microfin.reports.memberLedgerReportView.MemberLedgerReportTable', compact('RequestBranchName', 'RequestSamityName', 'RequestFromDate', 'RequestToDate', 'BranchInfo', 'SamityInfo', 'MemberInfo', 'SavingsProductInfo', 'SavingsProductDepositeInfos', 'SavingsProductWithDrawInfos', 'data', 'SavingsProductWithDrawInfos', 'date4', 'MfnLoanTableInfo', 'MfnLoanCollectionInfo', 'MfnLoanRebateInfo', 'RequestLoanAccount', 'SavingsCollectionBalanceBefore', 'Date3', 'MfnLoan', 'WithDrawBalanceAmountForBalance', 'MfnLoanBeforeAmountCollection', 'SearchType'));

  }

}
