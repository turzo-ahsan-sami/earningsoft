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
    use App\microfin\savings\MfnSavingsAccount;
    use App\microfin\member\MfnMemberInformation;
    use App\microfin\savings\MfnSavingsDeposit;
    use App\microfin\savings\MfnSavingsWithdraw;
    use App\microfin\samity\MfnSamity;
    use App\microfin\process\MfnDayEnd;
    use App\microfin\process\MfnMonthEnd;
    use App\microfin\process\MfnDayEndSavingsDetails;
    use App\microfin\process\MfnDayEndLoanDetails;
    use App\accounting\AddVoucher;
    use App\accounting\AddVoucherDetails;
    use Auth;
    use App\Http\Controllers\microfin\process\MfnAutoVoucher;

    use App\Http\Controllers\microfin\process\DayEndStoreInfo;
    use App\microfin\member\MfnMemberPrimaryProductTransfer;
    use App\Http\Controllers\microfin\MicroFin;
    use App\Traits\GetSoftwareDate;

    class DayEndProcessController extends Controller {

        private $TCN;

        private $dbMembers;
        private $dbSavAcc;
        private $dbSavDeposit;
        private $dbSavWithdraw;
        private $dbLoan;
        private $dbLoanCollection;
        private $todaySamityIds;
        private $dbSchedules;
        private $loanIdsHavingSheduleToday;

        public function __construct() {

            $this->TCN = array(
                array('SL#', 70),
                array('DATE', 0),
                array('BRANCH', 0),
                array('ON DATE DUE', 0),
                array('ON DATE ADVANCE', 0),
                array('TOTAL DUE', 0),
                array('TOTAL ADVANCE', 0),
                array('TOTAL OVERDUE', 0),
                array('CURRENT DUE', 0),
                array('Action', 80)
            );
        }

        public function index(Request $req) {

            $userBranchId = Auth::user()->branchId;

            $monthArray = MicroFin::getMonthsOption();

            $yearArray = array();

            if (isset($req->filBranch)) {
                $targetBranchId = $req->filBranch;
            }
            else{
                $targetBranchId = $userBranchId;
            }

            $dayEndMinYear = MfnDayEnd::active()->where('branchIdFk',$targetBranchId)->where('isLocked',1)->min('date');
            $dayEndMinYear = (int) Carbon::parse($dayEndMinYear)->format('Y');

            $dayEndMaxYear = MfnDayEnd::active()->where('branchIdFk',$targetBranchId)->where('isLocked',1)->max('date');
            $dayEndMaxYear = (int) Carbon::parse($dayEndMaxYear)->format('Y');

            while ($dayEndMaxYear>=$dayEndMinYear) {
                $yearArray = $yearArray + [$dayEndMaxYear=>$dayEndMaxYear];
                $dayEndMaxYear--;
            }

            $softwareDate = MfnDayEnd::active()->where('branchIdFk',$targetBranchId)->where('isLocked',0)->min('date');

            if ($softwareDate=='' || $softwareDate==null) {
                $softwareDate = DB::table('gnr_branch')->where('id',$targetBranchId)->value('softwareStartDate');
            }

            $dayEnds = MfnDayEnd::active()->where('branchIdFk',$targetBranchId)->where('isLocked',1);

            if ($req->filMonth!='') {
                $firstMonth = str_pad($req->filMonth, 2, '0', STR_PAD_LEFT);
                $lastMonth = $firstMonth;
            }
            else{
                $firstMonth = '01';
                $lastMonth = '12';
            }

            if ($req->filYear!='') {
                $yearFirstDate = Carbon::parse('01-'.$firstMonth.'-'.$req->filYear);
                $yearLastDate = Carbon::parse('31-'.$lastMonth.'-'.$req->filYear);
                $dayEnds = $dayEnds->where('date','>=',$yearFirstDate)->where('date','<=',$yearLastDate);
            }
            elseif(count($yearArray)>0){
                $yearFirstDate = Carbon::parse('01-'.$firstMonth.'-'.max($yearArray));
                $yearLastDate = Carbon::parse('31-'.$lastMonth.'-'.max($yearArray));
                $dayEnds = $dayEnds->where('date','>=',$yearFirstDate)->where('date','<=',$yearLastDate);
            }


            $dayEnds = $dayEnds->orderBy('date','desc')->paginate(31);

            $branchName = DB::table('gnr_branch')->where('id',$targetBranchId)->value('name');

            $maxDayEndId = (int) MfnDayEnd::active()->where('branchIdFk',$targetBranchId)->where('isLocked',1)->orderBy('date','desc')->value('id');

            $isFirstRequest = isset($req->filBranch) ? 0:1;

            $branchList = DB::table('gnr_branch');

            if ($userBranchId!=1) {
                $branchList = $branchList->where('id',$userBranchId);
            }

            $branchList = $branchList
                       ->orderBy('branchCode')
                       ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                       ->pluck('nameWithCode', 'id')
                       ->toArray();            
            
            $TCN = $this->TCN;
            $damageData = array(
                'TCN'             =>  $TCN,
                'softwareDate'    =>  $softwareDate,
                'dayEnds'         =>  $dayEnds,
                'branchName'      =>  $branchName,
                'monthArray'      =>  $monthArray,
                'yearArray'       =>  $yearArray,
                'maxDayEndId'     =>  $maxDayEndId,
                'lastYear'        =>  isset($yearArray[0]) ? $yearArray[0]:'',
                'userBranchId'    =>  $userBranchId,
                'isFirstRequest'  =>  $isFirstRequest,
                'branchList'      =>  $branchList,
            );

            return view('microfin/process/dayEndProcess/viewDayEndProcess',$damageData);
        }

        public function storeDayEnd(Request $req){
            

            $targetBranchId = $req->branchId;

            $numberOfDayEnd = MfnDayEnd::active()->where('branchIdFk',$targetBranchId)->where('isLocked',1)->count();
            if ($numberOfDayEnd==0) {
                $data = $this->storeDayEndFirstTime($targetBranchId);
                return response::json($data);
            }

            $softwareDate = MicroFin::getSoftwareDateBranchWise($targetBranchId);

            $softwareDate = Carbon::parse($softwareDate);
            $softwareDateFormat = $softwareDate->format('Y-m-d');

            $weekDayNumber = $softwareDate->dayOfWeek == 6 ? 1: $softwareDate->dayOfWeek+2;
            $dayNumber = $softwareDate->day;

            $this->dbSavAcc = DB::table('mfn_savings_account')
                                    ->where('softDel',0)
                                    ->where('status',1)
                                    ->where('branchIdFk',$targetBranchId)
                                    ->where('accountOpeningDate','<=',$softwareDateFormat)
                                    ->select('id','samityIdFk','memberIdFk','depositTypeIdFk','savingsProductIdFk','branchIdFk','samityIdFk')
                                    ->get();


            $this->dbSavDeposit = DB::table('mfn_savings_deposit')
                                    ->where('softDel',0)
                                    ->where('branchIdFk',$targetBranchId)
                                    ->where('depositDate','<=',$softwareDateFormat)
                                    ->select('id','accountIdFk','productIdFk','primaryProductIdFk','isAuthorized','depositDate','isTransferred','ledgerIdFk','amount')
                                    ->get();
            

            $this->dbSavWithdraw = DB::table('mfn_savings_withdraw')
                                    ->where('softDel',0)
                                    ->where('amount','>',0)
                                    ->where('branchIdFk',$targetBranchId)
                                    ->where('withdrawDate','<=',$softwareDateFormat)
                                    ->select('id','amount','ledgerIdFk','accountIdFk','productIdFk','primaryProductIdFk','isAuthorized','withdrawDate','isTransferred')
                                    ->get();

            $start = microtime(true);
            $this->dbLoan = DB::table('mfn_loan')
                                ->where('softDel',0)
                                ->where('status',1)
                                ->where(function($query) use ($softwareDateFormat){
                                    $query->where('isLoanCompleted',0)
                                        ->orWhere('loanCompletedDate','=',$softwareDateFormat);
                                })
                                // ->where('isLoanCompleted',0)
                                ->where('branchIdFk',$targetBranchId)
                                ->where('disbursementDate','<=',$softwareDateFormat)
                                ->select('id','loanAmount','totalRepayAmount','lastInstallmentDate','interestCalculationMethodId','samityIdFk','memberIdFk','additionalFee','productIdFk','loanFormFee','insuranceAmount','ledgerId','disbursementDate','primaryProductIdFk')
                                ->get();
                                
            


            $this->dbLoanCollection = DB::table('mfn_loan_collection')
                                            ->where('softDel',0)
                                            ->where('branchIdFk',$targetBranchId)
                                            ->where('collectionDate','<=',$softwareDateFormat)
                                            ->select('id','loanIdFk','memberIdFk','primaryProductIdFk','amount','principalAmount','interestAmount','productIdFk','principalAmount','interestAmount','ledgerIdFk','collectionDate','isAuthorized')
                                            ->get();

            $memberIdsHavingAccounts = array_merge($this->dbSavAcc->pluck('memberIdFk')->toArray(),$this->dbLoan->pluck('memberIdFk')->toArray());
            $memberIdsHavingAccounts = array_unique($memberIdsHavingAccounts);

            $this->dbMembers = DB::table('mfn_member_information')
                                    ->where('softDel',0)
                                    ->where('status',1)
                                    ->where('admissionDate','<=',$softwareDate)
                                    ->where('branchId',$targetBranchId)
                                    // ->whereIn('id',$memberIdsHavingAccounts)
                                    ->select('id','primaryProductId','admissionFee','admissionDate','samityId','gender')
                                    ->get();

            $todaySamitys = DB::table('mfn_samity')
                                ->where('branchId',$targetBranchId)
                                 ->where(function ($query) use ($weekDayNumber,$dayNumber) {
                                        $query->where('samityDayId', $weekDayNumber)
                                              ->orWhere('fixedDate',$dayNumber);
                                    })
                                ->where('openingDate','<=',$softwareDateFormat)
                                ->select(DB::raw("CONCAT(code, ' - ', name) AS nameWithCode"), 'id')
                                ->get();

            $this->todaySamityIds = $todaySamitys->pluck('id')->toArray();
            

            $this->dbSchedules = DB::table('mfn_loan_schedule AS t1')
                                        ->join('mfn_loan AS t2','t1.loanIdFk','t2.id')
                                        ->where('t1.softDel',0)
                                        ->where('t2.softDel',0)
                                        ->where('t2.status',1)
                                        ->where('t2.status',1)
                                        ->where('t2.branchIdFk',$targetBranchId)
                                        ->where('t2.disbursementDate','<=',$softwareDateFormat)
                                        // ->whereIn('loanIdFk',$this->dbLoan->pluck('id'))
                                        ->where('t1.scheduleDate','<=',$softwareDateFormat)
                                        ->select('t1.loanIdFk','t1.installmentAmount','t1.principalAmount','t1.scheduleDate','t2.primaryProductIdFk','t2.productIdFk','t2.memberIdFk')
                                        ->get();
            
            
            $this->loanIdsHavingSheduleToday = $this->dbSchedules
                                            ->where('scheduleDate',$softwareDateFormat)     
                                            ->pluck('loanIdFk')
                                            ->toArray();

            
            // if it is the first day end, then store opening data
            /*$numberOfDayEnd = MfnDayEnd::active()->where('branchIdFk',$targetBranchId)->where('isLocked',1)->count();
            if ($numberOfDayEnd==0) {
                $data = $this->storeDayEndFirstTime($targetBranchId);
                return response::json($data);
            }*/

            // $startTime = microtime(true);
            /// check is there should be transaction from auto process but not entry
            // $isAllTraExits = $this->checkTodayAllTraExits($softwareDateFormat);


            $missingTransactions = $this->checkTodayMissingTransactions($softwareDateFormat);
            if (count($missingTransactions)>0) {
                
                if ($missingTransactions['name']=='savings') {
                    $missingSamityIds = $this->dbSavAcc->whereIn('id',$missingTransactions['ids'])->pluck('samityIdFk')->toArray();
                    if($targetBranchId==32){
                        // dd('sav ',$missingTransactions['ids']);
                    }
                }
                else{
                    $missingSamityIds = $this->dbLoan->whereIn('id',$missingTransactions['ids'])->pluck('samityIdFk')->toArray();
                    if($targetBranchId==32){
                        // dd('loan ',$missingTransactions['ids']);
                    }
                    
                }
                $missingSamityNames = implode('<br>',$todaySamitys->whereIn('id',$missingSamityIds)->pluck('nameWithCode')->toArray());
                $warningText = $missingSamityNames;
                $data = array(
                    'responseTitle' =>  'entryTransaction',
                    'responseText'  =>  $warningText
                );
                return response::json($data);
            }

            // $time_elapsed_secs = microtime(true) - $startTime;   dd($time_elapsed_secs .'sec');

            /// check is any unauthorized transaction exits or not, if exits then day end process can't be executed
            // $startTime = microtime(true);
            $unauthorizedDeposit = (int) $this->dbSavDeposit->where('depositDate',$softwareDateFormat)->where('amount','>',0)->where('isAuthorized',0)->max('id');

            $unauthorizedWithdraw = (int) $this->dbSavWithdraw->where('withdrawDate',$softwareDateFormat)->where('isAuthorized',0)->max('id');

            $unauthorizedCollection = (int) $this->dbLoanCollection->where('collectionDate',$softwareDateFormat)->where('amount','>',0)->where('isAuthorized',0)->max('id');

            $hasAnyUnauthorizedTra = max($unauthorizedDeposit,$unauthorizedWithdraw,$unauthorizedCollection);
            
           /* if($targetBranchId==87){
                dd($softwareDateFormat,$unauthorizedDeposit,$unauthorizedWithdraw,$unauthorizedCollection);
            }*/


            if($hasAnyUnauthorizedTra>0){
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Please authorize all transactions first.'
                );

                return response::json($data);
            }

            // check month end is executed or not
            // if it is the first month then skip the month end cheking
            $softwareStartDate = Carbon::parse(DB::table('gnr_branch')->where('id',$targetBranchId)->value('softwareStartDate'));
            if ($softwareStartDate->copy()->format('Y-m')==$softwareDate->copy()->format('Y-m')) {
                // this date ar in the same month (e.g. it is the starting month)
            }
            else{
                // first month passed, now check the month end closed or not
                $lastMonthLastDate = $softwareDate->copy()->startOfMonth()->subDay()->format('Y-m-d');
                $monthEndExits = (int) MfnMonthEnd::where('branchIdFk',$targetBranchId)->where('date',$lastMonthLastDate)->value('id');
                if ($monthEndExits<1) {
                    $data = array(
                        'responseTitle' =>  'Warning!',
                        'responseText'  =>  'Executed month end first.'
                    );

                    return response::json($data);
                }
            }

            /*$onDateDueAdvanceAmount = $this->getOnDateDueAdvanceAmount($softwareDateFormat,$targetBranchId);           
            $totalDueAdvanceAmount = $this->getTotalDueAdvance($softwareDateFormat,$targetBranchId);           
            $totalOverDueAmount = $this->getTotalOverDue($softwareDateFormat,$targetBranchId);*/
            /*if($targetBranchId==7){
                $time_elapsed_secs = microtime(true) - $start;
                dd($time_elapsed_secs);
            }*/

            $loanInfo = $this->getLoanInfo($targetBranchId,$softwareDateFormat);

            $isDayExits = (int) MfnDayEnd::where('branchIdFk',$targetBranchId)->where('date',$softwareDate)->value('id');
            if ($isDayExits>0) {
                $dayEnd = MfnDayEnd::where('branchIdFk',$targetBranchId)->where('date',$softwareDate)->first();
            }
            else{
                $dayEnd = new MfnDayEnd;
            }     

            $dayEnd->date               = $softwareDate;
            $dayEnd->branchIdFk         = $targetBranchId;

            $dayEnd->dueAmount          = $loanInfo['onDateDue'];
            $dayEnd->advancedAmount     = $loanInfo['onDateAdvance'];
            $dayEnd->totalDueAmount     = $loanInfo['totalDue'];
            $dayEnd->totalAdvanceAmount = $loanInfo['totalAdvance'];
            $dayEnd->totalOverDueAmount = $loanInfo['totalOverdue'];
            $dayEnd->currentDueAmount   = $loanInfo['currentDue'];
            $dayEnd->isLocked           = 1;
            $dayEnd->executedByEmpIdFk  = Auth::user()->emp_id_fk;
            $dayEnd->createdAt          = Carbon::now();
            
            $consData = array(
                'targetBranchId'        => $targetBranchId,
                'softwareDate'    => $softwareDateFormat,
                'dbSavDeposit'    => $this->dbSavDeposit,
                'dbSavWithdraw'    => $this->dbSavWithdraw,
                'dbLoan'    => $this->dbLoan,
                'dbMembers'    => $this->dbMembers,
                'dbLoanCollection'    => $this->dbLoanCollection,
            );
            
            $autoVoucher = New MfnAutoVoucher($consData);
            $autoVoucher->createCreditVoucher();
            $autoVoucher->createDebitVoucher();    
            $autoVoucher->createJournalVoucher();


            $this->redoTransferState($targetBranchId,$softwareDate->format('Y-m-d'));
            $this->undateTransfer($targetBranchId,$softwareDate->format('Y-m-d'));
            $dayEnd->save();

            // Insert Next working day. Holiday will not be the next working Day.
            
            $nextDay = '';
            $isNextDayHoliday = 1;
            $nextDay = $softwareDate->copy();

            while ($isNextDayHoliday==1) {
                $nextDay = $nextDay->addDay();
                $nextDateString = $nextDay->copy()->format('Y-m-d');
                $isNextDayHoliday = $this->isHoliday($nextDateString,$targetBranchId);
            }

            $isDayExits = (int) MfnDayEnd::where('branchIdFk',$targetBranchId)->where('date',$nextDay)->value('id');
            if ($isDayExits>0) {
                $nextDayEnd = MfnDayEnd::where('branchIdFk',$targetBranchId)->where('date',$nextDay)->first();
            }
            else{
                $nextDayEnd = new MfnDayEnd;
            }

            $nextDayEnd->date       = $nextDay;
            $nextDayEnd->branchIdFk = $targetBranchId;            
            $nextDayEnd->isLocked   = 0;
            $nextDayEnd->save();

           
           if ($targetBranchId>=84) {
                $updateInfo = new DayEndStoreInfo($targetBranchId,$softwareDateFormat);
                // $consData['dbSavAcc'] = $this->dbSavAcc;
                // $consData['dbSchedules'] = $this->dbSchedules;
                // $updateInfo = new DayEndStoreInfo($consData);
                $updateInfo->saveData();
            }

            


            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Day End executed successfully.'
            );

            
            return response::json($data);
        }

        public function getLoanInfo($branchId,$date){

            $openingBalance = DB::table('mfn_opening_balance_loan')
                                ->where('softDel',0)
                                // ->whereIn('loanIdFk',$this->dbLoan->pluck('id'))
                                ->get();

            $onDateDue = 0;
            $onDateAdvance = 0;
            $totalDue = 0;
            $totalAdvance = 0;
            $totalOverdue = 0;
            $currentDue = 0;

            foreach ($this->dbLoan as $loan) {

                $due = 0;
                
                /// for overdue loans
                if ($loan->lastInstallmentDate<$date) {
                    // interestCalculationMethodId = 3 means flat method
                    if ($loan->interestCalculationMethodId==3) {
                        $totalOverdue += $loan->totalRepayAmount - $openingBalance->where('loanIdFk',$loan->id)->sum('paidLoanAmountOB') - $this->dbLoanCollection->where('loanIdFk',$loan->id)->sum('amount');
                    }
                    // interestCalculationMethodId = 4 means reducing method
                    elseif ($loan->interestCalculationMethodId==4) {
                        $totalOverdue += $loan->loanAmount - $openingBalance->where('loanIdFk',$loan->id)->sum('principalAmountOB') - $this->dbLoanCollection->where('loanIdFk',$loan->id)->sum('principalAmount');
                    }
                }
                /// for running loans
                else{
                    $amountPayable = $this->dbSchedules->where('loanIdFk',$loan->id)->sum('installmentAmount');
                    // interestCalculationMethodId = 3 means flat method
                    if ($loan->interestCalculationMethodId==3) {
                        $due = $amountPayable - $openingBalance->where('loanIdFk',$loan->id)->sum('paidLoanAmountOB') - $this->dbLoanCollection->where('loanIdFk',$loan->id)->sum('amount');
                    }
                    // interestCalculationMethodId = 4 means reducing method
                    elseif ($loan->interestCalculationMethodId==4) {
                        $due = $amountPayable - $openingBalance->where('loanIdFk',$loan->id)->sum('principalAmountOB') - $this->dbLoanCollection->where('loanIdFk',$loan->id)->sum('principalAmount');
                    }

                    if ($due>0) {
                        $isScheduleToday = $this->dbSchedules->where('loanIdFk',$loan->id)->where('scheduleDate',$date)->count();
                        if ($isScheduleToday>0) {
                            $installmentAmount = $this->dbSchedules->where('loanIdFk',$loan->id)->max('installmentAmount');
                            $thisLoanOndateDue = $due > $installmentAmount ? $installmentAmount : $due;
                            $onDateDue += $thisLoanOndateDue;
                        }                        
                        $totalDue += $due;
                    }
                    else{
                        $totalAdvance -= $due;
                    } 

                }
            }

            $currentDue = $totalOverdue + $totalDue;
            $onDateAdvance = $totalAdvance;

            $data = array(
                'onDateDue'     => $onDateDue,
                'onDateAdvance' => $onDateAdvance,
                'totalDue'      => $totalDue,
                'totalAdvance'  => $totalAdvance,
                'totalOverdue'  => $totalOverdue,
                'currentDue'    => $currentDue
            );
            return $data;
        }

        public function storeDayEndFirstTime($targetBranchId){
            /*$data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Day end service is stoped temporarily.'
            );
            return response::json($data);*/
            
            $softwareDate = MicroFin::getSoftwareDateBranchWise($targetBranchId);
            $softwareDate = Carbon::parse($softwareDate);
            $softwareDateFormat = $softwareDate->format('Y-m-d');

            // check is all Loan opening balnace information is given or not
            $loanIdsFromOpeningBalance = DB::table('mfn_opening_balance_loan')
                                            ->where('softDel',0)
                                            ->pluck('loanIdFk')
                                            ->toArray();

            $numOfMissingInformation = DB::table('mfn_loan')
                                        ->where('softDel',0)
                                        ->where('isFromOpening',1)
                                        ->where('branchIdFk',$targetBranchId)
                                        ->whereNotIn('id',$loanIdsFromOpeningBalance)
                                        ->count();

            if ($numOfMissingInformation>0) {
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Please give all Loan Accounts Opening Balance.'
                );
                return $data;
            }

            // check is all Savings opening balnace information is given or not
            $savingIdsFromOpeningBalance = DB::table('mfn_opening_savings_account_info')
                                            ->pluck('savingsAccIdFk')
                                            ->toArray();

            $numOfMissingInformation = DB::table('mfn_savings_account')
                                        ->where('softDel',0)
                                        ->where('isFromOpening',1)
                                        ->where('branchIdFk',$targetBranchId)
                                        ->whereNotIn('id',$savingIdsFromOpeningBalance)
                                        ->count();

            if ($numOfMissingInformation>0) {
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Please give all Saving Accounts Opening Balance.'
                );
                return $data;
            }

            // get loan due, advance info from openign table

            $loanInfo = $this->getLoanInfoFromOpeningBalance($targetBranchId,$softwareDateFormat);

            // store dayend
            $isDayExits = (int) MfnDayEnd::where('branchIdFk',$targetBranchId)->where('date',$softwareDateFormat)->value('id');
            if ($isDayExits>0) {
                $dayEnd = MfnDayEnd::where('branchIdFk',$targetBranchId)->where('date',$softwareDateFormat)->first();
            }
            else{
                $dayEnd = new MfnDayEnd;
            }  

            $dayEnd->date               = $softwareDate;
            $dayEnd->branchIdFk         = $targetBranchId;
            $dayEnd->dueAmount          = $loanInfo['onDateDue'];
            $dayEnd->advancedAmount     = $loanInfo['onDateAdvance'];
            $dayEnd->totalDueAmount     = $loanInfo['totalDue'];
            $dayEnd->totalAdvanceAmount = $loanInfo['totalAdvance'];
            $dayEnd->totalOverDueAmount = $loanInfo['totalOverdue'];
            $dayEnd->currentDueAmount   = $loanInfo['currentDue'];
            $dayEnd->isLocked           = 1;
            $dayEnd->executedByEmpIdFk  = Auth::user()->emp_id_fk;
            $dayEnd->createdAt          = Carbon::now();
            $dayEnd->save();

            // insert next working day
            $nextDay = '';
            $isNextDayHoliday = 1;
            $nextDay = $softwareDate->copy();

            while ($isNextDayHoliday==1) {
                $nextDay = $nextDay->addDay();
                $nextDateString = $nextDay->copy()->format('Y-m-d');
                $isNextDayHoliday = $this->isHoliday($nextDateString,$targetBranchId);
            }

            $isDayExits = (int) MfnDayEnd::where('branchIdFk',$targetBranchId)->where('date',$nextDay)->value('id');
            if ($isDayExits>0) {
                $nextDayEnd = MfnDayEnd::where('branchIdFk',$targetBranchId)->where('date',$nextDay)->first();
            }
            else{
                $nextDayEnd = new MfnDayEnd;
            }

            $nextDayEnd->date       = $nextDay;
            $nextDayEnd->branchIdFk = $targetBranchId;            
            $nextDayEnd->isLocked   = 0;
            $nextDayEnd->save();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Day End executed successfully.'
            );

            return $data;
        }

        public function getLoanInfoFromOpeningBalance($branchId,$openingDate){
            $loans = DB::table('mfn_loan')
                        ->where('softDel',0)
                        ->where('isFromOpening',1)
                        ->where('branchIdFk',$branchId)
                        ->select('id','loanAmount','totalRepayAmount','lastInstallmentDate','interestCalculationMethodId')
                        ->get();

            $openingBalance = DB::table('mfn_opening_balance_loan')
                                ->where('softDel',0)
                                ->whereIn('loanIdFk',$loans->pluck('id'))
                                ->get();

            $shedules = DB::table('mfn_loan_schedule')
                            ->where('softDel',0)
                            ->where('scheduleDate','<=',$openingDate)
                            ->whereIn('loanIdFk',$loans->pluck('id'))
                            ->select('loanIdFk','installmentAmount','scheduleDate')
                            ->get();

            $onDateDue = 0;
            $onDateAdvance = 0;
            $totalDue = 0;
            $totalAdvance = 0;
            $totalOverdue = 0;
            $currentDue = 0;

            foreach ($loans as $loan) {

                $due = 0;
                
                /// for overdue
                if ($loan->lastInstallmentDate<$openingDate) {
                    // interestCalculationMethodId = 3 means flat method
                    if ($loan->interestCalculationMethodId==3) {
                        $totalOverdue += $loan->totalRepayAmount - $openingBalance->where('loanIdFk',$loan->id)->sum('paidLoanAmountOB');
                    }
                    // interestCalculationMethodId = 4 means reducing method
                    elseif ($loan->interestCalculationMethodId==4) {
                        $totalOverdue += $loan->loanAmount - $openingBalance->where('loanIdFk',$loan->id)->sum('principalAmountOB');                     
                    }
                }
                /// for running loans
                else{
                    $amountPayable = $shedules->where('loanIdFk',$loan->id)->sum('installmentAmount');
                    // interestCalculationMethodId = 3 means flat method
                    if ($loan->interestCalculationMethodId==3) {
                        $due = $amountPayable - $openingBalance->where('loanIdFk',$loan->id)->sum('paidLoanAmountOB');
                    }
                    // interestCalculationMethodId = 4 means reducing method
                    elseif ($loan->interestCalculationMethodId==4) {
                        $due = $amountPayable - $openingBalance->where('loanIdFk',$loan->id)->sum('principalAmountOB');
                    }

                    if ($due>0) {
                        $totalDue += $due;
                    }
                    else{
                        $totalAdvance -= $due;
                    } 

                }
            }

            $currentDue = $totalOverdue + $totalDue;

            $data = array(
                'onDateDue'     => $onDateDue,
                'onDateAdvance' => $onDateAdvance,
                'totalDue'      => $totalDue,
                'totalAdvance'  => $totalAdvance,
                'totalOverdue'  => $totalOverdue,
                'currentDue'    => $currentDue
            );
            return $data;
        }

        /**
         * [isHoliday description]
         * @param  [date string]  $date [description]
         * @return boolean       [if it is holiday then return true or return false]
         */
        public function isHoliday($date,$targetBranchId){
            $date = Carbon::parse($date)->format('Y-m-d');
            $isHoliday = 0;
            //get holidays
            $holiday = (int) DB::table('mfn_setting_holiday')->where('status',1)->where('date',$date)->value('id');
            if ($holiday>0) {
                $isHoliday = 1;
            }

            // get the organazation id and branch id of the loggedin user
            if ($isHoliday!=1) {
                $userBranchId = Auth::user()->branchId;
                $userOrgId = Auth::user()->company_id_fk;

                if($targetBranchId!=1){
                    $userBranchId = $targetBranchId;
                    $userOrgId = DB::table('gnr_branch')->where('id',$targetBranchId)->value('companyId');
                }

                $holiday = (int) DB::table('mfn_setting_orgBranchSamity_holiday')
                                       ->where('status',1)
                                       ->where(function ($query) use ($userBranchId,$userOrgId) {
                                            $query->where('ogrIdFk', '=', $userOrgId)
                                                  ->orWhere('branchIdFk', '=', $userBranchId);
                                        })
                                       ->where('dateFrom','<=',$date)
                                       ->where('dateTo','>=',$date)
                                       ->value('id');
                if($holiday>0){
                    $isHoliday = 1;
                }
            }

            return $isHoliday;
            
        }

        public function getOnDateDueAdvanceAmount($date,$targetBranchId){
            $branchId = $targetBranchId;
            $schedule = DB::table('mfn_loan_schedule as t1')
                                ->join('mfn_loan as t2','t1.loanIdFk','t2.id')
                                ->where('t1.softDel',0)
                                ->where('t1.status',1)
                                ->where('t1.scheduleDate',$date)
                                ->where('t2.branchIdFk',$branchId);                                

            $onDateDueAmount = clone($schedule);
            $onDatePartialPaidAmount = clone($schedule);
            $onDateDueAmount = $onDateDueAmount->where('isCompleted',0)->sum('t1.installmentAmount');
            $onDateDueAmount = $onDateDueAmount - $onDatePartialPaidAmount->where('t1.isPartiallyPaid',1)->sum('partiallyPaidAmount');

            // on date installment amount
            $onDateInstallmentAmount = clone($schedule);
            $onDateInstallmentAmount = $onDateInstallmentAmount->sum('t1.installmentAmount');
            $onDatePaidAmount = DB::table('mfn_loan_collection')->where('softDel',0)->where('status',1)->where('branchIdFk',$branchId)->where('collectionDate',$date)->sum('amount');
            // on date advance amount
            $onDateAdvanceAmount = $onDatePaidAmount - $onDateInstallmentAmount;
            $onDateAdvanceAmount = $onDateAdvanceAmount<0 ? 0: $onDateAdvanceAmount;

            $data = array(
                'onDateDueAmount'       => $onDateDueAmount,
                'onDateAdvanceAmount'   => $onDateAdvanceAmount
            );

            return $data;
        }

        public function getTotalDueAdvance($date,$targetBranchId){
            $branchId = $targetBranchId;
            $schedule = DB::table('mfn_loan_schedule as t1')
                                ->join('mfn_loan as t2','t1.loanIdFk','t2.id')
                                ->where('t1.softDel',0)
                                ->where('t1.status',1)
                                ->where('t1.scheduleDate','<=',$date)
                                ->where('t2.branchIdFk',$branchId);                                

            $scheduleAmount = clone($schedule);
            // till Date Installment Amount to Be collected
            $scheduleAmount = $scheduleAmount->sum('t1.installmentAmount');

            $loanCollection = DB::table('mfn_loan_collection')->where('softDel',0)->where('status',1)->where('branchIdFk',$branchId)->where('collectionDate','<=',$date);
            $collectedAmount = clone($loanCollection);
            // till Date Collected Amount
            $collectedAmount = $collectedAmount->sum('amount');

            // till Date Due Amount
            $totalDueAmount = $scheduleAmount - $collectedAmount;
            $totalDueAmount = $totalDueAmount<0 ? 0 : $totalDueAmount;

            // till Date Advance Amount
            $totalAdvanceAmount = $collectedAmount - $scheduleAmount;
            $totalAdvanceAmount = $totalAdvanceAmount<0 ? 0 : $totalAdvanceAmount;

            $data = array(
                'totalDueAmount'       => $totalDueAmount,
                'totalAdvanceAmount'   => $totalAdvanceAmount
            );

            return $data;
        }

        public function getTotalOverDue($date,$targetBranchId){
            $branchId = $targetBranchId;
            $loanIdsOfThisBranch = DB::table('mfn_loan')->where('softDel',1)->where('status',1)->where('branchIdFk',$branchId)->pluck('id')->toArray();
            $overDueLoanIds = array();
            foreach ($loanIdsOfThisBranch as $key => $loanId) {

                /*$lastSheduleDate = DB::table('mfn_loan_schedule')
                                ->where('softDel',0)
                                ->where('status',1)
                                ->where('loanIdFk',$loanId)
                                ->max('newScheduleDate');

                if ($lastSheduleDate=='0000-00-00') {
                    $lastSheduleDate = DB::table('mfn_loan_schedule')
                                ->where('softDel',0)
                                ->where('status',1)
                                ->where('loanIdFk',$loanId)
                                ->max('scheduleDate');
                }*/

                $lastSheduleDate = DB::table('mfn_loan_schedule')
                                ->where('softDel',0)
                                ->where('status',1)
                                ->where('loanIdFk',$loanId)
                                ->max('scheduleDate');

                if ($lastSheduleDate>$date) {
                    array_push($overDueLoanIds, $loanId);
                }
            }

            $totalOverDueAmount = 0;
            foreach ($overDueLoanIds as $key => $overDueLoanId) {
                $totalRepayAmount = DB::table('mfn_loan')->where('id',$overDueLoanId)->value('totalRepayAmount');
                $totalCollection = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$overDueLoanId)->sum('amount');
                $totalOverDueAmount = $totalOverDueAmount + $totalRepayAmount - $totalCollection;
            }

            return $totalOverDueAmount;
            
        }


        public function deleteDayEnd(Request $req){
            $dayEnd = MfnDayEnd::find($req->id);

            // first check month end is deleted or not
            $dayEndMonthLastDate = Carbon::parse($dayEnd->date)->endOfMonth()->format('Y-m-d');
            $isMonthEndExits = (int) MfnMonthEnd::active()->where('branchIdFk',$dayEnd->branchIdFk)->where('date',$dayEndMonthLastDate)->value('id');
            if ($isMonthEndExits>0) {
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Deleted month end first.'
                );

                return response::json($data);
            }

            $dayEnd->dueAmount          = 0;
            $dayEnd->advancedAmount     = 0;
            $dayEnd->totalDueAmount     = 0;
            $dayEnd->totalAdvanceAmount = 0;
            $dayEnd->totalOverDueAmount = 0;
            $dayEnd->currentDueAmount   = 0;
            $dayEnd->isLocked           = 0;
            $dayEnd->executedByEmpIdFk  = 0;
            $dayEnd->save();

            
            DB::table('mfn_day_end')->where('branchIdFk',$dayEnd->branchIdFk)->where('date','>',$dayEnd->date)->delete();

            DB::table('mfn_day_end_saving_details')->where('branchIdFk',$dayEnd->branchIdFk)->where('date',$dayEnd->date)->delete();

            DB::table('mfn_day_end_loan_details')->where('branchIdFk',$dayEnd->branchIdFk)->where('date',$dayEnd->date)->delete();

            $this->undoTransferState($dayEnd->branchIdFk,$dayEnd->date);

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Day End deleted successfully.'
            );

            return response::json($data);
        }


        public function undoTransferState($branchId,$date){

            // Member Samity Transfer
            $memberSamityTransfers = DB::table('mfn_member_samity_transfer')
                                        ->where('softDel',0)
                                        ->where('branchIdFk',$branchId)
                                        ->where('transferDate',$date)
                                        ->orderBy('id','desc')
                                        ->get();

            foreach ($memberSamityTransfers as $memberSamityTransfer) {

                $memberId = (int) $memberSamityTransfer->memberIdFk;

                $member = MfnMemberInformation::find($memberId);
                $member->samityId = $memberSamityTransfer->previousSamityIdFk;
                // $member->code = $memberSamityTransfer->previousMemberCodeFk;
                // $member->primaryProductId = $memberSamityTransfer->previousPrimaryProductIdFk;
                $member->save();
                
                $savingsDetails = json_decode($memberSamityTransfer->savingsRecord);
                if (is_array($savingsDetails)) {
                    foreach ($savingsDetails as $saving) {
                        $savingsAcc = MfnSavingsAccount::find($saving->id);
                        $savingsAcc->samityIdFk = $memberSamityTransfer->previousSamityIdFk;
                        $savingsAcc->save();
                    }   
                }
                           
            }

            // Primary Product Transfer
            $productTransfers = DB::table('mfn_loan_primary_product_transfer')
                                        ->where('softDel',0)
                                        ->where('branchIdFk',$branchId)
                                        ->where('transferDate',$date)
                                        ->orderBy('id','desc')
                                        ->get();

            foreach ($productTransfers as $productTransfer) {
                $memberId = (int) $productTransfer->memberIdFk;
                $member = MfnMemberInformation::find($memberId);
                $member->primaryProductId = $productTransfer->oldPrimaryProductFk;
                $member->save();
            }
        }


        public function redoTransferState($branchId,$date){

            $date = Carbon::parse($date)->addDay()->format('Y-m-d');

            // Member Samity Transfer
            $memberSamityTransfers = DB::table('mfn_member_samity_transfer')
                                        ->where('softDel',0)
                                        ->where('branchIdFk',$branchId)
                                        ->where('transferDate',$date)
                                        ->orderBy('id','desc')
                                        ->get();

            foreach ($memberSamityTransfers as $memberSamityTransfer) {

                $memberId = (int) $memberSamityTransfer->memberIdFk;

                $member = MfnMemberInformation::find($memberId);
                $member->samityId = $memberSamityTransfer->newSamityIdFk;
                // $member->code = $memberSamityTransfer->newMemberCodeFk;
                // $member->primaryProductId = $memberSamityTransfer->newPrimaryProductIdFk;
                $member->save();
                
                $savingsDetails = json_decode($memberSamityTransfer->savingsRecord);
                if (is_array($savingsDetails)) {
                    foreach ($savingsDetails as $saving) {
                        $savingsAcc = MfnSavingsAccount::find($saving->id);
                        $savingsAcc->samityIdFk = $memberSamityTransfer->newSamityIdFk;
                        $savingsAcc->save();
                    }   
                }           
            }

            // Primary Product Transfer
            $productTransfers = DB::table('mfn_loan_primary_product_transfer')
                                        ->where('softDel',0)
                                        ->where('branchIdFk',$branchId)
                                        ->where('transferDate',$date)
                                        ->orderBy('id','desc')
                                        ->get();

            foreach ($productTransfers as $productTransfer) {
                $memberId = (int) $productTransfer->memberIdFk;
                $member = MfnMemberInformation::find($memberId);
                $member->primaryProductId = $productTransfer->newPrimaryProductFk;
                $member->save();
            }
        }

        public function checkTodayAllTraExitsOld($softDate){

            $traExitsFlag = 1;

            foreach ($this->todaySamityIds as $key => $todaySamityId) {

                $members = $this->dbMembers->where('samityId',$todaySamityId);

                foreach ($members as $member) {
                    $savingsAccounts = $this->dbSavAcc->where('memberIdFk',$member->id)->where('depositTypeIdFk','!=',4);

                    $loanAccounts = $this->dbLoan
                                        ->where('memberIdFk',$member->id)
                                        ->whereIn('id',$this->loanIdsHavingSheduleToday);

                    foreach ($savingsAccounts as  $savingsAccount) {
                        $isTraExits = (int) $this->dbSavDeposit->where('depositDate',$softDate)->where('accountIdFk',$savingsAccount->id)->max('id');
                        if ($isTraExits<1) {
                            $traExitsFlag = 0;
                            // dd('savings: '.$savingsAccount->id);
                        }
                     } /*saving account foreach*/

                     foreach ($loanAccounts as  $loanAccount) {
                        $isTraExits = (int) $this->dbLoanCollection->where('collectionDate',$softDate)->where('loanIdFk',$loanAccount->id)->max('id');
                        if ($isTraExits<1) {
                            $traExitsFlag = 0;
                            // dd('loan '.$loanAccount->id);
                        }
                     } /*loan account foreach*/

                } /*member foreach*/
                
            } /*samity foreach*/

            
            return $traExitsFlag;
        }


        public function checkTodayMissingTransactions($softDate){
            $todaySamitySavAccIds = $this->dbSavAcc->where('depositTypeIdFk','!=',4)->whereIn('samityIdFk',$this->todaySamityIds)->pluck('id')->toArray();
            $todaySavIdsFromTransactions = $this->dbSavDeposit->where('depositDate',$softDate)->pluck('accountIdFk')->toArray();

            $missingAccountIds = array_diff($todaySamitySavAccIds,$todaySavIdsFromTransactions);

            if (count($missingAccountIds)>0) {
                // dd('SAvings ',$missingAccountIds);
                $data = array(
                    'name'  =>'savings',
                    'ids'   => $missingAccountIds
                );
                return $data;
            }

            $todaySamityLoanAccIds = $this->dbLoan->whereIn('samityIdFk',$this->todaySamityIds)->whereIn('id',$this->loanIdsHavingSheduleToday)->pluck('id')->toArray();
            $todayLoanIdsFromTransactions = $this->dbLoanCollection->where('collectionDate',$softDate)->pluck('loanIdFk')->toArray();
            $missingAccountIds = array_diff($todaySamityLoanAccIds,$todayLoanIdsFromTransactions);

            if (count($missingAccountIds)>0) {
                // dd('Loan ',$missingAccountIds);
                $data = array(
                    'name'  =>'loan',
                    'ids'   => $missingAccountIds
                );
                return $data;
            }

            return [];
        }

        public function getYears(Request $req){

            $dayEndMinYear = MfnDayEnd::active()->where('branchIdFk',$req->branchId)->where('isLocked',1)->min('date');
            $dayEndMinYear = (int) Carbon::parse($dayEndMinYear)->format('Y');

            $dayEndMaxYear = MfnDayEnd::active()->where('branchIdFk',$req->branchId)->where('isLocked',1)->max('date');
            $dayEndMaxYear = (int) Carbon::parse($dayEndMaxYear)->format('Y');

            $yearArray = array();

            while ($dayEndMaxYear>=$dayEndMinYear) {
                $yearArray = $yearArray + [$dayEndMaxYear=>$dayEndMaxYear];
                $dayEndMaxYear--;
            }

            return response::json($yearArray);
        }

        public function undateTransfer($targetBranchId, $softwareDate){

            $transfers = DB::table('mfn_loan_primary_product_transfer')
                                    ->where('softDel',0)
                                    ->where('branchIdFk',$targetBranchId)
                                    ->where('transferDate',$softwareDate)
                                    ->get();

            // transfer Date is now assumed a day before because it is assumed date the deposits and withdraws on the tranfer date belongs to the new product
            //$transferDate = Carbon::parse($softwareDate)->format("Y-m-d");

            foreach ($transfers as $key => $transfer) {
                $memberId = $transfer->memberIdFk;

                /*$savingsAccount = DB::table('mfn_savings_account')->where('softDel',0)->where('memberIdFk',$memberId)->where('accountOpeningDate','<=',$softwareDate)->select('id','savingsProductIdFk')->get();*/
                $savingsAccount = $this->dbSavAcc->where('memberIdFk',$memberId);

                $i = 0;
                $savingsSummary = [];
                $totalTransferAmount = 0;
                
                //    GET ALL THE DETAILS OF ALL THE SAVINGS ACCOUNT OF A MEMBER.
                foreach($savingsAccount as $savingsAcc):
                    $savingsSummary[$i]['id'] = $savingsAcc->id;
                    $savingsSummary[$i]['savingsProductId'] = $savingsAcc->savingsProductIdFk;                    

                    /*$savingsSummary[$i]['deposit'] = DB::table('mfn_savings_deposit')->where('softDel',0)->where('accountIdFk', $savingsAcc->id)->where('ledgerIdFk','!=',0)->where('primaryProductIdFk',$transfer->oldPrimaryProductFk)->where('depositDate','<=',$softwareDate)->sum('amount');*/
                    
                    $savingsSummary[$i]['deposit'] = $this->dbSavDeposit->where('accountIdFk', $savingsAcc->id)->where('ledgerIdFk','!=',0)->where('primaryProductIdFk',$transfer->oldPrimaryProductFk)->sum('amount');

                    /*$savingsSummary[$i]['withdraw'] = DB::table('mfn_savings_withdraw')->where('softDel',0)->where('accountIdFk', $savingsAcc->id)->where('ledgerIdFk','!=',0)->where('primaryProductIdFk',$transfer->oldPrimaryProductFk)->where('withdrawDate','<=',$softwareDate)->sum('amount');*/

                    $savingsSummary[$i]['withdraw'] = $this->dbSavWithdraw->where('accountIdFk', $savingsAcc->id)->where('ledgerIdFk','!=',0)->where('primaryProductIdFk',$transfer->oldPrimaryProductFk)->sum('amount');

                    $balance = $savingsSummary[$i]['deposit'] - $savingsSummary[$i]['withdraw'];

                    $savingsSummary[$i]['balance'] = $savingsSummary[$i]['deposit'] - $savingsSummary[$i]['withdraw'];
                    $totalTransferAmount +=  $savingsSummary[$i]['balance'];
                    $i++;

                    DB::table('mfn_savings_deposit')->where('accountIdFk', $savingsAcc->id)->where('depositDate',$softwareDate)->where('isTransferred',1)->delete();


                    $deposit = new MfnSavingsDeposit;
                    $deposit->accountIdFk           =  $savingsAcc->id;
                    $deposit->productIdFk           =  $savingsAcc->savingsProductIdFk;
                    $deposit->primaryProductIdFk    =  $transfer->newPrimaryProductFk;
                    $deposit->memberIdFk            =  $transfer->memberIdFk;
                    $deposit->branchIdFk            =  $savingsAcc->branchIdFk;
                    $deposit->samityIdFk            =  $savingsAcc->samityIdFk;
                    $deposit->amount                =  $balance;
                    $deposit->depositDate           =  Carbon::parse($softwareDate);
                    $deposit->entryByEmployeeIdFk   =  Auth::user()->emp_id_fk;
                    $deposit->isAuthorized          =  1;
                    $deposit->isTransferred         =  1;
                    $deposit->createdAt             =  Carbon::now();
                    $deposit->save();

                    DB::table('mfn_savings_withdraw')->where('accountIdFk', $savingsAcc->id)->where('withdrawDate',$softwareDate)->where('isTransferred',1)->delete();

                    $withdraw = New MfnSavingsWithdraw;
                    $withdraw->accountIdFk          =  $savingsAcc->id;
                    $withdraw->productIdFk          =  $savingsAcc->savingsProductIdFk;
                    $withdraw->primaryProductIdFk   =  $transfer->oldPrimaryProductFk;
                    $withdraw->memberIdFk           =  $transfer->memberIdFk;
                    $withdraw->branchIdFk           =  $savingsAcc->branchIdFk;
                    $withdraw->samityIdFk           =  $savingsAcc->samityIdFk;
                    $withdraw->amount               =  $balance;
                    $withdraw->withdrawDate         =  Carbon::parse($softwareDate);
                    $withdraw->entryByEmployeeIdFk  =  Auth::user()->emp_id_fk;
                    $withdraw->isAuthorized         =  1;
                    $withdraw->isTransferred        =  1;
                    $withdraw->createdAt            =  Carbon::now();
                    $withdraw->save();
                endforeach;

                $obj = MfnMemberPrimaryProductTransfer::find($transfer->id);
                $obj->totalTransferAmount = $totalTransferAmount;
                $obj->savingsRecord = $savingsSummary;
                $obj->save();
            }            
            
        }
        
    }