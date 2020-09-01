<?php

namespace App\Http\Controllers\microfin\reports\advanceDueListController;

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


 // - Controller for Branch Wise Samity Report
 // - Created By Atiqul Haque
 // - Date:11/04/18

class AdvanceDueListController extends Controller
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
    // dd($UniqueLoanYear);
    // return 'Hello';
    // \\120.50.0.141\erp\resources\views\microfin\reports\advanceDueListViews\AdvanceDueListForm.blade.php
    return view('microfin.reports.advanceDueListViews.AdvanceDueListForm', compact('BranchDatas', 'FundingOrganization', 'UniqueLoanYear'));
  }

  public function getSamity(Request $request ){
    $SamityName = DB::table('mfn_samity')
                  ->get();

    return response()->json($SamityName);
  }

  public function getProCat(Request $request){
    $SearchProCat = $request->id;
    $ProCatName = array();

    if ($SearchProCat == 1) {
      $ProCatName = DB::table('mfn_loans_product')
                    ->get();
      // $ProCatName = ['1', '2'];
    }
    elseif ($SearchProCat == 2) {
      $ProCatName = DB::table('mfn_loans_product_category')
                    ->get();
    }

    return response()->json($ProCatName);
  }

  public function getTable(Request $request){
    $RequestBranch = $request->searchBranch;
    $RequestSamity = $request->searchSamity;
    $RequestSearchType = $request->searchType;
    $RequestProCat = $request->searchProCat;
    $RequestDate = $request->txtDate1;
    // dd($RequestProCat);

    $BranchInfos = array();
    $SamityInfos = array();
    $MemberLists = array();
    $MemberLoanInstallmentInfos = array();
    $MemberLoanInstallmentAmounts = array();
    $MemberLoanCollectionAmounts = array();
    $MemberAdvDueListArrays = array();
    $MemberLoanInstallmentAmountsOnlyPrincipals = array();
    $MemberLoanCollectionAmountsOnlyPrincipals = array();
    $MemberAdvDueListArraysOnlyPrincipals = array();
    $MargedAdvDueListArrays = array();

    $BranchInfos = DB::table('gnr_branch')
                  ->where('id', $RequestBranch)
                  ->get();

    if ($RequestSamity != 'all') {
      $SamityInfos = DB::table('mfn_samity')
                    ->where('branchId', $RequestBranch)
                    ->where('id', $RequestSamity)
                    ->get();
    }
    elseif ($RequestSamity == 'all') {
      $SamityInfos = DB::table('mfn_samity')
                    ->where('branchId', $RequestBranch)
                    ->get();

                    // dd($SamityInfos);
    }

    if ($RequestSamity != 'all') {        //If samity is not selected all
      if ($RequestSearchType == 1) {          //Product wise SEARCH......
        if ($RequestProCat != 'all') {        //If Product not selected as all
          foreach ($SamityInfos as $key => $SamityInfo) {
            $Id = $SamityInfo->id;
            $MemberLists = DB::table('mfn_loan')
                          ->select('mfn_loan.id', 'mfn_loan.samityIdFk', 'mfn_loan.branchIdFk', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.actualNumberOfInstallment', 'mfn_loan.actualInstallmentAmount', 'mfn_loan.installmentAmount', 'mfn_loan.insuranceAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.interestAmount', 'mfn_loan.isLoanCompleted', 'mfn_member_information.id as memberId', 'mfn_member_information.name')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          // ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                          ->where('mfn_loan.samityIdFk', $Id)
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.productIdFk', $RequestProCat)
                          ->distinct()
                          ->get();
          }

          foreach ($MemberLists as $key => $MemberList) {
            $Member = $MemberList->memberId;
            $MemberLoanInstallmentInfos[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                          ->count('mfn_loan_schedule.scheduleDate');

            $MemberLoanInstallmentAmounts[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                          ->sum('mfn_loan_schedule.installmentAmount');

            $MemberLoanInstallmentAmountsOnlyPrincipals[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                          ->sum('mfn_loan_schedule.principalAmount');

            $MemberLoanCollectionAmounts[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_collection.collectionDate', '<=', $RequestDate)
                          ->sum('mfn_loan_collection.amount');

            $MemberLoanCollectionAmountsOnlyPrincipals[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_collection.collectionDate', '<=', $RequestDate)
                          ->sum('mfn_loan_collection.principalAmount');
          }

          foreach ($MemberLoanInstallmentAmounts as $key => $MemberLoanInstallmentAmount) {
            foreach ($MemberLoanCollectionAmounts as $key1 => $MemberLoanCollectionAmount) {
              if ($key == $key1) {
                if ($MemberLoanInstallmentAmount > $MemberLoanCollectionAmount) {
                  $MemberAdvDueListArrays[$key]['Due'] = $MemberLoanInstallmentAmount - $MemberLoanCollectionAmount;
                }
                elseif ($MemberLoanInstallmentAmount < $MemberLoanCollectionAmount) {
                  $MemberAdvDueListArrays[$key]['Adv'] = $MemberLoanCollectionAmount - $MemberLoanInstallmentAmount;
                }
              }
            }
          }

          // dd($MemberAdvDueListArrays);

          // foreach ($MemberLoanInstallmentAmountsOnlyPrincipals as $key => $MemberLoanInstallmentAmountsOnlyPrincipal) {
          //   foreach ($MemberLoanCollectionAmountsOnlyPrincipals as $key1 => $MemberLoanCollectionAmountsOnlyPrincipal) {
          //     if ($key == $key1) {
          //       if ($MemberLoanInstallmentAmountsOnlyPrincipal > $MemberLoanCollectionAmountsOnlyPrincipal) {
          //         $MemberAdvDueListArrays[$key]['OnlyPrincipalDue'] = $MemberLoanInstallmentAmountsOnlyPrincipal - $MemberLoanCollectionAmountsOnlyPrincipal;
          //       }
          //       elseif ($MemberLoanInstallmentAmountsOnlyPrincipal < $MemberLoanCollectionAmountsOnlyPrincipal) {
          //         $MemberAdvDueListArrays[$key]['OnlyPrincipalAdv'] = $MemberLoanInstallmentAmountsOnlyPrincipal - $MemberLoanCollectionAmountsOnlyPrincipal;
          //       }
          //     }
          //   }
          // }

          foreach ($MemberLoanInstallmentAmountsOnlyPrincipals as $key => $MemberLoanInstallmentAmountsOnlyPrincipal) {
            foreach ($MemberLoanCollectionAmountsOnlyPrincipals as $key1 => $MemberLoanCollectionAmountsOnlyPrincipal) {
              if ($key == $key1) {
                if ($MemberLoanInstallmentAmountsOnlyPrincipal > $MemberLoanCollectionAmountsOnlyPrincipal) {
                  $MargedAdvDueListArrays[$key]['Due'] = $MemberLoanInstallmentAmountsOnlyPrincipal - $MemberLoanCollectionAmountsOnlyPrincipal;
                }
                elseif ($MemberLoanInstallmentAmountsOnlyPrincipal < $MemberLoanCollectionAmountsOnlyPrincipal) {
                  $MargedAdvDueListArrays[$key]['Adv'] = $MemberLoanCollectionAmountsOnlyPrincipal - $MemberLoanInstallmentAmountsOnlyPrincipal;
                }
              }
            }
          }

        }
        elseif ($RequestProCat == 'all') {          //If Product selected as all
          foreach ($SamityInfos as $key => $SamityInfo) {
            $Id = $SamityInfo->id;
            $MemberLists = DB::table('mfn_loan')
                          ->select('mfn_loan.id', 'mfn_loan.samityIdFk', 'mfn_loan.branchIdFk', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.actualNumberOfInstallment', 'mfn_loan.actualInstallmentAmount', 'mfn_loan.installmentAmount', 'mfn_loan.insuranceAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.interestAmount', 'mfn_loan.isLoanCompleted', 'mfn_member_information.id as memberId', 'mfn_member_information.name')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          // ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                          ->where('mfn_loan.samityIdFk', $Id)
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          // ->where('mfn_loan.productIdFk', $RequestProCat)
                          ->distinct()
                          ->get();
          }

          foreach ($MemberLists as $key => $MemberList) {
            $Member = $MemberList->memberId;
            $MemberLoanInstallmentInfos[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                          ->count('mfn_loan_schedule.scheduleDate');

            $MemberLoanInstallmentAmounts[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                          ->sum('mfn_loan_schedule.installmentAmount');

            $MemberLoanInstallmentAmountsOnlyPrincipals[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                          ->sum('mfn_loan_schedule.principalAmount');

            $MemberLoanCollectionAmounts[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_collection.collectionDate', '<=', $RequestDate)
                          ->sum('mfn_loan_collection.amount');

            $MemberLoanCollectionAmountsOnlyPrincipals[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_collection.collectionDate', '<=', $RequestDate)
                          ->sum('mfn_loan_collection.principalAmount');
          }

          foreach ($MemberLoanInstallmentAmounts as $key => $MemberLoanInstallmentAmount) {
            foreach ($MemberLoanCollectionAmounts as $key1 => $MemberLoanCollectionAmount) {
              if ($key == $key1) {
                if ($MemberLoanInstallmentAmount > $MemberLoanCollectionAmount) {
                  $MemberAdvDueListArrays[$key]['Due'] = $MemberLoanInstallmentAmount - $MemberLoanCollectionAmount;
                }
                elseif ($MemberLoanInstallmentAmount < $MemberLoanCollectionAmount) {
                  $MemberAdvDueListArrays[$key]['Adv'] = $MemberLoanCollectionAmount - $MemberLoanInstallmentAmount;
                }
              }
            }
          }

          // foreach ($MemberLoanInstallmentAmountsOnlyPrincipals as $key => $MemberLoanInstallmentAmountsOnlyPrincipal) {
          //   foreach ($MemberLoanCollectionAmountsOnlyPrincipals as $key1 => $MemberLoanCollectionAmountsOnlyPrincipal) {
          //     if ($key == $key1) {
          //       if ($MemberLoanInstallmentAmountsOnlyPrincipal > $MemberLoanCollectionAmountsOnlyPrincipal) {
          //         $MemberAdvDueListArrays[$key]['OnlyPrincipalDue'] = $MemberLoanInstallmentAmountsOnlyPrincipal - $MemberLoanCollectionAmountsOnlyPrincipal;
          //       }
          //       elseif ($MemberLoanInstallmentAmountsOnlyPrincipal < $MemberLoanCollectionAmountsOnlyPrincipal) {
          //         $MemberAdvDueListArrays[$key]['OnlyPrincipalAdv'] = $MemberLoanInstallmentAmountsOnlyPrincipal - $MemberLoanCollectionAmountsOnlyPrincipal;
          //       }
          //     }
          //   }
          // }

          foreach ($MemberLoanInstallmentAmountsOnlyPrincipals as $key => $MemberLoanInstallmentAmountsOnlyPrincipal) {
            foreach ($MemberLoanCollectionAmountsOnlyPrincipals as $key1 => $MemberLoanCollectionAmountsOnlyPrincipal) {
              if ($key == $key1) {
                if ($MemberLoanInstallmentAmountsOnlyPrincipal > $MemberLoanCollectionAmountsOnlyPrincipal) {
                  $MargedAdvDueListArrays[$key]['Due'] = $MemberLoanInstallmentAmountsOnlyPrincipal - $MemberLoanCollectionAmountsOnlyPrincipal;
                }
                elseif ($MemberLoanInstallmentAmountsOnlyPrincipal < $MemberLoanCollectionAmountsOnlyPrincipal) {
                  $MargedAdvDueListArrays[$key]['Adv'] = $MemberLoanCollectionAmountsOnlyPrincipal - $MemberLoanInstallmentAmountsOnlyPrincipal;
                }
              }
            }
          }

        }

      }           //End of Product wise SEARCH.......
      elseif ($RequestSearchType ==2) {         //Product Category wise SEARCH.......
        if ($RequestProCat != 'all') {          //If Product Category is not selected as all......
          foreach ($SamityInfos as $key => $SamityInfo) {
            $Id = $SamityInfo->id;
            $MemberLists = DB::table('mfn_loan')
                          ->select('mfn_loan.id', 'mfn_loan.samityIdFk', 'mfn_loan.branchIdFk', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.actualNumberOfInstallment', 'mfn_loan.actualInstallmentAmount', 'mfn_loan.installmentAmount', 'mfn_loan.insuranceAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.interestAmount', 'mfn_loan.isLoanCompleted', 'mfn_member_information.id as memberId', 'mfn_member_information.name')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                          ->where('mfn_loan.samityIdFk', $Id)
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          // ->where('mfn_loan.productIdFk', $RequestProCat)
                          ->where('mfn_loans_product.productCategoryId', $RequestProCat)
                          ->distinct()
                          ->get();
          }

          foreach ($MemberLists as $key => $MemberList) {
            $Member = $MemberList->memberId;
            $MemberLoanInstallmentInfos[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                          ->count('mfn_loan_schedule.scheduleDate');

            $MemberLoanInstallmentAmounts[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                          ->sum('mfn_loan_schedule.installmentAmount');

            $MemberLoanInstallmentAmountsOnlyPrincipals[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                          ->sum('mfn_loan_schedule.principalAmount');

            $MemberLoanCollectionAmounts[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_collection.collectionDate', '<=', $RequestDate)
                          ->sum('mfn_loan_collection.amount');

            $MemberLoanCollectionAmountsOnlyPrincipals[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_collection.collectionDate', '<=', $RequestDate)
                          ->sum('mfn_loan_collection.principalAmount');
          }

          foreach ($MemberLoanInstallmentAmounts as $key => $MemberLoanInstallmentAmount) {
            foreach ($MemberLoanCollectionAmounts as $key1 => $MemberLoanCollectionAmount) {
              if ($key == $key1) {
                if ($MemberLoanInstallmentAmount > $MemberLoanCollectionAmount) {
                  $MemberAdvDueListArrays[$key]['Due'] = $MemberLoanInstallmentAmount - $MemberLoanCollectionAmount;
                }
                elseif ($MemberLoanInstallmentAmount < $MemberLoanCollectionAmount) {
                  $MemberAdvDueListArrays[$key]['Adv'] = $MemberLoanCollectionAmount - $MemberLoanInstallmentAmount;
                }
              }
            }
          }

          // dd($MemberAdvDueListArrays);

          // foreach ($MemberLoanInstallmentAmountsOnlyPrincipals as $key => $MemberLoanInstallmentAmountsOnlyPrincipal) {
          //   foreach ($MemberLoanCollectionAmountsOnlyPrincipals as $key1 => $MemberLoanCollectionAmountsOnlyPrincipal) {
          //     if ($key == $key1) {
          //       if ($MemberLoanInstallmentAmountsOnlyPrincipal > $MemberLoanCollectionAmountsOnlyPrincipal) {
          //         $MemberAdvDueListArrays[$key]['OnlyPrincipalDue'] = $MemberLoanInstallmentAmountsOnlyPrincipal - $MemberLoanCollectionAmountsOnlyPrincipal;
          //       }
          //       elseif ($MemberLoanInstallmentAmountsOnlyPrincipal < $MemberLoanCollectionAmountsOnlyPrincipal) {
          //         $MemberAdvDueListArrays[$key]['OnlyPrincipalAdv'] = $MemberLoanInstallmentAmountsOnlyPrincipal - $MemberLoanCollectionAmountsOnlyPrincipal;
          //       }
          //     }
          //   }
          // }

          foreach ($MemberLoanInstallmentAmountsOnlyPrincipals as $key => $MemberLoanInstallmentAmountsOnlyPrincipal) {
            foreach ($MemberLoanCollectionAmountsOnlyPrincipals as $key1 => $MemberLoanCollectionAmountsOnlyPrincipal) {
              if ($key == $key1) {
                if ($MemberLoanInstallmentAmountsOnlyPrincipal > $MemberLoanCollectionAmountsOnlyPrincipal) {
                  $MargedAdvDueListArrays[$key]['Due'] = $MemberLoanInstallmentAmountsOnlyPrincipal - $MemberLoanCollectionAmountsOnlyPrincipal;
                }
                elseif ($MemberLoanInstallmentAmountsOnlyPrincipal < $MemberLoanCollectionAmountsOnlyPrincipal) {
                  $MargedAdvDueListArrays[$key]['Adv'] = $MemberLoanCollectionAmountsOnlyPrincipal - $MemberLoanInstallmentAmountsOnlyPrincipal;
                }
              }
            }
          }

          // dd($MargedAdvDueListArrays);

        }
        elseif ($RequestProCat == 'all') {            //If Product Category slected as all
          foreach ($SamityInfos as $key => $SamityInfo) {
            $Id = $SamityInfo->id;
            $MemberLists = DB::table('mfn_loan')
                          ->select('mfn_loan.id', 'mfn_loan.samityIdFk', 'mfn_loan.branchIdFk', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.actualNumberOfInstallment', 'mfn_loan.actualInstallmentAmount', 'mfn_loan.installmentAmount', 'mfn_loan.insuranceAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.interestAmount', 'mfn_loan.isLoanCompleted', 'mfn_member_information.id as memberId', 'mfn_member_information.name')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                          ->where('mfn_loan.samityIdFk', $Id)
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          // ->where('mfn_loan.productIdFk', $RequestProCat)
                          // ->where('mfn_loans_product.productCategoryId', $RequestProCat)
                          ->distinct()
                          ->get();
          }

          foreach ($MemberLists as $key => $MemberList) {
            $Member = $MemberList->memberId;
            $MemberLoanInstallmentInfos[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                          ->count('mfn_loan_schedule.scheduleDate');

            $MemberLoanInstallmentAmounts[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                          ->sum('mfn_loan_schedule.installmentAmount');

            $MemberLoanInstallmentAmountsOnlyPrincipals[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                          ->sum('mfn_loan_schedule.principalAmount');

            $MemberLoanCollectionAmounts[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_collection.collectionDate', '<=', $RequestDate)
                          ->sum('mfn_loan_collection.amount');

            $MemberLoanCollectionAmountsOnlyPrincipals[$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $RequestSamity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_collection.collectionDate', '<=', $RequestDate)
                          ->sum('mfn_loan_collection.principalAmount');
          }

          foreach ($MemberLoanInstallmentAmounts as $key => $MemberLoanInstallmentAmount) {
            foreach ($MemberLoanCollectionAmounts as $key1 => $MemberLoanCollectionAmount) {
              if ($key == $key1) {
                if ($MemberLoanInstallmentAmount > $MemberLoanCollectionAmount) {
                  $MemberAdvDueListArrays[$key]['Due'] = $MemberLoanInstallmentAmount - $MemberLoanCollectionAmount;
                }
                elseif ($MemberLoanInstallmentAmount < $MemberLoanCollectionAmount) {
                  $MemberAdvDueListArrays[$key]['Adv'] = $MemberLoanCollectionAmount - $MemberLoanInstallmentAmount;
                }
              }
            }
          }

          // foreach ($MemberLoanInstallmentAmountsOnlyPrincipals as $key => $MemberLoanInstallmentAmountsOnlyPrincipal) {
          //   foreach ($MemberLoanCollectionAmountsOnlyPrincipals as $key1 => $MemberLoanCollectionAmountsOnlyPrincipal) {
          //     if ($key == $key1) {
          //       if ($MemberLoanInstallmentAmountsOnlyPrincipal > $MemberLoanCollectionAmountsOnlyPrincipal) {
          //         $MemberAdvDueListArrays[$key]['OnlyPrincipalDue'] = $MemberLoanInstallmentAmountsOnlyPrincipal - $MemberLoanCollectionAmountsOnlyPrincipal;
          //       }
          //       elseif ($MemberLoanInstallmentAmountsOnlyPrincipal < $MemberLoanCollectionAmountsOnlyPrincipal) {
          //         $MemberAdvDueListArrays[$key]['OnlyPrincipalAdv'] = $MemberLoanInstallmentAmountsOnlyPrincipal - $MemberLoanCollectionAmountsOnlyPrincipal;
          //       }
          //     }
          //   }
          // }

          foreach ($MemberLoanInstallmentAmountsOnlyPrincipals as $key => $MemberLoanInstallmentAmountsOnlyPrincipal) {
            foreach ($MemberLoanCollectionAmountsOnlyPrincipals as $key1 => $MemberLoanCollectionAmountsOnlyPrincipal) {
              if ($key == $key1) {
                if ($MemberLoanInstallmentAmountsOnlyPrincipal > $MemberLoanCollectionAmountsOnlyPrincipal) {
                  $MargedAdvDueListArrays[$key]['Due'] = $MemberLoanInstallmentAmountsOnlyPrincipal - $MemberLoanCollectionAmountsOnlyPrincipal;
                }
                elseif ($MemberLoanInstallmentAmountsOnlyPrincipal < $MemberLoanCollectionAmountsOnlyPrincipal) {
                  $MargedAdvDueListArrays[$key]['Adv'] =  $MemberLoanCollectionAmountsOnlyPrincipal - $MemberLoanInstallmentAmountsOnlyPrincipal;
                }
              }
            }
          }

        }

      }           //End of Product Category wise SEARCH.......

    }         //End of samity is not selected as all
    elseif ($RequestSamity == 'all') {          //If samity is selected as all
      if ($RequestSearchType == 1) {          //Product wise SEARCH......
        if ($RequestProCat != 'all') {        //If Product not selected as all
          foreach ($SamityInfos as $key => $SamityInfo) {
            $Id = $SamityInfo->id;
            $MemberLists[$Id] = DB::table('mfn_loan')
                          ->select('mfn_loan.id', 'mfn_loan.samityIdFk', 'mfn_loan.branchIdFk', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.actualNumberOfInstallment', 'mfn_loan.actualInstallmentAmount', 'mfn_loan.installmentAmount', 'mfn_loan.insuranceAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.interestAmount', 'mfn_loan.isLoanCompleted', 'mfn_member_information.id as memberId', 'mfn_member_information.name')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          // ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                          ->where('mfn_loan.samityIdFk', $Id)
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.productIdFk', $RequestProCat)
                          ->distinct()
                          ->get();
          }
          // dd($MemberLists);

          foreach ($MemberLists as $key => $MemberList) {
            foreach ($MemberList as $key => $MemberListss) {
              $Member = $MemberListss->memberId;
              $Samity = $MemberListss->samityIdFk;
              $MemberLoanInstallmentInfos[$Samity][$Member] = DB::table('mfn_loan')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->where('mfn_loan.branchIdFk', $RequestBranch)
                            ->where('mfn_loan.samityIdFk', $Samity)
                            ->where('mfn_loan.memberIdFk', $Member)
                            ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                            ->count('mfn_loan_schedule.scheduleDate');

              $MemberLoanInstallmentAmounts[$Samity][$Member] = DB::table('mfn_loan')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->where('mfn_loan.branchIdFk', $RequestBranch)
                            ->where('mfn_loan.samityIdFk', $Samity)
                            ->where('mfn_loan.memberIdFk', $Member)
                            ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                            ->sum('mfn_loan_schedule.installmentAmount');

              $MemberLoanInstallmentAmountsOnlyPrincipals[$Samity][$Member] = DB::table('mfn_loan')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->where('mfn_loan.branchIdFk', $RequestBranch)
                            ->where('mfn_loan.samityIdFk', $Samity)
                            ->where('mfn_loan.memberIdFk', $Member)
                            ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                            ->sum('mfn_loan_schedule.principalAmount');

              $MemberLoanCollectionAmounts[$Samity][$Member] = DB::table('mfn_loan')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->where('mfn_loan.branchIdFk', $RequestBranch)
                            ->where('mfn_loan.samityIdFk', $Samity)
                            ->where('mfn_loan.memberIdFk', $Member)
                            ->where('mfn_loan_collection.collectionDate', '<=', $RequestDate)
                            ->sum('mfn_loan_collection.amount');

              $MemberLoanCollectionAmountsOnlyPrincipals[$Samity][$Member] = DB::table('mfn_loan')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->where('mfn_loan.branchIdFk', $RequestBranch)
                            ->where('mfn_loan.samityIdFk', $Samity)
                            ->where('mfn_loan.memberIdFk', $Member)
                            ->where('mfn_loan_collection.collectionDate', '<=', $RequestDate)
                            ->sum('mfn_loan_collection.principalAmount');
            }
          }
          // dd($MemberLoanInstallmentInfos);
          // dd($MemberLoanInstallmentAmounts);
          // dd($MemberLoanInstallmentAmountsOnlyPrincipals);
          // dd($MemberLoanCollectionAmounts);
          // dd($MemberLoanCollectionAmountsOnlyPrincipals);

          foreach ($MemberLoanInstallmentAmounts as $key1 => $MemberLoanInstallmentAmount) {
            foreach ($MemberLoanInstallmentAmount as $key2 => $MemberLoanInstallmentAmountss) {
              foreach ($MemberLoanCollectionAmounts as $key3 => $MemberLoanCollectionAmount) {
                foreach ($MemberLoanCollectionAmount as $key4 => $MemberLoanCollectionAmountss) {
                  if ($key1 == $key3 and $key2 == $key4) {
                    if ($MemberLoanInstallmentAmountss > $MemberLoanCollectionAmountss) {
                      $MemberAdvDueListArrays[$key1]['Due/'.$key2]= $MemberLoanInstallmentAmountss - $MemberLoanCollectionAmountss;
                    }
                    elseif ($MemberLoanInstallmentAmountss < $MemberLoanCollectionAmountss) {
                      $MemberAdvDueListArrays[$key1]['Adv/'.$key2] = $MemberLoanCollectionAmountss - $MemberLoanInstallmentAmountss;
                    }
                  }
                }
              }
            }

          }
          // dd($MemberAdvDueListArrays);

          foreach ($MemberLoanInstallmentAmountsOnlyPrincipals as $key1 => $MemberLoanInstallmentAmount) {
            foreach ($MemberLoanInstallmentAmount as $key2 => $MemberLoanInstallmentAmountss) {
              foreach ($MemberLoanCollectionAmountsOnlyPrincipals as $key3 => $MemberLoanCollectionAmount) {
                foreach ($MemberLoanCollectionAmount as $key4 => $MemberLoanCollectionAmountss) {
                  if ($key1 == $key3 and $key2 == $key4) {
                    if ($MemberLoanInstallmentAmountss > $MemberLoanCollectionAmountss) {
                      $MargedAdvDueListArrays[$key1]['Due/'.$key2] = $MemberLoanInstallmentAmountss - $MemberLoanCollectionAmountss;
                    }
                    elseif ($MemberLoanInstallmentAmountss < $MemberLoanCollectionAmountss) {
                      $MargedAdvDueListArrays[$key1]['Adv/'.$key2] = $MemberLoanCollectionAmountss - $MemberLoanInstallmentAmountss;
                    }
                  }
                }
              }
            }

          }
          // dd($MargedAdvDueListArrays);

        }
        elseif ($RequestProCat == 'all') {          //If Product selected as all
          foreach ($SamityInfos as $key => $SamityInfo) {
            $Id = $SamityInfo->id;
            $MemberLists[$Id] = DB::table('mfn_loan')
                          ->select('mfn_loan.id', 'mfn_loan.samityIdFk', 'mfn_loan.branchIdFk', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.actualNumberOfInstallment', 'mfn_loan.actualInstallmentAmount', 'mfn_loan.installmentAmount', 'mfn_loan.insuranceAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.interestAmount', 'mfn_loan.isLoanCompleted', 'mfn_member_information.id as memberId', 'mfn_member_information.name')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          // ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                          ->where('mfn_loan.samityIdFk', $Id)
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          // ->where('mfn_loan.productIdFk', $RequestProCat)
                          ->distinct()
                          ->get();
          }

          foreach ($MemberLists as $key => $MemberList) {
          foreach ($MemberList as $key => $MemberListss) {
            $Member = $MemberListss->memberId;
            $Samity = $MemberListss->samityIdFk;
            $MemberLoanInstallmentInfos[$Samity][$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $Samity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                          ->count('mfn_loan_schedule.scheduleDate');

            $MemberLoanInstallmentAmounts[$Samity][$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $Samity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                          ->sum('mfn_loan_schedule.installmentAmount');

            $MemberLoanInstallmentAmountsOnlyPrincipals[$Samity][$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $Samity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                          ->sum('mfn_loan_schedule.principalAmount');

            $MemberLoanCollectionAmounts[$Samity][$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $Samity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_collection.collectionDate', '<=', $RequestDate)
                          ->sum('mfn_loan_collection.amount');

            $MemberLoanCollectionAmountsOnlyPrincipals[$Samity][$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $Samity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_collection.collectionDate', '<=', $RequestDate)
                          ->sum('mfn_loan_collection.principalAmount');
          }
        }

        // dd($MemberLoanInstallmentInfos);
        // dd($MemberLoanInstallmentAmounts);
        // dd($MemberLoanInstallmentAmountsOnlyPrincipals);
        // dd($MemberLoanCollectionAmounts);
        // dd($MemberLoanCollectionAmountsOnlyPrincipals);

        foreach ($MemberLoanInstallmentAmounts as $key1 => $MemberLoanInstallmentAmount) {
          foreach ($MemberLoanInstallmentAmount as $key2 => $MemberLoanInstallmentAmountss) {
            foreach ($MemberLoanCollectionAmounts as $key3 => $MemberLoanCollectionAmount) {
              foreach ($MemberLoanCollectionAmount as $key4 => $MemberLoanCollectionAmountss) {
                if ($key1 == $key3 and $key2 == $key4) {
                  if ($MemberLoanInstallmentAmountss > $MemberLoanCollectionAmountss) {
                    $MemberAdvDueListArrays[$key1]['Due/'.$key2]= $MemberLoanInstallmentAmountss - $MemberLoanCollectionAmountss;
                  }
                  elseif ($MemberLoanInstallmentAmountss < $MemberLoanCollectionAmountss) {
                    $MemberAdvDueListArrays[$key1]['Adv/'.$key2] = $MemberLoanCollectionAmountss - $MemberLoanInstallmentAmountss;
                  }
                }
              }
            }
          }

        }

        // dd($MemberAdvDueListArrays);

        foreach ($MemberLoanInstallmentAmountsOnlyPrincipals as $key1 => $MemberLoanInstallmentAmount) {
          foreach ($MemberLoanInstallmentAmount as $key2 => $MemberLoanInstallmentAmountss) {
            foreach ($MemberLoanCollectionAmountsOnlyPrincipals as $key3 => $MemberLoanCollectionAmount) {
              foreach ($MemberLoanCollectionAmount as $key4 => $MemberLoanCollectionAmountss) {
                if ($key1 == $key3 and $key2 == $key4) {
                  if ($MemberLoanInstallmentAmountss > $MemberLoanCollectionAmountss) {
                    $MargedAdvDueListArrays[$key1]['Due/'.$key2] = $MemberLoanInstallmentAmountss - $MemberLoanCollectionAmountss;
                  }
                  elseif ($MemberLoanInstallmentAmountss < $MemberLoanCollectionAmountss) {
                    $MargedAdvDueListArrays[$key1]['Adv/'.$key2] = $MemberLoanCollectionAmountss - $MemberLoanInstallmentAmountss;
                  }
                }
              }
            }
          }

        }

        }

      }           //End of Product wise SEARCH.......
      elseif ($RequestSearchType ==2) {         //Product Category wise SEARCH.......
        if ($RequestProCat != 'all') {          //If Product Category not selected as all......
          foreach ($SamityInfos as $key => $SamityInfo) {
            $Id = $SamityInfo->id;
            $MemberLists[$Id] = DB::table('mfn_loan')
                          ->select('mfn_loan.id', 'mfn_loan.samityIdFk', 'mfn_loan.branchIdFk', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.actualNumberOfInstallment', 'mfn_loan.actualInstallmentAmount', 'mfn_loan.installmentAmount', 'mfn_loan.insuranceAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.interestAmount', 'mfn_loan.isLoanCompleted', 'mfn_member_information.id as memberId', 'mfn_member_information.name')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                          ->where('mfn_loan.samityIdFk', $Id)
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          // ->where('mfn_loan.productIdFk', $RequestProCat)
                          ->where('mfn_loans_product.productCategoryId', $RequestProCat)
                          ->distinct()
                          ->get();
          }

          // dd($MemberLists);

          foreach ($MemberLists as $key => $MemberList) {
            foreach ($MemberList as $key => $MemberListss) {
              $Member = $MemberListss->memberId;
              $Samity = $MemberListss->samityIdFk;
              $MemberLoanInstallmentInfos[$Samity][$Member] = DB::table('mfn_loan')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->where('mfn_loan.branchIdFk', $RequestBranch)
                            ->where('mfn_loan.samityIdFk', $Samity)
                            ->where('mfn_loan.memberIdFk', $Member)
                            ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                            ->count('mfn_loan_schedule.scheduleDate');

              $MemberLoanInstallmentAmounts[$Samity][$Member] = DB::table('mfn_loan')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->where('mfn_loan.branchIdFk', $RequestBranch)
                            ->where('mfn_loan.samityIdFk', $Samity)
                            ->where('mfn_loan.memberIdFk', $Member)
                            ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                            ->sum('mfn_loan_schedule.installmentAmount');

              $MemberLoanInstallmentAmountsOnlyPrincipals[$Samity][$Member] = DB::table('mfn_loan')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->where('mfn_loan.branchIdFk', $RequestBranch)
                            ->where('mfn_loan.samityIdFk', $Samity)
                            ->where('mfn_loan.memberIdFk', $Member)
                            ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                            ->sum('mfn_loan_schedule.principalAmount');

              $MemberLoanCollectionAmounts[$Samity][$Member] = DB::table('mfn_loan')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->where('mfn_loan.branchIdFk', $RequestBranch)
                            ->where('mfn_loan.samityIdFk', $Samity)
                            ->where('mfn_loan.memberIdFk', $Member)
                            ->where('mfn_loan_collection.collectionDate', '<=', $RequestDate)
                            ->sum('mfn_loan_collection.amount');

              $MemberLoanCollectionAmountsOnlyPrincipals[$Samity][$Member] = DB::table('mfn_loan')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->where('mfn_loan.branchIdFk', $RequestBranch)
                            ->where('mfn_loan.samityIdFk', $Samity)
                            ->where('mfn_loan.memberIdFk', $Member)
                            ->where('mfn_loan_collection.collectionDate', '<=', $RequestDate)
                            ->sum('mfn_loan_collection.principalAmount');
            }
          }
          // dd($MemberLoanInstallmentInfos);
          // dd($MemberLoanInstallmentAmounts);
          // dd($MemberLoanInstallmentAmountsOnlyPrincipals);
          // dd($MemberLoanCollectionAmounts);
          // dd($MemberLoanCollectionAmountsOnlyPrincipals);

          foreach ($MemberLoanInstallmentAmounts as $key1 => $MemberLoanInstallmentAmount) {
            foreach ($MemberLoanInstallmentAmount as $key2 => $MemberLoanInstallmentAmountss) {
              foreach ($MemberLoanCollectionAmounts as $key3 => $MemberLoanCollectionAmount) {
                foreach ($MemberLoanCollectionAmount as $key4 => $MemberLoanCollectionAmountss) {
                  if ($key1 == $key3 and $key2 == $key4) {
                    if ($MemberLoanInstallmentAmountss > $MemberLoanCollectionAmountss) {
                      $MemberAdvDueListArrays[$key1]['Due/'.$key2]= $MemberLoanInstallmentAmountss - $MemberLoanCollectionAmountss;
                    }
                    elseif ($MemberLoanInstallmentAmountss < $MemberLoanCollectionAmountss) {
                      $MemberAdvDueListArrays[$key1]['Adv/'.$key2] = $MemberLoanCollectionAmountss - $MemberLoanInstallmentAmountss;
                    }
                  }
                }
              }
            }

          }
          // dd($MemberAdvDueListArrays);

          foreach ($MemberLoanInstallmentAmountsOnlyPrincipals as $key1 => $MemberLoanInstallmentAmount) {
            foreach ($MemberLoanInstallmentAmount as $key2 => $MemberLoanInstallmentAmountss) {
              foreach ($MemberLoanCollectionAmountsOnlyPrincipals as $key3 => $MemberLoanCollectionAmount) {
                foreach ($MemberLoanCollectionAmount as $key4 => $MemberLoanCollectionAmountss) {
                  if ($key1 == $key3 and $key2 == $key4) {
                    if ($MemberLoanInstallmentAmountss > $MemberLoanCollectionAmountss) {
                      $MargedAdvDueListArrays[$key1]['Due/'.$key2] = $MemberLoanInstallmentAmountss - $MemberLoanCollectionAmountss;
                    }
                    elseif ($MemberLoanInstallmentAmountss < $MemberLoanCollectionAmountss) {
                      $MargedAdvDueListArrays[$key1]['Adv/'.$key2] = $MemberLoanCollectionAmountss - $MemberLoanInstallmentAmountss;
                    }
                  }
                }
              }
            }

          }
          // dd($MargedAdvDueListArrays);

        }
        elseif ($RequestProCat == 'all') {            //If Product Category slected as all
          foreach ($SamityInfos as $key => $SamityInfo) {
            $Id = $SamityInfo->id;
            $MemberLists[$Id] = DB::table('mfn_loan')
                          ->select('mfn_loan.id', 'mfn_loan.samityIdFk', 'mfn_loan.branchIdFk', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.actualNumberOfInstallment', 'mfn_loan.actualInstallmentAmount', 'mfn_loan.installmentAmount', 'mfn_loan.insuranceAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.interestAmount', 'mfn_loan.isLoanCompleted', 'mfn_member_information.id as memberId', 'mfn_member_information.name')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                          ->where('mfn_loan.samityIdFk', $Id)
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          // ->where('mfn_loan.productIdFk', $RequestProCat)
                          // ->where('mfn_loans_product.productCategoryId', $RequestProCat)
                          ->distinct()
                          ->get();
          }

          foreach ($MemberLists as $key => $MemberList) {
          foreach ($MemberList as $key => $MemberListss) {
            $Member = $MemberListss->memberId;
            $Samity = $MemberListss->samityIdFk;
            $MemberLoanInstallmentInfos[$Samity][$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $Samity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                          ->count('mfn_loan_schedule.scheduleDate');

            $MemberLoanInstallmentAmounts[$Samity][$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $Samity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                          ->sum('mfn_loan_schedule.installmentAmount');

            $MemberLoanInstallmentAmountsOnlyPrincipals[$Samity][$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $Samity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $RequestDate)
                          ->sum('mfn_loan_schedule.principalAmount');

            $MemberLoanCollectionAmounts[$Samity][$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $Samity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_collection.collectionDate', '<=', $RequestDate)
                          ->sum('mfn_loan_collection.amount');

            $MemberLoanCollectionAmountsOnlyPrincipals[$Samity][$Member] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.samityIdFk', $Samity)
                          ->where('mfn_loan.memberIdFk', $Member)
                          ->where('mfn_loan_collection.collectionDate', '<=', $RequestDate)
                          ->sum('mfn_loan_collection.principalAmount');
          }
        }

        // dd($MemberLoanInstallmentInfos);
        // dd($MemberLoanInstallmentAmounts);
        // dd($MemberLoanInstallmentAmountsOnlyPrincipals);
        // dd($MemberLoanCollectionAmounts);
        // dd($MemberLoanCollectionAmountsOnlyPrincipals);

        foreach ($MemberLoanInstallmentAmounts as $key1 => $MemberLoanInstallmentAmount) {
          foreach ($MemberLoanInstallmentAmount as $key2 => $MemberLoanInstallmentAmountss) {
            foreach ($MemberLoanCollectionAmounts as $key3 => $MemberLoanCollectionAmount) {
              foreach ($MemberLoanCollectionAmount as $key4 => $MemberLoanCollectionAmountss) {
                if ($key1 == $key3 and $key2 == $key4) {
                  if ($MemberLoanInstallmentAmountss > $MemberLoanCollectionAmountss) {
                    $MemberAdvDueListArrays[$key1]['Due/'.$key2]= $MemberLoanInstallmentAmountss - $MemberLoanCollectionAmountss;
                  }
                  elseif ($MemberLoanInstallmentAmountss < $MemberLoanCollectionAmountss) {
                    $MemberAdvDueListArrays[$key1]['Adv/'.$key2] = $MemberLoanCollectionAmountss - $MemberLoanInstallmentAmountss;
                  }
                }
              }
            }
          }

        }

        // dd($MemberAdvDueListArrays);

        foreach ($MemberLoanInstallmentAmountsOnlyPrincipals as $key1 => $MemberLoanInstallmentAmount) {
          foreach ($MemberLoanInstallmentAmount as $key2 => $MemberLoanInstallmentAmountss) {
            foreach ($MemberLoanCollectionAmountsOnlyPrincipals as $key3 => $MemberLoanCollectionAmount) {
              foreach ($MemberLoanCollectionAmount as $key4 => $MemberLoanCollectionAmountss) {
                if ($key1 == $key3 and $key2 == $key4) {
                  if ($MemberLoanInstallmentAmountss > $MemberLoanCollectionAmountss) {
                    $MargedAdvDueListArrays[$key1]['Due/'.$key2] = $MemberLoanInstallmentAmountss - $MemberLoanCollectionAmountss;
                  }
                  elseif ($MemberLoanInstallmentAmountss < $MemberLoanCollectionAmountss) {
                    $MargedAdvDueListArrays[$key1]['Adv/'.$key2] = $MemberLoanCollectionAmountss - $MemberLoanInstallmentAmountss;
                  }
                }
              }
            }
          }

        }

        }

      }           //End of Product Category wise SEARCH.......

    }         //End of samity is selected as all

    //START OPENING LOAN BALANCE ARRAY=====

        $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                          ->join('mfn_loan', 'mfn_loan.id', '=', 
                            'mfn_opening_balance_loan.loanIdFk')
                          ->where('mfn_opening_balance_loan.softDel', 0)
                          ->select('mfn_opening_balance_loan.paidLoanAmountOB',
                            'mfn_opening_balance_loan.principalAmountOB',
                            'mfn_loan.id',
                            'mfn_loan.samityIdFk',
                            'mfn_loan.memberIdFk')
                          ->get();
        //END OPENING LOAN BALANCE ARRAY===== 

    // dd($MemberLists);

    return view('microfin.reports.advanceDueListViews.AdvanceDueListTable', compact('BranchInfos', 'SamityInfos', 'RequestDate', 'RequestSamity', 'MemberLoanInstallmentAmounts', 'MemberLoanCollectionAmounts', 'MemberAdvDueListArrays', 'MemberLists', 'MemberLoanInstallmentAmountsOnlyPrincipals', 'MemberLoanCollectionAmountsOnlyPrincipals', 'MargedAdvDueListArrays', 'RequestSamity', 'loanOpeningBalance'));
  }


}
