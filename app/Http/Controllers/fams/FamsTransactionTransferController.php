<?php

namespace App\Http\Controllers\fams;

use App\Http\Controllers\Controller;
use App\Http\Controllers\gnr\Service;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\fams\FamsTraTransfer;
use App\fams\FamsProduct;


class FamsTransactionTransferController extends Controller
{
    public function index(){
        $user = Auth::user();
        if($user->branchId==1){
            $transfers = DB::table('fams_tra_transfer')->get();
        }
        else{
            $transfers = DB::table('fams_tra_transfer')->where('branchIdFrom',$user->branchId)->orWhere('branchIdTo',$user->branchId)->get();
        }        

        $products = DB::table('fams_product')->get();        

        $productGroups = DB::table('fams_product_group')->get();
        $productCategories = DB::table('fams_product_category')->get();
        $productSubCategories = DB::table('fams_product_sub_category')->get();
        $productBrands = DB::table('fams_product_brand')->get();
        $branches = array(''=>'Select Branch') + DB::table('gnr_branch')->pluck('name','id')->all();
        $projects = array(''=>'Select Project') + DB::table('gnr_project')->pluck('name','id')->all();
        $projectTypes = array(''=>'Select Project Type') + DB::table('gnr_project_type')->pluck('name','id')->all();

        $prefix = DB::table('fams_product_prefix')->where('status',1)->value('name');

        return view('fams/transaction/transfer/famsTransfer',['transfers'=>$transfers,'productGroups'=>$productGroups,'productCategories'=>$productCategories,'productSubCategories'=>$productSubCategories,'productBrands'=>$productBrands,'products'=>$products,'branches'=>$branches,'user'=>$user,'projects'=>$projects,'projectTypes'=>$projectTypes,'prefix'=>$prefix]);

    }
    
    public function addTransfer()
    { 
        $userBranchId = (int) Auth::user()->branchId;
        if ($userBranchId==1) {
            $branches = DB::table('gnr_branch')->get();
        }
        else{
            $branches = DB::table('gnr_branch')->where('id',$userBranchId)->get();
        }
        

        $writeOffProducts = DB::table('fams_write_off')->pluck('productId')->toArray();
        $soldProducts = DB::table('fams_sale')->pluck('productId')->toArray();
        $result = array_merge($writeOffProducts, $soldProducts);
        $products = DB::table('fams_product')->whereNotIn('id', $result)->get();

        $productGroups = DB::table('fams_product_group')->get();
        $productCategories = DB::table('fams_product_category')->get();
        $productSubCategories = DB::table('fams_product_sub_category')->orderBy('name')->get();
        $productTypes = DB::table('fams_product_type')->orderBy('productTypeCode')->get();
        $productNames = DB::table('fams_product_name')->orderBy('productNameCode')->get();
        
        $prefix = DB::table('fams_product_prefix')->where('status',1)->value('name');
        
        return view('fams/transaction/transfer/famsAddTransfer',['branches'=> $branches,'products'=>$products,'productGroups'=>$productGroups,'productCategories'=>$productCategories,'productSubCategories'=>$productSubCategories,'productNames'=>$productNames,'userBranchId'=>$userBranchId,'productTypes'=>$productTypes,'prefix'=>$prefix]);
    }

    public function storeTransfer(Request $request){   

        $productId = (int) DB::table('fams_product')->where('id',$request->productSelectedId)->value('id');
        $product = FamsProduct::find($productId);


        if ($product->branchId!=$request->branchFromId) {
            return redirect("famsViewTransfer")->with('alreadyTransfered','Sorry, This Product is already Transfered.');
        }

        $branchCode = str_pad(DB::table('gnr_branch')->where('id',$request->branchFromId)->value('branchCode'), 3, "0", STR_PAD_LEFT);
        $lastTransfer = str_pad(DB::table('fams_tra_transfer')->where('branchIdFrom',$request->branchFromId)->max('branchTransferNo')+1, 5, "0", STR_PAD_LEFT);
        $transferId = "TR".$branchCode.$lastTransfer;

        $transfer = new famsTraTransfer;

        $oldPieces = explode("-", $request->productSelected);
        $newPieces = explode("-", $request->newProductCode);

        $prefix = DB::table('fams_product_prefix')->where('status',1)->value('name');

        if ($prefix==null) {
            $oldProductCode = $oldPieces[0]."-".$oldPieces[1]."-".$oldPieces[2]."-".$oldPieces[3]."-".$oldPieces[4]."-".$oldPieces[5]."-".$oldPieces[6];
            $newProductCode = $newPieces[0]."-".$newPieces[1]."-".$newPieces[2]."-".$newPieces[3]."-".$newPieces[4]."-".$newPieces[5]."-".$newPieces[6];
        }
        else{
            $oldProductCode = $oldPieces[1]."-".$oldPieces[2]."-".$oldPieces[3]."-".$oldPieces[4]."-".$oldPieces[5]."-".$oldPieces[6]."-".$oldPieces[7];
            $newProductCode = $newPieces[1]."-".$newPieces[2]."-".$newPieces[3]."-".$newPieces[4]."-".$newPieces[5]."-".$newPieces[6]."-".$newPieces[7];
        }

        

        $transfer->transferId = $transferId;
        $transfer->productId = $productId;
        $transfer->oldProductCode = $oldProductCode;
        $transfer->newProductCode = $newProductCode;
        $transfer->branchTransferNo = DB::table('fams_tra_transfer')->where('branchIdFrom',$request->branchFromId)->max('branchTransferNo')+1;
        $transfer->projectIdFrom = $request->projectFromId;
        if ($request->projectTo=="") {
            $transfer->projectIdTo = $request->projectFromId;
        }
        else{
            $transfer->projectIdTo = $request->projectTo;
        }
        
        $transfer->projectTypeIdFrom = $request->projectTypeFromId;
        if ($request->projectTypeTo=="") {
            $transfer->projectTypeIdTo = $request->projectTypeFromId;
        }
        else{
            $transfer->projectTypeIdTo = $request->projectTypeTo;
        }
        
        $transfer->branchIdFrom = $request->branchFromId;

        if ($request->branchTo=="") {
            $transfer->branchIdTo = $request->branchFromId;
        }
        else{
            $transfer->branchIdTo = $request->branchTo;
        }
        
        $transfer->transferDate = Carbon::createFromFormat('d-m-Y', $request->transferDate)->hour(0)->minute(0)->second(0);Carbon::today();
        $transfer->totalTransferAmount = $request->remainingDep;
        $transfer->productTotalCost = $request->costPrice;
        $transfer->pastDep = $request->depGenerated;
        $transfer->depRemainning = $request->remainingDep;

        $transfer->oldProjectAssetNo = $product->projectAssetNo;
        $transfer->oldProjectTypeAssetNo = $product->projectTypeAssetNo;
        $transfer->oldBranchAssetNo = $product->branchAssetNo;
        

        $product->productCode = $request->newProductCode;
        if ($product->branchId != $request->branchTo) {
            $product->branchId = $request->branchTo;
            $product->branchAssetNo = DB::table('fams_product')->where('branchId', $request->branchTo)->max('branchAssetNo') + 1;
        }
        if ($product->projectId != $request->projectTo) {
            $product->projectId = $request->projectTo;
            $product->projectAssetNo = DB::table('fams_product')->where('projectId', $request->projectTo)->max('projectAssetNo') + 1;
        }
        if ($product->projectTypeId != $request->projectTypeTo) {
            $product->projectTypeId = $request->projectTypeTo;
            $product->projectTypeAssetNo = DB::table('fams_product')->where('productTypeId', $request->productTypeId)->max('productTypeAssetNo') + 1;
        }
        
        $product->save();
        
        $transfer->save();

        $logArray = array(
            'moduleId'  => 2,
            'controllerName'  => 'FamsTransactionTransferController',
            'tableName'  => 'fams_tra_transfer',
            'operation'  => 'insert',
            'primaryIds'  => [DB::table('fams_tra_transfer')->max('id')]
        );
        Service::createLog($logArray);
        return redirect("famsViewTransfer");

    }


    public function editTransfer(Request $request){
        $previousdata = FamsTraTransfer::find($request->editModalTransferRowId);
        $transfer = FamsTraTransfer::find($request->editModalTransferRowId);
        $transfer->newProductCode = $request->newProductCode;
        $transfer->projectIdTo = $request->projectIdTo;
        $transfer->projectTypeIdTo = $request->projectTypeIdTo;
        $transfer->branchIdTo = $request->branchTo;        
        $transfer->save();
        $logArray = array(
            'moduleId'  => 2,
            'controllerName'  => 'FamsTransactionTransferController',
            'tableName'  => 'fams_tra_transfer',
            'operation'  => 'update',
            'previousData'  => $previousdata,
            'primaryIds'  => [$previousdata->id]
        );
        Service::createLog($logArray);
        return redirect('famsViewTransfer')->with('editTransfer','Record is Updated Successfully!');
    }

    public function deleteTransfer(Request $request){
        $previousdata=FamsTraTransfer::find($request->deleteModalTransferRowId);

        $transfer = FamsTraTransfer::find($request->deleteModalTransferRowId);

        $product = FamsProduct::find($transfer->productId);

        $product->productCode = $transfer->oldProductCode;

        $product->projectId = $transfer->projectIdFrom;
        $product->projectAssetNo = $transfer->oldProjectAssetNo;

        $product->projectTypeId = $transfer->projectTypeIdFrom;
        $product->projectTypeAssetNo = $transfer->oldProjectTypeAssetNo;

        $product->branchId = $transfer->branchIdFrom;
        $product->branchAssetNo = $transfer->oldBranchAssetNo;
        
        $product->save();

        FamsTraTransfer::find($request->deleteModalTransferRowId)->delete();
        $logArray = array(
          'moduleId'  => 2,
          'controllerName'  => 'FamsTransactionTransferController',
          'tableName'  => 'fams_tra_transfer',
          'operation'  => 'delete',
          'previousData'  => $previousdata,
          'primaryIds'  => [$previousdata->id]
      );
        Service::createLog($logArray);


        return redirect('famsViewTransfer')->with('deleteTransfer','Record is Deleted Successfully!');
    }

    public function getProductInfo(Request $request)
    {
        $productId = (int) json_decode($request->productId);
        $product = FamsProduct::find($productId);

        $projectFrom = DB::table('gnr_project')->where('id',$product->projectId)->value('name');
        $projectTypeFrom = DB::table('gnr_project_type')->where('id',$product->projectTypeId)->value('name');
        $branchFrom = DB::table('gnr_branch')->where('id',$product->branchId)->value('name');
        $costPrice = (float) $product->totalCost;
        $additionalCharge = (float) DB::table('fams_additional_charge')->where('productId',$productId)->sum('amount');
        $totalCost = $costPrice + $additionalCharge;
        $depGenerated = round((float) DB::table('fams_depreciation_details')->where('productId',$productId)->sum('amount') + (float) DB::table('fams_product')->where('id',$productId)->value('depreciationOpeningBalance'),2);
        $remainingDep = round($totalCost - $depGenerated,2);

        $branchCode = str_pad(DB::table('gnr_branch')->where('id',$product->branchId)->value('branchCode'), 3, "0", STR_PAD_LEFT);
        $lastTransfer = str_pad(DB::table('fams_tra_transfer')->where('branchIdFrom',$product->branchId)->max('branchTransferNo')+1, 5, "0", STR_PAD_LEFT);
        $transferId = "TR".$branchCode.$lastTransfer;
        $purchaseDate = DB::table('fams_product')->where('id',$productId)->value('purchaseDate');


        $data = array(
         'projectFrom' => $projectFrom,
         'projectFromId' => $product->projectId,
         'projectTypeFrom' => $projectTypeFrom,
         'projectTypeFromId' => $product->projectTypeId,
         'branchFrom' => $branchFrom,
         'branchFromId' => $product->branchId,
         'costPrice' => $totalCost,
         'depGenerated' => $depGenerated,
         'remainingDep' => $remainingDep,
         'transferId' => $transferId,
         'purchaseDate' => $purchaseDate
     );



        return response()->json($data);

    }
    
}


