<?php

namespace App\Http\Controllers\accounting\reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\accounting\AddVoucher;
use App\accounting\AddVoucherDetails;
use App\accounting\AddLedger;
use App\accounting\AddVoucherType;
use App\accounting\AddAccountType;
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


class AccVoucherRegisterReportController extends Controller
{
    
 	public function voucherRegister(Request $request) {

        $user_branch_id = Auth::user()->branchId;
        $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');
        $user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectTypeId');

        if($request->checkFirstLoad==null){
            $checkFirstLoad=0;
        }else{
            $checkFirstLoad=(int)$request->checkFirstLoad;
        }

        // $checkFirstLoad=0;
        
        // echo "checkFirstLoad $checkFirstLoad"; exit();
        $searchedProjectId=$request->projectId;
        $searchedProjectTypeId=$request->projectTypeId;
        $searchedBranchId=$request->branchId;
        $searchedVoucherTypeId=$request->voucherTypeId;
        $searchedDateFrom=$request->dateFrom;
        $searchedDateTo=$request->dateTo;

        $projectIdArray = array();
        $projectTypeIdArray = array();
        $branchIdArray = array();
        $voucherTypeIdArray = array();


         //Project
        if ($searchedProjectId==null) {
            if ($user_branch_id == 1) {
                $projectSelected = null;
                $projectIdArray = DB::table('gnr_project')->pluck('id')->toArray();
            }else{
                $projectSelected= (int) json_decode($user_project_id);
                array_push($projectIdArray, (int) json_decode($user_project_id));
            }
        }else{
            $projectSelected= (int) json_decode($searchedProjectId);
            array_push($projectIdArray, (int) json_decode($searchedProjectId));
        }

        //Project Type                        
        if ($searchedProjectTypeId==null) {
            if ($user_branch_id == 1) {
                $projectTypeSelected = null;
                $projectTypeIdArray = DB::table('gnr_project_type')->pluck('id')->toArray();
            }else{
                $projectTypeSelected=(int) json_decode($user_project_type_id);
                array_push($projectTypeIdArray, (int) json_decode($user_project_type_id));
                // dd($projectTypeIdArray);
            }
        }else{
            $projectTypeSelected=(int) json_decode($searchedProjectTypeId);
            array_push($projectTypeIdArray, (int) json_decode($searchedProjectTypeId));
        }

        //Branch
        if ($searchedBranchId==null) {
            if ($user_branch_id == 1) {
                if ($checkFirstLoad ==0) {
                    $branchSelected = 1;
                    array_push($branchIdArray, 1);
                }else{
                    $branchSelected = null;
                    $branchIdArray = DB::table('gnr_branch')->whereIn('projectId', $projectIdArray)->orWhere('id', 1)->pluck('id')->toArray();
                }
            }else{
                $branchSelected=(int) json_decode($user_branch_id);
                array_push($branchIdArray, (int) json_decode($user_branch_id));
            }
        }elseif ($searchedBranchId==0) {
            $branchSelected = 0;
            $branchIdArray = DB::table('gnr_branch')->whereIn('projectId', $projectIdArray)->where('id', '!=', 1)->pluck('id')->toArray();
        }else{
            $branchSelected=(int) json_decode($searchedBranchId);
            array_push($branchIdArray, (int) json_decode($searchedBranchId));
        }


        //voucherTypeId
        if ($searchedVoucherTypeId==null) {
            $voucherTypeIdArray = DB::table('acc_voucher_type')->pluck('id')->toArray();
        }else{
            array_push($voucherTypeIdArray, (int) json_decode($searchedVoucherTypeId));
        }

        if($searchedDateFrom==null && $searchedDateTo==null){
            $toDate = date("Y-m-d");
            $startDate=DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$toDate)->where('fyEndDate','>=',$toDate)->value('fyStartDate');
            // $startDate = date('Y-m-d', strtotime("-1 week +1 day".$toDate));
            $endDate = $toDate;
            $startDateSelected=date('d-m-Y',strtotime($startDate));
            $endDateSelected=date('d-m-Y',strtotime($endDate));
        }else{
            $startDate = date('Y-m-d',strtotime($searchedDateFrom));
            $endDate = date('Y-m-d',strtotime($searchedDateTo));
            $startDateSelected=$searchedDateFrom;
            $endDateSelected=$searchedDateTo;
        }
        // echo $startDate." ".$endDate;

        $projects = GnrProject::select('id','name','projectCode')->get();
        $branches = GnrBranch::select('id','name','branchCode')->whereIn('projectId',$projectIdArray)->orWhere('id',1)->get();
        $projectTypes = GnrProjectType::select('id','name','projectTypeCode')->whereIn('projectId',$projectIdArray)->get();
        $voucherTypes=DB::table('acc_voucher_type')->select('id','shortName')->get();
        // $vouchers=DB::table('acc_voucher')->whereIn('branchId', $branches)->whereIn('projectId', $projects)->whereIn('')->select('id','shortName')->get();

        if(isset($request->projectId)){
            $firstLoad = 1;
        }else{
            $firstLoad = 0;
        }


        // if(!$firstLoad){
        //      $data = array(
        //         // 'checkFirstLoad'            => $checkFirstLoad,
        //         'firstLoad'                 => $firstLoad,
        //         'projectSelected'           => $projectSelected,
        //         'projectTypeSelected'       => $projectTypeSelected,
        //         'branchSelected'            => $branchSelected,
        //         'startDateSelected'         => $startDateSelected,
        //         'endDateSelected'           => $endDateSelected,
        //         // 'searchedVoucherTypeId'     => $searchedVoucherTypeId,
        //         'user_branch_id'            => $user_branch_id,
        //         'user_project_id'           => $user_project_id,
        //         'user_project_type_id'      => $user_project_type_id,
        //         'projects'                  => $projects,
        //         'branches'                  => $branches, 
        //         'projectTypes'              => $projectTypes, 
        //         'voucherTypes'              => $voucherTypes
        //         // 'searchedProjectId'         => $searchedProjectId,
        //         // 'searchedProjectTypeId'    => $searchedProjectTypeId,
        //         // 'searchedBranchId'          => $searchedBranchId,
        //         // 'searchedVoucherTypeId'     => $searchedVoucherTypeId,
        //         // 'searchedDateFrom'          => $searchedDateFrom,
        //         // 'searchedDateTo'            => $searchedDateTo
        //     // 'vouchers'                  => $vouchers
        //     );
        // }else{
            if($user_branch_id == 1){
            $vouchers = DB::table('acc_voucher')
                        ->whereIn('projectId', $projectIdArray)
                        ->whereIn('projectTypeId', $projectTypeIdArray)
                        ->whereIn('branchId', $branchIdArray)
                        ->whereIn('voucherTypeId', $voucherTypeIdArray)
                        ->where('status', 1)
                        ->where('authBy', '!=', 0)
                        ->where(function ($query) use ($startDate,$endDate){
                            $query->where('voucherDate','>=',$startDate)
                            ->where('voucherDate','<=',$endDate);
                        })
                        ->orderBy('voucherDate', 'asc') 
                        ->paginate(10000);
            }else{
                $vouchers = DB::table('acc_voucher')
                            ->whereIn('projectId', $projectIdArray)
                            ->whereIn('branchId', $branchIdArray)
                            ->whereIn('voucherTypeId', $voucherTypeIdArray)
                            ->where('status', 1)
                            ->where('authBy', '!=', 0)
                            ->where(function ($query) use ($startDate,$endDate){
                                $query->where('voucherDate','>=',$startDate)
                                ->where('voucherDate','<=',$endDate);
                            })
                            ->orderBy('voucherDate', 'asc') 
                            ->paginate(10000);
            }

            $data = array(
                'checkFirstLoad'            => $checkFirstLoad,
                'firstLoad'                 => $firstLoad,
                'projectSelected'           => $projectSelected,
                'projectTypeSelected'       => $projectTypeSelected,
                'branchSelected'            => $branchSelected,
                'startDateSelected'         => $startDateSelected,
                'endDateSelected'           => $endDateSelected,
                'searchedVoucherTypeId'     => $searchedVoucherTypeId,
                'user_branch_id'            => $user_branch_id,
                'user_project_id'           => $user_project_id,
                'user_project_type_id'      => $user_project_type_id,
                'projects'                  => $projects,
                'branches'                  => $branches, 
                'projectTypes'              => $projectTypes, 
                'voucherTypes'              => $voucherTypes, 
                'vouchers'                  => $vouchers,
                'searchedProjectId'         => $searchedProjectId,
                'searchedProjectTypeId'    => $searchedProjectTypeId,
                'searchedBranchId'          => $searchedBranchId,
                'searchedVoucherTypeId'     => $searchedVoucherTypeId,
                'searchedDateFrom'          => $searchedDateFrom,
                'searchedDateTo'            => $searchedDateTo
           
            );

        // }

        
        


        return view('accounting.reports.viewVoucherRegister', $data);
    

    }       //End voucherTest function

}		//End AccVoucherRegisterReportController

