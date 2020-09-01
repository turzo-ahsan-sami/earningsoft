<?php

namespace App\Http\Controllers\microfin\process;

use DB;
use Carbon\Carbon;    
use App\Http\Controllers\Controller;
use App\microfin\process\MfnMonthEndProcessTotalMembers;
use App\microfin\process\MfnMonthEndProcessMembers;
use App\microfin\process\MfnMonthEndProcessSavings; 
use App\microfin\process\MfnMonthEndProcessLoan; 
use App\microfin\process\MfnMonthEndProcessLoanTest; 
use App\microfin\process\MfnMonthEndProcessStaffInfo;
use App\microfin\process\MfnMonthEndProcessLoanAreaInfo;
use App\Http\Controllers\microfin\MicroFin;
use Auth;


class MonthEndStoreInfoTOFastProcess {

    private $targetBranchId;
    private $monthFirstDate;
    private $monthEndDate;
    private $isCustomCall;

    function __construct($targetBranchId,$monthFirstDate,$monthEndDate){

        // If the $monthFirstDate is null, it is assumed that is is the custom call from the 'UpdateMonthEndInfoController' controller.
        if($monthFirstDate == null){
            $this->monthFirstDate = Carbon::parse($monthEndDate)->startOfMonth()->format('Y-m-d');
            $this->isCustomCall = 1;
        }
        else{
            $this->monthFirstDate = $monthFirstDate;
            $this->isCustomCall = 0;
        }

        $this->targetBranchId = $targetBranchId;
        
        $this->monthEndDate = $monthEndDate;
    }

    public function saveData(){

        $this->storeLoanInfo();
        // dd();        
    }

    /**
     * this function store data to "mfn_month_end_process_loans" table
     * @return void
     */
    public function storeLoanInfo(){

        DB::table('mfn_month_end_process_loans_test')->where('branchIdFk',$this->targetBranchId)->where('date',$this->monthEndDate)->delete();

        $monthFirstDate = $this->monthFirstDate;
        $monthEndDate = $this->monthEndDate;

        $datas = $this->getLoanDetails();
        // return 1;

        foreach ($datas as $data) {
            $mEloanInfo = new MfnMonthEndProcessLoanTest;
            $mEloanInfo->date                                                   = Carbon::parse($this->monthEndDate);
            $mEloanInfo->branchIdFk                                             = $this->targetBranchId;
            $mEloanInfo->productIdFk                                            = $data->productIdFk;
            $mEloanInfo->genderTypeId                                           = $data->genderTypeId;
            $mEloanInfo->openingBorrowerNo                                      = $data->openingBorrowerNo;
            $mEloanInfo->openingOutstandingAmount                               = $data->openingOutstandingAmount;
            $mEloanInfo->openingOutstandingAmountWithServicesCharge             = $data->openingOutstandingAmountWithServicesCharge;
            $mEloanInfo->openingDisbursedAmount                                 = $data->openingDisbursedAmount;
            $mEloanInfo->borrowerNo                                             = $data->borrowerNo;
            // $mEloanInfo->borrowerNo_bt                                          = $data->borrowerNo_bt;
            $mEloanInfo->disbursedAmount                                        = $data->disbursedAmount;
            $mEloanInfo->repayAmount                                            = $data->repayAmount;
            // $mEloanInfo->disbursedAmount_bt                                     = $data->disbursedAmount_bt;
            $mEloanInfo->principalRecoveryAmount                                = $data->principalRecoveryAmount;
            // $mEloanInfo->principalRecoveryAmount_bt                             = $data->principalRecoveryAmount_bt;
            $mEloanInfo->recoveryAmount                                         = $data->recoveryAmount;
            // $mEloanInfo->recoveryAmount_bt                                      = $data->recoveryAmount_bt;
            $mEloanInfo->fullyPaidBorrowerNo                                    = $data->fullyPaidBorrowerNo;
            // $mEloanInfo->fullyPaid_borrowerNo_bt                                = $data->fullyPaid_borrowerNo_bt;
            $mEloanInfo->closingBorrowerNo                                      = $data->closingBorrowerNo;
            $mEloanInfo->closingOutstandingAmount                               = $data->closingOutstandingAmount;
            $mEloanInfo->closingOutstandingAmountWithServicesCharge             = $data->closingOutstandingAmountWithServicesCharge;
            $mEloanInfo->closingDisbursedAmount                                 = $data->closingDisbursedAmount;

            $mEloanInfo->openingDueAmount                                       = $data->openingDueAmount;
            $mEloanInfo->openingDueAmountWithServicesCharge                     = $data->openingDueAmountWithServicesCharge;

            $mEloanInfo->principalRecoverableAmount                             = $data->principalRecoverableAmount;
            $mEloanInfo->recoverableAmount                                      = $data->recoverableAmount;
            $mEloanInfo->principalRegularAmount                                 = $data->principalRegularAmount;
            $mEloanInfo->regularAmount                                          = $data->regularAmount;
            $mEloanInfo->principalAdvanceAmount                                 = $data->principalAdvanceAmount;
            $mEloanInfo->advanceAmount                                          = $data->advanceAmount;
            $mEloanInfo->principalDueAmount                                     = $data->principalDueAmount;
            $mEloanInfo->dueAmount                                              = $data->dueAmount;

            $mEloanInfo->principalNewDueAmount                                  = $data->principalNewDueAmount;
            $mEloanInfo->newDueAmount                                           = $data->newDueAmount;
            $mEloanInfo->closingDueAmount                                       = $data->closingDueAmount;
            $mEloanInfo->closingDueAmountWithServicesCharge                     = $data->closingDueAmountWithServicesCharge;
            $mEloanInfo->noOfDueLoanee                                          = $data->noOfDueLoanee;
            // $mEloanInfo->totalNoOfDueLoaneeOnlyOptionalProduct                  = $data->totalNoOfDueLoaneeOnlyOptionalProduct;
            $mEloanInfo->noOfUniqueLoanee                                       = $data->noOfUniqueLoanee;
            $mEloanInfo->loanRebateAmount                                       = $data->loanRebateAmount;
            $mEloanInfo->cumBorrowerNo                                          = $data->cumBorrowerNo;
            $mEloanInfo->cumLoanNo                                              = $data->cumLoanNo;

            $mEloanInfo->watchfulOutstanding                                    = $data->watchfulOutstanding;
            $mEloanInfo->watchfulOutstandingWithServicesCharge                  = $data->watchfulOutstandingWithServicesCharge;
            $mEloanInfo->watchfulOverdue                                        = $data->watchfulOverdue;
            $mEloanInfo->watchfulOverdueWithServicesCharge                      = $data->watchfulOverdueWithServicesCharge;
            $mEloanInfo->substandardOutstanding                                 = $data->substandardOutstanding;
            $mEloanInfo->substandardOutstandingWithServicesCharge               = $data->substandardOutstandingWithServicesCharge;
            $mEloanInfo->substandardOverdue                                     = $data->substandardOverdue;
            $mEloanInfo->substandardOverdueWithServicesCharge                   = $data->substandardOverdueWithServicesCharge;
            $mEloanInfo->doubtfullOutstanding                                   = $data->doubtfullOutstanding;
            $mEloanInfo->doubtfullOutstandingWithServicesCharge                 = $data->doubtfullOutstandingWithServicesCharge;
            $mEloanInfo->doubtfullOverdue                                       = $data->doubtfullOverdue;
            $mEloanInfo->doubtfullOverdueWithServicesCharge                     = $data->doubtfullOverdueWithServicesCharge;
            $mEloanInfo->badOutstanding                                         = $data->badOutstanding;
            $mEloanInfo->badOutstandingWithServicesCharge                       = $data->badOutstandingWithServicesCharge;
            $mEloanInfo->badOverdue                                             = $data->badOverdue;
            $mEloanInfo->badOverdueWithServicesCharge                           = $data->badOverdueWithServicesCharge;
            $mEloanInfo->outstandingWithMoreThan2DueInstallments                = $data->outstandingWithMoreThan2DueInstallments;
            $mEloanInfo->outstandingWithMoreThan2DueInstallmentsServicesCharge  = $data->outstandingWithMoreThan2DueInstallmentsServicesCharge;
            $mEloanInfo->savingBalanceOfOverdueLoanee                           = $data->savingBalanceOfOverdueLoanee;

            $mEloanInfo->createdAt                                              = Carbon::now();
            $mEloanInfo->updatedAt                                              = Carbon::now();

            $mEloanInfo->save();
        }        

    }

    public function getLoanDetails(){

        $time_start = microtime(true);

        $monthFirstDate = $this->monthFirstDate;
        $monthEndDate = $this->monthEndDate;
        $monthEndDateOB = Carbon::parse($this->monthEndDate);

        $loans = DB::table('mfn_loan AS loan')
        ->join('mfn_member_information AS mi', 'mi.id', 'loan.memberIdFk')
        ->where([['loan.softDel',0], ['loan.branchIdFk',$this->targetBranchId], ['loan.disbursementDate','<=',$monthEndDate]])
        ->where(function ($query) use ($monthFirstDate,$monthEndDate){
            $query->where('loan.loanCompletedDate','0000-00-00')
            ->orWhere('loan.loanCompletedDate','>=',$monthFirstDate);
        })
        ->select('mi.gender','mi.id AS memberId','loan.id','loan.loanCode','loan.productIdFk','loan.loanAmount','loan.totalRepayAmount','loan.disbursementDate','loan.loanCompletedDate','loan.loanTypeId','loan.loanRepayPeriodIdFk')
        ->get();

        $schedules = DB::table('mfn_loan_schedule')
        ->where('softDel',0)
        ->whereIn('loanIdFk',$loans->pluck('id')->toArray())
        ->where('scheduleDate','>=',$monthFirstDate)
        ->where('scheduleDate','<=',$monthEndDate)
        ->select(DB::raw("loanIdFk,SUM(installmentAmount) AS installmentAmount, SUM(principalAmount) AS principalAmount"))
        ->groupBy('loanIdFk')
        ->get();

        $openingSchedules = DB::table('mfn_loan_schedule')
        ->where('softDel',0)
        ->whereIn('loanIdFk',$loans->pluck('id')->toArray())
        ->where('scheduleDate','<',$monthFirstDate)
        ->select(DB::raw("loanIdFk,SUM(installmentAmount) AS installmentAmount, SUM(principalAmount) AS principalAmount"))
        ->groupBy('loanIdFk')
        ->get();

        $collections = DB::table('mfn_loan_collection')
        ->where('softDel',0)
        ->whereIn('loanIdFk',$loans->pluck('id')->toArray())
        ->where('collectionDate','>=',$monthFirstDate)
        ->where('collectionDate','<=',$monthEndDate)
        ->select(DB::raw("loanIdFk,SUM(amount) AS amount, SUM(principalAmount) AS principalAmount"))
        ->groupBy('loanIdFk')
        ->get();

        $openingCollections = DB::table('mfn_loan_collection')
        ->where('softDel',0)
        ->whereIn('loanIdFk',$loans->pluck('id')->toArray())
        ->where('collectionDate','<',$monthFirstDate)
        ->select(DB::raw("loanIdFk,SUM(amount) AS amount, SUM(principalAmount) AS principalAmount"))
        ->groupBy('loanIdFk')
        ->get();

        $openingBalances = DB::table('mfn_opening_balance_loan')
        ->where('softDel',0)
        ->whereIn('loanIdFk',$loans->pluck('id')->toArray())
        ->select('loanIdFk','paidLoanAmountOB','principalAmountOB')
        ->get();

        $repayPeriods = DB::table('mfn_loan_repay_period')->select('id','inMonths')->get();

        $data = collect([]);


        foreach ($loans as $loan) {

            $loanData = collect();

            // GET OPENING INFO
            $openingBalanceAmount = (float) $openingBalances->where('loanIdFk',$loan->id)->sum('paidLoanAmountOB');

            $openingRecoverableAmount = (float) $openingSchedules->where('loanIdFk',$loan->id)->sum('installmentAmount');
            $openingCollectionAmount = $openingBalanceAmount;
            $openingCollectionAmount += (float) $openingCollections->where('loanIdFk',$loan->id)->sum('amount');
            $openingCollectionAmount += DB::table('mfn_loan_waivers')->where('loanIdFk',$loan->id)->where('date','<',$monthFirstDate)->sum('amount');
            $openingCollectionAmount += DB::table('mfn_loan_rebates')->where('softDel',0)->where('loanIdFk',$loan->id)->where('date','<',$monthFirstDate)->sum('amount');
            $openingCollectionAmount += DB::table('mfn_loan_write_off')->where('softDel',0)->where('loanIdFk',$loan->id)->where('date','<',$monthFirstDate)->sum('amount');

            $openingDueAmount = $openingRecoverableAmount - $openingCollectionAmount;
            $openingAdvanceAmount = - $openingDueAmount;

           /* if ($loan->id==193513) {
                echo '$openingRecoverableAmount: '.$openingRecoverableAmount.'<br>';
                echo '$openingBalanceAmount: '.$openingBalanceAmount.'<br>';
                echo '$openingCollectionAmount: '.$openingCollectionAmount.'<br>';
                echo '$openingDueAmount: '.$openingDueAmount.'<br>';
            }*/

            $openingDueAmount = $openingDueAmount <= 0 ? 0 : $openingDueAmount;
            $openingAdvanceAmount = $openingAdvanceAmount <= 0 ? 0 : $openingAdvanceAmount;

            // GET THIS MONTH INFO
            $thisMonthRecoverable = (float) $schedules->where('loanIdFk',$loan->id)->sum('installmentAmount');
            $recoverableAmount = max(0 ,$thisMonthRecoverable + $openingDueAmount - $openingAdvanceAmount);

            $collectionAmount = (float) $collections->where('loanIdFk',$loan->id)->sum('amount');
            $collectionAmount += DB::table('mfn_loan_waivers')->where('loanIdFk',$loan->id)->where('date','>=',$monthFirstDate)->where('date','<=',$monthEndDate)->sum('amount');
            $collectionAmount += DB::table('mfn_loan_rebates')->where('softDel',0)->where('loanIdFk',$loan->id)->where('date','>=',$monthFirstDate)->where('date','<=',$monthEndDate)->sum('amount');
            $collectionAmount += DB::table('mfn_loan_write_off')->where('softDel',0)->where('loanIdFk',$loan->id)->where('date','>=',$monthFirstDate)->where('date','<=',$monthEndDate)->sum('amount');

            $dueRecoveryAmount = min($collectionAmount, $openingDueAmount);
            $regularRecoveryAmount = min($recoverableAmount, $collectionAmount - $dueRecoveryAmount);
            $regularRecoveryAmount = max(0, $regularRecoveryAmount);
            $advanceAmount = $collectionAmount - $regularRecoveryAmount - $dueRecoveryAmount;
            $advanceAmount = $advanceAmount < 0 ? 0 : $advanceAmount;

            $disbursedAmount = $loan->disbursementDate >= $monthFirstDate ? $loan->loanAmount : 0;
            $repayAmount = $loan->disbursementDate >= $monthFirstDate ? $loan->totalRepayAmount : 0;

            $outstanding = $loan->totalRepayAmount - $openingCollectionAmount - $collectionAmount;
            $outstanding = $outstanding < 0 ? 0 : $outstanding;

            $newDueAmount = max(0 ,$thisMonthRecoverable - $regularRecoveryAmount - $openingAdvanceAmount);

            $closingDueAmountWithServicesCharge = max(0, $openingRecoverableAmount + $thisMonthRecoverable - $openingCollectionAmount - $collectionAmount);

            $loanRebateAmount = DB::table('mfn_loan_rebates')->where('softDel',0)->where('loanIdFk',$loan->id)->where('date','>=',$monthFirstDate)->where('date','<=',$monthEndDate)->sum('amount');            

            $loanData->id                   = $loan->id;
            $loanData->loanCode             = $loan->loanCode;
            $loanData->productIdFk          = $loan->productIdFk;
            $loanData->genderTypeId         = $loan->gender;
            $loanData->memberId             = $loan->memberId;
            $loanData->disbursementDate     = $loan->disbursementDate;
            $loanData->loanCompletedDate    = $loan->loanCompletedDate;
            $loanData->loanTypeId           = $loan->loanTypeId;
            $loanData->disbursedAmount      = $disbursedAmount;
            $loanData->repayAmount          = $repayAmount;
            $loanData->recoveryAmount       = $collectionAmount;

            $loanData->recoverableAmount    = $recoverableAmount;
            $loanData->closingOutstandingAmountWithServicesCharge        = $outstanding;
            $loanData->dueAmount            = $dueRecoveryAmount;
            $loanData->regularAmount        = $regularRecoveryAmount;
            $loanData->advanceAmount        = $advanceAmount;
            $loanData->newDueAmount         = $newDueAmount;
            $loanData->closingDueAmountWithServicesCharge   = $closingDueAmountWithServicesCharge;
            $loanData->loanRebateAmount     = $loanRebateAmount;

            //// FOR PRINCIPAL AMOUNT
            
            // GET OPENING INFO (PRINCIPAL)
            $openingBalanceAmount = (float) $openingBalances->where('loanIdFk',$loan->id)->sum('principalAmountOB');

            $openingRecoverableAmount = (float) $openingSchedules->where('loanIdFk',$loan->id)->sum('principalAmount');
            $openingCollectionAmount = $openingBalanceAmount;
            $openingCollectionAmount += (float) $openingCollections->where('loanIdFk',$loan->id)->sum('principalAmount');
            $openingCollectionAmount += DB::table('mfn_loan_waivers')->where('loanIdFk',$loan->id)->where('date','<',$monthFirstDate)->sum('principalAmount');            
            $openingCollectionAmount += DB::table('mfn_loan_write_off')->where('softDel',0)->where('loanIdFk',$loan->id)->where('date','<',$monthFirstDate)->sum('principalAmount');

            $openingDueAmount = $openingRecoverableAmount - $openingCollectionAmount;
            $openingAdvanceAmount = - $openingDueAmount;

            $openingDueAmount = $openingDueAmount < 1 ? 0 : $openingDueAmount;
            $openingAdvanceAmount = $openingAdvanceAmount < 1 ? 0 : $openingAdvanceAmount;

            // GET THIS MONTH INFO (PRINCIPAL)
            $thisMonthRecoverable = (float) $schedules->where('loanIdFk',$loan->id)->sum('principalAmount');
            $recoverableAmount = max(0 ,$thisMonthRecoverable + $openingDueAmount - $openingAdvanceAmount);

            $collectionAmount = (float) $collections->where('loanIdFk',$loan->id)->sum('principalAmount');
            $collectionAmount += DB::table('mfn_loan_waivers')->where('loanIdFk',$loan->id)->where('date','>=',$monthFirstDate)->where('date','<=',$monthEndDate)->sum('principalAmount');
            $collectionAmount += DB::table('mfn_loan_write_off')->where('softDel',0)->where('loanIdFk',$loan->id)->where('date','>=',$monthFirstDate)->where('date','<=',$monthEndDate)->sum('principalAmount');

            $dueRecoveryAmount = min($collectionAmount, $openingDueAmount) < 1 ? 0 : min($collectionAmount, $openingDueAmount);
            $regularRecoveryAmount = min($recoverableAmount, $collectionAmount - $dueRecoveryAmount);
            $regularRecoveryAmount = max(0, $regularRecoveryAmount);

            /////////////
            if (abs($regularRecoveryAmount - $recoverableAmount)<1 && abs($regularRecoveryAmount - $recoverableAmount)>0) {
                /*echo 'abs($regularRecoveryAmount - $recoverableAmount): '.abs($regularRecoveryAmount - $recoverableAmount).'<br>';
                echo '$regularRecoveryAmount: '.$regularRecoveryAmount.'<br>';*/
                $regularRecoveryAmount = $recoverableAmount;
                /*echo '$regularRecoveryAmount: '.$regularRecoveryAmount.'<br>';
                echo '$recoverableAmount: '.$recoverableAmount.'<br><br>';*/
                
            }
            /////////////

            


            $advanceAmount = $collectionAmount - $regularRecoveryAmount - $dueRecoveryAmount;
            $advanceAmount = $advanceAmount < 0 ? 0 : $advanceAmount;

            $repayAmount = $loan->disbursementDate >= $monthFirstDate ? $loan->totalRepayAmount : 0;

            $outstanding = $loan->loanAmount - $openingCollectionAmount - $collectionAmount;
            $outstanding = $outstanding < 0 ? 0 : $outstanding;

            $newDueAmount = $thisMonthRecoverable - $regularRecoveryAmount - $openingAdvanceAmount < 1 ? 0 : $thisMonthRecoverable - $regularRecoveryAmount - $openingAdvanceAmount;
            

            // $closingDueAmount = max(0, $openingRecoverableAmount + $thisMonthRecoverable - $openingCollectionAmount - $collectionAmount);
            $closingDueAmount = $openingRecoverableAmount + $thisMonthRecoverable - $openingCollectionAmount - $collectionAmount;
            $closingDueAmount = $closingDueAmount < 1 ? 0 : $closingDueAmount;

            // $newDueAmount = $closingDueAmount - $openingDueAmount < 1 ? 0 : $closingDueAmount - $openingDueAmount;

            $loanData->principalRecoveryAmount    = $collectionAmount;
            $loanData->principalRecoverableAmount = max(0 ,$thisMonthRecoverable - $openingAdvanceAmount);
            $loanData->closingOutstandingAmount   = $outstanding;
            $loanData->principalDueAmount         = $dueRecoveryAmount;
            $loanData->principalRegularAmount     = $regularRecoveryAmount;
            $loanData->principalAdvanceAmount     = $advanceAmount;
            $loanData->principalNewDueAmount      = $newDueAmount;
            $loanData->closingDueAmount           = $closingDueAmount;
            $loanData->openingDueAmount           = $openingDueAmount;

            // clasify this loan, e.g. Standard (no due), Watchful (1-30 Days), Substandard(31-180 Days), Doubtful(181-365), Bad Loan(365+)
            if ($closingDueAmount<=0) {
                $loanData->classificationTag = 'Standard';
                $loanData->numOfDueInstallment = 0;
            }
            else{
                // calculate the last due schedule or partially paid schedule date.
                $loanId = $loan->id;
                $totalPrincipalCollectionAmount = $openingCollectionAmount + $collectionAmount;

                $lastUnpaidScheduleDate = DB::select("SELECT MIN(scheduleDate) AS scheduleDate,COUNT(id) AS numOfDueInstallment FROM `mfn_loan_schedule` AS t1 WHERE softDel=0 AND `loanIdFk`=? AND (SELECT SUM(principalAmount) FROM mfn_loan_schedule WHERE softDel=0 AND `loanIdFk`=t1.`loanIdFk` AND `scheduleDate`<=t1.`scheduleDate`) + 1 > ? AND scheduleDate<=?", [$loanId,$totalPrincipalCollectionAmount,$monthEndDate]);

                $lastUnpaidScheduleDate = collect($lastUnpaidScheduleDate);
                $loanData->numOfDueInstallment = $lastUnpaidScheduleDate[0]->numOfDueInstallment;
                $lastUnpaidScheduleDate = Carbon::parse($lastUnpaidScheduleDate[0]->scheduleDate);  


                // GET THE LOAN REPAY PERIOD
                $loanYear = $repayPeriods->where('id',$loan->loanRepayPeriodIdFk)->first()->inMonths/12;
                $daysDifference = $monthEndDateOB->diffInDays($lastUnpaidScheduleDate);
                $daysDifference = (int) $daysDifference/$loanYear;

                if ($daysDifference<=30) {
                    $loanData->classificationTag = 'Watchful';
                }
                elseif ($daysDifference>30 && $daysDifference<=180) {
                    $loanData->classificationTag = 'Substandard';
                }
                elseif ($daysDifference>180 && $daysDifference<=365) {
                    $loanData->classificationTag = 'Doubtful';
                }
                else{
                    $loanData->classificationTag = 'Bad Loan';
                }
            }

            /*if (abs($newDueAmount - ($loanData->principalRecoverableAmount - $regularRecoveryAmount)) > 1) {
                echo '$loan->id: '.$loan->id.'<br>';
                echo '$loan->loanCode: '.$loan->loanCode.'<br>';
                echo '$openingDueAmount: '.$openingDueAmount.'<br>';
                echo '$openingAdvanceAmount: '.$openingAdvanceAmount.'<br>';
                echo '$thisMonthRecoverable: '.$thisMonthRecoverable.'<br>';
                echo '$collectionAmount: '.$collectionAmount.'<br>';
                echo '$dueRecoveryAmount: '.$dueRecoveryAmount.'<br>';
                echo '$advanceAmount: '.$advanceAmount.'<br>';
                echo '$regularRecoveryAmount: '.$regularRecoveryAmount.'<br>';

                echo '$newDueAmount: '.$newDueAmount.'<br>';
                
                echo 'recoverableAmount: '.$recoverableAmount.'<br>';

                echo '$loanData->principalRecoverableAmount: '.$loanData->principalRecoverableAmount.'<br><br>';
            }*/

           /* if ($loan->id==184535) {
                echo '$loan->id: '.$loan->id.'<br>';
                echo '$openingDueAmount: '.$openingDueAmount.'<br>';
                echo '$openingAdvanceAmount: '.$openingAdvanceAmount.'<br>';
                echo '$thisMonthRecoverable: '.$thisMonthRecoverable.'<br>';
                echo '$recoverableAmount: '.$recoverableAmount.'<br>';
                echo '$dueRecoveryAmount: '.$dueRecoveryAmount.'<br>';
                echo '$regularRecoveryAmount: '.$regularRecoveryAmount.'<br>';
                echo '$advanceAmount: '.$advanceAmount.'<br>';
                echo '$newDueAmount: '.$newDueAmount.'<br>';             
                echo '$closingDueAmount: '.$closingDueAmount.'<br>';             
                echo '$collectionAmount: '.$collectionAmount.'<br>';             
                echo '$totalPrincipalCollectionAmount: '.$totalPrincipalCollectionAmount.'<br>';             
                echo '$lastUnpaidScheduleDate: '.$lastUnpaidScheduleDate->format('Y-m-d').'<br><br>';

                echo '$openingRecoverableAmount: '.$openingRecoverableAmount.'<br>';
                echo '$thisMonthRecoverable: '.$thisMonthRecoverable.'<br>';
                echo '$openingCollectionAmount: '.$openingCollectionAmount.'<br>';
                echo '$collectionAmount: '.$collectionAmount.'<br>';
            }*/

            $data->push($loanData);
        }

        // dd();

        /*echo '<pre>';
        print_r($data->where('productIdFk',1)->where('classificationTag','!=','Standard'));
        echo '</pre>';*/

        $targetLoans = $data->where('principalNewDueAmount','>',0);

        foreach ($targetLoans as $targetLoan) {
            /*if (abs($targetLoan->closingDueAmount - ($targetLoan->openingDueAmount-$targetLoan->principalDueAmount+$targetLoan->principalNewDueAmount)) >1) {
                echo '$targetLoan->id: '.$targetLoan->id.'<br>';
                echo '$targetLoan->loanCode: '.$targetLoan->loanCode.'<br>';
                echo '$targetLoan->openingDueAmount: '.$targetLoan->openingDueAmount.'<br>';
                
                echo '$targetLoan->principalDueAmount: '.$targetLoan->principalDueAmount.'<br>';

                echo '$targetLoan->principalNewDueAmount: '.$targetLoan->principalNewDueAmount.'<br>';
                echo '$targetLoan->closingDueAmount: '.$targetLoan->closingDueAmount.'<br><br>';
            }*/

            
        }

        // dd($targetLoans->sum('principalNewDueAmount'));

       /* echo 'closingOutstandingAmount: '.$data->sum('closingOutstandingAmount').'<br>';
        echo 'principalRecoveryAmount: '.$data->sum('principalRecoveryAmount').'<br>';
        echo 'closingDueAmount: '.$data->where('closingDueAmount','<=',1)->sum('closingDueAmount').'<br><br><br>';

        $dueLoans = $data;

        foreach ($dueLoans as $key => $loan) {
            echo 'loanCode: '. $loan->loanCode.'<br>';
            echo 'principalRecoveryAmount: '. $loan->principalRecoveryAmount.'<br>';
            echo 'closingOutstandingAmount: '. $loan->closingOutstandingAmount.'<br>';

            echo 'closingDueAmount: '. number_format($loan->closingDueAmount,20).'<br><br>';
        }
        dd();*/

        // get the opening summary data
        $lastMonthEndDate = Carbon::parse($monthFirstDate)->subDay()->format('Y-m-d');
        $openingInfos = DB::table('mfn_month_end_process_loans')
        ->where('branchIdFk',$this->targetBranchId)
        ->where('date',$lastMonthEndDate)
        ->select('productIdFk','genderTypeId','closingBorrowerNo','closingOutstandingAmount','closingOutstandingAmountWithServicesCharge','closingDisbursedAmount','closingDueAmount','openingDueAmountWithServicesCharge','cumLoanNo')
        ->get();

        $loanProductIds = array_unique(array_merge($openingInfos->unique('productIdFk')->pluck('productIdFk')->toArray(), $data->unique('productIdFk')->pluck('productIdFk')->toArray()));

        $genderIds = [1,2]; // 1 FOR MALE, 2 FOR FEMALE

        $summaryData = collect();

        $loanProducts = DB::table('mfn_loans_product')->select('id','name')->get();

        foreach ($loanProductIds as $loanProductId) {
            foreach ($genderIds as $genderId) {

                $openingInfo = $openingInfos->where('productIdFk',$loanProductId)->where('genderTypeId',$genderId);
                $currentData = $data->where('productIdFk',$loanProductId)->where('genderTypeId',$genderId);

                $productData = collect();

                // fullyPaidBorrowerNo
                $memberIdsHavingLoan = array_unique(array_merge($currentData->where('loanCompletedDate','0000-00-00')->pluck('memberId')->toArray() , $currentData->where('loanCompletedDate','>',$monthEndDate)->pluck('memberId')->toArray()));
                $memberIdsHavingCompletedLoan = $currentData->where('loanCompletedDate','>=',$monthFirstDate)->where('loanCompletedDate','<=',$monthEndDate)->unique('memberId')->pluck('memberId')->toArray();

                $fullyPaidBorrowerNo = count(array_diff($memberIdsHavingCompletedLoan, $memberIdsHavingLoan));

                $productData->productIdFk       = $loanProductId;
                $productData->productIdName      = $loanProducts->where('id',$loanProductId)->max('name');
                $productData->genderTypeId      = $genderId;
                $productData->openingBorrowerNo = $openingInfo->sum('closingBorrowerNo');
                $productData->openingOutstandingAmount = $openingInfo->sum('closingOutstandingAmount');
                $productData->openingOutstandingAmountWithServicesCharge = $openingInfo->sum('closingOutstandingAmountWithServicesCharge');
                $productData->openingDisbursedAmount = $openingInfo->sum('closingDisbursedAmount');
                $productData->disbursedAmount = $currentData->where('disbursementDate','>=',$monthFirstDate)->sum('disbursedAmount');
                $productData->borrowerNo = count($currentData->where('disbursementDate','>=',$monthFirstDate)->unique('memberId')->pluck('memberId')->toArray());
                $productData->repayAmount = $currentData->sum('repayAmount');
                $productData->principalRecoveryAmount = $currentData->sum('principalRecoveryAmount');
                $productData->recoveryAmount = $currentData->sum('recoveryAmount');
                $productData->fullyPaidBorrowerNo = $fullyPaidBorrowerNo;
                // $productData->closingBorrowerNo = count($memberIdsHavingLoan) - $fullyPaidBorrowerNo;
                $productData->closingBorrowerNo = $productData->openingBorrowerNo + $productData->borrowerNo - $productData->fullyPaidBorrowerNo;
                $productData->closingOutstandingAmount = $currentData->sum('closingOutstandingAmount');
                $productData->closingOutstandingAmountWithServicesCharge = $currentData->sum('closingOutstandingAmountWithServicesCharge');
                $productData->closingDisbursedAmount = $openingInfo->sum('closingDisbursedAmount') + $currentData->where('disbursementDate','>=',$monthFirstDate)->sum('disbursedAmount');
                $productData->openingDueAmount = $openingInfo->sum('closingDueAmount');
                $productData->openingDueAmountWithServicesCharge = $openingInfo->sum('closingDueAmountWithServicesCharge');
                $productData->principalRecoverableAmount = $currentData->sum('principalRecoverableAmount');
                $productData->recoverableAmount = $currentData->sum('recoverableAmount');
                $productData->principalRegularAmount = $currentData->sum('principalRegularAmount');
                $productData->regularAmount = $currentData->sum('regularAmount');
                $productData->principalAdvanceAmount = $currentData->sum('principalAdvanceAmount');
                $productData->advanceAmount = $currentData->sum('advanceAmount');
                $productData->principalDueAmount = $currentData->sum('principalDueAmount');
                $productData->dueAmount = $currentData->sum('dueAmount');
                $productData->principalNewDueAmount = $currentData->sum('principalNewDueAmount');
                $productData->newDueAmount = $currentData->sum('newDueAmount');
                $productData->closingDueAmount = $currentData->sum('closingDueAmount');
                $productData->closingDueAmountWithServicesCharge = $currentData->sum('closingDueAmountWithServicesCharge');
                $productData->noOfDueLoanee = $currentData->where('classificationTag','!=','Standard')->unique('memberId')->count();
                // $productData->totalNoOfDueLoaneeOnlyOptionalProduct = ;
                $productData->noOfUniqueLoanee = $currentData->unique('memberId')->count();
                $productData->loanRebateAmount = $currentData->sum('loanRebateAmount');
                $productData->cumBorrowerNo = $productData->openingBorrowerNo + $productData->borrowerNo;
                $productData->cumLoanNo = $openingInfo->sum('cumLoanNo') + $currentData->where('disbursementDate','>=',$monthFirstDate)->count();
                $productData->watchfulOutstanding = $currentData->where('classificationTag','Watchful')->sum('closingOutstandingAmount');
                $productData->watchfulOutstandingWithServicesCharge = $currentData->where('classificationTag','Watchful')->sum('closingOutstandingAmountWithServicesCharge');
                $productData->watchfulOverdue = $currentData->where('classificationTag','Watchful')->sum('closingDueAmount');
                $productData->watchfulOverdueWithServicesCharge = $currentData->where('classificationTag','Watchful')->sum('watchfulOverdueWithServicesCharge');

                $productData->substandardOutstanding = $currentData->where('classificationTag','Substandard')->sum('closingOutstandingAmount');
                $productData->substandardOutstandingWithServicesCharge = $currentData->where('classificationTag','Substandard')->sum('closingOutstandingAmountWithServicesCharge');
                $productData->substandardOverdue = $currentData->where('classificationTag','Substandard')->sum('closingDueAmount');
                $productData->substandardOverdueWithServicesCharge = $currentData->where('classificationTag','Substandard')->sum('watchfulOverdueWithServicesCharge');

                $productData->doubtfullOutstanding = $currentData->where('classificationTag','Doubtful')->sum('closingOutstandingAmount');
                $productData->doubtfullOutstandingWithServicesCharge = $currentData->where('classificationTag','Doubtful')->sum('closingOutstandingAmountWithServicesCharge');
                $productData->doubtfullOverdue = $currentData->where('classificationTag','Doubtful')->sum('closingDueAmount');
                $productData->doubtfullOverdueWithServicesCharge = $currentData->where('classificationTag','Doubtful')->sum('watchfulOverdueWithServicesCharge');

                $productData->badOutstanding = $currentData->where('classificationTag','Bad Loan')->sum('closingOutstandingAmount');
                $productData->badOutstandingWithServicesCharge = $currentData->where('classificationTag','Bad Loan')->sum('closingOutstandingAmountWithServicesCharge');
                $productData->badOverdue = $currentData->where('classificationTag','Bad Loan')->sum('closingDueAmount');
                $productData->badOverdueWithServicesCharge = $currentData->where('classificationTag','Bad Loan')->sum('watchfulOverdueWithServicesCharge');

                $productData->outstandingWithMoreThan2DueInstallments = $currentData->where('numOfDueInstallment','>',2)->sum('closingOutstandingAmount');
                $productData->outstandingWithMoreThan2DueInstallmentsServicesCharge = $currentData->where('numOfDueInstallment','>',2)->sum('closingOutstandingAmountWithServicesCharge');

                // SAVINGS BALACE OF OVER DUE LOANE
                $savingsOpeningBalance = DB::table('mfn_opening_savings_account_info')
                ->where('softDel',0)
                ->whereIn('memberIdFk',$currentData->where('classificationTag','!=','Standard')->unique('memberId')->pluck('memberId')->toArray())
                ->sum('openingBalance');

                $depositAmount = DB::table('mfn_savings_deposit')
                ->where('softDel',0)
                ->where('depositDate','<=',$monthEndDate)
                ->whereIn('memberIdFk',$currentData->where('classificationTag','!=','Standard')->unique('memberId')->pluck('memberId')->toArray())
                ->sum('amount');

                $withdrawAmount = DB::table('mfn_savings_withdraw')
                ->where('softDel',0)
                ->where('withdrawDate','<=',$monthEndDate)
                ->whereIn('memberIdFk',$currentData->where('classificationTag','!=','Standard')->unique('memberId')->pluck('memberId')->toArray())
                ->sum('amount');

                $productData->savingBalanceOfOverdueLoanee = $savingsOpeningBalance + $depositAmount - $withdrawAmount;

                $summaryData->push($productData);
            }
        }

        $execution_time = (microtime(true) - $time_start);

        
        // dd($execution_time.' sec',memory_get_usage()/(1024*1024*8).' MB',$summaryData);

        return $summaryData;
    }

}
