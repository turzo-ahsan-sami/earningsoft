<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\fams\FamsDepreciation as FamsDep;
use App\fams\FamsDepDetails;
use App\fams\FamsSale;
use App\fams\FamsProduct;
use App\fams\FamsAdditionalCharge;
use App\gnr\GnrBranch;
use App\Http\Controllers\gnr\Service;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

class FamsTransactionPurchaseController extends Controller
{
  public function index()
  {
    $sales = DB::table('fams_sale')->orderBy('id','desc')->get();
    $prefix = DB::table('fams_product_prefix')->where('status',1)->value('name');
    return view('fams/transaction/sale/viewSale',['sales'=>$sales,'prefix'=>$prefix]);
  }



  public function addSale()
  {
    $productTypes = DB::table('fams_product_type')->select('id','name','productTypeCode')->get();
    $productNames = DB::table('fams_product_name')->select('id','name','productNameCode')->get();
    $prefix = DB::table('fams_product_prefix')->where('status',1)->value('name');
    return view('fams/transaction/sale/addSale',['productTypes'=>$productTypes,'productNames'=>$productNames,'prefix'=>$prefix]);

  }      

  public function storeSale(Request $request)
  {
    $user = Auth::user();
    $saleByBranchId = $user->branchId;

    $user_id = Auth::id();

    $productId = (int) json_decode($request->productId);


    $isSold = DB::table('fams_sale')                              
    ->where('productId',$productId)                              
    ->get();

    if(sizeof($isSold)>0){        
      return response()->json("It is already Sold Out."); 
    }

    else{ 

     $branchOfThisProduct = (int) DB::table('fams_product')->where('id',$request->productId)->value('branchId');
     $branchCodeOfThisProduct = DB::table('gnr_branch')->where('id',$branchOfThisProduct)->value('branchCode');
     $formatedBranchCode = str_pad( $branchCodeOfThisProduct, 3, "0", STR_PAD_LEFT );

     $lastSaleId = FamsSale::max('id')+1;
     $lastSaleForThisBranch = (int) FamsSale::where('branchId',$branchOfThisProduct)->max('branchSaleNo')+1;
         //$lastSaleForThisBranch = (int) DB::table('fams_sale')->where('branchId',$branchOfThisProduct)->max('branchSaleNo')+1;
     $formatedlastSaleforThisBranch = str_pad( $lastSaleForThisBranch, 5, "0", STR_PAD_LEFT );

     $writtenDownValue = (float) json_decode($request->writtenDownValue);
     $salePrice = (float) json_decode($request->salePrice);
     if ($salePrice > $writtenDownValue) {
       $profit = $salePrice - $writtenDownValue;
       $loss = 0;
     }
     elseif($salePrice < $writtenDownValue){
      $profit = 0;
      $loss = $writtenDownValue - $salePrice;
    }
    else{
      $profit = 0;
      $loss = 0;
    }

    $sale = new FamsSale;
    $sale->saleId = "SL".$formatedBranchCode.$formatedlastSaleforThisBranch;
    $sale->productId = (int) json_decode($request->productId);
    $sale->branchId = $branchOfThisProduct;
    $sale->branchSaleNo = $lastSaleForThisBranch;
    $sale->amount = $salePrice;
    $sale->profitAmount = $profit;
    $sale->lossAmount = $loss;
    $sale->productTotalCost = DB::table('fams_product')->where('id',$request->productId)->value('totalCost');
    $sale->productAdditionalCharge = DB::table('fams_additional_charge')->where('productId',$request->productId)->sum('amount');
    $sale->depGenerated = $request->accDep;
    $sale->productResaleValue = (int) DB::table('fams_product')->where('id',$request->productId)->value('resellValue');
    $sale->createdDate = Carbon::createFromFormat('d-m-Y', $request->saleDate)->hour(0)->minute(0)->second(0);
    $sale->saleByBranchId = $saleByBranchId;
    $sale->saleByUserId = $user_id;
    $sale->save();

  } 

  $logArray = array(
    'moduleId'  => 2,
    'controllerName'  => 'FamsTransactionSaleController',
    'tableName'  => 'fams_sale',
    'operation'  => 'insert',
    'primaryIds'  => [DB::table('fams_sale')->max('id')]
  );
  Service::createLog($logArray);      

  return response()->json("success");  

}

public function editSale(Request $request)
{
  $saleId = (int) json_decode($request->EMsaleRowId);
  $productCost = (float) json_decode($request->EMproductCost);
  $accDep = (float) json_decode($request->EMaccDep);
  $newSalePrice = (float) json_decode($request->EMsaleAmount);

  if (($productCost - $accDep - $newSalePrice) > 0) {
    $profit = 0;
    $loss = $productCost - $accDep - $newSalePrice;
  }
  elseif (($productCost - $accDep - $newSalePrice)<0) {
    $profit = $newSalePrice - $productCost + $accDep ;
    $loss = 0;
  }
  else{
    $profit = 0;
    $loss = 0;
  }
  $previousdata = FamsSale::find ($saleId);
  $sale = FamsSale::find($saleId);
  $sale->amount = (float) json_decode($request->EMsaleAmount);
  $sale->profitAmount = $profit;
  $sale->lossAmount = $loss;
  $sale->save();

  $logArray = array(
    'moduleId'  => 2,
    'controllerName'  => 'FamsTransactionSaleController',
    'tableName'  => 'fams_sale',
    'operation'  => 'update',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray); 
  return redirect('famsViewSale')->with('saleUpdate','Record is Updated Successfully.');
}


public function deleteSale(Request $request)
{
 $previousdata=FamsSale::find($request->saleId);
 FamsSale::where('id',$request->saleId)->delete();
 $logArray = array(
  'moduleId'  => 2,
  'controllerName'  => 'FamsTransactionSaleController',
  'tableName'  => 'fams_sale',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);
 return redirect('famsViewSale')->with('saleDelete','Record is Deleted Successfully.');
}

public function getProductInfo(Request $request)
{
  $productId = (int) json_decode($request->productId);

  if ($request->productId!="") {
    $product = FamsProduct::find($productId);
        $depOpeningBalance = DB::table('fams_product')->where('id',$productId)->value('depreciationOpeningBalance');//(double) $product->depreciationOpeningBalance;
        $totalCost = (float) DB::table('fams_product')->where('id',$productId)->value('totalCost');
        $reSalePrice = (float) DB::table('fams_product')->where('id',$productId)->value('resellValue');

        $additionalCharge = (float) FamsAdditionalCharge::where('productId',$productId)->sum('amount');
        $dep = (float) FamsDepDetails::where('productId',$product->id)->sum('amount');
        $writtenDownValue = $totalCost + $additionalCharge - $dep - $depOpeningBalance;
        

        $data = array(
          'purchaseDate' => $product->purchaseDate, 
          'costPrice' => number_format($product->totalCost,2,'.',','), 
          'additionalCharge' => number_format($additionalCharge,2,'.',','), 
          'totalCost' => round($product->totalCost + $additionalCharge,2), 
          'dep' =>  round($dep + $depOpeningBalance,2),
          'depOpeningBalance' => round($depOpeningBalance,2),
          'reSalePrice' => round($reSalePrice,2),
          'writtenDownValue' => round($writtenDownValue,2)
        );  

        return response()->json($data);    
      }
    }

    public function getSaleBillId(Request $request)
    {
      $productId = (int) json_decode($request->productId);

      if ($request->productId=="") {
        $saleId = "";
      }
      else{

       $branchOfThisProduct = (int) DB::table('fams_product')->where('id',$request->productId)->value('branchId');
       $branchCodeOfThisProduct = DB::table('gnr_branch')->where('id',$branchOfThisProduct)->value('branchCode');
       $formatedBranchCode = str_pad( $branchCodeOfThisProduct, 3, "0", STR_PAD_LEFT );

       $lastSaleId = FamsSale::max('id')+1;
       $lastSaleForThisBranch = (int) FamsSale::where('branchId',$branchOfThisProduct)->max('branchSaleNo')+1;
       $formatedlastSaleforThisBranch = str_pad( $lastSaleForThisBranch, 5, "0", STR_PAD_LEFT );

       $saleId = "SL".$formatedBranchCode.$formatedlastSaleforThisBranch;

     }

     $lastDepId = FamsDepDetails::where('productId',$productId)->max('id');
     $lastDepDate = FamsDepDetails::where('id',$lastDepId)->value('depTo');

     $data = array(
      'saleId' => $saleId,
      'lastDepDate' => $lastDepDate
    );

     return response()->json($data); 
   }

 }
