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
    use App\Traits\GetSoftwareDate;
    use App\microfin\savings\MfnSavingsProduct;
    use App\microfin\savings\MfnSavingsFdrProductRepayAmount;
    use App\microfin\savings\MfnSavingsAccount;
    use App\microfin\savings\MfnOpeningSavingsAccountInfo;
    use App\microfin\savings\MfnSavingsAccountNominee;
    use App\microfin\savings\MfnSavingsDeposit;
    use Auth;

    class MfnSavingsAccountController extends Controller {
        use CreateForm;
        use GetSoftwareDate;

        private $TCN;

        public function __construct() {

            $this->TCN = array(
                array('SL#', 55),
                array('Savings Code', 170),
                array('Member Code', 0),
                array('Member Name', 0),
                array('Samity Code', 0),
                array('Samity Name', 0),
                array('Auto Process Amount', 120),
                array('Opening Date', 0),
                array('Savings <br> Status', 80),
                array('Closing Date', 0),
                array('Entry By', 0),
                array('Action', 100),
            );            

        }

        public function index(Request $req) {

            $userBarnchId = Auth::user()->branchId;
            $softDate = GetSoftwareDate::getSoftwareDate();

            $accounts = MfnSavingsAccount::where('softDel',0);
            if ($userBarnchId!=1) {
                $accounts = $accounts->where('branchIdFk',$userBarnchId);
            }
            $accounts = $accounts->where('accountOpeningDate','<=',$softDate)->paginate(15);
            $TCN = $this->TCN;

            $softwareDate = GetSoftwareDate::getSoftwareDateInFormat();
            $bankList = $this->getBankList();

            $damageData = array(
                'TCN'           =>  $TCN,
                'accounts'      =>  $accounts,
                'softwareDate'  =>  $softwareDate,
                'softDate'      =>  $softDate,
                'bankList'      =>  $bankList,
                'userBarnchId'  =>  $userBarnchId
            );

            return view('microfin.savings.savingsAccount.viewSavingsAccount',$damageData);
        }

        public function viewOpenigAccounts(){

            $accounts = MfnSavingsAccount::where('softDel',0)->where('isFromOpening',1)->paginate(15);

            $openingInfo = DB::table('mfn_opening_savings_account_info')->select('savingsAccIdFk','manualSavingCode')->get();
            
            $TCN = $this->TCN;

            $damageData = array(
                'TCN'           =>  $TCN,
                'accounts'      =>  $accounts,
                'openingInfo'   =>  $openingInfo
            );

            return view('microfin.configuration.openingBalance.savings.OpeningSavingsAccount.viewSavingsAccount',$damageData);
        }

        public function addAccount() {
            $softwareDate = GetSoftwareDate::getSoftwareDate();
            $softwareDateInFormat = GetSoftwareDate::getSoftwareDateInFormat();
            $bankList = $this->getBankList();
            $data = array(
                'softwareDate'          =>  $softwareDate,
                'softwareDateInFormat'  =>  $softwareDateInFormat,
                'bankList'              =>  $bankList
            );

            return view('microfin.savings.savingsAccount.addSavingsAccount',$data);
        }

        public function addOpeningAccount() {
            $softwareDate = GetSoftwareDate::getSoftwareDate();
            $softwareDateInFormat = GetSoftwareDate::getSoftwareDateInFormat();
            $bankList = $this->getBankList();
            $data = array(
                'softwareDate'          =>  $softwareDate,
                'softwareDateInFormat'  =>  $softwareDateInFormat,
                'bankList'              =>  $bankList
            );
            
            return view('microfin.configuration.openingBalance.savings.OpeningSavingsAccount.addSavingsAccount',$data);
        }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: STORE PRODUCT
        |--------------------------------------------------------------------------
        */
        public function storeAccount(Request $req) {

            $member = DB::table('mfn_member_information')->where('id',$req->memberId)->first();
            $product = MfnSavingsProduct::find($req->product);
            $customError = array();

            $rules = array(
                'member'        =>  'required',
                'product'       =>  'required'                
            );

            if (isset($req->isOpeningData)) {
                $rules = $rules + array(
                    'manualSavingCode'  => 'required|unique:mfn_opening_savings_account_info'
                );
            }
            
            // Deposit Type Voluntary Monthly
            if ($product->depositTypeIdFk==2 && $product->savingCollectionFrequencyIdFk==2){
                $rules = $rules + array(
                    'periodYear'        => 'required',
                    'periodMonth'       => 'required',
                    /*'initialAmount'     => 'required',*/
                    'autoProcessAmount' => 'required',
                    'matureDate'        => 'required',
                    'payableAmount'     => 'required'
                );
            }            

            // Deposit Type FDR (Voluntary)
            if ($product->depositTypeIdFk==4) {                
                $rules = $rules + array(
                    'periodOts'             => 'required',
                    'fixedDepositAmount'    => 'required',
                    'matureDate'            => 'required',
                    'payableAmount'         => 'required'
                                      
                ); 

                if (!isset($req->isOpeningData)) {
                    $rules = $rules + array(
                        'transactionType'       => 'required'
                    );
                }
            } 
            if ($req->transactionType=='Bank' && !isset($req->isOpeningData)) {
                $rules = $rules + array(
                'bank'          => 'required',
                'chequeNumber'  => 'required'
            );
            }

            if ($product->isNomineeRequired==1 && !isset($req->isOpeningData)) {  

                // If Nominee information is empty give custom error
                if (isset($req->nomineeName)) {
                    $totalShare = 0;
                    foreach ($req->nomineeName as $key => $nomineeName) {
                        if ($nomineeName=='' || $req->nomineeRealtion[$key]=='' || $req->nomineeShare[$key]=='') {
                            $customError =  $customError + array(
                                'emptyTableError'   => 'Please fill all the fields.'
                            );
                        }
                        $totalShare = $totalShare + (float) $req->nomineeShare[$key];             
                    }

                    if ($totalShare!=100) {
                        $customError =  $customError + array(
                            'nomineeShareError'   => 'Sum of Share should be 100%.'
                        );
                    }
                    
                }
                else{
                    // No nominee added
                    $customError =  $customError + array(
                        'emptyTableError'   => 'Please add atleast one nominee.'
                    );
                }
            }

            $attributesNames = array(
                'member'                => 'Member',
                'product'               => 'Product',
                'manualSavingCode'      => 'Saving Code',
                'periodYear'            => 'Year',
                'periodMonth'           => 'Month',
                'autoProcessAmount'     => 'Auto Process Amount',
                'matureDate'            => 'Mature Date',
                'payableAmount'         => 'Payable Amount',
                'periodOts'             => 'Period',
                'fixedDepositAmount'    => 'Fixed Deposit Amount',
                'transactionType'       => 'Transaction Type'
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails() || count($customError)>0) {
                return response::json(array('errors' => $validator->getMessageBag()->toArray() + $customError));
            }
            else {
                // Store Data                
                DB::beginTransaction();
                try{
                $savingCycle = DB::table('mfn_savings_account')->where('memberIdFk',$member->id)->where('depositTypeIdFk','!=',1)->where('savingsProductIdFk',$product->id)->count() + 1;

                $numberOfAcc = $savingCycle;
                $numberOfAcc = str_pad($numberOfAcc, 3,'0',STR_PAD_LEFT);
                $savingCode = $product->shortName.'.'.$member->code.'.'.$numberOfAcc;
                
                $account = new MfnSavingsAccount;
                $account->savingsCode           =   $savingCode;
                $account->accountOpeningDate    =   Carbon::parse($req->openingDate);
                $account->savingsProductIdFk    =   $product->id;
                $account->memberIdFk            =   $member->id;
                $account->depositTypeIdFk       =   $product->depositTypeIdFk;
                $account->savingsInterestRate   =   $product->interestRate;
                $account->branchIdFk            =   $req->branchId;
                $account->samityIdFk            =   $req->samityId;
                $account->workingAreaIdFk       =   $req->workingAreaId;
                $account->entryByEmployeeIdFk   =   Auth::user()->emp_id_fk;
                $account->createdDate           =   Carbon::now();

                // Deposit Type Mendatory
                if ($product->depositTypeIdFk==1){
                    $account->autoProcessAmount     =  $req->autoProcessAmount;
                }

                // Deposit Type Voluntary
                if ($product->depositTypeIdFk==2 && $product->savingCollectionFrequencyIdFk==1){
                    $account->autoProcessAmount     =  $req->autoProcessAmount;
                }

                // Deposit Type Voluntary Monthly
                if ($product->depositTypeIdFk==2 && $product->savingCollectionFrequencyIdFk==2){                    
                    
                    $account->periodYear            =  $req->periodYear;
                    $account->periodMonth           =  $req->periodMonth;
                    $account->initialAmount         =  $req->initialAmount;
                    $account->autoProcessAmount     =  $req->autoProcessAmount;
                    $account->accountMatureDate     =  Carbon::parse($req->matureDate);
                    $account->payableAmount         =  str_replace(',','',$req->payableAmount);
                }

                // Deposit Type FDR (Voluntary)
                if ($product->depositTypeIdFk==4){

                    $period = explode('-',$req->periodOts);               
                    
                    $account->periodYear            =  $period[0];
                    $account->periodMonth           =  $period[1];
                    $account->fixedDepositAmount    =  $req->fixedDepositAmount;
                    $account->accountMatureDate     =  Carbon::parse($req->matureDate);
                    $account->payableAmount         =  str_replace(',','',$req->payableAmount);
                    $account->transactionType       =  $req->transactionType;
                }
                else{
                    $account->transactionType       =  '';
                }

                if ($product->isMultipleSavingAllowed) {
                    $account->savingCycle       =  $savingCycle;
                }

                if (isset($req->isOpeningData)) {
                    $account->isFromOpening = 1;
                }

                $account->save();

                // Deposit Type FDR (Voluntary)
                if ($product->isNomineeRequired==1){
                    if (count($req->nomineeName)>0) {
                        foreach ($req->nomineeName as $key => $nomineeName) {
                            $nominee = new MfnSavingsAccountNominee;
                            $nominee->memberIdFk            =   $member->id;
                            $nominee->savingsAccountIdFk    =   $account->id;
                            $nominee->name                  =   $nomineeName;
                            $nominee->relation              =   $req->nomineeRealtion[$key];
                            $nominee->share                 =   $req->nomineeShare[$key];
                            $nominee->createdAt             =   Carbon::now();
                            $nominee->save();
                        }  
                    }           
                } 

                // Here deposit will be saved when it is one time savings account
                if ($req->transactionType=="Cash") {
                    // Cash In Hand ledger id
                    $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');;
                }
                else{
                     $ledgerId = $req->bank;
                }
                if ($product->depositTypeIdFk==4 && !isset($req->isOpeningData)){
                    $primaryProductId = DB::table('mfn_member_information')->where('id',$account->memberIdFk)->value('primaryProductId');
                    $deposit = new MfnSavingsDeposit;
                    $deposit->memberIdFk            = $member->id;
                    $deposit->branchIdFk            = $req->branchId;
                    $deposit->samityIdFk            = $req->samityId;
                    $deposit->accountIdFk           = $account->id;
                    $deposit->productIdFk           = $account->savingsProductIdFk;
                    $deposit->primaryProductIdFk    = $primaryProductId;
                    $deposit->amount                = str_replace(',','',$req->fixedDepositAmount);
                    $deposit->balanceBeforeDeposit  = 0;
                    $deposit->depositDate           = Carbon::parse($req->openingDate);
                    $deposit->paymentType           = $req->transactionType;
                    $deposit->ledgerIdFk            = $ledgerId;
                    $deposit->chequeNumber          = $req->chequeNumber;
                    $deposit->entryByEmployeeIdFk   = Auth::user()->emp_id_fk;
                    $deposit->isFromAutoProcess     = 0;
                    $deposit->createdAt             = Carbon::now();
                    $deposit->save();
                }


                // Store Data for the Opening Accounts
                if (isset($req->isOpeningData)) {
                    $savingsInfo = new MfnOpeningSavingsAccountInfo;
                    $savingsInfo->savingsAccIdFk    = $account->id;
                    $savingsInfo->manualSavingCode  = $req->manualSavingCode;
                    $savingsInfo->save();
                }
               
                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Account inserted successfully.'
                );
                DB::commit();
                return response::json($data);  } 
                catch(\Exception $e){
					DB::rollback();
					$data = array(
						'responseTitle'  =>  'Warning!',
						'responseText'   =>  'Something went wrong. Please try again.'
	 				);
	 				return response::json($data);
				}

             
            }
            
        }


        public function updateAccount(Request $req) {
            DB::beginTransaction();

            try{$member = DB::table('mfn_member_information')->where('id',$req->memberId)->first();
            $product = MfnSavingsProduct::find($req->product);
            $customError = array();

            $rules = array(
                'member'        =>  'required',
                'product'       =>  'required'                
            );
            
            // Deposit Type Voluntary Monthly
            if ($product->depositTypeIdFk==2 && $product->savingCollectionFrequencyIdFk==2){
                $rules = $rules + array(
                    'periodYear'        => 'required',
                    'periodMonth'       => 'required',
                    'initialAmount'     => 'required',
                    'autoProcessAmount' => 'required',
                    'matureDate'        => 'required',
                    'payableAmount'     => 'required'
                );
            }            

            // Deposit Type FDR (Voluntary)
            if ($product->depositTypeIdFk==4) {                
                $rules = $rules + array(
                    'periodOts'             => 'required',
                    'fixedDepositAmount'    => 'required',
                    'matureDate'            => 'required',
                    'payableAmount'         => 'required',
                    'transactionType'       => 'required'                  
                ); 
            } 
            if ($req->transactionType=='Bank') {
                $rules = $rules + array(
                'bank'          => 'required',
                'chequeNumber'  => 'required'
                ); 
            }

            if ($product->isNomineeRequired==1) {  

            // If Nominee information is empty give custom error
                if (isset($req->nomineeName)) {
                    $totalShare = 0;
                    foreach ($req->nomineeName as $key => $nomineeName) {
                        if ($nomineeName=='' || $req->nomineeRealtion[$key]=='' || $req->nomineeShare[$key]=='') {
                            $customError =  $customError + array(
                                'emptyTableError'   => 'Please fill all the fields.'
                            );
                        }
                        $totalShare = $totalShare + (float) $req->nomineeShare[$key];             
                    }

                    if ($totalShare!=100) {
                        $customError =  $customError + array(
                            'nomineeShareError'   => 'Sum of Share should be 100%.'
                        );
                    }
                    
                }
                else{
                    // No nominee added
                    $customError =  $customError + array(
                        'emptyTableError'   => 'Please add atleast one nominee.'
                    );
                }
            }

            $attributesNames = array(
                'member'                => 'Member',
                'product'               => 'Product',
                'periodYear'            => 'Year',
                'periodMonth'           => 'Month',
                'initialAmount'         => 'Initial Amount',
                'autoProcessAmount'     => 'Auto Process Amount',
                'matureDate'            => 'Mature Date',
                'payableAmount'         => 'Payable Amount',
                'periodOts'             => 'Period',
                'fixedDepositAmount'    => 'Fixed Deposit Amount',
                'transactionType'       => 'Transaction Type',
                'bank'                  => 'Bank',
                'chequeNumber'          => 'Cheque Number'
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails() || count($customError)>0) {
                return response::json(array('errors' => $validator->getMessageBag()->toArray() + $customError));
            }
            else {
                // Store Data                

                $savingCycle = DB::table('mfn_savings_account')->where('memberIdFk',$member->id)->where('depositTypeIdFk','!=',1)->where('savingsProductIdFk',$product->id)->count() + 1;
                
                $account = MfnSavingsAccount::find($req->accId);
                $account->savingsCode           =   $req->savingCode;
                $account->accountOpeningDate    =   Carbon::now();
                $account->savingsProductIdFk    =   $product->id;
                $account->memberIdFk            =   $member->id;
                $account->depositTypeIdFk       =   $product->depositTypeIdFk;
                $account->savingsInterestRate   =   $product->interestRate;
                $account->branchIdFk            =   $req->branchId;
                $account->samityIdFk            =   $req->samityId;
                $account->workingAreaIdFk       =   $req->workingAreaId;

                // Deposit Type Mendatory
                if ($product->depositTypeIdFk==1){
                    $account->autoProcessAmount     =  $req->autoProcessAmount;
                }

                // Deposit Type Voluntary
                if ($product->depositTypeIdFk==2 && $product->savingCollectionFrequencyIdFk==1){
                    $account->autoProcessAmount     =  $req->autoProcessAmount;
                }

                // Deposit Type Voluntary Monthly
                if ($product->depositTypeIdFk==2 && $product->savingCollectionFrequencyIdFk==2){                    
                    
                    $account->periodYear            =  $req->periodYear;
                    $account->periodMonth           =  $req->periodMonth;
                    $account->initialAmount         =  $req->initialAmount;
                    $account->autoProcessAmount     =  $req->autoProcessAmount;
                    $account->accountMatureDate     =  Carbon::parse($req->matureDate);
                    $account->payableAmount         =  str_replace(',','',$req->payableAmount);
                }

                // Deposit Type FDR (Voluntary)
                if ($product->depositTypeIdFk==4){

                    $period = explode('-',$req->periodOts);               
                    
                    $account->periodYear            =  $period[0];
                    $account->periodMonth           =  $period[1];
                    $account->fixedDepositAmount    =  $req->fixedDepositAmount;
                    $account->accountMatureDate     =  Carbon::parse($req->matureDate);
                    $account->payableAmount         =  str_replace(',','',$req->payableAmount);
                    $account->transactionType       =  $req->transactionType;
                }

                if ($product->isMultipleSavingAllowed) {
                    $account->savingCycle       =  $savingCycle;
                }

                $account->save();

                DB::table('mfn_savings_fdr_acc_nominee_info')->where('savingsAccountIdFk',$account->id)->delete();
                // Deposit Type FDR (Voluntary)
                if ($product->isNomineeRequired==1){

                    foreach ($req->nomineeName as $key => $nomineeName) {
                        $nominee = new MfnSavingsAccountNominee;
                        $nominee->memberIdFk            =   $member->id;
                        $nominee->savingsAccountIdFk    =   $account->id;
                        $nominee->name                  =   $nomineeName;
                        $nominee->relation              =   $req->nomineeRealtion[$key];
                        $nominee->share                 =   $req->nomineeShare[$key];
                        $nominee->createdAt             =   Carbon::now();
                        $nominee->save();
                    }
                    
                }   

                if ($req->transactionType=="Cash") {
                    // Cash In Hand ledger id
                    $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');;
                }
                else{
                     $ledgerId = $req->bank;
                }

                if ($product->depositTypeIdFk==4){
                    $deposit = MfnSavingsDeposit::firstOrNew(['accountIdFk'=>$account->id]);
                    $deposit->memberIdFk            = $member->id;
                    $deposit->branchIdFk            = $req->branchId;
                    $deposit->samityIdFk            = $req->samityId;
                    $deposit->accountIdFk           = $account->id;
                    $deposit->amount                = str_replace(',','',$req->fixedDepositAmount);
                    $deposit->balanceBeforeDeposit  = 0;
                    $deposit->depositDate           = Carbon::parse($req->openingDate);
                    $deposit->paymentType           = $req->transactionType;
                    $deposit->ledgerIdFk            = $ledgerId;
                    $deposit->chequeNumber          = $req->chequeNumber;
                    $deposit->entryByEmployeeIdFk   = Auth::user()->emp_id_fk;
                    $deposit->isFromAutoProcess     = 0;
                    $deposit->createdAt             = Carbon::now();
                    $deposit->save();
                }                   
               

                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Account updated successfully.'
                );
                DB::beginTransaction();
                return response::json($data);                
            }}
            catch(\Exception $e){
					DB::rollback();
					$data = array(
						'responseTitle'  =>  'Warning!',
						'responseText'   =>  'Something went wrong. Please try again.'
	 				);
	 				return response::json($data);
				}
            
        }        
      

        
        public function deleteAccount(Request $req) {
            $product = MfnSavingsAccount::find($req->id);
            $product->softDel = 1;
            $product->save();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Your selected product deleted successfully.'
            );

            return response()->json($data);
        }


        public function getBankList(){
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

            $resultedBankList = DB::table('acc_account_ledger')
                                    ->whereIn('id',$bankList)
                                    ->where('id','!=',350)
                                    ->select('name','id','code')
                                    ->get();

            return $resultedBankList;
        }



        
    }