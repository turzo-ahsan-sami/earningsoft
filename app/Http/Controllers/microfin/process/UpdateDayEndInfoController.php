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
use App\microfin\process\MfnDayEnd;
use App\Http\Controllers\microfin\process\MfnAutoVoucher;
use App\Http\Controllers\microfin\process\DayEndStoreInfo;
use Auth;
use App\Http\Controllers\microfin\MicroFinance;
use App\microfin\member\MfnMemberPrimaryProductTransfer;
use App\microfin\savings\MfnSavingsDeposit;
use App\microfin\savings\MfnSavingsWithdraw;
use App\Service\Service;

class UpdateDayEndInfoController extends Controller
{
    protected $MicroFinance;

    public function __construct()
    {
        $this->MicroFinance = new MicroFinance;
    }

    public function index($branchId = null, $date = null)
    {

        // dd('stoped');

        if ($branchId == null) {
            $branchId = 109;
        }
        if ($date == null) {
            $date = '2019-08-02';
        }

        // $branchIds = [84,85,86,87,88,89,90,91,92,93];
        $branchIds = [$branchId];
        // $startDate = Carbon::parse('2019-07-25');
        $startDate = Carbon::parse($date);

        // $lastDayEnds = DB::select("SELECT `branchIdFk`,MAX(`date`) as endDate FROM `mfn_day_end` WHERE `isLocked`=1 GROUP BY `branchIdFk`");
        // $lastDayEnds = collect($lastDayEnds);

        foreach ($branchIds as $branchId) {
            // $endDate = Carbon::parse($lastDayEnds->where('branchIdFk',$branchId)->max('endDate'));
            $endDate = $startDate->copy()->addDay();//->endOfMonth();
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $updateInfo = new DayEndStoreInfo($branchId, $currentDate->format('Y-m-d'));
                $updateInfo->saveData();
                $currentDate->addDay();
            }
            $branchName = DB::table('gnr_branch')->where('id', $branchId)->value('name');
            echo $branchName . ' Updated<br>';
        }
    }

    public function updateDayEnds()
    {
        // $branchIds = [84,85,86,87,88,89,90,91,92,93];
        $branchIds = [85];
        $branchNames = DB::table('gnr_branch')->whereIn('id', $branchIds)->select('id', 'name')->get();
        $lastDayEnds = DB::table('mfn_day_end')
            ->where('isLocked', 1)
            ->whereIn('branchIdFk', $branchIds)
            ->groupBy('branchIdFk')
            ->select(DB::raw('branchIdFk, MAX(date) as endDate'))
            ->get();

        $undoDate = "2018-03-01";

        foreach ($lastDayEnds as $lastDayEnd) {
            echo $branchNames->where('id', $lastDayEnd->branchIdFk)->max('name') . "<br>";
            $this->undoDayEnds($lastDayEnd->branchIdFk, $undoDate);
            $this->redoDayEnds($lastDayEnd->branchIdFk, $lastDayEnd->endDate);
            echo "<br><br>";
        }
    }

    public function undoDayEnds($branchId, $undoDate)
    {

        // foreach ($branchIds as $branchId) {
        $flag = 1;
        while ($flag == 1) {
            $dayEnd = DB::table('mfn_day_end')->where('branchIdFk', $branchId)->where('isLocked', 1)->orderBy('date', 'desc')->select('id', 'date')->first();
            $dayEndId = $dayEnd->id;

            if ($dayEndId == 0 || $dayEnd->date == $undoDate) {
                $flag = 0;
                break;
            }

            $dayEnd = MfnDayEnd::find($dayEndId);
            $dayEnd->dueAmount          = 0;
            $dayEnd->advancedAmount     = 0;
            $dayEnd->totalDueAmount     = 0;
            $dayEnd->totalAdvanceAmount = 0;
            $dayEnd->totalOverDueAmount = 0;
            $dayEnd->currentDueAmount   = 0;
            $dayEnd->isLocked           = 0;
            $dayEnd->executedByEmpIdFk  = 0;
            $dayEnd->save();

            DB::table('mfn_day_end')->where('branchIdFk', $dayEnd->branchIdFk)->where('date', '>', $dayEnd->date)->delete();

            DB::table('mfn_day_end_saving_details')->where('branchIdFk', $dayEnd->branchIdFk)->where('date', $dayEnd->date)->delete();

            DB::table('mfn_day_end_loan_details')->where('branchIdFk', $dayEnd->branchIdFk)->where('date', $dayEnd->date)->delete();

            app('App\Http\Controllers\microfin\process\DayEndProcessController')->undoTransferState($branchId, $dayEnd->date);
            echo "Day: " . $dayEnd->date . " deleted<br>";
        }
        // }
    }

    public function redoDayEnds($targetBranchId, $targetDate)
    {

        //$branchIds = [84,85,86,87,88,89,90,91,92,93];
        //$branchIds = [87];

        // foreach ($branchIds as $targetBranchId) {

        $flag = 1;

        while ($flag == 1) {

            $softwareDate = MfnDayEnd::active()->where('branchIdFk', $targetBranchId)->where('isLocked', 0)->min('date');

            if ($softwareDate == '' || $softwareDate == null) {
                $softwareDate = DB::table('gnr_branch')->where('id', $targetBranchId)->value('softwareStartDate');
            }

            $softwareDate = Carbon::parse($softwareDate);
            // $softwareDate = Carbon::parse('2017-11-10');
            $softwareDateFormat = $softwareDate->format('Y-m-d');

            if ($targetDate < $softwareDateFormat) {
                $flag = 0;
                break;
                return true;
            }

            $isDayExits = (int) MfnDayEnd::where('branchIdFk', $targetBranchId)->where('date', $softwareDate)->value('id');
            if ($isDayExits > 0) {
                $dayEnd = MfnDayEnd::where('branchIdFk', $targetBranchId)->where('date', $softwareDate)->first();
            } else {
                $dayEnd = new MfnDayEnd;
            }

            $dayEnd->date               = $softwareDate;
            $dayEnd->branchIdFk         = $targetBranchId;
            $dayEnd->isLocked           = 1;
            $dayEnd->executedByEmpIdFk  = Auth::user()->emp_id_fk;
            $dayEnd->createdAt          = Carbon::now();

            app('App\Http\Controllers\microfin\process\DayEndProcessController')->redoTransferState($targetBranchId, $softwareDate->format('Y-m-d'));

            $dayEnd->save();

            // Insert Next working day. Holiday will not be the next working Day.

            $nextDay = '';
            $isNextDayHoliday = 1;
            $nextDay = $softwareDate->copy();

            while ($isNextDayHoliday == 1) {
                $nextDay = $nextDay->addDay();
                $nextDateString = $nextDay->copy()->format('Y-m-d');

                $isNextDayHoliday = app('App\Http\Controllers\microfin\process\DayEndProcessController')->isHoliday($nextDateString, $targetBranchId);
            }

            $isDayExits = (int) MfnDayEnd::where('branchIdFk', $targetBranchId)->where('date', $nextDay)->value('id');
            if ($isDayExits > 0) {
                $nextDayEnd = MfnDayEnd::where('branchIdFk', $targetBranchId)->where('date', $nextDay)->first();
            } else {
                $nextDayEnd = new MfnDayEnd;
            }

            $nextDayEnd->date       = $nextDay;
            $nextDayEnd->branchIdFk = $targetBranchId;
            $nextDayEnd->isLocked   = 0;
            $nextDayEnd->save();

            $this->undateTransfer($targetBranchId, $softwareDateFormat);

            $autoVoucher = new MfnAutoVoucher($targetBranchId, $softwareDateFormat);

            $autoVoucher->createCreditVoucher();
            $autoVoucher->createDebitVoucher();
            $autoVoucher->createJournalVoucher();

            $updateInfo = new DayEndStoreInfo($targetBranchId, $softwareDateFormat);
            $updateInfo->saveData();

            echo "Day: " . $dayEnd->date . " executed<br>";
        }
        // }            
    }

    public function undateTransfer($targetBranchId, $softwareDate)
    {

        $transfers = DB::table('mfn_loan_primary_product_transfer')
            ->where('softDel', 0)
            ->where('branchIdFk', $targetBranchId)
            ->where('transferDate', $softwareDate)
            ->get();

        $savOpeningBalances = DB::table('mfn_opening_savings_account_info')
            ->whereIn('memberIdFk', $transfers->pluck('memberIdFk'))
            ->get();

        // transfer Date is now assumed a day before because it is assumed date the deposits and withdraws on the tranfer date belongs to the new product
        //$transferDate = Carbon::parse($softwareDate)->format("Y-m-d");

        foreach ($transfers as $key => $transfer) {
            $memberId = $transfer->memberIdFk;

            $savingsAccount = $this->MicroFinance->getSavingsAccountNumberPerMember($memberId);

            $i = 0;
            $savingsSummary = [];
            $totalTransferAmount = 0;

            //    GET ALL THE DETAILS OF ALL THE SAVINGS ACCOUNT OF A MEMBER.
            foreach ($savingsAccount as $savingsAcc) :
                $savingsSummary[$i]['id'] = $savingsAcc['id'];
                $savingsSummary[$i]['savingsProductId'] = $savingsAcc['savingsProductIdFk'];
                /* $savingsSummary[$i]['deposit'] = $this->MicroFinance->getSavingsDepositPerAccountForProductTransferUpdate($savingsAcc['id'],$softwareDate);*/

                $savingsSummary[$i]['deposit'] = DB::table('mfn_savings_deposit')->where('softDel', 0)->where('accountIdFk', $savingsAcc['id'])->where('ledgerIdFk', '!=', 0)->where('primaryProductIdFk', $transfer->oldPrimaryProductFk)->where('depositDate', '<=', $softwareDate)->sum('amount');
                $savingsSummary[$i]['deposit'] += $savOpeningBalances->where('savingsAccIdFk', $savingsAcc->id)->sum('openingBalance');

                /*$savingsSummary[$i]['withdraw'] = $this->MicroFinance->getSavingsWithdrawPerAccountForProductTransferUpdate($savingsAcc['id'],$transferDate);*/

                $savingsSummary[$i]['withdraw'] = DB::table('mfn_savings_withdraw')->where('softDel', 0)->where('accountIdFk', $savingsAcc['id'])->where('ledgerIdFk', '!=', 0)->where('primaryProductIdFk', $transfer->oldPrimaryProductFk)->where('withdrawDate', '<=', $softwareDate)->sum('amount');

                $balance = $savingsSummary[$i]['deposit'] - $savingsSummary[$i]['withdraw'];

                $savingsSummary[$i]['balance'] = $savingsSummary[$i]['deposit'] - $savingsSummary[$i]['withdraw'];
                $totalTransferAmount +=  $savingsSummary[$i]['balance'];
                $i++;

                DB::table('mfn_savings_deposit')->where('accountIdFk', $savingsAcc['id'])->where('depositDate', $softwareDate)->where('isTransferred', 1)->delete();

                $savAcc = DB::table('mfn_savings_account')->where('id', $savingsAcc['id'])->select('savingsProductIdFk', 'branchIdFk', 'samityIdFk')->first();

                $deposit = new MfnSavingsDeposit;
                $deposit->accountIdFk           =  $savingsAcc['id'];
                $deposit->productIdFk           =  $savAcc->savingsProductIdFk;
                $deposit->primaryProductIdFk    =  $transfer->newPrimaryProductFk;
                $deposit->memberIdFk            =  $transfer->memberIdFk;
                $deposit->branchIdFk            =  $savAcc->branchIdFk;
                $deposit->samityIdFk            =  $savAcc->samityIdFk;
                $deposit->amount                =  $balance;
                $deposit->depositDate           =  Carbon::parse($softwareDate);
                $deposit->entryByEmployeeIdFk   =  Auth::user()->emp_id_fk;
                $deposit->isAuthorized          =  1;
                $deposit->isTransferred         =  1;
                $deposit->createdAt             =  Carbon::now();
                $deposit->save();

                DB::table('mfn_savings_withdraw')->where('accountIdFk', $savingsAcc['id'])->where('withdrawDate', $softwareDate)->where('isTransferred', 1)->delete();

                $withdraw = new MfnSavingsWithdraw;
                $withdraw->accountIdFk          =  $savingsAcc['id'];
                $withdraw->productIdFk          =  $savAcc->savingsProductIdFk;
                $withdraw->primaryProductIdFk   =  $transfer->oldPrimaryProductFk;
                $withdraw->memberIdFk           =  $transfer->memberIdFk;
                $withdraw->branchIdFk           =  $savAcc->branchIdFk;
                $withdraw->samityIdFk           =  $savAcc->samityIdFk;
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

    public function updateSchedule()
    {

        ////////////////////

        //  UPDATE SCHEDULE ON FIRST REPAY DATE BASIS.
        $repaymentFrequencyWiseRepayDate = [
            '1'  =>  7,
            '2'  =>  30
        ];

        $microFinance = new MicroFinance;

        $globalGovtHoliday = $microFinance->getGlobalGovtHoliday();
        $organizationHoliday = $microFinance->getOrganizationHoliday(1);

        $targetLoanIds = DB::select("SELECT DISTINCT `loanIdFk` FROM `mfn_loan_schedule` WHERE `softDel`=0 AND scheduleDate='2018-12-25'");
        $targetLoanIds = collect($targetLoanIds);
        $loanIdArr = $targetLoanIds->pluck('loanIdFk')->toArray();

        foreach ($loanIdArr as $loanId) :
            $loanOB = DB::table('mfn_loan')->where('id', $loanId)->select('id', 'branchIdFk', 'samityIdFk', 'repaymentFrequencyIdFk', 'repaymentNo', 'firstRepayDate', 'disbursementDate', 'memberIdFk', 'primaryProductIdFk')->first();

            $branchHoliday = $microFinance->getBranchHolidayNew($loanOB->branchIdFk);
            $samityHoliday = $microFinance->getSamityHolidayWithSamityParam($loanOB->samityIdFk);
            $holiday = array_unique(array_merge($globalGovtHoliday, $organizationHoliday, $branchHoliday, $samityHoliday));

            $holidayFound = 0;
            $scheduleDateArr = [];
            $repaymentFrequencyId = $loanOB->repaymentFrequencyIdFk;
            $repaymentNo = $loanOB->repaymentNo;
            $firstRepayDate = $loanOB->firstRepayDate;
            $disbursementDate = $loanOB->disbursementDate;
            $memberId = $loanOB->memberIdFk;


            for ($i = 0; $i < 1000; $i++) :
                $dayDiff = ($repaymentFrequencyWiseRepayDate[$repaymentFrequencyId] * $i) . 'days';
                $date = date_create($firstRepayDate);
                date_add($date, date_interval_create_from_date_string($dayDiff));
                // dd($date,$dayDiff);
                //  PROPAGATE SCHEDULE DATE FOR WEEKLY FREQUENCY.
                if ($repaymentFrequencyId == 1) :
                    //  CHECK IF A DATE IS MATCHES TO HOLIDAY.
                    foreach ($holiday as $key => $val) :
                        if (date_create($val) >= $date) :
                            if (date_create($val) == $date) :
                                $holidayFound = 1;
                                break;
                            endif;
                        endif;
                    endforeach;

                    if ($holidayFound == 0)
                        $scheduleDateArr[] = date_format($date, "Y-m-d");

                    $holidayFound = 0;
                endif;

                //  PROPAGATE SCHEDULE DATE FOR MONTHLY FREQUENCY.
                if ($repaymentFrequencyId == 2) :
                    $dayDiff = ($repaymentFrequencyWiseRepayDate[$repaymentFrequencyId] * $i) . 'days';
                    $date = date_create($firstRepayDate);
                    date_add($date, date_interval_create_from_date_string($dayDiff));

                    $disbursementDated = date_create($disbursementDate);
                    date_add($disbursementDated, date_interval_create_from_date_string($dayDiff));

                    $tos = Carbon::parse($firstRepayDate);
                    $sot = $tos->addMonthsNoOverflow($i)->toDateString();

                    if ($i == 0)
                        $targetDate = date_format($date, "Y-m-d");
                    else
                        $targetDate = $microFinance->getMonthlyLoanScheduleDateFilter($sot, $memberId);

                    $originalMD = Carbon::parse($targetDate);
                    $MD = Carbon::parse($targetDate);
                    $targetDate = $MD->toDateString();

                    //  CHECK IF A DATE IS MATCHES TO HOLIDAY, THEN SAMITY DATE SET TO NEXT WEEK.
                    for ($j = 0; $j < 100; $j++) :
                        if (in_array($targetDate, $holiday)) :
                            $targetDate = $MD->addDays(7)->toDateString();

                            if ($targetDate > $originalMD->endOfMonth()) :
                                $targetDate = $MD->subDays(14)->toDateString();
                            else :
                                if (in_array($targetDate, $holiday)) :
                                    $targetDate = $MD->addDays(7)->toDateString();

                                    if ($targetDate > $originalMD->endOfMonth()) :
                                        $targetDate = $MD->subDays(21)->toDateString();
                                    endif;
                                else :
                                    break;
                                endif;
                            endif;
                        else :
                            break;
                        endif;
                    endfor;

                    $scheduleDateArr[] = $targetDate;
                endif;

                if (count($scheduleDateArr) == $repaymentNo)
                    break;
            endfor;

            // dd($scheduleDateArr);



            for ($i = 0; $i < $repaymentNo; $i++) :
                if ($scheduleDateArr[$i] >= '2018-12-18') {
                    DB::table('mfn_loan_schedule')->where('softDel', 0)->where('loanIdFk', $loanId)->where('installmentSl', $i + 1)->update(['scheduleDate' => $scheduleDateArr[$i]]);
                }

            endfor;
        endforeach;
        dd(1);
        ////////////////////       
    }


    public function updateScheduleSamityDayChangeWise()
    {

        ////////////////////

        //  UPDATE SCHEDULE ON FIRST REPAY DATE AND SAMITY DAY CHNAGE BASIS.
        $repaymentFrequencyWiseRepayDate = [
            '1'  =>  7,
            '2'  =>  30
        ];

        $microFinance = new MicroFinance;

        $globalGovtHoliday = $microFinance->getGlobalGovtHoliday();
        $organizationHoliday = $microFinance->getOrganizationHoliday(1);

        $targetLoanIds = DB::select("SELECT DISTINCT `loanIdFk` FROM `mfn_loan_schedule` WHERE `softDel`=0 AND `scheduleDate`='2019-03-26'");
        $targetLoanIds = collect($targetLoanIds);
        $loanIdArr = $targetLoanIds->pluck('loanIdFk')->toArray();


        foreach ($loanIdArr as $loanId) :
            $loanOB = DB::table('mfn_loan')->where('id', $loanId)->select('id', 'branchIdFk', 'samityIdFk', 'repaymentFrequencyIdFk', 'repaymentNo', 'firstRepayDate', 'disbursementDate', 'memberIdFk', 'primaryProductIdFk')->first();

            if ($loanOB->repaymentFrequencyIdFk == 0) {
                continue;
            }

            $samityDayChanges = DB::table('mfn_samity_day_change')
                ->where('samityId', $loanOB->samityIdFk)
                ->select('effectiveDate', 'samityDayOrFixedDateId', 'samityDayId', 'newSamityDayId', 'fixedDate', 'newFixedDate')
                ->get();

            $branchHoliday = $microFinance->getBranchHolidayNew($loanOB->branchIdFk);
            $samityHoliday = $microFinance->getSamityHolidayWithSamityParam($loanOB->samityIdFk);
            $holiday = array_unique(array_merge($globalGovtHoliday, $organizationHoliday, $branchHoliday, $samityHoliday));

            $holidayFound = 0;
            $scheduleDateArr = [];
            $repaymentFrequencyId = $loanOB->repaymentFrequencyIdFk;
            $repaymentNo = $loanOB->repaymentNo;
            $firstRepayDate = $loanOB->firstRepayDate;
            $disbursementDate = $loanOB->disbursementDate;
            $memberId = $loanOB->memberIdFk;


            for ($i = 0; $i < 1000; $i++) :

                $dayDiff = ($repaymentFrequencyWiseRepayDate[$repaymentFrequencyId] * $i) . 'days';
                $date = date_create($firstRepayDate);
                date_add($date, date_interval_create_from_date_string($dayDiff));

                // IF SAMITY DAY US CHANGED THEN CONSIDER THE FIRST REPAY DATE AS A NEW SAMITY DAY
                $newSamityDay = null;
                $newSamityDay = $samityDayChanges->where('effectiveDate', '<=', date_format($date, "Y-m-d"))->sortByDesc('effectiveDate')->first();
                if ($newSamityDay != null) {

                    $firstRepayDate = Carbon::parse($loanOB->firstRepayDate);
                    $firstReapayDateWeekDayNumber = $firstRepayDate->dayOfWeek == 6 ? 1 : $firstRepayDate->dayOfWeek + 2;
                    $dayNumber = $firstRepayDate->day;

                    // dd($firstRepayDate,$newSamityDay);


                    if ($newSamityDay->samityDayOrFixedDateId == 1) {
                        if ($newSamityDay->newSamityDayId > $firstReapayDateWeekDayNumber) {
                            $firstRepayDate->addDays($newSamityDay->newSamityDayId - $firstReapayDateWeekDayNumber);
                        } else {
                            $firstRepayDate->addDays(7 + $newSamityDay->newSamityDayId - $firstReapayDateWeekDayNumber);
                        }
                    } else {
                        if ($newSamityDay->newFixedDate > $dayNumber) {
                            $firstRepayDate->addDays($newSamityDay->newFixedDate - $dayNumber);
                        } else {
                            $firstRepayDate->subDays($dayNumber - $newSamityDay->newFixedDate);
                        }
                    }

                    $firstRepayDate = $firstRepayDate->format('Y-m-d');
                    // dd($firstRepayDate,$loanOB->firstRepayDate);
                }

                $dayDiff = ($repaymentFrequencyWiseRepayDate[$repaymentFrequencyId] * $i) . 'days';
                $date = date_create($firstRepayDate);
                date_add($date, date_interval_create_from_date_string($dayDiff));


                // dd($date,$dayDiff);
                //  PROPAGATE SCHEDULE DATE FOR WEEKLY FREQUENCY.
                if ($repaymentFrequencyId == 1) :
                    //  CHECK IF A DATE IS MATCHES TO HOLIDAY.
                    foreach ($holiday as $key => $val) :
                        if (date_create($val) >= $date) :
                            if (date_create($val) == $date) :
                                $holidayFound = 1;
                                break;
                            endif;
                        endif;
                    endforeach;

                    if ($holidayFound == 0)
                        $scheduleDateArr[] = date_format($date, "Y-m-d");

                    $holidayFound = 0;
                endif;

                //  PROPAGATE SCHEDULE DATE FOR MONTHLY FREQUENCY.
                if ($repaymentFrequencyId == 2) :
                    $dayDiff = ($repaymentFrequencyWiseRepayDate[$repaymentFrequencyId] * $i) . 'days';
                    $date = date_create($firstRepayDate);
                    date_add($date, date_interval_create_from_date_string($dayDiff));

                    $disbursementDated = date_create($disbursementDate);
                    date_add($disbursementDated, date_interval_create_from_date_string($dayDiff));

                    $tos = Carbon::parse($firstRepayDate);
                    $sot = $tos->addMonthsNoOverflow($i)->toDateString();

                    if ($i == 0)
                        $targetDate = date_format($date, "Y-m-d");
                    else
                        $targetDate = $microFinance->getMonthlyLoanScheduleDateFilter($sot, $memberId);

                    $originalMD = Carbon::parse($targetDate);
                    $MD = Carbon::parse($targetDate);
                    $targetDate = $MD->toDateString();

                    //  CHECK IF A DATE IS MATCHES TO HOLIDAY, THEN SAMITY DATE SET TO NEXT WEEK.
                    for ($j = 0; $j < 100; $j++) :
                        if (in_array($targetDate, $holiday)) :
                            $targetDate = $MD->addDays(7)->toDateString();

                            if ($targetDate > $originalMD->endOfMonth()) :
                                $targetDate = $MD->subDays(14)->toDateString();
                            else :
                                if (in_array($targetDate, $holiday)) :
                                    $targetDate = $MD->addDays(7)->toDateString();

                                    if ($targetDate > $originalMD->endOfMonth()) :
                                        $targetDate = $MD->subDays(21)->toDateString();
                                    endif;
                                else :
                                    break;
                                endif;
                            endif;
                        else :
                            break;
                        endif;
                    endfor;

                    $scheduleDateArr[] = $targetDate;
                endif;

                if (count($scheduleDateArr) == $repaymentNo)
                    break;
            endfor;

            dd($loanId, $scheduleDateArr);

            for ($i = 0; $i < $repaymentNo; $i++) :
                if ($scheduleDateArr[$i] >= '2019-03-19') {
                    DB::table('mfn_loan_schedule')->where('softDel', 0)->where('loanIdFk', $loanId)->where('installmentSl', $i + 1)->update(['scheduleDate' => $scheduleDateArr[$i]]);
                }

            endfor;

            // update loan last installment date
            DB::table('mfn_loan')->where('id', $loanId)->update(['lastInstallmentDate' => max($scheduleDateArr)]);
        endforeach;
        dd(1);
        ////////////////////       
    }


    public function updateLoanFirstRepayDate()
    {
        $microFinance = new MicroFinance;
        $loanOB = DB::table('mfn_loan')->where('branchIdFk', 15)->select('id', 'firstRepayDate', 'samityIdFk')->get();

        $loanIdArr = [];
        $loanIdArrFRDOk = [];

        foreach ($loanOB as $loan) :
            $samityDayIdLoan = DB::table('mfn_samity')->where('id', $loan->samityIdFk)->value('samityDayId');
            $firstRepayDate = Carbon::parse($loan->firstRepayDate);
            $samityDayName = $firstRepayDate->format('l');
            $samityDayId = $microFinance->getSamityDayId($samityDayName);
            //echo $samityDayId . '<br>';

            if ($samityDayIdLoan != $samityDayId) :
                $loanIdArr[] = $loan->id;
                //  UPDATE FIRST REPAY DATE TO NEXT DAY i.e ADD 1 DAY.
                DB::table('mfn_loan')->where('id', $loan->id)->update(['firstRepayDate' =>  date_add(date_create($loan->firstRepayDate), date_interval_create_from_date_string("1 days"))]);
            endif;
        endforeach;
    }


    public function generateLoanSchedule()
    {

        //  UPDATE SCHEDULE ON FIRST REPAY DATE BASIS.
        $repaymentFrequencyWiseRepayDate = [
            '1'  =>  7,
            '2'  =>  30
        ];

        $microFinance = new MicroFinance;

        $globalGovtHoliday = $microFinance->getGlobalGovtHoliday();
        $organizationHoliday = $microFinance->getOrganizationHoliday(1);

        $loanIdArr = DB::select();

        $loanIdArr = collect($loanIdArr);
        $loanIdArr = $loanIdArr->pluck('id')->toArray();

        $openingBalances = DB::table('mfn_opening_balance_loan')
            ->whereIn('loanIdFk', $loanIdArr)
            ->select('loanIdFk', 'paidLoanAmountOB')
            ->get();

        $collections = DB::table('mfn_loan_collection')
            ->where('softDel', 0)
            ->whereIn('loanIdFk', $loanIdArr)
            ->select('loanIdFk', 'amount');

        foreach ($loanIdArr as $loanId) :
            $loanOB = DB::table('mfn_loan')->where('id', $loanId)->select('branchIdFk', 'loanCode', 'loanTypeId', 'samityIdFk', 'repaymentFrequencyIdFk', 'repaymentNo', 'firstRepayDate', 'disbursementDate', 'memberIdFk', 'primaryProductIdFk', 'installmentAmount', 'interestRateIndex', 'actualInstallmentAmount', 'extraInstallmentAmount')->first();

            $samityDayChanges = DB::table('mfn_samity_day_change')
                ->where('samityId', $loanOB->samityIdFk)
                ->select('effectiveDate', 'samityDayOrFixedDateId', 'samityDayId', 'newSamityDayId', 'fixedDate', 'newFixedDate')
                ->get();

            DB::table('mfn_loan_schedule')->where('loanIdFk', $loanId)->delete();

            $branchHoliday = $microFinance->getBranchHolidayNew($loanOB->branchIdFk);
            $samityHoliday = $microFinance->getSamityHolidayWithSamityParam($loanOB->samityIdFk);
            $holiday = array_unique(array_merge($globalGovtHoliday, $organizationHoliday, $branchHoliday, $samityHoliday));

            $holidayFound = 0;
            $scheduleDateArr = [];
            $repaymentFrequencyId = $loanOB->repaymentFrequencyIdFk;
            $repaymentNo = $loanOB->repaymentNo;
            $firstRepayDate = $loanOB->firstRepayDate;
            $disbursementDate = $loanOB->disbursementDate;
            $memberId = $loanOB->memberIdFk;

            /*echo '<pre>';
                    print_r($loanOB);
                    echo '</pre>';*/

            for ($i = 0; $i < 1000; $i++) :

                $dayDiff = ($repaymentFrequencyWiseRepayDate[$repaymentFrequencyId] * $i) . 'days';
                $date = date_create($firstRepayDate);
                date_add($date, date_interval_create_from_date_string($dayDiff));

                // IF SAMITY DAY US CHANGED THEN CONSIDER THE FIRST REPAY DATE AS A NEW SAMITY DAY
                $newSamityDay = null;
                $newSamityDay = $samityDayChanges->where('effectiveDate', '<=', date_format($date, "Y-m-d"))->sortByDesc('effectiveDate')->first();
                if ($newSamityDay != null) {

                    $firstRepayDate = Carbon::parse($loanOB->firstRepayDate);
                    $firstReapayDateWeekDayNumber = $firstRepayDate->dayOfWeek == 6 ? 1 : $firstRepayDate->dayOfWeek + 2;
                    $dayNumber = $firstRepayDate->day;

                    // dd($firstRepayDate,$newSamityDay);


                    if ($newSamityDay->samityDayOrFixedDateId == 1) {
                        if ($newSamityDay->newSamityDayId > $firstReapayDateWeekDayNumber) {
                            $firstRepayDate->addDays($newSamityDay->newSamityDayId - $firstReapayDateWeekDayNumber);
                        } else {
                            $firstRepayDate->addDays(7 + $newSamityDay->newSamityDayId - $firstReapayDateWeekDayNumber);
                        }
                    } else {
                        if ($newSamityDay->newFixedDate > $dayNumber) {
                            $firstRepayDate->addDays($newSamityDay->newFixedDate - $dayNumber);
                        } else {
                            $firstRepayDate->subDays($dayNumber - $newSamityDay->newFixedDate);
                        }
                    }

                    $firstRepayDate = $firstRepayDate->format('Y-m-d');
                    // dd($firstRepayDate,$loanOB->firstRepayDate);
                }

                $dayDiff = ($repaymentFrequencyWiseRepayDate[$repaymentFrequencyId] * $i) . 'days';
                $date = date_create($firstRepayDate);
                date_add($date, date_interval_create_from_date_string($dayDiff));


                // dd($date,$dayDiff);
                //  PROPAGATE SCHEDULE DATE FOR WEEKLY FREQUENCY.
                if ($repaymentFrequencyId == 1) :
                    //  CHECK IF A DATE IS MATCHES TO HOLIDAY.
                    foreach ($holiday as $key => $val) :
                        if (date_create($val) >= $date) :
                            if (date_create($val) == $date) :
                                $holidayFound = 1;
                                break;
                            endif;
                        endif;
                    endforeach;

                    if ($holidayFound == 0)
                        $scheduleDateArr[] = date_format($date, "Y-m-d");

                    $holidayFound = 0;
                endif;

                //  PROPAGATE SCHEDULE DATE FOR MONTHLY FREQUENCY.
                if ($repaymentFrequencyId == 2) :
                    $dayDiff = ($repaymentFrequencyWiseRepayDate[$repaymentFrequencyId] * $i) . 'days';
                    $date = date_create($firstRepayDate);
                    date_add($date, date_interval_create_from_date_string($dayDiff));

                    $disbursementDated = date_create($disbursementDate);
                    date_add($disbursementDated, date_interval_create_from_date_string($dayDiff));

                    $tos = Carbon::parse($firstRepayDate);
                    $sot = $tos->addMonthsNoOverflow($i)->toDateString();

                    if ($i == 0)
                        $targetDate = date_format($date, "Y-m-d");
                    else
                        $targetDate = $microFinance->getMonthlyLoanScheduleDateFilter($sot, $memberId);

                    $originalMD = Carbon::parse($targetDate);
                    $MD = Carbon::parse($targetDate);
                    $targetDate = $MD->toDateString();

                    //  CHECK IF A DATE IS MATCHES TO HOLIDAY, THEN SAMITY DATE SET TO NEXT WEEK.
                    for ($j = 0; $j < 100; $j++) :
                        if (in_array($targetDate, $holiday)) :
                            $targetDate = $MD->addDays(7)->toDateString();

                            if ($targetDate > $originalMD->endOfMonth()) :
                                $targetDate = $MD->subDays(14)->toDateString();
                            else :
                                if (in_array($targetDate, $holiday)) :
                                    $targetDate = $MD->addDays(7)->toDateString();

                                    if ($targetDate > $originalMD->endOfMonth()) :
                                        $targetDate = $MD->subDays(21)->toDateString();
                                    endif;
                                else :
                                    break;
                                endif;
                            endif;
                        else :
                            break;
                        endif;
                    endfor;

                    $scheduleDateArr[] = $targetDate;
                endif;

                if (count($scheduleDateArr) == $repaymentNo)
                    break;
            endfor;

            $paidAmount = $openingBalances->where('loanIdFk', $loanId)->sum('paidLoanAmountOB');
            $paidAmount += $collections->where('loanIdFk', $loanId)->sum('amount');

            $curPaidAmount = $paidAmount;

            for ($i = 0; $i < $repaymentNo; $i++) :

                $principalAmount = round($loanOB->installmentAmount / $loanOB->interestRateIndex, 2);
                $interestAmount = $loanOB->installmentAmount - $principalAmount;

                if ($paidAmount >= $loanOB->installmentAmount) {
                    $isCompleted = 1;
                    $isPartiallyPaid = 0;
                    $partiallyPaidAmount = 0;
                } elseif ($paidAmount > 0) {
                    $isCompleted = 0;
                    $isPartiallyPaid = 1;
                    $partiallyPaidAmount = $paidAmount;
                } else {
                    $isCompleted = 0;
                    $isPartiallyPaid = 0;
                    $partiallyPaidAmount = 0;
                }

                DB::table('mfn_loan_schedule')->insert([
                    'loanIdFk'                  => $loanId,
                    'loanCode'                  => $loanOB->loanCode,
                    'loanTypeId'                => $loanOB->loanTypeId,
                    'installmentSl'             => $i + 1,
                    'installmentAmount'         => $loanOB->installmentAmount,
                    'actualInstallmentAmount'   => $loanOB->actualInstallmentAmount,
                    'extraInstallmentAmount'    => $loanOB->extraInstallmentAmount,
                    'principalAmount'           => $principalAmount,
                    'interestAmount'            => $interestAmount,
                    'scheduleDate'              => $scheduleDateArr[$i],
                    'isCompleted'               => $isCompleted,
                    'isPartiallyPaid'           => $isPartiallyPaid,
                    'partiallyPaidAmount'       => $partiallyPaidAmount,
                    'createdDate'               => Carbon::now(),
                    'status'                    => 1,
                    'softDel'                   => 0,
                    'ds'                        => 0
                ]);

                $paidAmount = $paidAmount - $loanOB->installmentAmount;
            endfor;

            DB::table('mfn_loan')->where('id', $loanId)->update(['lastInstallmentDate' => end($scheduleDateArr)]);
        endforeach;

        dd(1);
    }

    public function getMemberWiseSavingsBalance()
    {

        $branchId = 87;
        $endDateValue = '2018-09-24';

        $dbMembers = DB::table('mfn_member_information')
            ->where('softDel', 0)
            ->where('branchId', $branchId)
            ->where('admissionDate', '<=', $endDateValue)
            ->orderBy('samityId', $branchId)
            ->select('id', 'primaryProductId', 'samityId', 'code')
            ->get();

        ////// update transfer

        $primaryProductTransfers = DB::table('mfn_loan_primary_product_transfer')
            ->where('softDel', 0)
            ->where('branchIdFk', $branchId)
            ->where('transferDate', '>', $endDateValue)
            ->get();

        $primaryProductTransfers = $primaryProductTransfers->sortBy('transferDate')->unique('memberIdFk');

        foreach ($primaryProductTransfers as $key => $primaryProductTransfer) {
            if ($dbMembers->where('id', $primaryProductTransfer->memberIdFk)->first() != null) {
                $dbMembers->where('id', $primaryProductTransfer->memberIdFk)->first()->primaryProductId = $primaryProductTransfer->oldPrimaryProductFk;
            }
        }

        $samityTransfers = DB::table('mfn_member_samity_transfer')
            ->where('softDel', 0)
            ->where('branchIdFk', $branchId)
            ->where('transferDate', '>', $endDateValue)
            ->get();

        $samityTransfers = $samityTransfers->sortBy('transferDate')->unique('memberIdFk');

        foreach ($samityTransfers as $key => $samityTransfer) {
            if ($dbMembers->where('id', $samityTransfer->memberIdFk)->first() != null) {
                $dbMembers->where('id', $samityTransfer->memberIdFk)->first()->samityId = $samityTransfer->previousSamityIdFk;
            }
        }

        ////// end updating transfer

        $deposits = DB::table('mfn_savings_deposit')
            ->where('softDel', 0)
            ->where('branchIdFk', $branchId)
            ->where('depositDate', '<=', $endDateValue)
            ->get();

        $withdraws = DB::table('mfn_savings_withdraw')
            ->where('softDel', 0)
            ->where('branchIdFk', $branchId)
            ->where('withdrawDate', '<=', $endDateValue)
            ->get();

        $totalBalance = $deposits->sum('amount') - $withdraws->sum('amount');

        echo 'Total Balance: ' . $totalBalance . '<br><br>';

        $samities = DB::table('mfn_samity')
            ->where('branchId', $branchId)
            ->select('id', 'name', 'code')
            ->get();

        foreach ($samities as $key => $samity) {
            $samityTotalBalance = $deposits->where('samityIdFk', $samity->id)->sum('amount') - $withdraws->where('samityIdFk', $samity->id)->sum('amount');

            echo '<br>Samity Code: ' . $samity->code . '<br>';
            echo 'Samity Total Balance: ' . $samityTotalBalance . '<br><br>';
            $curMembers = $dbMembers->where('samityId', $samity->id);

            foreach ($curMembers as $key => $dbMember) {
                $balance = $deposits->where('memberIdFk', $dbMember->id)->sum('amount') - $withdraws->where('memberIdFk', $dbMember->id)->sum('amount');
                echo "Member Code: " . $dbMember->code . ' Balance: ' . $balance . '<br>';
            }
        }
    }

    public function updateTransferDepositWithdraw()
    {

        $memberIdsFromnegetiveDeposits = DB::table('mfn_savings_deposit')
            ->where('softDel', 0)
            ->where('amount', '<', 0)
            ->where('isTransferred', 1)
            ->groupBy('memberIdFk')
            ->pluck('memberIdFk')
            ->toArray();

        $primaryProductTransfers = DB::table('mfn_loan_primary_product_transfer')
            ->where('softDel', 0)
            ->whereIn('memberIdFk', $memberIdsFromnegetiveDeposits)
            ->offset(10)
            ->limit(20)
            ->select('memberIdFk', 'transferDate')
            ->get();

        // dd($primaryProductTransfers);

        $deposits = DB::table('mfn_savings_deposit')
            ->where('softDel', 0)
            ->whereIn('memberIdFk', $primaryProductTransfers->pluck('memberIdFk'))
            ->where('isTransferred', 0)
            ->get();

        $withdraws = DB::table('mfn_savings_withdraw')
            ->where('softDel', 0)
            ->whereIn('memberIdFk', $primaryProductTransfers->pluck('memberIdFk'))
            ->where('isTransferred', 0)
            ->get();

        $openingBalances = DB::table('mfn_opening_savings_account_info')
            ->whereIn('memberIdFk', $primaryProductTransfers->pluck('memberIdFk'))
            ->get();

        $savingsAccounts = DB::table('mfn_savings_account')
            ->where('softDel', 0)
            ->whereIn('memberIdFk', $primaryProductTransfers->pluck('memberIdFk'))
            ->get();

        foreach ($primaryProductTransfers as $primaryProductTransfer) {

            // get the savings account of this memeber
            $savAccs = $savingsAccounts->where('memberIdFk', $primaryProductTransfer->memberIdFk)->where('accountOpeningDate', '<=', $primaryProductTransfer->transferDate);

            foreach ($savAccs as $savAcc) {
                // actual information
                $actualDeposit = $deposits->where('isTransferred', 0)->where('accountIdFk', $savAcc->id)->where('depositDate', '<=', $primaryProductTransfer->transferDate)->sum('amount');
                $actualDeposit += $openingBalances->where('savingsAccIdFk', $savAcc->id)->sum('openingBalance');

                $actualWithdraw = $withdraws->where('isTransferred', 0)->where('accountIdFk', $savAcc->id)->where('withdrawDate', '<=', $primaryProductTransfer->transferDate)->sum('amount');
                $actualBalance = $actualDeposit - $actualWithdraw;

                // transfer information
                $transferDeposit = $deposits->where('isTransferred', 1)->where('accountIdFk', $savAcc->id)->where('depositDate', '=', $primaryProductTransfer->transferDate)->sum('amount');
                $transferWithdraw = $withdraws->where('isTransferred', 1)->where('accountIdFk', $savAcc->id)->where('withdrawDate', '=', $primaryProductTransfer->transferDate)->sum('amount');

                echo '$savAcc->id : ' . $savAcc->id . '<br>';
                echo '$savAcc->savingsCode : ' . $savAcc->savingsCode . '<br>';
                echo 'transfer date : ' . $primaryProductTransfer->transferDate . '<br>';
                echo '$actualDeposit : ' . $actualDeposit . '<br>';
                echo 'opening Balance : ' . $openingBalances->where('savingsAccIdFk', $savAcc->id)->sum('openingBalance') . '<br>';
                echo '$actualWithdraw : ' . $actualWithdraw . '<br>';
                echo '$actualBalance : ' . $actualBalance . '<br>';
                echo '$transferDeposit : ' . $transferDeposit . '<br>';
                echo '$transferWithdraw : ' . $transferWithdraw . '<br><br>';
            }
        }
    }

    public function getNegetiveTransferDeposits()
    {

        $negetiveDeposits = DB::table('mfn_savings_deposit')
            ->where('softDel', 0)
            ->where('amount', '<', 0)
            ->where('isTransferred', 1)
            /*->offset(0)
                                                ->limit(10)*/
            ->get();

        $deposits = DB::table('mfn_savings_deposit')
            ->where('softDel', 0)
            ->whereIn('memberIdFk', $negetiveDeposits->pluck('memberIdFk'))
            ->where('isTransferred', 0)
            ->get();

        $withdraws = DB::table('mfn_savings_withdraw')
            ->where('softDel', 0)
            ->whereIn('memberIdFk', $negetiveDeposits->pluck('memberIdFk'))
            ->where('isTransferred', 0)
            ->get();

        $openingBalances = DB::table('mfn_opening_savings_account_info')
            ->whereIn('memberIdFk', $negetiveDeposits->pluck('memberIdFk'))
            ->get();

        $savingsAccounts = DB::table('mfn_savings_account')
            ->where('softDel', 0)
            ->whereIn('memberIdFk', $negetiveDeposits->pluck('memberIdFk'))
            ->get();

        foreach ($negetiveDeposits as $negetiveDeposit) {

            // get the savings account of this memeber
            $savAcc = $savingsAccounts->where('id', $negetiveDeposit->accountIdFk)->first();


            // actual information
            $actualDeposit = $deposits->where('isTransferred', 0)->where('accountIdFk', $savAcc->id)->where('depositDate', '<=', $negetiveDeposit->depositDate)->sum('amount');
            // $actualDeposit += $openingBalances->where('savingsAccIdFk',$savAcc->id)->sum('openingBalance');

            $actualWithdraw = $withdraws->where('isTransferred', 0)->where('accountIdFk', $savAcc->id)->where('withdrawDate', '<=', $negetiveDeposit->depositDate)->sum('amount');
            $actualBalance = $actualDeposit - $actualWithdraw;

            // transfer information
            /* $transferDeposit = $deposits->where('isTransferred',1)->where('accountIdFk',$savAcc->id)->where('depositDate','=',$negetiveDeposit->depositDate)->sum('amount');
               $transferWithdraw = $withdraws->where('isTransferred',1)->where('accountIdFk',$savAcc->id)->where('withdrawDate','=',$negetiveDeposit->depositDate)->sum('amount');*/

            echo '$savAcc->id : ' . $savAcc->id . '<br>';
            echo '$savAcc->savingsCode : ' . $savAcc->savingsCode . '<br>';
            echo 'transfer date : ' . $negetiveDeposit->depositDate . '<br>';
            echo '$actualDeposit : ' . $actualDeposit . '<br>';
            echo 'opening Balance : ' . $openingBalances->where('savingsAccIdFk', $savAcc->id)->sum('openingBalance') . '<br>';
            echo '$actualWithdraw : ' . $actualWithdraw . '<br>';
            echo '$actualBalance : ' . $actualBalance . '<br>';
            echo 'negetive deposit : ' . $negetiveDeposit->amount . '<br><br>';
        }
    }


    public function updateTransfers()
    {

        $negetiveDeposits = DB::table('mfn_savings_deposit')
            ->where('softDel', 0)
            ->where('amount', '<', 0)
            ->where('isTransferred', 1)
            /*->offset(0)
        ->limit(5)*/
            ->get();

        $branchIds = $negetiveDeposits->unique('branchIdFk')->pluck('branchIdFk')->toArray();

        foreach ($branchIds as $branchId) {
            $dates = $negetiveDeposits->where('branchIdFk', $branchId)->unique('depositDate')->pluck('depositDate')->toArray();

            foreach ($dates as $date) {
                $this->undateTransfer($branchId, $date);
                app('App\Http\Controllers\microfin\process\RedoAutoVouchersController')->redoAutoVouchers($branchId, $date);
            }
        }
    }

    public function updateClosingNegetiveValues()
    {

        $withdrawsFromClosing = DB::table('mfn_savings_withdraw')
            ->where('softDel', 0)
            ->where('amount', '<', 0)
            ->where('isFromClosing', 1)
            ->get();

        $branchIds = $withdrawsFromClosing->sortBy('branchIdFk')->unique('branchIdFk')->pluck('branchIdFk')->toArray();

        $infos = [];

        foreach ($branchIds as $branchId) {
            $dates = $withdrawsFromClosing->where('branchIdFk', $branchId)->unique('withdrawDate')->pluck('withdrawDate')->toArray();

            foreach ($dates as $date) {
                $data = array(
                    'branchId' => $branchId,
                    'date' => $date
                );

                array_push($infos, $data);
            }
        }

        foreach ($withdrawsFromClosing as $withdraw) {
            $amount = -$withdraw->amount;
            DB::table('mfn_savings_withdraw')
                ->where('id', $withdraw->id)
                ->update(['amount' => $amount]);
        }

        foreach ($infos as $info) {
            app('App\Http\Controllers\microfin\process\RedoAutoVouchersController')->redoAutoVouchers($info['branchId'], $info['date']);
        }

        dd($infos);
    }

    public function getNegetiveBalaneLoans()
    {

        $branchId = 36;

        $loans = DB::table('mfn_loan')
            ->where('softDel', 0)
            ->where('branchIdFk', $branchId)
            ->select('id', 'branchIdFk', 'loanAmount', 'totalRepayAmount', 'loanCompletedDate')
            ->get();

        foreach ($loans as $loan) {

            $openingBalance = DB::table('mfn_opening_balance_loan')
                ->where('loanIdFk', $loan->id)
                ->sum('paidLoanAmountOB');

            $collectionAmount = $openingBalance;

            $realCollectionAmount = DB::table('mfn_loan_collection')
                ->where('softDel', 0)
                ->where('amount', '>', 0)
                ->where('loanIdFk', $loan->id)
                ->sum('amount');

            $maxCollectionDate = DB::table('mfn_loan_collection')
                ->where('softDel', 0)
                ->where('amount', '>', 0)
                ->where('loanIdFk', $loan->id)
                ->max('collectionDate');

            $collectionAmount += $realCollectionAmount;

            $waiverPrincipalAmount =  DB::table('mfn_loan_waivers')
                ->where('loanIdFk', $loan->id)
                ->sum('principalAmount');

            $collectionAmount += $waiverPrincipalAmount;

            $rebateAmount =  DB::table('mfn_loan_rebates')
                ->where('loanIdFk', $loan->id)
                ->sum('amount');

            $rebateDate = DB::table('mfn_loan_rebates')
                ->where('loanIdFk', $loan->id)
                ->max('date');

            $collectionAmount += $rebateAmount;

            $writeOffAmount =  DB::table('mfn_loan_write_off')
                ->where('loanIdFk', $loan->id)
                ->sum('amount');

            $collectionAmount += $writeOffAmount;

            $maxDate = max($maxCollectionDate, $rebateDate);

            if ($collectionAmount > $loan->totalRepayAmount && $loan->loanCompletedDate == '0000-00-00') {
                echo '$loan->id: ' . $loan->id . '<br>';
                echo '$loan->branchIdFk: ' . $loan->branchIdFk . '<br>';
                echo '$collectionAmount: ' . $collectionAmount . '<br>';
                echo '$loan->totalRepayAmount: ' . $loan->totalRepayAmount . '<br>';
                echo 'extraAmount: ' . ($collectionAmount - $loan->totalRepayAmount) . '<br>';
                echo '$openingBalance: ' . $openingBalance . '<br>';
                echo '$realCollectionAmount: ' . $realCollectionAmount . '<br>';
                echo '$waiverPrincipalAmount: ' . $waiverPrincipalAmount . '<br>';
                echo '$rebateAmount: ' . $rebateAmount . '<br>';
                echo '$writeOffAmount: ' . $writeOffAmount . '<br>';
                echo '$maxCollectionDate: ' . $maxCollectionDate . '<br>';
                echo '$rebateDate: ' . $rebateDate . '<br>';
                echo '$maxDate: ' . $maxDate . '<br><br>';
            }
        }
    }

    public function completeClosedMembersActivities()
    {

        $branchId = 20;

        // TAKE THE ENTRY BY INFORMATION FROM HR TABLE
        $entryBy = (int) DB::table('hr_emp_org_info')
            ->where('branch_id_fk', $branchId)
            ->where(function ($query) {
                $query->where('position_id_fk', 125)
                    ->orWhere('position_id_fk', 122)
                    ->orWhere('position_id_fk', 127);
            })
            ->where('job_status', 'Present')
            ->value('emp_id_fk');

        $closedMembers = DB::table('mfn_member_information AS mem')
            ->join('mfn_member_closing AS memClo', 'mem.id', 'memClo.memberIdFk')
            ->where('mem.softDel', 0)
            ->where('mem.branchId', $branchId)
            ->where('memClo.softDel', 0)
            ->where('mem.closingDate', '0000-00-00')
            ->select('mem.id', 'mem.code', 'memClo.closingDate', 'mem.branchId', 'mem.primaryProductId')
            ->get();

        $autoVoucherDates = [];

        foreach ($closedMembers as $closedMember) {

            $activeLoans = DB::table('mfn_loan')
                ->where('softDel', 0)
                ->where('memberIdFk', $closedMember->id)
                ->where('isLoanCompleted', 0)
                ->select('id', 'loanCode')
                ->select('id', 'loanCode', 'loanAmount', 'totalRepayAmount', 'loanTypeId')
                ->get();

            if (count($activeLoans) > 0) {
                echo 'Active Loan exits for: ' . $closedMember->code . '<br>';
            }

            foreach ($activeLoans as $activeLoan) {
                echo 'Loan Code: ' . $activeLoan->loanCode . '<br>';
            }

            $savAccs = DB::table('mfn_savings_account')
                ->where('softDel', 0)
                ->where('memberIdFk', $closedMember->id)
                ->get();

            foreach ($savAccs as $savAcc) {
                $openingBalance = DB::table('mfn_opening_savings_account_info')
                    ->where('savingsAccIdFk', $savAcc->id)
                    ->sum('openingBalance');

                $depositAmount = DB::table('mfn_savings_deposit')
                    ->where('softDel', 0)
                    ->where('accountIdFk', $savAcc->id)
                    ->sum('amount');

                $withdrawAmount = DB::table('mfn_savings_withdraw')
                    ->where('softDel', 0)
                    ->where('accountIdFk', $savAcc->id)
                    ->sum('amount');

                $balance = $openingBalance + $depositAmount - $withdrawAmount;

                if ($balance > 0) {
                    echo 'Member Code: ' . $closedMember->code . '<br>';
                    echo 'Savings Code: ' . $savAcc->savingsCode . '<br>';
                    echo 'Balance: ' . $balance . '<br><br>';

                    // IF NO ACTIVE LOAN EXITS THEN MAKE AN EQUIVALENT WITHDRAW ON THE CLOSING DATE
                    if (count($activeLoans) == 0) {
                        /*$withdraw = new MfnSavingsWithdraw;
                            $withdraw->memberIdFk               = $closedMember->id;
                            $withdraw->branchIdFk               = $closedMember->branchId;
                            $withdraw->samityIdFk               = $savAcc->samityIdFk;
                            $withdraw->accountIdFk              = $savAcc->id;
                            $withdraw->productIdFk              = $savAcc->savingsProductIdFk;
                            $withdraw->primaryProductIdFk       = $closedMember->primaryProductId;
                            $withdraw->amount                   = $balance;
                            $withdraw->balanceBeforeWithdraw    = 0;
                            $withdraw->WithdrawDate             = $closedMember->closingDate;
                            $withdraw->paymentType              = 'Cash';
                            $withdraw->ledgerIdFk               = 412;
                            $withdraw->entryByEmployeeIdFk      = $entryBy;
                            $withdraw->createdAt                = Carbon::now();
                            $withdraw->save();*/

                        array_push($autoVoucherDates, $closedMember->closingDate);
                    }
                }
                // get the negetive balance info
                elseif ($balance < 0) {
                    echo 'Negetive Balance Detected' . '<br>';
                    echo 'Member Code: ' . $closedMember->code . '<br>';
                    echo 'Savings Code: ' . $savAcc->savingsCode . '<br>';
                    echo 'Balance: ' . $balance . '<br><br>';
                }

                // CLOSE THE SAVINGS ACCOUNT IF NOT CLOSED
                $isAlreadyClosed = (int) DB::table('mfn_savings_closing')->where('softDel', 0)->where('accountIdFk', $savAcc->id)->value('id');

                if ($isAlreadyClosed == 0) {

                    /*DB::table('mfn_savings_closing')
                            ->insert(
                                [
                                    'branchIdFk'            => $savAcc->branchIdFk,
                                    'memberIdFk'            => $savAcc->memberIdFk,
                                    'accountIdFk'           => $savAcc->id,
                                    'depositAmount'         => 0,
                                    'payableAmount'         => 0,
                                    'totalSavingInterest'   => 0,
                                    'closingAmount'         => $balance,
                                    'closingDate'           => $closedMember->closingDate,
                                    'paymentType'           => 'Cash',
                                    'ledgerIdFk'            => 412,
                                    'chequeNumber'          => '',
                                    'entryByEmployeeIdFk'   => $entryBy,
                                    'status'                => 1,
                                    'softDel'               => 0
                                ]
                            );*/

                    //DB::table('mfn_savings_account')->where('id',$savAcc->id)->update(['closingDate'=>$closedMember->closingDate]);
                }
            }

            // IF HAS LOAN THEN DELETE THE CLOSING INFORMATION
            if (count($activeLoans) > 0) {
                /*DB::table('mfn_member_closing')
                        ->where('memberIdFk',$closedMember->id)
                        ->update(['softDel' => 1]);*/ } else {
                // IF NO ACTIVE LOAN UPDATE THE MEMBER STATUS AND CLOSING DATE
                /*DB::table('mfn_member_information')
                        ->where('id',$closedMember->id)
                        ->update(['status' => 0, 'closingDate' => $closedMember->closingDate]);*/ }
        }

        // redo the auto vouchres on the savings withdraw dates
        $autoVoucherDates = array_unique($autoVoucherDates);

        /*foreach ($autoVoucherDates as $autoVoucherDate) {
                app('App\Http\Controllers\microfin\process\RedoAutoVouchersController')->redoAutoVouchers($branchId,$autoVoucherDate);

                // if month end summery needed than do accounting month end summery
                $accLastMonthEndDate = DB::table('acc_month_end')->where('branchIdFk',$branchId)->max('date');
                if ($autoVoucherDate<=$accLastMonthEndDate) {
                    $accService = new Service();
                    $accService->monthEndExecute($branchId,Carbon::parse($autoVoucherDate)->endOfMonth()->format('Y-m-d'));
                    echo 'voucher date: '.$autoVoucherDate.'<br>';
                }
            }*/

        $branch = DB::table('gnr_branch')->where('id', $branchId)->select('name', 'branchCode')->first();

        echo $branch->branchCode . ' - ' . $branch->name . ' Finished';
        dd();
    }


    public function updateLoanCollectionLoanIndexWise()
    {
        $loanIds = [201958];

        foreach ($loanIds as $loanId) {
            $loanOB = DB::table('mfn_loan')->where('id', $loanId)->select('branchIdFk', 'loanCode', 'loanTypeId', 'samityIdFk', 'repaymentFrequencyIdFk', 'repaymentNo', 'firstRepayDate', 'disbursementDate', 'memberIdFk', 'primaryProductIdFk', 'installmentAmount', 'interestRateIndex', 'actualInstallmentAmount', 'extraInstallmentAmount')->first();

            $collections = DB::table('mfn_loan_collection')
                ->where('softDel', 0)
                ->where('amount', '>', 0)
                ->where('loanIdFk', $loanId)
                ->select('id', 'amount', 'collectionDate')
                ->get();

            foreach ($collections as $collection) {
                $principalAmount = round($collection->amount / $loanOB->interestRateIndex, 5);
                $interestAmount = round($collection->amount - $principalAmount, 5);

                DB::table('mfn_loan_collection')->where('id', $collection->id)->update(['principalAmount' => $principalAmount, 'interestAmount' => $interestAmount]);
                app('App\Http\Controllers\microfin\process\RedoAutoVouchersController')->redoAutoVouchers($loanOB->branchIdFk, $collection->collectionDate);
            }
        }

        dd('done');
    }

    public function fixClosingSavingsAccounts()
    {

        $targetAccounts = DB::table('mfn_savings_account AS sa')
            ->join('mfn_savings_closing AS sc', 'sc.accountIdFk', 'sa.id')
            ->where('sa.softDel', 0)
            ->where('sc.softDel', 0)
            ->where('sa.closingDate', '0000-00-00')
            ->limit(200)
            ->select('sa.id', 'sc.closingDate', 'sa.savingsCode')
            ->get();

        if (count($targetAccounts) == 0) {
            dd('fin ished');
        }

        $openingBalance = DB::table('mfn_opening_savings_account_info')
            ->whereIn('savingsAccIdFk', $targetAccounts->pluck('id')->toArray())
            ->select('savingsAccIdFk', 'openingBalance')
            ->get();

        $deposits = DB::table('mfn_savings_deposit')
            ->where('softDel', 0)
            ->whereIn('accountIdFk', $targetAccounts->pluck('id')->toArray())
            ->select(DB::raw('accountIdFk,SUM(amount) AS amount'))
            ->groupBy('accountIdFk')
            ->get();

        // dd($openingBalance);

        $withdraws = DB::table('mfn_savings_withdraw')
            ->where('softDel', 0)
            ->whereIn('accountIdFk', $targetAccounts->pluck('id')->toArray())
            ->select(DB::raw('accountIdFk,SUM(amount) AS amount'))
            ->groupBy('accountIdFk')
            ->get();

        foreach ($targetAccounts as $savAcc) {
            $balance = $openingBalance->where('savingsAccIdFk', $savAcc->id)->sum('openingBalance');
            $balance += $deposits->where('accountIdFk', $savAcc->id)->sum('amount');
            $balance -= $withdraws->where('accountIdFk', $savAcc->id)->sum('amount');

            if ($balance == 0) {
                /*echo '$savAcc: '.$savAcc->id.'<br>';
                    echo 'savingsCode: '.$savAcc->savingsCode.'<br>';
                    echo 'Balance Is zero: '.$balance.'<br><br>';*/


                DB::table('mfn_savings_account')
                    ->where('id', $savAcc->id)
                    ->update([
                        'status' => 0,
                        'closingDate' => $savAcc->closingDate
                    ]);
            } elseif ($balance > 0) {
                /*echo '$savAcc: '.$savAcc->id.'<br>';
                    echo 'savingsCode: '.$savAcc->savingsCode.'<br>';
                    echo 'Balance: '.$balance.'<br><br>';*/

                DB::table('mfn_savings_account')
                    ->where('id', $savAcc->id)
                    ->update([
                        'status' => 1,
                        'closingDate' => '0000-00-00'
                    ]);
            }
        }
    }
}
