<?php

namespace App\Http\Controllers\microfin\loan;

use Illuminate\Http\Request;
use App\Http\Requests;
use Session;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\microfin\loan\MfnLoanWriteOff;
use App\microfin\loan\MfnLoanWriteOffCollection;
use App\microfin\process\MfnLoanRebate;
use App\Traits\GetSoftwareDate;
use App\Http\Controllers\microfin\MicroFin;
use App\Service\Service;
use App;


class MfnLoanWriteOffController extends controller {

    public function writeOffEligibleLoanList(){
        $userBranchId = Auth::user()->branchId;
        //$branchList = MicroFin::getBranchList();

        $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
        if ($userBranchId==1) {
            $branchList = MicroFin::getBranchList();

        }
        else{
            $branchList = DB::table('gnr_branch')
            ->whereIn('id',$branchIdArray )
            ->orderBy('branchCode')
            ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
            ->pluck('nameWithCode', 'id')
            ->all();
        }
        if ($userBranchId!=1) {
            //$samityList = MicroFin::getBranchWiseSamityList($userBranchId);
            $branchIdArray = Service::getEngagedBranchesByUserId(Auth::user()->id);
            $samityList = MicroFin::getBranchWiseSamityList($branchIdArray);
        }
        else{
            $samityList = [];
        }

        $data = array(
            'userBranchId' 	=> $userBranchId,
            'branchList' 	=> $branchList,
            'branchIdArray'   => $branchIdArray,
            'samityList' 	=> $samityList
        );

        return view('microfin.loan.loanWriteOff.writeOffEligibleListFilteringPart',$data);
    }

    public function getWriteOffEligibleLoanList(Request $req){

        if ($req->filBranch==null && Auth::user()->branchId==1) {
            return false;
        }

        $userBranchId = Auth::user()->branchId;
        $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
        if ($userBranchId==1) {
            $softwareDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);
            $targetBranchId = $req->filBranch;
        }
        else{
            $softwareDate = MicroFin::getSoftwareDateBranchWise($userBranchId);
            $targetBranchId = $userBranchId;
        }

        if (isset($req->filSamity) && (int) $req->filSamity > 0) {
            $loans = DB::select("SELECT id,loanCode,memberIdFk,productIdFk,disbursementDate ,lastInstallmentDate,loanAmount,totalRepayAmount FROM `mfn_loan` WHERE softDel=0 AND loanCompletedDate='0000-00-00' AND branchIdFk=? AND samityIdFk=? AND DATEDIFF(? ,lastInstallmentDate) >= 366", [$targetBranchId,$req->filSamity,$softwareDate]);
        }
        else{
            $loans = DB::select("SELECT id,loanCode,memberIdFk,productIdFk,disbursementDate ,lastInstallmentDate,loanAmount,totalRepayAmount FROM `mfn_loan` WHERE softDel=0 AND loanCompletedDate='0000-00-00' AND branchIdFk=? AND DATEDIFF(? ,lastInstallmentDate) >= 366", [$targetBranchId,$softwareDate]);
        }        

        $loans = collect($loans);

        $loanProducts = DB::table('mfn_loans_product')
        ->get();

        $members = DB::table('mfn_member_information')
        ->whereIn('id',$loans->pluck('memberIdFk'))
        ->select('id','name','spouseFatherSonName')
        ->get();

        $lastTransactionDates = DB::table('mfn_loan_collection')
        ->where('softDel',0)
        ->where('amount','>',0)
        ->whereIn('loanIdFk',$loans->pluck('id'))
        ->select(DB::raw('loanIdFk, MAX(collectionDate) AS lastTransactionDate'))
        ->groupBy('loanIdFk')
        ->get();

        $savingsDeposits = DB::table('mfn_savings_deposit')
        ->where('softDel',0)
        ->whereIn('memberIdFk',$loans->unique('memberIdFk')->pluck('memberIdFk'))
        ->select(DB::raw('memberIdFk, SUM(amount) AS amount'))
        ->groupBy('memberIdFk')
        ->get();

        $savingsWithdraws = DB::table('mfn_savings_withdraw')
        ->where('softDel',0)
        ->whereIn('memberIdFk',$loans->unique('memberIdFk')->pluck('memberIdFk'))
        ->select(DB::raw('memberIdFk, SUM(amount) AS amount'))
        ->groupBy('memberIdFk')
        ->get();

        $loanInfo = collect();

        foreach ($loans as $loan) {
            $outstanding = Microfin::getLoanOutstanding($loan->id);
            $advanceDue = Microfin::getLoanAdvanceDue($loan->id, $softwareDate);
            
            $loanInfo->push([
                'loanId' => $loan->id,
                'paidAmount' => $outstanding['paidAmount'],
                'outstanding' => $outstanding['outstanding'],
                'dueAmount' => $advanceDue['dueAmount'],
                'dueAmountPrincipal' => $advanceDue['dueAmountPrincipal'],
                'dueAmountInterest' => $advanceDue['dueAmount'] - $advanceDue['dueAmountPrincipal'],
                'lastTransactionDate' => $lastTransactionDates->where('loanIdFk',$loan->id)->max('lastTransactionDate')
            ]);
        }


        if ($userBranchId==1) {
            $branchList = MicroFin::getBranchList();

        }
        else{
            $branchList = DB::table('gnr_branch')
            ->whereIn('id',$branchIdArray )
            ->orderBy('branchCode')
            ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
            ->pluck('nameWithCode', 'id')
            ->all();
        }


        $data = array(
            'targetBranchId'        => $targetBranchId,
            'softwareDate'          => $softwareDate,
            'loans'                 => $loans,
            'loanProducts'          => $loanProducts,
            'members'               => $members,
            'userBranchId'     => $userBranchId,
            'branchList'               => $branchList,
            'branchIdArray'               => $branchIdArray,
            'savingsDeposits'       => $savingsDeposits,
            'savingsWithdraws'      => $savingsWithdraws,
            'loanInfo'              => $loanInfo
        );

        return view('microfin.loan.loanWriteOff.writeOffEligibleList',$data);
    }






    public function getWriteOffEligibleLoanList_old(Request $req){

        if ($req->filBranch==null && Auth::user()->branchId==1) {
            return false;
        }

        $userBranchId = Auth::user()->branchId;
        if ($userBranchId==1) {
            $softwareDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);
            $targetBranchId = $req->filBranch;
        }
        else{
            $softwareDate = MicroFin::getSoftwareDateBranchWise($userBranchId);
            $targetBranchId = $userBranchId;
        }

        if (isset($req->filSamity) && (int) $req->filSamity > 0) {
            $loans = DB::select("SELECT id,loanCode,memberIdFk,productIdFk,disbursementDate ,lastInstallmentDate,loanAmount,totalRepayAmount FROM `mfn_loan` WHERE softDel=0 AND loanCompletedDate='0000-00-00' AND branchIdFk=? AND samityIdFk=? AND DATEDIFF(? ,lastInstallmentDate) >= 366", [$targetBranchId,$req->filSamity,$softwareDate]);
        }
        else{
            $loans = DB::select("SELECT id,loanCode,memberIdFk,productIdFk,disbursementDate ,lastInstallmentDate,loanAmount,totalRepayAmount FROM `mfn_loan` WHERE softDel=0 AND loanCompletedDate='0000-00-00' AND branchIdFk=? AND DATEDIFF(? ,lastInstallmentDate) >= 366", [$targetBranchId,$softwareDate]);
        }        

        $loans = collect($loans);

        $loanProducts = DB::table('mfn_loans_product')
        ->get();

        $members = DB::table('mfn_member_information')
        ->whereIn('id',$loans->pluck('memberIdFk'))
        ->select('id','name','spouseFatherSonName')
        ->get();

        $lastTransactionDates = DB::table('mfn_loan_collection')
        ->where('softDel',0)
        ->where('amount','>',0)
        ->whereIn('loanIdFk',$loans->pluck('id'))
        ->select(DB::raw('loanIdFk, MAX(collectionDate) AS lastTransactionDate'))
        ->groupBy('loanIdFk')
        ->get();

        $savingsDeposits = DB::table('mfn_savings_deposit')
        ->where('softDel',0)
        ->whereIn('memberIdFk',$loans->unique('memberIdFk')->pluck('memberIdFk'))
        ->select(DB::raw('memberIdFk, SUM(amount) AS amount'))
        ->groupBy('memberIdFk')
        ->get();

        $savingsWithdraws = DB::table('mfn_savings_withdraw')
        ->where('softDel',0)
        ->whereIn('memberIdFk',$loans->unique('memberIdFk')->pluck('memberIdFk'))
        ->select(DB::raw('memberIdFk, SUM(amount) AS amount'))
        ->groupBy('memberIdFk')
        ->get();

        $loanInfo = collect();

        foreach ($loans as $loan) {
            $outstanding = Microfin::getLoanOutstanding($loan->id);
            $advanceDue = Microfin::getLoanAdvanceDue($loan->id, $softwareDate);
            
            $loanInfo->push([
                'loanId' => $loan->id,
                'paidAmount' => $outstanding['paidAmount'],
                'outstanding' => $outstanding['outstanding'],
                'dueAmount' => $advanceDue['dueAmount'],
                'dueAmountPrincipal' => $advanceDue['dueAmountPrincipal'],
                'dueAmountInterest' => $advanceDue['dueAmount'] - $advanceDue['dueAmountPrincipal'],
                'lastTransactionDate' => $lastTransactionDates->where('loanIdFk',$loan->id)->max('lastTransactionDate')
            ]);
        }


        $data = array(
            'targetBranchId'        => $targetBranchId,
            'softwareDate'          => $softwareDate,
            'loans'                 => $loans,
            'loanProducts'          => $loanProducts,
            'members'               => $members,
            'savingsDeposits'       => $savingsDeposits,
            'savingsWithdraws'      => $savingsWithdraws,
            'loanInfo'              => $loanInfo
        );

        return view('microfin.loan.loanWriteOff.writeOffEligibleList',$data);
    }


    public function viewWriteOff(){
        $userBranchId = Auth::user()->branchId;
        //$branchList = MicroFin::getBranchList();
        $branchIdArray = Service::getEngagedBranchesByUserId(Auth::user()->id);
        if ($userBranchId==1) {
            $branchList = MicroFin::getBranchList();

        }
        else{
            $branchList = DB::table('gnr_branch')
            ->whereIn('id',$branchIdArray )
            ->orderBy('branchCode')
            ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
            ->pluck('nameWithCode', 'id')
            ->all();
        }
        if ($userBranchId!=1) {
            //$branchIdArray = Service::getEngagedBranchesByUserId(Auth::user()->id);
            $samityList = MicroFin::getBranchWiseSamityList($branchIdArray);
        }
        else{
            $samityList = [];
        }

        $data = array(
            'userBranchId'  => $userBranchId,
            'branchList'    => $branchList,
            'branchIdArray'    => $branchIdArray,
            'samityList'    => $samityList
        );

        return view('microfin.loan.loanWriteOff.writeOffListFilteringPart',$data);
    }



    public function getWriteOffList(Request $req){
        $userBranchId = Auth::user()->branchId;
        $writeOffs = DB::table('mfn_loan_write_off')->where('softDel',0);
        $branchIdArray = Service::getEngagedBranchesByUserId(Auth::user()->id);
        $softwareDate = null;
        if ($userBranchId!=1) {
            $writeOffs = $writeOffs->where('branchIdFk',$userBranchId);
            $softwareDate = MicroFin::getSoftwareDateBranchWise($userBranchId);
        }
        else{
            if ($req->filBranch!='' || $req->filBranch!=null) {
                $writeOffs = $writeOffs->where('branchIdFk',$req->filBranch);
                $softwareDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);
            }                
        }

        if (isset($req->filBranch)) {
            if ($req->filBranch != '' && $req->filBranch != null) {
                $writeOffs = $writeOffs->where('branchIdFk', '=', $req->filBranch);
            }
        }

        if ($req->filSamity!='' || $req->filSamity!=null) {
            $writeOffs = $writeOffs->where('samityIdFk',$req->filSamity);
        }
        if ($req->filDateFrom!='' || $req->filDateFrom!=null) {
            $filDateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');
            $writeOffs = $writeOffs->where('date','>=',$filDateFrom);
        }
        if ($req->filDateTo!='' || $req->filDateTo!=null) {
            $filDateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
            $writeOffs = $writeOffs->where('date','<=',$filDateTo);
        }
        $writeOffs = $writeOffs->orderBy('branchIdFk','samityIdFk','date')->get();

        $loans = DB::table('mfn_loan')
        ->whereIn('id',$writeOffs->pluck('loanIdFk'));

        if ($req->filSearchKey!='' || $req->filSearchKey!=null) {
            $loans = $loans->where('loanCode','like',"%$req->filSearchKey%");
        }

        $loans = $loans->select('id','loanCode','memberIdFk')->get();
        $writeOffs = $writeOffs->whereIn('loanIdFk',$loans->pluck('id'));

        $samities = DB::table('mfn_samity')
        ->whereIn('id',$writeOffs->pluck('samityIdFk'))
        ->select('id','code')
        ->get();

        $members = DB::table('mfn_member_information')
        ->whereIn('id',$writeOffs->pluck('memberIdFk'))
        ->select('id','code')
        ->get();

        $writeOffCollections = DB::table('mfn_loan_write_off_collection')
        ->where('softDel',0)
        ->whereIn('loanIdFk',$writeOffs->pluck('loanIdFk'))
        ->select('loanIdFk')
        ->get();

        if ($userBranchId==1) {
            $branchList = MicroFin::getBranchList();

        }
        else{
            $branchList = DB::table('gnr_branch')
            ->whereIn('id',$branchIdArray )
            ->orderBy('branchCode')
            ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
            ->pluck('nameWithCode', 'id')
            ->all();
        }

        $data = array(
            'writeOffs'             => $writeOffs,
            'loans'                 => $loans,
            'samities'              => $samities,
            'members'               => $members,
            'softwareDate'          => $softwareDate,
            'branchIdArray'         => $branchIdArray,
            'writeOffCollections'   => $writeOffCollections,
        );

        return view('microfin.loan.loanWriteOff.writeOffList',$data);
    }


    public function getWriteOffList_old(Request $req){
        $userBranchId = Auth::user()->branchId;
        $writeOffs = DB::table('mfn_loan_write_off')->where('softDel',0);
        $softwareDate = null;
        if ($userBranchId!=1) {
            $writeOffs = $writeOffs->where('branchIdFk',$userBranchId);
            $softwareDate = MicroFin::getSoftwareDateBranchWise($userBranchId);
        }
        else{
            if ($req->filBranch!='' || $req->filBranch!=null) {
                $writeOffs = $writeOffs->where('branchIdFk',$req->filBranch);
                $softwareDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);
            }                
        }

        if ($req->filSamity!='' || $req->filSamity!=null) {
            $writeOffs = $writeOffs->where('samityIdFk',$req->filSamity);
        }
        if ($req->filDateFrom!='' || $req->filDateFrom!=null) {
            $filDateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');
            $writeOffs = $writeOffs->where('date','>=',$filDateFrom);
        }
        if ($req->filDateTo!='' || $req->filDateTo!=null) {
            $filDateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
            $writeOffs = $writeOffs->where('date','<=',$filDateTo);
        }
        $writeOffs = $writeOffs->orderBy('branchIdFk','samityIdFk','date')->get();

        $loans = DB::table('mfn_loan')
        ->whereIn('id',$writeOffs->pluck('loanIdFk'));

        if ($req->filSearchKey!='' || $req->filSearchKey!=null) {
            $loans = $loans->where('loanCode','like',"%$req->filSearchKey%");
        }

        $loans = $loans->select('id','loanCode','memberIdFk')->get();
        $writeOffs = $writeOffs->whereIn('loanIdFk',$loans->pluck('id'));

        $samities = DB::table('mfn_samity')
        ->whereIn('id',$writeOffs->pluck('samityIdFk'))
        ->select('id','code')
        ->get();

        $members = DB::table('mfn_member_information')
        ->whereIn('id',$writeOffs->pluck('memberIdFk'))
        ->select('id','code')
        ->get();

        $writeOffCollections = DB::table('mfn_loan_write_off_collection')
        ->where('softDel',0)
        ->whereIn('loanIdFk',$writeOffs->pluck('loanIdFk'))
        ->select('loanIdFk')
        ->get();

        $data = array(
            'writeOffs'             => $writeOffs,
            'loans'                 => $loans,
            'samities'              => $samities,
            'members'               => $members,
            'softwareDate'          => $softwareDate,
            'writeOffCollections'   => $writeOffCollections,
        );

        return view('microfin.loan.loanWriteOff.writeOffList',$data);
    }

    public function addWriteOff($loanId=null){

        if ($loanId!=null) {
            $encryptLoanId = $loanId;
            $loanId = decrypt($loanId);

            $loan = DB::table('mfn_loan')->where('id',$loanId)->first();
            $member = DB::table('mfn_member_information')
            ->where('id',$loan->memberIdFk)
            ->select(DB::raw("CONCAT(TRIM(name), ' - ', code) AS memberName"))
            ->first();
            $softwareDate = MicroFin::getSoftwareDateBranchWise($loan->branchIdFk);
            $softwareDate = date('d-m-Y',strtotime($softwareDate));

            $outstanding = MicroFin::getLoanOutstanding($loan->id);
            $dueAdvance = MicroFin::getLoanAdvanceDue($loan->id,$softwareDate);

            // dd(get_defined_vars());

            $data = array(
                'loan'          => $loan,
                'encryptLoanId' => $encryptLoanId,
                'softwareDate'  => $softwareDate,
                'member'        => $member->memberName,
                'outstanding'   => $outstanding,
                'dueAdvance'    => $dueAdvance
            );
        }
        else{
            $data = array();
        }

        return view('microfin/loan/loanWriteOff/addLoanWriteOff',$data);
    }

    public function storeWriteOff(Request $req){
        $loanId = decrypt($req->loanId);
        $loan = DB::table('mfn_loan')->where('id',$loanId)->select('id','memberIdFk','productIdFk','primaryProductIdFk','branchIdFk','samityIdFk')->first();

        $writeoffDate = Carbon::parse($req->writeOffDate)->format('Y-m-d');
        $branchDate = Microfin::getSoftwareDateBranchWise($loan->branchIdFk);

        if ($writeoffDate!=$branchDate) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Writeoff date and the branch date are not the same.'
            );
            return response::json($data);
        }

        // IF ANY WRITE OFF, REBATE OR WAIVER EXITS THEN IT CAN NOT BE DONE.
        $errorMsg = '';
        $writeOffId = (int) DB::table('mfn_loan_write_off')
        ->where([['softDel',0], ['loanIdFk',$loan->id] ])
        ->value('id');
        $errorMsg = $writeOffId > 0 ? 'Writeoff already exists.' : $errorMsg;

        $waiverId = (int) DB::table('mfn_loan_waivers')
        ->where([['softDel',0], ['loanIdFk',$loan->id] ])
        ->value('id');
        $errorMsg = $writeOffId > 0 ? 'This loan is in Waiver List. You can not write off it.' : $errorMsg;

        $rebateId = (int) DB::table('mfn_loan_rebates')
        ->where([['softDel',0], ['loanIdFk',$loan->id] ])
        ->value('id');
        $errorMsg = $writeOffId > 0 ? 'This loan is in Rebate List. You can not write off it.' : $errorMsg;

        if (max($writeOffId,$waiverId,$rebateId) > 0) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  $errorMsg
            );
            return response::json($data);
        }

        // STORE WRITE OFF
        DB::beginTransaction();

        $outstanding = MicroFin::getLoanOutstanding($loan->id);

        try{
            $writeOff = new MfnLoanWriteOff;
            $writeOff->loanIdFk             = $loan->id;
            $writeOff->productIdFk          = $loan->productIdFk;
            $writeOff->primaryProductIdFk   = $loan->primaryProductIdFk;
            $writeOff->memberIdFk           = $loan->memberIdFk;
            $writeOff->samityIdFk           = $loan->samityIdFk;
            $writeOff->branchIdFk           = $loan->branchIdFk;
            $writeOff->isDeath              = $req->isDeath;
            $writeOff->date                 = $branchDate;
            $writeOff->amount               = $outstanding['outstandingPrincipal'];
            $writeOff->principalAmount      = $outstanding['outstandingPrincipal'];
            $writeOff->interestAmount       = 0;
            $writeOff->notes                = $req->notes;
            $writeOff->createdAt            = Carbon::now();
            $writeOff->save();

            $loanRebate = new MfnLoanRebate;
            $loanRebate->loanIdFk           = $loan->id;
            $loanRebate->productIdFk        = $loan->productIdFk;
            $loanRebate->primaryProductIdFk = $loan->primaryProductIdFk;
            $loanRebate->memberIdFk         = $loan->memberIdFk;
            $loanRebate->samityIdFk         = $loan->samityIdFk;
            $loanRebate->branchIdFk         = $loan->branchIdFk;
            $loanRebate->date               = $branchDate;
            $loanRebate->amount             = $outstanding['outstandingInterest'];
            $loanRebate->notes              = $req->notes;
            $loanRebate->writeOffId         = $writeOff->id;
            $loanRebate->entryByEmpIdFk     = Auth::user()->emp_id_fk;
            $loanRebate->status             = 1;
            $loanRebate->save();

            // UPDATE LOANSTATUS AND SCHEDULE
            Microfin::updateLoanStatusNSchedule($loan->id);

            DB::commit();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data inserted successfully.'
            );
            return response::json($data);
        }
        catch(\Exception $e){
            DB::rollback();
            $data = array(
                'responseTitle'  =>  'Warning!',
                'responseText'   =>  'Something went wrong. Please try again.'.$e->getLine().' '.$e->getMessage()
            );
            return response::json($data);
        }

    }

    public function deleteWriteOff(Request $req){
        $writeOff = MfnLoanWriteOff::find($req->id);

        $branchDate = Microfin::getSoftwareDateBranchWise($writeOff->branchIdFk);
        if ($writeOff->date != $branchDate) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Writeoff date and the branch date are not the same.'
            );
            return response::json($data);
        }

        DB::beginTransaction();
        try{
            $writeOff->softDel = 1;
            $writeOff->deletedByEmpIdFk = Auth::user()->emp_id_fk;
            $writeOff->save();

            $loanRebate = MfnLoanRebate::where('writeOffId',$req->id)->first();
            $loanRebate->softDel = 1;
            $loanRebate->deletedByEmpIdFk = Auth::user()->emp_id_fk;
            $loanRebate->save();

            // UPDATE LOANSTATUS AND SCHEDULE
            Microfin::updateLoanStatusNSchedule($writeOff->loanIdFk);

            DB::commit();
            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data deleted successfully.'
            );
            return response::json($data);
        }
        catch(\Exception $e){
            DB::rollback();
            $data = array(
                'responseTitle'  =>  'Warning!',
                'responseText'   =>  'Something went wrong. Please try again.'
            );
            return response::json($data);
        }
    }

    /// Writeoff Collection

    public function writeOffCollection(){
        $userBranchId = Auth::user()->branchId;
        //$branchList = MicroFin::getBranchList();
        $branchIdArray = Service::getEngagedBranchesByUserId(Auth::user()->id);
        if ($userBranchId==1) {
            $branchList = MicroFin::getBranchList();

        }
        else{
            $branchList = DB::table('gnr_branch')
            ->whereIn('id',$branchIdArray )
            ->orderBy('branchCode')
            ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
            ->pluck('nameWithCode', 'id')
            ->all();
        }

        if ($userBranchId!=1) {
            //$samityList = MicroFin::getBranchWiseSamityList($userBranchId);
            //$branchIdArray = Service::getEngagedBranchesByUserId(Auth::user()->id);
            $samityList = MicroFin::getBranchWiseSamityList($branchIdArray);
        }
        else{
            $samityList = [];
        }

        $data = array(
            'userBranchId'  => $userBranchId,
            'branchList'    => $branchList,
            'branchIdArray'    => $branchIdArray,
            'samityList'    => $samityList
        );
        return view('microfin.loan.loanWriteOff.writeOffCollectionFilteringPart',$data);
    }

    public function getWriteOffCollectionList(Request $req){
        $userBranchId = Auth::user()->branchId;
        $writeOffcollections = DB::table('mfn_loan_write_off_collection')->where('softDel',0);
        $branchIdArray = Service::getEngagedBranchesByUserId(Auth::user()->id);
        $softwareDate = null;
        if ($userBranchId!=1) {
            $writeOffcollections = $writeOffcollections->where('branchIdFk',$userBranchId);
            $softwareDate = MicroFin::getSoftwareDateBranchWise($userBranchId);
        }
        else{
            if ($req->filBranch!='' || $req->filBranch!=null) {
                $writeOffcollections = $writeOffcollections->where('branchIdFk',$req->filBranch);
                $softwareDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);
            }                
        }

        if (isset($req->filBranch)) {
            if ($req->filBranch != '' && $req->filBranch != null) {
                $writeOffcollections = $writeOffcollections->where('branchIdFk', '=', $req->filBranch);
            }
        }


        if ($req->filSamity!='' || $req->filSamity!=null) {
            $writeOffcollections = $writeOffcollections->where('samityIdFk',$req->filSamity);
        }
        if ($req->filDateFrom!='' || $req->filDateFrom!=null) {
            $filDateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');
            $writeOffcollections = $writeOffcollections->where('date','>=',$filDateFrom);
        }
        if ($req->filDateTo!='' || $req->filDateTo!=null) {
            $filDateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
            $writeOffcollections = $writeOffcollections->where('date','<=',$filDateTo);
        }
        $writeOffcollections = $writeOffcollections->orderBy('branchIdFk','samityIdFk','date')->get();

        $loans = DB::table('mfn_loan')
        ->whereIn('id',$writeOffcollections->pluck('loanIdFk'));

        if ($req->filSearchKey!='' || $req->filSearchKey!=null) {
            $loans = $loans->where('loanCode','like',"%$req->filSearchKey%");
        }

        $loans = $loans->select('id','loanCode','memberIdFk')->get();
        $writeOffcollections = $writeOffcollections->whereIn('loanIdFk',$loans->pluck('id'));

        $samities = DB::table('mfn_samity')
        ->whereIn('id',$writeOffcollections->pluck('samityIdFk'))
        ->select('id','code')
        ->get();

        $members = DB::table('mfn_member_information')
        ->whereIn('id',$writeOffcollections->pluck('memberIdFk'))
        ->select('id','code')
        ->get();

        if ($userBranchId==1) {
            $branchList = MicroFin::getBranchList();

        }
        else{
            $branchList = DB::table('gnr_branch')
            ->whereIn('id',$branchIdArray )
            ->orderBy('branchCode')
            ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
            ->pluck('nameWithCode', 'id')
            ->all();
        }

        $data = array(
            'writeOffcollections'   => $writeOffcollections,
            'loans'                 => $loans,
            'samities'              => $samities,
            'members'               => $members,
            'branchIdArray'               => $branchIdArray,
            'softwareDate'          => $softwareDate,
        );

        return view('microfin.loan.loanWriteOff.writeOffCollectionList',$data);
    }

    public function addWriteOffCollection($writeOffId=null){            

        if ($writeOffId!=null) {
            $encryptWriteOffId = $writeOffId;
            $writeOffId = decrypt($writeOffId);
            $writeOff = DB::table('mfn_loan_write_off')->where('id',$writeOffId)->first();
            $loan = DB::table('mfn_loan')->where('id',$writeOff->loanIdFk)->first();
            $encryptLoanId = encrypt($loan->id);
            $member = DB::table('mfn_member_information')
            ->where('id',$loan->memberIdFk)
            ->select(DB::raw("CONCAT(name, ' - ', code) AS memberName"))
            ->first();
            $softwareDate = date('d-m-Y',strtotime(MicroFin::getSoftwareDateBranchWise($loan->branchIdFk)));
            $writeOffDate = date('d-m-Y',strtotime($writeOff->date));

            $writeOffCollectionAmount = DB::table('mfn_loan_write_off_collection')
            ->where('softDel',0)
            ->where('loanIdFk',$loan->id)
            ->sum('amount');

            $writeOffEligibleAmount = $writeOff->amount - $writeOffCollectionAmount;
            $data = array(
                'loan'                      => $loan,
                'writeOff'                  => $writeOff,
                'encryptLoanId'             => $encryptLoanId,
                'encryptWriteOffId'         => $encryptWriteOffId,
                'softwareDate'              => $softwareDate,
                'member'                    => $member->memberName,
                'writeOffDate'              => $writeOffDate,
                'writeOffEligibleAmount'    => $writeOffEligibleAmount,
            );
        }
        else{
            $data = array();
        }

        return view('microfin.loan.loanWriteOff.addLoanWriteOffCollection',$data);        
    }

    public function storeWriteOffCollection(Request $req){

        if ($req->writeOffCollectionAmount=='' || $req->writeOffCollectionAmount==0) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Please insert valid Write Off Collection Amount.'
            );
            return response::json($data);
        }

        $loanId = decrypt($req->loanId);
        $loan = DB::table('mfn_loan')->where('id',$loanId)->select('memberIdFk','productIdFk','branchIdFk','samityIdFk','interestRateIndex')->first();

        $softwareDate = MicroFin::getSoftwareDateBranchWise($loan->branchIdFk);
        $softwareDate = Carbon::parse($softwareDate);

        $amount = floatval(str_replace(',', '', $req->writeOffCollectionAmount));
        $principalAmount = round($amount/(float) $loan->interestRateIndex,5);
        $interestAmount = round($amount - $principalAmount,5);

        $writeOffCollection = new MfnLoanWriteOffCollection;
        $writeOffCollection->loanWriteOffIdFk   = decrypt($req->writeOffId);
        $writeOffCollection->branchIdFk         = $loan->branchIdFk;
        $writeOffCollection->samityIdFk         = $loan->samityIdFk;
        $writeOffCollection->memberIdFk         = $loan->memberIdFk;
        $writeOffCollection->loanIdFk           = $loanId;
        $writeOffCollection->loanProductIdFk    = $loan->productIdFk;
        $writeOffCollection->date               = $softwareDate;
        $writeOffCollection->amount             = $amount;
        $writeOffCollection->principalAmount    = $principalAmount;
        $writeOffCollection->interestAmount     = $interestAmount;
        $writeOffCollection->notes              = $req->notes;
        $writeOffCollection->entryByEmpIdFk     = Auth::user()->emp_id_fk;
        $writeOffCollection->createdAt          = Carbon::now();
        $writeOffCollection->save();

        $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Data inserted successfully.'
        );
        return response::json($data);
    }

    public function updateWriteOffCollection(Request $req){

        if ($req->writeOffCollectionAmount=='' || $req->writeOffCollectionAmount==0) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Please insert valid Write Off Collection Amount.'
            );
            return response::json($data);
        }

        $writeOffCollection = MfnLoanWriteOffCollection::find($req->writeOffCollectionId);

        $interestRateIndex = DB::table('mfn_loan')->where('id',$writeOffCollection->loanIdFk)->first()->interestRateIndex;

        $amount = floatval(str_replace(',', '', $req->writeOffCollectionAmount));
        $principalAmount = round($amount/(float) $interestRateIndex,5);
        $interestAmount = round($amount - $principalAmount,5);

        $writeOffCollection->amount             = $amount;
        $writeOffCollection->principalAmount    = $principalAmount;
        $writeOffCollection->interestAmount     = $interestAmount;
        $writeOffCollection->notes              = $req->notes;
        $writeOffCollection->save();

        $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Data updated successfully.'
        );
        return response::json($data);
    }

    public function deleteLoanWriteOffCollection(Request $req){

        $writeOffCollection = MfnLoanWriteOffCollection::find($req->id);
        $writeOffCollection->softDel = 1;
        $writeOffCollection->save();

        $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Data deleted successfully.'
        );
        return response::json($data);
    }

    public function loanInfoForWaiver($loanId,$softwareDate){

        $loan = DB::table('mfn_loan')->where('id',$loanId)->first();
        $waiverDate = Carbon::parse($softwareDate)->format('Y-m-d');
        $collection = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$loan->id)->get();

        $openingBalance = DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$loanId)->get();

        foreach ($openingBalance as $key => $opb) {
            $collection->push([
                'loanIdFk'          => $opb->loanIdFk,
                'amount'            => $opb->paidLoanAmountOB,
                'principalAmount'   => $opb->principalAmountOB,
                'collectionDate'    => $opb->date
            ]);
        }

        $waivers = DB::table('mfn_loan_waivers')->where('softDel',0)->where('loanIdFk',$loanId)->get();

        foreach ($waivers as $key => $waiver) {
            $collection->push([
                'loanIdFk'          => $waiver->loanIdFk,
                'amount'            => $waiver->amount,
                'principalAmount'   => $waiver->principalAmount,
                'collectionDate'    => $waiver->date
            ]);
        }

        $rebates = DB::table('mfn_loan_rebates')->where('softDel',0)->where('loanIdFk',$loanId)->where('softDel',0)->get();

            //dd($rebates);

        foreach ($rebates as $key => $rebate) {
            $collection->push([
                'loanIdFk'          => $rebate->loanIdFk,
                'amount'            => $rebate->amount,
                'principalAmount'   => 0,
                'collectionDate'    => $rebate->date
            ]);
        }

        $amountPaid = $collection->sum('amount');
        $PrincipalAmountPaid = $collection->sum('principalAmount');

        $outStandingAmount = $loan->totalRepayAmount - $amountPaid;

        $principalAmount = $loan->loanAmount - $PrincipalAmountPaid;
        $interestAmount = $outStandingAmount - $principalAmount;

        $data = array(
            'outStandingAmount' => number_format($outStandingAmount,2),
            'principalAmount' => number_format($principalAmount,2),
            'interestAmount'  => number_format($interestAmount,2),
        );

        return $data;
    }

}