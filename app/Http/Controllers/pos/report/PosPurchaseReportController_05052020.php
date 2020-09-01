<?php
namespace App\Http\Controllers\pos\report;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\pos\PosSales;
use App\pos\PosCollection;
use App\pos\PosSalesDetails;
use App\pos\PosSupplier;
use Validator;
use Response;
use DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Service\DatabasePartitionHelper;

class PosPurchaseReportController extends Controller
{
    public $partitionName;

    public function index(Request $req){
                
        $user = Auth::user();
        Session::put('branchId', $user->branchId);
        
        $gnrBranchId = Session::get('branchId');
        $logedUserName = $user->name;
        $purchaseSuppliers = PosSupplier::all();
                
        $productNames = DB::table('pos_product')->select('id','name','code')->get();
        $purchaseNo = DB::table('pos_purchase')->select('id','billNo')->get();
        
        return view('pos/report/posPurchaseReport/posPurchaseReportForm',['purchaseSuppliers'=>$purchaseSuppliers,'productNames'=>$productNames,'purchaseNo'=>$purchaseNo]);
    }


    public function getBillNoOnChangeSupplierPurchase(Request $request){
        
        $purchases = DB::table('pos_purchase')
        ->where('supplierId',$request->supplierId)
        ->select('billNo','id')
        ->get();

        return response()->json($purchases);
   }


   public function getProductOnChangeSupplierPurchase(Request $request){
//dd($request->id);
    $purchases = DB::table('pos_purchase')
    ->join('pos_product', 'pos_product.id', '=', 'pos_purchase.productId')
    ->where('pos_purchase.id',$request->id)
    ->select('pos_purchase.*','pos_product.id','pos_product.name','pos_product.code')->get();
//dd($purchases);

    return response()->json($purchases);
}
public function getPurchaseReport1(Request $req){

    $user = Auth::user();
    Session::put('branchId', $user->branchId);
    $gnrBranchId = Session::get('branchId');
    $logedUserName = $user->name;
    $startDate=$req->startDate;
    $endDate=$req->endDate;
    $purchaseId=$req->billNo;
    $productId=$req->productId;
    $supplierId=$req->supplierId;
    $posPurchaseReports = DB::table('pos_purchase')
    ->select('pos_purchase.*','pos_purchase_details.*','pos_product.*')
    ->Join('pos_purchase_details','pos_purchase_details.purchaseId', '=',
        'pos_purchase.id')
    ->Join('pos_product','pos_product.id', '=','pos_purchase_details.productId')
    ->where('pos_purchase.purchaseDate', '>=', $startDate)
    ->where('pos_purchase.purchaseDate', '<=', $endDate)
    ->where('pos_purchase.id', '=', $purchaseId)
    ->where('pos_purchase.productId', '=',$productId)
    ->where('pos_purchase.supplierId', '=',$supplierId)
    ->orderBy('pos_purchase.purchaseDate','DESC')
    ->get();



    return view('pos/report/posPurchaseReport/posPurchaseReportTable',['posPurchaseReports'=>$posPurchaseReports,'startDate'=>$startDate,'endDate'=>$endDate]);
}


public function getPurchaseReport(Request $req){
//dd($req->all());
    $user = Auth::user();
    Session::put('branchId', $user->branchId);
    $gnrBranchId = Session::get('branchId');

    $posAllPurchase = DB::table('pos_purchase');

    if($req->billNo > 0)
        $posPurchaseReports = $posAllPurchase->where('pos_purchase.id',$req->billNo);
    if($req->productId > 0)
        $posPurchaseReports = $posAllPurchase->where('pos_purchase.productId',$req->productId);
    if($req->supplierId > 0)
        $posPurchaseReports = $posAllPurchase->where('pos_purchase.supplierId',$req->supplierId);
    if($req->startDate != null)
        $posPurchaseReports = $posAllPurchase->where('purchaseDate','>=', $req->startDate);
    if($req->endDate != null)
        $posPurchaseReports = $posAllPurchase->where('purchaseDate','<=', $req->endDate);

    $posPurchaseReports = $posAllPurchase
    ->Join('pos_purchase_details','pos_purchase_details.purchaseId', '=',
        'pos_purchase.id')
    ->Join('pos_product','pos_product.id', '=','pos_purchase_details.productId')

    ->select('pos_purchase.*','pos_purchase_details.*','pos_product.*')
    ->orderBy('pos_purchase.purchaseDate','DESC')
    ->get();
    $startDate=$req->startDate;
    $endDate=$req->endDate;

    return view('pos/report/posPurchaseReport/posPurchaseReportTable',['posPurchaseReports'=>$posPurchaseReports,'startDate'=>$startDate,'endDate'=>$endDate]);
}




public function getPurchaseReport_backup(Request $req){
//dd($req->all());
    $user = Auth::user();
    Session::put('branchId', $user->branchId);
    $gnrBranchId = Session::get('branchId');

    $posAllPurchase = DB::table('pos_purchase');

    if($req->billNo == 'all' && $req->productId == 'all' && $req->supplierId == 'all' && $req->startDate == null && $req->end == null){
        $posPurchaseReports = $posAllPurchase
        ->Join('pos_purchase_details','pos_purchase_details.purchaseId', '=',
            'pos_purchase.id')
        ->Join('pos_product','pos_product.id', '=','pos_purchase_details.productId')

        ->select('pos_purchase.*','pos_purchase_details.*','pos_product.*')
        ->orderBy('pos_purchase.purchaseDate','DESC')
        ->get();
    }
    elseif($req->billNo >0 && $req->productId >0  && $req->supplierId >0 && $req->startDate != null && $req->endDate != null){
        $posPurchaseReports = $posAllPurchase
        ->Join('pos_purchase_details','pos_purchase_details.purchaseId', '=',
            'pos_purchase.id')
        ->Join('pos_product','pos_product.id', '=','pos_purchase_details.productId')
        ->where('purchaseId',$req->billNo)
        ->where('pos_purchase.productId',$req->productId)
        ->where('pos_purchase.supplierId',$req->supplierId)
        ->where('purchaseDate','>=',$req->startDate)
        ->where('purchaseDate','<=',$req->endDate)

        ->select('pos_purchase.*','pos_purchase_details.*','pos_product.*')
        ->orderBy('pos_purchase.purchaseDate','DESC')
        ->get();
    }
    elseif($req->billNo >0 && $req->productId >0  && $req->supplierId > 0 && $req->startDate != null && $req->endDate != null){

        $posPurchaseReports = $posAllPurchase
        ->Join('pos_purchase_details','pos_purchase_details.purchaseId', '=',
            'pos_purchase.id')
        ->Join('pos_product','pos_product.id', '=','pos_purchase_details.productId')
        ->where('purchaseDate','>=',$req->startDate)
        ->where('purchaseDate','<=',$req->endDate)

        ->select('pos_purchase.*','pos_purchase_details.*','pos_product.*')
        ->orderBy('pos_purchase.purchaseDate','DESC')
        ->get();
    }

    elseif($req->billNo == '' && $req->productId == ''  && $req->supplierId == '' && $req->startDate != null && $req->endDate != null){

        $posPurchaseReports = $posAllPurchase
        ->Join('pos_purchase_details','pos_purchase_details.purchaseId', '=',
            'pos_purchase.id')
        ->Join('pos_product','pos_product.id', '=','pos_purchase_details.productId')
        ->where('purchaseDate','>=',$req->startDate)
        ->where('purchaseDate','<=',$req->endDate)

        ->select('pos_purchase.*','pos_purchase_details.*','pos_product.*')
        ->orderBy('pos_purchase.purchaseDate','DESC')
        ->get();
    }

    elseif($req->billNo == 'all' && $req->productId == 'all'  && $req->supplierId == 'all' && $req->startDate != null && $req->endDate != null){

        $posPurchaseReports = $posAllPurchase
        ->Join('pos_purchase_details','pos_purchase_details.purchaseId', '=',
            'pos_purchase.id')
        ->Join('pos_product','pos_product.id', '=','pos_purchase_details.productId')
        ->where('purchaseDate','>=',$req->startDate)
        ->where('purchaseDate','<=',$req->endDate)

        ->select('pos_purchase.*','pos_purchase_details.*','pos_product.*')
        ->orderBy('pos_purchase.purchaseDate','DESC')
        ->get();
    }
    else{

        $posPurchaseReports = $posAllPurchase
        ->Join('pos_purchase_details','pos_purchase_details.purchaseId', '=',
            'pos_purchase.id')
        ->Join('pos_product','pos_product.id', '=','pos_purchase_details.productId')
        ->where('purchaseId',$req->billNo)
        ->where('pos_purchase.productId',$req->productId)
        ->where('pos_purchase.supplierId',$req->supplierId)
        ->where('purchaseDate',$req->startDate)
        ->where('purchaseDate',$req->endDate)

        ->select('pos_purchase.*','pos_purchase_details.*','pos_product.*')
        ->orderBy('pos_purchase.purchaseDate','DESC')
        ->get();
    }
    $startDate=$req->startDate;
    $endDate=$req->endDate;



    return view('pos/report/posPurchaseReport/posPurchaseReportTable',['posPurchaseReports'=>$posPurchaseReports,'startDate'=>$startDate,'endDate'=>$endDate]);
}


}