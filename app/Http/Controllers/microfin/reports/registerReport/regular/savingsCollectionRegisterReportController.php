<?php

namespace App\Http\Controllers\microfin\reports\registerReport\regular;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use App\hr\Exam;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\microfin\MicroFin;
use App\Http\Controllers\microfin\MicroFinance;
use App\microfin\settings\MfnProfession;



class savingsCollectionRegisterReportController extends Controller {
    
    public function index(){
    
        $userBranchId=Auth::user()->branchId;

        $branchList = DB::table('gnr_branch');

        if($userBranchId == 1) {
          $BranchDatas = $branchList->get();
        } else { 
          $BranchDatas = $branchList->where('id', $userBranchId)->get();
        }

        $ProductDatas = DB::table('mfn_loans_product')
                        ->where('mfn_loans_product.isPrimaryProduct', '=', 1)->get();
        $ProductsCategories = DB::table('mfn_loans_product_category')->get();
        $SavingsProducts = DB::table('mfn_saving_product')->get();
                              

        return view("microfin.reports.registerReport.regular.savingsCollectionRegisterReportViews.SavingsCollectionRegisterReportForm", compact('userBranchId','BranchDatas', 'ProductDatas', 'ProductsCategories', 'SavingsProducts'));
    }

    public function ajaxData() { 

        $categoryId = Input::get('categoryId'); 

        $primaryProduct = DB::table('mfn_loans_product')
                    ->where('productCategoryId', '=', $categoryId)
                    ->get();
        
        return response::json($primaryProduct);         
    }

  public function viewReport(Request $request){

    $userBranchId=Auth::user()->branchId;

    if($request->searchBranch==Null || $request->searchBranch=='') {
      $BranchDatas = DB::table('gnr_branch')
                    ->where('id', $userBranchId)
                    ->get();
    }

    elseif ($request->searchBranch != 'All') {
      $BranchDatas = DB::table('gnr_branch')
                    ->where('id', $request->searchBranch)
                    ->get();
    }
    elseif ($request->searchBranch == 'All') {
      $BranchDatas = DB::table('gnr_branch')
                    ->get();
    }
        $ProductDatas = DB::table('mfn_loans_product')
                          ->where('mfn_loans_product.isPrimaryProduct', '=', 1)
                          ->select('id','name','shortName', 'code', 'productCategoryId')
                          ->get();
        $ProductsCategories = DB::table('mfn_loans_product_category')
                          ->select('id','name','shortName', 'categoryTypeId')
                          ->get();
        $SavingsProducts = DB::table('mfn_saving_product')
                          ->select('id','name')
                          ->get();

        // date input                  

        $dateTo = Carbon::parse($request->dateTo)->format('Y-m-d');
        $dateFrom = Carbon::parse($request->dateFrom)->format('Y-m-d');

        // deposit collection initial value

        $allDepositsCollection = DB::table('mfn_savings_deposit')
                          ->where('amount', '>', 0)
                          ->where('softDel', 0)
                          ->where('depositDate', '<=', $dateTo)
                          ->select('id','amount', 'depositDate', 'accountIdFk',
                          'productIdFk', 'primaryProductIdFk', 'memberIdFk',
                          'branchIdFk', 'samityIdFk', 'isTransferred');

        // deposit collection for opening balance calc

        $allDepositsFromBeginning = $allDepositsCollection->get();

        // deposit collection continue with condition

        $allDeposits = $allDepositsCollection
                          ->where('depositDate', '>=', $dateFrom)
                          ->get();
        
        // joining information

        $samityInfos = DB::table('mfn_samity')
                          ->whereIn('id', $allDeposits->unique('samityIdFk')->pluck('samityIdFk'))
                          ->select('id','name', 'code')
                          ->get();

        $memberInfos = DB::table('mfn_member_information')
                          ->whereIn('id', $allDeposits->unique('memberIdFk')->pluck('memberIdFk'))
                          ->select('id','name', 'code')
                          ->get();

        $savingsAccounts = DB::table('mfn_savings_account')
                          ->whereIn('id', $allDeposits->unique('accountIdFk')->pluck('accountIdFk'))
                          ->select('id','savingsProductIdFk', 'savingsCode')
                          ->get();

        $savingsWithdraw = DB::table('mfn_savings_withdraw')
                          ->whereIn('accountIdFk', $allDepositsFromBeginning->unique('accountIdFk')->pluck('accountIdFk'))
                          ->select('accountIdFk', 'amount', 'withdrawDate')
                          ->get();

        $accountOpeningBalance = DB::table('mfn_opening_savings_account_info')
                          ->whereIn('savingsAccIdFk', $allDeposits->unique('accountIdFk')->pluck('accountIdFk'))
                          ->select('savingsAccIdFk', 'openingBalance')
                          ->get();

        // conditions start

        if($request->searchBranch == '' || $request->searchBranch == Null) {
            $allDeposits = $allDeposits->where('branchIdFk', $userBranchId);
        }
        elseif((int)$request->searchBranch > 0) {
          $allDeposits = $allDeposits->where('branchIdFk', $request->searchBranch);
        }

        
       
        if((int)$request->productCategory > 0) {
            $productCategoryById = DB::table('mfn_loans_product_category')
                        ->where('id', $request->productCategory)
                        ->first();

            if($request->searchProduct == 'AllSELECTED') {
                    $productsByCategory = DB::table('mfn_loans_product')
                            ->where('productCategoryId', $productCategoryById->id)
                            ->pluck('id')->toArray();
                    $allDeposits = $allDeposits
                        ->whereIn('primaryProductIdFk', $productsByCategory);   
            } elseif($request->searchProduct > 0){
                    $allDeposits = $allDeposits
                      ->where('primaryProductIdFk', $request->searchProduct);
            } 
        }

        if((int)$request->savingsProduct > 0) {
        
            $allDeposits = $allDeposits
                             ->where('productIdFk', $request->savingsProduct);   
        }

        if($request->order == 0) {
            $allDeposits = $allDeposits->sortBy('depositDate');   
        }

        if($request->order == 1) {
            $allDeposits = $allDeposits->sortBy('samityIdFk');   
        }

        //$allDeposits = $allDeposits->get();

        // collecting all info

        
        $data = array(
            'allDeposits'                  => $allDeposits,
            'allDepositsFromBeginning'     => $allDepositsFromBeginning,
            'samityInfos'                  => $samityInfos,
            'memberInfos'                  => $memberInfos,
            'savingsAccounts'              => $savingsAccounts,
            'savingsWithdraw'              => $savingsWithdraw,
            'accountOpeningBalance'        => $accountOpeningBalance,
            'branches'                     => $request->searchBranch,
            'product'                      => $request->searchProduct,
            'productCategory'              => $request->productCategory,
            'savingsProduct'               => $request->savingsProduct,
            'dateFrom'                     => $request->dateFrom,
            'dateTo'                       => $request->dateTo,
        );

        // dd($allDeposits);

    return view("microfin.reports.registerReport.regular.savingsCollectionRegisterReportViews.SavingsCollectionRegisterReport", $data, compact('BranchDatas', 'ProductDatas', 'ProductsCategories', 'SavingsProducts', 'userBranchId'));

  }

}
