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

class InvStockAmountReportController extends Controller
{
    public function index(){
        //$InvProducts = InvProduct::groupBy('modelId')->get();
        $InvProducts = InvProduct::all();
       
    	return view('inventory/reports/stockAmount/viewInvStockAmountReport',['InvProducts' => $InvProducts]);
        
    }

    public function filterStockAmountReport(Request $req){

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

		return view('inventory/reports/stockAmount/viewInvStockAmountReport',['InvProducts' => $InvProducts, 'searchTypeCtr' => $searchTypeCtr, 'fiscalYearCtr' => $fiscalYearCtr]);
	}

		public function invBranchStockAmountReport(){
    	$InvProducts = InvProduct::all();
    	return view('inventory/reports/stockAmount/viewInvStockAmountReportBranch',['InvProducts' => $InvProducts]);
    }

    public function filterStockAmountReportBranch(Request $req){
    	
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

		return view('inventory/reports/stockAmount/viewInvStockAmountReportBranch',['InvProducts' => $InvProducts, 'searchTypeCtr' => $searchTypeCtr, 'fiscalYearCtr' => $fiscalYearCtr]);
	}


	/*============================ Current fiscal year start dates and end Date ==============================*/

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
