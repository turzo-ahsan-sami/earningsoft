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

class MemberCancellationRegisterController extends Controller {
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
                       ->whereNotIn('id',[1]) 
                       ->pluck('nameWithCode', 'id')
                       ->all();
        $loanProductCatagoryList    = MicroFin::getAllProductCategoryList();
        $loanProductList            = MicroFin::getAllLoanProductList();
        $getAllSavingsProductList   = MicroFin::getAllSavingsProductList();
        $filteringArray = array(
            'branchList'                    => $branchList,
            'loanProductCatagoryList'       => $loanProductCatagoryList,
            'loanProductList'               => $loanProductList,
            'getAllSavingsProductList'      => $getAllSavingsProductList,
            'userBranchId'                  => $userBranchId
        );

    	return view('microfin.reports.registerReport.regular.memberCancellationRegister.reportFilteringPart', $filteringArray);
    }

    public function getReport(Request $req) {
        $userBranchId=Auth::user()->branchId;
        $dateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
        $filDateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');

        $allCancelledMembers = DB::table('mfn_member_closing as t1')
                          ->join('mfn_samity as t2','t1.samityIdFk','t2.id')
                          ->join('mfn_member_information as t3','t1.memberIdFk','t3.id')
                          ->where('t1.softDel',0);
    
        if((int)$req->filBranch > 0) {
         $allCancelledMembers = $allCancelledMembers->where('t1.branchIdFk',$req->filBranch);   
        } elseif ($userBranchId!=1) {
          
            $allCancelledMembers = $allCancelledMembers->where('t1.branchIdFk',$userBranchId);
        } 
       
       if((int)$req->filCategory > 0) {
        $productIdArr = DB::table('mfn_loans_product')->where('productCategoryId',$req->filCategory)->pluck('id')->toArray();
        $allCancelledMembers = $allCancelledMembers->whereIn('t1.primaryProductIdFk',$productIdArr);   
        }

        if((int)$req->filLoanProduct > 0) {
        $allCancelledMembers = $allCancelledMembers->where('t1.primaryProductIdFk',$req->filLoanProduct);   
        }

        if($dateTo !='' &&  $filDateFrom !='') {
        $allCancelledMembers = $allCancelledMembers->where('t1.closingDate','>=',$filDateFrom)
                                ->where('t1.closingDate','<=',$dateTo); 
        //dd($allMembers);  
        }


        if($req->filOrderByChange == 1) {
         $allCancelledMembers = $allCancelledMembers->orderBy('t2.code');   
        }
        if($req->filOrderByChange == 2) {
         $allCancelledMembers = $allCancelledMembers->orderBy('t1.closingDate');   
        }
        $allCancelledMembers = $allCancelledMembers->select('t3.name','t3.code','t1.closingDate','t3.admissionDate','t1.primaryProductIdFk','t3.spouseFatherSonName','t2.id','t2.code as samityCode','t2.name as samityName','t2.fieldOfficerId','t1.closedByFk','t1.note')->get();

         //START ALL FIELDOFFICER ARRAY=====
        $allFieldOfficerList = DB::table('hr_emp_general_info')->pluck('emp_name_english','id');
        //END ALL FIELDOFFICER ARRAY=====
        //START ALL PRIMARY PRODUCT ID ARRAY=====
        $allPrimaryProductList = MicroFin::getAllPrimaryProductList();
        //END ALL PRIMARY PRODUCT ID ARRAY=====

        $data = array(
            'allCancelledMembers'   => $allCancelledMembers,
            'allFieldOfficerList'   => $allFieldOfficerList,
            'allPrimaryProductList' => $allPrimaryProductList,
        );
        return view('microfin.reports.registerReport.regular.memberCancellationRegister.reportPage',$data);
        
    }

}
