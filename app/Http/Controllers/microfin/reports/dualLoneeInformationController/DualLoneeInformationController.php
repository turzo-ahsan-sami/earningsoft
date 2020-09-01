<?php

namespace App\Http\Controllers\microfin\reports\dualLoneeInformationController;

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
class DualLoneeInformationController extends Controller
{
  public function getBranchName(){
    $BranchDatas = DB::table('gnr_branch')->get();

    return view('microfin.reports.dualLoneeInformationViews.DualLoneeInformationForm', compact('BranchDatas'));

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

  public function getReport(Request $request){
    $SelectionType = $request->DateSelection;
    $BranchId = $request->searchBranch;
    $SamityId = $request->Samity;
    $SearchTypes = $request->SearchType;
    // $date=date_create("0000-00-00");
    // $DateFrom = date_format($date,"Y-m-d");

    if ($SelectionType == 'N') {
      // code...
      $DateFrom = $request->txtDate1;
    }
    else {
      // code...
      $DateFrom = '';
    }
    $DateTo = $request->txtDate2;

    $BranchInfos = array();
    $SamityInfos = array();
    $BranchAllInfos = array();
    $samityAllInfos = array();
    $MmeberInfos = array();
    $MmeberOPtionalProductInfos = array();
    $MemberOutstanding = array();
    $MemberRepaymentWeek = array();
    $AdvDue1 = array();
    $AdvDue2 = array();

    if ($BranchId != 'All') {
      // for only single branch
      $BranchInfos = DB::table('gnr_branch')
                    ->where('id', $BranchId)
                    ->get();
    }
    else {
      //for all branch
      $BranchInfos = DB::table('gnr_branch')
                    ->get();
    }

    if ($SamityId != 'All') {
      // for single samity
      $SamityInfos = DB::table('mfn_samity')
                    ->where('id', $SamityId)
                    ->get();
    }
    else {
      // for all samity
      $SamityInfos = DB::table('mfn_samity')
                    ->get();
    }

    if ($SelectionType == 'N') {
      // with Date Range
      if ($BranchId == 'All') {
        // for all branch
        if ($SamityId == 'All') {
          // for all samity

            $BranchAllInfos = DB::table('mfn_loan')
                          ->select('memberIdFk', 'id', 'branchIdFk', 'samityIdFk')
                          // ->where('branchIdFk', $BranchId)
                          // ->where('samityIdFk', $SamityId)
                          ->where('status', '=', 1)
                          ->get();

                          // dd($BranchAllInfos);

            foreach ($BranchAllInfos as $key => $BranchAllInfo) {
              $MemberIds = $BranchAllInfo->memberIdFk;
              $LoanId = $BranchAllInfo->id;
              $Bid = $BranchAllInfo->branchIdFk;
              $Sid = $BranchAllInfo->samityIdFk;
              $MmeberInfos[$MemberIds] = DB::table('mfn_loan')
                          ->select('mfn_member_information.name', 'mfn_member_information.code as memberCode', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.loanTypeId', 'mfn_loan.id', 'mfn_loan.installmentAmount', 'mfn_loan.repaymentNo')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '>=', $DateFrom)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->get();

              $MmeberOPtionalProductInfos[$MemberIds] = DB::table('mfn_loan')
                          ->select('mfn_member_information.name', 'mfn_member_information.code as memberCode', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.loanTypeId', 'mfn_loan.id', 'mfn_loan.installmentAmount', 'mfn_loan.repaymentNo')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '>=', $DateFrom)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.primaryProductIdFk', '=', 0)
                          ->where('mfn_loan.status', '=', 1)
                          ->get();

              $MemberOutstanding[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '>=', $DateFrom)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->sum('mfn_loan_collection.amount');

              $MemberRepaymentWeek[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '>=', $DateFrom)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_schedule.loanIdFk', $LoanId)
                          ->where('mfn_loan_schedule.isCompleted', '=', 1)
                          ->count('mfn_loan_schedule.scheduleDate');

              $AdvDue1[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan_collection.collectionDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_collection.loanIdFk', '=', $LoanId)
                          ->sum('mfn_loan_collection.amount');

              $AdvDue2[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          // ->select('mfn_loan_schedule.installmentAmount', 'mfn_loan_schedule.scheduleDate')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.branchIdFk', $Bid)
                          ->where('mfn_loan.samityIdFk', $Sid)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_schedule.loanIdFk', $LoanId)
                          ->sum('mfn_loan_schedule.installmentAmount');
            }
            // dd($MmeberOPtionalProductInfos);


        }
        elseif ($SamityId != 'All') {
          // for single samity

            $BranchAllInfos = DB::table('mfn_loan')
                          ->select('memberIdFk', 'id', 'branchIdFk', 'samityIdFk')
                          // ->where('branchIdFk', $BranchId)
                          ->where('samityIdFk', $SamityId)
                          ->where('status', '=', 1)
                          ->get();

                          // dd($BranchAllInfos);

            foreach ($BranchAllInfos as $key => $BranchAllInfo) {
              $MemberIds = $BranchAllInfo->memberIdFk;
              $LoanId = $BranchAllInfo->id;
              $Bid = $BranchAllInfo->branchIdFk;
              $Sid = $BranchAllInfo->samityIdFk;
              $MmeberInfos[$MemberIds] = DB::table('mfn_loan')
                          ->select('mfn_member_information.name', 'mfn_member_information.code as memberCode', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.loanTypeId', 'mfn_loan.id', 'mfn_loan.installmentAmount', 'mfn_loan.repaymentNo')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '>=', $DateFrom)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->get();

              $MmeberOPtionalProductInfos[$MemberIds] = DB::table('mfn_loan')
                          ->select('mfn_member_information.name', 'mfn_member_information.code as memberCode', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.loanTypeId', 'mfn_loan.id', 'mfn_loan.installmentAmount', 'mfn_loan.repaymentNo')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '>=', $DateFrom)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.primaryProductIdFk', '=', 0)
                          ->where('mfn_loan.status', '=', 1)
                          ->get();

              $MemberOutstanding[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '>=', $DateFrom)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->sum('mfn_loan_collection.amount');

              $MemberRepaymentWeek[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '>=', $DateFrom)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_schedule.loanIdFk', $LoanId)
                          ->where('mfn_loan_schedule.isCompleted', '=', 1)
                          ->count('mfn_loan_schedule.scheduleDate');

              $AdvDue1[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan_collection.collectionDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_collection.loanIdFk', '=', $LoanId)
                          ->sum('mfn_loan_collection.amount');

              $AdvDue2[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          // ->select('mfn_loan_schedule.installmentAmount', 'mfn_loan_schedule.scheduleDate')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.branchIdFk', $Bid)
                          ->where('mfn_loan.samityIdFk', $Sid)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_schedule.loanIdFk', $LoanId)
                          ->sum('mfn_loan_schedule.installmentAmount');
            }


        }
      }
      elseif ($BranchId != 'All') {
        // for single branch
        if ($SamityId == 'All') {
          // for all samity

            $BranchAllInfos = DB::table('mfn_loan')
                          ->select('memberIdFk', 'id', 'branchIdFk', 'samityIdFk')
                          ->where('branchIdFk', $BranchId)
                          // ->where('samityIdFk', $SamityId)
                          ->where('status', '=', 1)
                          ->get();

                          // dd($BranchAllInfos);

            foreach ($BranchAllInfos as $key => $BranchAllInfo) {
              $MemberIds = $BranchAllInfo->memberIdFk;
              $LoanId = $BranchAllInfo->id;
              $Bid = $BranchAllInfo->branchIdFk;
              $Sid = $BranchAllInfo->samityIdFk;
              $MmeberInfos[$MemberIds] = DB::table('mfn_loan')
                          ->select('mfn_member_information.name', 'mfn_member_information.code as memberCode', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.loanTypeId', 'mfn_loan.id', 'mfn_loan.installmentAmount', 'mfn_loan.repaymentNo')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '>=', $DateFrom)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->get();

              $MmeberOPtionalProductInfos[$MemberIds] = DB::table('mfn_loan')
                          ->select('mfn_member_information.name', 'mfn_member_information.code as memberCode', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.loanTypeId', 'mfn_loan.id', 'mfn_loan.installmentAmount', 'mfn_loan.repaymentNo')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '>=', $DateFrom)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.primaryProductIdFk', '=', 0)
                          ->where('mfn_loan.status', '=', 1)
                          ->get();

              $MemberOutstanding[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '>=', $DateFrom)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->sum('mfn_loan_collection.amount');

              $MemberRepaymentWeek[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '>=', $DateFrom)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_schedule.loanIdFk', $LoanId)
                          ->where('mfn_loan_schedule.isCompleted', '=', 1)
                          ->count('mfn_loan_schedule.scheduleDate');

              $AdvDue1[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan_collection.collectionDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_collection.loanIdFk', '=', $LoanId)
                          ->sum('mfn_loan_collection.amount');

              $AdvDue2[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          // ->select('mfn_loan_schedule.installmentAmount', 'mfn_loan_schedule.scheduleDate')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.branchIdFk', $Bid)
                          ->where('mfn_loan.samityIdFk', $Sid)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_schedule.loanIdFk', $LoanId)
                          ->sum('mfn_loan_schedule.installmentAmount');
            }


        }
        elseif ($SamityId != 'All') {
          // for single samity
            $BranchAllInfos = DB::table('mfn_loan')
                          ->select('memberIdFk', 'id', 'branchIdFk', 'samityIdFk')
                          ->where('branchIdFk', $BranchId)
                          ->where('samityIdFk', $SamityId)
                          ->where('status', '=', 1)
                          ->get();

            foreach ($BranchAllInfos as $key => $BranchAllInfo) {
              $MemberIds = $BranchAllInfo->memberIdFk;
              $LoanId = $BranchAllInfo->id;
              $Bid = $BranchAllInfo->branchIdFk;
              $Sid = $BranchAllInfo->samityIdFk;
              $MmeberInfos[$MemberIds] = DB::table('mfn_loan')
                          ->select('mfn_member_information.name', 'mfn_member_information.code as memberCode', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.loanTypeId', 'mfn_loan.id', 'mfn_loan.installmentAmount', 'mfn_loan.repaymentNo')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '>=', $DateFrom)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->get();

              $MmeberOPtionalProductInfos[$MemberIds] = DB::table('mfn_loan')
                          ->select('mfn_member_information.name', 'mfn_member_information.code as memberCode', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.loanTypeId', 'mfn_loan.id', 'mfn_loan.installmentAmount', 'mfn_loan.repaymentNo')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '>=', $DateFrom)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.primaryProductIdFk', '=', 0)
                          ->where('mfn_loan.status', '=', 1)
                          ->get();

              $MemberOutstanding[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '>=', $DateFrom)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->sum('mfn_loan_collection.amount');

              $MemberRepaymentWeek[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '>=', $DateFrom)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_schedule.loanIdFk', $LoanId)
                          ->where('mfn_loan_schedule.isCompleted', '=', 1)
                          ->count('mfn_loan_schedule.scheduleDate');

              $AdvDue1[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan_collection.collectionDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_collection.loanIdFk', '=', $LoanId)
                          ->sum('mfn_loan_collection.amount');

              $AdvDue2[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          // ->select('mfn_loan_schedule.installmentAmount', 'mfn_loan_schedule.scheduleDate')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.branchIdFk', $Bid)
                          ->where('mfn_loan.samityIdFk', $Sid)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_schedule.loanIdFk', $LoanId)
                          ->sum('mfn_loan_schedule.installmentAmount');
            }
            // dd($MmeberOPtionalProductInfos);

        }
      }
    }
    elseif ($SelectionType == 'Y') {
      // without Date Range and it's as on date
      if ($BranchId == 'All') {
        // for all branch
        if ($SamityId == 'All') {
          // for all samity

            $BranchAllInfos = DB::table('mfn_loan')
                          ->select('memberIdFk', 'id', 'branchIdFk', 'samityIdFk')
                          // ->where('branchIdFk', $BranchId)
                          // ->where('samityIdFk', $SamityId)
                          ->where('status', '=', 1)
                          ->get();

                          // dd($BranchAllInfos);

            foreach ($BranchAllInfos as $key => $BranchAllInfo) {
              $MemberIds = $BranchAllInfo->memberIdFk;
              $LoanId = $BranchAllInfo->id;
              $Bid = $BranchAllInfo->branchIdFk;
              $Sid = $BranchAllInfo->samityIdFk;
              $MmeberInfos[$MemberIds] = DB::table('mfn_loan')
                          ->select('mfn_member_information.name', 'mfn_member_information.code as memberCode', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.loanTypeId', 'mfn_loan.id', 'mfn_loan.installmentAmount', 'mfn_loan.repaymentNo')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->get();

              $MmeberOPtionalProductInfos[$MemberIds] = DB::table('mfn_loan')
                          ->select('mfn_member_information.name', 'mfn_member_information.code as memberCode', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.loanTypeId', 'mfn_loan.id', 'mfn_loan.installmentAmount', 'mfn_loan.repaymentNo')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.primaryProductIdFk', '=', 0)
                          ->where('mfn_loan.status', '=', 1)
                          ->get();

              $MemberOutstanding[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->sum('mfn_loan_collection.amount');

              $MemberRepaymentWeek[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_schedule.loanIdFk', $LoanId)
                          ->where('mfn_loan_schedule.isCompleted', '=', 1)
                          ->count('mfn_loan_schedule.scheduleDate');

              $AdvDue1[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan_collection.collectionDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_collection.loanIdFk', '=', $LoanId)
                          ->sum('mfn_loan_collection.amount');

              $AdvDue2[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          // ->select('mfn_loan_schedule.installmentAmount', 'mfn_loan_schedule.scheduleDate')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.branchIdFk', $Bid)
                          ->where('mfn_loan.samityIdFk', $Sid)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_schedule.loanIdFk', $LoanId)
                          ->sum('mfn_loan_schedule.installmentAmount');
            }

        }
        elseif ($SamityId != 'All') {
          // for single samity

            $BranchAllInfos = DB::table('mfn_loan')
                          ->select('memberIdFk', 'id', 'branchIdFk', 'samityIdFk')
                          // ->where('branchIdFk', $BranchId)
                          ->where('samityIdFk', $SamityId)
                          ->where('status', '=', 1)
                          ->get();

                          // dd($BranchAllInfos);

            foreach ($BranchAllInfos as $key => $BranchAllInfo) {
              $MemberIds = $BranchAllInfo->memberIdFk;
              $LoanId = $BranchAllInfo->id;
              $Bid = $BranchAllInfo->branchIdFk;
              $Sid = $BranchAllInfo->samityIdFk;
              $MmeberInfos[$MemberIds] = DB::table('mfn_loan')
                          ->select('mfn_member_information.name', 'mfn_member_information.code as memberCode', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.loanTypeId', 'mfn_loan.id', 'mfn_loan.installmentAmount', 'mfn_loan.repaymentNo')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->get();

              $MmeberOPtionalProductInfos[$MemberIds] = DB::table('mfn_loan')
                          ->select('mfn_member_information.name', 'mfn_member_information.code as memberCode', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.loanTypeId', 'mfn_loan.id', 'mfn_loan.installmentAmount', 'mfn_loan.repaymentNo')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.primaryProductIdFk', '=', 0)
                          ->where('mfn_loan.status', '=', 1)
                          ->get();

              $MemberOutstanding[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->sum('mfn_loan_collection.amount');

              $MemberRepaymentWeek[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_schedule.loanIdFk', $LoanId)
                          ->where('mfn_loan_schedule.isCompleted', '=', 1)
                          ->count('mfn_loan_schedule.scheduleDate');

              $AdvDue1[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan_collection.collectionDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_collection.loanIdFk', '=', $LoanId)
                          ->sum('mfn_loan_collection.amount');

              $AdvDue2[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          // ->select('mfn_loan_schedule.installmentAmount', 'mfn_loan_schedule.scheduleDate')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.branchIdFk', $Bid)
                          ->where('mfn_loan.samityIdFk', $Sid)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_schedule.loanIdFk', $LoanId)
                          ->sum('mfn_loan_schedule.installmentAmount');
            }

        }
      }
      elseif ($BranchId != 'All') {
        // for single branch
        if ($SamityId == 'All') {
          // for all samity

            $BranchAllInfos = DB::table('mfn_loan')
                          ->select('memberIdFk', 'id', 'branchIdFk', 'samityIdFk')
                          ->where('branchIdFk', $BranchId)
                          // ->where('samityIdFk', $SamityId)
                          ->where('status', '=', 1)
                          ->get();

                          // dd($BranchAllInfos);

            foreach ($BranchAllInfos as $key => $BranchAllInfo) {
              $MemberIds = $BranchAllInfo->memberIdFk;
              $LoanId = $BranchAllInfo->id;
              $Bid = $BranchAllInfo->branchIdFk;
              $Sid = $BranchAllInfo->samityIdFk;
              $MmeberInfos[$MemberIds] = DB::table('mfn_loan')
                          ->select('mfn_member_information.name', 'mfn_member_information.code as memberCode', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.loanTypeId', 'mfn_loan.id', 'mfn_loan.installmentAmount', 'mfn_loan.repaymentNo')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->get();

              $MmeberOPtionalProductInfos[$MemberIds] = DB::table('mfn_loan')
                          ->select('mfn_member_information.name', 'mfn_member_information.code as memberCode', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.loanTypeId', 'mfn_loan.id', 'mfn_loan.installmentAmount', 'mfn_loan.repaymentNo')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.primaryProductIdFk', '=', 0)
                          ->where('mfn_loan.status', '=', 1)
                          ->get();

              $MemberOutstanding[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->sum('mfn_loan_collection.amount');

              $MemberRepaymentWeek[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_schedule.loanIdFk', $LoanId)
                          ->where('mfn_loan_schedule.isCompleted', '=', 1)
                          ->count('mfn_loan_schedule.scheduleDate');

              $AdvDue1[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan_collection.collectionDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_collection.loanIdFk', '=', $LoanId)
                          ->sum('mfn_loan_collection.amount');

              $AdvDue2[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          // ->select('mfn_loan_schedule.installmentAmount', 'mfn_loan_schedule.scheduleDate')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.branchIdFk', $Bid)
                          ->where('mfn_loan.samityIdFk', $Sid)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_schedule.loanIdFk', $LoanId)
                          ->sum('mfn_loan_schedule.installmentAmount');
            }

        }
        elseif ($SamityId != 'All') {
          // for single samity
          // if ($SearchTypes == 'DualLoanee') {
            // search type is Dual loanee

            $BranchAllInfos = DB::table('mfn_loan')
                          ->select('memberIdFk', 'id', 'branchIdFk', 'samityIdFk')
                          ->where('branchIdFk', $BranchId)
                          ->where('samityIdFk', $SamityId)
                          ->where('status', '=', 1)
                          ->get();

                          // dd($BranchAllInfos);

            foreach ($BranchAllInfos as $key => $BranchAllInfo) {
              $MemberIds = $BranchAllInfo->memberIdFk;
              $LoanId = $BranchAllInfo->id;
              $Bid = $BranchAllInfo->branchIdFk;
              $Sid = $BranchAllInfo->samityIdFk;
              $MmeberInfos[$MemberIds] = DB::table('mfn_loan')
                          ->select('mfn_member_information.name', 'mfn_member_information.code as memberCode', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.loanTypeId', 'mfn_loan.id', 'mfn_loan.installmentAmount', 'mfn_loan.repaymentNo')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->get();

              $MmeberOPtionalProductInfos[$MemberIds] = DB::table('mfn_loan')
                          ->select('mfn_member_information.name', 'mfn_member_information.code as memberCode', 'mfn_loan.loanCode', 'mfn_loan.disbursementDate', 'mfn_loan.loanAmount', 'mfn_loan.totalRepayAmount', 'mfn_loan.loanTypeId', 'mfn_loan.id', 'mfn_loan.installmentAmount', 'mfn_loan.repaymentNo')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.primaryProductIdFk', '=', 0)
                          ->where('mfn_loan.status', '=', 1)
                          ->get();

              $MemberOutstanding[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->sum('mfn_loan_collection.amount');

              $MemberRepaymentWeek[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.disbursementDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_schedule.loanIdFk', $LoanId)
                          ->where('mfn_loan_schedule.isCompleted', '=', 1)
                          ->count('mfn_loan_schedule.scheduleDate');

              $AdvDue1[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan_collection.collectionDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_collection.loanIdFk', '=', $LoanId)
                          ->sum('mfn_loan_collection.amount');

              $AdvDue2[$MemberIds][$LoanId] = DB::table('mfn_loan')
                          // ->select('mfn_loan_schedule.installmentAmount', 'mfn_loan_schedule.scheduleDate')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where('mfn_loan.memberIdFk', $MemberIds)
                          ->where('mfn_loan.branchIdFk', $Bid)
                          ->where('mfn_loan.samityIdFk', $Sid)
                          ->where('mfn_loan_schedule.scheduleDate', '<=', $DateTo)
                          ->where('mfn_loan.status', '=', 1)
                          ->where('mfn_loan_schedule.loanIdFk', $LoanId)
                          ->sum('mfn_loan_schedule.installmentAmount');
            }

          // }
          // elseif ($SearchTypes == 'OnlyOptionalProductLoanee') {
          //   // search type is only optional loanee
          //
          // }
        }
      }
    }

    // dd($SelectionType);

    return view("microfin.reports.dualLoneeInformationViews.DualLoneeInformationReport", compact('DateFrom', 'DateTo', 'BranchInfos', 'SamityInfos',
    'BranchId', 'SamityId', 'SelectionType', 'MmeberInfos', 'MemberOutstanding', 'MemberRepaymentWeek', 'MmeberOPtionalProductInfos', 'AdvDue1', 'AdvDue2',
    'SearchTypes'));

  }

}
