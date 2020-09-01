<?php
namespace App\Http\Controllers\accounting\advRegister;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use Illuminate\Support\Facades\Input;
use Response;
use App\Http\Controllers\gnr\Service;
use Carbon\Carbon;
use DB;
use App\gnr\GnrHouseOwner;
use App\gnr\GnrSupplier;
use App\gnr\GnrEmployee;
use App\accounting\AccAdvRegister;
use App\accounting\AccAdvRegisterType;
use App\accounting\AccAdvanceReceive;

class AccAdvRegReceiveController extends Controller {

 /*---------------------View List -----------------------------*/

 public function index(){
  $accAdvanceReceive = AccAdvanceReceive::all();

  return view('accounting.register.advRegister.advReceiveview',['accAdvanceReceive'=>$accAdvanceReceive]);
}

/*--------------------------Auto Code Genaretor---------------------------*/
public function createAdvanceReceive(Request $request) {
  $maxAdvReceiveNumber = AccAdvanceReceive::max('id')+1;
  $advReceiveNumber = 'AR'.str_pad($maxAdvReceiveNumber,6,'0',STR_PAD_LEFT);

  return view('accounting.register.advRegister.addAdvReceive',['advReceiveNumber'=>$advReceiveNumber]);
}
/*------------------ delete Data----------------------------*/

public function deleteadvReceive(Request $request) {
  $previousdata= AccAdvanceReceive::find($request->advReceive);
  $accAdvanceReceive= AccAdvanceReceive::find($request->advReceive);

  if($accAdvanceReceive->houseOwnerId>0) {
    $index=$accAdvanceReceive->houseOwnerId;
    $value =(float)$accAdvanceReceive->amount;

    $reciveAmount=(float)DB::table('acc_adv_receive')->where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('regTypeId',$accAdvanceReceive->regTypeId)->where('houseOwnerId',$index)->sum('amount');

    $payableAmount =$reciveAmount-$value;

    $totalAdvNos=AccAdvRegister::where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('advRegType',$accAdvanceReceive->regTypeId)->where('houseOwnerId',$index)->get();

    foreach ($totalAdvNos as $totalAdvNo) {
      if($payableAmount>=$totalAdvNo->amount){
       $totalAdvNo->status=0;
       $totalAdvNo->save();
     }
     else{
       $totalAdvNo->status=1;
       $totalAdvNo->save();
     }
     $payableAmount = $payableAmount - $totalAdvNo->amount;
   }
 }
 elseif($accAdvanceReceive->supplierId>0) {
  $index=$accAdvanceReceive->supplierId;
  $value =(float)$accAdvanceReceive->amount;

  $reciveAmount=(float)DB::table('acc_adv_receive')->where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('regTypeId',$accAdvanceReceive->regTypeId)->where('supplierId',$index)->sum('amount');

  $payableAmount =$reciveAmount-$value;

  $totalAdvNos=AccAdvRegister::where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('advRegType',$accAdvanceReceive->regTypeId)->where('supplierId',$index)->get();

  foreach ($totalAdvNos as $totalAdvNo) {
    if($payableAmount>=$totalAdvNo->amount){
     $totalAdvNo->status=0;
     $totalAdvNo->save();
   }
   else{
     $totalAdvNo->status=1;
     $totalAdvNo->save();
   }
   $payableAmount = $payableAmount - $totalAdvNo->amount;
 }
}
else if($accAdvanceReceive->employeeId>0) {
  $index=$accAdvanceReceive->employeeId;
  $value =(float)$accAdvanceReceive->amount;

  $reciveAmount=(float)DB::table('acc_adv_receive')->where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('regTypeId',$accAdvanceReceive->regTypeId)->where('employeeId',$index)->sum('amount');

  $payableAmount =$reciveAmount-$value;

  $totalAdvNos=AccAdvRegister::where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('advRegType',$accAdvanceReceive->regTypeId)->where('employeeId',$index)->get();

  foreach ($totalAdvNos as $totalAdvNo) {
    if($payableAmount>=$totalAdvNo->amount){
     $totalAdvNo->status=0;
     $totalAdvNo->save();
   }
   else{
     $totalAdvNo->status=1;
     $totalAdvNo->save();
   }
   $payableAmount = $payableAmount - $totalAdvNo->amount;
 }
}
$accAdvanceReceive->delete();

$logArray = array(
  'moduleId'  => 4,
  'controllerName'  => 'AccAdvRegReceiveController',
  'tableName'  => 'acc_adv_register',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
Service::createLog($logArray);
return response::json('success');
}
/*----------------------------- store  data------------------------*/

public function storeAdvanceReceive(Request $request){
//      $rules = array(
//        'advRegType'         => 'required',
//        'advRegName'         => 'required',
//        'project'            => 'required',
//        'projectType'        => 'required',
//        'advReceiveAmount'   => 'required',
//        'paymentDate'        => 'required'
//
//      );
//
// //Radio button required
//
//      if($request->advReceiveChange==null) {
//        $rules = $rules + array( 'advReceiveChange'=> 'required',);
//      }
//      else if($request->advReceiveChange=='a'){
//        $rules = $rules + array( 'cachfield'=> 'required',);
//      }
//      else if($request->advReceiveChange=='b'){
//        $rules = $rules + array( 'vauchar'=> 'required',);
//      }
//      else if($request->advReceiveChange=='c'){
//        $rules = $rules + array( 'bank'=> 'required',);
//      }
//
//    $attributeNames = array(
//        'advRegType'           => 'Advance Receive Type',
//        'advRegName'           => 'Advance Receive Name',
//        'advReceiveChange'     => 'Advance Receive Type',
//        'project'              => 'project',
//        'projectType'          => 'project Type',
//        'cachfield'            => 'cach field',
//        'vauchar'              => 'vauchar field',
//        'bank'                 => 'bank name',
//        'advReceiveAmount'     => 'advance Receive Amount',
//        'paymentDate'          => 'advance Receive Date'
//
//    );
//    $validator = Validator::make ( Input::all (), $rules);
//    $validator->setAttributeNames($attributeNames);
//    if ($validator->fails()) {
//      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
//    }


 $paymentDate = Carbon::parse($request->paymentDate);
 $accAdvanceReceive = new AccAdvanceReceive;
 $accAdvanceReceive->advReceiveId = $request->advReceiveNumber;
 $accAdvanceReceive->projectId= $request->projectIdR;
 $accAdvanceReceive->projectTypeId= $request->projectTypeIdHiddenR;
 $accAdvanceReceive->regTypeId= $request->advRegTypeIdHiddenR;
 $accAdvanceReceive->chequeNo =$request->advChequeNumber;
 $accAdvanceReceive->regIdFk =$request->regIdFk;

 if($request->serviceCatagoryIdHiddenR==1) {
   $accAdvanceReceive->houseOwnerId = $request->advRegNameIdHiddenR;
 }

 elseif($request->serviceCatagoryIdHiddenR==2) {
   $accAdvanceReceive->supplierId = $request->advRegNameIdHiddenR;
 }
 elseif($request->serviceCatagoryIdHiddenR==3) {
   $accAdvanceReceive->employeeId = $request->advRegNameIdHiddenR;
 }
 if($request->advReceiveChange=='a') {
   $accAdvanceReceive->cashId = $request->cachfield;
 }
 elseif($request->advReceiveChange=='b') {
   $accAdvanceReceive->vaucharId = $request->vauchar;
 }
 elseif($request->advReceiveChange=='c') {
   $accAdvanceReceive->bankId = $request->bank;
 }
 $accAdvanceReceive->amount = $request->advReceiveAmount;
 $accAdvanceReceive->receivePaymentDate = $paymentDate;
 $accAdvanceReceive->createAt = Carbon::now();

 $accAdvanceReceive->save();

 /*Status change */

 if($request->serviceCatagoryIdHiddenR==1) {
   $totalAmount=(float)DB::table('acc_adv_register')->where('id',$request->regIdFk)->where('projectId',$request->projectIdR)->where('projectTypeId',$request->projectTypeIdHiddenR)->where('advRegType',$request->advRegTypeIdHiddenR)->where('houseOwnerId',$request->advRegNameIdHiddenR)->sum('amount');

   $reciveAmount=DB::table('acc_adv_receive')->where('regIdFk',$request->regIdFk)->where('projectId',$request->projectIdR)->where('projectTypeId',$request->projectTypeIdHiddenR)->where('regTypeId',$request->advRegTypeIdHiddenR)->where('houseOwnerId',$request->advRegNameIdHiddenR)->sum('amount');

   $payableAmount =$totalAmount-$reciveAmount;

   $totalAdvNos=AccAdvRegister::where('projectId',$request->projectIdR)->where('id',$request->regIdFk)->where('projectTypeId',$request->projectTypeIdHiddenR)->where('advRegType',$request->advRegTypeIdHiddenR)->where('houseOwnerId',$request->advRegNameIdHiddenR)->get();

   foreach ($totalAdvNos as $totalAdvNo) {
     if($reciveAmount>=$totalAdvNo->amount){
       $totalAdvNo->status=0;

       $totalAdvNo->save();
     }
     else{
       $totalAdvNo->status=1;
       $totalAdvNo->save();
     }
     $reciveAmount=$reciveAmount-$totalAdvNo->amount;
   }

 }
 else if($request->serviceCatagoryIdHiddenR==2) {
   $totalAmount=(float) DB::table('acc_adv_register')->where('id',$request->regIdFk)->where('projectId',$request->projectIdR)->where('projectTypeId',[$request->projectTypeIdHiddenR])->where('advRegType',$request->advRegTypeIdHiddenR)->where('supplierId',$request->advRegNameIdHiddenR)->sum('amount');

   $reciveAmount=DB::table('acc_adv_receive')->where('regIdFk',$request->regIdFk)->where('projectId',$request->projectIdR)->where('projectTypeId',$request->projectTypeIdHiddenR)->where('regTypeId',$request->advRegTypeIdHiddenR)->where('supplierId',$request->advRegNameIdHiddenR)->sum('amount');

   $payableAmount =$totalAmount-$reciveAmount;

   $totalAdvNos=AccAdvRegister::where('projectId',$request->projectIdR)->where('id',$request->regIdFk)->where('projectTypeId',$request->projectTypeIdHiddenR)->where('advRegType',$request->advRegTypeIdHiddenR)->where('supplierId',$request->advRegNameIdHiddenR)->get();

   foreach ($totalAdvNos as $totalAdvNo) {
     if($reciveAmount>=$totalAdvNo->amount){
       $totalAdvNo->status=0;
       $totalAdvNo->save();
     }
     else{
       $totalAdvNo->status=1;
       $totalAdvNo->save();
     }
     $reciveAmount=$reciveAmount-$totalAdvNo->amount;
   }

 }
 else if($request->serviceCatagoryIdHiddenR==3) {
   $totalAmount=(float)DB::table('acc_adv_register')->where('id',$request->regIdFk)->where('projectId',$request->projectIdR)->where('projectTypeId',$request->projectTypeIdHiddenR)->where('advRegType',$request->advRegTypeIdHiddenR)->where('employeeId',$request->advRegNameIdHiddenR)->sum('amount');

   $reciveAmount=(float)DB::table('acc_adv_receive')->where('regIdFk',$request->regIdFk)->where('projectId',$request->projectIdR)->where('projectTypeId',$request->projectTypeIdHiddenR)->where('regTypeId',$request->advRegTypeIdHiddenR)->where('employeeId',$request->advRegNameIdHiddenR)->sum('amount');

   $payableAmount =$totalAmount-$reciveAmount;

   $totalAdvNos=AccAdvRegister::where('projectId',$request->projectIdR)->where('id',$request->regIdFk)->where('projectTypeId',$request->projectTypeIdHiddenR)->where('advRegType',$request->advRegTypeIdHiddenR)->where('employeeId',$request->advRegNameIdHiddenR)->get();

   foreach ($totalAdvNos as $totalAdvNo) {
     if($reciveAmount>=$totalAdvNo->amount){
       $totalAdvNo->status=0;

       $totalAdvNo->save();

     }
     else{
       $totalAdvNo->status=1;
       $totalAdvNo->save();

     }

     $reciveAmount=$reciveAmount-$totalAdvNo->amount;
   }

 }


 return response::json("success");

}
/* --------------------get data ------------------------------*/
public function viewAdvRegInfo(Request $request) {
  $accAdvanceReceive = AccAdvanceReceive::all();

  return view('accounting.register.advRegister.viewAdvRegisterList',['accAdvanceReceive'=>$accAdvanceReceive]);
}

/*------------- data change--------------------*/

public function advanceReceiveChange(Request $request) {
  if($request->advRegChange==1) {

    $response = DB::table('gnr_house_Owner')->select('id','houseOwnerName')->get();
  }

  elseif ($request->advRegChange==2) {

    $response = DB::table('gnr_supplier')->select('id','supplierCompanyName')->get();
  }

  elseif ($request->advRegChange==3) {
    $response = DB::table('hr_emp_general_info')->select('id','emp_name_english','emp_id')->orderBy('emp_id')->get();
  }
          //$payableAmount= 20;


  return response()->json($response);
}

public function advanceReceiveAmountChange(Request $request){

  if($request->advRegChange==1) {
    $amount=(float)DB::table('acc_adv_register')->where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('advRegType',$request->advRegType)->where('id',$request->advPaymentId)->where('houseOwnerId',$request->advRegName)->value('amount');

    $reciveAmount=DB::table('acc_adv_receive')->where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('regTypeId',$request->advRegType)->where('advReceiveId',$request->advPaymentId)->where('houseOwnerId',$request->advRegName)->sum('amount');
    $paidAmount = $amount - $reciveAmount;


  }

  else if($request->advRegChange==2) {
    $amount=(float) DB::table('acc_adv_register')->where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('advRegType',$request->advRegType)->where('id',$request->advPaymentId)->where('supplierId',$request->advRegName)->value('amount');


    $reciveAmount=DB::table('acc_adv_receive')->where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('regTypeId',$request->advRegType)->where('advReceiveId',$request->advPaymentId)->where('supplierId',$request->advRegName)->sum('amount');
    $paidAmount = $amount - $reciveAmount;

  }

  else if($request->advRegChange==3) {
    $amount=(float)DB::table('acc_adv_register')->where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('advRegType',$request->advRegType)->where('id',$request->advPaymentId)->where('employeeId',$request->advRegName)->value('amount');

    $reciveAmount=DB::table('acc_adv_receive')->where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('regTypeId',$request->advRegType)->where('advReceiveId',$request->advPaymentId)->where('employeeId',$request->advRegName)->sum('amount');

    $paidAmount = $amount - $reciveAmount;

  }
  $data=array(
    'amount'          =>$amount,
    'paidAmount'      =>$paidAmount,
    'reciveAmount'    =>$reciveAmount
  );

  return response::json($data);
}


/*-------------------- View Data -----------------------------*/

public function viewAdvReceive(Request $request) {
  $accAdvanceReceive =  AccAdvanceReceive::find($request->id);

  $advRegType = DB::table('acc_adv_register_type')->where('id',$accAdvanceReceive->regTypeId)->value('name');
  $advPaymentId = DB::table('acc_adv_register')->where('id',$accAdvanceReceive->advRegId)->value('advRegId');


  $employee =DB::table('hr_emp_general_info')->where('id',$accAdvanceReceive->employeeId)->value('emp_id').DB::table('hr_emp_general_info')->where('id',$accAdvanceReceive->employeeId)->value('emp_name_english');

  $supplier = DB::table('gnr_supplier')->where('id',$accAdvanceReceive->supplierId)->value('supplierCompanyName');

  $houseOwner = DB::table('gnr_house_Owner')->where('id',$accAdvanceReceive->houseOwnerId)->value('houseOwnerName');

  $project = DB::table('gnr_project')->where('id',$accAdvanceReceive->projectId)->value('name');
  $projectType = DB::table('gnr_project_type')->where('id',$accAdvanceReceive->projectTypeId)->value('name');

  $cash= DB::table('acc_account_ledger')->where('id',$accAdvanceReceive->cashId)->value('name');

  $bank = DB::table('acc_account_ledger')->where('id',$accAdvanceReceive->bankId)->value('name');

  $data = array(
    'accAdvanceReceive'          =>  $accAdvanceReceive,
    'advRegType'                 =>  $advRegType,
    'employee'                   =>  $employee,
    'supplier'                   =>  $supplier,
    'houseOwner'                 =>  $houseOwner,
    'cash'                       =>  $cash,
    'project'                    =>  $project,
    'bank'                       =>  $bank,
    'advPaymentId'               =>  $advPaymentId,
    'projectType'                =>  $projectType,
  );

  return response::json($data);

}

public function getAdvRecInfo(Request $request) {
  $accAdvanceReceive =  AccAdvanceReceive::find($request->id);

  $advRegType = DB::table('acc_adv_register_type')->where('id',$accAdvanceReceive->regTypeId)->value('name');
  $advPaymentId = DB::table('acc_adv_register')->where('id',$accAdvanceReceive->advRegId)->value('advRegId');

  $employee =DB::table('hr_emp_general_info')->where('id',$accAdvanceReceive->employeeId)->value('emp_id').DB::table('hr_emp_general_info')->where('id',$accAdvanceReceive->employeeId)->value('emp_name_english');

  $supplier = DB::table('gnr_supplier')->where('id',$accAdvanceReceive->supplierId)->value('supplierCompanyName');

  $houseOwner = DB::table('gnr_house_Owner')->where('id',$accAdvanceReceive->houseOwnerId)->value('houseOwnerName');

  $project = DB::table('gnr_project')->where('id',$accAdvanceReceive->projectId)->value('name');
  $projectType = DB::table('gnr_project_type')->where('id',$accAdvanceReceive->projectTypeId)->value('name');

  $cash= DB::table('acc_account_ledger')->where('id',$accAdvanceReceive->cashId)->value('name');

  $bank = DB::table('acc_account_ledger')->where('id',$accAdvanceReceive->bankId)->value('name');

  if($accAdvanceReceive->houseOwnerId>0) {
    $index=$accAdvanceReceive->houseOwnerId;
    $value = (float)$accAdvanceReceive->amount;

    $totalAmount=AccAdvRegister::where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('advRegType',$accAdvanceReceive->regTypeId)->where('id',$accAdvanceReceive->advRegId)->where('houseOwnerId',$index)->value('amount');

    $reciveAmount=DB::table('acc_adv_receive')->where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('regTypeId',$accAdvanceReceive->regTypeId)->where('advRegId',$accAdvanceReceive->advRegId)->where('houseOwnerId',$index)->sum('amount');

    $payableAmount =$totalAmount+$value-$reciveAmount;

  }
  elseif($accAdvanceReceive->supplierId>0) {
    $index=$accAdvanceReceive->supplierId;
    $value = (float)$accAdvanceReceive->amount;
    $totalAmount=AccAdvRegister::where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('advRegType',$accAdvanceReceive->regTypeId)->where('id',$accAdvanceReceive->advRegId)->where('supplierId',$index)->value('amount');

    $reciveAmount=DB::table('acc_adv_receive')->where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('regTypeId',$accAdvanceReceive->regTypeId)->where('advRegId',$accAdvanceReceive->advRegId)->where('supplierId',$index)->sum('amount');

    $payableAmount =$totalAmount+$value-$reciveAmount;
  }
  else if($accAdvanceReceive->employeeId>0) {
    $index=$accAdvanceReceive->employeeId;
    $value =(float)$accAdvanceReceive->amount;

    $totalAmount=AccAdvRegister::where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('advRegType',$accAdvanceReceive->regTypeId)->where('id',$accAdvanceReceive->advRegId)->where('employeeId',$index)->value('amount');



    $reciveAmount=(float)DB::table('acc_adv_receive')->where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('regTypeId',$accAdvanceReceive->regTypeId)->where('advRegId',$accAdvanceReceive->advRegId)->where('employeeId',$index)->sum('amount');

    $payableAmount =$totalAmount+$value-$reciveAmount;
  }

  $data = array(
    'accAdvanceReceive'          =>  $accAdvanceReceive,
    'advRegType'                 =>  $advRegType,
    'employee'                   =>  $employee,
    'supplier'                   =>  $supplier,
    'houseOwner'                 =>  $houseOwner,
    'cash'                       =>  $cash,
    'project'                    =>  $project,
    'bank'                       =>  $bank,
    'payableAmount'              =>  $payableAmount,
    'projectType'                =>  $projectType,
    'advPaymentId'               =>  $advPaymentId,

  );

  return response::json($data);
}
public function updateAdvReceiveInfo(Request $request){

  $rules = array(


                     //'amount'                  => 'required',
                     //'paymentDate'             => 'required'

  );

//Radio button required


  $attributeNames = array(
                  //'amount'                     => 'advance Receive Amount',
                  //'paymentDate'                => 'advance Receive Date'

  );


  $validator = Validator::make ( Input::all (), $rules);
  $validator->setAttributeNames($attributeNames);
  if ($validator->fails()) {
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));

  }
  else{
    $accPaymentDate  = Carbon::parse($request->paymentDate);
    $accAdvanceReceive = AccAdvanceReceive::where('id',$request->id)->first();
    $accAdvanceReceive->amount = $request->advReceiveAmount;
    $accAdvanceReceive->receivePaymentDate =$accPaymentDate;
    $accAdvanceReceive->save();
  }

  if($accAdvanceReceive->houseOwnerId>0) {
    $index=$accAdvanceReceive->houseOwnerId;
    $value = (float)$accAdvanceReceive->amount;

    $totalAmount=AccAdvRegister::where('projectId',$accAdvanceReceive->project)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('advRegType',$accAdvanceReceive->regTypeId)->where('id',$accAdvanceReceive->advRegId)->where('houseOwnerId',$index)->value('amount');

    $reciveAmount=DB::table('acc_adv_receive')->where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('regTypeId',$accAdvanceReceive->regTypeId)->where('advRegId',$accAdvanceReceive->advRegId)->where('houseOwnerId',$index)->sum('amount');

    $payableAmount =$totalAmount+$value-$reciveAmount;

    if( $payableAmount==0){
      AccAdvRegister::where('id',$accAdvanceReceive->advRegId)->Update(['status'=>'0']);

    }else{
     AccAdvRegister::where('id',$accAdvanceReceive->advRegId)->Update(['status'=>'1']);

   }

 }
 elseif($accAdvanceReceive->supplierId>0) {
  $index=$accAdvanceReceive->supplierId;
  $value =(float)$accAdvanceReceive->amount;

  $totalAmount=AccAdvRegister::where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('advRegType',$accAdvanceReceive->regTypeId)->where('id',$accAdvanceReceive->advRegId)->where('supplierId',$index)->value('amount');

  $reciveAmount=(float)DB::table('acc_adv_receive')->where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('regTypeId',$accAdvanceReceive->regTypeId)->where('supplierId',$index)->sum('amount');

  $payableAmount =$totalAmount+$value-$reciveAmount;

  if( $payableAmount==0){
    AccAdvRegister::where('id',$accAdvanceReceive->advRegId)->Update(['status'=>'0']);

  }else{
   AccAdvRegister::where('id',$accAdvanceReceive->advRegId)->Update(['status'=>'1']);

 }
}
else if($accAdvanceReceive->employeeId>0) {
  $index=$accAdvanceReceive->employeeId;
  $value =(float)$accAdvanceReceive->amount;

  $totalAmount=AccAdvRegister::where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('advRegType',$accAdvanceReceive->regTypeId)->where('id',$accAdvanceReceive->advRegId)->where('employeeId',$index)->value('amount');

  $reciveAmount=(float)DB::table('acc_adv_receive')->where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('regTypeId',$accAdvanceReceive->regTypeId)->where('employeeId',$index)->sum('amount');

  $payableAmount =$totalAmount+$value-$reciveAmount;

  if( $payableAmount==0){
    AccAdvRegister::where('id',$accAdvanceReceive->advRegId)->Update(['status'=>'0']);

  }else{
    AccAdvRegister::where('id',$accAdvanceReceive->advRegId)->Update(['status'=>'1']);

  }
}
return response::json('success');
}
}

?>
