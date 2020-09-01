<?php

namespace App\Http\Controllers\accounting\reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\accounting\AddVoucher;
use App\accounting\AddVoucherDetails;

use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use App\Traits\GetSoftwareDate;


class AccTrialBalanceReportController extends Controller
{
  //using GetSoftware class for getting software date
  use GetSoftwareDate;
    
 	public function index(Request $request){
		
	    $user_branch_id = Auth::user()->branchId;
      $user_company_id = Auth::user()->company_id_fk;
      $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');
      $user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectTypeId');

      $projectId = array();
      $projectTypeId = array();
      $branchId = array();
      $categoryId = array();
      $productTypeId = array();

      //Project
      if ($request->searchProject==null) {        
        
          if ($user_branch_id == 1) {
            $projectSelected = 1;
            $projectId = [1];//DB::table('gnr_project')->pluck('id');
          }
          else{
            $projectSelected = $user_project_id;
            array_push($projectId, $projectSelected);
          }
        
      }
      else{
        $projectSelected = (int)json_decode($request->searchProject);
        array_push($projectId, $projectSelected);
      }

      //Project Type
       if ($request->searchProjectType==null) {

        if ($user_branch_id == 1) {
            $projectTypeSelected = 0;
            $projectTypeId = DB::table('gnr_project_type')->pluck('id');
          }
          else{
            $projectTypeSelected = $user_project_type_id;
            array_push($projectTypeId, $projectTypeSelected);
          }
        
      }
      else{
        $projectTypeSelected = (int) json_decode($request->searchProjectType);
        array_push($projectTypeId, $projectTypeSelected);
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
        
        if ($request->searchBranch==0) {          
          $branchId = DB::table('gnr_branch')->where('id','!=',1)->pluck('id');
        }
        else{          
          array_push($branchId, $branchSelected);
        }
        
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
        $startDate = null;        
      }
      else{
        $dateFromSelected = $request->dateFrom;
        $startDate = date('Y-m-d', strtotime($request->dateFrom));        
      }

      //Date To      
      if ($request->dateTo==null) {
        $dateToSelected = $softDate; 
        $endDate = null;       
      }
      else{
        $dateToSelected = $request->dateTo;  
        $endDate = date('Y-m-d', strtotime($request->dateTo));       
      }
      

      $projects = DB::table('gnr_project')->get();
      $projectTypes = DB::table('gnr_project_type')->whereIn('projectId',$projectId)->get();
      $branches = DB::table('gnr_branch')->whereIn('projectId',$projectId)->orWhere('id',1)->get();
      
      
      if($branchSelected===0) {
      	$newBranches = DB::table('gnr_branch')->whereIn('projectId',$projectId)->where('id','!=',1)->pluck('id')->toArray();
      }
      elseif($branchSelected==null){
      	$newBranches = DB::table('gnr_branch')->whereIn('projectId',$projectId)->orWhere('id',1)->pluck('id')->toArray();
      	//$newBranches = DB::table('gnr_branch')->where('id',1)->pluck('id')->toArray();
      }
      else{
      	$newBranches = DB::table('gnr_branch')->where('id',$branchSelected)->pluck('id')->toArray();
      }

       //Is it the First Request
      if ($request->roundUp==null) {
        $firstRequest = 1;
      }
      else{
        $firstRequest = 0;
      }

      if ($request->roundUp==null) {
        $roundUpSelected = 1;
      }
      else{
        $roundUpSelected = $request->roundUp;
      } 

      if ($request->depthLevel==null) {
      	$depthLevelSelected = null;
        $depthLevel = 5;
      }
      else{
      	$depthLevelSelected = $request->depthLevel;
        $depthLevel = $request->depthLevel;
      } 

       if ($request->withZero==null) {
        $withZeroSelected = null;
      }
      else{
        $withZeroSelected = $request->withZero;
      } 




/////////////////////
      $allLedgers = DB::table('acc_account_ledger')->select("id","projectBranchId")->get();

        $ledgerMatchedId=array();
        
        
        foreach ($allLedgers as $singleLedger) {
            $splitArray=str_replace(array('[', ']', '"', ''), '',  $singleLedger->projectBranchId); 

            //$splitArrayFirstValue = explode(",", $splitArray);
            //$splitArraySecondValue = explode(":", $splitArrayFirstValue[0]);

            $array_length=substr_count($splitArray, ",");
            $arrayProjects=array();
            
            for($i=0; $i<$array_length+1; $i++){

                $splitArrayFirstValue = explode(",", $splitArray);

                $splitArraySecondValue = explode(":", $splitArrayFirstValue[$i]);
                $firstIndexValue=(int)$splitArraySecondValue[0];
                $secondIndexValue=(int)$splitArraySecondValue[1];

                if($firstIndexValue==0){
                	
                    if($secondIndexValue==0){                        
                        array_push($ledgerMatchedId, $singleLedger->id);
                    }
                }else{
                    
                        if($firstIndexValue==$projectSelected){ 
                        	if ($secondIndexValue==0) {
                        		array_push($ledgerMatchedId, $singleLedger->id);
                        	}
                        	if ($branchSelected!=null || $branchSelected!=0) {
                        		if ($secondIndexValue==$branchSelected) {
		                    			array_push($ledgerMatchedId, $singleLedger->id);
		                    		}
                        	}
                        	else{
                        		foreach ($newBranches as $selectedBranch) {
		                    		if ($secondIndexValue==$selectedBranch) {
		                    			array_push($ledgerMatchedId, $singleLedger->id);
		                    		}
		                    	}
                        	}     
                            
                        }
                    
                    
                }
            }   //for
        }       //foreach
        ///////////////////////

        if (sizeof($ledgerMatchedId)<=0) {
        	array_push($ledgerMatchedId, 0);
        }

        $ledgers = DB::table('acc_account_ledger')->where('parentId',0)->orderBy('ordering', 'asc')->get();

        if (!is_null($startDate) && !is_null($endDate)) {
        	$vouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->where('voucherDate','>=',$startDate)->where('voucherDate','<=',$endDate)->pluck('id')->toArray();
        }
        else{
        	$vouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->pluck('id')->toArray();
        }


        if (!is_null($startDate) && !is_null($endDate)) {
        $fiscalYearId = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('id');
        $fiscalEndDate = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('fyEndDate');
        $fiscalStartDate = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('fyStartDate');
      }
      else{
        $fiscalYearId=1;
        $fiscalEndDate="2017-06-30";
        $fiscalStartDate="2016-07-01";
      }

        if (!is_null($startDate) && !is_null($endDate)) {
          $openingVouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->where('voucherDate','<',$startDate)->where('voucherDate','>=',$fiscalStartDate)->pluck('id')->toArray();
        }
        else{
          $openingVouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->pluck('id')->toArray();
        }



if ($startDate!=null) {
  //previous fiscal year id
        $currentFiscalYearStartDate = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('fyStartDate');  

        $dateToCompare = date('Y-m-d', strtotime('-1 day', strtotime($currentFiscalYearStartDate)));
        $previousfiscalYearId = DB::table('gnr_fiscal_year')->where('fyEndDate',$dateToCompare)->value('id');
        if ($previousfiscalYearId!=null) {
          $previousfiscalYearStartDate = DB::table('gnr_fiscal_year')->where('id',$previousfiscalYearId)->value('fyStartDate');
        $previousfiscalYearEndDate = DB::table('gnr_fiscal_year')->where('id',$previousfiscalYearId)->value('fyEndDate');
        }
}
else{
  $previousfiscalYearId = 0;
}
        

        

        

        /*$data = array(
            'vouchers' => $vouchers,
            'openingVouchers' => $openingVouchers,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'fiscalYearId' => $fiscalYearId,
            'roundUpSelected' => $roundUpSelected,
          );*/

          

          $data = array(
            'ledgers' => $ledgers,
            'projects'=>$projects,
            'projectTypes'=>$projectTypes,
            'branches'=>$branches,
            'startDate'=>$startDate,
            'endDate'=>$endDate,
            'projectSelected'=>$projectSelected,
            'projectTypeSelected'=>$projectTypeSelected,
            'branchSelected'=>$branchSelected,
            'dateFromSelected'=>$dateFromSelected,
            'dateToSelected'=>$dateToSelected,
            'firstRequest'=>$firstRequest,
            'roundUpSelected'=>$roundUpSelected,
            'depthLevel'=>$depthLevel,
            'depthLevelSelected'=>$depthLevelSelected,
            'ledgerMatchedId'=>$ledgerMatchedId,
            'newBranches'=>$newBranches,
            'vouchers'=>$vouchers,
            'openingVouchers'=>$openingVouchers,
            'fiscalYearId'=>$fiscalYearId,
            'withZeroSelected'=>$withZeroSelected,
            'previousfiscalYearId'=>$previousfiscalYearId,
            'projectTypeId'=>$projectTypeId,
            'softwareDate' => $softDate,
            'user_company_id' => $user_company_id
          );

        

		return view('accounting/reports/trialBalance', $data);

	}



}		//End AccLedgerReportsController


