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

class SavingRefundRegisterController extends Controller {
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

    	return view('microfin.reports.registerReport.regular.savingRefundRegister.reportFilteringPart', $filteringArray);
    }

    public function getReport(Request $req){
        $userBranchId=Auth::user()->branchId;
        $dateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
        $filDateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');

        $allSavingsWithdraws = DB::table('mfn_savings_withdraw')
                          ->where('softDel',0);
    
        if((int)$req->filBranch > 0) {

         $allSavingsWithdraws = $allSavingsWithdraws->where('branchIdFk',$req->filBranch);

        } elseif ($userBranchId!=1) {
          
            $allSavingsWithdraws = $allSavingsWithdraws->where('branchIdFk',$userBranchId);
        } 

        if((int)$req->filCategory > 0) {
        $productIdArr = DB::table('mfn_loans_product')->where('productCategoryId',$req->filCategory)->pluck('id')->toArray();
        $allSavingsWithdraws = $allSavingsWithdraws->whereIn('primaryProductIdFk',$productIdArr);   
        }
       if((int)$req->filLoanProduct > 0) {
        $allSavingsWithdraws = $allSavingsWithdraws->where('primaryProductIdFk',$req->filLoanProduct);   
        }
        if($dateTo !='' &&  $filDateFrom !='') {
        $allSavingsWithdraws = $allSavingsWithdraws->where('withdrawDate','>=',$filDateFrom)
                                ->where('withdrawDate','<=',$dateTo); 
        }
        if((int)$req->filSavingsProduct > 0) {
        $allSavingsWithdraws = $allSavingsWithdraws->where('productIdFk',$req->filSavingsProduct);   
        }

        if($req->filOrderByChange == 1) {
         $allSavingsWithdraws = $allSavingsWithdraws->orderBy('samityIdFk');   
        }
        if($req->filOrderByChange == 2) {
         $allSavingsWithdraws = $allSavingsWithdraws->orderBy('withdrawDate');   
        }
       
        $allSavingsWithdraws = $allSavingsWithdraws->get();
      
      //START ALL SAMITY ARRAY=====
        $allMembers = DB::table('mfn_member_information')
                          ->whereIn('id',$allSavingsWithdraws->pluck('memberIdFk'))
                          ->select('id','code','name')
                          ->get();
        //END ALL SAMITY ARRAY=====
       //START ALL SAMITY ARRAY=====
        $allSamity = DB::table('mfn_samity')
                          ->whereIn('id',$allSavingsWithdraws->pluck('samityIdFk'))
                          ->select('id','code','name')
                          ->get();
        //END ALL SAMITY ARRAY=====
        //START ALL SAVINGS ACCOUNTS ARRAY=====
        $allSavingsAccounts = DB::table('mfn_savings_account')
                          ->whereIn('id',$allSavingsWithdraws->pluck('accountIdFk'))
                          ->select('id','savingsCode')
                          ->get();
        //END ALL SAVINGS ACCOUNTS ARRAY=====
        //START ALL SAVINGS DEPOSITS ARRAY=====
        $allSavingsDeposits = DB::table('mfn_savings_deposit')
                          ->whereIn('accountIdFk',$allSavingsWithdraws->pluck('accountIdFk'))
                          ->select('accountIdFk','depositDate','amount')
                          ->get();
        //END ALL SAVINGS DEPOSITS ARRAY=====

        $data = array(
            'allSavingsWithdraws'  => $allSavingsWithdraws,
            'allMembers'           => $allMembers,
            'allSamity'            => $allSamity,
            'allSavingsAccounts'   => $allSavingsAccounts,
            'allSavingsDeposits'   => $allSavingsDeposits
        );
        
        return view('microfin.reports.registerReport.regular.savingRefundRegister.reportPage', $data);
        
    }

}
