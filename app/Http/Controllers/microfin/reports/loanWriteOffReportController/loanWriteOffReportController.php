<?php

namespace App\Http\Controllers\microfin\reports\loanWriteOffReportController;

use App\Http\Controllers\Controller;
use DB;
use App\Http\Requests;
use Response;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
use Auth;
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
class loanWriteOffReportController extends Controller
{
  public function index(){
    $userBranchId=Auth::user()->branchId;

    $branchList = DB::table('gnr_branch');

    if($userBranchId == 1){
      $BranchDatas = $branchList->get();
    }
    else{
      $BranchDatas = $branchList->where('id', $userBranchId)->get();
    }
    $ProductDatas = DB::table('mfn_loans_product')->where('mfn_loans_product.isPrimaryProduct', '=', 1)->get();

    return view("microfin.reports.loanWriteOffReportViews.LoanWriteOffReportForm", compact('BranchDatas', 'ProductDatas', 'userBranchId'));
  }

  public function viewReport(Request $request){
    $userBranchId=Auth::user()->branchId;

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

    if ($RequestBranch == Null || $RequestBranch == '') {
      $BranchInfos = DB::table('gnr_branch')
      ->where('id', $userBranchId)
      ->get();
    }

    elseif ($RequestBranch != 'All') {
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
    elseif ($RequestProduct == 'All') {
      $ProductInfos = DB::table('mfn_loans_product')
      ->get();
    }

    $OldDate = DB::select("SELECT date FROM `mfn_loan_write_off` WHERE softDel=0 ORDER BY date ASC LIMIT 1");
    // dd($OldDate);

    if($RequestBranch == '' || $RequestBranch == Null){
      if ($RequestProduct == 'All') {
        if ($request->txtDate1 == '') {
          $MemberInfos = DB::table('mfn_loan_write_off')
          ->join('mfn_member_information', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_member_information.id')
          ->join('mfn_loan', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_loan.memberIdFk')
          ->join('mfn_samity', 'mfn_loan_write_off.samityIdFk', '=', 'mfn_samity.id')
          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')

          ->select('mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_write_off.date as write_off_date', 'mfn_loan.loanAmount', 'mfn_loan_write_off.amount as write_off_amount', 'mfn_loan_write_off.principalAmount as principal_amount', 'mfn_loan_write_off.interestAmount as interest_amount', 'mfn_loan_write_off.branchIdFk')

          ->where('mfn_loan_write_off.branchIdFk', '=', $userBranchId)
          ->where('mfn_loan_write_off.date', '<=', $RequestDateToUpdated)
          ->where('mfn_loan_write_off.softDel', 0)
          // ->where('mfn_loan_write_off.isDeath', '=', 1)
          ->get();
        }
        elseif ($request->txtDate1 != '') {
          $MemberInfos = DB::table('mfn_loan_write_off')
          ->join('mfn_member_information', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_member_information.id')
          ->join('mfn_loan', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_loan.memberIdFk')
          ->join('mfn_samity', 'mfn_loan_write_off.samityIdFk', '=', 'mfn_samity.id')
          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')

          ->select('mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_write_off.date as write_off_date', 'mfn_loan.loanAmount', 'mfn_loan_write_off.amount as write_off_amount', 'mfn_loan_write_off.principalAmount as principal_amount', 'mfn_loan_write_off.interestAmount as interest_amount', 'mfn_loan_write_off.branchIdFk')

          ->where('mfn_loan_write_off.branchIdFk', '=', $userBranchId)
          ->where('mfn_loan_write_off.date', '>=', $RequestDateFromUpdated)
          ->where('mfn_loan_write_off.date', '<=', $RequestDateToUpdated)
          ->where('mfn_loan_write_off.softDel', 0)
          // ->where('mfn_loan_write_off.isDeath', '=', 1)
          ->get();
        }
      }


      elseif ($RequestProduct != 'All') {
        if ($request->txtDate1 == '') {
          $MemberInfos = DB::table('mfn_loan_write_off')
          ->join('mfn_member_information', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_member_information.id')
          ->join('mfn_loan', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_loan.memberIdFk')
          ->join('mfn_samity', 'mfn_loan_write_off.samityIdFk', '=', 'mfn_samity.id')
          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')

          ->select('mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_write_off.date as write_off_date', 'mfn_loan.loanAmount', 'mfn_loan_write_off.amount as write_off_amount', 'mfn_loan_write_off.principalAmount as principal_amount', 'mfn_loan_write_off.interestAmount as interest_amount', 'mfn_loan.branchIdFk')

          ->where('mfn_loan_write_off.branchIdFk', '=', $userBranchId)
          ->where('mfn_loan.productIdFk', $RequestProduct)
          ->where('mfn_loan_write_off.date', '<=', $RequestDateToUpdated)
          ->where('mfn_loan_write_off.softDel', 0)
          ->get();
        }
        elseif ($request->txtDate1 != '') {
          $MemberInfos = DB::table('mfn_loan_write_off')
          ->join('mfn_member_information', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_member_information.id')
          ->join('mfn_loan', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_loan.memberIdFk')
          ->join('mfn_samity', 'mfn_loan_write_off.samityIdFk', '=', 'mfn_samity.id')
          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')

          ->select('mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_write_off.date as write_off_date', 'mfn_loan.loanAmount', 'mfn_loan_write_off.amount as write_off_amount', 'mfn_loan_write_off.principalAmount as principal_amount', 'mfn_loan_write_off.interestAmount as interest_amount', 'mfn_loan.branchIdFk')

          ->where('mfn_loan_write_off.branchIdFk', '=', $userBranchId)
          ->where('mfn_loan.productIdFk', $RequestProduct)
          ->where('mfn_loan_write_off.date', '>=', $RequestDateFromUpdated)
          ->where('mfn_loan_write_off.date', '<=', $RequestDateToUpdated)
          ->where('mfn_loan_write_off.softDel', 0)
          ->get();
        }
      }

    }

    elseif ($RequestBranch == 'All') {
      if ($RequestProduct == 'All') {
        if ($request->txtDate1 == '') {
          $MemberInfos = DB::table('mfn_loan_write_off')
          ->join('mfn_member_information', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_member_information.id')
          ->join('mfn_loan', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_loan.memberIdFk')
          ->join('mfn_samity', 'mfn_loan_write_off.samityIdFk', '=', 'mfn_samity.id')
          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')

          ->select('mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_write_off.date as write_off_date', 'mfn_loan.loanAmount', 'mfn_loan_write_off.amount as write_off_amount', 'mfn_loan_write_off.principalAmount as principal_amount', 'mfn_loan_write_off.interestAmount as interest_amount', 'mfn_loan.branchIdFk')

          ->where('mfn_loan_write_off.date', '<=', $RequestDateToUpdated)
          ->where('mfn_loan_write_off.softDel', 0)
          ->get();
        }
        elseif ($request->txtDate1 != '') {
          $MemberInfos = DB::table('mfn_loan_write_off')
          ->join('mfn_member_information', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_member_information.id')
          ->join('mfn_loan', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_loan.memberIdFk')
          ->join('mfn_samity', 'mfn_loan_write_off.samityIdFk', '=', 'mfn_samity.id')
          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')

          ->select('mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_write_off.date as write_off_date', 'mfn_loan.loanAmount', 'mfn_loan_write_off.amount as write_off_amount', 'mfn_loan_write_off.principalAmount as principal_amount', 'mfn_loan_write_off.interestAmount as interest_amount', 'mfn_loan.branchIdFk')

          ->where('mfn_loan_write_off.date', '>=', $RequestDateFromUpdated)
          ->where('mfn_loan_write_off.date', '<=', $RequestDateToUpdated)
          ->where('mfn_loan_write_off.softDel', 0)
          ->get();
        }
      }
      elseif ($RequestProduct != 'All') {
        if ($request->txtDate1 == '') {
          $MemberInfos = DB::table('mfn_loan_write_off')
          ->join('mfn_member_information', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_member_information.id')
          ->join('mfn_loan', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_loan.memberIdFk')
          ->join('mfn_samity', 'mfn_loan_write_off.samityIdFk', '=', 'mfn_samity.id')
          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')

          ->select('mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_write_off.date as write_off_date', 'mfn_loan.loanAmount', 'mfn_loan_write_off.amount as write_off_amount', 'mfn_loan_write_off.principalAmount as principal_amount', 'mfn_loan_write_off.interestAmount as interest_amount', 'mfn_loan.branchIdFk')

          ->where('mfn_loan.productIdFk', $RequestProduct)
          ->where('mfn_loan_write_off.date', '<=', $RequestDateToUpdated)
          ->where('mfn_loan_write_off.softDel', 0)
          ->get();
        }
        elseif ($request->txtDate1 != '') {
          $MemberInfos = DB::table('mfn_loan_write_off')
          ->join('mfn_member_information', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_member_information.id')
          ->join('mfn_loan', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_loan.memberIdFk')
          ->join('mfn_samity', 'mfn_loan_write_off.samityIdFk', '=', 'mfn_samity.id')
          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')

          ->select('mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_write_off.date as write_off_date', 'mfn_loan.loanAmount', 'mfn_loan_write_off.amount as write_off_amount', 'mfn_loan_write_off.principalAmount as principal_amount', 'mfn_loan_write_off.interestAmount as interest_amount', 'mfn_loan.branchIdFk')

          ->where('mfn_loan.productIdFk', $RequestProduct)
          ->where('mfn_loan_write_off.date', '>=', $RequestDateFromUpdated)
          ->where('mfn_loan_write_off.date', '<=', $RequestDateToUpdated)
          ->where('mfn_loan_write_off.softDel', 0)
          ->get();
        }
      }
    }
    elseif ($RequestBranch != 'All') {
      if ($RequestProduct == 'All') {
        if ($request->txtDate1 == '') {
          $MemberInfos = DB::table('mfn_loan_write_off')
          ->join('mfn_member_information', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_member_information.id')
          ->join('mfn_loan', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_loan.memberIdFk')
          ->join('mfn_samity', 'mfn_loan_write_off.samityIdFk', '=', 'mfn_samity.id')
          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')

          ->select('mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_write_off.date as write_off_date', 'mfn_loan.loanAmount', 'mfn_loan_write_off.amount as write_off_amount', 'mfn_loan_write_off.principalAmount as principal_amount', 'mfn_loan_write_off.interestAmount as interest_amount', 'mfn_loan_write_off.branchIdFk')

          ->where('mfn_loan_write_off.branchIdFk', '=', $RequestBranch)
          ->where('mfn_loan_write_off.date', '<=', $RequestDateToUpdated)
          ->where('mfn_loan_write_off.softDel', 0)
          ->get();
        }
        elseif ($request->txtDate1 != '') {
          $MemberInfos = DB::table('mfn_loan_write_off')
          ->join('mfn_member_information', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_member_information.id')
          ->join('mfn_loan', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_loan.memberIdFk')
          ->join('mfn_samity', 'mfn_loan_write_off.samityIdFk', '=', 'mfn_samity.id')
          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')

          ->select('mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_write_off.date as write_off_date', 'mfn_loan.loanAmount', 'mfn_loan_write_off.amount as write_off_amount', 'mfn_loan_write_off.principalAmount as principal_amount', 'mfn_loan_write_off.interestAmount as interest_amount', 'mfn_loan_write_off.branchIdFk')

          ->where('mfn_loan_write_off.branchIdFk', '=', $RequestBranch)
          ->where('mfn_loan_write_off.date', '>=', $RequestDateFromUpdated)
          ->where('mfn_loan_write_off.date', '<=', $RequestDateToUpdated)
          ->where('mfn_loan_write_off.softDel', 0)
          ->get();
        }
      }


      elseif ($RequestProduct != 'All') {
        if ($request->txtDate1 == '') {
          $MemberInfos = DB::table('mfn_loan_write_off')
          ->join('mfn_member_information', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_member_information.id')
          ->join('mfn_loan', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_loan.memberIdFk')
          ->join('mfn_samity', 'mfn_loan_write_off.samityIdFk', '=', 'mfn_samity.id')
          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')

          ->select('mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_write_off.date as write_off_date', 'mfn_loan.loanAmount', 'mfn_loan_write_off.amount as write_off_amount', 'mfn_loan_write_off.principalAmount as principal_amount', 'mfn_loan_write_off.interestAmount as interest_amount', 'mfn_loan.branchIdFk')

          ->where('mfn_loan_write_off.branchIdFk', '=', $RequestBranch)
          ->where('mfn_loan.productIdFk', $RequestProduct)
          ->where('mfn_loan_write_off.date', '<=', $RequestDateToUpdated)
          ->where('mfn_loan_write_off.softDel', 0)
          ->get();
        }
        elseif ($request->txtDate1 != '') {
          $MemberInfos = DB::table('mfn_loan_write_off')
          ->join('mfn_member_information', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_member_information.id')
          ->join('mfn_loan', 'mfn_loan_write_off.memberIdFk', '=', 'mfn_loan.memberIdFk')
          ->join('mfn_samity', 'mfn_loan_write_off.samityIdFk', '=', 'mfn_samity.id')
          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')

          ->select('mfn_member_information.code as member_code', 'mfn_member_information.name as member_name', 'mfn_loan.loanCode', 'mfn_loans_product.shortName', 'mfn_samity.code as samity_code', 'mfn_samity.name as samity_name', 'mfn_loan_write_off.date as write_off_date', 'mfn_loan.loanAmount', 'mfn_loan_write_off.amount as write_off_amount', 'mfn_loan_write_off.principalAmount as principal_amount', 'mfn_loan_write_off.interestAmount as interest_amount', 'mfn_loan.branchIdFk')

          ->where('mfn_loan_write_off.branchIdFk', '=', $RequestBranch)
          ->where('mfn_loan.productIdFk', $RequestProduct)
          ->where('mfn_loan_write_off.date', '>=', $RequestDateFromUpdated)
          ->where('mfn_loan_write_off.date', '<=', $RequestDateToUpdated)
          ->where('mfn_loan_write_off.softDel', 0)
          ->get();
        }
      }
    }

    // dd($BranchInfos);

    return view("microfin.reports.loanWriteOffReportViews.LoanWriteOffReportView", compact('BranchInfos', 'RequestBranch', 'ProductInfos', 'RequestProduct', 'RequestDateFromUpdated', 'RequestDateToUpdated', 'MemberInfos', 'OldDate', 'RDF'));
  }

}
