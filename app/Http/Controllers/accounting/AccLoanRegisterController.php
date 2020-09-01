<?php
namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\accounting\AccLoanRegisterAccount;
use Validator;
use Illuminate\Support\Facades\Input;
use Response;
use App\Http\Controllers\gnr\Service;
use Carbon\Carbon;
use DB;


class AccLoanRegisterController extends Controller
{
	public function index()
	{
   $loanAccounts = AccLoanRegisterAccount::all();
   return view('accounting.register.loanRegister.viewLoanRegister',['loanAccounts'=>$loanAccounts]);   
 }

 public function addLoanRegister()
 {
  return view('accounting.register.loanRegister.addLoanRegister');
}


public function validateFirstStep(Request $request)
{

 $rules = array(
  'donor' => 'required',
  'loanProduct' => 'required',
  'branch' => 'required',
  'project' => 'required',
  'projectType' => 'required',
  'agreementDate' => 'required',       
  'loanDate' => 'required',

  'loanAmount' => 'required',
  'interestRate' => 'required',
  'repaymentFrequency' => 'required',
  'loanDuration' => 'required|integer|min:1',
  'gracePeriod' => 'required',
  'numOfInstallment' => 'required',
  'status' => 'required'            
);


 if ($request->donorType==0) {
  $rules = $rules + array('accNo' => 'required');
  $rules = $rules + array('loanSanctionNumber' => 'required');
}
else{
  $rules = $rules + array('phase' => 'required');
  $rules = $rules + array('cycle' => 'required');
}



$attributeNames = array(
  'donor' => 'Donor',
  'loanProduct' => 'Loan Product',
  'branch' => 'Branch',
  'project' => 'Project',
  'projectType' => 'Project Type',
  'accNo' => 'Account Number',
  'agreementDate' => 'Agreement Date',
  'loanSanctionNumber' => 'Loan Sanction Number',            
  'loanDate' => 'Loan Date',

  'loanAmount' => 'Loan Amount',
  'interestRate' => 'Interest Rate',
  'cycle' => 'Cycle',
  'phase' => 'Phase',
  'repaymentFrequency' => 'Repayment Frequency',
  'loanDuration' => 'Loan Duration',
  'gracePeriod' => 'Grace Period',
  'numOfInstallment' => 'Number Of Installment',
  'status' => 'Status'            
);

$validator = Validator::make ( Input::all (), $rules);
$validator->setAttributeNames($attributeNames);
if ($validator->fails()){
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
}



else{

  /*Check Duplicate Phase and Cycle of the same loan product*/
  if ($request->donorType==1) {
    if ($request->accId!=null) {
      $dataExits = (int) DB::table('acc_loan_register_account')->where('id','!=',$request->accId)->where('loanProductId_fk',$request->loanProduct)->where('phase',$request->phase)->where('cycle',$request->cycle)->value('id');
    }
    else{
      $dataExits = (int) DB::table('acc_loan_register_account')->where('loanProductId_fk',$request->loanProduct)->where('phase',$request->phase)->where('cycle',$request->cycle)->value('id');
    }

    if($dataExits>0){
      return response::json(array('errors' => ['cycle'=>'This Cycle already exists.']));
    }
  }

  /*End Check Duplicate Phase and Cycle of the same loan product*/

  /*Check is given data inappropriate*/
  $loanDate = Carbon::parse($request->loanDate);
  $endDate = $loanDate->copy()->addMonthsNoOverflow($request->loanDuration);

  $firstInstallment = $loanDate->copy()->addMonthsNoOverflow($request->gracePeriod);

  for ($i=0; $i < $request->numOfInstallment-2; $i++) { 
    $firstInstallment->addMonthsNoOverflow($request->repaymentFrequency);
  }

  if($firstInstallment->gte($endDate)){
    return response::json(array('errors' => ['inappropriate'=>'Loan Information is not matched.']));
  }
  /*End Check is given data inappropriate*/


  return response::json('sucess');
}

}

public function storeLoanRegister(Request $request)
{
  DB::table('acc_loan_register_account')
  ->insert([
    'projectId_fk' => $request->project,
    'projectTypeId_fk' => $request->projectType,
    'bankId_fk' => $request->donor,
    'bankBranchId_fk' => $request->branch,
    'loanProductId_fk' => $request->loanProduct,
    'accNo' => $request->accNo,
    'loanDate' => Carbon::parse($request->loanDate),
    'agreementDate' => Carbon::parse($request->agreementDate),
    'loanSanctionNumber' => $request->loanSanctionNumber,
    'interestRate' => $request->interestRate,
    'loanAmount' => $request->loanAmount,
    'cycle' => $request->cycle,
    'phase' => $request->phase,
    'repaymentFrequency' => $request->repaymentFrequency,
    'loanDuration' => $request->loanDuration,
    'grasePeriod' => $request->gracePeriod,
    'numOfInstallment' => $request->numOfInstallment,
    'status' => $request->status,
    'createdAt' => Carbon::now()
  ]);


  $arraySize = sizeof($request->tPrincipalAmount);

  for($i=0;$i<$arraySize;$i++){

    $principalAmount = round($request->tPrincipalAmount[$i],2);
    $interestAmount = round($request->tInterestAmount[$i],2);
    $totalAmount = round($principalAmount + $interestAmount,2);
    DB::table('acc_loan_register_payment_schedule')
    ->insert([
      'accId_fk' => DB::table('acc_loan_register_account')->max('id'),
    						'scheduleNumber' => $i+1,//$request->tInstallmentNumber[$i],
    						'paymentDate' => Carbon::parse($request->tPaymentDate[$i]),
    						'principalAmount' => $principalAmount,
    						'interestAmount' => $interestAmount,
    						'totalAmount' => $totalAmount,
                'isPaid' => 0,
                'paymentStatus' => 'Unpaid',
                'createdAt' => Carbon::now()
              ]);
  }
  $logArray = array(
    'moduleId'  => 4,
    'controllerName'  => 'AccLoanRegisterController',
    'tableName'  => 'acc_loan_register_account',
    'operation'  => 'insert',
    'primaryIds'  => [DB::table('acc_loan_register_account')->max('id')]
  );
  Service::createLog($logArray);

  return response::json($arraySize);
}


public function editLoanRegister(Request $request)
{
  $previousdata = AccLoanRegisterAccount::find ($request->accId);
  DB::table('acc_loan_register_account')
  ->where('id',$request->accId)
  ->update([
    'projectId_fk' => $request->project,
    'projectTypeId_fk' => $request->projectType,
    'bankId_fk' => $request->donor,
    'bankBranchId_fk' => $request->branch,
    'loanProductId_fk' => $request->loanProduct,
    'accNo' => $request->accNo,
    'loanDate' => Carbon::parse($request->loanDate),
    'agreementDate' => Carbon::parse($request->agreementDate),
    'loanSanctionNumber' => $request->loanSanctionNumber,
    'interestRate' => $request->interestRate,
    'loanAmount' => $request->loanAmount,
    'cycle' => $request->cycle,
    'phase' => $request->phase,
    'repaymentFrequency' => $request->repaymentFrequency,
    'loanDuration' => $request->loanDuration,
    'grasePeriod' => $request->gracePeriod,
    'numOfInstallment' => $request->numOfInstallment,
    'status' => $request->status
  ]);


  DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$request->accId)->delete();

  $arraySize = sizeof($request->tPrincipalAmount);

  for($i=0;$i<$arraySize;$i++){

    $principalAmount = round($request->tPrincipalAmount[$i],2);
    $interestAmount = round($request->tInterestAmount[$i],2);
    $totalAmount = round($principalAmount + $interestAmount,2);
    DB::table('acc_loan_register_payment_schedule')
    ->insert([
      'accId_fk' => $request->accId,
                'scheduleNumber' => $i+1,//$request->tInstallmentNumber[$i],
                'paymentDate' => Carbon::parse($request->tPaymentDate[$i]),
                'principalAmount' => $principalAmount,
                'interestAmount' => $interestAmount,
                'totalAmount' => $totalAmount,
                'isPaid' => 0,
                'paymentStatus' => 'Unpaid',
                'createdAt' => Carbon::now()
              ]);
  }

  $logArray = array(
    'moduleId'  => 4,
    'controllerName'  => 'AccLoanRegisterController',
    'tableName'  => 'acc_loan_register_account',
    'operation'  => 'update',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);

  return response::json('success');

}



public function deleteLoanRegister(Request $request)
{
  $previousdata = AccLoanRegisterAccount::find ($request->accId);
  $account = AccLoanRegisterAccount::find($request->accId);
  DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$account->id)->delete();
  $account->delete();
  $logArray = array(
    'moduleId'  => 4,
    'controllerName'  => 'AccLoanRegisterController',
    'tableName'  => 'acc_loan_register_account',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return response::json('success');
}



  ////// Payment  /////////
public function viewPayment()
{
  $payments = DB::table('acc_loan_register_payments')->get();
  return view('accounting.register.loanRegister.viewPayment',['payments'=>$payments]);
}

public function addPayment()
{
  $maxPaymentNo = DB::table('acc_loan_register_payments')->max('paymentNo')+1;
  $paymentId = "LRP".str_pad($maxPaymentNo,6,'0',STR_PAD_LEFT);

  return view('accounting.register.loanRegister.addPayment',['paymentId'=>$paymentId]);
}

public function storePayment(Request $request)
{



  $rules = array(
    'loanProductId' => 'required',
    'principalAmount' => 'required',
    'interestAmount' => 'required',
    'paymentDate' => 'required',

  );

  if($request->isDonor==1){
    $rules = $rules + array('phase' => 'required');
    $rules = $rules + array('cycle' => 'required');
  }
  elseif ($request->isDonor!=null) {
    $rules = $rules + array('loanAccId' => 'required');
  }

  if ($request->isRebate==1) {
    $rules = $rules + array('rebateAmount' => 'required');
  }



  $attributeNames = array(
    'loanProductId' => 'Loan Product',
    'principalAmount' => 'Principal Amount',
    'interestAmount' => 'Interest Amount',
    'paymentDate' => 'Payment Date',

    'loanAccId' => 'Account No',
    'phase' => 'Phase',
    'cycle' => 'Cycle',
    'rebateAmount' => 'Rebate Amount',

  );

  $validator = Validator::make ( Input::all (), $rules);
  $validator->setAttributeNames($attributeNames);
  if ($validator->fails()){
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));            
  }


  /*Store Data*/
  else{



   $maxPaymentNo = DB::table('acc_loan_register_payments')->max('paymentNo')+1;
   $paymentId = "LRP".str_pad($maxPaymentNo,6,'0',STR_PAD_LEFT);


   if ($request->isDonor==0) {
    $account = DB::table('acc_loan_register_account')->where('id',$request->loanAccId)->first();
  }
  else{
    $account = DB::table('acc_loan_register_account')->where('loanProductId_fk',$request->loanProductId)->where('phase',$request->phase)->where('cycle',$request->cycle)->first();
  }

  DB::table('acc_loan_register_payments')->insert([
    'paymentId' => $paymentId,
    'accId_fk' => $account->id,
    'installmentNo' => $request->installmentNo,
    'principalAmount' => round($request->principalAmount,2),
    'interestAmount' => round($request->interestAmount,2),
    'totalAmount' => round($request->principalAmount + $request->interestAmount,2),
    'duePrincipalAmount' => round($request->duePrincipalAmount,2),
    'dueInterestAmount' => round($request->dueInterestAmount,2),
    'dueTotalAmount' => round($request->duePrincipalAmount + $request->dueInterestAmount,2),
    'isRebate' => $request->isRebate,
    'rebateAmount' => round($request->rebateAmount,2),
    'paymentNo' => $maxPaymentNo,
    'paymentDate' => Carbon::parse($request->paymentDate),
    'createdAt' => Carbon::now()
  ]);

  $installmentArray = explode(",",$request->installmentNo);

  if ($request->isRebate==1) {
    DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$account->id)->whereIn('scheduleNumber', $installmentArray)->update(['paymentId'=>$paymentId,'isPaid'=>'1','paymentStatus'=>'Rebate']);
    DB::table('acc_loan_register_account')->where('id',$account->id)->update(['status'=>0]);
  }

  elseif ($request->duePrincipalAmount + $request->dueInterestAmount <=0) {
    DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$account->id)->where('scheduleNumber',$request->installmentNo)->update(['paymentId'=>$paymentId,'isPaid'=>'1','paymentStatus'=>'Paid']);
    $lastScheduleNumber = DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$account->id)->max('scheduleNumber');
    if ($lastScheduleNumber==$request->installmentNo) {
      DB::table('acc_loan_register_account')->where('id',$account->id)->update(['status'=>0]);
    }
  }
  else{
   DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$account->id)->where('scheduleNumber',$request->installmentNo)->update(['paymentId'=>$paymentId,'paymentStatus'=>'Partial']);
 }

}

$logArray = array(
  'moduleId'  => 4,
  'controllerName'  => 'AccLoanRegisterController',
  'tableName'  => 'acc_loan_register_payments',
  'operation'  => 'insert',
  'primaryIds'  => [DB::table('acc_loan_register_payments')->max('id')]
);
Service::createLog($logArray);


return response::json('success');


}


public function editPayment(Request $request)
{
  if ($request->isRebate==1) {
    $rules = array(
      'rebateAmount' => 'required'                                   
    );
  }
  else{
   $rules = array(
    'principalAmount' => 'required',
    'interestAmount' => 'required'                                   
  );

 }



 $attributeNames = array(
  'rebateAmount' => 'Rebate Amount',
  'principalAmount' => 'Principal Amount',
  'interestAmount' => 'Interest Amount'
);

 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails()){
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));            
}

else{
 $previousdata = DB::table('acc_loan_register_payments')->where('id',$request->paymentId)->first();

 $payment = DB::table('acc_loan_register_payments')->where('id',$request->paymentId)->first();
 if ($request->isRebate==1) {
  DB::table('acc_loan_register_payments')->where('id',$request->paymentId)->update(['rebateAmount'=>$request->rebateAmount]);
}
else{
  //$previousdata = DB::table('acc_loan_register_payments')->where('id',$request->paymentId)->first();
  $principalAmount = round($request->principalAmount,2);
  $interestAmount = round($request->interestAmount,2);
  $totalAmount = round($principalAmount + $interestAmount,2);

  $duePrincipalAmount = round($request->duePrincipalAmount,2);
  $dueInterestAmount = round($request->dueInterestAmount,2);
  $totalDueAmount = round($duePrincipalAmount + $dueInterestAmount,2);

  DB::table('acc_loan_register_payments')->where('id',$request->paymentId)->update(['principalAmount'=>$principalAmount,'interestAmount'=>$interestAmount,'totalAmount'=>$totalAmount,'duePrincipalAmount'=>$duePrincipalAmount,'dueInterestAmount'=>$dueInterestAmount,'dueTotalAmount'=>$totalDueAmount]);

  /*If Due*/
  if ($totalDueAmount>0) {
    DB::table('acc_loan_register_payment_schedule')->where('paymentId',$payment->paymentId)->update(['isPaid'=>'0','paymentStatus'=>'Partial']);
    DB::table('acc_loan_register_account')->where('id',$payment->accId_fk)->update(['status'=>'1']);

  }
  /*End If Due*/

  /*If No Due*/
  else{

    DB::table('acc_loan_register_payment_schedule')->where('paymentId',$payment->paymentId)->update(['isPaid'=>'1','paymentStatus'=>'Paid']);

    $maxInstallmentNo = DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$payment->accId_fk)->max('scheduleNumber');
    if($payment->installmentNo==$maxInstallmentNo){
      DB::table('acc_loan_register_account')->where('id',$payment->accId_fk)->update(['status'=>'0']);
    }
  }
  /*End If No Due*/
}

$paymentDate = Carbon::parse($request->paymentDate);

DB::table('acc_loan_register_payments')->where('id',$request->paymentId)->update(['paymentDate'=>$paymentDate]);

}

$logArray = array(
  'moduleId'  => 4,
  'controllerName'  => 'AccLoanRegisterController',
  'tableName'  => 'acc_loan_register_payments',
  'operation'  => 'update',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
Service::createLog($logArray);



return response::json('success');
}


public function deletePayment(Request $request)

{
 $previousdata = DB::table('acc_loan_register_payments')->where('id',$request->paymentId)->first();
 $payment = DB::table('acc_loan_register_payments')->where('id',$request->paymentId)->first();

 $schedules = array_map('intval',explode(",",$payment->installmentNo));

 foreach ($schedules as $key => $schedule) {
  $isAnyOtherPayment = (int) DB::table('acc_loan_register_payments')->where('id','!=',$payment->id)->where('accId_fk',$payment->accId_fk)->where('installmentNo',$schedule)->value('id');
  if ($isAnyOtherPayment>0) {
    DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$payment->accId_fk)->where('scheduleNumber',$schedule)->update(['paymentId'=>'','isPaid'=>'0','paymentStatus'=>'Partial']);
  }
  else{
    DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$payment->accId_fk)->where('scheduleNumber',$schedule)->update(['paymentId'=>'','isPaid'=>'0','paymentStatus'=>'Unpaid']);
  }

}

DB::table('acc_loan_register_account')->where('id',$payment->accId_fk)->update(['status'=>1]);

DB::table('acc_loan_register_payments')->where('id',$request->paymentId)->delete();
$logArray = array(
  'moduleId'  => 4,
  'controllerName'  => 'AccLoanRegisterController',
  'tableName'  => 'acc_loan_register_payments',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
Service::createLog($logArray);

return response::json('success');
}

public function onChangeProjectType(Request $request)
{

  if ($request->projectTypeId!=null) {
    $activeDonors = DB::table('acc_loan_register_account')->where('projectTypeId_fk',$request->projectTypeId)->pluck('bankId_fk')->toArray();
    $activeBranches = DB::table('acc_loan_register_account')->where('projectTypeId_fk',$request->projectTypeId)->pluck('bankBranchId_fk')->toArray();
    $activeLoanProducts = DB::table('acc_loan_register_account')->where('projectTypeId_fk',$request->projectTypeId)->pluck('loanProductId_fk')->toArray();
  }

  else{
    $activeDonors = DB::table('acc_loan_register_account')->pluck('bankId_fk')->toArray();
    $activeBranches = DB::table('acc_loan_register_account')->pluck('bankBranchId_fk')->toArray();
    $activeLoanProducts = DB::table('acc_loan_register_account')->pluck('loanProductId_fk')->toArray();    
  }



  $donors = DB::table('gnr_bank')->whereIn('id',$activeDonors)->select('id','name')->get();
  $branches = DB::table('gnr_bank_branch as t1')
  ->join('gnr_bank as t2','t1.bankId_fk','t2.id')
  ->whereIn('t1.id',$activeBranches)->select('t1.id','t1.name','t2.shortName')
  ->get();
  $loanProducts = DB::table('gnr_loan_product')->whereIn('id',$activeLoanProducts)->select('id','name')->get();

  $data = array(
    'donors' => $donors,
    'branches' => $branches,
    'loanProducts' => $loanProducts
  );

  return response::json($data);

}


  ////// End Payment  /////////




  ////////// Ajax Functions   ////////


public function getLoanProductBaseOnDonor(Request $request)
{
 $loanProducts = DB::table('gnr_loan_product')->where('donorId_fk',$request->donor)->get();

 return response::json($loanProducts);
}

public function getLoanProductsBaseOnBranch(Request $request)
{

  $activeLoanProducts = DB::table('acc_loan_register_account')->where('bankBranchId_fk',$request->branchId)->pluck('loanProductId_fk')->toArray();
  $loanProducts = DB::table('gnr_loan_product')->whereIn('id',$activeLoanProducts)->get();

  return response::json($loanProducts);
}


public function getLoanRegisterInfo(Request $request)
{
 $account = AccLoanRegisterAccount::find($request->accId);
 $donorType = (int) DB::table('gnr_bank')->where('id',$account->bankId_fk)->value('isDonor');

 $donorName = DB::table('gnr_bank')->where('id',$account->bankId_fk)->value('name');
 $productName = DB::table('gnr_loan_product')->where('id',$account->loanProductId_fk)->value('name');
 $branchName = DB::table('gnr_bank_branch')->where('id',$account->bankBranchId_fk)->value('name');
 $projectName = DB::table('gnr_project')->where('id',$account->projectId_fk)->value('name');
 $projectTypeName = DB::table('gnr_project_type')->where('id',$account->projectTypeId_fk)->value('name');

 $data = array(
  'account' => $account,
  'donorType' => $donorType,
  'donorName' => $donorName,
  'productName' => $productName,
  'branchName' => $branchName,
  'projectName' => $projectName,
  'projectTypeName' => $projectTypeName,
);

 return response::json($data);
}


public function getInstallmentInfo(Request $request)
{
 $installments = DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$request->accId)->get();


 foreach ($installments as $key => $installment) {

 }
 return response::json($installments);

}


public function onChangeLoanProduct(Request $request)
{
  $donorId = DB::table('gnr_loan_product')->where('id',$request->loanProductId)->value('donorId_fk');
  $isDonor = DB::table('gnr_bank')->where('id',$donorId)->value('isDonor');

  if ($isDonor==0) {
    $accounts = DB::table('acc_loan_register_account')->where('loanProductId_fk',$request->loanProductId)->where('status',1)->select('id','accNo')->get();
    $data = array(
      'isDonor' => $isDonor,
      'accounts' => $accounts
    );

    return response::json($data);
  }


  else{
    $phases = DB::table('acc_loan_register_account')->where('status',1)->where('loanProductId_fk',$request->loanProductId)->groupBy('phase')->select('id','phase')->get();
    $data = array(
      'isDonor' => $isDonor,
      'phases' => $phases
    );

    return response::json($data);
  }


}


public function onChangePhase(Request $request)
{
  $cycles = DB::table('acc_loan_register_account')->where('status',1)->where('loanProductId_fk',$request->loanProductId)->where('phase',$request->phase)->select('cycle')->get();

  return response::json($cycles);
}


public function getLoanAccNpaymentInfo(Request $request)
{


  if ($request->key=="accNo") {
    $account = DB::table('acc_loan_register_account')->where('id',$request->loanAccId)->first();
  }
  elseif($request->key=="cycle"){
    $account = DB::table('acc_loan_register_account')->where('loanProductId_fk',$request->loanProductId)->where('phase',$request->phase)->where('cycle',$request->cycle)->first();
  }
  $lastUnpaidInstallmentId = DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$account->id)->where('isPaid',0)->value('scheduleNumber');

  $partialPricipalAmount = (float) DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->where('installmentNo',$lastUnpaidInstallmentId)->sum('principalAmount');
  $partialInterestAmount = (float) DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->where('installmentNo',$lastUnpaidInstallmentId)->sum('interestAmount');

  $principalAmount = (float) DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$account->id)->where('isPaid',0)->value('principalAmount') - $partialPricipalAmount;
  $principalAmount = round($principalAmount,2);

  $interestAmount = (float) DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$account->id)->where('isPaid',0)->value('interestAmount') - $partialInterestAmount;
  $interestAmount = round($interestAmount,2);

  $schedules = DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$account->id)->get();

  $principalPaymentAmount = array();
  $interestPaymentAmount = array();
  $totalPaidAmount = array();

  $principalDueAmount = array();
  $interestDueAmount = array();
  $totalDueAmount = array();
  $paymentDateArray = array();

  foreach ($schedules as $key => $schedule) {
    if ($schedule->isPaid==1) {
      $principalPayAmount = round($schedule->principalAmount,2);
      $interestPayAmount = round($schedule->interestAmount,2);
      $principalDue = 0;
      $interestDue = 0;
      $rebateInstallmentArray = explode(",",DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->where('isRebate',1)->value('installmentNo'));
      if (in_array($schedule->scheduleNumber, $rebateInstallmentArray)) {
        $paymentDate = DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->where('isRebate',1)->value('paymentDate');
      }
      else{
        $paymentDate = DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->where('installmentNo',$schedule->scheduleNumber)->value('paymentDate');
      }

    }
    else{
     $principalPayAmount = round(DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->where('installmentNo',$schedule->scheduleNumber)->sum('principalAmount'),2);
     $interestPayAmount = round(DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->where('installmentNo',$schedule->scheduleNumber)->sum('interestAmount'),2);

     if ($schedule->paymentStatus=='Partial') {
      $principalDue = round(DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->where('installmentNo',$schedule->scheduleNumber)->orderBy('id','desc')->value('duePrincipalAmount'),2);
      $interestDue = round(DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->where('installmentNo',$schedule->scheduleNumber)->orderBy('id','desc')->value('dueInterestAmount'),2);
    }
    else{
      $principalDue = round($schedule->principalAmount,2);
      $interestDue = round($schedule->interestAmount,2);
    }

    $toDay = Carbon::toDay();
    $scheduleDate = Carbon::parse($schedule->paymentDate);

    if ($toDay->lt($scheduleDate)) {
      $principalDue = 0;
      $interestDue = 0;
    }


    /*If Partially Paid then get the payment Date*/
    $isPartillyPaid = (int) DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->where('installmentNo',$schedule->scheduleNumber)->value('id');

    if ($isPartillyPaid>0) {
      $paymentDate = DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->where('installmentNo',$schedule->scheduleNumber)->value('paymentDate');
    }
    else{
      $paymentDate = null;
    }



  }

  array_push($principalPaymentAmount,$principalPayAmount);
  array_push($interestPaymentAmount,$interestPayAmount);
  array_push($totalPaidAmount,round(($principalPayAmount+$interestPayAmount),2));

  array_push($principalDueAmount,$principalDue);
  array_push($interestDueAmount,$interestDue);
  array_push($totalDueAmount,round(($principalDue+$interestDue),2));
  array_push($paymentDateArray,$paymentDate);
}


$lastPaymentDate = DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->max('paymentDate');
if ($lastPaymentDate==null) {
  $lastPaymentDate = "2000-01-01";
}

$isRebate = (int) DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->where('isRebate',1)->value('id');

$rebateAmount = round(DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->where('isRebate',1)->value('rebateAmount'),2);

$data = array(
  'account' => $account,
  'schedules' => $schedules,
  'principalPaymentAmount' => $principalPaymentAmount,
  'interestPaymentAmount' => $interestPaymentAmount,
  'totalPaidAmount' => $totalPaidAmount,
  'principalDueAmount' => $principalDueAmount,
  'interestDueAmount' => $interestDueAmount,
  'totalDueAmount' => $totalDueAmount,
  'lastUnpaidInstallmentId' => $lastUnpaidInstallmentId,
  'principalAmount' => $principalAmount,
  'interestAmount' => $interestAmount,
  'lastPaymentDate' => $lastPaymentDate,
  'paymentDate' => $paymentDateArray,
  'isRebate' => $isRebate,
  'rebateAmount' => $rebateAmount
);

return response::json($data);

}


public function getRebateData(Request $request)
{
  if ($request->isDonor==0) {
    $account = DB::table('acc_loan_register_account')->where('id',$request->accNo)->first();
  }

  else{
    $account = DB::table('acc_loan_register_account')->where('loanProductId_fk',$request->loanProductId)->where('phase',$request->phase)->where('cycle',$request->cycle)->first();
  }

    //$scheduleIds = DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$account->id)->pluck('id')->toArray();

  $paidPrincipalAmount = round(DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->sum('principalAmount'),2);

  $paidInterestAmount = round(DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->sum('interestAmount'),2);

  $unpaidPrincipalAmount = round($account->loanAmount - $paidPrincipalAmount,2);

  $interestAmount = round(DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$account->id)->sum('interestAmount'),2);

  $unpaidInterestAmount = round($interestAmount - $paidInterestAmount,2);

  $installments = DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$account->id)->where('isPaid',0)->pluck('scheduleNumber')->toArray();

  $data = array(
    'unpaidPrincipalAmount' => $unpaidPrincipalAmount,
    'unpaidInterestAmount' => $unpaidInterestAmount,
    'installments' => $installments
  );

  return response::json($data);
}

public function getPaymentInfo(Request $request)
{
  $payment = DB::table('acc_loan_register_payments')->where('id',$request->paymentId)->first();
  $account = DB::table('acc_loan_register_account')->where('id',$payment->accId_fk)->first();

  $donarName = DB::table('gnr_bank')->where('id',$account->bankId_fk)->value('name');
  $branchName = DB::table('gnr_bank_branch')->where('id',$account->bankBranchId_fk)->value('name');
  $loanProductName = DB::table('gnr_loan_product')->where('id',$account->loanProductId_fk)->value('name');
  $isDonor = (int) DB::table('gnr_bank')->where('id',$account->bankId_fk)->value('isDonor');

  $previousPaymentDate = DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->where('id','!=',$request->paymentId)->max('paymentDate');

  if ($previousPaymentDate==null) {
    $previousPaymentDate = "2000-01-01";
  }

  if ($payment->isRebate==1) {
    $schedulePrincipalAmount = $payment->principalAmount;
    $scheduleInterestAmount = $payment->interestAmount;
  }
  else{
    $schedulePrincipalAmount = round(DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$account->id)->where('scheduleNumber',$payment->installmentNo)->value('principalAmount'),2);
    $scheduleInterestAmount = round(DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$account->id)->where('scheduleNumber',$payment->installmentNo)->value('interestAmount'),2);
  }



  $data = array(
    'payment' => $payment,
    'account' => $account,
    'donarName' => $donarName,
    'branchName' => $branchName,
    'loanProductName' => $loanProductName,
    'isDonor' => $isDonor,
    'previousPaymentDate' => $previousPaymentDate,
    'schedulePrincipalAmount' => $schedulePrincipalAmount,
    'scheduleInterestAmount' => $scheduleInterestAmount
  );

  return response::json($data);
}




}



?>