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

class AccFdrRegisterAccountClosingReportController extends Controller
{
	public function index(Request $request){         

     
     

     $resource = array();

     $projects = DB::table('gnr_project');
     $projectTypes = DB::table('gnr_project_type');
     $branches = DB::table('gnr_branch');

     $fdrTypes = DB::table('acc_fdr_type');
     $bankNames = DB::table('acc_fdr_account');
     $bankBranchLocations = DB::table('acc_fdr_account');
     $fdrAccounts = DB::table('acc_fdr_account');

      //Project
      if ($request->searchProject!=null) {        
        $projectTypes = $projectTypes->where('projectId',$request->searchProject);
        $branches = $branches->orWhere('id',1)->where('projectId',$request->searchProject)->orWhere('id',1);
        $fdrAccounts = $fdrAccounts->where('projectId_fk',$request->searchProject);
      }

      //Project Type
      if ($request->searchProjectType!=null) {
        $branches = $branches->where('projectTypeId',$request->searchProjectType)->orWhere('id',1); 
        $fdrAccounts = $fdrAccounts->where('projectTypeId_fk',$request->searchProjectType);
      }

      //Branch
      if ($request->searchBranch!=null) {
        if ($request->searchBranch==0) {
               $fdrAccounts = $fdrAccounts->where('branchId_fk','!=',1);
             }
             else{
                $fdrAccounts = $fdrAccounts->where('branchId_fk',$request->searchBranch);
             }
        
      }

      //FDR Type
      if ($request->searchFdrType!=null) {        
        $fdrAccounts = $fdrAccounts->where('fdrTypeId_fk',$request->searchFdrType);
        $bankNames = $bankNames->where('fdrTypeId_fk',$request->searchFdrType);
        $bankBranchLocations = $bankBranchLocations->where('fdrTypeId_fk',$request->searchFdrType);
      }

      //Bank Name
      if ($request->searchBankName!=null) {        
        $fdrAccounts = $fdrAccounts->where('bankName',$request->searchBankName);
        $bankBranchLocations = $bankBranchLocations->where('bankName',$request->searchBankName);
      }

      //Bank Branch Location
      if ($request->searchBankBranchLocation!=null) {        
        $fdrAccounts = $fdrAccounts->where('bankBranchLocation',$request->searchBankBranchLocation);
      }

      //Date Range
      if ($request->dateFrom!=null && $request->dateTo!=null) {
        $startDate = Carbon::parse($request->dateFrom);      
        $endDate = Carbon::parse($request->dateTo);
        $accClosingList = DB::table('acc_fdr_close')->where('closingDate','>=',$startDate)->where('closingDate','<=',$endDate)->pluck('accId_fk')->toArray();    
        $fdrAccounts = $fdrAccounts->whereIn('id',$accClosingList);
      }


      $user_branch_id = Auth::user()->branchId;
      $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');
      $user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectTypeId');

      if ($user_branch_id!=1) {
        $projects = $projects->where('id',$user_project_id);
        $projectTypes = $projectTypes->where('id',$user_project_type_id);
        $branches = $branches->where('id',$user_branch_id);
      }


      $projects = $projects->select('id','name','projectCode')->get();
      $projectTypes = $projectTypes->select('id','name','projectTypeCode')->get();
      $branches = $branches->select('id','name','branchCode')->get();

      $fdrTypes = $fdrTypes->pluck('name','id')->toArray();
      $bankNames = $bankNames->pluck('bankName','id')->toArray();
      $bankBranchLocations = $bankBranchLocations->pluck('bankBranchLocation','id')->toArray();

      $fdrAccounts = $fdrAccounts->get();  

      

      return view('accounting.registerReport.fdr.fdrRegisterAccountClosingReport',['fdrAccounts'=>$fdrAccounts,'projects'=>$projects,'projectTypes'=>$projectTypes,'branches'=>$branches,'fdrTypes'=>$fdrTypes,'bankNames'=>$bankNames,'bankBranchLocations'=>$bankBranchLocations]);      
    }

    

}



?>