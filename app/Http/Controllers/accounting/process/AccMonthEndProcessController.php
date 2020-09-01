<?php

namespace App\Http\Controllers\accounting\process;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Traits\CreateForm;
// use App\microfin\process\MfnDayEnd;
// use App\microfin\process\MfnMonthEnd;
use Auth;

use App\accounting\process\AccDayEnd;
use App\accounting\process\AccMonthEnd;
use App\accounting\process\AccYearEnd;
use App\gnr\GnrBranch;
use App\Http\Controllers\accounting\Accounting;
use App\Http\Controllers\microfin\MicroFinance;
use App\Service\Service;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class AccMonthEndProcessController extends Controller {

    protected $Accounting;
    protected $MicroFinance;
    protected $Service;

    public function __construct() {
        $this->Accounting = new Accounting;
        $this->MicroFinance = New MicroFinance;
        $this->Service = New Service;
    }

    public function accMonthEndProcess(Request $request) {

        $userBranch = Auth::user()->branch;
        $userCompanyId = Auth::user()->company_id_fk;

        $monthsOption = $this->MicroFinance->getMonthsOption();

        $yearsOption = array();

        if (isset($request->filBranch)) {
            $targetBranchId = $request->filBranch;
        }
        else{
            $targetBranchId = $userBranch->id;
        }


        // $dayEndMinYear = AccDayEnd::active()->where('branchIdFk',$targetBranchId)->where('isDayEnd',1)->min('date');
        // $dayEndMinYear = (int) Carbon::parse($dayEndMinYear)->format('Y');

        $dayEndMinYear = Carbon::parse(DB::table('gnr_branch')->where('id', $targetBranchId)->value('softwareStartDate'))->format('Y');
        // $dayEndMinYear = (int) Carbon::parse($dayEndMinYear)->format('Y');

        // $dayEndMaxYear = AccDayEnd::active()->where('branchIdFk',$targetBranchId)->where('isDayEnd',1)->max('date');
        // $dayEndMaxYear = (int) Carbon::parse($dayEndMaxYear)->format('Y');
        $dayEndMaxYear = (int) Carbon::now()->format('Y');

        $yearsOption = array_combine(range($dayEndMaxYear, $dayEndMinYear), range($dayEndMaxYear, $dayEndMinYear));

        // $branchList = DB::table('gnr_branch');
           $branchList = DB::table('gnr_branch')->where('companyId',$userCompanyId);

        if ($userBranch->branchCode!=0) {
            $branchList = $branchList->where('id',$userBranch->id);
        }

        $branchOption = $branchList
                   ->orderBy('branchCode')
                   ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                   ->pluck('nameWithCode', 'id')
                   ->toArray();
                //    dd($branchOption);

        $filteringArr = array(
            'monthsOption'  =>  $monthsOption,
            'yearsOption'   =>  $yearsOption,
            'userBranchId'  =>  $userBranch->id,
            'userBranchCode'  =>  $userBranch->branchCode,
            'branchOption'  =>  $branchOption,
        );

        return view('accounting.process.monthEndProcess.filteringPart',$filteringArr);
    }


    public function loadAccMonthEndProcess(Request $request){

        $userBranch = Auth::user()->branch;

        if($userBranch->branchCode == 0){
            $targetBranchId = $request->filBranch;
        }else{
            $targetBranchId = $userBranch->id;
        }

        $monthEndInfo = AccMonthEnd::active()->where('branchIdFk',$targetBranchId)->where('isMonthEnd',1);
        if($request->checkFirstLoad==1){
            $yearStartDate = Carbon::parse('01-01-'.$request->filYear);
            $yearEndDate = Carbon::parse('31-12-'.$request->filYear);
            $monthEndInfo = $monthEndInfo->where('date','>=',$yearStartDate)->where('date','<=',$yearEndDate);
        }

        $monthEndInfo = $monthEndInfo->orderBy('date','desc')->paginate(50);

        $activeDeleteMonthEndId = (int) AccMonthEnd::active()->where('branchIdFk',$targetBranchId)->where('isMonthEnd',1)->orderBy('date','desc')->value('id');

        // $dayEndInfomation = $dayEndInfomation->orderBy('date','desc')->paginate(2);

        $branchName=DB::table('gnr_branch')->where('id',$targetBranchId)->value('name');

        // identify month to close
        $lastMonthEndDate = AccMonthEnd::active()->where('branchIdFk', $targetBranchId)->max('date');
        // dd($lastMonthEndDate);
        if ($lastMonthEndDate) {
            $currentMonth = Carbon::parse($lastMonthEndDate)->addDay()->endOfMonth()->format('F Y');
        } else {
            $currentMonth = Carbon::parse(GnrBranch::where('id', $targetBranchId)->value('aisStartDate'))->endOfMonth()->format('F Y');
        }
        // dd($currentMonth);

        $loadingArr = array(
                            'branchName'            => $branchName,
                            'currentMonth'          => $currentMonth,
                            'monthEndInfo'          => $monthEndInfo,
                            'activeDeleteMonthEndId'=> $activeDeleteMonthEndId,
                            'targetBranchId'        => $targetBranchId,
                            'userBranchId'          => $userBranch->id,
                        );

        return view('accounting.process.monthEndProcess.monthEndProcess',$loadingArr);
    }


    public function addAccMonthEndProcessItem(Request $request){

        $targetBranchId = $request->branchId;
        $userCompanyId = Auth::user()->company_id_fk;

        // identify month to close and verify conditions
        $lastMonthEndDate = AccMonthEnd::active()->where('branchIdFk',$targetBranchId)->max('date');
        // dd($lastMonthEndDate);
        if ($lastMonthEndDate) {
            // fiscal year end date
            $lastDateOfFY = DB::table('gnr_fiscal_year')
                                ->where('companyId', $userCompanyId)
                                ->where('fyStartDate', '<=', $lastMonthEndDate)
                                ->where('fyEndDate', '>=', $lastMonthEndDate)
                                ->value('fyEndDate');

            // if last closed month is last date of fy
            if ($lastDateOfFY == $lastMonthEndDate) {
                // check year end
                $yearEndExists = DB::table('acc_year_end')
                                ->where('companyId', $userCompanyId)
                                ->where('branchIdFk', $targetBranchId)
                                ->where('date', $lastMonthEndDate)
                                ->first();

                if (!$yearEndExists) {
                    $data = array(
                        'responseTitle' =>  'Warning!',
                        'responseText'  =>  'Please execute year end for previous fiscal year to proceed!'
                    );

                    return response::json($data);
                }
            }

            $monthEndDate = Carbon::parse($lastMonthEndDate)->addDay()->endOfMonth()->format('Y-m-d');
        }
        else {
            $monthEndDate = Carbon::parse(GnrBranch::where('id', $targetBranchId)->value('aisStartDate'))->endOfMonth()->format('Y-m-d');
        }
        $monthStartDate = Carbon::parse($monthEndDate)->startOfMonth()->format('Y-m-d');
        // dd($monthEndDate, $monthFirstDate);

        // check un-authorized vouchers
        $unauthorizedVoucherCounter = DB::table('acc_voucher')
                                    ->where('vGenerateType', 2)
                                    ->where('branchId', $targetBranchId)
                                    ->where('voucherDate', '>=', $monthStartDate)
                                    ->where('voucherDate', '<=', $monthEndDate)
                                    ->where('authBy', 0)
                                    ->count();
                                    // dd($unauthorizedVoucherCounter);

        if ($unauthorizedVoucherCounter > 0) {

            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  "Please authorize all pending Vouchers of <em><u>" . Carbon::parse($monthEndDate)->format('F, Y') . "</em></u>"
            );

            return response::json($data);
        }

        // month end Execution start if condition fulfilled
        $monthEnd = AccMonthEnd::where('branchIdFk', $targetBranchId)->where('date', $monthEndDate)->first();
        if (!$monthEnd) {
            $monthEnd = new AccMonthEnd;
        }

        $monthEnd->date         = $monthEndDate;
        $monthEnd->branchIdFk   = $targetBranchId;
        $monthEnd->isMonthEnd   = 1;
        $monthEnd->createdAt    = Carbon::now();

        /////// month end balance data passing ///////////
        $branchCode =  DB::table('gnr_branch')->where('id', $targetBranchId)->value('branchCode');
        $currentfiscalYearId = DB::table('gnr_fiscal_year')
                            ->where('companyId', $userCompanyId)
                            ->where('fyStartDate', '<=', $monthEndDate)
                            ->where('fyEndDate', '>=', $monthEndDate)
                            ->value('id');
        // dd($currentfiscalYearId);

        if ($branchCode == 0) {
            $projectIdArr = DB::table('gnr_project')->where('companyId', $userCompanyId)->pluck('id')->toArray();
        } else {
            $projectIdArr = DB::table('gnr_branch')->where('id', $targetBranchId)->pluck('projectId')->toArray();
        }
        // dd($projectIdArr);

        // delete previous data if exists
        DB::table('acc_month_end_balance')->where('companyIdFk', $userCompanyId)->where('branchId', $targetBranchId)->where('monthEndDate', $monthEndDate)->delete();

        // total
        $dataArr = [];
        $totalDebitAmount = 0;
        $totalCreditAmount = 0;
        // $projectIdArr = [1];
        foreach ($projectIdArr as $projectId) {
            // dd($projectIdArr);
            $ledgers = $this->Service->getLedgerHeaderInfo($projectId, $targetBranchId, $userCompanyId)->where('isGroupHead', 0);
            $cashLedgers = $ledgers->where('accountTypeId', 4)->pluck('id')->toArray();
            $bankLedgers = $ledgers->where('accountTypeId', 5)->pluck('id')->toArray();
            $projectTypeIdArr = DB::table('gnr_project_type')->where('projectId', $projectId)->pluck('id')->toArray();
            // dd($projectTypeIdArr);
            foreach ($projectTypeIdArr as $projectTypeId) {
                // dd($projectTypeIdArr);
                $voucherInfos = $this->Service->getVoucherInfo($projectTypeId, $projectId, $targetBranchId, $userCompanyId, $monthStartDate, $monthEndDate);
                // dd($voucherInfos);

                foreach ($ledgers as $key => $ledger) {

                    $obId = $currentfiscalYearId . "." . $projectId . "." . $targetBranchId . "." . $projectTypeId;
                    // dd($obId);
                    $debitAmount = $voucherInfos->where('debitAcc', $ledger->id)->sum('amount');
                    $creditAmount = $voucherInfos->where('creditAcc', $ledger->id)->sum('amount');
                    // dd($creditAmount);

                    if ($debitAmount == 0 && $creditAmount == 0) {
                        // do nothing
                    } else {
                        $cashDebit = $voucherInfos->where('voucherTypeId', '!=', 5)
                            ->where('debitAcc', $ledger->id)
                            ->whereIn('creditAcc', $cashLedgers)
                            ->sum('amount');
                        $cashCredit = $voucherInfos->where('voucherTypeId', '!=', 5)
                            ->where('creditAcc', $ledger->id)
                            ->whereIn('debitAcc', $cashLedgers)
                            ->sum('amount');
                        // dd($cashCredit);
                        $bankDebit = $voucherInfos->where('voucherTypeId', '!=', 5)
                            ->where('debitAcc', $ledger->id)
                            ->whereIn('creditAcc', $bankLedgers)
                            ->sum('amount');
                        $bankCredit = $voucherInfos->where('voucherTypeId', '!=', 5)
                            ->where('creditAcc', $ledger->id)
                            ->whereIn('debitAcc', $bankLedgers)
                            ->sum('amount');
                        // dd($bankCredit);
                        $ftDebit = $voucherInfos->where('debitAcc', $ledger->id)->where('voucherTypeId', 5)->sum('amount');
                        $ftCredit = $voucherInfos->where('creditAcc', $ledger->id)->where('voucherTypeId', 5)->sum('amount');
                        // dd($ftCredit);
                        $jvDebit = $voucherInfos->where('debitAcc', $ledger->id)->where('voucherTypeId', 3)->sum('amount');
                        $jvCredit = $voucherInfos->where('creditAcc', $ledger->id)->where('voucherTypeId', 3)->sum('amount');
                        // dd($jvCredit);

                        $dataArr[] = array(

                            'obId'          => $obId,
                            'projectId'     => $projectId,
                            'branchId'      => $targetBranchId,
                            'companyIdFk'   => $userCompanyId,
                            'projectTypeId' => $projectTypeId,
                            'monthEndDate'  => $monthEndDate,
                            'fiscalYearId'  => $currentfiscalYearId,
                            'ledgerId'      => $ledger->id,
                            'debitAmount'   => $debitAmount,
                            'creditAmount'  => $creditAmount,
                            'balanceAmount' => $debitAmount - $creditAmount,
                            'cashDebit'     => $cashDebit,
                            'cashCredit'    => $cashCredit,
                            'bankDebit'     => $bankDebit,
                            'bankCredit'    => $bankCredit,
                            'jvDebit'       => $jvDebit,
                            'jvCredit'      => $jvCredit,
                            'ftDebit'       => $ftDebit,
                            'ftCredit'      => $ftCredit,
                            'createdDate'   => Carbon::now()
                        );
                        // dd($data);

                        // total
                        $totalDebitAmount += $debitAmount;
                        $totalCreditAmount += $creditAmount;
                    }
                } // foreach loop ledger
            }  // foreach loop projectTypeId
        }   // foreach loop projectId

        $debitAndCredit = array(
            // 'dataArr'   => $dataArr,
            'debit'     => $totalDebitAmount,
            'credit'    => $totalCreditAmount,
        );
        // dd($dataArr);

        if (count($dataArr) == 0) {
            // return;
        } else {
            // check debit and credit match
            if (round($debitAndCredit['debit']) != round($debitAndCredit['credit'])) {

                // $array = array(
                //     'debit'         => $debitAndCredit['debit'],
                //     'credit'        => $debitAndCredit['credit'],
                //     'diff'          => $debitAndCredit['debit'] - $debitAndCredit['credit'],
                //     'date'          => $monthEndDate,
                //     'dataArr'       => $dataArr
                // );

                $data = array(
                    'responseTitle' => 'Warning!',
                    'responseText'  => 'Debit Amount and Credit Amount are not same!<br>' . 'Total Debit: ' . $debitAndCredit['debit'] . '<br>Total Credit: ' . $debitAndCredit['credit'] . '<br>Difference: ' . ($debitAndCredit['debit'] - $debitAndCredit['credit']),
                );

                return response::json($data);
            }
            // if matched then execute
            else {
                // dd($dataArr);
                foreach ($dataArr as $key => $data) {
                    // insert data
                    DB::table('acc_month_end_balance')->insert($data);
                }  // foreach loop closed
            }  // execution area close

        }


        // month end execute
        $monthEndExists = $monthEnd->where('branchIdFk', $targetBranchId)->where('date', $monthEndDate)->first();
        // dd($monthEndExists);
        if (!$monthEndExists) {
            $monthEnd->save();
        }

        $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Month End Executed Successfully.<br/><b><em>Month: <u>' . Carbon::parse($monthEndDate)->format('F Y') . '</u><em/><b/>'
        );

        return response::json($data);
    }


     public function deleteAccMonthEndItem(Request $request){

        $monthEnd = AccMonthEnd::find($request->id);

        // check if there is any year end after the month end date
        $isYearEndExists = AccYearEnd::where('branchIdFk',$monthEnd->branchIdFk)->where('date','>=',$monthEnd->date)->first();
        if ($isYearEndExists) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Please Delete All Year Ends After The Month (<b><em><u>'.Carbon::parse($monthEnd->date)->format('F Y').'</u></b></em>)'

            );

            return response::json($data);
        }

        $monthEnd->delete();
        DB::table('acc_month_end_balance')->where('branchId', $monthEnd->branchIdFk)->where('monthEndDate', $monthEnd->date)->delete();

        $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Month End Deleted Successfully.<br/><b><em>Month: <u>'.Carbon::parse($monthEnd->date)->format('F Y').'</u></b></em>'
        );

        return response::json($data);
    }

    public function manualMonthEnd(){


        $branchList = DB::table('gnr_branch')
                    ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS branchName"), 'id')
                    ->pluck('branchName','id')
                    ->toArray();

        $fiscalYears = DB::table('gnr_fiscal_year')
                    ->orderBy('name', 'desc')
                    ->pluck('name', 'id')
                    ->toArray();

        $data = array(
            'branchList'    =>  $branchList,
            'fiscalYears'   =>  $fiscalYears
        );
        // dd($data);

        return view('accounting.process.monthEndProcess.massExecute',$data);
    }

    public function getMonthsByFiscalYear(Request $request) {

        $fiscalYear = DB::table('gnr_fiscal_year')
                    ->where('id', $request->fiscalYearId)
                    ->first();

        $fyStartDate = $fiscalYear->fyStartDate;
        $fyEndDate = $fiscalYear->fyEndDate;

        $firstMonth = Carbon::parse($fyStartDate)->format('Y-m-d');
        $currentMonth = $firstMonth;

        for ($i=1; $i<=12; $i++) {
            $monthsArr[Carbon::parse($currentMonth)->endOfMonth()->format('Y-m-d')] = Carbon::parse($currentMonth)->format('M, Y'); // collect all months ends
            $nextMonth = Carbon::parse($currentMonth)->addMonths(1)->format('Y-m-d');
            $currentMonth = $nextMonth;
        }
        // dd($monthsArr);

        return response()->json($monthsArr);
    }

    public function executeMonthEnd(Request $req) {

        // manual month end execution
        $branchIdArr = $req->filBranch;
        $monthsArr = $req->months;

        $failedData = [];

        foreach ($branchIdArr as $key => $branch) {
            foreach ($monthsArr as $key => $date) {

                $aisStartDate = DB::table('gnr_branch')->where('id', $branch)->value('aisStartDate');

                if ($aisStartDate <= $date) {
                    $array = $this->Service->monthEndExecute($branch, $date);
                }
                else {
                    $array = [];
                }

                if (count($array) != 0) {
                    $failedData[] = $array;
                }

            }
        }
        // dd($failedData);
        /// manual month end execution ends

        return view('accounting.process.monthEndProcess.massExecuteReport', ['failedData' => $failedData]);
    }

}
