<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\pos\PosProduct;
use App\pos\PosCostSheet;
use Illuminate\Support\Facades\Auth;
use App\Service\TransactionCheckHelper;
use DB;
use App\pos\PosOtherCost;

class CostSheetController extends Controller
{
    
    public function costSheetList()
    {  
        $costSheets = PosCostSheet::where('companyId', Auth::user()->company_id_fk)->with('product')->get();
        return view('pos/costSheet/list', compact('costSheets'));
    }

    public function addCostSheet()
    {   
        $products = PosProduct::where('type', 'product')->where('companyId', Auth::user()->company_id_fk)->get();
        
        $rawMaterialData = PosProduct::where('type', 'raw')->where('companyId', Auth::user()->company_id_fk)->get();
        $rawMaterials = array();
        foreach($rawMaterialData as $record)
        {   
            $purchase = DB::table('pos_purchase_details')->where('productId', $record->id)->first();
            if($purchase) $rawMaterials[] = $record;
        }

        $units = config('constants.product_units');

        foreach($rawMaterials as $key => $record)
        {   
            $record->unitType = $units[$record->unit];
        }

        $otherCosts = PosOtherCost::where('companyId', Auth::user()->company_id_fk)->get();

        $checkValue = new TransactionCheckHelper();
        $checkTransaction = $checkValue->monthEndYearEndCheck();

        return view('pos/costSheet/add', compact('products', 'rawMaterials', 'checkTransaction', 'otherCosts'));
    }

    public function getProductPrice(Request $request)
    {    
        $effectDate = date('Y-m-d', strtotime($request->effectDate));

        $purchaseDetails = DB::table('pos_purchase_details')
                                    ->join('pos_purchase', 'pos_purchase.id', 'pos_purchase_details.purchaseId')
                                    ->select('pos_purchase_details.price', 'pos_purchase.purchaseDate')
                                    ->where('pos_purchase.purchaseDate', '<=', $effectDate)
                                    ->where('pos_purchase_details.productId', $request->rawMaterialId)
                                    ->orderBy('pos_purchase.purchaseDate', 'desc')->first();

        return response()->json($purchaseDetails);
    }

    public function insertCostSheet(Request $request)
    {      
        $rawProducts = json_decode($request->rawProducts);
        $otherCostArr = json_decode($request->otherCostArr);

        $productInfo = array();
        $otherCost = array();

        foreach($rawProducts as $key => $record)
        {
            $productInfo[$key]['productId'] = $record->productId;
            $productInfo[$key]['qty'] = $record->qty;
            $productInfo[$key]['costPrice'] = $record->costPrice;
        }
        
        foreach($otherCostArr as $key => $record)
        {
            $otherCost[$key]['otherCostId'] = $record->otherCostId;
            $otherCost[$key]['costAmount'] = $record->costAmount;
            $otherCost[$key]['isVoucher'] = $record->isVoucher;
        }

        $costSheet = new PosCostSheet;
        $costSheet->companyId = Auth::user()->company_id_fk;
        $costSheet->productId = (int)$request->product;
        $costSheet->productInfo = json_encode($productInfo);
        $costSheet->otherCost = json_encode($otherCost);
        $costSheet->totalAmount = $request->totalAmount;
        $costSheet->effectDate = date('Y-m-d', strtotime($request->effectDate));
        $costSheet->save();

        return response()->json('success');

    }

    function viewCostSheet($id)
    {
        $data = array();
        
        // Company & Branch Information 
        $companyDetails = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)
                                                  ->select('name','address')->first();

        $branch = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->first();
        $productDetails = PosCostSheet::where('companyId', Auth::user()->company_id_fk)->where('id', $id)->with('product')->first();

        $data['info'] = [
            'companyName'      => $companyDetails->name,
            'companyAddress'   => $companyDetails->address,
            'branch'           => $branch->name,
            'product'          => $productDetails->product->name,
            'date'             => $productDetails->effectDate
        ];

        $totalAmount = 0;

        // Raw Material Information
        $rawMetarail = json_decode($productDetails->productInfo);
        $data['rawMetarial'] = array();

        foreach($rawMetarail as $key => $record)
        {     
            $product = PosProduct::find($record->productId);
            $data['rawMetarial'][$key]['name'] = $product->name;
            $data['rawMetarial'][$key]['costPrice'] = $record->costPrice;
            $data['rawMetarial'][$key]['qty'] = $record->qty;
            $data['rawMetarial'][$key]['total'] = $record->costPrice * $record->qty;
            $totalAmount = $totalAmount + ($record->costPrice * $record->qty);
        }

        // Other Cost Information
        $otherCost = json_decode($productDetails->otherCost);
        $data['otherCost'] = array();

        foreach($otherCost as $key => $record)
        {   
            $otherCostTemp = PosOtherCost::find($record->otherCostId); 
            $data['otherCost'][$key]['costType'] = $otherCostTemp->name;
            $data['otherCost'][$key]['costAmount'] = $record->costAmount;
            $totalAmount = $totalAmount + $record->costAmount;
        }

        $data['totalAmount'] = $totalAmount;

        return view('pos.costSheet.view',['data'=>$data]);
    }

    function editCostSheet($id)
    {   
        $products = PosProduct::where('type', 'product')->where('companyId', Auth::user()->company_id_fk)->get();
        
        $rawMaterialData = PosProduct::where('type', 'raw')->where('companyId', Auth::user()->company_id_fk)->get();
        $rawMaterials = array();
        foreach($rawMaterialData as $record)
        {   
            $purchase = DB::table('pos_purchase_details')->where('productId', $record->id)->first();
            if($purchase) $rawMaterials[] = $record;
        }

        $units = config('constants.product_units');

        foreach($rawMaterials as $key => $record)
        {
            $record->unitType = $units[$record->unit];
        }

        $otherCosts = PosOtherCost::where('companyId', Auth::user()->company_id_fk)->get();

        $checkValue = new TransactionCheckHelper();
        $checkTransaction = $checkValue->monthEndYearEndCheck();

        
        $productDetails = PosCostSheet::where('companyId', Auth::user()->company_id_fk)->where('id', $id)->with('product')->first();

        // Raw Material Information
        $rawMetarail = json_decode($productDetails->productInfo);
        $existRawProduct = array();

        foreach($rawMetarail as $key => $record)
        {   
            $product = PosProduct::find($record->productId);

            $existRawProduct[$key]['productName'] = $product->name;
            $existRawProduct[$key]['unitType'] = $units[$product->unit];
            $existRawProduct[$key]['productId'] = $record->productId;
            $existRawProduct[$key]['qty'] = $record->qty;
            $existRawProduct[$key]['costPrice'] = $record->costPrice;
            $existRawProduct[$key]['totalAmount'] = $record->qty * $record->costPrice;
        }

        // Other Cost
        $otherCost = json_decode($productDetails->otherCost);
        $existOtherCost = array();

        foreach($otherCost as $key => $record)
        {   
            $otherCostTemp = PosOtherCost::find($record->otherCostId); 

            $existOtherCost[$key]['otherCostName'] = $otherCostTemp->name;
            $existOtherCost[$key]['otherCostId'] = $record->otherCostId;
            $existOtherCost[$key]['costAmount'] = $record->costAmount;
            $existOtherCost[$key]['isVoucher'] = $record->isVoucher;
        }


        return view('pos.costSheet.edit', compact('products', 'productDetails', 'rawMaterials', 'checkTransaction', 
                'existOtherCost', 'existRawProduct', 'otherCosts'));
    }

    function updateCostSheet(Request $request)
    {   
        $rawProducts = json_decode($request->rawProducts);
        $otherCostArr = json_decode($request->otherCostArr);

        $productInfo = array();
        $otherCost = array();

        foreach($rawProducts as $key => $record)
        {
            $productInfo[$key]['productId'] = $record->productId;
            $productInfo[$key]['qty'] = $record->qty;
            $productInfo[$key]['costPrice'] = $record->costPrice;
        }
        
        foreach($otherCostArr as $key => $record)
        {
            $otherCost[$key]['otherCostId'] = $record->otherCostId;
            $otherCost[$key]['costAmount'] = $record->costAmount;
            $otherCost[$key]['isVoucher'] = $record->isVoucher;
        }

        $costSheet = PosCostSheet::find($request->costSheetId);
        $costSheet->companyId = Auth::user()->company_id_fk;
        $costSheet->productId = (int)$request->product;
        $costSheet->productInfo = json_encode($productInfo);
        $costSheet->otherCost = json_encode($otherCost);
        $costSheet->totalAmount = $request->totalAmount;
        $costSheet->effectDate = date('Y-m-d', strtotime($request->effectDate));
        $costSheet->update();

        return response()->json('success');

    }

    function deleteCostSheet(Request $request)
    {   
        $costSheet = PosCostSheet::find($request->id);
        $costSheet->delete();
        $data = ['text' => 'Deleted successfully!'];

        return response()->json($data);
    }
}
