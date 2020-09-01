<?php

namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\accounting\AddVoucher;
use App\accounting\AddVoucherDetails;
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;
use App\gnr\GnrBranch;
use App\accounting\AddLedger;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon;
use App\Http\Controllers\accounting\Accounting;


class AccAuthenticateVoucherController extends Controller {

    protected $Accounting;

    public function __construct() {

        // $this->Accounting = new AccAjaxResponseController;
        $this->Accounting = new Accounting;
    }


    public function filtering($request) {

        $user_branch_id = Auth::user()->branchId;
        $user_branch_code = DB::table('gnr_branch')->where('id', $user_branch_id)->value('branchCode');
        $user_company_id = Auth::user()->company_id_fk;
        $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');
        $user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectTypeId'); 

        if($request->checkFirstLoad==null){
            $checkFirstLoad=0;
        }else{
            $checkFirstLoad=(int)$request->checkFirstLoad;
        }

        $searchedProjectId      =$request->projectId;
        $searchedProjectTypeId  =$request->projectTypeId;
        $searchedBranchId       =$request->branchId;
        $searchedVoucherTypeId  =$request->voucherTypeId;
        $searchedDateFrom       =$request->dateFrom;
        $searchedDateTo         =$request->dateTo;

        $projectIdArray = array();
        $projectTypeIdArray = array();
        $branchIdArray = array();
        $voucherTypeIdArray = array();


        if($user_branch_code==0){
            //project
            if ($searchedProjectId==null) {
                $projectSelected = null;
                $projectIdArray = DB::table('gnr_project')->where('companyId', $user_company_id)->pluck('id')->toArray();
            }else{
                $projectSelected= (int) json_decode($searchedProjectId);
                array_push($projectIdArray, (int) json_decode($searchedProjectId));
            }
            //Project Type                        
            if ($searchedProjectTypeId==null) {
                $projectTypeSelected = null;
                $projectTypeIdArray = DB::table('gnr_project_type')->where('companyId', $user_company_id)->pluck('id')->toArray();
            }else{
                $projectTypeSelected=(int) json_decode($searchedProjectTypeId);
                array_push($projectTypeIdArray, (int) json_decode($searchedProjectTypeId));
            }
            
            //Branch
            if ($searchedBranchId==null) {
                if ($checkFirstLoad ==0) {
                    $branchSelected = $user_branch_id;
                    array_push($branchIdArray, $user_branch_id);
                }else{
                    $branchSelected = null;
                    $branchIdArray = DB::table('gnr_branch')->whereIn('projectId', $projectIdArray)->orWhere('id', $user_branch_id)->pluck('id')->toArray();
                }
            }elseif ($searchedBranchId==0) {
                $branchSelected = 0;
                $branchIdArray = DB::table('gnr_branch')->whereIn('projectId', $projectIdArray)->where('id', '!=', $user_branch_id)->pluck('id')->toArray();
            }else{
                $branchSelected=(int) json_decode($searchedBranchId);
                array_push($branchIdArray, (int) json_decode($searchedBranchId));
            }

        }else{
            //project
            $projectSelected= (int) json_decode($user_project_id);
            array_push($projectIdArray, (int) json_decode($user_project_id));

            //Project Type            
            $projectTypeSelected=(int) json_decode($user_project_type_id);
            array_push($projectTypeIdArray, (int) json_decode($user_project_type_id));
            
            //Branch
            $branchSelected=(int) json_decode($user_branch_id);
            array_push($branchIdArray, (int) json_decode($user_branch_id));
        }


         //Project
        // if ($searchedProjectId==null) {
        //     if ($user_branch_id == 1) {
        //         $projectSelected = null;
        //         $projectIdArray = DB::table('gnr_project')->pluck('id')->toArray();
        //     }else{
        //         $projectSelected= (int) json_decode($user_project_id);
        //         array_push($projectIdArray, (int) json_decode($user_project_id));
        //     }
        // }else{
        //     $projectSelected= (int) json_decode($searchedProjectId);
        //     array_push($projectIdArray, (int) json_decode($searchedProjectId));
        // }

        //Project Type                        
        // if ($searchedProjectTypeId==null) {
        //     if ($user_branch_id == 1) {
        //         $projectTypeSelected = null;
        //         $projectTypeIdArray = DB::table('gnr_project_type')->pluck('id')->toArray();
        //     }else{
        //         $projectTypeSelected=(int) json_decode($user_project_type_id);
        //         array_push($projectTypeIdArray, (int) json_decode($user_project_type_id));
        //     }
        // }else{
        //     $projectTypeSelected=(int) json_decode($searchedProjectTypeId);
        //     array_push($projectTypeIdArray, (int) json_decode($searchedProjectTypeId));
        // }

        //Branch
        // if ($searchedBranchId==null) {
        //     if ($user_branch_id == 1) {
        //         if ($checkFirstLoad ==0) {
        //             $branchSelected = 1;
        //             array_push($branchIdArray, 1);
        //         }else{
        //             $branchSelected = null;
        //             $branchIdArray = DB::table('gnr_branch')->whereIn('projectId', $projectIdArray)->orWhere('id', 1)->pluck('id')->toArray();
        //         }
        //     }else{
        //         $branchSelected=(int) json_decode($user_branch_id);
        //         array_push($branchIdArray, (int) json_decode($user_branch_id));
        //     }
        // }elseif ($searchedBranchId==0) {
        //     $branchSelected = 0;
        //     $branchIdArray = DB::table('gnr_branch')->whereIn('projectId', $projectIdArray)->where('id', '!=', 1)->pluck('id')->toArray();
        // }else{
        //     $branchSelected=(int) json_decode($searchedBranchId);
        //     array_push($branchIdArray, (int) json_decode($searchedBranchId));
        // }


        //voucherTypeId
        if ($searchedVoucherTypeId==null) {
            $voucherTypeIdArray = DB::table('acc_voucher_type')->pluck('id')->toArray();
        }else{
            array_push($voucherTypeIdArray, (int) json_decode($searchedVoucherTypeId));
        }

        if($searchedDateFrom==null && $searchedDateTo==null){
            $toDate = date("Y-m-d");
            $startDate=DB::table('gnr_fiscal_year')->where('companyId', $user_company_id)->where('fyStartDate','<=',$toDate)->where('fyEndDate','>=',$toDate)->value('fyStartDate');
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

        $projectsOption =   array('' => 'All') 
                            +
                            $this->Accounting->getProjectList();

        $branchesOption =   array('' => 'All With HeadOffice', '0' => 'All With Out HeadOffice') 
                            +
                            $this->Accounting->getBranchList();

        $projectTypesOption =   array('' => 'All') 
                                +
                                $this->Accounting->getProjectTypeList();

        $voucherTypesOption =   array('' => 'All') 
                                +
                                $this->Accounting->getVoucherTypeList();

        // $projects = GnrProject::select('id','name','projectCode')->get();
        // $branches = GnrBranch::select('id','name','branchCode')->whereIn('projectId',$projectIdArray)->orWhere('id',1)->get();
        // $projectTypes = GnrProjectType::select('id','name','projectTypeCode')->whereIn('projectId',$projectIdArray)->get();
        // $voucherTypes=DB::table('acc_voucher_type')->select('id','shortName')->get();


        // echo "<br/>projectIdArray: ";var_dump($projectIdArray);
        // echo "<br/>projectTypeIdArray: ";var_dump($projectTypeIdArray);
        // echo "<br/>branchIdArray: ";var_dump($branchIdArray);
        // echo "<br/>voucherTypeIdArray: ";var_dump($voucherTypeIdArray);

        // exit();


        $vouchersObj = DB::table('acc_voucher')
            ->where('companyId', $user_company_id)
            ->whereIn('projectId', $projectIdArray)
            ->whereIn('projectTypeId', $projectTypeIdArray)
            ->whereIn('branchId', $branchIdArray)
            ->whereIn('voucherTypeId', $voucherTypeIdArray)
            ->where('vGenerateType', 2)
            ->where(function ($query) use ($startDate,$endDate){
                $query->where('voucherDate','>=',$startDate)
                ->where('voucherDate','<=',$endDate);
            })
            ->orderBy('voucherDate', 'desc');
            // ->paginate(100);


        $filteringArr=[
            'checkFirstLoad'        => $checkFirstLoad,
            'projectSelected'       => $projectSelected,
            'projectTypeSelected'   => $projectTypeSelected,
            'branchSelected'        => $branchSelected,
            'startDateSelected'     => $startDateSelected,
            'endDateSelected'       => $endDateSelected,
            'searchedVoucherTypeId' => $searchedVoucherTypeId,
            'user_branch_id'        => $user_branch_id,
            'user_project_id'       => $user_project_id,
            'user_project_type_id'  => $user_project_type_id,
            'projectIdArray'        => $projectIdArray,
            'startDate'             => $startDate,
            'endDate'               => $endDate,
            'projectTypeIdArray'    => $projectTypeIdArray,
            'branchIdArray'         => $branchIdArray,
            'voucherTypeIdArray'    => $voucherTypeIdArray,
            'projectsOption'        => $projectsOption,
            'branchesOption'        => $branchesOption, 
            'projectTypesOption'    => $projectTypesOption, 
            'voucherTypesOption'    => $voucherTypesOption, 
            'vouchersObj'           => $vouchersObj,
        ];
        // dd($filteringArr);

        return $filteringArr;




    }   //filtering

    public function authorizedVouchersList(Request $request) {

        $filteringArr=$this->filtering($request);

        $vouchers = $filteringArr['vouchersObj']->where('authBy', '>' , 0)->paginate(100);
        $vouchersIdArr = $filteringArr['vouchersObj']->where('authBy', '>' , 0)->pluck('id')->toArray();

        $reportingArr=$filteringArr+[
                            'vouchers'          => $vouchers,
                            'vouchersIdArr'     => $vouchersIdArr
                        ];

        return view('accounting.vouchers.authorizedVouchersList', $reportingArr);   

    }       //End authorizedVouchersList function


    public function unauthenticateVoucherItem(Request $request) {

        // $requestId=$request->id;
        // $userId=Auth::user()->id;
        // $userBranchId=Auth::user()->branchId;
        // if ($requestId==-1) {
        //     $voucherIdArr=DB::table('acc_voucher')->where('branchId', $userBranchId)->where('authBy','>', 0)->pluck('id')->toArray();
        // }else{
        //     $voucherIdArr=[$requestId];
        // }

        $voucherIdArr=$request->vouchersIdArr;

        // DB::table('acc_voucher')->whereIn('id', $voucherIdArr)->update(array('authBy' => $userId));
        $updateStatus=DB::table('acc_voucher')->whereIn('id', $voucherIdArr)->update(array('authBy' => 0));


        if ($updateStatus) {
            $data = array(
                        'responseTitle' =>  'Success!',
                        'updatedVoucher' =>  $updateStatus,
                        'responseText'  =>  ($updateStatus==1)? $updateStatus.' Voucher Unauthencate Successful.' : $updateStatus.' Vouchers Unauthencate Successful.'
                    );
        }else{
            $data = array(
                        'responseTitle' =>  'Warning!',
                        'responseText'  =>  'Voucher Counld Not Unauthencate.'
                    );          
        }


        // $data = array(  'requestId'                   => $requestId,
        //                 'voucherIdArr'                 => $voucherIdArr,
        //             );

        return response()->json($data);

    }

    public function unauthorizedVouchersList(Request $request) {

        $filteringArr=$this->filtering($request);
        //dd($request->all());
        // $projects = GnrProject::select('id','name','projectCode')->get();
        // $branches = GnrBranch::select('id','name','branchCode')->whereIn('projectId',$filteringArr['projectIdArray'])->orWhere('id',1)->get();
        // $projectTypes = GnrProjectType::select('id','name','projectTypeCode')->whereIn('projectId',$filteringArr['projectIdArray'])->get();
        // $voucherTypes=DB::table('acc_voucher_type')->select('id','shortName')->get();

      

        //dd(Auth::user()->branchId);


        //$vouchers = $filteringArr['vouchersObj']->where('authBy', 0)->paginate(100);
        // dd($vouchers);
        $vouchersIdArr = $filteringArr['vouchersObj']->where('authBy', 0)->pluck('id')->toArray();
       //dd($vouchersIdArr);

        // $vouchers = DB::table('acc_voucher')
        //     ->whereIn('projectId', $filteringArr['projectIdArray'])
        //     ->whereIn('projectTypeId', $filteringArr['projectTypeIdArray'])
        //     ->whereIn('branchId', $filteringArr['branchIdArray'])
        //     ->whereIn('voucherTypeId', $filteringArr['voucherTypeIdArray'])
        //     ->where('authBy', 0)
        //     ->where(function ($query) use ($startDate,$endDate){
        //         $query->where('voucherDate','>=',$startDate)
        //         ->where('voucherDate','<=',$endDate);
        //     })
        //     ->orderBy('voucherDate', 'desc') 
        //     ->paginate(100);

        //($vouchers);

        $branches = GnrBranch::select('id')->where('companyId', Auth::user()->company_id_fk)->pluck('id')->toArray();
    
        $startDate=$filteringArr['startDate'];
        $endDate=$filteringArr['endDate'];

        if($request->all()){
            $vouchers = DB::table('acc_voucher')
                        ->where('voucherTypeId', $request->voucherTypeId)
                        ->where('vGenerateType', 1)
                        ->where('authBy', 0)
                        ->where(function ($query) use ($startDate,$endDate){
                            $query->where('voucherDate','>=',$startDate)
                            ->where('voucherDate','<=',$endDate);    
                        })
                        ->orderBy('voucherDate', 'desc') 
                        ->paginate(100);
        }else{
            $vouchers = DB::table('acc_voucher')
            ->whereIn('branchId', $branches)
            ->where('vGenerateType', 1)
            ->orderBy('voucherDate', 'desc') 
            ->paginate(100);
        }

        $vouchersIdArr = $vouchers->where('authBy', 0)->pluck('id')->toArray();
      
        $reportingArr=$filteringArr+[
            // 'projects'              => $projects,
            // 'branches'              => $branches, 
            // 'projectTypes'          => $projectTypes, 
            // 'voucherTypes'          => $voucherTypes, 
            'vouchers'              => $vouchers,
            'vouchersIdArr'         => $vouchersIdArr
        ];

        return view('accounting.vouchers.unauthorizedVouchersList', $reportingArr);    

    }       //End unauthorizedVouchersList function


    public function authenticateVoucherItem(Request $request) {

        // $requestId=$request->id;
        // $userId=Auth::user()->id;
        $userEmpId=Auth::user()->emp_id_fk;
        // dd($userEmpId);
        // $userBranchId=Auth::user()->branchId;
        // if ($requestId==-1) {
        //     $voucherIdArr=DB::table('acc_voucher')->where('branchId', $userBranchId)->where('authBy', 0)->pluck('id')->toArray();
        // }else{
        //     $voucherIdArr=[$requestId];
        // }
        $voucherIdArr=$request->vouchersIdArr;

        if ($userEmpId != null) {
            $updateStatus = DB::table('acc_voucher')->whereIn('id', $voucherIdArr)->update(array('authBy' => $userEmpId));
        }
        else {
            $updateStatus = DB::table('acc_voucher')->whereIn('id', $voucherIdArr)->update(array('authBy' => 1));
        }
        
        // DB::table('acc_voucher')->whereIn('id', $voucherIdArr)->update(array('authBy' => 0));


        if ($updateStatus) {
            $data = array(
                        'responseTitle' =>  'Success!',
                        'updatedVoucher' =>  $updateStatus,
                        'responseText'  =>  ($updateStatus==1)? $updateStatus.' Voucher Authencate Successful.' : $updateStatus.' Vouchers Authencate Successful.'
                    );
        }else{
            $data = array(
                        'responseTitle' =>  'Warning!',
                        'responseText'  =>  'Voucher Counld Not Authencate.'
                    );          
        }

        // $data = array(
        //                 'key' =>  $key,
        //                 'updateStatus' =>  $updateStatus,
        //                 'responseTitle' =>  'Success!',
        //                 'responseText'  =>  'Voucher Authencate Successful.'
        //             );

        // $data = array(  
        //                 // 'requestId'                   => $requestId,
        //                 'voucherIdArr'                 => $voucherIdArr,
        //                 // 'voucherIdArr'                 =>  json_decode(base64_decode($request->vouchersIdArr)),
        //             );

        return response()->json($data);

    }




}   //END Controller
