<?php

namespace App\Http\Controllers\microfin\loan\ajax;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Traits\GetSoftwareDate;    
use App\Http\Controllers\microfin\MicroFin;
use App\Http\Controllers\microfin\MicroFinance;

class MfnLoanAjaxController extends Controller {
    use GetSoftwareDate;

    public function getRegularLoanAccountsOnChangeMember(Request $req) {

        $memberBranchId = DB::table('mfn_member_information')->where('id',$req->memberId)->value('branchId');

            // $softwareDate = GetSoftwareDate::getSoftwareDate();
        $softwareDate = MicroFin::getSoftwareDateBranchWise($memberBranchId);


            $today = Carbon::parse($softwareDate);//Carbon::toDay();
            $weekDayNumber = $today->dayOfWeek== 6 ? 1: $today->dayOfWeek+2;
            $dayNumber = $today->day;        

            // get the samitys which have collection at today
            /*
            $todaySamityId = DB::table('mfn_samity')->where('samityDayId',$weekDayNumber)->orWhere('fixedDate',$dayNumber)->pluck('id')->toArray();

            $loanAccounts = DB::table('mfn_loan')
            ->where('memberIdFk',$req->memberId)
            ->where('loanTypeId',1)
            ->where(function ($query) use ($softwareDate,$todaySamityId) {
                $query->whereNotIn('samityIdFk',$todaySamityId)
                      ->orWhere('firstRepayDate', '>', $softwareDate);
            })
            ->select('id','loanCode')->get();*/

            if (isset($req->operation) && $req->operation=='waiver') {

                $loanAccounts = DB::table('mfn_loan')
                ->where('softDel',0)
                ->where('isLoanCompleted',0)
                ->where('memberIdFk',$req->memberId)
                ->where('disbursementDate','<=',$softwareDate)
                ->where('loanTypeId',1)
                ->select('id','loanCode')
                ->get();

            }
            else{
                $loanAccountIds = DB::table('mfn_loan')
                ->where('softDel',0)
                ->where('memberIdFk',$req->memberId)
                ->where('disbursementDate','<=',$softwareDate)
                ->where('loanTypeId',1)
                ->pluck('id')
                ->toArray();

                $loanAccoutIdsHavingSchedule = DB::table('mfn_loan_schedule')
                ->whereIn('loanIdFk',$loanAccountIds)
                ->where('scheduleDate',$softwareDate)
                ->pluck('loanIdFk')
                ->toArray();

                $loanAccounts = DB::table('mfn_loan')
                ->where('softDel',0)
                ->where('isLoanCompleted',0)
                ->where('memberIdFk',$req->memberId)
                ->where('disbursementDate','<=',$softwareDate)
                ->where('loanTypeId',1)
                ->whereNotIn('id',$loanAccoutIdsHavingSchedule)
                ->select('id','loanCode')
                ->get();
            }

            return response::json($loanAccounts);
        }

        public function getSoftwareDateOnChangeMember(Request $req){
            $memberBranchId = DB::table('mfn_member_information')->where('id',$req->memberId)->value('branchId');

            $softwareDate = MicroFin::getSoftwareDateBranchWise($memberBranchId);
            $softwareDate = Carbon::parse($softwareDate)->format('d-m-Y');
            return response::json($softwareDate);
        }

        public function getOneTimeLoanAccountsOnChangeMember(Request $req) {

            $softwareDate = GetSoftwareDate::getSoftwareDate();
           /* $today = Carbon::parse($softwareDate);
            $weekDayNumber = $today->dayOfWeek== 6 ? 1: $today->dayOfWeek+2;
            $dayNumber = $today->day;             

            // get the samitys which have collection at today
            $todaySamityId = DB::table('mfn_samity')->where('samityDayId',$weekDayNumber)->orWhere('fixedDate',$dayNumber)->pluck('id')->toArray();

            $loanAccounts = DB::table('mfn_loan')
            ->where('memberIdFk',$req->memberId)
            ->where('loanTypeId',2)
            ->where(function ($query) use ($softwareDate,$todaySamityId) {
                $query->whereNotIn('samityIdFk', $todaySamityId)
                      ->orWhere('firstRepayDate', '>', $softwareDate);
            })
            ->select('id','loanCode')->get();*/


            $loanAccountIds = DB::table('mfn_loan')
            ->where('softDel',0)
            ->where('memberIdFk',$req->memberId)
            ->where('disbursementDate','<=',$softwareDate)
            ->where('loanTypeId',2)
            ->pluck('id')
            ->toArray();

            $loanAccoutIdsHavingSchedule = DB::table('mfn_loan_schedule')
            ->whereIn('loanIdFk',$loanAccountIds)
            ->where('scheduleDate',$softwareDate)
            ->pluck('loanIdFk')
            ->toArray();

            $loanAccounts = DB::table('mfn_loan')
            ->where('softDel',0)
            ->where('memberIdFk',$req->memberId)
            ->where('disbursementDate','<=',$softwareDate)
            ->where('loanTypeId',2)
            // ->whereNotIn('id',$loanAccoutIdsHavingSchedule)
            ->select('id','loanCode')
            ->get();

            return response::json($loanAccounts);
        }

        public function getLoanInfo(Request $req){

            $loan = DB::table('mfn_loan')->where('id',$req->loanId)->first();
            $interestCalMethodId = DB::table('mfn_loan_product_interest_rate')
            ->where('loanProductId',$loan->productIdFk)
            ->value('interestCalculationMethodId');

            if ($interestCalMethodId!=4) {
                $data = $this->getLoanInfoForFlatMethod($req->loanId,$req->transactionDate);
            }
            else{
                $data = $this->getLoanInfoForReducingMethod($req->loanId,$req->transactionDate);
            }

            return response::json($data);
        }

        public function getLoanInfoForFlatMethod($loanId,$transactionDate){

            $loan = DB::table('mfn_loan')->where('id',$loanId)->first();
            $transactionDate = Carbon::parse($transactionDate)->format('Y-m-d');

            $schedules = DB::table('mfn_loan_schedule')->where('softDel',0)->where('loanIdFk',$loan->id)->get();

            $amountToPay = $schedules->where('scheduleDate','<',$transactionDate)->sum('installmentAmount');

			$amountPaid = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$loan->id)->sum('amount');
			$amountPaid += DB::table('mfn_loan_waivers')->where('softDel',0)->where('loanIdFk',$loan->id)->sum('amount');
			$amountPaid += DB::table('mfn_loan_write_off')->where('softDel',0)->where('loanIdFk',$loan->id)->sum('amount');
			$amountPaid += DB::table('mfn_loan_rebates')->where('softDel',0)->where('loanIdFk',$loan->id)->sum('amount');
			

            $paidAmountOB = DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$loan->id)->sum('paidLoanAmountOB');
            $amountPaid += $paidAmountOB;

            $dueAmount = $amountToPay - $amountPaid;
            $dueAmount = ($dueAmount<0) ? 0 : $dueAmount;

            $advanceAmount = $amountPaid - $amountToPay;
            $advanceAmount = ($advanceAmount<0) ? 0 : $advanceAmount;

            //get installemnt number to pay  

            /*$installmentNo = DB::table('mfn_loan_schedule')->where('softDel',0)->where('loanIdFk',$loanId)->where('scheduleDate','<=',$transactionDate)->where('isCompleted',0)->orderBy('installmentSl')->pluck('installmentSl')->toArray();*/
            $installmentNo = $schedules->where('isCompleted',0)->min('installmentSl');

            /*if (count($installmentNo)<1) {
                $installmentNo = DB::table('mfn_loan_schedule')->where('softDel',0)->where('loanIdFk',$loanId)->where('isCompleted',0)->min('installmentSl');
            } */           

            $outStandingAmount = $loan->totalRepayAmount - $amountPaid;

            $installmentAmount = $schedules->max('installmentAmount');
            $data = array(
                'dueAmount'          => $dueAmount,
                'advanceAmount'      => $advanceAmount,
                'outStandingAmount'  => $outStandingAmount,
                'installmentNo'      => $installmentNo,
                'installmentAmount'  => $installmentAmount,
                'isReducingMethod'   => 0
            );

            return $data;
        }

        public function getLoanInfoForReducingMethod($loanId,$transactionDate){

            $loan = DB::table('mfn_loan')->where('id',$loanId)->first();
            $transactionDate = Carbon::parse($transactionDate);

            $loanCollections = DB::table('mfn_loan_collection')
            ->where('softDel',0)
            ->where('amount','>',0)
            ->where('loanIdFk',$loanId)
            ->select('collectionDate','principalAmount')
            ->get();

            $principalAmountOB = DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$loan->id)->sum('principalAmountOB');

            if (count($loanCollections)>0) {
                $lastTransactionDate = Carbon::parse($loanCollections->max('collectionDate'));
                $remainingPrincipal = $loan->loanAmount - $loanCollections->sum('principalAmount') - $principalAmountOB;
                $daysDiff = $transactionDate->diffInDays($lastTransactionDate);
            }
            else{
                if ($loan->isFromOpening==1) {
                    $lastTransactionDate = DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$loan->id)->value('lastTransactionDate');
                    if ($lastTransactionDate=='' || $lastTransactionDate==null || $lastTransactionDate=='0000-00-00') {
                        $lastTransactionDate = $loan->disbursementDate;
                        $lastTransactionDate = Carbon::parse($lastTransactionDate);
                        $daysDiff = $transactionDate->diffInDays($lastTransactionDate) + 1;
                    }
                    else{
                        $lastTransactionDate = Carbon::parse($lastTransactionDate);
                        $daysDiff = $transactionDate->diffInDays($lastTransactionDate);
                    }                            
                }
                else{
                    $lastTransactionDate = Carbon::parse($loan->disbursementDate);
                    $daysDiff = $transactionDate->diffInDays($lastTransactionDate) + 1;
                }
                $remainingPrincipal = $loan->loanAmount - $principalAmountOB;                
            }

            $daysDiff = $transactionDate->diffInDays($lastTransactionDate);
            $interestAmount = round($remainingPrincipal * $daysDiff * $loan->interestRateIndex);

            $installmentNo = count($loanCollections) + 1;
            
            $data = array(
                'outStandingAmount'  => $remainingPrincipal,
                'interestAmount'     => $interestAmount,
                'installmentNo'      => $installmentNo,
                'isReducingMethod'   => 1
            );

            return $data;
        }

       /* public function getLoanInfoToUpdate(Request $req){
            $loan = DB::table('mfn_loan')->where('id',$req->loanId)->first();
            
        }*/

        public function getLoanInstallmentMinDate(Request $req){

            $loan = DB::table('mfn_loan')->where('id',$req->loanId)->first();
            
            $lastCollectionDate = DB::table('mfn_loan_collection')->where('loanIdFk',$loan->id)->orderBy('collectionDate','desc')->value('collectionDate');
            $lastCollectionDate = Carbon::parse($lastCollectionDate)->addDay()->format('Y-m-d');

            if ($lastCollectionDate=='' || $lastCollectionDate==null) {
                $lastCollectionDate = $loan->disbursementDate;
            }

            $data = array(
                'lastCollectionDate'    => $lastCollectionDate
            );

            return response::json($data);
        }

        public function getLoanCollectionDetails(Request $req){
            $count = 0;
            $collection = DB::table('mfn_loan_collection')->where('id',$req->id)->first();
            $loan = DB::table('mfn_loan')->where('id',$collection->loanIdFk)->select('id','productIdFk')->first();
            $interestCalMethodId = DB::table('mfn_loan_product_interest_rate')
            ->where('loanProductId',$loan->productIdFk)
            ->value('interestCalculationMethodId');

            if ($interestCalMethodId!=4) {
                $data = $this->getLoanCollectionDetailsForFlatMethod($collection->id);
                $count = 1;
                // dd($data);
            }
            else{
                $data = $this->getLoanCollectionDetailsForReducingMethod($collection->id);
                $count = 2;
            }

            // dd($data, $count);

            return response::json($data);
        }

        public function getLoanCollectionDetailsForReducingMethod($collectionId){

            $collection = DB::table('mfn_loan_collection')->where('id',$collectionId)->first();
            $member = DB::table('mfn_member_information')->where('id',$collection->memberIdFk)->select('name','code')->first();
            $loan = DB::table('mfn_loan')->where('id',$collection->loanIdFk)->select('id','loanCode','loanAmount','disbursementDate','totalRepayAmount')->first();
            $entryBy = DB::table('hr_emp_general_info')->where('id',$collection->entryByEmployeeIdFk)->select('emp_id','emp_name_english')->first();

            $pastCollectionAmount = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$collection->loanIdFk)->where('collectionDate','<',$collection->collectionDate)->sum('principalAmount');
            $pastCollectionAmount += DB::table('mfn_opening_balance_loan')->where('loanIdFk',$collection->loanIdFk)->sum('principalAmountOB');

            $paidAmount = $pastCollectionAmount + $collection->principalAmount;
            $outstandingAmount = $loan->loanAmount - $paidAmount;

            //Array for the First Coloum
            $tFirst = array(
             'Member Name:'        => $member->name,
             'Member Code:'        => $member->code,
             'Loan Code:'          => $loan->loanCode,
             'Loan Id:'            => $loan->id,
             'Date:'               => date('d-m-Y',strtotime($collection->collectionDate)),
             'Entry By:'           => $entryBy->emp_id.'-'.$entryBy->emp_name_english,
             'Mode Of Payment:'    => $collection->paymentType
         );

            //Array for the Second Coloum
            $tSecond = array(   
             'Principal:'      => number_format($collection->principalAmount,2),
             'Interest:'       => number_format($collection->interestAmount,2),
             'P+I:'            => number_format($collection->amount,2),
             'Paid:'           => number_format($paidAmount,2),
             'Outstanding:'    => number_format($outstandingAmount,2),
             'Status:'         => ($collection->isAuthorized==1) ? "Authorized" : "Unauthorized"
         );

            if ($collection->paymentType=="Bank") {

                $bankName = DB::table('acc_account_ledger')->where('id',$collection->ledgerIdFk)->value('name');

                $tFirst = $tFirst + array(
                    'Bank'          => $bankName,
                    'Cheque Number' => $collection->chequeNumber
                );

                $tSecond = $tSecond + array(
                    '-' => '-',
                    '-' => '-'
                );
            }

            // All values corresponds to the current installment number
            // $amountToPay = DB::table('mfn_loan_schedule')->where('softDel',0)->where('id',$loan->id)->where('installmentSl','<=',$collection->installmentNo)->sum('installmentAmount');
            $amountToPay = DB::table('mfn_loan_schedule')
            ->where([['softDel', '=', 0], ['loanIdFk', $loan->id], ['installmentSl', '<=', $collection->installmentNo]])
            ->sum('installmentAmount');

            $dueAmount = $amountToPay - $pastCollectionAmount - $collection->amount;
            $dueAmount = ($dueAmount<0) ? 0 : $dueAmount;

            $advanceAmount = $pastCollectionAmount + $collection->amount - $amountToPay;
            $advanceAmount = ($advanceAmount<0) ? 0 : $advanceAmount;

            $outstandingAmount = $loan->totalRepayAmount - $pastCollectionAmount - $collection->amount;

            // get the date of last installment or if it is first get the loan date
            if ($collection->id>0) {
                $lastInstallmentDate = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$collection->loanIdFk)->where('id','!=',$collection->id)->orderBy('collectionDate','desc')->value('collectionDate');
                
                // At the same day there not be multiple transaction of a member, so add one day
                $lastInstallmentDate = Carbon::parse($lastInstallmentDate)->addDay()->format('Y-m-d');
            }
            else{
                $lastInstallmentDate = $loan->disbursementDate;

                // At the same day there not be multiple transaction of a member, so add one day
                $lastInstallmentDate = Carbon::parse($lastInstallmentDate)->addDay()->format('Y-m-d');
            }            

            $data = array(
                'tFirst'                => $tFirst,
                'tSecond'               => $tSecond,
                'collection'            => $collection,
                'dueAmount'             => $dueAmount,
                'advanceAmount'         => $advanceAmount,
                'outstandingAmount'     => $outstandingAmount,
                'lastInstallmentDate'   => $lastInstallmentDate,
            );

            return $data;
        }

        public function getLoanCollectionDetailsForFlatMethod($collectionId){
            // dd($collectionId);
            $collection = DB::table('mfn_loan_collection')->where('id',$collectionId)->first();
            $member = DB::table('mfn_member_information')->where('id',$collection->memberIdFk)->select('name','code')->first();
            $loan = DB::table('mfn_loan')->where('id',$collection->loanIdFk)->select('id','loanCode','totalRepayAmount','disbursementDate')->first();
            $entryBy = DB::table('hr_emp_general_info')->where('id',$collection->entryByEmployeeIdFk)->select('emp_id','emp_name_english')->first();

            $pastCollectionAmount = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$collection->loanIdFk)->where('collectionDate','<',$collection->collectionDate)->sum('amount');

            $pastCollectionAmount += DB::table('mfn_opening_balance_loan')->where('loanIdFk',$collection->loanIdFk)->sum('paidLoanAmountOB');

            $paidAmount = $pastCollectionAmount + $collection->amount;
            $outstandingAmount = $loan->totalRepayAmount - $paidAmount;

            //Array for the First Coloum
            $tFirst = array(
             'Member Name:'        => $member->name,
             'Member Code:'        => $member->code,
             'Loan Code:'          => $loan->loanCode,
             'Loan Id:'            => $loan->id,
             'Date:'               => date('d-m-Y',strtotime($collection->collectionDate)),
             'Entry By:'           => @$entryBy->emp_id.'-'.@$entryBy->emp_name_english,
             'Mode Of Payment:'    => $collection->paymentType
         );

            //Array for the Second Coloum
            $tSecond = array(   
             'Principal:'      => number_format($collection->principalAmount,2),
             'Interest:'       => number_format($collection->interestAmount,2),
             'P+I:'            => number_format($collection->amount,2),
             'Paid:'           => number_format($paidAmount,2),
             'Outstanding:'    => number_format($outstandingAmount,2),
             'Status:'         => ($collection->isAuthorized==1) ? "Authorized" : "Unauthorized"
         );

            if ($collection->paymentType=="Bank") {

                $bankName = DB::table('acc_account_ledger')->where('id',$collection->ledgerIdFk)->value('name');

                $tFirst = $tFirst + array(
                    'Bank'          => $bankName,
                    'Cheque Number' => $collection->chequeNumber
                );

                $tSecond = $tSecond + array(
                    '-' => '-',
                    '-' => '-'
                );
            }

            // All values corresponds to the current installment number
            // $amountToPay = DB::table('mfn_loan_schedule')->where('softDel', 0)->where('id',$loan->id)->where('installmentSl','<=',$collection->installmentNo)->sum('installmentAmount');
            $amountToPay = DB::table('mfn_loan_schedule')
            ->where([['softDel', '=', 0], ['loanIdFk', $loan->id], ['installmentSl', '<=', $collection->installmentNo]])
            ->sum('installmentAmount');
            
            $dueAmount = $amountToPay - $pastCollectionAmount - $collection->amount;
            $dueAmount = ($dueAmount<0) ? 0 : $dueAmount;

            $advanceAmount = ($pastCollectionAmount + $collection->amount) - $amountToPay;
            // dd($amountToPay, $pastCollectionAmount, $collection->amount, $collection->installmentNo, $loan->id);
            $advanceAmount = ($advanceAmount<0) ? 0 : $advanceAmount;

            $outstandingAmount = $loan->totalRepayAmount - $pastCollectionAmount - $collection->amount;

            // get the date of last installment or if it is first get the loan date
            if ($collection->id>0) {
                $lastInstallmentDate = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$collection->loanIdFk)->where('id','!=',$collection->id)->orderBy('collectionDate','desc')->value('collectionDate');
                
                // At the same day there not be multiple transaction of a member, so add one day
                $lastInstallmentDate = Carbon::parse($lastInstallmentDate)->addDay()->format('Y-m-d');
            }
            else{
                $lastInstallmentDate = $loan->disbursementDate;

                // At the same day there not be multiple transaction of a member, so add one day
                $lastInstallmentDate = Carbon::parse($lastInstallmentDate)->addDay()->format('Y-m-d');
            }            

            $data = array(
                'tFirst'                => $tFirst,
                'tSecond'               => $tSecond,
                'collection'            => $collection,
                'dueAmount'             => $dueAmount,
                'advanceAmount'         => $advanceAmount,
                'outstandingAmount'     => $outstandingAmount,
                'lastInstallmentDate'   => $lastInstallmentDate,
            );

            return $data;
        }

        // One Time Loan Collections
        public function getOneTimeLoanCollectionDetails(Request $req){
            $collection = DB::table('mfn_loan_collection')->where('id',$req->id)->first();
            $member = DB::table('mfn_member_information')->where('id',$collection->memberIdFk)->select('name','code')->first();
            $loan = DB::table('mfn_loan')->where('id',$collection->loanIdFk)->select('id','loanCode','loanAmount','totalRepayAmount','disbursementDate')->first();
            $entryBy = DB::table('hr_emp_general_info')->where('id',$collection->entryByEmployeeIdFk)->select('emp_id','emp_name_english')->first();

            $paidAmount = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$collection->loanIdFk)->where('collectionDate','<=',$collection->collectionDate)->sum('amount');

            $pastCollectionAmount = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$collection->loanIdFk)->where('collectionDate','<',$collection->collectionDate)->sum('principalAmount');

            $paidPrincipalAmount = $pastCollectionAmount + $collection->principalAmount;
            $outstandingAmount = $loan->loanAmount - $paidPrincipalAmount;

            //Array for the First Coloum
            $tFirst = array(
             'Member Name:'        => $member->name,
             'Member Code:'        => $member->code,
             'Loan Code:'          => $loan->loanCode,
             'Date:'               => date('d-m-Y',strtotime($collection->collectionDate)),
             'Entry By:'           => $entryBy->emp_id.'-'.$entryBy->emp_name_english,
             'Mode Of Payment:'    => $collection->paymentType
         );

            //Array for the Second Coloum
            $tSecond = array(   
             'Principal:'      => number_format($collection->principalAmount,2),
             'Interest:'       => number_format($collection->interestAmount,2),
             'P+I:'            => number_format($collection->amount,2),
             'Paid:'           => number_format($paidAmount,2),
             'Outstanding:'    => number_format($outstandingAmount,2),
             'Status:'         => ($collection->isAuthorized==1) ? "Authorized" : "Unauthorized"
         );

            if ($collection->paymentType=="Bank") {

                $bankName = DB::table('acc_account_ledger')->where('id',$collection->ledgerIdFk)->value('name');

                $tFirst = $tFirst + array(
                    'Bank'          => $bankName,
                    'Cheque Number' => $collection->chequeNumber
                );

                $tSecond = $tSecond + array(
                    '-' => '-',
                    '-' => '-'
                );
            }

            // get data to edit
            $softwareDate = GetSoftwareDate::getSoftwareDate();
            $collectionInfo = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$collection->loanIdFk)->get();
            $totalPrincipalCollection = $collectionInfo->sum('principalAmount');
            $totalOutstanding = $loan->loanAmount - $totalPrincipalCollection;
            
            $scheduleDate = DB::table('mfn_loan_schedule')->where('softDel',0)->where('loanIdFk',$loan->id)->value('scheduleDate');
            
            $data = array(
                'tFirst'                    => $tFirst,
                'tSecond'                   => $tSecond,
                'collection'                => $collection,
                'outstandingAmount'         => $outstandingAmount,              
                'totalPrincipalCollection'  => $totalPrincipalCollection,              
                'totalOutstanding'          => $totalOutstanding,            

            );

            return response::json($data);
        }

        public function getLoanInfoForWaiver(Request $req){

            $loan = DB::table('mfn_loan')->where('id',$req->loanId)->first();
            $waiverDate = Carbon::parse($req->waiverDate)->format('Y-m-d');
            $collection = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$loan->id)->get();

            $openingBalance = DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$req->loanId)->get();

            foreach ($openingBalance as $key => $opb) {
                $collection->push([
                    'loanIdFk'          => $opb->loanIdFk,
                    'amount'            => $opb->paidLoanAmountOB,
                    'principalAmount'   => $opb->principalAmountOB,
                    'collectionDate'    => $opb->date
                ]);
            }

            $amountPaid = $collection->sum('amount');
            $PrincipalAmountPaid = $collection->sum('principalAmount');

            $outStandingAmount = $loan->totalRepayAmount - $amountPaid;

            /*$principalAmount = round($outStandingAmount/(float) $loan->interestRateIndex,2);
            $interestAmount = round($outStandingAmount - $principalAmount,2);*/

            $principalAmount = $loan->loanAmount - $PrincipalAmountPaid;
            $interestAmount = $outStandingAmount - $principalAmount;
            
            $amountToPay = DB::table('mfn_loan_schedule')->where('softDel',0)->where('loanIdFk',$loan->id)->where('scheduleDate','<',$waiverDate)->sum('installmentAmount');
            $amountPaid = $collection->where('collectionDate','<',$waiverDate)->sum('amount');

            $dueAmount = $amountToPay - $amountPaid;
            $dueAmount = ($dueAmount<0) ? 0 : $dueAmount;

            $advanceAmount = $amountPaid - $amountToPay;
            $advanceAmount = ($advanceAmount<0) ? 0 : $advanceAmount;

            // Transaction
            $traTotal = $collection->sum('amount');
            $traPrincipal = $collection->sum('principalAmount');
            $traInterest = $collection->sum('interestAmount');

            // Installment
            $installmentTotal = $loan->installmentAmount;
            $installmentPrincipal = round($installmentTotal/(float) $loan->interestRateIndex,2);
            $installmentInterest = round($installmentTotal - $installmentPrincipal,2);

            // Advance
            $advancePrincipal = round($advanceAmount/(float) $loan->interestRateIndex,2);
            $advanceInterest = round($advanceAmount - $advancePrincipal,2);

            // Due
            $duePricipal = round($dueAmount/(float) $loan->interestRateIndex,2);
            $dueInterest = round($dueAmount - $duePricipal,2);
            
            $data = array(
                'outStandingAmount' => number_format($outStandingAmount,2),
                'outStandingAmountPlain' => $outStandingAmount,
                'principalAmount' => number_format($principalAmount,2),
                'interestAmount'  => number_format($interestAmount,2),

                // Payable
                'payableTotal' => number_format($loan->totalRepayAmount,2),
                'payablePricipal' => number_format($loan->loanAmount,2),
                'payableInterest' => number_format($loan->interestAmount,2),

                // Transaction
                'traTotal' => number_format($traTotal,2),
                'traPrincipal' => number_format($traPrincipal,2),
                'traInterest' => number_format($traInterest,2),

                // Installment
                'installmentTotal' => number_format($installmentTotal,2),
                'installmentPrincipal' => number_format($installmentPrincipal,2),
                'installmentInterest' => number_format($installmentInterest,2),

                // Advance
                'advanceTotal' => number_format($advanceAmount,2),
                'advancePricipal' => number_format($advancePrincipal,2),
                'advanceInterest' => number_format($advanceInterest,2),

                // Due
                'dueTotal' => number_format($dueAmount,2),
                'duePricipal' => number_format($duePricipal,2),
                'dueInterest' => number_format($dueInterest,2),
            );

            return response::json($data);
        }

        public function getLoanInfoForWriteOffCollection(Request $req){

            $loan = DB::table('mfn_loan')->where('id',$req->loanId)->first();
            $writeOffDate = Carbon::parse($req->writeOffDate)->format('Y-m-d');
            $collection = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$loan->id)->get();

            $openingBalance = DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$loan->id)->get();

            foreach ($openingBalance as $key => $opb) {
                $collection->push([
                    'loanIdFk'          => $opb->loanIdFk,
                    'amount'            => $opb->paidLoanAmountOB,
                    'principalAmount'   => $opb->principalAmountOB,
                    'collectionDate'    => $opb->date
                ]);
            }

            $amountPaid = $collection->sum('amount');
            $PrincipalAmountPaid = $collection->sum('principalAmount');

            $outStandingAmount = $loan->totalRepayAmount - $amountPaid;

            $principalAmount = $loan->loanAmount - $PrincipalAmountPaid;
            $interestAmount = $outStandingAmount - $principalAmount;
            
            $amountToPay = $loan->totalRepayAmount;
            $amountPaid = $collection->sum('amount');

            // Transaction
            $traTotal = $collection->sum('amount');
            $traPrincipal = $collection->sum('principalAmount');
            $traInterest = $collection->sum('interestAmount');

            // Installment
            $installmentTotal = $loan->installmentAmount;
            $installmentPrincipal = round($installmentTotal/(float) $loan->interestRateIndex,2);
            $installmentInterest = round($installmentTotal - $installmentPrincipal,2);

            // Writeoff Collection
            $writeOffCollections = DB::table('mfn_loan_write_off_collection')
            ->where('softDel',0)
            ->where('loanIdFk',$loan->id)
            ->select('amount','principalAmount','interestAmount')
            ->get();

            $writeoffCollectionAmount = $writeOffCollections->sum('amount');
            $writeoffCollectionPrincipalAmount = $writeOffCollections->sum('principalAmount');
            $writeoffCollectionInterestAmount = $writeOffCollections->sum('interestAmount');
            
            $data = array(
                'outStandingAmount' => number_format($outStandingAmount,2),
                'outStandingAmountPlain' => $outStandingAmount,
                'principalAmount' => number_format($principalAmount,2),
                'interestAmount'  => number_format($interestAmount,2),

                // Payable
                'payableTotal' => number_format($loan->totalRepayAmount,2),
                'payablePricipal' => number_format($loan->loanAmount,2),
                'payableInterest' => number_format($loan->interestAmount,2),

                // Transaction
                'traTotal' => number_format($traTotal,2),
                'traPrincipal' => number_format($traPrincipal,2),
                'traInterest' => number_format($traInterest,2),

                // Installment
                'installmentTotal' => number_format($installmentTotal,2),
                'installmentPrincipal' => number_format($installmentPrincipal,2),
                'installmentInterest' => number_format($installmentInterest,2),

                // Writeoff Collection
                'writeoffCollectionAmount' => number_format($writeoffCollectionAmount,2),
                'writeoffCollectionPrincipalAmount' => number_format($writeoffCollectionPrincipalAmount,2),
                'writeoffCollectionInterestAmount' => number_format($writeoffCollectionInterestAmount,2),

                'writeoffCollectionAmountPlain' => $writeoffCollectionAmount,
                'writeoffCollectionPrincipalAmountPlain' => $writeoffCollectionPrincipalAmount,
                'writeoffCollectionInterestAmountPlain' => $writeoffCollectionInterestAmount,

                'interestRateIndex' => $loan->interestRateIndex,
            );

            return response::json($data);
        }

        public function getWaiverDetails(Request $req){

            $waiver = DB::table('mfn_loan_waivers')->where('id',$req->id)->first();
            $member = DB::table('mfn_member_information')->where('id',$waiver->memberIdFk)->select('id','name','code')->first();
            $loan = DB::table('mfn_loan')->where('id',$waiver->loanIdFk)->select('id','loanCode','totalRepayAmount')->first();
            $entryBy = DB::table('hr_emp_general_info')->where('id',$waiver->entryByEmployeeIdFk)->select('emp_name_english')->first();

            $collection = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$loan->id)->select('amount')->get();

            $openingBalance = DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$loan->id)->get();

            foreach ($openingBalance as $key => $opb) {
                $collection->push([
                    'loanIdFk'          => $opb->loanIdFk,
                    'amount'            => $opb->paidLoanAmountOB,
                    'principalAmount'   => $opb->principalAmountOB,
                    'collectionDate'    => $opb->date
                ]);
            }

            $amountPaid = $collection->sum('amount');

            $outStandingAmount = $loan->totalRepayAmount - $amountPaid;

            if ($req->operation=='details') {
                //Array for the First Coloum
                $tFirst = array(
                    'Member Name:'        => $member->name,
                    'Member Code:'        => $member->code,
                    'Loan Code:'          => $loan->loanCode,
                    'Waiver Date:'        => date('d-m-Y',strtotime($waiver->date)),
                    'Entry By:'           => $entryBy->emp_name_english
                );

                //Array for the Second Coloum
                $tSecond = array(   
                    'Outstanding Amount:' => number_format($outStandingAmount,2),
                    // 'Outstanding Amount:' => number_format(MicroFin::,2),
                    'Waiver Type:'        => $waiver->isWithServiceCharge==1 ? 'With Service Charge' : 'Without Service Charge',
                    'Waiver Amount:'      => number_format($waiver->amount,2),
                    'Principal Amount:'   => number_format($waiver->principalAmount,2),
                    'Interest Amount:'    => number_format($waiver->interestAmount,2),
                );

                $data = array(
                    'tFirst'                => $tFirst,
                    'tSecond'               => $tSecond
                );
            }

            else{
                $data = array(
                    'member' => $member->name . '-' .$member->code,
                    'loanId' => $loan->id,
                    'loanCode' => $loan->loanCode,
                    'waiverDate' => date('d-m-Y',strtotime($waiver->date)),
                    'waiverType' => $waiver->isWithServiceCharge==1 ? 1 : 2,
                    'principalAmount' => number_format($waiver->principalAmount,2),
                    'interestAmount' => number_format($waiver->interestAmount,2),
                    'outstandingAmount' => number_format($outStandingAmount,2),
                    'waiverAmount' => number_format($waiver->amount,2),
                    'waiverAmountLimit' => $outStandingAmount,
                    'notes' => $waiver->notes
                );
            }            

            return response::json($data);
        }


        public function getOneTimeLoanInfo(Request $req){

            $loan = DB::table('mfn_loan')->where('id',$req->loanId)->first();
            $collection = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$loan->id)->select('principalAmount','interestAmount')->get();
            $princpalCollection = $collection->sum('principalAmount');
            $princpalCollection += DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$req->loanId)->sum('principalAmountOB');
            //$intererstCollection = $collection->sum('interestAmount');
            $outstandingAmount = $loan->loanAmount - $princpalCollection;
            //$outstandingAmount = $loan->totalRepayAmount - $princpalCollection - $intererstCollection;

            // One Time Loan has Only One Schedule
            $scheduleDate = DB::table('mfn_loan_schedule')->where('loanIdFk',$loan->id)->value('scheduleDate');

            $transactionDate = Carbon::parse($req->transactionDate)->format('Y-m-d');
            if ($transactionDate>$scheduleDate) {
                $dueAmount = $outstandingAmount;
                $advanceAmount = 0;
            }
            else{
                $dueAmount = 0;
                $advanceAmount = $princpalCollection;
            }

            $installmentNo = count($collection) + 1;

            $data = array(
                'princpalCollection'    => $princpalCollection,
                /*'intererstCollection'   => $intererstCollection,*/
                'outstandingAmount'     => $outstandingAmount,
                'dueAmount'             => $dueAmount,
                'advanceAmount'         => $advanceAmount,
                'installmentNo'         => $installmentNo
            );

            return response::json($data);

        }

        public function getLoanInfoForRebate(Request $req){

            $loan = DB::table('mfn_loan')->where('id',$req->loanId)->first();
            $softwareDate = MicroFin::getSoftwareDateBranchWise($loan->branchIdFk);

            $rebateDate = Carbon::parse($softwareDate)->format('Y-m-d');
            // $collection = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$loan->id)->where('collectionDate','<',$softwareDate)->get();

            $collection = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$loan->id)->get();

            $amountPaid = $collection->sum('amount');

            $amountPaid += DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$req->loanId)->sum('paidLoanAmountOB');

            $outStandingAmount = $loan->totalRepayAmount - $amountPaid;

            $principalAmount = round($outStandingAmount/(float) $loan->interestRateIndex,2);
            $interestAmount = round($outStandingAmount - $principalAmount,2);

            $amountToPay = DB::table('mfn_loan_schedule')->where('softDel',0)->where('loanIdFk',$loan->id)->where('scheduleDate','<',$rebateDate)->sum('installmentAmount');
            $amountPaid = $collection->where('collectionDate','<',$rebateDate)->sum('amount');

            $dueAmount = $amountToPay - $amountPaid;
            $dueAmount = ($dueAmount<0) ? 0 : $dueAmount;

            $advanceAmount = $amountPaid - $amountToPay;
            $advanceAmount = ($advanceAmount<0) ? 0 : $advanceAmount;

            // Transaction
            $traTotal = $collection->sum('amount');
            $traPrincipal = $collection->sum('principalAmount');
            $traInterest = $collection->sum('interestAmount');

            // Installment
            $installmentTotal = $loan->installmentAmount;
            $installmentPrincipal = round($installmentTotal/(float) $loan->interestRateIndex,2);
            $installmentInterest = round($installmentTotal - $installmentPrincipal,2);

            // Advance
            $advancePrincipal = round($advanceAmount/(float) $loan->interestRateIndex,2);
            $advanceInterest = round($advanceAmount - $advancePrincipal,2);

            // Due
            $duePricipal = round($dueAmount/(float) $loan->interestRateIndex,2);
            $dueInterest = round($dueAmount - $duePricipal,2);
            
            $data = array(
                'outStandingAmount' => number_format($outStandingAmount,2),
                'outStandingAmountPlain' => $outStandingAmount,
                'principalAmount' => number_format($principalAmount,2),
                'interestAmount'  => number_format($interestAmount,2),

                // Payable
                'payableTotal' => number_format($loan->totalRepayAmount,2),
                'payablePricipal' => number_format($loan->loanAmount,2),
                'payableInterest' => number_format($loan->interestAmount,2),

                // Transaction
                'traTotal' => number_format($traTotal,2),
                'traPrincipal' => number_format($traPrincipal,2),
                'traInterest' => number_format($traInterest,2),

                // Installment
                'installmentTotal' => number_format($installmentTotal,2),
                'installmentPrincipal' => number_format($installmentPrincipal,2),
                'installmentInterest' => number_format($installmentInterest,2),

                // Advance
                'advanceTotal' => number_format($advanceAmount,2),
                'advancePricipal' => number_format($advancePrincipal,2),
                'advanceInterest' => number_format($advanceInterest,2),

                // Due
                'dueTotal' => number_format($dueAmount,2),
                'duePricipal' => number_format($duePricipal,2),
                'dueInterest' => number_format($dueInterest,2),

                // rebate date
                'rebateDate' => date('d-m-Y',strtotime($softwareDate))
            );

            return response::json($data);
        }

        public function getLoanRebateDetails(Request $req){

            $rebate = DB::table('mfn_loan_rebates')->where('id',$req->id)->first();

            $member = DB::table('mfn_member_information')
            ->where('id',$rebate->memberIdFk)
            ->select('name','code')
            ->first();

            $loan = DB::table('mfn_loan')
            ->where('id',$rebate->loanIdFk)
            ->select('id','loanCode')
            ->first();

            $samityCode = DB::table('mfn_samity')
            ->where('id',$rebate->samityIdFk)
            ->value('code');

            $entryBy = DB::table('hr_emp_general_info')
            ->where('id',$rebate->entryByEmpIdFk)
            ->value('emp_name_english');

            //Array for the First Coloum
            $tFirst = array(
             'Member Name:'        => $member->name,
             'Member Code:'        => $member->code,
             'Loan Code:'          => $loan->loanCode,
             'Samity Code:'        => $samityCode
         );

            //Array for the Second Coloum
            $tSecond = array(   
             'Transaction Date:'  => date('Y-m-d',strtotime($rebate->date)),
             'Rebate Amount:'     => number_format($rebate->amount,2),
             'Entry By:'          => $entryBy,
             'Notes:'             => $rebate->notes
         );

            $data = array(
                'tFirst'        => $tFirst,
                'tSecond'       => $tSecond,
                'loanId'        => $loan->id,
                'rebateAmount'  => $rebate->amount,
            );

            return response::json($data);
        }


        public function getWriteOffDetails(Request $req){            

            $writeOff = DB::table('mfn_loan_write_off')->where('id',$req->id)->first();

            $member = DB::table('mfn_member_information')
            ->where('id',$writeOff->memberIdFk)
            ->select('name','code')
            ->first();

            $loan = DB::table('mfn_loan')
            ->where('id',$writeOff->loanIdFk)
            ->select('id','loanCode')
            ->first();

            $samityCode = DB::table('mfn_samity')
            ->where('id',$writeOff->samityIdFk)
            ->value('code');

            $entryBy = DB::table('hr_emp_general_info')
            ->where('id',$writeOff->entryByEmpIdFk)
            ->value('emp_name_english');

            //Array for the First Coloum
            $tFirst = array(
             'Member Name:'        => $member->name,
             'Member Code:'        => $member->code,
             'Loan Code:'          => $loan->loanCode,
             'Samity Code:'        => $samityCode
         );

            //Array for the Second Coloum
            $tSecond = array(   
             'WriteOff Date:'     => date('d-m-Y',strtotime($writeOff->date)),
             'WriteOff Amount:'   => number_format($writeOff->amount,2),
             'Entry By:'          => $entryBy,
             'Notes:'             => $writeOff->notes
         );

            $data = array(
                'tFirst'            => $tFirst,
                'tSecond'           => $tSecond,
                'loanId'            => $loan->id,
                'writeOffAmount'    => $writeOff->amount,
            );

            return response::json($data);
        }

        public function getWriteOffCollectionDetails(Request $req){
            $writeOffCollection = DB::table('mfn_loan_write_off_collection')
            ->where('id',$req->id)
            ->first();

            $member = DB::table('mfn_member_information')
            ->where('id',$writeOffCollection->memberIdFk)
            ->select('name','code')
            ->first();

            $loan = DB::table('mfn_loan')
            ->where('id',$writeOffCollection->loanIdFk)
            ->select('id','loanCode')
            ->first();

            $samityCode = DB::table('mfn_samity')
            ->where('id',$writeOffCollection->samityIdFk)
            ->value('code');

            $entryBy = DB::table('hr_emp_general_info')
            ->where('id',$writeOffCollection->entryByEmpIdFk)
            ->value('emp_name_english');

            //Array for the First Coloum
            $tFirst = array(
             'Member Name:'        => $member->name,
             'Member Code:'        => $member->code,
             'Loan Code:'          => $loan->loanCode,
             'Samity Code:'        => $samityCode,
             'Notes:'              => $writeOffCollection->notes
         );

            //Array for the Second Coloum
            $tSecond = array(   
             'WriteOff Collection Date:'  => date('d-m-Y',strtotime($writeOffCollection->date)),
             'WriteOff Collection Amount:'           => number_format($writeOffCollection->amount,2),
             'Principal Amount:'          => number_format($writeOffCollection->principalAmount,2),
             'Interest Amount:'           => number_format($writeOffCollection->interestAmount,2),
             'Entry By:'                  => $entryBy,

         );

            $data = array(
                'tFirst'            => $tFirst,
                'tSecond'           => $tSecond
            );

            if (isset($req->operation)) {
                if ($req->operation=='update') {
                    $writeOff = DB::table('mfn_loan_write_off')
                    ->where('id',$writeOffCollection->loanWriteOffIdFk)
                    ->first();

                    $writeOffCollectionAmount = DB::table('mfn_loan_write_off_collection')
                    ->where('softDel',0)
                    ->where('loanIdFk',$loan->id)
                    ->where('id','!=',$writeOffCollection->id)
                    ->sum('amount');

                    $writeOffEligibleAmount = $writeOff->amount - $writeOffCollectionAmount;

                    $info = array(
                        'writeOffDate'    => date('d-m-Y',strtotime($writeOff->date)),
                        'writeOffAmount'    => number_format($writeOff->amount,2),
                        'writeOffEligibleAmount'    => $writeOffEligibleAmount,
                        'writeOffCollectionAmountPlain'    => $writeOffCollection->amount,
                        'loanId'    => $loan->id,
                    );

                    $data['info'] = $info;
                }
            }
            
            return response::json($data);
        }

        public function getLoanInterestIndex(){

            $interestRateOB = MfnLoanProductInterestRate::where('loanProductId', $req->productId)
            ->select('interestModeId',
               'interestRate',
               'interestCalculationMethodShortName',
               'installmentNum',
               'interestRateIndex',
               'repaymentFrequencyId'
           )
            ->first();

            $insuranceAmount = DB::table('mfn_loans_product')
            ->where('id', $req->productId)
            ->pluck('insuranceAmount')
            ->toArray();

            $microfinance = new MicroFinance;

            $interestMode = $microfinance->getInterestModeOptions();

            $loanRepayPeriodOB = MfnLoanRepayPeriod::where('id', $req->loanRepayPeriodId)->select('inMonths')->first();

            if($loanRepayPeriodOB->inMonths==12)
                $yearCount = 1;
            if($loanRepayPeriodOB->inMonths==24)
                $yearCount = 2;
            if($loanRepayPeriodOB->inMonths==36)
                $yearCount = 3;

            //  INTEREST AMOUNT CALCULATION.
            $interestIndex = sprintf("%.2f", ($interestRateOB->interestRateIndex / 100) * 365 * $yearCount);

            return response::json($interestIndex);
        }

    }