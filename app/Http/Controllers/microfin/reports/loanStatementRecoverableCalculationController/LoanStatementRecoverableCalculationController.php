<?php

namespace App\Http\Controllers\microfin\reports\loanStatementRecoverableCalculationController;

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
class LoanStatementRecoverableCalculationController extends Controller
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

    return view('microfin.reports.loanStatementRecoverableCalculationViews.LoanStatementRecoverableCalculationForm', compact('BranchDatas', 'FundingOrganization', 'UniqueLoanYear'));
  }

  public function getSamity(Request $request){
    $SamityInfo = DB::table('mfn_samity')
                    ->select('id', 'name', 'code', 'branchId')
                    ->get();
    return response()->json($SamityInfo);
  }

  public function getReportTableData(Request $request){
    $RequestBranch = $request->searchBranch;
    $RequestSamity = $request->searchSamity;
    $RequestYear = $request->Year;
    $RequestMonth = $request->Month;
    $RequestRoundUp = $request->isRoundUp;
    $RequestFieldOrganization = $request->FundingOrganization;

    $TotalDisbursUpToLastWeek = array();
    $TotalDisbursUpToLastWeekDatas = array();
    $TotalDisbursAtLastWeekDatas = array();
    $TotalFullyPaidUptoLastWeek = array();
    $TotalOverDueLoan = array();
    $RegularCurrentLoan = array();
    $NetWeeklyCollectable = array();
    $NetWeeklyCollectableDatas = array();
    $Gendar = array();

    // Start of the Branch Data
    $BranchDatas = DB::table('gnr_branch')
                   ->where('id', $RequestBranch)
                   ->get();
    // dd($BranchDatas);
    // End of the Branch Data

    // Start of the Samity data
    $SamityDatas = DB::table('mfn_samity')
                   ->where('id', $RequestSamity)
                   ->get();

    // dd($SamityDatas);
    // End of the Samity Data

    // Start of the FIeld Organization Data
    if ($RequestFieldOrganization != 100) {
      $FieldOrganizationDatas = DB::table('mfn_loans_product')
                                ->join('mfn_loan', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                                ->where([['mfn_loans_product.fundingOrganizationId', $RequestFieldOrganization], ['mfn_loan.branchIdFk', $RequestBranch], ['mfn_loan.samityIdFk', $RequestSamity]])
                                ->get();
    }
    else {
      $FieldOrganizationDatas = DB::table('mfn_loans_product')
                                ->join('mfn_loan', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                                ->where([['mfn_loan.branchIdFk', $RequestBranch], ['mfn_loan.samityIdFk', $RequestSamity]])
                                ->get();
    }
    // dd($FieldOrganizationDatas);
    // End of the Field Organization Data

    // START OF THE Weekly date calculation
    $Date = $RequestYear.'-'.$RequestMonth.'-1';
    $DateConv = strtotime($Date);
    $FixedDate = date("Y-m-d", $DateConv);
    $FormatedfixedDate = DateTime::createFromFormat('Y-m-d', $Date);
    $FormatedfixedDay = $FormatedfixedDate->format('D');
    // $AllTheDays[] = cal_days_in_month ( CAL_GREGORIAN , $RequestMonth , $RequestYear );

    $list = array();
    $Week = array();
    $TotalDay = cal_days_in_month(CAL_GREGORIAN, $RequestMonth, $RequestYear);
    $WeekList = array();
    $WeekArray = array();

    for($d=1; $d<=31; $d++)
    {
        $time=mktime(12, 0, 0, $RequestMonth, $d, $RequestYear);
        if (date('m', $time)==$RequestMonth)
            $list[]=date('Y-m-d-D', $time);
    }
    // dd($list);
    $Count = 0;
    foreach ($list as $key => $lists) {
        $Count = $Count + $key;

      if (substr($lists,11) == 'Sat' || substr($lists,11) == 'Thu' ) {
        $Week[] = $lists;
      }
      elseif (substr($lists,11) != 'Fri' and $Count == 0) {
        $Week[] = $lists;
        // $Count = 1;
      }
      elseif (($TotalDay-1) == $key and substr($lists,11) != 'Fri') {
        $Week[] = $lists;
      }

     if ($key == 0 and substr($lists,11) == 'Thu') {
       $Week[] = $lists;
     }

     if (($TotalDay-1) == $key and substr($lists,11) == 'Sat') {
       $Week[] = $lists;
     }
    }
    // dd($Week);
    foreach ($Week as $key => $Weeks) {
      $WeekList[] = substr($Weeks, 0, 10);
    }

    $StartDate = '';
    $EndDate = '';
    foreach ($WeekList as $key => $WeekLists) {
      if ($StartDate == null) {
        $StartDate = $WeekLists;
      }
      elseif ($EndDate == null) {
        $EndDate = $WeekLists;
      }

      if ($StartDate != null and $EndDate != null) {
        $WeekArray[] = $StartDate.' to '.$EndDate;
        $StartDate = '';
        $EndDate = '';
      }
    }
    // dd($WeekList);
    // dd($WeekArray);
    // END OF THE Weekly date calculation
    $StartDate = '';
    $EndDate = '';
    // Start of the Product Wise Data
    $Start = '';
    foreach ($WeekList as $key => $WeekLists) {
      if ($key == 0) {
        $Start = $WeekLists;
      }
      if ($StartDate == null) {
        $StartDate = $WeekLists;
      }
      elseif ($EndDate == null) {
        $EndDate = $WeekLists;
      }

      if ($StartDate != null and $EndDate != null) {
        if ($RequestFieldOrganization != 100) {
          foreach ($FieldOrganizationDatas as $key => $FieldOrganizationData) {
            $ProductID = $FieldOrganizationData->productIdFk;
            $ProductName = $FieldOrganizationData->name;
            $TotalDisbursUpToLastWeek[$StartDate.'/'.$ProductName] = DB::table('mfn_loan')
                                 ->select('mfn_member_information.gender', 'mfn_loan.loanAmount', 'mfn_loan.disbursementDate', 'mfn_loan.memberIdFk')
                                 ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                                 ->where('mfn_loan.branchIdFk', $RequestBranch)
                                 ->where('mfn_loan.samityIdFk', $RequestSamity)
                                 ->where('mfn_loan.disbursementDate', '<', $StartDate)
                                 ->where('mfn_loan.productIdFk', $ProductID)
                                 ->get();

           $TotalDisbursAtLastWeek[$StartDate.'/'.$ProductName] = DB::table('mfn_loan')
                                ->select('mfn_member_information.gender', 'mfn_loan.loanAmount', 'mfn_loan.disbursementDate', 'mfn_loan.memberIdFk')
                                ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                                ->where('mfn_loan.branchIdFk', $RequestBranch)
                                ->where('mfn_loan.samityIdFk', $RequestSamity)
                                ->where('mfn_loan.disbursementDate', '>=', $StartDate)
                                ->where('mfn_loan.disbursementDate', '<=', $EndDate)
                                ->where('mfn_loan.productIdFk', $ProductID)
                                ->get();

            $TotalFullyPaidUptoLastWeek[$StartDate.'/'.$ProductName] = DB::table('mfn_loan')
                                 ->select('mfn_member_information.gender', 'mfn_loan.totalRepayAmount', 'mfn_loan.isLoanCompleted', 'mfn_loan.disbursementDate', 'mfn_loan.loanCompletedDate', 'mfn_loan.memberIdFk')
                                 ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                                 ->where('mfn_loan.branchIdFk', $RequestBranch)
                                 ->where('mfn_loan.samityIdFk', $RequestSamity)
                                 ->where('mfn_loan.isLoanCompleted', '=', 1)
                                 ->where('mfn_loan.loanCompletedDate', '<', $StartDate)
                                 ->where('mfn_loan.productIdFk', $ProductID)
                                 ->get();

            $TotalOverDueLoan[$StartDate.'/'.$ProductName] = DB::table('mfn_loan')
                                 ->select('mfn_member_information.gender', 'mfn_loan_collection.installmentDueAmount', 'mfn_loan.isLoanCompleted', 'mfn_loan.disbursementDate', 'mfn_loan.loanCompletedDate', 'mfn_loan.memberIdFk')
                                 ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                                 ->join('mfn_loan_collection', 'mfn_loan.memberIdFk', '=', 'mfn_loan_collection.memberIdFk')
                                 ->where('mfn_loan.branchIdFk', $RequestBranch)
                                 ->where('mfn_loan.samityIdFk', $RequestSamity)
                                 ->where('mfn_loan.isLoanCompleted', '=', 1)
                                 ->where('mfn_loan.loanCompletedDate', '<', $StartDate)
                                 ->where('mfn_loan.productIdFk', $ProductID)
                                 ->get();

            // $RegularCurrentLoan[$StartDate.'/'.$ProductName] = DB::table('mfn_loan')
            //                      ->select('mfn_member_information.gender', 'mfn_loan.loanAmount', 'mfn_loan_collection.installmentDueAmount', 'mfn_loan.isLoanCompleted', 'mfn_loan.disbursementDate', 'mfn_loan.loanCompletedDate', 'mfn_loan.memberIdFk')
            //                      ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
            //                      ->join('mfn_loan_collection', 'mfn_loan.memberIdFk', '=', 'mfn_loan_collection.memberIdFk')
            //                      ->where('mfn_loan.branchIdFk', $RequestBranch)
            //                      ->where('mfn_loan.samityIdFk', $RequestSamity)
            //                      ->where('mfn_loan.isLoanCompleted', '=', 0)
            //                      ->where('mfn_loan.loanCompletedDate', '<', $StartDate)
            //                      ->where('mfn_loan.productIdFk', $ProductID)
            //                      ->where('mfn_loan_collection.installmentDueAmount', '=', 0)
            //                      ->distinct()
            //                      ->get();

            $RegularCurrentLoan[$StartDate.'/'.$ProductName] = DB::table('mfn_loan')
                                 ->select('mfn_member_information.gender', 'mfn_loan.loanAmount', 'mfn_loan_collection.installmentDueAmount', 'mfn_loan.isLoanCompleted', 'mfn_loan.disbursementDate', 'mfn_loan.loanCompletedDate', 'mfn_loan.memberIdFk', 'mfn_loan.id as LoanID', 'mfn_loan.branchIdFk', 'mfn_loan.samityIdFk')
                                 ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                                 ->join('mfn_loan_collection', 'mfn_loan.memberIdFk', '=', 'mfn_loan_collection.memberIdFk')
                                 ->where('mfn_loan.branchIdFk', $RequestBranch)
                                 ->where('mfn_loan.samityIdFk', $RequestSamity)
                                 ->where('mfn_loan.isLoanCompleted', '=', 0)
                                 ->where('mfn_loan.loanCompletedDate', '<', $StartDate)
                                 ->where('mfn_loan.productIdFk', $ProductID)
                                 ->where('mfn_loan_collection.installmentDueAmount', '=', 0)
                                 ->distinct()
                                 ->get();

            foreach ($RegularCurrentLoan as $key => $RegularCurrentLoans) {
              foreach ($RegularCurrentLoans as $key => $RegularCurrentLoanss) {
                $MemberName = $RegularCurrentLoanss->memberIdFk;
                $NetWeeklyCollectable[$StartDate.'/'.$ProductName] = DB::table('mfn_loan')
                                  ->select('mfn_loan.loanAmount', 'mfn_member_information.gender', 'mfn_loan.isLoanCompleted', 'mfn_loan.disbursementDate', 'mfn_loan.loanCompletedDate', 'mfn_loan.memberIdFk', 'mfn_loan.id as LoanID', 'mfn_loan.branchIdFk', 'mfn_loan.samityIdFk','mfn_loan_schedule.loanIdFk', 'mfn_loan_schedule.scheduleDate', 'mfn_loan_schedule.installmentAmount')
                                  ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                                  ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                                  // ->where([['mfn_loan.branchIdFk', $RequestBranch], ['mfn_loan.samityIdFk', $RequestSamity], ['mfn_loan.productIdFk', $ProductID], ['mfn_loan.memberIdFk', $MemberName], ['mfn_loan_schedule.scheduleDate', '>=', $StartDate], ['mfn_loan_schedule.scheduleDate', '<=', $EndDate]])
                                  ->where('mfn_loan.branchIdFk', $RequestBranch)
                                  ->where('mfn_loan.samityIdFk', $RequestSamity)
                                  ->where('mfn_loan.productIdFk', $ProductID)
                                  // ->where('mfn_loan.memberIdFk', $MemberName)
                                  ->where('mfn_loan_schedule.scheduleDate', '>=', $StartDate)
                                  ->where('mfn_loan_schedule.scheduleDate', '<=', $EndDate)
                                  // ->distinct()
                                  ->get();
                                  // OK

              }
            }
          }
        }
        else {
          foreach ($FieldOrganizationDatas as $key => $FieldOrganizationData) {
            $ProductID = $FieldOrganizationData->productIdFk;
            $ProductName = $FieldOrganizationData->name;
            $TotalDisbursUpToLastWeek[$StartDate.'/'.$ProductName] = DB::table('mfn_loan')
                                 ->select('mfn_member_information.gender', 'mfn_loan.loanAmount', 'mfn_loan.disbursementDate', 'mfn_loan.memberIdFk', 'mfn_loan.branchIdFk', 'mfn_loan.samityIdFk')
                                 ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                                 ->where('mfn_loan.branchIdFk', $RequestBranch)
                                 ->where('mfn_loan.samityIdFk', $RequestSamity)
                                 ->where('mfn_loan.disbursementDate', '<', $StartDate)
                                 ->where('mfn_loan.productIdFk', $ProductID)
                                 ->get();

           $TotalDisbursAtLastWeek[$StartDate.'/'.$ProductName] = DB::table('mfn_loan')
                                ->select('mfn_member_information.gender', 'mfn_loan.loanAmount', 'mfn_loan.disbursementDate', 'mfn_loan.memberIdFk', 'mfn_loan.branchIdFk', 'mfn_loan.samityIdFk')
                                ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                                ->where('mfn_loan.branchIdFk', $RequestBranch)
                                ->where('mfn_loan.samityIdFk', $RequestSamity)
                                ->where('mfn_loan.disbursementDate', '>=', $StartDate)
                                ->where('mfn_loan.disbursementDate', '<=', $EndDate)
                                ->where('mfn_loan.productIdFk', $ProductID)
                                ->get();

          $TotalFullyPaidUptoLastWeek[$StartDate.'/'.$ProductName] = DB::table('mfn_loan')
                               ->select('mfn_member_information.gender', 'mfn_loan.totalRepayAmount', 'mfn_loan.isLoanCompleted', 'mfn_loan.disbursementDate', 'mfn_loan.loanCompletedDate', 'mfn_loan.memberIdFk')
                               ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                               ->where('mfn_loan.branchIdFk', $RequestBranch)
                               ->where('mfn_loan.samityIdFk', $RequestSamity)
                               ->where('mfn_loan.isLoanCompleted', '=', 1)
                               ->where('mfn_loan.disbursementDate', '>=', $StartDate)
                               ->where('mfn_loan.disbursementDate', '<=', $EndDate)
                               ->where('mfn_loan.productIdFk', $ProductID)
                               ->get();

         $TotalOverDueLoan[$StartDate.'/'.$ProductName] = DB::table('mfn_loan')
                              ->select('mfn_member_information.gender', 'mfn_loan_collection.installmentDueAmount', 'mfn_loan.isLoanCompleted', 'mfn_loan.disbursementDate', 'mfn_loan.loanCompletedDate', 'mfn_loan.memberIdFk')
                              ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                              ->join('mfn_loan_collection', 'mfn_loan.memberIdFk', '=', 'mfn_loan_collection.memberIdFk')
                              ->where('mfn_loan.branchIdFk', $RequestBranch)
                              ->where('mfn_loan.samityIdFk', $RequestSamity)
                              ->where('mfn_loan.isLoanCompleted', '=', 1)
                              ->where('mfn_loan.loanCompletedDate', '<', $StartDate)
                              ->where('mfn_loan.productIdFk', $ProductID)
                              ->get();

        $RegularCurrentLoan[$StartDate.'/'.$ProductName] = DB::table('mfn_loan')
                             ->select('mfn_member_information.gender', 'mfn_loan.loanAmount', 'mfn_loan_collection.installmentDueAmount', 'mfn_loan.isLoanCompleted', 'mfn_loan.disbursementDate', 'mfn_loan.loanCompletedDate', 'mfn_loan.memberIdFk', 'mfn_loan.id as LoanID', 'mfn_loan.branchIdFk', 'mfn_loan.samityIdFk')
                             ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                             ->join('mfn_loan_collection', 'mfn_loan.memberIdFk', '=', 'mfn_loan_collection.memberIdFk')
                             ->where('mfn_loan.branchIdFk', $RequestBranch)
                             ->where('mfn_loan.samityIdFk', $RequestSamity)
                             ->where('mfn_loan.isLoanCompleted', '=', 0)
                             ->where('mfn_loan.loanCompletedDate', '<', $StartDate)
                             ->where('mfn_loan.productIdFk', $ProductID)
                             ->where('mfn_loan_collection.installmentDueAmount', '=', 0)
                             ->distinct()
                             ->get();

        foreach ($RegularCurrentLoan as $key => $RegularCurrentLoans) {
          foreach ($RegularCurrentLoans as $key => $RegularCurrentLoanss) {
            $MemberName = $RegularCurrentLoanss->memberIdFk;
            $NetWeeklyCollectable[$StartDate.'/'.$ProductName] = DB::table('mfn_loan')
                              ->select('mfn_loan.loanAmount', 'mfn_member_information.gender', 'mfn_loan.isLoanCompleted', 'mfn_loan.disbursementDate', 'mfn_loan.loanCompletedDate', 'mfn_loan.memberIdFk', 'mfn_loan.id as LoanID', 'mfn_loan.branchIdFk', 'mfn_loan.samityIdFk','mfn_loan_schedule.loanIdFk', 'mfn_loan_schedule.scheduleDate', 'mfn_loan_schedule.installmentAmount')
                              ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                              ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                              // ->where([['mfn_loan.branchIdFk', $RequestBranch], ['mfn_loan.samityIdFk', $RequestSamity], ['mfn_loan.productIdFk', $ProductID], ['mfn_loan.memberIdFk', $MemberName], ['mfn_loan_schedule.scheduleDate', '>=', $StartDate], ['mfn_loan_schedule.scheduleDate', '<=', $EndDate]])
                              ->where('mfn_loan.branchIdFk', $RequestBranch)
                              ->where('mfn_loan.samityIdFk', $RequestSamity)
                              ->where('mfn_loan.productIdFk', $ProductID)
                              // ->where('mfn_loan.memberIdFk', $MemberName)
                              ->where('mfn_loan_schedule.scheduleDate', '>=', $StartDate)
                              ->where('mfn_loan_schedule.scheduleDate', '<=', $EndDate)
                              // ->distinct()
                              ->get();
                              // OK


          }
        }


          }
        }

        $StartDate = '';
        $EndDate = '';
      }
    }

    // dd($FieldOrganizationDatas);
    // dd($Gendar);
    // dd($NetWeeklyCollectable);
    // dd($TotalDisbursUpToLastWeek);
    // dd($RegularCurrentLoan);
    // dd($FieldOrganizationDatas);
    // End of the Product Wise Data


    return view('microfin.reports.loanStatementRecoverableCalculationViews.LoanStatementRecoverableCalculationView', compact('BranchDatas', 'SamityDatas', 'RequestYear', 'RequestMonth', 'FieldOrganizationDatas', 'WeekArray', 'TotalDisbursUpToLastWeek', 'TotalDisbursAtLastWeek', 'TotalFullyPaidUptoLastWeek', 'TotalOverDueLoan', 'RegularCurrentLoan', 'NetWeeklyCollectable', 'RequestRoundUp'));
  }

}
