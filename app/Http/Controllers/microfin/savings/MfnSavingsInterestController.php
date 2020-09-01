<?php

namespace App\Http\Controllers\microfin\savings;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Route;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\microfin\MfnMemberType;
use App\Traits\CreateForm;
use App\Traits\GetSoftwareDate;
use App\microfin\savings\MfnSavingsDeposit;
use App\microfin\savings\MfnSavingsWithdraw;
use App\microfin\savings\MfnSavingsAccount;
use App\microfin\samity\MfnSamity;
use App\microfin\member\MfnMemberInformation;
use Auth;

class MfnSavingsInterestController extends Controller {
    use CreateForm;
    use GetSoftwareDate;

    public function index() {

        $userBranchId = Auth::user()->branchId;
        if ($userBranchId == 1) {
            $branchList = ['' => 'All Branches'] + DB::table('gnr_branch')
            ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS branchName"), 'id')
            ->pluck('branchName','id')
            ->toArray();
        }
        else {
            $branchList = DB::table('gnr_branch')->where('id', $userBranchId)
            ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS branchName"), 'id')
            ->pluck('branchName','id')
            ->toArray();
        }

        $data = array(
            'userBranchId' => $userBranchId,
            'branchList'    =>  $branchList
        );

            // dd("This page is under maintenance! Please wait for next notice.");

        return view('microfin.savings.savingsInterest.filteringPart',$data);
    }

    public function loadMfnSamityList(Request $req) {
            // dd($req->filBranch);
        $userBranchId = Auth::user()->branchId;
        $branchId = $req->filBranch;
            // dd($branchId);

        if ($userBranchId == 1) {
            if($branchId) {
                $samityList = DB::table('mfn_samity')->where('status', 1)->where('branchId', $branchId)->select(DB::raw("CONCAT(code, ' - ', name) AS samityName"), 'id', 'samityDayId')->get();
            }
            else {
                $samityList = DB::table('mfn_samity')->where('status', 1)->select(DB::raw("CONCAT(code, ' - ', name) AS samityName"), 'id', 'samityDayId')->get();
            }
        }
        else {
            $samityList = DB::table('mfn_samity')->where('status', 1)->where('branchId', $userBranchId)->select(DB::raw("CONCAT(code, ' - ', name) AS samityName"), 'id', 'samityDayId')->get();
        }
            // dd($samityList);

        $weekDaysArr = Array(
            1 => 'SAT',
            2 => 'SUN',
            3 => 'MON',
            4 => 'TUE',
            5 => 'WED',
            6 => 'THU',
        );

        $savingsProducts = DB::table('mfn_saving_product')->where('interestCalFrequencyIdFk', 2)->select('id', 'name')->get();
        $effectiveDate = collect($this->collectAllMonthsOfFiYr($userBranchId))->max();
            // dd($effectiveDate);

        $savedInterestSamity = DB::table('mfn_savings_interest')
        ->whereIn('samityIdFk', $samityList->pluck('id')->toArray())
        ->whereIn('productIdFk', $savingsProducts->pluck('id')->toArray())
        ->where('effectiveDate', $effectiveDate)
        ->select('samityIdFk', 'interestSaveType', 'productIdFk', 'effectiveDate')
        ->get();

        $data = array(
            'samityList'                =>  $samityList,
            'weekDaysArr'               =>  $weekDaysArr,
            'savingsProducts'           =>  $savingsProducts,
            'savedInterestSamity'       =>  $savedInterestSamity
        );

            // dd($data);
        return view('microfin.savings.savingsInterest.viewSamityList', $data);
    }

    public function getMemberWiseAutoSavingsInterest($samityId, $savingsProductId) {

        $requestedBranchId = DB::table('mfn_samity')->where('id', $samityId)->value('branchId');
        $branchDate = DB::table('mfn_day_end')->where('branchIdFk', $requestedBranchId)->where('isLocked', 0)->value('date');
        $branchOpeningDate = DB::table('gnr_branch')->where('id', $requestedBranchId)->value('softwareStartDate');
        $branchDate = $branchDate == null ? $branchOpeningDate : $branchDate;
        $effectiveDate = collect($this->collectAllMonthsOfFiYr($requestedBranchId))->max();
            // dd($branchDate, $effectiveDate);

        $isSaveAllowed = $branchDate == $effectiveDate ? 1 : 0;

        $isSamitySaved = DB::table('mfn_savings_interest')
        ->where('samityIdFk', $samityId)
        ->where('productIdFk', $savingsProductId)
        ->where('effectiveDate', $effectiveDate)
        ->select('id', 'interestSaveType')
        ->first();

        $data = $this->interestCalculation($samityId, $savingsProductId);
            // dd($data);

        return view('microfin.savings.savingsInterest.viewMemberWiseAutoInterest', $data)->with('isSaved', $isSamitySaved)->with('isSaveAllowed', $isSaveAllowed);
    }

    public function getMemberWiseManualSavingsInterest($samityId, $savingsProductId) {

        $requestedBranchId = DB::table('mfn_samity')->where('id', $samityId)->value('branchId');
        $branchDate = DB::table('mfn_day_end')->where('branchIdFk', $requestedBranchId)->where('isLocked', 0)->value('date');
        $branchOpeningDate = DB::table('gnr_branch')->where('id', $requestedBranchId)->value('softwareStartDate');
        $branchDate = $branchDate == null ? $branchOpeningDate : $branchDate;
        $effectiveDate = collect($this->collectAllMonthsOfFiYr($requestedBranchId))->max();
            // dd($branchDate, $effectiveDate);

        $isSaveAllowed = $branchDate == $effectiveDate ? 1 : 0;

        $manuallySavedData = DB::table('mfn_savings_interest')
        ->where('samityIdFk', $samityId)
        ->where('productIdFk', $savingsProductId)
        ->where('effectiveDate', $effectiveDate)
        ->select('id', 'interestSaveType', 'monthWiseInterest', 'memberIdFk')
        ->get();

        $isSamitySaved = $manuallySavedData->first();

        if ($manuallySavedData->count() > 0) {

            foreach ($manuallySavedData as $key => $data) {
                $monthAndInterest[] = array(
                    'memberId'              => $data->memberIdFk,
                    'monthWiseInterest'     => json_decode($data->monthWiseInterest)
                );
            }

            $monthAndInterest = collect($monthAndInterest);
        }
        else {
            $monthAndInterest= collect();
        }

        $data = $this->interestCalculation($samityId, $savingsProductId);
            // dd($monthAndInterest);

        return view('microfin.savings.savingsInterest.viewMemberWiseManualInterest', $data)
        ->with('isSaved', $isSamitySaved)
        ->with('isSaveAllowed', $isSaveAllowed)
        ->with('monthAndInterest', $monthAndInterest);
    }



    public function saveAutoInterest(){
            // DB::beginTransaction();
            // try {
        $samityId = Input::get('samityId');
        $savingsProductId = Input::get('savingsProductId');
                // dd($savingsProductId);

        $calculatedData = $this->interestCalculation($samityId, $savingsProductId);
        $members = $calculatedData['members'];
        $closingBalance = $calculatedData['closingBalance'];
        $monthsArr = $calculatedData['monthsArr'];
        $effectiveDate = collect($monthsArr)->max();
        $totalInterest = 0;
                // dd($closingBalance);

                foreach ($members as $key => $member) { // member loop


                    $savAccIds = $closingBalance->where('memberId', $member->id)->unique('savingsAccId')->pluck('savingsAccId')->toArray();
                    // dd($savAccIds);

                    foreach ($savAccIds as $key => $savAccId) {
                        $sumAccountInterest = 0;

                        foreach ($closingBalance->where('memberId', $member->id)->where('savingsAccId', $savAccId) as $key => $value) { // balance info loop

                            // apply withdraw condition
                            if ($value['withdraw'] == 0) {
                                $monthsInterest = $value['monthsInterest'];
                                $averageBalance = $value['averageBalance'];
                            } else {
                                $monthsInterest = 0;
                                $averageBalance = 0;
                            }

                            // accounts total interest
                            $sumAccountInterest += $monthsInterest;

                            // memberInfo
                            $memberInfo = array(
                                'memberId' 			=> $member->id,
                                'savingsAccId' 		=> $value['savingsAccId'],
                            );

                            // monthlyInterest
                            $monthWiseInterest[] = array(
                                'month' 	=> $value['month'],
                                'balance' 	=> $value['averageBalance'],
                                'interest'  => $value['monthsInterest']
                            );

                        }  // balance info loop close

                        // collect data for submit
                        $dataForTable[] = array(
                            'memberInfo' 		=> $memberInfo,
                            'memberInterest' 	=> $monthWiseInterest,
                            'averageBalance'    => $averageBalance,
                            'finalInterest' 	=> $sumAccountInterest,
                        );
                        $monthWiseInterest = array();
                    }

                    // all accounts total interest
                    $totalInterest += $sumAccountInterest;

                }  // member loop close
                // dd($dataForTable);

                // dd($dataForTable);
                foreach ($dataForTable as $key => $data) {

                    $branchId = DB::table('mfn_samity')->where('id', $samityId)->value('branchId');
                    $memberId = $data['memberInfo']['memberId'];
                    $primaryProductId = DB::table('mfn_member_information')->where('id', $memberId)->value('primaryProductId');
                    $SavingsAccId = $data['memberInfo']['savingsAccId'];
                    $monthWiseInterest = json_encode($data['memberInterest']);
                    $ledgerId = DB::table('acc_mfn_av_config_saving')
                    ->where('loanProductId', $primaryProductId)
                    ->where('savingProductId', $savingsProductId)
                    ->value('ledgerIdForInterestProvision');

                    $tableArray = array(
                        'branchIdFk'            => $branchId,
                        'samityIdFk'            => $samityId,
                        'memberIdFk'            => $memberId,
                        'primaryProductIdFk'    => $primaryProductId,
                        'productIdFk'           => $savingsProductId,
                        'accIdFk'               => $SavingsAccId,
                        'date'                  => Carbon::now()->format('Y-m-d'),
                        'monthWiseInterest'     => $monthWiseInterest,
                        'balanceBefore'         => $data['averageBalance'],
                        'interestAmount'        => $data['finalInterest'],
                        'effectiveDate'         => $effectiveDate,
                        'interestSaveType'      => 0,
                    );
                    // dd($tableArray);


                    $depositTableArray = array(
                        'branchIdFk'            => $branchId,
                        'samityIdFk'            => $samityId,
                        'memberIdFk'            => $memberId,
                        'primaryProductIdFk'    => $primaryProductId,
                        'productIdFk'           => $savingsProductId,
                        'accountIdFk'           => $SavingsAccId,
                        'amount'                => $data['finalInterest'],
                        'depositDate'           => $effectiveDate,
                        'paymentType'           => 'Interest',
                        'ledgerIdFk'            => $ledgerId,
                        'isAuthorized'          => 1,
                        'entryByEmployeeIdFk'   => Auth::user()->emp_id_fk,
                    );

                    DB::table('mfn_savings_interest')->insert($tableArray);
                    DB::table('mfn_savings_deposit')->insert($depositTableArray);
                }
                // DB::commit();
                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  "Interest Saved Succesfully."
                );

                return response::json($data);
            // }
            //
            // catch(\Exception $e){
            //     DB::rollback();
            //     $data = array(
            //         'responseTitle'  =>  'Warning!',
            //         'responseText'   =>  'Something went wrong. Please try again.'
            //     );
            //     return response::json($data);
            // }
            }

            public function saveManualInterest(Request $request){
                DB::beginTransaction();
                try{
                    $samityId = Input::get('samityId');
                    $savingsProductId = Input::get('savingsProductId');
                    $inputValuesArr = explode('&', $request['requestData']);

            foreach($inputValuesArr as $key => $value) {  // collecting all input arrays
                $inputs = explode('=', $value);
                $inputInterestArr[] = array(
                    // 'field' => $inputs[0],  // name field
                    'inputInterest' => $inputs[1],  // value field
                );
            }

            unset($inputInterestArr[0], $inputInterestArr[1]);  // removing token indexes
            $inputInterestArr = array_values($inputInterestArr); // resetting indexes
            // dd($inputInterestArr);

            $calculatedData = $this->interestCalculation($samityId, $savingsProductId);
            $members = $calculatedData['members'];
            $monthsArr = $calculatedData['monthsArr'];
            $effectiveDate = collect($monthsArr)->max();
            $totalInterest = 0;

            foreach ($members as $key => $member) {  // member loop
                foreach ($monthsArr as $key => $date) {  // months loop

                    $monthOpeningDate = Carbon::parse($date)->startOfMonth()->format('Y-m-d');  // month opening

                    // savings account info
                    $savingsAccountInfo = DB::table('mfn_savings_account')
                    ->where('memberIdFk', $member->id)
                    ->where('savingsProductIdFk', $savingsProductId)
                    ->select('id', 'savingsInterestRate')
                    ->first();
                                        // dd($savingsAccountInfo);
                    // opening balance
                    if ($savingsAccountInfo) {
                        $accountOpeningBalance = DB::table('mfn_opening_savings_account_info')
                        ->where('savingsAccIdFk', $savingsAccountInfo->id)
                        ->sum('openingBalance');
                    }
                    else {
                        $accountOpeningBalance = 0;
                    }

                    // month opening deposits
                    $openingDeposits = DB::table('mfn_savings_deposit')
                    ->where('amount', '>', 0)
                    ->where('softDel', 0)
                    ->where('depositDate', '<', $monthOpeningDate)
                    ->where('memberIdFk', $member->id)
                    ->where('productIdFk', $savingsProductId)
                    ->sum('amount');
                                      // dd($deposits);

                    // month opening withdraws
                    $openingWithdraws = DB::table('mfn_savings_withdraw')
                    ->where('amount', '>', 0)
                    ->where('softDel', 0)
                    ->where('withdrawDate', '<', $monthOpeningDate)
                    ->where('memberIdFk', $member->id)
                    ->where('productIdFk', $savingsProductId)
                    ->sum('amount');
                                        // dd($withdraws);

                    // month closing deposits
                    $closingDeposits = DB::table('mfn_savings_deposit')
                    ->where('amount', '>', 0)
                    ->where('softDel', 0)
                    ->where('depositDate', '<=', $date)
                    ->where('memberIdFk', $member->id)
                    ->where('productIdFk', $savingsProductId)
                    ->sum('amount');
                                      // dd($deposits);

                    // month closing withdraws
                    $closingWithdraws = DB::table('mfn_savings_withdraw')
                    ->where('amount', '>', 0)
                    ->where('softDel', 0)
                    ->where('withdrawDate', '<=', $date)
                    ->where('memberIdFk', $member->id)
                    ->where('productIdFk', $savingsProductId)
                    ->sum('amount');
                                        // dd($withdraws);

                    // check selected months withdraw
                    $withdraw = DB::table('mfn_savings_withdraw')
                    ->where('amount', '>', 0)
                    ->where('softDel', 0)
                    ->where('withdrawDate', '<=', $date)
                    ->where('withdrawDate', '>=', $monthOpeningDate)
                    ->where('memberIdFk', $member->id)
                    ->where('productIdFk', $savingsProductId)
                    ->select('id')
                    ->get();

                    $monthsOpeningBalance = $accountOpeningBalance + $openingDeposits - $openingWithdraws;
                    $monthsClosingBalance = $accountOpeningBalance + $closingDeposits - $closingWithdraws;
                    $averageBalance = ($monthsOpeningBalance + $monthsClosingBalance)/2;

                    $closingBalanceData[] = array(
                        'memberId'           => $member->id,
                        'savingsAccId'       => $savingsAccountInfo->id,
                        'averageBalance'     => $averageBalance,
                        'month'              => $date,
                        'withdraw'           => $withdraw->count('id'),
                    );

                }  // month loop
            }  // member loop

            $closingBalance = $closingBalanceData;
            // dd($closingBalance);

            for($i=0; $i<count($closingBalance); $i++){

                $inputInterest = $inputInterestArr[$i]['inputInterest'];
                $closingBalanceArr = $closingBalance[$i];

                $newClosingBalance[] = array(
                    'memberId'          => $closingBalanceArr['memberId'],
                    'savingsAccId'      => $closingBalanceArr['savingsAccId'],
                    'averageBalance'    => $closingBalanceArr['averageBalance'],
                    'month'             => $closingBalanceArr['month'],
                    'withdraw'          => $closingBalanceArr['withdraw'],
                    'monthsInterest'    => (float)$inputInterest,
                );
            }

            foreach ($members as $key => $member) { // member loop

                $sumMemberInterest = 0;
                foreach (collect($newClosingBalance)->where('memberId', $member->id) as $key => $value) { // balance info loop

                    // apply withdraw condition
                    if ($value['withdraw'] == 0) {
                        $monthsInterest = $value['monthsInterest'];
                        $averageBalance = $value['averageBalance'];
                    } else {
                        $monthsInterest = 0;
                        $averageBalance = 0;
                    }

                    // members total interest
                    $sumMemberInterest += $monthsInterest;

                    // memberInfo
                    $memberInfo = array(
                        'memberId' 			=> $member->id,
                        'savingsAccId' 		=> $value['savingsAccId'],
                    );

                    // monthlyInterest
                    $monthWiseInterest[] = array(
                        'month' 	=> $value['month'],
                        'balance' 	=> $value['averageBalance'],
                        'interest'  => $value['monthsInterest']
                    );

                }  // balance info loop close

                // all members total interest
                $totalInterest += $sumMemberInterest;

                // collect data for submit
                $dataForTable[] = array(
                    'memberInfo' 		=> $memberInfo,
                    'memberInterest' 	=> $monthWiseInterest,
                    'averageBalance'    => $averageBalance,
                    'finalInterest' 	=> $sumMemberInterest,
                );
                $monthWiseInterest = array();

            }  // member loop close
            // dd($dataForTable);

            foreach ($dataForTable as $key => $data) {

                $branchId = DB::table('mfn_samity')->where('id', $samityId)->value('branchId');
                $memberId = $data['memberInfo']['memberId'];
                $primaryProductId = DB::table('mfn_member_information')->where('id', $memberId)->value('primaryProductId');
                $SavingsAccId = $data['memberInfo']['savingsAccId'];
                $monthWiseInterest = json_encode($data['memberInterest']);
                $ledgerId = DB::table('acc_mfn_av_config_saving')
                ->where('loanProductId', $primaryProductId)
                ->where('savingProductId', $savingsProductId)
                ->value('ledgerIdForInterestProvision');

                $tableArray = array(
                    'branchIdFk'            => $branchId,
                    'samityIdFk'            => $samityId,
                    'memberIdFk'            => $memberId,
                    'primaryProductIdFk'    => $primaryProductId,
                    'productIdFk'           => $savingsProductId,
                    'accIdFk'               => $SavingsAccId,
                    'date'                  => Carbon::now()->format('Y-m-d'),
                    'monthWiseInterest'     => $monthWiseInterest,
                    'balanceBefore'         => $data['averageBalance'],
                    'interestAmount'        => $data['finalInterest'],
                    'effectiveDate'         => $effectiveDate,
                    'interestSaveType'      => 0,
                );
                // dd($tableArray);


                $depositTableArray = array(
                    'branchIdFk'            => $branchId,
                    'samityIdFk'            => $samityId,
                    'memberIdFk'            => $memberId,
                    'primaryProductIdFk'    => $primaryProductId,
                    'productIdFk'           => $savingsProductId,
                    'accountIdFk'           => $SavingsAccId,
                    'amount'                => $data['finalInterest'],
                    'depositDate'           => $effectiveDate,
                    'paymentType'           => 'Interest',
                    'ledgerIdFk'            => $ledgerId,
                    'isAuthorized'          => 1,
                    'entryByEmployeeIdFk'   => Auth::user()->emp_id_fk,
                );

                DB::table('mfn_savings_interest')->insert($tableArray);
                DB::table('mfn_savings_deposit')->insert($depositTableArray);
            }

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  "Interest Saved Succesfully."
            );
            DB::commit();
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

       public function collectAllMonthsOfFiYr($branchId){

            // collect months
            // $today = Carbon::now()->format('Y-m-d');  // calculation for recent year
            // $today = '2019-06-30';  // calculation for recent year
        $branchDate = DB::table('mfn_day_end')->where('branchIdFk', $branchId)->where('isLocked', 0)->value('date');
        $branchOpeningDate = DB::table('gnr_branch')->where('id', $branchId)->value('softwareStartDate');
        $today = $branchDate == null ? $branchOpeningDate : $branchDate;

            // this year info
        $thisFiYrInfo = DB::table('gnr_fiscal_year')
        ->where('fyStartDate','<=', $today)
        ->where('fyEndDate','>=', $today)
        ->select('id', 'fyStartDate', 'fyEndDate')
        ->first();

            if($today != $thisFiYrInfo->fyEndDate) {  // check if today is the last day of fiscal year

                $today = Carbon::now()->subYears(1)->format('Y-m-d');  // calculation for previous year

                // previous year info
                $thisFiYrInfo = DB::table('gnr_fiscal_year')
                ->where('fyStartDate','<=', $today)
                ->where('fyEndDate','>=', $today)
                ->select('id', 'fyStartDate', 'fyEndDate')
                ->first();
            }

            $firstMonthOfThisFiYr = Carbon::parse($thisFiYrInfo->fyStartDate)->format('Y-m-d');
            $previousMonth = Carbon::parse($firstMonthOfThisFiYr)->subMonths(1)->format('Y-m-d');

            for ($i=1; $i<=12; $i++) {
                $nextMonth = Carbon::parse($previousMonth)->addMonths(1)->format('Y-m-d');
                $monthsArr[] = Carbon::parse($nextMonth)->endOfMonth()->format('Y-m-d'); // collect all months ends
                $previousMonth = $nextMonth;
            }

            return $monthsArr;
        }

        public function interestCalculation($samityId, $savingsProductId) {

            $requestedBranchId = DB::table('mfn_samity')->where('id', $samityId)->value('branchId');
            $softwareStartDate = DB::table('gnr_branch')->where('id', $requestedBranchId)->value('softwareStartDate');
            // collect months-
            $monthsArr = $this->collectAllMonthsOfFiYr($requestedBranchId);
            $lastMonthDate = collect($monthsArr)->max();
            // $today = CarBon::now()->format('Y-m-d');

            $samityName = DB::table('mfn_samity')
            ->where('id', (int)$samityId)
            ->select(DB::raw("CONCAT(code, ' - ', name) AS samityName"))
            ->first()->samityName;

            $savingsAccountsMember = DB::table('mfn_savings_account')
            ->where('softDel', 0)
            ->where(function($query) use ($lastMonthDate){
                $query->where('closingDate','0000-00-00')
                ->orWhere('closingDate','>', $lastMonthDate);
            })
            ->where('samityIdFk', (int)$samityId)
            ->where('savingsProductIdFk', (int)$savingsProductId)
            ->pluck('memberIdFk')
            ->toArray();
                                    // dd($savingsAccountsMember);

            $savingsAccountsIdArr = DB::table('mfn_savings_account')
            ->where('softDel', 0)
            ->where(function($query) use ($lastMonthDate){
                $query->where('closingDate','0000-00-00')
                ->orWhere('closingDate','>', $lastMonthDate);
            })
            ->where('samityIdFk', (int)$samityId)
            ->where('savingsProductIdFk', (int)$savingsProductId)
            ->pluck('id')
            ->toArray();
                                    // dd($savingsAccountsIdArr);

            $openingBalances = DB::table('mfn_opening_savings_account_info')
            ->whereIn('savingsAccIdFk', $savingsAccountsIdArr)
            ->pluck('openingBalance', 'savingsAccIdFk')->toArray();
                                // dd($openingBalances);

            // deposits
            $deposits = DB::table('mfn_savings_deposit')->whereIn('accountIdFk', $savingsAccountsIdArr)
            ->where('amount', '>', 0)
            ->where('softDel', 0)
            ->where('depositDate', '<=', $lastMonthDate)
            ->where('paymentType', '!=', 'Interest')
            ->groupby('accountIdFk')
            ->select('accountIdFk', DB::raw('sum(amount) as amount'))
            ->get()->keyBy('accountIdFk')->toArray();
                              // dd($deposits);

                              // withdraws
            $withdraws = DB::table('mfn_savings_withdraw')->whereIn('accountIdFk', $savingsAccountsIdArr)
            ->where('amount', '>', 0)
            ->where('softDel', 0)
            ->where('withdrawDate', '<=', $lastMonthDate)
            ->groupby('accountIdFk')
            ->select('accountIdFk', DB::raw('sum(amount) as amount'))
            ->get()->keyBy('accountIdFk')->toArray();
                          // dd($withdraws);

            // find zero balance ids
            $zeroBalanceAccounts = [];
            foreach ($savingsAccountsIdArr as $key => $id) {

                $openingBalance = isset($openingBalances[$id]) ? $openingBalances[$id] : 0;
                $deposit = isset($deposits[$id]) ? $deposits[$id]->amount : 0;
                $withdraw = isset($withdraws[$id]) ? $withdraws[$id]->amount : 0;
                $balance = $openingBalance + $deposit - $withdraw;
                if ($balance == 0) {
                    array_push($zeroBalanceAccounts, $id);
                }
            }
            // dd($zeroBalanceAccounts);

            $members = DB::table('mfn_member_information')
            ->where('softDel', 0)
            ->where(function($query) use ($lastMonthDate){
                $query->where('closingDate','0000-00-00')
                ->orWhere('closingDate','>', $lastMonthDate);
            })
            ->whereIn('id', $savingsAccountsMember)
            ->where('samityId', (int)$samityId)
                        // ->where('samityId', 9)
            ->select('id', 'name', 'code', 'spouseFatherSonName')
            ->get();

            $savingsProduct = DB::table('mfn_saving_product')->where('id', $savingsProductId)->where('interestCalFrequencyIdFk', 2)->select('id', 'shortName')->first();
            // dd($zeroBalanceAccounts);

            if($members->count() > 0){  // check if member exists
                foreach ($members as $key => $member) {  // member loop

                    // savings account info
                    $savingsAccountInfos = DB::table('mfn_savings_account')
                    ->whereNotIn('id', $zeroBalanceAccounts)
                    ->where('memberIdFk', $member->id)
                    ->where('softDel', 0)
                    ->where(function($query) use ($lastMonthDate){
                        $query->where('closingDate','0000-00-00')
                        ->orWhere('closingDate','>', $lastMonthDate);
                    })
                    ->where('savingsProductIdFk', $savingsProductId)
                    ->select('id', 'savingsInterestRate', 'accountOpeningDate')
                    ->get();
                                        // dd($savingsAccountInfo);

                    foreach ($savingsAccountInfos as $key => $savingsAccountInfo) {

                        foreach ($monthsArr as $key => $date) {  // months loop

                            $monthOpeningDate = Carbon::parse($date)->startOfMonth()->format('Y-m-d');  // month opening
                            $monthClosingDate = Carbon::parse($date)->endOfMonth()->format('Y-m-d');  // month opening
                            $monthDays = Carbon::parse($monthClosingDate)->diffInDays(Carbon::parse($monthOpeningDate)) + 1;

                            if ($savingsAccountInfo) {
                                $accountOpeningBalance = DB::table('mfn_opening_savings_account_info')
                                ->where('savingsAccIdFk', $savingsAccountInfo->id)
                                ->sum('openingBalance');
                            }
                            else {
                                $accountOpeningBalance = 0;
                            }

                            // months balance
                            if ($softwareStartDate >= $monthClosingDate) {
                                $monthsOpeningBalance = $accountOpeningBalance;
                                $monthsClosingBalance = $accountOpeningBalance;

                                if($savingsAccountInfo->accountOpeningDate > $monthClosingDate) {
                                    $monthsOpeningBalance = 0;
                                    $monthsClosingBalance = 0;
                                }
                                elseif ($monthClosingDate >= $savingsAccountInfo->accountOpeningDate && $monthOpeningDate <= $savingsAccountInfo->accountOpeningDate) {
                                    $monthsOpeningBalance = 0;
                                }

                            }
                            else {
                                // month opening deposits
                                $openingDeposits = DB::table('mfn_savings_deposit')
                                ->where('amount', '>', 0)
                                ->where('softDel', 0)
                                ->where('depositDate', '<', $monthOpeningDate)
                                ->where('paymentType', '!=', 'Interest')
                                ->where('accountIdFk', $savingsAccountInfo->id)
                                ->where('productIdFk', $savingsProductId)
                                ->sum('amount');
                                                  // dd($deposits);

                                // month opening withdraws
                                $openingWithdraws = DB::table('mfn_savings_withdraw')
                                ->where('amount', '>', 0)
                                ->where('softDel', 0)
                                ->where('withdrawDate', '<', $monthOpeningDate)
                                ->where('accountIdFk', $savingsAccountInfo->id)
                                ->where('productIdFk', $savingsProductId)
                                ->sum('amount');
                                                    // dd($withdraws);

                                // month closing deposits
                                $closingDeposits = DB::table('mfn_savings_deposit')
                                ->where('amount', '>', 0)
                                ->where('softDel', 0)
                                ->where('depositDate', '<=', $date)
                                ->where('paymentType', '!=', 'Interest')
                                ->where('accountIdFk', $savingsAccountInfo->id)
                                ->where('productIdFk', $savingsProductId)
                                ->sum('amount');
                                                  // dd($deposits);

                                // month closing withdraws
                                $closingWithdraws = DB::table('mfn_savings_withdraw')
                                ->where('amount', '>', 0)
                                ->where('softDel', 0)
                                ->where('withdrawDate', '<=', $date)
                                ->where('accountIdFk', $savingsAccountInfo->id)
                                ->where('productIdFk', $savingsProductId)
                                ->sum('amount');
                                                    // dd($withdraws);

                                $monthsOpeningBalance = $accountOpeningBalance + $openingDeposits - $openingWithdraws;
                                $monthsClosingBalance = $accountOpeningBalance + $closingDeposits - $closingWithdraws;
                            }

                            // check selected months withdraw
                            $withdraw = DB::table('mfn_savings_withdraw')
                            ->where('amount', '>', 0)
                            ->where('softDel', 0)
                            ->where('withdrawDate', '<=', $date)
                            ->where('withdrawDate', '>=', $monthOpeningDate)
                            ->where('accountIdFk', $savingsAccountInfo->id)
                            ->where('productIdFk', $savingsProductId)
                            ->select('id')
                            ->get();

                            $averageBalance = ($monthsOpeningBalance + $monthsClosingBalance)/2;
                            $accountMaturity = Carbon::parse($lastMonthDate)->diffInDays(Carbon::parse($savingsAccountInfo->accountOpeningDate)) + 1;
                            // dd($savingsAccountInfo->accountOpeningDate, $lastMonthDate, $accountMaturity);

                            // interest calculation
                            if ($savingsAccountInfo) {
                                if ($accountMaturity >= 181) {
                                    $monthsInterest = ($averageBalance * $savingsAccountInfo->savingsInterestRate)/(100 * 12);

                                    if ($savingsAccountInfo->accountOpeningDate >= $monthOpeningDate && $savingsAccountInfo->accountOpeningDate <= $monthClosingDate) {

                                        $openingDaysTillMonthEnd = Carbon::parse($monthClosingDate)->diffInDays(Carbon::parse($savingsAccountInfo->accountOpeningDate)) + 1;
                                        $monthsInterest = $monthsInterest * ($openingDaysTillMonthEnd / $monthDays);
                                        // dd($monthsInterest);
                                    }
                                }
                                else {
                                    $monthsInterest = 0;
                                }

                            }
                            else {
                                $monthsInterest = 0;
                            }


                            $closingBalanceData[] = array(
                                'memberId'           => $member->id,
                                'savingsAccId'       => $savingsAccountInfo->id,
                                'averageBalance'     => $averageBalance,
                                'month'              => $date,
                                'withdraw'           => $withdraw->count('id'),
                                'monthsInterest'     => round($monthsInterest)
                            );

                        }  // month loop
                    } // savings account loop
                }  // member loop

                $closingDataCollection = collect($closingBalanceData);
                // dd($closingDataCollection->unique('savingsAccId'));

                // // filtering active members
                // $activeMembersArr = array();
                // foreach ($members as $key => $member) {
                //
                //     $sumAvgBalance = $closingDataCollection->where('memberId', $member->id)->sum('averageBalance');
                //     $lastMonthBalance = $closingDataCollection->where('memberId', $member->id)->where('month', $lastMonthDate)->sum('averageBalance');
                //
                //     if($sumAvgBalance != 0 && $lastMonthBalance != 0){
                //         $activeMembersArr[] = $member->id;
                //     }
                // }  // active member loop
                //
                // if(count($activeMembersArr) > 0) {
                //     // $members = $members;
                //     $members = $members->whereIn('id', $activeMembersArr);
                //     // dd($members);
                // } else {
                //     $members = collect();
                // }
            }
            else {  // no member in samity so blank collection
                $closingDataCollection = collect();
            }

            $path = Route::current()->uri();
            $route = explode('/', $path)[0];

            $data = array(
                'samityId'                => $samityId,
                'savingsProductId'        => $savingsProductId,
                'closingBalance'          => $closingDataCollection->whereIn('memberId', $members->pluck('id')->toArray()),
                'monthsArr'               => $monthsArr,
                'samityName'              => $samityName,
                'members'                 => $members,
                'savingsProduct'          => $savingsProduct,
                'route'                   => $route,
            );
            // dd($data);

            return $data;
        }

        public function massInterestGenerate() {

            // $this->saveMissingInterest();

            $branchList = DB::table('gnr_branch')
            ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS branchName"), 'id')
            ->pluck('branchName','id')
            ->toArray();

            $productList = DB::table('mfn_saving_product')->where('status', 1)->whereNotIn('id', [3, 4])
            ->select(DB::raw("CONCAT(LPAD(code, 3, 0), ' - ', shortname) AS name"), 'id')
            ->pluck('name','id')
            ->toArray();

            $fiscalYears = DB::table('gnr_fiscal_year')
            ->orderBy('name', 'desc')
            ->pluck('name', 'id')
            ->toArray();

            $data = array(
                'branchList'    =>  $branchList,
                'productList'   =>  $productList,
                'fiscalYears'   =>  $fiscalYears
            );
            // dd($data);

            return view('microfin.savings.savingsInterest.massGenerate',$data);
        }

        public function getBranchWiseSamity(Request $req) {

            $samityList = DB::table('mfn_samity')
            ->where('branchId', $req->branchId)
            ->select(DB::raw("CONCAT(LPAD(code, 8, 0), ' - ', name) AS name"), 'id')
            ->pluck('name','id')
            ->toArray();

            return response()->json($samityList);
        }

        public function saveInterest(Request $req){
            // dd($req->all());
            $branchId = (int) $req->filBranch;
            $samityIdArr = $req->filSamity == null
            ? DB::table('mfn_samity')->where('branchId', $branchId)->pluck('id')->toArray()
            : [(int) $req->filSamity];

            $savingsProductIdArr = $req->filProduct == null
            ? DB::table('mfn_saving_product')->where('status', 1)->whereNotIn('id', [3, 4])->pluck('id')->toArray()
            : [(int) $req->filProduct];
                                // dd($savingsProductIdArr);

            $fiscalYear = DB::table('gnr_fiscal_year')->where('id', (int) $req->fiscalYear)->first();
            $firstMonthOfThisFiYr = Carbon::parse($fiscalYear->fyStartDate)->format('Y-m-d');
            $previousMonth = Carbon::parse($firstMonthOfThisFiYr)->subMonths(1)->format('Y-m-d');

            for ($i=1; $i<=12; $i++) {
                $nextMonth = Carbon::parse($previousMonth)->addMonths(1)->format('Y-m-d');
                $monthsArr[] = Carbon::parse($nextMonth)->endOfMonth()->format('Y-m-d'); // collect all months ends
                $previousMonth = $nextMonth;
            }

            $lastMonthDate = collect($monthsArr)->max();
            $softwareStartDate = DB::table('gnr_branch')->where('id', $branchId)->value('softwareStartDate');
            // $today = CarBon::now()->format('Y-m-d');

            $savingsAccounts = DB::table('mfn_savings_account')
            ->where('softDel', 0)
            ->where(function($query) use ($lastMonthDate){
                $query->where('closingDate','0000-00-00')
                ->orWhere('closingDate','>', $lastMonthDate);
            })
            ->whereIn('samityIdFk', $samityIdArr)
            ->whereIn('savingsProductIdFk', $savingsProductIdArr)
            ->select('id', 'savingsInterestRate', 'accountOpeningDate', 'memberIdFk', 'samityIdFk', 'savingsProductIdFk')
            ->get();
                                // dd($savingsAccounts);

            $savingsAccountsIdArr = $savingsAccounts->pluck('id')->toArray();

            $openingBalances = DB::table('mfn_opening_savings_account_info')
            ->whereIn('savingsAccIdFk', $savingsAccountsIdArr)
            ->pluck('openingBalance', 'savingsAccIdFk')->toArray();
                                // dd($openingBalances);

            // deposits
            $deposits = DB::table('mfn_savings_deposit')->whereIn('accountIdFk', $savingsAccountsIdArr)
            ->where('amount', '>', 0)
            ->where('softDel', 0)
            ->where('depositDate', '<=', $lastMonthDate)
            ->where('paymentType', '!=', 'Interest')
            ->groupby('accountIdFk')
            ->select('accountIdFk', DB::raw('sum(amount) as amount'))
            ->get()->keyBy('accountIdFk')->toArray();
                              // dd($deposits);

                              // withdraws
            $withdraws = DB::table('mfn_savings_withdraw')->whereIn('accountIdFk', $savingsAccountsIdArr)
            ->where('amount', '>', 0)
            ->where('softDel', 0)
            ->where('withdrawDate', '<=', $lastMonthDate)
            ->groupby('accountIdFk')
            ->select('accountIdFk', DB::raw('sum(amount) as amount'))
            ->get()->keyBy('accountIdFk')->toArray();
                          // dd($withdraws);

            // find zero balance ids
            $zeroBalanceAccounts = [];
            foreach ($savingsAccountsIdArr as $key => $id) {

                $openingBalance = isset($openingBalances[$id]) ? $openingBalances[$id] : 0;
                $deposit = isset($deposits[$id]) ? $deposits[$id]->amount : 0;
                $withdraw = isset($withdraws[$id]) ? $withdraws[$id]->amount : 0;
                $balance = $openingBalance + $deposit - $withdraw;
                if ($balance == 0) {
                    array_push($zeroBalanceAccounts, $id);
                }
            }
            // dd($zeroBalanceAccounts);
            $savingsAccounts = $savingsAccounts->whereNotIn('id', $zeroBalanceAccounts);
            // dd($savingsAccounts);

            if($savingsAccounts->count() > 0){  // check if account exists
                foreach ($savingsAccounts as $key => $savingsAccount) {  // account loop

                    $totalInterest = 0;
                    $monthWiseInterest = [];
                    $averageBalance = 0;
                    foreach ($monthsArr as $key => $date) {  // months loop

                        $monthOpeningDate = Carbon::parse($date)->startOfMonth()->format('Y-m-d');  // month opening
                        $monthClosingDate = Carbon::parse($date)->endOfMonth()->format('Y-m-d');  // month opening
                        $monthDays = Carbon::parse($monthClosingDate)->diffInDays(Carbon::parse($monthOpeningDate)) + 1;

                        // opening balance
                        $accountOpeningBalance = isset($openingBalances[$savingsAccount->id]) ? $openingBalances[$savingsAccount->id] : 0;

                        // months balance
                        if ($softwareStartDate >= $monthClosingDate) {
                            $monthsOpeningBalance = $accountOpeningBalance;
                            $monthsClosingBalance = $accountOpeningBalance;

                            if($savingsAccount->accountOpeningDate > $monthClosingDate) {
                                $monthsOpeningBalance = 0;
                                $monthsClosingBalance = 0;
                            }
                            elseif ($monthClosingDate >= $savingsAccount->accountOpeningDate && $monthOpeningDate <= $savingsAccount->accountOpeningDate) {
                                $monthsOpeningBalance = 0;
                            }

                        }
                        else {
                            // month opening deposits
                            $openingDeposits = DB::table('mfn_savings_deposit')
                            ->where('amount', '>', 0)
                            ->where('softDel', 0)
                            ->where('depositDate', '<', $monthOpeningDate)
                            ->where('paymentType', '!=', 'Interest')
                            ->where('accountIdFk', $savingsAccount->id)
                            ->sum('amount');
                                              // dd($deposits);

                            // month opening withdraws
                            $openingWithdraws = DB::table('mfn_savings_withdraw')
                            ->where('amount', '>', 0)
                            ->where('softDel', 0)
                            ->where('withdrawDate', '<', $monthOpeningDate)
                            ->where('accountIdFk', $savingsAccount->id)
                            ->sum('amount');
                                                // dd($withdraws);

                            // month closing deposits
                            $closingDeposits = DB::table('mfn_savings_deposit')
                            ->where('amount', '>', 0)
                            ->where('softDel', 0)
                            ->where('depositDate', '<=', $date)
                            ->where('paymentType', '!=', 'Interest')
                            ->where('accountIdFk', $savingsAccount->id)
                            ->sum('amount');
                                              // dd($deposits);

                            // month closing withdraws
                            $closingWithdraws = DB::table('mfn_savings_withdraw')
                            ->where('amount', '>', 0)
                            ->where('softDel', 0)
                            ->where('withdrawDate', '<=', $date)
                            ->where('accountIdFk', $savingsAccount->id)
                            ->sum('amount');
                                                // dd($withdraws);

                            $monthsOpeningBalance = $accountOpeningBalance + $openingDeposits - $openingWithdraws;
                            $monthsClosingBalance = $accountOpeningBalance + $closingDeposits - $closingWithdraws;
                        }

                        // check selected months withdraw
                        $withdraw = DB::table('mfn_savings_withdraw')
                        ->where('amount', '>', 0)
                        ->where('softDel', 0)
                        ->where('withdrawDate', '<=', $date)
                        ->where('withdrawDate', '>=', $monthOpeningDate)
                        ->where('accountIdFk', $savingsAccount->id)
                        ->count('id');

                        $averageBalance = ($monthsOpeningBalance + $monthsClosingBalance)/2;
                        $accountMaturity = Carbon::parse($lastMonthDate)->diffInDays(Carbon::parse($savingsAccount->accountOpeningDate)) + 1;
                        // dd($savingsAccountInfo->accountOpeningDate, $lastMonthDate, $accountMaturity);

                        // interest calculation
                        if ($accountMaturity >= 181) {
                            $monthsInterest = ($averageBalance * $savingsAccount->savingsInterestRate)/(100 * 12);

                            if ($savingsAccount->accountOpeningDate >= $monthOpeningDate && $savingsAccount->accountOpeningDate <= $monthClosingDate) {

                                $openingDaysTillMonthEnd = Carbon::parse($monthClosingDate)->diffInDays(Carbon::parse($savingsAccount->accountOpeningDate)) + 1;
                                $monthsInterest = $monthsInterest * ($openingDaysTillMonthEnd / $monthDays);
                                // dd($monthsInterest);
                            }
                        }
                        else {
                            $monthsInterest = 0;
                        }

                        // apply withdraw condition
                        if ($withdraw > 0) {
                            $monthsInterest = 0;
                            $averageBalance = 0;
                        }

                        // monthwise Interest
                        $monthWiseInterest[] = array(
                            'month' 	=> $date,
                            'balance' 	=> $averageBalance,
                            'interest'  => round($monthsInterest)
                        );
                        // accounts total interest
                        $totalInterest += round($monthsInterest);

                    }  // month loop closed

                    $closingBalanceData[] = array(
                        'branchIdFk'            => $branchId,
                        'samityIdFk'            => $savingsAccount->samityIdFk,
                        'memberIdFk'            => $savingsAccount->memberIdFk,
                        'primaryProductIdFk'    => DB::table('mfn_member_information')->where('id', $savingsAccount->memberIdFk)->value('primaryProductId'),
                        'productIdFk'           => $savingsAccount->savingsProductIdFk,
                        'accIdFk'               => $savingsAccount->id,
                        'date'                  => Carbon::now()->format('Y-m-d'),
                        'monthWiseInterest'     => json_encode($monthWiseInterest),
                        'balanceBefore'         => $averageBalance,
                        'interestAmount'        => $totalInterest,
                        'effectiveDate'         => $lastMonthDate,
                        'interestSaveType'      => 0,
                    );
                    // dd($closingBalanceData);

                }  // accounts loop closed

            }
            else {  // no accounts in samity so blank collection
                $closingBalanceData = [];
            }
            // dd($closingBalanceData);

            DB::beginTransaction();
            try {

                DB::table('mfn_savings_interest')->whereIn('samityIdFk', $samityIdArr)->whereIn('productIdFk', $savingsProductIdArr)->where('effectiveDate', $lastMonthDate)->delete();
                DB::table('mfn_savings_deposit')->whereIn('samityIdFk', $samityIdArr)->whereIn('productIdFk', $savingsProductIdArr)->where('depositDate', $lastMonthDate)->where('paymentType', 'Interest')->delete();

                foreach ($closingBalanceData as $key => $data) {

                    $ledgerId = DB::table('acc_mfn_av_config_saving')
                    ->where('loanProductId', $data['primaryProductIdFk'])
                    ->where('savingProductId', $data['productIdFk'])
                    ->value('ledgerIdForInterestProvision');

                    $ledgerId = $ledgerId == null ? 0 : $ledgerId;

                    $depositTableArray = array(
                        'branchIdFk'            => $data['branchIdFk'],
                        'samityIdFk'            => $data['samityIdFk'],
                        'memberIdFk'            => $data['memberIdFk'],
                        'primaryProductIdFk'    => $data['primaryProductIdFk'],
                        'productIdFk'           => $data['productIdFk'],
                        'accountIdFk'           => $data['accIdFk'],
                        'amount'                => $data['interestAmount'],
                        'depositDate'           => $data['effectiveDate'],
                        'paymentType'           => 'Interest',
                        'ledgerIdFk'            => $ledgerId,
                        'isAuthorized'          => 1,
                        'entryByEmployeeIdFk'   => Auth::user()->emp_id_fk,
                    );
                    // dd($data, $depositTableArray);

                    DB::table('mfn_savings_interest')->insert($data);
                    DB::table('mfn_savings_deposit')->insert($depositTableArray);

                }
                DB::commit();
                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  "Interest Saved Succesfully."
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

        public function saveMissingInterest(){

            $fiscalYear = DB::table('gnr_fiscal_year')->where('id', 4)->first();
            $firstMonthOfThisFiYr = Carbon::parse($fiscalYear->fyStartDate)->format('Y-m-d');
            $previousMonth = Carbon::parse($firstMonthOfThisFiYr)->subMonths(1)->format('Y-m-d');

            for ($i=1; $i<=12; $i++) {
                $nextMonth = Carbon::parse($previousMonth)->addMonths(1)->format('Y-m-d');
                $monthsArr[] = Carbon::parse($nextMonth)->endOfMonth()->format('Y-m-d'); // collect all months ends
                $previousMonth = $nextMonth;
            }

            $lastMonthDate = collect($monthsArr)->max();

            $query = DB::select("SELECT DISTINCT `accountIdFk` FROM `mfn_savings_withdraw` WHERE softDel=0 AND withdrawDate>='2019-07-01' AND withdrawDate<='2019-07-11' AND amount!=0 AND `accountIdFk` NOT IN (SELECT `accIdFk` FROM `mfn_savings_interest`) AND `productIdFk` IN (1,2)");
            $ids = collect($query)->pluck('accountIdFk')->toArray();
            dd($ids);

            $savingsAccounts = DB::table('mfn_savings_account')->whereIn('id', $ids)
            ->select('id', 'savingsInterestRate', 'accountOpeningDate', 'memberIdFk', 'samityIdFk', 'savingsProductIdFk', 'branchIdFk')
            ->get();
                                // dd($savingsAccounts);

            $savingsAccountsIdArr = $savingsAccounts->pluck('id')->toArray();

            $openingBalances = DB::table('mfn_opening_savings_account_info')
            ->whereIn('savingsAccIdFk', $savingsAccountsIdArr)
            ->pluck('openingBalance', 'savingsAccIdFk')->toArray();
                                // dd($openingBalances);

            // deposits
            $deposits = DB::table('mfn_savings_deposit')->whereIn('accountIdFk', $savingsAccountsIdArr)
            ->where('amount', '>', 0)
            ->where('softDel', 0)
            ->where('depositDate', '<=', $lastMonthDate)
            ->where('paymentType', '!=', 'Interest')
            ->groupby('accountIdFk')
            ->select('accountIdFk', DB::raw('sum(amount) as amount'))
            ->get()->keyBy('accountIdFk')->toArray();
                              // dd($deposits);

                              // withdraws
            $withdraws = DB::table('mfn_savings_withdraw')->whereIn('accountIdFk', $savingsAccountsIdArr)
            ->where('amount', '>', 0)
            ->where('softDel', 0)
            ->where('withdrawDate', '<=', $lastMonthDate)
            ->groupby('accountIdFk')
            ->select('accountIdFk', DB::raw('sum(amount) as amount'))
            ->get()->keyBy('accountIdFk')->toArray();
                          // dd($withdraws);

            // find zero balance ids
            $zeroBalanceAccounts = [];
            foreach ($savingsAccountsIdArr as $key => $id) {

                $openingBalance = isset($openingBalances[$id]) ? $openingBalances[$id] : 0;
                $deposit = isset($deposits[$id]) ? $deposits[$id]->amount : 0;
                $withdraw = isset($withdraws[$id]) ? $withdraws[$id]->amount : 0;
                $balance = $openingBalance + $deposit - $withdraw;
                if ($balance == 0) {
                    array_push($zeroBalanceAccounts, $id);
                }
            }
            // dd($zeroBalanceAccounts);
            $savingsAccounts = $savingsAccounts->whereNotIn('id', $zeroBalanceAccounts);
            // dd($savingsAccounts);

            if($savingsAccounts->count() > 0){  // check if account exists
                foreach ($savingsAccounts as $key => $savingsAccount) {  // account loop

                    $totalInterest = 0;
                    $monthWiseInterest = [];
                    $averageBalance = 0;
                    foreach ($monthsArr as $key => $date) {  // months loop

                        $monthOpeningDate = Carbon::parse($date)->startOfMonth()->format('Y-m-d');  // month opening
                        $monthClosingDate = Carbon::parse($date)->endOfMonth()->format('Y-m-d');  // month opening
                        $monthDays = Carbon::parse($monthClosingDate)->diffInDays(Carbon::parse($monthOpeningDate)) + 1;
                        $softwareStartDate = DB::table('gnr_branch')->where('id', $savingsAccount->branchIdFk)->value('softwareStartDate');

                        // opening balance
                        $accountOpeningBalance = isset($openingBalances[$savingsAccount->id]) ? $openingBalances[$savingsAccount->id] : 0;

                        // months balance
                        if ($softwareStartDate >= $monthClosingDate) {
                            $monthsOpeningBalance = $accountOpeningBalance;
                            $monthsClosingBalance = $accountOpeningBalance;

                            if($savingsAccount->accountOpeningDate > $monthClosingDate) {
                                $monthsOpeningBalance = 0;
                                $monthsClosingBalance = 0;
                            }
                            elseif ($monthClosingDate >= $savingsAccount->accountOpeningDate && $monthOpeningDate <= $savingsAccount->accountOpeningDate) {
                                $monthsOpeningBalance = 0;
                            }

                        }
                        else {
                            // month opening deposits
                            $openingDeposits = DB::table('mfn_savings_deposit')
                            ->where('amount', '>', 0)
                            ->where('softDel', 0)
                            ->where('depositDate', '<', $monthOpeningDate)
                            ->where('paymentType', '!=', 'Interest')
                            ->where('accountIdFk', $savingsAccount->id)
                            ->sum('amount');
                                              // dd($deposits);

                            // month opening withdraws
                            $openingWithdraws = DB::table('mfn_savings_withdraw')
                            ->where('amount', '>', 0)
                            ->where('softDel', 0)
                            ->where('withdrawDate', '<', $monthOpeningDate)
                            ->where('accountIdFk', $savingsAccount->id)
                            ->sum('amount');
                                                // dd($withdraws);

                            // month closing deposits
                            $closingDeposits = DB::table('mfn_savings_deposit')
                            ->where('amount', '>', 0)
                            ->where('softDel', 0)
                            ->where('depositDate', '<=', $date)
                            ->where('paymentType', '!=', 'Interest')
                            ->where('accountIdFk', $savingsAccount->id)
                            ->sum('amount');
                                              // dd($deposits);

                            // month closing withdraws
                            $closingWithdraws = DB::table('mfn_savings_withdraw')
                            ->where('amount', '>', 0)
                            ->where('softDel', 0)
                            ->where('withdrawDate', '<=', $date)
                            ->where('accountIdFk', $savingsAccount->id)
                            ->sum('amount');
                                                // dd($withdraws);

                            $monthsOpeningBalance = $accountOpeningBalance + $openingDeposits - $openingWithdraws;
                            $monthsClosingBalance = $accountOpeningBalance + $closingDeposits - $closingWithdraws;
                        }

                        // check selected months withdraw
                        $withdraw = DB::table('mfn_savings_withdraw')
                        ->where('amount', '>', 0)
                        ->where('softDel', 0)
                        ->where('withdrawDate', '<=', $date)
                        ->where('withdrawDate', '>=', $monthOpeningDate)
                        ->where('accountIdFk', $savingsAccount->id)
                        ->count('id');

                        $averageBalance = ($monthsOpeningBalance + $monthsClosingBalance)/2;
                        $accountMaturity = Carbon::parse($lastMonthDate)->diffInDays(Carbon::parse($savingsAccount->accountOpeningDate)) + 1;
                        // dd($savingsAccountInfo->accountOpeningDate, $lastMonthDate, $accountMaturity);

                        // interest calculation
                        if ($accountMaturity >= 181) {
                            $monthsInterest = ($averageBalance * $savingsAccount->savingsInterestRate)/(100 * 12);

                            if ($savingsAccount->accountOpeningDate >= $monthOpeningDate && $savingsAccount->accountOpeningDate <= $monthClosingDate) {

                                $openingDaysTillMonthEnd = Carbon::parse($monthClosingDate)->diffInDays(Carbon::parse($savingsAccount->accountOpeningDate)) + 1;
                                $monthsInterest = $monthsInterest * ($openingDaysTillMonthEnd / $monthDays);
                                // dd($monthsInterest);
                            }
                        }
                        else {
                            $monthsInterest = 0;
                        }

                        // apply withdraw condition
                        if ($withdraw > 0) {
                            $monthsInterest = 0;
                            $averageBalance = 0;
                        }

                        // monthwise Interest
                        $monthWiseInterest[] = array(
                            'month' 	=> $date,
                            'balance' 	=> $averageBalance,
                            'interest'  => round($monthsInterest)
                        );
                        // accounts total interest
                        $totalInterest += round($monthsInterest);

                    }  // month loop closed

                    $closingBalanceData[] = array(
                        'branchIdFk'            => $savingsAccount->branchIdFk,
                        'samityIdFk'            => $savingsAccount->samityIdFk,
                        'memberIdFk'            => $savingsAccount->memberIdFk,
                        'primaryProductIdFk'    => DB::table('mfn_member_information')->where('id', $savingsAccount->memberIdFk)->value('primaryProductId'),
                        'productIdFk'           => $savingsAccount->savingsProductIdFk,
                        'accIdFk'               => $savingsAccount->id,
                        'date'                  => Carbon::now()->format('Y-m-d'),
                        'monthWiseInterest'     => json_encode($monthWiseInterest),
                        'balanceBefore'         => $averageBalance,
                        'interestAmount'        => $totalInterest,
                        'effectiveDate'         => $lastMonthDate,
                        'interestSaveType'      => 0,
                    );
                    // dd($closingBalanceData);

                }  // accounts loop closed

            }
            else {  // no accounts in samity so blank collection
                $closingBalanceData = [];
            }
            // dd($closingBalanceData);

            DB::beginTransaction();
            try {

                DB::table('mfn_savings_interest')->whereIn('accIdFk', $savingsAccountsIdArr)->delete();
                DB::table('mfn_savings_deposit')->whereIn('accountIdFk', $savingsAccountsIdArr)->where('paymentType', 'Interest')->delete();

                foreach ($closingBalanceData as $key => $data) {

                    $ledgerId = DB::table('acc_mfn_av_config_saving')
                    ->where('loanProductId', $data['primaryProductIdFk'])
                    ->where('savingProductId', $data['productIdFk'])
                    ->value('ledgerIdForInterestProvision');

                    $ledgerId = $ledgerId == null ? 0 : $ledgerId;

                    $depositTableArray = array(
                        'branchIdFk'            => $data['branchIdFk'],
                        'samityIdFk'            => $data['samityIdFk'],
                        'memberIdFk'            => $data['memberIdFk'],
                        'primaryProductIdFk'    => $data['primaryProductIdFk'],
                        'productIdFk'           => $data['productIdFk'],
                        'accountIdFk'           => $data['accIdFk'],
                        'amount'                => $data['interestAmount'],
                        'depositDate'           => $data['effectiveDate'],
                        'paymentType'           => 'Interest',
                        'ledgerIdFk'            => $ledgerId,
                        'isAuthorized'          => 1,
                        'entryByEmployeeIdFk'   => Auth::user()->emp_id_fk,
                    );
                    // dd($data, $depositTableArray);

                    DB::table('mfn_savings_interest')->insert($data);
                    DB::table('mfn_savings_deposit')->insert($depositTableArray);

                }
                DB::commit();
                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  "Interest Saved Succesfully."
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
