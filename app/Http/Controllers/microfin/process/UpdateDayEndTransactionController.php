<?php

    namespace App\Http\Controllers\microfin\process;

    use Illuminate\Http\Request;
    use App\Http\Requests;
    use Validator;
    use Response;
    use DB;
    use Carbon\Carbon;
    use Auth;
    use App\microfin\savings\MfnSavingsDeposit;
    use App\microfin\loan\MfnLoanCollection;
    use App\Http\Controllers\Controller;
    use App\microfin\member\MfnMemberInformation;

    class UpdateDayEndTransactionController extends Controller {

        public function index(){
            $branchIds = [84,85,86,87,88,89,90,91,92,93];
            $startDateString = '2017-12-03';

            foreach ($branchIds as $branchId) {
                $startDate = Carbon::parse($startDateString);
                $endDateString = DB::table('mfn_savings_deposit')->where('softDel',0)->where('branchIdFk',$branchId)->where('isFromAutoProcess',1)->max('depositDate');
                $endDate = Carbon::parse($endDateString);
                while ($endDate->gte($startDate)) {
                    $isTodayHoliday = app('App\Http\Controllers\microfin\process\DayEndProcessController')->isHoliday($startDate->format('Y-m-d'),$branchId);
                    if ($isTodayHoliday!=1) {
                        $this->updateAutoProcessTransaction($branchId,$startDate->format('Y-m-d'));
                    }
                    $startDate->addDay();
                }
                echo $branchId." updated<br>";
            }

            
        }

        public function updateAutoProcessTransaction($branchId,$softwareDate){            

            $traExitsFlag = 1;

            $entryByEmployeeIdFk =  DB::table('mfn_savings_deposit')->where('branchIdFk',$branchId)->where('depositDate',$softwareDate)->where('isFromAutoProcess',1)->value('entryByEmployeeIdFk');

            $softDate = Carbon::parse($softwareDate);
            $weekDayNumber = $softDate->dayOfWeek == 6 ? 1: $softDate->dayOfWeek+2;
            $dayNumber = $softDate->day;

            $todaySamities = DB::table('mfn_samity')
                                ->where('branchId',$branchId)
                                 ->where(function ($query) use ($weekDayNumber,$dayNumber) {
                                        $query->where('samityDayId', $weekDayNumber)
                                              ->orWhere('fixedDate',$dayNumber);
                                    })
                                ->where('openingDate','<=',$softwareDate)
                                ->select('id')
                                ->get();

            foreach ($todaySamities as $key => $todaySamity) {
                $members = MfnMemberInformation::active()->where('samityId',$todaySamity->id)->where('admissionDate','<=',$softwareDate)->get();

                foreach ($members as $member) {
                    $savingsAccounts = DB::table('mfn_savings_account')->where('softDel',0)->where('status',1)->where('memberIdFk',$member->id)->where('depositTypeIdFk','!=',4)->where('accountOpeningDate','<=',$softwareDate)->get();

                    $loanIdsHavingSheduleToday = DB::table('mfn_loan_schedule')
                                                        ->where('softDel',0)
                                                        ->where('scheduleDate',$softwareDate)     
                                                        ->pluck('loanIdFk')->toArray();                        

                    $loanAccounts = DB::table('mfn_loan')                                                
                                            ->where('softDel',0)
                                            ->where('status',1)
                                            ->where('disbursementDate','<=',$softwareDate)
                                            ->where('memberIdFk',$member->id)
                                            ->whereIn('id',$loanIdsHavingSheduleToday)
                                            ->get();

                    $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');

                    foreach ($savingsAccounts as  $savingsAccount) {
                        $isTraExits = (int) DB::table('mfn_savings_deposit')->where('softDel',0)->where('accountIdFk',$savingsAccount->id)->where('depositDate',$softwareDate)->value('id');
                        if ($isTraExits<1) {

                            ////////////////
                            $primaryProductId = DB::table('mfn_member_information')->where('id',$savingsAccount->memberIdFk)->value('primaryProductId');


                            $deposit = new MfnSavingsDeposit;
                            $deposit->memberIdFk            = $savingsAccount->memberIdFk;
                            $deposit->branchIdFk            = $savingsAccount->branchIdFk;
                            $deposit->samityIdFk            = $savingsAccount->samityIdFk;
                            $deposit->accountIdFk           = $savingsAccount->id;
                            $deposit->productIdFk           = $savingsAccount->savingsProductIdFk;
                            $deposit->primaryProductIdFk    = $primaryProductId;
                            $deposit->amount                = 0;
                            $deposit->balanceBeforeDeposit  = 0;
                            $deposit->depositDate           = Carbon::parse($softwareDate);
                            $deposit->paymentType           = 'Cash';
                            $deposit->ledgerIdFk            = $ledgerId;
                            $deposit->chequeNumber          = '';
                            $deposit->entryByEmployeeIdFk   = $entryByEmployeeIdFk;//Auth::user()->emp_id_fk;
                            $deposit->isFromAutoProcess     = 1;
                            $deposit->isAuthorized          = 1;
                            $deposit->createdAt             = Carbon::now();
                            $deposit->save();
                            ////////////////
                        }
                     } /*saving account foreach*/

                     foreach ($loanAccounts as  $loanAccount) {
                        $isTraExits = (int) DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$loanAccount->id)->where('collectionDate',$softwareDate)->value('id');
                        if ($isTraExits<1) {
                            $traExitsFlag = 0;

                            /////////////
                            $loanCollection = new MfnLoanCollection;
                            $loanCollection->loanIdFk               = $loanAccount->id;
                            $loanCollection->productIdFk            = $loanAccount->productIdFk;
                            $loanCollection->primaryProductIdFk     = $loanAccount->primaryProductIdFk;
                            $loanCollection->loanTypeId             = $loanAccount->loanTypeId;
                            $loanCollection->memberIdFk             = $loanAccount->memberIdFk;
                            $loanCollection->branchIdFk             = $loanAccount->branchIdFk;
                            $loanCollection->samityIdFk             = $loanAccount->samityIdFk;
                            $loanCollection->collectionDate         = Carbon::parse($softwareDate);
                            $loanCollection->amount                 = 0;
                            $loanCollection->principalAmount        = 0;
                            $loanCollection->interestAmount         = 0;                
                            $loanCollection->paymentType            = 'Cash';
                            $loanCollection->ledgerIdFk             = $ledgerId;
                            $loanCollection->chequeNumber           = '';
                            $loanCollection->installmentNo          = 0;
                            $loanCollection->isFromAutoProcess      = 1;
                            $loanCollection->isAuthorized           = 1;
                            $loanCollection->entryByEmployeeIdFk    = $entryByEmployeeIdFk;//Auth::user()->emp_id_fk;
                            $loanCollection->createdAt              = Carbon::now();
                            $loanCollection->save();
                            /////////////
                        }
                     } /*loan account foreach*/

                } /*member foreach*/
                
            } /*samity foreach*/
        
        }
    }

        