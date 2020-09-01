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

use App\Service\DatabasePartitionHelper;

class AccCapitalFundReportController extends Controller{

 	public function capitalFund(Request $request){
        
        $user_branch_id = Auth::user()->branchId;
        $user_company_id = Auth::user()->company_id_fk;
        $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');
        $user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectTypeId');

        //Project
        if ($request->projectId==null) {
            if ($user_branch_id == 1) {
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
            if ($user_branch_id == 1) {
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
            if ($user_branch_id == 1) {
                $branchSelected = null;
            }
            else{
                $branchSelected = (int) json_decode($user_branch_id);
            }
        }
        else{
            $branchSelected = (int) json_decode($request->branchId);
        }



        $projects = DB::table('gnr_project')->where('companyId', $user_company_id)->get();
        $branches = DB::table('gnr_branch')->where('id',$user_branch_id)->get();

        $projectTypes = DB::table('gnr_project_type')->where('companyId', $user_company_id)->where('projectId',$projectSelected)->get();
        $fiscalYears = DB::table('gnr_fiscal_year')->where('companyId', $user_company_id)->orderBy('name', 'desc')->pluck('name','id');

        
        $searchedProjectId = $request->projectId;
        $searchedProjectTypeId = $request->projectTypeId;
        $searchedBranchId = $request->branchId;
        $searchedDepthLevel = $request->depthLevel;
        $searchedRoundUp = $request->roundUp;
        $searchedWithZero = $request->withZero;
        $searchedSearchMethod = $request->searchMethod;
        $searchedFiscalYearId = $request->fiscalYearId;
        $searchedDateFrom = $request->dateFrom;
        $searchedDateTo = $request->dateTo;
        
        
        $dbhelper = new DatabasePartitionHelper();        
        $partitionName = DatabasePartitionHelper::getCompanyWisePartitionName(Auth::user()->company_id_fk);
        $acc_opening_balance = DatabasePartitionHelper::getUserPartitionWiseDBTableName('acc_opening_balance');   
        $acc_voucher = DatabasePartitionHelper::getUserPartitionWiseDBTableName('acc_voucher');

		return view('accounting.reports.capitalFund',[
            'user_branch_id' => $user_branch_id, 
            'user_company_id' => $user_company_id,
            'user_project_id' => $user_project_id,
            'user_project_type_id' => $user_project_type_id,
            'projects' => $projects, 
            'projectTypes' => $projectTypes, 
            'branches' => $branches, 
            'fiscalYears' => $fiscalYears, 
            'searchedProjectId' => $searchedProjectId, 
            'searchedProjectTypeId' => $searchedProjectTypeId, 
            'searchedBranchId' => $searchedBranchId, 
            'searchedDepthLevel' => $searchedDepthLevel, 
            'searchedRoundUp' => $searchedRoundUp, 
            'searchedWithZero' => $searchedWithZero, 
            'searchedSearchMethod' => $searchedSearchMethod, 
            'searchedFiscalYearId' => $searchedFiscalYearId, 
            'searchedDateFrom' => $searchedDateFrom, 
            'searchedDateTo' => $searchedDateTo, 
            'projectSelected' => $projectSelected, 
            'projectTypeSelected' => $projectTypeSelected, 
            'branchSelected' => $branchSelected,
            
            'acc_opening_balance' => $acc_opening_balance,
            'acc_voucher' => $acc_voucher           

        ]);

	}

}		//End AccLedgerReportsController

