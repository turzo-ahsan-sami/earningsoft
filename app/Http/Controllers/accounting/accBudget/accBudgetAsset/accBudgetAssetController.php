<?php

namespace App\Http\Controllers\accounting\accBudget\accBudgetAsset;

use App\Http\Controllers\Controller;
use DB;
use App\Http\Requests;
use Response;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\gnr\Service;

use App\Http\Controllers\microfin\MicroFin;
use App\Traits\GetSoftwareDate;

//
// use Illuminate\Http\Request;
// use App\Http\Requests;
// use Validator;
// use Response;
// use App\Traits\GetSoftwareDate;
// use Illuminate\Support\Facades\Input;
// use Illuminate\Support\Facades\Hash;
// use App\Http\Controllers\Controller;


 // - Controller for Branch Wise Samity Report
 // - Created By Atiqul Haque
 // - Date:11/04/18

class accBudgetAssetController extends Controller
{
  use GetSoftwareDate;

  public function getBudgetPreview() {
    $BranchDatas = DB::table('gnr_branch')->get();

    // dd($BranchDatas);

    $fiscalYear = DB::table('gnr_fiscal_year')
    ->select('name')
    ->groupBy('name')
    ->get();

    return view('accounting.accBudgetViews.accBudgetAssetViews.accBudgetPreview', compact('BranchDatas', 'fiscalYear'));
  }

  public function getBudgetPreviewTable(Request $request) {
    $requestedBranchId = $request->searchBranch;
    $requestedFiscalYear = $request->searchYear;

    $BranchName = DB::table('gnr_branch')
    ->select('name')
    ->where('id', $requestedBranchId)
    ->pluck('name')
    ->toArray();

    $fiscalYear = DB::table('gnr_fiscal_year')
    ->select('name')
    ->groupBy('name')
    ->get();

    $BudgetInfos = DB::table('acc_budget_createdBy')
    ->where([['branchIdFk', $requestedBranchId], ['fiscalYear', $requestedFiscalYear]])
    ->get();

      // dd($BudgetInfos);

    return view('accounting.accBudgetViews.accBudgetAssetViews.accBudgetPreviewTable', compact('BranchName', 'fiscalYear', 'BudgetInfos'));
  }

  public function getAddBudgetPreview() {
    // $arr =

    $BranchDatas = DB::table('gnr_branch')->get();

    // dd($BranchDatas);

    $fiscalYear = DB::table('gnr_fiscal_year')
    ->select('name')
    ->groupBy('name')
    ->get();

    $budgetTypeName = DB::table('acc_account_ledger')
    ->select('code', 'name')
    ->where([['code', 'like', '%0000'], ['code', '!=', 30000]])
    ->get();



    foreach ($budgetTypeName as $key => $budgetTypeName1) {
      // code...
      if ($budgetTypeName1->code == 10000) {
        $arr = $budgetTypeName1->code;
        $arrName = $budgetTypeName1->name;
      }
      elseif ($budgetTypeName1->code == 20000) {
        $budgetTypeName1->code = $arr.' & '.$budgetTypeName1->code;
        $budgetTypeName1->name = $arrName.' & '.$budgetTypeName1->name;
      }
    }

    // dd($budgetTypeName);

    return view('accounting.accBudgetViews.accBudgetAssetViews.accAddBudget', compact('BranchDatas', 'fiscalYear', 'budgetTypeName'));
  }

  public function getLedgerName(Request $request){
    // dd($request->searchBudget);
    $requestedBranchId = $request->searchBranch;
    // dd($requestedBranchId);
    if ($request->searchBudget == '10000 & 20000') {
      $assetLedgers = DB::table('acc_account_ledger')
      ->where([['parentId', 0], ['code', 10000]])
      ->orWhere([['parentId', 0], ['code', 20000]])
      ->orderBy('ordering', 'asc')
      ->get();

      $companyId = DB::table('acc_account_ledger')
      ->select('companyIdFk')
      ->where([['parentId', 0], ['code', 10000]])
      ->orWhere([['parentId', 0], ['code', 20000]])
      ->orderBy('ordering', 'asc')
      ->pluck('companyIdFk')
      ->toArray();

      $BudgetName = 'Asset & Liabilities';
      // dd($requestedBranchId);

      return view('accounting.accBudgetViews.accBudgetAssetViews.accBudgetAssetSubmit', compact('assetLedgers', 'companyId', 'BudgetName', 'requestedBranchId'));

    }
    elseif ($request->searchBudget == '40000') {
      $assetLedgers = DB::table('acc_account_ledger')->where([['parentId', 0], ['code', 40000]])->orderBy('ordering', 'asc')->get();

      $companyId = DB::table('acc_account_ledger')
      ->select('companyIdFk')
      ->where([['parentId', 0], ['code', 40000]])
      ->orderBy('ordering', 'asc')
      ->pluck('companyIdFk')
      ->toArray();

        // dd($companyId);

      $BudgetName = 'Income';

      return view('accounting.accBudgetViews.accBudgetAssetViews.accBudgetAssetSubmit', compact('assetLedgers', 'companyId', 'BudgetName', 'requestedBranchId'));

    }
    else {
      $assetLedgers = DB::table('acc_account_ledger')->where([['parentId', 0], ['code', 50000]])->orderBy('ordering', 'asc')->get();

      $companyId = DB::table('acc_account_ledger')
      ->select('companyIdFk')
      ->where([['parentId', 0], ['code', 50000]])
      ->orderBy('ordering', 'asc')
      ->pluck('companyIdFk')
      ->toArray();

        // dd($companyId);

      $BudgetName = 'Expenditure';

      return view('accounting.accBudgetViews.accBudgetAssetViews.accBudgetAssetSubmit', compact('assetLedgers', 'companyId', 'BudgetName', 'requestedBranchId'));

    }

  }

  public function getLedgerNameLiability(){
    $assetLedgers = DB::table('acc_account_ledger')->where([['parentId', 0], ['code', 20000]])->orderBy('ordering', 'asc')->get();

    $companyId = DB::table('acc_account_ledger')
    ->select('companyIdFk')
    ->where([['parentId', 0], ['code', 20000]])
    ->orderBy('ordering', 'asc')
    ->pluck('companyIdFk')
    ->toArray();

      // dd($companyId);

    $BudgetName = 'Liabilities';

    return view('accounting.accBudgetViews.accBudgetAssetViews.accBudgetAssetSubmit', compact('assetLedgers', 'companyId', 'BudgetName'));
  }

  public function getLedgerNameIncome(){
    $assetLedgers = DB::table('acc_account_ledger')->where([['parentId', 0], ['code', 40000]])->orderBy('ordering', 'asc')->get();

    $companyId = DB::table('acc_account_ledger')
    ->select('companyIdFk')
    ->where([['parentId', 0], ['code', 40000]])
    ->orderBy('ordering', 'asc')
    ->pluck('companyIdFk')
    ->toArray();

      // dd($companyId);

    $BudgetName = 'Income';

    return view('accounting.accBudgetViews.accBudgetAssetViews.accBudgetAssetSubmit', compact('assetLedgers', 'companyId', 'BudgetName'));
  }

  public function getLedgerNameExpenditure(){
    $assetLedgers = DB::table('acc_account_ledger')->where([['parentId', 0], ['code', 50000]])->orderBy('ordering', 'asc')->get();

    $companyId = DB::table('acc_account_ledger')
    ->select('companyIdFk')
    ->where([['parentId', 0], ['code', 50000]])
    ->orderBy('ordering', 'asc')
    ->pluck('companyIdFk')
    ->toArray();

      // dd($companyId);

    $BudgetName = 'Expenditure';

    return view('accounting.accBudgetViews.accBudgetAssetViews.accBudgetAssetSubmit', compact('assetLedgers', 'companyId', 'BudgetName'));
  }



  // MAIN(getReport) METHOD TO CALL............................................................................................
  public function saveBudget(Request $request){
    // REQUESTED DATA VARIABLES................................................................................................
    $softDate = GetSoftwareDate::getAccountingSoftwareDate();

    $userBranchId = Auth::user()->branchId;

    $userId = Auth::user()->emp_id_fk;

    $Count = sizeof($request->name);

    // dd(substr_count(20000, 0, 2, 3));

    $x = 0;
    $y = 0;
    $z = 0;
    $z1 = 0;
    $u = 0;

    $assetBudgetTotal = 0;
    $liabalityBudgetTotal = 0;

    for ($i=0; $i < 1; $i++) {
      // code...
      if (substr_compare($request->code[$i], 1, 0, 1) == 0) {
        // code...
        $DataCheck = DB::table('acc_budget_createdBy')
        ->where([['branchIdFk', $request->branchId[$i]], ['budget_category_id', '!=', 50000], ['budget_category_id', '!=', 40000], ['fiscalYear', $request->fiscalYear[$i]]])
        ->pluck('id')
        ->toArray();
      }
      elseif (substr_compare($request->code[$i], 4, 0, 1) == 0) {
        // code...
        $DataCheck = DB::table('acc_budget_createdBy')
        ->where([['branchIdFk', $request->branchId[$i]], ['budget_category_id', 'like', '4%'], ['fiscalYear', $request->fiscalYear[$i]]])
        ->pluck('id')
        ->toArray();
      }
      elseif (substr_compare($request->code[$i], 5, 0, 1) == 0) {
        // code...
        $DataCheck = DB::table('acc_budget_createdBy')
        ->where([['branchIdFk', $request->branchId[$i]], ['budget_category_id', 'like', '5%'], ['fiscalYear', $request->fiscalYear[$i]]])
        ->pluck('id')
        ->toArray();
      }

      if (sizeof($DataCheck) == 0 || sizeof($DataCheck) == null) {
        // code...
        if (substr_compare($request->code[$i], 1, 0, 1) == 0) {
          // code...
          for ($i=0; $i < $Count; $i++) {
            if (substr_compare($request->code[$i], 1, 0, 1) == 0) {
              $assetCodeLength[] = $i;

              // dd(substr_count($request->code[$i], 0, 2, 3));
            }
            else {
              $libalitiesCodeLength[] = $i;
            }
          }

          for ($i=0; $i < sizeof($assetCodeLength); $i++) {
            ++$x;
            foreach ($request->budget[$x] as $key => $budgetValueA) {
              $assetBudgetTotal = $assetBudgetTotal + $budgetValueA;
            }
          }

          $y = ++$x;
          $u = $y;

          for ($i=$y; $i <= sizeof($request->budget); $i++) {
            // ++$y;
            foreach ($request->budget[$y] as $key => $budgetValueL) {
              $liabalityBudgetTotal = $liabalityBudgetTotal + $budgetValueL;
            }
            ++$y;
          }

          $con = 0;

          if ($assetBudgetTotal == $liabalityBudgetTotal) {
            // EQUAL;
            for ($i=0; $i < $Count; $i++) {
              ++$z;
              $amount = json_encode($request->budget[$z]);

              $SuccessQuery = DB::table('acc_budget')->insert(
                [
                  'ladgerName' => $request->name[$i],
                  'code' => $request->code[$i],
                  'companyIdFk' => $request->companyId[$i],
                  'branchIdFk' => $request->branchId[$i],
                  'parentId' => $request->parentId[$i],
                  'isGroupHead' => $request->iswGroupHead[$i],
                  'ordering' => $request->ordering[$i],
                  'fiscalYear' => $request->fiscalYear[$i],
                  'date' =>  $softDate,
                  'amount' => $amount,
                  'approvedDate' =>  $request->approvedDate[$i],
                  'accountTypeId' => $request->acountTypeId[$i]
                ]
              );

              if ($con == 0) {
                $arr = array(
                  '0' => 10000,
                  '1' => 20000
                );

                $budgetCategoryId = json_encode($arr);

                $SuccessQueryCreatedBy = DB::table('acc_budget_createdBy')->insert(
                  [
                    'companyId' => $request->companyId[$i],
                    'branchIdFk' => $request->branchId[$i],
                    'approvedBy_empId' => 0,
                    'createdBy_empId' => $userId,
                    'budget_category_id' => $budgetCategoryId,
                    'fiscalYear' => $request->fiscalYear[$i],
                    'createdDate' =>  $softDate,
                    'approvedDate' =>  $request->approvedDate[$i],
                    'isApproved' => 0
                  ]
                );

                $con = 1;
              }
            }

            if ($SuccessQuery == true) {
              // $Success = 'True';
              $notification = array(
               'message' => 'You have successfully inserted the information!',
               'alert-type' => 'success'
             );
            }
            else {
              $notification = array(
               'message' => 'Please full fill the information properly!',
               'alert-type' => 'error'
             );
            }

          }
          else {
            $notification = array(
             'message' => 'Asset and Liability are not equal! Please fill up and submit again!',
             'alert-type' => 'warning'
           );
          }

          // return redirect()->back()->with($notification);
        }
        else {
          // code...
          $budgetCategoryId = 0;
          if (substr_compare($request->code[$i], 5, 0, 1) == 0) {
            // code...
            $budgetCategoryId = 50000;
          }
          else {
            // code...
            $budgetCategoryId = 40000;
          }
          $con1 = 0;
          for ($i=0; $i < $Count; $i++) {
            ++$z1;
            $amount = json_encode($request->budget[$z1]);

            $SuccessQuery = DB::table('acc_budget')->insert(
              [
                'ladgerName' => $request->name[$i],
                'code' => $request->code[$i],
                'companyIdFk' => $request->companyId[$i],
                'branchIdFk' => $request->branchId[$i],
                'parentId' => $request->parentId[$i],
                'isGroupHead' => $request->iswGroupHead[$i],
                'ordering' => $request->ordering[$i],
                'fiscalYear' => $request->fiscalYear[$i],
                'date' =>  $softDate,
                'amount' => $amount,
                'approvedDate' =>  $request->approvedDate[$i],
                'accountTypeId' => $request->acountTypeId[$i]
              ]
            );

            // dd($SuccessQuery);

            if ($con1 == 0) {
              // dd($request->approvedDate[$i]);
              $SuccessQueryCreatedBy = DB::table('acc_budget_createdBy')->insert(
                [
                  'companyId' => $request->companyId[$i],
                  'branchIdFk' => $request->branchId[$i],
                  'approvedBy_empId' => 0,
                  'createdBy_empId' => $userId,
                  'budget_category_id' => $budgetCategoryId,
                  'fiscalYear' => $request->fiscalYear[$i],
                  'createdDate' =>  $softDate,
                  'approvedDate' =>  $request->approvedDate[$i],
                  'isApproved' => 0
                ]
              );

              $con1 = 1;
            }
          }

          if ($SuccessQuery == true) {
            // $Success = 'True';
            $notification = array(
             'message' => 'You have successfully inserted the information!',
             'alert-type' => 'success'
           );
          }
          else {
            $notification = array(
             'message' => 'Please full fill the information properly!',
             'alert-type' => 'error'
           );
          }

        }

      }
      else {
        $notification = array(
         'message' => 'This budget is already created!',
         'alert-type' => 'warning'
       );
      }

    }

    $logArray = array(
      'moduleId'  => 4,
      'controllerName'  => 'accBudgetAssetController',
      'tableName'  => 'acc_budget_createdBy',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('acc_budget_createdBy')->max('id')]
    );
    Service::createLog($logArray);

    return redirect()->back()->with($notification);

  }

  public function getLedgerNameUpdateAssetAndLiability(){
    $userBranchId = Auth::user()->branchId;

    $userId = Auth::user()->emp_id_fk;

    $assetLedgers = DB::table('acc_account_ledger')
    ->where([['parentId', 0], ['code', 10000]])
    ->orWhere([['parentId', 0], ['code', 20000]])
    ->orderBy('ordering', 'asc')
    ->get();

    $companyId = DB::table('acc_account_ledger')
    ->select('companyIdFk')
    ->where([['parentId', 0], ['code', 10000]])
    ->orWhere([['parentId', 0], ['code', 20000]])
    ->orderBy('ordering', 'asc')
    ->pluck('companyIdFk')
    ->toArray();

    $BudgetName = 'Asset & Liabilities';

    $fiscalYear = DB::table('gnr_fiscal_year')
    ->where('companyId', $companyId[0])
    ->orderByRaw('id DESC')
    ->limit(1)
    ->pluck('name')
    ->toArray();

    $BudgetValues = DB::table('acc_budget')
    ->where([['fiscalYear', $fiscalYear[0]], ['companyIdFk', $companyId[0]], ['branchIdFk', $userBranchId], ['code', 'like', '1%']])
    ->orWhere([['fiscalYear', $fiscalYear[0]], ['companyIdFk', $companyId[0]], ['branchIdFk', $userBranchId], ['code', 'like', '2%']])
    ->get();

      // dd($BudgetValues);

    $counter = 0;
    foreach ($BudgetValues as $key => $BudgetValue) {
      $BudgetAmount[$counter][] = json_decode($BudgetValue->amount, true);
      ++$counter;
      // dd($BudgetAmount);
    }

    return view('accounting.accBudgetViews.accBudgetAssetViews.accBudgetAssetUpdate', compact('assetLedgers', 'companyId', 'BudgetName', 'BudgetValues', 'BudgetAmount'));
  }

  public function getLedgerNameAssetAndLiabilityApprove(){
    $userBranchId = Auth::user()->branchId;

    $userId = Auth::user()->emp_id_fk;

    $assetLedgers = DB::table('acc_account_ledger')
    ->where([['parentId', 0], ['code', 10000]])
    ->orWhere([['parentId', 0], ['code', 20000]])
    ->orderBy('ordering', 'asc')
    ->get();

    $companyId = DB::table('acc_account_ledger')
    ->select('companyIdFk')
    ->where([['parentId', 0], ['code', 10000]])
    ->orWhere([['parentId', 0], ['code', 20000]])
    ->orderBy('ordering', 'asc')
    ->pluck('companyIdFk')
    ->toArray();

    $BudgetName = 'Asset & Liabilities';

    $fiscalYear = DB::table('gnr_fiscal_year')
    ->where('companyId', $companyId[0])
    ->orderByRaw('id DESC')
    ->limit(1)
    ->pluck('name')
    ->toArray();

    $BudgetValues = DB::table('acc_budget')
    ->where([['fiscalYear', $fiscalYear[0]], ['companyIdFk', $companyId[0]], ['branchIdFk', $userBranchId], ['code', 'like', '1%']])
    ->orWhere([['fiscalYear', $fiscalYear[0]], ['companyIdFk', $companyId[0]], ['branchIdFk', $userBranchId], ['code', 'like', '2%']])
    ->get();

      // dd($BudgetValues);

    $counter = 0;
    foreach ($BudgetValues as $key => $BudgetValue) {
      $BudgetAmount[$counter][] = json_decode($BudgetValue->amount, true);
      ++$counter;
      // dd($BudgetAmount);
    }

    return view('accounting.accBudgetViews.accBudgetAssetViews.accBudgetAssetAndBudgetApproval', compact('assetLedgers', 'companyId', 'BudgetName', 'BudgetValues', 'BudgetAmount'));
  }

  public function getLedgerNameAssetAndLiabilityRevisedApprove(){
    $userBranchId = Auth::user()->branchId;

    $userId = Auth::user()->emp_id_fk;

    $assetLedgers = DB::table('acc_account_ledger')
    ->where([['parentId', 0], ['code', 10000]])
    ->orWhere([['parentId', 0], ['code', 20000]])
    ->orderBy('ordering', 'asc')
    ->get();

    $companyId = DB::table('acc_account_ledger')
    ->select('companyIdFk')
    ->where([['parentId', 0], ['code', 10000]])
    ->orWhere([['parentId', 0], ['code', 20000]])
    ->orderBy('ordering', 'asc')
    ->pluck('companyIdFk')
    ->toArray();

    $BudgetName = 'Asset & Liabilities';

    $fiscalYear = DB::table('gnr_fiscal_year')
    ->where('companyId', $companyId[0])
    ->orderByRaw('id DESC')
    ->limit(1)
    ->pluck('name')
    ->toArray();

    $BudgetValues = DB::table('acc_budget_revised')
    ->where([['fiscalYear', $fiscalYear[0]], ['companyIdFk', $companyId[0]], ['branchIdFk', $userBranchId], ['code', 'like', '1%']])
    ->orWhere([['fiscalYear', $fiscalYear[0]], ['companyIdFk', $companyId[0]], ['branchIdFk', $userBranchId], ['code', 'like', '2%']])
    ->get();

      // dd($BudgetValues);

    $counter = 0;
    foreach ($BudgetValues as $key => $BudgetValue) {
      $BudgetAmount[$counter][] = json_decode($BudgetValue->revisedAmount, true);
      ++$counter;
      // dd($BudgetAmount);
    }

    return view('accounting.accBudgetViews.accBudgetAssetViews.accBudgetAssetAndRevisedBudgetApproval', compact('assetLedgers', 'companyId', 'BudgetName', 'BudgetValues', 'BudgetAmount'));
  }

  public function getBudgetAssetAndLiabilityApproveSubmit(Request $request){
    // REQUESTED DATA VARIABLES................................................................................................
    $softDate = GetSoftwareDate::getAccountingSoftwareDate();

    $userBranchId = Auth::user()->branchId;

    $userId = Auth::user()->emp_id_fk;

    $Count = sizeof($request->name);

    // dd(substr_count(20000, 0, 2, 3));

    $x = 0;
    $y = 0;
    $z = 0;
    $z1 = 0;
    $u = 0;

    $assetBudgetTotal = 0;
    $liabalityBudgetTotal = 0;

    $con1 = 0;
    for ($i=0; $i < $Count; $i++) {
      if ($con1 == 0) {
        if (substr_compare($request->code[$i], 4, 0, 1) == 0) {
          $dataQuery = DB::table('acc_budget_createdBy')
          ->where([['fiscalYear', $request->fiscalYear[$i]], ['budget_category_id', 'like', '4%'], ['isApproved', '=', 0]])
          ->pluck('id')
          ->toArray();

            // dd($dataQuery);
        }
        elseif (substr_compare($request->code[$i], 5, 0, 1) == 0) {
          $dataQuery = DB::table('acc_budget_createdBy')
          ->where([['fiscalYear', $request->fiscalYear[$i]], ['budget_category_id', 'like', '5%'], ['isApproved', '=', 0]])
          ->pluck('id')
          ->toArray();
        }
        else {
          $dataQuery = DB::table('acc_budget_createdBy')
          ->where([['fiscalYear', $request->fiscalYear[$i]], ['budget_category_id', '!=', 40000], ['budget_category_id', '!=', 50000], ['isApproved', '=', 0]])
          ->pluck('id')
          ->toArray();
        }

        // dd($dataQuery);

        if (sizeof($dataQuery) > 0) {
          // code...
          $SuccessQueryCreatedBy = DB::table('acc_budget_createdBy')
          ->where([['companyId', $request->companyId[$i]], ['branchIdFk', $userBranchId], ['id', $dataQuery[0]]])
          ->update(
            [
              'approvedBy_empId' => $userId,
              'approvedDate' =>  $softDate,
              'isApproved' => 1
            ]
          );

          $con1 = 1;

          if ($SuccessQueryCreatedBy == true) {
            // $Success = 'True';
            $notification = array(
             'message' => 'You have successfully approved this budget!',
             'alert-type' => 'success'
           );
          }
          else {
            $notification = array(
             'message' => 'Something is wrong in this ionformation!',
             'alert-type' => 'error'
           );
          }

        }
        else {
          // code...
          $notification = array(
           'message' => 'This budget is already approved!',
           'alert-type' => 'warning'
         );
        }

      }
    }

    return redirect()->back()->with($notification);

  }

  public function getBudgetRevisedApproveSubmit(Request $request){
    // REQUESTED DATA VARIABLES................................................................................................
    $softDate = GetSoftwareDate::getAccountingSoftwareDate();

    $userBranchId = Auth::user()->branchId;

    $userId = Auth::user()->emp_id_fk;

    $Count = sizeof($request->name);

    // dd(substr_count(20000, 0, 2, 3));

    $x = 0;
    $y = 0;
    $z = 0;
    $z1 = 0;
    $u = 0;

    $assetBudgetTotal = 0;
    $liabalityBudgetTotal = 0;

    $con1 = 0;
    for ($i=0; $i < $Count; $i++) {
      if ($con1 == 0) {
        if (substr_compare($request->code[$i], 4, 0, 1) == 0) {
          $dataQuery = DB::table('acc_budget_revisedBy')
          ->where([['fiscalYear', $request->fiscalYear[$i]], ['budget_category_id', 'like', '4%'], ['isApproved', '=', 0]])
          ->pluck('id')
          ->toArray();

            // dd($dataQuery);
        }
        elseif (substr_compare($request->code[$i], 5, 0, 1) == 0) {
          $dataQuery = DB::table('acc_budget_revisedBy')
          ->where([['fiscalYear', $request->fiscalYear[$i]], ['budget_category_id', 'like', '5%'], ['isApproved', '=', 0]])
          ->pluck('id')
          ->toArray();
        }
        else {
          $dataQuery = DB::table('acc_budget_revisedBy')
          ->where([['fiscalYear', $request->fiscalYear[$i]], ['budget_category_id', '!=', 40000], ['budget_category_id', '!=', 50000], ['isApproved', '=', 0]])
          ->pluck('id')
          ->toArray();
        }

        // dd($dataQuery);

        if (sizeof($dataQuery) > 0) {
          // code...
          $SuccessQueryCreatedBy = DB::table('acc_budget_revisedBy')
          ->where([['companyId', $request->companyId[$i]], ['branchIdFk', $userBranchId], ['id', $dataQuery[0]]])
          ->update(
            [
              'approvedBy_empId' => $userId,
              'approvedDate' =>  $softDate,
              'isApproved' => 1
            ]
          );

          $con1 = 1;

          if ($SuccessQueryCreatedBy == true) {
            // $Success = 'True';
            $notification = array(
             'message' => 'You have successfully approved this budget!',
             'alert-type' => 'success'
           );
          }
          else {
            $notification = array(
             'message' => 'Something is wrong in this ionformation!',
             'alert-type' => 'error'
           );
          }

        }
        else {
          // code...
          $notification = array(
           'message' => 'This budget is already approved!',
           'alert-type' => 'warning'
         );
        }

      }
    }

    return redirect()->back()->with($notification);

  }

  public function getLedgerNameExpenditureApprove(){
    $userBranchId = Auth::user()->branchId;

    $userId = Auth::user()->emp_id_fk;

    $assetLedgers = DB::table('acc_account_ledger')
    ->where([['parentId', 0], ['code', 50000]])
    ->orderBy('ordering', 'asc')
    ->get();

    $companyId = DB::table('acc_account_ledger')
    ->select('companyIdFk')
    ->where([['parentId', 0], ['code', 50000]])
    ->orderBy('ordering', 'asc')
    ->pluck('companyIdFk')
    ->toArray();

    $BudgetName = 'Expenditure';

    $fiscalYear = DB::table('gnr_fiscal_year')
    ->where('companyId', $companyId[0])
    ->orderByRaw('id DESC')
    ->limit(1)
    ->pluck('name')
    ->toArray();

    $BudgetValues = DB::table('acc_budget')
    ->where([['fiscalYear', $fiscalYear[0]], ['companyIdFk', $companyId[0]], ['branchIdFk', $userBranchId], ['code', 'like', '5%']])
    ->get();

      // dd($BudgetValues);

    $counter = 0;
    foreach ($BudgetValues as $key => $BudgetValue) {
      $BudgetAmount[$counter][] = json_decode($BudgetValue->amount, true);
      ++$counter;
      // dd($BudgetAmount);
    }

    return view('accounting.accBudgetViews.accBudgetAssetViews.accBudgetAssetAndBudgetApproval', compact('assetLedgers', 'companyId', 'BudgetName', 'BudgetValues', 'BudgetAmount'));
  }

  public function getLedgerNameExpenditureRevisedApprove(){
    $userBranchId = Auth::user()->branchId;

    $userId = Auth::user()->emp_id_fk;

    $assetLedgers = DB::table('acc_account_ledger')
    ->where([['parentId', 0], ['code', 50000]])
    ->orderBy('ordering', 'asc')
    ->get();

    $companyId = DB::table('acc_account_ledger')
    ->select('companyIdFk')
    ->where([['parentId', 0], ['code', 50000]])
    ->orderBy('ordering', 'asc')
    ->pluck('companyIdFk')
    ->toArray();

    $BudgetName = 'Expenditure';

    $fiscalYear = DB::table('gnr_fiscal_year')
    ->where('companyId', $companyId[0])
    ->orderByRaw('id DESC')
    ->limit(1)
    ->pluck('name')
    ->toArray();

    $BudgetValues = DB::table('acc_budget_revised')
    ->where([['fiscalYear', $fiscalYear[0]], ['companyIdFk', $companyId[0]], ['branchIdFk', $userBranchId], ['code', 'like', '5%']])
    ->get();

      // dd($BudgetValues);

    $counter = 0;
    foreach ($BudgetValues as $key => $BudgetValue) {
      $BudgetAmount[$counter][] = json_decode($BudgetValue->revisedAmount, true);
      ++$counter;
      // dd($BudgetAmount);
    }

    return view('accounting.accBudgetViews.accBudgetAssetViews.accBudgetAssetAndRevisedBudgetApproval', compact('assetLedgers', 'companyId', 'BudgetName', 'BudgetValues', 'BudgetAmount'));
  }

  public function getLedgerNameIncomeRevisedApprove(){
    $userBranchId = Auth::user()->branchId;

    $userId = Auth::user()->emp_id_fk;

    $assetLedgers = DB::table('acc_account_ledger')
    ->where([['parentId', 0], ['code', 40000]])
    ->orderBy('ordering', 'asc')
    ->get();

    $companyId = DB::table('acc_account_ledger')
    ->select('companyIdFk')
    ->where([['parentId', 0], ['code', 40000]])
    ->orderBy('ordering', 'asc')
    ->pluck('companyIdFk')
    ->toArray();

    $BudgetName = 'Income';

    $fiscalYear = DB::table('gnr_fiscal_year')
    ->where('companyId', $companyId[0])
    ->orderByRaw('id DESC')
    ->limit(1)
    ->pluck('name')
    ->toArray();

    $BudgetValues = DB::table('acc_budget_revised')
    ->where([['fiscalYear', $fiscalYear[0]], ['companyIdFk', $companyId[0]], ['branchIdFk', $userBranchId], ['code', 'like', '4%']])
    ->get();

      // dd($BudgetValues);

    $counter = 0;
    foreach ($BudgetValues as $key => $BudgetValue) {
      $BudgetAmount[$counter][] = json_decode($BudgetValue->revisedAmount, true);
      ++$counter;
      // dd($BudgetAmount);
    }

    return view('accounting.accBudgetViews.accBudgetAssetViews.accBudgetAssetAndRevisedBudgetApproval', compact('assetLedgers', 'companyId', 'BudgetName', 'BudgetValues', 'BudgetAmount'));
  }


  public function getLedgerNameIncomeApprove(){
    $userBranchId = Auth::user()->branchId;

    $userId = Auth::user()->emp_id_fk;

    $assetLedgers = DB::table('acc_account_ledger')
    ->where([['parentId', 0], ['code', 40000]])
    ->orderBy('ordering', 'asc')
    ->get();

    $companyId = DB::table('acc_account_ledger')
    ->select('companyIdFk')
    ->where([['parentId', 0], ['code', 40000]])
    ->orderBy('ordering', 'asc')
    ->pluck('companyIdFk')
    ->toArray();

    $BudgetName = 'Income';

    $fiscalYear = DB::table('gnr_fiscal_year')
    ->where('companyId', $companyId[0])
    ->orderByRaw('id DESC')
    ->limit(1)
    ->pluck('name')
    ->toArray();

    $BudgetValues = DB::table('acc_budget')
    ->where([['fiscalYear', $fiscalYear[0]], ['companyIdFk', $companyId[0]], ['branchIdFk', $userBranchId], ['code', 'like', '4%']])
    ->get();

      // dd($BudgetValues);

    $counter = 0;
    foreach ($BudgetValues as $key => $BudgetValue) {
      $BudgetAmount[$counter][] = json_decode($BudgetValue->amount, true);
      ++$counter;
      // dd($BudgetAmount);
    }

    return view('accounting.accBudgetViews.accBudgetAssetViews.accBudgetAssetAndBudgetApproval', compact('assetLedgers', 'companyId', 'BudgetName', 'BudgetValues', 'BudgetAmount'));
  }

  public function getLedgerNameUpdateIncome(){
    $userBranchId = Auth::user()->branchId;

    $userId = Auth::user()->emp_id_fk;

    $companyId = DB::table('acc_account_ledger')
    ->select('companyIdFk')
    ->where([['parentId', 0], ['code', 40000]])
    ->orderBy('ordering', 'asc')
    ->pluck('companyIdFk')
    ->toArray();

    $BudgetName = 'Income';

    $fiscalYear = DB::table('gnr_fiscal_year')
    ->where('companyId', $companyId[0])
    ->orderByRaw('id DESC')
    ->limit(1)
    ->pluck('name')
    ->toArray();

    $BudgetValues = DB::table('acc_budget')
    ->where([['fiscalYear', $fiscalYear[0]], ['companyIdFk', $companyId[0]], ['branchIdFk', $userBranchId], ['code', 'like', '4%']])
    ->get();

    $counter = 0;
    foreach ($BudgetValues as $key => $BudgetValue) {
      $BudgetAmount[$counter][] = json_decode($BudgetValue->amount, true);
      ++$counter;
      // dd($BudgetAmount);
    }

    return view('accounting.accBudgetViews.accBudgetAssetViews.accBudgetAssetUpdate', compact('assetLedgers', 'companyId', 'BudgetName', 'BudgetValues', 'BudgetAmount'));
  }

  public function getLedgerNameUpdateExpenditure(){
    $userBranchId = Auth::user()->branchId;

    $userId = Auth::user()->emp_id_fk;

    $companyId = DB::table('acc_account_ledger')
    ->select('companyIdFk')
    ->where([['parentId', 0], ['code', 50000]])
    ->orderBy('ordering', 'asc')
    ->pluck('companyIdFk')
    ->toArray();

    $BudgetName = 'Income';

    $fiscalYear = DB::table('gnr_fiscal_year')
    ->where('companyId', $companyId[0])
    ->orderByRaw('id DESC')
    ->limit(1)
    ->pluck('name')
    ->toArray();

    $BudgetValues = DB::table('acc_budget')
    ->where([['fiscalYear', $fiscalYear[0]], ['companyIdFk', $companyId[0]], ['branchIdFk', $userBranchId], ['code', 'like', '5%']])
    ->get();

    $counter = 0;
    foreach ($BudgetValues as $key => $BudgetValue) {
      $BudgetAmount[$counter][] = json_decode($BudgetValue->amount, true);
      ++$counter;
      // dd($BudgetAmount);
    }

    return view('accounting.accBudgetViews.accBudgetAssetViews.accBudgetAssetUpdate', compact('assetLedgers', 'companyId', 'BudgetName', 'BudgetValues', 'BudgetAmount'));
  }

  public function getLedgerNameBudgetUpdate(Request $request){

    $softDate = GetSoftwareDate::getAccountingSoftwareDate();

    $Count = sizeof($request->name);

    $userBranchId = Auth::user()->branchId;

    $userId = Auth::user()->emp_id_fk;

    // dd($userBranchId);

    // dd($request->budget);

    $x = 0;
    $y = 0;
    $z = 0;
    $z1 = 0;
    $u = 0;

    $assetBudgetTotal = 0;
    $liabalityBudgetTotal = 0;

    for ($i=0; $i < 1; $i++) {
      // code...
      if (substr_compare($request->code[$i], 4, 0, 1) == 0) {
        $dataQuery = DB::table('acc_budget_createdBy')
        ->where([['fiscalYear', $request->fiscalYear[$i]], ['budget_category_id', 'like', '4%'], ['isApproved', '=', 0]])
        ->pluck('id')
        ->toArray();

          // dd($dataQuery);
      }
      elseif (substr_compare($request->code[$i], 5, 0, 1) == 0) {
        $dataQuery = DB::table('acc_budget_createdBy')
        ->where([['fiscalYear', $request->fiscalYear[$i]], ['budget_category_id', 'like', '5%'], ['isApproved', '=', 0]])
        ->pluck('id')
        ->toArray();
      }
      else {
        $dataQuery = DB::table('acc_budget_createdBy')
        ->where([['fiscalYear', $request->fiscalYear[$i]], ['budget_category_id', '!=', 40000], ['budget_category_id', '!=', 50000], ['isApproved', '=', 0]])
        ->pluck('id')
        ->toArray();
      }

    }

    if (sizeof($dataQuery) > 0) {
      // code...
      for ($i=0; $i < 1; $i++) {

        if (substr_compare($request->code[$i], 1, 0, 1) == 0) {
          for ($i=0; $i < $Count; $i++) {
            if (substr_compare($request->code[$i], 1, 0, 1) == 0) {
              $assetCodeLength[] = $i;

              // dd(substr_count($request->code[$i], 0, 2, 3));
            }
            else {
              $libalitiesCodeLength[] = $i;
            }
          }

          for ($i=0; $i < sizeof($assetCodeLength); $i++) {
            ++$x;
            foreach ($request->budget[$x] as $key => $budgetValueA) {
              $assetBudgetTotal = $assetBudgetTotal + $budgetValueA;
            }
          }

          $y = ++$x;
          $u = $y;

          for ($i=$y; $i <= sizeof($request->budget); $i++) {
            // ++$y;
            foreach ($request->budget[$y] as $key => $budgetValueL) {
              $liabalityBudgetTotal = $liabalityBudgetTotal + $budgetValueL;
            }
            ++$y;
          }

          // dd($request->budget, $assetBudgetTotal, $liabalityBudgetTotal, $y);

          $con = 0;

          if ($assetBudgetTotal == $liabalityBudgetTotal) {
            // EQUAL;
            for ($i=0; $i < $Count; $i++) {
              ++$z;
              $amount = json_encode($request->budget[$z]);

              // dd($amount);

              $SuccessQuery = DB::table('acc_budget')
              ->where([['code', $request->code[$i]], ['companyIdFk', $request->companyId[$i]], ['branchIdFk', $userBranchId]])
              ->update(
                [
                  'ladgerName' => $request->name[$i],
                  'code' => $request->code[$i],
                  'companyIdFk' => $request->companyId[$i],
                  'branchIdFk' => $userBranchId,
                  'parentId' => $request->parentId[$i],
                  'isGroupHead' => $request->iswGroupHead[$i],
                  'ordering' => $request->ordering[$i],
                  'fiscalYear' => $request->fiscalYear[$i],
                  'date' =>  $softDate,
                  'amount' => $amount,
                  'approvedDate' =>  $request->approvedDate[$i],
                  'accountTypeId' => $request->acountTypeId[$i]
                ]
              );

              if ($con == 0) {
                $SuccessQueryCreatedBy = DB::table('acc_budget_createdBy')
                ->where([['branchIdFk', $request->branchId[$i]], ['companyId', $request->companyId[$i]]])
                ->update(
                  [
                    'companyId' => $request->companyId[$i],
                    'branchIdFk' => $userBranchId,
                    'approvedBy_empId' => 0,
                    'createdBy_empId' => $userId,
                    'createdDate' =>  $softDate,
                    'approvedDate' =>  $request->approvedDate[$i],
                    'isApproved' => 0
                  ]
                );

                $con = 1;
              }
            }

            // dd($SuccessQuery);

            if ($SuccessQuery == 0) {
              // $Success = 'True';
              $notification = array(
               'message' => 'You have successfully updated the information!',
               'alert-type' => 'success'
             );
            }
            else {
              $notification = array(
               'message' => 'Please full fill the information properly!',
               'alert-type' => 'error'
             );
            }

          }
          else {
            $notification = array(
             'message' => 'Asset and Liability are not equal! Please fill up and submit again!',
             'alert-type' => 'warning'
           );
          }

          // return redirect()->back()->with($notification);
        }
        else {
          // code...

          $con1 = 0;
          for ($i=0; $i < $Count; $i++) {
            ++$z1;
            $amount = json_encode($request->budget[$z1]);

            // dd($amount);

            $SuccessQuery = DB::table('acc_budget')
            ->where([['code', $request->code[$i]], ['companyIdFk', $request->companyId[$i]], ['branchIdFk', $userBranchId]])
            ->update(
              [
                'amount' => $amount
              ]
            );

            if ($con1 == 0) {
              $SuccessQueryCreatedBy = DB::table('acc_budget_createdBy')
              ->where([['branchIdFk', $request->branchId[$i]], ['companyId', $request->companyId[$i]]])
              ->update(
                [
                  'companyId' => $request->companyId[$i],
                  'branchIdFk' => $userBranchId,
                  'approvedBy_empId' => 0,
                  'createdBy_empId' => $userId,
                  'createdDate' =>  $softDate,
                  'approvedDate' =>  $request->approvedDate[$i],
                  'isApproved' => 0
                ]
              );

              $con1 = 1;
            }
          }

          // dd($SuccessQuery);

          if ($SuccessQuery == 0) {
            // $Success = 'True';
            $notification = array(
             'message' => 'You have successfully updated the information!',
             'alert-type' => 'success'
           );
          }
          else {
            // dd($SuccessQuery);
            $notification = array(
             'message' => 'Please full fill the information properly!',
             'alert-type' => 'error'
           );
          }
        }
      }
    }
    else {
      // RVISED BUDGET...
      for ($i=0; $i < 1; $i++) {

        if (substr_compare($request->code[$i], 1, 0, 1) == 0) {
          for ($i=0; $i < $Count; $i++) {
            if (substr_compare($request->code[$i], 1, 0, 1) == 0) {
              $assetCodeLength[] = $i;

              // dd(substr_count($request->code[$i], 0, 2, 3));
            }
            else {
              $libalitiesCodeLength[] = $i;
            }
          }

          for ($i=0; $i < sizeof($assetCodeLength); $i++) {
            ++$x;
            foreach ($request->budget[$x] as $key => $budgetValueA) {
              $assetBudgetTotal = $assetBudgetTotal + $budgetValueA;
            }
          }

          $y = ++$x;
          $u = $y;

          for ($i=$y; $i <= sizeof($request->budget); $i++) {
            // ++$y;
            foreach ($request->budget[$y] as $key => $budgetValueL) {
              $liabalityBudgetTotal = $liabalityBudgetTotal + $budgetValueL;
            }
            ++$y;
          }

          // dd($request->budget, $assetBudgetTotal, $liabalityBudgetTotal, $y);

          $con = 0;

          if ($assetBudgetTotal == $liabalityBudgetTotal) {
            // EQUAL;
            $arr = array(
              '0' => 10000,
              '1' => 20000
            );

            $budgetCategoryId = json_encode($arr);

            for ($i=0; $i < $Count; $i++) {
              ++$z;
              $amount = json_encode($request->budget[$z]);

              // dd($amount);

              $SuccessQuery = DB::table('acc_budget_revised')
              ->insert(
                [
                  'ladgerName' => $request->name[$i],
                  'code' => $request->code[$i],
                  'budgetIdFk' => $budgetCategoryId,
                  'accountTypeId' => $request->acountTypeId[$i],
                  'companyIdFk' => $request->companyId[$i],
                  'branchIdFk' => $userBranchId,
                  'parentId' => $request->parentId[$i],
                  'isGroupHead' => $request->iswGroupHead[$i],
                  'ordering' => $request->ordering[$i],
                  'revisedBy_empid' => $userId,
                  'fiscalYear' => $request->fiscalYear[$i],
                  'revisedDate' =>  $softDate,
                  'revisedAmount' => $amount
                ]
              );

              if ($con == 0) {
                $SuccessQueryCreatedBy = DB::table('acc_budget_revisedBy')
                ->insert(
                  [
                    'companyId' => $request->companyId[$i],
                    'branchIdFk' => $userBranchId,
                    'approvedBy_empId' => 0,
                    'createdBy_empId' => $userId,
                    'budget_category_id' => $budgetCategoryId,
                    'fiscalYear' => $request->fiscalYear[$i],
                    'createdDate' =>  $softDate,
                    'approvedDate' =>  $request->approvedDate[$i],
                    'isApproved' => 0
                  ]
                );

                $con = 1;
              }
            }

            if ($SuccessQuery == true) {
              // $Success = 'True';
              $notification = array(
               'message' => 'You have successfully revised the information!',
               'alert-type' => 'success'
             );
            }
            else {
              $notification = array(
               'message' => 'Please full fill the information properly!',
               'alert-type' => 'error'
             );
            }

          }
          else {
            $notification = array(
             'message' => 'Asset and Liability are not equal! Please fill up and submit again!',
             'alert-type' => 'warning'
           );
          }

          // return redirect()->back()->with($notification);
        }
        else {
          // code...
          if (substr_compare($request->code[$i], 4, 0, 1) == 0) {
            // code...
            $budgetCategoryId = 40000;
          }
          elseif (substr_compare($request->code[$i], 5, 0, 1) == 0) {
            // code...
            $budgetCategoryId = 50000;
          }

          $con1 = 0;
          for ($i=0; $i < $Count; $i++) {
            ++$z1;
            $amount = json_encode($request->budget[$z1]);

            // dd($request->name);

            $SuccessQuery = DB::table('acc_budget_revised')
            ->insert(
              [
                'ladgerName' => $request->name[$i],
                'code' => $request->code[$i],
                'budgetIdFk' => $budgetCategoryId,
                'accountTypeId' => $request->acountTypeId[$i],
                'companyIdFk' => $request->companyId[$i],
                'branchIdFk' => $userBranchId,
                'parentId' => $request->parentId[$i],
                'isGroupHead' => $request->iswGroupHead[$i],
                'ordering' => $request->ordering[$i],
                'revisedBy_empid' => $userId,
                'fiscalYear' => $request->fiscalYear[$i],
                'revisedDate' =>  $softDate,
                'revisedAmount' => $amount
              ]
            );

            if ($con1 == 0) {
              $SuccessQueryCreatedBy = DB::table('acc_budget_revisedBy')
              ->insert(
                [
                  'companyId' => $request->companyId[$i],
                  'branchIdFk' => $userBranchId,
                  'approvedBy_empId' => 0,
                  'createdBy_empId' => $userId,
                  'budget_category_id' => $budgetCategoryId,
                  'fiscalYear' => $request->fiscalYear[$i],
                  'createdDate' =>  $softDate,
                  'approvedDate' =>  $request->approvedDate[$i],
                  'isApproved' => 0
                ]
              );

              $con1 = 1;
            }
            $logArray = array(
              'moduleId'  => 4,
              'controllerName'  => 'accBudgetAssetController',
              'tableName'  => 'acc_budget_revisedBy',
              'operation'  => 'insert',
              'primaryIds'  => [DB::table('acc_budget_revisedBy')->max('id')]
            );
            Service::createLog($logArray);
          }

          // dd($SuccessQuery, $SuccessQueryCreatedBy, $request->budget, $request->code, $request->name);

          if ($SuccessQuery == true) {
            // $Success = 'True';
            $notification = array(
             'message' => 'You have successfully revised the information!',
             'alert-type' => 'success'
           );
          }
          else {
            $notification = array(
             'message' => 'Please full fill the information properly!',
             'alert-type' => 'error'
           );
          }
        }
      }
    }




    return redirect()->back()->with($notification);

    // return "update will come back soon";
  }


}
