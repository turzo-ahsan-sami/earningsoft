<?php

namespace App\Http\Controllers\microfin\reports\PassBookBalancingRegisterReportController;

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
class MemberWisePassBookBalancingRegisterReportController extends Controller
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

    return view('microfin.reports.passBookBalancingRegisterReport.MemberWisePassBookBalancingRegisterReportForm', compact('BranchDatas', 'ProdCats', 'UniqueLoanYear'));

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

  public function BranchQuery($RequestedBranch){
    $Branch = array();

    $Branch = DB::table('gnr_branch')
              ->where('id', $RequestedBranch)
              ->get();

    return $Branch;
  }

  public function AreaQuery($RequestedBranch){
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
        if ($AreaVal == $RequestedBranch) {
          $AreaName = $key1;
        }
      }
    }

    // dd($AreaName);

    return $AreaName;
  }

  public function CreditOfficerQuery($branch, $Samity){
    $CreditOfficer = array();
    $Officer = array();
    $FieldOfficer = array();

    $CreditOfficer = DB::table('hr_emp_org_info')
              ->select('hr_emp_general_info.emp_name_english', 'hr_emp_general_info.emp_id', 'hr_emp_general_info.id as EmployeeID', 'gnr_branch.id', 'hr_settings_position.name as PositionName')
              ->join('gnr_branch', 'hr_emp_org_info.branch_id_fk', '=', 'gnr_branch.id')
              ->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', '=', 'hr_settings_position.id')
              ->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
              ->where([['gnr_branch.id', $branch], ['hr_settings_position.name', '=', 'Credit Officer']])
              ->get();

    if ($Samity != 'All') {
      $Officer = DB::table('mfn_samity')
                ->select('fieldOfficerId')
                ->where([['branchId', $branch], ['id', $Samity]])
                ->get();

      foreach ($CreditOfficer as $key => $CreditOfficers) {
        foreach ($Officer as $key => $Officers) {
          if ($CreditOfficers->EmployeeID == $Officers->fieldOfficerId) {
            $FieldOfficer = $CreditOfficers;
          }
        }
      }
    }
    else {
      $FieldOfficer = $CreditOfficer;
    }

    // dd($FieldOfficer);

    return $FieldOfficer;
  }

  public function SamityQuery($branch, $Samity){
    $SamityInfo = array();

    if ($Samity != 'All') {
      $SamityInfo = DB::table('mfn_samity')
                ->where('id', $Samity)
                ->get();
    }
    else {
      $SamityInfo['name'] = 'All';
      $SamityInfo['code'] = '';
    }

    // dd($SamityInfo);

    return $SamityInfo;
  }

  public function MemberQuery($branch, $Samity){
    $Member = array();

    $Member = DB::table('mfn_member_information')
              ->select('id', 'code', 'name', 'spouseFatherSonName', 'primaryProductId')
              ->where([['branchId', $branch], ['samityId', $Samity]])
              ->get();

              // dd($Member);

    return $Member;
  }

  public function LoanQuery($branch, $Samity, $Member, $RequestedCategory){
    $Loan = array();
    $ProductInfo = array();

    // dd($Member);

    if ($RequestedCategory == 'All') {
      $ProductInfo = DB::table('mfn_loans_product')
                  ->get();
      foreach ($Member as $key => $Member1) {
        foreach ($ProductInfo as $key => $ProductInfo1) {
          $Loan[$Member1->id.'/'.$ProductInfo1->id] = DB::table('mfn_loan')
                  ->where([['memberIdFk', $Member1->id], ['productIdFk', $ProductInfo1->id]])
                  ->get();
        }
      }
    }
    else {
      $ProductInfo = DB::table('mfn_loans_product')
                  ->where('productCategoryId', $RequestedCategory)
                  ->get();
      foreach ($Member as $key => $Member1) {
        foreach ($ProductInfo as $key => $ProductInfo1) {
          $Loan[$Member1->id.'/'.$ProductInfo1->id] = DB::table('mfn_loan')
                  ->where([['memberIdFk', $Member1->id], ['productIdFk', $ProductInfo1->id]])
                  ->get();
        }
      }
    }
    // dd($Loan);

    return $Loan;
  }

  public function getOutstanding($branch, $Samity, $Member, $RequestedCategory, $startDate){
    $Loan = array();
    $Collection = array();
    $ProductInfo = array();
    $Outstanding = array();

    // dd($startDate);

    if ($RequestedCategory == 'All') {
      $ProductInfo = DB::table('mfn_loans_product')
                  ->get();
      foreach ($Member as $key => $Member1) {
        foreach ($ProductInfo as $key => $ProductInfo1) {
          $Loan[$Member1->id.'/'.$ProductInfo1->id] = DB::table('mfn_loan')
                  ->where([['memberIdFk', $Member1->id], ['productIdFk', $ProductInfo1->id], ['disbursementDate', '<=', $startDate]])
                  ->sum('totalRepayAmount');

          $Collection[$Member1->id.'/'.$ProductInfo1->id] = DB::table('mfn_loan_collection')
                  ->where([['memberIdFk', $Member1->id], ['primaryProductIdFk', $ProductInfo1->id], ['collectionDate', '<=', $startDate]])
                  ->sum('amount');
        }
      }
    }
    else {
      $ProductInfo = DB::table('mfn_loans_product')
                  ->where('productCategoryId', $RequestedCategory)
                  ->get();
      foreach ($Member as $key => $Member1) {
        foreach ($ProductInfo as $key => $ProductInfo1) {
          $Loan[$Member1->id.'/'.$ProductInfo1->id] = DB::table('mfn_loan')
                  ->where([['memberIdFk', $Member1->id], ['productIdFk', $ProductInfo1->id], ['disbursementDate', '<=', $startDate]])
                  ->sum('totalRepayAmount');

          $Collection[$Member1->id.'/'.$ProductInfo1->id] = DB::table('mfn_loan_collection')
                  ->where([['memberIdFk', $Member1->id], ['primaryProductIdFk', $ProductInfo1->id], ['collectionDate', '<=', $startDate]])
                  ->sum('amount');
        }
      }
    }

    // dd($Collection);

    foreach ($Loan as $key1 => $Loan1) {
      list($mId1, $pId1) = explode('/', $key1);
      foreach ($Collection as $key2 => $Collection1) {
        list($mId2, $pId2) = explode('/', $key2);
        if ($mId1 == $mId2 and $pId1 == $pId2) {
          $Outstanding[$key2] = $Loan1 - $Collection1;
        }
      }
    }

    // dd($Outstanding);

    return $Outstanding;
  }

  public function getDue($branch, $Samity, $Member, $RequestedCategory, $startDate){
    $Loan = array();
    $Collection = array();
    $ProductInfo = array();
    $Due = array();

    // dd($Member);

    if ($RequestedCategory == 'All') {
      $ProductInfo = DB::table('mfn_loans_product')
                  ->get();
      foreach ($Member as $key => $Member1) {
        foreach ($ProductInfo as $key => $ProductInfo1) {
          $Loan[$Member1->id.'/'.$ProductInfo1->id] = DB::table('mfn_loan')
                  ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                  ->where([['mfn_loan.memberIdFk', $Member1->id], ['mfn_loan.productIdFk', $ProductInfo1->id], ['mfn_loan_schedule.scheduleDate', '<=', $startDate]])
                  ->sum('mfn_loan_schedule.installmentAmount');

          $Collection[$Member1->id.'/'.$ProductInfo1->id] = DB::table('mfn_loan_collection')
                  ->where([['memberIdFk', $Member1->id], ['primaryProductIdFk', $ProductInfo1->id], ['collectionDate', '<=', $startDate]])
                  ->sum('amount');
        }
      }
    }
    else {
      $ProductInfo = DB::table('mfn_loans_product')
                  ->where('productCategoryId', $RequestedCategory)
                  ->get();
      foreach ($Member as $key => $Member1) {
        foreach ($ProductInfo as $key => $ProductInfo1) {
          $Loan[$Member1->id.'/'.$ProductInfo1->id] = DB::table('mfn_loan')
                  ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                  ->where([['mfn_loan.memberIdFk', $Member1->id], ['mfn_loan.productIdFk', $ProductInfo1->id], ['mfn_loan_schedule.scheduleDate', '<=', $startDate]])
                  ->sum('mfn_loan_schedule.installmentAmount');

          $Collection[$Member1->id.'/'.$ProductInfo1->id] = DB::table('mfn_loan_collection')
                  ->where([['memberIdFk', $Member1->id], ['primaryProductIdFk', $ProductInfo1->id], ['collectionDate', '<=', $startDate]])
                  ->sum('amount');
        }
      }
    }

    // dd($Collection);

    foreach ($Loan as $key1 => $Loan1) {
      list($mId1, $pId1) = explode('/', $key1);
      foreach ($Collection as $key2 => $Collection1) {
        list($mId2, $pId2) = explode('/', $key2);
        if ($mId1 == $mId2 and $pId1 == $pId2) {
          if ($Loan1 >= $Collection1) {
            $Due[$key2] = $Loan1 - $Collection1;
          }
          else {
            $Due[$key2] = 0;
          }
        }
      }
    }

    // dd($Due);

    return $Due;
  }

  public function getSavings($branch, $Samity, $Member, $RequestedCategory, $startDate, $FromDate){
    $Loan = array();
    $Collection = array();
    $SavingsInfos = array();
    $Due = array();

    // dd($Member);
    $SavingsProduct = DB::table('mfn_saving_product')->get();

    if ($RequestedCategory == 'All') {
      $ProductInfo = DB::table('mfn_loans_product')
                  ->get();
      foreach ($Member as $key => $Member1) {
        foreach ($ProductInfo as $key => $ProductInfo1) {
          foreach ($SavingsProduct as $key => $SavingsProduct1) {
            $SavingsInfos[$Member1->id.'/'.$ProductInfo1->id][$SavingsProduct1->id] = DB::table('mfn_savings_deposit')
                    ->where([['memberIdFk', $Member1->id], ['primaryProductIdFk', $ProductInfo1->id], ['depositDate', '>=', $FromDate], ['depositDate', '<=', $startDate], ['productIdFk', $SavingsProduct1->id]])
                    ->sum('amount');
          }
        }
      }
    }
    else {
      $ProductInfo = DB::table('mfn_loans_product')
                  ->where('productCategoryId', $RequestedCategory)
                  ->get();
      foreach ($Member as $key => $Member1) {
        foreach ($ProductInfo as $key => $ProductInfo1) {
          foreach ($SavingsProduct as $key => $SavingsProduct1) {
            $SavingsInfos[$Member1->id.'/'.$ProductInfo1->id][$SavingsProduct1->id] = DB::table('mfn_savings_deposit')
                    ->where([['memberIdFk', $Member1->id], ['primaryProductIdFk', $ProductInfo1->id], ['depositDate', '>=', $FromDate], ['depositDate', '<=', $startDate], ['productIdFk', $SavingsProduct1->id]])
                    ->sum('amount');
          }
        }
      }
    }

    // dd($SavingsInfos);

    return $SavingsInfos;
  }

  public function getManualPassBook($branch, $Samity, $Member, $RequestedCategory, $startDate){
    $Loan = array();
    $Collection = array();
    $ManualInfos = array();
    $Due = array();

    // dd($Member);

    if ($RequestedCategory == 'All') {
      $ProductInfo = DB::table('mfn_loans_product')
                  ->get();
      foreach ($Member as $key => $Member1) {
        foreach ($ProductInfo as $key => $ProductInfo1) {
          $ManualInfos[$Member1->id.'/'.$ProductInfo1->id] = DB::table('manual_pass_book_balance')
                  ->select('outstanding', 'due', 'savingsBalance')
                  ->where([['memberIdFk', $Member1->id], ['productIdFk', $ProductInfo1->id], ['date', $startDate]])
                  ->get();
        }
      }
    }
    else {
      $ProductInfo = DB::table('mfn_loans_product')
                  ->where('productCategoryId', $RequestedCategory)
                  ->get();
      foreach ($Member as $key => $Member1) {
        foreach ($ProductInfo as $key => $ProductInfo1) {
          $ManualInfos[$Member1->id.'/'.$ProductInfo1->id] = DB::table('manual_pass_book_balance')
                  ->select('outstanding', 'due', 'savingsBalance')
                  ->where([['memberIdFk', $Member1->id], ['productIdFk', $ProductInfo1->id], ['date', $startDate]])
                  ->get();
        }
      }
    }

    // dd($ManualInfos);


    return $ManualInfos;
  }

  public function getCategoryname($branch, $Samity, $Member, $RequestedCategory, $startDate){
    $CategoryName = array();
    $MargedArray = array();
    $ProductInfo = array();

    $product = '';

    foreach ($Member as $key => $Member1) {
      $Loan[] = DB::table('mfn_loan')
              ->select('productIdFk')
              ->where('memberIdFk', $Member1->id)
              ->groupBy('productIdFk')
              ->get();
    }

    foreach ($Loan as $key => $Loan1) {
      foreach ($Loan1 as $key => $Loan2) {
        if ($product != $Loan2->productIdFk) {
          $ProductInfo[] = [
            'productIdFk' =>  $Loan2->productIdFk
          ];

          $product = $Loan2->productIdFk;
        }
      }
    }

    if ($RequestedCategory == 'All') {
      foreach ($ProductInfo as $key => $ProductInfo1) {
        $CategoryName[] = DB::table('mfn_loans_product_category')
              ->select('mfn_loans_product_category.name', 'mfn_loans_product_category.id')
              ->join('mfn_loans_product', 'mfn_loans_product_category.id', '=', 'mfn_loans_product.productCategoryId')
              ->where('mfn_loans_product.id', $ProductInfo1['productIdFk'])
              ->get();
      }
      // $CategoryName = DB::table('mfn_loans_product_category')->get();
    }
    else {
      $CategoryName[] = DB::table('mfn_loans_product_category')->where('id', $RequestedCategory)->get();
    }

    // dd($CategoryName);

    return $CategoryName;
  }

  public function categoryWiseDisbursementAmount($branch, $Samity, $Member, $RequestedCategory, $startDate, $CategoryName){
    $DisbursementAmount = array();

    foreach ($CategoryName as $key => $CategoryName1) {
      foreach ($CategoryName1 as $key => $CategoryName2) {
        $DisbursementAmount[$CategoryName2->id] = DB::table('mfn_loan')
                ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                ->where([['mfn_loans_product.productCategoryId', $CategoryName2->id], ['mfn_loan.samityIdFk', $Samity]])
                ->sum('mfn_loan.loanAmount');
      }
    }

    // dd($DisbursementAmount);

    return $DisbursementAmount;
  }

  public function categoryWiseOutstanding($branch, $Samity, $Member, $RequestedCategory, $startDate, $CategoryName){
    $OutstandingAmount = array();

    foreach ($CategoryName as $key => $CategoryName1) {
      foreach ($CategoryName1 as $key => $CategoryName2) {
        $TotalRepayAmount[$CategoryName2->id] = DB::table('mfn_loan')
                ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                ->where([['mfn_loans_product.productCategoryId', $CategoryName2->id], ['mfn_loan.samityIdFk', $Samity], ['mfn_loan.disbursementDate', '<=', $startDate]])
                ->sum('mfn_loan.totalRepayAmount');

        $TotalCollectionAmont[$CategoryName2->id] = DB::table('mfn_loan')
                ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                ->where([['mfn_loans_product.productCategoryId', $CategoryName2->id], ['mfn_loan.samityIdFk', $Samity], ['mfn_loan_collection.collectionDate', '<=', $startDate]])
                ->sum('mfn_loan_collection.amount');
      }
    }

    foreach ($TotalRepayAmount as $key1 => $TotalRepayAmount1) {
      foreach ($TotalCollectionAmont as $key2 => $TotalCollectionAmont1) {
        if ($key1 == $key2) {
          $OutstandingAmount[$key2] = $TotalRepayAmount1 - $TotalCollectionAmont1;
        }
      }
    }

    // dd($TotalRepayAmount);

    return $OutstandingAmount;
  }

  public function categoryWiseDue($branch, $Samity, $Member, $RequestedCategory, $startDate, $CategoryName){
    $DueAmount = array();

    // dd($CategoryName);

    foreach ($CategoryName as $key => $CategoryName1) {
      foreach ($CategoryName1 as $key => $CategoryName2) {
        $TotalScheduleAmount[$CategoryName2->id] = DB::table('mfn_loan')
                ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                ->where([['mfn_loans_product.productCategoryId', $CategoryName2->id], ['mfn_loan.samityIdFk', $Samity], ['mfn_loan_schedule.scheduleDate', '<=', $startDate]])
                ->sum('mfn_loan_schedule.installmentAmount');

        $TotalCollectionAmont[$CategoryName2->id] = DB::table('mfn_loan')
                ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                ->where([['mfn_loans_product.productCategoryId', $CategoryName2->id], ['mfn_loan.samityIdFk', $Samity], ['mfn_loan_collection.collectionDate', '<=', $startDate]])
                ->sum('mfn_loan_collection.amount');
      }
    }

    // dd($TotalCollectionAmont);

    foreach ($TotalScheduleAmount as $key1 => $TotalScheduleAmount1) {
      foreach ($TotalCollectionAmont as $key2 => $TotalCollectionAmont1) {
        if ($key1 == $key2 and $TotalScheduleAmount1 >= $TotalCollectionAmont1) {
          $DueAmount[$key2] = $TotalScheduleAmount1 - $TotalCollectionAmont1;
          // dd($DueAmount);
        }
        elseif ($key1 == $key2 and $TotalScheduleAmount1 < $TotalCollectionAmont1) {
          $DueAmount[$key2] = 0;
          // dd($DueAmount);
        }
      }
    }

    // dd($DueAmount);

    return $DueAmount;
  }
// After Eid I will start from here.............................
  public function categoryWiseSavings($branch, $Samity, $Member, $RequestedCategory, $startDate, $CategoryName, $FromDate){
    $Savings = array();
    $SavingsProduct = array();

    // dd($CategoryName);

    $SavingsProduct = DB::table('mfn_saving_product')
                   ->get();

   // dd($SavingsProduct);

    foreach ($CategoryName as $key => $CategoryName1) {
      foreach ($CategoryName1 as $key => $CategoryName2) {
        foreach ($SavingsProduct as $key => $SavingsProduct1) {
          $Savings[$CategoryName2->id][$SavingsProduct1->id] = DB::table('mfn_savings_deposit')
                  ->join('mfn_loans_product', 'mfn_savings_deposit.primaryProductIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loans_product.productCategoryId', $CategoryName2->id], ['mfn_savings_deposit.samityIdFk', $Samity], ['mfn_savings_deposit.productIdFk', $SavingsProduct1->id], ['mfn_savings_deposit.depositDate', '>=', $FromDate], ['mfn_savings_deposit.depositDate', '<=', $startDate]])
                  ->sum('mfn_savings_deposit.amount');
        }
      }
    }

    // dd($Savings);

    return $Savings;
  }

  public function categoryWiseManualOutstandings($branch, $Samity, $Member, $RequestedCategory, $startDate, $CategoryName){
    $Outstanding = array();
    $SavingsProduct = array();

    // dd($CategoryName);

    $SavingsProduct = DB::table('mfn_saving_product')
                   ->get();

   // dd($SavingsProduct);

    foreach ($CategoryName as $key => $CategoryName1) {
      foreach ($CategoryName1 as $key => $CategoryName2) {
        // foreach ($SavingsProduct as $key => $SavingsProduct1) {
          $Outstanding[$CategoryName2->id] = DB::table('manual_pass_book_balance')
                  ->join('mfn_loans_product', 'manual_pass_book_balance.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loans_product.productCategoryId', $CategoryName2->id], ['manual_pass_book_balance.samityIdFk', $Samity], ['manual_pass_book_balance.date', $startDate]])
                  ->sum('manual_pass_book_balance.outstanding');
        // }
      }
    }

    // dd($Outstanding);

    return $Outstanding;
  }

  public function categoryWiseManualDue($branch, $Samity, $Member, $RequestedCategory, $startDate, $CategoryName){
    $Due = array();
    $SavingsProduct = array();

    // dd($CategoryName);

    $SavingsProduct = DB::table('mfn_saving_product')
                   ->get();

   // dd($SavingsProduct);

    foreach ($CategoryName as $key => $CategoryName1) {
      foreach ($CategoryName1 as $key => $CategoryName2) {
        // foreach ($SavingsProduct as $key => $SavingsProduct1) {
          $Due[$CategoryName2->id] = DB::table('manual_pass_book_balance')
                  ->join('mfn_loans_product', 'manual_pass_book_balance.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loans_product.productCategoryId', $CategoryName2->id], ['manual_pass_book_balance.samityIdFk', $Samity], ['manual_pass_book_balance.date', $startDate]])
                  ->sum('manual_pass_book_balance.due');
        // }
      }
    }

    // dd($Outstanding);

    return $Due;
  }

  public function categoryWiseManualSavings($branch, $Samity, $Member, $RequestedCategory, $startDate, $CategoryName){
    $Savings = array();
    $SavingsProduct = array();

    // dd($CategoryName);

    $SavingsProduct = DB::table('mfn_saving_product')
                   ->get();

   // dd($SavingsProduct);

    foreach ($CategoryName as $key => $CategoryName1) {
      foreach ($CategoryName1 as $key => $CategoryName2) {
        foreach ($SavingsProduct as $key => $SavingsProduct1) {
          $Savings[$CategoryName2->id][$SavingsProduct1->id] = DB::table('manual_pass_book_balance')
                  ->join('mfn_loans_product', 'manual_pass_book_balance.productIdFk', '=', 'mfn_loans_product.id')
                  ->join('mfn_savings_account', 'manual_pass_book_balance.savingsCode', '=', 'mfn_savings_account.savingsCode')
                  // ->join('mfn_savings_account', 'mfn_saving_product.id', '=', 'mfn_savings_account.savingsProductIdFk')
                  ->where([['mfn_loans_product.productCategoryId', $CategoryName2->id], ['manual_pass_book_balance.samityIdFk', $Samity], ['manual_pass_book_balance.date', $startDate], ['mfn_savings_account.savingsProductIdFk', $SavingsProduct1->id]])
                  ->sum('manual_pass_book_balance.savingsBalance');
        }
      }
    }

    // dd($Savings);

    return $Savings;
  }

  public function memberManualPassBookSavingsTotal($branch, $Samity, $Member, $RequestedCategory, $startDate, $CategoryName){
    $MemberManualPassBookSavingsTotal = array();

    $SavingsProduct = DB::table('mfn_saving_product')
                   ->get();

    if ($RequestedCategory == 'All') {
      foreach ($SavingsProduct as $key => $SavingsProduct1) {
        $MemberManualPassBookSavingsTotal[$SavingsProduct1->id] = DB::table('manual_pass_book_balance')
                    ->join('mfn_savings_account', 'manual_pass_book_balance.savingsCode', '=', 'mfn_savings_account.savingsCode')
                    ->where([['mfn_savings_account.savingsProductIdFk', $SavingsProduct1->id], ['manual_pass_book_balance.samityIdFk', $Samity], ['manual_pass_book_balance.date', $startDate]])
                    ->sum('manual_pass_book_balance.savingsBalance');
      }
    }
    else {
      foreach ($SavingsProduct as $key => $SavingsProduct1) {
        $MemberManualPassBookSavingsTotal[$SavingsProduct1->id] = DB::table('manual_pass_book_balance')
                    ->join('mfn_savings_account', 'manual_pass_book_balance.savingsCode', '=', 'mfn_savings_account.savingsCode')
                    ->join('mfn_loans_product', 'manual_pass_book_balance.productIdFk', '=', 'mfn_loans_product.id')
                    ->where([['mfn_savings_account.savingsProductIdFk', $SavingsProduct1->id], ['mfn_loans_product.productCategoryId', $RequestedCategory], ['manual_pass_book_balance.samityIdFk', $Samity], ['manual_pass_book_balance.date', $startDate]])
                    ->sum('manual_pass_book_balance.savingsBalance');
      }
    }

    // dd($MemberManualPassBookSavingsTotal);

    return $MemberManualPassBookSavingsTotal;
  }

  public function getReport(Request $request){
    // Requested data variables.....
    $RequestedBranch = $request->searchBranch;
    $RequestedSamity = $request->searchSamity;
    $RequestedCategory = $request->searchProCat;
    $RequestedYear = $request->Year;
    $RequestedQuerter = $request->DateRange;

    // Declared data variables.....
    $FromDate = '';
    $ToDate = '';
    $AreaInfos = '';
    $startDate = '';
    $Querter = '';

    // Declared data array......
    $BranchInfos = array();
    $CreditOfficerInfos = array();
    $SamityInfos = array();
    $MemberInfos = array();
    $LoanInfos = array();
    $OutstandingsInfos = array();
    $DueInfos = array();
    $SavingsInfos = array();
    $ManualInfos = array();
    $SavingsProduct = array();
    $CategoryName = array();
    $CategoryWiseDisbursementAmount = array();
    $CategoryWiseOutstanding = array();
    $CategoryWiseDue = array();
    $CategoryWiseSavings = array();
    $CategoryWiseManualOutstandings = array();
    $CategoryWiseManualDue = array();
    $CategoryWiseManualSavings = array();
    $MemberManualPassBookSavingsTotal = array();


    // dd($RequestedQuerter);

    switch ($RequestedQuerter) {
       case '1-3':
           $FromDate = $RequestedYear.'-01-01';
           $startDate = $RequestedYear.'-03-31';
           $Querter = '1st';
           break;
       case '4-6':
           $FromDate = $RequestedYear.'-04-01';
           $startDate = $RequestedYear.'-06-30';
           $Querter = '2nd';
           break;
       case '7-9':
           $FromDate = $RequestedYear.'-07-01';
           $startDate = $RequestedYear.'-09-30';
           $Querter = '3rd';
           break;
       case '10-12':
           $FromDate = $RequestedYear.'-10-01';
           $startDate = $RequestedYear.'-12-31';
           $Querter = '4th';
           break;
   }

   // dd($startDate);

   $SavingsProduct = DB::table('mfn_saving_product')
                  ->get();

    // Methods for Query starts.....
    $BranchInfos = $this->BranchQuery($RequestedBranch);

    $AreaInfos = $this->AreaQuery($RequestedBranch);

    $CreditOfficerInfos = $this->CreditOfficerQuery($RequestedBranch, $RequestedSamity);

    $SamityInfos = $this->SamityQuery($RequestedBranch, $RequestedSamity);

    $MemberInfos = $this->MemberQuery($RequestedBranch, $RequestedSamity);

    $LoanInfos = $this->LoanQuery($RequestedBranch, $RequestedSamity, $MemberInfos, $RequestedCategory);

    $OutstandingsInfos = $this->getOutstanding($RequestedBranch, $RequestedSamity, $MemberInfos, $RequestedCategory, $startDate);

    $DueInfos = $this->getDue($RequestedBranch, $RequestedSamity, $MemberInfos, $RequestedCategory, $startDate);

    $SavingsInfos = $this->getSavings($RequestedBranch, $RequestedSamity, $MemberInfos, $RequestedCategory, $startDate, $FromDate);

    $ManualInfos = $this->getManualPassBook($RequestedBranch, $RequestedSamity, $MemberInfos, $RequestedCategory, $startDate); //Not using

    $CategoryName = $this->getCategoryname($RequestedBranch, $RequestedSamity, $MemberInfos, $RequestedCategory, $startDate);

    $CategoryWiseDisbursementAmount = $this->categoryWiseDisbursementAmount($RequestedBranch, $RequestedSamity, $MemberInfos, $RequestedCategory, $startDate, $CategoryName);

    $CategoryWiseOutstanding = $this->categoryWiseOutstanding($RequestedBranch, $RequestedSamity, $MemberInfos, $RequestedCategory, $startDate, $CategoryName);

    $CategoryWiseDue = $this->categoryWiseDue($RequestedBranch, $RequestedSamity, $MemberInfos, $RequestedCategory, $startDate, $CategoryName);

    $CategoryWiseSavings = $this->categoryWiseSavings($RequestedBranch, $RequestedSamity, $MemberInfos, $RequestedCategory, $startDate, $CategoryName, $FromDate);

    $CategoryWiseManualOutstandings = $this->categoryWiseManualOutstandings($RequestedBranch, $RequestedSamity, $MemberInfos, $RequestedCategory, $startDate, $CategoryName);

    $CategoryWiseManualDue = $this->categoryWiseManualDue($RequestedBranch, $RequestedSamity, $MemberInfos, $RequestedCategory, $startDate, $CategoryName);

    $CategoryWiseManualSavings = $this->categoryWiseManualSavings($RequestedBranch, $RequestedSamity, $MemberInfos, $RequestedCategory, $startDate, $CategoryName);

    $MemberManualPassBookSavingsTotal = $this->memberManualPassBookSavingsTotal($RequestedBranch, $RequestedSamity, $MemberInfos, $RequestedCategory, $startDate, $CategoryName);

    // dd($ManualInfos);

    return view('microfin.reports.passBookBalancingRegisterReport.MemberWisePassBookBalancingRegisterReportTable', compact('BranchInfos', 'AreaInfos', 'CreditOfficerInfos',
    'SamityInfos', 'RequestedSamity', 'MemberInfos', 'LoanInfos', 'OutstandingsInfos', 'DueInfos', 'SavingsInfos', 'ManualInfos', 'startDate', 'SavingsProduct', 'CategoryName',
    'CategoryWiseDisbursementAmount', 'CategoryWiseOutstanding', 'CategoryWiseDue', 'CategoryWiseSavings', 'CategoryWiseManualOutstandings', 'CategoryWiseManualDue', 'CategoryWiseManualSavings',
    'MemberManualPassBookSavingsTotal', 'RequestedQuerter', 'Querter'));

  }

}
