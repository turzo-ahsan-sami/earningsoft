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

class MfnDueLoanTransactionController extends controller {
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

    $transactions = MfnLoanCollection::where('softDel',0)->where('amount','>',0)->where('transactionType', '=', 'Over Due');
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
                'samityList'        =>  $samityList,
                'branchIdArray'        =>  $branchIdArray,
                'loanProductList'   =>  $loanProductList
            );
            
            return view('microfin.loan.transaction.dueLoan.viewDueLoanTransaction', $damageData);
        }




        public function index_old(Request $req) {

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

            $transactions = MfnLoanCollection::where('softDel',0)->where('amount','>',0)->where('transactionType', '=', 'Over Due');
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
            
            return view('microfin.loan.transaction.dueLoan.viewDueLoanTransaction', $damageData);
        }

        public function addTransaction(){

            $dueLoanList = array();
            $inCompleteLoansSchedule = array();
            $branchId = Auth::user()->branchId; 
            // $branchId = 2;

            $softwareDate = GetSoftwareDate::getSoftwareDateInFormat();	 
            $softDate = GetSoftwareDate::getSoftwareDate();	

            $bankList = $this->getBankList();

            // GET THE DUE LOAN LIST FOR THE SPECIFIC BRANCH
            $loanIdFk = DB::table('mfn_loan')
            ->where([['branchIdFk', $branchId], ['loanCompletedDate', '=', '0000-00-00']])
            ->pluck('id')
            ->toArray();

            foreach ($loanIdFk as $key => $loanIdFkValue) {
                $inCompleteLoansSchedule[$loanIdFkValue] = DB::select(" SELECT loanIdFk, MAX(scheduleDate) AS maxScheduleDate FROM mfn_loan_schedule WHERE loanIdFk='$loanIdFkValue'");
            }

            foreach ($inCompleteLoansSchedule as $keyL => $inCompleteLoansScheduleValue) {
                foreach ($inCompleteLoansScheduleValue as $key => $value) {
                    if ($value->maxScheduleDate < $softDate) {
                        $memberId = DB::table('mfn_loan')
                        ->where('id', $value->loanIdFk)
                        ->select('memberIdFk', 'branchIdFk', 'samityIdFk')
                        ->get();

                        foreach ($memberId as $key => $memberIdValue) {
                            $memberName = DB::table('mfn_member_information')
                            ->where('id', $memberIdValue->memberIdFk)
                            ->pluck('name')
                            ->toArray();

                            $memberCode = DB::table('mfn_member_information')
                            ->where('id', $memberIdValue->memberIdFk)
                            ->pluck('code')
                            ->toArray();

                            $branchName = DB::table('gnr_branch')
                            ->where('id', $memberIdValue->branchIdFk)
                            ->pluck('name')
                            ->toArray();

                            $branchId = DB::table('gnr_branch')
                            ->where('id', $memberIdValue->branchIdFk)
                            ->pluck('id')
                            ->toArray();

                            $samityName = DB::table('mfn_samity')
                            ->where('id', $memberIdValue->samityIdFk)
                            ->pluck('name')
                            ->toArray();

                            $samityId = DB::table('mfn_samity')
                            ->where('id', $memberIdValue->samityIdFk)
                            ->pluck('id')
                            ->toArray();

                            $dueLoanList[$keyL] = [
                                'id'              => $value->loanIdFk,
                                'maxScheduleDate' => $value->maxScheduleDate,
                                'memberId'        => $memberIdValue->memberIdFk,
                                'memberName'      => $memberName[0],
                                'memberCode'      => $memberCode[0],
                                'branchName'      => $branchName[0],
                                'branchId'        => $branchId[0],
                                'samityName'      => $samityName[0],
                                'samityId'        => $samityId[0]
                            ];
                        }
                    }
                }
            }

            // dd($dueLoanList);
            

            $damageData = array(
                'bankList'      =>  $bankList,
                'softwareDate'  =>  $softwareDate,
                'softDate'      =>  $softDate,
                'dueLoanList'   =>  $dueLoanList  
            );
            
            // dd($branchId, $damageData, $inCompleteLoansSchedule, $dueLoanList);

            return view('microfin/loan/transaction/dueLoan/addDueLoanTransaction', ['damageData' => $damageData]);
        }
        
        public function getTransactionValue (Request $req) {
            // $memberId = [$req->id];

            $loanIdFk = DB::table('mfn_loan')
            ->where([['memberIdFk', $req->id], ['loanCompletedDate', '=', '0000-00-00']])
            ->select('id', 'loanCode')
            ->get();


            return response()->json($loanIdFk);
        }

        public function getDueOutstandingValue (Request $req) {
            $dueOutstanding=[];
            $loan_id = $req->loan_id;
            $amountGiven  = $req->amount;

            if ($amountGiven != '') {
                $collectionAmount = DB::table('mfn_loan_collection')
                ->where([['loanIdFk', $loan_id], ['softDel', '=', 0]])
                ->sum('amount');

                $installmentNum = DB::table('mfn_loan_schedule')
                ->where('loanIdFk', $loan_id)
                ->min('installmentSl');

                $repayAmount = DB::table('mfn_loan')
                ->where([['id', $loan_id]])
                ->sum('totalRepayAmount');

                $amount = ($repayAmount - $collectionAmount) - $amountGiven;

                $dueOutstanding = [
                    'number' => $installmentNum+1,
                    'amount'  => $amount
                ];

                return response()->json($dueOutstanding);
            }
            else {
                $dueOutstanding = [
                    'number' => 'N/A',
                    'amount'  => 'N/A'
                ];

                return response()->json($dueOutstanding);
            }
            
        }

        public function storeTransaction(Request $req){
            $searchMember = $req->searchMember;
            
            // STORE DATA IN COLLECTION TABLE
            $productIdInfo = DB::table('mfn_loan')
            ->where('id', $req->loanId)
            ->pluck('productIdFk')
            ->toArray();

            $primaryProductIdInfo = DB::table('mfn_loan')
            ->where('id', $req->loanId)
            ->pluck('primaryProductIdFk')
            ->toArray();

            $interestInfo = DB::table('mfn_loan')
            ->where('id', $req->loanId)
            ->pluck('interestRateIndex')
            ->toArray();

            $loanTypeId = DB::table('mfn_loan')
            ->where('id', $req->loanId)
            ->pluck('loanTypeId')
            ->toArray();

            // $interestAmount = ($req->amount * $interestInfo[0]) / 100 ;

            // $principalAmount = $req->amount - $interestAmount;

            list($memberId, $branchId, $samityId) = explode('-', $searchMember);

            $interestCalMethodId = DB::table('mfn_loan_product_interest_rate')
            ->where('loanProductId',$productIdInfo[0])
            ->value('interestCalculationMethodId');

            // $interestCalMethodId equals 4 means the reducing method
            if ($interestCalMethodId==4) {
                // Check is Subsequent Transaction exists after this date or not, if so then give a meaasge
                $isTransactionExits = (int) DB::table('mfn_loan_collection')->where('softDel',0)->where('collectionDate','>',$softwareDate)->where('loanIdFk',$req->loanId)->value('id');
                if ($isTransactionExits>0) {
                    $notification = array(
                        'message' => 'Transaction Already Exits Today!',
                        'alert-type' => 'Warning'
                    );

                    return redirect()->back()->with($notification);
                }
            }


            // $interestCalMethodId equals 4 means the reducing method
            if ($interestCalMethodId==4) {
                $loanCollections = DB::table('mfn_loan_collection')
                ->where('softDel',0)
                ->where('loanIdFk',$req->loanId)
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
                $interestAmount = round($remainingPrincipal * $daysDiff * $interestInfo[0]);
                $principalAmount = $req->amount - $interestAmount;
            }
            else{
                $principalAmount = round($req->amount/(float) $interestInfo[0],2);
                $interestAmount = round($req->amount - $principalAmount,2);
            }

            if ($req->paymentType=="Cash") {
                // Cash In Hand ledger
                $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');
            }
            else{
                $ledgerId = $req->bank;
            }

            // dd($req, $principalAmount, $interestAmount, $memberId, $branchId, $samityId, $req->loanId, $req->transactionDate, $interestCalMethodId );

            if ($req->paymentType == 'Cash') {
                $storeDueTransaction = DB::Table('mfn_loan_collection')
                ->insert(
                    [
                        'loanIdFk'             => $req->loanId,
                        'productIdFk'          => $productIdInfo[0],
                        'primaryProductIdFk'   => $primaryProductIdInfo[0],
                        'loanTypeId'           => $loanTypeId[0],
                        'memberIdFk'           => $memberId,
                        'branchIdFk'           => $branchId,
                        'samityIdFk'           => $samityId,
                        'collectionDate'       => date_format(date_create($req->transactionDate),"Y-m-d"),
                        'amount'               => $req->amount,
                        'principalAmount'      => $principalAmount,
                        'interestAmount'       => $interestAmount,
                        'paymentType'          => $req->paymentType,
                        'ledgerIdFk'           => $ledgerId,
                        'installmentNo'        => $req->installmentNo,
                        'installmentDueAmount' => $req->due,
                        'transactionType'      => 'Over Due',
                        'entryByEmployeeIdFk'  => Auth::user()->emp_id_fk
                    ]
                );
            }
            else {
                $storeDueTransaction = DB::Table('mfn_loan_collection')
                ->insert(
                    [
                        'loanIdFk'             => $req->loanId,
                        'productIdFk'          => $productIdInfo[0],
                        'primaryProductIdFk'   => $primaryProductIdInfo[0],
                        'loanTypeId'           => $loanTypeId[0],
                        'memberIdFk'           => $memberId,
                        'branchIdFk'           => $branchId,
                        'samityIdFk'           => $samityId,
                        'collectionDate'       => date_format(date_create($req->transactionDate),"Y-m-d"),
                        'amount'               => $req->amount,
                        'principalAmount'      => $principalAmount,
                        'interestAmount'       => $interestAmount,
                        'paymentType'          => $req->paymentType,
                        'ledgerIdFk'           => $ledgerId,
                        'chequeNumber'         => $req->chequeNumber,
                        'installmentNo'        => $req->installmentNo,
                        'installmentDueAmount' => $req->due,
                        'transactionType'      => 'Over Due',
                        'entryByEmployeeIdFk'  => Auth::user()->emp_id_fk
                    ]
                );
            }

            $logArray = array(
                'moduleId'  => 6,
                'controllerName'  => 'MfnDueLoanTransactionController',
                'tableName'  => 'mfn_loan_collection',
                'operation'  => 'insert',
                'primaryIds'  => [DB::table('mfn_loan_collection')->max('id')]
            );
            Service::createLog($logArray);


            // if ($storeDueTransaction == 'true') {
            //     $data = array(
            //         'responseTitle' =>  'Success!',
            //         'responseText'  =>  'Data inserted successfully.'
            //     );

            //     return response::json($data);
            // }


            if ($storeDueTransaction == true) {
                // $Success = 'True';
                $notification = array(
                    'message' => 'You have successfully inserted the information!',
                    'alert-type' => 'success'
                );
            }
            else {
                $notification = array(
                    'message' => 'Please full fill the information properly!',
                    'alert-type' => 'error'
                );
            }

            return redirect()->back()->with($notification);
            


			//  $rules = array(
            //     'member'            =>  'required',
            //     'transactionDate'   =>  'required',
            //     'loanId'       		=>  'required',
            //     'amount'            =>  'required|numeric'
            // );

            // if ($req->paymentType=='Bank') {
            //     $rules = $rules + array(
            //         'bank'          =>  'required',
            //         'chequeNumber'  =>  'required'
            //     );
            // }

            // $attributesNames = array(
            //     'member'            =>  'Member',
            //     'transactionDate'   =>  'Transaction Date',
            //     'loanId'       		=>  'Loan Id',
            //     'amount'            =>  'Amount',
            //     'bank'              =>  'Bank',
            //     'chequeNumber'      =>  'Cheque Number'
            // );

            // $validator = Validator::make(Input::all(), $rules);
            // $validator->setAttributeNames($attributesNames);

            // if($validator->fails()){
            //     return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            // }
            // else {            	

            //     $softwareDate = GetSoftwareDate::getSoftwareDate();
            //     $transactionDate = Carbon::parse($softwareDate);

            //     // Check is Transaction exists today or not, if so then give a meaasge
            //     $isTransactionExits = (int) DB::table('mfn_loan_collection')->where('softDel',0)->where('amount','>',0)->where('collectionDate',$softwareDate)->where('loanIdFk',$req->loanId)->value('id');
            //     if ($isTransactionExits>0) {
            //         $data = array(
            //             'responseTitle' =>  'Warning!',
            //             'responseText'  =>  'Transaction Already Exits Today.'
            //         );

            //         return response::json($data);
            //     }

            //     $loan = DB::table('mfn_loan')->where('id',$req->loanId)->first();
            
            //     $interestCalMethodId = DB::table('mfn_loan_product_interest_rate')
            //                                 ->where('loanProductId',$loan->productIdFk)
            //                                 ->value('interestCalculationMethodId');

            //     // $interestCalMethodId equals 4 means the reducing method
            //     if ($interestCalMethodId==4) {
            //         // Check is Subsequent Transaction exists after this date or not, if so then give a meaasge
            //         $isTransactionExits = (int) DB::table('mfn_loan_collection')->where('softDel',0)->where('collectionDate','>',$softwareDate)->where('loanIdFk',$req->loanId)->value('id');
            //         if ($isTransactionExits>0) {
            //             $data = array(
            //                 'responseTitle' =>  'Warning!',
            //                 'responseText'  =>  'Subsequent Transaction Already Exits.'
            //             );

            //             return response::json($data);
            //         }
            //     }

            //     // Store Data

            // 	$amount = (float) $req->amount;

			// 	if ($req->paymentType=="Cash") {
	        //         // Cash In Hand ledger
	        //         $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');
	        //     }
	        //     else{
	        //          $ledgerId = $req->bank;
	        //     }

            //     // $interestCalMethodId equals 4 means the reducing method
            //     if ($interestCalMethodId==4) {
            //         $loanCollections = DB::table('mfn_loan_collection')
            //                         ->where('softDel',0)
            //                         ->where('loanIdFk',$loan->id)
            //                         ->select('collectionDate','principalAmount')
            //                         ->get();

            //         if (count($loanCollections)>0) {
            //             $lastTransactionDate = Carbon::parse($loanCollections->max('collectionDate'));
            //             $remainingPrincipal = $loan->loanAmount - $loanCollections->sum('principalAmount');
            //         }
            //         else{
            //             $lastTransactionDate = Carbon::parse($loan->disbursementDate);
            //             $remainingPrincipal = $loan->loanAmount;
            //         }

            //         $daysDiff = $transactionDate->diffInDays($lastTransactionDate);
            //         $interestAmount = round($remainingPrincipal * $daysDiff * $loan->interestRateIndex);
            //         $principalAmount = $amount - $interestAmount;
            //     }
            //     else{
            //         $principalAmount = round($amount/(float) $loan->interestRateIndex,2);
            //         $interestAmount = round($amount - $principalAmount,2);
            //     }

            //     $transactionDate = Carbon::parse($req->transactionDate);

            //    /* $primaryProductId =  DB::table('mfn_member_information')
            //                                 ->where('id',$req->memberId)
            //                                 ->value('primaryProductId');*/

			// 	$loanCollection = new MfnLoanCollection;
            //     $loanCollection->loanIdFk               = $req->loanId;
            //     $loanCollection->productIdFk            = $loan->productIdFk;
			// 	$loanCollection->primaryProductIdFk		= $loan->primaryProductIdFk;
			// 	$loanCollection->loanTypeId				= 1;
			// 	$loanCollection->memberIdFk				= $req->memberId;
			// 	$loanCollection->branchIdFk				= $req->branchId;
			// 	$loanCollection->samityIdFk				= $req->samityId;
			// 	$loanCollection->collectionDate			= $transactionDate;
			// 	$loanCollection->amount					= $amount;
			// 	$loanCollection->principalAmount		= $principalAmount;
			// 	$loanCollection->interestAmount			= $interestAmount;                
			// 	$loanCollection->paymentType			= $req->paymentType;
			// 	$loanCollection->ledgerIdFk				= $ledgerId;
			// 	$loanCollection->chequeNumber			= $req->chequeNumber;
			// 	$loanCollection->installmentNo			= $req->installmentNo;
			// 	$loanCollection->entryByEmployeeIdFk	= Auth::user()->emp_id_fk;
			// 	$loanCollection->createdAt 				= Carbon::now();
			// 	$loanCollection->save();

            //     $totalCollectionAmount = MfnLoanCollection::active()->where('loanIdFk',$req->loanId)->sum('amount');

            //     $paidAmountOB = DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$req->loanId)->sum('paidLoanAmountOB');
            //     $totalCollectionAmount += $paidAmountOB;

            //     if ($loan->totalRepayAmount<=$totalCollectionAmount) {
            //         DB::table('mfn_loan')->where('id',$req->loanId)->update(['isLoanCompleted'=>1,'loanCompletedDate'=>$transactionDate]);
            //     }
            //     else{
            //         DB::table('mfn_loan')->where('id',$req->loanId)->update(['isLoanCompleted'=>0]);
            //     }

            //     if ($interestCalMethodId!=4) {
            //         // mark the completed installments
            

            //         $shedules = MfnLoanSchedule::active()->where('loanIdFk',$req->loanId)->get();

            //         foreach ($shedules as $key => $shedule) {
            //             if ($totalCollectionAmount>=$shedule->installmentAmount) {
            //                 $shedule->isCompleted = 1;
            //                 $shedule->isPartiallyPaid = 0;
            //                 $shedule->partiallyPaidAmount = 0;
            //                 $shedule->save();
            //             }
            //             elseif ($totalCollectionAmount>0) {
            //                 $shedule->isCompleted = 0;
            //                 $shedule->isPartiallyPaid = 1;
            //                 $shedule->partiallyPaidAmount = $totalCollectionAmount;
            //                 $shedule->save();
            //             }
            //             else{
            //                 $shedule->isCompleted = 0;
            //                 $shedule->isPartiallyPaid = 0;
            //                 $shedule->partiallyPaidAmount = 0;
            //                 $shedule->save();
            //             }

            //             $totalCollectionAmount = $totalCollectionAmount - $shedule->installmentAmount;
            //         }
            //     }

            
			// }

			// $data = array(
            //     'responseTitle' =>  'Success!',
            //     'responseText'  =>  'Data inserted successfully.'
            // );

            // return response::json($data);
        }

        public function updateTransaction(Request $req){

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
        else{

            DB::beginTransaction();
            try{

                if ($req->paymentType=="Cash") {
                        // Cash In Hand ledger
                    $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId',4)->where('isGroupHead',0)->value('id');
                }
                else{
                    $ledgerId = $req->bank;
                }

                $amount = floatval(str_replace(',', '', $req->amount));

                $loanCollection = MfnLoanCollection::find($req->collectionId);  
                $previousdata = $loanCollection;

                
                $loan = DB::table('mfn_loan')->where('id',$loanCollection->loanIdFk)->select('id','productIdFk','interestRateIndex','totalRepayAmount')->first();

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
                    $principalAmount = round($amount/(float) $loan->interestRateIndex,2);
                    $interestAmount = round($amount - $principalAmount,2);
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
                    'controllerName'  => 'MfnDueLoanTransactionController',
                    'tableName'  => 'mfn_loan_collection',
                    'operation'  => 'update',
                    'previousData'  => $previousdata,
                    'primaryIds'  => [$previousdata->id]
                );
                Service::createLog($logArray);

                $totalCollectionAmount = MfnLoanCollection::active()->where('loanIdFk',$loanCollection->loanIdFk)->sum('amount');

                $paidAmountOB = DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$loanCollection->loanIdFk)->sum('paidLoanAmountOB');
                $totalCollectionAmount += $paidAmountOB;

                if ($loan->totalRepayAmount<=$totalCollectionAmount) {
                    $maxCollectionDate = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$req->loanId)->max('collectionDate');
                    $maxCollectionDate = Carbon::parse($maxCollectionDate);
                    DB::table('mfn_loan')->where('id',$req->loanId)->update(['isLoanCompleted'=>1,'loanCompletedDate'=>$maxCollectionDate]);
                }
                else{
                    DB::table('mfn_loan')->where('id',$req->loanId)->update(['isLoanCompleted'=>0]);
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


 }


 public function deleteTransaction(Request $req) {      
    DB::beginTransaction();
    try{
        $collection = MfnLoanCollection::find($req->id);
        $previousdata = $collection;
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
            'controllerName'  => 'MfnDueLoanTransactionController',
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
            $maxCollectionDate = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$collection->loanIdFk)->max('collectionDate');
            $maxCollectionDate = Carbon::parse($maxCollectionDate);
            DB::table('mfn_loan')->where('id',$collection->loanIdFk)->update(['isLoanCompleted'=>1,'loanCompletedDate'=>$maxCollectionDate]);
        }
        else{
            DB::table('mfn_loan')->where('id',$collection->loanIdFk)->update(['isLoanCompleted'=>0]);
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