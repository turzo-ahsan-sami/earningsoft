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
    use App\microfin\savings\MfnSavingsClosing;
    use App\microfin\savings\MfnSavingsWithdraw;
    use Auth;
    use App\Http\Controllers\microfin\MicroFin;

    class MfnSavingsClosingController extends Controller {
        use CreateForm;
        use GetSoftwareDate;

        private $TCN;
        
        public function __construct() {

            $this->TCN = array(
                array('SL#', 70),
                array('Member Name', 0),
                array('Member Code', 0),
                array('Savings Code', 0),
                array('Closing Date', 0),
                array('Mode of Payment', 0),
                array('Amount', 0),
                array('Entry By', 0),
                array('Status', 0),
                array('Action', 0)
            );
        }

        public function index() {

            $softDate = GetSoftwareDate::getSoftwareDate();
            $softwareDate = GetSoftwareDate::getSoftwareDateInFormat();

            $userBranchId   = Auth::user()->branchId;

            $closings = MfnSavingsClosing::where('softDel',0);
            // $withdraws = DB::table('mfn_savings_withdraw')->where('softDel',0);

            if ($userBranchId!=1) {
                $savingsAccIds = DB::table('mfn_savings_account')->where('softDel',0)->where('branchIdFk',$userBranchId)->pluck('id')->toArray();
                $closings = $closings->whereIn('accountIdFk',$savingsAccIds);
                // $withdraws = $withdraws->where('branchIdFk',$userBranchId);
                $closings = $closings->where('closingDate','<=',$softDate)->orderBy('closingDate','desc')->paginate(15);
            }
            else{
                $closings = $closings->orderBy('closingDate','desc')->paginate(30);
            }
            // $withdraws = $withdraws->select('id','accountIdFk','withdrawDate','isAuthorized')->get();

            $withdraws = DB::table('mfn_savings_withdraw')->where('softDel',0)->whereIn('accountIdFk',$closings->pluck('accountIdFk'))->select('id','accountIdFk','withdrawDate','isAuthorized')->get();

            $bankList = $this->getBankList();

            $TCN = $this->TCN;
            $damageData = array(
                'TCN'           =>  $TCN,
                'closings'      =>  $closings,
                'withdraws'     =>  $withdraws,
                'bankList'      =>  $bankList,
                'softwareDate'  =>  $softwareDate,
                'softDate'      =>  $softDate
            );

            return view('microfin/savings/savingsClosing/viewSavingsClosing',$damageData);
        }

        public function addClosing() {

            $bankList = $this->getBankList();
            
            $softwareDate = GetSoftwareDate::getSoftwareDateInFormat(); 
            $softDate = GetSoftwareDate::getSoftwareDate();
            $data = array(
                'bankList'      => $bankList,
                'softwareDate'  => $softwareDate,
                'softDate'      => $softDate
            );
            return view('microfin.savings.savingsClosing.addSavingsClosing',$data);
        }

        
        public function storeClosing(Request $req) {

            $rules = array(
                'member'            =>  'required',
                'closingDate'       =>  'required',
                'savingsCode'       =>  'required'
            );

            if ($req->paymentMode=='Bank') {
                $rules = $rules + array(
                    'bank'          =>  'required',
                    'chequeNumber'  =>  'required'
                );
            }

            $attributesNames = array(
                'member'            =>  'Member',
                'closingDate'       =>  'Closing Date',
                'savingsCode'       =>  'Savings Code',
                'bank'              =>  'Bank',
                'chequeNumber'      =>  'Cheque Number'
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {

                $closingDate = Carbon::parse($req->closingDate)->format('Y-m-d');
                $savingsAccount = DB::table('mfn_savings_account')->where('id',$req->savingsCode)->select('id','savingsProductIdFk','accountOpeningDate')->first();

                if ($closingDate<$savingsAccount->accountOpeningDate) {
                    $data = array(
                        'responseTitle' =>  'Warning!',
                        'responseText'  =>  'You entered date before account opening date.'
                    );
                    return response::json($data);
                }

                


                // Check Is tere any today or future transaction, if yes then closing can't be done
                $depositTransactions = (int) DB::table('mfn_savings_deposit')->where('softDel',0)->where('amount','>',0)->where('accountIdFk',$req->savingsCode)->where('depositDate','>=',$closingDate)->value('id');
                $withdrawTransactions = (int) DB::table('mfn_savings_withdraw')->where('softDel',0)->where('amount','>',0)->where('accountIdFk',$req->savingsCode)->where('withdrawDate','>=',$closingDate)->value('id');
                $toDayOrFutureTransactions = max($depositTransactions,$withdrawTransactions);
                
                if ($toDayOrFutureTransactions>0) {
                    $data = array(
                        'responseTitle' =>  'Warning!',
                        'responseText'  =>  'Transaction exits later to this date.'
                    );
                    return response::json($data);
                }
                
                // Store Data
                
                if ($req->paymentMode=="Cash") {
                    // Cash In Hand ledger
                    $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');
                }
                else{
                     $ledgerId = $req->bank;
                }

                // get the product
                
                $savingsProduct = DB::table('mfn_saving_product')->where('id',$savingsAccount->savingsProductIdFk)->select('depositTypeIdFk')->first();

                if ($req->totalSavingInterestAmount == '') {
                    $req->totalSavingInterestAmount = 0;
                }

                if ($savingsProduct->depositTypeIdFk==4) {
                    $closingAmount  = $req->depositAmount + $req->totalSavingInterestAmount;
                    $payableAmount = $req->payableAmount;
                }
                elseif ($savingsProduct->depositTypeIdFk==1 || $savingsProduct->depositTypeIdFk==2) {
                    // $closingAmount = $req->actualBalance + $req->totalSavingInterestAmount;
                    $closingAmount = floatval(str_replace(',', '', $req->actualBalance)) + floatval(str_replace(',', '', $req->totalSavingInterestAmount));
                    // $payableAmount = $req->actualBalance + $req->totalSavingInterestAmount;
                    $payableAmount = $closingAmount;
                }

                $closingId = (int) DB::table('mfn_savings_closing')->where('softDel',0)->where('accountIdFk',$req->savingsCode)->value('id');
                if ($closingId>0) {
                    $closing = MfnSavingsClosing::find($closingId);
                }
                else{
                    $closing = new MfnSavingsClosing;
                }

                $closing->memberIdFk            = $req->memberId;
                //value of saving code is the account id
                $closing->accountIdFk           = $req->savingsCode;
                $closing->depositAmount         = floatval(str_replace(',', '', $req->depositAmount));
                $closing->payableAmount         = $payableAmount;
                $closing->totalSavingInterest   = floatval(str_replace(',', '', $req->totalSavingInterestAmount));
                $closing->closingAmount         = $closingAmount;
                $closing->closingDate           = Carbon::parse($req->closingDate);
                $closing->paymentType           = $req->paymentMode;
                $closing->ledgerIdFk            = $ledgerId;
                $closing->chequeNumber          = $req->chequeNumber;
                $closing->entryByEmployeeIdFk   = Auth::user()->emp_id_fk;
                $closing->createdAt             = Carbon::now();
                $closing->save();

                $account = DB::table('mfn_savings_account')->where('id',$req->savingsCode)->select('savingsProductIdFk')->first();
                $primaryProductId = DB::table('mfn_member_information')->where('id',$req->memberId)->value('primaryProductId');

                // make a withdraw of the outstanding amount
                $member = DB::table('mfn_member_information')->where('id',$req->memberId)->first();

                $closingDate = Carbon::parse($req->closingDate)->format('Y-m-d');
                $withdrawId = DB::table('mfn_savings_withdraw')->where('softDel',0)->where('accountIdFk',$req->savingsCode)->where('isFromClosing',1)->value('id');
                if ($withdrawId>0) {
                    $withdraw = MfnSavingsWithdraw::find($withdrawId);
                }
                else{
                    $withdraw = new MfnSavingsWithdraw;                    
                }

                $withdraw->memberIdFk               = $member->id;
                $withdraw->branchIdFk               = $member->branchId;
                $withdraw->samityIdFk               = $member->samityId;
                // value of saving code is the account id
                $withdraw->accountIdFk              = $req->savingsCode;
                $withdraw->productIdFk              = $account->savingsProductIdFk;
                $withdraw->primaryProductIdFk       = $primaryProductId;
                $withdraw->amount                   = (float) str_replace(',','',$req->actualBalance) + (float) $req->totalSavingInterestAmount;
                $withdraw->balanceBeforeWithdraw    = (float) str_replace(',','',$req->actualBalance);
                $withdraw->WithdrawDate             = Carbon::parse($req->closingDate);
                $withdraw->paymentType              = $req->paymentMode;
                $withdraw->ledgerIdFk               = $ledgerId;
                $withdraw->chequeNumber             = $req->chequeNumber;
                $withdraw->isFromClosing            = 1;
                $withdraw->entryByEmployeeIdFk      = Auth::user()->emp_id_fk;
                $withdraw->createdAt                = Carbon::now();
                $withdraw->save();

                // Make the account Inactive
                DB::table('mfn_savings_account')->where('id',$closing->accountIdFk)->update(['status'=>0]);

                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Data inserted successfully.'
                );

                return response::json($data);                
            }            
        }

        public function updateClosing(Request $req) {

            $closing = MfnSavingsClosing::find($req->closingId);
                
            if ($req->paymentMode=="Cash") {
                // Cash In Hand ledger id
                $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');
            }
            else{
                $ledgerId = $req->bank;
            }

             // get the product
            $savingsAccount = DB::table('mfn_savings_account')->where('id',$closing->accountIdFk)->select('savingsProductIdFk')->first();
            $savingsProduct = DB::table('mfn_saving_product')->where('id',$savingsAccount->savingsProductIdFk)->select('depositTypeIdFk')->first();

            if ($req->totalSavingInterestAmount == '') {
                $req->totalSavingInterestAmount = 0;
            }

            if ($savingsProduct->depositTypeIdFk==4) {
                // $closingAmount  = $req->depositAmount + $req->totalSavingInterestAmount;
                $closingAmount  = floatval(str_replace(',', '', $req->depositAmount)) + floatval(str_replace(',', '', $req->totalSavingInterestAmount));
            }
            elseif ($savingsProduct->depositTypeIdFk==1 || $savingsProduct->depositTypeIdFk==2) {
                // $closingAmount = $req->actualBalance + $req->totalSavingInterestAmount;
                $closingAmount = floatval(str_replace(',', '', $req->actualBalance)) + floatval(str_replace(',', '', $req->totalSavingInterestAmount));
            }
            
            $closing->totalSavingInterest   = floatval(str_replace(',', '', $req->totalSavingInterestAmount));
            $closing->closingAmount         = $closingAmount;
            $closing->paymentType           = $req->paymentMode;
            $closing->ledgerIdFk            = $ledgerId;
            $closing->chequeNumber          = $req->chequeNumber;               
            $closing->save();

            // update withdraw    
            
            $withdraw = MfnSavingsWithdraw::where('accountIdFk',$closing->accountIdFk)->where('softDel',0)->where('isFromClosing',1)->first();
            $withdraw->amount                   = (float) str_replace(',','',$req->actualBalance) + (float) $req->totalSavingInterestAmount;
            $withdraw->balanceBeforeWithdraw    = (float) str_replace(',','',$req->actualBalance);
            $withdraw->paymentType              = $req->paymentMode;
            $withdraw->ledgerIdFk               = $ledgerId;
            $withdraw->chequeNumber             = $req->chequeNumber;
            $withdraw->save();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Closing updated successfully.'
            );

            return response::json($data);                
                       
        }

        public function deleteClosing(Request $req) {
            $closing = MfnSavingsClosing::find($req->id);
            $withdraw = MfnSavingsWithdraw::where('withdrawDate',$closing->getOriginal()['closingDate'])->where('accountIdFk',$closing->accountIdFk)->first();            
            $withdraw->delete();
            $closing->delete();

            // Make the Account active
            DB::table('mfn_savings_account')->where('id',$closing->accountIdFk)->update(['status'=>1]);

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Closing deleted successfully.'
            );
            
            return response::json($data);   
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

        