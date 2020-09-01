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
    private $dbMembers;
    private $dbDeposit;
    private $dbWithdraw;
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

        $this->dbMembers = DB::table('mfn_member_information')
        ->where('softDel',0)
        ->where('branchId',$targetBranchId)
        ->where('admissionDate','<=',$this->monthEndDate)
        ->select('id','gender','samityId','primaryProductId','admissionDate','closingDate')
        ->get();


        // According to the transfer history, consider the member's primary product
        $primaryProductTransfers = DB::table('mfn_loan_primary_product_transfer')
        ->where('softDel',0)
        ->where('branchIdFk',$this->targetBranchId)
        ->where('transferDate','>',$this->monthEndDate)
        ->get();

        $primaryProductTransfers = $primaryProductTransfers->sortBy('transferDate')->unique('memberIdFk');

        foreach ($primaryProductTransfers as $key => $primaryProductTransfer) {
            if ($this->dbMembers->where('id',$primaryProductTransfer->memberIdFk)->first()!=null) {
                $this->dbMembers->where('id',$primaryProductTransfer->memberIdFk)->first()->primaryProductId = $primaryProductTransfer->oldPrimaryProductFk;
            }                
        }

        // According to the transfer history, consider the member's samity
        $samityTransfers = DB::table('mfn_member_samity_transfer')
        ->where('softDel',0)
        ->where('branchIdFk',$this->targetBranchId)
        ->where('transferDate','>',$this->monthEndDate)
        ->get();

        $samityTransfers = $samityTransfers->sortBy('transferDate')->unique('memberIdFk');

        foreach ($samityTransfers as $key => $samityTransfer) {
            if ($this->dbMembers->where('id',$samityTransfer->memberIdFk)->first()!=null) {
                $this->dbMembers->where('id',$samityTransfer->memberIdFk)->first()->samityId = $samityTransfer->previousSamityIdFk;
            }                
        }


        $this->dbDeposit = DB::table('mfn_savings_deposit')
        ->where('softDel',0)
        ->where('branchIdFk',$targetBranchId)
        ->where('depositDate','<=',$monthEndDate)
        ->select('memberIdFk','depositDate','isFromAutoProcess','primaryProductIdFk','accountIdFk','productIdFk','amount')
        ->get();

        $savOpeningBalances = DB::table('mfn_opening_savings_account_info AS savOpBal')
        ->join('mfn_savings_account AS savAcc', 'savAcc.id', 'savOpBal.savingsAccIdFk')
        ->where('savAcc.softDel',0)
        ->where('savAcc.branchIdFk',$targetBranchId)
        ->select('savOpBal.memberIdFk','savOpBal.primaryProductIdFk','savOpBal.savingsAccIdFk','savAcc.savingsProductIdFk','savOpBal.openingBalance')
        ->get();

        foreach ($savOpeningBalances as $savOpeningBalance) {
            $this->dbDeposit->push([
                'memberIdFk'            => $savOpeningBalance->memberIdFk,
                'depositDate'           => '2018-10-31',
                'isFromAutoProcess'     => 0,
                'primaryProductIdFk'    => $savOpeningBalance->primaryProductIdFk,
                'accountIdFk'            => $savOpeningBalance->savingsAccIdFk,
                'productIdFk'            => $savOpeningBalance->savingsProductIdFk,
                'amount'            => $savOpeningBalance->openingBalance
            ]);
        }

        $this->dbWithdraw = DB::table('mfn_savings_withdraw')
        ->where('softDel',0)
        ->where('branchIdFk',$targetBranchId)
        ->where('withdrawDate','<=',$monthEndDate)
        ->select('memberIdFk','withdrawDate','primaryProductIdFk','accountIdFk','productIdFk','amount')
        ->get();
    }

    public function saveData(){        

        if ($this->isCustomCall==1) {
            // $this->storeTotalMemberInfo();
            // $this->storeMemberInfo();
            // $this->storeMemberInfoCategoryWise();
            // $this->storeSavingsInfo();
            $this->storeLoanInfo();
            // $this->storeStaffInfo();
        }

        else{
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

            // if all the information is zero than do not need to save it.
            $isEmptyInfo = 0;
            if (max($femaleData)==0 && max($maleData)==0 && min($femaleData)==0 && min($maleData)==0) {
                $isEmptyInfo = 1;
            }

            if ($existingId>0) {
                $monthEndTotalMembers = MfnMonthEndProcessTotalMembers::find($existingId);
                if ($isEmptyInfo==1) {
                    $monthEndTotalMembers->delete();
                    continue;
                }
            }
            else{
                if ($isEmptyInfo==1) {
                    continue;
                }
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
        /*$genderWiseMemberIds = DB::table('mfn_member_information')
        ->where('softDel',0)
        ->where('gender',$genderTypeId) 
        ->pluck('id')
        ->toArray();*/

        $genderWiseMemberIds = $this->dbMembers
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

        /*$members = DB::table('mfn_member_information')
        ->where('softDel',0)
        ->where('branchId',$this->targetBranchId)
        ->where('gender',$genderTypeId)
        ->whereIn('samityId',$samityIds)
        ->select('samityId','primaryProductId','admissionDate')
        ->get();*/

        $members = $this->dbMembers
        ->where('gender',$genderTypeId)
        ->whereIn('samityId',$samityIds);

        $newSamityNo = 0;
        $inactiveSamityNo = 0;

        foreach($samityIds as $samityId){
                // member having product id of the funding org product ids and newly assing on this month
            $isPreviouslyAssinged = $members->where('samityId',$samityId)->whereIn('primaryProductId',$primaryProductIds)->where('admissionDate','<',$this->monthFirstDate)->count();
            if(!$isPreviouslyAssinged>0){
                $newAssingedNum = $members->where('samityId',$samityId)->whereIn('primaryProductId',$primaryProductIds)->where('admissionDate','>=',$this->monthFirstDate)->where('admissionDate','<=',$this->monthEndDate)->count();
                if($newAssingedNum>0){
                    $newSamityNo++;
                }
            }

            // count inactive samity
            $samityTotalMember = $members->count();
            $samityClosingMember = $members->where('closingDate','!=','0000-00-00')->where('closingDate','!=','')->where('closingDate','<=',$this->monthEndDate)->count();
            if($samityClosingMember>=$samityTotalMember){
                $inactiveSamityNo++;
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
        // $memberCancellationNo = 0;

        // Closing Samity No
        $closingSamityNo = $openingSamityNo + $newSamityNo - $cancelSamityNo - $inactiveSamityNo;
        $closingSamityNo = $closingSamityNo <1 ? 0 : $closingSamityNo;



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
        /*$newMemberAdmissionNo = DB::table('mfn_member_information')
        ->where('softDel',0)
        ->where('branchId',$this->targetBranchId)
        ->whereIn('primaryProductId',$primaryProductIds)
        ->where('admissionDate','>=',$this->monthFirstDate)
        ->where('admissionDate','<=',$this->monthEndDate)
        ->where('gender',$genderTypeId)
        ->count();*/

        $newMemberAdmissionNo = $this->dbMembers
        ->whereIn('primaryProductId',$primaryProductIds)
        ->where('admissionDate','>=',$this->monthFirstDate)
        ->where('admissionDate','<=',$this->monthEndDate)
        ->where('gender',$genderTypeId)
        ->count();

        $memberCancellationNo = $this->dbMembers
        ->whereIn('primaryProductId',$primaryProductIds)
        ->where('closingDate','>=',$this->monthFirstDate)
        ->where('closingDate','<=',$this->monthEndDate)
        ->where('gender',$genderTypeId)
        ->count();

        $noOfMemberProductIn = DB::table('mfn_loan_primary_product_transfer')
        ->where('softDel',0)
        ->where('branchIdFk',$this->targetBranchId)
        ->whereIn('newPrimaryProductFk',$primaryProductIds)
        ->whereIn('memberIdFk',$genderWiseMemberIds)
        ->where('transferDate','>=',$this->monthFirstDate)
        ->where('transferDate','<=',$this->monthEndDate)
        ->where('oldDateTo','=','')
        ->count();


        $noOfMemberProductOut = DB::table('mfn_loan_primary_product_transfer')
        ->where('softDel',0)
        ->where('branchIdFk',$this->targetBranchId)
        ->whereIn('oldPrimaryProductFk',$primaryProductIds)
        ->whereIn('memberIdFk',$genderWiseMemberIds)
        ->where('transferDate','>=',$this->monthFirstDate)
        ->where('transferDate','<=',$this->monthEndDate)
        ->where('oldDateTo','=','')
        ->count();

        $newMemberAdmissionNoBt = $noOfMemberProductIn;
        $memberCancellationNoBt = $noOfMemberProductOut; 

        $closingMember = $openingMember + $newMemberAdmissionNo - $memberCancellationNo + $newMemberAdmissionNoBt - $memberCancellationNoBt;

        // dd($closingMember , $openingMember , $newMemberAdmissionNo , $memberCancellationNo);



       /* $noOfMemberProductOut = DB::table('mfn_member_samity_transfer')
        ->where('branchIdFk',$this->targetBranchId)
        ->whereColumn('newPrimaryProductIdFk','!=','newPrimaryProductIdFk')
        ->whereIn('previousPrimaryProductIdFk',$primaryProductIds)
        ->whereIn('memberIdFk',$genderWiseMemberIds)
        ->where('transferDate','>=',$this->monthFirstDate)
        ->where('transferDate','<=',$this->monthEndDate)
        ->count();*/



       /* $noOfMemberProductIn = DB::table('mfn_member_samity_transfer')
        ->where('branchIdFk',$this->targetBranchId)
        ->whereColumn('newPrimaryProductIdFk','!=','newPrimaryProductIdFk')
        ->whereIn('newPrimaryProductIdFk',$primaryProductIds)
        ->whereIn('memberIdFk',$genderWiseMemberIds)
        ->where('transferDate','>=',$this->monthFirstDate)
        ->where('transferDate','<=',$this->monthEndDate)
        ->count();*/



        // Number of inactive members
       /* $memberIds = DB::table('mfn_member_information')
        ->where('branchId',$this->targetBranchId)
        ->whereIn('primaryProductId',$primaryProductIds)
        ->whereIn('id',$genderWiseMemberIds)
        ->pluck('id')
        ->toArray();*/

        $memberIds = $this->dbMembers
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

        /*$memberAdmissionDate = DB::table('mfn_member_information')
        ->where('id',$memberId)
        ->value('admissionDate');*/

        $memberAdmissionDate = $this->dbMembers
        ->where('id',$memberId)
        ->max('admissionDate');

        if($monthEndCarbonDate->diffInDays(Carbon::parse($memberAdmissionDate))<=$daysLength){
            return false;
        }

        /*$lastDepositDate = DB::table('mfn_savings_deposit')
        ->where('memberIdFk',$memberId)
        ->where('depositDate','<=',$this->monthEndDate)
        ->where('softDel',0)
        ->max('depositDate');*/

        $lastDepositDate = $this->dbDeposit
        ->where('memberIdFk',$memberId)
        ->max('depositDate');

        if($monthEndCarbonDate->diffInDays(Carbon::parse($lastDepositDate))<=$daysLength){
            return false;
        }

        /*$lastWithdrawDate = DB::table('mfn_savings_withdraw')
        ->where('memberIdFk',$memberId)
        ->where('withdrawDate','<=',$this->monthEndDate)
        ->where('softDel',0)
        ->max('withdrawDate');*/

        $lastWithdrawDate = $this->dbWithdraw
        ->where('memberIdFk',$memberId)
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

        /*$members = DB::table('mfn_member_information')
        ->where('softDel',0)
        ->whereIn('primaryProductId',$loanProductIds)
        ->where('branchId',$this->targetBranchId)
        ->select('id','primaryProductId','gender')
        ->get();*/

        $members = $this->dbMembers
        ->whereIn('primaryProductId',$loanProductIds);

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

                /*$members = DB::table('mfn_member_information')
                ->where('softDel',0)
                ->where('branchId',$this->targetBranchId)
                ->where('gender',$genderTypeId)
                ->whereIn('samityId',$samityIds)
                ->select('id','samityId','primaryProductId','admissionDate')
                ->get();*/

                $members = $this->dbMembers
                ->where('gender',$genderTypeId)
                ->whereIn('samityId',$samityIds);

                $newSamityNo = 0;
                $inactiveSamityNo = 0;

                foreach($samityIds as $key => $samityId){
                    // member having product id of the funding org product ids and newly assing on this month
                    $isPreviouslyAssinged = $members->where('samityId',$samityId)->where('primaryProductId',$loanProduct->id)->where('admissionDate','<',$this->monthFirstDate)->count();

                    if(!$isPreviouslyAssinged>0){
                        $newAssingedNum = $members->where('samityId',$samityId)->where('primaryProductId',$loanProduct->id)->where('admissionDate','>=',$this->monthFirstDate)->where('admissionDate','<=',$this->monthEndDate)->count();

                        if($newAssingedNum>0){
                            $newSamityNo++;
                        }
                    }

                    // count inactive samity
                    $samityTotalMember = $members->count();
                    $samityClosingMember = $members->where('closingDate','!=','0000-00-00')->where('closingDate','!=','')->where('closingDate','<=',$this->monthEndDate)->count();
                    if($samityClosingMember>=$samityTotalMember){
                                // dd($samityTotalMember,$samityClosingMember,$samityId);
                        $inactiveSamityNo++;
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

                /*$newMemberAdmissionNo = DB::table('mfn_member_information as t1')
                ->where('t1.softDel',0)
                ->where('t1.gender',$genderTypeId)
                ->where('t1.branchId',$this->targetBranchId)
                ->where('t1.primaryProductId',$loanProduct->id)
                ->where('t1.admissionDate','>=',$this->monthFirstDate)
                ->where('t1.admissionDate','<=',$this->monthEndDate)
                ->count();*/

                $newMemberAdmissionNo = $this->dbMembers
                ->where('gender',$genderTypeId)
                ->where('primaryProductId',$loanProduct->id)
                ->where('admissionDate','>=',$this->monthFirstDate)
                ->where('admissionDate','<=',$this->monthEndDate)
                ->count();

                $newMemberAdmissionNoBpt = DB::table('mfn_loan_primary_product_transfer')
                ->where('softDel',0)
                ->where('branchIdFk',$this->targetBranchId)
                ->whereIn('memberIdFk',$members->pluck('id'))
                ->where('newPrimaryProductFk',$loanProduct->id)
                ->where('transferDate','>=',$this->monthFirstDate)
                ->where('transferDate','<=',$this->monthEndDate)
                ->where('oldDateTo','=','')
                ->count();

                $memberCancellationNoBpt = DB::table('mfn_loan_primary_product_transfer')
                ->where('softDel',0)
                ->where('branchIdFk',$this->targetBranchId)
                ->whereIn('memberIdFk',$members->pluck('id'))
                ->where('oldPrimaryProductFk',$loanProduct->id)
                ->where('transferDate','>=',$this->monthFirstDate)
                ->where('transferDate','<=',$this->monthEndDate)
                ->where('oldDateTo','=','')
                ->count();

                /*if(Auth::user()->id==1){
                    echo '$memberCancellationNoBpt: '.$memberCancellationNoBpt.'<br>';
                }*/

                /*$memberCancellationNo = DB::table('mfn_member_closing as mcl')
                ->join('mfn_member_information as mi','mcl.memberIdFk','mi.id')
                ->where('mcl.softDel',0)
                ->where('mi.gender',$genderTypeId)
                ->where('mcl.branchIdFk',$this->targetBranchId)
                ->where('mi.primaryProductId',$loanProduct->id)
                ->where('mcl.closingDate','>=',$this->monthFirstDate)
                ->where('mcl.closingDate','<=',$this->monthEndDate)
                ->count();*/

                $memberCancellationNo = $this->dbMembers
                ->where('primaryProductId',$loanProduct->id)
                ->where('gender',$genderTypeId)
                ->where('closingDate','>=',$this->monthFirstDate)
                ->where('closingDate','<=',$this->monthEndDate)
                ->count();



                // $memberCancellationNo = $memberCancellationNo + $memberCancellationNoBpt;

                /*$depositsFromAutoProcess = DB::table('mfn_savings_deposit')
                ->where('softDel',0)
                ->whereIn('memberIdFk',$thiProductMembers->pluck('id'))
                ->where('depositDate','>=',$this->monthFirstDate)
                ->where('depositDate','<=',$this->monthEndDate)
                ->where('isFromAutoProcess',1)
                ->get();*/

                $depositsFromAutoProcess = $this->dbDeposit
                ->whereIn('memberIdFk',$thiProductMembers->pluck('id'))
                ->where('depositDate','>=',$this->monthFirstDate)
                ->where('isFromAutoProcess',1);

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


                // if all the information is zero than do not need to save it.
                $isEmptyInfo = 0;

                if (max($openingMember,$openingSamityNo,$newSamityNo,$newMemberAdmissionNo,$newMemberAdmissionNoBpt,$memberCancellationNo,$memberCancellationNoBpt,$avgSavingsDepositor,$inactiveSamityNo)==0 && min($openingMember,$openingSamityNo,$newSamityNo,$newMemberAdmissionNo,$newMemberAdmissionNoBpt,$memberCancellationNo,$memberCancellationNoBpt,$avgSavingsDepositor,$inactiveSamityNo)==0) {
                    $isEmptyInfo = 1;
                }              

                $mfnMonthEndProcessMembersId = (int) DB::table('mfn_month_end_process_members')->where('date',$this->monthEndDate)->where('branchIdFk',$this->targetBranchId)->where('fundingOrgIdFk',$loanProduct->fundingOrganizationId)->where('loanProductIdFk',$loanProduct->id)->where('genderTypeId',$genderTypeId)->where('loanProductIdFk','>',0)->value('id');
                if ($mfnMonthEndProcessMembersId>0) {
                    $monEndMember = MfnMonthEndProcessMembers::find($mfnMonthEndProcessMembersId);
                    if ($isEmptyInfo==1) {
                        $monEndMember->delete();
                        continue;
                    }
                }
                else{
                    if ($isEmptyInfo==1) {
                        continue;
                    }
                    $monEndMember = new MfnMonthEndProcessMembers;
                }

                // This data will be countetd later, for the initialization, it is declared as zero
                $cancelSamityNo = 0;
                // $closingMember = $openingMember + $newMemberAdmissionNo - $memberCancellationNo;
                $closingMember = $openingMember + $newMemberAdmissionNo - $memberCancellationNo + $newMemberAdmissionNoBpt - $memberCancellationNoBpt;

                $closingSamityNo = $openingSamityNo + $newSamityNo - $cancelSamityNo - $inactiveSamityNo;
                $closingSamityNo = $closingSamityNo <1 ? 0 : $closingSamityNo;

                /*if(Auth::user()->id==1){
                    echo '$loanProduct->fundingOrganizationId: '.$loanProduct->fundingOrganizationId.'<br>';
                    echo '$loanProduct->id: '.$loanProduct->id.'<br>';
                    echo '$newMemberAdmissionNoBpt: '.$newMemberAdmissionNoBpt.'<br>';
                    echo '$memberCancellationNoBpt: '.$memberCancellationNoBpt.'<br><br>';
                }*/

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
                $monEndMember->closingSamityNo          = $closingSamityNo;
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

        /*$members = DB::table('mfn_member_information')
        ->where('softDel',0)
        ->whereIn('primaryProductId',$loanProductIds)
        ->where('branchId',$this->targetBranchId)
        ->select('id','primaryProductId','gender')
        ->get();*/

        $members = $this->dbMembers
        ->whereIn('primaryProductId',$loanProductIds);

        // 1 = Male, 2 = Female
        $genderTypeIds = [1,2];

        $monthEndNo = DB::table('mfn_month_end')
        ->where('branchIdFk',$this->targetBranchId)
        ->where('date','<',$this->monthEndDate)
        ->count();

        $loanCategoryIds = $loanProducts->unique('productCategoryId')->pluck('productCategoryId')->toArray();

        foreach ($loanCategoryIds as $loanCategoryId) {

            /////
            $fundingOrganizationIds = $loanProducts->where('productCategoryId',$loanCategoryId)->unique('fundingOrganizationId')->pluck('fundingOrganizationId')->toArray();
            /////
            // data will save with respect to the gender of members, one for female and one for male

            foreach ($fundingOrganizationIds as $key => $fundingOrganizationId) {
                $currentLoanProductIds = $loanProducts->where('productCategoryId',$loanCategoryId)->where('fundingOrganizationId',$fundingOrganizationId)->pluck('id')->toArray();
                foreach ($genderTypeIds as $genderTypeId) {

                    $thiProductMembers = $members->whereIn('primaryProductId',$currentLoanProductIds)->where('gender',$genderTypeId);

                    if ($monthEndNo>=1) {
                        $lastMonthEnd = MfnMonthEndProcessMembers::where('branchIdFk',$this->targetBranchId)
                        ->where('loanProductCategoryIdFk',$loanCategoryId)
                        ->where('fundingOrgIdFk',$fundingOrganizationId)
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

                    /*$members = DB::table('mfn_member_information')
                    ->where('softDel',0)
                    ->where('branchId',$this->targetBranchId)
                    ->where('gender',$genderTypeId)
                    ->whereIn('samityId',$samityIds)
                    ->select('samityId','primaryProductId','admissionDate')
                    ->get();*/

                    $members = $this->dbMembers
                    ->where('gender',$genderTypeId)
                    ->whereIn('samityId',$samityIds);

                    $newSamityNo = 0;
                    $inactiveSamityNo = 0;

                    foreach($samityIds as $key => $samityId){
                        // member having product id of the funding org product ids and newly assing on this month
                        $isPreviouslyAssinged = $members->where('samityId',$samityId)->whereIn('primaryProductId',$currentLoanProductIds)->where('admissionDate','<',$this->monthFirstDate)->count();

                        if(!$isPreviouslyAssinged>0){
                            $newAssingedNum = $members->where('samityId',$samityId)->whereIn('primaryProductId',$currentLoanProductIds)->where('admissionDate','>=',$this->monthFirstDate)->where('admissionDate','<=',$this->monthEndDate)->count();

                            if($newAssingedNum>0){
                                $newSamityNo++;
                            }
                        }

                        // count inactive samity
                        $samityTotalMember = $members->count();
                        $samityClosingMember = $members->where('closingDate','!=','0000-00-00')->where('closingDate','!=','')->where('closingDate','<=',$this->monthEndDate)->count();
                        if($samityClosingMember>=$samityTotalMember){
                            $inactiveSamityNo++;
                        }
                    }
                    ///////////////////////////////

                    /*$newSamityNo = DB::table('mfn_samity')
                    ->where('status',1)
                    ->where('branchId',$this->targetBranchId)
                    ->where('openingDate','>=',$this->monthFirstDate)
                    ->where('openingDate','<=',$this->monthEndDate)
                    ->where('samityTypeId',$genderTypeId)
                    ->count();*/

                    /*$newMemberAdmissionNo = DB::table('mfn_member_information')
                    ->where('softDel',0)
                    ->where('gender',$genderTypeId)
                    ->where('branchId',$this->targetBranchId)
                    ->whereIn('primaryProductId',$currentLoanProductIds)
                    ->where('admissionDate','>=',$this->monthFirstDate)
                    ->where('admissionDate','<=',$this->monthEndDate)
                    ->count();*/

                    $newMemberAdmissionNo = $this->dbMembers
                    ->where('gender',$genderTypeId)
                    ->whereIn('primaryProductId',$currentLoanProductIds)
                    ->where('admissionDate','>=',$this->monthFirstDate)
                    ->where('admissionDate','<=',$this->monthEndDate)
                    ->count();

                    $newMemberAdmissionNoBpt = DB::table('mfn_loan_primary_product_transfer')
                    ->where('softDel',0)
                    ->where('branchIdFk',$this->targetBranchId)
                    ->whereIn('memberIdFk',$members->pluck('id'))
                    ->whereIn('newPrimaryProductFk',$currentLoanProductIds)
                    ->where('transferDate','>=',$this->monthFirstDate)
                    ->where('transferDate','<=',$this->monthEndDate)
                    ->where('oldDateTo','=','')
                    ->count();
                    
                    $memberCancellationNo = $this->dbMembers
                    ->where('gender',$genderTypeId)
                    ->whereIn('primaryProductId',$currentLoanProductIds)
                    ->where('closingDate','>=',$this->monthFirstDate)
                    ->where('closingDate','<=',$this->monthEndDate)
                    ->count();
                    
                    $memberCancellationNoArraySize = $this->dbMembers
                    ->where('gender',$genderTypeId)
                    ->whereIn('primaryProductId',$currentLoanProductIds)
                    ->where('closingDate','>=',$this->monthFirstDate)
                    ->where('closingDate','<=',$this->monthEndDate)
                    ->pluck('id')
                    ->toArray();
                    

                    $newMemberCancellationNoBpt = DB::table('mfn_loan_primary_product_transfer')
                    ->where('softDel',0)
                    ->where('branchIdFk',$this->targetBranchId)
                    ->whereIn('memberIdFk',$members->pluck('id'))
                    ->whereIn('oldPrimaryProductFk',$currentLoanProductIds)
                                                    // ->whereNotIn('newPrimaryProductFk',$currentLoanProductIds)
                    ->where('transferDate','>=',$this->monthFirstDate)
                    ->where('transferDate','<=',$this->monthEndDate)
                    ->where('oldDateTo','=','')
                    ->count();

                    /*$depositsFromAutoProcess = DB::table('mfn_savings_deposit')
                    ->where('softDel',0)
                    ->where('branchIdFk',$this->targetBranchId)
                    ->whereIn('memberIdFk',$thiProductMembers->pluck('id'))
                    ->where('depositDate','>=',$this->monthFirstDate)
                    ->where('depositDate','<=',$this->monthEndDate)
                    ->where('isFromAutoProcess',1)
                    ->get();*/

                    $depositsFromAutoProcess = $this->dbDeposit
                    ->whereIn('memberIdFk',$thiProductMembers->pluck('id'))
                    ->where('depositDate','>=',$this->monthFirstDate)
                    ->where('isFromAutoProcess',1);

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

                    // if all the information is zero than do not need to save it.
                    $isEmptyInfo = 0;

                    if (max($openingMember,$openingSamityNo,$newSamityNo,$newMemberAdmissionNo,$newMemberAdmissionNoBpt,$avgSavingsDepositor,$inactiveSamityNo)==0 && min($openingMember,$openingSamityNo,$newSamityNo,$newMemberAdmissionNo,$newMemberAdmissionNoBpt,$avgSavingsDepositor,$inactiveSamityNo)==0) {
                        $isEmptyInfo = 1;
                    } 

                    $mfnMonthEndProcessMembersId = (int) DB::table('mfn_month_end_process_members')->where('date',$this->monthEndDate)->where('branchIdFk',$this->targetBranchId)->where('fundingOrgIdFk',$fundingOrganizationId)->where('loanProductCategoryIdFk',$loanCategoryId)->where('genderTypeId',$genderTypeId)->where('loanProductCategoryIdFk','>',0)->value('id');

                    if ($mfnMonthEndProcessMembersId>0) {
                        $monEndMember = MfnMonthEndProcessMembers::find($mfnMonthEndProcessMembersId);
                        if ($isEmptyInfo==1) {
                            $monEndMember->delete();
                            continue;
                        }
                    }
                    else{
                        if ($isEmptyInfo==1) {
                            continue;
                        }
                        $monEndMember = new MfnMonthEndProcessMembers;
                    }

                        // This data will be countetd later, for the initialization, it is declared as zero
                        /// assummption

                    $cancelSamityNo = 0;
                        // $memberCancellationNo = 0;

                        /// end assummption



                        // $closingMember = $openingMember + $newMemberAdmissionNo - $memberCancellationNo;
                    $closingMember = $openingMember + $newMemberAdmissionNo - $memberCancellationNo + $newMemberAdmissionNoBpt - $newMemberCancellationNoBpt;

                        /*if(Auth::user()->id==1){
                            echo '$fundingOrganizationId: '.$fundingOrganizationId.'<br>';
                            echo '$loanCategoryId: '.$loanCategoryId.'<br>';
                            echo '$currentLoanProductIds: '.implode(',',$currentLoanProductIds).'<br>';
                            echo '$newMemberCancellationNoBpt: '.$newMemberCancellationNoBpt.'<br><br>';
                        }*/
                        $closingSamityNo = $openingSamityNo + $newSamityNo - $cancelSamityNo - $inactiveSamityNo;
                        $closingSamityNo = $closingSamityNo <1 ? 0 : $closingSamityNo;

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
                        $monEndMember->closingSamityNo          = $closingSamityNo;
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

                        // if all the information is zero than do not need to save it.
                        $isEmptyInfo = 0;

                        if ( max($openingBalance,$currentSavDetails->sum('savingDepositAmount'),$currentTransferTo->sum('amount'),$currentSavDetails->sum('savingWithdrawAmount'),$currentTransferFrom->sum('amount'),$closingBalance,$currentTransferTo->sum('amount'),$currentTransferFrom->sum('amount'))==0 && min($openingBalance,$currentSavDetails->sum('savingDepositAmount'),$currentTransferTo->sum('amount'),$currentSavDetails->sum('savingWithdrawAmount'),$currentTransferFrom->sum('amount'),$closingBalance,$currentTransferTo->sum('amount'),$currentTransferFrom->sum('amount'))==0) {
                            $isEmptyInfo = 1;
                        }

                        if ($isEmptyInfo==1) {
                            continue;
                        }

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
                $openingCollectionAmount += DB::table('mfn_loan_waivers')->where('loanIdFk',$loan->id)->where('date','<',$monthFirstDate)->sum('principalAmount');
                $openingCollectionAmount += DB::table('mfn_loan_rebates')->where('softDel',0)->where('loanIdFk',$loan->id)->where('date','<',$monthFirstDate)->sum('amount');
                $openingCollectionAmount += DB::table('mfn_loan_write_off')->where('softDel',0)->where('loanIdFk',$loan->id)->where('date','<',$monthFirstDate)->sum('amount');

                $openingDueAmount = $openingRecoverableAmount - $openingCollectionAmount;
                $openingAdvanceAmount = - $openingDueAmount;

                $openingDueAmount = $openingDueAmount <= 0 ? 0 : $openingDueAmount;
                $openingAdvanceAmount = $openingAdvanceAmount <= 0 ? 0 : $openingAdvanceAmount;

            // GET THIS MONTH INFO
                $thisMonthRecoverable = (float) $schedules->where('loanIdFk',$loan->id)->sum('installmentAmount');
                $recoverableAmount = max(0 ,$thisMonthRecoverable - $openingAdvanceAmount);

                $collectionAmount = (float) $collections->where('loanIdFk',$loan->id)->sum('amount');
                $collectionAmount += DB::table('mfn_loan_waivers')->where('loanIdFk',$loan->id)->where('date','>=',$monthFirstDate)->where('date','<=',$monthEndDate)->sum('principalAmount');
                $collectionAmount += DB::table('mfn_loan_rebates')->where('softDel',0)->where('loanIdFk',$loan->id)->where('date','>=',$monthFirstDate)->where('date','<=',$monthEndDate)->sum('amount');
                $collectionAmount += DB::table('mfn_loan_write_off')->where('softDel',0)->where('loanIdFk',$loan->id)->where('date','>=',$monthFirstDate)->where('date','<=',$monthEndDate)->sum('amount');

                $dueRecoveryAmount = min($collectionAmount, $openingDueAmount);
                if ($recoverableAmount<$thisMonthRecoverable) {
                    $regularRecoveryAmount = min($recoverableAmount, $collectionAmount - $dueRecoveryAmount);
                }
                else{
                    $regularRecoveryAmount = min($thisMonthRecoverable, $collectionAmount - $dueRecoveryAmount);
                }
                $regularRecoveryAmount = max(0, $regularRecoveryAmount);
                $advanceAmount = $collectionAmount - $regularRecoveryAmount - $dueRecoveryAmount;
                $advanceAmount = $advanceAmount < 0 ? 0 : $advanceAmount;

                $disbursedAmount = $loan->disbursementDate >= $monthFirstDate ? $loan->loanAmount : 0;
                $repayAmount = $loan->disbursementDate >= $monthFirstDate ? $loan->totalRepayAmount : 0;

                $outstanding = $loan->totalRepayAmount - $openingCollectionAmount - $collectionAmount;
                $outstanding = $outstanding < 0 ? 0 : $outstanding;

            // $newDueAmount = max(0 ,$thisMonthRecoverable - $regularRecoveryAmount);
                $newDueAmount = max(0 ,$recoverableAmount - $regularRecoveryAmount);

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

                $loanData->recoverableAmount    = max(0,$thisMonthRecoverable - $openingAdvanceAmount);
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
                $recoverableAmount = max(0 ,$thisMonthRecoverable - $openingAdvanceAmount);

                $collectionAmount = (float) $collections->where('loanIdFk',$loan->id)->sum('principalAmount');
                $collectionAmount += DB::table('mfn_loan_waivers')->where('loanIdFk',$loan->id)->where('date','>=',$monthFirstDate)->where('date','<=',$monthEndDate)->sum('principalAmount');
                $collectionAmount += DB::table('mfn_loan_write_off')->where('softDel',0)->where('loanIdFk',$loan->id)->where('date','>=',$monthFirstDate)->where('date','<=',$monthEndDate)->sum('principalAmount');

                $dueRecoveryAmount = min($collectionAmount, $openingDueAmount) < 1 ? 0 : min($collectionAmount, $openingDueAmount);
                if ($recoverableAmount<$thisMonthRecoverable) {
                    $regularRecoveryAmount = min($recoverableAmount, $collectionAmount - $dueRecoveryAmount);
                }
                else{
                    $regularRecoveryAmount = min($thisMonthRecoverable, $collectionAmount - $dueRecoveryAmount);
                }

                $regularRecoveryAmount = max(0, $regularRecoveryAmount);

            /////////////
                if (abs($regularRecoveryAmount - $recoverableAmount)<1 && abs($regularRecoveryAmount - $recoverableAmount)>0) {

                // $regularRecoveryAmount = $recoverableAmount;
                    $recoverableAmount = $regularRecoveryAmount;


                }
            /////////////


                $advanceAmount = $collectionAmount - $regularRecoveryAmount - $dueRecoveryAmount;
                $advanceAmount = $advanceAmount < 0 ? 0 : $advanceAmount;


                $outstanding = $loan->loanAmount - $openingCollectionAmount - $collectionAmount;
                $outstanding = $outstanding < 0 ? 0 : $outstanding;

                $newDueAmount = $recoverableAmount - $regularRecoveryAmount  < 1 ? 0 : $recoverableAmount - $regularRecoveryAmount;


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

                $productData->productIdFk               = $loanProductId;
                $productData->productIdName             = $loanProducts->where('id',$loanProductId)->max('name');
                $productData->genderTypeId              = $genderId;
                $productData->openingBorrowerNo         = $openingInfo->sum('closingBorrowerNo');
                $productData->openingOutstandingAmount  = $openingInfo->sum('closingOutstandingAmount');
                $productData->openingOutstandingAmountWithServicesCharge = $openingInfo->sum('closingOutstandingAmountWithServicesCharge');
                $productData->openingDisbursedAmount    = $openingInfo->sum('closingDisbursedAmount');
                $productData->disbursedAmount           = $currentData->sum('disbursedAmount');
                $productData->borrowerNo                = count($currentData->where('disbursementDate','>=',$monthFirstDate)->unique('memberId')->pluck('memberId')->toArray());
                $productData->repayAmount               = $currentData->sum('repayAmount');
                $productData->principalRecoveryAmount   = $currentData->sum('principalRecoveryAmount');
                $productData->recoveryAmount            = $currentData->sum('recoveryAmount');
                $productData->fullyPaidBorrowerNo       = $fullyPaidBorrowerNo;
                // $productData->closingBorrowerNo = count($memberIdsHavingLoan) - $fullyPaidBorrowerNo;
                $productData->closingBorrowerNo         = $productData->openingBorrowerNo + $productData->borrowerNo - $productData->fullyPaidBorrowerNo;
                $productData->closingOutstandingAmount  = $currentData->sum('closingOutstandingAmount');
                $productData->closingOutstandingAmountWithServicesCharge = $currentData->sum('closingOutstandingAmountWithServicesCharge');
                $productData->closingDisbursedAmount    = $openingInfo->sum('closingDisbursedAmount') + $currentData->where('disbursementDate','>=',$monthFirstDate)->sum('disbursedAmount');
                $productData->openingDueAmount          = $openingInfo->sum('closingDueAmount');
                $productData->openingDueAmountWithServicesCharge = $openingInfo->sum('closingDueAmountWithServicesCharge');
                $productData->principalRecoverableAmount = $currentData->sum('principalRecoverableAmount');
                $productData->recoverableAmount         = $currentData->sum('recoverableAmount');
                $productData->principalRegularAmount    = $currentData->sum('principalRegularAmount');
                $productData->regularAmount             = $currentData->sum('regularAmount');
                $productData->principalAdvanceAmount    = $currentData->sum('principalAdvanceAmount');
                $productData->advanceAmount             = $currentData->sum('advanceAmount');
                $productData->principalDueAmount        = $currentData->sum('principalDueAmount');
                $productData->dueAmount                 = $currentData->sum('dueAmount');
                $productData->principalNewDueAmount     = $currentData->sum('principalNewDueAmount');
                $productData->newDueAmount              = $currentData->sum('newDueAmount');
                $productData->closingDueAmount          = $currentData->sum('closingDueAmount');
                $productData->closingDueAmountWithServicesCharge = $currentData->sum('closingDueAmountWithServicesCharge');
                $productData->noOfDueLoanee             = $currentData->where('classificationTag','!=','Standard')->unique('memberId')->count();
                // $productData->totalNoOfDueLoaneeOnlyOptionalProduct = ;
                $productData->noOfUniqueLoanee          = $currentData->unique('memberId')->count();
                $productData->loanRebateAmount          = $currentData->sum('loanRebateAmount');
                $productData->cumBorrowerNo             = $productData->openingBorrowerNo + $productData->borrowerNo;
                $productData->cumLoanNo                 = $openingInfo->sum('cumLoanNo') + $currentData->where('disbursementDate','>=',$monthFirstDate)->count();
                $productData->watchfulOutstanding       = $currentData->where('classificationTag','Watchful')->sum('closingOutstandingAmount');
                $productData->watchfulOutstandingWithServicesCharge = $currentData->where('classificationTag','Watchful')->sum('closingOutstandingAmountWithServicesCharge');
                $productData->watchfulOverdue           = $currentData->where('classificationTag','Watchful')->sum('closingDueAmount');
                $productData->watchfulOverdueWithServicesCharge = $currentData->where('classificationTag','Watchful')->sum('watchfulOverdueWithServicesCharge');

                $productData->substandardOutstanding    = $currentData->where('classificationTag','Substandard')->sum('closingOutstandingAmount');
                $productData->substandardOutstandingWithServicesCharge = $currentData->where('classificationTag','Substandard')->sum('closingOutstandingAmountWithServicesCharge');
                $productData->substandardOverdue        = $currentData->where('classificationTag','Substandard')->sum('closingDueAmount');
                $productData->substandardOverdueWithServicesCharge = $currentData->where('classificationTag','Substandard')->sum('watchfulOverdueWithServicesCharge');

                $productData->doubtfullOutstanding      = $currentData->where('classificationTag','Doubtful')->sum('closingOutstandingAmount');
                $productData->doubtfullOutstandingWithServicesCharge = $currentData->where('classificationTag','Doubtful')->sum('closingOutstandingAmountWithServicesCharge');
                $productData->doubtfullOverdue          = $currentData->where('classificationTag','Doubtful')->sum('closingDueAmount');
                $productData->doubtfullOverdueWithServicesCharge = $currentData->where('classificationTag','Doubtful')->sum('watchfulOverdueWithServicesCharge');

                $productData->badOutstanding            = $currentData->where('classificationTag','Bad Loan')->sum('closingOutstandingAmount');
                $productData->badOutstandingWithServicesCharge = $currentData->where('classificationTag','Bad Loan')->sum('closingOutstandingAmountWithServicesCharge');
                $productData->badOverdue                = $currentData->where('classificationTag','Bad Loan')->sum('closingDueAmount');
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

        DB::table('mfn_month_end_process_loans')->where('branchIdFk',$this->targetBranchId)->where('date',$this->monthEndDate)->delete();

        $monthFirstDate = $this->monthFirstDate;
        $monthEndDate = $this->monthEndDate;

        $datas = $this->getLoanDetails();
        // dd();

        foreach ($datas as $data) {
            $mEloanInfo = new MfnMonthEndProcessLoan;
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

    

    public function getOverDueInfo($loanIds,$loanProductId,$memberIds,$loans,$allCollections,$shedules,$loanWriteOffs,$loanWaivers,$loanRebates){


            /*if($loanProductId==202916){
                $abc = implode(',',$loanIds);
                echo $abc.'<br>';
            }*/

            $overdue = 0;
            $overdueWithServicesCharge = 0;

            $moreThan2DueInstallmentsLoanIds = array();

            foreach ($loanIds as $loanId) {

                $cWriteOffAmount = $loanWriteOffs->where('date','>=',$this->monthFirstDate)->where('loanIdFk',$loanId)->sum('amount');
                $cWriteOffAmountPrincipal = $loanWriteOffs->where('date','>=',$this->monthFirstDate)->where('loanIdFk',$loanId)->sum('principalAmount');
                $cWaiverAmountPrincipal = $loanWaivers->where('date','>=',$this->monthFirstDate)->where('loanIdFk',$loanId)->sum('principalAmount');
                $cRebateAmount = $loanRebates->where('date','>=',$this->monthFirstDate)->where('loanIdFk',$loanId)->sum('amount');
                $cPaidWithServiceCharge = $cWriteOffAmount + $cWaiverAmountPrincipal + $cRebateAmount;
                $cPaidWithOutServiceCharge = $cWriteOffAmountPrincipal + $cWaiverAmountPrincipal;
                

                /*$overdue = $overdue + (float) $shedules->where('loanIdFk',$loanId)->where('isCompleted',0)->sortBy('scheduleDate')->max('principalAmount');*/
                if ($shedules->where('loanIdFk',$loanId)->sum('principalAmount') - $allCollections->where('loanIdFk',$loanId)->sum('principalAmount') - $cPaidWithOutServiceCharge > 0) {
                    $overdue = $overdue + $shedules->where('loanIdFk',$loanId)->sum('principalAmount') - $allCollections->where('loanIdFk',$loanId)->sum('principalAmount') - $cPaidWithOutServiceCharge;
                }

                /*$overdueWithServicesCharge = $overdueWithServicesCharge + (float) $shedules->where('loanIdFk',$loanId)->where('isCompleted',0)->sortBy('scheduleDate')->max('installmentAmount');*/

                if ($shedules->where('loanIdFk',$loanId)->sum('installmentAmount') - $allCollections->where('loanIdFk',$loanId)->sum('amount') - $cPaidWithServiceCharge > 0) {
                    $overdueWithServicesCharge = $overdueWithServicesCharge + $shedules->where('loanIdFk',$loanId)->sum('installmentAmount') - $allCollections->where('loanIdFk',$loanId)->sum('amount') - $cPaidWithServiceCharge;
                }

                if ($shedules->where('loanIdFk',$loanId)->where('isCompleted',0)->count()>2) {
                    array_push($moreThan2DueInstallmentsLoanIds,$loanId);
                }
            }

            $cWriteOffAmount = $loanWriteOffs->where('date','>=',$this->monthFirstDate)->whereIn('loanIdFk',$loanIds)->sum('amount');
            $cWriteOffAmountPrincipal = $loanWriteOffs->where('date','>=',$this->monthFirstDate)->whereIn('loanIdFk',$loanIds)->sum('principalAmount');
            $cWaiverAmountPrincipal = $loanWaivers->where('date','>=',$this->monthFirstDate)->whereIn('loanIdFk',$loanIds)->sum('principalAmount');
            $cRebateAmount = $loanRebates->where('date','>=',$this->monthFirstDate)->whereIn('loanIdFk',$loanIds)->sum('amount');
            $cPaidWithServiceCharge = $cWriteOffAmount + $cWaiverAmountPrincipal + $cRebateAmount;
            $cPaidWithOutServiceCharge = $cWriteOffAmountPrincipal + $cWaiverAmountPrincipal;

            $outStanding = $loans->whereIn('id',$loanIds)->where('productIdFk',$loanProductId)->whereIn('memberIdFk',$memberIds)->sum('loanAmount') - $allCollections->where('productIdFk',$loanProductId)->whereIn('memberIdFk',$memberIds)->whereIn('loanIdFk',$loanIds)->sum('principalAmount') - $cPaidWithOutServiceCharge;

            $outStandingWithServiceCharge = $loans->whereIn('id',$loanIds)->where('productIdFk',$loanProductId)->whereIn('memberIdFk',$memberIds)->sum('totalRepayAmount') - $allCollections->where('productIdFk',$loanProductId)->whereIn('memberIdFk',$memberIds)->whereIn('loanIdFk',$loanIds)->sum('amount') - $cPaidWithServiceCharge;

            $outstandingWithMoreThan2DueInstallments = $loans->whereIn('id',$moreThan2DueInstallmentsLoanIds)->where('productIdFk',$loanProductId)->whereIn('memberIdFk',$memberIds)->sum('loanAmount') - $allCollections->where('productIdFk',$loanProductId)->whereIn('memberIdFk',$memberIds)->whereIn('loanIdFk',$moreThan2DueInstallmentsLoanIds)->sum('principalAmount') - $cPaidWithOutServiceCharge;

            $outstandingWithMoreThan2DueInstallmentsServicesCharge = $loans->whereIn('id',$moreThan2DueInstallmentsLoanIds)->where('productIdFk',$loanProductId)->whereIn('memberIdFk',$memberIds)->sum('totalRepayAmount') - $allCollections->where('productIdFk',$loanProductId)->whereIn('memberIdFk',$memberIds)->whereIn('loanIdFk',$moreThan2DueInstallmentsLoanIds)->sum('amount') - $cPaidWithServiceCharge;

            /*if($loanProductId==7){
                // $abc = implode(',',$loanIds);
                echo '$overdue: '.$overdue.'<br>';
            }*/

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
            ->select('id','fundingOrganizationId','productCategoryId')
            ->get();

            $loanCategoryIds = $loanProducts->unique('productCategoryId')->pluck('productCategoryId')->toArray();

            $funOrgs = DB::table('mfn_funding_organization')
            ->whereIn('id',$loanProducts->pluck('fundingOrganizationId'))
            ->select('id','projectIdFk','projectTypeIdFk')
            ->get();

            $projectTypeIds = $funOrgs->unique('projectTypeIdFk')->pluck('projectTypeIdFk')->all();

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
            $excludedIds = DB::select("SELECT `users_id_fk` FROM `hr_transfer` as t1 WHERE `effect_date`=(SELECT MAX(`effect_date`) FROM `hr_transfer` WHERE `users_id_fk`=t1.`users_id_fk` AND effect_date>?) AND effect_date>? AND `cur_branch_id_fk`=? GROUP BY `users_id_fk`", [$this->monthEndDate,$this->monthEndDate,$this->targetBranchId]);
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

            /*echo '<pre>';
            print_r($excludedIds);
            echo '<pre>';*/

            $targetBranchId = $this->targetBranchId;
            $monthEndDate = $this->monthEndDate;
            $employeeInfo = DB::table('hr_emp_org_info')
            ->where(function($query) use ($includeEmpIds,$targetBranchId,$monthEndDate){
                                    // $query->where([['branch_id_fk',$targetBranchId],['status','Active'],['job_status','Present']])
                $query->where([['branch_id_fk',$targetBranchId],['terminate_resignation_date','>',$monthEndDate]])
                ->orWhere([['branch_id_fk',$targetBranchId],['terminate_resignation_date','0000-00-00']])
                ->orWhereIn('emp_id_fk',$includeEmpIds);
            })
            ->where('joining_date','<=',$this->monthEndDate)
            ->whereNotIn('emp_id_fk',$excludeEmpIds)
            ->select('emp_id_fk','position_id_fk')
            ->get();

            // get the on date position of employess
            $users = DB::table('users')
            ->whereIn('emp_id_fk',$employeeInfo->pluck('emp_id_fk'))
            ->select('id','emp_id_fk')
            ->get();

            /*echo '<pre>';
            print_r($employeeInfo->where('emp_id_fk',105));
            echo '<pre>';*/

            $userIdsString = implode(',',$users->pluck('id')->toArray());

            /*$promotions = DB::select("SELECT `users_id_fk`,`previous_data` FROM `hr_promotion_increment` as t1 WHERE `effect_month`=(SELECT MAX(`effect_month`) FROM `hr_promotion_increment` WHERE `users_id_fk`=t1.`users_id_fk` AND `effect_month`>?) AND `effect_month`>? AND `users_id_fk` IN (?) GROUP BY `users_id_fk`", [$this->monthEndDate,$this->monthEndDate,$userIdsString]);
            
            $promotions = collect($promotions);*/

            $promotions = DB::table('hr_promotion_increment')
            ->where('effect_month','>',$this->monthEndDate)
            ->whereIn('users_id_fk',$users->pluck('id')->toArray())
            ->orderBy('effect_month')
            ->select('users_id_fk','previous_data')
            ->get();

            $promotions = $promotions->unique('users_id_fk');

            /*echo '<pre>';
            print_r($promotions);
            echo '<pre>';*/

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

            /*foreach ($employeeInfo as $employee) {
                echo DB::table('hr_emp_general_info')->where('id',$employee->emp_id_fk)->value('emp_id');
                echo ' - '.DB::table('hr_settings_position')->where('id',$employee->position_id_fk)->value('name').'<br>';
            }*/


            // $tests = $employeeInfo->whereIn('position_id_fk',[131,130,133,66,125,122,127,123]);

            /*foreach ($tests as $employee) {
                echo DB::table('hr_emp_general_info')->where('id',$employee->emp_id_fk)->value('emp_id');
                echo ' - '.DB::table('hr_settings_position')->where('id',$employee->position_id_fk)->value('name').'<br>';
            }*/

            /*echo '<pre>';
            print_r($employeeInfo);
            echo '<pre>';*/

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

            /*$filedOfficerChanges = DB::select("SELECT `samityId`,`fieldOfficerId` FROM `mfn_samity_field_officer_change` as t1 WHERE `effectiveDate`=(SELECT MIN(`effectiveDate`) FROM `mfn_samity_field_officer_change` WHERE `samityId`=t1.`samityId` AND `effectiveDate`>?) AND `effectiveDate`>? AND `samityId` IN (?) GROUP BY `samityId`", [$this->monthEndDate,$this->monthEndDate,$samityIdsString]);
            $filedOfficerChanges = collect($filedOfficerChanges);*/

            $filedOfficerChanges = DB::table('mfn_samity_field_officer_change')
            ->where('effectiveDate','>',$this->monthEndDate)
            ->orderBy('effectiveDate')
            ->whereIn('samityId',$samities->pluck('id')->toArray())
            ->select('samityId','fieldOfficerId')
            ->get();

            $filedOfficerChanges = $filedOfficerChanges->unique('samityId');

            foreach ($filedOfficerChanges as $filedOfficerChange) {
                $samities->where('id',$filedOfficerChange->samityId)->first()->fieldOfficerId = $filedOfficerChange->fieldOfficerId;
            }

            $loans = DB::table('mfn_loan')
            ->where('softDel',0)
            ->where('branchIdFk',$this->targetBranchId)
            ->where('disbursementDate','<=',$this->monthEndDate)
            ->groupBy('samityIdFk','productIdFk')
            ->select('id','samityIdFk','productIdFk')
            ->get();


            // store information product wise
            foreach ($loanProducts as $loanProduct) {

                $samityIds = $loans->where('productIdFk',$loanProduct->id)->pluck('samityIdFk')->toArray();
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

            }

            // store information product category wise
            foreach ($funOrgs as $funOrg) {
                foreach ($loanCategoryIds as $loanCategoryId) {

                    $loanProductIds = $loanProducts->where('fundingOrganizationId',$funOrg->id)->where('productCategoryId',$loanCategoryId)->pluck('id')->toArray();

                    $samityIds = $loans->whereIn('productIdFk',$loanProductIds)->pluck('samityIdFk')->toArray();
                    $fieldOfficerIds = $samities->whereIn('id',$samityIds)->pluck('fieldOfficerId')->toArray();

                    $creditOfficer = $employeeInfo->whereIn('emp_id_fk',$fieldOfficerIds)->where('position_id_fk',122)->count();
                    $enterpriseOfficer = $employeeInfo->whereIn('emp_id_fk',$fieldOfficerIds)->where('position_id_fk',133)->count();

                    $productStaffInfo = new MfnMonthEndProcessStaffInfo;
                    $productStaffInfo->branchIdFk          = $this->targetBranchId;
                    $productStaffInfo->date                = Carbon::parse($this->monthEndDate);
                    $productStaffInfo->fundingOrgId        = $funOrg->id;
                    $productStaffInfo->loanCategoryId      = $loanCategoryId;
                    $productStaffInfo->creditOfficer       = $creditOfficer;
                    $productStaffInfo->enterpriseOfficer   = $enterpriseOfficer;
                    $productStaffInfo->isGrandTotal        = 0;
                    $productStaffInfo->save();
                }
            }

            // store information funding org. wise
            foreach ($funOrgs as $funOrg) {

                $loanProductIds = $loanProducts->where('fundingOrganizationId',$funOrg->id)->pluck('id')->toArray();

                $samityIds = $loans->whereIn('productIdFk',$loanProductIds)->pluck('samityIdFk')->toArray();
                $fieldOfficerIds = $samities->whereIn('id',$samityIds)->pluck('fieldOfficerId')->toArray();

                $creditOfficer = $employeeInfo->whereIn('emp_id_fk',$fieldOfficerIds)->where('position_id_fk',122)->count();
                $enterpriseOfficer = $employeeInfo->whereIn('emp_id_fk',$fieldOfficerIds)->where('position_id_fk',133)->count();
                
                $productStaffInfo = new MfnMonthEndProcessStaffInfo;
                $productStaffInfo->branchIdFk          = $this->targetBranchId;
                $productStaffInfo->date                = Carbon::parse($this->monthEndDate);
                $productStaffInfo->fundingOrgId        = $funOrg->id;
                $productStaffInfo->loanProductId       = 0;
                $productStaffInfo->creditOfficer       = $creditOfficer;
                $productStaffInfo->enterpriseOfficer   = $enterpriseOfficer;
                $productStaffInfo->isGrandTotal        = 0;
                $productStaffInfo->save();
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

            /*$members = DB::table('mfn_member_information')
            ->where('softDel',0)
            ->whereNotIn('id',$closedMemberIds)
            ->where('admissionDate','<=',$this->monthEndDate)
            ->where('branchId',$this->targetBranchId)
            ->select('id','primaryProductId','samityId')
            ->get();*/

            $members = $this->dbMembers
            ->whereNotIn('id',$closedMemberIds)
            ->where('admissionDate','<=',$this->monthEndDate);

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

            /*$savingsDeposits = DB::table('mfn_savings_deposit')
            ->where('softDel',0)
            ->whereIn('accountIdFk',$savingsAccounts->pluck('id'))
            ->where('depositDate','<=',$this->monthEndDate)
            ->select('accountIdFk','productIdFk','memberIdFk','amount','depositDate')
            ->get();*/

            $savingsDeposits = $this->dbDeposit
            ->whereIn('accountIdFk',$savingsAccounts->pluck('id'));

            /*$savinngsWithdraws = DB::table('mfn_savings_withdraw')
            ->where('softDel',0)
            ->whereIn('accountIdFk',$savingsAccounts->pluck('id'))
            ->where('withdrawDate','<=',$this->monthEndDate)
            ->select('accountIdFk','productIdFk','memberIdFk','amount','withdrawDate')
            ->get();*/

            $savinngsWithdraws = $this->dbWithdraw
            ->whereIn('accountIdFk',$savingsAccounts->pluck('id'));

            foreach ($members as $member) {
                $mSavAccs = $savingsAccounts->where('memberIdFk',$member->id);
                $productIds = $mSavAccs->unique('savingsProductIdFk')->pluck('savingsProductIdFk')->toArray();

                foreach ($productIds as $productId) {
                    $pSavAccs = $mSavAccs->where('savingsProductIdFk',$productId);

                    $pInterestAmount = 0;

                    foreach ($pSavAccs as $pSavAcc) {
                        $opDep = $savingsDeposits->where('depositDate','<',$this->monthFirstDate)->where('accountIdFk',$pSavAcc->id)->sum('amount');
                        $opWithdraw = $savinngsWithdraws->where('withdrawDate','<',$this->monthFirstDate)->where('accountIdFk',$pSavAcc->id)->sum('amount');
                        $opBalance = $opDep - $opWithdraw;

                        $closingDep = $savingsDeposits->where('accountIdFk',$pSavAcc->id)->sum('amount');
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