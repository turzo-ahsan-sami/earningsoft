<?php

namespace App\Http\Controllers\accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\accounting\AddVoucher;
use App\accounting\AddVoucherDetails;
use App\accounting\AddLedger;
use App\accounting\AddVoucherType;
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;
use App\gnr\GnrBranch;
use App\Service\EasyCode;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use App\Service\Service;


class AccBudgetController extends Controller {

    public function index(Request $req) {

        //dd($req->all());
        $userBranchId = Auth::user()->branchId;
        $userCompanyId = Auth::user()->company_id_fk;
        $userBranchStartDate = DB::table('gnr_branch')->where('id', $userBranchId)->value('aisStartDate');
        $branchDate = DB::table('acc_day_end')->max('date') != null
                    ? DB::table('acc_day_end')->max('date')
                    : $userBranchStartDate;
        $fiscalYears = DB::table('gnr_fiscal_year')
                    ->where('companyId',$userCompanyId)
                    ->where('fyStartDate', '>=', $branchDate)
                    ->orderByDesc('id')
                    ->pluck('name','id')
                    ->toArray();

        $projects =  ['Select project'] + DB::table('gnr_project')
                    ->where('companyId',$userCompanyId)
                    ->pluck(DB::raw("CONCAT(projectCode, ' - ', name) AS nameWithCode"), 'id')
                    ->toArray();
         //dd($projects);           
         $branchLists = ['All (With HO)']+ DB::table('gnr_branch')
                        ->where('companyId',$userCompanyId)
                        ->orderBy('branchCode')
                        ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                        ->toArray();
        //dd($branchLists,$fiscalYears);
        $accountTypes = DB::table('acc_account_type')->where('parentId', 0)->pluck('name','id')->toarray();
        //dd($accountTypes) ;         
        $projectSelected = $req->filProject;
        $fiscalYearSelected = $req->fiscalYear;
        $accountType = $req->filAccountType;
        $branchId = $req->branchId;
        
         $userBranchData = DB::table('gnr_branch')
                        ->where('id', $userBranchId)
                        ->select('id', DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"))
                        ->first();
        //dd($fiscalYearSelected);
        if ($req->checkFirstLoad == 1) {
            
            $budgets = DB::table('acc_budget')->where('projectId', $projectSelected)->where('fiscalYearId', $fiscalYearSelected)->where('accountType',$accountType)->where('branchId',$branchId)->paginate(20); 
            
        }
        else {
            $budgets = DB::table('acc_budget')->where('companyId',$userCompanyId)->paginate(20);
        }
    //dd($budgets);
        $data = array(
            'budgets'               => $budgets,
            'fiscalYears'           => $fiscalYears,
            'projects'              => $projects,
            'accountTypes'          => $accountTypes,
            'projectSelected'       => $projectSelected,
            'branchId'              => $branchId,
            'branchLists'           => $branchLists,
            'userBranchData'        => $userBranchData,
            'fiscalYearSelected'    => $fiscalYearSelected
        );
        //dd($data);

        return view('accounting.budgetViews.viewBudget', $data);
    }

    public function addBudget() {
        $userBranchId = Auth::user()->branchId;
        $userCompanyId = Auth::user()->company_id_fk;
        $userBranchStartDate = DB::table('gnr_branch')->where('id', $userBranchId)->value('aisStartDate');
        $branchDate = DB::table('acc_day_end')->max('date') != null
                    ? DB::table('acc_day_end')->max('date')
                    : $userBranchStartDate;
        $fiscalYears = DB::table('gnr_fiscal_year')
                    ->where('companyId',$userCompanyId)
                    ->where('fyStartDate', '>=', $branchDate)
                    ->select('name','id')->orderBy('id', 'desc')->get();

        $projects =  [''=>'Select project'] + DB::table('gnr_project')
        ->where('companyId',$userCompanyId)
        ->pluck(DB::raw("CONCAT(projectCode, ' - ', name) AS nameWithCode"), 'id')
        ->toArray();
        $projectTypes = DB::table('gnr_project_type')->pluck('name','id')->toarray();
        //dd($fiscalYears);
        //$accProjects = DB::table('acc_project')->select('name','id', 'projectCode')->get();
        $accLedgerTypes = DB::table('acc_account_type')->where('parentId', 0)->pluck('name','id')->toarray();
        //dd($accLedgerTypes);
         $branchLists = DB::table('gnr_branch')
                            ->where('id', $userBranchId)
                            ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                            ->toArray();
        $userBranchData = DB::table('gnr_branch')
                        ->where('id', $userBranchId)
                        ->select('id', DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"))
                        ->first();
                            //dd($projects,$projectTypes);
        $accBudgetArr = array(
            'branchLists'               => $branchLists,
            'userBranchData'               => $userBranchData,
            // 'currencyLists'             => $currencyLists,
            'userBranchId'              => $userBranchId,
            'projects'                  => $projects,
            'projectTypes'              => $projectTypes,
            //'accProjects'               => $accProjects,
            'accLedgerTypes'            => $accLedgerTypes,
            'fiscalYears'               => $fiscalYears,
            'userBranchStartDate'       => $userBranchStartDate,
            'branchDate'                => Carbon::parse($branchDate)->format('d-m-Y'),
        );
        // dd($accBudgetArr);

        return view('accounting.budgetViews.addBudgetFilterForm', $accBudgetArr);
    }

    public function checkBudgetItem(Request $request){
        //dd($request->all());
        $fiscalYearId = $request->fiscalYearId;
        $projectId = $request->projectId;
        $accountType = $request->accountType;
        $branchId = $request->branchId;
        $userCompanyId = Auth::user()->company_id_fk;


            $getAccBugget = DB::table('acc_budget')
                            ->where('fiscalYearId',$fiscalYearId)
                            ->where('projectId',$projectId)
                            ->where('accountType',$accountType)
                            ->where('branchId',$branchId)
                            ->where('companyId',$userCompanyId)
                            ->first();
            if($getAccBugget){
                  return response()->json('Already exists!',404);
              }else{
                 return response()->json();
              }
        //dd($getAccBugget);
    }

    
    public function loadBudget(Request $request) {
        //dd($request->all());
        $user_company_id = Auth::user()->company_id_fk;
        //dd($user_company_id);
        $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
        $userBranchId = Auth::user()->branchId;
        //dd($company);
        // collecting request data
        
        $projectId = $request->projectId;
        $accountType = $request->filAccountType;
        $fiscalYearId = (int) $request->fiscalYearId;
        $branchId = (int) $request->filBranch;
        //dd( $branchId);
        //$currencyId = (int) $request->filCurrency;
        if($projectId == " "){

        }
        // branch date
        $branchDate = DB::table('acc_day_end')->where('branchIdFk', $userBranchId)->max('date');
        $branchDate = $branchDate == null ? DB::table('gnr_branch')->where('id', $userBranchId)->where('companyId',$user_company_id)->value('aisStartDate') : $branchDate;
        //dd($branchDate);

        $currentFiscalYear = DB::table('gnr_fiscal_year')
                            ->where('companyId',$user_company_id)
                             ->where('fyStartDate', '>=', $branchDate)
                            ->select('id','name', 'fyStartDate', 'fyEndDate')
                            ->first();
                            //dd($userBranchId);

        // collecting date range
        $dateFrom = DB::table('gnr_fiscal_year')->where('id', $currentFiscalYear->id)->value('fyStartDate');
        $fyStartDate = $dateFrom;
        $dateTo = DB::table('gnr_fiscal_year')->where('id', $currentFiscalYear->id)->value('fyEndDate');
        $lastOpeningDate = Carbon::parse($fyStartDate)->subdays(1)->format('Y-m-d');
        //dd($fyStartDate, $lastOpeningDate);

        $accountTypeName = DB::table('acc_account_type')->where('id', $accountType)->value('name');
        $fiscalYearName = DB::table('gnr_fiscal_year')->where('id', $fiscalYearId)->value('name');
        //$branchName = DB::table('gnr_branch')->where('companyId',$user_company_id)->where('id', $branchId)->value('name');
        $branchName = DB::table('gnr_branch')->where('id', $request->filBranch)->value(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', TRIM(REPLACE(name, '\t', '')))"));
        //dd($request->filBranch);
       // dd($branchName);
        $projectName = DB::table('gnr_project')->where('id', $projectId)->where('companyId',$user_company_id)->value(DB::raw("CONCAT(LPAD(projectCode, 3, 0), ' - ', name)"));
        //dd($projectName);

        //collecting fiscal month

         $fiscalYear = DB::table('gnr_fiscal_year')
                    ->where('id', $fiscalYearId)
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

        //dd($branchId);
        // requested info array
        $loadBudgetTableArr = array(
            'company'                 => $company,
            'fiscalYearId'            => $fiscalYearId,
            'projectId'               => $projectId,
            'fiscalYearName'          => $fiscalYearName,
            // 'dateFrom'                => $dateFrom,
            // 'dateTo'                  => $dateTo,
            'projectName'             => $projectName,
            'branchName'             => $branchName,
            'branchId'                 => $branchId,
            'accountTypeName'         => $accountTypeName,
            'accountType'             => $accountType,
            'monthsArr'             => $monthsArr
          
        );
      
      //dd($loadBudgetTableArr);

        // ledgers variable and collection
        $service = new Service;
        $ledgersCollection = DB::table('acc_account_ledger')->where('companyIdFk', $user_company_id)->where('isGroupHead', 0);
        //dd($ledgersCollection);

        if ($accountType != null) {
            $accountTypeIdArr = [$accountType => (int)$accountType] + DB::table('acc_account_type')->where('parentId', $accountType)->pluck('id', 'id')->toArray();
            $ledgersCollection = $ledgersCollection->whereIn('accountTypeId', $accountTypeIdArr);
        }

        $ledgersCollection = $ledgersCollection
                            ->select('id', 'name', 'code', 'accountTypeId', 'isGroupHead', 'level', 'parentId')
                            ->orderBy('code')
                            ->get();
        //dd($ledgersCollection);

        $finalLevelLedgerIdArr  = $ledgersCollection->pluck('id')->toArray();

        // final level ledgers opening balance informations
        $finalLevelLedgersOpData = DB::table('acc_account_ledger')
                                ->leftJoin('acc_opening_balance', 'acc_opening_balance.ledgerId', '=', 'acc_account_ledger.id')
                                //->where('acc_opening_balance.projectId', $projectId)
                                ->whereIn('acc_account_ledger.id', $finalLevelLedgerIdArr)
                                ->where('acc_opening_balance.openingDate', $lastOpeningDate);
                                
                               
        //project filter
        if ((int)$projectId != 0) {
            $finalLevelLedgersOpData->where('acc_opening_balance.projectId', $projectId);
        }

        $finalLevelLedgersOpData = $finalLevelLedgersOpData
                                    ->select('acc_account_ledger.id',
                                            'acc_account_ledger.name',
                                            'acc_account_ledger.level',
                                            'acc_account_ledger.code',
                                            'acc_account_ledger.parentId',
                                            'acc_account_ledger.isGroupHead',
                                            DB::raw('SUM(acc_opening_balance.debitAmount) as debitAmount'),
                                            DB::raw('SUM(acc_opening_balance.creditAmount) as creditAmount')
                                        )
                                    ->groupBy('acc_account_ledger.id')
                                    ->get()->keyBy('id')->toArray();
                                    // dd($finalLevelLedgersOpData);

        // current period voucher informations
        // current period voucher debit
        $currentPeriodVoucherDebitInfo = DB::table('acc_voucher')
                                        ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                        ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.debitAcc', $finalLevelLedgerIdArr);
                                         

                                        
        // project filter
        if ((int)$projectId != 0) { 
            $currentPeriodVoucherDebitInfo->where('acc_voucher.projectId', $projectId);
        }
        // // currency filter
        // if ($currencyId == 0) {
            $currentPeriodVoucherDebitInfo = $currentPeriodVoucherDebitInfo
                                            ->groupBy('acc_voucher_details.debitAcc')
                                            ->select(
                                                'acc_voucher_details.debitAcc as ledgerId',
                                                DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                            )
                                            ->get()->keyBy('ledgerId')->toArray();
                                            //dd($currentPeriodVoucherDebitInfo);
        // current period voucher credit
        $currentPeriodVoucherCreditInfo = DB::table('acc_voucher')
                                        ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')                           
                                        ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                        ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.creditAcc', $finalLevelLedgerIdArr);
                                        
                                        
                                        
        // project filter
        if ((int)$projectId != null) {
            $currentPeriodVoucherCreditInfo->where('acc_voucher.projectId', $projectId);
        }
        // // currency filter
        // if ($currencyId == 0) {
            $currentPeriodVoucherCreditInfo = $currentPeriodVoucherCreditInfo
                                            ->groupBy('acc_voucher_details.creditAcc')
                                            ->select(
                                                'acc_voucher_details.creditAcc as ledgerId',
                                                DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                                            )
                                            ->get()->keyBy('ledgerId')->toArray();

                                            //dd($currentPeriodVoucherCreditInfo);

        $currentYearBudget = DB::table('acc_budget')
                                ->leftJoin('acc_budget_details', 'acc_budget.id', '=', 'acc_budget_details.budgetId')
                                ->whereIn('acc_budget_details.ledgerId', $finalLevelLedgerIdArr)
                                ->where('acc_budget.fiscalYearId', $fiscalYearId)
                                ->where('acc_budget.projectId', $projectId)
                                ->select('acc_budget_details.ledgerId as ledgerId',
                                    'acc_budget_details.debitAmount',
                                    'acc_budget_details.creditAmount'
                                )
                                ->groupBy('acc_budget_details.ledgerId')
                                ->get()->keyBy('ledgerId')->toArray();
                                //dd($currentYearBudget);

        // loop running after collecting all info
        $totalBalance = ['debit' => 0, 'credit' => 0, 'budgetDebit' => 0, 'budgetCredit' => 0];

        $ledgerWiseData = [];

        if($ledgersCollection){
            foreach ($ledgersCollection as $key => $singleLedgerData) {

                $ledgerType = Service::checkLedgerAccountType($singleLedgerData->id);

                // // opening balance
                $openingDebitAmount = isset($finalLevelLedgersOpData[$singleLedgerData->id])
                                        ? $finalLevelLedgersOpData[$singleLedgerData->id]->debitAmount
                                        : 0;
                $openingCreditAmount = isset($finalLevelLedgersOpData[$singleLedgerData->id])
                                        ? $finalLevelLedgersOpData[$singleLedgerData->id]->creditAmount
                                        : 0;

                // balance during current period
                $currentPeriodDebitAmount = 0;
                $currentPeriodCreditAmount = 0;
                if (isset($currentPeriodVoucherDebitInfo[$singleLedgerData->id])) {
                    $currentPeriodDebitAmount += $currentPeriodVoucherDebitInfo[$singleLedgerData->id]->debitAmount;
                }

                if (isset($currentPeriodVoucherCreditInfo[$singleLedgerData->id])) {
                    $currentPeriodCreditAmount += $currentPeriodVoucherCreditInfo[$singleLedgerData->id]->creditAmount;
                }

                // cumulative
                $debitAmount = $openingDebitAmount + $currentPeriodDebitAmount;
                $creditAmount = $openingCreditAmount + $currentPeriodCreditAmount;
                //dd($debitAmount,$creditAmount);
                // balance calculation
                if ($ledgerType == true) {
                    $debitBalance = $debitAmount - $creditAmount;
                    $creditBalance = 0;

                    if ($debitBalance < 0) {
                        $creditBalance = abs($debitBalance);
                        $debitBalance = 0;
                    }
                }
                elseif($ledgerType == false) {
                    $creditBalance = $creditAmount - $debitAmount;
                    $debitBalance = 0;

                    if ($creditBalance < 0) {
                        $debitBalance = abs($creditBalance);
                        $creditBalance = 0;
                    }
                }
                // end of balance calculation

                $totalBalance['debit']  += $debitBalance;
                $totalBalance['credit'] += $creditBalance;

                // budget amounts
                $budgetDebitAmount = isset($currentYearBudget[$singleLedgerData->id])
                                    ?$currentYearBudget[$singleLedgerData->id]->debitAmount
                                    : 0;
                $budgetCreditAmount = isset($currentYearBudget[$singleLedgerData->id])
                                        ? $currentYearBudget[$singleLedgerData->id]->creditAmount
                                        : 0;

                $totalBalance['budgetDebit'] += $budgetDebitAmount;
                $totalBalance['budgetCredit'] += $budgetCreditAmount;

                // final ledger information array
                $ledgerWiseData[] = array(
                    'id'                            => $singleLedgerData->id,
                    'code'                          => $singleLedgerData->code,
                    'name'                          => $singleLedgerData->name,
                    'isGroupHead'                   => $singleLedgerData->isGroupHead,
                    'parentId'                      => $singleLedgerData->parentId,
                    'level'                         => $singleLedgerData->level,
                    'debitBalance'                  => $debitBalance,
                    'creditBalance'                 => $creditBalance,
                    'budgetDebitAmount'             => $budgetDebitAmount,
                    'budgetCreditAmount'            => $budgetCreditAmount,
                   
                );

            }
        }else{
            $ledgerWiseData = '';
        }
        

        // structuring data to send information in view file
        // $data = array(
        //     'loadBudgetTableArr'        => $loadBudgetTableArr,
        //     'ledgerWiseData'            => collect($ledgerWiseData),
        //     'totalBalance'              => $totalBalance
        // );
        // $a = number_format($totalBalance['debit'], 2);
        // dd($a);

        return view('accounting.budgetViews.addBudgetTable', [

           'loadBudgetTableArr'        => $loadBudgetTableArr,
            'ledgerWiseData'            => collect($ledgerWiseData),
            'totalBalance'              => $totalBalance
        ]);
    }


    public function addBudgetItem(Request $request) {
       //dd($request->all());
        $requestData = $request->all();
        $fiscalYearId = $request->fiscalYearId;
        $projectId = $request->projectId;
        $accountType = $request->accountType;
        $branchId = $request->branchId;
        $userCompanyId = Auth::user()->company_id_fk;
        //dd(json_decode($request->month));
           
            
            $getAccBugget = DB::table('acc_budget')
                            ->where('fiscalYearId',$fiscalYearId)
                            ->where('projectId',$projectId)
                            ->where('accountType',$accountType)
                            ->where('branchId',$branchId)
                            ->where('companyId',$userCompanyId)
                            ->first();
            if($getAccBugget){
               return response()->json();
               //return back();
            }else{
                DB::table('acc_budget')->insert([
                    'fiscalYearId'  => $fiscalYearId,
                    'accountType'   => $accountType,
                    'branchId'      => $branchId,
                    'projectId'     => $projectId,
                    'companyId'     => $userCompanyId
                    ]);   
           
                $lastledgerId = DB::table('acc_budget')->max('id');
               // dd($lastledgerId);
                $dataArray = [];
               //Starting  Asset,liability accounttype
                if($request->accountType == 1 || $request->accountType == 6 || $request->accountType == 9){
                    foreach (json_decode($request->debit) as $key => $value) {
                        $ledgerId= explode('-', $key)[1];
                        $dataArray[$ledgerId]['ledgerId'] = $ledgerId;
                        $dataArray[$ledgerId]['debit'] = (double) $value; 
                    }
                //loop fo r debit 
                    foreach (json_decode($request->credit) as $key => $value) {
                        //dd( $value);
                        $ledgerId = explode('-', $key)[1];
                        $dataArray[$ledgerId]['ledgerId'] = $ledgerId;
                        $dataArray[$ledgerId]['credit'] = (double) $value;           
                    }

                    foreach ($dataArray as $key => $data) {

                        if($data['debit'] != 0 && $data['credit'] != 0){
                            DB::table('acc_budget_details')->insert([
                                'budgetId'      =>$lastledgerId,
                                'ledgerId'      =>$data['ledgerId'],
                                'debitAmount'   => $data['debit'],
                                'creditAmount'  => $data['credit']
                            ]);
                         }
                    }
                }elseif($request->accountType == 12){
                    foreach (json_decode($request->month) as $key => $value) {
                            $ledgerId = explode('-', $key)[1];
                            $dataArray[$ledgerId]['ledgerId'] = $ledgerId;
                            $dataArray[$ledgerId]['month'] = json_encode($value);           
                    }
                        //loop for total
                        foreach (json_decode($request->total) as $key => $value) {
                            $ledgerId = explode('-', $key)[1];
                            $dataArray[$ledgerId]['ledgerId'] = $ledgerId;
                            $dataArray[$ledgerId]['total'] = $value; 
                                 
                        }
                            foreach ($dataArray as $key => $data) {
                                //dd($data);
                                if($data['total'] != 0){
                                    DB::table('acc_budget_details')->insert([
                                        'budgetId'      =>$lastledgerId,
                                        'ledgerId'      =>$data['ledgerId'],
                                        'creditAmount'  => $data['total'],
                                        'month'   => $data['month'],
                                    ]);
                               }
                             }
                }else{
                    foreach (json_decode($request->month) as $key => $value) {
                            $ledgerId = explode('-', $key)[1];
                            $dataArray[$ledgerId]['ledgerId'] = $ledgerId;
                            $dataArray[$ledgerId]['month'] = json_encode($value);           
                    }
                        //loop for total
                        foreach (json_decode($request->total) as $key => $value) {
                            $ledgerId = explode('-', $key)[1];
                            $dataArray[$ledgerId]['ledgerId'] = $ledgerId;
                            $dataArray[$ledgerId]['total'] = $value; 
                                 
                        }
                         
                            foreach ($dataArray as $key => $data) {
                                //dd($data);
                                if($data['total'] != 0){
                                    DB::table('acc_budget_details')->insert([
                                        'budgetId'      =>$lastledgerId,
                                        'ledgerId'      =>$data['ledgerId'],
                                        'debitAmount'  => $data['total'],
                                        'month'   => $data['month'],
                                    ]);
                                }
                             } 
                }
                     
                

                return response()->json('Budget saved succesfully!');
 
            }
                
           

        

       
    }

    public function editBudget($id) {
        //dd($id);
        $user_company_id = Auth::user()->company_id_fk;
        $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
        $userBranchId = Auth::user()->branchId;

        // branch date
        $branchDate = DB::table('acc_day_end')->where('branchIdFk', $userBranchId)->max('date');
        $branchDate = $branchDate == null ? DB::table('gnr_branch')->where('id', $userBranchId)->value('aisStartDate') : $branchDate;
        // dd($branchDate);

        $currentFiscalYear = DB::table('gnr_fiscal_year')
                            ->where('fyStartDate', '>=', $branchDate)
                            ->select('id','name', 'fyStartDate', 'fyEndDate')
                            ->first();

        // collecting date range
        $dateFrom = DB::table('gnr_fiscal_year')->where('id', $currentFiscalYear->id)->value('fyStartDate');
        $fyStartDate = $dateFrom;
        $dateTo = DB::table('gnr_fiscal_year')->where('id', $currentFiscalYear->id)->value('fyEndDate');
        $lastOpeningDate = Carbon::parse($fyStartDate)->subdays(1)->format('Y-m-d');

        $budgetInfo = DB::table('acc_budget')->where('id', $id)->first();
        $projectId = $budgetInfo->projectId;
        $branchId = $budgetInfo->branchId;
        $fiscalYearId = $budgetInfo->fiscalYearId;
        $accountTypeId = $budgetInfo->accountType;
        //$months = $budgetInfo->month;
        $fiscalYearName = DB::table('gnr_fiscal_year')->where('id', $fiscalYearId)->value('name');
        $accountTypeName = DB::table('acc_account_type')->where('id', $accountTypeId)->value('name');
        $accountType = DB::table('acc_account_type')->where('id', $accountTypeId)->value('id');
        $totalMonths = DB::table('acc_account_type')->where('id', $accountTypeId)->value('id');
        $branchName = DB::table('gnr_branch')->where('id', $branchId)->value('name');
        //dd($budgetInfo);

        $projectName = DB::table('gnr_project')->where('id', $projectId)->value(DB::raw("CONCAT(LPAD(projectCode, 3, 0), ' - ', name)"));
        $fiscalYear = DB::table('gnr_fiscal_year')
                ->where('id', $fiscalYearId)
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

       
        
    
       
        // requested info array
        $loadBudgetTableArr = array(
            'company'                => $company,
            'fiscalYearId'           => $fiscalYearId,
            'projectId'              => $projectId,
            'fiscalYearName'         => $fiscalYearName,
            'accountTypeName'        => $accountTypeName,
            'accountType'            => $accountType,
            'branchName'             => $branchName,
            'projectName'            => $projectName,
            'monthsArr'              => $monthsArr,
          
        );
       //dd($accountType); 
        $ledgerWiseData = [];
        // ledgers variable and collection
        $service = new Service;
         $ledgersCollection = DB::table('acc_account_ledger')->where('companyIdFk', $user_company_id)->where('isGroupHead', 0);

        if ($accountType != null) {
            $accountTypeIdArr = [$accountType => (int)$accountType] + DB::table('acc_account_type')->where('parentId', $accountType)->pluck('id', 'id')->toArray();
            $ledgersCollection = $ledgersCollection->whereIn('accountTypeId', $accountTypeIdArr);
        }
         $ledgersCollection = $ledgersCollection
                            ->select('id', 'name', 'code', 'accountTypeId', 'isGroupHead', 'level', 'parentId')
                            ->orderBy('code')
                            ->get();

       // dd( $ledgersCollection);
        //dd($ledgersCollection);
        // if($ledgersCollection){
           
        // }else{
        //      $ledgersCollection = '';
        // }

                           // dd($ledgersCollection);
        // if($accountType == 1 || $accountType == 6 || $accountType == 9){
           
        //                     dd($ledgersCollection); 

        // }elseif($accountType == 12){
        //     $ledgersCollection = DB::table('acc_account_ledger')
        //                     ->where('accountTypeId', $accountType)
        //                     ->where('isGroupHead', 0)
        //                     ->select('id', 'name', 'code', 'accountTypeId', 'isGroupHead', 'level', 'parentId')
        //                     ->orderBy('code')
        //                     ->get();

        // }else{
        //     $ledgersCollection = DB::table('acc_account_ledger')
        //                     ->where('accountTypeId', $accountType)
        //                     ->where('isGroupHead', 0)
        //                     ->select('id', 'name', 'code', 'accountTypeId', 'isGroupHead', 'level', 'parentId')
        //                     ->orderBy('code')
        //                     ->get();   
        // }
       
                            //dd($ledgersCollection);

        $finalLevelLedgerIdArr  = $ledgersCollection->pluck('id')->toArray();

        // final level ledgers opening balance informations
        $finalLevelLedgersOpData = DB::table('acc_account_ledger')
                                ->leftJoin('acc_opening_balance', 'acc_opening_balance.ledgerId', '=', 'acc_account_ledger.id')
                                ->whereIn('acc_account_ledger.id', $finalLevelLedgerIdArr)
                                ->where('acc_opening_balance.openingDate', $lastOpeningDate);

        // project filter
        if ((int)$projectId != 0) {
            $finalLevelLedgersOpData->where('acc_opening_balance.projectId', $projectId);
        }
        // currency filter
            $finalLevelLedgersOpData = $finalLevelLedgersOpData
                                        ->select('acc_account_ledger.id',
                                            'acc_account_ledger.name',
                                            'acc_account_ledger.level',
                                            'acc_account_ledger.code',
                                            'acc_account_ledger.parentId',
                                            'acc_account_ledger.isGroupHead',
                                            DB::raw('SUM(acc_opening_balance.debitAmount) as debitAmount'),
                                            DB::raw('SUM(acc_opening_balance.creditAmount) as creditAmount')
                                        )
                                        ->groupBy('acc_account_ledger.id')
                                        ->get()->keyBy('id')->toArray();
                                        //dd($finalLevelLedgersOpData);
        
        // current period voucher debit
       $currentPeriodVoucherDebitInfo = DB::table('acc_voucher')
                                        ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                        ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.debitAcc', $finalLevelLedgerIdArr);
                                         

                                        
        // project filter
        if ((int)$projectId != 0) { 
            $currentPeriodVoucherDebitInfo->where('acc_voucher.projectId', $projectId);
        }
        // // currency filter
        // if ($currencyId == 0) {
        $currentPeriodVoucherDebitInfo = $currentPeriodVoucherDebitInfo
                                        ->groupBy('acc_voucher_details.debitAcc')
                                        ->select(
                                            'acc_voucher_details.debitAcc as ledgerId',
                                            DB::raw('SUM(acc_voucher_details.amount) as debitAmount')
                                        )
                                        ->get()->keyBy('ledgerId')->toArray();
                                       

        // current period voucher credit
        $currentPeriodVoucherCreditInfo = DB::table('acc_voucher')
                                        ->join('acc_voucher_details', 'acc_voucher.id', '=', 'acc_voucher_details.voucherId')
                                        ->where('acc_voucher.voucherDate', '>=', $dateFrom)
                                        ->where('acc_voucher.voucherDate', '<=', $dateTo)
                                        ->whereIn('acc_voucher_details.creditAcc', $finalLevelLedgerIdArr);
                                         //dd($currentPeriodVoucherCreditInfo);
        // project filter
        if ((int)$projectId != 0) { 
            $currentPeriodVoucherCreditInfo->where('acc_voucher.projectId', $projectId);
        }
        //dd($currentPeriodVoucherCreditInfo);     
        // currency filter
        // if ($currencyId == 0) {
        $currentPeriodVoucherCreditInfo = $currentPeriodVoucherCreditInfo
                                        ->groupBy('acc_voucher_details.creditAcc')
                                        ->select(
                                            'acc_voucher_details.creditAcc as ledgerId',
                                            DB::raw('SUM(acc_voucher_details.amount) as creditAmount')
                                        )
                                        ->get()->keyBy('ledgerId')->toArray();    
                                    
        

        $currentYearBudget = DB::table('acc_budget')
                                ->leftJoin('acc_budget_details', 'acc_budget.id', '=', 'acc_budget_details.budgetId')
                                ->whereIn('acc_budget_details.ledgerId', $finalLevelLedgerIdArr)
                                ->where('acc_budget.id', $id);
        // currency filter
        // if ($currencyId == 0) {
            $currentYearBudget = $currentYearBudget
                                ->select('acc_budget_details.ledgerId as ledgerId',
                                    'acc_budget_details.debitAmount',
                                    'acc_budget_details.creditAmount',
                                    'month'
                                )
                                ->groupBy('acc_budget_details.ledgerId')
                                ->get()->keyBy('ledgerId')->toArray();
                               
            //dd($currentYearBudget);
        

        // loop running after collecting all info
        $totalBalance = ['debit' => 0, 'credit' => 0, 'budgetDebit' => 0, 'budgetCredit' => 0];

        foreach ($ledgersCollection as $key => $singleLedgerData) {
            //dd($singleLedgerData);
            $ledgerType = Service::checkLedgerAccountType($singleLedgerData->id);

            // // opening balance
            $openingDebitAmount = isset($finalLevelLedgersOpData[$singleLedgerData->id])
                                    ? $finalLevelLedgersOpData[$singleLedgerData->id]->debitAmount
                                    : 0;
            $openingCreditAmount = isset($finalLevelLedgersOpData[$singleLedgerData->id])
                                    ? $finalLevelLedgersOpData[$singleLedgerData->id]->creditAmount
                                    : 0;

            // balance during current period
            $currentPeriodDebitAmount = 0;
            $currentPeriodCreditAmount = 0;
            if (isset($currentPeriodVoucherDebitInfo[$singleLedgerData->id])) {
                $currentPeriodDebitAmount += $currentPeriodVoucherDebitInfo[$singleLedgerData->id]->debitAmount;
            }

            if (isset($currentPeriodVoucherCreditInfo[$singleLedgerData->id])) {
                $currentPeriodCreditAmount += $currentPeriodVoucherCreditInfo[$singleLedgerData->id]->creditAmount;
            }

            // cumulative
            $debitAmount = $openingDebitAmount + $currentPeriodDebitAmount;
            $creditAmount = $openingCreditAmount + $currentPeriodCreditAmount;
            
            //dd($creditAmount);
            // balance calculation
            if ($ledgerType == true) {
                $debitBalance = $debitAmount - $creditAmount;
                $creditBalance = 0;

                if ($debitBalance < 0) {
                    $creditBalance = abs($debitBalance);
                    $debitBalance = 0;
                }
            }
            elseif($ledgerType == false) {
                $creditBalance = $creditAmount - $debitAmount;
                $debitBalance = 0;

                if ($creditBalance < 0) {
                    $debitBalance = abs($creditBalance);
                    $creditBalance = 0;
                }
            }
            // end of balance calculation

            $totalBalance['debit'] += $debitBalance;
            $totalBalance['credit'] += $creditBalance;

            // budget amounts
            $budgetDebitAmount = isset($currentYearBudget[$singleLedgerData->id])
                                ?$currentYearBudget[$singleLedgerData->id]->debitAmount
                                : 0;
            $budgetCreditAmount = isset($currentYearBudget[$singleLedgerData->id])
                                    ? $currentYearBudget[$singleLedgerData->id]->creditAmount
                                    : 0;
           
          
            $monthsData = isset($currentYearBudget[$singleLedgerData->id]) ? json_decode($currentYearBudget[$singleLedgerData->id]->month) : [];
           
          
            // final ledger information array
            $ledgerWiseData[] = array(
                'id'                            => $singleLedgerData->id,
                'code'                          => $singleLedgerData->code,
                'name'                          => $singleLedgerData->name,
                'isGroupHead'                   => $singleLedgerData->isGroupHead,
                'parentId'                      => $singleLedgerData->parentId,
                'level'                         => $singleLedgerData->level,
                'debitBalance'                  => $debitBalance,
                'creditBalance'                 => $creditBalance,
                'budgetDebitAmount'             => $budgetDebitAmount,
                'budgetCreditAmount'            => $budgetCreditAmount,
                'monthsData'                     => $monthsData, 
                'budgetCreditAmount'            => $budgetCreditAmount,
            );

        }
        
     
        //dd($ledgerWiseData);
        // structuring data to send information in view file
        $data = array(
            'budgetInfo'                => $budgetInfo,
            'loadBudgetTableArr'        => $loadBudgetTableArr,
            'ledgerWiseData'            => collect($ledgerWiseData),
            'totalBalance'              => $totalBalance
        );
        //dd($data);

        return view('accounting.budgetViews.editBudgetTable', $data);
    }

   public function editBudgetItem(Request $request) {
        //dd($request->all());
        $requestData = $request->all();
        $budgetId = $requestData['budgetId'];
        $checkAccountTypeId = DB::table('acc_budget')
                                ->where('id', $budgetId)
                                ->first();
       //dd($checkAccountTypeId);
        $dataArray = [];
        if($checkAccountTypeId->accountType == 1 || $checkAccountTypeId->accountType == 6 || $checkAccountTypeId->accountType == 9){
            foreach (json_decode($request->debit) as $key => $value) {
                $ledgerId= explode('-', $key)[1];
                 //dd($key);
                $dataArray[$ledgerId]['ledgerId'] = $ledgerId;
                $dataArray[$ledgerId]['debit'] = $value; 
            }
        //loop fo r debit 
            foreach (json_decode($request->credit) as $key => $value) {
                //dd( $value);
                $ledgerId = explode('-', $key)[1];
                $dataArray[$ledgerId]['ledgerId'] = $ledgerId;
                $dataArray[$ledgerId]['credit'] = $value;           
            }
          // dd( $dataArray);

            foreach ($dataArray as $key => $data) {  
                //dd($data);
               $checkLedgerExists = DB::table('acc_budget_details')
                                ->where('budgetId', $budgetId)
                                ->where('ledgerId', $data['ledgerId'])
                                ->first();
                 //dd($checkLedgerExists);
                if($checkLedgerExists){
                   
                    if($data['debit'] != 0 && $data['credit'] != 0){
                        DB::table('acc_budget_details')
                        ->where('budgetId', $budgetId)
                        ->where('ledgerId', $data['ledgerId'])
                        ->update([ 
                            'debitAmount'   => $data['debit'],
                            'creditAmount'  => $data['credit'],
                        ]);
                    }
                }else{
                     if($data['debit'] != 0 && $data['credit'] != 0){
                        DB::table('acc_budget_details')->insert([
                            'budgetId'      =>$budgetId,
                            'ledgerId'      =>$data['ledgerId'],
                            'debitAmount'   =>$data['debit'],
                            'creditAmount'  =>$data['credit']
                        ]);
                   } 
                }
            }     
        }elseif($checkAccountTypeId->accountType == 12){
            foreach (json_decode($request->month) as $key => $value) {
                $ledgerId = explode('-', $key)[1];
                $dataArray[$ledgerId]['ledgerId'] = $ledgerId;
                $dataArray[$ledgerId]['month'] = json_encode($value);           
            }
            //loop for total
            foreach (json_decode($request->total) as $key => $value) {
                $ledgerId = explode('-', $key)[1];
                $dataArray[$ledgerId]['ledgerId'] = $ledgerId;
                $dataArray[$ledgerId]['total'] = (double) $value; 
                     
            }
          //dd($dataArray);
            foreach ($dataArray as $key => $data) {  
                //dd($data);
               $checkLedgerExists = DB::table('acc_budget_details')
                                ->where('budgetId', $budgetId)
                                ->where('ledgerId', $data['ledgerId'])
                                ->first();
                if($checkLedgerExists){
                   
                    if($data['total'] != 0){
                        DB::table('acc_budget_details')
                        ->where('budgetId', $budgetId)
                        ->where('ledgerId', $data['ledgerId'])
                        ->update([ 
                            'creditAmount'  => $data['total'],
                            'month'         => $data['month'],
                        ]);
                    }
                }else{
                    if($data['total'] != 0){
                        DB::table('acc_budget_details')->insert([
                            'budgetId'      =>$budgetId,
                            'ledgerId'      =>$data['ledgerId'],
                            'creditAmount'  =>$data['total'],
                            'month'         =>$data['month']
                        ]);
                   } 
                }
            }      
    }elseif($checkAccountTypeId->accountType == 13){
        foreach (json_decode($request->month) as $key => $value) {
            $ledgerId = explode('-', $key)[1];
            $dataArray[$ledgerId]['ledgerId'] = $ledgerId;
            $dataArray[$ledgerId]['month'] = json_encode($value);           
        }
                        //loop for total
        foreach (json_decode($request->total) as $key => $value) {
            $ledgerId = explode('-', $key)[1];
            $dataArray[$ledgerId]['ledgerId'] = $ledgerId;
            $dataArray[$ledgerId]['total'] = (double) $value; 
                 
        }

        foreach ($dataArray as $key => $data) { 
            //dd($data);
            $checkLedgerExists = DB::table('acc_budget_details')
                                    ->where('budgetId', $budgetId)
                                    ->where('ledgerId', $data['ledgerId'])
                                    ->first();
                if($checkLedgerExists){
                    if($data['total'] != 0){
                        DB::table('acc_budget_details')
                        ->where('budgetId', $budgetId)
                        ->where('ledgerId', $data['ledgerId'])
                        ->update([ 
                            'debitAmount'   => $data['total'],
                            'month'         => $data['month'],
                        ]);
                    }
                }else{
                    if($data['total'] != 0){
                        DB::table('acc_budget_details')->insert([
                            'budgetId'      =>$budgetId,
                            'ledgerId'      =>$data['ledgerId'],
                            'debitAmount'  =>$data['total'],
                            'month'         =>$data['month']
                        ]);
                   } 
                }
        }
                     
    }
        // unset($requestData['_token'], $requestData['budgetId']);
        // // dd($requestData);
        // foreach ($requestData as $key => $value) {

        //     $arr = explode('-', $key);
        //     $ledgerId = (int)$arr[1];

        //     if (isset($dataArray[$ledgerId])) {

        //         if ($arr[0] == 'debit') {
        //             $dataArray[$ledgerId]['debitBalance'] = (double) $value;
        //         }
        //         elseif ($arr[0] == 'credit') {
        //             $dataArray[$ledgerId]['creditBalance'] = (double) $value;
        //         }

        //     }
        //     else {

        //         $dataArray[$ledgerId]['ledgerId'] = $ledgerId;

        //         if ($arr[0] == 'debit') {
        //             $dataArray[$ledgerId]['debitBalance'] = (double) $value;
        //         }
        //         elseif ($arr[0] == 'credit') {
        //             $dataArray[$ledgerId]['creditBalance'] = (double) $value;
        //         }

        //     }

        // } // loop close
        // // dd($dataArray);

        // foreach ($dataArray as $key => $data) {

        //     $checkLedgerExists = DB::table('acc_budget_details')
        //                         ->where('budgetId', $budgetId)
        //                         ->where('ledgerId', $data['ledgerId'])
        //                         ->first();
        //     // check ledger existence
        //     if ($checkLedgerExists) { // if exist then update
        //         DB::table('acc_budget_details')
        //         ->where('budgetId', $budgetId)
        //         ->where('ledgerId', $data['ledgerId'])
        //         ->update([
        //             'debitAmount'   => $data['debitBalance'],
        //             'creditAmount'  => $data['creditBalance']
        //         ]);
        //     }
        //     else { // insert new ledger data for same budget
        //         DB::table('acc_budget_details')
        //         ->insert([
        //             'budgetId'      => $budgetId,
        //             'ledgerId'      => $data['ledgerId'],
        //             'debitAmount'   => $data['debitBalance'],
        //             'creditAmount'  => $data['creditBalance']
        //         ]);
        //     }

        // }

        return response()->json('Budget updated succesfully!');
    }


    public function deleteItem(Request $req) {
        $budgetId = $req->id;
        $checkRevisedBudgetExists = DB::table('acc_budget_revised')->where('budgetId', $budgetId)->count();
        //dd($checkRevisedBudgetExists);
        if($checkRevisedBudgetExists > 0){
            return response()->json('Revised Budget Exists!');
        }else{
            DB::table('acc_budget_details')->where('budgetId', $budgetId)->delete();
            DB::table('acc_budget')->where('id', $budgetId)->delete();

            return response()->json('Budget deleted succesfully!');   
        }
       

    }

    public function approveBudgetItem(Request $request){
       //dd( $request->all());
       DB::table('acc_budget')->where('id', (int)$request['budgetId'])->update(['status'=>1]);
       //dd($budget);
        return response()->json('Budget approved succesfully!');
    }

    public function getBranchByProjectId(Request $request){
        if($request->projectId==""){
            $branchList =  DB::table('gnr_branch')->whereNotIn('id', [1])->where('companyId',Auth::user()->company_id_fk)->select('id','name','branchCode')->get();
            $projectTypeList = 'All';
        }
    else{
        $branchList =  DB::table('gnr_branch')->where('projectId',(int)json_decode($request->projectId))->whereNotIn('id', [1])->where('companyId', Auth::user()->company_id_fk)->select('id','name','branchCode')->get();
        $projectTypeList =  DB::table('gnr_project_type')->where('companyId',Auth::user()->company_id_fk)->where('projectId',(int)json_decode($request->projectId))->select('id','name','projectTypeCode')->get();
    }

    $data = array(
        'branchList' => $branchList,
        'projectTypeList' => $projectTypeList
    );

    //dd($data);

    return response()->json($data);
    }

}
