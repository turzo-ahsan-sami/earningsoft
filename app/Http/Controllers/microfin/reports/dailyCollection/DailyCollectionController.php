<?php

namespace App\Http\Controllers\microfin\reports\dailyCollection;

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
class DailyCollectionController extends Controller
{

  public function getBranchName(){
    $BranchDatas = DB::table('gnr_branch')->get();
    $ProductCategorys = DB::table('mfn_loans_product_category')->get();
    $Products = DB::table('mfn_loans_product')->get();

    return view('microfin.reports.mfnDailyCollectionReport.dailyCollectionReport', compact('BranchDatas', 'ProductCategorys', 'Products'));
  }

  public function getTable(){
    return view('microfin.reports.mfnDailyCollectionReport.dailyCollectionComponentWiseReportTable');
  }

  public function getBranchOfficer(Request $request){

  }

  public function getComponent(Request $request){
    $Prod = DB::table('mfn_loans_product')
                    ->select('id', 'name', 'shortName', 'productCategoryId')
                    ->get();
    return response()->json($Prod);
  }

  public function getFieldOfficerReport(Request $request){
    // dd($request);
    $DN = 0;
    $AllComponent = array();
    $Samities = array();
    $TotalMemberByProduct = array();
    $TotalComponents = array();
    $SamityComponents = array();
    $LoanDisbursements = array();
    $SavingsRecoverableValues = array();
    $MonthlyCollectionValues = array();
    $MonthlyCollectionSplitValues = array();
    $MonthlyCollectionComparedValues = array();
    // $LoanIdPicker = array();
    // $FieldOffcers = array();

    $BranchId = $request->searchBranch;
    // dd($BranchId);
    $ProductType = $request->productCtg;
    $Product = $request->product;
    $ReportDate = $request->txtDate;
    $SavingsRecoverable = $request->savingsRecoverable;
    // dd($ReportDate);

    $LoanDisbursementsTotal = 0;

    $MonthlyCollectionValues = DB::table('mfn_saving_monthly_collection_type')
                                      ->select('id','value')
                                      // ->join('mfn_saving_monthly_collection_type', 'mfn_saving_product.monthlyCollectionTypeIdFk', '=', 'mfn_saving_monthly_collection_type.id')
                                      // ->where('mfn_saving_product.monthlyCollectionTypeIdFk', '=', 'mfn_saving_monthly_collection_type.id')
                                      ->where('id', '=', 1)
                                      ->get();

    foreach ($MonthlyCollectionValues as $key => $MonthlyCollectionValue) {
      $MonthlyCollectionSplitValues[] = explode(',', $MonthlyCollectionValue->value);
    }

    foreach ($MonthlyCollectionSplitValues as $key1 => $MonthlyCollectionSplitValue) {
      foreach ($MonthlyCollectionSplitValue as $key2 => $MonthlyCollectionSplitedValue) {
        $MonthlyCollectionComparedValues[] = DB::table('mfn_saving_product')
                                          ->select('mfn_saving_product.id', 'mfn_saving_product.monthlyCollectionTypeIdValueIndex')
                                          ->join('mfn_saving_monthly_collection_type', 'mfn_saving_product.monthlyCollectionTypeIdFk', '=', 'mfn_saving_monthly_collection_type.id')
                                          ->where('mfn_saving_product.monthlyCollectionTypeIdFk', '=', $key2)
                                          ->get();
      }
    }

    $mfnSavingsProducts = DB::table('mfn_saving_product')
                            ->select('id', 'name', 'shortName')
                            ->get();
    // dd($mfnSavingsProducts);

    $BrDatas = DB::table('gnr_branch')
                      ->select('branchCode', 'name', 'address', 'savingsProductId', 'loanProductId')
                      ->where('id', $BranchId)
                      ->first();
    // dd($BrDatas);
    $BranchSavingsProducts[] = explode('"',$BrDatas->loanProductId);
    // dd($BranchSavingsProducts);

    $a = 0;
    $BLProducts = array();

    foreach ($BranchSavingsProducts as $BranchSavingsProduct) {
      // $a = sizeof($BranchSavingsProduct);
      for ($i=0; $i <= sizeof($BranchSavingsProduct)+4; $i++) {
        if($BranchSavingsProduct[$i] == null){
          $BLProducts[] = $BranchSavingsProduct[$i];
          break;
        }
        else{
          if($BranchSavingsProduct[$i] == '[' || $BranchSavingsProduct[$i] == ',' || $BranchSavingsProduct[$i] == ']'){
            unset($BranchSavingsProduct[$i]);
            // $BLProducts1[] = $BranchSavingsProduct[$i];
            $a = $a + 1;
          }
          else {
            $BLProducts[] = $BranchSavingsProduct[$i];
            $a = $a + 1;
          }
        }
      }
    }
    // dd($BLProducts);
    // dd($a);
    // dd($FinalBranchSavingsProducts);

    $Samities = DB::table('mfn_samity')
                  ->select('id', 'code', 'name', 'fieldOfficerId')
                  ->where('branchId', $BranchId)
                  ->get();
                  // dd($Samities);
    $UniqueFieldOfficers = DB::table('mfn_samity')
                              ->select(DB::raw('DISTINCT(fieldOfficerId) as fid'))
                              ->where('branchId', $BranchId)
                              ->get();

    foreach ($UniqueFieldOfficers as $UniqueFieldOfficer) {
      $Eid = $UniqueFieldOfficer->fid;
      $FieldOffcers[] = DB::table('hr_emp_general_info')
                        ->select('id', 'emp_id', 'emp_name_english')
                        ->where('id', $Eid)
                        ->get();
    }

    if ($BLProducts != null) {

      if($ProductType < 100 && $Product < 100) { //Product Type & Product Selected (OK) (OK)
        $Component = DB::table('mfn_loans_product')
                        ->select('id','shortName')
                        ->where('id', $Product)
                        ->first();
      }
      elseif ($ProductType == 100 && $Product < 100) { //Product Type All but Product Selected (OK) (OK)
        $Component = DB::table('mfn_loans_product')
                        ->select('id','shortName')
                        ->where('id', $Product)
                        ->first();
      }
      elseif ($ProductType < 100 && $Product == 100) { //Product Type Selected but Product All (OK) (OK)
        $Component = DB::table('mfn_loans_product')
                        ->select('id','shortName')
                        ->where('productCategoryId', $ProductType)
                        ->get();
      }
      elseif($ProductType == 100 && $Product == 100){ //Product Type & Product All (OK)
        $Component = DB::table('mfn_loans_product')
                        ->select('id','shortName')
                        // ->where('id', $ProductType)
                        ->get();
      }
      // dd($Component);
      // dd($BLProducts);
      // dd($Component);

      if($ProductType < 100 && $Product < 100) { //Product Type & Product Selected (OK) (OK)
        $ShowProduct = $Product;
      }
      elseif ($ProductType == 100 && $Product < 100) { //Product Type All but Product Selected (OK) (OK)
        $ShowProduct = $Product;
      }
      elseif ($ProductType < 100 && $Product == 100) { //Product Type Selected but Product All (OK) (OK)
        $ShowProduct = 'All';
      }
      elseif($ProductType == 100 && $Product == 100){ //Product Type & Product All (OK)
        $ShowProduct = 'All';
      }

      $I = 0;
      foreach ($BLProducts as $BLProduct) {
        if (sizeof($Component) == 1) {
          // foreach ($Component as $Components) {
            if ($BLProduct == $Component->id) {
              $TotalComponents[$i]['id'] = $Component->id;
              $TotalComponents[$i]['shortName'] = $Component->id;
            }
          // }
        }
        else {
          foreach ($Component as $Components) {
            if ($BLProduct == $Components->id) {
              $TotalComponents[$I]['id'] = $Components->id;
              $TotalComponents[$I]['shortName'] = $Components->shortName;
              $I = $I + 1;
            }
          }
        }
      }
      // dd($TotalComponents);

      foreach ($TotalComponents as $TotalComponent) {  //Need to adjust the loop to get the proper values......
        $SamityComponents[] = DB::table('mfn_loans_product')
                                ->select('id','name', 'shortName')
                                ->where('id', $TotalComponent)
                                ->get();
      }
      // dd($SamityComponents);

      $LoanIdPicker[] = DB::table('mfn_loan_schedule')
                                  ->select('loanIdFk')
                                  ->where('scheduleDate', $ReportDate)
                                  ->get();
      // dd($LoanIdPicker);

      foreach ($LoanIdPicker as $LoanIdPick) {
        foreach ($LoanIdPick as $LoanIdP) {
          $idPick[] = $LoanIdP->loanIdFk;
        }
      }
      // dd($idPick);

      $compoSize = sizeof($Component);
      $a = 1;
      $b = 1;
      $as = array();
      $as1 = array();
      foreach ($Samities as $Samity) {
        // var_dump($Samity);
        $Sname = $Samity->name;
        $SId = $Samity->id;
        $as[] = $SId;
        if (sizeof($Component) == 1) {
          $Cname = $Component->shortName;
          $CId = $Component->id;
          $as1[] = $CId;
          $TotalMemberByProduct[$SId][$CId] = DB::table('mfn_loan')
                                      // ->select('memberIdFk','savingsProductIdFk', 'samityIdFk')
                                      // ->select(DB::raw('COUNT(id) as TM'))
                                      ->where('branchIdFk', $BranchId)
                                      ->where('samityIdFk', $SId)
                                      ->where('primaryProductIdFk', $CId)
                                      ->count();
          $TotalLoanByProduct[$SId][$CId] = DB::table('mfn_loan')
                                      // ->select('memberIdFk','savingsProductIdFk', 'samityIdFk')
                                      // ->select(DB::raw('COUNT(id) as TM'))
                                      ->where('branchIdFk', $BranchId)
                                      ->where('samityIdFk', $SId)
                                      ->where('primaryProductIdFk', $CId)
                                      ->count();
          $InstallmentPayer[$SId][$CId] = DB::table('mfn_loan')
                                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                                            ->where('mfn_loan_schedule.scheduleDate', $ReportDate)
                                            // ->where('mfn_loan_schedule.loanIdFk', $idPicks)
                                            ->where('mfn_loan.productIdFk', $CId)
                                            ->count();
          // dd($InstallmentPayer);
          $InstallmentDue[$SId][$CId] = DB::table('mfn_loan')
                                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                                            ->where('mfn_loan_schedule.scheduleDate', $ReportDate)
                                            // ->where('mfn_loan_schedule.loanIdFk', $idPicks)
                                            ->where('mfn_loan_schedule.isCompleted', '<', 1)
                                            ->where('mfn_loan.productIdFk', $CId)
                                            ->where('mfn_loan.branchIdFk', $BranchId)
                                            ->where('mfn_loan.samityIdFk', $SId)
                                            ->count();
          // }
          $SavingsDeposite[$SId][$CId] = DB::table('mfn_savings_deposit')
                                            // ->select('mfn_savings_deposit.amount')
                                            // ->selectRaw('SUM(mfn_savings_deposit.amount) as Total')
                                            ->join('mfn_savings_account', 'mfn_savings_deposit.accountIdFk', '=', 'mfn_savings_account.id')
                                            ->where('mfn_savings_deposit.depositDate', $ReportDate)
                                            ->where('mfn_savings_deposit.primaryProductIdFk', $CId)
                                            ->where('mfn_savings_deposit.samityIdFk', '=', $SId)
                                            ->where('mfn_savings_deposit.productIdFk', '=', 1)
                                            ->sum('mfn_savings_deposit.amount');
          $SavingsDeposite1[$SId][$CId] = DB::table('mfn_savings_deposit')
                                            // ->select(DB::raw('SUM(mfn_savings_deposit.amount) as Total'))
                                            // ->selectRaw('SUM(mfn_savings_deposit.amount) as Total')
                                            ->join('mfn_savings_account', 'mfn_savings_deposit.accountIdFk', '=', 'mfn_savings_account.id')
                                            ->where('mfn_savings_deposit.depositDate', $ReportDate)
                                            ->where('mfn_savings_deposit.primaryProductIdFk', $CId)
                                            ->where('mfn_savings_deposit.samityIdFk', '=', $SId)
                                            ->where('mfn_savings_deposit.productIdFk', '=', 2)
                                            ->sum('mfn_savings_deposit.amount');
          $SavingsDeposite2[$SId][$CId] = DB::table('mfn_savings_deposit')
                                            // ->select(DB::raw('SUM(mfn_savings_deposit.amount) as Total'))
                                            // ->selectRaw('SUM(mfn_savings_deposit.amount) as Total')
                                            ->join('mfn_savings_account', 'mfn_savings_deposit.accountIdFk', '=', 'mfn_savings_account.id')
                                            ->where('mfn_savings_deposit.depositDate', $ReportDate)
                                            ->where('mfn_savings_deposit.primaryProductIdFk', $CId)
                                            ->where('mfn_savings_deposit.samityIdFk', '=', $SId)
                                            ->where('mfn_savings_deposit.productIdFk', '=', 3)
                                            ->sum('mfn_savings_deposit.amount');
          $SavingsDeposite3[$SId][$CId] = DB::table('mfn_savings_deposit')
                                            // ->select(DB::raw('SUM(mfn_savings_deposit.amount) as Total'))
                                            // ->selectRaw('SUM(mfn_savings_deposit.amount) as Total')
                                            ->join('mfn_savings_account', 'mfn_savings_deposit.accountIdFk', '=', 'mfn_savings_account.id')
                                            ->where('mfn_savings_deposit.depositDate', $ReportDate)
                                            ->where('mfn_savings_deposit.primaryProductIdFk', $CId)
                                            ->where('mfn_savings_deposit.samityIdFk', '=', $SId)
                                            ->where('mfn_savings_deposit.productIdFk', '=', 4)
                                            ->sum('mfn_savings_deposit.amount');
          $SavingsWithdraw[$SId][$CId] = DB::table('mfn_savings_withdraw')
                                            // ->select('mfn_savings_deposit.amount')
                                            // ->selectRaw('SUM(mfn_savings_deposit.amount) as Total')
                                            ->join('mfn_savings_account', 'mfn_savings_withdraw.accountIdFk', '=', 'mfn_savings_account.id')
                                            ->where('mfn_savings_withdraw.withdrawDate', $ReportDate)
                                            ->where('mfn_savings_withdraw.primaryProductIdFk', $CId)
                                            ->where('mfn_savings_withdraw.samityIdFk', '=', $SId)
                                            ->where('mfn_savings_withdraw.productIdFk', '=', 1)
                                            ->sum('mfn_savings_withdraw.amount');
          $SavingsWithdraw1[$SId][$CId] = DB::table('mfn_savings_withdraw')
                                            // ->select(DB::raw('SUM(mfn_savings_deposit.amount) as Total'))
                                            // ->selectRaw('SUM(mfn_savings_deposit.amount) as Total')
                                            ->join('mfn_savings_account', 'mfn_savings_withdraw.accountIdFk', '=', 'mfn_savings_account.id')
                                            ->where('mfn_savings_withdraw.withdrawDate', $ReportDate)
                                            ->where('mfn_savings_withdraw.primaryProductIdFk', $CId)
                                            ->where('mfn_savings_withdraw.samityIdFk', '=', $SId)
                                            ->where('mfn_savings_withdraw.productIdFk', '=', 2)
                                            ->sum('mfn_savings_withdraw.amount');
          $SavingsWithdraw2[$SId][$CId] = DB::table('mfn_savings_withdraw')
                                            // ->select(DB::raw('SUM(mfn_savings_deposit.amount) as Total'))
                                            // ->selectRaw('SUM(mfn_savings_deposit.amount) as Total')
                                            ->join('mfn_savings_account', 'mfn_savings_withdraw.accountIdFk', '=', 'mfn_savings_account.id')
                                            ->where('mfn_savings_withdraw.withdrawDate', $ReportDate)
                                            ->where('mfn_savings_withdraw.primaryProductIdFk', $CId)
                                            ->where('mfn_savings_withdraw.samityIdFk', '=', $SId)
                                            ->where('mfn_savings_withdraw.productIdFk', '=', 3)
                                            ->sum('mfn_savings_withdraw.amount');
          $SavingsWithdraw3[$SId][$CId] = DB::table('mfn_savings_withdraw')
                                            // ->select(DB::raw('SUM(mfn_savings_deposit.amount) as Total'))
                                            // ->selectRaw('SUM(mfn_savings_deposit.amount) as Total')
                                            ->join('mfn_savings_account', 'mfn_savings_withdraw.accountIdFk', '=', 'mfn_savings_account.id')
                                            ->where('mfn_savings_withdraw.withdrawDate', $ReportDate)
                                            ->where('mfn_savings_withdraw.primaryProductIdFk', $CId)
                                            ->where('mfn_savings_withdraw.samityIdFk', '=', $SId)
                                            ->where('mfn_savings_withdraw.productIdFk', '=', 4)
                                            ->sum('mfn_savings_withdraw.amount');
          $LoanDisbursements[$SId][$CId] = DB::table('mfn_loan')
                                            ->where('mfn_loan.primaryProductIdFk', $CId)
                                            ->where('mfn_loan.samityIdFk', '=', $SId)
                                            ->where('mfn_loan.productIdFk', '=', 1)
                                            ->where('mfn_loan.disbursementDate', $ReportDate)
                                            ->get();
                                            // dd($LoanDisbursements);
          // $LoanDisbursementsTotal = 0;
          if (sizeof($LoanDisbursements) > 0) {
            foreach ($LoanDisbursements as $LoanDisbursemen) {
              $LoanDisbursementsTotal = $LoanDisbursementsTotal + array_sum($LoanDisbursemen);
            }
          }

          // $LoanDisbursementsTotal = array_sum($abc);
          $LoanDisbursementsAmount[$SId][$CId] = DB::table('mfn_loan')
                                            ->where('primaryProductIdFk', $CId)
                                            ->where('samityIdFk', $SId)
                                            ->where('branchIdFk', $BranchId)
                                            ->where('disbursementDate', $ReportDate)
                                            ->sum('loanAmount');
          $FullyPaymentLoan[$SId][$CId] = DB::table('mfn_loan')
                                            ->where('primaryProductIdFk', $CId)
                                            ->where('samityIdFk', $SId)
                                            ->where('branchIdFk', $BranchId)
                                            ->where('loanCompletedDate', $ReportDate)
                                            ->where('isLoanCompleted', '=', 1)
                                            ->count();
          $FullyPaymentLoanAmount[$SId][$CId] = DB::table('mfn_loan')
                                            ->where('primaryProductIdFk', $CId)
                                            ->where('samityIdFk', $SId)
                                            ->where('branchIdFk', $BranchId)
                                            ->where('loanCompletedDate', $ReportDate)
                                            ->where('isLoanCompleted', '=', 1)
                                            ->sum('loanAmount');
          $AdditionalFeeCollection[$SId][$CId] = DB::table('mfn_loan')
                                            ->where('primaryProductIdFk', $CId)
                                            ->where('samityIdFk', $SId)
                                            ->where('branchIdFk', $BranchId)
                                            ->where('disbursementDate', $ReportDate)
                                            ->sum('additionalFee');

          $LoanRecoverable[$SId][$CId] = DB::table('mfn_loan_schedule')
                                            // ->select('mfn_loan_schedule.loanIdFk','mfn_loan_schedule.installmentAmount','mfn_loan_schedule.scheduleDate')
                                            ->join('mfn_loan', 'mfn_loan_schedule.loanIdFk', '=', 'mfn_loan.id')
                                            ->where('mfn_loan.primaryProductIdFk', $CId)
                                            ->where('mfn_loan.samityIdFk', $SId)
                                            ->where('mfn_loan.branchIdFk', $BranchId)
                                            ->where('mfn_loan_schedule.scheduleDate', $ReportDate)
                                            // ->get();
                                            ->sum('mfn_loan_schedule.installmentAmount');
          $RegularLoan[$SId][$CId] = DB::table('mfn_loan_collection')
                                            ->where('primaryProductIdFk', $CId)
                                            ->where('samityIdFk', $SId)
                                            ->where('branchIdFk', $BranchId)
                                            ->where('collectionDate', $ReportDate)
                                            ->sum('amount');
          $RegularLoan1[$SId][$CId] = DB::table('mfn_loan_collection')
                                            ->select('amount', 'collectionDate', 'loanIdFk', 'principalAmount', 'interestAmount')
                                            ->where('primaryProductIdFk', $CId)
                                            ->where('samityIdFk', $SId)
                                            ->where('branchIdFk', $BranchId)
                                            ->where('collectionDate', $ReportDate)
                                            ->get();
          // $PreviousDatesLoanRecoverable[$SId][$CId] = DB::table('mfn_loan_schedule')
          //                                   // ->select('mfn_loan_schedule.loanIdFk','mfn_loan_schedule.installmentAmount')
          //                                   ->join('mfn_loan', 'mfn_loan_schedule.loanIdFk', '=', 'mfn_loan.id')
          //                                   ->where('mfn_loan.primaryProductIdFk', $CId)
          //                                   ->where('mfn_loan.samityIdFk', $SId)
          //                                   ->where('mfn_loan.branchIdFk', $BranchId)
          //                                   ->where('mfn_loan_schedule.scheduleDate', '<', $ReportDate)
          //                                   // ->get();
          //                                   ->sum('mfn_loan_schedule.installmentAmount');
          // $PreviousDatesCollection[$SId][$CId] = DB::table('mfn_loan_collection')
          //                                   ->where('primaryProductIdFk', $CId)
          //                                   ->where('samityIdFk', $SId)
          //                                   ->where('branchIdFk', $BranchId)
          //                                   ->where('collectionDate', '<', $ReportDate)
          //                                   ->sum('amount');
          $PreviousLoanCollectionId[$SId][$CId] = DB::table('mfn_loan_collection')
                                            ->select('amount', 'collectionDate', 'loanIdFk')
                                            ->where('primaryProductIdFk', $CId)
                                            ->where('samityIdFk', $SId)
                                            ->where('branchIdFk', $BranchId)
                                            ->where('collectionDate', '<', $ReportDate)
                                            ->get();
          $InstallmentAmountById[$SId][$CId] = DB::table('mfn_loan')
                                            ->select('installmentAmount', 'id')
                                            ->where('primaryProductIdFk', $CId)
                                            ->where('samityIdFk', $SId)
                                            ->where('branchIdFk', $BranchId)
                                            ->get();

          $loanRebate[$SId][$CId] = DB::table('mfn_loan_rebates')
                                            // ->select('amount')
                                            ->where('primaryProductIdFk', $CId)
                                            ->where('samityIdFk', $SId)
                                            ->where('branchIdFk', $BranchId)
                                            ->sum('amount');
          $InsurancePremium[$SId][$CId] = DB::table('mfn_loan')
                                            ->select('insuranceAmount')
                                            ->where('primaryProductIdFk', $CId)
                                            ->where('samityIdFk', $SId)
                                            ->where('branchIdFk', $BranchId)
                                            ->where('disbursementDate', $ReportDate)
                                            ->get();

        }
        else {
          foreach ($TotalComponents as $TotalComponent) {
            $Cname = $TotalComponent['shortName'];
            $CId = $TotalComponent['id'];
            $as1[] = $CId;
            $MembersByProduct[$SId][$CId] = DB::table('mfn_loan')
                                        ->select('memberIdFk')
                                        ->where('branchIdFk', $BranchId)
                                        ->where('samityIdFk', $SId)
                                        ->where('primaryProductIdFk', $CId)
                                        ->get();

            $TotalMemberByProduct[$SId][$CId] = DB::table('mfn_loan')
                                        // ->select('memberIdFk','savingsProductIdFk', 'samityIdFk')
                                        // ->select(DB::raw('COUNT(id) as TM'))
                                        ->where('branchIdFk', $BranchId)
                                        ->where('samityIdFk', $SId)
                                        ->where('primaryProductIdFk', $CId)
                                        ->count();
            $TotalLoanByProduct[$SId][$CId] = DB::table('mfn_loan')
                                        // ->select('id')
                                        // ->select(DB::raw('COUNT(id) as TM'))
                                        ->where('branchIdFk', $BranchId)
                                        ->where('samityIdFk', $SId)
                                        ->where('primaryProductIdFk', $CId)
                                        ->count();
          // foreach ($idPick as $idPicks) {
            $InstallmentPayer[$SId][$CId] = DB::table('mfn_loan')
                                              ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                                              ->where('mfn_loan_schedule.scheduleDate', $ReportDate)
                                              // ->where('mfn_loan_schedule.loanIdFk', $idPicks)
                                              ->where('mfn_loan.branchIdFk', $BranchId)
                                              ->where('mfn_loan.samityIdFk', $SId)
                                              ->where('mfn_loan.productIdFk', $CId)
                                              ->count();
            // dd($InstallmentPayer);
            $InstallmentDue[$SId][$CId] = DB::table('mfn_loan')
                                              ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                                              ->where('mfn_loan_schedule.scheduleDate', $ReportDate)
                                              // ->where('mfn_loan_schedule.loanIdFk', $idPicks)
                                              ->where('mfn_loan_schedule.isCompleted', '<', 1)
                                              ->where('mfn_loan.productIdFk', $CId)
                                              ->where('mfn_loan.branchIdFk', $BranchId)
                                              ->where('mfn_loan.samityIdFk', $SId)
                                              ->count();
            // }
            $SavingsDeposite[$SId][$CId] = DB::table('mfn_savings_deposit')
                                              // ->select('mfn_savings_deposit.amount')
                                              // ->selectRaw('SUM(mfn_savings_deposit.amount) as Total')
                                              ->join('mfn_savings_account', 'mfn_savings_deposit.accountIdFk', '=', 'mfn_savings_account.id')
                                              ->where('mfn_savings_deposit.depositDate', $ReportDate)
                                              ->where('mfn_savings_deposit.primaryProductIdFk', $CId)
                                              ->where('mfn_savings_deposit.samityIdFk', '=', $SId)
                                              ->where('mfn_savings_deposit.productIdFk', '=', 1)
                                              ->sum('mfn_savings_deposit.amount');
            $SavingsDeposite1[$SId][$CId] = DB::table('mfn_savings_deposit')
                                              // ->select(DB::raw('SUM(mfn_savings_deposit.amount) as Total'))
                                              // ->selectRaw('SUM(mfn_savings_deposit.amount) as Total')
                                              ->join('mfn_savings_account', 'mfn_savings_deposit.accountIdFk', '=', 'mfn_savings_account.id')
                                              ->where('mfn_savings_deposit.depositDate', $ReportDate)
                                              ->where('mfn_savings_deposit.primaryProductIdFk', $CId)
                                              ->where('mfn_savings_deposit.samityIdFk', '=', $SId)
                                              ->where('mfn_savings_deposit.productIdFk', '=', 2)
                                              ->sum('mfn_savings_deposit.amount');
            $SavingsDeposite2[$SId][$CId] = DB::table('mfn_savings_deposit')
                                              // ->select(DB::raw('SUM(mfn_savings_deposit.amount) as Total'))
                                              // ->selectRaw('SUM(mfn_savings_deposit.amount) as Total')
                                              ->join('mfn_savings_account', 'mfn_savings_deposit.accountIdFk', '=', 'mfn_savings_account.id')
                                              ->where('mfn_savings_deposit.depositDate', $ReportDate)
                                              ->where('mfn_savings_deposit.primaryProductIdFk', $CId)
                                              ->where('mfn_savings_deposit.samityIdFk', '=', $SId)
                                              ->where('mfn_savings_deposit.productIdFk', '=', 3)
                                              ->sum('mfn_savings_deposit.amount');
            $SavingsDeposite3[$SId][$CId] = DB::table('mfn_savings_deposit')
                                              // ->select(DB::raw('SUM(mfn_savings_deposit.amount) as Total'))
                                              // ->selectRaw('SUM(mfn_savings_deposit.amount) as Total')
                                              ->join('mfn_savings_account', 'mfn_savings_deposit.accountIdFk', '=', 'mfn_savings_account.id')
                                              ->where('mfn_savings_deposit.depositDate', $ReportDate)
                                              ->where('mfn_savings_deposit.primaryProductIdFk', $CId)
                                              ->where('mfn_savings_deposit.samityIdFk', '=', $SId)
                                              ->where('mfn_savings_deposit.productIdFk', '=', 4)
                                              ->sum('mfn_savings_deposit.amount');
            $SavingsWithdraw[$SId][$CId] = DB::table('mfn_savings_withdraw')
                                              // ->select('mfn_savings_deposit.amount')
                                              // ->selectRaw('SUM(mfn_savings_deposit.amount) as Total')
                                              ->join('mfn_savings_account', 'mfn_savings_withdraw.accountIdFk', '=', 'mfn_savings_account.id')
                                              ->where('mfn_savings_withdraw.withdrawDate', $ReportDate)
                                              ->where('mfn_savings_withdraw.primaryProductIdFk', $CId)
                                              ->where('mfn_savings_withdraw.samityIdFk', '=', $SId)
                                              ->where('mfn_savings_withdraw.productIdFk', '=', 1)
                                              ->sum('mfn_savings_withdraw.amount');
            $SavingsWithdraw1[$SId][$CId] = DB::table('mfn_savings_withdraw')
                                              // ->select(DB::raw('SUM(mfn_savings_deposit.amount) as Total'))
                                              // ->selectRaw('SUM(mfn_savings_deposit.amount) as Total')
                                              ->join('mfn_savings_account', 'mfn_savings_withdraw.accountIdFk', '=', 'mfn_savings_account.id')
                                              ->where('mfn_savings_withdraw.withdrawDate', $ReportDate)
                                              ->where('mfn_savings_withdraw.primaryProductIdFk', $CId)
                                              ->where('mfn_savings_withdraw.samityIdFk', '=', $SId)
                                              ->where('mfn_savings_withdraw.productIdFk', '=', 2)
                                              ->sum('mfn_savings_withdraw.amount');
            $SavingsWithdraw2[$SId][$CId] = DB::table('mfn_savings_withdraw')
                                              // ->select(DB::raw('SUM(mfn_savings_deposit.amount) as Total'))
                                              // ->selectRaw('SUM(mfn_savings_deposit.amount) as Total')
                                              ->join('mfn_savings_account', 'mfn_savings_withdraw.accountIdFk', '=', 'mfn_savings_account.id')
                                              ->where('mfn_savings_withdraw.withdrawDate', $ReportDate)
                                              ->where('mfn_savings_withdraw.primaryProductIdFk', $CId)
                                              ->where('mfn_savings_withdraw.samityIdFk', '=', $SId)
                                              ->where('mfn_savings_withdraw.productIdFk', '=', 3)
                                              ->sum('mfn_savings_withdraw.amount');
            $SavingsWithdraw3[$SId][$CId] = DB::table('mfn_savings_withdraw')
                                              // ->select(DB::raw('SUM(mfn_savings_deposit.amount) as Total'))
                                              // ->selectRaw('SUM(mfn_savings_deposit.amount) as Total')
                                              ->join('mfn_savings_account', 'mfn_savings_withdraw.accountIdFk', '=', 'mfn_savings_account.id')
                                              ->where('mfn_savings_withdraw.withdrawDate', $ReportDate)
                                              ->where('mfn_savings_withdraw.primaryProductIdFk', $CId)
                                              ->where('mfn_savings_withdraw.samityIdFk', '=', $SId)
                                              ->where('mfn_savings_withdraw.productIdFk', '=', 4)
                                              ->sum('mfn_savings_withdraw.amount');
            $LoanDisbursements[$SId][$CId] = DB::table('mfn_loan')
                                              ->where('primaryProductIdFk', $CId)
                                              ->where('samityIdFk', $SId)
                                              ->where('branchIdFk', $BranchId)
                                              ->where('disbursementDate', $ReportDate)
                                              ->count();
            // $LoanDisbursementsTotal = array_sum($LoanDisbursements);

            // $LoanDisbursementsTotal = 0;
            if (sizeof($LoanDisbursements) > 0) {
              foreach ($LoanDisbursements as $LoanDisbursemen) {
                $LoanDisbursementsTotal = $LoanDisbursementsTotal + array_sum($LoanDisbursemen);
              }
            }
            // $LoanDisbursementsTotal = array_sum($abc);
            $LoanDisbursementsAmount[$SId][$CId] = DB::table('mfn_loan')
                                              ->where('primaryProductIdFk', $CId)
                                              ->where('samityIdFk', $SId)
                                              ->where('branchIdFk', $BranchId)
                                              ->where('disbursementDate', $ReportDate)
                                              ->sum('loanAmount');
            $FullyPaymentLoan[$SId][$CId] = DB::table('mfn_loan')
                                              ->where('primaryProductIdFk', $CId)
                                              ->where('samityIdFk', $SId)
                                              ->where('branchIdFk', $BranchId)
                                              ->where('loanCompletedDate', $ReportDate)
                                              ->where('isLoanCompleted', '=', 1)
                                              ->count();
            $FullyPaymentLoanAmount[$SId][$CId] = DB::table('mfn_loan')
                                              ->where('primaryProductIdFk', $CId)
                                              ->where('samityIdFk', $SId)
                                              ->where('branchIdFk', $BranchId)
                                              ->where('loanCompletedDate', $ReportDate)
                                              ->where('isLoanCompleted', '=', 1)
                                              ->sum('loanAmount');
            $AdditionalFeeCollection[$SId][$CId] = DB::table('mfn_loan')
                                              ->where('primaryProductIdFk', $CId)
                                              ->where('samityIdFk', $SId)
                                              ->where('branchIdFk', $BranchId)
                                              ->where('disbursementDate', $ReportDate)
                                              ->sum('additionalFee');

            $LoanRecoverable[$SId][$CId] = DB::table('mfn_loan_schedule')
                                              // ->select('mfn_loan_schedule.loanIdFk','mfn_loan_schedule.installmentAmount','mfn_loan_schedule.scheduleDate')
                                              ->join('mfn_loan', 'mfn_loan_schedule.loanIdFk', '=', 'mfn_loan.id')
                                              ->where('mfn_loan.primaryProductIdFk', $CId)
                                              ->where('mfn_loan.samityIdFk', $SId)
                                              ->where('mfn_loan.branchIdFk', $BranchId)
                                              ->where('mfn_loan_schedule.scheduleDate', $ReportDate)
                                              // ->get();
                                              ->sum('mfn_loan_schedule.installmentAmount');
            $RegularLoan[$SId][$CId] = DB::table('mfn_loan_collection')
                                              ->where('primaryProductIdFk', $CId)
                                              ->where('samityIdFk', $SId)
                                              ->where('branchIdFk', $BranchId)
                                              ->where('collectionDate', $ReportDate)
                                              ->sum('amount');
            $RegularLoan1[$SId][$CId] = DB::table('mfn_loan_collection')
                                              ->select('amount', 'collectionDate', 'loanIdFk', 'principalAmount', 'interestAmount')
                                              ->where('primaryProductIdFk', $CId)
                                              ->where('samityIdFk', $SId)
                                              ->where('branchIdFk', $BranchId)
                                              ->where('collectionDate', $ReportDate)
                                              ->get();
            // $PreviousDatesLoanRecoverable[$SId][$CId] = DB::table('mfn_loan_schedule')
            //                                   // ->select('mfn_loan_schedule.loanIdFk','mfn_loan_schedule.installmentAmount')
            //                                   ->join('mfn_loan', 'mfn_loan_schedule.loanIdFk', '=', 'mfn_loan.id')
            //                                   ->where('mfn_loan.primaryProductIdFk', $CId)
            //                                   ->where('mfn_loan.samityIdFk', $SId)
            //                                   ->where('mfn_loan.branchIdFk', $BranchId)
            //                                   ->where('mfn_loan_schedule.scheduleDate', '<', $ReportDate)
            //                                   // ->get();
            //                                   ->sum('mfn_loan_schedule.installmentAmount');
            // $PreviousDatesCollection[$SId][$CId] = DB::table('mfn_loan_collection')
            //                                   ->where('primaryProductIdFk', $CId)
            //                                   ->where('samityIdFk', $SId)
            //                                   ->where('branchIdFk', $BranchId)
            //                                   ->where('collectionDate', '<', $ReportDate)
            //                                   ->sum('amount');
            $PreviousLoanCollectionId[$SId][$CId] = DB::table('mfn_loan_collection')
                                              ->select('amount', 'collectionDate', 'loanIdFk')
                                              ->where('primaryProductIdFk', $CId)
                                              ->where('samityIdFk', $SId)
                                              ->where('branchIdFk', $BranchId)
                                              ->where('collectionDate', '<', $ReportDate)
                                              ->get();
            $InstallmentAmountById[$SId][$CId] = DB::table('mfn_loan')
                                              ->select('installmentAmount', 'id')
                                              ->where('primaryProductIdFk', $CId)
                                              ->where('samityIdFk', $SId)
                                              ->where('branchIdFk', $BranchId)
                                              ->get();

            $loanRebate[$SId][$CId] = DB::table('mfn_loan_rebates')
                                              // ->select('amount')
                                              ->where('primaryProductIdFk', $CId)
                                              ->where('samityIdFk', $SId)
                                              ->where('branchIdFk', $BranchId)
                                              ->sum('amount');
            $InsurancePremium[$SId][$CId] = DB::table('mfn_loan')
                                              // ->select('insuranceAmount')
                                              ->where('primaryProductIdFk', $CId)
                                              ->where('samityIdFk', $SId)
                                              ->where('branchIdFk', $BranchId)
                                              ->where('disbursementDate', $ReportDate)
                                              ->sum('insuranceAmount');
            $WeeklyDate = DateTime::createFromFormat('Y-m-d', $ReportDate);
            $DayName = $WeeklyDate->format('D');

            if ($DayName == 'Sat') {
              $DN = 1;
            }
            elseif ($DayName == 'Sun') {
              $DN = 2;
            }
            elseif ($DayName == 'Mon') {
              $DN = 3;
            }
            elseif ($DayName == 'Tue') {
              $DN = 4;
            }
            elseif ($DayName == 'Wed') {
              $DN = 5;
            }
            elseif ($DayName == 'Thu') {
              $DN = 6;
            }

            $SavingsRecoverableValues[$SId][$CId] = DB::table('mfn_savings_deposit')
                                              // ->select('mfn_saving_product.weeklyDepositAmount')
                                              // ->join('mfn_saving_product', 'mfn_savings_account.savingsProductIdFk', '=', 'mfn_saving_product.id')
                                              ->join('mfn_saving_product', 'mfn_savings_deposit.productIdFk', '=', 'mfn_saving_product.id')
                                              ->join('mfn_samity', 'mfn_savings_deposit.samityIdFk', '=', 'mfn_samity.id')
                                              ->where('mfn_savings_deposit.primaryProductIdFk', $CId)
                                              ->where('mfn_savings_deposit.samityIdFk', $SId)
                                              ->where('mfn_savings_deposit.branchIdFk', $BranchId)
                                              ->where('mfn_samity.samityDayId', $DN)
                                              ->where('mfn_saving_product.savingCollectionFrequencyIdFk', 1)
                                              // ->get();
                                              ->sum('mfn_saving_product.weeklyDepositAmount');

            // $MonthlySavingsRecoverable[$SId][$CId] = DB::table('mfn_savings_deposit')


          }
        }
      }
    }
    // dd($MembersByProduct);
    // dd($SavingsRecoverableValues);
    // dd($MonthlyCollectionValues);
    // dd($MonthlyCollectionSplitValues);
    // dd($MonthlyCollectionComparedValues);

    return view('microfin.reports.mfnDailyCollectionReport.dailyCollectionComponentWiseReportTable', compact('BrDatas', 'Component', 'ReportDate', 'FieldOffcers', 'Samities', 'SamityComponents', 'ShowProduct', 'TotalMemberByProduct', 'TotalLoanByProduct', 'mfnSavingsProducts', 'TotalComponents', 'InstallmentPayer','InstallmentDue', 'SavingsDeposite', 'SavingsDeposite1', 'SavingsDeposite2', 'SavingsDeposite3', 'SavingsWithdraw', 'SavingsWithdraw1', 'SavingsWithdraw2', 'SavingsWithdraw3', 'LoanDisbursements', 'SavingsRecoverable', 'LoanDisbursementsAmount', 'FullyPaymentLoan', 'FullyPaymentLoanAmount', 'LoanDisbursementsTotal', 'AdditionalFeeCollection', 'LoanRecoverable', 'RegularLoan', 'PreviousDatesLoanRecoverable', 'PreviousDatesCollection', 'RegularLoan1', 'PreviousLoanCollectionId', 'InstallmentAmountById', 'loanRebate', 'InsurancePremium', 'SavingsRecoverableValues'));

  }

}
