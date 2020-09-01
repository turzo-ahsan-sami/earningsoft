<?php

namespace App\Http\Controllers\microfin\loan;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\microfin\loan\MfnPurpose;
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
use App\microfin\loan\MfnLoan;
use App\microfin\loan\MfnLoanCollection;
use App\microfin\loan\MfnLoanRebate;
use App\microfin\loan\MfnLoanSchedule;
use App\Traits\GetSoftwareDate;
use App\Http\Controllers\microfin\MicroFin;
use App\Http\Controllers\microfin\MicroFinance;
use App\Http\Controllers\gnr\Service;
use App;

class MfnLoanRebateController extends controller {
    use GetSoftwareDate;

    public function index(Request $req) {

        $userBranchId = Auth::user()->branchId;
        $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
        //$branchList = MicroFin::getBranchList();
        $loanProductList = MicroFin::getAllLoanProductList();
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
        // $softwareDate = GetSoftwareDate::getSoftwareDate();
        

        if ($userBranchId!=1) {
            //$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
            $samityList = MicroFin::getBranchWiseSamityList($branchIdArray);
            $softwareDate = MicroFin::getSoftwareDateBranchWise($userBranchId);               
        }
        else{
            $samityList = MicroFin::getAllSamityList();
            $softwareDate = null;
        }

        if (isset($req->filSamity)) {

            $loanRebates = DB::table('mfn_loan_rebates')->where('softDel',0);

            if (isset($req->filBranch)) {
                if ($req->filBranch!=null && $req->filBranch!='') {
                    $loanRebates = $loanRebates->where('branchIdFk',$req->filBranch);
                    $softwareDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);
                }
            }
            if ($req->filSamity!=null || $req->filSamity!='') {
                $loanRebates = $loanRebates->where('samityIdFk',$req->filSamity);
            }
            if ($req->filMemberCode!=null || $req->filMemberCode!='') {
                $memberId = DB::table('mfn_member_information')->where('code',$req->filMemberCode)->value('id');
                $loanRebates = $loanRebates->where('memberIdFk',$memberId);
            }
            if ($req->filLoanProduct!=null || $req->filLoanProduct!='') {
                $loanRebates = $loanRebates->where('productIdFk',$req->filLoanProduct);
            }
            if ($req->filDateFrom!=null || $req->filDateFrom!='') {
                $dateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');
                $loanRebates = $loanRebates->where('date','>=',$dateFrom);
            }
            if ($req->filDateTo!=null || $req->filDateTo!='') {
                $dateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
                $loanRebates = $loanRebates->where('date','<=',$dateTo);
            }

            $loanRebates = $loanRebates->paginate(100);

            $members = DB::table('mfn_member_information')
            ->whereIn('id',$loanRebates->pluck('memberIdFk'))
            ->select('id','name','code')
            ->get();

            $loans = DB::table('mfn_loan')
            ->whereIn('id',$loanRebates->pluck('loanIdFk'))
            ->select('id','loanCode')
            ->get();

            $samity = DB::table('mfn_samity')
            ->whereIn('id',$loanRebates->pluck('samityIdFk'))
            ->select('id','code')
            ->get();

            $employee = DB::table('hr_emp_general_info')
            ->whereIn('id',$loanRebates->pluck('entryByEmpIdFk'))
            ->select('emp_name_english as name','id')
            ->get();



            $data = array(
                'userBranchId'      => $userBranchId,
                'branchList'        => $branchList,
                'samityList'        => $samityList,
                'loanProductList'   => $loanProductList,
                'loanRebates'       => $loanRebates,
                'members'           => $members,
                'loans'             => $loans,
                'employee'          => $employee,
                'samity'            => $samity,
                'branchIdArray'            => $branchIdArray,
                'softwareDate'      => $softwareDate
            );
        }
        else{
            $data = array(
                'userBranchId'      => $userBranchId,
                'branchList'        => $branchList,
                'branchIdArray'     => $branchIdArray,
                'samityList'        => $samityList,
                'loanProductList'   => $loanProductList,
                'softwareDate'      => $softwareDate
            );
        }

        return view('microfin.loan.loanRebate.viewLoanRebates',$data);
    }








    public function index_old(Request $req) {

        $userBranchId = Auth::user()->branchId;
        $branchList = MicroFin::getBranchList();
        $loanProductList = MicroFin::getAllLoanProductList();
        // $softwareDate = GetSoftwareDate::getSoftwareDate();
        

        if ($userBranchId!=1) {
            $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
            $samityList = MicroFin::getBranchWiseSamityList($branchIdArray);
            $softwareDate = MicroFin::getSoftwareDateBranchWise($userBranchId);               
        }
        else{
            $samityList = MicroFin::getAllSamityList();
            $softwareDate = null;
        }

        if (isset($req->filSamity)) {

            $loanRebates = DB::table('mfn_loan_rebates')->where('softDel',0);

            if (isset($req->filBranch)) {
                if ($req->filBranch!=null && $req->filBranch!='') {
                    $loanRebates = $loanRebates->where('branchIdFk',$req->filBranch);
                    $softwareDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);
                }
            }
            if ($req->filSamity!=null || $req->filSamity!='') {
                $loanRebates = $loanRebates->where('samityIdFk',$req->filSamity);
            }
            if ($req->filMemberCode!=null || $req->filMemberCode!='') {
                $memberId = DB::table('mfn_member_information')->where('code',$req->filMemberCode)->value('id');
                $loanRebates = $loanRebates->where('memberIdFk',$memberId);
            }
            if ($req->filLoanProduct!=null || $req->filLoanProduct!='') {
                $loanRebates = $loanRebates->where('productIdFk',$req->filLoanProduct);
            }
            if ($req->filDateFrom!=null || $req->filDateFrom!='') {
                $dateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');
                $loanRebates = $loanRebates->where('date','>=',$dateFrom);
            }
            if ($req->filDateTo!=null || $req->filDateTo!='') {
                $dateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
                $loanRebates = $loanRebates->where('date','<=',$dateTo);
            }

            $loanRebates = $loanRebates->paginate(100);

            $members = DB::table('mfn_member_information')
            ->whereIn('id',$loanRebates->pluck('memberIdFk'))
            ->select('id','name','code')
            ->get();

            $loans = DB::table('mfn_loan')
            ->whereIn('id',$loanRebates->pluck('loanIdFk'))
            ->select('id','loanCode')
            ->get();

            $samity = DB::table('mfn_samity')
            ->whereIn('id',$loanRebates->pluck('samityIdFk'))
            ->select('id','code')
            ->get();

            $employee = DB::table('hr_emp_general_info')
            ->whereIn('id',$loanRebates->pluck('entryByEmpIdFk'))
            ->select('emp_name_english as name','id')
            ->get();

            $data = array(
                'userBranchId'      => $userBranchId,
                'branchList'        => $branchList,
                'samityList'        => $samityList,
                'loanProductList'   => $loanProductList,
                'loanRebates'       => $loanRebates,
                'members'           => $members,
                'loans'             => $loans,
                'employee'          => $employee,
                'samity'            => $samity,
                'softwareDate'      => $softwareDate
            );
        }
        else{
            $data = array(
                'userBranchId'      => $userBranchId,
                'branchList'        => $branchList,
                'samityList'        => $samityList,
                'loanProductList'   => $loanProductList,
                'softwareDate'      => $softwareDate
            );
        }

        return view('microfin.loan.loanRebate.viewLoanRebates',$data);
    }

    public function addRebate(){	

        /*$softwareDate = GetSoftwareDate::getSoftwareDateInFormat();	 
        $softDate = GetSoftwareDate::getSoftwareDate();	

		$data = array(
            'softwareDate'  =>  $softwareDate,
			'softDate'      =>  $softDate
        );*/

        $userBranchId = Auth::user()->branchId;

        $softwareDate = GetSoftwareDate::getSoftwareDateInFormat();
        $softDate = GetSoftwareDate::getSoftwareDate();

        $waiverTypeList = array(
            ''   => 'Select',
            '1'  => 'Full',
            '2'  => 'Partial'
        );


        if ($userBranchId==1) {
            $branchList = [''=>'Select'] + Microfin::getBranchList();
            $samityList = [''=>'Select'];
        }
        else{
            $branchList = [''=>'Select'];
            $samityList = [''=>'Select'] + Microfin::getBranchWiseSamityList($userBranchId);
        }

        $data = array(
            'userBranchId'      =>  $userBranchId,
            'softwareDate'      =>  $softwareDate,
            'softDate'          =>  $softDate,
            'waiverTypeList'    =>  $waiverTypeList,
            'branchList'        =>  $branchList,
            'samityList'        =>  $samityList
        );

        return view('microfin.loan.loanRebate.addLoanRebate', $data);
    }

    public function storeLoanRebate(Request $req){

        $rules = array(
            'member'        =>  'required',
            'rebateDate'    =>  'required',
            'loanId'       	=>  'required',
            'rebateAmount'  =>  'required|numeric'
        );

        $attributesNames = array(
            'member'       =>  'Member',
            'rebateDate'   =>  'Rebate Date',
            'loanId'       =>  'Loan Id',
            'rebateAmount' =>  'Rebate Amount'
        );

        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributesNames);

        if($validator->fails()){
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        
        $loan = DB::table('mfn_loan')->where('id',$req->loanId)->select('id','loanTypeId','memberIdFk','productIdFk','primaryProductIdFk','branchIdFk','samityIdFk')->first();

        $softwareDate = MicroFin::getSoftwareDateBranchWise($loan->branchIdFk);
        $rebateDate = Carbon::parse($req->rebateDate)->format('Y-m-d');

        if ($softwareDate != $rebateDate) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Rebate date and the branch date are not the same.'
            );
            return response::json($data);
        }

        // CHECK IS THERE ANY SUBSEQUENT OR TODAY TRANSACTION EXITS OR NOT, IF EXITS THEN COULD NOT DO THIS OPERATION
        $isSubsequentCollectionExits = (int) DB::table('mfn_loan_collection')
        ->where('softDel',0)
        ->where('amount','>',0)
        ->where('loanIdFk',$req->loanId)
        ->where('collectionDate','>=',$softwareDate)
        ->value('id');

        if ($isSubsequentCollectionExits>0) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Subsequent Transaction Exits!!'
            );

            return response::json($data);
        }

            // IF REBATE EXITS THAN YOU CAN NOT ENTRY ANY OTHER REBATE
        $isRebateExits = (int) DB::table('mfn_loan_rebates')->where('loanIdFk',$loan->id)->value('id');

        if ($isRebateExits>0) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Already Rebate Exits!!'
            );

            return response::json($data);
        }

        DB::beginTransaction();

        try{
            $loanRebate = new MfnLoanRebate;
            $loanRebate->loanIdFk           = $loan->id;
            $loanRebate->productIdFk        = $loan->productIdFk;
            $loanRebate->primaryProductIdFk = $loan->primaryProductIdFk;
            $loanRebate->memberIdFk         = $loan->memberIdFk;
            $loanRebate->samityIdFk         = $loan->samityIdFk;
            $loanRebate->branchIdFk         = $loan->branchIdFk;
            $loanRebate->date               = Carbon::parse($softwareDate);
            $loanRebate->amount             = (float) $req->rebateAmount;
            $loanRebate->notes              = $req->notes;
            $loanRebate->entryByEmpIdFk     = Auth::user()->emp_id_fk;
            $loanRebate->status             = 1;
            $loanRebate->save();

            $logArray = array(
                'moduleId'  => 6,
                'controllerName'  => 'MfnLoanRebateController',
                'tableName'  => 'mfn_loan_rebates',
                'operation'  => 'insert',
                'primaryIds'  => [DB::table('mfn_loan_rebates')->max('id')]
            );
            Service::createLog($logArray);

            $collectionAmount = floatval(str_replace(',', '', $req->principalAmount)) + floatval(str_replace(',', '', $req->interestAmount)) - floatval(str_replace(',', '', $req->rebateAmount));

                // Cash in hand ledger id
            $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');

            $loanCollection = new MfnLoanCollection;
            $loanCollection->loanIdFk               = $loan->id;
            $loanCollection->productIdFk            = $loan->productIdFk;
            $loanCollection->primaryProductIdFk     = $loan->primaryProductIdFk;
            $loanCollection->loanTypeId             = $loan->loanTypeId;
            $loanCollection->memberIdFk             = $loan->memberIdFk;
            $loanCollection->branchIdFk             = $loan->branchIdFk;
            $loanCollection->samityIdFk             = $loan->samityIdFk;
            $loanCollection->collectionDate         = Carbon::parse($softwareDate);
            $loanCollection->amount                 = $collectionAmount;
            $loanCollection->principalAmount        = floatval(str_replace(',', '', $req->principalAmount));
            $loanCollection->interestAmount         = floatval(str_replace(',', '', $req->interestAmount)) - floatval(str_replace(',', '', $req->rebateAmount));                
            $loanCollection->paymentType            = 'Cash';
            $loanCollection->ledgerIdFk             = $ledgerId;
            $loanCollection->transactionType        = 'Rebate';
            $loanCollection->entryByEmployeeIdFk    = Auth::user()->emp_id_fk;
            $loanCollection->createdAt              = Carbon::now();
            $loanCollection->save();

            // UPDATE LOANSTATUS AND SCHEDULE
            Microfin::updateLoanStatusNSchedule($loan->id);

            /*DB::table('mfn_loan')->where('id',$loan->id)->update(['isLoanCompleted'=>1,'loanCompletedDate'=>Carbon::parse($req->rebateDate)]);
            DB::table('mfn_loan_schedule')->where('loanIdFk',$loan->id)->update(['isCompleted'=>1,'isPartiallyPaid'=>0,'partiallyPaidAmount'=>0]);*/

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
                'responseText'   =>  'Something went wrong. Please try again.'
            );
            return response::json($data);
        }
        
    }

    public function updateLoanRebate(Request $req){

        $rules = array(
            'rebateAmount'  =>  'required|numeric'
        );


        $attributesNames = array(
            'rebateAmount' =>  'Rebate Amount'
        );

        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributesNames);

        if($validator->fails()){
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        $loanRebate = MfnLoanRebate::find($req->rebateId);
        if ($loanRebate->waiverId>0) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'This is from Waiver. You can not update it.'
            );

            return response::json($data);
        }

        if ($loanRebate->writeOffId>0) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'This is from Writeoff. You can not update it.'
            );

            return response::json($data);
        }

        $branchDate = Microfin::getSoftwareDateBranchWise($loanRebate->branchIdFk);

        if ($branchDate != $loanRebate->date) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Rebate date and the branch date are not the same.'
            );
            return response::json($data);
        }

        DB::beginTransaction();

        try {

            $previousdata = $loanRebate;

            $loan = DB::table('mfn_loan')->where('id',$loanRebate->loanIdFk)->select('id','loanTypeId','memberIdFk','productIdFk','primaryProductIdFk','branchIdFk','samityIdFk')->first();               

                // $loanRebate->amount             = (float) $req->rebateAmount;
            $loanRebate->amount             = floatval(str_replace(',', '', $req->rebateAmount));
            $loanRebate->notes              = $req->notes;
            $loanRebate->updatedByEmpIdFk   = Auth::user()->emp_id_fk;
            $loanRebate->save();

            $logArray = array(
                'moduleId'  => 6,
                'controllerName'  => 'MfnLoanRebateController',
                'tableName'  => 'mfn_loan_rebates',
                'operation'  => 'update',
                'previousData'  => $previousdata,
                'primaryIds'  => [$previousdata->id]
            );
            Service::createLog($logArray);


            $collectionAmount = floatval(str_replace(',', '', $req->principalAmount)) + floatval(str_replace(',', '', $req->interestAmount)) - floatval(str_replace(',', '', $req->rebateAmount));

                // Cash in hand ledger id
            $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');

            $loanCollection = MfnLoanCollection::where('loanIdFk',$loanRebate->loanIdFk)->where('collectionDate',$loanRebate->date)->first();


            $loanCollection->amount                 = $collectionAmount;
            $loanCollection->principalAmount        = floatval(str_replace(',', '', $req->principalAmount));
            $loanCollection->interestAmount         = floatval(str_replace(',', '', $req->interestAmount));
            $loanCollection->interestAmount         = floatval(str_replace(',', '', $req->interestAmount)) - floatval(str_replace(',', '', $req->rebateAmount));                 
            $loanCollection->save();

            // UPDATE LOANSTATUS AND SCHEDULE
            Microfin::updateLoanStatusNSchedule($loan->id);

            DB::commit();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data Updated successfully.'
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

    public function deleteLoanRebate(Request $req) {

        $loanRebate = MfnLoanRebate::find($req->id);

        $branchDate = Microfin::getSoftwareDateBranchWise($loanRebate->branchIdFk);

        if ($branchDate != $loanRebate->date) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Rebate date and the branch date are not the same.'
            );
            return response::json($data);
        }

        if ($loanRebate->waiverId>0) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'This is from waiver. You can not delete it.'
            );

            return response::json($data);
        }

        if ($loanRebate->writeOffId>0) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'This is from Writeoff. You can not delete it.'
            );

            return response::json($data);
        }

        DB::beginTransaction();

        try{
            $previousdata = $loanRebate;

            // delete relative collection
            DB::table('mfn_loan_collection')->where('loanIdFk',$loanRebate->loanIdFk)->where('collectionDate',$loanRebate->date)->where('transactionType','Rebate')->update(['softDel'=>1]);

            DB::table('mfn_loan_rebates')->where('id',$loanRebate->id)->update(['softDel'=>1]);

            // UPDATE LOANSTATUS AND SCHEDULE
            Microfin::updateLoanStatusNSchedule($loanRebate->loanIdFk);

            $logArray = array(
                'moduleId'  => 6,
                'controllerName'  => 'MfnLoanRebateController',
                'tableName'  => 'mfn_loan_rebates',
                'operation'  => 'delete',
                'previousData'  => $previousdata,
                'primaryIds'  => [$previousdata->id]
            );
            Service::createLog($logArray);

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
                'responseText'   =>  'Something went wrong. Please try again.'.$e->getLine().' '.$e->getMessage()
            );
            return response::json($data);
        }

    }

}