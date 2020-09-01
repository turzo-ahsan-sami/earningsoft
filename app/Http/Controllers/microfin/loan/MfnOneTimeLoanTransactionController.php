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
use App\microfin\loan\MfnLoanCollection;
use App\microfin\loan\MfnLoanSchedule;
use App\Traits\GetSoftwareDate;
use App\Http\Controllers\microfin\MicroFin;
use App\Http\Controllers\gnr\Service;
use App;

class MfnOneTimeLoanTransactionController extends controller {
  use GetSoftwareDate;

  public function index(Request $req) {

            // $softDate = GetSoftwareDate::getSoftwareDate();
    $userBranchId = Auth::user()->branchId;
    $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
    if ($userBranchId!=1) {
        $softDate = MicroFin::getSoftwareDateBranchWise($userBranchId);
    }
    else{
        $softDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);
    }

    $transactions = MfnLoanCollection::where('softDel',0)->where('amount','>',0)->where('loanTypeId',2);
    if ($userBranchId!=1) {
        $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
        $transactions = $transactions->whereIn('branchIdFk',$branchIdArray)->where('collectionDate','<=',$softDate);
        $samityList = MicroFin::getBranchWiseSamityList($branchIdArray);
                //$samityList = MicroFin::getBranchWiseSamityList($userBranchId);

                //$samityList = $this->MicroFinance->getBranchWiseSamityOptions($branchIdArray);
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

    $transactions = $transactions->orderBy('collectionDate');

    if (isset($req->filDateFrom)) {
        $transactions = $transactions->get();
    }
    else{
        $transactions = $transactions->paginate(100);
    }

    $loanRebates = DB::table('mfn_loan_rebates')
    ->whereIn('loanIdFk',$transactions->pluck('loanIdFk'))
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
        'loanRebates'       =>  $loanRebates,
        'softDate'	        =>  $softDate,
        'userBranchId'      =>  $userBranchId,
        'branchList'        =>  $branchList,
        'branchIdArray'        => $branchIdArray,
        'samityList'        =>  $samityList,
        'loanProductList'   =>  $loanProductList
    );

    return view('microfin.loan.transaction.oneTimeLoan.viewOneTimeLoanTransaction', $damageData);
}






public function index_old(Request $req) {

            // $softDate = GetSoftwareDate::getSoftwareDate();
    $userBranchId = Auth::user()->branchId;
    if ($userBranchId!=1) {
        $softDate = MicroFin::getSoftwareDateBranchWise($userBranchId);
    }
    else{
        $softDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);
    }

    $transactions = MfnLoanCollection::where('softDel',0)->where('amount','>',0)->where('loanTypeId',2);
    if ($userBranchId!=1) {
        $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
        $transactions = $transactions->whereIn('branchIdFk',$branchIdArray)->where('collectionDate','<=',$softDate);
        $samityList = MicroFin::getBranchWiseSamityList($branchIdArray);
                //$samityList = MicroFin::getBranchWiseSamityList($userBranchId);

                //$samityList = $this->MicroFinance->getBranchWiseSamityOptions($branchIdArray);
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

    $transactions = $transactions->orderBy('collectionDate');

    if (isset($req->filDateFrom)) {
        $transactions = $transactions->get();
    }
    else{
        $transactions = $transactions->paginate(100);
    }

    $loanRebates = DB::table('mfn_loan_rebates')
    ->whereIn('loanIdFk',$transactions->pluck('loanIdFk'))
    ->get();

    $bankList = $this->getBankList();
    $branchList = MicroFin::getBranchList();
    $loanProductList = MicroFin::getAllLoanProductList();

    $damageData = array(
        'bankList'          =>  $bankList,
        'transactions'      =>  $transactions,
        'loanRebates'       =>  $loanRebates,
        'softDate'          =>  $softDate,
        'userBranchId'      =>  $userBranchId,
        'branchList'        =>  $branchList,
        'samityList'        =>  $samityList,
        'loanProductList'   =>  $loanProductList
    );

    return view('microfin.loan.transaction.oneTimeLoan.viewOneTimeLoanTransaction', $damageData);
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

    return view('microfin.loan.transaction.oneTimeLoan.addOneTimeLoanTransaction', $damageData);
}

public function storeTransaction(Request $req){
    DB::beginTransaction();

    try{$rules = array(
        'member'            =>  'required',
        'transactionDate'   =>  'required',
        'loanId'       		=>  'required',
        'principalAmount'   =>  'required|numeric',
        'interestAmount'    =>  'required|numeric'
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
        'principalAmount'   =>  'Principal Amount',
        'interestAmount'    =>  'Interest Amount',
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

                // Check is Transaction exists today or not, if so then give a meaasge
        $isTransactionExits = (int) DB::table('mfn_loan_collection')->where('softDel',0)->where('amount','>',0)->where('collectionDate',$softwareDate)->where('loanIdFk',$req->loanId)->value('id');
        if ($isTransactionExits>0) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Transaction Already Exits Today.'
            );

            return response::json($data);
        }

                // store data
        $amount = (float) $req->amount;

        if ($req->paymentType=="Cash") {
	                // Cash In Hand ledger
         $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');
     }
     else{
      $ledgerId = $req->bank;
  }

  $transactionDate = Carbon::parse($req->transactionDate);
  $loan = DB::table('mfn_loan')->where('id',$req->loanId)->select('interestRateIndex','productIdFk','loanAmount','totalRepayAmount')->first();

  $principalAmount = (float) $req->principalAmount;
  $interestAmount = (float) $req->interestAmount;

  $primaryProductId = DB::table('mfn_member_information')
  ->where('id',$req->memberId)
  ->value('primaryProductId');

                /*if ($primaryProductId<1) {
                    $data = array(
                        'responseTitle' =>  'Warning!',
                        'responseText'  =>  'Something went wrong, please try again.'
                    );

                    return response::json($data);
                }*/

                $loanCollection = new MfnLoanCollection;
                $loanCollection->loanIdFk               = $req->loanId;
                $loanCollection->productIdFk		    = $loan->productIdFk;
                $loanCollection->primaryProductIdFk     = $primaryProductId;
				$loanCollection->loanTypeId				= 2; // One Time Transaction
				$loanCollection->memberIdFk				= $req->memberId;
				$loanCollection->branchIdFk				= $req->branchId;
				$loanCollection->samityIdFk				= $req->samityId;
				$loanCollection->collectionDate			= $transactionDate;
				$loanCollection->amount					= $principalAmount + $interestAmount;
				$loanCollection->principalAmount		= $principalAmount;
                $loanCollection->interestAmount         = $interestAmount;
                $loanCollection->paymentType			= $req->paymentType;
                $loanCollection->ledgerIdFk				= $ledgerId;
                $loanCollection->chequeNumber			= $req->chequeNumber;
                $loanCollection->installmentNo			= $req->installmentNo;
                $loanCollection->entryByEmployeeIdFk	= Auth::user()->emp_id_fk;
                $loanCollection->createdAt 				= Carbon::now();
                $loanCollection->save();

                $logArray = array(
                    'moduleId'  => 6,
                    'controllerName'  => 'MfnOneTimeLoanTransactionController',
                    'tableName'  => 'mfn_loan_collection',
                    'operation'  => 'insert',
                    'primaryIds'  => [DB::table('mfn_loan_collection')->max('id')]
                );
                Service::createLog($logArray);

				// mark the completed installments
                $totalCollectionAmount = MfnLoanCollection::active()->where('loanIdFk',$req->loanId)->sum('principalAmount');
                $paidAmountOB = DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$req->loanId)->sum('principalAmountOB');
                $totalCollectionAmount += $paidAmountOB;

                if ($loan->loanAmount<=$totalCollectionAmount) {
                    DB::table('mfn_loan')->where('id',$req->loanId)->update(['isLoanCompleted'=>1,'loanCompletedDate'=>$transactionDate]);
                }
                else{
                    DB::table('mfn_loan')->where('id',$req->loanId)->update(['isLoanCompleted'=>0,'loanCompletedDate'=>'0000-00-00']);
                }

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

     try{$rules = array(
        'principalAmount'   =>  'required',
        'interestAmount'    =>  'required'
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
        'principalAmount'   =>  'Principal Amount',
        'interestAmount'   =>   'Interest Amount',
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


  $loan = DB::table('mfn_loan')->where('id',$req->loanId)->select('interestRateIndex','loanAmount','totalRepayAmount')->first();

  $principalAmount = (float) $req->principalAmount;
  $interestAmount = (float) $req->interestAmount;

  $loanCollection = MfnLoanCollection::find($req->collectionId);
  $previousdata = $loanCollection;

  $loanCollection->amount					= $principalAmount + $interestAmount;
  $loanCollection->principalAmount		= $principalAmount;
  $loanCollection->interestAmount			= $interestAmount;
  $loanCollection->paymentType			= $req->paymentType;
  $loanCollection->ledgerIdFk				= $ledgerId;
  $loanCollection->chequeNumber			= $req->chequeNumber;

  $loanCollection->save();
  $logArray = array(
    'moduleId'  => 6,
    'controllerName'  => 'MfnOneTimeLoanTransactionController',
    'tableName'  => 'mfn_loan_collection',
    'operation'  => 'update',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
);
  Service::createLog($logArray);


                // mark the completed installments
  $totalCollectionAmount = MfnLoanCollection::active()->where('loanIdFk',$req->loanId)->sum('principalAmount');
  $paidAmountOB = DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$req->loanId)->sum('principalAmountOB');
  $totalCollectionAmount += $paidAmountOB;

  if ($loan->loanAmount<=$totalCollectionAmount) {
    $maxCollectionDate = DB::table('mfn_loan_collection')->where('softDel',0)->where('amount',0)->where('loanIdFk',$req->loanId)->max('collectionDate');
    $maxCollectionDate = Carbon::parse($maxCollectionDate);
    DB::table('mfn_loan')->where('id',$req->loanId)->update(['isLoanCompleted'=>1,'loanCompletedDate'=>$maxCollectionDate]);
}
else{
    DB::table('mfn_loan')->where('id',$req->loanId)->update(['isLoanCompleted'=>0,'loanCompletedDate'=>'0000-00-00']);
}

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
DB::commit();
$data = array(
    'responseTitle' =>  'Success!',
    'responseText'  =>  'Data updated successfully.'
);

return response::json($data);}
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
    DB::beginTransaction();

    try{$collection = MfnLoanCollection::find($req->id);
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
            'controllerName'  => 'MfnOneTimeLoanTransactionController',
            'tableName'  => 'mfn_loan_collection',
            'operation'  => 'delete',
            'previousData'  => $previousdata,
            'primaryIds'  => [$previousdata->id]
        );
        Service::createLog($logArray);

        $loan = DB::table('mfn_loan')->where('id',$collection->loanIdFk)->select('totalRepayAmount','loanAmount','productIdFk')->first();
        $interestCalMethodId = DB::table('mfn_loan_product_interest_rate')
        ->where('loanProductId',$loan->productIdFk)
        ->value('interestCalculationMethodId');

            // $interestCalMethodId equals 4 means the reducing method
        if ($interestCalMethodId!=4) {
                // mark the completed installments
            $totalCollectionAmount = MfnLoanCollection::active()->where('id','!=',$req->id)->where('loanIdFk',$collection->loanIdFk)->sum('principalAmount');
            $paidAmountOB = DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$collection->loanIdFk)->sum('principalAmountOB');
            $totalCollectionAmount += $paidAmountOB;

            if ($loan->loanAmount<=$totalCollectionAmount) {
                $maxCollectionDate = DB::table('mfn_loan_collection')->where('softDel',0)->where('amount',0)->where('loanIdFk',$collection->loanIdFk)->max('collectionDate');
                $maxCollectionDate = Carbon::parse($maxCollectionDate);
                DB::table('mfn_loan')->where('id',$collection->loanIdFk)->update(['isLoanCompleted'=>1,'loanCompletedDate'=>$maxCollectionDate]);
            }
            else{
                DB::table('mfn_loan')->where('id',$collection->loanIdFk)->update(['isLoanCompleted'=>0,'loanCompletedDate'=>'0000-00-00']);
            }

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


        DB::commit();
        $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Data deleted successfully.'
        );

        return response::json($data);}
        catch(\Exception $e){
         DB::rollback();
         $data = array(
          'responseTitle'  =>  'Warning!',
          'responseText'   =>  'Something went wrong. Please try again.'
      );
         return response::json($data);
     }
 }

		/*public function deleteTransaction(Request $req) {
            $collection = MfnLoanCollection::find($req->id);
            $collection->softDel = 1;
            $collection->save();

            // mark the completed installments
            $totalCollectionAmount = MfnLoanCollection::active()->where('id','!=',$req->id)->where('loanIdFk',$collection->loanIdFk)->sum('amount');

            if ($loan->totalRepayAmount<=$totalCollectionAmount) {
                $maxCollectionDate = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$collection->loanIdFk)->max('collectionDate');
                $maxCollectionDate = Carbon::parse($maxCollectionDate);
                DB::table('mfn_loan')->where('id',$collection->loanIdFk)->update(['isLoanCompleted'=>1,'loanCompletedDate'=>$maxCollectionDate]);
            }
            else{
                DB::table('mfn_loan')->where('id',$collection->loanIdFk)->update(['isLoanCompleted'=>0]);
            }

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

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data deleted successfully.'
            );

            return response::json($data);
        }*/

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
