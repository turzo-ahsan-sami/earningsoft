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
use App\accounting\AccDayEnd;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon;
use App\Traits\GetSoftwareDate;


class AccAjaxResponseController extends Controller
{

    //using GetSoftware class for getting software date
    use GetSoftwareDate;

    // protected $AccAjaxResponse;
    protected $Accounting;

    public function __construct() {

        // $this->Accounting = new AccAjaxResponseController;
        $this->Accounting = new Accounting;
    }

    public function getLedgersOptionByBranch(Request $request){

        $projectId=$request->projectId;
        $branchId=$request->branchId;

        if($branchId==-1){
            $branchIdArray=GnrBranch::pluck('id')->toArray();
        }else if($branchId==-2){
            $branchIdArray=GnrBranch::where('id','!=', 1)->pluck('id')->toArray();
        }else{
            $branchIdArray=[$branchId];
        }

        $ledgerIdArr=$this->Accounting->getBranchArrWiseLedgerIds($projectId, $branchIdArray);
 
        // $ledgerOptions =    array('' => '--Select Ledger--')
        //                     +
        //                     DB::table('acc_account_ledger')->whereIn('id', $ledgerIdArr )->select(DB::raw("CONCAT(code, '-', name) AS nameWithCode"), 'id')->orderBy('code')->pluck('nameWithCode', 'id')->toArray();

        $ledgerOptions =    DB::table('acc_account_ledger')->where('isGroupHead', 0)->whereIn('id', $ledgerIdArr )->orderBy('code')->select('name','code', 'id')->get();
        $ledgerOfBanksOptions =    DB::table('acc_account_ledger')->where('isGroupHead', 0)->where('accountTypeId', 5)->whereIn('id', $ledgerIdArr )->orderBy('code')->select('name','code', 'id')->get();

       


        // $ledgersOfCashAndBank = DB::table('acc_account_ledger')->whereIn('id', $matchedId )->whereIn('accountTypeId',[4,5])->orderBy('code','asc')->select('id','name','code')->get();

        // $ledgersOfAssetNLiabilityNCapitalFundNExpense = DB::table('acc_account_ledger')->whereIn('id', $matchedId )->orderBy('code','asc')->whereIn('accountTypeId', [1,2,3,6,7,8,10,11,13])->select('id','name','code')->get();

        // $ledgersOfAssetNLiabilityNCapitalFundNIncome = DB::table('acc_account_ledger')->whereIn('id', $matchedId )->orderBy('code','asc')->whereIn('accountTypeId', [1,2,3,6,7,8,10,11,12])->select('id','name','code')->get();

        // $ledgersOfAllAccountType = DB::table('acc_account_ledger')->whereIn('id', $matchedId )->orderBy('code','asc')->select('id','name','code')->get();

        // $ledgersOfBank = DB::table('acc_account_ledger')->whereIn('id', $matchedId )->where('accountTypeId',5)->orderBy('code','asc')->select('id','name','code')->get();

        // $data = array('projectTypeList'                             => $projectTypeList, 
        //             'branchList'                                    => $branchList,
        //             'ledgersOfCashAndBank'                          => $ledgersOfCashAndBank,
        //             'ledgersOfAssetNLiabilityNCapitalFundNExpense'  => $ledgersOfAssetNLiabilityNCapitalFundNExpense,
        //             'ledgersOfAssetNLiabilityNCapitalFundNIncome'   => $ledgersOfAssetNLiabilityNCapitalFundNIncome,
        //             'ledgersOfAllAccountType'                       => $ledgersOfAllAccountType,
        //             'ledgersOfBank'                                 => $ledgersOfBank
        //             );

        // $c="hello";
        $data = array(  
                        // 'ledgerIdArr'                   => $ledgerIdArr,
                        'ledgerOptions'                 => $ledgerOptions,
                        'ledgerOfBanksOptions'          => $ledgerOfBanksOptions,
                    );

        return response()->json($data);
    }


    public function getSoftwareDateByBranch(Request $request){
        $branchId = $request->branchId;

        //Getting software date
          $softwareMaxDate = GetSoftwareDate::getAccountingSoftwareDate();
          
          //changing format
          $softwareMaxDate = Carbon::parse($softDate)->format('d-m-Y');

         

        $softStartingDate = DB::table('gnr_branch')
        ->where('id', $branchId)
        ->value('softwareStartDate');
        
        $date = Carbon::parse($softStartingDate)->format('d-m-Y');
        $data = array(
            'softwareDate' => $date,
            'softwareMaxDate'     => $softwareMaxDate
        );

        return response::json($data);
    }


}
