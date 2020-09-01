<?php

    namespace App\Http\Controllers\microfin\process;

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
    use App\microfin\savings\MfnSavingsAccount;
    use App\microfin\member\MfnMemberInformation;
    use App\microfin\savings\MfnSavingsDeposit;
    use App\microfin\savings\MfnSavingsWithdraw;
    use App\microfin\samity\MfnSamity;
    use App\microfin\loan\MfnLoanCollection;
    use App\microfin\loan\MfnLoanSchedule;
    use App\microfin\process\AuthorizationInfo;
    use App\microfin\process\MfnAutoProcess;
    use Auth;
    use App\Traits\GetSoftwareDate;

    class MfnAutoProcessController extends Controller {
        use CreateForm;
        use GetSoftwareDate;

        private $TCN;
        
        public function __construct() {

            $this->TCN = array(
                array('SL#', 70),
                array('Samity Name', 0),
                array('Field Officer', 0),
                array('Action', 80)
            );
        }

        public function index() {

            $softwareDate = GetSoftwareDate::getSoftwareDate();

            $today = Carbon::parse($softwareDate);
            $weekDayNumber = $today->dayOfWeek == 6 ? 1: $today->dayOfWeek+2;
            $dayNumber = $today->day;

            $userBranchId = Auth::user()->branchId;

            $todaySamity = DB::table('mfn_samity')
                                ->where('branchId',$userBranchId)
                                 ->where(function ($query) use ($weekDayNumber,$dayNumber) {
                                        $query->where('samityDayId', $weekDayNumber)
                                              ->orWhere('fixedDate',$dayNumber);
                                    })
                                ->where('openingDate','<=',$softwareDate)
                                ->select('id','code','name','fieldOfficerId')
                                ->get();           

            $todayString = $today->copy()->format('Y-m-d');
            
            $TCN = $this->TCN;
            $damageData = array(
                'TCN'           =>  $TCN,
                'todaySamity'   =>  $todaySamity,
                'todayString'   =>  $todayString,
                'softwareDate'  =>  $softwareDate
            );

            return view('microfin.process.autoProcess.viewAutoProcess',['damageData'=>$damageData]);
        }

        public function viewSamityAutoProcess(Request $req){

            $samity = MfnSamity::where('id',$req->samityId)->first();            

            $softwareDate = GetSoftwareDate::getSoftwareDate();

            $memberIdsFromSavingsAccounts = DB::table('mfn_savings_account')
                                                ->where('softDel',0)
                                                ->where('status',1)
                                                ->where('accountOpeningDate','<=',$softwareDate)
                                                ->where('samityIdFk',$req->samityId)
                                                ->pluck('memberIdFk')
                                                ->toArray();

            $memberIdsFromLoanAccounts = DB::table('mfn_loan')
                                            ->where('softDel',0)
                                            ->where('status',1)
                                            ->where('disbursementDate','<=',$softwareDate)
                                            ->where('samityIdFk',$req->samityId)
                                            ->pluck('memberIdFk')
                                            ->toArray();

            $memberIdsHavingAccounts = array_merge($memberIdsFromSavingsAccounts,$memberIdsFromLoanAccounts);
            $memberIdsHavingAccounts = array_unique($memberIdsHavingAccounts);

            $members = MfnMemberInformation::active()->where('samityId',$req->samityId)->where('admissionDate','<=',$softwareDate)->whereIn('id',$memberIdsHavingAccounts)->get();            

            $today = Carbon::parse($softwareDate);

            $dayNames = array(
                '1'=>'Saturday',
                '2'=>'Sunday',
                '3'=>'Monday',
                '4'=>'Tuesday',
                '5'=>'Wednesday',
                '6'=>'Thursday',
                '7'=>'Fridayday'
            );

            $samityDayName = "";
            if($samity->samityDayId>0){
                $samityDayName = $dayNames[$samity->samityDayId];
            }
            elseif($samity->fixedDate>0){
                $samityDay = $today;
                $samityDay->day = $samity->fixedDate;
                $samityDayName = $samity->fixedDate;//$samityDay->format('l');
            }
            
            $damageData = array(
                'samity'        =>  $samity,
                'samityDayName' =>  $samityDayName,
                'members'       =>  $members,
                'softwareDate'  =>  $softwareDate
            );

            // check is data of today of yhis samity at mfn_auto_process_tra_authorization_info table, if exits in table then it means that transaction alreadly exits and all transaction will be updated.
            $hasTransactionToday = (int) DB::table('mfn_auto_process_tra_authorization_info')->where('samityIdFk',$samity->id)->where('date',$softwareDate)->value('id');
            if ($hasTransactionToday>0) { 
               
                $memberPresentsInfo = json_decode(MfnAutoProcess::where('date',$softwareDate)->where('samityIdFk',$samity->id)->value('memberAttendence'));
                if (count($memberPresentsInfo)<1) {
                    $memberPresentsInfo = [];
                }

                $damageData = $damageData + array(                              
                                'memberPresentsInfo'    => $memberPresentsInfo
                            );

                return view('microfin.process.autoProcess.editAutoProcessSamity',$damageData);
            }
            else{                
                return view('microfin.process.autoProcess.viewAutoProcessSamity',$damageData);
            }
                
        }


        public function storeCollectionNDeposit(Request $req) {
            // here branch and samity are got by the savings account id
            $savings = DB::table('mfn_savings_account')->where('id',$req->savingsAccId[0])->select('branchIdFk','samityIdFk')->first();

            $softwareDate = GetSoftwareDate::getSoftwareDate(); 
            $errorFlag = 0;  
            $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');         
            
            // store savings deposit
            foreach ($req->savingsAccId as $key => $savingsAcc) {

               /* if ($req->savingsAmount[$key]<=0) {
                    continue;
                }*/

                $balanceBeforeDeposit = DB::table('mfn_savings_deposit')->where('softDel',0)->where('accountIdFk',$savingsAcc)->where('depositDate','<',$softwareDate)->sum('amount');

                $account = MfnSavingsAccount::where('id',$savingsAcc)->select('branchIdFk','samityIdFk','savingsProductIdFk')->first();

                $primaryProductId = DB::table('mfn_member_information')->where('id',$req->savingsMemberId[$key])->value('primaryProductId');

                $depositId = MfnSavingsDeposit::where('depositDate',$softwareDate)->where('accountIdFk',$savingsAcc)->where('isFromAutoProcess',1)->value('id');

                if ($depositId>0) {
                    $deposit = MfnSavingsDeposit::find($depositId);
                }
                else{
                     $deposit = new MfnSavingsDeposit;
                }

                /*if ($account->savingsProductIdFk==0 || $primaryProductId==0) {
                    continue;
                    $errorFlag = 1;
                }*/

                $deposit->memberIdFk            = $req->savingsMemberId[$key];
                $deposit->branchIdFk            = $savings->branchIdFk;
                $deposit->samityIdFk            = $savings->samityIdFk;
                $deposit->accountIdFk           = $savingsAcc;
                $deposit->productIdFk           = $account->savingsProductIdFk;
                $deposit->primaryProductIdFk    = $primaryProductId;
                $deposit->amount                = $req->savingsAmount[$key];
                $deposit->balanceBeforeDeposit  = $balanceBeforeDeposit;
                $deposit->depositDate           = Carbon::parse($softwareDate);
                $deposit->paymentType           = 'Cash';
                $deposit->ledgerIdFk            = $ledgerId; // Cash In Hand ledger id
                $deposit->chequeNumber          = '';
                $deposit->entryByEmployeeIdFk   = Auth::user()->emp_id_fk;
                $deposit->isFromAutoProcess     = 1;
                $deposit->createdAt             = Carbon::now();
                $deposit->softDel               = 0;
                $deposit->save();
            }

            // store loan collection
            if (is_array($req->loanAccId)) {
                
            foreach ($req->loanAccId as $key => $loanAcc) {

               /* if ($req->loanAmount[$key]<=0) {
                    continue;
                }*/

                $loan = DB::table('mfn_loan')->where('id',$loanAcc)->first();

                $principalAmount = round($req->loanAmount[$key]/(float) $loan->interestRateIndex,2);
                $interestAmount = round($req->loanAmount[$key] - $principalAmount,2);
                $pastCollectionAmount = DB::table('mfn_loan_collection')->where('loanIdFk',$loan->id)->sum('amount');

                // get the installment numbers which is coverd by the software date
                $installmentNo = DB::table('mfn_loan_schedule')->where('softDel',0)->where('loanIdFk',$loan->id)->where('scheduleDate','<=',$softwareDate)/*->where('newScheduleDate','<=',$softwareDate)*/->where('isCompleted',0)->orderBy('installmentSl')->pluck('installmentSl')->toArray();
                if (count($installmentNo)<1) {
                    $installmentNo = DB::table('mfn_loan_schedule')->where('softDel',0)->where('loanIdFk',$loan->id)->where('isCompleted',0)->min('installmentSl');
                    $installmentNo = [$installmentNo];
                }

                $primaryProductId =  DB::table('mfn_member_information')
                                            ->where('id',$loan->memberIdFk)
                                            ->value('primaryProductId');

                $loanCollectionId = MfnLoanCollection::where('collectionDate',$softwareDate)->where('loanIdFk',$loanAcc)->where('isFromAutoProcess',1)->value('id');
                if ($loanCollectionId>0) {
                     $loanCollection = MfnLoanCollection::find($loanCollectionId);
                }
                else{
                    $loanCollection = new MfnLoanCollection;
                }

                /*if($account->savingsProductIdFk==0 || $primaryProductId==0) {
                    continue;
                    $errorFlag = 1;
                }*/

                $loanCollection->loanIdFk               = $loanAcc;
                $loanCollection->productIdFk            = $loan->productIdFk;
                $loanCollection->primaryProductIdFk     = $loan->primaryProductIdFk;
                $loanCollection->loanTypeId             = $loan->loanTypeId;
                $loanCollection->memberIdFk             = $loan->memberIdFk;
                $loanCollection->branchIdFk             = $loan->branchIdFk;
                $loanCollection->samityIdFk             = $loan->samityIdFk;
                $loanCollection->collectionDate         = Carbon::parse($softwareDate);
                $loanCollection->amount                 = $req->loanAmount[$key];
                $loanCollection->principalAmount        = $principalAmount;
                $loanCollection->interestAmount         = $interestAmount;
                $loanCollection->paymentType            = 'Cash';
                $loanCollection->ledgerIdFk             = $ledgerId; // Cash In Hand ledger id
                $loanCollection->chequeNumber           = '';
                $loanCollection->installmentNo          = implode(',',$installmentNo);
                $loanCollection->isFromAutoProcess      = 1;
                $loanCollection->entryByEmployeeIdFk    = Auth::user()->emp_id_fk;
                $loanCollection->createdAt              = Carbon::now();
                $loanCollection->softDel                = 0;
                $loanCollection->save();
                

                // mark the completed installments
                $totalCollectionAmount = MfnLoanCollection::active()->where('loanIdFk',$loan->id)->sum('amount');

                $shedules = MfnLoanSchedule::active()->where('loanIdFk',$loan->id)->get();

                foreach ($shedules as $key => $shedule) {
                    if ($totalCollectionAmount>=$shedule->installmentAmount) {
                        $shedule->isCompleted = 1;
                        $shedule->isPartiallyPaid = 0;
                        $shedule->partiallyPaidAmount = 0;
                        $shedule->save();
                    }
                    elseif ($totalCollectionAmount>0) {
                        $shedule->isCompleted = 0;
                        $shedule->isPartiallyPaid = 1;
                        $shedule->partiallyPaidAmount = $totalCollectionAmount;
                        $shedule->save();
                    }
                    else{
                        $shedule->isCompleted = 0;
                        $shedule->isPartiallyPaid = 0;
                        $shedule->partiallyPaidAmount = 0;
                        $shedule->save();
                    }

                    $totalCollectionAmount = $totalCollectionAmount - $shedule->installmentAmount;
                }
            }
            }

            // store authorization information
            $authorizationInfoId = (int) DB::table('mfn_auto_process_tra_authorization_info')
                                        ->where('date',$softwareDate)
                                        ->where('samityIdFk',$savings->samityIdFk)
                                        ->where('branchIdFk',$savings->branchIdFk)
                                        ->value('id');
            if ($authorizationInfoId>0) {
                $authorizationInfo = AuthorizationInfo::find($authorizationInfoId);
            }
            else{
                $authorizationInfo = new AuthorizationInfo;
            }

            $authorizationInfo->date                = Carbon::parse($softwareDate);
            $authorizationInfo->samityIdFk          = $savings->samityIdFk;
            $authorizationInfo->branchIdFk          = $savings->branchIdFk;
            $authorizationInfo->isAuthorized        = 0;
            $authorizationInfo->isReUnauthorized    = 0;
            $authorizationInfo->createdAt           = Carbon::now();
            $authorizationInfo->save();

            // store auto process info
            $mfnAutoProcessId = (int) DB::table('mfn_auto_process_info')
                                        ->where('date',$softwareDate)
                                        ->where('samityIdFk',$savings->samityIdFk)
                                        ->where('branchIdFk',$savings->branchIdFk)
                                        ->value('id');

            if ($mfnAutoProcessId>0) {
                $autoProcess = MfnAutoProcess::find($mfnAutoProcessId);
            }
            else{
                $autoProcess = new MfnAutoProcess;
            }
            
            $autoProcess->date                  = Carbon::parse($softwareDate);
            $autoProcess->samityIdFk            = $savings->samityIdFk;
            $autoProcess->branchIdFk            = $savings->branchIdFk;
            $autoProcess->totalCollectionAmount = $req->loanTotalAmount;
            $autoProcess->totalDepositAmount    = $req->savingsTotalAmount;
            $autoProcess->memberAttendence      = json_encode(array_map(null,$req->presentMemberId,$req->isPresentText));
            $autoProcess->createdAt             = Carbon::now();
            $autoProcess->save();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data saved successfully.'
            );
            
            return response::json($data);
        }

        public function updateCollectionNDeposit(Request $req) {
            // here branch is got by the savings account id
            $savings = DB::table('mfn_savings_account')->where('id',$req->savingsAccId[0])->select('branchIdFk','samityIdFk')->first();
            $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id'); 

            $softwareDate = GetSoftwareDate::getSoftwareDate();            
            
            // store savings deposit
            foreach ($req->savingsAccId as $key => $savingsAcc) {

               /* if ($req->savingsAmount[$key]<=0) {
                    continue;
                }*/

                $account = MfnSavingsAccount::where('id',$savingsAcc)->select('savingsProductIdFk')->first();

                $balanceBeforeDeposit = DB::table('mfn_savings_deposit')->where('softDel',0)->where('accountIdFk',$savingsAcc)->where('depositDate','<',$softwareDate)->sum('amount');

                $depositId = MfnSavingsDeposit::where('depositDate',$softwareDate)->where('accountIdFk',$savingsAcc)->where('isFromAutoProcess',1)->value('id');

                if ($depositId>0) {
                    $deposit = MfnSavingsDeposit::find($depositId);
                }
                else{
                     $deposit = new MfnSavingsDeposit;
                }
                $primaryProductId = DB::table('mfn_member_information')->where('id',$req->savingsMemberId[$key])->value('primaryProductId');
                $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');
                
                $deposit->memberIdFk            = $req->savingsMemberId[$key];
                $deposit->branchIdFk            = $savings->branchIdFk;
                $deposit->productIdFk           = $account->savingsProductIdFk;
                $deposit->primaryProductIdFk    = $primaryProductId;
                $deposit->samityIdFk            = $savings->samityIdFk;
                $deposit->accountIdFk           = $savingsAcc;
                $deposit->amount                = $req->savingsAmount[$key];
                $deposit->balanceBeforeDeposit  = $balanceBeforeDeposit;
                $deposit->depositDate           = Carbon::parse($softwareDate);
                $deposit->paymentType           = 'Cash';
                $deposit->ledgerIdFk            = $ledgerId; // Cash In Hand ledger id
                $deposit->chequeNumber          = '';
                $deposit->entryByEmployeeIdFk   = Auth::user()->emp_id_fk;
                $deposit->isFromAutoProcess     = 1;
                $deposit->createdAt             = Carbon::now();
                $deposit->softDel               = 0;
                $deposit->save();

            }

            // store loan collection
            if (is_array($req->loanAccId)) {
                
            foreach ($req->loanAccId as $key => $loanAcc) {

               /* if ($req->loanAmount[$key]<=0) {
                    continue;
                }*/

                $loan = DB::table('mfn_loan')->where('id',$loanAcc)->first();

                $principalAmount = round($req->loanAmount[$key]/(float) $loan->interestRateIndex,2);
                $interestAmount = round($req->loanAmount[$key] - $principalAmount,2);
                $pastCollectionAmount = DB::table('mfn_loan_collection')->where('loanIdFk',$loan->id)->sum('amount');

                // get the installment numbers which is coverd by the software date
               /* $installmentNo = DB::table('mfn_loan_schedule')->where('softDel',0)->where('loanIdFk',$loan->id)->where('scheduleDate','<=',$softwareDate)->where('newScheduleDate','<=',$softwareDate)->where('isCompleted',0)->orderBy('installmentSl')->pluck('installmentSl')->toArray();
                if (count($installmentNo)<1) {
                    $installmentNo = DB::table('mfn_loan_schedule')->where('softDel',0)->where('loanIdFk',$loan->id)->where('isCompleted',0)->min('installmentSl');
                    $installmentNo = [$installmentNo];
                }*/

                /*$primaryProductId =  DB::table('mfn_member_information')
                                            ->where('id',$loan->memberIdFk)
                                            ->value('primaryProductId');*/

                $loanCollectionId = MfnLoanCollection::where('collectionDate',$softwareDate)->where('loanIdFk',$loanAcc)->where('isFromAutoProcess',1)->value('id');
                if ($loanCollectionId>0) {
                     $loanCollection = MfnLoanCollection::find($loanCollectionId);
                }
                else{
                    $loanCollection = new MfnLoanCollection;
                }
                
                $loanCollection->loanIdFk               = $loanAcc;
                $loanCollection->productIdFk            = $loan->productIdFk;
                $loanCollection->primaryProductIdFk     = $loan->primaryProductIdFk;
                $loanCollection->loanTypeId             = $loan->loanTypeId;
                $loanCollection->memberIdFk             = $loan->memberIdFk;
                $loanCollection->branchIdFk             = $loan->branchIdFk;
                $loanCollection->samityIdFk             = $loan->samityIdFk;
                $loanCollection->collectionDate         = Carbon::parse($softwareDate);
                $loanCollection->amount                 = $req->loanAmount[$key];
                $loanCollection->principalAmount        = $principalAmount;
                $loanCollection->interestAmount         = $interestAmount;
                $loanCollection->paymentType            = 'Cash';
                $loanCollection->ledgerIdFk             = $ledgerId; // Cash In Hand ledger id
                $loanCollection->chequeNumber           = '';
                //$loanCollection->installmentNo          = implode(',',$installmentNo);
                $loanCollection->isFromAutoProcess      = 1;
                $loanCollection->entryByEmployeeIdFk    = Auth::user()->emp_id_fk;
                $loanCollection->createdAt              = Carbon::now();
                $loanCollection->softDel                = 0;
                $loanCollection->save();
                

                // mark the completed installments
                $totalCollectionAmount = MfnLoanCollection::active()->where('loanIdFk',$loan->id)->sum('amount');

                $shedules = MfnLoanSchedule::active()->where('loanIdFk',$loan->id)->get();

                foreach ($shedules as $key => $shedule) {
                    if ($totalCollectionAmount>=$shedule->installmentAmount) {
                        $shedule->isCompleted = 1;
                        $shedule->isPartiallyPaid = 0;
                        $shedule->partiallyPaidAmount = 0;
                        $shedule->save();
                    }
                    elseif ($totalCollectionAmount>0) {
                        $shedule->isCompleted = 0;
                        $shedule->isPartiallyPaid = 1;
                        $shedule->partiallyPaidAmount = $totalCollectionAmount;
                        $shedule->save();
                    }
                    else{
                        $shedule->isCompleted = 0;
                        $shedule->isPartiallyPaid = 0;
                        $shedule->partiallyPaidAmount = 0;
                        $shedule->save();
                    }

                    $totalCollectionAmount = $totalCollectionAmount - $shedule->installmentAmount;
                }
            }
            }

            // store authorization information
            $authorizationInfoId = AuthorizationInfo::where('date',$softwareDate)->where('samityIdFk',$savings->samityIdFk)->max('id');
            if ($authorizationInfoId>0) {
                $authorizationInfo = AuthorizationInfo::find($authorizationInfoId);
            }
            else{
                $authorizationInfo = new AuthorizationInfo;
            }
            /*$authorizationInfo = AuthorizationInfo::where('date',$softwareDate)->where('samityIdFk',$savings->samityIdFk)->first();*/
            $authorizationInfo->date                = Carbon::parse($softwareDate);
            $authorizationInfo->samityIdFk          = $savings->samityIdFk;
            $authorizationInfo->branchIdFk          = $savings->branchIdFk;
            $authorizationInfo->isAuthorized        = 0;
            $authorizationInfo->isReUnauthorized    = 0;
            $authorizationInfo->createdAt           = Carbon::now();
            $authorizationInfo->save();

            // store auto process info
            $autoProcessId = MfnAutoProcess::where('date',$softwareDate)->where('samityIdFk',$savings->samityIdFk)->max('id');
            if ($autoProcessId>0) {
                $autoProcess = MfnAutoProcess::find($autoProcessId);
            }
            else{
                $autoProcess = new MfnAutoProcess;
            }
            /*$autoProcess = MfnAutoProcess::where('date',$softwareDate)->where('samityIdFk',$savings->samityIdFk)->first();*/
            $autoProcess->date                  = Carbon::parse($softwareDate);
            $autoProcess->samityIdFk            = $savings->samityIdFk;
            $autoProcess->branchIdFk            = $savings->branchIdFk;
            $autoProcess->totalCollectionAmount = $req->loanTotalAmount;
            $autoProcess->totalDepositAmount    = $req->savingsTotalAmount;
            $autoProcess->memberAttendence      = json_encode(array_map(null,$req->presentMemberId,$req->isPresentText));
            $autoProcess->createdAt             = Carbon::now();
            $autoProcess->save();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data updated successfully.'
            );
            
            return response::json($data);
        }
    }

        