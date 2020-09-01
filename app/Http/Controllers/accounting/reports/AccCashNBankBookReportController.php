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
use App\accounting\process\AccMonthEnd;
use App\Service\DatabasePartitionHelper;

class AccCashNBankBookReportController extends Controller
{
    // using GetSoftwareDate class for getting software date
    use GetSoftwareDate;

    public function cashBookReport(Request $request){
        //dd($request->all());
        $user_branch_id = Auth::user()->branchId;
        $user_company_id = Auth::user()->company_id_fk;
        $userBranch = Auth::user()->branch;
        
        $user_project_id = (int) DB::table('gnr_branch')->where('companyId',$user_company_id)->where('id',$user_branch_id)->value('projectId');
        $user_project_type_id = (int) DB::table('gnr_branch')->where('companyId',$user_company_id)->where('id',$user_branch_id)->value('projectTypeId');
        
        //Initializing Different array
        $projectIdArray = array();
        $projectTypeIdArray = array();
        $branchIdArray = array();

        //Project
        if ($request->projectId==null) {
            if ($userBranch->branchCode == 0) {
                $projectSelected = 1;
                $projectIdArray = DB::table('gnr_project')->where('companyId',$user_company_id)->pluck('id')->toArray();
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
                $projectTypeIdArray = DB::table('gnr_project_type')->where('companyId',$user_company_id)->pluck('id')->toArray();
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
                $branchIdArray = DB::table('gnr_branch')->where('companyId',$user_company_id)->whereIn('projectId', $projectIdArray)->orWhere('id', 1)->pluck('id')->toArray();
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



        $projects = ['All'] + DB::table('gnr_project')->where('companyId', $user_company_id)->pluck('name','id')->toarray();
        $projectTypes = DB::table('gnr_project_type')->where('projectId',$projectSelected)->where('companyId', $user_company_id)->get();
        $branches = DB::table('gnr_branch')->where('projectId',$projectSelected)->where('companyId', $user_company_id)->orWhere('id',1)->get();



        // $projects = GnrProject::select('id','name','projectCode')->get();
        // $projectTypes = GnrProjectType::select('id','name','projectTypeCode')->get();
        // $branches = GnrBranch::select('id','name','branchCode')->get();

        $cashLedgerId = DB::table('acc_account_ledger')->where('companyIdFk', $user_company_id)->where('accountTypeId', 4)->where('isGroupHead', 0)->value('id');

        $voucherTypes = AddVoucherType::select('id','shortName')->get();
        $searchedProjectId=$request->projectId;
        $searchedProjectTypeId=$request->projectTypeId;
        $searchedBranchId=$request->branchId;
        $searchedLedgerId=$cashLedgerId;
        $searchedVoucherTypeId=$request->voucherTypeId;

        $searchedDateFrom=$request->dateFrom;
        $searchedDateTo=$request->dateTo;


        //Getting software date
        $softDate = GetSoftwareDate::getAccountingSoftwareDate();

        //changing format
        $softDate = Carbon::parse($softDate)->format('d-m-Y');

        //Getting software start date
        $softwareStartDate = DB::table('gnr_branch')->where('companyId', $user_company_id)->where('id', $user_branch_id)->value('softwareStartDate');

        //changing format
        $softwareStartDate = Carbon::parse($softwareStartDate)->format('d-m-Y');

        $data = array(
            'user_branch_id'        => $user_branch_id,
            'user_project_id'       => $user_project_id,
            'user_project_type_id'  => $user_project_type_id,
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


        return view('accounting.reports.cashBookReport', $data);
    }



    public function bankBookReport(Request $request){
        $user_company_id = Auth::user()->company_id_fk;
        $userBranch = Auth::user()->branchId;
        $user_branch_code = Auth::user()->branch;
        $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');
        $user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectTypeId');
        $headOfficeId = DB::table('gnr_branch')->where('companyId', $userCompanyId)->where('branchCode', 0)->value('id');
        //Initializing Different array
        $projectIdArray = array();
        $projectTypeIdArray = array();
        $branchIdArray = array();
        $ledgerIdsArray = array();


        $allLedgers = DB::table('acc_account_ledger')->where('companyIdFk',$user_company_id)->where('isGroupHead', 0)->where('accountTypeId', 5)->select("id","projectBranchId")->get();

        // if($user_branch_id!=1){
        //     $matchedId=array();
        //     foreach ($allLedgers as $singleLedger) {
        //         $splitArray=str_replace(array('[', ']', '"', ''), '',  $singleLedger->projectBranchId);

        //         $splitArrayFirstValue = explode(",", $splitArray);
        //         $splitArraySecondValue = explode(":", $splitArrayFirstValue[0]);

        //         $array_length=substr_count($splitArray, ",");
        //         $arrayProjects=array();
        //         $temp=null;
        //         // $temp1=null;
        //         for($i=0; $i<$array_length+1; $i++){

        //             $splitArrayFirstValue = explode(",", $splitArray);

        //             $splitArraySecondValue = explode(":", $splitArrayFirstValue[$i]);
        //             $firstIndexValue=(int)$splitArraySecondValue[0];
        //             $secondIndexValue=(int)$splitArraySecondValue[1];

        //             if($firstIndexValue==0){
        //                 if($secondIndexValue==0){
        //                     array_push($matchedId, $singleLedger->id);
        //                 }
        //             }else{
        //                 if($firstIndexValue==$user_project_id){
        //                     if($secondIndexValue==0){
        //                         array_push($matchedId, $singleLedger->id);
        //                     }elseif($secondIndexValue==$user_branch_id ){
        //                         array_push($matchedId, $singleLedger->id);
        //                     }
        //                 }
        //             }
        //         }   //for
        //     }       //foreach
        //     $childrenLedger = DB::table('acc_account_ledger')->whereIn('id', $matchedId )->select('id','name','code')->orderBy('code')->get();
        // }else{
        //     $childrenLedger = DB::table('acc_account_ledger')->where('isGroupHead', 0 )->where('accountTypeId', 5)->select('id','name','code')->orderBy('code')->get();
        // }

        $matchedId=array();

        if($headOfficeId){

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
                    }
                    // elseif($firstIndexValue==$user_project_id){
                    //     if($secondIndexValue==$user_branch_id){
                    //         array_push($matchedId, $singleLedger->id);
                    //     }
                    // }
                    else{
                        // if($firstIndexValue!=$temp){
                            if($firstIndexValue==$request->projectId){
                                    if($secondIndexValue==0){
                                        array_push($matchedId, $singleLedger->id);
                                    }elseif($secondIndexValue==$request->branchId){
                                        array_push($matchedId, $singleLedger->id);
                                    }elseif($request->branchId == null){
                                        array_push($matchedId, $singleLedger->id);
                                    }elseif($request->branchId == 0){
                                        if($secondIndexValue != 1){
                                            array_push($matchedId, $singleLedger->id);
                                        }
                                    }
                            }
                    }

                }   //for
            } //foreach
        }else{
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
            $childrenLedger = DB::table('acc_account_ledger')->where('companyIdFk',$user_company_id)->whereIn('id', $matchedId )->orderBy('code','asc')->select('id','name','code')->get();
        }
        // Getting ledger details based on first request
        if(isset($request->projectId)){
            $childrenLedger = DB::table('acc_account_ledger')->where('companyIdFk',$user_company_id)->whereIn('id', $matchedId )->orderBy('code','asc')->select('id','name','code')->get();
        }elseif($headOfficeId){
            $childrenLedger = DB::table('acc_account_ledger')->where('companyIdFk',$user_company_id)->where('isGroupHead', 0)->where('accountTypeId', 5)->select('id','name','code')->orderBy('code')->get();
        }
        // else{
        //     $childrenLedger = DB::table('acc_account_ledger')->whereIn('id', $matchedId )->orderBy('code','asc')->select('id','name','code')->get();
        // }


        // Assigning ledger id to ledgerIdsArray based on request
        if($request->ledgerId){
            $ledgerIdsArray = [$request->ledgerId];
        }else{
            $ledgerIdsArray = $matchedId;
            // dd($ledgerIdsArray);
        }


        if(isset($request->projectId)){
             // dd($matchedId);
        }




        //Project
        if ($request->projectId==null) {
            if ($headOfficeId) {
                $projectSelected = 1;
                $projectIdArray = DB::table('gnr_project')->where('companyId',$user_company_id)->pluck('id')->toArray();
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
            if ($headOfficeId) {
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
            if ($headOfficeId) {
                $branchSelected = null;
                $branchIdArray = DB::table('gnr_branch')->where('companyId',$user_company_id)->whereIn('projectId', $projectIdArray)->orWhere('id', 1)->pluck('id')->toArray();
            }
            else{
                $branchSelected = (int) json_decode($user_branch_id);
                array_push($branchIdArray, (int) json_decode($user_branch_id));
            }
        }elseif ($request->branchId==0) {
            $branchSelected = 0;
            $branchIdArray = DB::table('gnr_branch')->where('companyId',$user_company_id)->whereIn('projectId', $projectIdArray)->where('id', '!=', 1)->pluck('id')->toArray();
        }
        else{
            $branchSelected = (int) json_decode($request->branchId);
            array_push($branchIdArray, (int) json_decode($request->branchId));
        }



        $projects = DB::table('gnr_project')->where('companyId',$user_company_id)->get();
        $projectTypes = DB::table('gnr_project_type')->where('companyId',$user_company_id)->where('projectId',$projectSelected)->get();
        $branches = DB::table('gnr_branch')->where('companyId',$user_company_id)->where('projectId',$projectSelected)->orWhere('id',1)->get();




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
        $softwareStartDate = DB::table('gnr_branch')->where('companyId',$user_company_id)->where('id', $user_branch_id)->value('softwareStartDate');

        //changing format
        $softwareStartDate = Carbon::parse($softwareStartDate)->format('d-m-Y');

        // $softwareStartDate = DB::table('gnr_branch')->where('id', $user_branch_id)->value('softwareStartDate');

        // $softwareStartDate = Carbon::parse($softwareStartDate)->format('d-m-Y');

        
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
            'softwareStartDate'     => $softwareStartDate,
            'ledgerIdsArray'        => $ledgerIdsArray

        );


        return view('accounting.reports.bankBookReport', $data);
    }



    public function cashNbankBookReportCopy(Request $request){
        $user_company_id = Auth::user()->company_id_fk;
        $headOfficeId = DB::table('gnr_branch')->where('companyId', $user_company_id)->where('branchCode', 0)->value('id');
        $user_branch_id = Auth::user()->branchId;
        $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');
        $user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectTypeId');

        //Initializing Different array
        $projectIdArray = array();
        $projectTypeIdArray = array();
        $branchIdArray = array();
        $ledgerIdsArray = array();


        $allLedgers = DB::table('acc_account_ledger')->where('companyIdFk',$user_company_id)->where('isGroupHead', 0)->whereIn('accountTypeId', [4, 5])->select("id","projectBranchId")->get();

        // if($user_branch_id!=1){
        //     $matchedId=array();
        //     foreach ($allLedgers as $singleLedger) {
        //         $splitArray=str_replace(array('[', ']', '"', ''), '',  $singleLedger->projectBranchId);

        //         $splitArrayFirstValue = explode(",", $splitArray);
        //         $splitArraySecondValue = explode(":", $splitArrayFirstValue[0]);

        //         $array_length=substr_count($splitArray, ",");
        //         $arrayProjects=array();
        //         $temp=null;
        //         // $temp1=null;
        //         for($i=0; $i<$array_length+1; $i++){

        //             $splitArrayFirstValue = explode(",", $splitArray);

        //             $splitArraySecondValue = explode(":", $splitArrayFirstValue[$i]);
        //             $firstIndexValue=(int)$splitArraySecondValue[0];
        //             $secondIndexValue=(int)$splitArraySecondValue[1];

        //             if($firstIndexValue==0){
        //                 if($secondIndexValue==0){
        //                     array_push($matchedId, $singleLedger->id);
        //                 }
        //             }else{
        //                 if($firstIndexValue==$user_project_id){
        //                     if($secondIndexValue==0){
        //                         array_push($matchedId, $singleLedger->id);
        //                     }elseif($secondIndexValue==$user_branch_id ){
        //                         array_push($matchedId, $singleLedger->id);
        //                     }
        //                 }
        //             }
        //         }   //for
        //     }       //foreach
        //     $childrenLedger = DB::table('acc_account_ledger')->whereIn('id', $matchedId )->select('id','name','code')->orderBy('code')->get();
        // }else{
        //     $childrenLedger = DB::table('acc_account_ledger')->where('isGroupHead', 0 )->where('accountTypeId', 5)->select('id','name','code')->orderBy('code')->get();
        // }

        $matchedId=array();

        if($headOfficeId){

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
                    }
                    // elseif($firstIndexValue==$user_project_id){
                    //     if($secondIndexValue==$user_branch_id){
                    //         array_push($matchedId, $singleLedger->id);
                    //     }
                    // }
                    else{
                        // if($firstIndexValue!=$temp){
                            if($firstIndexValue==$request->projectId){
                                    if($secondIndexValue==0){
                                        array_push($matchedId, $singleLedger->id);
                                    }elseif($secondIndexValue==$request->branchId){
                                        array_push($matchedId, $singleLedger->id);
                                    }elseif($request->branchId == null){
                                        array_push($matchedId, $singleLedger->id);
                                    }elseif($request->branchId == 0){
                                        if($secondIndexValue != 1){
                                            array_push($matchedId, $singleLedger->id);
                                        }
                                    }
                            }
                    }

                }   //for
            } //foreach
        }else{
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
            $childrenLedger = DB::table('acc_account_ledger')->where('companyIdFk',$user_company_id)->whereIn('id', $matchedId )->orderBy('code','asc')->select('id','name','code')->get();
        }
        // Getting ledger details based on first request
        if(isset($request->projectId)){
            $childrenLedger = DB::table('acc_account_ledger')->where('companyIdFk',$user_company_id)->whereIn('id', $matchedId )->orderBy('code','asc')->select('id','name','code')->get();
        }elseif($headOfficeId){
            $childrenLedger = DB::table('acc_account_ledger')->where('companyIdFk',$user_company_id)->where('isGroupHead', 0)->whereIn('accountTypeId', [4, 5])->select('id','name','code')->orderBy('code')->get();
        }
        // else{
        //     $childrenLedger = DB::table('acc_account_ledger')->whereIn('id', $matchedId )->orderBy('code','asc')->select('id','name','code')->get();
        // }


        // Assigning ledger id to ledgerIdsArray based on request
        if($request->ledgerId){
            $ledgerIdsArray = [$request->ledgerId];
        }else{
            $ledgerIdsArray = $matchedId;
            // dd($ledgerIdsArray);
        }


        if(isset($request->projectId)){
             // dd($matchedId);
        }




        //Project
        if ($request->projectId==null) {
            if ($headOfficeId) {
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
            if ($headOfficeId) {
                $projectTypeSelected = null;
                $projectTypeIdArray = DB::table('gnr_project_type')->where('companyId',$user_company_id)->pluck('id')->toArray();
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
            if ($headOfficeId) {
                $branchSelected = null;
                $branchIdArray = DB::table('gnr_branch')->where('companyId',$user_company_id)->whereIn('projectId', $projectIdArray)->orWhere('id', 1)->pluck('id')->toArray();
            }
            else{
                $branchSelected = (int) json_decode($user_branch_id);
                array_push($branchIdArray, (int) json_decode($user_branch_id));
            }
        }elseif ($request->branchId==0) {
            $branchSelected = 0;
            $branchIdArray = DB::table('gnr_branch')->where('companyId',$user_company_id)->whereIn('projectId', $projectIdArray)->where('id', '!=', 1)->pluck('id')->toArray();
        }
        else{
            $branchSelected = (int) json_decode($request->branchId);
            array_push($branchIdArray, (int) json_decode($request->branchId));
        }



        $projects = DB::table('gnr_project')->get();
        $projectTypes = DB::table('gnr_project_type')->where('companyId',$user_company_id)->where('projectId',$projectSelected)->get();
        $branches = DB::table('gnr_branch')->where('companyId',$user_company_id)->where('projectId',$projectSelected)->orWhere('id',1)->get();




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
        $softwareStartDate = DB::table('gnr_branch')->where('companyId',$user_company_id)->where('id', $user_branch_id)->value('softwareStartDate');

        //changing format
        $softwareStartDate = Carbon::parse($softwareStartDate)->format('d-m-Y');

        // $softwareStartDate = DB::table('gnr_branch')->where('id', $user_branch_id)->value('softwareStartDate');

        // $softwareStartDate = Carbon::parse($softwareStartDate)->format('d-m-Y');

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
            'softwareStartDate'     => $softwareStartDate,
            'ledgerIdsArray'        => $ledgerIdsArray

        );


        return view('accounting.reports.cashNbankBookReport', $data);
    }
    
    
    public function cashNbankBookReport(Request $request){
        //dd('ok');
        $dbhelper = new DatabasePartitionHelper();
        $acc_opening_balance = $dbhelper->getUserPartitionWiseDBTableName('acc_opening_balance');        
        $acc_voucher = $dbhelper->getUserPartitionWiseDBTableName('acc_voucher');
        $acc_voucher_join = $dbhelper->getPartitionWiseDBTableNameForJoin('acc_voucher', 'av');

        $user_company_id = Auth::user()->company_id_fk;
        $headOfficeId = DB::table('gnr_branch')->where('companyId', $user_company_id)->where('branchCode', 0)->value('id');
        $user_branch_id = Auth::user()->branchId;
        $user_project_id = (int) DB::table('gnr_branch')->where('companyId',$user_company_id)->where('id',$user_branch_id)->value('projectId');
        $user_project_type_id = (int) DB::table('gnr_branch')->where('companyId',$user_company_id)->where('id',$user_branch_id)->value('projectTypeId');
        $userBranch = Auth::user()->branch;
        $projectIdArray = array();
        $projectTypeIdArray = array();
        $branchIdArray = array();
        $ledgerIdsArray = array();

        $allLedgers = DB::table('acc_account_ledger')->where('companyIdFk', $user_company_id)->where('isGroupHead', 0)->whereIn('accountTypeId', [4, 5])->select("id","projectBranchId")->get();

        $matchedId=array();

        if($headOfficeId){

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
                    }
                    // elseif($firstIndexValue==$user_project_id){
                    //     if($secondIndexValue==$user_branch_id){
                    //         array_push($matchedId, $singleLedger->id);
                    //     }
                    // }
                    else{
                        // if($firstIndexValue!=$temp){
                            if($firstIndexValue==$request->projectId){
                                    if($secondIndexValue==0){
                                        array_push($matchedId, $singleLedger->id);
                                    }elseif($secondIndexValue==$request->branchId){
                                        array_push($matchedId, $singleLedger->id);
                                    }elseif($request->branchId == null){
                                        array_push($matchedId, $singleLedger->id);
                                    }elseif($request->branchId == 0){
                                        if($secondIndexValue != 1){
                                            array_push($matchedId, $singleLedger->id);
                                        }
                                    }
                            }
                    }

                }   //for
            } //foreach
        }else{
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
            $childrenLedger = DB::table('acc_account_ledger')->where('companyIdFk', $user_company_id)->whereIn('id', $matchedId )->orderBy('code','asc')->select('id','name','code')->get();
        }
        // Getting ledger details based on first request
        if(isset($request->projectId)){
            $childrenLedger = DB::table('acc_account_ledger')->where('companyIdFk', $user_company_id)->whereIn('id', $matchedId )->orderBy('code','asc')->select('id','name','code')->get();
        }elseif($headOfficeId){
            $childrenLedger = DB::table('acc_account_ledger')->where('companyIdFk', $user_company_id)->where('isGroupHead', 0)->whereIn('accountTypeId', [4, 5])->select('id','name','code')->orderBy('code')->get();
        }
        // else{
        //     $childrenLedger = DB::table('acc_account_ledger')->whereIn('id', $matchedId )->orderBy('code','asc')->select('id','name','code')->get();
        // }


        // Assigning ledger id to ledgerIdsArray based on request
        if($request->ledgerId){
            $ledgerIdsArray = [$request->ledgerId];
        }else{
            $ledgerIdsArray = $matchedId;
            // dd($ledgerIdsArray);
        }


        if(isset($request->projectId)){
             // dd($matchedId);
        }




        //Project
        if ($request->projectId==null) {
            if (!$headOfficeId) {
                $projectSelected = 1;
                $projectIdArray = DB::table('gnr_project')->where('companyId', $user_company_id)->pluck('id')->toArray();
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
            if ($headOfficeId) {
                $projectTypeSelected = null;
                $projectTypeIdArray = DB::table('gnr_project_type')->where('companyId', $user_company_id)->pluck('id')->toArray();
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
            if ($headOfficeId) {
                $branchSelected = null;
                $branchIdArray = DB::table('gnr_branch')->where('companyId', $user_company_id)->whereIn('projectId', $projectIdArray)->orWhere('id', 1)->pluck('id')->toArray();
            }
            else{
                $branchSelected = (int) json_decode($user_branch_id);
                array_push($branchIdArray, (int) json_decode($user_branch_id));
            }
        }elseif ($request->branchId==0) {
            $branchSelected = 0;
            $branchIdArray = DB::table('gnr_branch')->where('companyId', $user_company_id)->whereIn('projectId', $projectIdArray)->where('id', '!=', 1)->pluck('id')->toArray();
        }
        else{
            $branchSelected = (int) json_decode($request->branchId);
            array_push($branchIdArray, (int) json_decode($request->branchId));
        }
            if ($userBranch->branchCode != 0) {
            $projects = GnrProject::where('id', $userBranch->projectId)->pluck('name', 'id')->toarray();
            $projectTypes = DB::table('gnr_project_type')->where('projectId', $userBranch->projectId)->pluck('name', 'id')->toarray();
            $branchLists = DB::table('gnr_branch')
                            ->where('id', Auth::user()->branchId)
                            ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                            ->toArray();
            $userBranchStartDate = GnrBranch::where('id', $userBranch->id)->value('aisStartDate');
            $lastMonthEndDate = AccMonthEnd::active()->where('branchIdFk', $userBranch->id)->max('date');
            if ($lastMonthEndDate) {
                $userBranchDate = Carbon::parse($lastMonthEndDate)->addDay()->endOfMonth()->format('Y-m-d');
            } else {
                $userBranchDate = Carbon::parse($userBranchStartDate)->endOfMonth()->format('Y-m-d');
            }
        } else {
            $projects = /*['0' => 'All'] +*/ GnrProject::where('companyId',Auth::user()->company_id_fk)->pluck('name', 'id')->toarray();
            $projectTypes = DB::table('gnr_project_type')->where('companyId',Auth::user()->company_id_fk)->pluck('name', 'id')->toarray();
            $branchLists = DB::table('gnr_branch')
                            ->where('id', Auth::user()->branchId)
                            ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                            ->toArray();
           
        }
        
        //dd($projects,$projectTypes,$branchLists);
       
       // $headOfficeId = DB::table('gnr_branch')->where('companyId', $user_company_id)->where('branchCode', 0)->value('id');

        $projectLists = DB::table('gnr_project')->where('companyId', $user_company_id)->get();
        // $projectTypes = DB::table('gnr_project_type')->where('projectId',$projectSelected)->get();
        // $branches = DB::table('gnr_branch')->where('projectId',$projectSelected)->orWhere('id',1)->get();


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
        $softwareStartDate = DB::table('gnr_branch')->where('companyId', $user_company_id)->where('id', $user_branch_id)->value('softwareStartDate');

        //changing format
        $softwareStartDate = Carbon::parse($softwareStartDate)->format('d-m-Y');

        // $softwareStartDate = DB::table('gnr_branch')->where('id', $user_branch_id)->value('softwareStartDate');

        // $softwareStartDate = Carbon::parse($softwareStartDate)->format('d-m-Y');
         $companyBranches = GnrBranch::where('companyId', $user_company_id)->pluck('id')->toArray();
      
        $userBranchStartDate = GnrBranch::where('companyId', $user_company_id)->min('aisStartDate');
        //dd($userBranchStartDate);
        $lastMonthEndDate = AccMonthEnd::active()->whereIn('branchIdFk', $companyBranches)->max('date');

        if ($lastMonthEndDate) {
            $userBranchDate = Carbon::parse($lastMonthEndDate)->addDay()->endOfMonth()->format('Y-m-d');
        } else {
            $userBranchDate = Carbon::parse(GnrBranch::where('companyId', $user_company_id)->max('aisStartDate'))->endOfMonth()->format('Y-m-d');
        }

        $currentFiscalYear = DB::table('gnr_fiscal_year')
                            ->where('companyId', $user_company_id)
                            ->where('fyStartDate', '<=', $userBranchDate)
                            ->where('fyEndDate', '>=', $userBranchDate)
                            ->select('id','name', 'fyStartDate', 'fyEndDate')
                            ->first();
                            //dd($projectTypes);

        $startDate = $currentFiscalYear != null ? $currentFiscalYear->fyStartDate : $userBranchDate;
        $userBranchId = Auth::user()->branchId;
        $userBranchData = DB::table('gnr_branch')
                        ->where('id', $userBranchId)
                        ->select('id', DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"))
                        ->first();  
        //dd($userBranchData);
        
        $data = array(
            'user_company_id'        => $user_company_id,
            'user_branch_id'        => $user_branch_id,
            'user_project_id'       => $user_project_id,
            'user_project_type_id'  => $user_project_type_id,
            'childrenLedger'        => $childrenLedger,
            'projects'              => $projects,
            'projectTypes'          => $projectTypes,
            'branchLists'           => $branchLists,
            'projectLists'          => $projectLists,
          
            'headOfficeId'          => $headOfficeId,
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
            'softwareStartDate'     => $softwareStartDate,
            'ledgerIdsArray'        => $ledgerIdsArray,
            
            'acc_opening_balance' => $acc_opening_balance,
            'acc_voucher'         => $acc_voucher,
            'acc_voucher_join'    => $acc_voucher_join,

            'startDate'           => $startDate,
            'userBranchDate'      => $userBranchDate,
            'userBranchData'      => $userBranchData,
            'userBranchStartDate' => $userBranchStartDate
        );

        
        return view('accounting.reports.cashNbankBookReport', $data);
    }



}       //End AccLedgerReportsController
