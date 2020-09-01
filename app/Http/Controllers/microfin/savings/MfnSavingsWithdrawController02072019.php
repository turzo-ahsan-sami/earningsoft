<?php

namespace App\Http\Controllers\microfin\savings;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Traits\CreateForm;
use App\Traits\GetSoftwareDate;
use App\microfin\savings\MfnSavingsWithdraw;
use App\microfin\savings\MfnSavingsAccount;
use App\Http\Controllers\microfin\MicroFin;
use Auth;
use App\Http\Controllers\gnr\Service;
use App;


class MfnSavingsWithdrawController extends Controller {
    use CreateForm;
    use GetSoftwareDate;

    private $TCN;
    
    public function __construct() {

        $this->TCN = array(
            array('SL#', 70),
            array('Member Name', 0),
            array('Member Code', 0),
            array('Savings Code', 0),
            array('Transaction Date', 0),
            array('Mode of Payment', 0),
            array('Amount', 0),
            array('Entry By', 0),
            array('Status', 0),
            array('Action', 0)
        );
    }

    public function index(Request $req) {

        $softDate = null;

        $userProjectId  = Auth::user()->project_id_fk;
        $userBranchId   = Auth::user()->branchId;

        $withdraws = MfnSavingsWithdraw::where('softDel',0)->where('isTransferred',0)->where('amount','>',0);

        if ($userBranchId!=1) {
            $softDate = GetSoftwareDate::getSoftwareDate();
            $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

                //$withdraws = $withdraws->where('branchIdFk',$userBranchId)->where('withdrawDate','<=',$softDate);
            $withdraws = $withdraws->whereIn('branchIdFk',$branchIdArray)->where('withdrawDate','<=',$softDate);
                //$samityList = MicroFin::getBranchWiseSamityList($userBranchId);
            $samityList = MicroFin::getBranchWiseSamityList($branchIdArray);
        }
        else{
            if (isset($req->filBranch)) {
                if ($req->filBranch!='' && $req->filBranch!=null) {
                    $withdraws = $withdraws->where('branchIdFk',$req->filBranch);
                    $samityList = MicroFin::getBranchWiseSamityList($req->filBranch);
                    $softDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);
                }
                else{
                    $samityList = MicroFin::getAllSamityList();
                }                    
            }
            else{
                $samityList = MicroFin::getAllSamityList();
            }
        }

        if (isset($req->filSamity)) {
            if ($req->filSamity!='' && $req->filSamity!=null) {
                $withdraws = $withdraws->where('samityIdFk','=',$req->filSamity);
            }
        }

        if (isset($req->filMemberCode)) {
            if ($req->filMemberCode!='' && $req->filMemberCode!=null) {
                $memberId = DB::table('mfn_member_information')->where('softDel',0)->where('code',$req->filMemberCode)->value('id');
                $withdraws = $withdraws->where('memberIdFk',$memberId);
            }
        }

        if (isset($req->filPrimaryProduct)) {
            if ($req->filPrimaryProduct!='' && $req->filPrimaryProduct!=null) {
                $withdraws = $withdraws->where('primaryProductIdFk','=',$req->filPrimaryProduct);
            }
        }

        if (isset($req->filDateFrom)) {
            if ($req->filDateFrom!='' && $req->filDateFrom!=null) {
                $dateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');
                $withdraws = $withdraws->where('withdrawDate','>=',$dateFrom);
            }
        }
        if (isset($req->filDateTo)) {
            if ($req->filDateTo!='' && $req->filDateTo!=null) {
                $dateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
                $withdraws = $withdraws->where('withdrawDate','<=',$dateTo);
            }
        }
        
        $withdraws = $withdraws->orderBy('withdrawDate','DESC')->orderBy('branchIdFk');

        if (isset($req->filDateFrom)) {
            $withdraws = $withdraws->get();
        }
        else{
            $withdraws = $withdraws->paginate(100);
        }

        $bankFromLaser = DB::table('acc_account_ledger')
        ->where('accountTypeId',5)
        ->select('projectBranchId','id')
        ->get();

        $bankList = array();
        foreach ($bankFromLaser as $key => $bank) {
            $projectBranchString = str_replace(['"','[',']'], '', $bank->projectBranchId);
            $projectBranchArray = explode(',', $projectBranchString);
            
            foreach ($projectBranchArray as $key => $projectBranch) {
                $result = explode(':', $projectBranch);
                $bankProjectId  = $result[0];
                $bankBranchId   = $result[1];

                if (($bankProjectId==0 && $bankBranchId==0) || ($bankBranchId==$userBranchId) || ($userProjectId==$bankProjectId && $bankBranchId==0)) {
                    array_push($bankList, $bank->id);                        
                }
            }                
        }

        $resultedBankList = DB::table('acc_account_ledger')
        ->whereIn('id',$bankList)
        ->where('id','!=',350)
        ->select('name','id','code')
        ->get();

        $TCN = $this->TCN;

        $userBranchId = Auth::user()->branchId;
        $primaryProductList = MicroFin::getAllPrimaryProductList();

        $branchList = DB::table('gnr_branch')
        ->orderBy('branchCode')
        ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
        ->pluck('nameWithCode', 'id')
        ->all();

        $damageData = array(
            'TCN'                   =>  $TCN,
            'withdraws'             =>  $withdraws,
            'bankList'              =>  $resultedBankList,
            'softDate'              =>  $softDate,
            'userBranchId'          =>  $userBranchId,
            'branchList'            =>  $branchList,
            'samityList'            =>  $samityList,
            'primaryProductList'    =>  $primaryProductList
        );

        return view('microfin/savings/savingsWithdraw/viewSavingsWithdraw',$damageData);
    }

    public function addWithdraw() {

        $userProjectId  = Auth::user()->project_id_fk;
        $userBranchId   = Auth::user()->branchId;
        
        $bankFromLaser = DB::table('acc_account_ledger')
        ->where('accountTypeId',5)
        ->select('projectBranchId','id')
        ->get();

        $bankList = array();
        foreach ($bankFromLaser as $key => $bank) {
            $projectBranchString = str_replace(['"','[',']'], '', $bank->projectBranchId);
            $projectBranchArray = explode(',', $projectBranchString);
            
            foreach ($projectBranchArray as $key => $projectBranch) {
                $result = explode(':', $projectBranch);
                $bankProjectId  = $result[0];
                $bankBranchId   = $result[1];

                if (($bankProjectId==0 && $bankBranchId==0) || ($bankBranchId==$userBranchId) || ($userProjectId==$bankProjectId && $bankBranchId==0)) {
                    array_push($bankList, $bank->id);
                    
                }
            }                
        }

        $rerultedBankList = DB::table('acc_account_ledger')
        ->whereIn('id',$bankList)
        ->where('id','!=',350)
        ->select('name','id','code')
        ->get();

        $softwareDate = GetSoftwareDate::getSoftwareDateInFormat();
        $softDate = GetSoftwareDate::getSoftwareDate();

        $data = array(
            'bankList'      =>$rerultedBankList,
            'softwareDate'  =>$softwareDate,
            'softDate'      => $softDate
        );
        

        return view('microfin/savings/savingsWithdraw/addSavingsWithdraw',$data);
    }

    
    public function storeWithdraw(Request $req) {
        $branchId = MfnSavingsAccount::where('id',$req->savingsCode)->select('branchIdFk')->first();
        $softDate = MicroFin::getSoftwareDateBranchWise($branchId->branchIdFk); 

        if ($softDate == date_format(date_create($req->transactionDate), 'Y-m-d') ) {
            $rules = array(
                'member'            =>  'required',
                'transactionDate'   =>  'required',
                'savingsCode'       =>  'required',
                'amount'            =>  'required'
            );

            if ($req->paymentMode=='Bank') {
                $rules = $rules + array(
                    'bank'          =>  'required',
                    'chequeNumber'  =>  'required'
                );
            }

            $attributesNames = array(
                'member'            =>  'Member',
                'transactionDate'   =>  'Transaction Date',
                'savingsCode'       =>  'Savings Code',
                'amount'            =>  'Amount',
                'bank'              =>  'Bank',
                'chequeNumber'      =>  'Cheque Number'
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {

                    // Check Is there any other transaction Today, if is then this transaction can't be done
                $transactionDate = Carbon::parse($req->transactionDate)->format('Y-m-d');
                    $depositToday = 0;//(int) DB::table('mfn_savings_deposit')->where('softDel',0)->where('accountIdFk',$req->savingsCode)->where('depositDate',$transactionDate)->value('id');
                    $withdrawToday = (int) DB::table('mfn_savings_withdraw')->where('softDel',0)->where('isTransferred',0)->where('accountIdFk',$req->savingsCode)->where('withdrawDate',$transactionDate)->value('id');
                    $trasactionToday = max($depositToday,$withdrawToday);
                    if ($trasactionToday>0) {
                        $data = array(
                            'responseTitle' =>  'Warning!',
                            'responseText'  =>  'Today Already Transaction Exits.'
                        );

                        return response::json($data);
                    }

                    $amount = floatval(str_replace(',', '', $req->amount));

                    if ($amount==0) {
                        $data = array(
                            'responseTitle' =>  'Warning!',
                            'responseText'  =>  'Amount must be greater than zero.'
                        );

                        return response::json($data);
                    }

                    // IF WITHDRAW AMOUNT IS GREATER THA THE OUTSTANDING AMOUNT THEN GIVE A WARNING MEASSGAE
                    $totalDeposit = DB::table('mfn_savings_deposit')
                    ->where('softDel',0)
                    ->where('accountIdFk',$req->savingsCode)
                    ->sum('amount');

                    $totalDeposit += DB::table('mfn_opening_savings_account_info')
                    ->where('savingsAccIdFk',$req->savingsCode)
                    ->sum('openingBalance');

                    $totalWithdraw = DB::table('mfn_savings_withdraw')
                    ->where('softDel',0)
                    ->where('accountIdFk',$req->savingsCode)
                    ->sum('amount');

                    $balance = $totalDeposit - $totalWithdraw;

                    if ($amount>$balance) {
                        $data = array(
                            'responseTitle' =>  'Warning!',
                            'responseText'  =>  'Amount is greater than balance amount.'
                        );

                        return response::json($data);
                    }
                    
                    // Store Data
                    
                    if ($req->paymentMode=="Cash") {
                        $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');;
                    }
                    else{
                        $ledgerId = $req->bank;
                    }

                    $account = MfnSavingsAccount::where('id',$req->savingsCode)->select('branchIdFk','samityIdFk','savingsProductIdFk')->first();
                    $primaryProductId = DB::table('mfn_member_information')->where('id',$req->memberId)->value('primaryProductId');

                    $withdraw = new MfnSavingsWithdraw;
                    $withdraw->memberIdFk               = $req->memberId;
                    $withdraw->branchIdFk               = $account->branchIdFk;
                    $withdraw->samityIdFk               = $account->samityIdFk;
                    //value of saving code is the account id
                    $withdraw->accountIdFk              = $req->savingsCode;
                    $withdraw->productIdFk              = $account->savingsProductIdFk;
                    $withdraw->primaryProductIdFk       = $primaryProductId;
                    $withdraw->amount                   = $amount;
                    $withdraw->balanceBeforeWithdraw    = str_replace(',','',$req->balance);
                    $withdraw->WithdrawDate             = Carbon::parse($req->transactionDate);
                    $withdraw->paymentType              = $req->paymentMode;
                    $withdraw->ledgerIdFk               = $ledgerId;
                    $withdraw->chequeNumber             = $req->chequeNumber;
                    $withdraw->entryByEmployeeIdFk      = Auth::user()->emp_id_fk;
                    $withdraw->createdAt                = Carbon::now();
                    $withdraw->save();

                    $logArray = array(
                        'moduleId'  => 6,
                        'controllerName'  => 'MfnSavingsWithdrawController',
                        'tableName'  => 'mfn_savings_withdraw',
                        'operation'  => 'insert',
                        'primaryIds'  => [DB::table('mfn_savings_withdraw')->max('id')]
                    );
                    Service::createLog($logArray);

                    $data = array(
                        'responseTitle' =>  'Success!',
                        'responseText'  =>  'Withdraw inserted successfully.'
                    );

                    return response::json($data);                
                }   
            }
            else {
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Transaction date is not matching with software date! Please check day end again!'
                );

                return response::json($data);
            }

            
        }

        public function updateWithdraw(Request $req) {
            $withdraw = MfnSavingsWithdraw::find($req->withdrawId);

            $previousdata = $withdraw;

            // if it is from primary produc transfer then it can not be delete
            if ($withdraw->isTransferred==1) {
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'The transaction is from transfer, you cant not update it.'
                );
                return response::json($data);
            }

            $branchId = MfnSavingsAccount::where('id',$req->savingsCode)->select('branchIdFk')->first();
            $softDate = MicroFin::getSoftwareDateBranchWise($branchId->branchIdFk); 

            if ($softDate == date_format(date_create($req->transactionDate), 'Y-m-d') ) {
                $rules = array(
                    'member'            =>  'required',
                    'transactionDate'   =>  'required',
                    'savingsCode'       =>  'required',
                    'amount'            =>  'required'
                );

                if ($req->paymentMode=='Bank') {
                    $rules = $rules + array(
                        'bank'          =>  'required',
                        'chequeNumber'  =>  'required'
                    );
                }

                $attributesNames = array(
                    'member'            =>  'Member',
                    'transactionDate'   =>  'Transaction Date',
                    'savingsCode'       =>  'Savings Code',
                    'amount'            =>  'Amount',
                    'bank'              =>  'Bank',
                    'chequeNumber'      =>  'Cheque Number'
                );

                $validator = Validator::make(Input::all(), $rules);
                $validator->setAttributeNames($attributesNames);

                if($validator->fails()) 
                    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
                else {

                    $amount = floatval(str_replace(',', '', $req->amount));

                    if ($amount==0) {
                        $data = array(
                            'responseTitle' =>  'Warning!',
                            'responseText'  =>  'Amount must be greater than zero.'
                        );

                        return response::json($data);
                    }
                    // Store Data
                    
                    if ($req->paymentMode=="Cash") {
                        $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');;
                    }
                    else{
                        $ledgerId = $req->bank;
                    }

                    

                    if ($withdraw->isFromClosing==1) {
                        $data = array(
                            'responseTitle' =>  'Warning!',
                            'responseText'  =>  'It is from closing. You can not update it.'
                        );

                        return response::json($data);
                    }

                    // IF WITHDRAW AMOUNT IS GREATER THA THE OUTSTANDING AMOUNT THEN GIVE A WARNING MEASSGAE
                    $totalDeposit = DB::table('mfn_savings_deposit')
                    ->where('softDel',0)
                    ->where('accountIdFk',$req->savingsCode)
                    ->sum('amount');

                    $totalDeposit += DB::table('mfn_opening_savings_account_info')
                    ->where('savingsAccIdFk',$req->savingsCode)
                    ->sum('openingBalance');

                    $totalWithdraw = DB::table('mfn_savings_withdraw')
                    ->where('softDel',0)
                    ->where('id','!=',$withdraw->id)
                    ->where('accountIdFk',$req->savingsCode)
                    ->sum('amount');

                    $balance = $totalDeposit - $totalWithdraw;

                    if ($amount>$balance) {
                        $data = array(
                            'responseTitle' =>  'Warning!',
                            'responseText'  =>  'Amount is greater than balance amount.'
                        );

                        return response::json($data);
                    }

                    $savingsAcc = DB::table('mfn_savings_account')->where('id',$req->savingsCode)->first();
                    $primaryProductId = DB::table('mfn_member_information')->where('id',$savingsAcc->memberIdFk)->first()->primaryProductId;


                    //value of saving code is the account id

                    $withdraw->memberIdFk            = $savingsAcc->memberIdFk;
                    $withdraw->productIdFk           = $savingsAcc->savingsProductIdFk;
                    $withdraw->primaryProductIdFk    = $primaryProductId;
                    $withdraw->accountIdFk           = $savingsAcc->id;
                    $withdraw->amount                = $amount;
                    $withdraw->paymentType           = $req->paymentMode;
                    $withdraw->ledgerIdFk            = $ledgerId;
                    $withdraw->chequeNumber          = $req->chequeNumber;               
                    $withdraw->save();

                    $logArray = array(
                        'moduleId'  => 6,
                        'controllerName'  => 'MfnSavingsWithdrawController',
                        'tableName'  => 'mfn_savings_withdraw',
                        'operation'  => 'update',
                        'previousData'  => $previousdata,
                        'primaryIds'  => [$previousdata->id]
                    );
                    Service::createLog($logArray);

                    $data = array(
                        'responseTitle' =>  'Success!',
                        'responseText'  =>  'Withdraw updated successfully.'
                    );

                    return response::json($data);                
                }  
            }
            else {
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Transaction date is not matching with software date! Please check day end again!'
                );

                return response::json($data);
            }

            
        }

        public function deleteWithdraw(Request $req) {
            $withdraw = MfnSavingsWithdraw::find($req->id);

            $previousdata = $withdraw;
            
            // if it is from savings closing, then can't delete it.
            if ($withdraw->isFromClosing==1) {
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'It is from closing, you can not delete it directly.'
                );
                return response::json($data);
            }

            // if it is from primary produc transfer then it can not be delete
            if ($withdraw->isTransferred==1) {
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'The transaction is from transfer, you cant not update it.'
                );
                return response::json($data);
            }

            // if it is authorized then can't delete
            if ($withdraw->isAuthorized==1) {
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'The transaction is not unauthorized.'
                );
                return response::json($data);
            }
            

            // if branch date and transaction date is not the same, then can't delete
            if ($withdraw->getOriginal()['withdrawDate']!=MicroFin::getSoftwareDateBranchWise($withdraw->branchIdFk)) {
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Withdraw date is not in branch date.'
                );
                return response::json($data);
            }
            
            $withdraw->softDel = 1;
            $withdraw->save();

            $logArray = array(
                'moduleId'  => 6,
                'controllerName'  => 'MfnSavingsWithdrawController',
                'tableName'  => 'mfn_savings_withdraw',
                'operation'  => 'delete',
                'previousData'  => $previousdata,
                'primaryIds'  => [$previousdata->id]
            );
            Service::createLog($logArray);

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Withdraw deleted successfully.'
            );
            
            return response::json($data);   
        }

        public function getPassport($withdraw,$operation){

        // IF BRANCH DATE AND TRANSACTION DATE IS NOT THE SAME, THEN CAN'T DELETE
            if ($withdraw->getOriginal()['depositDate'] != MicroFin::getSoftwareDateBranchWise($withdraw->branchIdFk)) {
                $data = array(
                    'canProceed'    =>  false,
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Deposit date is not in branch date.'
                );
                return $data;
            }

        // IF IT IS AUTO GENERATED INTEREST, THAN IT COULD NOT BE UPDATED/DELETED
            if ($withdraw->paymentType=='Interest') {
                $data = array(
                    'canProceed'    =>  false,
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'The transaction is auto generated. You can not '.$operation.' it.'
                );
                return $data;
            }

        // IF IT ANY INEREST GENERATE AFTER OR THIS DATE, THEN IS COULD NOT BE UPDATED/DELETED
            $isAnyInterest = (int) DB::table('mfn_savings_deposit')
            ->where('softDel',0)
            ->where('accountIdFk',$withdraw->accountIdFk)
            ->where('withdrawDate','>=',$withdraw->depositDate)
            ->where('paymentType','Interest')
            ->value('id');

            if ($isAnyInterest>0) {
                $data = array(
                    'canProceed'    =>  false,
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Interest is generated at today or after this date. You can not '.$operation.' it.'
                );
                return $data;
            }

        // IF IT IS FROM PRIMARY PRODUCT TRANSFER THEN IT CAN NOT BE UPDATED
            if ($withdraw->isTransferred==1) {
                $data = array(
                    'canProceed'    =>  false,
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'The transaction is from transfer, you cant not update it.'
                );
                return $data;
            }

        // IF DELETING THIS DEPOSIT MAKES THE ACCOUNT BALANCE NEGETIVE THAN IT COULD NOT BE DELETED.
            $depositAmount = DB::table('mfn_savings_deposit')->where('softDel',0)->where('accountIdFk',$withdraw->accountIdFk)->sum('amount');
            $depositAmount += DB::table('mfn_opening_savings_account_info')->where('savingsAccIdFk',$withdraw->accountIdFk)->sum('openingBalance');
            $withdrawAmount = DB::table('mfn_savings_withdraw')->where('softDel',0)->where('accountIdFk',$withdraw->accountIdFk)->sum('amount');

            $balace = $depositAmount - $withdrawAmount;

            if ($balace + (float)$req->amount - $withdraw->amount<0) {
                $data = array(
                    'canProceed'    =>  false,
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Updating this deposit makes the account balance negetive. You can not update this deposit.'
                );
                return $data;
            }



        }
        
    }

    