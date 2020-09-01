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
use App\gnr\GnrCompany;
use App\accounting\AddLedger;
use Validator;
use Response;
//use App\Http\Controllers\gnr\Service;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Traits\GetSoftwareDate;
use DB;
use Carbon;
use App\Service\Service;
use App\Service\TransactionCheckHelper;

class AccAddVoucherController extends Controller
{

    // public function addVoucher1(){

    //     $projects = GnrProject::select('id','name')->get();
    //     $projectTypes = GnrProjectType::select('id','name')->get();
    //     $ledgerAccounts = AddLedger::select('id','name')->where('parentId', '>', 0)->get();
    //     // $maxId = AddVoucher::max('id');
    //     // $maxVoucherId = AddVoucher::max('voucherId')+1;
    //     $maxVoucherId = AddVoucher::max('id')+1;


    //     return view('accounting.vouchers.addVouchers1',['projects' => $projects,'projectTypes' => $projectTypes,'ledgerAccounts' => $ledgerAccounts,
    //         // 'maxId' => $maxId,
    //         'maxVoucherId' => $maxVoucherId ]);
    // }

    public function addVoucher(){
        // dd(1);
        $service = new Service;
        // $service->addOpeningBalance(1, 2, 3, 6, 278);
        // $service->yearEndExecute(4, 6);

        $userCompanyId = Auth::user()->company_id_fk;
        $branch = GnrBranch::where('id', Auth::user()->branchId)->first();
        //dd($branch);
        $user_branch_id = Auth::user()->branchId;
        $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->where('companyId',$userCompanyId)->value('projectId');
        $user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->where('companyId',$userCompanyId)->value('projectTypeId');
        if ($branch->branchCode == 0) {
            $projects = GnrProject::select('id','name')->where('companyId',$userCompanyId)->get();
            //dd($projects);
            $projectTypes = GnrProjectType::select('id','name')->where('companyId',$userCompanyId)->get();
        }else{
            $projects = GnrProject::select('id','name')->where('id', $user_project_id)->where('companyId',$userCompanyId)->get();
            $projectTypes = GnrProjectType::select('id','name')->where('id', $user_project_type_id)->where('companyId',$userCompanyId)->get();
        }


        $ledgerAccounts = AddLedger::where('companyIdFk', $userCompanyId)->select('id','name')->where('parentId', '>', 0)->get();
        // $maxId = AddVoucher::max('id');
        // $maxVoucherId = AddVoucher::max('voucherId')+1;
        $maxVoucherId = AddVoucher::where('companyId',$userCompanyId)->max('id')+1;
        //dd($maxVoucherId);
        //CURRENT SOFTWARE DATE for Voucher date
        $currSoftDate=\Carbon\Carbon::parse(GetSoftwareDate::getAccountingSoftwareDate())->format('d-m-Y');;
        // dd(1);

        $checkValue = new TransactionCheckHelper();
        $dateRange = $checkValue->monthEndYearEndCheck();
        
        return view('accounting/vouchers/addVouchers',[
            'projects' => $projects,
            'projectTypes' => $projectTypes,
            'ledgerAccounts' => $ledgerAccounts,
            'currSoftDate' => $currSoftDate,
            'maxVoucherId' => $maxVoucherId ,
            'dateRange' => $dateRange 
        ]);
    }

    /*public function getVoucherCode(Request $request){

        $shortName = DB::table('acc_voucher_type')->where('id',(int)json_decode($request->voucherTypeId))->value('shortName');
        $branchCode =  str_pad( DB::table('gnr_branch')->where('id',Auth::user()->branchId)->value('branchCode'), 3, "0", STR_PAD_LEFT);

        $projectTypeCode =  str_pad( DB::table('gnr_project_type')->where('id',(int)json_decode($request->projectTypeId))->value('projectTypeCode'), 5, "0", STR_PAD_LEFT);

        $voucherNo = DB::table('acc_voucher')
                        ->where('voucherTypeId', (int)json_decode($request->voucherTypeId))
                        ->where('projectTypeId', (int)json_decode($request->projectTypeId))
                        ->where('branchId', Auth::user()->branchId)
                        ->count('id') + 1;

        $voucherNo = str_pad($voucherNo,5,'0',STR_PAD_LEFT);

        $voucherCode = $shortName.'.'.$branchCode.'.'.$projectTypeCode.'.'.$voucherNo;


        return response()->json($voucherCode);
    }*/

    public function getVoucherCode(Request $request)
    {
        // dd(1);
        $userCompanyId = Auth::user()->company_id_fk;
        $checkVouchers = DB::table('acc_voucher')
                        ->where('voucherTypeId', (int)json_decode($request->voucherTypeId))
                        ->where('projectId', (int)json_decode($request->projectId))
                        ->where('branchId', (int)json_decode($request->branchId))
                        ->where('companyId', $userCompanyId)
                        ->select(
                            'voucherCode',
                            DB::raw("CONVERT(SUBSTRING_INDEX(voucherCode, '.', -1), SIGNED) as code")
                            )
                        ->get();

        $singleVoucher = $checkVouchers->where('code', $checkVouchers->max('code'))->first();

        if( $singleVoucher == null ){
            $singleVoucherCount = 0;
        } else {
            $singleVoucherCount = $singleVoucher->voucherCode;
        }
        //dd($singleVoucherCount);
        $shortName       = DB::table('acc_voucher_type')->where('id',(int)json_decode($request->voucherTypeId))->value('shortName');
        $projectTypeCode =  str_pad( DB::table('gnr_project_type')->where('projectId',(int)json_decode($request->projectId))->where('companyId', $userCompanyId)->value('projectTypeCode'), 5, "0", STR_PAD_LEFT);

        $data = array(
            'singleVoucherCount' => $singleVoucherCount,
            'shortName' => $shortName,
            'projectTypeCode' => $projectTypeCode
        );

        return response()->json($data);
    }

    public function getLedgersByBranches(Request $request){

        $branchIdArray=$request->branchIdArray;
        $userCompanyId = Auth::user()->company_id_fk;
        // $branchList =  DB::table('gnr_branch')->whereIn('id', $branchIdArray)->select('id','name','branchCode')->get();


        $allLedgers = DB::table('acc_account_ledger')->where('companyIdFk', $userCompanyId)->where('isGroupHead', 0)->select("id","projectBranchId")->get();

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
                    // if($firstIndexValue!=$temp){
                    if($firstIndexValue==$request->projectId){
                        if($secondIndexValue==0){
                            array_push($matchedId, $singleLedger->id);
                        }
                        else{
                            if (in_array($secondIndexValue, $branchIdArray)){
                                array_push($matchedId, $singleLedger->id);
                            }
                        }
                    }
                    // }
                    // $temp=$firstIndexValue;
                }
            }   //for
        }       //foreach
        // dd($targetBranch);

        // ft debit ledger collection
        $service = new Service;
        $ftDebitLedger = DB::table('acc_account_ledger')->where('companyIdFk', $userCompanyId)->where('code', 27500 )->value('id');
        $ftDebitFinalLevelLedger = $service->finalLevelLedgers($ftDebitLedger);
        // dd($ftDebitFinalLevelLedger);
        $ledgersOfFTDebitCashAndBank = DB::table('acc_account_ledger')
        ->whereIn('id', $ftDebitFinalLevelLedger )
        ->orderBy('code','asc')
        ->select('id','name','code')
        ->get();

        $ledgersOfCashAndBank = DB::table('acc_account_ledger')->where('companyIdFk', $userCompanyId)->whereIn('id', $matchedId )->whereIn('accountTypeId',[4,5])->orderBy('code','asc')->select('id','name','code')->get();
        $ledgersWithOutCashNBank = DB::table('acc_account_ledger')->where('companyIdFk', $userCompanyId)->whereIn('id', $matchedId )->whereNotIn('accountTypeId',[4,5])->orderBy('code','asc')->select('id','name','code','accountTypeId')->get();
        $ledgersOfAllAccountType = DB::table('acc_account_ledger')->where('companyIdFk', $userCompanyId)->whereIn('id', $matchedId )->orderBy('code','asc')->select('id','name','code')->get();

        $data = array('ledgersOfCashAndBank'        => $ledgersOfCashAndBank,
            'ledgersWithOutCashNBank'       => $ledgersWithOutCashNBank,
            'ledgersOfAllAccountType'       => $ledgersOfAllAccountType,
            'ledgersOfFTDebitCashAndBank'   => $ledgersOfFTDebitCashAndBank,
        );

        return response()->json($data);
    }
    public function getProjectTypeNLedgersInfo1(Request $request){
        $userCompanyId = Auth::user()->company_id_fk;
        if($request->projectId==""){
           $projectTypeList =  DB::table('gnr_project_type')->where('companyId',$userCompanyId)->pluck('id','name');
       }
       else{
        $projectTypeList =  DB::table('gnr_project_type')->where('companyId',$userCompanyId)->where('projectId',(int)json_decode($request->projectId))->pluck('id','name');
    }

    $data = array('projectTypeList' => $projectTypeList);

    return response()->json($data);
}

public function getProjectTypeNLedgersInfo(Request $request){
    $userCompanyId = Auth::user()->company_id_fk;
    if($request->projectId==""){
       $projectTypeList =  DB::table('gnr_project_type')->where('companyId',$userCompanyId)->pluck('id','name');
       $branchList =  DB::table('gnr_branch')->where('companyId',$userCompanyId)->select('id','name','branchCode')->get();
    }
   else{
    $projectTypeList =  DB::table('gnr_project_type')->where('companyId',$userCompanyId)->where('projectId',(int)json_decode($request->projectId))->pluck('id','name');
    $branchList =  DB::table('gnr_branch')->where('companyId',$userCompanyId)->where('projectId',(int)json_decode($request->projectId))->orWhere('id', 1)->select('id','name','branchCode')->get();
}

$excludeLedgers = array();

// $ledgerIdForInsurence1 = DB::table('acc_mfn_av_config_interest_skt_amount')->distinct('ledgerIdForInsurence1')->pluck('ledgerIdForInsurence1')->toArray();
// $ledgerIdForInsurence2 = DB::table('acc_mfn_av_config_interest_skt_amount')->distinct('ledgerIdForInsurence2')->pluck('ledgerIdForInsurence2')->toArray();
// $ledgerIdForSktAmount = DB::table('acc_mfn_av_config_interest_skt_amount')->distinct('ledgerIdForSktAmount')->pluck('ledgerIdForSktAmount')->toArray();
// $excludeLedgersSktAmount = array_unique(array_merge($ledgerIdForInsurence1,$ledgerIdForInsurence2,$ledgerIdForSktAmount));

// $ledgerIdForPrinciple = DB::table('acc_mfn_av_config_loan')->distinct('ledgerIdForPrincipal')->pluck('ledgerIdForPrincipal')->toArray();
// $ledgerIdForInterest = DB::table('acc_mfn_av_config_loan')->distinct('ledgerIdForInterest')->pluck('ledgerIdForInterest')->toArray();
// $ledgerIdForRiskInsurance = DB::table('acc_mfn_av_config_loan')->distinct('ledgerIdForRiskInsurance')->pluck('ledgerIdForRiskInsurance')->toArray();
// $excludeLedgersConfigLoan = array_unique(array_merge($ledgerIdForPrinciple, $ledgerIdForInterest, $ledgerIdForRiskInsurance));

// $excludeLedgersConfigOthers = DB::table('acc_mfn_av_config_others')->distinct('ledgerIdFk')->pluck('ledgerIdFk')->toArray();

// $ledgerIdForSavingPrinciple = DB::table('acc_mfn_av_config_saving')->distinct('ledgerIdForPrincipal')->pluck('ledgerIdForPrincipal')->toArray();
// $ledgerIdForSavingInterest = DB::table('acc_mfn_av_config_saving')->distinct('ledgerIdForInterest')->pluck('ledgerIdForInterest')->toArray();
// $ledgerIdForInterestProvision = DB::table('acc_mfn_av_config_saving')->distinct('ledgerIdForInterestProvision')->pluck('ledgerIdForInterestProvision')->toArray();
// $ledgerIdForUnsettledClaim = DB::table('acc_mfn_av_config_saving')->distinct('ledgerIdForUnsettledClaim')->pluck('ledgerIdForUnsettledClaim')->toArray();
// $excludeLedgersConfigSaving = array_unique(array_merge($ledgerIdForSavingPrinciple, $ledgerIdForSavingInterest, $ledgerIdForInterestProvision, $ledgerIdForUnsettledClaim));

// $excludeLedgers = array_unique(array_merge($excludeLedgersSktAmount, $excludeLedgersConfigLoan, $excludeLedgersConfigOthers, $excludeLedgersConfigSaving));


$allLedgers = DB::table('acc_account_ledger')->where('companyIdFk', $userCompanyId)->where('isGroupHead', 0)/*->whereNotIn('id', $excludeLedgers)*/->select("id","projectBranchId")->get();

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
        }
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
                    // }
                    // $temp=$firstIndexValue;
        }

            }   //for
        }       //foreach

        $ledgersOfCashAndBank = DB::table('acc_account_ledger')->where('companyIdFk', $userCompanyId)->whereIn('id', $matchedId )->whereIn('accountTypeId',[4,5])->orderBy('code','asc')->select('id','name','code')->get();

        $ledgersOfAssetNLiabilityNCapitalFundNExpense = DB::table('acc_account_ledger')->where('companyIdFk', $userCompanyId)->whereIn('id', $matchedId )->orderBy('code','asc')->whereIn('accountTypeId', [1,2,3,6,7,8,10,11,13])->select('id','name','code')->get();

        $ledgersOfAssetNLiabilityNCapitalFundNIncome = DB::table('acc_account_ledger')->where('companyIdFk', $userCompanyId)->whereIn('id', $matchedId )->orderBy('code','asc')->whereIn('accountTypeId', [1,2,3,6,7,8,10,11,12])->select('id','name','code')->get();

        $ledgersOfAllAccountType = DB::table('acc_account_ledger')->where('companyIdFk', $userCompanyId)->whereIn('id', $matchedId )->orderBy('code','asc')->select('id','name','code')->get();

        $ledgersOfNonCashNExpense = DB::table('acc_account_ledger')->where('companyIdFk', $userCompanyId)->whereIn('id', $matchedId )->whereNotIn('accountTypeId',[4,5,13])->orderBy('code','asc')->select('id','name','code')->get();
        $ledgersOfNonCashNIncome = DB::table('acc_account_ledger')->where('companyIdFk', $userCompanyId)->whereIn('id', $matchedId )->whereNotIn('accountTypeId',[4,5,12])->orderBy('code','asc')->select('id','name','code')->get();

        $ledgersOfBank = DB::table('acc_account_ledger')->where('companyIdFk', $userCompanyId)->whereIn('id', $matchedId )->where('accountTypeId',5)->orderBy('code','asc')->select('id','name','code')->get();

        $data = array('projectTypeList'                             => $projectTypeList,
            'branchList'                                    => $branchList,
            'ledgersOfCashAndBank'                          => $ledgersOfCashAndBank,
            'ledgersOfAssetNLiabilityNCapitalFundNExpense'  => $ledgersOfAssetNLiabilityNCapitalFundNExpense,
            'ledgersOfAssetNLiabilityNCapitalFundNIncome'   => $ledgersOfAssetNLiabilityNCapitalFundNIncome,
            'ledgersOfAllAccountType'                       => $ledgersOfAllAccountType,
            'ledgersOfNonCashNExpense'                      => $ledgersOfNonCashNExpense,
            'ledgersOfNonCashNIncome'                       => $ledgersOfNonCashNIncome,
            'ledgersOfBank'                                 => $ledgersOfBank
        );

        return response()->json($data);
    }


    // public function addVoucherItem(Request $request){
    //    // dd($request->all());
    //     $rules = array(
    //         'projectId' => 'required',
    //         'projectTypeId' => 'required',
    //         'voucherCode' => 'required',
    //         'amountColumn' => 'required',
    //         'globalNarration' => 'required'
    //     );

    //     $attributeNames = array(
    //         'projectId'    => 'Project Name',
    //         'projectTypeId'   => 'Project Type Name',
    //         'voucherCode'   => 'Voucher Code',
    //         'amountColumn'   => 'Table Data',
    //         'globalNarration'   => 'Global Narration'
    //     );

    //     $validator = Validator::make ( Input::all (), $rules);
    //     $validator->setAttributeNames($attributeNames);

    //     if ($validator->fails())
    //         return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    //     else{

    //         $previousVoucherCodes=DB::table('acc_voucher')->where('branchId', $request->branchId)->where('voucherTypeId', $request->voucherTypeId)->where('projectTypeId', $request->projectTypeId)->select('voucherCode')->get();

    //         $temp=false;
    //         foreach ($previousVoucherCodes as $previousVoucherCode) {
    //             if($previousVoucherCode->voucherCode==$request->voucherCode){
    //                 $temp=true;
    //                 $preVoucherCode = DB::table('acc_voucher')->where('branchId', $request->branchId)->where('projectTypeId', $request->projectTypeId)->where('voucherTypeId', $request->voucherTypeId)->max('voucherCode');
    //             }
    //         }

    //         if($temp){
    //             $splitPreVoucherCode = explode(".", $preVoucherCode);

    //             foreach ($splitPreVoucherCode as $key => $value) {
    //                 if ($key==0) {
    //                     $shortNameOfVoucherType=$value;
    //                 }elseif ($key==1) {
    //                     $branchCode=$value;
    //                 }elseif ($key==2) {
    //                     $projectTypeCode=$value;
    //                 }elseif ($key==3) {
    //                     $newCode=str_pad(((int)$value)+1, 5,"0",STR_PAD_LEFT);
    //                 }
    //             }

    //             $voucherCode=$shortNameOfVoucherType.".".$branchCode.".".$projectTypeCode.".".$newCode;
    //         }else{
    //             // $a="New data";
    //             $voucherCode=$request->voucherCode;
    //         }

    //         $now = \Carbon\Carbon::now();
    //         // $now = date('Y-m-d H:i:s');

    //         $voucher = new AddVoucher;
    //         $voucherDate=\Carbon\Carbon::parse($request->voucherDate);

    //         $voucher->voucherTypeId = $request->voucherTypeId;
    //         $voucher->projectId = $request->projectId;
    //         $voucher->projectTypeId = $request->projectTypeId;
    //         $voucher->voucherDate = $voucherDate;
    //         $voucher->voucherCode = $voucherCode;
    //         $voucher->globalNarration = $request->globalNarration;
    //         $voucher->branchId = $request->branchId;
    //         $voucher->companyId = $request->companyId;
    //         $voucher->vGenerateType = 2;
    //         $voucher->prepBy = $request->prepBy;
    //         $voucher->createdDate = $now;
    //         $voucher->save();

    //         $lastVoucherId = DB::table('acc_voucher')->where('voucherCode',$voucherCode)->select('id')->first();

    //         $array_size = count($request->tableAmount);
    //         $data = [];
    //         for ($i=0; $i<$array_size; $i++){

    //             $data[] = array(
    //                 // 'voucherId' => $lastVoucherId->id,
    //                 'voucherId' => $voucher->id,
    //                 'createdDate' => $now,

    //                 'debitAcc' => (int) json_decode($request->tableDebitAcc[$i]),
    //                 'creditAcc' =>(int) json_decode($request->tableCreditAcc[$i]),
    //                 'amount' => (float) json_decode($request->tableAmount[$i]),
    //                 'localNarration' => (string) json_decode($request->tableNarration[$i])
    //             );
    //         }
    //         DB::table('acc_voucher_details')->insert($data);
    //         $logArray = array(
    //             'moduleId'  => 4,
    //             'controllerName'  => 'AccAddVoucherController',
    //             'tableName'  => 'acc_voucher',
    //             'operation'  => 'insert',
    //             'primaryIds'  => [DB::table('acc_voucher')->max('id')]
    //         );
    //         \App\Http\Controllers\gnr\Service::createLog($logArray);

    //         return response()->json(encrypt($voucher->id));

    //     }
    // }

     public function addVoucherItem(Request $request){
        //dd($request->all());
        $v_approval_step = GnrCompany::where('id',Auth::user()->company_id_fk)->value('voucher_type_step');   
        foreach($request->image as $image){
            if ($image != 'undefined') {
                 $filename = str_random(10) . '.' . $image->getClientOriginalExtension();
                 $destinationPath = base_path() . '/public/images/vouchers/';
                  $image->move($destinationPath,$filename);
                  $data[] = $filename;
            }else{
               $data = null ;
            }
        }

        $userCompanyId = Auth::user()->company_id_fk;
        $rules = array(
            'projectId' => 'required',
            'projectTypeId' => 'required',
            'voucherCode' => 'required',
            'amountColumn' => 'required',
            'globalNarration' => 'required'
        );

        $attributeNames = array(
            'projectId'    => 'Project Name',
            'projectTypeId'   => 'Project Type Name',
            'voucherCode'   => 'Voucher Code',
            'amountColumn'   => 'Table Data',
            'globalNarration'   => 'Global Narration'
        );

        $validator = Validator::make ( Input::all(), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails())
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else{

            $previousVoucherCodes=DB::table('acc_voucher')->where('companyId',$userCompanyId)->where('branchId', $request->branchId)->where('voucherTypeId', $request->voucherTypeId)->where('projectTypeId', $request->projectTypeId)->select('voucherCode')->get();

            $temp=false;
            foreach ($previousVoucherCodes as $previousVoucherCode) {
                if($previousVoucherCode->voucherCode==$request->voucherCode){
                    $temp=true;
                    $preVoucherCode = DB::table('acc_voucher')->where('branchId', $request->branchId)->where('projectTypeId', $request->projectTypeId)->where('companyId',$userCompanyId)->where('voucherTypeId', $request->voucherTypeId)->max('voucherCode');
                }
            }

            if($temp){
                $splitPreVoucherCode = explode(".", $preVoucherCode);

                foreach ($splitPreVoucherCode as $key => $value) {
                    if ($key==0) {
                        $shortNameOfVoucherType=$value;
                    }elseif ($key==1) {
                        $branchCode=$value;
                    }elseif ($key==2) {
                        $projectTypeCode=$value;
                    }elseif ($key==3) {
                        $newCode=str_pad(((int)$value)+1, 5,"0",STR_PAD_LEFT);
                    }
                }

                $voucherCode=$shortNameOfVoucherType.".".$branchCode.".".$projectTypeCode.".".$newCode;
            }else{
                // $a="New data";
                $voucherCode=$request->voucherCode;
            }


            //dd(Auth::user()->branchId);

            $now = \Carbon\Carbon::now();
            // $now = date('Y-m-d H:i:s');

          
            $voucher = new AddVoucher;
            $voucherDate=\Carbon\Carbon::parse($request->voucherDate);

            $voucher->voucherTypeId = $request->voucherTypeId;
            $voucher->projectId = $request->projectId;
            $voucher->projectTypeId = $request->projectTypeId;
            $voucher->voucherDate = $voucherDate;
            $voucher->voucherCode = $voucherCode;
            $voucher->globalNarration = $request->globalNarration;
            $voucher->branchId = Auth::user()->branchId;
            $voucher->companyId = $userCompanyId;
            $voucher->vGenerateType = 2;
            $voucher->prepBy = $request->prepBy;
            if($v_approval_step == 0){
               $voucher->authBy = Auth::user()->emp_id_fk; 
            }else{
                $voucher->authBy = 0;  
            }
            if ($data) {
                $voucher->image = json_encode($data);
            }else{
                $voucher->image = $data;
            }


            $voucher->createdDate = $now;
            //dd($voucher);
            $voucher->save();

            $lastVoucherId = DB::table('acc_voucher')->where('voucherCode',$voucherCode)->where('companyId',$userCompanyId)->select('id')->first();
            // dd(json_decode($request->tableAmount));

            $array_size = count(json_decode($request->tableAmount));
            $data = [];
            for ($i=0; $i<$array_size; $i++){

                $data[] = array(
                    // 'voucherId' => $lastVoucherId->id,
                    'voucherId' => $voucher->id,
                    'createdDate' => $now,

                    'debitAcc' => (int) json_decode($request->tableDebitAcc)[$i],
                    'creditAcc' =>(int) json_decode($request->tableCreditAcc)[$i],
                    'amount' => (float) json_decode($request->tableAmount)[$i],
                    'localNarration' => (string) json_decode($request->tableNarration)[$i]
                );
            }
            DB::table('acc_voucher_details')->insert($data);
            $logArray = array(
                'moduleId'  => 4,
                'controllerName'  => 'AccAddVoucherController',
                'tableName'  => 'acc_voucher',
                'operation'  => 'insert',
                'primaryIds'  => [DB::table('acc_voucher')->where('companyId',$userCompanyId)->max('id')]
            );
            \App\Http\Controllers\gnr\Service::createLog($logArray);

            return response()->json(encrypt($voucher->id));

        }
    }

    public function addFTVoucherItem(Request $request){
        //dd($request->all());
        

         $data=[];
        foreach($request->image as $image){
            if ($image != 'undefined') {
                 $filename = str_random(10) . '.' . $image->getClientOriginalExtension();
                 $destinationPath = base_path() . '/public/images/vouchers/';
                  $image->move($destinationPath,$filename);
                  $data[] = $filename;
            }else{
               $data = null ;
            }
        }
        // dd($data);

        $user_branch_id = Auth::user()->branchId;
        $userCompanyId = Auth::user()->company_id_fk;
        $branchInfo = GnrBranch::where('id',$user_branch_id)->where('companyId',$userCompanyId)->first();
        //dd($branchInfo);

        $rules = array(
            'projectId' => 'required',
            'projectTypeId' => 'required',
            'voucherDate' => 'required',
            'voucherCode' => 'required',
            'amountColumn' => 'required',
            'globalNarration' => 'required'
        );

        $attributeNames = array(
            'projectId'    => 'Project Name',
            'projectTypeId'   => 'Project Type Name',
            'voucherDate'   => 'Voucher Date',
            'voucherCode'   => 'Voucher Code',
            'amountColumn'   => 'Information In The Table',
            'globalNarration'   => 'Global Narration'
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()){
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        else{


            $targetBranchIdsArr = [];
            $targetHeadOfficeId = array(1);


            for($i = 0; $i < count(json_decode($request->tableTargetBranch)); $i++){

                $targetBranchIdsArr[] = (int) json_decode($request->tableTargetBranch)[$i];

            }

            //dd($targetBranchIdsArr);

            $targetBranchIdsArrWithoutHeadOffice = array_diff($targetBranchIdsArr, $targetHeadOfficeId);


            if($branchInfo->branchCode != 0 && count($targetBranchIdsArrWithoutHeadOffice) >0){

                //previousVoucherCodes for branch
                $previousVoucherCodes=DB::table('acc_voucher')
                ->where('projectTypeId', $request->projectTypeId)
                ->where('branchId', $request->branchId)
                ->where('companyId', $userCompanyId)
                ->where('voucherTypeId', $request->voucherTypeId)
                ->pluck('voucherCode')
                ->toArray();

                //previousVoucherCodes for Head Office
                $previousVoucherCodesForHeadOffice=DB::table('acc_voucher')
                ->where('projectTypeId', $request->projectTypeId)
                ->where('branchId', $user_branch_id)
                ->where('companyId', $userCompanyId)
                ->where('voucherTypeId', $request->voucherTypeId)
                ->pluck('voucherCode')
                ->toArray();

                $tempForBranch=false;

                // voucherCode for branch
                if (in_array($request->voucherCode, $previousVoucherCodes)){
                    $tempForBranch=true;
                    $preVoucherCode = DB::table('acc_voucher')
                    ->where('branchId', $request->branchId)
                    ->where('projectTypeId', $request->projectTypeId)
                    ->where('voucherTypeId', $request->voucherTypeId)
                    ->where('companyId', $userCompanyId)
                    ->max('voucherCode');

                }

                if($tempForBranch){
                 $splitPreVoucherCode = explode(".", $preVoucherCode);
                 foreach ($splitPreVoucherCode as $key => $value) {
                    if ($key==0) {
                        $shortNameOfVoucherType=$value;
                    }elseif ($key==1) {
                        $branchCode=$value;
                    }elseif ($key==2) {
                        $projectTypeCode=$value;
                    }elseif ($key==3) {
                        $newCode=str_pad(((int)$value)+1, 5,"0",STR_PAD_LEFT);
                    }
                }

                $voucherCode=$shortNameOfVoucherType.".".$branchCode.".".$projectTypeCode.".".$newCode;
            }else{

                $voucherCode=$request->voucherCode;
            }


            $preVoucherCodeForHeadOffice = DB::table('acc_voucher')
            ->where('branchId', $user_branch_id)
            ->where('projectTypeId', $request->projectTypeId)
            ->where('voucherTypeId', $request->voucherTypeId)
            ->where('companyId', $userCompanyId)
            ->max('voucherCode');

                // if($tempForHeadOffice){
            if(in_array($preVoucherCodeForHeadOffice, $previousVoucherCodesForHeadOffice)) {
             $splitPreVoucherCode = explode(".", $preVoucherCodeForHeadOffice);
             foreach ($splitPreVoucherCode as $key => $value) {
                if ($key==0) {
                    $shortNameOfVoucherType=$value;
                }elseif ($key==1) {
                    $branchCode=$value;
                }elseif ($key==2) {
                    $projectTypeCode=$value;
                }elseif ($key==3) {
                    $newCode=str_pad(((int)$value)+1, 5,"0",STR_PAD_LEFT);
                }
            }
            $voucherCodeForHeadOffice=$shortNameOfVoucherType.".".$branchCode.".".$projectTypeCode.".".$newCode;
        }



        $now = \Carbon\Carbon::now();
        $targetBranchArray = $request->tableTargetBranch;
        $targetBranchUniArray = array_values(array_unique($targetBranchArray));
                // sort($targetBranchUniArray);
        $targetBranchArraySize = count($targetBranchArray);
        $uniqueBranchArraySize = count($targetBranchUniArray);


                // creating voucher for brach
        $voucherFrom = new AddVoucher;
        $voucherDate=\Carbon\Carbon::parse($request->voucherDate);
        $ftId = DB::table('acc_voucher')->where('voucherTypeId', $request->voucherTypeId)->where('companyId', $userCompanyId)->max('ftId')+1;

                // $newArray = array();

        $voucherFrom->voucherTypeId = $request->voucherTypeId;
        $voucherFrom->projectId = $request->projectId;
        $voucherFrom->projectTypeId = $request->projectTypeId;
        $voucherFrom->voucherDate = $voucherDate;
        $voucherFrom->voucherCode = $voucherCode;
        $voucherFrom->globalNarration = $request->globalNarration;
        $voucherFrom->branchId = $request->branchId;
        $voucherFrom->companyId = $userCompanyId;
        $voucherFrom->vGenerateType = 2;
        $voucherFrom->prepBy = $request->prepBy;
        $voucherFrom->ftId = $ftId;
        $voucherFrom->createdDate = $now;
        if ($data) {
                $voucher->image = json_encode($data);
            }else{
                $voucher->image = $data;
        }
         //dd($voucherFrom);
        $voucherFrom->save();

                //creating voucher for Head Office
        $voucherFromHeadOffice = new AddVoucher;
        $voucherDate=\Carbon\Carbon::parse($request->voucherDate);

        $voucherFromHeadOffice->voucherTypeId = $request->voucherTypeId;
        $voucherFromHeadOffice->projectId = $request->projectId;
        $voucherFromHeadOffice->projectTypeId = $request->projectTypeId;
        $voucherFromHeadOffice->voucherDate = $voucherDate;
        $voucherFromHeadOffice->voucherCode = $voucherCodeForHeadOffice;
        $voucherFromHeadOffice->globalNarration = $request->globalNarration;
        $voucherFromHeadOffice->branchId = $user_branch_id;
        $voucherFromHeadOffice->companyId = $userCompanyId;
        $voucherFromHeadOffice->vGenerateType = 2;
        $voucherFromHeadOffice->prepBy = $request->prepBy;
        $voucherFromHeadOffice->ftId = $ftId;
        $voucherFromHeadOffice->createdDate = $now;
        if ($data) {
                $voucher->image = json_encode($data);
            }else{
                $voucher->image = $data;
        }
        $voucherFromHeadOffice->save();

        $lastVoucherId = DB::table('acc_voucher')->where('voucherCode',$voucherCode) ->where('companyId', $userCompanyId)->value('id');
                // array_push($newArray, $lastVoucherId);
        $dataFrom = [];
        $dataFromHeadOffice = [];

        for ($i=0; $i<$targetBranchArraySize; $i++){

                    //for branch
            $dataFrom[] = array(
                'voucherId' => $voucherFrom->id,
                'createdDate' => $now,
                'debitAcc' => (int) json_decode($request->tableDebitAcc)[$i],
                'creditAcc' => (int) json_decode($request->tableCreditAcc)[$i],
                'amount' => (float) json_decode($request->tableAmount)[$i],
                'ftFrom' => (int) $request->branchId,
                'ftTo' => (int) json_decode($request->tableTargetBranch)[$i],
                'ftTargetAcc' => (int) json_decode($request->tableTargetBranchHead)[$i],
                'localNarration' => (string) json_decode($request->tableNarration)[$i]
            );

            if((int) json_decode($request->tableTargetBranch)[$i] == 1){
                continue;
            }

            //dd($request->branchId);
                     // for Head Office
            $dataFromHeadOffice[] = array(
                'voucherId' => $voucherFromHeadOffice->id,
                'createdDate' => $now,

                'debitAcc' => (int) json_decode($request->tableDebitAcc)[$i],
                'creditAcc' =>(int) json_decode($request->tableCreditAcc)[$i],
                'amount' => (float) json_decode($request->tableAmount)[$i],
                'ftFrom' => (int) $request->branchId,
                'ftTo' => (int) json_decode($request->tableTargetBranch)[$i],
                'ftTargetAcc' => (int) json_decode($request->tableTargetBranchHead)[$i],
                'localNarration' => (string) json_decode($request->tableNarration)[$i]
            );
        }
    }else{
        $previousVoucherCodes=DB::table('acc_voucher')->where('projectTypeId', $request->projectTypeId)->where('branchId', $request->branchId)->where('voucherTypeId', $request->voucherTypeId)->where('companyId', $userCompanyId)->pluck('voucherCode')->toArray();
        $temp=false;
        if (in_array($request->voucherCode, $previousVoucherCodes)){
            $temp=true;
            $preVoucherCode = DB::table('acc_voucher')->where('branchId', $request->branchId)->where('companyId', $userCompanyId)->where('projectTypeId', $request->projectTypeId)->where('voucherTypeId', $request->voucherTypeId)->max('voucherCode');
                    // $preVoucherCode=$request->voucherCode;
        }

        if($temp){
         $splitPreVoucherCode = explode(".", $preVoucherCode);
         foreach ($splitPreVoucherCode as $key => $value) {
            if ($key==0) {
                $shortNameOfVoucherType=$value;
            }elseif ($key==1) {
                $branchCode=$value;
            }elseif ($key==2) {
                $projectTypeCode=$value;
            }elseif ($key==3) {
                $newCode=str_pad(((int)$value)+1, 5,"0",STR_PAD_LEFT);
            }
        }

        $voucherCode=$shortNameOfVoucherType.".".$branchCode.".".$projectTypeCode.".".$newCode;
    }else{
                    // $a="New data";
        $voucherCode=$request->voucherCode;
    }

    $now = \Carbon\Carbon::now();
                // $now = date('Y-m-d H:i:s');    json_decode($json_string, TRUE);
    $targetBranchArray = json_decode($request->tableTargetBranch);
    $targetBranchUniArray = array_values(array_unique($targetBranchArray));
                // sort($targetBranchUniArray);
    $targetBranchArraySize = count($targetBranchArray);
    $uniqueBranchArraySize = count($targetBranchUniArray);

    $voucherFrom = new AddVoucher;
    $voucherDate=\Carbon\Carbon::parse($request->voucherDate);
    $ftId = DB::table('acc_voucher')->where('voucherTypeId', $request->voucherTypeId)->where('companyId', $userCompanyId)->max('ftId')+1;

    $newArray = array();

    $voucherFrom->voucherTypeId = $request->voucherTypeId;
    $voucherFrom->projectId = $request->projectId;
    $voucherFrom->projectTypeId = $request->projectTypeId;
    $voucherFrom->voucherDate = $voucherDate;
    $voucherFrom->voucherCode = $voucherCode;
    $voucherFrom->globalNarration = $request->globalNarration;
    $voucherFrom->branchId = $request->branchId;
    $voucherFrom->companyId = $userCompanyId;
    $voucherFrom->vGenerateType = 2;
    $voucherFrom->prepBy = $request->prepBy;
    $voucherFrom->ftId = $ftId;
    $voucherFrom->createdDate = $now;
    if ($data) {
        $voucher->image = json_encode($data);
    }
    $voucherFrom->save();

    $lastVoucherId = DB::table('acc_voucher')->where('voucherCode',$voucherCode)->where('companyId', $userCompanyId)->value('id');
                // array_push($newArray, $lastVoucherId);
    $dataFrom = [];
    for ($i=0; $i<$targetBranchArraySize; $i++){

        $dataFrom[] = array(
                        // 'voucherId' => $lastVoucherId,
            'voucherId' => $voucherFrom->id,
            'createdDate' => $now,

            'debitAcc' => (int) json_decode($request->tableDebitAcc)[$i],
            'creditAcc' =>(int) json_decode($request->tableCreditAcc)[$i],
            'amount' => (float) json_decode($request->tableAmount)[$i],
            'ftFrom' => (int) $request->branchId,
            'ftTo' => (int) json_decode($request->tableTargetBranch)[$i],
            'ftTargetAcc' => (int) json_decode($request->tableTargetBranchHead)[$i],
            'localNarration' => (string) json_decode($request->tableNarration)[$i]
        );
    }

            } // end of else

            DB::table('acc_voucher_details')->insert($dataFrom);
            if(isset($dataFromHeadOffice)){
                DB::table('acc_voucher_details')->insert($dataFromHeadOffice);
            }



            $dataTarget = [];
            // $dataTargetForHeadOffice = [];
            for ($i=0; $i<$uniqueBranchArraySize; $i++){

                $tempVoucherCode = DB::table('acc_voucher')->where('branchId',(int) json_decode($targetBranchUniArray[$i]))->where('projectTypeId', $request->projectTypeId)->where('voucherTypeId', $request->voucherTypeId)->where('companyId', $userCompanyId)->max('voucherCode');

                if($tempVoucherCode){
                    $splitPreVoucherCode = explode(".", $tempVoucherCode);
                    foreach ($splitPreVoucherCode as $key => $value) {
                        if ($key==0) {
                            $shortNameOfVoucherType=$value;
                        }elseif ($key==1) {
                            $branchCode=$value;
                        }elseif ($key==2) {
                            $projectTypeCode=$value;
                        }elseif ($key==3) {
                            $newCode=str_pad(((int)$value)+1, 5,"0",STR_PAD_LEFT);
                        }
                    }
                    $voucherCodeTo=$shortNameOfVoucherType.".".$branchCode.".".$projectTypeCode.".".$newCode;
                }else{
                    $shortNameOfVoucherType = DB::table('acc_voucher_type')->where('id',  $request->voucherTypeId)->value('shortName');
                    $branchCode =str_pad( DB::table('gnr_branch')->where('id',  (int) json_decode($targetBranchUniArray[$i]))->where('companyId', $userCompanyId)->value('branchCode') , 3, "0", STR_PAD_LEFT);
                    $projectTypeCode =str_pad( DB::table('gnr_project_type')->where('id', $request->projectTypeId)->where('companyId', $userCompanyId)->value('projectTypeCode') , 5, "0", STR_PAD_LEFT);
                    $newCode=str_pad(1, 5,"0",STR_PAD_LEFT);
                    $voucherCodeTo=$shortNameOfVoucherType.".".$branchCode.".".$projectTypeCode.".".$newCode;
                }

                $voucherTargetBranch = new AddVoucher;
                $voucherDate=\Carbon\Carbon::parse($request->voucherDate);
                $ftId = $ftId;

                $voucherTargetBranch->voucherTypeId = $request->voucherTypeId;
                $voucherTargetBranch->projectId = $request->projectId;
                $voucherTargetBranch->projectTypeId = $request->projectTypeId;
                $voucherTargetBranch->voucherDate = $voucherDate;
                $voucherTargetBranch->voucherCode = $voucherCodeTo;
                $voucherTargetBranch->globalNarration = $request->globalNarration;
                $voucherTargetBranch->branchId = (int) json_decode($targetBranchUniArray[$i]);
                $voucherTargetBranch->companyId = $userCompanyId;
                $voucherTargetBranch->vGenerateType = 2;
                $voucherTargetBranch->prepBy = $request->prepBy;
                $voucherTargetBranch->ftId = $ftId;
                $voucherTargetBranch->createdDate = $now;
                if ($data) {
                    $voucher->image = json_encode($data);
                }
                $voucherTargetBranch->save();

                        //$lastVoucherIdTargetBranch = DB::table('acc_voucher')->where('voucherCode',$voucherCodeTo)->value('id');

                for ($j=0; $j<$targetBranchArraySize; $j++){
                    if ($targetBranchUniArray[$i]==$targetBranchArray[$j]) {
                        $dataTarget[] = array(
                            'voucherId' => $voucherTargetBranch->id,
                            'createdDate' => $now,

                            'debitAcc' => ((int) json_decode($request->tableTargetBranchHead)[$j]==0 ? (int) json_decode($request->tableCreditAcc)[$j] : (int) json_decode($request->tableTargetBranchHead)[$j]),
                            'creditAcc' =>(int) json_decode($request->tableDebitAcc)[$j],
                            'amount' => (float) json_decode($request->tableAmount)[$j],
                            'ftFrom' => (int) $request->branchId,
                            'ftTo' => (int) json_decode($request->tableTargetBranch)[$j],
                            'ftTargetAcc' => (int) json_decode($request->tableTargetBranchHead)[$j],
                            'localNarration' => (string) json_decode($request->tableNarration)[$j]
                        );
                            } // end of if
                        } // end of child for loop

            } // end of parent for loop

            //dd($dataTarget);
            DB::table('acc_voucher_details')->insert($dataTarget);
            $logArray = array(
                'moduleId'  => 4,
                'controllerName'  => 'AccAddVoucherController',
                'tableName'  => 'acc_voucher',
                'operation'  => 'insert',
                'primaryIds'  => [DB::table('acc_voucher')->max('id')]
            );
            \App\Http\Controllers\gnr\Service::createLog($logArray);

            return response()->json(encrypt($voucherFrom->id));

        }  //else bracket
    }  //addFTVoucherItem function


}  //controller
