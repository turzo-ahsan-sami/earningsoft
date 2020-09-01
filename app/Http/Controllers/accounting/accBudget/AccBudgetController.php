<?php

namespace App\Http\Controllers\accounting\accBudget;

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
        // dd($req->checkFirstLoad);
        $userBranchId = Auth::user()->branchId;
        $userBranchStartDate = DB::table('gnr_branch')->where('id', $userBranchId)->value('aisStartDate');
        $branchDate = DB::table('acc_day_end')->max('date') != null
                    ? DB::table('acc_day_end')->max('date')
                    : $userBranchStartDate;
        $fiscalYears = DB::table('gnr_fiscal_year')
                    ->orderBy('name', 'desc')
                    ->pluck('name','id')
                    ->toArray();
        $projects = [0 => 'All'] + DB::table('gnr_project')
                    ->pluck(DB::raw("CONCAT(projectCode, ' - ', name) AS nameWithCode"), 'id')
                    ->toArray();
        $branches = DB::table('gnr_branch')
                    ->pluck(DB::raw("CONCAT(branchCode, ' - ', name) AS nameWithCode"), 'id')
                    ->toArray();
        $accountTypes = [0 => 'All'] + DB::table('acc_account_type')->where('parentId', 0)->pluck('name','id')->toarray();

        $projectSelected = $req->filProject;
        $fiscalYearSelected = $req->fiscalYear;
        $branchSelected = $req->filBranch;
        $accountTypeSelected = $req->accountType;

        if ($req->checkFirstLoad == 1) {
            $budgets = DB::table('acc_budget')
                    ->where('fiscalYearId', $fiscalYearSelected)
                    ->where('accountType', $accountTypeSelected);

            if ($projectSelected != 0) {
                $budgets = $budgets->where('projectId', $projectSelected);
            }
            if ($branchSelected != 0) {
                $budgets = $budgets->where('branchId', $branchSelected);

            }

            $budgets = $budgets->paginate(20);

        }
        else {
            $budgets = DB::table('acc_budget')->paginate(20);
        }
        // dd($budgets);

        $data = array(
            'budgets'               => $budgets,
            'fiscalYears'           => $fiscalYears,
            'projects'              => $projects,
            'branches'              => $branches,
            'accountTypes'          => $accountTypes,
            'projectSelected'       => $projectSelected,
            'fiscalYearSelected'    => $fiscalYearSelected,
            'branchSelected'        => $branchSelected,
            'accountTypeSelected'   => $accountTypeSelected
        );
        // dd($data);

        return view('accounting.accBudgetViews.viewBudget', $data);
    }

    public function getProjectWiseBranches(Request $request) {

        if ($request->projectId == 0) {
            $branches = [];
        }
        else {
            $branches = [1 => '000 - Head Office'] + DB::table('gnr_branch')
                        ->where('id', '!=', 1)
                        ->where('projectId', $request->projectId)
                        ->orderBy('branchCode')
                        ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                        ->toArray();
        }

        $projectWiseBranches = array(
            'branches'      => $branches
        );

        return response()->json($projectWiseBranches);
    }

    public function addBudget() {

        $userBranchId = Auth::user()->branchId;
        $userBranchStartDate = DB::table('gnr_branch')->where('id', $userBranchId)->value('aisStartDate');
        $branchDate = DB::table('acc_day_end')->max('date') != null
                    ? DB::table('acc_day_end')->max('date')
                    : $userBranchStartDate;
        $fiscalYears = DB::table('gnr_fiscal_year')
                    ->where(function ($query) use($branchDate) {
                        $query->where('fyStartDate', '>=', $branchDate)
                        ->orWhere('fyEndDate', '>=', $branchDate);
                    })
                    ->select('name','id')->orderBy('id', 'desc')->get();
                    // dd($fiscalYears);

        $projects = DB::table('gnr_project')->pluck('name','id')->toarray();
        $accLedgerTypes = DB::table('acc_account_type')->where('parentId', 0)->pluck('name','id')->toarray();
        $branchLists = DB::table('gnr_branch')
                        ->orderBy('branchCode')
                        ->pluck(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                        ->toArray();

        $accBudgetArr = array(
            'branchLists'               => $branchLists,
            'userBranchId'              => $userBranchId,
            'projects'                  => $projects,
            'accLedgerTypes'            => $accLedgerTypes,
            'fiscalYears'               => $fiscalYears,
            'userBranchStartDate'       => $userBranchStartDate,
            'branchDate'                => Carbon::parse($branchDate)->format('d-m-Y'),
        );
        // dd($accBudgetArr);

        return view('accounting.accBudgetViews.addBudgetFilterForm', $accBudgetArr);
    }

    public function getMonthsByFiscalYear($fiscalYearId) {

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
        // dd($monthsArr);

        return $monthsArr;
    }

    public function checkDependentLedgers(Request $request) {

        $calculatedLedgersData = [];
        $name = explode('_', $request->name);
        $month = $name[0];
        $ledgerId = $name[1];
        $value = (double) $request->val;
        $projectId = (int) $request->projectId;

        $dependents = DB::table('acc_ledger_relations')->where('projectid', $projectId)->where('ledger2', $ledgerId)->get();

        foreach ($dependents as $key => $item) {

            $nextLevelDependents = DB::table('acc_ledger_relations')->where('projectid', $projectId)->where('ledger2', $item->ledger1)->count();

            $calculatedLedgersData[] = array(
                'nextLevelExist' => $nextLevelDependents,
                'name' => $month . '_' . $item->ledger1,
                'value' => $value * $item->relation / 100
            );
        }

        return response()->json($calculatedLedgersData);
    }

    public function loadBudget(Request $request) {

        $user_company_id = Auth::user()->company_id_fk;
        $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
        $userBranchId = Auth::user()->branchId;

        // collecting request data
        // dd($request->all());
        $accountType = (int) $request->filAccountType;
        $project = (int) $request->filProject;
        $fiscalYearId = (int) $request->fiscalYearId;
        if ($userBranchId != 1) {
            $branch = $userBranchId;
        }
        else {
            $branch = (int) $request->filBranch;
        }

        // branch date
        $branchDate = DB::table('acc_day_end')->where('branchIdFk', $branch)->max('date');
        $branchDate = $branchDate == null ? DB::table('gnr_branch')->where('id', $branch)->value('aisStartDate') : $branchDate;
        // dd($branchDate);

        $currentFiscalYear = DB::table('gnr_fiscal_year')
                            ->where('fyStartDate', '<=', $branchDate)
                            ->where('fyEndDate', '>=', $branchDate)
                            ->select('id','name', 'fyStartDate', 'fyEndDate')
                            ->first();

        $projectName = DB::table('gnr_project')->where('id', $project)->value('name');
        $branchName = DB::table('gnr_branch')->where('id', $branch)->value(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', TRIM(REPLACE(name, '\t', '')))"));
        $accountTypeName = $accountType == 0 ? 'All' : DB::table('acc_account_type')->where('id', $accountType)->value('name');
        $fiscalYearName = DB::table('gnr_fiscal_year')->where('id', $fiscalYearId)->value('name');

        $monthsArray = $this->getMonthsByFiscalYear($fiscalYearId);

        // requested info array
        $loadBudgetTableArr = array(
            'company'                 => $company,
            'fiscalYearId'            => $fiscalYearId,
            'projectId'               => $project,
            'branchId'                => $branch,
            'accountType'             => $accountType,
            'fiscalYearName'          => $fiscalYearName,
            'projectName'             => $projectName,
            'branchName'              => $branchName,
            'accountTypeName'         => $accountTypeName,
            'monthsArray'             => $monthsArray
        );
        // dd($loadBudgetTableArr);

        // ledgers variable and collection
        $service = new Service;
        $ledgersCollection = $service->getLedgerHeaderInfo($project, $branch, $user_company_id);
        // relation ledgers
        $relationLedgers = DB::table('acc_ledger_relations')->where('projectId', $project)->get();
        $dependentLedgersIdArray = $relationLedgers->pluck('ledger1')->toArray();

        if ($accountType != 0) {
            $accountTypesIdArr = DB::table('acc_account_type')->where('parentId', $accountType)->pluck('id')->toArray();
            array_push($accountTypesIdArr, $accountType);

            $ledgersCollection = $ledgersCollection->whereIn('accountTypeId', $accountTypesIdArr);
        }
        // dd($ledgersCollection);


        $ledgers = ['ledgers' => [], 'parents' => []];

        foreach ($ledgersCollection as $ledger) {
            $ledgers['ledgers'][$ledger->id] = $ledger;
            $ledgers['parents'][$ledger->parentId][] = $ledger->id;
        }
        // dd($ledgers);

        // generate html tree
        $treeView = $this->buildTree(0, $ledgers, $monthsArray, $dependentLedgersIdArray);

        // structuring data to send information in view file
        $data = array(
            'loadBudgetTableArr'                => $loadBudgetTableArr,
            'relationLedgers'                   => $relationLedgers,
            'dependentLedgersIdArray'           => $dependentLedgersIdArray,
            'treeView'                          => $treeView
        );

        return view('accounting.accBudgetViews.addBudgetTable', $data);
    }

    // function for html tree view with recursion
    public function buildTree($parents, $ledgers, $monthsArray, $dependentLedgersIdArray) {
        // dd($ledgers);

        $html = "";

        if (isset($ledgers['parents'][$parents])) {

            foreach ($ledgers['parents'][$parents] as $ledgerId) {

                if ($ledgers['ledgers'][$ledgerId]->level > 4) {

                    if ($ledgers['ledgers'][$ledgerId]->isGroupHead == 0) {
                        $trStyle = 'ledgerTr level level-final level-constant';
                    }
                    else {
                        $trStyle = 'ledgerTr level level-constant level-'. $ledgers['ledgers'][$ledgerId]->level;
                    }
                }
                else {
                    if ($ledgers['ledgers'][$ledgerId]->isGroupHead == 0) {
                        $trStyle = 'ledgerTr level level-final level-constant';
                    }
                    else {
                        $trStyle = 'ledgerTr level level-'. $ledgers['ledgers'][$ledgerId]->level;
                    }
                }

                if (!isset($ledgers['parents'][$ledgerId]) && $ledgers['ledgers'][$ledgerId]->isGroupHead == 0) {

                    $html .= '<tr class="'.$trStyle.' budget-row" data-parent="'.$ledgers['ledgers'][$ledgerId]->parentId.'" data-id="'.$ledgers['ledgers'][$ledgerId]->id.'">
                                <td style="text-align: left;">' .$ledgers['ledgers'][$ledgerId]->name. '[' . $ledgers['ledgers'][$ledgerId]->code . ']</td>';

                    foreach ($monthsArray as $key => $month) {

                        if (in_array($ledgers['ledgers'][$ledgerId]->id, $dependentLedgersIdArray)) {
                            $inputStyle = 'readonly';
                        }
                        else {
                            $inputStyle = '';
                        }

                        $monthBalance = isset($ledgers['ledgers'][$ledgerId]->$key) ? $ledgers['ledgers'][$ledgerId]->$key : 0;

                        $html .= '<td class="amount">
                                    <input type="number" name="'. $key . '_' . $ledgers['ledgers'][$ledgerId]->id . '" value="' . number_format($monthBalance, 2, '.', '') . '" class="form-control budget-input input-sm text-right" min="0" step="0.01" autocomplete="off"' . $inputStyle . '>
                                </td>';

                    }

                    $totalBalance = isset($ledgers['ledgers'][$ledgerId]->totalBalance) ? $ledgers['ledgers'][$ledgerId]->totalBalance : 0;
                    // dd($totalBalance);

                    $html .= '<td class="amount">
                                    <input type="number" name="total_' . $ledgers['ledgers'][$ledgerId]->id . '" value="' . number_format($totalBalance, 2, '.', '') . '" class="form-control input-sm text-right total-budget" min="0" step="0.01" autocomplete="off" readonly>
                                </td>
                            </tr>';

                }

                if (isset($ledgers['parents'][$ledgerId])) {

                    $html .= '<tr class="'.$trStyle.'" data-parent="'.$ledgers['ledgers'][$ledgerId]->parentId.'" data-id="'.$ledgers['ledgers'][$ledgerId]->id.'">
                                <td style="text-align: left;">' . $ledgers['ledgers'][$ledgerId]->name . '[' . $ledgers['ledgers'][$ledgerId]->code . ']</td>';

                    foreach ($monthsArray as $key => $month) {
                        $html .= '<td class="amount '.$key.'_amount">'. number_format(0, 2) . '</td><input type="hidden" name="' . $key . '_val" value="' . number_format(0, 2, '.', '') . '">';
                    }

                    $html .= '<td class="amount total-amount">'. number_format(0, 2) . '</td><input type="hidden" name="total_val" value="' . number_format(0, 2, '.', '') . '"></tr>';

                    $html .= $this->buildTree($ledgerId, $ledgers, $monthsArray, $dependentLedgersIdArray);

                }
                // dd($html);

            }

        }

        return $html;
    }

    public function addBudgetItem(Request $request) {
        // dd($request->all());
        $requestData = $request->all();
        $fiscalYearId = $request->fiscalYearId;
        $projectId = $request->projectId;
        $branchId = $request->branchId;
        $accountType = $request->accountType;

        // insert into budget table
        DB::table('acc_budget')->insert([
            'fiscalYearId'  => $fiscalYearId,
            'projectId'     => $projectId,
            'branchId'      => $branchId,
            'accountType'   => $accountType,
            'createdDate'   => Carbon::now()
        ]);

        $budgetId = DB::table('acc_budget')->max('id');
        $dataArray = [];

        unset($requestData['_token'], $requestData['fiscalYearId'], $requestData['projectId'], $requestData['branchId'], $requestData['accountType']);
        // dd($requestData);
        foreach ($requestData as $key => $value) {

            $arr = explode('_', $key);
            if ($arr[1] != 'val') {
                $ledgerId = (int)$arr[1];
                $dataArray[$ledgerId][$arr[0]] = (double) $value;
            }

        } // loop close
        // dd($dataArray);

        foreach ($dataArray as $key => $data) {

            $total = $data['total'];
            unset($data['total']);

            DB::table('acc_budget_details')
            ->insert([
                'budgetId'          => $budgetId,
                'ledgerId'          => $key,
                'monthBalance'      => json_encode($data),
                'totalBalance'      => $total,
                'createdDate'       => Carbon::now()
            ]);

        }

        return response()->json(array('responseText' => 'Budget saved succesfully!'));
    }

    public function editBudget($id) {

        $budget = DB::table('acc_budget')->where('id', $id)->first();
        $budgetDetails = DB::table('acc_budget_details')->where('budgetId', $id)->get();
        // dd($budget);

        $user_company_id = Auth::user()->company_id_fk;
        $company = DB::table('gnr_company')->where('id',$user_company_id)->select('name','address')->first();
        $userBranchId = Auth::user()->branchId;

        // collecting request data
        // dd($request->all());
        $accountType = $budget->accountType;
        $project = $budget->projectId;
        $fiscalYearId = $budget->fiscalYearId;
        $branch = $budget->branchId;

        $projectName = DB::table('gnr_project')->where('id', $project)->value('name');
        $branchName = DB::table('gnr_branch')->where('id', $branch)->value(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', TRIM(REPLACE(name, '\t', '')))"));
        $accountTypeName = $accountType == 0 ? 'All' : DB::table('acc_account_type')->where('id', $accountType)->value('name');
        $fiscalYearName = DB::table('gnr_fiscal_year')->where('id', $fiscalYearId)->value('name');

        $monthsArray = $this->getMonthsByFiscalYear($fiscalYearId);

        // requested info array
        $loadBudgetTableArr = array(
            'company'                 => $company,
            'fiscalYearId'            => $fiscalYearId,
            'projectId'               => $project,
            'branchId'                => $branch,
            'budgetId'                => $budget->id,
            'accountType'             => $accountType,
            'fiscalYearName'          => $fiscalYearName,
            'projectName'             => $projectName,
            'branchName'              => $branchName,
            'accountTypeName'         => $accountTypeName,
            'monthsArray'             => $monthsArray,
        );

        // ledgers variable and collection
        $service = new Service;
        $ledgersCollection = $service->getLedgerHeaderInfo($project, $branch, $user_company_id);
        // relation ledgers
        $relationLedgers = DB::table('acc_ledger_relations')->where('projectId', $project)->get();
        $dependentLedgersIdArray = $relationLedgers->pluck('ledger1')->toArray();

        if ($accountType != 0) {
            $accountTypesIdArr = DB::table('acc_account_type')->where('parentId', $accountType)->pluck('id')->toArray();
            array_push($accountTypesIdArr, $accountType);

            $ledgersCollection = $ledgersCollection->whereIn('accountTypeId', $accountTypesIdArr);
        }
        // dd($ledgersCollection);

        $ledgers = ['ledgers' => [], 'parents' => []];

        foreach ($ledgersCollection as $ledger) {

            if ($ledger->isGroupHead == 0) {

                $ledgerBudget = $budgetDetails->where('ledgerId', $ledger->id)->first();
                if ($ledgerBudget) {

                    $monthsBalance = json_decode($ledgerBudget->monthBalance);

                    foreach ($monthsBalance as $key => $value) {
                        $ledger->$key = $value;
                    }
                    $ledger->totalBalance = $ledgerBudget->totalBalance;
                    // dd($ledger);
                }

            }

            $ledgers['ledgers'][$ledger->id] = $ledger;
            $ledgers['parents'][$ledger->parentId][] = $ledger->id;
        }
        // dd($ledgers);

        // generate html tree
        $treeView = $this->buildTree(0, $ledgers, $monthsArray, $dependentLedgersIdArray);

        // structuring data to send information in view file
        $data = array(
            'loadBudgetTableArr'                => $loadBudgetTableArr,
            'treeView'                          => $treeView
        );

        return view('accounting.accBudgetViews.editBudgetTable', $data);
    }

    public function editBudgetItem(Request $request) {

        $requestData = $request->all();
        $budgetId = $requestData['budgetId'];
        // dd($budgetId);

        $dataArray = [];
        unset($requestData['_token'], $requestData['budgetId']);
        // dd($requestData);
        foreach ($requestData as $key => $value) {

            $arr = explode('_', $key);

            if ($arr[1] != 'val') {
                $ledgerId = (int)$arr[1];
                $dataArray[$ledgerId][$arr[0]] = (double) $value;
            }

        } // loop close
        // dd($dataArray);

        // DB::table('acc_budget_details')->where('budgetId', $budgetId)->delete();

        foreach ($dataArray as $key => $data) {

            $total = $data['total'];
            unset($data['total']);

            $ledgerIdBudget = DB::table('acc_budget_details')->where('budgetId', $budgetId)->where('ledgerId', $key)->first();

            if ($ledgerIdBudget == null) {
                DB::table('acc_budget_details')
                ->insert([
                    'budgetId'          => $budgetId,
                    'ledgerId'          => $key,
                    'monthBalance'      => json_encode($data),
                    'totalBalance'      => $total,
                    'createdDate'       => Carbon::now()
                ]);
            }
            else {
                DB::table('acc_budget_details')->where('budgetId', $budgetId)->where('ledgerId', $key)
                ->update([
                    'monthBalance'      => json_encode($data),
                    'totalBalance'      => $total
                ]);
            }

        }

        return response()->json(array('responseText' => 'Budget updated succesfully!'));
    }

    public function deleteItem(Request $req) {
        $budgetId = $req->id;
        DB::table('acc_budget_details')->where('budgetId', $budgetId)->delete();
        DB::table('acc_budget')->where('id', $budgetId)->delete();

        return response()->json(array('responseText' => 'Budget deleted succesfully!'));

    }

    public function updateItem(Request $req) {
        $budgetId = $req->id;
        // DB::table('acc_budget_details')->where('budgetId', $budgetId)->delete();
        $budgetStatus = DB::table('acc_budget')->where('id', $budgetId)->value('status');

        if ($budgetStatus == 0) {
            DB::table('acc_budget')->where('id', $budgetId)->update(['status' => 1]);
            return response()->json(array('responseText' => 'Budget status is changed to approved!'));
        }
        elseif ($budgetStatus == 1) {
            DB::table('acc_budget')->where('id', $budgetId)->update(['status' => 0]);
            return response()->json(array('responseText' => 'Budget status is changed to pending!'));
        }

    }

}
