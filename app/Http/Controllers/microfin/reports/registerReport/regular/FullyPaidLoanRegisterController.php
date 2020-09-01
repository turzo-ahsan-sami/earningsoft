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

class FullyPaidLoanRegisterController extends Controller {
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

        return view('microfin.reports.registerReport.regular.fullyPaidLoanRegister.reportFilteringPart', $filteringArray);
    }

    public function getReport(Request $req) {

        $userBranchId=Auth::user()->branchId;
        $dateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
        $filDateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');

        $allFullyPaidLoans = DB::table('mfn_loan')
        ->where('isLoanCompleted',1)
        ->where('softDel',0);


        if((int)$req->filBranch > 0) {

            $allFullyPaidLoans = $allFullyPaidLoans->where('branchIdFk',$req->filBranch);

        } elseif ($userBranchId!=1) {

            $allFullyPaidLoans = $allFullyPaidLoans->where('branchIdFk',$userBranchId);
        } 

        if((int)$req->filCategory > 0) {
            $productIdArr = DB::table('mfn_loans_product')->where('productCategoryId',$req->filCategory)->pluck('id')->toArray();
            $allFullyPaidLoans = $allFullyPaidLoans->whereIn('productIdFk',$productIdArr);   
        }

        if((int)$req->filLoanProduct > 0) {
            $allFullyPaidLoans = $allFullyPaidLoans->where('productIdFk',$req->filLoanProduct);   
        }

        if($dateTo !='' &&  $filDateFrom !='') {
            $allFullyPaidLoans = $allFullyPaidLoans->where('loanCompletedDate','>=',$filDateFrom)
            ->where('loanCompletedDate','<=',$dateTo); 
        }

        $inactiveMemberIds = DB::table('mfn_member_closing')
        ->where('closingDate','<=',$filDateFrom)
        ->pluck('memberIdFk')->all();

        if ($req->filByMemberStatusChange == 1) {
            $allFullyPaidLoans = $allFullyPaidLoans->whereNotIn('memberIdFk',$inactiveMemberIds);  
        }

        if ($req->filByMemberStatusChange == 2) {
            $allFullyPaidLoans = $allFullyPaidLoans->whereIn('memberIdFk',$inactiveMemberIds);  
        }

        if($req->filOrderByChange == 1) {
            $allFullyPaidLoans = $allFullyPaidLoans->orderBy('samityIdFk');   
        }

        if($req->filOrderByChange == 2) {
            $allFullyPaidLoans = $allFullyPaidLoans->orderBy('disbursementDate');   
        }

        $allFullyPaidLoans = $allFullyPaidLoans->get();

      //START ALL SAMITY ARRAY=====
        $allMembers = DB::table('mfn_member_information')
        ->whereIn('id',$allFullyPaidLoans->pluck('memberIdFk'))
        ->select('id','code','name','spouseFatherSonName','nID','mobileNo','birthRegNo')
        ->get();
        //END ALL SAMITY ARRAY=====

       //START ALL SAMITY ARRAY=====
        $allSamity = DB::table('mfn_samity')
        ->whereIn('id',$allFullyPaidLoans->pluck('samityIdFk'))
        ->select('id','code','name','workingAreaId')
        ->get();
      //END ALL SAMITY ARRAY=====

      //START ALL COLLECTION ARRAY=====
        $allCollections = DB::table('mfn_loan_collection')
        ->whereIn('loanIdFk',$allFullyPaidLoans->pluck('id'))
        ->select('loanIdFk','principalAmount')
        ->get();
        //END ALL COLLECTION ARRAY=====

        //START ALL COLLECTION ARRAY=====
        $allWavers = DB::table('mfn_loan_waivers')
        ->where('softDel',0)
        ->whereIn('loanIdFk',$allFullyPaidLoans->pluck('id'))
        ->select('loanIdFk','amount')
        ->get();
        //END ALL COLLECTION ARRAY=====

            // Branch
        $branchList = DB::table('gnr_branch');

        if ($userBranchId!=1) {
            $branchList = $branchList->where('id', $userBranchId);
        }
        $branch = $branchList
        ->orderBy('branchCode')
        ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0)) AS code"), 'name','address')
        ->whereNotIn('id',[1])
        ->where('id',$req->filBranch) 
        ->first();

        $data = array(
            'allFullyPaidLoans'    => $allFullyPaidLoans,
            'allMembers'           => $allMembers,
            'allSamity'            => $allSamity,
            'allCollections'       => $allCollections,
            'allWavers'            => $allWavers

        );

        return view('microfin.reports.registerReport.regular.fullyPaidLoanRegister.reportPage',$data);

    }

}
