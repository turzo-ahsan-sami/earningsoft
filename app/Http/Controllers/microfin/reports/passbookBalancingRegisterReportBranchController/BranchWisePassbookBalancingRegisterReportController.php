<?php

namespace App\Http\Controllers\microfin\reports\passbookBalancingRegisterReportBranchController;

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
class BranchWisePassbookBalancingRegisterReportController extends Controller
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

    return view('microfin.reports.passbookBalancingRegisterReportBranch.passbookBalancingRegisterReportBranchForm', compact('BranchDatas', 'ProdCats', 'UniqueLoanYear'));

  }

  // Methods to calculate............
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

  public function CreditOfficerQuery($branch){
    $CreditOfficer = array();
    $Officer = array();
    $FieldOfficer = array();

    // $CreditOfficer = DB::table('hr_emp_org_info')
    //           ->select('hr_emp_general_info.emp_name_english', 'hr_emp_general_info.emp_id', 'hr_emp_general_info.id as EmployeeID', 'gnr_branch.id', 'hr_settings_position.name as PositionName')
    //           ->join('gnr_branch', 'hr_emp_org_info.branch_id_fk', '=', 'gnr_branch.id')
    //           ->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', '=', 'hr_settings_position.id')
    //           ->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
    //           ->where([['gnr_branch.id', $branch], ['hr_settings_position.name', '=', 'Credit Officer']])
    //           ->get();

    $CreditOfficer = DB::table('hr_emp_general_info')
                 ->select('hr_emp_general_info.emp_name_english', 'hr_emp_general_info.emp_id', 'hr_emp_general_info.id as EmployeeID')
                 ->join('mfn_samity', 'hr_emp_general_info.id', '=', 'mfn_samity.fieldOfficerId')
                 ->where('mfn_samity.branchId', $branch)
                 ->groupBy('hr_emp_general_info.emp_id')
                 ->get();

                 // dd($CreditOfficer);

    return $CreditOfficer;
  }

  public function SamityQuery($branch, $CreditOfficerInfos){
    $SamityInfo = array();

    // dd($CreditOfficerInfos);

    foreach ($CreditOfficerInfos as $key => $CreditOfficerInfo) {
      $SamityInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_samity')
                ->where([['branchId', $branch], ['fieldOfficerId', $CreditOfficerInfo->EmployeeID]])
                ->count('id');
    }

    return $SamityInfo;
  }

  public function MemberQuery($branch, $CreditOfficerInfos, $requestedCategory,$fromDate, $toDate){
    $SamityInfo = array();
    $MemberInfo = array();

    foreach ($CreditOfficerInfos as $key => $CreditOfficerInfo) {
      $SamityInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_samity')
                ->where([['branchId', $branch], ['fieldOfficerId', $CreditOfficerInfo->EmployeeID]])
                ->get();
    }

    if ($requestedCategory == 'All') {
      foreach ($SamityInfo as $key1 => $SamityInfo1) {
        foreach ($SamityInfo1 as $key2 => $SamityInfo2) {
          $MemberInfo[$key1][$SamityInfo2->id] = DB::table('mfn_member_information')
                ->where([['samityId', $SamityInfo2->id], ['admissionDate', '<=', $toDate]])
                ->get();
        }
      }
    }
    else {
      foreach ($SamityInfo as $key1 => $SamityInfo1) {
        foreach ($SamityInfo1 as $key2 => $SamityInfo2) {
          $MemberInfo[$key1][$SamityInfo2->id] = DB::table('mfn_member_information')
                ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
                ->where([['mfn_member_information.samityId', $SamityInfo2->id], ['mfn_loans_product.productCategoryId', $requestedCategory], ['mfn_member_information.admissionDate', '<=', $toDate]])
                ->get();
        }
      }
    }

    if ($requestedCategory == 'All') {
      foreach ($MemberInfo as $key1 => $MemberInfo1) {
        foreach ($MemberInfo1 as $key2 => $MemberInfo2) {
          foreach ($MemberInfo2 as $key3 => $MemberInfo3) {
            $MemberInfo[$key1] = DB::table('mfn_member_information')
                    ->join('mfn_samity', 'mfn_member_information.samityId', '=', 'mfn_samity.id')
                    ->where([['mfn_samity.fieldOfficerId', $key1], ['mfn_member_information.admissionDate', '>=', $fromDate], ['mfn_member_information.admissionDate','<=', $toDate]])
                    ->count('mfn_member_information.id');

                    // dd($MemberInfo);
          }
        }
      }
    }
    else {
      foreach ($MemberInfo as $key1 => $MemberInfo1) {
        foreach ($MemberInfo1 as $key2 => $MemberInfo2) {
          foreach ($MemberInfo2 as $key3 => $MemberInfo3) {
            $MemberInfo[$key1] = DB::table('mfn_member_information')
                    ->join('mfn_samity', 'mfn_member_information.samityId', '=', 'mfn_samity.id')
                    ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', 'mfn_loans_product.id')
                    ->where([['mfn_samity.fieldOfficerId', $key1], ['mfn_loans_product.productCategoryId', $requestedCategory], ['mfn_member_information.admissionDate', '>=', $fromDate], ['mfn_member_information.admissionDate','<=', $toDate]])
                    ->count('mfn_member_information.id');
          }
        }
      }
    }

    // if ($requestedCategory == 'All') {
    //   foreach ($SamityInfo as $key1 => $SamityInfo1) {
    //     foreach ($SamityInfo1 as $key => $SamityInfo2) {
    //       $MemberInfo[$key1] = DB::table('mfn_member_information')
    //               ->join('mfn_samity', 'mfn_member_information.samityId', '=', 'mfn_samity.id')
    //               ->where([['mfn_member_information.samityId', $SamityInfo2->id], ['mfn_samity.fieldOfficerId', $key1], ['mfn_member_information.admissionDate','<=', $toDate]])
    //               ->count('mfn_member_information.id');
    //     }
    //   }
    // }
    // else {
    //   foreach ($SamityInfo as $key1 => $SamityInfo1) {
    //     foreach ($SamityInfo1 as $key => $SamityInfo2) {
    //       $MemberInfo[$key1] = DB::table('mfn_member_information')
    //               ->join('mfn_samity', 'mfn_member_information.samityId', '=', 'mfn_samity.id')
    //               ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', 'mfn_loans_product.id')
    //               ->where([['mfn_member_information.samityId', $SamityInfo2->id], ['mfn_samity.fieldOfficerId', $key1], ['mfn_loans_product.productCategoryId', $requestedCategory], ['mfn_member_information.admissionDate','<=', $toDate]])
    //               ->count('mfn_member_information.id');
    //     }
    //   }
    // }

    // dd($MemberInfo);

    return $MemberInfo;
  }

  public function LoanBorrowerQuery($branch, $CreditOfficerInfos, $requestedCategory,$fromDate, $toDate){
    $SamityInfo = array();
    $MemberInfo = array();
    $LoanBorrowerInfo = array();

    foreach ($CreditOfficerInfos as $key => $CreditOfficerInfo) {
      $SamityInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_samity')
                ->where([['branchId', $branch], ['fieldOfficerId', $CreditOfficerInfo->EmployeeID]])
                ->get();
    }

    if ($requestedCategory == 'All') {
      foreach ($CreditOfficerInfos as $key => $CreditOfficerInfo) {
        $LoanBorrowerInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_loan')
                ->join('mfn_samity', 'mfn_loan.samityIdFk', '=', 'mfn_samity.id')
                ->where([['mfn_samity.fieldOfficerId', $CreditOfficerInfo->EmployeeID], ['mfn_loan.disbursementDate', '>=', $fromDate], ['mfn_loan.disbursementDate','<=', $toDate]])
                ->count('mfn_loan.id');
      }
    }
    else {
      foreach ($CreditOfficerInfos as $key => $CreditOfficerInfo) {
        $LoanBorrowerInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_loan')
                ->join('mfn_samity', 'mfn_loan.samityIdFk', '=', 'mfn_samity.id')
                ->join('mfn_loans_product', 'mfn_loan.primaryProductIdFk', 'mfn_loans_product.id')
                ->where([['mfn_samity.fieldOfficerId', $CreditOfficerInfo->EmployeeID], ['mfn_loans_product.productCategoryId', $requestedCategory], ['mfn_loan.disbursementDate', '>=', $fromDate], ['mfn_loan.disbursementDate','<=', $toDate]])
                ->count('mfn_loan.id');
      }
    }

    // dd($LoanBorrowerInfo);

    return $LoanBorrowerInfo;
  }

  public function LoanDisbursmentQuery($branch, $CreditOfficerInfos, $requestedCategory,$fromDate, $toDate){
    $SamityInfo = array();
    $MemberInfo = array();
    $LoanDisbursmentInfo = array();

    foreach ($CreditOfficerInfos as $key => $CreditOfficerInfo) {
      $SamityInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_samity')
                ->where([['branchId', $branch], ['fieldOfficerId', $CreditOfficerInfo->EmployeeID]])
                ->get();
    }

    if ($requestedCategory == 'All') {
      foreach ($CreditOfficerInfos as $key => $CreditOfficerInfo) {
        $LoanDisbursmentInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_loan')
                // ->select('mfn_loan.totalRepayAmount', 'mfn_loan.memberIdFk', 'mfn_loan.disbursementDate')
                ->join('mfn_samity', 'mfn_loan.samityIdFk', '=', 'mfn_samity.id')
                ->where([['mfn_samity.fieldOfficerId', $CreditOfficerInfo->EmployeeID], ['mfn_loan.disbursementDate', '>=', $fromDate], ['mfn_loan.disbursementDate', '<=', $toDate]])
                ->sum('mfn_loan.totalRepayAmount');
                // ->get();
      }
    }
    else {
      foreach ($CreditOfficerInfos as $key => $CreditOfficerInfo) {
        $LoanDisbursmentInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_loan')
                ->join('mfn_samity', 'mfn_loan.samityIdFk', '=', 'mfn_samity.id')
                ->join('mfn_loans_product', 'mfn_loan.primaryProductIdFk', 'mfn_loans_product.id')
                ->where([['mfn_samity.fieldOfficerId', $CreditOfficerInfo->EmployeeID], ['mfn_loans_product.productCategoryId', $requestedCategory], ['mfn_loan.disbursementDate', '>=', $fromDate], ['mfn_loan.disbursementDate', '<=', $toDate]])
                ->sum('mfn_loan.totalRepayAmount');
      }
    }

    // dd($LoanDisbursmentInfo);

    return $LoanDisbursmentInfo;

  }

  public function OutstandingQuery($branch, $CreditOfficerInfos, $requestedCategory, $fromDate, $toDate){
    $SamityInfo = array();
    $MemberInfo = array();
    $LoanDisbursmentInfo = array();
    $LoanCollectionInfo = array();
    $OutstandingInfo = array();

    foreach ($CreditOfficerInfos as $key => $CreditOfficerInfo) {
      $SamityInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_samity')
                ->where([['branchId', $branch], ['fieldOfficerId', $CreditOfficerInfo->EmployeeID]])
                ->get();
    }

    // dd($MemberInfo);

    if ($requestedCategory == 'All') {
      foreach ($CreditOfficerInfos as $key => $CreditOfficerInfo) {
        $LoanDisbursmentInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_loan')
                // ->select('mfn_loan.totalRepayAmount', 'mfn_loan.memberIdFk', 'mfn_loan.disbursementDate')
                ->join('mfn_samity', 'mfn_loan.samityIdFk', '=', 'mfn_samity.id')
                ->where([['mfn_samity.fieldOfficerId', $CreditOfficerInfo->EmployeeID], ['mfn_loan.disbursementDate', '<=', $toDate]])
                ->sum('mfn_loan.totalRepayAmount');
                // ->get();

        $LoanCollectionInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_loan_collection')
                // ->select('mfn_loan_collection.amount', 'mfn_loan_collection.memberIdFk')
                ->join('mfn_samity', 'mfn_loan_collection.samityIdFk', '=', 'mfn_samity.id')
                ->where([['mfn_samity.fieldOfficerId', $CreditOfficerInfo->EmployeeID], ['mfn_loan_collection.collectionDate', '<=', $toDate]])
                ->sum('mfn_loan_collection.amount');
                // ->get();
      }
    }
    else {
      foreach ($CreditOfficerInfos as $key => $CreditOfficerInfo) {
        $LoanDisbursmentInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_loan')
                ->join('mfn_samity', 'mfn_loan.samityIdFk', '=', 'mfn_samity.id')
                ->join('mfn_loans_product', 'mfn_loan.primaryProductIdFk', 'mfn_loans_product.id')
                ->where([['mfn_samity.fieldOfficerId', $CreditOfficerInfo->EmployeeID], ['mfn_loans_product.productCategoryId', $requestedCategory], ['mfn_loan.disbursementDate', '<=', $toDate]])
                ->sum('mfn_loan.totalRepayAmount');

        $LoanCollectionInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_loan_collection')
                ->join('mfn_samity', 'mfn_loan_collection.samityIdFk', '=', 'mfn_samity.id')
                ->join('mfn_loans_product', 'mfn_loan_collection.primaryProductIdFk', 'mfn_loans_product.id')
                ->where([['mfn_samity.fieldOfficerId', $CreditOfficerInfo->EmployeeID], ['mfn_loans_product.productCategoryId', $requestedCategory], ['mfn_loan_collection.collectionDate', '<=', $toDate]])
                ->sum('mfn_loan_collection.amount');
      }
    }

    // if ($requestedCategory == 'All') {
    //   foreach ($MemberInfo as $key1 => $MemberInfo1) {
    //     foreach ($MemberInfo1 as $key2 => $MemberInfo2) {
    //       foreach ($MemberInfo2 as $key3 => $MemberInfo3) {
    //         $LoanDisbursmentInfo[$key1] = DB::table('mfn_loan')
    //                 // ->select('mfn_loan.totalRepayAmount', 'mfn_loan.memberIdFk', 'mfn_loan.disbursementDate')
    //                 ->join('mfn_samity', 'mfn_loan.samityIdFk', '=', 'mfn_samity.id')
    //                 ->where([['mfn_samity.fieldOfficerId', $key1], ['mfn_loan.disbursementDate', '<=', $toDate]])
    //                 ->sum('mfn_loan.totalRepayAmount');
    //                 // ->get();
    //
    //         $LoanCollectionInfo[$key1] = DB::table('mfn_loan_collection')
    //                 // ->select('mfn_loan_collection.amount', 'mfn_loan_collection.memberIdFk')
    //                 ->join('mfn_samity', 'mfn_loan_collection.samityIdFk', '=', 'mfn_samity.id')
    //                 ->where([['mfn_samity.fieldOfficerId', $key1], ['mfn_loan_collection.collectionDate', '<=', $toDate]])
    //                 ->sum('mfn_loan_collection.amount');
    //                 // ->get();
    //       }
    //     }
    //   }
    // }
    // else {
    //   // dd($requestedCategory);
    //   foreach ($MemberInfo as $key1 => $MemberInfo1) {
    //     foreach ($MemberInfo1 as $key2 => $MemberInfo2) {
    //       foreach ($MemberInfo2 as $key3 => $MemberInfo3) {
    //         $LoanDisbursmentInfo[$key1] = DB::table('mfn_loan')
    //                 ->join('mfn_samity', 'mfn_loan.samityIdFk', '=', 'mfn_samity.id')
    //                 ->join('mfn_loans_product', 'mfn_loan.primaryProductIdFk', 'mfn_loans_product.id')
    //                 ->where([['mfn_samity.fieldOfficerId', $key1], ['mfn_loans_product.productCategoryId', $requestedCategory], ['mfn_loan.disbursementDate', '<=', $toDate]])
    //                 ->sum('mfn_loan.totalRepayAmount');
    //
    //         $LoanCollectionInfo[$key1] = DB::table('mfn_loan_collection')
    //                 ->join('mfn_samity', 'mfn_loan_collection.samityIdFk', '=', 'mfn_samity.id')
    //                 ->join('mfn_loans_product', 'mfn_loan_collection.primaryProductIdFk', 'mfn_loans_product.id')
    //                 ->where([['mfn_samity.fieldOfficerId', $key1], ['mfn_loans_product.productCategoryId', $requestedCategory], ['mfn_loan_collection.collectionDate', '<=', $toDate]])
    //                 ->sum('mfn_loan_collection.amount');
    //       }
    //     }
    //   }
    // }

    // dd($LoanCollectionInfo);

    foreach ($LoanDisbursmentInfo as $key1 => $LoanDisbursmentInfo1) {
      foreach ($LoanCollectionInfo as $key2 => $LoanCollectionInfo1) {
        if ($key1 == $key2 and $LoanDisbursmentInfo1 >= $LoanCollectionInfo1) {
          $OutstandingInfo[$key1] = $LoanDisbursmentInfo1 - $LoanCollectionInfo1;
        }
        elseif($key1 == $key2 and $LoanDisbursmentInfo1 < $LoanCollectionInfo1) {
          $OutstandingInfo[$key1] = 0.0;
        }
      }
    }

    // dd($LoanDisbursmentInfo);

    return $OutstandingInfo;
  }

  public function DueQuery($branch, $CreditOfficerInfos, $requestedCategory, $fromDate, $toDate){
    $SamityInfo = array();
    $MemberInfo = array();
    $LoanScheduleInfo = array();
    $LoanCollectionInfo = array();
    $DueInfo = array();

    foreach ($CreditOfficerInfos as $key => $CreditOfficerInfo) {
      $SamityInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_samity')
                ->where([['branchId', $branch], ['fieldOfficerId', $CreditOfficerInfo->EmployeeID]])
                ->get();
    }

    if ($requestedCategory == 'All') {
      foreach ($CreditOfficerInfos as $key => $CreditOfficerInfo) {
        $LoanScheduleInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_loan')
                ->join('mfn_samity', 'mfn_loan.samityIdFk', '=', 'mfn_samity.id')
                ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                ->where([['mfn_samity.fieldOfficerId', $CreditOfficerInfo->EmployeeID], ['mfn_loan_schedule.scheduleDate', '<=', $toDate]])
                ->sum('mfn_loan_schedule.installmentAmount');

        $LoanCollectionInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_loan_collection')
                ->join('mfn_samity', 'mfn_loan_collection.samityIdFk', '=', 'mfn_samity.id')
                ->where([['mfn_samity.fieldOfficerId', $CreditOfficerInfo->EmployeeID], ['mfn_loan_collection.collectionDate', '<=', $toDate]])
                ->sum('mfn_loan_collection.amount');
      }
    }
    else {
      // dd($requestedCategory);
      foreach ($CreditOfficerInfos as $key => $CreditOfficerInfo) {
        $LoanScheduleInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_loan')
                ->join('mfn_samity', 'mfn_loan.samityIdFk', '=', 'mfn_samity.id')
                ->join('mfn_loans_product', 'mfn_loan.primaryProductIdFk', 'mfn_loans_product.id')
                ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                ->where([['mfn_samity.fieldOfficerId', $CreditOfficerInfo->EmployeeID], ['mfn_loans_product.productCategoryId', $requestedCategory], ['mfn_loan_schedule.scheduleDate', '<=', $toDate]])
                ->sum('mfn_loan_schedule.installmentAmount');

        $LoanCollectionInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_loan_collection')
                ->join('mfn_samity', 'mfn_loan_collection.samityIdFk', '=', 'mfn_samity.id')
                ->join('mfn_loans_product', 'mfn_loan_collection.primaryProductIdFk', 'mfn_loans_product.id')
                ->where([['mfn_samity.fieldOfficerId', $CreditOfficerInfo->EmployeeID], ['mfn_loans_product.productCategoryId', $requestedCategory], ['mfn_loan_collection.collectionDate', '<=', $toDate]])
                ->sum('mfn_loan_collection.amount');
      }
    }

    // dd($LoanScheduleInfo);

    foreach ($LoanScheduleInfo as $key1 => $LoanScheduleInfo1) {
      foreach ($LoanCollectionInfo as $key2 => $LoanCollectionInfo1) {
        if ($key1 == $key2 and $LoanScheduleInfo1 >= $LoanCollectionInfo1) {
          $DueInfo[$key1] = $LoanScheduleInfo1 - $LoanCollectionInfo1;
        }
        elseif($key1 == $key2 and $LoanScheduleInfo1 < $LoanCollectionInfo1) {
          $DueInfo[$key1] = 0.0;
        }
      }
    }

    // dd($DueInfo);

    return $DueInfo;
  }

  public function SavingsQuery($branch, $CreditOfficerInfos, $requestedCategory, $fromDate, $toDate, $SavingsProduct){
    $SamityInfo = array();
    $MemberInfo = array();
    $SavingsInfo = array();

    // dd($CreditOfficerInfos);

    foreach ($CreditOfficerInfos as $key => $CreditOfficerInfo) {
      $SamityInfo[$CreditOfficerInfo->EmployeeID] = DB::table('mfn_samity')
                ->where([['branchId', $branch], ['fieldOfficerId', $CreditOfficerInfo->EmployeeID]])
                ->get();
    }

    if ($requestedCategory == 'All') {
      foreach ($SamityInfo as $key1 => $SamityInfo1) {
        foreach ($SamityInfo1 as $key2 => $SamityInfo2) {
          $MemberInfo[$key1][$SamityInfo2->id] = DB::table('mfn_member_information')
                ->where([['samityId', $SamityInfo2->id], ['admissionDate', '<=', $toDate]])
                ->get();
        }
      }
    }
    else {
      foreach ($SamityInfo as $key1 => $SamityInfo1) {
        foreach ($SamityInfo1 as $key2 => $SamityInfo2) {
          $MemberInfo[$key1][$SamityInfo2->id] = DB::table('mfn_member_information')
                ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
                ->where([['mfn_member_information.samityId', $SamityInfo2->id], ['mfn_loans_product.productCategoryId', $requestedCategory], ['mfn_member_information.admissionDate', '<=', $toDate]])
                ->get();
        }
      }
    }

    if ($requestedCategory == 'All') {
      foreach ($CreditOfficerInfos as $key => $CreditOfficerInfo) {
        foreach ($SavingsProduct as $key => $SavingsProduct1) {
          $SavingsInfo[$CreditOfficerInfo->EmployeeID][$SavingsProduct1->id] = DB::table('mfn_savings_deposit')
              ->join('mfn_samity', 'mfn_savings_deposit.samityIdFk', '=', 'mfn_samity.id')
              ->where([['mfn_samity.fieldOfficerId', $CreditOfficerInfo->EmployeeID], ['mfn_savings_deposit.productIdFk', $SavingsProduct1->id], ['mfn_savings_deposit.depositDate', '>=', $fromDate], ['mfn_savings_deposit.depositDate', '<=', $toDate]])
              ->sum('mfn_savings_deposit.amount');
        }
      }

    }
    else {
      foreach ($CreditOfficerInfos as $key => $CreditOfficerInfo) {
        foreach ($SavingsProduct as $key => $SavingsProduct1) {
          $SavingsInfo[$CreditOfficerInfo->EmployeeID][$SavingsProduct1->id] = DB::table('mfn_savings_deposit')
              ->join('mfn_samity', 'mfn_savings_deposit.samityIdFk', '=', 'mfn_samity.id')
              ->join('mfn_loans_product', 'mfn_savings_deposit.primaryProductIdFk', '=', 'mfn_loans_product.id')
              ->where([['mfn_samity.fieldOfficerId', $CreditOfficerInfo->EmployeeID], ['mfn_loans_product.productCategoryId', $requestedCategory], ['mfn_savings_deposit.productIdFk', $SavingsProduct1->id], ['mfn_savings_deposit.depositDate', '>=', $fromDate], ['mfn_savings_deposit.depositDate', '<=', $toDate]])
              ->sum('mfn_savings_deposit.amount');
        }
      }
    }

    // dd($SavingsInfo);

    return $SavingsInfo;
  }

  public function ManualOutstandingsQuery($requestedBranch, $CreditOfficerInfos, $requestedCategory, $fromDate, $toDate, $SavingsProduct){
    $ManualOutstandings = array();

    if ($requestedCategory == 'All') {
      foreach ($CreditOfficerInfos as $key => $CreditOfficerInfos1) {
        $ManualOutstandings[$CreditOfficerInfos1->EmployeeID] = DB::table('manual_pass_book_balance')
                ->join('mfn_samity', 'manual_pass_book_balance.samityIdFk', '=', 'mfn_samity.id')
                ->where([['manual_pass_book_balance.branchIdFk', $requestedBranch], ['mfn_samity.fieldOfficerId', $CreditOfficerInfos1->EmployeeID], ['manual_pass_book_balance.date', $toDate]])
                ->sum('manual_pass_book_balance.outstanding');
      }
    }
    else {
      foreach ($CreditOfficerInfos as $key => $CreditOfficerInfos1) {
        $ManualOutstandings[$CreditOfficerInfos1->id] = DB::table('manual_pass_book_balance')
                ->join('mfn_samity', 'manual_pass_book_balance.samityIdFk', '=', 'mfn_samity.id')
                ->join('mfn_loans_product', 'manual_pass_book_balance.productIdFk', '=', 'mfn_loans_product.id')
                ->where([['manual_pass_book_balance.branchIdFk', $requestedBranch], ['mfn_loans_product.productCategoryId', $requestedCategory], ['mfn_samity.fieldOfficerId', $CreditOfficerInfos1->EmployeeID], ['manual_pass_book_balance.date', $toDate]])
                ->sum('manual_pass_book_balance.outstanding');
      }
    }

    // dd($ManualOutstandings);

    return $ManualOutstandings;
  }

  public function ManualDuQuery($requestedBranch, $CreditOfficerInfos, $requestedCategory, $fromDate, $toDate, $SavingsProduct){
    $ManualDue = array();

    if ($requestedCategory == 'All') {
      foreach ($CreditOfficerInfos as $key => $CreditOfficerInfos1) {
        $ManualDue[$CreditOfficerInfos1->EmployeeID] = DB::table('manual_pass_book_balance')
                ->join('mfn_samity', 'manual_pass_book_balance.samityIdFk', '=', 'mfn_samity.id')
                ->where([['manual_pass_book_balance.branchIdFk', $requestedBranch], ['mfn_samity.fieldOfficerId', $CreditOfficerInfos1->EmployeeID], ['manual_pass_book_balance.date', $toDate]])
                ->sum('manual_pass_book_balance.due');
      }
    }
    else {
      foreach ($CreditOfficerInfos as $key => $CreditOfficerInfos1) {
        $ManualDue[$CreditOfficerInfos1->id] = DB::table('manual_pass_book_balance')
                ->join('mfn_samity', 'manual_pass_book_balance.samityIdFk', '=', 'mfn_samity.id')
                ->join('mfn_loans_product', 'manual_pass_book_balance.productIdFk', '=', 'mfn_loans_product.id')
                ->where([['manual_pass_book_balance.branchIdFk', $requestedBranch], ['mfn_loans_product.productCategoryId', $requestedCategory], ['mfn_samity.fieldOfficerId', $CreditOfficerInfos1->EmployeeID], ['manual_pass_book_balance.date', $toDate]])
                ->sum('manual_pass_book_balance.due');
      }
    }

    // dd($ManualDue);

    return $ManualDue;
  }

  public function ManualSavingsQuery($requestedBranch, $CreditOfficerInfos, $requestedCategory, $fromDate, $toDate, $SavingsProduct){
    $ManualSavings = array();

    if ($requestedCategory == 'All') {
      foreach ($CreditOfficerInfos as $key => $CreditOfficerInfos1) {
        foreach ($SavingsProduct as $key => $SavingsProduct1) {
          $ManualSavings[$CreditOfficerInfos1->EmployeeID][$SavingsProduct1->id] = DB::table('manual_pass_book_balance')
                  ->join('mfn_samity', 'manual_pass_book_balance.samityIdFk', '=', 'mfn_samity.id')
                  ->join('mfn_savings_account', 'manual_pass_book_balance.savingsCode', '=', 'mfn_savings_account.savingsCode')
                  ->where([['manual_pass_book_balance.branchIdFk', $requestedBranch], ['mfn_savings_account.savingsProductIdFk', $SavingsProduct1->id], ['mfn_samity.fieldOfficerId', $CreditOfficerInfos1->EmployeeID], ['manual_pass_book_balance.date', $toDate]])
                  ->sum('manual_pass_book_balance.savingsBalance');
        }
      }
    }
    else {
      foreach ($CreditOfficerInfos as $key => $CreditOfficerInfos1) {
        foreach ($SavingsProduct as $key => $SavingsProduct1) {
          $ManualSavings[$CreditOfficerInfos1->id][$SavingsProduct1->id] = DB::table('manual_pass_book_balance')
                  ->join('mfn_samity', 'manual_pass_book_balance.samityIdFk', '=', 'mfn_samity.id')
                  ->join('mfn_loans_product', 'manual_pass_book_balance.productIdFk', '=', 'mfn_loans_product.id')
                  ->join('mfn_savings_account', 'manual_pass_book_balance.savingsCode', '=', 'mfn_savings_account.savingsCode')
                  ->where([['manual_pass_book_balance.branchIdFk', $requestedBranch], ['mfn_savings_account.savingsProductIdFk', $SavingsProduct1->id], ['mfn_loans_product.productCategoryId', $requestedCategory], ['mfn_samity.fieldOfficerId', $CreditOfficerInfos1->EmployeeID], ['manual_pass_book_balance.date', $toDate]])
                  ->sum('manual_pass_book_balance.savingsBalance');
        }
      }
    }

    // dd($ManualSavings);

    return $ManualSavings;
  }

  // Query and methods to call (FOR TABLE 2) ...................................................
  public function CategorynameQuery($requestedBranch, $requestedCategory, $fromDate, $toDate){
    $MemberInfo = array();
    $CategoryInfo = array();

    if ($requestedCategory == 'All') {
      $CategoryInfo = DB::table('mfn_member_information')
              ->select('mfn_loans_product_category.id', 'mfn_loans_product_category.name')
              ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
              ->join('mfn_loans_product_category', 'mfn_loans_product.productCategoryId', '=', 'mfn_loans_product_category.id')
              ->where([['branchId', $requestedBranch], ['mfn_member_information.admissionDate', '>=', $fromDate], ['mfn_member_information.admissionDate', '<=', $toDate]])
              ->groupBy('mfn_loans_product_category.id')
              ->get();
    }
    else {
      // $CategoryInfo = DB::table('mfn_loans_product_category')
      //         ->where('id', $requestedCategory)
      //         ->get();

      $CategoryInfo = DB::table('mfn_member_information')
              ->select('mfn_loans_product_category.id', 'mfn_loans_product_category.name')
              ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
              ->join('mfn_loans_product_category', 'mfn_loans_product.productCategoryId', '=', 'mfn_loans_product_category.id')
              ->where([['branchId', $requestedBranch], ['mfn_loans_product.productCategoryId', $requestedCategory], ['mfn_member_information.admissionDate', '>=', $fromDate], ['mfn_member_information.admissionDate', '<=', $toDate]])
              ->groupBy('mfn_loans_product_category.id')
              ->get();
    }

            // dd($CategoryInfo);

    return $CategoryInfo;
  }

  public function CategoryWiseOutstandingQuery($requestedBranch, $requestedCategory, $fromDate, $toDate, $CategoryName){
    $CategoryWiseOutstanding = array();
    $LoanDisbursmentInfo = array();
    $LoanCollectionInfo = array();

    foreach ($CategoryName as $key => $CategoryName1) {
      $LoanDisbursmentInfo[$CategoryName1->id] = DB::table('mfn_loan')
            ->join('mfn_loans_product', 'mfn_loan.primaryProductIdFk', '=', 'mfn_loans_product.id')
            ->where([['mfn_loan.branchIdFk', $requestedBranch], ['mfn_loans_product.productCategoryId', $CategoryName1->id], ['mfn_loan.disbursementDate', '<=', $toDate]])
            ->sum('mfn_loan.totalRepayAmount');
    }

    foreach ($CategoryName as $key => $CategoryName1) {
      $LoanCollectionInfo[$CategoryName1->id] = DB::table('mfn_loan_collection')
            ->join('mfn_loans_product', 'mfn_loan_collection.primaryProductIdFk', '=', 'mfn_loans_product.id')
            ->where([['mfn_loan_collection.branchIdFk', $requestedBranch], ['mfn_loans_product.productCategoryId', $CategoryName1->id], ['mfn_loan_collection.collectionDate', '<=', $toDate]])
            ->sum('mfn_loan_collection.amount');
    }

    foreach ($LoanDisbursmentInfo as $key1 => $LoanDisbursmentInfo1) {
      foreach ($LoanCollectionInfo as $key2 => $LoanCollectionInfo1) {
        if ($key1 == $key2 and $LoanDisbursmentInfo1 >= $LoanCollectionInfo1) {
          $CategoryWiseOutstanding[$key1] = $LoanDisbursmentInfo1 - $LoanCollectionInfo1;
        }
        elseif ($key1 == $key2 and $LoanDisbursmentInfo1 < $LoanCollectionInfo1) {
          $CategoryWiseOutstanding[$key1] = 0.0;
        }
      }
    }

    // dd($CategoryWiseOutstanding);

    return $CategoryWiseOutstanding;
  }

  public function CategoryWiseDueQuery($requestedBranch, $requestedCategory, $fromDate, $toDate, $CategoryName){
    $CategoryWiseDue = array();
    $LoanSchedulInfo = array();
    $LoanCollectionInfo = array();

    foreach ($CategoryName as $key => $CategoryName1) {
      $LoanSchedulInfo[$CategoryName1->id] = DB::table('mfn_loan_schedule')
            ->join('mfn_loan', 'mfn_loan_schedule.loanIdFk', '=', 'mfn_loan.id')
            ->join('mfn_loans_product', 'mfn_loan.primaryProductIdFk', '=', 'mfn_loans_product.id')
            ->where([['mfn_loan.branchIdFk', $requestedBranch], ['mfn_loans_product.productCategoryId', $CategoryName1->id], ['mfn_loan_schedule.scheduleDate', '<=', $toDate]])
            ->sum('mfn_loan_schedule.installmentAmount');
    }

    foreach ($CategoryName as $key => $CategoryName1) {
      $LoanCollectionInfo[$CategoryName1->id] = DB::table('mfn_loan_collection')
            ->join('mfn_loans_product', 'mfn_loan_collection.primaryProductIdFk', '=', 'mfn_loans_product.id')
            ->where([['mfn_loan_collection.branchIdFk', $requestedBranch], ['mfn_loans_product.productCategoryId', $CategoryName1->id], ['mfn_loan_collection.collectionDate', '<=', $toDate]])
            ->sum('mfn_loan_collection.amount');
    }

    foreach ($LoanSchedulInfo as $key1 => $LoanSchedulInfo1) {
      foreach ($LoanCollectionInfo as $key2 => $LoanCollectionInfo1) {
        if ($key1 == $key2 and $LoanSchedulInfo1 >= $LoanCollectionInfo1) {
          $CategoryWiseDue[$key1] = $LoanSchedulInfo1 - $LoanCollectionInfo1;
        }
        elseif ($key1 == $key2 and $LoanSchedulInfo1 < $LoanCollectionInfo1) {
          $CategoryWiseDue[$key1] = 0.0;
        }
      }
    }

    // dd($LoanSchedulInfo);

    return $CategoryWiseDue;
  }

  public function CategoryWiseSavingQuery($requestedBranch, $requestedCategory, $fromDate, $toDate, $CategoryName, $SavingsProduct){
    $CategoryWiseSavings = array();
    $LoanSchedulInfo = array();
    $LoanCollectionInfo = array();

    foreach ($CategoryName as $key => $CategoryName1) {
      foreach ($SavingsProduct as $key => $SavingsProduct1) {
        $CategoryWiseSavings[$CategoryName1->id][$SavingsProduct1->id] = DB::table('mfn_savings_deposit')
                ->join('mfn_loans_product', 'mfn_savings_deposit.primaryProductIdFk', '=', 'mfn_loans_product.id')
                ->where([['mfn_savings_deposit.branchIdFk', $requestedBranch], ['mfn_loans_product.productCategoryId', $CategoryName1->id], ['mfn_savings_deposit.productIdFk', $SavingsProduct1->id], ['mfn_savings_deposit.depositDate', '>=', $fromDate], ['mfn_savings_deposit.depositDate', '<=', $toDate]])
                ->sum('mfn_savings_deposit.amount');
      }
    }

    // dd($CategoryWiseSavings);

    return $CategoryWiseSavings;
  }

  public function CategoryWiseManualOutstandingsQuery($requestedBranch, $requestedCategory, $fromDate, $toDate, $CategoryName, $SavingsProduct){
    $CategoryWiseManualOutstandings = array();

    foreach ($CategoryName as $key => $CategoryName1) {
      $CategoryWiseManualOutstandings[$CategoryName1->id] = DB::table('manual_pass_book_balance')
              ->join('mfn_loans_product', 'manual_pass_book_balance.productIdFk', '=', 'mfn_loans_product.id')
              ->where([['manual_pass_book_balance.branchIdFk', $requestedBranch], ['mfn_loans_product.productCategoryId', $CategoryName1->id], ['manual_pass_book_balance.date', $toDate]])
              ->sum('manual_pass_book_balance.outstanding');
    }

    // dd($CategoryWiseManualOutstandings);

    return $CategoryWiseManualOutstandings;
  }

  public function CategoryWiseManualDueQuery($requestedBranch, $requestedCategory, $fromDate, $toDate, $CategoryName, $SavingsProduct){
    $CategoryWiseManualDue = array();

    foreach ($CategoryName as $key => $CategoryName1) {
      $CategoryWiseManualDue[$CategoryName1->id] = DB::table('manual_pass_book_balance')
              ->join('mfn_loans_product', 'manual_pass_book_balance.productIdFk', '=', 'mfn_loans_product.id')
              ->where([['manual_pass_book_balance.branchIdFk', $requestedBranch], ['mfn_loans_product.productCategoryId', $CategoryName1->id], ['manual_pass_book_balance.date', $toDate]])
              ->sum('manual_pass_book_balance.due');
    }

    // dd($CategoryWiseManualDue);

    return $CategoryWiseManualDue;
  }

  public function CategoryWiseManualSavingsQuery($requestedBranch, $requestedCategory, $fromDate, $toDate, $CategoryName, $SavingsProduct){
    $CategoryWiseManualSavings = array();

    foreach ($CategoryName as $key => $CategoryName1) {
      foreach ($SavingsProduct as $key => $SavingsProduct1) {
        $CategoryWiseManualSavings[$CategoryName1->id][$SavingsProduct1->id] = DB::table('manual_pass_book_balance')
                ->join('mfn_loans_product', 'manual_pass_book_balance.productIdFk', '=', 'mfn_loans_product.id')
                ->join('mfn_savings_account', 'manual_pass_book_balance.savingsCode', '=', 'mfn_savings_account.savingsCode')
                ->where([['manual_pass_book_balance.branchIdFk', $requestedBranch], ['mfn_savings_account.savingsProductIdFk', $SavingsProduct1->id], ['mfn_loans_product.productCategoryId', $CategoryName1->id], ['manual_pass_book_balance.date', $toDate]])
                ->sum('manual_pass_book_balance.savingsBalance');
      }
    }

    // dd($CategoryWiseManualSavings);

    return $CategoryWiseManualSavings;
  }

  // Main method for calculation...........
  public function getReport(Request $request){
    // Requested data variables.....
    $requestedBranch = $request->searchBranch;
    $requestedCategory = $request->searchProCat;
    $requestedYear = $request->Year;
    $requestedQuerter = $request->DateRange;

    // Declared data variables.....
    $fromDate = '';
    $toDate = '';
    $areaInfos = '';
    $startDate = '';
    $querter = '';

    // Declared data array......
    $BranchInfos = array();
    $CreditOfficerInfos = array();
    $SamityInfos = array();
    $MemberInfos = array();
    $LoanBorrowerInfos = array();
    $LoanDisbursmentInfos = array();
    $OutstandingInfos = array();
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
    $ManualOutstandings = array();
    $ManualDue = array();
    $ManualSavings = array();


    // dd($RequestedQuerter);

    switch ($requestedQuerter) {
       case '1-3':
           $fromDate = $requestedYear.'-01-01';
           $toDate = $requestedYear.'-03-31';
           $querter = '1st';
           break;
       case '4-6':
           $fromDate = $requestedYear.'-04-01';
           $toDate = $requestedYear.'-06-30';
           $querter = '2nd';
           break;
       case '7-9':
           $fromDate = $requestedYear.'-07-01';
           $toDate = $requestedYear.'-09-30';
           $querter = '3rd';
           break;
       case '10-12':
           $fromDate = $requestedYear.'-10-01';
           $toDate = $requestedYear.'-12-31';
           $querter = '4th';
           break;
   }

   // dd($startDate);

   // Query and methods to call (FOR TABLE 1) ...................................................
   $SavingsProduct = DB::table('mfn_saving_product')
                  ->get();

   $BranchInfos = $this->BranchQuery($requestedBranch);

   $AreaInfos = $this->AreaQuery($requestedBranch);

   $CreditOfficerInfos = $this->CreditOfficerQuery($requestedBranch);

   $SamityInfos = $this->SamityQuery($requestedBranch, $CreditOfficerInfos);

   $MemberInfos = $this->MemberQuery($requestedBranch, $CreditOfficerInfos, $requestedCategory,$fromDate, $toDate);

   $LoanBorrowerInfos = $this->LoanBorrowerQuery($requestedBranch, $CreditOfficerInfos, $requestedCategory,$fromDate, $toDate);

   $LoanDisbursmentInfos = $this->LoanDisbursmentQuery($requestedBranch, $CreditOfficerInfos, $requestedCategory,$fromDate, $toDate);

   $OutstandingInfos = $this->OutstandingQuery($requestedBranch, $CreditOfficerInfos, $requestedCategory, $fromDate, $toDate);

   $DueInfos = $this->DueQuery($requestedBranch, $CreditOfficerInfos, $requestedCategory, $fromDate, $toDate);

   $SavingsInfos = $this->SavingsQuery($requestedBranch, $CreditOfficerInfos, $requestedCategory, $fromDate, $toDate, $SavingsProduct);

   $ManualOutstandings = $this->ManualOutstandingsQuery($requestedBranch, $CreditOfficerInfos, $requestedCategory, $fromDate, $toDate, $SavingsProduct);

   $ManualDue = $this->ManualDuQuery($requestedBranch, $CreditOfficerInfos, $requestedCategory, $fromDate, $toDate, $SavingsProduct);

   $ManualSavings = $this->ManualSavingsQuery($requestedBranch, $CreditOfficerInfos, $requestedCategory, $fromDate, $toDate, $SavingsProduct);

   // Query and methods to call (FOR TABLE 2) ...................................................
   $CategoryName = $this->CategorynameQuery($requestedBranch, $requestedCategory, $fromDate, $toDate);

   $CategoryWiseOutstanding = $this->CategoryWiseOutstandingQuery($requestedBranch, $requestedCategory, $fromDate, $toDate, $CategoryName);

   $CategoryWiseDue = $this->CategoryWiseDueQuery($requestedBranch, $requestedCategory, $fromDate, $toDate, $CategoryName);

   $CategoryWiseSavings = $this->CategoryWiseSavingQuery($requestedBranch, $requestedCategory, $fromDate, $toDate, $CategoryName, $SavingsProduct);

   $CategoryWiseManualOutstandings = $this->CategoryWiseManualOutstandingsQuery($requestedBranch, $requestedCategory, $fromDate, $toDate, $CategoryName, $SavingsProduct);

   $CategoryWiseManualDue = $this->CategoryWiseManualDueQuery($requestedBranch, $requestedCategory, $fromDate, $toDate, $CategoryName, $SavingsProduct);

   $CategoryWiseManualSavings = $this->CategoryWiseManualSavingsQuery($requestedBranch, $requestedCategory, $fromDate, $toDate, $CategoryName, $SavingsProduct);


                  // return "OK";
   return  view('microfin.reports.passbookBalancingRegisterReportBranch.passbookBalancingRegisterReportBranchTable', compact('SavingsProduct', 'querter', 'requestedQuerter',
   'requestedYear', 'BranchInfos', 'AreaInfos', 'CreditOfficerInfos', 'SamityInfos', 'MemberInfos', 'LoanBorrowerInfos', 'LoanDisbursmentInfos', 'OutstandingInfos', 'DueInfos',
   'SavingsInfos', 'CategoryName', 'CategoryWiseOutstanding', 'CategoryWiseDue', 'CategoryWiseSavings', 'CategoryWiseManualOutstandings', 'CategoryWiseManualDue', 'CategoryWiseManualSavings',
   'ManualOutstandings', 'ManualDue', 'ManualSavings'));
  }

}
