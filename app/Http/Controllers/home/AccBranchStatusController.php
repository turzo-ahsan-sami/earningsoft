<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;
use App\accounting\process\AccMonthEnd;
use Session;
use DB;
//use Auth;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Service\Service;

class AccBranchStatusController extends Controller {

    public static function accAddBranchStatus($branchId){

        // variables
        $sumPreviousMonthBalance = 0;
        $sumCurrentMonthBalance  = 0;
        $sumPreviousMonthSurplusBalance = 0;
        $sumCurrentMonthSurplusBalance  = 0;
        $sumCurrentYearSurplusBalance   = 0;
        $sumCumulativeSurplusBalance    = 0;
        $cashAndBankLedgerAccountTypeIdArray = array(4,5);
        $incomeAndExpensesLedgerAccountTypeIdArray = array(12,13);

        $service = new Service;

        // branch info collection
        $branchInfo = DB::table('gnr_branch')->where('id', $branchId)->select('aisStartDate')->first();
        // day end collection
        $branchDayEnd = DB::table('acc_day_end')->where('branchIdFk', $branchId)->where('isDayEnd', 0)->where('status', 1)->max('date');
        // dd($branchDayEnd);
        // branch date
        if ($branchDayEnd){
            $branchDate = $branchDayEnd;
        }
        else {
            $branchDate = $branchInfo->aisStartDate;
        }
        // dd($branchDate);

        // dates collection
        //First & Last Day of This Month
		$firstDayofThisMonth = Carbon::parse($branchDate)->startOfMonth()->format('Y-m-d');
		$lastDayofThisMonth = $branchDate;
        // this year start date
		$firstDayofThisFiYr =DB::table('gnr_fiscal_year')->where('fyStartDate','<=', $lastDayofThisMonth)->where('fyEndDate','>=', $lastDayofThisMonth)->value('fyStartDate');
		//First & Last Day of Last Month
		$firstDayofLastMonth = Carbon::parse($branchDate)->subMonth()->startOfMonth()->format('Y-m-d');
		$lastDayofLastMonth = Carbon::parse($branchDate)->subMonth()->endOfMonth()->format('Y-m-d');
		//Get Previous Fiscal Year
		$preYearDate = Carbon::parse($firstDayofThisFiYr)->subDay()->format('Y-m-d');
		$preFiYrId = DB::table('gnr_fiscal_year')->where('fyStartDate','<=', $preYearDate)->where('fyEndDate','>=', $preYearDate)->first();
        //voucher start date
        $voucherStartDate = Carbon::parse($lastDayofLastMonth)->addDay()->format('Y-m-d');
        // dd($lastDayofThisMonth);

        // $lastMonthEndDate = AccMonthEnd::active()->where('branchIdFk',$branchId)->where('isMonthEnd',1)->max('date');
        // $lastYearEndDate = DB::table('acc_year_end')->where('branchIdFk',$branchId)->max('date');
        // dd($lastYearEndDate);

        // if($lastMonthEndDate){
        //     $lastMonthEndDate = $lastMonthEndDate;
        // }else {
        //     $lastMonthEndDate = $branchDate;
        // }
        // if ($lastYearEndDate) {
        //     $lastYearEndDate = $lastYearEndDate;
        // } else {
        //     $lastYearEndDate = $branchDate;
        // }

        // cash data calculation
        $companyId = DB::table('gnr_branch')->where('id', $branchId)->first()->companyId;

        if($branchId == 1){
            $projectIdArr = DB::table('gnr_project')->pluck('id')->toArray();
        } else{
            $projectIdArr = DB::table('gnr_branch')->where('id', $branchId)->pluck('projectId')->toArray();
        }
        // dd($projectIdArr);

        // opening data collection
        $openingBalanceInfo = DB::table('acc_opening_balance')
                            ->where('branchId', $branchId)
                            ->where('openingDate', $preFiYrId->fyEndDate)
                            ->select('ledgerId', 'projectId', 'projectTypeId', 'debitAmount', 'creditAmount')
                            ->get();

        $monthEndsInfo = DB::table('acc_month_end_balance')
                        ->where('branchId', $branchId)
                        ->where('monthEndDate', '>=', $firstDayofThisFiYr)
                        ->where('monthEndDate', '<=', $lastDayofLastMonth)
                        ->select('monthEndDate', 'ledgerId', 'projectId', 'projectTypeId', 'debitAmount', 'creditAmount')
                        ->get();

        // calculate cash and bank
        foreach ($cashAndBankLedgerAccountTypeIdArray as $key => $ledgerType) {

            foreach($projectIdArr as $projectId){
                // dd($projectIdArr);
                $ledgers = $service->getLedgerHeaderInfo($projectId, $branchId, $companyId)
                            ->where('accountTypeId', $ledgerType)
                            ->where('isGroupHead', 0)
                            ->pluck('id')->toArray();
                // dd($ledgers);
                $projectTypeIdArr = DB::table('gnr_project_type')
                                    ->where('projectId', $projectId)
                                    ->pluck('id')->toArray();
                                    // dd($ledgers);
                foreach($projectTypeIdArr as $projectTypeId){
                    // dd($projectTypeIdArr);
                    $currentMonthVoucherInfos = $service->getVoucherInfo($projectTypeId, $projectId, $branchId, $companyId, $voucherStartDate, $lastDayofThisMonth);
                    // dd($currentMonthVoucherInfos);
                    // ledgerwise calculation
                    foreach ($ledgers as $key => $ledger) {

                        $openingBalanceLedgerInfo = $openingBalanceInfo
                                                    ->where('ledgerId', $ledger)
                                                    ->where('projectId', $projectId)
                                                    ->where('projectTypeId', $projectTypeId);
                                                // dd($openingBalanceInfo);

                        $openingDebitAmount = $openingBalanceLedgerInfo->sum('debitAmount');
                        $openingCreditAmount = $openingBalanceLedgerInfo->sum('creditAmount');
                        // dd($openingDebitAmount);

                        $monthEndsLedgerInfo = $monthEndsInfo
                                                ->where('ledgerId', $ledger)
                                                ->where('projectId', $projectId)
                                                ->where('projectTypeId', $projectTypeId);

                        //previous month
                        $previousMonthDebitAmount = $openingDebitAmount + $monthEndsLedgerInfo->sum('debitAmount');
                        $previousMonthCreditAmount = $openingCreditAmount + $monthEndsLedgerInfo->sum('creditAmount');
                        //current month
                        $currentMonthDebitAmount = $previousMonthDebitAmount + $currentMonthVoucherInfos->where('debitAcc', $ledger)->sum('amount');
                        $currentMonthCreditAmount = $previousMonthCreditAmount + $currentMonthVoucherInfos->where('creditAcc', $ledger)->sum('amount');
                        // dd($currentMonthDebitAmount);
                        //balance
                        $accountType = $service->checkLedgerAccountType($ledger);
                        // dd($accountType);

                        if ($accountType == true) {
                            $currentMonthBalance = $currentMonthDebitAmount - $currentMonthCreditAmount;
                            $previousMonthBalance = $previousMonthDebitAmount - $previousMonthCreditAmount;
                        } elseif ($accountType == false) {
                            $currentMonthBalance = $currentMonthCreditAmount - $currentMonthDebitAmount;
                            $previousMonthBalance = $previousMonthCreditAmount - $previousMonthDebitAmount;
                        }

                        $sumCurrentMonthBalance += $currentMonthBalance;
                        $sumPreviousMonthBalance += $previousMonthBalance;

                    } // foreach loop ledger
                }   // foreach loop projectTypeId
            }   // foreach loop projectId

            $cashAndBank[] = array(
                'ledgerType'            => $ledgerType,
                'previousMonthBalance'  => $sumPreviousMonthBalance,
                'currentMonthBalance'   => $sumCurrentMonthBalance,
            );
            // dd($cashAndBank);

            $sumCurrentMonthBalance  = 0;
            $sumPreviousMonthBalance = 0;

        }
        // dd($cashAndBank);

        // calculate income and expenses
        foreach ($incomeAndExpensesLedgerAccountTypeIdArray as $key => $ledgerType) {

            foreach($projectIdArr as $projectId){
                // dd($projectIdArr);
                $ledgers = $service->getLedgerHeaderInfo($projectId, $branchId, $companyId)
                            ->where('accountTypeId', $ledgerType)
                            ->where('isGroupHead', 0)
                            ->pluck('id')->toArray();
                // dd($ledgers);
                $projectTypeIdArr = DB::table('gnr_project_type')
                                    ->where('projectId', $projectId)
                                    ->pluck('id')->toArray();
                                    // dd($ledgers);
                foreach($projectTypeIdArr as $projectTypeId){
                    // dd($projectTypeIdArr);
                    $currentMonthSurplusVoucherInfos = $service->getVoucherInfo($projectTypeId, $projectId, $branchId, $companyId, $voucherStartDate, $lastDayofThisMonth);

                    // cash calculation
                    foreach ($ledgers as $key => $ledger) {

                        $openingSurplusBalanceLedgerInfo = $openingBalanceInfo
                                                        ->where('ledgerId', $ledger)
                                                        ->where('projectId', $projectId)
                                                        ->where('projectTypeId', $projectTypeId);
                                                        // dd($openingBalanceInfo);

                        $openingSurplusDebitAmount = $openingSurplusBalanceLedgerInfo->sum('debitAmount');
                        $openingSurplusCreditAmount = $openingSurplusBalanceLedgerInfo->sum('creditAmount');
                        $monthEndsSurplusLedgerInfo = $monthEndsInfo
                                                    ->where('ledgerId', $ledger)
                                                    ->where('projectId', $projectId)
                                                    ->where('projectTypeId', $projectTypeId);
                                                    // dd($monthEndsSurplusLedgerInfo);

                        //previous month
                        $previousMonthSurplusDebitAmount = $monthEndsSurplusLedgerInfo->where('monthEndDate', $lastDayofLastMonth)->sum('debitAmount');
                        $previousMonthSurplusCreditAmount = $monthEndsSurplusLedgerInfo->where('monthEndDate', $lastDayofLastMonth)->sum('creditAmount');
                        //current month
                        $currentMonthSurplusDebitAmount = $currentMonthSurplusVoucherInfos->where('debitAcc', $ledger)->sum('amount');
                        $currentMonthSurplusCreditAmount = $currentMonthSurplusVoucherInfos->where('creditAcc', $ledger)->sum('amount');
                        // current year
                        $currentYearSurplusDebitAmount = $monthEndsSurplusLedgerInfo->sum('debitAmount') + $currentMonthSurplusDebitAmount;
                        $currentYearSurplusCreditAmount = $monthEndsSurplusLedgerInfo->sum('creditAmount') + $currentMonthSurplusCreditAmount;
                        // cumulative
                        $cumulativeSurplusDebitAmount = $openingSurplusDebitAmount + $currentYearSurplusDebitAmount;
                        $cumulativeSurplusCreditAmount = $openingSurplusCreditAmount + $currentYearSurplusCreditAmount;

                        // balance
                        $accountType = $service->checkLedgerAccountType($ledger);

                        if ($accountType == true) {
                            $currentMonthSurplusBalance     = $currentMonthSurplusDebitAmount - $currentMonthSurplusCreditAmount;
                            $previousMonthSurplusBalance    = $previousMonthSurplusDebitAmount - $previousMonthSurplusCreditAmount;
                            $currentYearSurplusBalance      = $currentYearSurplusDebitAmount - $currentYearSurplusCreditAmount;
                            $cumulativeSurplusBalance       = $cumulativeSurplusDebitAmount - $cumulativeSurplusCreditAmount;
                        }
                        elseif ($accountType == false) {
                            $currentMonthSurplusBalance     = $currentMonthSurplusCreditAmount - $currentMonthSurplusDebitAmount;
                            $previousMonthSurplusBalance    = $previousMonthSurplusCreditAmount - $previousMonthSurplusDebitAmount;
                            $currentYearSurplusBalance      = $currentYearSurplusCreditAmount - $currentYearSurplusDebitAmount;
                            $cumulativeSurplusBalance       = $cumulativeSurplusCreditAmount - $cumulativeSurplusDebitAmount;
                        }

                        // sum
                        $sumPreviousMonthSurplusBalance += $previousMonthSurplusBalance;
                        $sumCurrentMonthSurplusBalance  += $currentMonthSurplusBalance;
                        $sumCurrentYearSurplusBalance   += $currentYearSurplusBalance;
                        $sumCumulativeSurplusBalance    += $cumulativeSurplusBalance;


                    } // foreach loop ledger
                }   // foreach loop projectTypeId
            }   // foreach loop projectId

            $incomeAndExpenses[] = array(
                'ledgerType'            => $ledgerType,
                'currentMonthBalance'   => $sumCurrentMonthSurplusBalance,
                'previousMonthBalance'  => $sumPreviousMonthSurplusBalance,
                'currentYearBalance'    => $sumCurrentYearSurplusBalance,
                'cumulativeBalance'     => $sumCumulativeSurplusBalance,
            );

            $sumPreviousMonthSurplusBalance = 0;
            $sumCurrentMonthSurplusBalance  = 0;
            $sumCurrentYearSurplusBalance   = 0;
            $sumCumulativeSurplusBalance    = 0;

        }
        // dd($incomeAndExpenses);

        // current month
        $currentMonthIncome    = $incomeAndExpenses[0]['currentMonthBalance'];
        $currentMonthExpenses  = $incomeAndExpenses[1]['currentMonthBalance'];

        // previous month
        $previousMonthIncome   = $incomeAndExpenses[0]['previousMonthBalance'];
        $previousMonthExpenses = $incomeAndExpenses[1]['previousMonthBalance'];

        // current year
        $currentYearIncome     = $incomeAndExpenses[0]['currentYearBalance'];
        $currentYearExpenses   = $incomeAndExpenses[1]['currentYearBalance'];

        // cumulative
        $cumulativeIncome      = $incomeAndExpenses[0]['cumulativeBalance'];
        $cumulativeExpenses    = $incomeAndExpenses[1]['cumulativeBalance'];

        $data = array(
                'branchIdFk'                        => $branchId,   //ok
                'branchDate'                        => $branchDate,     //ok
                'previousMonthCash'                 => round((float) $cashAndBank[0]['previousMonthBalance'], 2),     //ok
                'currentMonthCash'                  => round((float) $cashAndBank[0]['currentMonthBalance'], 2),        //ok
                'previousMonthBank'                 => round((float) $cashAndBank[1]['previousMonthBalance'], 2),  //ok
                'currentMonthBank'                  => round((float) $cashAndBank[1]['currentMonthBalance'], 2),      //ok
                'currentCashAndBank'                => round((float) $cashAndBank[0]['currentMonthBalance'] + $cashAndBank[1]['currentMonthBalance'], 2),   //ok
                'currentYearIncome'                 => round((float) $currentYearIncome, 2),      //ok
                'currentYearExpenses'               => round((float) $currentYearExpenses, 2),      //ok
                'currentMonthSurplus'               => round((float) $currentMonthIncome - $currentMonthExpenses, 2),   //ok
                'previousMonthSurplus'              => round((float) $previousMonthIncome - $previousMonthExpenses, 2),   //ok
                'currentYearSurplus'                => round((float) $currentYearIncome - $currentYearExpenses, 2),   //ok
                'cumulativeSurplus'                 => round((float) $cumulativeIncome - $cumulativeExpenses, 2),   //ok
                'createdDate'                       => Carbon::now()        //ok
        );
        // dd($data);

        $branchStatusInfo = DB::table('acc_dashboard_report')->where('branchIdFK', $branchId)->first();
        // check if today's value exists or not
        if($branchStatusInfo){
            // if exists then update
            DB::table('acc_dashboard_report')->where('id', $branchStatusInfo->id)->update($data);
        } else {
            // if not exist then insert new data
            DB::table('acc_dashboard_report')->insert($data);
        }

    }

    public static function accAddAllBranchStatus(){

        $allBranches = DB::table('gnr_branch')->select('id')->get();

        // running loop for all branches
        foreach($allBranches as $item){
            self::accAddBranchStatus($item->id);
        }

    }

    public static function getLedgerHeaderInfo($projectId, $branchId, $companyId){
        $allLedgers = DB::table('acc_account_ledger')
                      ->where('isGroupHead', 0)
                      ->select('id','projectBranchId')
                      ->get();

        $matchedId=array();

        foreach ($allLedgers as $singleLedger) {
            $splitArray=str_replace(array('[', ']', '"', ''), '',  $singleLedger->projectBranchId);

            $splitArrayFirstValue = explode(",", $splitArray);
            $splitArraySecondValue = explode(":", $splitArrayFirstValue[0]);

            $array_length=substr_count($splitArray, ",");
            $arrayProjects=array();
            $temp=null;
            for($i=0; $i<$array_length+1; $i++){

                $splitArrayFirstValue = explode(",", $splitArray);

                $splitArraySecondValue = explode(":", $splitArrayFirstValue[$i]);
                $firstIndexValue=(int)$splitArraySecondValue[0];
                $secondIndexValue=(int)$splitArraySecondValue[1];

                if($firstIndexValue==0){
                    if($secondIndexValue==0){
                        array_push($matchedId, $singleLedger->id);
                    }
                }else{
                    // dd($projectId);
                    if($firstIndexValue==$projectId){
                        if($secondIndexValue==0){
                            array_push($matchedId, $singleLedger->id);
                        }elseif($secondIndexValue==$branchId ){
                            array_push($matchedId, $singleLedger->id);
                        }
                    }
                }
            }   //for
        }       //foreach

        $ledgers = DB::table('acc_account_ledger')
            ->whereIn('acc_account_ledger.id', $matchedId)
            ->where('companyIdFk', $companyId)
            ->where('isGroupHead', 0)
            ->whereIn('accountTypeId', array(4,5,12,13))
            ->select('id', 'accountTypeId')
            ->get();
            // dd($ledgers);

        return $ledgers;
    }

    public static function getVoucherInfo($projectTypeId, $projectId, $branchId, $companyId, $date, $startDate){
        $voucherInfo = DB::table('acc_voucher')
                        ->join('acc_voucher_details','acc_voucher_details.voucherId', '=', 'acc_voucher.id')
                        ->where('companyId', $companyId)
                        ->where('projectId', $projectId)
                        ->where('branchId', $branchId)
                        ->where('projectTypeId', $projectTypeId)
                        ->where('voucherDate', '<=', $date)
                        ->where('voucherDate', '>=', $startDate)
                        ->select(
                            'acc_voucher_details.debitAcc',
                            'acc_voucher_details.creditAcc',
                            'acc_voucher_details.amount',
                            'acc_voucher.projectId',
                            'acc_voucher.projectTypeId'
                        )
                        ->get();

        return $voucherInfo;
    }
}
