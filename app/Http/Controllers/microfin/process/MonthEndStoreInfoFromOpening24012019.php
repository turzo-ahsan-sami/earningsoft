<?php

    namespace App\Http\Controllers\microfin\process;

    use DB;
    use Carbon\Carbon;    
    use App\Http\Controllers\Controller;
    use App\microfin\process\MfnMonthEndProcessTotalMembers;
    use App\microfin\process\MfnMonthEndProcessMembers;
    use App\microfin\process\MfnMonthEndProcessSavings; 
    use App\microfin\process\MfnMonthEndProcessLoan; 
    use App\microfin\process\MfnMonthEndProcessStaffInfo;
    use App\microfin\process\MfnMonthEndProcessLoanAreaInfo;
    use App\Http\Controllers\microfin\MicroFin;
    use Auth;

    class MonthEndStoreInfoFromOpening {

        private $targetBranchId;
        private $monthFirstDate;
        private $monthEndDate;

        function __construct($targetBranchId,$monthFirstDate,$monthEndDate){
            $this->targetBranchId = $targetBranchId;
            $this->monthFirstDate = $monthFirstDate;
            $this->monthEndDate = $monthEndDate;
        }

        public function saveData(){
            $this->storeTotalMemberInfo();
            $this->storeMemberInfo();
            $this->storeMemberInfoCategoryWise();
            $this->storeSavingsInfo();
            $this->storeLoanInfo();
            $this->storeStaffInfo();
        }

        /// this function stores data to "mfn_month_end_process_total_members" table
        public function storeTotalMemberInfo(){

            DB::table('mfn_month_end_process_total_members')->where('date',$this->monthEndDate)->where('branchIdFk',$this->targetBranchId)->delete();

            $openingSamityInfo = DB::table('mfn_opening_info_samity_total')
                                    ->where('branchIdFk',$this->targetBranchId)
                                    ->get();

            $openingMemberInfo = DB::table('mfn_opening_info_member_samity')
                                    ->where('branchIdFk',$this->targetBranchId)
                                    ->get();

            $givenFunOrgIds = $openingSamityInfo->pluck('fundingOrgIdFk')->toArray();

            $givenFunOrgIds = array_merge($givenFunOrgIds,$openingMemberInfo->pluck('fundingOrgIdFk')->toArray());

            $givenFunOrgIds = array_unique($givenFunOrgIds);

            $loanProductIds = explode(',',str_replace(['[',']','"'],'',DB::table('gnr_branch')->where('id',$this->targetBranchId)->value('loanProductId')));

            $fundingOrgIds = DB::table('mfn_loans_product')
                                 ->where('softDel',0)
                                 ->whereIn('id',$loanProductIds)
                                 ->whereIn('fundingOrganizationId',$givenFunOrgIds)
                                 ->pluck('fundingOrganizationId')
                                 ->toArray();

            foreach ($fundingOrgIds as $key => $fundingOrgId) {

                $curOpeningSamityInfo = $openingSamityInfo->where('fundingOrgIdFk',$fundingOrgId);
                $curOpeningMemberInfo = $openingMemberInfo->where('fundingOrgIdFk',$fundingOrgId);

                $fNewSamityNo = $curOpeningSamityInfo->sum('thisMonthNewFemaleSamityNo');
                $fCancelSamityNo = $curOpeningSamityInfo->sum('thisMonthCloseFemaleSamityNo');
                $fClosingSamityNo = $curOpeningSamityInfo->sum('femaleSamityNo');
                $fOpeningSamityNo = $fClosingSamityNo - $fNewSamityNo + $fCancelSamityNo;

                $fNewMemberAdmissionNo = $curOpeningMemberInfo->sum('thisMonthFemaleMemberAdmissionNo');
                $fMemberCancellationNo = $curOpeningMemberInfo->sum('thisMonthFemalememberCancelationNo');
                $fClosingMember = $curOpeningMemberInfo->sum('femaleMemberNo');
                $fOpeningMember = $fClosingMember - $fNewMemberAdmissionNo + $fMemberCancellationNo;

                $mNewSamityNo = $curOpeningSamityInfo->sum('thisMonthNewMaleSamityNo');
                $mCancelSamityNo = $curOpeningSamityInfo->sum('thisMonthCloseMaleSamityNo');
                $mClosingSamityNo = $curOpeningSamityInfo->sum('maleSamityNo');
                $mOpeningSamityNo = $mClosingSamityNo - $mNewSamityNo + $mCancelSamityNo;

                $mNewMemberAdmissionNo = $curOpeningMemberInfo->sum('thisMonthMaleMemberAdmissionNo');
                $mMemberCancellationNo = $curOpeningMemberInfo->sum('thisMonthMalememberCancelationNo');
                $mClosingMember = $curOpeningMemberInfo->sum('MaleMemberNo');
                $mOpeningMember = $mClosingMember - $mNewMemberAdmissionNo + $mMemberCancellationNo;


                $existingId = (int) DB::table('mfn_month_end_process_total_members')->where('date',$this->monthEndDate)->where('branchIdFk',$this->targetBranchId)->where('fundingOrgIdFk',$fundingOrgId)->value('id');

                if ($existingId>0) {
                    $monthEndTotalMembers = MfnMonthEndProcessTotalMembers::find($existingId);
                }
                else{
                    $monthEndTotalMembers = new MfnMonthEndProcessTotalMembers;
                }
                
                $monthEndTotalMembers->date                     = Carbon::parse($this->monthEndDate);
                $monthEndTotalMembers->branchIdFk               = $this->targetBranchId;
                $monthEndTotalMembers->fundingOrgIdFk           = $fundingOrgId;

                $monthEndTotalMembers->fOpeningSamityNo         = $fOpeningSamityNo;
                $monthEndTotalMembers->fNewSamityNo             = $fNewSamityNo;
                $monthEndTotalMembers->fCancelSamityNo          = $fCancelSamityNo;
                $monthEndTotalMembers->fClosingSamityNo         = $fClosingSamityNo;
                $monthEndTotalMembers->fOpeningMember           = $fOpeningMember;
                $monthEndTotalMembers->fNewMemberAdmissionNo    = $fNewMemberAdmissionNo;
                $monthEndTotalMembers->fNewMemberAdmissionNo_bt = 0;
                $monthEndTotalMembers->fMemberCancellationNo    = $fMemberCancellationNo;
                $monthEndTotalMembers->fMemberCancellationNo_bt = 0;
                $monthEndTotalMembers->fClosingMember           = $fClosingMember;
                $monthEndTotalMembers->fNoOfInactiveMember      = 0;
                
                $monthEndTotalMembers->mOpeningSamityNo         = $mOpeningSamityNo;
                $monthEndTotalMembers->mNewSamityNo             = $mNewSamityNo;
                $monthEndTotalMembers->mCancelSamityNo          = $mCancelSamityNo;
                $monthEndTotalMembers->mClosingSamityNo         = $mClosingSamityNo;
                $monthEndTotalMembers->mOpeningMember           = $mOpeningMember;
                $monthEndTotalMembers->mNewMemberAdmissionNo    = $mNewMemberAdmissionNo;
                $monthEndTotalMembers->mNewMemberAdmissionNo_bt = 0;
                $monthEndTotalMembers->mMemberCancellationNo    = $mMemberCancellationNo;
                $monthEndTotalMembers->mMemberCancellationNo_bt = 0;
                $monthEndTotalMembers->mClosingMember           = $mClosingMember;
                $monthEndTotalMembers->mNoOfInactiveMember      = 0;
                // $monthEndTotalMembers->uniqueClosingSamityNo    = 
                // $monthEndTotalMembers->isGrandTotal             = 
                $monthEndTotalMembers->fNoOfMemberProductOut    = 0;
                $monthEndTotalMembers->fNoOfMemberProductIn     = 0;
                $monthEndTotalMembers->mNoOfMemberProductOut    = 0;
                $monthEndTotalMembers->mNoOfMemberProductIn     = 0;
                $monthEndTotalMembers->save();
            }
        }

        /// this function stores data to "mfn_month_end_process_members" table
        public function storeMemberInfo(){

            DB::table('mfn_month_end_process_members')->where('date',$this->monthEndDate)->where('branchIdFk',$this->targetBranchId)->where('loanProductIdFk','>',0)->delete();

            $openingMemberInfo = DB::table('mfn_opening_info_member_samity')
                                    ->where('branchIdFk',$this->targetBranchId)
                                    ->get();

            $loanProductIds = explode(',',str_replace(['[',']','"'],'',DB::table('gnr_branch')->where('id',$this->targetBranchId)->value('loanProductId')));

            $loanProducts = DB::table('mfn_loans_product')
                                    ->whereIn('id',$loanProductIds)
                                    ->whereIn('id',$openingMemberInfo->pluck('productIdFk')->toArray())
                                    ->select('id','fundingOrganizationId')
                                    ->get();

            // 1 = Male, 2 = Female
            $genderTypeIds = [1,2];

            foreach ($loanProducts as $loanProduct) {
                foreach ($genderTypeIds as $genderTypeId) {

                    $curInfo = $openingMemberInfo->where('productIdFk',$loanProduct->id);

                    if ($genderTypeId==1) {
                        
                        $newSamityNo = $curInfo->sum('thisMonthNewMaleSamityNo');
                        $cancelSamityNo = $curInfo->sum('thisMonthCloseMaleSamityNo');
                        $closingSamityNo = $curInfo->sum('maleSamityNo');
                        $openingSamityNo = $closingSamityNo - $newSamityNo + $closingSamityNo;

                        $newMemberAdmissionNo = $curInfo->sum('thisMonthMaleMemberAdmissionNo');
                        $memberCancellationNo = $curInfo->sum('thisMonthMaleMemberCancelationNo');
                        $closingMember = $curInfo->sum('maleMemberNo');
                        $openingMember = $closingMember - $newMemberAdmissionNo + $memberCancellationNo;
                    }
                    else{
                        $newSamityNo = $curInfo->sum('thisMonthNewFemaleSamityNo');
                        $cancelSamityNo = $curInfo->sum('thisMonthCloseFemaleSamityNo');
                        $closingSamityNo = $curInfo->sum('femaleSamityNo');
                        $openingSamityNo = $closingSamityNo - $newSamityNo + $closingSamityNo;

                        $newMemberAdmissionNo = $curInfo->sum('thisMonthFemaleMemberAdmissionNo');
                        $memberCancellationNo = $curInfo->sum('thisMonthFemalememberCancelationNo');
                        $closingMember = $curInfo->sum('femaleMemberNo');
                        $openingMember = $closingMember - $newMemberAdmissionNo + $memberCancellationNo;
                    }

                    $avgAttendance = $curInfo->sum('avgMemberAttendence');
                     


                    $mfnMonthEndProcessMembersId = (int) DB::table('mfn_month_end_process_members')->where('date',$this->monthEndDate)->where('branchIdFk',$this->targetBranchId)->where('fundingOrgIdFk',$loanProduct->fundingOrganizationId)->where('loanProductIdFk',$loanProduct->id)->where('genderTypeId',$genderTypeId)->where('loanProductIdFk','>',0)->value('id');

                    if ($mfnMonthEndProcessMembersId>0) {
                        $monEndMember = MfnMonthEndProcessMembers::find($mfnMonthEndProcessMembersId);
                    }
                    else{
                        $monEndMember = new MfnMonthEndProcessMembers;
                    }

                    $monEndMember->date                     = Carbon::parse($this->monthEndDate);
                    $monEndMember->branchIdFk               = $this->targetBranchId;
                    $monEndMember->fundingOrgIdFk           = $loanProduct->fundingOrganizationId;
                    $monEndMember->loanProductIdFk          = $loanProduct->id;
                    $monEndMember->genderTypeId             = $genderTypeId;

                    $monEndMember->openingMember            = $openingMember;
                    $monEndMember->openingSamityNo          = $openingSamityNo;
                    $monEndMember->newSamityNo              = $newSamityNo;
                    $monEndMember->cancelSamityNo           = $cancelSamityNo;
                    $monEndMember->newMemberAdmissionNo     = $newMemberAdmissionNo;
                    $monEndMember->newMemberAdmissionNo_bpt = 0;
                    $monEndMember->memberCancellationNo     = $memberCancellationNo;
                    $monEndMember->memberCancellationNo_bpt = 0;
                    $monEndMember->avgSavingsDepositor      = 0;
                    $monEndMember->avgAttendance            = $avgAttendance;
                    $monEndMember->closingMember            = $closingMember;
                    $monEndMember->closingSamityNo          = $closingSamityNo;
                    $monEndMember->createdAt                = Carbon::now();
                    $monEndMember->save();

                }/*gender*/
            }/*loan product*/
        }

        public function storeMemberInfoCategoryWise(){

            DB::table('mfn_month_end_process_members')->where('date',$this->monthEndDate)->where('branchIdFk',$this->targetBranchId)->where('loanProductCategoryIdFk','>',0)->delete();

            $openingMemberInfo = DB::table('mfn_opening_info_member_samity')
                                    ->where('branchIdFk',$this->targetBranchId)
                                    ->get();

            $loanProductIds = explode(',',str_replace(['[',']','"'],'',DB::table('gnr_branch')->where('id',$this->targetBranchId)->value('loanProductId')));

            $loanProducts = DB::table('mfn_loans_product')
                                    ->whereIn('id',$loanProductIds)
                                    ->whereIn('id',$openingMemberInfo->pluck('productIdFk')->toArray())
                                    ->select('id','productCategoryId','fundingOrganizationId')
                                    ->get();

            $productCategoryIds = $loanProducts->unique('productCategoryId')->pluck('productCategoryId')->toArray();

            $genderTypeIds = [1,2];

            foreach ($productCategoryIds as $productCategoryId) {

                $curLoanProducts = $loanProducts->where('productCategoryId',$productCategoryId);
                $curFunOrgIds = $curLoanProducts->pluck('fundingOrganizationId')->toArray();

                foreach ($curFunOrgIds as $curFunOrgId) {
                    $loanProductIds = $loanProducts->where('productCategoryId',$productCategoryId)->where('fundingOrganizationId',$curFunOrgId)->pluck('id')->toArray();

                    foreach ($genderTypeIds as $genderTypeId) {

                        $curInfo = $openingMemberInfo->whereIn('productIdFk',$loanProductIds);

                        if ($genderTypeId==1) {
                            
                            $newSamityNo = $curInfo->sum('thisMonthNewMaleSamityNo');
                            $cancelSamityNo = $curInfo->sum('thisMonthCloseMaleSamityNo');
                            $closingSamityNo = $curInfo->sum('maleSamityNo');
                            $openingSamityNo = $closingSamityNo - $newSamityNo + $closingSamityNo;

                            $newMemberAdmissionNo = $curInfo->sum('thisMonthMaleMemberAdmissionNo');
                            $memberCancellationNo = $curInfo->sum('thisMonthMaleMemberCancelationNo');
                            $closingMember = $curInfo->sum('maleMemberNo');
                            $openingMember = $closingMember - $newMemberAdmissionNo + $memberCancellationNo;
                        }
                        else{
                            $newSamityNo = $curInfo->sum('thisMonthNewFemaleSamityNo');
                            $cancelSamityNo = $curInfo->sum('thisMonthCloseFemaleSamityNo');
                            $closingSamityNo = $curInfo->sum('femaleSamityNo');
                            $openingSamityNo = $closingSamityNo - $newSamityNo + $closingSamityNo;

                            $newMemberAdmissionNo = $curInfo->sum('thisMonthFemaleMemberAdmissionNo');
                            $memberCancellationNo = $curInfo->sum('thisMonthFemalememberCancelationNo');
                            $closingMember = $curInfo->sum('femaleMemberNo');
                            $openingMember = $closingMember - $newMemberAdmissionNo + $memberCancellationNo;
                        }

                        $avgAttendance = $curInfo->sum('avgMemberAttendence');
                         


                        $mfnMonthEndProcessMembersId = (int) DB::table('mfn_month_end_process_members')->where('date',$this->monthEndDate)->where('branchIdFk',$this->targetBranchId)->where('fundingOrgIdFk',$curFunOrgId)->where('loanProductCategoryIdFk',$productCategoryId)->where('genderTypeId',$genderTypeId)->where('loanProductCategoryIdFk','>',0)->value('id');

                        if ($mfnMonthEndProcessMembersId>0) {
                            $monEndMember = MfnMonthEndProcessMembers::find($mfnMonthEndProcessMembersId);
                        }
                        else{
                            $monEndMember = new MfnMonthEndProcessMembers;
                        }

                        $monEndMember->date                     = Carbon::parse($this->monthEndDate);
                        $monEndMember->branchIdFk               = $this->targetBranchId;
                        $monEndMember->fundingOrgIdFk           = $curFunOrgId;
                        $monEndMember->loanProductIdFk          = 0;
                        $monEndMember->loanProductCategoryIdFk  = $productCategoryId;
                        $monEndMember->genderTypeId             = $genderTypeId;

                        $monEndMember->openingMember            = $openingMember;
                        $monEndMember->openingSamityNo          = $openingSamityNo;
                        $monEndMember->newSamityNo              = $newSamityNo;
                        $monEndMember->cancelSamityNo           = $cancelSamityNo;
                        $monEndMember->newMemberAdmissionNo     = $newMemberAdmissionNo;
                        $monEndMember->newMemberAdmissionNo_bpt = 0;
                        $monEndMember->memberCancellationNo     = $memberCancellationNo;
                        $monEndMember->memberCancellationNo_bpt = 0;
                        $monEndMember->avgSavingsDepositor      = 0;
                        $monEndMember->avgAttendance            = $avgAttendance;
                        $monEndMember->closingMember            = $closingMember;
                        $monEndMember->closingSamityNo          = $closingSamityNo;
                        $monEndMember->createdAt                = Carbon::now();
                        $monEndMember->save();

                    }/*gender*/
                }
            }
        }

        /// this function stores data to "mfn_month_end_process_savings" table
        public function storeSavingsInfo(){

            DB::table('mfn_month_end_process_savings')->where('branchIdFk',$this->targetBranchId)->where('date',$this->monthEndDate)->delete();

            $savInfo = DB::table('mfn_opening_info_savings')
                            ->where('branchIdFk',$this->targetBranchId)
                            ->get();

            $primaryProductIds = explode(',',str_replace(['[',']','"'],'',DB::table('gnr_branch')->where('id',$this->targetBranchId)->value('loanProductId')));

            $primaryProducts = DB::table('mfn_loans_product')
                                    ->whereIn('id',$primaryProductIds)
                                    ->whereIn('id',$savInfo->pluck('productIdFk'))
                                    ->select('id','fundingOrganizationId')
                                    ->get();

            $savingsProductIds = DB::table('mfn_saving_product')
                                    ->where('softDel',0)
                                    ->whereIn('id',$savInfo->pluck('savingProductIdFk'))
                                    ->pluck('id')
                                    ->toArray();

            // 1= Male, 2= Female
            $genderTypeIds = [1,2];

            foreach ($primaryProducts as $primaryProduct) {
                foreach ($savingsProductIds as $savingsProductId) {

                    foreach ($genderTypeIds as $genderTypeId) {
                        $curSavInfo = $savInfo->where('productIdFk',$primaryProduct->id)->where('savingProductIdFk',$savingsProductId)->where('genderTypeId',$genderTypeId);

                        if (count($curSavInfo)==0) {
                            continue;
                        }

                        $closingBalance = $curSavInfo->sum('closingBalance');
                        $depositCollection = $curSavInfo->sum('thisMonthDepositCollection') + $curSavInfo->sum('thisMonthInterestAmount');
                        $savingRefund = $curSavInfo->sum('thisMontSavingRefund');
                        $openingBalance = $closingBalance - $depositCollection + $savingRefund;

                        $monthEndSavings = new MfnMonthEndProcessSavings;
                        $monthEndSavings->date                      = Carbon::parse($this->monthEndDate);
                        $monthEndSavings->branchIdFk                = $this->targetBranchId;
                        $monthEndSavings->productIdFk               = $primaryProduct->id;
                        $monthEndSavings->savingProductIdFk         = $savingsProductId;
                        $monthEndSavings->genderTypeId              = $genderTypeId;

                        $monthEndSavings->openingBalance            = $openingBalance;
                        $monthEndSavings->depositCollection         = $depositCollection;
                        $monthEndSavings->savingRefund              = $savingRefund;
                        $monthEndSavings->closingBalance            = $closingBalance;

                        $monthEndSavings->createdAt                 = Carbon::now();
                        $monthEndSavings->save();

                    }
                }
            }

        } /*end of storeSavingsInfo() function*/

        /**
         * this function store data to "mfn_month_end_process_loans" table
         * @return void
         */
        public function storeLoanInfo(){
            DB::table('mfn_month_end_process_loans')->where('branchIdFk',$this->targetBranchId)->where('date',$this->monthEndDate)->delete();

            $loanInfo = DB::table('mfn_opening_info_loan')
                                ->where('branchIdFk',$this->targetBranchId)
                                ->get();

            $primaryProductIds = explode(',',str_replace(['[',']','"'],'',DB::table('gnr_branch')->where('id',$this->targetBranchId)->value('loanProductId')));

            $givenLoanIds = $loanInfo->pluck('productIdFk')->toArray();

            $primaryProductIds = array_intersect($primaryProductIds, $givenLoanIds);

            // 1 = Male, 2 = Female
            $genderTypeIds = [1,2];

            foreach ($primaryProductIds as $primaryProductId) {
                foreach ($genderTypeIds as $genderTypeId) {

                    $curLoanInfo = $loanInfo->where('productIdFk',$primaryProductId)->where('genderTypeId',$genderTypeId);

                    $borrowerNo = $curLoanInfo->sum('thisMonthBorrowerNo');
                    $closingBorrowerNo = $curLoanInfo->sum('borrowerNo');
                    $openingBorrowerNo = $closingBorrowerNo - $borrowerNo;

                    // $disbursedAmount = $curLoanInfo->sum('disbursedAmount');
                    $disbursedAmount = $curLoanInfo->sum('thisMonthDisbursedAmount');
                    $repayAmount = $curLoanInfo->sum('repayAmount');
                    // $principalRecoveryAmount = $curLoanInfo->sum('principalRecoveryAmount');
                    $principalRecoveryAmount = $curLoanInfo->sum('thisMonthPrincipalRecoveryAmount');
                    // $recoveryAmount = $curLoanInfo->sum('recoveryAmount');
                    $recoveryAmount = $curLoanInfo->sum('thisMonthRecoveryAmount');
                    $fullyPaidBorrowerNo = $curLoanInfo->sum('fullyPaidBorrowerNo');
                    $closingOutstandingAmount = $curLoanInfo->sum('closingOutstandingAmount');
                    $closingOutstandingAmountWithServicesCharge = $curLoanInfo->sum('closingOutstandingAmountWithServicesCharge');
                    $closingDisbursedAmount = $curLoanInfo->sum('disbursedAmount');
                    // $principalRecoverableAmount = $curLoanInfo->sum('principalRecoverableAmount');
                    $principalRecoverableAmount = $curLoanInfo->sum('thisMonthPrincipalRecoverableAmount');
                    // $recoverableAmount = $curLoanInfo->sum('recoverableAmount');
                    $recoverableAmount = $curLoanInfo->sum('thisMonthRecoverableAmount');
                    // $principalRegularAmount = $curLoanInfo->sum('principalRegularCollectionAmount');
                    $principalRegularAmount = $curLoanInfo->sum('thisMonthPrincipalRegularCollectionAmount');
                    // $regularAmount = $curLoanInfo->sum('regularCollectionAmount');
                    $regularAmount = $curLoanInfo->sum('thisMonthRegularCollectionAmount');
                    // $principalAdvanceAmount = $curLoanInfo->sum('principalAdvanceCollectionAmount');
                    $principalAdvanceAmount = $curLoanInfo->sum('thisMonthPrincipalAdvanceCollectionAmount');
                    // $advanceAmount = $curLoanInfo->sum('advanceCollectionAmount');
                    $advanceAmount = $curLoanInfo->sum('thisMonthAdvanceCollectionAmount');
                    // $principalDueAmount = $curLoanInfo->sum('principalDueCollectionAmount');
                    $principalDueAmount = $curLoanInfo->sum('thisMonthPrincipalDueCollectionAmount');
                    // $dueAmount = $curLoanInfo->sum('dueCollectionAmount');
                    $dueAmount = $curLoanInfo->sum('thisMonthDueCollectionAmount');

                    $principalNewDueAmount = $curLoanInfo->sum('thisMonthNewDueAmount');
                    $newDueAmount = $curLoanInfo->sum('thisMonthNewDueAmountWithServicesCharge');
                    $noOfDueLoanee = $curLoanInfo->sum('noOfDueLoanee');
                    $totalNoOfDueLoaneeOnlyOptionalProduct = $curLoanInfo->sum('totalNoOfDueLoaneeOnlyOptionalProduct');
                    $noOfUniqueLoanee = $curLoanInfo->sum('noOfUniqueLoanee');
                    $loanRebateAmount = $curLoanInfo->sum('loanRebateAmount');
                    $cumBorrowerNo = $curLoanInfo->sum('cumBorrowerNo');
                    $cumLoanNo = $curLoanInfo->sum('cumLoanNo');
                    $watchfulOutstanding = $curLoanInfo->sum('watchfulOutstanding');
                    $watchfulOutstandingWithServicesCharge = $curLoanInfo->sum('watchfulOutstandingWithServicesCharge');
                    $watchfulOverdue = $curLoanInfo->sum('watchfulOverdue');
                    $watchfulOverdueWithServicesCharge = $curLoanInfo->sum('watchfulOverdueWithServicesCharge');
                    $substandardOutstanding = $curLoanInfo->sum('substandardOutstanding');
                    $substandardOutstandingWithServicesCharge = $curLoanInfo->sum('substandardOutstandingWithServicesCharge');
                    $substandardOverdue = $curLoanInfo->sum('substandardOverdue');
                    $substandardOverdueWithServicesCharge = $curLoanInfo->sum('substandardOverdueWithServicesCharge');
                    $doubtfullOutstanding = $curLoanInfo->sum('doubtfullOutstanding');
                    $doubtfullOutstandingWithServicesCharge = $curLoanInfo->sum('doubtfullOutstandingWithServicesCharge');
                    $doubtfullOverdue = $curLoanInfo->sum('doubtfullOverdue');
                    $doubtfullOverdueWithServicesCharge = $curLoanInfo->sum('doubtfullOverdueWithServicesCharge');
                    $badOutstanding = $curLoanInfo->sum('badOutstanding');
                    $badOutstandingWithServicesCharge = $curLoanInfo->sum('badOutstandingWithServicesCharge');
                    $badOverdue = $curLoanInfo->sum('badOverdue');
                    $badOverdueWithServicesCharge = $curLoanInfo->sum('badOverdueWithServicesCharge');
                    $outstandingWithMoreThan2DueInstallments = $curLoanInfo->sum('outstandingWithMoreThan2DueInstallments');
                    $outstandingWithMoreThan2DueInstallmentsServicesCharge = $curLoanInfo->sum('outstandingWithMoreThan2DueInstallmentsServicesCharge');
                    $savingBalanceOfOverdueLoanee = $curLoanInfo->sum('savingBalanceOfOverdueLoanee');

                    // $openingOutstandingAmount = $closingOutstandingAmount - $disbursedAmount + $recoveryAmount;
                    $openingOutstandingAmount = $closingOutstandingAmount - $curLoanInfo->sum('thisMonthDisbursedAmount') + $curLoanInfo->sum('thisMonthPrincipalRecoveryAmount');

                    // $openingOutstandingAmountWithServicesCharge = $closingOutstandingAmountWithServicesCharge - $disbursedAmount + $principalRecoveryAmount;
                    
                    $openingOutstandingAmountWithServicesCharge = $closingOutstandingAmountWithServicesCharge - $curLoanInfo->sum('thisMonthDisbursedAmount') + $curLoanInfo->sum('thisMonthRecoveryAmount');

                    $openingDisbursedAmount = $disbursedAmount - $curLoanInfo->sum('thisMonthDisbursedAmount');
                    $closingDueAmount = $curLoanInfo->sum('closingDueAmount');
                    $closingDueAmountWithServicesCharge = $curLoanInfo->sum('closingDueAmountWithServicesCharge');
                    // $openingDueAmount = $closingDueAmount - $curLoanInfo->sum('thisMonthNewDueAmount');
                    // $openingDueAmountWithServicesCharge = $closingDueAmountWithServicesCharge - $curLoanInfo->sum('thisMonthNewDueAmountWithServicesCharge');
                    $openingDueAmount = $closingDueAmount + $principalDueAmount - $principalNewDueAmount;
                    $openingDueAmountWithServicesCharge = $closingDueAmountWithServicesCharge + $dueAmount - $newDueAmount;

                    /*if ($primaryProductId==1 && $genderTypeId==2) {
                        echo '$openingDueAmount '.$openingDueAmount.'<br>';
                        echo '$closingDueAmount '.$closingDueAmount.'<br>';
                        echo '$curLoanInfo->sum(principalDueAmount) '.$curLoanInfo->sum('principalDueAmount').'<br><br><br>';

                        echo '$openingDueAmountWithServicesCharge '.$openingDueAmountWithServicesCharge.'<br>';
                        echo '$closingDueAmountWithServicesCharge '.$closingDueAmountWithServicesCharge.'<br>';
                        echo '$curLoanInfo->sum(dueAmount) '.$curLoanInfo->sum('dueAmount').'<br>';
                    }*/

                    $mEloanInfo = new MfnMonthEndProcessLoan;
                    $mEloanInfo->date                                                   = Carbon::parse($this->monthEndDate);
                    $mEloanInfo->branchIdFk                                             = $this->targetBranchId;
                    $mEloanInfo->productIdFk                                            = $primaryProductId;
                    $mEloanInfo->genderTypeId                                           = $genderTypeId;

                    $mEloanInfo->openingBorrowerNo                                      = $openingBorrowerNo;
                    $mEloanInfo->openingOutstandingAmount                               = $openingOutstandingAmount;
                    $mEloanInfo->openingOutstandingAmountWithServicesCharge             = $openingOutstandingAmountWithServicesCharge;
                    $mEloanInfo->openingDisbursedAmount                                 = $openingDisbursedAmount;
                    $mEloanInfo->borrowerNo                                             = $borrowerNo;
                    $mEloanInfo->disbursedAmount                                        = $disbursedAmount;
                    $mEloanInfo->repayAmount                                            = $repayAmount;
                    $mEloanInfo->principalRecoveryAmount                                = $principalRecoveryAmount;
                    $mEloanInfo->recoveryAmount                                         = $recoveryAmount;
                    $mEloanInfo->fullyPaidBorrowerNo                                    = $fullyPaidBorrowerNo;
                    $mEloanInfo->closingBorrowerNo                                      = $closingBorrowerNo;
                    $mEloanInfo->closingOutstandingAmount                               = $closingOutstandingAmount;
                    $mEloanInfo->closingOutstandingAmountWithServicesCharge             = $closingOutstandingAmountWithServicesCharge;
                    $mEloanInfo->closingDisbursedAmount                                 = $closingDisbursedAmount;
                    $mEloanInfo->openingDueAmount                                       = $openingDueAmount;
                    $mEloanInfo->openingDueAmountWithServicesCharge                     = $openingDueAmountWithServicesCharge;
                    $mEloanInfo->principalRecoverableAmount                             = $principalRecoverableAmount;
                    $mEloanInfo->recoverableAmount                                      = $recoverableAmount;
                    $mEloanInfo->principalRegularAmount                                 = $principalRegularAmount;
                    $mEloanInfo->regularAmount                                          = $regularAmount;
                    $mEloanInfo->principalAdvanceAmount                                 = $principalAdvanceAmount;
                    $mEloanInfo->advanceAmount                                          = $advanceAmount;
                    $mEloanInfo->principalDueAmount                                     = $principalDueAmount;
                    $mEloanInfo->dueAmount                                              = $dueAmount;

                    $mEloanInfo->principalNewDueAmount                                  = $principalNewDueAmount;
                    $mEloanInfo->newDueAmount                                           = $newDueAmount;
                    $mEloanInfo->closingDueAmount                                       = $closingDueAmount;
                    $mEloanInfo->closingDueAmountWithServicesCharge                     = $closingDueAmountWithServicesCharge;
                    $mEloanInfo->noOfDueLoanee                                          = $noOfDueLoanee;
                    $mEloanInfo->totalNoOfDueLoaneeOnlyOptionalProduct                  = $totalNoOfDueLoaneeOnlyOptionalProduct;
                    $mEloanInfo->noOfUniqueLoanee                                       = $noOfUniqueLoanee;
                    $mEloanInfo->loanRebateAmount                                       = $loanRebateAmount;
                    $mEloanInfo->cumBorrowerNo                                          = $cumBorrowerNo;
                    $mEloanInfo->cumLoanNo                                              = $cumLoanNo;

                    $mEloanInfo->watchfulOutstanding                                    = $watchfulOutstanding;
                    $mEloanInfo->watchfulOutstandingWithServicesCharge                  = $watchfulOutstandingWithServicesCharge;
                    $mEloanInfo->watchfulOverdue                                        = $watchfulOverdue;
                    $mEloanInfo->watchfulOverdueWithServicesCharge                      = $watchfulOverdueWithServicesCharge;
                    $mEloanInfo->substandardOutstanding                                 = $substandardOutstanding;
                    $mEloanInfo->substandardOutstandingWithServicesCharge               = $substandardOutstandingWithServicesCharge;
                    $mEloanInfo->substandardOverdue                                     = $substandardOverdue;
                    $mEloanInfo->substandardOverdueWithServicesCharge                   = $substandardOverdueWithServicesCharge;
                    $mEloanInfo->doubtfullOutstanding                                   = $doubtfullOutstanding;
                    $mEloanInfo->doubtfullOutstandingWithServicesCharge                 = $doubtfullOutstandingWithServicesCharge;
                    $mEloanInfo->doubtfullOverdue                                       = $doubtfullOverdue;
                    $mEloanInfo->doubtfullOverdueWithServicesCharge                     = $doubtfullOverdueWithServicesCharge;
                    $mEloanInfo->badOutstanding                                         = $badOutstanding;
                    $mEloanInfo->badOutstandingWithServicesCharge                       = $badOutstandingWithServicesCharge;
                    $mEloanInfo->badOverdue                                             = $badOverdue;
                    $mEloanInfo->badOverdueWithServicesCharge                           = $badOverdueWithServicesCharge;
                    $mEloanInfo->outstandingWithMoreThan2DueInstallments                = $outstandingWithMoreThan2DueInstallments;
                    $mEloanInfo->outstandingWithMoreThan2DueInstallmentsServicesCharge  = $outstandingWithMoreThan2DueInstallmentsServicesCharge;
                    $mEloanInfo->savingBalanceOfOverdueLoanee                           = $savingBalanceOfOverdueLoanee;

                    $mEloanInfo->createdAt                                              = Carbon::now();
                    $mEloanInfo->updatedAt                                              = Carbon::now();

                    $mEloanInfo->save();
                }
            }
        }

        /**
         * this function stores data to "mfn_month_end_staff_info" table
         * @return void
         */
        public function storeStaffInfo(){
            DB::table('mfn_month_end_staff_info')->where('branchIdFk',$this->targetBranchId)->where('date',$this->monthEndDate)->delete();

            $loanProductIds = explode(',',str_replace(['[',']','"'],'',DB::table('gnr_branch')->where('id',$this->targetBranchId)->value('loanProductId')));

            $loanProducts = DB::table('mfn_loans_product')
                                ->where('softDel',0)
                                ->whereIn('id',$loanProductIds)
                                ->select('id','fundingOrganizationId')
                                ->get();

            $funOrgs = DB::table('mfn_funding_organization')
                                ->whereIn('id',$loanProducts->pluck('fundingOrganizationId'))
                                ->select('projectIdFk','projectTypeIdFk')
                                ->get();

            $projectTypeIds = $funOrgs->unique('projectIdFk')->pluck('projectIdFk')->all();

            $allEmp = DB::table('hr_emp_org_info')
                        ->where('branch_id_fk',$this->targetBranchId)
                        ->whereIn('project_type_id_fk',$projectTypeIds)
                        ->select('id','position_id_fk')
                        ->get();

            $allUsers = DB::table('users')
                            ->whereIn('emp_id_fk',$allEmp->pluck('id'))
                            ->select('id','emp_id_fk')
                            ->get();

            //////////////////
            // these id will be included
            $includedIds = DB::table('hr_terminate_info')
                                    ->whereIn('users_id_fk',$allUsers->pluck('id'))
                                    ->where('effect_date','>',$this->monthEndDate)
                                    ->pluck('users_id_fk')
                                    ->toArray();

            $includedIds = $includedIds + DB::table('hr_resign_info')
                                    ->whereIn('users_id_fk',$allUsers->pluck('id'))
                                    ->where('effect_date','>',$this->monthEndDate)
                                    ->pluck('users_id_fk')
                                    ->toArray();

            $includedIds = $includedIds + DB::table('hr_retirement_info')
                                    ->whereIn('users_id_fk',$allUsers->pluck('id'))
                                    ->where('effect_date','>',$this->monthEndDate)
                                    ->pluck('users_id_fk')
                                    ->toArray();

            $tarnsferIds = DB::select("SELECT `users_id_fk` FROM `hr_transfer` as t1 WHERE `effect_date`=(SELECT MIN(`effect_date`) FROM `hr_transfer` WHERE `users_id_fk`=t1.`users_id_fk` AND effect_date>?) AND effect_date>? AND `pre_branch_id_fk`=? GROUP BY `users_id_fk`", [$this->monthEndDate,$this->monthEndDate,$this->targetBranchId]);
            $tarnsferIds = collect($tarnsferIds);
            $tarnsferIds = $tarnsferIds->pluck('users_id_fk')->all();

            $includedIds = array_merge($includedIds ,$tarnsferIds);

            // these id will be excluded
             $excludedIds = DB::select("SELECT `users_id_fk` FROM `hr_transfer` as t1 WHERE `effect_date`=(SELECT MAX(`effect_date`) FROM `hr_transfer` WHERE `users_id_fk`=t1.`users_id_fk` AND effect_date<=?) AND effect_date<=? AND `pre_branch_id_fk`=? GROUP BY `users_id_fk`", [$this->monthEndDate,$this->monthEndDate,$this->targetBranchId]);
            $excludedIds = collect($excludedIds);
            $excludedIds = $excludedIds->pluck('users_id_fk')->all();

            $includeEmpIds = DB::table('users')
                                ->whereIn('id',$includedIds)
                                ->pluck('emp_id_fk')
                                ->toArray();

            $excludeEmpIds = DB::table('users')
                                ->whereIn('id',$excludedIds)
                                ->pluck('emp_id_fk')
                                ->toArray();

            $targetBranchId = $this->targetBranchId;
            $employeeInfo = DB::table('hr_emp_org_info')
                                ->where(function($query) use ($includeEmpIds,$targetBranchId){
                                    $query->where([['branch_id_fk',$targetBranchId],['status','Active'],['job_status','Present']])
                                    ->orWhereIn('emp_id_fk',$includeEmpIds);
                                })
                                ->whereNotIn('emp_id_fk',$excludeEmpIds)
                                ->select('emp_id_fk','position_id_fk')
                                ->get();

            // get the on date position of employess
            $users = DB::table('users')
                            ->whereIn('emp_id_fk',$employeeInfo->pluck('emp_id_fk'))
                            ->select('id','emp_id_fk')
                            ->get();

            $userIdsString = implode(',',$users->pluck('id')->toArray());

            $promotions = DB::select("SELECT `users_id_fk`,`previous_data` FROM `hr_promotion_increment` as t1 WHERE `effect_month`=(SELECT MAX(`effect_month`) FROM `hr_promotion_increment` WHERE `users_id_fk`=t1.`users_id_fk` AND `effect_month`<=?) AND `effect_month`<=? AND `users_id_fk` IN (?) GROUP BY `users_id_fk`", [$this->monthEndDate,$this->monthEndDate,$userIdsString]);
            $promotions = collect($promotions);

            foreach ($promotions as $promotion) {
                $temp = json_decode($promotion->previous_data);
                $temp = $temp->value;
                $temp = collect($temp);
                $temp = $temp['position'];
                $temp = collect($temp);
                $previousPositionId = $temp['id'];
                $empId = $users->where('id',$promotion->users_id_fk)->first()->emp_id_fk;
                $employeeInfo->where('emp_id_fk',$empId)->first()->position_id_fk = $previousPositionId;
            }

            $areaManager = $employeeInfo->where('position_id_fk',131)->count();
            $zonalManager = $employeeInfo->where('position_id_fk',130)->count();
            $enterpriseOfficer = $employeeInfo->where('position_id_fk',133)->count();
            $cook = $employeeInfo->where('position_id_fk',66)->count();
            $branchManager = $employeeInfo->where('position_id_fk',125)->count();
            $creditOfficer = $employeeInfo->where('position_id_fk',122)->count();
            $asstACO = $employeeInfo->where('position_id_fk',127)->count();
            $srAsstAO = $employeeInfo->where('position_id_fk',123)->count();

            // $srAsstAO is not branch wise, so it will be taken seperately
            // this query compareable data is static against ambala foundation, it will vary company to company
           /* $srAsstAO = DB::table('hr_emp_org_info')
                            ->where('project_id_fk',1)
                            ->where('branch_id_fk',1)
                            ->where('department',2)
                            ->where('position_id_fk',116)
                            ->count();*/

            // get headOffice employee information
            // $headOffice = DB::table('')

            $staffInfo = new MfnMonthEndProcessStaffInfo;
            $staffInfo->branchIdFk          = $this->targetBranchId;
            $staffInfo->date                = Carbon::parse($this->monthEndDate);
            $staffInfo->fundingOrgId        = 0;
            $staffInfo->loanProductId       = 0;
            $staffInfo->areaManager         = $areaManager;
            $staffInfo->asstACO             = $asstACO;
            $staffInfo->branchManager       = $branchManager;
            $staffInfo->creditOfficer       = $creditOfficer;
            $staffInfo->cook                = $cook;
            $staffInfo->enterpriseOfficer   = $enterpriseOfficer;
            $staffInfo->srAsstAO            = $srAsstAO;
            $staffInfo->zonalManager        = $zonalManager;
            // $staffInfo->headOffice          = 
            $staffInfo->isGrandTotal        = 1;
            $staffInfo->save();

            /// get the time when loan product is launch samity wise
            $samities = DB::table('mfn_samity')
                            ->where('branchId',$this->targetBranchId)
                            ->where('openingDate','<=',$this->monthEndDate)
                            ->select('id','fieldOfficerId')
                            ->get();

            $samityIdsString = implode(',',$samities->pluck('id')->toArray());

            $filedOfficerChanges = DB::select("SELECT `samityId`,`fieldOfficerId` FROM `mfn_samity_field_officer_change` as t1 WHERE `effectiveDate`=(SELECT MIN(`effectiveDate`) FROM `mfn_samity_field_officer_change` WHERE `samityId`=t1.`samityId` AND `effectiveDate`>?) AND `effectiveDate`>? AND `samityId` IN (?) GROUP BY `samityId`", [$this->monthEndDate,$this->monthEndDate,$samityIdsString]);
            $filedOfficerChanges = collect($filedOfficerChanges);

            foreach ($filedOfficerChanges as $filedOfficerChange) {
                $samities->where('id',$filedOfficerChange->samityId)->first()->fieldOfficerId = $filedOfficerChange->fieldOfficerId;
            }

            $loans = DB::table('mfn_loan')
                            ->where('branchIdFk',$this->targetBranchId)
                            ->where('disbursementDate','<=',$this->monthEndDate)
                            ->groupBy('samityIdFk','primaryProductIdFk')
                            ->select('id','samityIdFk','primaryProductIdFk')
                            ->get();

            $productWiseTotalCreditOfficer = 0;
            $productWiseTotalEnterpriseOfficer = 0;

            foreach ($loanProducts as $loanProduct) {

                $samityIds = $loans->where('primaryProductIdFk',$loanProduct->id)->pluck('samityIdFk')->toArray();
                $fieldOfficerIds = $samities->whereIn('id',$samityIds)->pluck('fieldOfficerId')->toArray();

                $creditOfficer = $employeeInfo->whereIn('emp_id_fk',$fieldOfficerIds)->where('position_id_fk',122)->count();
                $enterpriseOfficer = $employeeInfo->whereIn('emp_id_fk',$fieldOfficerIds)->where('position_id_fk',133)->count();

                $productStaffInfo = new MfnMonthEndProcessStaffInfo;
                $productStaffInfo->branchIdFk          = $this->targetBranchId;
                $productStaffInfo->date                = Carbon::parse($this->monthEndDate);
                $productStaffInfo->fundingOrgId        = $loanProduct->fundingOrganizationId;
                $productStaffInfo->loanProductId       = $loanProduct->id;
                $productStaffInfo->creditOfficer       = $creditOfficer;
                $productStaffInfo->enterpriseOfficer   = $enterpriseOfficer;
                $productStaffInfo->isGrandTotal        = 0;
                $productStaffInfo->save();

                $productWiseTotalCreditOfficer += $creditOfficer;
                $productWiseTotalEnterpriseOfficer += $enterpriseOfficer;
            }
        }
    }