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
use App\Traits\GetSoftwareDate;


class AccRevisedBudgetController extends Controller {

    public function index(Request $req) {
        //dd('ok');
        //dd($req->all());
        $userBranchId = Auth::user()->branchId;
        $userCompanyId = Auth::user()->company_id_fk;
        $userBranchStartDate = DB::table('gnr_branch')->where('id', $userBranchId)->value('aisStartDate');
        $branchDate = DB::table('acc_day_end')->max('date') != null
        ? DB::table('acc_day_end')->max('date')
        : $userBranchStartDate;
        $fiscalYear = DB::table('gnr_fiscal_year')
            ->where('companyId',$userCompanyId)
            ->where('fyStartDate', '>=', $branchDate)
            ->orderByDesc('id')
            ->pluck('name','id')->toArray();

        $projects = [0 => 'Select project'] +DB::table('gnr_project')
        ->where('companyId',$userCompanyId)
        ->pluck(DB::raw("CONCAT(projectCode, ' - ', name) AS nameWithCode"), 'id')
        ->toArray();
        $branchLists =['All (With HeadOffice)'] +  DB::table('gnr_branch')
         ->where('companyId',$userCompanyId)
        ->orderBy('branchCode')
        ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
        ->toArray();
        // dd($branchLists);
        $accountTypes = DB::table('acc_account_type')->where('parentId', 0)->pluck('name','id')->toarray();
        //dd($accountTypes) ;
        $projectSelected = $req->filProject;
        $fiscalYearSelected = $req->fiscalYear;
        $accountType = $req->filAccountType;
        $accountType = $req->filAccountType;
        $branchId = $req->branchId;
        if (session()->has('currentModule')) {
            $softwareDate = GetSoftwareDate::getAccountingSoftwareDate();
        } else {
            $softwareDate = date("Y-m-d");
        }
        //dd($softwareDate);
        if ($req->checkFirstLoad == 1) {

            //$revisedBudgets = DB::table('acc_budget_revised')->where('projectId', $projectSelected)->where('fiscalYearId', $fiscalYearSelected)->where('accountType',$accountType)->where('branchId',$branchId)->where('revisedDate',$softwareDate)->paginate(20);
            $revisedBudgets = DB::table('acc_budget_revised')->where('projectId', $projectSelected)->where('fiscalYearId', $fiscalYearSelected)->where('accountType',$accountType)->where('branchId',$branchId)->paginate(20);

        }
        else {
            $revisedBudgets = DB::table('acc_budget_revised')->where('companyId',$userCompanyId)->paginate(20);
        }

        $userBranchData = DB::table('gnr_branch')
                        ->where('id', $userBranchId)
                        ->select('id', DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"))
                        ->first();
        //dd($projectSelected);
        $data = array(
            'revisedBudgets'       => $revisedBudgets,
            'fiscalYear'           => $fiscalYear,
            'projects'             => $projects,
            'userBranchData'       => $userBranchData,
            'softwareDate'         => $softwareDate,
            'accountTypes'         => $accountTypes,
            'projectSelected'      => $projectSelected,
            'branchId'             => $branchId,
            'branchLists'          => $branchLists,
            'fiscalYearSelected'   => $fiscalYearSelected
        );
        // dd($data);

        return view('accounting.revisedBudgetViews.viewRevisedBudget', $data);
    }

    public function addRevisedBudget() {
        $userBranchId = Auth::user()->branchId;
        $userCompanyId = Auth::user()->company_id_fk;
        $userBranchStartDate = DB::table('gnr_branch')->where('id', $userBranchId)->value('aisStartDate');
        $branchDate = DB::table('acc_day_end')->max('date') != null
        ? DB::table('acc_day_end')->max('date')
        : $userBranchStartDate;

        $fiscalYear = DB::table('gnr_fiscal_year')
                    ->where('companyId',$userCompanyId)
                    ->orderByDesc('id')
                    ->where('fyStartDate', '>=', $branchDate)
                    ->pluck('name','id')->toArray();
        //dd($fiscalYears);


        //dd($fiscalYear);
        $projects = ["" => 'Select project'] + DB::table('gnr_project') ->where('companyId',$userCompanyId)->pluck('name','id', 'projectCode')->toarray();
        $projectTypes = DB::table('gnr_project_type')->pluck('name','id')->toarray();
        //dd($projects);
        //$accProjects = DB::table('acc_project')->select('name','id', 'projectCode')->get();
        $accLedgerTypes = DB::table('acc_account_type')->where('parentId', 0)->pluck('name','id')->toarray();
        //dd($accLedgerTypes);
        $branchLists = ['All (With HeadOffice)'] + DB::table('gnr_branch')
         ->where('companyId',$userCompanyId)
        ->orderBy('branchCode')
        ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
        ->toArray();
        //dd($projects,$projectTypes);
        $userBranchData = DB::table('gnr_branch')
                        ->where('id', $userBranchId)
                        ->select('id', DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"))
                        ->first();
        $accBudgetArr = array(
            'branchLists'               => $branchLists,
            // 'currencyLists'             => $currencyLists,
            'userBranchId'              => $userBranchId,
            'projects'                  => $projects,
            'projectTypes'              => $projectTypes,
            //'accProjects'               => $accProjects,
            'accLedgerTypes'            => $accLedgerTypes,
            'fiscalYear'                => $fiscalYear,
            'userBranchStartDate'       => $userBranchStartDate,
            'userBranchData'            => $userBranchData,
            'branchDate'                => Carbon::parse($branchDate)->format('d-m-Y'),
        );
        //dd($accBudgetArr);

        return view('accounting.revisedBudgetViews.addRevisedBudgetFilterForm',$accBudgetArr);
    }

    public function checkRevisedBudgetItem(Request $request){
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
        ->where('branchId',$branchId)
        ->where('companyId',$userCompanyId)
        ->where('status',1)
        ->first();
        //dd($getAccBugget);
        if(!$getAccBugget){
            return response()->json('Budget not available', 404);

        }else{
            return response()->json();
        }

    }

    public function loadRevisedBudget(Request $request, $id = null) {
       // dd($request->all()) ;
        $requestData = $request->all();
        $fiscalYearId = (int)$request->fiscalYearId;
        $projectId = (int)$request->projectId;
        $accountType = (int)$request->filAccountType;
        $branchId = (int)$request->branchId;
        $user_company_id = Auth::user()->company_id_fk;
        //dd(json_decode($request->month));
        if($id == null){
            $budgetInfo = DB::table('acc_budget')
                            ->where('fiscalYearId',$fiscalYearId)
                            ->where('projectId',$projectId)
                            ->where('accountType',$accountType)
                            ->where('branchId',$branchId)
                            ->where('companyId',$user_company_id)
                            ->where('status',1)
                            ->first();

            $revisedBudgetInfo = DB::table('acc_budget_revised')
                                 ->where('companyId',$user_company_id)
                                ->where('budgetId', $budgetInfo->id)
                                 ->where('companyId',$user_company_id)
                                ->orderBy('revisedDate', 'DESC')->first();
            //dd($revisedBudgetInfo);

            if ($revisedBudgetInfo) {
                $activeBudgetDetails = DB::table('acc_revised_budget_details')
                                        ->where('revisedBudgetId', $revisedBudgetInfo->id)
                                        ->select('ledgerId','debitAmount','creditAmount','month')
                                        ->groupBy('ledgerId')
                                        ->get()->keyBy('ledgerId')->toArray();
            }
            else {
                $activeBudgetDetails = DB::table('acc_budget_details')
                                        ->where('budgetId', $budgetInfo->id)
                                        ->select('ledgerId','debitAmount','creditAmount','month')
                                        ->groupBy('ledgerId')
                                        ->get()->keyBy('ledgerId')->toArray();
            }
        }else{
            $revisedBudgetInfo = DB::table('acc_budget_revised')->where('id', $id)->where('companyId',$user_company_id)->first();
            $budgetInfo = DB::table('acc_budget')->where('id', $revisedBudgetInfo->budgetId)->where('companyId',$user_company_id)->first();

            $projectId = $revisedBudgetInfo->projectId;
            $branchId = $revisedBudgetInfo->branchId;
            $fiscalYearId = $revisedBudgetInfo->fiscalYearId;
            $accountType = $revisedBudgetInfo->accountType;

            $activeBudgetDetails = DB::table('acc_revised_budget_details')
                                    ->where('revisedBudgetId', $revisedBudgetInfo->id)
                                    ->select('ledgerId','debitAmount','creditAmount','month')
                                    ->groupBy('ledgerId')
                                    ->get()->keyBy('ledgerId')->toArray();
            //dd($revisedBudgetInfo );
        }
        //dd($activeBudgetDetails);

       
        $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
        //dd($company);
        $userBranchId = Auth::user()->branchId;

        // branch date
        $branchDate = DB::table('acc_day_end')->where('branchIdFk', $userBranchId)->max('date');
        $branchDate = $branchDate == null ? DB::table('gnr_branch')->where('id', $userBranchId)->where('companyId',$user_company_id)->value('aisStartDate') : $branchDate;
        //dd($branchDate);

        $currentFiscalYear = DB::table('gnr_fiscal_year')
                            ->where('companyId',$user_company_id)
                             ->where('fyStartDate', '>=', $branchDate)
                            ->select('id','name', 'fyStartDate', 'fyEndDate')
                            ->first();

        // collecting date range
         //dd($currentFiscalYear);
        $dateFrom = DB::table('gnr_fiscal_year')->where('id', $currentFiscalYear->id)->value('fyStartDate');

        $fyStartDate = $dateFrom;
        $dateTo = DB::table('gnr_fiscal_year')->where('id', $currentFiscalYear->id)->value('fyEndDate');
        $lastOpeningDate = Carbon::parse($fyStartDate)->subdays(1)->format('Y-m-d');
        //dd($lastOpeningDate);
        //dd($dateFrom,$dateTo);
        //$months = $budgetInfo->month;
        $fiscalYearName = DB::table('gnr_fiscal_year')->where('id', $fiscalYearId)->value('name');
        $accountTypeName = DB::table('acc_account_type')->where('id', $accountType)->value('name');
        $branchName = DB::table('gnr_branch')->where('id', $branchId)->where('companyId',$user_company_id)->value('name');
        //dd($branchName);

        $projectName = DB::table('gnr_project')->where('id', $projectId)->where('companyId',$user_company_id)->value(DB::raw("CONCAT(LPAD(projectCode, 3, 0), ' - ', name)"));
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
            'branchId'               => $branchId,
            'projectName'            => $projectName,
            'monthsArr'              => $monthsArr,

        );
        //dd($loadBudgetTableArr);
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
        //dd($ledgersCollection);
        $finalLevelLedgerIdArr  = $ledgersCollection->pluck('id')->toArray();
        //dd($finalLevelLedgerIdArr);
        // final level ledgers opening balance informations
        $finalLevelLedgersOpData = DB::table('acc_account_ledger')
        ->leftJoin('acc_opening_balance', 'acc_opening_balance.ledgerId', '=', 'acc_account_ledger.id')
        ->whereIn('acc_account_ledger.id', $finalLevelLedgerIdArr)
        //->get();
        ->where('acc_opening_balance.openingDate', $lastOpeningDate);
        //dd($finalLevelLedgersOpData);
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
                                         //->where('fyStartDate', '>=', $branchDate)
                                        ->whereIn('acc_voucher_details.debitAcc', $finalLevelLedgerIdArr);


                                        //dd($currentPeriodVoucherDebitInfo);
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



                //dd($currentPeriodVoucherCreditInfo);


                // loop running after collecting all info
                $totalBalance = ['debit' => 0, 'credit' => 0, 'budgetDebit' => 0, 'budgetCredit' => 0];

                if($ledgersCollection){
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
                    $budgetDebitAmount = isset($activeBudgetDetails[$singleLedgerData->id])
                    ?$activeBudgetDetails[$singleLedgerData->id]->debitAmount
                    : 0;
                    $budgetCreditAmount = isset($activeBudgetDetails[$singleLedgerData->id])
                    ? $activeBudgetDetails[$singleLedgerData->id]->creditAmount
                    : 0;


                    $monthsData = isset($activeBudgetDetails[$singleLedgerData->id]) ? json_decode($activeBudgetDetails[$singleLedgerData->id]->month) : [];


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
                }else{
                    $ledgerWiseData = '';
                }


                //dd($ledgerWiseData);
                // structuring data to send information in view file
                // $data = array(
                //     'id'                        => $id,
                //     'budgetInfo'                => $budgetInfo,
                //     'loadBudgetTableArr'        => $loadBudgetTableArr,
                //     'ledgerWiseData'            => collect($ledgerWiseData),
                //     'totalBalance'              => $totalBalance
                // );
                //dd($data);
                return view('accounting.revisedBudgetViews.addRevisedBudgetTable',[

                    'id'                        => $id,
                    'budgetInfo'                => $budgetInfo,
                    'revisedBudgetInfo'         => $revisedBudgetInfo,
                    'loadBudgetTableArr'        => $loadBudgetTableArr,
                    'ledgerWiseData'            => collect($ledgerWiseData),
                    'totalBalance'              => $totalBalance
                ]);

            }


            public function addRevisedBudgetItem(Request $request) {
                //dd($request->all());
                $fiscalYearId = $request->fiscalYearId;
                $projectId   = $request->projectId;
                $accountType = $request->accountType;
                $branchId    = $request->branchId;
                $budgetId    = $request->budgetId;
                $userCompanyId  = Auth::user()->company_id_fk;
                // dd($request->all());


                $softwareDate = DB::table('acc_day_end')->where('branchIdFk',$branchId)->where('isDayEnd',0)->value('date');
                if ($softwareDate == '' || $softwareDate == null) {
                    $softwareDate = DB::table('acc_opening_balance')->where('branchId',$branchId)->value('openingDate');
                }
                if($softwareDate  =='' || $softwareDate == null){
                    $softwareDate = DB::table('gnr_branch')->where('id',$branchId)->value('aisStartDate');
                }

                //dd($softwareDate);
                $getAccRevisedBugget = DB::table('acc_budget_revised')
                                    ->where('fiscalYearId',$fiscalYearId)
                                    ->where('projectId',$projectId)
                                    ->where('accountType',$accountType)
                                    ->where('budgetId',$budgetId)
                                    ->where('revisedDate',$softwareDate)
                                    ->where('companyId',$userCompanyId)
                                   ->first();
                                   //dd($getAccRevisedBugget) ;

                if($getAccRevisedBugget){
                    $revisedDate   = $getAccRevisedBugget->revisedDate;
                    $revisedId     = $getAccRevisedBugget->id;
                   // $ledgerId     = $getAccRevisedBugget->ledgerId;
                    $accountType   = $getAccRevisedBugget->accountType;
                    //dd($revisedId);


                    $dataArray = [];
                    if($accountType == 1 || $accountType == 6 || $accountType == 9){
                        foreach (json_decode($request->debit) as $key => $value) {
                            $ledgerId= explode('-', $key)[1];
                            $dataArray[$ledgerId]['ledgerId'] = $ledgerId;
                            $dataArray[$ledgerId]['debit'] = (double) $value;
                            //dd($dataArray);
                        }



                        //loop fo r debit
                        foreach (json_decode($request->credit) as $key => $value) {
                            //dd( $value);
                            $ledgerId = explode('-', $key)[1];
                            $dataArray[$ledgerId]['ledgerId'] = $ledgerId;
                            $dataArray[$ledgerId]['credit'] = (double) $value;
                        }

                        //dd($dataArray);

                        foreach ($dataArray as $key => $data) {
                            //dd($data['debit'],$data['credit']);
                            $checkLedgerExists = DB::table('acc_revised_budget_details')
                                ->where('revisedBudgetId', $revisedId)
                                ->where('ledgerId', $data['ledgerId'])
                                ->first();
                            if($checkLedgerExists){
                                if($data['debit'] != 0 && $data['credit'] != 0){
                                    DB::table('acc_revised_budget_details')
                                    ->where('revisedBudgetId', $revisedId)
                                    ->where('ledgerId', $data['ledgerId'])
                                    ->update([
                                        'debitAmount'   => $data['debit'],
                                        'creditAmount'  => $data['credit'],
                                    ]);
                                }   
                            }else{
                                    if($data['debit'] != 0 && $data['credit'] != 0){
                                        DB::table('acc_revised_budget_details')->insert([
                                            'revisedBudgetId'=>$revisedId,
                                            'ledgerId'      =>$data['ledgerId'],
                                            'debitAmount'   =>$data['debit'],
                                            'creditAmount'  =>$data['credit']
                                        ]);
                                     } 
                            }
                            
                        }

                    }elseif($accountType == 12){
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
                            $checkLedgerExists = DB::table('acc_revised_budget_details')
                                ->where('revisedBudgetId', $revisedId)
                                ->where('ledgerId', $data['ledgerId'])
                                ->first();
                                //dd($data['ledgerId']);
                                if($checkLedgerExists){
                                    if($data['total'] != 0){
                                        DB::table('acc_revised_budget_details')
                                        ->where('revisedBudgetId', $revisedId)
                                        ->where('ledgerId', $data['ledgerId'])
                                        ->update([
                                            'creditAmount'  => $data['total'],
                                            'month'         => $data['month'],
                                    ]);
                                    }
                                }else{
                                    if($data['total'] != 0){
                                        DB::table('acc_revised_budget_details')->insert([
                                            'revisedBudgetId'      =>$revisedId,
                                            'ledgerId'      =>$data['ledgerId'],
                                            'creditAmount'  =>$data['total'],
                                            'month'         =>$data['month']
                                        ]);
                                    } 
                                
                                }  
                            
                        }

                    }
                    elseif($accountType == 13){
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
                            $checkLedgerExists = DB::table('acc_revised_budget_details')
                                ->where('revisedBudgetId', $revisedId)
                                ->where('ledgerId', $data['ledgerId'])
                                ->first();
                            if($checkLedgerExists){
                                if($data['total'] != 0){
                                    DB::table('acc_revised_budget_details')
                                    ->where('revisedBudgetId', $revisedId)
                                    ->where('ledgerId', $data['ledgerId'])
                                    ->update([
                                        'debitAmount'  => $data['total'],
                                        'month'         => $data['month'],
                                    ]);
                                }  
                            }else{
                                if($data['total'] != 0){
                                    DB::table('acc_revised_budget_details')->insert([
                                        'revisedBudgetId' =>$revisedId,
                                        'ledgerId'      =>$data['ledgerId'],
                                        'debitAmount'  =>$data['total'],
                                        'month'         =>$data['month']
                                    ]);
                               } 
                            }
                            
                        }

                        return response()->json();

                    }
                }
                else{
                    // dd(1);
                    DB::table('acc_budget_revised')->insert([
                        'fiscalYearId'  => $fiscalYearId,
                        'projectId'     => $projectId,
                        'branchId'      => $branchId,
                        'accountType'   => $accountType,
                        'budgetId'      => $budgetId,
                        'revisedDate'   => $softwareDate,
                        'companyId'     => $userCompanyId
                    ]);
                    $revisedBudgetId = DB::table('acc_budget_revised')->max('id');
                    //dd()
                    $dataArray = [];

                    if($accountType == 1 || $accountType == 6 || $accountType == 9){

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
                        // dd($dataArray);

                        foreach ($dataArray as $key => $data) {
                            //dd($data);
                            if($data['debit'] != 0 && $data['credit'] != 0){
                                DB::table('acc_revised_budget_details')->insert([
                                    'revisedBudgetId'=>$revisedBudgetId,
                                    'ledgerId'      =>$data['ledgerId'],
                                    'debitAmount'   => $data['debit'],
                                    'creditAmount'  => $data['credit']
                                ]);
                            }
                        }

                    }elseif($accountType == 12){
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
                                DB::table('acc_revised_budget_details')->insert([
                                    'revisedBudgetId'      =>$revisedBudgetId,
                                    'ledgerId'      =>$data['ledgerId'],
                                    'creditAmount'  => $data['total'],
                                    'month'   => $data['month'],
                                ]);
                            }
                        }
                    }elseif($accountType == 13){
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
                                DB::table('acc_revised_budget_details')->insert([
                                    'revisedBudgetId'     =>$revisedBudgetId,
                                    'ledgerId'      =>$data['ledgerId'],
                                    'debitAmount'   => $data['total'],
                                    'month'   => $data['month'],
                                ]);
                            }
                        }
                    }

                }
                
                return response()->json('Revised Budget saved succesfully!');

            }


            // public function updateRevisedBudget(Request $request){
            //     dd('ok');
            // }
            public function deleteRevisedBudgetItem(Request $req) {

                $revisedBudgetId = $req->id;

                DB::table('acc_revised_budget_details')->where('revisedBudgetId', $revisedBudgetId)->delete();
                DB::table('acc_budget_revised')->where('id', $revisedBudgetId)->delete();

                return response()->json(' Revised Budget deleted succesfully!');

            }

        }
