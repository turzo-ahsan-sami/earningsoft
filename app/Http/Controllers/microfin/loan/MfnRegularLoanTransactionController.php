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
use App\microfin\loan\MfnLoanSchedule;
use App\Traits\GetSoftwareDate;
use App\Http\Controllers\microfin\MicroFin;
use App\Http\Controllers\gnr\Service;
use App;

class MfnRegularLoanTransactionController extends controller {
    use GetSoftwareDate;

    public function index(Request $req) {

        $softDate = null;
        if (isset($req->filBranch)) {
            if ($req->filBranch!='' && $req->filBranch!=null) {
                $softDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);
            }
        }
        else{
            $softDate = GetSoftwareDate::getSoftwareDate();
        }

        $userBranchId = Auth::user()->branchId;
        $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

        $transactions = MfnLoanCollection::where('softDel',0)->where('amount','>',0)->where('loanTypeId',1);
        if ($userBranchId!=1) {
            /*$transactions = $transactions->where('branchIdFk',$userBranchId)->where('collectionDate','<=',$softDate);
            $samityList = MicroFin::getBranchWiseSamityList($userBranchId);*/
            //$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
            $transactions = $transactions->whereIn('branchIdFk',$branchIdArray)->where('collectionDate','<=',$softDate);
            $samityList = MicroFin::getBranchWiseSamityList($branchIdArray);
        }
        else{
            if (isset($req->filBranch)) {
                if ($req->filBranch!='' && $req->filBranch!=null) {
                    $transactions = $transactions->where('branchIdFk',$req->filBranch);
                    $samityList = MicroFin::getBranchWiseSamityList($req->filBranch);
                    $softDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);
                }
                else{
                    $samityList = MicroFin::getAllSamityList();
                }                    
            }
            else{
                $samityList = MicroFin::getAllSamityList();
            }
        }

        if (isset($req->filBranch)) {
            if ($req->filBranch != '' && $req->filBranch != null) {
                $transactions = $transactions->where('branchIdFk', '=', $req->filBranch);
            }
        }

        if (isset($req->filSamity)) {
            if ($req->filSamity!='' && $req->filSamity!=null) {
                $transactions = $transactions->where('samityIdFk','=',$req->filSamity);
            }
        }

        if (isset($req->filMemberCode)) {
            if ($req->filMemberCode!='' && $req->filMemberCode!=null) {
                $memberId = DB::table('mfn_member_information')->where('softDel',0)->where('code',$req->filMemberCode)->value('id');
                $transactions = $transactions->where('memberIdFk',$memberId);
            }
        }

        if (isset($req->filLoanProduct)) {
            if ($req->filLoanProduct!='' && $req->filLoanProduct!=null) {
                $transactions = $transactions->where('productIdFk','=',$req->filLoanProduct);
            }
        }

        if (isset($req->filDateFrom)) {
            if ($req->filDateFrom!='' && $req->filDateFrom!=null) {
                $dateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');
                $transactions = $transactions->where('collectionDate','>=',$dateFrom);
            }
        }
        if (isset($req->filDateTo)) {
            if ($req->filDateTo!='' && $req->filDateTo!=null) {
                $dateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
                $transactions = $transactions->where('collectionDate','<=',$dateTo);
            }
        }

        $transactions = $transactions->orderBy('collectionDate','desc');

        if (isset($req->filDateFrom)) {
            $transactions = $transactions->get();
        }
        else{
            $transactions = $transactions->paginate(100);
        }

        $loanRebates = DB::table('mfn_loan_rebates')
        ->where('softDel',0)
        ->whereIn('loanIdFk',$transactions->pluck('loanIdFk'))
        ->get();

        $loanWaivers = DB::table('mfn_loan_waivers')
        ->where('softDel',0)
        ->whereIn('loanIdFk',$transactions->pluck('loanIdFk'))
        ->get();

        $loanWriteOffs = DB::table('mfn_loan_write_off')
        ->whereIn('loanIdFk',$transactions->pluck('loanIdFk'))
        ->get();

        $openingBalances = DB::table('mfn_opening_balance_loan')
        ->whereIn('loanIdFk',$transactions->pluck('loanIdFk'))
        ->select('loanIdFk','paidLoanAmountOB')
        ->get();

        $members = DB::table('mfn_member_information')
        ->whereIn('id',$transactions->pluck('memberIdFk'))
        ->select('id','name','code')
        ->get();

        $loans = DB::table('mfn_loan')
        ->whereIn('id',$transactions->pluck('loanIdFk'))
        ->select('id','loanCode','totalRepayAmount','productIdFk','interestRateIndex','loanAmount','disbursementDate')
        ->get();

        $bankList = $this->getBankList();
        //$branchList = MicroFin::getBranchList();
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
        $loanProductList = MicroFin::getAllLoanProductList();

        $damageData = array(
            'bankList'          =>  $bankList,
            'transactions'      =>  $transactions,
            'members'           =>  $members,
            'loans'             =>  $loans,
            'loanRebates'       =>  $loanRebates,
            'loanWaivers'       =>  $loanWaivers,
            'loanWriteOffs'     =>  $loanWriteOffs,
            'openingBalances'   =>  $openingBalances,
            'softDate'	        =>  $softDate,
            'userBranchId'      =>  $userBranchId,
            'branchList'        =>  $branchList,
            'samityList'        =>  $samityList,
            'branchIdArray'        =>  $branchIdArray,
            'loanProductList'   =>  $loanProductList
        );

        return view('microfin.loan.transaction.regularLoan.viewRegularLoanTransaction', $damageData);
    }







    public function index_backup(Request $req) {

        $softDate = null;
        if (isset($req->filBranch)) {
            if ($req->filBranch!='' && $req->filBranch!=null) {
                $softDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);
            }
        }
        else{
            $softDate = GetSoftwareDate::getSoftwareDate();
        }

        $userBranchId = Auth::user()->branchId;

        $transactions = MfnLoanCollection::where('softDel',0)->where('amount','>',0)->where('loanTypeId',1);
        if ($userBranchId!=1) {
            /*$transactions = $transactions->where('branchIdFk',$userBranchId)->where('collectionDate','<=',$softDate);
            $samityList = MicroFin::getBranchWiseSamityList($userBranchId);*/
            $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
            $transactions = $transactions->whereIn('branchIdFk',$branchIdArray)->where('collectionDate','<=',$softDate);
            $samityList = MicroFin::getBranchWiseSamityList($branchIdArray);
        }
        else{
            if (isset($req->filBranch)) {
                if ($req->filBranch!='' && $req->filBranch!=null) {
                    $transactions = $transactions->where('branchIdFk',$req->filBranch);
                    $samityList = MicroFin::getBranchWiseSamityList($req->filBranch);
                    $softDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);
                }
                else{
                    $samityList = MicroFin::getAllSamityList();
                }                    
            }
            else{
                $samityList = MicroFin::getAllSamityList();
            }
        }

        if (isset($req->filSamity)) {
            if ($req->filSamity!='' && $req->filSamity!=null) {
                $transactions = $transactions->where('samityIdFk','=',$req->filSamity);
            }
        }

        if (isset($req->filMemberCode)) {
            if ($req->filMemberCode!='' && $req->filMemberCode!=null) {
                $memberId = DB::table('mfn_member_information')->where('softDel',0)->where('code',$req->filMemberCode)->value('id');
                $transactions = $transactions->where('memberIdFk',$memberId);
            }
        }

        if (isset($req->filLoanProduct)) {
            if ($req->filLoanProduct!='' && $req->filLoanProduct!=null) {
                $transactions = $transactions->where('productIdFk','=',$req->filLoanProduct);
            }
        }

        if (isset($req->filDateFrom)) {
            if ($req->filDateFrom!='' && $req->filDateFrom!=null) {
                $dateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');
                $transactions = $transactions->where('collectionDate','>=',$dateFrom);
            }
        }
        if (isset($req->filDateTo)) {
            if ($req->filDateTo!='' && $req->filDateTo!=null) {
                $dateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
                $transactions = $transactions->where('collectionDate','<=',$dateTo);
            }
        }

        $transactions = $transactions->orderBy('collectionDate','desc');

        if (isset($req->filDateFrom)) {
            $transactions = $transactions->get();
        }
        else{
            $transactions = $transactions->paginate(100);
        }

        $loanRebates = DB::table('mfn_loan_rebates')
        ->where('softDel',0)
        ->whereIn('loanIdFk',$transactions->pluck('loanIdFk'))
        ->get();

        $loanWaivers = DB::table('mfn_loan_waivers')
        ->where('softDel',0)
        ->whereIn('loanIdFk',$transactions->pluck('loanIdFk'))
        ->get();

        $loanWriteOffs = DB::table('mfn_loan_write_off')
        ->whereIn('loanIdFk',$transactions->pluck('loanIdFk'))
        ->get();

        $openingBalances = DB::table('mfn_opening_balance_loan')
        ->whereIn('loanIdFk',$transactions->pluck('loanIdFk'))
        ->select('loanIdFk','paidLoanAmountOB')
        ->get();

        $members = DB::table('mfn_member_information')
        ->whereIn('id',$transactions->pluck('memberIdFk'))
        ->select('id','name','code')
        ->get();

        $loans = DB::table('mfn_loan')
        ->whereIn('id',$transactions->pluck('loanIdFk'))
        ->select('id','loanCode','totalRepayAmount','productIdFk','interestRateIndex','loanAmount','disbursementDate')
        ->get();

        $bankList = $this->getBankList();
        $branchList = MicroFin::getBranchList();
        $loanProductList = MicroFin::getAllLoanProductList();

        $damageData = array(
            'bankList'          =>  $bankList,
            'transactions'      =>  $transactions,
            'members'           =>  $members,
            'loans'             =>  $loans,
            'loanRebates'       =>  $loanRebates,
            'loanWaivers'       =>  $loanWaivers,
            'loanWriteOffs'     =>  $loanWriteOffs,
            'openingBalances'   =>  $openingBalances,
            'softDate'          =>  $softDate,
            'userBranchId'      =>  $userBranchId,
            'branchList'        =>  $branchList,
            'samityList'        =>  $samityList,
            'loanProductList'   =>  $loanProductList
        );

        return view('microfin.loan.transaction.regularLoan.viewRegularLoanTransaction', $damageData);
    }

    public function addTransaction(){	

        $softwareDate = GetSoftwareDate::getSoftwareDateInFormat();	 
        $softDate = GetSoftwareDate::getSoftwareDate();	

        $bankList = $this->getBankList();

        $damageData = array(
            'bankList'      =>  $bankList,
            'softwareDate'  =>  $softwareDate,
            'softDate'      =>  $softDate
        );

        return view('microfin/loan/transaction/regularLoan/addRegularLoanTransaction', ['damageData' => $damageData]);
    }

    public function storeTransaction(Request $req){
        DB::beginTransaction();

        try{            
            $loan = DB::table('mfn_loan')->where('id',$req->loanId)->first();

            $paidAmount = DB::table('mfn_loan_collection')
            ->where('loanIdFk', $loan->id)
            ->where('softDel', 0)
            ->sum('amount');

            $paidAmount += DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$loan->id)->sum('paidLoanAmountOB');
			$paidAmount += DB::table('mfn_loan_waivers')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('amount');
			$paidAmount += DB::table('mfn_loan_write_off')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('amount');
			$paidAmount += DB::table('mfn_loan_rebates')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('amount');


            $currentOutStanding = $loan->totalRepayAmount - $paidAmount;

            if ($req->amount > $currentOutStanding) {
                return response::json(array('errors' => 'Given amount is greater than current outstanding!'));
            }


            // dd($req);
            $rules = array(
                'member'            =>  'required',
                'transactionDate'   =>  'required',
                'loanId'       		=>  'required',
                'amount'            =>  'required|numeric'
            );

            if ($req->paymentType=='Bank') {
                $rules = $rules + array(
                    'bank'          =>  'required',
                    'chequeNumber'  =>  'required'
                );
            }

            $attributesNames = array(
                'member'            =>  'Member',
                'transactionDate'   =>  'Transaction Date',
                'loanId'       		=>  'Loan Id',
                'amount'            =>  'Amount',
                'bank'              =>  'Bank',
                'chequeNumber'      =>  'Cheque Number'
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()){
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            }
            else {


                $softwareDate = GetSoftwareDate::getSoftwareDate();
                $transactionDate = Carbon::parse($softwareDate);

                // Check is Transaction exists today or not, if so then give a meaasge
                $isTransactionExits = (int) DB::table('mfn_loan_collection')->where('softDel',0)->where('amount','>',0)->where('collectionDate',$softwareDate)->where('loanIdFk',$req->loanId)->value('id');
                if ($isTransactionExits>0) {
                    $data = array(
                        'responseTitle' =>  'Warning!',
                        'responseText'  =>  'Transaction Already Exits Today.'
                    );

                    return response::json($data);
                }

                $interestCalMethodId = DB::table('mfn_loan_product_interest_rate')
                ->where('loanProductId',$loan->productIdFk)
                ->value('interestCalculationMethodId');

                // $interestCalMethodId equals 4 means the reducing method
                if ($interestCalMethodId==4) {
                    // Check is Subsequent Transaction exists after this date or not, if so then give a meaasge
                    $isTransactionExits = (int) DB::table('mfn_loan_collection')->where('softDel',0)->where('collectionDate','>',$softwareDate)->where('loanIdFk',$req->loanId)->value('id');
                    if ($isTransactionExits>0) {
                        $data = array(
                            'responseTitle' =>  'Warning!',
                            'responseText'  =>  'Subsequent Transaction Already Exits.'
                        );

                        return response::json($data);
                    }
                }

            // Store Data

                $amount = (float) $req->amount;

                if ($amount<=0) {
                    $data = array(
                        'responseTitle' =>  'Warning!',
                        'responseText'  =>  'Please give valuable amount.'
                    );

                    return response::json($data);
                }

                if ($req->paymentType=="Cash") {
	                // Cash In Hand ledger
                    $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');
                }
                else{
                    $ledgerId = $req->bank;
                }

                // $interestCalMethodId equals 4 means the reducing method
                if ($interestCalMethodId==4) {
                    $loanCollections = DB::table('mfn_loan_collection')
                    ->where('softDel',0)
                    ->where('loanIdFk',$loan->id)
                    ->select('collectionDate','principalAmount')
                    ->get();

                    if (count($loanCollections)>0) {
                        $lastTransactionDate = Carbon::parse($loanCollections->max('collectionDate'));
                        $remainingPrincipal = $loan->loanAmount - $loanCollections->sum('principalAmount');
                    }
                    else{
                        $lastTransactionDate = Carbon::parse($loan->disbursementDate);
                        $remainingPrincipal = $loan->loanAmount;
                    }

                    $daysDiff = $transactionDate->diffInDays($lastTransactionDate);
                    $interestAmount = round($remainingPrincipal * $daysDiff * $loan->interestRateIndex);
                    $principalAmount = $amount - $interestAmount;
                }
                else{
                    $principalAmount = round($amount/(float) $loan->interestRateIndex,5);
                    $interestAmount = round($amount - $principalAmount,5);
                }

                $transactionDate = Carbon::parse($req->transactionDate);

                $loanCollection = new MfnLoanCollection;
                $loanCollection->loanIdFk               = $req->loanId;
                $loanCollection->productIdFk            = $loan->productIdFk;
                $loanCollection->primaryProductIdFk		= $loan->primaryProductIdFk;
                $loanCollection->loanTypeId				= 1;
                $loanCollection->memberIdFk				= $loan->memberIdFk;
                $loanCollection->branchIdFk				= $loan->branchIdFk;
                $loanCollection->samityIdFk				= $loan->samityIdFk;
                $loanCollection->collectionDate			= $transactionDate;
                $loanCollection->amount					= $amount;
                $loanCollection->principalAmount		= $principalAmount;
                $loanCollection->interestAmount			= $interestAmount;                
                $loanCollection->paymentType			= $req->paymentType;
                $loanCollection->ledgerIdFk				= $ledgerId;
                $loanCollection->chequeNumber			= $req->chequeNumber;
                $loanCollection->installmentNo			= $req->installmentNo;
                $loanCollection->entryByEmployeeIdFk	= Auth::user()->emp_id_fk;
                $loanCollection->createdAt 				= Carbon::now();
                $loanCollection->save();

                $logArray = array(
                    'moduleId'  => 6,
                    'controllerName'  => 'MfnRegularLoanTransactionController',
                    'tableName'  => 'mfn_loan_collection',
                    'operation'  => 'insert',
                    'primaryIds'  => [DB::table('mfn_loan_collection')->max('id')]
                );
                Service::createLog($logArray);

                $totalCollectionAmount = MfnLoanCollection::active()->where('loanIdFk',$req->loanId)->sum('amount');

                $paidAmountOB = DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$req->loanId)->sum('paidLoanAmountOB');
                $totalCollectionAmount += $paidAmountOB;

                if ($loan->totalRepayAmount<=$totalCollectionAmount) {
                    $maxCollectionDate = DB::table('mfn_loan_collection')->where('softDel',0)->where('amount','>',0)->where('loanIdFk',$req->loanId)->max('collectionDate');
                    DB::table('mfn_loan')->where('id',$req->loanId)->update(['isLoanCompleted'=>1,'loanCompletedDate'=>$maxCollectionDate]);
                }
                else{
                    DB::table('mfn_loan')->where('id',$req->loanId)->update(['isLoanCompleted'=>0,'loanCompletedDate'=>'0000-00-00']);
                }

                if ($interestCalMethodId!=4) {
                    // mark the completed installments
                    $shedules = MfnLoanSchedule::active()->where('loanIdFk',$req->loanId)->get();

                    foreach ($shedules as $key => $shedule) {
                        if ($totalCollectionAmount>=$shedule->installmentAmount) {
                            $shedule->isCompleted = 1;
                            $shedule->isPartiallyPaid = 0;
                            $shedule->partiallyPaidAmount = 0;
                            $shedule->save();
                        }
                        elseif ($totalCollectionAmount>0) {
                            $shedule->isCompleted = 0;
                            $shedule->isPartiallyPaid = 1;
                            $shedule->partiallyPaidAmount = $totalCollectionAmount;
                            $shedule->save();
                        }
                        else{
                            $shedule->isCompleted = 0;
                            $shedule->isPartiallyPaid = 0;
                            $shedule->partiallyPaidAmount = 0;
                            $shedule->save();
                        }

                        $totalCollectionAmount = $totalCollectionAmount - $shedule->installmentAmount;
                    }
                }


            }
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

    public function updateTransaction(Request $req){
        DB::beginTransaction();
        try{ 
            $loanCollection = MfnLoanCollection::find($req->collectionId);
            $previousData = $loanCollection;

            $amount = floatval(str_replace(',', '', $req->amount));

            $loan = DB::table('mfn_loan')->where('id',$loanCollection->loanIdFk)->select('id','productIdFk','interestRateIndex','totalRepayAmount')->first();

            $paidAmount = DB::table('mfn_loan_collection')
            ->where('loanIdFk', $loan->id)
            ->where('id','!=' ,$loanCollection->id)
            ->where('softDel', 0)
            ->sum('amount');

			$paidAmount += DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$loan->id)->sum('paidLoanAmountOB');
			$paidAmount += DB::table('mfn_loan_waivers')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('amount');
			$paidAmount += DB::table('mfn_loan_write_off')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('amount');
			$paidAmount += DB::table('mfn_loan_rebates')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('amount');

            $paidAmount += $amount;

            $currentOutStanding = $loan->totalRepayAmount - $paidAmount;

            if ($currentOutStanding<0) {
                return response::json(array('errors' => 'Given amount is greater than current outstanding!'));
            }            

            $paidAmount = DB::table('mfn_loan_collection')
            ->where('loanIdFk', $loan->id)
            ->where('softDel', 0)
            ->where('id','!=', $loanCollection->id)
            ->sum('amount');

            $currentOutStanding = $loan->totalRepayAmount - $paidAmount;

            if ($req->amount > $currentOutStanding) {
                return response::json(array('errors' => 'Given amount is greater than current outstanding!'));
            }

            // IF THE LOAN IS IN REBATE, WAIVER OR WRITEOFF LIST, THEN NO COLLECTION WILL BE EDITABLE
            $isLoanReabted = (int) DB::table('mfn_loan_rebates')->where('loanIdFk',$loanCollection->loanIdFk)->value('id');
            $isLoanWaivered = (int) DB::table('mfn_loan_waivers')->where('loanIdFk',$loanCollection->loanIdFk)->value('id');
            $isLoanWriteOffed = (int) DB::table('mfn_loan_write_off')->where('loanIdFk',$loanCollection->loanIdFk)->value('id');

            if (max($isLoanReabted,$isLoanWaivered,$isLoanWriteOffed) > 0) {
                $msgText = '';
                if ($isLoanReabted>0) {
                    $msgText = 'Rebate';
                }
                elseif ($isLoanWaivered>0) {
                    $msgText = 'Waiver';
                }
                else{
                    $msgText = 'Write Off';
                }
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'This Loan is in '.$msgText.' List. You can not update it.'
                );

                return response::json($data);
            }

            // dd($req, $installmentNo, $amountPaiedBySchedulePrevious, $totalRepayAmount, $currentOutStanding);

            $rules = array(
                'member'            =>  'required',
                'transactionDate'   =>  'required',
                'loanId'            =>  'required',
                'amount'            =>  'required|numeric'
            );

            if ($req->paymentType=='Bank') {
                $rules = $rules + array(
                    'bank'          =>  'required',
                    'chequeNumber'  =>  'required'
                );
            }

            $attributesNames = array(
                'member'            =>  'Member',
                'transactionDate'   =>  'Transaction Date',
                'loanId'            =>  'Loan Id',
                'amount'            =>  'Amount',
                'bank'              =>  'Bank',
                'chequeNumber'      =>  'Cheque Number'
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {

                if ($req->paymentType=="Cash") {
                    // Cash In Hand ledger
                    $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');
                }
                else{
                    $ledgerId = $req->bank;
                }



               /* $installment = DB::table('mfn_loan_schedule')->where('softDel',0)->where('loanIdFk',$req->loanId)->where('installmentSl',$req->installmentNo)->select('id','installmentAmount')->first();
                $installmentDueAmount = (float) $installment->installmentAmount - $amount;
                
                $isPartial = ($installmentDueAmount)>0 ? 1:0;*/

                $interestCalMethodId = DB::table('mfn_loan_product_interest_rate')
                ->where('loanProductId',$loan->productIdFk)
                ->value('interestCalculationMethodId');

                // $interestCalMethodId equals 4 means the reducing method
                if ($interestCalMethodId==4) {
                    // Check is Subsequent Transaction exists after this date or not, if so then give a meaasge
                    $isTransactionExits = (int) DB::table('mfn_loan_collection')->where('softDel',0)->where('collectionDate','>',$loanCollection->collectionDate)->where('loanIdFk',$loan->id)->value('id');
                    if ($isTransactionExits>0) {
                        $data = array(
                            'responseTitle' =>  'Warning!',
                            'responseText'  =>  'Subsequent Transaction Already Exits.'
                        );

                        return response::json($data);
                    }
                }


                // $interestCalMethodId equals 4 means the reducing method
                if ($interestCalMethodId==4) {
                    $interestAmount = floatval(str_replace(',', '', $req->interest));
                    $principalAmount = $amount - $interestAmount;
                }
                else{
                    $principalAmount = round($amount/(float) $loan->interestRateIndex,5);
                    $interestAmount = round($amount - $principalAmount,5);
                }

                
                /*$loanCollection->collectionDate           = Carbon::parse($req->transactionDate);*/
                $loanCollection->amount                 = $amount;
                $loanCollection->principalAmount        = $principalAmount;
                $loanCollection->interestAmount         = $interestAmount;
                $loanCollection->paymentType            = $req->paymentType;
                $loanCollection->ledgerIdFk             = $ledgerId;
                $loanCollection->chequeNumber           = $req->chequeNumber;           
                /*$loanCollection->installmentDueAmount = $installmentDueAmount;*/
                /*$loanCollection->isPartial                = $isPartial;*/
                $loanCollection->save();

                $logArray = array(
                    'moduleId'  => 6,
                    'controllerName'  => 'MfnRegularLoanTransactionController',
                    'tableName'  => 'mfn_loan_collection',
                    'operation'  => 'update',
                    'previousData'  => $previousData,
                    'primaryIds'  => [$previousData->id]
                );
                Service::createLog($logArray);

                // CREATE A LOG
                /*Service::createLog('microfinance','mfn_loan_collection','update',[$loanCollection->id],$previousData);*/

                $totalCollectionAmount = MfnLoanCollection::active()->where('loanIdFk',$loanCollection->loanIdFk)->sum('amount');

                $paidAmountOB = DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$loanCollection->loanIdFk)->sum('paidLoanAmountOB');
                $totalCollectionAmount += $paidAmountOB;                

                if ($loan->totalRepayAmount<=$totalCollectionAmount) {
                    $maxCollectionDate = DB::table('mfn_loan_collection')->where('softDel',0)->where('amount','>',0)->where('loanIdFk',$req->loanId)->max('collectionDate');
                    $maxCollectionDate = Carbon::parse($maxCollectionDate);
                    DB::table('mfn_loan')->where('id',$req->loanId)->update(['isLoanCompleted'=>1,'loanCompletedDate'=>$maxCollectionDate]);
                }
                else{

                    $updateLoanStatus = DB::table('mfn_loan')->where('id',$req->loanId)->update(['isLoanCompleted'=>0, 'loanCompletedDate'=>'0000-00-00']);
                    // dd($loan->totalRepayAmount, $totalCollectionAmount, $updateLoanStatus, $req->loanId);
                }

                if ($interestCalMethodId!=4) {
                    // mark the completed installments

                    $shedules = MfnLoanSchedule::active()->where('loanIdFk',$loanCollection->loanIdFk)->get();

                    foreach ($shedules as $key => $shedule) {
                        if ($totalCollectionAmount>=$shedule->installmentAmount) {
                            $shedule->isCompleted = 1;
                            $shedule->isPartiallyPaid = 0;
                            $shedule->partiallyPaidAmount = 0;
                            $shedule->save();
                        }
                        elseif ($totalCollectionAmount>0) {
                            $shedule->isCompleted = 0;
                            $shedule->isPartiallyPaid = 1;
                            $shedule->partiallyPaidAmount = $totalCollectionAmount;
                            $shedule->save();
                        }
                        else{
                            $shedule->isCompleted = 0;
                            $shedule->isPartiallyPaid = 0;
                            $shedule->partiallyPaidAmount = 0;
                            $shedule->save();
                        }

                        $totalCollectionAmount = $totalCollectionAmount - $shedule->installmentAmount;
                    }
                }

            }
            DB::commit();
            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data updated successfully.'
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

    public function deleteTransaction(Request $req) {

        $collection = MfnLoanCollection::find($req->id);
        $previousdata = $collection;

        // IF THE LOAN IS IN REBATE, WAIVER OR WRITEOFF LIST, THEN NO COLLECTION WILL BE EDITABLE
        $isLoanReabted = (int) DB::table('mfn_loan_rebates')->where('loanIdFk',$collection->loanIdFk)->value('id');
        $isLoanWaivered = (int) DB::table('mfn_loan_waivers')->where('loanIdFk',$collection->loanIdFk)->value('id');
        $isLoanWriteOffed = (int) DB::table('mfn_loan_write_off')->where('loanIdFk',$collection->loanIdFk)->value('id');

        if (max($isLoanReabted,$isLoanWaivered,$isLoanWriteOffed) > 0) {
            $msgText = '';
            if ($isLoanReabted>0) {
                $msgText = 'Rebate';
            }
            elseif ($isLoanWaivered>0) {
                $msgText = 'Waiver';
            }
            else{
                $msgText = 'Write Off';
            }
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'This Loan is in '.$msgText.' List. You can not update it.'
            );

            return response::json($data);
        }

        if ($collection->transactionType=='Rebate') {
            $data = array(
                'responseTitle'  =>  'Warning!',
                'responseText'   =>  'You can not delete this transaction, because it is generated with the rebate. If you want to delete, you need to delete Rebate.'
            );
            return response::json($data);
        }


        DB::beginTransaction();

        try{

            if ($collection->isFromAutoProcess==1) {
                $collection->amount = 0;
                $collection->principalAmount = 0;
                $collection->interestAmount = 0;
                $collection->isPartial = 0;
                $collection->isAuthorized = 1;
            }
            else{
                $collection->softDel = 1;                
            }
            $collection->save();

            $logArray = array(
                'moduleId'  => 6,
                'controllerName'  => 'MfnRegularLoanTransactionController',
                'tableName'  => 'mfn_loan_collection',
                'operation'  => 'delete',
                'previousData'  => $previousdata,
                'primaryIds'  => [$previousdata->id]
            );
            Service::createLog($logArray);

            $loan = DB::table('mfn_loan')->where('id',$collection->loanIdFk)->select('totalRepayAmount','productIdFk')->first();
            $interestCalMethodId = DB::table('mfn_loan_product_interest_rate')
            ->where('loanProductId',$loan->productIdFk)
            ->value('interestCalculationMethodId');

            $totalCollectionAmount = MfnLoanCollection::active()->where('id','!=',$req->id)->where('loanIdFk',$collection->loanIdFk)->sum('amount');

            $paidAmountOB = DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$collection->loanIdFk)->sum('paidLoanAmountOB');
            $totalCollectionAmount += $paidAmountOB;

            if ($loan->totalRepayAmount<=$totalCollectionAmount) {
                $maxCollectionDate = DB::table('mfn_loan_collection')->where('softDel',0)->where('amount','>',0)->where('loanIdFk',$collection->loanIdFk)->max('collectionDate');
                $maxCollectionDate = Carbon::parse($maxCollectionDate);
                DB::table('mfn_loan')->where('id',$collection->loanIdFk)->update(['isLoanCompleted'=>1,'loanCompletedDate'=>$maxCollectionDate]);
            }
            else{
                $deleteTransaction = DB::table('mfn_loan')->where('id',$collection->loanIdFk)->update(['isLoanCompleted'=>0,'loanCompletedDate'=>'0000-00-00']);
                    // dd($deleteTransaction);
            }     

            // $interestCalMethodId equals 4 means the reducing method
            if ($interestCalMethodId!=4) {
                // mark the completed installments


                $shedules = MfnLoanSchedule::active()->where('loanIdFk',$collection->loanIdFk)->get();

                foreach ($shedules as $key => $shedule) {
                    if ($totalCollectionAmount>=$shedule->installmentAmount) {
                        $shedule->isCompleted = 1;
                        $shedule->isPartiallyPaid = 0;
                        $shedule->partiallyPaidAmount = 0;
                        $shedule->save();
                    }
                    elseif ($totalCollectionAmount>0) {
                        $shedule->isCompleted = 0;
                        $shedule->isPartiallyPaid = 1;
                        $shedule->partiallyPaidAmount = $totalCollectionAmount;
                        $shedule->save();
                    }
                    else{
                        $shedule->isCompleted = 0;
                        $shedule->isPartiallyPaid = 0;
                        $shedule->partiallyPaidAmount = 0;
                        $shedule->save();
                    }

                    $totalCollectionAmount = $totalCollectionAmount - $shedule->installmentAmount;
                }
            }

            // dd($deleteTransaction, $collection->loanIdFk);
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

    public function getBankList(){

        $userProjectId  = Auth::user()->project_id_fk;
        $userBranchId   = Auth::user()->branchId;

        $bankFromLaser = DB::table('acc_account_ledger')
        ->where('accountTypeId',5)
        ->select('projectBranchId','id')
        ->get();

        $bankList = array();
        foreach ($bankFromLaser as $key => $bank) {
            $projectBranchString = str_replace(['"','[',']'], '', $bank->projectBranchId);
            $projectBranchArray = explode(',', $projectBranchString);

            foreach ($projectBranchArray as $key => $projectBranch) {
                $result = explode(':', $projectBranch);
                $bankProjectId  = $result[0];
                $bankBranchId   = $result[1];

                if (($bankProjectId==0 && $bankBranchId==0) || ($bankBranchId==$userBranchId) || ($userProjectId==$bankProjectId && $bankBranchId==0)) {
                    array_push($bankList, $bank->id);

                }
            }                
        }

        $resultedBankList = DB::table('acc_account_ledger')
        ->whereIn('id',$bankList)
        ->where('id','!=',350)
        ->select('name','id','code')
        ->get();
        return $resultedBankList;
    }

}