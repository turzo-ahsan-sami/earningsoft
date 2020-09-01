<?php
namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\accounting\AccFDRRegisterAccount;
use App\accounting\AccFDRRegisterInterest;
use App\accounting\AccFDRRegisterReceivable;
use App\accounting\AccFdrAccountClose;
use Validator;
use Illuminate\Support\Facades\Input;
use Response;
use App\Http\Controllers\gnr\Service;
use Carbon\Carbon;
use DB;


class AccFdrRegisterController extends Controller
{
	public function index(){

   $frdAccounts = AccFDRRegisterAccount::all();

   return view('accounting.register.fdrRegister.viewFdrRegister',['frdAccounts'=>$frdAccounts]);      
 }

 public function addFdr(){
  $maxAccNumber = AccFDRRegisterAccount::max('accNumber')+1;
  $fdrId = 'FDR'.str_pad($maxAccNumber,6,'0',STR_PAD_LEFT);
  return view('accounting.register.fdrRegister.addFdrRegister',['fdrId'=>$fdrId]);   
}

public function storeFdr(Request $request){


  $rules = array(
    'fdrType' => 'required',
    'accNo' => 'required|unique:acc_fdr_account',
    'accName' => 'required',
    'project' => 'required',
    'projectType' => 'required',
    'branch' => 'required',
    'bank' => 'required',
    'bankBranch' => 'required',
    'principalAmount' => 'required|numeric',
    'interestRate' => 'required|numeric',
    'openingDate' => 'required',
    'duration' => 'required'            

  );
  $attributeNames = array(
    'fdrType' => 'FDR Type',
    'accNo' => 'Account No',
    'accName' => 'Account Name',
    'project' => 'Project',
    'projectType' => 'Project Type',
    'branch' => 'Branch',
    'bank' => 'Bank',
    'bankBranch' => 'Branch Location',
    'principalAmount' => 'Principal Amount',
    'interestRate' => 'Interest Rate',
    'openingDate' => 'Opening Date',
    'duration' => 'Duration'        
  );

  $validator = Validator::make ( Input::all (), $rules);
  $validator->setAttributeNames($attributeNames);
  if ($validator->fails()){
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  }


          ///Store Information
  else{
    $maxAccNumber = AccFDRRegisterAccount::max('accNumber')+1;
    $fdrId = 'FDR'.str_pad($maxAccNumber,6,'0',STR_PAD_LEFT);

    $openingDate = Carbon::parse($request->openingDate);
    $matureDate = Carbon::parse($request->matureDate);

    $request->merge(['fdrId'=>$fdrId,'openingDate' => $openingDate,'matureDate'=>$matureDate]);

    $request->request->add(['fdrTypeId_fk'=>$request->fdrType,'accNumber'=>$maxAccNumber,'projectId_fk'=>$request->project,'projectTypeId_fk'=>$request->projectType,'branchId_fk'=>$request->branch,'bankId_fk'=>$request->bank,'bankBranchId_fk'=>$request->bankBranch,'createdAt'=>Carbon::toDay()]);


    AccFDRRegisterAccount::create($request->all());

  }
  $logArray = array(
    'moduleId'  => 4,
    'controllerName'  => 'AccFdrRegisterController',
    'tableName'  => 'acc_fdr_account',
    'operation'  => 'insert',
    'primaryIds'  => [DB::table('acc_fdr_account')->max('id')]
  );
  Service::createLog($logArray);

  return response::json('success');

}

public function editFdr(Request $request)
{
  $rules = array(
    'fdrTypeId' => 'required',
    'accNo' => 'required|unique:acc_fdr_account,accNo,'.$request->accId,
    'accName' => 'required',
    'bank' => 'required',
    'project' => 'required',
    'projectType' => 'required',
    'branch' => 'required',
    'bankBranch' => 'required',
    'principalAmount' => 'required|numeric',
    'interestRate' => 'required|numeric',
    'openingDate' => 'required',
    'duration' => 'required'            

  );
  $attributeNames = array(
    'fdrTypeId' => 'FDR Type',
    'accNo' => 'Account No',
    'accName' => 'Account Name',
    'project' => 'Project',
    'projectType' => 'Project Type',
    'branch' => 'Branch',
    'bank' => 'Bank',
    'bankBranch' => 'Branch Location',
    'principalAmount' => 'Principal Amount',
    'interestRate' => 'Interest Rate',
    'openingDate' => 'Opening Date',
    'duration' => 'Duration'        
  );

  $validator = Validator::make ( Input::all (), $rules);
  $validator->setAttributeNames($attributeNames);
  if ($validator->fails()){
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  }


          ///Update Information
  else{
    $previousdata = AccFDRRegisterAccount::find($request->accId);

    $openingDate = Carbon::parse($request->openingDate);
    $matureDate = Carbon::parse($request->matureDate);


    $account = AccFDRRegisterAccount::find($request->accId);
    $account->fdrTypeId_fk = $request->fdrTypeId;
    $account->accNo = $request->accNo;
    $account->accName  = $request->accName;

    $account->projectId_fk  = $request->project;
    $account->projectTypeId_fk  = $request->projectType;
    $account->branchId_fk = $request->branch;

    $account->principalAmount = $request->principalAmount;
    $account->interestRate = $request->interestRate;
    $account->duration = $request->duration;
    $account->openingDate = $openingDate;
    $account->matureDate = $matureDate;
    $account->bankId_fk = $request->bank;
    $account->bankBranchId_fk = $request->bankBranch;

    $account->save();

  }
  $logArray = array(
    'moduleId'  => 4,
    'controllerName'  => 'AccFdrRegisterController',
    'tableName'  => 'acc_fdr_account',
    'operation'  => 'update',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);

  return response::json('success');
}

public function deleteFdr(Request $request)
{  
  $previousdata = AccFDRRegisterAccount::find($request->accId);
  $account = AccFDRRegisterAccount::find($request->accId);
  $account->delete();
  $logArray = array(
    'moduleId'  => 4,
    'controllerName'  => 'AccFdrRegisterController',
    'tableName'  => 'acc_fdr_account',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);

  return response::json('success');
}


///////////  Receivable  ///////////

public function viewReceivable(Request $request)
{

  $receivables = AccFDRRegisterReceivable::all();
  return view('accounting.register.fdrRegister.viewFdrReceivable',['receivables'=>$receivables]);
}

public function addReceivable(Request $request)
{
  $maxReceivableNumber = AccFDRRegisterReceivable::max('receivableNumber')+1;
  $receivableId = "FDRR".str_pad($maxReceivableNumber,6,'0',STR_PAD_LEFT);
  return view('accounting.register.fdrRegister.addFdrReceivable',['receivableId'=>$receivableId]);
}

public function storeReceivable(Request $request)
{


 $rules = array(
  'accId' => 'required',
  'receivableAmount' => 'required',            
  'interestRate' => 'required',            
  'dateFrom' => 'required',            
  'receivableDate' => 'required'

);
 $attributeNames = array(
  'accId' => 'Account ID',
  'receivableAmount' => 'Receivable Amount',
  'interestRate' => 'Interest Rate',            
  'dateFrom' => 'Date From',            
  'receivableDate' => 'Receivable Date'     
);

 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails()){
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
}

          //Store Information
else{
  $maxReceivableNumber = AccFDRRegisterReceivable::max('receivableNumber')+1;
  $receivableId = "FDRR".str_pad($maxReceivableNumber,6,'0',STR_PAD_LEFT);

  $receivable = new AccFDRRegisterReceivable;
  $receivable->receivableId = $receivableId;
  $receivable->accId_fk = $request->accId;
  $receivable->amount = $request->receivableAmount;
  $receivable->interestRate = $request->interestRate;

  $receivable->receivableDate = Carbon::parse($request->receivableDate);
  $receivable->dateFrom = Carbon::parse($request->dateFrom);

  $account = AccFDRRegisterAccount::find($request->accId);
  $netInterestAmount = (float) DB::table('acc_fdr_interest')->where('fdrAccId_fk',$request->accId)->sum('netInterestAmount');
  $totalAmount = $account->principalAmount + $netInterestAmount;

  $receivable->receivableNumber = $maxReceivableNumber;
  $receivable->netAmountBeforeReceivable = $totalAmount;
  $receivable->days = $request->days;
  $receivable->createdAt = Carbon::now();
  $receivable->save();
}
$logArray = array(
  'moduleId'  => 4,
  'controllerName'  => 'AccFdrRegisterController',
  'tableName'  => 'acc_fdr_receivable',
  'operation'  => 'insert',
  'primaryIds'  => [DB::table('acc_fdr_receivable')->max('id')]
);
Service::createLog($logArray);


return response::json('success');

}


public function editReceivable(Request $request)
{


 $rules = array(
  'receivableAmount' => 'required' 
);
 $attributeNames = array(
  'receivableAmount' => 'Receivable Amount' 
);

 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails()){
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
}

          //Store Information
else{
 $previousdata = AccFDRRegisterReceivable::find($request->receivableId);

 $receivable = AccFDRRegisterReceivable::find($request->receivableId);

 $receivable->amount = $request->receivableAmount;            
 $receivable->dateFrom = Carbon::parse($request->dateFrom);
 $receivable->receivableDate = Carbon::parse($request->receivableDate);
 $receivable->days = $request->days;

 $receivable->save();
}
$logArray = array(
  'moduleId'  => 4,
  'controllerName'  => 'AccFdrRegisterController',
  'tableName'  => 'acc_fdr_receivable',
  'operation'  => 'update',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
Service::createLog($logArray);


return response::json('success');

}


public function deleteReceivable(Request $request)
{
 $previousdata = AccFDRRegisterReceivable::find($request->receivableId);
 AccFDRRegisterReceivable::where('id',$request->receivableId)->delete();
 $logArray = array(
  'moduleId'  => 4,
  'controllerName'  => 'AccFdrRegisterController',
  'tableName'  => 'acc_fdr_receivable',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);
 return response::json('success');
}




    ///////////  Receivable  ///////////





    //////////////////  Interest   ///////////

public function viewInterest(Request $request)
{

  $interests = AccFDRRegisterInterest::all();

  return view('accounting.register.fdrRegister.viewFdrInterest',['interests'=>$interests]);
}

public function addInterest(Request $request)
{
  $maxInterestNumber = AccFDRRegisterInterest::max('interestNumber')+1;
  $interestId = "FDRI".str_pad($maxInterestNumber,6,'0',STR_PAD_LEFT);
  return view('accounting.register.fdrRegister.addFdrInterest',['interestId'=>$interestId]);
}

public function storeInterest(Request $request)
{


 $rules = array(
  'accId' => 'required',
  'interestAmount' => 'required',
  'bankCharge' => 'required',
  'tax' => 'required',
  'receiveDate' => 'required'

);
 $attributeNames = array(
  'accId' => 'Account ID',
  'interestAmount' => 'Interest Amount',
  'bankCharge' => 'Bank Charge',
  'tax' => 'Tax Amount',
  'receiveDate' => 'Date'     
);

 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails()){
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
}

          //Store Information
else{
  $maxInterestNumber = AccFDRRegisterInterest::max('interestNumber')+1;
  $interestId = "FDRI".str_pad($maxInterestNumber,6,'0',STR_PAD_LEFT);

  $interest = new AccFDRRegisterInterest;
  $interest->interestId = $interestId;
  $interest->fdrAccId_fk = $request->accId;
  $interest->interestAmount = $request->interestAmount;
  $interest->bankCharge = $request->bankCharge;
  $interest->taxAmount = $request->tax;
  $interest->receivableAmount = $request->receivableAmount;
  $interest->netInterestAmount = $request->netInterestAmount;
  $interest->receivableIds_fk = $request->receivableIds;
  $interest->receiveDate = Carbon::parse($request->receiveDate);
  $interest->interestNumber = $maxInterestNumber;
  $interest->createdAt = Carbon::toDay();
  $interest->save();



            ////set status of receivable to zero
  $receivables = str_replace(['{', '}'] ,'',  $request->receivableIds);
  $receivableIds = explode(",", $receivables);

  DB::table('acc_fdr_receivable')->whereIn('id',$receivableIds)->update(['status'=>0]);

}
$logArray = array(
  'moduleId'  => 4,
  'controllerName'  => 'AccFdrRegisterController',
  'tableName'  => 'acc_fdr_interest',
  'operation'  => 'insert',
  'primaryIds'  => [DB::table('acc_fdr_interest')->max('id')]
);
Service::createLog($logArray);


return response::json('success');

}


public function editInterest(Request $request)
{
  $previousdata = AccFDRRegisterInterest::find($request->interestId);
  $interest = AccFDRRegisterInterest::find($request->interestId);

  $interest->interestAmount = $request->interestAmount;
  $interest->bankCharge = $request->bankChargeAmount;
  $interest->taxAmount = $request->taxAmount;
  $interest->netInterestAmount = $request->netInterestAmount;
  $interest->receiveDate = Carbon::parse($request->receivedDate);
  $interest->save();
  $logArray = array(
    'moduleId'  => 4,
    'controllerName'  => 'AccFdrRegisterController',
    'tableName'  => 'acc_fdr_interest',
    'operation'  => 'update',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);

  return response::json('success');
}

public function deleteInterest(Request $request)
{
 $previousdata = AccFDRRegisterInterest::find($request->interestId);
 $interest = AccFDRRegisterInterest::where('id',$request->interestId)->first();


      ////set status of receivable to 1
 $receivables = str_replace(['{', '}'] ,'',  $interest->receivableIds_fk);
 $receivableIds = explode(",", $receivables);

 DB::table('acc_fdr_receivable')->whereIn('id',$receivableIds)->update(['status'=>1]);


 $interest->delete();
 $logArray = array(
  'moduleId'  => 4,
  'controllerName'  => 'AccFdrRegisterController',
  'tableName'  => 'acc_fdr_interest',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);
 return response::json('success');
}


    //////////  End Interest   /////////




    //////////   Account Closing  /////////


public function viewAccountClose()
{
  $accClose = AccFdrAccountClose::all();
  return view('accounting.register.fdrRegister.viewFdrClosingAccount',['accountClose'=>$accClose]);
}

public function addAccountClose()
{

  return view('accounting.register.fdrRegister.addFdrClosingAccount');
}

public function storeAccountClose(Request $request)
{

  $rules = array(
    'closingDate' => 'required'


  );
  $attributeNames = array(
    'closingDate' => 'Closing Date'      
  );

  $validator = Validator::make ( Input::all (), $rules);
  $validator->setAttributeNames($attributeNames);
  if ($validator->fails()){
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  }
  else{

    $account = AccFDRRegisterAccount::find($request->accId);

    $accClose = new AccFdrAccountClose;

    $accClose->accId_fk = $account->id;
    $accClose->closingDate = Carbon::parse($request->closingDate);
    $accClose->createdAt = Carbon::toDay();
    $accClose->save();

    $account->status = 0;
    $account->save();

  }
  $logArray = array(
    'moduleId'  => 4,
    'controllerName'  => 'AccFdrRegisterController',
    'tableName'  => 'acc_fdr_close',
    'operation'  => 'insert',
    'primaryIds'  => [DB::table('acc_fdr_close')->max('id')]
  );
  Service::createLog($logArray);

  return response::json('success');

}

public function editAccountClose(Request $request)
{
  $previousdata = AccFdrAccountClose::find($request->accCloseId);
  $accClose = AccFdrAccountClose::find($request->accCloseId);
  $accClose->closingDate = Carbon::parse($request->closingDate);
  $accClose->save();
  $logArray = array(
    'moduleId'  => 4,
    'controllerName'  => 'AccFdrRegisterController',
    'tableName'  => 'acc_fdr_close',
    'operation'  => 'update',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return response::json('success');
}

public function deleteAccountClose(Request $request)
{  
  $previousdata = AccFdrAccountClose::find($request->accCloseId);
  $accClose = AccFdrAccountClose::find($request->accCloseId);
  $account = AccFDRRegisterAccount::find($accClose->accId_fk);
  $account->status = 1;
  $account->save();
  $accClose->delete();
  $logArray = array(
    'moduleId'  => 4,
    'controllerName'  => 'AccFdrRegisterController',
    'tableName'  => 'acc_fdr_close',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return response::json('success');
}



    //////////  End Account Closing  /////////




    /////////////    Ajax Functions   /////////////


public function getFilteredAccount(Request $request)
{
  $accounts = DB::table('acc_fdr_account');

  if ($request->projectId!='') {
    $accounts = $accounts->where('projectId_fk',$request->projectId);
  }
  if ($request->projectTypeId!='') {
    $accounts = $accounts->where('projectTypeId_fk',$request->projectTypeId);
  }
  if ($request->branchId!='') {
    $accounts = $accounts->where('branchId_fk',$request->branchId);
  }

  if ($request->fdrType!='') {
    $accounts = $accounts->where('fdrTypeId_fk',$request->fdrType);
  }
  if ($request->bank!='') {
    $accounts = $accounts->where('bankId_fk',$request->bank);
  }
  if ($request->bankBranch!='') {
    $accounts = $accounts->where('bankBranchId_fk',$request->bankBranch);
  }


  $accounts = $accounts->where('status',1)->select('accNo','id')->get();

  return response::json($accounts);

}

public function getInterestInfo(Request $request)
{
  $interest = AccFDRRegisterInterest::find($request->interestId);
  $account = AccFDRRegisterAccount::where('id',$interest->fdrAccId_fk)->first();
  $frdTypeName = DB::table('acc_fdr_type')->where('id',$account->fdrTypeId_fk)->value('name');
  $receiveDate = $interest->receiveDate;

  $receivables = str_replace(['{','}'],'',$interest->receivableIds_fk);
  $receivableIds = explode(',',$receivables);

  $receivableAmount = DB::table('acc_fdr_receivable')->whereIn('id',$receivableIds)->sum('amount');

  $data = array(
    'interest' => $interest,
    'account' => $account,
    'receiveDate' => $receiveDate,
    'frdTypeName' => $frdTypeName,
    'receivableAmount' => $receivableAmount
  );

  return response::json($data);
}

public function getReceivableInfo(Request $request)
{
  $receivable = AccFDRRegisterReceivable::find($request->receivableId);
  $account = AccFDRRegisterAccount::where('id',$receivable->accId_fk)->first();

  $frdTypeName = DB::table('acc_fdr_type')->where('id',$account->fdrTypeId_fk)->value('name');
  $bankName = DB::table('gnr_bank')->where('id',$account->bankId_fk)->value('name');
  $bankBranchName = DB::table('gnr_bank_branch')->where('id',$account->bankBranchId_fk)->value('name');



  $data = array(
    'receivable' => $receivable,
    'account' => $account,
    'receivableDate' => $receivable->receivableDate,
    'dateFrom' => $receivable->dateFrom,
    'frdTypeName' => $frdTypeName,
    'bankName' => $bankName,
    'bankBranchName' => $bankBranchName
  );

  return response::json($data);

}



public function getLocationBaseOnBank(Request $request)
{
 $branches = DB::table('gnr_bank_branch as t1')
 ->join('gnr_bank as t2','t1.bankId_fk','=','t2.id');
 if ($request->bank!='') {
  $branches = $branches->where('t1.bankId_fk',$request->bank);
}
$branches = $branches->select('t1.id','t1.name','t2.shortName as bankName')->get();

return response::json($branches);


}


public function getAccountInfo(Request $request)
{
  $account = AccFDRRegisterAccount::find($request->accId);
  $fdrTypeName = DB::table('acc_fdr_type')->where('id',$account->fdrTypeId_fk)->value('name');
  $openingDate = $account->openingDate;
  $matureDate = $account->matureDate;
  if ($account->duration>1) {
    $duration = str_pad($account->duration,2,'0',STR_PAD_LEFT)." months";
  }
  else{
    $duration = str_pad($account->duration,2,'0',STR_PAD_LEFT)." month";
  }

  $projectName = DB::table('gnr_project')->where('id',$account->projectId_fk)->value('name');
  $projectTypeName = DB::table('gnr_project_type')->where('id',$account->projectTypeId_fk)->value('name');
  $branchName = DB::table('gnr_branch')->where('id',$account->branchId_fk)->value('name');

  $bankName = DB::table('gnr_bank')->where('id',$account->bankId_fk)->value('name');
  $bankBranchName = DB::table('gnr_bank_branch')->where('id',$account->bankBranchId_fk)->value('name');

  $netInterestAmount = (float) DB::table('acc_fdr_interest')->where('fdrAccId_fk',$request->accId)->sum('netInterestAmount');

  $totalAmount = $account->principalAmount + $netInterestAmount;

  $lastInterestReceivedDate = DB::table('acc_fdr_interest')->where('fdrAccId_fk',$request->accId)->orderBy('receiveDate','desc')->value('receiveDate');

  $accCloseId =  DB::table('acc_fdr_close')->where('accId_fk',$request->accId)->value('id');
  $closingDate =  DB::table('acc_fdr_close')->where('accId_fk',$request->accId)->value('closingDate');

  $receiveableAmount = (float) DB::table('acc_fdr_receivable')->where('accId_fk',$request->accId)->where('status',1)->sum('amount');
  $receivableIds = DB::table('acc_fdr_receivable')->where('accId_fk',$request->accId)->where('status',1)->pluck('id')->toArray();


  $data = array(
    'account' => $account,
    'fdrTypeName' => $fdrTypeName,
    'openingDate' => $openingDate,
    'matureDate' => $matureDate,
    'duration' => $duration,
    'projectName' => $projectName,
    'projectTypeName' => $projectTypeName,
    'branchName' => $branchName,
    'bankName' => $bankName,
    'bankBranchName' => $bankBranchName,
    'netInterestAmount' => $netInterestAmount,
    'totalAmount' => $totalAmount,
    'lastInterestReceivedDate' => $lastInterestReceivedDate,
    'accCloseId' => $accCloseId,
    'closingDate' => $closingDate,
    'receiveableAmount' => $receiveableAmount,
    'receivableIds' => $receivableIds
  );


  return response::json($data);

}




}



?>