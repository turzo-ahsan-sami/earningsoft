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



class AccLoanRegisterInstallmentReportController extends Controller
{
    
 	public function index(Request $request){		
		

     $accounts = DB::table('acc_loan_register_account');

     if ($request->searchDonorType!='') {
       $bankIds = DB::table('gnr_bank')->where('isDonor',$request->searchDonorType)->pluck('id')->toArray();
     }
     else{
      $bankIds = DB::table('gnr_bank')->pluck('id')->toArray();
     }

     
     if ($request->searchProject!='') {
       $accounts = $accounts->where('projectId_fk',$request->searchProject);
     }

     if ($request->searchProjectType!='') {
       $accounts = $accounts->where('projectTypeId_fk',$request->searchProjectType);
     }

     $startDate = date("Y-m-d", strtotime($request->searchDate));
     $endDate = date("Y-m-t", strtotime($startDate));

     $accountListHavingInstallment = DB::table('acc_loan_register_payment_schedule')->where('paymentDate','>=',$startDate)->where('paymentDate','<=',$endDate)->pluck('accId_fk')->toArray();

    

     $accounts = $accounts->whereIn('bankId_fk',$bankIds)->whereIn('id',$accountListHavingInstallment)->orderBy('bankId_fk')->get();

     $activeProjectTypeList = array();

     foreach ($accounts as $key => $account) {
       array_push($activeProjectTypeList, $account->projectTypeId_fk);
     }



     $projectTypes = DB::table('gnr_project_type')->whereIn('id',$activeProjectTypeList)->get();



    

		return view('accounting.registerReport.loanRegister.installmentReport',['accounts'=>$accounts,'projectTypes'=>$projectTypes,'bankIds'=>$bankIds,'accountListHavingInstallment'=>$accountListHavingInstallment]);

	}



}		


