<?php

namespace App\Http\Controllers\microfin\reports\dueCollectionRegisterController;

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
class DueCollectionRegisterController extends Controller
{
  public function getBranchName(){
    $BranchDatas = DB::table('gnr_branch')->get();
    $ProductDatas = DB::table('mfn_loans_product')->get();

    return view('microfin.reports.dueCollectionRegister.DueCollectionRegisterForm', compact('BranchDatas', 'ProductDatas'));

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
    $BranchId = $request->searchBranch;
    $ProductId = $request->Product;
    $SamityId = $request->Samity;
    $DateFrom = $request->txtDate1;
    $DateTo = $request->txtDate2;
    $SearchType = $request->SearchType;

    $BranchInfos = array();
    $ProductInfos = array();
    $MemberInfos = array();
    $ScheduleGeneralInfos = array();
    $CollectionGeneralInfos = array();
    $ScheduleInfos = array();
    $CollectionInfos = array();
    $SamityInfos = array();

    if ($BranchId != 'All') {
      // for only single branch
      $BranchInfos = DB::table('gnr_branch')
                    ->where('id', $BranchId)
                    ->get();

      $SamityInfos = DB::table('mfn_samity')
                    ->select('id')
                    ->where('branchId', $BranchId)
                    ->get();

                    // dd($SamityInfos);
    }
    else {
      //for all branch
      $BranchInfos = DB::table('gnr_branch')
                    ->get();
    }

    if ($ProductId != 'All') {
      // for single samity
      $ProductInfos = DB::table('mfn_loans_product')
                    ->where('id', $ProductId)
                    ->get();
    }
    else {
      // for all samity
      $ProductInfos = DB::table('mfn_samity')
                    ->get();
    }

    if ($BranchId == 'All') {
      // if all branch selected
      if ($SamityId == 'All') {
        // if all samity selected
        if ($ProductId == 'All') {
          // if all product selected
        }
        else {
          // if a single product selected
        }
      }
      else {
        // if a single samity selected
        if ($ProductId == 'All') {
          // if all product selected
        }
        else {
          // if a single product selected

        }
      }
    }
    else {
      // if only a single branch selected
      if ($SamityId == 'All') {
        // if all samity selected
        if ($ProductId == 'All') {
          // if all product selected
          if ($SearchType == 'WithServiceCharge') {
            // if with Service charge
            $MemberInfos = DB::table('mfn_loan')
                          ->select('mfn_member_information.id as MemberID', 'mfn_member_information.name as MemberName', 'mfn_member_information.code as MemberCode', 'mfn_samity.id as SamityID', 'mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.branchIdFk', 'mfn_loan.totalRepayAmount')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                          ->where([['mfn_loan.branchIdFk', $BranchId]])
                          ->get();

                          // dd($MemberInfos);

            foreach ($MemberInfos as $key => $MemberInfo) {
              $SamityID = $MemberInfo->SamityID;
              $MemberID = $MemberInfo->MemberID;

              $ScheduleGeneralInfos[$MemberID]  = DB::table('mfn_loan')
                            ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_schedule.scheduleDate', 'mfn_loan_schedule.installmentAmount', 'mfn_loan.status')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan_schedule.scheduleDate', '>=', $DateFrom], ['mfn_loan_schedule.scheduleDate', '<=', $DateTo]])
                            ->orderByRaw('mfn_loan_schedule.scheduleDate DESC')
                            ->limit(1)
                            ->get();
                            // ->sum('mfn_loan_schedule.installmentAmount');

              $CollectionGeneralInfos[$MemberID] = DB::table('mfn_loan')
                            ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_collection.collectionDate', 'mfn_loan_collection.amount')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan_collection.amount', '>', 0], ['mfn_loan_collection.collectionDate', '>=', $DateFrom], ['mfn_loan_collection.collectionDate', '<=', $DateTo]])
                            ->orderByRaw('mfn_loan_collection.collectionDate DESC')
                            ->limit(1)
                            ->get();
                            // ->sum('mfn_loan_collection.amount');

              $ScheduleInfos[$MemberID] = DB::table('mfn_loan')
                            // ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_schedule.scheduleDate', 'mfn_loan_schedule.installmentAmount')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan_schedule.scheduleDate', '>=', $DateFrom], ['mfn_loan_schedule.scheduleDate', '<=', $DateTo]])
                            // ->get();
                            ->sum('mfn_loan_schedule.installmentAmount');

              $CollectionInfos[$MemberID] = DB::table('mfn_loan')
                            // ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_collection.collectionDate', 'mfn_loan_collection.amount')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan_collection.collectionDate', '>=', $DateFrom], ['mfn_loan_collection.collectionDate', '<=', $DateTo]])
                            // ->get();
                            ->sum('mfn_loan_collection.amount');
            }
          }
          else {
            // if without service charge
            $MemberInfos = DB::table('mfn_loan')
                          ->select('mfn_member_information.id as MemberID', 'mfn_member_information.name as MemberName', 'mfn_member_information.code as MemberCode', 'mfn_samity.id as SamityID', 'mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.branchIdFk', 'mfn_loan.loanAmount as totalRepayAmount')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                          ->where([['mfn_loan.branchIdFk', $BranchId]])
                          ->get();

                          // dd($MemberInfos);

            foreach ($MemberInfos as $key => $MemberInfo) {
              $MemberID = $MemberInfo->MemberID;

              $ScheduleGeneralInfos[$MemberID]  = DB::table('mfn_loan')
                            ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_schedule.scheduleDate', 'mfn_loan_schedule.principalAmount', 'mfn_loan.status')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan_schedule.scheduleDate', '>=', $DateFrom], ['mfn_loan_schedule.scheduleDate', '<=', $DateTo]])
                            ->orderByRaw('mfn_loan_schedule.scheduleDate DESC')
                            ->limit(1)
                            ->get();
                            // ->sum('mfn_loan_schedule.installmentAmount');

              $CollectionGeneralInfos[$MemberID] = DB::table('mfn_loan')
                            ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_collection.collectionDate', 'mfn_loan_collection.principalAmount as amount')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan_collection.amount', '>', 0], ['mfn_loan_collection.collectionDate', '>=', $DateFrom], ['mfn_loan_collection.collectionDate', '<=', $DateTo]])
                            ->orderByRaw('mfn_loan_collection.collectionDate DESC')
                            ->limit(1)
                            ->get();
                            // ->sum('mfn_loan_collection.amount');

              $ScheduleInfos[$MemberID] = DB::table('mfn_loan')
                            // ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_schedule.scheduleDate', 'mfn_loan_schedule.installmentAmount')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan_schedule.scheduleDate', '>=', $DateFrom], ['mfn_loan_schedule.scheduleDate', '<=', $DateTo]])
                            // ->get();
                            ->sum('mfn_loan_schedule.principalAmount');

              $CollectionInfos[$MemberID] = DB::table('mfn_loan')
                            // ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_collection.collectionDate', 'mfn_loan_collection.amount')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan_collection.collectionDate', '>=', $DateFrom], ['mfn_loan_collection.collectionDate', '<=', $DateTo]])
                            // ->get();
                            ->sum('mfn_loan_collection.principalAmount');
            }
          }

        }
        else {
          // if a single product selected
          if ($SearchType == 'WithServiceCharge') {
            // if with Service charge
            $MemberInfos = DB::table('mfn_loan')
                          ->select('mfn_member_information.id as MemberID', 'mfn_member_information.name as MemberName', 'mfn_member_information.code as MemberCode', 'mfn_samity.id as SamityID', 'mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.branchIdFk', 'mfn_loan.totalRepayAmount')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                          ->where([['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.productIdFk', $ProductId]])
                          ->get();

                          // dd($MemberInfos);

            foreach ($MemberInfos as $key => $MemberInfo) {
              $MemberID = $MemberInfo->MemberID;

              $ScheduleGeneralInfos[$MemberID]  = DB::table('mfn_loan')
                            ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_schedule.scheduleDate', 'mfn_loan_schedule.installmentAmount', 'mfn_loan.status')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.productIdFk', $ProductId], ['mfn_loan_schedule.scheduleDate', '>=', $DateFrom], ['mfn_loan_schedule.scheduleDate', '<=', $DateTo]])
                            ->orderByRaw('mfn_loan_schedule.scheduleDate DESC')
                            ->limit(1)
                            ->get();
                            // ->sum('mfn_loan_schedule.installmentAmount');

              $CollectionGeneralInfos[$MemberID] = DB::table('mfn_loan')
                            ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_collection.collectionDate', 'mfn_loan_collection.amount')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.productIdFk', $ProductId], ['mfn_loan_collection.amount', '>', 0], ['mfn_loan_collection.collectionDate', '>=', $DateFrom], ['mfn_loan_collection.collectionDate', '<=', $DateTo]])
                            ->orderByRaw('mfn_loan_collection.collectionDate DESC')
                            ->limit(1)
                            ->get();
                            // ->sum('mfn_loan_collection.amount');

              $ScheduleInfos[$MemberID] = DB::table('mfn_loan')
                            // ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_schedule.scheduleDate', 'mfn_loan_schedule.installmentAmount')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.productIdFk', $ProductId], ['mfn_loan_schedule.scheduleDate', '>=', $DateFrom], ['mfn_loan_schedule.scheduleDate', '<=', $DateTo]])
                            // ->get();
                            ->sum('mfn_loan_schedule.installmentAmount');

              $CollectionInfos[$MemberID] = DB::table('mfn_loan')
                            // ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_collection.collectionDate', 'mfn_loan_collection.amount')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.productIdFk', $ProductId], ['mfn_loan_collection.collectionDate', '>=', $DateFrom], ['mfn_loan_collection.collectionDate', '<=', $DateTo]])
                            // ->get();
                            ->sum('mfn_loan_collection.amount');
            }
          }
          else {
            // if without service charge
            $MemberInfos = DB::table('mfn_loan')
                          ->select('mfn_member_information.id as MemberID', 'mfn_member_information.name as MemberName', 'mfn_member_information.code as MemberCode', 'mfn_samity.id as SamityID', 'mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.branchIdFk', 'mfn_loan.loanAmount as totalRepayAmount')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                          ->where([['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.productIdFk', $ProductId]])
                          ->get();

                          // dd($MemberInfos);

            foreach ($MemberInfos as $key => $MemberInfo) {
              $MemberID = $MemberInfo->MemberID;

              $ScheduleGeneralInfos[$MemberID]  = DB::table('mfn_loan')
                            ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_schedule.scheduleDate', 'mfn_loan_schedule.principalAmount', 'mfn_loan.status')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.productIdFk', $ProductId], ['mfn_loan_schedule.scheduleDate', '>=', $DateFrom], ['mfn_loan_schedule.scheduleDate', '<=', $DateTo]])
                            ->orderByRaw('mfn_loan_schedule.scheduleDate DESC')
                            ->limit(1)
                            ->get();
                            // ->sum('mfn_loan_schedule.installmentAmount');

              $CollectionGeneralInfos[$MemberID] = DB::table('mfn_loan')
                            ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_collection.collectionDate', 'mfn_loan_collection.principalAmount as amount')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.productIdFk', $ProductId], ['mfn_loan_collection.amount', '>', 0], ['mfn_loan_collection.collectionDate', '>=', $DateFrom], ['mfn_loan_collection.collectionDate', '<=', $DateTo]])
                            ->orderByRaw('mfn_loan_collection.collectionDate DESC')
                            ->limit(1)
                            ->get();
                            // ->sum('mfn_loan_collection.amount');

              $ScheduleInfos[$MemberID] = DB::table('mfn_loan')
                            // ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_schedule.scheduleDate', 'mfn_loan_schedule.installmentAmount')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.productIdFk', $ProductId], ['mfn_loan_schedule.scheduleDate', '>=', $DateFrom], ['mfn_loan_schedule.scheduleDate', '<=', $DateTo]])
                            // ->get();
                            ->sum('mfn_loan_schedule.principalAmount');

              $CollectionInfos[$MemberID] = DB::table('mfn_loan')
                            // ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_collection.collectionDate', 'mfn_loan_collection.amount')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.productIdFk', $ProductId], ['mfn_loan_collection.collectionDate', '>=', $DateFrom], ['mfn_loan_collection.collectionDate', '<=', $DateTo]])
                            // ->get();
                            ->sum('mfn_loan_collection.principalAmount');
            }
          }
        }
      }
      else {
        // if a single samity selected
        if ($ProductId == 'All') {
          // if all product selected
          if ($SearchType == 'WithServiceCharge') {
            // if with service charge
            $MemberInfos = DB::table('mfn_loan')
                          ->select('mfn_member_information.id as MemberID', 'mfn_member_information.name as MemberName', 'mfn_member_information.code as MemberCode', 'mfn_samity.id as SamityID', 'mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.branchIdFk', 'mfn_loan.totalRepayAmount')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                          ->where([['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId]])
                          ->get();

            foreach ($MemberInfos as $key => $MemberInfo) {
              $MemberID = $MemberInfo->MemberID;

              $ScheduleGeneralInfos[$MemberID]  = DB::table('mfn_loan')
                            ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_schedule.scheduleDate', 'mfn_loan_schedule.installmentAmount', 'mfn_loan.status')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId], ['mfn_loan_schedule.scheduleDate', '>=', $DateFrom], ['mfn_loan_schedule.scheduleDate', '<=', $DateTo]])
                            ->orderByRaw('mfn_loan_schedule.scheduleDate DESC')
                            ->limit(1)
                            ->get();
                            // ->sum('mfn_loan_schedule.installmentAmount');

              $CollectionGeneralInfos[$MemberID] = DB::table('mfn_loan')
                            ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_collection.collectionDate', 'mfn_loan_collection.amount')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId], ['mfn_loan_collection.amount', '>', 0], ['mfn_loan_collection.collectionDate', '>=', $DateFrom], ['mfn_loan_collection.collectionDate', '<=', $DateTo]])
                            ->orderByRaw('mfn_loan_collection.collectionDate DESC')
                            ->limit(1)
                            ->get();
                            // ->sum('mfn_loan_collection.amount');

              $ScheduleInfos[$MemberID] = DB::table('mfn_loan')
                            // ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_schedule.scheduleDate', 'mfn_loan_schedule.installmentAmount')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId], ['mfn_loan_schedule.scheduleDate', '>=', $DateFrom], ['mfn_loan_schedule.scheduleDate', '<=', $DateTo]])
                            // ->get();
                            ->sum('mfn_loan_schedule.installmentAmount');

              $CollectionInfos[$MemberID] = DB::table('mfn_loan')
                            // ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_collection.collectionDate', 'mfn_loan_collection.amount')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId], ['mfn_loan_collection.collectionDate', '>=', $DateFrom], ['mfn_loan_collection.collectionDate', '<=', $DateTo]])
                            // ->get();
                            ->sum('mfn_loan_collection.amount');
            }
          }
          else {
            // if without service charge
            $MemberInfos = DB::table('mfn_loan')
                          ->select('mfn_member_information.id as MemberID', 'mfn_member_information.name as MemberName', 'mfn_member_information.code as MemberCode', 'mfn_samity.id as SamityID', 'mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.branchIdFk', 'mfn_loan.loanAmount as totalRepayAmount')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                          ->where([['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId]])
                          ->get();

            foreach ($MemberInfos as $key => $MemberInfo) {
              $MemberID = $MemberInfo->MemberID;

              $ScheduleGeneralInfos[$MemberID]  = DB::table('mfn_loan')
                            ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_schedule.scheduleDate', 'mfn_loan_schedule.principalAmount', 'mfn_loan.status')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId], ['mfn_loan_schedule.scheduleDate', '>=', $DateFrom], ['mfn_loan_schedule.scheduleDate', '<=', $DateTo]])
                            ->orderByRaw('mfn_loan_schedule.scheduleDate DESC')
                            ->limit(1)
                            ->get();
                            // ->sum('mfn_loan_schedule.installmentAmount');

              $CollectionGeneralInfos[$MemberID] = DB::table('mfn_loan')
                            ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_collection.collectionDate', 'mfn_loan_collection.principalAmount as amount')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId], ['mfn_loan_collection.amount', '>', 0], ['mfn_loan_collection.collectionDate', '>=', $DateFrom], ['mfn_loan_collection.collectionDate', '<=', $DateTo]])
                            ->orderByRaw('mfn_loan_collection.collectionDate DESC')
                            ->limit(1)
                            ->get();
                            // ->sum('mfn_loan_collection.amount');

              $ScheduleInfos[$MemberID] = DB::table('mfn_loan')
                            // ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_schedule.scheduleDate', 'mfn_loan_schedule.installmentAmount')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId], ['mfn_loan_schedule.scheduleDate', '>=', $DateFrom], ['mfn_loan_schedule.scheduleDate', '<=', $DateTo]])
                            // ->get();
                            ->sum('mfn_loan_schedule.principalAmount');

              $CollectionInfos[$MemberID] = DB::table('mfn_loan')
                            // ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_collection.collectionDate', 'mfn_loan_collection.amount')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId], ['mfn_loan_collection.collectionDate', '>=', $DateFrom], ['mfn_loan_collection.collectionDate', '<=', $DateTo]])
                            // ->get();
                            ->sum('mfn_loan_collection.principalAmount');
            }
          }
        }
        else {
          // if a single product selected
          if ($SearchType == 'WithServiceCharge') {
            // if with service charge
            $MemberInfos = DB::table('mfn_loan')
                          ->select('mfn_member_information.id as MemberID', 'mfn_member_information.name as MemberName', 'mfn_member_information.code as MemberCode', 'mfn_samity.id as SamityID', 'mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.branchIdFk', 'mfn_loan.totalRepayAmount')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                          ->where([['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId], ['mfn_loan.productIdFk', $ProductId]])
                          ->get();

            foreach ($MemberInfos as $key => $MemberInfo) {
              $MemberID = $MemberInfo->MemberID;

              $ScheduleGeneralInfos[$MemberID]  = DB::table('mfn_loan')
                            ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_schedule.scheduleDate', 'mfn_loan_schedule.installmentAmount', 'mfn_loan.status')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId], ['mfn_loan.productIdFk', $ProductId], ['mfn_loan_schedule.scheduleDate', '>=', $DateFrom], ['mfn_loan_schedule.scheduleDate', '<=', $DateTo]])
                            ->orderByRaw('mfn_loan_schedule.scheduleDate DESC')
                            ->limit(1)
                            ->get();
                            // ->sum('mfn_loan_schedule.installmentAmount');

              $CollectionGeneralInfos[$MemberID] = DB::table('mfn_loan')
                            ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_collection.collectionDate', 'mfn_loan_collection.amount')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId], ['mfn_loan.productIdFk', $ProductId], ['mfn_loan_collection.amount', '>', 0], ['mfn_loan_collection.collectionDate', '>=', $DateFrom], ['mfn_loan_collection.collectionDate', '<=', $DateTo]])
                            ->orderByRaw('mfn_loan_collection.collectionDate DESC')
                            ->limit(1)
                            ->get();
                            // ->sum('mfn_loan_collection.amount');

              $ScheduleInfos[$MemberID] = DB::table('mfn_loan')
                            // ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_schedule.scheduleDate', 'mfn_loan_schedule.installmentAmount')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId], ['mfn_loan.productIdFk', $ProductId], ['mfn_loan_schedule.scheduleDate', '>=', $DateFrom], ['mfn_loan_schedule.scheduleDate', '<=', $DateTo]])
                            // ->get();
                            ->sum('mfn_loan_schedule.installmentAmount');

              $CollectionInfos[$MemberID] = DB::table('mfn_loan')
                            // ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_collection.collectionDate', 'mfn_loan_collection.amount')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId], ['mfn_loan.productIdFk', $ProductId], ['mfn_loan_collection.collectionDate', '>=', $DateFrom], ['mfn_loan_collection.collectionDate', '<=', $DateTo]])
                            // ->get();
                            ->sum('mfn_loan_collection.amount');
            }
          }
          else {
            // if without service charge
            $MemberInfos = DB::table('mfn_loan')
                          ->select('mfn_member_information.id as MemberID', 'mfn_member_information.name as MemberName', 'mfn_member_information.code as MemberCode', 'mfn_samity.id as SamityID', 'mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.branchIdFk', 'mfn_loan.loanAmount as totalRepayAmount')
                          ->join('mfn_member_information', 'mfn_loan.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                          ->where([['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId], ['mfn_loan.productIdFk', $ProductId]])
                          ->get();

            foreach ($MemberInfos as $key => $MemberInfo) {
              $MemberID = $MemberInfo->MemberID;

              $ScheduleGeneralInfos[$MemberID]  = DB::table('mfn_loan')
                            ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_schedule.scheduleDate', 'mfn_loan_schedule.principalAmount', 'mfn_loan.status')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId], ['mfn_loan.productIdFk', $ProductId], ['mfn_loan_schedule.scheduleDate', '>=', $DateFrom], ['mfn_loan_schedule.scheduleDate', '<=', $DateTo]])
                            ->orderByRaw('mfn_loan_schedule.scheduleDate DESC')
                            ->limit(1)
                            ->get();
                            // ->sum('mfn_loan_schedule.installmentAmount');

              $CollectionGeneralInfos[$MemberID] = DB::table('mfn_loan')
                            ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_collection.collectionDate', 'mfn_loan_collection.principalAmount as amount')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId], ['mfn_loan.productIdFk', $ProductId], ['mfn_loan_collection.amount', '>', 0], ['mfn_loan_collection.collectionDate', '>=', $DateFrom], ['mfn_loan_collection.collectionDate', '<=', $DateTo]])
                            ->orderByRaw('mfn_loan_collection.collectionDate DESC')
                            ->limit(1)
                            ->get();
                            // ->sum('mfn_loan_collection.amount');

              $ScheduleInfos[$MemberID] = DB::table('mfn_loan')
                            // ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_schedule.scheduleDate', 'mfn_loan_schedule.installmentAmount')
                            ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId], ['mfn_loan.productIdFk', $ProductId], ['mfn_loan_schedule.scheduleDate', '>=', $DateFrom], ['mfn_loan_schedule.scheduleDate', '<=', $DateTo]])
                            // ->get();
                            ->sum('mfn_loan_schedule.principalAmount');

              $CollectionInfos[$MemberID] = DB::table('mfn_loan')
                            // ->select('mfn_samity.name as SamityName', 'mfn_samity.code as SamityCode', 'mfn_loan.loanCode', 'mfn_loan.memberIdFk', 'mfn_loan_collection.collectionDate', 'mfn_loan_collection.amount')
                            ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                            ->join('mfn_samity', 'mfn_loan.samityIdfk', '=', 'mfn_samity.id')
                            ->where([['mfn_loan.memberIdFk', $MemberID], ['mfn_loan.branchIdFk', $BranchId], ['mfn_loan.samityIdfk', $SamityId], ['mfn_loan.productIdFk', $ProductId], ['mfn_loan_collection.collectionDate', '>=', $DateFrom], ['mfn_loan_collection.collectionDate', '<=', $DateTo]])
                            // ->get();
                            ->sum('mfn_loan_collection.principalAmount');
            }
          }
        }
      }
    }

    $openingLoanPaidAmount = DB::table('mfn_opening_balance_loan')
                             ->join('mfn_loan', 'mfn_loan.id', '=', 'mfn_opening_balance_loan.loanIdFk')
                             ->where('mfn_opening_balance_loan.softDel', 0)
                             ->select('mfn_opening_balance_loan.loanIdFk', 
                                      'mfn_opening_balance_loan.paidLoanAmountOB', 
                                      'mfn_loan.memberIdFk', 
                                      'mfn_loan.samityIdfk')
                             ->get();
    // dd($MemberInfos);
    // dd($ScheduleGeneralInfos);
    // dd($CollectionGeneralInfos);
    // dd($ScheduleInfos);
    // dd($openingLoanPaidAmount);

    return view('microfin.reports.dueCollectionRegister.DueCollectionRegisterReport', compact('BranchInfos', 'BranchId', 'ProductInfos', 'ProductId', 'MemberInfos',
    'ScheduleInfos', 'CollectionInfos', 'ScheduleGeneralInfos', 'CollectionGeneralInfos', 'DateFrom', 'DateTo', 'SamityId', 'SamityInfos', 'openingLoanPaidAmount'));
  }

}
