<?php
namespace App\Http\Controllers\accounting\registerReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Response;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

use App\accounting\AccOTSRegisterAccount;
use App\accounting\AccOTSRegisterMember;
use App\accounting\AccOTSRegisterInterest;
use App\accounting\AccOTSRegisterInterestDetails;
use App\accounting\AccOTSRegisterPayment;
use App\accounting\AccOTSRegisterPaymentDetails;
use App\accounting\AccOTSRegisterPrincipalPayment;
use App\Traits\GetSoftwareDate;


class AccOtsInterestGenerateReportController extends Controller
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

      $accountList = DB::table('acc_ots_account')->whereIn('projectId_fk',$projectId)->whereIn('branchId_fk',$branchId)->pluck('id')->toArray();
      $InterestList = DB::table('acc_ots_interest_details')->whereIn('accId_fk',$accountList)->pluck('interestId_fk')->toArray();

       $searchInterestIds = array('' => 'All') + DB::table('acc_ots_interest')->whereIn('id',$InterestList)->pluck('interestId','id')->toArray();

        //Interest ID      
      if ($request->searchInterestId==null) {
        $interestIdSelected = null;
           
      }
      else{
        $interestIdSelected = $request->searchInterestId;
        
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


      if ($request->searchInterestId==null) {
        $interestDetails = AccOTSRegisterInterestDetails::whereIn('accId_fk',$accountList)->where('generateDate','>=',$startDate)->where('generateDate','<=',$endDate)->groupBy('accId_fk')->get();
      }
      else{
        $interestDetails = AccOTSRegisterInterestDetails::whereIn('accId_fk',$accountList)->where('interestId_fk',$request->searchInterestId)->groupBy('accId_fk')->get();
      }

      

      $accName = array();
      $branchName = array();
      $accNature = array();
      $interestRate = array();
      $principalAmount = array();
      $interestDueAmount = array();

      foreach ($interestDetails as $interestDetail) {
        $account = AccOTSRegisterAccount::find($interestDetail->accId_fk);
        array_push($accName, AccOTSRegisterMember::where('id',$account->memberId_fk)->value('name'));
        array_push($branchName, DB::table('gnr_branch')->where('id',$account->branchId_fk)->value('name'));
        array_push($accNature, DB::table('acc_ots_period')->where('id',$account->periodId_fk)->value('name'));
        // array_push($interestRate, DB::table('acc_ots_period')->where('id',$account->periodId_fk)->value('interestRate'));        
        array_push($interestRate, number_format($account->interestRate,2));        
        array_push($principalAmount, $account->amount);
        if ($request->searchInterestId==null) {
          array_push($interestDueAmount, $this->getInterestDue($account->id,$startDate,$endDate));
        }
        else{
          array_push($interestDueAmount, $interestDetail->amount);
        }
        
      }


      

        $data = array(
          'interestDetails'=>$interestDetails,
          'projects'=>$projects,
          'branches'=>$branches,
          'startDate'=>$startDate,
          'endDate'=>$endDate,
          'searchInterestIds'=>$searchInterestIds,
          'projectSelected'=>$projectSelected,
          'branchSelected'=>$branchSelected,
          'dateFromSelected'=>$dateFromSelected,
          'dateToSelected'=>$dateToSelected,
          'firstRequest'=>$firstRequest,
          'interestIdSelected'=>$interestIdSelected,
          'accName'=>$accName,
          'branchName'=>$branchName,
          'accNature'=>$accNature,
          'interestRate'=>$interestRate,
          'principalAmount'=>$principalAmount,
          'interestDueAmount'=>$interestDueAmount,
          'softwareDate' => $softDate
        );

      return view('accounting.registerReport.ots.otsRegisterInterestGenerateReport', $data);      
    }


    public function getInterestDue($accId,$startDate,$endDate)
    {
        
        $openingBalance = (float) DB::table('acc_ots_account')->where('id',$accId)->where('effectiveDate','>',$startDate)->value('openingBalance');
        $interests = (float) DB::table('acc_ots_interest_details')->where('accId_fk',$accId)->where('generateDate','>=',$startDate)->where('generateDate','<=',$endDate)->sum('amount');

        $totalDue = $openingBalance + $interests;

        return $totalDue;

    }


    public function getInterestBaseOnProjectNbranch(Request $request)
    {
      $projectId = array();
      $branchId = array();

      if ($request->projectId=='') {
        $projectId = DB::table('gnr_project')->pluck('id')->toArray();
      }
      else{
        array_push($projectId, $request->projectId);
      }

      if ($request->branchId=='') {
        $branchId = DB::table('gnr_branch')->pluck('id')->toArray();
      }
      else{
        array_push($branchId, $request->branchId);
      }



      $accountList = DB::table('acc_ots_account')->whereIn('projectId_fk',$projectId)->whereIn('branchId_fk',$branchId)->pluck('id')->toArray();
      $InterestList = DB::table('acc_ots_interest_details')->whereIn('accId_fk',$accountList)->pluck('interestId_fk')->toArray();


      $interests = DB::table('acc_ots_interest')->whereIn('id',$InterestList)->pluck('interestId','id')->toArray();

      return response::json($interests);
      //return response::json('ghjg');

    }

    

}



?>