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
use App\Traits\GetSoftwareDate;

class AccLedgerReportController extends Controller
{
    // using GetSofware class for getting softwae date
    use GetSoftwareDate;
    //
    public function ledgerReport(Request $request){

    	$user_branch_id = Auth::user()->branchId;
     	$user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');
     	$user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectTypeId');
        $userBranch = Auth::user()->branch;


        $allLedgers = DB::table('acc_account_ledger')->where('companyIdFk',Auth::user()->company_id_fk)->where('isGroupHead', 0)->select("id","projectBranchId")->get();

        if($userBranch->branchCode !=0){
		    $matchedId=array();        
		    foreach ($allLedgers as $singleLedger) {
		        $splitArray=str_replace(array('[', ']', '"', ''), '',  $singleLedger->projectBranchId); 

		        $splitArrayFirstValue = explode(",", $splitArray);
		        $splitArraySecondValue = explode(":", $splitArrayFirstValue[0]);

		        $array_length=substr_count($splitArray, ",");
		        $arrayProjects=array();
		        $temp=null;
		        // $temp1=null;
		        for($i=0; $i<$array_length+1; $i++){

		            $splitArrayFirstValue = explode(",", $splitArray);

		            $splitArraySecondValue = explode(":", $splitArrayFirstValue[$i]);
		            $firstIndexValue=(int)$splitArraySecondValue[0];
		            $secondIndexValue=(int)$splitArraySecondValue[1];

		            if($firstIndexValue==0){
		                if($secondIndexValue==0){
		                    array_push($matchedId, $singleLedger->id);
		                }
		            }else{
		                if($firstIndexValue==$user_project_id){
		                    if($secondIndexValue==0){
		                        array_push($matchedId, $singleLedger->id);
		                    }elseif($secondIndexValue==$user_branch_id ){
		                        array_push($matchedId, $singleLedger->id);
		                    }
		                }
		            }
		        }   //for
		    }       //foreach
        	$childrenLedger = DB::table('acc_account_ledger')->where('companyIdFk',Auth::user()->company_id_fk)->whereIn('id', $matchedId )->orderBy('code','asc')->select('id','name','code')->get();
		}else{
        	$childrenLedger = DB::table('acc_account_ledger')->where('companyIdFk',Auth::user()->company_id_fk)->where('isGroupHead', 0 )->orderBy('code','asc')->select('id','name','code')->get();
		}

        //Initializing Different array
        $projectIdArray = array();
        $projectTypeIdArray = array();
        $branchIdArray = array();

        //Project
        if ($request->projectId==null) {
            if ($userBranch->branchCode == 0) {
                $projectSelected = 1;
                $projectIdArray = DB::table('gnr_project')->pluck('id')->toArray();
            }
            else{
                $projectSelected = $user_project_id;
                array_push($projectIdArray, (int) json_decode($user_project_id));
            }
        }
        else{
            $projectSelected = (int) json_decode($request->projectId);
            array_push($projectIdArray, (int) json_decode($request->projectId));
        }
        
        //Project Type                        
        if ($request->projectTypeId==null) {
            if ($userBranch->branchCode == 0) {
                $projectTypeSelected = null;
                $projectTypeIdArray = DB::table('gnr_project_type')->pluck('id')->toArray();
            }
            else{
                $projectTypeSelected = (int) json_decode($user_project_type_id);
                array_push($projectTypeIdArray, (int) json_decode($user_project_type_id));
            }
        }
        else{
            $projectTypeSelected = (int) json_decode($request->projectTypeId);
            array_push($projectTypeIdArray, (int) json_decode($request->projectTypeId));
        }

        //Branch
        if ($request->branchId==null) {
            if ($userBranch->branchCode == 0) {
                $branchSelected = null;
                $branchIdArray = DB::table('gnr_branch')->whereIn('projectId', $projectIdArray)->orWhere('id', 1)->pluck('id')->toArray();
            }
            else{
                $branchSelected = (int) json_decode($user_branch_id);
                array_push($branchIdArray, (int) json_decode($user_branch_id));
            }
        }elseif ($request->branchId==0) {
            $branchSelected = 0;
            $branchIdArray = DB::table('gnr_branch')->whereIn('projectId', $projectIdArray)->where('id', '!=', 1)->pluck('id')->toArray();
        }
        else{
            $branchSelected = (int) json_decode($request->branchId);
            array_push($branchIdArray, (int) json_decode($request->branchId));
        }



        $projects = DB::table('gnr_project')->get();
        $projectTypes = DB::table('gnr_project_type')->where('projectId',$projectSelected)->get();
        $branches = DB::table('gnr_branch')->where('projectId',$projectSelected)->orWhere('id',1)->get();
      


        // $projects = GnrProject::select('id','name','projectCode')->get();
        // $projectTypes = GnrProjectType::select('id','name','projectTypeCode')->get();
        // $branches = GnrBranch::select('id','name','branchCode')->get();
        $voucherTypes = AddVoucherType::select('id','shortName')->get();
        $searchedProjectId=$request->projectId;
        $searchedProjectTypeId=$request->projectTypeId;
        $searchedBranchId=$request->branchId;
        $searchedLedgerId=$request->ledgerId;
        $searchedVoucherTypeId=$request->voucherTypeId;

        $searchedDateFrom=$request->dateFrom;
        $searchedDateTo=$request->dateTo;

        //Getting software date
        $softDate = GetSoftwareDate::getAccountingSoftwareDate();
        //changing format
        $softDate = Carbon::parse($softDate)->format('d-m-Y');

        //Getting software start date
        $softwareStartDate = DB::table('gnr_branch')->where('id', $user_branch_id)->value('softwareStartDate');

        //changing format
        $softwareStartDate = Carbon::parse($softwareStartDate)->format('d-m-Y');
        
        // var_dump($softDate);
        // exit();


        $data = array(
            'user_branch_id'        => $user_branch_id,
            'user_project_id'       => $user_project_id,
            'user_project_type_id'  => $user_project_type_id,
            'childrenLedger'        => $childrenLedger,
            'projects'              => $projects,
            'projectTypes'          => $projectTypes, 
            'branches'              => $branches, 
            'voucherTypes'          => $voucherTypes, 
            'searchedProjectId'     => $searchedProjectId,
            'searchedProjectTypeId' => $searchedProjectTypeId, 
            'searchedBranchId'      => $searchedBranchId, 
            'searchedLedgerId'      => $searchedLedgerId, 
            'searchedVoucherTypeId' => $searchedVoucherTypeId, 
            'searchedDateFrom'      => $searchedDateFrom,
            'searchedDateTo'        => $searchedDateTo, 
            'projectSelected'       => $projectSelected, 
            'projectTypeSelected'   => $projectTypeSelected,
            'branchSelected'        => $branchSelected,
            'softwareDate'          => $softDate,
            'softwareStartDate'     => $softwareStartDate
        );

	    
	return view('accounting/reports/ledgerReport',$data);
    }

 	public function incomeStatement(){
		$ledgers = DB::table('acc_account_ledger')->whereIn('accountTypeId',[12,13])->where('parentId',0)->orderBy('ordering', 'asc')->get();
		

		return view('accounting.reports.incomeStatement',['ledgers' => $ledgers]);

	}


    public function getDate(Request $request){
        $branchId = $request->branchId;

        $softStartingDate = DB::table('gnr_branch')
        ->where('id', $branchId)
        ->value('softwareStartDate')
        ->get();
    }



}		//End AccLedgerReportsController


