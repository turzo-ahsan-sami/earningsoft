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

        $user_branch_id = Auth::user()->branchId;
        $user_company_id = Auth::user()->company_id_fk;
        $userBranch = Auth::user()->branch;
        $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');
        $user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectTypeId');


        //Project
        if ($request->projectId==null) {
            if ($userBranch->branchCode == 0) {
                $projectSelected = 1;
            }
            else{
                $projectSelected = $user_project_id;
            }
        }
        else{
            $projectSelected = (int) json_decode($request->projectId);
        }

        //Project Type
        if ($request->projectTypeId==null) {
            if ($userBranch->branchCode == 0) {
                $projectTypeSelected = null;
            }
            else{
                $projectTypeSelected = (int) json_decode($user_project_type_id);
            }
        }
        else{
            $projectTypeSelected = (int) json_decode($request->projectTypeId);
        }

        //Branch
        if ($request->branchId==null) {
            if ($userBranch->branchCode == 0) {
                $branchSelected = null;
            }
            else{
                $branchSelected = (int) json_decode($user_branch_id);
            }
        }
        else{
            $branchSelected = (int) json_decode($request->branchId);
        }



        $projects = DB::table('gnr_project')->get();
        $projectTypes = DB::table('gnr_project_type')->where('projectId',$projectSelected)->get();
        $branches = DB::table('gnr_branch')->where('projectId',$projectSelected)->orWhere('id',1)->get();
        $fiscalYears = DB::table('gnr_fiscal_year')->orderBy('name', 'desc')->pluck('name','id');


        $searchedProjectId=$request->projectId;
        $searchedProjectTypeId=$request->projectTypeId;
        $searchedBranchId=$request->branchId;
        $searchedDepthLevel=$request->depthLevel;
        $searchedRoundUp=$request->roundUp;
        $searchedWithZero=$request->withZero;
        $searchedSearchMethod=$request->searchMethod;
        $searchedFiscalYearId=$request->fiscalYearId;
        $searchedDateFrom=$request->dateFrom;
        $searchedDateTo=$request->dateTo;

        $cashFlowArr= array(
          'user_branch_id'       => $user_branch_id,
          'user_company_id'      => $user_company_id,
          'user_project_id'      => $user_project_id,
          'user_project_type_id' => $user_project_type_id,
          'projects'             => $projects,
          'projectTypes'         => $projectTypes,
          'branches'             => $branches,
          'fiscalYears'          => $fiscalYears,
          'searchedProjectId'    => $searchedProjectId,
          'searchedProjectTypeId' => $searchedProjectTypeId,
          'searchedBranchId'      => $searchedBranchId,
          'searchedDepthLevel'    => $searchedDepthLevel,
          'searchedRoundUp'       => $searchedRoundUp,
          'searchedWithZero'      => $searchedWithZero,
          'searchedSearchMethod'  => $searchedSearchMethod,
          'searchedFiscalYearId'  => $searchedFiscalYearId,
          'searchedDateFrom'      => $searchedDateFrom,
          'searchedDateTo'        => $searchedDateTo,
          'projectSelected'       => $projectSelected,
          'projectTypeSelected'   => $projectTypeSelected,
          'branchSelected'        => $branchSelected
        );

        return view('accounting.reports.cashFlowStatement.viewCashflowStatement',$cashFlowArr);

    }

}       //End AccLedgerReportsController
