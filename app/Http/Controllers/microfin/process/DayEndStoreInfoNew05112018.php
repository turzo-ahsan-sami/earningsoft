<?php 
namespace App\Http\Controllers\microfin\process;

    use DB;
    use Carbon\Carbon;    
    use App\Http\Controllers\Controller;
    use App\microfin\process\MfnMonthEndProcessTotalMembers;
    use App\microfin\process\MfnMonthEndProcessMembers;
    use App\microfin\process\MfnMonthEndProcessSavings;
    use App\microfin\process\MfnDayEndSavingsDetails;
    use App\microfin\process\MfnDayEndSavingsTransferDetails;
    use App\microfin\process\MfnDayEndLoanDetails;
    use App\Http\Controllers\microfin\MicroFin;
    use Auth;

    class DayEndStoreInfo {

        private $targetBranchId;
        private $softwareDate;
        private $deposits;
        private $withdraws;
        private $dbMembers;
        private $dbSavAcc;
        private $dbLoan;
        private $dbLoanCollection;
        private $dbSchedules;

        function __construct($consData){

            $this->targetBranchId = $consData['targetBranchId'];
            $this->softwareDate = $consData['softwareDate'];

            /*$this->deposits = DB::table('mfn_savings_deposit')
                            ->where('softDel',0)
                            ->where('branchIdFk',$this->targetBranchId)
                            ->where('depositDate',$this->softwareDate)
                            ->where('amount','>',0)
                            ->get();*/

            $this->deposits = $consData['dbSavDeposit']->where('amount','>',0);

            /*$this->withdraws = DB::table('mfn_savings_withdraw')
                            ->where('softDel',0)
                            ->where('branchIdFk',$this->targetBranchId)
                            ->where('withdrawDate',$this->softwareDate)
                            ->where('amount','>',0)
                            ->get();*/

            $this->withdraws = $consData['dbSavWithdraw'];

            $this->dbMembers = $consData['dbMembers'];
            $this->dbSavAcc = $consData['dbSavAcc'];
            $this->dbLoan = $consData['dbLoan'];
            $this->dbLoanCollection = $consData['dbLoanCollection'];
            $this->dbSchedules = $consData['dbSchedules'];
        }

        public function saveData(){
             $this->storeSavingsDetails();
             $this->storeSavingsTransferDetails();
            $this->storeLoanDetails();
        }

        /**
         * [this function stores data to "mfn_day_end_saving_details" table]
         * @return [void]                 [description]
         */
        public function storeSavingsDetails(){

            DB::table('mfn_day_end_saving_details')->where('branchIdFk',$this->targetBranchId)->where('date',$this->softwareDate)->delete();

            // 1 = Male, 2 = Female
            $genderTypeIds = [1,2];

            $primaryProductIds = $this->deposits->where('depositDate',$this->softwareDate)->unique('primaryProductIdFk')->pluck('primaryProductIdFk')->toArray();
            $primaryProducts = DB::table('mfn_loans_product')
                                    ->whereIn('id',$primaryProductIds)
                                    ->select('id','fundingOrganizationId')
                                    ->get();

            $memberIds = $this->deposits->where('depositDate',$this->softwareDate)->unique('memberIdFk')->pluck('memberIdFk')->toArray();

            $maleMemberIds = $this->dbMembers
                                ->whereIn('id',$memberIds)
                                ->where('gender',1)
                                ->pluck('id')
                                ->toArray();

            $femaleMemberIds = $this->dbMembers
                                ->whereIn('id',$memberIds)
                                ->where('gender',2)
                                ->pluck('id')
                                ->toArray();

            // 1 is for Deposit and 2 is for Withdraw
            $transactionTypeId = 1;

            $this->storeSavingsDetailsHelper($primaryProducts,$transactionTypeId,$this->deposits,$genderTypeIds,$maleMemberIds,$femaleMemberIds);

            ///////////// withdraws

            $primaryProductIds = $this->withdraws->unique('primaryProductIdFk')->pluck('primaryProductIdFk');
            $primaryProducts = DB::table('mfn_loans_product')
                                    ->whereIn('id',$primaryProductIds)
                                    ->select('id','fundingOrganizationId')
                                    ->get();

            $memberIds = $this->withdraws->unique('memberIdFk')->pluck('memberIdFk')->toArray();

            $maleMemberIds = $this->dbMembers
                                ->whereIn('id',$memberIds)
                                ->where('gender',1)
                                ->pluck('id')
                                ->toArray();

            $femaleMemberIds = $this->dbMembers
                                ->whereIn('id',$memberIds)
                                ->where('gender',2)
                                ->pluck('id')
                                ->toArray();

            // 1 is for Deposit and 2 is for Withdraw
            $transactionTypeId = 2;

            $this->storeSavingsDetailsHelper($primaryProducts,$transactionTypeId,$this->withdraws,$genderTypeIds,$maleMemberIds,$femaleMemberIds);

        }

        public function storeSavingsDetailsHelper($primaryProducts,$transactionTypeId,$transactions,$genderTypeIds,$maleMemberIds,$femaleMemberIds){
             
            foreach ($primaryProducts as $primaryProduct) {

                $savingsProductIds = $transactions->where('primaryProductIdFk',$primaryProduct->id)->unique('productIdFk')->pluck('productIdFk')->toArray();

                foreach ($savingsProductIds as $savingsProductId) {                    
                    foreach ($genderTypeIds as $genderTypeId) {

                        if ($genderTypeId==1) {
                            $genderWiseMemberIds = $maleMemberIds;
                        }
                        else{
                            $genderWiseMemberIds = $femaleMemberIds;
                        }

                        $paymentTypes = $transactions->where('primaryProductIdFk',$primaryProduct->id)->where('productIdFk',$savingsProductId)->whereIn('memberIdFk',$genderWiseMemberIds)->unique('paymentType')->pluck('paymentType')->toArray();

                        foreach ($paymentTypes as $paymentType) {


                            $ledgerIds = $transactions->where('primaryProductIdFk',$primaryProduct->id)->where('productIdFk',$savingsProductId)->whereIn('memberIdFk',$genderWiseMemberIds)->where('paymentType',$paymentType)->unique('ledgerIdFk')->pluck('ledgerIdFk');

                            foreach ($ledgerIds as $ledgerId) {

                                $amount = $transactions->where('primaryProductIdFk',$primaryProduct->id)->where('productIdFk',$savingsProductId)->whereIn('memberIdFk',$genderWiseMemberIds)->where('paymentType',$paymentType)->where('ledgerIdFk',$ledgerId)->sum('amount');

                                if ($transactionTypeId==1) {
                                    $savingDepositAmount = $amount;
                                    $savingWithdrawAmount = 0;
                                }
                                else{
                                    $savingDepositAmount = 0;
                                    $savingWithdrawAmount = $amount;
                                }   
                                
                                $savDetails = new MfnDayEndSavingsDetails;
                                $savDetails->branchIdFk                 = $this->targetBranchId;
                                $savDetails->fundingOrganizationIdFk    = $primaryProduct->fundingOrganizationId;
                                $savDetails->genderTypeId               = $genderTypeId;
                                $savDetails->date                       = Carbon::parse($this->softwareDate);
                                $savDetails->productIdFk                = $savingsProductId;
                                $savDetails->primayProductIdFk          = $primaryProduct->id;
                                $savDetails->transactionTypeId          = $transactionTypeId;
                                $savDetails->paymentMode                = $paymentType;
                                $savDetails->ledgerIdFk                 = $ledgerId;
                                $savDetails->savingDepositAmount        = $savingDepositAmount;
                                $savDetails->savingWithdrawAmount       = $savingWithdrawAmount;
                                // $savDetails->savingInterestAmount       = 
                                // $savDetails->savingClosingAmount        = 
                                // $savDetails->sktAmount                  = 
                                // $savDetails->transferDeposit            = 
                                // $savDetails->transferWithdraw           = 
                                // $savDetails->receiptVoucherStatus       = 
                                // $savDetails->paymentVoucherStatus       = 
                                // $savDetails->journalVoucherStatus       = 
                                // $savDetails->isMigrated                 = 
                                $savDetails->createdAt                  = Carbon::now();
                                $savDetails->save();
                            }                            
                        }                        
                    }
                }
            }
        }


        /**
         * [this function stores data to "mfn_day_end_savings_transfer_details" table]
         * @return [void]                 [description]
         */
        public function storeSavingsTransferDetails(){

            DB::table('mfn_day_end_savings_transfer_details')
                    ->where('branchIdFk',$this->targetBranchId)
                    ->where('date',$this->softwareDate)
                    ->delete();

            // male
            $maleMemberIds = $this->dbMembers
                                    ->where('gender',1)
                                    ->pluck('id')
                                    ->toArray();

            // female
            $femaleMemberIds = $this->dbMembers
                                    ->where('gender',2)
                                    ->pluck('id')
                                    ->toArray();


            /// for samity transfer

          /*  $memberSamityTransfer = DB::table('mfn_member_samity_transfer')
                                        ->where('softDel',0)
                                        ->whereColumn('previousPrimaryProductIdFk','!=','newPrimaryProductIdFk')
                                        ->where('branchIdFk',$this->targetBranchId)
                                        ->where('transferDate',$this->softwareDate)
                                        ->get();
            
            $maleMemberSamityTransfer = $memberSamityTransfer->whereIn('memberIdFk',$maleMemberIds);
            $genderTypeId = 1;
            $this->storeSavingsTransferDetailsHelperForMemberSamityTransfer($genderTypeId,$maleMemberSamityTransfer);
            
            

            $femaleMemberSamityTransfer = $memberSamityTransfer->whereIn('memberIdFk',$femaleMemberIds);
            $genderTypeId = 2;
            $this->storeSavingsTransferDetailsHelperForMemberSamityTransfer($genderTypeId,$femaleMemberSamityTransfer);*/

            /// end for samity transfer
            

            /// for primary product transfer
            $productTransfers = DB::table('mfn_loan_primary_product_transfer')
                                    ->where('branchIdFk',$this->targetBranchId)
                                    ->where('transferDate',$this->softwareDate)
                                    ->get();

            $maleProductTransfers = $productTransfers->whereIn('memberIdFk',$maleMemberIds);
            $genderTypeId = 1;
            $this->storeSavingsTransferDetailsHelperForProductTransfer($genderTypeId,$maleProductTransfers);

            $femaleProductTransfers = $productTransfers->whereIn('memberIdFk',$femaleMemberIds);
            $genderTypeId = 2;
            $this->storeSavingsTransferDetailsHelperForProductTransfer($genderTypeId,$femaleProductTransfers);

            /// end for primary product transfer
        }

        public function storeSavingsTransferDetailsHelperForMemberSamityTransfer($genderTypeId,$tarnsfers){

            $previousPrimaryProductIds = $tarnsfers->unique('previousPrimaryProductIdFk')->pluck('previousPrimaryProductIdFk');
            
            foreach ($previousPrimaryProductIds as $previousPrimaryProductId) {


                $newPrimaryProductIds = $tarnsfers->where('previousPrimaryProductIdFk',$previousPrimaryProductId)->unique('newPrimaryProductIdFk')->pluck('newPrimaryProductIdFk');

                foreach ($newPrimaryProductIds as $newPrimaryProductId) {

                    $currentMemberIds = $tarnsfers->where('previousPrimaryProductIdFk',$previousPrimaryProductId)->where('newPrimaryProductIdFk',$newPrimaryProductId)->pluck('memberIdFk');

                    /*$savingsProductIds = DB::table('mfn_savings_account')
                                            ->where('softDel',0)
                                            ->where('status',1)
                                            ->whereIn('memberIdFk',$currentMemberIds)
                                            ->groupBy('savingsProductIdFk')
                                            ->pluck('savingsProductIdFk');*/

                    $savingsProductIds = $this->dbSavAcc    
                                            ->whereIn('memberIdFk',$currentMemberIds)
                                            ->unique('savingsProductIdFk')
                                            ->pluck('savingsProductIdFk');

                    $transferType = 'MemberTransfer';
                    if ($previousPrimaryProductId!=$newPrimaryProductId) {                        
                        $transferType = 'MemberProductTransfer';
                    }

                    foreach ($savingsProductIds as $savingsProductId) {

                        /*$depositAmount = DB::table('mfn_savings_deposit')
                                        ->where('softDel',0)
                                        ->where('isTransferred',0)
                                        ->where('depositDate','<=',$this->softwareDate)
                                        ->whereIn('memberIdFk',$currentMemberIds)
                                        ->where('productIdFk',$savingsProductId)
                                        ->sum('amount');*/

                        $depositAmount = $this->deposits
                                        ->where('isTransferred',0)
                                        ->whereIn('memberIdFk',$currentMemberIds)
                                        ->where('productIdFk',$savingsProductId)
                                        ->sum('amount');

                        $withdrawAmount = $this->withdraws
                                        ->where('isTransferred',0)
                                        ->whereIn('memberIdFk',$currentMemberIds)
                                        ->where('productIdFk',$savingsProductId)
                                        ->sum('amount');

                        $amount = $depositAmount - $withdrawAmount;

                        $transferDetails = new MfnDayEndSavingsTransferDetails;
                        $transferDetails->branchIdFk            = $this->targetBranchId;
                        $transferDetails->branchIdFkTo          = $this->targetBranchId;
                        $transferDetails->genderTypeId          = $genderTypeId;
                        $transferDetails->date                  = Carbon::parse($this->softwareDate);
                        $transferDetails->savingsProductIdFk    = $savingsProductId;
                        $transferDetails->oldPrimaryProductIdFk = $previousPrimaryProductId;
                        $transferDetails->newPrimaryProductIdFk = $newPrimaryProductId;
                        $transferDetails->amount                = $amount;
                        $transferDetails->transferType          = $transferType;
                        // $transferDetails->journalVoucherStatus  = 
                        $transferDetails->createdAt             = Carbon::now();
                        $transferDetails->updatedAt             = Carbon::now();
                        $transferDetails->save();
                    }
                }
            }
        }

        public function storeSavingsTransferDetailsHelperForProductTransfer($genderTypeId,$tarnsfers){

            $previousPrimaryProductIds = $tarnsfers->unique('oldPrimaryProductFk')->pluck('oldPrimaryProductFk');
            
            foreach ($previousPrimaryProductIds as $previousPrimaryProductId) {

                $newPrimaryProductIds = $tarnsfers->where('oldPrimaryProductFk',$previousPrimaryProductId)->unique('newPrimaryProductFk')->pluck('newPrimaryProductFk');

                foreach ($newPrimaryProductIds as $newPrimaryProductId) {

                    $currentMemberIds = $tarnsfers->where('oldPrimaryProductFk',$previousPrimaryProductId)->where('newPrimaryProductFk',$newPrimaryProductId)->pluck('memberIdFk');
                    
                    /*$savingsProductIds = DB::table('mfn_savings_account')
                                            ->where('softDel',0)
                                            ->where('status',1)
                                            ->whereIn('memberIdFk',$currentMemberIds)
                                            ->groupBy('savingsProductIdFk')
                                            ->pluck('savingsProductIdFk');*/

                    $savingsProductIds = $this->dbSavAcc
                                            ->whereIn('memberIdFk',$currentMemberIds)
                                            ->unique('savingsProductIdFk')
                                            ->pluck('savingsProductIdFk');
                                            

                    $transferType = 'ProductTransfer';

                    foreach ($savingsProductIds as $savingsProductId) {

                        /*$depositAmount = DB::table('mfn_savings_deposit')
                                        ->where('softDel',0)
                                        ->where('depositDate','<=',$this->softwareDate)
                                        ->whereIn('memberIdFk',$currentMemberIds)
                                        ->where('productIdFk',$savingsProductId)
                                        ->sum('amount');*/

                        $depositAmount = $this->deposits
                                        ->whereIn('memberIdFk',$currentMemberIds)
                                        ->where('productIdFk',$savingsProductId)
                                        ->sum('amount');

                        /*$withdrawAmount = DB::table('mfn_savings_withdraw')
                                        ->where('softDel',0)
                                        ->where('withdrawDate','<=',$this->softwareDate)
                                        ->whereIn('memberIdFk',$currentMemberIds)
                                        ->where('productIdFk',$savingsProductId)
                                        ->sum('amount');*/

                        $withdrawAmount = $this->withdraws
                                        ->whereIn('memberIdFk',$currentMemberIds)
                                        ->where('productIdFk',$savingsProductId)
                                        ->sum('amount');

                        $amount = $depositAmount - $withdrawAmount;

                        $transferDetails = new MfnDayEndSavingsTransferDetails;
                        $transferDetails->branchIdFk            = $this->targetBranchId;
                        $transferDetails->branchIdFkTo          = $this->targetBranchId;
                        $transferDetails->genderTypeId          = $genderTypeId;
                        $transferDetails->date                  = Carbon::parse($this->softwareDate);
                        $transferDetails->savingsProductIdFk    = $savingsProductId;
                        $transferDetails->oldPrimaryProductIdFk = $previousPrimaryProductId;
                        $transferDetails->newPrimaryProductIdFk = $newPrimaryProductId;
                        $transferDetails->amount                = $amount;
                        $transferDetails->transferType          = $transferType;
                        // $transferDetails->journalVoucherStatus  = 
                        $transferDetails->createdAt             = Carbon::now();
                        $transferDetails->updatedAt             = Carbon::now();
                        $transferDetails->save();
                    }
                }
            }
        }

        /**
         * [this function store data to "mfn_day_end_loan_details" table]
         * @return [void]
         */
        public function storeLoanDetails(){

            DB::table('mfn_day_end_loan_details')->where('branchIdFk',$this->targetBranchId)->where('date',$this->softwareDate)->delete();

            $maleMemberIdsOfthisBranch = $this->dbMembers
                                            ->where('gender',1)
                                            ->pluck('id')
                                            ->toArray();

            $femaleMemberIdsOfthisBranch = $this->dbMembers
                                            ->where('gender',2)
                                            ->pluck('id')
                                            ->toArray();

            /*$loans = DB::table('mfn_loan')
                        ->where('softDel',0)
                        ->where('branchIdFk',$this->targetBranchId)
                        ->where('disbursementDate','<=',$this->softwareDate)
                        ->select('id','memberIdFk','primaryProductIdFk','productIdFk')
                        ->get();*/

            //$loans = $this->dbLoan;

           /* $maleMemberIdsFromLoan = DB::table('mfn_member_information')
                                ->whereIn('id',$this->dbLoan->pluck('memberIdFk'))
                                ->where('gender',1)
                                ->pluck('id')
                                ->toArray();*/

            $maleMemberIdsFromLoan = $this->dbMembers
                                ->whereIn('id',$this->dbLoan->pluck('memberIdFk'))
                                ->where('gender',1)
                                ->pluck('id')
                                ->toArray();

            $maleMemberLoanIds = $this->dbLoan->whereIn('memberIdFk',$maleMemberIdsFromLoan)->pluck('id')->toArray();

            /*$femaleMemberIdsFromLoan = DB::table('mfn_member_information')
                                ->whereIn('id',$this->dbLoan->pluck('memberIdFk'))
                                ->where('gender',2)
                                ->pluck('id')
                                ->toArray();*/

            $femaleMemberIdsFromLoan = $this->dbMembers
                                ->whereIn('id',$this->dbLoan->pluck('memberIdFk'))
                                ->where('gender',2)
                                ->pluck('id')
                                ->toArray();

            $femaleMemberLoanIds = $this->dbLoan->whereIn('memberIdFk',$femaleMemberIdsFromLoan)->pluck('id')->toArray();

            /*$todayLoans = DB::table('mfn_loan')
                        ->where('softDel',0)
                        ->where('branchIdFk',$this->targetBranchId)
                        ->where('disbursementDate',$this->softwareDate)
                        ->select('memberIdFk','primaryProductIdFk','productIdFk','loanAmount','totalRepayAmount','insuranceAmount','additionalFee','loanFormFee','ledgerId')
                        ->get();*/

            $todayLoans = $this->dbLoan
                        ->where('disbursementDate',$this->softwareDate);
            
            /*$loanCollections = DB::table('mfn_loan_collection')
                                ->where('softDel',0)
                                ->where('branchIdFk',$this->targetBranchId)
                                ->where('collectionDate',$this->softwareDate)
                                ->select('loanIdFk','memberIdFk','primaryProductIdFk','productIdFk','amount','principalAmount','interestAmount','ledgerIdFk')
                                ->get();*/

            $loanCollections = $this->dbLoanCollection
                                ->where('collectionDate',$this->softwareDate);

            /*$allCollections =  DB::table('mfn_loan_collection')
                                    ->where('softDel',0)
                                    ->whereIn('loanIdFk',$this->dbLoan->pluck('id'))
                                    ->where('collectionDate','<=',$this->softwareDate)
                                    ->select('loanIdFk','memberIdFk','primaryProductIdFk','productIdFk','amount','principalAmount','interestAmount','ledgerIdFk','collectionDate')
                                    ->get();*/

            // $allCollections =  $this->dbLoanCollection;

            /*$shedules = DB::table('mfn_loan_schedule')
                            ->where('softDel',0)
                            ->whereIn('loanIdFk',$this->dbLoan->pluck('id'))
                            ->where('scheduleDate','<=',$this->softwareDate)
                            ->select('loanIdFk','installmentAmount','principalAmount','interestAmount','scheduleDate')
                            ->get();*/

            $shedules = $this->dbSchedules
                            ->whereIn('loanIdFk',$this->dbLoan->pluck('id'));
                            

            $rebates = DB::table('mfn_loan_rebates')
                            ->where('branchIdFk',$this->targetBranchId)
                            ->where('date',$this->softwareDate)
                            ->get();

            // get the member ids with in these three transations
            $memberIds = array_unique(array_merge($todayLoans->pluck('memberIdFk')->toArray(),$loanCollections->pluck('memberIdFk')->toArray(),$rebates->pluck('memberIdFk')->toArray()));

            /*$maleMemberIds = DB::table('mfn_member_information')
                                ->whereIn('id',$memberIds)
                                ->where('gender',1)
                                ->pluck('id')
                                ->toArray();*/

            $maleMemberIds = $this->dbMembers
                                ->whereIn('id',$memberIds)
                                ->where('gender',1)
                                ->pluck('id')
                                ->toArray();

            /*$femaleMemberIds = DB::table('mfn_member_information')
                                ->whereIn('id',$memberIds)
                                ->where('gender',2)
                                ->pluck('id')
                                ->toArray();*/

            $femaleMemberIds = $this->dbMembers
                                ->whereIn('id',$memberIds)
                                ->where('gender',2)
                                ->pluck('id')
                                ->toArray();

            // genderType 1 is for male and 2 is for female
            $genderTypeIds = [1,2];

            // Cash In Hand Legder Id
            $cashInHandLedgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');

            // get the primary product ids
            /*$primaryProductIdsFromLoan = $todayLoans->unique('primaryProductIdFk')->pluck('primaryProductIdFk')->toArray();
            $primaryProductIdsFromCollection = $loanCollections->unique('primaryProductIdFk')->pluck('primaryProductIdFk')->toArray();
            $primaryProductIdsFromRebate = $rebates->unique('primaryProductIdFk')->pluck('primaryProductIdFk')->toArray();
            $primaryProductIds = array_unique(array_merge($primaryProductIdsFromLoan,$primaryProductIdsFromCollection,$primaryProductIdsFromRebate));*/

            $primaryProductIds = explode(',',str_replace(['[',']','"'],'',DB::table('gnr_branch')->where('id',$this->targetBranchId)->value('loanProductId')));

            $primaryProducts = DB::table('mfn_loans_product')
                                    ->whereIn('id',$primaryProductIds)
                                    ->select('id','fundingOrganizationId')
                                    ->get();

            foreach ($primaryProducts as $primaryProduct) {

                // get the loan product id
                /*$productIdsFromLoan = $todayLoans->unique('productIdFk')->pluck('productIdFk')->toArray();
                $productIdsFromCollection = $loanCollections->unique('productIdFk')->pluck('productIdFk')->toArray();
                $productIdsFromRebate = $rebates->unique('loanIdFk')->pluck('loanIdFk')->toArray();
                $productIds = array_unique(array_merge($productIdsFromLoan,$productIdsFromCollection,$productIdsFromRebate));*/

                $productIds = $this->dbLoan->where('primaryProductIdFk',$primaryProduct->id)->unique('productIdFk')->pluck('productIdFk')->toArray();

                foreach ($productIds as $productId) {
                    foreach ($genderTypeIds as $genderTypeId) {

                        if ($genderTypeId==1) {
                            $currentMemberIds = $maleMemberIds;
                        }
                        else{
                            $currentMemberIds = $femaleMemberIds;                            
                        }

                        // note: rebate has no ledger id
                        $ledgerIdsFromLoan = $todayLoans->where('primaryProductIdFk',$primaryProduct->id)->where('productIdFk',$productId)->whereIn('memberIdFk',$currentMemberIds)->unique('ledgerId')->pluck('ledgerId')->toArray();
                        $ledgerIdsFromCollection = $loanCollections->where('primaryProductIdFk',$primaryProduct->id)->where('productIdFk',$productId)->whereIn('memberIdFk',$currentMemberIds)->unique('ledgerIdFk')->pluck('ledgerIdFk')->toArray();
                        $ledgerIds = array_unique(array_merge($ledgerIdsFromLoan,$ledgerIdsFromCollection));

                        // loan and collection data will be store ledger wise, and rebate,insuranceAmount information will be stored gender wise later

                        foreach ($ledgerIds as $ledgerId) {

                            if ($ledgerId==$cashInHandLedgerId) {
                                $paymentMode = "Cash";
                            }
                            else{
                                $paymentMode = "Bank";
                            }

                            $disbursmentAmount = $todayLoans->where('primaryProductIdFk',$primaryProduct->id)->where('productIdFk',$productId)->whereIn('memberIdFk',$currentMemberIds)->where('ledgerId',$ledgerId)->sum('loanAmount');
                            $repayAmount = $todayLoans->where('primaryProductIdFk',$primaryProduct->id)->where('productIdFk',$productId)->whereIn('memberIdFk',$currentMemberIds)->where('ledgerId',$ledgerId)->sum('totalRepayAmount');
                            $currentCollections = $loanCollections->where('primaryProductIdFk',$primaryProduct->id)->where('productIdFk',$productId)->whereIn('memberIdFk',$currentMemberIds)->where('ledgerIdFk',$ledgerId);
                            
                            $dayEndLoanDetails = new MfnDayEndLoanDetails;
                            $dayEndLoanDetails->branchIdFk                          = $this->targetBranchId;
                            $dayEndLoanDetails->fundingOrganizationId               = $primaryProduct->fundingOrganizationId;
                            $dayEndLoanDetails->primaryProductIdFk                  = $primaryProduct->id;
                            $dayEndLoanDetails->genderTypeId                        = $genderTypeId;
                            $dayEndLoanDetails->date                                = Carbon::parse($this->softwareDate);
                            $dayEndLoanDetails->productIdFk                         = $productId;
                            $dayEndLoanDetails->paymentMode                         = $paymentMode;
                            $dayEndLoanDetails->ledgerIdFk                          = $ledgerId;
                            $dayEndLoanDetails->disbursmentAmount                   = $disbursmentAmount;
                            $dayEndLoanDetails->repayAmount                         = $repayAmount;
                            $dayEndLoanDetails->collectionAmount                    = $currentCollections->sum('amount');
                            $dayEndLoanDetails->principaleCollectionAmount          = $currentCollections->sum('principalAmount');
                            $dayEndLoanDetails->interestCollectionAmount            = $currentCollections->sum('interestAmount');
                            

                            /// following are not ledger wise, so those are being stored outside of this block
                            // $dayEndLoanDetails->insuranceAmount             = 
                            // $dayEndLoanDetails->additionalFee               = 
                            // $dayEndLoanDetails->loanFormFee                 = 
                            // $dayEndLoanDetails->transferIn                  = 
                            // $dayEndLoanDetails->transferOut                 = 
                            // $dayEndLoanDetails->totalRecoverable            = 
                            // $dayEndLoanDetails->totalRecovery               = 
                            // $dayEndLoanDetails->regularCollectionAmount             = $tRegularCollection;
                            // $dayEndLoanDetails->regularPrincipalCollectionAmount    = $tRegularCollectionPrincipal;
                            // $dayEndLoanDetails->dueCollectionAmount                 = $tDueCollection;
                            // $dayEndLoanDetails->duePrincipalCollectionAmount        = $tDueCollectionPrincipal;
                            // $dayEndLoanDetails->advanceCollectionAmount             = $tAdvanceCollection;
                            // $dayEndLoanDetails->advancePrincipalCollectionAmount    = $tAdvanceCollectionPrincipal;
                            $dayEndLoanDetails->createdAt                           = Carbon::now();
                            $dayEndLoanDetails->updatedAt                           = Carbon::now();
                            $dayEndLoanDetails->save();
                        }                        

                        if ($genderTypeId==1) {
                            $allMemberIds = $maleMemberIdsOfthisBranch;
                        }
                        else{
                            $allMemberIds = $femaleMemberIdsOfthisBranch;
                        }

                        $currentLoan = $this->dbLoan->where('primaryProductIdFk',$primaryProduct->id)->where('productIdFk',$productId)->whereIn('memberIdFk',$allMemberIds);

                        $currentCollections = $loanCollections->where('primaryProductIdFk',$primaryProduct->id)->where('productIdFk',$productId)->whereIn('memberIdFk',$allMemberIds);

                        /*$totalRecoverableInfo = DB::table('mfn_loan_schedule as t1')
                                                ->join('mfn_loan as t2','t1.loanIdFk','t2.id')
                                                ->where('t2.branchIdFk',$this->targetBranchId)
                                                ->where('t2.primaryProductIdFk',$primaryProduct->id)
                                                ->where('t2.productIdFk',$productId)
                                                ->whereIn('t2.memberIdFk',$allMemberIds)
                                                ->where('scheduleDate',$this->softwareDate);*/

                        $totalRecoverableInfo = $this->dbSchedules
                                                ->where('primaryProductIdFk',$primaryProduct->id)
                                                ->where('productIdFk',$productId)
                                                ->whereIn('memberIdFk',$allMemberIds)
                                                ->where('scheduleDate',$this->softwareDate);



                        $totalRecoverable = $totalRecoverableInfo->sum('t1.installmentAmount');
                        $totalPrincipalRecoverable = $totalRecoverableInfo->sum('t1.principalAmount');


                        //////////////
                        $currentAllCollections = $this->dbLoanCollection->where('primaryProductIdFk',$primaryProduct->id)->where('productIdFk',$productId)->whereIn('memberIdFk',$currentMemberIds);
                        $currentLoanIds = $this->dbLoan->where('primaryProductIdFk',$primaryProduct->id)->where('productIdFk',$productId)->whereIn('memberIdFk',$currentMemberIds)->pluck('id')->toArray();
                        
                        if ($genderTypeId==1) {
                            $todayShedules = $shedules->whereIn('loanIdFk',$maleMemberLoanIds)->where('scheduleDate',$this->softwareDate);
                            $currentShedules = $shedules->whereIn('loanIdFk',$maleMemberLoanIds);
                        }
                        else{
                            $todayShedules = $shedules->whereIn('loanIdFk',$femaleMemberLoanIds)->where('scheduleDate',$this->softwareDate);
                            $currentShedules = $shedules->whereIn('loanIdFk',$femaleMemberLoanIds);
                        }
                        $loanIds = $currentCollections->unique('loanIdFk')->pluck('loanIdFk')->toArray() /*+ $todayShedules->unique('loanIdFk')->pluck('loanIdFk')->toArray()*/;
                        $loanIds = array_unique($loanIds);
                       
                        $tDueCollection = 0;
                        $tRegularCollection = 0;
                        $tAdvanceCollection = 0;

                        $tDueCollectionPrincipal = 0;
                        $tRegularCollectionPrincipal = 0;
                        $tAdvanceCollectionPrincipal = 0;

                        foreach ($loanIds as $loanId) {
                            
                            $dueCollection = 0;
                            $regularCollection = 0;
                            $advanceCollection = 0;
                            $dueCollectionPrincipal = 0;
                            $regularCollectionPrincipal = 0;
                            $advanceCollectionPrincipal = 0;

                            // for total amount
                            $previousAmountPayable = $shedules->where('loanIdFk',$loanId)->where('scheduleDate','<',$this->softwareDate)->sum('installmentAmount');
                            $previousAmountPaid = $this->dbLoanCollection->where('loanIdFk',$loanId)->where('collectionDate','<',$this->softwareDate)->sum('amount');
                            $previosDueAmount = $previousAmountPayable - $previousAmountPaid;
                            $todayCollection = $loanCollections->where('loanIdFk',$loanId)->sum('amount');

                            // for principal amount
                            $previousPrincipalPayable = $shedules->where('loanIdFk',$loanId)->where('scheduleDate','<',$this->softwareDate)->sum('principalAmount');
                            $previousPrincipalPaid = $this->dbLoanCollection->where('loanIdFk',$loanId)->where('collectionDate','<',$this->softwareDate)->sum('principalAmount');
                            $previosPrincipalDueAmount = $previousPrincipalPayable - $previousPrincipalPaid;
                            $todayCollectionPrincipal = $loanCollections->where('loanIdFk',$loanId)->sum('principalAmount');
                            $todayPayableAmount = $shedules->where('scheduleDate',$this->softwareDate)->where('loanIdFk',$loanId)->sum('installmentAmount');
                            $todayPayableAmountPrincipal = $shedules->where('scheduleDate',$this->softwareDate)->where('loanIdFk',$loanId)->sum('principalAmount');

                            if ($previosDueAmount>0) {
                                // for total amount                                
                                $dueCollection = $previosDueAmount>$todayCollection ? $todayCollection : $previosDueAmount;
                                $regularCollection = ($todayCollection - $dueCollection)>0 ? ($todayCollection - $dueCollection) : 0;
                                if ($regularCollection>0) {
                                    $regularCollection = $regularCollection > $todayPayableAmount ? $todayPayableAmount : $regularCollection;
                                }                                    
                                $advanceCollection = ($todayCollection - $regularCollection - $dueCollection)>0 ? ($todayCollection - $regularCollection - $dueCollection) : 0;
                                

                                // for principal
                                $dueCollectionPrincipal = $previosPrincipalDueAmount>$todayCollectionPrincipal ? $todayCollectionPrincipal : $previosPrincipalDueAmount;
                                $regularCollectionPrincipal = ($todayCollectionPrincipal - $dueCollectionPrincipal)>0 ? $todayCollectionPrincipal - $dueCollectionPrincipal : 0;
                                if ($regularCollectionPrincipal>0) {
                                    $regularCollectionPrincipal = $regularCollectionPrincipal > $todayPayableAmountPrincipal ? $todayPayableAmountPrincipal : $regularCollectionPrincipal;
                                }                                    
                                $advanceCollectionPrincipal = ($todayCollectionPrincipal - $regularCollectionPrincipal - $dueCollectionPrincipal)>0 ? ($todayCollectionPrincipal - $regularCollectionPrincipal - $dueCollectionPrincipal) : 0;
                            }
                            else{
                                // for total amount
                                $regularCollection = $todayCollection > $todayPayableAmount ? $todayPayableAmount : $todayCollection;
                                
                                $advanceCollection = $todayCollection > $regularCollection ? ($todayCollection - $regularCollection) : 0;

                                // for principal amount
                                $regularCollectionPrincipal = $todayCollectionPrincipal > $todayPayableAmountPrincipal ? $todayPayableAmountPrincipal : $todayCollectionPrincipal;
                                
                                $advanceCollectionPrincipal = $todayCollectionPrincipal > $regularCollectionPrincipal ? ($advanceCollectionPrincipal - $regularCollectionPrincipal) : 0;

                            }

                            $tDueCollection = $tDueCollection + $dueCollection;
                            $tRegularCollection = $tRegularCollection + $regularCollection;
                            $tAdvanceCollection = $tAdvanceCollection + $advanceCollection;

                            $tDueCollectionPrincipal = $tDueCollectionPrincipal + $dueCollectionPrincipal;
                            $tRegularCollectionPrincipal = $tRegularCollectionPrincipal + $regularCollectionPrincipal;
                            $tAdvanceCollectionPrincipal = $tAdvanceCollectionPrincipal + $advanceCollectionPrincipal;
                        }

                        //////////////

                        $dayEndLoanDetails = new MfnDayEndLoanDetails;
                        $dayEndLoanDetails->branchIdFk                  = $this->targetBranchId;
                        $dayEndLoanDetails->fundingOrganizationId       = $primaryProduct->fundingOrganizationId;
                        $dayEndLoanDetails->primaryProductIdFk          = $primaryProduct->id;
                        $dayEndLoanDetails->genderTypeId                = $genderTypeId;
                        $dayEndLoanDetails->date                        = Carbon::parse($this->softwareDate);
                        $dayEndLoanDetails->productIdFk                 = $productId;
                        $dayEndLoanDetails->insuranceAmount             = $currentLoan->sum('insuranceAmount');
                        $dayEndLoanDetails->additionalFee               = $currentLoan->sum('additionalFee');
                        $dayEndLoanDetails->loanFormFee                 = $currentLoan->sum('loanFormFee');
                        $dayEndLoanDetails->totalRecoverable            = $totalRecoverable;
                        $dayEndLoanDetails->totalPrincipalRecoverable   = $totalPrincipalRecoverable;
                        $dayEndLoanDetails->totalRecovery               = $currentCollections->sum('amount');

                        $dayEndLoanDetails->regularCollectionAmount             = $tRegularCollection;
                        $dayEndLoanDetails->regularPrincipalCollectionAmount    = $tRegularCollectionPrincipal;
                        $dayEndLoanDetails->dueCollectionAmount                 = $tDueCollection;
                        $dayEndLoanDetails->duePrincipalCollectionAmount        = $tDueCollectionPrincipal;
                        $dayEndLoanDetails->advanceCollectionAmount             = $tAdvanceCollection;
                        $dayEndLoanDetails->advancePrincipalCollectionAmount    = $tAdvanceCollectionPrincipal;

                        $dayEndLoanDetails->createdAt                   = Carbon::now();
                        $dayEndLoanDetails->updatedAt                   = Carbon::now();
                        $dayEndLoanDetails->save();
                    }
                }
            }            
        }

    }