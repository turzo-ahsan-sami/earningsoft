<?php

namespace App\Http\Controllers\accounting\autoVouchers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\accounting\AccMisType;
use App\accounting\AddVoucherType;
use App\accounting\AccMisConfiguration;
use App\accounting\AccAutoVoucherConfigForAll;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\accounting\Accounting;

class AccAutoVoucherConfigForAllController extends Controller
{
    protected $Accounting;

    public function __construct() {
        $this->Accounting = new Accounting;
    }

    public function index(){

        $misTypeOption =array('' => '--Select MIS Type--') + $this->Accounting->getMisTypeOption();
        $voucherTypes  =array('' => '--Select Voucher--') + $this->Accounting->getVoucherTypesOption();
        $moduleOption  =array('' => '--Select Module--') + $this->Accounting->getModuleOption();
        $autoVoucherConfigInfos = AccAutoVoucherConfigForAll::groupBy('configId')->get();

        $viewArr=   array(  'autoVoucherConfigInfos'    => $autoVoucherConfigInfos, 
                            'misTypeOption'             => $misTypeOption,
                            'moduleOption'              => $moduleOption,
                            'voucherTypes'              => $voucherTypes
                        );

        return view('accounting.autoVouchers.autoVoucherCofigForAll.viewAutoVoucherConfig', $viewArr);
    }

    public function addAutoVoucherConfigForAll(){

        $misTypeOption=array('' => '--Select MIS Type--') + $this->Accounting->getMisTypeOption();
        $voucherTypes=array('' => '--Select Voucher--') + $this->Accounting->getVoucherTypesOption();
        $moduleOption=array('' => '--Select Module--') + $this->Accounting->getModuleOption();

        return view('accounting.autoVouchers.autoVoucherCofigForAll.addAutoVoucherConfig',['moduleOption'=> $moduleOption,'misTypeOption'=> $misTypeOption, 'voucherTypes'=> $voucherTypes]);
    }

    public function addAutoVoucherConfigForAllItem(Request $request) {
        $rules = array(
            'moduleId' => 'required',
            'misType' => 'required',
            'voucherType' => 'required',
            'localNarration' => 'required'
        );
        $attributeNames = array(
            'moduleId'    => 'Module',
            'misType'    => 'MIS Type',
            'voucherType'   => 'Voucher Type',
            'localNarration'   => 'Local Narration'
        );

 		$validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails())
        	return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  		else{

            $now = Carbon::now();
            // $ledgerCodeArrphp=array();
            $configId=DB::table('acc_auto_voucher_config')->max('configId');
            if ($configId==null) { $configId=1; }else{ $configId++;}
            $dataInsert = [];

            foreach ($request->amountType as $index => $amountTypeValue) {
                if( (!empty($request->ledgerId[$index])) && ((int)json_decode($amountTypeValue)!=null) ){
                // if( ( (int)json_decode($amountTypeValue)!=null) ){
                // if(((int) json_decode($ledgerCode)!=null ) && ((int) json_decode($request->amountType[$index])!=null)){
                // if(((int) $ledgerCode!=null ) && ((int) $request->accountType[$index]!=null)){
                    // array_push($ledgerCodeArrphp, (int) json_decode($ledgerCo));
                    // $ledgerId=DB::table('acc_account_ledger')->where('code', (int) json_decode($ledgerCode))->value('id');

                    $dataInsert[] = array(                    
                        'configId'      => $configId,
                        'moduleId'      => $request->moduleId,
                        'misTypeId_Fk'  => $request->misType,
                        'voucherType'   => $request->voucherType,

                        'amountType'    => (int) json_decode($amountTypeValue),
                        'ledgerId'      => json_encode($request->ledgerId[$index]),
                        // 'ledgerCode'    => (int) json_decode($request->ledgerId),
                        'localNarration'=> $request->localNarration,

                        'misConfigId'   => (int) json_decode($request->misConfigId[$index]),
                        'misConfigName' => $request->misConfigName[$index],
                        'tableFieldName'=> $request->tableFieldName[$index],
                        'createdDate'   => $now
                    );
                }
                    // array_push($ledgerCodeArrphp, (int) json_decode($request->amountType[$index]));
            }
            $insertAutoConf=DB::table('acc_auto_voucher_config')->insert($dataInsert);

            // foreach ($request->ledgerCodeArray as $eachValue) {
            //     array_push($ledgerCodeArrphp, (int) json_decode($eachValue));
            // }


            // $data=array(
            //     // 'ledgerCodeArrphp'=>$ledgerCodeArrphp,
            //     'configId'=>$configId,
            //     'dataInsert'=>$dataInsert
            //     );

            if ($insertAutoConf) {
                $data = array(
                            'responseTitle' =>  'Success!',
                            'responseText'  =>  ' Auto Voucher Configuration Save Successful.'
                        );
            }else{
                $data = array(
                            'responseTitle' =>  'Warning!',
                            'responseText'  =>  ' Auto Voucher Configuration Save Unsuccessful.'
                        );          
            }


            return response()->json($data);
            // return response()->json(['responseText' => 'Data successfully inserted!'], 200);
    	}
    }

    public function checkPreMISConfigData(Request $request){
        $misInfo=DB::table('acc_auto_voucher_config')->where('salesType', $request->misType)->where('voucherType', $request->voucherType)->pluck('id')->toArray();
        
        return response()->json($misInfo); 
    }

    public function getMISConfigData(Request $request){
        $matchedVoucherConfigId=DB::table('acc_mis_configuration')->where('misTypeId_Fk', $request->misType)->where('moduleId', $request->moduleId)->select('id', 'misName', 'tableFieldName')->get();

        return response()->json($matchedVoucherConfigId); 
    }

    public function editAutoVoucherConfigItem(Request $request){
        $autoVoucherConfigDetails=DB::table('acc_auto_voucher_config')->where('configId', $request->configId)->select('id','ledgerId','misConfigName','amountType')->get();
        
        return response()->json($autoVoucherConfigDetails); 
    }

    public function updateAutoVoucherConfigItem(Request $request) {
        $rules = array(
            'misType' => 'required',
            'voucherType' => 'required',
            'localNarration' => 'required'
        );
        $attributeNames = array(
            'misType'    => 'MIS Type',
            'voucherType'   => 'Voucher Type',
            'localNarration'   => 'Local Narration'
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails())
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else{

            // foreach ($request->ledgerCodes as $index => $ledgerCode) {
            $updated=false;
            foreach ($request->autoVouConfigId as $index => $updateAutoVouConfigId) {
                // if(((int) json_decode($ledgerCode)!=null ) && ((int) json_decode($request->amountTypeArray[$index])!=null)){
                    // $ledgerId=DB::table('acc_account_ledger')->where('code', (int) json_decode($ledgerCode))->value('id');

                    $dataUpdate = array(                    
                        // 'configId' =>       $configId,
                        // 'misType' =>      $request->misType,
                        // 'voucherType' =>    $request->voucherType,

                        // 'id' =>     (int) json_decode($updateAutoVouConfigId),
                        'amountType' =>     (int) json_decode($request->amountType[$index]),
                        'ledgerId' =>        json_encode($request->ledgerId[$index]),
                        // 'ledgerCode' =>     (int) json_decode($ledgerCode),
                        'localNarration' => $request->localNarration
                        // 'misConfigId' =>    (int) json_decode($request->misConfigIdArray[$index]),
                        // 'misConfigName' =>  trim((string) json_decode($request->misConfigNameArray[$index])),
                        // 'tableFieldName' => trim((string) json_decode($request->tableFieldNameArray[$index])),
                    );
                // }

                $updateAutoVouConfigId=(int) json_decode($updateAutoVouConfigId);
                $updateStatus=DB::table('acc_auto_voucher_config')->where('id', $updateAutoVouConfigId)->update($dataUpdate);
                
                if($updateStatus){ $updated=true; }

            }


            if ($updated) {
                $data = array(
                            'updateStatus' =>  $updateStatus,
                            'responseTitle' =>  'Success!',
                            'responseText'  =>  'Auto Voucher Configuration Update Successful.'
                        );
            }else{
                $data = array(
                            'updateStatus' =>  $updateStatus,
                            'responseTitle' =>  'Warning!',
                            'responseText'  =>  'Auto Voucher Configuration Update Unsuccessful.'
                        );          
            }

            // $data = array(
            //     // 'autoVouConfigId'     => $autoVouConfigId,
            //     'updated'     => $updated,
            //     'updateStatus'     => $updateStatus,
            // );

            return response()->json($data);
            // return response()->json(['responseText' => 'Data successfully inserted!'], 200);
        }
    }

    public function deleteAutoVoucherConfigItem(Request $request){

        $configId=AccAutoVoucherConfigForAll::where('id',$request->id)->value('configId');
        $deletedAutoVoucherConfig=AccAutoVoucherConfigForAll::where('configId',$configId)->delete();

        if ($deletedAutoVoucherConfig) {
            $data = array(
                        'responseTitle' =>  'Success!',
                        'responseText'  =>  'Auto Voucher Configuration Delete Successful.'
                    );
        }else{
            $data = array(
                        'responseTitle' =>  'Warning!',
                        'responseText'  =>  'Auto Voucher Configuration Delete Unsuccessful.'
                    );          
        }
//        $accountTypes = AddAccountType::all();
        return response()->json($data);     //json($accountTypes);
    }

}
