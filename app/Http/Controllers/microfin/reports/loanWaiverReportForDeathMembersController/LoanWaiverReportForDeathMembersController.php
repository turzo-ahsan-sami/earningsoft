<?php

namespace App\Http\Controllers\microfin\reports\loanWaiverReportForDeathMembersController;

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
class LoanWaiverReportForDeathMembersController extends Controller
{
  public function getBranchName(){
    $BranchDatas = DB::table('gnr_branch')->get();
    $ProductDatas = DB::table('mfn_loans_product')->get();

    return view("microfin.reports.loanWaiverReportForDeathMembersViews.LoanWaiverReportForDeathMembersForm", compact('BranchDatas', 'ProductDatas'));
  }

  public function getReport(Request $request){
    $RequestBranch = $request->searchBranch;
    $RequestProduct = $request->searchProduct;
    $RDF = $request->txtDate1;
    $RequestDateFrom = date_create($request->txtDate1);
    $RequestDateTo = date_create($request->txtDate2);
    $RequestDateToUpdated = date_format($RequestDateTo, 'Y-m-d');
    $RequestDateFromUpdated = date_format($RequestDateFrom, 'Y-m-d');
    // dd($request->txtDate1);

    $BranchInfos = array();
    $MemberInfos = array();

    if ($RequestBranch != 'All') {
      $BranchInfos = DB::table('gnr_branch')
                    ->where('id', $RequestBranch)
                    ->get();
    }
    elseif ($RequestBranch == 'All') {
      $BranchInfos = DB::table('gnr_branch')
                    ->get();
    }

    if ($RequestProduct != 'All') {
      $ProductInfos = DB::table('mfn_loans_product')
                    ->where('id', $RequestProduct)
                    ->get();
    }

    $OldDate = DB::select("SELECT date FROM `mfn_loan_waivers` ORDER BY date ASC LIMIT 1");
    // dd($OldDate);

    if ($RequestBranch == 'All') {
      if ($RequestProduct == 'All') {
        if ($request->txtDate1 == '') {
          $MemberInfos = DB::table('mfn_loan_waivers')
                          ->select('mfn_member_information.id', 'mfn_loan.id as loanId','mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_waivers.date as waiver_date', 'mfn_loan.loanAmount', 'mfn_loan_waivers.amount as waiver_amount', 'mfn_loan.branchIdFk')
                          ->join('mfn_member_information', 'mfn_loan_waivers.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_loan', 'mfn_loan_waivers.memberIdFk', '=', 'mfn_loan.memberIdFk')
                          ->join('mfn_samity', 'mfn_loan_waivers.samityIdFk', '=', 'mfn_samity.id')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', 'mfn_loans_product.id')
                          // ->where('mfn_loan_waivers.branchIdFk', $RequestBranch)
                          // ->where('mfn_loan.productIdFk', $RequestProduct)
                          // ->where('mfn_loan_waivers.date', '>=', $RequestDateFromUpdated)
                          ->where('mfn_loan_waivers.date', '<=', $RequestDateToUpdated)
                          ->where('mfn_loan_waivers.isForDeath', '=', 1)
                          ->get();
        }
        elseif ($request->txtDate1 != '') {
          $MemberInfos = DB::table('mfn_loan_waivers')
                          ->select('mfn_member_information.id', 'mfn_loan.id as loanId','mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_waivers.date as waiver_date', 'mfn_loan.loanAmount', 'mfn_loan_waivers.amount as waiver_amount', 'mfn_loan.branchIdFk')
                          ->join('mfn_member_information', 'mfn_loan_waivers.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_loan', 'mfn_loan_waivers.memberIdFk', '=', 'mfn_loan.memberIdFk')
                          ->join('mfn_samity', 'mfn_loan_waivers.samityIdFk', '=', 'mfn_samity.id')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', 'mfn_loans_product.id')
                          // ->where('mfn_loan_waivers.branchIdFk', $RequestBranch)
                          // ->where('mfn_loan.productIdFk', $RequestProduct)
                          ->where('mfn_loan_waivers.date', '>=', $RequestDateFromUpdated)
                          ->where('mfn_loan_waivers.date', '<=', $RequestDateToUpdated)
                          ->where('mfn_loan_waivers.isForDeath', '=', 1)
                          ->get();
        }
      }
      elseif ($RequestProduct != 'All') {
        if ($request->txtDate1 == '') {
          $MemberInfos = DB::table('mfn_loan_waivers')
                          ->select('mfn_member_information.id', 'mfn_loan.id as loanId','mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_waivers.date as waiver_date', 'mfn_loan.loanAmount', 'mfn_loan_waivers.amount as waiver_amount', 'mfn_loan.branchIdFk')
                          ->join('mfn_member_information', 'mfn_loan_waivers.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_loan', 'mfn_loan_waivers.memberIdFk', '=', 'mfn_loan.memberIdFk')
                          ->join('mfn_samity', 'mfn_loan_waivers.samityIdFk', '=', 'mfn_samity.id')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', 'mfn_loans_product.id')
                          // ->where('mfn_loan_waivers.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.productIdFk', $RequestProduct)
                          // ->where('mfn_loan_waivers.date', '>=', $RequestDateFromUpdated)
                          ->where('mfn_loan_waivers.date', '<=', $RequestDateToUpdated)
                          ->where('mfn_loan_waivers.isForDeath', '=', 1)
                          ->get();
        }
        elseif ($request->txtDate1 != '') {
          $MemberInfos = DB::table('mfn_loan_waivers')
                          ->select('mfn_member_information.id', 'mfn_loan.id as loanId','mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_waivers.date as waiver_date', 'mfn_loan.loanAmount', 'mfn_loan_waivers.amount as waiver_amount', 'mfn_loan.branchIdFk')
                          ->join('mfn_member_information', 'mfn_loan_waivers.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_loan', 'mfn_loan_waivers.memberIdFk', '=', 'mfn_loan.memberIdFk')
                          ->join('mfn_samity', 'mfn_loan_waivers.samityIdFk', '=', 'mfn_samity.id')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', 'mfn_loans_product.id')
                          // ->where('mfn_loan_waivers.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.productIdFk', $RequestProduct)
                          ->where('mfn_loan_waivers.date', '>=', $RequestDateFromUpdated)
                          ->where('mfn_loan_waivers.date', '<=', $RequestDateToUpdated)
                          ->where('mfn_loan_waivers.isForDeath', '=', 1)
                          ->get();
        }
      }
    }
    elseif ($RequestBranch != 'All') {
      if ($RequestProduct == 'All') {
        if ($request->txtDate1 == '') {
          $MemberInfos = DB::table('mfn_loan_waivers')
                          ->select('mfn_member_information.id', 'mfn_loan.id as loanId','mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_waivers.date as waiver_date', 'mfn_loan.loanAmount', 'mfn_loan_waivers.amount as waiver_amount', 'mfn_loan.branchIdFk')
                          ->join('mfn_member_information', 'mfn_loan_waivers.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_loan', 'mfn_loan_waivers.memberIdFk', '=', 'mfn_loan.memberIdFk')
                          ->join('mfn_samity', 'mfn_loan_waivers.samityIdFk', '=', 'mfn_samity.id')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', 'mfn_loans_product.id')
                          ->where('mfn_loan_waivers.branchIdFk', $RequestBranch)
                          // ->where('mfn_loan.productIdFk', $RequestProduct)
                          // ->where('mfn_loan_waivers.date', '>=', $RequestDateFromUpdated)
                          ->where('mfn_loan_waivers.date', '<=', $RequestDateToUpdated)
                          ->where('mfn_loan_waivers.isForDeath', '=', 1)
                          ->get();
        }
        elseif ($request->txtDate1 != '') {
          $MemberInfos = DB::table('mfn_loan_waivers')
                          ->select('mfn_member_information.id', 'mfn_loan.id as loanId','mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_waivers.date as waiver_date', 'mfn_loan.loanAmount', 'mfn_loan_waivers.amount as waiver_amount', 'mfn_loan.branchIdFk')
                          ->join('mfn_member_information', 'mfn_loan_waivers.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_loan', 'mfn_loan_waivers.memberIdFk', '=', 'mfn_loan.memberIdFk')
                          ->join('mfn_samity', 'mfn_loan_waivers.samityIdFk', '=', 'mfn_samity.id')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', 'mfn_loans_product.id')
                          ->where('mfn_loan_waivers.branchIdFk', $RequestBranch)
                          // ->where('mfn_loan.productIdFk', $RequestProduct)
                          ->where('mfn_loan_waivers.date', '>=', $RequestDateFromUpdated)
                          ->where('mfn_loan_waivers.date', '<=', $RequestDateToUpdated)
                          ->where('mfn_loan_waivers.isForDeath', '=', 1)
                          ->get();
        }
      }
      elseif ($RequestProduct != 'All') {
        if ($request->txtDate1 == '') {
          $MemberInfos = DB::table('mfn_loan_waivers')
                          ->select('mfn_member_information.id', 'mfn_loan.id as loanId','mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_waivers.date as waiver_date', 'mfn_loan.loanAmount', 'mfn_loan_waivers.amount as waiver_amount', 'mfn_loan.branchIdFk')
                          ->join('mfn_member_information', 'mfn_loan_waivers.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_loan', 'mfn_loan_waivers.memberIdFk', '=', 'mfn_loan.memberIdFk')
                          ->join('mfn_samity', 'mfn_loan_waivers.samityIdFk', '=', 'mfn_samity.id')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', 'mfn_loans_product.id')
                          ->where('mfn_loan_waivers.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.productIdFk', $RequestProduct)
                          // ->where('mfn_loan_waivers.date', '>=', $RequestDateFromUpdated)
                          ->where('mfn_loan_waivers.date', '<=', $RequestDateToUpdated)
                          ->where('mfn_loan_waivers.isForDeath', '=', 1)
                          ->get();
        }
        elseif ($request->txtDate1 != '') {
          $MemberInfos = DB::table('mfn_loan_waivers')
                          ->select('mfn_member_information.id', 'mfn_loan.id as loanId','mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_waivers.date as waiver_date', 'mfn_loan.loanAmount', 'mfn_loan_waivers.amount as waiver_amount', 'mfn_loan.branchIdFk')
                          ->join('mfn_member_information', 'mfn_loan_waivers.memberIdFk', '=', 'mfn_member_information.id')
                          ->join('mfn_loan', 'mfn_loan_waivers.memberIdFk', '=', 'mfn_loan.memberIdFk')
                          ->join('mfn_samity', 'mfn_loan_waivers.samityIdFk', '=', 'mfn_samity.id')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', 'mfn_loans_product.id')
                          ->where('mfn_loan_waivers.branchIdFk', $RequestBranch)
                          ->where('mfn_loan.productIdFk', $RequestProduct)
                          ->where('mfn_loan_waivers.date', '>=', $RequestDateFromUpdated)
                          ->where('mfn_loan_waivers.date', '<=', $RequestDateToUpdated)
                          ->where('mfn_loan_waivers.isForDeath', '=', 1)
                          ->get();
        }
      }
    }


    return view("microfin.reports.loanWaiverReportForDeathMembersViews.LoanWaiverReportForDeathMembersReport", compact('BranchInfos', 'RequestBranch', 'ProductInfos', 'RequestProduct', 'RequestDateFromUpdated', 'RequestDateToUpdated', 'MemberInfos', 'OldDate', 'RDF', 'loanOpeningBalance'));
  }

}
