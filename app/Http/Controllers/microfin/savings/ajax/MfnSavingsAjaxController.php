<?php

namespace App\Http\Controllers\microfin\savings\ajax;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\microfin\savings\MfnSavingsMonthlyCollectionType as MonthlyCollectionType;
use App\microfin\savings\MfnSavingsProduct;
use App\microfin\savings\MfnSavingsDepositType;
use App\microfin\savings\MfnSavingsCollectionFrequency;
use App\microfin\savings\MfnSavingsInterestCalFrequency;
use App\microfin\savings\MfnSavingsInterestCalMethod;
use App\microfin\savings\MfnSavingsMonthlyCollectionType;
use App\microfin\savings\MfnSavingsAccount;
use App\microfin\savings\MfnSavingsDeposit;
use App\microfin\savings\MfnSavingsWithdraw;
use App\microfin\member\MfnMemberInformation;
use App\Traits\GetSoftwareDate;
use App\Http\Controllers\microfin\MicroFin;
use App\microfin\savings\MfnOpeningSavingsAccountInfo;


class MfnSavingsAjaxController extends Controller {
    use GetSoftwareDate;

    public function getMonthlyCollectionTypeValues(Request $req) {

        $monthlyCollectionType = MonthlyCollectionType::find($req->monthlyCollectionTypeId);

        $values = explode(',',$monthlyCollectionType->value);
        $label = $monthlyCollectionType->label;

        $data = array(
            'values' => $values,
            'label'  => $label
        );

        return response::json($data);           
    }


    public function getProductInfo(Request $req) {
        $product = MfnSavingsProduct::find($req->id);

        $depositTypeName = DB::table('mfn_savings_deposit_type')->where('id',$product->depositTypeIdFk)->value('name');
        $interestCalculationFrequencyName = DB::table('mfn_savings_interest_cal_frequency')->where('id',$product->interestCalFrequencyIdFk)->value('name');
            //Array for the First Coloum
        $tFirst = array(
            'Name'                                  => $product->name,
            'Short Name'                            => $product->shortName,
            'Saving Product Code'                   => $product->code,
            'Start Date'                            => $product->startDate,
            'Minimum Savings Balance'               => $product->minSavingBalance,
            'Type Of Deposit'                       => $depositTypeName,
            'Weekly Deposit Amount'                 => $product->weeklyDepositAmount,
            'Monthly Deposit Amount'                => $product->monthlyDepositAmount,
            'Is Multiple Savings Allowed?'          => $product->isMultipleSavingAllowed ? "Yes" : "No",
            'Is Nominee Required?'                  => $product->isNomineeRequired ? "Yes" : "No",
            'Is Closing Charge Applicable?'         => $product->hasClosingCharge ? "Yes" : "No",
            'Closing Charge'                        => $product->closingChargeAmount,
            'Interest Calculation Frequency'        => $interestCalculationFrequencyName

        );



        $savingCollectionFrequencyName = DB::table('mfn_savings_collection_frequency')->where('id',$product->savingCollectionFrequencyIdFk)->value('name');

        $monthlyCollectionTypeName = DB::table('mfn_saving_monthly_collection_type')->where('id',$product->monthlyCollectionTypeIdFk)->value('name');
        $label = DB::table('mfn_saving_monthly_collection_type')->where('id',$product->monthlyCollectionTypeIdFk)->value('label');
        $monthlyCollectionTypeValueArray = explode(',',DB::table('mfn_saving_monthly_collection_type')->where('id',$product->monthlyCollectionTypeIdFk)->value('value'));

        $interestCalculationMethodName = DB::table('mfn_savings_interest_cal_method')->where('id',$product->interestCalMethodIdFk)->value('name');


    //Array for the Second Coloum
        $tSecond = array(

            'Saving Collection Frequency'           => $savingCollectionFrequencyName,
            'Monthly Collection Type'               => $monthlyCollectionTypeName,
            'Monthly Collection '.$label            => $monthlyCollectionTypeValueArray[$product->monthlyCollectionTypeIdValueIndex],

            'Interest Calculation Method'           => $interestCalculationMethodName,
            'Interest Rate'                         => $product->interestRate,
            'Late Installment Cal Method'           => $product->lateInstalmentCalMethodId,
            'Late Installment Penalty Amount'       => $product->lateInstallmentPenaltyAmount,
            'Is Partial Withdraw Allowed?'          => $product->partialWithdrawAllowId ? "Yes" : "No",
            'Is Due Member Getting Interest?'       => $product->isDueMemberGetInterstId ? "Yes" : "No",
            'Status'                                => $product->status ? "Active" : "Inactive",
            'On Closing Interest Editable'          => $product->onClosingInterestEditableId ? "Yes" : "No",
            'Maximum Allowed Missing Installments'  => (int) $product->maxAllowMissingInstallmentNum,
            '-'                                     => '-',
        );

        $data = array(
            'product' => $product,
            'tFirst'  => $tFirst,
            'tSecond' => $tSecond
        );

        return response::json($data);
    }


    public function getDepositTypeDetails(Request $req) {
        $depositType = MfnSavingsDepositType::find($req->id);

        //Array for the First Coloum
        $tFirst = array(
            'Name'                                  => $depositType->name
        );

        //Array for the Second Coloum
        $tSecond = array(   
            'Code'                                     => $depositType->code
        );

        $data = array(
            'depositType'   => $depositType,
            'tFirst'        => $tFirst,
            'tSecond'       => $tSecond
        );

        return response::json($data);
    }


    public function getCollectionFrequencyDetails(Request $req) {
        $collFrequency = MfnSavingsCollectionFrequency::find($req->id);

        //Array for the First Coloum
        $tFirst = array(
            'Name'    => $collFrequency->name
        );

        //Array for the Second Coloum
        $tSecond = array(   
            'Status'    => $collFrequency->status ? "Active": "Inactive"
        );

        $data = array(
            'collFrequency'   => $collFrequency,
            'tFirst'        => $tFirst,
            'tSecond'       => $tSecond
        );

        return response::json($data);
    }

    public function getInterstCalFrequencyDetails(Request $req) {
        $interestCalFrequency = MfnSavingsInterestCalFrequency::find($req->id);

            //Array for the First Coloum
        $tFirst = array(
            'Name'    => $interestCalFrequency->name
        );

            //Array for the Second Coloum
        $tSecond = array(   
            'Status'    => $interestCalFrequency->status ? "Active": "Inactive"
        );

        $data = array(
            'interestCalFrequency'   => $interestCalFrequency,
            'tFirst'        => $tFirst,
            'tSecond'       => $tSecond
        );

        return response::json($data);
    }

    public function getInterstCalMethodDetails(Request $req) {
        $interestCalMethod = MfnSavingsInterestCalMethod::find($req->id);

            //Array for the First Coloum
        $tFirst = array(
            'Name'    => $interestCalMethod->name
        );

            //Array for the Second Coloum
        $tSecond = array(   
            'Status'    => $interestCalMethod->status ? "Active": "Inactive"
        );

        $data = array(
            'interestCalMethod'   => $interestCalMethod,
            'tFirst'        => $tFirst,
            'tSecond'       => $tSecond
        );

        return response::json($data);
    }

    public function getMonthlyCollectionTypeDetails(Request $req) {
        $monthlyCollectionType = MfnSavingsMonthlyCollectionType::find($req->id);
        $values = explode(',',$monthlyCollectionType->value);

            //Array for the First Coloum
        $tFirst = array(
            'Name'      => $monthlyCollectionType->name,
            'Values'    => $monthlyCollectionType->value
        );

            //Array for the Second Coloum
        $tSecond = array(   
            'Status'    => $monthlyCollectionType->status ? "Active": "Inactive",
            '-'         => '-'
        );

        $data = array(
            'monthlyCollectionType'   => $monthlyCollectionType,
            'values'                  => $values,
            'tFirst'                  => $tFirst,
            'tSecond'                 => $tSecond
        );

        return response::json($data);
    }


    public function getInfoToCraeteSavingsAccount(Request $req) {
        $member = DB::table('mfn_member_information')->where('id',$req->memberId)->first();
        $memberBranchIdForSoftwareDate = DB::table('mfn_member_information')->where('id',$req->memberId)->value('branchId');
            // dd($memberBranchIdForSoftwareDate);
        $product = MfnSavingsProduct::find($req->productId);
        $depositTypeName = DB::table('mfn_savings_deposit_type')->where('id',$product->depositTypeIdFk)->value('name');


            //Saving Cycle is the number of account of this member of the same product
        $savingCycle = DB::table('mfn_savings_account')->where('softDel',0)->where('memberIdFk',$member->id)->where('savingsProductIdFk',$product->id)->max('savingCycle') + 1;
        
        $numberOfAccOfThisMember = $savingCycle;
        $numberOfAccOfThisMember = str_pad($numberOfAccOfThisMember, 3,'0',STR_PAD_LEFT);
        $savingCode = $product->shortName.'.'.$member->code.'.'.$numberOfAccOfThisMember;

            //Get period for the OTS account
        if($product->depositTypeIdFk==4) {
            $period = DB::table('mfn_savings_fdr_product_repay_amount')->where('productIdFk',$product->id)->pluck('month','year')->toArray();
        }
        else{
            $period = []; 
        }

        // $softwareDate = GetSoftwareDate::getSoftwareDateInFormat();   
        $softwareDate = MicroFin::getSoftwareDateBranchWise($memberBranchIdForSoftwareDate);  

        $data = array(
            'product' => $product,
            'depositTypeName' => $depositTypeName,
            'savingCode' => $savingCode,
            'openingDate' => $softwareDate,
            'autoProcessAmount' => $product->weeklyDepositAmount,
            'savingCycle' => $savingCycle,
            'period' => $period,
        );

        return response::json($data);
    }

    public function getInfoToCraeteSavingsAccountForOpening(Request $req) {
        $member = DB::table('mfn_member_information')->where('id',$req->memberId)->first();
        $product = MfnSavingsProduct::find($req->productId);
        $depositTypeName = DB::table('mfn_savings_deposit_type')->where('id',$product->depositTypeIdFk)->value('name');


        //Saving Cycle is the number of account of this member of the same product
        $savingCycle = DB::table('mfn_savings_account')->where('softDel',0)->where('memberIdFk',$member->id)/*->where('depositTypeIdFk','!=',1)*/->where('savingsProductIdFk',$product->id)->count() + 1;

        $numberOfAccOfThisMember = $savingCycle;
        $numberOfAccOfThisMember = str_pad($numberOfAccOfThisMember, 3,'0',STR_PAD_LEFT);
        $savingCode = $product->shortName.'.'.$member->code.'.'.$numberOfAccOfThisMember;

            //Get period for the OTS account
        if($product->depositTypeIdFk==4) {
            $period = DB::table('mfn_savings_fdr_product_repay_amount')->where('productIdFk',$product->id)->pluck('month','year')->toArray();
        }
        else{
            $period = []; 
        }

        $softwareDate = GetSoftwareDate::getSoftwareDateInFormat();     

        $data = array(
            'product' => $product,
            'depositTypeName' => $depositTypeName,
            'savingCode' => $savingCode,
            'openingDate' => $softwareDate,
            'autoProcessAmount' => $product->weeklyDepositAmount,
            'savingCycle' => $savingCycle,
            'period' => $period,
        );

        return response::json($data);
    }

    public function getOtsFixedAmountBaseOnPeriod(Request $req) {
        $amounts = DB::table('mfn_savings_fdr_product_repay_amount')->where('productIdFk',$req->productId)->where('year',$req->year)->where('month',$req->month)->pluck('monthlyAmount')->toArray();
        return $amounts;
    }

    public function getAccountDetails(Request $req) {
        $account = MfnSavingsAccount::find($req->id);
        $product = MfnSavingsProduct::where('id',$account->savingsProductIdFk)->select('name','depositTypeIdFk','isMultipleSavingAllowed','isNomineeRequired')->first();
        $member = DB::table('mfn_member_information')->where('id',$account->memberIdFk)->select('name','code')->first();
        $depositTypeName = MfnSavingsDepositType::where('id',$product->depositTypeIdFk)->value('name');

        if (isset($req->isOpeningData)) {
            $savingsCode = DB::table('mfn_opening_savings_account_info')->where('savingsAccIdFk',$account->id)->value('manualSavingCode');
        }
        else{
            $savingsCode = $account->savingsCode;
        }

            //Array for the First Coloum
        $tFirst = array(
            'Member:'           => $member->name.'-'.$member->code,
            'Deposit Type:'     => $depositTypeName,
            'Code:'             => $savingsCode,
            'Opening Date:'     => date('d-m-Y',strtotime($account->accountOpeningDate)),
            'Total Refund:'     => '-',
            'Total Savings:'    => '-'              
        );

            //Array for the Second Coloum
        $tSecond = array(   
            'Product:'               => $product->name,
            'Interest Rate:'         => number_format($account->savingsInterestRate,2),
            'Auto Process Amount:'   => number_format(max($account->autoProcessAmount,$account->fixedDepositAmount),2),
            'Total Deposit:'         => '',
            'Total Interest:'        => '',
            'Current Status:'        => $account->status ? "Open" : "Close"
        );           


        if ($product->depositTypeIdFk==4 || $product->depositTypeIdFk==2){
            $tFirst = $tFirst + array(
                'Mature Date:'   =>  date('d-m-Y',strtotime($account->accountMatureDate))
            );

            $tSecond = $tSecond + array(
                'Period:'   =>  $account->periodYear.' Year '.$account->periodMonth.' Month'
            );
        }

        if ($product->isMultipleSavingAllowed) {
            $tFirst = $tFirst + array(
                'cycle'   =>  $account->savingCycle
            );
            $tSecond = $tSecond + array(
                '-'   =>  '-',
            );
        }

            // if depositType= FDR then get the deposit info, (e.g. transaction type, if bank then bank acc and cheque number)
        if ($product->depositTypeIdFk==4){                
            $transactionType = $account->transactionType;                
            if ($transactionType=='Cash') {
                $bank = '';
                $chequeNumber = '';
            }
            elseif ($transactionType=='Bank') {
                $deposit = DB::table('mfn_savings_deposit')->where('softDel',0)->where('accountIdFk',$account->id)->select('ledgerIdFk','chequeNumber')->first();
                $bank = $deposit->ledgerIdFk;
                $chequeNumber = $deposit->chequeNumber;
            }
        }
        else{
            $transactionType = '';
            $bank = '';
            $chequeNumber = '';
        }

        if (isset($req->isOpeningData)) {
            $bank = '';
            $chequeNumber = '';

                // remove items from array
            unset($tFirst['Total Refund:']);
            unset($tFirst['Total Savings:']);
            unset($tSecond['Total Deposit:']);
            unset($tSecond['Total Interest:']);
        }

        if ($product->isNomineeRequired==1) {
                // Get nominees
            $nominees = DB::table('mfn_savings_fdr_acc_nominee_info')->where('savingsAccountIdFk',$account->id)->get();

            $data = array(
                'account'           =>  $account,
                'savingsCode'       =>  $savingsCode,
                'product'           =>  $product,
                'tFirst'            =>  $tFirst,
                'tSecond'           =>  $tSecond,
                'nominees'          =>  $nominees,
                'depositTypeId'     =>  $product->depositTypeIdFk,
                'transactionType'   =>  $transactionType,
                'bank'              =>  $bank,
                'chequeNumber'      =>  $chequeNumber
            );
        }
        else{
            $data = array(
                'account'           =>  $account,
                'savingsCode'       =>  $savingsCode,
                'product'           =>  $product,
                'tFirst'            =>  $tFirst,
                'tSecond'           =>  $tSecond,
                'depositTypeId'     =>  $product->depositTypeIdFk,
                'transactionType'   =>  $transactionType,
                'bank'              =>  $bank,
                'chequeNumber'      =>  $chequeNumber
            );
        }

        return response::json($data);
    }

    public function getSavingsAccountOnChangeMember(Request $req) {

        $member = DB::table('mfn_member_information')->find($req->memberId);

        $softwareDate = MicroFin::getSoftwareDateBranchWise($member->branchId);      

        $today = Carbon::parse($softwareDate);

        $weekDayNumber = $today->dayOfWeek==6 ? 1: $today->dayOfWeek+2;
        $dayNumber = $today->day;             

            // get the samitys which have collection at today
        $samities = DB::table('mfn_samity')
        ->where('softDel',0)
        ->where('branchId',$member->branchId)
        ->where('openingDate','<=',$softwareDate)
        ->get();

        $samityDayChanges = DB::table('mfn_samity_day_change')
        ->whereIn('samityId',$samities->pluck('id'))
        ->where('effectiveDate','>',$softwareDate)
        ->select('samityId','samityDayId','fixedDate')
        ->get();

        $samityDayChanges = $samityDayChanges->sortBy('effectiveDate')->unique('samityId');

        foreach ($samityDayChanges as $samityDayChange) {
            $samities->where('id',$samityDayChange->samityId)->first()->samityDayId = $samityDayChange->samityDayId;
            $samities->where('id',$samityDayChange->samityId)->first()->fixedDate = $samityDayChange->fixedDate;
        }

            // $todaySamityId = DB::table('mfn_samity')->where('samityDayId',$weekDayNumber)->orWhere('fixedDate',$dayNumber)->pluck('id')->toArray();

        $todaySamityId = $samities->filter(function ($ob) use ($weekDayNumber,$dayNumber) {                           
           if (($ob->samityDayId == $weekDayNumber) || ($ob->fixedDate == $dayNumber)) {
               return true;
           }
           else {
               return false;
           }
       })
        ->pluck('id')
        ->toArray();

            // Get those Acounts which has transaction today
        $toDay = Carbon::toDay()->format('Y-m-d');
        $accDepositToday = DB::table('mfn_savings_deposit')->where('softDel',0)->where('amount','>',0)->where('memberIdFk',$req->memberId)->where('depositDate',$toDay)->pluck('accountIdFk')->toArray();
        $accWithdrawToday = DB::table('mfn_savings_withdraw')->where('softDel',0)->where('amount','>',0)->where('memberIdFk',$req->memberId)->where('withdrawDate',$toDay)->pluck('accountIdFk')->toArray();
            // $accTodayTra = $accDepositToday + $accWithdrawToday;

            // if it is withdraw than identify the accounts which are not allowed to withdraw
        if (isset($req->task)) {
            if ($req->task=='withdraw') {
                $accTodayTra = $accWithdrawToday;
                /*$accountsWithNotAllowed =  DB::table('mfn_savings_account as sa')
                ->join('mfn_saving_product as sp','sa.savingsProductIdFk','sp.id')
                ->where('sa.memberIdFk',$req->memberId)
                ->where('sa.softDel',0)
                ->where('sp.partialWithdrawAllowId',0)
                ->pluck('sa.id')
                ->toArray();*/

                /*$savingsAccounts = DB::table('mfn_savings_account')->where('memberIdFk',$req->memberId)->where('softDel',0)->where('status',1)->where('depositTypeIdFk','!=',4)->whereNotIn('id',$accTodayTra)->whereNotIn('id',$accountsWithNotAllowed)->select('id','savingsCode')->get();*/ 

                $savingsAccounts = DB::table('mfn_savings_account')
                ->where('memberIdFk',$req->memberId)
                ->where('softDel',0)
                ->where('status',1)
                ->where('depositTypeIdFk','!=',4)
                ->whereNotIn('id',$accTodayTra)
                ->where(function($query){
                    $query->where('isPartialWithdrawAllowed',1)
                    ->orWhere('depositTypeIdFk','=',2)
                    ->orWhere('depositTypeIdFk','=',1);
                })
                    // ->where('isPartialWithdrawAllowed',1)
                ->select('id','savingsCode')
                ->get();
            }
            elseif ($req->task=='deposit') {
                $accTodayTra = $accDepositToday;
                $savingsAccounts = DB::table('mfn_savings_account')->where('memberIdFk',$req->memberId)->where('softDel',0)->where('status',1)->where('depositTypeIdFk','!=',4)->whereNotIn('samityIdFk',$todaySamityId)->whereNotIn('id',$accTodayTra)->select('id','savingsCode')->get();

            }                
        }
        else{
            $accountsWithNotAllowed = [];
            $savingsAccounts = DB::table('mfn_savings_account')->where('memberIdFk',$req->memberId)->where('softDel',0)->where('status',1)->where('depositTypeIdFk','!=',4)->whereNotIn('samityIdFk',$todaySamityId)/*->whereNotIn('id',$accTodayTra)*/->whereNotIn('id',$accountsWithNotAllowed)->select('id','savingsCode')->get();

        }

        return response::json($savingsAccounts);
    }

    public function getSavingsAccountOnChangeMemberOnClosing(Request $req){    
        $savingsAccounts = DB::table('mfn_savings_account')->where('memberIdFk',$req->memberId)->where('softDel',0)->where('status',1)->select('id','savingsCode')->get();
        return response::json($savingsAccounts);
    }

    public function getSavingsAccountBalanceInfo(Request $req) {
        $memberId = DB::table('mfn_savings_account')->where('id',$req->savingsAccountId)->value('memberIdFk');
        $primaryProductId = DB::table('mfn_member_information')->where('id',$memberId)->value('primaryProductId');
        $openingBalanceInfo = DB::table('mfn_opening_savings_account_info')->where('savingsAccIdFk',$req->savingsAccountId)->first();

        $authorizedDeposit = MfnSavingsDeposit::active()->where('accountIdFk',$req->savingsAccountId)/*->where('primaryProductIdFk',$primaryProductId)*/->where('isAuthorized',1)->sum('amount');
        if ($openingBalanceInfo!=null) {
            $authorizedDeposit += $openingBalanceInfo->openingPrincipal + $openingBalanceInfo->openingInterest;
        }
        $unAuthorizedDeposit = (float) MfnSavingsDeposit::active()->where('accountIdFk',$req->savingsAccountId)/*->where('primaryProductIdFk',$primaryProductId)*/->where('isAuthorized',0)->sum('amount');

        $totalDeposit = $authorizedDeposit + $unAuthorizedDeposit;


        $authorizedWithdraw = MfnSavingsWithdraw::active()->where('accountIdFk',$req->savingsAccountId)/*->where('primaryProductIdFk',$primaryProductId)*/->where('isAuthorized',1)->sum('amount');
        if ($openingBalanceInfo!=null) {
            $authorizedWithdraw += $openingBalanceInfo->openingWithdraw;
        }
        $unAuthorizedWithdraw = MfnSavingsWithdraw::active()->where('accountIdFk',$req->savingsAccountId)/*->where('primaryProductIdFk',$primaryProductId)*/->where('isAuthorized',0)->sum('amount');

        $totalWithdraw = $authorizedWithdraw + $unAuthorizedWithdraw;

        $balance = $totalDeposit - $totalWithdraw;

        $lastDepositDate = MfnSavingsDeposit::active()->where('accountIdFk',$req->savingsAccountId)->max('depositDate');

        if ($lastDepositDate=='' || $lastDepositDate==null) {
            $lastDepositDate = MfnSavingsAccount::where('id',$req->savingsAccountId)->value('accountOpeningDate');
        }

        $lastWithdrawDate = MfnSavingsWithdraw::active()->where('accountIdFk',$req->savingsAccountId)->max('withdrawDate');

        if ($lastWithdrawDate=='' || $lastWithdrawDate==null) {
            $lastWithdrawDate = MfnSavingsAccount::where('id',$req->savingsAccountId)->value('accountOpeningDate');
        }

        $account = MfnSavingsAccount::where('id',$req->savingsAccountId)->select('accountMatureDate','payableAmount','fixedDepositAmount','depositTypeIdFk','branchIdFk')->first();

            // get payable amount for saving account closing
        $payableAmount = 0;
        if ($account->depositTypeIdFk==4) {
            $matureDate = Carbon::parse($account->accountMatureDate);
            if ($matureDate->gte(Carbon::toDay())) {
                $payableAmount = $account->payableAmount;
            }
            else{
                $payableAmount = $account->fixedDepositAmount;
            }
        }

            // get closing date for account closing
        if ($lastDepositDate>$lastWithdrawDate) {
            $minClosingDate = $lastDepositDate;
        }
        else{
            $minClosingDate = $lastWithdrawDate;
        }

        // All transaction dates (which will not be selected as a transaction date further)
        /*$depositDates =  DB::table('mfn_savings_deposit')->where('softDel',0)->where('accountIdFk',$req->savingsAccountId)->pluck('depositDate')->toArray();

        $withdrawDates = DB::table('mfn_savings_withdraw')->where('softDel',0)->where('accountIdFk',$req->savingsAccountId)->pluck('withdrawDate')->toArray();
        $transactionDates = $depositDates + $withdrawDates;*/

        $softwareDate = Microfin::getSoftwareDateBranchWise($account->branchIdFk);
        $softwareDate = Carbon::parse($softwareDate)->format('d-m-Y');


        $data = array(
            'authorizedDeposit'     =>  $authorizedDeposit,
            'unAuthorizedDeposit'   =>  $unAuthorizedDeposit,
            'totalDeposit'          =>  $totalDeposit,
            'authorizedWithdraw'    =>  $authorizedWithdraw,
            'unAuthorizedWithdraw'  =>  $unAuthorizedWithdraw,
            'totalWithdraw'         =>  $totalWithdraw,
            'balance'               =>  $balance,
            'lastDepositDate'       =>  date('Y-m-d',strtotime($lastDepositDate)),
            'lastWithdrawDate'      =>  date('Y-m-d',strtotime($lastWithdrawDate)),
            'accDepositType'        =>  $account->depositTypeIdFk,
            'depositAmount'         =>  $account->fixedDepositAmount,
            'payableAmount'         =>  $payableAmount,
            'minClosingDate'        =>  $minClosingDate,
            'softwareDate'          =>  $softwareDate,
            /*'transactionDates'      =>  $transactionDates,*/
        );

        return response::json($data);
    }

    public function getSavingsDepositDetails(Request $req){
        $deposit = MfnSavingsDeposit::find($req->id);
        $member = DB::table('mfn_member_information')->where('id',$deposit->memberIdFk)->select('name','code')->first();
        $account  = MfnSavingsAccount::where('id',$deposit->accountIdFk)->select('savingsCode')->first();
        $entryBy = DB::table('hr_emp_general_info')->where('id',$deposit->entryByEmployeeIdFk)->select('emp_id','emp_name_english')->first();
            //Array for the First Coloum
        $tFirst = array(
            'Member Name:'          => $member->name,
            'Member Code:'          => $member->code,
            'Savings Code:'         => $account->savingsCode,
            'Entry By:'             => @$entryBy->emp_id.'-'.@$entryBy->emp_name_english,
            'Transaction Date:'     => date('d-m-Y',strtotime($deposit->depositDate))
        );

        if ($deposit->paymentType=="Bank") {
            $bankObj = DB::table('acc_account_ledger')->where('id',$deposit->ledgerIdFk)->select('name','code')->first();
            $bank = $bankObj->name.' ['.$bankObj->code.']';
            $cheque = $deposit->chequeNumber;
        }
        else{
            $bank = '-';
            $cheque = '-';
        }

            //Array for the Second Coloum
        $tSecond = array(                  
            'Mode of Payment:'  => $deposit->paymentType,
            'Amount:'           => number_format($deposit->amount,2),
            'Bank:'             => $bank,
            'Cheque Number:'    => $cheque,
            '-'                 => '-',
        );

        $data = array(
            'deposit'  =>  $deposit,
            'tFirst'   =>  $tFirst,
            'tSecond'  =>  $tSecond
        );

        return response::json($data);
    }

    public function getSavingsWithdrawDetails(Request $req){
        $withdraw = MfnSavingsWithdraw::find($req->id);
        $member = DB::table('mfn_member_information')->where('id',$withdraw->memberIdFk)->select('name','code')->first();
        $account  = MfnSavingsAccount::where('id',$withdraw->accountIdFk)->select('savingsCode')->first();
        $entryBy = DB::table('hr_emp_general_info')->where('id',$withdraw->entryByEmployeeIdFk)->select('emp_id','emp_name_english')->first();
            //Array for the First Coloum
        $tFirst = array(
            'Member Name:'          => $member->name,
            'Member Code:'          => $member->code,
            'Savings Code:'         => $account->savingsCode,
            'Entry By:'             => @$entryBy->emp_id.'-'.@$entryBy->emp_name_english,
            'Transaction Date:'     => date('d-m-Y',strtotime($withdraw->withdrawDate))
        );

        if ($withdraw->paymentType=="Bank") {
            $bankObj = DB::table('acc_account_ledger')->where('id',$withdraw->ledgerIdFk)->select('name','code')->first();
            $bank = $bankObj->name.' ['.$bankObj->code.']';
            $cheque = $withdraw->chequeNumber;
        }
        else{
            $bank = '-';
            $cheque = '-';
        }

            //Array for the Second Coloum
        $tSecond = array(                  
            'Mode of Payment:'  => $withdraw->paymentType,
            'Amount:'           => number_format($withdraw->amount,2),
            'Bank:'             => $bank,
            'Cheque Number:'    => $cheque,
            '-'                 => '-',
        );

        $data = array(
            'withdraw'  =>  $withdraw,
            'tFirst'    =>  $tFirst,
            'tSecond'   =>  $tSecond
        );

        return response::json($data);
    }

        // Savings Status Report
    public function getMfnSavingsStatusReportData(Request $req){
            //return response::json($req->filMemberCode);

        $userBarnchId = Auth::user()->branchId;
        $softwareDate = GetSoftwareDate::getSoftwareDate();

            //$members = MfnMemberInformation::active()->where('branchId',$userBarnchId);
        $memberIdsFromSavingsAccount = MfnSavingsAccount::active()->where('branchIdFk',$userBarnchId)->where('accountOpeningDate','<=',$softwareDate)->pluck('memberIdFk')->toArray();
        $members = DB::table('mfn_member_information')->where('softDel',0)->where('status',1)->where('branchId',$userBarnchId)->whereIn('id',$memberIdsFromSavingsAccount)->where('admissionDate','<=',$softwareDate);
        $savingAccounts = MfnSavingsAccount::active()->where('branchIdFk',$userBarnchId)->where('accountOpeningDate','<=',$softwareDate);

        if($req->samity!=''){
            $members = $members->where('samityId',$req->samity);
        }
        if ($req->filMemberCode!='') {                
            $members = $members->where('code', $req->filMemberCode);
        }


        if($req->filDateTo!=''){
            $dateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
            $savingAccounts = $savingAccounts->where('accountOpeningDate','<=',$dateTo);
        }

        $membersArray = clone($members);
        $membersArray = $membersArray->pluck('id')->toArray();

        $members = $members->get();

        $savingAccounts = $savingAccounts->whereIn('memberIdFk',$membersArray)->get();


            // Get the savings account information
        $depositArray = array();
        $interestAmountArray = array();
        $refundArray = array();
        $balanceArray = array();
        foreach ($savingAccounts as $key => $savingAccount) {
            $deposit = MfnSavingsDeposit::active()->where('accountIdFk',$savingAccount->id);
            $withdraw = MfnSavingsWithdraw::active()->where('accountIdFk',$savingAccount->id);
            if ($req->filDateFrom!='') {
                $dateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');
                $deposit = $deposit->where('depositDate','>=',$dateFrom);
                $withdraw = $withdraw->where('withdrawDate','>=',$dateFrom);
            }
            if ($req->filDateTo!='') {
                $dateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
                $deposit = $deposit->where('depositDate','<=',$dateTo);
                $withdraw = $withdraw->where('withdrawDate','<=',$dateTo);
            }

            $depositAmount = $deposit->sum('amount');
            array_push($depositArray,number_format($depositAmount,2));

            if ($savingAccount->depositTypeIdFk==1 || $savingAccount->depositTypeIdFk==2) {
                $interestAmount = $depositAmount * $savingAccount->savingsInterestRate / 100 ;
            }
            else{
                $interestAmount = 0;
            }
                // this is zero now, interest will not be calculated here, it will come when interest is generated.
            $interestAmount = 0;
            array_push($interestAmountArray,number_format($interestAmount,2));

            $withdrawAmount = $withdraw->sum('amount');
            array_push($refundArray,number_format($withdrawAmount,2));

            $balance = $depositAmount + $interestAmount - $withdrawAmount;
            array_push($balanceArray,number_format($balance,2));
        }

            // Get the row span value for individual member
        $rowSpan = array();
        foreach ($members as $key => $member) {
            $numberOfSavingsAccounts = MfnSavingsAccount::active()->where('memberIdFk',$member->id)->count();
            array_push($rowSpan, $numberOfSavingsAccounts);
        }

        $data = array(
            'members'               => $members,
            'savingAccounts'        => $savingAccounts,
            'rowSpan'               => $rowSpan,
            'depositArray'          => $depositArray,
            'interestAmountArray'   => $interestAmountArray,
            'refundArray'           => $refundArray,
            'balanceArray'          => $balanceArray
        );

        return response::json($data);
    }

    public function getSavingsProductOnChangeMember(Request $req){

        $savingProducts = MfnSavingsProduct::active()->where('depositTypeIdFk','!=',1);

            // Get the products which has been opened by the member and are multi savings is not allowed
        $savingsAccountsProducts = MfnSavingsAccount::active()->where('memberIdFk',$req->memberId)->pluck('savingsProductIdFk')->toArray();

        $savingProducts = $savingProducts->where(function ($query) use ($savingsAccountsProducts) {
            $query->whereNotIn('id', $savingsAccountsProducts)
            ->orWhere('isMultipleSavingAllowed',1);
        });

        if(isset($req->savingsProductId)){
            $savingProducts = $savingProducts->orWhere('id',$req->savingsProductId);
        }

        $savingProducts = $savingProducts->select('id','shortName','code')
        ->get();

        return response::json($savingProducts);
    }

    public function getSavingsProductOnChangeMemberForOpening(Request $req){

        $savingProducts = MfnSavingsProduct::active();

            // Get the products which has been opened by the member and are multi savings is not allowed
        $savingsAccountsProducts = MfnSavingsAccount::active()->where('memberIdFk',$req->memberId)->pluck('savingsProductIdFk')->toArray();

        $savingProducts = $savingProducts->where(function ($query) use ($savingsAccountsProducts) {
            $query->whereNotIn('id', $savingsAccountsProducts)
            ->orWhere('isMultipleSavingAllowed',1);
        });

        if(isset($req->savingsProductId)){
            $savingProducts = $savingProducts->orWhere('id',$req->savingsProductId);
        }

        $savingProducts = $savingProducts->select('id','shortName','code')
        ->get();
        return response::json($savingProducts);
    }

    public function getPayableAmountOnChangePeriodOts(Request $req){
        $payableAmount = DB::table('mfn_savings_fdr_product_repay_amount')->where('softDel',0)->where('status',1)->where('productIdFK',$req->productId)->where('monthlyAmount',$req->depositAmount)->where('year',$req->year)->where('month',$req->month)->value('repayAmount');
        $payableAmount = number_format($payableAmount,2);

        return response::json($payableAmount);
    }

    public function getSavingsClosingDetails(Request $req){            
        $savingsClosing = DB::table('mfn_savings_closing')->where('id',$req->id)->first();
        $member = DB::table('mfn_member_information')->where('id',$savingsClosing->memberIdFk)->select('name','code')->first();
        $savingsAccount = DB::table('mfn_savings_account')->where('id',$savingsClosing->accountIdFk)->select('savingsCode')->first();
        $entryBy = DB::table('hr_emp_general_info')->where('id',$savingsClosing->entryByEmployeeIdFk)->value('emp_name_english');

        $ledger = DB::table('acc_account_ledger')->where('id',$savingsClosing->ledgerIdFk)->select('id','name')->first();

            //Array for the First Coloum
        $tFirst = array(
            'Member Name:'    => $member->name,
            'Member Code:'    => $member->code,
            'Savings Code:'   => $savingsAccount->savingsCode,
            'Closing Date:'   => date('d-m-Y',strtotime($savingsClosing->closingDate)),
            'Entry By:'       => $entryBy
        );

            //Array for the Second Coloum
        $tSecond = array(   
            'Deposit Amount:'        => number_format($savingsClosing->depositAmount,2),
            'Payable Amount:'        => number_format($savingsClosing->payableAmount,2),
            'Total Saving Interest:' => number_format($savingsClosing->totalSavingInterest,2),
            'Closing Amount:'        => number_format($savingsClosing->closingAmount,2),
            'Payment Method:'        => $savingsClosing->paymentType,
        );

        if ($savingsClosing->paymentType=='Bank') {
            $bankName = $ledger->name;

            $tFirst = $tFirst + array(
                'Bank' => $bankName
            );

            $tSecond = $tSecond + array(
                'Cheque Number' => $savingsClosing->chequeNumber
            );
        }

        $data = array(
            'tFirst'    =>  $tFirst,
            'tSecond'   =>  $tSecond,
            'savingsAccountId' => $savingsClosing->accountIdFk,
            'ledgerId' => $savingsClosing->ledgerIdFk,
            'ledgerId' => $ledger->id,
            'chequeNumber' => $savingsClosing->chequeNumber,
            'totalSavingInterest' => $savingsClosing->totalSavingInterest
        );

        return response::json($data);
    }

    public function getSavingsClosingAccountBalanceInfoToUpdate(Request $req){

        $closing = DB::table('mfn_savings_closing')->where('id',$req->closingId)->first();

        $authorizedDeposit = MfnOpeningSavingsAccountInfo::where('savingsAccIdFk', $req->savingsAccountId)->where('softDel', '=', 0)->sum('openingBalance');
        $authorizedDeposit += MfnSavingsDeposit::active()->where('accountIdFk',$req->savingsAccountId)->where('isAuthorized',1)->sum('amount');
        $unAuthorizedDeposit = (float) MfnSavingsDeposit::active()->where('accountIdFk',$req->savingsAccountId)->where('isAuthorized',0)->sum('amount');

        $totalDeposit = $authorizedDeposit + $unAuthorizedDeposit;

        $authorizedWithdraw = MfnSavingsWithdraw::active()->where('accountIdFk',$req->savingsAccountId)->where('isAuthorized',1)->where('withdrawDate','!=',$closing->closingDate)->sum('amount');
        $unAuthorizedWithdraw = MfnSavingsWithdraw::active()->where('accountIdFk',$req->savingsAccountId)->where('isAuthorized',0)->where('withdrawDate','!=',$closing->closingDate)->sum('amount');

        $totalWithdraw = $authorizedWithdraw + $unAuthorizedWithdraw;

        $balance = $totalDeposit - $totalWithdraw;

        $account = MfnSavingsAccount::where('id',$req->savingsAccountId)->select('accountMatureDate','payableAmount','fixedDepositAmount','depositTypeIdFk')->first();

            // get payable amount for saving account closing
        $payableAmount = 0;
        if ($account->depositTypeIdFk==4) {
            $matureDate = Carbon::parse($account->accountMatureDate);
            if ($matureDate->gte(Carbon::toDay())) {
                $payableAmount = $account->payableAmount;
            }
            else{
                $payableAmount = $account->fixedDepositAmount;
            }
        }

        $data = array(
            'authorizedDeposit'     =>  $authorizedDeposit,
            'unAuthorizedDeposit'   =>  $unAuthorizedDeposit,
            'totalDeposit'          =>  $totalDeposit,
            'authorizedWithdraw'    =>  $authorizedWithdraw,
            'unAuthorizedWithdraw'  =>  $unAuthorizedWithdraw,
            'totalWithdraw'         =>  $totalWithdraw,
            'balance'               =>  $balance,
            'accDepositType'        =>  $account->depositTypeIdFk,
            'depositAmount'         =>  $account->fixedDepositAmount,
            'payableAmount'         =>  $payableAmount
        );

        return response::json($data);
    }

    public function getBranchSoftwareOpeningDateBaseOnMember(Request $req){
        $memberBranchId = DB::table('mfn_member_information')->where('id',$req->memberId)->value('branchId');
        $branch = DB::table('gnr_branch')->where('id',$memberBranchId)->select('branchOpeningDate','softwareStartDate')->first();

        $data = array(
            'branchOpeningDate' => $branch->branchOpeningDate,
            'softwareStartDate' => $branch->softwareStartDate
        );            

        return response::json($data);
    }


    public function getBranchWiseSamityList($branchId){
        $samityList = DB::table('mfn_samity')
        ->where('softDel',0);
        if ($branchId!='' || $branchId!=null) {
            $samityList = $samityList->where('branchId',$branchId);
        }
        $samityList = $samityList->select(DB::raw("CONCAT(code,' - ',name) AS name"),'id')
        ->orderBy('code')
        ->pluck('name','id')
        ->all();
        return $samityList;

    }

    public function getSamityWiseMemeberDropDownList(Request $req){

        $userBranchId = Auth::user()->branchId;
        if ($userBranchId==1 && $req->samityId=='') {
            return '';
        }

        if ($userBranchId!=1) {
            $targetBranchId = $userBranchId;
        }
        else{
            $targetBranchId = (int) DB::table('mfn_samity')->where('id',$req->samityId)->value('branchId');
        }

        $branchSoftwareDate = MicroFin::getSoftwareDateBranchWise($targetBranchId);

        $members = DB::table('mfn_member_information')->where('softDel',0)->where('status',1)->where('branchId',$targetBranchId)->where('admissionDate','<=',$branchSoftwareDate);

        if ($req->samityId!='' || $req->samityId!=0) {
            $members = $members->where('samityId',$req->samityId);
        }
        $members = $members->orderBy('code')->select('id','name','code','branchId','samityId')->get();

        $branches = DB::table('gnr_branch')->select('id','name')->get();
        $samities = DB::table('mfn_samity')->select('id','name','workingAreaId')->get();
        $workingAreas = DB::table('gnr_working_area')->select('id','name')->get();

        $concatString = '';
        foreach ($members as $member) {
            $branchName = $branches->where('id',$member->branchId)->max('name');
            $samity = $samities->where('id',$member->samityId)->first();
            $workingAreaName = $workingAreas->where('id',$samity->workingAreaId)->max('name');
            $concatString = $concatString."<tr>
            <td memberId=".$member->id." branchId=".$member->branchId." samityId=".$member->samityId." workingAreaId=".$samity->workingAreaId." style='text-align: left;'>
            <span class='memberName' style='font-size: 11;font-weight: bold;'>".$member->name."</span> - 
            <span class='memberCode' style='font-size: 11;font-weight: bold;'>".$member->code."</span><br>
            Branch: ".$branchName."<br>
            Samity: ".$samity->name."<br>
            Working Area: ".$workingAreaName."
            </td>
            </tr>";
        }
        return response::json($concatString);
    }

    public function getBranchWiseMemeberDropDownList(Request $req){

        if ($req->branchId=='' || $req->branchId==null) {
            return '';
        }

        $branchSoftwareDate = MicroFin::getSoftwareDateBranchWise($req->branchId);

        $memebers = DB::table('mfn_member_information')
        ->where('softDel',0)
        ->where('Status',1)
        ->where('admissionDate','<=',$branchSoftwareDate)
        ->where('branchId',$req->branchId)
        ->orderBy('code')
        ->select('id','branchId','samityId','name','code')
        ->get();

        $branchName = DB::table('gnr_branch')->where('id',$req->branchId)->value('name');

        $samities = DB::table('mfn_samity')
        ->where('softDel',0)
        ->where('branchId',$req->branchId)
        ->select('id','name','workingAreaId')
        ->get();



        $workingAreas = DB::table('gnr_working_area')
        ->where('branchId',$req->branchId)
        ->select('id','name')
        ->get();

        $memberListText = '';
        foreach ($memebers as $member) {

            $samity = $samities->where('id',$member->samityId)->first();

            $workingAreaName = $workingAreas->where('id',$samity->workingAreaId)->max('name');

            $memberListText = $memberListText."<tr>
            <td memberId=".$member->id." branchId=".$member->branchId." samityId=".$member->samityId." workingAreaId=".$samity->workingAreaId." style='text-align: left;'>
            <span class='memberName' style='font-size: 11;font-weight: bold;'>".$member->name."</span> - 
            <span class='memberCode' style='font-size: 11;font-weight: bold;'>".$member->code."</span><br>
            Branch: ".$branchName."<br>
            Samity: ".$samity->name."<br>
            Working Area: ".$workingAreaName."
            </td>
            </tr>";
        }

        return response::json($memberListText);
    }

}