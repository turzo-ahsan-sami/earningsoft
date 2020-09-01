<?php

namespace App\Http\Controllers\microfin\reports\regularNGeneralReports\fieldOfficerReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\microfin\MicroFin;

class FieldOfficerReportReportController extends Controller {

    public function index(){

        $branchList = MicroFin::getBranchList();

        // Year
        $yearsOption = MicroFin::getYearsOption();

        // Month
        $monthsOption = MicroFin::getMonthsOption();

        // Category
        $categoryList = MicroFin::getAllProductCategoryList();

        

        $data = array(
            'branchList'    => $branchList,
            'yearsOption'   => $yearsOption,
            'monthsOption'  => $monthsOption,
            'categoryList'  => $categoryList,
        );

        return view('microfin.reports.regularNGeneralReports.fieldOfficerReport.reportFilteringPart', $data);  
    }

    public function getReport(Request $req){

        // this variable 
        $startTime = microtime(true);

        $fieldOfficer = DB::table('hr_emp_general_info')
                            ->where('id',$req->filFieldOfficer)
                            ->select('emp_id','emp_name_english')
                            ->first();

        $savingsProducts = DB::table('mfn_saving_product')
                                ->select('id','shortName')
                                ->get();

        $dates = explode(',',$req->filWeek);

        $startDate = $dates[0];
        $endDate = $dates[1];

        $samities = DB::select("SELECT t1.id, t1.name,t1.code, IF(?<t2.effectiveDate,t2.`fieldOfficerId`,t1.fieldOfficerId) as fieldOfficerId FROM `mfn_samity` as t1
                LEFT JOIN mfn_samity_field_officer_change as t2 ON t1.id = t2.samityId", [$startDate]);
        $samities = collect($samities);

        $samities = $samities->where('fieldOfficerId',$req->filFieldOfficer);

        $savings = DB::table('mfn_savings_deposit')
                                ->where('softDel',0)
                                ->whereIn('samityIdFk',$samities->pluck('id'))
                                ->select('accountIdFk','samityIdFk','depositDate','productIdFk','amount')
                                ->get();

        //START OPENING BALANCE ARRAY=====

        $samityOpeningBalance = DB::table('mfn_opening_savings_account_info')
                          // ->whereIn('samityIdFk', $savings->unique('samityIdFk')->pluck('samityIdFk'))
                          ->join('mfn_savings_account', 'mfn_savings_account.id', '=', 
                            'mfn_opening_savings_account_info.savingsAccIdFk')
                          ->select('mfn_opening_savings_account_info.samityIdFk', 
                            'mfn_opening_savings_account_info.openingBalance', 
                            'mfn_savings_account.savingsProductIdFk')
                          ->get();
        //END OPENING BALANCE ARRAY=====      

        $weekEndsavingsInterest = DB::table('mfn_savings_interest')
                                    ->whereIn('samityIdFk',$samities->pluck('id'))
                                    ->where('effectiveDate','<=',$endDate)
                                    ->get();

        $beginingSavingsInterest = $weekEndsavingsInterest->where('effectiveDate','<',$startDate);

        $beginingSavings = $savings->where('depositDate','<',$startDate);
        $thisWeekSavings = $savings->where('depositDate','>=',$startDate)->where('depositDate','<=',$endDate);
        $weekEndSavings = $savings->where('depositDate','<=',$endDate);

        $withdraws = DB::table('mfn_savings_withdraw')
                                ->where('softDel',0)
                                ->whereIn('samityIdFk',$samities->pluck('id'))
                                ->where('withdrawDate','<',$startDate)
                                ->select('samityIdFk','withdrawDate','productIdFk','amount')
                                ->get();

        $beginingWithdraws = $withdraws->where('withdrawDate','<',$startDate);
        $thisWeekWithdraws = $withdraws->where('withdrawDate','>=',$startDate)->where('withdrawDate','<=',$endDate);
        $weekEndWithdraws = $withdraws->where('withdrawDate','<=',$endDate);

        $members = DB::select("SELECT t1.id, IF(t2.transferDate>?,t2.previousSamityIdFk,t1.`samityId`) as samityId FROM `mfn_member_information` as t1
            LEFT JOIN mfn_member_samity_transfer as t2 ON t1.id = t2.memberIdFk WHERE t1.admissionDate<=?", [$startDate,$startDate]);

        $members = collect($members);
        $members = $members->whereIn('samityId',$samities->pluck('id'));

        $writeOffs = DB::table('mfn_loan_write_off')
                        ->whereIn('samityIdFk',$samities->pluck('id'));
        $loans = DB::table('mfn_loan')
                    ->where('softDel',0)
                    ->whereIn('samityIdFk',$samities->pluck('id'));
                    
        if ($req->filLoanProduct!='' || $req->filLoanProduct!=null) {
            $writeOffs = $writeOffs->where('productIdFk',$req->filLoanProduct);
            $loans = $loans->where('productIdFk',$req->filLoanProduct);
        }
        else if($req->filProductCategory!='' || $req->filProductCategory!=null){
            $loanProductIds = DB::table('mfn_loans_product')->where('productCategoryId',$req->filProductCategory)->pluck('id')->toArray();
            $writeOffs = $writeOffs->whereIn('productIdFk',$loanProductIds);
            $loans = $loans->whereIn('productIdFk',$loanProductIds);
        }
        $writeOffs = $writeOffs->get();
        $loans = $loans->select('id','samityIdFk','disbursementDate','loanAmount','insuranceAmount','additionalFee')
                    ->get();

        $beginingWriteOffs = $writeOffs->where('date','<',$startDate);
        $thisWeekWriteOffs = $writeOffs->where('date','>=',$startDate)->where('date','<=',$endDate);
        $weekEndWriteOffs = $writeOffs->where('date','>',$endDate);

        //START OPENING LOAN BALANCE ARRAY=====

        $samityLoanOpeningBalance = DB::table('mfn_opening_balance_loan')
                          ->join('mfn_loan', 'mfn_loan.id', '=', 
                            'mfn_opening_balance_loan.loanIdFk')
                          ->where('mfn_opening_balance_loan.softDel', 0)
                          ->select('mfn_opening_balance_loan.paidLoanAmountOB', 
                            'mfn_opening_balance_loan.principalAmountOB', 
                            'mfn_opening_balance_loan.interestAmountOB', 
                            'mfn_loan.samityIdFk')
                          ->get();
        //END OPENING LOAN BALANCE ARRAY=====   

        $beginingLoans = $loans->where('disbursementDate','<',$startDate);
        $thisWeekLoans = $loans->where('disbursementDate','>=',$startDate)->where('disbursementDate','<=',$endDate);
        $weekEndLoans = $loans->where('disbursementDate','<=',$endDate);

        $paidLoans = DB::table('mfn_loan')
                    ->where('softDel',0)
                    ->whereIn('samityIdFk',$samities->pluck('id'))
                    ->where('isLoanCompleted',1)
                    ->select('samityIdFk','disbursementDate','loanAmount','loanCompletedDate')
                    ->get();

        $beginingPaidLoans = $paidLoans->where('loanCompletedDate','<',$startDate);
        $thisWeekPaidLoans = $paidLoans->where('loanCompletedDate','>=',$startDate)->where('loanCompletedDate','<=',$endDate);
        $weekEndPaidLoans = $paidLoans->where('loanCompletedDate','<=',$endDate);

        $beginigExpiredLoanIds = DB::select("SELECT loanIdFk FROM `mfn_loan_schedule` AS t1 WHERE ? > (SELECT MAX(`scheduleDate`) FROM mfn_loan_schedule WHERE `loanIdFk`=t1.`loanIdFk`) AND `loanIdFk` IN (SELECT id from mfn_loan WHERE isCompleted=0) GROUP BY `loanIdFk`", [$startDate]);
        $beginigExpiredLoanIds = collect($beginigExpiredLoanIds);
        $beginigExpiredLoanIds = $beginigExpiredLoanIds->pluck('loanIdFk')->toArray();

        $weekEndExpiredLoanIds = DB::select("SELECT loanIdFk FROM `mfn_loan_schedule` AS t1 WHERE ? > (SELECT MAX(`scheduleDate`) FROM mfn_loan_schedule WHERE `loanIdFk`=t1.`loanIdFk`) AND `loanIdFk` IN (SELECT id from mfn_loan WHERE isCompleted=0) GROUP BY `loanIdFk`", [$endDate]);
        $weekEndExpiredLoanIds = collect($weekEndExpiredLoanIds);
        $weekEndExpiredLoanIds = $weekEndExpiredLoanIds->pluck('loanIdFk')->toArray();

        $beginingExpiredLoans = $beginingLoans->whereIn('id',$beginigExpiredLoanIds);

        $weekEndExpiredLoans = $weekEndLoans->whereIn('id',$weekEndExpiredLoanIds);

        $beginingCurrentLoans = DB::table('mfn_loan')
                                ->where('softDel',0)
                                ->whereIn('samityIdFk',$samities->pluck('id'))
                                ->whereNotIn('id',$beginigExpiredLoanIds)
                                ->where('disbursementDate','<',$startDate)
                                ->select('id','samityIdFk','disbursementDate','loanAmount','loanCompletedDate')
                                ->get();

        $weekEndCurrentLoans = DB::table('mfn_loan')
                                ->where('softDel',0)
                                ->whereIn('samityIdFk',$samities->pluck('id'))
                                ->whereNotIn('id',$beginigExpiredLoanIds)
                                ->where('disbursementDate','<',$startDate)
                                ->select('id','samityIdFk','disbursementDate','loanAmount','loanCompletedDate')
                                ->get();

        if ($req->filServiceCharge==1) {
            $beginingCurentDue = DB::table('mfn_loan_schedule')
                                    ->where('softDel',0)
                                    ->whereIn('loanIdFk',$beginingCurrentLoans->pluck('id'))
                                    ->where('scheduleDate','<',$startDate)
                                    ->where('isCompleted',0)
                                    ->select(DB::raw('installmentAmount - partiallyPaidAmount as dueAmount'),'loanIdFk')
                                    ->get();

            $weekEndCurentDue = DB::table('mfn_loan_schedule')
                                    ->where('softDel',0)
                                    ->whereIn('loanIdFk',$weekEndCurrentLoans->pluck('id'))
                                    ->where('scheduleDate','<',$endDate)
                                    ->where('isCompleted',0)
                                    ->select(DB::raw('installmentAmount - partiallyPaidAmount as dueAmount'),'loanIdFk')
                                    ->get();


            $beginingExpiredDue = DB::table('mfn_loan_schedule')
                                    ->where('softDel',0)
                                    ->whereIn('loanIdFk',$beginigExpiredLoanIds)
                                    ->where('scheduleDate','<',$startDate)
                                    ->where('isCompleted',0)
                                    ->select(DB::raw('installmentAmount - partiallyPaidAmount as expiredDueAmount'),'loanIdFk')
                                    ->get();

            $weekEndExpiredDue = DB::table('mfn_loan_schedule')
                                    ->where('softDel',0)
                                    ->whereIn('loanIdFk',$weekEndExpiredLoanIds)
                                    ->where('scheduleDate','<',$endDate)
                                    ->where('isCompleted',0)
                                    ->select(DB::raw('installmentAmount - partiallyPaidAmount as expiredDueAmount'),'loanIdFk')
                                    ->get();

            $beginingOutStanding = DB::table('mfn_loan_schedule')
                                    ->where('softDel',0)
                                    ->whereIn('loanIdFk',$loans->pluck('id'))
                                    ->where('scheduleDate','>',$startDate)
                                    ->where('isCompleted',0)
                                    ->select(DB::raw('installmentAmount - partiallyPaidAmount as outStandingAmount'),'loanIdFk')
                                    ->get();

            $weekEndOutStanding = DB::table('mfn_loan_schedule')
                                    ->where('softDel',0)
                                    ->whereIn('loanIdFk',$loans->pluck('id'))
                                    ->where('scheduleDate','>',$endDate)
                                    ->where('isCompleted',0)
                                    ->select(DB::raw('installmentAmount - partiallyPaidAmount as outStandingAmount'),'loanIdFk')
                                    ->get();
        }
        else{
            $beginingCurentDue = DB::table('mfn_loan_schedule')
                                    ->where('softDel',0)
                                    ->whereIn('loanIdFk',$beginingCurrentLoans->pluck('id'))
                                    ->where('scheduleDate','<',$startDate)
                                    ->where('isCompleted',0)
                                    ->select(DB::raw('principalAmount - (`partiallyPaidAmount`/`installmentAmount`*`principalAmount`) as dueAmount'),'loanIdFk')
                                    ->get();

            $weekEndCurentDue = DB::table('mfn_loan_schedule')
                                    ->where('softDel',0)
                                    ->whereIn('loanIdFk',$weekEndCurrentLoans->pluck('id'))
                                    ->where('scheduleDate','<',$endDate)
                                    ->where('isCompleted',0)
                                    ->select(DB::raw('principalAmount - (`partiallyPaidAmount`/`installmentAmount`*`principalAmount`) as dueAmount'),'loanIdFk')
                                    ->get();

             $beginingExpiredDue = DB::table('mfn_loan_schedule')
                                    ->where('softDel',0)
                                    ->whereIn('loanIdFk',$beginigExpiredLoanIds)
                                    ->where('scheduleDate','<',$startDate)
                                    ->where('isCompleted',0)
                                    ->select(DB::raw('principalAmount - (`partiallyPaidAmount`/`installmentAmount`*`principalAmount`) as expiredDueAmount'),'loanIdFk')
                                    ->get();

            $weekEndExpiredDue = DB::table('mfn_loan_schedule')
                                    ->where('softDel',0)
                                    ->whereIn('loanIdFk',$weekEndExpiredLoanIds)
                                    ->where('scheduleDate','<',$endDate)
                                    ->where('isCompleted',0)
                                    ->select(DB::raw('principalAmount - (`partiallyPaidAmount`/`installmentAmount`*`principalAmount`) as expiredDueAmount'),'loanIdFk')
                                    ->get();

            $beginingOutStanding = DB::table('mfn_loan_schedule')
                                    ->where('softDel',0)
                                    ->whereIn('loanIdFk',$loans->pluck('id'))
                                    ->where('scheduleDate','>',$startDate)
                                    ->where('isCompleted',0)
                                    ->select(DB::raw('principalAmount - (`partiallyPaidAmount`/`installmentAmount`*`principalAmount`) as outStandingAmount'),'loanIdFk')
                                    ->get();

            $weekEndOutStanding = DB::table('mfn_loan_schedule')
                                    ->where('softDel',0)
                                    ->whereIn('loanIdFk',$loans->pluck('id'))
                                    ->where('scheduleDate','>',$endDate)
                                    ->where('isCompleted',0)
                                    ->select(DB::raw('principalAmount - (`partiallyPaidAmount`/`installmentAmount`*`principalAmount`) as outStandingAmount'),'loanIdFk')
                                    ->get();
        }
    

        //// Part-B
        $memberAdmission = DB::select("SELECT t1.id,IF(t2.transferDate>?,t2.previousSamityIdFk,t1.`samityId`) AS samityId FROM `mfn_member_information` as t1
LEFT JOIN mfn_member_samity_transfer as t2 ON t1.id=t2.memberIdFk
    WHERE t1.admissionDate>=? AND t1.admissionDate<=? AND t1.branchId=?", [$startDate,$startDate,$endDate,$req->filBranch]);
        $memberAdmission = collect($memberAdmission);
        $memberAdmission = $memberAdmission->whereIn('samityId',$samities->pluck('id'));

        $memberAdmissionByTransfer = DB::table('mfn_member_samity_transfer')
                                    ->where('softDel',0)
                                    ->where('transferDate','>=',$startDate)
                                    ->where('transferDate','<=',$endDate)
                                    ->whereIn('newSamityIdFk',$samities->pluck('id'))
                                    ->select('newSamityIdFk')
                                    ->get();

        $memberClosing = DB::table('mfn_member_closing')
                                ->where('softDel',0)
                                ->whereIn('samityIdFk',$samities->pluck('id'))
                                ->select('memberIdFk','samityIdFk')
                                ->get();

        $memberClosingByTransfer = DB::table('mfn_member_samity_transfer')
                                    ->where('softDel',0)
                                    ->where('transferDate','>=',$startDate)
                                    ->where('transferDate','<=',$endDate)
                                    ->whereIn('previousSamityIdFk',$samities->pluck('id'))
                                    ->select('previousSamityIdFk')
                                    ->get();

        $savingsCollection = DB::table('mfn_savings_deposit')
                                ->where('softDel',0)
                                ->whereIn('samityIdFk',$samities->pluck('id'))
                                ->where('depositDate','>=',$startDate)
                                ->where('depositDate','<=',$endDate)
                                ->select('samityIdFk','amount','productIdFk')
                                ->get();

        $savingsWithdraw = DB::table('mfn_savings_withdraw')
                                ->where('softDel',0)
                                ->whereIn('samityIdFk',$samities->pluck('id'))
                                ->where('withdrawDate','>=',$startDate)
                                ->where('withdrawDate','<=',$endDate)
                                ->select('samityIdFk','amount','productIdFk')
                                ->get();

        $loanDisbursement = DB::table('mfn_loan')
                            ->where('softDel',0)
                            ->where('disbursementDate','>=',$startDate)
                            ->where('disbursementDate','<=',$endDate)
                            ->whereIn('samityIdFk',$samities->pluck('id'))
                            ->select('samityIdFk','loanAmount')
                            ->get();

        $loanComplete = DB::table('mfn_loan')
                            ->where('softDel',0)
                            ->where('isLoanCompleted',1)
                            ->where('loanCompletedDate','>=',$startDate)
                            ->where('loanCompletedDate','<=',$endDate)
                            ->whereIn('samityIdFk',$samities->pluck('id'))
                            ->select('samityIdFk','id')
                            ->get();

        $allColepeLoanIds = DB::table('mfn_loan')
                            ->where('softDel',0)
                            ->where('isLoanCompleted',1)
                            ->where('loanCompletedDate','<=',$endDate)
                            ->whereIn('samityIdFk',$samities->pluck('id'))
                            ->pluck('id')
                            ->toArray();

        $completeLoansCollection = DB::table('mfn_loan_collection')
                                        ->where('softDel',0)
                                        ->whereIn('loanIdFk',$allColepeLoanIds)
                                        ->select('samityIdFk','amount','principalAmount')
                                        ->get();

        $expiredLoanIds = DB::select("SELECT `loanIdFk` FROM `mfn_loan_schedule` as t1 WHERE `scheduleDate` = (SELECT MAX(`scheduleDate`) FROM mfn_loan_schedule WHERE loanIdFk=t1.loanIdFk) AND scheduleDate>=? AND scheduleDate<=? GROUP BY loanIdFk", [$startDate,$endDate]);

        $expiredLoanIds = collect($expiredLoanIds);
        $expiredLoanIds = $expiredLoanIds->pluck('loanIdFk')->toArray();
        $expiredLoanIdsString = implode(',', $expiredLoanIds);

        $expiredLoanOutstandings = DB::select("SELECT t2.id,t2.samityIdFk,SUM(t2.totalRepayAmount - t1.`amount`) as totalOutstanding ,SUM(t2.loanAmount - t1.`principalAmount`) as principalOutstanding FROM `mfn_loan_collection` as t1
            JOIN mfn_loan as t2 ON t1.`loanIdFk`=t2.id
             WHERE t1.softDel=0 AND t2.id IN (?) GROUP BY t1.`loanIdFk`", [$expiredLoanIdsString]);
        $expiredLoanOutstandings = collect($expiredLoanOutstandings);

        $regularLoans = DB::table('mfn_loan')
                            ->where('softDel',0)
                            ->where('disbursementDate','<=',$endDate)
                            ->whereIn('samityIdFk',$samities->pluck('id'))
                            ->whereNotIn('id',$expiredLoanIds)
                            ->select('id','samityIdFk')
                            ->get();

        $regularRecoverable = DB::table('mfn_loan_schedule')
                                    ->whereIn('loanIdFk',$regularLoans->pluck('id'))
                                    ->where('scheduleDate','>=',$startDate)
                                    ->where('scheduleDate','<=',$endDate)
                                    ->select('loanIdFk','installmentAmount','principalAmount','scheduleDate')
                                    ->get();

        $regularRecovery = DB::table('mfn_loan_collection')
                                ->where('softDel',0)
                                ->whereIn('loanIdFk',$regularLoans->pluck('id'))
                                ->where('collectionDate','>=',$startDate)
                                ->where('collectionDate','<=',$endDate)
                                ->select('loanIdFk','samityIdFk','amount','principalAmount')
                                ->get();

        $regularLoanInfo = collect([]);
        foreach ($regularLoans as $regularLoan) {
            // shedule dates of this loan in this week
            $sheduleDates = $regularRecoverable->where('loanIdFk',$regularLoan->id)->pluck('scheduleDate')->toArray();
            foreach ($sheduleDates as $sheduleDate) {
                $totalRecoverable = DB::table('mfn_loan_schedule')->where('softDel',0)->where('loanIdFk',$regularLoan->id)->where('scheduleDate','<=',$sheduleDate)->sum('installmentAmount');
                $totalRecovery = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$regularLoan->id)->where('collectionDate','<=',$sheduleDate)->sum('amount');
                $dueAmount = ($totalRecoverable - $totalRecovery)>0 ? ($totalRecoverable - $totalRecovery) : 0;
                $advanceAmount = ($totalRecovery - $totalRecoverable)>0 ? ($totalRecovery - $totalRecoverable) : 0;
                $afterCollection = DB::table('mfn_loan_collection')
                                        ->where('softDel',0)
                                        ->where('loanIdFk',$regularLoan->id)
                                        ->where('collectionDate','>',$sheduleDate)
                                        ->where('collectionDate','<=',$endDate)
                                        ->sum('amount');
                $dueCollection = ($afterCollection>$dueAmount) ? $dueAmount : $afterCollection;
                $dueInfo = array(
                    'loanId' => $regularLoan->id,
                    'samityId' => $regularLoan->samityIdFk,
                    'dueAmount' => $dueAmount,
                    'advanceAmount' => $advanceAmount,
                    'dueCollection' => $dueCollection,
                );
            }
            if (isset($dueInfo)) {
                $regularLoanInfo = collect($dueInfo);                
            }
        }

        $expiredCollection = DB::select("SELECT `loanIdFk`,samityIdFk,sum(`amount`) as amount FROM `mfn_loan_collection` as t1 WHERE `softDel`=0 AND `collectionDate`>(SELECT MAX(`scheduleDate`) FROM `mfn_loan_schedule` WHERE t1.loanIdFk=loanIdFk) AND collectionDate<=? GROUP BY `loanIdFk`,collectionDate", [$endDate]);
        $expiredCollection = collect($expiredCollection);



        $data = array(
            'startTime'             => $startTime,
            'filBranch'             => $req->filBranch,
            'filSavingsInterest'    => $req->filSavingsInterest,
            'filServiceCharge'      => $req->filServiceCharge,
            'fieldOfficer'          => $fieldOfficer,
            'savingsProducts'       => $savingsProducts,
            'samities'              => $samities,
            'members'               => $members,

            'samityOpeningBalance'  => $samityOpeningBalance,
            'beginingSavings'       => $beginingSavings,
            'thisWeekSavings'       => $thisWeekSavings,
            'weekEndSavings'        => $weekEndSavings,

            'beginingWithdraws'     => $beginingWithdraws,
            'thisWeekWithdraws'     => $thisWeekWithdraws,
            'weekEndWithdraws'      => $weekEndWithdraws,

            'beginingWriteOffs'     => $beginingWriteOffs,
            'thisWeekWriteOffs'     => $thisWeekWriteOffs,
            'weekEndWriteOffs'      => $weekEndWriteOffs,

            'samityLoanOpeningBalance' => $samityLoanOpeningBalance,
            'beginingLoans'         => $beginingLoans,
            'thisWeekLoans'         => $thisWeekLoans,
            'weekEndLoans'          => $weekEndLoans,

            'beginingPaidLoans'     => $beginingPaidLoans,
            'thisWeekPaidLoans'     => $thisWeekPaidLoans,
            'weekEndPaidLoans'      => $weekEndPaidLoans,

            'beginigExpiredLoanIds' => $beginigExpiredLoanIds,
            'beginingExpiredLoans'  => $beginingExpiredLoans,

            'weekEndExpiredLoanIds'  => $weekEndExpiredLoanIds,
            'weekEndExpiredLoans'  => $weekEndExpiredLoans,
            /*'beginingLoansOutstandingAmount'  => $beginingLoansOutstandingAmount,*/

            'beginingCurrentLoans'  => $beginingCurrentLoans,
            'beginingCurentDue'     => $beginingCurentDue,
            'weekEndCurentDue'     => $weekEndCurentDue,
            'beginingExpiredDue'     => $beginingExpiredDue,
            'weekEndExpiredDue'     => $weekEndExpiredDue,

            'beginingOutStanding'     => $beginingOutStanding,
            'weekEndOutStanding'     => $weekEndOutStanding,

            'memberAdmission'     => $memberAdmission,
            'memberAdmissionByTransfer'     => $memberAdmissionByTransfer,
            'memberClosing'     => $memberClosing,
            'memberClosingByTransfer'     => $memberClosingByTransfer,

            'savingsCollection'     => $savingsCollection,
            'savingsWithdraw'     => $savingsWithdraw,
            'loanDisbursement'     => $loanDisbursement,
            'loanComplete'     => $loanComplete,
            'completeLoansCollection'     => $completeLoansCollection,
            'expiredLoanOutstandings'     => $expiredLoanOutstandings,

            'regularLoans'     => $regularLoans,
            'regularRecoverable'     => $regularRecoverable,
            'regularRecovery'     => $regularRecovery,

            'regularLoanInfo'     => $regularLoanInfo,
            'expiredCollection'     => $expiredCollection,

            'weekEndsavingsInterest'     => $weekEndsavingsInterest,
            'beginingSavingsInterest'     => $beginingSavingsInterest,
        );

        return view('microfin.reports.regularNGeneralReports.fieldOfficerReport.fieldOfficerReport', $data);
        
        
    }

}
