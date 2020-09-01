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



class AccOtsInterestPaymentReportController extends Controller
{
    
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


       //Date From       
      if ($request->dateFrom==null) {
        $dateFromSelected = null;
        $startDate = Carbon::toDay();        
      }
      else{
        $dateFromSelected = $request->dateFrom;
        $startDate = date('Y-m-d', strtotime($request->dateFrom));        
      }

      //Date To      
      if ($request->dateTo==null) {
        $dateToSelected = null; 
        $endDate = Carbon::toDay();       
      }
      else{
        $dateToSelected = $request->dateTo;  
        $endDate = date('Y-m-d', strtotime($request->dateTo));       
      }


      //Get Interest Payment details ID base on project and Branch
      $accountList = DB::table('acc_ots_account')->whereIn('projectId_fk',$projectId)->whereIn('branchId_fk',$branchId)->pluck('id')->toArray();
      
      if ($request->searchInterestPayment=='') {
        $interestPaymentList = DB::table('acc_ots_payment_details')->where('paymentDate','>=',$startDate)->where('paymentDate','<=',$endDate)->whereIn('accId_fk',$accountList)->pluck('paymentId_fk')->toArray();
      }
      else{
        $interestPaymentList = DB::table('acc_ots_payment_details')->whereIn('accId_fk',$accountList)->pluck('paymentId_fk')->toArray();
      }
      

       //Interest Payment ID      
      if ($request->searchInterestPayment==null) {
        $interestPaymentSelected = null;
        $interestPaymentId =  $interestPaymentList;     
      }
      else{
        $interestPaymentSelected = $request->searchInterestPayment;
        array_push($interestPaymentId, $request->searchInterestPayment);
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
      $searchInterestPaymentList = DB::table('acc_ots_payment_details')->whereIn('accId_fk',$accountList)->pluck('paymentId_fk')->toArray();
      $searchInterestPayments = array('' => 'All') + DB::table('acc_ots_payment')->whereIn('id',$searchInterestPaymentList)->pluck('paymentId','id')->toArray();

      $accountPaymentList = DB::table('acc_ots_payment_details')->whereIn('paymentId_fk',$interestPaymentId)->pluck('accId_fk')->toArray();
      $accounts = AccOTSRegisterAccount::whereIn('projectId_fk',$projectId)->whereIn('branchId_fk',$branchId)->whereIn('id',$accountPaymentList)->orderBy('branchId_fk')->orderBy('openingDate')->get();
     
     $accNo = array();
     $memberName = array();
     $branchName = array();
     $paymentNatureName = array();
     $interestRate = array();
     $principalAmount = array();
     $interestPayment = array();

     $payments = DB::table('acc_ots_payment_details')->whereIn('accId_fk',$accountList)->whereIn('paymentId_fk',$interestPaymentId)->orderBy('paymentDate','asc')->get();
     
    foreach ($payments as $key => $payment) {
        $account = DB::table('acc_ots_account')->where('id',$payment->accId_fk)->first();

        array_push($accNo, $account->accNo);
        array_push($memberName, DB::table('acc_ots_member')->where('id',$account->memberId_fk)->value('name'));
        array_push($branchName, DB::table('gnr_branch')->where('id',$account->branchId_fk)->value('name'));
        array_push($paymentNatureName, DB::table('acc_ots_period')->where('id',$account->periodId_fk)->value('name'));
        array_push($principalAmount, $account->amount);
        array_push($interestRate, number_format($account->interestRate,2));
        array_push($interestPayment, $payment->amount);
    }

      //Is it the First Request
      if ($request->firstRequest==null) {
        $firstRequest = 1;
      }
      else{
        $firstRequest = 0;
      }

     

		return view('accounting.registerReport.ots.otsInterestPaymentReport',['payments'=>$payments,'accNo'=>$accNo,'memberName'=>$memberName,'branchName'=>$branchName,'paymentNatureName'=> $paymentNatureName,'principalAmount'=>$principalAmount,'interestRate'=>$interestRate,'interestPayment'=>$interestPayment,'searchInterestPayments'=>$searchInterestPayments,'projects'=>$projects,'branches'=>$branches,'startDate'=>$startDate,'endDate'=>$endDate,'projectSelected'=>$projectSelected,'branchSelected'=>$branchSelected,'dateFromSelected'=>$dateFromSelected,'dateToSelected'=>$dateToSelected,'firstRequest'=>$firstRequest,'interestPaymentSelected'=>$interestPaymentSelected]);

	}



  public function getPaymentBaseOnProjectNbranch(Request $request)
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
    $paymentList = DB::table('acc_ots_payment_details')->whereIn('accId_fk',$accountList)->pluck('paymentId_fk')->toArray();


    $payments = DB::table('acc_ots_payment')->whereIn('id',$paymentList)->pluck('paymentId','id')->toArray();

    return response::json($payments);
    //return response::json('ghjg');

  }



}		


