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


class AccIncomeStatementReportController extends Controller
{

 	public function incomeStatement(Request $request){
		$ledgers = DB::table('acc_account_ledger')->whereIn('accountTypeId',[12,13])->where('parentId',0)->orderBy('ordering', 'asc')->get();


        $user_branch_id = Auth::user()->branchId;
    	$user_company_id = Auth::user()->company_id_fk;
     	$user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');
     	$user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectTypeId');

        //Project
        if ($request->projectId==null) {
            if ($user_branch_id == 1) {
                $projectSelected = 1;
                // $projectIdArray = DB::table('gnr_project')->pluck('id')->toArray();
            }
            else{
                $projectSelected = $user_project_id;
                // array_push($projectIdArray, (int) json_decode($user_project_id));
            }
        }
        else{
            $projectSelected = (int) json_decode($request->projectId);
            // array_push($projectIdArray, (int) json_decode($request->projectId));
        }

        //Project Type
        if ($request->projectTypeId==null) {
            if ($user_branch_id == 1) {
                $projectTypeSelected = null;
                // $projectTypeIdArray = DB::table('gnr_project_type')->pluck('id')->toArray();
            }
            else{
                $projectTypeSelected = (int) json_decode($user_project_type_id);
                // array_push($projectTypeIdArray, (int) json_decode($user_project_type_id));
            }
        }
        else{
            $projectTypeSelected = (int) json_decode($request->projectTypeId);
            // array_push($projectTypeIdArray, (int) json_decode($request->projectTypeId));
        }

        //Branch
        if ($request->branchId==null) {
            if ($user_branch_id == 1) {
                $branchSelected = null;
                // $branchIdArray = DB::table('gnr_branch')->whereIn('projectId', $projectIdArray)->orWhere('id', 1)->pluck('id')->toArray();
            }
            else{
                $branchSelected = (int) json_decode($user_branch_id);
                // array_push($branchIdArray, (int) json_decode($user_branch_id));
            }
        }
        else{
            $branchSelected = (int) json_decode($request->branchId);
            // array_push($branchIdArray, (int) json_decode($request->branchId));
        }



        $projects = DB::table('gnr_project')->get();
        $projectTypes = DB::table('gnr_project_type')->where('projectId',$projectSelected)->get();
        $branches = DB::table('gnr_branch')->where('projectId',$projectSelected)->orWhere('id',1)->get();

        // $projects = GnrProject::select('id','name','projectCode')->get();
        // $projectTypes = GnrProjectType::select('id','name','projectTypeCode')->get();
        // $branches = GnrBranch::select('id','name','branchCode')->get();
      	$fiscalYears = DB::table('gnr_fiscal_year')->orderBy('name', 'desc')->pluck('name','id');

        $searchedProjectId=$request->projectId;
        $searchedProjectTypeId=$request->projectTypeId;
        $searchedBranchId=$request->branchId;
        $searchedDepthLevel=$request->depthLevel;
        $searchedRoundUp=$request->roundUp;
        $searchedWithZero=$request->withZero;
        $searchedSearchMethod=$request->searchMethod;
        $searchedFiscalYearId=$request->fiscalYearId;
        // $searchedDateFrom=$request->dateFrom;
        // $searchedDateTo=$request->dateTo;
        // $searchedDateFrom=$request->dateFrom;
        $searchedDateTo= Carbon::parse($request->dateTo)->format('Y-m-d');

        // date collection
        $toDate = DB::table('acc_day_end')->where('branchIdFk', $user_branch_id)->where('isDayEnd', '=', '0')->value('date');
        // dd($toDate);
        if ($searchedFiscalYearId) {
            $currentFiYearStartDate=DB::table('gnr_fiscal_year')->where('id', $searchedFiscalYearId)->value('fyStartDate');
            $currentFiYearEndDate=DB::table('gnr_fiscal_year')->where('id', $searchedFiscalYearId)->value('fyEndDate');
        }
        else {
            $currentFiYearStartDate=DB::table('gnr_fiscal_year')->where('fyStartDate','<=', $toDate)->where('fyEndDate','>=', $toDate)->value('fyStartDate');
            $currentFiYearEndDate=DB::table('gnr_fiscal_year')->where('fyStartDate','<=', $toDate)->where('fyEndDate','>=', $toDate)->value('fyEndDate');
        }

        // dd($currentFiYearEndDate);
        if ($searchedSearchMethod == 1) {
            $toDateFrom = $currentFiYearStartDate;
            $toDateTo = $currentFiYearEndDate;
        }
        elseif ($searchedSearchMethod == 3) {
            $toDateTo = $searchedDateTo;
            $toDateFrom = DB::table('gnr_fiscal_year')->where('fyStartDate', '<=', $toDateTo)->where('fyEndDate','>=', $toDateTo)->value('fyStartDate');
        }else {
            $toDateTo=$toDate;
            $toDateFrom='';
        }


        $data = array(
            'user_branch_id'        => $user_branch_id,
            'user_company_id'       => $user_company_id,
            'user_project_id'       => $user_project_id,
            'user_project_type_id'  => $user_project_type_id,
            'projects'              => $projects,
            'projectTypes'          => $projectTypes,
            'branches'              => $branches,
            'fiscalYears'           => $fiscalYears,
            'searchedProjectId'     => $searchedProjectId,
            'searchedProjectTypeId' => $searchedProjectTypeId,
            'searchedBranchId'      => $searchedBranchId,
            'searchedDepthLevel'    => $searchedDepthLevel,
            'searchedRoundUp'       => $searchedRoundUp,
            'searchedWithZero'      => $searchedWithZero,
            'searchedSearchMethod'  => $searchedSearchMethod,
            'searchedFiscalYearId'  => $searchedFiscalYearId,
            'searchedDateFrom'      => $toDateFrom,
            'searchedDateTo'        => $toDateTo,
            'ledgers'               => $ledgers,
            'projectSelected'       => $projectSelected,
            'projectTypeSelected'   => $projectTypeSelected,
            'branchSelected'        => $branchSelected
        );
        // dd($data);


		return view('accounting.reports.incomeStatement', $data);

	}

}		//End AccLedgerReportsController
