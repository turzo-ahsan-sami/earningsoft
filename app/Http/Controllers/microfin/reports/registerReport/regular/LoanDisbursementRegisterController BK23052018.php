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

class LoanDisbursementRegisterController extends Controller {
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

    	return view('microfin.reports.registerReport.regular.loanDisbursementRegister.reportFilteringPart', $filteringArray);
    }

    public function getReport(Request $req) {

        $userBranchId=Auth::user()->branchId;
        $dateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
        $filDateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');

        $allDisbursementLoans = DB::table('mfn_loan')
                          ->where('softDel',0);

        
        if((int)$req->filBranch > 0) {

         $allDisbursementLoans = $allDisbursementLoans->where('branchIdFk',$req->filBranch);

        } elseif ($userBranchId!=1) {
            $allDisbursementLoans = $allDisbursementLoans->where('branchIdFk',$userBranchId);
        } 
        
        if((int)$req->filCategory > 0) {
        $productIdArr = DB::table('mfn_loans_product')->where('productCategoryId',$req->filCategory)->pluck('id')->toArray();
        $allDisbursementLoans = $allDisbursementLoans->whereIn('productIdFk',$productIdArr);   
        }

        if((int)$req->filLoanProduct > 0) {
        $allDisbursementLoans = $allDisbursementLoans->where('productIdFk',$req->filLoanProduct);   
        }

        if($dateTo !='' &&  $filDateFrom !='') {
        $allDisbursementLoans = $allDisbursementLoans->where('disbursementDate','>=',$filDateFrom)
                                ->where('disbursementDate','<=',$dateTo); 
        }

        $inactiveMemberIds = DB::table('mfn_member_closing')
                                  ->where('closingDate','<=',$filDateFrom)
                                  ->pluck('memberIdFk')->all();

        if ($req->filByMemberStatusChange == 1) {
           $allDisbursementLoans = $allDisbursementLoans->whereNotIn('memberIdFk',$inactiveMemberIds);  
        }
        
        if ($req->filByMemberStatusChange == 2) {
           $allDisbursementLoans = $allDisbursementLoans->whereIn('memberIdFk',$inactiveMemberIds);  
        }
        
        if($req->filByLoanTypesChange == 1) {
         $allDisbursementLoans = $allDisbursementLoans->where('loanCycle',1);   
        }

        if($req->filByLoanTypesChange == 2) {
         $allDisbursementLoans = $allDisbursementLoans->where('loanCycle','>',1);   
        }


        if($req->filOrderByChange == 1) {
         $allDisbursementLoans = $allDisbursementLoans->orderBy('samityIdFk');   
        }

        if($req->filOrderByChange == 2) {
         $allDisbursementLoans = $allDisbursementLoans->orderBy('disbursementDate');   
        }

        if($req->filByLoanDisbursementChange == 'Cash') {
         $allDisbursementLoans = $allDisbursementLoans->where('paymentTypeIdFk',$req->filByLoanDisbursementChange);   
        }

        if($req->filByLoanDisbursementChange == 'Bank') {
         $allDisbursementLoans = $allDisbursementLoans->where('paymentTypeIdFk',$req->filByLoanDisbursementChange);   
        }

        $allDisbursementLoans = $allDisbursementLoans->get();
      
      //START ALL SAMITY ARRAY=====
        $allMembers = DB::table('mfn_member_information')
                          ->whereIn('id',$allDisbursementLoans->pluck('memberIdFk'))
                          ->select('id','code','name','spouseFatherSonName','nID','mobileNo','birthRegNo')
                          ->get();
        //END ALL SAMITY ARRAY=====

       //START ALL SAMITY ARRAY=====
       $allSamity = DB::table('mfn_samity')
                          ->whereIn('id',$allDisbursementLoans->pluck('samityIdFk'))
                          ->select('id','code','name','workingAreaId')
                          ->get();
      //END ALL SAMITY ARRAY=====

      //START ALL VILLAGE AND WORKINGAREA ID AND VILLAGEID ARRAY=====
        $villageList = DB::table('gnr_village')->pluck('name','id');
        $workingAreaVillageId = DB::table('gnr_working_area')->pluck('villageId','id');
      //END ALL VILLAGE AND WORKINGAREA ID AND VILLAGEID ARRAY=====
      
      //START LOANS SUB PURPOSE COLLECTION ======
      $allLoanPurpose = DB::table('mfn_loans_sub_purpose')
                          ->whereIn('id',$allDisbursementLoans->pluck('loanSubPurposeIdFk'))
                          ->select('id','name')
                          ->get();
      //END LOANS SUB PURPOSE COLLECTION ========

       //START COLLECT ALL LOAN PRODUCT ======
      $allLoanProducts = DB::table('mfn_loans_product')
                          ->whereIn('id',$allDisbursementLoans->pluck('productIdFk'))
                          ->select('id','name')
                          ->get();
      //END COLLECT ALL LOAN PRODUCT ======== 

      //START COLLECT ALL LOAN RE-PAY PERIOD ======
      $allLoanRepayPerionds = DB::table('mfn_loan_repay_period')
                          ->whereIn('id',$allDisbursementLoans->pluck('loanRepayPeriodIdFk'))
                          ->select('id','inMonths')
                          ->get();
      //END COLLECT ALL LOAN RE-PAY PERIOD ========   

              /// Branch
              $branchList = DB::table('gnr_branch');

              if ($userBranchId!=1) {
                  $branchList = $branchList->where('id', $userBranchId);
              }

              if($userBranchId!=1) {
                $req->filBranch = $userBranchId;
              }
              $branch = $branchList
                       ->orderBy('branchCode')
                       ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0)) AS code"), 'name','address')
                       ->whereNotIn('id',[1])
                       ->where('id',$req->filBranch) 
                       ->first();
            $allScheduleDates = DB::table('mfn_loan_schedule')
                                ->whereIn('loanIdFk',$allDisbursementLoans->pluck('id'))
                                ->where('scheduleDate','>=',$filDateFrom)
                                ->where('scheduleDate','<',$dateTo)
                                ->select('id','loanIdFk','installmentAmount','actualInstallmentAmount','scheduleDate','isCompleted','isPartiallyPaid','partiallyPaidAmount')
                                ->get();

        $data = array(
            'allDisbursementLoans' => $allDisbursementLoans,
            'allMembers'           => $allMembers,
            'allSamity'            => $allSamity,
            'villageList'          => $villageList,
            'workingAreaVillageId' => $workingAreaVillageId,
            'filByDueStatusChange' => (int)$req->filByDueStatusChange,
            'allLoanPurpose'       => $allLoanPurpose,
            'allLoanProducts'      => $allLoanProducts,
            'allLoanRepayPerionds' => $allLoanRepayPerionds,
            'filBranch'            => $req->filBranch,
            'branch'               => $branch,
            'allScheduleDates'     => $allScheduleDates
        );
        
        return view('microfin.reports.registerReport.regular.loanDisbursementRegister.reportPage',$data);
        
    }

}
