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
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;


class AccAdvRegisterController extends Controller {

/*---------------------View List -----------------------------*/
    public function index(Request $request) {
        $accAdvRegisters = AccAdvRegister::all();

        return view('accounting.register.advRegister.viewAdvRegisterList',['accAdvRegister'=>$accAdvRegisters]);
    }

    /*--------------------------Auto Code Genaretor---------------------------*/
    public function createAdvanceRegesterFrom(Request $request) {
        $maxAdvRegNumber = AccAdvRegister::max('id')+1;
        $advRegNumber = 'AAR'.str_pad($maxAdvRegNumber,6,'0',STR_PAD_LEFT);

        return view('accounting.register.advRegister.addAdvRegister',['advRegNumber'=>$advRegNumber]
        );
     }
  /*------------------ delete Data----------------------------*/

    public function deleteadvanceRegister(Request $request) {

        $accAdvRegister= AccAdvRegister::find($request->advanceRegId);
        $accAdvRegister->delete();

        return response::json('success');

    }

 /*-------------------- Update data-----------------------*/

    public function storeAdvanceReg(Request $request) {
        $rules = array(
            'advRegType'          => 'required',
            'project'             => 'required',
            'projectType'         => 'required',
            'advRegName'          => 'required',
            'paymentTypeChange'   => 'required',
            'changePaymentType'   => 'required',
            'advRegAmount'        => 'required',
            'paymentDate'         => 'required'
                 
        );

        $attributeNames = array(

            'advRegType'          => 'Advance Register Type',
            'project'             => 'Project Name',
            'projectType'         => 'Project Type Name',
            'advRegName'          => 'Advance Register Name ',
            'changePaymentType'   => 'Advance Change Payment Type',
            'paymentTypeChange'   => 'Advance Payment Type',
            'advRegAmount'        =>'advance Register Amount',
            'paymentDate'         =>'advance Register Date'
          
        );


        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails()) {

            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        } 
        else{

            $paymentDate = Carbon::parse($request->paymentDate);
            $accAdvRegister = new AccAdvRegister;
            $accAdvRegister->advRegId = $request->advRegId;
            $accAdvRegister->projectId = $request->project;
            $accAdvRegister->projectTypeId = $request->projectType;
            $accAdvRegister->advRegType = $request->advRegType;
            if($request->advRegChange==1){
                $accAdvRegister->houseOwnerId = $request->advRegName;
            }
            elseif($request->advRegChange==2){
                $accAdvRegister->supplierId = $request->advRegName;
            }
            elseif($request->advRegChange==3){
                $accAdvRegister->employeeId = $request->advRegName;
            }
            if($request->paymentTypeChange=='cash'){
                $accAdvRegister->cashId = $request->changePaymentType;
            }
            elseif($request->paymentTypeChange=='bank'){
                $accAdvRegister->bankId = $request->changePaymentType;
            } 
            $accAdvRegister->amount = $request->advRegAmount;
            $accAdvRegister->advPaymentDate = $paymentDate;
            $accAdvRegister->createdAt = Carbon::now();
            $accAdvRegister->save();

            return response::json('success');

        }
  }
/* --------------------get data ------------------------------*/
    public function viewAdvRegInfo(Request $request) {
        $accAdvRegister = AccAdvRegister::all();

        return view('accounting.register.advRegister.viewAdvRegisterList',['accAdvRegister'=>$accAdvRegister]);

    }

 /*------------- service category data change--------------------*/

    public function advanceRegisterChange(Request $request) {
  
        if($request->advRegChange==1){
            $response = DB::table('gnr_house_Owner')->select('id','houseOwnerName')->get();
        }
        elseif ($request->advRegChange==2){
            $response = DB::table('gnr_supplier')->select('id','supplierCompanyName')->get();
        }
        elseif ($request->advRegChange==3){
            $response = DB::table('hr_emp_general_info')->select('id','emp_name_english','emp_id')->orderBy('emp_id')->get();
        }

            return response()->json($response);
    }

        /*-------------payment type data change--------------------*/

    public function paymentTypeChange(Request $request) {

        if($request->paymentTypeChange=='cash'){
            $response = DB::table('acc_account_ledger')->select('id','name')->where('accountTypeId',4)->get();
        }
        elseif ($request->paymentTypeChange=='bank'){
            $response = DB::table('acc_account_ledger')->select('id','name')->where('accountTypeId',5)->get();
        }

         return response()->json($response);
    }



/*-------------------- View Data -----------------------------*/

    public function viewAdvRegData(Request $request) {
        $accAdvRegister =  AccAdvRegister::find($request->id);
                
        $advRegType = DB::table('acc_adv_register_type')->where('id',$accAdvRegister->advRegType)->value('name');

        $project= DB::table('gnr_project')->where('id',$accAdvRegister->projectId)->value('name');

        $projectType= DB::table('gnr_project_type')->where('id',$accAdvRegister->projectTypeId)->value('name');

        $employee =DB::table('hr_emp_general_info')->where('id',$accAdvRegister->employeeId)->value('emp_id').DB::table('hr_emp_general_info')->where('id',$accAdvRegister->employeeId)->value('emp_name_english');

        $supplier = DB::table('gnr_supplier')->where('id',$accAdvRegister->supplierId)->value('supplierCompanyName');
            
        $houseOwner = DB::table('gnr_house_Owner')->where('id',$accAdvRegister->houseOwnerId)->value('houseOwnerName');
        $paymentTypeName="";

        if($accAdvRegister->cashId>0) {

            $paymentTypeName='Cash';
        }else if($accAdvRegister->bankId>0) {

            $paymentTypeName='Bank';
        }
        $data = array(
            'accAdvRegister'            => $accAdvRegister,
            'advRegType'                =>  $advRegType,
            'employee'                  => $employee,
            'supplier'                  =>  $supplier,
            'houseOwner'                =>$houseOwner,
            'project'                   =>$project,
            'projectType'               =>$projectType,
            'paymentTypeName'           =>$paymentTypeName
        );

         return response::json($data);
      }

    public function getAdvRegInfo(Request $request){

        $accAdvRegister =  AccAdvRegister::find($request->id);
                
        $advRegType = DB::table('acc_adv_register_type')->where('id',$accAdvRegister->advRegType)->value('name');

        $project= DB::table('gnr_project')->where('id',$accAdvRegister->projectId)->value('name');

        $projectType= DB::table('gnr_project_type')->where('id',$accAdvRegister->projectTypeId)->value('name');

        $employee =DB::table('hr_emp_general_info')->where('id',$accAdvRegister->employeeId)->value('emp_id').DB::table('hr_emp_general_info')->where('id',$accAdvRegister->employeeId)->value('emp_name_english');

        $supplier = DB::table('gnr_supplier')->where('id',$accAdvRegister->supplierId)->value('supplierCompanyName');
            
        $houseOwner = DB::table('gnr_house_Owner')->where('id',$accAdvRegister->houseOwnerId)->value('houseOwnerName');
        $paymentTypeName="";

        if($accAdvRegister->cashId>0) {

            $paymentTypeName='Cash';
        }
        else if($accAdvRegister->bankId>0)
        {
            $paymentTypeName='Bank';
        }
             
        if($accAdvRegister->cashId>0) {
            $cash = DB::table('acc_account_ledger')->where('id',$accAdvRegister->cashId)->where('accountTypeId',4)->select('id')->get();
            $ress=$cash;
            $ind=$accAdvRegister->cashId;

        }
        elseif($accAdvRegister->bankId>0) {
            $bank = DB::table('acc_account_ledger')->where('id',$accAdvRegister->bankId)->where('accountTypeId',5)->select('id')->get();
            $ress=$bank;
            $ind=$accAdvRegister->bankId;
        }

        if($accAdvRegister->houseOwnerId>0) {
            $index=$accAdvRegister->houseOwnerId;
            $value = (float)$accAdvRegister->amount;

            $totalAmount=AccAdvRegister::where('projectId',$accAdvRegister->projectId)->where('projectTypeId',$accAdvRegister->projectTypeId)->where('advRegType',$accAdvRegister->advRegType)->where('houseOwnerId',$index)->sum('amount');

            $reciveAmount=(float)DB::table('acc_adv_receive')->where('projectId',$accAdvRegister->projectId)->where('projectTypeId',$accAdvRegister->projectTypeId)->where('regTypeId',$accAdvRegister->advRegType)->where('houseOwnerId',$index)->sum('amount');
              
            $payableAmount =$totalAmount-$value;
        }
        elseif($accAdvRegister->supplierId>0) {
            $index=$accAdvRegister->supplierId;
            $value = (float)$accAdvRegister->amount;

            $totalAmount=AccAdvRegister::where('projectId',$accAdvRegister->projectId)->where('projectTypeId',$accAdvRegister->projectTypeId)->where('advRegType',$accAdvRegister->advRegType)->where('supplierId',$index)->sum('amount');
             
            $reciveAmount=DB::table('acc_adv_receive')->where('projectId',$accAdvRegister->projectId)->where('projectTypeId',$accAdvRegister->projectTypeId)->where('regTypeId',$accAdvRegister->advRegType)->where('supplierId',$index)->sum('amount');
             
            $payableAmount = $totalAmount-$value;

        }
        else if($accAdvRegister->employeeId>0) {
            $index=$accAdvRegister->employeeId;
            $value =(float)$accAdvRegister->amount;

            $totalAmount=AccAdvRegister::where('projectId',$accAdvRegister->projectId)->where('projectTypeId',$accAdvRegister->projectTypeId)->where('advRegType',$accAdvRegister->advRegType)->where('employeeId',$index)->sum('amount');

            $reciveAmount=(float)DB::table('acc_adv_receive')->where('projectId',$accAdvRegister->projectId)->where('projectTypeId',$accAdvRegister->projectTypeId)->where('regTypeId',$accAdvRegister->advRegType)->where('employeeId',$index)->sum('amount');
             
            $payableAmount =$totalAmount-$value;
              
        }
        $data = array(
            'accAdvRegister'            =>$accAdvRegister,
            'advRegType'                =>$advRegType,
            'employee'                  =>$employee,
            'supplier'                  =>$supplier,
            'houseOwner'                =>$houseOwner,
            'project'                   =>$project,
            'projectType'               =>$projectType,
            'paymentTypeName'           =>$paymentTypeName,
            'payableAmount'             =>$payableAmount,
            'reciveAmount'              =>$reciveAmount

        );
             return response::json($data); 

    }

    public function updateAdvRegInfo(Request $request) {

        $rules = array(
            /* 'regAmount'               => 'required',
            'regPaymentDate'            => 'required'*/
                         
            );
        $attributeNames = array(
            /*'regAmount'               =>'advance Register Amount',
            'regPaymentDate'            =>'advance Register Date'*/
                    
        );


        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails())  {

            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        } 


        else{

            $paymentDate = Carbon::parse($request->paymentDate);
            $accAdvRegister = AccAdvRegister::where('id',$request->id)->first();

            $accAdvRegister->amount = $request->advRegAmount;
            $accAdvRegister->advPaymentDate = $paymentDate;
            $accAdvRegister->save(); 
        }

        if($accAdvRegister->houseOwnerId>0) {
            $index=$accAdvRegister->houseOwnerId;
            $value = (float)$accAdvRegister->amount;

            $totalAmount=AccAdvRegister::where('projectId',$accAdvRegister->projectId)->where('projectTypeId',$accAdvRegister->projectTypeId)->where('advRegType',$accAdvRegister->advRegType)->where('houseOwnerId',$index)->sum('amount');
                 
                   
            $reciveAmount=DB::table('acc_adv_receive')->where('projectId',$accAdvRegister->projectId)->where('projectTypeId',$accAdvRegister->projectTypeId)->where('regTypeId',$accAdvRegister->advRegType)->where('houseOwnerId',$index)->sum('amount');
            $payableAmount =$totalAmount+$value-$reciveAmount;

            $totalAdvNos=AccAdvRegister::where('projectId',$accAdvRegister->projectId)->where('projectTypeId',$accAdvRegister->projectTypeId)->where('advRegType',$accAdvRegister->advRegType)->where('houseOwnerId',$index)->get();

            foreach ($totalAdvNos as $totalAdvNo) {
                if($totalAdvNo->amount>$reciveAmount){
                    $totalAdvNo->status=1;
                    $totalAdvNo->save();
                }
                else{
                    $totalAdvNo->status=0;
                    $totalAdvNo->save();
                }

                $reciveAmount=$reciveAmount-$totalAdvNo->amount;

            }
        }
            elseif($accAdvRegister->supplierId>0) {
                $index=$accAdvRegister->supplierId;
                $value = (float)$accAdvRegister->amount;

                $totalAmount=AccAdvRegister::where('projectId',$accAdvRegister->projectId)->where('projectTypeId',$accAdvRegister->projectTypeId)->where('advRegType',$accAdvRegister->advRegType)->where('supplierId',$index)->sum('amount');
                 
                   
                $reciveAmount=DB::table('acc_adv_receive')->where('projectId',$accAdvRegister->projectId)->where('projectTypeId',$accAdvRegister->projectTypeId)->where('regTypeId',$accAdvRegister->advRegType)->where('supplierId',$index)->sum('amount');
                $payableAmount =$totalAmount+$value-$reciveAmount;

                $totalAdvNos=AccAdvRegister::where('projectId',$accAdvRegister->projectId)->where('projectTypeId',$accAdvRegister->projectTypeId)->where('advRegType',$accAdvRegister->advRegType)->where('supplierId',$index)->get();

                foreach ($totalAdvNos as $totalAdvNo) {
                    if($totalAdvNo->amount>$reciveAmount){
                        $totalAdvNo->status=1;
                        $totalAdvNo->save();
                    }
                    else{
                        $totalAdvNo->status=0;
                        $totalAdvNo->save();
                    }
                        $reciveAmount=$reciveAmount-$totalAdvNo->amount;
                }
            }
            else if($accAdvRegister->employeeId>0) {
                $index=$accAdvRegister->employeeId;
                $value = (float)$accAdvRegister->amount;

                $totalAmount=AccAdvRegister::where('projectId',$accAdvRegister->projectId)->where('projectTypeId',$accAdvRegister->projectTypeId)->where('advRegType',$accAdvRegister->advRegType)->where('employeeId',$index)->sum('amount');
                   
                $reciveAmount=DB::table('acc_adv_receive')->where('projectId',$accAdvRegister->projectId)->where('projectTypeId',$accAdvRegister->projectTypeId)->where('regTypeId',$accAdvRegister->advRegType)->where('employeeId',$index)->sum('amount');
                $payableAmount =$totalAmount+$value-$reciveAmount;

                $totalAdvNos=AccAdvRegister::where('projectId',$accAdvRegister->projectId)->where('projectTypeId',$accAdvRegister->projectTypeId)->where('advRegType',$accAdvRegister->advRegType)->where('employeeId',$index)->get();

                foreach ($totalAdvNos as $totalAdvNo) {
                    if($totalAdvNo->amount>$reciveAmount){
                        $totalAdvNo->status=1;
                        $totalAdvNo->save();
                    }
                    else{
                        $totalAdvNo->status=0;
                        $totalAdvNo->save();
                    }

                    $reciveAmount=$reciveAmount-$totalAdvNo->amount;
                  }
            
              return response::json('success');
        }
    }
}

?>

