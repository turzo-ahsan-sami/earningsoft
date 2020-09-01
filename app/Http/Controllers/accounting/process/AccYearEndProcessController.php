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
use Auth;
use App\Service\EasyCode;
use App\Service\Service;

use App\accounting\process\AccDayEnd;
use App\accounting\process\AccMonthEnd;
use App\accounting\process\AccYearEnd;
use App\gnr\GnrBranch;
use App\gnr\FiscalYear;
use App\Http\Controllers\accounting\Accounting;
use App\Http\Controllers\microfin\MicroFinance;

class AccYearEndProcessController extends Controller {

    protected $Accounting;

    public function __construct() {
        $this->Accounting = new Accounting;
        $this->Service = new Service;
    }

    public function accYearEndProcess(Request $request) {

        $companyId = Auth::user()->company_id_fk;
        $userBranch = Auth::user()->branch;

        // identify current month and years
        $lastMonthEndDate = AccMonthEnd::active()->where('branchIdFk', $userBranch->id)->max('date');
        //dd($userBranch->id);
        
        if ($lastMonthEndDate) {
            $currentMonth = Carbon::parse($lastMonthEndDate)->addDay()->endOfMonth()->format('Y-m-d');
        } else {
            $currentMonth = Carbon::parse(GnrBranch::where('id', $userBranch->id)->value('aisStartDate'))->endOfMonth()->format('Y-m-d');
        }
        //dd($currentMonth);

        $fiscalYearInfo = DB::table('gnr_fiscal_year')->where('companyId',$companyId)
                         ->where('fyStartDate', '<=', $currentMonth)
                         ->where('fyEndDate', '>=', $currentMonth)
                        ->first();
         //dd($fiscalYearInfo);
        $lastEndedYears = AccYearEnd::orderBy('endDate', 'desc')->where('companyId', $companyId)->groupBy('branchIdFk')->get();
        // dd($lastEndedYears);

        // collect branches
        $branchList = DB::table('gnr_branch')->where('companyId', $companyId);

        if ($userBranch->branchCode != 0) {
            $branchList = $branchList->where('id', $userBranch->id);
        }

        $branchOption = $branchList
                        ->orderBy('branchCode')
                        ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                        ->pluck('nameWithCode', 'id')
                        ->toArray();
       //dd($fiscalYearInfo);
        $filteringArr = array(
            'userbranchName'       =>  $userBranch->name,
            'userBranchId'         =>  $userBranch->id,
            'userBranchCode'       =>  $userBranch->branchCode,
            'companyId'            =>  $companyId,
            'fiscalYearInfo'       =>  $fiscalYearInfo,
            'branchOption'         =>  $branchOption,
            'lastEndedYears'       =>  $lastEndedYears,
        );

        return view('accounting.process.yearEndProcess.filteringPart',$filteringArr);
    }

    public function ajaxBranch() {

        $companyId = Auth::user()->company_id_fk;
        $branches = DB::table('gnr_branch')
                    ->where('companyId', '=', $companyId)
                    ->orderBy('branchCode')
                    ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                    ->get();

        if (Auth::user()->branch->branchCode != 0) {
            $branches = $branches->where('id', Auth::user()->branchId);
        }

        return response::json($branches);
    }

    public function getBranchInfo(Request $request) {

        $branchId = $request->branchId;
        $branchInfo = DB::table('gnr_branch')->where('id', '=', $branchId)->first();

        $branchName = DB::table('gnr_branch')
                    ->where('id', '=', $branchId)
                    ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                    ->value('nameWithCode');

        // identify current month and years
        $lastMonthEndDate = AccMonthEnd::active()->where('branchIdFk', $branchInfo->id)->max('date');
        // dd($lastMonthEndDate);
        if ($lastMonthEndDate) {
            $currentMonth = Carbon::parse($lastMonthEndDate)->addDay()->endOfMonth()->format('Y-m-d');
        } else {
            $currentMonth = Carbon::parse(GnrBranch::where('id', $branchInfo->id)->value('aisStartDate'))->endOfMonth()->format('Y-m-d');
        }
        // dd($currentMonth);

        $currentYear = DB::table('gnr_fiscal_year')->where('companyId', $branchInfo->companyId)
                    ->where('fyStartDate', '<=', $currentMonth)
                    ->where('fyEndDate', '>=', $currentMonth)
                    ->value('name');
        // dd($currentYear);

        $branchInfo = array(
            'branchId'          => $branchId,
            'branchName'        => $branchName,
            'branchDate'        => $currentMonth,
            'currentYear'       => $currentYear,
        );

        return response::json($branchInfo);
    }

    public function loadAccYearEndProcess(Request $request){

        $companyId = Auth::user()->company_id_fk;
        $userBranch = Auth::user()->branch;
        if ($request->filBranch) {
            $branchId = $request->filBranch;
        } else {
            $branchId = $userBranch->id;
        }
        
        $fiscalYearInfo = DB::table('gnr_fiscal_year')->where('companyId', $companyId)->get();
        $branchInfo= GnrBranch::find($branchId);
        $yearEndInfo = AccYearEnd::whereIn('fiscalYearId', $fiscalYearInfo->pluck('id'))
                    ->where('branchIdFk', $branchInfo->id)
                    ->orderBy('date', 'desc')
                    ->paginate(10);
        // dd($fiscalYearInfo,$branchInfo);
        //dd($yearEndInfo->where('id', $activeDeleteYearEndId)->first()->endDate);
        $activeDeleteYearEndId = (int) AccYearEnd::where('branchIdFk',$branchId)->orderBy('date','desc')->value('id');
        //dd($activeDeleteYearEndId);
        if ($activeDeleteYearEndId) {
            $currentYear = DB::table('gnr_fiscal_year')
                            ->where('companyId', $companyId)
                            ->where('fyEndDate', '>', $yearEndInfo->where('id', $activeDeleteYearEndId)->first()->endDate)
                            ->orderBy('fyEndDate')
                            ->value('name');

        }

        else {
            $currentYear = DB::table('gnr_fiscal_year')->where('companyId', $companyId)
                            ->where('fyStartDate', '<=', $branchInfo->aisStartDate)
                            ->where('fyEndDate', '>=', $branchInfo->aisStartDate)
                            ->value('name');
        }
        

        $loadingArr = array(
                            'branchInfo'            => $branchInfo,
                            'currentYear'           => $currentYear,
                            'yearEndInfo'           => $yearEndInfo,
                            'activeDeleteYearEndId' => $activeDeleteYearEndId,
                            'BranchId'              => $branchId,
                            'userBranch'            => $userBranch,
                            'fiscalYearInfo'        => $fiscalYearInfo
                        );
                        // dd($loadingArr);

        return view('accounting.process.yearEndProcess.YearEndProcess',$loadingArr);
    }


    public function addAccYearEndProcessItem(Request $request){
        //dd($request->all());
        $companyId = Auth::user()->company_id_fk;
        $branch =  Auth::user()->branch;
        if($branch->branchCode != 0){
            $branchId =  Auth::user()->branchId;
            $headOfficeId = GnrBranch::where('companyId', $companyId)->where('branchCode', 0)->value('id');
        } else {
            $branchId = $request->branchId;
            $headOfficeId = Auth::user()->branchId;
        }
        //dd($headOfficeId);
        
        if($branch->branchCode != 0){
            $projectIdArr = DB::table('gnr_project')->where('companyId', $companyId)->pluck('id')->toArray();
        } else{
            $projectIdArr = DB::table('gnr_branch')->where('id', $branchId)->pluck('projectId')->toArray();
        }
        //dd($projectIdArr);

        $lastMonthEndDate = AccMonthEnd::active()->where('branchIdFk',$branchId)->where('isMonthEnd',1)->max('date');
        // dd($lastMonthEndDate);
        if($lastMonthEndDate){
            $fiscalYearInfo = DB::table('gnr_fiscal_year')->where('companyId',$companyId)->where('fyEndDate', '>=', $lastMonthEndDate)->where('fyStartDate', '<=', $lastMonthEndDate)->first();
         //dd($fiscalYearInfo->fyEndDate);

            if($lastMonthEndDate != $fiscalYearInfo->fyEndDate){
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  "Execute All Month Ends of this Fiscal Year!."
                );

                return response::json($data);
            }
        } else {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  "No Month End Available!."
            );

            return response::json($data);
        }

        // check all branches year end before head office year end
        if($branch->branchCode == 0){

            // check other active branches number
            $activeBranchesExceptHO = DB::table('acc_voucher')->where('branchId', '!=', $headOfficeId)->distinct('branchId')->pluck('branchId');
            $activeBranchesCount = $activeBranchesExceptHO->count('branchId');
            // dd($activeBranchesCount);
            // check count of other active year ended branches
            $yearEndBranchesExceptHOCount = AccYearEnd::where('branchIdFk', '!=', $headOfficeId)->where('endDate', $fiscalYearInfo->fyEndDate)->count('branchIdFk');


            if ($activeBranchesCount != $yearEndBranchesExceptHOCount) {

                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  "Execute All Active Branches' Year End First to Execute Head Office Year End."
                );

                return response::json($data);
            }

        }

        // all month end check
        // dd($fiscalYearInfo);
        // if ($fiscalYearInfo) {
        //dd($fiscalYearInfo);
        $yearStartDate = $fiscalYearInfo->fyStartDate;
        $yearEndDate = $fiscalYearInfo->fyEndDate;
        $fiscalYearId = $fiscalYearInfo->id;
        $lastFiscalYearEndDate = Carbon::parse($yearStartDate)->subDays(1)->format('Y-m-d');
        //dd($fiscalYearId);

        // Execute year end
        $isYearEndExists = (int) AccYearEnd::where('branchIdFk',$branchId)->where('date',$yearEndDate)->value('id');
        if ($isYearEndExists>0) {
            $yearEnd = AccYearEnd::where('branchIdFk',$branchId)->where('date',$yearEndDate)->first();

            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Year End is Already Executed!'
            );

            return response::json($data);
        }
        else{
            $yearEnd = new AccYearEnd;

            $yearEnd->date          = $yearEndDate;
            $yearEnd->branchIdFk    = $branchId;
            $yearEnd->companyId     = $companyId;
            $yearEnd->fiscalYearId  = $fiscalYearId;
            $yearEnd->startDate     = $yearStartDate;
            $yearEnd->endDate       = $yearEndDate;
            $yearEnd->createdAt     = Carbon::now();
            //dd($yearEnd);

            // send information to opening balance for next fiscal year
            $openingBalanceInfo = DB::table('acc_opening_balance')
                                ->where('branchId', $branchId)
                                ->where('openingDate', $lastFiscalYearEndDate)
                                ->get();

            // delete previous existing data
            DB::table('acc_opening_balance')->where('branchId', $branchId)->where('openingDate', $yearEndDate)->delete();
            // dd($previousExistingInfo);

            $dataArr = [];
            $array = [];
            foreach($projectIdArr as $projectId){
                // dd($projectIdArr);
                $totalDebitAmount = 0;
                $totalCreditAmount = 0;
                // $dataArr = [];

                $ledgers = $this->Service->getLedgerHeaderInfo($projectId, $branchId, $companyId)->where('isGroupHead', 0);
                // dd($ledgers);
                $projectTypeIdArr = DB::table('gnr_project_type')
                                    ->where('projectId', $projectId)
                                    ->pluck('id')->toArray();
                                    // dd($projectTypeIdArr);
                foreach($projectTypeIdArr as $projectTypeId){
                    // dd($projectTypeIdArr);
                    // $voucherInfos = $this->getVoucherInfo($projectTypeId, $projectId, $branchId, $companyId, $yearStartDate, $yearEndDate);
                    $monthEndsInfo = $this->Service->getMonthEndsInfo($projectTypeId, $projectId, $branchId, $companyId, $yearStartDate, $yearEndDate);
                    // dd($monthEndsInfo);

                    foreach ($ledgers as $key => $ledger) {
                        // dd($ledger->id);
                        $openingBalanceLedgerInfo = $openingBalanceInfo
                                                    ->where('ledgerId', $ledger->id)
                                                    ->where('projectId', $projectId)
                                                    ->where('projectTypeId', $projectTypeId);
                        // dd($openingBalanceLedgerInfo);
                        // collect data from op balance by ledger id
                        $openingDebitAmount     = $openingBalanceLedgerInfo->sum('debitAmount');
                        $openingCreditAmount    = $openingBalanceLedgerInfo->sum('creditAmount');
                        $openingCashDebit       = $openingBalanceLedgerInfo->sum('cashDebit');
                        $openingCashCredit      = $openingBalanceLedgerInfo->sum('cashCredit');
                        $openingBankDebit       = $openingBalanceLedgerInfo->sum('bankDebit');
                        $openingBankCredit      = $openingBalanceLedgerInfo->sum('bankCredit');
                        $openingJvDebit         = $openingBalanceLedgerInfo->sum('jvDebit');
                        $openingJvCredit        = $openingBalanceLedgerInfo->sum('jvCredit');
                        $openingFtDebit         = $openingBalanceLedgerInfo->sum('ftDebit');
                        $openingFtCredit        = $openingBalanceLedgerInfo->sum('ftCredit');

                        // collect data for new op balance by ledger id
                        $obId           = $fiscalYearId.".".$projectId.".".$branchId.".".$projectTypeId;
                        $debitAmount    = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('debitAmount') + $openingDebitAmount;
                        $creditAmount   = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('creditAmount') + $openingCreditAmount;
                        $cashDebit      = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('cashDebit') + $openingCashDebit;
                        $cashCredit     = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('cashCredit') + $openingCashCredit;
                        $bankDebit      = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('bankDebit') + $openingBankDebit;
                        $bankCredit     = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('bankCredit') + $openingBankCredit;
                        $jvDebit        = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('jvDebit') + $openingJvDebit;
                        $jvCredit       = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('jvCredit') + $openingJvCredit;
                        $ftDebit        = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('ftDebit') + $openingFtDebit;
                        $ftCredit       = $monthEndsInfo->where('ledgerId', $ledger->id)->sum('ftCredit') + $openingFtCredit;
                        // dd($creditAmount);

                        // total
                        $totalDebitAmount += $debitAmount;
                        $totalCreditAmount += $creditAmount;

                        $dataArr[] = array(
                            'obId'              => $obId,
                            'projectId'         => $projectId,
                            'branchId'          => $branchId,
                            'companyIdFk'       => $companyId,
                            'projectTypeId'     => $projectTypeId,
                            'openingDate'       => $yearEndDate,
                            'fiscalYearId'      => $fiscalYearId,
                            'ledgerId'          => $ledger->id,
                            'debitAmount'       => $debitAmount,
                            'creditAmount'      => $creditAmount,
                            'balanceAmount'     => $debitAmount - $creditAmount,
                            'cashDebit'         => $cashDebit,
                            'cashCredit'        => $cashCredit,
                            'bankDebit'         => $bankDebit,
                            'bankCredit'        => $bankCredit,
                            'jvDebit'           => $jvDebit,
                            'jvCredit'          => $jvCredit,
                            'ftDebit'           => $ftDebit,
                            'ftCredit'          => $ftCredit,
                            'createdDate'       => Carbon::now(),
                            'ds'                => 0
                        );
                        // dd($dataArr);

                    } // foreach loop ledger
                }  // foreach loop projectTypeId
            }   // foreach loop

            $debitAndCredit = array(
                'debit'   => $totalDebitAmount,
                'credit'  => $totalCreditAmount,
            );
            // dd($debitAndCredit);
            // check debit and credit match
            if (round($debitAndCredit['debit']) != round($debitAndCredit['credit'])) {

                // $array[] = array(
                //     // 'dataArr'   => $dataArr,
                //     'debit'     => $debitAndCredit['debit'],
                //     'credit'    => $debitAndCredit['credit'],
                //     'diff'      => $debitAndCredit['debit'] - $debitAndCredit['credit'],
                //     'date'      => $yearEndDate,
                //     'branchId'  => $branchId,
                //     'projectId' => $projectId,
                // );

                $data = array(
                    'responseTitle' => 'Warning!',
                    'responseText'  => 'Debit Amount and Credit Amount are not same!<br>'. 'Total Debit: ' . $debitAndCredit['debit'] . '<br>Total Credit: ' . $debitAndCredit['credit'] . '<br>Difference: '. ($debitAndCredit['debit'] - $debitAndCredit['credit']),
                );

                return response::json($data);

            }
            // if matched then execute
            else {
                // dd(collect($dataArr)->where('ledgerId', 412)->where('projectTypeId', 2));
                foreach ($dataArr as $key => $data) {

                    DB::table('acc_opening_balance')->insert($data);

                } // foreach loop db insert

            } // execution close=====

            $yearEndExists = $yearEnd->where('branchIdFk', $branchId)->where('fiscalYearId', $fiscalYearId)->where('companyId',$companyId)->first();
            //dd($yearEndExists);
            if ($yearEndExists) {

            }
            else {
                $yearEnd->save();

                // fiscal year generate
                   $userBranch = Auth::user()->branch;
                    $company = \App\gnr\GnrCompany::find($companyId);
                    //dd($company);
                    $lastMonthEndDate = AccMonthEnd::where('branchIdFk', $userBranch->id)->max('date');
                    $currentMonth = Carbon::parse($lastMonthEndDate)->addDay()->endOfMonth()->format('Y-m-d');
                    $currentDate = Carbon::parse($currentMonth);
                   
                    // dd($currentDate);
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

                    $existFiscalYear = DB::table('gnr_fiscal_year')->where('companyId',$company->id)->where('name',$fyName)->first();
                    
                    if($existFiscalYear){
                        
                    }else{
                        $fyData['name'] = $fyName;
                        $fyData['companyId'] = $company->id;
                        $fyData['fyStartDate'] = $fyStartDate;
                        $fyData['fyEndDate'] = $fyEndDate;
                        $fyData['createdDate'] = $currentDate;
                        $fiscalYear = FiscalYear::create($fyData); 
                    }

                    //generate next year fiscal year
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
                    $existNextYearFiscalYear = DB::table('gnr_fiscal_year')->where('companyId',$company->id)->where('name',$fyName)->first();
                    //dd($existNextYearFiscalYear);
                   
                    if($existNextYearFiscalYear){
                        // dd('ok');
                    }else{
                        $fyData['name'] = $fyName;
                        $fyData['companyId'] = $company->id;
                        $fyData['fyStartDate'] = $fyStartDate;
                        $fyData['fyEndDate'] = $fyEndDate;
                        $fyData['createdDate'] = $currentDate;
                        //dd($fyData);
                        $fiscalYear = FiscalYear::create($fyData);
                    }
                        
                   
            }

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Year End Executed Successfully.<br/><b><em>Year: <u>'.$fiscalYearInfo->name.'</u><em/><b/>'
            );
        }
        // }
        // // All Month End not completed of this month
        // else{
        //
        //     $data = array(
        //         'responseTitle' =>  'Warning!',
        //         'responseText'  =>  'Please Execute All Month Ends of This Fiscal Year (<b><em><u>'.Carbon::parse($lastMonthEndDate)->format('Y').'</u><em/><b/>)'
        //     );
        // }

        return response::json($data);

    }

    public function deleteAccYearEndItem(Request $request){

        // dd($request->all());
        $yearEnd = AccYearEnd::find($request->id);
        $fiscalYearInfo = DB::table('gnr_fiscal_year')->where('companyId',$yearEnd->companyId)->where('fyEndDate', $yearEnd->endDate)->first();

        // check if there any month end after the year end date
        // $isMonthEndExists = AccMonthEnd::where('branchIdFk',$yearEnd->branchIdFk)->where('date','>',$yearEnd->date)->where('isMonthEnd',1)->value('id');
        //
        // if ($isMonthEndExists>0) {
        //     $data = array(
        //         'responseTitle' =>  'Warning!',
        //         'responseText'  =>  'Please Delete All Month Ends After Fiscal Year (<b><em><u>'.$fiscalYearInfo->name.'</u></b></em>)'
        //
        //     );
        //
        //     return response::json($data);
        // }

        // check if there any day end after the year end date
        $isMonthEndExists = AccMonthEnd::where('branchIdFk',$yearEnd->branchIdFk)->where('date','>',$yearEnd->date)->first();

        if ($isMonthEndExists) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Please Delete All Month Ends After Fiscal Year (<b><em><u>'.$fiscalYearInfo->name.'</u></b></em>)'

            );

            return response::json($data);
        }

        // check if there head office year end executed
        $headOfficeId = GnrBranch::where('companyId', Auth::user()->company_id_fk)->where('branchCode', 0)->value('id');
        if($request->branchId != $headOfficeId && Auth::user()->branchId != $headOfficeId){

            $isyearEndHOExists = AccYearEnd::where('branchIdFk', $headOfficeId)->where('endDate', $yearEnd->endDate)->count('id');
            // dd($isyearEndHOExists);

            if ($isyearEndHOExists > 0 && $isyearEndHOExists == 1) {

                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  "Please, Delete Head Office Year End First!"
                );

                return response::json($data);
            }

        }

        $accOpeningBalance = DB::table('acc_opening_balance')
                            ->where('branchId', $yearEnd->branchIdFk)
                            ->where('openingDate', $yearEnd->date)
                            ->delete();

        $yearEnd->delete();

        $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Year End Deleted Successfully.<br/><b><em>Year: <u>'.$fiscalYearInfo->name.'</u></b></em>'
        );

        return response::json($data);

    }

    public function checkYearEndToExecuteDayEnd($date, $branchId, $companyId){

        $currentfiscalYearInfo = DB::table('gnr_fiscal_year')
                          ->where('companyId', $companyId)
                          ->whereDate('fyStartDate', '<=', $date)
                          ->whereDate('fyEndDate', '>=', $date)
                          ->first();

        $currentFiscalYearStartDate = $currentfiscalYearInfo->fyStartDate;
        $previousFiscalYearEndDate = Carbon::parse($currentFiscalYearStartDate)->subDays(1)->format('Y-m-d');

        $lastYearEndInfo = DB::table('acc_year_end')
                          ->where('companyId', $companyId)
                          ->where('branchIdFk', $branchId)
                          ->orderBy('date', 'desc')
                          ->first();

        if($lastYearEndInfo){
            $lastYearEndDate = Carbon::parse($lastYearEndInfo->date)->format('Y-m-d');
            if($previousFiscalYearEndDate == $lastYearEndDate){
                return true;
            } else{
                return false;
            }
        }
        else {
            return false;
        }


    }

    public function getLedgerHeaderInfo($projectId, $branchId, $companyId){
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
            ->select('id')
            ->get();
            // dd($ledgers);

        return $ledgers;
    }

    public function getVoucherInfo($projectTypeId, $projectId, $branchId, $companyId, $yearStartDate, $yearEndDate){
        $voucherInfo = DB::table('acc_voucher')
                        ->join('acc_voucher_details','acc_voucher_details.voucherId', '=', 'acc_voucher.id')
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
                            'acc_voucher.projectTypeId'
                        )
                        ->get();

        return $voucherInfo;
    }

    public function manualYearEnd(){


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

        return view('accounting.process.yearEndProcess.massExecute', $data);
    }

    public function executeYearEnd(Request $req) {
        //dd($req->all());
        /////// manual year end execution //////
        $service = new Service;
        $branchIdArr = $req->filBranch;
        $fiscalYearId = $req->fiscalYear;
        $failedData = [];
        // dd($branchIdArr, $fiscalYearId);

        // foreach ($fiscalYears as $key => $value) {
            foreach ($branchIdArr as $key => $branch) {

                $array = $service->yearEndExecute($branch, $fiscalYearId);

                if (count($array) != 0) {
                    $failedData[] = $array;
                }

            }
        // }
        // dd($failedData);

        /////// manual year end execution //////
        return view('accounting.process.yearEndProcess.massExecuteReport', ['failedData' => $failedData, 'fiscalYearId' => $fiscalYearId]);

    }

}
