<?php

namespace App\Http\Controllers\microfin\process;

use DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\accounting\AddVoucher;
use App\accounting\AddVoucherDetails;
use App\accounting\AccMfnAutoVoucherRef;
use Auth;
use App\Http\Controllers\accounting\Accounting;
use App\Http\Controllers\microfin\MicroFinance;

class MfnAutoVoucher
{

    protected $MicroFinance;
    protected $Accounting;

    protected $branchId;
    protected $softwareDate;

    protected $vouchers;
    protected $voucherDetails;

    protected $savingsProducts;
    protected $LoanProducts;

    private $dbLoan;
    private $dbSavDeposit;
    private $dbSavWithdraw;
    private $dbMembers;
    private $dbLoanCollection;

    public function __construct($consData)
    {

        $this->MicroFinance = new MicroFinance;
        $this->Accounting = new Accounting;

        $this->branchId = $consData['targetBranchId'];
        $this->softwareDate = $consData['softwareDate'];

        $this->vouchers = DB::table('acc_voucher')->where('projectId', 1)->where('branchId', $consData['targetBranchId'])->where('voucherDate', $consData['softwareDate'])->where('vGenerateType', 1)->where('moduleIdFk', 6)/*->where('voucherFor','!=','InterestProbation')*/->select('id', 'voucherTypeId')->get();

        $this->voucherDetails = DB::table('acc_voucher_details')->whereIn('voucherId', $this->vouchers->pluck('id'))->select('id', 'voucherId', 'debitAcc', 'creditAcc')->get();

        $this->savingsProducts = DB::table('mfn_saving_product')->select('id', 'name')->get();
        $this->loanProducts = DB::table('mfn_loans_product')->select('id', 'name')->get();

        $this->dbSavDeposit = $consData['dbSavDeposit'];
        $this->dbSavWithdraw = $consData['dbSavWithdraw'];
        $this->dbLoan = $consData['dbLoan'];
        $this->dbMembers = $consData['dbMembers'];
        $this->dbLoanCollection = $consData['dbLoanCollection'];
    }

    public function createCreditVoucher()
    {
        $cashInHandLedgerId = DB::table('acc_account_ledger')->where('accountTypeId', 4)->where('isGroupHead', 0)->value('id');
        // Trasactions counted - Member Admission Fee
        // Trasactions counted - Savings Deposit
        // Trasactions counted - Loan Disbursement Fees, Insurence Fee, Loan Collection, WriteOff Collection
        $memberAdmissionFee = $this->dbMembers->where('admissionDate', $this->softwareDate);

        $savingsDeposits = $this->dbSavDeposit->where('depositDate', $this->softwareDate)->where('isTransferred', 0)->where('amount', '>', 0)->where('paymentType', '!=', 'Interest');

        $loansFees = $this->dbLoan->where('disbursementDate', $this->softwareDate);

        $loanCollections = $this->dbLoanCollection->where('collectionDate', $this->softwareDate);

        $writeOffCollections = DB::table('mfn_loan_write_off_collection')->where('branchIdFk', $this->branchId)->where('date', $this->softwareDate)->select('loanProductIdFk', 'loanIdFk', 'amount')->get();

        $voucherId = $this->storeOrUpdateVoucher(2);
        DB::table('acc_voucher_details')->where('voucherId', $voucherId)->delete();

        $hasTraExits = max(count($savingsDeposits), count($loansFees), count($loanCollections), count($writeOffCollections));

        if ($hasTraExits < 1) {
            DB::table('acc_voucher')->where('id', $voucherId)->delete();
            DB::table('acc_voucher_details')->where('voucherId', $voucherId)->delete();
        }

        // get the config for ledger
        $configSavings = DB::table('acc_mfn_av_config_saving')->get();
        $configLoan = DB::table('acc_mfn_av_config_loan')->get();
        $configOthers = DB::table('acc_mfn_av_config_others')->get();
        $configInsurence = DB::table('acc_mfn_av_config_interest_skt_amount')->get();

        // Member Admission Fee
        $primaryProductIds = $memberAdmissionFee->unique('primaryProductId')->pluck('primaryProductId')->toArray();
        foreach ($primaryProductIds as $primaryProductId) {
            $admissionFeeAmount = $memberAdmissionFee->where('primaryProductId', $primaryProductId)->sum('admissionFee');
            if ($creditAcc = $configOthers->where('id', 5)->first() != null) {
                $creditAcc = $configOthers->where('id', 5)->first()->ledgerIdFk;
                $this->storeOrUpdateVoucherDetails(0, 0, $voucherId, $cashInHandLedgerId, $creditAcc, $admissionFeeAmount);
            }
        }
        // End Member Admission Fee            

        // Savings Deposit
        $ledgerIds = $savingsDeposits->unique('ledgerIdFk')->pluck('ledgerIdFk')->toArray();

        foreach ($ledgerIds as $ledgerId) {
            // Primary Product Ids
            $primaryProductIds = $savingsDeposits->where('ledgerIdFk', $ledgerId)->unique('primaryProductIdFk')->pluck('primaryProductIdFk')->toArray();

            foreach ($primaryProductIds as $primaryProductId) {

                $savingsProductIds = $savingsDeposits->where('ledgerIdFk', $ledgerId)->where('primaryProductIdFk', $primaryProductId)->unique('productIdFk')->pluck('productIdFk')->toArray();

                foreach ($savingsProductIds as $savingsProductId) {

                    $depositAmount = $savingsDeposits->where('ledgerIdFk', $ledgerId)->where('primaryProductIdFk', $primaryProductId)->where('productIdFk', $savingsProductId)->sum('amount');
                    if ($creditAcc = $configSavings->where('loanProductId', $primaryProductId)->where('savingProductId', $savingsProductId)->first() != null) {
                        $creditAcc = $configSavings->where('loanProductId', $primaryProductId)->where('savingProductId', $savingsProductId)->first()->ledgerIdForPrincipal;
                        $this->storeOrUpdateVoucherDetails(0, $savingsProductId, $voucherId, $ledgerId, $creditAcc, $depositAmount);
                    }
                }
            }
        }
        // End Savings Deposit


        ////////////////////////////////////////////////////////////
        // Loan Fees
        $loanFormFeeAmount = $loansFees->sum('loanFormFee');
        $addFeeAmount = $loansFees->sum('additionalFee');
        $totalFeeAmount = $loanFormFeeAmount + $addFeeAmount;
        if ($configOthers->where('id', 1)->first() != null) :
            $creditAcc = $configOthers->where('id', 1)->first()->ledgerIdFk;
            $this->storeOrUpdateVoucherDetails(0, 0, $voucherId, $cashInHandLedgerId, $creditAcc, $totalFeeAmount);
        endif;

        $insuranceAmount = $loansFees->sum('insuranceAmount');
        if ($creditAcc = $configInsurence->first() != null) :
            $creditAcc = $configInsurence->first()->ledgerIdForInsurence1;
            $this->storeOrUpdateVoucherDetails(0, 0, $voucherId, $cashInHandLedgerId, $creditAcc, $insuranceAmount);
        endif;
        // End Loan Fees
        ////////////////////////////////////////////////////////////

        // Loan Fees
        /*$ledgerIds = $loansFees->unique('ledgerId')->pluck('ledgerId')->toArray();

            foreach ($ledgerIds as $ledgerId) {
                $loanFeeProductIds = $loansFees->where('ledgerId',$ledgerId)->unique('productIdFk')->pluck('productIdFk')->toArray();
                foreach ($loanFeeProductIds as $loanFeeProductId) {

                    $loanFormFeeAmount = $loansFees->where('ledgerId',$ledgerId)->where('productIdFk',$loanFeeProductId)->sum('loanFormFee');
                    $addFeeAmount = $loansFees->where('ledgerId',$ledgerId)->where('productIdFk',$loanFeeProductId)->sum('additionalFee');
                    $totalFeeAmount = $loanFormFeeAmount + $addFeeAmount;
                    
                    $creditAcc = $configOthers->where('id',1)->first()->ledgerIdFk;
                    $this->storeOrUpdateVoucherDetails(1,$loanFeeProductId,$voucherId,$cashInHandLedgerId,$creditAcc,$totalFeeAmount);
                }
            }*/
        // End Loan Fees

        // Loan Collection
        $ledgerIds = $loanCollections->unique('ledgerIdFk')->pluck('ledgerIdFk');
        foreach ($ledgerIds as $ledgerId) {
            $loanProductIds = $loanCollections->where('ledgerIdFk', $ledgerId)->unique('productIdFk')->pluck('productIdFk');
            foreach ($loanProductIds as $loanProductId) {
                $principalAmount = $loanCollections->where('ledgerIdFk', $ledgerId)->where('productIdFk', $loanProductId)->sum('principalAmount');
                if ($creditAccPrincipal = $configLoan->where('loanProductId', $loanProductId)->first() != null) :
                    $creditAccPrincipal = $configLoan->where('loanProductId', $loanProductId)->first()->ledgerIdForPrincipal;
                    $this->storeOrUpdateVoucherDetails(1, $loanProductId, $voucherId, $ledgerId, $creditAccPrincipal, $principalAmount);
                endif;

                $interestAmount = $loanCollections->where('ledgerIdFk', $ledgerId)->where('productIdFk', $loanProductId)->sum('interestAmount');
                if ($creditAccInterest = $configLoan->where('loanProductId', $loanProductId)->first() != null) :
                    $creditAccInterest = $configLoan->where('loanProductId', $loanProductId)->first()->ledgerIdForInterest;
                    $this->storeOrUpdateVoucherDetails(1, $loanProductId, $voucherId, $ledgerId, $creditAccInterest, $interestAmount);
                endif;
            }
        }
        // End Loan Collection

        // WriteOff Collections (all transaction will be in cash in hand)

        $loanProductIds = $writeOffCollections->unique('loanProductIdFk')->pluck('loanProductIdFk')->toArray();
        foreach ($loanProductIds as $loanProductId) {
            $amount = $writeOffCollections->where('loanProductIdFk', $loanProductId)->sum('amount');
            $creditAcc = $configOthers->where('id', 2)->value('ledgerIdFk');
            $this->storeOrUpdateVoucherDetails(1, $loanProductId, $voucherId, $cashInHandLedgerId, $creditAcc, $amount);
        }
        // End WriteOff Collections
        $this->deleteVoucherIfNoTransactions($voucherId);
    }
    // end createCreditVoucher()


    public function createDebitVoucher()
    {
        // Trasactions counted - Savings Withdraw
        // Trasactions counted - Loan Disbursement
        $savingsWithdraws = $this->dbSavWithdraw->where('withdrawDate', $this->softwareDate)->where('isTransferred', 0);

        $loanDisbursements = $this->dbLoan->where('disbursementDate', $this->softwareDate);

        $voucherId = $this->storeOrUpdateVoucher(1);
        DB::table('acc_voucher_details')->where('voucherId', $voucherId)->delete();

        $hasTraExits = max(count($savingsWithdraws), count($loanDisbursements));

        if ($hasTraExits < 1) {
            DB::table('acc_voucher')->where('id', $voucherId)->delete();
            DB::table('acc_voucher_details')->where('voucherId', $voucherId)->delete();
        }

        // get the config for ledger
        $configSavings = DB::table('acc_mfn_av_config_saving')->get();
        $configLoan = DB::table('acc_mfn_av_config_loan')->get();
        $configOthers = DB::table('acc_mfn_av_config_others')->get();

        // Savings Withdraw
        $ledgerIds = $savingsWithdraws->unique('ledgerIdFk')->pluck('ledgerIdFk')->toArray();
        foreach ($ledgerIds as $ledgerId) {
            $primaryProductIds = $savingsWithdraws->where('ledgerIdFk', $ledgerId)->unique('primaryProductIdFk')->pluck('primaryProductIdFk')->toArray();
            foreach ($primaryProductIds as $primaryProductId) {
                $savingsProductIds = $savingsWithdraws->where('ledgerIdFk', $ledgerId)->where('primaryProductIdFk', $primaryProductId)->unique('productIdFk')->pluck('productIdFk')->toArray();
                foreach ($savingsProductIds as $savingsProductId) {
                    $withdrawAmount = $savingsWithdraws->where('ledgerIdFk', $ledgerId)->where('primaryProductIdFk', $primaryProductId)->where('productIdFk', $savingsProductId)->sum('amount');
                    if ($configSavings->where('loanProductId', $primaryProductId)->where('savingProductId', $savingsProductId)->first() != null) :
                        $debitAcc = $configSavings->where('loanProductId', $primaryProductId)->where('savingProductId', $savingsProductId)->first()->ledgerIdForPrincipal;
                        $this->storeOrUpdateVoucherDetails(0, $savingsProductId, $voucherId, $debitAcc, $ledgerId, $withdrawAmount);
                    endif;
                }
            }
        }
        // End Savings Withdraw

        // Loan Disbursements
        $ledgerIds = $loanDisbursements->unique('ledgerId')->pluck('ledgerId')->toArray();
        foreach ($ledgerIds as $ledgerId) {
            $loanProductIds = $loanDisbursements->where('ledgerId', $ledgerId)->unique('productIdFk')->pluck('productIdFk')->toArray();
            foreach ($loanProductIds as $loanProductId) {
                $amount = $loanDisbursements->where('ledgerId', $ledgerId)->where('productIdFk', $loanProductId)->sum('loanAmount');
                if ($configLoan->where('loanProductId', $loanProductId)->first() != null) {
                    $debitAcc = $configLoan->where('loanProductId', $loanProductId)->first()->ledgerIdForPrincipal;
                    $this->storeOrUpdateVoucherDetails(1, $loanProductId, $voucherId, $debitAcc, $ledgerId, $amount);
                }
            }
        }
        // End Loan Disbursements
        $this->deleteVoucherIfNoTransactions($voucherId);
    }


    public function createJournalVoucher()
    {
        // Trasactions counted - Loan Product Transfer (it will carry the savings amount)
        // Trasactions counted - Waiver, WriteOff
        $productTransfers = DB::table('mfn_loan_primary_product_transfer')->where('softDel', 0)->where('branchIdFk', $this->branchId)->where('transferDate', $this->softwareDate)->select('oldPrimaryProductFk', 'newPrimaryProductFk', 'savingsRecord')->get();

        $waivers = DB::table('mfn_loan_waivers')->where('softDel', 0)->where('branchIdFk', $this->branchId)->where('date', $this->softwareDate)->select('loanIdFk', 'principalAmount', 'interestAmount')->get();

        $writeOffs = DB::table('mfn_loan_write_off')->where('softDel', 0)->where('branchIdFk', $this->branchId)->where('date', $this->softwareDate)->select('loanIdFk', 'principalAmount', 'interestAmount')->get();

        $savingsInterests = $this->dbSavDeposit->where('depositDate', $this->softwareDate)->where('amount', '>', 0)->where('paymentType', '=', 'Interest');

        $loanIds = array_merge($waivers->pluck('loanIdFk')->toArray(), $writeOffs->pluck('loanIdFk')->toArray());

        $loans = $this->dbLoan->whereIn('id', $loanIds);

        $voucherId = $this->storeOrUpdateVoucher(3);
        DB::table('acc_voucher_details')->where('voucherId', $voucherId)->delete();

        $hasTraExits = max(count($productTransfers), count($waivers), count($writeOffs), count($savingsInterests));

        if ($hasTraExits < 1) {
            DB::table('acc_voucher')->where('id', $voucherId)->delete();
            DB::table('acc_voucher_details')->where('voucherId', $voucherId)->delete();
        }


        // get the config for ledger
        $configSavings = DB::table('acc_mfn_av_config_saving')->get();
        $configLoan = DB::table('acc_mfn_av_config_loan')->get();
        $configOthers = DB::table('acc_mfn_av_config_others')->get();

        ///////////////////////////////////////////////////////////            

        $infos = array();

        foreach ($productTransfers as $productTransfer) {
            $savingsDetails = json_decode($productTransfer->savingsRecord);
            foreach ($savingsDetails as $savingsDetail) {
                $temp = array(
                    'from'              => $productTransfer->oldPrimaryProductFk,
                    'to'                => $productTransfer->newPrimaryProductFk,
                    'savingsProductId'  => $savingsDetail->savingsProductId,
                    'amount'            => $savingsDetail->balance
                );
                $mathedFlag = 0;
                foreach ($infos as $key => $info) {
                    if ($info['from'] == $temp['from'] && $info['to'] == $temp['to'] && $info['savingsProductId'] == $temp['savingsProductId']) {
                        $infos[$key]['amount'] = $infos[$key]['amount'] + $temp['amount'];
                        $mathedFlag = 1;
                    }
                }
                if ($mathedFlag == 0) {
                    array_push($infos, $temp);
                }
            }
        }

        foreach ($infos as $info) {
            if ($configSavings->where('loanProductId', $info['from'])->where('savingProductId', $info['savingsProductId'])->first() != null && $configSavings->where('loanProductId', $info['to'])->where('savingProductId', $info['savingsProductId'])->first() != null) :
                $debitAccPrincipal = $configSavings->where('loanProductId', $info['from'])->where('savingProductId', $info['savingsProductId'])->first()->ledgerIdForPrincipal;
                $creditAccPrincipal = $configSavings->where('loanProductId', $info['to'])->where('savingProductId', $info['savingsProductId'])->first()->ledgerIdForPrincipal;
                $this->storeOrUpdateVoucherDetails(0, $info['savingsProductId'], $voucherId, $debitAccPrincipal, $creditAccPrincipal, $info['amount']);
            endif;
        }


        ///////////////////////////////////////////////////////////

        // Product Transfer            
        /*foreach ($productTransfers as $productTransfer) {

                $debitAccPrincipal = $configLoan->where('loanProductId',$productTransfer->oldPrimaryProductFk)->first()->ledgerIdForPrincipal;
                $creditAccPrincipal = $configLoan->where('loanProductId',$productTransfer->newPrimaryProductFk)->first()->ledgerIdForPrincipal;                
                $this->storeOrUpdateVoucherDetails(1,0,$voucherId,$debitAccPrincipal,$creditAccPrincipal,$productTransfer->totalTransferAmount);

                $debitAccInterest = $configLoan->where('loanProductId',$productTransfer->oldPrimaryProductFk)->first()->ledgerIdForInterest;
                $creditAccInterest = $configLoan->where('loanProductId',$productTransfer->newPrimaryProductFk)->first()->ledgerIdForInterest;
                $this->storeOrUpdateVoucherDetails(1,0,$voucherId,$debitAccInterest,$creditAccInterest,$productTransfer->totalInterestAmount);
            }*/
        // End Product Transfer

        // Waivers
        foreach ($waivers as $waiver) {
            $productId = $loans->where('id', $waiver->loanIdFk)->first()->productIdFk;
            if ($configOthers->where('id', 3)->first() != null && $loans->where('id', $waiver->loanIdFk)->first() != null && $configLoan->where('loanProductId', $productId)->first() != null && $configLoan->where('loanProductId', $productId)->first() != null) :
                $debitAcc = $configOthers->where('id', 3)->first()->ledgerIdFk;
                $creditAccPrincipal = $configLoan->where('loanProductId', $productId)->first()->ledgerIdForPrincipal;
                $creditAccInterest = $configLoan->where('loanProductId', $productId)->first()->ledgerIdForInterest;
                $this->storeOrUpdateVoucherDetails(1, 0, $voucherId, $debitAcc, $creditAccPrincipal, $waiver->principalAmount);
                $this->storeOrUpdateVoucherDetails(1, 0, $voucherId, $debitAcc, $creditAccInterest, $waiver->interestAmount);
            endif;
        }
        // End Waivers

        // Write Offs
        foreach ($writeOffs as $writeOff) {
            $productId = $loans->where('id', $writeOff->loanIdFk)->first()->productIdFk;
            if ($configOthers->where('id', 4)->first() != null && $loans->where('id', $writeOff->loanIdFk)->first() != null && $configLoan->where('loanProductId', $productId)->first() != null && $configLoan->where('loanProductId', $productId)->first() != null) :
                $debitAcc = $configOthers->where('id', 4)->first()->ledgerIdFk;
                $creditAccPrincipal = $configLoan->where('loanProductId', $productId)->first()->ledgerIdForPrincipal;
                $creditAccInterest = $configLoan->where('loanProductId', $productId)->first()->ledgerIdForInterest;
                $this->storeOrUpdateVoucherDetails(1, 0, $voucherId, $debitAcc, $creditAccPrincipal, $writeOff->principalAmount);
                $this->storeOrUpdateVoucherDetails(1, 0, $voucherId, $debitAcc, $creditAccInterest, $writeOff->interestAmount);
            endif;
        }
        // End Waivers


        // Savings Interest
        $ledgerIds = $savingsInterests->unique('ledgerIdFk')->pluck('ledgerIdFk')->toArray();

        foreach ($ledgerIds as $ledgerId) {
            // Primary Product Ids
            $primaryProductIds = $savingsInterests->where('ledgerIdFk', $ledgerId)->unique('primaryProductIdFk')->pluck('primaryProductIdFk')->toArray();

            foreach ($primaryProductIds as $primaryProductId) {

                $savingsProductIds = $savingsInterests->where('ledgerIdFk', $ledgerId)->where('primaryProductIdFk', $primaryProductId)->unique('productIdFk')->pluck('productIdFk')->toArray();

                foreach ($savingsProductIds as $savingsProductId) {

                    $depositAmount = $savingsInterests->where('ledgerIdFk', $ledgerId)->where('primaryProductIdFk', $primaryProductId)->where('productIdFk', $savingsProductId)->sum('amount');
                    if ($creditAcc = $configSavings->where('loanProductId', $primaryProductId)->where('savingProductId', $savingsProductId)->first() != null) {
                        $creditAcc = $configSavings->where('loanProductId', $primaryProductId)->where('savingProductId', $savingsProductId)->first()->ledgerIdForPrincipal;
                        $this->storeOrUpdateVoucherDetails(0, $savingsProductId, $voucherId, $ledgerId, $creditAcc, $depositAmount);
                    }
                }
            }
        }
        // End Savings Interest

        $this->deleteVoucherIfNoTransactions($voucherId);
    }


    /**
     * [getVoucherId description]
     * @param  [type] $voucherTypeId     [1=debit, 2=credit, 3=Journal....]
     * @return [int]          [voucher id if exits or return zero]
     */
    public function getVoucherId($voucherTypeId)
    {

        $voucherId = (int) $this->vouchers
            ->where('voucherTypeId', $voucherTypeId)
            ->max('id');

        return $voucherId;
    }

    /**
     * [storeVoucher description]
     * @param  [type] $voucherTypeId     [1=debit, 2=credit, 3=Journal..]
     * @return [type]                [description]
     */
    public function storeOrUpdateVoucher($voucherTypeId)
    {

        $voucherId = $this->getVoucherId($voucherTypeId);
        if ($voucherId > 0) {
            DB::table('acc_voucher_details')->where('voucherId', $voucherId)->delete();
            //DB::table('acc_voucher_details')->where('voucherId',$voucherId)->update(['amount'=>0]);
            return $voucherId;
        }

        $branchProjectTypeId = DB::table('gnr_branch')->where('id', $this->branchId)->value('projectTypeId');

        $voucherCodeArr = array(
            'voucherTypeId'  => $voucherTypeId,
            'projectTypeId'  => $branchProjectTypeId,
            'branchId'       => $this->branchId
        );

        $voucherCode = $this->Accounting->getVoucherCodeForAV($voucherCodeArr);

        $voucher = new AddVoucher;
        $voucher->voucherTypeId     = $voucherTypeId;
        $voucher->projectId         = 1; // Microfinance
        $voucher->projectTypeId     = $branchProjectTypeId;
        $voucher->voucherDate       = Carbon::parse($this->softwareDate);
        $voucher->voucherCode       = $voucherCode;
        $voucher->globalNarration   = 'Auto Voucher';
        $voucher->branchId          = $this->branchId;
        $voucher->moduleIdFk        = 6;
        $voucher->companyId         = Auth::user()->company_id_fk;
        $voucher->vGenerateType     = 1;
        $voucher->prepBy            = Auth::user()->id;
        $voucher->authBy            = Auth::user()->emp_id_fk;
        $voucher->createdDate       = Carbon::now();
        $voucher->status            = 1;
        $voucher->save();

        return $voucher->id;
    }

    public function storeOrUpdateVoucherDetails($isLoan, $productId, $voucherId, $debitAcc, $creditAcc, $amount)
    {

        if ($amount <= 0) {
            return false;
        }

        /*$voucherDetailsId = $this->voucherDetails->where('voucherId',$voucherId)->where('debitAcc',$debitAcc)->where('creditAcc',$creditAcc)->max('id');

            if ($voucherDetailsId>0) {
                $voucherDetails = AddVoucherDetails::find($voucherDetailsId);
            }
            else{
                $voucherDetails = new AddVoucherDetails; 
                $voucherDetails->createdDate = Carbon::now();              
            }*/

        $voucherDetails = new AddVoucherDetails;
        $voucherDetails->createdDate = Carbon::now();

        if ($productId == 0) {
            // for journal voucher
            $localNarration = 'Auto Generated';
        } else {
            if ($isLoan == 1) {
                $localNarration = $this->loanProducts->where('id', $productId)->first()->name;
            } else {
                $localNarration = $this->savingsProducts->where('id', $productId)->first()->name;
            }
        }

        $voucherDetails->voucherId      = $voucherId;
        $voucherDetails->debitAcc       = $debitAcc;
        $voucherDetails->creditAcc      = $creditAcc;
        $voucherDetails->amount         = $amount;
        $voucherDetails->localNarration = $localNarration;
        $voucherDetails->status         = 1;
        $voucherDetails->save();
    }


    public function deleteVoucherIfNoTransactions($voucherId)
    {
        $isExits = (int) DB::table('acc_voucher_details')->where('voucherId', $voucherId)->value('id');
        if ($isExits < 1) {
            DB::table('acc_voucher')->where('id', $voucherId)->delete();
        }
        return true;
    }

    /*public function deleteVoucher($voucherTypeId){

            $voucherId = DB::table('acc_voucher')
                                ->where('projectId',1)
                                ->where('branchId',$this->branchId)
                                ->where('vGenerateType',1)
                                ->where('moduleIdFk',6)
                                ->where('voucherTypeId',$voucherTypeId)
                                ->value('id');

            DB::table('acc_voucher')->where('id',$voucherId)->delete();
            DB::table('acc_voucher_details')->where('voucherId',$voucherId)->delete();
        }*/

    /**
     * this function should be called from month end
     * this is Journal Voucher
     */
    public function createProbationInterestVoucher($branchId, $monthEndDate)
    {

        $monthEndFirstDate = Carbon::parse($monthEndDate)->startOfMonth()->format('Y-m-d');

        $voucherId = DB::table('acc_voucher')->where('branchId', $branchId)->where('projectId', 1)->where('moduleIdFk', 6)->where('vGenerateType', 1)->where('voucherTypeId', 3)->where('voucherFor', 'InterestProbation')->where('voucherDate', '>=', $monthEndFirstDate)->where('voucherDate', '<=', $monthEndDate)->value('id');

        if ($voucherId > 0) {
            DB::table('acc_voucher_details')->where('voucherId', $voucherId)->delete();
            DB::table('acc_voucher')->where('id', $voucherId)->delete();
        }


        $lastWorkingDate = DB::table('mfn_day_end')->where('branchIdFk', $branchId)->where('date', '<=', $monthEndDate)->where('isLocked', 1)->max('date');
        if ($lastWorkingDate == null) {
            return false;
        }

        $configSavings = DB::table('acc_mfn_av_config_saving')->get();

        $interestProbations = DB::table('mfn_savings_probation_interest')
            ->where('branchIdFk', $branchId)
            ->where('date', $monthEndDate)
            ->get();

        if (count($interestProbations) < 1) {
            return false;
        }

        $branchProjectTypeId = DB::table('gnr_branch')->where('id', $branchId)->value('projectTypeId');

        $voucherCodeArr = array(
            'voucherTypeId'  => 3, // 3 = Journal Voucher
            'projectTypeId'  => $branchProjectTypeId,
            'branchId'       => $branchId
        );

        $voucherCode = $this->Accounting->getVoucherCodeForAV($voucherCodeArr);

        $voucher = new AddVoucher;
        $voucher->voucherTypeId     = 3;
        $voucher->projectId         = 1; // Microfinance
        $voucher->projectTypeId     = $branchProjectTypeId;
        $voucher->voucherDate       = Carbon::parse($lastWorkingDate);
        $voucher->voucherCode       = $voucherCode;
        $voucher->globalNarration   = 'Auto Voucher (Savings Interest Probation)';
        $voucher->branchId          = $branchId;
        $voucher->moduleIdFk        = 6;
        $voucher->voucherFor        = 'InterestProbation';
        $voucher->companyId         = Auth::user()->company_id_fk;
        $voucher->vGenerateType     = 1;
        $voucher->prepBy            = Auth::user()->id;
        $voucher->authBy            = Auth::user()->emp_id_fk;
        $voucher->createdDate       = Carbon::now();
        $voucher->status            = 1;
        $voucher->save();

        $primaryProductIds = $interestProbations->unique('primaryProductIdFk')->pluck('primaryProductIdFk')->toArray();

        foreach ($primaryProductIds as $primaryProductId) {
            $loanProductProbations = $interestProbations->where('primaryProductIdFk', $primaryProductId);

            $savingsProductIds = $loanProductProbations->unique('productIdFk')->pluck('productIdFk')->toArray();

            foreach ($savingsProductIds as $savingsProductId) {
                $cProbation = $loanProductProbations->where('productIdFk', $savingsProductId);

                if ($configSavings->where('loanProductId', $primaryProductId)->where('savingProductId', $savingsProductId)->first() != null && $configSavings->where('loanProductId', $primaryProductId)->where('savingProductId', $savingsProductId)->first() != null) :

                    $debitAcc = $configSavings->where('loanProductId', $primaryProductId)->where('savingProductId', $savingsProductId)->first()->ledgerIdForInterest;
                    $creditAcc = $configSavings->where('loanProductId', $primaryProductId)->where('savingProductId', $savingsProductId)->first()->ledgerIdForInterestProvision;
                    $amount = $cProbation->sum('interestAmount');
                    $localNarration = $this->savingsProducts->where('id', $savingsProductId)->first()->name;

                    $voucherDetails = new AddVoucherDetails;
                    $voucherDetails->voucherId      = $voucher->id;
                    $voucherDetails->debitAcc       = $debitAcc;
                    $voucherDetails->creditAcc      = $creditAcc;
                    $voucherDetails->amount         = $amount;
                    $voucherDetails->localNarration = $localNarration;
                    $voucherDetails->status         = 1;
                    $voucherDetails->save();
                endif;
            }
        }
    }
}
