<?php

namespace App\Http\Controllers\microfin\reports\dailyRecoverableAndCollectionRegisterController;

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
 *  Controller for Daily Recoverable and Collection Register
 *  -Created By Atiqul Haque
 *  -Date:19/04/18
 **/
class DailyRecoverableAndCollectionRegisterController extends Controller
{
  public function getBranchName(){
    $LoanYear = array();
    $LoanYearArray = array();
    $UniqueLoanYear = array();

    $BranchDatas = DB::table('gnr_branch')->get();

    $ProductCategory = DB::table('mfn_loans_product_category')->get();

    return view('microfin.reports.dailyRecoverableAndCollectionRegisterViews.DailyRecoverableAndCollectionRegisterForm', compact('BranchDatas', 'ProductCategory'));
  }

  public function getProduct(Request $request){
    $ProCatId = $request->id;

    $ProductName = array();

    if ($ProCatId != 'all') {
      $ProductName = DB::table('mfn_loans_product')
                      ->where('productCategoryId', $ProCatId)
                      ->get();
    }
    elseif ($ProCatId == 'all') {
      $ProductName = DB::table('mfn_loans_product')->get();
    }

    return response()->json($ProductName);
  }

  public function getReportTableData(Request $request){
    $RequestedBranchInfos = $request->searchBranch;
    $RequestedDate = $request->txtDate1;
    $RequestedLoanOptions = $request->LoanOptions;
    $RequestedProduct = $request->Product;

    $SavingsProduct = array();
    $BranchInfo = array();
    $ProdORproCate = array();
    $SamityInfos = array();
    $FieldWorkerInfos = array();
    $FieldWorkerList = array();
    $RequiredTableInfos = array();
    $InfoList = array();
    $RegularLoanAmount = array();
    $RegularLoan = array();

    $BranchInfo = DB::table('gnr_branch')->where('id', $RequestedBranchInfos)->get();
    $SavingsProduct = DB::table('mfn_saving_product')->get();

    $SamityInfos = DB::table('mfn_samity')
                  ->where('branchId', $RequestedBranchInfos)
                  ->get();
                  // dd($SamityInfos);
    foreach ($SamityInfos as $key => $SamityInfo) {
      $SID = $SamityInfo->id;
      $FID = $SamityInfo->fieldOfficerId;
      $FieldWorkerInfos[$SID] = DB::table('hr_emp_general_info')
                  ->where('id', $FID)
                  ->get();
    }
    // dd($FieldWorkerInfos);
    foreach ($FieldWorkerInfos as $key => $FieldWorkerInfoss) {
      foreach ($FieldWorkerInfoss as $key => $FieldWorkerInfo) {
        $FieldWorkerList[$FieldWorkerInfo->id.'/'.$FieldWorkerInfo->emp_name_english.'/'.$FieldWorkerInfo->emp_id] = array();
      }
    }

    // dd($FieldWorkerList);

    foreach ($SamityInfos as $key1 => $SamityInfo) {
      foreach ($FieldWorkerList as $key2 => $FieldWorkerLists) {
        $Comp = explode('/', $key2);
        list($first, $second, $third) = $Comp;
        if ($SamityInfo->fieldOfficerId == $first) {
          $InfoList[$key2][$SamityInfo->id.'/'.$SamityInfo->name.'/'.$SamityInfo->code] = array();
        }
      }
    }

    // dd($InfoList);

    if ($RequestedLoanOptions == 1) {
      if ($RequestedProduct == 'all') {
        foreach ($SamityInfos as $key => $SamityInfo) {
          $SID = $SamityInfo->id;
          $ProdORproCate[$SID] = DB::table('mfn_loan')
                    ->select('mfn_loans_product.name', 'mfn_loans_product.id')
                    ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                    ->where('mfn_loan.samityIdFk', $SID)
                    ->where('mfn_loan.branchIdFk', $RequestedBranchInfos)
                    ->where('mfn_loan.disbursementDate', '<=', $RequestedDate)
                    ->distinct()
                    ->get();

          $RegularLoan[$SID] = DB::table('mfn_loan')
                    ->select('mfn_loans_product.shortname', 'mfn_loans_product.id', 'mfn_loan.loanAmount')
                    ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                    ->where('mfn_loan.samityIdFk', $SID)
                    ->where('mfn_loan.branchIdFk', $RequestedBranchInfos)
                    // ->where('mfn_loans_product.id', $RequestedProduct)
                    ->where('mfn_loan.disbursementDate', '<=', $RequestedDate)
                    ->where('mfn_loan.isloanCompleted', '=', 0)
                    // ->sum('mfn_loan.loanAmount');
                    ->get();

          $RegularLoanAmount[$SID] = DB::table('mfn_loan')
                    // ->select('mfn_loans_product.shortname', 'mfn_loans_product.id')
                    ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                    ->where('mfn_loan.samityIdFk', $SID)
                    ->where('mfn_loan.branchIdFk', $RequestedBranchInfos)
                    // ->where('mfn_loans_product.id', $RequestedProduct)
                    ->where('mfn_loan.disbursementDate', '<=', $RequestedDate)
                    ->where('mfn_loan.isloanCompleted', '=', 0)
                    ->sum('mfn_loan.loanAmount');
                    // ->get();
        }
        // dd($ProdORproCate);
      }
      elseif ($RequestedProduct != 'all') {
        foreach ($SamityInfos as $key => $SamityInfo) {
          $SID = $SamityInfo->id;
          $ProdORproCate[$SID] = DB::table('mfn_loan')
                    ->select('mfn_loans_product.name', 'mfn_loans_product.id')
                    ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                    ->where('mfn_loan.samityIdFk', $SID)
                    ->where('mfn_loan.branchIdFk', $RequestedBranchInfos)
                    ->where('mfn_loans_product.id', $RequestedProduct)
                    ->distinct()
                    ->get();

          $RegularLoanAmount[$SID] = DB::table('mfn_loan')
                    // ->select('mfn_loans_product.shortname', 'mfn_loans_product.id')
                    ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                    ->where('mfn_loan.samityIdFk', $SID)
                    ->where('mfn_loan.branchIdFk', $RequestedBranchInfos)
                    ->where('mfn_loans_product.id', $RequestedProduct)
                    ->where('mfn_loan.disbursementDate', '<=', $RequestedDate)
                    ->where('mfn_loan.isloanCompleted', '=', 0)
                    ->sum('mfn_loan.loanAmount');
                    // ->get();
        }
      }
      // dd($ProdORproCate);
      // dd($RegularLoanAmount);
      /*
      if ($RegularLoanss->shortname == $ProdORproCatess->shortname) {
        $RequiredTableInfos[$keyM][$keyN][] = array(
          'PName' => $ProdORproCatess->shortname,
          'PId' => $ProdORproCatess->id,
          'RegularLoanAmount' => $RegularLoanss->loanAmount
        );
      */
      foreach ($InfoList as $keyM => $InfoLists) {
        $Comp = explode('/', $keyM);
        list($first, $second, $third) = $Comp;

        foreach ($InfoLists as $keyN => $InfoListsss) {
          $Comp1 = explode('/', $keyN);
          list($fourth, $fifth, $sixth) = $Comp1;

          foreach ($ProdORproCate as $key1 => $ProdORproCates) {
            foreach ($ProdORproCates as $key2 => $ProdORproCatess) {
              //check condition
              if ($RequestedProduct == 'all') {
                foreach ($RegularLoan as $key3 => $RegularLoans) {
                  if (sizeof($RegularLoans)>0) {
                    foreach ($RegularLoans as $key4 => $RegularLoanss) {
                      if ($key3 == $fourth and $key1 == $fourth and $ProdORproCatess->id == $RegularLoanss->id) {
                        $RequiredTableInfos[$keyM][$keyN][] = array(
                          'PName' => $ProdORproCatess->name,
                          'PId' => $ProdORproCatess->id,
                          'RegularLoanAmount' => $RegularLoanss->loanAmount
                        );
                      }
                    }
                  }
                  elseif (sizeof($RegularLoans)==0) {
                    if ($key3 == $fourth and $key1 == $fourth) {
                      $RequiredTableInfos[$keyM][$keyN][] = array(
                        'PName' => $ProdORproCatess->name,
                        'PId' => $ProdORproCatess->id,
                        'RegularLoanAmount' => 0.00
                      );
                    }
                  }
                }
              }
              elseif ($RequestedProduct != 'all') {
                foreach ($RegularLoanAmount as $key5 => $RegularLoanAmounts) {
                  if ($key1 == $fourth and $fourth == $key5) {
                    $RequiredTableInfos[$keyM][$keyN][] = array(
                      'PName' => $ProdORproCatess->name,
                      'PId' => $ProdORproCatess->id,
                      'RegularLoanAmount' => $RegularLoanAmounts
                    );
                  }
                }
              }
            }
          }
        }
      }
      // dd($RegularLoan);
      // dd($RequiredTableInfos);
    }
    elseif ($RequestedLoanOptions == 2) {
      if ($RequestedProduct == 'all') {
        foreach ($SamityInfos as $key => $SamityInfo) {
          $SID = $SamityInfo->id;
          $ProdORproCate[$SID] = DB::table('mfn_loan')
                    ->select('mfn_loans_product_category.name', 'mfn_loans_product_category.id')
                    ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                    ->join('mfn_loans_product_category', 'mfn_loans_product.productCategoryId', 'mfn_loans_product_category.id')
                    ->where('mfn_loan.samityIdFk', $SID)
                    ->where('mfn_loan.branchIdFk', $RequestedBranchInfos)
                    ->where('mfn_loan.disbursementDate', '<=', $RequestedDate)
                    ->distinct()
                    ->get();

          $RegularLoan[$SID] = DB::table('mfn_loan')
                    ->select('mfn_loans_product_category.name', 'mfn_loans_product_category.id', 'mfn_loan.loanAmount', 'mfn_loan.disbursementDate')
                    ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                    ->join('mfn_loans_product_category', 'mfn_loans_product.productCategoryId', 'mfn_loans_product_category.id')
                    ->where('mfn_loan.samityIdFk', $SID)
                    ->where('mfn_loan.branchIdFk', $RequestedBranchInfos)
                    ->where('mfn_loan.disbursementDate', '<=', $RequestedDate)
                    ->where('mfn_loan.isloanCompleted', '=', 0)
                    ->where('mfn_loan.softDel', '=', 0)
                    ->get();

          $RegularLoanAmount[$SID] = DB::table('mfn_loan')
                    // ->select('mfn_loans_product_category.name', 'mfn_loans_product_category.id')
                    ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                    ->join('mfn_loans_product_category', 'mfn_loans_product.productCategoryId', 'mfn_loans_product_category.id')
                    ->where('mfn_loan.samityIdFk', $SID)
                    ->where('mfn_loan.branchIdFk', $RequestedBranchInfos)
                    ->where('mfn_loan.disbursementDate', '<=', $RequestedDate)
                    ->where('mfn_loan.isloanCompleted', '=', 0)
                    ->sum('mfn_loan.loanAmount');
        }
        // dd($RegularLoanAmount);
        // dd($RegularLoan);
      }
      elseif ($RequestedProduct != 'all') {
        foreach ($SamityInfos as $key => $SamityInfo) {
          $SID = $SamityInfo->id;
          $ProdORproCate[$SID] = DB::table('mfn_loan')
                    ->select('mfn_loans_product_category.name', 'mfn_loans_product_category.id')
                    ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                    ->join('mfn_loans_product_category', 'mfn_loans_product.productCategoryId', 'mfn_loans_product_category.id')
                    ->where('mfn_loan.samityIdFk', $SID)
                    ->where('mfn_loan.branchIdFk', $RequestedBranchInfos)
                    ->where('mfn_loans_product.id', $RequestedProduct)
                    ->distinct()
                    ->get();

          $RegularLoanAmount[$SID] = DB::table('mfn_loan')
                    ->select('mfn_loans_product_category.name', 'mfn_loans_product_category.id')
                    ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                    ->join('mfn_loans_product_category', 'mfn_loans_product.productCategoryId', 'mfn_loans_product_category.id')
                    ->where('mfn_loan.samityIdFk', $SID)
                    ->where('mfn_loan.branchIdFk', $RequestedBranchInfos)
                    ->where('mfn_loans_product.id', $RequestedProduct)
                    ->where('mfn_loan.disbursementDate', '<=', $RequestedDate)
                    ->where('mfn_loan.isloanCompleted', '=', 0)
                    // ->distinct()
                    // ->get();
                    ->sum('mfn_loan.loanAmount');
        }
      }
      // dd($RegularLoanAmount);
      foreach ($InfoList as $keyM => $InfoLists) {
        $Comp = explode('/', $keyM);
        list($first, $second, $third) = $Comp;

        foreach ($InfoLists as $keyN => $InfoListsss) {
          $Comp1 = explode('/', $keyN);
          list($fourth, $fifth, $sixth) = $Comp1;

          foreach ($ProdORproCate as $key1 => $ProdORproCates) {
            foreach ($ProdORproCates as $key2 => $ProdORproCatess) {
              //check condition
              if ($RequestedProduct == 'all') {
                foreach ($RegularLoan as $key3 => $RegularLoans) {
                  if (sizeof($RegularLoans)>0) {
                    foreach ($RegularLoans as $key4 => $RegularLoanss) {
                      if ($key3 == $fourth and $key1 == $fourth and $ProdORproCatess->id == $RegularLoanss->id) {
                        $RequiredTableInfos[$keyM][$keyN][] = array(
                          'PName' => $ProdORproCatess->name,
                          'PId' => $ProdORproCatess->id,
                          'RegularLoanAmount' => $RegularLoanss->loanAmount
                        );
                      }
                    }
                  }
                  elseif (sizeof($RegularLoans)==0) {
                    if ($key3 == $fourth and $key1 == $fourth) {
                      $RequiredTableInfos[$keyM][$keyN][] = array(
                        'PName' => $ProdORproCatess->name,
                        'PId' => $ProdORproCatess->id,
                        'RegularLoanAmount' => 0.0
                      );
                    }
                  }
                }
              }
              elseif ($RequestedProduct != 'all') {
                foreach ($RegularLoanAmount as $key5 => $RegularLoanAmounts) {
                  if ($key1 == $fourth and $fourth == $key5) {
                    $RequiredTableInfos[$keyM][$keyN][] = array(
                      'PName' => $ProdORproCatess->name,
                      'PId' => $ProdORproCatess->id,
                      'RegularLoanAmount' => $RegularLoanAmounts
                    );
                  }
                }
              }
            }
          }
        }
      }
    }
    // dd($ProdORproCate);   //Problem
    // dd($RequiredTableInfos);   //Problem
    // dd($RegularLoan);

    return view('microfin.reports.dailyRecoverableAndCollectionRegisterViews.DailyRecoverableAndCollectionRegisterTable', compact('BranchInfo', 'RequestedDate', 'SavingsProduct', 'RequiredTableInfos', 'RequestedProduct', 'RequestedLoanOptions', 'RequestedBranchInfos'));
  }

}
