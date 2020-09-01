<?php

namespace App\Http\Controllers\accounting\registerReport;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\accounting\AccOTSRegisterAccount;

use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use App\Traits\GetSoftwareDate;




class AccOtsRegisterBalanceReportController extends Controller
{

  //using GetSoftware class for getting software date
  use GetSoftwareDate;
    
 	public function index(Request $request){		
		
	    $user_branch_id = Auth::user()->branchId;
      $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');
      

      $projectId = array();
      $branchId = array();
      $accNatureId = array();
      

      //Project
      if ($request->searchProject==null) {        
        
          if ($user_branch_id == 1) {
            $projectSelected = null;
            $projectId = DB::table('gnr_project')->pluck('id');
          }
          else{
            $projectSelected = $user_project_id;
            array_push($projectId, $user_project_id);
          }
        
      }
      else{
        $projectSelected = (int)json_decode($request->searchProject);
        array_push($projectId, $projectSelected);
      }      

       //Branch
      if ($request->searchBranch==null) {

        if ($user_branch_id == 1) {
          $branchSelected = null;
            $branchId = DB::table('gnr_branch')->pluck('id');
          }
          else{
            $branchSelected = $user_branch_id;
            array_push($branchId, $branchSelected);
          }
        
      }
      else{
        $branchSelected = (int) json_decode($request->searchBranch);  
          array_push($branchId, $branchSelected); 
      }

    if ($request->searchAccNature=='') {
      $accNatureId = DB::table('acc_ots_period')->pluck('id')->toArray();
    }
    else{
      array_push($accNatureId, $request->searchAccNature); 
    }


       //Date From       
      /*if ($request->dateFrom==null) {
        $dateFromSelected = null;
        $startDate = Carbon::toDay();        
      }
      else{
        $dateFromSelected = $request->dateFrom;
        $startDate = date('Y-m-d', strtotime($request->dateFrom));        
      }*/

      $startDate = Carbon::parse(DB::table('acc_ots_account')->min('openingDate'));

      //Getting software date
      $softDate = GetSoftwareDate::getAccountingSoftwareDate();
      
      //changing format
      $softDate = Carbon::parse($softDate)->format('d-m-Y');

      

      //Date To      
      if($request->dateTo==null) {
        $dateToSelected = $softDate; 
        $endDate = Carbon::toDay()->format('Y-m-d');       
      }
      else{
        $dateToSelected = $request->dateTo;  
        $endDate = date('Y-m-d', strtotime($request->dateTo));       
      }


      if($user_branch_id!=1){
        $projects = DB::table('gnr_project')->where('id',$user_project_id)->get();
        $branches = DB::table('gnr_branch')->where('id',$user_branch_id)->get();
      }
      else{
         $projects = DB::table('gnr_project')->get();
         $branches = DB::table('gnr_branch')->whereIn('projectId',$projectId)->orWhere('id',1)->get();
      }


      //Get the Interest Base on project and Branch
      $searchAccId = array(''=>'All') + DB::table('acc_ots_account')->whereIn('projectId_fk',$projectId)->whereIn('branchId_fk',$branchId)->pluck('accNo','id')->toArray();
     

       //Is it the First Request
      if ($request->firstRequest==null) {
        $firstRequest = 1;
      }
      else{
        $firstRequest = 0;
      }

      if ($request->searchAccId!='') {
        $accounts = DB::table('acc_ots_account')
                          ->where('id',$request->searchAccId)
                          ->get();
      }
      else{
        $accounts = DB::table('acc_ots_account')
                         ->whereIn('projectId_fk',$projectId)
                         ->whereIn('branchId_fk',$branchId)
                         ->whereIn('periodId_fk',$accNatureId)
                         ->where('openingDate','<=',$endDate)
                         ->where(function($query) use ($endDate) {
                          $query->where('status',1)
                                ->orWhere('closingDate','>=',$endDate);
                         })
                         ->get();
      }


      
        // var_dump($softDate);
        // exit();

      $data = array(
        'accounts'=>$accounts,
        'searchAccId'=>$searchAccId,
        'projects'=>$projects,
        'branches'=>$branches,
        'startDate'=>$startDate,
        'endDate'=>$endDate,
        'projectSelected'=>$projectSelected,
        'branchSelected'=>$branchSelected,
        /*'dateFromSelected'=>$dateFromSelected,*/
        'dateToSelected'=>$dateToSelected,
        'firstRequest'=>$firstRequest,
        'accIdSelected'=>$request->searchAccId,
        'accNatureSelected'=>$request->searchAccNature,
        'softwareDate' => $softDate
      );      

    

		return view('accounting.registerReport.ots.otsRegisterBalanceReport',$data);

	}



  public function getAccountBaseOnBranch(Request $request)
  {
    
    $branchId = array();

    if ($request->branchId=='') {
      $branchId = DB::table('gnr_branch')->pluck('id')->toArray();
    }
    else{
      array_push($branchId, $request->branchId);
    }

    $accountList = DB::table('acc_ots_account')->whereIn('branchId_fk',$branchId)->pluck('accNo','id')->toArray();


    return response::json($accountList);
    //return response::json('ghjg');

  }



}		


