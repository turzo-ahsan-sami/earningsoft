<?php
namespace App\Http\Controllers\accounting\advRegister;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use Illuminate\Support\Facades\Input;
use Response;
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
          return response::json('success');
          }
/*----------------------------- store  data------------------------*/

        public function storeAdvanceReceive(Request $request){
            $rules = array(
              'advRegType'         => 'required',
              'advRegName'         => 'required',
              'project'            => 'required',
              'projectType'        => 'required',
              'advReceiveAmount'   => 'required',
              'paymentDate'        => 'required'
                     
            );

//Radio button required

            if($request->advReceiveChange==null) {
              $rules = $rules + array( 'advReceiveChange'=> 'required',);
            }
            else if($request->advReceiveChange=='a'){
              $rules = $rules + array( 'cachfield'=> 'required',);
            }
            else if($request->advReceiveChange=='b'){
              $rules = $rules + array( 'vauchar'=> 'required',);
            }
            else if($request->advReceiveChange=='c'){
              $rules = $rules + array( 'bank'=> 'required',);
            }

          $attributeNames = array(
              'advRegType'           => 'Advance Receive Type',
              'advRegName'           => 'Advance Receive Name',
              'advReceiveChange'     => 'Advance Receive Type',
              'project'              => 'project',
              'projectType'          => 'project Type',
              'cachfield'            => 'cach field',
              'vauchar'              => 'vauchar field',
              'bank'                 => 'bank name',
              'advReceiveAmount'     => 'advance Receive Amount',
              'paymentDate'          => 'advance Receive Date'
          
          );
          $validator = Validator::make ( Input::all (), $rules);
          $validator->setAttributeNames($attributeNames);
          if ($validator->fails()) {
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
          } 

          else{
              $paymentDate = Carbon::parse($request->paymentDate);
              $accAdvanceReceive = new AccAdvanceReceive;
              $accAdvanceReceive->advReceiveId = $request->advReceiveNumber;
              $accAdvanceReceive->projectId= $request->project;
              $accAdvanceReceive->projectTypeId= $request->projectType;
              $accAdvanceReceive->regTypeId= $request->advRegType;
             
                            
              if($request->advRegChange==1) {
                  $accAdvanceReceive->houseOwnerId = $request->advRegName;
              }

              elseif($request->advRegChange==2) {
                  $accAdvanceReceive->supplierId = $request->advRegName;
              }
              elseif($request->advRegChange==3) {
                  $accAdvanceReceive->employeeId = $request->advRegName;
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

          if($request->advRegChange==1) {
              $totalAmount=(float)DB::table('acc_adv_register')->where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('advRegType',$request->advRegType)->where('houseOwnerId',$request->advRegName)->sum('amount');

              $reciveAmount=DB::table('acc_adv_receive')->where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('regTypeId',$request->advRegType)->where('houseOwnerId',$request->advRegName)->sum('amount');

              $payableAmount =$totalAmount-$reciveAmount;

              $totalAdvNos=AccAdvRegister::where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('advRegType',$request->advRegType)->where('houseOwnerId',$request->advRegName)->get();
                   
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
        else if($request->advRegChange==2) {
            $totalAmount=(float) DB::table('acc_adv_register')->where('projectId',$request->project)->where('projectTypeId',[$request->projectType])->where('advRegType',$request->advRegType)->where('supplierId',$request->advRegName)->sum('amount');

            $reciveAmount=DB::table('acc_adv_receive')->where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('regTypeId',$request->advRegType)->where('supplierId',$request->advRegName)->sum('amount');

            $payableAmount =$totalAmount-$reciveAmount;

            $totalAdvNos=AccAdvRegister::where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('advRegType',$request->advRegType)->where('supplierId',$request->advRegName)->get();
              
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
        else if($request->advRegChange==3) {
            $totalAmount=(float)DB::table('acc_adv_register')->where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('advRegType',$request->advRegType)->where('employeeId',$request->advRegName)->sum('amount');

            $reciveAmount=(float)DB::table('acc_adv_receive')->where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('regTypeId',$request->advRegType)->where('employeeId',$request->advRegName)->sum('amount');

            $payableAmount =$totalAmount-$reciveAmount;

            $totalAdvNos=AccAdvRegister::where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('advRegType',$request->advRegType)->where('employeeId',$request->advRegName)->get();
                  
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

           return response::json('success');

       }
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
              $amount=(float)DB::table('acc_adv_register')->where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('advRegType',$request->advRegType)->where('houseOwnerId',$request->advRegName)->sum('amount');

              $reciveAmount=DB::table('acc_adv_receive')->where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('regTypeId',$request->advRegType)->where('houseOwnerId',$request->advRegName)->sum('amount');
              $paidAmount = $amount - $reciveAmount;
              
          }

          else if($request->advRegChange==2) {
              $amount=(float) DB::table('acc_adv_register')->where('projectId',$request->project)->where('projectTypeId',[$request->projectType])->where('advRegType',$request->advRegType)->where('supplierId',$request->advRegName)->sum('amount');

              $reciveAmount=DB::table('acc_adv_receive')->where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('regTypeId',$request->advRegType)->where('supplierId',$request->advRegName)->sum('amount');
              $paidAmount = $amount - $reciveAmount;

          }

          else if($request->advRegChange==3) {
              $amount=(float)DB::table('acc_adv_register')->where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('advRegType',$request->advRegType)->where('employeeId',$request->advRegName)->sum('amount');

              $reciveAmount=DB::table('acc_adv_receive')->where('projectId',$request->project)->where('projectTypeId',$request->projectType)->where('regTypeId',$request->advRegType)->where('employeeId',$request->advRegName)->sum('amount');

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

            $employee =DB::table('hr_emp_general_info')->where('id',$accAdvanceReceive->employeeId)->value('emp_id').DB::table('hr_emp_general_info')->where('id',$accAdvanceReceive->employeeId)->value('emp_name_english');

            $supplier = DB::table('gnr_supplier')->where('id',$accAdvanceReceive->supplierId)->value('supplierCompanyName');

            $houseOwner = DB::table('gnr_house_Owner')->where('id',$accAdvanceReceive->houseOwnerId)->value('houseOwnerName');

            $project = DB::table('gnr_project')->where('id',$accAdvanceReceive->projectId)->value('name');

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
                'bank'                       =>  $bank
            );

              return response::json($data);

        }

        public function getAdvRecInfo(Request $request) {
            $accAdvanceReceive =  AccAdvanceReceive::find($request->id);

            $advRegType = DB::table('acc_adv_register_type')->where('id',$accAdvanceReceive->regTypeId)->value('name');

            $employee =DB::table('hr_emp_general_info')->where('id',$accAdvanceReceive->employeeId)->value('emp_id').DB::table('hr_emp_general_info')->where('id',$accAdvanceReceive->employeeId)->value('emp_name_english');

            $supplier = DB::table('gnr_supplier')->where('id',$accAdvanceReceive->supplierId)->value('supplierCompanyName');

            $houseOwner = DB::table('gnr_house_Owner')->where('id',$accAdvanceReceive->houseOwnerId)->value('houseOwnerName');

            $project = DB::table('gnr_project')->where('id',$accAdvanceReceive->projectId)->value('name');

            $cash= DB::table('acc_account_ledger')->where('id',$accAdvanceReceive->cashId)->value('name');

            $bank = DB::table('acc_account_ledger')->where('id',$accAdvanceReceive->bankId)->value('name');

            if($accAdvanceReceive->houseOwnerId>0) {
                $index=$accAdvanceReceive->houseOwnerId;
                $value = (float)$accAdvanceReceive->amount;

                $totalAmount=AccAdvRegister::where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('advRegType',$accAdvanceReceive->regTypeId)->where('houseOwnerId',$index)->sum('amount');
                         
                $reciveAmount=DB::table('acc_adv_receive')->where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('regTypeId',$accAdvanceReceive->regTypeId)->where('houseOwnerId',$index)->sum('amount');

                $payableAmount =$totalAmount+$value-$reciveAmount;
            
            }
            elseif($accAdvanceReceive->supplierId>0) {
                $index=$accAdvanceReceive->supplierId;
                $value = (float)$accAdvanceReceive->amount;
                $totalAmount=AccAdvRegister::where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('advRegType',$accAdvanceReceive->regTypeId)->where('supplierId',$index)->sum('amount');

                $reciveAmount=DB::table('acc_adv_receive')->where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('regTypeId',$accAdvanceReceive->regTypeId)->where('supplierId',$index)->sum('amount');

                $payableAmount =$totalAmount+$value-$reciveAmount;
            }
            else if($accAdvanceReceive->employeeId>0) {
                $index=$accAdvanceReceive->employeeId;
                $value =(float)$accAdvanceReceive->amount;

                $totalAmount=AccAdvRegister::where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('advRegType',$accAdvanceReceive->regTypeId)->where('employeeId',$index)->sum('amount');

                $reciveAmount=(float)DB::table('acc_adv_receive')->where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('regTypeId',$accAdvanceReceive->regTypeId)->where('employeeId',$index)->sum('amount');

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

                  $totalAmount=AccAdvRegister::where('projectId',$accAdvanceReceive->project)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('advRegType',$accAdvanceReceive->regTypeId)->where('houseOwnerId',$index)->sum('amount');
                     
                  $reciveAmount=DB::table('acc_adv_receive')->where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('regTypeId',$accAdvanceReceive->regTypeId)->where('houseOwnerId',$index)->sum('amount');

                  $payableAmount =$totalAmount+$value-$reciveAmount;

                  $totalAdvNos=AccAdvRegister::where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('advRegType',$accAdvanceReceive->regTypeId)->where('houseOwnerId',$index)->get();
                    
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
            elseif($accAdvanceReceive->supplierId>0) {
                $index=$accAdvanceReceive->supplierId;
                $value =(float)$accAdvanceReceive->amount;

                $totalAmount=AccAdvRegister::where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('advRegType',$accAdvanceReceive->regTypeId)->where('supplierId',$index)->sum('amount');

                $reciveAmount=(float)DB::table('acc_adv_receive')->where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('regTypeId',$accAdvanceReceive->regTypeId)->where('supplierId',$index)->sum('amount');

                $totalAdvNos=AccAdvRegister::where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('advRegType',$accAdvanceReceive->regTypeId)->where('supplierId',$index)->get();
                    
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
          else if($accAdvanceReceive->employeeId>0) {
              $index=$accAdvanceReceive->employeeId;
              $value =(float)$accAdvanceReceive->amount;

              $totalAmount=AccAdvRegister::where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('advRegType',$accAdvanceReceive->regTypeId)->where('employeeId',$index)->sum('amount');

              $reciveAmount=(float)DB::table('acc_adv_receive')->where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('regTypeId',$accAdvanceReceive->regTypeId)->where('employeeId',$index)->sum('amount');

              $totalAdvNos=AccAdvRegister::where('projectId',$accAdvanceReceive->projectId)->where('projectTypeId',$accAdvanceReceive->projectTypeId)->where('advRegType',$accAdvanceReceive->regTypeId)->where('employeeId',$index)->get();
                    
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
           return response::json('success');
       }
   }

?>

