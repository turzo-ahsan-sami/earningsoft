<?php

namespace App\Http\Controllers\microfin\reports\registerReport\regular;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use App\hr\Exam;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\microfin\MicroFin;
use App\Http\Controllers\microfin\MicroFinance;
use App\microfin\settings\MfnProfession;

class AdmissionRegisterController extends Controller {
    protected $MicroFinance;
    public function __construct() {

            $this->MicroFinance = new MicroFinance;

            
        }
    public function index() {

        $userBranchId=Auth::user()->branchId;
       
        /// Branch
        $branchList = DB::table('gnr_branch');

        if ($userBranchId!=1) {
            $branchList = $branchList->where('id', $userBranchId);
        }
        $branchList = $branchList
                       ->orderBy('branchCode')
                       ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                       ->pluck('nameWithCode', 'id')
                       ->all();
        $loanProductCatagoryList    = MicroFin::getAllProductCategoryList();
        $loanProductList            = MicroFin::getAllLoanProductList();
        if ($userBranchId==1) {
            $allFieldOfficerList        = MicroFin::getAllFieldOfficerList();
        }
        else{
            $allFieldOfficerList        = MicroFin::getBranchWiseFieldOfficerList($userBranchId);
        }
        
        $filteringArray = array(
            'branchList'                    => $branchList,
            'loanProductCatagoryList'       => $loanProductCatagoryList,
            'loanProductList'               => $loanProductList,
            'allFieldOfficerList'           => $allFieldOfficerList,
            'userBranchId'                  => $userBranchId
        );

    	return view('microfin.reports.registerReport.regular.admissionRegiser.reportFilteringPart', $filteringArray);
    }

    public function getReport(Request $req){
        $userBranchId=Auth::user()->branchId;

        $dateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
        $filDateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');

        $allMembers = DB::table('mfn_member_information as t1')
                          ->join('mfn_samity as t2','t1.samityId','t2.id')
                          ->where('t1.softDel',0);
    
        if((int)$req->filBranch > 0) {
         $allMembers = $allMembers->where('t1.branchId',$req->filBranch);   
        } elseif ($userBranchId != 1) {
            $allMembers = $allMembers->where('t1.branchId',$userBranchId);
        } 
       
       if((int)$req->filCategory > 0) {
        $productIdArr = DB::table('mfn_loans_product')->where('productCategoryId',$req->filCategory)->pluck('id')->toArray();
        $allMembers = $allMembers->whereIn('t1.primaryProductId',$productIdArr);   
        }

        if((int)$req->filLoanProduct > 0) {
        $allMembers = $allMembers->where('t1.primaryProductId',$req->filLoanProduct);   
        }

        if($dateTo !='' &&  $filDateFrom !='') {
        $allMembers = $allMembers->where('t1.admissionDate','>=',$filDateFrom)
                                ->where('t1.admissionDate','<=',$dateTo); 
        //dd($allMembers);  
        }

        if((int)$req->filFieldOfficer > 0) {
        $fieldOfficerSamityIdArr = DB::table('mfn_samity')->where('fieldOfficerId',$req->filFieldOfficer)->pluck('id')->toArray();
        $allMembers = $allMembers->whereIn('t1.samityId',$fieldOfficerSamityIdArr);   
        }

        if($req->filOrderByChange == 1) {
         $allMembers = $allMembers->orderBy('t2.code');   
        }
        if($req->filOrderByChange == 2) {
         $allMembers = $allMembers->orderBy('t1.admissionDate');   
        }
        $allMembers = $allMembers->select('t1.name as memberName','t1.code','t1.age','t1.profession','t1.maxEducation','t1.admissionDate','t1.admissionNo','t1.formApplicationNo','t1.branchId','t1.samityId','t1.primaryProductId','t1.spouseFatherSonName','t1.status','t2.id','t2.code as samityCode','t2.name','t2.workingAreaId','t2.fieldOfficerId')->get();

       //START ALL SAMITY ARRAY=====
        /*$allSamity = DB::table('mfn_samity')
                          ->whereIn('id',$allMembers->pluck('samityId'))
                          ->select('id','code','name','workingAreaId','fieldOfficerId')
                          ->get();*/
        //END ALL SAMITY ARRAY=====

        //START ALL EDUCATION LAVEL ARRAY=====
        $educationLevel = $this->MicroFinance->getEducationLevel();
        unset($educationLevel['']);
        //END ALL EDUCATION LAVEL ARRAY=====

        //START ALL PROFESSION ARRAY=====
        $allProfession = DB::table('mfn_profession')->pluck('name','id');
        //END ALL PROFESSION ARRAY=====

        //START ALL FIELDOFFICER ARRAY=====
        $allFieldOfficerList = DB::table('hr_emp_general_info')->pluck('emp_name_english','id');
        //END ALL FIELDOFFICER ARRAY=====
      
      //START ALL VILLAGE AND WORKINGAREA ID AND VILLAGEID ARRAY=====
        $villageList = DB::table('gnr_village')->pluck('name','id');
        $workingAreaVillageId = DB::table('gnr_working_area')->pluck('villageId','id');
      //END ALL VILLAGE AND WORKINGAREA ID AND VILLAGEID ARRAY=====

    //START ALL UNION AND WORKINGAREA ID AND UNIONID ARRAY=====
        $unionList = DB::table('gnr_union')->pluck('name','id');
        $workingAreaUnionId = DB::table('gnr_working_area')->pluck('unionId','id');
    //END ALL UNION AND WORKINGAREA ID AND UNIONID ARRAY=====  

     //START ALL UPZILLA AND WORKINGAREA ID AND UPZILLAID ARRAY=====
        $upzillaList = DB::table('gnr_upzilla')->pluck('name','id');
        $workingAreaUpzillaId = DB::table('gnr_working_area')->pluck('upazilaId','id');
    //END ALL UNION AND WORKINGAREA ID AND UPZILLAID ARRAY=====         

        $data = array(
            'allMembers'            => $allMembers,
            'filBranch'             => $req->filBranch,
            'educationLevel'        => $educationLevel,
            'allProfession'         => $allProfession,
            'allFieldOfficerList'   => $allFieldOfficerList,
            'villageList'           => $villageList,
            'workingAreaVillageId'  => $workingAreaVillageId,
            'unionList'             => $unionList,
            'workingAreaUnionId'    => $workingAreaUnionId,
            'upzillaList'           => $upzillaList,
            'workingAreaUpzillaId'  => $workingAreaUpzillaId,
            'dateFrom'              => $req->filDateFrom,
            'dateTo'               => $req->filDateTo,
            /*'filBranch'          => $branchId,
            'samitys'               => $samitys,
            'allSamity'             => $allSamity,
            'loanProducts'          => $loanProducts,
            'loanCategories'        => $loanCategories,
            'loanInfo'              => $loanInfo,
            'primmaryProductIds'    => $primmaryProductIds*/
        );
        
        return view('microfin.reports.registerReport.regular.admissionRegiser.reportPage', $data);
        
    }

}
