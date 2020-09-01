<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;
use Session;
use DB;
use Carbon\Carbon;

class MfnBranchStatusController extends Controller {

    public static function mfnAddBranchStatus($branchId){

        // taking variables
        $sumTodayDue                     = 0;
        $sumLastMonthDue                 = 0;
        $sumTotalDue                     = 0;
        $sumLoanOverdue                  = 0;
        $sumCurrentDue                   = 0;
        $sumTodayRecovery                = 0;
        $sumTotalRecovery                = 0;
        $sumTotalOutstandning            = 0;
        $totalCumulativeDisbursement     = 0;
        // $undefinedLoanIdArr              = [];

        // branch info collection
        $branchInfo = DB::table('gnr_branch')
                    ->where('id', $branchId)
                    ->select('softwareStartDate')
                    ->first();

        // day end collection
        $branchDayEnd = DB::table('mfn_day_end')
                        ->where('isLocked', 0)
                        ->where('branchIdFk', $branchId)
                        ->select('date')
                        ->first();

        // branch date code
        if ($branchDayEnd){
            $branchDate = $branchDayEnd->date;
        }
        else {
            $branchDate = $branchInfo->softwareStartDate;
        }
        // dd($branchDate);
        $branchDateMonth = Carbon::parse($branchDate)->format('Y-m');
        $branchPreviousMonth = Carbon::parse($branchDateMonth)->subMonth()->format('Y-m');
        $lastMonthStartDate = Carbon::parse($branchPreviousMonth)->startOfMonth()->format('Y-m-d');
        $lastMonthEndDate = Carbon::parse($branchPreviousMonth)->endOfMonth()->format('Y-m-d');
        // dd($lastMonthEndDate);

        // member collection
        $members = DB::table('mfn_member_information')
                    ->where('softDel', 0)
                    ->where('branchId', $branchId)
                    ->where('admissionDate', '<=', $branchDate)
                    ->select('id', 'status', 'gender')
                    ->get();
        $memberCount = $members->count('id');
        $totalActiveMember = $members->where('status', 1)->count('id');
        $totalActiveMaleMember = $members->where('status', 1)->where('gender', 1)->count('id');
        $totalActiveFemaleMember = $members->where('status', 1)->where('gender', 2)->count('id');
        $totalInctiveMember = $members->where('status', 0)->count('id');
        $totalInctiveMaleMember = $members->where('status', 0)->where('gender', 1)->count('id');
        $totalInctiveFemaleMember = $members->where('status', 0)->where('gender', 2)->count('id');
        $totalClosedMember = DB::table('mfn_member_closing')
                            ->where('softdel', 0)->where('status', 1)
                            ->where('branchIdFk', $branchId)
                            ->where('closingDate', '<=', $branchDate)
                            ->count('id');
        // dd($totalClosedMember);

        // loanee count
        $loans = DB::table('mfn_loan')
                ->where('branchIdFK', $branchId)->where('status', 1)->where('softDel', 0)
                ->where('disbursementDate', '<=', $branchDate)
                ->select('id', 'memberIdFk', 'totalRepayAmount','disbursementDate', 'loanAmount')
                ->get();
                // dd($loans);
        // $totalLoanee = $loans->unique('memberIdFk')->count('memberIdFk');
        $totalMaleLoanee = $loans->whereIn('memberIdFk', $members->where('status', 1)
                            ->where('gender', 1)->pluck('id')->toArray())
                            ->unique('memberIdFk')->count();
        $totalFemaleLoanee = $loans->whereIn('memberIdFk', $members->where('status', 1)
                            ->where('gender', 2)->pluck('id')->toArray())
                            ->unique('memberIdFk')->count();

        // loans calculation by branch
        $loanOpeningBalance = DB::table('mfn_opening_balance_loan')->where('status', 1)->where('softDel', 0)->get();
        $loanMonthEndsInfo = DB::table('mfn_month_end_process_loans')->where('branchIdFk', $branchId)->where('date', '<=', $branchDate)->get();
        $loanMonthEndDate = $loanMonthEndsInfo->max('date');
        $loanPreviousMonthEndDate = $loanMonthEndsInfo->where('date', '<', $loanMonthEndDate)->max('date');
        $loanMonthEndsInfo = $loanMonthEndsInfo->where('date', $loanMonthEndDate);
        $loanPreviousMonthEndsInfo = $loanMonthEndsInfo->where('date', $loanPreviousMonthEndDate);
        // dd($loanPreviousMonthEndDate);
        $loanScedules = DB::table('mfn_loan_schedule')
                        ->whereIn('loanIdFk', $loans->pluck('id')->toArray())
                        ->where('status', 1)->where('softDel', 0)
                        // ->where('scheduleDate', '<=', $branchDate)
                        ->select(
                            'scheduleDate',
                            'isCompleted',
                            'isPartiallyPaid',
                            'installmentAmount',
                            'principalAmount',
                            'partiallyPaidAmount',
                            'loanIdFk'
                        )
                        ->get();
                        // dd($loanCollection);
        $loanCollection = DB::table('mfn_loan_collection')
                        ->whereIn('loanIdFk', $loans->pluck('id')->toArray())
                        ->where('status', 1)->where('softDel', 0)
                        ->where('collectionDate', '<=', $branchDate)
                        ->select(
                            'collectionDate',
                            'amount',
                            'principalAmount',
                            'loanIdFk'
                        )
                        ->get();
                        // dd($branchDate);
        // $loanRebatesAmount = DB::table('mfn_loan_rebates')->where('branchIdFk', $branchId)->sum('amount');
        // $loanWaiversAmount = DB::table('mfn_loan_waivers')->where('branchIdFk', $branchId)->sum('principalAmount');
        // $loanWriteOffAmount = DB::table('mfn_loan_write_off')->where('branchIdFk', $branchId)->sum('principalAmount');
        // dd($loanWaiversAmount);

        //  today disbursement calculation
        $todayDisbursement = $loans->where('disbursementDate', $branchDate)->sum('loanAmount');

        foreach ($loans as $key => $loan){

            $loanOpeningBalanceById = $loanOpeningBalance->where('loanIdFk', $loan->id);
            // $openingBalanceDate = $loanOpeningBalanceById->max('date');
            $loanSchedulesById = $loanScedules->where('loanIdFk', $loan->id);
            $loanCollectionById = $loanCollection->where('loanIdFk', $loan->id);
            // $loanScheduleDates = $loanSchedulesById->sortByDesc('scheduleDate');
            $loanScheduleToday = $loanSchedulesById->where('scheduleDate', $branchDate);
            $loanCollectionToday = $loanCollectionById->where('collectionDate', $branchDate);

            // today due
            $todayDue = $loanScheduleToday->sum('principalAmount') - $loanCollectionToday->sum('principalAmount');
            if ($todayDue < 0) {
                $todayDue = 0;
            }
            // dd($todayDue);

            // current month due and overdue calculation
            $lastInstallmentDateInfo = $loanSchedulesById->sortByDesc('scheduleDate')->first();
            $loanScheduleCurrentMonth = $loanSchedulesById->where('scheduleDate', '>', $loanMonthEndDate)->where('scheduleDate', '<=', $branchDate);
            $loanCollectionCurrentMonth = $loanCollectionById->where('collectionDate', '>', $loanMonthEndDate)->where('collectionDate', '<=', $branchDate);
            $loanCollectionCurrentOB = $loanOpeningBalanceById->where('date', '<=', $branchDate);
            // dd($lastInstallmentDateInfo);
            // current due & overdue
            if (!$lastInstallmentDateInfo) {
                $currentMonthDue = 0;
                $loanOverdue = 0;
            }
            else {
                if($lastInstallmentDateInfo->scheduleDate <= $branchDate && $lastInstallmentDateInfo->isCompleted == 0) {

                    $loanOverdue = $loanSchedulesById->sum('principalAmount') - $loanCollectionById->where('collectionDate', '<=', $branchDate)->sum('principalAmount') - $loanCollectionCurrentOB->sum('principalAmountOB');
                    $currentMonthDue = 0;

                }
                elseif ($lastInstallmentDateInfo->scheduleDate > $branchDate) {
                    // current due
                    if ($loanMonthEndDate == $branchDate) {
                        $currentMonthDue = 0;
                    }
                    else {
                         $currentMonthDue = $loanScheduleCurrentMonth->sum('principalAmount') - $loanCollectionCurrentMonth->sum('principalAmount');
                    }
                    //overdue
                    $loanOverdue = 0;

                    if ($currentMonthDue < 0) {
                        $currentMonthDue = 0;
                    }

                }
                else {
                    $currentMonthDue =0;
                    $loanOverdue = 0;
                }
            }
            // dd($currentMonthDue);

            // total outstanding calculation
            $completelyCollectedAmount = $loanCollectionById->sum('principalAmount');
            $opCollectedAmount = $loanOpeningBalanceById->sum('principalAmountOB');
            $totalRecovery = $completelyCollectedAmount + $opCollectedAmount;
            $totalRepayAmount = $loan->loanAmount;
            $totalOutstandning = $totalRepayAmount - $totalRecovery;
            // dd($totalOutstandning);

            // today Recovery calculation
            $todayRecovery = $loanCollectionToday->sum('principalAmount');
            // dd($todayRecovery);

            // total cumulative disbursement
            $singleDisbursement = $loan->loanAmount;

            // sum all info
            $sumTodayDue                     += $todayDue;
            $sumLastMonthDue                 += $lastMonthDue;
            // $sumTotalDue                     += $totalDue;
            $sumLoanOverdue                  += $loanOverdue;
            $sumCurrentDue                   += $currentDue;
            $sumTodayRecovery                += $todayRecovery;
            $sumTotalRecovery                += $totalRecovery;
            $sumTotalOutstandning            += $totalOutstandning;
            $totalCumulativeDisbursement     += $singleDisbursement;
            // dd($totalDisbursement);

            // $array[] = array(
            //     'loanId'=> $loan->id,
            //     'todayDue' => $todayDue,
            //     'lastMonthDue' => $lastMonthDue,
            //     'currentDue' => $currentDue,
            //     'overDue' => $loanOverdue,
            // );
        }
        // echo "<pre>";
        // print_r($array);
        // echo "</pre>";
        // dd($array);
        // last month due & current due
        if ($loanMonthEndDate == $branchDate) {
            $lastMonthDue = $loanMonthEndsInfo->sum('openingDueAmount');
            $currentDue = $loanMonthEndsInfo->sum('closingDueAmount');
        }
        else {
             $lastMonthDue = $loanMonthEndsInfo->sum('closingDueAmount');
             $currentDue = $lastMonthDue + $sumCurrentMonthDue;
        }
        // dd($lastMonthDue);

        // savings calculation start

        // today deposit calculation
        $deposits = DB::table('mfn_savings_deposit')
                    ->where('branchIdFk', $branchId)
                    ->where('depositDate','<=', $branchDate)
                    ->select('depositDate', 'amount')
                    ->get();

        $todayDeposits = $deposits->where('depositDate', $branchDate)->sum('amount');
        $totalDeposits = $deposits->sum('amount');

        // today refund calculation
        $withdraws = DB::table('mfn_savings_withdraw')
                    ->where('branchIdFk', $branchId)
                    ->where('withdrawDate','<=', $branchDate)
                    ->select('withdrawDate', 'amount')
                    ->get();

        $todayRefund = $withdraws->where('withdrawDate', $branchDate)->sum('amount');
        $totalRefunds = $withdraws->sum('amount');

        // total savings balance calculation
        $savings = DB::table('mfn_savings_account')
                    ->where('branchIdFK', $branchId)
                    ->where('accountOpeningDate', '<=', $branchDate)
                    ->select('id')
                    ->get();

        $withdraws = DB::table('mfn_savings_withdraw')
                    ->where('branchIdFk', $branchId)
                    ->where('withdrawDate','<=', $branchDate)
                    ->select('withdrawDate', 'amount')
                    ->get();

        $openingBalance = DB::table('mfn_opening_savings_account_info')
                    ->whereIn('savingsAccIdFk', $savings->pluck('id')->toArray())
                    ->sum('openingBalance');

        $totalSavingsBalance = $deposits->sum('amount') + $openingBalance - $withdraws->sum('amount');
        // dd($totalSavingsBalance);

        $data = array(
                'totalMember'                       => $memberCount,
                'totalActiveMaleMember'             => $totalActiveMaleMember,
                'totalActiveFemaleMember'           => $totalActiveFemaleMember,
                'totalInctiveMaleMember'            => $totalInctiveMaleMember,
                'totalInctiveFemaleMember'          => $totalInctiveFemaleMember,
                'totalClosedMember'                 => $totalClosedMember,
                'totalMaleLoanee'                   => $totalMaleLoanee,
                'totalFemaleLoanee'                 => $totalFemaleLoanee,
                'branchIdFk'                        => $branchId,   //ok
                'dayEndDate'                        => $branchDate,     //ok
                'todayDueAmount'                    => round((float) $sumTodayDue, 2),     //ok
                'currentDueAmount'                  => round((float) $sumCurrentDue, 2),        //ok
                'totalOverdue'                      => round((float) $sumLoanOverdue, 2),      //ok
                'lastMonthDueAmount'                => round((float) $sumLastMonthDue, 2),      //ok
                'totalDueAmount'                    => round((float) $sumCurrentDue + $sumLoanOverdue, 2),      //ok
                'totalOutstanding'                  => round((float) $sumTotalOutstandning, 2),   //ok
                'todayDeposit'                      => round((float) $todayDeposits, 2),    //ok
                'totalDeposits'                     => round((float) $totalDeposits, 2),    //ok
                'todayRefund'                       => round((float) $todayRefund, 2),  //ok
                'totalRefunds'                      => round((float) $totalRefunds, 2),  //ok
                'totalSavingBalance'                => round((float) $totalSavingsBalance, 2),  //ok
                'todayDisbursement'                 => round((float) $todayDisbursement, 2),    //ok
                'todayRecovery'                     => round((float) $sumTodayRecovery, 2),        //ok
                'totalRecovery'                     => round((float) $sumTotalRecovery, 2),        //ok
                'totalCumulativeDisbursement'       => round((float) $totalCumulativeDisbursement, 2),  //ok
        );
        // dd($data);

        $branchStatusInfo = DB::table('mfn_dashboard_report')->where('branchIdFK', $branchId)->first();
        // dd($branchStatusInfo);
        // check if today's value exists or not
        if($branchStatusInfo){
            // if exists then update
            DB::table('mfn_dashboard_report')->where('id', $branchStatusInfo->id)->update($data);
        } else {
            // if not exist then insert new data
            DB::table('mfn_dashboard_report')->insert($data);
        }

        // return $undefinedLoanIdArr;

    }

    public static function mfnAddAllBranchStatus(){

        $allBranches = DB::table('gnr_branch')->orderBy('branchCode')->select('id')->get();
        // dd($allBranches);

        // running loop for all branches
        foreach($allBranches as $item){
            //$this->mfnAddBranchStatus($item->id);
            self::mfnAddBranchStatus($item->id);
            // $array[] = self::mfnAddBranchStatus($item->id);
        }
        // self::mfnAddBranchStatus(88);

        self::mfnComponentWiseLoanInfo();
        // return $array;
    }

    public static function mfnComponentWiseLoanInfo(){

        // loans collection by product
        $loanProducts = DB::table('mfn_loans_product')
                        ->join('mfn_loans_product_category', 'mfn_loans_product_category.id', '=', 'mfn_loans_product.productCategoryId')
                        ->select('mfn_loans_product.id', 'mfn_loans_product_category.id as productId', 'mfn_loans_product_category.name')
                        ->get();
                        // dd($loanProducts);

        $loans = DB::table('mfn_loan')
                ->where('status', 1)->where('softDel', 0)
                ->select('id', 'productIdFk', 'branchIdFk', 'disbursementDate', 'loanAmount')
                ->get();

        // branch status collection
        $branchStatusInfos = DB::table('mfn_dashboard_report')->select('branchIdFk', 'dayEndDate')->get();

        // $totalDisbursement = 0;
        // $totalRecovery = 0;
        // $totalOutstanding = 0;

        foreach ($loanProducts as $key => $product) {
            $totalDisbursement = 0;
            $totalRecovery = 0;
            $totalOutstanding = 0;
            foreach ($branchStatusInfos as $key => $branch) {
                $disbursements = $loans->where('branchIdFk', $branch->branchIdFk)
                                ->where('productIdFk', $product->id)
                                ->where('disbursementDate', '<=', $branch->dayEndDate)
                                ->sum('loanAmount');
                                // dd($disbursements);

                $totalDisbursement += $disbursements;
                $loansByProducts = $loans->where('productIdFk', $product->id)->where('branchIdFk', $branch->branchIdFk);
                // dd($loansByProducts);

                foreach ($loansByProducts as $key => $loan) {
                    // dd($loansByProducts);
                    $loanSchedules = DB::table('mfn_loan_schedule')
                                    ->where('loanIdFk', $loan->id)
                                    ->select(
                                        'loanIdFk',
                                        'scheduleDate',
                                        'isCompleted',
                                        'isPartiallyPaid',
                                        'installmentAmount',
                                        'partiallyPaidAmount'
                                    )
                                    ->get();

                    $loanScedulesInfos = $loanSchedules->where('scheduleDate', '<=', $branch->dayEndDate);
                    $fullyRecoveredLoans = $loanScedulesInfos->where('isCompleted', 1);
                    $partiallyRecoveredLoans = $loanScedulesInfos->where('isPartiallyPaid', 1);

                    $recovery = $fullyRecoveredLoans->sum('installmentAmount') + $partiallyRecoveredLoans->sum('partiallyPaidAmount');
                    // dd($recovery);

                    $repayAmount = $loanSchedules->sum('installmentAmount');

                    $outstanding = $repayAmount - $recovery;

                    $totalRecovery += $recovery;
                    $totalOutstanding += $outstanding;
                }
            }

            $disbursements = 0;
            $recovery = 0;
            $outstanding = 0;

            $data = array(
                'productId'           =>  $product->id,
                'componentId'         =>  $product->productId,
                'componentName'       =>  $product->name,
                'totalDisbursement'   =>  $totalDisbursement,
                'totalRecovery'       =>  $totalRecovery,
                'totalOutstanding'    =>  $totalOutstanding,
                'createdDate'         =>  Carbon::now()->format('Y-m-d')
            );

            // dd($data);
            $componentWiseLoansInfo = DB::table('mfn_componentwise_loans_info')
                                ->where('productId', $product->id)
                                ->where('componentId', $product->productId)
                                ->first();

            // check if today's value exists or not
            if($componentWiseLoansInfo){
                // if exists then update
                DB::table('mfn_componentwise_loans_info')->where('id', $componentWiseLoansInfo->id)->update($data);
            } else {
                // if not exist then insert new data
                DB::table('mfn_componentwise_loans_info')->insert($data);
            }
        }
        // echo '<pre>'; print_r($data); echo '</pre>';
    }
}
