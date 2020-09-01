<?php

namespace App\Service;

use App\ConstValue;
use App\gnr\GnrRegion;
use App\gnr\GnrResponsibility;
use App\hr\ReportingBossRule;
use App\Http\Controllers\microfin\MicroFinance;

use App\gnr\FiscalYear;
use App\gnr\GnrBranch;

use App\hr\AdvancedSalaryLoan;
use App\hr\AdvancedSalaryLoanReceive;
use App\hr\Edps;
use App\hr\EdpsReceive;
use App\hr\EdpsInterest;
use App\hr\EdpsInterestItem;
use App\hr\EmployeeGeneralInfo;
use App\hr\EpsReceive;
use App\hr\OpeningBalanceFund;
use App\hr\ProvidentFundInterest;
use App\hr\OpeningBalanceLoanAdvancedSalary;

use App\hr\OpeningBalanceLoanVehicle;
use App\hr\ProvidentFundInterestReceive;
use App\hr\ProvidentFundReceive;
use App\hr\SalaryStructure;
use App\hr\SecurityMoneyCollection;
use App\hr\SecurityDepositInterest;
use App\hr\SecurityDepositInterestItem;
use App\hr\VehicleLoan;
use App\hr\VehicleLoanReceive;
use App\hr\WelfareFundReceive;
use App\accounting\process\AccYearEnd;
use App\accounting\process\AccMonthEnd;
use App\User;
use Auth;
use DB;
use Route;
use \Carbon\Carbon;
use App\gnr\GnrArea;
use App\gnr\GnrZone;

class Service
{
    public static function getEngagedBranchesByUserId($userId)
    {
        $branchList = [];

        $user = User::with(['employee','employee.organization','employee.organization.position','employee.organization.branch'])
            ->where('id', $userId)
            ->first();

        if($user->isSuperAdminUser()){
            $branchList = GnrBranch::all()->pluck('id')->toArray();
        } else {
            $employee = $user->employee ?? null;
            $organization = $user->employee->organization ?? null;
            $branch = $user->employee->organization->branch ?? null;
            $position = $user->currentPosition() ?? null;
            $reponsibility = GnrResponsibility::where('position_id_fk', $position->id)->get()->filter(function($item) use($employee){
                if($item->emp_id_fk){
                    if($item->emp_id_fk == $employee->id){
                        return true;
                    }
                } else {
                    return true;
                }
            })->first();

            if($reponsibility){
                if($reponsibility->type_code == ConstValue::RESPONSIBILITY_TYPE_CODE_REGION){
                    $regions = GnrRegion::whereIn('id', json_decode($reponsibility->id_list))->get();
                    foreach ($regions as $region) {
                        $zones = GnrZone::whereIn('id', $region->zoneId)->get();
                        foreach ($zones as $zone) {
                            $areas = GnrArea::whereIn('id', $zone->areaId)->get();
                            $areas->each(function ($item) use (&$branchList) {
                                $branchList = array_merge($branchList, $item->branchId);
                            });
                        }
                    }
                }

                if($reponsibility->type_code == ConstValue::RESPONSIBILITY_TYPE_CODE_AREA){
                    $area = GnrArea::whereRaw('branchId Like :bid', [':bid' => '%"' . $user->branch->id . '"%'])->first();
                    if ($area) {
                        $branchList = $area->branchId;
                    }

                }

                if($reponsibility->type_code == ConstValue::RESPONSIBILITY_TYPE_CODE_ZONE){
                    $branchList = [];
                    $area = GnrArea::whereRaw('branchId Like :bid', [':bid' => '%"' . $user->branchId . '"%'])->first();
                    if ($area) {
                        $zone = GnrZone::whereRaw('areaId Like :aid', [':aid' => '%"' . $area->id . '"%'])->first();
                    }
                    $areas = $zone ? GnrArea::whereIn('id', $zone->areaId)->get() : null;
                    $areas->each(function ($item) use (&$branchList) {
                        $branchList = array_merge($branchList, $item->branchId);
                    });
                }

            } else {
                if($branch->id == ConstValue::BRANCH_ID_HEAD_OFFICE){
                    $branchList = GnrBranch::all()->pluck('id')->toArray();
                } else {
                    $branchList = [$organization->branch_id_fk];
                }
            }
        }

        return $branchList;
    }

    public function getUserAdvancedLoanData($uid)
    {

        $advancedLoanOpeningBalance = DB::table('hr_ob_loan_vehicle')->where('users_id_fk', $uid)->get();
        $advancedloans = DB::table('hr_advanced_salary_loan')->where('users_id_fk', $uid)->where('status', 'Approved')->get();

        if ($advancedloans->count() > 0) {
            $loanType = 'asloan';
            $loanId = $advancedloans->first()->id;
        } elseif ($advancedLoanOpeningBalance->count() > 0) {
            $loanType = 'obasloan';
            $loanId = $advancedLoanOpeningBalance->first()->id;
        } else {
            $loanType = '';
            $loanId = 0;
        }

        $opbalance = floatval(@OpeningBalanceLoanAdvancedSalary::select(\DB::raw('SUM(ob_amount) as ob_amount'))->where('users_id_fk', $uid)->first()->ob_amount);

        $curbalance = floatval(@AdvancedSalaryLoan::select(\DB::raw('SUM(approved_amount) as approved_amount'))
            ->where('users_id_fk', $uid)->where('status', 'Approved')->first()->approved_amount);

        $collection = floatval(@AdvancedSalaryLoanReceive::select(\DB::raw('SUM(amount) as amount'))
            ->where('users_id_fk', $uid)->where('payment_status', 'Paid')->first()->amount);

        $opCollection = DB::table('hr_ob_loan_advanced_salary')->where('users_id_fk', $uid)->sum('ob_amount');

        // dd(($opbalance + $curbalance) - ($collection + $opCollection));
        $balance = array(
            'loanType' => $loanType,
            'loanId' => $loanId,
            'loanAmount' => $opbalance + $curbalance,
            'collectionAmount' => $collection + $opCollection,
            'outstanding' => ($opbalance + $curbalance) - ($collection + $opCollection)
        );

        return $balance;
    }

    public function getUserVehicleLoanData($uid, $type)
    {

        $vehicleOpeningBalance = OpeningBalanceLoanVehicle
            ::where('users_id_fk', $uid)
            ->where('settlement_type', '!=', 'Settlement')
            ->where('settlement_type', '!=', 'Return')
            ->where('vehicle_type_fk', $type)
            ->get();

        $vehicleloans = VehicleLoan
            ::where('users_id_fk', $uid)
            ->where('status', 'Approved')
            ->where('vehicle_type_fk', $type)
            ->where('settlement_type', '!=', 'Settlement')
            ->where('settlement_type', '!=', 'Return')
            ->get();
        // dd($vehicleOpeningBalance);

        $vehicleCollection = VehicleLoanReceive::where('users_id_fk', $uid)->get();

        if ($vehicleloans->count() > 0) {

            foreach ($vehicleloans as $key => $loan) {

                $loanAmount = $loan->approved_amount;
                $curcollection = $vehicleCollection->where('vehicle_loan_fk', $loan->id)->where('type', 'Regular')->sum('amount');
                $loanOutstanding = $loanAmount - $curcollection;

                $curBalance[] = array(
                    'loanId' => $loan->id,
                    'principal' => $loanAmount,
                    'collectedPrincipal' => $curcollection,
                    'collectedInterest' => 0,
                    'outstanding' => $loanOutstanding,
                );
            }
        } else {
            $curBalance = [];
        }

        if ($vehicleOpeningBalance->count() > 0) {

            foreach ($vehicleOpeningBalance as $key => $loan) {

                $opbalance = $loan->approved_amount;
                $opcollection = $loan->ob_total + $vehicleCollection
                        ->where('vehicle_loan_fk', $loan->id)
                        ->where('type', 'Opening Balance')
                        ->sum('amount');

                $loanOpOutstanding = $opbalance - $opcollection;

                $opBalance[] = array(
                    'opLoanId' => $loan->id,
                    'opPrincipal' => $opbalance,
                    'opCollectedPrincipal' => $opcollection,
                    'opCollectedInterest' => 0,
                    'opOutstanding' => $loanOpOutstanding,
                );
            }
        } else {
            $opBalance = [];
        }

        $balance = array(
            'opBalance' => $opBalance,
            'curBalance' => $curBalance
        );
        // dd($balance);

        return $balance;
    }

    public static function getUserPfLoanData($uid)
    {

        // $uid = 48;
        // opening balance
        $pfOpeningBalance = DB::table('hr_ob_loan_pf')
            ->where('settlement_type', '!=', 'Settlement')
            ->where('settlement_type', '!=', 'Return')
            ->where('users_id_fk', $uid)
            ->get();


        $pfloans = DB::table('hr_pf_loan')
            ->where('users_id_fk', $uid)
            ->where('settlement_type', '!=', 'Settlement')
            ->where('settlement_type', '!=', 'Return')
            ->where('status', 'Approved')
            ->get();
        // dd($pfOpeningBalance);

        $pfLoanReviews = DB::table('hr_pf_loan_review')->whereIn('pf_loan_id_fk', $pfloans->pluck('id')->toArray())->get();
        $pfReceive = DB::table('hr_pf_loan_receive')->where('users_id_fk', $uid)->get();
        // dd($pfReceive);

        if ($pfloans->count() > 0) {

            foreach ($pfloans as $key => $loan) {

                // $pfloanAmount = $loan->accepted_amount;
                $pfloanAmount = $pfLoanReviews->where('pf_loan_id_fk', $loan->id)->sum('approved_loan_amount');
                $pfLoanReceivePri = $pfReceive->where('pf_loan_fk', $loan->id)->where('type', 'Regular')->sum('principal_amount');
                $pfLoanReceiveInt = $pfReceive->where('pf_loan_fk', $loan->id)->where('type', 'Regular')->sum('interest_amount');
                $pfLoanOutstanding = $pfloanAmount - $pfLoanReceivePri;

                $curBalance[] = array(
                    'loanId' => $loan->id,
                    'principal' => $pfloanAmount,
                    'collectedPrincipal' => $pfLoanReceivePri,
                    'collectedInterest' => $pfLoanReceiveInt,
                    'outstanding' => $pfLoanOutstanding,
                );
            }
        } else {
            $curBalance = [];
        }

        if ($pfOpeningBalance->count() > 0) {

            foreach ($pfOpeningBalance as $key => $loan) {

                $pfloanOpAmount = $loan->loan_amount;
                // $pfloanOpInterestAmount = $loan->interest_amount;

                $pfLoanOpReceivePri = $loan->ob_principal + $pfReceive
                        ->where('pf_loan_fk', $loan->id)
                        ->where('type', 'Opening Balance')
                        ->sum('principal_amount');
                $pfLoanOpReceiveInt = $loan->ob_interest + $pfReceive
                        ->where('pf_loan_fk', $loan->id)
                        ->where('type', 'Opening Balance')
                        ->sum('interest_amount');

                $pfLoanOpOutstanding = $pfloanOpAmount - $pfLoanOpReceivePri;

                $opBalance[] = array(
                    'opLoanId' => $loan->id,
                    'opPrincipal' => $pfloanOpAmount,
                    'opCollectedPrincipal' => $pfLoanOpReceivePri,
                    'opCollectedInterest' => $pfLoanOpReceiveInt,
                    'opOutstanding' => $pfLoanOpOutstanding,
                );
            }
        } else {
            $opBalance = [];
        }

        $balance = array(
            'opBalance' => $opBalance,
            'curBalance' => $curBalance
        );
        // dd($balance);

        return $balance;
    }

    public function getCurrentFiscalYear($date = '', $companyId = 1)
    {

        if ($date == '') {
            $date = date("Y-m-d");
        }
        $findGnr = FiscalYear::where('companyId', $companyId)
            ->whereRaW('(? BETWEEN `fyStartDate` and `fyEndDate`)', [$date])->first();
        return ['id' => $findGnr->id, 'name' => $findGnr->name];
    }

    public static function checkLedgerAccountType($ledgerId)
    {

        $accountTypeIdArr = DB::table('acc_account_type')->pluck('id')->toArray();
        $accountLedgerType = DB::table('acc_account_ledger')->where('id', $ledgerId)->value('accountTypeId');
        $positiveTypeArr = array(1, 2, 3, 4, 5, 13);
        $negetiveTypeArr = array_values(array_diff($accountTypeIdArr, $positiveTypeArr));

        if (in_array($accountLedgerType, $positiveTypeArr)) {
            return true;
        } elseif (in_array($accountLedgerType, $negetiveTypeArr)) {
            return false;
        }
    }

    public function monthEndExecute($branchId, $lastDayEndDate)
    {

        $branchInfo = DB::table('gnr_branch')->where('id', $branchId)->first();
        $monthLastDate = Carbon::parse($lastDayEndDate)->endOfMonth();
        $monthEndDate = $monthLastDate->copy();
        $monthFirstDate = $monthLastDate->copy()->startOfMonth();

        $isMonthLastDayHoliday = 1;
        while ($isMonthLastDayHoliday == 1) {
            $monthLastDateString = $monthLastDate->copy()->format('Y-m-d');
            if (MicroFinance::isHoliday($monthLastDateString, $branchId)) {
                $monthLastDate->subDay();
            } else {
                $isMonthLastDayHoliday = 0;
            }
        }

        $monthEnd = new AccMonthEnd;
        $monthEnd->date = $monthEndDate->format('Y-m-d');
        $monthEnd->branchIdFk = $branchId;
        $monthEnd->isMonthEnd = 1;
        $monthEnd->createdAt = Carbon::now();

        /////// month opening balance data passing ///////////

        // send information to opening balance for next month
        $companyId = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->first()->companyId;
        $monthStartDate = Carbon::parse($monthFirstDate)->format('Y-m-d');
        $monthEndDate = Carbon::parse($monthEndDate)->format('Y-m-d');
        $currentfiscalYearId = DB::table('gnr_fiscal_year')->where('fyStartDate', '<=', $lastDayEndDate)->where('fyEndDate', '>=', $lastDayEndDate)->value('id');
        $lastMonthEndDate = Carbon::parse($lastDayEndDate)->subMonth()->endOfMonth()->format('Y-m-d');
        // dd($monthEndDate);

        if ($branchId == 1) {
            $projectIdArr = DB::table('gnr_project')->pluck('id')->toArray();
        } else {
            $projectIdArr = DB::table('gnr_branch')->where('id', $branchId)->pluck('projectId')->toArray();
        }
        $projectIdArr = [15];
        // dd($projectIdArr);
        // delete previous existing data
        DB::table('acc_month_end_balance')->where('branchId', $branchId)->whereIn('projectId', $projectIdArr)->where('monthEndDate', $monthEndDate)->delete();

        // total
        $totalDebitAmount = 0;
        $totalCreditAmount = 0;
        $dataArr = [];
        $array = [];
        // $projectIdArr = [1];
        foreach ($projectIdArr as $projectId) {
            // dd($projectIdArr);
            $ledgers = $this->getLedgerHeaderInfo($projectId, $branchId, $companyId)->where('isGroupHead', 0);
            // dd($ledgers);
            $cashLedgers = $ledgers->where('accountTypeId', 4)->pluck('id')->toArray();
            $bankLedgers = $ledgers->where('accountTypeId', 5)->pluck('id')->toArray();
            // dd($bankLedgers);
            $projectTypeIdArr = DB::table('gnr_project_type')
                ->where('projectId', $projectId)
                ->pluck('id')->toArray();
            // dd($projectTypeIdArr);
            $dataArr = [];
            // $projectTypeIdArr = [3];
            foreach ($projectTypeIdArr as $projectTypeId) {
                // dd($projectTypeIdArr);
                $voucherInfos = $this->getVoucherInfo($projectTypeId, $projectId, $branchId, $companyId, $monthStartDate, $monthEndDate);
                // dd($voucherInfos);

                foreach ($ledgers as $key => $ledger) {
                    // $ledger->id = 233;
                    $obId = $currentfiscalYearId . "." . $projectId . "." . $branchId . "." . $projectTypeId;
                    // dd($obId);
                    $debitAmount = $voucherInfos->where('debitAcc', $ledger->id)->sum('amount');
                    $creditAmount = $voucherInfos->where('creditAcc', $ledger->id)->sum('amount');
                    // dd($creditAmount);

                    if ($debitAmount == 0 && $creditAmount == 0) {
                    } else {
                        $cashDebit = $voucherInfos->where('debitAcc', $ledger->id)->whereIn('creditAcc', $cashLedgers)->sum('amount');
                        $cashCredit = $voucherInfos->where('creditAcc', $ledger->id)->whereIn('debitAcc', $cashLedgers)->sum('amount');
                        // dd($cashCredit);
                        $bankDebit = $voucherInfos->where('debitAcc', $ledger->id)->whereIn('creditAcc', $bankLedgers)->sum('amount');
                        $bankCredit = $voucherInfos->where('creditAcc', $ledger->id)->whereIn('debitAcc', $bankLedgers)->sum('amount');
                        // dd($bankCredit);
                        $ftDebit = $voucherInfos->where('debitAcc', $ledger->id)->where('voucherTypeId', 5)->sum('amount');
                        $ftCredit = $voucherInfos->where('creditAcc', $ledger->id)->where('voucherTypeId', 5)->sum('amount');
                        // dd($ftCredit);
                        $jvDebit = $voucherInfos->where('debitAcc', $ledger->id)->where('voucherTypeId', 3)->sum('amount');
                        $jvCredit = $voucherInfos->where('creditAcc', $ledger->id)->where('voucherTypeId', 3)->sum('amount');
                        // dd($jvCredit);

                        $dataArr[] = array(

                            'obId' => $obId,
                            'projectId' => $projectId,
                            'branchId' => $branchId,
                            'companyIdFk' => $companyId,
                            'projectTypeId' => $projectTypeId,
                            'monthEndDate' => $monthEndDate,
                            'fiscalYearId' => $currentfiscalYearId,
                            'ledgerId' => $ledger->id,
                            'jvCredit' => $jvCredit,
                            'debitAmount' => $debitAmount,
                            'creditAmount' => $creditAmount,
                            'balanceAmount' => $debitAmount - $creditAmount,
                            'cashDebit' => $cashDebit,
                            'cashCredit' => $cashCredit,
                            'bankDebit' => $bankDebit,
                            'bankCredit' => $bankCredit,
                            'jvDebit' => $jvDebit,
                            'ftDebit' => $ftDebit,
                            'ftCredit' => $ftCredit,
                            'createdDate' => Carbon::now()
                        );
                        // dd($dataArr);

                        // total
                        $totalDebitAmount += $debitAmount;
                        $totalCreditAmount += $creditAmount;
                    }
                } // foreach loop ledger
            }  // foreach loop projectTypeId

            $debitAndCredit = array(
                // 'dataArr'   => $dataArr,
                'debit' => $totalDebitAmount,
                'credit' => $totalCreditAmount,
            );

            if (count($dataArr) == 0) {
                // return;
            } else {
                // check debit and credit match
                // dd($totalDebitAmount);
                if (round($debitAndCredit['debit']) != round($debitAndCredit['credit'])) {

                    $array[] = array(
                        'dataArr' => $dataArr,
                        'debit' => $debitAndCredit['debit'],
                        'credit' => $debitAndCredit['credit'],
                        'diff' => $debitAndCredit['debit'] - $debitAndCredit['credit'],
                        'date' => $monthEndDate,
                        'branchId' => $branchId,
                        'projectId' => $projectId,
                    );
                } // if matched then execute
                else {
                    // dd($dataArr);
                    foreach ($dataArr as $key => $data) {
                        // insert data
                        DB::table('acc_month_end_balance')->insert($data);
                    }
                }
            }
        }   // foreach loop projectId

        $monthEndExists = $monthEnd->where('branchIdFk', $branchId)->where('date', $monthEndDate)->first();
        // dd($monthEndExists);
        if ($monthEndExists) {
            // dd(1);
        } else {
            $monthEnd->save();
        }

        return $array;
    }

    public function yearEndExecute($branchId, $fiscalId)
    {

        $companyId = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->first()->companyId;

        if ($branchId == 1) {
            $projectIdArr = DB::table('gnr_project')->pluck('id')->toArray();
        } else {
            $projectIdArr = DB::table('gnr_branch')->where('id', $branchId)->pluck('projectId')->toArray();
        }
        $projectIdArr = [15];

        $fiscalYearInfo = DB::table('gnr_fiscal_year')->where('companyId', $companyId)->where('id', $fiscalId)->first();
        $yearStartDate = $fiscalYearInfo->fyStartDate;
        $yearEndDate = $fiscalYearInfo->fyEndDate;
        $lastFiscalYearEndDate = Carbon::parse($yearStartDate)->subDays(1)->format('Y-m-d');
        // dd($lastFiscalYearEndDate);

        $yearEnd = new AccYearEnd;

        $yearEnd->date = $yearEndDate;
        $yearEnd->branchIdFk = $branchId;
        $yearEnd->companyId = $companyId;
        $yearEnd->fiscalYearId = $fiscalId;
        $yearEnd->startDate = $fiscalYearInfo->fyStartDate;
        $yearEnd->endDate = $fiscalYearInfo->fyEndDate;
        $yearEnd->createdAt = Carbon::now();
        // dd($yearEnd);

        // send information to opening balance for next fiscal year
        $openingBalanceInfo = DB::table('acc_opening_balance')
            ->where('branchId', $branchId)
            ->where('openingDate', $lastFiscalYearEndDate)
            ->get();

        DB::table('acc_opening_balance')->where('branchId', $branchId)->whereIn('projectId', $projectIdArr)->where('openingDate', $yearEndDate)->delete();

        $dataArr = [];
        $array = [];

        foreach ($projectIdArr as $projectId) {
            // dd($projectIdArr);
            $totalDebitAmount = 0;
            $totalCreditAmount = 0;
            $dataArr = [];


            $ledgers = $this->getLedgerHeaderInfo($projectId, $branchId, $companyId)->where('isGroupHead', 0)->sortBy('id');
            // dd($ledgers);
            $projectTypeIdArr = DB::table('gnr_project_type')
                ->where('projectId', $projectId)
                ->pluck('id')->toArray();

            foreach ($projectTypeIdArr as $projectTypeId) {

                $monthEndsInfo = $this->getMonthEndsInfo($projectTypeId, $projectId, $branchId, $companyId, $yearStartDate, $yearEndDate);
                // dd($monthEndsInfo);

                foreach ($ledgers as $key => $ledger) {
                    // dd($ledger->id);
                    $openingBalanceLedgerInfo = $openingBalanceInfo
                        ->where('ledgerId', $ledger->id)
                        ->where('projectId', $projectId)
                        ->where('projectTypeId', $projectTypeId);
                    // dd($openingBalanceLedgerInfo);
                    $openingDebitAmount = $openingBalanceLedgerInfo->sum('debitAmount');
                    $openingCreditAmount = $openingBalanceLedgerInfo->sum('creditAmount');
                    $openingCashDebit = $openingBalanceLedgerInfo->sum('cashDebit');
                    $openingCashCredit = $openingBalanceLedgerInfo->sum('cashCredit');
                    $openingBankDebit = $openingBalanceLedgerInfo->sum('bankDebit');
                    $openingBankCredit = $openingBalanceLedgerInfo->sum('bankCredit');
                    $openingJvDebit = $openingBalanceLedgerInfo->sum('jvDebit');
                    $openingJvCredit = $openingBalanceLedgerInfo->sum('jvCredit');
                    $openingFtDebit = $openingBalanceLedgerInfo->sum('ftDebit');
                    $openingFtCredit = $openingBalanceLedgerInfo->sum('ftCredit');

                    $obId = $fiscalId . "." . $projectId . "." . $branchId . "." . $projectTypeId;
                    $debitAmount = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('debitAmount') + $openingDebitAmount;
                    $creditAmount = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('creditAmount') + $openingCreditAmount;
                    $cashDebit = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('cashDebit') + $openingCashDebit;
                    $cashCredit = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('cashCredit') + $openingCashCredit;
                    $bankDebit = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('bankDebit') + $openingBankDebit;
                    $bankCredit = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('bankCredit') + $openingBankCredit;
                    $jvDebit = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('jvDebit') + $openingJvDebit;
                    $jvCredit = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('jvCredit') + $openingJvCredit;
                    $ftDebit = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('ftDebit') + $openingFtDebit;
                    $ftCredit = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('ftCredit') + $openingFtCredit;
                    // dd($creditAmount);

                    // total
                    $totalDebitAmount += $debitAmount;
                    $totalCreditAmount += $creditAmount;

                    $dataArr[] = array(
                        'obId' => $obId,
                        'projectId' => $projectId,
                        'branchId' => $branchId,
                        'companyIdFk' => $companyId,
                        'projectTypeId' => $projectTypeId,
                        'openingDate' => $yearEndDate,
                        'fiscalYearId' => $fiscalId,
                        'ledgerId' => $ledger->id,
                        'debitAmount' => $debitAmount,
                        'creditAmount' => $creditAmount,
                        'balanceAmount' => $debitAmount - $creditAmount,
                        'cashDebit' => $cashDebit,
                        'cashCredit' => $cashCredit,
                        'bankDebit' => $bankDebit,
                        'bankCredit' => $bankCredit,
                        'jvDebit' => $jvDebit,
                        'jvCredit' => $jvCredit,
                        'ftDebit' => $ftDebit,
                        'ftCredit' => $ftCredit,
                        'createdDate' => Carbon::now(),
                        'ds' => 0
                    );

                } // foreach loop ledger
            }  // foreach loop projectTypeId

            $debitAndCredit = array(
                'debit' => $totalDebitAmount,
                'credit' => $totalCreditAmount,
            );
            // dd($debitAndCredit);
            // check debit and credit match
            if (round($debitAndCredit['debit']) != round($debitAndCredit['credit'])) {

                $array[] = array(
                    'dataArr' => $dataArr,
                    'debit' => $debitAndCredit['debit'],
                    'credit' => $debitAndCredit['credit'],
                    'diff' => $debitAndCredit['debit'] - $debitAndCredit['credit'],
                    'date' => $yearEndDate,
                    'branchId' => $branchId,
                    'projectId' => $projectId,
                );
            } // if matched then execute
            else {
                foreach ($dataArr as $key => $data) {

                    DB::table('acc_opening_balance')->insert($data);

                } // foreach loop db insert/update
            } // execution close
        }   // foreach loop projectId

        $yearEndExists = $yearEnd->where('branchIdFk', $branchId)->where('fiscalYearId', $fiscalId)->first();
        // dd($yearEndExists);
        if ($yearEndExists) {
        } else {
            $yearEnd->save();
        }

        return $array;
    }

    public function addOpeningBalance($projectId, $projectTypeId, $branchId, $fiscalId, $ledgerId)
    {

        $companyId = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->first()->companyId;
        $fiscalYearInfo = DB::table('gnr_fiscal_year')->where('companyId', $companyId)->where('id', $fiscalId)->first();
        $yearStartDate = $fiscalYearInfo->fyStartDate;
        $yearEndDate = $fiscalYearInfo->fyEndDate;
        $lastFiscalYearEndDate = Carbon::parse($yearStartDate)->subDays(1)->format('Y-m-d');
        // dd($lastFiscalYearEndDate);

        $voucherInfos = $this->getVoucherInfo($projectTypeId, $projectId, $branchId, $companyId, $yearStartDate, $yearEndDate);

        $openingBalanceInfo = DB::table('acc_opening_balance')
            ->where('ledgerId', $ledgerId)
            ->where('projectId', $projectId)
            ->where('projectTypeId', $projectTypeId)
            ->where('branchId', $branchId)
            ->where('openingDate', $lastFiscalYearEndDate)
            ->get();
        // dd($openingBalanceInfo);

        if ($openingBalanceInfo->count() > 0) {
            $openingDebitAmount = $openingBalanceInfo->sum('debitAmount');
            $openingCreditAmount = $openingBalanceInfo->sum('creditAmount');
        } else {
            $openingDebitAmount = 0;
            $openingCreditAmount = 0;
        }
        // dd($openingDebitAmount);

        $obId = $fiscalId . "." . $projectId . "." . $branchId . "." . $projectTypeId;
        // dd($obId);
        $debitAmount = $voucherInfos->where('debitAcc', $ledgerId)->sum('amount') + $openingDebitAmount;
        // $debitAmount = $voucherInfos->where('debitAcc', $ledgerId)->sum('amount');
        // dd($debitAmount);
        $creditAmount = $voucherInfos->where('creditAcc', $ledgerId)->sum('amount') + $openingCreditAmount;
        // $creditAmount = $voucherInfos->where('creditAcc', $ledgerId)->sum('amount');
        // dd($creditAmount);

        $data = array(

            'obId' => $obId,
            'projectId' => $projectId,
            'branchId' => $branchId,
            'companyIdFk' => $companyId,
            'projectTypeId' => $projectTypeId,
            'openingDate' => $yearEndDate,
            'fiscalYearId' => $fiscalId,
            'ledgerId' => $ledgerId,
            'debitAmount' => round((float)$debitAmount, 2),
            'creditAmount' => round((float)$creditAmount, 2),
            'balanceAmount' => round((float)$debitAmount - $creditAmount, 2)
        );

        dd($data);
    }

    public function getLedgerHeaderInfos($projectId, $branchIdArr, $companyId, $fundTrExclude = 0)
    {
        $allLedgers = DB::table('acc_account_ledger')->where('companyIdFk', $companyId)
            // ->where('isGroupHead', 0)
            ->select('id', 'projectBranchId')
            ->get();

        $matchedId = array();

        foreach ($allLedgers as $singleLedger) {
            $splitArray = str_replace(array('[', ']', '"', ''), '', $singleLedger->projectBranchId);

            $splitArrayFirstValue = explode(",", $splitArray);
            $splitArraySecondValue = explode(":", $splitArrayFirstValue[0]);

            $array_length = substr_count($splitArray, ",");
            $arrayProjects = array();
            $temp = null;
            for ($i = 0; $i < $array_length + 1; $i++) {

                $splitArrayFirstValue = explode(",", $splitArray);

                $splitArraySecondValue = explode(":", $splitArrayFirstValue[$i]);
                $firstIndexValue = (int)$splitArraySecondValue[0];
                $secondIndexValue = (int)$splitArraySecondValue[1];

                if ($firstIndexValue == 0) {
                    if ($secondIndexValue == 0) {
                        array_push($matchedId, $singleLedger->id);
                    }
                } else {
                    // dd($projectId);
                    if ($firstIndexValue == $projectId) {
                        if ($secondIndexValue == 0) {
                            array_push($matchedId, $singleLedger->id);
                        } elseif (in_array($secondIndexValue, $branchIdArr)) {
                            array_push($matchedId, $singleLedger->id);
                        }
                    }
                }
            }   //for
        }       //foreach

        $ledgers = DB::table('acc_account_ledger')
                ->whereIn('id', $matchedId)
                // ->where('status', 1)
                ->where('companyIdFk', $companyId)
                ->select('id', 'name', 'code', 'accountTypeId', 'isGroupHead', 'level', 'parentId')
                ->orderBy('code')
                ->get();
                // dd($ledgers);

        return $ledgers;
    }

    public function getLedgerHeaderInfo($projectId, $branchId, $companyId)
    {
        $allLedgers = DB::table('acc_account_ledger')->where('companyIdFk', $companyId)
            // ->where('isGroupHead', 0)
            ->select('id', 'projectBranchId')
            ->get();

        $matchedId = array();

        foreach ($allLedgers as $singleLedger) {
            $splitArray = str_replace(array('[', ']', '"', ''), '', $singleLedger->projectBranchId);

            $splitArrayFirstValue = explode(",", $splitArray);
            $splitArraySecondValue = explode(":", $splitArrayFirstValue[0]);

            $array_length = substr_count($splitArray, ",");
            $arrayProjects = array();
            $temp = null;
            for ($i = 0; $i < $array_length + 1; $i++) {

                $splitArrayFirstValue = explode(",", $splitArray);

                $splitArraySecondValue = explode(":", $splitArrayFirstValue[$i]);
                $firstIndexValue = (int)$splitArraySecondValue[0];
                $secondIndexValue = (int)$splitArraySecondValue[1];

                if ($firstIndexValue == 0) {
                    if ($secondIndexValue == 0) {
                        array_push($matchedId, $singleLedger->id);
                    }
                } else {
                    // dd($projectId);
                    if ($firstIndexValue == $projectId) {
                        if ($secondIndexValue == 0) {
                            array_push($matchedId, $singleLedger->id);
                        } elseif ($secondIndexValue == $branchId) {
                            array_push($matchedId, $singleLedger->id);
                        }
                    }
                }
            }   //for
        }       //foreach

        $ledgers = DB::table('acc_account_ledger')
            ->whereIn('acc_account_ledger.id', $matchedId)
            ->where('status', 1)
            ->where('companyIdFk', $companyId)
            ->select('id', 'name', 'code', 'accountTypeId', 'isGroupHead', 'level', 'parentId')
            ->orderBy('code')
            ->get();
        // dd($ledgers->where('code', 55100));

        return $ledgers;
    }

    public function getVoucherInfo($projectTypeId, $projectId, $branchId, $companyId, $yearStartDate, $yearEndDate)
    {
        $voucherInfo = DB::table('acc_voucher')
            ->join('acc_voucher_details', 'acc_voucher_details.voucherId', '=', 'acc_voucher.id')
            ->where('voucherDate', '>=', $yearStartDate)
            ->where('voucherDate', '<=', $yearEndDate)
            ->where('companyId', $companyId)
            ->where('projectId', $projectId)
            ->where('branchId', $branchId)
            ->where('projectTypeId', $projectTypeId)
            ->select(
                'acc_voucher_details.debitAcc',
                'acc_voucher_details.creditAcc',
                'acc_voucher_details.amount',
                'acc_voucher.projectId',
                'acc_voucher.projectTypeId',
                'acc_voucher.voucherTypeId'
            )
            ->get();
        // dd($voucherInfo->where('creditAcc', 361)->sum('amount'));

        return $voucherInfo;
    }

    public function getMonthEndsInfo($projectTypeId, $projectId, $branchId, $companyId, $yearStartDate, $yearEndDate)
    {

        $monthEndsInfo = DB::table('acc_month_end_balance')
            ->where('monthEndDate', '>=', $yearStartDate)
            ->where('monthEndDate', '<=', $yearEndDate)
            ->where('companyIdFk', $companyId)
            ->where('projectId', $projectId)
            ->where('branchId', $branchId)
            ->where('projectTypeId', $projectTypeId)
            ->select(
                'debitAmount',
                'creditAmount',
                'balanceAmount',
                'fiscalYearId',
                'ledgerId',
                'obId',
                'monthEndDate',
                'cashDebit',
                'cashCredit',
                'bankDebit',
                'bankCredit',
                'jvDebit',
                'jvCredit',
                'ftDebit',
                'ftCredit'
            )
            ->get();
        // dd($voucherInfo->where('creditAcc', 361)->sum('amount'));

        return $monthEndsInfo;
    }

    public function deleteYearEnd($yearEndId)
    {

        $yearEnd = AccYearEnd::find($yearEndId);

        $accOpeningBalance = DB::table('acc_opening_balance')
            ->where('branchId', $yearEnd->branchIdFk)
            ->where('openingDate', $yearEnd->date)
            ->delete();

        $yearEnd->delete();
    }

    public static function negativeReplace($value)
    {

        $replaced = preg_replace('/(-)([\d\.\,]+)/ui', '($2)', number_format($value, 2));

        return $replaced;
    }

    public function getUserPfInfo($userId)
    {

        $opPfData = OpeningBalanceFund::where('users_id_fk', $userId)->get();
        $pfData = ProvidentFundReceive::where('users_id_fk', $userId)->get();
        $pfInterestData = ProvidentFundInterest::select('id', 'generate_date')->get();
        $pfInterestReceiveData = ProvidentFundInterestReceive::where('users_id_fk', $userId)->get();
        // dd($pfInterestReceiveData);

        // opening balance
        if ($opPfData->count() > 0) {
            foreach ($opPfData as $key => $item) {

                if ($item->pf_own == 0 && $item->pf_interest == 0 && $item->pf_org == 0) {
                    $data['opPf'] = [];
                } else {
                    $data['opPf'][] = array(
                        'date' => 'Opening Balance',
                        'own' => $item->pf_own,
                        'org' => $item->pf_org,
                        'interest' => $item->pf_interest,
                    );
                }
            }
        } else {
            $data['opPf'] = [];
        }

        // pf data
        if ($pfData->count() > 0) {
            foreach ($pfData as $key => $item) {
                $data['pf'][] = array(
                    'date' => $item->transaction_date,
                    'own' => $item->emp_amount,
                    'org' => $item->org_amount,
                    'interest' => 0,
                );
            }
        } else {
            $data['pf'] = [];
        }

        // interest
        if ($pfInterestReceiveData->count() > 0) {
            foreach ($pfInterestReceiveData as $key => $item) {
                $data['pfInt'][] = array(
                    'date' => $pfInterestData->where('id', $item->interest_id_fk)->first()->generate_date,
                    'own' => 0,
                    'org' => 0,
                    'interest' => $item->profit_amount,
                );
            }
        } else {
            $data['pfInt'] = [];
        }

        $opData = collect($data['opPf']);
        $data = array_merge($data['pf'], $data['pfInt']);
        $data = collect($data)->sortBy('date');
        $pfInfo = $opData->merge($data);

        return $pfInfo;
    }

    public function getUserEdpsInfo($userId)
    {

        $opEdpsData = OpeningBalanceFund::where('users_id_fk', $userId)->get();
        $edps = Edps::where('users_id_fk', $userId)->first();
        $edpsData = EdpsReceive::where('users_id_fk', $userId)->get();
        $edpsInterestData = EdpsInterest::select('id', 'generate_date')->get();
        $edpsInterestReceiveData = EdpsInterestItem::where('users_id_fk', $userId)->get();
        // dd($edpsInterestReceiveData);

        // opening balance
        if ($opEdpsData->count() > 0) {
            foreach ($opEdpsData as $key => $item) {

                if ($item->edps == 0 && $item->edps_interest == 0) {
                    $data['opEdps'] = [];
                } else {
                    $data['opEdps'][] = array(
                        'date' => 'Opening Balance',
                        'own' => $item->edps,
                        'interest' => $item->edps_interest,
                    );
                }
            }
        } else {
            $data['opEdps'] = [];
        }

        // edps data
        if ($edpsData->count() > 0) {
            foreach ($edpsData as $key => $item) {
                $data['edps'][] = array(
                    'date' => $item->transaction_date,
                    'own' => $item->amount,
                    'interest' => 0,
                );
            }
        } else {
            $data['edps'] = [];
        }

        // interest
        if ($edpsInterestReceiveData->count() > 0) {
            foreach ($edpsInterestReceiveData as $key => $item) {

                if (strtotime(date('01-m-Y', strtotime($edps->end_month))) > strtotime(date('d-m-Y'))) {
                    $interest = $item->amount_before_matured;
                } else {
                    $interest = $item->amount_after_matured;
                }

                $data['edpsInt'][] = array(
                    'date' => $edpsInterestData->where('id', $item->edps_interest_id_fk)->first()->generate_date,
                    'own' => 0,
                    'interest' => $interest,
                );
            }
        } else {
            $data['edpsInt'] = [];
        }

        $opData = collect($data['opEdps']);
        $data = array_merge($data['edps'], $data['edpsInt']);
        $data = collect($data)->sortBy('date');
        $edpsInfo = $opData->merge($data);

        return $edpsInfo;
    }

    public function getUserSecurityFundInfo($userId)
    {

        $emp = User::find($userId)->employee;
        $opSfData = OpeningBalanceFund::where('users_id_fk', $userId)->get();
        $sfData = SecurityMoneyCollection::where('emp_id_fk', $emp->id)->get();
        $sfInterestData = SecurityDepositInterest::select('id', 'to_month')->get();
        $sfInterestReceiveData = SecurityDepositInterestItem::where('users_id_fk', $userId)->get();
        // dd($sfInterestReceiveData);

        // opening balance
        if ($opSfData->count() > 0) {
            foreach ($opSfData as $key => $item) {

                if ($item->security_deposit == 0 && $item->sd_interest == 0) {
                    $data['opSf'] = [];
                } else {
                    $data['opSf'][] = array(
                        'date' => 'Opening Balance',
                        'own' => $item->security_deposit,
                        'interest' => $item->sd_interest,
                    );
                }
            }
        } else {
            $data['opSf'] = [];
        }

        // security fund data
        if ($sfData->count() > 0) {
            foreach ($sfData as $key => $item) {
                $data['sf'][] = array(
                    'date' => $item->received_at,
                    'own' => $item->amount,
                    'interest' => 0,
                );
            }
        } else {
            $data['sf'] = [];
        }

        //interest
        if ($sfInterestReceiveData->count() > 0) {
            foreach ($sfInterestReceiveData as $key => $item) {

                $data['sfInt'][] = array(
                    'date' => $sfInterestData->where('id', $item->sd_interest_id_fk)->first()->to_month,
                    'own' => 0,
                    'interest' => $item->net_amount,
                );
            }
        } else {
            $data['sfInt'] = [];
        }

        $opData = collect($data['opSf']);
        $data = array_merge($data['sf'], $data['sfInt']);
        $data = collect($data)->sortBy('date');
        $sfInfo = $opData->merge($data);

        return $sfInfo;
    }

    public function getUserWelfareFundInfo($userId)
    {

        $opWfData = OpeningBalanceFund::where('users_id_fk', $userId)->get();
        $wfData = WelfareFundReceive::where('users_id_fk', $userId)->get();
        // dd($opWfData);

        // opening balance
        if ($opWfData->count() > 0) {
            foreach ($opWfData as $key => $item) {

                if ($item->wf_refundable == 0) {
                    $data['opWf'] = [];
                } else {
                    $data['opWf'][] = array(
                        'date' => 'Opening Balance',
                        'own' => $item->wf_refundable,
                    );
                }
            }
        } else {
            $data['opWf'] = [];
        }

        // welfare fund data
        if ($wfData->count() > 0) {
            foreach ($wfData as $key => $item) {
                $data['wf'][] = array(
                    'date' => $item->transaction_date,
                    'own' => $item->emp_amount,
                );
            }
        } else {
            $data['wf'] = [];
        }

        $opData = collect($data['opWf']);
        $data = collect($data['wf'])->sortBy('date');
        $wfInfo = $opData->merge($data);

        return $wfInfo;
    }

    public function getUserEpsInfo($userId)
    {

        $opEpsData = OpeningBalanceFund::where('users_id_fk', $userId)->get();
        $epsData = EpsReceive::where('users_id_fk', $userId)->get();
        $epsInterestData = DB::table('hr_eps_benefits')->select('id', 'generate_date')->get();
        $epsInterestReceiveData = DB::table('hr_eps_benefit_items')->where('users_id_fk', $userId)->get();
        // dd($epsInterestReceiveData);

        // opening balance
        if ($opEpsData->count() > 0) {
            foreach ($opEpsData as $key => $item) {

                if ($item->eps == 0 && $item->eps_interest == 0) {
                    $data['opEps'] = [];
                } else {
                    $data['opEps'][] = array(
                        'date' => 'Opening Balance',
                        'own' => $item->eps,
                        'interest' => $item->eps_interest,
                    );
                }
            }
        } else {
            $data['opEps'] = [];
        }

        // pension scheme data
        if ($epsData->count() > 0) {
            foreach ($epsData as $key => $item) {
                $data['eps'][] = array(
                    'date' => $item->transaction_date,
                    'own' => $item->amount,
                    'interest' => 0,
                );
            }
        } else {
            $data['eps'] = [];
        }

        //interest
        if ($epsInterestReceiveData->count() > 0) {
            foreach ($epsInterestReceiveData as $key => $item) {

                $data['epsInt'][] = array(
                    'date' => $epsInterestData->where('id', $item->eps_benefit_id_fk)->first()->generate_date,
                    'own' => 0,
                    'interest' => $item->contribute_amount,
                );
            }
        } else {
            $data['epsInt'] = [];
        }

        $opData = collect($data['opEps']);
        $data = array_merge($data['eps'], $data['epsInt']);
        $data = collect($data)->sortBy('date');
        $epsInfo = $opData->merge($data);

        return $epsInfo;
    }

    public function getUserGratuityInfo($userId)
    {

        $employeeId = DB::table('users')->where('id', $userId)->first()->emp_id_fk;
        $employee = EmployeeGeneralInfo::where('id', $employeeId)->first();

        $opGratuityData = OpeningBalanceFund::where('users_id_fk', $userId)->get();
        $gratuityData = DB::table('hr_gratuity_item')->where('users_id_fk', $userId)
            ->join('hr_gratuity', 'hr_gratuity_item.gratuity_id_fk', '=', 'hr_gratuity.id')
            ->get();
        // dd($opGratuityData);

        // opening balance
        if ($opGratuityData->count() > 0) {
            foreach ($opGratuityData as $key => $item) {

                if ($item->gratuity_fund == 0) {
                    $data['opGratuity'] = [];
                } else {

                    $opBasicSalary = Helper::getEmployeeBasicSalaryWithTargetDate($employee, $item->ob_date);
                    $opJobDuration = Helper::getJobDurationIntervalWithTargetDateByEmployeeId($employeeId, $item->ob_date);
                    $jobDuration = array(
                        'year' => $opJobDuration->y,
                        'month' => $opJobDuration->m,
                    );

                    $data['opGratuity'][] = array(
                        'date' => 'Opening Balance',
                        'obDate' => $item->ob_date,
                        'obJobDuration' => $jobDuration,
                        'org' => $item->gratuity_fund,
                    );
                }
            }
        } else {
            $data['opGratuity'] = [];
        }
        // dd($data['opGratuity']);

        // gratuity data
        if ($gratuityData->count() > 0) {
            foreach ($gratuityData as $key => $item) {

                $jobDuration = array(
                    'year' => $item->job_duration_year,
                    'month' => $item->job_duration_month % 12
                );

                $data['gratuity'][] = array(
                    'date' => $item->generate_date,
                    'org' => $item->net_amount,
                    'basicSalary' => $item->basic_salary,
                    'jobDuration' => $jobDuration,
                );
            }
        } else {
            $data['gratuity'] = [];
        }
        // dd($data['gratuity']);

        $opData = collect($data['opGratuity']);
        $data = collect($data['gratuity'])->sortBy('date');
        $gratuityInfo = $opData->merge($data);
        // dd($gratuityInfo);

        return $gratuityInfo;
    }

    public static function getUserSalaryStructureByTagetDate($employee, $targetDate)
    {

        $user = $employee->user;
        $orgInfo = $employee->organization;
        $data = [];
        $promotionIncrement = Helper::getEmployeePromotionIncrementByTargetDate($employee, $targetDate);

        if ($promotionIncrement) {
            $previousData = json_decode($promotionIncrement->previous_data, true);
            $q = SalaryStructure::where('company_id_fk', $orgInfo->company_id_fk)
                ->where('project_id_fk', $orgInfo->project_id_fk)
                ->where('grade_id_fk', $previousData['value']['grade']['id'])
                ->where('level_id_fk', $previousData['value']['level']['id'])
                ->where('fiscal_year_fk', $previousData['value']['fiscal_year']['id'])
                ->whereRaw('FIND_IN_SET(?,position_id_fk)', [$orgInfo->position_id_fk])
                ->whereRaw('FIND_IN_SET(?,recruitment_type_fk)', $previousData['value']['recruitment_type']['id'])
                ->orderby('id', 'desc')
                ->limit(1)
                ->first();
        } else {
            $q = SalaryStructure::where('company_id_fk', $orgInfo->company_id_fk)
                ->where('project_id_fk', $orgInfo->project_id_fk)
                ->where('grade_id_fk', $orgInfo->grade)
                ->where('level_id_fk', $orgInfo->level_id_fk)
                ->where('fiscal_year_fk', $orgInfo->fiscal_year_fk)
                ->whereRaw('FIND_IN_SET(?,position_id_fk)', [$orgInfo->position_id_fk])
                ->whereRaw('FIND_IN_SET(?,recruitment_type_fk)', [$orgInfo->recruitment_type])
                ->orderby('id', 'desc')
                ->limit(1)
                ->first();
        }

        $data = $q;
        if (!$data) {
            $data = [];
        }

        return $data;
    }

    public static function getFilteredBranhIds($filReportLevel, $filBranch, $filArea, $filZone, $filRegion)
    {
        $userBranchId = Auth::user()->branchId;

        if ($userBranchId != 1) {
            $filBranchIds = [$userBranchId];
        } else {
            /// Report Level Branch
            if ($filReportLevel == "Branch") {

                if ($filBranch == "All") {
                    $filBranchIds = DB::table('gnr_branch')->pluck('id')->toArray();
                } elseif ($filBranch == 0) {
                    $filBranchIds = DB::table('gnr_branch')->where('id', '!=', 1)->pluck('id')->toArray();
                } elseif ($filBranch != '' || $filBranch != null || $filBranch > 0) {
                    $filBranch = (int)$filBranch;
                    $filBranchIds = [$filBranch];
                } else {
                    $filBranchIds = array_map('intval', DB::table('gnr_branch')->pluck('id')->toArray());
                }
            } /// Report Level Area
            elseif ($filReportLevel == "Area") {
                $filBranchIds = array_map('intval', explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_area')->where('id', $filArea)->value('branchId'))));
            } /// Report Level Zone
            elseif ($filReportLevel == "Zone") {
                $areaIds = explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_zone')->where('id', $filZone)->value('areaId')));

                $filBranchIds = array();
                foreach ($areaIds as $key => $areaId) {
                    $branchIds = array_map('intval', explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_area')->where('id', $areaId)->value('branchId'))));
                    $filBranchIds = array_merge($filBranchIds, $branchIds);
                    $filBranchIds = array_unique($filBranchIds);
                }
            } /// Report Level Region
            elseif ($filReportLevel == "Region") {
                $zoneIds = explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_region')->where('id', $filRegion)->value('zoneId')));

                $filBranchIds = array();
                foreach ($zoneIds as $zoneId) {
                    $areaIds = explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_zone')->where('id', $zoneId)->value('areaId')));
                    foreach ($areaIds as $key => $areaId) {
                        $branchIds = array_map('intval', explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_area')->where('id', $areaId)->value('branchId'))));
                        $filBranchIds = array_merge($filBranchIds, $branchIds);
                        $filBranchIds = array_unique($filBranchIds);
                    }
                }
            }
        }

        return $filBranchIds;
    }

    public static function getFilteredBranchIds($projectId, $filReportLevel, $filBranch, $filArea, $filZone, $filRegion)
    {
        $userBranch = Auth::user()->branch;
        $user_company_id = Auth::user()->company_id_fk;

        if ($userBranch->branchCode != 0) {
            $filBranchIds = [$userBranch->id];
        } else {
            /// Report Level Branch
            if ($filReportLevel == "Branch") {

                if ($filBranch == "All") {
                    $filBranchIds = DB::table('gnr_branch')->where('companyId', $user_company_id)->where('projectId', $projectId)->pluck('id')->toArray();
                    // if ($projectId != 1) {
                        array_push($filBranchIds, $userBranch->id);
                    // }
                } elseif ($filBranch == 0) {
                    $filBranchIds = DB::table('gnr_branch')->where('companyId', $user_company_id)->where('projectId', $projectId)->where('branchCode', '!=', 0)->pluck('id')->toArray();
                } elseif ($filBranch != '' || $filBranch != null || $filBranch > 0) {
                    $filBranch = (int)$filBranch;
                    $filBranchIds = [$filBranch];
                } else {
                    $filBranchIds = array_map('intval', DB::table('gnr_branch')->where('companyId', $user_company_id)->where('projectId', $projectId)->pluck('id')->toArray());
                }
            } /// Report Level Area
            elseif ($filReportLevel == "Area") {
                $filBranchIds = array_map('intval', explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_area')->where('id', $filArea)->value('branchId'))));
            } /// Report Level Zone
            elseif ($filReportLevel == "Zone") {
                $areaIds = explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_zone')->where('id', $filZone)->value('areaId')));

                $filBranchIds = array();
                foreach ($areaIds as $key => $areaId) {
                    $branchIds = array_map('intval', explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_area')->where('id', $areaId)->value('branchId'))));
                    $filBranchIds = array_merge($filBranchIds, $branchIds);
                    $filBranchIds = array_unique($filBranchIds);
                }
            } /// Report Level Region
            elseif ($filReportLevel == "Region") {
                $zoneIds = explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_region')->where('id', $filRegion)->value('zoneId')));

                $filBranchIds = array();
                foreach ($zoneIds as $zoneId) {
                    $areaIds = explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_zone')->where('id', $zoneId)->value('areaId')));
                    foreach ($areaIds as $key => $areaId) {
                        $branchIds = array_map('intval', explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_area')->where('id', $areaId)->value('branchId'))));
                        $filBranchIds = array_merge($filBranchIds, $branchIds);
                        $filBranchIds = array_unique($filBranchIds);
                    }
                }
            }
        }

        return $filBranchIds;
    }

    public static function getProjectType(Request $request)
    {

        $projectTypes = DB::table('gnr_project_type')->select('name', 'id')
            ->where('projectId', $request->projectId)
            ->get();

        return response()->json($projectTypes);
    }

    public function finalLevelLedgers($firstChildId)
    {

        $grandChildrenLedgers = DB::table('acc_account_ledger')
            ->select('id', 'code', 'isGroupHead')
            ->where('parentId', $firstChildId)
            ->orderBy('ordering', 'asc')
            ->get();
        // dd($grandChildrenLedgers);

        foreach ($grandChildrenLedgers as $key => $grandChild) {

            if ($grandChild->isGroupHead == 0) {
                $finalLedgers = $grandChildrenLedgers->pluck('id')->toArray();
                // dd($finalLedgers);
            } elseif ($grandChild->isGroupHead == 1) {

                $lastGrandChildrenLedgers = DB::table('acc_account_ledger')
                    ->select('id', 'code', 'isGroupHead')
                    ->where('parentId', $grandChild->id)
                    ->orderBy('ordering', 'asc')
                    ->get();
                // dd($lastGrandChildrenLedgers);

                foreach ($lastGrandChildrenLedgers as $key => $lastGrandChild) {

                    if ($lastGrandChild->isGroupHead == 0) {
                        $finalLedgers = $lastGrandChildrenLedgers->pluck('id')->toArray();
                        // dd($finalLedgers);
                    } elseif ($lastGrandChild->isGroupHead == 1) {
                        $finalGrandChildrenLedgers = DB::table('acc_account_ledger')
                            ->select('id', 'code', 'isGroupHead')
                            ->where('parentId', $lastGrandChild->id)
                            ->orderBy('ordering', 'asc')
                            ->get();
                        // dd($finalGrandChildrenLedgers);

                        $finalLedgers = $finalGrandChildrenLedgers->pluck('id')->toArray();
                    }
                }
            }
        }
        // dd($finalLedgers);

        return $finalLedgers;
    }

    public function getParentsNChildsLedgers($ledgerCodeArr)
    {

        $finalArr = [];
        foreach ($ledgerCodeArr as $key => $ledger) {
            $ledgerInfo = DB::table('acc_account_ledger')->where('code', $ledger)->first();
            $ledgerId = $ledgerInfo->id;
            $level = $ledgerInfo->level;
            $parentId = $ledgerInfo->parentId;
            $finalArr[] = $ledgerId;
            // top level $ledgers
            for ($i = $level; $i > 1; --$i) {
                $parent = DB::table('acc_account_ledger')->where('id', $parentId)->first();
                $finalArr[] = $parent->id;
                $parentId = $parent->parentId;
            }
            // bottom level ledgers
            $parentLedgers = [$ledgerId];
            for ($i = $level; $i < 5; ++$i) {
                foreach ($parentLedgers as $parentLedger) {
                    $childs = DB::table('acc_account_ledger')->where('parentId', $parentLedger)->get();
                    foreach ($childs as $key => $child) {
                        $finalArr[] = $child->id;
                        $parentLedgers[] = $child->id;
                    }
                }
            }
        }

        return array_unique($finalArr);
    }

    public static function updateOpBalanceGenerateType()
    {

        $branchLists = DB::table('gnr_branch')->pluck('aisStartDate', 'id')->toArray();
        // dd($branchLists);
        foreach ($branchLists as $branchId => $aisStartDate) {
            $openingBalanceData = DB::table('acc_opening_balance')
                ->where('branchId', $branchId)
                ->where('openingDate', '<=', $aisStartDate)
                ->update(['generateType' => 1]);
            // dd($openingBalanceData);
        }
        // dd($openingBalanceData);

    }

    public static function voucherCorrection()
    {

        $voucherConfig = DB::table('acc_mfn_av_config_loan')
            ->groupBy('ledgerIdForPrincipal')
            ->pluck('ledgerIdForInterest', 'ledgerIdForPrincipal')
            ->toArray();

        foreach ($voucherConfig as $key => $config) {
            $principalInterestArr[] = array(
                'principalLedger' => $key,
                'interestLedger' => $config
            );
        }
        // dd($principalInterestArr);

        $diff = 0;
        $update = [];
        $showSingle = [];
        $showMultiple = [];

        foreach ($principalInterestArr as $key => $singleArray) {

            $voucherInfo = DB::table('acc_voucher')
                ->join('acc_voucher_details', 'acc_voucher_details.voucherId', '=', 'acc_voucher.id')
                // ->where('voucherTypeId', 2)
                // ->where('vGenerateType', 1)
                ->where('debitAcc', 622)
                // ->where('branchId', '>=', 66)
                // ->where('branchId', '<=', 83)
                // ->where('branchId', 30)
                ->whereIn('creditAcc', [$singleArray['principalLedger'], $singleArray['interestLedger']])
                ->select(
                    'acc_voucher_details.id',
                    'acc_voucher_details.voucherId',
                    'acc_voucher_details.amount',
                    'acc_voucher_details.creditAcc'
                )
                ->get();
            // dd($voucherInfo);
            $voucherIdArr = $voucherInfo->unique('voucherId')->pluck('voucherId')->toArray();
            // $voucherIdArr = [25302];
            // dd($voucherIdArr);
            foreach ($voucherIdArr as $key => $vId) {

                $voucherLoanInfo = $voucherInfo->where('voucherId', $vId);
                // dd($voucherLoanInfo);
                if ($voucherLoanInfo->count() > 2) {
                    // $principalAmount = $voucherLoanInfo->where('creditAcc', $singleArray['principalLedger'])->sum('amount');
                    // $interestAmount = $voucherLoanInfo->where('creditAcc', $singleArray['interestLedger'])->sum('amount');
                    // $collection = $principalAmount + $interestAmount;
                    // $collectionRound = round($collection);
                    // $diff = number_format($collectionRound - $collection, 2);
                    //
                    // if ($diff != 0) {
                    //     $showMultiple[] = array(
                    //         'id'                => $vId,
                    //         'diff'              => $diff,
                    //         'collection'        => $collection,
                    //         'collectionRound'   => $collectionRound,
                    //         'principalAmount'   => $principalAmount,
                    //         'interestAmount'    => $interestAmount,
                    //         'info'              => $voucherLoanInfo,
                    //     );
                    //
                    // }

                } elseif ($voucherLoanInfo->count() < 2) {
                    // $showSingle[] = array(
                    //     'id'    => $vId,
                    //     'info'  => $voucherLoanInfo,
                    // );
                } else {
                    $principalAmount = $voucherLoanInfo->where('creditAcc', $singleArray['principalLedger'])->first()->amount;
                    $interestAmount = $voucherLoanInfo->where('creditAcc', $singleArray['interestLedger'])->first()->amount;
                    $collection = $principalAmount + $interestAmount;
                    $collectionRound = round($collection);

                    if ($collection != $collectionRound) {
                        $idToUpdate = $voucherLoanInfo->where('creditAcc', $singleArray['interestLedger'])->first()->id;
                        $diff = (double)number_format($collectionRound - $collection, 2);
                        $updatedInterest = $interestAmount + $diff;
                        $update = array(
                            // 'id'                    => $idToUpdate,
                            // 'voucherId'             => $vId,
                            // 'prePrincipalAmount'    => $principalAmount,
                            // 'preInterestAmount'     => $interestAmount,
                            'amount' => $updatedInterest,
                            // 'principalLedger'       => $singleArray['principalLedger'],
                            // 'interestLedger'        => $singleArray['interestLedger'],
                        );
                        // dd($update);
                        // update voucher details table
                        DB::table('acc_voucher_details')->where('id', $idToUpdate)->update($update);
                    }
                }
            }
        } // loop close
        // dd($update);
        // echo "<br>";
        // echo "<br>";
        // echo "<br>";
        // echo "<br>";
        // echo "<br>";
        // echo "<br>";
        // echo "<pre>";
        // print_r($showSingle);
        // echo "</pre>";

    }

    public static function generateFiscalYear($companyId) {

        $currentDate = Carbon::now();
        $company = \App\gnr\GnrCompany::find($companyId);

        if ($company->fy_type == 'january') {
            $fyStartDate = $currentDate->copy()->startOfYear()->format('Y-m-d');
            $fyEndDate = $currentDate->copy()->endOfYear()->format('Y-m-d');
            $fyName = $currentDate->copy()->format('Y');
        } elseif ($company->fy_type == 'july') {
            $currentMonth = $currentDate->month;
            if ($currentMonth < 7) {
                $fyStartDate = $currentDate->copy()->subYear()->format('Y-07-01');
            } elseif ($currentMonth >= 7) {
                $fyStartDate = $currentDate->copy()->format('Y-07-01');
            }

            $fyEndDate = Carbon::parse($fyStartDate)->addYear()->format('Y-06-30');
            $fyName = Carbon::parse($fyStartDate)->format('Y') . ' - ' . Carbon::parse($fyEndDate)->format('Y');
        }

        $fyData['name'] = $fyName;
        $fyData['companyId'] = $company->id;
        $fyData['fyStartDate'] = $fyStartDate;
        $fyData['fyEndDate'] = $fyEndDate;
        $fyData['createdDate'] = $currentDate;
        $fiscalYear = FiscalYear::create($fyData);

    }

    public static function generateNextFiscalYear($companyId) {

        $currentDate = Carbon::now();
        $company = \App\gnr\GnrCompany::find($companyId);

        if ($company->fy_type == 'january') {
            $fyStartDate = $currentDate->copy()->addYear()->startOfYear()->format('Y-m-d');
            $fyEndDate = $currentDate->copy()->addYear()->endOfYear()->format('Y-m-d');
            $fyName = $currentDate->copy()->addYear()->format('Y');
        } elseif ($company->fy_type == 'july') {
            $currentMonth = $currentDate->month;
            if ($currentMonth < 7) {
                $fyStartDate = $currentDate->copy()->format('Y-07-01');
            } elseif ($currentMonth >= 7) {
                $fyStartDate = $currentDate->copy()->addYear()->format('Y-07-01');
            }

            $fyEndDate = Carbon::parse($fyStartDate)->addYear()->format('Y-06-30');
            $fyName = Carbon::parse($fyStartDate)->format('Y') . ' - ' . Carbon::parse($fyEndDate)->format('Y');
        }

        $fyData['name'] = $fyName;
        $fyData['companyId'] = $company->id;
        $fyData['fyStartDate'] = $fyStartDate;
        $fyData['fyEndDate'] = $fyEndDate;
        $fyData['createdDate'] = $currentDate;
        $fiscalYear = FiscalYear::create($fyData);

    }

}
