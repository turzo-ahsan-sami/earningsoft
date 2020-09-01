<?php
namespace App\Http\Controllers\accounting\registerReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Response;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use App\accounting\AccFDRRegisterAccount;
use App\Traits\GetSoftwareDate;


class AccLoanRegisterReportController extends Controller
{

  //using GetSoftware class for getting software date
  use GetSoftwareDate;

	public function index(Request $request){         


     $projects = DB::table('gnr_project');
     $projectTypes = DB::table('gnr_project_type');
     //$branches = DB::table('gnr_branch');

     $fdrTypes = DB::table('acc_fdr_type');
     $banks = DB::table('gnr_bank')->pluck('name','id')->toArray();
     $bankBranches = DB::table('gnr_bank_branch');
     $loanAccounts = DB::table('acc_loan_register_account');
     

      //Project
      if ($request->searchProject!=null) {        
        $projectTypes = $projectTypes->where('projectId',$request->searchProject);
        //$branches = $branches->orWhere('id',1)->where('projectId',$request->searchProject)->orWhere('id',1);
        $loanAccounts = $loanAccounts->where('projectId_fk',$request->searchProject);
      }

      //Project Type
      if ($request->searchProjectType!=null) {
        //$branches = $branches->where('projectTypeId',$request->searchProjectType)->orWhere('id',1); 
        $loanAccounts = $loanAccounts->where('projectTypeId_fk',$request->searchProjectType);
      }

     /* //Branch
      if ($request->searchBranch!=null) {
        if ($request->searchBranch==0) {
               $fdrAccounts = $fdrAccounts->where('branchId_fk','!=',1);
             }
             else{
                $fdrAccounts = $fdrAccounts->where('branchId_fk',$request->searchBranch);
             }
        
      }*/

     /* //FDR Type
      if ($request->searchFdrType!=null) {        
        $fdrAccounts = $fdrAccounts->where('fdrTypeId_fk',$request->searchFdrType);
      }*/

      //Bank
      if ($request->searchBank!=null) {        
        $loanAccounts = $loanAccounts->where('bankId_fk',$request->searchBank);
        $bankBranches = $bankBranches->where('bankId_fk',$request->searchBank);
      }

      //Bank Branch
      if ($request->searchBankBranch!=null) {        
        $loanAccounts = $loanAccounts->where('bankBranchId_fk',$request->searchBankBranch);
        
      }

      //Date Range
      if ($request->dateFrom!=null && $request->dateTo!=null) {
        $startDate = Carbon::parse($request->dateFrom);      
        $endDate = Carbon::parse($request->dateTo);    
        
     //$closingAccountIdsBeforeStartDate = DB::table('acc_fdr_close')->where('closingDate','<',$startDate)->pluck('accId_fk')->toArray();      
        $loanAccounts = $loanAccounts/*->where('openingDate','>=',$startDate)*/->where('loanDate','<=',$endDate)/*->whereNotIn('id',$closingAccountIdsBeforeStartDate)*/;
      }
      else{
        $startDate = null;
        $endDate = null;
      }


      $user_branch_id = Auth::user()->branchId;
      

      if ($user_branch_id!=1) {

        $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');
        $user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectTypeId');

        $projects = $projects->where('id',$user_project_id);
        $projectTypes = $projectTypes->where('id',$user_project_type_id);
        //$branches = $branches->where('id',$user_branch_id);
      }


      $projects = $projects->select('id','name','projectCode')->get();
      $projectTypes = $projectTypes->select('id','name','projectTypeCode')->get();
      //$branches = $branches->select('id','name','branchCode')->get();

      $fdrTypes = $fdrTypes->pluck('name','id')->toArray();
      $bankBranches = $bankBranches->select('name','id','bankId_fk')->get();

      //Get the Bank Branch having current accounts
      $bankBranchList = $loanAccounts;      
      $bankBranchList = $bankBranchList->pluck('bankBranchId_fk')->toArray();
      ////////

      $loanAccounts = $loanAccounts->get();

      //Getting software date
      $softDate = GetSoftwareDate::getAccountingSoftwareDate();
      
      //changing format
      $softDate = Carbon::parse($softDate)->format('d-m-Y');

      //Getting software start date
      $softwareStartDate = DB::table('gnr_branch')->where('id', $user_branch_id)->value('softwareStartDate');

      //changing format
      $softwareStartDate = Carbon::parse($softwareStartDate)->format('d-m-Y');

        $data = array(
          'loanAccounts'      =>$loanAccounts,
          'projects'          =>$projects,
          'projectTypes'      =>$projectTypes,/*'branches'=>$branches,*/
          'fdrTypes'          =>$fdrTypes,
          'banks'             =>$banks,
          'bankBranches'      =>$bankBranches,
          'banks'             =>$banks,
          'bankBranchList'    =>$bankBranchList,
          'startDate'         =>$startDate,
          'endDate'           =>$endDate,
          'softwareDate'      => $softDate,
          'softwareStartDate' => $softwareStartDate
        );
      

      return view('accounting.registerReport.loanRegister.loanRegisterReport', $data);      
    }
}



?>