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

    class MonthEndStoreInfo {

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
            
            // $this->storeSavingsProbationInterest();

            // $autoVoucher = New MfnAutoVoucher($this->targetBranchId,$this->monthEndDate);
            // $autoVoucher->createProbationInterestVoucher($this->targetBranchId,$this->monthEndDate);
        }

        /// this function stores data to "mfn_month_end_process_total_members" table
        public function storeTotalMemberInfo(){
            
            $loanProductIds = explode(',',str_replace(['[',']','"'],'',DB::table('gnr_branch')->where('id',$this->targetBranchId)->value('loanProductId')));

            ///////////////////////
            $fundingOrgIds = DB::table('mfn_loans_product')
                                 ->where('softDel',0)
                                 ->whereIn('id',$loanProductIds)
                                 ->pluck('fundingOrganizationId')
                                 ->toArray();

            $monthEndNo = DB::table('mfn_month_end')
                                ->where('branchIdFk',$this->targetBranchId)
                                ->where('date','<',$this->monthEndDate)
                                ->count();

            $samity = DB::table('mfn_samity')->where('branchId',$this->targetBranchId)->select('id','samityTypeId')->get();
            $maleSamityIds = $samity->where('samityTypeId',1)->pluck('id')->toArray();
            $femaleSamityIds = $samity->where('samityTypeId',2)->pluck('id')->toArray();

            foreach ($fundingOrgIds as $key => $fundingOrgId) {
                /// get information to save data
                $femaleData = $this->getGenderWiseData($monthEndNo,$fundingOrgId,2,$maleSamityIds,$femaleSamityIds);

                $maleData = $this->getGenderWiseData($monthEndNo,$fundingOrgId,1,$maleSamityIds,$femaleSamityIds);

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

                $monthEndTotalMembers->fOpeningSamityNo         = $femaleData['openingSamityNo'];
                $monthEndTotalMembers->fNewSamityNo             = $femaleData['newSamityNo'];
                $monthEndTotalMembers->fCancelSamityNo          = $femaleData['cancelSamityNo'];
                $monthEndTotalMembers->fClosingSamityNo         = $femaleData['closingSamityNo'];
                $monthEndTotalMembers->fOpeningMember           = $femaleData['openingMember'];
                $monthEndTotalMembers->fNewMemberAdmissionNo    = $femaleData['newMemberAdmissionNo'];
                $monthEndTotalMembers->fNewMemberAdmissionNo_bt = $femaleData['newMemberAdmissionNoBt'];
                $monthEndTotalMembers->fMemberCancellationNo    = $femaleData['memberCancellationNo'];
                $monthEndTotalMembers->fMemberCancellationNo_bt = $femaleData['memberCancellationNoBt'];
                $monthEndTotalMembers->fClosingMember           = $femaleData['closingMember'];
                $monthEndTotalMembers->fNoOfInactiveMember      = $femaleData['noOfInactiveMember'];
                
                $monthEndTotalMembers->mOpeningSamityNo         = $maleData['openingSamityNo'];
                $monthEndTotalMembers->mNewSamityNo             = $maleData['newSamityNo'];
                $monthEndTotalMembers->mCancelSamityNo          = $maleData['cancelSamityNo'];
                $monthEndTotalMembers->mClosingSamityNo         = $maleData['closingSamityNo'];
                $monthEndTotalMembers->mOpeningMember           = $maleData['openingMember'];
                $monthEndTotalMembers->mNewMemberAdmissionNo    = $maleData['newMemberAdmissionNo'];
                $monthEndTotalMembers->mNewMemberAdmissionNo_bt = $maleData['newMemberAdmissionNoBt'];
                $monthEndTotalMembers->mMemberCancellationNo    = $maleData['memberCancellationNo'];
                $monthEndTotalMembers->mMemberCancellationNo_bt = $maleData['memberCancellationNoBt'];
                $monthEndTotalMembers->mClosingMember           = $maleData['closingMember'];
                $monthEndTotalMembers->mNoOfInactiveMember      = $maleData['noOfInactiveMember'];
                // $monthEndTotalMembers->uniqueClosingSamityNo    = 
                // $monthEndTotalMembers->isGrandTotal             = 
                $monthEndTotalMembers->fNoOfMemberProductOut    = $femaleData['noOfMemberProductOut'];
                $monthEndTotalMembers->fNoOfMemberProductIn     = $femaleData['noOfMemberProductIn'];
                $monthEndTotalMembers->mNoOfMemberProductOut    = $maleData['noOfMemberProductOut'];
                $monthEndTotalMembers->mNoOfMemberProductIn     = $maleData['noOfMemberProductIn'];
                $monthEndTotalMembers->save();
            }
            /////////////////////////
            
        }

        public function getGenderWiseData($monthEndNo,$fundingOrgId,$genderTypeId,$maleSamityIds,$femaleSamityIds){

            // primary products of this funding organization
            $primaryProductIds = DB::table('mfn_loans_product')
                                    ->where('fundingOrganizationId',$fundingOrgId)
                                    ->where('isPrimaryProduct',1)
                                    ->pluck('id')
                                    ->toArray();
            // gender members ids
            $genderWiseMemberIds = DB::table('mfn_member_information')
                                    ->where('softDel',0)
                                    ->where('gender',$genderTypeId) 
                                    ->pluck('id')
                                    ->toArray();

            if ($monthEndNo>=1) {
                $openingSamityNo = MfnMonthEndProcessTotalMembers::where('branchIdFk',$this->targetBranchId)
                                            ->where('fundingOrgIdFk',$fundingOrgId)
                                            ->where('date','<',$this->monthEndDate)
                                            ->orderBy('date','desc');
                if ($genderTypeId==1) {
                    $openingSamityNo = (int) $openingSamityNo->value('mClosingSamityNo');
                }
                else{
                    $openingSamityNo = (int) $openingSamityNo->value('fClosingSamityNo');
                }
            }
            else{
                $openingSamityNo = DB::table('mfn_branch_opening_informations')
                                            ->where('branchIdFk',$this->targetBranchId)
                                            ->where('fundingOrgIdFk',$fundingOrgId)
                                            ->where('genderTypeId',$genderTypeId)
                                            ->groupBy('samityIdFk')
                                            ->count();
            }

            /// new samity number is assumed as when a product of the target funding organization is assined to any member is couunted as the new samity of the target funding organization.
            $samityIds = DB::table('mfn_samity')
                                ->where('branchId',$this->targetBranchId)
                                ->where('samityTypeId',$genderTypeId)
                                ->pluck('id')
                                ->toArray();

            $members = DB::table('mfn_member_information')
                                ->where('softDel',0)
                                ->where('branchId',$this->targetBranchId)
                                ->where('gender',$genderTypeId)
                                ->whereIn('samityId',$samityIds)
                                ->select('samityId','primaryProductId','admissionDate')
                                ->get();

            $newSamityNo = 0;

            foreach($samityIds as $samityId){
                // member having product id of the funding org product ids and newly assing on this month
                $isPreviouslyAssinged = $members->where('samityId',$samityId)->whereIn('primaryProductId',$primaryProductIds)->where('admissionDate','<',$this->monthFirstDate)->count();
                if(!$isPreviouslyAssinged>0){
                    $newAssingedNum = $members->where('samityId',$samityId)->whereIn('primaryProductId',$primaryProductIds)->where('admissionDate','>=',$this->monthFirstDate)->where('admissionDate','<=',$this->monthEndDate)->count();
                    if($newAssingedNum>0){
                        $newSamityNo++;
                    }
                }
            }
            
            // New Samity Number
            /*$newSamityNo = DB::table('mfn_samity')
                                ->where('branchId',$this->targetBranchId)
                                ->where('samityTypeId',$genderTypeId)
                                ->where('openingDate','>=',$this->monthFirstDate)
                                ->where('openingDate','<=',$this->monthEndDate)
                                ->count();*/

            // Following data are the assumtion, it it be get latter.
            $cancelSamityNo = 0;
            $memberCancellationNo = 0;

            // Closing Samity No
            $closingSamityNo = $openingSamityNo + $newSamityNo - $cancelSamityNo;
                     

            // Opening Member
            if ($monthEndNo>=1) {
                $openingMember = MfnMonthEndProcessTotalMembers::where('branchIdFk',$this->targetBranchId)
                                            ->where('fundingOrgIdFk',$fundingOrgId)
                                            ->where('date','<',$this->monthEndDate)
                                            ->orderBy('date','desc',$this->monthEndDate);
                if ($genderTypeId==1) {
                    $openingMember = (int) $openingMember->value('mClosingMember'); 
                }
                elseif ($genderTypeId==2) {
                    $openingMember = (int) $openingMember->value('fClosingMember'); 
                }
            }
            else{
                $openingMember = DB::table('mfn_branch_opening_members')
                                            ->where('branchIdFk',$this->targetBranchId)
                                            ->where('fundingOrgIdFk',$fundingOrgId)
                                            ->where('genderTypeId',$genderTypeId)
                                            ->sum('totalMembers');
            }

            // Number of new member
            $newMemberAdmissionNo = DB::table('mfn_member_information')
                                            ->where('softDel',0)
                                            ->where('branchId',$this->targetBranchId)
                                            ->whereIn('primaryProductId',$primaryProductIds)
                                            ->where('admissionDate','>=',$this->monthFirstDate)
                                            ->where('admissionDate','<=',$this->monthEndDate)
                                            ->where('gender',$genderTypeId)
                                            ->count();

            $closingMember = $openingMember + $newMemberAdmissionNo - $memberCancellationNo;

            $newMemberAdmissionNoBt = 0;
            $memberCancellationNoBt = 0;            

            $noOfMemberProductOut = DB::table('mfn_member_samity_transfer')
                                            ->where('branchIdFk',$this->targetBranchId)
                                            ->whereColumn('newPrimaryProductIdFk','!=','newPrimaryProductIdFk')
                                            ->whereIn('previousPrimaryProductIdFk',$primaryProductIds)
                                            ->whereIn('memberIdFk',$genderWiseMemberIds)
                                            ->where('transferDate','>=',$this->monthFirstDate)
                                            ->where('transferDate','<=',$this->monthEndDate)
                                            ->count();

            $noOfMemberProductOut = $noOfMemberProductOut + DB::table('mfn_loan_primary_product_transfer')
                                        ->where('branchIdFk',$this->targetBranchId)
                                        ->whereIn('oldPrimaryProductFk',$primaryProductIds)
                                        ->whereIn('memberIdFk',$genderWiseMemberIds)
                                        ->where('transferDate','>=',$this->monthFirstDate)
                                        ->where('transferDate','<=',$this->monthEndDate)
                                        ->count();

            $noOfMemberProductIn = DB::table('mfn_member_samity_transfer')
                                            ->where('branchIdFk',$this->targetBranchId)
                                            ->whereColumn('newPrimaryProductIdFk','!=','newPrimaryProductIdFk')
                                            ->whereIn('newPrimaryProductIdFk',$primaryProductIds)
                                            ->whereIn('memberIdFk',$genderWiseMemberIds)
                                            ->where('transferDate','>=',$this->monthFirstDate)
                                            ->where('transferDate','<=',$this->monthEndDate)
                                            ->count();

            $noOfMemberProductIn = $noOfMemberProductIn + DB::table('mfn_loan_primary_product_transfer')
                                        ->where('branchIdFk',$this->targetBranchId)
                                        ->whereIn('newPrimaryProductFk',$primaryProductIds)
                                        ->whereIn('memberIdFk',$genderWiseMemberIds)
                                        ->where('transferDate','>=',$this->monthFirstDate)
                                        ->where('transferDate','<=',$this->monthEndDate)
                                        ->count();

            // Number of inactive members
            $memberIds = DB::table('mfn_member_information')
                            ->where('branchId',$this->targetBranchId)
                            ->whereIn('primaryProductId',$primaryProductIds)
                            ->whereIn('id',$genderWiseMemberIds)
                            ->pluck('id')
                            ->toArray();

            $noOfInactiveMember = 0;      

            /*foreach ($memberIds as $memberId) {
                if(!$this->isMemberActive($memberId,$this->monthEndDate)){
                    $noOfInactiveMember++;
                }
            }*/

            $data = array(
                'openingSamityNo'           => $openingSamityNo,
                'newSamityNo'               => $newSamityNo,
                'cancelSamityNo'            => $cancelSamityNo,
                'closingSamityNo'           => $closingSamityNo,

                'openingMember'             => $openingMember,
                'newMemberAdmissionNo'      => $newMemberAdmissionNo,
                'memberCancellationNo'      => $memberCancellationNo,
                'closingMember'             => $closingMember,

                'newMemberAdmissionNoBt'    => $newMemberAdmissionNoBt,
                'memberCancellationNoBt'    => $memberCancellationNoBt,
                'noOfMemberProductOut'      => $noOfMemberProductOut,
                'noOfMemberProductIn'       => $noOfMemberProductIn,

                'noOfInactiveMember'       => $noOfInactiveMember,
            );

            return $data;
        }

        public function isMemberActive($memberId){

            $monthEndCarbonDate = Carbon::parse($this->monthEndDate);

            $daysLength = 30;

            $memberAdmissionDate = DB::table('mfn_member_information')
                                        ->where('id',$memberId)
                                        ->value('admissionDate');

            if($monthEndCarbonDate->diffInDays(Carbon::parse($memberAdmissionDate))<=$daysLength){
                return false;
            }

            $lastDepositDate = DB::table('mfn_savings_deposit')
                                    ->where('memberIdFk',$memberId)
                                    ->where('depositDate','<=',$this->monthEndDate)
                                    ->where('softDel',0)
                                    ->max('depositDate');

            if($monthEndCarbonDate->diffInDays(Carbon::parse($lastDepositDate))<=$daysLength){
                return false;
            }

            $lastWithdrawDate = DB::table('mfn_savings_withdraw')
                                    ->where('memberIdFk',$memberId)
                                    ->where('withdrawDate','<=',$this->monthEndDate)
                                    ->where('softDel',0)
                                    ->max('withdrawDate');

            if($monthEndCarbonDate->diffInDays(Carbon::parse($lastWithdrawDate))<=$daysLength){
                return false;
            }

            $lastProductTransferDate = DB::table('mfn_loan_primary_product_transfer')
                                            ->where('softDel',0)
                                            ->where('memberIdFk',$memberId)
                                            ->where('transferDate','<=',$this->monthEndDate)
                                            ->max('transferDate');

            if($monthEndCarbonDate->diffInDays(Carbon::parse($lastProductTransferDate))<=$daysLength){
                return false;
            }

            return true;
        }


        /// this function stores data to "mfn_month_end_process_members" table
        public function storeMemberInfo(){

            $loanProductIds = explode(',',str_replace(['[',']','"'],'',DB::table('gnr_branch')->where('id',$this->targetBranchId)->value('loanProductId')));

            $loanProducts = DB::table('mfn_loans_product')
                                    ->whereIn('id',$loanProductIds)
                                    ->select('id','fundingOrganizationId')
                                    ->get();

            $members = DB::table('mfn_member_information')
                            ->where('softDel',0)
                            ->whereIn('primaryProductId',$loanProductIds)
                            ->where('branchId',$this->targetBranchId)
                            ->select('id','primaryProductId','gender')
                            ->get();

            // 1 = Male, 2 = Female
            $genderTypeIds = [1,2];

            $monthEndNo = DB::table('mfn_month_end')
                                ->where('branchIdFk',$this->targetBranchId)
                                ->where('date','<',$this->monthEndDate)
                                ->count();

            foreach ($loanProducts as $loanProduct) {
                // data will save with respect to the gender of members, one for female and one for male             
                foreach ($genderTypeIds as $genderTypeId) {

                    $thiProductMembers = $members->where('primaryProductId',$loanProduct->id)->where('gender',$genderTypeId);

                    if ($monthEndNo>=1) {
                        $lastMonthEnd = MfnMonthEndProcessMembers::where('branchIdFk',$this->targetBranchId)
                                                                ->where('loanProductIdFk',$loanProduct->id)
                                                                ->where('genderTypeId',$genderTypeId)
                                                                ->where('date','<',$this->monthEndDate)
                                                                ->orderBy('date','desc')
                                                                ->first();

                        if ($lastMonthEnd!=null) {
                            $openingMember = $lastMonthEnd->closingMember;
                            $openingSamityNo = $lastMonthEnd->closingSamityNo;
                        }
                        else{
                            $openingMember = 0;
                            $openingSamityNo = 0;
                        }
                        
                    }
                    else{
                        $openingMember = DB::table('mfn_branch_opening_members')
                                                ->where('branchIdFk',$this->targetBranchId)
                                                ->where('loanProductIdFk',$loanProduct->id)
                                                ->where('genderTypeId',$genderTypeId)
                                                ->value('totalMembers');

                        $openingSamityNo = DB::table('mfn_branch_opening_informations')
                                                ->where('branchIdFk',$this->targetBranchId)
                                                ->where('loanProductIdFk',$loanProduct->id)
                                                ->where('genderTypeId',$genderTypeId)
                                                ->groupBy('samityIdFk')
                                                ->count();
                    }

                    if($openingMember==null){
                        $openingMember = 0;
                    }

                    //////////////////////////
                    /// new samity number is assumed as when a product of the target funding organization is assined to any member is couunted as the new samity of the target funding organization.

                    $samityIds = DB::table('mfn_samity')
                                        ->where('branchId',$this->targetBranchId)
                                        ->where('samityTypeId',$genderTypeId)
                                        ->pluck('id')
                                        ->toArray();

                    $members = DB::table('mfn_member_information')
                                        ->where('softDel',0)
                                        ->where('branchId',$this->targetBranchId)
                                        ->where('gender',$genderTypeId)
                                        ->whereIn('samityId',$samityIds)
                                        ->select('id','samityId','primaryProductId','admissionDate')
                                        ->get();

                    $newSamityNo = 0;

                    foreach($samityIds as $key => $samityId){
                        // member having product id of the funding org product ids and newly assing on this month
                        $isPreviouslyAssinged = $members->where('samityId',$samityId)->where('primaryProductId',$loanProduct->id)->where('admissionDate','<',$this->monthFirstDate)->count();

                        if(!$isPreviouslyAssinged>0){
                            $newAssingedNum = $members->where('samityId',$samityId)->where('primaryProductId',$loanProduct->id)->where('admissionDate','>=',$this->monthFirstDate)->where('admissionDate','<=',$this->monthEndDate)->count();
                            
                            /*if($key==0){
                                echo "Result: ".$newAssingedNum."<br>";
                                echo "Branch Id: ".$this->targetBranchId."<br>";
                                echo "Loan Product Id: ".$loanProduct->id."<br>";
                                echo "Start Date: ".$this->monthFirstDate."<br>";
                                echo "End Date: ".$this->monthEndDate."<br>";

                                echo "<pre>";
                                print_r($samityIds);
                                echo "</pre>";

                            }*/
                            
                            if($newAssingedNum>0){
                                $newSamityNo++;
                            }
                        }
                    }
                    ///////////////////////////////

                  /*  $newSamityNo = DB::table('mfn_samity')
                                        ->where('status',1)
                                        ->where('branchId',$this->targetBranchId)
                                        ->where('openingDate','>=',$this->monthFirstDate)
                                        ->where('openingDate','<=',$this->monthEndDate)
                                        ->where('samityTypeId',$genderTypeId)
                                        ->count(); */

                    $loanProductId = $loanProduct->id;

                    $newMemberAdmissionNo = DB::table('mfn_member_information as t1')
                                                ->where('t1.softDel',0)
                                                ->where('t1.gender',$genderTypeId)
                                                ->where('t1.branchId',$this->targetBranchId)
                                                ->where('t1.primaryProductId',$loanProduct->id)
                                                ->where('t1.admissionDate','>=',$this->monthFirstDate)
                                                ->where('t1.admissionDate','<=',$this->monthEndDate)
                                                ->count();

                    $newMemberAdmissionNoBpt = DB::table('mfn_loan_primary_product_transfer')
                                                ->where('branchIdFk',$this->targetBranchId)
                                                ->whereIn('memberIdFk',$members->pluck('id'))
                                                ->where('newPrimaryProductFk',$loanProduct->id)
                                                ->where('transferDate','>=',$this->monthFirstDate)
                                                ->where('transferDate','<=',$this->monthEndDate)
                                                ->count();

                    $memberCancellationNoBpt = DB::table('mfn_loan_primary_product_transfer')
                                                ->where('branchIdFk',$this->targetBranchId)
                                                ->whereIn('memberIdFk',$members->pluck('id'))
                                                ->where('oldPrimaryProductFk',$loanProduct->id)
                                                ->where('transferDate','>=',$this->monthFirstDate)
                                                ->where('transferDate','<=',$this->monthEndDate)
                                                ->count();

                    $memberCancellationNo = DB::table('mfn_member_closing as mcl')
                                                ->join('mfn_member_information as mi','mcl.memberIdFk','mi.id')
                                                ->where('mcl.softDel',0)
                                                ->where('mi.gender',$genderTypeId)
                                                ->where('mcl.branchIdFk',$this->targetBranchId)
                                                ->where('mi.primaryProductId',$loanProduct->id)
                                                ->where('mcl.closingDate','>=',$this->monthFirstDate)
                                                ->where('mcl.closingDate','<=',$this->monthEndDate)
                                                ->count();
                    $memberCancellationNo = $memberCancellationNo + $memberCancellationNoBpt;

                    $depositsFromAutoProcess = DB::table('mfn_savings_deposit')
                                                    ->where('softDel',0)
                                                    ->whereIn('memberIdFk',$thiProductMembers->pluck('id'))
                                                    ->where('depositDate','>=',$this->monthFirstDate)
                                                    ->where('depositDate','<=',$this->monthEndDate)
                                                    ->where('isFromAutoProcess',1)
                                                    ->get();

                    $numOfWeek = $depositsFromAutoProcess->groupBy('depositDate')->count();

                    /*$depositors = $depositsFromAutoProcess->where('amount','>',0)->groupBy('depositDate')->pluck('memberIdFk')->toArray();

                    $memberString = implode(',',$thiProductMembers->pluck('id')->toArray());

                     $nonDepositors = DB::select('SELECT t1.`memberIdFk` FROM `mfn_savings_deposit` as t1 WHERE t1.`amount` = 0 and t1.`memberIdFk` not in (SELECT t2.`memberIdFk` FROM mfn_savings_deposit as t2 WHERE t1.`depositDate` = t2.`depositDate` AND t2.`amount`>0) AND t1.`depositDate`>=? AND t1.`depositDate`<=? AND t1.`isFromAutoProcess`=1 AND t1.`softDel`=0 AND t1.`memberIdFk` IN (?) GROUP BY t1.`depositDate`' , [$this->monthFirstDate,$this->monthEndDate,$memberString]);

                     if ($numOfWeek==0) {
                         $avgSavingsDepositor = 0;
                     }
                     else{
                        $avgSavingsDepositor = (count($depositors) - count($nonDepositors)) / $numOfWeek;
                     }*/


                     $nummberOfdepositors = $depositsFromAutoProcess->where('amount','>',0)->count();

                     if ($numOfWeek==0) {
                         $avgSavingsDepositor = 0;
                     }
                     else{
                        $avgSavingsDepositor = floor($nummberOfdepositors / $numOfWeek);
                     }
                     /////////  end collecting information              

                    $mfnMonthEndProcessMembersId = (int) DB::table('mfn_month_end_process_members')->where('date',$this->monthEndDate)->where('branchIdFk',$this->targetBranchId)->where('fundingOrgIdFk',$loanProduct->fundingOrganizationId)->where('loanProductIdFk',$loanProduct->id)->where('genderTypeId',$genderTypeId)->where('loanProductIdFk','>',0)->value('id');
                    if ($mfnMonthEndProcessMembersId>0) {
                        $monEndMember = MfnMonthEndProcessMembers::find($mfnMonthEndProcessMembersId);
                    }
                    else{
                        $monEndMember = new MfnMonthEndProcessMembers;
                    }

                    // This data will be countetd later, for the initialization, it is declared as zero
                    $cancelSamityNo = 0;
                    $closingMember = $openingMember + $newMemberAdmissionNo;

                    $monEndMember->date                     = Carbon::parse($this->monthEndDate);
                    $monEndMember->branchIdFk               = $this->targetBranchId;
                    $monEndMember->fundingOrgIdFk           = $loanProduct->fundingOrganizationId;
                    $monEndMember->loanProductIdFk          = $loanProduct->id;
                    $monEndMember->genderTypeId             = $genderTypeId;
                    $monEndMember->openingMember            = $openingMember;
                    $monEndMember->openingSamityNo          = $openingSamityNo;
                    $monEndMember->newSamityNo              = $newSamityNo;
                    // $monEndMember->cancelSamityNo           = 
                    $monEndMember->newMemberAdmissionNo     = $newMemberAdmissionNo;
                    // $monEndMember->newMemberAdmissionNo_bt  = 
                    $monEndMember->newMemberAdmissionNo_bpt = $newMemberAdmissionNoBpt;
                    $monEndMember->memberCancellationNo     = $memberCancellationNo;
                    // $monEndMember->memberCancellationNo_bt  = 
                    $monEndMember->memberCancellationNo_bpt = $memberCancellationNoBpt;
                    $monEndMember->avgSavingsDepositor      = $avgSavingsDepositor;
                    // $monEndMember->avgAttendance            = 
                    $monEndMember->closingMember            = $closingMember;
                    $monEndMember->closingSamityNo          = $openingSamityNo + $newSamityNo - $cancelSamityNo;
                    // $monEndMember->closingSamityNoUnique    = 
                    // $monEndMember->numOfInactiveMember      = 
                    // $monEndMember->numOfTransferOutMember   = 
                    // $monEndMember->numOfMemberProductIn     = 
                    // $monEndMember->numOfMemberProductOut    = 
                    $monEndMember->createdAt                = Carbon::now();
                    $monEndMember->save();
                }                
            }
        }


        public function storeMemberInfoCategoryWise(){            

            $loanProductIds = explode(',',str_replace(['[',']','"'],'',DB::table('gnr_branch')->where('id',$this->targetBranchId)->value('loanProductId')));

            $loanProducts = DB::table('mfn_loans_product')
                                    ->whereIn('id',$loanProductIds)
                                    ->select('id','fundingOrganizationId','productCategoryId')
                                    ->get();

            $members = DB::table('mfn_member_information')
                                    ->where('softDel',0)
                                    ->whereIn('primaryProductId',$loanProductIds)
                                    ->where('branchId',$this->targetBranchId)
                                    ->select('id','primaryProductId','gender')
                                    ->get();

            // 1 = Male, 2 = Female
            $genderTypeIds = [1,2];

            $monthEndNo = DB::table('mfn_month_end')
                                ->where('branchIdFk',$this->targetBranchId)
                                ->where('date','<',$this->monthEndDate)
                                ->count();

            $loanCategoryIds = $loanProducts->unique('productCategoryId')->pluck('productCategoryId')->toArray();

            foreach ($loanCategoryIds as $loanCategoryId) {
                $currentLoanProductIds = $loanProducts->where('productCategoryId',$loanCategoryId)->pluck('id')->toArray();
                $fundingOrganizationId = $loanProducts->where('productCategoryId',$loanCategoryId)->first()->fundingOrganizationId;

                /////
                $fundingOrganizationIds = $loanProducts->where('productCategoryId',$loanCategoryId)->unique('fundingOrganizationId')->pluck('fundingOrganizationId')->toArray();
                /////
                // data will save with respect to the gender of members, one for female and one for male

                foreach ($fundingOrganizationIds as $key => $fundingOrganizationId) {
                    foreach ($genderTypeIds as $genderTypeId) {

                        $thiProductMembers = $members->whereIn('primaryProductId',$currentLoanProductIds)->where('gender',$genderTypeId);

                        if ($monthEndNo>=1) {
                            $lastMonthEnd = MfnMonthEndProcessMembers::where('branchIdFk',$this->targetBranchId)
                                                                    ->where('loanProductCategoryIdFk',$loanCategoryId)
                                                                    ->where('genderTypeId',$genderTypeId)
                                                                    ->where('date','<',$this->monthEndDate)
                                                                    ->orderBy('date','desc')
                                                                    ->first();

                            if ($lastMonthEnd!=null) {
                                $openingMember = $lastMonthEnd->closingMember;
                                $openingSamityNo = $lastMonthEnd->closingSamityNo;
                            }
                            else{
                                $openingMember = 0;
                                $openingSamityNo = 0;
                            }
                            
                        }
                        else{
                            $openingMember = DB::table('mfn_branch_opening_members')
                                                    ->where('branchIdFk',$this->targetBranchId)
                                                    ->whereIn('loanProductIdFk',$currentLoanProductIds)
                                                    ->where('genderTypeId',$genderTypeId)
                                                    ->sum('totalMembers');

                            $openingSamityNo = DB::table('mfn_branch_opening_informations')
                                                    ->where('branchIdFk',$this->targetBranchId)
                                                    ->whereIn('loanProductIdFk',$currentLoanProductIds)
                                                    ->where('genderTypeId',$genderTypeId)
                                                    ->groupBy('samityIdFk')
                                                    ->count();
                        }

                        if($openingMember==null){
                            $openingMember = 0;
                        }

                        //////////////////////////
                        /// new samity number is assumed as when a product of the target funding organization is assined to any member is couunted as the new samity of the target funding organization.

                        $samityIds = DB::table('mfn_samity')
                                            ->where('branchId',$this->targetBranchId)
                                            ->where('samityTypeId',$genderTypeId)
                                            ->pluck('id')
                                            ->toArray();

                        $members = DB::table('mfn_member_information')
                                            ->where('softDel',0)
                                            ->where('branchId',$this->targetBranchId)
                                            ->where('gender',$genderTypeId)
                                            ->whereIn('samityId',$samityIds)
                                            ->select('samityId','primaryProductId','admissionDate')
                                            ->get();

                        $newSamityNo = 0;

                        foreach($samityIds as $key => $samityId){
                            // member having product id of the funding org product ids and newly assing on this month
                            $isPreviouslyAssinged = $members->where('samityId',$samityId)->whereIn('primaryProductId',$currentLoanProductIds)->where('admissionDate','<',$this->monthFirstDate)->count();

                            if(!$isPreviouslyAssinged>0){
                                $newAssingedNum = $members->where('samityId',$samityId)->whereIn('primaryProductId',$currentLoanProductIds)->where('admissionDate','>=',$this->monthFirstDate)->where('admissionDate','<=',$this->monthEndDate)->count();
                                
                                /*if($key==0){
                                    echo "Result: ".$newAssingedNum."<br>";
                                    echo "Branch Id: ".$this->targetBranchId."<br>";
                                    echo "Loan Product Id: ".$loanProduct->id."<br>";
                                    echo "Start Date: ".$this->monthFirstDate."<br>";
                                    echo "End Date: ".$this->monthEndDate."<br>";

                                    echo "<pre>";
                                    print_r($samityIds);
                                    echo "</pre>";

                                }*/
                                
                                if($newAssingedNum>0){
                                    $newSamityNo++;
                                }
                            }
                        }
                        ///////////////////////////////

                      /*  $newSamityNo = DB::table('mfn_samity')
                                            ->where('status',1)
                                            ->where('branchId',$this->targetBranchId)
                                            ->where('openingDate','>=',$this->monthFirstDate)
                                            ->where('openingDate','<=',$this->monthEndDate)
                                            ->where('samityTypeId',$genderTypeId)
                                            ->count();*/

                        $newMemberAdmissionNo = DB::table('mfn_member_information')
                                                    ->where('softDel',0)
                                                    ->where('gender',$genderTypeId)
                                                    ->where('branchId',$this->targetBranchId)
                                                    ->whereIn('primaryProductId',$currentLoanProductIds)
                                                    ->where('admissionDate','>=',$this->monthFirstDate)
                                                    ->where('admissionDate','<=',$this->monthEndDate)
                                                    ->count();

                        $newMemberAdmissionNoBpt = DB::table('mfn_loan_primary_product_transfer')
                                                    ->where('branchIdFk',$this->targetBranchId)
                                                    ->whereIn('memberIdFk',$members->pluck('id'))
                                                    ->whereIn('newPrimaryProductFk',$currentLoanProductIds)
                                                    ->where('transferDate','>=',$this->monthFirstDate)
                                                    ->where('transferDate','<=',$this->monthEndDate)
                                                    ->count();

                        $newMemberCancellationNoBpt = DB::table('mfn_loan_primary_product_transfer')
                                                    ->where('branchIdFk',$this->targetBranchId)
                                                    ->whereIn('memberIdFk',$members->pluck('id'))
                                                    ->whereIn('oldPrimaryProductFk',$currentLoanProductIds)
                                                    ->whereNotIn('newPrimaryProductFk',$currentLoanProductIds)
                                                    ->where('transferDate','>=',$this->monthFirstDate)
                                                    ->where('transferDate','<=',$this->monthEndDate)
                                                    ->count();

                        $depositsFromAutoProcess = DB::table('mfn_savings_deposit')
                                                        ->where('softDel',0)
                                                        ->where('branchIdFk',$this->targetBranchId)
                                                        ->whereIn('memberIdFk',$thiProductMembers->pluck('id'))
                                                        ->where('depositDate','>=',$this->monthFirstDate)
                                                        ->where('depositDate','<=',$this->monthEndDate)
                                                        ->where('isFromAutoProcess',1)
                                                        ->get();


                        $numOfWeek = $depositsFromAutoProcess->groupBy('depositDate')->count();

                        /*$depositors = $depositsFromAutoProcess->where('amount','>',0)->groupBy('depositDate')->pluck('memberIdFk')->toArray();

                        $memberString = implode(',',$thiProductMembers->pluck('id')->toArray());

                         $nonDepositors = DB::select('SELECT t1.`memberIdFk` FROM `mfn_savings_deposit` as t1 WHERE t1.`amount` = 0 and t1.`memberIdFk` not in (SELECT t2.`memberIdFk` FROM mfn_savings_deposit as t2 WHERE t1.`depositDate` = t2.`depositDate` AND t2.`amount`>0) AND t1.`depositDate`>=? AND t1.`depositDate`<=? AND t1.`isFromAutoProcess`=1 AND t1.`softDel`=0 AND t1.`memberIdFk` IN (?) GROUP BY t1.`depositDate`' , [$this->monthFirstDate,$this->monthEndDate,$memberString]);

                         if ($numOfWeek==0) {
                             $avgSavingsDepositor = 0;
                         }
                         else{
                            $avgSavingsDepositor = (count($depositors) - count($nonDepositors)) / $numOfWeek;
                         }         */ 


                         $nummberOfDepositors = $depositsFromAutoProcess->where('amount','>',0)->count();

                         if ($numOfWeek==0) {
                             $avgSavingsDepositor = 0;
                         }
                         else{
                            $avgSavingsDepositor = floor($nummberOfDepositors / $numOfWeek);
                         }

                         ///////////// end collecting information 

                        $mfnMonthEndProcessMembersId = (int) DB::table('mfn_month_end_process_members')->where('date',$this->monthEndDate)->where('branchIdFk',$this->targetBranchId)->where('fundingOrgIdFk',$fundingOrganizationId)->where('loanProductCategoryIdFk',$loanCategoryId)->where('genderTypeId',$genderTypeId)->where('loanProductCategoryIdFk','>',0)->value('id');

                        if ($mfnMonthEndProcessMembersId>0) {
                            $monEndMember = MfnMonthEndProcessMembers::find($mfnMonthEndProcessMembersId);
                        }
                        else{
                            $monEndMember = new MfnMonthEndProcessMembers;
                        }

                        // This data will be countetd later, for the initialization, it is declared as zero
                        /// assummption

                        $cancelSamityNo = 0;
                        $memberCancellationNo = 0;

                        /// end assummption
                        
                        $closingMember = $openingMember + $newMemberAdmissionNo;
                        $memberCancellationNo = $newMemberCancellationNoBpt;

                        $monEndMember->date                     = Carbon::parse($this->monthEndDate);
                        $monEndMember->branchIdFk               = $this->targetBranchId;
                        $monEndMember->fundingOrgIdFk           = $fundingOrganizationId;
                        $monEndMember->loanProductCategoryIdFk  = $loanCategoryId;
                        $monEndMember->genderTypeId             = $genderTypeId;
                        $monEndMember->openingMember            = $openingMember;
                        $monEndMember->openingSamityNo          = $openingSamityNo;
                        $monEndMember->newSamityNo              = $newSamityNo;
                        // $monEndMember->cancelSamityNo           = 
                        $monEndMember->newMemberAdmissionNo     = $newMemberAdmissionNo;
                        // $monEndMember->newMemberAdmissionNo_bt  = 
                        $monEndMember->newMemberAdmissionNo_bpt = $newMemberAdmissionNoBpt;
                        $monEndMember->memberCancellationNo     = $memberCancellationNo;
                        // $monEndMember->memberCancellationNo_bt  = 
                        $monEndMember->memberCancellationNo_bpt = $newMemberCancellationNoBpt;
                        $monEndMember->avgSavingsDepositor      = $avgSavingsDepositor;
                        // $monEndMember->avgAttendance            = 
                        $monEndMember->closingMember            = $closingMember;
                        $monEndMember->closingSamityNo          = $openingSamityNo + $newSamityNo - $cancelSamityNo;
                        // $monEndMember->closingSamityNoUnique    = 
                        // $monEndMember->numOfInactiveMember      = 
                        // $monEndMember->numOfTransferOutMember   = 
                        // $monEndMember->numOfMemberProductIn     = 
                        // $monEndMember->numOfMemberProductOut    = 
                        $monEndMember->createdAt                = Carbon::now();
                        $monEndMember->save();
                    }
                }          
                
                
            }
        
        }


        /// this function stores data to "mfn_month_end_process_savings" table
        public function storeSavingsInfo(){

            DB::table('mfn_month_end_process_savings')->where('branchIdFk',$this->targetBranchId)->where('date',$this->monthEndDate)->delete();

            $savDetails = DB::table('mfn_day_end_saving_details')
                                ->where('branchIdFk',$this->targetBranchId)
                                ->whereBetween('date',[$this->monthFirstDate,$this->monthEndDate])
                                ->get();

            $savTransferDetails = DB::table('mfn_day_end_savings_transfer_details')
                                        ->where('branchIdFk',$this->targetBranchId)
                                        ->whereBetween('date',[$this->monthFirstDate,$this->monthEndDate])
                                        ->get();

            $oldPrimaryProductIds = $savTransferDetails->unique('oldPrimaryProductIdFk')->pluck('oldPrimaryProductIdFk')->toArray();
            $newPrimaryProductIds = $savTransferDetails->unique('newPrimaryProductIdFk')->pluck('newPrimaryProductIdFk')->toArray();

            /*$primaryProductIds = $savDetails->unique('primayProductIdFk')->pluck('primayProductIdFk')->toArray();

            $primaryProductIds = array_unique(array_merge($oldPrimaryProductIds,$newPrimaryProductIds,$primaryProductIds));*/

            $primaryProductIds = explode(',',str_replace(['[',']','"'],'',DB::table('gnr_branch')->where('id',$this->targetBranchId)->value('loanProductId')));

            $primaryProducts = DB::table('mfn_loans_product')
                                    ->whereIn('id',$primaryProductIds)
                                    ->select('id','fundingOrganizationId')
                                    ->get();

            $savingsProductIds = DB::table('mfn_saving_product')
                                    ->where('softDel',0)
                                    ->pluck('id')
                                    ->toArray();

            // 1= Male, 2= Female
            $genderTypeIds = [1,2];

            foreach ($primaryProducts as $primaryProduct) {
                /*$savingsProductIds = $savDetails->where('primayProductIdFk',$primaryProduct->id)->unique('productIdFk')->pluck('productIdFk')->toArray();*/

                foreach ($savingsProductIds as $savingsProductId) {
                    foreach ($genderTypeIds as $genderTypeId) {

                        //////  fetching data  /////

                        $lastMonthEndDate = Carbon::parse($this->monthFirstDate)->subDay()->format('Y-m-d');

                        $openingBalance = (float) DB::table('mfn_month_end_process_savings')
                                                        ->where('branchIdFk',$this->targetBranchId)
                                                        ->where('date',$lastMonthEndDate)
                                                        ->where('productIdFk',$primaryProduct->id)
                                                        ->where('savingProductIdFk',$savingsProductId)
                                                        ->where('genderTypeId',$genderTypeId)
                                                        ->value('closingBalance');

                        $currentSavDetails = $savDetails->where('primayProductIdFk',$primaryProduct->id)->where('productIdFk',$savingsProductId)->where('genderTypeId',$genderTypeId);

                        $currentTransferFrom = $savTransferDetails->where('oldPrimaryProductIdFk',$primaryProduct->id)->where('savingsProductIdFk',$savingsProductId)->where('genderTypeId',$genderTypeId);

                        $currentTransferTo = $savTransferDetails->where('newPrimaryProductIdFk',$primaryProduct->id)->where('savingsProductIdFk',$savingsProductId)->where('genderTypeId',$genderTypeId);

                        $closingBalance = $openingBalance + $currentSavDetails->sum('savingDepositAmount') - $currentSavDetails->sum('savingWithdrawAmount');

                        //////  end fetching data  /////

                        $monthEndSavings = new MfnMonthEndProcessSavings;
                        $monthEndSavings->date                      = Carbon::parse($this->monthEndDate);
                        $monthEndSavings->branchIdFk                = $this->targetBranchId;
                        $monthEndSavings->productIdFk               = $primaryProduct->id;
                        $monthEndSavings->savingProductIdFk         = $savingsProductId;
                        $monthEndSavings->genderTypeId              = $genderTypeId;
                        $monthEndSavings->openingBalance            = $openingBalance;
                        // $monthEndSavings->openingBalanceInactive    = 
                        $monthEndSavings->depositCollection         = $currentSavDetails->sum('savingDepositAmount');
                        // $monthEndSavings->depositCollectionInactive = 
                        $monthEndSavings->depositCollection_bt      = 0;
                        $monthEndSavings->depositCollection_bpt     = $currentTransferTo->sum('amount');
                        $monthEndSavings->depositCollection_spt     = 0;
                        $monthEndSavings->savingRefund              = $currentSavDetails->sum('savingWithdrawAmount');
                        // $monthEndSavings->savingRefundInactive      = 
                        $monthEndSavings->savingRefund_bt           = 0;
                        $monthEndSavings->savingRefund_bpt          = $currentTransferFrom->sum('amount');
                        $monthEndSavings->savingRefund_spt          = 0;
                        $monthEndSavings->closingBalance            =  $closingBalance;
                        // $monthEndSavings->closingBalanceInactive    = 

                        $monthEndSavings->transferDeposit           = $currentTransferTo->sum('amount');
                        $monthEndSavings->transferRefund            = $currentTransferFrom->sum('amount');
                        $monthEndSavings->createdAt                 = Carbon::now();
                        $monthEndSavings->save();
                    }
                }
            }
        }

        public function getSavingsInfo($primaryProductId,$savingsProductId){
            
            $monthEndNo = DB::table('mfn_month_end')
                                ->where('branchIdFk',$this->targetBranchId)
                                ->where('date','<',$this->monthEndDate)
                                ->count();
            if ($monthEndNo>=1) {
                $openingBalance = MfnMonthEndProcessSavings::where('productIdFk',$primaryProductId)->where('savingProductIdFk',$savingsProductId)->where('date','<',$this->monthEndDate)->orderBy('date','desc')->value('closingBalance');
            }
            else{
                $openingBalanceInfo = DB::table('mfn_branch_opening_balances')
                                        ->where('savingProductIdFk',$savingsProductId)
                                        ->select('savingsDepositAmount','savingsWithdrawnAmount','savingsInterestAmount')
                                        ->first();
                if ($openingBalanceInfo!=null) {
                    $openingBalance = $openingBalanceInfo->savingsDepositAmount + $openingBalanceInfo->savingsInterestAmount - $savingsInterestAmount->savingsWithdrawnAmount;
                }
                else{
                    $openingBalance = 0;
                }
                
            }

            $data = array(
                'openingBalance'    => $openingBalance
            );
        }

        /**
         * this function store data to "mfn_month_end_process_loans" table
         * @return void
         */
        public function storeLoanInfo(){

            $targetBranchId = $this->targetBranchId;
            $monthFirstDate = $this->monthFirstDate;
            $monthEndDate = $this->monthEndDate;

            DB::table('mfn_month_end_process_loans')->where('branchIdFk',$this->targetBranchId)->where('date',$this->monthEndDate)->delete();

            $dayEndLoanDetails = DB::table('mfn_day_end_loan_details')
                                ->where('branchIdFk',$this->targetBranchId)
                                ->where('date','>=',$this->monthFirstDate)
                                ->where('date','<=',$this->monthEndDate)
                                ->get();

            $loanWriteOffs = DB::table('mfn_loan_write_off')
                                ->where('branchIdFk',$this->targetBranchId)
                                ->where('date','<=',$this->monthEndDate)
                                ->get();

            $loanWaivers = DB::table('mfn_loan_waivers')
                                ->where('branchIdFk',$this->targetBranchId)
                                ->where('date','<=',$this->monthEndDate)
                                ->get();

            $loanRebates = DB::table('mfn_loan_rebates')
                                ->where('branchIdFk',$this->targetBranchId)
                                ->where('date','<=',$this->monthEndDate)
                                ->get();                        

            // $loanProductIds = $dayEndLoanDetails->unique('productIdFk')->pluck('productIdFk');
            
            $loanProductIds = DB::table('mfn_day_end_loan_details')
                                ->where('branchIdFk',$this->targetBranchId)
                                ->where('date','<=',$this->monthEndDate)
                                ->groupBy('productIdFk')
                                ->pluck('productIdFk')
                                ->toArray();

            /*$loanProductIds = explode(',',str_replace(['[',']','"'],'',DB::table('gnr_branch')->where('id',$this->targetBranchId)->value('loanProductId')));*/

            // 1 = Male, 2 = Female
            $genderTypeIds = [1,2];

            $monthEndNo = DB::table('mfn_month_end')
                                ->where('branchIdFk',$this->targetBranchId)
                                ->where('date','<',$this->monthEndDate)
                                ->count();

            $maleMemberIds = DB::table('mfn_member_information')
                                    ->where('softDel',0)
                                    ->where('branchId',$this->targetBranchId)
                                    ->where('gender',1)
                                    ->pluck('id')
                                    ->toArray();

            $femaleMemberIds = DB::table('mfn_member_information')
                                    ->where('softDel',0)
                                    ->where('branchId',$this->targetBranchId)
                                    ->where('gender',2)
                                    ->pluck('id')
                                    ->toArray();

            $optionalLoanProductIds = DB::table('mfn_loans_product')
                                            ->where('softDel',0)
                                            ->where('isPrimaryProduct',0)
                                            ->pluck('id')
                                            ->toArray();

            $thisBranchLoans = DB::table('mfn_loan')
                                    ->where('softDel',0)
                                    ->where('branchIdFk',$this->targetBranchId)
                                    ->where('disbursementDate','<=',$this->monthEndDate)
                                    ->select('id','productIdFk','memberIdFk','loanRepayPeriodIdFk','totalRepayAmount','loanAmount','loanCompletedDate')
                                    ->get();

            $thisMonthShedules = DB::table('mfn_loan_schedule')
                                        ->where('softDel',0)
                                        ->whereIn('loanIdFk',$thisBranchLoans->pluck('id'))
                                        ->where('scheduleDate','>=',$this->monthFirstDate)
                                        ->where('scheduleDate','<=',$this->monthEndDate)
                                        ->select('loanIdFk','installmentAmount','principalAmount','scheduleDate')
                                        ->get();

            /*$thiMonthCollections = DB::table('mfn_loan_collection')
                                            ->where('softDel',0)
                                            ->where('branchIdFk',$this->targetBranchId)
                                            ->where('collectionDate','>=',$this->monthFirstDate)
                                            ->where('collectionDate','<=',$this->monthEndDate)
                                            ->select('loanIdFk','productIdFk','primaryProductIdFk','memberIdFk','amount','principalAmount')
                                            ->get();*/

            $allCollections = DB::table('mfn_loan_collection')
                                    ->where('softDel',0)
                                    ->whereIn('loanIdFk',$thisBranchLoans->pluck('id'))
                                    ->where('collectionDate','<=',$this->monthEndDate)
                                    ->select('loanIdFk','memberIdFk','primaryProductIdFk','productIdFk','amount','principalAmount','interestAmount','ledgerIdFk','collectionDate')
                                    ->get();

            $thiMonthCollections = $allCollections->where('collectionDate','>=',$this->monthFirstDate);

            $shedules = DB::table('mfn_loan_schedule')
                            ->where('softDel',0)
                            ->whereIn('loanIdFk',$thisBranchLoans->pluck('id'))
                            ->where('scheduleDate','<=',$this->monthEndDate)
                            ->select('loanIdFk','installmentAmount','principalAmount','interestAmount','scheduleDate','isCompleted')
                            ->get();

            $watchfulLoanIds = [];
            $substandarLoanIds = [];
            $doubtfullLoanIds = [];

            $repayments = DB::table('mfn_loan_repay_period')->get();
            $monthEndDate = Carbon::parse($this->monthEndDate);
            $monthEndDateFormat = $this->monthEndDate;

            // get the loans which have due at the end of month
            $thisBranchActiveLoans = DB::table('mfn_loan')
                                    ->where('softDel',0)
                                    ->where('branchIdFk',$this->targetBranchId)
                                    ->where('disbursementDate','<=',$this->monthEndDate)
                                    ->where(function($query) use ($monthEndDateFormat){
                                        $query->where('loanCompletedDate','>',$monthEndDateFormat)
                                            ->orWhere('loanCompletedDate','0000-00-00');
                                    })
                                    ->select('id','loanRepayPeriodIdFk')
                                    ->get();
            foreach ($thisBranchActiveLoans as $thisBranchLoan) {
                $payableAmount = $shedules->where('loanIdFk',$thisBranchLoan->id)->sum('installmentAmount');
                $paidAmount = $allCollections->where('loanIdFk',$thisBranchLoan->id)->sum('amount');
                if ($payableAmount - $paidAmount > 0) {
                    $lastCollectioDate = $allCollections->where('loanIdFk',$thisBranchLoan->id)->max('collectionDate');
                    $lastCollectioDate = Carbon::parse($lastCollectioDate);
                    $loanYear = (int) $repayments->where('id',$thisBranchLoan->loanRepayPeriodIdFk)->first()->inMonths / 12;
                    $daysDifference = $monthEndDate->diffInDays($lastCollectioDate);
                    $daysDifference = (int) $daysDifference/$loanYear;
                    if ($daysDifference<=30) {
                        array_push($watchfulLoanIds, $thisBranchLoan->id);
                    }
                    elseif ($daysDifference>30 && $daysDifference<=180) {
                        array_push($substandarLoanIds, $thisBranchLoan->id);
                    }
                    elseif ($daysDifference>180 && $daysDifference<=365) {
                        array_push($doubtfullLoanIds, $thisBranchLoan->id);
                    }
                }
            }

            /// get info for expired loans

            $expiredLoans = DB::table('mfn_loan')
                                ->where('softDel',0)
                                ->where('branchIdFk',$this->targetBranchId)
                                ->where('lastInstallmentDate','<',$this->monthEndDate)
                                ->where('loanCompletedDate','>',$this->monthEndDate)
                                ->select('id','productIdFk','memberIdFk','loanAmount','totalRepayAmount')
                                ->get();

            $expiredLoanIds = $expiredLoans->pluck('id')->toArray();
            $watchfulLoanIds = array_diff($watchfulLoanIds,$expiredLoanIds);
            $substandarLoanIds = array_diff($substandarLoanIds,$expiredLoanIds);
            $doubtfullLoanIds = array_diff($doubtfullLoanIds,$expiredLoanIds);

            $overDueLoanIds = array_merge($expiredLoanIds,$watchfulLoanIds,$substandarLoanIds,$doubtfullLoanIds);

            $overDueLoans = DB::table('mfn_loan')
                                ->whereIn('id',$overDueLoanIds)
                                ->select('id','productIdFk','memberIdFk','loanAmount','totalRepayAmount')
                                ->get();

            $expiredMalesMemberIds = $expiredLoans->whereIn('memberIdFk',$maleMemberIds)->unique('memberIdFk')->pluck('memberIdFk')->toArray();
            $expiredFemaleMemberIds = $expiredLoans->whereIn('memberIdFk',$femaleMemberIds)->unique('memberIdFk')->pluck('memberIdFk')->toArray();

            $overDueMalesMemberIds = $overDueLoans->whereIn('memberIdFk',$maleMemberIds)->unique('memberIdFk')->pluck('memberIdFk')->toArray();
            $overDueFemaleMemberIds = $overDueLoans->whereIn('memberIdFk',$femaleMemberIds)->unique('memberIdFk')->pluck('memberIdFk')->toArray();

            $savingsDepositsOfExpiredLoanMembers = DB::table('mfn_savings_deposit')
                                                            ->where('softDel',0)
                                                            ->where('depositDate','<=',$this->monthEndDate)
                                                            ->whereIn('memberIdFk',$overDueLoans->pluck('memberIdFk'))
                                                            ->select('amount','memberIdFk','primaryProductIdFk')
                                                            ->get();

            $savingsWIthdrawsOfExpiredLoanMembers = DB::table('mfn_savings_withdraw')
                                                            ->where('softDel',0)
                                                            ->where('withdrawDate','<=',$this->monthEndDate)
                                                            ->whereIn('memberIdFk',$overDueLoans->pluck('memberIdFk'))
                                                            ->select('amount','memberIdFk','primaryProductIdFk')
                                                            ->get();

            

            /*$pastDay30 = Carbon::parse($this->monthEndDate)->subDays(30)->format('Y-m-d');
            $pastDay180 = Carbon::parse($this->monthEndDate)->subDays(180)->format('Y-m-d');
            $pastDay365 = Carbon::parse($this->monthEndDate)->subDays(365)->format('Y-m-d');*/

            /*$watchfulLoanIds = $expiredLoans->where('lastInstallmentDate','>=',$pastDay30)->pluck('id')->toArray();
            $substandarLoanIds = $expiredLoans->where('lastInstallmentDate','<',$pastDay30)->where('lastInstallmentDate','>=',$pastDay180)->pluck('id')->toArray();
            $doubtfullLoanIds = $expiredLoans->where('lastInstallmentDate','<',$pastDay180)->where('lastInstallmentDate','>=',$pastDay365)->pluck('id')->toArray();*/
            $badLoanIds = $expiredLoans/*->where('lastInstallmentDate','<',$pastDay365)*/->pluck('id')->toArray();

            /// end get info for expired loans


            foreach ($loanProductIds as $loanProductId) {
                foreach ($genderTypeIds as $genderTypeId) {
                    ////////////   Collect the information
                    $openingData = $this->storeLoanInfoHelper($loanProductId,$genderTypeId,$monthEndNo);
                    
                    $currentLoanDetails = $dayEndLoanDetails->where('productIdFk',$loanProductId)->where('genderTypeId',$genderTypeId);

                    if ($genderTypeId==1) {
                        $memberIds = $maleMemberIds;
                    }
                    else{
                        $memberIds = $femaleMemberIds;
                    }

                    $thisMonthLoans = DB::table('mfn_loan')
                                        ->where('softDel',0)
                                        ->where('branchIdFk',$this->targetBranchId)
                                        ->where('productIdFk',$loanProductId)
                                        ->where('disbursementDate','>=',$this->monthFirstDate)
                                        ->where('disbursementDate','<=',$this->monthEndDate)
                                        ->whereIn('memberIdFk',$memberIds)
                                        ->select('id','loanAmount','totalRepayAmount','memberIdFk')
                                        ->get();

                    $borrowerNo = $thisMonthLoans->unique('memberIdFk')->count();                    

                    $disbursedAmount = $thisMonthLoans->sum('loanAmount');
                    $repayAmount = $thisMonthLoans->sum('totalRepayAmount');

                    // $recoveryAmount = (float) $currentLoanDetails->sum('collectionAmount');
                    // $principalRecoveryAmount = (float) $currentLoanDetails->sum('principaleCollectionAmount');

                    // $regularRecoveryAmount = (float) $currentLoanDetails->sum('regularCollectionAmount');
                    // $regularPrincipalRecoveryAmount = (float) $currentLoanDetails->sum('regularPrincipalCollectionAmount');
                    
                    // $dueRecoveryAmount = (float) $currentLoanDetails->sum('dueCollectionAmount');
                    // $duePrincipalRecoveryAmount = (float) $currentLoanDetails->sum('duePrincipalCollectionAmount');

                    $queryValues = [$this->targetBranchId,$this->targetBranchId,$genderTypeId,$this->monthFirstDate,$this->monthEndDate];

                    $fullyPaidBorrowers = DB::table('mfn_loan')
                                                ->where('softDel',0)
                                                ->where('branchIdFk',$this->targetBranchId)
                                                ->whereIn('memberIdFk',$memberIds)
                                                ->where('productIdFk',$loanProductId)
                                                ->where('loanCompletedDate','>=',$this->monthFirstDate)            
                                                ->where('loanCompletedDate','<=',$this->monthEndDate)
                                                ->groupBy('memberIdFk')
                                                ->pluck('memberIdFk')
                                                ->toArray();

                    $fullyPaidBorrowerNo = count($fullyPaidBorrowers);

                    $currentLoans = DB::table('mfn_loan')
                                        ->where('softDel',0)
                                        ->where('branchIdFk',$this->targetBranchId)
                                        ->where('productIdFk',$loanProductId)
                                        ->where('disbursementDate','<=',$this->monthEndDate)
                                        ->whereIn('memberIdFk',$memberIds)
                                        ->select('id','memberIdFk')
                                        ->get();

                    //$closingBorrowerNo = $currentLoans->unique('memberIdFk')->count();
                    $closingBorrowerNo = $borrowerNo + (int) $openingData['openingBorrowerNo'] - $fullyPaidBorrowerNo;

                    $loanIds = $currentLoans->pluck('id')->toArray();

                    $lastMonthLastDate = Carbon::parse($this->monthFirstDate)->subDay()->format('Y-m-d');

                    $lastMonthClosingDisbursedAmount = (float) DB::table('mfn_month_end_process_loans')
                                                                    ->where('branchIdFk',$this->targetBranchId)
                                                                    ->where('date',$lastMonthLastDate)
                                                                    ->where('productIdFk',$loanProductId)
                                                                    ->where('genderTypeId',$genderTypeId)
                                                                    ->value('closingDisbursedAmount');

                    $closingDisbursedAmount = $lastMonthClosingDisbursedAmount + $disbursedAmount;

                    // $principalRecoverableAmount = $currentLoanDetails->sum('totalPrincipalRecoverable');
                    // $recoverableAmount = $currentLoanDetails->sum('totalRecoverable');
                    // $loanRebateAmount = $currentLoanDetails->sum('rebateAmount');
                    $noOfUniqueLoanee = $thisMonthLoans->unique('memberIdFk')->count();
                    $cumBorrowerNo = $currentLoans->unique('memberIdFk')->count();
                    $cumLoanNo = $currentLoans->count();

                    

                    $loanRebateAmount = round($loanRebates->whereIn('loanIdFk',$currentLoans->pluck('id')->toArray())->sum('amount'));

                    

                    $advanceAmount = $currentLoanDetails->sum('advanceCollectionAmount');
                    $principalAdvanceAmount = $currentLoanDetails->sum('advancePrincipalCollectionAmount');
                   

                   ///// new code to fetch regular, due, advance collection

                    $currentLoanIds = $thisBranchLoans
                                        ->where('productIdFk',$loanProductId)
                                        ->whereIn('memberIdFk',$memberIds)
                                        ->pluck('id')
                                        ->toArray();

                    $tDueCollection = 0;
                    $tRegularCollection = 0;
                    $tAdvanceCollection = 0;

                    $tDueCollectionPrincipal = 0;
                    $tRegularCollectionPrincipal = 0;
                    $tAdvanceCollectionPrincipal = 0;

                    $tClosingDue = 0;
                    $tClosingDuePrincipal = 0;

                    $tRecoverableAmount = 0;
                    $tPrincipalRecoverableAmount = 0;

                    $dueLoanIds = array();
                    $newDueAmount = 0;
                    $principalNewDueAmount = 0;

                    foreach ($currentLoanIds as $loanId) {
                        
                        $dueCollection = 0;
                        $regularCollection = 0;
                        $advanceCollection = 0;
                        $dueCollectionPrincipal = 0;
                        $regularCollectionPrincipal = 0;
                        $advanceCollectionPrincipal = 0;

                        $recoverableAmount = 0;
                        $principalRecoverableAmount = 0;

                        // loan waiver interest amount is within into the rebate amount, so waiver amount is skiped and only principal amount is taken.
                        // this month
                        $cWriteOffAmount = $loanWriteOffs->where('date','>=',$this->monthFirstDate)->where('loanIdFk',$loanId)->sum('amount');
                        $cWriteOffAmountPrincipal = $loanWriteOffs->where('date','>=',$this->monthFirstDate)->where('loanIdFk',$loanId)->sum('principalAmount');
                        $cWaiverAmountPrincipal = $loanWaivers->where('date','>=',$this->monthFirstDate)->where('loanIdFk',$loanId)->sum('principalAmount');
                        $cRebateAmount = $loanRebates->where('date','>=',$this->monthFirstDate)->where('loanIdFk',$loanId)->sum('amount');
                        $cPaidWithServiceCharge = $cWriteOffAmount + $cWaiverAmountPrincipal + $cRebateAmount;
                        $cPaidWithOutServiceCharge = $cWriteOffAmountPrincipal + $cWaiverAmountPrincipal;

                        // previous data
                        $pWriteOffAmount = $loanWriteOffs->where('date','<',$this->monthFirstDate)->where('loanIdFk',$loanId)->sum('amount');
                        $pWriteOffAmountPrincipal = $loanWriteOffs->where('date','<',$this->monthFirstDate)->where('loanIdFk',$loanId)->sum('principalAmount');
                        $pWaiverAmountPrincipal = $loanWaivers->where('date','<',$this->monthFirstDate)->where('loanIdFk',$loanId)->sum('');
                        $pRebateAmount = $loanRebates->where('date','<',$this->monthFirstDate)->where('loanIdFk',$loanId)->sum('amount');
                        $pPaidWithServiceCharge = $pWriteOffAmount + $pWaiverAmountPrincipal + $pRebateAmount;
                        $pPaidWithOutServiceCharge = $pWriteOffAmountPrincipal + $pWaiverAmountPrincipal;


                        // for total amount
                        $previousAmountPayable = $shedules->where('loanIdFk',$loanId)->where('scheduleDate','<',$this->monthFirstDate)->sum('installmentAmount');
                        $previousAmountPaid = $allCollections->where('loanIdFk',$loanId)->where('collectionDate','<',$this->monthFirstDate)->sum('amount') + $pPaidWithServiceCharge;
                        $previosDueAmount = $previousAmountPayable - $previousAmountPaid;
                        $thisMonthCollectionAmount = $thiMonthCollections->where('loanIdFk',$loanId)->sum('amount') + $cPaidWithServiceCharge;

                        // for principal amount
                        $previousPrincipalPayable = $shedules->where('loanIdFk',$loanId)->where('scheduleDate','<',$this->monthFirstDate)->sum('principalAmount');
                        $previousPrincipalPaid = $allCollections->where('loanIdFk',$loanId)->where('collectionDate','<',$this->monthFirstDate)->sum('principalAmount') + $pPaidWithOutServiceCharge;
                        $previosPrincipalDueAmount = $previousPrincipalPayable - $previousPrincipalPaid;
                        $thisMonthCollectionAmountPrincipal = $thiMonthCollections->where('loanIdFk',$loanId)->sum('principalAmount') + $cPaidWithOutServiceCharge;
                        $thisMonthPayableAmount = $thisMonthShedules->where('loanIdFk',$loanId)->sum('installmentAmount');
                        $thisMonthPayableAmountPrincipal = $thisMonthShedules->where('loanIdFk',$loanId)->sum('principalAmount');                        

                        $advanceAmount = - $previosDueAmount;
                        $advancePrincipalAmount = - $previosPrincipalDueAmount;

                        /*if ($loanId==1296) {
                            echo '$previousPrincipalPayable: '.$previousPrincipalPayable.'<br>';
                            echo '$previousPrincipalPaid: '.$previousPrincipalPaid.'<br>';
                            echo '$previosPrincipalDueAmount: '.$previosPrincipalDueAmount.'<br>';
                            echo '$thisMonthCollectionAmountPrincipal: '.$thisMonthCollectionAmountPrincipal.'<br>';
                            echo '$thisMonthPayableAmount: '.$thisMonthPayableAmount.'<br>';
                            echo '$thisMonthPayableAmountPrincipal: '.$thisMonthPayableAmountPrincipal.'<br>';
                            echo '$advanceAmount: '.$advanceAmount.'<br>';
                            echo '$advancePrincipalAmount: '.$advancePrincipalAmount.'<br>';
                        }*/

                        if ($previosDueAmount>0) {
                            // for total amount                                
                            $dueCollection = $previosDueAmount>$thisMonthCollectionAmount ? $thisMonthCollectionAmount : $previosDueAmount;
                            $regularCollection = ($thisMonthCollectionAmount - $dueCollection)>0 ? ($thisMonthCollectionAmount - $dueCollection) : 0;
                            if ($regularCollection>0) {
                                $regularCollection = $regularCollection > $thisMonthPayableAmount ? $thisMonthPayableAmount : $regularCollection;
                            }
                                                          
                            $advanceCollection = ($thisMonthCollectionAmount - $regularCollection - $dueCollection)>0 ? ($thisMonthCollectionAmount - $regularCollection - $dueCollection) : 0;

                            // for principal
                            $dueCollectionPrincipal = $previosPrincipalDueAmount-$thisMonthCollectionAmountPrincipal>=1 ? $thisMonthCollectionAmountPrincipal : $previosPrincipalDueAmount;
                            $regularCollectionPrincipal = ($thisMonthCollectionAmountPrincipal - $dueCollectionPrincipal)>=1 ? $thisMonthCollectionAmountPrincipal - $dueCollectionPrincipal : 0;
                            if ($regularCollectionPrincipal>=1) {
                                $regularCollectionPrincipal = $regularCollectionPrincipal > $thisMonthPayableAmountPrincipal ? $thisMonthPayableAmountPrincipal : $regularCollectionPrincipal;
                            }
                            if ($thisMonthPayableAmountPrincipal + $previosPrincipalDueAmount - $thisMonthCollectionAmountPrincipal>=1) {
                                
                                $principalNewDueAmount += $thisMonthPayableAmountPrincipal + $previosPrincipalDueAmount - $thisMonthCollectionAmountPrincipal;
                                $newDueAmount += $thisMonthPayableAmount + $previosDueAmount - $thisMonthCollectionAmount;
                                
                                
                               /* echo 'IN IF<br>';
                                echo '$loanId: '.$loanId.'<br>';
                                echo 'BRANCH ID: '.$this->targetBranchId.'<br>';
                                echo '$thisMonthPayableAmountPrincipal: '.$thisMonthPayableAmountPrincipal.'<br>';
                                echo '$previosPrincipalDueAmount: '.$previosPrincipalDueAmount.'<br>';
                                echo '$thisMonthCollectionAmountPrincipal: '.$thisMonthCollectionAmountPrincipal.'<br>';
                                echo '$newDueAmount'.$newDueAmount.'<br><br>';*/
                                
                                array_push($dueLoanIds, $loanId);
                            }                              
                            $advanceCollectionPrincipal = ($thisMonthCollectionAmountPrincipal - $regularCollectionPrincipal - $dueCollectionPrincipal)>=1 ? ($thisMonthCollectionAmountPrincipal - $regularCollectionPrincipal - $dueCollectionPrincipal) : 0;

                        }
                        // collection is equal or advance
                        else{
                            // for total amount

                            $collectableAmount = $thisMonthPayableAmount - $advanceAmount;
                            $collectablePrincipalAmount = $thisMonthPayableAmountPrincipal - $advancePrincipalAmount;

                            if ($collectableAmount>0) {
                                $regularCollection = $thisMonthCollectionAmount > $collectableAmount ? $collectableAmount : $thisMonthCollectionAmount;
                                $dueAmount = $collectableAmount - $regularCollection;
                                if ($dueAmount>0) {
                                    array_push($dueLoanIds, $loanId);                                    
                                    $newDueAmount += $collectableAmount - $regularCollection;
                                }

                            }
                            else{
                                $regularCollection = 0;
                            }
                            
                            $advanceCollection = $thisMonthCollectionAmount > $regularCollection ? ($thisMonthCollectionAmount - $regularCollection) : 0;

                            // for principal amount
                            if ($collectablePrincipalAmount>0) {
                                $regularCollectionPrincipal = $thisMonthCollectionAmountPrincipal > $collectablePrincipalAmount ? $collectablePrincipalAmount : $thisMonthCollectionAmountPrincipal;
                                $duePrincipal = $collectablePrincipalAmount - $regularCollectionPrincipal;
                                if ($duePrincipal>=1) {
                                    $principalNewDueAmount += $collectablePrincipalAmount - $regularCollectionPrincipal;
                                }
                            }
                            else{
                                $regularCollectionPrincipal = 0;
                            }
                            /*$regularCollectionPrincipal = $thisMonthCollectionAmountPrincipal - $thisMonthPayableAmountPrincipal >=1 ? $thisMonthPayableAmountPrincipal : $thisMonthCollectionAmountPrincipal;*/

                            /*if ($thisMonthPayableAmountPrincipal - $thisMonthCollectionAmountPrincipal - $advancePrincipalAmount>=1) {
                                $principalNewDueAmount += $thisMonthPayableAmountPrincipal - $thisMonthCollectionAmountPrincipal;
                                $newDueAmount += $thisMonthPayableAmount - $thisMonthCollectionAmount;
                                array_push($dueLoanIds, $loanId);
                            } */
                            
                            $advanceCollectionPrincipal = ($thisMonthCollectionAmountPrincipal - $regularCollectionPrincipal)>=1 ? ($thisMonthCollectionAmountPrincipal - $regularCollectionPrincipal) : 0;
                        }

                        if ($advanceAmount>0) {
                            $recoverableAmount = $thisMonthPayableAmount - $advanceAmount;
                            $recoverableAmount = $recoverableAmount > 0 ? $recoverableAmount : 0;

                            $principalRecoverableAmount = $thisMonthPayableAmountPrincipal - $advancePrincipalAmount;
                            $principalRecoverableAmount = $principalRecoverableAmount > 0 ? $principalRecoverableAmount : 0;
                        }
                        else{
                            $recoverableAmount = $thisMonthPayableAmount;
                            $principalRecoverableAmount = $thisMonthPayableAmountPrincipal;
                        }

                        /*$recoverableAmount = $thisMonthPayableAmount + $previosDueAmount > 0 ? $thisMonthPayableAmount + $previosDueAmount : 0;
                        $principalRecoverableAmount = $thisMonthPayableAmountPrincipal + $previosPrincipalDueAmount > 0? $thisMonthPayableAmountPrincipal + $previosPrincipalDueAmount : 0;*/

                        /*if ($regularCollection>$recoverableAmount) {
                            echo 'Branch Id: '.$this->targetBranchId.'<br>';
                            echo 'Loan Id: '.$loanId.'<br>';
                            echo '$recoverableAmount: '.$recoverableAmount.'<br>';
                            echo '$regularCollection: '.$regularCollection.'<br>';
                        }*/

                        if ($thisMonthCollectionAmount<=0) {
                            $dueCollection = 0;
                            $regularCollection = 0;
                            $advanceCollection = 0;

                            $dueCollectionPrincipal = 0;
                            $regularCollectionPrincipal = 0;
                            $advanceCollectionPrincipal = 0;
                        }
                        

                        $tDueCollection = $tDueCollection + $dueCollection;
                        $tRegularCollection = $tRegularCollection + $regularCollection;
                        $tAdvanceCollection = $tAdvanceCollection + $advanceCollection;

                        $tDueCollectionPrincipal = $tDueCollectionPrincipal + $dueCollectionPrincipal;
                        $tRegularCollectionPrincipal = $tRegularCollectionPrincipal + $regularCollectionPrincipal;
                        $tAdvanceCollectionPrincipal = $tAdvanceCollectionPrincipal + $advanceCollectionPrincipal;

                        $tRecoverableAmount = $tRecoverableAmount + $recoverableAmount;
                        $tPrincipalRecoverableAmount = $tPrincipalRecoverableAmount + $principalRecoverableAmount;

                        $closingDue = $shedules->where('loanIdFk',$loanId)->sum('installmentAmount') - $allCollections->where('loanIdFk',$loanId)->sum('amount');
                        $closingDuePrincipal = $shedules->where('loanIdFk',$loanId)->sum('principalAmount') - $allCollections->where('loanIdFk',$loanId)->sum('principalAmount');
                        if ($closingDuePrincipal<1) {
                            $closingDue = 0;
                            $closingDuePrincipal = 0;
                        }
                        $tClosingDue += $closingDue;
                        $tClosingDuePrincipal += $closingDuePrincipal;
                    }

                   ///// end of new code to fetch regular, due, advance collection

                $regularRecoveryAmount = $tRegularCollection;
                $regularPrincipalRecoveryAmount = $tRegularCollectionPrincipal;
                $advanceAmount = $tAdvanceCollection;
                $principalAdvanceAmount = $tAdvanceCollectionPrincipal;
                $dueRecoveryAmount = $tDueCollection;
                $duePrincipalRecoveryAmount = $tDueCollectionPrincipal;

                $recoverableAmount = $tRecoverableAmount;
                $principalRecoverableAmount = $tPrincipalRecoverableAmount;

                /*$closingDueAmountWithServicesCharge = (float) $openingData['openingDueAmountWithServicesCharge'] + $newDueAmount;
                $closingDueAmount = (float) $openingData['openingDueAmount'] + $principalNewDueAmount;*/

                $closingDueAmountWithServicesCharge = $tClosingDue;
                $closingDueAmount = $tClosingDuePrincipal;
                if ($closingDueAmount<0) {
                    $closingDueAmount = 0;
                }
                if ($closingDueAmountWithServicesCharge<0) {
                    $closingDueAmountWithServicesCharge = 0;
                }

                /*$principalNewDueAmount = $principalRecoverableAmount - $regularPrincipalRecoveryAmount>=1 ? $principalRecoverableAmount - $regularPrincipalRecoveryAmount : 0;*/
                /*$newDueAmount = ($recoverableAmount - $regularRecoveryAmount)>=1 ? ($recoverableAmount - $regularRecoveryAmount) : 0;*/

                $noOfDueLoanee = $thisBranchLoans->whereIn('id',$dueLoanIds)->unique('memberIdFk')->count();
                $totalNoOfDueLoaneeOnlyOptionalProduct = $thisBranchLoans->whereIn('id',$dueLoanIds)->whereIn('productIdFk',$optionalLoanProductIds)->unique('memberIdFk')->count();

                $currentWatchfulLoanIds = array_intersect($watchfulLoanIds,$currentLoanIds);
                $currentSubstandarLoanIds = array_intersect($substandarLoanIds,$currentLoanIds);
                $currentDoubtfullLoanIds = array_intersect($doubtfullLoanIds,$currentLoanIds);
                $currentBadLoanIds = array_intersect($badLoanIds,$currentLoanIds);


                $watchFul       = $this->getOverDueInfo($currentWatchfulLoanIds,$loanProductId,$memberIds,$thisBranchLoans,$allCollections,$shedules);
                $substandard    = $this->getOverDueInfo($currentSubstandarLoanIds,$loanProductId,$memberIds,$thisBranchLoans,$allCollections,$shedules);
                $doubtfull      = $this->getOverDueInfo($currentDoubtfullLoanIds,$loanProductId,$memberIds,$thisBranchLoans,$allCollections,$shedules);
                $bad            = $this->getOverDueInfo($currentBadLoanIds,$loanProductId,$memberIds,$thisBranchLoans,$allCollections,$shedules);

                $outstandingWithMoreThan2DueInstallments = $watchFul['outstandingWithMoreThan2DueInstallments'] + $substandard['outstandingWithMoreThan2DueInstallments'] + $doubtfull['outstandingWithMoreThan2DueInstallments'] + $bad['outstandingWithMoreThan2DueInstallments'];

                $outstandingWithMoreThan2DueInstallmentsServicesCharge = $watchFul['outstandingWithMoreThan2DueInstallmentsServicesCharge'] + $substandard['outstandingWithMoreThan2DueInstallmentsServicesCharge'] + $doubtfull['outstandingWithMoreThan2DueInstallmentsServicesCharge'] + $bad['outstandingWithMoreThan2DueInstallmentsServicesCharge'];
                if ($genderTypeId==1) {
                    $overDueMemberIds = $overDueMalesMemberIds;
                }
                else{
                    $overDueMemberIds = $overDueFemaleMemberIds;
                }

                $savingsDeposit = $savingsDepositsOfExpiredLoanMembers->where('primaryProductIdFk',$loanProductId)->whereIn('memberIdFk',$overDueMemberIds)->sum('amount');

                $savingsWithdraw = $savingsWIthdrawsOfExpiredLoanMembers->where('primaryProductIdFk',$loanProductId)->whereIn('memberIdFk',$overDueMemberIds)->sum('amount');

                $recoveryAmount = $dueRecoveryAmount + $regularRecoveryAmount + $advanceAmount;
                $principalRecoveryAmount = $duePrincipalRecoveryAmount + $regularPrincipalRecoveryAmount + $principalAdvanceAmount;



                $savingBalanceOfOverdueLoanee = $savingsDeposit - $savingsWithdraw;
                ////////////   End Collecting the information                  
                    
                    /*$closingOutstandingAmount = ((float) $openingData['openingOutstandingAmount'] + (float) $disbursedAmount) - (float) $principalRecoveryAmount;
*/
                    $closingOutstandingAmount = (float) $openingData['openingOutstandingAmount'] + $disbursedAmount - $regularPrincipalRecoveryAmount - $principalAdvanceAmount - $duePrincipalRecoveryAmount;

                    /*$closingOutstandingAmountWithServicesCharge = (float) $openingData['openingOutstandingAmountWithServicesCharge'] + $repayAmount - $recoveryAmount - $loanRebateAmount;*/
                    $closingOutstandingAmountWithServicesCharge = (float) $openingData['openingOutstandingAmountWithServicesCharge'] + $repayAmount - $regularRecoveryAmount - $dueRecoveryAmount - $advanceAmount;

                    if ($newDueAmount<1) {
                        $newDueAmount = 0;
                    }
                    if ($principalNewDueAmount<1) {
                        $principalNewDueAmount = 0;
                    }


                    $mEloanInfo = new MfnMonthEndProcessLoan;
                    $mEloanInfo->date                                                   = Carbon::parse($this->monthEndDate);
                    $mEloanInfo->branchIdFk                                             = $this->targetBranchId;
                    $mEloanInfo->productIdFk                                            = $loanProductId;
                    $mEloanInfo->genderTypeId                                           = $genderTypeId;
                    $mEloanInfo->openingBorrowerNo                                      = (int) $openingData['openingBorrowerNo'];
                    $mEloanInfo->openingOutstandingAmount                               = (float) $openingData['openingOutstandingAmount'];
                    $mEloanInfo->openingOutstandingAmountWithServicesCharge             = (float) $openingData['openingOutstandingAmountWithServicesCharge'];
                    $mEloanInfo->openingDisbursedAmount                                 = (float) $openingData['openingDisbursedAmount'];
                    $mEloanInfo->borrowerNo                                             = $borrowerNo;
                    // $mEloanInfo->borrowerNo_bt                                          = 
                    $mEloanInfo->disbursedAmount                                        = $disbursedAmount;
                    $mEloanInfo->repayAmount                                            = $repayAmount;
                    // $mEloanInfo->disbursedAmount_bt                                     = 
                    $mEloanInfo->principalRecoveryAmount                                = $principalRecoveryAmount;
                    // $mEloanInfo->principalRecoveryAmount_bt                             = 
                    $mEloanInfo->recoveryAmount                                         = $recoveryAmount;
                    // $mEloanInfo->recoveryAmount_bt                                      = 
                    $mEloanInfo->fullyPaidBorrowerNo                                    = $fullyPaidBorrowerNo;
                    // $mEloanInfo->fullyPaid_borrowerNo_bt                                = 
                    $mEloanInfo->closingBorrowerNo                                      = $closingBorrowerNo;
                    $mEloanInfo->closingOutstandingAmount                               = $closingOutstandingAmount;
                    $mEloanInfo->closingOutstandingAmountWithServicesCharge             = $closingOutstandingAmountWithServicesCharge;
                    $mEloanInfo->closingDisbursedAmount                                 = $closingDisbursedAmount;

                    $mEloanInfo->openingDueAmount                                       = (float) $openingData['openingDueAmount'];
                    $mEloanInfo->openingDueAmountWithServicesCharge                     = (float) $openingData['openingDueAmountWithServicesCharge'];
                    $mEloanInfo->principalRecoverableAmount                             = $principalRecoverableAmount;
                    $mEloanInfo->recoverableAmount                                      = $recoverableAmount;
                    $mEloanInfo->principalRegularAmount                                 = $regularPrincipalRecoveryAmount;
                    $mEloanInfo->regularAmount                                          = $regularRecoveryAmount;
                    $mEloanInfo->principalAdvanceAmount                                 = $principalAdvanceAmount;
                    $mEloanInfo->advanceAmount                                          = $advanceAmount;
                    $mEloanInfo->principalDueAmount                                     = $duePrincipalRecoveryAmount;
                    $mEloanInfo->dueAmount                                              = $dueRecoveryAmount;
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
                    $mEloanInfo->createdAt                                              = Carbon::today();
                    $mEloanInfo->updatedAt                                              = Carbon::now();

                    /*new coloums*/
                    $mEloanInfo->watchfulOutstanding                                    = $watchFul['outStanding'];
                    $mEloanInfo->watchfulOutstandingWithServicesCharge                  = $watchFul['outStandingWithServiceCharge'];
                    $mEloanInfo->watchfulOverdue                                        = $watchFul['overdue'];
                    $mEloanInfo->watchfulOverdueWithServicesCharge                      = $watchFul['overdueWithServicesCharge'];
                    $mEloanInfo->substandardOutstanding                                 = $substandard['outStanding'];
                    $mEloanInfo->substandardOutstandingWithServicesCharge               = $substandard['outStandingWithServiceCharge'];
                    $mEloanInfo->substandardOverdue                                     = $substandard['overdue'];
                    $mEloanInfo->substandardOverdueWithServicesCharge                   = $substandard['overdueWithServicesCharge'];
                    $mEloanInfo->doubtfullOutstanding                                   = $doubtfull['outStanding'];
                    $mEloanInfo->doubtfullOutstandingWithServicesCharge                 = $doubtfull['outStandingWithServiceCharge'];
                    $mEloanInfo->doubtfullOverdue                                       = $doubtfull['overdue'];
                    $mEloanInfo->doubtfullOverdueWithServicesCharge                     = $doubtfull['overdueWithServicesCharge'];
                    $mEloanInfo->badOutstanding                                         = $bad['outStanding'];
                    $mEloanInfo->badOutstandingWithServicesCharge                       = $bad['outStandingWithServiceCharge'];
                    $mEloanInfo->badOverdue                                             = $bad['overdue'];
                    $mEloanInfo->badOverdueWithServicesCharge                           = $bad['overdueWithServicesCharge'];
                    $mEloanInfo->outstandingWithMoreThan2DueInstallments                = $outstandingWithMoreThan2DueInstallments;
                    $mEloanInfo->outstandingWithMoreThan2DueInstallmentsServicesCharge  = $outstandingWithMoreThan2DueInstallmentsServicesCharge;
                    $mEloanInfo->savingBalanceOfOverdueLoanee                           = $savingBalanceOfOverdueLoanee;
                    /*end new coloums*/

                    $mEloanInfo->save();
                }                
            }            
        }

        /**
         * this function collect the information for the storeLoanInfo function
         * @param  [int] $loanProductId
         * @param  [int] $genderTypeId
         * @return [array]
         */
        public function storeLoanInfoHelper($loanProductId,$genderTypeId,$monthEndNo){

            if ($monthEndNo>=1) {
                $lastMonthLastDate = Carbon::parse($this->monthFirstDate)->subDay()->format('Y-m-d');
                $lastMonthEndInfo = DB::table('mfn_month_end_process_loans')
                                        ->where('date',$lastMonthLastDate)
                                        ->where('branchIdFk',$this->targetBranchId)
                                        ->where('productIdFk',$loanProductId)
                                        ->where('genderTypeId',$genderTypeId)
                                        ->first();
                if (count($lastMonthEndInfo)>0) {
                    // openingBorrowerNo
                    $openingBorrowerNo = (int) $lastMonthEndInfo->closingBorrowerNo;

                    // openingOutstandingAmount
                    $openingOutstandingAmount = (float) $lastMonthEndInfo->closingOutstandingAmount;

                    // openingOutstandingAmountWithServicesCharge
                    $openingOutstandingAmountWithServicesCharge = (float) $lastMonthEndInfo->closingOutstandingAmountWithServicesCharge;

                    // openingDisbursedAmount
                    $openingDisbursedAmount = (float) $lastMonthEndInfo->closingDisbursedAmount;

                    // openingDueAmount
                    $openingDueAmount = (float) $lastMonthEndInfo->closingDueAmount;

                    // openingDueAmountWithServicesCharge
                    $openingDueAmountWithServicesCharge = (float) $lastMonthEndInfo->closingDueAmountWithServicesCharge;
                }
                else{
                    $openingBorrowerNo = $openingOutstandingAmount = $openingOutstandingAmountWithServicesCharge = $openingDisbursedAmount = $openingDueAmount = $openingDueAmountWithServicesCharge = 0;
                }
            }

            else{
                //// for the first month end
                // openingBorrowerNo
                $openingBorrowerNo = (int) DB::table('mfn_branch_opening_informations')
                                                ->where('loanProductIdFk',$loanProductId)
                                                ->where('genderTypeId',$genderTypeId)
                                                ->where('branchIdFk',$this->targetBranchId)
                                                ->value('totalFullyPaidBorrowerNo');

                $openingBalanceInfo = DB::table('mfn_branch_opening_balances')
                                            ->where('loanProductIdFk',$loanProductId)
                                            ->where('genderTypeId',$genderTypeId)    
                                            ->where('branchIdFk',$this->targetBranchId)
                                            ->where('isGrandTotal',0)
                                            ->first();

                $openingBalanceInfoWithSerCharge = DB::table('mfn_branch_opening_balances')
                                                    ->where('loanProductIdFk',$loanProductId)
                                                    ->where('genderTypeId',$genderTypeId)
                                                    ->where('branchIdFk',$this->targetBranchId)
                                                    ->where('isGrandTotal',1)
                                                    ->first();

                if (count($openingBalanceInfo)>0) {
                    // openingOutstandingAmount
                    $openingOutstandingAmount = (float) $openingBalanceInfo->totalDisburseAmount - (float) $openingBalanceInfo->totalCollectedAmount;

                    // openingDisbursedAmount
                    $openingDisbursedAmount = (float) $openingBalanceInfo->totalDisburseAmount;

                    // openingDueAmount
                    $openingDueAmount = (float) $openingBalanceInfo->totalDueAmount;
                }
                else{
                    $openingOutstandingAmount = 0;
                    $openingDisbursedAmount = 0;
                    $openingDueAmount = 0;
                }

                if (count($openingBalanceInfoWithSerCharge)>0) {
                    // openingOutstandingAmountWithServicesCharge
                    $openingOutstandingAmountWithServicesCharge = (float) $openingBalanceInfoWithSerCharge->totalDisburseAmount - (float) $openingBalanceInfoWithSerCharge->totalCollectedAmount;
                    // openingDueAmountWithServicesCharge
                    $openingDueAmountWithServicesCharge = (float) $openingBalanceInfoWithSerCharge->totalDueAmount;
                }
                else{
                    $openingOutstandingAmountWithServicesCharge = 0;
                    $openingDueAmountWithServicesCharge = 0;
                }                
            }

            $data = array(
                'openingBorrowerNo'                             => $openingBorrowerNo,
                'openingOutstandingAmount'                      => $openingOutstandingAmount,
                'openingOutstandingAmountWithServicesCharge'    => $openingOutstandingAmountWithServicesCharge,
                'openingDisbursedAmount'                        => $openingDisbursedAmount,
                'openingDueAmount'                              => $openingDueAmount,
                'openingDueAmountWithServicesCharge'            => $openingDueAmountWithServicesCharge,
            );
            return $data;
        }

        public function getOverDueInfo($loanIds,$loanProductId,$memberIds,$loans,$allCollections,$shedules){

            $overdue = 0;
            $overdueWithServicesCharge = 0;

            $moreThan2DueInstallmentsLoanIds = array();

            foreach ($loanIds as $loanId) {

                /*$overdue = $overdue + (float) $shedules->where('loanIdFk',$loanId)->where('isCompleted',0)->sortBy('scheduleDate')->max('principalAmount');*/
                $overdue = $overdue + $shedules->where('loanIdFk',$loanId)->sum('principalAmount') - $allCollections->where('loanIdFk',$loanId)->sum('principalAmount');

                /*$overdueWithServicesCharge = $overdueWithServicesCharge + (float) $shedules->where('loanIdFk',$loanId)->where('isCompleted',0)->sortBy('scheduleDate')->max('installmentAmount');*/

                $overdueWithServicesCharge = $overdueWithServicesCharge + $shedules->where('loanIdFk',$loanId)->sum('installmentAmount') - $allCollections->where('loanIdFk',$loanId)->sum('amount');

                if ($shedules->where('loanIdFk',$loanId)->where('isCompleted',0)->count()>2) {
                    array_push($moreThan2DueInstallmentsLoanIds,$loanId);
                }
            }

            $outStanding = $loans->whereIn('id',$loanIds)->where('productIdFk',$loanProductId)->whereIn('memberIdFk',$memberIds)->sum('loanAmount') - $allCollections->where('productIdFk',$loanProductId)->whereIn('memberIdFk',$memberIds)->whereIn('loanIdFk',$loanIds)->sum('principalAmount');

            $outStandingWithServiceCharge = $loans->whereIn('id',$loanIds)->where('productIdFk',$loanProductId)->whereIn('memberIdFk',$memberIds)->sum('totalRepayAmount') - $allCollections->where('productIdFk',$loanProductId)->whereIn('memberIdFk',$memberIds)->whereIn('loanIdFk',$loanIds)->sum('amount');

            $outstandingWithMoreThan2DueInstallments = $loans->whereIn('id',$moreThan2DueInstallmentsLoanIds)->where('productIdFk',$loanProductId)->whereIn('memberIdFk',$memberIds)->sum('loanAmount') - $allCollections->where('productIdFk',$loanProductId)->whereIn('memberIdFk',$memberIds)->whereIn('loanIdFk',$moreThan2DueInstallmentsLoanIds)->sum('principalAmount');

            $outstandingWithMoreThan2DueInstallmentsServicesCharge = $loans->whereIn('id',$moreThan2DueInstallmentsLoanIds)->where('productIdFk',$loanProductId)->whereIn('memberIdFk',$memberIds)->sum('totalRepayAmount') - $allCollections->where('productIdFk',$loanProductId)->whereIn('memberIdFk',$memberIds)->whereIn('loanIdFk',$moreThan2DueInstallmentsLoanIds)->sum('amount');



            return array(
                'outStanding'                       => (float) $outStanding,
                'outStandingWithServiceCharge'      => (float) $outStandingWithServiceCharge,
                'overdue'                           => (float) $overdue,
                'overdueWithServicesCharge'         => (float) $overdueWithServicesCharge,
                'outstandingWithMoreThan2DueInstallments'         => (float) $outstandingWithMoreThan2DueInstallments,
                'outstandingWithMoreThan2DueInstallmentsServicesCharge'         => (float) $outstandingWithMoreThan2DueInstallmentsServicesCharge,
            );
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

        public function storeSavingsProbationInterest(){

            DB::table('mfn_savings_probation_interest')->where('branchIdFk',$this->targetBranchId)->where('date',$this->monthEndDate)->delete();

            $closedMemberIds = DB::table('mfn_member_closing')
                                    ->where('softDel',0)
                                    ->where('branchIdFk',$this->targetBranchId)
                                    ->where('closingDate','<=',$this->monthEndDate)
                                    ->pluck('memberIdFk')
                                    ->toArray();

            $members = DB::table('mfn_member_information')
                                ->where('softDel',0)
                                ->whereNotIn('id',$closedMemberIds)
                                ->where('admissionDate','<=',$this->monthEndDate)
                                ->where('branchId',$this->targetBranchId)
                                ->select('id','primaryProductId','samityId')
                                ->get();

            $closedSavingsAccountIds = DB::table('mfn_savings_closing')
                                            ->where('softDel',0)
                                            ->where('closingDate','<=',$this->monthEndDate)
                                            ->pluck('accountIdFk')
                                            ->toArray();

            $savingsAccounts = DB::table('mfn_savings_account')
                                    ->where('softDel',0)
                                    ->whereNotIn('id',$closedSavingsAccountIds)
                                    ->where('accountOpeningDate','<=',$this->monthEndDate)
                                    ->whereIn('memberIdFk',$members->pluck('id'))
                                    ->select('id','memberIdFk','savingsProductIdFk','savingsInterestRate')
                                    ->get();

            $savinngsDeposits = DB::table('mfn_savings_deposit')
                                    ->where('softDel',0)
                                    ->whereIn('accountIdFk',$savingsAccounts->pluck('id'))
                                    ->where('depositDate','<=',$this->monthEndDate)
                                    ->select('accountIdFk','productIdFk','memberIdFk','amount','depositDate')
                                    ->get();

            $savinngsWithdraws = DB::table('mfn_savings_withdraw')
                                    ->where('softDel',0)
                                    ->whereIn('accountIdFk',$savingsAccounts->pluck('id'))
                                    ->where('withdrawDate','<=',$this->monthEndDate)
                                    ->select('accountIdFk','productIdFk','memberIdFk','amount','withdrawDate')
                                    ->get();

            foreach ($members as $member) {
                $mSavAccs = $savingsAccounts->where('memberIdFk',$member->id);
                $productIds = $mSavAccs->unique('savingsProductIdFk')->pluck('savingsProductIdFk')->toArray();

                foreach ($productIds as $productId) {
                    $pSavAccs = $mSavAccs->where('savingsProductIdFk',$productId);

                    $pInterestAmount = 0;

                    foreach ($pSavAccs as $pSavAcc) {
                        $opDep = $savinngsDeposits->where('depositDate','<',$this->monthFirstDate)->where('accountIdFk',$pSavAcc->id)->sum('amount');
                        $opWithdraw = $savinngsWithdraws->where('withdrawDate','<',$this->monthFirstDate)->where('accountIdFk',$pSavAcc->id)->sum('amount');
                        $opBalance = $opDep - $opWithdraw;

                        $closingDep = $savinngsDeposits->where('accountIdFk',$pSavAcc->id)->sum('amount');
                        $closingWithdraw = $savinngsWithdraws->where('accountIdFk',$pSavAcc->id)->sum('amount');
                        $closingBalance = $closingDep - $closingWithdraw;

                        $avgBalance = ($opBalance + $closingBalance)/2;

                        $interest = ($avgBalance * $pSavAcc->savingsInterestRate)/(100*12);

                        $pInterestAmount += $interest;
                    }

                    DB::table('mfn_savings_probation_interest')->insert([
                        'memberIdFk'            => $member->id,
                        'productIdFk'           => $productId,
                        'primaryProductIdFk'    => $member->primaryProductId,
                        'samityIdFk'            => $member->samityId,
                        'branchIdFk'            => $this->targetBranchId,
                        'date'                  => $this->monthEndDate,
                        'interestAmount'        => $pInterestAmount
                    ]);
                }                
            }
        }

    }