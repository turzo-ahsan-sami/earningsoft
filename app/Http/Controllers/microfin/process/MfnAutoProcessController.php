<?php

namespace App\Http\Controllers\microfin\process;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\microfin\MfnMemberType;
use App\Traits\CreateForm;
use App\microfin\savings\MfnSavingsAccount;
use App\microfin\member\MfnMemberInformation;
use App\microfin\savings\MfnSavingsDeposit;
use App\microfin\savings\MfnSavingsWithdraw;
use App\microfin\samity\MfnSamity;
use App\microfin\loan\MfnLoanCollection;
use App\microfin\loan\MfnLoanSchedule;
use App\microfin\process\AuthorizationInfo;
use App\microfin\process\MfnAutoProcess;
use Auth;
use App\Traits\GetSoftwareDate;
use App\Service\Service;


class MfnAutoProcessController extends Controller
{
    use CreateForm;
    use GetSoftwareDate;

    private $TCN;

    public function __construct()
    {

        $this->TCN = array(
            array('SL#', 70),
            array('Samity Name', 0),
            array('Field Officer', 0),
            array('Action', 80)
        );
    }

    public function index()
    {

        // $this->getMemberAttendence();

        $softwareDate = GetSoftwareDate::getSoftwareDate();

        $today = Carbon::parse($softwareDate);
        $weekDayNumber = $today->dayOfWeek == 6 ? 1 : $today->dayOfWeek + 2;
        $dayNumber = $today->day;

        $userBranchId = Auth::user()->branchId;
        $branchIdArray = Service::getEngagedBranchesByUserId(Auth::user()->id);


        // GET THE SAMITY IDS WHICH HAVE HOLIDAY TODAY.
        $holidaySamityIds = DB::table('mfn_setting_orgBranchSamity_holiday')
            ->where('softDel', 0)
            ->where('dateFrom', '<=', $softwareDate)
            ->where('dateTo', '>=', $softwareDate)
            ->pluck('samityIdFk')
            ->toArray();

        $samities = DB::table('mfn_samity')
            ->where('softDel', 0)
            //->where('branchId',$userBranchId)
            ->whereIn('branchId', $branchIdArray)
            ->where('openingDate', '<=', $softwareDate)
            ->whereNotIn('id', $holidaySamityIds)
            ->get();

        $samityDayChanges = DB::table('mfn_samity_day_change')
            ->whereIn('samityId', $samities->pluck('id'))
            ->where('effectiveDate', '>', $softwareDate)
            ->select('samityId', 'samityDayId', 'fixedDate')
            ->get();

        $samityDayChanges = $samityDayChanges->sortBy('effectiveDate')->unique('samityId');

        foreach ($samityDayChanges as $samityDayChange) {
            $samities->where('id', $samityDayChange->samityId)->first()->samityDayId = $samityDayChange->samityDayId;
            $samities->where('id', $samityDayChange->samityId)->first()->fixedDate = $samityDayChange->fixedDate;
        }

        $todaySamity = $samities->filter(function ($obj) use ($weekDayNumber, $dayNumber) {
            if ($obj->samityDayId == $weekDayNumber || $obj->fixedDate == $dayNumber) {
                return true;
            } else {
                return false;
            }
        });

        $todaySamity = $todaySamity->values();

        $todayString = $today->copy()->format('Y-m-d');

        $TCN = $this->TCN;
        $damageData = array(
            'TCN'           =>  $TCN,
            'todaySamity'   =>  $todaySamity,
            'todayString'   =>  $todayString,
            'softwareDate'  =>  $softwareDate
        );

        return view('microfin/process/autoProcess/viewAutoProcess', ['damageData' => $damageData]);
    }

    public function viewSamityAutoProcess(Request $req)
    {

        $samity = MfnSamity::where('id', $req->samityId)->first();

        $softwareDate = GetSoftwareDate::getSoftwareDate();

        $dbSavingAccounts = DB::table('mfn_savings_account')
            ->where('softDel', 0)
            ->where('status', 1)
            ->where('depositTypeIdFk', '!=', 4)
            ->where('accountOpeningDate', '<=', $softwareDate)
            ->where('samityIdFk', $req->samityId)
            ->get();

        $loanIdsHavingSheduleToday = DB::table('mfn_loan_schedule')
            ->where('softDel', 0)
            ->where('scheduleDate', $softwareDate)
            ->pluck('loanIdFk')->toArray();

        $memberIdsFromLoanAccounts = DB::table('mfn_loan')
            ->where('softDel', 0)
            ->where('status', 1)
            ->where('loanTypeId', 1)
            ->where('isLoanCompleted', 0)
            ->whereIn('id', $loanIdsHavingSheduleToday)
            ->where('disbursementDate', '<=', $softwareDate)
            ->where('samityIdFk', $req->samityId)
            ->pluck('memberIdFk')
            ->toArray();

        $memberIdsHavingAccounts = array_merge($dbSavingAccounts->pluck('memberIdFk')->toArray(), $memberIdsFromLoanAccounts);
        $memberIdsHavingAccounts = array_unique($memberIdsHavingAccounts);

        $members = MfnMemberInformation::active()->where('samityId', $req->samityId)->where('admissionDate', '<=', $softwareDate)->whereIn('id', $memberIdsHavingAccounts)->get();

        $today = Carbon::parse($softwareDate);

        $dayNames = array(
            '1' => 'Saturday',
            '2' => 'Sunday',
            '3' => 'Monday',
            '4' => 'Tuesday',
            '5' => 'Wednesday',
            '6' => 'Thursday',
            '7' => 'Fridayday'
        );

        $samityDayName = "";
        if ($samity->samityDayId > 0) {
            $samityDayName = $dayNames[$samity->samityDayId];
        } elseif ($samity->fixedDate > 0) {
            $samityDay = $today;
            $samityDay->day = $samity->fixedDate;
            $samityDayName = $samity->fixedDate; //$samityDay->format('l');
        }

        $dbLoanAccounts = DB::table('mfn_loan')
            ->where('softDel', 0)
            ->where('status', 1)
            ->where('isLoanCompleted', 0)
            ->where('loanTypeId', 1)
            ->whereIn('memberIdFk', $members->pluck('id'))
            ->whereIn('id', $loanIdsHavingSheduleToday)
            ->get();

        $dbSchedules = DB::table('mfn_loan_schedule')
            ->where('softDel', 0)
            ->whereIn('loanIdFk', $dbLoanAccounts->pluck('id'))
            ->select('loanIdFk', 'installmentAmount', 'scheduleDate')
            ->get();

        $dbLoanCollections = DB::table('mfn_loan_collection')
            ->where('softDel', 0)
            ->whereIn('loanIdFk', $dbLoanAccounts->pluck('id'))
            ->select('loanIdFk', 'amount', 'collectionDate')
            ->get();

        $openingLoanBalances = DB::table('mfn_opening_balance_loan')
            ->whereIn('loanIdFk', $dbLoanAccounts->pluck('id'))
            ->select('loanIdFk', 'paidLoanAmountOB', 'date')
            ->get();

        foreach ($openingLoanBalances as $openingLoanBalance) {
            $dbLoanCollections->push([
                'loanIdFk'  => $openingLoanBalance->loanIdFk,
                'amount'  => $openingLoanBalance->paidLoanAmountOB,
                'collectionDate'  => $openingLoanBalance->date
            ]);
        }

        $damageData = array(
            'softwareDate'      =>  $softwareDate,
            'samity'            =>  $samity,
            'samityDayName'     =>  $samityDayName,
            'members'           =>  $members,
            'softwareDate'      =>  $softwareDate,
            'dbLoanAccounts'    =>  $dbLoanAccounts,
            'dbSchedules'       =>  $dbSchedules,
            'dbLoanCollections' =>  $dbLoanCollections,
            'dbSavingAccounts'  =>  $dbSavingAccounts,
        );

        // check is data of today of yhis samity at mfn_auto_process_tra_authorization_info table, if exits in table then it means that transaction alreadly exits and all transaction will be updated.
        $hasTransactionToday = (int) DB::table('mfn_auto_process_tra_authorization_info')->where('samityIdFk', $samity->id)->where('date', $softwareDate)->value('id');
        if ($hasTransactionToday > 0) {

            $memberPresentsInfo = json_decode(MfnAutoProcess::where('date', $softwareDate)->where('samityIdFk', $samity->id)->value('memberAttendence'));
            if (count($memberPresentsInfo) < 1) {
                $memberPresentsInfo = [];
            }

            $damageData = $damageData + array(
                'memberPresentsInfo'    => $memberPresentsInfo
            );

            return view('microfin/process/autoProcess/editAutoProcessSamity', $damageData);
        } else {
            return view('microfin/process/autoProcess/viewAutoProcessSamity', $damageData);
        }
    }


    public function storeCollectionNDeposit(Request $req)
    {

        // dd(count($req->savingsAccId));

        $softwareDate = GetSoftwareDate::getSoftwareDate();

        if ($softwareDate != $req->softwareDate) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Branch date is modifiyed. Please try again later.'
            );

            return response::json($data);
        }

        $accIdsHavingAmount = array();

        foreach ($req->savingsAccId as $key => $accId) {
            if ($req->savingsAmount[$key] > 0) {
                array_push($accIdsHavingAmount, $accId);
            }
        }

        // IF IT ANY INEREST GENERATE AFTER OR THIS DATE, THEN IS COULD NOT BE UPDATED/DELETED
        $savAccIdsHavingInterests = DB::table('mfn_savings_deposit')
            ->where('softDel', 0)
            ->where('amount', '>', 0)
            ->whereIn('accountIdFk', $accIdsHavingAmount)
            ->where('depositDate', '>=', $softwareDate)
            ->where('paymentType', 'Interest')
            ->pluck('accountIdFk')
            ->toArray();

        if (count($savAccIdsHavingInterests) > 0) {
            $responseText = DB::table('mfn_savings_account')
                ->whereIn('id', $savAccIdsHavingInterests)
                ->select(DB::raw("GROUP_CONCAT(`savingsCode` SEPARATOR ', ') AS savingsCodes"))
                ->value('savingsCodes');
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Interest is generated at today or after this date for ' . $responseText
            );
            return response::json($data);
		}
		
		// IF ANY PRIMARY PRODUCT TRANSFERS TODAY OR AFTER THIS DATE, THEN IT COULD NOT BE DEPOSIT.
		$memberIds = DB::table('mfn_savings_account')
			->whereIn('id', $accIdsHavingAmount)
			->pluck('memberIdFk')
			->toArray();

		$memberHavingsProductTransfer = DB::table('mfn_loan_primary_product_transfer')
			->where('softDel', 0)
			->whereIn('memberIdFk', $memberIds)
			->where('transferDate', '>=', $softwareDate)
			->pluck('memberIdFk')
			->toArray();

		if (count($memberHavingsProductTransfer) > 0) {
			$responseText = DB::table('mfn_member_information')
				->whereIn('id', $memberHavingsProductTransfer)
				->select(DB::raw("GROUP_CONCAT(`code` SEPARATOR ', ') AS memberCodes"))
				->value('memberCodes');
			$data = array(
				'responseTitle' =>  'Warning!',
				'responseText'  =>  'Primary Product Tranfer is detected at today or after this date for ' . $responseText
			);
			return response::json($data);
		}

        // CHECK NEGETIVE OUTSTANDING
        $isOverPaymentoccured = 0;
        $negetiveBalanceLonCodes = '';

        if (is_array($req->loanAccId)) {

            foreach ($req->loanAccId as $key => $loanAcc) {

                $loan = DB::table('mfn_loan')->where('id', $loanAcc)->first();

                // CHEQUE THAT THE LOAN OUTSTANDING IS GOING TO BE NEGETIVE OR NOT, IF SO GIVE ALERT MESSAGE
                $totalCollectionAmount = MfnLoanCollection::active()->where('loanIdFk', $loan->id)->sum('amount');
                $totalCollectionAmount += DB::table('mfn_opening_balance_loan')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('paidLoanAmountOB');

                if ($totalCollectionAmount + (float) $req->loanAmount[$key] > $loan->totalRepayAmount) {
                    $isOverPaymentoccured = 1;
                    $negetiveBalanceLonCodes = $negetiveBalanceLonCodes . ' ' . $loan->loanCode . ' ';
                }
            }

            if ($isOverPaymentoccured == 1) {
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Overpayment detected for ' . $negetiveBalanceLonCodes
                );

                return response::json($data);
            }

            // IF ANY LOAN COLLECTION EXISTS FROM REGULAR COLLECTION (WHICH IS NOT AUTOPROCESS COLLECTION) THEN GIVE AN ERROR MESSAGE
            $regularCollectionLoanIds = DB::table('mfn_loan_collection')->where('collectionDate', $softwareDate)->where('softDel', 0)->where('amount', '>', 0)->where('softDel', 0)->where('isFromAutoProcess', 0)->whereIn('loanIdFk', $req->loanAccId)->pluck('loanIdFk')->toArray();

            if (count($regularCollectionLoanIds) > 0) {
                $loanCodes = DB::table('mfn_loan')->whereIn('id', $regularCollectionLoanIds)->pluck('loanCode')->toArray();
                $loanCodes = implode(',', $loanCodes);
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Regular Collection detected for ' . $loanCodes
                );

                return response::json($data);
            }
        }
        // END CHECKING NEGETIVE OUTSTANDING

        DB::beginTransaction();

        try {

            // here branch and samity are got by the savings account id
            $savings = DB::table('mfn_savings_account')->where('id', $req->savingsAccId[0])->select('branchIdFk', 'samityIdFk')->first();

            $errorFlag = 0;
            $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId', 4)->where('isGroupHead', 0)->value('id');

            // store savings deposit
            foreach ($req->savingsAccId as $key => $savingsAcc) {


                $balanceBeforeDeposit = DB::table('mfn_savings_deposit')->where('softDel', 0)->where('accountIdFk', $savingsAcc)->where('depositDate', '<', $softwareDate)->sum('amount');

                $account = MfnSavingsAccount::where('id', $savingsAcc)->select('branchIdFk', 'samityIdFk', 'savingsProductIdFk')->first();

                $primaryProductId = DB::table('mfn_member_information')->where('id', $req->savingsMemberId[$key])->value('primaryProductId');

                $depositId = MfnSavingsDeposit::where('depositDate', $softwareDate)->where('accountIdFk', $savingsAcc)->where('isFromAutoProcess', 1)->value('id');

                if ($depositId > 0) {
                    $deposit = MfnSavingsDeposit::find($depositId);
                } else {
                    $deposit = new MfnSavingsDeposit;
                }

                /*if ($account->savingsProductIdFk==0 || $primaryProductId==0) {
                    continue;
                    $errorFlag = 1;
                }*/

                $deposit->memberIdFk            = $req->savingsMemberId[$key];
                $deposit->branchIdFk            = $savings->branchIdFk;
                $deposit->samityIdFk            = $savings->samityIdFk;
                $deposit->accountIdFk           = $savingsAcc;
                $deposit->productIdFk           = $account->savingsProductIdFk;
                $deposit->primaryProductIdFk    = $primaryProductId;
                $deposit->amount                = $req->savingsAmount[$key];
                $deposit->balanceBeforeDeposit  = $balanceBeforeDeposit;
                $deposit->depositDate           = Carbon::parse($softwareDate);
                $deposit->paymentType           = 'Cash';
                $deposit->ledgerIdFk            = $ledgerId; // Cash In Hand ledger id
                $deposit->chequeNumber          = '';
                $deposit->entryByEmployeeIdFk   = Auth::user()->emp_id_fk;
                $deposit->isFromAutoProcess     = 1;
                $deposit->createdAt             = Carbon::now();
                $deposit->softDel               = 0;
                $deposit->save();
            }


            // store loan collection
            if (is_array($req->loanAccId)) {

                foreach ($req->loanAccId as $key => $loanAcc) {

                    $loan = DB::table('mfn_loan')->where('id', $loanAcc)->first();

                    $principalAmount = round($req->loanAmount[$key] / (float) $loan->interestRateIndex, 5);
                    $interestAmount = round($req->loanAmount[$key] - $principalAmount, 5);
                    $pastCollectionAmount = DB::table('mfn_loan_collection')->where('loanIdFk', $loan->id)->sum('amount');

                    $installmentNo = DB::table('mfn_loan_schedule')->where('softDel', 0)->where('loanIdFk', $loan->id)->where('scheduleDate', '<=', $softwareDate)/*->where('newScheduleDate','<=',$softwareDate)*/->where('isCompleted', 0)->orderBy('installmentSl')->pluck('installmentSl')->toArray();
                    if (count($installmentNo) < 1) {
                        $installmentNo = DB::table('mfn_loan_schedule')->where('softDel', 0)->where('loanIdFk', $loan->id)->where('isCompleted', 0)->min('installmentSl');
                        $installmentNo = [$installmentNo];
                    }

                    $primaryProductId =  DB::table('mfn_member_information')
                        ->where('id', $loan->memberIdFk)
                        ->value('primaryProductId');

                    $loanCollectionId = MfnLoanCollection::where('collectionDate', $softwareDate)->where('loanIdFk', $loanAcc)->where('isFromAutoProcess', 1)->value('id');

                    if ($loanCollectionId > 0) {
                        $loanCollection = MfnLoanCollection::find($loanCollectionId);
                    } else {
                        $loanCollection = new MfnLoanCollection;
                    }

                    $loanCollection->loanIdFk               = $loanAcc;
                    $loanCollection->productIdFk            = $loan->productIdFk;
                    $loanCollection->primaryProductIdFk     = $loan->primaryProductIdFk;
                    $loanCollection->loanTypeId             = $loan->loanTypeId;
                    $loanCollection->memberIdFk             = $loan->memberIdFk;
                    $loanCollection->branchIdFk             = $loan->branchIdFk;
                    $loanCollection->samityIdFk             = $loan->samityIdFk;
                    $loanCollection->collectionDate         = Carbon::parse($softwareDate);
                    $loanCollection->amount                 = $req->loanAmount[$key];
                    $loanCollection->principalAmount        = $principalAmount;
                    $loanCollection->interestAmount         = $interestAmount;
                    $loanCollection->paymentType            = 'Cash';
                    $loanCollection->ledgerIdFk             = $ledgerId; // Cash In Hand ledger id
                    $loanCollection->chequeNumber           = '';
                    $loanCollection->installmentNo          = implode(',', $installmentNo);
                    $loanCollection->isFromAutoProcess      = 1;
                    $loanCollection->entryByEmployeeIdFk    = Auth::user()->emp_id_fk;
                    $loanCollection->createdAt              = Carbon::now();
                    $loanCollection->softDel                = 0;
                    $loanCollection->save();

                    $totalCollectionAmount = MfnLoanCollection::active()->where('loanIdFk', $loan->id)->sum('amount');

                    // update the status if applicable
                    $this->updateStatus($loan, $totalCollectionAmount, $softwareDate);

                    // mark the completed installments

                    $shedules = MfnLoanSchedule::active()->where('loanIdFk', $loan->id)->get();

                    foreach ($shedules as $key => $shedule) {
                        if ($totalCollectionAmount >= $shedule->installmentAmount) {
                            $shedule->isCompleted = 1;
                            $shedule->isPartiallyPaid = 0;
                            $shedule->partiallyPaidAmount = 0;
                            $shedule->save();
                        } elseif ($totalCollectionAmount > 0) {
                            $shedule->isCompleted = 0;
                            $shedule->isPartiallyPaid = 1;
                            $shedule->partiallyPaidAmount = $totalCollectionAmount;
                            $shedule->save();
                        } else {
                            $shedule->isCompleted = 0;
                            $shedule->isPartiallyPaid = 0;
                            $shedule->partiallyPaidAmount = 0;
                            $shedule->save();
                        }

                        $totalCollectionAmount = $totalCollectionAmount - $shedule->installmentAmount;
                    }
                }
            }

            // store authorization information
            $authorizationInfoId = (int) DB::table('mfn_auto_process_tra_authorization_info')
                ->where('date', $softwareDate)
                ->where('samityIdFk', $savings->samityIdFk)
                ->where('branchIdFk', $savings->branchIdFk)
                ->value('id');
            if ($authorizationInfoId > 0) {
                $authorizationInfo = AuthorizationInfo::find($authorizationInfoId);
            } else {
                $authorizationInfo = new AuthorizationInfo;
            }

            $authorizationInfo->date                = Carbon::parse($softwareDate);
            $authorizationInfo->samityIdFk          = $savings->samityIdFk;
            $authorizationInfo->branchIdFk          = $savings->branchIdFk;
            $authorizationInfo->isAuthorized        = 0;
            $authorizationInfo->isReUnauthorized    = 0;
            $authorizationInfo->createdAt           = Carbon::now();
            $authorizationInfo->save();

            // store auto process info
            $mfnAutoProcessId = (int) DB::table('mfn_auto_process_info')
                ->where('date', $softwareDate)
                ->where('samityIdFk', $savings->samityIdFk)
                ->where('branchIdFk', $savings->branchIdFk)
                ->value('id');

            if ($mfnAutoProcessId > 0) {
                $autoProcess = MfnAutoProcess::find($mfnAutoProcessId);
            } else {
                $autoProcess = new MfnAutoProcess;
            }

            $autoProcess->date                  = Carbon::parse($softwareDate);
            $autoProcess->samityIdFk            = $savings->samityIdFk;
            $autoProcess->branchIdFk            = $savings->branchIdFk;
            $autoProcess->totalCollectionAmount = $req->loanTotalAmount;
            $autoProcess->totalDepositAmount    = $req->savingsTotalAmount;
            $autoProcess->memberAttendence      = json_encode(array_map(null, $req->presentMemberId, $req->isPresentText));
            $autoProcess->createdAt             = Carbon::now();
            $autoProcess->save();

            DB::commit();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data saved successfully.'
            );

            return response::json($data);
        } catch (\Exception $e) {
            DB::rollback();
            $data = array(
                'responseTitle'  =>  'Warning!',
                'responseText'   =>  'Something went wrong. Please try again.'
            );
            return response::json($data);
        }
    }

    public function updateCollectionNDeposit(Request $req)
    {
        $softwareDate = GetSoftwareDate::getSoftwareDate();

        if ($softwareDate != $req->softwareDate) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Branch date is modifiyed. Please try again later.'
            );
            return response::json($data);
        }

        // IF IT ANY INEREST GENERATE AFTER OR THIS DATE, THEN IS COULD NOT BE UPDATED/DELETED
        $accIdsHavingAmountChanged = array();
        $savAutoProcessAmounts = DB::table('mfn_savings_deposit')
            ->whereIn('accountIdFk', $req->savingsAccId)
            ->where('depositDate', $softwareDate)
            ->where('isFromAutoProcess', 1)
            ->select('accountIdFk', 'amount')
            ->get();

        foreach ($req->savingsAccId as $key => $accId) {
            // IF THE SAVINGS AMOUNT HAS BEEN CHANGED, THEN THIS SHOULD BE COUNTABLE
            if ($req->savingsAmount[$key] != $savAutoProcessAmounts->where('accountIdFk', $accId)->sum('amount')) {
                array_push($accIdsHavingAmountChanged, $accId);
            }
        }

        $savAccIdsHavingInterests = DB::table('mfn_savings_deposit')
            ->where('softDel', 0)
            ->where('amount', '>', 0)
            // ->whereIn('accountIdFk',$req->savingsAccId)
            ->whereIn('accountIdFk', $accIdsHavingAmountChanged)
            ->where('depositDate', '>=', $softwareDate)
            ->where('paymentType', 'Interest')
            ->pluck('accountIdFk')
            ->toArray();

        if (count($savAccIdsHavingInterests) > 0) {
            $responseText = DB::table('mfn_savings_account')
                ->whereIn('id', $savAccIdsHavingInterests)
                ->select(DB::raw("GROUP_CONCAT(`savingsCode` SEPARATOR ', ') AS savingsCodes"))
                ->value('savingsCodes');
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Interest is generated at today or after this date for ' . $responseText
            );
            return response::json($data);
		}

		// IF ANY PRIMARY PRODUCT TRANSFERS TODAY OR AFTER THIS DATE, THEN IT COULD NOT BE DEPOSIT.
		$memberIds = DB::table('mfn_savings_account')
			->whereIn('id', $accIdsHavingAmountChanged)
			->pluck('memberIdFk')
			->toArray();

		$memberHavingsProductTransfer = DB::table('mfn_loan_primary_product_transfer')
			->where('softDel', 0)
			->whereIn('memberIdFk', $memberIds)
			->where('transferDate', '>=', $softwareDate)
			->pluck('memberIdFk')
			->toArray();

		if (count($memberHavingsProductTransfer) > 0) {
			$responseText = DB::table('mfn_member_information')
				->whereIn('id', $memberHavingsProductTransfer)
				->select(DB::raw("GROUP_CONCAT(`code` SEPARATOR ', ') AS memberCodes"))
				->value('memberCodes');
			$data = array(
				'responseTitle' =>  'Warning!',
				'responseText'  =>  'Primary Product Tranfer is detected at today or after this date for ' . $responseText
			);
			return response::json($data);
		}

        // CHECK NEGETIVE OUTSTANDING FOR LOAN
        $isOverPaymentoccured = 0;
        $negetiveBalanceLonCodes = '';

        if (is_array($req->loanAccId)) {

            foreach ($req->loanAccId as $key => $loanAcc) {

                $loan = DB::table('mfn_loan')->where('id', $loanAcc)->first();

                // CHEQUE THAT THE LOAN OUTSTANDING IS GOING TO BE NEGETIVE OR NOT, IF SO GIVE ALERT MESSAGE
                $loanCollectionId = (int) MfnLoanCollection::where('collectionDate', $softwareDate)->where('loanIdFk', $loan->id)->where('isFromAutoProcess', 1)->value('id');
                $totalCollectionAmount = MfnLoanCollection::active()->where('id', '!=', $loanCollectionId)->where('loanIdFk', $loan->id)->sum('amount');
                $totalCollectionAmount += DB::table('mfn_opening_balance_loan')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('paidLoanAmountOB');

                if ($totalCollectionAmount + (float) $req->loanAmount[$key] > $loan->totalRepayAmount) {
                    $isOverPaymentoccured = 1;
                    $negetiveBalanceLonCodes = $negetiveBalanceLonCodes . ' ' . $loan->loanCode . ' ';
                }
            }

            if ($isOverPaymentoccured == 1) {
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Overpayment detected for ' . $negetiveBalanceLonCodes
                );

                return response::json($data);
            }

            // IF ANY LOAN COLLECTION EXISTS FROM REGULAR COLLECTION (WHICH IS NOT AUTOPROCESS COLLECTION) THEN GIVE AN ERROR MESSAGE
            $regularCollectionLoanIds = DB::table('mfn_loan_collection')->where('collectionDate', $softwareDate)->where('softDel', 0)->where('amount', '>', 0)->where('softDel', 0)->where('isFromAutoProcess', 0)->whereIn('loanIdFk', $req->loanAccId)->pluck('loanIdFk')->toArray();

            if (count($regularCollectionLoanIds) > 0) {
                $loanCodes = DB::table('mfn_loan')->whereIn('id', $regularCollectionLoanIds)->pluck('loanCode')->toArray();
                $loanCodes = implode(',', $loanCodes);
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Regular Collection detected for ' . $loanCodes
                );

                return response::json($data);
            }
        }
        // END CHECKING NEGETIVE OUTSTANDING

        // CHECK NEGETIVE OUTSTANDING FOR SAVINGS
        $isOverPaymentoccured = 0;
        $negetiveBalanceSavingsCodes = '';
        foreach ($req->savingsAccId as $key => $savingsAcc) {

            $account = MfnSavingsAccount::where('id', $savingsAcc)->select('id', 'savingsProductIdFk', 'savingsCode')->first();

            // IF DELETING THIS DEPOSIT MAKES THE ACCOUNT BALANCE NEGETIVE THAN IT COULD NOT BE DELETED.
            $depositAmount = DB::table('mfn_savings_deposit')->where('softDel', 0)->where('accountIdFk', $account->id)->sum('amount');
            $depositAmount += DB::table('mfn_opening_savings_account_info')->where('savingsAccIdFk', $account->id)->sum('openingBalance');
            $withdrawAmount = DB::table('mfn_savings_withdraw')->where('softDel', 0)->where('accountIdFk', $account->id)->sum('amount');

            $depositId = MfnSavingsDeposit::where('depositDate', $softwareDate)->where('accountIdFk', $savingsAcc)->where('isFromAutoProcess', 1)->value('id');

            if ($depositId > 0) {
                $deposit = MfnSavingsDeposit::find($depositId);

                $balace = $depositAmount - $withdrawAmount;

                if ($balace + (float) $req->savingsAmount[$key] - $deposit->amount < 0) {
                    $isOverPaymentoccured = 1;
                    $negetiveBalanceSavingsCodes = $negetiveBalanceSavingsCodes . ' ' . $account->savingsCode . ' ';
                }
            }
        }

        if ($isOverPaymentoccured == 1) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Negetive Savings Status detected for ' . $negetiveBalanceSavingsCodes
            );

            return response::json($data);
        }

        // END CHECKING NEGETIVE OUTSTANDING SAVINGS

        DB::beginTransaction();

        try {
            // HERE BRANCH IS GOT BY THE SAVINGS ACCOUNT ID
            $savings = DB::table('mfn_savings_account')->where('id', $req->savingsAccId[0])->select('branchIdFk', 'samityIdFk')->first();
            $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId', 4)->where('isGroupHead', 0)->value('id');

            // STORE SAVINGS DEPOSIT
            foreach ($req->savingsAccId as $key => $savingsAcc) {

                $account = MfnSavingsAccount::where('id', $savingsAcc)->select('savingsProductIdFk')->first();

                $balanceBeforeDeposit = DB::table('mfn_savings_deposit')->where('softDel', 0)->where('accountIdFk', $savingsAcc)->where('depositDate', '<', $softwareDate)->sum('amount');

                $depositId = MfnSavingsDeposit::where('depositDate', $softwareDate)->where('accountIdFk', $savingsAcc)->where('isFromAutoProcess', 1)->value('id');

                if ($depositId > 0) {
                    $deposit = MfnSavingsDeposit::find($depositId);
                } else {
                    $deposit = new MfnSavingsDeposit;
                }
                $primaryProductId = DB::table('mfn_member_information')->where('id', $req->savingsMemberId[$key])->value('primaryProductId');
                $ledgerId = DB::table('acc_account_ledger')->where('accountTypeId', 4)->where('isGroupHead', 0)->value('id');

                $deposit->memberIdFk            = $req->savingsMemberId[$key];
                $deposit->branchIdFk            = $savings->branchIdFk;
                $deposit->productIdFk           = $account->savingsProductIdFk;
                $deposit->primaryProductIdFk    = $primaryProductId;
                $deposit->samityIdFk            = $savings->samityIdFk;
                $deposit->accountIdFk           = $savingsAcc;
                $deposit->amount                = $req->savingsAmount[$key];
                $deposit->balanceBeforeDeposit  = $balanceBeforeDeposit;
                $deposit->depositDate           = Carbon::parse($softwareDate);
                $deposit->paymentType           = 'Cash';
                $deposit->ledgerIdFk            = $ledgerId; // Cash In Hand ledger id
                $deposit->chequeNumber          = '';
                $deposit->entryByEmployeeIdFk   = Auth::user()->emp_id_fk;
                $deposit->isFromAutoProcess     = 1;
                $deposit->createdAt             = Carbon::now();
                $deposit->softDel               = 0;
                $deposit->save();
            }

            // STORE LOAN COLLECTION
            if (is_array($req->loanAccId)) {

                foreach ($req->loanAccId as $key => $loanAcc) {

                    /* if ($req->loanAmount[$key]<=0) {
                        continue;
                    }*/

                    $loan = DB::table('mfn_loan')->where('id', $loanAcc)->first();

                    $principalAmount = round($req->loanAmount[$key] / (float) $loan->interestRateIndex, 5);
                    $interestAmount = round($req->loanAmount[$key] - $principalAmount, 5);
                    $pastCollectionAmount = DB::table('mfn_loan_collection')->where('loanIdFk', $loan->id)->sum('amount');



                    $loanCollectionId = MfnLoanCollection::where('collectionDate', $softwareDate)->where('loanIdFk', $loanAcc)->where('isFromAutoProcess', 1)->value('id');
                    if ($loanCollectionId > 0) {
                        $loanCollection = MfnLoanCollection::find($loanCollectionId);
                    } else {
                        $loanCollection = new MfnLoanCollection;
                    }

                    $loanCollection->loanIdFk               = $loanAcc;
                    $loanCollection->productIdFk            = $loan->productIdFk;
                    $loanCollection->primaryProductIdFk     = $loan->primaryProductIdFk;
                    $loanCollection->loanTypeId             = $loan->loanTypeId;
                    $loanCollection->memberIdFk             = $loan->memberIdFk;
                    $loanCollection->branchIdFk             = $loan->branchIdFk;
                    $loanCollection->samityIdFk             = $loan->samityIdFk;
                    $loanCollection->collectionDate         = Carbon::parse($softwareDate);
                    $loanCollection->amount                 = $req->loanAmount[$key];
                    $loanCollection->principalAmount        = $principalAmount;
                    $loanCollection->interestAmount         = $interestAmount;
                    $loanCollection->paymentType            = 'Cash';
                    $loanCollection->ledgerIdFk             = $ledgerId; // Cash In Hand ledger id
                    $loanCollection->chequeNumber           = '';
                    //$loanCollection->installmentNo          = implode(',',$installmentNo);
                    $loanCollection->isFromAutoProcess      = 1;
                    $loanCollection->entryByEmployeeIdFk    = Auth::user()->emp_id_fk;
                    $loanCollection->createdAt              = Carbon::now();
                    $loanCollection->softDel                = 0;
                    $loanCollection->save();

                    $totalCollectionAmount = MfnLoanCollection::active()->where('loanIdFk', $loan->id)->sum('amount');

                    // update the status if applicable
                    $this->updateStatus($loan, $totalCollectionAmount, $softwareDate);

                    // mark the completed installments

                    $shedules = MfnLoanSchedule::active()->where('loanIdFk', $loan->id)->get();

                    foreach ($shedules as $key => $shedule) {
                        if ($totalCollectionAmount >= $shedule->installmentAmount) {
                            $shedule->isCompleted = 1;
                            $shedule->isPartiallyPaid = 0;
                            $shedule->partiallyPaidAmount = 0;
                            $shedule->save();
                        } elseif ($totalCollectionAmount > 0) {
                            $shedule->isCompleted = 0;
                            $shedule->isPartiallyPaid = 1;
                            $shedule->partiallyPaidAmount = $totalCollectionAmount;
                            $shedule->save();
                        } else {
                            $shedule->isCompleted = 0;
                            $shedule->isPartiallyPaid = 0;
                            $shedule->partiallyPaidAmount = 0;
                            $shedule->save();
                        }

                        $totalCollectionAmount = $totalCollectionAmount - $shedule->installmentAmount;
                    }
                }
            }

            // store authorization information
            $authorizationInfoId = AuthorizationInfo::where('date', $softwareDate)->where('samityIdFk', $savings->samityIdFk)->max('id');
            if ($authorizationInfoId > 0) {
                $authorizationInfo = AuthorizationInfo::find($authorizationInfoId);
            } else {
                $authorizationInfo = new AuthorizationInfo;
            }
            /*$authorizationInfo = AuthorizationInfo::where('date',$softwareDate)->where('samityIdFk',$savings->samityIdFk)->first();*/
            $authorizationInfo->date                = Carbon::parse($softwareDate);
            $authorizationInfo->samityIdFk          = $savings->samityIdFk;
            $authorizationInfo->branchIdFk          = $savings->branchIdFk;
            $authorizationInfo->isAuthorized        = 0;
            $authorizationInfo->isReUnauthorized    = 0;
            $authorizationInfo->createdAt           = Carbon::now();
            $authorizationInfo->save();

            // store auto process info
            $autoProcessId = MfnAutoProcess::where('date', $softwareDate)->where('samityIdFk', $savings->samityIdFk)->max('id');
            if ($autoProcessId > 0) {
                $autoProcess = MfnAutoProcess::find($autoProcessId);
            } else {
                $autoProcess = new MfnAutoProcess;
            }
            /*$autoProcess = MfnAutoProcess::where('date',$softwareDate)->where('samityIdFk',$savings->samityIdFk)->first();*/
            $autoProcess->date                  = Carbon::parse($softwareDate);
            $autoProcess->samityIdFk            = $savings->samityIdFk;
            $autoProcess->branchIdFk            = $savings->branchIdFk;
            $autoProcess->totalCollectionAmount = $req->loanTotalAmount;
            $autoProcess->totalDepositAmount    = $req->savingsTotalAmount;
            $autoProcess->memberAttendence      = json_encode(array_map(null, $req->presentMemberId, $req->isPresentText));
            $autoProcess->createdAt             = Carbon::now();
            $autoProcess->save();

            DB::commit();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data updated successfully.'
            );

            return response::json($data);
        } catch (\Exception $e) {
            DB::rollback();
            $data = array(
                'responseTitle'  =>  'Warning!',
                'responseText'   =>  'Something went wrong. Please try again.'
            );
            return response::json($data);
        }
    }

    public function parseMemberAttendenceData()
    {
        $memberPresentsInfo = json_decode(MfnAutoProcess::where('date', '2017-12-24')->where('samityIdFk', 14)->value('memberAttendence'));

        foreach ($memberPresentsInfo as $key => $memberPresents) {
            echo 'Member ID: ' . $memberPresents[0] . '<br>';
            echo 'Attendence Status: ' . $memberPresents[1] . '<br><br>';
        }
    }

    public function updateStatus($loan, $totalCollectionAmount, $transactionDate)
    {
        $paidAmountOB = DB::table('mfn_opening_balance_loan')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('paidLoanAmountOB');
        $totalCollectionAmount += $paidAmountOB;

        if ($loan->totalRepayAmount <= $totalCollectionAmount) {
            DB::table('mfn_loan')->where('id', $loan->id)->update(['isLoanCompleted' => 1, 'loanCompletedDate' => $transactionDate]);
        } else {
            DB::table('mfn_loan')->where('id', $loan->id)->update(['isLoanCompleted' => 0, 'loanCompletedDate' => '0000-00-00']);
        }
    }
}
