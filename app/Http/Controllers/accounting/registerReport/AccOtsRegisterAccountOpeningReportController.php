<?php
namespace App\Http\Controllers\accounting\registerReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Response;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\GetSoftwareDate;


class AccOtsRegisterAccountOpeningReportController extends Controller
{

  //using GetSoftware class for getting software date
  use GetSoftwareDate;

  public function index(Request $request){

      $user_branch_id = Auth::user()->branchId;
      $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');
      

      $projectId = array();
      $branchId = array();
      $interestPaymentId = array();
      

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


        //Getting software date
        $softDate = GetSoftwareDate::getAccountingSoftwareDate();
      
        //changing format
        $softDate = Carbon::parse($softDate)->format('d-m-Y');

        //Getting software start date
        $softwareStartDate = DB::table('gnr_branch')->where('id', $user_branch_id)->value('softwareStartDate');

        //changing format
        $softwareStartDate = Carbon::parse($softwareStartDate)->format('d-m-Y');

       //Date From       
      if ($request->dateFrom==null) {
        $dateFromSelected = $softwareStartDate;
        $startDate = Carbon::toDay();        
      }
      else{
        $dateFromSelected = $request->dateFrom;
        $startDate = date('Y-m-d', strtotime($request->dateFrom));        
      }

      //Date To      
      if ($request->dateTo==null) {
        $dateToSelected = $softDate; 
        $endDate = Carbon::toDay();       
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

        //Is it the First Request
      if ($request->firstRequest==null) {
        $firstRequest = 1;
      }
      else{
        $firstRequest = 0;
      }



        $infos = DB::table('acc_ots_account as t1')
                                ->join('acc_ots_member as t2','t1.memberId_fk','t2.id')
                                ->select('t1.*','t2.name')
                                ->whereIn('t1.projectId_fk',$projectId)
                                ->whereIn('t1.branchId_fk',$branchId)
                                ->where('t1.openingDate','>=',$startDate)
                                ->where('t1.openingDate','<=',$endDate)
                                ->orderBy('openingDate','asc')
                                ->get();

        $data = array(
          'infos'=>$infos,
          'projects'=>$projects,
          'branches'=>$branches,
          'startDate'=>$startDate,
          'endDate'=>$endDate,
          'projectSelected'=>$projectSelected,
          'branchSelected'=>$branchSelected,
          'dateFromSelected'=>$dateFromSelected,
          'dateToSelected'=>$dateToSelected,
          'firstRequest'=>$firstRequest,
          'softwareDate' => $softDate
        );

      return view('accounting.registerReport.ots.otsRegisterAccountOpeningReport', $data);      
    }

    

}



?>