<?php

namespace App\Http\Controllers\accounting\reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\accounting\AddVoucher;
use App\accounting\AddVoucherDetails;
use App\accounting\AddLedger;
use App\accounting\AddVoucherType;
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;
use App\gnr\GnrBranch;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;


class AccCashFlowStatementController extends Controller{

    public function cashFlowStatementView(){

        $projects=array(''=>"Select Project")+DB::table('gnr_project')->pluck('name','id')->toarray();
        $branchLists=array(''=>"Select Branch")+DB::table('gnr_branch')->pluck('name','id')->toarray();
        $fiscalYears=array(''=>"Select Fiscal Year")+DB::table('gnr_fiscal_year')->pluck('name','id')->toarray();

        $accCashFlowStatementArr= array(
          'projects'   => $projects,
          'branchLists' => $branchLists,
          'fiscalYears' => $fiscalYears
        );

        return view('accounting.reports.cashFlowStatement.viewCashflowStatement',$accCashFlowStatementArr);

    }

    public function cashFlowStatementProjectType(Request $request)
    {
      $projectTypes=DB::table('gnr_project_type')->select('name','id')->where('projectId',$request->projectId)->get();
      	return response()->json($projectTypes);
    }
    public function cashFlowStatementLoadTable(Request $request)
    {
      $user_company_id = Auth::user()->company_id_fk;
      $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
      $fiscalId = (int) $request->filFiscalYear;
      if($fiscalId==1)
      {
        $fiscalId2=$fiscalId;
      }
      else{
        $fiscalId2=$fiscalId-1;
      }
      $fiscalYearsSelected1=DB::table('gnr_fiscal_year')->where('id',$fiscalId)->select('name','id')->first();
      $fiscalYearsSelected2=DB::table('gnr_fiscal_year')->where('id',$fiscalId2)->select('name','id')->first();
      $cashFlowStatementLoadTableArr=array(
      'company' => $company,
      'fiscalYearsSelected1' => $fiscalYearsSelected1,
      'fiscalYearsSelected2' => $fiscalYearsSelected2
      );
      return view('accounting.reports.cashFlowStatement.viewCashflowStatementTable',$cashFlowStatementLoadTableArr);
    }

}
