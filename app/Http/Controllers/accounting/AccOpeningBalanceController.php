<?php

namespace App\Http\Controllers\accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\accounting\AddVoucher;
use App\accounting\AddVoucherDetails;
use App\accounting\AddLedger;
use App\accounting\AccEquity;
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;
use App\gnr\GnrBranch;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class AccOpeningBalanceController extends Controller
{
    //
    public function index(){
        $openingBalances=DB::table('acc_opening_balance')->groupBy('projectId')->where('companyIdFk', Auth::user()->company_id_fk)->get();
        //$openingBalances=DB::table('acc_opening_balance')->where('companyIdFk', Auth::user()->company_id_fk)->get();
        //dd($openingBalances);
        // var_dump($openingBalances);

        return view('accounting.openingBalance.viewOpeningBalance',['openingBalances' => $openingBalances]);
    }
    // public function addOpeningBalance(){
    //     $projects = GnrProject::select('id','name','projectCode')->get();
    //     $projectTypes = GnrProjectType::select('id','name','projectTypeCode')->get();
    //     $branches = GnrBranch::select('id','name','branchCode')->get();

    //     return view('accounting.openingBalance.addOpeningBalance',['projects' => $projects, 'projectTypes' => $projectTypes, 'branches' => $branches]);
    // }
    public function addOpeningBalance(){

        $projects = GnrProject::where('companyId', Auth::user()->company_id_fk)->select('id','name','projectCode')->get();
        $projectTypes = GnrProjectType::where('companyId', Auth::user()->company_id_fk)->select('id','name','projectTypeCode')->get();
        $branches = GnrBranch::where('companyId', Auth::user()->company_id_fk)->select('id','name','branchCode')->get();
        $openingDatefromDB=DB::table('acc_opening_balance')->where('companyIdFk', Auth::user()->company_id_fk)->value('openingDate');

        return view('accounting.openingBalance.addOpeningBalance',['projects' => $projects, 'projectTypes' => $projectTypes, 'branches' => $branches, 'openingDatefromDB' => $openingDatefromDB]);
    }

    public function editOpeningBalance($encryptedId){

        $openingBalanceId=decrypt($encryptedId);
        $obId=DB::table('acc_opening_balance')->where('id',$openingBalanceId)->value('obId');
        $openingBalanceInfos=DB::table('acc_opening_balance')->where('obId',$obId)->select('id','ledgerId','debitAmount','creditAmount','balanceAmount')->get();

        ///////// updated code ///////////
        $obInfo = DB::table('acc_opening_balance')->where('id',$openingBalanceId)->first();
        $projectId = $obInfo->projectId;
        $branchId = $obInfo->branchId;

        $allLedgers = DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('isGroupHead', 0)->select("id","projectBranchId")->get();

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
                    if($firstIndexValue==$projectId){
                        if($secondIndexValue==0){
                            array_push($matchedId, $singleLedger->id);
                        }elseif($secondIndexValue==$branchId ){
                            array_push($matchedId, $singleLedger->id);
                        }
                    }
                    // }
                    // $temp=$firstIndexValue;
                }
            }   //for
        }       //foreach

        $ledgers = DB::table('acc_account_ledger')
        ->whereIn('id', $matchedId)
        ->orderBy('code', 'asc')
        ->select('id','name','code')
        ->get();
                    // dd($ledgers);
        ///// updated code /////////

        $projects = GnrProject::where('companyId', Auth::user()->company_id_fk)->select('id','name','projectCode')->get();
        $projectTypes = GnrProjectType::where('companyId', Auth::user()->company_id_fk)->select('id','name','projectTypeCode')->get();
        $branches = GnrBranch::where('companyId', Auth::user()->company_id_fk)->select('id','name','branchCode')->get();

        return view('accounting.openingBalance.editOpeningBalance',['projects' => $projects, 'projectTypes' => $projectTypes, 'branches' => $branches, 'openingBalanceId' => $openingBalanceId, 'obId' => $obId, 'openingBalanceInfos' => $openingBalanceInfos, 'ledgers' => $ledgers]);
    }


     // public function checkPreOpeningBalance(Request $request){

     //    $fiscalYearId=1;
     //    $projectId=$request->projectId;
     //    $branchId=$request->branchId;
     //    $projectTypeId=$request->projectTypeId;
     //    $matchedOpeningBalanceId=DB::table('acc_opening_balance')->where('fiscalYearId',$fiscalYearId)->where('projectId',$projectId)->where('branchId',$branchId)->where('projectTypeId',$projectTypeId)->pluck('id')->toArray();
     //    $obId=$fiscalYearId.".".$projectId.".".$branchId.".".$projectTypeId;

     //    $data = array(
     //            'obId' => $obId,
     //            'matchedOpeningBalanceId' => $matchedOpeningBalanceId,
     //            'fiscalYearId' => $fiscalYearId,
     //            'projectId' => $projectId,
     //            'branchId' => $branchId,
     //            'projectTypeId' => $projectTypeId
     //        );

     //    return response()->json($data);
     // }

    public function checkPreOpeningBalance(Request $request){
        //dd($request->all());
        $openingDate=Carbon::parse($request->openingDate)->format('Y-m-d');

        $fiscalYearId=DB::table('gnr_fiscal_year')->where('companyId', Auth::user()->company_id_fk)->where('fyStartDate', '<=', $openingDate)->where('fyEndDate', '>=', $openingDate)->value('id');
        // $fiscalYearId=1;
        $projectId=$request->projectId;
        $branchId=$request->branchId;
        $projectTypeId=$request->projectTypeId;
        $matchedOpeningBalanceId=DB::table('acc_opening_balance')->where('companyIdFk', Auth::user()->company_id_fk)->where('fiscalYearId',$fiscalYearId)->where('projectId',$projectId)->where('branchId',$branchId)->where('projectTypeId',$projectTypeId)->pluck('id')->toArray();
        $obId=$fiscalYearId.".".$projectId.".".$branchId.".".$projectTypeId;

        $data = array(
            'obId' => $obId,
            'matchedOpeningBalanceId' => $matchedOpeningBalanceId,
            'fiscalYearId' => $fiscalYearId,
            'projectId' => $projectId,
            'branchId' => $branchId,
            'projectTypeId' => $projectTypeId
                // 'fiscalYearId' => $fiscalYearId,
                // 'openingDate' => $openingDate
        );
        //dd($data);
        return response()->json($data);
    }

    public function getBranchNProjectTypeByProject(Request $request){

       if($request->projectId==""){
            $branchList =  DB::table('gnr_branch')->whereNotIn('id', [1])->where('companyId',Auth::user()->company_id_fk)->select('id','name','branchCode')->get();
            $projectTypeList = 'All';
        }
    else{
        $branchList =  DB::table('gnr_branch')->where('projectId',(int)json_decode($request->projectId))->whereNotIn('id', [1])->where('companyId', Auth::user()->company_id_fk)->select('id','name','branchCode')->get();
        $projectTypeList =  DB::table('gnr_project_type')->where('companyId',Auth::user()->company_id_fk)->where('projectId',(int)json_decode($request->projectId))->select('id','name','projectTypeCode')->get();
    }

    $data = array(
        'branchList' => $branchList,
        'projectTypeList' => $projectTypeList
    );
    //dd($data);

    return response()->json($data);
	    // return response()->json($branchList);
}

public function getLedgerHeader(Request $request){


    $allLedgers = DB::table('acc_account_ledger')->where('isGroupHead', 0)->select("id","projectBranchId")->get();

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
                    }elseif($secondIndexValue==$request->branchId ){
                        array_push($matchedId, $singleLedger->id);
                    }
                }
                    // }
                    // $temp=$firstIndexValue;
            }
            }   //for
        }       //foreach

        // $ledgers = DB::table('acc_account_ledger')->whereIn('id', $matchedId )->select('id','name','code')->orderBy('accountTypeId', 'asc')->get();
        // $ledgers = DB::table('acc_account_ledger')->whereIn('id', $matchedId )->select('id','name','code','accountTypeId','parentId')->orderBy('accountTypeId', 'desc')->get();

        // $ledgers = DB::table('acc_account_ledger')->whereIn('id', $matchedId )->select('id','name','code','accountTypeId','parentId')->orderBy('accountTypeId', 'desc')->get();

        $ledgers = DB::table('acc_account_ledger')
        ->join('acc_account_type', 'acc_account_type.id', '=', 'acc_account_ledger.accountTypeId')
        ->join('acc_account_ledger as parentName', 'parentName.id', '=', 'acc_account_ledger.parentId')
        ->whereIn('acc_account_ledger.id', $matchedId )
        ->orderBy('acc_account_ledger.accountTypeId', 'asc')
        ->select('acc_account_ledger.id','acc_account_ledger.name as ledgerName','acc_account_ledger.code', 'acc_account_type.name as accountTypeName', 'parentName.name as parentName')
        ->get();


        $data = array(
            'ledgers' => $ledgers
        );

        return response()->json($data);
    }
    public function addOpeningBalanceItem(Request $request){

        $rules = array(
            'projectId' => 'required',
            'branchId' => 'required',
            'projectTypeId' => 'required',
            'openingDate' => 'required'
        );

        $attributeNames = array(
            'projectId'    => 'Project Name',
            'branchId'   => 'Branch Name',
            'projectTypeId'   => 'Project Type Name',
            'openingDate'   => 'Opening Date'
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()){
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }else{

            $now = Carbon::now();
            // $now = date('Y-m-d H:i:s');
            $openingDate=Carbon::parse($request->openingDate)->format('Y-m-d');
            $fiscalYearId=DB::table('gnr_fiscal_year')->where('fyStartDate', '<=', $openingDate)->where('fyEndDate', '>=', $openingDate)->value('id');
            $obId=$fiscalYearId.".".$request->projectId.".".$request->branchId.".".$request->projectTypeId;
            $array_size = count($request->ledgerIdArray);
            $companyIdFk =Auth::user()->company_id_fk;
            $data = [];
            for ($i=0; $i<$array_size; $i++){
                $data[] = array(

                    'obId' => $obId,
                    'projectId' => $request->projectId,
                    'branchId' => $request->branchId,
                    'projectTypeId' => $request->projectTypeId,
                    'openingDate' => $openingDate,
                    'fiscalYearId' => $fiscalYearId,
                    //'companyIdFk' => $companyIdFk,

                    'ledgerId' => (int) json_decode($request->ledgerIdArray[$i]),
                    'debitAmount' => round((float) json_decode($request->debitAmountArray[$i]), 2),
                    'creditAmount' => round((float) json_decode($request->creditAmountArray[$i]), 2),
                    'balanceAmount' => round((float) json_decode($request->balanceAmountArray[$i]), 2),
                    'generateType' => 1,
                    'createdDate' => $now
                );
            }
           // dd($data);
            DB::table('acc_opening_balance')->insert($data);

            $incomeLedgersArray = DB::table('acc_account_ledger')->where('accountTypeId',12)->where('isGroupHead',0)->pluck('id')->toArray();
            $expenseLedgersArray = DB::table('acc_account_ledger')->where('accountTypeId',13)->where('isGroupHead',0)->pluck('id')->toArray();

            $incomeCrAmount=$incomeDrAmount=$expenseAmount=$ledgerIdArray=array();

            foreach ($request->ledgerIdArray as $eachValue) {
                array_push($ledgerIdArray, (int) json_decode($eachValue));
            }
            foreach ($ledgerIdArray as $index1 => $ledgerId) {
                if(in_array($ledgerId, $incomeLedgersArray)){
                    array_push($incomeCrAmount, round((float) json_decode($request->creditAmountArray[$index1]), 2) );
                    array_push($incomeDrAmount, round((float) json_decode($request->debitAmountArray[$index1]), 2) );
                }else if(in_array($ledgerId, $expenseLedgersArray)){
                    array_push($expenseAmount, round((float) json_decode($request->balanceAmountArray[$index1]), 2) );
                }
            }
            $incomeAmount=(array_sum($incomeCrAmount)-array_sum($incomeDrAmount));
            // $surplusAmount=$incomeAmount-array_sum($expenseAmount);
            $surplusAmount=round($incomeAmount-array_sum($expenseAmount),2);

            // $addEquity = new AccEquity;
            // $addEquity->obId = trim($obId);
            // $addEquity->projectId = $request->projectId;
            // $addEquity->branchId = $request->branchId;
            // $addEquity->projectTypeId = $request->projectTypeId;
            // $addEquity->openingDate = $openingDate;
            // $addEquity->fiscalYearId = $fiscalYearId;
            // $addEquity->reserveFundAmount = $surplusAmount;
            // $addEquity->surplusAmount = $surplusAmount;
            // $addEquity->createdDate = $now;
            // $addEquity->save();
            // $dataArray=array(
            //     'incomeLedgersArray'=> $incomeLedgersArray,
            //     'expenseLedgersArray'=> $expenseLedgersArray,
            //     'incomeAmount'=> $incomeAmount,
            //     'expenseAmount'=> array_sum($expenseAmount),
            //     'surplusAmount'=> $surplusAmount,
            //     'addEquity'=> $addEquity,
            //     );

            // return response()->json($dataArray);
            $logArray = array(
                'moduleId'  => 4,
                'controllerName'  => 'AccOpeningBalanceController',
                'tableName'  => 'acc_opening_balance',
                'operation'  => 'insert',
                'primaryIds'  => [DB::table('acc_opening_balance')->max('id')]
            );
            Service::createLog($logArray);
            return response()->json(['responseText' => 'Data successfully inserted!'], 200);
        }
    }

    public function updateOpeningBalanceItem(Request $request){
        $rules = array(
            'projectId' => 'required',
            'branchId' => 'required',
            'projectTypeId' => 'required',
            'openingDate' => 'required'
        );
        $attributeNames = array(
            'projectId'    => 'Project Name',
            'branchId'   => 'Branch Name',
            'projectTypeId'   => 'Project Type Name',
            'openingDate'   => 'Opening Date'
        );
        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()){
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }else{
            // dd($request->all());
            $now = Carbon::now();
            // $now = date('Y-m-d H:i:s');
            // $previousdata =DB::table('acc_opening_balance')->find ($request->id);
            //dd($previousdata);
            $openingDate=Carbon::parse($request->openingDate)->format('Y-m-d');
            $fiscalYearId=DB::table('gnr_fiscal_year')->where('fyStartDate', '<=', $openingDate)->where('fyEndDate', '>=', $openingDate)->value('id');
            $obId=$fiscalYearId.".".$request->projectId.".".$request->branchId.".".$request->projectTypeId;
            $obDatas = DB::table('acc_opening_balance')->where('obId', $obId)->get();
            // $array_size = count($request->openingBalanceIdArray);
            $array_size = count($request->ledgerIdArray);
            for ($i=0; $i<$array_size; $i++){

                $data = array(

                    'obId' => $obId,
                    'projectId' => $request->projectId,
                    'branchId' => $request->branchId,
                    'projectTypeId' => $request->projectTypeId,
                    'openingDate' => $openingDate,
                    'fiscalYearId' => $fiscalYearId,
                    'generateType' => 1,

                    'ledgerId' => (int) json_decode($request->ledgerIdArray[$i]),
                    'debitAmount' => round((float) json_decode($request->debitAmountArray[$i]), 2),
                    'creditAmount' => round((float) json_decode($request->creditAmountArray[$i]), 2),
                    'balanceAmount' => round((float) json_decode($request->balanceAmountArray[$i]), 2)
                );

                $ledgerId=(int) json_decode($request->ledgerIdArray[$i]);
                $obUpdateRow = $obDatas->where('ledgerId', $ledgerId)->first();
                // dd($obUpdateRow);
                if ($obUpdateRow) {
                    DB::table('acc_opening_balance')->where('obId', $obId)->where('ledgerId', $ledgerId)->update($data);
                }
                else {
                    DB::table('acc_opening_balance')->insert($data);
                }

            }

            $incomeLedgersArray = DB::table('acc_account_ledger')->where('accountTypeId',12)->where('isGroupHead',0)->pluck('id')->toArray();
            $expenseLedgersArray = DB::table('acc_account_ledger')->where('accountTypeId',13)->where('isGroupHead',0)->pluck('id')->toArray();

            $incomeCrAmount=$incomeDrAmount=$expenseAmount=$ledgerIdArray=array();

            foreach ($request->ledgerIdArray as $eachValue) {
                array_push($ledgerIdArray, (int) json_decode($eachValue));
            }
            foreach ($ledgerIdArray as $index1 => $ledgerId) {
                if(in_array($ledgerId, $incomeLedgersArray)){
                    array_push($incomeCrAmount, round((float) json_decode($request->creditAmountArray[$index1]), 2) );
                    array_push($incomeDrAmount, round((float) json_decode($request->debitAmountArray[$index1]), 2) );
                }else if(in_array($ledgerId, $expenseLedgersArray)){
                    array_push($expenseAmount, round((float) json_decode($request->balanceAmountArray[$index1]), 2) );
                }
            }
            $incomeAmount=(array_sum($incomeCrAmount)-array_sum($incomeDrAmount));
            // $surplusAmount=$incomeAmount-array_sum($expenseAmount);
            $surplusAmount=round($incomeAmount-array_sum($expenseAmount),2);
            // $logArray = array(
            //     'moduleId'  => 4,
            //     'controllerName'  => 'AccOpeningBalanceController',
            //     'tableName'  => 'acc_opening_balance',
            //     'operation'  => 'update',
            //     'previousData'  => $previousdata,
            //     'primaryIds'  => [$previousdata->id]
            // );
            // Service::createLog($logArray);

            //Update data of Equity
            // $equityId = AccEquity::where('obId',$request->obId)->where('projectId',$request->projectId)->where('branchId',$request->branchId)->where('projectTypeId',$request->projectTypeId)->value('id');
            // $updateEquity = AccEquity::find($equityId);

            // $updateEquity->obId = trim($request->obId);
            // $updateEquity->projectId = $request->projectId;
            // $updateEquity->branchId = $request->branchId;
            // $updateEquity->projectTypeId = $request->projectTypeId;
            // $updateEquity->openingDate = $openingDate;
            // $updateEquity->fiscalYearId = $fiscalYearId;

            // $updateEquity->reserveFundAmount = $surplusAmount;
            // $updateEquity->surplusAmount = $surplusAmount;
            // $updateEquity->save();

            // $dataArray=array(
            //     'updateEquity'=> $updateEquity,
            //     'ledgerIdArray'=> $ledgerIdArray,
            //     'incomeLedgersArray'=> $incomeLedgersArray,
            //     'debitAmountArray'=> $request->debitAmountArray,
            //     'creditAmountArray'=> $request->creditAmountArray,
            //     'balanceAmountArray'=> $request->balanceAmountArray,
            //     'expenseLedgersArray'=> $expenseLedgersArray
            //     );
            // return response()->json($dataArray);

            return response()->json(['responseText' => 'Data successfully inserted!'], 200);
        }
    }



}		//End AccOpeningBalanceController
