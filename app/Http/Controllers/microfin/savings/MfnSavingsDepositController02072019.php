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
use App\microfin\MfnMemberType;
use App\Traits\CreateForm;
use App\microfin\savings\MfnSavingsDeposit;
use App\microfin\savings\MfnSavingsAccount;
use Auth;
use App\Traits\GetSoftwareDate;
use App\Http\Controllers\microfin\MicroFin;
use App\Http\Controllers\gnr\Service;
use App;


class MfnSavingsDepositController extends Controller {
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

        $deposit = MfnSavingsDeposit::where('paymentType','Interest')->first();

        $passport = $this->getPassport($deposit,'Update');

        dd($deposit,$passport);         

        $userProjectId  = Auth::user()->project_id_fk;
        $userBranchId   = Auth::user()->branchId;

        $deposits = MfnSavingsDeposit::where('softDel',0)->where('isTransferred',0);

        if ($userBranchId!=1) {
            $softDate = GetSoftwareDate::getSoftwareDate();
            $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
            //$deposits = $deposits->where('branchIdFk',$userBranchId)->where('depositDate','<=',$softDate);

            $deposits = $deposits->whereIn('branchIdFk',$branchIdArray)->where('depositDate','<=',$softDate);

            //$samityList = MicroFin::getBranchWiseSamityList($userBranchId);
            $samityList = MicroFin::getBranchWiseSamityList($branchIdArray);
        }
        else{
            if (isset($req->filBranch)) {
                if ($req->filBranch!='' && $req->filBranch!=null) {
                    $softDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);
                    $deposits = $deposits->where('branchIdFk',$req->filBranch);
                    $samityList = MicroFin::getBranchWiseSamityList($req->filBranch);
                }
                else{
                    $samityList = MicroFin::getAllSamityList();
                    $softDate = null;
                }                    
            }
            else{
                $samityList = MicroFin::getAllSamityList();
                $softDate = null;
            }
        }

        if (isset($req->filSamity)) {
            if ($req->filSamity!='' && $req->filSamity!=null) {
                $deposits = $deposits->where('samityIdFk','=',$req->filSamity);
            }
        }

        if (isset($req->filMemberCode)) {
            if ($req->filMemberCode!='' && $req->filMemberCode!=null) {
                $memberId = DB::table('mfn_member_information')->where('softDel',0)->where('code',$req->filMemberCode)->value('id');
                $deposits = $deposits->where('memberIdFk',$memberId);
            }
        }

        if (isset($req->filPrimaryProduct)) {
            if ($req->filPrimaryProduct!='' && $req->filPrimaryProduct!=null) {
                $deposits = $deposits->where('primaryProductIdFk','=',$req->filPrimaryProduct);
            }
        }

        if (isset($req->filDateFrom)) {
            if ($req->filDateFrom!='' && $req->filDateFrom!=null) {
                $dateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');
                $deposits = $deposits->where('depositDate','>=',$dateFrom);
            }
        }
        if (isset($req->filDateTo)) {
            if ($req->filDateTo!='' && $req->filDateTo!=null) {
                $dateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
                $deposits = $deposits->where('depositDate','<=',$dateTo);
            }
        }

        $deposits = $deposits->orderBy('depositDate','desc')->orderBy('branchIdFk')->where('amount','>',0);

        if (isset($req->filDateFrom)) {
            $deposits = $deposits->get();
        }
        else{
            $deposits = $deposits->paginate(100);
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

        $branchList = MicroFin::getBranchList();
        $primaryProductList = MicroFin::getAllPrimaryProductList();

        $damageData = array(
            'TCN'                   =>  $TCN,
            'deposits'              =>  $deposits,
            'bankList'              =>  $resultedBankList,
            'softDate'              =>  $softDate,
            'userBranchId'          =>  $userBranchId,
            'branchList'            =>  $branchList,
            'samityList'            =>  $samityList,
            'primaryProductList'    =>  $primaryProductList
        );

        return view('microfin.savings.savingsDeposit.viewSavingsDeposit',$damageData);
    }

    public function addDeposit() {

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

            // dd($softwareDate, $softDate);

        $data = array(
            'bankList'      => $rerultedBankList,
            'softwareDate'  => $softwareDate,
            'softDate'      => $softDate
        );

        return view('microfin/savings/savingsDeposit/addSavingsDeposit',$data);
    }


    public function storeDeposit(Request $req) {
        $branchId = MfnSavingsAccount::where('id',$req->savingsCode)->select('branchIdFk')->first();
        $softDate = MicroFin::getSoftwareDateBranchWise($branchId->branchIdFk); 

            // dd($softDate, date_format(date_create($req->transactionDate), 'Y-m-d'));

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

            if($validator->fails()){
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            }
            else {
                // CHECK IS THERE ANY OTHER TRANSACTION TODAY, IF IS THEN THIS TRANSACTION CAN'T BE DONE 
                $transactionDate = Carbon::parse($req->transactionDate)->format('Y-m-d');
                $depositToday = (int) MfnSavingsDeposit::where('accountIdFk',$req->savingsCode)->where('softDel',0)->where('amount','>',0)->where('isTransferred',0)->where('depositDate',$transactionDate)->value('id');
                $withdrawToday = 0;// (int) DB::table('mfn_savings_withdraw')->where('softDel',0)->where('accountIdFk',$req->savingsCode)->where('withdrawDate',$transactionDate)->value('id');
                $trasactionToday = max($depositToday,$withdrawToday);
                if ($trasactionToday>0) {
                    $data = array(
                        'responseTitle' =>  'Warning!',
                        'responseText'  =>  'Today Already Transaction Exits.'
                    );

                    return response::json($data);
                }


                    // Store Data
                
                if ($req->paymentMode=="Cash") {

                    $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');
                }
                else{
                    $ledgerId = $req->bank;
                }

                $account = MfnSavingsAccount::where('id',$req->savingsCode)->select('branchIdFk','samityIdFk','savingsProductIdFk')->first();

                $primaryProductId = DB::table('mfn_member_information')->where('id',$req->memberId)->value('primaryProductId');

                if (!$account->savingsProductIdFk>0 || !$primaryProductId>0) {
                    $data = array(
                        'responseTitle' =>  'Warning!',
                        'responseText'  =>  'Unexpected error ocurs, please try again.'
                    );

                    return response::json($data);
                }
                else{
                    $deposit = new MfnSavingsDeposit;
                    $deposit->memberIdFk            = $req->memberId;
                    $deposit->branchIdFk            = $account->branchIdFk;
                    $deposit->samityIdFk            = $account->samityIdFk;
                        //value of saving code is the account id
                    $deposit->accountIdFk           = $req->savingsCode;
                    $deposit->productIdFk           = $account->savingsProductIdFk;
                    $deposit->primaryProductIdFk    = $primaryProductId;
                    $deposit->amount                = $req->amount;
                    $deposit->balanceBeforeDeposit  = str_replace(',','',$req->balance);
                    $deposit->depositDate           = Carbon::parse($req->transactionDate);
                    $deposit->paymentType           = $req->paymentMode;
                    $deposit->ledgerIdFk            = $ledgerId;
                    $deposit->chequeNumber          = $req->chequeNumber;
                    $deposit->entryByEmployeeIdFk   = Auth::user()->emp_id_fk;
                    $deposit->isFromAutoProcess     = 0;
                    $deposit->createdAt             = Carbon::now();
                    $deposit->save();

                    $logArray = array(
                        'moduleId'  => 6,
                        'controllerName'  => 'MfnSavingsDepositController',
                        'tableName'  => 'mfn_savings_deposit',
                        'operation'  => 'insert',
                        'primaryIds'  => [DB::table('mfn_savings_deposit')->max('id')]
                    );
                    Service::createLog($logArray);

                    $data = array(
                        'responseTitle' =>  'Success!',
                        'responseText'  =>  'Deposit inserted successfully.'
                    );

                    return response::json($data);                
                }

                
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

    public function updateDeposit(Request $req) {

        $deposit = MfnSavingsDeposit::find($req->depositId);
        $passport = $this->getPassport($deposit,'Update');

        $previousdata = $deposit;

        // IF IT IS SUTO GENERATED INTEREST, THAN IT COULD NOT BE DELETED
        if ($deposit->paymentType=='Interest') {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'The transaction is auto generated. You can not update it.'
            );
            return response::json($data);
        }

        // IF IT ANY INEREST GENERATE AFTER OR THIS DATE, THEN IS COULD NOT BE UPDATE
        $isAnyInterest = (int) DB::table('mfn_savings_deposit')
        ->where('softDel',0)
        ->where('accountIdFk',$deposit->accountIdFk)
        ->where('depositDate','>=',$deposit->depositDate)
        ->where('paymentType','Interest')
        ->value('id');

        if ($isAnyInterest>0) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Interest is generated at today or afetr this date. You can not update it.'
            );
            return response::json($data);
        }

        // IF IT IS FROM PRIMARY PRODUCT TRANSFER THEN IT CAN NOT BE UPDATED
        if ($deposit->isTransferred==1) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'The transaction is from transfer, you cant not update it.'
            );
            return response::json($data);
        }

        // IF DELETING THIS DEPOSIT MAKES THE ACCOUNT BALANCE NEGETIVE THAN IT COULD NOT BE DELETED.
        $depositAmount = DB::table('mfn_savings_deposit')->where('softDel',0)->where('accountIdFk',$deposit->accountIdFk)->sum('amount');
        $depositAmount += DB::table('mfn_opening_savings_account_info')->where('savingsAccIdFk',$deposit->accountIdFk)->sum('openingBalance');
        $withdrawAmount = DB::table('mfn_savings_withdraw')->where('softDel',0)->where('accountIdFk',$deposit->accountIdFk)->sum('amount');

        $balace = $depositAmount - $withdrawAmount;

        if ($balace + (float)$req->amount - $deposit->amount<0) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Updating this deposit makes the account balance negetive. You can not update this deposit.'
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
                    // Store Data

                if ($req->paymentMode=="Cash") {

                    $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');;
                }
                else{
                    $ledgerId = $req->bank;
                }

                $savingsAcc = DB::table('mfn_savings_account')->where('id',$req->savingsCode)->first();
                $primaryProductId = DB::table('mfn_member_information')->where('id',$savingsAcc->memberIdFk)->first()->primaryProductId;
                
                    //value of saving code is the account id

                $deposit->memberIdFk            = $savingsAcc->memberIdFk;
                $deposit->productIdFk           = $savingsAcc->savingsProductIdFk;
                $deposit->primaryProductIdFk    = $primaryProductId;
                $deposit->accountIdFk           = $savingsAcc->id;
                $deposit->amount                = $req->amount;
                $deposit->paymentType           = $req->paymentMode;
                $deposit->ledgerIdFk            = $ledgerId;
                $deposit->chequeNumber          = $req->chequeNumber;               
                $deposit->save();

                $logArray = array(
                    'moduleId'  => 6,
                    'controllerName'  => 'MfnSavingsDepositController',
                    'tableName'  => 'mfn_savings_deposit',
                    'operation'  => 'update',
                    'previousData'  => $previousdata,
                    'primaryIds'  => [$previousdata->id]
                );
                Service::createLog($logArray);

                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Deposit updated successfully.'
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

    public function deleteDeposit(Request $req) {
        $deposit = MfnSavingsDeposit::find($req->id);
        $previousdata = $deposit;
        $account = MfnSavingsAccount::where('id',$deposit->accountIdFk)->first();
        
            // IF PRODUCT DEPOSIT TYPE ID IS 4, E.G. FIXED DEPOSIT, IT CANT BE DELETE 
        if ($account->depositTypeIdFk==4) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Sorry, you can not delete fixed deposit.'
            );
            return response::json($data);
        }

            // IF IT IS SUTO GENERATED INTEREST, THAN IT COULD NOT BE DELETED
        if ($deposit->paymentType=='Interest') {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'The transaction is auto generated. You can not delete it.'
            );
            return response::json($data);
        }

        // IF IT ANY INEREST GENERATE AFTER OR THIS DATE, THEN IS COULD NOT BE UPDATE
        $isAnyInterest = (int) DB::table('mfn_savings_deposit')
        ->where('softDel',0)
        ->where('accountIdFk',$deposit->accountIdFk)
        ->where('depositDate','>=',$deposit->depositDate)
        ->where('paymentType','Interest')
        ->value('id');

        if ($isAnyInterest>0) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Interest is generated at today or afetr this date. You can not delete it.'
            );
            return response::json($data);
        }
        
            // IF IT IS AUTHORIZED THEN CAN'T DELETE
        if ($deposit->isAuthorized==1) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'The transaction is not unauthorized.'
            );
            return response::json($data);
        }

            // IF IT IS FROM PRIMARY PRODUC TRANSFER THEN IT CAN NOT BE DELETE
        if ($deposit->isTransferred==1) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'The transaction is from transfer, you cant not delete it.'
            );
            return response::json($data);
        }
        
            // IF BRANCH DATE AND TRANSACTION DATE IS NOT THE SAME, THEN CAN'T DELETE
        if ($deposit->getOriginal()['depositDate']!=MicroFin::getSoftwareDateBranchWise($deposit->branchIdFk)) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Deposit date is not in branch date.'
            );
            return response::json($data);
        }

        // IF DELETING THIS DEPOSIT MAKES THE ACCOUNT BALANCE NEGETIVE THAN IT COULD NOT BE DELETED.
        $depositAmount = DB::table('mfn_savings_deposit')->where('softDel',0)->where('accountIdFk',$account->id)->sum('amount');        
        $depositAmount += DB::table('mfn_opening_savings_account_info')->where('savingsAccIdFk',$account->id)->sum('openingBalance');
        $withdrawAmount = DB::table('mfn_savings_withdraw')->where('softDel',0)->where('accountIdFk',$account->id)->sum('amount');

        $balace = $depositAmount - $withdrawAmount;

        if ($balace - $deposit->amount<0) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Deleting this deposit makes the account balance negetive. You can not delete this deposit.'
            );
            return response::json($data);
        }
        
        if($deposit->isFromAutoProcess==1){
            $deposit->amount = 0;
        }
        else{
            $deposit->softDel = 1;
        }
        
        $deposit->save();

        $logArray = array(
            'moduleId'  => 6,
            'controllerName'  => 'MfnSavingsDepositController',
            'tableName'  => 'mfn_savings_deposit',
            'operation'  => 'delete',
            'previousData'  => $previousdata,
            'primaryIds'  => [$previousdata->id]
        );
        Service::createLog($logArray);

        $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Deposit deleted successfully.'
        );
        
        return response::json($data);   
    }

    public function getPassport($deposit,$operation){

        // IF BRANCH DATE AND TRANSACTION DATE IS NOT THE SAME, THEN CAN'T DELETE
        if ($deposit->getOriginal()['depositDate'] != MicroFin::getSoftwareDateBranchWise($deposit->branchIdFk)) {
            $data = array(
                'canProceed'    =>  false,
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Deposit date is not in branch date.'
            );
            return $data;
        }

        // IF IT IS AUTO GENERATED INTEREST, THAN IT COULD NOT BE UPDATED/DELETED
        if ($deposit->paymentType=='Interest') {
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
        ->where('accountIdFk',$deposit->accountIdFk)
        ->where('depositDate','>=',$deposit->depositDate)
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
        if ($deposit->isTransferred==1) {
            $data = array(
                'canProceed'    =>  false,
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'The transaction is from transfer, you cant not update it.'
            );
            return $data;
        }

        // IF DELETING THIS DEPOSIT MAKES THE ACCOUNT BALANCE NEGETIVE THAN IT COULD NOT BE DELETED.
        $depositAmount = DB::table('mfn_savings_deposit')->where('softDel',0)->where('accountIdFk',$deposit->accountIdFk)->sum('amount');
        $depositAmount += DB::table('mfn_opening_savings_account_info')->where('savingsAccIdFk',$deposit->accountIdFk)->sum('openingBalance');
        $withdrawAmount = DB::table('mfn_savings_withdraw')->where('softDel',0)->where('accountIdFk',$deposit->accountIdFk)->sum('amount');

        $balace = $depositAmount - $withdrawAmount;

        if ($balace + (float)$req->amount - $deposit->amount<0) {
            $data = array(
                'canProceed'    =>  false,
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Updating this deposit makes the account balance negetive. You can not update this deposit.'
            );
            return $data;
        }



    }
    
}

