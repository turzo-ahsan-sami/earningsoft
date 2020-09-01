<?php

namespace App\Http\Controllers\inventory\reports;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\inventory\InvProduct;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class InvStockReportController extends Controller
{
    public function index(){
    	$InvProducts = InvProduct::all();
    	return view('inventory/reports/viewInvStockReport',['InvProducts' => $InvProducts]);
    }

    public function filterStockReport(Request $req){

    	$startDate 				= (string)$req->startDate;
    	$endDate 				= (string)$req->endDate;
    	$branchId 				= (int)$req->branchId;
    	$supplierId 			= (int)$req->supplierId;
    	$productGroupId 		= (int)$req->productGroupId;
    	$productCategoryId 		= (int)$req->productCategoryId;
    	$productSubCategoryId 	= (int)$req->productSubCategoryId;
    	$productBrandId 		= (int)$req->productBrandId;
    	$productName 			= (string)$req->productName;
    	$searchTypeCtr 			= (int)$req->searchType;
    	$fiscalYearCtr 			= (int)$req->fiscalYear;
    
    	/*$InvProducts = InvProduct::where('createdDate', '<=',$startDate)->get();
    	return view('inventory/reports/viewInvStockReport',['InvProducts' => $InvProducts]);*/

    	$query  = DB::table('inv_product');

		// if ($startDate)
		//     $query->where(DB::raw('DATE(createdDate)'), '>=', $startDate);

		// if ($endDate)
		//     $query->where(DB::raw('DATE(createdDate)'), '<=', $endDate);

		if ($branchId)
		    $query->where('branchId', '=', $branchId);

		if ($supplierId)
		    $query->where('supplierId', '=', $supplierId);

		if ($productGroupId)
		    $query->where('groupId', '=', $productGroupId);

		if ($productCategoryId)
		    $query->where('categoryId', '=', $productCategoryId);

		if ($productSubCategoryId)
		    $query->where('subCategoryId', '=', $productSubCategoryId);

		if ($productBrandId)
		    $query->where('brandId', '=', $productBrandId);

		if ($productName)
		    $query->where('name', '=', $productName);

		$InvProducts = $query->get();

		return view('inventory/reports/viewInvStockReport',['InvProducts' => $InvProducts, 'searchTypeCtr' => $searchTypeCtr, 'fiscalYearCtr' => $fiscalYearCtr]);
	}

		public function branchStockReport(){
    	$InvProducts = InvProduct::all();
    	return view('inventory/reports/viewInvStockReportBranch',['InvProducts' => $InvProducts]);
    }

    public function filterStockReportBranch(Request $req){
    	
    	$startDate 				= (string)$req->startDate;
    	$endDate 				= (string)$req->endDate;
    	$branchId 				= (int)$req->branchId;
    	$supplierId 			= (int)$req->supplierId;
    	$productGroupId 		= (int)$req->productGroupId;
    	$productCategoryId 		= (int)$req->productCategoryId;
    	$productSubCategoryId 	= (int)$req->productSubCategoryId;
    	$productBrandId 		= (int)$req->productBrandId;
    	$productName 			= (string)$req->productName;
    	$searchTypeCtr 			= (int)$req->searchType;
    	$fiscalYearCtr 			= (int)$req->fiscalYear;
    
    	/*$InvProducts = InvProduct::where('createdDate', '<=',$startDate)->get();
    	return view('inventory/reports/viewInvStockReport',['InvProducts' => $InvProducts]);*/

    	$query  = DB::table('inv_product');

		// if ($startDate)
		//     $query->where('createdDate', '>=', $startDate);

		// if ($endDate)
		//     $query->where('createdDate', '<=', $endDate);
		
		// if ($startDate)
		//     $query->where(DB::raw('DATE(createdDate)'), '>=', $startDate);

		// if ($endDate)
		//     $query->where(DB::raw('DATE(createdDate)'), '<=', $endDate);

		// if ($branchId)
		//     $query->where('branchId', '=', $branchId);

		if ($supplierId)
		    $query->where('supplierId', '=', $supplierId);

		if ($productGroupId)
		    $query->where('groupId', '=', $productGroupId);

		if ($productCategoryId)
		    $query->where('categoryId', '=', $productCategoryId);

		if ($productSubCategoryId)
		    $query->where('subCategoryId', '=', $productSubCategoryId);

		if ($productBrandId)
		    $query->where('brandId', '=', $productBrandId);

		if ($productName)
		    $query->where('name', '=', $productName);

		$InvProducts = $query->get();

		return view('inventory/reports/viewInvStockReportBranch',['InvProducts' => $InvProducts, 'searchTypeCtr' => $searchTypeCtr, 'fiscalYearCtr' => $fiscalYearCtr]);
	}

// Calculation current stock of head office and branch office========================================
	public function headofficestockevery(Request $req){
		if($req->branchId==1){
			$branchId = $req->branchId;
			$productId = $req->productId; 

			$openingStock  = DB::table('inv_product')->where('id', $productId)->sum('openingStock');

			$purchaseQty   = DB::table('inv_purchase')
							->join('inv_purchase_details', 'inv_purchase.id', '=', 'inv_purchase_details.purchaseId')
							->select('inv_purchase.billNo','inv_purchase_details.productId', 'inv_purchase_details.quantity')
							->where('inv_purchase.branchId', $branchId)
							->where('inv_purchase_details.productId', $productId)
							->sum('inv_purchase_details.quantity');
							//->get();
			$issueReturnQty  = DB::table('inv_tra_issue_return')
							->join('inv_tra_issue_return_details', 'inv_tra_issue_return.id', '=', 'inv_tra_issue_return_details.issueReturnId')
							//->where('inv_tra_issue_return.branchId', $branchId)
							->where('inv_tra_issue_return_details.productId', $productId)
							->sum('inv_tra_issue_return_details.Quantity');
							//->get();

			$useReturnQty  = DB::table('inv_tra_use_return')
							->join('inv_tra_use_return_details', 'inv_tra_use_return.id', '=', 'inv_tra_use_return_details.useReturnId')
							->where('inv_tra_use_return.branchId', $branchId)
							->where('inv_tra_use_return_details.productId', $productId)
							->sum('inv_tra_use_return_details.productQuantity');
							//->get();

			$purchaseReturnQty = DB::table('inv_purchase_return')
							->join('inv_purchase_return_details', 'inv_purchase_return.id', '=', 'inv_purchase_return_details.purchaseReturnId')
							->where('inv_purchase_return.branchId', $branchId)
							->where('inv_purchase_return_details.productId', $productId)
							->sum('inv_purchase_return_details.quantity');
							//->get();

				$issueQty = DB::table('inv_tra_issue')
							->join('inv_tra_issue_details', 'inv_tra_issue.id', '=', 'inv_tra_issue_details.issueId')
							->select('inv_tra_issue_details.issueBillNoId','inv_tra_issue_details.issueQuantity')
							->where('inv_tra_issue_details.issueProductId', $productId)
							->sum('inv_tra_issue_details.issueQuantity');
							//->get();

				$useQty	= DB::table('inv_tra_use')
							->join('inv_tra_use_details', 'inv_tra_use.id', '=', 'inv_tra_use_details.useId')
							->select('inv_tra_use_details.productQuantity')
							->where('inv_tra_use.branchId', $branchId)
							->where('inv_tra_use_details.productId', $productId)
							->sum('inv_tra_use_details.productQuantity');
							//->get();

			$stock = (int)($openingStock+$purchaseQty+$issueReturnQty+$useReturnQty)-(int)($purchaseReturnQty+$issueQty+$useQty);
			//return response()->json($stock);
		}else{

			$branchId  = $req->branchId;
			$productId = $req->productId; 

			$openingStock = DB::table('inv_stock')->where('branchId', $branchId)
                                                  ->where('productId', $productId)->sum('quantity');
			$purchaseQty  = DB::table('inv_purchase')
							->join('inv_purchase_details', 'inv_purchase.id', '=', 'inv_purchase_details.purchaseId')
							->select('inv_purchase.billNo','inv_purchase_details.productId', 'inv_purchase_details.quantity')
							->where('inv_purchase.branchId', $branchId)
							->where('inv_purchase_details.productId', $productId)
							->sum('inv_purchase_details.quantity');
							//->get();

			$issueQty = DB::table('inv_tra_issue')
							->join('inv_tra_issue_details', 'inv_tra_issue.id', '=', 'inv_tra_issue_details.issueId')
							->select('inv_tra_issue_details.issueQuantity')
							->where('inv_tra_issue.branchId', $branchId)
							->where('inv_tra_issue_details.issueProductId', $productId)
							->sum('inv_tra_issue_details.issueQuantity');
							//->get();

	$thisBranchGetTrnsfrQty = DB::table('inv_tra_transfer')
							->join('inv_tra_transfer_details', 'inv_tra_transfer.id', '=', 'inv_tra_transfer_details.transferId')
							->select('inv_tra_transfer_details.transferQuantity')
							->where('inv_tra_transfer.branchIdTo', $branchId)
							->where('inv_tra_transfer_details.transferProductId', $productId)
							->sum('inv_tra_transfer_details.transferQuantity');
							//->get();

			$useReturnQty  = DB::table('inv_tra_use_return')
							->join('inv_tra_use_return_details', 'inv_tra_use_return.id', '=', 'inv_tra_use_return_details.useReturnId')
							->where('inv_tra_use_return.branchId', $branchId)
							->where('inv_tra_use_return_details.productId', $productId)
							->sum('inv_tra_use_return_details.productQuantity');
							//->get();

			$purchaseReturnQty = DB::table('inv_purchase_return')
							->join('inv_purchase_return_details', 'inv_purchase_return.id', '=', 'inv_purchase_return_details.purchaseReturnId')
							->where('inv_purchase_return.branchId', $branchId)
							->where('inv_purchase_return_details.productId', $productId)
							->sum('inv_purchase_return_details.quantity');
							//->get();

			$issueReturnQty  = DB::table('inv_tra_issue_return')
							->join('inv_tra_issue_return_details', 'inv_tra_issue_return.id', '=', 'inv_tra_issue_return_details.issueReturnId')
							->where('inv_tra_issue_return.branchId', $branchId)
							->where('inv_tra_issue_return_details.productId', $productId)
							->sum('inv_tra_issue_return_details.quantity');
							//->get();

	$thisBranchSendTrnsfrQty = DB::table('inv_tra_transfer')
							->join('inv_tra_transfer_details', 'inv_tra_transfer.id', '=', 'inv_tra_transfer_details.transferId')
							->select('inv_tra_transfer_details.transferQuantity')
							->where('inv_tra_transfer.brancIdFrom', $branchId)
							->where('inv_tra_transfer_details.transferProductId', $productId)
							->sum('inv_tra_transfer_details.transferQuantity');
							//->get();

			$useQty	= DB::table('inv_tra_use')
							->join('inv_tra_use_details', 'inv_tra_use.id', '=', 'inv_tra_use_details.useId')
							->select('inv_tra_use_details.productQuantity')
							->where('inv_tra_use.branchId', $branchId)
							->where('inv_tra_use_details.productId', $productId)
							->sum('inv_tra_use_details.productQuantity');
							//->get();

			 $stock = (int)($openingStock+$purchaseQty+$issueQty+$thisBranchGetTrnsfrQty+$useReturnQty)-(int)($purchaseReturnQty+$issueReturnQty+$thisBranchSendTrnsfrQty+$useQty);

			// return response()->json($stock);
		}
		return response()->json($stock);
	} 

// Average cost price calculation================================================================
	public function averagecostpricecalculation(Request $req){
		if($req->branchId==1){
			$branchId  =  $req->branchId;
			$productId = $req->productId; 

			$openingCostPrice  = (float)DB::table('inv_product')->where('id', $productId)->value('costPrice');
			$openingStock  = (int)DB::table('inv_product')->where('id', $productId)->value('openingStock');

			$openingAmount = $openingCostPrice * $openingStock;

			$purchaseAmount = DB::table('inv_purchase')
							->join('inv_purchase_details', 'inv_purchase.id', '=', 'inv_purchase_details.purchaseId')
							->where('inv_purchase.branchId', $branchId)
							->where('inv_purchase_details.productId', $productId)
							->sum('inv_purchase_details.totalPrice');
							//->get();
			
		$issueReturnAmount  = DB::table('inv_tra_issue_return')
							->join('inv_tra_issue_return_details', 'inv_tra_issue_return.id', '=', 'inv_tra_issue_return_details.issueReturnId')
							//->where('inv_tra_issue_return.branchId', $branchId)
							->where('inv_tra_issue_return_details.productId', $productId)
							->sum('inv_tra_issue_return_details.totalAmount');
							//->get();

		  $useReturnAmount  = DB::table('inv_tra_use_return')
							->join('inv_tra_use_return_details', 'inv_tra_use_return.id', '=', 'inv_tra_use_return_details.useReturnId')
							->where('inv_tra_use_return.branchId', $branchId)
							->where('inv_tra_use_return_details.productId', $productId)
							->sum('inv_tra_use_return_details.totalPrice');
							//->get();

	$purchaseReturnAmount = DB::table('inv_purchase_return')
							->join('inv_purchase_return_details', 'inv_purchase_return.id', '=', 'inv_purchase_return_details.purchaseReturnId')
							->where('inv_purchase_return.branchId', $branchId)
							->where('inv_purchase_return_details.productId', $productId)
							->sum('inv_purchase_return_details.totalPrice');
							//->get();

				$issueAmount = DB::table('inv_tra_issue')
							->join('inv_tra_issue_details', 'inv_tra_issue.id', '=', 'inv_tra_issue_details.issueId')
							->select('inv_tra_issue_details.issueBillNoId','inv_tra_issue_details.issueQuantity')
							->where('inv_tra_issue_details.issueProductId', $productId)
							->sum('inv_tra_issue_details.totalPrice');
							//->get();

				$useAmount	= DB::table('inv_tra_use')
							->join('inv_tra_use_details', 'inv_tra_use.id', '=', 'inv_tra_use_details.useId')
							->select('inv_tra_use_details.productQuantity')
							->where('inv_tra_use.branchId', $branchId)
							->where('inv_tra_use_details.productId', $productId)
							->sum('inv_tra_use_details.totalCostPrice');
							//->get();

			// $one = (int)($openingAmount+$purchaseAmount+$issueReturnAmount+$useReturnAmount);
			// $two = (int)($purchaseReturnAmount+$issueAmount+$useAmount);
			$averagePrice = (int)($openingAmount+$purchaseAmount+$issueReturnAmount+$useReturnAmount)-(int)($purchaseReturnAmount+$issueAmount+$useAmount);
			return response()->json($averagePrice);
			
		}else{

			$branchId  = $req->branchId;
			$productId = $req->productId; 

			$openingAmount = DB::table('inv_stock')->where('branchId', $branchId)
                                                   ->where('productId', $productId)->sum('amount');
			$purchaseAmount = DB::table('inv_purchase')
							->join('inv_purchase_details', 'inv_purchase.id', '=', 'inv_purchase_details.purchaseId')
							->where('inv_purchase.branchId', $branchId)
							->where('inv_purchase_details.productId', $productId)
							->sum('inv_purchase_details.totalPrice');
							//->get();

			$issueAmount = DB::table('inv_tra_issue')
							->join('inv_tra_issue_details', 'inv_tra_issue.id', '=', 'inv_tra_issue_details.issueId')
							->where('inv_tra_issue.branchId', $branchId)
							->where('inv_tra_issue_details.issueProductId', $productId)
							->sum('inv_tra_issue_details.totalPrice');
							//->get();

	$thisBranchGetTrnsfrAmo = DB::table('inv_tra_transfer')
							->join('inv_tra_transfer_details', 'inv_tra_transfer.id', '=', 'inv_tra_transfer_details.transferId')
							->select('inv_tra_transfer_details.transferQuantity')
							->where('inv_tra_transfer.branchIdTo', $branchId)
							->where('inv_tra_transfer_details.transferProductId', $productId)
							->sum('inv_tra_transfer_details.totalPrice');
							//->get();

		$useReturnAmount  = DB::table('inv_tra_use_return')
							->join('inv_tra_use_return_details', 'inv_tra_use_return.id', '=', 'inv_tra_use_return_details.useReturnId')
							->where('inv_tra_use_return.branchId', $branchId)
							->where('inv_tra_use_return_details.productId', $productId)
							->sum('inv_tra_use_return_details.totalPrice');
							//->get();

	$purchaseReturnAmount = DB::table('inv_purchase_return')
							->join('inv_purchase_return_details', 'inv_purchase_return.id', '=', 'inv_purchase_return_details.purchaseReturnId')
							->where('inv_purchase_return.branchId', $branchId)
							->where('inv_purchase_return_details.productId', $productId)
							->sum('inv_purchase_return_details.totalPrice');
							//->get();

		$issueReturnAmount  = DB::table('inv_tra_issue_return')
							->join('inv_tra_issue_return_details', 'inv_tra_issue_return.id', '=', 'inv_tra_issue_return_details.issueReturnId')
							//->where('inv_tra_issue_return.branchId', $branchId)
							->where('inv_tra_issue_return_details.productId', $productId)
							->sum('inv_tra_issue_return_details.totalAmount');
							//->get();

	$thisBranchSendTrnsfrAmo = DB::table('inv_tra_transfer')
							->join('inv_tra_transfer_details', 'inv_tra_transfer.id', '=', 'inv_tra_transfer_details.transferId')
							->select('inv_tra_transfer_details.transferQuantity')
							->where('inv_tra_transfer.brancIdFrom', $branchId)
							->where('inv_tra_transfer_details.transferProductId', $productId)
							->sum('inv_tra_transfer_details.totalPrice');
							//->get();

			$useAmount	= DB::table('inv_tra_use')
							->join('inv_tra_use_details', 'inv_tra_use.id', '=', 'inv_tra_use_details.useId')
							->select('inv_tra_use_details.productQuantity')
							->where('inv_tra_use.branchId', $branchId)
							->where('inv_tra_use_details.productId', $productId)
							->sum('inv_tra_use_details.totalCostPrice');
							//->get();

			 $averagePrice = (int)($openingAmount+$purchaseAmount+$issueAmount+$thisBranchGetTrnsfrAmo+$useReturnAmount)-
			 (int)($purchaseReturnAmount+$issueReturnAmount+$thisBranchSendTrnsfrAmo+$useAmount);



			return response()->json($averagePrice);
		}
	}

	/*============================current fiscal year dates==============================*/

	public function currentYearFscYrFdLd(Request $req){
		
        	//$lastFiscalYear = DB::table('gnr_fiscal_year')->orderBy('name', 'desc')->pluck('id')->first();
        	$minDate = DB::table('gnr_fiscal_year')->orderBy('name', 'desc')->value('fyStartDate');
        	$maxDate = DB::table('gnr_fiscal_year')->orderBy('name', 'desc')->value('fyEndDate');
    	
    	$data = array(
    		'maxDate'		=> $maxDate,
    		'minDate'		=> $minDate
    		);
		return response()->json($data);
	}

}
